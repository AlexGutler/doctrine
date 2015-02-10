<?php
require_once __DIR__.'/../bootstrap.php';

use AG\config\Routes;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

// cria a rota index
$app->get("/", function() use($app){
    return $app['twig']->render('index.html.twig', []);
})->bind('index');



$app->get("/login", function(Request $request) use($app){
    return $app['twig']->render('login.html.twig', array(
        'error'         => $app['security.last_error']($request),
        'last_username' => $app['session']->get('_security.last_username'),
    ));
    // $user = ($token === null) ? null : $token->getUser();
    // $twig->addGlobal('user', $user);
})->bind('login');

$app->get("/criaAdmin", function() use($app){
    $repo = $app['user_repository'];
    $repo->createAdminUser('admin', 'admin');
});

//$app->post("/login_check", function(Request $request) use($app){
//    // o que fazer aqui para que o security entenda que há um usuário logado ???
//})->bind('login_check');

$app->get("/resetting/request", function() use($app){
    return "Esqueci minha senha.";
})->bind('forgot_password');

// cria as rotas
$routes = new Routes();
$routes->begin($app);

// captura de erro e 404
$app->error(function (\Exception $e, $code) use ($app) {
    if ($code == 404)
    {
        return new Response($app['twig']->render('404.html.twig'), 404);
    }

//    //$username = $app['request']->server->get('PHP_AUTH_USER', false);
//    //echo $username;
//    $token = $app['security']->getToken();
//    if (null !== $token) {
//        $user = $token->getUser();
//    }
//
//    if($user and $e->getMessage() == 'Access Denied') {
//        return new Response($app['twig']->render('access-denied.html.twig'), 403);
//    }
    //return new Response('Desculpe, aconteceu algo errado.<br> Erro: '.$e->getMessage().' - Code: '.$e->getCode(), $code);
});

//Request::enableHttpMethodParameterOverride();

$app->run();