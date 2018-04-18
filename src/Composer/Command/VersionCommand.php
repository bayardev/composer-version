<?php

namespace Bayard\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Composer\Command\BaseCommand;

class VersionCommand extends BaseCommand
{
    protected function configure()
    {
        $this
        	->setName('version')
        	->setDescription('Show/Update/Modifie composer.json and/or git tag version')
        	->setDefinition(array(
        		new InputArgument('typeUp', InputArgument::OPTIONAL, "Type of update version (major|minor|patch)")
        	 ))
        	->setHelp(<<<EOT
The composer-version command display/update/create composer.json/git version
EOT
			);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
    	if(in_array($input->getArgument('typeUp'), array('major', 'minor', 'patch')))
    	{
    		$output->writeln($input->getArgument('typeUp'));
    		exec("cat VERSION", $out, $ret);
    		var_dump($out, $ret);
    		if($ret === 0)
    		{
    			var_dump(str_split('.', $out[0]));
    		}
    	} 
    	else 
    	{
    		$output->writeln('Still developing');
    	}
    }
}