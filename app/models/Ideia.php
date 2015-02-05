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
     * @var integer
     */
    public $empresaId;  

    /**
     * @var string
     */
    public $titulo;
    
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
        $this->status       = 'Y';
    }

    public function initialize()
    {
        
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario',
            'reusable' => true
        ));
        
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
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
        
        $ideia = Ideia::query()->columns(array('id',
                                                'titulo',
                                                'descricao',
                                                'resposta',
                                                'criacaoDt', 
                                                'status' ));
        
        if($objIdeia->empresaId)
        {
           $ideia->andwhere( "empresaId = {$objIdeia->empresaId}");
        }
        if($objIdeia->filter)
        {
           $ideia->andwhere( "descricao LIKE('%{$objIdeia->filter}%')");
        }

        $ideia->orderBy('id');

        return $ideia->execute();
    }
    
    public function buscarIdeiasUsuario(\stdClass $objIdeia){
        
        $ideias = $this::query()->columns(
                         array( 'id',
                                'titulo',
                                'descricao',
                                'status',
                                'resposta',
                                'criacaoDt'));
 
        $ideias->andwhere( "usuarioId = {$objIdeia->usuarioId}");
        $ideias->orderBy('id');

        return $ideias->execute();
    }
    
    public function salvarIdeia(\stdClass $objIdeia){

        try {
            
            if($objIdeia->id){
               $ideia = $this->findFirst("id = ".$objIdeia->id);
            }else{
               $ideia = $this;
            }

            $ideia->assign(array(
                'empresaId'     => $objIdeia->empresaId,
                'usuarioId'     => $objIdeia->usuarioId,
                'titulo'        => $objIdeia->titulo,
                'descricao'     => $objIdeia->descricao
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
    
    public function guardarAprovarIdeia(\stdClass $dados){

        $ideia = $this->findFirst("id = ".$dados->id);
        
        $ideia->assign(array(
            'resposta'         => $dados->resposta
        ));

        if (!$ideia->save()) {
            foreach ($ideia->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->resposta == 'N'){
                return array('status' => 'ok', 'message' => 'Ideia guardada com sucesso!!!');
            }else{
                return array('status' => 'ok', 'message' => 'Ideia aprovada com sucesso!!!');
            }  
        }
    }
}