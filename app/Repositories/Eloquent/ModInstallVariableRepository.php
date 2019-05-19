<?php

namespace Pterodactyl\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Pterodactyl\Models\Mod_installed_variable;
use Pterodactyl\Contracts\Repository\ModInstallVariableRepositoryInterface;

class ModInstallVariableRepository extends EloquentRepository implements ModInstallVariableRepositoryInterface
{
    /**
     * Return the model backing this repository.
     *
     * @return string
     */
    public function model()
    {
        return Mod_installed_variable::class;
    }

}
