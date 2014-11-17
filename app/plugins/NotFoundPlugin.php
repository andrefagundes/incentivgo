<?php

namespace Incentiv\Plugins;

use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Dispatcher;
use Phalcon\Mvc\Dispatcher\Exception as DispatcherException;
use Phalcon\Mvc\Dispatcher as MvcDispatcher;

/**
 * NotFoundPlugin
 *
 * Handles not-found controller/actions
 */
class NotFoundPlugin extends Plugin {

    /**
     * Esta ação é executada antes de executar qualquer ação no aplicativo
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     */
    public function beforeException(Event $event, MvcDispatcher $dispatcher, DispatcherException $exception) {
        if ($exception instanceof DispatcherException) {
            switch ($exception->getCode()) {
                case Dispatcher::EXCEPTION_HANDLER_NOT_FOUND:
                case Dispatcher::EXCEPTION_ACTION_NOT_FOUND:
//                    $dispatcher->forward(array(
//                        'module' => 'publico',
//                        'controller' => 'errors',
//                        'action' => 'show404'));
//                    ));
                    $this->response->redirect('show404');
                    //return false;
            }
        }
//        $dispatcher->forward(array(
//            'module' => 'publico',
//            'controller' => 'errors',
//            'action' => 'show500'
//        ));

        $this->response->redirect('show500');
//        return false;
    }

}