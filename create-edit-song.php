<?php
require('config/config.php');
$current_file = 'create-edit-song.php';
$success_page = 'manage-songs.php';
require_once('resources/pages/create-edit-song/form_handler.php');

// find a way to find if the genre is being edited or created, leave isedit here as false for now
$isEdit = isset($_GET['song_id']) ? true : false;
if ($isEdit) {
  // get artist details for this artist
  $song = getSong($_GET['song_id']);
  // if card doesn't exist for the supplied card_id, then kick out back to list page and show error
  if ($song == NULL) {
    //First create the redirect to use query string until i see it working and then change it to use the errors the same way it does in the form handler
    siteAddNotification("error", "songs", "That song doesn't exist");
  } else {
    $song_url = 'songs/' . $song['song_id']. '.mp3';
  }
}
$_SESSION['page_title'] = $isEdit ? 'Edit song' : 'Create song';
$_SESSION['page_description'] = $isEdit ? 'Edit existing song' : 'Here you can add a new song. For this, you need to know the title of the song, the artist who authored it, the genre and if it is part of an album, then the album';
include_once('header.php');

$artists = getArtists();
$albums = getAlbums();
$genres = getGenres();

?>
  <main>
    <?php
    // Conditions to check whether any error/success messages are present in the session, if there are then print them out on the screen
    outputNotifications("songs");
    ?>
    <section class="create-edit-song">
      <form action="create-edit-song.php" method="post" enctype="multipart/form-data">
        <label for="songTitle">Song title:</label>
        <input type="text" name="song_title" class="form-control" aria-describedby="songTitleHelp" placeholder="Enter song title..." value="<?=$isEdit ? $song['song_title'] : ''?>">
        <label>By artist:</label>
        <input list="artists" name="song_artist_id" placeholder="Search for artist..." class="form-control" value="<?=$isEdit ? $song['artist_id'] . ' - ' . $song['artist_name'] : ''?>">
        <datalist id="artists">
          <?php
          foreach ($artists as $artist) {
            ?>
            <option value="<?=$artist['artist_id'] . ' - ' . $artist['artist_name'];?>"><?=$artist['artist_name']?></option>
            <?php
          }
          ?>
        </datalist>
        </input>
        <label>In album (can be left blank if the song is not in an album):</label>
        <input list="albums" name="song_album_id" placeholder="Search for album..." class="form-control" value="<?=!is_null($song['album_id']) ? $song['album_id'] . ' - ' . $song['album_title'] : ''?>">
        <datalist id="albums">
        <?php
          foreach ($albums as $album) {
            ?>
            <option value="<?=$album['album_id'] . ' - ' . $album['album_title'];?>"><?=$album['album_title']?></option>
            <?php
            }
          ?>
        </datalist>
        </input>
        <label>Genre:</label>
        <select id="select-genre" name="song_genre_id" class="custom-select my-1 mr-sm-2">
          <?php
          if ($isEdit) {
            ?>
            <option value="<?=$song['genre_id'];?>"><?=$song['genre_name']?></option>
            <?php
          } else {
            ?>
            <option value="0">Please select...</option>
            <?php
          }
          foreach ($genres as $genre) {
            ?>
            <option value="<?=$genre['genre_id'];?>"><?=$genre['genre_name']?></option>
            <?php
          }
          ?>
        </select>
        <label for="uploadedSong"><?=$isEdit ? 'Change uploaded song' : 'Upload song'?></label>
        <div class="custom-file">
          <input type="file" class="custom-file-input" id="customFile" name="song">
          <label class="custom-file-label" for="customFile">Choose file</label>
        </div>
        <?php
        // If isEdit is true then show the thumbnail image that is already being used for the card
        if ($isEdit && file_exists($song_url)) {
          ?>
          <label for="existingCoverPhoto">Current uploaded song - <?=$song['song_title'];?></label>
          <audio controls>
              <source src="<?=$song_url;?>" type="audio/mpeg">
          </audio>
          <?php
        }
        ?>
        <div class="form-group">
          <?php
          if ($isEdit) {
            ?>
            <input type="hidden" value="<?=$song['song_id']?>" name="song_id"/>
            <input type="hidden" value="edit-song" name="action"/>
            <?php
          }
          else {
            ?>
            <input type="hidden" value="create-song" name="action"/>
            <?php
          }
          ?>
          <button type="submit" class="btn btn-primary mt-3" name="create-edit-song-submit"><?=$isEdit ? 'Update' : 'Submit';?></button>
      </form>
    </section>
  </main>
<?php
include_once('footer.php');