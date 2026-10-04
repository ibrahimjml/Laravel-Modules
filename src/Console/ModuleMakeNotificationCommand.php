<?php

namespace Ibrahimjml\LaravelModules\Console;

class ModuleMakeNotificationCommand extends ModuleGeneratorCommand
{
    protected $signature = 'module:make-notification
        {module : The module name}
        {name : The notification name}
        {--database : Generate a database notification}
        {--force : Overwrite the notification if it already exists}';

    protected $description = 'Create a new notification for a module';

    protected $type = 'Notification';

    protected function getStub()
    {
        return $this->resolveStub($this->option('database') ? 'notification.database' : 'notification');
    }

    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Notifications';
    }
}
