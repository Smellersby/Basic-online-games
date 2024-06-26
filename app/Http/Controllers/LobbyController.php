<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
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

        
        $currentUser=User::find(auth()->id());
        if ($currentUser) {
            if($currentUser->id==$playerOne->id||$currentUser->id==$playerTwo->id){
                $currentUser->status="in game";
                $currentUser->save();
            }
        }

        if($playerOne==$playerTwo){
            $lobby->playerTwo=NULL;
            $lobby->save();
            $playerTwo = User::find($lobby->playerTwo);
        }
        
        $response = [
            'lobby' => $lobby,
            'playerOne' => $playerOne,
            'playerTwo' => $playerTwo,
            'fields' => $fields
        ];

        return response()->json($response);
    }
    public function updateGameInfo(Request $request){
        $theField= fields::where('lobbyID', $request->input('lobbyID'))
                  ->where('x', $request->input('x'))
                  ->where('y', $request->input('y'))
                  ->first();
        if($theField){
            $lobby=lobbies::find($request->input('lobbyID'));
            if($lobby->turn==1){
                $lobby->turn=2;
            }else{
                $lobby->turn=1;
            }
            $theField->cellState=$request->input('sign');
            $theField->save();
            $lobby->save();
            return response()->json(['success' => true,'theField'=>$theField]);
        }
        return response()->json(['success' => false, 'message' => 'field not found'], 404);
    }

    public function getGameInfoSnake(Request $request){
        $lobby = lobbies::find($request->input('lobby_id'));
        $playerOne = User::find($lobby->playerOne);
        $playerTwo = User::find($lobby->playerTwo);

        
        $currentUser=User::find(auth()->id()); 

        if($playerOne==$playerTwo){ //player duplicate check
            $lobby->playerTwo=NULL;
            $lobby->save();
            $playerTwo = User::find($lobby->playerTwo);
        }
        
        $response = [
            'lobby' => $lobby,
            'playerOne' => $playerOne,
            'playerTwo' => $playerTwo
        ];

        return response()->json($response);
    }
    
    public function updateSnake1(Request $request){
        $lobby = lobbies::find($request->input('lobbyID'));
        if (!$lobby) {
            return response()->json(['error' => 'Lobby not found'], 404);
        }
        $playerOne = User::find($lobby->playerOne);
        if (!$playerOne) {
            return response()->json(['error' => 'Player One not found'], 404);
        }
        if(!$request->input('playerOneDirection')){
            $playerOne->direction='arrowup';
        }else{
            $playerOne->direction=$request->input('playerOneDirection');
        }
        
        $playerOne->save();
        return response()->json(['success'=>true,'message' => 'Player direction updated successfully'], 200);
    }
    public function updateSnake2(Request $request){
        $lobby = lobbies::find($request->input('lobbyID'));
        if (!$lobby) {
            return response()->json(['error' => 'Lobby not found'], 404);
        }
        $playerTwo= User::find($lobby->playerTwo);
        if (!$playerTwo) {
            return response()->json(['error' => 'Player two not found'], 404);
        }
        
        if(!$request->input('playerTwoDirection')){
            $playerTwo->direction='arrowdown';
        }else{
            $playerTwo->direction=$request->input('playerTwoDirection');
        }
        $playerTwo->save();
        return response()->json(['success'=>true,'message' => 'Player direction updated successfully'], 200);
    }

    public function updateStatus(Request $request){
        $currentUser=User::find(auth()->id());
        $currentUser->status=$request->input('status');
        $currentUser->save();
        return response()->json(['success'=>true,'message' => 'Player direction updated successfully'], 200);
    }

    public function playerLeave(Request $request){
        $lobby = lobbies::find($request->input('lobby_id'));
        if ($lobby) {
            $player= auth()->id(); 
            User::find($player)->status="not in game";
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
    public function index(Request $request)
    {
        $lobbies = lobbies::all()->sortByDesc('created_at');
        $users = User::all()->sortByDesc('created_at');
        App::setLocale($request->input('selectLanguage'));
        return view('lobbies.index', compact('lobbies','users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        if (auth()->guest()) {
            abort(401, 'Only authorised users can create lobbies');
        }
        if(lobbies::where('creator',auth()->id())->first()){
            abort(403, 'You are not allowed to have more than one lobby');
        }
        App::setLocale($request->input('selectLanguage'));
        $users = User::all()->sortByDesc('created_at');
        return view('lobbies.create', compact('users'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        if ($request->name == null || $request->gameType == null) {
            return redirect()->route('lobbies.create');
        }
        $lobby = new lobbies();
        $lobby->name = $request->name;
        $lobby->gameType = $request->gameType;
        $lobby->speed = $request->speed;
        $lobby->creator=auth()->id();
        $lobby->save();
        for($y=0;$y<3;$y++){
            for($x=0;$x<3;$x++){
                fields::create([
                    'lobbyID' => $lobby->id,
                    'x' => $x,
                    'y' => $y,
                ]);
            }
        }
        
        return redirect()->route('lobbies.show', $lobby->id);

    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request,string $id)
    {
        App::setLocale($request->input('selectLanguage'));
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
    public function edit(Request $request,string $id)
    {
        $lobby = lobbies::find($id);
        if (auth()->id()!=$lobby->creator && User::find(auth()->id())->role!="admin") {
            abort(403,'You are not the author');
        }
        App::setLocale($request->input('selectLanguage'));
        return view('lobbies.edit', compact('lobby'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $lobby = lobbies::find($id);
        if (auth()->id()!=$lobby->creator && User::find(auth()->id())->role!="admin") {
            abort(403,'You are not the author');
        }
        if ($request->name == null || $request->gameType == null) {
            return redirect()->route('lobby.edit', $id);
        }
        $lobby->name = $request->name;
        $lobby->gameType = $request->gameType;
        $lobby->save();
        return redirect()->route('lobbies.show', $id);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $lobby = lobbies::find($id);
        if (auth()->id()!=$lobby->creator && User::find(auth()->id())->role!="admin") {
            abort(403,'You are not the author');
        }
        Lobbies::findOrfail($id)->delete();
        return redirect()->route('lobbies.index');
    }
}

    
class LanguageController extends Controller
{
public function changeLanguage($locale)
{
    Session::put('locale', $locale);
    return redirect()->back();
}
}
