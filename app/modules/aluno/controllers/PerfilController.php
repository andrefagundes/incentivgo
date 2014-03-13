<?php
namespace Aluno\Controllers;

use Phalcon\Tag,
    Phalcon\Mvc\Model\Criteria,
    Phalcon\Paginator\Adapter\Model as Paginator;

use Aluno\Forms\PerfilForm,
    Incentiv\Models\Perfil;

/**
 * Publico\Controllers\PerfilController
 * CRUD para gerenciar perfis
 */
class PerfilController extends ControllerBase
{

    /**
     * Ação padrão. Define o privado (autenticado) layout (layouts/privado.phtml)
     */
    public function initialize()
    {
        $this->view->setTemplateBefore('private');
    }

    /**
     * Ação padrão, mostra o formulário de busca
     */
    public function indexAction()
    {
        $this->persistent->conditions = null;
        $this->view->form = new PerfilForm();
    }

    /**
     * Pesquisas para perfis
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Incentiv\Models\Perfil', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = array();
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $perfis = Perfil::find($parameters);
        if (count($perfis) == 0) {

            $this->flash->notice("Nenhum perfil encontrado");

            return $this->dispatcher->forward(array(
                "action" => "index"
            ));
        }

        $paginator = new Paginator(array(
            "data"  => $perfis,
            "limit" => 10,
            "page"  => $numberPage
        ));

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Cria um novo perfil
     */
    public function createAction()
    {
        if ($this->request->isPost()) {

            $perfil = new Perfil();

            $perfil->assign(array(
                'nome'      => $this->request->getPost('nome', 'striptags'),
                'ativo'    => $this->request->getPost('ativo')
            ));

            if (!$perfil->save()) {
                $this->flash->error($perfil->getMessages());
            } else {
                $this->flash->success("Perfil criado com sucesso");
            }

            Tag::resetInput();
        }

        $this->view->form = new PerfilForm(null);
    }

    /**
     * Edita um perfil existente
     *
     * @param int $id
     */
    public function editAction($id)
    {
        $perfil = Perfil::findFirstById($id);
        if (!$perfil) {
            $this->flash->error("Perfil não encontrado");
            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if ($this->request->isPost()) {

            $perfil->assign(array(
                'nome'      => $this->request->getPost('nome', 'striptags'),
                'ativo'    => $this->request->getPost('ativo')
            ));

            if (!$perfil->save()) {
                $this->flash->error($perfil->getMessages());
            } else {
                $this->flash->success("Perfil alterado com sucesso");
            }

            Tag::resetInput();
        }

        $this->view->form = new PerfilForm($perfil, array(
            'edit' => true
        ));

        $this->view->perfil = $perfil;
    }

    /**
     * Deleta um perfil
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $perfil = Perfil::findFirstById($id);
        if (!$perfil) {

            $this->flash->error("Perfil não encontrado");

            return $this->dispatcher->forward(array(
                'action' => 'index'
            ));
        }

        if (!$perfil->delete()) {
            $this->flash->error($perfil->getMessages());
        } else {
            $this->flash->success("Perfil deletado com sucesso");
        }

        return $this->dispatcher->forward(array(
            'action' => 'index'
        ));
    }
}