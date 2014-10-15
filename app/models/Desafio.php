<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\Desafio
 * Modelo de registro de desafios
 */
class Desafio extends Model
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
    public $desafio;
    
    /**
     * @var integer
     */
    public $pontuacao;
    
    /**
     * @var integer
     */
    public $premiacao;
    
    /**
     * @var integer
     */
    public $criacaoDt;
    
    /**
     * @var integer
     */
    public $inicioDt;
    
    /**
     * @var integer
     */
    public $fimDt;
   
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
     * Antes de criar o desafio atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de cadastro
        $this->criacaoDt = time();

        // Seta status do desafio para ativo
        $this->ativo = 'S';
    }
    
    /**
     * Valida campos obrigatórios
     */
    public function validation()
    {
        $this->validate(new PresenceOf(array(
          'field' => 'desafio',
          'message' => 'A descrição do desafio é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'pontuacao',
          'message' => 'A pontuação do desafio é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'inicioDt',
          'message' => 'A data de início do desafio é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'fimDt',
          'message' => 'A data de fim do desafio é obrigatória!!!'
        )));

        return $this->validationHasFailed() != true;
    }

    /**
     * Define a data e hora antes de atualizar o desafio
     */
    public function beforeValidationOnUpdate()
    {
        // Data de alteração
        $this->modificacaoDt = date('Y-m-d');
    }

    public function initialize()
    {
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPontuacao', 'desafioId', array(
            'alias' => 'pontuacao',
            'foreignKey' => array(
                'message' => 'O desafio não pode ser excluído porque ele possui pontuação lançada.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\DesafioUsuario', 'desafioId', array(
            'alias' => 'desafioUsuario',
            'foreignKey' => array(
                'message' => 'O desafio não pode ser excluído porque ele possui usuarios.'
            )
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => Desafio::DELETED
            )
        ));
    }
    
    public function fetchAllDesafios(\stdClass $objDesafio) {
        
        $desafios = Desafio::query()->columns(
                         array( 'id',
                                'desafio',
                                'pontuacao', 
                                'inicioDt' => "DATE_FORMAT( inicioDt , '%d/%c/%Y' )",
                                'fimDt' => "DATE_FORMAT( fimDt , '%d/%c/%Y' )",
                                'premiacao',
                                'status' => 'ativo'));
        
        if($objDesafio->filter)
        {
           $desafios->andwhere( "desafio LIKE('%{$objDesafio->filter}%')");
        }
        if($objDesafio->ativo && $objDesafio->ativo != 'T' )
        {
            $desafios->andwhere("ativo = '{$objDesafio->ativo}'");
        }
        
        $desafios->order('desafio');

        return $desafios->execute();
    }
    
    public function salvarDesafio($dados){

        //traz os serviços funcoes e db para o model
        $funcoes = $this->getDI()->getShared('funcoes');
        $db      = $this->getDI()->getShared('db');
        
        $db->begin();

        if($dados['id']){
           $desafio = $this->findFirst("id = ".$dados['id']);
           $desafio->desafioUsuario->delete();
        }else{
           $desafio = $this;
        }

        $desafio->assign(array(
            'empresaId'     => 1,
            'desafio'       => $dados['desafio'],
            'pontuacao'     => (int) $dados['pontuacao'],
            'premiacao'     => $dados['premiacao'],
            'inicioDt'      => $funcoes->formatarData($dados['data_inicio']),
            'fimDt'         => $funcoes->formatarData($dados['data_fim'])
        ));
        
        $colaboradores = explode(',', $dados['colaboradores-participantes']);
        $desafioUsuario = array();
        
        //grava os usuarios participantes
        foreach ($colaboradores as $id){
            $desafioUsuario[$id]             = new DesafioUsuario();
            $desafioUsuario[$id]->usuarioId  = $id;
            $desafio->assign(array(
                'desafioUsuario' => $desafioUsuario,
            ));
        }

        if (!$desafio->save()) {
            $db->rollback();
            return array('status' => 'error', 'message'=>'Não foi possível salvar o desafio!!!');
        }

        $db->commit();
        return array('status' => 'ok','message'=>'Desafio salvo com sucesso!!!');
        
    }
    
    public function ativarInativarDesafio(\stdClass $dados){

        $desafio = $this->findFirst("id = ".$dados->id);
        
        $desafio->assign(array(
            'ativo'         => $dados->status
        ));

        if (!$desafio->save()) {
            foreach ($desafio->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->status == 'N'){
                return array('status' => 'ok', 'message' => 'Desafio inativado com sucesso!!!');
            }else{
                return array('status' => 'ok', 'message' => 'Desafio ativado com sucesso!!!');
            }  
        }
    }
}