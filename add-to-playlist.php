<?php
require('config/config.php');
$playlist_id = $_POST['playlist_id'];
$song_id = $_POST['song_id'];
// Involve some validation to stop the same song being added to the playlist twice
$sql = "SELECT playlist_id, song_id FROM playlist_assignment WHERE playlist_id = :playlist_id AND song_id = :song_id";
$stmt = $conn->prepare($sql);
// Create execute variable to be assigned the statement execute function
$execute = $stmt->execute([
  ':playlist_id' => $playlist_id,
  ':song_id' => $song_id
]);
// Condition to check if the prepared statement will provide something to the database that already exists
if ($stmt->rowCount() > 0) {
  siteAddNotification("error", "playlists", "The playlist already has that song");
  header("Location: playlists.php");
  exit();
} else {
  $sql = "INSERT INTO playlist_assignment (playlist_id, song_id) VALUES (:playlist_id, :song_id)";
  $stmt = $conn->prepare($sql);
  $execute = $stmt->execute([
    ':playlist_id' => $playlist_id,
    ':song_id' => $song_id
  ]);
  if ($execute) {
    siteAddNotification("success", "playlists", "Song added to playlist");
    header("Location: playlist.php?playlist=" . $playlist_id);
    exit();
  }
}
