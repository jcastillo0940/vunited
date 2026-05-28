<?php

namespace App\Support\Media;

use App\Domain\Media\Models\Media;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class StoresUploadedMedia
{
    public static function store(
        Model $model,
        UploadedFile $file,
        string $collection,
        ?string $altText = null,
        bool $isPublic = true,
    ): Media {
        $existing = Media::query()
            ->where('mediable_type', $model::class)
            ->where('mediable_id', $model->getKey())
            ->where('collection', $collection)
            ->first();

        if ($existing) {
            Storage::disk($existing->disk)->delete($existing->path);
        }

        $path = $file->store('media', 'public');

        return Media::query()->updateOrCreate(
            [
                'mediable_type' => $model::class,
                'mediable_id' => $model->getKey(),
                'collection' => $collection,
            ],
            [
                'disk' => 'public',
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?? $file->getClientMimeType(),
                'size' => $file->getSize(),
                'alt_text' => $altText,
                'is_public' => $isPublic,
            ],
        );
    }
}
