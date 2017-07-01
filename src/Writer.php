<?php

namespace BrainMaestro\Envman;

final class Writer
{
    /**
     * Write contents of the env to the respective files
     *
     * @param Env $env
     * @return void
     */
    public static function write(Env $env)
    {
        $output = [];

        foreach ($env->all() as $key => $entry) {
            $output[$entry['file']] = $output[$entry['file']] ?? [];
            $output[$entry['file']][] = "{$key}={$entry['value']}";
        }

        foreach ($output as $file => $contents) {
            $directory = dirname($file);

            if (! is_dir($directory)) {
                mkdir($directory, 0700, true);
            }

            file_put_contents($file, implode("\n", $contents));
        }
    }
}
