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
* @param 	int    $id            The users id
* @param 	int    $permission    The users permission level
* @param 	string $uname         The users username
* @param 	string $upass         The users password
*/
function storeUserInSession($id, $permission, $uname, $upass) {
    $_SESSION["logged-in"] = TRUE;
    $_SESSION["userid"] = $id;
    $_SESSION["permission"] = $permission;
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

// TODO: Describe what this function does!
function convertPermissionToString($permission) {
    if ($permission == 0) {
        return utf8_decode("Redaktör");
    } elseif ($permission == 1) {
        return utf8_decode("Superadministratör");
    }
    return NULL;
}

// TODO: Describe what this does!
function checkExistingOrReturnPredefined($alternative, $predefined) {
    if ($alternative != NULL) {
        return $alternative;
    } else {
        return $predefined;
    }
}

/**
 * The function removes special characters and format string as HTML markup.
 * @param  string $string The string that needs formatting.
 * @return string         HTML markup.
 */
function formatInnerHtml($string) {
    $newString = str_replace("\n", "<br>", $string);
    $newString = str_replace("\r", "", $newString);
    return replaceSpecialCharacters($newString);
}

function replaceSpecialCharacters($string) {
    $newString = str_replace('\n', "\n", $string);
    $newString = str_replace('\r', "\r", $newString);
    $newString = str_replace('\t', "\t", $newString);
    $newString = str_replace('\\\'', "'", $newString);
    $newString = str_replace('\\"', '"', $newString);
    $newString = str_replace('\\', "", $newString);
    return $newString;
}

/**
 * The function formats a timestamp according to year-month-date.
 * @param  int $timestamp       The timestamp returned from the database
 * @return int $formattedDate   The date formatted as year-month-date.
 */
function formatDate($timestamp) {
    $formattedDate = date('Y-m-d', strtotime($timestamp));
    return $formattedDate;
}

?>
