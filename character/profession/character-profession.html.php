<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (profession - characters) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <div id="results">
        <div class="
          <?php if(isset($_SESSION['successclass'])) { echo $_SESSION['successclass']; unset($_SESSION['successclass']); } ?>">
          <?php if(isset($_SESSION['message'])) { echo $_SESSION['message']; unset($_SESSION['message']); } ?>
        </div>

        <h4>PROFESSION (characters):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(($prsn_lnk) || !empty($rel_profs1) || !empty($rel_profs2)) { ?>
        <div id="dscrptn" class='box'><table class='overview'>
        <?php
        if($prsn_lnk) { ?><tr><td class="ovrvwcol1">Link to:</td><td><?php echo $prsn_lnk; ?></td></tr><?php }
        if(!empty($rel_profs2)){ ?><tr><td class="ovrvwcol1">Related professions:</td><td><?php echo implode(' / ', $rel_profs2); ?></td></tr><?php }
        if(!empty($rel_profs1)){ ?><tr><td class="ovrvwcol1">Sub-professions:</td><td><?php echo implode(' / ', $rel_profs1); ?></td></tr><?php }
        ?>
        </table></div></br>
        <?php }

        if(!empty($chars))
        { ?>
        <div id="chars"><table class="credits">
        <tr><th colspan="5">Characters</th></tr>
        <?php $rowclass=0;
        foreach($chars as $char): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="charcol1"><?php echo $char['char_nm'].$char['char_amnt']; ?></td>
          <td class="charcol2"><?php echo $char['char_sx']; ?></td>
          <td class="charcol3"><?php echo $char['char_age']; ?></td>
          <td class="charcol4"><?php echo $char['char_dscr']; ?></td>
        </tr>
        <?php if(!empty($char['pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="charcol5" colspan="5"><?php echo implode(' / ', $char['pts']);
          if($char['pt_cnt']>0) {if($char['pt_cnt']==1) {echo ' <em>(and 1 other playtext)</em>';} else {echo ' <em>(and '.$char['pt_cnt'].' other playtexts)</em>';}} ?></td>
        </tr>
        <?php } if($k>0 && $char['prof_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="charcol5" colspan="5">▪ <em><?php echo $char['prof_nm']; ?></em></td>
        </tr>
        <?php } $rowclass=1-$rowclass; endforeach; ?>
        </table></div>
        <?php } else { ?></br><h5><em>NO CHARACTERS LISTED WITH THIS PROFESSION</em></h5><?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="prof_id" value="<?php echo $prof_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>