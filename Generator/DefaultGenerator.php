<?php

namespace Rf\WebComponent\GeneratorBundle\Generator;

use Rf\WebComponent\EngineBundle\Utils\UtilsTrait;
use Symfony\Component\Filesystem\Filesystem;

/**
 * Class DefaultGenerator.
 *
 * @author Yoan Guillemin <yoann.guillemin@radiofrance.com>
 */
abstract class DefaultGenerator
{
    use UtilsTrait;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $kernelRootDir;

    /**
     * @var string
     */
    protected $componentBaseDir;

    /**
     * TwigGenerator constructor.
     *
     * @param string $name
     * @param string $kernelRootDir
     * @param string $componentBaseDir
     */
    public function __construct($name, $kernelRootDir, $componentBaseDir)
    {
        $this->filesystem = new Filesystem();
        $this->name = $name;
        $this->kernelRootDir = $kernelRootDir;
        $this->componentBaseDir = $componentBaseDir;
    }

    protected function render($template, $parameters)
    {
        $twig = $this->getTwigEnvironment();

        return $twig->render($template, $parameters);
    }

    /**
     * Get the twig environment that will render skeletons.
     *
     * @return \Twig_Environment
     */
    protected function getTwigEnvironment()
    {
        $skeletonDirs = [
            __DIR__.'/../Resources/skeleton',
        ];

        return new \Twig_Environment(new \Twig_Loader_Filesystem($skeletonDirs), array(
            'debug' => true,
            'cache' => false,
            'strict_variables' => true,
            'autoescape' => false
        ));
    }

    /**
     * Render the content file.
     *
     * @param string $template
     * @param string $target
     * @param array  $parameters
     *
     * @return int
     */
    protected function renderFile($template, $target, $parameters = array())
    {
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0777, true);
        }

        return file_put_contents($target, $this->render($template, $parameters));
    }

    /**
     * Check if the page already exists.
     *
     * @return bool
     */
    public function exists()
    {
        return is_dir($this->getPath());
    }

    /**
     * Get the Web Component path.
     *
     * @return string|void
     */
    public function getPath()
    {
        if (!defined('static::BASE_DIR_NAME')) {
            return;
        }

        return $this->componentBaseDir.DIRECTORY_SEPARATOR.static::BASE_DIR_NAME.DIRECTORY_SEPARATOR.$this->name;
    }

    /**
     * Get the Web Component namespace.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->getNamespaceFromDir($this->kernelRootDir, $this->getPath());
    }

    /**
     * Generate the Web Component.
     *
     * @param string $env
     * @param bool   $replace
     *
     * @return mixed
     */
    abstract public function generate($env, $replace = false);

    /**
     * Get the parameters.
     *
     * @return array
     */
    abstract public function getParameters();
}
