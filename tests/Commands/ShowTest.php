<?php

namespace BrainMaestro\Envman\Tests\Commands;

use BrainMaestro\Envman\Commands\Show;
use BrainMaestro\Envman\Tests\TestUtil;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class ShowTest extends TestCase
{
    use TestUtil;

    private $commandTester;

    public function setUp()
    {
        $this->commandTester = new CommandTester(new Show);

        file_put_contents('.env.app', "APP_NAME=env-test-app\nAPP_KEY=abcdef");
        file_put_contents('.env.auth', "APP_NAME=env-test-auth\n\$AUTH_SECRET=very-secret-key\n#AUTH_API=auth");
    }

    /**
     * @test
     */
    public function it_lists_all_environment_variables()
    {
        $this->commandTester->execute([]);

        $this->assertContains('APP_KEY', $this->commandTester->getDisplay());
        $this->assertContains('APP_NAME', $this->commandTester->getDisplay());
        $this->assertContains('AUTH_SECRET', $this->commandTester->getDisplay());
        $this->assertContains('AUTH_API', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_lists_only_commented_environment_variables()
    {
        $this->commandTester->execute(['--show-only-comments' => true]);

        $this->assertContains('AUTH_API', $this->commandTester->getDisplay());

        $this->assertNotContains('APP_KEY', $this->commandTester->getDisplay());
        $this->assertNotContains('APP_NAME', $this->commandTester->getDisplay());
        $this->assertNotContains('AUTH_SECRET', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_lists_only_duplicate_environment_variables()
    {
        $this->commandTester->execute(['--show-only-duplicates' => true]);

        $this->assertContains('APP_NAME', $this->commandTester->getDisplay());

        $this->assertNotContains('APP_KEY', $this->commandTester->getDisplay());
        $this->assertNotContains('AUTH_API', $this->commandTester->getDisplay());
        $this->assertNotContains('AUTH_SECRET', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_lists_only_encrypted_environment_variables()
    {
        $this->commandTester->execute(['--show-only-encrypted' => true]);

        $this->assertContains('AUTH_SECRET', $this->commandTester->getDisplay());

        $this->assertNotContains('APP_KEY', $this->commandTester->getDisplay());
        $this->assertNotContains('APP_NAME', $this->commandTester->getDisplay());
        $this->assertNotContains('AUTH_API', $this->commandTester->getDisplay());
    }

    /**
     * @test
     */
    public function it_lists_only_regular_environment_variables()
    {
        $this->commandTester->execute(['--show-only-regular' => true]);

        $this->assertContains('APP_KEY', $this->commandTester->getDisplay());

        $this->assertNotContains('APP_NAME', $this->commandTester->getDisplay());
        $this->assertNotContains('AUTH_API', $this->commandTester->getDisplay());
        $this->assertNotContains('AUTH_SECRET', $this->commandTester->getDisplay());
    }

    public function tearDown()
    {
        $this->delete('.', 'app', 'auth');
    }
}
