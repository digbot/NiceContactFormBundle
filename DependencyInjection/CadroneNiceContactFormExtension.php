<?php

namespace Cadrone\NiceContactFormBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class CadroneNiceContactFormExtension extends Extension
{

    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

//        if ($config['subject_field_type'] == "text" && empty($config["recipient"])) {
//            throw new \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException(
//            "If you want to use tex subject field for CadroneNiceContactForm you must define recipient."
//            );
//        }
//
//        if ($config['subject_field_type'] == "dropdown" && empty($config["subjects"])) {
//            throw new \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException(
//            "If you want to use dropdown subject field for CadroneNiceContactForm you must define subjects."
//            );
//        }

        // attachments config
        $container->setParameter("cadrone.nice_contact_form.attachments_enabled", $config["attachments"]["enable"]);

        if ($config["attachments"]["enable"]) {
            unset($config["attachments"]["enable"]);

            $container->setParameter("cadrone.nice_contact_form.attachments", $config["attachments"]);
        }

        $container->setParameter("cadrone.nice_contact_form.recipients", $config["recipients"]);
        $container->setParameter("cadrone.nice_contact_form.subjects", $config["subjects"]);
        $container->setParameter("cadrone.nice_contact_form.constraints", $config["constraints"]);
        $container->setParameter("cadrone.nice_contact_form.labels", $config["labels"]);
        $container->setParameter("cadrone.nice_contact_form.captcha", $config["captcha"]);
        $container->setParameter("cadrone.nice_contact_form.processors", $config["processors"]);
        $container->setParameter("cadrone.nice_contact_form.action", $config["action"]);
        $container->setParameter("cadrone.nice_contact_form.message.success", $config["messages"]["success"]);
        $container->setParameter("cadrone.nice_contact_form.message.errors", $config["messages"]["errors"]);


        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');
    }

}
