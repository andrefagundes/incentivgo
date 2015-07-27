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
    public $subdominio;
    
    /**
     * @var string
     */
    public $logo;

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
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPedidoRecompensa', 'empresaId', array(
            'alias' => 'pedidoRecompensa',
            'foreignKey' => array(
                'message' => 'A  empresa não pode ser excluída porque ela possui usuario com pedido de recompensa.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\EmpresaDominio', 'empresaId', array(
            'alias' => 'empresaDominio',
            'foreignKey' => array(
                'message' => 'A  empresa não pode ser excluída porque ela possui domínio cadastrado.'
            )
        ));
    }
    
    public function salvarEmpresaPerfil(\stdClass $dados){

        try {
            
            if($dados->dados['id']){
               $empresa = $this->findFirst("id = ".$dados->dados['id']);
            }else{
               $empresa = $this;
            }
            
            if(isset($dados->dados['logo'])){
                $empresa->logo = $dados->dados['logo'];
            }

            $empresa->assign(array(
                'nome'          => $dados->dados['nome']
            ));

            if (!$empresa->save()) {

                foreach ($this->getMessages() as $mensagem) {
                  $message =  $mensagem;
                  break;
                }

                return array('status' => 'error', 'message'=> $message );
            }

            return array('status' => 'ok','message'=>'Perfil da empresa salvo com sucesso!!!');
        
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    
    public function findEmpresa(\stdClass $objEmpresa){

        $empresa = $this::query()->columns(array('id','subdominio','nome'));
        
        if($objEmpresa->filter)
        {
           $empresa->andwhere( "nome LIKE('%{$objEmpresa->filter}%')");
        }
       
        $empresa->andwhere("ativo = 'Y'");
  
        $empresa->orderBy('nome');

        return $empresa->execute()->toArray();
    }
}