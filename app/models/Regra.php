<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\Regra
 * Modelo de registro de regras
 */
class Regra extends Model
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
                'value' => Regra::DELETED
            )
        ));
    }
    
    public function fetchAllRegras(\stdClass $objDesafio) {
        
        $regras = Regra::query()->columns(
                         array( 'id',
                                'regra',
                                'pontuacao', 
                                'observacao',
                                'ativo'));
        
        if($objDesafio->filter)
        {
           $regras->andwhere( "regra LIKE('%{$objDesafio->filter}%')");
        }
        if($objDesafio->ativo && $objDesafio->ativo != 'T' )
        {
            $regras->andwhere("ativo = '{$objDesafio->ativo}'");
        }
        
        $regras->order('regra');

        return $regras->execute();
    }
    
    public function salvarRegra($dados){
       // var_dump($dados['data_inicio']);die;
        $this->assign(array(
            'empresaId'     => 1,
            'regra'         => $dados['regra'],
            'pontuacao'     => $dados['pontuacao'],
            'observacao'    => $dados['observacao']
        ));

        if (!$this->save()) {
            return array('status' => 'error', 'message' => 'Não foi possível salvar a regra');
        } else {
            return array('status' => 'ok', 'message' => 'Regra salva com sucesso');
        }
    }
}