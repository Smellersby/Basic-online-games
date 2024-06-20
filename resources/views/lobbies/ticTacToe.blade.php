<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$lobby->name}}</title>
    <style>
        /*red:#e10c0c, blue: #0e23de*/
        a{
            color: black;
        }
        body {
            margin: 0px;
            display: flex;
            justify-content: center;
        }
        .box{
        display: flex;
        justify-content: center;
        align-items: center;
        width:100px;
        height: 100px;
        background-color: rgb(255, 255, 255);
        font-family: Arial, Helvetica, sans-serif;
        font-size: 52px;
        font-weight: 750;
        position: relative;
        }
        .gridContainer {
        display: grid;
        gap: 10px;
        grid-template-columns: 100px 100px 100px;
        background-color: black;
        }
        h1{
            font-size: 42px;
            font-family: Arial, Helvetica, sans-serif;
        }
        h2{
            font-size: 18px;
            font-family: Arial, Helvetica, sans-serif;
        }
        h3{
            color: white;
            transition-duration: 0.5s;
            font-size: 32px;
            font-family: Arial, Helvetica, sans-serif;
        }
        #line{
            position: absolute;
            left: 10px;
            width: 300px;
            height: 15px;
            background-color: #a52a2a;
            z-index: 2;
            transition-duration: 0.5s;
        }
        button{
            align-self: flex-start;
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
        #mainContainer{
        width: 700px;
        padding: 10px;
        background-color: rgb(255, 255, 255);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    </style>
</head>
<body>
    <div id="mainContainer">
        @if($lobby->playerOne==Auth::id()||$lobby->playerTwo==Auth::id())
        <button onclick="exit()">Leave game</button>
        <br>
        @endif
        <h1 id="lobbyHeader">Tic-Tac-Toe</h1>
        <h2 id="playerIndicator"></h2>
        <h2 id="scoreIndicator">game score:</h2>
        <div class="gridContainer">
            <div class="box" id="00"></div>
            <div class="box" id="10"></div>
            <div class="box" id="20"></div>
            <div class="box" id="01"></div>
            <div class="box" id="11"></div>
            <div class="box" id="21"></div>
            <div class="box" id="02"></div>
            <div class="box" id="12"></div>
            <div class="box" id="22"></div>
        </div>
        <h2 id="turnIndicator">
        </h2>
        <h3 id="results"></h3>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>     
        const gameBoxes = [[],[],[]];
        let turnIndicator=document.querySelector("#turnIndicator")
        let resultScreen=document.querySelector("#results")
        let turn=1//whos turning
        let turnCount=0//to check when game ends
        let horizontalVictory=0
        let verticalVictory=0
        let diagonalVictory=0
        let lastSign=0
        let lockPlayer=1
        let alreadyTriedToExit=false


        function getGameInfo(){
            $.ajax({
                    url: '{{ route('lobbies.getGameInfo') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                    _token: '{{ csrf_token() }}', 
                    lobby_id: lobbyId={{$lobby->id}}
                    },
                    success: function(response) {
                        let j=0;
                        for(let x=0;x<3;x++){
                            for(let y=0;y<3;y++){
                                gameBoxes[x][y].innerHTML=response.fields[j].cellState
                                if(gameBoxes[x][y].innerHTML=="X"){
                                    gameBoxes[x][y].style.color="#e10c0c"
                                }else{
                                    gameBoxes[x][y].style.color="#0e23de"
                                }
                                j++;
                            }
                        }
                        turn=response.lobby.turn
                        lobbyHeader.innerHTML=response.lobby.name
                        if(response.playerOne!=null && response.playerTwo!=null){
                            playerIndicator.innerHTML= response.playerOne.name+" VS "+response.playerTwo.name;
                            if(response.lobby.turn==1){
                                turnIndicator.innerHTML=response.playerOne.name+" turns"
                            }else{
                                turnIndicator.innerHTML=response.playerTwo.name+" turns"
                            }
                            lockPlayer=1;
                            @auth
                                if((response.lobby.turn==1 && response.playerOne.id=={{Auth::id()}})||(response.lobby.turn==2 && response.playerTwo.id=={{Auth::id()}})){
                                    lockPlayer=0;
                                }
                            @endauth
                            victoryCheck();
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log("bad get");
                    }
                
            });
        }
        $(document).ready(function() {
            getGameInfo();
            setInterval(getGameInfo, 1200);
        });

        function exit(){
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
                        window.location.href = '/lobbies'; 
                    }
                });
            }
        }
        window.addEventListener('beforeunload', exit);

        function updateGameInfo(changedBox){
            $.ajax({
                url: '{{ route('lobbies.updateGameInfo') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    lobbyID: {{$lobby->id}},
                    x: changedBox.id[0],
                    y: changedBox.id[1],
                    sign: changedBox.innerHTML
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
        function changeCondition(){
            if(lockPlayer==0){
                if(turn==1&&!this.innerHTML){
                    this.innerHTML="X"
                    this.style.color="#e10c0c"
                    lockPlayer=1
                    updateGameInfo(this)
                }else if(!this.innerHTML){
                    this.innerHTML="O"
                    this.style.color="#0e23de"
                    lockPlayer=1
                    updateGameInfo(this)
                }else{
                    alert("This box is already filled")
                }
                fieldListen()
            }

        }
        //console.log(gameBoxes[0][1])
        function fieldListen(){
            for(let p=0;p<3;p++){
                for(let i=0;i<3;i++){
                    gameBoxes[p][i]=(document.querySelectorAll(".box")[i+p*3])
                    gameBoxes[p][i].addEventListener("click", changeCondition);
                }
            }
        }
        function victoryCheck(){
            if(gameBoxes[0][0].innerHTML==gameBoxes[1][1].innerHTML&&gameBoxes[0][0].innerHTML==gameBoxes[2][2].innerHTML&&gameBoxes[1][1].innerHTML!=0){
                victoryAlert("diagonal1",0)
            }else if(gameBoxes[1][1].innerHTML==gameBoxes[2][0].innerHTML&&gameBoxes[1][1].innerHTML==gameBoxes[0][2].innerHTML&&gameBoxes[1][1].innerHTML!=0){
                victoryAlert("diagonal2",2)
            }else{
                for(let p=0;p<3;p++){
                    for(let i=0;i<3;i++){
                        if (lastSign==gameBoxes[p][i].innerHTML&&lastSign){
                            horizontalVictory+=1
                        }else{
                            horizontalVictory=0
                        }
                        lastSign=gameBoxes[p][i].innerHTML
                    }
                    lastSign=0
                    if(horizontalVictory!=2){
                    horizontalVictory=0
                    }else{
                    victoryAlert("horizontal",p)
                    }
                    
                }
            }
            for(let i=0;i<3;i++){
                for(let p=0;p<3;p++){
                    if (lastSign==gameBoxes[p][i].innerHTML&&lastSign){
                        vericalVictory+=1
                    }else{
                        vericalVictory=0
                    }
                    lastSign=gameBoxes[p][i].innerHTML
                }
                lastSign=0
                if(vericalVictory!=2){
                vericalVictory=0
                }else{
                    victoryAlert("vertical",i)
                }
                
            }
            let tieBool=true
            for(let i=0;i<3;i++){
                for(let p=0;p<3;p++){
                    if(gameBoxes[p][i].innerHTML!="X"&&gameBoxes[p][i].innerHTML!="O"){
                        tieBool=false
                        break
                    }
                }
            }
            if(tieBool==true){
                resultScreen.style.color="#000000"
                resultScreen.innerHTML="Tie !"
                clearField(true)
            }

        }
        function clearField(tie){
            lockPlayer=1
            if(tie!=true){
                previousWidth=line.style.width
                if(previousWidth=="300px"||previousWidth=="400px"){
                line.style.width="0px"
                }else{
                line.style.height="0px"   
                }
            }
            //disgusting amount of timers
            setTimeout(() => {
                if(tie!=true){
                    line.style.transitionDuration="0.5s"//extending
                    if(previousWidth=="15px"){
                        line.style.height="300px"
                        
                    }else{
                    line.style.width="300px"  
                    if(previousWidth=="400px"){
                            line.style.width="400px"
                    }
                    }
                }

                for(let p=0;p<3;p++){
                    for(let i=0;i<3;i++){
                        gameBoxes[p][i].style.transitionDuration="0.3s"
                    }
                }
            }, 20);
            setTimeout(() => {
                for(let p=0;p<3;p++){
                    for(let i=0;i<3;i++){
                        gameBoxes[p][i].style.color="#ffffff"
                    }
                }
                if(tie!=true){
                line.style.color="#ffffff"
                line.style.backgroundColor="#a52a2a00"
                }
                
                resultScreen.style.color="#ffffff"
            }, 500); 
            setTimeout(() => { 
                for(let p=0;p<3;p++){
                    for(let i=0;i<3;i++){
                        gameBoxes[p][i].innerHTML=null
                        updateGameInfo(gameBoxes[p][i])
                        gameBoxes[p][i].style.transitionDuration="0s"
                        gameBoxes[p][i].style.color="#000000"
                    }
                }
            }, 1000);
        }
        function victoryAlert(axis,coordinate){
            if(turn==1){
                resultScreen.style.color="#0000ff"
                resultScreen.innerHTML="O wins !"
            }else{
                resultScreen.style.color="#ff0000"
                resultScreen.innerHTML="X wins !"
            }
            const line = document.createElement("div")
            line.id="line";
            line.style.transitionDuration="0s"
            if(axis=="vertical"){
            line.style.width="15px"
            line.style.height="300px" 
            line.style.left="43px" 
            line.style.top="10px" 
            gameBoxes[0][coordinate].appendChild(line)
            }else if(axis=="horizontal"){
            line.style.width="300px"
            line.style.height="15px" 
            gameBoxes[coordinate][0].appendChild(line)
            }else if(axis=="diagonal1"){
            line.style.transformOrigin="top left"
            line.style.left="20px" 
            line.style.top="10px" 
            line.style.width="400px"
            line.style.height="15px"
            line.style.rotate="45deg"
            gameBoxes[0][0].appendChild(line)  
            }else{
            line.style.transformOrigin="top left"
            line.style.left="80px" 
            line.style.top="35px" 
            line.style.width="400px"
            line.style.height="15px" 
            line.style.rotate="-225deg" 
            gameBoxes[0][2].appendChild(line) 
            }
            turnCount=0
            clearField()
        }
        fieldListen()
    </script>
</body>
</html>