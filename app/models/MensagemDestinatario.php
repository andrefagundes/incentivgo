<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * MensagemDestinatario
 * Este modelo registra os usuários que receberam a mensagem
 */
class MensagemDestinatario extends Model
{
     public static $_instance;

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $mensagemId;
    
    /**
     *
     * @var integer
     */
    public $destinatarioId;
    
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
        $this->belongsTo('mensagemId', 'Incentiv\Models\Mensagem', 'id', array(
            'alias' => 'mensagem'
        ));
        $this->belongsTo('destinatarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuarios'
        ));
    }

}
