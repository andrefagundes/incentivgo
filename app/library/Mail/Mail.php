<?php
/**
 * Incentiv Go
 * Plataforma online de incentivo para colaboradores de empresas
 *
 * Componente para envio de email
 *
 * @package     incentiv
 * @category    Empresas
 * @name        Mail.php
 * @author      André Maciel Fagundes <amfcom@gmail.com>
 * @copyright   Â© 2015 - Incentiv - All Rights Reserved
 * @link        ...
 * @version     1.0.0 
 * @since       2015-03-05
 */

namespace Incentiv\Mail;

use Phalcon\Mvc\User\Component,
    Phalcon\Mvc\View;

/**
 * Autoloader classe sendgrid
 */
require_once __APP_ROOT__ . '/vendor/sendgrid-php/sendgrid-php.php';

/**
 * Incentiv\Mail\Mail
 * Envia email baseado em templates pre definidas
 */
class Mail extends Component
{
    
    public function send($to, $subject,$nome,$params){

        // Settings
        $mailSettings = $this->config->mail;
        $template = $this->getTemplate($nome, $params);

        $sendgrid = new \SendGrid('amfcom','mfcom5841');
 
        $email = new \SendGrid\Email();
        $email
            ->addTo($to)
            ->setFrom($mailSettings->fromEmail)
            ->setSubject($subject)
            ->setHtml($template);

        try {
            $sendgrid->send($email);
        } catch(\SendGrid\Exception $e) {
            echo $e->getCode();
            foreach($e->getErrors() as $er) {
                echo $er;
            }
        }
    }
    
    /**
     * Aplica-se um modelo a ser usado no e-mail
     *
     * @param string $nome
     * @param array $params
     */
    public function getTemplate($nome, $params)
    {
        $parameters = array_merge(array(
            'publicUrl' => $this->config->application->publicUrl
        ), $params);

        return $this->view->getRender('emailTemplates', $nome, $parameters, function ($view) {
            $view->setRenderLevel(View::LEVEL_LAYOUT);
        });

        return $view->getContent();
    }
}