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
       $lang = $this->getDI()->getShared('lang');

        $nome = new Text('nome', array(
            'placeholder'   => $lang['informe_seu_nome'],
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $nome->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['nome_obrigatorio']
            ))
        ));

        $this->add($nome);

        // Email
        $email = new Text('email', array(
            'placeholder'   => $lang['informe_seu_email'],
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['email_obrigatorio']
            )),
            new Email(array(
                'message' => $lang['email_nao_valido']
            ))
        ));
        
        $this->add($email);
        
        // Mensagem
        $description = new textArea('description', array(
            'placeholder'   => $lang['informe_sua_mensagem'],
            'class'         => 'required form-control',
            "rows"          => 6,
            'required'      => ''
        ));

        $description->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['mensagem_obrigatoria']
            ))
        ));

        $this->add($description);

        // Sign Up
        $this->add(new Submit('go', array(
            'class' => 'btn btn-fancy squared',
            $lang['enviar']
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
