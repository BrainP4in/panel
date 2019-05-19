<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Repositories\Eloquent;

use Pterodactyl\Models\Mod;
use Pterodactyl\Models\Mod_installed;
use Pterodactyl\Contracts\Repository\ModInstallRepositoryInterface;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;
use Pterodactyl\Repositories\Concerns\Searchable;

class ModInstallRepository extends EloquentRepository implements ModInstallRepositoryInterface
{
    use Searchable;

    /**
     * Return the model backing this repository.
     *
     * @return string
     */
    public function model()
    {
        return Mod_installed::class;
    }

    /**
     * Return a nest or all nests with their associated eggs, variables, and packs.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection|\Pterodactyl\Models\Nest
     *  
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getInstalled(int $serverId)
    {       
        
        $instance = $this->getBuilder()->with('mod')->where('server_id', $serverId);

        $instance = $instance->with('mod_installed_variable');

        $instance = $instance->with('mod_installed_variable.mod_variables');

        /*$instance = $this->getBuilder();

        if (! is_null($id)) {
            $instance = $instance->find($id, $this->getColumns());
            if (! $instance) {
                throw new RecordNotFoundException;
            }
            return $instance;
        }*/

        return  $instance->get($this->getColumns());
    }


    /**
     * Return a nest or all nests and the count of eggs, packs, and servers for that nest.
     *
     * @param int|null $id
     * @return \Pterodactyl\Models\Nest|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithVariables(int $id)
    {

        
        $instance = $this->getBuilder()->with('mod')->where('id', $id);

        $instance = $instance->with('mod_installed_variable');

        $instance = $instance->with('mod_installed_variable.mod_variables')->first();

        if (! $instance) {
            throw new RecordNotFoundException;
        }
        return $instance;

        return  $instance->get($this->getColumns());
    }


}
