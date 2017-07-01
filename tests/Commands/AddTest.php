<?php

namespace BrainMaestro\Envman\Tests\Commands;

use BrainMaestro\Envman\Commands\Add;
use BrainMaestro\Envman\Tests\TestUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class AddTest extends TestCase
{
    use TestUtil;

    private $commandTester;
    private $data = [
        'key' => 'APP_NAME',
        'value' => 'env-test-app',
        'file' => 'app',
    ];

    public function setUp()
    {
        $this->commandTester = new CommandTester(new Add);
        $this->deleteEnv('.');
    }

    public function tearDown()
    {
        $this->deleteEnv('.');
    }

    /**
     * @test
     */
    public function it_adds_an_env_variable_that_does_not_already_exist()
    {
        $this->commandTester->execute($this->data);

        $this->assertContains('Added APP_NAME=env-test-app to .env.app', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_does_not_add_an_env_variable_that_already_exists()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app");
        $this->commandTester->execute($this->data);

        $this->assertContains('APP_NAME already exists in .env.app', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_adds_an_env_variable_that_already_exists_if_forced_to()
    {
        file_put_contents('.env.app', "APP_NAME=env-test-app");
        $this->commandTester->execute(array_merge($this->data, ['--allow-duplicates' => true]));

        $this->assertContains('Added APP_NAME=env-test-app to .env.app', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_adds_an_env_variable_to_a_file_in_a_custom_directory()
    {
        $this->commandTester->execute(array_merge($this->data, ['--dir' => 'environment']));

        $this->assertContains('Added APP_NAME=env-test-app to environment/.env.app', $this->commandTester->getDisplay());
        $this->deleteEnv('environment');
    }
}
