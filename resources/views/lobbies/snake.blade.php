<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$lobby->name}}</title>
    <style>
:root {
    --snake1:rgb(3, 15, 92)0;
    --snake1:rgb(92, 3, 3)0;
    --background: #3e8000;
    --cell: #43b103;
    --food: hsl(0, 59%, 41%);
    --body: hsl(0, 0%, 100%);
}
body{
    background-color: var(--body);
    display: flex;
    flex-flow: column;
    align-items: center;
}
img{
    position: absolute;
    left: -50px;
    top: -50px;
    opacity: 0;
}
a{
    color: black;
    margin-top: 20px;
    margin-bottom: 20px;
}
h1{
    font-family: Arial, Helvetica, sans-serif;
    font-weight: 600;
    font-size: 20px;
    color: var(--body);
}
.cell{
    width: 25px;
    height: 25px;
    background-color: var(--cell);
    margin: 2px;
    border-radius: 3px;
}
#fieldContainer{
    display: flex;
    flex-flow: column;
}
.row{
    display: flex;
    
}
.snake1{
    background-color: var(--snake1);
}
.snake1{
    background-color: var(--snake2);
}
.food{
    background-color: var(--food);
}
#fieldContainer{
    width: fit-content;
    border-radius: 7px;
    padding: 5px;
}
#startingScreen{
    padding: 30px;
    width: fit-content;
    background-color: var(--cell);
    border-radius: 10px;
    margin-top: 30px;
    margin-bottom: 50px;
}
p{
    margin-top: 0px;
}
#startButton{
    margin-top: 10px;
    height: 50px;
}
#extraFoodP{
    font-family: Arial, Helvetica, sans-serif;
    font-weight: 600;
    margin-top: 10px;
    color: var(--body);
}
.difficulty{
    background-color: var(--body);
    width: 90px;
    height: 30px;
    border-radius: 8px;
    font-size: 18px;
    color: var(--snake);
    background-color: var(--body);
    border-style: none;
}
.difficulty:active{
    background-color: gainsboro;
}
    </style>
</head>
<body>
    <h1 id="lobbyHeader"></h1>
    <a href="../HTML/index.html">back to main menu</a>
        <div id="startingScreen">
            <h1>Choose speed</h1>
            <button class="difficulty" id="slow">supr slow</button>
            <button class="difficulty" id="medium">medium</button>
            <button class="difficulty" id="fast">fast</button><br>
            <p id="extraFoodP">Extra food <input id="extraFood" type="checkbox"></p>
            <button class="difficulty" id="startButton">Start Game</button>
            <h1 id="playerIndicator"></h1>
            <div id="fieldContainer">

            </div>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    var root = document.querySelector(':root');
document.body.addEventListener("keydown", keyCheck)
let startButton = document.getElementById("startButton")
startButton.addEventListener("click", createField)
let fieldContainer = document.getElementById("fieldContainer")

let inputKey //first, raw data
let currentKey //checked value
let lastKeyOne //used value
let lastKeyTwo 
let fieldExists = false
let widthInput = 11
let heightInput = 11
let snake1Y,snake1X
let snake2Y,snake2X
let foodEaten1,foodEaten2
let foodX,foodY
let timerInterval, preGameInterval
let hungry1,hungry2
let alive1,alive2
let playerOneId,playerTwoId
let foodExists
let start=0;
let speed = 250
let fun = false
const field = [];
let slowButton = document.getElementById("slow")
slowButton.addEventListener("click", () => { speed = 3000 })
/*
function getGameInfoSnake(){
            $.ajax({
                    url: '{{ route('lobbies.getGameInfoSnake') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                    _token: '{{ csrf_token() }}', 
                    lobby_id: lobbyId={{$lobby->id}}
                    },
                    success: function(response) {
                        if(response.lobby.gameType=="tic-tac-toe"){
                            exit();
                        }
                        lobbyHeader.innerHTML=response.lobby.name
                        if(response.playerOne!=null && response.playerTwo!=null){
                            console.log("ready to start")
                            if(response.lobby.start==0){
                                timerInterval = setInterval(gameLoop, speed);
                                start==1
                                clearInterval(preGameInterval)
                            }
                            playerIndicator.innerHTML= response.playerOne.name+" VS "+response.playerTwo.name;
                            foodEaten1=response.playerOne.length
                            foodEaten2=response.playerTwo.length
                            lastKeyOne=response.playerOne.direction
                            lastKeyTwo=response.playerTwo.direction
                            playerOneId=response.playerOne.id;
                            playerTwoId=response.playerTwo.id;
                            if(response.playerOne.alive==false||response.playerTwo.alive==false){
                                death()    
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("bad get");
                    }
                
            });
        }

       // preGameInterval = setInterval(getGameInfoSnake, 500);

        function updateGameInfoSnake(){
            $.ajax({
                url: '{{ route('lobbies.updateGameInfoSnake') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    lobbyID: {{$lobby->id}},
                    foodX: foodX,
                    foodY: foodY,
                    playerOneDirection: lastKeyOne,
                    playerTwoDirection: lastKeyTwo,
                    playerOneLength: foodEaten1,
                    playerTwoLength: foodEaten2,
                    playerOneAlive: alive1,
                    playerTwoAlive: alive2,
                    start:start
                },
                success: function(response) {
                    if (response.success) {
                        console.log('good update',response); 
                    } else {
                        console.log('update error:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Request failed:', error);
                }
            });
        }
*/
        function exit(reason){
            if(alreadyTriedToExit==false){
                alreadyTriedToExit=true
                $.ajax({
                    url: '{{ route('lobbies.playerLeave') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}', 
                        lobby_id: lobbyId={{$lobby->id}}
                    },
                    success: function(response) {
                        if(reason==1){//1=="edit"
                            window.location.href = '{{ route('lobbies.edit', $lobby->id) }}';
                        }else{
                            window.location.href = '/lobbies'; 
                        }
                    }
                });
            }
        }
        window.addEventListener('beforeunload', exit);


class cell {
    constructor(y, x) {
        //this.condition = "empty";
        this.id = String(y) + " " + String(x)
        this.ticksLeft = 0
        this.visual = document.getElementById(String(y) + " " + String(x))
    }
}

function keyCheck(event) {
    @auth
    inputKey = String(event.key).toLowerCase()
    if (inputKey == "w" || inputKey == "a" || inputKey == "d" || inputKey == "s" || inputKey == "arrowup" || inputKey == "arrowdown" || inputKey == "arrowleft" || inputKey == "arrowright") {
        if (inputKey == "w" || inputKey == "arrowup") {
            if (lastKey != "arrowdown" && lastKey != "s") {
                if({{Auth::id()}}==playerOneId){
                    currentKey1 = inputKey
                }else if({{Auth::id()}}==playerTwoId){
                    currentKey2 = inputKey
                }
            }
        } else if (inputKey == "s" || inputKey == "arrowdown") {
            if (lastKey != "arrowup" && lastKey != "w") {
                if({{Auth::id()}}==playerOneId){
                    currentKey1 = inputKey
                }else if({{Auth::id()}}==playerTwoId){
                    currentKey2 = inputKey
                }
            }
        } else if (inputKey == "d" || inputKey == "arrowright") {
            if (lastKey != "arrowleft" && lastKey != "a") {
                if({{Auth::id()}}==playerOneId){
                    currentKey1 = inputKey
                }else if({{Auth::id()}}==playerTwoId){
                    currentKey2 = inputKey
                }
            }
        } else {
            if (lastKey != "arrowright" && lastKey != "d") {
                if({{Auth::id()}}==playerOneId){
                    currentKey1 = inputKey
                }else if({{Auth::id()}}==playerTwoId){
                    currentKey2 = inputKey
                }
            }
        }
        //currentKey=inputKey 
    }
    @endauth
}
function createField() {
    alive1=true
    alive2=true
    hungry1 = true
    hungry2 = true
    currentKey1 = "arrowup"
    currentKey2 = "arrowdown"
    snake1Y = 4
    snake1X = 4
    snake2Y = 6
    snake2X = 6
    foodEaten = 4
    foodEaten2 = 4
    foodExists = false
    if (fieldExists == true) {
        clearInterval(timerInterval)

        while (fieldContainer.hasChildNodes()) {
            fieldContainer.removeChild(fieldContainer.firstChild);
        }
        for (let y = 0; y < field.length; y++) {
            for (let x = 0; x < field[y].length; x++) {
                field[y][x].id = null
                field[y][x].condition = null
            }
        }
    }
    fieldExists = true
    fieldContainer.setAttribute("style", "background-color:var(--background);")
    timerInterval = setInterval(gameLoop, speed);
    for (let y = 0; y < heightInput; y++) {
        const row = [];
        field[y] = row
        const visualRow = document.createElement("div");
        visualRow.className = "row";
        fieldContainer.appendChild(visualRow);
        for (let x = 0; x < widthInput; x++) {
            const visualCell = document.createElement("div");
            visualCell.className = "cell";
            visualCell.id = String(y) + " " + String(x);
            visualRow.appendChild(visualCell);
            field[y][x] = new cell(y, x);

        }
    }

}

function gameLoop() {
    //getGameInfoSnake();
    console.log("start loop")
    hungry = true
    if (foodExists == false) {
        console.log("create food")
        do {
            randomX = Math.floor(Math.random() * widthInput);
            randomY = Math.floor(Math.random() * heightInput);
        } while (field[randomY][randomX].visual.className == "cell snake");
        @auth
        if({{Auth::id()}}== playerOneId){
            field[randomY][randomX].visual.className += " food"
        }
        @endauth
        foodExists = true
    }

    lastKey = currentKey
    switch (lastKeyOne) {
        case 'arrowup':
            snake1Y--
            break;
        case 'w':
            snake1Y--
            break;
        case "arrowdown":
            snake1Y++
            break;
        case "s":
            snake1Y++
            break;
        case 'arrowleft':
            snake1X--
            break;
        case 'a':
            snake1X--
            break;
        case 'arrowright':
            snake1X++
            break;
        case 'd':
            snake1X++
            break;
    }
    switch (lastKeyTwo) {
        case 'arrowup':
            snake2Y--
            break;
        case 'w':
            snake2Y--
            break;
        case "arrowdown":
            snake2Y++
            break;
        case "s":
            snake2Y++
            break;
        case 'arrowleft':
            snake2X--
            break;
        case 'a':
            snake2X--
            break;
        case 'arrowright':
            snake2X++
            break;
        case 'd':
            snake2X++
            break;
    }

    if ((snake1X < widthInput && snake1X > -1) && (snake1Y < heightInput && snake1Y > -1) ) {
        if (field[snake1Y][snake1X].visual.className == "cell food") {
            hungry1 = false
            foodEaten1++
            foodExists = false
            field[snake1Y][snake1X].visual.className = "cell snake1"
            field[snake1Y][snake1X].ticksLeft = foodEaten1 - 1
        }else if(field[snake1Y][snake1X].ticksLeft>1){
            alive1=false
            death();
        }else {
            field[snake1Y][snake1X].visual.className += " snake1"
            field[snake1Y][snake1X].ticksLeft = foodEaten1
        }

    } else {
        alive1=false
        death()
    }

    if ((snake2X < widthInput && snake2X > -1) && (snake2Y < heightInput && snake2Y > -1) ) {
        if (field[snake2Y][snake2X].visual.className == "cell food") {
            hungry2 = false
            foodEaten2++
            foodExists = false
            field[snake2Y][snake2X].visual.className = "cell snake2"
            field[snake2Y][snake2X].ticksLeft = foodEaten2 - 1
        }else if(field[snake2Y][snake2X].ticksLeft>1){
            alive2=false
            death();
        }else {
            field[snake2Y][snake2X].visual.className += " snake2"
            field[snake2Y][snake2X].ticksLeft = foodEaten2
        }

    } else {
        alive2=false
        death()
    }

    for (let y = 0; y < field.length; y++) {
        for (let x = 0; x < field[y].length; x++) {
            if (hungry == true) {
                field[y][x].ticksLeft--
            }
            if (field[y][x].ticksLeft < 1 && field[y][x].visual.className != "cell food") {
                field[y][x].visual.className = "cell"
            }
        }
    }
    if({{Auth::id()}}==playerOneId || {{Auth::id()}}==playerTwoId) {
        updateGameInfoSnake()
    }
}

function death(){
    clearInterval(timerInterval)
    setTimeout(() => {
        if(alive1==false && alive2==false){
            alert("tie")
        }else if(alive1==false){
            alert("player two won")
        }else if(alive2==false){
            alert("player one won")
        }
        start=0
        //let preGameInterval = setInterval(getGameInfoSnake, 500);
    }, 3000)
}


    </script>
</body>
</html>