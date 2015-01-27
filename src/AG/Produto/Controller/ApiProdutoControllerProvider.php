<?php
namespace AG\Produto\Controller;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

class ApiProdutoControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        // listar todos
        $controllers->get('/', function (Application $app) {
            $produtos = $app['produtoService']->fetchAll();

            return $app->json($produtos);
        })->bind('api-produtos-listar');

        // listar apenas 1
        $controllers->get('/{id}', function (Application $app, $id) {
            $produto = $app['produtoService']->fetch($id);
            if($produto){
                return $app->json($produto);
            } else {
                return $app->json(['erro' => 'Produto não encontrado!']);
            }
        })->bind('api-produtos-listar-id');

        // cadastrar
        $controllers->post('/', function(Request $request) use($app) {
            $result = $app['produtoService']->insert($request);

            if (!is_array($result)) {
                return $app->json(['success' => true, 'messages' => ['produto cadastrado com sucesso']]);
            } else {
                return $app->json($result);
            }

        })->bind('api-produtos-cadastrar');

        // alterar
        $controllers->put("/{id}", function(Request $request, $id) use($app) {
            $result = $app['produtoService']->update($request, $id);

            if(!$result) {
                return $app->json(['erro' => 'Produto não encontrado!']);
            }elseif (!is_array($result)) {
                return $app->json(['success' => true, 'messages' => ['Produto alterado com sucesso']]);
            } else {
                return $app->json($result);
            }
        })->bind('api-produtos-alterar');

        // deletar
        $controllers->delete('/{id}', function($id) use($app){
            $result = $app['produtoService']->delete($id);

            if ($result) {
                return $app->json(['success' => "Produto Removido com Sucesso!"]);
            } else {
                return $app->json(['erro '=> "Erro ao Remover o produto"]);
            }
        })->bind('api-produtos-deletar');

        return $controllers;
    }
}