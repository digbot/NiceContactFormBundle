<?php

namespace Cadrone\NiceContactFormBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cadrone_nice_contact_form');

        $rootNode
                ->children()
                    ->scalarNode("action")->isRequired()->cannotBeEmpty()->end()
                    ->arrayNode("processors")
                        ->defaultValue(array("cadrone_nice_contact_form.form.processor.sendmail"))
                        ->prototype("scalar")
                        ->end()
                    ->end()
                    ->scalarNode("captcha")->defaultValue(null)->end()
                    ->arrayNode("labels")
                        ->children()
                            ->scalarNode("name")->defaultValue("Name")->end()
                            ->scalarNode("email")->defaultValue("E-mail")->end()
                            ->scalarNode("recipients")->defaultValue("To")->end()
                            ->scalarNode("subject")->defaultValue("Subject")->end()
                            ->scalarNode("body")->defaultValue("Text")->end()
                            ->scalarNode("attachments")->defaultValue("Attachments")->end()
                            ->scalarNode("captcha")->defaultValue("Verification code")->end()
                            ->scalarNode("submit")->defaultValue("Send")->end()
                        ->end()
                    ->end()
                    ->arrayNode("attachments")
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->booleanNode("enable")->defaultValue(false)->end()
                            ->integerNode("max_allowed")->min(0)->defaultValue(3)->end()
                            ->scalarNode("upload_dir")->defaultValue("%kernel.root_dir%/../web/attachments")->end()
                        ->end()
                    ->end()
                    ->arrayNode("recipients")
                        ->defaultValue(array())
                        ->prototype("array")
                            ->children()
                                ->scalarNode("title")->isRequired()->cannotBeEmpty()->end()
                                ->arrayNode("recipients")
                                    ->isRequired()
                                    ->cannotBeEmpty()
                                    ->prototype("scalar")
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                    ->arrayNode("subjects")
                        ->defaultValue(array())
                        ->prototype("scalar")
                    ->end()
                ->end()
        ;

        return $treeBuilder;
    }
}
