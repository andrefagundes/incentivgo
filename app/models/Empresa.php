<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * Incentiv\Models\Empresa
 * Todos os usuários registrados na aplicação
 */
class Empresa extends Model
{
    public static $_instance;
   
    /**
     * @var integer
     */
    public $id;

    /**
     * @var string
     */
    public $nome;

    /**
     * @var string
     */
    public $email;
    
    /**
     * @var string
     */
    public $telefone;

    /**
     * @var string
     */
    public $ativo;
    
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
        $this->hasMany('id', 'Incentiv\Models\Usuario', 'empresaId', array(
            'alias'         => 'empresaId',
            'foreignKey'    => array(
               'message'    => 'A  empresa não pode ser excluída porque ela tem usuarios no sistema'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\Ideia', 'empresaId', array(
            'alias'         => 'empresaIdeiasId',
            'foreignKey'    => array(
               'message'    => 'A  empresa não pode ser excluída porque ela tem ideia de usuarios no sistema'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\Noticia', 'empresaId', array(
            'alias'         => 'empresaNoticiasId',
            'foreignKey'    => array(
               'message'    => 'A  empresa não pode ser excluída porque ela tem notícias no sistema'
            )
        ));
    }
}