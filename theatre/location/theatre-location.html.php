<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (location - theatres) | TheatreBase</title>
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
        <h4>LOCATION<?php echo $lctn_exp; ?> (theatres):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(!empty($rel_lctn_cnt)) { ?>
        <div id="dscrptn" class="box"><table class="overview">
        <?php
        if(!empty($lnks)) { ?><tr><td class="ovrvwcol1">Link<?php if(count($lnk_cnt)>1) {echo 's';} ?> to:</td><td><?php echo implode('</br>', $lnks); ?></td></tr><?php }
        if(!empty($rel_lctns2)){ ?><tr><td class="ovrvwcol1">Part of:</td><td><?php echo implode(' / ', $rel_lctns2); ?></td></tr><?php }
        if(!empty($rel_lctns2_exp)){ ?><tr><td class="ovrvwcol1">Part of (pre-existing):</td><td><?php echo implode(' / ', $rel_lctns2_exp); ?></td></tr><?php }
        if(!empty($rel_lctns1)){ ?><tr><td class="ovrvwcol1">Comprises:</td><td><?php echo implode(' / ', $rel_lctns1); ?></td></tr><?php }
        if(!empty($rel_lctns1_exp)) { ?><tr><td class="ovrvwcol1">Comprises</br>(pre-existing):</td><td><?php echo implode(' / ', $rel_lctns1_exp); ?></td></tr><?php }
        if($lctn_dt) { ?><tr><td class="ovrvwcol1">Dates of existence:</td><td><?php echo $lctn_dt; ?></td></tr><?php }
        if(!empty($sbsq_lctns)) { ?><tr><td class="ovrvwcol1">Subsequently:</td><td><?php echo implode(' / ', $sbsq_lctns); ?></td></tr><?php }
        if(!empty($sbsq_lctns_prt_of)) { ?><tr><td class="ovrvwcol1">Subsequently part of:</td><td><?php echo implode(' / ', $sbsq_lctns_prt_of); ?></td></tr><?php }
        if(!empty($sbsq_lctns_cmprs)) { ?><tr><td class="ovrvwcol1">Succeeded by:</td><td><?php echo implode(' / ', $sbsq_lctns_cmprs); ?></td></tr><?php }
        if(!empty($prvs_lctns)) { ?><tr><td class="ovrvwcol1">Previously:</td><td><?php echo implode(' / ', $prvs_lctns); ?></td></tr><?php }
        if(!empty($prvs_lctns_prt_of)) { ?><tr><td class="ovrvwcol1">Previously part of:</td><td><?php echo implode(' / ', $prvs_lctns_prt_of); ?></td></tr><?php }
        if(!empty($prvs_lctns_cmprs)) { ?><tr><td class="ovrvwcol1">Preceded by:</td><td><?php echo implode(' / ', $prvs_lctns_cmprs); ?></td></tr><?php }
        ?>
        </table></div></br>
        <?php }

        if(!empty($thtrs))
        { ?>
        <div id="thtrs"><table class="credits">
        <tr><th colspan="1">Theatres</th></tr>
        <?php $rowclass=0;
        foreach($thtrs as $thtr): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $thtr['thtr'];
          if(!empty($thtr['thtr_sbsq_nms'])) {echo ' (Subsequently named: '.implode(' / ', $thtr['thtr_sbsq_nms']).')';}
          if(!empty($thtr['thtr_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $thtr['thtr_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php if($k>0) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="charcol5" colspan="5">▪ <em><?php echo $thtr['lctn_nm']; ?></em></td>
        </tr>
        <?php }
        if(!empty($thtr['sbthtrs'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5">Comprises:</td></tr>
        <?php foreach($thtr['sbthtrs'] as $sbthtr): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $sbthtr['sbthtr'];
          if(!empty($sbthtr['sbthtr_sbsq_nms'])) {echo ' (Subsequently named: '.implode(' / ', $sbthtr['sbthtr_sbsq_nms']).')';}
          if(!empty($sbthtr['sbthtr_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $sbthtr['sbthtr_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php endforeach; }
        $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($thtrs_clsd))
        { ?>
        <div id="thtrs_clsd"><table class="credits">
        <tr><th colspan="1">Theatres (closed)</th></tr>
        <?php $rowclass=0;
        foreach($thtrs_clsd as $thtr_clsd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $thtr_clsd['thtr'];
          if(!empty($thtr_clsd['thtr_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $thtr_clsd['thtr_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php if($m>0) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="charcol5" colspan="5">▪ <em><?php echo $thtr_clsd['lctn_nm']; ?></em></td>
        </tr>
        <?php }
        if(!empty($thtr_clsd['sbthtrs'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5">
        <?php if($thtr_clsd['sbthtr_cnt']>count($thtr_clsd['sbthtrs'])) {echo 'Includes (closed):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($thtr_clsd['sbthtrs'] as $sbthtr): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $sbthtr['sbthtr'];
          if(!empty($sbthtr['sbthtr_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $sbthtr['sbthtr_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php endforeach; } ?>
        <?php $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(empty($thtrs) && empty ($thtrs_clsd)) { ?></br><h5><em>NO THEATRES LISTED AT THIS LOCATION</em></h5><?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="lctn_id" value="<?php echo $lctn_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>