<?php
namespace AG\Tag\Controller;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

class ApiTagControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        // listar todos
        $controllers->get('/', function (Application $app) {
            $tags = $app['tagService']->fetchAll();

            $arrayTags = array();

            foreach ($tags as $tag)
            {
                $arrayTags[] = $tag->toArray();
            }

            return $app->json($arrayTags);
        })->bind('api-tags-listar');

        // listar apenas 1
        $controllers->get('/{id}', function (Application $app, $id) {
            $categoria = $app['tagService']->fetch($id);

            return $app->json($categoria->toArray());
        })->bind('api-tags-listar-id');

        // cadastrar
        $controllers->post('/', function(Request $request) use($app) {
            $result = $app['tagService']->insert($request);

            if (!is_array($result)) {
                return $app->json($result->toArray());
            } else {
                return $app->json($result);
            }

        })->bind('api-tags-cadastrar');

        // alterar
        $controllers->put("/{id}", function(Request $request, $id) use($app) {
            $result = $app['tagService']->update($request, $id);

            if(!$result) {
                return $app->json(['erro' => 'Tag não encontrada!']);
            }elseif (!is_array($result)) {
                return $app->json($result->toArray());
            } else {
                return $app->json($result);
            }
        })->bind('api-tags-alterar');

        // deletar
        $controllers->delete('/{id}', function($id) use($app){
            $result = $app['tagService']->delete($id);

            if ($result) {
                return $app->json(['success' => "Tag Removida com Sucesso!"]);
            } else {
                return $app->json(['erro' => "Erro ao Remover a Tag"]);
            }
        })->bind('api-tags-deletar');

        return $controllers;
    }
}