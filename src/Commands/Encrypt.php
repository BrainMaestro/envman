<?php

namespace BrainMaestro\Envman\Commands;

use BrainMaestro\Envman\Env;
use BrainMaestro\Envman\Parser;
use BrainMaestro\Envman\Writer;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Encrypt extends Command
{
    protected function configure()
    {
        $this
            ->setName('encrypt')
            ->setDescription('Encrypts all environment variables in a file or directory')
            ->setHelp('This command allows you to encrypt your environment variables')
            ->addArgument(
                'targets',
                InputArgument::IS_ARRAY,
                'Files or directories with .env.* files to be encrypted'
            )
            ->addOption(
                'key',
                'k',
                InputOption::VALUE_REQUIRED,
                'Key file for encryption'
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $targets = $input->getArgument('targets');
        $keyFile = $input->getOption('key');

        if ($keyFile === null || ! is_file($keyFile)) {
            $output->writeln('<error>You must specify a valid key file to use for encryption</error>');
            return 1;
        }

        if (count($targets) === 0) {
            $output->writeln('<error>No files or directories specified for encryption</error>');
            return 1;
        }

        $key = Key::loadFromAsciiSafeString(file_get_contents($keyFile));

        $env = Parser::parse(array_filter($targets, 'is_dir'), array_filter($targets, 'is_file'));
        $count = 0;
        foreach ($env->all() as $envKey => &$entry) {
            if (! $env->isEncrypted($envKey) && ! $env->isComment($envKey)) {
                $env->encryptKey($envKey);
                $count++;
                $entry['value'] = Crypto::encrypt($entry['value'], $key);
            }
        }

        Writer::write($env);

        $output->writeln("Encrypted <info>{$count}</info> key(s)");
    }
}
