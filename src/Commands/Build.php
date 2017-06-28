<?php

namespace BrainMaestro\Envman\Commands;

use BrainMaestro\Envman\Parser;
use BrainMaestro\Envman\Writer;
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
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directories = $input->getArgument('directories');
        $env = Parser::parse($directories);
        $file = fopen('.env', 'w+');

        foreach ($env->all() as $key => $entry) {
            fwrite($file, "{$key}={$entry['value']}\n");
        }

        fclose($file);
    }
}
