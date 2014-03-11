<?php
namespace Publico\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\textArea,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email;

class ContatoForm extends Form
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
        
        // Mensagem
        $description = new textArea('description', array(
            'placeholder'   => 'Informe sua mensagem',
            'class'         => 'required form-control',
            "rows"          => 6,
            'required'      => ''
        ));

        $description->addValidators(array(
            new PresenceOf(array(
                'message' => 'A mensagem é obrigatória'
            ))
        ));

        $this->add($description);

        // Sign Up
        $this->add(new Submit('go', array(
            'class' => 'btn btn-fancy squared',
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
