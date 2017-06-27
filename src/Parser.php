<?php

namespace BrainMaestro\Envman;

final class Parser
{
    /**
     * Parse the environment variables from separate files
     *
     * @param array $directories
     * @return Env
     */
    public static function parse(array $directories = ['.']): Env
    {
        $env = new Env;

        foreach (self::getEnvContents($directories) as $envContent) {
            list($key, $value, $file) = explode('=', $envContent);
            $env->add($key, $value, $file, true);
        }

        return $env;
    }

    /**
     * Gets the contents of the env files together
     *
     * @param array $directories
     * @return array
     */
    private static function getEnvContents(array $directories): array
    {
        return array_reduce(self::getEnvFiles($directories), function (array &$lines, string $file) {
            $_lines = file($file);
            array_walk($_lines, function (string &$line) use ($file) {
                $line .= "={$file}";
            });

            return array_merge($lines, $_lines);
        }, []);
    }

    /**
     * Get env files from all directories
     *
     * @param array $directories
     * @return array
     */
    private static function getEnvFiles(array $directories): array
    {
        return array_reduce($directories, function (array &$files, string $directory) {
            $_files = self::getDirectoryEnvFiles($directory);
            array_walk($_files, function (string &$file) use ($directory) {
                if ($directory !== '.') {
                    $file = "{$directory}/{$file}";
                }
            });

            return array_merge($files, $_files);
        }, []);
    }

    /**
     * Get .env files in a directory
     *
     * @param string $directory
     * @return array
     */
    private static function getDirectoryEnvFiles(string $directory): array
    {
        if (! is_dir($directory)) {
            return [];
        }

        return array_filter(scandir($directory), function (string $file) {
            return preg_match('/^\.env\./', $file);
        });
    }
}
