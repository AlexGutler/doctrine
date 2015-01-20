<?php
require_once __DIR__.'/../bootstrap.php';

use AG\Database\DB;
use AG\Produto\Entity\Produto,
    AG\Produto\Mapper\ProdutoMapper,
    AG\Produto\Service\ProdutoService,
    AG\Produto\Validator\ProdutoValidator,
    AG\Produto\Controller\ProdutoControllerProvider,
    AG\Produto\Controller\ApiProdutoControllerProvider;
use Symfony\Component\HttpFoundation\Response,
    Symfony\Component\HttpFoundation\Request;

/* CONFIGURAÇÃO DE DEPENDENCIAS - PIMPLE */
// criando a conexão
$config = include __DIR__ .'/../src/AG/config/config.php';
$app['conn'] = function() use ($config){
    return (new DB($config['db']['dsn'], $config['db']['dbname'], $config['db']['username'], $config['db']['password']))->getConnection();
};
// armazenando a entidade produto
$app['produto'] = function(){
    return new Produto();
};
//armazenando o mapper do produto
$app['mapper'] = function() use ($em) {
    return new ProdutoMapper($em);
};
// armazenando a dependencia ao ProdutoValidator
$app['produtoValidator'] = function(){
  return new ProdutoValidator();
};
// armazenar o service do produto
$app['produtoService'] = function() use ($app, $em) {
    return new ProdutoService($em, $app['produtoValidator']);
};

// mount no ControllerProvider de Produtos
$app->mount('/produtos', new ProdutoControllerProvider());

// mount no API REST
$app->mount('/api/produtos', new ApiProdutoControllerProvider());

$app->get("/", function() use($app){
    return $app['twig']->render('index.html.twig', []);
})->bind('index');

$app->error(function (\Exception $e, $code) use ($app) {

    if ($code == 404) {
        return new Response( $app['twig']->render('404.html.twig'), 404);
    }

    return new Response('Desculpe, aconteceu algo errado.<br> Erro: '.$e->getMessage(), $code);
});

$app->run();