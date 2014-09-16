<?php
namespace Publico\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Hidden,
//    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
//    Phalcon\Forms\Element\Check,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\Identical;
//    Phalcon\Validation\Validator\Confirmation;

class CadastroForm extends Form
{
    public function initialize()
    {
        
        $nome = new Text('nome', array(
            'placeholder'   => 'Informe o nome da empresa',
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $nome->addValidators(array(
            new PresenceOf(array(
                'message' => 'O nome é obrigatório'
            ))
        ));

        $this->add($nome);

        // Email
        $email = new Text('email', array(
            'placeholder'   => 'Informe um e-mail de contato',
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

        // telefone
        $telefone = new Text('telefone', array(
            'placeholder'   => 'Informe o telefone de contato',
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $telefone->addValidators(array(
            new PresenceOf(array(
                'message' => 'O telefone é obrigatório'
            ))  
        ));

        $this->add($telefone);
        
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

        // Sign Up
        $this->add(new Submit('go', array(
            'class' => 'btn btn-syndicate squared form-control',
            'Cadastrar'
        )));
    }

    /**
     * Prints messages for a specific element
     */
    public function messages($name)
    {
        if ($this->hasMessagesFor($name)) {
            foreach ($this->getMessagesFor($name) as $message) {
                $this->flash->error($message);
            }
        }
    }
}
