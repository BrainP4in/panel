<?php

namespace Pterodactyl\Services\Mods;

use Pterodactyl\Models\EggVariable;
use Pterodactyl\Models\Mod_variable;
use Illuminate\Contracts\Validation\Factory;
use Pterodactyl\Traits\Services\ValidatesValidationRules;
use Pterodactyl\Contracts\Repository\ModVariableRepositoryInterface;
use Pterodactyl\Exceptions\Service\Egg\Variable\ReservedVariableNameException;

class ModVariableCreationService
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
     * VariableCreationService constructor.
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
     * Create a new variable for a given Egg.
     *
     * @param int   $egg
     * @param array $data
     * @return \Pterodactyl\Models\EggVariable
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Service\Egg\Variable\BadValidationRuleException
     * @throws \Pterodactyl\Exceptions\Service\Egg\Variable\ReservedVariableNameException
     */
    public function handle(int $mod, array $data): Mod_variable
    {
        if (in_array(strtoupper(array_get($data, 'env_variable')), explode(',', EggVariable::RESERVED_ENV_NAMES))) {
            throw new ReservedVariableNameException(sprintf(
                'Cannot use the protected name %s for this environment variable.',
                array_get($data, 'env_variable')
            ));
        }

        if (! empty($data['rules'] ?? '')) {
            $this->validateRules($data['rules']);
        }

        $options = array_get($data, 'options') ?? [];

        return $this->repository->create([
            'mod_id' => $mod,
            'name' => $data['name'] ?? '',
            'description' => $data['description'] ?? '',
            'env_variable' => $data['env_variable'] ?? '',
            'default_value' => $data['default_value'] ?? '',
            'user_viewable' => in_array('user_viewable', $options),
            'user_editable' => in_array('user_editable', $options),
            'rules' => $data['rules'] ?? '',
            'input_type' => $data['input_type'] ?? '',
        ]);
    }
}
