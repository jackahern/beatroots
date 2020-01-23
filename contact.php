<?php
require('config/config.php');

// Show success message if the form has been submitted
$successful_submit = isset($_GET['submit']) ? true : false;
if ($successful_submit == 'true') {
  siteAddNotification("success", "contact", "You have sent your contact form successfully");
  header('Location: contact.php');
  exit;
}

$_SESSION['page_title'] = 'Contact Us';
$_SESSION['page_description'] = 'If you need to reach out to us personally, please fill out our form and find our details below';
require_once('header.php');
?>
<main>
  <?php
  outputNotifications("contact");
  ?>
  <form class="w-50" action="contact.php?submit=successful" method="post">
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="inputEmail">Email</label>
        <input type="email" class="form-control" id="inputEmail" placeholder="Email" required>
      </div>
      <div class="form-group col-md-6">
        <label for="inputName">Name</label>
        <input type="text" class="form-control" id="inputName" placeholder="Jane Doe" required>
      </div>
    </div>
    <div class="form-group">
      <label for="inputMessage">Message</label>
      <input type="text" class="form-control message-box" id="inputMessage" required>
    </div>
    <button type="submit" class="btn btn-primary">Send</button>
  </form>
  <section class="contact">
    <h3>Our contact details</h3>
    <h4>Email</h4>
    <p>rebmem.engineering@webdev.dev.web</p>
    <h4>Phone</h4>
    <p>011701210193</p>
    <h4>Address</h4>
    <p>21 Quay Street, Bristol, BS1 1LQ</p>
  </section>
</main>
<?php
require_once('footer.php');
