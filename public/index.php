<?php
require_once __DIR__.'/../bootstrap.php';

use AG\config\Routes;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;


$before = function (Request $request, \Silex\Application $app) {
    if (null === $user = $app['session']->get('user')) {
        return $app->redirect('/ag/user/login');
    }
};
// cria a rota index
$app->get("/", function() use($app){
    return $app['twig']->render('index.html.twig', []);
})->bind('index')->before($before);

//$app->get("/login", function(Request $request) use($app){
//    return $app['twig']->render('login.html.twig', array(
//        'error'         => $app['security.last_error']($request),
//        'last_username' => $app['session']->get('_security.last_username'),
//    ));
//})->bind('login');
//
//$app->get("/criaAdmin", function() use($app){
//    $repo = $app['user_repository'];
//    $repo->createAdminUser('admin', 'admin');
//});


$app->get("/resetting/request", function() use($app){
    return "Esqueci minha senha.";
})->bind('forgot_password');

// cria as rotas
$routes = new Routes();
$routes->begin($app);

// captura de erro e 404
$app->error(function (\Exception $e, $code) use ($app) {
    if ($code == 404) {
        return new Response($app['twig']->render('404.html.twig'), 404);
    }

    // capturar o usuário logado
    //$username = $app['security']->getToken()->getUser()->getUserName();

//    if ($app['current_username'] !== null AND $e->getMessage() == 'Access Denied') {
//        return new Response($app['twig']->render('access-denied.html.twig'), 403);
//    } else {
//        return new Response($app->redirect('/'));
//    }
    //return new Response('Desculpe, aconteceu algo errado.<br> Erro: '.$e->getMessage().' - Code: '.$e->getCode(), $code);
});

//Request::enableHttpMethodParameterOverride();

$app->run();