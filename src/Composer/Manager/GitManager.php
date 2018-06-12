<?php
/**
 * Class GitManager | Composer/Manager/GitManager.php
 *
 * @package Bayard\Composer\Manager
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @author Rémi Colet            <remi.colet@icloud.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Composer\Manager;

/**
 * Gestionnaire de repository Git
 */
class GitManager
{
    /**
     * Name git file
     */
    const GIT_DIR = '.git';

    /**
     * Option gpg-sign
     * @var string
     */
    protected $gpgSign;

    /**
     * Préfix du tag
     * @var string
     */
    protected $prefixTag;

    /**
     * Constructeur la classe GitManager
     * @param boolean $gpg_sign   Option gpg-sign (default=false)
     * @param string  $prefix_tag Préfix du tag (defaul="")
     */
    public function __construct($gpg_sign = false, $prefix_tag = "")
    {
        $this->setGpgSign($gpg_sign)
             ->setPrefixTag($prefix_tag);
    }

    /**
     * Configuration de l'attribut $gpgSign
     * @param boolean $gpg_sign Si true $gpgSign="-s", sinon $gpgSign="-a"
     * @return GitManager
     */
    public function setGpgSign($gpg_sign)
    {
        $this->gpgSign = $gpg_sign ? "-s" : "-a";
        return $this;
    }

    /**
     * retourne la valeur de l'attribut $gpgSign
     * @param boolean $gpg_sign Si true $gpgSign="-s", sinon $gpgSign="-a"
     * @return GitManager
     */
    public function getGpgSign()
    {
        return $this->gpgSign;
    }

    /**
     * Configuration de l'attribut $prefixTag
     * @param string $prefix_tag
     * @return GitManager
     */
    public function setPrefixTag($prefix_tag)
    {
        $this->prefixTag = $prefix_tag;
        return $this;
    }

    /**
     * retourne la valeur de l'attribut $gpgSign
     * @param boolean $gpg_sign Si true $gpgSign="-s", sinon $gpgSign="-a"
     * @return GitManager
     */
    public function getPrefixTag()
    {
        return $this->prefixTag;
    }

    /**
     * Retourne le nom du dossier du repository
     * @return string
     */
    public function getGitDir()
    {
        return self::GIT_DIR;
    }

    /**
     * [isGitRepository description]
     * @return boolean [description]
     */
    public function isGitRepository()
    {
        return is_dir($this->getGitDir());
    }

    /**
     * Lance un commande "git add" sur le fichier passer en paramêtre
     * @param  string $file
     * @return GitManager
     */
    public function gitAdd($file)
    {
        shell_exec("git add ".$file);
        return $this;
    }

    /**
     * Lance la commande "git commit" avec comme message "New version $version"
     * @param  string $file Fichier que l'on commit
     * @param  string $version Nouvelle version que l'on commit
     * @return GitManager
     */
    public function gitCommit($file, $version)
    {
        shell_exec("git commit -m \"New Version : ".$version."\" ".$file);
        return $this;
    }

    /**
     * Lance la commande "git tag"
     * le tag sera :
     *     - l'option donner grâce à l'attribut $gpgSign,
     *     - le prefix contenu dans l'attribut $prefixTag concaténer à $tag,
     *     - le message "New version $tag",
     *     - et le dernier commit
     * @param  string $tag Tag ajouter
     * @return GitManager
     */
    public function gitTag($tag)
    {
        shell_exec("git tag "
            .$this->gpgSign." "
            .$this->prefixTag.$tag." -m \"New version "
            .$tag."\" \$(git log --format=\"%H\" -n 1)");
        return $this;
    }

    /**
     * Exécution du git add, git commit et git tag
     * @param  string $file
     * @param  string $version
     * @return GitManager
     */
    public function gitAddNewTag($file, $version)
    {
        $this->gitAdd($file)
            ->gitCommit($file, $version)
            ->gitTag($version);
        return $this;
    }
}
