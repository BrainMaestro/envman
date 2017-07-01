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
     * Get number of entries of an env key or the entire env
     *
     * @param string $key
     * @return int
     */
    public function entries(string $key = null): int
    {
        return count($key ? $this->env[$key] : $this->env);
    }

    /**
     * Return all values from the env
     *
     * @return \Generator
     */
    public function all(): \Generator
    {
        uksort($this->env, function (string $a, string $b) {
            $a = preg_replace('/^[#\$]/', '', $a);
            $b = preg_replace('/^[#\$]/', '', $b);

            return strcasecmp($a, $b);
        });

        foreach ($this->env as $key => $entries) {
            foreach ($entries as $entry) {
                yield $key => $entry;
            }
        }
    }

    /**
     * Check if env entry for key is a comment
     *
     * @param string $key
     * @return bool
     */
    public function isComment(string $key): bool
    {
        return $this->has($key) && $key[0] === '#';
    }

    /**
     * Check if env entry is duplicated
     *
     * @param string $key
     * @return bool
     */
    public function isDuplicate(string $key): bool
    {
        return $this->entries($key) > 1;
    }

    /**
     * Check if env entry for key is encrypted
     *
     * @param string $key
     * @return bool
     */
    public function isEncrypted(string $key): bool
    {
        return $this->has($key) && $key[0] === '$';
    }

    /**
     * Get env files from all directories
     *
     * @param array $directories
     * @return array
     */
    public static function getFiles(array $directories): array
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
     * Check if a file is an env file
     *
     * @param string $file
     * @return bool
     */
    public static function isEnvFile(string $file): bool
    {
        return preg_match('/^\.env\./', $file);
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

        return array_filter(scandir($directory), 'self::isEnvFile');
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
