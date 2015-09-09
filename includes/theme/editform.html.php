<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (theme) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>THEME:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing theme (**N.B. This will amend the theme details for all other instances in which it is used**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('thm_edit_error') ?>
      <?php echo error_for('thm_url') ?>
      <?php echo error_for('thm_dlt') ?>
      </div>

      <?php if(!empty($rel_thms1)) { ?>
      <div id="dscrptn" class="box"><table class="overview">
      <tr><td class="ovrvwcol1">Sub-themes:</td><td><?php echo implode(' / ', $rel_thms1); ?></td></tr>
      </table></div></br>
      <?php } ?>

      <form action="" method="post">
        <fieldset>
          <div id="thm_nm" class="entry">
            <label for="thm_nm" class="fixedwidth">* THEME:   <?php echo error_for('thm_nm') ?></label>
            <input type="text" name="thm_nm" id="thm_nm" maxlength="255" value="<?php echo $thm_nm; ?>" class="entryfield <?php echo errorfield('thm_nm') ?> <?php echo errorfield('thm_url') ?>"/>
            <h6>i.e. Global Warming / Regicide / Unrequited Love / Postnatal Depression / Russian Revolution / Genocide / Cultural Identity / Homophobia / World War I / World War II / 9/11 / AIDS crisis / Thatcherism / Vietnam War, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rel_thm_list" class="entry">
            <label for="rel_thm_list" class="fixedwidth">RELATED (BROADER) THEMES: <?php echo error_for('rel_thm_nm_array_excss') ?><?php echo error_for('rel_thm_empty') ?><?php echo error_for('rel_thm_dplct') ?><?php echo error_for('rel_thm_nm_excss_lngth') ?><?php echo error_for('rel_thm_nm') ?><?php echo error_for('rel_thm_nonexst') ?><?php echo error_for('rel_thm_id_mtch') ?><?php echo error_for('rel_thm_inv_comb') ?></label>
            <input type="text" name="rel_thm_list" id="rel_thm_list" value="<?php echo $rel_thm_list; ?>" class="entryfield <?php echo errorfield('rel_thm_nm_array_excss') ?> <?php echo errorfield('rel_thm_empty') ?> <?php echo errorfield('rel_thm_dplct') ?> <?php echo errorfield('rel_thm_nm_excss_lngth') ?> <?php echo errorfield('rel_thm_nm') ?> <?php echo errorfield('rel_thm_nonexst') ?> <?php echo errorfield('rel_thm_id_mtch') ?> <?php echo errorfield('rel_thm_inv_comb') ?>"/>
            <h6>i.e. Normandy landings: World War II,,World War,,War / Thatcherism: Conservatism,,Politics / Fratricide: Homicide / Unrequited love: Love</br>
            - Separate multiple entries using double comma [,,].</br>
            NB: Only link to broader themes in the most universal sense, via one trail of thinking, and where there are no exceptions, i.e. 'homicide' should not be linked to 'crime' as there are non-criminal instances of homicide (euthanasia; assisted suicide; capital punishment; feticide, etc.).</h6>
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
          <input type="hidden" name="thm_id" value="<?php echo $thm_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>