<?php
namespace Publico\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Check,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\Identical,
    Phalcon\Validation\Validator\Confirmation;

use Incentiv\Models\Instituicao;

class CadastroForm extends Form
{

    public function initialize()
    {
        $select = new Select('instituicaoId', Instituicao::find('ativo = "S"'), array(
            'using' => array(
                'id',
                'nome'
            ),
            'useEmpty'      => true,
            'emptyText'     => '--Selecionar Instituição--',
            'emptyValue'    => '',
            'class'         => 'required form-control',
            'required'      => ''
        ));
        
         $select->addValidators(array(
            new PresenceOf(array(
                'message' => 'A instituição é obrigatória'
            ))
        ));
         
        $this->add($select);
        
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

        // matricula
        $password = new Password('matricula', array(
            'placeholder'   => 'Informe sua matrícula',
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $password->addValidators(array(
            new PresenceOf(array(
                'message' => 'A matrícula é obrigatória'
            )),
            new Confirmation(array(
                'message' => 'A matrícula não está conferindo',
                'with' => 'confirmMatricula'
            ))
        ));

        $this->add($password);

        // Confirma Matrícula
        $confirmMatricula = new Password('confirmMatricula', array(
            'placeholder'   => 'Confirme sua matrícula',
            'class'         => 'required form-control',
            'required'      => ''
        ));

        $confirmMatricula->addValidators(array(
            new PresenceOf(array(
                'message' => 'A confirmação de senha é obrigatória'
            ))
        ));

        $this->add($confirmMatricula);

        // Remember
        $terms = new Check('terms', array(
            'value' => 'yes'
        ));

        $terms->setLabel('Aceite os termos e condições');

        $terms->addValidator(new Identical(array(
            'value'     => 'yes',
            'message'   => 'Termos e condições devem ser aceitos'
        )));

        $this->add($terms);
        
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
