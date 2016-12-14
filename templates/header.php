<?php
   require_once __DIR__."/../assets/db_connect.php";
   require_once __DIR__."/../assets/session.php";

/*******************************************************************************
    QUERY TO PRINT OUT CATEGORIES
*******************************************************************************/

   $query = "SELECT * FROM categories";
   if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $category);
   }

/*******************************************************************************
    SET USER PERMISSION
*******************************************************************************/

   if (isset($_SESSION["permission"])) {
       $currentUserPermission = $_SESSION["permission"];
   } else {
       $currentUserPermission = NULL;
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
                <li class="nav-desktop__list-item">
                    <a href="<?php echo $path; ?>index.php" class="nav-desktop__link">Start</a>
                </li>
                <li class="nav-desktop__list-item"><span class="nav-desktop__link">Kategorier <i class="fa fa-caret-down" aria-hidden="true"></i></span>
                    <ul class="nav-desktop__dropdown">
                        <?php while (mysqli_stmt_fetch($stmt)): ?>
                            <li>
                                <a href="<?php echo $path; ?>index.php?display=<?php echo $id; ?>" class="nav-desktop__dropdown-item"><?php echo ucfirst($category); ?></a>
                            </li>
                        <?php endwhile?>
                    </ul>
                </li>
                <li class="nav-desktop__list-item">
                    <a href="<?php echo $path; ?>archive.php" class="nav-desktop__link">Arkiv</a>
                </li>
                <li class="nav-desktop__list-item">
                    <a href="<?php echo $path; ?>contact.php" class="nav-desktop__link">Kontakt</a>
                </li>
                <?php if(isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE): ?>
                    <li class="nav-desktop__list-item">
                        <a href="<?php echo $path; ?>admin/dashboard.php" class="nav-desktop__link"><i class="fa fa-cog" aria-hidden="true"></i> Kontrollpanel</a>
                        <ul class="nav-desktop__dropdown">
                            <li>
                                <a href="<?php echo $path; ?>admin/posteditor.php" class="nav-desktop__dropdown-item">Skapa nytt inlägg</a>
                            </li>
                            <li>
                                <a href="<?php echo $path; ?>admin/postlist.php" class="nav-desktop__dropdown-item">Se alla inlägg</a>
                            </li>
                            <li>
                                <a href="<?php echo $path; ?>admin/comments.php" class="nav-desktop__dropdown-item">Se alla kommentarer</a>
                            </li>
                            <?php if ($currentUserPermission == 1): ?>
                                <li>
                                    <a href="<?php echo $path; ?>admin/categories.php" class="nav-desktop__dropdown-item">Hantera kategorier</a>
                                </li>
                                <li>
                                    <a href="<?php echo $path; ?>admin/users.php" class="nav-desktop__dropdown-item">Hantera användare</a>
                                </li>
                            <?php endif; ?>
                            <li>
                                <a href="<?php echo $path; ?>admin/dashboard.php?statistics=true" class="nav-desktop__dropdown-item">Se statistik</a>
                            </li>
                            <li>
                                <a href="<?php echo $path; ?>assets/logout.php" class="nav-desktop__dropdown-item nav-desktop__dropdown-item--logout">Logga ut</a>
                            </li>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <nav class="hamburger">
            <ul class="list-style-none">
                <li class="has-sub"><a href="#"><span class="burger-size"><i class="fa fa-fw fa-bars"></i></span></a>
                    <ul class="list-style-none box-shadow">
                        <li>
                            <a href="<?php echo $path; ?>index.php" class="hamburger-menu">Start</a>
                        </li>
                        <?php while (mysqli_stmt_fetch($stmt)): ?>
                            <li>
                                <a href="<?php echo $path; ?>index.php?display=<?php echo $id; ?>" class="hamburger-menu hamburger-menu--categories">Kategori: <?php echo ucfirst($category); ?></a>
                            </li>
                        <?php endwhile?>
                        <li>
                            <a href="<?php echo $path; ?>archive.php" class="hamburger-menu">Arkiv</a>
                        </li>
                        <li>
                            <a href="<?php echo $path; ?>contact.php" class="hamburger-menu <?php if(!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE): ?>border-none<?php endif; ?>">Kontakt</a>
                        </li>
                        <?php if(isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == TRUE): ?>
                        <li>
                            <a href="<?php echo $path; ?>admin/dashboard.php" class="hamburger-menu">Kontrollpanel</a>
                        </li>
                        <li class="error-link">
                            <a href="<?php echo $path; ?>assets/logout.php" class="hamburger-menu border-none">Logga ut</a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </nav>
    </header>
