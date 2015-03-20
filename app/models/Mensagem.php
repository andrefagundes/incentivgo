<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\Mensagem
 * Modelo de mensagens
 */
class Mensagem extends Model
{
    const DELETED               = 'N';
    const NOT_DELETED           = 'Y';
    
    public static $_instance;
   
    /**
     * @var integer
     */
    public $id;

    /**
     * @var char
     */
    public $titulo;
    
    /**
     * @var char
     */
    public $mensagem;
    
    /**
     * @var integer
     */
    public $mensagemId;
    
    /**
     * @var integer
     */
    public $remetenteId;
    
    /**
     * @var date
     */
    public $envioDt;
    
    /**
     * @var char
     */
    public $lida;
    
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
     * Antes de criar a mensagem atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de cadastro
        $this->envioDt = date('Y-m-d H:i:s');

        // Seta status da mensagem para ativa
        $this->ativo = 'Y';
        
        // Seta status da mensagem para ativa
        $this->lida = 'N';
    }
    
    /**
     * Valida campos obrigatórios
     */
    public function validation()
    {
        $this->validate(new PresenceOf(array(
          'field' => 'titulo',
          'message' => 'O título da mensagem é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'mensagem',
          'message' => 'A descrição da mensagem é obrigatória!!!'
        )));
        
        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
        $this->belongsTo('remetenteId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuarioRemetente',
            'reusable' => true
        ));
        
        $this->hasMany('id', 'Incentiv\Models\MensagemDestinatario', 'mensagemId', array(
            'alias' => 'mensagemDestinatario',
            'foreignKey' => array(
                'message' => 'A mensagem não pode ser excluída porque ela possui destinatários.'
            )
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => Mensagem::DELETED
            )
        ));
    }
    
    public function fetchAllMensagens(\stdClass $objMensagem){
        
        $mensagem = Mensagem::query()->columns(array('id',
                                                'titulo',
                                                'mensagem',
                                                'mensagemId',
                                                'remetenteId',
                                                'envioDt', 
                                                'ativo' ));
        
        if(isset($objMensagem->remetenteId))
        {
           $mensagem->andwhere( "remetenteId = {$objMensagem->remetenteId}");
        }
        if($objMensagem->filter)
        {
           $mensagem->andwhere( "lida = {$objMensagem->filter}");
        }
        
        $mensagem->andwhere( "mensagemId IS NULL");

        $mensagem->orderBy('id');

        return $mensagem->execute();
    }
    
    public function salvarMensagem($arrMensagem){

        $this->assign(array(
            'titulo'            => $arrMensagem['titulo'],
            'mensagem'          => $arrMensagem['mensagem'],
            'remetenteId'       => $arrMensagem['remetente']
        ));
        
        $destinatarios = explode(',', $arrMensagem['destinatarios-mensagem']);
        $mensagemDestinatario = array();

        //grava os usuarios participantes
        foreach ($destinatarios as $id){
            $mensagemDestinatario[$id]                  = new MensagemDestinatario();
            $mensagemDestinatario[$id]->destinatarioId  = $id;
            $this->assign(array(
                'mensagemDestinatario' => $mensagemDestinatario
            ));
        }
            
        if (!$this->save()) {
            return array('status' => 'error', 'message'=>'Não foi possível enviar a mensagem!!!');
        }
        
        return array('status' => 'ok','message'=>'Mensagem salva com sucesso!!!');
        
    }
    public function responderMensagem(\stdClass $objMensagem){

        $this->assign(array(
            'titulo'            => $objMensagem->titulo,
            'mensagem'          => $objMensagem->mensagem,
            'remetenteId'       => $objMensagem->remetenteId,
            'destinatarioId'    => $objMensagem->destinatarioId,
            'ajudaId'           => $objMensagem->mensagemId
        ));

        if (!$this->save()) {
            return array('status' => 'error', 'message'=>'Não foi possível responder a mensagem!!!');
        }

        return array('status' => 'ok','message'=>'Mensagem respondida com sucesso!!!');
        
    }
    
    public function excluirMensagem(\stdClass $objMensagem){

        $mensagem = $this::findFirst($objMensagem->id);
        if ($mensagem != false) {
            if ($mensagem->delete() == false) {
                return array('status' => 'error', 'message'=>'Não foi possível excluir a mensagem!!!');
            } else {
                return array('status' => 'ok','message'=>'Mensagem excluída com sucesso!!!');
            }
        }else{
              return array('status' => 'error', 'message'=>'Mensagem não foi encontrada!!!');
        }
    }
}