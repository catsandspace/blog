<?php

/**
 * The function checks size and type on uploaded files.
 * @param  string $file The uploaded file.
 * @return string       The function returns information about the error.
 */
function checkUploadedFile($file) {
  $allowedFileTypes = array("jpg", "jpeg", "gif", "png", "webp");
  $listAllowedTypes = implode(", ", $allowedFileTypes);
  $type = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

  if ($file["size"] > 5000000) {
    return "Filen är för stor (max 5 MB).";
  }

  if ($file["size"] == 0 || empty($file)) {
    return "Du har inte laddat upp någon fil.";
  }

  if (!in_array($type, $allowedFileTypes)) {
    return "Förbjudet filformat. <br>Tillåtna format: {$listAllowedTypes}";
  }
  return NULL;
}

/**
* The functions stores variables in the session
* @param 	int    $id 			The users id
* @param 	string $username 	The users username
* @param 	string $upass 		The users password
*/
function storeUserInSession($id, $uname, $upass) {
    $_SESSION["logged-in"] = TRUE;
	$_SESSION["userid"] = $id;
	$_SESSION["username"] = $uname;
	$_SESSION["userpassword"] = $upass;
}

/**
 * The function unsets and destroy cookies stored in a session.
 */
function logout() {
  unset($_SESSION["logged-in"]);
  setcookie("session_catsandspace", "", time()-(60*60*24), "/");
  session_destroy();
}
?>
