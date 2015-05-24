<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * Permissao
 * Armazena as permissões por perfil
 */
class Permissao extends Model
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
    public $perfilId;

    /**
     *
     * @var string
     */
    public $resource;

    /**
     *
     * @var string
     */
    public $action;

    public function initialize()
    {
        $this->belongsTo('perfilId', 'Incentiv\Models\Perfil', 'id', array(
            'alias' => 'perfil'
        ));
    }
}