<?php

namespace Bayardev\Composer;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

/**
* 
*/
class Plugin implements PluginInterface, Capable
{
	
	public function activate(Composer $composer, IOInterface $io) {

	}

	public function getCapabilities() {
		return array('Composer\Plugin\Capability\CommandProvider' => 'Bayardev\Composer\CommandProvider' );
	}

}