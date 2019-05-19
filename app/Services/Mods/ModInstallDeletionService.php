<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Services\Mods;

use Pterodactyl\Models\Mod;
use Pterodactyl\Models\Mod_installed;
use Pterodactyl\Models\Mod_installed_variable;
use Illuminate\Database\ConnectionInterface;
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModInstallRepositoryInterface;
use Pterodactyl\Exceptions\Service\HasActiveServersException;
use Pterodactyl\Contracts\Repository\ServerRepositoryInterface;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;
use Pterodactyl\Contracts\Repository\Daemon\ServerRepositoryInterface as DaemonServerRepositoryInterface;

class ModInstallDeletionService
{
    /**
     * @var \Illuminate\Database\ConnectionInterface
     */
    protected $connection;

    /**
     * @var \Pterodactyl\Contracts\Repository\PackRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Pterodactyl\Contracts\Repository\ServerRepositoryInterface
     */
    protected $serverRepository;

    /**
     * @var \Illuminate\Contracts\Filesystem\Factory
     */
    protected $storage;

    /**
     * PackDeletionService constructor.
     *
     * @param \Illuminate\Database\ConnectionInterface                    $connection
     * @param \Illuminate\Contracts\Filesystem\Factory                    $storage
     * @param \Pterodactyl\Contracts\Repository\PackRepositoryInterface   $repository
     * @param \Pterodactyl\Contracts\Repository\ServerRepositoryInterface $serverRepository
     */
    public function __construct(
        ConnectionInterface $connection,
        FilesystemFactory $storage,
        ModRepositoryInterface $modRepository,
        ModInstallRepositoryInterface $repository,
        ServerRepositoryInterface $serverRepository,
        DaemonServerRepositoryInterface $daemonServerRepository

    ) {
        $this->connection = $connection;
        $this->repository = $repository;
        $this->serverRepository = $serverRepository;
        $this->storage = $storage;
        $this->daemonServerRepository = $daemonServerRepository;
        $this->modRepository = $modRepository;
    }

    /**
     * Delete a pack from the database as well as the archive stored on the server.
     *
     * @param  int|\Pterodactyl\Models\Pack$pack
     *
     * @throws \Pterodactyl\Exceptions\Service\HasActiveServersException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function handle($mod, $server)
    {

        //$mod = $this->repository->getWithVariables($mod);

        /*
        $mod->install_script = $mod->uninstall_script;

        // modVariableData -> [{"id":3,"key":"steamModId","value":"fprzfs"}]

        $modVariableData = [];

        foreach ($mod->// variable  as $key) {

            $modVariableData-> // PUSH $key

        }



        $data = array(
            'server'    => $server,
            'mod'       => $mod,
            'variables' => $modVariableData
        );
        
        try {
            $this->daemonServerRepository->setServer($server)->installMod(json_encode($data));

        } catch (RequestException $exception) {
            throw new DaemonConnectionException($exception);
        }
        */

        return $this->repository->delete($mod);

    }
}
