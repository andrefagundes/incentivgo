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

    public function initialize() {
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
                $this->flashSession->notice('Pré-cadastro já foi feito, entraremos em contato!!!');
                $this->response->redirect('session/mensagem');
            } else {

                if ($form->isValid($this->request->getPost()) != false) {
                    $empresa = Empresa::build();

                    $filter = new \Phalcon\Filter();

                    //Using an anonymous function
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
                        $this->flashSession->success('Pré-cadastro enviado com sucesso, entraremos em contato!!!');
                        $this->response->redirect('session/mensagem');
                    }

                    $this->flash->error($empresa->getMessages());
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Inicia uma sessão no backend de administração
     */
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
                    $this->flash->error('Não há nenhuma conta associada a este e-mail');
                } else {
                    //só faz alteração de senha se usuário estiver ativo
                    if ($user->ativo == 'Y') {
                        $alteraSenha = new AlteraSenha();
                        $alteraSenha->usuarioId = $user->id;
                        if ($alteraSenha->save()) {
                            $this->flash->success('Sucesso! Por favor, verifique seu e-mail para redefinição de senha');
                        } else {
                            foreach ($alteraSenha->getMessages() as $message) {
                                $this->flash->error($message);
                            }
                        }
                    } else {
                        $this->flash->notice('Usuário inativo');
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

                $this->flashSession->success('Sugestão enviada com sucesso!!!');
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
        $autorizacao = EmpresaDominio::build()->findFirst("empresaId = {$this->dispatcher->getParam("empresaId")} AND codigoAutorizacaoCadastro = '{$this->dispatcher->getParam("code")}' AND status = 'Y'");
        if(isset($autorizacao->status) && $autorizacao->status == 'Y')
        {
            $this->view->empresaId = $autorizacao->empresaId;
            $this->view->codigo    = $autorizacao->codigoAutorizacaoCadastro;
        }else{
            $this->flashSession->error("Não possui autorização de cadastro externo de usuário.");
             return $this->response->redirect('session/mensagem');
        }
        
        if ($this->request->isPost()) {

            //verifica se cadastro de usuário já existe.
            if (Usuario::count("email = '{$this->request->getPost('email', 'email')}'") > 0) {
                $this->flashSession->notice('O cadastro com este e-mail já existe !!!<a class="btn btn-syndicate squared" href="/incentiv/session/login">Entrar</a>');
            } else {
                
                $dominios = EmpresaDominio::build()->find(array("empresaId = {$this->request->getPost('empresaId', 'int')} AND status = 'Y'",'columns' =>'dominio'));

                foreach ($dominios as $dominio){
                    //verifica se domínio de email é autorizado para esta empresa.
                    $dominioUsuario = explode('@', $this->request->getPost('email', 'email'));
                    if($dominioUsuario[1] != $dominio->dominio){
                        $this->flashSession->error("E-mails autorizados somente com o final: <strong>@{$dominio->dominio}</strong>");
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

                            $this->flashSession->success('Cadastro feito com sucesso, você receberá um email para confirmar seu cadastro!!!');
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