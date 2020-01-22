<?php
require ('config/config.php');
?>
<html>
<head>
  <title><?=$_SESSION['page_title'];?></title>

  <!-- Include bootstrap, fontawesome, any other files that need linking -->
  <!-- Bootstrap 4 -->
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <!-- Fontawesome 4.7 -->
  <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
  <!-- main css style sheet -->
  <link rel="stylesheet" href="css/main.css">
  <!-- more script tags for using bootstraps modals -->
  <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
</head>
<body>
<header>
  <h1><?=$_SESSION['page_title'];?></h1>
  <p><?=$_SESSION['page_description']?></p>
  <div id="screensaver">
      <p class="centered">You are operating in idle mode <br/><span class="idle-subtext">(Come out of idle mode by clicking anywhere on the screen)</span></p>
  </div>
</header>
<nav id="sidenav">
  <a href="index.php">Home</a>
  <a href="genres.php">Genres</a>
  <button class="dropdown-btn">Albums
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <!-- Change this to take 3 albums at random from an array and then the option to see full list -->
    <div class="sub-items">
      <?php
      $albums = getAlbums();
      $albums_count = count($albums);
      if ($albums_count >= 3) {
        shuffle($albums);
        for ($x = 0; $x < 3; $x++) {
            ?>
              <a href="album.php?album=<?=$albums[$x]['album_id'];?>"><?=$albums[$x]['album_title'];?></a>
            <?php
          }
      } else {
        for ($x = 0; $x < $albums_count; $x++) {
          ?>
            <a href="album.php?album=<?=$albums[$x]['album_id'];?>"><?=$albums[$x]['album_title'];?></a>
          <?php
        }
      }
      ?>
      <a href="albums.php?browse-more">Browse more albums</a>
    </div>
  </div>
  <button class="dropdown-btn">Artists
    <i class="fa fa-caret-down"></i>
  </button>
  <div class="dropdown-container">
    <!-- Change this to take 3 artists at random from an array and then the option to see full list -->
    <div class="sub-items">
      <?php
      $artists = getArtists();
      $artists_count = count($artists);
      if ($artists_count >= 3) {
        shuffle($artists);
        for ($x = 0; $x < 3; $x++) {
          ?>
            <a id="artist-id-<?=$artists[$x]['artist_id'];?>" href="artists.php?artist=<?=$artists[$x]['artist_id'];?>"><?=$artists[$x]['artist_name'];?></a>
          <?php
        }
      } else {
          for ($x = 0; $x < $artists_count; $x++) {
            ?>
              <a id="artist-id-<?= $artists[$x]['artist_id']; ?>"
                 href="artists.php?artist=<?= $artists[$x]['artist_id']; ?>"><?= $artists[$x]['artist_name']; ?></a>
            <?php
          }
      }
      ?>
      <a href="artists.php?browse-more">Browse more artists</a>
    </div>
  </div>
  <a href="songs.php">Songs</a>
  <a href="playlists.php">Playlists</a>
  <a href="search.php">Search</a>
  <a href="#contact">Contact</a>
</nav>