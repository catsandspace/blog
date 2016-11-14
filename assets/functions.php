<?php

/**
 * The function checks size and type on uploaded files.
 * @param  string $file The uploaded file.
 * @return string       The function returns information about the error.
 */
function checkUploadedFile($file) {
  $allowed_file_types = array("jpg", "jpeg", "gif", "png", "webp");
  $list_allowed_types = implode(", ", $allowed_file_types);
  $type = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));

  if ($file["size"] > 5000000) {
    return "Filen är för stor (max 5 MB).";
  }

  if ($file["size"] == 0 || empty($file)) {
    return "Du har inte laddat upp någon fil.";
  }

  if (!in_array($type, $allowed_file_types)) {
    return "Förbjudet filformat. <br>Tillåtna format: {$list_allowed_types}";
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
	$_SESSION["userid"] = $id;			
	$_SESSION["username"] = $uname;
	$_SESSION["userpassword"] = $upass;
}
?>
