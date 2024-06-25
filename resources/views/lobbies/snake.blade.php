<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$lobby->name}}</title>
    <style>
:root {
    --snake:rgb(0, 0, 0);
    --snake1:rgb(3, 15, 92);
    --snake2:rgb(92, 3, 3);
    --background: #43b103;
    --cell: #67cb09;
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
    background-color: var(--background);
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
let currentKey1,currentKey2 //checked value
let lastKey1,lastKey2 //used value
let dead1,dead2
let fieldExists = false
let widthInput = 11
let heightInput = 11
let snake1Y,snake2Y
let snake1X,snake2X
let foodEaten1,foodEaten2
let timerInterval
let hungry1
let foodExists
let randomColorSend
let speed = 250
let fun = false
const field = [];

let slowButton = document.getElementById("slow")
slowButton.addEventListener("click", () => { speed = 700/*350*/ })
let mediumButton = document.getElementById("medium")
mediumButton.addEventListener("click", () => { speed = 250 })
let fastButton = document.getElementById("fast")
fastButton.addEventListener("click", () => { speed = 150 })
let foodBox = document.getElementById("extraFood")
foodBox.addEventListener("click", () => { fun = foodBox.checked })

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
        } else {
            if (lastKey1 != "arrowright" && lastKey1 != "d") {
                currentKey1 = inputKey
            }
        }
        //currentKey1=inputKey 
    }
}
function createField() {
    dead1=false
    dead2=false
    hungry1 = true
    hungry2 = true
    currentKey1= "arrowup"
    currentKey2= "arrowdown"
    snake1Y = 6
    snake1X = 4
    snake2Y = 4
    snake2X = 6
    foodEaten1 = 4
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
    hungry1 = true
    hungry2 = true
    if (foodExists == false) {
        do {
            randomX = Math.floor(Math.random() * widthInput);
            randomY = Math.floor(Math.random() * heightInput);
        } while (field[randomY][randomX].visual.className == "cell snake1"||field[randomY][randomX].visual.className == "cell snake2");
        field[randomY][randomX].visual.className += " food"
        foodExists = true
    }

    lastKey1 = currentKey1

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

    
    //lastKey2=currentKey2
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
    lastKey2=currentKey1 // for testing

    if ((snake1X < widthInput && snake1X > -1) && (snake1Y < heightInput && snake1Y > -1) ) {
        if (field[snake1Y][snake1X].visual.className == "cell food") {
            hungry1 = false
            foodEaten1++
            foodExists = false
            field[snake1Y][snake1X].visual.className = "cell snake1"
            field[snake1Y][snake1X].ticksLeft = foodEaten1 - 1
        }else if(field[snake1Y][snake1X].ticksLeft>1){
            dead1=true
            death();
        }else {
            field[snake1Y][snake1X].visual.className += " snake1"
            field[snake1Y][snake1X].ticksLeft = foodEaten1
        }

    } else {
        dead1=true
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
            dead2=true
            death();
        }else {
            field[snake2Y][snake2X].visual.className += " snake2"
            field[snake2Y][snake2X].ticksLeft = foodEaten2
        }

    } else {
        dead2=true
        death()
    }


    for (let y = 0; y < field.length; y++) {
        for (let x = 0; x < field[y].length; x++) {
            console.log(field[y][x].visual.className)
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
}

function death(){
    clearInterval(timerInterval)
    setTimeout(() => {
        if(dead1==true&&dead2==true){
            alert("tie")
        }else if(dead1==true){
            alert("player 2 wins")
        }else{
            alert("player 1 wins")
        }
        
    }, 200);
}
    </script>
</body>
</html>