<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * SucessoLogin
 * Este modelo registra logins com sucesso que os usuários registrados fizeram
 */
class SucessoLogin extends Model
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

    public function initialize()
    {
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'user'
        ));
    }
}
