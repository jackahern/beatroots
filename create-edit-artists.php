<?php
session_start();
$action = $_POST['action'] ?? NULL;
$currentFile = 'create-edit-artist.php';
$maxUpload = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$maxUpload = str_replace('M', '', $maxUpload);
$maxUploadMsg = $maxUpload . 'MB';
$maxUpload = $maxUpload * 2048;
$uploadOk = 1;
$target_dir = "images/artists";

// handle form input here
if ($action == 'create-artist' || $action == 'edit-artist') {
  // Validate the image upload
  if (isset($_FILES['artist_avatar']) && strlen(trim($_FILES['artist_avatar']['tmp_name']))) {
    $target_file = $target_dir . basename($_FILES["artist_avatar"]["name"]);
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
  else if ($action == 'create-artist') {
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
  }
}

$_SESSION['page_title'] = $isEdit ? 'Edit artist' : 'Create artist';
$_SESSION['page_description'] = 'Here you can create a new artist, all this requires is a name!';
include_once('header.php');

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = isset($_GET['artist_id']) ? true : false;
if ($isEdit) {
  // get artist details for this artist
  $artist = getArtistById($_GET['artist_id']);
  // if card doesn't exist for the supplied card_id, then kick out back to list page and show error
  if ($artist == NULL) {
    //First create the redirect to use query string until i see it working and then change it to use the errors the same way it does in the form handler
    siteAddNotification("error", "artists", "The artist doesn't exist");
  }
}


$imgUrl = 'images/artists/' . $artist['artist_id'].'.png';
?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("artists");
    ?>
    <section class="create-edit-artist">
      <form action="create-edit-artists.php" method="post" enctype="multipart/form-data">
        <label for="artistName">Artist name</label>
        <input type="text" name="artist_name" class="form-control" aria-describedby="artistNameHelp" placeholder="Enter artist name..." value="<?=$artist['artist_name'] ?? $_GET['name'] ?? '' ?>">
        <label for="artistAvatar"><?=$isEdit ? 'Replace avatar' : 'Artist avatar' ?></label>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="customFile" name="artist_avatar">
          <label class="custom-file-label" for="customFile">Choose file...</label>
        </div>
        <?php
        // If isEdit is true then show the thumbnail image that is already being used for the card
        if ($isEdit && file_exists($imgUrl)) {
          ?>
            <label for="existingAvatar">Current Avatar</label>
            <img class="edit-thumbnail-img" src="<?=$imgUrl . '?nc=' . filemtime($imgUrl)?>">
          <?php
        }
        ?>
        <div class="form-group">
          <?php
          if ($isEdit) {
            ?>
              <input type="hidden" value="<?=$artist['artist_id']?>" name="artist_id"/>
              <input type="hidden" value="edit-artist" name="action"/>
            <?php
          }
          else {
            ?>
              <input type="hidden" value="create-artist" name="action"/>
            <?php
          }
          ?>
        </div>
        <button type="submit" class="btn btn-primary mt-3" name="create-edit-artist-submit"><?=$isEdit ? 'Update' : 'Submit'?></button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');
