<!DOCTYPE html>
<html>
<head>
  <title>Courses: <?php echo $pagetitle; ?> | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <div id="results">
        <h4>YEAR (courses):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if($crs_yr_nxt || $crs_yr_lst) { ?>
        <div id="crs_yr_nxt_lst" class="box">
          <table class="overview">
            <?php if($crs_yr_nxt) { ?>
            <tr><td class="ovrvwcol1">Following year:</td><td><?php echo $crs_yr_nxt; ?></td></tr>
            <?php } if($crs_yr_lst) { ?>
            <tr><td class="ovrvwcol1">Preceding year:</td><td><?php echo $crs_yr_lst; ?></td></tr>
            <?php } ?>
          </table>
        </div>
        <?php } ?>

        <?php if(!empty($yr_crss))
        { ?>
        <div id="yr_crss"><table class="credits">
        <tr><th colspan="2">Courses ending in this year</th></tr>
        <?php $rowclass=0;
        foreach($yr_crss as $yr_crs): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol3"><?php echo $yr_crs['crs_nm']; ?></td>
          <td class="prdcol4"><?php echo $yr_crs['crs_dts']; ?></td>
        </tr>
        <?php $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php } else { ?></br><h5><em>NO COURSES ENDING IN THIS YEAR YET LISTED</em></h5><?php } ?>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>