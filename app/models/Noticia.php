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
          'message' => 'O título da notícia é obrigatória!!!'
        )));
        
        $this->validate(new PresenceOf(array(
          'field' => 'noticia',
          'message' => 'A descrição da notícia é obrigatória!!!'
        )));
        
        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
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
                                'criacaoDt' => "DATE_FORMAT( criacaoDt , '%d/%m/%Y' )",
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
        
        $noticias->order('noticia');

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
            die($message);
            return array('status' => 'error', 'message'=>'Não foi possível salvar a notícia!!!');
        }

        return array('status' => 'ok','message'=>'Notícia salva com sucesso!!!');
        
    }
    
    public function excluirNoticia(\stdClass $objNoticia){

        $noticia = $this::findFirst($objNoticia->noticiaId);
        if ($noticia != false) {
            if ($noticia->delete() == false) {
                return array('status' => 'error', 'message'=>'Não foi possível excluir a notícia!!!');
            } else {
                return array('status' => 'ok','message'=>'Notícia excluída com sucesso!!!');
            }
        }else{
              return array('status' => 'error', 'message'=>'Notícia não foi encontrada!!!');
        }
    }
}