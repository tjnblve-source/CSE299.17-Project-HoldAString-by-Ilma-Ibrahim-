<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tic Tac Toe - Bot Edition</title>
    <style>
        body {
            background-color: #121213;
            color: #f5ebe0;
            font-family: "Arial", sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 { color: #d4a373; }
        .ui { display: flex; flex-direction: column; align-items: center; }
        .row { display: flex; }

        .cell {
            width: 80px;
            height: 80px;
            border: 2px solid #3a3a3c;
            font-size: 40px;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: #2a2a2c;
            margin: 2px;
            transition: 0.2s;
        }

        #print {
            font-family: 'Verdana', sans-serif;
            color: #d4a373;
            font-size: 20px;
            margin-top: 20px;
            height: 30px;
        }

        .reset-btn {
            width: 150px;
            height: 40px;
            border: none;
            border-radius: 4px;
            background-color: #d4a373;
            color: #121213;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="ui">
        <h1>TIC TAC TOE</h1>
        <p>You are <b>Player X</b></p>
        
        <div class="row">
            <div class="cell" id="b1" onclick="playerMove('b1')"></div>
            <div class="cell" id="b2" onclick="playerMove('b2')"></div>
            <div class="cell" id="b3" onclick="playerMove('b3')"></div>
        </div>
        <div class="row">
            <div class="cell" id="b4" onclick="playerMove('b4')"></div>
            <div class="cell" id="b5" onclick="playerMove('b5')"></div>
            <div class="cell" id="b6" onclick="playerMove('b6')"></div>
        </div>
        <div class="row">
            <div class="cell" id="b7" onclick="playerMove('b7')"></div>
            <div class="cell" id="b8" onclick="playerMove('b8')"></div>
            <div class="cell" id="b9" onclick="playerMove('b9')"></div>
        </div>

        <button class="reset-btn" onclick="resetGame()">RESET</button>
        <p id="print"></p>
    </div>

    <script>
        let gameActive = true;

        function playerMove(id) {
            let cell = document.getElementById(id);
            
            if (cell.innerHTML === "" && gameActive) {
                cell.innerHTML = "X";
                cell.style.color = "#6aaa64";
                cell.style.pointerEvents = "none";
                
                if (!checkWinner()) {
                    document.getElementById('print').innerHTML = "Thinking...";
                    setTimeout(botMove, 600);
                }
            }
        }

        function botMove() {
            if (!gameActive) return;

            let cells = [];
            for (let i = 1; i <= 9; i++) {
                cells.push(document.getElementById("b" + i).innerHTML);
            }

            const winPatterns = [
                [0, 1, 2], [3, 4, 5], [6, 7, 8],
                [0, 3, 6], [1, 4, 7], [2, 5, 8],
                [0, 4, 8], [2, 4, 6]
            ];

            let move = -1;

            move = findBestSpot(winPatterns, cells, "O");

            if (move === -1) {
                move = findBestSpot(winPatterns, cells, "X");
            }

            if (move === -1) {
                let empty = cells.map((v, i) => v === "" ? i : null).filter(v => v !== null);
                if (empty.length > 0) {
                    move = empty[Math.floor(Math.random() * empty.length)];
                }
            }

            if (move !== -1) {
                let botCell = document.getElementById("b" + (move + 1));
                botCell.innerHTML = "O";
                botCell.style.color = "#c9b458";
                botCell.style.pointerEvents = "none";
                checkWinner();
            }
        }

        function findBestSpot(patterns, cells, player) {
            for (let pattern of patterns) {
                let vals = pattern.map(idx => cells[idx]);
                let count = vals.filter(v => v === player).length;
                let emptyIndex = vals.indexOf("");

                if (count === 2 && emptyIndex !== -1) {
                    return pattern[emptyIndex];
                }
            }
            return -1;
        }

        function checkWinner() {
            let cells = [];
            for (let i = 1; i <= 9; i++) {
                cells.push(document.getElementById("b" + i).innerHTML);
            }

            const winPatterns = [
                [0, 1, 2], [3, 4, 5], [6, 7, 8],
                [0, 3, 6], [1, 4, 7], [2, 5, 8],
                [0, 4, 8], [2, 4, 6]
            ];

            for (let pattern of winPatterns) {
                const [a, b, c] = pattern;
                if (cells[a] && cells[a] === cells[b] && cells[a] === cells[c]) {
                    endGame(cells[a] === "X" ? "You Won! 🏆" : "You lost! 😭");
                    return true;
                }
            }

            if (!cells.includes("")) {
                endGame("It's a Tie! 🤝");
                return true;
            }

            document.getElementById('print').innerHTML = "Your Turn";
            return false;
        }

        function endGame(msg) {
            gameActive = false;
            document.getElementById('print').innerHTML = msg;
            
            if(window.Android && Android.showNotification) {
                Android.showNotification("Tic Tac Toe", msg);
            }

            for(let i=1; i<=9; i++) { 
                document.getElementById("b"+i).style.pointerEvents = "none"; 
            }
        }

        function resetGame() {
            location.reload();
        }
    </script>
</body>
</html>