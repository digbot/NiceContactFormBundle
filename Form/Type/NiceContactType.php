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
        ));

        $builder->add("email", null, array(
            "required" => true,
            "label" => $labels["email"],
        ));

        if ($this->container->getParameter("cadrone.nice_contact_form.recipients") > 0) {
            $builder->add("recipients", $this->getRecipientsFieldType(), $this->getRecipentsFieldConfig($labels));
        }

        $builder->add("subject", $this->getSubjectFieldType(), $this->getSubjectFieldConfig($labels));

        $builder->add("body", "textarea", array(
            "required" => true,
            "label" => $labels["body"],
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

    public function getName()
    {
        return "cadrone_nice_contact_form_type";
    }

}
