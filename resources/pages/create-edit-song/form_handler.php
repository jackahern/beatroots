<?php
$action = $_POST['action'] ?? NULL;
$max_upload = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
$max_upload = str_replace('M', '', $max_upload);
$max_upload_msg = $max_upload . 'MB';
// Convert MB to bytes
$max_upload = $max_upload * 1000000;
$upload_ok = 1;
$target_dir = "songs";
$song_id = $_POST['song_id'];
$song_title = $_POST['song_title'];
$song_artist_id_with_name = $_POST['song_artist_id'];
$song_album_id_with_title = $_POST['song_album_id'];
$song_genre_id = $_POST['song_genre_id'];



if ($action == 'create-song' || $action == 'edit-song') {
  $exploded_artist_val = explode(" ", $song_artist_id_with_name);
  $song_artist_id = $exploded_artist_val[0];

  if (!empty($_POST['song_album_id'])) {
    $exploded_album_val = explode(" ", $song_album_id_with_title);
    $song_album_id = $exploded_album_val[0];
  } else {
    $song_album_id = NULL;
  }
  // Validate the image upload
  if (isset($_FILES['song']) && strlen(trim($_FILES['song']['tmp_name']))) {
    $target_file = $target_dir . basename($_FILES["song"]["name"]);
    $song_file_type = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
    if ($_FILES["song"]["size"] == 0) {
      $upload_ok = 0;
      siteAddNotification("error", "songs", "The file is not an MP3");
    }
    // Check file size - cannot exceed the size allocated on the server
    if ($_FILES["song"]["size"] > $max_upload) {
      $upload_ok = 0;
      siteAddNotification("error", "artists", "The file is too large");
    }
    // Allow certain file formats
    if($song_file_type != "mp3") {
      $upload_ok = 0;
      siteAddNotification("error", "songs", "The file is not an accepted file type, MP3 is needed");
    }
  }
  // If the upload has not worked and there are errors present, refresh the page and show the user the errors
  if ($upload_ok == 0) {
    siteAddNotification("error", "songs", "The file upload has not worked and therefore your song has not been saved");
    header('Location:' . $current_file);
    exit;
  }
  else if ($action == 'create-song') {
    // Involve some validation to stop the same card being created twice
    $sql = "SELECT song_title FROM songs WHERE song_title = :song_title";
    $stmt = $conn->prepare($sql);
    // Create execute variable to be assigned the statement execute function
    $execute = $stmt->execute([
      ':song_title' => $song_title
    ]);
    // Condition to check if the prepared statement will provide something to the database that already exists
    if ($stmt->rowCount() > 0) {
      siteAddNotification("error", "songs", "A song titled " . $song_title . " already exists");
      header('Location:' . $current_file);
      exit;
    } else {
      $conn->beginTransaction();
      $sql = "INSERT INTO songs (song_title, song_artist_id, song_album_id, song_genre_id) VALUES (:song_title, :song_artist_id, :song_album_id, :song_genre_id)";
      $stmt = $conn->prepare($sql);
      $execute = $stmt->execute([
        ':song_title' => $song_title,
        ':song_artist_id' => $song_artist_id,
        ':song_album_id' => $song_album_id,
        ':song_genre_id' => $song_genre_id
      ]);
      $id = $conn->lastInsertId();
      if ($execute) {
        $destination = $target_dir . "/" . $id . "." . $song_file_type;
        if (move_uploaded_file($_FILES["song"]["tmp_name"], $destination)) {
          $conn->commit();
          chmod($destination, 0755);
          siteAddNotification("success", "songs", "Song titled " . $song_title . " added");
          header("Location:" . $success_page);
          exit;
        } else {
          $conn->rollback();
          siteAddNotification("error", "songs", "Upload of song MP3 unsuccessful");
          header('Location:' . $current_file);
          exit;
        }
      }
    }
  }
  else if ($action == 'edit-song') {
    // Write an update query to change the card details that has been edited
    $sql = "UPDATE songs 
    SET song_title = :song_title,
    song_artist_id = :song_artist_id,
    song_album_id = :song_album_id,
    song_genre_id = :song_genre_id
    WHERE song_id = :song_id";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':song_title' => $song_title,
      ':song_artist_id' => $song_artist_id,
      ':song_album_id' => $song_album_id,
      ':song_genre_id' => $song_genre_id,
      ':song_id' => $song_id
    ]);
    // If the upload has worked, then check the existence of the image and move it to the destination if it has been changed
    if ($upload_ok) {
      $destination = $target_dir . "/" . $song_id . ".mp3";
      if (!empty($_FILES['song']['tmp_name'])) {
        move_uploaded_file($_FILES["song"]["tmp_name"], $destination);
        chmod($destination, 0755);
      }
      siteAddNotification("success", "songs", "The song has been updated");
      header("Location:" . $success_page);
      exit;
    } else {
      siteAddNotification("error", "songs", "Song could not be updated, file upload failed");
      header('Location:' . $current_file);
      exit;
    }
  }
}
else if ($action == 'delete-song' && isset($_POST['song_id'])) {
  $conn->beginTransaction();
  $sql = "DELETE FROM songs WHERE song_id = :song_id";
  $stmt = $conn->prepare($sql);
  $execute = $stmt->execute([
    ':song_id' => $_POST['song_id']
  ]);
  $song = $_POST['song_id'] . ".mp3";
  if (unlink( 'songs/' . $song) || !file_exists('songs/' . $song)) {
    $conn->commit();
    siteAddNotification("success", "songs", "The song has been deleted");
    header("Location:" . $current_file);
    exit();
  }
  else {
    $conn->rollback();
    siteAddNotification("error", "songs", "The audio file could not be deleted, therefore the song was stopped from being deleted");
    header("Location:" . $current_file);
    exit();
  }
}