<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Contracts\Repository;

use Pterodactyl\Models\Mod;
use Pterodactyl\Contracts\Repository\Attributes\SearchableInterface;

interface ModInstallRepositoryInterface extends RepositoryInterface, SearchableInterface
{
    /**
     * Return a nest or all nests with their associated eggs, variables, and packs.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection|\Pterodactyl\Models\Nest
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getInstalled(int $serverId);

    /**
     * Return a nest or all nests and the count of eggs, packs, and servers for that nest.
     *
     * @param int|null $id
     * @return \Pterodactyl\Models\Nest|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithVariables(int $id);

}
