<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Contracts\Repository;

use Illuminate\Support\Collection;

interface ModVariableRepositoryInterface extends RepositoryInterface
{
    /**
     * Return editable variables for a given egg. Editable variables must be set to
     * user viewable in order to be picked up by this function.
     *
     * @param int $egg
     * @return \Illuminate\Support\Collection
     */
    public function getEditableVariables(int $mod): Collection;
}
