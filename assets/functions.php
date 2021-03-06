<?php

/**
 * The function checks if an alternative variable exists and return either that or a
 * predefined variable.
 * @param  string $alternative The optional alternative variable.
 * @param  string $predefined  The predefined variable.
 * @return string              Return one of the two.
 */
function checkExistingOrReturnPredefined($alternative, $predefined) {
    if ($alternative != NULL) {
        return $alternative;
    } else {
        return $predefined;
    }
}

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
 * The function converts permission int to string.
 * @param  int      $permission     The user's permission level.
 * @return string                   The user's permission level as string.
 */
function convertPermissionToString($permission) {
    if ($permission == 0) {
        return utf8_decode("Redaktör");
    } elseif ($permission == 1) {
        return utf8_decode("Superadministratör");
    }
    return NULL;
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

/**
 * The function removes special characters and format string as HTML markup.
 * @param  string $string The string that needs formatting.
 * @return string         HTML markup with replaced special characters.
 */
function formatInnerHtml($string) {
    $newString = str_replace("\n", "<br>", $string);
    $newString = str_replace("\r", "", $newString);
    $newString = str_replace("<", "&lt;", $newString);
    return replaceSpecialCharacters($newString);
}

/**
 * The function unsets and destroy cookies stored in a session.
 */
function logout() {
    unset($_SESSION["logged-in"]);
    setcookie("session_catsandspace", "", time()-(60*60*24), "/");
    session_destroy();
}

/**
 * The function replaces special characters.
 * @param  string $string The string that needs to be formatted.
 * @return string         The string with replaced special characters.
 */
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
 * The function takes a existing array and returns a new "unique" array without duplicate values.
 * @param  array    $array        The array that needs to be checked for multiple values
 * @param  int      $key          Key value that needs to be checked
 * @return array    $tempArray    Array that does not contain ints or duplicated values
 */
function uniqueArray($array, $key) {
    $tempArray = array();
    $i = 0;
    $keyArray = array();

    foreach ($array as $value) {
        if (!in_array($value[$key], $keyArray)) {
            $keyArray[$i] = $value[$key];
            $tempArray[$i] = $value;
        }
        $i++;
    }
    return $tempArray;
}

?>
