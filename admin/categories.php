<?php
    require_once "../assets/db_connect.php";
    require_once "../assets/functions.php";
    require_once "../assets/session.php";

    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE) {
        header("Location: ../login.php");

    // Redirect if user is not a superadmin.
    } elseif ($_SESSION["permission"] != 1) {
        header("Location: ./dashboard.php");
    }

/*******************************************************************************
    START OF VARIABLES USED ON PAGE
*******************************************************************************/

    $changeCategoryId = NULL;
    $change = FALSE;
    $errorMessage = NULL;
    $queryFailed = "Det blev något fel. Försök igen senare.";

/*******************************************************************************
    START OF QUERY TO CHANGE CATEGORY
*******************************************************************************/

    if (isset ($_POST["change-category"])):
        if (!empty($_POST["checklist"])):
            $count = 0;

            foreach ($_POST['checklist'] as $selected):
                $catId = $selected;
                $count ++;
            endforeach;

            if ($count > 1):
                $errorMessage = "Du kan bara ändra en kategori åt gången.";
                $catId = NULL;

            else:
                $changeCategoryId = $selected;

            endif;

        elseif (!empty($_POST["category-change"])):

            $category = mysqli_real_escape_string($conn, $_POST["category-change"]);
            $catId = $_POST["category-id"];
            $query = "UPDATE categories SET name = '{$category}' WHERE id = '{$catId}'";

            if ($stmt -> prepare($query)):
                $stmt->execute();
                header("Location: ./categories.php");

            else:
                $errorMessage = $queryFailed;

            endif;

        else:
            $errorMessage = "Du måste välja vilken kategori du vill ändra.";

        endif;

    endif;

/*******************************************************************************
    START OF QUERY TO REMOVE CATEGORY
*******************************************************************************/

    if (isset($_POST["removeCat"])):

        if (!empty($_POST["checklist"])):

            foreach ($_POST['checklist'] as $selected):
                $catId = $selected;
                $query = "DELETE FROM categories WHERE id = '{$catId}'";

                if ($stmt->prepare($query)):
                    $stmt->execute();
                    header("Location: ./categories.php");
                else:
                    $errorMessage = $queryFailed;
                endif;
            endforeach;
        else:
            $errorMessage = "Du måste välja vilken kategori du vill radera.";
        endif;
    endif;

/*******************************************************************************
    START OF QUERY TO ADD NEW CATEGORY
*******************************************************************************/

    if (isset($_POST["addCat"])):

        if (!empty($_POST["category"])):

            $category = mysqli_real_escape_string($conn, $_POST["category"]);
            $query = "INSERT INTO categories VALUES (NULL, '{$category}')";

            if ($stmt->prepare($query)):
                $stmt->execute();
                header("Location: ./categories.php");
            else:
                $errorMessage = $queryFailed;
            endif;
        else:
            $errorMessage = "Du måste döpa din kategori till något.";
        endif;
    endif;

/*******************************************************************************
    GET HEADER WHICH (AMONG OTHER THINGS) PRINTS HTMLS
*******************************************************************************/

    require_once "../templates/header.php";

/*******************************************************************************
    START OF QUERY USED TO PRINT OUT CATEGORIES
*******************************************************************************/

    $query = "SELECT * FROM categories";

    if ($stmt->prepare($query)):
        $stmt->execute();
        $stmt->bind_result($catId, $category);
    endif;
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
            <?php if ($errorMessage): ?>
                <p class="error-msg"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <?php if ($change): ?>
                <label class="form-field__label" for="category-change">Ändra kategori <?php echo $changeCat; ?></label>
                <input class="form-field" type="text" name="category-change">
                <input class="form-field" type="hidden" name="category-id" value="<?php echo $changeCatId; ?>">
                <button type="submit" value="Ändra" name="change-category" class="button">Genomför ändring</button>
            <?php else: ?>
                <button type="submit" name="change-category" class="button">Ändra</button>
            <?php endif; ?>
            <button type="submit" name="removeCat" class="button error">Ta bort</button>
        </form>
        <form method="post" action="categories.php" class="input-wrapper">
            <label class="form-field__label" for="add-category">Lägg till kategori</label>
            <input class="form-field" type="text" name="category" id="add-category">
            <button type="submit" name="addCat" class="button">Lägg till</button>
        </form>
    </div>
</main>
<?php include_once "../templates/footer.php"; ?>
