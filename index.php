<?php
	session_start();
	require("sudoku.php");
?>
<html lang="en-US" xml:lang="en-US" xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>Briton Westerhaus - Sudoku</title>
		<meta name="description" content="An online version of sudoku." />
		<meta name="keywords" content="media, entertainment, fun, games" />
		<meta name="author" content="Briton Westerhaus" />
		<link rel="stylesheet" type="text/css" href="default.css" />
		<script type="text/javascript" src="default.js"></script>
	</head>
	<body<?php if (isSet($_SESSION['guessgrid']) && $_POST['submitButton'] != "Get a new Sudoku!") echo ' onload="init();"'; ?>>
		<div class="content">
			<h1>Sudoku</h1>
			<form action="index.php" method="post" autocomplete="off" id="TheForm">
				<?php
					if ($_POST['submitButton'] == "Easy") {
						$_SESSION['difficulty'] = 12;
						getGrids();
						addSome($_SESSION['difficulty']);
					} else if ($_POST['submitButton'] == "Medium") {
						$_SESSION['difficulty'] = 6;
						getGrids();
						addSome($_SESSION['difficulty']);
					} else if ($_POST['submitButton'] == "Hard") {
						$_SESSION['difficulty'] = 0;
						getGrids();
						addSome($_SESSION['difficulty']);
					} else if ($_POST['submitButton'] == "Check" || $_POST['completed'] == "true") {
						$done = checkGuess();
					} else if (!isSet($_POST['submitButton']) || $_POST['submitButton'] == "Get a new Sudoku!") {
						unset($_SESSION['difficulty']);
						$_SESSION['checks'] = 0;
						$_SESSION['incorrects'] = 0;
						echo '<h2><center>Choose your difficulty.</center></h2>';
						echo '<center><input type="submit" name="submitButton" value="Easy" />';
						echo '<input type="submit" name="submitButton" value="Medium" />';
						echo '<input type="submit" name="submitButton" value="Hard" /></center>';
					}

					if (isSet($_SESSION['difficulty'])) {
						echo '<input type="hidden" name="completed" id="Completed" value="false" />';
						echo '<table class="gameGrid"><tbody>';
						for ($i = 0; $i < 3; $i++) {
							echo "<tr>";
							for ($j = 0; $j < 3; $j++) {
								echo '<td><table><tbody>';
								for ($k = 3 * $i; $k < 3 * $i + 3; $k++) {
									echo "<tr>";
									for ($l = 3 * $j; $l < 3 * $j + 3; $l++) {
										if ($_SESSION['guessgrid'][$k][$l] == $_POST[$k . ':' . $l])
											echo '<td><span class="correct">' . $_SESSION['guessgrid'][$k][$l] . '</span>';
										else
											echo '<td><span>' . $_SESSION['guessgrid'][$k][$l] . '</span>';
										echo '<table class="pencilContainer"><tbody>';
										for ($m = 0; $m < 3; $m++) {
											echo '<tr>';
												for ($n = 0; $n < 3; $n++) {
													echo '<td class="pencilMark" id="' . $k . ':' . $l . ':' . ($m * 3 + $n + 1) . '">&nbsp;</td>';
												}
											echo '</tr>';
										}
										echo '</tbody></table></td>';	
									}
									echo "</tr>";
								}
								echo '</tbody></table></td>';
							}
							echo "</tr>";
						}
						echo "</tbody></table>";
						echo '<section>';
						if (!$done)
							echo '<input type="submit" name="submitButton" value="Check" />';
						echo '<input type="submit" name="submitButton" value="Get a new Sudoku!" />';
						echo '</section>';
						echo '<button title="Pencil mode lets you take notes of possible numbers." onclick="togglePencil();return false;">Pencil &#x270F;</button>';
					}
				?>
			</form>
			<?php
				if ($done) {
			?>
			<div id="ModalContainer">
				<form action="index.php" method="post" class="modal">
					<h3>You win!</h3>
					<p>Checks: <?php echo $_SESSION['checks']; ?></p>
					<p>Wrong Numbers: <?php echo $_SESSION['incorrects']; ?></p>
					<input type="submit" name="submitButton" value="Get a new Sudoku!" />
				</form>
				<button class="closeButton" onclick="closeModal();" name="submitButton">&#10006;</button>
			</div>
			<?php
				}
			?>
		</div>
	</body>
</html>