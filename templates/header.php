<?php

   // Database connection
   require_once __DIR__."/../assets/db_connect.php";
   require_once __DIR__."/../assets/session.php";

   // Execute categories query
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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $path.'styles/css/main.css';?>">

    <title>Cats and Space</title>
</head>
<body>
    <header>
       <img src="<?php echo $path; ?>img/logo.png" alt="logo">
       <h1 class="logo-title">Cats and Space</h1>
       <nav class="hamburger">
            <ul>
                <li class="has-sub"><a href="#"><span><i class="fa fa-fw fa-bars"></i></span></a>
                    <ul>
                        <li><a href="<?php echo $path; ?>index.php">Hem</a></li>
                        <?php while (mysqli_stmt_fetch($stmt)): ?>
                        <li><a href="<?php echo $path; ?>index.php?display=<?php echo $id; ?>"><?php echo ucfirst($category); ?></a></li>
                        <?php endwhile?>
                        <li><a href="#">Kontakt</a></li>
                        <?php if(isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE): ?>
                        <li><a href="<?php echo $path; ?>admin/dashboard.php">Dashboard</a></li>
                        <li><a href="<?php echo $path; ?>assets/logout.php">Logga ut</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
