<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\View\View;
use Pterodactyl\Models\Mod;
use Pterodactyl\Models\Egg;
use Pterodactyl\Models\Mod_variable;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Contracts\Repository\EggRepositoryInterface;
use Pterodactyl\Services\Mods\ModVariableUpdateService;
use Pterodactyl\Http\Requests\Admin\ModVariableFormRequest;
use Pterodactyl\Services\Mods\ModVariableCreationService;
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModVariableRepositoryInterface;

class ModVariableController extends Controller
{
    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    protected $alert;

    /**
     * @var \Pterodactyl\Services\Eggs\Variables\VariableCreationService
     */
    protected $creationService;

    /**
     * @var \Pterodactyl\Contracts\Repository\EggRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Pterodactyl\Services\Eggs\Variables\VariableUpdateService
     */
    protected $updateService;

    /**
     * @var \Pterodactyl\Contracts\Repository\EggVariableRepositoryInterface
     */
    protected $variableRepository;

    /**
     * EggVariableController constructor.
     *
     * @param \Prologue\Alerts\AlertsMessageBag                                $alert
     * @param \Pterodactyl\Services\Eggs\Variables\VariableCreationService     $creationService
     * @param \Pterodactyl\Services\Eggs\Variables\VariableUpdateService       $updateService
     * @param \Pterodactyl\Contracts\Repository\EggRepositoryInterface         $repository
     * @param \Pterodactyl\Contracts\Repository\EggVariableRepositoryInterface $variableRepository
     */
    public function __construct(
        AlertsMessageBag $alert,
        ModVariableCreationService $creationService,
        ModVariableUpdateService $updateService,
        ModRepositoryInterface $repository,
        ModVariableRepositoryInterface $variableRepository
    ) {
        $this->alert = $alert;
        $this->creationService = $creationService;
        $this->repository = $repository;
        $this->updateService = $updateService;
        $this->variableRepository = $variableRepository;
    }

    /**
     * Handle request to view the variables attached to an Egg.
     *
     * @param int $egg
     * @return \Illuminate\View\View
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function view(int $egg): View
    {
        $egg = $this->repository->getWithVariables($egg);

        return view('admin.mods.variables', ['mod' => $egg]);
    }

    /**
     * Handle a request to create a new Egg variable.
     *
     * @param \Pterodactyl\Http\Requests\Admin\Egg\EggVariableFormRequest $request
     * @param \Pterodactyl\Models\Egg $egg
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Service\Egg\Variable\BadValidationRuleException
     * @throws \Pterodactyl\Exceptions\Service\Egg\Variable\ReservedVariableNameException
     */
    public function store(ModVariableFormRequest $request, Mod $mod): RedirectResponse
    {
        $this->creationService->handle($mod->id, $request->normalize());
        $this->alert->success(trans('admin/mods.variables.notices.variable_created'))->flash();

        return redirect()->route('admin.mods.variables', $mod->id);
    }

    /**
     * Handle a request to update an existing Egg variable.
     *
     * @param \Pterodactyl\Http\Requests\Admin\Egg\EggVariableFormRequest $request
     * @param \Pterodactyl\Models\Egg                                     $egg
     * @param \Pterodactyl\Models\EggVariable                             $variable
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Pterodactyl\Exceptions\DisplayException
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     * @throws \Pterodactyl\Exceptions\Service\Egg\Variable\ReservedVariableNameException
     */
    public function update(ModVariableFormRequest $request, Mod $mod, Mod_variable $variable): RedirectResponse
    {
        $this->updateService->handle($variable, $request->normalize());
        $this->alert->success(trans('admin/mods.variables.notices.variable_updated', [
            'variable' => $variable->name,
        ]))->flash();

        return redirect()->route('admin.mods.variables', $mod->id);
    }

    /**
     * Handle a request to delete an existing Egg variable from the Panel.
     *
     * @param int                             $egg
     * @param \Pterodactyl\Models\EggVariable $variable
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(int $mod, Mod_variable $variable): RedirectResponse
    {
        $this->variableRepository->delete($variable->id);
        $this->alert->success(trans('admin/mods.variables.notices.variable_deleted', [
            'variable' => $variable->name,
        ]))->flash();

        return redirect()->route('admin.mods.variables', $mod);
    }
}
