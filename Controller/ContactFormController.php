<?php

namespace Cadrone\NiceContactFormBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;

class ContactFormController extends Controller
{

    public function formAction()
    {
        $form = $this->createForm("cadrone_nice_contact_form_type");

        $form->handleRequest($this->getRequest());

        if ($this->get('session')->getFlashBag()->has("form_errors")) {
            $errors = $this->get('session')->getFlashBag()->get("form_errors");
            $this->applyErrors($form, $errors[0]);

            $data = $this->get('session')->getFlashBag()->get("form_data");
            $form->bind($data[0]);
        }

        $queryData = $this->getRequest()->query->get("cadrone_nice_contact_form_type", array());
        $presetData = $this->getRequest()->get("presetData");

        if (!$this->getRequest()->isMethod("POST") && (!empty($presetData) || !empty($queryData)))
        {
            $data = array_merge($queryData, $presetData);
            $form->setData($data);
        }

        if ($form->isValid()) {
            $this->get('cadrone_nice_contact_form.form_handler')->handle($form);

            $message = $this->container->getParameter("cadrone.nice_contact_form.message.success");

            if ($this->getRequest()->isXmlHttpRequest()) {
                return $this->render("CadroneNiceContactFormBundle:ContactForm:success.html.twig", [
                            "message" => $message,
                ]);
            } else {
                $this->get("session")->getFlashBag()->add("notice", $message);
                return $this->redirect($this->getRequest()->headers->get("referer"));
            }
        }

        //if form is invalid and the request is not through ajax
        if ($this->getRequest()->isMethod("POST") && !$this->getRequest()->isXmlHttpRequest()) {
            if (count($form->getErrors(true, false)) > 0) {
                $this->get('session')->getFlashBag()->add("form_errors", $this->getErrorsAsArray($form));
                $this->get('session')->getFlashBag()->add("form_data", $this->getRequest()->request->get($form->getName()));
            }

            return $this->redirect($this->getRequest()->headers->get("referer"));
        }

        return $this->render("CadroneNiceContactFormBundle:Form:form.html.twig", [
                    "form" => $form->createView(),
        ]);
    }

    protected function getErrorsAsArray($form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[$form->getName()][] = $error->getMessage();
        }

        foreach ($form as $child) {
            $childErrors = $this->getErrorsAsArray($child, false);

            $errors = array_merge($errors, $childErrors);
        }

        return $errors;
    }

    protected function applyErrors($form, $errors)
    {
        $name = $form->getName();
        if (array_key_exists($name, $errors)) {
            foreach ($errors[$name] as $message) {
                $form->addError(new FormError($message));
            }
        }

        foreach ($form as $child) {
            $childName = $child->getName();
            if (array_key_exists($childName, $errors)) {
                $this->applyErrors($child, array($childName => $errors[$childName]));
            }
        }
    }

}
