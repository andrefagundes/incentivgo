<?php
namespace Publico\Forms;
    
use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical;

class CorporationForm extends Form
{

    public function initialize()
    {
        $lang = $this->getDI()->getShared('lang');
        //empresa
        $empresa = new Select('empresa',array(), array(
            'useEmpty'      => true,
            'class'         => 'required',
            'style'         => 'width:100%;',
            'lang'          => 'pt-BR',
            'required'      => '',
        ));

        $empresa->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['empresa_obrigatoria']
            ))
        ));

        $this->add($empresa);

        // CSRF
        $csrf = new Hidden('csrf',array(
            'value' => $this->security->getToken(),
            'name' => $this->security->getTokenKey()  
        ));

        $csrf->addValidator(new Identical(array(
            'value' => $this->security->checkToken(),
            'message' => 'CSRF validation failed'
        )));

        $this->add($csrf);

        $this->add(new Submit('go', array(
            'class' => 'btn btn-syndicate squared form-control',
            $lang['continuar'],
        )));
    }
    
    /**
     * Prints messages for a specific element
     */
    public function messages($nome)
    {
        if ($this->hasMessagesFor($nome)) {
            foreach ($this->getMessagesFor($nome) as $message) {
                $this->flash->error($message);
            }
        }
    }
}
