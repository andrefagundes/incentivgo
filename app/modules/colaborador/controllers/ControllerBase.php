<?php
namespace Colaborador\Controllers;

use Phalcon\Mvc\Controller,
    Phalcon\Mvc\View,
    Phalcon\Mvc\Dispatcher;

/**
 * ControllerBase
 * Este é o controlador de base para todos os controladores na aplicação
 */
class ControllerBase extends Controller
{

    /**
     * Executar antes o roteador para que possamos determinar se este é um controlador privado, 
     * e deve ser autenticado, ou um controlador público que está aberto a todos.
     * 
     * @param Dispatcher $dispatcher
     * @return boolean
     */
    public function beforeExecuteRoute(Dispatcher $dispatcher)
    {
        $controllerName = $dispatcher->getControllerName();

        // Verifique as permissões somente em controladores privados
        if ($this->acl->isPrivate($controllerName)) {

            // Obter a identidade atual
            $identity = $this->auth->getIdentity();

            // Se não há identidade disponível o usuário é redirecionado para index/index
            if (!is_array($identity)) {
                $this->response->redirect('');
                return false;
            }

            // Verifica se o usuário tem permissão para a opção atual
            $actionName = $dispatcher->getActionName();
            if (!$this->acl->isAllowed($identity['perfil'], $controllerName, $actionName)) {

                $this->flash->notice('Você não têm acesso a este módulo: ' . $controllerName . ':' . $actionName);

                if ($this->acl->isAllowed($identity['perfil'], $controllerName, 'index')) {
                    $dispatcher->forward(array(
                        'controller' => $controllerName,
                        'action' => 'index'
                    ));
                } else {
                    $dispatcher->forward(array(
                        'controller' => 'colaborador',
                        'action' => 'index'
                    ));
                }

                return false;
            }
        }
    }
    
    public function disableLayoutBefore(){
        return $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}