<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (category) | TheatreBase</title>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>CATEGORY:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing category (**N.B. This will amend the category details for all other instances in which it is used**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('ctgry_edit_error') ?>
      <?php echo error_for('ctgry_url') ?>
      <?php echo error_for('ctgry_dlt') ?>
      </div>
      <form action="" method="post">
        <fieldset>
          <div id="ctgry_nm" class="entry">
            <label for="ctgry_nm" class="fixedwidth">* CATEGORY: <?php echo error_for('ctgry_nm') ?></label>
            <input type="text" name="ctgry_nm" id="ctgry_nm" maxlength="255" value="<?php echo $ctgry_nm; ?>" class="entryfield <?php echo errorfield('ctgry_nm') ?> <?php echo errorfield('ctgry_url') ?>"/>
            <h6>i.e. Play / Musical / Solo Performance / Reading / Installation / Collection / Drama School Production / Youth Theatre Production / Opera / Operetta / Ballet / Benefit / Burlesque / Cabaret / Circus / Comedy Show / Dance / Devised /  Extravaganza / Hypnotism / Impersonations / Improvisation / Magic / Mime / Monologue / One Act Play / Puppetry / Poetry / Pantomime / Play with Music / Revue / Scenes / Short Play / Tribute / Variety / Vaudeville / Misc., etc.</h6>
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
          <input type="hidden" name="ctgry_id" value="<?php echo $ctgry_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>