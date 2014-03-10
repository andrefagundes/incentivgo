<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model,
    Phalcon\Mvc\Model\Validator\Uniqueness,
    Phalcon\Mvc\Model\Behavior\SoftDelete;

/**
 * Incentiv\Models\Usuario
 * Todos os usuários registrados na aplicação
 */
class Usuario extends Model
{
    const DELETED       = 'N';

    const NOT_DELETED   = 'Y';
    
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
    public $matricula;

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
    public $instituicaoId;

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
    public function afterSave()
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
     * Validar que e-mails e matrículas são únicos entre os usuários 
     */
    public function validation()
    {
        $this->validate(new Uniqueness(array(
            "field"     => "email",
            "message"   => "O e-mail já está registrado"
        )));
        
        $this->validate(new Uniqueness(array(
            "field"     => "email",
            "message"   => "Esta matrícula já está registrada"
        )));

        return $this->validationHasFailed() != true;
    }

    public function initialize()
    {
        $this->belongsTo('perfilId', 'Incentiv\Models\Perfil', 'id', array(
            'alias' => 'perfil',
            'reusable' => true
        ));
        
        $this->belongsTo('instituicaoId', 'Incentiv\Models\Instituicao', 'id', array(
            'alias' => 'instituicao',
            'reusable' => true
        ));

        $this->hasMany('id', 'Incentiv\Models\SucessoLogin', 'usuarioId', array(
            'alias' => 'sucessoLogin',
            'foreignKey' => array(
                'message' => 'O usuário não pode ser excluído porque ele tem atividade no sistema'
            )
        ));

        $this->hasMany('id', 'Incentiv\Models\AlteracaoSenha', 'usuarioId', array(
            'alias' => 'passwordChanges',
            'foreignKey' => array(
                'message' => 'O usuário não pode ser excluído porque ele tem atividade no sistema'
            )
        ));

        $this->hasMany('id', 'Incentiv\Models\AlteraSenha', 'usuarioId', array(
            'alias' => 'alteraSenha',
            'foreignKey' => array(
                'message' => 'O usuário não pode ser excluído porque ele tem atividade no sistema'
            )
        ));
        
        $this->addBehavior(new SoftDelete(
            array(
                'field' => 'ativo',
                'value' => Usuario::DELETED
            )
        ));
    }
}