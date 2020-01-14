<?php

namespace Anomaly\Streams\Platform\Addon\Console;

use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Extension\Extension;
use Anomaly\Streams\Platform\Addon\Extension\ExtensionManager;
use Anomaly\Streams\Platform\Addon\Module\Module;
use Anomaly\Streams\Platform\Addon\Module\ModuleManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class AddonSeed
 *
 * @link   http://pyrocms.com/
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class AddonSeed extends Command
{

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'addon:seed';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed an addon.';

    /**
     * Execute the console command.
     *
     * @param ModuleManager    $modules
     * @param ExtensionManager $extensions
     */
    public function handle(ModuleManager $modules, ExtensionManager $extensions)
    {
        if (!$addon = app($this->argument('addon'))) {
            $this->error('The [' . $this->argument('addon') . '] could not be found.');
        }

        // $paths = array_filter(scandir($path = $addon->getPath('migrations')), function ($file) use ($path) {
        //     return is_file($path . DIRECTORY_SEPARATOR . $file);
        // });

        // $migrations = array_map(function ($file) use ($path) {
        //     return $path . DIRECTORY_SEPARATOR . $file;
        // }, $paths);

        //console()->call('migrate', ['--path' => implode(' ', $migrations), '--realpath' => true]);
        if (!class_exists($class = get_class($addon) . 'Seeder')) {

            $this->info("Nothing do seed [{$this->argument('addon')}].");

            return;
        }

        console()->call('db:seed', ['--class' => $class]);
    }

    /**
     * Get the command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['addon', InputArgument::REQUIRED, 'The addon to migrate.'],
        ];
    }
}