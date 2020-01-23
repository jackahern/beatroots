<?php
$action = $_POST['action'] ?? NULL;
$genre_id = $_POST['genre_id'];
$genre_name = $_POST['genre_name'];

// handle form input here
if ($action == 'create-genre') {
  // Involve some validation to stop the same genre being created twice
  $sql = "SELECT genre_name FROM genres WHERE genre_name = :genre_name";
  $stmt = $conn->prepare($sql);
  // Create execute variable to be assigned the statement execute function
  $execute = $stmt->execute([
    ':genre_name' => $genre_name
  ]);
  // Condition to check if the prepared statement will provide something to the database that already exists
  if ($stmt->rowCount() > 0) {
    siteAddNotification("error", "genres", "A genre with the name " . $genre_name . " already exists");
  } else {
    $sql = "INSERT INTO genres (genre_name) VALUES (:genre_name)";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':genre_name' => $genre_name
    ]);
    if ($execute) {
      siteAddNotification("success", "genres", "Genre of " . $genre_name . " added");
      header("Location:" . $success_page);
      exit();
    }
  }
}
else if ($action == 'edit-genre') {
  $sql = "UPDATE genres
          SET genre_name = :genre_name
          WHERE genre_id = :genre_id";
  $stmt = $conn->prepare($sql);
  $execute = $stmt->execute([
    ':genre_name' => $genre_name,
    ':genre_id' => $genre_id
  ]);
  if ($execute) {
    siteAddNotification("success", "genres", "Genre has been updated");
    header("Location:" . $success_page);
    exit();
  } else {
    siteAddNotification("error", "genres", "Genre failed to update in the database");
    header("Location:" . $current_file);
    exit();
  }
}
// Delete the genre
else if ($action == 'delete-genre' && isset($_POST['genre_id'])) {
  $sql = "DELETE FROM genres WHERE genre_id = :genre_id";
  $stmt = $conn->prepare($sql);
  $execute = $stmt->execute([
    ':genre_id' => $genre_id
  ]);
  if ($execute) {
    siteAddNotification("success" , "genres", "Genre deleted");
    header("Location:" . $current_file);
    exit();
  } else {
    siteAddNotification("error" , "genres", $genre_name . " failed to delete from the database");
    header("Location:" . $current_file);
    exit();
  }
}