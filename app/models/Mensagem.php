<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\PresenceOf,
    Phalcon\Mvc\Model\Relation;
use Phalcon\Events\Manager as EventsManager,
	Phalcon\Mvc\Model\Manager as ModelsManager;
/**
 * Incentiv\Models\Mensagem
 * Modelo de mensagens
 */
class Mensagem extends Model
{
    const MENSAGENS_TIPO_ENTRADA    = 1;
    const MENSAGENS_TIPO_ENVIADA    = 2;
    const MENSAGENS_TIPO_EXCLUIDA   = 3;
    
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
                'message' => 'A mensagem não pode ser excluída porque ela possui destinatários.',
                'action' => Relation::ACTION_CASCADE
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\MensagemExcluida', 'mensagemId', array(
            'alias' => 'mensagemExcluida',
            'foreignKey' => array(
                'message' => 'A mensagem não pode ser excluída porque ela possui exclusão na tabela mensagem_excluida.'
            )
        ));
    }
    
    public function fetchAllMensagens(\stdClass $objMensagem){

        $mensagem = Mensagem::query()->columns(array('Incentiv\Models\Mensagem.id',
                                                'Incentiv\Models\Mensagem.titulo',
                                                'Incentiv\Models\Mensagem.mensagem',
                                                'Incentiv\Models\Mensagem.mensagemId',
                                                'Incentiv\Models\Mensagem.remetenteId',
                                                'Incentiv\Models\Mensagem.envioDt',
                                                'usuario.nome'));
        
        $mensagem->innerjoin('Incentiv\Models\Usuario', "Incentiv\Models\Mensagem.remetenteId = usuario.id", 'usuario');
         
        if($objMensagem->tipo == Mensagem::MENSAGENS_TIPO_ENTRADA)
        {
            $mensagem->innerjoin('Incentiv\Models\MensagemDestinatario', "MensagemDestinatario.mensagemId = Incentiv\Models\Mensagem.id", 'MensagemDestinatario');
            $mensagem->leftjoin('Incentiv\Models\MensagemExcluida', "MensagemDestinatario.destinatarioId = mensagemExcluidaDestinatario.usuarioId AND MensagemDestinatario.mensagemId = mensagemExcluidaDestinatario.mensagemId", 'mensagemExcluidaDestinatario');
            $mensagem->where( "MensagemDestinatario.destinatarioId = {$objMensagem->destinatarioId}");
            $mensagem->andwhere( "mensagemExcluidaDestinatario.id IS NULL");
        }
        
        if(isset($objMensagem->remetenteId) && $objMensagem->tipo == Mensagem::MENSAGENS_TIPO_ENVIADA)
        {
           $mensagem->leftjoin('Incentiv\Models\MensagemExcluida', "Incentiv\Models\Mensagem.id = mensagemExcluidaRemetente.mensagemId AND Incentiv\Models\Mensagem.remetenteId = mensagemExcluidaRemetente.usuarioId", 'mensagemExcluidaRemetente');
           $mensagem->where( "Incentiv\Models\Mensagem.remetenteId = {$objMensagem->remetenteId}");
           $mensagem->andwhere( "mensagemExcluidaRemetente.id IS NULL");
        }
        
        if($objMensagem->filter && $objMensagem->tipo == Mensagem::MENSAGENS_TIPO_ENTRADA)
        {
           $mensagem->andwhere( "MensagemDestinatario.lida = '{$objMensagem->filter}'");
        }
        
        $mensagem->andwhere( "Incentiv\Models\Mensagem.mensagemId IS NULL");
        
        $mensagem->orderBy('Incentiv\Models\Mensagem.id');

        return $mensagem->execute();
        
    }
    
    public function fetchAllMensagensExcluidas(\stdClass $objMensagem){
        
        $mensagem = Mensagem::query()->columns(array('DISTINCT Incentiv\Models\Mensagem.id',
                                                'Incentiv\Models\Mensagem.titulo',
                                                'Incentiv\Models\Mensagem.mensagem',
                                                'Incentiv\Models\Mensagem.mensagemId',
                                                'Incentiv\Models\Mensagem.remetenteId',
                                                'Incentiv\Models\Mensagem.envioDt',
                                                'usuario.nome'));
        
        $mensagem->innerjoin('Incentiv\Models\Usuario', "Incentiv\Models\Mensagem.remetenteId = usuario.id", 'usuario');
        $mensagem->leftjoin('Incentiv\Models\MensagemDestinatario', "MensagemDestinatario.mensagemId = Incentiv\Models\Mensagem.id", 'MensagemDestinatario');
        $mensagem->leftjoin('Incentiv\Models\MensagemExcluida', "MensagemDestinatario.mensagemId = mensagemExcluidaDestinatario.mensagemId AND MensagemDestinatario.destinatarioId = mensagemExcluidaDestinatario.usuarioId", 'mensagemExcluidaDestinatario');
        $mensagem->leftjoin('Incentiv\Models\MensagemExcluida', "Incentiv\Models\Mensagem.id = mensagemExcluidaRemetente.mensagemId AND Incentiv\Models\Mensagem.remetenteId = mensagemExcluidaRemetente.usuarioId", 'mensagemExcluidaRemetente');
        
         $mensagem->where( "(mensagemExcluidaDestinatario.id IS NOT NULL AND mensagemExcluidaDestinatario.usuarioId = {$objMensagem->destinatarioId}) OR 
            (mensagemExcluidaRemetente.id IS NOT NULL AND mensagemExcluidaRemetente.usuarioId = {$objMensagem->remetenteId} )");
        
        if($objMensagem->filter)
        {
           $mensagem->andwhere( "MensagemDestinatario.lida = '{$objMensagem->filter}' OR mensagemExcluidaRemetente.id IS NOT NULL");
        }

        $mensagem->orderBy('Incentiv\Models\Mensagem.id');

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
        
        return array('status' => 'ok','message'=>'Mensagem enviada com sucesso!!!');
        
    }
    public function salvarMensagemResposta($arrMensagem){

        $mensagemPai = $this->findFirst("id = {$arrMensagem['mensagemId']}");
        
        $this->assign(array(
            'titulo'            => $arrMensagem['titulo'],
            'mensagemId'        => $arrMensagem['mensagemId'],
            'mensagem'          => $arrMensagem['mensagem'],
            'remetenteId'       => $arrMensagem['remetenteId']
        ));
        
        //responde a mensagem somente pra o remetente que o enviou a mensagem.
        $mensagemDestinatario = array();
        $mensagemDestinatario[$mensagemPai->remetenteId] = new MensagemDestinatario();
        $mensagemDestinatario[$mensagemPai->remetenteId]->destinatarioId  = $mensagemPai->remetenteId;
        $this->assign(array(
            'mensagemDestinatario' => $mensagemDestinatario
        ));
        
        if (!$this->save()) {
            return array('status' => 'error', 'message'=>'Não foi possível enviar a mensagem!!!');
        }
        
        return array('status' => 'ok','message'=>'Mensagem respondida com sucesso!!!');
        
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
    
    public function quantMensagensEnviadas($remetenteId){
        $mensagensRecebidas = Mensagem::query()->columns(array('quant'=>'count(*)'));
        
        $mensagensRecebidas->leftjoin('Incentiv\Models\MensagemExcluida', "Incentiv\Models\Mensagem.id = mensagemExcluida.mensagemId AND Incentiv\Models\Mensagem.remetenteId = mensagemExcluida.usuarioId", 'mensagemExcluida');
        $mensagensRecebidas->andwhere( "Incentiv\Models\Mensagem.remetenteId = {$remetenteId}");
        $mensagensRecebidas->andwhere( "mensagemExcluida.id IS NULL");
        $mensagensRecebidas->andwhere( "Incentiv\Models\Mensagem.mensagemId IS NULL");
        $count = $mensagensRecebidas->execute();
        
        return $count['quant']->quant;
    }
    
    public function buscarMensagensRecebidas($destinatarioId){
        $mensagensRecebidas = Mensagem::query()->columns(array('Incentiv\Models\Mensagem.titulo',
                                                                'envioDt' => "DATE_FORMAT( Incentiv\Models\Mensagem.envioDt , '%d/%m/%Y' )"));
        $mensagensRecebidas->innerjoin('Incentiv\Models\MensagemDestinatario', "Incentiv\Models\Mensagem.id = MensagemDestinatario.mensagemId AND MensagemDestinatario.destinatarioId = {$destinatarioId}", 'MensagemDestinatario');
        $mensagensRecebidas->leftjoin('Incentiv\Models\MensagemExcluida', "MensagemDestinatario.mensagemId = mensagemExcluida.mensagemId AND MensagemDestinatario.destinatarioId = mensagemExcluida.usuarioId", 'mensagemExcluida');
        $mensagensRecebidas->andwhere( "MensagemDestinatario.lida = 'N'");
        $mensagensRecebidas->andwhere( "mensagemExcluida.id IS NULL");
        $mensagensRecebidas->limit(3);
        
        return $mensagensRecebidas->execute()->toArray();
    }
}