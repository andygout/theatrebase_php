<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (format) | TheatreBase</title>
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

        <h4>FORMAT:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(!empty($mats))
        { ?>
        <div id="mats"><table class="credits">
        <tr><th colspan="1">Materials of this format</th></tr>
        <?php $rowclass=0;
        foreach($mats as $mat): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $mat; ?></td>
        </tr>
        <?php $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($src_mat_prds))
        { ?>
        <div id="src_mat_prds"><table class="credits">
        <tr><th colspan="3">Productions that use this format of material as source material</th></tr>
        <?php $rowclass=0;
        foreach($src_mat_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        } if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($src_mat_pts))
        { ?>
        <div id="src_mat_pts"><table class="credits">
        <tr><th colspan="3">Playtexts that use this format of material as source material</th></tr>
        <?php $rowclass=0;
        foreach($src_mat_pts as $pt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';
        }
        if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
        <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes (with this format as source material):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($pt['sg_pts'] as $pt): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';
        } endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="frmt_id" value="<?php echo $frmt_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>