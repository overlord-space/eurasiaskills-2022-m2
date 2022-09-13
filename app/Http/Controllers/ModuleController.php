<?php

namespace App\Http\Controllers;

use App\Http\Requests\ModuleAddRequest;
use App\Models\Module;
use Exception;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use ZipArchive;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = Module::query()->get();

        return view('pages.user.modules.module_list')->with([
            'modules' => $modules,
        ]);
    }

    public function batchUpload(ModuleAddRequest $request)
    {
        $statuses = [];

        $zip = new ZipArchive();
        $openingStatus = $zip->open($request->file('modules')->path());

        throw_if($openingStatus !== true, ValidationException::withMessages([
            'modules' => 'Something went wrong while unpacking archive',
        ]));

        $uploadKey = Str::random(18);
        $uploadPath = storage_path('app/modules/' . $uploadKey);

        $zip->extractTo($uploadPath);

        // delete useless files
        foreach (File::files($uploadPath) as $uselessFile) {
            echo 'delete ' . $uselessFile->getFilename();
            File::delete($uselessFile);
        }

        $moduleDirectories = File::directories($uploadPath);

        throw_if(empty($moduleDirectories), ValidationException::withMessages([
            'modules' => 'Provided archive does not contain any modules',
        ]));

        // validate module directories
        foreach ($moduleDirectories as $moduleDirectory) {
            $moduleName = last(explode(DIRECTORY_SEPARATOR, $moduleDirectory));

            try {
                [
                    $moduleNameFromInfo,
                    $moduleDesc,
                ] = $this->validateModuleStructure($moduleDirectory);

                $moduleName = $moduleNameFromInfo ?: $moduleName;

                /** @var Module $module */
                $module = Module::make([
                    'user_id' => $request->user()->id,
                    'upload_key' => $uploadKey,
                    'name' => $moduleName,
                    'desc' => $moduleDesc,
                ]);

                $module->save();

                $statuses[] = "[${moduleName}] Uploaded";
            } catch (Exception $e) {
                $statuses[] = "[${moduleName}] " . $e->getMessage();
                File::deleteDirectory($moduleDirectory);
            }
        }

        $zip->close();

        return redirect()->back()->with('status', $statuses);
    }

    protected function validateModuleStructure(string $directory)
    {
        $modInfoFilePath = $directory . DIRECTORY_SEPARATOR . 'modinfo.json';

        if (!File::exists($modInfoFilePath)) {
            throw new Exception("Unable to locate modinfo.json");
        }

        try {
            $modInfoContents = json_decode(file_get_contents($modInfoFilePath), true);
            // resolve module name
            $name = $modInfoContents['about']['moduleNadme'] ?? '';
            // and description
            $desc = $modInfoContents['about']['moduleDesc'] ?? '';
        } catch (Exception) {
            throw new Exception("Corrupted modinfo.json");
        }

        return [
            $name,
            $desc,
        ];
    }

    public function download(Module $module)
    {
        $tmpArchivePath = storage_path('app/tmp/' . Str::random() . '.zip');

        $zip = new ZipArchive();
        $zip->open($tmpArchivePath, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $files = File::allFiles($module->getModuleDirectory());

        foreach ($files as $file) {
            $zip->addFile($file->getPathname(), $file->getRelativePathname());
        }

        $zip->close();

        return response()->download(
            $tmpArchivePath,
            $module->name . '.zip'
        )->deleteFileAfterSend();
    }

    public function destroy(Module $module)
    {
        $module->delete();

        return redirect()->back()->with('status', 'Module successfully deleted');
    }
}
