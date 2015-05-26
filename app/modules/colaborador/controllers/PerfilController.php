<?php

namespace Colaborador\Controllers;

use Phalcon\Image\Adapter\GD;
use \Incentiv\Models\Usuario,    
    Incentiv\Models\UsuarioPontuacaoCredito;

class PerfilController extends ControllerBase {

    private $_auth;

    public function initialize() {
        if (!$this->request->isAjax()) {
            $this->_auth = $this->auth->getIdentity(); 
            $this->view->count_pontuacao        = UsuarioPontuacaoCredito::build()->buscarPontuacaoUsuario($this->_auth['id']);
            $this->view->usuario_logado = $this->auth->getName();
            $this->view->avatar = $this->_auth['avatar'];
            $this->view->empresaId = $this->_auth['empresaId'];
            $this->view->id = $this->_auth['id'];
            $this->view->setTemplateBefore('private-colaborador');
        }
    }

    public function indexAction() {
        
    }

    public function perfilAction() {
        
        if ($this->request->isPost()) {
            $objUsuario = new \stdClass();
            $objUsuario->dados = $this->request->getPost('dados');

            if ($this->request->hasFiles() == true) {
                $resposta_upload = $this->upload();
                $objUsuario->dados['avatar'] = $resposta_upload;
            }

            $result = Usuario::build()->salvarUsuarioPerfil($objUsuario);

            if ($result['status'] == 'ok') {
                $this->flash->success($result['message']);
            } else {
                $this->flash->error($result['message']);
            }
        } 
        
        $usuario = Usuario::build()->findFirst(array('id = ' . $this->_auth['id'], 
                                                'columns' => 'id,
                                                empresaId,
                                                nome,
                                                email,
                                                cargo,
                                                DATE_FORMAT( nascimentoDt , "%d/%m/%Y" ) as nascimentoDt,
                                                avatar'));
        $this->view->usuario = $usuario; 
    }

    private function upload() {
        if ($this->request->hasFiles() == true) {

            $pastaEmpresa = $this->_auth['empresaId'];
            $pastaUsuario = $this->_auth['id'];

            foreach ($this->request->getUploadedFiles() as $file) {

                $file->moveTo('img/users/' . $file->getName());
                $image = new GD('img/users/' . $file->getName());

                $height = $width = null;
                $height_60 = $width_60 = $height_40 = $width_40 = null;

                if ($image->getWidth() >= $image->getHeight()) {
                    $width = ($image->getWidth() < 200 ) ? $image->getWidth() : 200;
                    $height_60 = 60;
                    $height_40 = 40;
                } else {
                    $height = ($image->getHeight() < 300) ? $image->getHeight() : 300;
                    $width_60 = 60;
                    $width_40 = 40;
                }

                $nomeArquivo = md5(uniqid(time())) . '.' . $file->getExtension();

                if (!is_dir("img/users/{$pastaEmpresa}/{$pastaUsuario}")) {
                    mkdir("img/users/{$pastaEmpresa}/{$pastaUsuario}", 0777, true);
                }

                $image->resize($width, $height)->save("img/users/{$pastaEmpresa}/{$pastaUsuario}/{$nomeArquivo}");
                $image->resize($width_60, $height_60)->save("img/users/{$pastaEmpresa}/{$pastaUsuario}/60_{$nomeArquivo}");
                $image->resize($width_40, $height_40)->save("img/users/{$pastaEmpresa}/{$pastaUsuario}/40_{$nomeArquivo}");
                unlink('img/users/' . $file->getName());

                $this->session->set('auth-identity', array(
                    'id'        => $this->_auth['id'],
                    'avatar'    => $nomeArquivo,
                    'nome'      => $this->_auth['nome'],
                    'perfilId'  => $this->_auth['perfilId'],
                    'perfil'    => $this->_auth['perfil'],
                    'empresaId' => $this->_auth['empresaId']
                ));

                return $nomeArquivo;
            }
        }
    }

}