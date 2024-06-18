<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Game lobbies</title>
<style>
    #accountManagment{
        display: flex;
        align-items: center;
        gap: 10px;
        align-self: flex-end;
    }
    button{
        background-color: rgb(248, 248, 248);
        border-radius: 6px;
        border-style: solid;
        border-color: black;
        padding: 4px;
        border-width: 1px;
    }
    button:hover{
        background-color: rgb(226, 226, 226);
    }
    body{
        margin:0px;
        display: flex;
        justify-content: center;
    }
    #mainContainer{
        width: 1000px;
        padding: 10px;
        background-color: rgb(244, 246, 247);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    td{
        background-color: rgb(250, 251, 252);
        padding: 5px 15px;
        min-width: 50px;
    }
    form{
        margin: 5px 0px;
    }
</style>
</head>
<body>
    <div id="mainContainer">
        <div id="accountManagment">
            @auth 
            <p>logged in as {{ $users[Auth::id()-1]->name }}</p>
            <a href="{{ url('/dashboard') }}"><button>dashboard</button></a>
            @endauth
            @guest
            <a href="{{ url('/login') }}"><button>log in</button></a>
            <a href="{{ url('/register') }}"><button>register</button></a>
            @endguest
        </div>
        <h1>Welcome to Basic-online-games</h1>
        <table>
            <tr>
                <th>Title</th>
                <th>Game</th>
                <th>Creator</th>
                <th>Player one</th>
                <th>Player two</th>
                <th><!--buttons--></th>
            </tr>
        @foreach ($lobbies as $lobby)
            <tr>
                <td>{{ $lobby->name }}</td>
                <td>{{$lobby->gameType}}</td>
                <td>{{$users[$lobby->creator-1]->name }}</td>
                <td>
                    @if ($lobby->playerOne-1>=0)
                    {{$users[$lobby->playerOne-1]->name}}
                    @endif
                </td>
                <td>
                    @if ($lobby->playerTwo-1>=0)
                    {{$users[$lobby->playerTwo-1]->name}}
                    @endif
                </td>
                <td><!--buttons-->
                    @if ($lobby->playerTwo!=NULL && $lobby->playerTwo!=NULL)
                    <form method="POST" action="{{ route('lobbies.show', $lobby->id) }}">
                        @csrf
                        @method('GET')
                        <button type="submit">spectate</button>
                    </form>
                    @endif
                    
                    @auth
                    @if ($lobby->playerTwo==NULL || $lobby->playerTwo==NULL)
                        <form method="POST" action="{{ route('lobbies.show', $lobby->id) }}">
                            @csrf
                            @method('GET')
                            <button type="submit">join</button>
                        </form>
                    @endif
                    @endauth
                    @if ($lobby->creator==Auth::id())
                        <form method="POST" action="{{ route('lobbies.destroy', $lobby->id) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit">delete</button>
                        </form>
                    @endif
                </td>
            </tr>
        @endforeach
        </table> 
    </div>
</body>
</html>