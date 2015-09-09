<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (awards) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <div id="results">
        <div class="
        <?php if(isset($_SESSION['successclass'])) {echo $_SESSION['successclass']; unset($_SESSION['successclass']);} ?>">
        <?php if(isset($_SESSION['message'])) {echo $_SESSION['message']; unset($_SESSION['message']);} ?>
        </div>

        <h4>AWARDS:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(!empty($awrd_yrs))
        { ?>
        <div id="awrds"><table class="credits">
        <tr><th colspan="3">Years held</th></tr>
        <?php $rowclass=0;
        foreach($awrd_yrs as $awrd_yr): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $awrd_yr['awrd_nm_yr']; ?></td>
          <td class="prdcol2"><?php echo $awrd_yr['thtr']; ?></td>
          <td class="prdcol4"><?php echo $awrd_yr['awrd_dt']; ?></td>
        </tr>
        <?php $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($ctgrys))
        { ?>
        <div id="ctgrys"><table class="credits">
        <tr><th colspan="2">Categories</th></tr>
        <?php $rowclass=0;
        foreach($ctgrys as $ctgry): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol3"><?php echo $ctgry['ctgry']; ?></td>
          <td class="prdcol4"><?php echo $ctgry['ctgry_dts']; ?></td>
        </tr>
        <?php if($ctgry['ctgry_alt_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">(Also as: <?php echo $ctgry['ctgry_alt_nm']; ?>)</td>
        </tr>
        <?php } ?>
        <?php $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="awrds_id" value="<?php echo $awrds_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>