<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\Ideia
 * Todas as ideias registradas na aplicação
 */
class Ideia extends Model
{
    const DELETED       = 'N';
    const NOT_DELETED   = 'Y';
    
    private static $_instance;
   
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
     * @var char
     */
    public $status;
    
    /**
     * @var char
     */
    public $resposta;
    
    /**
     * @var integer
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
     * Antes de criar o usuário atribui uma senha
     */
    public function beforeValidationOnCreate()
    {
        $this->criacaoDt    = time();
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
                'value' => Ideia::DELETED
            )
        ));
    }
    
    public function fetchAllIdeias(\stdClass $objIdeia){
        
        $ideia = Ideia::query()->columns(array('id','descricao','criacaoDt', 'status' ));
        
        if($objIdeia->filter)
        {
           $ideia->andwhere( "descricao LIKE('%{$objIdeia->filter}%')");
        }

        $ideia->order('id');

        return $ideia->execute();
    }
    
    public function salvarIdeia(\stdClass $dados){

        try {
            
        if($dados->id){
           $ideia = $this->findFirst("id = ".$dados->id);
        }else{
           $ideia = $this;
        }

        $ideia->assign(array(
            'empresaId'     => 1,
            'usuarioId'     => $dados->usuarioId,
            'descricao'     => $dados->descricao
        ));
        
        if (!$ideia->save()) {

            foreach ($this->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            
            return array('status' => 'error', 'message'=> $message );
        }

        return array('status' => 'ok','message'=>'Ideia salva com sucesso!!!');
        
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    
    public function ativarInativarIdeia(\stdClass $dados){

        $ideia = $this->findFirst("id = ".$dados->id);
        
        $ideia->assign(array(
            'ativo'         => $dados->status
        ));

        if (!$ideia->save()) {
            foreach ($ideia->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->status == 'N'){
                return array('status' => 'ok', 'message' => 'Ideia inativada com sucesso!!!');
            }else{
                return array('status' => 'ok', 'message' => 'Ideia ativada com sucesso!!!');
            }  
        }
    }
}