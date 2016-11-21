<?php

namespace Rf\WebComponent\GeneratorBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class GeneratorExtension.
 *
 * @author Yoan Guillemin <yoann.guillemin@radiofrance.com>
 */
class GeneratorExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');

        if (!$container->hasParameter('wc.root_dir')) {
            throw new ParameterNotFoundException('wc.root_dir');
        }

        if (!$container->hasParameter('wc.component_dir')) {
            throw new ParameterNotFoundException('wc.component_dir');
        }

        // Inject web component dir path
        $container
            ->getDefinition('rf.wc.generator_command')
            ->replaceArgument(1, $container->getParameter('wc.component_dir'))
        ;
    }
}
