<?php

namespace Rf\WebComponent\GeneratorBundle\Generator;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class ComponentGenerator.
 *
 * @author Yoan Guillemin <yoann.guillemin@radiofrance.com>
 */
class ComponentGenerator extends DefaultGenerator
{
    const BASE_DIR_NAME = 'Component',
          TEMPLATE_DIR_NAME = 'Component'
    ;

    /**
     * {@inheritdoc}
     */
    public function generate($env, $replace = false)
    {
        $this->createDirectories();
        $this->generateViewObject($replace);
        $this->generateComponent($env, $replace);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return array(
            'component_namespace' => $this->getNamespace(),
            'component_name' => $this->name,
            'component_name_var' => lcfirst($this->name),
            'component_name_underscore' => Container::underscore($this->name),
        );
    }

    /**
     * Create:
     *      The View object directory
     *      The Component directory
     *      The LESS directory inside the Component directory
     *      The JS directory inside the Component directory
     */
    private function createDirectories()
    {
        $dirComponent = $this->getPath();

        $this->filesystem->mkdir($dirComponent);
        $this->filesystem->mkdir($dirComponent);
        $this->filesystem->mkdir("$dirComponent/less");
        $this->filesystem->mkdir("$dirComponent/js");
    }

    /**
     * Generate the View object.
     *
     * @param bool $replace
     */
    private function generateViewObject($replace)
    {
        $dirComponent = $this->getPath();
        $viewObject = "$dirComponent/$this->name.php";

        if (!file_exists($viewObject) || $replace) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/ViewObject/component.php.twig', $viewObject, $this->getParameters());
        }
    }

    /**
     * Generate the component.
     *
     * @param string $env
     * @param bool   $replace
     */
    private function generateComponent($env, $replace)
    {
        $parameters = $this->getParameters();
        $dirComponent = $this->getPath();
        $component = $dirComponent.'/'.$parameters['component_name_underscore'].'.html.twig';
        $readme = "$dirComponent/README.md";
        $json = "$dirComponent/component.json";
        $less = "$dirComponent/less/{$env}.less";
        $mainJS = "$dirComponent/js/{$env}.js";
        $componentJS = "$dirComponent/js/component.js";
        $handlerJS = "$dirComponent/js/handler.js";
        $initFile = function() use ($component) {
            file_put_contents($component, <<<EOT
{% spaceless %}
{% endspaceless %}
EOT
            );
        };

        // Component
        if (!file_exists($component)) {
            $this->filesystem->touch($component);

            $initFile();
        }

        if ($replace) {
            $initFile();
        }

        // README
        if (!file_exists($readme) || $replace) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/README.md.twig', $readme, $parameters);
        }

        // JSON
        if (!file_exists($json) || $replace) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/component.json.twig', $json, $parameters);
        }

        // LESS
        if (file_exists($less) && $replace) {
            $this->filesystem->remove($less);
        }

        if (!file_exists($less)) {
            $this->filesystem->touch($less);
        }

        // JS
        if (!file_exists($mainJS) || $replace) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/js/main.js.twig', $mainJS, $parameters);
        }

        if (!file_exists($componentJS) || $replace) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/js/component.js.twig', $componentJS, $parameters);
        }

        if (!file_exists($handlerJS) || $replace) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/js/handler.js.twig', $handlerJS, $parameters);
        }
    }
}
