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
        return array_reduce(Env::getFiles($directories), function (array &$lines, string $file) {
            $_lines = file($file);
            array_walk($_lines, function (string &$line) use ($file) {
                $line .= "={$file}";
            });

            return array_merge($lines, $_lines);
        }, []);
    }
}
