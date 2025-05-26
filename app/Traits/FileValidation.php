<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait FileValidation
{
    protected function validateAndStoreFile(UploadedFile $file, string $directory): string
    {
        $this->validateFile($file);
        return $this->storeFile($file, $directory);
    }

    protected function validateFile(UploadedFile $file): void
    {
        $allowedMimes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
        $maxSize = 5120; // 5MB dalam kilobytes

        if (!in_array($file->getClientOriginalExtension(), $allowedMimes)) {
            throw new \Exception('Format file tidak diizinkan. Format yang diizinkan: ' . implode(', ', $allowedMimes));
        }

        if ($file->getSize() > ($maxSize * 1024)) {
            throw new \Exception('Ukuran file melebihi batas maksimum ' . ($maxSize / 1024) . 'MB');
        }
    }

    protected function storeFile(UploadedFile $file, string $directory): string
    {
        try {
            $fileName = Str::random(40) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($directory, $fileName, 'public');
            
            // Debug info
            Log::info('File stored:', [
                'original_name' => $file->getClientOriginalName(),
                'stored_name' => $fileName,
                'path' => $path,
                'full_path' => Storage::disk('public')->path($path),
                'exists' => Storage::disk('public')->exists($path),
                'url' => Storage::disk('public')->url($path)
            ]);
            
            // Verifikasi file tersimpan
            if (!Storage::disk('public')->exists($path)) {
                throw new \Exception('File gagal disimpan di storage');
            }
            
            return $path;
        } catch (\Exception $e) {
            Log::error('Error storing file:', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            throw $e;
        }
    }

    protected function deleteFile(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
} 