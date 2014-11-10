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
          'message' => 'A descrição do desafio é obrigatória!!!'
        )));
        
        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
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
            'usuarioId'     => 1,
            'mensagem'      => $objAjuda->mensagem
        ));

        if (!$ajuda->save()) {
            return array('status' => 'error', 'message'=>'Não foi possível salvar a ajuda!!!');
        }

        return array('status' => 'ok','message'=>'Ajuda salva com sucesso!!!');
        
    }
}