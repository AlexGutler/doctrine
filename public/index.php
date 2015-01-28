<?php
require_once __DIR__.'/../bootstrap.php';

use AG\Config\Routes;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

// cria a rota index
$app->get("/", function() use($app){
    return $app['twig']->render('index.html.twig', []);
})->bind('index');

// cria as rotas
$routes = new Routes();
$routes->begin($app);

// captura de erro e 404
$app->error(function (\Exception $e, $code) use ($app) {
    if ($code == 404)
    {
        return new Response( $app['twig']->render('404.html.twig'), 404);
    }
    return new Response('Desculpe, aconteceu algo errado.<br> Erro: '.$e->getMessage(), $code);
});

Request::enableHttpMethodParameterOverride();

$app->run();