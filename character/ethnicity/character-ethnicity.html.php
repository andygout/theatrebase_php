<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (ethnicity - characters) | TheatreBase</title>
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

        <h4>ETHNICITY (characters):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(($prsn_lnk) || !empty($rel_ethns1) || !empty($rel_ethns2)) { ?>
        <div id="dscrptn" class='box'><table class='overview'>
        <?php
        if($prsn_lnk) { ?><tr><td class="ovrvwcol1">Link to:</td><td><?php echo $prsn_lnk; ?></td></tr><?php }
        if(!empty($rel_ethns2)){ ?><tr><td class="ovrvwcol1">Related ethnicities:</td><td><?php echo implode(' / ', $rel_ethns2); ?></td></tr><?php }
        if(!empty($rel_ethns1)){ ?><tr><td class="ovrvwcol1">Sub-ethnicities:</td><td><?php echo implode(' / ', $rel_ethns1); ?></td></tr><?php }
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
        <?php } if($k>0 && $char['ethn_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="charcol5" colspan="5">▪ <em><?php echo $char['ethn_nm']; ?></em></td>
        </tr>
        <?php } $rowclass=1-$rowclass; endforeach; ?>
        </table></div>
        <?php } else { ?></br><h5><em>NO CHARACTERS LISTED OF THIS ETHNICITY</em></h5><?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="ethn_id" value="<?php echo $ethn_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>