<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (location) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>LOCATION:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing location (**N.B. This will amend the location details for all other instances in which it is used**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('lctn_edit_error') ?>
      <?php echo error_for('lctn_excss_lngth') ?>
      <?php echo error_for('lctn_url') ?>
      <?php echo error_for('lctn_dlt') ?>
      </div>

      <?php if(!empty($rel_lctns1) || !empty($rel_lctns1_exp) || !empty($rel_lctns1_fctn)) { ?>
      <div id="dscrptn" class="box"><table class="overview">
      <?php
      if(!empty($rel_lctns1)) { ?><tr><td class="ovrvwcol1">Comprises:</td><td><?php echo implode(' / ', $rel_lctns1); ?></td></tr><?php }
      if(!empty($rel_lctns1_exp)) { ?><tr><td class="ovrvwcol1">Comprises (past):</td><td><?php echo implode(' / ', $rel_lctns1_exp); ?></td></tr><?php }
      if(!empty($rel_lctns1_fctn)) { ?><tr><td class="ovrvwcol1">Comprises (fictional):</td><td><?php echo implode(' / ', $rel_lctns1_fctn); ?></td></tr><?php }
      ?>
      </table></div></br>
      <?php } ?>

      <form action="" method="post">
        <fieldset>
          <div id="lctn_nm" class="entry">
            <label for="lctn_nm" class="fixedwidth">* LOCATION: <?php echo error_for('lctn_nm') ?></label>
            <input type="text" name="lctn_nm" id="lctn_nm" maxlength="255" value="<?php echo $lctn_nm; ?>" class="entryfield <?php echo errorfield('lctn_nm') ?> <?php echo errorfield('lctn_excss_lngth') ?> <?php echo errorfield('lctn_url') ?>"/>
            <h6>i.e. Knightsbridge / Verona / Soho / Eiffel Tower / Tokyo / Times Square, etc.</h6>
          </div>

          <div id="lctn_sffx_num" class="entry">
            <label for="lctn_sffx_num" class="fixedwidth">SUFFIX [1-99]: <?php echo error_for('lctn_sffx') ?></label>
            <input type="text" name="lctn_sffx_num" id="lctn_sffx_num" maxlength="2" value="<?php echo $lctn_sffx_num; ?>" class="entryfield2chars <?php echo errorfield('lctn_sffx') ?> <?php echo errorfield('lctn_excss_lngth') ?> <?php echo errorfield('lctn_url') ?>"/>
            <h6>To differentiate theatres with the same name, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 99 with no leading 0s).</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rel_lctn_list" class="entry">
            <label for="rel_lctn_list" class="fixedwidth">RELATED LOCATIONS: <?php echo error_for('rel_lctn_nm_array_excss') ?><?php echo error_for('rel_lctn_empty') ?><?php echo error_for('rel_lctn_nm_smcln_excss') ?><?php echo error_for('rel_lctn_nm_nt2_excss_lngth') ?><?php echo error_for('rel_lctn_nm_smcln') ?><?php echo error_for('rel_lctn_nm_cln_excss') ?><?php echo error_for('rel_lctn_nm_nt1_excss_lngth') ?><?php echo error_for('rel_lctn_nm_cln') ?><?php echo error_for('rel_lctn_hyphn_excss') ?><?php echo error_for('rel_lctn_sffx') ?><?php echo error_for('rel_lctn_dplct') ?><?php echo error_for('rel_lctn_nm_excss_lngth') ?><?php echo error_for('rel_lctn_nm') ?><?php echo error_for('rel_lctn_id_mtch') ?><?php echo error_for('rel_lctn_inv_comb') ?><?php echo error_for('rmvd_rel_lctn_prds') ?><?php echo error_for('rmvd_rel_lctn_pts') ?><?php echo error_for('rmvd_rel_lctn_ppl') ?><?php echo error_for('rmvd_rel_lctn_chars') ?><?php echo error_for('rmvd_rel_lctn_thtrs') ?><?php echo error_for('rmvd_rel_lctn_comps') ?></label>
            <input type="text" name="rel_lctn_list" id="rel_lctn_list" value="<?php echo $rel_lctn_list; ?>" class="entryfield <?php echo errorfield('rel_lctn_nm_array_excss') ?> <?php echo errorfield('rel_lctn_empty') ?> <?php echo errorfield('rel_lctn_nm_smcln_excss') ?> <?php echo errorfield('rel_lctn_nm_nt2_excss_lngth') ?> <?php echo errorfield('rel_lctn_nm_smcln') ?> <?php echo errorfield('rel_lctn_nm_cln_excss') ?> <?php echo errorfield('rel_lctn_nm_nt1_excss_lngth') ?> <?php echo errorfield('rel_lctn_nm_cln') ?> <?php echo errorfield('rel_lctn_hyphn_excss') ?> <?php echo errorfield('rel_lctn_sffx') ?> <?php echo errorfield('rel_lctn_dplct') ?> <?php echo errorfield('rel_lctn_nm_excss_lngth') ?> <?php echo errorfield('rel_lctn_nm') ?> <?php echo errorfield('rel_lctn_id_mtch') ?> <?php echo errorfield('rel_lctn_inv_comb') ?> <?php echo errorfield('rmvd_rel_lctn_prds') ?> <?php echo errorfield('rmvd_rel_lctn_pts') ?> <?php echo errorfield('rmvd_rel_lctn_ppl') ?> <?php echo errorfield('rmvd_rel_lctn_chars') ?> <?php echo errorfield('rmvd_rel_lctn_thtrs') ?> <?php echo errorfield('rmvd_rel_lctn_comps') ?>"/>
            <h6>i.e. Drury Lane: Covent Garden / West End / London / England / United Kingdom / Europe, etc.</br>
            - Separate multiple entries using double comma [,,].</br>
            - Add prefix note with [::] and suffix note with [;;] to explain relationship, i.e. Indian Island: Off the coast of::Devon / Skye: Scotland;;and its surrounding isles</br>
            - To differentiate identically-named locations, use a double hyphen [--] followed by an integer between 1 and 99, i.e. London--2 / Kingston--2 / Springfield--2</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="lctn_est_dt" class="entry">
            <label for="lctn_est_dt" class="fixedwidth">LOCATION ESTABLISHED DATE [DD]-[MM]-[YYYY]: <?php echo error_for('lctn_est_dt') ?></label>
            <input type="checkbox" name="lctn_est_dt_c" id="lctn_est_dt_c"<?php if($lctn_est_dt_c) {echo ' checked="checked"';} ?>/> circa
            <input type="date" name="lctn_est_dt" id="lctn_est_dt" maxlength="10" value="<?php echo $lctn_est_dt; ?>" class="entryfielddate <?php echo errorfield('lctn_est_dt') ?>"/>
            <input type="checkbox" name="lctn_est_dt_bce" id="lctn_est_dt_bce"<?php if($lctn_est_dt_bce) {echo ' checked="checked"';} ?>/> BCE</br>
            <h6>i.e. 30-12-1922</h6>
            <input type="radio" name="lctn_est_dt_frmt" value="1" <?php if($lctn_est_dt_frmt=='1' || !$lctn_est_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="lctn_est_dt_frmt" value="2" <?php if($lctn_est_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="lctn_est_dt_frmt" value="3" <?php if($lctn_est_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="lctn_est_dt_frmt" value="4" <?php if($lctn_est_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="lctn_exp_dt" class="entry">
            <label for="lctn_exp_dt" class="fixedwidth">LOCATION EXPIRED DATE [DD]-[MM]-[YYYY]: <?php echo error_for('lctn_exp_dt') ?><?php echo error_for('lctn_exp_dt_lctn_exp') ?></label>
            <input type="checkbox" name="lctn_exp_dt_c" id="lctn_exp_dt_c"<?php if($lctn_exp_dt_c) {echo ' checked="checked"';} ?>/> circa
            <input type="date" name="lctn_exp_dt" id="lctn_exp_dt" maxlength="10" value="<?php echo $lctn_exp_dt; ?>" class="entryfielddate <?php echo errorfield('lctn_exp_dt') ?> <?php echo errorfield('lctn_exp_dt_lctn_exp') ?>"/>
            <input type="checkbox" name="lctn_exp_dt_bce" id="lctn_exp_dt_bce"<?php if($lctn_exp_dt_bce) {echo ' checked="checked"';} ?>/> BCE</br>
            <h6>i.e. 26-12-1991</h6>
            <input type="radio" name="lctn_exp_dt_frmt" value="1" <?php if($lctn_exp_dt_frmt=='1' || !$lctn_exp_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="lctn_exp_dt_frmt" value="2" <?php if($lctn_exp_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="lctn_exp_dt_frmt" value="3" <?php if($lctn_exp_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="lctn_exp_dt_frmt" value="4" <?php if($lctn_exp_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="lctn_exp" class="entry">
            <label for="lctn_exp" class="fixedwidth">LOCATION EXPIRED: <?php echo error_for('lctn_exp_dt_lctn_exp') ?></label>
            <input type="checkbox" name="lctn_exp" id="lctn_exp"<?php if($lctn_exp) {echo ' checked="checked"';} ?>/>
            <h6>Check box if location is a previously existing state (or name is no longer in use).</br></h6>
          </div>

          <div id="lctn_fctn" class="entry">
            <label for="lctn_fctn" class="fixedwidth">LOCATION FICTIONAL: <?php echo error_for('lctn_fctn') ?><?php echo error_for('lctn_fctn_alt') ?></label>
            <input type="checkbox" name="lctn_fctn" id="lctn_fctn"<?php if($lctn_fctn) {echo ' checked="checked"';} ?>/>
            <h6>Check box if location is fictional (i.e. Mordor, The Bull's Head, etc.).</br></h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="sbsq_lctn_list" class="entry">
            <label for="sbsq_lctn_list" class="fixedwidth">SUBSEQUENTLY: <?php echo error_for('sbsq_lctn_nm_array_excss') ?><?php echo error_for('sbsq_lctn_empty') ?><?php echo error_for('sbsq_lctn_astrsk_excss') ?><?php echo error_for('sbsq_lctn_hyphn_excss') ?><?php echo error_for('sbsq_lctn_sffx') ?><?php echo error_for('sbsq_lctn_dplct') ?><?php echo error_for('sbsq_lctn_nm_excss_lngth') ?><?php echo error_for('sbsq_lctn_nm') ?><?php echo error_for('sbsq_lctn_id_mtch') ?><?php echo error_for('sbsq_lctn_inv_comb') ?><?php echo error_for('sbsq_lctn_non_exp') ?><?php echo error_for('sbsq_lctn_dt_mtch') ?></label>
            <input type="text" name="sbsq_lctn_list" id="sbsq_lctn_list" value="<?php echo $sbsq_lctn_list; ?>" class="entryfield <?php echo errorfield('sbsq_lctn_nm_array_excss') ?> <?php echo errorfield('sbsq_lctn_empty') ?> <?php echo errorfield('sbsq_lctn_astrsk_excss') ?> <?php echo errorfield('sbsq_lctn_hyphn_excss') ?> <?php echo errorfield('sbsq_lctn_sffx') ?> <?php echo errorfield('sbsq_lctn_dplct') ?> <?php echo errorfield('sbsq_lctn_nm_excss_lngth') ?> <?php echo errorfield('sbsq_lctn_nm') ?> <?php echo errorfield('sbsq_lctn_nonexst') ?> <?php echo errorfield('sbsq_lctn_id_mtch') ?> <?php echo errorfield('sbsq_lctn_inv_comb') ?> <?php echo errorfield('sbsq_lctn_non_exp') ?> <?php echo errorfield('sbsq_lctn_dt_mtch') ?>"/>
            <h6>Enter name(s) of location(s) by which this location was subsequently known.</br>
            - Separate multiple entries using double comma [,,].</br>
            - To differentiate identically-named locations, use a double hyphen [--] followed by an integer between 1 and 99, i.e. London--2 / Kingston--2 / Springfield--2</br>
            - To specify that location was previously only part of a larger location, suffix location with asterisk [*], or prefix if the subsequent location comprised the former.</br>
            Byzantium: Istanbul,,Constantinople</br>
            USSR: Armenia*,,Azerbaijan*,,Belarus*,,Estonia*,,Georgia*,,Kazakhstan*,,Kyrgyzstan*,,Latvia*,,Lithuania*,,Moldova*,,Russia*,,Tajikistan*,,Turkmenistan*,,Ukraine*,,Uzbekistan*</br>
            North Vietnam: *Vietnam</br>
            Russian SFSR: *USSR</h6>
          </div>

          <div id="prvs_lctn_list" class="entry">
            <label for="prvs_lctn_list" class="fixedwidth">PREVIOUSLY: <?php echo error_for('prvs_lctn_nm_array_excss') ?><?php echo error_for('prvs_lctn_empty') ?><?php echo error_for('prvs_lctn_astrsk_excss') ?><?php echo error_for('prvs_lctn_hyphn_excss') ?><?php echo error_for('prvs_lctn_sffx') ?><?php echo error_for('prvs_lctn_dplct') ?><?php echo error_for('prvs_lctn_nm_excss_lngth') ?><?php echo error_for('prvs_lctn_nm') ?><?php echo error_for('prvs_lctn_id_mtch') ?><?php echo error_for('prvs_lctn_inv_comb') ?><?php echo error_for('prvs_lctn_non_exp') ?><?php echo error_for('prvs_lctn_dt_mtch') ?></label>
            <input type="text" name="prvs_lctn_list" id="prvs_lctn_list" value="<?php echo $prvs_lctn_list; ?>" class="entryfield <?php echo errorfield('prvs_lctn_nm_array_excss') ?> <?php echo errorfield('prvs_lctn_empty') ?> <?php echo errorfield('prvs_lctn_astrsk_excss') ?> <?php echo errorfield('prvs_lctn_hyphn_excss') ?> <?php echo errorfield('prvs_lctn_sffx') ?> <?php echo errorfield('prvs_lctn_dplct') ?> <?php echo errorfield('prvs_lctn_nm_excss_lngth') ?> <?php echo errorfield('prvs_lctn_nm') ?> <?php echo errorfield('prvs_lctn_nonexst') ?> <?php echo errorfield('prvs_lctn_id_mtch') ?> <?php echo errorfield('prvs_lctn_inv_comb') ?> <?php echo errorfield('prvs_lctn_non_exp') ?> <?php echo errorfield('prvs_lctn_dt_mtch') ?>"/>
            <h6>Enter name(s) of location(s) by which this location was previously known.</br>
            - Separate multiple entries using double comma [,,].</br>
            - To differentiate identically-named locations, use a double hyphen [--] followed by an integer between 1 and 99, i.e. London--2 / Kingston--2 / Springfield--2</br>
            - To specify that location was previously only part of a larger location, suffix location with asterisk [*], or prefix if the subsequent location comprised the former.</br>
            Istanbul: Constantinople,,Byzantium</br>
            Russia: USSR*</br>
            Vietnam: *North Vietnam,,*South Vietnam</br>
            USSR: *Russian SFSR,,*Transcaucasian SFSR,,*Ukranian SSR,,*Byelorussian SSR</h6>
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
          <input type="hidden" name="lctn_id" value="<?php echo $lctn_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>