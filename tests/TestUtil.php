<?php

namespace BrainMaestro\Envman\Tests;

trait TestUtil
{
    public function delete(string $directory, string ...$files)
    {
        foreach ($files as $file) {
            if (is_file("{$directory}/.env.{$file}")) {
                unlink("{$directory}/.env.{$file}");
            }
        }

        if (count(scandir($directory)) === 2) {
            rmdir($directory);
        }
    }
}
