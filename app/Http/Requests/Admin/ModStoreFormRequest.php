<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Http\Requests\Admin;

use Pterodactyl\Models\Mod_installed;
use Pterodactyl\Services\Mods\ModCreationService;

class ModStoreFormRequest extends AdminFormRequest
{
    /**
     * @return array
     */ 
    public function rules()
    {

        return mod_installed::getCreateRules();
    }

    /**
     * Run validation after the rules above have been applied.
     *
     * @param \Illuminate\Validation\Validator $validator
     */
    public function withValidator($validator)
    {
        if ($this->method() !== 'POST') {
            return;
        }

    }
}
