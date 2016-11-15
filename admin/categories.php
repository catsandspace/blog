<?php
    require_once (__DIR__."/../assets/db_connect.php");

    if (isset ($_GET["addCat"])) {
        $category = strip_tags($_GET["category"]);
        $query = "INSERT INTO categories VALUES (NULL, '$category')";
        if ($stmt -> prepare($query)) {
            $stmt->execute();
        } else {
            echo "fel";
        }
    }

    if (isset ($_GET["removeCat"])) {
        if (!empty($_GET["checkList"])) {
            foreach ($_GET['checkList'] as $selected) {
                $catId = $selected;
                $query = "DELETE FROM categories WHERE id=$catId";
                if ($stmt -> prepare($query)) {
                    $stmt->execute();
                } else {
                    echo "fel";
                } //end if
            } // end foreach              
        } else {
            echo "fellist";
        }
    }
            

    if (isset ($_GET["changeCat"])) {
        if (!empty($_GET["checkList"])) {
            foreach ($_GET['checkList'] as $selected) {
                $catId = "$selected";
                echo $catId;
                ?>
                <form method="get" action="categories.php">
                    Ändra kategori: <input type="text" name="categoryChange">
                    <input type="hidden" name="catId" value="<?php echo $catId ?>">
                    <input type="submit" value="Ändra" name="changeCat2">
                </form>
                <?php
            }
        } 
    }

    if (isset ($_GET["changeCat2"])) {
        if (!empty($_GET["categoryChange"])) {
            $category = $_GET["categoryChange"];
            $catId = $_GET["catId"];
            $query = "UPDATE categories SET category= '$category' WHERE id=$catId";
            if ($stmt -> prepare($query)) {
                $stmt->execute();
            } else {
                echo "fel";
            }
        }
    }
?>

<h1>Kategorier</h1>

<?php

// $query = "SELECT * FROM posts LEFT JOIN users ON posts.userid = users.id";
$query = "SELECT * FROM categories";
if ($stmt -> prepare($query)) {
    $stmt-> execute();
    $stmt -> bind_result($catId, $cat);
?>
    <form method="get" action="categories.php">
<?php
    while (mysqli_stmt_fetch($stmt)) {
        ?>
        <input type="checkbox" name="checkList[]" value="<?php echo $catId ?>"> <?php echo $cat; ?>
        <br>        
        <?php
    } // end while
    ?>
        <br>
        <input type="submit" value="Ta bort" name="removeCat">
        <input type="submit" value="Ändra" name="changeCat">
        <br>
    </form>
    <form method="get" action="categories.php">
        Lägg till kategori: <input type="text" name="category">
        <input type="submit" value="Lägg till" name="addCat">
    </form>
    <?php
    
}

?>
