<?php
    require_once "../templates/header.php";
    require_once "../assets/session.php";

    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == false) {
        header("Location: ../login.php");
    }

    // SQL statement with LEFT JOIN table -> posts & categories.
    // TODO: Just get the variables you need.
    $query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id";

    // Execute query.
    if ($stmt->prepare($query)) {
       $stmt->execute();
       $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId, $categoryName);
   }
?>
<h2>Inlägg</h2>
<table>
<?php while (mysqli_stmt_fetch($stmt)): ?>
    <tr>
        <td><?php echo $image; ?></td>
        <td><?php echo $title; ?></td>
    </tr>
<?php endwhile; ?>
</table>
<?php require_once "../templates/footer.php"; ?>
