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
   
    /**
     * @var integer
     */
    public $id; 
    
    /**
     * @var integer
     */
    public $desafioTipo;
   
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
        $this->hasMany('id', 'Incentiv\Models\DesafioPontuacao', 'desafioTipoId', array(
            'alias' => 'desafioTipo',
            'foreignKey' => array(
                'message' => 'O desafio não pode ser excluído porque ele possui pontuação lançada.'
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
                                'desafioPontuacao.pontuacao'));
        
        $desafios->leftjoin('Incentiv\Models\DesafioPontuacao', "Incentiv\Models\DesafioTipo.id = desafioPontuacao.desafioTipoId AND desafioPontuacao.empresaId = {$empresaId} AND desafioPontuacao.ativo = 'Y'", 'desafioPontuacao');
        
        $desafios->orderBy('Incentiv\Models\DesafioTipo.id');

        return $desafios->execute();
    }
}