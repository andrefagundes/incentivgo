<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\DesafioPontuacao
 * Todas as pontuacoes dos desafios registradas na aplicação(mapeamento)
 */
class DesafioPontuacao extends Model
{
    const DELETED                   = 'N';
    const NOT_DELETED               = 'Y';
    
    private static $_instance;
    
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
     * @var integer
     */
    public $desafioTipoId;
   
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
          'message' => $this->getDI()->getShared('lang')->_("MSG40")
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'desafioTipoId',
          'message' => $this->getDI()->getShared('lang')->_("MSG41")
        )));

        return $this->validationHasFailed() != true;
    }

    /**
     * Antes de criar o usuário atribui uma senha
     */
    public function beforeValidationOnCreate()
    {
        $this->criacaoDt    = date('Y-m-d');
        $this->ativo        = DesafioPontuacao::NOT_DELETED;
    }

    public function initialize()
    {  
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
        
        $this->belongsTo('desafioTipoId', 'Incentiv\Models\DesafioTipo', 'id', array(
            'alias' => 'desafioTipo',
            'reusable' => true
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => DesafioPontuacao::DELETED
            )
        ));
    }
    
   public function salvarMapeamentoDesafio($objMapeamento){
        try {
            
            $db = $this->getDI()->getShared('db');
        
            $db->begin();
 
            foreach ($objMapeamento->dados['tipos'] as $key => $pontuacao){
                $tipoDesafioEmpresa = $this->findFirst("empresaId = {$objMapeamento->dados['empresaId']} AND desafioTipoId = {$key} AND ativo = 'Y'");
                
                
                if(!$tipoDesafioEmpresa){
                    $desafioTipo = new DesafioPontuacao();
                    $desafioTipo->assign(array(
                        'empresaId'     => $objMapeamento->dados['empresaId'],
                        'desafioTipoId' => $key,
                        'pontuacao'     => $pontuacao
                    ));
                    
                    if(!$desafioTipo->save()) {
                        $db->rollback();
                        return array('status' => 'error', 'message' => $this->_lang['MSG42']);
                    }
                }else{
                    $desafioTipo = new DesafioPontuacao();
                    if($tipoDesafioEmpresa->pontuacao != $pontuacao){
                        $tipoDesafioEmpresa->delete();
                        
                        $desafioTipo->assign(array(
                            'empresaId'     => $objMapeamento->dados['empresaId'],
                            'desafioTipoId' => $key,
                            'pontuacao'     => $pontuacao
                        ));
                        
                        if(!$desafioTipo->save()) {
                            $db->rollback();
                            return array('status' => 'error', 'message'=> $this->_lang['MSG42']);
                        }
                    }
                }
            }
            
            $db->commit();
            return array('status' => 'ok','message'=> $this->_lang['MSG43']);
        
        } catch (Exception $message) {
            return array('status' => 'error', 'message'=> $message );
        }
    }
}