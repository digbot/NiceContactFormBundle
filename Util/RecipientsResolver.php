<?php

namespace Cadrone\NiceContactFormBundle\Util;

use Symfony\Component\Form\Form;

class RecipientsResolver
{

    protected $subjectFieldType;
    protected $recipients;
    protected $subjects;

    public function __construct($subjectFieldType, $recipients, $subjects)
    {
        $this->subjectFieldType = $subjectFieldType;
        $this->recipients = $recipients;
        $this->subjects = $subjects;
    }

    public function getRecipient(Form $form)
    {
        switch ($this->subjectFieldType) {
            case "text":
                $recipients = explode(",", $this->recipients);
                break;
            case "dropdown":
                $data = $form->getData();
                $recipients = explode(",", $data["subject"]);
                break;
        }

        return $recipients;
    }

}
