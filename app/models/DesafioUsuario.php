<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * DesafioUsuario
 * Este modelo registra os usuários que participam do desafio lançado
 */
class DesafioUsuario extends Model
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
    public $desafioId;

    public function initialize()
    {
        $this->belongsTo('usuarioId', 'Incentiv\Models\Usuario', 'id', array(
            'alias' => 'usuario'
        ));
        $this->belongsTo('desafioId', 'Incentiv\Models\Desafio', 'id', array(
            'alias' => 'desafio'
        ));
    }
}
