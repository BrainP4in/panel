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
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Pterodactyl\Exceptions\Repository\RecordNotFoundException;
use Pterodactyl\Repositories\Concerns\Searchable;

class ModRepository extends EloquentRepository implements ModRepositoryInterface
{
    use Searchable;

    /**
     * Return the model backing this repository.
     *
     * @return string
     */
    public function model()
    {
        return Mod::class;
    }

    /**
     * Return a nest or all nests with their associated eggs, variables, and packs.
     *
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Collection|\Pterodactyl\Models\Nest
     *  
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithEggs(int $id = null)
    {       // $instance = $this->getBuilder()->with('eggs.servers')->find($id, $this->getColumns());

        $instance = $this->getBuilder();

        if (! is_null($id)) {
            $instance = $instance->find($id, $this->getColumns());
            if (! $instance) {
                throw new RecordNotFoundException;
            }
            return $instance;
        }

        return null; //$instance->get($this->getColumns());
    }

    /**
     * Return a nest or all nests and the count of eggs, packs, and servers for that nest.
     *
     * @param int|null $id
     * @return \Pterodactyl\Models\Nest|\Illuminate\Database\Eloquent\Collection
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function getWithCounts(int $id = null)
    {
        $instance = $this->getBuilder()->withCount(['eggs', 'packs', 'servers']);

        if (! is_null($id)) {
            $instance = $instance->find($id, $this->getColumns());
            if (! $instance) {
                throw new RecordNotFoundException;
            }

            return $instance;
        }

        return $instance->get($this->getColumns());
    }


    public function getAvailable(int $serverId, int $nestId)
    {      
        
        function array_to_object($arr) {
            $arrObject = array();
            foreach ($arr as $array) {
                $object = new Mod();
                foreach ($array as $key => $value) {
                    $object->$key = $value;
                }
                $arrObject[] = $object;
            }
        
            return $arrObject;
        } 
        
        $instance = $this->getBuilder()->where('comprehensive', '=', true)->with('mod_installed')->leftJoin('mods_installed', 'mod_id', '=', 'mods.id')->where('mods_installed.mod_id', '=', NULL)->where('mods.steam_id', '=', NULL);
        $instanceSteam = $this->getBuilder()->where('steam_id', '<>', NULL);
        $instanceEgg = $this->getBuilder()->where('comprehensive', '=', true)->with('egg');


        $instance = array_merge($instance->get($this->getColumns())->toArray(), $instanceSteam->get($this->getColumns())->toArray(), $instanceEgg->get($this->getColumns())->toArray());

        $obj = array_to_object($instance);
        //$instance = $instance->where('mod_installed', NULL);

        /*$instance = $this->getBuilder();

        if (! is_null($id)) {
            $instance = $instance->find($id, $this->getColumns());
            if (! $instance) {
                throw new RecordNotFoundException;
            }
            return $instance;
        }*/

        return $obj;
    }

}
