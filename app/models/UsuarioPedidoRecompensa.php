<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\PresenceOf;

use Incentiv\Models\Recompensa;

/**
 * Incentiv\Models\UsuarioPedidoRecompensa
 * Controla todos os pedidos de uso de recompensa(inventivs) do colaborador.
 */
class UsuarioPedidoRecompensa extends Model
{
    
    private static $_instance;
    
    CONST PEDIDO_RECOMPENSA_ENVIADO     = 1;
    CONST PEDIDO_RECOMPENSA_USADO       = 2;
    CONST PEDIDO_RECOMPENSA_CANCELADO   = 3;
    
    /**
     * @var integer
     */
    public $id; 
    
    /**
     * @var integer
     */
    public $empresaId;  
   
    /**
     * @var integer
     */
    public $usuarioId;
    
    /**
     * @var integer
     */
    public $recompensaId;
   
    /**
     * @var integer
     */
    public $status;
    
    /**
     * @var varchar
     */
    public $observacaoUsuario;
    
    /**
     * @var date
     */
    public $cadastroDt;   
    
    public static function build()
    {
        if( !isset( self::$_instance ) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    /**
     * Valida campos obrigatórios
     */
    public function validation()
    {
        $this->validate(new PresenceOf(array(
          'field' => 'empresaId',
          'message' => 'O id da empresa é obrigatório!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'usuarioId',
          'message' => 'O id do usuário é obrigatório!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'recompensaId',
          'message' => 'O id da recompensa é obrigatório!!!'
        )));

        return $this->validationHasFailed() != true;
    }

    /**
     * Antes de criar a recompensa atribui uma data
     */
    public function beforeValidationOnCreate()
    {
        $this->cadastroDt    = date('Y-m-d H:i:s');
        $this->status        = UsuarioPedidoRecompensa::PEDIDO_RECOMPENSA_ENVIADO;
    }
    
    /**
     * Define a data e hora antes de atualizar o status
     */
    public function beforeValidationOnUpdate()
    {
        // Seta a data e hora antes de atualizar o status
        $this->respostaDt = date('Y-m-d H:i:s');
    }

    public function initialize()
    {  
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario',
            'reusable' => true
        ));
        
        $this->belongsTo('recompensaId', 'Incentiv\Models\Recompensa', 'id', array(
            'alias' => 'recompensa',
            'reusable' => true
        ));
    }
    
    public function fetchAllPedidosRecompensa(\stdClass $objPedido) {
        
        $pedidosRecompensa = UsuarioPedidoRecompensa::query()->columns(
                         array( 'Incentiv\Models\UsuarioPedidoRecompensa.id',
                                'recompensa.recompensa',
                                'Incentiv\Models\UsuarioPedidoRecompensa.observacaoUsuario',
                                'cadastroDt' => "DATE_FORMAT( Incentiv\Models\UsuarioPedidoRecompensa.cadastroDt , '%d/%m/%Y %H:%i:%s' )",
                                'Incentiv\Models\UsuarioPedidoRecompensa.status'));
        
        $pedidosRecompensa->innerjoin('Incentiv\Models\Recompensa', 'Incentiv\Models\UsuarioPedidoRecompensa.recompensaId = recompensa.id', 'recompensa');
        
        $pedidosRecompensa->where("Incentiv\Models\UsuarioPedidoRecompensa.usuarioId = {$objPedido->usuarioId}");
        $pedidosRecompensa->andwhere("Incentiv\Models\UsuarioPedidoRecompensa.empresaId = {$objPedido->empresaId}");
        
        $pedidosRecompensa->orderBy('Incentiv\Models\UsuarioPedidoRecompensa.id');

        return $pedidosRecompensa->execute();
    }
    
    public function salvarPedidoRecompensa($objPedidoRecompensa){
        
        $pedidoRecompensa = $this::build()->findFirst(array("usuarioId = {$objPedidoRecompensa['usuarioId']} AND status = ".UsuarioPedidoRecompensa::PEDIDO_RECOMPENSA_ENVIADO." ",'columns' => 'id'));
        
        if($pedidoRecompensa){
            return array('status' => 'error', 'message' => 'Desculpe!!! Só pode ser feito um pedido por vez.');
            break;
        }
        
        $pontosUsuario    = UsuarioPontuacaoCredito::build()->buscarPontuacaoUsuario($objPedidoRecompensa['usuarioId']);
      
        $pontosRecompensa = Recompensa::build()->findFirst(array("id = {$objPedidoRecompensa['recompensaId']} AND empresaId = {$objPedidoRecompensa['empresaId']}",'columns' => 'pontuacao'));

        if($pontosUsuario < $pontosRecompensa->pontuacao ){
            return array('status' => 'error', 'message' => 'Desculpe, você não possui incentivs suficientes para uso da recompensa.');
            break;
        }
        
        $this->assign(array(
            'usuarioId'             => $objPedidoRecompensa['usuarioId'],
            'empresaId'             => $objPedidoRecompensa['empresaId'],
            'recompensaId'          => $objPedidoRecompensa['recompensaId'],
            'observacaoUsuario'     => $objPedidoRecompensa['observacaoUsuario']
        ));

        if (!$this->save()) {
            foreach ($this->getMessages() as $mensagem) {
              return array('status' => 'error', 'message' => $mensagem);
              break;
            }
            return array('status' => 'error', 'message' => 'Não foi possível fazer o pedido.');
        } else {
            return array('status' => 'ok', 'message' => 'Pedido feito com sucesso. Entre em contato com seu gerente para uso da recompensa');
        }
    }
    
    public function fetchAllPedidos(\stdClass $objPedidos) {
        
        $pedidosRecompensa = UsuarioPedidoRecompensa::query()->columns(
                         array( 'Incentiv\Models\UsuarioPedidoRecompensa.id',
                                'recompensa.recompensa',
                                'recompensa.pontuacao',
                                'usuario.nome',
                                'Incentiv\Models\UsuarioPedidoRecompensa.observacaoUsuario',
                                'cadastroDt' => "DATE_FORMAT( Incentiv\Models\UsuarioPedidoRecompensa.cadastroDt , '%d/%m/%Y %H:%i:%s' )",
                                'Incentiv\Models\UsuarioPedidoRecompensa.status'));
        
        $pedidosRecompensa->innerjoin('Incentiv\Models\Recompensa', 'Incentiv\Models\UsuarioPedidoRecompensa.recompensaId = recompensa.id', 'recompensa');
        $pedidosRecompensa->innerjoin('Incentiv\Models\Usuario', 'Incentiv\Models\UsuarioPedidoRecompensa.usuarioId = usuario.id', 'usuario');
        
        $pedidosRecompensa->andwhere("Incentiv\Models\UsuarioPedidoRecompensa.empresaId = {$objPedidos->empresaId}");
        
        if($objPedidos->filter)
        {
           $pedidosRecompensa->andwhere( "recompensa LIKE('%{$objPedidos->filter}%') OR usuario.nome LIKE('%{$objPedidos->filter}%')");
        }
        
        $pedidosRecompensa->orderBy('Incentiv\Models\UsuarioPedidoRecompensa.id');

        return $pedidosRecompensa->execute();
    }
    
    public function alterarStatusPedidoRecompensa(\stdClass $objDadosPedido){
        
        $db = $this->getDI()->getShared('db');
        
        try {        
            $db->begin();
 
            $pedidoRecompensa = $this->findFirst("id = {$objDadosPedido->id_recompensa}");

            if($objDadosPedido->resposta == 'Y'){
                $pedidoRecompensa->status = UsuarioPedidoRecompensa::PEDIDO_RECOMPENSA_USADO;
                $message = 'Recompensa usada com sucesso!!!';
            }elseif ($objDadosPedido->resposta == 'N') {
                $pedidoRecompensa->status = UsuarioPedidoRecompensa::PEDIDO_RECOMPENSA_CANCELADO;
                $message = 'Pedido de recompensa cancelada com sucesso!!!';
            }
            
            $pedidoRecompensa->save();

            if($objDadosPedido->resposta == 'Y'){
                $objDebitoUsuario = UsuarioPontuacaoDebito::build();
                $objDebitoUsuario->empresaId    = $pedidoRecompensa->empresaId;
                $objDebitoUsuario->usuarioId    = $pedidoRecompensa->usuarioId;
                $objDebitoUsuario->recompensaId = $pedidoRecompensa->recompensaId;
                $objDebitoUsuario->pontuacao    = $pedidoRecompensa->recompensa->pontuacao;
                $objDebitoUsuario->save();
            }

            $db->commit();
            return array('status' => 'ok', 'message' => $message);
        } catch (Exception $exc) {
            $db->rollback();
            return array('status' => 'error', 'message' => $exc);
        }
    }
}