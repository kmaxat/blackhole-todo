<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', Auth::id())->get();
        return $tasks;
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
            'description' => 'required',
            'priority' => 'integer|min:1|max:4'
        ]);

        $task = Task::create([
            'description' => $request->get('description'),
            'priority' => $request->get('priority'),
            'user_id' => Auth::id(),
        ]);
        return $task;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::where('id', $id)->where('user_id', Auth::id())->first();
        if ($task) {
            return $task;
        } else
            return response()->json(['Unauthorized'],401);
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
        $this->validate($request, [
            'priority' => 'integer|min:1|max:4'
        ]);
        $task = Task::where('id', $id)->first();
        if ($task) {
            $task->fill($request->all());
            $task->save();
            return $task;
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
        $task = Task::destroy($id);
        return $task;
    }
}