<?php
    require_once "./templates/header.php";
    require_once "./assets/functions.php";

    $query = "SELECT * FROM posts WHERE published = 1 ORDER BY EXTRACT(MONTH FROM created) ASC";

    $month = "";
    $sort = "";

/*******************************************************************************
    START OF MONTH ARRAY
*******************************************************************************/

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
    }

    $months = array();

    while (mysqli_stmt_fetch($stmt)) {
        // WINDOWS
        setlocale(LC_ALL, 'swedish');
        // MAC
        setlocale(LC_ALL, 'sv_SE');

        array_push($months, array(
            "name" => strftime("%B", strtotime($created)),
            "number" => date("n", strtotime($created))
        ));
    }

/*******************************************************************************
    START OF POST ARRAY
*******************************************************************************/

    if (isset($_GET["sort"]) ) {

        $month = $_GET["month"];
        $sort = $_GET["sort"];

        if ($sort == "asc") {
            if ($month == "all") {
                $query = "SELECT * FROM posts WHERE published = 1 ORDER BY created ASC";

            } else {
                $query = "SELECT * FROM posts WHERE published = 1 AND EXTRACT(MONTH FROM created) = {$month} ORDER BY created ASC";
            }
        }

        if ($sort == "desc") {
            if ($month == "all") {
                $query = "SELECT * FROM posts WHERE published = 1 ORDER BY created DESC";

            } else {
                $query = "SELECT * FROM posts WHERE published = 1 AND EXTRACT(MONTH FROM created) = {$month} ORDER BY created DESC";
            }
        }

        if ($sort == "name") {
            if ($month == "all") {
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

/*******************************************************************************
    SET HEADLINE
*******************************************************************************/

    if (isset($_GET["month"])) {
        $monthTitle = $_GET["month"];
    }

    if (isset($monthTitle) && $monthTitle <= 12 && $monthTitle >= 1) {
        $headLine = strftime("%B", strtotime($created));

    } else {
        $headLine = "Alla inlägg";
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
                if (isset($_GET["month"]) && $_GET["month"] == $month["number"]) {
                    $selectedAttribute = "selected";
                }
                // Takes existing array and returns a new array without duplicate values.
                $months = uniqueArray($months,'number');
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
        <h2><?php echo ucfirst($headLine); ?></h2>
        <ul class="no-padding">
        <?php for ($i=0; $i < count($posts); $i++): $post = $posts[$i]; ?>
            <li class="list-style-none"><span class="archive__date">[ <?php echo formatDate($post["created"]); ?> ]</span> <a class="archive__link" href="post.php?getpost=<?php echo $post["id"] ?>"><?php echo $post["title"]; ?></a></li>
        <?php endfor; ?>
        </ul>
    </div>
</main>
<?php require_once "./templates/footer.php"; ?>
