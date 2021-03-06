<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Board;

class BoardController extends Controller
{
    
    public function index(){
        $boards = Auth::user()->boards->load('lists.cards');
        return response()->json(['boards' => $boards], 200);
    }

    public function show($board_id){
        // TODO: Find board by id
        $board = Board::find($board_id);

        if($board){
            if(Auth::user()->id == $board->user_id){
                return response()->json(['board' => $board], 200);
            }
            return response()->json(['status' => 'error', 'message' => 'unauthorized'], 401);
        }
        return response()->json(['error' => 'Not Found'], 404, []);
    }

    public function store(Request $request){
        $data = $request->all();

        $board = Auth::user()->boards()->create([
            'name' => $data['name'],
        ]);

        return response()->json(['board' => $board], 201);
    }

    public function update(Request $request, $board_id){
        $data = $request->all();
        // TODO: Create update or create user in the DB

        $board = Board::find($board_id);

        if($board){
            if(Auth::user()->id == $board->user_id){
                $board = Board::updateOrCreate([
                    'id' => $data['id']
                ],
                [
                    'name' => $data['name'],
                    'user_id' => Auth::user()->id
                ]
                );
                return response()->json(['status' => 'Updated'], 202);
            }
            return response()->json(['status' => 'error', 'message' => 'unauthorazed'], 401);
        }
        return response()->json(['error' => 'Not Found'], 404, []);
    }

    public function destroy($board_id){
        $board = Board::find($board_id);
        // TODO: Create delete board in the DB
        if($board){
            if(Auth::user()->id == $board->user_id){
                if($board->delete()){
                    return response()->json(['status' => 'Deleted'], 202); 
                }
                return response()->json(['error' => 'Bad Request'], 400); 
                }
            return response()->json(['status' => 'error', 'message' => 'unauthorazed'], 401);
        }
        return response()->json(['error' => 'Not Found'], 404, []); 
    }
}
