<?php

namespace BrainMaestro\Envman\Tests;

use BrainMaestro\Envman\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    use TestUtil;

    /**
     * @test
     */
    public function it_parses_environment_variables_from_env_files()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app\nAPP_KEY=abcdef");
        file_put_contents('.env.auth', "AUTH_SECRET=very-secret-key\nAUTH_API=auth");

        $env = Parser::parse();

        $this->assertTrue($env->has('APP_NAME'));
        $this->assertTrue($env->has('APP_KEY'));
        $this->assertTrue($env->has('AUTH_SECRET'));
        $this->assertTrue($env->has('AUTH_API'));

        $this->assertEquals(['env-test-app'], $env->values('APP_NAME'));
        $this->assertEquals(['very-secret-key'], $env->values('AUTH_SECRET'));
        $this->assertEquals(['.env.app'], $env->files('APP_KEY'));
        $this->assertEquals(['.env.auth'], $env->files('AUTH_API'));

        $this->deleteEnv('.');
    }

    /**
     * @test
     */
    public function it_parses_duplicate_environment_variables()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app");
        file_put_contents('.env.auth', "APP_NAME=env-test-auth");

        $env = Parser::parse();

        $this->assertTrue($env->has('APP_NAME'));
        $this->assertEquals(2, $env->entries('APP_NAME'));
        $this->assertEquals(['env-test-app', 'env-test-auth'], $env->values('APP_NAME'));
        $this->assertEquals(['.env.app', '.env.auth'], $env->files('APP_NAME'));

        $this->deleteEnv('.');
    }

    /**
     * @test
     */
    public function it_parses_environment_variables_from_a_custom_directory()
    {
        mkdir('staging');
        mkdir('production');
        file_put_contents('staging/.env.db', "DB_HOST=localhost");
        file_put_contents('production/.env.app', "APP_NAME=env-test-app");

        $env = Parser::parse(['staging', 'production']);

        $this->assertTrue($env->has('APP_NAME'));
        $this->assertEquals(['localhost'], $env->values('DB_HOST'));
        $this->assertEquals(['env-test-app'], $env->values('APP_NAME'));
        $this->assertEquals(['staging/.env.db'], $env->files('DB_HOST'));
        $this->assertEquals(['production/.env.app'], $env->files('APP_NAME'));

        $this->deleteEnv('staging', 'production');
    }
}
