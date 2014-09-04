<?php

namespace Cadrone\NiceContactFormBundle\Processor;

use Symfony\Component\Form\Form;
use Symfony\Component\DependencyInjection\ContainerInterface;

class FormHandler
{

    protected $processors;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    public function __construct($processors, ContainerInterface $container)
    {
        $this->processors = $processors;
        $this->container = $container;
    }

    public function handle(Form $form)
    {
        foreach ($this->processors as $processorName) {


            if ($this->container->has($processorName)) {
                $processor = $this->container->get($processorName);
            } else {
                $processor = new $processorName();
            }

            if ($processor instanceof FormProcessorInterface) {
                $processor->process($form);
            } else {
                //throw invalid processor exception
            }
        }
    }

}
