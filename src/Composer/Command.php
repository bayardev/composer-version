<?php

namespace Bayardev\Composer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class Command extends BaseCommand
{
    protected function configure()
    {
        $this->setName('composer-version');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Still developing');
    }
}