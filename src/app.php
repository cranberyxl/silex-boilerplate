<?php

use App\Validator\Constraints\UniqueEntityValidator;
use App\Validator\ConstraintValidatorFactory;
use Doctrine\Bundle\MongoDBBundle\Form\Type\DocumentType;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use Neutron\Silex\Provider\MongoDBODMServiceProvider;
use Silex\Application;
use Silex\Provider\DoctrineManagerRegistryServiceProvider;
use Symfony\Component\Form\PreloadedExtension;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Mapping\ClassMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;

require_once __DIR__.'/../vendor/autoload.php';
AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/mongodb-odm/lib/Doctrine/ODM/MongoDB/Mapping/Annotations/DoctrineAnnotations.php');
AnnotationRegistry::registerFile(__DIR__.'/../vendor/symfony/validator/Symfony/Component/Validator/Constraints/NotBlank.php');
AnnotationRegistry::registerFile(__DIR__.'/../vendor/doctrine/mongodb-odm-bundle/Doctrine/Bundle/MongoDBBundle/Validator/Constraints/Unique.php');

$app = new Application();
$app['debug'] = true;

// Register prodivers
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
    'twig.options' => array(
        'strict_variables' => false
    )
));
$app->register(new MongoDBODMServiceProvider(), array(
    'doctrine.odm.mongodb.connection_options' => array(
    ),
    'doctrine.odm.mongodb.documents' => array(
        0 => array(
            'type' => 'annotation',
            'path' => array(
                'src/App',
            ),
            'namespace' => 'App'
        ),
    ),
    'doctrine.odm.mongodb.proxies_dir'           => __DIR__.'/../cache/doctrine/odm/mongodb/Proxy',
    'doctrine.odm.mongodb.proxies_namespace'     => 'DoctrineMongoDBProxy',
    'doctrine.odm.mongodb.auto_generate_proxies' => true,
    'doctrine.odm.mongodb.hydrators_dir'         => __DIR__.'/../cache/doctrine/odm/mongodb/Hydrator',
    'doctrine.odm.mongodb.hydrators_namespace'   => 'DoctrineMongoDBHydrator',
    'doctrine.odm.mongodb.metadata_cache'        => new \Doctrine\Common\Cache\ArrayCache(),
//     'doctrine.odm.mongodb.logger_callable'       => $app->protect(function($foo) {echo(json_encode($foo) ."\n");}),
));
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\FormServiceProvider());
$app->register(new Silex\Provider\ServiceControllerServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\ValidatorServiceProvider(), array(
    'validator.mapping.class_metadata_factory' => new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader())),
    'validator.validator_factory' => new ConstraintValidatorFactory($app),
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/../views',
    'twig.options' => array(
        'strict_variables' => false
    )
));
$app->register(new Silex\Provider\TranslationServiceProvider(), array());
$app->register(new DoctrineManagerRegistryServiceProvider(), array(
    'doctrine.common.manager_registry.definitions' => array(
        'mongodb' => array(
            'connections' => array('default' => 'doctrine.odm.mongodb.connection'),
            'managers' => array('default' => 'doctrine.odm.mongodb.dm'),
            'default_connection' => 'default',
            'default_manager' => 'default',
            'proxy_interface_name' => 'Doctrine\ODM\MongoDB\Proxy\Proxy'
        )
    ),
));

// DI
$app['doctrine_odm.mongodb.unique'] = $app->share(function(Application $app) {
   return new UniqueEntityValidator($app['doctrine.common.manager_registry.map']['mongodb']);
});
$app['form.extensions'] = $app->share($app->extend('form.extensions', function($extensions) use ($app) {
    $types = array('document' => new DocumentType($app['doctrine.common.manager_registry.map']['mongodb']));

    $extensions[] = new PreloadedExtension($types, array());

    return $extensions;
}));
$app['dm'] = $app->share(function(Application $app) {
   return $app['doctrine.odm.mongodb.dm'];
});

require 'controllers.php';

return $app;
