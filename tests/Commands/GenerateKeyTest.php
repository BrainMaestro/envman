<?php

namespace BrainMaestro\Envman\Tests\Commands;

use BrainMaestro\Envman\Commands\GenerateKey;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

class GenerateKeyTest extends TestCase
{
    /**
     * @test
     */
    public function it_generates_a_new_key()
    {
        $commandTester = new CommandTester(new GenerateKey);

        $commandTester->execute(['key-name' => 'env-test-key']);

        $this->assertContains('Key saved to env-test-key', $commandTester->getDisplay());
        $this->assertContains('CAUTION: Keep this key somewhere safe and DO NOT check it into your repository', $commandTester->getDisplay());

        unlink('env-test-key');
    }
}
