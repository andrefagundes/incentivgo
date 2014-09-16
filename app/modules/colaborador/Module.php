<?php
namespace Colaborador;

use Phalcon\Loader,
    Phalcon\Mvc\View,
    Phalcon\Mvc\Dispatcher as Dispatcher,
    Phalcon\Mvc\View\Engine\Volt as VoltEngine,
    Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders()
    {
        $loader = new Loader();

        $loader->registerNamespaces(array(
            'Colaborador\Controllers' => __DIR__ . '/controllers/',
            'Colaborador\Forms' => __DIR__ . '/forms/'
        ));

        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices($di)
    {
        /**
        * Dispatcher use a default namespace
        */
       $di->set('dispatcher', function () {
           $dispatcher = new Dispatcher();
           $dispatcher->setDefaultNamespace('Colaborador\Controllers');
           return $dispatcher;
       });
        
        /**
         * Setting up the view component
         */
        $di->set('view', function() {

                $view = new View();
                $view->setViewsDir(__DIR__.'/views/');

                $view->registerEngines(array(
                    '.phtml' => function($view, $di) {
                        $volt = new VoltEngine($view, $di);
                        $volt->setOptions(array(
                            'compiledPath' => __DIR__.'/../../cache/volt/',
                            'compiledSeparator' => '_'
                        ));
                        return $volt;
                    }
                ));

                return $view;
        }, true);
    }
}