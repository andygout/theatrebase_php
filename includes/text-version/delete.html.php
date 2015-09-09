<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (text version) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>TEXT VERSION:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3>Are you sure you want to delete this text version?</h3>

      <form action="" method="post">
        <div id="buttons" class="buttons">
          <input type="hidden" name="txt_vrsn_id" value="<?php echo $txt_vrsn_id; ?>"/>
          <input type="submit" name="delete" value="Delete" class="button"/>
          <input type="submit" name="delete" value="Cancel" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>