<?php

namespace Cadrone\NiceContactFormBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class NiceContactType extends AbstractType
{

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * @var array
     */
    protected $constraints;

    /**
     * @var array
     */
    protected $messages;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $labels = $this->container->getParameter("cadrone.nice_contact_form.labels");
        $captcha = $this->container->getParameter("cadrone.nice_contact_form.captcha");

        $builder->setAction($this->getFormAction());

        $builder->add("name", null, array(
            "required" => true,
            "label" => $labels["name"],
            "constraints" => $this->setConstraints("name"),
        ));

        $builder->add("email", null, array(
            "required" => true,
            "label" => $labels["email"],
            "constraints" => $this->setConstraints("email"),
        ));

        if ($this->container->getParameter("cadrone.nice_contact_form.recipients") > 0) {
            $builder->add("recipients", $this->getRecipientsFieldType(), $this->getRecipentsFieldConfig($labels));
        }

        $builder->add("subject", $this->getSubjectFieldType(), $this->getSubjectFieldConfig($labels));

        $builder->add("body", "textarea", array(
            "required" => true,
            "label" => $labels["body"],
            "constraints" => $this->setConstraints("body"),
        ));

        if (null !== $captcha) {
            $builder->add("captcha", $captcha, array(
                "label" => $labels["captcha"],
            ));
        }

        $builder->add("submit", "submit", array(
            "label" => $labels["submit"],
        ));
    }

    protected function getRecipientsFieldType()
    {
        if (count($this->container->getParameter("cadrone.nice_contact_form.recipients")) > 1) {
            return "choice";
        } else {
            return "hidden";
        }
    }

    protected function getRecipentsFieldConfig($labels)
    {
        if ("hidden" === $this->getRecipientsFieldType()) {
            $config = array(
                "data" => 0,
            );
        }

        if ("choice" === $this->getRecipientsFieldType()) {

            // todo: move out
            foreach ($this->container->getParameter("cadrone.nice_contact_form.recipients") as $k => $list) {
                $recipients[$k] = $list["title"];
            }

            $config = array(
                "choices" => $recipients
            );
        }

        return array_merge(
                array(
            "required" => true,
            "label" => $labels["recipients"],
            "constraints" => $this->setConstraints("recipients"),
                ), $config
        );
    }

    protected function getSubjectFieldType()
    {
        if (count($this->container->getParameter("cadrone.nice_contact_form.subjects")) > 0) {
            return "choice";
        } else {
            return "text";
        }
    }

    protected function getSubjectFieldConfig($labels)
    {
        if ("text" === $this->getSubjectFieldType()) {
            $config = array();
        }

        if ("choice" === $this->getSubjectFieldType()) {
            $config = array(
                "choices" => $this->container->getParameter("cadrone.nice_contact_form.subjects"),
            );
        }

        return array_merge(
                array(
            "required" => true,
            "label" => $labels["subject"],
            "constraints" => $this->setConstraints("subject"),
                ), $config
        );
    }

    protected function getFormAction()
    {
        $router = $this->container->get('router');
        $action = $this->container->getParameter("cadrone.nice_contact_form.action");

        if (null === $router->getRouteCollection()->get($action)) {
            return $action;
        } else {
            return $router->generate($action);
        }
    }

    protected function setConstraints($name)
    {
        if (null === $this->constraints) {
            $this->constraints = $this->container->getParameter("cadrone.nice_contact_form.constraints");
        }

        if (null === $this->messages) {
            $this->messages = $this->container->getParameter("cadrone.nice_contact_form.message.errors");
        }

        if (array_key_exists($name, $this->constraints) && !empty($this->constraints[$name])) {

            foreach ($this->constraints[$name] as $constraint => $options) {

                $class = "Symfony\\Component\\Validator\\Constraints\\".$constraint;

                if (array_key_exists($name, $this->messages) && array_key_exists($constraint, $this->messages[$name])) {
                    $options = array_merge($options, $this->messages[$name][$constraint]);
                }

                if (class_exists($class)) {
                    $configuration[] = new $class($options);
                } elseif (class_exists($constraint)) {
                    $configuration[] = new $constraint($options);
                } else {
                    //throw class not fund exception
                }
            }

            return $configuration;

        } else {
            return array();
        }
    }

    public function getName()
    {
        return "cadrone_nice_contact_form_type";
    }

}
