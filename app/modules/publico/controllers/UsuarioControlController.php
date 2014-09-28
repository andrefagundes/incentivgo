<?php
namespace Publico\Controllers;

use Incentiv\Models\EmailConfirmacao,
    Incentiv\Models\AlteraSenha;

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

            foreach ($confirmation->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                'controller'    => 'index',
                'action'        => 'index'
            ));
        }
        
        //depois de e-mail confirmado envia um novo email para criação de senha.
        $alteraSenha = new AlteraSenha();
        $alteraSenha->usuarioId = $confirmation->usuarioId;
        if ($alteraSenha->save()) {
            $this->flash->success('Sucesso! Por favor, verifique seu e-mail para criar senha');
        } else {
            foreach ($alteraSenha->getMessages() as $message) {
                $this->flash->error($message);
            }
        }

        return $this->dispatcher->forward(array(
            'controller'    => 'session',
            'action'        => 'mensagem'
        ));
    }

    public function resetPasswordAction()
    {
        $codigo = $this->dispatcher->getParam('code');

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

        $resetPassword->reset = 'Y';

        /**
         * Altera a confirmação de reset
         */
        if (!$resetPassword->save()) {

            foreach ($resetPassword->getMessages() as $message) {
                $this->flash->error($message);
            }

            return $this->dispatcher->forward(array(
                'controller'    => 'index',
                'action'        => 'index'
            ));
        }

        /**
         * Identifica o usuário na aplicação
         */
        $this->auth->authUserById($resetPassword->usuarioId);

        $this->flash->success('Por favor, redefinir sua senha');

        return $this->dispatcher->forward(array(
            'module'    => 'colaborador',
            'controller'    => 'colaborador',
            'action'        => 'alteraSenha'
        ));
    }
}