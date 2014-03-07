<?php
namespace Incentiv\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Password,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\StringLength,
    Phalcon\Validation\Validator\Confirmation;

class AlteraSenhaForm extends Form
{

    public function initialize()
    {
        // senha
        $password = new Password('password');

        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'Senha é obrigatória'
            )),
            new StringLength(array(
                'min' => 8,
                'messageMinimum' => 'Senha curta. Mínimo 8 caracteres'
            )),
            new Confirmation(array(
                'message' => 'A confirmação de senha é obrigatória',
                'with' => 'confirmPassword'
            ))
        ));

        $this->add($password);

        // Confirma senha
        $confirmPassword = new Password('confirmPassword');

        $confirmPassword->addValidators(array(
            new PresenceOf(array(
                'message' => 'A senha não está conferindo'
            ))
        ));

        $this->add($confirmPassword);
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
