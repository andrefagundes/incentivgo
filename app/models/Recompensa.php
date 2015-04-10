<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\Recompensa
 * Modelo de registro de recompensas
 */
class Recompensa extends Model
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
    public $empresaId;
    
    /**
     * @var string
     */
    public $recompensa;
    
    /**
     * @var string
     */
    public $observacao;
    
    /**
     * @var integer
     */
    public $pontuacao;
    
    /**
     * @var integer
     */
    public $criacaoDt;
   
    /**
     * @var integer
     */
    public $modificacaoDt;
        
    /**
     * @var integer
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
     * Antes de criar a recompensa atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de confirmação
        $this->criacaoDt = time();

        // Seta estado para ativa
        $this->ativo = Recompensa::NOT_DELETED;
    }

    /**
     * Define a data e hora antes de atualizar a recompensa
     */
    public function beforeValidationOnUpdate()
    {
        // Data de alteração
        $this->modificacaoDt = time();
    }

    public function initialize()
    {
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPontuacao', 'recompensaId', array(
            'alias' => 'pontuacao',
            'foreignKey' => array(
                'message' => 'A recompensa não pode ser excluída porque ela possui pontuação lançada.'
            )
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => Recompensa::DELETED
            )
        ));
    }
    
    public function fetchAllRecompensas(\stdClass $objDesafio) {
        
        $recompensas = Recompensa::query()->columns(
                         array( 'id',
                                'recompensa',
                                'pontuacao', 
                                'observacao',
                                'criacaoDt',
                                'status' => 'ativo'));
        
        if($objDesafio->filter)
        {
           $recompensas->andwhere( "recompensa LIKE('%{$objDesafio->filter}%')");
        }
        if($objDesafio->ativo && $objDesafio->ativo != 'T' )
        {
            $recompensas->andwhere("ativo = '{$objDesafio->ativo}'");
        }
        
        $recompensas->orderBy('recompensa');

        return $recompensas->execute();
    }
    
    public function salvarRecompensa($dados){
       
        if($dados['id']){
           $recompensa = $this->findFirst("id = ".$dados['id']);
        }else{
           $recompensa = $this;
        }
        
        $recompensa->assign(array(
            'empresaId'     => $dados['empresaId'],
            'recompensa'    => $dados['recompensa'],
            'pontuacao'     => $dados['pontuacao'],
            'observacao'    => $dados['observacao']
        ));

        if (!$recompensa->save()) {
            foreach ($recompensa->getMessages() as $mensagem) {
                die($mensagem);
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => 'Não foi possível salvar a recompensa!!!');
        } else {
            return array('status' => 'ok', 'message' => 'Recompensa salva com sucesso!!!');
        }
    }
    
    public function ativarInativarRecompensa(\stdClass $dados){

        $recompensa = $this->findFirst("id = ".$dados->id);
        
        $recompensa->assign(array(
            'ativo'         => $dados->status
        ));

        if (!$recompensa->save()) {
            foreach ($recompensa->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->status == 'N'){
                return array('status' => 'ok', 'message' => 'Recompensa inativada com sucesso!!!');
            }else{
                return array('status' => 'ok', 'message' => 'Recompensa ativada com sucesso!!!');
            }  
        }
    }
}