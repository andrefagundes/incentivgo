<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\UsuarioRegra
 * Vinculo de usuario com regras cumpridas
 */
class UsuarioRegra extends Model
{
    const DELETED       = 'N';
    const NOT_DELETED   = 'Y';
    
    public static $_instance;
   
    /**
     * @var integer
     */
    public $usuarioId;

    /**
     * @var integer
     */
    public $regraId;
    
    /**
     * @var integer
     */
    public $criacaoDt;
        
    /**
     * @var integer
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

    /**
     * Antes de criar a campanha atribui uma senha
     */
    public function beforeValidationOnCreate()
    {
        //Data de vinculo
        $this->criacaoDt = time();
        
        //A conta deve ser confirmada via e-mail
        $this->ativo = 'S';
    }

    public function initialize()
    {
        
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias'     => 'usuario',
            'reusable'  => true
        ));
        
        $this->belongsTo('regraId', 'Incentiv\Models\Regra', 'id', array(
            'alias'     => 'regra',
            'reusable'  => true
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => UsuarioRegra::DELETED
            )
        ));
    }
}