<?php
namespace AG\Controller\Categoria;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

class CategoriaControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];


        $controllers->get('/', function (Application $app) {
            return $app->redirect('pag/1');
        })->bind('categorias');


        $controllers->get('/pag/{id}', function (Application $app, $id) {
            if(!isset($id)){$id = 1;}

            $limit = 5;
            $offset = ($id - 1) * $limit;

            $pags = $app['categoriaService']->fetchAll();
            $numPages = ceil(count($pags)/$limit);

            $categorias = $app['categoriaService']->fetchPagination($offset, $limit);


            return $app['twig']->render(
                'Categoria/index.html.twig',
                ['categorias' => $categorias, 'paginas' => $numPages, 'activepage' => $id]
            );
        })->bind('categorias-pagination');


        $controllers->post("/find", function(Request $request) use($app){
            $options = array(
                'coluna' => 'nome',
                'valor' => $request->get('nome')
            );

            $categorias = $app['categoriaService']->buscarCategorias($options);

            return $app['twig']->render(
                'Categoria/index.html.twig',
                ['categorias' => $categorias, 'paginas' => 0]
            );
        })->bind('categorias-find');


        $controllers->get("/novo", function() use($app){
            return $app['twig']->render('Categoria/novo.html.twig', ['errors' => array('nome' => null),]);
        })->bind('categoria-novo');


        $controllers->post("/novo", function(Request $request) use($app) {
            $result = $app['categoriaService']->insert($request);

            if (!is_array($result)) {
                return $app->redirect('/categorias');
            } else {
                return $app['twig']->render(
                    'Categoria/novo.html.twig',
                    [
                        'errors' => $result,
                        'categoria' => $request->request->all()
                    ]);
            }
        })->bind('categoria-salvar');


        $controllers->get('/{id}/deletar', function($id) use($app){
            $categoria = $app['categoriaService']->fetch($id);

            return $app['twig']->render('Categoria/excluir.html.twig',
                [
                    'categoria' => $categoria,
                    'errors' => array('nome' => null),
                ]);
        })->bind('categoria-deletar-form');


        $controllers->post('/{id}/deletar', function($id) use($app){
            $result = $app['categoriaService']->delete($id);
            if ($result)
            {
                return $app->redirect('/categorias');
            } else {
                $app->abort(500, "Erro ao deletar a categoria");
            }
        })->bind('categoria-deletar');


        $controllers->get("/{id}/editar", function($id) use($app){
            $categoria = $app['categoriaService']->fetch($id);

            return $app['twig']->render('Categoria/editar.html.twig',
                [
                    'categoria' => $categoria,
                    'errors' => array('nome' => null),
                ]);
        })->bind('categoria-editar');


        $controllers->post("/{id}/editar", function(Request $request, $id) use($app) {
            $result = $app['categoriaService']->update($request, $id);

            if (!is_array($result)) {
                return $app->redirect('/categorias');
            } else {
                return $app['twig']->render('Categoria/editar.html.twig',
                    [
                        'id' => $id,
                        'categoria' => $request->request->all(),
                        'errors' => $result
                    ]);
            }
        })->bind('categoria-atualizar');


        return $controllers;
    }
}