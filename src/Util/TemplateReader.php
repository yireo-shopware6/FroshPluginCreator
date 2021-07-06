<?php declare(strict_types=1);

namespace Frosh\PluginCreator\Util;

use RuntimeException;
use Symfony\Component\HttpKernel\KernelInterface;

class TemplateReader
{
    private KernelInterface $kernel;
    private string $bundleClassTemplate;
    private string $composerJsonTemplate;
    private string $serviceXmlTemplate;
    private string $configXmlTemplate;

    /**
     * ComposerTemplate constructor.
     * @param KernelInterface $kernel
     * @param string $bundleClassTemplate
     * @param string $composerJsonTemplate
     * @param string $serviceXmlTemplate
     * @param string $configXmlTemplate
     */
    public function __construct(
        KernelInterface $kernel,
        string $bundleClassTemplate,
        string $composerJsonTemplate,
        string $serviceXmlTemplate,
        string $configXmlTemplate
    ) {
        $this->kernel = $kernel;
        $this->bundleClassTemplate = $bundleClassTemplate;
        $this->composerJsonTemplate = $composerJsonTemplate;
        $this->serviceXmlTemplate = $serviceXmlTemplate;
        $this->configXmlTemplate = $configXmlTemplate;
    }

    /**
     * @return string
     */
    public function getBundleClassTemplate(): string
    {
        return $this->get($this->bundleClassTemplate);
    }

    /**
     * @return string
     */
    public function getComposerTemplate(): string
    {
        return $this->get($this->composerJsonTemplate);
    }

    /**
     * @return string
     */
    public function getServiceXml(): string
    {
        return $this->get($this->serviceXmlTemplate);
    }

    /**
     * @return string
     */
    public function getConfigXml(): string
    {
        return $this->get($this->configXmlTemplate);
    }

    /**
     * @param string $template
     * @return string
     */
    private function get(string $template): string
    {
        $bundleName = 'FroshPluginCreator';
        $bundleFile = $this->kernel->locateResource('@' . $bundleName);
        if (empty($bundleFile) || !file_exists($bundleFile)) {
            throw new RuntimeException('Unable to locate current bundle');
        }

        $bundleDirectory = dirname($bundleFile);

        // @todo: Refactor this to use a Symfony component for this, instead of using file_get_contents
        return file_get_contents($bundleDirectory . '/src/Resources/templates/' . $template);
    }
}

