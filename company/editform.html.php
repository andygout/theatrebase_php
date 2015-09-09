<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (company) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>COMPANY:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing company.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('comp_edit_error') ?>
      <?php echo error_for('comp_excss_lngth') ?>
      <?php echo error_for('comp_url') ?>
      <?php echo error_for('comp_dlt') ?>
      </div>

      <form action="" method="post">
        <fieldset>
          <div id="comp_nm" class="entry">
            <label for="comp_nm" class="fixedwidth">* COMPANY:   <?php echo error_for('comp_nm') ?></label>
            <input type="text" name="comp_nm" id="comp_nm" maxlength="255" value="<?php echo $comp_nm; ?>" class="entryfield <?php echo errorfield('comp_nm') ?> <?php echo errorfield('comp_excss_lngth') ?> <?php echo errorfield('comp_url') ?>"/>
            <h6>i.e. Donmar Warehouse Projects Ltd.</h6>
          </div>

          <div id="comp_sffx_num" class="entry">
            <label for="comp_sffx_num" class="fixedwidth">SUFFIX [1-99]:   <?php echo error_for('comp_sffx') ?></label>
            <input type="text" name="comp_sffx_num" id="comp_sffx_num" maxlength="2" value="<?php echo $comp_sffx_num; ?>" class="entryfield2chars <?php echo errorfield('comp_sffx') ?> <?php echo errorfield('comp_excss_lngth') ?> <?php echo errorfield('comp_url') ?>"/>
            <h6>To differentiate theatres with the same name, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 99 with no leading 0s).</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="comp_adrs_list" class="entry">
            <label for="comp_adrs_list" class="fixedwidth">COMPANY ADDRESS(ES): <?php echo error_for('comp_adrs_array_excss') ?><?php echo error_for('comp_adrs_empty') ?><?php echo error_for('comp_adrs_cln_excss') ?><?php echo error_for('comp_adrs_ttl_excss_lngth') ?><?php echo error_for('comp_adrs_cln') ?><?php echo error_for('comp_adrs_excss_lngth') ?></label>
            <input type="text" name="comp_adrs_list" id="comp_adrs_list" value="<?php echo $comp_adrs_list; ?>" class="entryfield <?php echo errorfield('comp_adrs_array_excss') ?> <?php echo errorfield('comp_adrs_empty') ?> <?php echo errorfield('comp_adrs_cln_excss') ?> <?php echo errorfield('comp_adrs_ttl_excss_lngth') ?> <?php echo errorfield('comp_adrs_cln') ?> <?php echo errorfield('comp_adrs_excss_lngth') ?>"/>
            <h6>Enter company address(es). Use double commas [,,] to separate address lines (so address can be displayed with lines separated by commas or breaks as dictated).</br>
            - Separate multiple addresses using double slashes [//].</br>
            - For entries with more than one address, headers must be assigned for each address using double colon [::].</br>
            Royal Shakespeare Company: Stratford-upon-Avon::The Royal Shakespeare Theatre,,Waterside,,Stratford-upon-Avon,,Warwickshire,,CV37 6BB//London (I)::35 Clapham High Street,,London,,SW4 7TW//London (II)::1 Earlham Street,,Covent Garden,,London,,WC2H 9LL</br>
            Paines Plough: 43 Aldwych,,London,,WC2B 4DN<h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="comp_reg_nm" class="entry">
            <label for="comp_reg_nm" class="fixedwidth">REGISTERED NAME: <?php echo error_for('reg_nm_excss_lngth') ?></label>
            <input type="text" name="comp_reg_nm" id="comp_reg_nm" maxlength="255" value="<?php echo $comp_reg_nm; ?>" class="entryfield <?php echo errorfield('reg_nm_excss_lngth') ?>"/>
            <h6>Enter registered company name.</br>
            Royal Court Theatre: The English Stage Company Ltd. / Bush Theatre: The Alternative Theatre Company Ltd. / Sheffield Theatres: Sheffield Theatres Trust / Kneehigh: Kneehigh Theatre Trust Ltd.</h6>
          </div>

          <div id="comp_reg_adrs" class="entry">
            <label for="comp_reg_adrs" class="fixedwidth">REGISTERED ADDRESS: <?php echo error_for('reg_adrs_excss_lngth') ?></label>
            <input type="text" name="comp_reg_adrs" id="comp_reg_adrs" maxlength="255" value="<?php echo $comp_reg_adrs; ?>" class="entryfield <?php echo errorfield('reg_adrs_excss_lngth') ?>"/>
            <h6>Enter registered company address. Use double commas [,,] to separate address lines (so address can be displayed with lines separated by commas or breaks as dictated).</br>
            Royal Court Theatre: Royal Court Theatre,,Sloane Square,,London,,SW1W 8AS / Bush Theatre: Bush Theatre,,7 Uxbridge Road,,London,,W12 8LJ / Sheffield Theatres: Crucible Theatre,,55 Norfolk Street,,Sheffield,,S1 1DA / Kneehigh Theatre: 15 Walsingham Place,,Truro,,Cornwall,,TR1 2RP</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="comp_est_dt" class="entry">
            <label for="comp_est_dt" class="fixedwidth">COMPANY ESTABLISHED DATE [DD]-[MM]-[YYYY]: <?php echo error_for('comp_est_dt') ?></label>
            <input type="date" name="comp_est_dt" id="comp_est_dt" maxlength="10" value="<?php echo $comp_est_dt; ?>" class="entryfielddate <?php echo errorfield('comp_est_dt') ?>"/>
            <h6>i.e. 28-05-1974</h6>
            <input type="radio" name="comp_est_dt_frmt" value="1" <?php if($comp_est_dt_frmt=='1' || !$comp_est_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="comp_est_dt_frmt" value="2" <?php if($comp_est_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="comp_est_dt_frmt" value="3" <?php if($comp_est_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="comp_est_dt_frmt" value="4" <?php if($comp_est_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="comp_dslv_dt" class="entry">
            <label for="comp_dslv_dt" class="fixedwidth">COMPANY DISSOLVED DATE [DD]-[MM]-[YYYY]: <?php echo error_for('comp_dslv_dt') ?><?php echo error_for('comp_dslv_dt_comp_dslv') ?></label>
            <input type="date" name="comp_dslv_dt" id="comp_dslv_dt" maxlength="10" value="<?php echo $comp_dslv_dt; ?>" class="entryfielddate <?php echo errorfield('comp_dslv_dt') ?> <?php echo errorfield('comp_dslv_dt_comp_dslv') ?>"/>
            <h6>i.e. 05-01-2013</h6>
            <input type="radio" name="comp_dslv_dt_frmt" value="1" <?php if($comp_dslv_dt_frmt=='1' || !$comp_dslv_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="comp_dslv_dt_frmt" value="2" <?php if($comp_dslv_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="comp_dslv_dt_frmt" value="3" <?php if($comp_dslv_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="comp_dslv_dt_frmt" value="4" <?php if($comp_dslv_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="comp_dslv" class="entry">
            <label for="comp_dslv" class="fixedwidth">COMPANY DISSOLVED: <?php echo error_for('comp_dslv_dt_comp_dslv') ?></label>
            <input type="checkbox" name="comp_dslv" id="comp_dslv"<?php if($comp_dslv) {echo ' checked="checked"';} ?>/>
            <h6>Check box if company is now closed or inactive.</br></h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="comp_nm_frm_dt" class="entry">
            <label for="comp_nm_frm_dt" class="fixedwidth">COMPANY NAME BEGINS [DD]-[MM]-[YYYY]: <?php echo error_for('comp_nm_frm_dt') ?><?php echo error_for('comp_tr_ov_nm_frm_dt') ?><?php echo error_for('comp_est_nm_frm_dt_mtch') ?></label>
            <input type="date" name="comp_nm_frm_dt" id="comp_nm_frm_dt" maxlength="10" value="<?php echo $comp_nm_frm_dt; ?>" class="entryfielddate <?php echo errorfield('comp_nm_frm_dt') ?> <?php echo errorfield('comp_tr_ov_nm_frm_dt') ?> <?php echo errorfield('comp_est_nm_frm_dt_mtch') ?>"/>
            <h6>i.e. 13-06-2006</h6>
            <input type="radio" name="comp_nm_frm_dt_frmt" value="1" <?php if($comp_nm_frm_dt_frmt=='1' || !$comp_nm_frm_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="comp_nm_frm_dt_frmt" value="2" <?php if($comp_nm_frm_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="comp_nm_frm_dt_frmt" value="3" <?php if($comp_nm_frm_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="comp_nm_frm_dt_frmt" value="4" <?php if($comp_nm_frm_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="comp_nm_exp_dt" class="entry">
            <label for="comp_nm_exp_dt" class="fixedwidth">COMPANY NAME EXPIRES [DD]-[MM]-[YYYY]: <?php echo error_for('comp_nm_exp_dt') ?><?php echo error_for('comp_nm_exp_dt_nm_exp') ?><?php echo error_for('comp_tr_ov_nm_exp_dt') ?><?php echo error_for('comp_dslv_nm_exp_dt_mtch') ?></label>
            <input type="date" name="comp_nm_exp_dt" id="comp_nm_exp_dt" maxlength="10" value="<?php echo $comp_nm_exp_dt; ?>" class="entryfielddate <?php echo errorfield('comp_nm_exp_dt') ?> <?php echo errorfield('comp_nm_exp_dt_nm_exp') ?> <?php echo errorfield('comp_tr_ov_nm_exp_dt') ?> <?php echo errorfield('comp_dslv_nm_exp_dt_mtch') ?>"/>
            <h6>i.e. 12-06-2006</h6>
            <input type="radio" name="comp_nm_exp_dt_frmt" value="1" <?php if($comp_nm_exp_dt_frmt=='1' || !$comp_nm_exp_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="comp_nm_exp_dt_frmt" value="2" <?php if($comp_nm_frm_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="comp_nm_exp_dt_frmt" value="3" <?php if($comp_nm_frm_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="comp_nm_exp_dt_frmt" value="4" <?php if($comp_nm_frm_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="comp_nm_exp" class="entry">
            <label for="comp_nm_exp" class="fixedwidth">COMPANY NAME REPLACED: <?php echo error_for('comp_nm_exp_dt_nm_exp') ?></label>
            <input type="checkbox" name="comp_nm_exp" id="comp_nm_exp"<?php if($comp_nm_exp) {echo ' checked="checked"';} ?>/>
            <h6>Check box if theatre name has been replaced by another name.</br></h6>
          </div>

          <div id="sbsq_comp_list" class="entry">
            <label for="sbsq_comp_list" class="fixedwidth">SUBSEQUENTLY KNOWN AS: <?php echo error_for('sbsq_comp_nm_exp_unchckd') ?><?php echo error_for('sbsq_comp_nm_array_excss') ?><?php echo error_for('sbsq_comp_empty') ?><?php echo error_for('sbsq_comp_hyphn_excss') ?><?php echo error_for('sbsq_comp_sffx') ?><?php echo error_for('sbsq_comp_hyphn') ?><?php echo error_for('sbsq_comp_dplct') ?><?php echo error_for('sbsq_comp_nm_excss_lngth') ?><?php echo error_for('sbsq_comp_url') ?><?php echo error_for('sbsq_comp_id_mtch') ?><?php echo error_for('sbsq_comp_inv_comb') ?><?php echo error_for('sbsq_comp_est_dt_mtch') ?><?php echo error_for('sbsq_comp_dslv_dt_mtch') ?><?php echo error_for('sbsq_comp_nm_dt_mtch') ?></label>
            <input type="text" name="sbsq_comp_list" id="sbsq_comp_list" value="<?php echo $sbsq_comp_list; ?>" class="entryfield <?php echo errorfield('sbsq_comp_nm_exp_unchckd') ?> <?php echo errorfield('sbsq_comp_nm_array_excss') ?> <?php echo errorfield('sbsq_comp_empty') ?> <?php echo errorfield('sbsq_comp_hyphn_excss') ?> <?php echo errorfield('sbsq_comp_sffx') ?> <?php echo errorfield('sbsq_comp_hyphn') ?> <?php echo errorfield('sbsq_comp_dplct') ?> <?php echo errorfield('sbsq_comp_nm_excss_lngth') ?> <?php echo errorfield('sbsq_comp_url') ?> <?php echo errorfield('sbsq_comp_id_mtch') ?> <?php echo errorfield('sbsq_comp_inv_comb') ?> <?php echo errorfield('sbsq_comp_est_dt_mtch') ?> <?php echo errorfield('sbsq_comp_dslv_dt_mtch') ?> <?php echo errorfield('sbsq_comp_nm_dt_mtch') ?>"/>
            <h6>Enter name of theatre by which this company was subsequently known.</br>
            - Separate multiple entries using double comma [,,].</br>
            - To differentiate identically-named companies, use a double hyphen followed by an integer between 1 and 99:- Headlong Theatre--2</br>
            Anvil Productions: Headlong Theatre,,Oxford Stage Company</h6>
          </div>

          <div id="prvs_comp_list" class="entry">
            <label for="prvs_comp_list" class="fixedwidth">PREVIOUSLY KNOWN AS: <?php echo error_for('prvs_comp_nm_array_excss') ?><?php echo error_for('prvs_comp_empty') ?><?php echo error_for('prvs_comp_hyphn_excss') ?><?php echo error_for('prvs_comp_sffx') ?><?php echo error_for('prvs_comp_hyphn') ?><?php echo error_for('prvs_comp_dplct') ?><?php echo error_for('prvs_comp_nm_excss_lngth') ?><?php echo error_for('prvs_comp_url') ?><?php echo error_for('prvs_comp_id_mtch') ?><?php echo error_for('prvs_comp_inv_comb') ?><?php echo error_for('prvs_comp_est_dt_mtch') ?><?php echo error_for('prvs_comp_dslv_dt_mtch') ?><?php echo error_for('prvs_comp_nm_dt_mtch') ?><?php echo error_for('prvs_comp_nm_exp') ?></label>
            <input type="text" name="prvs_comp_list" id="prvs_comp_list" value="<?php echo $prvs_comp_list; ?>" class="entryfield <?php echo errorfield('prvs_comp_nm_array_excss') ?> <?php echo errorfield('prvs_comp_empty') ?> <?php echo errorfield('prvs_comp_hyphn_excss') ?> <?php echo errorfield('prvs_comp_sffx') ?> <?php echo errorfield('prvs_comp_hyphn') ?> <?php echo errorfield('prvs_comp_dplct') ?> <?php echo errorfield('prvs_comp_nm_excss_lngth') ?> <?php echo errorfield('prvs_comp_url') ?> <?php echo errorfield('prvs_comp_id_mtch') ?> <?php echo errorfield('prvs_comp_inv_comb') ?> <?php echo errorfield('prvs_comp_est_dt_mtch') ?> <?php echo errorfield('prvs_comp_dslv_dt_mtch') ?> <?php echo errorfield('prvs_comp_nm_dt_mtch') ?> <?php echo errorfield('prvs_comp_nm_exp') ?>"/>
            <h6>Enter name of theatre by which this theatre was previously known.</br>
            - Separate multiple entries using double comma [,,].</br>
            - To differentiate identically-named companies, use a double hyphen followed by an integer between 1 and 99:- Headlong Theatre--2</br>
            Headlong Theatre: Oxford Stage Company,,Anvil Productions</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="comp_lctn_list" class="entry">
            <label for="comp_lctn_list" class="fixedwidth">COMPANY LOCATION(S) (link): <?php echo error_for('comp_lctn_nm_array_excss') ?><?php echo error_for('comp_lctn_empty') ?><?php echo error_for('comp_lctn_pipe_excss') ?><?php echo error_for('comp_lctn_pipe') ?><?php echo error_for('comp_lctn_hyphn_excss') ?><?php echo error_for('comp_lctn_sffx') ?><?php echo error_for('comp_lctn_hyphn') ?><?php echo error_for('comp_lctn_dplct') ?><?php echo error_for('comp_lctn_nm_excss_lngth') ?><?php echo error_for('comp_lctn_nm') ?><?php echo error_for('comp_lctn_alt_list') ?><?php echo error_for('comp_lctn_alt_array_excss') ?><?php echo error_for('comp_lctn_alt_empty') ?><?php echo error_for('comp_lctn_alt_hyphn_excss') ?><?php echo error_for('comp_lctn_alt_sffx') ?><?php echo error_for('comp_lctn_alt_hyphn') ?><?php echo error_for('comp_lctn_alt_dplct') ?><?php echo error_for('comp_lctn_alt_excss_lngth') ?><?php echo error_for('comp_lctn_alt_url') ?><?php echo error_for('comp_lctn_alt') ?><?php echo error_for('comp_lctn_alt_assoc') ?></label>
            <input type="text" name="comp_lctn_list" id="comp_lctn_list" maxlength="255" value="<?php echo $comp_lctn_list; ?>" class="entryfield <?php echo errorfield('comp_lctn_nm_array_excss') ?> <?php echo errorfield('comp_lctn_empty') ?> <?php echo errorfield('comp_lctn_pipe_excss') ?> <?php echo errorfield('comp_lctn_pipe') ?> <?php echo errorfield('comp_lctn_hyphn_excss') ?> <?php echo errorfield('comp_lctn_sffx') ?> <?php echo errorfield('comp_lctn_hyphn') ?> <?php echo errorfield('comp_lctn_dplct') ?> <?php echo errorfield('comp_lctn_nm_excss_lngth') ?> <?php echo errorfield('comp_lctn_nm') ?> <?php echo errorfield('comp_lctn_alt_list') ?> <?php echo errorfield('comp_lctn_alt_array_excss') ?> <?php echo errorfield('comp_lctn_alt_empty') ?> <?php echo errorfield('comp_lctn_alt_hyphn_excss') ?> <?php echo errorfield('comp_lctn_alt_sffx') ?> <?php echo errorfield('comp_lctn_alt_hyphn') ?> <?php echo errorfield('comp_lctn_alt_dplct') ?> <?php echo errorfield('comp_lctn_alt_excss_lngth') ?> <?php echo errorfield('comp_lctn_alt_url') ?> <?php echo errorfield('comp_lctn_alt') ?> <?php echo errorfield('comp_lctn_alt_assoc') ?>"/>
            <h6>i.e. Stratford-upon-Avon / Ludlow / Ipswich / Sydney, etc.</br>
            Royal Shakespeare Company: Stratford-upon-Avon / Pentabus Theatre: Ludlow / Eastern Angles: Ipswich / Sydney Theatre Company: Sydney</br>
            - Separate multiple entries using double comma [,,] and only list smallest denomination (of location) given:-</br>
            - To differentiate identically-named locations, use a double hyphen followed by an integer between 1 and 99:- London--2 / Kingston--2 / Springfield--2</br>
            - When a location should be associated with specific locations (default will exclude those that are pre-existing and fictional; although fictional not an option for people), set list with double pipes [||] and separate multiple entries with double chevron [>>], i.e. Hagia Irene||Constantinople>>Turkey>>Europe / Moscow||USSR>>Europe</h6>
          </div>

          <div id="comp_typ_list" class="entry">
            <label for="comp_typ_list" class="fixedwidth">COMPANY TYPE: <?php echo error_for('comp_typ_nm_array_excss') ?><?php echo error_for('comp_typ_empty') ?><?php echo error_for('comp_typ_dplct') ?><?php echo error_for('comp_typ_nm_excss_lngth') ?><?php echo error_for('comp_typ_nm') ?></label>
            <input type="text" name="comp_typ_list" id="comp_typ_list" value="<?php echo $comp_typ_list; ?>" class="entryfield <?php echo errorfield('comp_typ_nm_array_excss') ?> <?php echo errorfield('comp_typ_empty') ?> <?php echo errorfield('comp_typ_dplct') ?> <?php echo errorfield('comp_typ_nm_excss_lngth') ?> <?php echo errorfield('comp_typ_nm') ?>"/>
            <h6>Enter the company type.</br>
            - Separate multiple entries using double comma [,,].</br>
            i.e. Subsidised Theatre / Repertory Theatre / Commercial Theatre Producers / Agency (talent) / Agency (literary) / Publication / MRSL Grade 1 / TNC, etc.</br>
            National Theatre Company: Subsidised Theatre,,TNC / Playful Productions: Commercial Theatre Producers / United Agents: Agency (talent),,Agency (literary) / Hull Truck Theatre Company: Subsidised Theatre,,MRSL Grade 2</h6>
          </div>

          <div id="comp_prsn_list" class="entry">
            <label for="comp_prsn_list" class="fixedwidth">COMPANY MEMBERS (people):   <?php echo error_for('comp_prsn_nm_rl_array_excss') ?><?php echo error_for('comp_prsn_empty') ?><?php echo error_for('comp_prsn_hsh_excss') ?><?php echo error_for('comp_prsn_hsh') ?><?php echo error_for('comp_prsn_yr') ?><?php echo error_for('comp_prsn_yr_frmt') ?><?php echo error_for('comp_prsn_cln_excss') ?><?php echo error_for('comp_prsn_rl_smcln_excss') ?><?php echo error_for('comp_prsn_rl_smcln') ?><?php echo error_for('comp_prsn_rl_nt_excss_lngth') ?><?php echo error_for('comp_prsn_rl_excss_lngth') ?><?php echo error_for('comp_prsn_cln') ?><?php echo error_for('comp_prsn_hyphn_excss') ?><?php echo error_for('comp_prsn_sffx') ?><?php echo error_for('comp_prsn_hyphn') ?><?php echo error_for('comp_prsn_smcln_excss') ?><?php echo error_for('comp_prsn_dplct') ?><?php echo error_for('comp_prsn_excss_lngth') ?><?php echo error_for('comp_prsn_smcln') ?><?php echo error_for('comp_prsn_nm') ?><?php echo error_for('comp_prsn_url') ?><?php echo error_for('rmvd_agnts') ?><?php echo error_for('rmvd_lcnsrs') ?></label>
            <input type="text" name="comp_prsn_list" id="comp_prsn_list" value="<?php echo $comp_prsn_list; ?>" class="entryfield <?php echo errorfield('comp_prsn_nm_rl_array_excss') ?> <?php echo errorfield('comp_prsn_empty') ?> <?php echo errorfield('comp_prsn_hsh_excss') ?> <?php echo errorfield('comp_prsn_hsh') ?> <?php echo errorfield('comp_prsn_yr') ?> <?php echo errorfield('comp_prsn_yr_frmt') ?> <?php echo errorfield('comp_prsn_cln_excss') ?> <?php echo errorfield('comp_prsn_rl_smcln_excss') ?> <?php echo errorfield('comp_prsn_rl_smcln') ?> <?php echo errorfield('comp_prsn_rl_nt_excss_lngth') ?> <?php echo errorfield('comp_prsn_rl_excss_lngth') ?> <?php echo errorfield('comp_prsn_cln') ?> <?php echo errorfield('comp_prsn_hyphn_excss') ?> <?php echo errorfield('comp_prsn_sffx') ?> <?php echo errorfield('comp_prsn_hyphn') ?> <?php echo errorfield('comp_prsn_smcln_excss') ?> <?php echo errorfield('comp_prsn_dplct') ?> <?php echo errorfield('comp_prsn_excss_lngth') ?> <?php echo errorfield('comp_prsn_smcln') ?> <?php echo errorfield('comp_prsn_nm') ?> <?php echo errorfield('comp_prsn_url') ?> <?php echo errorfield('rmvd_agnts') ?> <?php echo errorfield('rmvd_lcnsrs') ?>"/>
            <h6>i.e. Artistic Director, Agent - Literary, etc.</br>
            - Separate multiple entries using double comma [,,], role duration (optional) using double hash [##], role note (optional) using double semicolon [;;], roles using double colon [::], and first name and surname using double semi-colon [;;]:-</br>
            Nicholas;;Hytner::Artistic Director;;To step down in March 2015##2003;;2015,,Howard;;Davies::Associate Director,,Nick;;Starr::Executive Director##2002</h6>
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
          <input type="hidden" name="comp_id" value="<?php echo $comp_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>