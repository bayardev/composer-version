<?php

namespace Bayardev\Composer;

use bayardev\plugin\gitplusplus\CommandProvider as CommandProviderCapability;

class CommandProvider implements CommandProviderCapability
{
    public function getCommands()
    {
        return array(new Command);
    }
}