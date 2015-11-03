<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\DesafioTipo
 * Todas os tipos de pontuacoes dos desafios registradas na aplicação(mapeamento)
 */
class DesafioTipo extends Model
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
     * @var char
     */
    public $desafioTipo;
    
    /**
     * @var char
     */
    public $desafioTipo_en;
   
    /**
     * @var char
     */
    public $status;
    
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

    public function initialize()
    { 
        $this->_lang    = $this->getDI()->getShared('lang');
        
        $this->hasMany('id', 'Incentiv\Models\DesafioPontuacao', 'desafioTipoId', array(
            'alias' => 'desafioTipo',
            'foreignKey' => array(
                'message' => $this->_lang['MSG44']
            )
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => DesafioTipo::DELETED
            )
        ));
    }
    
    public function buscarTiposDesafio($empresaId){
        $desafios = $this::query()->columns(
                         array( 'Incentiv\Models\DesafioTipo.id',
                                'Incentiv\Models\DesafioTipo.desafioTipo',
                                'Incentiv\Models\DesafioTipo.desafioTipoEn',
                                'desafioPontuacao.pontuacao'));
        
        $desafios->leftjoin('Incentiv\Models\DesafioPontuacao', "Incentiv\Models\DesafioTipo.id = desafioPontuacao.desafioTipoId AND desafioPontuacao.empresaId = {$empresaId} AND desafioPontuacao.ativo = 'Y'", 'desafioPontuacao');
        
        $desafios->orderBy('Incentiv\Models\DesafioTipo.id');

        return $desafios->execute();
    }
}