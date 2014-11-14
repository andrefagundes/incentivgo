<?php

namespace Publico\Controllers;

class ErrorsController extends ControllerBase {

    public function initialize() {
        $this->tag->setTitle('Oops!');
        $this->view->setTemplateAfter('public_session');
    }

    public function show404Action() {
        
    }

    public function show401Action() {
        
    }

    public function show500Action() {
        
    }

}