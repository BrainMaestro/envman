<?php

namespace BrainMaestro\Envman\Tests;

use BrainMaestro\Envman\Env;
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase
{
    use TestUtil;

    protected $env;

    public function setUp()
    {
        $this->env = new Env;
    }

    /**
     * @test
     */
    public function it_adds_an_env_variable()
    {
        $this->env->add('APP_NAME', 'env-test-app', '.env.app');
        $this->env->add('AUTH_SECRET', 'very-secret-key', 'environments/.env.auth');

        $this->assertTrue($this->env->has('APP_NAME'));
        $this->assertFalse($this->env->has('APP_KEY'));
        $this->assertEquals(2, $this->env->entries());
        $this->assertEquals(['env-test-app'], $this->env->values('APP_NAME'));
        $this->assertEquals(['.env.app'], $this->env->files('APP_NAME'));
    }

    /**
     * @test
     */
    public function it_does_not_add_duplicate_env_variables()
    {
        $this->env->add('APP_NAME', 'env-test-app', '.env.app');
        $this->assertFalse($this->env->add('APP_NAME', 'env-test-auth', '.env.auth'));

        $this->assertTrue($this->env->has('APP_NAME'));
        $this->assertFalse($this->env->isDuplicate('APP_NAME'));
        $this->assertEquals(1, $this->env->entries());
        $this->assertEquals(['env-test-app'], $this->env->values('APP_NAME'));
        $this->assertEquals(['.env.app'], $this->env->files('APP_NAME'));
    }

    /**
     * @test
     */
    public function it_adds_duplicate_env_variables()
    {
        $this->env->add('APP_NAME', 'env-test-app', '.env.app');
        $this->env->add('APP_NAME', 'env-test-auth', '.env.auth', true);

        $this->assertTrue($this->env->has('APP_NAME'));
        $this->assertTrue($this->env->isDuplicate('APP_NAME'));
        $this->assertEquals(2, $this->env->entries('APP_NAME'));
        $this->assertEquals(['env-test-app', 'env-test-auth'], $this->env->values('APP_NAME'));
        $this->assertEquals(['.env.app', '.env.auth'], $this->env->files('APP_NAME'));
    }

    /**
     * @test
     */
    public function it_checks_for_encrypted_and_comment_env_variables()
    {
        $this->env->add('#APP_NAME', 'env-test-app', '.env.app');
        $this->env->add('$AUTH_SECRET', 'encrypted-very-secret-key', '.env.auth');

        $this->assertEquals(2, $this->env->entries());
        $this->assertTrue($this->env->isComment('#APP_NAME'));
        $this->assertTrue($this->env->isEncrypted('$AUTH_SECRET'));
        $this->assertFalse($this->env->isEncrypted('$APP_KEY'));
    }
}
