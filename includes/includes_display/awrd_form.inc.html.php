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
      <h4>AWARDS:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p><?php echo $pagesubtitle; ?></p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('awrd_add_edit_error') ?>
      <?php echo error_for('awrds_nm_awrd_yr') ?>
      <?php echo error_for('awrd_dlt') ?>
      </div>
      <form action="" method="post">
        <fieldset>
          <div id="awrds_nm" class="entry">
            <label for="awrds_nm" class="fixedwidth">* AWARDS NAME: <?php echo error_for('awrds_nm') ?></label>
            <input type="text" name="awrds_nm" id="awrds_nm" maxlength="255" value="<?php echo $awrds_nm; ?>" class="entryfield <?php echo errorfield('awrds_nm') ?> <?php echo errorfield('awrds_nm_awrd_yr') ?>"/>
            <h6>i.e. Laurence Olivier Awards, Critics' Choice Awards, WhatsOnStage Awards, etc.</h6>
          </div>

          <div id="awrd_yr" class="entry">
            <label for="awrd_yr" class="fixedwidth">* AWARD YEAR [YYYY]: <?php echo error_for('awrd_yr') ?></label>
            <input type="text" name="awrd_yr" id="awrd_yr" maxlength="4" value="<?php echo $awrd_yr; ?>" class="entryfield4chars <?php echo errorfield('awrd_yr') ?> <?php echo errorfield('awrds_nm_awrd_yr') ?>"/>
            <h6>The year of the awards.</h6>
          </div>

          <div id="awrd_yr_end" class="entry">
            <label for="awrd_yr_end" class="fixedwidth">AWARD YEAR #2 [YYYY]: <?php echo error_for('awrd_yr_end') ?></label>
            <input type="text" name="awrd_yr_end" id="awrd_yr_end" maxlength="4" value="<?php echo $awrd_yr_end; ?>" class="entryfield4chars <?php echo errorfield('awrd_yr_end') ?> <?php echo errorfield('awrds_nm_awrd_yr') ?>"/>
            <h6>If awards cover a range of two years (i.e. Laurence Olivier Awards 1989/90) then enter the latter of the two years (i.e. 1990) here.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="awrd_dt" class="entry">
            <label for="awrd_dt" class="fixedwidth">AWARDS DATE [DD]-[MM]-[YYYY]: <?php echo error_for('awrd_dt') ?></label>
            <input type="date" name="awrd_dt" id="awrd_dt" maxlength="10" value="<?php echo $awrd_dt; ?>" class="entryfielddate <?php echo errorfield('awrd_dt') ?>"/>
            <h6>i.e. 27-03-2013</h6>
          </div>

          <div id="thtr_nm" class="entry">
            <label for="thtr_nm" class="fixedwidth">VENUE: <?php echo error_for('thtr_hyphn_excss') ?><?php echo error_for('thtr_sffx') ?><?php echo error_for('thtr_hyphn') ?><?php echo error_for('thtr_cln_excss') ?><?php echo error_for('thtr_smcln_excss') ?><?php echo error_for('thtr_excss_lngth') ?><?php echo error_for('thtr_cmpstn') ?><?php echo error_for('thtr_url') ?><?php echo error_for('thtr_tr_ov') ?></label>
            <input type="text" name="thtr_nm" id="thtr_nm" maxlength="255" value="<?php echo $thtr_nm; ?>" class="entryfield <?php echo errorfield('thtr_hyphn_excss') ?> <?php echo errorfield('thtr_sffx') ?> <?php echo errorfield('thtr_hyphn') ?> <?php echo errorfield('thtr_cln_excss') ?> <?php echo errorfield('thtr_smcln_excss') ?> <?php echo errorfield('thtr_excss_lngth') ?> <?php echo errorfield('thtr_cmpstn') ?> <?php echo errorfield('thtr_url') ?> <?php echo errorfield('thtr_tr_ov') ?>"/>
            <h6>i.e. National Theatre: Olivier Theatre (South Bank, London)</br>
            - Separate location using double colon [::] (optional), and theatre and sub-theatre using [;;] (optional).</br>
            National Theatre;;Olivier Theatre::South Bank, London / Donmar Warehouse::Covent Garden, London / Bristol Old Vic / Salisbury Playhouse;;The Salberg</br>
            - To differentiate identically-named theatres, use a double hyphen followed by an integer between 1 and 99:-</br>
            Bush Theatre::Shepherd's Bush, London--2</br>
            NB: If production is a Tour Overview then write type of tour, i.e. UK Tour, International Tour, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="awrd_list" class="entry">
            <label for="awrd_list" class="fixedwidth">AWARD CATEGORIES (inc. NOMINEE/WINNER information): <?php echo error_for('awrd_ctgry_array_excss') ?><?php echo error_for('awrd_empty') ?><?php echo error_for('awrd_eql_excss') ?><?php echo error_for('awrd_ctgry_dtl_smcln_excss') ?><?php echo error_for('awrd_ctgry_alt_nm') ?><?php echo error_for('awrd_ctgry_alt_nm_smcln') ?><?php echo error_for('awrd_ctgry_dplct') ?><?php echo error_for('awrd_ctgry_nm') ?><?php echo error_for('awrd_nom_dtl_dscr_empty') ?><?php echo error_for('awrd_nom_array_excss') ?><?php echo error_for('awrd_nom_dtl_dscr_cln_excss') ?><?php echo error_for('awrd_nom_dscr_excss_lngth') ?><?php echo error_for('awrd_nom_dtl_pls_excss') ?><?php echo error_for('nom_pts_array_excss') ?><?php echo error_for('nom_pt_empty') ?><?php echo error_for('nom_pt_hyphn_excss') ?><?php echo error_for('nom_pt_sffx') ?><?php echo error_for('nom_pt_hyphn') ?><?php echo error_for('nom_pt_hsh_excss') ?><?php echo error_for('nom_pt_yr') ?><?php echo error_for('nom_pt_yr_frmt') ?><?php echo error_for('nom_pt_hsh') ?><?php echo error_for('nom_pt_dplct') ?><?php echo error_for('nom_pt_nm_yr_excss_lngth') ?><?php echo error_for('nom_pt_url') ?><?php echo error_for('awrd_nom_dtl_pls') ?><?php echo error_for('awrd_nom_dtl_hsh_excss') ?><?php echo error_for('nom_prd_array_excss') ?><?php echo error_for('awrd_nom_prd_empty') ?><?php echo error_for('awrd_nom_prd_nonnmrcl') ?><?php echo error_for('nom_prd_dplct') ?><?php echo error_for('awrd_nom_prd_nonexst') ?><?php echo error_for('awrd_nom_dtl_hsh') ?><?php echo error_for('awrd_nom_prd_pt') ?><?php echo error_for('awrd_ctgry_prd_pt') ?><?php echo error_for('awrd_nom_prsn_empty') ?><?php echo error_for('awrd_pipe_excss') ?><?php echo error_for('awrd_pipe') ?><?php echo error_for('awrd_nomnee_array_excss') ?><?php echo error_for('awrd_comp_tld_excss') ?><?php echo error_for('awrd_comp_rl') ?><?php echo error_for('awrd_comp_tld') ?><?php echo error_for('awrd_comp_hyphn_excss') ?><?php echo error_for('awrd_comp_sffx') ?><?php echo error_for('awrd_comp_hyphn') ?><?php echo error_for('awrd_comp_nm_excss_lngth') ?><?php echo error_for('nom_comp_dplct') ?><?php echo error_for('awrd_comp_url') ?><?php echo error_for('awrd_prsn_empty') ?><?php echo error_for('awrd_prsn_tld_excss') ?><?php echo error_for('awrd_prsn_rl') ?><?php echo error_for('awrd_prsn_tld') ?><?php echo error_for('awrd_prsn_hyphn_excss') ?><?php echo error_for('awrd_prsn_sffx') ?><?php echo error_for('awrd_prsn_hyphn') ?><?php echo error_for('awrd_prsn_smcln_excss') ?><?php echo error_for('awrd_prsn_excss_lngth') ?><?php echo error_for('nom_prsn_dplct') ?><?php echo error_for('awrd_prsn_smcln') ?><?php echo error_for('awrd_prsn_nm') ?><?php echo error_for('awrd_prsn_url') ?><?php echo error_for('awrd_nom_dtl_dscr_cln') ?><?php echo error_for('awrd_eql') ?></label>
            <input type="text" name="awrd_list" id="awrd_list" value="<?php echo $awrd_list; ?>" class="entryfield <?php echo errorfield('awrd_ctgry_array_excss') ?> <?php echo errorfield('awrd_empty') ?> <?php echo errorfield('awrd_eql_excss') ?> <?php echo errorfield('awrd_ctgry_dtl_smcln_excss') ?> <?php echo errorfield('awrd_ctgry_alt_nm') ?> <?php echo errorfield('awrd_ctgry_alt_nm_smcln') ?> <?php echo errorfield('awrd_ctgry_dplct') ?> <?php echo errorfield('awrd_ctgry_nm') ?> <?php echo errorfield('awrd_nom_dtl_dscr_empty') ?> <?php echo errorfield('awrd_nom_array_excss') ?> <?php echo errorfield('awrd_nom_dtl_dscr_cln_excss') ?> <?php echo errorfield('awrd_nom_dscr_excss_lngth') ?> <?php echo errorfield('awrd_nom_dtl_pls_excss') ?> <?php echo errorfield('nom_pts_array_excss') ?> <?php echo errorfield('nom_pt_empty') ?> <?php echo errorfield('nom_pt_hyphn_excss') ?> <?php echo errorfield('nom_pt_sffx') ?> <?php echo errorfield('nom_pt_hyphn') ?> <?php echo errorfield('nom_pt_hsh_excss') ?> <?php echo errorfield('nom_pt_yr') ?> <?php echo errorfield('nom_pt_yr_frmt') ?> <?php echo errorfield('nom_pt_hsh') ?> <?php echo errorfield('nom_pt_dplct') ?> <?php echo errorfield('nom_pt_nm_yr_excss_lngth') ?> <?php echo errorfield('nom_pt_url') ?> <?php echo errorfield('awrd_nom_dtl_pls') ?> <?php echo errorfield('awrd_nom_dtl_hsh_excss') ?> <?php echo errorfield('nom_prd_array_excss') ?> <?php echo errorfield('awrd_nom_prd_empty') ?> <?php echo errorfield('awrd_nom_prd_nonnmrcl') ?> <?php echo errorfield('nom_prd_dplct') ?> <?php echo errorfield('awrd_nom_prd_nonexst') ?> <?php echo errorfield('awrd_nom_dtl_hsh') ?> <?php echo errorfield('awrd_nom_prd_pt') ?> <?php echo errorfield('awrd_ctgry_prd_pt') ?> <?php echo errorfield('awrd_nom_prsn_empty') ?> <?php echo errorfield('awrd_pipe_excss') ?> <?php echo errorfield('awrd_pipe') ?> <?php echo errorfield('awrd_nomnee_array_excss') ?> <?php echo errorfield('awrd_comp_tld_excss') ?> <?php echo errorfield('awrd_comp_rl') ?> <?php echo errorfield('awrd_comp_tld') ?> <?php echo errorfield('awrd_comp_hyphn_excss') ?> <?php echo errorfield('awrd_comp_sffx') ?> <?php echo errorfield('awrd_comp_hyphn') ?> <?php echo errorfield('awrd_comp_nm_excss_lngth') ?> <?php echo errorfield('nom_comp_dplct') ?> <?php echo errorfield('awrd_comp_url') ?> <?php echo errorfield('awrd_prsn_empty') ?> <?php echo errorfield('awrd_prsn_tld_excss') ?> <?php echo errorfield('awrd_prsn_rl') ?> <?php echo errorfield('awrd_prsn_tld') ?> <?php echo errorfield('awrd_prsn_hyphn_excss') ?> <?php echo errorfield('awrd_prsn_sffx') ?> <?php echo errorfield('awrd_prsn_hyphn') ?> <?php echo errorfield('awrd_prsn_smcln_excss') ?> <?php echo errorfield('awrd_prsn_excss_lngth') ?> <?php echo errorfield('nom_prsn_dplct') ?> <?php echo errorfield('awrd_prsn_smcln') ?> <?php echo errorfield('awrd_prsn_nm') ?> <?php echo errorfield('awrd_prsn_url') ?> <?php echo errorfield('awrd_nom_dtl_dscr_cln') ?> <?php echo errorfield('awrd_eql') ?>"/>
            <h6>- Separate multiple nominations using double comma [,,], nomination/win info using double colon [::], multiple nominees (within nomination) using double chevron [>>], optional role using double tilde [~~] and (if person) given name and family name using double semi-colon [;;]</br>
            - Give (optional) alternate category name using double semi-colon [;;], i.e. Best New Play;;Mastercard Best New Play
            - Categorise companies by ending with double pipes [||]; if people are members of this company on this production, enter subsequent list, separating entries using double slash [//]:-</br>
            - Playtext entry: Playtext Name##c(optional circa indication)-(option BCE indication) Year Started (format: YYYY)(if additional Year Started required:);;c(optional circa indication)-(option BCE indication) Year Written (format: YYYY)--(optional suffix [1-99])</br>
            - Production entry: Enter prd_id</br>
            - To differentiate identically-named people, use a double hyphen followed by an integer between 1 and 99:- Vanessa;;Redgrave--2::Associate Director</br>
            </br>
            Best Actress==Nominee::Gillian;;Anderson~~Nora Helmer##22,,Nominee::Lorraine;;Burroughs~~Camae##15,,Nominee::Imelda;;Staunton~~Kath##19,,Nominee::Juliet;;Stevenson~~Stephanie Anderson##15,,Winner*::Rachel;;Weisz~~Blanche DuBois##2@@Best Actor==Nominee::James Earl;;Jones~~Big Daddy##20,,Nominee::Jude;;Law~~Hamlet##16,,Nominee::James;;McAvoy~~Walker / Ned##23,,Winner*::Mark;;Rylance~~Johnny 'Rooster' Byron##15>>43,,Nominee::Ken;;Stott~~Eddie Carbone##25,,Nominee::Samuel;;West~~Jeffrey Skilling##54>>75</br>
            </br>
            Best Sound Design==Nominee::Christopher;;Shutt##25,,Nominee::Autograph||Ian;;Dickenson##24,,Nominees::Autograph||Andrew;;Bruce//Nick;;Lidster##20,,Winner*::Brian;;Ronan##16@@Most Promising Playwright==Winner*::Penelope;;Skinner++The Village Bike##2011,,Nominee::Laura;;Wade++Breathing Corpses##2009>>Posh##2011</br>
            </br>
            Outstanding Achievement in an Affiliate Theatre==Nominee::Theatre Royal Stratford East~~For a powerful season of provocative work, reaching new audiences||,,Winner*::Roy;;Dotrice~~Marky##15,,Nominees (Stage Management teams)::English National Opera||>>London Coliseum||>>Royal Opera House||,,Nominee::Young Vic~~For an audacious season under the artistic directorship of David Lan||David;;Lan~~Artistic Director,,Nominees (the cast of That Face)::Royal Court Theatre~~Production Company||>>Lindsay;;Duncan~~Martha>>Abigail;;Hood~~Alice>>Felicity;;Jones~~Mia>>Matt;;Smith~~Henry>>Catherine;;Steadman~~Izzy>>Julian;;Wadham~~Hugh##21</br>
            </br>
            Best New Play;;Mastercard Best New Play==Winner*::##44,,Nominee::##29,,Nominee::##43,,Nominee::##69</h6>
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
          <input type="hidden" name="awrd_id" value="<?php echo $awrd_id; ?>"/>
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