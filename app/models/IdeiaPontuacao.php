<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\IdeiaPontuacao
 * Todas as pontuacoes das ideias registradas na aplicação(mapeamento)
 */
class IdeiaPontuacao extends Model
{
    const DELETED                   = 'N';
    const NOT_DELETED               = 'Y';
    
    private static $_instance;
   
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
    public $pontuacaoIdeiaEnviada;
    
    /**
     * @var integer
     */
    public $pontuacaoIdeiaAprovada;
   
    /**
     * @var char
     */
    public $ativo;
    
    /**
     * @var date
     */
    public $criacaoDt;   
    
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
          'field' => 'pontuacaoIdeiaEnviada',
          'message' => 'A pontuação para ideia enviada é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'pontuacaoIdeiaAprovada',
          'message' => 'A pontuação para ideia aprovada é obrigatória!!!'
        )));

        return $this->validationHasFailed() != true;
    }

    /**
     * Antes de criar o usuário atribui uma senha
     */
    public function beforeValidationOnCreate()
    {
        $this->criacaoDt    = date('Y-m-d');
        $this->ativo        = IdeiaPontuacao::NOT_DELETED;
    }

    public function initialize()
    {  
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => IdeiaPontuacao::DELETED
            )
        ));
    }
    
   public function salvarMapeamentoIdeia($objMapeamento){
        try {

            if($objMapeamento->dados['id']){
               $mapeamento = $this->findFirst("id = {$objMapeamento->dados['id']}");
               if($mapeamento->pontuacaoIdeiaEnviada != $objMapeamento->dados['pontuacao_ideia_enviada'] ||
                  $mapeamento->pontuacaoIdeiaAprovada != $objMapeamento->dados['pontuacao_ideia_aprovada']){
                  $mapeamento->delete();
                  $mapeamento = $this->build();
               }
            }else{
                $mapeamento = $this->build();
            }
            
            $mapeamento->assign(array(
                'empresaId'               => $objMapeamento->dados['empresaId'],
                'pontuacaoIdeiaEnviada'   => $objMapeamento->dados['pontuacao_ideia_enviada'],
                'pontuacaoIdeiaAprovada'  => $objMapeamento->dados['pontuacao_ideia_aprovada']
            ));

            if (!$mapeamento->save()) {

                foreach ($this->getMessages() as $mensagem) {
                  $message =  $mensagem;
                  break;
                }

                return array('status' => 'error', 'message'=> $message );
            }

            return array('status' => 'ok','message'=>'Mapeamento salvo com sucesso!!!');
        
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}