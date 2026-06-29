<?php

namespace App\Http\Controllers;

use App\Models\Backup;
use App\Services\BackupService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class BackupController extends Controller
{
    public function __construct(
        private readonly BackupService $service
    ) {}

    public function index(Request $request)
    {
        $filters = array_merge(ExtractFiltre::extractFilter($request), [
            'start_date' => $request->input('start_date'),
            'end_date'   => $request->input('end_date'),
        ]);
        return Inertia::render('Parametre/Backup/Index', [
            'data'    => $this->service->getAll($filters),
            'filters' => $filters,
        ]);
    }

    public function store(Request $request)
    {
        $response = $this->service->runBackup();

        if ($response['error']) {
            return back()->with('message.error', $response['message']);
        }

        return back()->with('message.success', $response['message']);
    }

    public function download(Backup $backup)
    {
        try {
            return $this->service->download($backup);
        } catch (\Exception $e) {
            return back()->with('message.error', $e->getMessage());
        }
    }

    public function destroy(Backup $backup)
    {
        $response = $this->service->delete($backup);

        if ($response['error']) {
            return back()->with('message.error', $response['message']);
        }

        return back()->with('message.success', $response['message']);
    }
}
