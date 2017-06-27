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
        $env->add('APP_NAME', 'env-test-auth', '.env.auth', true);

        (new Writer($env))->write();

        $this->assertEquals('APP_KEY=abcdef\nAPP_NAME=env-test-app', file_get_contents('.env.app'));
        $this->assertEquals('APP_NAME=env-test-auth', file_get_contents('.env.auth'));

        $this->delete('.', 'app', 'auth');
    }

    /**
     * @test
     */
    public function it_overwrites_existing_env_values_in_a_file()
    {
        file_put_contents('.env.app', "APP_KEY=abcdef");
        $env = new Env;
        $env->add('APP_NAME', 'env-test-app', '.env.app');

        (new Writer($env))->write();

        $this->assertEquals('APP_NAME=env-test-app', file_get_contents('.env.app'));

        $this->delete('.', 'app');
    }
}
