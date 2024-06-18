<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\lobbies;
use App\Models\fields;

class LobbyController extends Controller
{
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
        $user = User::all();
        $fields = fields::all();
        if($lobby->gameType=="tic-tac-toe"){
            return view('lobbies.ticTacToe', compact('lobby','user','fields'));
        }else if($lobby->gameType=="snake"){
            return view('lobbies.snake', compact('lobby','user','fields'));
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
