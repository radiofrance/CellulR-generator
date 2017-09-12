<?php

namespace Rf\CellulR\GeneratorBundle\Command;

use Rf\CellulR\GeneratorBundle\Generator\ComponentGenerator;
use Rf\CellulR\GeneratorBundle\Generator\PageGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Container;

/**
 * Class GenerateCellulRCommand.
 */
class GenerateWebComponentCommand extends Command
{
    /**
     * @var string
     */
    protected $kernelRootDir;

    /**
     * @var string
     */
    protected $componentDir;

    /**
     * GenerateComponentCommand constructor.
     *
     * @param string $kernelRootDir
     * @param string $componentDir
     */
    public function __construct($kernelRootDir, $componentDir)
    {
        $this->kernelRootDir = $kernelRootDir;
        $this->componentDir = $componentDir;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'The cell name'),
                new InputArgument('type', InputArgument::REQUIRED, 'The cell type: component or page'),
                new InputArgument('env', InputArgument::OPTIONAL, 'The cell environment', 'main'),
            ))
            ->setDescription('Generates a cell')
            ->setHelp(<<<EOT
The <info>generate:cell</info> command helps you generates new cell with it's core object.
EOT
            )
            ->setName('generate:cell');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $name = strtr(ucwords(strtr(Container::camelize($input->getArgument('name')), array('-' => ' '))), array(' ' => ''));
        $name = transliterator_transliterate(
            'Any-Latin; Latin-ASCII; [:Nonspacing Mark:] Remove; [:Punctuation:] Remove;',
            $name
        );

        switch ($type = $input->getArgument('type')) {
            case 'p':
            case 'pag':
            case 'page':
                $type = 'page';
                $generator = new PageGenerator($name, $this->kernelRootDir, $this->componentDir);
                break;
            case 'c':
            case 'comp':
            case 'component':
                $type = 'component';
                $generator = new ComponentGenerator($name, $this->kernelRootDir, $this->componentDir);
                break;
            default:
                $io->error(sprintf('The argument %s must be one of theses values: [%s]', 'type', implode(', ', ['component', 'page'])));

                return;
                break;

        }

        if ($exists = $generator->exists()) {
            $question = sprintf(
                'The %s "%s" already exists. Do you want to replace it ?',
                $type,
                $input->getArgument('name')
            );

            if (!$replace = $io->confirm($question, true)) {
                $io->warning('Operation aborted.');

                return;
            }
        }

        $generator->generate($input->getArgument('env'), isset($replace) ? $replace : false);

        $io->success(sprintf(
            'The %s %s has been %s',
            $type,
            $input->getArgument('name'),
            $exists ? 'replaced' : 'created'
        ));

        $io->writeln(sprintf('<comment>[Component]</comment> %s', $generator->getPath()));
    }
}
