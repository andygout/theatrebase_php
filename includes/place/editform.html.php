<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (place) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>SETTING (PLACE):</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing place (**N.B. This will amend the place details for all other instances in which it is used**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('plc_edit_error') ?>
      <?php echo error_for('plc_url') ?>
      <?php echo error_for('plc_dlt') ?>
      </div>

      <?php if(!empty($rel_plcs1)) { ?>
      <div id="dscrptn" class="box"><table class="overview">
      <tr><td class="ovrvwcol1">Sub-related places:</td><td><?php echo implode(' / ', $rel_plcs1); ?></td></tr>
      </table></div></br>
      <?php } ?>

      <form action="" method="post">
        <fieldset>
          <div id="plc_nm" class="entry">
            <label for="plc_nm" class="fixedwidth">* PLACE: <?php echo error_for('plc_nm') ?></label>
            <input type="text" name="plc_nm" id="plc_nm" maxlength="255" value="<?php echo $plc_nm; ?>" class="entryfield <?php echo errorfield('plc_nm') ?> <?php echo errorfield('plc_url') ?>"/>
            <h6>i.e. Council Estate / Hospital / Boarding School, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rel_plc_list" class="entry">
            <label for="rel_plc_list" class="fixedwidth">RELATED PLACES: <?php echo error_for('rel_plc_nm_array_excss') ?><?php echo error_for('rel_plc_empty') ?><?php echo error_for('rel_plc_dplct') ?><?php echo error_for('rel_plc_nm_excss_lngth') ?><?php echo error_for('rel_plc_nm') ?><?php echo error_for('rel_plc_nonexst') ?><?php echo error_for('rel_plc_id_mtch') ?><?php echo error_for('rel_plc_inv_comb') ?></label>
            <input type="text" name="rel_plc_list" id="rel_plc_list" value="<?php echo $rel_plc_list; ?>" class="entryfield <?php echo errorfield('rel_plc_nm_array_excss') ?> <?php echo errorfield('rel_plc_empty') ?> <?php echo errorfield('rel_plc_dplct') ?> <?php echo errorfield('rel_plc_nm_excss_lngth') ?> <?php echo errorfield('rel_plc_nm') ?> <?php echo errorfield('rel_plc_nonexst') ?> <?php echo errorfield('rel_plc_id_mtch') ?> <?php echo errorfield('rel_plc_inv_comb') ?>"/>
            <h6>Places within which it would be found and also the type of place it is (if it is a subcategory of such).</br>
            i.e. McDonalds: fast food restaurant / restaurant</br>
            airport café: café / airport</br>
            NHS psychiatric hospital: psychiatric hospital / hospital</br>
            railroad apartment: apartment</br>
            birthday party: party</br>
            - Use asterisk [*] suffix for entries where it is a subcategory of such place (McDonalds: fast food restaurant) (rather than physically located within (airport café: airport)).</br>
            - Separate multiple entries using double comma [,,]:-</br>
            airport,,café*</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="textarea" class="entry">
            <label for="textarea" class="fixedwidth">TEXT AREA: </label>
            <textarea name="textarea" id="textarea" rows="7" cols="143"><?php echo $textarea; ?></textarea>
            <b>INTERNATIONAL/NON-KEYBOARD CHARACTERS:-</b></br>
            À / È / Ì / Ò / Ù / à / è / ì / ò / ù / Á / É / Í / Ó / Ú / Ý / á / é / í / ó / ú / ý / Â / Ê / Î / Ô / Û / â / ê / î / ô / û / Ä / Ë / Ï / Ö / Ü / Ÿ / ä / ë / ï / ö / ü / ÿ</br>
            Ã / Ñ / Õ / ã / ñ / õ / Ā / Ē / Ī / Ō / Ū / Ȳ / Ǣ / Ḡ / ā / ē / ī / ō / ū / ȳ / ǣ / ḡ / Å / å / Æ / æ / Œ / œ / Ç / ç / Ð / ð / Ø / ø / ¿ / ¡ / ß</br>
            Ǟ / Ȫ / Ǖ / Ṻ / Ǡ / Ȱ / Ḹ / Ṝ / Ǭ / Ȭ / Ḗ / Ṓ / Ḕ / Ṑ / Ӣ / Ӯ / Ᾱ / Ῑ / Ῡ / ǟ / ȫ / ǖ / ṻ / ǡ / ȱ / ḹ / ṝ / ǭ / ȭ / ḗ / ṓ / ḕ / ṑ / ӣ / ӯ / ᾱ / ῑ / ῡ</br>
            ▪</br>
          </div>
        </fieldset>

        <div id="buttons" class="buttons">
          <input type="hidden" name="plc_id" value="<?php echo $plc_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>