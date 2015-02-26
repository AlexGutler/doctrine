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

        $controllers->match("/drop/{id}", function(Request $request, $id) use ($app){
            return $this->deleteAction($app, $request, $id);
        })->bind('drop')->method('GET|DELETE');

        $controllers->match('/password', function(Request $request) use ($app){
            return $this->forgotAction($app, $request);
        })->bind('forgot_password')->method('GET|POST');

        $controllers->match("/reset_password/{salt}", function($salt, Request $request) use ($app){
            return $this->resetPssword($app, $request, $salt);
        })->bind('reset_password')->method('GET|PUT');

        return $controllers;
    }

    public function loginAction(Application $app, Request $request)
    {
        if($request->isMethod('POST')){
            $result = $app['usuarioService']->login($request);

            if($result){
                // criar a sessão
                $app['session']->set(
                    'user',
                    [
                        'id' => $result['id'],
                        'username' => $result['username'],
                        'roles' => $result['roles']
                    ]
                );
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
        return $app['twig']->render(
            'Usuario/editar.html.twig',
            array('usuario' => $usuario)
        );
    }

    public function deleteAction(Application $app, Request $request, $id)
    {
        if($request->isMethod('DELETE')){
            $app['usuarioService']->delete($id);
            return $app->redirect('/ag/usuarios');
        }

        $usuario = $app['usuarioService']->fetch($id);
        return $app['twig']->render(
            'Usuario/excluir.html.twig',
            array('usuario' => $usuario)
        );
    }

    public function logoutAction(Application $app)
    {
        $app['session']->remove('user');
        return $app->redirect('/');
    }

    public function forgotAction(Application $app, Request $request)
    {
        if($request->isMethod('POST')){
            $result = $app['usuarioService']->forgot($request, $app);
            return $app['twig']->render(
                'Usuario/forgot.html.twig',
                [
                    'warning' => $result['warning'],
                    'error' => $result['error']
                ]
            );
        }
        return $app['twig']->render(
            'Usuario/forgot.html.twig',
            [
                'warning' => null,
                'error' => null
            ]
        );
    }

    public function resetPssword(Application $app, Request $request, $salt)
    {
        if($request->isMethod('PUT')){
            $result = $app['usuarioService']->resetPassword($request);
            return $app['twig']->render(
                'Usuario/resetpassword.html.twig',
                [
                    'error' => $result['error'],
                    'warning' => $result['warning'],
                    'salt' => $salt
                ]
            );
        }

        return $app['twig']->render(
            'Usuario/resetpassword.html.twig',
            [
                'error' => null,
                'warning' => null,
                'salt' => $salt
            ]
        );
    }
}