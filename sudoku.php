<?php
    function makeGrid() {
        unset($_SESSION['fullgrid']);
        $_SESSION['fullgrid'] = array();
        for($i = 0; $i < 9; $i++)
            $_SESSION['fullgrid'][$i] = array();
        recurse(0, 0);
        $_SESSION['guessgrid'] = array();
        for($i = 0; $i < 9; $i++)
            $_SESSION['guessgrid'][$i] = array();
        for($i = 0; $i < 9; $i++){
            for($j = 0; $j < 9; $j++){
                $_SESSION['guessgrid'][$i][$j] = $_SESSION['fullgrid'][$i][$j];
            }
        }
        removeSome($_SESSION['difficulty']);
    }

    function recurse($x, $y) {
        $numberexcluded = 0;
        $allowed = array(1 => true, 2 => true, 3 => true, 4 => true, 5 => true, 6 => true, 7 => true, 8 => true, 9 => true);
        for ($k = 0; $k < 9; $k++) {
            if($allowed[$_SESSION['fullgrid'][$k][$y]] != false){
                $allowed[$_SESSION['fullgrid'][$k][$y]] = false;
                $numberexcluded++;
            }
            if($allowed[$_SESSION['fullgrid'][$x][$k]] != false){
                $allowed[$_SESSION['fullgrid'][$x][$k]] = false;
                $numberexcluded++;
            }
        }
        if ($numberexcluded == 9)
            return array('boolean' => false, 'value' => 0);
        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if($allowed[$_SESSION['fullgrid'][$x + $xpos + $k][$y + $ypos + $l]] != false) {
                    $allowed[$_SESSION['fullgrid'][$x + $xpos + $k][$y + $ypos + $l]] = false;
                    $numberexcluded++;
                }
            }
        }
        if ($numberexcluded == 9)
            return array('boolean' => false, 'value' => 0);
        for ($i = 1; $i < 10; $i++) {
            if ($allowed[$i] == true)
                $whichnumbers[] = $i;
        }
        if ($x == 8 && $y == 8) {
            $_SESSION['fullgrid'][$x][$y] = $whichnumbers[rand(0, count($whichnumbers) - 1)];
            return array('boolean' => true, 'value' => $_SESSION['fullgrid'][$x][$y]);
        }
        $nexty = $y;
        if ($x == 8) {
            $nextx = 0;
            $nexty++;
        } else {
            $nextx = $x + 1;
        }
        $testnext = false;
        while (!$testnext) {
            $_SESSION['fullgrid'][$x][$y] = $whichnumbers[rand(0, count($whichnumbers) - 1)];
            $testnext = recurse($nextx, $nexty);
            if ($testnext['value'] == 0) {
                $allowed[$_SESSION['fullgrid'][$x][$y]] = false;
                $numberexcluded++;
                if ($numberexcluded == 9) {
                    unset($_SESSION['fullgrid'][$x][$y]);
                    return array('boolean' => false, 'value' => 0);
                }
                unset($whichnumbers);
                for ($i = 1; $i < 10; $i++) {
                    if ($allowed[$i] == true)
                        $whichnumbers[] = $i;
                }
            }
            $testnext = $testnext['boolean'];
        }
        return array('boolean' =>true, 'value' => $_SESSION['fullgrid'][$x][$y]);
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

    function canSolve(){
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if (solvehelp($i, $j))
                    return true;
            }
        }
        return false;
    }

    function solvehelp($x, $y) {
        $canbe = array(1 => true, 2 => true, 3 => true, 4 => true, 5 => true, 6 => true, 7 => true, 8 => true, 9 => true);
        for ($i = 0; $i < 9; $i++) {
            if (is_int($_SESSION['guessgrid'][$x][$i]))
                $canbe[$_SESSION['guessgrid'][$x][$i]] = false;
            if (is_int($_SESSION['guessgrid'][$i][$y]))
                $canbe[$_SESSION['guessgrid'][$i][$y]] = false;
        }
        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if (is_int($_SESSION['guessgrid'][$x + $xpos + $k][$y + $ypos + $l]))
                    $canbe[$_SESSION['guessgrid'][$x + $xpos + $k][$y + $ypos + $l]] = false;
            }
        }
        $count = 0;
        foreach ($canbe as $temp) {
            if ($temp == true)
                $count++;
        }
        if ($count == 1)
            return true;
        return false;
    }
    function checkGuess() {
        $count = 0;
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if ($_POST[$i . ":" . $j] == $_SESSION['fullgrid'][$i][$j])
                    $_SESSION['guessgrid'][$i][$j] = $_SESSION['fullgrid'][$i][$j];
                else if (strlen($_POST[$i . ":" . $j]) > 0) { // wrong guess
                    $exploded = explode(' type="text" ', $_SESSION['guessgrid'][$i][$j]);
                    $_SESSION['guessgrid'][$i][$j] = $exploded[0] . ' class="wrong" type="text" value="' . $_POST[$i . ":" . $j] . '" ' . $exploded[1];
                }
                if ($_SESSION['guessgrid'][$i][$j] == $_SESSION['fullgrid'][$i][$j])
                    $count++;
            }
        }
        if($count == 81)
            return true;
    }
?>