<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * EmailConfirmacao
 * Armazena os códigos de redefinição de senha e sua evolução
 */
class EmailConfirmacao extends Model
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
     * @var integer
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

    public $confirmado;

    /**
     * Antes de criar o usuário atribuir uma senha
     */
    public function beforeValidationOnCreate()
    {
        // Data de confirmação
        $this->criacaoDt = time();

        // Gera um código de confirmação aleatório
        $this->codigo = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        // Seta estado para não confirmado
        $this->confirmado = 'N';
    }

    /**
     * Define a data e hora antes de atualizar a confirmação
     */
    public function beforeValidationOnUpdate()
    {
        // Data de confirmação
        $this->modificacaoDt = time();
    }

    /**
     * Envia um e-mail de confirmação para o usuário, após criar a conta
     */
    public function afterCreate()
    {
        $lang = $this->getDI()->getShared('lang');
        
        $this->getDI()
            ->getMail()
            ->send(array(
            $this->user->nome => $this->user->email
        ), $lang['favor_confirme_email'], 'confirmation', array(
            'confirmUrl' => '/confirm/' . $this->codigo . '/' . $this->user->email,
            'confirme_email' => $lang['confirme_email'],
            'txt_confirme_email' => $lang['txt_confirme_email'],
            'confirmar' => $lang['confirmar']
        ));
    }

    public function initialize()
    {
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'user'
        ));
    }
}
