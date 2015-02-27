<?php
namespace AG\config;

use AG\Controller\Categoria\ApiCategoriaControllerProvider;
use AG\Controller\Categoria\CategoriaControllerProvider;
use AG\Controller\Produto\ApiProdutoControllerProvider;
use AG\Controller\Produto\ProdutoControllerProvider;
use AG\Controller\Tag\ApiTagControllerProvider;
use AG\Controller\Tag\TagControllerProvider;

use AG\Controller\Usuario\UsuarioControllerProvider;
use Silex\Application;

class Routes
{
    public function begin(Application $app, $before)
    {
        $app->mount('ag/produtos', new ProdutoControllerProvider($before));
        $app->mount('ag/api/produtos', new ApiProdutoControllerProvider() );

        $app->mount('ag/categorias', new CategoriaControllerProvider($before));
        $app->mount('ag/api/categorias', new ApiCategoriaControllerProvider());

        $app->mount('ag/tags', new TagControllerProvider($before));
        $app->mount('ag/api/tags', new ApiTagControllerProvider());

        $app->mount('ag/user', new UsuarioControllerProvider());
    }
}