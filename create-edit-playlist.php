<?php
require_once('config/config.php');
$currentFile = 'create-edit-playlist.php';
$success_page = 'manage-playlists.php';
require_once('resources/pages/create-edit-playlist/form_handler.php');

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = isset($_GET['playlist_id']) ? true : false;
if ($isEdit) {
  // get artist details for this artist
  $playlist = getPlaylistById($_GET['playlist_id']);
  // if card doesn't exist for the supplied card_id, then kick out back to list page and show error
  if ($playlist == NULL) {
    //First create the redirect to use query string until i see it working and then change it to use the errors the same way it does in the form handler
    siteAddNotification("error", "playlists", "That playlist doesn't exist");
  }
}

$_SESSION['page_title'] = $isEdit ? 'Edit playlist' : 'Create playlist';
$_SESSION['page_description'] = $isEdit ? 'Edit an existing playlist' : 'Here you can create a new playlist, all this requires is a name!';
include_once('header.php');

?>
      <main>
      <?php
      outputNotifications("playlists");
      ?>
      <section class="w-25">
        <form action="<?=$current_file?>" method="post" enctype="multipart/form-data">
          <label for="playlistName">Playlist name</label>
          <input type="text" name="playlist_name" class="form-control" aria-describedby="playlistNameHelp" placeholder="Enter playlist name..." value="<?=$isEdit ? $playlist['playlist_name'] : ''?>" required>
          <div class="form-group">
              <?php
              if ($isEdit) {
                ?>
                  <input type="hidden" value="<?=$playlist['playlist_id']?>" name="playlist_id"/>
                  <input type="hidden" value="edit-playlist" name="action"/>
                <?php
              }
              else {
                ?>
                  <input type="hidden" value="create-playlist" name="action"/>
                <?php
              }
              ?>
          </div>
          <button type="submit" class="btn btn-primary mt-3"><?=$isEdit ? 'Update' : 'Submit'?></button>
        </form>
      </section>
      </main>

<?php
include_once('footer.php');
