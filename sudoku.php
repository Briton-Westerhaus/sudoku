<?php
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
        removeSome($_SESSION['difficulty']);
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

    function removeSome($difficulty) {
        $solvable = true;
        while ($solvable) {
            $x = rand(0, 8);
            $y = rand(0, 8);
            $_SESSION['guessgrid'][$x][$y] = '<input type="text" maxlength="1" id="' . $x . ':' . $y . '" name="' . $x . ':' . $y . '" onselect="selectInput(' . $x . ', ' . $y . ')" onclick="selectInput(' . $x . ', ' . $y . ')" oninput="inputChanged(this)" />';
            $solvable = canSolve();
        }
        $_SESSION['guessgrid'][$x][$y] = $_SESSION['fullgrid'][$x][$y];
        for ($i = 0; $i < $difficulty; $i++) {
            $x = rand(0, 8);
            $y = rand(0, 8);
            $_SESSION['guessgrid'][$x][$y] = $_SESSION['fullgrid'][$x][$y];
        }
    }

    function canSolve() {
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if (solveHelp($i, $j))
                    return true;
            }
        }
        return false;
    }

    function solveHelp($x, $y) {
        $canBe = array(1 => true, 2 => true, 3 => true, 4 => true, 5 => true, 6 => true, 7 => true, 8 => true, 9 => true);
        for ($i = 0; $i < 9; $i++) {
            if (is_int($_SESSION['guessgrid'][$x][$i]))
                $canBe[$_SESSION['guessgrid'][$x][$i]] = false;
            if (is_int($_SESSION['guessgrid'][$i][$y]))
                $canBe[$_SESSION['guessgrid'][$i][$y]] = false;
        }
        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if (is_int($_SESSION['guessgrid'][$x + $xpos + $k][$y + $ypos + $l]))
                    $canBe[$_SESSION['guessgrid'][$x + $xpos + $k][$y + $ypos + $l]] = false;
            }
        }
        $count = 0;
        foreach ($canBe as $temp) {
            if ($temp == true)
                $count++;
        }
        if ($count == 1)
            return true;
        // Here we need to check for other solving methods, i.e. no other squares in the row/column/grid can be some number. 
        for ($i = 1; $1 <= 9; $i++) {
            if ($canBe[$i]) {
                $excluded = false;
                $anotherCanBeNumber = false;
                for ($j = 0; $j < 9; $j++) {
                    if (canBe($x, $j, $i))
                        return false;
                    if (canBe($j, $y, $i))
                        return false;
                }
            }
        }
        return false;
    }

    function canBe($x, $y, $num) {
        for ($i = 0; $i < 9; $i++) {
            if ($_SESSION['guessgrid'][$x][$i]) == $num)
                return false;
            if ($_SESSION['guessgrid'][$i][$y]) == $num)
                return false;
        }
        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if ($_SESSION['guessgrid'][$x + $xpos + $k][$y + $ypos + $l]) == $num)
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