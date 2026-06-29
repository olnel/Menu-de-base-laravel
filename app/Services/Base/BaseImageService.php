<?php

namespace App\Services\Base;

use App\Services\ImageService;
use Dotenv\Util\Str;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

abstract class BaseImageService extends BaseService
{
    protected ImageService $imageService;

    protected array $imageFields = [
        'main' => [
            'field' => 'image',
            'path_field' => 'image',
            'thumb_field' => 'thumb_image',
            'path_prefix' => 'img/default',
            'name_prefix' => 'item',
            'create_thumb' => true,
            'max_dimension' => 1920,
            'quality' => 80,
            'format' => 'webp',
            'multiple' => false
        ]
    ];
    protected array $scope= ['filter' => 'search'];

    public function __construct($repository, ImageService $imageService)
    {
        parent::__construct($repository);
        $this->imageService = $imageService;
    }

    /**
     * Crée un nouvel élément avec gestion des images
     */
    public function create(array $validated): array
    {
        DB::beginTransaction();

        try {
            $imagesData = $this->extractImagesData($validated);
            $model = $this->repository->create($validated);

            $updateData = $this->processImagesData($imagesData, $model->id);

            if (!empty($updateData)) {
                $this->repository->update($model, $updateData);
            }

            DB::commit();
            return $this->successResponse('Création réussie');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erreur création : " . $e->getMessage());
            return $this->errorResponse('Erreur lors de la création', $e);
        }
    }

    /**
     * Met à jour un élément avec gestion des images
     */
    public function update($model, array $validated): array
    {
        DB::beginTransaction();

        try {
            $updateData = $this->handleImageUpdates($model, $validated);
            $this->repository->update($model, array_merge($validated, $updateData));

            DB::commit();
            return $this->successResponse('Mise à jour réussie');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erreur mise à jour : " . $e->getMessage());
            return $this->errorResponse('Erreur lors de la mise à jour', $e);
        }
    }

    /**
     * Supprime un élément et ses fichiers associés
     */
    public function delete($model): array
    {
        DB::beginTransaction();

        try {
            $this->deleteAllImages($model);
            $this->repository->delete($model);

            DB::commit();
            return $this->successResponse('Suppression réussie');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Erreur suppression : " . $e->getMessage());
            return $this->errorResponse('Erreur lors de la suppression', $e);
        }
    }

    /****************************************************************
     * METHODES PROTEGEES
     ****************************************************************/

    protected function extractImagesData(array &$validated): array
    {
        $images = [];
        foreach ($this->imageFields as $config) {
            if (isset($validated[$config['field']])) {
                $images[] = [
                    'files' => $validated[$config['field']],
                    'config' => $config
                ];
                unset($validated[$config['field']]);
            }
        }
        return $images;
    }

    protected function processImagesData(array $imagesData, int|string $modelId): array
    {
        $updateData = [];

        foreach ($imagesData as $image) {
            $config = $image['config'];

            if ($config['multiple'] ?? false) {
                $updateData[$config['path_field']] = $this->processMultipleImages(
                    $image['files'],
                    $config,
                    $modelId
                );
            } else {
                $result = $this->processSingleImage(
                    $image['files'],
                    $config,
                    $modelId.'_'.$this->subName()
                );
                $updateData[$config['path_field']] = $result['main']['path'];

                if ($config['create_thumb'] && $config['thumb_field']) {
                    $updateData[$config['thumb_field']] = $result['thumb']['path'];
                }
            }
        }
        return $updateData;
    }

    protected function handleImageUpdates($model, array &$validated): array
    {
        $updateData = [];
        foreach ($this->imageFields as $config) {
            if (!isset($validated[$config['field']])) {
                continue;
            }


            if ($config['multiple'] ?? false) {
                $updateData[$config['path_field']] = $this->handleMultipleImageUpdate(
                    $model,
                    $config,
                    $validated[$config['field']]
                );
            } else {
                $updateData = array_merge(
                    $updateData,
                    $this->handleSingleImageUpdate(
                        $model,
                        $config,
                        $validated[$config['field']]
                    )
                );
            }

            unset($validated[$config['field']]);
        }
        return $updateData;
    }

    protected function processSingleImage(
        UploadedFile $file,
        array $config,
        int|string $modelId
    ): array {
        return $this->processImage($file, $config, $modelId);
    }

    protected function processMultipleImages(
        array $files,
        array $config,
        int|string $modelId
    ): string {
        $imagePaths = [];
        foreach ($files as $file) {


            $result = $this->processImage($file, $config, $modelId.'_'.$this->subName());
            $imageData = ['main' => $result['main']['path']];

            if ($config['create_thumb'] && $config['thumb_field']) {
                $imageData['thumb'] = $result['thumb']['path'];
            }

            $imagePaths[] = $imageData;
        }
        return json_encode($imagePaths);
    }

    protected function handleSingleImageUpdate(
        $model,
        array $config,
        $inputValue
    ): array {
        $updateData = [];

        if ($inputValue instanceof UploadedFile) {
            $this->deleteImageFiles($model, $config);
            $result = $this->processImage($inputValue, $config, $model->id.'_'.$this->subName());

            $updateData[$config['path_field']] = $result['main']['path'];
            if ($config['create_thumb'] && $config['thumb_field']) {
                $updateData[$config['thumb_field']] = $result['thumb']['path'];
            }
        } elseif ($inputValue === $model->{$config['path_field']}) {
            // Même image, rien à faire
        }

        return $updateData;
    }

    protected function handleMultipleImageUpdate(
        $model,
        array $config,
        array $inputFiles
    ): string {
        $currentImages = json_decode($model->{$config['path_field']}, true) ?: [];
        $newImages = [];

        foreach ($inputFiles as  $file) {

            if ($file instanceof UploadedFile) {
                $result = $this->processImage($file, $config, $model->id.'_'.$this->subName());
                $imageData = ['main' => $result['main']['path']];

                if ($config['create_thumb'] && $config['thumb_field']) {
                    $imageData['thumb'] = $result['thumb']['path'];
                }

                $newImages[] = $imageData;
            } elseif (is_string($file)) {
                // Conserve les images existantes
                foreach ($currentImages as $currentImage) {
                    if ($currentImage['main'] === $file) {
                        $newImages[] = $currentImage;
                        break;
                    }
                }
            }
        }

//        $this->deleteMultipleImageFiles($model, $config);
        return json_encode($newImages);
    }

    protected function processImage(
        UploadedFile $file,
        array $config,
        int|string $modelId
    ): array {
        return $this->imageService->processWithMemorySafety(
            $file,
            "{$config['path_prefix']}/{$config['name_prefix']}_{$modelId}",
            [
                'max_dimension' => $config['max_dimension'],
                'quality' => $config['quality'],
                'create_thumb' => $config['create_thumb'],
                'format' => $config['format']
            ]
        );
    }

    protected function deleteAllImages($model): void
    {
        foreach ($this->imageFields as $config) {
            if ($config['multiple'] ?? false) {
                $this->deleteMultipleImageFiles($model, $config);
            } else {
                $this->deleteImageFiles($model, $config);
            }
        }
    }

    protected function deleteImageFiles($model, array $config): void
    {
        try {
            if (!empty($model->{$config['path_field']})) {
                $this->imageService->delete($model->{$config['path_field']});
            }

            if ($config['create_thumb'] && $config['thumb_field'] && !empty($model->{$config['thumb_field']})) {
                $this->imageService->delete($model->{$config['thumb_field']});
            }
        } catch (Exception $e) {
            Log::error("Erreur suppression fichiers : " . $e->getMessage());
        }
    }

    protected function deleteMultipleImageFiles($model, array $config): void
    {
        try {
            $images = json_decode($model->{$config['path_field']}, true) ?: [];

            foreach ($images as $image) {
                if (!empty($image['main'])) {
                    $this->imageService->delete($image['main']);
                }
                if (!empty($image['thumb'])) {
                    $this->imageService->delete($image['thumb']);
                }
            }
        } catch (Exception $e) {
            Log::error("Erreur suppression fichiers multiples : " . $e->getMessage());
        }
    }

    private function subName()
    {
        $aleatoire= \Illuminate\Support\Str::random(5).rand(1, 2500);
        $sub_name= date('YmdHis') .$aleatoire;
        return $sub_name;
    }
}
