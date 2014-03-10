<?php

namespace Incentiv\Controllers;

use Incentiv\Models\Usuario,
    Incentiv\Forms\LoginForm,
    Incentiv\Forms\CadastroForm,
    Incentiv\Forms\EsqueceuSenhaForm,
    Incentiv\Auth\Exception as AuthException,
    Incentiv\Models\AlteraSenha;

class SessionController extends ControllerBase
{
    public function initialize()
    {
        $this->view->setTemplateBefore('public_session');
    }
    
    public function indexAction(){}
    
    /**
     * Permite que um usuário se cadastre no sistema
     */
    public function cadastroAction()
    {
        $form = new CadastroForm();

        if ($this->request->isPost()) {
            
            if ($form->isValid($this->request->getPost()) != false) {
                $user = Usuario::build();

                $user->assign(array(
                    'nome'          => $this->request->getPost('nome', 'striptags'),
                    'email'         => $this->request->getPost('email', 'email'),
                    'instituicaoId' => (int) $this->request->getPost('instituicaoId','int'),
                    'matricula'     => $this->request->getPost('matricula', 'striptags'),
                    'perfilId'      => 3
                ));
               
                if ($user->save()) {
                    return $this->dispatcher->forward(array(
                        'controller' => 'session',
                        'action'     => 'mensagem'
                    ));
                }
 
                $this->flash->error($user->getMessages());
            }
        }
        
        $this->view->form = $form;
    }

    /**
     * Inicia uma sessão no backend de administração
     */
    public function loginAction()
    {
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
                        'senha' => $this->request->getPost('senha'),
                        'remember' => $this->request->getPost('remember')
                    ));

                    return $this->response->redirect('usuario');
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
    public function esqueceuSenhaAction()
    {
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
                    if($user->ativo == 'Y')
                    {
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
     * Tela de mensagem de cadastro de usuário
     */
    public function mensagemAction(){}

     /**
     * Fechar sessão
     */
    public function logoutAction()
    {
        $this->auth->remove();

        return $this->response->redirect('index');
    }
}