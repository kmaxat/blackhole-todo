<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Label;

use Auth;
use Log;
use DB;

class LabelsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $labels = Label::where('user_id', Auth::id())->get();
        return $labels;
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
            'color_id' => 'required|exists:colors,id',
            'name' => 'required'
        ]);
        $label = Label::create([
            'color_id' => $request->get('color_id'),
            'name' => $request->get('name'),
            'user_id' => Auth::id(),
        ]);
        return $label;
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
            'color_id' => 'exists:colors,id',
            'task_id' => 'exists:tasks,id',
        ]);

        $label = Label::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$label) {
            return response()->json('Not found', 404);
        };

        
        if ($request->has('task_id')) {
            $taskAttached = DB::table('labellables')
            ->where('label_id', $label->id)
            ->where('labellable_id', $request->get('task_id'))
            ->where('labellable_type', 'App\Models\Task')->first();

            if (!$taskAttached) {
                $label->tasks()->attach($request->get('task_id'));
            }
        }

        $label->fill($request->all());
        $label->save();
        return $label;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $label = Label::where('id', $id)->where('user_id', Auth::id())->first();
        if (!$label) {
            return response()->json(404, 'Not found');
        };
        $label->delete();
        return response()->json([
            'message' => 'success',
        ], 200);
    }
}
