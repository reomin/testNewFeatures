<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Http\Resources\TodoResource;
use App\Models\Todo;
use GuzzleHttp\Psr7\Request;

class TodoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //collectionとは？？
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTodoRequest $request)
    {
        dd($request->validated());

        $todo = $request->user()->todos()->create($request->validated());

        return new TodoResource($todo);
    }

    /**
     * Display the specified resource.
     */
    public function show(Todo $todo)
    {
        return new TodoResource($todo);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTodoRequest $request, Todo $todo)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Todo $todo)
    {
        $todo->delete();
    }

    //   GET|HEAD        todos ..................................................................................... todos.index › TodoController@index
    //   POST            todos ..................................................................................... todos.store › TodoController@store
    //   GET|HEAD        todos/{todo} ................................................................................ todos.show › TodoController@show
    //   PUT|PATCH       todos/{todo} ............................................................................ todos.update › TodoController@update
    //   DELETE          todos/{todo} .......................................................................... todos.destroy › TodoController@destroy
}
