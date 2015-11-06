<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\Ajuda
 * Modelo de registro de ajudas
 */
class Ajuda extends Model
{
    const DELETED               = 'N';
    const NOT_DELETED           = 'Y';
    
    public static $_instance;
    private $_lang = array();
   
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $ajudaId;
    
    /**
     * @var string
     */
    public $mensagem;
    
    /**
     * @var integer
     */
    public $usuarioId;
    
    /**
     * @var date
     */
    public $envioDt;
    
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

    /**
     * Antes de criar o desafio atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de cadastro
        $this->envioDt = date('Y-m-d H:i:s');

        // Seta status da ajuda para ativa
        $this->ativo = 'Y';
    }
    
    /**
     * Valida campos obrigatórios
     */
    public function validation()
    {
        $this->validate(new PresenceOf(array(
          'field' => 'mensagem',
          'message' => $this->getDI()->getShared('lang')->_("MSG01", array("campo" => $this->_lang['descrição']))
        )));
        
        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario',
            'reusable' => true
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => Ajuda::DELETED
            )
        ));
    }
    
    public function pedirAjuda(\stdClass $objAjuda){

        if($objAjuda->id){
           $ajuda = $this->find("id = ".$objAjuda->id);
        }else{
           $ajuda = $this;
        }

        $ajuda->assign(array(
            'usuarioId'     => $objAjuda->usuarioId,
            'mensagem'      => $objAjuda->mensagem
        ));

        if (!$ajuda->save()) {
            return array('status' => 'error', 'message'=> $this->_lang->_("MSG08", array("campo" => $this->_lang['ajuda'])));
        }

        return array('status' => 'ok','message'=>$this->_lang->_("MSG09", array("campo" => $this->_lang['ajuda'])));
        
    }
    public function ajudar(\stdClass $objAjuda){

        $this->assign(array(
            'usuarioId'     => $objAjuda->usuarioId,
            'ajudaId'       => $objAjuda->ajudaId,
            'mensagem'      => $objAjuda->mensagem
        ));

        if (!$this->save()) {
            return array('status' => 'error', 'message'=> $this->_lang['MSG16']);
        }

        return array('status' => 'ok','message'=> $this->_lang['MSG17']);
        
    }
    public function excluirAjuda(\stdClass $objAjuda){

        $ajuda = $this::findFirst($objAjuda->ajudaId);
        if ($ajuda != false) {
            if ($ajuda->delete() == false) {
                return array('status' => 'error', 'message'=> $this->_lang->_("MSG10", array("campo" => $this->_lang['ajuda'])));
            } else {
                return array('status' => 'ok','message'=> $this->_lang->_("MSG11", array("campo" => $this->_lang['ajuda'])));
            }
        }else{
              return array('status' => 'error', 'message'=> $this->_lang->_("MSG12", array("campo" => $this->_lang['ajuda'])));
        }
    }
}