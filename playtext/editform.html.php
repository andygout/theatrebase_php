<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (playtext) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>PLAYTEXT<?php echo $coll_dsply; ?>:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing playtext.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('pt_edit_error') ?>
      <?php echo error_for('pt_nm_yr_excss_lngth') ?>
      <?php echo error_for('pt_url') ?>
      <?php echo error_for('pt_dlt') ?>
      </div>

      <form action="" method="post">
        <fieldset>
          <div id="pt_nm" class="entry">
            <label for="pt_nm" class="fixedwidth">* PLAYTEXT NAME: <?php echo error_for('pt_nm') ?></label>
            <input type="text" name="pt_nm" id="pt_nm" maxlength="255" value="<?php echo $pt_nm; ?>" class="entryfield <?php echo errorfield('pt_nm') ?> <?php echo errorfield('pt_nm_yr_excss_lngth') ?> <?php echo errorfield('pt_url') ?>"/>
            <h6>i.e. Coriolanus / The Seagull / If There Is I Haven't Found It Yet, etc.</h6>
          </div>

          <div id="pt_sbnm" class="entry">
            <label for="pt_sbnm" class="fixedwidth">PLAYTEXT SUB-NAME: <?php echo error_for('pt_sbnm_excss_lngth') ?></label>
            <input type="text" name="pt_sbnm" id="pt_sbnm" maxlength="255" value="<?php echo $pt_sbnm; ?>" class="entryfield <?php echo errorfield('pt_sbmn_excss_lngth') ?>"/>
            <h6>For any sort of sub-header to/translation of the title:</br>
            i.e. Do Unto Others: In response to The Gospel According to St Luke / A Lady of Little Sense: La Dama Boba / Sixty-Six Books: 21st Century Writers Speak To The King James Bible / Decade: Two Towers.  Ten Years.  Twenty Plays, etc.</h6>
          </div>

          <div id="mat_list" class="entry">
            <label for="mat_list" class="fixedwidth">MATERIAL: <?php echo error_for('mat_coll_wrks_checked') ?><?php echo error_for('mat_nm_frmt_array_excss') ?><?php echo error_for('mat_empty') ?><?php echo error_for('mat_hyphn_excss') ?><?php echo error_for('mat_sffx') ?><?php echo error_for('mat_hyphn') ?><?php echo error_for('mat_smcln_excss') ?><?php echo error_for('mat_dplct') ?><?php echo error_for('frmt_nm_excss_lngth') ?><?php echo error_for('frmt_url') ?><?php echo error_for('mat_nm_excss_lngth') ?><?php echo error_for('mat_url') ?><?php echo error_for('mat_smcln') ?></label>
            <input type="text" name="mat_list" id="mat_list" value="<?php echo $mat_list; ?>" class="entryfield <?php echo errorfield('mat_coll_wrks_checked') ?> <?php echo errorfield('mat_nm_frmt_array_excss') ?> <?php echo errorfield('mat_empty') ?> <?php echo errorfield('mat_hyphn_excss') ?> <?php echo errorfield('mat_sffx') ?> <?php echo errorfield('mat_hyphn') ?> <?php echo errorfield('mat_smcln_excss') ?> <?php echo errorfield('mat_dplct') ?> <?php echo errorfield('frmt_nm_excss_lngth') ?> <?php echo errorfield('frmt_url') ?> <?php echo errorfield('mat_nm_excss_lngth') ?> <?php echo errorfield('mat_url') ?> <?php echo errorfield('mat_smcln') ?>"/>
            <h6>i.e. List 'The Seagull (play)' for original text and all subsequent versions of The Seagull.</br>
            - Separate multiple entries (if applicable) using double comma [,,] and material name and format using double semi-colon [;;]:-</br>
            The Village Bike;;play,,The Seagull;;play</br>
            To differentiate identically-named materials, use a double hyphen followed by an integer between 1 and 99:-</br>
            Red;;play,,Red;;play--2</h6>
          </div>

          <div id="txt_vrsn_list" class="entry">
            <label for="txt_vrsn_list" class="fixedwidth">VERSION: <?php echo error_for('txt_vrsn_coll_wrks_checked') ?><?php echo error_for('txt_vrsn_coll_ov_checked') ?><?php echo error_for('txt_vrsn_nm_array_excss') ?><?php echo error_for('txt_vrsn_empty') ?><?php echo error_for('txt_vrsn_dplct') ?><?php echo error_for('txt_vrsn_nm_excss_lngth') ?><?php echo error_for('txt_vrsn_nm') ?></label>
            <input type="text" name="txt_vrsn_list" id="txt_vrsn_list" value="<?php echo $txt_vrsn_list; ?>" class="entryfield <?php echo errorfield('txt_vrsn_coll_wrks_checked') ?> <?php echo errorfield('txt_vrsn_coll_ov_checked') ?> <?php echo errorfield('txt_vrsn_nm_array_excss') ?> <?php echo errorfield('txt_vrsn_empty') ?> <?php echo errorfield('txt_vrsn_dplct') ?> <?php echo errorfield('txt_vrsn_nm_excss_lngth') ?> <?php echo errorfield('txt_vrsn_nm') ?>"/>
            <h6>i.e. Original Text / New Translation / New Adaptation / Abridged Text, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            New Version,,Abridged Text</h6>
          </div>

          <div id="pt_yr_strtd" class="entry">
            <label for="pt_yr_strtd" class="fixedwidth">YEAR STARTED [YYYY] (if applicable; else leave blank (or as 0)): <?php echo error_for('pt_yr_strtd') ?></label>
            <input type="checkbox" name="pt_yr_strtd_c" id="pt_yr_strtd_c"<?php if($pt_yr_strtd_c) { echo ' checked="checked"'; } ?>/> circa
            <input type="text" name="pt_yr_strtd" id="pt_yr_strtd" maxlength="4" value="<?php echo $pt_yr_strtd; ?>" class="entryfield4chars <?php echo errorfield('pt_yr_strtd') ?> <?php echo errorfield('pt_nm_yr_excss_lngth') ?> <?php echo errorfield('pt_url') ?>"/>
            <input type="checkbox" name="pt_yr_strtd_bce" id="pt_yr_strtd_bce"<?php if($pt_yr_strtd_bce) {echo ' checked="checked"'; } ?>/> BCE</br>
            <h6>i.e. 1601 (Hamlet was written 1601-02)</h6>
          </div>

          <div id="pt_yr_wrttn" class="entry">
            <label for="pt_yr_wrttn" class="fixedwidth">* YEAR WRITTEN [YYYY]: <?php echo error_for('pt_yr_wrttn') ?></label>
            <input type="checkbox" name="pt_yr_wrttn_c" id="pt_yr_wrttn_c"<?php if($pt_yr_wrttn_c) { echo ' checked="checked"'; } ?>/> circa
            <input type="text" name="pt_yr_wrttn" id="pt_yr_wrttn" maxlength="4" value="<?php echo $pt_yr_wrttn; ?>" class="entryfield4chars <?php echo errorfield('pt_yr_wrttn') ?> <?php echo errorfield('pt_nm_yr_excss_lngth') ?> <?php echo errorfield('pt_url') ?>"/>
            <input type="checkbox" name="pt_yr_wrttn_bce" id="pt_yr_wrttn_bce"<?php if($pt_yr_wrttn_bce) {echo ' checked="checked"'; } ?>/> BCE</br>
            <h6>i.e. 1602</h6>
          </div>

          <div id="pt_sffx_num" class="entry">
            <label for="pt_sffx_num" class="fixedwidth">SUFFIX [1-99]: <?php echo error_for('pt_sffx') ?></label>
            <input type="text" name="pt_sffx_num" id="pt_sffx_num" maxlength="2" value="<?php echo $pt_sffx_num; ?>" class="entryfield2chars <?php echo errorfield('pt_sffx') ?> <?php echo errorfield('pt_nm_nonalph_excss') ?> <?php echo errorfield('pt_url') ?>"/>
            <h6>To differentiate playtexts with the same name, version, year(s) and format, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 99 with no leading 0s).</h6>
          </div>

          <div id="pt_pub_dt" class="entry">
            <label for="pt_pub_dt" class="fixedwidth">PLAYTEXT PUBLICATION DATE [DD]-[MM]-[YYYY]: <?php echo error_for('pt_pub_dt') ?></label>
            <input type="date" name="pt_pub_dt" id="pt_pub_dt" maxlength="10" value="<?php echo $pt_pub_dt; ?>" class="entryfielddate <?php echo errorfield('pt_pub_dt') ?>"/>
            <h6>i.e. 21-08-2007</h6>
            <input type="radio" name="pt_pub_dt_frmt" value="1" <?php if($pt_pub_dt_frmt=='1' || !$pt_pub_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="pt_pub_dt_frmt" value="2" <?php if($pt_pub_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="pt_pub_dt_frmt" value="3" <?php if($pt_pub_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="pt_pub_dt_frmt" value="4" <?php if($pt_pub_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>
        </fieldset>

        <fieldset>
          <div id="pt_coll" class="entry">
            <label for="pt_coll" class="fixedwidth">COLLECTIONS: </label><?php echo error_for('coll_ov_assoc') ?><?php echo error_for('coll_wrks_assoc') ?><?php echo error_for('coll_wrks_prd_assoc') ?>
            <input type="radio" name="pt_coll" value="1" <?php if($pt_coll=='1' || !$pt_coll) {echo ' checked="checked"';} ?>/> Collection N/A (i.e. none of below apply)<br>
            <input type="radio" name="pt_coll" value="2" <?php if($pt_coll=='2') {echo ' checked="checked"';} ?>/> Collected Works<br>
            <input type="radio" name="pt_coll" value="3" <?php if($pt_coll=='3') {echo ' checked="checked"';} ?>/> Collection Overview<br>
            <input type="radio" name="pt_coll" value="4" <?php if($pt_coll=='4') {echo ' checked="checked"';} ?>/> Collection Segment<br>
          </div>

          <div id="pt_wrks_sg_list" class="entry">
            <label for="pt_wrks_sg_list" class="fixedwidth">COLLECTED WORKS SEGMENTS (only if playtext is a collected works): <?php echo error_for('wrks_sg_ov_unchckd') ?><?php echo error_for('wrks_sg_sbhdr_pt_array_excss') ?><?php echo error_for('wrks_sg_sbhdr_pt_empty') ?><?php echo error_for('wrks_sg_eql_excss') ?><?php echo error_for('wrks_sbhdr_excss_lngth') ?><?php echo error_for('wrks_sg_eql') ?><?php echo error_for('wrks_sg_sbhdr') ?><?php echo error_for('wrks_sg_pts_array_excss') ?><?php echo error_for('wrks_sg_empty') ?><?php echo error_for('wrks_sg_cln_excss') ?><?php echo error_for('wrks_sg_cln') ?><?php echo error_for('wrks_sg_rl_excss_lngth') ?><?php echo error_for('wrks_sg_pts_array_excss') ?><?php echo error_for('wrks_sg_hyphn_excss') ?><?php echo error_for('wrks_sg_sffx') ?><?php echo error_for('wrks_sg_hyphn') ?><?php echo error_for('wrks_sg_hsh_excss') ?><?php echo error_for('wrks_sg_yr') ?><?php echo error_for('wrks_sg_yr_frmt') ?><?php echo error_for('wrks_sg_hsh') ?><?php echo error_for('wrks_sg_dplct') ?><?php echo error_for('wrks_sg_nm_yr_excss_lngth') ?><?php echo error_for('wrks_sg_url') ?><?php echo error_for('wrks_sg_nonexst') ?><?php echo error_for('wrks_sg_id_mtch') ?><?php echo error_for('wrks_chckd') ?><?php echo error_for('wrks_lnk_assoc') ?><?php echo error_for('wrks_sg_lnk_assoc') ?><?php echo error_for('wrks_coll_ov_sg_exst') ?></label>
            <input type="text" name="pt_wrks_sg_list" id="pt_wrks_sg_list" value="<?php echo $pt_wrks_sg_list; ?>" class="entryfield <?php echo errorfield('wrks_sg_ov_unchckd') ?> <?php echo errorfield('wrks_sg_sbhdr_pt_array_excss') ?> <?php echo errorfield('wrks_sg_sbhdr_pt_empty') ?> <?php echo errorfield('wrks_sg_eql_excss') ?> <?php echo errorfield('wrks_sbhdr_excss_lngth') ?> <?php echo errorfield('wrks_sg_eql') ?> <?php echo errorfield('wrks_sg_sbhdr') ?> <?php echo errorfield('wrks_sg_pts_array_excss') ?> <?php echo errorfield('wrks_sg_empty') ?> <?php echo errorfield('wrks_sg_cln_excss') ?> <?php echo errorfield('wrks_sg_cln') ?> <?php echo errorfield('wrks_sg_rl_excss_lngth') ?> <?php echo errorfield('wrks_sg_pts_array_excss') ?> <?php echo errorfield('wrks_sg_hyphn_excss') ?> <?php echo errorfield('wrks_sg_sffx') ?> <?php echo errorfield('wrks_sg_hyphn') ?> <?php echo errorfield('wrks_sg_hsh_excss') ?> <?php echo errorfield('wrks_sg_yr') ?> <?php echo errorfield('wrks_sg_yr_frmt') ?> <?php echo errorfield('wrks_sg_hsh') ?> <?php echo errorfield('wrks_sg_dplct') ?> <?php echo errorfield('wrks_sg_nm_yr_excss_lngth') ?> <?php echo errorfield('wrks_sg_url') ?> <?php echo errorfield('wrks_sg_nonexst') ?> <?php echo errorfield('wrks_sg_id_mtch') ?> <?php echo errorfield('wrks_chckd') ?> <?php echo errorfield('wrks_lnk_assoc') ?> <?php echo errorfield('wrks_sg_lnk_assoc') ?> <?php echo errorfield('wrks_coll_ov_sg_exst') ?>"/>
            <h6>Enter playtexts for each production that comprises this collection.</br>
            NB: Collected works are collated after publication.</br>
            - Separate multiple entries using double comma [,,], optional note using double colon [::], and year written using double hash [##] (years to adhere to below format):-</br>
            Playtext Name##c(optional circa indication)-(option BCE indication) Year Started (format: YYYY)(if additional Year Started required:);;c(optional circa indication)-(option BCE indication) Year Written (format: YYYY)--(optional suffix [1-99])</br>
            i.e. Hamlet##1601;;1602 / The Seagull##1997--2 / Oedipus The King##c-429</br>
            TIM CROUCH PLAYS ONE: My Arm##2003,,An Oak Tree##2005,,England##2007,,The Author##2009</br>
            MODERN MONOLOGUES FOR WOMEN VOL 2: Part One: Teens==Apples##2010::Claire,,DNA##2008::Leah@@Part Two: Twenties==Muswell Hill##2012::Annie,,Many Moons##2011::Juniper</h6>
          </div>

          <div id="pt_coll_sg_list" class="entry">
            <label for="pt_coll_sg_list" class="fixedwidth">COLLECTION SEGMENTS (only if playtext is a collection overview): <?php echo error_for('coll_sg_ov_unchckd') ?><?php echo error_for('coll_sg_sbhdr_pt_array_excss') ?><?php echo error_for('coll_sg_sbhdr_pt_empty') ?><?php echo error_for('coll_sg_eql_excss') ?><?php echo error_for('coll_sbhdr_excss_lngth') ?><?php echo error_for('coll_sg_eql') ?><?php echo error_for('coll_sg_sbhdr') ?><?php echo error_for('coll_sg_pts_array_excss') ?><?php echo error_for('coll_sg_empty') ?><?php echo error_for('coll_sg_hyphn_excss') ?><?php echo error_for('coll_sg_sffx') ?><?php echo error_for('coll_sg_hyphn') ?><?php echo error_for('coll_sg_hsh_excss') ?><?php echo error_for('coll_sg_yr') ?><?php echo error_for('coll_sg_yr_frmt') ?><?php echo error_for('coll_sg_hsh') ?><?php echo error_for('coll_sg_dplct') ?><?php echo error_for('coll_sg_nm_yr_excss_lngth') ?><?php echo error_for('coll_sg_url') ?><?php echo error_for('coll_sg_nonexst') ?><?php echo error_for('coll_sg_id_mtch') ?><?php echo error_for('coll_sg_assoc') ?><?php echo error_for('coll_sg_unchckd') ?><?php echo error_for('coll_lnk_assoc') ?><?php echo error_for('coll_sg_lnk_assoc') ?></label>
            <input type="text" name="pt_coll_sg_list" id="pt_coll_sg_list" value="<?php echo $pt_coll_sg_list; ?>" class="entryfield <?php echo errorfield('coll_sg_ov_unchckd') ?> <?php echo errorfield('coll_sg_sbhdr_pt_array_excss') ?> <?php echo errorfield('coll_sg_sbhdr_pt_empty') ?> <?php echo errorfield('coll_sg_eql_excss') ?> <?php echo errorfield('coll_sbhdr_excss_lngth') ?> <?php echo errorfield('coll_sg_eql') ?> <?php echo errorfield('coll_sg_sbhdr') ?> <?php echo errorfield('coll_sg_pts_array_excss') ?> <?php echo errorfield('coll_sg_empty') ?> <?php echo errorfield('coll_sg_hyphn_excss') ?> <?php echo errorfield('coll_sg_sffx') ?> <?php echo errorfield('coll_sg_hyphn') ?> <?php echo errorfield('coll_sg_hsh_excss') ?> <?php echo errorfield('coll_sg_yr') ?> <?php echo errorfield('coll_sg_yr_frmt') ?> <?php echo errorfield('coll_sg_hsh') ?> <?php echo errorfield('coll_sg_dplct') ?> <?php echo errorfield('coll_sg_nm_yr_excss_lngth') ?> <?php echo errorfield('coll_sg_url') ?> <?php echo errorfield('coll_sg_nonexst') ?> <?php echo errorfield('coll_sg_id_mtch') ?> <?php echo errorfield('coll_sg_assoc') ?> <?php echo errorfield('coll_sg_unchckd') ?> <?php echo errorfield('coll_lnk_assoc') ?> <?php echo errorfield('coll_sg_lnk_assoc') ?>"/>
            <h6>Enter playtexts for each production that comprises this collection.</br>
            NB: Collections are initially intended to be published collectively.</br>
            - Separate multiple entries using double comma [,,], optional note using double colon [::], and year written using double hash [##] (years to adhere to below format):-</br>
            Playtext Name##c(optional circa indication)-(option BCE indication) Year Started (format: YYYY)(if additional Year Started required:);;c(optional circa indication)-(option BCE indication) Year Written (format: YYYY)--(optional suffix [1-99])</br>
            i.e. Hamlet##1601;;1602 / The Seagull##1997--2 / Oedipus The King##c-429</br>
            THE COAST OF UTOPIA: Voyage##2002,,Shipwreck##2002,,Salvage##2002</br>
            SIXTY-SIX BOOKS: Old Testament==Do Unto Others##2012,,All The Trees of The Field##2012,,The Loss Of All Things##2012,,The Rules##2012@@New Testament==Titus Sermon##2012,,When We Praise##2012,,Amos The Shepherd Curses The Rulers of Ancient Israel##2012,,The Crossing##2012</br>
            TONIGHT AT 8:30: Cocktails==We Were Dancing##1935;;1936,,The Astonished Heart##1935;;1936,,Red Peppers##1935;;1936@@Dinner==Ways And Means##1935;;1936,,Fumed Oak##1935;;1936,,Still Life##1935;;1936@@Dancing==Family Album##1935;;1936,,Hands Across The Sea##1935;;1936,,Shadow Play##1935;;1936</br>
            THE BOMB: First Blast: Proliferation==From Elsewhere: The Message##2012,,Calculated Risk##2012,,Seven Joys##2012,,Option##2012,,Little Russians##2012@@Second Blast: Present Dangers==There Was A Man. There Was No Man##2012,,Axis##2012,,Talk Talk Fight Fight##2012,,The Letter Of Last Resort##2012,,From Elsewhere: On The Watch##2012</br>
            CHARGED: Cycle 1==Fatal Light##2010,,Taken##2010,,Dream Pill##2010@@Cycle 2==E V Crowe##2010,,Dancing Bears##2010,,That Almost Unnameable Lust##2010</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="pt_lnk_list" class="entry">
            <label for="pt_lnk_list" class="fixedwidth">LINK PLAYTEXTS: <?php echo error_for('lnk_empty') ?><?php echo error_for('lnk_hyphn_excss') ?><?php echo error_for('lnk_sffx') ?><?php echo error_for('lnk_hyphn') ?><?php echo error_for('lnk_hsh_excss') ?><?php echo error_for('lnk_yr') ?><?php echo error_for('lnk_yr_frmt') ?><?php echo error_for('lnk_hsh') ?><?php echo error_for('lnk_dplct') ?><?php echo error_for('lnk_nm_yr_excss_lngth') ?><?php echo error_for('lnk_url') ?><?php echo error_for('lnk_nonexst') ?><?php echo error_for('lnk_id_mtch') ?><?php echo error_for('lnk_wrks_assoc') ?><?php echo error_for('lnk_wrks_lnk_assoc') ?><?php echo error_for('lnk_coll_assoc') ?><?php echo error_for('lnk_coll_lnk_assoc') ?><?php echo error_for('lnk_wrks_ov_sg_exst') ?><?php echo error_for('lnk_coll_ov_sg_exst') ?></label>
            <input type="text" name="pt_lnk_list" id="pt_lnk_list" value="<?php echo $pt_lnk_list; ?>" class="entryfield <?php echo errorfield('lnk_empty') ?> <?php echo errorfield('lnk_hyphn_excss') ?> <?php echo errorfield('lnk_sffx') ?> <?php echo errorfield('lnk_hyphn') ?> <?php echo errorfield('lnk_hsh_excss') ?> <?php echo errorfield('lnk_yr') ?> <?php echo errorfield('lnk_yr_frmt') ?> <?php echo errorfield('lnk_hsh') ?> <?php echo errorfield('lnk_dplct') ?> <?php echo errorfield('lnk_nm_yr_excss_lngth') ?> <?php echo errorfield('lnk_url') ?> <?php echo errorfield('lnk_nonexst') ?> <?php echo errorfield('lnk_id_mtch') ?> <?php echo errorfield('lnk_wrks_assoc') ?> <?php echo errorfield('lnk_wrks_lnk_assoc') ?> <?php echo errorfield('lnk_coll_assoc') ?> <?php echo errorfield('lnk_coll_lnk_assoc') ?> <?php echo errorfield('lnk_wrks_ov_sg_exst') ?> <?php echo errorfield('lnk_coll_ov_sg_exst') ?>"/>
            <h6>Enter playtexts for each other playtext to which this playtext is linked.</br>
            - Separate multiple entries using double comma [,,], optional note using double colon [::], and year written using double hash [##] (years to adhere to below format):-</br>
            Playtext Name##c(optional circa indication)-(option BCE indication) Year Started (format: YYYY)(if additional Year Started required:);;c(optional circa indication)-(option BCE indication) Year Written (format: YYYY)--(optional suffix [1-99])
            i.e. Hamlet##1601;;1602 / The Seagull##1997--2 / Oedipus The King##c-429</br>
            Women, Power and Politics: Then##2010</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="wri_list" class="entry">
            <label for="wri_list" class="fixedwidth">WRITERS: <?php echo error_for('wri_coll_wrks_checked') ?><?php echo error_for('wri_rl_array_excss') ?><?php echo error_for('wri_empty') ?><?php echo error_for('wri_pls_excss') ?><?php echo error_for('src_mat_cln_excss') ?><?php echo error_for('src_mat_rl') ?><?php echo error_for('src_mats_array_excss') ?><?php echo error_for('src_mat_empty') ?><?php echo error_for('src_mat_hyphn_excss') ?><?php echo error_for('src_mat_sffx') ?><?php echo error_for('src_mat_hyphn') ?><?php echo error_for('src_mat_smcln_excss') ?><?php echo error_for('src_mat_dplct') ?><?php echo error_for('src_frmt_nm_excss_lngth') ?><?php echo error_for('src_frmt_url') ?><?php echo error_for('src_mat_nm_excss_lngth') ?><?php echo error_for('src_mat_url') ?><?php echo error_for('src_mat_smcln') ?><?php echo error_for('src_mat_cln') ?><?php echo error_for('wri_pls') ?><?php echo error_for('wri_cln_excss') ?><?php echo error_for('wri_rl') ?><?php echo error_for('wri_comp_prsn_empty') ?><?php echo error_for('wri_pipe_excss') ?><?php echo error_for('wri_pipe') ?><?php echo error_for('wri_prsn_empty') ?><?php echo error_for('wri_rl_ttl_array_excss') ?><?php echo error_for('wri_comp_tld_excss') ?><?php echo error_for('wri_comp_rl') ?><?php echo error_for('wri_comp_tld') ?><?php echo error_for('wri_comp_hyphn_excss') ?><?php echo error_for('wri_comp_sffx') ?><?php echo error_for('wri_comp_hyphn') ?><?php echo error_for('wri_comp_dplct') ?><?php echo error_for('wri_comp_nm_excss_lngth') ?><?php echo error_for('wri_comp_url') ?><?php echo error_for('wri_prsn_tld_excss') ?><?php echo error_for('wri_prsn_rl') ?><?php echo error_for('wri_prsn_tld') ?><?php echo error_for('wri_prsn_hyphn_excss') ?><?php echo error_for('wri_prsn_sffx') ?><?php echo error_for('wri_prsn_hyphn') ?><?php echo error_for('wri_prsn_smcln_excss') ?><?php echo error_for('wri_prsn_dplct') ?><?php echo error_for('wri_prsn_excss_lngth') ?><?php echo error_for('wri_prsn_smcln') ?><?php echo error_for('wri_prsn_nm') ?><?php echo error_for('wri_prsn_url') ?><?php echo error_for('wri_cln') ?></label>
            <input type="text" name="wri_list" id="wri_list" value="<?php echo $wri_list; ?>" class="entryfield <?php echo errorfield('wri_coll_wrks_checked') ?> <?php echo errorfield('wri_rl_array_excss') ?> <?php echo errorfield('wri_empty') ?> <?php echo errorfield('wri_pls_excss') ?> <?php echo errorfield('src_mat_cln_excss') ?> <?php echo errorfield('src_mat_rl') ?> <?php echo errorfield('src_mats_array_excss') ?> <?php echo errorfield('src_mat_empty') ?> <?php echo errorfield('src_mat_hyphn_excss') ?> <?php echo errorfield('src_mat_sffx') ?> <?php echo errorfield('src_mat_hyphn') ?> <?php echo errorfield('src_mat_smcln_excss') ?> <?php echo errorfield('src_mat_dplct') ?> <?php echo errorfield('src_frmt_nm_excss_lngth') ?> <?php echo errorfield('src_frmt_url') ?> <?php echo errorfield('src_mat_nm_excss_lngth') ?> <?php echo errorfield('src_mat_url') ?> <?php echo errorfield('src_mat_smcln') ?> <?php echo errorfield('src_mat_cln') ?> <?php echo errorfield('wri_pls') ?> <?php echo errorfield('wri_cln_excss') ?> <?php echo errorfield('wri_rl') ?> <?php echo errorfield('wri_comp_prsn_empty') ?> <?php echo errorfield('wri_pipe_excss') ?> <?php echo errorfield('wri_pipe') ?> <?php echo errorfield('wri_prsn_empty') ?> <?php echo errorfield('wri_rl_ttl_array_excss') ?> <?php echo errorfield('wri_comp_tld_excss') ?> <?php echo errorfield('wri_comp_rl') ?> <?php echo errorfield('wri_comp_tld') ?> <?php echo errorfield('wri_comp_hyphn_excss') ?> <?php echo errorfield('wri_comp_sffx') ?> <?php echo errorfield('wri_comp_hyphn') ?> <?php echo errorfield('wri_comp_dplct') ?> <?php echo errorfield('wri_comp_nm_excss_lngth') ?> <?php echo errorfield('wri_comp_url') ?> <?php echo errorfield('wri_prsn_tld_excss') ?> <?php echo errorfield('wri_prsn_rl') ?> <?php echo errorfield('wri_prsn_tld') ?> <?php echo errorfield('wri_prsn_hyphn_excss') ?> <?php echo errorfield('wri_prsn_sffx') ?> <?php echo errorfield('wri_prsn_hyphn') ?> <?php echo errorfield('wri_prsn_smcln_excss') ?> <?php echo errorfield('wri_prsn_dplct') ?> <?php echo errorfield('wri_prsn_excss_lngth') ?> <?php echo errorfield('wri_prsn_smcln') ?> <?php echo errorfield('wri_prsn_nm') ?> <?php echo errorfield('wri_prsn_url') ?> <?php echo errorfield('wri_cln') ?>"/>
            <h6>i.e. Written by / Adapted by / Translation by / Literal translation by / Source Material by / Additional material by / Abridgment by / Revisions by / Text by (devised) / Music by or Music co-written by (musical) / Lyrics by (musical) / Book by (musical) / Libretto by (opera) / Edited by / Devised by / Created by / Conceived by, etc.</br>
            - Separate multiple entries using double comma [,,], roles using double colon [::], multiple parties within roles using double chevron [>>], optional sub-roles using double tilde [~~], and (if person) given name and family name using double semi-colon [;;]</br>
            - To establish source material(s) as part of a role, use double plus [++], separate its role using double colon [::], multiple materials within roles using double chevron [>>], and material name and format using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if people are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - Indicate if rights grantors using [***]; if source material writers using [**]; if original writer (i.e. if a new version of their work) using [*]; no indication required if new writer.</br>
            - To differentiate identically-named people or companies, use a double hyphen followed by an integer between 1 and 99:- Written by::Anton;;Chekhov--2</br>
            New adaptation by::Tom;;Stoppard,,Written by::Anton;;Chekhov,,Created by::Theatre du Complicite||Co-Created by~~Simon;;McBurney//Co-Created by~~Annabel;;Arden//Co-Created by~~Marcello;;Magni</br>
            Written by::Penelope;;Skinner>>Jack;;Thorne>>Moira;;Buffini>>Matt;;Charman</br>
            Written by::Anton;;Chekhov*>>in a new version by~~Anya;;Reiss</br>
            Based on::Let The Right One In;;novel>>Let The Right One In;;screenplay++by::John Ajvide;;Lindqvist**,,Stage adaptation by::Jack;;Thorne</br>
            Based on::Fatal Attraction;;motion picture++produced by::Paramount Pictures Corporation**||>>and written by~~James;;Dearden**,,Stage adaptation by::James;;Dearden</br>
            By::Graham;;Linehan,,From the::The Ladykillers;;motion picture screenplay++by::William;;Rose**,,By special arrangement with::StudioCanal***||</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="cntr_list" class="entry">
            <label for="cntr_list" class="fixedwidth">CONTRIBUTORS: <?php echo error_for('cntr_rl_array_excss') ?><?php echo error_for('cntr_empty') ?><?php echo error_for('cntr_cln_excss') ?><?php echo error_for('cntr_rl') ?><?php echo error_for('cntr_cln') ?><?php echo error_for('cntr_comp_prsn_empty') ?><?php echo error_for('cntr_pipe_excss') ?><?php echo error_for('cntr_pipe') ?><?php echo error_for('cntr_prsn_empty') ?><?php echo error_for('cntr_rl_ttl_array_excss') ?><?php echo error_for('cntr_comp_tld_excss') ?><?php echo error_for('cntr_comp_rl') ?><?php echo error_for('cntr_comp_tld') ?><?php echo error_for('cntr_comp_hyphn_excss') ?><?php echo error_for('cntr_comp_sffx') ?><?php echo error_for('cntr_comp_hyphn') ?><?php echo error_for('cntr_comp_dplct') ?><?php echo error_for('cntr_comp_nm_excss_lngth') ?><?php echo error_for('cntr_comp_url') ?><?php echo error_for('cntr_prsn_tld_excss') ?><?php echo error_for('cntr_prsn_rl') ?><?php echo error_for('cntr_prsn_tld') ?><?php echo error_for('cntr_prsn_hyphn_excss') ?><?php echo error_for('cntr_prsn_sffx') ?><?php echo error_for('cntr_prsn_hyphn') ?><?php echo error_for('cntr_prsn_smcln_excss') ?><?php echo error_for('cntr_prsn_dplct') ?><?php echo error_for('cntr_prsn_excss_lngth') ?><?php echo error_for('cntr_prsn_smcln') ?><?php echo error_for('cntr_prsn_nm') ?><?php echo error_for('cntr_prsn_url') ?></label>
            <input type="text" name="cntr_list" id="cntr_list" value="<?php echo $cntr_list; ?>" class="entryfield <?php echo errorfield('cntr_rl_array_excss') ?> <?php echo errorfield('cntr_empty') ?> <?php echo errorfield('cntr_cln_excss') ?> <?php echo errorfield('cntr_rl') ?> <?php echo errorfield('cntr_cln') ?> <?php echo errorfield('cntr_comp_prsn_empty') ?> <?php echo errorfield('cntr_pipe_excss') ?> <?php echo errorfield('cntr_pipe') ?> <?php echo errorfield('cntr_prsn_empty') ?> <?php echo errorfield('cntr_rl_ttl_array_excss') ?> <?php echo errorfield('cntr_comp_tld_excss') ?> <?php echo errorfield('cntr_comp_rl') ?> <?php echo errorfield('cntr_comp_tld') ?> <?php echo errorfield('cntr_comp_hyphn_excss') ?> <?php echo errorfield('cntr_comp_sffx') ?> <?php echo errorfield('cntr_comp_hyphn') ?> <?php echo errorfield('cntr_comp_dplct') ?> <?php echo errorfield('cntr_comp_nm_excss_lngth') ?> <?php echo errorfield('cntr_comp_url') ?> <?php echo errorfield('cntr_prsn_tld_excss') ?> <?php echo errorfield('cntr_prsn_rl') ?> <?php echo errorfield('cntr_prsn_tld') ?> <?php echo errorfield('cntr_prsn_hyphn_excss') ?> <?php echo errorfield('cntr_prsn_sffx') ?> <?php echo errorfield('cntr_prsn_hyphn') ?> <?php echo errorfield('cntr_prsn_smcln_excss') ?> <?php echo errorfield('cntr_prsn_dplct') ?> <?php echo errorfield('cntr_prsn_excss_lngth') ?> <?php echo errorfield('cntr_prsn_smcln') ?> <?php echo errorfield('cntr_prsn_nm') ?> <?php echo errorfield('cntr_prsn_url') ?>"/>
            <h6>i.e. Introduction by / Foreword by / Afterword by / Edited by, etc.</br>
            - Separate multiple entries using double comma [,,], roles using double colon [::], multiple parties within roles using double chevron [>>], optional sub-roles using double tilde [~~], and (if person) given name and family name using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if people are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - To differentiate identically-named people or companies, use a double hyphen followed by an integer between 1 and 99:- Written by::Anton;;Chekhov--2</br>
            Edited by::Mark;;Subias,,With introductions by::André;;Bishop>>Edward;;Albee>>Christopher;;Durang>>Douglas;;Wright</br>
            Introduction by::Company of Angels||Artistic Director~~John;;Retallack</br>
            Edited and introduced by::Lynette;;Goddard</br>
            Foreword by::Christopher;;Haydon>>Rachel;;Holmes>>Josie;;Rourke</br>
          </div>
        </fieldset>

        <fieldset>
          <div id="ctgry_list" class="entry">
            <label for="ctgry_list" class="fixedwidth">CATEGORY: <?php echo error_for('ctgry_nm_array_excss') ?><?php echo error_for('ctgry_empty') ?><?php echo error_for('ctgry_dplct') ?><?php echo error_for('ctgry_nm_excss_lngth') ?><?php echo error_for('ctgry_nm') ?></label>
            <input type="text" name="ctgry_list" id="ctgry_list" value="<?php echo $ctgry_list; ?>" class="entryfield <?php echo errorfield('ctgry_nm_array_excss') ?> <?php echo errorfield('ctgry_empty') ?> <?php echo errorfield('ctgry_dplct') ?> <?php echo errorfield('ctgry_nm_excss_lngth') ?> <?php echo errorfield('ctgry_nm') ?>"/>
            <h6>i.e. Play / Musical / Libretto / Monologue / One Act Play / Puppetry / Poetry / Pantomime / Play with Music / Revue / Scenes / Short Play / Collection / Trilogy / Collected Works / Misc., etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            One Act Play,,Play with Music</h6>
          </div>

          <div id="gnr_list" class="entry">
            <label for="gnr_list" class="fixedwidth">GENRE: <?php echo error_for('gnr_nm_array_excss') ?><?php echo error_for('gnr_empty') ?><?php echo error_for('gnr_dplct') ?><?php echo error_for('gnr_nm_excss_lngth') ?><?php echo error_for('gnr_nm') ?></label>
            <input type="text" name="gnr_list" id="gnr_list" value="<?php echo $gnr_list; ?>" class="entryfield <?php echo errorfield('gnr_nm_array_excss') ?> <?php echo errorfield('gnr_empty') ?> <?php echo errorfield('gnr_dplct') ?> <?php echo errorfield('gnr_nm_excss_lngth') ?> <?php echo errorfield('gnr_nm') ?>"/>
            <h6>i.e. Theatre of the Absurd / African-American drama / African drama / Alternative theatre / American drama / Ancient Greek drama / Ancient Roman drama / Asian drama / Australian drama / Austrian drama / Avante-garde theatre / Black theatre / British drama / Canadian drama / Caribbean drama / Caroline theatre / Children's theatre / City comedy / Classical / Comedy / Comedy of manners / Commedia dell-Arte / Community theatre / Theatre of Cruelty / Czech drama /
            Dance drama / Documentary theatre / Drama / Elizabethan theatre / English drama / Ensemble / Epic theatre / European drama / Expressionism / Farce / Feminist theatre / French drama / Future history / Gay theatre / German drama / Greek drama / Greek tragedy / History play / Hungarian drama / In-yer-face theatre / Irish drama / Italian drama / Jacobean theatre / Jacobean revenge tragedy / Japanese drama / Kitchen sink drama / Latin American drama / Melodrama / Middle Eastern drama / Modernist drama / Morality play / Murder mystery /
            Mystery / Myth / Naturalistic drama / Northern Irish drama / Norwegian drama / Theatre of the Oppressed / Parody / Physical theatre / Political theatre / Poor theatre / Popular theatre / Post-colonial drama / Post-dramatic theatre / Postmodern theatre / Restoration comedy / Revenge tragedy / Ritual drama / Russian drama / Satire / Scottish drama / Shakespearean comedy / Shakespearean history / Shakespearean problem play / Shakespearean tragedy / Site-specific /
            Southern Gothic / Spanish drama / Street theatre / Sturm und Drang / Surrealist drama / Swedish drama / Symbolist drama / Thriller / Tragedy / Tragicomedy / Verbatim theatre  / Welsh drama / Youth theatre, etc.</br>
            - Separate multiple entries using double comma [,,]:-
            Drama,,Verbatim theatre</h6>
          </div>

          <div id="ftr_list" class="entry">
            <label for="ftr_list" class="fixedwidth">FEATURES: <?php echo error_for('ftr_nm_array_excss') ?><?php echo error_for('ftr_empty') ?><?php echo error_for('ftr_dplct') ?><?php echo error_for('ftr_nm_excss_lngth') ?><?php echo error_for('ftr_nm') ?></label>
            <input type="text" name="ftr_list" id="ftr_list" value="<?php echo $ftr_list; ?>" class="entryfield <?php echo errorfield('ftr_nm_array_excss') ?> <?php echo errorfield('ftr_empty') ?> <?php echo errorfield('ftr_dplct') ?> <?php echo errorfield('ftr_nm_excss_lngth') ?> <?php echo errorfield('ftr_nm') ?>"/>
            <h6>i.e. Modernised Setting / Alternate Setting / Radical Interpretation / Site Specific / Foreign Language / Spanish Language / Sign Language, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            All Male Cast,,Site Specific</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="thm_list" class="entry">
            <label for="thm_list" class="fixedwidth">THEMES: <?php echo error_for('thm_nm_array_excss') ?><?php echo error_for('thm_empty') ?><?php echo error_for('thm_dplct') ?><?php echo error_for('thm_nm_excss_lngth') ?><?php echo error_for('thm_nm') ?></label>
            <input type="text" name="thm_list" id="thm_list" value="<?php echo $thm_list; ?>" class="entryfield <?php echo errorfield('thm_nm_array_excss') ?> <?php echo errorfield('thm_empty') ?> <?php echo errorfield('thm_dplct') ?> <?php echo errorfield('thm_nm_excss_lngth') ?> <?php echo errorfield('thm_nm') ?>"/>
            <h6>i.e. Global Warming / Regicide / Unrequited Love / Postnatal Depression / Russian Revolution / Genocide / Cultural Identity / Homophobia / World War I / World War II / 9/11 / AIDS crisis / Thatcherism / Vietnam War, etc.</br>
            - Separate multiple entries using double comma [,,] (and list most specific theme as broader themes can later be applied):-</br>
            Genocide,,Cultural Identity</h6>
          </div>

          <div id="sttng_list" class="entry">
            <label for="sttng_list" class="fixedwidth">SETTING (TIME / LOCATION / PLACE): <?php echo error_for('sttng_tr_lg_chckd') ?><?php echo error_for('sttng_array_excss') ?><?php echo error_for('sttng_empty') ?> <?php echo error_for('sttng_pls_excss') ?><?php echo error_for('sttng_plc_array_excss') ?><?php echo error_for('sttng_plc_empty') ?><?php echo error_for('sttng_plc_hsh') ?><?php echo error_for('sttng_plc_smcln_excss') ?><?php echo error_for('sttng_plc_nt2_excss_lngth') ?><?php echo error_for('sttng_plc_nt2_cln') ?><?php echo error_for('sttng_plc_smcln') ?><?php echo error_for('sttng_plc_cln_excss') ?><?php echo error_for('sttng_plc_nt1_excss_lngth') ?><?php echo error_for('sttng_plc_cln') ?><?php echo error_for('sttng_plc_dplct') ?><?php echo error_for('sttng_plc_excss_lngth') ?><?php echo error_for('sttng_plc_url') ?><?php echo error_for('sttng_pls') ?><?php echo error_for('sttng_hsh_excss') ?><?php echo error_for('sttng_lctn_array_excss') ?><?php echo error_for('sttng_lctn_empty') ?><?php echo error_for('sttng_lctn_pipe_excss') ?><?php echo error_for('sttng_lctn_pipe') ?><?php echo error_for('sttng_lctn_smcln_excss') ?><?php echo error_for('sttng_lctn_nt2_excss_lngth') ?><?php echo error_for('sttng_lctn_nt2_cln') ?><?php echo error_for('sttng_lctn_smcln') ?><?php echo error_for('sttng_lctn_cln_excss') ?><?php echo error_for('sttng_lctn_nt1_excss_lngth') ?><?php echo error_for('sttng_lctn_cln') ?><?php echo error_for('sttng_lctn_hyphn_excss') ?><?php echo error_for('sttng_lctn_sffx') ?><?php echo error_for('sttng_lctn_hyphn') ?><?php echo error_for('sttng_lctn_dplct') ?><?php echo error_for('sttng_lctn_excss_lngth') ?><?php echo error_for('sttng_lctn_url') ?><?php echo error_for('sttng_lctn_alt_list') ?><?php echo error_for('sttng_lctn_alt_array_excss') ?><?php echo error_for('sttng_lctn_alt_empty') ?><?php echo error_for('sttng_lctn_alt_hyphn_excss') ?><?php echo error_for('sttng_lctn_alt_sffx') ?><?php echo error_for('sttng_lctn_alt_hyphn') ?><?php echo error_for('sttng_lctn_alt_dplct') ?><?php echo error_for('sttng_lctn_alt_excss_lngth') ?><?php echo error_for('sttng_lctn_alt_url') ?><?php echo error_for('sttng_lctn_alt') ?><?php echo error_for('sttng_lctn_alt_assoc') ?><?php echo error_for('sttng_hsh') ?><?php echo error_for('sttng_tm_array_excss') ?><?php echo error_for('sttng_tm_spn') ?><?php echo error_for('sttng_tm_empty') ?><?php echo error_for('sttng_tm_smcln_excss') ?><?php echo error_for('sttng_tm_nt2_excss_lngth') ?><?php echo error_for('sttng_tm_nt2_tm_spn') ?><?php echo error_for('sttng_tm_nt2_cln') ?><?php echo error_for('sttng_tm_smcln') ?><?php echo error_for('sttng_tm_cln_excss') ?><?php echo error_for('sttng_tm_nt1_excss_lngth') ?><?php echo error_for('sttng_tm_nt1_tm_spn') ?><?php echo error_for('sttng_tm_cln') ?><?php echo error_for('sttng_tm_dplct') ?><?php echo error_for('sttng_tm_excss_lngth') ?><?php echo error_for('sttng_tm_url') ?></label>
            <input type="text" name="sttng_list" id="sttng_list" value="<?php echo $sttng_list; ?>" class="entryfield <?php echo errorfield('sttng_tr_lg_chckd') ?> <?php echo errorfield('sttng_array_excss') ?> <?php echo errorfield('sttng_empty') ?> <?php echo errorfield('sttng_pls_excss') ?> <?php echo errorfield('sttng_plc_array_excss') ?> <?php echo errorfield('sttng_plc_empty') ?> <?php echo errorfield('sttng_plc_hsh') ?> <?php echo errorfield('sttng_plc_smcln_excss') ?> <?php echo errorfield('sttng_plc_nt2_excss_lngth') ?> <?php echo errorfield('sttng_plc_nt2_cln') ?> <?php echo errorfield('sttng_plc_smcln') ?> <?php echo errorfield('sttng_plc_cln_excss') ?> <?php echo errorfield('sttng_plc_nt1_excss_lngth') ?> <?php echo errorfield('sttng_plc_cln') ?> <?php echo errorfield('sttng_plc_dplct') ?> <?php echo errorfield('sttng_plc_excss_lngth') ?> <?php echo errorfield('sttng_plc_url') ?> <?php echo errorfield('sttng_pls') ?> <?php echo errorfield('sttng_hsh_excss') ?> <?php echo errorfield('sttng_lctn_array_excss') ?> <?php echo errorfield('sttng_lctn_empty') ?> <?php echo errorfield('sttng_lctn_pipe_excss') ?> <?php echo errorfield('sttng_lctn_pipe') ?> <?php echo errorfield('sttng_lctn_smcln_excss') ?> <?php echo errorfield('sttng_lctn_nt2_excss_lngth') ?> <?php echo errorfield('sttng_lctn_nt2_cln') ?> <?php echo errorfield('sttng_lctn_smcln') ?> <?php echo errorfield('sttng_lctn_cln_excss') ?> <?php echo errorfield('sttng_lctn_nt1_excss_lngth') ?> <?php echo errorfield('sttng_lctn_cln') ?> <?php echo errorfield('sttng_lctn_hyphn_excss') ?> <?php echo errorfield('sttng_lctn_sffx') ?> <?php echo errorfield('sttng_lctn_hyphn') ?> <?php echo errorfield('sttng_lctn_dplct') ?> <?php echo errorfield('sttng_lctn_excss_lngth') ?> <?php echo errorfield('sttng_lctn_url') ?> <?php echo errorfield('sttng_lctn_alt_list') ?> <?php echo errorfield('sttng_lctn_alt_array_excss') ?> <?php echo errorfield('sttng_lctn_alt_empty') ?> <?php echo errorfield('sttng_lctn_alt_hyphn_excss') ?> <?php echo errorfield('sttng_lctn_alt_sffx') ?> <?php echo errorfield('sttng_lctn_alt_hyphn') ?> <?php echo errorfield('sttng_lctn_alt_dplct') ?> <?php echo errorfield('sttng_lctn_alt_excss_lngth') ?> <?php echo errorfield('sttng_lctn_alt_url') ?> <?php echo errorfield('sttng_lctn_alt') ?> <?php echo errorfield('sttng_lctn_alt_assoc') ?> <?php echo errorfield('sttng_hsh') ?> <?php echo errorfield('sttng_tm_array_excss') ?> <?php echo errorfield('sttng_tm_spn') ?> <?php echo errorfield('sttng_tm_empty') ?> <?php echo errorfield('sttng_tm_smcln_excss') ?> <?php echo errorfield('sttng_tm_nt2_excss_lngth') ?> <?php echo errorfield('sttng_tm_nt2_tm_spn') ?> <?php echo errorfield('sttng_tm_nt2_cln') ?> <?php echo errorfield('sttng_tm_smcln') ?> <?php echo errorfield('sttng_tm_cln_excss') ?> <?php echo errorfield('sttng_tm_nt1_excss_lngth') ?> <?php echo errorfield('sttng_tm_nt1_tm_spn') ?> <?php echo errorfield('sttng_tm_cln') ?> <?php echo errorfield('sttng_tm_dplct') ?> <?php echo errorfield('sttng_tm_excss_lngth') ?> <?php echo errorfield('sttng_tm_url') ?>"/>
            <h6><b>Time:</b> Summer / February / Christmas / Ancient / Ancient Arabian / Ancient Greek / 4th Century BCE / 360s BCE / 350s BCE / Ancient Roman / Biblical / Arthurian / 10th Century / Medieval / 19th Century / 1900s / 1910s / 1920s / 1930s / 1940s / 1950s / 1960s / 1970s / January 1974 / 1980s / 1990s / 2nd May 1997 / 20th Century / 2000s / 2010s / 21st Century / Contemporary / Contemporary and Period / Modern costume / Future / Fantasy / Steampunk / Unspecified, etc.</br>
            <b>Location [##]:</b> Hell's Kitchen / Knightsbridge / Verona / British Museum / Elysian Fields</br>
            <b>Place [++]:</b> NHS psychiatric hospital / cottage / travel agency / council estate / hospital / boarding school / birthday party</br>
            - Separate multiple groups using double comma [,,]; establish array of places using [++], array of locations using [##] and array of times without prefix (list smallest denomination given for each):-</br>
            - Add prefix note with [::] and suffix note with [;;], i.e. On the outskirts of::Padua / 11th September 2011;;and the days following / A derelict::cottage;;beyond repair</br>
            - When range of times is given, end time array with [*] (as below) to display time span (i.e. first and last of array only), i.e. EMPEROR & GALILEAN: 351AD to 363AD / AN AUGUST BANK HOLIDAY LARK: August 1914 to October 1915 / CARTHAGE: 1998 to 2013</br>
            - To differentiate identically-named locations, use a double hyphen followed by an integer between 1 and 99:- London--2 / Kingston--2 / Springfield--2</br>
            - When a location should be associated with specific locations (default will exclude those that are pre-existing and fictional), set list with double pipes [||] and separate multiple entries with double chevron [>>], i.e. Hagia Irene||Constantinople>>Turkey>>Europe / The Ministry Of Love||London>>Airstrip One>>Oceania</br>
            ARCADIA: 1809//1812##Derbyshire++country house,,1993##Derbyshire++stately home</br>
            PLENTY: Easter 1962##Knightsbridge++bedsit,,November 1943##St Benoît++poppy field,,June 1947##Brussels++administration office,,September 1947##Pimlico++art gallery,,May 1951##Temple//Embankment//Knightsbridge++pavement</br>
            EMPEROR & GALILEAN: 351AD//352AD//353AD//354AD//355AD//356AD//357AD//358AD//359AD//360AD//361AD//362AD//363AD*,,Easter Sunday##Hagia Irene||Constantinople>>Turkey>>Europe++public square,,##Athens++open square with pool,,##Ephesus++ancient palace courtyard,,##Gaul++battlefield,,##Vienne++catacombs//beneath imperial church,,##The Imperial Palace++state room,,##Antioch++church,,##Temple of Helios,,##Persian border++remote::mountainside//valley,,##River Euphrates++river banks//imperial camp,,##Persian desert++desert//battlefield//tent</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="cst_reqd" class="entry">
            <label for="cst_reqd" class="fixedwidth">CAST REQUIRED: <?php echo error_for('cst_coll_wrks_checked') ?><?php echo error_for('cst_m') ?><?php echo error_for('cst_f') ?><?php echo error_for('cst_non_spc') ?></label>
            <input type="text" name="cst_m" id="cst_m" maxlength="2" value="<?php echo $cst_m; ?>" class="entryfield2chars <?php echo errorfield('cst_coll_wrks_checked') ?> <?php echo errorfield('cst_m') ?>"/>
            Male [0-99] |
            <input type="text" name="cst_f" id="cst_f" maxlength="2" value="<?php echo $cst_f; ?>" class="entryfield2chars <?php echo errorfield('cst_coll_wrks_checked') ?> <?php echo errorfield('cst_f') ?>"/>
            Female [0-99] |
            <input type="text" name="cst_non_spc" id="cst_non_spc" maxlength="2" value="<?php echo $cst_non_spc; ?>" class="entryfield2chars <?php echo errorfield('cst_coll_wrks_checked') ?> <?php echo errorfield('cst_non_spc') ?>"/>
            Non-specific [0-99] |
            <input type="checkbox" name="cst_addt" id="cst_addt"<?php if($cst_addt) {echo ' checked="checked"';} ?>/>
            Plus additional roles</br>
            <h6>NB: Cast required may be different to number of characters, i.e. STONES IN HIS POCKETS: two performers play all thirteen characters  / SPRING AWAKENING (version by Anya Reiss): the younger characters play the parts of the adults / Other instances of doubling/tripling, etc. of roles.</h6>
          </div>

          <div id="cst_nt" class="entry">
            <label for="cst_nt" class="fixedwidth">CAST NOTES: <?php echo error_for('cst_nt_coll_wrks_checked') ?><?php echo error_for('cst_nt_excss_lngth') ?></label>
            <input type="text" name="cst_nt" id="cst_nt" maxlength="255" value="<?php echo $cst_nt; ?>" class="entryfield <?php echo errorfield('cst_nt_coll_wrks_checked') ?> <?php echo errorfield('cst_nt_excss_lngth') ?>"/>
            <h6>i.e. Doubling (or tripling) (of roles) (other than Esme, Alice and Eleanor) is optional, etc.</h6>
          </div>

          <div id="char_list" class="entry">
            <label for="char_list" class="fixedwidth">CHARACTERS: <?php echo error_for('char_coll_wrks_checked') ?><?php echo error_for('char_grp_nm_array_excss') ?><?php echo error_for('char_grp_pt_empty') ?><?php echo error_for('char_eql_excss') ?><?php echo error_for('coll_grp_excss_lngth') ?><?php echo error_for('char_eql') ?><?php echo error_for('char_grp') ?><?php echo error_for('char_nm_array_excss') ?><?php echo error_for('char_empty') ?><?php echo error_for('char_cln_excss') ?><?php echo error_for('char_cln') ?><?php echo error_for('char_nt_excss_lngth') ?><?php echo error_for('char_hyphn_excss') ?><?php echo error_for('char_sffx') ?><?php echo error_for('char_hyphn') ?><?php echo error_for('char_dplct') ?><?php echo error_for('char_nm_excss_lngth') ?><?php echo error_for('char_nm') ?></label>
            <input type="text" name="char_list" id="char_list" value="<?php echo $char_list; ?>" class="entryfield <?php echo errorfield('char_coll_wrks_checked') ?> <?php echo errorfield('char_grp_nm_array_excss') ?> <?php echo errorfield('char_grp_pt_empty') ?> <?php echo errorfield('char_eql_excss') ?> <?php echo errorfield('coll_grp_excss_lngth') ?> <?php echo errorfield('char_eql') ?> <?php echo errorfield('char_grp') ?> <?php echo errorfield('char_nm_array_excss') ?> <?php echo errorfield('char_empty') ?> <?php echo errorfield('char_cln_excss') ?> <?php echo errorfield('char_cln') ?> <?php echo errorfield('char_nt_excss_lngth') ?> <?php echo errorfield('char_hyphn_excss') ?> <?php echo errorfield('char_sffx') ?> <?php echo errorfield('char_hyphn') ?> <?php echo errorfield('char_dplct') ?> <?php echo errorfield('char_nm_excss_lngth') ?> <?php echo errorfield('char_nm') ?>"/>
            <h6>- Separate multiple entries using double comma [,,] (and optional character note using double colon [::]).</br>
            To differentiate identically-named characters, use a double hyphen followed by an integer between 1 and 99:-</br>
            Demetrius--2</br>
            HAMLET: Hamlet,,Claudius,,Gertrude,,Ophelia</br>
            A MIDSUMMER NIGHT'S DREAM: Oberon::Optional doubling with Theseus,,Titania::Optional doubling with Hippolyta,,Lysander,,Demetrius</br>
            CIPHERS: Justine::Actor #1,,Kerry::Actor #1,,Kai::Actor #2,,Kareem::Actor #2,,Sunita::Actor #3,,Anoushka::Actor #3</br>
            TITUS ANDRONICUS: Romans==Titus Andronicus,,Lucius,,Quintus,,Martius,,Young Lucius,,Lavinia,,Marcis Andronicus,,Publius@@Goths==Tamora,,Demetrius,,Chiron,,Alarbus,,Aaron</br>
            ARCADIA: 1809==Thomasina Coverly,,Septimus Hodge,,Jellaby,,Ezra Chater,,Richard Noakes,,Lady Croom,,Captain Brice,,Augustus Coverly@@1993==Hannah Jarvis,,Chloe Coverly,,Bernard Nightingale,,Valentine Coverly,,Gus Coverly</br>
            AN AUGUST BANK HOLIDAY LARK: The Rushcart Lads==John Farrar,,Jim Haworth,,Edward Farrar,,William Farrar,,Frank Armitage,,Alan Ramsden,,Herbert Tweddle,,Dick Shaw@@The Greenmill Lasses==Alice Armitage,,Mary Farrar,,Edie Stapleton,,Susie Hughes</br>
            SIX CHARACTERS IN SEARCH OF AN AUTHOR: The Characters==The Father,,The Stepdaughter,,The Mother,,The Son,,The Teenager,,The Little Girl,,Madame Pace,,The Director,,The Stage Manager,,The Carpenter,,The Assistant@@The Actors==Actor #1,,Actor #2,,Actor #3,,Actor #4</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="lcnsr_list" class="entry">
            <label for="lcnsr_list" class="fixedwidth">LICENSORS: <?php echo error_for('lcnsr_coll_wrks_checked') ?><?php echo error_for('lcnsr_array_excss') ?><?php echo error_for('lcnsr_empty') ?><?php echo error_for('lcnsr_pipe_excss') ?><?php echo error_for('lcnsr_pipe') ?><?php echo error_for('lcnsr_comp_cln_excss') ?><?php echo error_for('lcnsr_comp_hyphn_excss') ?><?php echo error_for('lcnsr_comp_sffx') ?><?php echo error_for('lcnsr_comp_hyphn') ?><?php echo error_for('lcnsr_comp_dplct') ?><?php echo error_for('lcnsr_comp_nm_excss_lngth') ?><?php echo error_for('lcnsr_comp_rl_excss_lngth') ?><?php echo error_for('lcnsr_comp_cln') ?><?php echo error_for('lcnsr_comp_url') ?><?php echo error_for('lcnsr_comp_nonexst') ?><?php echo error_for('lcnsr_prsn_empty') ?><?php echo error_for('lcnsr_prsn_cln_excss') ?><?php echo error_for('lcnsr_prsn_rl_excss_lngth') ?><?php echo error_for('lcnsr_prsn_cln') ?><?php echo error_for('lcnsr_prsn_hyphn_excss') ?><?php echo error_for('lcnsr_prsn_sffx') ?><?php echo error_for('lcnsr_prsn_smcln_excss') ?><?php echo error_for('lcnsr_prsn_dplct') ?><?php echo error_for('lcnsr_prsn_excss_lngth') ?><?php echo error_for('lcnsr_prsn_smcln') ?><?php echo error_for('lcnsr_prsn_nm') ?><?php echo error_for('lcnsr_prsn_url') ?><?php echo error_for('lcnsr_prsn_nonexst') ?><?php echo error_for('lcnsr_prsn_id_mtch') ?><?php echo error_for('comp_lcnsr_no_assoc') ?></label>
            <input type="text" name="lcnsr_list" id="lcnsr_list" value="<?php echo $lcnsr_list; ?>" class="entryfield <?php echo errorfield('lcnsr_coll_wrks_checked') ?> <?php echo errorfield('lcnsr_array_excss') ?> <?php echo errorfield('lcnsr_empty') ?> <?php echo errorfield('lcnsr_pipe_excss') ?> <?php echo errorfield('lcnsr_pipe') ?> <?php echo errorfield('lcnsr_comp_cln_excss') ?> <?php echo errorfield('lcnsr_comp_hyphn_excss') ?> <?php echo errorfield('lcnsr_comp_sffx') ?> <?php echo errorfield('lcnsr_comp_hyphn') ?> <?php echo errorfield('lcnsr_comp_dplct') ?> <?php echo errorfield('lcnsr_comp_nm_excss_lngth') ?> <?php echo errorfield('lcnsr_comp_rl_excss_lngth') ?> <?php echo errorfield('lcnsr_comp_cln') ?> <?php echo errorfield('lcnsr_comp_nonexst') ?> <?php echo errorfield('lcnsr_comp_url') ?> <?php echo errorfield('lcnsr_prsn_empty') ?> <?php echo errorfield('lcnsr_prsn_cln_excss') ?> <?php echo errorfield('lcnsr_prsn_rl_excss_lngth') ?> <?php echo errorfield('lcnsr_prsn_cln') ?> <?php echo errorfield('lcnsr_prsn_hyphn_excss') ?> <?php echo errorfield('lcnsr_prsn_sffx') ?> <?php echo errorfield('lcnsr_prsn_smcln_excss') ?> <?php echo errorfield('lcnsr_prsn_dplct') ?> <?php echo errorfield('lcnsr_prsn_excss_lngth') ?> <?php echo errorfield('lcnsr_prsn_smcln') ?> <?php echo errorfield('lcnsr_prsn_nm') ?> <?php echo errorfield('lcnsr_prsn_url') ?> <?php echo errorfield('lcnsr_prsn_nonexst') ?> <?php echo errorfield('lcnsr_prsn_id_mtch') ?> <?php echo errorfield('comp_lcnsr_no_assoc') ?>"/>
            <h6>- Separate multiple entries using double comma [,,], type of representation using double colon [::], and (if person) given name and family name using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if agents are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - To differentiate identically-named people or companies, use a double hyphen followed by an integer between 1 and 99:- St John;;Donald--2::Agent: Literary</br>
            United Agents::Professional rights||Giles;;Smart::Professional rights (English-speaking)//Nicki;;Stoddart::Professional rights (Non-English-speaking),,Samuel French::Amateur rights||Felicity;;Barks::Amateur rights
          </div>
        </fieldset>

        <fieldset>
          <div id="alt_nm_list" class="entry">
            <label for="alt_nm_list" class="fixedwidth">PLAYTEXT ALTERNATE NAME(S): <?php echo error_for('alt_nm_coll_wrks_chckd') ?><?php echo error_for('alt_nm_array_excss') ?><?php echo error_for('alt_nm_empty') ?><?php echo error_for('alt_nm_cln_excss') ?><?php echo error_for('alt_nm_dscr_excss_lngth') ?><?php echo error_for('alt_nm_cln') ?><?php echo error_for('alt_nm_dplct') ?><?php echo error_for('alt_nm_excss_lngth') ?></label>
            <input type="text" name="alt_nm_list" id="alt_nm_list" value="<?php echo $alt_nm_list; ?>" class="entryfield <?php echo errorfield('alt_nm_coll_wrks_chckd') ?> <?php echo errorfield('alt_nm_array_excss') ?> <?php echo errorfield('alt_nm_empty') ?> <?php echo errorfield('alt_nm_cln_excss') ?> <?php echo errorfield('alt_nm_dscr_excss_lngth') ?> <?php echo errorfield('alt_nm_cln') ?> <?php echo errorfield('alt_nm_dplct') ?> <?php echo errorfield('alt_nm_excss_lngth') ?>"/>
            <h6>i.e. TITUS ANDRONICUS: The Most Lamentable Tragedy of Titus Andronicus; TWELFTH NIGHT: What You Will; MUCH ADO ABOUT NOTHING: Love's Labour's Won; THE HOUSE OF BERNARDA ALBA: La casa de Bernarda Alba, etc.</br>
            - Separate multiple entries using double comma [,,] and description using double colon [::]:-</br>
            La casa de Bernarda Alba::Original Spanish title,,Bernarda Alba::Abbreviated title</h6>
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
          <input type="hidden" name="pt_id" value="<?php echo $pt_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>