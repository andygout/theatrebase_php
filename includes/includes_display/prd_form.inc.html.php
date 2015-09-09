<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?></title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4><?php echo $prd_clss_dsply; ?>PRODUCTION<?php echo $tr_dsply.$coll_dsply; ?>:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p><?php echo $pagesubtitle; ?></p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('prd_add_edit_error') ?>
      <?php echo error_for('prd_dlt') ?>
      </div>

      <div id="crtd_updtd" class="box">
        <table class="overview">
          <tr><td class="ovrvwcol1">Created:</td><td><?php echo $prd_crtd; ?></td></tr>
          <tr><td class="ovrvwcol1">Last updated:</td><td><?php echo $prd_updtd; ?></td></tr>
        </table>
      </div>

      <form action="<?php echo $frmactn; ?>" method="post">

        <fieldset>
          <div id="prd_nm" class="entry">
            <label for="prd_nm" class="fixedwidth">* PRODUCTION NAME: <?php echo error_for('prd_nm') ?><?php echo error_for('tr_ov_prd_nm_mtch') ?></label>
            <input type="text" name="prd_nm" id="prd_nm" maxlength="255" value="<?php echo $prd_nm; ?>" class="entryfield <?php echo errorfield('prd_nm') ?> <?php echo errorfield('tr_ov_prd_nm_mtch') ?>"/>
            <h6>i.e. Hamlet</h6>
          </div>

          <div id="prd_sbnm" class="entry">
            <label for="prd_sbnm" class="fixedwidth">PRODUCTION SUB-NAME: <?php echo error_for('prd_sbnm_excss_lngth') ?></label>
            <input type="text" name="prd_sbnm" id="prd_sbnm" maxlength="255" value="<?php echo $prd_sbnm; ?>" class="entryfield <?php echo errorfield('prd_sbmn_excss_lngth') ?>"/>
            <h6>For any sort of sub-header to/translation of the title:</br>
            i.e. Do Unto Others: In response to The Gospel According to St Luke / A Lady of Little Sense: La Dama Boba, etc.</h6>
          </div>

          <div id="mat_list" class="entry">
            <label for="mat_list" class="fixedwidth">MATERIAL: <?php echo error_for('mat_tr_lg_chckd') ?><?php echo error_for('mat_nm_frmt_array_excss') ?><?php echo error_for('mat_empty') ?><?php echo error_for('mat_hyphn_excss') ?><?php echo error_for('mat_sffx') ?><?php echo error_for('mat_hyphn') ?><?php echo error_for('mat_smcln_excss') ?><?php echo error_for('mat_dplct') ?><?php echo error_for('frmt_nm_excss_lngth') ?><?php echo error_for('frmt_url') ?><?php echo error_for('mat_nm_excss_lngth') ?><?php echo error_for('mat_url') ?><?php echo error_for('mat_smcln') ?></label>
            <input type="text" name="mat_list" id="mat_list" value="<?php echo $mat_list; ?>" class="entryfield <?php echo errorfield('mat_tr_lg_chckd') ?> <?php echo errorfield('mat_nm_frmt_array_excss') ?> <?php echo errorfield('mat_empty') ?> <?php echo errorfield('mat_hyphn_excss') ?> <?php echo errorfield('mat_sffx') ?> <?php echo errorfield('mat_hyphn') ?> <?php echo errorfield('mat_smcln_excss') ?> <?php echo errorfield('mat_dplct') ?> <?php echo errorfield('frmt_nm_excss_lngth') ?> <?php echo errorfield('frmt_url') ?> <?php echo errorfield('mat_nm_excss_lngth') ?> <?php echo errorfield('mat_url') ?> <?php echo errorfield('mat_smcln') ?>"/>
            <h6>- Separate multiple entries (if applicable) using double comma [,,], and material name and format using double semi-colon [;;]:-</br>
            Hamlet;;play::Kiss Me Kate;;musical</br>
            To differentiate identically-named materials, use a double hyphen followed by an integer between 1 and 99:-</br>
            Red;;play,,Red;;play--2</h6>
          </div>

          <div id="pt_list" class="entry">
            <label for="pt_list" class="fixedwidth">PLAYTEXT: <?php echo error_for('pt_tr_lg_chckd') ?><?php echo error_for('pt_list_array_excss') ?><?php echo error_for('pt_empty') ?><?php echo error_for('pt_hyphn_excss') ?><?php echo error_for('pt_sffx') ?><?php echo error_for('pt_hyphn') ?><?php echo error_for('pt_hsh_excss') ?><?php echo error_for('pt_yr') ?><?php echo error_for('pt_yr_frmt') ?><?php echo error_for('pt_hsh') ?><?php echo error_for('pt_dplct') ?><?php echo error_for('pt_nm_yr_excss_lngth') ?><?php echo error_for('pt_url') ?><?php echo error_for('pt_coll_wrks') ?></label>
            <input type="text" name="pt_list" id="pt_list" value="<?php echo $pt_list; ?>" class="entryfield <?php echo errorfield('pt_tr_lg_chckd') ?> <?php echo errorfield('pt_list_array_excss') ?> <?php echo errorfield('pt_empty') ?> <?php echo errorfield('pt_hyphn_excss') ?> <?php echo errorfield('pt_sffx') ?> <?php echo errorfield('pt_hyphn') ?> <?php echo errorfield('pt_hsh_excss') ?> <?php echo errorfield('pt_yr') ?> <?php echo errorfield('pt_yr_frmt') ?> <?php echo errorfield('pt_hsh') ?> <?php echo errorfield('pt_dplct') ?> <?php echo errorfield('pt_nm_yr_excss_lngth') ?> <?php echo errorfield('pt_url') ?> <?php echo errorfield('pt_coll_wrks') ?>"/>
            <h6>- Separate multiple entries (if applicable) using double comma [,,]; entries to adhere to below format:-</br>
            Playtext Name##c(optional circa indication)-(option BCE indication) Year Started (format: YYYY)(if additional Year Started required:);;c(optional circa indication)-(option BCE indication) Year Written (format: YYYY)--(optional suffix [1-99])</br>
            i.e. Hamlet##1601;;1602 / Oedipus The King##c-429</br>
            To differentiate playtexts with an identical name and year, use a double hyphen followed by an integer between 1 and 99:-</br>
            The Seagull##1997--1,,The Seagull##1997--2</h6>
          </div>

          <div id="prd_frst_dt" class="entry">
            <label for="prd_frst_dt" class="fixedwidth">* FIRST PERFORMANCE [DD]-[MM]-[YYYY]: <?php echo error_for('prd_frst_dt') ?><?php echo error_for('tr_ov_prd_frst_dt_mtch') ?><?php echo error_for('coll_ov_prd_frst_dt_mtch') ?></label>
            <input type="date" name="prd_frst_dt" id="prd_frst_dt" maxlength="10" value="<?php echo $prd_frst_dt; ?>" class="entryfielddate <?php echo errorfield('prd_frst_dt') ?> <?php echo errorfield('tr_ov_prd_frst_dt_mtch') ?> <?php echo errorfield('coll_ov_prd_frst_dt_mtch') ?>"/>
            <h6>i.e. 21-03-2013</h6>
          </div>

          <div id="prd_prss_dt" class="entry">
            <label for="prd_prss_dt" class="fixedwidth">PRESS PERFORMANCE [DD]-[MM]-[YYYY]: <?php echo error_for('prd_prss_dt') ?></label>
            <input type="date" name="prd_prss_dt" id="prd_prss_dt" maxlength="10" value="<?php echo $prd_prss_dt; ?>" class="entryfielddate <?php echo errorfield('prd_prss_dt') ?> <?php echo errorfield('prd_prss_dt_tbc') ?> <?php echo errorfield('prd_prv_only') ?>"/>
            <h6>i.e. 27-03-2013</h6>
          </div>

          <div id="prd_lst_dt" class="entry">
            <label for="prd_lst_dt" class="fixedwidth">* LAST PERFORMANCE [DD]-[MM]-[YYYY]: <?php echo error_for('prd_lst_dt') ?><?php echo error_for('tr_ov_prd_lst_dt_mtch') ?><?php echo error_for('coll_ov_prd_lst_dt_mtch') ?></label>
            <input type="date" name="prd_lst_dt" id="prd_lst_dt" maxlength="10" value="<?php echo $prd_lst_dt; ?>" class="entryfielddate <?php echo errorfield('prd_lst_dt') ?> <?php echo errorfield('tr_ov_prd_lst_dt_mtch') ?> <?php echo errorfield('coll_ov_prd_lst_dt_mtch') ?>"/>
            <h6>i.e. 11-05-2013</h6>
          </div>

          <div id="prd_prss_dt_tbc" class="entry">
            <label for="prd_prss_dt_tbc" class="fixedwidth">PRESS PERFORMANCE DATE TBC: <?php echo error_for('prd_prss_dt_tbc') ?></label>
            <input type="checkbox" name="prd_prss_dt_tbc" id="prd_prss_dt_tbc"<?php if($prd_prss_dt_tbc) {echo ' checked="checked"';} ?>/>
            <h6>Check box if press night date is yet to be confirmed (Press Performance date field must be left empty).</h6>
          </div>

          <div id="prd_prv_only" class="entry">
            <label for="prd_prv_only" class="fixedwidth">PREVIEWS ONLY: <?php echo error_for('prd_prv_only') ?></label>
            <input type="checkbox" name="prd_prv_only" id="prd_prv_only"<?php if($prd_prv_only) { echo ' checked="checked"'; } ?>/>
            <h6>Check box if this run is previews only (i.e. full run and press night to take place in another venue) (Press Performance date field must be left empty).</h6>
          </div>

          <div id="prd_dts_info" class="entry">
            <label for="prd_dts_info" class="fixedwidth">DATES INFO: </label>
            <input type="radio" name="prd_dts_info" value="1" <?php if($prd_dts_info=='1') {echo ' checked="checked"';} ?>/> Dates info N/A (i.e. none of below apply).<br>
            <input type="radio" name="prd_dts_info" value="2" <?php if($prd_dts_info=='2') {echo ' checked="checked"';} ?>/> Booking until (if last performance date given is only a 'Booking Until' date, i.e. exact Last Performance is TBC).<br>
            <input type="radio" name="prd_dts_info" value="3" <?php if($prd_dts_info=='3') {echo ' checked="checked"';} ?>/> Last date TBC (if last performance date is yet to be confirmed).</br>
            <input type="radio" name="prd_dts_info" value="4" <?php if($prd_dts_info=='4') {echo ' checked="checked"';} ?>/> Dates TBC (if all performance dates are yet to be confirmed).</br>
          </div>

          <div id="prd_prss_wrdng" class="entry">
            <label for="prd_prss_wrdng" class="fixedwidth">PRESS DATE WORDING: <?php echo error_for('prd_prss_wrdng') ?></label>
            <input type="text" name="prd_prss_wrdng" id="prd_prss_wrdng" maxlength="20" value="<?php echo $prd_prss_wrdng; ?>" class="entryfield <?php echo errorfield('prd_prss_wrdng') ?>"/>
            <h6>Use if alternate wording to 'Press Night' is desired, i.e. Press Day / Press Nights / Opening Night (if U.S.) / Gala Night.</h6>
          </div>

          <div id="prd_tbc_nt" class="entry">
            <label for="prd_tbc_nt" class="fixedwidth">DATES TBC NOTE (14 CHARACTERS ONLY): <?php echo error_for('prd_tbc_nt') ?></label>
            <input type="text" name="prd_tbc_nt" id="prd_tbc_nt" maxlength="14" value="<?php echo $prd_tbc_nt; ?>" class="entryfield <?php echo errorfield('prd_tbc_nt') ?>"/>
            <h6>To add brief note (to appear in credits) if dates are set to TBC, i.e. Jan 2014 / End 2015 / Beg 2016.</h6>
          </div>

          <div id="prd_dt_nt" class="entry">
            <label for="prd_dt_nt" class="fixedwidth">DATES NOTES: <?php echo error_for('prd_dt_nt_excss_lngth') ?></label>
            <input type="text" name="prd_dt_nt" id="prd_dt_nt" maxlength="255" value="<?php echo $prd_dt_nt; ?>" class="entryfield <?php echo errorfield('prd_dt_nt_excss_lngth') ?>"/>
            <h6>i.e. Press Night postponed owing to lead actress injured; Productions closed early (original set to close on 23 Feb 2013); Press Night at Richmond Theatre (if touring), etc.</h6>
          </div>

          <div id="prd_prss_dt_2" class="entry">
            <label for="prd_prss_dt_2" class="fixedwidth">SECOND PRESS PERFORMANCE [DD]-[MM]-[YYYY]: <?php echo error_for('prd_prss_dt_2') ?></label>
            <input type="date" name="prd_prss_dt_2" id="prd_prss_dt_2" maxlength="10" value="<?php echo $prd_prss_dt_2; ?>" class="entryfielddate <?php echo errorfield('prd_prss_dt_2') ?>"/>
            <h6>i.e. 28-03-2013</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="thtr_nm" class="entry">
            <label for="thtr_nm" class="fixedwidth">* THEATRE: <?php echo error_for('thtr_nm') ?><?php echo error_for('thtr_hyphn_excss') ?><?php echo error_for('thtr_sffx') ?><?php echo error_for('thtr_hyphn') ?><?php echo error_for('thtr_cln') ?><?php echo error_for('thtr_smcln') ?><?php echo error_for('thtr_excss_lngth') ?><?php echo error_for('thtr_cmpstn') ?><?php echo error_for('thtr_url') ?><?php echo error_for('thtr_tr_ov') ?></label>
            <input type="text" name="thtr_nm" id="thtr_nm" maxlength="255" value="<?php echo $thtr_nm; ?>" class="entryfield <?php echo errorfield('thtr_nm') ?> <?php echo errorfield('thtr_hyphn_excss') ?> <?php echo errorfield('thtr_sffx') ?> <?php echo errorfield('thtr_hyphn') ?> <?php echo errorfield('thtr_cln') ?> <?php echo errorfield('thtr_smcln') ?> <?php echo errorfield('thtr_excss_lngth') ?> <?php echo errorfield('thtr_cmpstn') ?> <?php echo errorfield('thtr_url') ?> <?php echo errorfield('thtr_tr_ov') ?>"/>
            <h6>i.e. National Theatre: Olivier Theatre (South Bank, London)</br>
            - Separate location using double colon [::] (optional), and theatre and sub-theatre using [;;] (optional).</br>
            National Theatre;;Olivier Theatre::South Bank, London / Donmar Warehouse::Covent Garden, London / Bristol Old Vic / Salisbury Playhouse;;The Salberg</br>
            - To differentiate identically-named theatres, use a double hyphen followed by an integer between 1 and 99:-</br>
            Bush Theatre::Shepherd's Bush, London--2</br>
            NB: If production is a Tour Overview then write type of tour, i.e. UK Tour, International Tour, etc.</h6>
          </div>

          <div id="prd_thtr_nt" class="entry">
            <label for="prd_thtr_nt" class="fixedwidth">THEATRE NOTES: <?php echo error_for('prd_thtr_nt_excss_lngth') ?></label>
            <input type="text" name="prd_thtr_nt" id="prd_thtr_nt" maxlength="255" value="<?php echo $prd_thtr_nt; ?>" class="entryfield <?php echo errorfield('prd_thtr_nt_excss_lngth') ?>"/>
            <h6>i.e. Re-opening of venue; Opening production at new address, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="prd_clss" class="entry">
            <label for="prd_clss" class="fixedwidth">PRODUCTION CLASS: <?php echo error_for('tr_ov_prd_clss_mtch') ?><?php echo error_for('coll_ov_prd_clss_mtch') ?></label>
            <input type="radio" name="prd_clss" value="1" <?php if($prd_clss=='1') {echo ' checked="checked"';} ?>/> Professional<br>
            <input type="radio" name="prd_clss" value="2" <?php if($prd_clss=='2') {echo ' checked="checked"';} ?>/> Amateur<br>
            <input type="radio" name="prd_clss" value="3" <?php if($prd_clss=='3') {echo ' checked="checked"';} ?>/> Drama School</br>
          </div>
        </fieldset>

        <fieldset>
          <div id="prd_tr" class="entry">
            <label for="prd_tr" class="fixedwidth">TOURING PRODUCTIONS: <?php echo error_for('tr_ov_assoc') ?></label>
            <input type="radio" name="prd_tr" value="1" <?php if($prd_tr=='1') {echo ' checked="checked"';} ?>/> Tour N/A (i.e. neither of below apply)<br>
            <input type="radio" name="prd_tr" value="2" <?php if($prd_tr=='2') {echo ' checked="checked"';} ?>/> Tour Overview<br>
            <input type="radio" name="prd_tr" value="3" <?php if($prd_tr=='3') {echo ' checked="checked"';} ?>/> Tour Leg</br>
          </div>

          <div id="tr_lg_list" class="entry">
            <label for="tr_lg_list" class="fixedwidth">TOUR LEGS (only if production is a tour overview): <?php echo error_for('tr_lg_entry_ov_unchckd') ?><?php echo error_for('tr_lg_empty') ?><?php echo error_for('tr_lg_nonnmrcl') ?><?php echo error_for('tr_lg_dplct') ?><?php echo error_for('tr_lg_nonexst') ?><?php echo error_for('tr_lg_prd_id_mtch') ?><?php echo error_for('tr_lg_assoc') ?><?php echo error_for('tr_lg_prd_nm_mtch') ?><?php echo error_for('tr_lg_prd_dts_mtch') ?><?php echo error_for('tr_lg_prd_unchckd') ?><?php echo error_for('tr_lg_prd_clss_mtch') ?></label>
            <input type="text" name="tr_lg_list" id="tr_lg_list" value="<?php echo $tr_lg_list; ?>" class="entryfield <?php echo errorfield('tr_lg_entry_ov_unchckd') ?> <?php echo errorfield('tr_lg_empty') ?> <?php echo errorfield('tr_lg_nonnmrcl') ?> <?php echo errorfield('tr_lg_dplct') ?> <?php echo errorfield('tr_lg_nonexst') ?> <?php echo errorfield('tr_lg_prd_id_mtch') ?> <?php echo errorfield('tr_lg_assoc') ?> <?php echo errorfield('tr_lg_prd_nm_mtch') ?> <?php echo errorfield('tr_lg_prd_dts_mtch') ?> <?php echo errorfield('tr_lg_prd_unchckd') ?> <?php echo errorfield('tr_lg_prd_clss_mtch') ?>"/>
            <h6>Enter production ids for each leg that comprises this tour.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            55,,56,,57</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="prd_coll" class="entry">
            <label for="prd_coll" class="fixedwidth">COLLECTIONS: <?php echo error_for('coll_ov_assoc') ?></label>
            <input type="radio" name="prd_coll" value="1" <?php if($prd_coll=='1') {echo ' checked="checked"';} ?>/> Collection N/A (i.e. neither of below apply)<br>
            <input type="radio" name="prd_coll" value="2" <?php if($prd_coll=='2') {echo ' checked="checked"';} ?>/> Collection Overview<br>
            <input type="radio" name="prd_coll" value="3" <?php if($prd_coll=='3') {echo ' checked="checked"';} ?>/> Collection Segment<br>
          </div>

          <div id="coll_sg_list" class="entry">
            <label for="coll_sg_list" class="fixedwidth">COLLECTION SEGMENTS (only if production is a collection overview): <?php echo error_for('coll_sg_entry_ov_unchckd') ?><?php echo error_for('coll_sg_sbhdr_id_array_excss') ?><?php echo error_for('coll_sg_sbhdr_id_empty') ?><?php echo error_for('coll_sg_eql_excss') ?><?php echo error_for('coll_sbhdr_excss_lngth') ?><?php echo error_for('coll_sg_eql') ?><?php echo error_for('coll_sg_sbhdr') ?><?php echo error_for('coll_sg_list_array_excss') ?><?php echo error_for('coll_sg_empty') ?><?php echo error_for('coll_sg_nonnmrcl') ?><?php echo error_for('coll_sg_dplct') ?><?php echo error_for('coll_sg_nonexst') ?><?php echo error_for('coll_sg_prd_id_mtch') ?><?php echo error_for('coll_sg_assoc') ?><?php echo error_for('coll_sg_prd_dts_mtch') ?><?php echo error_for('coll_sg_prd_unchckd') ?><?php echo error_for('coll_sg_prd_clss_mtch') ?></label>
            <input type="text" name="coll_sg_list" id="coll_sg_list" value="<?php echo $coll_sg_list; ?>" class="entryfield <?php echo errorfield('coll_sg_entry_ov_unchckd') ?> <?php echo errorfield('coll_sg_sbhdr_id_array_excss') ?> <?php echo errorfield('coll_sg_sbhdr_id_empty') ?> <?php echo errorfield('coll_sg_eql_excss') ?> <?php echo errorfield('coll_sbhdr_excss_lngth') ?> <?php echo errorfield('coll_sg_eql') ?> <?php echo errorfield('coll_sg_sbhdr') ?> <?php echo errorfield('coll_sg_list_array_excss') ?> <?php echo errorfield('coll_sg_empty') ?> <?php echo errorfield('coll_sg_nonnmrcl') ?> <?php echo errorfield('coll_sg_dplct') ?> <?php echo errorfield('coll_sg_nonexst') ?> <?php echo errorfield('coll_sg_prd_id_mtch') ?> <?php echo errorfield('coll_sg_assoc') ?> <?php echo errorfield('coll_sg_prd_dts_mtch') ?> <?php echo errorfield('coll_sg_prd_unchckd') ?> <?php echo errorfield('coll_sg_prd_clss_mtch') ?>"/>
            <h6>Enter production ids for each production that comprises this collection.</br>
            - Separate multiple entries using double comma [,,] and apply subdivisions using double at symbols [@@] and in which instance apply subheaders using double equals [==]:-</br>
            THE COAST OF UTOPIA: 92,,93,,94</br>
            SIXTY-SIX BOOKS: Old Testament==51,,45,,50,,49@@New Testament==48,,47,,52,,46</br>
            TONIGHT AT 8:30: Cocktails==106,,107,,108@@Dinner==109,,110,,111@@Dancing==112,,113,,114</br>
            THE BOMB: First Blast: Proliferation==233,,234,,235,,236,,237@@Second Blast: Present Dangers==238,,239,,240,,241,,242</br>
            CHARGED: Cycle 1==298,,299,,300@@Cycle 2==301,,302,,303</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rep_list" class="entry">
            <label for="rep_list" class="fixedwidth">PLAYS IN REP: <?php echo error_for('rep_empty') ?><?php echo error_for('rep_nonnmrcl') ?><?php echo error_for('rep_dplct') ?><?php echo error_for('rep_nonexst') ?><?php echo error_for('rep_prd_id_mtch') ?></label>
            <input type="text" name="rep_list" id="rep_list" value="<?php echo $rep_list; ?>" class="entryfield <?php echo errorfield('rep_empty') ?> <?php echo errorfield('rep_nonnmrcl') ?> <?php echo errorfield('rep_dplct') ?> <?php echo errorfield('rep_nonexst') ?> <?php echo errorfield('rep_prd_id_mtch') ?>"/>
            <h6>Enter production ids for each other production with which this production plays in rep.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            119,,120,,121</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="prdrn_list" class="entry">
            <label for="prdrn_list" class="fixedwidth">PRODUCTION RUN: <?php echo error_for('prdrn_tr_lg_chckd') ?><?php echo error_for('prdrn_empty') ?><?php echo error_for('prdrn_nonnmrcl') ?><?php echo error_for('prdrn_dplct') ?><?php echo error_for('prdrn_nonexst') ?><?php echo error_for('prdrn_prd_id_mtch') ?></label>
            <input type="text" name="prdrn_list" id="prdrn_list" maxlength="255" value="<?php echo $prdrn_list; ?>" class="entryfield <?php echo errorfield('prdrn_tr_lg_chckd') ?> <?php echo errorfield('prdrn_empty') ?> <?php echo errorfield('prdrn_nonnmrcl') ?> <?php echo errorfield('prdrn_dplct') ?> <?php echo errorfield('prdrn_nonexst') ?> <?php echo errorfield('prdrn_prd_id_mtch') ?>"/>
            <h6>Enter production ids for all previous and subsequent runs of this production.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            67,,68,,69</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="prd_vrsn_list" class="entry">
            <label for="prd_vrsn_list" class="fixedwidth">PRODUCTION VERSION: <?php echo error_for('prd_vrsn_tr_lg_chckd') ?><?php echo error_for('prd_vrsn_nm_array_excss') ?><?php echo error_for('prd_vrsn_empty') ?><?php echo error_for('prd_vrsn_dplct') ?><?php echo error_for('prd_vrsn_nm_excss_lngth') ?><?php echo error_for('prd_vrsn_nm') ?></label>
            <input type="text" name="prd_vrsn_list" id="prd_vrsn_list" value="<?php echo $prd_vrsn_list; ?>" class="entryfield <?php echo errorfield('prd_vrsn_tr_lg_chckd') ?> <?php echo errorfield('prd_vrsn_nm_array_excss') ?> <?php echo errorfield('prd_vrsn_empty') ?> <?php echo errorfield('prd_vrsn_dplct') ?> <?php echo errorfield('prd_vrsn_nm_excss_lngth') ?> <?php echo errorfield('prd_vrsn_nm') ?>"/>
            <h6>i.e. World Premiere / European Premiere / UK Premiere / Regional Premiere / Return / Transfer / West End Transfer / London Transfer / UK Transfer / Broadway Transfer / Transfer from West End / Transfer from Broadway, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            UK Premiere,,West End Transfer</h6>
          </div>

          <div id="txt_vrsn_list" class="entry">
            <label for="txt_vrsn_list" class="fixedwidth">TEXT VERSION: <?php echo error_for('txt_vrsn_tr_lg_chckd') ?><?php echo error_for('txt_vrsn_nm_array_excss') ?><?php echo error_for('txt_vrsn_empty') ?><?php echo error_for('txt_vrsn_dplct') ?><?php echo error_for('txt_vrsn_nm_excss_lngth') ?><?php echo error_for('txt_vrsn_nm') ?></label>
            <input type="text" name="txt_vrsn_list" id="txt_vrsn_list" value="<?php echo $txt_vrsn_list; ?>" class="entryfield <?php echo errorfield('txt_vrsn_tr_lg_chckd') ?> <?php echo errorfield('txt_vrsn_nm_array_excss') ?> <?php echo errorfield('txt_vrsn_empty') ?> <?php echo errorfield('txt_vrsn_dplct') ?> <?php echo errorfield('txt_vrsn_nm_excss_lngth') ?> <?php echo errorfield('txt_vrsn_nm') ?>"/>
            <h6>i.e. New / Revival / New Adaptation / New Translation / New Version / Abridged Text, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            Revival,,New Version</h6>
          </div>

          <div id="ctgry_list" class="entry">
            <label for="ctgry_list" class="fixedwidth">CATEGORY: <?php echo error_for('ctgry_tr_lg_chckd') ?><?php echo error_for('ctgry_nm_array_excss') ?><?php echo error_for('ctgry_empty') ?><?php echo error_for('ctgry_dplct') ?><?php echo error_for('ctgry_nm_excss_lngth') ?><?php echo error_for('ctgry_nm') ?></label>
            <input type="text" name="ctgry_list" id="ctgry_list" value="<?php echo $ctgry_list; ?>" class="entryfield <?php echo errorfield('ctgry_tr_lg_chckd') ?> <?php echo errorfield('ctgry_nm_array_excss') ?> <?php echo errorfield('ctgry_empty') ?> <?php echo errorfield('ctgry_dplct') ?> <?php echo errorfield('ctgry_nm_excss_lngth') ?> <?php echo errorfield('ctgry_nm') ?>"/>
            <h6>i.e. Play / Musical / Solo Performance / Reading / Installation / Collection / Trilogy / Cycle / Drama School Production / Youth Theatre Production / Opera / Operetta / Ballet / Benefit / Burlesque / Cabaret / Circus / Comedy Show / Dance / Devised /  Extravaganza / Hypnotism / Impersonations / Improvisation / Magic / Mime / Monologue / One Act Play / Puppetry / Poetry / Pantomime / Play with Music / Revue / Scenes / Short Play / Tribute / Variety / Vaudeville / Misc., etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            Reading,,Play</h6>
          </div>

          <div id="gnr_list" class="entry">
            <label for="gnr_list" class="fixedwidth">GENRE: <?php echo error_for('gnr_tr_lg_chckd') ?><?php echo error_for('gnr_nm_array_excss') ?><?php echo error_for('gnr_empty') ?><?php echo error_for('gnr_dplct') ?><?php echo error_for('gnr_nm_excss_lngth') ?><?php echo error_for('gnr_nm') ?></label>
            <input type="text" name="gnr_list" id="gnr_list" value="<?php echo $gnr_list; ?>" class="entryfield <?php echo errorfield('gnr_tr_lg_chckd') ?> <?php echo errorfield('gnr_nm_array_excss') ?> <?php echo errorfield('gnr_empty') ?> <?php echo errorfield('gnr_dplct') ?> <?php echo errorfield('gnr_nm_excss_lngth') ?> <?php echo errorfield('gnr_nm') ?>"/>
            <h6>i.e. Theatre of the Absurd / African-American drama / African drama / Alternative theatre / American drama / Ancient Greek drama / Ancient Roman drama / Asian drama / Australian drama / Austrian drama / Avante-garde theatre / Black theatre / British drama / Canadian drama / Caribbean drama / Caroline theatre / Children's theatre / City comedy / Classical / Comedy / Comedy of manners / Commedia dell-Arte / Community theatre / Theatre of Cruelty / Czech drama /
            Dance drama / Documentary theatre / Drama / Elizabethan theatre / English drama / Ensemble / Epic theatre / European drama / Expressionism / Farce / Feminist theatre / French drama / Future history / Gay theatre / German drama / Greek drama / Greek tragedy / History play / Hungarian drama / In-yer-face theatre / Irish drama / Italian drama / Jacobean theatre / Jacobean revenge tragedy / Japanese drama / Kitchen sink drama / Latin American drama / Melodrama / Middle Eastern drama / Modernist drama / Morality play / Murder mystery /
            Mystery / Myth / Naturalistic drama / Northern Irish drama / Norwegian drama / Theatre of the Oppressed / Parody / Physical theatre / Political theatre / Poor theatre / Popular theatre / Post-colonial drama / Post-dramatic theatre / Postmodern theatre / Restoration comedy / Revenge tragedy / Ritual drama / Russian drama / Satire / Scottish drama / Shakespearean comedy / Shakespearean history / Shakespearean problem play / Shakespearean tragedy / Site-specific /
            Southern Gothic / Spanish drama / Street theatre / Sturm und Drang / Surrealist drama / Swedish drama / Symbolist drama / Thriller / Tragedy / Tragicomedy / Verbatim theatre  / Welsh drama / Youth theatre, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            Drama,,Verbatim theatre</h6>
          </div>

          <div id="ftr_list" class="entry">
            <label for="ftr_list" class="fixedwidth">FEATURES: <?php echo error_for('ftr_tr_lg_chckd') ?><?php echo error_for('ftr_nm_array_excss') ?><?php echo error_for('ftr_empty') ?><?php echo error_for('ftr_dplct') ?><?php echo error_for('ftr_nm_excss_lngth') ?><?php echo error_for('ftr_nm') ?></label>
            <input type="text" name="ftr_list" id="ftr_list" value="<?php echo $ftr_list; ?>" class="entryfield <?php echo errorfield('ftr_tr_lg_chckd') ?> <?php echo errorfield('ftr_nm_array_excss') ?> <?php echo errorfield('ftr_empty') ?> <?php echo errorfield('ftr_dplct') ?> <?php echo errorfield('ftr_nm_excss_lngth') ?> <?php echo errorfield('ftr_nm') ?>"/>
            <h6>i.e. All Male Cast / All Female Cast / All Black Cast (if unconventional) / Modernised Setting / Alternate Setting / Radical Interpretation / Alternating Cast / Site Specific / Foreign Language / Spanish Language / Sign Language, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            All Male Cast,,Site Specific</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="thm_list" class="entry">
            <label for="thm_list" class="fixedwidth">THEMES: <?php echo error_for('thm_tr_lg_chckd') ?><?php echo error_for('thm_nm_array_excss') ?><?php echo error_for('thm_empty') ?><?php echo error_for('thm_dplct') ?><?php echo error_for('thm_nm_excss_lngth') ?><?php echo error_for('thm_nm') ?></label>
            <input type="text" name="thm_list" id="thm_list" value="<?php echo $thm_list; ?>" class="entryfield <?php echo errorfield('thm_tr_lg_chckd') ?> <?php echo errorfield('thm_nm_array_excss') ?> <?php echo errorfield('thm_empty') ?> <?php echo errorfield('thm_dplct') ?> <?php echo errorfield('thm_nm_excss_lngth') ?> <?php echo errorfield('thm_nm') ?>"/>
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
          <div id="wri_list" class="entry">
            <label for="wri_list" class="fixedwidth">WRITERS: <?php echo error_for('wri_tr_lg_chckd') ?><?php echo error_for('wri_rl_array_excss') ?><?php echo error_for('wri_empty') ?><?php echo error_for('wri_pls_excss') ?><?php echo error_for('src_mat_cln_excss') ?><?php echo error_for('src_mat_rl') ?><?php echo error_for('src_mats_array_excss') ?><?php echo error_for('src_mat_empty') ?><?php echo error_for('src_mat_hyphn_excss') ?><?php echo error_for('src_mat_sffx') ?><?php echo error_for('src_mat_hyphn') ?><?php echo error_for('src_mat_smcln_excss') ?><?php echo error_for('src_mat_dplct') ?><?php echo error_for('src_frmt_nm_excss_lngth') ?><?php echo error_for('src_frmt_url') ?><?php echo error_for('src_mat_nm_excss_lngth') ?><?php echo error_for('src_mat_url') ?><?php echo error_for('src_mat_smcln') ?><?php echo error_for('src_mat_cln') ?><?php echo error_for('wri_pls') ?><?php echo error_for('wri_cln_excss') ?><?php echo error_for('wri_rl') ?><?php echo error_for('wri_comp_prsn_empty') ?><?php echo error_for('wri_pipe_excss') ?><?php echo error_for('wri_pipe') ?><?php echo error_for('wri_prsn_empty') ?><?php echo error_for('wri_rl_ttl_array_excss') ?><?php echo error_for('wri_comp_tld_excss') ?><?php echo error_for('wri_comp_rl') ?><?php echo error_for('wri_comp_tld') ?><?php echo error_for('wri_comp_hyphn_excss') ?><?php echo error_for('wri_comp_sffx') ?><?php echo error_for('wri_comp_hyphn') ?><?php echo error_for('wri_comp_dplct') ?><?php echo error_for('wri_comp_nm_excss_lngth') ?><?php echo error_for('wri_comp_url') ?><?php echo error_for('wri_prsn_tld_excss') ?><?php echo error_for('wri_prsn_rl') ?><?php echo error_for('wri_prsn_tld') ?><?php echo error_for('wri_prsn_hyphn_excss') ?><?php echo error_for('wri_prsn_sffx') ?><?php echo error_for('wri_prsn_hyphn') ?><?php echo error_for('wri_prsn_smcln_excss') ?><?php echo error_for('wri_prsn_dplct') ?><?php echo error_for('wri_prsn_excss_lngth') ?><?php echo error_for('wri_prsn_smcln') ?><?php echo error_for('wri_prsn_nm') ?><?php echo error_for('wri_prsn_url') ?><?php echo error_for('wri_cln') ?></label>
            <input type="text" name="wri_list" id="wri_list" value="<?php echo $wri_list; ?>" class="entryfield <?php echo errorfield('wri_tr_lg_chckd') ?> <?php echo errorfield('wri_rl_array_excss') ?> <?php echo errorfield('wri_empty') ?> <?php echo errorfield('wri_pls_excss') ?> <?php echo errorfield('src_mat_cln_excss') ?> <?php echo errorfield('src_mat_rl') ?> <?php echo errorfield('src_mats_array_excss') ?> <?php echo errorfield('src_mat_empty') ?> <?php echo errorfield('src_mat_hyphn_excss') ?> <?php echo errorfield('src_mat_sffx') ?> <?php echo errorfield('src_mat_hyphn') ?> <?php echo errorfield('src_mat_smcln_excss') ?> <?php echo errorfield('src_mat_dplct') ?> <?php echo errorfield('src_frmt_nm_excss_lngth') ?> <?php echo errorfield('src_frmt_url') ?> <?php echo errorfield('src_mat_nm_excss_lngth') ?> <?php echo errorfield('src_mat_url') ?> <?php echo errorfield('src_mat_smcln') ?> <?php echo errorfield('src_mat_cln') ?> <?php echo errorfield('wri_pls') ?> <?php echo errorfield('wri_cln_excss') ?> <?php echo errorfield('wri_rl') ?> <?php echo errorfield('wri_comp_prsn_empty') ?> <?php echo errorfield('wri_pipe_excss') ?> <?php echo errorfield('wri_pipe') ?> <?php echo errorfield('wri_prsn_empty') ?> <?php echo errorfield('wri_rl_ttl_array_excss') ?> <?php echo errorfield('wri_comp_tld_excss') ?> <?php echo errorfield('wri_comp_rl') ?> <?php echo errorfield('wri_comp_tld') ?> <?php echo errorfield('wri_comp_hyphn_excss') ?> <?php echo errorfield('wri_comp_sffx') ?> <?php echo errorfield('wri_comp_hyphn') ?> <?php echo errorfield('wri_comp_dplct') ?> <?php echo errorfield('wri_comp_nm_excss_lngth') ?> <?php echo errorfield('wri_comp_url') ?> <?php echo errorfield('wri_prsn_tld_excss') ?> <?php echo errorfield('wri_prsn_rl') ?> <?php echo errorfield('wri_prsn_tld') ?> <?php echo errorfield('wri_prsn_hyphn_excss') ?> <?php echo errorfield('wri_prsn_sffx') ?> <?php echo errorfield('wri_prsn_hyphn') ?> <?php echo errorfield('wri_prsn_smcln_excss') ?> <?php echo errorfield('wri_prsn_dplct') ?> <?php echo errorfield('wri_prsn_excss_lngth') ?> <?php echo errorfield('wri_prsn_smcln') ?> <?php echo errorfield('wri_prsn_nm') ?> <?php echo errorfield('wri_prsn_url') ?> <?php echo errorfield('wri_cln') ?>"/>
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
            By::Graham;;Linehan,,From the::The Ladykillers;;motion picture screenplay++by::William;;Rose**>>and by special arrangement with~~StudioCanal***||</h6>
          </div>

          <div id="prdcr_list" class="entry">
            <label for="prdcr_list" class="fixedwidth">PRODUCERS: <?php echo error_for('prdcr_tr_lg_chckd') ?><?php echo error_for('prdcr_rl_array_excss') ?><?php echo error_for('prdcr_empty') ?><?php echo error_for('prdcr_cln_excss') ?><?php echo error_for('prdcr_rl') ?><?php echo error_for('prdcr_comp_prsn_empty') ?><?php echo error_for('prdcr_pipe_excss') ?><?php echo error_for('prdcr_comp_tld_excss') ?><?php echo error_for('prdcr_comp_sb_rl') ?><?php echo error_for('prdcr_comp_tld') ?><?php echo error_for('prdcr_pipe') ?><?php echo error_for('prdcr_prsn_tld_excss') ?><?php echo error_for('prdcr_prsn_sb_rl') ?><?php echo error_for('prdcr_prsn_tld') ?><?php echo error_for('prdcr_compprsn_rl_empty') ?><?php echo error_for('prdcr_compprsn_tld_excss') ?><?php echo error_for('prdcr_compprsn_rl') ?><?php echo error_for('prdcr_compprsn_empty') ?><?php echo error_for('prdcr_compprsn_crt_excss') ?><?php echo error_for('prdcr_compprsn_sb_rl') ?><?php echo error_for('prdcr_compprsn_crt') ?><?php echo error_for('prdcr_compprsn_tld') ?><?php echo error_for('prdcr_rl_ttl_array_excss') ?><?php echo error_for('prdcr_comp_hyphn_excss') ?><?php echo error_for('prdcr_comp_sffx') ?><?php echo error_for('prdcr_comp_hyphn') ?><?php echo error_for('prdcr_comp_dplct') ?><?php echo error_for('prdcr_comp_nm_excss_lngth') ?><?php echo error_for('prdcr_comp_url') ?><?php echo error_for('prdcr_prsn_hyphn_excss') ?><?php echo error_for('prdcr_prsn_sffx') ?><?php echo error_for('prdcr_prsn_hyphn') ?><?php echo error_for('prdcr_prsn_smcln_excss') ?><?php echo error_for('prdcr_prsn_dplct') ?><?php echo error_for('prdcr_prsn_excss_lngth') ?><?php echo error_for('prdcr_prsn_smcln') ?><?php echo error_for('prdcr_prsn_nm') ?><?php echo error_for('prdcr_prsn_url') ?><?php echo error_for('prdcr_cln') ?></label>
            <input type="text" name="prdcr_list" id="prdcr_list" value="<?php echo $prdcr_list; ?>" class="entryfield <?php echo errorfield('prdcr_tr_lg_chckd') ?> <?php echo errorfield('prdcr_rl_array_excss') ?> <?php echo errorfield('prdcr_empty') ?> <?php echo errorfield('prdcr_cln_excss') ?> <?php echo errorfield('prdcr_rl') ?> <?php echo errorfield('prdcr_comp_prsn_empty') ?> <?php echo errorfield('prdcr_pipe_excss') ?> <?php echo errorfield('prdcr_comp_tld_excss') ?> <?php echo errorfield('prdcr_comp_sb_rl') ?> <?php echo errorfield('prdcr_comp_tld') ?> <?php echo errorfield('prdcr_pipe') ?> <?php echo errorfield('prdcr_prsn_tld_excss') ?> <?php echo errorfield('prdcr_prsn_sb_rl') ?> <?php echo errorfield('prdcr_prsn_tld') ?> <?php echo errorfield('prdcr_compprsn_rl_empty') ?> <?php echo errorfield('prdcr_compprsn_tld_excss') ?> <?php echo errorfield('prdcr_compprsn_empty') ?> <?php echo errorfield('prdcr_compprsn_rl') ?> <?php echo errorfield('prdcr_compprsn_crt_excss') ?> <?php echo errorfield('prdcr_compprsn_sb_rl') ?> <?php echo errorfield('prdcr_compprsn_crt') ?> <?php echo errorfield('prdcr_compprsn_tld') ?> <?php echo errorfield('prdcr_rl_ttl_array_excss') ?> <?php echo errorfield('prdcr_comp_hyphn_excss') ?> <?php echo errorfield('prdcr_comp_sffx') ?> <?php echo errorfield('prdcr_comp_hyphn') ?> <?php echo errorfield('prdcr_comp_dplct') ?> <?php echo errorfield('prdcr_comp_nm_excss_lngth') ?> <?php echo errorfield('prdcr_comp_url') ?> <?php echo errorfield('prdcr_prsn_hyphn_excss') ?> <?php echo errorfield('prdcr_prsn_sffx') ?> <?php echo errorfield('prdcr_prsn_hyphn') ?> <?php echo errorfield('prdcr_prsn_smcln_excss') ?> <?php echo errorfield('prdcr_prsn_dplct') ?> <?php echo errorfield('prdcr_prsn_excss_lngth') ?> <?php echo errorfield('prdcr_prsn_smcln') ?> <?php echo errorfield('prdcr_prsn_nm') ?> <?php echo errorfield('prdcr_prsn_url') ?> <?php echo errorfield('prdcr_cln') ?>"/>
            <h6>i.e. Producer / Co-Producer (for Production Company) / Artistic Director (of Production Company) (inaugural / outgoing production - if applicable) / Deputy Artistic Director / Associate Director / Executive Producer / Associate Producer, etc.</br>
            - Separate multiple entries using double comma [,,], roles using double colon [::], multiple parties within roles using double chevron [>>], optional sub-roles using double tilde [~~] (mandatory for company members), multiple people sharing company roles using double logical negation symbol [¬¬], optional credit display role (for company members only; to express the singular form of company role for credits if it is otherwise expressed in plural form (i.e. if shared role: Joint Artistic Director)) using double caret [^^], and (if person) given name and family name using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if people are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - Indicate if rights grantors using [*]; if company members who require a producing credit using [*] (company members only).</br>
            - To differentiate identically-named people or companies, use a double hyphen followed by an integer between 1 and 99:- Associate Director::Howard;;Davies--2</br>
            Co-produced by::National Theatre Company||Artistic Director~~Nicholas;;Hytner//Associate Director~~Howard;;Davies>>Headlong Theatre||Artistic Director~~Rupert;;Goold//Associate Director~~Robert;;Icke</br>
            Produced by::Royal Court Theatre||Artistic Director~~Vicky;;Featherstone//Associate Directors~~Associate Director^^Carrie;;Cracknell¬¬Associate Director^^Simon;;Godwin¬¬Associate Director^^John;;Tiffany</br>
            Presented by::Lightbox Theatre||>>in association with~~Finborough Theatre||Artistic Director~~Neil;;McPherson*</br>
            Presented by::Bill;;Kenwright>>by special arrangement with~~Agatha Christie Ltd***||</h6>
          </div>

          <div id="prf_list" class="entry">
            <label for="prf_list" class="fixedwidth">PERFORMERS (people): <?php echo error_for('prf_tr_lg_chckd') ?><?php echo error_for('prf_nm_rl_array_excss') ?><?php echo error_for('prf_empty') ?><?php echo error_for('prf_cln_excss') ?><?php echo error_for('prf_rl_array_excss') ?><?php echo error_for('prf_rl_smcln_excss') ?><?php echo error_for('prf_rl_dscr_excss_lngth') ?><?php echo error_for('prf_rl_smcln') ?><?php echo error_for('prf_rl_pipe_excss') ?><?php echo error_for('prf_rl_lnk_excss_lngth') ?><?php echo error_for('prf_rl_pipe') ?><?php echo error_for('prf_rl_excss_lngth') ?><?php echo error_for('prf_rl_dplct') ?><?php echo error_for('prf_cln') ?><?php echo error_for('prf_hyphn_excss') ?><?php echo error_for('prf_sffx') ?><?php echo error_for('prf_hyphn') ?><?php echo error_for('prf_smcln_excss') ?><?php echo error_for('prf_dplct') ?><?php echo error_for('prf_fll_nm_excss_lngth') ?><?php echo error_for('prf_smcln') ?><?php echo error_for('prf_nm') ?><?php echo error_for('prf_url') ?></label>
            <input type="text" name="prf_list" id="prf_list" value="<?php echo $prf_list; ?>" class="entryfield <?php echo errorfield('prf_tr_lg_chckd') ?> <?php echo errorfield('prf_nm_rl_array_excss') ?> <?php echo errorfield('prf_empty') ?> <?php echo errorfield('prf_cln_excss') ?> <?php echo errorfield('prf_rl_array_excss') ?> <?php echo errorfield('prf_rl_smcln_excss') ?> <?php echo errorfield('prf_rl_dscr_excss_lngth') ?> <?php echo errorfield('prf_rl_smcln') ?> <?php echo errorfield('prf_rl_pipe_excss') ?> <?php echo errorfield('prf_rl_lnk_excss_lngth') ?> <?php echo errorfield('prf_rl_pipe') ?> <?php echo errorfield('prf_rl_excss_lngth') ?> <?php echo errorfield('prf_rl_dplct') ?> <?php echo errorfield('prf_cln') ?> <?php echo errorfield('prf_hyphn_excss') ?> <?php echo errorfield('prf_sffx') ?> <?php echo errorfield('prf_hyphn') ?> <?php echo errorfield('prf_smcln_excss') ?> <?php echo errorfield('prf_dplct') ?> <?php echo errorfield('prf_fll_nm_excss_lngth') ?> <?php echo errorfield('prf_smcln') ?> <?php echo errorfield('prf_nm') ?> <?php echo errorfield('prf_url') ?>"/>
            <h6>- Separate multiple entries using double comma [,,], roles using double colon [::] (optional '[alt]' display using star [*]; optional link using double pipes [||] if variant name is used; and optional role description using double semi-colon [;;]), multiple roles using using double slash [//], and given name and family name using double semi-colon [;;]:-</br>
            - To differentiate identically-named people, use a double hyphen followed by an integer between 1 and 99:- Patrick;;Stewart--2::Vladimir</br>
            Ian;;McKellen::Estragon;;A tramp//Lucky,,Patrick;;Stewart::Vladimir;;Another tramp//Pozzo</br>
            Abigail;;Cruttenden::Irina||Arkadina;;An actress//Polina;;Ilya's wife,,Gyuri;;Sarossy::Boris||Trigorin;;A well-known writer//Semyon||Medvedenko;;A teacher</br>
            Ian;;McKellen::Sorin||Petr;;Irina's brother*,,William;;Gaunt::Sorin||Petr;;Irina's brother*</h6>
          </div>

          <div id="prd_othr_prts" class="entry">
            <label for="prd_othr_prts" class="fixedwidth">OTHER PARTS PLAYED BY MEMBERS OF THE COMPANY:</label>
            <input type="checkbox" name="prd_othr_prts" id="prd_othr_prts"<?php if($prd_othr_prts) {echo ' checked="checked"';} ?>/>
            <h6>Check box if other parts are played by members of the company.</h6>
          </div>

          <div id="prd_cst_nt" class="entry">
            <label for="prd_cst_nt" class="fixedwidth">CAST NOTES: <?php echo error_for('prd_cst_nt_excss_lngth') ?></label>
            <input type="text" name="prd_cst_nt" id="prd_cst_nt" maxlength="255" value="<?php echo $prd_cst_nt; ?>" class="entryfield <?php echo errorfield('prd_cst_nt_excss_lngth') ?>"/>
            <h6>i.e. Plus 185 community cast members; Actor playing Swiss Cheese replaced during rehearsals, etc.</h6>
          </div>

          <div id="us_list" class="entry">
            <label for="us_list" class="fixedwidth">UNDERSTUDIES (people): <?php echo error_for('us_tr_lg_chckd') ?><?php echo error_for('us_nm_rl_array_excss') ?><?php echo error_for('us_empty') ?><?php echo error_for('us_cln_excss') ?><?php echo error_for('us_rl_array_excss') ?><?php echo error_for('us_rl_smcln_excss') ?><?php echo error_for('us_rl_dscr_excss_lngth') ?><?php echo error_for('us_rl_smcln') ?><?php echo error_for('us_rl_pipe_excss') ?><?php echo error_for('us_rl_lnk_excss_lngth') ?><?php echo error_for('us_rl_pipe') ?><?php echo error_for('us_rl_excss_lngth') ?><?php echo error_for('us_rl_dplct') ?><?php echo error_for('us_cln') ?><?php echo error_for('us_hyphn_excss') ?><?php echo error_for('us_sffx') ?><?php echo error_for('us_hyphn') ?><?php echo error_for('us_smcln_excss') ?><?php echo error_for('us_dplct') ?><?php echo error_for('us_fll_nm_excss_lngth') ?><?php echo error_for('us_smcln') ?><?php echo error_for('us_nm') ?><?php echo error_for('us_url') ?></label>
            <input type="text" name="us_list" id="us_list" value="<?php echo $us_list; ?>" class="entryfield <?php echo errorfield('us_tr_lg_chckd') ?> <?php echo errorfield('us_nm_rl_array_excss') ?> <?php echo errorfield('us_empty') ?> <?php echo errorfield('us_cln_excss') ?> <?php echo errorfield('us_rl_array_excss') ?> <?php echo errorfield('us_rl_smcln_excss') ?> <?php echo errorfield('us_rl_dscr_excss_lngth') ?> <?php echo errorfield('us_rl_smcln') ?> <?php echo errorfield('us_rl_pipe_excss') ?> <?php echo errorfield('us_rl_lnk_excss_lngth') ?> <?php echo errorfield('us_rl_pipe') ?> <?php echo errorfield('us_rl_excss_lngth') ?> <?php echo errorfield('us_rl_dplct') ?> <?php echo errorfield('us_cln') ?> <?php echo errorfield('us_hyphn_excss') ?> <?php echo errorfield('us_sffx') ?> <?php echo errorfield('us_hyphn') ?> <?php echo errorfield('us_smcln_excss') ?> <?php echo errorfield('us_dplct') ?> <?php echo errorfield('us_fll_nm_excss_lngth') ?> <?php echo errorfield('us_smcln') ?> <?php echo errorfield('us_nm') ?> <?php echo errorfield('us_url') ?>"/>
            <h6>- Separate multiple entries using double comma [,,], roles using double colon [::] (optional '[alt]' display using star [*]; optional link using double pipes [||] if variant name is used; and optional role description using double semi-colon [;;]), multiple roles using using double slash [//], and given name and family name using double semi-colon [;;]:-</br>
            - To differentiate identically-named people, use a double hyphen followed by an integer between 1 and 99:- Gareth;;Williams--2::Vladimir;;Another tramp//Pozzo</br>
            Colin;;Haigh::Estragon;;A tramp//Lucky,,Gareth;;Williams::Vladimir;;Another tramp//Pozzo</br>
            Abigail;;Cruttenden::Irina||Arkadina;;An actress//Polina;;Ilya's wife,,Gyuri;;Sarossy::Boris||Trigorin;;A well-known writer//Semyon||Medvedenko;;A teacher</br>
            Ian;;McKellen::Sorin*,,William;;Gaunt::Sorin*</h6>
          </div>

          <div id="mscn_list" class="entry">
            <label for="mscn_list" class="fixedwidth">MUSICIANS: <?php echo error_for('mscn_tr_lg_chckd') ?><?php echo error_for('mscn_rl_array_excss') ?><?php echo error_for('mscn_empty') ?><?php echo error_for('mscn_cln_excss') ?><?php echo error_for('mscn_rl') ?><?php echo error_for('mscn_comp_prsn_empty') ?><?php echo error_for('mscn_pipe_excss') ?><?php echo error_for('mscn_comp_tld_excss') ?><?php echo error_for('mscn_comp_rl') ?><?php echo error_for('mscn_comp_tld') ?><?php echo error_for('mscn_pipe') ?><?php echo error_for('mscn_prsn_tld_excss') ?><?php echo error_for('mscn_prsn_rl') ?><?php echo error_for('mscn_prsn_tld') ?><?php echo error_for('mscn_compprsn_rl_empty') ?><?php echo error_for('mscn_compprsn_tld_excss') ?><?php echo error_for('mscn_compprsn_rl') ?><?php echo error_for('mscn_compprsn_empty') ?><?php echo error_for('mscn_compprsn_crt_excss') ?><?php echo error_for('mscn_compprsn_sb_rl') ?><?php echo error_for('mscn_compprsn_crt') ?><?php echo error_for('mscn_compprsn_tld') ?><?php echo error_for('mscn_rl_ttl_array_excss') ?><?php echo error_for('mscn_comp_hyphn_excss') ?><?php echo error_for('mscn_comp_sffx') ?><?php echo error_for('mscn_comp_hyphn') ?><?php echo error_for('mscn_comp_dplct') ?><?php echo error_for('mscn_comp_nm_excss_lngth') ?><?php echo error_for('mscn_comp_url') ?><?php echo error_for('mscn_prsn_hyphn_excss') ?><?php echo error_for('mscn_prsn_sffx') ?><?php echo error_for('mscn_prsn_hyphn') ?><?php echo error_for('mscn_prsn_smcln_excss') ?><?php echo error_for('mscn_prsn_dplct') ?><?php echo error_for('mscn_prsn_excss_lngth') ?><?php echo error_for('mscn_prsn_smcln') ?><?php echo error_for('mscn_prsn_nm') ?><?php echo error_for('mscn_prsn_url') ?><?php echo error_for('mscn_cln') ?></label>
            <input type="text" name="mscn_list" id="mscn_list" value="<?php echo $mscn_list; ?>" class="entryfield <?php echo errorfield('mscn_tr_lg_chckd') ?> <?php echo errorfield('mscn_rl_array_excss') ?> <?php echo errorfield('mscn_empty') ?> <?php echo errorfield('mscn_cln_excss') ?> <?php echo errorfield('mscn_rl') ?> <?php echo errorfield('mscn_comp_prsn_empty') ?> <?php echo errorfield('mscn_pipe_excss') ?> <?php echo errorfield('mscn_comp_tld_excss') ?> <?php echo errorfield('mscn_comp_rl') ?> <?php echo errorfield('mscn_comp_tld') ?> <?php echo errorfield('mscn_pipe') ?> <?php echo errorfield('mscn_prsn_tld_excss') ?> <?php echo errorfield('mscn_prsn_rl') ?> <?php echo errorfield('mscn_prsn_tld') ?> <?php echo errorfield('mscn_compprsn_rl_empty') ?> <?php echo errorfield('mscn_compprsn_tld_excss') ?> <?php echo errorfield('mscn_compprsn_rl') ?> <?php echo errorfield('mscn_compprsn_empty') ?> <?php echo errorfield('mscn_compprsn_crt_excss') ?> <?php echo errorfield('mscn_compprsn_sb_rl') ?> <?php echo errorfield('mscn_compprsn_crt') ?> <?php echo errorfield('mscn_compprsn_tld') ?> <?php echo errorfield('mscn_rl_ttl_array_excss') ?> <?php echo errorfield('mscn_comp_hyphn_excss') ?> <?php echo errorfield('mscn_comp_sffx') ?> <?php echo errorfield('mscn_comp_hyphn') ?> <?php echo errorfield('mscn_comp_dplct') ?> <?php echo errorfield('mscn_comp_nm_excss_lngth') ?> <?php echo errorfield('mscn_comp_url') ?> <?php echo errorfield('mscn_prsn_hyphn_excss') ?> <?php echo errorfield('mscn_prsn_sffx') ?> <?php echo errorfield('mscn_prsn_hyphn') ?> <?php echo errorfield('mscn_prsn_smcln_excss') ?> <?php echo errorfield('mscn_prsn_dplct') ?> <?php echo errorfield('mscn_prsn_excss_lngth') ?> <?php echo errorfield('mscn_prsn_smcln') ?> <?php echo errorfield('mscn_prsn_nm') ?> <?php echo errorfield('mscn_prsn_url') ?> <?php echo errorfield('mscn_cln') ?>"/>
            <h6>- Separate multiple entries using double comma [,,], roles using double colon [::], multiple parties within roles using double chevron [>>], optional sub-roles using double tilde [~~] (mandatory for company members), multiple people sharing company roles using double logical negation symbol [¬¬], optional credit display role (for company members only; to express the singular form of company role for credits if it is otherwise expressed in plural form (i.e. if shared role: Strings)) using double caret [^^], and (if person) given name and family name using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if people are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - To differentiate identically-named people or companies, use a double hyphen followed by an integer between 1 and 99:- Orchestra Conducter::Simon;;Over--2</br>
            Orchestra::Southbank Sinfonia||Flute / Piccolo / Alto Flute~~Nancy;;Ruffer//Strings~~String^^John;;Smithson¬¬String^^Carl;;Maywick//Orchestra Conductor~~Simon;;Over,,Soundtrack: "Rebellion (Lies)"::Arcade Fire||,,Musicians::The Craze||Music (writer and composer)~~Grant;;Olding//Lead vocals / Guitar / Keys / Accordion / Harmonica~~John;;Sneesby//Guitar / Banjo / Backing vocals~~Philip;;James//Double bass / Electric bass / Backing vocals~~Richard;;Coughlan//Percussion (including Washboard and Spoons / Drums / Backing Vocals)~~Ben;;Brooker</h6>
          </div>

          <div id="crtv_list" class="entry">
            <label for="crtv_list" class="fixedwidth">CREATIVE TEAM: <?php echo error_for('crtv_tr_lg_chckd') ?><?php echo error_for('crtv_rl_array_excss') ?><?php echo error_for('crtv_empty') ?><?php echo error_for('crtv_cln_excss') ?><?php echo error_for('crtv_rl') ?><?php echo error_for('crtv_comp_prsn_empty') ?><?php echo error_for('crtv_pipe_excss') ?><?php echo error_for('crtv_pipe') ?><?php echo error_for('crtv_prsn_empty') ?><?php echo error_for('crtv_rl_ttl_array_excss') ?><?php echo error_for('crtv_comp_tld_excss') ?><?php echo error_for('crtv_comp_rl') ?><?php echo error_for('crtv_comp_tld') ?><?php echo error_for('crtv_comp_hyphn_excss') ?><?php echo error_for('crtv_comp_sffx') ?><?php echo error_for('crtv_comp_hyphn') ?><?php echo error_for('crtv_comp_dplct') ?><?php echo error_for('crtv_comp_nm_excss_lngth') ?><?php echo error_for('crtv_comp_url') ?><?php echo error_for('crtv_prsn_tld_excss') ?><?php echo error_for('crtv_prsn_rl') ?><?php echo error_for('crtv_prsn_tld') ?><?php echo error_for('crtv_prsn_hyphn_excss') ?><?php echo error_for('crtv_prsn_sffx') ?><?php echo error_for('crtv_prsn_hyphn') ?><?php echo error_for('crtv_prsn_smcln_excss') ?><?php echo error_for('crtv_prsn_dplct') ?><?php echo error_for('crtv_prsn_excss_lngth') ?><?php echo error_for('crtv_prsn_smcln') ?><?php echo error_for('crtv_prsn_nm') ?><?php echo error_for('crtv_prsn_url') ?><?php echo error_for('crtv_cln') ?></label>
            <input type="text" name="crtv_list" id="crtv_list" value="<?php echo $crtv_list; ?>" class="entryfield <?php echo errorfield('crtv_tr_lg_chckd') ?> <?php echo errorfield('crtv_rl_array_excss') ?> <?php echo errorfield('crtv_empty') ?> <?php echo errorfield('crtv_cln_excss') ?> <?php echo errorfield('crtv_rl') ?> <?php echo errorfield('crtv_comp_prsn_empty') ?> <?php echo errorfield('crtv_pipe_excss') ?> <?php echo errorfield('crtv_pipe') ?> <?php echo errorfield('crtv_prsn_empty') ?> <?php echo errorfield('crtv_rl_ttl_array_excss') ?> <?php echo errorfield('crtv_comp_tld_excss') ?> <?php echo errorfield('crtv_comp_rl') ?> <?php echo errorfield('crtv_comp_tld') ?> <?php echo errorfield('crtv_comp_hyphn_excss') ?> <?php echo errorfield('crtv_comp_sffx') ?> <?php echo errorfield('crtv_comp_hyphn') ?> <?php echo errorfield('crtv_comp_dplct') ?> <?php echo errorfield('crtv_comp_nm_excss_lngth') ?> <?php echo errorfield('crtv_comp_url') ?> <?php echo errorfield('crtv_prsn_tld_excss') ?> <?php echo errorfield('crtv_prsn_rl') ?> <?php echo errorfield('crtv_prsn_tld') ?> <?php echo errorfield('crtv_prsn_hyphn_excss') ?> <?php echo errorfield('crtv_prsn_sffx') ?> <?php echo errorfield('crtv_prsn_hyphn') ?> <?php echo errorfield('crtv_prsn_smcln_excss') ?> <?php echo errorfield('crtv_prsn_dplct') ?> <?php echo errorfield('crtv_prsn_excss_lngth') ?> <?php echo errorfield('crtv_prsn_smcln') ?> <?php echo errorfield('crtv_prsn_nm') ?> <?php echo errorfield('crtv_prsn_url') ?> <?php echo errorfield('crtv_cln') ?>"/>
            <h6>i.e. Director / Designer / Costume / Lighting / Music (plays) / Sound / Soundscape / Fight Direction / Musical Direction / Musical Staging / Musical Arrangement / Vocal Arrangement / Choral Arrangement / Orchestrations / Songs / Songs Translation / Voice Director / Dialect Coach / Dance Arrangement / Choreography / Movement / Video / Projections / Cinematography / Digital Artist / Visual Designer / Audio Designer / AV designer / Animation Director / Drawings / Flying / Aerial Choreographer / Special Effects / Visual Effects / Magic Consultant / Makeup / Wigs / Masks / Puppets / Mime Work / Bullwhip Specialist / Gunspinning / Director of Ropework / Casting Director / Dramaturg / Script Consultant / Original Director / Assistant Director / Associate Director / Assistant Designer / Associate Designer / Assistant Lighting Designer / Associate Lighting Designer / Costume Supervisor / Assistant Costume Supervisor / Assistant Sound Designer / Associate Sound Designer / Musical Supervisor / Music Programming / Assistant Voice Coach / Assistant Choreographer / Collaborator / Consultant, etc.</br>
            - Separate multiple entries using double comma [,,], roles using double colon [::], multiple parties within roles using double chevron [>>], optional credit display role using double tilde [~~] (to express the singular form of role for credits if it is otherwise expressed in plural form), and (if person) given name and family name using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if people are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - To differentiate identically-named people or companies, use a double hyphen followed by an integer between 1 and 99:- Director::Michael;;Grandage--2</br>
            Director::Tim;;Crouch,,Co-Directors::Co-Director~~Karl;;James>>Co-Director~~Andy;;Smith,,Sound Designers::Autograph||Sound Designer~~Andrew;;Bruce//Sound Designer~~Nick;;Lidster,,Composers::Composer~~Ben;;Ringham>>Composer~~Max;;Ringham</h6>
          </div>

          <div id="prdtm_list" class="entry">
            <label for="prdtm_list" class="fixedwidth">PRODUCTION TEAM: <?php echo error_for('prdtm_tr_lg_chckd') ?><?php echo error_for('prdtm_rl_array_excss') ?><?php echo error_for('prdtm_empty') ?><?php echo error_for('prdtm_cln_excss') ?><?php echo error_for('prdtm_rl') ?><?php echo error_for('prdtm_comp_prsn_empty') ?><?php echo error_for('prdtm_pipe_excss') ?><?php echo error_for('prdtm_pipe') ?><?php echo error_for('prdtm_prsn_empty') ?><?php echo error_for('prdtm_rl_ttl_array_excss') ?><?php echo error_for('prdtm_comp_tld_excss') ?><?php echo error_for('prdtm_comp_rl') ?><?php echo error_for('prdtm_comp_tld') ?><?php echo error_for('prdtm_comp_hyphn_excss') ?><?php echo error_for('prdtm_comp_sffx') ?><?php echo error_for('prdtm_comp_hyphn') ?><?php echo error_for('prdtm_comp_dplct') ?><?php echo error_for('prdtm_comp_nm_excss_lngth') ?><?php echo error_for('prdtm_comp_url') ?><?php echo error_for('prdtm_prsn_tld_excss') ?><?php echo error_for('prdtm_prsn_rl') ?><?php echo error_for('prdtm_prsn_tld') ?><?php echo error_for('prdtm_prsn_hyphn_excss') ?><?php echo error_for('prdtm_prsn_sffx') ?><?php echo error_for('prdtm_prsn_hyphn') ?><?php echo error_for('prdtm_prsn_smcln_excss') ?><?php echo error_for('prdtm_prsn_dplct') ?><?php echo error_for('prdtm_prsn_excss_lngth') ?><?php echo error_for('prdtm_prsn_smcln') ?><?php echo error_for('prdtm_prsn_nm') ?><?php echo error_for('prdtm_prsn_url') ?><?php echo error_for('prdtm_cln') ?></label>
            <input type="text" name="prdtm_list" id="prdtm_list" value="<?php echo $prdtm_list; ?>" class="entryfield <?php echo errorfield('prdtm_tr_lg_chckd') ?> <?php echo errorfield('prdtm_rl_array_excss') ?> <?php echo errorfield('prdtm_empty') ?> <?php echo errorfield('prdtm_cln_excss') ?> <?php echo errorfield('prdtm_rl') ?> <?php echo errorfield('prdtm_comp_prsn_empty') ?> <?php echo errorfield('prdtm_pipe_excss') ?> <?php echo errorfield('prdtm_pipe') ?> <?php echo errorfield('prdtm_prsn_empty') ?> <?php echo errorfield('prdtm_rl_ttl_array_excss') ?> <?php echo errorfield('prdtm_comp_tld_excss') ?> <?php echo errorfield('prdtm_comp_rl') ?> <?php echo errorfield('prdtm_comp_tld') ?> <?php echo errorfield('prdtm_comp_hyphn_excss') ?> <?php echo errorfield('prdtm_comp_sffx') ?> <?php echo errorfield('prdtm_comp_hyphn') ?> <?php echo errorfield('prdtm_comp_dplct') ?> <?php echo errorfield('prdtm_comp_nm_excss_lngth') ?> <?php echo errorfield('prdtm_comp_url') ?> <?php echo errorfield('prdtm_prsn_tld_excss') ?> <?php echo errorfield('prdtm_prsn_rl') ?> <?php echo errorfield('prdtm_prsn_tld') ?> <?php echo errorfield('prdtm_prsn_hyphn_excss') ?> <?php echo errorfield('prdtm_prsn_sffx') ?> <?php echo errorfield('prdtm_prsn_hyphn') ?> <?php echo errorfield('prdtm_prsn_smcln_excss') ?> <?php echo errorfield('prdtm_prsn_dplct') ?> <?php echo errorfield('prdtm_prsn_excss_lngth') ?> <?php echo errorfield('prdtm_prsn_smcln') ?> <?php echo errorfield('prdtm_prsn_nm') ?> <?php echo errorfield('prdtm_prsn_url') ?> <?php echo errorfield('prdtm_cln') ?>"/>
            <h6>i.e. Literary Manager / Stage Manager / Assistant Stage Manager / Deputy Stage Manager / Technical Director / Technical Stage Manager / Staff Director / Production Manager / Deputy Production Manager / Production Assistant / Production Intern / Scenic Painter / Set Construction / Head Electrician / Head Mechanist / Prop Buyer / Wardrobe Supervisor / Wardrobe Assistant / Press / Publicity / Marketing / Production Photographer / Education Director / Education Associate / Sponsor, etc.</br>
            - Separate multiple entries using double comma [,,], roles using double colon [::], multiple parties within roles using double chevron [>>], optional credit display role using double tilde [~~] (to express the singular form of role for credits if it is otherwise expressed in plural form), and (if person) given name and family name using double semi-colon [;;]</br>
            - Categorise companies by ending with double pipes [||]; if people are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - To differentiate identically-named people or companies, use a double hyphen followed by an integer between 1 and 99:- Stage Manager::Suzanne;;Bourke--2</br>
            Company Manager::Rupert;;Carlile,,Stage Manager::Suzanne;;Bourke,,Set Builders::Miraculous Engineering||,,Set Builders::Scott Fleary Scenery Ltd||Set Builder~~Ken;;Fleary//Set Builder~~Matthew;;Scott</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="ssn_nm" class="entry">
            <label for="ssn_nm" class="fixedwidth">SEASON: <?php echo error_for('ssn_nm') ?></label>
            <input type="text" name="ssn_nm" id="ssn_nm" maxlength="255" value="<?php echo $ssn_nm; ?>" class="entryfield <?php echo errorfield('ssn_nm') ?>"/>
            <h6>i.e. David Hare Season (Sheffield Theatres)</br>
            NB: Seasons comprise productions produced by the same producer/co-producers.</h6>
          </div>

          <div id="fstvl_nm" class="entry">
            <label for="fstvl_nm" class="fixedwidth">FESTIVAL: <?php echo error_for('fstvl_nm') ?></label>
            <input type="text" name="fstvl_nm" id="fstvl_nm" maxlength="255" value="<?php echo $fstvl_nm; ?>" class="entryfield <?php echo errorfield('fstvl_nm') ?>"/>
            <h6>i.e. RSC Complete Works Festival / Edinburgh Fringe Festival 2012, etc.</br>
            NB: Festivals comprise productions produced by an assortment of producers/co-producers.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="crs_list" class="entry">
            <label for="crs_list" class="fixedwidth">COURSE: <?php echo error_for('crs_tr_lg_chckd') ?><?php echo error_for('crs_prd_clss_chckd') ?><?php echo error_for('crs_nm_array_excss') ?><?php echo error_for('crs_empty') ?><?php echo error_for('crs_hsh_excss') ?><?php echo error_for('crs_hyphn_excss') ?><?php echo error_for('crs_sffx') ?><?php echo error_for('crs_hyphn') ?><?php echo error_for('crs_dt') ?><?php echo error_for('crs_dt_frmt') ?><?php echo error_for('crs_cln_excss') ?><?php echo error_for('crs_schl_hyphn_excss') ?><?php echo error_for('crs_schl_sffx') ?><?php echo error_for('crs_schl_hyphn') ?><?php echo error_for('crs_schl_excss_lngth') ?><?php echo error_for('crs_schl_nm') ?><?php echo error_for('crs_typ_excss_lngth') ?><?php echo error_for('crs_typ_nm') ?><?php echo error_for('crs_dplct') ?><?php echo error_for('crs_cln') ?><?php echo error_for('crs_hsh') ?></label>
            <input type="text" name="crs_list" id="crs_list" value="<?php echo $crs_list; ?>" class="entryfield <?php echo errorfield('crs_tr_lg_chckd') ?> <?php echo errorfield('crs_prd_clss_chckd') ?> <?php echo errorfield('crs_nm_array_excss') ?> <?php echo errorfield('crs_empty') ?> <?php echo errorfield('crs_hsh_excss') ?> <?php echo errorfield('crs_hyphn_excss') ?> <?php echo errorfield('crs_sffx') ?> <?php echo errorfield('crs_hyphn') ?> <?php echo errorfield('crs_dt') ?> <?php echo errorfield('crs_dt_frmt') ?> <?php echo errorfield('crs_cln_excss') ?> <?php echo errorfield('crs_schl_hyphn_excss') ?> <?php echo errorfield('crs_schl_sffx') ?> <?php echo errorfield('crs_schl_hyphn') ?> <?php echo errorfield('crs_schl_excss_lngth') ?> <?php echo errorfield('crs_schl_nm') ?> <?php echo errorfield('crs_typ_excss_lngth') ?> <?php echo errorfield('crs_typ_nm') ?> <?php echo errorfield('crs_dplct') ?> <?php echo errorfield('crs_cln') ?> <?php echo errorfield('crs_hsh') ?>"/>
            <h6>i.e. Royal Academy of Dramatic Art (RADA): 3 Year Acting (2006-09) / Guildhall School of Music and Drama (GSMD): 2 year Directing (2009-10), etc.</br>
            - Separate multiple entries using double comma [,,], course years using double hash [##] (and double semi-colon to establish course span between two years) and course type using double colon [::]:-</br>
            Guildhall School of Music and Drama (GSMD)::3 Year Acting##2006;;2009,,Guildhall School of Music and Drama (GSMD)::3 year Acting##2007;;2010</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rvw_list" class="entry">
            <label for="rvw_list" class="fixedwidth">REVIEWS: <?php echo error_for('rvw_list_array_excss') ?><?php echo error_for('rvw_empty') ?><?php echo error_for('rvw_cln_excss') ?><?php echo error_for('rvw_url_dplct') ?><?php echo error_for('rvw_url_excss_lngth') ?><?php echo error_for('rvw_url') ?><?php echo error_for('rvw_hsh_excss') ?><?php echo error_for('rvw_dt') ?><?php echo error_for('rvw_dt_frmt') ?><?php echo error_for('rvw_pipes_excss') ?><?php echo error_for('rvw_pub_hyphn_excss') ?><?php echo error_for('rvw_pub_sffx') ?><?php echo error_for('rvw_pub_hyphn') ?><?php echo error_for('rvw_pub_excss_lngth') ?><?php echo error_for('rvw_pub_url') ?><?php echo error_for('rvw_crtc_hyphn_excss') ?><?php echo error_for('rvw_crtc_sffx') ?><?php echo error_for('rvw_crtc_hyphn') ?><?php echo error_for('rvw_crtc_smcln_excss') ?><?php echo error_for('rvw_crtc_fll_nm_excss_lngth') ?><?php echo error_for('rvw_crtc_smcln') ?><?php echo error_for('rvw_crtc_nm') ?><?php echo error_for('rvw_crtc_url') ?><?php echo error_for('rvw_pipes') ?><?php echo error_for('rvw_hsh') ?><?php echo error_for('rvw_cln') ?></label>
            <input type="text" name="rvw_list" id="rvw_list" value="<?php echo $rvw_list; ?>" class="entryfield <?php echo errorfield('rvw_list_array_excss') ?> <?php echo errorfield('rvw_empty') ?> <?php echo errorfield('rvw_cln_excss') ?> <?php echo errorfield('rvw_url_dplct') ?> <?php echo errorfield('rvw_url_excss_lngth') ?> <?php echo errorfield('rvw_url') ?> <?php echo errorfield('rvw_hsh_excss') ?> <?php echo errorfield('rvw_dt') ?> <?php echo errorfield('rvw_dt_frmt') ?> <?php echo errorfield('rvw_pipes_excss') ?> <?php echo errorfield('rvw_pub_hyphn_excss') ?> <?php echo errorfield('rvw_pub_sffx') ?> <?php echo errorfield('rvw_pub_hyphn') ?> <?php echo errorfield('rvw_pub_excss_lngth') ?> <?php echo errorfield('rvw_pub_url') ?> <?php echo errorfield('rvw_crtc_hyphn_excss') ?> <?php echo errorfield('rvw_crtc_sffx') ?> <?php echo errorfield('rvw_crtc_hyphn') ?> <?php echo errorfield('rvw_crtc_smcln_excss') ?> <?php echo errorfield('rvw_crtc_fll_nm_excss_lngth') ?> <?php echo errorfield('rvw_crtc_smcln') ?> <?php echo errorfield('rvw_crtc_nm') ?> <?php echo errorfield('rvw_crtc_url') ?> <?php echo errorfield('rvw_pipes') ?> <?php echo errorfield('rvw_hsh') ?> <?php echo errorfield('rvw_cln') ?>"/>
            <h6>- Separate multiple entries using double comma [,,]; entries to adhere to below format:-</br>
            - To differentiate identically-named people or companies, use a double hyphen followed by an integer between 1 and 99:- Matt;;Trueman--2</br>
            Publication::Critic Given Name;;Critic Family Name(--Optional Suffix (1-99))##Review Date (format: [DD]-[MM]-[YYYY])::URL</br>
            i.e. The Guardian||Michael;;Billington##20-06-2014::http://www.theguardian.com/stage/2014/jun/20/adler-and-gibb-review-royal-court-theatre-london,,The Telegraph||Dominic;;Cavendish##20-06-2014::http://www.telegraph.co.uk/culture/theatre/theatre-reviews/10914097/Adler-and-Gibb-Royal-Court-Theatre-Downstairs-review-affectless.html,,matttrueman.co.uk||Matt;;Trueman##25-06-2014::http://matttrueman.co.uk/2014/06/review-adler-gibb-royal-court.html</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="alt_nm_list" class="entry">
            <label for="alt_nm_list" class="fixedwidth">PRODUCTION ALTERNATE NAME(S): <?php echo error_for('alt_nm_tr_lg_chckd') ?><?php echo error_for('alt_nm_array_excss') ?><?php echo error_for('alt_nm_empty') ?><?php echo error_for('alt_nm_cln_excss') ?><?php echo error_for('alt_nm_dscr_excss_lngth') ?><?php echo error_for('alt_nm_cln') ?><?php echo error_for('alt_nm_dplct') ?><?php echo error_for('alt_nm_excss_lngth') ?></label>
            <input type="text" name="alt_nm_list" id="alt_nm_list" value="<?php echo $alt_nm_list; ?>" class="entryfield <?php echo errorfield('alt_nm_tr_lg_chckd') ?> <?php echo errorfield('alt_nm_array_excss') ?> <?php echo errorfield('alt_nm_empty') ?> <?php echo errorfield('alt_nm_cln_excss') ?> <?php echo errorfield('alt_nm_dscr_excss_lngth') ?> <?php echo errorfield('alt_nm_cln') ?> <?php echo errorfield('alt_nm_dplct') ?> <?php echo errorfield('alt_nm_excss_lngth') ?>"/>
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
          <?php if(!$edit) { ?>
          <input type="submit" name="add" value="Submit" class="button"/>
          <?php } else { ?>
          <input type="hidden" name="prd_id" value="<?php echo $prd_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
          <?php } ?>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>