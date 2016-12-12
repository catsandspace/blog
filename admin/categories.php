<?php
    require_once "../templates/header.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");

    // Redirect to .dashboard.php if user is not a superadmin.
    } elseif ($_SESSION["permission"] != 1) {
        header("Location: ./dashboard.php");
    }

    // Reset functions for the internal variables
    $changeCategoryId = NULL;
    $errorMessage = NULL;

    // If-statement to check if button for adding new categories is set
    // If everything looks okay insert into db
    if (isset ($_POST["addCat"])):
        if (!empty($_POST["category"])):
            $category = mysqli_real_escape_string($conn, $_POST["category"]);
            $query = "INSERT INTO categories VALUES (NULL, '$category')";
            if ($stmt->prepare($query)):
                $stmt->execute();
            else:
                $errorMessage ="Faulty query in addCat";
            endif;
        else:
            $errorMessage ="Du måste ange en kategori";
        endif;
    endif;

    // If-statement to check if button for removing categories is set
    // If button is pressed continue to check through the array  and
    // for each category checked, remove it from the db
    if (isset ($_POST["removeCat"])):
        if (!empty($_POST["checklist"])):
            foreach ($_POST['checklist'] as $selected):
                $catId = $selected;
                $query = "DELETE FROM categories WHERE id=$catId";
                if ($stmt->prepare($query)):
                    $stmt->execute();
                else:
                    $errorMessage ="Faulty query in removeCat";
                endif;
            endforeach;
        else:
            $errorMessage ="Ange kategori att radera!";
        endif;
    endif;

    // If-statement to check if button for changing categories is set
    // A counter is set to see if only one category is checked
    // The id for the checked category is memorized, if nothing is
    // checked the category id is set to NULL
    if (isset ($_POST["change-category"])):
        if (!empty($_POST["checklist"])):
            $count = 0;
            foreach ($_POST['checklist'] as $selected):
                $catId = $selected;
                $count ++;
            endforeach;
            if ($count > 1):
                $errorMessage ="Du kan bara välja en kategori att ändra.";
                $catId = NULL;
            else:
                $changeCategoryId = $selected;
            endif;
        elseif (!empty($_POST["categoryChange"])):
            $category = mysqli_real_escape_string($conn, $_POST["categoryChange"]);
            $catId = $_POST["catId"];
            echo $category;
            echo $catId;
            $query = "UPDATE categories SET name = '$category' WHERE id = '$catId'";
            if ($stmt -> prepare($query)):
                $stmt->execute();
            else:
                $errorMessage ="Faulty query in change-category2";
            endif;
        else:
            $errorMessage = "Du måste ange en kategori!";
        endif;
    endif;


    $query = "SELECT * FROM categories";

    if ($stmt->prepare($query)):
        $stmt->execute();
        $stmt->bind_result($catId, $category);
    endif;

    $change = FALSE;
?>
<main>
    <h1 class="center-text margin-bottom-l">Kategorier</h1>
    <div class="flexbox-wrapper">
        <form method="post" action="categories.php" class="list-wrapper">
            <?php while (mysqli_stmt_fetch($stmt)): ?>
                <label class="checkbox-wrapper checkbox-wrapper--margin">
                    <input class="checkbox-wrapper__checkbox" type="checkbox" name="checklist[]" value="<?php echo $catId; ?>">
                    <i class="checkbox-wrapper__icon"></i>
                    <?php echo ucfirst($category); ?>
                </label>
                <?php if ($catId == $changeCategoryId) {
                    $change = TRUE;
                    $changeCatId = $catId;
                    $changeCat = $category;
                }
            endwhile; ?>
            <?php if ($errorMessage) { echo "<p class='error-msg'>".$errorMessage."</p>"; } ?>
            <?php if ($change): ?>
            <label class="form-field__label" for="categoryChange">Ändra kategori <?php echo $changeCat; ?></label>
            <input class="form-field" type="text" name="categoryChange">
            <input class="form-field" type="hidden" name="catId" value="<?php echo $changeCatId; ?>">
            <button type="submit" value="Ändra" name="change-category" class="button">Genomför ändring</button>
            <?php else: ?>
            <button type="submit" value="Ändra" name="change-category" class="button">Ändra</button>
            <?php endif; ?>
            <button type="submit" value="Ta bort" name="removeCat" class="button error">Ta bort</button>
        </form>
        <form method="post" action="categories.php" class="input-wrapper">
            <label class="form-field__label" for="add-category">Lägg till kategori</label>
            <input class="form-field" type="text" name="category" id="add-category">
            <button type="submit" value="Lägg till" name="addCat" class="button">Lägg till</button>
        </form>
    </div>
</main>
<?php include_once "../templates/footer.php"; ?>
