<?php

namespace Bayard\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;

class VersionCommand extends BaseCommand
{

    protected $NAME_VERSION_FILE = 'VERSION';
    protected $NAME_GIT_FOLDER = '.git';
    protected $VERSION_PROJECT = "0.0.0";

    protected function configure()
    {
        $this
        	->setName('version')
        	->setDescription('Show/Update/Modifie composer.json and/or git tag version')
        	->setDefinition(array(
        		new InputArgument('typeUp', InputArgument::OPTIONAL, "Type of update version (major|minor|patch)"),
                new InputOption('root', 'r', InputOption::VALUE_REQUIRED, "Root to the project", "./"),
        	 ))
        	->setHelp(<<<EOT
The composer-version command display/update/create composer.json/git version
EOT
			);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $return_path = $input->getOption("root");
        $project_path = substr($return_path, -1) == '/' ? $return_path : $return_path.'/';

        if(!file_exists($project_path."composer.json")) {
            $output->writeln("You are not aiming Composer project");
            exit(1);
        }

        if(file_exists($project_path.$this->NAME_VERSION_FILE)) {
            $this->VERSION_PROJECT = exec("cat ".$project_path.$this->NAME_VERSION_FILE);
            if(in_array($input->getArgument('typeUp'), array('major', 'minor', 'patch')) || preg_match('#^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}#', $input->getArgument('typeUp'))) {
                $explode_version = explode('.', $this->VERSION_PROJECT);
                switch ($input->getArgument('typeUp')) {
                    case 'major':
                        $explode_version[0]++;
                        $explode_version[1] = '0';
                        $explode_version[2] = '0';
                        break;
                    case 'minor':
                        $explode_version[1]++;
                        $explode_version[2] = '0';
                        break;
                    case 'patch':
                        $explode_version[2]++;
                        break;
                    default:
                        $output->writeln("ERROR 404!!! WTF!!");
                        exit(1);
                        break;
                }
                $output->writeln("Old version project : ".$this->VERSION_PROJECT);
                $this->VERSION_PROJECT = implode('.', $explode_version);
                exec("echo ".$this->VERSION_PROJECT." > ".$project_path.$this->NAME_VERSION_FILE);
                $output->writeln("New version project : ".$this->VERSION_PROJECT);
            } else {
                $output->writeln("Project version : ".$this->VERSION_PROJECT);
            }
        } else {
            $output->writeln("File VERSION not exist");
            $anwser = $io->choice("Create this file ? ", array("yes", "no"), "yes");
            if($anwser === "yes") {
                exec("echo ".$this->VERSION_PROJECT. " > ".$project_path.$this->NAME_VERSION_FILE);
            }

            $output->writeln("THE END!!!");
        }
    }
}