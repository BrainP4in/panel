<?php
/**
 * Pterodactyl - Panel
 * Copyright (c) 2015 - 2017 Dane Everitt <dane@daneeveritt.com>.
 *
 * This software is licensed under the terms of the MIT license.
 * https://opensource.org/licenses/MIT
 */

namespace Pterodactyl\Http\Controllers\Server;


use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Prologue\Alerts\AlertsMessageBag;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Traits\Controllers\JavascriptInjection;


use Pterodactyl\Models\Mod;
use Pterodactyl\Services\Packs\ExportPackService;
use Pterodactyl\Services\Mods\ModUpdateService;
use Pterodactyl\Services\Mods\ModInstallCreationService;
use Pterodactyl\Services\Mods\ModDeletionService;
use Pterodactyl\Http\Requests\Admin\ModFormRequest;
use Pterodactyl\Http\Requests\Admin\ModStoreFormRequest;
use Pterodactyl\Services\Packs\TemplateUploadService;
use Pterodactyl\Contracts\Repository\NestRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Pterodactyl\Contracts\Repository\ModInstallRepositoryInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;
use Pterodactyl\Contracts\Repository\Daemon\ServerRepositoryInterface;




class ModInstallController extends Controller
{

    use JavascriptInjection;

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
        ModInstallCreationService $creationService,
        ModDeletionService $deletionService,
        ModRepositoryInterface $repository,
        ModUpdateService $updateService,
        ModRepositoryInterface $serviceRepository,
        ModInstallRepositoryInterface $installRepository,
        TemplateUploadService $templateUploadService,
        ServerRepositoryInterface $serverRepository

    ) {
        $this->alert = $alert;
        $this->config = $config;
        $this->creationService = $creationService;
        $this->deletionService = $deletionService;
        $this->exportService = $exportService;
        $this->repository = $repository;
        $this->updateService = $updateService;
        $this->serviceRepository = $serviceRepository;
        $this->installRepository = $installRepository;
        $this->serverRepository = $serverRepository;
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
        $server = $request->attributes->get('server');

        $data = array(
            'server'    =>  $server,
            'mod'       =>  $this->installRepository->getInstalled($server->id)[0],

        );
        $data = json_encode($data);
        $data = ['json' => $data];

        return view('server.mods.index', [
            'server_id' => $server->uuid,
            'installed' => $this->installRepository->getInstalled($server->id),
            'mods' => $this->repository->getAvailable($server->id, $server->nest_id),  //$this->repository->all()->where('egg_id', '=', $server->egg_id), //->paginated(50)->setSearchTerm($request->input('query'))
        ]);
        

        //$mod = $this->creationService->handle($request->normalize());

        

/*
        public function reinstall(array $data = null): ResponseInterface
        {
            return $this->getHttpClient()->request('POST', 'server/reinstall', [
                'json' => $data ?? [],
            ]);
        }
        */








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
    public function store(ModStoreFormRequest $request,Mod $mod)
    {
        $server = $request->attributes->get('server');

        $workshopModId = $request->request->get('workshopModId');
        $workshopModName = $request->request->get('workshopModName');

        $modId = $request->request->get('modId');
        $mod = $this->repository->all()->where('id', '=', $modId)->first();

        $installedMod = $this->creationService->handle($server, $mod, $workshopModId, $workshopModName);

        $this->alert->success(trans('mods.notices.mod_installed'))->flash();

        return redirect()->route('server.mods', $server->uuid);
    }

    /**
     * Display pack view template to user.
     *
     * @param \Pterodactyl\Models\Pack $pack
     * @return \Illuminate\View\View
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function workshop(Mod $mod)
    {
        return view('admin.mods.view', [
            'mod' => $this->repository->getWithEggs($mod->id),
            'nests' => $this->serviceRepository->getWithEggs(),
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




}
