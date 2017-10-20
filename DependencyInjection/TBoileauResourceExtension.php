<?php

namespace TBoileau\ResourceBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class TBoileauResourceExtension extends Extension implements ExtensionInterface, CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    public function process(ContainerBuilder $container)
    {
        $resources = [];
        if ($container->hasParameter('twig.form.resources')) {
            $resources = $container->getParameter('twig.form.resources');
        }

        $resources[] = 'TBoileauResourceBundle::form.html.twig';

        $container->setParameter('twig.form.resources', $resources);
    }


}
