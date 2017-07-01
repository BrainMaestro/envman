<?php

namespace BrainMaestro\Envman\Tests\Commands;

use BrainMaestro\Envman\Parser;
use BrainMaestro\Envman\Commands\Encrypt;
use BrainMaestro\Envman\Commands\GenerateKey;
use BrainMaestro\Envman\Tests\TestUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class EncryptTest extends TestCase
{
    use TestUtil;

    private $commandTester;

    public function setUp()
    {
        $this->commandTester = new CommandTester(new Encrypt);

        (new CommandTester(new GenerateKey))->execute(['key-name' => 'env-test-key']);

        mkdir('staging');
        file_put_contents('.env.app', "APP_NAME=env-test-app\nAPP_KEY=abcdef");
        file_put_contents('.env.auth', "AUTH_SECRET=very-secret-key");
        file_put_contents('staging/.env.db', "\$DB_HOST=not-localhost");
        file_put_contents('staging/.env.cache', "#CACHE_ENABLED=0");
    }

    public function tearDown()
    {
        unlink('env-test-key');
        $this->deleteEnv('.', 'staging');
    }

    /**
     * @test
     */
    public function it_encrypts_environment_variables_that_are_not_encrypted_or_commented_out()
    {
        $this->commandTester->execute(['targets' => ['.env.app', '.env.auth', 'staging'], '--key' => 'env-test-key']);

        $this->assertContains('Encrypted 3 environment variable(s)', $this->commandTester->getDisplay());

        $env = Parser::parse(['staging'], ['.env.app', '.env.auth']);

        $this->assertTrue($env->has('$APP_NAME'));
        $this->assertTrue($env->has('$APP_KEY'));
        $this->assertTrue($env->has('$AUTH_SECRET'));
        $this->assertTrue($env->has('$DB_HOST'));
        $this->assertTrue($env->has('#CACHE_ENABLED'));

        $this->assertFalse($env->has('APP_NAME'));
        $this->assertFalse($env->has('APP_KEY'));
        $this->assertFalse($env->has('AUTH_SECRET'));
        $this->assertFalse($env->has('DB_HOST'));
        $this->assertFalse($env->has('$#CACHE_ENABLED'));
    }

    /**
     * @test
     */
    public function it_fails_with_an_error_message_if_targets_are_not_provided()
    {
        $this->commandTester->execute(['--key' => 'env-test-key']);

        $this->assertContains('No files or directories specified for encryption', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_fails_with_an_error_message_if_a_key_file_is_not_provided()
    {
        $this->commandTester->execute(['targets' => ['.env.app', '.env.auth', 'staging']]);

        $this->assertContains('You must specify a valid key file to use for encryption', $this->commandTester->getDisplay());
    }
}
