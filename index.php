<?php

	// Includes.
	include_once ("assets/db_connect.php"); // Database connection.
	include_once ("templates/header.php"); // Header content.
	
	// Variables.
	$display = ""; // To avoid "undefined variable".

	// SQL statement with LEFT JOIN table -> posts & categories.
	$query  = "SELECT posts.*, categories.category FROM posts LEFT JOIN categories ON posts.categoryid = categories.id";

	// If GET request "display" is set. 
	if (isset($_GET["display"])) {
		$display = $_GET["display"];

		// New SQL statement WHERE categories.category = $display.
		$query = "SELECT posts.*, categories.category FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE categories.id = '{$display}'";
	}

	// Execute query.
	if ($stmt->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($id, $userid, $created, $updated, $image, $title, $content, $published, $catid, $categoryname);
	}

?>

<section>

	<?php while (mysqli_stmt_fetch($stmt)): ?>
	<article>
		<h2><?php echo $title; ?></h2>
		<img src="<?php echo $image; ?>" alt="<?php echo $title; ?>">
		<p><?php echo $content; ?></p>
		<p>Tags: <?php echo str_replace(' ', '', $categoryname); ?></p>
	</article>
	<?php endwhile; ?>

</section>

<?php include_once ("templates/footer.php"); ?>