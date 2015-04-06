<?php

namespace Incentiv\Plugins;

use Phalcon\Acl;
use Phalcon\Acl\Role;
use Phalcon\Acl\Resource;
use Phalcon\Events\Event;
use Phalcon\Mvc\User\Plugin;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Acl\Adapter\Memory as AclList;

/**
 * SecurityPlugin
 *
 * Este é o plugin de segurança que controla o que os usuários só têm acesso aos módulos que estão designados
 */
class SecurityPlugin extends Plugin {
    
    /**
     * Retorna uma lista de controle de acesso existentes ou novos
     *
     * @returns AclList
     */
    public function getAcl() {
        //throw new \Exception("something");
//        if (!isset($this->persistent->acl)) {
            //$acl = new \Phalcon\Acl\Adapter\Memory();
            $acl = new AclList();
            $acl->setDefaultAction(Acl::DENY);

            //Register roles
            $roles = array(
                'administrador' => new Role('administrador'),
                'colaborador'   => new Role('colaborador'),
                'admin'         => new Role('admin'),
                'publico'       => new Role('publico')
            );

            foreach ($roles as $role) {
                $acl->addRole($role);
            }
            
            $AllResources = array(
                'administradorResources' => array(
                    'empresa' => array( 'index' ,'empresa'),
                    'empresa_geral' => array( 'index' ,'empresa'),
                    'empresa_colaborador' => array( 'index','colaborador','pesquisarColaborador','modalColaborador',
                    'salvarColaborador','ativarInativarColaborador'),
                    'empresa_ideia' => array( 'index','ideia','pesquisarIdeia','guardarAprovarIdeia','modalIdeia','mapearPontuacao'),
                    'empresa_desafio' => array('index','desafio','pesquisarDesafio','modalDesafio','pesquisarColaboradoresDesafio',
                            'salvarDesafio', 'ativarInativarDesafio','modalAnalisarDesafio','analisarDesafio'),
                    'empresa_noticia' => array('index','noticia','pesquisarNoticia','modalNoticia',
                            'salvarNoticia', 'ativarInativarNoticia'),
                    'empresa_pontuacao' => array('index','pontuacao', 'pesquisarPontuacao','modalPontuacao',
                            'salvarPontuacao','ativarInativarPontuacao'),
                    'empresa_regra' => array('index','regra','pesquisarRegra','modalRegra','salvarRegra','ativarInativarRegra'),
                    'empresa_perfil' => array('index','perfil'),
                    'empresa_mensagem' => array('index','mensagem','pesquisarMensagem','novaMensagem',
                                                'salvarMensagem','lerMensagem','excluirMensagem','verificarMensagens','responderMensagem')
                ),
                'colaboradorResources'  => array(
                    'colaborador' => array('index','modalAnotacoes','salvarAnotacao','excluirAnotacao'),
                    'ajuda' => array('index','modalAjudas','pedirAjuda','modalAjudar'),
                    'chat' => array('index','chat'),
                    'ideia' => array('index','ideia','modalIdeias','salvarIdeia'),
                    'perfil' => array('index','perfil'),
                    'noticia' => array('index','noticia','modalNoticias','modalLerNoticia'),
                    'desafio' => array('index','modalDesafios','responderDesafio','desafioCumprido')
                ),
                'adminResources' => array(
                    'usuario' => array('index','search','edit','create','delete','alteraSenha'),
                    'perfil' => array('index','search','edit','create','delete'),
                    'permissao' => array('index')
                )
            );
 
            foreach ($roles as $role) {
                if($role != 'publico'){
                    foreach ($AllResources[$role->getName().'Resources'] as $resource => $actions) {
                        $acl->addResource(new Resource($resource), $actions);
                        foreach ($actions as $action) {
                            $acl->allow($role->getName(),$resource,$action);
                        }
                    }
                }
            }
            
            // Recursos área pública
            $publicResources = array(
                'index'             => array('index','contato','route404'),
                'errors'            => array('show401','show404', 'show500'),
                'session'           => array('index', 'cadastro', 'login', 'esqueceuSenha','enviarSugestao','mensagem','logout'),
                'usuario_control'   => array('index', 'confirmEmail','resetPassword')
            );
            foreach ($publicResources as $resource => $actions) {
                $acl->addResource(new Resource($resource), $actions);
            }
            //Conceda acesso a áreas públicas para todos os usuários
            foreach ($roles as $role) {
                foreach ($publicResources as $resource => $actions) {
                    $acl->allow($role->getName(), $resource, '*');
                }
            }
            
            //The acl is stored in session, APC would be useful here too
            $this->persistent->acl = $acl;
//        }

        return $this->persistent->acl;
    }

    /**
     * This action is executed before execute any action in the application
     *
     * @param Event $event
     * @param Dispatcher $dispatcher
     */
    public function beforeExecuteRoute(Event $event, Dispatcher $dispatcher) {

        $auth = $this->auth->getIdentity();

        if (!$auth) {
            $role = 'publico';
        } else {
            $role = $auth['perfil'];
        }

        $controller = $dispatcher->getControllerName();
        $action     = $dispatcher->getActionName();

        $acl        = $this->getAcl();

        $allowed    = $acl->isAllowed($role, $controller, $action);

        if ($allowed != Acl::ALLOW || empty($allowed)) {
            $this->response->redirect('show401');
            return false;
        }
    }

}