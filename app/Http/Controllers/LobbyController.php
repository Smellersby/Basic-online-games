<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\lobbies;
use App\Models\fields;

class LobbyController extends Controller
{
    public function getGameInfo(Request $request){
        $lobby = lobbies::find($request->input('lobby_id'));
        $playerOne = User::find($lobby->playerOne);
        $playerTwo = User::find($lobby->playerTwo);
        $fields = fields::where('lobbyID', $lobby->id)->get();
        
        $response = [
            'lobby' => $lobby,
            'playerOne' => $playerOne,
            'playerTwo' => $playerTwo,
            'fields' => $fields
        ];
        return response()->json($response);
    }
    public function playerLeave(Request $request){
        $lobby = lobbies::find($request->input('lobby_id'));
        if ($lobby) {
            $player= auth()->id(); // either 'playerOne' or 'playerTwo'
            if($lobby->playerOne==$player){
                $lobby->playerOne=null;
            }else if($lobby->playerTwo==$player){
                $lobby->playerTwo = null;
            }
            $lobby->save();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false, 'message' => 'Lobby not found'], 404);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $lobbies = lobbies::all()->sortByDesc('created_at');
        $users = User::all()->sortByDesc('created_at');
        return view('lobbies.index', compact('lobbies','users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
        $lobby = lobbies::find($id);
        $users = User::all();
        $fields = fields::all();
        if (auth()) {
            if($lobby->playerOne==NULL){
                $lobby->playerOne=auth()->id();
            }else if($lobby->playerTwo==NULL){
                $lobby->playerTwo=auth()->id();
            }
            $lobby->save();
        }
        if($lobby->gameType=="tic-tac-toe"){
            return view('lobbies.ticTacToe', compact('lobby','users','fields'));
        }else if($lobby->gameType=="snake"){
            return view('lobbies.snake', compact('lobby','users','fields'));
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Lobbies::findOrfail($id)->delete();
        return redirect()->route('lobbies.index');
    }
}
