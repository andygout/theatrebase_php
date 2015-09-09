<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (course) | TheatreBase</title>
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

        <h4>COURSE:</h4>

        <h1><?php echo $pagetitle; ?></h1>

        <div id="clssfctn" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Drama school:</td><td><?php echo $crs_schl; ?></td></tr>
        <tr><td class="ovrvwcol1">Course type:</td><td><?php echo $crs_typ; ?></td></tr>
        <tr><td class="ovrvwcol1">Course ends:</td><td><?php if($crs_yr_end) {echo $crs_yr_end;} else {echo $crs_yr_strt;} ?></td></tr>
        </table></div></br>

        <?php if($crs_dt_strt || $crs_dt_end) { ?>
        <div id="dts"><h5>Dates</h5>
        <table class="prod1">
          <?php if($crs_dt_strt) { ?><tr><td><b>Course start date:</b></td><td><?php echo $crs_dt_strt; ?></td></tr><?php }
          if($crs_dt_end) { ?><tr><td><b>Course end date:</b></td><td><?php echo $crs_dt_end; ?></td></tr><?php } ?>
        </table></div>
        <?php }

        if(!empty($cdntr_rls)) { ?>
        <div id="cdntr_rls"><h5>Course coordinators</h5><table class="prod1">
        <?php $h=0; foreach($cdntr_rls as $cdntr_rl): $h+=count($cdntr_rl['cdntrs']) ?>
        <tr><?php if(!empty($cdntr_rl['cdntrs'])) {$i=0; $ppl=count($cdntr_rl['cdntrs']); ?>
        <td><?php echo $cdntr_rl['cdntr_rl'].' '; ?></td>
        <td>.....</td>
        <td><?php foreach($cdntr_rl['cdntrs'] as $cdntr):
        echo $cdntr['comp_nm']; if($i<$ppl-2) {echo ', ';} elseif($i==$ppl-2) {echo ' and ';} $i++; endforeach;} endforeach; ?>
        </td></tr>
        </table>
        <?php foreach($cdntr_rls as $cdntr_rl): foreach($cdntr_rl['cdntrs'] as $cdntr): if(!empty($cdntr['comp_rls'])) { ?>
        <table class="prod3">
        <?php if($h>1) { ?><tr><td colspan="3"><u>For <?php echo $cdntr['comp_nm_pln']; ?>:</u></td></tr><?php }
        foreach($cdntr['comp_rls'] as $comp_rl): ?>
        <tr><td><?php echo $comp_rl['cdntr_comprl']; ?></td><td>.....</td><td><?php $j=0; $compppl=count($comp_rl['cdntrcomp_ppl']);
        foreach($comp_rl['cdntrcomp_ppl'] as $cdntrcomp_prsn): echo $cdntrcomp_prsn;
        if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; ?></td></tr>
        <?php endforeach;} endforeach; endforeach; ?>
        </table></div>
        <?php }

        if(!empty($stff_ppl))
        { ?>
        <div id="stff_ppl"><h5>Course staff</h5>
        <table class="prod1">
        <?php foreach($stff_ppl as $stff_prsn): ?>
        <tr><td><?php echo $stff_prsn['prsn_nm']; ?></td><td>.....</td><td><?php echo $stff_prsn['stff_prsn_rl']; ?></td></tr>
        <?php endforeach; ?>
        </table></div>
        <?php }

        if(!empty($stdnt_ppl))
        { ?>
        <div id="stdnt_ppl"><h5>Students</h5>
        <table class="prod1">
        <?php foreach($stdnt_ppl as $stdnt_prsn): ?>
        <tr><td><?php echo $stdnt_prsn['prsn_nm']; ?></td><td>.....</td><td><?php echo $stdnt_prsn['stdnt_prsn_rl']; ?></td></tr>
        <?php endforeach; ?>
        </table></div>
        <?php }

        if(!empty($prds)) { ?>
        <div id="prds"><table class="credits">
        <tr><th colspan="3">Productions</th></tr>
        <?php $rowclass=0;
        foreach($prds as $prd): ?>
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
        <?php if(!empty($prd['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        } endforeach; }
        $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="crs_id" value="<?php echo $crs_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>