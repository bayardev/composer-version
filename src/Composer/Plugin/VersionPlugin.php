<?php
/**
 * Class VersionPlugin | Composer/Plugin/VersionPlugin.php
 *
 * @package Bayard\Composer\Plugin
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @author Rémi Colet            <remi.colet@icloud.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Composer\Plugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

/**
 * Class utiliser pour l'instanciation du plugin
 */
class VersionPlugin implements PluginInterface, Capable
{
    /**
     * Méthode appelé après la chargement du plugin
     * @param  Composer    $composer
     * @param  IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
    }

    /**
     * Permet de retourner les différentes capacitées du plugin
     * @return array Listes des capacitées du plugin
     */
    public function getCapabilities()
    {
        return array(
            'Composer\Plugin\Capability\CommandProvider' => 'Bayard\Composer\Plugin\Capability\CommandProvider' );
    }
}
