<?php

namespace BrainMaestro\Envman;

final class Parser
{
    /**
     * Parse the environment variables from separate files
     *
     * @param array $directories
     * @param array $files
     * @return Env
     */
    public static function parse(array $directories = ['.'], array $files = []): Env
    {
        $env = new Env;

        foreach (self::getEnvContents($directories, $files) as $envContent) {
            list($key, $value, $file) = explode('=', $envContent);
            $env->add($key, $value, $file, true);
        }

        return $env;
    }

    /**
     * Gets the contents of the env files together
     *
     * @param array $directories
     * @param array $files
     * @return array
     */
    private static function getEnvContents(array $directories, array $files): array
    {
        return array_reduce(Env::getFiles($directories, $files), function (array &$lines, string $file) {
            $_lines = file($file);
            array_walk($_lines, function (string &$line) use ($file) {
                $line .= "={$file}";
            });

            return array_merge($lines, $_lines);
        }, []);
    }
}
