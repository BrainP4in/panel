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
use Illuminate\Database\ConnectionInterface;
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Pterodactyl\Exceptions\Service\HasActiveServersException;
use Pterodactyl\Contracts\Repository\ServerRepositoryInterface;
use Illuminate\Contracts\Filesystem\Factory as FilesystemFactory;

class ModDeletionService
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
        ModRepositoryInterface $repository,
        ServerRepositoryInterface $serverRepository
    ) {
        $this->connection = $connection;
        $this->repository = $repository;
        $this->serverRepository = $serverRepository;
        $this->storage = $storage;
    }

    /**
     * Delete a pack from the database as well as the archive stored on the server.
     *
     * @param  int|\Pterodactyl\Models\Pack$pack
     *
     * @throws \Pterodactyl\Exceptions\Service\HasActiveServersException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function handle($mod)
    {
        //$count = $this->serverRepository->findCountWhere([['mod_id', '=', $mod]]);
        //if ($count > 0) {
        //    throw new HasActiveServersException(trans('exceptions.service.delete_has_servers'));
        //}

        return $this->repository->delete($mod);

    }
}
