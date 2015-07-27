<?php

namespace Empresa\Controllers;

use Phalcon\Image\Adapter\GD;
use \Incentiv\Models\Empresa,
    Incentiv\Models\Perfil;

class EmpresaPersonalizarController extends ControllerBase {

    private $_auth;
    
    public function initialize() {
        $this->_auth = $this->auth->getIdentity();
        $this->view->perfilAdmin     = Perfil::ADMINISTRADOR;
        $this->view->perfilId        = $this->_auth['perfilId'];
        if (!$this->request->isAjax()) {
            $this->view->usuario_logado = $this->auth->getName();
            $this->view->avatar         = $this->_auth['avatar'];
            $this->view->empresaId      = $this->_auth['empresaId'];
            $this->view->id             = $this->_auth['id'];
            $this->view->setTemplateBefore('private-empresa');
        }
    }

    public function indexAction() {
        
    }

    public function perfilAction() {
        
        if ($this->request->isPost()) {
            $objEmpresa = new \stdClass();
            $objEmpresa->dados = $this->request->getPost('dados');

            if ($this->request->hasFiles(true)) {
                $resposta_upload = $this->upload();
                if($resposta_upload)
                    $objEmpresa->dados['logo'] = $resposta_upload;
            }

            $result = Empresa::build()->salvarEmpresaPerfil($objEmpresa);

            if ($result['status'] == 'ok') {
                $this->flashSession->success($result['message']);
            } else {
                $this->flashSession->error($result['message']);
            }
        } 

        $empresa = Empresa::build()->findFirst(array('id = ' . $this->_auth['empresaId'], 
                                                'columns' => 'id,
                                                nome,
                                                subdominio,
                                                logo'));
        

        $this->view->empresa = $empresa; 
    }

    private function upload() {

        if ($this->request->hasFiles(true)) {

            $pastaEmpresa = $this->_auth['empresaId'];

            foreach ($this->request->getUploadedFiles() as $file) {

                $file->moveTo('img/users/logo' . $file->getName());
                $image = new GD('img/users/logo' . $file->getName());
                
                if (!is_dir("img/users/{$pastaEmpresa}/logo")) {
                    mkdir("img/users/{$pastaEmpresa}/logo", 0777, true);
                }
                
                if($image->getWidth() > 0 ){
                    $nomeArquivo = md5('corporation_logo_'.$pastaEmpresa) . '.' . $file->getExtension();
                    $image->save("img/users/{$pastaEmpresa}/logo/{$nomeArquivo}");
                }
                
                unlink('img/users/logo' . $file->getName());
                return $nomeArquivo;
            }
        }
    }

}