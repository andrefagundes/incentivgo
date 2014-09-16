<?php
namespace Colaborador\Controllers;

use Incentiv\Models\Perfil,
    Incentiv\Models\Permissao;

/**
 * Exibi e defini permissões para os vários níveis de perfil.
 */
class PermissaoController extends ControllerBase
{
    /**
     * Ve as permissões para um nível de perfil, e alterá-las se tivermos um POST.
     */
    public function indexAction()
    {
        $this->view->setTemplateBefore('private');

        if ($this->request->isPost()) {

            // Validar o perfil
            $perfil = Perfil::findFirstById($this->request->getPost('perfilId'));

            if ($perfil) {

                if ($this->request->hasPost('permissions')) {

                    // Exclui as permissões atuais
                    $perfil->getPermissions()->delete();

                    // Salva as novas permissões
                    foreach ($this->request->getPost('permissions') as $permission) {

                        $parts = explode('.', $permission);

                        $permission = new Permissao();
                        $permission->perfilId = $perfil->id;
                        $permission->resource = $parts[0];
                        $permission->action = $parts[1];

                        $permission->save();
                    }

                    $this->flash->success('As permissões foram atualizadas com sucesso');
                }

                // Reconstrói o ACL
                $this->acl->rebuild();

                // Passe as permissões atuais para a view
                $this->view->permissions = $this->acl->getPermissions($perfil);
            }

            $this->view->perfil = $perfil;
        }

        // Pega todos os perfis ativos
        $this->view->perfis = Perfil::find('ativo = "Y"');
    }
}