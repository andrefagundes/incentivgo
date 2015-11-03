<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\PresenceOf;

/**
 * Incentiv\Models\Noticia
 * Modelo de registro de anotações
 */
class Noticia extends Model
{
    
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
    public $titulo;
    
    /**
     * @var string
     */
    public $noticia;
    
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
     * Antes de criar o noticia atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de cadastro
        $this->criacaoDt = time();
        
        // status da notícia
        $this->ativo = 'Y';
    }
    
    /**
     * Valida campos obrigatórios
     */
    public function validation()
    {
        $this->validate(new PresenceOf(array(
          'field' => 'titulo',
          'message' => $this->getDI()->getShared('lang')->_("MSG07", array("campo" => $this->_lang['titulo']))
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'noticia',
          'message' => $this->getDI()->getShared('lang')->_("MSG01", array("campo" => $this->_lang['descricao']))
        )));
        
        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));
    }
    
    public function fetchAllNoticias(\stdClass $objNoticia) {
        
        $noticias = Noticia::query()->columns(
                         array( 'id',
                                'titulo',
                                'noticia', 
                                'criacaoDt',
                                'status'    => 'ativo'));
        
        if($objNoticia->filter)
        {
           $noticias->andwhere( "noticia LIKE('%{$objNoticia->filter}%') OR titulo LIKE('%{$objNoticia->filter}%') ");
        }
        if($objNoticia->ativo && $objNoticia->ativo != 'T' )
        {
            $noticias->andwhere("ativo = '{$objNoticia->ativo}'");
        }
        
        $noticias->andwhere("empresaId = '{$objNoticia->empresaId}'");
        
        $noticias->orderBy('noticia');

        return $noticias->execute();
    }
    
    public function salvarNoticia($objNoticia){
        
        if($objNoticia['id']){
           $noticia = $this->findFirst("id = ".$objNoticia['id']);
        }else{
           $noticia = $this;
        }

        $noticia->assign(array(
            'empresaId'     => $objNoticia['empresaId'],
            'titulo'        => $objNoticia['titulo'],
            'noticia'       => $objNoticia['noticia']
        ));

        if (!$noticia->save()) {
            foreach ($noticia->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message'=> $this->_lang->_("MSG08", array("campo" => $this->_lang['noticia'])));
        }

        return array('status' => 'ok','message'=> $this->_lang->_("MSG09", array("campo" => $this->_lang['noticia'])));
        
    }
    
    public function excluirNoticia(\stdClass $objNoticia){

        $noticia = $this::findFirst($objNoticia->noticiaId);
        if ($noticia != false) {
            if ($noticia->delete() == false) {
                return array('status' => 'error', 'message'=> $this->_lang->_("MSG10", array("campo" => $this->_lang['noticia'])));
            } else {
                return array('status' => 'ok','message'=> $this->_lang->_("MSG11", array("campo" => $this->_lang['noticia'])));
            }
        }else{
              return array('status' => 'error', 'message'=>$this->_lang->_("MSG12", array("campo" => $this->_lang['noticia'])));
        }
    }
    
    public function ativarInativarNoticia(\stdClass $dados){

        $noticia = $this->findFirst("id = ".$dados->id);
        
        $noticia->assign(array(
            'ativo'         => $dados->status
        ));

        if (!$noticia->save()) {
            foreach ($noticia->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->status == 'N'){
                return array('status' => 'ok', 'message' => $this->_lang->_("MSG05", array("campo" => $this->_lang['noticia'])));
            }else{
                return array('status' => 'ok', 'message' => $this->_lang->_("MSG06", array("campo" => $this->_lang['noticia'])));
            }  
        }
    }
    
    
    
}