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
    <link rel="apple-touch-icon" sizes="180x180" href="<?php echo $path?>/img/apple-touch-icon.png">
    <link rel="icon" type="image/png" href="<?php echo $path?>/img/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="<?php echo $path?>/img/favicon-16x16.png" sizes="16x16">
    <link rel="manifest" href="<?php echo $path?>/img/manifest.json">
    <link rel="mask-icon" href="<?php echo $path?>/img/safari-pinned-tab.svg" color="#1d2225">
    <link rel="shortcut icon" href="<?php echo $path?>/img/favicon.ico">
    <meta name="msapplication-config" content="<?php echo $path?>/img/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo $path.'styles/css/main.css';?>">

    <title>Cats and Space</title>
</head>
<body>
    <header>
        <div class="header-logotext">
            <a href="<?php echo $path.'./index.php'; ?>">
                <svg class="header-logotext__logo">
                    <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#logo"></use>
                </svg>
            </a>
            <span class="header-logotext__text">Cats and Space</span>
        </div>
        <nav class="nav-desktop">
            <ul class="nav-desktop_list">
                <li class="nav-desktop_list-item">Start</li>
                <li class="nav-desktop_list-item">Kategorier
                    <ul class="nav-desktop_dropdown">
                        <li class="nav-desktop_dropdown-item">Cats</li>
                        <li class="nav-desktop_dropdown-item"></li>
                        <li class="nav-desktop_dropdown-item"></li>
                    </ul>
                </li>
                <li class="nav-desktop_list-item">Arkiv</li>
                <li class="nav-desktop_list-item">Kontrollpanel</li>
                <li class="nav-desktop_list-item">Kontakt</li>
            </ul>
        </div>
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
