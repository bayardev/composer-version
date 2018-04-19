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
                new InputOption('root', 'r', InputOption::VALUE_REQUIRED, "Root to the project", "./"),
                new InputOption('prefix', 'p', InputOption::VALUE_REQUIRED, "set tag prefix"),
                new InputOption('gpg-sign', 's', InputOption::VALUE_REQUIRED, "sign tag with gpg key"),
                new InputArgument('new-version', InputArgument::OPTIONAL, "Type of update version (major|minor|patch or a direct version like 0.0.1)"),
        	 ))
        	->setHelp(<<<EOT
The composer-version command display/update/create composer.json/git version
EOT
			);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $return_root = $input->getOption("root");
        $project_root = substr($return_root, -1) == '/' ? $return_root : $return_root.'/';

        if(!file_exists($project_root."composer.json")) {
            $output->writeln("You are not aiming Composer project");
            exit(1);
        }

        if(file_exists($project_root.$this->NAME_VERSION_FILE)) {
            $this->VERSION_PROJECT = exec("cat ".$project_root.$this->NAME_VERSION_FILE);
            if($input->getArgument('new-version')){
                if(preg_match('#^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(-[[:graph:]]+[[:alnum:]]){0,1}#', $input->getArgument('new-version'))) {
                    $output->writeln("Old version project : ".$this->VERSION_PROJECT);
                    $this->VERSION_PROJECT = $input->getArgument('new-version');
                } else {
                    $explode_version = explode('.', $this->VERSION_PROJECT);
                    switch ($input->getArgument('new-version')) {
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
                            $output->writeln("ERROR : major|minor|patch or a direct version like 0.0.1");
                            exit(1);
                            break;
                    }
                    $output->writeln("Old version project : ".$this->VERSION_PROJECT);
                $this->VERSION_PROJECT = implode('.', $explode_version);
                }
                exec("echo ".$this->VERSION_PROJECT." > ".$project_root.$this->NAME_VERSION_FILE);
                $output->writeln("New version project : ".$this->VERSION_PROJECT);
            } else {
                $output->writeln("Project version : ".$this->VERSION_PROJECT);
            }
        } else {
            if(file_exists($project_root.$this->NAME_GIT_FOLDER)) {
                $output->writeln("Git repo found : under development!!!");
            }

            if($input->getArgument('new-version')){
                if(preg_match('#^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(-[[:graph:]]+[[:alnum:]]){0,1}#', $input->getArgument('new-version'))) {
                    $this->VERSION_PROJECT = $input->getArgument('new-version');
                } else {
                    $output->writeln("I don't understand");
                }
            }
            $output->writeln("File VERSION not exist");
            $anwser = $io->choice("Create this file ? [".$this->VERSION_PROJECT."]", array("yes", "no"), "yes");
            if($anwser === "yes") {
                exec("echo ".$this->VERSION_PROJECT. " > ".$project_root.$this->NAME_VERSION_FILE);
            }
        }

        $output->writeln("THE END!!!");
    }
}