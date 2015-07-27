<?php
namespace Publico\Forms;
    
use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Identical;

class CorporationForm extends Form
{

    public function initialize()
    {
        //empresa
        $empresa = new Text('empresa', array(
            'placeholder'   => 'Informe sua empresa...',
            'class'         => 'required multi',
            'style'         => 'width:100%;',
            'required'      => ''
        ));

        $empresa->addValidators(array(
            new PresenceOf(array(
                'message' => 'A empresa é obrigatória'
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
            'Continuar'
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
