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
          'message' => 'A descrição do rascunho é obrigatória!!!'
        )));
        
        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario',
            'reusable' => true
        ));
    }
    
    public function salvarAnotacao(\stdClass $objAnotacao){

        $this->assign(array(
            'usuarioId'     => $objAnotacao->usuarioId,
            'descricao'       => $objAnotacao->descricao
        ));

        if (!$this->save()) {
            return array('status' => 'error', 'message'=>'Não foi possível salvar a anotação!!!');
        }

        return array('status' => 'ok','message'=>'Anotação salva com sucesso!!!');
        
    }
    
    public function excluirAnotacao(\stdClass $objAnotacao){

        $anotacao = $this::findFirst($objAnotacao->anotacaoId);
        if ($anotacao != false) {
            if ($anotacao->delete() == false) {
                return array('status' => 'error', 'message'=>'Não foi possível excluir a anotação!!!');
            } else {
                return array('status' => 'ok','message'=>'Anotação excluída com sucesso!!!');
            }
        }else{
              return array('status' => 'error', 'message'=>'Anotação não foi encontrada!!!');
        }
    }
}