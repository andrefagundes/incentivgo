<?php
namespace Publico\Forms;
    
use Phalcon\Forms\Form,
//    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Check,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\Identical;

//use Incentiv\Models\Empresa;

class LoginForm extends Form
{

    public function initialize()
    {
        $lang = $this->getDI()->getShared('lang');
//        $select = new Select('empresaId', Empresa::find("ativo = 'Y'"), array(
//            'using' => array(
//                'id',
//                'nome'
//            ),
//            'useEmpty'      => true,
//            'emptyText'     => '--Selecione sua empresa--',
//            'emptyValue'    => '',
//            'class'         => 'required form-control',
//            'required'      => ''
//        ));
        
//         $select->addValidators(array(
//            new PresenceOf(array(
//                'message' => 'A empresa é obrigatória'
//            ))
//        ));
         
//        $this->add($select);
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

        // Password
        $password = new Password('password', array(
            'placeholder'   => $lang['informe_sua_senha'],
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

        $remember->setLabel($lang['lembrar']);

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
