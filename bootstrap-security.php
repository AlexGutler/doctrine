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

$app['upload_folder'] = __DIR__ . '/public_html/imagens';

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
// armazenando a dependencia ao ProdutoValidator
$app['produtoValidator'] = function(){
    return new ProdutoValidator();
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
// ----------------------------------------------------------------
$app->register(new SessionServiceProvider());
$app['user_provider'] = $app->share(function($app) use($em){
    $user = new AG\Entity\User\User;

    // find the encoder for a UserInterface instance
    $encoder = $app['security.encoder_factory']->getEncoder($user);

    // compute the encoded password for foo

    //echo $password = $encoder->encodePassword('00fb00', $user->getSalt());

    //echo $password = $encoder->encodePassword('admin', $user->getSalt());

    // $app['security.encoder.digest']->encodePassword('password', '');

    $userProvider = new UserProvider($em, $encoder);

    return $userProvider;
});


/* MINE */
//$app->register(new SecurityServiceProvider(), array(
//    'security.firewalls' => array(
//        'tags' => array(
//            'pattern' => '^/tags/',
//            'http' => true,
//            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
//            'users' => $app->share(function() use ($app) {
//                return $app['user_provider'];
//            }),
//        ),
//    )
//));


/* THEIRS */
$app->register(new SecurityServiceProvider(), array(
    'security.firewalls' => array(
        'admin' => array(
            'pattern' => '^/ag/',
            //'http' => true,
            'form' => array(
                'login_path' => '/login',
                'check_path' => '/ag/login_check'
            ),
            'logout' => array('logout_path' => '/ag/logout'),
            'users' => $app->share(function() use ($app) {
                return $app['user_provider'];
            }),
        ),
    )
));

//$app->register(new SecurityServiceProvider(), array(
//    'security.firewalls' => array(
//        'tags' => array(
//            'pattern' => '^/tags/',
//            'http' => true,
//            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
//
//            'users' => $app->share(function() use ($app) {
//                return $app['user_provider'];
//            }),
//        ),
//    )
//));


/* BLOQUEIA TUDO  */
//$app->register(new SecurityServiceProvider(), array(
//    'security.firewalls' => array(
//        'login' => array(
//            'pattern' => '^/login$',
//        ),
//        'secured' => array(
//            'pattern' => '^.*$',
//            'form' => array(
//                'login_path' => '/login',
//                'check_path' => '/login_check'
//            ),
//            'logout' => array('logout_path' => '/logout'),
//            'users' => $app->share(function() use ($app) {
//                return $app['user_provider'];
//            }),
//        ),
//    )
//));



//'secured' => array(
//    'pattern' => '^.*$',

//
//$app->register(new SecurityServiceProvider(), array(
//    'security.firewalls' => array(
//        'admin' => array(
//            'anonymous' => true,
//            'pattern' => '^/',
//            'http' => true,
//            'form' => array('login_path' => '/login', 'check_path' => '/admin/login_check'),
//            'users' => $app->share(function() use ($app) {
//                return $app['user_provider'];
//            }),
//            'logout' => array('logout_path' => '/admin/logout')
//        )
//    ),
//));
//
//$app['security.access_rules'] = array(
//    array('^/tags/' => 'ROLE_ADMIN')
//);


//$app['security.access_rules'] = array(
//    array('^/tags/', 'ROLE_ADMIN'),
//    array('^/produtos/', 'ROLE_ADMIN'),
//    array('^/categorias/', 'ROLE_ADMIN'),
//    //array('^.*$', 'ROLE_USER'),
//);

$app['security.access_rules'] = array(
//    array('^/tags/', 'ROLE_ADMIN'),
//    array('^/produtos/', 'ROLE_ADMIN'),
//    array('^/categorias/', 'ROLE_ADMIN'),
    array('^.*$', 'ROLE_ADMIN'),
);

//$app['security.access_rules'] = array(
//    array('^/tags/', 'ROLE_ADMIN')
//    //array('^.*$', 'ROLE_USER'),
//);

$app['current_username'] = function() use($app) {
    $token = $app['security']->getToken();
    if (null !== $token) {
        $username = $token->getUser()->getUserName();
    } else {
        $username = null;
    }
    return $username;
};