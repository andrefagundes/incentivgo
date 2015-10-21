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
        $lang = $this->getDI()->getShared('lang');
        
        $this->id = 'cadastro';
        $nome = new Text('nome', array(
            'placeholder'   => $lang['informe_nome_empresa'],
            'class'         => 'required form-control',
        ));

        $nome->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['nome_obrigatorio']
            ))
        ));

        $this->add($nome);

        // Email
        $email = new Text('email', array(
            'placeholder'   => $lang['informe_email_contato'],
            'class'         => 'required form-control',
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

        // telefone
        $telefone = new Text('telefone', array(
            'placeholder'   => $lang['informe_telefone_contato'],
            'class'         => 'required form-control',
            'id'         => 'telefone',
        ));

        $telefone->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['telefone_obrigatorio']
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
            $lang['enviar_pre_cadastro']
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
