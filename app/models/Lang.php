<?php
namespace Incentiv\Models;

use Phalcon\Mvc\Model;

/**
 * Incentiv\Models\Lang
 * Todas as metas registradas na aplicação
 */
class Lang extends Model
{  
    private static $_instance;
   
    /**
     * @var integer
     */
    public $id;
     
    /**
     * @var char
     */
    public $ipaddress;  
    
    /**
     * @var integer
     */
    public $lang;   
    
    public static function build()
    {
        if( !isset( self::$_instance ) )
        {
            self::$_instance = new self();
        }

        return self::$_instance;
    }  
    
    public function inserirAlterarLang($ipaddress,$lang){
        try {
 
            $addressLang = $this->findFirst("ipaddress = '{$ipaddress}'");
        
            if(!$addressLang){
                $addressLang = $this;
            }
            
            $addressLang->assign(array(
                'lang'         => $lang,
                'ipaddress'    => $ipaddress
            ));

            $addressLang->save();
            
        } catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
    }
}