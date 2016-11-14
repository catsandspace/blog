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
            echo "button pressed";
            if (!empty($_GET["checkList"])) {
                echo "checked";
                foreach ($_GET['checkList'] as $selected) {
                    $catId = $selected;
                    echo $selected;
                    ?>
                    <form method="get" action="categories.php">
                        Ändra kategori: <input type="text" name="categoryChange">
                        <input type="submit" value="Ändra" name="changeCat">
                    </form>
                    <?php
                    $category = $_GET["categoryChange"];
                    $query = "UPDATE categories SET category= $category WHERE id=$catId";
                    if ($stmt -> prepare($query)) {
                        $stmt->execute();
                    } else {
                        echo "fel";
                    }
                }
            }
        }
?>

<h1>användare</h1>

<?php

// $query = "SELECT * FROM posts LEFT JOIN users ON posts.userid = users.id";
$query = "SELECT * FROM users";
if ($stmt -> prepare($query)) {
    $stmt-> execute();
    $stmt -> bind_result($userId, $permission, $uName, $uPass, $uMail, $uWebSite, $ufName, $ulName, $uPic, $uDesc);
?>
    <form method="get" action="categories.php">
<?php
    while (mysqli_stmt_fetch($stmt)) {
        ?>
        <input type="checkbox" name="checkList[]" value="<?php echo $catId ?>"> <?php echo $uName; ?>
        <br>        
        <?php
    } // end while
    ?>
        <br>
        <input type="submit" value="Ta bort" name="removeUser">
        <br>
    </form>
    <form method="get" action="categories.php">
        Lägg till användare: <input type="text" name="category">
        <input type="submit" value="Lägg till" name="addUser">
    </form>
    <?php
    
}

?>