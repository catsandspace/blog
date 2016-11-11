<?php
  require_once "../templates/header.php";
  require_once "../assets/db_connect.php";

  if ($db_error) {
    echo $db_error_message;
  }

  // Only temporary variables.
  // $userid = 1; Editor
  $userid = 2; // Superadmin

  // This query returns the user's first name and permission level.
  $query = "SELECT firstname, permission FROM users WHERE id = '{$userid}'";

  if ($stmt->prepare($query)) {
    $stmt->execute();
    $stmt->bind_result($user_firstname, $permission);
    $stmt->fetch();
    $stmt->close();
    $conn->close();
  }

  // This checks current user's permission level. Should probably be a function.
  if ($permission == 0) {
    $user_permission = "Redaktör";
  } elseif ($permission == 1) {
    $user_permission = "Superadministratör";
  }

?>

<h2>Inloggad användare: <?php echo $user_firstname; ?></h2>
<!-- TODO: Remove information about permission after testing is completed. -->
<p>Behörighet: <?php echo $user_permission; ?></p>
<a href="./addpost.php" class="button">Skapa nytt inlägg</a>
<a href="./postlist.php" class="button">Se alla inlägg</a>
<a href="./comments.php" class="button">Se alla kommentarer</a>
<?php if ($permission == 1): ?>
<a href="./postlist.php" class="button">Hantera kategorier</a>
<a href="./users.php" class="button">Hantera användare</a>
<?php endif; ?>
<a href="../assets/logout.php" class="button">Logga ut</a>

<?php require_once "../templates/footer.php"; ?>
