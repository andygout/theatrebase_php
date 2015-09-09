<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (setting: time) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>SETTING (TIME):</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing setting (time).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('tm_edit_error') ?>
      <?php echo error_for('tm_url') ?>
      <?php echo error_for('tm_dlt') ?>
      </div>

      <?php if(!empty($rel_tms1)) { ?>
      <div id="dscrptn" class="box"><table class="overview">
      <tr><td class="ovrvwcol1">Comprises:</td><td><?php echo implode(' / ', $rel_tms1); ?></td></tr>
      </table></div></br>
      <?php } ?>

      <form action="" method="post">
        <fieldset>
          <div id="tm_nm" class="entry">
            <label for="tm_nm" class="fixedwidth">* SETTING (TIME):   <?php echo error_for('tm_nm') ?></label>
            <input type="text" name="tm_nm" id="tm_nm" maxlength="255" value="<?php echo $tm_nm; ?>" class="entryfield <?php echo errorfield('tm_nm') ?> <?php echo errorfield('tm_url') ?>"/>
            <h6>i.e. Summer / February / Christmas / Ancient / Ancient Arabian / Ancient Greek / 4th Century BCE / 360s BCE / 350s BCE / Ancient Roman / Biblical / Arthurian / 10th Century / Medieval / 19th Century / 1900s / 1910s / 1920s / 1930s / 1940s / 1950s / 1960s / 1970s / 1980s / 1990s / 20th Century / 2000s / 2010s / 21st Century / Contemporary / Contemporary and Period / Modern costume / Future / Fantasy / Steampunk / Unspecified, etc.</br>
          </div>
        </fieldset>

        <fieldset>
          <div id="tm_frm_dt" class="entry">
            <label for="tm_frm_dt" class="fixedwidth">FROM [DD]-[MM]-[YYYY]: <?php echo error_for('tm_frm_dt') ?></label>
            <input type="date" name="tm_frm_dt" id="tm_frm_dt" maxlength="10" value="<?php echo $tm_frm_dt; ?>" class="entryfielddate <?php echo errorfield('tm_frm_dt') ?> <?php echo errorfield('tm_rcr') ?>"/>
            <input type="checkbox" name="tm_frm_dt_bce" id="tm_frm_dt_bce"<?php if($tm_frm_dt_bce) {echo ' checked="checked"';} ?>/> BCE</br>
            <h6>i.e. 21-08-2007</h6>
          </div>

          <div id="tm_to_dt" class="entry">
            <label for="tm_to_dt" class="fixedwidth">TO [DD]-[MM]-[YYYY]: <?php echo error_for('tm_to_dt') ?></label>
            <input type="date" name="tm_to_dt" id="tm_to_dt" maxlength="10" value="<?php echo $tm_to_dt; ?>" class="entryfielddate <?php echo errorfield('tm_to_dt') ?> <?php echo errorfield('tm_rcr') ?>"/>
            <input type="checkbox" name="tm_to_dt_bce" id="tm_to_dt_bce"<?php if($tm_to_dt_bce) {echo ' checked="checked"';} ?>/> BCE</br>
            <h6>i.e. 05-01-2013</h6>
          </div>

          <div id="tm_rcr" class="entry">
            <label for="tm_rcr" class="fixedwidth">RECURRING TIME: <?php echo error_for('tm_rcr') ?></label>
            <input type="checkbox" name="tm_rcr" id="tm_rcr"<?php if($tm_rcr) {echo ' checked="checked"';} ?>/>
            <h6>Check box if this time is of a recurring nature (i.e. Christmas Day, Leap Day, Easter Sunday, New Year's Day, Diwali, etc.).</br></h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rel_tm_list" class="entry">
            <label for="rel_tm_list" class="fixedwidth">RELATED TIMES: <?php echo error_for('rel_tm_nm_array_excss') ?><?php echo error_for('rel_tm_empty') ?><?php echo error_for('rel_tm_dplct') ?><?php echo error_for('rel_tm_nm_excss_lngth') ?><?php echo error_for('rel_tm_nm') ?><?php echo error_for('rel_tm_nonexst') ?><?php echo error_for('rel_tm_id_mtch') ?><?php echo error_for('rel_tm_inv_comb') ?><?php echo error_for('rel_tm_dt_mtch') ?></label>
            <input type="text" name="rel_tm_list" id="rel_tm_list" value="<?php echo $rel_tm_list; ?>" class="entryfield <?php echo errorfield('rel_tm_nm_array_excss') ?> <?php echo errorfield('rel_tm_empty') ?> <?php echo errorfield('rel_tm_dplct') ?> <?php echo errorfield('rel_tm_nm_excss_lngth') ?> <?php echo errorfield('rel_tm_nm') ?> <?php echo errorfield('rel_tm_nonexst') ?> <?php echo errorfield('rel_tm_id_mtch') ?> <?php echo errorfield('rel_tm_inv_comb') ?> <?php echo errorfield('rel_tm_dt_mtch') ?>"/>
            <h6>i.e. 1595: 1590s,,16th Century,,Elizabethan Era / Easter 1962: Easter,,1962,,1960s,,20th Century / December 1978: December,,Christmas,,1978,,1970s,,20th Century</br>
            - Separate multiple entries using double comma [,,].</h6>
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
          <input type="hidden" name="tm_id" value="<?php echo $tm_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>