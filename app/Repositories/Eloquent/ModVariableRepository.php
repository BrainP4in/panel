<?php

namespace Pterodactyl\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Pterodactyl\Models\Mod_variable;
use Pterodactyl\Contracts\Repository\ModVariableRepositoryInterface;

class ModVariableRepository extends EloquentRepository implements ModVariableRepositoryInterface
{
    /**
     * Return the model backing this repository.
     *
     * @return string
     */
    public function model()
    {
        return Mod_variable::class;
    }

    /**
     * Return editable variables for a given egg. Editable variables must be set to
     * user viewable in order to be picked up by this function.
     *
     * @param int $egg
     * @return \Illuminate\Support\Collection
     */
    public function getEditableVariables(int $mod): Collection
    {
        return $this->getBuilder()->where([
            ['mod_id', '=', $mod],
            //['user_viewable', '=', 1],
            //['user_editable', '=', 1],
        ])->get($this->getColumns());
    }
}
