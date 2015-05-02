<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\PresenceOf;

use Incentiv\Models\Recompensa;

/**
 * Incentiv\Models\UsuarioPedidoRecompensa
 * Controla todos os pedidos de uso de recompensa(pontos) do colaborador.
 */
class UsuarioPedidoRecompensa extends Model
{
    
    private static $_instance;
    
    CONST PEDIDO_RECOMPENSA_ENVIADO = 1;
    CONST PEDIDO_RECOMPENSA_USADO = 2;
    
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
        
        $pontosUsuario    = UsuarioPontuacaoCredito::build()->buscarPontuacaoUsuario($objPedidoRecompensa['usuarioId']);
      
        $pontosRecompensa = Recompensa::build()->findFirst(array("id = {$objPedidoRecompensa['recompensaId']} AND empresaId = {$objPedidoRecompensa['empresaId']}",'columns' => 'pontuacao'));

        if($pontosUsuario < $pontosRecompensa->pontuacao ){
            return array('status' => 'error', 'message' => 'Desculpe, você não possui pontos suficientes para uso da recompensa.');
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
}