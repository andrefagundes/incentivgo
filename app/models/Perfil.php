<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * Incentiv\Models\Perfil
 * Todos os níveis de perfil da aplicação. Usado em conjunto com listas ACL 
 */
class Perfil extends Model
{
    const ADMINISTRADOR             = 1;
    const COLABORADOR               = 2;
    const ADMINISTRADOR_INCENTIV    = 3;
    
    public static $_instance;

    /**
     * ID
     * @var integer
     */
    public $id;

    /**
     * Nome
     * @var string
     */
    public $nome;
    
    public static function build()
    {
        if( !isset( self::$_instance ) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

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