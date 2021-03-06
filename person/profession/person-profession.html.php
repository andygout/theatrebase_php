<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (profession - people) | TheatreBase</title>
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

        <h4>PROFESSION (people):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(($char_lnk) || !empty($rel_profs1) || !empty($rel_profs2)) { ?>
        <div id="dscrptn" class='box'><table class='overview'>
        <?php
        if($char_lnk) { ?><tr><td class="ovrvwcol1">Link to:</td><td><?php echo $char_lnk; ?></td></tr><?php }
        if(!empty($rel_profs2)){ ?><tr><td class="ovrvwcol1">Related professions:</td><td><?php echo implode(' / ', $rel_profs2); ?></td></tr><?php }
        if(!empty($rel_profs1)){ ?><tr><td class="ovrvwcol1">Sub-professions:</td><td><?php echo implode(' / ', $rel_profs1); ?></td></tr><?php }
        ?>
        </table></div></br>
        <?php }

        if(!empty($ppl)) { ?>
        <div id="ppl"><table class="credits">
        <tr><th colspan="1">People</th></tr>
        <?php $rowclass=0;
        foreach($ppl as $prsn): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $prsn['prsn_nm']; ?></td>
        </tr>
        <?php if($k>0 && $prsn['prof_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="charcol5" colspan="5">▪ <em><?php echo $prsn['prof_nm']; ?></em></td>
        </tr>
        <?php } $rowclass=1-$rowclass; endforeach; ?>
        </table></div>
        <?php } else { ?></br><h5><em>NO PEOPLE CREDITED IN THIS ROLE</em></h5><?php } ?>
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