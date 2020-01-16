<?php
session_start();
$currentFile = 'create-edit-album.php';
$maxUpload = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$maxUpload = str_replace('M', '', $maxUpload);
$maxUploadMsg = $maxUpload . 'MB';
$maxUpload = $maxUpload * 2048;
$uploadOk = 1;
$target_dir = "images/albums";

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = false;
$_SESSION['page_title'] = $isEdit ? 'Edit album' : 'Create album';
$_SESSION['page_description'] = 'Here you can create a new album. For this, you need to know the title of the album, the artist who authored it and the genre the album suits';
include_once('header.php');

// handle form input here
if (isset($_POST['album_title']) && isset($_POST['album_artist_id']) && isset($_POST['album_genre_id'])) {
  // To get the correct value for the artist id, we need to explode the post array and take out the first item in the array
  $exploded_artist_val = explode(" ", $_POST['album_artist_id']);
  $artist_id = $exploded_artist_val[0];

  // Validate the image upload
  if (isset($_FILES['album_cover']) && strlen(trim($_FILES['album_cover']['tmp_name']))) {
    $target_file = $target_dir . basename($_FILES["album_cover"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if ($_FILES["album_cover"]["size"] == 0) {
      $uploadOk = 0;
      siteAddNotification("error", "albums", "The file is not an image");
    }
    // Check file size - cannot exceed 4MB
    if ($_FILES["album_cover"]["size"] > $maxUpload) {
      $uploadOk = 0;
      siteAddNotification("error", "albums", "The file is too large");
    }
    // Allow certain file formats
    if($imageFileType != "png" && $imageFileType != "svg") {
      $uploadOk = 0;
      siteAddNotification("error", "albums", "The file is not an accepted file type");
    }
  }
  // If the upload has not worked and there are errors present, refresh the page and show the user the errors
  if ($uploadOk == 0) {
    siteAddNotification("warning", "albums", "An album cover photo has not been saved with this album. However, it can be added at a later date");
  }

  // Involve some validation to stop the same card being created twice
  $sql = "SELECT album_title FROM albums WHERE album_title = :album_title";
  $stmt = $conn->prepare($sql);
  // Create execute variable to be assigned the statement execute function
  $execute = $stmt->execute([
    ':album_title' => $_POST['album_title']
  ]);
  // Condition to check if the prepared statement will provide something to the database that already exists
  if ($stmt->rowCount() > 0) {
    siteAddNotification("error", "albums", "An album with the name " . $_POST['album_title'] . " already exists");
  } else {
    // Insert as a transaction so it can be rolled back without putting mis-matched items into the database
    $conn->beginTransaction();
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
      $destination = $target_dir . "/" . $id . "." . $imageFileType;
      //echo "<pre>";
      //var_dump($destination);
      //var_dump($_FILES);
      if (move_uploaded_file($_FILES["album_cover"]["tmp_name"], $destination)) {
        $conn->commit();
        siteAddNotification("success", "albums", "Album titled " . $_POST['album_title'] . " added");
        unset($_POST['album_title']);
        unset($_POST['album_artist_id']);
        unset($_POST['album_genre_id']);
      } else {
        $conn->rollback();
        siteAddNotification("error", "albums", "Upload of album cover unsuccessful");
      }
    }
  }
}

$artists = getArtists();
$genres = getGenres();
?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("albums");
    ?>
    <section class="create-edit-album">
      <form action="create-edit-album.php" method="post" enctype="multipart/form-data">
        <label for="albumTitle">Album title:</label>
        <input type="text" name="album_title" class="form-control" aria-describedby="albumTitleHelp" placeholder="Enter album title...">
        <label>By artist:</label>
        <input list="artists" name="album_artist_id" placeholder="Search for artist..." class="form-control">
        <datalist id="artists">
          <?php
          foreach ($artists as $artist) {
            ?>
            <option value="<?=$artist['artist_id'] . ' - ' . $artist['artist_name'];?>"><?=$artist['artist_name']?></option>
            <?php
          }
          ?>
        </datalist>
        </input>
        <label>Genre:</label>
        <select id="select-genre" name="album_genre_id" class="custom-select my-1 mr-sm-2">
          <option value="0">Please select...</option>
          <?php
          foreach ($genres as $genre) {
            ?>
            <option value="<?=$genre['genre_id'];?>"><?=$genre['genre_name']?></option>
            <?php
          }
          ?>
        </select>
        <label for="albumCover">Album cover photo</label>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="customFile" name="album_cover">
          <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');
