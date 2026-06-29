<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentModel;
use App\Models\DocumentType;
use App\Models\DocumentableType;
use App\Services\DocumentDynamicService;
use App\Services\DocumentModelService;
use App\Services\DocumentTypeService;
use App\Services\DocumentableTypeService;
use App\Utils\ExtractFiltre;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentDynamicController extends Controller
{
    private $service;
    private $modelService;
    private $typeService;
    private $docableTypeService;

    public function __construct(
        DocumentDynamicService $service,
        DocumentModelService $modelService,
        DocumentTypeService $typeService,
        DocumentableTypeService $docableTypeService
    ) {
        $this->service = $service;
        $this->modelService = $modelService;
        $this->typeService = $typeService;
        $this->docableTypeService = $docableTypeService;
    }

    /**
     * Vue d'ensemble des documents par type (Personne, Tracteur, Remorque)
     */
    public function index(Request $request)
    {
        $type = $request->get('type', 'Personne');
        $filtre = ExtractFiltre::extractFilter($request);
        
        $docableType = DocumentableType::where('nom', $type)->firstOrFail();
        $modelClass = $docableType->model_class;
        
        // On récupère les entités du type demandé avec leurs documents
        $query = $modelClass::with(['documents.documentType']);
        
        // On pourrait ajouter des filtres ici si nécessaire
        if ($request->search) {
            // Logique de recherche simple
            if ($type === 'Personne') {
                $query->where('nom', 'LIKE', "%{$request->search}%")
                      ->orWhere('prenom', 'LIKE', "%{$request->search}%");
            } else {
                $query->where('matricule', 'LIKE', "%{$request->search}%")
                      ->orWhere('numero_chassis', 'LIKE', "%{$request->search}%")
                      ->orWhere('immatriculation', 'LIKE', "%{$request->search}%");
            }
        }

        $entities = $query->paginate(10);
        
        // On récupère les modèles de documents requis pour ce type
        $requiredModels = $this->service->getRequiredDocumentModels($modelClass);

        return Inertia::render('Document/Index', [
            'entities' => $entities,
            'requiredModels' => $requiredModels,
            'currentType' => $type,
            'modelClass' => $modelClass,
            'documentableTypes' => DocumentableType::all(),
            'filters' => [
                'search' => $request->search,
                'type' => $type
            ]
        ]);
    }

    /**
     * Interface de paramétrage des modèles de documents
     */
    public function configIndex(Request $request)
    {
        $filtre = ExtractFiltre::extractFilter($request);
        $data = $this->docableTypeService->getAll($filtre);

        return Inertia::render('Document/Config', [
            'data' => $data,
            'documentableTypes' => $this->docableTypeService->getAll(),
            'documentTypes' => $this->typeService->getAll(),
            'filters' => [
                'search' => $request->search
            ]
        ]);
    }

    /**
     * Enregistrer un nouveau modèle de document (multi-lignes)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'documentable_type_id' => 'required|exists:documentable_types,id',
            'documents' => 'required|array|min:1',
            'documents.*.id' => 'nullable',
            'documents.*.document_type_name' => 'required|string',
            'documents.*.obligatoire' => 'boolean',
            'documents.*.expiration_required' => 'boolean',
            'documents.*.multiple_allowed' => 'boolean',
            'documents.*.alert_days' => 'integer|min:0',
            'documents.*.ordre' => 'integer',
            'documents.*.actif' => 'boolean',
        ]);

        $output = $this->modelService->syncModels($validated['documentable_type_id'], $validated['documents']);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Afficher les modèles d'une entité pour édition
     */
    public function show($id)
    {
        $allModels = DocumentModel::where('documentable_type_id', $id)
            ->with('documentType')
            ->orderBy('ordre')
            ->get()
            ->map(function($m) {
                return [
                    'id' => $m->id,
                    'document_type_name' => $m->documentType->nom,
                    'obligatoire' => (bool)$m->obligatoire,
                    'expiration_required' => (bool)$m->expiration_required,
                    'multiple_allowed' => (bool)$m->multiple_allowed,
                    'alert_days' => $m->alert_days,
                    'actif' => (bool)$m->actif,
                    'ordre' => $m->ordre,
                ];
            });

        return back()->with('data', [
            'documentable_type_id' => (int)$id,
            'documents' => $allModels->toArray()
        ]);
    }

    /**
     * Mettre à jour un modèle
     */
    public function update(Request $request, DocumentModel $configModel)
    {
        $validated = $request->validate([
            'documentable_type_id' => 'required|exists:documentable_types,id',
            'document_type_id' => 'required|exists:document_types,id',
            'obligatoire' => 'boolean',
            'expiration_required' => 'boolean',
            'alert_days' => 'integer|min:0',
            'ordre' => 'integer',
            'actif' => 'boolean',
        ]);

        $output = $this->modelService->update($configModel, $validated);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Supprimer tous les modèles d'une entité
     */
    public function destroy($id)
    {
        try {
            DocumentModel::where('documentable_type_id', $id)->delete();
            return back()->with('message.success', 'Configuration documentaire supprimée avec succès');
        } catch (\Exception $e) {
            return back()->with('message.error', 'Une erreur est survenue lors de la suppression');
        }
    }

    /**
     * API pour récupérer les documents requis et existants d'une entité
     */
    public function getRequiredDocuments(Request $request)
    {
        $request->validate([
            'model_class' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $modelClass = $request->model_class;
        $modelId = $request->model_id;

        $required = $this->service->getRequiredDocumentModels($modelClass);
        $existing = $this->service->getEntityDocuments($modelClass, $modelId)->groupBy('document_type_id');

        return response()->json([
            'required' => $required,
            'existing' => $existing
        ]);
    }

    /**
     * API pour récupérer les documents réels d'une entité
     */
    public function getEntityDocuments(Request $request)
    {
        $request->validate([
            'model_class' => 'required|string',
            'model_id' => 'required|integer',
        ]);

        $documents = $this->service->getEntityDocuments($request->model_class, $request->model_id);
        return response()->json($documents);
    }

    /**
     * Upload d'un document via le système dynamique
     */
    public function upload(Request $request)
    {
        $validated = $request->validate([
            'documentable_type' => 'required|string',
            'documentable_id' => 'required|integer',
            'document_type_id' => 'required|exists:document_types,id',
            'fichier' => 'nullable|file|max:10240', // Optionnel si on crée un dossier
            'date_expiration' => 'nullable|date',
            'observation' => 'nullable|string',
            'parent_id' => 'nullable|exists:documents,id',
            'type' => 'nullable|in:fichier,dossier',
        ]);

        $modelClass = $validated['documentable_type'];
        if (!class_exists($modelClass)) {
            if ($request->wantsJson()) return response()->json(['error' => true, 'message' => 'Type d\'entité invalide']);
            return back()->with('message.error', 'Type d\'entité invalide');
        }

        $entity = $modelClass::findOrFail($validated['documentable_id']);
        
        $output = $this->service->saveDocument(
            $entity,
            $validated['document_type_id'],
            $request->file('fichier'),
            $validated['date_expiration'],
            $validated['observation'],
            $validated['parent_id'] ?? null,
            $validated['type'] ?? 'fichier'
        );

        if ($request->wantsJson()) return response()->json($output);

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }

    /**
     * Supprimer un document réel
     */
    public function deleteDocument(Document $document)
    {
        $output = $this->service->deleteDocument($document);

        if (request()->wantsJson()) {
            return response()->json($output);
        }

        return back()->with(
            $output['error'] ? 'message.error' : 'message.success',
            $output['message']
        );
    }
}
