<?php

namespace App\Http\Controllers;

use App\Repositories\ActivityLogRepository;
use App\Services\ActivityLogService;
use App\Services\UserService;
use App\Utils\ActivityLogValueResolver;
use App\Utils\ExtractFiltreActivityLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActivityLogController extends Controller
{
    public function __construct(
        private readonly ActivityLogService $service,
        private readonly ActivityLogRepository $repository,
        private readonly UserService $userService,
        private readonly ActivityLogValueResolver $resolver,
    ) {}

    public function index(Request $request)
    {
        $filters = ExtractFiltreActivityLog::extractFilter($request);
        $data    = $this->service->getAll($filters);
        $this->resolver->resolveForData($data);

        return Inertia::render('ActivityLog/Index', [
            'data'    => $data,
            'modules' => $this->repository->getDistinctModules(),
            'actions' => $this->repository->getDistinctActions(),
            'users'   => $this->userService->getAll([]),
            'filters' => $filters,
        ]);
    }
}
