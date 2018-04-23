<?php
/**
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Composer\Manager;

class VersionManager
{
    const VERSION_FILE = 'VERSION';

    //construit avec la root
    public function __construct()
    {
    }

    public function getAppVersion()
    {
        return file_get_contents($this->getVersionFile());
    }

    public function setAppVersion($version)
    {
        if($this->followConvetion($version)) {
            file_put_contents($this->getVersionFile(), $version);
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
            
            file_put_contents($this->getVersionFile(), implode('.', $explode_version));
            return 1;
        }
    }

    public function followConvetion($verison)
    {
        return preg_match('#^[0-9]{1,2}\.[0-9]{1,2}\.[0-9]{1,2}(-[[:graph:]]+[[:alnum:]]){0,1}#', $verison);
    }

    public function getVersionFile()
    {
        return self::VERSION_FILE;
    }
}