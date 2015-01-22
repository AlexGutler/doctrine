<?php
require_once __DIR__.'/../bootstrap.php';

use AG\Database\DB;
use AG\Produto\Entity\Produto,
    AG\Produto\Mapper\ProdutoMapper,
    AG\Produto\Service\ProdutoService,
    AG\Produto\Validator\ProdutoValidator,
    AG\Produto\Controller\ProdutoControllerProvider,
    AG\Produto\Controller\ApiProdutoControllerProvider;
use AG\Categoria\Service\CategoriaService,
    AG\Categoria\Controller\ApiCategoriaControllerProvider,
    AG\Categoria\Validator\CategoriaValidator;
use AG\Tag\Controller\ApiTagControllerProvider,
    AG\Tag\Validator\TagValidator,
    AG\Tag\Service\TagService;
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
// armazenar o validator da categoria
$app['categoriaValidator'] = function(){
    return new CategoriaValidator();
};
// armazenar o validator da categoria
$app['tagValidator'] = function(){
    return new TagValidator();
};
// armazenar o service do produto
$app['produtoService'] = function() use ($app, $em) {
    return new ProdutoService($em, $app['produtoValidator']);
};
// armazenar o service da categoria
$app['categoriaService'] = function() use ($app, $em) {
    return new CategoriaService($em, $app['categoriaValidator']);
};
// armazenar o service da tag
$app['tagService'] = function() use ($app, $em) {
    return new TagService($em, $app['tagValidator']);
};

// mount no ControllerProvider de Produtos
$app->mount('/produtos', new ProdutoControllerProvider());

// mount no API REST produtos
$app->mount('/api/produtos', new ApiProdutoControllerProvider());

// mount no API REST categorias
$app->mount('/api/categorias', new ApiCategoriaControllerProvider());

// mount no API REST categorias
$app->mount('/api/tags', new ApiTagControllerProvider());

$app->get("/", function() use($app){
    return $app['twig']->render('index.html.twig', []);
})->bind('index');

$app->error(function (\Exception $e, $code) use ($app) {
    if ($code == 404)
    {
        return new Response( $app['twig']->render('404.html.twig'), 404);
    }
    return new Response('Desculpe, aconteceu algo errado.<br> Erro: '.$e->getMessage(), $code);
});

$app->run();