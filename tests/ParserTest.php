<?php

namespace BrainMaestro\Envman\Tests;

use BrainMaestro\Envman\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser;
    }

    /**
     * @test
     */
    public function it_parses_environment_variables_from_env_files()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app\nAPP_KEY=abcdef");
        file_put_contents('.env.auth', "AUTH_SECRET=very-secret-key\nAUTH_API=auth");

        $parsedEnv = $this->parser->parse();

        $this->assertNotEmpty($parsedEnv);
        $this->assertArrayHasKey('APP_NAME', $parsedEnv);
        $this->assertArrayHasKey('APP_KEY', $parsedEnv);
        $this->assertArrayHasKey('AUTH_SECRET', $parsedEnv);
        $this->assertArrayHasKey('AUTH_API', $parsedEnv);

        $this->assertEquals('env-test-app', $parsedEnv['APP_NAME'][0]['value']);
        $this->assertEquals('very-secret-key', $parsedEnv['AUTH_SECRET'][0]['value']);
        $this->assertEquals('.env.app', $parsedEnv['APP_KEY'][0]['file']);
        $this->assertEquals('.env.auth', $parsedEnv['AUTH_API'][0]['file']);

        unlink('.env.app');
        unlink('.env.auth');
    }

    /**
     * @test
     */
    public function it_parses_duplicate_environment_variables()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app");
        file_put_contents('.env.auth', "APP_NAME=env-test-auth");

        $parsedEnv = $this->parser->parse();

        $this->assertNotEmpty($parsedEnv);
        $this->assertArrayHasKey('APP_NAME', $parsedEnv);
        $this->assertCount(2, $parsedEnv['APP_NAME']);

        $this->assertEquals('env-test-app', $parsedEnv['APP_NAME'][0]['value']);
        $this->assertEquals('env-test-auth', $parsedEnv['APP_NAME'][1]['value']);
        $this->assertEquals('.env.app', $parsedEnv['APP_NAME'][0]['file']);
        $this->assertEquals('.env.auth', $parsedEnv['APP_NAME'][1]['file']);

        unlink('.env.app');
        unlink('.env.auth');
    }

    /**
     * @test
     */
    public function it_parses_environment_variables_from_a_custom_directory()
    {
        mkdir('./environment');
        file_put_contents('./environment/.env.app', "APP_NAME=env-test-app");

        $parser = new Parser('./environment');
        $parsedEnv = $parser->parse();

        $this->assertNotEmpty($parsedEnv);
        $this->assertArrayHasKey('APP_NAME', $parsedEnv);
        $this->assertCount(1, $parsedEnv['APP_NAME']);

        $this->assertEquals('env-test-app', $parsedEnv['APP_NAME'][0]['value']);
        $this->assertEquals('./environment/.env.app', $parsedEnv['APP_NAME'][0]['file']);

        unlink('./environment/.env.app');
        rmdir('./environment');
    }

    /**
     * @test
     */
    public function it_checks_if_an_environment_variable_already_exists()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app");

        $this->assertEquals($this->parser->envExists('APP_NAME'), '.env.app');
        $this->assertNull($this->parser->envExists('APP_KEY'));

        unlink('.env.app');
    }
}
