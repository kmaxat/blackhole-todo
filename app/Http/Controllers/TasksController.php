<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;
use Log;
use DB;

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
        $tasks = Task::active()->with('labels')->where($where)->orderBy('due_at')->get();
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
            'priority' => 'integer|min:1|max:4',
            'project_id' => 'exists:projects,id',
            'due_at' => 'date'
        ]);

        $task = Task::create([
            'description' => $request->get('description'),
            'priority' => $request->get('priority'),
            'user_id' => Auth::id(),
            'project_id' => $request->get('project_id'),
            'due_at' => $request->get('due_at')
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
            'status' => 'in:archived,deleted,completed',
            'project_id' => 'exists:projects,id',
            'due_at' => 'date',
            'label_id' => 'exists:labels,id',
        ]);
        $task = Task::where('id', $id)->first();
        if (!$task) {
            return json()->response()->json([
                'message' => 'Not found'
                ], 404);
        }

        if ($request->has('label_id')) {
            $labelIds = collect($request->get('label_id'))->values();
            //Retrieve currently existing labels
            $tasksAttached = DB::table('labellables')
            ->where('labellable_id', $task->id)
            ->where('labellable_type', 'App\Models\Task')->get();
            
            //Intersect $labelIds and $tasksAttached ids. Add not existing,
            //remove existing
            $newLabels = $labelIds->values()->toArray();
            $existingLabels = $tasksAttached->pluck('label_id')->toArray();
            
            //Diff to create new labels
            $insertIds = array_diff($newLabels, $existingLabels);
            $insertQuery = [];
            foreach ($insertIds as $key => $newLabel) {
                $insertQuery[] = [
                    'label_id' => $newLabel,
                    'labellable_id' => $task->id,
                    'labellable_type' => 'App\Models\Task'
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
                ->where('labellable_type', 'App\Models\Task')
                ->where('labellable_id', $task->id)
                ->whereIn('label_id', $idsToDelete)
                ->delete();
        }
        $task->fill($request->all());
        $task->save();
        return $task->load('labels');
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
