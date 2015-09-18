<?php

namespace Anomaly\Streams\Platform\Addon\Module;

use Anomaly\Streams\Platform\Addon\Module\Contract\ModuleRepositoryInterface;

/**
 * Class ModuleRepository.
 *
 * @link    http://anomaly.is/streams-platform
 * @author  AnomalyLabs, Inc. <hello@anomaly.is>
 * @author  Ryan Thompson <ryan@anomaly.is>
 * @package Anomaly\Streams\Platform\Addon\Module
 */
class ModuleRepository implements ModuleRepositoryInterface
{
    /**
     * The module model.
     *
     * @var
     */
    protected $model;

    /**
     * Create a new ModuleRepository instance.
     *
     * @param ModuleModel $model
     */
    public function __construct(ModuleModel $model)
    {
        $this->model = $model;
    }

    /**
     * Return all modules in the database.
     *
     * @return mixed
     */
    public function all()
    {
        return $this->model->all();
    }

    /**
     * Create a module record.
     *
     * @param Module $module
     * @return bool
     */
    public function create(Module $module)
    {
        $instance = $this->model->newInstance();

        $instance->namespace = $module->getNamespace();
        $instance->installed = false;
        $instance->enabled   = false;

        return $instance->save();
    }

    /**
     * Delete a module record.
     *
     * @param  Module $module
     * @return ModuleModel
     */
    public function delete(Module $module)
    {
        $module = $this->model->findByNamespace($module->getNamespace());

        if ($module) {
            $module->delete();
        }

        return $module;
    }

    /**
     * Mark a module as installed.
     *
     * @param  Module $module
     * @return bool
     */
    public function install(Module $module)
    {
        $module = $this->model->findByNamespaceOrNew($module->getNamespace());

        $module->installed = true;
        $module->enabled   = true;

        return $module->save();
    }

    /**
     * Mark a module as uninstalled.
     *
     * @param  Module $module
     * @return bool
     */
    public function uninstall(Module $module)
    {
        $module = $this->model->findByNamespace($module->getNamespace());

        $module->installed = false;
        $module->enabled   = false;

        return $module->save();
    }

    /**
     * Mark a module as disabled.
     *
     * @param Module $module
     * @return bool
     */
    public function disable(Module $module)
    {
        $module = $this->model->findByNamespace($module->getNamespace());

        $module->enabled = false;

        return $module->save();
    }

    /**
     * Mark a module as enabled.
     *
     * @param Module $module
     * @return bool
     */
    public function enabled(Module $module)
    {
        $module = $this->model->findByNamespace($module->getNamespace());

        $module->enabled = true;

        return $module->save();
    }
}
