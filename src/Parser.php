<?php

namespace BrainMaestro\Envman;

class Parser
{
    private $directory;

    public function __construct(string $directory = '.')
    {
        $this->directory = $directory;
    }

    /**
     * Parse the environment variables from separate files
     *
     * @return array
     */
    public function parse(): array
    {
        return array_reduce($this->getEnvContents(), function (array &$vars, string $var) {
            list($key, $value, $file) = explode('=', $var);
            $envVar = ['value' => trim($value), 'file' => $file];

            if (array_key_exists($key, $vars)) {
                $vars[$key][] = $envVar;
            } else {
                $vars[$key] = [$envVar];
            }

            return $vars;
        }, []);
    }

    /**
     * Checks if an environment variable already exists
     *
     * @param string $key
     * @return string
     */
    public function envExists(string $key): ?string
    {
        $parsedEnv = $this->parse();

        return array_key_exists($key, $parsedEnv)
            ? $parsedEnv[$key][0]['file']
            : null;
    }

    /**
     * Gets the contents of the env files together
     *
     * @return array
     */
    private function getEnvContents(): array
    {
        return array_reduce($this->getEnvFiles(), function (array &$envContents, string $file) {
            return array_merge($envContents, array_map(function (string $line) use ($file) {
                $fileName = $this->directory === '.' ? $file : "{$this->directory}/{$file}";
                return "{$line}={$fileName}";
            }, file("{$this->directory}/{$file}")));
        }, []);
    }

    /**
     * Get all environment files
     *
     * @return array
     */
    private function getEnvFiles(): array
    {
        return array_filter(scandir($this->directory), function (string $file) {
            return preg_match('/^\.env\./', $file);
        });
    }
}
