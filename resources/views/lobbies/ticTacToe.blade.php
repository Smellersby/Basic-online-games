<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$lobby->name}}</title>
    <style>
        a{
            color: black;
        }
        body{
            display: flex;
            align-items: center;
            flex-flow: column;
        }
        .box{
        display: flex;
        justify-content: center;
        align-items: center;
        width:100px;
        height: 100px;
        background-color: #ffffff;
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
        width: 1000px;
        padding: 10px;
        background-color: rgb(244, 246, 247);
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    </style>
</head>
<body>
    <div class="mainContainer">
        @if($lobby->playerOne==Auth::id()||$lobby->playerTwo==Auth::id())
        <button onclick="exit()">Leave game</button>
        <br>
        @endif
        <h1>Tic-Tac-Toe</h1>
        <div class="gridContainer">
            <div class="box" id="box1"></div>
            <div class="box" id="box2"></div>
            <div class="box" id="box3"></div>
            <div class="box" id="box4"></div>
            <div class="box" id="box5"></div>
            <div class="box" id="box6"></div>
            <div class="box" id="box7"></div>
            <div class="box" id="box8"></div>
            <div class="box" id="box9"></div>
        </div>
        <h2 id="turnIndicator">
            @if($lobby->turn==1)
                {{$users[$lobby->playerOne-1]->name}}
            @else
                {{$users[$lobby->playerTwo-1]->name}}
            @endif
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
        let lockPlayer=0


        function getGameInfo(){
            $.ajax({
                    url: '{{ route('lobbies.getGameInfo') }}',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                    _token: '{{ csrf_token() }}', // Include CSRF token
                    lobby_id: lobbyId={{$lobby->id}}
                    },
                    success: function(response) {
                        console.log(response)
                    },
                    error: function(xhr, status, error) {
                        console.log("shit");
                    }
                });
        }
        $(document).ready(function() {
            getGameInfo();
            setInterval(getGameInfo, 5000);
        });

        function exit(){
            $.ajax({
                url: '{{ route('lobbies.playerLeave') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Include CSRF token
                    lobby_id: lobbyId={{$lobby->id}}
                },
                success: function(response) {
                    if (response.success) {
                        console.log('Player has left the lobby successfully');
                        window.location.href = '/lobbies'; // Redirect to /lobbies
                    } else {
                        console.log('Error:', response.message);
                    }
                }
            });
        }
        window.addEventListener('beforeunload', function (e) {
            exit();
        });
        window.addEventListener('popstate', function (event) {
            console.log("detected");
        });

        function changeCondition(){
            if(lockPlayer==0){
                if(turn==1&&!this.innerHTML){
                this.innerHTML="X"
                this.style.color="#ff0000"
                turnIndicator.innerHTML="O turns"
                turn=0
                turnCount++
                }else if(!this.innerHTML){
                this.innerHTML="O"
                this.style.color="#0000ff"
                turn=1
                turnIndicator.innerHTML="X turns"
                turnCount++
                }else{
                    alert("This box is already filled")
                }
                fieldUpdate()
                victoryCheck()
                if(turnCount==9){
                    clearField(true)
                    resultScreen.style.color="#000000"
                    resultScreen.innerHTML="Tie !"
                    turnCount=0
                }
            }

        }
        //console.log(gameBoxes[0][1])
        function fieldUpdate(){
            for(let p=0;p<3;p++){
                for(let i=0;i<3;i++){
                    gameBoxes[p][i]=(document.querySelectorAll(".box")[i+p*3])
                    gameBoxes[p][i].addEventListener("click", changeCondition);
                }
            }
        }
        function victoryCheck(){
            //checking all of them simultaniously
            if(gameBoxes[0][0].innerHTML==gameBoxes[1][1].innerHTML&&gameBoxes[0][0].innerHTML==gameBoxes[2][2].innerHTML&&gameBoxes[1][1].innerHTML!=0){
                victoryAlert("diagonal1",0)
            }else if(gameBoxes[1][1].innerHTML==gameBoxes[2][0].innerHTML&&gameBoxes[1][1].innerHTML==gameBoxes[0][2].innerHTML&&gameBoxes[1][1].innerHTML!=0){
                victoryAlert("diagonal2",2)
            }else{
                //or checking one by one
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
                        gameBoxes[p][i].style.transitionDuration="0.5s"
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
            }, 1500); 
            setTimeout(() => { 
                for(let p=0;p<3;p++){
                    for(let i=0;i<3;i++){
                        gameBoxes[p][i].innerHTML=null
                        gameBoxes[p][i].style.transitionDuration="0s"
                    }
                }
                lockPlayer=0
            }, 2500);
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
            line.style.transformOrigin="top left"//super useful hujna
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
        fieldUpdate()
    </script>
</body>
</html>