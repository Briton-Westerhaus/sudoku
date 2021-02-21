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
						$_SESSION['difficulty'] = 20;
						makeGrid();
					} else if ($_POST['submitButton'] == "Medium") {
						$_SESSION['difficulty'] = 12;
						makeGrid();
					} else if ($_POST['submitButton'] == "Hard") {
						$_SESSION['difficulty'] = 5;
						makeGrid();
					} else if ($_POST['submitButton'] == "Check" || $_POST['completed'] == "true") {
						$done = checkGuess();
					} else if ($_POST['submitButton'] == "Get a new Sudoku!" || !isSet($_POST['submitButton'])) {
						unset($_SESSION['difficulty']);
						echo '<h2><center>Choose your difficulty.</center></h2>';
						echo '<center><input type="submit" name="submitButton" value="Easy" />';
						echo '<input type="submit" name="submitButton" value="Medium" />';
						echo '<input type="submit" name="submitButton" value="Hard" /></center>';
					}

					if (isSet($_SESSION['difficulty'])) {
						echo '<input type="hidden" name="completed" id="Completed" value="false" />';
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
						echo '<center><input type="submit" name="submitButton" value="Check" />';
						echo '<input type="submit" name="submitButton" value="Get a new Sudoku!" /></center>';
					}
				?>
			</form>
			<?php
				if ($done) {
			?>
			<div class="modal-container">
				<form action="index.php" method="post" class="modal">
					<h3>You win!</h3>
					<input type="submit" name="submitButton" value="Get a new Sudoku!" />
				</form>
			</div>
			<?php
				}
			?>
		</div>
	</body>
</html>