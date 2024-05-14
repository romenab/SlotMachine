<?php
$boardHeight = 4;
$boardWidth = 5;
$elements = [
    ["value" => "@", "weight" => 14],
    ["value" => "%", "weight" => 14],
    ["value" => "?", "weight" => 14],
    ["value" => "*", "weight" => 45],
    ["value" => "#", "weight" => 13]
];

function getRandom($elements)
{
    $randomElement = mt_rand(0, array_sum(array_column($elements, "weight")));
    $currentWeight = 0;
    foreach ($elements as $element) {
        $currentWeight += $element["weight"];
        if ($randomElement <= $currentWeight) {
            return $element;
        }
    }
}

function makeBoard($elements, $boardHeight, $boardWidth)
{
    $board = [];
    for ($i = 0; $i < $boardHeight; $i++) {
        $row = [];
        for ($j = 0; $j < $boardWidth; $j++) {
            $result = getRandom($elements);
            $row[] = $result["value"];
        }
        $board[] = $row;
    }
    return $board;
}

function showBoard($board)
{
    foreach ($board as $row) {
        foreach ($row as $element) {
            echo $element;
        }
        echo PHP_EOL;
    }
}

function winConditions($board, $bet, $boardWidth)
{
    $winCoinsA = 0;
    foreach ($board as $row) {
        if (count(array_unique($row)) === 1 && $row[0] === "*") {
            $winCoinsA += count($row) * 2 * $bet;
        }
    }
    $winCoinsB = 0;
    for ($y = 0; $y < $boardWidth; $y++) {
        $column = array_column($board, $y);
        if (count(array_unique($column)) === 1 && $column[0] === "*") {
            $winCoinsB += count($column) * $bet;
        }
    }
    $sum = $winCoinsA + $winCoinsB;
    return $sum;
}

$bet = 1;
echo "Welcome to slot machine!" . PHP_EOL;
while (true) {
    $coins = readline("Enter start amount of virtual coins to play with: ");
    if (is_numeric($coins) && $coins > 0) {
        while (true) {
            $wantToBet = strtolower(readline("Your BET per single spin is $bet. Do you want to bet a different amount (y/n)? "));
            if ($wantToBet == "yes" || $wantToBet == "y") {
                while (true) {
                    $userBet = readline("Enter your bet: ");
                    if (is_numeric($userBet) && $userBet > 0 && $userBet <= $coins) {
                        $bet = $userBet;
                        break;
                    } else {
                        echo "Invalid number of bets." . PHP_EOL;
                    }
                }
            } elseif ($wantToBet == "no" || $wantToBet == "n") {
                while (true) {
                    if (!game($coins, $bet, $elements, $boardHeight, $boardWidth)) {
                        break;
                    }
                }
                break;
            } else {
                echo "Invalid input. Try again!" . PHP_EOL;
            }
        }
        echo PHP_EOL;
        break;
    } else {
        echo "Invalid number. Try again!" . PHP_EOL;
    }
}

function game(&$coins, &$bet, $elements, $boardHeight, $boardWidth)
{
    while (true) {
        $board = makeBoard($elements, $boardHeight, $boardWidth);
        showBoard($board);
        $winCoins = winConditions($board, $bet, $boardWidth);
        $coins += $winCoins;
        $coins -= $bet;
        echo "You won: $winCoins coins" . PHP_EOL . "Your coins: $coins" . PHP_EOL . "Your bet: $bet" . PHP_EOL;
        while (true) {
            if ($coins < $bet && $coins > 0) {
                echo "You need to lower your bet amount because your bet amount is greater than your coins." . PHP_EOL;
                while (true) {
                    $newBet = readline("Enter your bet: ");
                    if (is_numeric($newBet) && $newBet > 0 && $newBet <= $coins) {
                        $bet = $newBet;
                        break;
                    } else {
                        echo "Invalid number of bets." . PHP_EOL;
                    }
                }
            } elseif ($coins == 0) {
                echo "Sorry, you are out of coins." . PHP_EOL;
                echo "Thank you for playing!";
                exit;
            }
            $nextStep = readline("Do you want to continue (1), end game (2) or change bet (3)? ");
            switch ($nextStep) {
                case "1":
                    return true;
                case "2":
                    echo "Your coins: $coins" . PHP_EOL . "Thank you for playing!";
                    exit;
                case "3":
                    while (true) {
                        $userBet = readline("Enter your bet: ");
                        if (is_numeric($userBet) && $userBet > 0 && $userBet <= $coins) {
                            $bet = $userBet;
                            break;
                        } else {
                            echo "Invalid number of bets." . PHP_EOL;
                        }
                    }
                    return true;
                default:
                    echo "Invalid input. Try again!" . PHP_EOL;
                    break;
            }

        }

    }
}