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
        $count = 0;
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {

                if ($_SESSION['guessgrid'][$i][$j] == $_SESSION['fullgrid'][$i][$j])
                    $count++;
            }
        }
        echo $count;
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
        echo '<table><tbody>';
        for ($i = 0; $i < 3; $i++) {
            echo "<tr>";
            for ($j = 0; $j < 3; $j++) {
                echo '<td><table><tbody>';
                for ($k = 3 * $i; $k < 3 * $i + 3; $k++) {
                    echo "<tr>";
                    for ($l = 3 * $j; $l < 3 * $j + 3; $l++) {
                        if ($_SESSION['guessgrid'][$k][$l] == $_POST[$k . ':' . $l])
                        echo '<td><span class="correct">' . $_SESSION['guessgrid'][$k][$l] . '</span></td>';
                        else
                            echo '<td><span>' . $_SESSION['fullgrid'][$k][$l] . '</span></td>';
                    }
                    echo "</tr>";
                }
                echo '</tbody></table></td>';
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
        $solvable = true;
        while ($solvable) {
            $x = rand(0, 8);
            $y = rand(0, 8);
            if ($_SESSION['guessgrid'][$x][$y] == $_SESSION['fullgrid'][$x][$y]) {
               $_SESSION['guessgrid'][$x][$y] = '<input type="text" maxlength="1" id="' . $x . ':' . $y . '" name="' . $x . ':' . $y . '" onselect="selectInput(' . $x . ', ' . $y . ')" onclick="selectInput(' . $x . ', ' . $y . ')" oninput="inputChanged(this)" />';
                $solvable = canSolve();
                echo "Removed " . $x . ", " . $y . "<br />";
            }
        }
        echo '<table><tbody>';
        for ($i = 0; $i < 3; $i++) {
            echo "<tr>";
            for ($j = 0; $j < 3; $j++) {
                echo '<td><table><tbody>';
                for ($k = 3 * $i; $k < 3 * $i + 3; $k++) {
                    echo "<tr>";
                    for ($l = 3 * $j; $l < 3 * $j + 3; $l++) {
                        if ($_SESSION['guessgrid'][$k][$l] == $_POST[$k . ':' . $l])
                        echo '<td><span class="correct">' . $_SESSION['guessgrid'][$k][$l] . '</span></td>';
                        else
                            echo '<td><span>' . $_SESSION['guessgrid'][$k][$l] . '</span></td>';
                    }
                    echo "</tr>";
                }
                echo '</tbody></table></td>';
            }
            echo "</tr>";
        }
        echo "</tbody></table>";
        $_SESSION['guessgrid'][$x][$y] = $_SESSION['fullgrid'][$x][$y];
        for ($i = 0; $i < $difficulty; $i++) {
            $x = rand(0, 8);
            $y = rand(0, 8);
            while ($_SESSION['guessgrid'][$x][$y] == $_SESSION['fullgrid'][$x][$y]) { //Don't want to put a number back in, if it's already in!
                $x = rand(0, 8);
                $y = rand(0, 8);
            }
            echo "Added " . $x . ", " . $y . "<br />";
            $_SESSION['guessgrid'][$x][$y] = $_SESSION['fullgrid'][$x][$y];
        }
    }

    function canSolve() {
        for ($i = 0; $i < 9; $i++) {
            for ($j = 0; $j < 9; $j++) {
                if (solveHelp($i, $j)) {
                    echo "Can solve at " . $i . ", " . $j . "<br />";
                    return true;
                }
             }
        }
        return false;
    }

    function solveHelp($x, $y) {
        if ($_SESSION['guessgrid'][$x][$y] == $_SESSION['fullgrid'][$x][$y]) //Can't solve with an already given number!
            return false;
        $canBe = array(1 => true, 2 => true, 3 => true, 4 => true, 5 => true, 6 => true, 7 => true, 8 => true, 9 => true);
        for ($i = 0; $i < 9; $i++) {
            if ($i != $y) {
                if (is_int($_SESSION['guessgrid'][$x][$i]))
                    $canBe[$_SESSION['guessgrid'][$x][$i]] = false;
            }
            if ($j != $x) {
                if (is_int($_SESSION['guessgrid'][$i][$y]))
                    $canBe[$_SESSION['guessgrid'][$i][$y]] = false;
            }
        }
        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if (!($xpos + $k == 0 && $ypos + $l == 0)) {
                    if (is_int($_SESSION['guessgrid'][$x + $xpos + $k][$y + $ypos + $l]))
                        $canBe[$_SESSION['guessgrid'][$x + $xpos + $k][$y + $ypos + $l]] = false;
                }        
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
        /*for ($i = 1; $i <= 9; $i++) {
            if ($canBe[$i]) {
                $anotherCanBeNumber = false;
                for ($j = 0; $j < 9; $j++) {
                    if ($j != $y) {
                        if (canBe($x, $j, $i)) {
                            $anotherCanBeNumber = true;
                            break;
                        }
                    }
                    
                        if (canBe($j, $y, $i)) {
                            $anotherCanBeNumber = true;
                            break;
                        }
                    }
                }

                for ($k = -2; $k <= 0; $k++) {
                    for ($l = -2; $l <= 0; $l++) {
                        
                            if (canBe($x + $xpos + $k, $y + $ypos + $l, $i)) {
                                $anotherCanBeNumber = true;
                                break;
                            }
                        }
                    }
                    if ($anotherCanBeNumber)
                        break;
                }
                if (!$anotherCanBeNumber) {
                    return true;
                    
                }
                
            }
        }*/
        return false;
    }

    function canBe($x, $y, $num) {
        for ($i = 0; $i < 9; $i++) {
            if ($_SESSION['guessgrid'][$x][$i] == $num)
                return false;
            if ($_SESSION['guessgrid'][$i][$y] == $num)
                return false;
        }
        $xpos = 2 - ($x % 3);
        $ypos = 2 - ($y % 3);
        for ($k = -2; $k <= 0; $k++) {
            for ($l = -2; $l <= 0; $l++) {
                if ($_SESSION['guessgrid'][$x + $xpos + $k][$y + $ypos + $l] == $num)
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