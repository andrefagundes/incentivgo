<?php

use Phalcon\DI\FactoryDefault,
    Phalcon\Crypt,
    Phalcon\Mvc\Url as UrlResolver,
    Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
    Phalcon\Mvc\Model\Metadata\Memory as MetaDataAdapter,
    Phalcon\Session\Adapter\Files as SessionAdapter,
    Phalcon\Flash\Direct as Flash,
    Phalcon\Flash\Session as FlashSession,
    Phalcon\Security;

use Incentiv\Auth\Auth,
    Incentiv\Mail\Mail,
    Incentiv\Funcoes\Funcoes;

/**
 * The FactoryDefault Dependency Injector automatically register the right services providing a full stack framework
 */
$di = new FactoryDefault();

/**
 * Register the global configuration as config
 */
$di->set('config', $config);

/**
 * The URL component is used to generate all kind of urls in the application
 */
$di->set('url', function() use ($config) {
	$url = new UrlResolver();
	$url->setBaseUri($config->application->baseUrl);
	return $url;
});

/**
 * Database connection is created based in the parameters defined in the configuration file
 */
$di->set('db', function() use ($config) {
	return new DbAdapter(array(
		'host'      => $config->database->host,
		'username'  => $config->database->username,
		'password'  => $config->database->password,
		'dbname'    => $config->database->dbname
	));
});

/**
 * If the configuration specify the use of metadata adapter use it or use memory otherwise
 */
$di->set('modelsMetadata', function () use ($config) {
    return new MetaDataAdapter(array(
        'metaDataDir' => __DIR__.$config->application->cacheDir . 'metaData/'
    ));
});

/**
 * Start the session the first time some component request the session service
 */
$di->set('session', function() {
	$session = new SessionAdapter();
	$session->start();
	return $session;
});

/**
 * Crypt service
 */
$di->set('crypt', function () use ($config) {
    $crypt = new Crypt();
    $crypt->setKey($config->application->cryptSalt);
    return $crypt;
});

/**
 * Loading routes from the routes.php file
 */
$di->set('router', function () {
    return require __DIR__ . '/routes.php';
});

/**
 * Register the flash service with custom CSS classes
 */
$di->set('flash', function(){
    return new Flash(array(
        'error'     => 'alert alert-danger',
        'success'   => 'alert alert-success',
        'notice'    => 'alert alert-info',
        'warning'   => 'alert'
    ));
});

/**
 * Register the flash service with custom CSS classes
 */
$di->set('flashSession', function(){
    return new FlashSession(array(
        'error'     => 'alert alert-danger',
        'success'   => 'alert alert-success',
        'notice'    => 'alert alert-info',
        'warning'   => 'alert' 
    ));
});

$di->set('security', function(){
        $security = new Security();

        //Set the password hashing factor to 12 rounds
        $security->setWorkFactor(12);

        return $security;
}, true);

/**
 * Custom authentication component
 */
$di->set('auth', function () {
    return new Auth();
});

/**
 * Mail service uses AmazonSES
 */
$di->set('mail', function () {
    return new Mail();
});

/**
 * Funcoes diversas
 */
$di->set('funcoes', function () {
    return new Funcoes();
});

$di->set('lang', function () use ($di) {

    $request = $di->get('request');
    $session = $di->get('session');

    $language = $request->getBestLanguage();

    // TODO mraspor Add cookie check for lang
    if ($session->has('lang') && null != $session->get('lang')) {
        $language = $session->get('lang');
    }
    
    $lang = strtolower(substr($language,0,2));
    
    // Use the user's language, if the file doesn't exist use the default one,
    if (file_exists(__DIR__ . '/../messages/' . $lang . '.php')) {
        require_once __DIR__ . '/../messages/' . $lang . '.php';
    } else {
        // Default language
        require_once __DIR__ . '/../messages/pt.php';
    }
    
    // Return translated content
    return new \Phalcon\Translate\Adapter\NativeArray(array(
        'content' => $messages,
    ));

}, true);