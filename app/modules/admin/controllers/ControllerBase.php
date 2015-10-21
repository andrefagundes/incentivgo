<?php
namespace Admin\Controllers;

use Phalcon\Mvc\Controller;

/**
 * ControllerBase
 * Este é o controlador de base para todos os controladores na aplicação
 */
class ControllerBase extends Controller{
    //multi linguas(services.php)
    public function initialize(){
        $this->view->lang = $this->getDI()->getShared('lang');
    }
}