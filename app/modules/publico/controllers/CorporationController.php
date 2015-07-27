<?php

namespace Publico\Controllers;

use Incentiv\Auth\Exception as AuthException,
    Incentiv\Models\Empresa;
use Phalcon\Http\Response;
use Publico\Forms\CorporationForm;

/**
 * Publico\Controllers\CorporationController
 * Método publico de pré-login das empresas.
 */
class CorporationController extends ControllerBase {

    public function initialize() {
        $this->view->setTemplateBefore('public_session');
    }

    public function indexAction() {
        
    }
    
    public function corporationAction(){
        $form = new CorporationForm();

        try {
            if($this->request->isPost()){
                if ($form->isValid($this->request->getPost()) != false) {
                    $funcoes = $this->getDI()->getShared('funcoes');
                   $empresa =  Empresa::build()->findFirst(array("id = {$this->request->getPost('empresa')}",'columns'=>'subdominio'));
                   $dominio = $funcoes->after('.', $this->config->application->publicUrl); 
                   
                   //verificar como resolver este problema(quando não se tem o subdominio
                   if($dominio == 'com.br')
                   {
                       $dominio = $funcoes->before('.com.br', $this->config->application->publicUrl);   
                   }else{
                       $dominio = $funcoes->before('.com.br', $dominio);
                   }

                   $this->response->redirect("http://{$empresa->subdominio}.{$dominio}.com.br/session/login", true);
                }                
            }  
        } catch (AuthException $e) {
            $this->flash->error($e->getMessage());
        }

        $this->view->form = $form;
    }
    
    public function pesquisarEmpresaAction() {

        $this->disableLayoutBefore();

        $objEmpresa = new \stdClass();
        $objEmpresa->filter         = $this->request->get("filter");

        $resultEmpresa = Empresa::build()->findEmpresa($objEmpresa);

        $this->response = new Response();
        $this->response->setJsonContent($resultEmpresa,'utf8');
        $this->response->send();
    }
}