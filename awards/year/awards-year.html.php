<!DOCTYPE html>
<html>
<head>
  <title>Awards: <?php echo $pagetitle; ?> | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <div id="results">

        <h4>YEAR (awards):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if($awrd_yr_nxt || $awrd_yr_lst) { ?>
        <div id="prcd-fllw-yr" class="box">
          <table class="overview">
            <?php
            if($awrd_yr_nxt) { ?><tr><td class="ovrvwcol1">Following year:</td><td><?php echo $awrd_yr_nxt; ?></td></tr><?php }
            if($awrd_yr_lst) { ?><tr><td class="ovrvwcol1">Preceding year:</td><td><?php echo $awrd_yr_lst; ?></td></tr><?php } ?>
          </table>
        </div></br>
        <?php }

        if(!empty($awrds))
        { ?>
        <div id="awrds"><table class="credits">
        <tr><th colspan="3">Awards held in this year</th></tr>
        <?php $rowclass=0;
        foreach($awrds as $awrd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $awrd['awrd_nm_yr']; ?></td>
          <td class="prdcol2"><?php echo $awrd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $awrd['awrd_dt']; ?></td>
        </tr>
        <?php $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div></br>
        <?php } else { ?></br><h5><em>NO AWARDS YET LISTED FOR THIS YEAR</em></h5><?php } ?>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>