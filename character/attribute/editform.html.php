<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (attribute) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>ATTRIBUTE:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing attribute.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('attr_edit_error') ?>
      <?php echo error_for('attr_url') ?>
      <?php echo error_for('attr_dlt') ?>
      </div>

      <form action="" method="post">
        <fieldset>
          <div id="attr_nm" class="entry">
            <label for="attr_nm" class="fixedwidth">* ATTRIBUTE: <?php echo error_for('attr_nm') ?></label>
            <input type="text" name="attr_nm" id="attr_nm" maxlength="255" value="<?php echo $attr_nm; ?>" class="entryfield <?php echo errorfield('attr_nm') ?> <?php echo errorfield('attr_url') ?>"/>
            <h6>i.e. Blonde hair / Pregnant / Tattoo / Husky voice / Paraplegic / Obsessive-compulsive disorder / Asperger syndrome / Philosophical / Pathological liar / Duplicitous / Fairy / Lion / Toad / Tree / God / Devil / Child / Teenager/ Elderly / Christian / Buddhist, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rel_attr_list" class="entry">
            <label for="rel_attr_list" class="fixedwidth">RELATED ATTRIBUTE: <?php echo error_for('rel_attr_nm_array_excss') ?><?php echo error_for('rel_attr_empty') ?><?php echo error_for('rel_attr_dplct') ?><?php echo error_for('rel_attr_nm_excss_lngth') ?><?php echo error_for('rel_attr_nm') ?><?php echo error_for('rel_attr_nonexst') ?><?php echo error_for('rel_attr_id_mtch') ?><?php echo error_for('rel_attr_inv_comb') ?></label>
            <input type="text" name="rel_attr_list" id="rel_attr_list" value="<?php echo $rel_attr_list; ?>" class="entryfield <?php echo errorfield('rel_attr_nm_array_excss') ?> <?php echo errorfield('rel_attr_empty') ?> <?php echo errorfield('rel_attr_dplct') ?> <?php echo errorfield('rel_attr_nm_excss_lngth') ?> <?php echo errorfield('rel_attr_nm') ?> <?php echo errorfield('rel_attr_nonexst') ?> <?php echo errorfield('rel_attr_id_mtch') ?> <?php echo errorfield('rel_attr_inv_comb') ?>"/>
            <h6>List attribute(s) of which this attribute is a sub-attribute.</br>
            - Separate multiple entries using double comma [,,].</br>
            Tattoo: Body Art</br>
            Asperger syndrome: Neudrodevelopmental disorder</br>
            Latin Church: Catholic / Christian</h6>
            </h6>
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
          <input type="hidden" name="attr_id" value="<?php echo $attr_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>