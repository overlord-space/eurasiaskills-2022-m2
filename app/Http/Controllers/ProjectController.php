<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProjectCreateRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Module;
use App\Models\Project;
use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class ProjectController extends Controller
{
    public function index()
    {
        $projects = Project::query()->get();

        return response()->view('pages.user.projects.project_list', [
            'projects' => $projects,
        ]);
    }

    protected function scanVendorModules()
    {
        $modules = [];

        $categoryDirectories = File::directories(base_path('/iot/src/modules/'));

        foreach ($categoryDirectories as $categoryDirectory) {
            $categoryName = last(explode(DIRECTORY_SEPARATOR, $categoryDirectory));
            $modules[$categoryName] = [];

            $moduleDirectories = File::directories($categoryDirectory);

            foreach ($moduleDirectories as $moduleDirectory) {
                $moduleName = last(explode(DIRECTORY_SEPARATOR, $moduleDirectory));

                $modules[$categoryName][$categoryName . '\/' . $moduleName] = $moduleName;
            }
        }

        return $modules;
    }

    public function create(Request $request)
    {
        $vendorModules = $this->scanVendorModules();
        $userModules = Module::query()->where('user_id', $request->user()->id)->get(['id', 'user_id', 'name']);

        return response()->view('pages.user.projects.project_create', [
            'vendor_modules' => $vendorModules,
            'user_modules' => $userModules,
        ]);
    }

    public function store(ProjectCreateRequest $request)
    {
        $iotPath = base_path('iot');

        $myProfile = json_decode(file_get_contents($iotPath . DIRECTORY_SEPARATOR . 'myProfile.json'), true);

        $myProfile['iotmSettings']['name'] = $request->get('name');
        $myProfile['iotmSettings']['routerssid'] = $request->get('ssid');
        $myProfile['iotmSettings']['routerpass'] = $request->get('ssid_password');

        $userModules = Module::query()
            ->where('user_id', $request->user()->id)
            ->whereIn('id', $request->get('user-module') ?? [])
            ->get() ?? collect();
        $modules = collect([]);

        // collect vendor modules
        foreach ($request->get('vendor-module') ?? [] as $module) {
            [$categoryName, $moduleName] = explode('\/', $module);

            $modulePath = $iotPath . '/src/modules/' . $categoryName . DIRECTORY_SEPARATOR . $moduleName;

            if (File::exists($modulePath)) {
                $modules->push([
                    'category' => $categoryName,
                    'name' => $moduleName,
                    'path' => $modulePath,
                ]);
            }
        }

        // collect user modules
        foreach ($userModules as $module) {
            /** @var Module $module */
            $modules->push([
                'category' => 'custom',
                'name' => $module->name . '_' . $module->id,
                'path' => $module->getModuleDirectory(),
            ]);
        }

        // generate modules section for json config
        $myProfile['modules'] = $modules->groupBy('category')->map(function ($items, $category) {
            return $items->map(function ($module) use ($category) {
                return [
                    'path' => 'src\\modules\\' . $category . '\\' . $module['name'],
                    'active' => true,
                ];
            });
        })->toArray();

        /** @var Project $project */
        $project = Project::make([
            'user_id' => $request->user()->id,
            'name' => $request->get('name'),
            'file_name' => Str::uuid() . '.zip',
        ]);

        $this->createProjectArchive($project, $myProfile, $modules);

        $project->save();

        try {
            Http::acceptJson()->asJson()->post('http://nvafmzc-m2.wsr.ru/api/builds', [
                'competitor' => 'xbcu',
                'project' => $project->name,
                'url' => Storage::disk('builds')->url($project->file_name),
            ]);
        } catch (Exception $e) {
            // ignore external errors
        }

        return redirect()->route('project.list')->with('status', 'Project successfully created');
    }

    protected function createProjectArchive(Project $project, array $profile, Collection $modules)
    {
        $iotPath = base_path('iot');

        $zip = new ZipArchive();
        $zip->open($project->getBuildPath(), ZipArchive::CREATE | ZipArchive::OVERWRITE);

        $vendorFiles = File::allFiles($iotPath);

        foreach ($vendorFiles as $file) {
            if (!str_starts_with($file->getRelativePathname(), 'src/modules') || $file->getRelativePathname() === 'src/modules/' . $file->getFilename()) {
                $zip->addFile($file->getPathname(), $file->getRelativePathname());
            }
        }

        foreach ($modules as $module) {
            $moduleDir = 'src/modules/' . $module['category'] . '/' . $module['name'];
            $zip->addEmptyDir($moduleDir);

            foreach (File::allFiles($module['path']) as $moduleFile) {
                $zip->addFile($moduleFile->getPathname(), $moduleDir . DIRECTORY_SEPARATOR . $moduleFile->getRelativePathname());
            }
        }

        $zip->addFromString('myProfile.json', json_encode($profile, JSON_PRETTY_PRINT), ZipArchive::FL_OVERWRITE);

        $zip->close();
    }

    public function destroy(Project $project)
    {
        try {
            File::delete($project->getBuildPath());
        } catch (Exception) {
            // ignore filesystem error
        }

        $project->delete();

        return redirect()->route('project.list')->with('status', 'Project successfully deleted');
    }

    public function indexApi(Request $request)
    {
        throw_if(empty($request->user()), AuthenticationException::class);

        $projects = Project::query()->where('user_id', $request->user()->id)->get();

        return response()->json([
            'data' => ProjectResource::collection($projects),
        ]);
    }
}
