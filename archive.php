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

    $months = array();
    while (mysqli_stmt_fetch($stmt)) {
        array_push($months, array(
            //"id" => $id,
            //"userid" => $userId,
            "name" => date("F", strtotime($created)),
            "number" => date("n", strtotime($created))
            //"updated" => $updated,
            //"image" => $image,
            //"title" => $title,
            //"content" => $content,
            //"published" => $published,
            //"categoryid" => $categoryId
        ));
    }
    //var_dump($months);
    //$months = array_map("unserialize", array_unique(array_map("serialize", $months)));

    for ($i=0; $i < count($months); $i++):
        $month = $months[$i];
        print_r(array_unique($month));
        //echo $month["name"]." ".$month["number"]."<br>";
    endfor;

    // Array that contains months and number for sorting posts

    // $month = array(
    //     array("Januari", "01"),
    //     array("Februari", "02"),
    //     array("Mars", "03"),
    //     array("April", "04"),
    //     array("Maj", "05"),
    //     array("Juni", "06"),
    //     array("Juli", "07"),
    //     array("Augusti", "08"),
    //     array("September", "09"),
    //     array("Oktober", "10"),
    //     array("November", "11"),
    //     array("December", "12")
    //     );

    /********************************************************************
                    Start of page headline info
    ********************************************************************/
    // $headLine = "Alla inlägg";
    // if(isset($_GET["month"])) {
    //
    //     foreach($month as $actualMonth) {
    //
    //         if ($actualMonth[1] == $_GET["month"]) {
    //             $headLine = $actualMonth[0];
    //         }
    //     }
    // }
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
        <?php while (mysqli_stmt_fetch($stmt)): ?>
            <li class="list-style-none"><span class="saffron-text primary-brand-font">[<?php echo formatDate($created); ?>]</span><a href="post.php?getpost=<?php echo $id ?>"><?php echo $title; ?></a></li>
        <?php endwhile; ?>
        </ul>

    </div>
</main>
<?php if($errorMessage) {
    echo $errorMessage;
}
?>
<?php require_once "./templates/footer.php"; ?>
