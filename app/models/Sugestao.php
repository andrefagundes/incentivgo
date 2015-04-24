<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;
/**
 * Incentiv\Models\Sugestao
 * Modelo de mensagens
 */
class Sugestao extends Model
{
    
    public static $_instance;
   
    /**
     * @var integer
     */
    public $id;

    /**
     * @var char
     */
    public $nome;
    
    /**
     * @var char
     */
    public $emailEmpresa;
    
    /**
     * @var char
     */
    public $emailUsuario;
    
    /**
     * @var date
     */
    public $cadastroDt;
    
    /**
     * @var char
     */
    public $enviadoEmail;
    
    public static function build()
    {
        if( !isset( self::$_instance ) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Antes de criar a sugestao atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de cadastro
        $this->cadastroDt = date('Y-m-d H:i:s');

        // Seta status da mensagem para ativa
        $this->enviadoEmail = 'N';
    }
}