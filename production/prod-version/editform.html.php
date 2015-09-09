<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (production version) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>PRODUCTION VERSION:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing production version.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('prd_vrsn_edit_error') ?>
      <?php echo error_for('prd_vrsn_url') ?>
      <?php echo error_for('prd_vrsn_dlt') ?>
      </div>
      <form action="" method="post">
        <fieldset>
          <div id="prd_vrsn_nm" class="entry">
            <label for="prd_vrsn_nm" class="fixedwidth">* PRODUCTION VERSION:   <?php echo error_for('prd_vrsn_nm') ?></label>
            <input type="text" name="prd_vrsn_nm" id="prd_vrsn_nm" maxlength="255" value="<?php echo $prd_vrsn_nm; ?>" class="entryfield <?php echo errorfield('prd_vrsn_nm') ?> <?php echo errorfield('prd_vrsn_url') ?>"/>
            <h6>i.e. World Premiere / European Premiere / UK Premiere / Regional Premiere / Return / Transfer / West End Transfer / London Transfer / UK Transfer / Broadway Transfer / Transfer from West End / Transfer from Broadway, etc.</h6>
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
          <input type="hidden" name="prd_vrsn_id" value="<?php echo $prd_vrsn_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>