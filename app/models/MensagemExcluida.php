<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * MensagemExcluida
 * Este modelo registra as mensagens excluidas
 */
class MensagemExcluida extends Model
{
     public static $_instance;
     
     private $_lang = array();

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
    public $usuarioId;
    
    /**
     * @var char
     */
    public $exclusaoDt;
    
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
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->belongsTo('mensagemId', 'Incentiv\Models\Mensagem', 'id', array(
            'alias' => 'mensagemId'
        ));
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuarioId'
        ));
    }
    
    /**
     * Antes de criar a mensagem atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        $this->exclusaoDt = date('Y-m-d H:i:s');
    }
    
    
    public function excluirMensagem(\stdClass $objMensagem){
        $this->assign(array(
            'mensagemId'        => $objMensagem->mensagemId,
            'usuarioId'         => $objMensagem->usuarioId,
        ));
        
        if (!$this->save()) {
              foreach ($this->getMessages() as $mensagem) {
              die($mensagem);
              break;
            }
            return array('status' => 'error', 'message'=> $this->_lang['MSG56']);
        }

        return array('status' => 'ok','message'=> $this->_lang->_("MSG11", array("campo" => $this->_lang['mensagem'])));
    }
}
