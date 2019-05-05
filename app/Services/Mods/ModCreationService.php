<?php

namespace Pterodactyl\Services\Mods;

use Ramsey\Uuid\Uuid;
use Pterodactyl\Models\Mod;
use Pterodactyl\Contracts\Repository\ModRepositoryInterface;
use Illuminate\Contracts\Config\Repository as ConfigRepository;

class ModCreationService
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
    public function __construct(ConfigRepository $config, ModRepositoryInterface $repository)
    {
        $this->config = $config;
        $this->repository = $repository;
    }

    /**
     * Create a new nest on the system.
     *
     * @param array       $data
     * @param string|null $author
     * @return \Pterodactyl\Models\Nest
     * @throws \Pterodactyl\Exceptions\Model\DataValidationException
     */
    public function handle(array $data): Mod
    {
        return $this->repository->create($data);
    }
}
