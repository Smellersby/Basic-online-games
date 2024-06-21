<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$lobby->name}}</title>
    <style>
:root {
    --snake: #000000;
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
.snake{
    background-color: var(--snake);
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
    <a href="../HTML/index.html">back to main menu</a>
        <div id="startingScreen">
            <h1>Choose speed</h1>
            <button class="difficulty" id="slow">slow</button>
            <button class="difficulty" id="medium">medium</button>
            <button class="difficulty" id="fast">fast</button><br>
            <p id="extraFoodP">Extra food <input id="extraFood" type="checkbox"></p>
            <button class="difficulty" id="startButton">Start Game</button>
        
        </div>

        <div id="funnyDiv">
            <img id="theFunny" src="../images/RDT_20231001_1919391163061592761754657.jpg" alt="" width="430px">
            <div style="" id="fieldContainer">
        </div>
            
        </div>
    <script>
    var root = document.querySelector(':root');
document.body.addEventListener("keydown", keyCheck)
let startButton = document.getElementById("startButton")
startButton.addEventListener("click", createField)
let fieldContainer = document.getElementById("fieldContainer")

let inputKey //first, raw data
let currentKey //checked value
let lastKey //used value
let fieldExists = false
let widthInput = 11
let heightInput = 11
let snakeY
let snakeX
let foodEaten
let timerInterval
let hungry
let foodExists
let randomColorSend
let speed = 250
let fun = false
const field = [];

let slowButton = document.getElementById("slow")
slowButton.addEventListener("click", () => { speed = 350 })
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
            if (lastKey != "arrowdown" && lastKey != "s") {
                currentKey = inputKey
            }
        } else if (inputKey == "s" || inputKey == "arrowdown") {
            if (lastKey != "arrowup" && lastKey != "w") {
                currentKey = inputKey
            }
        } else if (inputKey == "d" || inputKey == "arrowright") {
            if (lastKey != "arrowleft" && lastKey != "a") {
                currentKey = inputKey
            }
        } else {
            if (lastKey != "arrowright" && lastKey != "d") {
                currentKey = inputKey
            }
        }
        //currentKey=inputKey 
    }
}
function createField() {
    hungry = true
    currentKey = "arrowup"
    snakeY = 5
    snakeX = 5
    foodEaten = 4
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
    hungry = true
    if (fun == true) {
        foodExists = false
    }
    if (foodExists == false) {
        do {
            randomX = Math.floor(Math.random() * widthInput);
            randomY = Math.floor(Math.random() * heightInput);
        } while (field[randomY][randomX].visual.className == "cell snake");
        field[randomY][randomX].visual.className += " food"
        foodExists = true
    }

    lastKey = currentKey

    switch (lastKey) {
        case 'arrowup':
            snakeY--
            break;
        case 'w':
            snakeY--
            break;
        case "arrowdown":
            snakeY++
            break;
        case "s":
            snakeY++
            break;
        case 'arrowleft':
            snakeX--
            break;
        case 'a':
            snakeX--
            break;

        case 'arrowright':
            snakeX++
            break;
        case 'd':
            snakeX++
            break;
    }
    if ((snakeX < widthInput && snakeX > -1) && (snakeY < heightInput && snakeY > -1) ) {
        if (field[snakeY][snakeX].visual.className == "cell food") {
            hungry = false
            foodEaten++
            foodExists = false
            field[snakeY][snakeX].visual.className = "cell snake"
            field[snakeY][snakeX].ticksLeft = foodEaten - 1
            randomColor = String(Math.floor(Math.random() * 350));
            randomColorSend = "hsl( " + randomColor + ", 100%, 50%)"
            root.style.setProperty('--food', randomColorSend);
            randomColorSend = "hsl( " + randomColor + ", 90%, 37%)"
            root.style.setProperty('--cell', randomColorSend);
            randomColorSend = "hsl( " + randomColor + ", 80%, 30%)"
            root.style.setProperty('--background', randomColorSend);
            randomColorSend = "hsl( " + randomColor + ", 80%, 10%)"
            root.style.setProperty('--snake', randomColorSend);
            randomColorSend = "hsl( " + randomColor + ", 100%, 80%)"
            root.style.setProperty('--body', randomColorSend);
        }else if(field[snakeY][snakeX].ticksLeft>1){
            death();
        }else {
            field[snakeY][snakeX].visual.className += " snake"
            field[snakeY][snakeX].ticksLeft = foodEaten
        }

    } else {
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
}

function death(){
    clearInterval(timerInterval)
    setTimeout(() => {
        setTimeout(() => {
            let message = "your score is " + String(foodEaten - 4)
            alert(message)
        }, 1000);
    }, 200);
}
    </script>
</body>
</html>