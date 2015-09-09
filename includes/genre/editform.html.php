<?php include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php'; ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (genre) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <h4>GENRE:</h4>
      <h1><?php echo $pagetitle; ?></h1>
      <h3><p>Edit this existing genre (**N.B. This will amend the genre details for all other instances in which it is used**).</p>
      <p>* Mandatory field.</p></h3>
      <div id="errors">
      <?php echo error_for('gnr_edit_error') ?>
      <?php echo error_for('gnr_url') ?>
      <?php echo error_for('gnr_dlt') ?>
      </div>

      <?php if(!empty($rel_gnrs1)) { ?>
      <div id="dscrptn" class="box"><table class="overview">
      <tr><td class="ovrvwcol1">Sub-genres:</td><td><?php echo implode(' / ', $rel_gnrs1); ?></td></tr>
      </table></div></br>
      <?php } ?>

      <form action="" method="post">
        <fieldset>
          <div id="gnr_nm" class="entry">
            <label for="gnr_nm" class="fixedwidth">* GENRE:   <?php echo error_for('gnr_nm') ?></label>
            <input type="text" name="gnr_nm" id="gnr_nm" maxlength="255" value="<?php echo $gnr_nm; ?>" class="entryfield <?php echo errorfield('gnr_nm') ?> <?php echo errorfield('gnr_url') ?>"/>
            <h6>i.e. Theatre of the Absurd / African-American drama / African drama / Alternative theatre / American drama / Ancient Greek drama / Ancient Roman drama / Asian drama / Australian drama / Austrian drama / Avante-garde theatre / Black theatre / British drama / Canadian drama / Caribbean drama / Caroline theatre / Children's theatre / City comedy / Classical / Comedy / Comedy of manners / Commedia dell-Arte / Community theatre / Theatre of Cruelty / Czech drama /
              Dance drama / Documentary theatre / Drama / Elizabethan theatre / English drama / Ensemble / Epic theatre / European drama / Expressionism / Farce / Feminist theatre / French drama / Future history / Gay theatre / German drama / Greek drama / Greek tragedy / History play / Hungarian drama / In-yer-face theatre / Irishdrama / Italian drama / Jacobean theatre / Jacobean revenge tragedy / Japanese drama / Kitchen sink drama / Latin American drama / Melodrama / Middle Eastern drama / Modernist drama / Morality play / Murder mystery /
              Mystery / Myth / Naturalistic drama / Northern Irish drama / Norwegian drama / Theatre of the Oppressed / Parody / Physical theatre / Political theatre / Poor theatre / Popular theatre / Post-colonial drama / Post-dramatic theatre / Postmodern theatre / Restoration comedy / Revenge tragedy / Ritual drama / Russian drama / Satire / Scottish drama / Shakespearean comedy / Shakespearean history / Shakespearean problem play / Shakespearean tragedy / Site-specific /
              Southern Gothic / Spanish drama / Street theatre / Sturm und Drang / Surrealist drama / Swedish drama / Symbolist drama / Thriller / Tragedy / Tragicomedy / Verbatim theatre  / Welsh drama / Youth theatre, etc.</h6>
          </div>
        </fieldset>

        <fieldset>
          <div id="rel_gnr_list" class="entry">
            <label for="rel_gnr_list" class="fixedwidth">RELATED GENRES: <?php echo error_for('rel_gnr_nm_array_excss') ?><?php echo error_for('rel_gnr_empty') ?><?php echo error_for('rel_gnr_dplct') ?><?php echo error_for('rel_gnr_nm_excss_lngth') ?><?php echo error_for('rel_gnr_nm') ?><?php echo error_for('rel_gnr_nonexst') ?><?php echo error_for('rel_gnr_id_mtch') ?><?php echo error_for('rel_gnr_inv_comb') ?></label>
            <input type="text" name="rel_gnr_list" id="rel_gnr_list" value="<?php echo $rel_gnr_list; ?>" class="entryfield <?php echo errorfield('rel_gnr_nm_array_excss') ?> <?php echo errorfield('rel_gnr_empty') ?> <?php echo errorfield('rel_gnr_dplct') ?> <?php echo errorfield('rel_gnr_nm_excss_lngth') ?> <?php echo errorfield('rel_gnr_nm') ?> <?php echo errorfield('rel_gnr_nonexst') ?> <?php echo errorfield('rel_gnr_id_mtch') ?> <?php echo errorfield('rel_gnr_inv_comb') ?>"/>
            <h6>List genre(s) of which this genre is a sub-genre.</br>
            - Separate multiple entries using double comma [,,].</br>
            English drama: British drama / European drama</br>
            Brazilian drama: South American drama</br>
            Jacobean revenge tragedy: Jacobean theatre</br>
            Future history: History</h6>
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
          <input type="hidden" name="gnr_id" value="<?php echo $gnr_id; ?>"/>
          <input type="submit" name="edit" value="Submit" class="button"/>
          <input type="submit" name="edit" value="Delete" class="button"/>
        </div>
      </form>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>