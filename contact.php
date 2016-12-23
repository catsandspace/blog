<?php require_once "./templates/header.php";

/*******************************************************************************
    START OF QUERY USED TO PRINT OUT USERS
*******************************************************************************/

    $query = "SELECT email, firstname, lastname FROM users WHERE permission = 0";

    if ($stmt->prepare($query)):
        $stmt->execute();
        $stmt->bind_result($email, $firstName, $lastName);
    endif;
?>
<main>
    <h1 class="center-text">Kontaktinformation</h1>
    <div class="flexbox-wrapper">
    <?php while (mysqli_stmt_fetch($stmt)):
        $name = $firstName. ' '.$lastName;?>
        <div class="flex-item">
            <h2 class="padding-top-xl primary-color center-text"><?php echo $name; ?></h2>
            <ul class="list-style-none no-padding">
                <li class="contact-list__item"><a href="mailto:<?php echo $email ?>" class="contact-list__item"><i class="fa fa-envelope" aria-hidden="true"></i> <?php echo $email; ?></a></li>
                <li class="contact-list__item"><a href="tel:084429500" class="contact-list__item"><i class="fa fa-phone" aria-hidden="true"></i> 08-442 95 00</a></li>
            </ul>
        </div>
        <?php endwhile; ?>
    </div>
</main>
 <?php require_once "./templates/footer.php"; ?>
