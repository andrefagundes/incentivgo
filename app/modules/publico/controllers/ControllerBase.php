<?php
namespace Publico\Controllers;

use Phalcon\Mvc\Controller,
    Phalcon\Mvc\View;

/**
 * ControllerBase
 * Este é o controlador de base para todos os controladores na aplicação
 */
class ControllerBase extends Controller
{
    public function disableLayoutBefore(){
        return $this->view->setRenderLevel(View::LEVEL_ACTION_VIEW);
    }
}