<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (ethnicity) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>ETHNICITY:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing ethnicity (**N.B. This will amend the ethnicity details for all other instances in which it is used**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('ethn_edit_error') ?>
      <?php echo error_for('ethn_url') ?>
      <?php echo error_for('ethn_dlt') ?>

      </div>
      <form action="" method="post">
        <fieldset>
          <div id="ethn_nm" class="entry">
            <label for="ethn_nm" class="fixedwidth">* ETHNICITY: <?php echo error_for('ethn_nm') ?></label>
            <input type="text" name="ethn_nm" id="ethn_nm" maxlength="255" value="<?php echo $ethn_nm; ?>" class="entryfield <?php echo errorfield('ethn_nm') ?> <?php echo errorfield('ethn_url') ?>"/>
            <h6>i.e. African / Afro-Caribbean / Arab / Bangladeshi / Black British / Chinese / Indian / Japanese / Jewish / Korean / Mestizo / Mulatto / Native American / Pakistani / White / White-Afro-Caribbean / White-Pakistani / Zambos, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rel_ethn_list" class="entry">
            <label for="rel_ethn_list" class="fixedwidth">RELATED ETHNICITIES: <?php echo error_for('rel_ethn_nm_array_excss') ?><?php echo error_for('rel_ethn_empty') ?><?php echo error_for('rel_ethn_dplct') ?><?php echo error_for('rel_ethn_nm_excss_lngth') ?><?php echo error_for('rel_ethn_nm') ?><?php echo error_for('rel_ethn_nonexst') ?><?php echo error_for('rel_ethn_id_mtch') ?><?php echo error_for('rel_ethn_inv_comb') ?></label>
            <input type="text" name="rel_ethn_list" id="rel_ethn_list" value="<?php echo $rel_ethn_list; ?>" class="entryfield <?php echo errorfield('rel_ethn_nm_array_excss') ?> <?php echo errorfield('rel_ethn_empty') ?> <?php echo errorfield('rel_ethn_dplct') ?> <?php echo errorfield('rel_ethn_nm_excss_lngth') ?> <?php echo errorfield('rel_ethn_nm') ?> <?php echo errorfield('rel_ethn_nonexst') ?> <?php echo errorfield('rel_ethn_id_mtch') ?> <?php echo errorfield('rel_ethn_inv_comb') ?>"/>
            <h6>List genre(s) of which this ethnicity is a sub-ethnicity.</br>
            - Separate multiple entries using double comma [,,].</br>
            Rutul: Russian</br>
            Han: Chinese</br>
            Yoruba: African</h6>
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
          <input type="hidden" name="ethn_id" value="<?php echo $ethn_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>