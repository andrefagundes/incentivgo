<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * AlteraSenha
 * Armazena os códigos de redefinição de senha e sua evolução
 */
class AlteraSenha extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $usuarioId;

    /**
     *
     * @var string
     */
    public $codigo;

    /**
     *
     * @var integer
     */
    public $criacaoDt;

    /**
     *
     * @var integer
     */
    public $modificacaoDt;

    /**
     *
     * @var string
     */
    public $reset;

    /**
     * Antes de criar o usuário atribui uma senha
     */
    public function beforeValidationOnCreate()
    {
        // data de confirmação
        $this->criacaoDt = time();

        // Gere um código de confirmação aleatório
        $this->codigo = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        // Seta o estado para não confirmado
        $this->reset = 'N';
    }

    /**
     * Define a data e hora antes de atualizar a confirmação
     */
    public function beforeValidationOnUpdate()
    {
        // Seta a data e hora antes de atualizar a confirmação
        $this->modificacaoDt = time();
    }

    /**
     * Envia um e-mail para os usuários, permitindo redefinir sua senha
     */
    public function afterCreate()
    {
        $lang = $this->getDI()->getShared('lang');
        $this->getDI()
            ->getMail()
            ->send(array(
            $this->user->nome => $this->user->email
        ), $lang['redefinir_senha'], 'reset', array(
            'resetUrl' => '/altera-senha/' . $this->codigo . '/' . $this->user->email,
            'txt_redefinir_senha' => $lang['txt_redefinir_senha'],
            'redefinir' => $lang['redefinir'],
            'redefinir_sua_senha' => $lang['redefinir_sua_senha'],
        ));
    }

    public function initialize()
    {
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'user'
        ));
    }
}