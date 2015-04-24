<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * Incentiv\Models\EmpresaDominio
 * Todos os dominios de email registrados na aplicação e que tem autorização de cadastro.
 */
class EmpresaDominio extends Model
{
    public static $_instance;
   
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $empresaId;

    /**
     * @var string
     */
    public $dominio;
    
    /**
     * @var string
     */
    public $codigoAutorizacaoCadastro;
    
    /**
     * @var string
     */
    public $cadastroDt;

    /**
     * @var string
     */
    public $status;
    
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
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
    }
}