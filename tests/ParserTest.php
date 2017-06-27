<?php

namespace BrainMaestro\Envman\Tests;

use BrainMaestro\Envman\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    use TestUtil;

    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser;
    }

    /**
     * @tes
     */
    public function it_parses_environment_variables_from_env_files()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app\nAPP_KEY=abcdef");
        file_put_contents('.env.auth', "AUTH_SECRET=very-secret-key\nAUTH_API=auth");

        $env = $this->parser->parse();

        $this->assertTrue($env->has('APP_NAME'));
        $this->assertTrue($env->has('APP_KEY'));
        $this->assertTrue($env->has('AUTH_SECRET'));
        $this->assertTrue($env->has('AUTH_API'));

        $this->assertEquals(['env-test-app'], $env->values('APP_NAME'));
        $this->assertEquals(['very-secret-key'], $env->values('AUTH_SECRET'));
        $this->assertEquals(['.env.app'], $env->files('APP_KEY'));
        $this->assertEquals(['.env.auth'], $env->files('AUTH_API'));

        $this->delete('.', 'app', 'auth');
    }

    /**
     * @test
     */
    public function it_parses_duplicate_environment_variables()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app");
        file_put_contents('.env.auth', "APP_NAME=env-test-auth");

        $env = $this->parser->parse();

        $this->assertTrue($env->has('APP_NAME'));
        $this->assertEquals(2, $env->entries('APP_NAME'));
        $this->assertEquals(['env-test-app', 'env-test-auth'], $env->values('APP_NAME'));
        $this->assertEquals(['.env.app', '.env.auth'], $env->files('APP_NAME'));

        $this->delete('.', 'app', 'auth');
    }

    /**
     * @test
     */
    public function it_parses_environment_variables_from_a_custom_directory()
    {
        mkdir('./environment');
        file_put_contents('./environment/.env.app', "APP_NAME=env-test-app");

        $env = (new Parser('./environment'))->parse();

        $this->assertTrue($env->has('APP_NAME'));
        $this->assertEquals(1, $env->entries('APP_NAME'));
        $this->assertEquals(['env-test-app'], $env->values('APP_NAME'));
        $this->assertEquals(['./environment/.env.app'], $env->files('APP_NAME'));

        $this->delete('environment', 'app');
    }
}
