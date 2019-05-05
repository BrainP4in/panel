<?php

namespace Pterodactyl\Services\Mods;

use Ramsey\Uuid\Uuid;
use Pterodactyl\Models\Mod;
use Pterodactyl\Models\Mod_installed;
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;


use Pterodactyl\Models\Node;
use Pterodactyl\Models\User;
use Pterodactyl\Models\Server;
use Illuminate\Support\Collection;
use Pterodactyl\Models\Allocation;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\ConnectionInterface;
use Pterodactyl\Models\Objects\DeploymentObject;
use Pterodactyl\Services\Deployment\FindViableNodesService;
use Pterodactyl\Contracts\Repository\EggRepositoryInterface;
use Pterodactyl\Contracts\Repository\ServerRepositoryInterface;
use Pterodactyl\Services\Deployment\AllocationSelectionService;
use Pterodactyl\Contracts\Repository\AllocationRepositoryInterface;
use Pterodactyl\Exceptions\Http\Connection\DaemonConnectionException;
use Pterodactyl\Contracts\Repository\ServerVariableRepositoryInterface;
use Pterodactyl\Contracts\Repository\Daemon\ServerRepositoryInterface as DaemonServerRepositoryInterface;

use Pterodactyl\Contracts\Repository\ModInstallRepositoryInterface;
use Pterodactyl\Http\Requests\Admin\ModStoreFormRequest;


class ModInstallCreationService
{
    /**
     * @var \Illuminate\Contracts\Config\Repository
     */
    private $config;

    /**
     * @var \Pterodactyl\Contracts\Repository\NestRepositoryInterface
     */
    private $repository;

    /**
     * NestCreationService constructor.
     *
     * @param \Illuminate\Contracts\Config\Repository                   $config
     * @param \Pterodactyl\Contracts\Repository\ModRepositoryInterface $repository
     */
    public function __construct(
        ConfigRepository $config, 
        ModRepositoryInterface $repository,
        ModInstallRepositoryInterface $installRepository,
        DaemonServerRepositoryInterface $daemonServerRepository
    ) {
        $this->config = $config;
        $this->repository = $repository;
        $this->installRepository = $installRepository;
        $this->daemonServerRepository = $daemonServerRepository;
    }



    /**
     * Create a new nest on the system.
     *
     * @param array       $data
     * @param string|null $author
     * @return \Pterodactyl\Models\Nest
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     */
    public function handle(Server $server, Mod $mod,string $workshopModId,string $workshopModName) //: Mod_installed
    {
        $data = array(
            'server'    => $server,
            'mod'       => array(
                "steam_name"=> $workshopModName,
                "steam_id"=> $workshopModId, //$data->attribute->get('workshopModId')
                "created_at"=> null,
                "updated_at"=> null,
                "server_id"=> $server->id,
                "mod_id"=> $mod->id,
                "mod"=> $mod)
        );
        
        try {
            $this->daemonServerRepository->setServer($server)->installMod(json_encode($data));

        } catch (RequestException $exception) {
            throw new DaemonConnectionException($exception);
        }

        return $this->installRepository->create($data['mod'], true, true);
    }
}





/*

{
    "server": {
        "id": 3,
        "external_id": null,
        "uuid": "bf69ef0a-cb66-45e4-acc8-747a3204286c",
        "uuidShort": "bf69ef0a",
        "node_id": 1,
        "name": "Test",
        "description": "",	 
        "skip_scripts": false,
        "suspended": 0,
        "owner_id": 2,
        "memory": 2048,
        "swap": 0,
        "disk": 2048,
        "io": 500,
        "cpu": 0,
        "oom_disabled": 0,
        "allocation_id": 1,
        "nest_id": 1,
        "egg_id": 1,
        "pack_id": null,
        "startup": "java -Xms128M -Xmx{{SERVER_MEMORY}}M -jar {{SERVER_JARFILE}}",
        "image": "quay.io/pterodactyl/core:java",
        "installed": 1,
        "allocation_limit": 0,
        "database_limit": 0,
        "created_at": "2019-04-23 20:25:13",
        "updated_at": "2019-04-23 20:26:12",
        "nest": {
            "id": 1,
            "uuid": "ef336455-b779-4db0-ac35-ea340f0eb517",
            "author": "support@pterodactyl.io",
            "name": "Minecraft",
            "description": "Minecraft - the classic game from Mojang. With support for Vanilla MC, Spigot, and many others!",
            "created_at": "2019-04-21 10:47:07",
            "updated_at": "2019-04-21 10:47:07"
        },
        "node": {
            "id": 1,
            "public": true,
            "name": "Node",
            "description": "Node",
            "location_id": 1,
            "fqdn": "192.168.50.3",
            "scheme": "http",
            "behind_proxy": false,
            "maintenance_mode": false,
            "memory": 4096,
            "memory_overallocate": 50,
            "disk": 8182,
            "disk_overallocate": 50,
            "upload_size": 100,
            "daemonListen": 58080,
            "daemonSFTP": 32767,
            "daemonBase": "/srv/daemon-data",
            "created_at": "2019-04-21 14:19:09",
            "updated_at": "2019-04-23 20:24:04"
        }
    },
    "mod": {
        "id": 4,
        "steam_name": "Test2",
        "steam_id": "937322940",
        "created_at": null,
        "updated_at": null,
        "server_id": 3,
        "mod_id": 6,
        "mod": {
            "id": 6,
            "name": "Steam",
            "comprehensive": true,
            "steam_id": "937322940",
            "install_script": "#!/bin/bash\n# Steam Mod Installation Script\n#\n# Server Files: /mnt/server\napt -y update\napt -y --no-install-recommends install curl lib32gcc1 ca-certificates\nif [ ! -d '/mnt/server/steamcmd' ]; then\ncd /tmp\ncurl -sSL -o steamcmd.tar.gz http://media.steampowered.com/installer/steamcmd_linux.tar.gz\n\nmkdir -p /mnt/server/steamcmd\ntar -xzvf steamcmd.tar.gz -C /mnt/server/steamcmd\nfi\ncd /mnt/server/steamcmd\n# SteamCMD fails otherwise for some reason, even running as root.\n# This is changed at the end of the install process anyways.\nchown -R root:root /mnt\nexport HOME=/mnt/server\n./steamcmd.sh +login anonymous +force_install_dir /mnt/server +workshop_download_item 346110 {steamModId} +quit",
            "created_at": "2019-04-23 21:19:39",
            "updated_at": "2019-04-23 21:19:39",
            "egg_id": 1,
            "steam_username": "anonym",
            "steam_password": null,
            "install_script_container": "ubuntu:16.04",
            "install_script_entry": "bash"
        }
    }
}*/