<?php
namespace Publico\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\StringLength,
    Phalcon\Validation\Validator\Confirmation,
    Phalcon\Validation\Validator\Identical;

class AlteraSenhaForm extends Form
{

    public function initialize()
    {
        $lang = $this->getDI()->getShared('lang');
        
        // senha
        $password = new Password('senha',array('placeholder'   => $lang['informe_nova_senha'],'class' => 'required form-control'));

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

        // Confirma senha
        $confirmSenha = new Password('confirmSenha',array('placeholder'   => $lang['confirme_nova_senha'],'class' => 'required form-control'));

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
        
        $this->add(new Submit('go', array(
            'class' => 'btn btn-syndicate squared form-control',
            $lang['alterar_senha']
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
