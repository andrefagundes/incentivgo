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
        
        $this->hasMany('id', 'Incentiv\Models\PontuacaoIdeia', 'empresaId', array(
            'alias'         => 'empresaPontuacaoIdeiaId',
            'foreignKey'    => array(
               'message'    => 'A  empresa não pode ser excluída porque ela tem pontuações de ideias no sistema'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPontuacaoCredito', 'empresaId', array(
            'alias' => 'creditoUsuario',
            'foreignKey' => array(
                'message' => 'A  empresa não pode ser excluída porque ela possui usuario com credito cadastrado.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPontuacaoDebito', 'empresaId', array(
            'alias' => 'debitoUsuario',
            'foreignKey' => array(
                'message' => 'A  empresa não pode ser excluída porque ela possui usuario com débito cadastrado.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\EmpresaDominio', 'empresaId', array(
            'alias' => 'empresaDominio',
            'foreignKey' => array(
                'message' => 'A  empresa não pode ser excluída porque ela possui domínio cadastrado.'
            )
        ));
    }
}