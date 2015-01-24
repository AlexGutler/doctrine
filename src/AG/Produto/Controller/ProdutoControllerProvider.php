<?php

namespace AG\Produto\Controller;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

class ProdutoControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        // listagem de produtos
        $controllers->get('/', function (Application $app) {
            //$produtos = $app['produtoService']->fetchAll();
            //return $app['twig']->render('produtos.twig', ['produtos' => $produtos, 'deleted' => false]);

            // direcinar para pagina 1
            return $app->redirect('pag/1');
        })->bind('produtos');

        // PAGINATION DOS PRODUTOS
        $controllers->get('/pag/{id}', function (Application $app, $id) {
            // definir o limite de registros por página e o registro inicial da busca (offset)
            if(!isset($id)){$id = 1;}

            $limit = 5; // limite de registros por página
            $offset = ($id - 1) * $limit; // buscar a partir do registro

            // buscar o número de registros para calcular a quantidade de páginas
            $pags = $app['produtoService']->fetchAll();
            $numPages = ceil(count($pags)/$limit); // arredondar para cima para ter o número de páginas

            // busca os produtos
            $produtos = $app['produtoService']->fetchPagination($offset, $limit);

            /* passar os produtos, o deleted, o número de páginas, a página ativa */
            return $app['twig']->render(
                'Produto/index.html.twig',
                ['produtos' => $produtos, 'deleted' => false, 'paginas' => $numPages, 'activepage' => $id]
            );
        })->bind('produtos-pagination');

        // executa e exibe os resultados encontrados da busca pelo nome
        $controllers->post("/find", function(Request $request) use($app){
            // array com as opções de busca
            $options = array(
                'coluna' => 'nome',
                'valor' => $request->get('nome')
            );

            // faz a busca com os parametros
            $produtos = $app['produtoService']->buscarProduto($options);

            /* passar os produtos, o deleted, o número de páginas */
            return $app['twig']->render(
                'Produto/index.html.twig',
                ['produtos' => $produtos, 'deleted' => false, 'paginas' => 0]
            );
        })->bind('produto-find');

        // formulario para cadastro de novo produto
        $controllers->get("/novo", function() use($app){
            $categorias = $app['categoriaService']->fetchAll();
            $tags = $app['tagService']->fetchAll();
            /*$categorias = array();

            foreach ($data as $categoria)
            {
                $categorias[] = $categoria->toArray();
            }*/

            return $app['twig']->render(
                'Produto/novo.html.twig',
                [
                    'id' => null,
                    'errors' => array('nome' => null, 'descricao' => null, 'valor' => null),
                    'produto' => array('nome' => null, 'descricao' => null, 'valor' => null),
                    'categorias' => $categorias,
                    'tags' => $tags
                ]);
        })->bind('produto-novo');

        // post dos dados do novo produto
        $controllers->post("/novo", function(Request $request) use($app) {
            $result = $app['produtoService']->insert($request);

            if (!is_array($result)) {
                return $app['twig']->render('Produto/sucesso.html.twig', ['mensagem' => 'Produto cadastrado com sucesso!']);
            } else {
                return $app['twig']->render('Produto/novo.html.twig',
                    [
                        'id' => null,
                        'errors' => $result,
                        'produto' => $request->request->all()
                    ]);
            }
        })->bind('produto-salvar');

        // deletar produto
        $controllers->get('/{id}/deletar', function($id) use($app){
            $result = $app['produtoService']->delete($id);
            if ($result)
            {
                //$produtos = $app['produtoService']->fetchAll();
                //return $app['twig']->render('produtos.twig', ['produtos' => $produtos, 'deleted' => true]);
                return $app['twig']->render('Produto/sucesso.html.twig', ['mensagem' => 'Produto removido com sucesso!']);
            } else {
                $app->abort(500, "Erro ao deletar o produto");
            }
        })->bind('produto-deletar');

        // editar produto
        $controllers->get("/{id}/editar", function($id) use($app){
            $produto = $app['produtoService']->fetch($id);

            return $app['twig']->render('Produto/novo.html.twig',
                ['id' => $id, 'produto' => $produto, 'errors' => array('nome' => null,'descricao' => null,'valor' => null)]);
        })->bind('produto-editar');

        // post dos dados da edição
        $controllers->post("/{id}/editar", function(Request $request, $id) use($app) {
            $result = $app['produtoService']->update($request, $id);

            if (!is_array($result)) {
                return $app['twig']->render('Produto/sucesso.html.twig', ['mensagem' => 'Produto alterado com sucesso!']);
            } else {
                return $app['twig']->render('Produto/novo.html.twig',
                    [
                        'id' => $id,
                        'produto' => $request->request->all(),
                        'errors' => $result
                    ]);
            }
        })->bind('produto-atualizar');

        return $controllers;
    }
}