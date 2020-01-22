<?php
require_once('config/config.php');
$current_file = 'create-edit-genre.php';
$success_page = 'manage-genres.php';
require_once('resources/pages/create-edit-genre/form_handler.php');

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = isset($_GET['genre_id']) ? true : false;
if ($isEdit) {
  // get artist details for this artist
  $genre = getGenreById($_GET['genre_id']);
  // if card doesn't exist for the supplied card_id, then kick out back to list page and show error
  if ($genre == NULL) {
    //First create the redirect to use query string until i see it working and then change it to use the errors the same way it does in the form handler
    siteAddNotification("error", "genres", "The genre doesn't exist");
  }
}
$_SESSION['page_title'] = $isEdit ? 'Edit genre' : 'Create genre';
$_SESSION['page_description'] = $isEdit ? 'Edit this existing genre' : 'Here you can create a new genre, all this requires is a name!';
include_once('header.php');


?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("genres");
    ?>
    <section class="create-edit-genre">
      <form action="<?=$current_file?>" method="post" enctype="multipart/form-data">
        <label for="genreName">Genre name</label>
        <input type="text" name="genre_name" class="form-control" aria-describedby="genreNameHelp" placeholder="Enter genre name..." value="<?=$isEdit ? $genre['genre_name'] : ''?>">
        <div class="form-group">
        <?php
        if ($isEdit) {
          ?>
            <input type="hidden" value="<?=$genre['genre_id']?>" name="genre_id"/>
            <input type="hidden" value="edit-genre" name="action"/>
          <?php
        }
        else {
          ?>
            <input type="hidden" value="create-genre" name="action"/>
          <?php
        }
        ?>
        </div>
        <button type="submit" class="btn btn-primary mt-3"><?=$isEdit ? 'Update' : 'Submit' ?></button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');