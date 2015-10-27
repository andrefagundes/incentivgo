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
        
        $this->hasMany('id', 'Incentiv\Models\Ajuda', 'usuarioId', array(
            'alias' => 'ajudaUsuario',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui ajudas.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\Ideia', 'usuarioId', array(
            'alias' => 'ideiaUsuario',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui ideias.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\Desafio', 'usuarioResponsavelId', array(
            'alias' => 'responsavelUsuario',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui desafio como responsavel.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\Desafio', 'usuarioId', array(
            'alias' => 'desafioUsuarioCadastro',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui desafio cadastrado.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPontuacaoCredito', 'usuarioId', array(
            'alias' => 'creditoUsuario',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui crédito cadastrado.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPontuacaoDebito', 'usuarioId', array(
            'alias' => 'debitoUsuario',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui débito cadastrado.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\UsuarioPedidoRecompensa', 'usuarioId', array(
            'alias' => 'recompensaUsuario',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui recompensa cadastrada.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\Mensagem', 'remetenteId', array(
            'alias' => 'usuarioRemetente',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui mensagem como remetente.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\Mensagem', 'destinatarioId', array(
            'alias' => 'usuarioDestinatario',
            'foreignKey' => array(
                'message' => 'O colaborador não pode ser excluído porque ele possui mensagem como destinatário.'
            )
        ));
        
        $this->hasMany('id', 'Incentiv\Models\MensagemExcluida', 'usuarioId', array(
            'alias' => 'usuarioMensagemExcluida',
            'foreignKey' => array(
                'message' => 'O usuário não pode ser excluído porque ela possui exclusão na tabela mensagem_excluida.'
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
        
        $usuario = Usuario::query()->columns(array('Incentiv\Models\Usuario.id',
                                                    'Incentiv\Models\Usuario.nome',
                                                    'Incentiv\Models\Usuario.email',
                                                    'perfil' => 'perfil.nome', 
                                                    'status' => 'Incentiv\Models\Usuario.ativo'));
        
        $usuario->innerjoin('Incentiv\Models\Perfil', "Incentiv\Models\Usuario.perfilId = perfil.id", 'perfil');
        
        if($objUsuario->perfil)
        {
            $usuario->where("Incentiv\Models\Usuario.perfilId = {$objUsuario->perfil}");
        }
        
        if($objUsuario->filter)
        {
           $usuario->andwhere( "Incentiv\Models\Usuario.nome LIKE('%{$objUsuario->filter}%') OR 
                                Incentiv\Models\Usuario.email LIKE ('%{$objUsuario->filter}%')");
        }
        if(isset($objUsuario->ativo) && $objUsuario->ativo != 'T' )
        {
            $usuario->andwhere("Incentiv\Models\Usuario.ativo = '{$objUsuario->ativo}'");
        }
        
        $usuario->andwhere("Incentiv\Models\Usuario.empresaId = '{$objUsuario->empresaId}'");

        $usuario->orderBy('Incentiv\Models\Usuario.nome');
   
        return $usuario->execute();
    }
    
    public function fetchAllUsuariosDesafio(\stdClass $objUsuario){

        $usuario = Usuario::query()->columns(array('id','text'=>'nome'));
        
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
        
        $usuario->notInWhere('id', array($objUsuario->usuarioLogado));
  
        $usuario->orderBy('nome');

        return $usuario->execute()->toArray();
    }
    
    public function salvarUsuario(\stdClass $dados){

        try {
            $dominios = EmpresaDominio::build()->find(array("empresaId = {$dados->empresaId} AND status = 'Y'",'columns' =>'dominio'));

            foreach ($dominios as $dominio){
                $dominioUsuario = explode('@', $dados->email);
                if($dominioUsuario[1] != $dominio->dominio){
                    return array('status' => 'error', 'message'=> "E-mails autorizados somente com o final @{$dominio->dominio}" );
                }
            }
            
            if($dados->id){
               $usuario = $this->findFirst("id = ".$dados->id);
            }else{
               $usuario = $this;
            }

            $usuario->assign(array(
                'empresaId'     => $dados->empresaId,
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
    
    public function salvarUsuarioPerfil(\stdClass $dados){

        try {
            $funcoes = $this->getDI()->getShared('funcoes');
            
            if($dados->dados['id']){
               $usuario = $this->findFirst("id = ".$dados->dados['id']);
            }else{
               $usuario = $this;
            }
            
            if($dados->dados['dt_nascimento']){
               $nascimentoDt = $funcoes->formatarData($dados->dados['dt_nascimento']);
            }else{
                $nascimentoDt = null;
            }
            
            if(isset($dados->dados['avatar'])){
                $usuario->avatar = $dados->dados['avatar'];
            }

            $usuario->assign(array(
                'nome'          => $dados->dados['nome'],
                'email'         => $dados->dados['email'],
                'cargo'         => $dados->dados['cargo'],
                'nascimentoDt'  => $nascimentoDt
            ));

            if (!$usuario->save()) {

                foreach ($this->getMessages() as $mensagem) {
                  $message =  $mensagem;
                  break;
                }

                return array('status' => 'error', 'message'=> $message );
            }

            return array('status' => 'ok','message'=>'Perfil salvo com sucesso!!!');
        
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