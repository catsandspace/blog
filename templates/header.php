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
    <link rel="shortcut icon" href="<?php echo $path.'./img/favicon.ico'; ?>" type="image/x-icon">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $path.'styles/css/main.css';?>">

    <title>Cats and Space</title>
</head>
<body>
    <header>
        <a href="<?php echo $path.'./index.php'; ?>">
            <svg class="logo">
                <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#logo"></use>
            </svg>
        </a>
       <h1 class="logo-title">Cats and Space</h1>
       <nav class="hamburger">
            <ul class="list-style-none">
                <li class="has-sub"><a href="#"><span class="burger-size"><i class="fa fa-fw fa-bars"></i></span></a>
                    <ul class="list-style-none box-shadow">
                        <li><a href="<?php echo $path; ?>index.php" class="hamburger-menu">Start</a></li>
                        <?php while (mysqli_stmt_fetch($stmt)): ?>
                        <li><a href="<?php echo $path; ?>index.php?display=<?php echo $id; ?>" class="hamburger-menu hamburger-menu--categories">Kategori: <?php echo ucfirst($category); ?></a></li>
                        <?php endwhile?>
                        <li><a href="<?php echo $path; ?>archive.php" class="hamburger-menu">Arkiv</a></li>
                        <li><a href="<?php echo $path; ?>contact.php" class="hamburger-menu <?php if(!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE): ?>border-none<?php endif; ?>">Kontakt</a></li>
                        <?php if(isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE): ?>
                        <li><a href="<?php echo $path; ?>admin/dashboard.php" class="hamburger-menu">Kontrollpanel</a></li>
                        <li><a href="<?php echo $path; ?>assets/logout.php" class="hamburger-menu border-none">Logga ut</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
