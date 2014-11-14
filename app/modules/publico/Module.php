<?php

namespace Publico;

use Phalcon\Loader,
    Phalcon\Mvc\View,
    Phalcon\Mvc\View\Engine\Volt as VoltEngine,
    Phalcon\Mvc\ModuleDefinitionInterface,
    Phalcon\Mvc\Dispatcher;

use Incentiv\Plugins\SecurityPlugin,
    Incentiv\Plugins\NotFoundPlugin;

class Module implements ModuleDefinitionInterface {

    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders() {
        $loaderPublico = new Loader();

        $loaderPublico->registerNamespaces(array(
            'Publico\Controllers' => __DIR__ . '/controllers/',
            'Publico\Forms' => __DIR__ . '/forms/'
        ));

        $loaderPublico->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices($di) {


        $di->set('dispatcher', function() use ($di) {

            //Obtain the standard eventsManager from the DI
            $eventsManager = $di->getShared('eventsManager');

            //Instantiate the Security plugin
            $security = new SecurityPlugin($di);

            //Listen for events produced in the dispatcher using the Security plugin
            $eventsManager->attach('dispatch:beforeExecuteRoute', $security);
            $eventsManager->attach('dispatch:beforeException', new NotFoundPlugin());
            $dispatcher = new Dispatcher();

            //Bind the EventsManager to the Dispatcher
            $dispatcher->setEventsManager($eventsManager);
            $dispatcher->setDefaultNamespace('Publico\Controllers');

            return $dispatcher;
        });

        /**
         * Setting up the view component
         */
        $di->set('view', function() {

            $view = new View();
            $view->setViewsDir(__DIR__ . '/views/');

            $view->registerEngines(array(
                '.phtml' => function($view, $di) {
                    $volt = new VoltEngine($view, $di);
                    $volt->setOptions(array(
                        'compiledPath' => __DIR__ . '/../../cache/volt/',
                        'compiledSeparator' => '_'
                    ));
                    return $volt;
                }
            ));

            return $view;
        }, true);
    }

}