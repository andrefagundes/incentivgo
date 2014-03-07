<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * AlteracaoSenha
 * Registra quando um usuário muda sua senha
 */
class AlteracaoSenha extends Model
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
    public $ipAddress;

    /**
     *
     * @var string
     */
    public $userAgent;

    /**
     *
     * @var integer
     */
    public $criacaoDt;

    /**
     * Seta a ata que o usuário mudou a senha
     */
    public function beforeValidationOnCreate()
    {
        // Seta a data de confirmação
        $this->criacaoDt = time();
    }

    public function initialize()
    {
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'user'
        ));
    }
}
