<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\CandidateLanguage;
use App\Models\CandidateSkill;
use App\Models\Role;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use App\Models\Language;


class CreateCandidateProfileController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('candidate_profile');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Retrieve the id from the session
        $user_id = Auth::id();

        //get input array
        $skills = $request->skills;
        $languages= $request->languages;

        $request->validate([
            'first_name' => 'required|min:3|max:30',
            'last_name' => 'required|min:3|max:30',
            'phone_number' => 'required|numeric',
            'linkedin' => 'required|min:3',
            'github' => 'required|min:3',
            'education' => 'required|min:1|max:20',
            'role_id' => 'required|min:1|max:20',
        ]);

        // Create a Candidate object
        $candidate = new Candidate;

        Schema::disableForeignKeyConstraints();
  
        $candidate = Candidate::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'phone_number' => $request->phone_number,
            'linkedin' => $request->linkedin,
            'github' => $request->github,
            'education' => $request->education,
            'role_id' => $request->role_id,
            'user_id' => $user_id,
        ]);

        $candidate_id = $candidate->id;

        //insert into candidate_languages table depending on checked boxes
        $candidate_language = new CandidateLanguage;
        foreach ($languages as $language) {
            $candidate_language = CandidateLanguage::create([
                'candidate_id' => $candidate_id,
                'language_id' => $language,
            ]);
        }


        //insert into candidate_skills table depending on checked boxes
        $candidate_skill = new CandidateSkill;
        foreach ($skills as $skill) {
            $candidate_skill = CandidateSkill::create([
                'candidate_id' => $candidate_id,
                'skill_id' => $skill,
            ]);
        }
        
        Schema::enableForeignKeyConstraints();

        // Save it in the DB and check if it worked
        if ($candidate->save() && $candidate_language->save() && $candidate_skill->save())
            return redirect()->route('profile', ['name' => $candidate->first_name]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_id = Auth::id();
        $candidate = Candidate::where('user_id', $user_id)->first();

        //storing the values of the received objects into variables we'll use later
        $role_id = $candidate->role_id;
        $candidate_id = $candidate->id;

        //retrieving the role, language and skill values from their respective tables
        $candidate_role = Role::find($role_id);
        $candidate_language = Candidate::find($candidate_id)->languages;
        $candidate_skill = Candidate::find($candidate_id)->skills;

        //this will display the candidates who have this role usefol for the filtering later
        //$candidate_role = Role::find($role_id)->candidate;
        return view('display_candidate_profile', ['candidate' => $candidate, 'candidate_role' => $candidate_role, 'candidate_language' => $candidate_language, 'candidate_skill' => $candidate_skill]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
