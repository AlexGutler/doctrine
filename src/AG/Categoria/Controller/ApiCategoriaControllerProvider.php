<?php
namespace AG\Categoria\Controller;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

class ApiCategoriaControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        // listar todos
        $controllers->get('/', function (Application $app) {
            $categorias = $app['categoriaService']->fetchAll();

            $arrayCategorias = array();

            foreach ($categorias as $categoria)
            {
                $arrayCategorias[] = $categoria->toArray();
            }

            return $app->json($arrayCategorias);
        })->bind('api-categorias-listar');

        // listar apenas 1
        $controllers->get('/{id}', function (Application $app, $id) {
            $categoria = $app['categoriaService']->fetch($id);

            return $app->json($categoria->toArray());
        })->bind('api-categorias-listar-id');

        // cadastrar
        $controllers->post('/', function(Request $request) use($app) {
            $result = $app['categoriaService']->insert($request);

            if (!is_array($result)) {
                return $app->json($result->toArray());
            } else {
                return $app->json($result);
            }

        })->bind('api-categorias-cadastrar');

        // alterar
        $controllers->put("/{id}", function(Request $request, $id) use($app) {
            $result = $app['categoriaService']->update($request, $id);

            if(!$result) {
                return $app->json(['erro' => 'Categoria não encontrada!']);
            }elseif (!is_array($result)) {
                return $app->json($result->toArray());
            } else {
                return $app->json($result);
            }
        })->bind('api-categorias-alterar');

        // deletar
        $controllers->delete('/{id}', function($id) use($app){
            $result = $app['categoriaService']->delete($id);

            if ($result) {
                return $app->json(['success' => "Produto Removido com Sucesso!"]);
            } else {
                return $app->json(['erro' => "Erro ao Remover o produto"]);
            }
        })->bind('api-categorias-deletar');

        return $controllers;
    }
}