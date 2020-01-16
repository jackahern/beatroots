<?php
session_start();
$currentFile = 'create-edit-artist.php';

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = false;
$_SESSION['page_title'] = $isEdit ? 'Edit artist' : 'Create artist';
$_SESSION['page_description'] = 'Here you can create a new artist, all this requires is a name!';
include_once('header.php');

// handle form input here
if (isset($_POST['artist_name'])) {
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
    $sql = "INSERT INTO artists (artist_name) VALUES (:artist_name)";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':artist_name' => $_POST['artist_name']
    ]);
    if ($execute) {
      siteAddNotification("success", "artists", "Artist called " . $_POST['artist_name'] . " added");
      unset($_POST['artist_name']);
    }
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
