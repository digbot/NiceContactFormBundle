<?php

namespace Cadrone\NiceContactFormBundle\Processor;

use Symfony\Component\Form\Form;
use Cadrone\NiceContactFormBundle\Util\RecipientsResolver;
use Cadrone\NiceContactFormBundle\Util\SubjectResolver;

class SendEmailFormProcessor implements FormProcessorInterface
{

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * @var \Cadrone\NiceContactFormBundle\Util\SubjectResolver
     */
    protected $subjectResolver;

    /**
     *
     */
    protected $recipients;

    /**
     *
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer, SubjectResolver $subjectResolver, $recipients)
    {
        $this->mailer = $mailer;
        $this->subjectResolver = $subjectResolver;
        $this->recipients = $recipients;
    }

    public function process(Form $form)
    {
        $data = $form->getData();

        $message = \Swift_Message::newInstance()
                ->setSubject($this->subjectResolver->getSubject($form))
                ->setContentType("text/plain")
                ->setBody($data["body"])
                ->addFrom($data["email"], $data["name"])
        ;

        //add recipients
        foreach ($this->recipients[$data["recipients"]]["recipients"] as $address) {
            $message->addTo($address);
        }

        $this->mailer->send($message);
    }

}
