<?php 
require_once "templates/header.php"; // Lägger till header

if(isset($_POST["Loggain"])) {

	if(!empty($_POST["username"]) && !empty($_POST["Password"]) ) {

		require_once "assets/db_connect.php"; // Hämtar info för att koppla upp sig mot databasen.
	
		// $conn = new mysqli("localhost", "root", "", "catsandspace");

		$user = mysqli_real_escape_string($conn, $_POST["username"]);
		$pass = mysqli_real_escape_string($conn, $_POST["Password"]);

		$stmt = $conn->stmt_init(); 

		if($stmt->prepare("SELECT * FROM users WHERE username = '{$user}' ")) { 
			
			$stmt->execute(); 

			$stmt->bind_result($id, $perm, $uname, $upass, $email, $website, $fname, $lname, $pic, $desc); 
			$stmt->fetch();  

			$_SESSION["userid"] = $id;			
			$_SESSION["username"] = $uname;
			$_SESSION["userpassword"] = $upass;

			echo "$uname<br>" . "$email<br>" . "$website<br>" . "$desc<br>";

			require_once "assets/functions.php"; 
		}
	}
	else {
		echo "Misslyckades att logga in!";
	
	}
}
?>
<!--*************************************************
*****************************************************
		Formulär för att logga in
*****************************************************
**************************************************-->
<form action="login.php" method="post">
	<fieldset>
		<legend>Login</legend>
		Username/Email: <br>
		<input type="text" name="username"><br>
		Password: <br>
		<input type="password" name="Password"><br>
		<input type="submit" name="Loggain" value="Logga in">
	</fieldset>
</form>
<!-- Lägger till footer -->
<?php
require_once "templates/footer.php";
?>