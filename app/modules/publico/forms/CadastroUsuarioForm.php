<?php
namespace Publico\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\StringLength,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\Confirmation;

class CadastroUsuarioForm extends Form
{
    public function initialize()
    {
        $lang = $this->getDI()->getShared('lang');
        
        $this->id = 'cadastro';
        $nome = new Text('nome', array(
            'placeholder'   => $lang['informe_seu_nome'],
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
            'placeholder'   => $lang['informe_seu_email'],
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

        // senha
        $password = new Password('senha', array(
            'placeholder'   => $lang['informe_sua_senha'],
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $password->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['senha_obrigatoria']
            )),
            new StringLength(array(
                'min' => 6,
                'messageMinimum' => $lang['senha_curta']
            ))
        ));

        $this->add($password);
        
        // Confirma Matrícula
        $confirmSenha = new Password('confirmSenha', array(
            'placeholder'   => $lang['confirme_sua_senha'],
            'class'         => 'required form-control',
            'required'      => ''
        ));
        
        $confirmSenha->addValidators(array(
            new PresenceOf(array(
                'message' => $lang['confirmacao_obrigatoria']
            )),
            new Confirmation(array(
                'message' => $lang['senha_nao_confere'],
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
            $lang['cadastrar']
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
