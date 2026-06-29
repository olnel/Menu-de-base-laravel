<?php

namespace App\Services;

use App\Repositories\ClientRepository;
use App\Services\Base\BaseService;

class ClientService extends BaseService
{
    protected $repository;
    public function __construct(ClientRepository $repository)
    {
        $this->repository = $repository;
        parent::__construct($repository);
    }

    protected function initializeFilters(): void
    {
        $this->setFilterValue('id')
            ->setFilterLabel('nom_client');
    }

    public function create(array $validated): array
    {
        if (!empty($validated['login'])) {
            $validated['mot_de_passe'] = 'password';
        }
        return parent::create($validated);
    }

    public function resetPassword($client): array
    {
        return parent::update($client, ['mot_de_passe' => 'password']);
    }

    public function updateOrCreate(string $nom_client): array
    {
        $element = $this->repository->findElement(['nom_client' => $nom_client]);
        if ($element) {
            return parent::update($element, ['nom_client' => $nom_client]);
        } else {
            return parent::create(['nom_client' => $nom_client]);
        }
    }

    public function findOrCreateByNomClient(string $nom_client): array
    {
        $client = $this->repository->findElement(['nom_client' => $nom_client]);

        if ($client) {
            return [
                'error' => false,
                'message' => 'Client trouvé',
                'element' => $client
            ];
        } else {
            return parent::create(['nom_client' => $nom_client]);
        }
    }
}
