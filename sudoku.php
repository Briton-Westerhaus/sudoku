<?php

    function getCount($guessGrid) {
        $count = 0;
        for ($j = 0; $j < 9; $j++) {
            for ($k = 0; $k < 9; $k++) {

                if ($guessGrid[$j][$k] == $_SESSION['fullgrid'][$j][$k])
                    $count++;
            }
        }
        return $count;
    }

    function getGrids() {
        $_DIR = "./sudokus/";
        $sudokus = scandir($_DIR, 1);
        if (count($sudokus) <= 2) {
            exec("node sudoku.js");
            $sudokus = scandir($_DIR, 1);
        }
        $_FILENAME = $sudokus[0];
        $sudoku = fopen($_DIR . $_FILENAME, "r");
        $sudoku_grids = json_decode(fread($sudoku, filesize($_DIR . $_FILENAME)), true);
        $_SESSION['fullgrid'] = $sudoku_grids['fullGrid'];
        $_SESSION['guessgrid'] = $sudoku_grids['guessGrid'];
        fclose($sudoku);
        unlink($_DIR . $_FILENAME);

        for ($x = 0; $x < 9; $x++) {
            for ($y = 0; $y < 9; $y++) {
                if ($_SESSION['guessgrid'][$x][$y] == null) {
                    $_SESSION['guessgrid'][$x][$y] = '<input type="text" maxlength="1" id="' . $x . ':' . $y . '" name="' . $x . ':' . $y . '" onselect="selectInput(' . $x . ', ' . $y . ')" onclick="selectInput(' . $x . ', ' . $y . ')" oninput="inputChanged(this)" />';
                }
            }
        }
    }

    function addSome($difficulty) {
        for ($i = 0; $i < $difficulty; $i++) {
            $x = rand(0, 8);
            $y = rand(0, 8);
            while ($_SESSION['guessgrid'][$x][$y] == $_SESSION['fullgrid'][$x][$y]) { //Don't want to put a number back in, if it's already in!
                $x = rand(0, 8);
                $y = rand(0, 8);
            }
            $_SESSION['guessgrid'][$x][$y] = $_SESSION['fullgrid'][$x][$y];
        }
    }

    function checkGuess() {
        if (!$_POST['completed'])
            $_SESSION['checks']++;
        $count = 0;
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($_POST[$i . ":" . $j] == $_SESSION['fullgrid'][$i][$j])
                    $_SESSION['guessgrid'][$i][$j] = $_SESSION['fullgrid'][$i][$j];
                else if (strlen($_POST[$i . ":" . $j]) > 0) { // wrong guess
                    $exploded = explode(' type="text" ', $_SESSION['guessgrid'][$i][$j]);
                    $_SESSION['guessgrid'][$i][$j] = $exploded[0] . ' class="wrong" type="text" value="' . $_POST[$i . ":" . $j] . '" ' . $exploded[1];
                    $_SESSION['incorrects']++;
                } else if ($_SESSION['guessgrid'][$i][$j] != $_SESSION['fullgrid'][$i][$j]) { // cleanup?
                    $_SESSION['guessgrid'][$i][$j] = '<input type="text" maxlength="1" id="' . $i . ':' . $j . '" name="' . $i . ':' . $j . '" onselect="selectInput(' . $i . ', ' . $j . ')" onclick="selectInput(' . $i . ', ' . $j . ')" oninput="inputChanged(this)" />';
                }
                if ($_SESSION['guessgrid'][$i][$j] == $_SESSION['fullgrid'][$i][$j])
                    $count++;
            }
        }
        if ($count == 81) {
            return true;
        }
    }
?>