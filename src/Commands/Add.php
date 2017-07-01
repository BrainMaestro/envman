<?php

namespace BrainMaestro\Envman\Commands;

use BrainMaestro\Envman\Parser;
use BrainMaestro\Envman\Writer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Add extends Command
{
    protected function configure()
    {
        $this
            ->setName('add')
            ->setDescription('Add a new environment variable to your application')
            ->setHelp('This command allows you to add new environment variables')
            ->addArgument(
                'key',
                InputArgument::REQUIRED,
                'Key of new environment variable'
            )
            ->addArgument(
                'value',
                InputArgument::REQUIRED,
                'Value of new environment variable'
            )
            ->addArgument(
                'file',
                InputArgument::OPTIONAL,
                'File to store new environment variable',
                'example'
            )
            ->addOption('dir', 'd', InputOption::VALUE_REQUIRED, 'Path to env directory', '.')
            ->addOption('allow-duplicates', 'a', InputOption::VALUE_NONE, 'Allow duplication of env variables')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $key = strtoupper($input->getArgument('key'));
        $value = $input->getArgument('value');
        $file = ".env.{$input->getArgument('file')}";
        $directory = $input->getOption('dir');
        $duplicates = $input->getOption('allow-duplicates');

        $env = Parser::parse([$directory]);

        if ($directory !== '.') {
            $file = "{$directory}/{$file}";
        }

        if (! $env->add($key, $value, $file, $duplicates)) {
            $files = implode(', ', $env->files($key));
            $output->writeln("<error>{$key} already exists in {$files}</error>");
            return 1;
        }

        Writer::write($env);

        $output->writeln("Added <info>{$key}={$value}</info> to <info>{$file}</info>");
    }
}
