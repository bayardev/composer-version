<?php
/**
 * Class VersionCommand | Composer/Command/VersionCommand.php
 *
 * @package Bayard\Composer\Command
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @author Rémi Colet            <remi.colet@icloud.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Composer\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Composer\Command\BaseCommand;
use Symfony\Component\Console\Style\SymfonyStyle;
use Bayard\Composer\Manager\VersionManager;
use Bayard\Composer\Manager\GitManager;

/**
 * Commande "version"
 */
class VersionCommand extends BaseCommand
{

    /**
     * Permet l'affichage de message sur terminal
     * @var SymfonyStyle
     */
    protected $io;

    /**
     * Gestion des version
     * @var VersionManager
     */
    protected $versionManager;

    /**
     * Gestion des répository
     * @var GitManager
     */
    protected $gitManager;

    /**
     * Configuration de notre commande "version"
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('version')
            ->setDescription('Plugin that helps with releasing semantically versioned composer packages or projects.')
            ->setDefinition(array(
                new InputOption('prefix', 'p', InputOption::VALUE_REQUIRED, "set tag prefix", ""),
                new InputOption('gpg-sign', 's', InputOption::VALUE_NONE, "sign tag with gpg key"),
                new InputArgument(
                    'new-version',
                    InputArgument::OPTIONAL,
                    "Type of update version (major|minor|patch or a direct version like 0.0.1)"
                ),
             ))
            ->setHelp("A composer plugin that helps with releasing 
                semantically versioned composer packages or projects, 
                automatically adding git tags.");
    }

    /**
     * Initialisation des attribut
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return VersionCommand
     */
    protected function init(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->versionManager = new VersionManager();
        $this->gitManager = new GitManager($input->getOption('gpg-sign'), $input->getOption('prefix'));
        return $this;
    }

    /**
     * Vérifie que nous nous trouvons dans un bonne environnement pour exécuter la commande
     * @param  OutputInterface $output
     * @return VersionCommand
     */
    protected function initialCheck(OutputInterface $output)
    {
        if (!$this->gitManager->isGitRepository()) {//Problème nom
            $output->writeln("This is not GIT repository !");
            exit(502);
        }
        return $this;
    }

    /**
     * Initialise la version courante et vérifie si il existe un argument
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return VersionCommand
     */
    protected function getCurrentVersion(InputInterface $input, OutputInterface $output)
    {
        $this->versionManager->checkVersionFile();
        if (!$input->getArgument('new-version')) {
            $output->writeln("Project version : ".$this->versionManager->getAppVersion());
            exit();
        }
        return $this;
    }

    /**
     * Vérifie la sémantique de la version passer en argument
     * @param  InputInterface $input
     * @param  OutputInterface $output
     * @return VersionCommand
     */
    protected function getArguments(InputInterface $input, OutputInterface $output)
    {
        if (!$this->versionManager->checkVersion($input->getArgument('new-version'))) {
            $output->writeln("Only accept Semantic Version major.minor.patch[-pre_release]");
            $output->writeln("See https://semver.org/");
            exit(400);
        }
        $this->versionManager->putVersionFile();
        return $this;
    }

    /**
     * Réaliser l'ajout et le commit de la mise à jour du
     * fichier contenant la version et met à jour le tag du repository
     * @return VersionCommand
     */
    protected function gitManagement()
    {
        if ($this->versionManager->getAppVersion() !== "0.0.0") {
            $this->gitManager->gitAddNewTag(
                $this->versionManager->getVersionFile(),
                $this->versionManager->getAppVersion()
            );
        }
        return $this;
    }

    /**
     * Execution de la commande "version"
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);

        $this->initialCheck($output);

        $output->writeln("Start the composer plugin : VERSION");
        
        $this->getCurrentVersion($input, $output);
        $this->getArguments($input, $output);

        $output->writeln("New Version : ".$this->versionManager->getAppVersion());

        $this->gitManagement();

        $output->writeln("New version commit and tag update");

        $output->writeln("THE END!!!");
    }
}
