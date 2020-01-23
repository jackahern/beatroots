<?php
// Get the posted action, to decipher what is done with the POST data
$action = $_POST['action'] ?? NULL;
// Get the max_upload allowed from the server, otherwise known as being in the php.ini file of the server
$max_upload = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$max_upload = str_replace('M', '', $max_upload);
$max_upload_msg = $max_upload . 'MB';
// Convert MB to bytes
$max_upload = $max_upload * 1000000;
$upload_ok = 1;
// Save all POST variables into non-post variables for better readability
$album_id = $_POST['album_id'];
$album_title = $_POST['album_title'];
$album_cover = $_POST['album_cover'];
$album_genre_id = $_POST['album_genre_id'];
$album_artist_id_with_name = $_POST['album_artist_id'];
// To get the correct value for the artist id, we need to explode the post array and take out the first item in the array
$exploded_artist_val = explode(" ", $album_artist_id_with_name);
$album_artist_id = $exploded_artist_val[0];

// Keep the data when validating and submitting form
$redirect_url = $current_file . "?album=" . $album_id;
$post_data_args = [
  'title' => $album_title,
  'genre_id' => $album_genre_id,
  'artist_id' => $album_artist_id_with_name
];
// Used for uploading and moving images
$target_dir = "images/albums";

if ($action == 'create-album' || $action == 'edit-album') {
  // Prepare and validate an image upload for the album cover photo
  if (isset($_FILES['album_cover']) && strlen(trim($_FILES['album_cover']['tmp_name']))) {
    $target_file = $target_dir . basename($_FILES["album_cover"]["name"]);
    $image_file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if ($_FILES["album_cover"]["size"] == 0) {
      $upload_ok = 0;
      siteAddNotification("error", "albums", "The file is not an image");
    }
    // Check file size - cannot exceed the max_upload_size in server config, this is future proof for
    // the application being hosted on different servers that vary in max_upload_size config
    if ($_FILES["album_cover"]["size"] > $max_upload) {
      $upload_ok = 0;
      siteAddNotification("error", "albums", "The file is too large");
    }
    // Currently, we only want png
    if($image_file_type != "png") {
      $upload_ok = 0;
      siteAddNotification("error", "albums", "The file is not an accepted file type");
    }
  }
  // If the upload has not worked, do not exit the form handler because an upload is not essential
  // Carry on but inform the user that the image will not be saved with the album
  if ($upload_ok == 0) {
    siteAddNotification("warning", "albums", "An album cover photo has not been saved with this album. However, it can be added at a later date");
  }
  // Perform specific 'create-album' tasks now, rather than both create and edit
  if ($action == 'create-album') {
    // Involve some validation to stop the same album being created twice
    $sql = "SELECT album_title FROM albums WHERE album_title = :album_title";
    $stmt = $conn->prepare($sql);
    // Create execute variable to be assigned the statement execute function
    $execute = $stmt->execute([
      ':album_title' => $album_title
    ]);
    // Use the execute variable for the if statement to check if the prepared statement was executed
    if (!$execute) {
      siteAddNotification("error", "albums", "Unsuccessful connection with the database when trying to compare with existing albums");
      header("Location:" . $current_file );
      exit();
    }
    // Condition to check if the prepared statement will provide something to the database that already exists
    if ($stmt->rowCount() > 0) {
      siteAddNotification("error", "albums", "An album with the name " . $album_title . " already exists");
      header("Location:" . $current_file);
      exit();
    } else {
      // Insert as a transaction so it can be rolled back without putting mis-matched items into the database
      $conn->beginTransaction();
      $sql = "INSERT INTO albums (album_title, album_genre_id, album_artist_id) VALUES (:album_title, :album_genre_id, :album_artist_id)";
      $stmt = $conn->prepare($sql);
      $execute = $stmt->execute([
        ':album_title' => $album_title,
        ':album_genre_id' => $album_genre_id,
        ':album_artist_id' => $album_artist_id
      ]);
      // Collect the ID of the album just added to use when pairing the album cover photo
      // We will use the ID of the album as the name of the image file
      $id = $conn->lastInsertId();
      if ($execute) {
        // If the statement was executed into the database correctly, set a destination variable for the target file with the id
        // and move the file to the images directory
        $destination = $target_dir . "/" . $id . "." . $image_file_type;
        if (move_uploaded_file($_FILES["album_cover"]["tmp_name"], $destination)) {
          // Everything including the image has been added successfully so commit the sql
          $conn->commit();
          chmod($destination, 0755);
          siteAddNotification("success", "albums", "Album titled " . $album_title . " added");
          header("Location:" . $success_page);
          exit();
        } else {
          // Something did not work with the image upload/moving of the file so take back the data from the database
          $conn->rollback();
          siteAddNotification("error", "albums", "Upload of album cover unsuccessful");
          header("Location:" . $current_file . '?' . http_build_query($post_data_args));
          exit();
        }
      }
    }
  }
  else if ($action == 'edit-album') {
    // Write an update query to change the album details that have been edited
    $sql = "UPDATE albums 
            SET album_title = :album_title,
            album_genre_id = :album_genre_id,
            album_aritst_id = :album_artist_id
            WHERE artist_id = :artist_id";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':album_title' => $album_title,
      ':album_genre_id' => $album_genre_id,
      ':album_artist_id' => $album_artist_id
    ]);
    // If the upload has worked, then check the existence of the image and move it to the destination if it has been changed
    if ($upload_ok) {
      $destination = $target_dir . "/" . $album_id . ".png";
      if (!empty($_FILES['album_cover']['tmp_name'])) {
        move_uploaded_file($_FILES["album_cover"]["tmp_name"], $destination);
        chmod($destination, 0755);
      }
      siteAddNotification("success", "albums", "The album has been updated");
    }
    header("Location:" . $success_page);
    exit();
  }
}
// Else if the delete button has been pressed on the album, delete the album from the database
// This will then in turn, remove the album from the front end view to the user
else if ($action == 'delete-album' && isset($_POST['album_id'])) {
  // Put the sql into a transaction, by doing this, if the image deletion fails then the album will not be deleted without the image
  $conn->beginTransaction();
  $sql = "DELETE FROM albums WHERE album_id = :album_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':album_id' => $album_id
  ]);
  $img = $album_id . ".png";
  // Make sure the image is also deleted so we get rid of everything to do with this album
  if (unlink( 'images/albums/' . $img) || !file_exists('images/albums/' . $img)) {
    $conn->commit();
    siteAddNotification("success", "albums", "The album has been deleted");
    header("Location:" . $current_file);
    exit();
  }
  else {
    $conn->rollback();
    siteAddNotification("error", "albums", "The album cover photo could not be deleted, therefore the album was stopped from being deleted");
    header("Location:" . $current_file);
    exit();
  }
}



