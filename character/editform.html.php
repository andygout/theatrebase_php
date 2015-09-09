<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (character) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>CHARACTER:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing character.</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('char_edit_error') ?>
      <?php echo error_for('char_excss_lngth') ?>
      <?php echo error_for('char_url') ?>
      <?php echo error_for('char_dlt') ?>
      </div>
      <form action="" method="post">
        <fieldset>
          <div id="char_nm" class="entry">
            <label for="char_nm" class="fixedwidth">* CHARACTER NAME: <?php echo error_for('char_nm') ?></label>
            <input type="text" name="char_nm" id="char_nm" maxlength="255" value="<?php echo $char_nm; ?>" class="entryfield <?php echo errorfield('char_nm') ?> <?php echo errorfield('char_excss_lngth') ?> <?php echo errorfield('char_url') ?>"/>
            <h6>i.e. Hamlet / Claudius / Gertrude / Ophelia / Polonius, etc.</h6>
          </div>

          <div id="char_suffix_num" class="entry">
            <label for="char_suffix_num" class="fixedwidth">SUFFIX [1-999999]: <?php echo error_for('char_sffx') ?></label>
            <input type="text" name="char_sffx_num" id="char_sffx_num" maxlength="6" value="<?php echo $char_sffx_num; ?>" class="entryfield6chars <?php echo errorfield('char_sffx') ?> <?php echo errorfield('char_excss_lngth') ?> <?php echo errorfield('char_url') ?>"/>
            <h6>To differentiate theatres with the same name, i.e. 1, 2, 3 (must be left empty (or as 0) or between 1 and 999,999 with no leading 0s).</h6>
          </div>

          <div id="char_lnk" class="entry">
            <label for="char_lnk" class="fixedwidth">CHARACTER LINK: <?php echo error_for('char_lnk_excss_lngth') ?></label>
            <input type="text" name="char_lnk" id="char_lnk" maxlength="255" value="<?php echo $char_lnk; ?>" class="entryfield <?php echo errorfield('char_lnk_excss_lngth') ?>"/>
            <h6>Add specified wording to attract all occurences of this role from productions (given that different productions address same characters differently).</br>
            i.e. Irina Nikolayevna Arkadina -> Arkadina better employed as the link phrase given some productions will use this over the full name and otherwise be missed out of character's credit list.</br>
            If left blank, the character name will fill this field.</h6>
          </div>

          <div id="char_sx" class="entry">
            <label for="char_sx" class="fixedwidth">SEX: </label>
            <input type="radio" name="char_sx" value="1" <?php if($char_sx=='1' || !$char_sx) {echo ' checked="checked"';} ?>/> N/A (i.e. not yet applied)<br>
            <input type="radio" name="char_sx" value="2" <?php if($char_sx=='2') {echo ' checked="checked"';} ?>/> Male<br>
            <input type="radio" name="char_sx" value="3" <?php if($char_sx=='3') {echo ' checked="checked"';} ?>/> Female</br>
            <input type="radio" name="char_sx" value="4" <?php if($char_sx=='4') {echo ' checked="checked"';} ?>/> Non-specific</br>
          </div>

          <div id="char_age" class="entry">
            <label for="char_age" class="fixedwidth">AGE (RANGE) [1-999]-[1-999]: <?php echo error_for('char_age_frm') ?><?php echo error_for('char_age_to') ?><?php echo error_for('char_age_frm_to') ?></label>
            <input type="text" name="char_age_frm" id="char_age_frm" maxlength="3" value="<?php echo $char_age_frm; ?>" class="entryfield3chars <?php echo errorfield('char_age_frm') ?> <?php echo errorfield('char_age_frm_to') ?>"/>-<input type="text" name="char_age_to" id="char_age_to" maxlength="3" value="<?php echo $char_age_to; ?>" class="entryfield3chars <?php echo errorfield('char_age_to') ?> <?php echo errorfield('char_age_frm_to') ?>"/>
            <h6>Select age range of character.  If exact age is given, list same age in both fields.</h6>
          </div>

          <div id="char_dscr" class="entry">
            <label for="char_dscr" class="fixedwidth">DESCRIPTION: <?php echo error_for('char_dscr_excss_lngth') ?></label>
            <input type="text" name="char_dscr" id="char_dscr" maxlength="255" value="<?php echo $char_dscr; ?>" class="entryfield <?php echo errorfield('char_dscr_excss_lngth') ?>"/>
            <h6>i.e. A Roman general; father of Lavinia, Lucius, Quintus, Martius, Mutius; brother of Marcus, etc.<br>
          </div>
        </fieldset>

        <fieldset>
          <div id="char_amnt" class="entry">
            <label for="char_amnt" class="fixedwidth">CHARACTER AMOUNT (SPECIFIC) [1-99]: <?php echo error_for('char_amnt') ?></label>
            <input type="text" name="char_amnt" id="char_amnt" maxlength="2" value="<?php echo $char_amnt; ?>" class="entryfield2chars <?php echo errorfield('char_amnt') ?>"/>
            <h6>Select amount character represents if a specific multiple amount, i.e 3 for Witches in MACBETH; else leave as 1.</h6>
          </div>

          <div id="char_mlti" class="entry">
            <label for="char_mlti" class="fixedwidth">CHARACTER AMOUNT (MULTIPLE NON-SPECIFIC): </label>
            <input type="checkbox" name="char_mlti" id="char_mlti"<?php if($char_mlti) { echo ' checked="checked"'; } ?>/>
            <h6>Check box if character represents a non-specific multiple amount, i.e. Churchgoers in EMPEROR & GALILEAN (character amount field must be left empty).</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="ethn_list" class="entry">
            <label for="ethn_list" class="fixedwidth">ETHNICITY: <?php echo error_for('ethn_array_excss') ?><?php echo error_for('ethn_empty') ?><?php echo error_for('ethn_dplct') ?><?php echo error_for('ethn_excss_lngth') ?><?php echo error_for('ethn_nm') ?></label>
            <input type="text" name="ethn_list" id="ethn_list" value="<?php echo $ethn_list; ?>" class="entryfield <?php echo errorfield('ethn_array_excss') ?> <?php echo errorfield('ethn_empty') ?> <?php echo errorfield('ethn_dplct') ?> <?php echo errorfield('ethn_excss_lngth') ?> <?php echo errorfield('ethn_nm') ?>"/>
            <h6>i.e. African, Afro-Caribbean, Arab, Bangladeshi, Black British, Chinese, Indian, Japanese, Jewish, Korean, Mestizo, Mulatto, Native American, Pakistani, White, White-Afro-Caribbean, White-Pakistani, Zambos, etc.</br>
            - Separate multiple entries using double comma [,,] (likely required if character represents multiple amount of characters):-</br>
            Afro-Caribbean,,Chinese</h6>
          </div>

          <div id="org_lctn_list" class="entry">
            <label for="org_lctn_list" class="fixedwidth">PLACE OF ORIGIN: <?php echo error_for('org_lctn_nm_array_excss') ?><?php echo error_for('org_lctn_empty') ?><?php echo error_for('org_lctn_pipe_excss') ?><?php echo error_for('org_lctn_pipe') ?><?php echo error_for('org_lctn_hyphn_excss') ?><?php echo error_for('org_lctn_sffx') ?><?php echo error_for('org_lctn_hyphn') ?><?php echo error_for('org_lctn_dplct') ?><?php echo error_for('org_lctn_nm_excss_lngth') ?><?php echo error_for('org_lctn_nm') ?><?php echo error_for('org_lctn_alt_list') ?><?php echo error_for('org_lctn_alt_array_excss') ?><?php echo error_for('org_lctn_alt_empty') ?><?php echo error_for('org_lctn_alt_hyphn_excss') ?><?php echo error_for('org_lctn_alt_sffx') ?><?php echo error_for('org_lctn_alt_hyphn') ?><?php echo error_for('org_lctn_alt_dplct') ?><?php echo error_for('org_lctn_alt_excss_lngth') ?><?php echo error_for('org_lctn_alt_url') ?><?php echo error_for('org_lctn_alt') ?><?php echo error_for('org_lctn_alt_assoc') ?></label>
            <input type="text" name="org_lctn_list" id="org_lctn_list" value="<?php echo $org_lctn_list; ?>" class="entryfield <?php echo errorfield('org_lctn_nm_array_excss') ?> <?php echo errorfield('org_lctn_empty') ?> <?php echo errorfield('org_lctn_pipe_excss') ?> <?php echo errorfield('org_lctn_pipe') ?> <?php echo errorfield('org_lctn_hyphn_excss') ?> <?php echo errorfield('org_lctn_sffx') ?> <?php echo errorfield('org_lctn_hyphn') ?> <?php echo errorfield('org_lctn_dplct') ?> <?php echo errorfield('org_lctn_nm_excss_lngth') ?> <?php echo errorfield('org_lctn_nm') ?> <?php echo errorfield('org_lctn_alt_list') ?> <?php echo errorfield('org_lctn_alt_array_excss') ?> <?php echo errorfield('org_lctn_alt_empty') ?> <?php echo errorfield('org_lctn_alt_hyphn_excss') ?> <?php echo errorfield('org_lctn_alt_sffx') ?> <?php echo errorfield('org_lctn_alt_hyphn') ?> <?php echo errorfield('org_lctn_alt_dplct') ?> <?php echo errorfield('org_lctn_alt_excss_lngth') ?> <?php echo errorfield('org_lctn_alt_url') ?> <?php echo errorfield('org_lctn_alt') ?> <?php echo errorfield('org_lctn_alt_assoc') ?>"/>
            <h6>i.e. Elysian Fields / Rome / Basildon / Manhattan / Bangkok, etc.</br>
            Blanche DuBois from A Streetcar Named Desire: Elysian Fields</br>
            Lavinia from Titus Andronicus: Rome</br>
            - Separate multiple entries using double comma [,,] (if character represents mutiple amount of characters) and only list smallest denomination (of location) given:-</br>
            - To differentiate identically-named places, use a double hyphen followed by an integer between 1 and 99:- London--2 / Kingston--2 / Springfield--2</br>
            - When a location should be associated with specific locations (default will exclude those that are pre-existing and fictional), set list with double pipes [||] and separate multiple entries with double chevron [>>], i.e. Hagia Irene||Constantinople>>Turkey>>Europe / The Ministry Of Love||London>>Airstrip One>>Oceania</h6>
          </div>

          <div id="prof_list" class="entry">
            <label for="prof_list" class="fixedwidth">PROFESSION: <?php echo error_for('prof_array_excss') ?><?php echo error_for('prof_empty') ?><?php echo error_for('prof_dplct') ?><?php echo error_for('prof_excss_lngth') ?><?php echo error_for('prof_nm') ?></label>
            <input type="text" name="prof_list" id="prof_list" value="<?php echo $prof_list; ?>" class="entryfield <?php echo errorfield('prof_array_excss') ?> <?php echo errorfield('prof_empty') ?> <?php echo errorfield('prof_dplct') ?> <?php echo errorfield('prof_excss_lngth') ?> <?php echo errorfield('prof_nm') ?>"/>
            <h6>i.e. Doctor / Fireman / King of England / Lawyer / Magistrate / Soldier, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            Chemist,,Doctor</h6>
          </div>

          <div id="attr_list" class="entry">
            <label for="attr_list" class="fixedwidth">ATTRIBUTES: <?php echo error_for('attr_array_excss') ?><?php echo error_for('attr_empty') ?><?php echo error_for('attr_dplct') ?><?php echo error_for('attr_excss_lngth') ?><?php echo error_for('attr_nm') ?></label>
            <input type="text" name="attr_list" id="attr_list" value="<?php echo $attr_list; ?>" class="entryfield <?php echo errorfield('attr_array_excss') ?> <?php echo errorfield('attr_empty') ?> <?php echo errorfield('attr_dplct') ?> <?php echo errorfield('attr_excss_lngth') ?> <?php echo errorfield('attr_nm') ?>"/>
            <h6>List the physical and mental attributes of the character (also extends to type of being; age description; and religion).</br>
            i.e. Blonde hair / Pregnant / Tattoo / Husky voice / Paraplegic / Obsessive-compulsive disorder / Asperger syndrome / Philosophical / Pathological liar / Duplicitous / Fairy / Lion / Toad / Tree / God / Devil / Child / Teenager/ Elderly / Christian / Buddhist, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            Blonde hair,,Pregnant</h6>
          </div>

          <div id="abil_list" class="entry">
            <label for="abil_list" class="fixedwidth">ABILITIES: <?php echo error_for('abil_array_excss') ?><?php echo error_for('abil_empty') ?><?php echo error_for('abil_dplct') ?><?php echo error_for('abil_excss_lngth') ?><?php echo error_for('abil_nm') ?></label>
            <input type="text" name="abil_list" id="abil_list" value="<?php echo $abil_list; ?>" class="entryfield <?php echo errorfield('abil_array_excss') ?> <?php echo errorfield('abil_empty') ?> <?php echo errorfield('abil_dplct') ?> <?php echo errorfield('abil_excss_lngth') ?> <?php echo errorfield('abil_nm') ?>"/>
            <h6>i.e. Fencing / Singing / Painting / French speaker / Acrobat, etc.</br>
            - Separate multiple entries using double comma [,,]:-</br>
            Fencing,,Singing</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="var_char_list" class="entry">
            <label for="var_char_list" class="fixedwidth">VARIANT CHARACTERS: <?php echo error_for('var_char_nm_array_excss') ?><?php echo error_for('var_char_empty') ?><?php echo error_for('var_char_hyphn_excss') ?><?php echo error_for('var_char_sffx') ?><?php echo error_for('var_char_hyphn') ?><?php echo error_for('var_char_dplct') ?><?php echo error_for('var_char_nm_excss_lngth') ?><?php echo error_for('var_char_nm') ?><?php echo error_for('var_char_nonexst') ?><?php echo error_for('var_char_id_mtch') ?></label>
            <input type="text" name="var_char_list" id="var_char_list" value="<?php echo $var_char_list; ?>" class="entryfield <?php echo errorfield('var_char_nm_array_excss') ?> <?php echo errorfield('var_char_empty') ?> <?php echo errorfield('var_char_hyphn_excss') ?> <?php echo errorfield('var_char_sffx') ?> <?php echo errorfield('var_char_hyphn') ?> <?php echo errorfield('var_char_dplct') ?> <?php echo errorfield('var_char_nm_excss_lngth') ?> <?php echo errorfield('var_char_nm') ?> <?php echo errorfield('var_char_nonexst') ?> <?php echo errorfield('var_char_id_mtch') ?>"/>
            <h6>i.e. To link to variations of a character when there is no shared material or source material, i.e. Linking Hamlet from HAMLET (i.e. Hamlet--1) to Hamlet from ROSENCRANTZ AND GUILDENSTERN ARE DEAD (i.e. Hamlet--2).</br>
            - Separate multiple entries using double comma [,,]:-</br>
            Hamlet,,Hamlet The Dane,,Prince Hamlet</br>
            To differentiate identically-named characters, use a double hyphen followed by an integer between 1 and 99:-</br>
            Demetrius--2</h6>
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
          <input type="hidden" name="char_id" value="<?php echo $char_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>