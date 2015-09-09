<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (course type) | TheatreBase</title>
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
        <h4>COURSE TYPE:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <div id="crs_schl_nm" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Drama school:</td><td><?php echo $crs_schl_nm; ?></td></tr>
        </table></div></br>

        <?php if(!empty($comps)) { ?>
        <div id="comps" class="box"><table class="overview">
        <?php
        if(!empty($comps)) { ?><tr><td class="ovrvwcol1">Other schools:</td><td><?php echo implode(' / ', $comps); ?></td></tr><?php } ?>
        </table></div></br>
        <?php } ?>

        <?php if(!empty($crss))
        { ?>
        <div id="crss"><table class="credits">
        <tr><th colspan="2">Years course has been run</th></tr>
        <?php $rowclass=0;
        foreach($crss as $crs): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol3"><?php echo $crs['crs_nm']; ?></td>
          <td class="prdcol4"><?php echo $crs['crs_dts']; ?></td>
        </tr>
        <?php $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="crs_typ_id" value="<?php echo $crs_typ_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>