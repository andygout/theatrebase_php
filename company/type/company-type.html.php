<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (company type) | TheatreBase</title>
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
        <h4>COMPANY TYPE:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(!empty($comps))
        { ?>
        <div id="comps"><table class="credits">
        <tr><th colspan="1">Companies</th></tr>
        <?php $rowclass=0;
        foreach($comps as $comp): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>"><td class="prdcol5"><?php echo $comp['comp'];
          if(!empty($comp['comp_sbsq_nms'])) {echo ' (Subsequently named: '.implode(' / ', $comp['comp_sbsq_nms']).')';}
          if(!empty($comp['comp_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $comp['comp_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php $rowclass=1 - $rowclass; endforeach; ?>
        </table></div>
        <?php } ?>

        <?php if(!empty($comps_dslvd))
        { ?>
        <div id="comps_dslvd"><table class="credits">
        <tr><th colspan="1">Companies (dissolved)</th></tr>
        <?php $rowclass=0;
        foreach($comps_dslvd as $comp_dslvd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>"><td class="prdcol5"><?php echo $comp_dslvd['comp_dslvd'];
          if(!empty($comp_dslvd['comp_dslvd_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $comp_dslvd['comp_dslvd_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php $rowclass=1 - $rowclass; endforeach; ?>
        </table></div>
        <?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="comp_typ_id" value="<?php echo $comp_typ_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>