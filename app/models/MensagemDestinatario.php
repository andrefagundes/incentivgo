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
    
    /**
     * @var char
     */
    public $lida;
    
    /**
     * @var char
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
        $this->belongsTo('mensagemId', 'Incentiv\Models\Mensagem', 'id', array(
            'alias' => 'mensagem'
        ));
        $this->belongsTo('destinatarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario'
        ));
    }
    
    /**
     * Antes de criar a mensagem atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Seta status da mensagem para ativa
        $this->ativo = 'Y';
        
        // Seta status da mensagem para não lida
        $this->lida = 'N';
    }
    
    public function setarMensagemLida(\stdClass $objDadosMensagemLida ){

        $objMensagemDestinatario = $this->findFirst("destinatarioId = {$objDadosMensagemLida->destinatarioId} AND mensagemId = {$objDadosMensagemLida->mensagemId}");

        if($objMensagemDestinatario){
            $objMensagemDestinatario->lida = 'Y';
            $objMensagemDestinatario->save();
        }
    }
    
    public function quantMensagensRecebidas($destinatarioId){
        $mensagensRecebidas = MensagemDestinatario::query()->columns(array('quant'=>'count(*)'));

        $mensagensRecebidas->leftjoin('Incentiv\Models\MensagemExcluida', "Incentiv\Models\MensagemDestinatario.destinatarioId = mensagemExcluida.usuarioId AND Incentiv\Models\MensagemDestinatario.mensagemId = mensagemExcluida.mensagemId", 'mensagemExcluida');
        $mensagensRecebidas->andwhere( "Incentiv\Models\MensagemDestinatario.destinatarioId = {$destinatarioId}");
        $mensagensRecebidas->andwhere( "mensagemExcluida.id IS NULL");
        $count = $mensagensRecebidas->execute();

        return $count->quant;
    }

}
