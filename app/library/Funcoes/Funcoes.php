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
    
    public function formatarDataEn($data){
      $dataArray = explode("/", trim($data));
      $rData = implode("-", array($dataArray[2],$dataArray[0],$dataArray[1]));
      return $rData;
    }
    
    public function formatarDataSaida($data,$lang){
        return ($lang == 'pt-BR')?date('d/m/Y',strtotime($data)):date('m/d/Y',strtotime($data));
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