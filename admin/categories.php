<?php
    require_once "../templates/header.php"; // Header content.
    require_once "../assets/session.php";


    // Redirect to login.php if no session active.
    if (!isset($_SESSION["logged-in"]) && $_SESSION["logged-in"] == FALSE):
        header("Location: ../login.php");
    endif;

    // Reset functions for the internal variables
    $changeCategoryId = NULL;
    $errorMessage = NULL;

    // If-statement to check if button for adding new categories is set
    // If everything looks okay insert into db
    if (isset ($_GET["addCat"])):
        if (!empty($_GET["category"])): // Input given
            $category = mysql_real_escape_string($_GET["category"]);
            $query = "INSERT INTO categories VALUES (NULL, '$category')";
            if ($stmt->prepare($query)):
                $stmt->execute();
            else:
                $errorMessage ="Faulty query in addCat";
            endif;
        else:
            $errorMessage ="Du måste ange en kategori"; // No input given
        endif;
    endif;

    // If-statement to check if button for removing categories is set
    // If button is pressed continue to check through the array  and
    // for each category checked, remove it frpm the db
    if (isset ($_GET["removeCat"])):
        if (!empty($_GET["checkList"])):
            foreach ($_GET['checkList'] as $selected):
                $catId = $selected;
                $query = "DELETE FROM categories WHERE id=$catId";
                if ($stmt->prepare($query)):
                    $stmt->execute();
                else:
                    $errorMessage ="Faulty query in removeCat";
                endif;
            endforeach;
        else:
            $errorMessage ="Ange kategori att radera";
        endif;
    endif;

    // If-statement to check if button for changing categories is set
    // A counter is set to see if only one category is checked
    // The id for the checked category is memorized, if nothing is
    // checked the category id is set to NULL
    if (isset ($_GET["changeCat"])):
        if (!empty($_GET["checkList"])):
            $count = 0;
            foreach ($_GET['checkList'] as $selected):
                $catId = $selected;
                $count ++;
            endforeach;
            if ($count > 1):
                $errorMessage ="Du kan bara välja en.";
                $catId = NULL;
            else:
                $changeCategoryId = $selected;
            endif;
        endif;
    endif;

    // If-statement to check if button for changing categories in the category list
    if (isset ($_GET["changeCat2"])):
        if (!empty($_GET["categoryChange"])):
            $category = mysql_real_escape_string($_GET["categoryChange"]);
            $catId = $_GET["catId"];
            $query = "UPDATE categories SET name = '$category' WHERE id = $catId";
            if ($stmt -> prepare($query)):
                $stmt->execute();
            else:
                $errorMessage ="Faulty query in changeCat2";
            endif;
        else:
            $errorMessage = "Du måste ange en ny kategori.";
        endif;
    endif;

    // Select all rows from the database categories
    $query = "SELECT * FROM categories";
    if ($stmt->prepare($query)):
        $stmt->execute();
        $stmt->bind_result($catId, $cat);
    endif;
?>

<main>
<h2>Kategorier</h2>

<!--****************************************************************************
    FORM THAT PRINTS ALL CATEGORIES FROM DATABASE, INCLUDING CHECKBOXES
*****************************************************************************-->
    <div class="flexboxWrapper">
        <form method="get" action="categories.php" class="listWrapper">
            <div class="list">
            <div class="inner-list">
        <?php while (mysqli_stmt_fetch($stmt)): ?>
                <input type="checkbox" name="checkList[]" value="<?php echo $catId; ?>"> <?php echo $cat; ?>
                <?php if ($catId == $changeCategoryId): ?>
                        <form method="get" action="categories.php">
                            <input type="text" name="categoryChange">
                            <input type="hidden" name="catId" value="<?php echo $catId; ?>">
                            <button type="submit" value="Ändra" name="changeCat2" class="button">Ändra</button>
                        </form>
                <?php endif; ?>
            <br>
        <?php endwhile; ?>
        </div>
            </div>
            <button type="submit" value="Ändra" name="changeCat" class="button">Ändra</button>
            <button type="submit" value="Ta bort" name="removeCat" class="button red">Ta bort</button>

        </form>
        <form method="get" action="categories.php" class="inputWrapper">
            <label for="addCatagory">Lägg till kategori:</label>
            <input type="text" name="category" id="addCatagory">
            <button type="submit" value="Lägg till" name="addCat" class="button">Lägg till</button>
        </form>
    </div>
<?php
    // Print error message
    if ($errorMessage != NULL): echo $errorMessage; endif;

    include_once "../templates/footer.php"; // Footer.
?>
