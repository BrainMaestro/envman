<?php

namespace BrainMaestro\Envman\Tests;

use BrainMaestro\Envman\Env;

trait TestUtil
{
    public function deleteEnv(string ...$directories)
    {
        foreach ($directories as $directory) {
            foreach (scandir($directory) as $file) {
                if (Env::isEnvFile($file)) {
                    unlink("{$directory}/{$file}");
                }
            }

            if (count(scandir($directory)) === 2) {
                rmdir($directory);
            }
        }
    }
}
