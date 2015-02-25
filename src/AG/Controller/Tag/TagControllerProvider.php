<?php
namespace AG\Controller\Tag;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

class TagControllerProvider implements ControllerProviderInterface
{
    protected $before;

    public function __construct($before){
        $this->before = $before;
    }

    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/', function (Application $app) {
            return $app->redirect('pag/1');
        })->bind('tags')->before($this->before);

        $controllers->get('/pag/{id}', function (Application $app, $id) {
            if(!isset($id)){$id = 1;}

            $limit = 5;
            $offset = ($id - 1) * $limit;

            $pags = $app['tagService']->fetchAll();
            $numPages = ceil(count($pags)/$limit);

            $tags = $app['tagService']->fetchPagination($offset, $limit);

            return $app['twig']->render(
                'Tag/index.html.twig',
                ['tags' => $tags, 'paginas' => $numPages, 'activepage' => $id]
            );
        })->bind('tags-pagination');

        $controllers->post("/find", function(Request $request) use($app){
            $options = array(
                'coluna' => 'nome',
                'valor' => $request->get('nome')
            );

            $tags = $app['tagService']->buscarTags($options);

            return $app['twig']->render(
                'Tag/index.html.twig',
                ['tags' => $tags, 'paginas' => 0]
            );
        })->bind('tags-find');

        $controllers->get("/novo", function() use($app){
            return $app['twig']->render('Tag/novo.html.twig', ['errors' => array('nome' => null),]);
        })->bind('tags-novo');

        $controllers->post("/novo", function(Request $request) use($app) {
            $result = $app['tagService']->insert($request);

            if (!is_array($result)) {
                return $app->redirect('/ag/tags');
            } else {
                return $app['twig']->render(
                    'Tag/novo.html.twig',
                    [
                        'errors' => $result,
                        'tag' => $request->request->all()
                    ]);
            }
        })->bind('tags-salvar');

        $controllers->get('/{id}/deletar', function($id) use($app){
            $tag = $app['tagService']->fetch($id);

            return $app['twig']->render('Tag/excluir.html.twig',
                [
                    'tag' => $tag,
                    'errors' => array('nome' => null),
                ]);
        })->bind('tags-deletar-form');

        $controllers->post('/{id}/deletar', function($id) use($app){
            $result = $app['tagService']->delete($id);
            if ($result)
            {
                return $app->redirect('/ag/tags');
            } else {
                $app->abort(500, "Erro ao deletar a tag");
            }
        })->bind('tags-deletar');

        $controllers->get("/{id}/editar", function($id) use($app){
            $tag = $app['tagService']->fetch($id);

            return $app['twig']->render('Tag/editar.html.twig',
                [
                    'tag' => $tag,
                    'errors' => array('nome' => null),
                ]);
        })->bind('tags-editar');

        $controllers->post("/{id}/editar", function(Request $request, $id) use($app) {
            $result = $app['tagService']->update($request, $id);

            if (!is_array($result)) {
                return $app->redirect('/ag/tags');
            } else {
                return $app['twig']->render('Tag/editar.html.twig',
                    [
                        'id' => $id,
                        'tag' => $request->request->all(),
                        'errors' => $result
                    ]);
            }
        })->bind('tags-atualizar');

        return $controllers;
    }
}