<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * FalhaLogin
 * Este modelo registra logins sem sucesso em que usuários registrados e não registrados fizeram
 */
class FalhaLogin extends Model
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
     * @var integer
     */
    public $tentativa;

    public function initialize()
    {
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'user'
        ));
    }
}
