<?php

namespace BrainMaestro\Envman;

class Writer
{
    private $env;

    public function __construct(Env $env)
    {
        $this->env = $env;
    }

    /**
     * Write contents of the env to the respective files
     *
     * @return void
     */
    public function write()
    {
        $files = [];

        foreach ($this->env->all() as $key => $entry) {
            $files[$entry['file']] = $files[$entry['file']] ?? [];
            $files[$entry['file']][] = "{$key}={$entry['value']}";
        }

        foreach ($files as $file => $contents) {
            file_put_contents($file, implode('\n', $contents));
        }
    }
}
