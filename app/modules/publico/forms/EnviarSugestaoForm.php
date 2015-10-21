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
        $lang = $this->getDI()->getShared('lang');
        
        $nome = new Text('nome', array(
            'placeholder'   => $lang['informe_seu_nome'],
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $nome->addValidators(array(
            new PresenceOf(array(
                'message' =>  $lang['nome_obrigatorio']
            ))
        ));

        $this->add($nome);
        
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
        
        $email_empresa = new Text('email_empresa', array(
            'placeholder'   => $lang['informe_email_empresa_gerente'],
            'class'         => 'required form-control',
            'required'      => '',
            'email'         => ''
        ));

        $email_empresa->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['email_obrigatorio']
            )),
            new Email(array(
                'message' => $lang['email_nao_valido']
            ))
        ));

        $this->add($email_empresa);

        $this->add(new Submit('go', array(
            'class' => 'btn btn-syndicate squared form-control',
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
