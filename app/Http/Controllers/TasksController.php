<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Log;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $where[] = ['user_id', '=', Auth::id()];
        if ($request->has('range')) {
            $range = $request->get('range');
            if ($range == 'today') {
                $where[] = ['due_at','<=', Carbon::today()];
            } elseif ($range == 'week') {
                $where[] = ['due_at', '<=', Carbon::today()->addDays(7)];
            }
        }
        $tasks = Task::where($where)->get();
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
        //TODO: What happens if I don't send the archive,
        //or send some arbitrary value
        $this->validate($request, [
            'priority' => 'integer|min:1|max:4',
            'archived' => 'boolean',
            'project_id' => 'exists:projects,id',
            'due_at' => 'date'
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
