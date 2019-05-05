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
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Pterodactyl\Exceptions\Service\HasActiveServersException;
use Pterodactyl\Contracts\Repository\ServerRepositoryInterface;

class ModUpdateService
{
    /**
     * @var \Pterodactyl\Contracts\Repository\PackRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Pterodactyl\Contracts\Repository\ServerRepositoryInterface
     */
    protected $serverRepository;

    /**
     * PackUpdateService constructor.
     *
     * @param \Pterodactyl\Contracts\Repository\ModRepositoryInterface   $repository
     * @param \Pterodactyl\Contracts\Repository\ServerRepositoryInterface $serverRepository
     */
    public function __construct(
        ModRepositoryInterface $repository,
        ServerRepositoryInterface $serverRepository
    ) {
        $this->repository = $repository;
        $this->serverRepository = $serverRepository;
    }

    /**
     * Update a pack.
     *
     * @param int|\Pterodactyl\Models\Pack $pack
     * @param array                        $data
     * @return bool
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Service\HasActiveServersException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function handle(Mod $mod, array $data)
    {
        $this->repository->update($mod->id, $data);
    }


}
