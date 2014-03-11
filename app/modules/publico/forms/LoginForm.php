<?php
namespace Incentiv\Forms;
    
use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Check,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\Identical;

class LoginForm extends Form
{

    public function initialize()
    {
        // Email
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

        // Password
        $password = new Password('password', array(
            'placeholder'   => 'Informe sua senha',
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $password->addValidator(new PresenceOf(array(
            'message' => 'A senha é obrigatória'
        )));

        $this->add($password);

        // Remember
        $remember = new Check('remember', array(
            'value'         => 'no'
        ));

        $remember->setLabel('Lembrar');

        $this->add($remember);

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
            'Login'
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
