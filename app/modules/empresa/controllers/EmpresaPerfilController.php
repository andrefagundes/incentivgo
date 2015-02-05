<?php

namespace Empresa\Controllers;

use Phalcon\Image\Adapter\GD;
use \Incentiv\Models\Usuario;

class EmpresaPerfilController extends ControllerBase {

    public function initialize() {
        if (!$this->request->isAjax()) {
            $auth = $this->auth->getIdentity();

            $this->view->usuario_logado = $this->auth->getName();
            $this->view->avatar = $auth['avatar'];
            $this->view->empresaId = $auth['empresaId'];
            $this->view->id = $auth['id'];
            $this->view->setTemplateBefore('private-empresa');
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
                $this->flashSession->success($result['message']);
            } else {
                $this->flashSession->error($result['message']);
            }
        } 
        
        $auth    = $this->auth->getIdentity();
        $usuario = Usuario::build()->findFirst(array('id = ' . $auth['id'], 
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

            $auth = $this->auth->getIdentity();

            $pastaEmpresa = $auth['empresaId'];
            $pastaUsuario = $auth['id'];

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
                $image->resize($width_60, $height_60)->crop(60, 60)->save("img/users/{$pastaEmpresa}/{$pastaUsuario}/60_{$nomeArquivo}");
                $image->resize($width_40, $height_40)->crop(40, 40)->save("img/users/{$pastaEmpresa}/{$pastaUsuario}/40_{$nomeArquivo}");
                unlink('img/users/' . $file->getName());

                $this->session->set('auth-identity', array(
                    'id'        => $auth['id'],
                    'avatar'    => $nomeArquivo,
                    'nome'      => $auth['nome'],
                    'perfilId'  => $auth['perfilId'],
                    'perfil'    => $auth['perfil'],
                    'empresaId' => $auth['empresaId']
                ));

                return $nomeArquivo;
            }
        }
    }

}