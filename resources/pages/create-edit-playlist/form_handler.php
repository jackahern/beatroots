<?php
$action = $_POST['action'] ?? NULL;
$playlist_id = $_POST['playlist_id'];
$playlist_name = $_POST['playlist_name'];

// handle form input here
if ($action == 'create-playlist') {
  // Involve some validation to stop the same card being created twice
  $sql = "SELECT playlist_name FROM playlists WHERE playlist_name = :playlist_name";
  $stmt = $conn->prepare($sql);
  // Create execute variable to be assigned the statement execute function
  $execute = $stmt->execute([
    ':playlist_name' => $playlist_name
  ]);
  // Condition to check if the prepared statement will provide something to the database that already exists
  if ($stmt->rowCount() > 0) {
    siteAddNotification("error", "playlists", "A playlist with the name " . $playlist_name . " already exists");
    header("Location:" . $current_file);
    exit();
  } else {
    $sql = "INSERT INTO playlists (playlist_name) VALUES (:playlist_name)";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':playlist_name' => $playlist_name
    ]);
    if ($execute) {
      siteAddNotification("success", "playlists", "Playlist named " . $playlist_name . " added");
      header("Location:" . $success_page);
      exit();
    }
  }
}
else if ($action == 'edit-playlist') {
  $sql = "UPDATE playlists
          SET playlist_name = :playlist_name
          WHERE playlist_id = :playlist_id";
  $stmt = $conn->prepare($sql);
  $execute = $stmt->execute([
    ':playlist_name' => $playlist_name,
    ':playlist_id' => $playlist_id
  ]);
  if ($execute) {
    siteAddNotification("success", "playlists", "Playlist has been updated");
    header("Location:" . $success_page);
    exit();
  } else {
    siteAddNotification("error", "playlists", "Playlist failed to update in the database");
    header("Location:" . $current_file);
    exit();
  }
}
else if($action == 'delete-playlist' && isset($_POST['playlist_id'])) {
  $conn->beginTransaction();
  $sql = "DELETE FROM playlists WHERE playlist_id = :playlist_id";
  $stmt = $conn->prepare($sql);
  $execute = $stmt->execute([
    ':playlist_id' => $playlist_id
  ]);
  if ($execute) {
    $sql = "DELETE FROM playlist_assignment WHERE playlist_id = :playlist_id";
    $stmt = $conn->prepare($sql);
    $execute_assignment = $stmt->execute([
      ':playlist_id' => $playlist_id
    ]);
    if ($execute_assignment) {
      $conn->commit();
      siteAddNotification("success" , "playlists", "Playlist deleted");
      header("Location:" . $success_page);
      exit();

    } else {
      $conn->rollBack();
      siteAddNotification("error" , "playlists", "The playlist could be deleted but the playlist assignment failed to delete from the database, therefore the playlist remains in the system");
      header("Location:" . $current_file);
      exit();
    }
  } else {
    $conn->rollBack();
    siteAddNotification("error" , "playlists", $playlist_name . " failed to delete from the database");
    header("Location:" . $current_file);
    exit();
  }
}