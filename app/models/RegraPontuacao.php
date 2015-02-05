<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\RegraPontuacao
 * Modelo de registro de regras de pontuacões usadas(debitadas)
 */
class RegraPontuacao extends Model
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
    public $regra;
    
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
     * Antes de criar a regra atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de confirmação
        $this->criacaoDt = time();

        // Seta estado para ativa
        $this->ativo = 'S';
    }

    /**
     * Define a data e hora antes de atualizar a regra
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
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPontuacao', 'regraId', array(
            'alias' => 'pontuacao',
            'foreignKey' => array(
                'message' => 'A regra não pode ser excluída porque ela possui pontuação lançada.'
            )
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => RegraPontuacao::DELETED
            )
        ));
    }
    
    public function fetchAllPontuacoes(\stdClass $objDesafio) {
        
        $regras = RegraPontuacao::query()->columns(
                         array( 'id',
                                'regra',
                                'pontuacao', 
                                'observacao',
                                'criacaoDt',
                                'status' => 'ativo'));
        
        if($objDesafio->filter)
        {
           $regras->andwhere( "regra LIKE('%{$objDesafio->filter}%')");
        }
        if($objDesafio->ativo && $objDesafio->ativo != 'T' )
        {
            $regras->andwhere("ativo = '{$objDesafio->ativo}'");
        }
        
        $regras->orderBy('regra');

        return $regras->execute();
    }
    
    public function salvarPontuacao($dados){
       
        if($dados['id']){
           $regra = $this->findFirst("id = ".$dados['id']);
        }else{
           $regra = $this;
        }
        
        $regra->assign(array(
            'empresaId'     => 1,
            'regra'         => $dados['regra'],
            'pontuacao'     => $dados['pontuacao'],
            'observacao'    => $dados['observacao']
        ));

        if (!$regra->save()) {
            foreach ($regra->getMessages() as $mensagem) {
                die($mensagem);
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => 'Não foi possível salvar a regra!!!');
        } else {
            return array('status' => 'ok', 'message' => 'Regra salva com sucesso!!!');
        }
    }
    
    public function ativarInativarPontuacao(\stdClass $dados){

        $regra = $this->findFirst("id = ".$dados->id);
        
        $regra->assign(array(
            'ativo'         => $dados->status
        ));

        if (!$regra->save()) {
            foreach ($regra->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->status == 'N'){
                return array('status' => 'ok', 'message' => 'Pontuacao inativada com sucesso!!!');
            }else{
                return array('status' => 'ok', 'message' => 'Pontuacao ativada com sucesso!!!');
            }  
        }
    }
}