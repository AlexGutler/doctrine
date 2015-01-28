<?php
namespace AG\config;

use AG\Controller\Categoria\ApiCategoriaControllerProvider;
use AG\Controller\Categoria\CategoriaControllerProvider;
use AG\Controller\Produto\ApiProdutoControllerProvider;
use AG\Controller\Produto\ProdutoControllerProvider;
use AG\Controller\Tag\ApiTagControllerProvider;
use AG\Controller\Tag\TagControllerProvider;

use Silex\Application;

class Routes
{
    public function begin(Application $app)
    {
        $app->mount( '/produtos', new ProdutoControllerProvider() );
        $app->mount( '/api/produtos', new ApiProdutoControllerProvider() );

        $app->mount( '/categorias', new CategoriaControllerProvider());
        $app->mount( '/api/categorias', new ApiCategoriaControllerProvider());

        $app->mount( '/tags', new TagControllerProvider());
        $app->mount( '/api/tags', new ApiTagControllerProvider());
    }
}