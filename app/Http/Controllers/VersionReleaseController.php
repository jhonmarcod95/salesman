<?php

namespace App\Http\Controllers;

use Auth;
use App\VersionRelease;
use App\VersionReleaseNote;
use App\VersionReleaseFeedback;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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
            ->with('feedbacks:id,version_release_id,user_id,feedback,created_at')
            ->with('feedbacks.user')
            ->orderBy('release_date', 'desc')
            ->orderBy('id','desc') //in case release date doesnt sort properly
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
            'version' => 'required|regex:/(.+).(.+)\.(.+)/i|unique:version_releases,version,NULL,id,deleted_at,NULL',
            'release_date' => 'required',
            'new_features.*.*' => 'required_without_all:updates.*.*,fixes.*.*',
            // 'updates.*.*' => 'required_without_all:new_features.*.*,fixes.*.*',
            // 'fixes.*.*' => 'required_without_all:new_features.*.*,updates.*.*'
        ],[
            'version.unique' => 'Version number already in database',
            'version.regex' => 'Invalid version format (please use year.version.fixes)',
            'required_without_all' => 'Please indicate your changes'
        ]);

        //Get release notes
        $new_features = $this->getReleaseNotes($request->new_features, 'new');
        $updates = $this->getReleaseNotes($request->updates, 'updates');
        $fixes = $this->getReleaseNotes($request->fixes, 'fixes');

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

    //FOR THE ENTIRE VERSION
    public function delete($id) {
        $versionRelease = VersionRelease::find($id);
        $versionRelease->delete();

        return $versionRelease;
    }

    //FOR INDIVIDUAL NOTES
    public function deleteItem($id) {
        $versionReleaseNote = VersionReleaseNote::find($id);
        $versionReleaseNote->delete();

        return $versionReleaseNote;
    }

    //Feedback Submission
    public function submitFeedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'version_release_id' => 'required',
            'email' => 'required_if:authenticated,false',
            'password' => 'required_if:authenticated,false',
            'feedback' => 'required',
            'authenticated' => 'required',
        ],[
            'email.exists' => 'User not found.'
        ]);
        $validator->validate();

        if ($request->authenticated) {
            VersionReleaseFeedback::create([
                'version_release_id' => $request->version_release_id,
                'user_id' => Auth::id(),
                'feedback' => $request->feedback
            ]);
        }
        else {
            //add password validation before submitting if not authenticated
            $user = User::where('email', $request->email)->first();
            $password = $request->password;

            $validator->after(function($validator) use ($user, $password) {
                if (!$user) $validator->errors()->add('email', 'User not found.');
                if (!Hash::check($password, $user->password)) $validator->errors()->add('password', 'Password is incorrect.');
            })->validate();

            VersionReleaseFeedback::create([
               'version_release_id' => $request->version_release_id,
               'user_id' => $user->id,
               'feedback' => $request->feedback
            ]);
        }

    }

    //Delete feedback item
    public function deleteFeedback(Request $request) {
        $validator = Validator::make($request->all(), [
            'email' => 'required_if:authenticated,false',
            'password' => 'required_if:authenticated,false',
            'authenticated' => 'required',
            'feedbackId' => 'required|exists:version_release_feedbacks,id'
        ],[
            'email.exists' => 'User not found.',
            'feedbackId.exists' => 'The feedback you are trying to delete does not exist.' //extra just in case lmao
        ]);
        $validator->validate();

        $feedback = VersionReleaseFeedback::where('id', $request->feedbackId)->first();

        if ($request->authenticated) { //if authenticated, simply check if feedback belongs to user
            $user = Auth::user();
            $validator->after(function($validator) use ($user, $feedback) {
                if ($feedback->user_id != $user->id) $validator->errors()->add(null, 'Unable to delete the feedback of another user.');
            })->validate();
        }
        else { //if not authenticated, verify email and password, then check if feedback belongs to user
            $user = User::where('email', $request->email)->first();
            $password = $request->password;

            $validator->after(function($validator) use ($user, $password, $feedback) {
                if (!$user) $validator->errors()->add('email', 'User not found.');
                if (!Hash::check($password, $user->password)) $validator->errors()->add('password', 'Password is incorrect.');
                if ($feedback->user_id != $user->id) $validator->errors()->add(null, 'Unable to delete the feedback of another user.');
            })->validate();
        }

        $feedback->delete();
    }
}
