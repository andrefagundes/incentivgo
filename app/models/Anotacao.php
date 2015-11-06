<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\Anotacao
 * Modelo de registro de anotações
 */
class Anotacao extends Model
{
    
    public static $_instance;
    private $_lang = array();
   
    /**
     * @var integer
     */
    public $id;

    /**
     * @var integer
     */
    public $usuarioId;
    
    /**
     * @var string
     */
    public $descricao;
    
    /**
     * @var date
     */
    public $criacaoDt;
    
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
        $this->criacaoDt = time();
    }
    
    /**
     * Valida campos obrigatórios
     */
    public function validation()
    {
        $this->validate(new PresenceOf(array(
          'field' => 'descricao',
          'message' => $this->getDI()->getShared('lang')->_("MSG18")
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
    }
    
    public function buscarAnotacoes($usuarioId){
        $whereDate = ($this->_lang['lang'] == 'pt-BR')?'%d/%m/%Y':'%d/%m/%Y';
        
        $anotacoes = Anotacao::query()->columns(
                         array( 'Incentiv\Models\Anotacao.id',
                                'Incentiv\Models\Anotacao.usuarioId',
                                'Incentiv\Models\Anotacao.descricao',
                                'Incentiv\Models\Anotacao.criacaoDt'));
                                
        $anotacoes->andwhere("Incentiv\Models\Anotacao.usuarioId = {$usuarioId}");
        
        $anotacoes->orderBy('Incentiv\Models\Anotacao.id');

        return $anotacoes->execute();
    }

        public function salvarAnotacao(\stdClass $objAnotacao){

        $this->assign(array(
            'usuarioId'     => $objAnotacao->usuarioId,
            'descricao'       => $objAnotacao->descricao
        ));

        if (!$this->save()) {
            return array('status' => 'error', 'message'=> $this->_lang->_("MSG08", array("campo" => $this->_lang['nota'])));
        }

        return array('status' => 'ok','message'=> $this->_lang->_("MSG09", array("campo" => $this->_lang['nota'])));
        
    }
    
    public function excluirAnotacao(\stdClass $objAnotacao){

        $anotacao = $this::findFirst($objAnotacao->anotacaoId);
        if ($anotacao != false) {
            if ($anotacao->delete() == false) {
                return array('status' => 'error', 'message'=> $this->_lang->_("MSG10", array("campo" => $this->_lang['nota'])));
            } else {
                return array('status' => 'ok','message'=> $this->_lang->_("MSG11", array("campo" => $this->_lang['nota'])));
            }
        }else{
              return array('status' => 'error', 'message'=> $this->_lang->_("MSG12", array("campo" => $this->_lang['nota'])));
        }
    }
}