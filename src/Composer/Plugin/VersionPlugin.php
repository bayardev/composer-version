<?php

namespace Bayard\Composer\Plugin;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Plugin\Capable;

class VersionPlugin implements PluginInterface, Capable
{
	
	public function activate(Composer $composer, IOInterface $io) {
		//exit(var_dump($composer->getPluginManager()->getPlugins()));
	}

	public function getCapabilities() {
		return array('Composer\Plugin\Capability\CommandProvider' => 'Bayard\Composer\Plugin\Capability\CommandProvider' );
	}

}