<?php

namespace Bayard\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Bayard\Composer\Manager\VersionManager;
use Bayard\Composer\Manager\GitManager;

class VersionCommand extends BaseCommand
{
    protected function configure()
    {
        $this
        	->setName('version')
        	->setDescription('Plugin that helps with releasing semantically versioned composer packages or projects.')
        	->setDefinition(array(
                new InputOption('prefix', 'p', InputOption::VALUE_REQUIRED, "set tag prefix", ""),
                new InputOption('gpg-sign', 's', InputOption::VALUE_NONE, "sign tag with gpg key"),
                new InputArgument('new-version', InputArgument::OPTIONAL, "Type of update version (major|minor|patch or a direct version like 0.0.1)"),
        	 ))
        	->setHelp(<<<EOT
A composer plugin that helps with releasing semantically versioned composer packages or projects, automatically adding git tags.
EOT
			);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $versionManager = new VersionManager();
        $gitManager = new GitManager();

        $output->writeln("Start the composer plugin : VERSION");

        if(!file_exists($gitManager->getGitFile())) {
            $output->writeln("This is not GIT repository !");
            exit(502);
        }

        if(file_exists($versionManager->getVersionFile())) {
            if($input->getArgument('new-version')){
                $output->writeln("Old version project : ".$versionManager->getAppVersion());
                if(!$versionManager->setAppVersion($input->getArgument('new-version'))) {
                    $output->writeln("ERROR : major|minor|patch or a direct version like 0.0.1");
                    exit(502);
                }
                $output->writeln("New version project : ".$versionManager->getAppVersion());
            } else {
                $output->writeln("Project version : ".$versionManager->getAppVersion());
                exit();
            }
        } else {
            $output->writeln("File VERSION not exist");
            $version = "0.0.0";
            if($input->getArgument('new-version')){
                if(!$versionManager->followConvetion($input->getArgument('new-version'))) {
                    $output->writeln("I don't understand");
                    exit(502);
                }
                $version = $input->getArgument('new-version');
            }
            
            $anwser = $io->choice("Create this file ? [".$version."]", array("yes", "no"), "yes");
            if($anwser === "yes") {
                $versionManager->setAppVersion($version);
                $output->writeln("File created !");
            } else {
                exit($output->writeln("File not created !\nTHE END!!!"));
            }
        }

        if($versionManager->getAppVersion() !== "0.0.0") {
            $gitManager->setGpgSign($input->getOption('gpg-sign'));
            if($input->getOption('prefix'))
                $gitManager->setPrefixTag($input->getOption('prefix'));
            $gitManager->gitAdd($versionManager->getVersionFile(), $versionManager->getAppVersion());
            $output->writeln("VERSION file add and commit in repository");
            $gitManager->gitTag($versionManager->getAppVersion());
            $output->writeln("tag ".$input->getOption('prefix').$versionManager->getAppVersion()." add in repository");
        } 

        $output->writeln("THE END!!!");
    }
}