<?php
namespace Incentiv\Funcoes;

use Phalcon\Mvc\User\Component;

/**
 * Incentiv\Funcoes\Funcoes
 */
class Funcoes extends Component
{
    public function formatarData($data){
      $rData = implode("-", array_reverse(explode("/", trim($data))));
      return $rData;
    }
}