<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReparationVehiculeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicule_id' => 'required|exists:vehicules,id',
            'date_reparation' => 'required|date',
            'articles' => 'required|array|min:1',
            'articles.*.details' => 'required|array|min:1',
            'articles.*.details.*.article_changer_id' => 'required_without:articles.*.details.*.article_id|exists:articles,id',
            'articles.*.details.*.article_id' => 'required_without:articles.*.details.*.article_changer_id|exists:articles,id',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $articles = $this->input('articles', []);
            $hasArticleARemplacer = false;

            foreach ($articles as $articleIndex => $article) {
                $details = $article['details'] ?? [];
                foreach ($details as $detailIndex => $detail) {
                    if (!empty($detail['article_changer_id']) || !empty($detail['article_id'])) {
                        $hasArticleARemplacer = true;
                    }

                    // Validate technician name for main labor (right side)
                    if (
                        (floatval($detail['tarifs_horaires'] ?? 0) > 0 ||
                         floatval($detail['nbre_heure'] ?? 0) > 0 ||
                         floatval($detail['total_main_oeuvre'] ?? 0) > 0) &&
                        empty($detail['technicien'])
                    ) {
                        $validator->errors()->add(
                            "articles.{$articleIndex}.details.{$detailIndex}.technicien",
                            'Le nom du technicien est obligatoire pour la main d\'œuvre (Installation/Réparation).'
                        );
                    }

                    // Validate technician name for changer labor (left side)
                    if (
                        (floatval($detail['tarifs_horaires_changer'] ?? 0) > 0 ||
                         floatval($detail['nbre_heure_changer'] ?? 0) > 0 ||
                         floatval($detail['total_main_oeuvre_changer'] ?? 0) > 0) &&
                        empty($detail['technicien_changer'])
                    ) {
                        $validator->errors()->add(
                            "articles.{$articleIndex}.details.{$detailIndex}.technicien_changer",
                            'Le nom du technicien est obligatoire pour la main d\'œuvre associée (Remplacement).'
                        );
                    }
                }
            }

            if (!$hasArticleARemplacer) {
                $validator->errors()->add(
                    'articles',
                    'La réparation doit contenir au moins un article à remplacer ou une pièce de rechange.'
                );
            }
        });
    }

    public function messages(): array
    {
        return [
            'vehicule_id.required' => 'Le véhicule est obligatoire.',
            'vehicule_id.exists' => 'Le véhicule sélectionné n\'existe pas.',
            'date_reparation.required' => 'La date de réparation est obligatoire.',
            'articles.required' => 'Au moins un article est requis.',
            'articles.min' => 'Au moins un article est requis.',
            'articles.*.details.required' => 'Chaque article doit avoir au moins un détail.',
            'articles.*.details.*.article_changer_id.required_without' => 'Veuillez sélectionner un article à remplacer ou une pièce de rechange.',
            'articles.*.details.*.article_id.required_without' => 'Veuillez sélectionner un article à remplacer ou une pièce de rechange.',
        ];
    }
}
