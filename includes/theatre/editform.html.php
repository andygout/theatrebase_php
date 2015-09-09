<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (<?php echo $pagedscr; ?>) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4><?php echo $pagehdr ?></h4>
      <h1><?php echo $thtr_nm_dsply.$sbthtr_nm_dsply; ?></h1>
      <h2><?php echo $thtr_lctn_dsply; ?></h2>
      <h3><p>Edit this existing <?php echo $pagedscr; ?>.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('thtr_edit_error') ?>
      <?php echo error_for('thtr_excss_lngth') ?>
      <?php echo error_for('thtr_url') ?>
      <?php echo error_for('thtr_dlt') ?>
      </div>
      <form action="" method="post">
        <fieldset>
          <div id="thtr_nm" class="entry">
            <label for="thtr_nm" class="fixedwidth">* THEATRE NAME / TOUR TYPE: <?php echo error_for('thtr_nm') ?><?php echo error_for('sbthtr_assoc_thtr_nm_mtch') ?></label>
            <input type="text" name="thtr_nm" id="thtr_nm" maxlength="255" value="<?php echo $thtr_nm; ?>" class="entryfield <?php echo errorfield('thtr_nm') ?> <?php echo errorfield('sbthtr_assoc_thtr_nm_mtch') ?> <?php echo errorfield('thtr_excss_lngth') ?> <?php echo errorfield('thtr_url') ?>"/>
            <h6>i.e. National Theatre / UK Tour (if tour type)</h6>
          </div>

          <div id="sbthtr_nm" class="entry">
            <label for="sbthtr_nm" class="fixedwidth">SUB-THEATRE NAME: <?php echo error_for('sbthtr_nm') ?><?php echo error_for('thtr_tr_ov_sbthtr_nm') ?><?php echo error_for('sbthtr_list_sbthtr_nm') ?><?php echo error_for('sbthtr_assoc_sbthtr_nm_empty') ?></label>
            <input type="text" name="sbthtr_nm" id="sbthtr_nm" maxlength="255" value="<?php echo $sbthtr_nm; ?>" class="entryfield <?php echo errorfield('sbthtr_nm') ?> <?php echo errorfield('thtr_tr_ov_sbthtr_nm') ?> <?php echo errorfield('sbthtr_list_sbthtr_nm') ?> <?php echo errorfield('sbthtr_assoc_sbthtr_nm_empty') ?> <?php echo errorfield('thtr_excss_lngth') ?> <?php echo errorfield('thtr_url') ?>"/>
            <h6>i.e. Olivier Theatre</h6>
          </div>

          <div id="thtr_lctn" class="entry">
            <label for="thtr_lctn" class="fixedwidth">THEATRE LOCATION: <?php echo error_for('thtr_lctn_nm') ?><?php echo error_for('thtr_tr_ov_thtr_lctn_nm') ?><?php echo error_for('sbthtr_assoc_thtr_lctn_mtch') ?></label>
            <input type="text" name="thtr_lctn" id="thtr_lctn" maxlength="255" value="<?php echo $thtr_lctn; ?>" class="entryfield <?php echo errorfield('thtr_lctn_nm') ?> <?php echo errorfield('thtr_tr_ov_thtr_lctn_nm') ?> <?php echo errorfield('sbthtr_assoc_thtr_lctn_mtch') ?> <?php echo errorfield('thtr_excss_lngth') ?> <?php echo errorfield('thtr_url') ?>"/>
            <h6>i.e. South Bank, London</h6>
          </div>

          <div id="thtr_sffx_num" class="entry">
            <label for="thtr_sffx_num" class="fixedwidth">SUFFIX [1-99]: <?php echo error_for('thtr_sffx') ?></label>
            <input type="text" name="thtr_sffx_num" id="thtr_sffx_num" maxlength="2" value="<?php echo $thtr_sffx_num; ?>" class="entryfield2chars <?php echo errorfield('thtr_sffx') ?> <?php echo errorfield('thtr_excss_lngth') ?> <?php echo errorfield('thtr_url') ?>"/>
            <h6>To differentiate theatres with the same name, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 99 with no leading 0s).</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="thtr_adrs" class="entry">
            <label for="thtr_adrs" class="fixedwidth">ADDRESS: <?php echo error_for('adrs_excss_lngth') ?><?php echo error_for('thtr_tr_ov_adrs') ?><?php echo error_for('sbthtr_assoc_thtr_adrs') ?></label>
            <input type="text" name="thtr_adrs" id="thtr_adrs" value="<?php echo $thtr_adrs; ?>" class="entryfield <?php echo errorfield('adrs_excss_lngth') ?> <?php echo errorfield('thtr_tr_ov_adrs') ?> <?php echo errorfield('sbthtr_assoc_thtr_adrs') ?>"/>
            <h6>Enter theatre address. Use double commas [,,] to separate address lines (so address can be displayed with lines separated by commas or breaks as dictated).</br>
            National Theatre: Upper Ground,,South Bank,,London,,SE1 9PX</br>
            Royal Court Theatre: Sloane Square,,London,,SW1W 8AS</h6>
          </div>

          <div id="lctn_lnk_nm" class="entry">
            <label for="lctn_lnk_nm" class="fixedwidth">THEATRE LOCATION (link): <?php echo error_for('lctn_lnk_pipe_excss') ?><?php echo error_for('lctn_lnk_pipe') ?><?php echo error_for('lctn_lnk_hyphn_excss') ?><?php echo error_for('lctn_lnk_sffx') ?><?php echo error_for('lctn_lnk_hyphn') ?><?php echo error_for('lctn_lnk_nm_excss_lngth') ?><?php echo error_for('lctn_lnk_nm') ?><?php echo error_for('lctn_lnk_alt_list') ?><?php echo error_for('lctn_lnk_alt_array_excss') ?><?php echo error_for('lctn_lnk_alt_empty') ?><?php echo error_for('lctn_lnk_alt_hyphn_excss') ?><?php echo error_for('lctn_lnk_alt_sffx') ?><?php echo error_for('lctn_lnk_alt_hyphn') ?><?php echo error_for('lctn_lnk_alt_dplct') ?><?php echo error_for('lctn_lnk_alt_excss_lngth') ?><?php echo error_for('lctn_lnk_alt_url') ?><?php echo error_for('lctn_lnk_alt') ?><?php echo error_for('lctn_lnk_alt_fctn') ?><?php echo error_for('lctn_lnk_alt_assoc') ?><?php echo error_for('thtr_tr_ov_lctn_lnk') ?><?php echo error_for('sbthtr_assoc_thtr_lctn_lnk') ?></label>
            <input type="text" name="lctn_lnk_nm" id="lctn_lnk_nm" value="<?php echo $lctn_lnk_nm; ?>" class="entryfield <?php echo errorfield('lctn_lnk_pipe_excss') ?> <?php echo errorfield('lctn_lnk_pipe') ?> <?php echo errorfield('lctn_lnk_hyphn_excss') ?> <?php echo errorfield('lctn_lnk_sffx') ?> <?php echo errorfield('lctn_lnk_hyphn') ?> <?php echo errorfield('lctn_lnk_nm_excss_lngth') ?> <?php echo errorfield('lctn_lnk_nm') ?> <?php echo errorfield('lctn_lnk_alt_list') ?> <?php echo errorfield('lctn_lnk_alt_array_excss') ?> <?php echo errorfield('lctn_lnk_alt_empty') ?> <?php echo errorfield('lctn_lnk_alt_hyphn_excss') ?> <?php echo errorfield('lctn_lnk_alt_sffx') ?> <?php echo errorfield('lctn_lnk_alt_hyphn') ?> <?php echo errorfield('lctn_lnk_alt_dplct') ?> <?php echo errorfield('lctn_lnk_alt_excss_lngth') ?> <?php echo errorfield('lctn_lnk_alt_url') ?> <?php echo errorfield('lctn_lnk_alt') ?> <?php echo errorfield('lctn_lnk_alt_fctn') ?> <?php echo errorfield('lctn_lnk_alt_assoc') ?> <?php echo errorfield('thtr_tr_ov_lctn_lnk') ?> <?php echo errorfield('sbthtr_assoc_thtr_lctn_lnk') ?>"/>
            <h6>i.e. Stratford-upon-Avon / Notting Hill / Shaftesbury Avenue / Dalston, etc.</br>
            - Only list smallest denomination (of location) given:-</br>
            Royal Shakespeare Theatre (Stratford-upon-Avon): Stratford-upon-Avon / Gate Theatre (Notting Hill): Notting Hill / Apollo Theatre (West End, London): Shaftesbury Avenue / Arcola Theatre (Dalston, London): Dalston</br>
            - To differentiate identically-named locations, use a double hyphen followed by an integer between 1 and 99:- London--2 / Kingston--2 / Springfield--2</br>
            - When a location should be associated with specific locations (default will exclude those that are pre-existing and fictional; although fictional not an option for people), set list with double pipes [||] and separate multiple entries with double chevron [>>], i.e. Hagia Irene||Constantinople>>Turkey>>Europe / Moscow||USSR>>Europe</h6>
          </div>

          <div id="thtr_typ_list" class="entry">
            <label for="thtr_typ_list" class="fixedwidth">THEATRE TYPE: <?php echo error_for('thtr_typ_nm_array_excss') ?><?php echo error_for('thtr_typ_empty') ?><?php echo error_for('thtr_typ_dplct') ?><?php echo error_for('thtr_typ_nm_excss_lngth') ?><?php echo error_for('thtr_typ_nm') ?></label>
            <input type="text" name="thtr_typ_list" id="thtr_typ_list" value="<?php echo $thtr_typ_list; ?>" class="entryfield <?php echo errorfield('thtr_typ_nm_array_excss') ?> <?php echo errorfield('thtr_typ_empty') ?> <?php echo errorfield('thtr_typ_dplct') ?> <?php echo errorfield('thtr_typ_nm_excss_lngth') ?> <?php echo errorfield('thtr_typ_nm') ?>"/>
            <h6>Enter the theatre type.</br>
            - Separate multiple entries using double comma [,,].</br>
            i.e. West End (SOLT defined) / Off-West End / Fringe / Regional / Studio, etc.</br>
            Gielgud Theatre (West End, London): West End / Etcetera Theatre (Camden, London): Fringe,,Studio / Nottingham Playhouse: Regional / Jermyn Street Theatre (London): Off-West End,,Studio</h6>
          </div>

          <div id="thtr_comp_list" class="entry">
            <label for="thtr_comp_list" class="fixedwidth">THEATRE OWNED BY: <?php echo error_for('thtr_comp_thtr_clsd') ?><?php echo error_for('thtr_comp_array_excss') ?><?php echo error_for('thtr_comp_empty') ?><?php echo error_for('thtr_comp_hyphn_excss') ?><?php echo error_for('thtr_comp_sffx') ?><?php echo error_for('thtr_comp_hyphn') ?><?php echo error_for('thtr_comp_dplct') ?><?php echo error_for('thtr_comp_nm_excss_lngth') ?><?php echo error_for('thtr_comp_url') ?><?php echo error_for('thtr_tr_ov_comp') ?><?php echo error_for('sbthtr_assoc_thtr_comp') ?></label>
            <input type="text" name="thtr_comp_list" id="thtr_comp_list" value="<?php echo $thtr_comp_list; ?>" class="entryfield <?php echo errorfield('thtr_comp_thtr_clsd') ?> <?php echo errorfield('thtr_comp_array_excss') ?> <?php echo errorfield('thtr_comp_empty') ?> <?php echo errorfield('thtr_comp_hyphn_excss') ?> <?php echo errorfield('thtr_comp_sffx') ?> <?php echo errorfield('thtr_comp_hyphn') ?> <?php echo errorfield('thtr_comp_dplct') ?> <?php echo errorfield('thtr_comp_nm_excss_lngth') ?> <?php echo errorfield('thtr_comp_url') ?> <?php echo errorfield('thtr_tr_ov_comp') ?> <?php echo errorfield('sbthtr_assoc_thtr_comp') ?>"/>
            <h6>i.e. Nimax Theatres, Delfont Mackintosh Theatres, National Theatre Company, The Alternative Theatre Company, etc.</br>
            - To differentiate identically-named companies, use a double hyphen followed by an integer between 1 and 99:- Nimax Theatres--2</br>
            Duchess Theatre (West End, London): Nimax Theatres / Tragalgar Studios (West End, London): Delfont Mackintosh Theatre / National Theatre (South Bank, London): National Theatre Company / Bush Theatre (Shepherds Bush, London): The Alternative Theatre Company</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="sbthtr_list" class="entry">
            <label for="sbthtr_list" class="fixedwidth">SUBTHEATRES: <?php echo error_for('sbthtr_list_sbthtr_nm') ?><?php echo error_for('sbthtr_array_excss') ?><?php echo error_for('sbthtr_empty') ?><?php echo error_for('sbthtr_hyphn_excss') ?><?php echo error_for('sbthtr_sffx') ?><?php echo error_for('sbthtr_hyphn') ?><?php echo error_for('sbthtr_cln_excss') ?><?php echo error_for('sbthtr_cln') ?><?php echo error_for('sbthtr_thtr_lctn_mtch') ?><?php echo error_for('sbthtr_smcln_excss') ?><?php echo error_for('sbthtr_thtr_nm_mtch') ?><?php echo error_for('sbthtr_smcln') ?><?php echo error_for('sbthtr_excss_lngth') ?><?php echo error_for('sbthtr_dplct') ?><?php echo error_for('sbthtr_cmpstn') ?><?php echo error_for('sbthtr_url') ?><?php echo error_for('sbthtr_dt_mtch') ?><?php echo error_for('sbthtr_clsd_mtch') ?><?php echo error_for('sbthtr_assocs') ?><?php echo error_for('thtr_tr_ov_sbthtr_list') ?></label>
            <input type="text" name="sbthtr_list" id="sbthtr_list" value="<?php echo $sbthtr_list; ?>" class="entryfield <?php echo errorfield('sbthtr_list_sbthtr_nm') ?> <?php echo errorfield('sbthtr_array_excss') ?> <?php echo errorfield('sbthtr_empty') ?> <?php echo errorfield('sbthtr_hyphn_excss') ?> <?php echo errorfield('sbthtr_sffx') ?> <?php echo errorfield('sbthtr_hyphn') ?> <?php echo errorfield('sbthtr_cln_excss') ?> <?php echo errorfield('sbthtr_cln') ?> <?php echo errorfield('sbthtr_thtr_lctn_mtch') ?> <?php echo errorfield('sbthtr_smcln_excss') ?> <?php echo errorfield('sbthtr_thtr_nm_mtch') ?> <?php echo errorfield('sbthtr_smcln') ?> <?php echo errorfield('sbthtr_excss_lngth') ?> <?php echo errorfield('sbthtr_dplct') ?> <?php echo errorfield('sbthtr_cmpstn') ?> <?php echo errorfield('sbthtr_url') ?> <?php echo errorfield('sbthtr_dt_mtch') ?> <?php echo errorfield('sbthtr_clsd_mtch') ?> <?php echo errorfield('sbthtr_assocs') ?> <?php echo errorfield('thtr_tr_ov_sbthtr_list') ?>"/>
            <h6>Enter sub-theatres which comprise this theatre.</br>
            - Separate multiple entries using double comma [,,], location using double colon [::] (optional), and theatre and sub-theatre using [;;] (mandatory).</br>
            i.e. For National Theatre (South Bank, London):-</br>
            National Theatre;;Olivier Theatre::South Bank, London,,National Theatre;;Lyttelton Theatre::South Bank, London,,National Theatre;;Cottesloe Theatre::South Bank, London</br>
            -To differentiate identically-named theatres, use a double hyphen followed by an integer between 1 and 99:-</br>
            Bush Theatre (Shepherd's Bush, London)--2</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="thtr_cpcty" class="entry">
            <label for="thtr_cpcty" class="fixedwidth">THEATRE CAPACITY [0000-9999]: <?php echo error_for('thtr_cpcty') ?><?php echo error_for('sbthtr_list_thtr_cpcty') ?><?php echo error_for('thtr_tr_ov_thtr_cpcty') ?></label>
            <input type="text" name="thtr_cpcty" id="thtr_cpcty" maxlength="4" value="<?php echo $thtr_cpcty; ?>" class="entryfield4chars <?php echo errorfield('thtr_cpcty') ?> <?php echo errorfield('sbthtr_list_thtr_cpcty') ?> <?php echo errorfield('thtr_tr_ov_thtr_cpcty') ?>"/>
            <h6>Enter maximum seating capacity of the venue.</br>
            i.e. NT Olivier: 1160, NT Lyttelton: 890, NT Cottesloe: 400</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="thtr_opn_dt" class="entry">
            <label for="thtr_opn_dt" class="fixedwidth">THEATRE OPENING DATE [DD]-[MM]-[YYYY]: <?php echo error_for('thtr_opn_dt') ?><?php echo error_for('thtr_tr_ov_opn_dt') ?><?php echo error_for('sbthtr_assoc_thtr_opn_dt_mtch') ?></label>
            <input type="date" name="thtr_opn_dt" id="thtr_opn_dt" maxlength="10" value="<?php echo $thtr_opn_dt; ?>" class="entryfielddate <?php echo errorfield('thtr_opn_dt') ?> <?php echo errorfield('thtr_tr_ov_opn_dt') ?> <?php echo errorfield('sbthtr_assoc_thtr_opn_dt_mtch') ?>"/>
            <h6>i.e. 21-08-2007</h6>
            <input type="radio" name="thtr_opn_dt_frmt" value="1" <?php if($thtr_opn_dt_frmt=='1' || !$thtr_opn_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="thtr_opn_dt_frmt" value="2" <?php if($thtr_opn_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="thtr_opn_dt_frmt" value="3" <?php if($thtr_opn_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="thtr_opn_dt_frmt" value="4" <?php if($thtr_opn_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="thtr_cls_dt" class="entry">
            <label for="thtr_cls_dt" class="fixedwidth">THEATRE CLOSING DATE [DD]-[MM]-[YYYY]: <?php echo error_for('thtr_cls_dt') ?><?php echo error_for('thtr_cls_dt_thtr_clsd') ?><?php echo error_for('thtr_tr_ov_cls_dt') ?><?php echo error_for('sbthtr_assoc_thtr_cls_dt_mtch') ?></label>
            <input type="date" name="thtr_cls_dt" id="thtr_cls_dt" maxlength="10" value="<?php echo $thtr_cls_dt; ?>" class="entryfielddate <?php echo errorfield('thtr_cls_dt') ?> <?php echo errorfield('thtr_cls_dt_thtr_clsd') ?> <?php echo errorfield('thtr_tr_ov_cls_dt') ?> <?php echo errorfield('sbthtr_assoc_thtr_cls_dt_mtch') ?>"/>
            <h6>i.e. 05-01-2013</h6>
            <input type="radio" name="thtr_cls_dt_frmt" value="1" <?php if($thtr_cls_dt_frmt=='1' || !$thtr_cls_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="thtr_cls_dt_frmt" value="2" <?php if($thtr_cls_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="thtr_cls_dt_frmt" value="3" <?php if($thtr_cls_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="thtr_cls_dt_frmt" value="4" <?php if($thtr_cls_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="thtr_clsd" class="entry">
            <label for="thtr_clsd" class="fixedwidth">THEATRE CLOSED/INACTIVE: <?php echo error_for('thtr_cls_dt_thtr_clsd') ?><?php echo error_for('sbthtr_assoc_thtr_clsd') ?></label>
            <input type="checkbox" name="thtr_clsd" id="thtr_clsd"<?php if($thtr_clsd) {echo ' checked="checked"';} ?>/>
            <h6>Check box if theatre is now closed or inactive.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="thtr_nm_frm_dt" class="entry">
            <label for="thtr_nm_frm_dt" class="fixedwidth">THEATRE NAME BEGINS [DD]-[MM]-[YYYY]: <?php echo error_for('thtr_nm_frm_dt') ?><?php echo error_for('thtr_tr_ov_nm_frm_dt') ?><?php echo error_for('thtr_opn_nm_frm_dt_mtch') ?></label>
            <input type="date" name="thtr_nm_frm_dt" id="thtr_nm_frm_dt" maxlength="10" value="<?php echo $thtr_nm_frm_dt; ?>" class="entryfielddate <?php echo errorfield('thtr_nm_frm_dt') ?> <?php echo errorfield('thtr_tr_ov_nm_frm_dt') ?> <?php echo errorfield('thtr_opn_nm_frm_dt_mtch') ?>"/>
            <h6>i.e. 21-08-2007</h6>
            <input type="radio" name="thtr_nm_frm_dt_frmt" value="1" <?php if($thtr_nm_frm_dt_frmt=='1' || !$thtr_nm_frm_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="thtr_nm_frm_dt_frmt" value="2" <?php if($thtr_nm_frm_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="thtr_nm_frm_dt_frmt" value="3" <?php if($thtr_nm_frm_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="thtr_nm_frm_dt_frmt" value="4" <?php if($thtr_nm_frm_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="thtr_nm_exp_dt" class="entry">
            <label for="thtr_nm_exp_dt" class="fixedwidth">THEATRE NAME EXPIRES [DD]-[MM]-[YYYY]: <?php echo error_for('thtr_nm_exp_dt') ?><?php echo error_for('thtr_nm_exp_dt_nm_exp') ?><?php echo error_for('thtr_tr_ov_nm_exp_dt') ?><?php echo error_for('thtr_cls_nm_exp_dt_mtch') ?></label>
            <input type="date" name="thtr_nm_exp_dt" id="thtr_nm_exp_dt" maxlength="10" value="<?php echo $thtr_nm_exp_dt; ?>" class="entryfielddate <?php echo errorfield('thtr_nm_exp_dt') ?> <?php echo errorfield('thtr_nm_exp_dt_nm_exp') ?> <?php echo errorfield('thtr_tr_ov_nm_exp_dt') ?> <?php echo errorfield('thtr_cls_nm_exp_dt_mtch') ?>"/>
            <h6>i.e. 05-01-2013</h6>
            <input type="radio" name="thtr_nm_exp_dt_frmt" value="1" <?php if($thtr_nm_exp_dt_frmt=='1' || !$thtr_nm_exp_dt_frmt) {echo ' checked="checked"';} ?>/> DD/MM/YYYY<br>
            <input type="radio" name="thtr_nm_exp_dt_frmt" value="2" <?php if($thtr_nm_frm_dt_frmt=='2') {echo ' checked="checked"';} ?>/> MM/YYYY<br>
            <input type="radio" name="thtr_nm_exp_dt_frmt" value="3" <?php if($thtr_nm_frm_dt_frmt=='3') {echo ' checked="checked"';} ?>/> YYYY</br>
            <input type="radio" name="thtr_nm_exp_dt_frmt" value="4" <?php if($thtr_nm_frm_dt_frmt=='4') {echo ' checked="checked"';} ?>/> NULL</br>
          </div>

          <div id="thtr_nm_exp" class="entry">
            <label for="thtr_nm_exp" class="fixedwidth">THEATRE NAME REPLACED: <?php echo error_for('thtr_nm_exp_dt_nm_exp') ?></label>
            <input type="checkbox" name="thtr_nm_exp" id="thtr_nm_exp"<?php if($thtr_nm_exp) {echo ' checked="checked"';} ?>/>
            <h6>Check box if theatre name has been replaced by another name.</br></h6>
          </div>

          <div id="sbsq_thtr_list" class="entry">
            <label for="sbsq_thtr_list" class="fixedwidth">SUBSEQUENTLY KNOWN AS: <?php echo error_for('sbsq_thtr_array_excss') ?><?php echo error_for('sbsq_thtr_empty') ?><?php echo error_for('sbsq_thtr_hyphn_excss') ?><?php echo error_for('sbsq_thtr_sffx') ?><?php echo error_for('sbsq_thtr_hyphn') ?><?php echo error_for('sbsq_thtr_cln_excss') ?><?php echo error_for('sbsq_thtr_cln') ?><?php echo error_for('sbsq_thtr_lctn_mtch') ?><?php echo error_for('sbsq_thtr_smcln_excss') ?><?php echo error_for('sbsq_thtr_sbthtr') ?><?php echo error_for('sbsq_thtr_smcln') ?><?php echo error_for('sbthtr_sbsq_thtr') ?><?php echo error_for('sbsq_thtr_excss_lngth') ?><?php echo error_for('sbsq_thtr_dplct') ?><?php echo error_for('sbsq_thtr_cmpstn') ?><?php echo error_for('sbsq_thtr_url') ?><?php echo error_for('sbsq_thtr_id_mtch') ?><?php echo error_for('sbsq_thtr_opn_dt_mtch') ?><?php echo error_for('sbsq_thtr_cls_dt_mtch') ?><?php echo error_for('sbsq_thtr_nm_dt_mtch') ?><?php echo error_for('sbsq_thtr_nm_tr_ov') ?><?php echo error_for('thtr_tr_ov_sbsq_thtr') ?></label>
            <input type="text" name="sbsq_thtr_list" id="sbsq_thtr_list" value="<?php echo $sbsq_thtr_list; ?>" class="entryfield <?php echo errorfield('sbsq_thtr_array_excss') ?> <?php echo errorfield('sbsq_thtr_empty') ?> <?php echo errorfield('sbsq_thtr_hyphn_excss') ?> <?php echo errorfield('sbsq_thtr_sffx') ?> <?php echo errorfield('sbsq_thtr_hyphn') ?> <?php echo errorfield('sbsq_thtr_cln_excss') ?> <?php echo errorfield('sbsq_thtr_cln') ?> <?php echo errorfield('sbsq_thtr_lctn_mtch') ?> <?php echo errorfield('sbsq_thtr_smcln_excss') ?> <?php echo errorfield('sbsq_thtr_sbthtr') ?> <?php echo errorfield('sbsq_thtr_smcln') ?> <?php echo errorfield('sbthtr_sbsq_thtr') ?> <?php echo errorfield('sbsq_thtr_excss_lngth') ?> <?php echo errorfield('sbsq_thtr_dplct') ?> <?php echo errorfield('sbsq_thtr_cmpstn') ?> <?php echo errorfield('sbsq_thtr_url') ?> <?php echo errorfield('sbsq_thtr_id_mtch') ?> <?php echo errorfield('sbsq_thtr_opn_dt_mtch') ?> <?php echo errorfield('sbsq_thtr_cls_dt_mtch') ?> <?php echo errorfield('sbsq_thtr_nm_dt_mtch') ?> <?php echo errorfield('sbsq_thtr_nm_tr_ov') ?> <?php echo errorfield('thtr_tr_ov_sbsq_thtr') ?>"/>
            <h6>Enter name of theatre by which this theatre was subsequently known.</br>
            - Separate multiple entries using double comma [,,], location using double colon [::] (optional).</br>
            New Theatre::West End, London: Albert Theatre::West End, London,,Noël Coward Theatre::West End, London</br>
            National Theatre;;The Shed::South Bank, London: National Theatre;;Temporary Theatre::South Bank, London</br>
            -To differentiate identically-named theatres, use a double hyphen followed by an integer between 1 and 99:-</br>
            Bush Theatre (Shepherd's Bush, London)--2</h6>
          </div>

          <div id="prvs_thtr_list" class="entry">
            <label for="prvs_thtr_list" class="fixedwidth">PREVIOUSLY KNOWN AS: <?php echo error_for('prvs_thtr_array_excss') ?><?php echo error_for('prvs_thtr_empty') ?><?php echo error_for('prvs_thtr_hyphn_excss') ?><?php echo error_for('prvs_thtr_sffx') ?><?php echo error_for('prvs_thtr_hyphn') ?><?php echo error_for('prvs_thtr_cln_excss') ?><?php echo error_for('prvs_thtr_cln') ?><?php echo error_for('prvs_thtr_lctn_mtch') ?><?php echo error_for('prvs_thtr_smcln_excss') ?><?php echo error_for('prvs_thtr_sbthtr') ?><?php echo error_for('prvs_thtr_smcln') ?><?php echo error_for('sbthtr_prvs_thtr') ?><?php echo error_for('prvs_thtr_excss_lngth') ?><?php echo error_for('prvs_thtr_dplct') ?><?php echo error_for('prvs_thtr_cmpstn') ?><?php echo error_for('prvs_thtr_url') ?><?php echo error_for('prvs_thtr_id_mtch') ?><?php echo error_for('prvs_thtr_opn_dt_mtch') ?><?php echo error_for('prvs_thtr_cls_dt_mtch') ?><?php echo error_for('prvs_thtr_nm_dt_mtch') ?><?php echo error_for('prvs_thtr_nm_tr_ov') ?><?php echo error_for('thtr_tr_ov_prvs_thtr') ?></label>
            <input type="text" name="prvs_thtr_list" id="prvs_thtr_list" value="<?php echo $prvs_thtr_list; ?>" class="entryfield <?php echo errorfield('prvs_thtr_array_excss') ?> <?php echo errorfield('prvs_thtr_empty') ?> <?php echo errorfield('prvs_thtr_hyphn_excss') ?> <?php echo errorfield('prvs_thtr_sffx') ?> <?php echo errorfield('prvs_thtr_hyphn') ?> <?php echo errorfield('prvs_thtr_cln_excss') ?> <?php echo errorfield('prvs_thtr_cln') ?> <?php echo errorfield('prvs_thtr_lctn_mtch') ?> <?php echo errorfield('prvs_thtr_smcln_excss') ?> <?php echo errorfield('prvs_thtr_sbthtr') ?> <?php echo errorfield('prvs_thtr_smcln') ?> <?php echo errorfield('sbthtr_prvs_thtr') ?> <?php echo errorfield('prvs_thtr_excss_lngth') ?> <?php echo errorfield('prvs_thtr_dplct') ?> <?php echo errorfield('prvs_thtr_cmpstn') ?> <?php echo errorfield('prvs_thtr_url') ?> <?php echo errorfield('prvs_thtr_id_mtch') ?> <?php echo errorfield('prvs_thtr_opn_dt_mtch') ?> <?php echo errorfield('prvs_thtr_cls_dt_mtch') ?> <?php echo errorfield('prvs_thtr_nm_dt_mtch') ?> <?php echo errorfield('prvs_thtr_nm_tr_ov') ?> <?php echo errorfield('thtr_tr_ov_prvs_thtr') ?>"/>
            <h6>Enter name of theatre by which this theatre was previously known.</br>
            - Separate multiple entries using double comma [,,], location using double colon [::] (optional).</br>
            Harold Pinter Theatre::West End, London: Royal Comedy Theatre::West End, London,,Comedy Theatre::West End, London</br>
            Hampstead Theatre;;Hampstead Downstairs::London: Hampstead Theatre;;Michael Frayn Space::London</br>
            -To differentiate identically-named theatres, use a double hyphen followed by an integer between 1 and 99:-</br>
            Bush Theatre (Shepherd's Bush, London)--2</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="sbsqad_thtr_list" class="entry">
            <label for="sbsqad_thtr_list" class="fixedwidth">SUBSEQUENTLY LOCATED: <?php echo error_for('sbsqad_thtr_array_excss') ?><?php echo error_for('sbsqad_thtr_empty') ?><?php echo error_for('sbsqad_thtr_hyphn_excss') ?><?php echo error_for('sbsqad_thtr_sffx') ?><?php echo error_for('sbsqad_thtr_hyphn') ?><?php echo error_for('sbsqad_thtr_cln_excss') ?><?php echo error_for('sbsqad_thtr_cln') ?><?php echo error_for('sbsqad_thtr_smcln_excss') ?><?php echo error_for('sbsqad_thtr_sbthtr') ?><?php echo error_for('sbsqad_thtr_smcln') ?><?php echo error_for('sbthtr_sbsqad_thtr') ?><?php echo error_for('sbsqad_thtr_dplct') ?><?php echo error_for('sbsqad_thtr_excss_lngth') ?><?php echo error_for('sbsqad_thtr_cmpstn') ?><?php echo error_for('sbsqad_thtr_url') ?><?php echo error_for('sbsqad_thtr_id_mtch') ?><?php echo error_for('sbsqad_thtr_opn_cls_dt') ?><?php echo error_for('sbsqad_thtr_nm_tr_ov') ?><?php echo error_for('thtr_tr_ov_sbsqad_thtr') ?></label>
            <input type="text" name="sbsqad_thtr_list" id="sbsqad_thtr_list" value="<?php echo $sbsqad_thtr_list; ?>" class="entryfield <?php echo errorfield('sbsqad_thtr_array_excss') ?> <?php echo errorfield('sbsqad_thtr_empty') ?> <?php echo errorfield('sbsqad_thtr_hyphn_excss') ?> <?php echo errorfield('sbsqad_thtr_sffx') ?> <?php echo errorfield('sbsqad_thtr_hyphn') ?> <?php echo errorfield('sbsqad_thtr_cln_excss') ?> <?php echo errorfield('sbsqad_thtr_cln') ?> <?php echo errorfield('sbsqad_thtr_smcln_excss') ?> <?php echo errorfield('sbsqad_thtr_sbthtr') ?> <?php echo errorfield('sbsqad_thtr_smcln') ?> <?php echo errorfield('sbthtr_sbsqad_thtr') ?> <?php echo errorfield('sbsqad_thtr_dplct') ?> <?php echo errorfield('sbsqad_thtr_excss_lngth') ?> <?php echo errorfield('sbsqad_thtr_cmpstn') ?> <?php echo errorfield('sbsqad_thtr_url') ?> <?php echo errorfield('sbsqad_thtr_id_mtch') ?> <?php echo errorfield('sbsqad_thtr_opn_cls_dt') ?> <?php echo errorfield('sbsqad_thtr_nm_tr_ov') ?> <?php echo errorfield('thtr_tr_ov_sbsqad_thtr') ?>"/>
            <h6>Enter links to theatre of where it was subsequently located.</br>
            - Separate multiple entries using double comma [,,], location using double colon [::] (optional).</br>
            Bush Theatre::Shepherd's Bush, London: Bush Theatre::Shepherd's Bush, London--2</br>
            Southwark Playhouse::Southwark, London--1: Southwark Playhouse::Southwark, London--2,,Southwark Playhouse::Elephant & Castle, London</br>
            -To differentiate identically-named theatres, use a double hyphen followed by an integer between 1 and 99:-</br>
            Bush Theatre (Shepherd's Bush, London)--2</h6>
          </div>

          <div id="prvsad_thtr_list" class="entry">
            <label for="prvsad_thtr_list" class="fixedwidth">PREVIOUSLY LOCATED: <?php echo error_for('prvsad_thtr_array_excss') ?><?php echo error_for('prvsad_thtr_empty') ?><?php echo error_for('prvsad_thtr_hyphn_excss') ?><?php echo error_for('prvsad_thtr_sffx') ?><?php echo error_for('prvsad_thtr_hyphn') ?><?php echo error_for('prvsad_thtr_cln_excss') ?><?php echo error_for('prvsad_thtr_cln') ?><?php echo error_for('prvsad_thtr_smcln_excss') ?><?php echo error_for('prvsad_thtr_sbthtr') ?><?php echo error_for('prvsad_thtr_smcln') ?><?php echo error_for('sbthtr_prvsad_thtr') ?><?php echo error_for('prvsad_thtr_dplct') ?><?php echo error_for('prvsad_thtr_excss_lngth') ?><?php echo error_for('prvsad_thtr_cmpstn') ?><?php echo error_for('prvsad_thtr_url') ?><?php echo error_for('prvsad_thtr_id_mtch') ?><?php echo error_for('prvsad_thtr_opn_cls_dt') ?><?php echo error_for('prvsad_thtr_nm_tr_ov') ?><?php echo error_for('thtr_tr_ov_prvsad_thtr') ?></label>
            <input type="text" name="prvsad_thtr_list" id="prvsad_thtr_list" value="<?php echo $prvsad_thtr_list; ?>" class="entryfield <?php echo errorfield('prvsad_thtr_array_excss') ?> <?php echo errorfield('prvsad_thtr_empty') ?> <?php echo errorfield('prvsad_thtr_hyphn_excss') ?> <?php echo errorfield('prvsad_thtr_sffx') ?> <?php echo errorfield('prvsad_thtr_hyphn') ?> <?php echo errorfield('prvsad_thtr_cln_excss') ?> <?php echo errorfield('prvsad_thtr_cln') ?> <?php echo errorfield('prvsad_thtr_smcln_excss') ?> <?php echo errorfield('prvsad_thtr_sbthtr') ?> <?php echo errorfield('prvsad_thtr_smcln') ?> <?php echo errorfield('sbthtr_prvsad_thtr') ?> <?php echo errorfield('prvsad_thtr_dplct') ?> <?php echo errorfield('prvsad_thtr_excss_lngth') ?> <?php echo errorfield('prvsad_thtr_cmpstn') ?> <?php echo errorfield('prvsad_thtr_url') ?> <?php echo errorfield('prvsad_thtr_id_mtch') ?> <?php echo errorfield('prvsad_thtr_opn_cls_dt') ?> <?php echo errorfield('prvsad_thtr_nm_tr_ov') ?> <?php echo errorfield('thtr_tr_ov_prvsad_thtr') ?>"/>
            <h6>Enter links to theatre of where it was previously located.</br>
            - Separate multiple entries using double comma [,,], location using double colon [::] (optional).</br>
            Arcola Theatre::Dalston, London--2: Arcola Theatre::Dalston, London--1</br>
            Southwark Playhouse::Elephant & Castle, London: Southwark Playhouse::Southwark, London--1,,Southwark Playhouse::Southwark, London--2</br>
            -To differentiate identically-named theatres, use a double hyphen followed by an integer between 1 and 99:-</br>
            Bush Theatre (Shepherd's Bush, London)--2</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="thtr_tr_ov" class="entry">
            <label for="thtr_tr_ov" class="fixedwidth">TOUR OVERVIEW: <?php echo error_for('prd_tr_ov_assocs') ?><?php echo error_for('non_prd_tr_ov_assocs') ?><?php echo error_for('thtr_tr_ov_sbthtr_assoc_exst') ?></label>
            <input type="checkbox" name="thtr_tr_ov" id="thtr_tr_ov"<?php if($thtr_tr_ov) {echo ' checked="checked"';} ?>/>
            <h6>Check box if entry is a Tour Overview, i.e. UK Tour, International Tour, etc.</h6>
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
          <input type="hidden" name="thtr_id" value="<?php echo $thtr_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>