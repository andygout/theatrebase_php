<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (profession) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>PROFESSION:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing profession (**N.B. This will amend the profession details for all other instances in which it is used**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('prof_edit_error') ?>
      <?php echo error_for('prof_url') ?>
      <?php echo error_for('prof_dlt') ?>

      </div>
      <form action="" method="post">
        <fieldset>
          <div id="prof_nm" class="entry">
            <label for="prof_nm" class="fixedwidth">* PROFESSION: <?php echo error_for('prof_nm') ?></label>
            <input type="text" name="prof_nm" id="prof_nm" maxlength="255" value="<?php echo $prof_nm; ?>" class="entryfield <?php echo errorfield('prof_nm') ?> <?php echo errorfield('prof_url') ?>"/>
            <h6>i.e. (if character): Doctor / Fireman / King of England / Lawyer / Magistrate / Soldier, etc.</br>
            i.e. (if person): Writer / Novelist / Poet / Librettist / Producer / Performer / Understudy / Director / Set Designer / Composer / Casting / Stage Management / Set Construction / Agent, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rel_prof_list" class="entry">
            <label for="rel_prof_list" class="fixedwidth">RELATED PROFESSIONS: <?php echo error_for('rel_prof_nm_array_excss') ?><?php echo error_for('rel_prof_empty') ?><?php echo error_for('rel_prof_dplct') ?><?php echo error_for('rel_prof_nm_excss_lngth') ?><?php echo error_for('rel_prof_nm') ?><?php echo error_for('rel_prof_nonexst') ?><?php echo error_for('rel_prof_id_mtch') ?><?php echo error_for('rel_prof_inv_comb') ?></label>
            <input type="text" name="rel_prof_list" id="rel_prof_list" value="<?php echo $rel_prof_list; ?>" class="entryfield <?php echo errorfield('rel_prof_nm_array_excss') ?> <?php echo errorfield('rel_prof_empty') ?> <?php echo errorfield('rel_prof_dplct') ?> <?php echo errorfield('rel_prof_nm_excss_lngth') ?> <?php echo errorfield('rel_prof_nm') ?> <?php echo errorfield('rel_prof_nonexst') ?> <?php echo errorfield('rel_prof_id_mtch') ?> <?php echo errorfield('rel_prof_inv_comb') ?>"/>
            <h6>List profession(s) of which this profession is a sub-profession.</br>
            - Separate multiple entries using double comma [,,].</br>
            King of England: King / Royalty</br>
            Cardiologist: Doctor / Medical</br>
            Lawyer: Legal</br>
            Opera director: Director</br>
            Literary agent: Agent</br>
            Assistant Stage Manager: Stage Management</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="textarea" class="entry">
            <label for="textarea" class="fixedwidth">INTERNATIONAL/NON-KEYBOARD CHARACTERS:-</label>
            À / È / Ì / Ò / Ù / à / è / ì / ò / ù / Á / É / Í / Ó / Ú / Ý / á / é / í / ó / ú / ý / Â / Ê / Î / Ô / Û / â / ê / î / ô / û / Ä / Ë / Ï / Ö / Ü / Ÿ / ä / ë / ï / ö / ü / ÿ</br>
            Ã / Ñ / Õ / ã / ñ / õ / Ā / Ē / Ī / Ō / Ū / Ȳ / Ǣ / Ḡ / ā / ē / ī / ō / ū / ȳ / ǣ / ḡ / Å / å / Æ / æ / Œ / œ / Ç / ç / Ð / ð / Ø / ø / ¿ / ¡ / ß</br>
            Ǟ / Ȫ / Ǖ / Ṻ / Ǡ / Ȱ / Ḹ / Ṝ / Ǭ / Ȭ / Ḗ / Ṓ / Ḕ / Ṑ / Ӣ / Ӯ / Ᾱ / Ῑ / Ῡ / ǟ / ȫ / ǖ / ṻ / ǡ / ȱ / ḹ / ṝ / ǭ / ȭ / ḗ / ṓ / ḕ / ṑ / ӣ / ӯ / ᾱ / ῑ / ῡ</br>
            ▪</br>
          </div>
        </fieldset>

        <div id="buttons" class="buttons">
          <input type="hidden" name="prof_id" value="<?php echo $prof_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>