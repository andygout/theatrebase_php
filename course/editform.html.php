<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (course) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>COURSE:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing course.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('crs_edit_error') ?>
      <?php echo error_for('crs_schl_typ_yr') ?>
      <?php echo error_for('crs_dlt') ?>
      </div>
      <form action="" method="post">
        <fieldset>
          <div id="crs_schl_nm" class="entry">
            <label for="crs_schl_nm" class="fixedwidth">* COURSE SCHOOL (COMPANY): <?php echo error_for('crs_schl_nm') ?><?php echo error_for('crs_schl_excss_lngth') ?><?php echo error_for('crs_schl_url') ?></label>
            <input type="text" name="crs_schl_nm" id="crs_schl_nm" maxlength="255" value="<?php echo $crs_schl_nm; ?>" class="entryfield <?php echo errorfield('crs_schl_nm') ?> <?php echo errorfield('crs_schl_excss_lngth') ?> <?php echo errorfield('crs_schl_url') ?> <?php echo errorfield('crs_schl_typ_yr') ?>"/>
            <h6>i.e. Royal Academy of Dramatic Art (RADA) / Guildhall School of Music and Drama (GSMD), etc.</h6>
          </div>

          <div id="crs_schl_sffx_num" class="entry">
            <label for="crs_schl_sffx_num" class="fixedwidth">COURSE SCHOOL (COMPANY) SUFFIX [1-99]:   <?php echo error_for('crs_schl_sffx') ?></label>
            <input type="text" name="crs_schl_sffx_num" id="crs_schl_sffx_num" maxlength="2" value="<?php echo $crs_schl_sffx_num; ?>" class="entryfield2chars <?php echo errorfield('crs_schl_sffx') ?> <?php echo errorfield('crs_schl_excss_lngth') ?> <?php echo errorfield('crs_schl_url') ?> <?php echo errorfield('crs_schl_typ_yr') ?>"/>
            <h6>To differentiate theatres with the same name, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 99 with no leading 0s).</h6>
          </div>

          <div id="crs_typ_nm" class="entry">
            <label for="crs_typ_nm" class="fixedwidth">* COURSE TYPE: <?php echo error_for('crs_typ_nm') ?></label>
            <input type="text" name="crs_typ_nm" id="crs_typ_nm" maxlength="255" value="<?php echo $crs_typ_nm; ?>" class="entryfield <?php echo errorfield('crs_typ_nm') ?> <?php echo errorfield('crs_schl_typ_yr') ?>"/>
            <h6>i.e. 3 Year Acting / 2 Year Directing, etc.</h6>
          </div>

          <div id="crs_yr_strt" class="entry">
            <label for="crs_yr_strt" class="fixedwidth">* COURSE START YEAR [YYYY]: <?php echo error_for('crs_yr_strt') ?></label>
            <input type="text" name="crs_yr_strt" id="crs_yr_strt" maxlength="4" value="<?php echo $crs_yr_strt; ?>" class="entryfield4chars <?php echo errorfield('crs_yr_strt') ?> <?php echo errorfield('crs_schl_typ_yr') ?>"/>
            <h6>Year the course starts.</h6>
          </div>

          <div id="crs_yr_end" class="entry">
            <label for="crs_yr_end" class="fixedwidth">COURSE END YEAR [YYYY]: <?php echo error_for('crs_yr_end') ?></label>
            <input type="text" name="crs_yr_end" id="crs_yr_end" maxlength="4" value="<?php echo $crs_yr_end; ?>" class="entryfield4chars <?php echo errorfield('crs_yr_end') ?> <?php echo errorfield('crs_schl_typ_yr') ?>"/>
            <h6>Year the course ends (if course only spans one year then leave empty).</h6>
          </div>

          <div id="crs_sffx_num" class="entry">
            <label for="crs_sffx_num" class="fixedwidth">COURSE SUFFIX [1-99]: <?php echo error_for('crs_sffx') ?></label>
            <input type="text" name="crs_sffx_num" id="crs_sffx_num" maxlength="2" value="<?php echo $crs_sffx_num; ?>" class="entryfield2chars <?php echo errorfield('crs_sffx') ?> <?php echo errorfield('crs_schl_typ_yr') ?>"/>
            <h6>To differentiate theatres with the same name, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 99 with no leading 0s).</h6>
          </div>

          <div id="crs_dt_strt" class="entry">
            <label for="crs_dt_strt" class="fixedwidth">* COURSE START DATE [DD]-[MM]-[YYYY]: <?php echo error_for('crs_dt_strt') ?></label>
            <input type="date" name="crs_dt_strt" id="crs_dt_strt" maxlength="10" value="<?php echo $crs_dt_strt; ?>" class="entryfielddate <?php echo errorfield('crs_dt_strt') ?>"/>
            <h6>i.e. 17-09-2010</h6>
            <input type="radio" name="crs_dt_strt_frmt" value="1" <?php if($crs_dt_strt_frmt=='1' || !$crs_dt_strt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="crs_dt_strt_frmt" value="2" <?php if($crs_dt_strt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="crs_dt_strt_frmt" value="3" <?php if($crs_dt_strt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="crs_dt_strt_frmt" value="4" <?php if($crs_dt_strt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="crs_dt_end" class="entry">
            <label for="crs_dt_end" class="fixedwidth">* COURSE END DATE [DD]-[MM]-[YYYY]:   <?php echo error_for('crs_dt_end') ?></label>
            <input type="date" name="crs_dt_end" id="crs_dt_end" maxlength="10" value="<?php echo $crs_dt_end; ?>" class="entryfielddate <?php echo errorfield('crs_dt_end') ?>"/>
            <h6>i.e. 11-07-2013</h6>
            <input type="radio" name="crs_dt_end_frmt" value="1" <?php if($crs_dt_end_frmt=='1' || !$crs_dt_end_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="crs_dt_end_frmt" value="2" <?php if($crs_dt_end_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="crs_dt_end_frmt" value="3" <?php if($crs_dt_end_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="crs_dt_end_frmt" value="4" <?php if($crs_dt_end_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>
        </fieldset>

        <fieldset>
          <div id="cdntr_list" class="entry">
            <label for="cdntr_list" class="fixedwidth">COURSE COORDINATORS: <?php echo error_for('cdntr_rl_array_excss') ?><?php echo error_for('cdntr_empty') ?><?php echo error_for('cdntr_cln_excss') ?><?php echo error_for('cdntr_rl') ?><?php echo error_for('cdntr_comp_prsn_empty') ?><?php echo error_for('cdntr_pipe_excss') ?><?php echo error_for('cdntr_comp_tld_excss') ?><?php echo error_for('cdntr_comp_rl') ?><?php echo error_for('cdntr_comp_tld') ?><?php echo error_for('cdntr_pipe') ?><?php echo error_for('cdntr_prsn_tld_excss') ?><?php echo error_for('cdntr_prsn_rl') ?><?php echo error_for('cdntr_prsn_tld') ?><?php echo error_for('cdntr_compprsn_rl_empty') ?><?php echo error_for('cdntr_compprsn_tld_excss') ?><?php echo error_for('cdntr_compprsn_rl') ?><?php echo error_for('cdntr_compprsn_empty') ?><?php echo error_for('cdntr_compprsn_crt_excss') ?><?php echo error_for('cdntr_compprsn_sb_rl') ?><?php echo error_for('cdntr_compprsn_crt') ?><?php echo error_for('cdntr_compprsn_tld') ?><?php echo error_for('cdntr_rl_ttl_array_excss') ?><?php echo error_for('cdntr_comp_hyphn_excss') ?><?php echo error_for('cdntr_comp_sffx') ?><?php echo error_for('cdntr_comp_hyphn') ?><?php echo error_for('cdntr_comp_dplct') ?><?php echo error_for('cdntr_comp_nm_excss_lngth') ?><?php echo error_for('cdntr_comp_url') ?><?php echo error_for('cdntr_prsn_hyphn_excss') ?><?php echo error_for('cdntr_prsn_sffx') ?><?php echo error_for('cdntr_prsn_hyphn') ?><?php echo error_for('cdntr_prsn_smcln_excss') ?><?php echo error_for('cdntr_prsn_dplct') ?><?php echo error_for('cdntr_prsn_excss_lngth') ?><?php echo error_for('cdntr_prsn_smcln') ?><?php echo error_for('cdntr_prsn_nm') ?><?php echo error_for('cdntr_prsn_url') ?><?php echo error_for('cdntr_cln') ?></label>
            <input type="text" name="cdntr_list" id="cdntr_list" value="<?php echo $cdntr_list; ?>" class="entryfield <?php echo errorfield('cdntr_rl_array_excss') ?> <?php echo errorfield('cdntr_empty') ?> <?php echo errorfield('cdntr_cln_excss') ?> <?php echo errorfield('cdntr_rl') ?> <?php echo errorfield('cdntr_comp_prsn_empty') ?> <?php echo errorfield('cdntr_pipe_excss') ?> <?php echo errorfield('cdntr_comp_tld_excss') ?> <?php echo errorfield('cdntr_comp_rl') ?> <?php echo errorfield('cdntr_comp_tld') ?> <?php echo errorfield('cdntr_pipe') ?> <?php echo errorfield('cdntr_prsn_tld_excss') ?> <?php echo errorfield('cdntr_prsn_rl') ?> <?php echo errorfield('cdntr_prsn_tld') ?> <?php echo errorfield('cdntr_compprsn_rl_empty') ?> <?php echo errorfield('cdntr_compprsn_tld_excss') ?> <?php echo errorfield('cdntr_compprsn_rl') ?> <?php echo errorfield('cdntr_compprsn_empty') ?> <?php echo errorfield('cdntr_compprsn_crt_excss') ?> <?php echo errorfield('cdntr_compprsn_sb_rl') ?> <?php echo errorfield('cdntr_compprsn_crt') ?> <?php echo errorfield('cdntr_compprsn_tld') ?> <?php echo errorfield('cdntr_rl_ttl_array_excss') ?> <?php echo errorfield('cdntr_comp_hyphn_excss') ?> <?php echo errorfield('cdntr_comp_sffx') ?> <?php echo errorfield('cdntr_comp_hyphn') ?> <?php echo errorfield('cdntr_comp_dplct') ?> <?php echo errorfield('cdntr_comp_nm_excss_lngth') ?> <?php echo errorfield('cdntr_comp_url') ?> <?php echo errorfield('cdntr_prsn_hyphn_excss') ?> <?php echo errorfield('cdntr_prsn_sffx') ?> <?php echo errorfield('cdntr_prsn_hyphn') ?> <?php echo errorfield('cdntr_prsn_smcln_excss') ?> <?php echo errorfield('cdntr_prsn_dplct') ?> <?php echo errorfield('cdntr_prsn_excss_lngth') ?> <?php echo errorfield('cdntr_prsn_smcln') ?> <?php echo errorfield('cdntr_prsn_nm') ?> <?php echo errorfield('cdntr_prsn_url') ?> <?php echo errorfield('cdntr_cln') ?>"/>
            <h6>- Separate multiple entries using double comma [,,], roles using double colon [::], multiple parties within roles using double chevron [>>], optional sub-roles using double tilde [~~] (mandatory for company members), multiple people sharing company roles using double logical negation symbol [¬¬], optional credit display role (for company members only; to express the singular form of company role for credits if it is otherwise expressed in plural form (i.e. if shared role: Strings)) using double caret [^^], and (if person) given name and family name using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if people are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - To differentiate identically-named people, use a double hyphen followed by an integer between 1 and 99:- David;;Graves--2::Alderman</br>
            Course Coordinator::Guildhall School of Music and Drama (GSMD)||Alderman~~David;;Graves//Deputy Chairmen~~Deputy Chairman^^John;;Bennett¬¬Deputy Chairman^^Charles;;Wilton,,Course Coordinator::Royal Academy of Dramatic Art (RADA)||Chairman~~Sir Stephen;;Waley-Cohen//Vice Chairman~~Alan;;Rickman</h6>
          </div>

        <div id="stff_prsn_rl_list" class="entry">
            <label for="stff_prsn_rl_list" class="fixedwidth">COURSE STAFF (people):   <?php echo error_for('stff_prsn_nm_rl_array_excss') ?><?php echo error_for('stff_prsn_empty') ?><?php echo error_for('stff_prsn_rl_excss_lngth') ?><?php echo error_for('stff_prsn_cln') ?><?php echo error_for('stff_prsn_cln_excss') ?><?php echo error_for('stff_prsn_sffx') ?><?php echo error_for('stff_prsn_hyphn') ?><?php echo error_for('stff_prsn_hyphn_excss') ?><?php echo error_for('stff_prsn_excss_lngth') ?><?php echo error_for('stff_prsn_smcln') ?><?php echo error_for('stff_prsn_smcln_excss') ?><?php echo error_for('stff_prsn_dplct') ?><?php echo error_for('stff_prsn_url') ?><?php echo error_for('stff_prsn_nm') ?></label>
            <input type="text" name="stff_prsn_rl_list" id="stff_prsn_rl_list" value="<?php echo $stff_prsn_rl_list; ?>" class="entryfield <?php echo errorfield('stff_prsn_nm_rl_array_excss') ?> <?php echo errorfield('stff_prsn_empty') ?> <?php echo errorfield('stff_prsn_rl_excss_lngth') ?> <?php echo errorfield('stff_prsn_cln') ?> <?php echo errorfield('stff_prsn_cln_excss') ?> <?php echo errorfield('stff_prsn_sffx') ?> <?php echo errorfield('stff_prsn_hyphn') ?> <?php echo errorfield('stff_prsn_hyphn_excss') ?> <?php echo errorfield('stff_prsn_excss_lngth') ?> <?php echo errorfield('stff_prsn_smcln') ?> <?php echo errorfield('stff_prsn_smcln_excss') ?> <?php echo errorfield('stff_prsn_dplct') ?> <?php echo errorfield('stff_prsn_url') ?> <?php echo errorfield('stff_prsn_nm') ?>"/>
            <h6>i.e. Director of Acting, Director of MA Directing Course, etc.</br>
            - Separate multiple entries using double comma [,,], roles using double colon [::], and first name and surname using double semi-colon [;;]:-</br>
            - To differentiate identically-named people, use a double hyphen followed by an integer between 1 and 99:- Christian;;Burgess--2::Director of Drama</br>
            Christian;;Burgess::Director of Drama,,Wyn;;Jones::Director of Acting,,Patsy;;Rodenburg::Head of Voice</h6>
          </div>

          <div id="stdnt_prsn_rl_list" class="entry">
            <label for="stdnt_prsn_rl_list" class="fixedwidth">STUDENTS (people):   <?php echo error_for('stdnt_prsn_nm_rl_array_excss') ?><?php echo error_for('stdnt_prsn_empty') ?><?php echo error_for('stdnt_prsn_rl_excss_lngth') ?><?php echo error_for('stdnt_prsn_cln') ?><?php echo error_for('stdnt_prsn_cln_excss') ?><?php echo error_for('stdnt_prsn_sffx') ?><?php echo error_for('stdnt_prsn_hyphn') ?><?php echo error_for('stdnt_prsn_hyphn_excss') ?><?php echo error_for('stdnt_prsn_excss_lngth') ?><?php echo error_for('stdnt_prsn_smcln') ?><?php echo error_for('stdnt_prsn_smcln_excss') ?><?php echo error_for('stdnt_prsn_dplct') ?><?php echo error_for('stdnt_prsn_url') ?><?php echo error_for('stdnt_prsn_nm') ?></label>
            <input type="text" name="stdnt_prsn_rl_list" id="stdnt_prsn_rl_list" value="<?php echo $stdnt_prsn_rl_list; ?>" class="entryfield <?php echo errorfield('stdnt_prsn_nm_rl_array_excss') ?> <?php echo errorfield('stdnt_prsn_empty') ?> <?php echo errorfield('stdnt_prsn_rl_excss_lngth') ?> <?php echo errorfield('stdnt_prsn_cln') ?> <?php echo errorfield('stdnt_prsn_cln_excss') ?> <?php echo errorfield('stdnt_prsn_sffx') ?> <?php echo errorfield('stdnt_prsn_hyphn') ?> <?php echo errorfield('stdnt_prsn_hyphn_excss') ?> <?php echo errorfield('stdnt_prsn_excss_lngth') ?> <?php echo errorfield('stdnt_prsn_smcln') ?> <?php echo errorfield('stdnt_prsn_smcln_excss') ?> <?php echo errorfield('stdnt_prsn_dplct') ?> <?php echo errorfield('stdnt_prsn_url') ?> <?php echo errorfield('stdnt_prsn_nm') ?>"/>
            <h6>- Separate multiple entries using double comma [,,], roles using double colon [::], and first name and surname using double semi-colon [;;]:-</br>
            - To differentiate identically-named people, use a double hyphen followed by an integer between 1 and 99:- Susannah;;Fielding--2::Graduating Student</br>
            Susannah Fielding;;Fielding::Graduating Student,,Richard;;Goulding::Graduating Student</h6>
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
          <input type="hidden" name="crs_id" value="<?php echo $crs_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>