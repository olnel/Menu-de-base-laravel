<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Http\UploadedFile;

class ImageWithWebp implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Vérifier si le fichier est une instance valide de UploadedFile
        if (!$value instanceof UploadedFile) {
            $fail('Le fichier doit être une image valide.');
            return;
        }

        // Vérifier le type MIME
        $mimeType = $value->getMimeType();
        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/webp'];

        if (!in_array($mimeType, $allowedMimeTypes)) {
            $fail('Le fichier doit être une image de type JPG, PNG ou WebP.');
            return;
        }

        // Vérification supplémentaire de l'extension pour sécurité
        $extension = strtolower($value->getClientOriginalExtension());
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];

        if (!in_array($extension, $allowedExtensions)) {
            $fail("L'extension du fichier n'est pas autorisée.");
            return;
        }

        // Vérifier que le fichier est bien une image lisible
        if (!@getimagesize($value->getPathname())) {
            $fail('Le fichier n\'est pas une image valide ou est corrompu.');
        }
    }
}
