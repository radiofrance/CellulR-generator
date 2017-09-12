<?php

namespace Rf\CellulR\GeneratorBundle\DependencyInjection;

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

        if (!$container->hasParameter('cellulr.root_dir')) {
            throw new ParameterNotFoundException('cellulr.root_dir');
        }

        if (!$container->hasParameter('cellulr.component_dir')) {
            throw new ParameterNotFoundException('cellulr.component_dir');
        }

        // Inject cell component dir path
        $container
            ->getDefinition('rf.cellulr.generator_command')
            ->replaceArgument(1, $container->getParameter('cellulr.component_dir'))
        ;
    }
}
