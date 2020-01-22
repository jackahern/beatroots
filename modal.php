<?php
require('config/config.php');
$playlists = getPlaylists();
?>
<!-- Modal -->
<div class="modal fade" id="addPlaylistModal" tabindex="-1" role="dialog" aria-labelledby="addPlaylistModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title" id="addPlaylistModalLabel">Add this song to a playlist?</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <div class="modal-body">
        What playlist would you like to add this song to?
        <form action="add-to-playlist.php" method="post" id="add-song-to-playlist">
          <select id="select-genre" name="playlist_id" class="custom-select my-1 mr-sm-2">
            <?php
            foreach ($playlists as $playlist) {
            ?>
              <option value="<?=$playlist['playlist_id'];?>"><?=$playlist['playlist_name']?></option>
            <?php
            }
            ?>
          </select>
          <input id="song_id" type="hidden" name="song_id">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" form="add-song-to-playlist">Add</button>
      </div>
    </div>
  </div>
</div>
<script src="js/main.js"></script>
<script>
    var cookie = showCookie();
    document.getElementById("song_id").value = cookie;
    console.log(cookie);
</script>


