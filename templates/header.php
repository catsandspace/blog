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

   //TODO: Now when this variable is included in header we can remove it from dashbord etc.
   if (isset($_SESSION["permission"])) {
   $currentUsersPermission = $_SESSION["permission"];
   } else {
       $currentUsersPermission = NULL;
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
            <a href="<?php echo $path.'./index.php'; ?>">
                <span class="header-logotext__text">Cats and Space</span>
            </a>
        </div>
        <nav class="nav-desktop">
            <ul class="nav-desktop__list">
                <a href="<?php echo $path; ?>index.php" class="nav-desktop__list-item"><li>Start</li></a>
                <li class="nav-desktop__list-item">Kategorier <i class="fa fa-caret-down" aria-hidden="true"></i>
                    <ul class="nav-desktop__dropdown">
                        <?php while (mysqli_stmt_fetch($stmt)): ?>
                        <a href="<?php echo $path; ?>index.php?display=<?php echo $id; ?>" class="nav-desktop__dropdown-item"><li><?php echo ucfirst($category); ?></li></a>
                        <?php endwhile?>
                    </ul>
                </li>
                <a href="<?php echo $path; ?>archive.php" class="nav-desktop__list-item"><li>Arkiv</li></a>
                <?php if(isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE): ?>
                <li class="nav-desktop__list-item"><i class="fa fa-cog" aria-hidden="true"></i> Kontrollpanel
                    <ul class="nav-desktop__dropdown">
                        <a href="<?php echo $path; ?>admin/posteditor.php" class="nav-desktop__dropdown-item"><li>Skapa nytt inlägg</li></a>
                        <a href="<?php echo $path; ?>admin/postlist.php" class="nav-desktop__dropdown-item"><li>Se alla inlägg</li></a>
                        <a href="<?php echo $path; ?>admin/comments.php" class="nav-desktop__dropdown-item"><li>Se alla kommentarer</li></a>
                        <?php if ($currentUsersPermission == 1): ?>
                        <a href="<?php echo $path; ?>admin/categories.php" class="nav-desktop__dropdown-item"><li>Hantera kategorier</li></a>
                        <a href="<?php echo $path; ?>admin/users.php" class="nav-desktop__dropdown-item"><li>Hantera användare</li></a>
                        <?php endif; ?>
                        <a href="<?php echo $path; ?>admin/dashboard.php?statistics=true" class="nav-desktop__dropdown-item"><li>Se statistik</li></a>
                        <a href="<?php echo $path; ?>assets/logout.php" class="nav-desktop__dropdown-item"><li>Logga ut</li></a>
                    </ul>
                </li>
                <?php endif; ?>
                <a href="<?php echo $path; ?>contact.php" class="nav-desktop__list-item"><li>Kontakt</li></a>
            </ul>
        </nav>
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
