<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (person) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>PERSON:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing person.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('prsn_edit_error') ?>
      <?php echo error_for('prsn_fll_nm_excss_lngth') ?>
      <?php echo error_for('prsn_fll_nm') ?>
      <?php echo error_for('prsn_dlt') ?>
      </div>

      <form action="" method="post">
        <fieldset>
          <div id="prsn_frst_nm" class="entry">
            <label for="prsn_frst_nm" class="fixedwidth">* GIVEN NAME: <?php echo error_for('prsn_frst_nm') ?></label>
            <input type="text" name="prsn_frst_nm" id="prsn_frst_nm" maxlength="255" value="<?php echo $prsn_frst_nm; ?>" class="entryfield <?php echo errorfield('prsn_fll_nm_excss_lngth') ?> <?php echo errorfield('prsn_frst_nm') ?> <?php echo errorfield('prsn_fll_nm') ?>"/>
            <h6>i.e. Nicholas</h6>
          </div>

          <div id="prsn_lst_nm" class="entry">
            <label for="prsn_lst_nm" class="fixedwidth">FAMILY NAME: <?php echo error_for('prsn_lst_nm') ?></label>
            <input type="text" name="prsn_lst_nm" id="prsn_lst_nm" maxlength="255" value="<?php echo $prsn_lst_nm; ?>" class="entryfield <?php echo errorfield('prsn_fll_nm_excss_lngth') ?> <?php echo errorfield('prsn_lst_nm') ?> <?php echo errorfield('prsn_fll_nm') ?>"/>
            <h6>i.e. Hytner</h6>
          </div>

          <div id="prsn_sffx_num" class="entry">
            <label for="prsn_sffx_num" class="fixedwidth">SUFFIX [1-99]: <?php echo error_for('prsn_sffx') ?></label>
            <input type="text" name="prsn_sffx_num" id="prsn_sffx_num" maxlength="2" value="<?php echo $prsn_sffx_num; ?>" class="entryfield2chars <?php echo errorfield('prsn_fll_nm_excss_lngth') ?> <?php echo errorfield('prsn_fll_nm') ?> <?php echo errorfield('prsn_sffx') ?>"/>
            <h6>To differentiate people with the same name, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 99 with no leading 0s).</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="prsn_sx" class="entry">
            <label for="prsn_sx" class="fixedwidth">SEX: </label>
            <input type="radio" name="prsn_sx" value="1" <?php if($prsn_sx=='1' || !$prsn_sx) {echo ' checked="checked"';} ?>/> N/A (i.e. not yet applied)<br>
            <input type="radio" name="prsn_sx" value="2" <?php if($prsn_sx=='2') {echo ' checked="checked"';} ?>/> Male<br>
            <input type="radio" name="prsn_sx" value="3" <?php if($prsn_sx=='3') {echo ' checked="checked"';} ?>/> Female</br>
          </div>

          <div id="ethn_nm" class="entry">
            <label for="ethn_nm" class="fixedwidth">ETHNICITY: <?php echo error_for('ethn_nm') ?></label>
            <input type="text" name="ethn_nm" id="ethn_nm" maxlength="255" value="<?php echo $ethn_nm; ?>" class="entryfield <?php echo errorfield('ethn_nm') ?>"/>
            <h6>i.e. African, Afro-Caribbean, Arab, Bangladeshi, Black British, Chinese, Indian, Japanese, Jewish, Korean, Mestizo, Mulatto, Native American, Pakistani, White, White-Afro-Caribbean, White-Pakistani, Zambos, etc.</h6>
          </div>

          <div id="org_lctn_nm" class="entry">
            <label for="org_lctn_nm" class="fixedwidth">PLACE OF ORIGIN: <?php echo error_for('org_lctn_pipe_excss') ?><?php echo error_for('org_lctn_pipe') ?><?php echo error_for('org_lctn_hyphn_excss') ?><?php echo error_for('org_lctn_sffx') ?><?php echo error_for('org_lctn_hyphn') ?><?php echo error_for('org_lctn_nm_excss_lngth') ?><?php echo error_for('org_lctn_nm') ?><?php echo error_for('org_lctn_alt_list') ?><?php echo error_for('org_lctn_alt_array_excss') ?><?php echo error_for('org_lctn_alt_empty') ?><?php echo error_for('org_lctn_alt_hyphn_excss') ?><?php echo error_for('org_lctn_alt_sffx') ?><?php echo error_for('org_lctn_alt_hyphn') ?><?php echo error_for('org_lctn_alt_dplct') ?><?php echo error_for('org_lctn_alt_excss_lngth') ?><?php echo error_for('org_lctn_alt_url') ?><?php echo error_for('org_lctn_alt') ?><?php echo error_for('org_lctn_alt_fctn') ?><?php echo error_for('org_lctn_alt_assoc') ?></label>
            <input type="text" name="org_lctn_nm" id="org_lctn_nm" maxlength="255" value="<?php echo $org_lctn_nm; ?>" class="entryfield <?php echo errorfield('org_lctn_pipe_excss') ?> <?php echo errorfield('org_lctn_pipe') ?> <?php echo errorfield('org_lctn_hyphn_excss') ?> <?php echo errorfield('org_lctn_sffx') ?> <?php echo errorfield('org_lctn_hyphn') ?> <?php echo errorfield('org_lctn_nm_excss_lngth') ?> <?php echo errorfield('org_lctn_nm') ?> <?php echo errorfield('org_lctn_alt_list') ?> <?php echo errorfield('org_lctn_alt_array_excss') ?> <?php echo errorfield('org_lctn_alt_empty') ?> <?php echo errorfield('org_lctn_alt_hyphn_excss') ?> <?php echo errorfield('org_lctn_alt_sffx') ?> <?php echo errorfield('org_lctn_alt_hyphn') ?> <?php echo errorfield('org_lctn_alt_dplct') ?> <?php echo errorfield('org_lctn_alt_excss_lngth') ?> <?php echo errorfield('org_lctn_alt_url') ?> <?php echo errorfield('org_lctn_alt') ?> <?php echo errorfield('org_lctn_alt_fctn') ?> <?php echo errorfield('org_lctn_alt_assoc') ?>"/>
            <h6>i.e. Stratford-upon-Avon / Hackney / Foxrock / Leeds / Burnley, etc.</br>
            - Only list smallest denomination (of location) given:-</br>
            William Shakespeare: Stratford-upon-Avon</br>
            Harold Pinter: Hackney</br>
            - To differentiate identically-named locations, use a double hyphen followed by an integer between 1 and 99:- London--2 / Kingston--2 / Springfield--2</br>
            - When a location should be associated with specific locations (default will exclude those that are pre-existing and fictional; although fictional not an option for people), set list with double pipes [||] and separate multiple entries with double chevron [>>], i.e. Hagia Irene||Constantinople>>Turkey>>Europe / Moscow||USSR>>Europe</h6>
          </div>

          <div id="prof_list" class="entry">
            <label for="prof_list" class="fixedwidth">PROFESSION (i.e. credited roles): <?php echo error_for('prof_nm_array_excss') ?><?php echo error_for('prof_empty') ?><?php echo error_for('prof_dplct') ?><?php echo error_for('prof_nm_excss_lngth') ?><?php echo error_for('prof_nm') ?></label>
            <input type="text" name="prof_list" id="prof_list" value="<?php echo $prof_list; ?>" class="entryfield <?php echo errorfield('prof_nm_array_excss') ?> <?php echo errorfield('prof_empty') ?> <?php echo errorfield('prof_dplct') ?> <?php echo errorfield('prof_nm_excss_lngth') ?> <?php echo errorfield('prof_nm') ?>"/>
            <h6>Enter the person's credited roles.</br>
            - Separate multiple entries using double comma [,,].</br>
            i.e. Writer, Novelist, Poet, Librettist, Producer, Performer, Understudy, Director, Set Designer, Composer, Casting, Stage Management, Set Construction, Agent, etc.</br>
            Antony Sher: Writer,,Performer,,Director / Ben Ringham: Composer,,Sound Designer / Tim Crouch: Writer,,Performer,,Director / Nicholas Hytner: Producer,,Director / Lisa Spirling: Director,,Staff Director</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="agnt_list" class="entry">
            <label for="agnt_list" class="fixedwidth">REPRESENTATION: <?php echo error_for('agnt_array_excss') ?><?php echo error_for('agnt_empty') ?><?php echo error_for('agnt_pipe_excss') ?><?php echo error_for('agnt_pipe') ?><?php echo error_for('agnt_comp_cln_excss') ?><?php echo error_for('agnt_comp_hyphn_excss') ?><?php echo error_for('agnt_comp_sffx') ?><?php echo error_for('agnt_comp_hyphn') ?><?php echo error_for('agnt_comp_dplct') ?><?php echo error_for('agnt_comp_nm_excss_lngth') ?><?php echo error_for('agnt_comp_rl_excss_lngth') ?><?php echo error_for('agnt_comp_cln') ?><?php echo error_for('agnt_comp_url') ?><?php echo error_for('agnt_prsn_empty') ?><?php echo error_for('agnt_prsn_cln_excss') ?><?php echo error_for('agnt_prsn_rl_excss_lngth') ?><?php echo error_for('agnt_prsn_cln') ?><?php echo error_for('agnt_prsn_hyphn_excss') ?><?php echo error_for('agnt_prsn_sffx') ?><?php echo error_for('agnt_prsn_hyphn') ?><?php echo error_for('agnt_prsn_smcln_excss') ?><?php echo error_for('agnt_prsn_dplct') ?><?php echo error_for('agnt_prsn_excss_lngth') ?><?php echo error_for('agnt_prsn_smcln') ?><?php echo error_for('agnt_prsn_nm') ?><?php echo error_for('agnt_prsn_url') ?><?php echo error_for('agnt_prsn_nonexst') ?><?php echo error_for('agnt_prsn_id_mtch') ?><?php echo error_for('agncy_agnt_no_assoc') ?></label>
            <input type="text" name="agnt_list" id="agnt_list" value="<?php echo $agnt_list; ?>" class="entryfield <?php echo errorfield('agnt_array_excss') ?> <?php echo errorfield('agnt_empty') ?> <?php echo errorfield('agnt_pipe_excss') ?> <?php echo errorfield('agnt_pipe') ?> <?php echo errorfield('agnt_comp_cln_excss') ?> <?php echo errorfield('agnt_comp_hyphn_excss') ?> <?php echo errorfield('agnt_comp_sffx') ?> <?php echo errorfield('agnt_comp_hyphn') ?> <?php echo errorfield('agnt_comp_dplct') ?> <?php echo errorfield('agnt_comp_nm_excss_lngth') ?> <?php echo errorfield('agnt_comp_rl_excss_lngth') ?> <?php echo errorfield('agnt_comp_cln') ?> <?php echo errorfield('agnt_comp_url') ?> <?php echo errorfield('agnt_prsn_empty') ?> <?php echo errorfield('agnt_prsn_cln_excss') ?> <?php echo errorfield('agnt_prsn_rl_excss_lngth') ?> <?php echo errorfield('agnt_prsn_cln') ?> <?php echo errorfield('agnt_prsn_hyphn_excss') ?> <?php echo errorfield('agnt_prsn_sffx') ?> <?php echo errorfield('agnt_prsn_hyphn') ?> <?php echo errorfield('agnt_prsn_smcln_excss') ?> <?php echo errorfield('agnt_prsn_dplct') ?> <?php echo errorfield('agnt_prsn_excss_lngth') ?> <?php echo errorfield('agnt_prsn_smcln') ?> <?php echo errorfield('agnt_prsn_nm') ?> <?php echo errorfield('agnt_prsn_url') ?> <?php echo errorfield('agnt_prsn_nonexst') ?> <?php echo errorfield('agnt_prsn_id_mtch') ?> <?php echo errorfield('agncy_agnt_no_assoc') ?>"/>
            <h6>- Separate multiple entries using double comma [,,], type of representation using double colon [::], and (if person) given name and family name using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if agents are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - To differentiate identically-named people, use a double hyphen followed by an integer between 1 and 99:- St John;;Donald--2::Agent: Literary</br>
            Richard Stone Partnership::Agent: Acting||Meg;;Poole::Agent: Acting,,Curtis Brown::Agent: Directing (Film & TV)||Sam;;Greenwood::Agent: Directing (Film & TV)//Nish;;Panchal::Agent: Directing (Film & TV),,United Agents::Agent: Directing (Theatre)||St John;;Donald::Agent: Directing (Theatre)</br>
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
          <input type="hidden" name="prsn_id" value="<?php echo $prsn_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>