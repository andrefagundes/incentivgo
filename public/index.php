<?php

error_reporting(E_ALL);

try {
        /**
        * Define some useful constants
        */
        define('__APP_ROOT__', dirname(__DIR__));
        define('APP_DIR', __APP_ROOT__ . '/app');
        
        /**
        * Read the configuration
        */
        $config = include APP_DIR .'/config/config.php';
        
        /**
	 * Read auto-loader
	 */
	include APP_DIR .'/config/loader.php';

	/**
	 * Read services
	 */
	include APP_DIR .'/config/services.php';

	/**
	 * Handle the request
	 */
	$application = new \Phalcon\Mvc\Application($di);
        
        /**
	 * Read modules
	 */
	include APP_DIR .'/config/modules.php';

	echo $application->handle()->getContent();

} catch (Phalcon\Exception $e) {
	echo $e->getMessage();
} catch (PDOException $e){
	echo $e->getMessage();
}