<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (course type) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>COURSE TYPE:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing course type (**N.B. This will amend the course type name across all courses in which it appears**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('crs_typ_edit_error') ?>
      <?php echo error_for('crs_typ_url') ?>
      <?php echo error_for('crs_typ_dlt') ?>
      </div>

      <form action="" method="post">
        <fieldset>
          <div id="crs_typ_nm" class="entry">
            <label for="crs_typ_nm" class="fixedwidth">* COURSE TYPE: <?php echo error_for('crs_typ_nm') ?></label>
            <input type="text" name="crs_typ_nm" id="crs_typ_nm" maxlength="255" value="<?php echo $crs_typ_nm; ?>" class="entryfield <?php echo errorfield('crs_typ_nm') ?> <?php echo errorfield('crs_typ_url') ?>"/>
            <h6>i.e. 3 Year Acting</h6>
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
          <input type="hidden" name="crs_typ_id" value="<?php echo $crs_typ_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>