<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\UsuarioPontuacaoCredito
 * Todas as pontuacoes das ideias registradas na aplicação(mapeamento)
 */
class UsuarioPontuacaoCredito extends Model
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
    public $pontuacaoTipo;
   
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
          'field' => 'pontuacaoTipo',
          'message' => $this->getDI()->getShared('lang')->_("MSG66")
        )));

        return $this->validationHasFailed() != true;
    }

    /**
     * Antes de criar o usuário atribui uma senha
     */
    public function beforeValidationOnCreate()
    {
        $this->cadastroDt    = date('Y-m-d');
    }

    public function initialize()
    {  
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario',
            'reusable' => true
        ));
    }
    
    public function creditarUsuario(\stdClass $objCredito){
        try {
            
            $pontuacaoUsuario = new UsuarioPontuacaoCredito();
            
            $pontuacaoUsuario->assign(array(
                'empresaId'     => $objCredito->empresaId,
                'usuarioId'     => $objCredito->usuarioId,
                'pontuacao'     => $objCredito->pontuacao,
                'pontuacaoTipo' => $objCredito->pontuacaoTipo
            ));

            if (!$pontuacaoUsuario->save()) {

                foreach ($this->getMessages() as $mensagem) {
                  $message =  $mensagem;
                  break;
                }

                return array('status' => 'error', 'message'=> $message );
            }

            return array('status' => 'ok');
        
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    
    public function buscarPontuacaoUsuario($usuarioId){
        $pontuacaoCredito = UsuarioPontuacaoCredito::sum(array( "column" => "pontuacao", "conditions" => "usuarioId = {$usuarioId}"));
        $pontuacaoDebito  = UsuarioPontuacaoDebito::sum(array("column" => "pontuacao", "conditions" => "usuarioId = {$usuarioId}"));

        $total = $pontuacaoCredito - $pontuacaoDebito;

        return $total;
    }
}