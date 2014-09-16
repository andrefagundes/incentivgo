<?php
namespace Publico\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email;

class EnviarSugestaoForm extends Form
{

    public function initialize()
    {
        $nome = new Text('nome', array(
            'placeholder'   => 'Informe seu nome',
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $nome->addValidators(array(
            new PresenceOf(array(
                'message' => 'O nome é obrigatório'
            ))
        ));

        $this->add($nome);
        
        $email = new Text('email', array(
            'placeholder'   => 'Informe seu e-mail',
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'O e-mail é obrigatório'
            )),
            new Email(array(
                'message' => 'O e-mail não é válido'
            ))
        ));

        $this->add($email);
        
        $email_empresa = new Text('email_empresa', array(
            'placeholder'   => 'Informe o e-mail da empresa ou gerente',
            'class'         => 'required form-control',
            'required'      => '',
            'email'         => ''
        ));

        $email_empresa->addValidators(array(
            new PresenceOf(array(
                'message' => 'O e-mail da empresa é obrigatório'
            )),
            new Email(array(
                'message' => 'O e-mail da empresa não é válido'
            ))
        ));

        $this->add($email_empresa);

        $this->add(new Submit('go', array(
            'class' => 'btn btn-syndicate squared form-control',
            'Enviar'
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
