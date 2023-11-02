<?php

namespace Isometriks\Bundle\SpamBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class IsometriksSpamExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));

        $this->processTimedConfig($config['timed'], $container, $loader);
        $this->processHoneypotConfig($config['honeypot'], $container, $loader);
    }

    private function processTimedConfig(array $config, ContainerBuilder $container, XmlFileLoader $loader): void
    {
        if (!$this->isConfigEnabled($container, $config)) {
            return;
        }

        $loader->load('timed.xml');

        $definition = $container->getDefinition('isometriks_spam.form.extension.type.timed_spam');
        $definition->addArgument([
            'min' => $config['min'],
            'max' => $config['max'],
            'global' => $config['global'],
            'message' => $config['message'],
        ]);
    }

    private function processHoneypotConfig(array $config, ContainerBuilder $container, XmlFileLoader $loader): void
    {
        if (!$this->isConfigEnabled($container, $config)) {
            return;
        }

        $loader->load('honeypot.xml');

        $definition = $container->getDefinition('isometriks_spam.form.extension.type.honeypot');
        $definition->addArgument([
            'field' => $config['field'],
            'use_class' => $config['use_class'],
            'hide_class' => $config['hide_class'],
            'global' => $config['global'],
            'message' => $config['message'],
        ]);
    }
}
