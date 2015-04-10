<?php
namespace Incentiv\Funcoes;

use Phalcon\Mvc\User\Component,
    Phalcon\Mvc\Model\Query\Builder;

Class MySqlBuilder extends Component
{
       public function getParams()
       {
           return $this->_bindParams;
       }
       public function getSQL($mensagem)
       {
            $queryS = $mensagem->getPhql();
            $queryS = str_replace(array("[", "]"), "", $queryS);
            $queryS = str_replace(":,", ",", $queryS);
            $queryS = str_replace(":)", ")", $queryS);
            return $queryS; 
        }
}