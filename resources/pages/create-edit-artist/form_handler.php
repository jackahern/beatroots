<?php
$action = $_POST['action'] ?? NULL;
$upload_ok = 1;
$artist_id = $_POST['artist_id'];
$artist_name = $_POST['artist_name'];
$target_dir = "images/artists";

// handle form input here
if ($action == 'create-artist' || $action == 'edit-artist') {
  // Validate the image upload
  if (isset($_FILES['artist_avatar']) && strlen(trim($_FILES['artist_avatar']['tmp_name']))) {
    $target_file = $target_dir . basename($_FILES["artist_avatar"]["name"]);
    $image_file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if ($_FILES["artist_avatar"]["size"] == 0) {
      $upload_ok = 0;
      siteAddNotification("error", "artists", "The file is not an image");
    }
    // Allow certain file formats
    if($image_file_type != "png") {
      $upload_ok = 0;
      siteAddNotification("error", "artists", "The file is not an accepted file type");
    }
  }
  // If the upload has not worked and there are errors present, refresh the page and show the user the errors
  if ($upload_ok == 0) {
    siteAddNotification("warning", "artists", "An artist avatar has not been saved for this artist. However, it can be added at a later date");
  }
  if ($action == 'create-artist') {
    // Involve some validation to stop the same card being created twice
    $sql = "SELECT artist_name FROM artists WHERE artist_name = :artist_name";
    $stmt = $conn->prepare($sql);
    // Create execute variable to be assigned the statement execute function
    $execute = $stmt->execute([
      ':artist_name' => $artist_name
    ]);
    if (!$execute) {
      siteAddNotification("error", "artists", "Unsuccessful connection with the database when trying to compare with existing artists");
      header("Location:" . $current_file );
      exit();
    }
    // Condition to check if the prepared statement will provide something to the database that already exists
    if ($stmt->rowCount() > 0) {
      siteAddNotification("error", "artists", "An artist with the name " . $artist_name . " already exists");
      header("Location:" . $current_file);
      exit();
    } else {
      $conn->beginTransaction();
      $sql = "INSERT INTO artists (artist_name) VALUES (:artist_name)";
      $stmt = $conn->prepare($sql);
      $execute = $stmt->execute([
        ':artist_name' => $artist_name
      ]);
      $id = $conn->lastInsertId();
      if ($execute) {
        $destination = $target_dir . "/" . $id . "." . $image_file_type;
        if (move_uploaded_file($_FILES["artist_avatar"]["tmp_name"], $destination)) {
          $conn->commit();
          chmod($destination, 0755);
          siteAddNotification("success", "artists", "Artist called " . $artist_name . " added");
          header("Location:" . $success_page);
          exit();
        } else {
          $conn->rollback();
          siteAddNotification("error", "artists", "Upload of artist avatar unsuccessful");
          header("Location:" . $current_file . '?' . http_build_query($post_data_args));
          exit();
        }
      }
    }
  }
  else if ($action == 'edit-artist') {
    // Write an update query to change the card details that has been edited
    $sql = "UPDATE artists 
    SET artist_name = :artist_name
    WHERE artist_id = :artist_id";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':artist_name' => $artist_name,
      ':artist_id' => $artist_id
    ]);
    // If the upload has worked, then check the existence of the image and move it to the destination if it has been changed
    if ($upload_ok) {
      $destination = $target_dir . "/" . $artist_id . ".png";
      if (!empty($_FILES['artist_avatar']['tmp_name'])) {
        move_uploaded_file($_FILES["artist_avatar"]["tmp_name"], $destination);
        chmod($destination, 0755);
      }
      siteAddNotification("success", "artists", "The artist has been updated");
    }
    header("Location:" . $success_page);
    exit();
  }
}

// Else if the delete button has been pressed on the artist, delete the artist from the database
// This will then in turn, remove the artist from the front end view to the user
else if ($action == 'delete-artist' && isset($_POST['artist_id'])) {
  // Put the sql into a transaction, by doing this, if the image deletion fails then the artist will not be deleted without the image
  $conn->beginTransaction();
  $sql = "DELETE FROM artists WHERE artist_id = :artist_id";
  $stmt = $conn->prepare($sql);
  $stmt->execute([
    ':artist_id' => $artist_id
  ]);
  $img = $artist_id . ".png";
  if (unlink( 'images/artists/' . $img) || !file_exists('images/artists/' . $img)) {
    $conn->commit();
    siteAddNotification("success", "artists", "The artist has been deleted");
    header("Location:" . $current_file);
    exit();
  }
  else {
    $conn->rollback();
    siteAddNotification("error", "artists", "The artist avatar could not be deleted, therefore the artist was stopped from being deleted");
    header("Location:" . $current_file);
    exit();
  }
}
