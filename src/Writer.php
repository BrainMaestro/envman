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
        $files = [];

        foreach ($env->all() as $key => $entry) {
            $files[$entry['file']] = $files[$entry['file']] ?? [];
            $files[$entry['file']][] = "{$key}={$entry['value']}";
        }

        foreach ($files as $file => $contents) {
            $directory = dirname($file);

            if (! is_dir($directory)) {
                mkdir($directory, 0700, true);
            }

            file_put_contents($file, implode('\n', $contents));
        }
    }
}
