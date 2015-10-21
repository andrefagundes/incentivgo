<?php

namespace Publico\Controllers;

use Incentiv\Auth\Exception as AuthException,
    Incentiv\Models\Empresa,
    Incentiv\Models\Usuario,
    Incentiv\Models\AlteraSenha,
    Incentiv\Auth\Auth,
    Incentiv\Models\Perfil,
    Incentiv\Models\EmpresaDominio,
    Incentiv\Models\Sugestao;

use Publico\Forms\LoginForm,
    Publico\Forms\CadastroForm,
    Publico\Forms\CadastroUsuarioForm,
    Publico\Forms\EsqueceuSenhaForm,
    Publico\Forms\EnviarSugestaoForm;

/**
 * Publico\Controllers\SessionController
 * Métodos publicos para cadastro, login, esqueceu senha, logout
 */
class SessionController extends ControllerBase {
    
    private $_lang = array();

    public function initialize() {
        $this->view->logo = 'incentivgo.png';
        $this->view->nome = 'Incentiv Go';
        
        $funcoes = $this->getDI()->getShared('funcoes');
        $subdominio = $funcoes->before('.incentivgo', $this->config->application->publicUrl);

        $empresa_dominio = Empresa::findFirstBySubdominio($subdominio);
        if($empresa_dominio){
            if($empresa_dominio->logo){
                $this->view->logo = $empresa_dominio->id.'/logo/'.$empresa_dominio->logo;
                $this->view->nome = $empresa_dominio->nome;
            }
        }

        $this->_lang = parent::initialize();
        $this->view->setTemplateBefore('public_session');
    }

    public function indexAction() {
        
    }

    /**
     * Permite que uma empresa faça o pré cadastro no sistema
     */
    public function cadastroAction() {
        $form = new CadastroForm();

        if ($this->request->isPost()) {

            if (Empresa::count("email = '{$this->request->getPost('email', 'email')}'") > 0) {
                $this->flashSession->notice($this->_lang['pre_cadastro_feito']);
                $this->response->redirect('session/mensagem');
            } else {

                if ($form->isValid($this->request->getPost()) != false) {
                    $empresa = Empresa::build();

                    $filter = new \Phalcon\Filter();

                    //uso de função anônima
                    $filter->add('telefone', function($value) {
                                return preg_replace('/[^0-9]/', '', $value);
                            });

                    $empresa->assign(array(
                        'nome'      => $this->request->getPost('nome', 'striptags'),
                        'email'     => $this->request->getPost('email', 'email'),
                        'telefone'  => $filter->sanitize($this->request->getPost('telefone'), 'telefone'),
                        'preCadastroDt' => date('Y-m-d H:i:s'),
                        'ativo' => 'N'
                    ));

                    if($empresa->save()) {
                        $this->flashSession->success($this->_lang['pre_cadastro_enviado_sucesso']);
                        $this->response->redirect('session/mensagem');
                    }

                    $this->flash->error($empresa->getMessages());
                }
            }
        }
        $this->view->form = $form;
    }

    public function loginAction() {
        $form = new LoginForm();

        try {

            if (!$this->request->isPost()) {

                if ($this->auth->hasRememberMe()) {
                    return $this->auth->loginWithRememberMe();
                }
            } else {

                if ($form->isValid($this->request->getPost()) == false) {
                    foreach ($form->getMessages() as $message) {
                        $this->flash->error($message);
                    }
                } else {

                    $this->auth->check(array(
                        'email' => $this->request->getPost('email'),
                        'senha' => $this->request->getPost('password'),
                        'remember' => $this->request->getPost('remember')
                    ));
                    
                    $auth = Auth::getIdentity();
                    
                    if($auth['perfilId'] == Perfil::COLABORADOR)
                    {
                        return $this->response->redirect('colaborador/geral');
                    }elseif ($auth['perfilId'] == Perfil::ADMINISTRADOR || $auth['perfilId'] == Perfil::GERENTE) {
                        return $this->response->redirect('empresa/geral');
                    }elseif ($auth['perfilId'] == Perfil::ADMINISTRADOR_INCENTIV) {
                        return $this->response->redirect('admin');
                    }else{
                        return $this->response->redirect('/');
                    }
                    
                }
            }
        } catch (AuthException $e) {
            $this->flash->error($e->getMessage());
        }

        $this->view->form = $form;
    }

    /**
     * Mostra o formulário esqueceu senha
     */
    public function esqueceuSenhaAction() {
        $form = new EsqueceuSenhaForm();

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) == false) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {

                $user = Usuario::findFirstByEmail($this->request->getPost('email'));
                if (!$user) {
                    $this->flash->error($this->_lang['nenhuma_conta_associada']);
                } else {
                    //só faz alteração de senha se usuário estiver ativo
                    if ($user->ativo == 'Y') {
                        $alteraSenha = new AlteraSenha();
                        $alteraSenha->usuarioId = $user->id;
                        if ($alteraSenha->save()) {
                            $this->flash->success($this->_lang['email_redefinicao_senha']);
                        } else {
                            foreach ($alteraSenha->getMessages() as $message) {
                                $this->flash->error($message);
                            }
                        }
                    } else {
                        $this->flash->notice($this->_lang['usuario_inativo']);
                    }
                }
            }
        }

        $this->view->form = $form;
    }

    /**
     * Mostra o formulário enviar sugestão
     */
    public function enviarSugestaoAction() {
        $form = new EnviarSugestaoForm();

        if ($this->request->isPost()) {

            if ($form->isValid($this->request->getPost()) == false) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {

                $sugestao = Sugestao::build();

                $sugestao->assign(array(
                    'nome'   => $this->request->getPost('nome', 'striptags'),
                    'emailUsuario'  => $this->request->getPost('email', 'email'),
                    'emailEmpresa'  => $this->request->getPost('email_empresa', 'email'),
                ));

                if (!$sugestao->save()) {
                    $this->flashSession->error($sugestao->getMessages());
                }

                $this->flashSession->success($this->_lang['sugestao_enviada_sucesso']);
                $form->clear();
            }
        }

        $this->view->form = $form;
    }

    /**
     * Tela de mensagem de cadastro de usuário
     */
    public function mensagemAction() {}

    /**
     * Fechar sessão
     */
    public function logoutAction() {
        $this->auth->remove();
        return $this->response->redirect('');
    }
    
    /**
     * Método para cadastro externo de usuário. Quando autorizado pela empresa.
     */
    public function cadastroUsuarioAction(){
        $form = new CadastroUsuarioForm();

        //verifica se empresa tem autorização externa de cadastro
//        $autorizacao = EmpresaDominio::build()->findFirst("empresaId = {$this->dispatcher->getParam("empresaId")} AND codigoAutorizacaoCadastro = '{$this->dispatcher->getParam("code")}' AND status = 'Y'");
//        if(isset($autorizacao->status) && $autorizacao->status == 'Y')
//        {
            $this->view->empresaId = 1;
            $this->view->codigo    = 3;
//        }else{
//            $this->flashSession->error($this->_lang['nao_possui_autorizacao_cadastro']);
//             return $this->response->redirect('session/mensagem');
//        }
        
        if ($this->request->isPost()) {

            //verifica se cadastro de usuário já existe.
            if (Usuario::count("email = '{$this->request->getPost('email', 'email')}'") > 0) {
                $this->flashSession->notice("{$this->_lang['cadastro_ja_existe']}<a class='btn btn-syndicate squared' href='/incentiv/session/login'>{$this->_lang['entrar']}</a>");
            } else {
                
                $dominios = EmpresaDominio::build()->find(array("empresaId = {$this->request->getPost('empresaId', 'int')} AND status = 'Y'",'columns' =>'dominio'));

                foreach ($dominios as $dominio){
                    //verifica se domínio de email é autorizado para esta empresa.
                    $dominioUsuario = explode('@', $this->request->getPost('email', 'email'));
                    if($dominioUsuario[1] != $dominio->dominio){
                        $this->flashSession->error("{$this->_lang['emails_autorizados_final']} <strong>@{$dominio->dominio}</strong>");
                    }else{
                        if ($form->isValid($this->request->getPost()) != false) {
                            $usuario = Usuario::build();

                            $usuario->assign(array(
                                'empresaId' => $this->request->getPost('empresaId', 'int'),
                                'perfilId'  => Perfil::COLABORADOR,
                                'nome'      => $this->request->getPost('nome', 'striptags'),
                                'email'     => $this->request->getPost('email', 'email'),
                                'senha'     => $this->security->hash($this->request->getPost('senha'))
                            ));

                            if (!$usuario->save()) {
                                $this->flash->error($usuario->getMessages());
                            }

                            $this->flashSession->success($this->_lang['cadastro_feito_sucesso']);
                            $form->clear();
                             return $this->response->redirect('session/mensagem');
                        }
                    }
                }
            }
        }
        
        $this->view->form = $form;
    }
}