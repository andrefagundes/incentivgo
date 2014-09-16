<?php
namespace Colaborador\Controllers;

use Phalcon\Tag,
    Phalcon\Mvc\Model\Criteria,
    Phalcon\Paginator\Adapter\Model as Paginator;

use Colaborador\Forms\UsuarioForm,
    Colaborador\Forms\AlteraSenhaForm,
    Incentiv\Models\Usuario,
    Incentiv\Models\AlteracaoSenha;

/**
 * Colaborador\Controllers\UsuarioController
 * CRUD para gerenciar colaboradores
 */
class UsuarioController extends ControllerBase
{

    public function initialize()
    {
        $this->view->setTemplateBefore('private');
    }

    /**
     * Action padrão, mostra o formulário de busca
     */
    public function indexAction()
    {
        $this->persistent->conditions = null;
        $this->view->form = new UsuarioForm();
    }

    /**
     * Pesquisas para usuários
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Incentiv\Models\Usuario', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = array();
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $users = Usuario::find($parameters);
        if (count($users) == 0) {
            $this->flash->notice("Nenhum usuário encontrado");
            return $this->dispatcher->forward(array(
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data"  => $users,
            "limit" => 10,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Cria usuário
     */
    public function createAction()
    {
        if ($this->request->isPost()) {

            $user = Usuario::build();

            $user->assign(array(
                'nome'          => $this->request->getPost('nome', 'striptags'),
                'perfilId'      => $this->request->getPost('perfilId', 'int'),
                'matricula'     => $this->request->getPost('matricula', 'striptags'),
                'email'         => $this->request->getPost('email', 'email')
            ));

            if (!$user->save()) {
                $this->flash->error($user->getMessages());
            } else {

                $this->flash->success("O usuário foi criado com sucesso");

                Tag::resetInput();
            }
        }

        $this->view->form = new UsuarioForm(null);
    }

    /**
     * Salva o usuário a partir da ação "editar"
     */
    public function editAction($id)
    {
        $user = Usuario::findFirstById($id);
        if (!$user) {
            $this->flash->error("Usuário não encontrado");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if ($this->request->isPost()) {

            $user->assign(array(
                'nome'          => $this->request->getPost('nome', 'striptags'),
                'perfilId'      => $this->request->getPost('perfilId', 'int'),
                'email'         => $this->request->getPost('email', 'email'),
                'telefone'         => $this->request->getPost('telefone'),
                'banido'        => $this->request->getPost('banido'),
                'suspenso'      => $this->request->getPost('suspenso'),
                'ativo'         => $this->request->getPost('ativo')
            ));

            if (!$user->save()) {
                $this->flash->error($user->getMessages());
            } else {

                $this->flash->success("Usuário foi atualizado com sucesso");

                Tag::resetInput();
            }
        }

        $this->view->user = $user;

        $this->view->form = new UsuarioForm($user, array(
            'edit' => true
        ));
    }

    /**
     * Deleta o usuário
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $user = Usuario::findFirstById($id);
        if (!$user) {
            $this->flash->error("Usuário não encontrado");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if (!$user->delete()) {
            $this->flash->error($user->getMessages());
        } else {
            $this->flash->success("Usuário deletado com sucesso");
        }

        return $this->dispatcher->forward(array(
            'action' => 'index'
        ));
    }
    
    /**
     * Método para alterar senha de usuário
     */
    public function alteraSenhaAction()
    {
        $form = new AlteraSenhaForm();

        if ($this->request->isPost()) {

            if (!$form->isValid($this->request->getPost())) {

                foreach ($form->getMessages() as $message) {
                    $this->flash->error($message);
                }
            } else {

                $user = $this->auth->getUser();

                $user->senha            = $this->security->hash($this->request->getPost('senha'));
                $user->stAlterarSenha   = 'N';

                $alteracaoSenha             = new AlteracaoSenha();
                $alteracaoSenha->user       = $user;
                $alteracaoSenha->ipAddress  = $this->request->getClientAddress();
                $alteracaoSenha->userAgent  = $this->request->getUserAgent();

                if (!$alteracaoSenha->save()) {
                    $this->flash->error($alteracaoSenha->getMessages());
                } else {

                    $this->flash->success('Sua senha foi alterada com sucesso');

                    Tag::resetInput();
                }
            }
        }

        $this->view->form = $form;
    }
}