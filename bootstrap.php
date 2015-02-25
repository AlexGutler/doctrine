<?php
require_once "vendor/autoload.php";

use AG\Database\DB;
use AG\Entity\Produto\Produto,
    AG\Service\Produto\ProdutoService,
    AG\Utils\Validator\Produto\ProdutoValidator;
use AG\Service\Categoria\CategoriaService,
    AG\Utils\Validator\Categoria\CategoriaValidator;
use AG\Utils\Validator\Tag\TagValidator,
    AG\Service\Tag\TagService;
use AG\Utils\Validator\Usuario\UsuarioValidator,
    AG\Service\Usuario\UsuarioService,
    AG\Entity\Usuario\Usuario;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;
use AG\Entity\User\UserProvider;

/* DOCTRINE */
use Doctrine\ORM\Tools\Setup,
    Doctrine\ORM\EntityManager,
    Doctrine\Common\EventManager as EventManager,
    Doctrine\ORM\Events,
    Doctrine\ORM\Configuration,
    Doctrine\Common\Cache\ArrayCache as Cache,
    Doctrine\Common\Annotations\AnnotationRegistry,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\Common\ClassLoader;

$cache = new Doctrine\Common\Cache\ArrayCache;
$annotationReader = new Doctrine\Common\Annotations\AnnotationReader;

$cachedAnnotationReader = new Doctrine\Common\Annotations\CachedReader(
    $annotationReader, // use reader
    $cache // and a cache driver
);

$annotationDriver = new Doctrine\ORM\Mapping\Driver\AnnotationDriver(
    $cachedAnnotationReader, // our cached annotation reader
    array(__DIR__ . DIRECTORY_SEPARATOR . 'src')
);

$driverChain = new Doctrine\ORM\Mapping\Driver\DriverChain();
$driverChain->addDriver($annotationDriver, 'AG');

$config = new Doctrine\ORM\Configuration;
$config->setProxyDir('/tmp');
$config->setProxyNamespace('Proxy');
$config->setAutoGenerateProxyClasses(true); // this can be based on production config.
// register metadata driver
$config->setMetadataDriverImpl($driverChain);
// use our allready initialized cache driver
$config->setMetadataCacheImpl($cache);
$config->setQueryCacheImpl($cache);

AnnotationRegistry::registerFile(__DIR__. DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'doctrine' . DIRECTORY_SEPARATOR . 'orm' . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR . 'Doctrine' . DIRECTORY_SEPARATOR . 'ORM' . DIRECTORY_SEPARATOR . 'Mapping' . DIRECTORY_SEPARATOR . 'Driver' . DIRECTORY_SEPARATOR . 'DoctrineAnnotations.php');

$evm = new Doctrine\Common\EventManager();
$em = EntityManager::create(
    array(
        'driver'  => 'pdo_mysql',
        'host'    => '127.0.0.1',
        'port'    => '3306',
        'user'    => 'root',
        'password'  => '00fb00',
        'dbname'  => 'silexdb',
    ),
    $config,
    $evm
);
/* /DOCTRINE */

$app = new \Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/src/AG/views',
));

$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app['upload_folder'] = __DIR__ . '/public/imagens';

/* CONFIGURAÇÃO DE DEPENDENCIAS - PIMPLE */
// criando a conexão
$config = include __DIR__ . '/src/AG/config/config.php';
$app['conn'] = function() use ($config){
    return (new DB($config['db']['dsn'], $config['db']['dbname'], $config['db']['username'], $config['db']['password']))->getConnection();
};
// armazenando a entidade produto
$app['produto'] = function(){
    return new Produto();
};
// armazenando a entidade usuario
$app['usuario'] = function(){
    return new Usuario();
};
// armazenando a dependencia ao ProdutoValidator
$app['produtoValidator'] = function(){
    return new ProdutoValidator();
};
// armazenando a dependencia ao UsuarioValidator
$app['usuarioValidator'] = function(){
    return new UsuarioValidator();
};
// armazenar o validator da categoria
$app['categoriaValidator'] = function(){
    return new CategoriaValidator();
};
// armazenar o validator da tag
$app['tagValidator'] = function(){
    return new TagValidator();
};
// armazenar o service do produto
$app['produtoService'] = function() use ($app, $em) {
    return new ProdutoService($app['produto'], $em, $app['produtoValidator']);
};
// armazenar o service da categoria
$app['categoriaService'] = function() use ($app, $em) {
    return new CategoriaService($em, $app['categoriaValidator']);
};
// armazenar o service da tag
$app['tagService'] = function() use ($app, $em) {
    return new TagService($em, $app['tagValidator']);
};
// armazenar o service do usuario
$app['usuarioService'] = function() use ($app, $em) {
    return new UsuarioService($app['usuario'], $em, $app['usuarioValidator']);
};
// ----------------------------------------------------------------
$app->register(new SessionServiceProvider());