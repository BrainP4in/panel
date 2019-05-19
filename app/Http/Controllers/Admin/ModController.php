<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Pterodactyl\Models\Mod;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Services\Packs\ExportPackService;
use Pterodactyl\Services\Mods\ModUpdateService;
use Pterodactyl\Services\Mods\ModCreationService;
use Pterodactyl\Services\Mods\ModDeletionService;
use Pterodactyl\Http\Requests\Admin\ModFormRequest;
use Pterodactyl\Services\Packs\TemplateUploadService;
use Pterodactyl\Contracts\Repository\NestRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class ModController extends Controller
{
    /**
     * @var \Prologue\Alerts\AlertsMessageBag
     */
    protected $alert;

    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected $config;

    /**
     * @var \Pterodactyl\Services\Packs\PackCreationService
     */
    protected $creationService;

    /**
     * @var \Pterodactyl\Services\Packs\PackDeletionService
     */
    protected $deletionService;

    /**
     * @var \Pterodactyl\Services\Packs\ExportPackService
     */
    protected $exportService;

    /**
     * @var \Pterodactyl\Contracts\Repository\PackRepositoryInterface
     */
    protected $repository;

    /**
     * @var \Pterodactyl\Services\Packs\PackUpdateService
     */
    protected $updateService;

    /**
     * @var \Pterodactyl\Contracts\Repository\NestRepositoryInterface
     */
    protected $serviceRepository;

    /**
     * @var \Pterodactyl\Services\Packs\TemplateUploadService
     */
    protected $templateUploadService;

    /**
     * PackController constructor.
     *
     * @param \Prologue\Alerts\AlertsMessageBag                         $alert
     * @param \Illuminate\Contracts\Config\Repository                   $config
     * @param \Pterodactyl\Services\Packs\ExportPackService             $exportService
     * @param \Pterodactyl\Services\Mods\ModCreationService           $creationService
     * @param \Pterodactyl\Services\Packs\PackDeletionService           $deletionService
     * @param \Pterodactyl\Contracts\Repository\ModRepositoryInterface $repository
     * @param \Pterodactyl\Services\Packs\PackUpdateService             $updateService
     * @param \Pterodactyl\Contracts\Repository\NestRepositoryInterface $serviceRepository
     * @param \Pterodactyl\Services\Packs\TemplateUploadService         $templateUploadService
     */
    public function __construct(
        AlertsMessageBag $alert,
        ConfigRepository $config,
        ExportPackService $exportService,
        ModCreationService $creationService,
        ModDeletionService $deletionService,
        ModRepositoryInterface $repository,
        ModUpdateService $updateService,
        NestRepositoryInterface $serviceRepository,
        TemplateUploadService $templateUploadService
    ) {
        $this->alert = $alert;
        $this->config = $config;
        $this->creationService = $creationService;
        $this->deletionService = $deletionService;
        $this->exportService = $exportService;
        $this->repository = $repository;
        $this->updateService = $updateService;
        $this->serviceRepository = $serviceRepository;
        $this->templateUploadService = $templateUploadService;
    }

    /**
     * Display listing of all mods on the system.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        return view('admin.mods.index', [
            'mods' => $this->repository->getWithEggs(), //->setSearchTerm($request->input('query'))->paginated(50),
        ]);
    }

    /**
     * Display new pack creation form.
     *
     * @return \Illuminate\View\View
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function create()
    {
        return view('admin.mods.new', [
            'nests' => $this->serviceRepository->getWithEggs(),
        ]);
    }

    /**
     * Display new pack creation modal for use with template upload.
     *
     * @return \Illuminate\View\View
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function newTemplate()
    {
        return view('admin.mods.modal', [
            'nests' => $this->serviceRepository->getWithEggs(),
        ]);
    }

    /**
     * Handle create pack request and route user to location.
     *
     * @param \Pterodactyl\Http\Requests\Admin\ModFormRequest $request
     * @return \Illuminate\View\View
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Service\Pack\InvalidFileMimeTypeException
     * @throws \Pterodactyl\Exceptions\Service\InvalidFileUploadException
     * @throws \Pterodactyl\Exceptions\Service\Pack\InvalidPackArchiveFormatException
     * @throws \Pterodactyl\Exceptions\Service\Pack\UnreadableZipArchiveException
     * @throws \Pterodactyl\Exceptions\Service\Pack\ZipExtractionException
     */
    public function store(ModFormRequest $request)
    {
        $mod = $this->creationService->handle($request->normalize());

        $this->alert->success(trans('admin/pack.notices.pack_created'))->flash();

        return redirect()->route('admin.mods.view', $mod->id);
    }

    /**
     * Display pack view template to user.
     *
     * @param \Pterodactyl\Models\Pack $pack
     * @return \Illuminate\View\View
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function view(Mod $mod)
    {
        return view('admin.mods.view', [
            'mod' => $this->repository->getWithEggs($mod->id),
            'nests' => $this->serviceRepository->getWithEggs(),

            'servers' =>$this->repository->getServers($mod->id)->mod_installed,
        ]);
    }

    /**
     * Handle updating or deleting pack information.
     *
     * @param \Pterodactyl\Http\Requests\Admin\ModFormRequest $request
     * @param \Pterodactyl\Models\Mod                         $pack
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     * @throws \Pterodactyl\Exceptions\Service\HasActiveServersException
     */
    public function update(ModFormRequest $request, Mod $mod)
    {
        $this->updateService->handle($mod, $request->normalize());
        $this->alert->success(trans('admin/pack.notices.mod_updated'))->flash();

        return redirect()->route('admin.mods.view', $mod->id);
    }

    /**
     * Delete a pack if no servers are attached to it currently.
     *
     * @param \Pterodactyl\Models\Pack $pack
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     * @throws \Pterodactyl\Exceptions\Service\HasActiveServersException
     */
    public function destroy(Mod $mod)
    {
        $this->deletionService->handle($mod->id);
        $this->alert->success(trans('admin/mod.notices.mod_deleted', [
            'name' => $mod->name,
        ]))->flash();

        return redirect()->route('admin.mods');
    }

    /**
     * Creates an archive of the pack and downloads it to the browser.
     *
     * @param \Pterodactyl\Models\Pack $pack
     * @param bool|string              $files
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     * @throws \Pterodactyl\Exceptions\Service\Pack\ZipArchiveCreationException
     */
    public function export(Pack $pack, $files = false)
    {
        $filename = $this->exportService->handle($pack, is_string($files));

        if (is_string($files)) {
            return response()->download($filename, 'pack-' . $pack->name . '.zip')->deleteFileAfterSend(true);
        }

        return response()->download($filename, 'pack-' . $pack->name . '.json', [
            'Content-Type' => 'application/json',
        ])->deleteFileAfterSend(true);
    }

        /**
     * Display pack view template to user.
     *
     * @param \Pterodactyl\Models\Pack $pack
     * @return \Illuminate\View\View
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function variables(Mod $mod)
    {
        return view('admin.mods.variables', [
            'mod' => $this->repository->getWithVars($mod->id),
            'nests' => $this->serviceRepository->getWithEggs(),
        ]);
    }
}
