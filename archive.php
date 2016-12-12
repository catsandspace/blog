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

    // TODO: Get the styling right on buttons, select and svg. Unclear if this one is done or not

    // Array that contains months and number for sorting posts
    $month = array(
        array("Januari", "01"),
        array("Februari", "02"),
        array("Mars", "03"),
        array("April", "04"),
        array("Maj", "05"),
        array("Juni", "06"),
        array("Juli", "07"),
        array("augusti", "08"),
        array("September", "09"),
        array("Oktober", "10"),
        array("November", "11"),
        array("December", "12")
        );

?>
<?php

    $totalNumberOfMonthPosts = NULL;
    $errorMessage = NULL;

    // $query = "SELECT comments.* FROM comments LEFT JOIN posts ON comments.postid = posts.id";

    if ($stmt->prepare($query)) {
        $stmt->execute();
        $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
        $stmt->store_result();
    } else {
        $errorMessage = "Något gick fel.";
    }

    while (mysqli_stmt_fetch($stmt)) {

            $totalNumberOfMonthPosts++;
    }

?>
<main>
    <h1 class="margin-bottom-l">Arkiv</h1>
    <form method="GET" action="archive.php">
        <label for="sort">Sortera arkivet</label>
        <div class="select-arrows">
        <select class="form-field form-field__select" name="month" id="sort">
            <option value="all">Alla</option>
            <?php foreach($month as $actualMonth): ?>
             <option value="<?php echo $actualMonth[1]; ?>"><?php echo $actualMonth[0]; echo $totalNumberOfMonthPosts; ?></option>
            <?php endforeach; ?>
        </select>
          <select class="form-field form-field__select" name="sort" id="sort">
              <option value="desc">Senast publicerad</option>
              <option value="asc">Tidigast publicerad</option>
              <option value="name">Sortera efter bokstavsordning (A-Z)</option>
          </select>
          <button class="button button--small border-radius margin-bottom-l" type="submit">Sortera</button>
          <!-- <svg class="icon select-arrows">
            <use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#select-arrows"></use>
          </svg> -->
        </div>
    </form>

    <div class="list-wrapper">
        <!-- TODO: Fix so that correct months displays with actual posts -->
        <h2><?php echo $actualMonth[0];?></h2>

        <!-- STÄDA BORT SÅ FORT DET FUNKAR -->
    <!-- <?php
        // $totalNumberOfComments = 0;
        // $errorMessage = NULL;

        // $query = "SELECT comments.* FROM comments LEFT JOIN posts ON comments.postid = posts.id";

        // if ($stmt->prepare($query)) {
        //     $stmt->execute();
        //     $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
        //     $stmt->store_result();
        // } else {
        //     $errorMessage = "Något gick fel.";
        // }

        // while (mysqli_stmt_fetch($stmt)) {

        //         $totalNumberOfComments++;
        //     }

    ?>-->
        <p><?php echo $totalNumberOfMonthPosts; ?></p> <!-- STÄDA BORT SÅ FORT DET FUNKAR -->
        <ul class="no-padding">
        <?php while (mysqli_stmt_fetch($stmt)): ?>
            <li class="list-style-none"><span class="saffron-text primary-brand-font">[<?php echo formatDate($created); ?>]</span><a href="post.php?getpost=<?php echo $id ?>"><?php echo $title; ?></a></li>
        <?php endwhile; ?>
        </ul>
    </div>
</main>
<?php require_once "./templates/footer.php"; ?>
