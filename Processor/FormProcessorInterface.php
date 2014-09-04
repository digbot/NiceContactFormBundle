<?php

namespace Cadrone\NiceContactFormBundle\Processor;

interface FormProcessorInterface
{
    public function process(\Symfony\Component\Form\Form $form);
}
