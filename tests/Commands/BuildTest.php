<?php

namespace BrainMaestro\Envman\Tests\Commands;

use BrainMaestro\Envman\Commands\Build;
use BrainMaestro\Envman\Commands\Encrypt;
use BrainMaestro\Envman\Commands\GenerateKey;
use BrainMaestro\Envman\Tests\TestUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class BuildTest extends TestCase
{
    use TestUtil;

    private $commandTester;

    public function setUp()
    {
        $this->commandTester = new CommandTester(new Build);
    }

    public function tearDown()
    {
        unlink('.env');
    }

    /**
     * @test
     */
    public function it_builds_an_env_file_from_values_in_all_other_env_files()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app\nAPP_KEY=abcdef");
        file_put_contents('.env.auth', "AUTH_SECRET=very-secret-key\nAUTH_API=auth");

        $this->commandTester->execute([]);

        $this->assertEquals(
            "APP_KEY=abcdef\nAPP_NAME=env-test-app\nAUTH_API=auth\nAUTH_SECRET=very-secret-key\n",
            file_get_contents('.env')
        );

        $this->delete('.', 'app', 'auth');
    }

    /**
     * @test
     */
    public function it_builds_an_env_file_from_values_in_all_other_env_files_in_different_directories()
    {
        mkdir('staging');
        file_put_contents('staging/.env.app', "APP_NAME=env-test-app\nAPP_KEY=abcdef");
        file_put_contents('.env.auth', "AUTH_SECRET=very-secret-key\nAUTH_API=auth");

        $this->commandTester->execute(['directories' => ['.', 'staging']]);

        $this->assertEquals(
            "APP_KEY=abcdef\nAPP_NAME=env-test-app\nAUTH_API=auth\nAUTH_SECRET=very-secret-key\n",
            file_get_contents('.env')
        );

        $this->delete('.', 'auth');
        $this->delete('staging', 'app');
    }

    /**
     * @test
     */
    public function it_does_not_build_comments_into_the_env_file()
    {
        file_put_contents('.env.auth', "#AUTH_SECRET=very-secret-key\nAUTH_API=auth");

        $this->commandTester->execute(['directories' => ['.', 'staging']]);

        $this->assertEquals(
            "AUTH_API=auth\n",
            file_get_contents('.env')
        );

        $this->delete('.', 'auth');
    }

    /**
     * @test
     */
    public function it_decrypts_and_builds_encrypted_values_if_a_key_is_provided()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app");
        (new CommandTester(new GenerateKey))->execute(['key-name' => 'env-test-key']);
        (new CommandTester(new Encrypt))->execute(['--key' => 'env-test-key']);

        $this->commandTester->execute(['--key' => 'env-test-key']);

        $this->assertContains('Built 1 environment variable(s)', $this->commandTester->getDisplay());

        $this->delete('.', 'app');
        unlink('env-test-key');
    }

    /**
     * @test
     */
    public function it_ignores_encrypted_values_if_no_key_is_provided()
    {
        file_put_contents('.env.app', "\$APP_NAME=env-test-app");

        $this->commandTester->execute([]);

        $this->assertContains('Built 0 environment variable(s)', $this->commandTester->getDisplay());

        $this->delete('.', 'app');
    }

    /**
     * @test
     */
    public function it_fails_if_an_incorrect_key_is_provided()
    {
        file_put_contents('.env.app', "\$APP_NAME=env-test-app");
        (new CommandTester(new GenerateKey))->execute(['key-name' => 'env-test-key']);
        $this->commandTester->execute(['--key' => 'env-test-key']);

        $this->assertContains('The key you provided is incorrect', $this->commandTester->getDisplay());

        $this->delete('.', 'app');
        unlink('env-test-key');
    }
}
