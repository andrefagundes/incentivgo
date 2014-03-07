<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * Incentiv\Models\Instituicao
 * Todos os usuários registrados na aplicação
 */
class Instituicao extends Model
{
    public static $_instance;
   
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $nome;

    /**
     * @var string
     */
    public $email;

    /**
     * @var string
     */
    public $ativo;
    
    public static function build()
    {
        if( !isset( self::$_instance ) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function initialize()
    {
        $this->hasMany('id', 'Incentiv\Models\Usuario', 'instituicaoId', array(
            'alias'         => 'user',
            'foreignKey'    => array(
               'message'    => 'A  instituição não pode ser excluída porque ela tem usuarios no sistema'
            )
        ));
    }
}