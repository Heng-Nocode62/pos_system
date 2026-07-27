<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class SupabaseStorageService
{
    public function upload(UploadedFile $file)
    {
        $fileName = Str::uuid().'.'.$file->getClientOriginalExtension();

        Http::withHeaders([
            'apikey' => env('SUPABASE_KEY'),
            'Authorization' => 'Bearer '.env('SUPABASE_KEY'),
            'Content-Type' => $file->getMimeType(),
        ])->withBody(
            file_get_contents($file),
            $file->getMimeType()
        )->post(
            env('SUPABASE_URL')
            .'/storage/v1/object/'
            .env('SUPABASE_BUCKET')
            .'/'.$fileName
        );

        return env('SUPABASE_URL')
            .'/storage/v1/object/public/'
            .env('SUPABASE_BUCKET')
            .'/'.$fileName;
    }
}