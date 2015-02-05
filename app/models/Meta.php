<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\Meta
 * Todas as metas registradas na aplicação
 */
class Meta extends Model
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
    public $descricao;
   
    /**
     * @var integer
     */
    public $tipo;
    
    /**
     * @var char
     */
    public $ativo;
    
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
        
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => Meta::DELETED
            )
        ));
    }
    
    public function fetchAllMetas(\stdClass $objMeta){
        
        $meta = Meta::query()->columns(array('id','descricao','criacaoDt' ));
        
        if($objMeta->filter)
        {
           $meta->andwhere( "descricao LIKE('%{$objMeta->filter}%')");
        }

        $meta->orderBy('id');

        return $meta->execute();
    }
    
    public function buscarMetasUsuario(\stdClass $objMeta){
        
        $metas = $this::query()->columns(
                         array( 'id',
                                'descricao',
                                'criacaoDt'));
 
        $metas->andwhere( "usuarioId = {$objMeta->usuarioId}");
        $metas->orderBy('id');

        return $metas->execute();
    }
    
    public function salvarMeta(\stdClass $objMeta){

        try {
            
            if($objMeta->id){
               $meta = $this->findFirst("id = ".$objMeta->id);
            }else{
               $meta = $this;
            }

            $meta->assign(array(
                'empresaId'     => $objMeta->empresaId,
                'usuarioId'     => $objMeta->usuarioId,
                'descricao'     => $objMeta->descricao
            ));

            if (!$meta->save()) {

                foreach ($this->getMessages() as $mensagem) {
                  $message =  $mensagem;
                  break;
                }

                return array('status' => 'error', 'message'=> $message );
            }

            return array('status' => 'ok','message'=>'Meta salva com sucesso!!!');
        
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    
    public function ativarInativarMeta(\stdClass $dados){

        $meta = $this->findFirst("id = ".$dados->id);
        
        $meta->assign(array(
            'ativo'         => $dados->status
        ));

        if (!$meta->save()) {
            foreach ($meta->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->status == 'N'){
                return array('status' => 'ok', 'message' => 'Meta inativada com sucesso!!!');
            }else{
                return array('status' => 'ok', 'message' => 'Meta ativada com sucesso!!!');
            }  
        }
    }
}