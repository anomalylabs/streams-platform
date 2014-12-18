<?php namespace Anomaly\Streams\Platform\Addon\Module\Command;

use Anomaly\Streams\Platform\Addon\Module\Event\ModuleUninstalledEvent;
use Anomaly\Streams\Platform\Addon\Module\Module;
use Anomaly\Streams\Platform\Addon\Module\ModuleInstaller;
use Anomaly\Streams\Platform\Contract\InstallableInterface;

/**
 * Class UninstallModuleCommandHandler
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Addon\Module\Command
 */
class UninstallModuleCommandHandler
{

    /**
     * Handle the command.
     *
     * @param  UninstallModuleCommand $command
     * @return bool
     */
    public function handle(UninstallModuleCommand $command)
    {
        $module = app('streams.module.' . $command->getModule());

        if ($installer = $module->newInstaller()) {
            $this->runInstallers($module, $installer);
        }

        app('events')->fire('streams::module.uninstalled', new ModuleUninstalledEvent($module));

        return true;
    }

    /**
     * Run the installers.
     *
     * @param Module          $module
     * @param ModuleInstaller $installer
     */
    protected function runInstallers(Module $module, ModuleInstaller $installer)
    {
        foreach ($installer->getInstallers() as $installer) {
            $installer = $this->resolveInstaller($module, $installer);

            $this->runUninstall($installer);
        }
    }

    /**
     * Run the installer's uninstall method.
     *
     * @param InstallableInterface $installer
     */
    protected function runUninstall(InstallableInterface $installer)
    {
        $installer->install();
    }

    /**
     * Resolve the installer.
     *
     * @param  Module $module
     * @param         $installer
     * @return mixed
     */
    protected function resolveInstaller(Module $module, $installer)
    {
        return app($installer, ['addon' => $module]);
    }
}
