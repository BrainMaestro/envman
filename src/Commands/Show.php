<?php

namespace BrainMaestro\Envman\Commands;

use BrainMaestro\Envman\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Show extends Command
{
    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Shows a list of all environment variables from all .env.* files')
            ->setHelp('This command shows you an overview of your env')
            ->addArgument(
                'directories',
                InputArgument::IS_ARRAY,
                'Directories to search for .env.* files',
                ['.']
            )
            ->addOption('show-only-comments', 'c', InputOption::VALUE_NONE, 'Show only commented variables')
            ->addOption('show-only-duplicates', 'd', InputOption::VALUE_NONE, 'Show only duplicate variables')
            ->addOption('show-only-encrypted', 'e', InputOption::VALUE_NONE, 'Show only encrypted variables')
            ->addOption('show-only-regular', 'r', InputOption::VALUE_NONE, 'Show only regular variables')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $directories = $input->getArgument('directories');


        $env = Parser::parse($directories);
        $table = new Table($output);

        $table->setHeaders(['Key', 'Value', 'File']);

        foreach ($env->all() as $key => $entry) {
            $comment = $key[0] === '#';
            $duplicate = $env->entries($key) > 1;
            $encrypted = $key[0] === '$';

            if ($this->skip($input, $comment, $duplicate, $encrypted)) {
                continue;
            }

            list($open, $close) = $this->getTag($comment, $duplicate, $encrypted);

            $key = $open . preg_replace('/^[#\$]/', '', $key) . $close;
            $table->addRow([$key, $entry['value'], $entry['file']]);
        }

        $table->render();
    }

    /**
     * Get color tag
     *
     * @param bool $comment
     * @param bool $duplicate
     * @param bool $encrypted
     * @return array
     */
    private function getTag(bool $comment, bool $duplicate, bool $encrypted): array
    {
        if ($comment) {
            return ['<fg=white>', '</>'];
        }

        if ($duplicate) {
            return ['<comment>', '</comment>'];
        }

        if ($encrypted) {
            return ['<fg=red>', '</>'];
        }

        return ['<info>', '</info>'];
    }

    /**
     * Decide whether to skip displaying the current variable
     *
     * @param InputInterface $input
     * @param bool $comment
     * @param bool $duplicate
     * @param bool $encrypted
     * @return bool
     */
    private function skip(InputInterface $input, bool $comment, bool $duplicate, bool $encrypted): bool
    {
        $regular = ! $comment && ! $duplicate && ! $encrypted;

        return ($input->getOption('show-only-comments') && ! $comment) ||
                ($input->getOption('show-only-duplicates') && ! $duplicate) ||
                ($input->getOption('show-only-encrypted') && ! $encrypted) ||
                ($input->getOption('show-only-regular') && ! $regular);
    }
}
