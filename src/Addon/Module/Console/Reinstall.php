<?php

namespace Anomaly\Streams\Platform\Addon\Module\Console;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;

/**
 * Class Reinstall.
 *
 * @link          http://anomaly.is/streams-platform
 * @author        AnomalyLabs, Inc. <hello@anomaly.is>
 * @author        Ryan Thompson <ryan@anomaly.is>
 * @package       Anomaly\Streams\Platform\Stream\Console
 */
class Reinstall extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'module:reinstall';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reinstall a module.';

    /**
     * Execute the console command.
     */
    public function fire()
    {
        $this->call('module:uninstall', ['module' => $this->argument('module')]);
        $this->call('module:install', ['module' => $this->argument('module')]);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['module', InputArgument::REQUIRED, 'The module\'s dot namespace.'],
        ];
    }
}
