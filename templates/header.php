<?php
	// Execute categories query.
	$query = "SELECT * FROM categories";
	if ($stmt->prepare($query)) {
		$stmt->execute();
		$stmt->bind_result($id, $category);
	}

?>

<!DOCTYPE html>
<html lang="sv">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Cats and Space</title>
</head>
<body>
<header>
	<img src="img/logo.png" alt="logo">
	<h1 class="logo-title">Cats and Space</h1>
	<nav>
		<ul>
			<li><a href="index.php">Hem</a></li>
			<?php while (mysqli_stmt_fetch($stmt)): ?>
			<li><a href="?display=<?php echo $category; ?>"><?php echo ucfirst($category); ?></a></li>
			<?php endwhile?>
			<li><a href="#">Kontakt</a></li>
		</ul>
	</nav>
</header>