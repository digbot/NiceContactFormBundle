<?php

namespace Cadrone\NiceContactFormBundle\Util;

use Symfony\Component\Form\Form;

class SubjectResolver
{

    protected $subjects;

    public function __construct($subjects)
    {
        $this->subjects = $subjects;
    }

    public function getSubject(Form $form)
    {
        $data = $form->getData();
        $fieldType = $form["subject"]->getConfig()->getType()->getName();

        switch ($fieldType) {
            case "text":
                $subject = $data["subject"];
                break;
            case "choice":
                $subject = $this->subjects[$data["subject"]];
                break;
        }

        return $subject;
    }

}
