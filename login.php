<?php 
require_once "./templates/header.php"; 
require_once "assets/db_connect.php";

if (isset($_POST["login"]) ) {

	if (!empty($_POST["username"]) && !empty($_POST["password"]) ) {

		 

		$user = mysqli_real_escape_string($conn, $_POST["username"]);
		$pass = mysqli_real_escape_string($conn, $_POST["password"]);

		if($stmt->prepare("SELECT * FROM users WHERE username = '{$user}' ") ) { 
			
			$stmt->execute(); 

			$stmt->bind_result($id, $perm, $uname, $upass, $email, $website, $fname, $lname, $pic, $desc); 
			$stmt->fetch();  

			$_SESSION["userid"] = $id;			
			$_SESSION["username"] = $uname;
			$_SESSION["userpassword"] = $upass;

			echo "$uname<br>" . "$email<br>" . "$website<br>" . "$desc<br>";

			//require_once "assets/functions.php"; Make a function instead. 
		}
	}
	else {
		echo "Misslyckades att logga in!"; // make a variabel instead.
	
	}
}
?>
<!--*************************************************
*****************************************************
		Formulär för att logga in
*****************************************************
**************************************************-->
<form action="login.php" method="POST">
	<fieldset>
		<legend>Login</legend>
		Username/Email: <br>
		<input type="text" name="username"><br>
		Password: <br>
		<input type="password" name="password"><br>
		<input type="submit" name="login" value="Logga in">
	</fieldset>
</form>
<!-- Lägger till footer -->
<?php
require_once "templates/footer.php";
?>

<!-- Indentera ett steg från php tagen, använd ./ framför assets och templates, engelska kommentarer