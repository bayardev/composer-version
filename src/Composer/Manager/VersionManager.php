<?php
/**
 * Class VersionManager | Composer/Manager/VersionManager.php
 *
 * @package Bayard\Composer\Manager
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @author Rémi Colet            <remi.colet@icloud.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Composer\Manager;

/**
 * Gestionnaire de fichier version
 */
class VersionManager
{
    /**
     * Name file version
     * @var string
     */
    const VERSION_FILE = 'VERSION';

    /**
     * Version courante du projet
     * @var string
     */
    protected $currentVersion = "0.0.0";

    /**
     * Constructeur de la classe VersionManager
     */
    public function __construct()
    {
    }

    /**
     * Retourne le nom du fichier contenant la version
     * @return string
     */
    public function getVersionFile()
    {
        return self::VERSION_FILE;
    }

    /**
     * Retourne la version du projet contenu dans le fichier $VERSION_FILE
     * @return string
     */
    public function getAppVersion()
    {
        return $this->currentVersion;
        //return file_get_contents($this->getVersionFile());
    }
    
    /**
     * Permet d'ajouter dans le fichier $VERSION_FILE la version donner en paramêtre, si celui-ci est conforme à la gestion sémentique de version 2.0.0 [https://semver.org/]
     * @param string $version Version ajouter au fichier
     * @return int 1=OK, 0=Error
     */
    public function checkVersion($version)
    {
        if ($this->followConvention($version)) {
            $this->currentVersion = $version;
            return 1;
        } else {
            $explode_version = explode('.', $this->getAppVersion());
            switch ($version) {
                case 'major':
                    $explode_version[0] = ((int)$explode_version[0])+1;
                    $explode_version[1] = '0';
                    $explode_version[2] = '0';
                    break;
                case 'minor':
                    $explode_version[1] = ((int)$explode_version[1])+1;
                    $explode_version[2] = '0';
                    break;
                case 'patch':
                    $explode_version[2] = ((int)$explode_version[2])+1;
                    break;
                default:
                    return 0;
                    break;
            }
            $this->currentVersion = implode('.', $explode_version);
            return 1;
        }
    }

    /**
     * Vérifie la sémantique de version de $version
     * @param  string $version
     * @return boolean  Si c'est bon, il retourne "true", sinon "false"
     */
    public function followConvention($version)
    {
        return preg_match('#^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(-[[:graph:]]+[[:alnum:]]){0,1}#', $version);
    }

    /**
     * [checkVersionFile description]
     * @return [type] [description]
     */
    public function checkVersionFile()
    {
        if (file_exists($this->getVersionFile())) {
            $this->currentVersion = file_get_contents($this->getVersionFile());
        }
        return $this;
    }

    /**
     * [putVersionFile description]
     * @return [type] [description]
     */
    public function putVersionFile()
    {
        file_put_contents(self::VERSION_FILE, $this->getAppVersion());
    }
}
