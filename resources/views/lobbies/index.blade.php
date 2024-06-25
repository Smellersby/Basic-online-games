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
    h1{
        font-size: 36px;
        font-family: Arial, Helvetica, sans-serif;
    }
    h2{
        font-size: 18px;
        font-family: Arial, Helvetica, sans-serif;
    }
    p,th,td,button{
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
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
        background-color: rgb(234, 236, 237);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    td{
        background-color: rgb(250, 251, 252);
        padding: 3px 15px;
        min-width: 50px;
        height: 35px;
    }
    form{
        margin: 5px 0px;
    }
    #listContainer{
        display: grid;
        align-items: center;
        grid-template-columns: 50% 50%;
    }
    #createForm{
        justify-self: end;
    }
    table{
        grid-column-start: span 2;
        width: 100%;
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
            <form method="GET" action="{{ route('lobbies.index')}}">
                @csrf
                @method('DELETE')
                <input type="hidden" id="selectLanguage" name="selectLanguage" value="lv">
                <button id="lvButton" type="submit">LV</button>
            </form>
            
        </div>
        <h1>{{__('messages.welcome')}}</h1>
        <div id="listContainer">
            <h2>List of lobbies</h2>
            @auth
            <form id="createForm" method="POST" action="{{ route('lobbies.create') }}">
                @csrf
                @method('GET')
            <button type="submit">create lobby</button>
            </form>
            @endauth
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
                        @if ($lobby->playerOne!=null)
                        {{$users[$lobby->playerOne-1]->name}}
                        @endif
                    </td>
                    <td>
                        @if ($lobby->playerTwo!=null)
                        {{$users[$lobby->playerTwo-1]->name}}
                        @endif
                    </td>
                    <td><!--buttons-->
                        @if ($lobby->playerOne!=NULL && $lobby->playerTwo!=NULL)
                        <form method="POST" action="{{ route('lobbies.show', $lobby->id) }}">
                            @csrf
                            @method('GET')
                            <button type="submit">spectate</button>
                        </form>
                        @endif
                        
                        @auth
                        @if ($lobby->playerOne==NULL || $lobby->playerTwo==NULL)
                            <form method="POST" action="{{ route('lobbies.show', $lobby->id) }}">
                                @csrf
                                @method('GET')
                                <button type="submit">play</button>
                            </form>
                        @endif

                        @if ($lobby->creator==Auth::id()|| $users[Auth::id()-1]->role=="admin")
                            <form method="GET" action="{{ route('lobbies.edit', $lobby->id) }}">
                                @csrf
                                <button type="submit">edit</button>
                            </form>
                        @endif
                        
                        @if ($lobby->creator==Auth::id()|| $users[Auth::id()-1]->role=="admin")
                            <form method="POST" action="{{ route('lobbies.destroy', $lobby->id)}}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">delete</button>
                            </form>
                        @endif
                        @endauth
                    </td>
                </tr>
            @endforeach
            </table>
        </div>
    </div>
    <script>
        lvButton.addEventListener('mousedown',()=>{
            localStorage.setItem('language', 'lv');
            let language = localStorage.getItem('language');
            console.log(language); 
        })

        function exit(){
            $.ajax({
            url: '{{ route('lobbies.playerLeave') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // Include CSRF token
                lobby_id: lobbyId={{$lobby->id}}
            }
            });
        }
        console.log("script works")
    </script>
</body>
</html>