<?php
namespace AG\Controller\Usuario;

use Silex\Application,
    Silex\ControllerCollection,
    Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

class UsuarioControllerProvider implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

//        $controllers->get('/', function() use ($app){
//            return $this->indexAction($app);
//        })->bind('usuarios');

        $controllers->match("/login", function(Request $request) use ($app){
            return $this->loginAction($app, $request);
        })->bind('login')->method('GET|POST');

        $controllers->get("/logout", function() use ($app){
            return $this->logoutAction($app);
        })->bind('logout');

        $controllers->match("/register", function(Request $request) use ($app){
            return $this->createAction($app, $request);
        })->bind('register')->method('GET|POST');

        $controllers->match("/account", function(Request $request) use ($app){
            return $this->updateAction($app, $request);
        })->bind('account')->method('GET|PUT');

        $controllers->match("/drop", function(Request $request, $id) use ($app){
            return $this->deleteAction($app, $request, $id);
        })->bind('drop')->method('GET|DELETE');

        return $controllers;
    }

//    public function indexAction(Application $app)
//    {
//        return $app['twig']->render('Usuario/index.html.twig');
//    }

    public function loginAction(Application $app, Request $request)
    {
        if($request->isMethod('POST')){
            $result = $app['usuarioService']->login($request);

            if($result){
                // criar a sessão
                $app['session']->set('user', array('username' => $result['username'], 'id' => $result['id']));
            } else {
                // renderizar a página de login com error
                return $app['twig']->render(
                    'Usuario/login.html.twig',
                    [
                        'error' => 'Usuário ou senha inválidos.',
                        'username' => $request->get('username'),
                    ]
                );
            }
            return $app->redirect('/');
        }

        return $app['twig']->render(
            'Usuario/login.html.twig',
            [
                'error' => null,
                'username' => null
            ]
        );
    }

    public function createAction(Application $app, Request $request)
    {
        if($request->isMethod('POST')){
            $result = $app['usuarioService']->insert($request);

            if(!is_array($result)){
                return $app->redirect('/');
            } else {
                // renderizar a página de novo com errors
                return $app['twig']->render(
                    'Usuario/novo.html.twig',
                    [
                        'errors' => $result,
                        'usuario' => $request->request->all(),
                    ]
                );
            }
        }
        return $app['twig']->render(
            'Usuario/novo.html.twig',
            [
                'errors' => array('email' => null, 'username' => null, 'password' => null, 'role' => null),
                'usuario' => array('email' => null, 'username' => null, 'password' => null, 'role' => 'ROLE_USER'),
            ]
        );
    }

    public function updateAction(Application $app, Request $request)
    {
        if($request->isMethod('PUT')){
            $app['usuarioService']->insert($request);
            return $app->redirect('/ag/usuarios');
        }

        $usuario = $app['usuarioService']->fetch();
        return $app['twig']->render('Usuario/editar.twig', array('usuario' => $usuario));
    }

    public function deleteAction(Application $app, Request $request, $id)
    {
        if($request->isMethod('DELETE')){
            $app['usuarioService']->delete($id);
            return $app->redirect('/ag/usuarios');
        }

        $usuario = $app['usuarioService']->fetch($id);
        return $app['twig']->render('Usuario/excluir.twig', array('usuario' => $usuario));
    }

    public function logoutAction(Application $app)
    {
        $app['session']->remove('user');
        return $app->redirect('/');
    }


}