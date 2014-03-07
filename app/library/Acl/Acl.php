<?php
namespace Incentiv\Acl;

use Phalcon\Mvc\User\Component,
    Phalcon\Acl\Adapter\Memory as AclMemory,
    Phalcon\Acl\Role as AclRole,
    Phalcon\Acl\Resource as AclResource;

use Incentiv\Models\Perfil;

/**
 * Incentiv\Acl\Acl
 */
class Acl extends Component
{

    /**
     * O objeto ACL
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * O caminho de arquivo do arquivo de cache de ACL APP_DIR
     *
     * @var string
     */
    private $filePath = '/cache/acl/data.txt';

    /**
     * Defini os recursos que são considerados "privados". Estes controllers => actions exigem autenticação.
     *
     * @var array
     */
    private $privateResources = array(
        'usuario' => array(
            'index',
            'search',
            'edit',
            'create',
            'delete',
            'changePassword'
        ),
        'perfil' => array(
            'index',
            'search',
            'edit',
            'create',
            'delete'
        ),
        'permissao' => array(
            'index'
        )
    );

    /**
     * Descrições legível das ações usadas em {@see $privateResources}
     *
     * @var array
     */
    private $actionDescriptions = array(
        'index'             => 'Acessar',
        'search'            => 'Pesquisar',
        'create'            => 'Criar',
        'edit'              => 'Editar',
        'delete'            => 'Deletar',
        'changePassword'    => 'Alterar Senha'
    );

    /**
     * Verifica se um controller é privado ou não
     *
     * @param string $controllerName
     * @return boolean
     */
    public function isPrivate($controllerName)
    {
        return isset($this->privateResources[$controllerName]);
    }

    /**
     * Verifica se o perfil atual tem permissão para acessar um recurso
     *
     * @param string $perfil
     * @param string $controller
     * @param string $action
     * @return boolean
     */
    public function isAllowed($perfil, $controller, $action)
    {
        return $this->getAcl()->isAllowed($perfil, $controller, $action);
    }

    /**
     * Retorna lista de ACL
     *
     * @return Phalcon\Acl\Adapter\Memory
     */
    public function getAcl()
    {
        // Verifica se o ACL já está criado
        if (is_object($this->acl)) {
            return $this->acl;
        }

        // Verifica se o ACL está na APC
        if (function_exists('apc_fetch')) {
            $acl = apc_fetch('incentiv-acl');
            if (is_object($acl)) {
                $this->acl = $acl;
                return $acl;
            }
        }

        // Verifica se o ACL já foi gerado
        if (!file_exists(APP_DIR . $this->filePath)) {
            $this->acl = $this->rebuild();
            return $this->acl;
        }

        // Obtem o ACL do arquivo de dados
        $data = file_get_contents(APP_DIR . $this->filePath);
        $this->acl = unserialize($data);

        // Guarda o ACL em APC
        if (function_exists('apc_store')) {
            apc_store('incentiv-acl', $this->acl);
        }

        return $this->acl;
    }

    /**
     * Retorna as permissões atribuídas a um perfil
     *
     * @param Perfil $perfil
     * @return array
     */
    public function getPermissions(Perfil $perfil)
    {
        $permissoes = array();
        foreach ($perfil->getPermissions() as $permissao) {
            $permissoes[$permissao->resource . '.' . $permissao->action] = true;
        }
        return $permissoes;
    }

    /**
     * Retorna todos os recursos e suas ações disponíveis no aplicativo
     *
     * @return array
     */
    public function getResources()
    {
        return $this->privateResources;
    }

    /**
     * Retorna a descrição da ação de acordo com o seu nome simplificado
     *
     * @param string $action
     * @return $action
     */
    public function getActionDescription($action)
    {
        if (isset($this->actionDescriptions[$action])) {
            return $this->actionDescriptions[$action];
        } else {
            return $action;
        }
    }

    /**
     * Reconstrói a lista de acesso em um arquivo
     *
     * @return \Phalcon\Acl\Adapter\Memory
     */
    public function rebuild()
    {
        $acl = new AclMemory();

        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        // Registrar papéis
        $perfis = Perfil::find('ativo = "Y"');

        foreach ($perfis as $perfil) {
            $acl->addRole(new AclRole($perfil->nome));
        }

        foreach ($this->privateResources as $resource => $actions) {
            $acl->addResource(new AclResource($resource), $actions);
        }

        // Concede acesso à área privada para papel Usuário
        foreach ($perfis as $perfil) {

            // Concede permissões no modelo de "permissões"
            foreach ($perfil->getPermissions() as $permissao) {
                $acl->allow($perfil->nome, $permissao->resource, $permissao->action);
            }

            // Sempre conceder essas permissões
            $acl->allow($perfil->nome, 'usuario', 'alterarSenha');
        }

        if (touch(APP_DIR . $this->filePath) && is_writable(APP_DIR . $this->filePath)) {

            file_put_contents(APP_DIR . $this->filePath, serialize($acl));

            // Guarde a ACL em APC
            if (function_exists('apc_store')) {
                apc_store('incentiv-acl', $acl);
            }
        } else {
            $this->flash->error(
                'O usuário não tem permissões de escrita para criar a lista ACL em ' . APP_DIR . $this->filePath
            );
        }

        return $acl;
    }
}