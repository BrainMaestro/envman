<?php

namespace BrainMaestro\Envman\Commands;

use BrainMaestro\Envman\Parser;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Build extends Command
{
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Builds a .env with environment variables from all .env.* files')
            ->setHelp('This command allows you to setup your .env file')
            ->addArgument(
                'directories',
                InputArgument::IS_ARRAY,
                'Directories to search for .env.* files',
                ['.']
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
        $directories = $input->getArgument('directories');
        $keyFile = $input->getOption('key');

        $env = Parser::parse($directories);
        $file = fopen('.env', 'w+');
        if ($keyFile && is_file($keyFile)) {
            $key = Key::loadFromAsciiSafeString(file_get_contents($keyFile));
        }

        $count = 0;
        foreach ($env->all() as $envKey => $entry) {
            if ($env->isComment($envKey)) {
                $output->writeln(
                    "<fg=white>Skipped {$envKey} ({$entry['file']}) - Commented out</>",
                    OutputInterface::VERBOSITY_VERBOSE
                );
                continue;
            }

            if ($env->isEncrypted($envKey)) {
                if (!isset($key)) {
                    $output->writeln(
                        "<fg=red>Skipped {$envKey} ({$entry['file']}) - No key to decrypt</>",
                        OutputInterface::VERBOSITY_VERBOSE
                    );
                    continue;
                }

                try {
                    $entry['value'] = Crypto::decrypt($entry['value'], $key);
                } catch (WrongKeyOrModifiedCiphertextException $e) {
                    $output->writeln('<error>The key you provided is incorrect</error>');
                    return 1;
                }

                $envKey = str_replace('$', '', $envKey);
            }

            $count++;
            fwrite($file, "{$envKey}={$entry['value']}\n");
        }

        fclose($file);
        $output->writeln("Built <info>{$count}</info> environment variable(s)");
    }
}
