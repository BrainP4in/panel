<?php

namespace Pterodactyl\Providers;

use Illuminate\Support\ServiceProvider;
use Pterodactyl\Repositories\Daemon\FileRepository;
use Pterodactyl\Repositories\Daemon\PowerRepository;
use Pterodactyl\Repositories\Eloquent\EggRepository;
use Pterodactyl\Repositories\Eloquent\NestRepository;
use Pterodactyl\Repositories\Eloquent\ModRepository;
use Pterodactyl\Repositories\Eloquent\ModVariableRepository;
use Pterodactyl\Repositories\Eloquent\ModInstallVariableRepository;
use Pterodactyl\Repositories\Eloquent\ModInstallRepository;
use Pterodactyl\Repositories\Eloquent\NodeRepository;
use Pterodactyl\Repositories\Eloquent\PackRepository;
use Pterodactyl\Repositories\Eloquent\TaskRepository;
use Pterodactyl\Repositories\Eloquent\UserRepository;
use Pterodactyl\Repositories\Daemon\CommandRepository;
use Pterodactyl\Repositories\Eloquent\ApiKeyRepository;
use Pterodactyl\Repositories\Eloquent\ServerRepository;
use Pterodactyl\Repositories\Eloquent\SessionRepository;
use Pterodactyl\Repositories\Eloquent\SubuserRepository;
use Pterodactyl\Repositories\Eloquent\DatabaseRepository;
use Pterodactyl\Repositories\Eloquent\LocationRepository;
use Pterodactyl\Repositories\Eloquent\ScheduleRepository;
use Pterodactyl\Repositories\Eloquent\SettingsRepository;
use Pterodactyl\Repositories\Eloquent\DaemonKeyRepository;
use Pterodactyl\Repositories\Eloquent\AllocationRepository;
use Pterodactyl\Repositories\Eloquent\PermissionRepository;
use Pterodactyl\Contracts\Repository\EggRepositoryInterface;
use Pterodactyl\Repositories\Daemon\ConfigurationRepository;
use Pterodactyl\Repositories\Eloquent\EggVariableRepository;
use Pterodactyl\Contracts\Repository\NestRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModVariableRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModInstallRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModInstallVariableRepositoryInterface;
use Pterodactyl\Contracts\Repository\NodeRepositoryInterface;
use Pterodactyl\Contracts\Repository\PackRepositoryInterface;
use Pterodactyl\Contracts\Repository\TaskRepositoryInterface;
use Pterodactyl\Contracts\Repository\UserRepositoryInterface;
use Pterodactyl\Repositories\Eloquent\DatabaseHostRepository;
use Pterodactyl\Contracts\Repository\ApiKeyRepositoryInterface;
use Pterodactyl\Contracts\Repository\ServerRepositoryInterface;
use Pterodactyl\Repositories\Eloquent\ServerVariableRepository;
use Pterodactyl\Contracts\Repository\SessionRepositoryInterface;
use Pterodactyl\Contracts\Repository\SubuserRepositoryInterface;
use Pterodactyl\Contracts\Repository\DatabaseRepositoryInterface;
use Pterodactyl\Contracts\Repository\LocationRepositoryInterface;
use Pterodactyl\Contracts\Repository\ScheduleRepositoryInterface;
use Pterodactyl\Contracts\Repository\SettingsRepositoryInterface;
use Pterodactyl\Contracts\Repository\DaemonKeyRepositoryInterface;
use Pterodactyl\Contracts\Repository\AllocationRepositoryInterface;
use Pterodactyl\Contracts\Repository\PermissionRepositoryInterface;
use Pterodactyl\Contracts\Repository\Daemon\FileRepositoryInterface;
use Pterodactyl\Contracts\Repository\EggVariableRepositoryInterface;
use Pterodactyl\Contracts\Repository\Daemon\PowerRepositoryInterface;
use Pterodactyl\Contracts\Repository\DatabaseHostRepositoryInterface;
use Pterodactyl\Contracts\Repository\Daemon\CommandRepositoryInterface;
use Pterodactyl\Contracts\Repository\ServerVariableRepositoryInterface;
use Pterodactyl\Contracts\Repository\Daemon\ConfigurationRepositoryInterface;
use Pterodactyl\Repositories\Daemon\ServerRepository as DaemonServerRepository;
use Pterodactyl\Contracts\Repository\Daemon\ServerRepositoryInterface as DaemonServerRepositoryInterface;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register all of the repository bindings.
     */
    public function register()
    {
        // Eloquent Repositories
        $this->app->bind(AllocationRepositoryInterface::class, AllocationRepository::class);
        $this->app->bind(ApiKeyRepositoryInterface::class, ApiKeyRepository::class);
        $this->app->bind(DaemonKeyRepositoryInterface::class, DaemonKeyRepository::class);
        $this->app->bind(DatabaseRepositoryInterface::class, DatabaseRepository::class);
        $this->app->bind(DatabaseHostRepositoryInterface::class, DatabaseHostRepository::class);
        $this->app->bind(EggRepositoryInterface::class, EggRepository::class);
        $this->app->bind(EggVariableRepositoryInterface::class, EggVariableRepository::class);
        $this->app->bind(LocationRepositoryInterface::class, LocationRepository::class);
        $this->app->bind(NestRepositoryInterface::class, NestRepository::class);
        $this->app->bind(ModRepositoryInterface::class, ModRepository::class);
        $this->app->bind(ModVariableRepositoryInterface::class, ModVariableRepository::class);
        $this->app->bind(ModInstallVariableRepositoryInterface::class, ModInstallVariableRepository::class);
        $this->app->bind(ModInstallRepositoryInterface::class, ModInstallRepository::class);
        $this->app->bind(NodeRepositoryInterface::class, NodeRepository::class);
        $this->app->bind(PackRepositoryInterface::class, PackRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(ScheduleRepositoryInterface::class, ScheduleRepository::class);
        $this->app->bind(ServerRepositoryInterface::class, ServerRepository::class);
        $this->app->bind(ServerVariableRepositoryInterface::class, ServerVariableRepository::class);
        $this->app->bind(SessionRepositoryInterface::class, SessionRepository::class);
        $this->app->bind(SettingsRepositoryInterface::class, SettingsRepository::class);
        $this->app->bind(SubuserRepositoryInterface::class, SubuserRepository::class);
        $this->app->bind(TaskRepositoryInterface::class, TaskRepository::class);
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);

        // Daemon Repositories
        $this->app->bind(ConfigurationRepositoryInterface::class, ConfigurationRepository::class);
        $this->app->bind(CommandRepositoryInterface::class, CommandRepository::class);
        $this->app->bind(DaemonServerRepositoryInterface::class, DaemonServerRepository::class);
        $this->app->bind(FileRepositoryInterface::class, FileRepository::class);
        $this->app->bind(PowerRepositoryInterface::class, PowerRepository::class);
    }
}
