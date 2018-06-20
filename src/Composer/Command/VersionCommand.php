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
     * Valeur de l'argument "new-version"
     * @var bool|string
     */
    protected $newVersionArg;

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
        $this->newVersionArg = $input->getArgument('new-version');
        return $this;
    }

    /**
     * Vérifie que nous nous trouvons dans un bonne environnement pour exécuter la commande
     * @param  OutputInterface $output
     * @return VersionCommand
     */
    protected function initialCheck()
    {
        if (!$this->gitManager->isGitRepository()) {//Problème nom
            $this->io->error("502 : This is not GIT repository !");
        }
        return $this;
    }

    /**
     * Affiche la version courante
     * @return VersionCommand
     */
    protected function getCurrentVersion()
    {
        $this->versionManager->checkVersionFile();
        $this->io->text("Project version : ".$this->versionManager->getAppVersion());
        return $this;
    }

    /**
     * Vérifie la sémantique de la version passer en argument
     * @return VersionCommand|bool
     */
    protected function getArguments()
    {
        $this->versionManager->checkVersionFile();
        if (!$this->versionManager->checkVersion($this->newVersionArg)) {
            $this->io->error("400 : Only accept Semantic Version major.minor.patch[-pre_release]\n
                See https://semver.org/");
            return false;
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
     * Permet de faire la monter de version
     * par rapport à l'argument "new-version"
     * passer par l'utilisateur
     * @return VersionCommand|bool
     */
    protected function getNewVersion()
    {
        $this->io->text("Start the composer plugin : VERSION");
        if ($this->getArguments() === false) {
            return false;
        }
        $this->io->text("New Version : ".$this->versionManager->getAppVersion());
        $this->gitManagement();
        $this->io->text("New version commit and tag update");
        $this->io->text("THE END!!!");
        return $this;
    }

    /**
     * Execution de la commande "version"
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return VersionCommand
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->init($input, $output);
        $this->initialCheck();
        if ($input->getArgument('new-version')) {
            if ($this->getNewVersion() === false) {
                return 1;
            }
        } else {
            $this->getCurrentVersion();
        }
        return 0;
    }
}
