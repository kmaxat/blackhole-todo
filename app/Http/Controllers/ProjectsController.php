<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

use Auth;
use Log;
use DB;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $where[] = ['user_id', '=', Auth::id()];
        $projects = Project::active()->where($where)->with(['color','labels'])
        ->get();
        return $projects;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
        'name' => 'required',
        'color_id' => 'integer|exists:colors,id'
        ]);

        $project = Project::create([
        'name' => $request->get('name'),
        'color_id' => $request->get('color_id'),
        'user_id' => Auth::id(),
        ]);
        return $project;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Project::where('id', $id)->where('user_id', Auth::id())
        ->with(['color', 'tasks'])
        ->first();
        if ($task) {
            return $task;
        }
        return response()->json(['Unauthorized'], 401);
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
        //TODO: Any person can update their project. Should be based on permissions
        //TODO: What happens when I edit not my task, user_id shouldn' change
        //TODO: Add proper json responses
        Log::info($request->all());
        $this->validate($request, [
            'color_id' => 'integer|exists:colors,id',
            'status' => 'in:archived',
            'label_id' => 'exists:labels,id',
        ]);

        $project = Project::where('id', $id)->first();
        if (!$project) {
            return json()->response()->json([
            'message' => 'Not found'
            ], 404);
        }

        if ($request->has('label_id')) {
            $labelIds = collect($request->get('label_id'))->values();
            
            //Retrieve currently existing labels
            $projectAttached = DB::table('labellables')
            ->where('labellable_id', $project->id)
            ->where('labellable_type', 'App\Models\Project')->get();
            
            //Intersect $labelIds and $projectAttached ids. Add not existing,
            //remove existing
            $newLabels = $labelIds->values()->toArray();
            $existingLabels = $projectAttached->pluck('label_id')->toArray();
            
            //Diff to create new labels
            $insertIds = array_diff($newLabels, $existingLabels);
            $insertQuery = [];
            foreach ($insertIds as $key => $newLabel) {
                $insertQuery[] = [
                    'label_id' => $newLabel,
                    'labellable_id' => $project->id,
                    'labellable_type' => 'App\Models\Project'
                ];
            }
            DB::table('labellables')->insert($insertQuery);

            //Diff to delete no longer used labels
            $labelsToDelete = array_diff($existingLabels, $newLabels);
            $idsToDelete = [];
            foreach ($labelsToDelete as $key => $labelToDelete) {
                $idsToDelete[] = $labelToDelete;
            }
            DB::table('labellables')
                ->where('labellable_type', 'App\Models\Project')
                ->where('labellable_id', $project->id)
                ->whereIn('label_id', $idsToDelete)
                ->delete();
        }

        $project->fill($request->all());
        $project->save();
        return $project->load('labels');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
    //TODO: Proper response for delete
    //TODO: What happens when I delete not my project
        $project = Project::destroy($id);
        return $project;
    }
}
