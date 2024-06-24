<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit lobby</title>
</head>
<body>
    <div id="mainContainer">
    <h1>Edit lobby</h1>
    <form action="{{ route('lobbies.update',$lobby->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div>
            <label for="name">Title:</label>
            <input type="text" id="name" name="name" value="{{$lobby->name}}" required>
        </div>


        <div>
            <label for="gameType">Game type:</label>
            <select id="gameType" name="gameType">
                <option value="snake" @if($lobby->gameType=="snake")selected @endif>Snake</option>
                <option value="tic-tac-toe"  @if($lobby->gameType=="tic-tac-toe")selected @endif>Tic-tac-toe</option>
            </select>
        </div>

        <div>
            <label  id="label" for="speed">Game type:</label>
            <select id="speed" name="speed">
                <option value="super slow">super slow (for demo)</option>
                <option value="slow">slow</option>
                <option value="medium">medium</option>
                <option value="fast">fast</option>
            </select>
        </div>
        <button type="submit">Edit</button>
    </form>
    </div>
    <script>
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
