<?php
  require_once "../templates/header.php";
  require_once "../assets/config.php";
  require_once "../assets/db_connect.php";

  if($db_error) {
    echo $db_error_message;
  }

  // TODO: This needs to be connected to the current user.
  $userid = 2;

  // This query gets the users first name.
  $query = "SELECT firstname, permission FROM users WHERE id = '{$userid}'";

  if ($stmt->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($user_firstname, $permission);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
  }

  // This checks what permission the user has. Should probably be a function. 
  if ($permission == 0) {
    $user_permission = "Redaktör";
  } elseif ($permission == 1) {
    $user_permission = "Superadministratör";
  }

?>

<h2>Inloggad användare: <?php echo $user_firstname; ?></h2>
<p>Behörighet: <?php echo $user_permission; ?></p>
<a href="./addpost.php" class="button" target="_self">Skapa nytt inlägg</a>
<a href="./users.php" class="button" target="_self">Hantera användare</a>
<a href="./categories.php" class="button" target="_self">Hantera kategorier</a>
<a href="./postlist.php" class="button" target="_self">Hantera inlägg</a>
<a href="../assets/logout.php" class="button" target="_self">Logga ut</a>
<?php require_once "../templates/footer.php"; ?>
