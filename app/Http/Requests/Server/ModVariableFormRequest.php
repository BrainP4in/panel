<?php

namespace Pterodactyl\Http\Requests\Server;

use Pterodactyl\Models\Server;
use Pterodactyl\Http\Requests\FrontendUserFormRequest;

class ModVariableFormRequest extends AdminFormRequest
{
    /**
     * Define rules for validation of this request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'description' => 'sometimes|nullable|string',
            'env_variable' => 'required|regex:/^[\w]{1,255}$/|notIn:' . EggVariable::RESERVED_ENV_NAMES,
            'user_viewable' => 'sometimes',
            'user_editable' => 'sometimes',
            'rules' => 'bail|required|string',
            'default_value' => 'present',
            'input_type' => 'required|string|min:1|max:255'
        ];
    }
}
