<?php
namespace Admin\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Select,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email;

use Incentiv\Models\Perfil;

class UsuarioForm extends Form
{

    public function initialize($entity = null, $options = null)
    {

        // Na edição o id está escondido
        if (isset($options['edit']) && $options['edit']) {
            $id = new Hidden('id');
        } else {
            $id = new Text('id');
        }

        $this->add($id);

        $nome = new Text('nome', array(
            'placeholder' => 'Nome'
        ));

        $nome->addValidators(array(
            new PresenceOf(array(
                'message' => 'O nome é obrigatório'
            ))
        ));

        $this->add($nome);

        $email = new Text('email', array(
            'placeholder' => 'E-mail'
        ));

        $email->addValidators(array(
            new PresenceOf(array(
                'message' => 'O e-mail é obrigatório'
            )),
            new Email(array(
                'message' => 'O e-mail é inválido'
            ))
        ));

        $this->add($email);

        $this->add(new Select('perfilId', Perfil::find('ativo = "Y"'), array(
            'using' => array(
                'id',
                'nome'
            ),
            'useEmpty' => true,
            'emptyText' => '...',
            'emptyValue' => ''
        )));

        $this->add(new Select('banido', array(
            'Y' => 'Sim',
            'N' => 'Não'
        )));

        $this->add(new Select('suspenso', array(
            'Y' => 'Sim',
            'N' => 'Não'
        )));

        $this->add(new Select('ativo', array(
            'Y' => 'Sim',
            'N' => 'Não'
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
