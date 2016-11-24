<?php 
    require_once "./templates/header.php";

    //Remove these just to avoid header from hiding errors.
    require_once __DIR__."./assets/db_connect.php";
    require_once __DIR__."./assets/session.php";

    $post = array(
        "id" => "",
        "userid" => "",
        "updated" => "",
        "image" => "",
        "title" => "",
        "content" => "",
        "categoryid" => "",
        //"categoryname" => ""
    );


/*******************************************************************************
   GET SELECTED POST WHERE ID = post.php?getpost[id]
*******************************************************************************/

    if (isset($_GET['getpost'])) {

        $getPost = $_GET['getpost'];

        //$query  = "SELECT posts.*, categories.name FROM posts LEFT JOIN categories ON posts.categoryid = categories.id WHERE published = 1 AND id = '{$getPost}'";

        // ^ Får inte $getPost att lira när jag kör LEFT JOIN WHHHYYYY!? ^

        $query = "SELECT * FROM posts WHERE id = '{$getPost}' AND published = 1";

            if ($stmt->prepare($query)) {
            $stmt->execute();
            $stmt->bind_result($id, $userId, $created, $updated, $image, $title, $content, $published, $categoryId);
            $stmt->fetch();
            $stmt->close();

            $post["id"] = $id;
            $post["userid"] = $userId;
            $post["updated"] = $updated;
            $post["image"] = $image;
            $post["title"] = $title;
            $post["content"] = $content;
            $post["categoryid"] = $categoryId;
            //$post["categoryname"] = $categoryName;

            var_dump($post);

            } else {
                $errorMessage = "Något gick fel.";
            }

    }

/*******************************************************************************
   ERROR MESSAGE
*******************************************************************************/

    if ($id == NULL) {
        $errorMessage = "Vi hittade inget inlägg med id: $getPost (visa 404-sidan?)";
    }


/*******************************************************************************
   START OF HTML
*******************************************************************************/
?>
<?php if ($id != NULL): ?>
<article class="list">
        <div class="blogpost-wrapper">
            <img src="<?php echo $post["image"]; ?>" alt="<?php echo $post["title"]; ?>">
            <div class="blogpost-wrapper__text">
                <h2><?php echo $post["title"]; ?></h2>
                <p class="tag">Tags: <a href="index.php?display=<?php echo $post["categoryid"] ?>"><?php echo str_replace(' ', '', $post["categoryid"]); ?></a> </p>
                <p><?php echo $post["content"]; ?></p>
            </div>
        </div>           
</article>

<form>

</form>
<?php else: echo "<p class='error-msg'>".$errorMessage."</p>"; endif; ?>