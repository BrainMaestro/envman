<?php

namespace BrainMaestro\Envman;

class Parser
{
    private $directory;
    private $env;

    public function __construct(string $directory = '.')
    {
        $this->directory = $directory;
        $this->env = new Env;
    }

    /**
     * Parse the environment variables from separate files
     *
     * @return Env
     */
    public function parse(): Env
    {
        foreach ($this->getEnvContents() as $envContent) {
            list($key, $value, $file) = explode('=', $envContent);
            $this->env->add($key, $value, $file, true);
        }

        return $this->env;
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
