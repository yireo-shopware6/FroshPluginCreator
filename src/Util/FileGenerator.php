<?php declare(strict_types=1);

namespace Frosh\PluginCreator\Util;

use RuntimeException;

class FileGenerator
{
    private string $pluginDirectory;
    private array $variables;

    /**
     * @param string $pluginDirectory
     */
    public function setPluginDirectory(string $pluginDirectory)
    {
        $this->pluginDirectory = $pluginDirectory;
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        $this->variables = $variables;
    }

    /**
     * @param string $file
     * @param string $contents
     */
    public function generate(string $file, string $contents)
    {
        if (!is_dir($this->pluginDirectory)) {
            throw new RuntimeException('Plugin directory does not exist');
        }

        $file = $this->pluginDirectory . '/' . $file;
        file_put_contents($file, $this->parseVariables($contents));
    }

    /**
     * @param string $text
     * @return string
     */
    private function parseVariables(string $text): string
    {
        return str_replace(
            array_keys($this->variables),
            $this->variables,
            $text
        );
    }
}
