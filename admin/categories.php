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
    if (isset ($_POST["addCat"])):
        if (!empty($_POST["category"])): // Input given
            $category = mysql_real_escape_string($_POST["category"]);
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
    if (isset ($_POST["removeCat"])):
        if (!empty($_POST["checkList"])):
            foreach ($_POST['checkList'] as $selected):
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
    if (isset ($_POST["changeCat"])):
        if (!empty($_POST["checkList"])):
            $count = 0;
            foreach ($_POST['checkList'] as $selected):
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
            $category = mysql_real_escape_string($_POST["categoryChange"]);
            $catId = $_POST["catId"];
            echo $category;
            echo $catId;
            $query = "UPDATE categories SET name = '$category' WHERE id = '$catId'";
            if ($stmt -> prepare($query)):
                $stmt->execute();
            else:
                $errorMessage ="Faulty query in changeCat2";
            endif;
        else:
            $errorMessage = "Du måste ange en kategori!";
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
    IT ALSO PRINTS A TEXT INPUT IF SOMEONE HAS PRESSED CHANGE BUTTON
*****************************************************************************-->
    <div class="flexboxWrapper">

        <form method="post" action="categories.php" class="listWrapper">
            <div class="list">
                <div class="inner-list">
                <?php
                    $change=FALSE;
                    while (mysqli_stmt_fetch($stmt)): ?>
                        <input type="checkbox" name="checkList[]" value="<?php echo $catId; ?>"> <?php echo $cat; ?>
                        <?php

                        if ($catId == $changeCategoryId):

                                $change=TRUE;
                                $changeCatId="$catId";
                                $changeCat="$cat";
                        endif; ?>
                        <br>
                <?php endwhile; ?>
                </div>
            </div>
            <br>
            <?php
                if ($change):
            ?>
                    <label for="categoryChange">Ändra kategori <?php echo $changeCat; ?>:</label>
                    <input type="text" name="categoryChange">
                    <input type="hidden" name="catId" value="<?php echo $changeCatId; ?>">
            <?php
                endif;
            ?>
            <button type="submit" value="Ändra" name="changeCat" class="button">Ändra</button>
            <button type="submit" value="Ta bort" name="removeCat" class="button error">Ta bort</button>
        </form>

        <form method="post" action="categories.php" class="inputWrapper">
            <label for="addCatagory">Lägg till kategori:</label>
            <input type="text" name="category" id="addCategory">
            <button type="submit" value="Lägg till" name="addCat" class="button">Lägg till</button>
        </form>
    </div>
<?php
    // Print error message
    if ($errorMessage != NULL): echo $errorMessage; endif;

    include_once "../templates/footer.php"; // Footer.
?>
