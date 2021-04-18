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

    function makeGrid() {
        unset($_SESSION['fullgrid']);
        $_SESSION['fullgrid'] = array();
        for ($i = 0; $i < 9; $i++)
            $_SESSION['fullgrid'][$i] = array();
        recurse(0, 0);
        $_SESSION['guessgrid'] = array();
        for ($i = 0; $i < 9; $i++)
            $_SESSION['guessgrid'][$i] = array();
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                $_SESSION['guessgrid'][$i][$j] = $_SESSION['fullgrid'][$i][$j];
            }
        }
        removeSome();
    }

    function recurse($x, $y) {
        $numberExcluded = 0;
        $allowed = array(1 => true, 2 => true, 3 => true, 4 => true, 5 => true, 6 => true, 7 => true, 8 => true, 9 => true);
        for ($k = 0; $k < 9; $k++) {
            if($allowed[$_SESSION['fullgrid'][$k][$y]] != false){
                $allowed[$_SESSION['fullgrid'][$k][$y]] = false;
                $numberExcluded++;
            }
            if($allowed[$_SESSION['fullgrid'][$x][$k]] != false){
                $allowed[$_SESSION['fullgrid'][$x][$k]] = false;
                $numberExcluded++;
            }
        }
        if ($numberExcluded == 9)
            return array('boolean' => false, 'value' => 0);
        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if ($allowed[$_SESSION['fullgrid'][$x + $xpos + $k][$y + $ypos + $l]] != false) {
                    $allowed[$_SESSION['fullgrid'][$x + $xpos + $k][$y + $ypos + $l]] = false;
                    $numberExcluded++;
                }
            }
        }
        if ($numberExcluded == 9)
            return array('boolean' => false, 'value' => 0);
        for ($i = 1; $i < 10; $i++) {
            if ($allowed[$i] == true)
                $whichNumbers[] = $i;
        }
        if ($x == 8 && $y == 8) {
            $_SESSION['fullgrid'][$x][$y] = $whichNumbers[rand(0, count($whichNumbers) - 1)];
            return array('boolean' => true, 'value' => $_SESSION['fullgrid'][$x][$y]);
        }
        $nexty = $y;
        if ($x == 8) {
            $nextx = 0;
            $nexty++;
        } else {
            $nextx = $x + 1;
        }
        $testNext = false;
        while (!$testNext) {
            $_SESSION['fullgrid'][$x][$y] = $whichNumbers[rand(0, count($whichNumbers) - 1)];
            $testNext = recurse($nextx, $nexty);
            if ($testNext['value'] == 0) {
                $allowed[$_SESSION['fullgrid'][$x][$y]] = false;
                $numberExcluded++;
                if ($numberExcluded == 9) {
                    unset($_SESSION['fullgrid'][$x][$y]);
                    return array('boolean' => false, 'value' => 0);
                }
                unset($whichNumbers);
                for ($i = 1; $i < 10; $i++) {
                    if ($allowed[$i] == true)
                        $whichNumbers[] = $i;
                }
            }
            $testNext = $testNext['boolean'];
        }
        return array('boolean' => true, 'value' => $_SESSION['fullgrid'][$x][$y]);
    }

    function removeSome() {
        $solvable = true;
        $checkedSolvable = [];
        for ($x = 0; $x < 9; $x++) {
            $checkedSolvable[$x] = [];
            for ($y = 0; $y < 9; $y++) {
                $checkedSolvable[$x][$y] = false;
            }
        }

        while ($solvable) {
            $x = rand(0, 8);
            $y = rand(0, 8);
            if ($_SESSION['guessgrid'][$x][$y] == $_SESSION['fullgrid'][$x][$y]) {
                $_SESSION['guessgrid'][$x][$y] = '<input type="text" maxlength="1" id="' . $x . ':' . $y . '" name="' . $x . ':' . $y . '" onselect="selectInput(' . $x . ', ' . $y . ')" onclick="selectInput(' . $x . ', ' . $y . ')" oninput="inputChanged(this)" />';
                $checkedSolvable[$x][$y] = true;
                if (!canSolve($_SESSION['guessgrid'], $x, $y)) {
                    $_SESSION['guessgrid'][$x][$y] = $_SESSION['fullgrid'][$x][$y];
                }
                $solvable = false;
                for ($x = 0; $x < 9; $x++) {
                    for ($y = 0; $y < 9; $y++) {
                        if (!$checkedSolvable[$x][$y]) {
                            $solvable = true;
                            break;
                        }
                    }
                    if ($solvable)
                        break;
                }
            }
        }

        $_SESSION['guessgrid'][$x][$y] = $_SESSION['fullgrid'][$x][$y];
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

    function canSolve($guessGrid, $x = -1, $y = -1) {
        if ($x > -1 && $y > -1) { // It was solvable before we removed the last one, so a shortcut is to see if you can solve for the one just removed. 
            if (solveHelp($x, $y, $guessGrid)) 
                return true;
        } 
        $count = getCount($guessGrid);
        if ($count == 81)
            return true;
        // This actually needs to run recursively on the resulted grid to make sure it can be solved the whole way through. 
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                // TODO: Clone remove stack
                if ($guessGrid[$i][$j] != $_SESSION['fullgrid'][$i][$j] && solveHelp($i, $j, $guessGrid)) {
                    // Something bad with chaining back the false from canSolve below?
                    $guessGrid[$i][$j] = $_SESSION['fullgrid'][$i][$j];
                    return canSolve($guessGrid, []);
                }
            }
        }
        return false;
    }

    function solveHelp($x, $y, $guessGrid) {
        $canBe = array(1 => true, 2 => true, 3 => true, 4 => true, 5 => true, 6 => true, 7 => true, 8 => true, 9 => true);
        for ($i = 0; $i < 9; $i++) {
            if ($i != $y) {
                if (is_int($guessGrid[$x][$i]))
                    $canBe[$guessGrid[$x][$i]] = false;
            }
            if ($i != $x) {
                if (is_int($guessGrid[$i][$y]))
                    $canBe[$guessGrid[$i][$y]] = false;
            }
        }

        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if (!($xpos + $k == 0 && $ypos + $l == 0)) {
                    if (is_int($guessGrid[$x + $xpos + $k][$y + $ypos + $l]))
                        $canBe[$guessGrid[$x + $xpos + $k][$y + $ypos + $l]] = false;
                }        
            }
        }
        $count = 0;
        foreach ($canBe as $temp) {
            if ($temp == true)
                $count++;
        }
        if ($count == 1) {
            return true;
        }
        // Here we need to check for other solving methods, i.e. no other squares in the row/column/grid can be some number. 
        for ($i = 1; $i <= 9; $i++) {
            if ($canBe[$i]) {
                $anotherCanBeNumber = false;
                for ($j = 0; $j < 9; $j++) {
                    if ($j != $y) {
                        if (canBe($x, $j, $i, $guessGrid)) {
                            $anotherCanBeNumber = true;
                            break;
                        }
                    }
                }

                if (!$anotherCanBeNumber)
                    return true;
                
                $anotherCanBeNumber = false;
                for ($j = 0; $j < 9; $j++) {
                    if ($j != $x) {
                        if (canBe($j, $y, $i, $guessGrid)) {
                            $anotherCanBeNumber = true;
                            break;
                        }
                    }
                }
                
                if (!$anotherCanBeNumber)
                    return true;

                $anotherCanBeNumber = false;
                for ($k = -2; $k <= 0; $k++) {
                    for ($l = -2; $l <= 0; $l++) {
                        
                        if (canBe($x + $xpos + $k, $y + $ypos + $l, $i, $guessGrid)) {
                            $anotherCanBeNumber = true;
                            break;
                        }
                    }
                }
                
                if (!$anotherCanBeNumber) 
                    return true;
                
            }
        }
        return false;
    }

    function canBe($x, $y, $num, $guessGrid) {
        if ($guessGrid[$x][$y] == $_SESSION['fullgrid'][$x][$y]) // We don't check already solved squares.
            return false;
        
        for ($i = 0; $i < 9; $i++) {
            if ($guessGrid[$x][$i] == $num)
                return false;
            if ($guessGrid[$i][$y] == $num)
                return false;
        }
        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if ($guessGrid[$x + $xpos + $k][$y + $ypos + $l] == $num)
                    return false;
            }
        }
        return true;
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