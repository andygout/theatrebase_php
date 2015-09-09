<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (award category) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>AWARDS:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing award category (**N.B. This will amend the award category name across all awards in which it appears**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('awrd_ctgry_edit_error') ?>
      <?php echo error_for('awrd_ctgry_dlt') ?>
      </div>
      <form action="" method="post">
        <fieldset>
          <div id="awrd_ctgry_nm" class="entry">
            <label for="awrd_ctgry_nm" class="fixedwidth">* AWARD CATEGORY NAME: <?php echo error_for('awrd_ctgry_nm') ?></label>
            <input type="text" name="awrd_ctgry_nm" id="awrd_ctgry_nm" maxlength="255" value="<?php echo $awrd_ctgry_nm; ?>" class="entryfield <?php echo errorfield('awrd_ctgry_nm') ?>"/>
            <h6>i.e. Best Actor, Best Actress, Best Director, Best Revival, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="textarea" class="entry">
            <label for="textarea" class="fixedwidth">INTERNATIONAL/NON-KEYBOARD CHARACTERS:-</label>
            À / È / Ì / Ò / Ù / à / è / ì / ò / ù / Á / É / Í / Ó / Ú / Ý / á / é / í / ó / ú / ý / Â / Ê / Î / Ô / Û / â / ê / î / ô / û / Ä / Ë / Ï / Ö / Ü / Ÿ / ä / ë / ï / ö / ü / ÿ</br>
            Ã / Ñ / Õ / ã / ñ / õ / Ā / Ē / Ī / Ō / Ū / Ȳ / Ǣ / Ḡ / ā / ē / ī / ō / ū / ȳ / ǣ / ḡ / Å / å / Æ / æ / Œ / œ / Ç / ç / Ð / ð / Ø / ø / ¿ / ¡ / ß</br>
            Ǟ / Ȫ / Ǖ / Ṻ / Ǡ / Ȱ / Ḹ / Ṝ / Ǭ / Ȭ / Ḗ / Ṓ / Ḕ / Ṑ / Ӣ / Ӯ / Ᾱ / Ῑ / Ῡ / ǟ / ȫ / ǖ / ṻ / ǡ / ȱ / ḹ / ṝ / ǭ / ȭ / ḗ / ṓ / ḕ / ṑ / ӣ / ӯ / ᾱ / ῑ / ῡ</br>
            ▪</br>
          </div>
        </fieldset>

        <div id="buttons" class="buttons">
          <input type="hidden" name="awrd_ctgry_id" value="<?php echo $awrd_ctgry_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>