<?php
/**
 * Incentiv
 * Plataforma online de incentivo para colaboradores de empresas
 *
 * Componente para envio de email
 *
 * @package     Incentiv
 * @category    Empresas
 * @name        Mail.php
 * @author      André Maciel Fagundes <amfcom@gmail.com>
 * @copyright   Â© 2014 - Incentiv - All Rights Reserved
 * @link        ...
 * @version     1.0.0 
 * @since       2014-03-05
 */

namespace Incentiv\Mail;

use Phalcon\Mvc\User\Component,
    Phalcon\Mvc\View;

//use Swift_Message as Message,
//    Swift_SmtpTransport as Smtp;

/**
 * Autoloader classe Swift
 */
//require_once __APP_ROOT__ . '/vendor/Swift/swift_required.php';
require_once __APP_ROOT__ . '/vendor/sendgrid-php/sendgrid-php.php';

/**
 * Incentiv\Mail\Mail
 * Envia email baseado em templates pre definidas
 */
class Mail extends Component
{

    protected $transport;

    protected $amazonSes;

    protected $directSmtp = true;

    /**
     * Envie um e-mail através de matéria-AmazonSES
     *
     * @param string $raw
     */
    private function amazonSESSend($raw)
    {
        if ($this->amazonSes == null) {
            $this->amazonSes = new \AmazonSES(
                $this->config->amazon->AWSAccessKeyId,
                $this->config->amazon->AWSSecretKey
            );
            $this->amazonSes->disable_ssl_verification();
        }

        $response = $this->amazonSes->send_raw_email(array(
            'Data' => base64_encode($raw)
        ), array(
            'curlopts' => array(
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            )
        ));

        if (!$response->isOK()) {
            throw new Exception('Error sending email from AWS SES: ' . $response->body->asXML());
        }

        return true;
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

//    /**
//     * Envia e-mails via AmazonSES com base em modelos predefinidos
//     *
//     * @param array $to
//     * @param string $subject
//     * @param string $nome
//     * @param array $params
//     */
//    public function send($to, $subject, $nome, $params)
//    {
//
//        // Settings
//        $mailSettings = $this->config->mail;
//
//        $template = $this->getTemplate($nome, $params);
//
//        // Create the message
//        $message = Message::newInstance()
//            ->setSubject($subject)
//            ->setTo($to)
//            ->setFrom(array(
//                $mailSettings->fromEmail => $mailSettings->fromName
//            ))
//            ->setBody($template, 'text/html');
//
//        if ($this->directSmtp) {
//
//            if (!$this->transport) {
//                $this->transport = Smtp::newInstance(
//                    $mailSettings->smtp->server,
//                    $mailSettings->smtp->port,
//                    $mailSettings->smtp->security
//                )
//                ->setUsername($mailSettings->smtp->username)
//                ->setPassword($mailSettings->smtp->password);
//            }
//
//            // Create the Mailer using your created Transport
//            $mailer = \Swift_Mailer::newInstance($this->transport);
//
//            return $mailer->send($message);
//        } else {
//            return $this->amazonSESSend($message->toString());
//        }
//    }
}