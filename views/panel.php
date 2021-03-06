<?php 

    $username = DB_USER; 
    $password = DB_PASS; 
    $host = DB_HOST; 
    $dbname = DB_NAME; 

    $options = array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'); 
     
    try 
    { 
        $db = new PDO("mysql:host={$host};dbname={$dbname};charset=utf8", $username, $password, $options); 
    } 
    catch(PDOException $ex) 
    { 
        die("Failed to connect to the database."); 
        echo "fail";
    } 
     
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
     
    $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
     
    if(function_exists('get_magic_quotes_gpc') && get_magic_quotes_gpc()) 
    { 
        function undo_magic_quotes_gpc(&$array) 
        { 
            foreach($array as &$value) 
            { 
                if(is_array($value)) 
                { 
                    undo_magic_quotes_gpc($value); 
                } 
                else 
                { 
                    $value = stripslashes($value); 
                } 
            } 
        } 
     
        undo_magic_quotes_gpc($_POST); 
        undo_magic_quotes_gpc($_GET); 
        undo_magic_quotes_gpc($_COOKIE); 
    } 
     
	$solutionMessage = "";

	if(isset($_POST['solution'])) {
		$solution = $_POST['solution'];
		if($solution == $_SESSION['current_challenge_solution']) {
			$solutionMessage = "<div class='successBox'>Correct</div>";
			
			//update the challenge ID and database
			
			$query = " 
				UPDATE `users` 
				SET `user_current_challenge_id` = `user_current_challenge_id` + 1
				WHERE `user_id` = :user_id 
			"; 
			
			$query_params = array( 
				':user_id' => $_SESSION['user_id']
			); 				
			
			try 
			{ 
				$stmt = $db->prepare($query); 
				$result = $stmt->execute($query_params); 
				$_SESSION['current_challenge_id'] += 1;
			} 
			
			catch(PDOException $ex) 
			{ 
				 die("Failed to run query: " . $ex); 
			} 
			
		}
		else {
			$solutionMessage = "<div class='errorBox'>Incorrect</div>";
		}
	}

    header('Content-Type: text/html; charset=utf-8'); 
    

		$query = " 
			SELECT 
				*
			FROM challenges
			WHERE
				challenge_id = :id
		"; 
		
		$query_params = array( 
			':id' => $_SESSION['current_challenge_id']
		); 				

		try 
			{ 

			$stmt = $db->prepare($query); 
			$result = $stmt->execute($query_params); 	} 
		
		catch(PDOException $ex) 
				{ 
			 die("Failed to run query"); 
		} 
		

while ($row = $stmt->fetch()) {
 $challengeText = $row['challenge_instructions'];
 $challengeID = $row['challenge_id'];
 $challengeAuthor = $row['challenge_author'];
 $challengeSolution = $row['challenge_answer'];
 $_SESSION['current_challenge_solution'] = $challengeSolution;
}		


		$query = " 
			SELECT 
				*
			FROM hints
			WHERE
				challenge_id = :id
		"; 
		
		$query_params = array( 
			':id' => $_SESSION['current_challenge_id']
		); 				

		try 
			{ 

			$stmt = $db->prepare($query); 
			$result = $stmt->execute($query_params); 	} 
		
		catch(PDOException $ex) 
				{ 
			 die("Failed to run query"); 
		} 
		
		$hints = "";
		
while ($row = $stmt->fetch()) {
	 $hintStatus = $row['hint_status'];
	 
	 if($hintStatus == 1) { //if we should show the hint
		$hintText = $row['hint_text'];
		$hintID = $row['hint_id'];
		
		$hints .= "Hint: " . $hintText . "\n";
	 }
}
		
?>

<!DOCTYPE html>
<html lang="en">
    <head>
      <meta charset="utf-8">
      <title>Panel - Divine Challenge</title>
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <meta name="description" content="Divine Challenge Panel">
      <meta name="author" content="Divine">

		<!-- CSS -->
		<link rel="stylesheet" href="css/panel.css">
		<link href='http://fonts.googleapis.com/css?family=Montserrat:400,700' rel='stylesheet' type='text/css'>
		<script type='text/javascript' src='js/jquery-1.11.1.min.js'></script>
		<script type='text/javascript' src='js/jquery.particleground.min.js'></script>
		<script type='text/javascript' src='js/lol.js'></script>
		<script type="text/javascript">
			function submitForm() {
				document.getElementById('answerform').submit();
			}
		</script>
		<!--[if lt IE 9]>
			<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		<![endif]-->
		<link rel="shortcut icon" type="image/png" href="../resources/ficon2.png" />
		<link rel="shortcut icon" type="image/png" href="../resources/ficon2.png" />
    </head>
    <body>
<div id="particles">
  <div class="intro">
	<?php include('_header.php'); ?>

	<div id="content">
		<p style="margin-bottom: 26px;">Challenge number: <?php echo htmlspecialchars($_SESSION['current_challenge_id']) ?></p>
		<div class="box">
			<p id="boxTitle">Instructions:</p>
			<p class="smaller"><?php echo $challengeText ?></p>
		</div>
		<div id="hints" align="center">
			<?php if($hints != "") { ?>
				<p class="smaller" style="margin-top: 10px"><?php echo $hints; ?></p>
			<?php } ?>
		</div>
		<div id="message" align="center">
			<?php if($solutionMessage != "") { ?>
				<p class="solutionMessage"><?php echo $solutionMessage; ?></p>
			<?php } ?>
		</div>
		<div id="answerForm">
			<form method="post" id="answerform">
				<p>Solution:</p>
				<input type="text" name="solution" class="textBox" value=""><br />
				<a href="javascript: submitForm()" class="btn" name="submit">SUBMIT</a>
			</form>
		</div>
	</div>
	
	<br /><br />
	
	<div align="center">
		<?php echo WORDING_YOU_ARE_LOGGED_IN_AS . htmlspecialchars($_SESSION['user_name']) . "<br />"; ?>
		<a href="panel?logout" ><?php echo WORDING_LOGOUT; ?></a>
	</div>
	
	</div>
	<?php include('_footer.php'); ?>
	</div>
   </body>
</html>
