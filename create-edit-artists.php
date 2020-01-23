<?php
require_once('config/config.php');
$current_file = 'create-edit-artists.php';
$success_page = 'manage-artists.php';
require_once('resources/pages/create-edit-artist/form_handler.php');

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = isset($_GET['artist_id']) ? true : false;
if ($isEdit) {
  // get artist details for this artist
  $artist = getArtistById($_GET['artist_id']);
  // if card doesn't exist for the supplied card_id, then kick out back to list page and show error
  if ($artist == NULL) {
    //First create the redirect to use query string until i see it working and then change it to use the errors the same way it does in the form handler
    siteAddNotification("error", "artists", "The artist doesn't exist");
  } else {
    $imgUrl = 'images/artists/' . $artist['artist_id']. '.png';
  }
}

$_SESSION['page_title'] = $isEdit ? 'Edit artist' : 'Create artist';
$_SESSION['page_description'] = 'Here you can create a new artist, all this requires is a name!';
require_once('header.php');

?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("artists");
    ?>
    <section class="w-25">
      <form action="<?=$current_file?>" method="post" enctype="multipart/form-data">
        <label for="artistName">Artist name</label>
        <input type="text" name="artist_name" class="form-control" aria-describedby="artistNameHelp" placeholder="Enter artist name..." value="<?=$artist['artist_name'] ?? $_GET['name'] ?? '' ?>" required>
        <label for="artistAvatar"><?=$isEdit ? 'Replace avatar' : 'Artist avatar' ?></label>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="customFile" name="artist_avatar">
          <label class="custom-file-label" for="customFile">Choose file...</label>
        </div>
        <?php
        // If isEdit is true then show the thumbnail image that is already being used for the card
        if ($isEdit && file_exists($imgUrl)) {
          ?>
            <label for="existingAvatar">Current Avatar</label>
            <img class="edit-thumbnail-img" src="<?=$imgUrl . '?nc=' . filemtime($imgUrl)?>">
          <?php
        }
        ?>
        <div class="form-group">
          <?php
          if ($isEdit) {
            ?>
              <input type="hidden" value="<?=$artist['artist_id']?>" name="artist_id"/>
              <input type="hidden" value="edit-artist" name="action"/>
            <?php
          }
          else {
            ?>
              <input type="hidden" value="create-artist" name="action"/>
            <?php
          }
          ?>
        </div>
        <button type="submit" class="btn btn-primary mt-3" name="create-edit-artist-submit"><?=$isEdit ? 'Update' : 'Submit'?></button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');
