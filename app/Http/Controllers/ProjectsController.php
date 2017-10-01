<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Project;

use Auth;

class ProjectsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where[] = ['user_id', '=', Auth::id()];
        if ($request->has('archived')) {
            $where[] = ['archived', '=', $request->get('archived')];
        }
        $projects = Project::where($where)
            ->with('color')->get();
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
        } else {
            return response()->json(['Unauthorized'], 401);
        }
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
        //TODO: Any person can update their project. Should be based on permissions
        //TODO: What happens when I edit not my task, user_id shouldn' change

        $this->validate($request, [
            'name' => 'required',
            'color_id' => 'integer|exists:colors,id',
            'archived' => 'boolean'
        ]);

        $project = Project::where('id', $id)->first();
        if ($project) {
            $project->name = $request->get('name');
            $project->color_id = $request->get('color_id');
            if ($request->has('boolean')) {
                $project->archived = $request->get('boolean');
            }
            $project->save();
            return $project;
        }
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
