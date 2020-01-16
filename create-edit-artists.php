<?php
session_start();
$currentFile = 'create-edit-artist.php';
$maxUpload = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$maxUpload = str_replace('M', '', $maxUpload);
$maxUploadMsg = $maxUpload . 'MB';
$maxUpload = $maxUpload * 2048;
$uploadOk = 1;
$target_dir = "images/artists";

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = false;
$_SESSION['page_title'] = $isEdit ? 'Edit artist' : 'Create artist';
$_SESSION['page_description'] = 'Here you can create a new artist, all this requires is a name!';
include_once('header.php');

// handle form input here
if (isset($_POST['artist_name'])) {
  // Validate the image upload
  if (isset($_FILES['artist_avatar']) && strlen(trim($_FILES['album_cover']['tmp_name']))) {
    $target_file = $target_dir . basename($_FILES["album_cover"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if ($_FILES["artist_avatar"]["size"] == 0) {
      $uploadOk = 0;
      siteAddNotification("error", "artists", "The file is not an image");
    }
    // Check file size - cannot exceed 4MB
    if ($_FILES["artist_avatar"]["size"] > $maxUpload) {
      $uploadOk = 0;
      siteAddNotification("error", "artists", "The file is too large");
    }
    // Allow certain file formats
    if($imageFileType != "png" && $imageFileType != "svg") {
      $uploadOk = 0;
      siteAddNotification("error", "artists", "The file is not an accepted file type");
    }
  }
  // If the upload has not worked and there are errors present, refresh the page and show the user the errors
  if ($uploadOk == 0) {
    siteAddNotification("warning", "artists", "An artist avatar has not been saved for this artist. However, it can be added at a later date");
  }

  // Involve some validation to stop the same card being created twice
  $sql = "SELECT artist_name FROM artists WHERE artist_name = :artist_name";
  $stmt = $conn->prepare($sql);
  // Create execute variable to be assigned the statement execute function
  $execute = $stmt->execute([
    ':artist_name' => $_POST['artist_name']
  ]);
  // Condition to check if the prepared statement will provide something to the database that already exists
  if ($stmt->rowCount() > 0) {
    siteAddNotification("error", "artists", "An artist with the name " . $_POST['artist_name'] . " already exists");
  } else {
    $conn->beginTransaction();
    $sql = "INSERT INTO artists (artist_name) VALUES (:artist_name)";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':artist_name' => $_POST['artist_name']
    ]);
    $id = $conn->lastInsertId();
    if ($execute) {
      $destination = $target_dir . "/" . $id . "." . $imageFileType;
      if (move_uploaded_file($_FILES["artist_avatar"]["tmp_name"], $destination)) {
        $conn->commit();
        chmod($destination, 0755);
        siteAddNotification("success", "artists", "Artist called " . $_POST['artist_name'] . " added");
        unset($_POST['artist_name']);
      } else {
        $conn->rollback();
        siteAddNotification("error", "artists", "Upload of artist avatar unsuccessful");
      }
    }
  }


  // Insert as a transaction so it can be rolled back without putting mis-matched items into the database
  $sql = "INSERT INTO albums (album_title, album_genre_id, album_artist_id) VALUES (:album_title, :album_genre_id, :album_artist_id)";
  $stmt = $conn->prepare($sql);
  $execute = $stmt->execute([
    ':album_title' => $_POST['album_title'],
    ':album_genre_id' => $_POST['album_genre_id'],
    ':album_artist_id' => $artist_id
  ]);
  $id = $conn->lastInsertId();
  if ($execute) {
    // If the statement was executed into the database correctly, set a destination variable for the target file with the id
    // and move the file to the images directory
    //echo "<pre>";
    //var_dump($destination);
    //var_dump($_FILES);
  }



}
?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("artists");
    ?>
    <section class="create-edit-artist">
      <form action="create-edit-artists.php" method="post" enctype="multipart/form-data">
        <label for="artistName">Artist name</label>
        <input type="text" name="artist_name" class="form-control" aria-describedby="artistNameHelp" placeholder="Enter artist name...">
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');
