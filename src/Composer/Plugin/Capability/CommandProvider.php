<?php

namespace Bayard\Composer\Plugin\Capability;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return array(new \Bayard\Composer\Command\VersionCommand);
    }
}