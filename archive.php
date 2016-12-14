<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    $query = "SELECT * FROM posts WHERE published = 1 ORDER BY created DESC";

    //Determine if a variable is set and is not NULL
     $sort = "";
     $month = "";
     /*******************************************************************************
        if statement that sorts and diplay post by month, lastest created, last
        created and by name
     ********************************************************************************/


    /********************************************************************
                    START OF MONTH LOOP
    ********************************************************************/

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
    }

    $months = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($months, array(
            "name" => date("F", strtotime($created)),
            "number" => date("n", strtotime($created))
        ));
    }

    /********************************************************************
                    END OF MONTH LOOP
    ********************************************************************/




    /********************************************************************
                    START OF POST LOOP
    ********************************************************************/

    if(isset($_GET["sort"]) ) {
        $sort = $_GET["sort"];
        $month = $_GET["month"];

        // Sort post by lastest entry
        if($sort == "asc") {
            if($month == "all") {
                $query = "SELECT * FROM posts WHERE published = 1 ORDER BY created ASC";
            } else {
                $query = "SELECT * FROM posts WHERE published = 1 AND EXTRACT(MONTH FROM created) = {$month} ORDER BY created ASC";
            }
        }
        // Sort post by the last one
        if($sort == "desc") {
            if($month == "all") {
                $query = "SELECT * FROM posts WHERE published = 1 ORDER BY created DESC";
            } else {
                $query = "SELECT * FROM posts WHERE published = 1 AND EXTRACT(MONTH FROM created) = {$month} ORDER BY created DESC";
            }
        }
        // Sort post by name
        if($sort == "name") {
            if($month == "all") {
                $query = "SELECT * FROM posts WHERE published = 1 ORDER BY title ASC";
            } else {
                $query = "SELECT * FROM posts WHERE published = 1 AND EXTRACT(MONTH FROM created) = {$month} ORDER BY title ASC";
            }
        }
    } else {
        $query = "SELECT * FROM posts WHERE published = 1 ORDER BY created DESC";
    }

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
    }

    $posts = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($posts, array(
            "id" => $id,
            "created" => $created,
            "title" => $title
        ));
    }

    /********************************************************************
                    END OF POST LOOP
    ********************************************************************/



    /********************************************************************
                    Start of page headline info
    ********************************************************************/

    $headLine = "Alla inlägg";

    if(isset($_GET["month"])) {
    $monthTitle = $_GET["month"];

    }

    if(isset($monthTitle) && $monthTitle <= 12 && $monthTitle >= 1) {
        $headLine = date('F', mktime(0, 0, 0, $_GET["month"], 10));

    } else {
        $headLine = "Alla inlägg";
        //header("Location: ./archive.php");
    }
?>
<main>
    <h1 class="margin-bottom-l">Arkiv</h1>
    <form method="GET" action="archive.php">
        <label for="sort">Sortera arkivet</label>
        <div class="select-arrows">
        <select class="form-field form-field__select" name="month" id="sort">
            <option value="all">Alla</option>

            <?php for ($i=0; $i < count($months); $i++):
                $month = $months[$i];
                $selectedAttribute = "";
                if(isset($_GET["month"]) && $_GET["month"] == $actualMonth[0]) {
                    $selectedAttribute = "selected";
                }
            ?>

             <option value="<?php echo $month["number"]; ?>" <?php echo $selectedAttribute; ?>><?php echo $month["name"]; ?></option>
            <?php endfor; ?>
        </select>
        <select class="form-field form-field__select" name="sort" id="sort">
            <?php
                $selected = "";
                if (isset($_GET["sort"])) {
                    $selected = $_GET["sort"];
                }
            ?>
          <option value="desc" <?php if ($selected == "desc") { echo "selected"; } ?> >Senast publicerad</option>
          <option value="asc" <?php if ($selected == "asc") { echo "selected"; } ?> >Tidigast publicerad</option>
          <option value="name" <?php if ($selected == "name") { echo "selected"; } ?> >Sortera efter bokstavsordning (A-Z)</option>
        </select>
          <button class="button button--small border-radius margin-bottom-l" type="submit">Sortera</button>
        </div>
    </form>
    <div class="list-wrapper">
        <h1><?php echo $headLine; ?></h1>
        <p><?php //echo $totalNumberOfMonthPosts; ?></p> <!-- STÄDA BORT SÅ FORT DET FUNKAR -->
        <ul class="no-padding">

        <?php for ($i=0; $i < count($posts); $i++): $post = $posts[$i]; ?>
            <li class="list-style-none"><span class="saffron-text primary-brand-font">[<?php echo formatDate($post["created"]); ?>]</span><a href="post.php?getpost=<?php echo $post["id"] ?>"><?php echo $post["title"]; ?></a></li>
        <?php endfor; ?>



        </ul>

    </div>
</main>
<?php //if($errorMessage) {
    //echo $errorMessage;
//}
?>
<?php require_once "./templates/footer.php"; ?>
