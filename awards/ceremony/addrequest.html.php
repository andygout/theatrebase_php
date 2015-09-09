<!DOCTYPE html>
<html>
<head>
  <title>Add Awards | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>AWARDS:</h4>
      <h1>Add Awards</h1>
      <h3>Click button to add new awards to the database.</h3>
      <form action="?add" method="post">
        <div id="buttons" class="buttons">
          <input type="submit" name="add" value="Add Awards" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>