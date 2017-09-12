<?php

namespace Rf\CellulR\GeneratorBundle\Generator;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class PageGenerator.
 *
 * @author Yoan Guillemin <yoann.guillemin@radiofrance.com>
 */
class PageGenerator extends DefaultGenerator
{
    const BASE_DIR_NAME = 'Page',
          TEMPLATE_DIR_NAME = 'Page'
    ;

    /**
     * {@inheritdoc}
     */
    public function generate($env, $replace = false)
    {
        $this->createDirectories();
        $this->generateAbstractPage();
        $this->generateCoreObject($replace);
        $this->generateComponent($env, $replace);
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters()
    {
        return array(
            'abstract_page_namespace' => str_replace('\\'.$this->name, '', $this->getNamespace()),
            'page_namespace' => $this->getNamespace(),
            'page_name' => $this->name,
            'page_name_var' => lcfirst($this->name),
            'page_name_underscore' => Container::underscore($this->name),
        );
    }

    /**
     * Create:
     *      The Core object directory
     *      The Page directory
     *      The LESS directory inside the Page directory
     *      The JS directory inside the Page directory.
     */
    private function createDirectories()
    {
        $dirPage = $this->getPath();

        $this->filesystem->mkdir($dirPage);
        $this->filesystem->mkdir($dirPage);
        $this->filesystem->mkdir("$dirPage/less");
    }

    /**
     * Generate the abstract page model for the pages' core objects.
     */
    private function generateAbstractPage()
    {
        $dirPage = $this->componentBaseDir.DIRECTORY_SEPARATOR.self::BASE_DIR_NAME;
        $abstractPage = $dirPage.DIRECTORY_SEPARATOR.'AbstractPage.php';

        if (!file_exists($abstractPage)) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/CoreObject/abstract_page.php.twig', $abstractPage, $this->getParameters());
        }
    }

    /**
     * Generate the Core object.
     *
     * @param bool $replace
     */
    private function generateCoreObject($replace)
    {
        $dirPage = $this->getPath();
        $coreObject = "$dirPage/$this->name.php";

        if (!file_exists($coreObject) || $replace) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/CoreObject/page.php.twig', $coreObject, $this->getParameters());
        }
    }

    /**
     * Generate the page.
     *
     * @param string $env
     * @param bool   $replace
     */
    private function generateComponent($env, $replace)
    {
        $parameters = $this->getParameters();
        $dirPage = $this->getPath();
        $page = $dirPage.'/'.$parameters['page_name_underscore'].'.html.twig';
        $readme = "$dirPage/README.md";
        $json = "$dirPage/component.json";
        $less = "$dirPage/less/{$env}.less";

        // Page
        if (!file_exists($page) || $replace) {
            $this->renderFile(self::TEMPLATE_DIR_NAME.'/page.html.twig.twig', $page, $parameters);
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
    }
}
