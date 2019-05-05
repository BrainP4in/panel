<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Http\Requests\Admin;

use Pterodactyl\Models\Mod;
use Pterodactyl\Services\Mods\ModCreationService;

class ModFormRequest extends AdminFormRequest
{
    /**
     * @return array
     */ 
    public function rules()
    {
        if ($this->method() === 'PATCH') {
            return Mod::getUpdateRulesForId($this->route()->parameter('mod')->id);
        }

        return Mod::getCreateRules();
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
