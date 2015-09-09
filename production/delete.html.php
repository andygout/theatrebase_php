<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?></title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4><?php echo $prd_clss_dsply; ?>PRODUCTION<?php echo $tr_dsply.$coll_dsply; ?>:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3>Are you sure you want to delete this production?</h3>

      <div id="buttons" class="buttons">
        <form action="" method="post">
          <input type="hidden" name="prd_id" value="<?php echo $prd_id; ?>"/>
          <input type="submit" name="delete" value="Delete" class="button"/>
          <input type="submit" name="delete" value="Cancel" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>