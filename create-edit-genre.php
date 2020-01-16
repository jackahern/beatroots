<?php
session_start();
$currentFile = 'create-edit-genre.php';

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = false;
$_SESSION['page_title'] = $isEdit ? 'Edit genre' : 'Create genre';
$_SESSION['page_description'] = 'Here you can create a new genre, all this requires is a name!';
include_once('header.php');

// handle form input here
if (isset($_POST['genre_name'])) {
  // Involve some validation to stop the same card being created twice
  $sql = "SELECT genre_name FROM genres WHERE genre_name = :genre_name";
  $stmt = $conn->prepare($sql);
  // Create execute variable to be assigned the statement execute function
  $execute = $stmt->execute([
    ':genre_name' => $_POST['genre_name']
  ]);
  // Condition to check if the prepared statement will provide something to the database that already exists
  if ($stmt->rowCount() > 0) {
    siteAddNotification("error", "genres", "A genre with the name " . $_POST['genre_name'] . " already exists");
  } else {
    $sql = "INSERT INTO genres (genre_name) VALUES (:genre_name)";
    $stmt = $conn->prepare($sql);
    $execute = $stmt->execute([
      ':genre_name' => $_POST['genre_name']
    ]);
    if ($execute) {
      siteAddNotification("success", "genres", "Genre of " . $_POST['genre_name'] . " added");
      unset($_POST['genre_name']);
    }
  }
}
?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("genres");
    ?>
    <section class="create-edit-genre">
      <form action="create-edit-genre.php" method="post" enctype="multipart/form-data">
        <label for="genreName">Genre name</label>
        <input type="text" name="genre_name" class="form-control" aria-describedby="genreNameHelp" placeholder="Enter genre name...">
        <button type="submit" class="btn btn-primary mt-3">Submit</button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');