<?php

namespace App\Http\Controllers;

use App\VersionRelease;
use App\VersionReleaseNote;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class VersionReleaseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('version-release.index');
    }

    //Version release without nav and sidebar
    public function indexPlain()
    {
        return view('version-release.index2');
    }

    public function all(Request $request) {
        $versionRelease = VersionRelease::select('id','version','release_date')
            ->with('releaseNotes:id,version_release_id,description,type')
            ->orderBy('release_date', 'desc')
            ->paginate($request->limit);

        $versionRelease->getCollection()->transform(function($item) {
            $data = [];
            $release_note = $item->releaseNotes->groupBy('type');
            
            //Remove item releaseNotes
            unset($item->releaseNotes);

            //Assign new data
            $data = $item;
            // $data['release_date'] = date("F j, Y", strtotime($item->release_date));
            $data['release_note'] = $release_note;

            //Return Data
            return $data;
        });

        return $versionRelease;
    }

    public function store(Request $request) {
        $request->validate([
            'version' => 'required|regex:/(.+).(.+)\.(.+)/i',
            'release_date' => 'required',
            'new_features.*.*' => 'required_without_all:updates.*.*,fixes.*.*',
            'updates.*.*' => 'required_without_all:new_features.*.*,fixes.*.*',
            'fixes.*.*' => 'required_without_all:new_features.*.*,updates.*.*'
        ],[
            'version.regex' => 'Invalid version format (please use year.version.fixes).',
            'new_features.*.*.required_without_all' => 'Please indicate your changes.',
            'updates.*.*.required_without_all' => 'Please indicate your changes.',
            'fixes.*.*.required_without_all' => 'Please indicate your changes.'
        ]);

        //Get release notes
        $new_features = $this->getReleaseNotes($request->new_features, 'new');
        $updates = $this->getReleaseNotes($request->updates, 'updates');
        $fixes = $this->getReleaseNotes($request->fixes, 'fixes');
        
        //Validate empty notes
        // if(empty($new_features) && empty($updates) && empty($fixes)) {
        //     throw ValidationException::withMessages(['release_note' => 'Version description is required. Indicate what is your changes.']);
        // }

        //Merge all notes
        $formattedArrayNotes = array_merge($new_features, $updates, $fixes);

        try {
            DB::transaction(function () use($request, $formattedArrayNotes) {
                //Store version release item
                $version_release = VersionRelease::create([
                    'version' => $request->version,
                    'release_date' => $request->release_date
                ]);

                //Store version release notes
                foreach ($formattedArrayNotes as $note) {
                    $versionNote = Arr::add($note, 'version_release_id', $version_release->id);
                    VersionReleaseNote::create($versionNote);
                }
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function getReleaseNotes($releaseNotes, $type) {
        $notes = array_filter($releaseNotes,  function ($item) {
            return !is_null($item['description']) && $item['description'] !== '';
        });

        //Return emty array if empty
        if(empty($notes)) { return []; }

        //Embed release type in array object
        $release_notes = [];
        foreach($notes as $note) {
            $release_notes[] = Arr::add($note, 'type', $type);
        }

        return $release_notes;
    }

    public function submitItem(Request $request) {
        if(isset($request->id)) {
            $versionReleaseNote = VersionReleaseNote::find($request->id);
            $versionReleaseNote->description = $request->description;
            $versionReleaseNote->save();
        } else {
            VersionReleaseNote::create([
                'version_release_id' => $request->version_release_id,
                'description'        => $request->description,
                'type'               => $request->type
            ]);
        }
    }
}
