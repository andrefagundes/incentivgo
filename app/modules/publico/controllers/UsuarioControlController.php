<?php
namespace Publico\Controllers;

use Incentiv\Models\EmailConfirmacao,
    Incentiv\Models\AlteraSenha,
    Incentiv\Models\AlteracaoSenha,
    Incentiv\Models\Usuario;
use Publico\Forms\AlteraSenhaForm;

/**
 * UsuarioControlController
 * Fornece ajuda aos usuários para confirmar suas senhas ou redefini-las
 */
class UsuarioControlController extends ControllerBase
{

    public function initialize()
    {
        if ($this->session->has('auth-identity')) {
            $this->view->setTemplateBefore('private');
        }
    }

    public function indexAction(){}

    /**
     * Confirma o e-mail, se o usuário pode alterar a senha, em seguida, altera
     */
    public function confirmEmailAction()
    {
        $codigo = $this->dispatcher->getParam('code');

        $confirmation = EmailConfirmacao::findFirstByCodigo( $codigo );

        if (!$confirmation) {
            return $this->dispatcher->forward(array(
                'controller'    => 'index',
                'action'        => 'index'
            ));
        }

        if ($confirmation->confirmado != 'N') {
            return $this->dispatcher->forward(array(
                'controller'    => 'session',
                'action'        => 'login'
            ));
        }

        $confirmation->confirmado      = 'Y';

        /**
         * Altera a confirmação para "confirmar"
         */
        if (!$confirmation->save()) {
            $this->flashSession->error('Não foi possível fazer a confirmação.');
            return $this->response->redirect('session/mensagem');
        }
      
        $usuario = Usuario::findFirstById($confirmation->usuarioId);
        $usuario->ativo = 'Y';
        
        if (!$usuario->save()) {
            $this->flashSession->error('Não foi possível ativar usuário.');
            return $this->response->redirect('session/mensagem');
        }

        //verifica se depois que o usuário confirmar o email ele tem que alterar senha
        if($usuario->stAlterarSenha ==  'Y'){
            $alteraSenha = new AlteraSenha();
            $alteraSenha->usuarioId = $confirmation->usuarioId;
            if ($alteraSenha->save()) {
                $this->flashSession->success('Sucesso! Por favor, verifique seu e-mail para criar senha.');
            } else {
                foreach ($alteraSenha->getMessages() as $message) {
                    $this->flashSession->error($message);
                }
            }
            return $this->response->redirect('session/mensagem');
        }else{
            $this->flashSession->success('E-mail confirmado e usuário ativado com sucesso.');
            return $this->response->redirect('session/login');
        }
    }

    public function resetPasswordAction()
    {
        $codigo = $this->dispatcher->getParam('code');
        
        $form = new AlteraSenhaForm();

        if ($this->request->isPost()) {
            
            if (!$form->isValid($this->request->getPost())) {
                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {
                $resetPassword  = AlteraSenha::findFirstByCodigo($codigo);
                $user           = Usuario::findFirstById($resetPassword->usuarioId);

                if($user){
                $user->senha            = $this->security->hash($this->request->getPost('senha'));
                $user->stAlterarSenha   = 'N';

                $alteracaoSenha             = new AlteracaoSenha();
                $alteracaoSenha->user       = $user;
                $alteracaoSenha->ipAddress  = $this->request->getClientAddress();
                $alteracaoSenha->userAgent  = $this->request->getUserAgent();

                if (!$alteracaoSenha->save()) {
                    $this->flashSession->error('Erro ao alterar senha.');
                } else {
                    //se alterar a senha seta o pedido de alteração para Y
                    $resetPassword->reset   = 'Y';

                    if (!$resetPassword->save()) {

                        foreach ($resetPassword->getMessages() as $message) {
                            $this->flashSession->error($message);
                        }

                        return $this->dispatcher->forward(array(
                            'controller'    => 'index',
                            'action'        => 'index'
                        ));
                    }

                    $this->flashSession->success('Sua senha foi alterada com sucesso.');
                    $form->clear();
                    return $this->response->redirect('session/login');
                }
                }else{
                    $this->flashSession->error('Usuário não encontrado ou inativo.');
                }
            }
        }else{
            //valida se existe pedido de alteração de senha.
            $resetPassword = AlteraSenha::findFirstByCodigo($codigo);

            if (!$resetPassword) {
                return $this->dispatcher->forward(array(
                    'controller'    => 'index',
                    'action'        => 'index'
                ));
            }

            if ($resetPassword->reset != 'N') {
                return $this->dispatcher->forward(array(
                    'controller'    => 'session',
                    'action'        => 'login'
                ));
            }
        }
        
        $this->view->codigo = $codigo;
        $this->view->form = $form;
    }
}