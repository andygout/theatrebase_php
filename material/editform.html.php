<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (material) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>MATERIAL:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing material.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('mat_edit_error') ?>
      <?php echo error_for('mat_url') ?>
      <?php echo error_for('mat_dlt') ?>
      </div>

      <form action="" method="post">
        <fieldset>
          <div id="mat_nm" class="entry">
            <label for="mat_nm" class="fixedwidth">* MATERIAL NAME:   <?php echo error_for('mat_nm') ?></label>
            <input type="text" name="mat_nm" id="mat_nm" maxlength="255" value="<?php echo $mat_nm; ?>" class="entryfield <?php echo errorfield('mat_nm') ?> <?php echo errorfield('mat_nm_excss_lngth') ?> <?php echo errorfield('mat_url') ?>"/>
            <h6>i.e. A Midsummer Night's Dream / Metamorphosis / All About My Mother, etc.</h6>
          </div>

          <div id="frmt_nm" class="entry">
            <label for="frmt_nm" class="fixedwidth">* MATERIAL FORMAT:   <?php echo error_for('frmt_nm') ?></label>
            <input type="text" name="frmt_nm" id="frmt_nm" maxlength="255" value="<?php echo $frmt_nm; ?>" class="entryfield <?php echo errorfield('frmt_nm') ?> <?php echo errorfield('mat_url') ?>"/>
            <h6>i.e. play / novel / screenplay, etc.</h6>
          </div>

          <div id="mat_sffx_num" class="entry">
            <label for="mat_sffx_num" class="fixedwidth">SUFFIX [1-99]:   <?php echo error_for('mat_sffx') ?></label>
            <input type="text" name="mat_sffx_num" id="mat_sffx_num" maxlength="2" value="<?php echo $mat_sffx_num; ?>" class="entryfield2chars <?php echo errorfield('mat_sffx') ?> <?php echo errorfield('mat_nm_excss_lngth') ?> <?php echo errorfield('mat_url') ?>"/>
            <h6>To differentiate material with the same name and format, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 99 with no leading 0s).</h6>
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
          <input type="hidden" name="mat_id" value="<?php echo $mat_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>