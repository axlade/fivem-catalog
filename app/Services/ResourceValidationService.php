<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class ResourceValidationService
{
    /**
     * @var list<string>
     */
    private const PROHIBITED_EXTENSIONS = ['exe', 'dll', 'bat', 'sh', 'cmd', 'msi', 'com', 'scr', 'ps1'];

    /**
     * Pre-flight check on an uploaded FiveM resource ZIP, run before it's
     * moved to permanent storage: confirms it's a real archive containing a
     * valid fxmanifest.lua, and that it doesn't smuggle in an executable.
     *
     * Throws the same ValidationException the rest of the form uses, so the
     * failure surfaces in the standard $errors bag on the download_file field.
     *
     * @throws ValidationException
     */
    public function validateResourcePackage(UploadedFile $file): void
    {
        $zip = new ZipArchive();

        if ($zip->open($file->getRealPath()) !== true) {
            throw ValidationException::withMessages([
                'download_file' => 'The uploaded file is not a valid ZIP archive.',
            ]);
        }

        try {
            $hasManifest = false;
            $hasProhibitedFile = false;

            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);

                if ($name === false) {
                    continue;
                }

                if (basename($name) === 'fxmanifest.lua') {
                    $hasManifest = true;
                }

                $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

                if (in_array($extension, self::PROHIBITED_EXTENSIONS, true)) {
                    $hasProhibitedFile = true;
                }
            }

            if (! $hasManifest) {
                throw ValidationException::withMessages([
                    'download_file' => 'Invalid FiveM resource structure: fxmanifest.lua not found.',
                ]);
            }

            if ($hasProhibitedFile) {
                throw ValidationException::withMessages([
                    'download_file' => 'Security violation: Prohibited file types detected.',
                ]);
            }
        } finally {
            $zip->close();
        }
    }
}
