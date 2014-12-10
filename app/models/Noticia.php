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
    public $descricao;
    
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
     * Antes de criar o desafio atribui data e ativacao
     */
    public function beforeValidationOnCreate()
    {
        // Data de cadastro
        $this->criacaoDt = time();
    }
    
    /**
     * Valida campos obrigatórios
     */
    public function validation()
    {
        $this->validate(new PresenceOf(array(
          'field' => 'descricao',
          'message' => 'A descrição do rascunho é obrigatória!!!'
        )));
        
        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario',
            'reusable' => true
        ));
    }
    
    public function salvarNoticia(\stdClass $objNoticia){

        $this->assign(array(
            'empresaId'     => $objNoticia->empresaId,
            'descricao'       => $objNoticia->descricao
        ));

        if (!$this->save()) {
            return array('status' => 'error', 'message'=>'Não foi possível salvar a notícia!!!');
        }

        return array('status' => 'ok','message'=>'Notícia salva com sucesso!!!');
        
    }
    
    public function excluirNoticia(\stdClass $objNoticia){

        $noticia = $this::findFirst($objNoticia->anotacaoId);
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