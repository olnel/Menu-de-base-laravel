<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

abstract class Controller
{

    public function storeFile(Request $request, String $data_index, String $fileName, String $folder, String $extension = null)
    {
        if($file = $request->file($data_index))
        {
            $fileName = Str::slug($fileName, '_') . '_' . Carbon::now()->getTimestampMs() . '.' . ($extension ?? $file->getClientOriginalExtension());

            Storage::disk('public')->putFileAs($folder, $file, $fileName);

            return "/storage/$folder/$fileName";
        }

        return null;
    }


}
