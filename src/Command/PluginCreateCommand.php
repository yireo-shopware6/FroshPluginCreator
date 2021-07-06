<?php declare(strict_types=1);

namespace Frosh\PluginCreator\Command;

use Frosh\PluginCreator\Util\FileGenerator;
use Frosh\PluginCreator\Util\TemplateReader;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class PluginCreateCommand extends Command
{
    protected static $defaultName = 'plugin:create';

    /**
     * @var string
     */
    private string $projectDir;

    /**
     * @var TemplateReader
     */
    private TemplateReader $templateReader;

    /**
     * @var FileGenerator
     */
    private FileGenerator $fileGenerator;

    /**
     * PluginCreateCommand constructor.
     * @param TemplateReader $templateReader
     * @param FileGenerator $fileGenerator
     * @param string $projectDir
     */
    public function __construct(
        TemplateReader $templateReader,
        FileGenerator $fileGenerator,
        string $projectDir
    ) {
        parent::__construct();
        $this->templateReader = $templateReader;
        $this->fileGenerator = $fileGenerator;
        $this->projectDir = $projectDir;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::OPTIONAL)
            ->addOption('namespace', null, InputOption::VALUE_OPTIONAL, 'PHP namespace')
            ->addOption('composer-name', null, InputOption::VALUE_OPTIONAL, 'Composer name')
            ->addOption('description', 'd', InputOption::VALUE_OPTIONAL, 'Description')
            ->addOption('create-config', 'c', InputOption::VALUE_NONE, 'Create src/Resources/config/config.xml')
            ->setDescription('Creates a plugin skeleton (improved)');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $pluginName = ucfirst($this->getArgumentFromInput($input, $output, 'name'));
        $phpNamespace = $this->getOptionFromInput($input, 'namespace', $pluginName);
        $composerName = $this->getOptionFromInput($input, 'composer-name', $pluginName);
        $description = $this->getOptionFromInput($input, 'description', $pluginName);

        $this->fileGenerator->setPluginDirectory($this->getPluginDirectory($pluginName));
        $this->fileGenerator->setVariables([
            '#namespace#' => $phpNamespace,
            '#composerNamespace#' => str_replace('\\', '\\\\', $phpNamespace),
            '#pluginName#' => $pluginName,
            '#composerName#' => $composerName,
            '#description#' => $description,
        ]);

        $this->fileGenerator->generate('/composer.json', $this->templateReader->getComposerTemplate());
        $this->fileGenerator->generate('/src/' . $pluginName . '.php', $this->templateReader->getBundleClassTemplate());
        $this->fileGenerator->generate('/src/Resources/config/services.xml', $this->templateReader->getServiceXml());

        if ($input->getOption('create-config')) {
            $this->fileGenerator->generate('/src/Resources/config/config.xml', $this->templateReader->getConfigXml());
        }

        return 0;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param string $argumentName
     * @return string
     */
    private function getArgumentFromInput(
        InputInterface $input,
        OutputInterface $output,
        string $argumentName
    ): string {
        $argumentValue = $input->getArgument($argumentName);
        if ($argumentValue) {
            return $argumentValue;
        }

        $question = new Question('Please enter a ' . $argumentName . ': ');
        return $this->getHelper('question')->ask($input, $output, $question);
    }

    /**
     * @param InputInterface $input
     * @param string $argumentName
     * @param string $defaultValue
     * @return string
     */
    private function getOptionFromInput(
        InputInterface $input,
        string $optionName,
        string $defaultValue
    ): string {
        $optionValue = (string)$input->getOption($optionName);
        if ($optionValue) {
            return $optionValue;
        }

        return $defaultValue;
    }

    /**
     * @param string $pluginName
     * @return string
     */
    private function getPluginDirectory(string $pluginName): string
    {
        $pluginDirectory = $this->projectDir . '/custom/plugins/' . $pluginName;
        if (file_exists($pluginDirectory)) {
            throw new RuntimeException(sprintf('Plugin directory %s already exists', $pluginDirectory));
        }

        mkdir($pluginDirectory . '/src/Resources/config/', 0777, true);

        return $pluginDirectory;
    }
}
