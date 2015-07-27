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
    
    /**
     * Retorna o subdominio da url(para estilizar as páginas das empresas)
     * @param type $delimitador
     * @param type $dominio
     * @return type
     */
    function before ($delimitador, $dominio)
    {
        return substr($dominio, 0, strpos($dominio, $delimitador));
    }
    
    function after ($delimitador, $dominio)
    {
        if (!is_bool(strpos($dominio, $delimitador)))
        return substr($dominio, strpos($dominio,$delimitador)+strlen($delimitador));
    }
}