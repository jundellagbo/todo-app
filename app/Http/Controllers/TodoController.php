<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Todos;
use Auth;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    //
    public function index() {
        $todos = Todos::where("userid", Auth::user()->id)->get();
        return response()->json(["todos" => $todos], 200);
    }

    public function store( Request $req ) {
        
        $validator = Validator::make( $req->toArray(), [
            'todo' => 'unique:todos,todo'
        ]);

        if( $validator->fails() && $req->id == 0 ) {
            return response()->json(["response" => "exists"], 422);
        }

        $todo = $req->id == 0 ? new Todos : Todos::find( $req->id );
        $todo->todo = $req->todo;
        $todo->deadline = $req->deadline;
        $todo->duedate = $req->duedate;
        $todo->status = 0;
        $todo->userid = Auth::user()->id;
        $todo->save();
        return response()->json(["response" => "saved", "data" => Todos::find($todo->id)], 200);
    }

    public function remove( Request $req ) {
        $todo = Todos::find( $req->id );
        $todo->delete();
        return response()->json(["response" => "success"], 200);
    }

    public function set_status( Request $req ) {
        $todo = Todos::find( $req->id );
        $todo->status = $req->status;
        $todo->save();
        return response()->json(["response" => "success"], 200);
    }
}
