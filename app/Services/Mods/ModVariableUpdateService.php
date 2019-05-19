<?php

namespace Pterodactyl\Services\Mods;

use Pterodactyl\Models\Mod_variable;
use Pterodactyl\Models\EggVariable;
use Illuminate\Contracts\Validation\Factory;
use Pterodactyl\Exceptions\DisplayException;
use Pterodactyl\Traits\Services\ValidatesValidationRules;
use Pterodactyl\Contracts\Repository\ModVariableRepositoryInterface;
use Pterodactyl\Exceptions\Service\Egg\Variable\ReservedVariableNameException;

class ModVariableUpdateService
{
    use ValidatesValidationRules;

    /**
     * @var \Pterodactyl\Contracts\Repository\EggVariableRepositoryInterface
     */
    private $repository;

    /**
     * @var \Illuminate\Contracts\Validation\Factory
     */
    private $validator;

    /**
     * VariableUpdateService constructor.
     *
     * @param \Pterodactyl\Contracts\Repository\EggVariableRepositoryInterface $repository
     * @param \Illuminate\Contracts\Validation\Factory                         $validator
     */
    public function __construct(ModVariableRepositoryInterface $repository, Factory $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * Return the validation factory instance to be used by rule validation
     * checking in the trait.
     *
     * @return \Illuminate\Contracts\Validation\Factory
     */
    protected function getValidator(): Factory
    {
        return $this->validator;
    }

    /**
     * Update a specific egg variable.
     *
     * @param \Pterodactyl\Models\EggVariable $variable
     * @param array                           $data
     * @return mixed
     *
     * @throws \Pterodactyl\Exceptions\DisplayException
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     * @throws \Pterodactyl\Exceptions\Service\Egg\Variable\ReservedVariableNameException
     */
    public function handle(Mod_variable $variable, array $data)
    {
        if (! is_null(array_get($data, 'env_variable'))) {
            if (in_array(strtoupper(array_get($data, 'env_variable')), explode(',', EggVariable::RESERVED_ENV_NAMES))) {
                throw new ReservedVariableNameException(trans('exceptions.service.variables.reserved_name', [
                    'name' => array_get($data, 'env_variable'),
                ]));
            }

            $search = $this->repository->setColumns('id')->findCountWhere([
                ['env_variable', '=', $data['env_variable']],
                ['mod_id', '=', $variable->egg_id],
                ['id', '!=', $variable->id],
            ]);

            if ($search > 0) {
                throw new DisplayException(trans('exceptions.service.variables.env_not_unique', [
                    'name' => array_get($data, 'env_variable'),
                ]));
            }
        }

        if (! empty($data['rules'] ?? '')) {
            $this->validateRules($data['rules']);
        }

        $options = array_get($data, 'options') ?? [];

        return $this->repository->withoutFreshModel()->update($variable->id, [
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'env_variable' => $data['env_variable'] ?? '',
            'default_value' => $data['default_value'] ?? '',
            'user_viewable' => in_array('user_viewable', $options),
            'user_editable' => in_array('user_editable', $options),
            'rules' => $data['rules'] ?? '',
        ]);
    }
}
