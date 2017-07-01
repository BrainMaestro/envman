<?php

namespace BrainMaestro\Envman\Commands;

use BrainMaestro\Envman\Parser;
use Defuse\Crypto\Key;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateKey extends Command
{
    protected function configure()
    {
        $this
            ->setName('generate:key')
            ->setDescription('Generates a secret key to be used for encrypting environment variables')
            ->setHelp('This command allows you to generate a secret key for encryption')
            ->addArgument(
                'key-name',
                InputArgument::REQUIRED,
                'Name of the key to be generated'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $keyName = $input->getArgument('key-name');

        $key = Key::createNewRandomKey();
        file_put_contents($keyName, $key->saveToAsciiSafeString());

        $output->writeln("Key saved to <info>{$keyName}</info>");
        $output->writeln('<comment><fg=red>CAUTION:</> Keep this key somewhere safe and <fg=red>DO NOT</> check it into your repository</comment>');
    }
}
