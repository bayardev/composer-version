<?php

namespace Bayardev\Composer;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Composer\Command\BaseCommand;

class Command extends BaseCommand
{
    protected function configure()
    {
        $this
        	->setName('composer-version')
        	->setDescription('Show/Update/Modifie composer.json and/or git tag version')
        	->setHelp(<<<EOT
The composer-version command display/update/create composer.json/git version
EOT
			);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Still developing');
    }
}