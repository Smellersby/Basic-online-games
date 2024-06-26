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
        color: #2f71eb;
        font-size: 36px;
        font-family: Arial, Helvetica, sans-serif;
    }
    h2{
        font-size: 20px;
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
        margin-top: 10px;
        padding: 10px;
        background-color: rgb(202 224 255);
        border-radius:15px;
        padding-bottom: 30px; 
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
            <p>{{__('messages.logged')}}{{ $users[Auth::id()-1]->name }}</p>
            <a href="{{ url('/dashboard') }}"><button>{{__('messages.dashboard')}}</button></a>
            @endauth
            
            @guest
            <a href="{{ url('/login') }}"><button>{{__('messages.logIn')}}</button></a>
            <a href="{{ url('/register') }}"><button>{{__('messages.register')}}</button></a>
            @endguest
            <form id="lvForm" method="GET" action="{{ route('lobbies.index')}}">
                @csrf
                @method('GET')
                <input type="hidden" id="selectLanguage" name="selectLanguage" value="lv">
                <button id="lvButton" type="submit">LV</button>
            </form>
            <form id="enForm" method="GET" action="{{ route('lobbies.index')}}">
                @csrf
                @method('GET')
                <input type="hidden" id="selectLanguage" name="selectLanguage" value="en">
                <button id="enButton" type="submit">EN</button>
            </form>
            
        </div>
        <h1 id="welcome">{{__('messages.welcome')}}</h1>
        <div id="listContainer">
            <h2>{{__('messages.listHeader')}}</h2>
            @auth
            <form id="createForm" method="POST" action="{{ route('lobbies.create') }}">
                @csrf
                @method('GET')
            <button type="submit">{{__('messages.create')}}</button>
            </form>
            @endauth
            <table>
                <tr>
                    <th>{{__('messages.title')}}</th>
                    <th>{{__('messages.game')}}</th>
                    <th>{{__('messages.creator')}}</th>
                    <th>{{__('messages.P1')}}</th>
                    <th>{{__('messages.P2')}}</th>
                    <th><!--buttons--></th>
                </tr>
                @foreach ($lobbies as $lobby)
                <tr>
                    <td>{{ $lobby->name }}</td>
                    <td>@if($lobby->gameType=="snake"){{__('messages.snake')}}@else{{__('messages.ticTac')}}@endif</td>
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
                        <form id="specForm" method="POST" action="{{ route('lobbies.show', $lobby->id) }}">
                            @csrf
                            @method('GET')
                            <button type="submit">{{__('messages.spectate')}}</button>
                        </form>
                        @endif
                        
                        @auth
                        @if ($lobby->playerOne==NULL || $lobby->playerTwo==NULL)
                            <form id="playForm" method="POST" action="{{ route('lobbies.show', $lobby->id) }}">
                                @csrf
                                @method('GET')
                                <button type="submit">{{__('messages.play')}}</button>
                            </form>
                        @endif

                        @if ($lobby->creator==Auth::id()|| $users[Auth::id()-1]->role=="admin")
                            <form method="GET" action="{{ route('lobbies.edit', $lobby->id) }}">
                                @csrf
                                <button type="submit">{{__('messages.edit')}}</button>
                            </form>
                        @endif
                        
                        @if ($lobby->creator==Auth::id()|| $users[Auth::id()-1]->role=="admin")
                            <form method="POST" action="{{ route('lobbies.destroy', $lobby->id)}}">
                                @csrf
                                @method('DELETE')
                                <button type="submit">{{__('messages.delete')}}</button>
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
            sessionStorage.setItem('language', 'lv');
            let language = sessionStorage.getItem('language');
            console.log(language); 
        })

        enButton.addEventListener('mousedown',()=>{
            sessionStorage.setItem('language', 'en');
            let language = sessionStorage.getItem('language');
            console.log(language); 
        })
        const language = sessionStorage.getItem('language');
        console.log(language);
        if(language=="lv" && welcome.innerHTML!="Laipni lūdzam Basic-online-games"){
            lvForm.submit()
        }else if(language=="en" && welcome.innerHTML!="Welcome to Basic-online-games"){
            enForm.submit()
        }

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