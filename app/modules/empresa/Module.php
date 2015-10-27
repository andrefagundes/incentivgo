<?php
namespace Empresa;

use Phalcon\Loader,
    Phalcon\Mvc\View,
    Phalcon\Mvc\Dispatcher as Dispatcher,
    Phalcon\Mvc\View\Engine\Volt as VoltEngine,
    Phalcon\Mvc\ModuleDefinitionInterface;

use Incentiv\Plugins\SecurityPlugin,
    Incentiv\Plugins\NotFoundPlugin;

class Module implements ModuleDefinitionInterface
{
    /**
     * Registers the module auto-loader
     */
    public function registerAutoloaders(\Phalcon\DiInterface $dependencyInjector = null)
    {
        $loader = new Loader();

        $loader->registerNamespaces(array(
            'Empresa\Controllers' => __DIR__ . '/controllers/',
            'Empresa\Forms' => __DIR__ . '/forms/'
        ));

        $loader->register();
    }

    /**
     * Registers the module-only services
     *
     * @param Phalcon\DI $di
     */
    public function registerServices(\Phalcon\DiInterface $di)
    {
       /**
         * Dispatcher use a default namespace and register the events manager
         */
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
            $dispatcher->setDefaultNamespace('Empresa\Controllers');

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
                            'compiledSeparator' => '-'
                        ));
                        return $volt;
                    }
                ));

                return $view;
        }, true);
        
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
            if (file_exists(__DIR__ . '/messages/' . $lang . '.php')) {
                require_once __DIR__ . '/messages/' . $lang . '.php';
            } else {
                // Default language
                require_once __DIR__ . '/messages/pt.php';
            }

            // Return translated content
            return new \Phalcon\Translate\Adapter\NativeArray(array(
                'content' => $messages,
            ));

        }, true);
    }
}