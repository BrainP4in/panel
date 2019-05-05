<?php

namespace Pterodactyl\Http\Controllers\Api\Remote;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Pterodactyl\Http\Controllers\Controller;
use Pterodactyl\Services\Servers\EnvironmentService;
use Pterodactyl\Contracts\Repository\ServerRepositoryInterface;

class ModInstallController extends Controller
{
    /**
     * @var \Pterodactyl\Services\Servers\EnvironmentService
     */
    private $environment;

    /**
     * @var \Pterodactyl\Contracts\Repository\ServerRepositoryInterface
     */
    private $repository;

    /**
     * EggInstallController constructor.
     *
     * @param \Pterodactyl\Services\Servers\EnvironmentService            $environment
     * @param \Pterodactyl\Contracts\Repository\ServerRepositoryInterface $repository
     */
    public function __construct(EnvironmentService $environment, ServerRepositoryInterface $repository)
    {
        $this->environment = $environment;
        $this->repository = $repository;
    }

    /**
     * Handle request to get script and installation information for a server
     * that is being created on the node.
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $uuid
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Pterodactyl\Exceptions\Repository\RecordNotFoundException
     */
    public function index(Request $request, string $uuid, string $id): JsonResponse
    {
        $node = $request->attributes->get('node');

        /** @var \Pterodactyl\Models\Server $server */
        $server = $this->repository->findFirstWhere([
            ['uuid', '=', $uuid],
            ['node_id', '=', $node->id],
        ]);


        $mod = $this->repository->all()->where('id', '=', $id);


        return response()->json([
            'scripts' => [
                'install' => $mod->install_script,
                'privileged' => false,
            ],
            'config' => [
                'container' => $mod->install_script_container,
                'entry' => $mod->install_script_entry,
            ],
            'env' => $this->environment->handle($server),
        ]);



    }


}
