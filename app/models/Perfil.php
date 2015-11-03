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
    const GERENTE                   = 4;
    
    public static $_instance;
    
    private $_lang = array();

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
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->hasMany('id', 'Incentiv\Models\Usuario', 'perfilId', array(
            'alias' => 'usuario',
            'foreignKey' => array(
                'message' => $this->_lang['MSG39']
            )
        ));

        $this->hasMany('id', 'Incentiv\Models\Permissao', 'perfilId', array(
            'alias' => 'permissao'
        ));
    }
}