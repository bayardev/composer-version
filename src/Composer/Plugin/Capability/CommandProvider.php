<?php
/**
 * Class CommandProvider | Composer/Plugin/Capability/CommandProvider.php
 *
 * @package Bayard\Composer\Plugin\Capability
 * @author Massimiliano Pasquesi <massimiliano.pasquesi@bayard-presse.com>
 * @author Rémi Colet            <remi.colet@icloud.com>
 * @copyright 2016 Bayard Presse (http://www.groupebayard.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

namespace Bayard\Composer\Plugin\Capability;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

/**
 * Permet la création de commande Composer depuis un plugin
 */
class CommandProvider implements CommandProviderCapability
{
    /**
     * Retourne les commandes du plugin
     * @return array Listes de différentes commandes
     */
    public function getCommands()
    {
        return array(new \Bayard\Composer\Command\VersionCommand);
    }
}
