<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\UsuarioPontuacaoDebito
 * Todas as pontuacoes das ideias registradas na aplicação(mapeamento)
 */
class UsuarioPontuacaoDebito extends Model
{
    
    private static $_instance;
    
    private $_lang = array();
    
    CONST PONTUACAO_DESAFIO_APROVADO = 1;
    CONST PONTUACAO_IDEIA_ENVIADA = 2;
    CONST PONTUACAO_IDEIA_APROVADA = 3;
    
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
    public $pontuacao;
    
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
          'message' => $this->getDI()->getShared('lang')->_("MSG40")
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'usuarioId',
          'message' => $this->getDI()->getShared('lang')->_("MSG57")
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'pontuacao',
          'message' => $this->getDI()->getShared('lang')->_("MSG65")
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'recompensaId',
          'message' => $this->getDI()->getShared('lang')->_("MSG67")
        )));

        return $this->validationHasFailed() != true;
    }

    /**
     * Antes de criar o usuário atribui uma senha
     */
    public function beforeValidationOnCreate()
    {
        $this->cadastroDt    = date('Y-m-d H:i:s');
    }

    public function initialize()
    {  
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->belongsTo('recompensaId', 'Incentiv\Models\Recompensa', 'id', array(
            'alias' => 'recompensa',
            'reusable' => true
        ));
        
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario',
            'reusable' => true
        ));
        
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
    }
    
    public function debitarUsuario( $arrayDebito){
        try {
            
            $recompensa = Recompensa::build()->findFirst(array("id = {$arrayDebito['recompensa_id']} AND empresaId = 
                                        {$arrayDebito['empresa_id']} AND ativo = 'Y'",'columns'=>'pontuacao'));

            $pontuacaoDebitoUsuario = new UsuarioPontuacaoDebito();
            
            $pontuacaoDebitoUsuario->assign(array(
                'empresaId'     => $arrayDebito['empresa_id'],
                'usuarioId'     => $arrayDebito['usuario_id'],
                'pontuacao'     => $recompensa['pontuacao'],
                'recompensaId' => $arrayDebito['recompensa_id']
            ));

            if (!$pontuacaoDebitoUsuario->save()) {

                foreach ($this->getMessages() as $mensagem) {
                  $message =  $mensagem;
                  break;
                }

                return array('status' => 'error', 'message'=> $message );
            }

            return array('status' => 'ok','message'=> $this->_lang['MSG68']);
        
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }  
}