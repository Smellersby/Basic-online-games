<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit lobby</title>
    <style>
        
        h1{
        margin: 0px 0px 10px 0px;
        color: #2f71eb;
        font-size: 36px;
        font-family: Arial, Helvetica, sans-serif;
    }
    h2{
        color: rgb(61, 61, 61);
        font-size: 20px;
        font-family: Arial, Helvetica, sans-serif;
    }
    p,th,td,button{
        color: rgb(42, 42, 42);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 15px;
    }
    button{
        color: white;
        background-color: #2f71eb;
        border-radius: 7px;
        border-style: none;
        padding: 6px;
        border-width: 1px;
    }
    button:hover{
        background-color: #235ecb;
    }
    body{
        display: flex;
        justify-content: center;
    }
    #mainContainer{
        margin-top: 50px;
        width: 500px;
        padding: 20px;
        background-color: rgb(202 224 255);
        border-radius: 15px;
        display: flex;
        flex-direction: column;
    }
    div{
        padding: 5px;
    }
    #lang{
        align-self: flex-end;
        display: flex;
        flex-direction: row;
    }
    #enForm{
        margin-left: 10px
    }
    </style>
</head>
<body>
    <div id="mainContainer">
        <div id="lang">
        <form id="lvForm" method="GET" action="{{ route('lobbies.edit', $lobby->id)}}">
            @csrf
            @method('GET')
            <input type="hidden" id="selectLanguage" name="selectLanguage" value="lv">
            <button id="lvButton" type="submit">LV</button>
        </form>
        <form id="enForm" method="GET" action="{{ route('lobbies.edit', $lobby->id)}}">
            @csrf
            @method('GET')
            <input type="hidden" id="selectLanguage" name="selectLanguage" value="en">
            <button id="enButton" type="submit">EN</button>
        </form>
        </div>
    <h1 id="editH1">{{__('messages.Edit')}}</h1>
    <form action="{{ route('lobbies.update',$lobby->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">{{__('messages.title')}}:</label>
            <input type="text" id="name" name="name" value="{{$lobby->name}}" required>
        </div>


        <div>
            <label for="gameType">{{__('messages.gameType')}}:</label>
            <select id="gameType" name="gameType">
                <option value="snake" @if($lobby->gameType=="snake")selected @endif>{{__('messages.snake')}}</option>
                <option value="tic-tac-toe"  @if($lobby->gameType=="tic-tac-toe")selected @endif>{{__('messages.ticTac')}}</option>
            </select>
        </div>

        <div>
            <label  id="label" for="speed">{{__('messages.speed')}}:</label>
            <select id="speed" name="speed">
                <option value="super slow">{{__('messages.superSlow')}}</option>
                <option value="slow">{{__('messages.slow')}}</option>
                <option value="medium">{{__('messages.medium')}}</option>
                <option value="fast">{{__('messages.fast')}}</option>
            </select>
        </div>
        <button type="submit">{{__('messages.edit')}}</button>
    </form>
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
        if(language=="lv" && editH1.innerHTML!="Rediģēt spēli"){
            lvForm.submit()
        }else if(language=="en" && editH1.innerHTML!="Edit lobby"){
            enForm.submit()
        }

        function hide() {
            if(gameType.value=="tic-tac-toe"){
                speed.value="super slow"
                speed.style.display="none"
                label.style.display="none"
            }else{
                speed.style.display="inline"
                label.style.display="inline"
            }
        }
        hide()
        gameType.addEventListener('change', hide);
    </script>
</body>
</html>
