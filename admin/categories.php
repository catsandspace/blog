<?php

  // Variables used to connect to the database.
  $db_hostname = "localhost"; // The hostname of the database.
  $db_user = "root"; // The username of the database.
  $db_password = ""; // The password of the database.
  $db_name = "catsandspace"; // The name of the database.

    require_once "../assets/db_connect.php";
?>

<h1>Kategorier</h1>

<?php

// $query = "SELECT * FROM posts LEFT JOIN users ON posts.userid = users.id";
$query = "SELECT * FROM categories";
if ($stmt -> prepare($query)) {
    $stmt-> execute();
    $stmt -> bind_result($catid, $cat);

    while (mysqli_stmt_fetch($stmt)) {
        echo $cat;
        echo "<br>";
    }
}

?>
