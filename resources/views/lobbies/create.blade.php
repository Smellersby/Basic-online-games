<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create lobby</title>
</head>
<body>
    <div id="mainContainer">
    <h1>Create lobby</h1>
    <form action="{{ route('lobbies.store') }}" method="POST">
        @csrf
        <div>
            <label for="name">Title:</label>
            <input type="text" id="name" name="name" value="{{$users[Auth::id()-1]->name}}'s lobby" required>
        </div>


        <div>
            <label for="gameType">Game type:</label>
            <select id="gameType" name="gameType">
                <option value="snake">Snake</option>
                <option value="tic-tac-toe">Tic-tac-toe</option>
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
        <button type="submit">Create</button>
    </form>
    </div>
    <script>
        gameType.addEventListener('change', function () {
            console.log(gameType);
            if(gameType.value=="tic-tac-toe"){
                speed.value="super slow"
                speed.style.display="none"
                label.style.display="none"
            }else{
                speed.style.display="inline"
                label.style.display="inline"
            }
        });
    </script>
</body>
</html>
