<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\Uniqueness,
    Phalcon\Mvc\Model\Validator\Email,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\Usuario
 * Todos os usuários registrados na aplicação
 */
class Usuario extends Model
{
    const DELETED       = 'N';
    const NOT_DELETED   = 'Y';
    
    private static $_instance;
   
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
    public $senha;

    /**
     * @var string
     */
    public $stAlterarSenha;
        
    /**
     * @var integer
     */
    public $empresaId;
    
    /**
     * @var integer
     */
    public $criacaoDt;

    /**
     * @var integer
     */
    public $perfilId;
    
    /**
     * @var string
     */
    public $suspenso;
    
    /**
     * @var string
     */
    public $banido;
    
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

    /**
     * Antes de criar o usuário atribui uma senha
     */
    public function beforeValidationOnCreate()
    {
        if (empty($this->senha)) {

            //Gerar uma senha temporária 
            $tempSenha = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));

            //O usuário deve alterar a senha no primeiro login
            $this->stAlterarSenha = 'Y';

            //Use esta senha como padrão 
            $this->senha = $this->getDI()
                ->getSecurity()
                ->hash($tempSenha);
        } else {
            //O usuário não deve alterar sua senha no primeiro login 
            $this->stAlterarSenha = 'N';
        }
        
        $this->criacaoDt = time();

        //A conta deve ser confirmada via e-mail
        $this->ativo = 'N';
        
        
        // A conta não está suspensa por padrão
        $this->suspenso = 'N';

        // A conta não é proibida por padrão
        $this->banido   = 'N';
    }

    /**
     * Envie um e-mail para o usuário se a conta não está ativa
     */
    public function afterCreate()
    {
        if ($this->ativo == 'N') {

            $emailConfirmacao = new EmailConfirmacao();

            $emailConfirmacao->usuarioId = $this->id;

            if ($emailConfirmacao->save()) {
                $this->getDI()
                    ->getFlash()
                    ->notice('Um e-mail de confirmação foi enviado para ' . $this->email);
            }
        }
    }

    /**
     * Validar que e-mails são únicos entre os usuários 
     */
    public function validation()
    {
        $this->validate(new Email(array(
            "field"     => "email",
            "message"   => "O e-mail é inválido!!!"
        )));
        
        $this->validate(new Uniqueness(array(
            "field"     => "email",
            "message"   => "O e-mail já está registrado!!!"
        )));

        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
        $this->belongsTo('perfilId', 'Incentiv\Models\Perfil', 'id', array(
            'alias' => 'perfil',
            'reusable' => true
        ));
        
        $this->belongsTo('empresaId', 'Incentiv\Models\Empresa', 'id', array(
            'alias' => 'empresa',
            'reusable' => true
        ));

        $this->hasMany('id', 'Incentiv\Models\SucessoLogin', 'usuarioId', array(
            'alias' => 'sucessoLogin',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele tem atividade no sistema'
            )
        ));

        $this->hasMany('id', 'Incentiv\Models\AlteracaoSenha', 'usuarioId', array(
            'alias' => 'passwordChanges',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele tem atividade no sistema'
            )
        ));

        $this->hasMany('id', 'Incentiv\Models\AlteraSenha', 'usuarioId', array(
            'alias' => 'alteraSenha',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele tem atividade no sistema'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\DesafioUsuario', 'usuarioId', array(
            'alias' => 'desafioUsuario',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui desafios.'
            )
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => Usuario::DELETED
            )
        ));
    }
    
    public function fetchAllUsuarios(\stdClass $objUsuario){
        
        $usuario = Usuario::query()->columns(array('id','nome','email', 'status' => 'ativo'));
        
        if($objUsuario->perfil)
        {
            $usuario->where("perfilId = {$objUsuario->perfil}");
        }
        
        if($objUsuario->filter)
        {
           $usuario->andwhere( "nome LIKE('%{$objUsuario->filter}%') OR 
                                email LIKE ('%{$objUsuario->filter}%')");
        }
        if(isset($objUsuario->ativo) && $objUsuario->ativo != 'T' )
        {
            $usuario->andwhere("ativo = '{$objUsuario->ativo}'");
        }

        $usuario->order('nome');
   
        return $usuario->execute();
    }
    
    public function fetchAllUsuariosDesafio(\stdClass $objUsuario){
        
        $usuario = Usuario::query()->columns(array('id','nome','email'));
        if($objUsuario->perfil)
        {
            $usuario->where("perfilId = {$objUsuario->perfil}");
        }
        if($objUsuario->filter)
        {
           $usuario->andwhere( "nome LIKE('%{$objUsuario->filter}%') OR 
                                email LIKE ('%{$objUsuario->filter}%')");
        }
        if($objUsuario->colaboradores)
        {
            $usuario->andwhere("id IN ({$objUsuario->colaboradores})");
        }
        if(isset($objUsuario->ativo) && $objUsuario->ativo != 'T' )
        {
            $usuario->andwhere("ativo = '{$objUsuario->ativo}'");
        }
  
        $usuario->order('nome');

        return $usuario->execute()->toArray();
    }
    
    public function salvarUsuario(\stdClass $dados){

        try {
            
        if($dados->id){
           $usuario = $this->findFirst("id = ".$dados->id);
        }else{
           $usuario = $this;
        }

        $usuario->assign(array(
            'empresaId'     => 1,
            'perfilId'      => $dados->perfilId,
            'nome'          => $dados->nome,
            'email'         => $dados->email
        ));
        
        if (!$usuario->save()) {

            foreach ($this->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            
            return array('status' => 'error', 'message'=> $message );
        }

        return array('status' => 'ok','message'=>'Colaborador salvo com sucesso!!!');
        
        } catch (Exception $e) {
            echo $e->getTraceAsString();
        }
    }
    
    public function ativarInativarUsuario(\stdClass $dados){

        $colaborador = $this->findFirst("id = ".$dados->id);
        
        $colaborador->assign(array(
            'ativo'         => $dados->status
        ));

        if (!$colaborador->save()) {
            foreach ($colaborador->getMessages() as $mensagem) {
              $message =  $mensagem;
              break;
            }
            return array('status' => 'error', 'message' => $message);
        } else {
            if($dados->status == 'N'){
                return array('status' => 'ok', 'message' => 'Colaborador inativado com sucesso!!!');
            }else{
                return array('status' => 'ok', 'message' => 'Colaborador ativado com sucesso!!!');
            }  
        }
    }
}