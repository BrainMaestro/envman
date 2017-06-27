<?php

namespace BrainMaestro\Envman;

class Env
{
    private $env = [];

    /**
     * Add a new key value pair to the env
     *
     * @param string $key
     * @param string $value
     * @param string $file
     * @param bool $duplicates
     * @return bool
     */
    public function add(string $key, string $value, string $file, bool $duplicates = false): bool
    {
        if (! $duplicates && $this->has($key)) {
            return false;
        }

        $this->env[$key] = $this->env[$key] ?? [];
        $this->env[$key][] = ['value' => trim($value), 'file' => $file];

        return true;
    }

    /**
     * Check if a key exists in the env
     *
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->env);
    }

    /**
     * Get the value of an env key
     *
     * @param string $key
     * @return array
     */
    public function values(string $key): array
    {
        return $this->get($key, 'value');
    }

    /**
     * Get the file of an env key
     *
     * @param string $key
     * @return array
     */
    public function files(string $key): array
    {
        return $this->get($key, 'file');
    }

    /**
     * Get number of entries of an env key
     *
     * @param string $key
     * @return int
     */
    public function entries(string $key): int
    {
        return count($this->env[$key]);
    }

    /**
     * Return all values from the env
     *
     * @return \Generator
     */
    public function all(): \Generator
    {
        ksort($this->env);

        foreach ($this->env as $key => $entries) {
            foreach ($entries as $entry) {
                yield $key => $entry;
            }
        }
    }

    /**
     * Get a value from an env key
     *
     * @param string $key
     * @param string $value
     * @return array
     */
    private function get(string $key, string $value): array
    {
        return array_map(function (array $entry) use ($value) {
            return $entry[$value];
        }, $this->env[$key]);
    }
}
