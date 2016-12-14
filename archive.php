<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    $query = "SELECT * FROM posts WHERE published = 1 ORDER BY EXTRACT(MONTH FROM created) ASC";

    //Determine if a variable is set and is not NULL
     $sort = "";
     $month = "";

    /********************************************************************
                    START OF MONTH LOOP
    ********************************************************************/

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
    }

    $months = array();

    //echo strftime("%A %d %B %Y");
    while (mysqli_stmt_fetch($stmt)) {
        setlocale(LC_ALL, 'sv_SE');
        array_push($months, array(
            //"name" => utf8_encode(strftime("%B", strtotime($created))),
            "name" => strftime("%B", strtotime($created)),
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
                    START OF PAGE HEADLINE INFO
    ********************************************************************/

    //$headLine = "Alla inlägg";

    if(isset($_GET["month"])) {
    $monthTitle = $_GET["month"];

    }

    if(isset($monthTitle) && $monthTitle <= 12 && $monthTitle >= 1) {
        $headLine = date('F', mktime(0, 0, 0, $_GET["month"], 10));

    } else {
        $headLine = "Alla inlägg";
    }

    /********************************************************************
                        END OF HEADLINE INFO
    ********************************************************************/
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

                    if(isset($_GET["month"]) && $_GET["month"] == $month["number"]) {
                        $selectedAttribute = "selected";
                    }
                    // Function that send mutliple values from array and returns one value
                    $months = uniqueArray($months,'number');
                ?>
                 <option value="<?php echo $month["number"]; ?>" <?php echo $selectedAttribute; ?>><?php echo $month["name"]; ?></option>
                <?php endfor; ?>
            </select>
            <select class="form-field form-field__select" name="sort" id="sort">
                <?php
                    // Makes sure that selected value in dropdown meny stays selected after reloading page
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
            <ul class="no-padding">
            <?php for ($i=0; $i < count($posts); $i++): $post = $posts[$i]; ?>
                <li class="list-style-none"><span class="saffron-text primary-brand-font">[<?php echo formatDate($post["created"]); ?>] </span><a href="post.php?getpost=<?php echo $post["id"] ?>"><?php echo $post["title"]; ?></a></li>
            <?php endfor; ?>
            </ul>
        </div>
    </main>
<?php require_once "./templates/footer.php"; ?>
