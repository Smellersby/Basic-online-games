<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$lobby->name}}</title>
    <style>
:root {
    --snake:rgb(0, 0, 0);
    --snake1:rgb(0, 25, 187);
    --snake2:rgb(185, 0, 0);
    --background: #43b103;
    --cell: #67cb09;
    --food: hsl(47, 100%, 50%);
    --body: hsl(0, 0%, 100%);
}
body {
    margin: 0px;
    display: flex;
    justify-content: center;
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
    font-size: 42px;
    font-family: Arial, Helvetica, sans-serif;
}
h2{
    color: black;
    font-size: 18px;
    font-family: Arial, Helvetica, sans-serif;
}
h3{
    color: white;
    transition-duration: 0.5s;
    font-size: 32px;
    font-family: Arial, Helvetica, sans-serif;
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
.snake2{
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
    background-color: white;
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
    color: var(--snake1);
    background-color: var(--body);
    border-style: none;
}
.difficulty:active{
    background-color: gainsboro;
}
button{
    background-color: rgb(248, 248, 248);
    border-radius: 6px;
    border-style: solid;
    border-color: black;
    padding: 4px;
    border-width: 1px;
}
#buttonDiv{
    display: flex;
    width: 100%;
    justify-content: space-between;
}
#mainContainer{
    width: 700px;
    padding: 10px;
    background-color: rgb(255, 255, 255);
    display: flex;
    flex-direction: column;
    align-items: center;
}
#playerOneIndicator{
            color: var(--snake1)
        }
        #playerTwoIndicator{
            color: var(--snake2)
        }
        #players{
            display: flex;
        }
    </style>
</head>
<body>
    <div id="mainContainer">
        <div id="buttonDiv">
            @if($lobby->playerOne==Auth::id()||$lobby->playerTwo==Auth::id())
            <button id="exitButton" onclick="exit()">Leave game</button>
            @endif
            @if($lobby->creator==Auth::id())
            <button id="theEditButton" onclick=exit(1)>Edit</button>
            @endif     
        </div>
        <h1 id="lobbyHeader"></h1>
        <div id="players"><h2 id="playerOneIndicator"></h2><h2>&nbsp;VS&nbsp;</h2><h2 id="playerTwoIndicator"></h2></div>
        <h2 id="resultIndicatort">&nbsp;</h2>

        <div id="startingScreen">
            <div id="fieldContainer">
            </div>
        </div>
    </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    var root = document.querySelector(':root');
document.body.addEventListener("keydown", keyCheck)
let fieldContainer = document.getElementById("fieldContainer")

let inputKey //first, raw data
let currentKey1,currentKey2 //checked value
let lastKey1,lastKey2 //used value
let dead1,dead2
let fieldExists = false
let widthInput = 11
let heightInput = 11
let snake1Y,snake2Y
let snake1X,snake2X
let foodX,foodY
let foodEaten1,foodEaten2
let timerInterval
let hungry1
let foodExists
let randomColorSend
let savedPlayerTwo,savedPlayerOne
let speed = 250
let fun = false
const field = [];

function waitForP2(){
            getGameInfoSnake(true)
            console.log(savedPlayerOne)
            console.log(savedPlayerTwo)
            if(savedPlayerTwo.status=="ready"){
                updateStatus("ready");
                if(savedPlayerOne.status=="ready"){
                    clearInterval(syncInterval)
                    setTimeout(()=>{
                    console.log("Get ready")
                    resultIndicatort.innerHTML="Get ready!"
                    setTimeout(()=>{
                        resultIndicatort.innerHTML="Start !"
                        setTimeout(()=>{resultIndicatort.innerHTML="&nbsp;"},2000)
                        createField()
                    },2000)
                    },10)
                }
                
            }
        }

getGameInfoSnake(true)
function synchronise(){
    @auth
        
    setTimeout(()=>{
        syncInterval = setInterval(waitForP2, 50);
        if({{Auth::id()}}==savedPlayerTwo.id && savedPlayerOne!=null){
            updateStatus("ready");
            console.log("P2 ready")
        }
    },1000)
    @endauth
}
function updateStatus(status){
    $.ajax({
        url: '{{ route('lobbies.updateStatus') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            lobbyID: {{$lobby->id}},
            status:status
        }
    });
}



synchronise()

class cell {
    constructor(y, x) {
        //this.condition = "empty";
        this.id = String(y) + " " + String(x)
        this.ticksLeft = 0
        this.visual = document.getElementById(String(y) + " " + String(x))
    }
}

function keyCheck(event) {
    inputKey = String(event.key).toLowerCase()
    if (inputKey == "w" || inputKey == "a" || inputKey == "d" || inputKey == "s" || inputKey == "arrowup" || inputKey == "arrowdown" || inputKey == "arrowleft" || inputKey == "arrowright") {
        if (inputKey == "w" || inputKey == "arrowup") {
            if (lastKey1 != "arrowdown" && lastKey1 != "s") {
                currentKey1 = inputKey
            }
        } else if (inputKey == "s" || inputKey == "arrowdown") {
            if (lastKey1 != "arrowup" && lastKey1 != "w") {
                currentKey1 = inputKey
            }
        } else if (inputKey == "d" || inputKey == "arrowright") {
            if (lastKey1 != "arrowleft" && lastKey1 != "a") {
                currentKey1 = inputKey
            }
        } else if(inputKey == "a" || inputKey == "arrowleft"){
            if (lastKey1 != "arrowright" && lastKey1 != "d") {
                currentKey1 = inputKey
            }
        }
        console.log(currentKey1)
        //currentKey1=inputKey 
    }
    if (inputKey == "w" || inputKey == "a" || inputKey == "d" || inputKey == "s" || inputKey == "arrowup" || inputKey == "arrowdown" || inputKey == "arrowleft" || inputKey == "arrowright") {
        if (inputKey == "w" || inputKey == "arrowup") {
            if (lastKey2 != "arrowdown" && lastKey2 != "s") {
                currentKey1 = inputKey
            }
        } else if (inputKey == "s" || inputKey == "arrowdown") {
            if (lastKey2 != "arrowup" && lastKey2 != "w") {
                currentKey1 = inputKey
            }
        } else if (inputKey == "d" || inputKey == "arrowright") {
            if (lastKey2 != "arrowleft" && lastKey2 != "a") {
                currentKey1 = inputKey
            }
        } else if(inputKey == "a" || inputKey == "arrowleft"){
            if (lastKey2 != "arrowright" && lastKey2 != "d") {
                currentKey1 = inputKey
            }
        }
        console.log(currentKey1)
        //currentKey1=inputKey 
    }
}
function createField() {
    dead1=false
    dead2=false
    hungry1 = true
    hungry2 = true
    savedPlayerOne.direction="arrowup"
    savedPlayerTwo.direction="arrowdown"
    snake1Y = 6
    snake1X = 4
    snake2Y = 4
    snake2X = 6
    foodEaten1 = 4
    foodEaten2 = 4
    foodExists = false
    startingScreen.style.backgroundColor="var(--background)"
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
    //fieldContainer.setAttribute("style", "background-color:var(--background);")
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
    @auth
    if({{Auth::id()}}==savedPlayerOne.id){
        updateSnake1()
    }else if({{Auth::id()}}==savedPlayerTwo.id){
        updateSnake2()
    }
    @endauth
    setTimeout(() => { 
    getGameInfoSnake()
    setTimeout(() => { 

        hungry1 = true
        hungry2 = true
        @auth
        if (foodExists == false && {{Auth::id()}}==savedPlayerOne) {
            console.log("I cook!")
            do {
                randomX = Math.floor(Math.random()*(widthInput-1)+1);
                randomY = Math.floor(Math.random()*(heightInput-1)+1);
                snakeAround=false
                for(x=-1;x<2;x++){
                    for(y=-1;y<2;y++){
                        if(field[randomY-y][randomX+x].visual.className!="cell"){
                            snakeAround=true
                        }
                    }
                }
            } while (snakeAround==true);
            foodY=randomY;
            foodX=randomX;
            foodExists = true
        }
        field[foodY][foodX].visual.className += " food"
        @endauth

        if ((snake1X < widthInput && snake1X > -1) && (snake1Y < heightInput && snake1Y > -1) ) {
            if (field[snake1Y][snake1X].visual.className == "cell food") {
                hungry1 = false
                foodEaten1++
                foodExists = false
                field[snake1Y][snake1X].visual.className = "cell snake1"
                field[snake1Y][snake1X].ticksLeft = foodEaten1 - 1
            }else if(field[snake1Y][snake1X].ticksLeft>1){
                dead1=true
            }else {
                field[snake1Y][snake1X].visual.className += " snake1"
                field[snake1Y][snake1X].ticksLeft = foodEaten1
            }

        } else {
            dead1=true
        }

        if ((snake2X < widthInput && snake2X > -1) && (snake2Y < heightInput && snake2Y > -1) ) {
            if (field[snake2Y][snake2X].visual.className == "cell food") {
                hungry2 = false
                foodEaten2++
                foodExists = false
                field[snake2Y][snake2X].visual.className = "cell snake2"
                field[snake2Y][snake2X].ticksLeft = foodEaten2 - 1
            }else if(field[snake2Y][snake2X].ticksLeft>1){
                dead2=true
            }else {
                field[snake2Y][snake2X].visual.className += " snake2"
                field[snake2Y][snake2X].ticksLeft = foodEaten2
            }

        } else {
            dead2=true
        }

        death()

        for (let y = 0; y < field.length; y++) {
            for (let x = 0; x < field[y].length; x++) {
                if (hungry1 == true) {
                    if(field[y][x].visual.className=="cell snake1"){
                        field[y][x].ticksLeft--
                    }
                }
                if (hungry2 == true) {
                    if(field[y][x].visual.className=="cell snake2"){
                        field[y][x].ticksLeft--
                    }
                }
                if (field[y][x].ticksLeft < 1 && field[y][x].visual.className != "cell food") {
                    field[y][x].visual.className = "cell"
                }
            }
        }
        
    }, 100);
}, 100);
}
function getGameInfoSnake(sync=false){
    if(sync==true){
        foodX=5
        foodY=1
    }
    $.ajax({
            url: '{{ route('lobbies.getGameInfoSnake') }}',
            type: 'POST',
            dataType: 'json',
            data: {
            _token: '{{ csrf_token() }}', 
            lobby_id: lobbyId={{$lobby->id}},
            foodX: foodX,
            foodY: foodY,
            sync: sync
            },
            success: function(response) {
                //console.log("let me see",response)
                switch (response.lobby.speed) {
                case "super slow":
                    speed=2000
                    break;
                case "slow":
                    speed=1000
                    break;
                case "medium":
                    speed=500
                    break;
                case "fast":
                    speed=200
                    break;
                }
            foodX=response.lobby.foodX
            foodY=response.lobby.foodY
            lobbyHeader.innerHTML=response.lobby.name
            savedPlayerOne=response.playerOne
            savedPlayerTwo=response.playerTwo
            if((savedPlayerOne==null || savedPlayerTwo==null) && sync!=true){
                death("left")
            }
            if(sync!=true){
                updateStatus("in game");
                playerOneIndicator.innerHTML=response.playerOne.name
                playerTwoIndicator.innerHTML=response.playerTwo.name


                lastKey1 = response.playerOne.direction
                switch (lastKey1) {
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

                lastKey2=response.playerTwo.direction
                switch (lastKey2) {
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
            }
            },
            error: function(xhr, status, error) {
                //console.log("bad get");
            }
        
    });
}
function updateSnake1(){
    $.ajax({
        url: '{{ route('lobbies.updateSnake1') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            lobbyID: {{$lobby->id}},
            playerOneDirection:currentKey1
        },
        success: function(response) {
            if (response.success) {
                //console.log('good update',response); 
            } else {
                //console.log('update error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            //console.log('Request failed:', error);
        }
    });
}
function updateSnake2(){
    $.ajax({
        url: '{{ route('lobbies.updateSnake2') }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            lobbyID: {{$lobby->id}},
            playerTwoDirection:currentKey1
        },
        success: function(response) {
            if (response.success) {
                //console.log('good update',response); 
            } else {
                //console.log('update error:', response.message);
            }
        },
        error: function(xhr, status, error) {
            //console.log('Request failed:', error);
        }
    });
}

function death(reason){
    if(reason=="left"){
        clearInterval(timerInterval)
        alert("opponent left the game")
        setTimeout(synchronise,2000)
    }else if(dead1==true&&dead2==true){
        clearInterval(timerInterval)
        resultIndicatort.innerHTML="Tie!"
        setTimeout(synchronise,2000)
    }else if(dead1==true){
        clearInterval(timerInterval)
        resultIndicatort.innerHTML=savedPlayerTwo.name+" wins !"
        setTimeout(synchronise,2000)
    }else if(dead2==true){
        clearInterval(timerInterval)
        resultIndicatort.innerHTML=savedPlayerOne.name+" wins !"
        setTimeout(synchronise,2000)
    }
    
}



let alreadyTriedToExit=false
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
    
        const language = sessionStorage.getItem('language');
        if(language=="lv"){
            if(exitButton){
            exitButton.innerHTML="atgriezties"
            }
            @auth
            @if(Auth::id()==$lobby->creator)
            theEditButton.innerHTML="rediģēt"
            @endif
            @endauth
        }
    </script>
</body>
</html>