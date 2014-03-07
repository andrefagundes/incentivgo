<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * Incentiv\Models\Perfil
 * Todos os níveis de perfil da aplicação. Usado em conjunto com listas ACL 
 */
class Perfil extends Model
{

    /**
     * ID
     * @var integer
     */
    public $id;

    /**
     * Name
     * @var string
     */
    public $nome;

    /**
     * Defini relações com usuários e permissões
     */
    public function initialize()
    {
        $this->hasMany('id', 'Incentiv\Models\Usuario', 'perfilId', array(
            'alias' => 'usuario',
            'foreignKey' => array(
                'message' => 'Perfil não pode ser excluído porque ele é usado em Usuário'
            )
        ));

        $this->hasMany('id', 'Incentiv\Models\Permissao', 'perfilId', array(
            'alias' => 'permissao'
        ));
    }
}