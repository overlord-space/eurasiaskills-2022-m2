<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

class Module extends Model
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function (self $module) {
            try {
                File::deleteDirectory($module->getModuleDirectory());

                $parentDirectory = $module->getUploadDirectoryByKey();
                if (File::isEmptyDirectory($parentDirectory)) {
                    File::deleteDirectory($parentDirectory);
                }
            } catch (Exception) {
                // ignore filesystem error
            }
        });
    }

    protected $fillable = [
        'user_id',
        'upload_key',
        'name',
        'desc',
    ];

    public function getUploadDirectoryByKey()
    {
        return storage_path('app/modules/' . $this->upload_key);
    }

    public function getModuleDirectory()
    {
        return $this->getUploadDirectoryByKey() . DIRECTORY_SEPARATOR . $this->name;
    }
}
