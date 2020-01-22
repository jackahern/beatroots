<?php
require('config/config.php');
$current_file = 'search.php';

$has_searched = isset($_GET['searched']) ? true : false;
if ($has_searched) {
  // do stuff here to get search data
}

$_SESSION['page_title'] = "Search";
$_SESSION['page_description'] = "Search through the music player to find the album or song you desire!";

require_once('header.php');

?>
      <main>
          <form class="search-form"  action="<?=$current_file?>?search=true" method="post">
            <div class="row">
              <div class="col">
                <legend class="col-form-label col-sm-8 pt-0">I am searching for...</legend>
                <div class="col-sm-10">
                  <div class="form-check">
                    <input type="radio" name="search_criteria" value="album">
                    <label class="form-check-label" for="gridRadios1">
                      An album
                    </label>
                  </div>
                  <div class="form-check">
                    <input type="radio" name="search_criteria" value="song">
                    <label class="form-check-label" for="gridRadios2">
                      A song
                    </label>
                  </div>
                </div>
              </div>
              <div class="col">
                <label>Keyword(s)</label>
                <input type="text" class="form-control" name="search_keywords" placeholder="Someone like you"  value="<?=$has_searched ? $has_searched['search_keywords'] : ''?>" required>
              </div>
            </div>
            <button type="submit" class="btn btn-primary float-right">Search</button>
          </form>

        <section class="search-results">
          <?php
            if ($has_searched) {
              $matches = searchDatabase($_POST['search_criteria'], $_POST['keywords']);
              foreach ($matches as $match) {
                var_dump($match);
              }
            }
          ?>

        </section>
      </main>

<?php
require_once('footer.php');
