<?php
session_start();
$currentFile = 'create-edit-playlist.php';

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = false;
$_SESSION['page_title'] = $isEdit ? 'Edit playlist' : 'Create playlist';
$_SESSION['page_description'] = 'Here you can create a new genre, all this requires is a name!';
include_once('header.php');

// handle form input here
if (isset($_POST['playlist_name'])) {
  // Involve some validation to stop the same card being created twice
  $sql = "SELECT playlist_name FROM playlists WHERE playlist_name = :playlist_name";
  $stmt = $conn->prepare($sql);
  // Create execute variable to be assigned the statement execute function
  $execute = $stmt->execute([
    ':playlist_name' => $_POST['playlist_name']
  ]);
  // Condition to check if the prepared statement will provide something to the database that already exists
  if ($stmt->rowCount() > 0) {
    siteAddNotification("error", "playlists", "A playlist with the name " . $_POST['playlist_name'] . " already exists");
  } else {
    $sql = "INSERT INTO playlists (playlist_name) VALUES (:playlist_name)";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':playlist_name' => $_POST['playlist_name']
    ]);
    if ($execute) {
      siteAddNotification("success", "playlists", "Genre of " . $_POST['playlist_name'] . " added");
      unset($_POST['playlist_name']);
    }
  }
}
?>
      <main>
      <?php
      outputNotifications("playlists");
      ?>
      <section class="create-edit-playlist">
        <form action="create-edit-playlist.php" method="post" enctype="multipart/form-data">
          <label for="playlistName">Playlist name</label>
          <input type="text" name="playlist_name" class="form-control" aria-describedby="playlistNameHelp" placeholder="Enter playlist name...">
          <button type="submit" class="btn btn-primary mt-3">Submit</button>
        </form>
      </section>
      </main>

<?php
include_once('footer.php');
