<?php

namespace BrainMaestro\Envman\Tests;

use BrainMaestro\Envman\Env;
use BrainMaestro\Envman\Writer;
use PHPUnit\Framework\TestCase;

class WriterTest extends TestCase
{
    use TestUtil;

    /**
     * @test
     */
    public function it_writes_env_values_to_file()
    {
        $env = new Env;
        $env->add('APP_NAME', 'env-test-app', '.env.app');
        $env->add('APP_KEY', 'abcdef', '.env.app');
        $env->add('APP_NAME', 'env-test-auth', '.env.auth', '.', true);
        $env->add('DB_HOST', 'localhost', '.env.db', 'staging');

        Writer::write($env);

        $this->assertEquals('APP_KEY=abcdef\nAPP_NAME=env-test-app', file_get_contents('.env.app'));
        $this->assertEquals('APP_NAME=env-test-auth', file_get_contents('.env.auth'));
        $this->assertEquals('DB_HOST=localhost', file_get_contents('staging/.env.db'));

        $this->delete('.', 'app', 'auth');
        $this->delete('staging', 'db');
    }

    /**
     * @test
     */
    public function it_overwrites_existing_env_values_in_a_file()
    {
        file_put_contents('.env.app', "APP_KEY=abcdef");
        $env = new Env;
        $env->add('APP_NAME', 'env-test-app', '.env.app');

        Writer::write($env);

        $this->assertEquals('APP_NAME=env-test-app', file_get_contents('.env.app'));

        $this->delete('.', 'app');
    }
}
