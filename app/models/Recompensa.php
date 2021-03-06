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
    
    private $_lang = array();
   
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
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPontuacaoDebito', 'recompensaId', array(
            'alias' => 'recompensa',
            'foreignKey' => array(
                'message' => $this->_lang['MSG31']
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPedidoRecompensa', 'recompensaId', array(
            'alias' => 'pedidoRecompensa',
            'foreignKey' => array(
                'message' => $this->_lang['MSG32']
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
        
        $recompensas->where("empresaId = '{$objDesafio->empresaId}'");
        
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
              return array('status' => 'error', 'message' => $mensagem);
              break;
            }
            return array('status' => 'error', 'message' => $this->_lang->_("MSG08", array("campo" => $this->_lang['recompensa'])));
        } else {
            return array('status' => 'ok', 'message' => $this->_lang->_("MSG09", array("campo" => $this->_lang['recompensa'])));
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
                return array('status' => 'ok', 'message' => $this->_lang->_("MSG29", array("campo" => $this->_lang['recompensa'])));
            }else{
                return array('status' => 'ok', 'message' => $this->_lang->_("MSG30", array("campo" => $this->_lang['recompensa'])));
            }  
        }
    }
}