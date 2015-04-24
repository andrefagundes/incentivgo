<?php
namespace Publico\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\Confirmation;

class CadastroUsuarioForm extends Form
{
    public function initialize()
    {
        $this->id = 'cadastro';
        $nome = new Text('nome', array(
            'placeholder'   => 'Informe o seu nome',
            'class'         => 'required form-control',
        ));

        $nome->addValidators(array(
            new PresenceOf(array(
                'message' => 'O nome é obrigatório'
            ))
        ));

        $this->add($nome);

        // Email
        $email = new Text('email', array(
            'placeholder'   => 'Informe seu e-email',
            'class'         => 'required form-control',
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

        // senha
        $password = new Password('senha', array(
            'placeholder'   => 'Informe sua senha',
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'A senha é obrigatória'
            ))
        ));

        $this->add($password);
        
        // Confirma Matrícula
        $confirmSenha = new Password('confirmSenha', array(
            'placeholder'   => 'Confirme sua senha',
            'class'         => 'required form-control',
            'required'      => ''
        ));
        
        $confirmSenha->addValidators(array(
            new PresenceOf(array(
                'message' => 'A confirmação de senha é obrigatória'
            )),
            new Confirmation(array(
                'message' => 'A senha não confere',
                'with' => 'senha'
            ))
        ));

        $this->add($confirmSenha);

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
