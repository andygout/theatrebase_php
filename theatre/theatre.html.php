<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (theatre) | TheatreBase</title>
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

        <h4>THEATRE:</h4>
        <h1><?php echo $thtr_nm.$sbthtr_nm; ?></h1>
        <h2><?php echo $thtr_lctn; ?></h2>

        <?php if(!preg_match('/TBC$/', $thtr_fll_nm))
        {
          if(!empty($thtrs) || !empty($thtrs_clsd))
          { ?>
          <div id="thtrs" class="box"><table class="overview">
          <?php if(!empty($thtrs)) {$i=0; $thtr_cnt=count($thtrs); ?><tr><td class="ovrvwcol1">Part of:</td>
          <td><?php foreach($thtrs as $thtr): echo $thtr['thtr'];
          if(!empty($thtr['thtr_sbsq_nms'])) {echo ' (subsequently named: '.implode(' / ', $thtr['thtr_sbsq_nms']).')';}
          if(!empty($thtr['thtr_prvs_nms'])) {echo ' (previously named: '.implode(' / ', $thtr['thtr_prvs_nms']).')';}
          if($i<$thtr_cnt-1) {echo '</br>';} $i++; endforeach; ?></td></tr><?php }
          if(!empty($thtrs_clsd)) {$i=0; $thtr_clsd_cnt=count($thtrs_clsd); ?><tr><td class="ovrvwcol1">Part of (closed):</td>
          <td><?php foreach($thtrs_clsd as $thtr_clsd): echo $thtr_clsd['thtr_clsd']; if(!empty($thtr_clsd['thtr_clsd_prvs_nm'])) {echo ' (previously named: '.implode(' / ', $thtr_clsd['thtr_clsd_prvs_nm']).')';}
          if($i<$thtr_clsd_cnt-1) {echo '</br>';} $i++; endforeach; ?></td></tr><?php } ?>
          </table></div></br>
          <?php }

          if(!empty($sbthtrs) || !empty($sbthtrs_clsd))
          { ?>
          <div id="sbthtrs" class="box"><table class="overview">
          <?php if(!empty($sbthtrs)) {$i=0; $sbthtr_cnt=count($sbthtrs); ?><tr><td class="ovrvwcol1">Comprises:</td>
          <td><?php foreach($sbthtrs as $sbthtr): echo $sbthtr['sbthtr'];
          if(!empty($sbthtr['sbthtr_sbsq_nms'])) {echo ' (subsequently named: '.implode(' / ', $sbthtr['sbthtr_sbsq_nms']).')';}
          if(!empty($sbthtr['sbthtr_prvs_nms'])) {echo ' (previously named: '.implode(' / ', $sbthtr['sbthtr_prvs_nms']).')';}
          if($i<$sbthtr_cnt-1) {echo '</br>';} $i++; endforeach; ?></td></tr><?php }
          if(!empty($sbthtrs_clsd)) {$i=0; $sbthtr_clsd_cnt=count($sbthtrs_clsd); ?><tr><td class="ovrvwcol1">Comprises (closed):</td>
          <td><?php foreach($sbthtrs_clsd as $sbthtr_clsd): echo $sbthtr_clsd['sbthtr_clsd']; if(!empty($sbthtr_clsd['sbthtr_clsd_prvs_nm'])) {echo ' (previously named: '.implode(' / ', $sbthtr_clsd['sbthtr_clsd_prvs_nm']).')';}
          if($i<$sbthtr_clsd_cnt-1) {echo '</br>';} $i++; endforeach; ?></td></tr><?php } ?>
          </table></div></br>
          <?php }

          if($thtr_adrs || $thtr_cpcty || $thtr_opn_dt || $thtr_cls_dt || !empty($sbsqs) || !empty($prvss))
          { ?>
          <div id="dtls" class="box"><table class="overview">
          <?php if($thtr_adrs) { ?><tr><td class="ovrvwcol1">Address:</td><td><?php echo $thtr_adrs; ?></td></tr><?php }
          if($thtr_cpcty) { ?><tr><td class="ovrvwcol1">Capacity:</td><td><?php echo $thtr_cpcty; ?></td></tr><?php }
          if($thtr_cls_dt) { ?><tr><td class="ovrvwcol1">Closed:</td><td><?php echo $thtr_cls_dt; ?></td></tr><?php }
          if($thtr_opn_dt) { ?><tr><td class="ovrvwcol1">Opened:</td><td><?php echo $thtr_opn_dt; ?></td></tr><?php }
          if(!empty($sbsqs)) { ?><tr><td class="ovrvwcol1">Subsequently named:</td><td><?php echo implode('</br>', $sbsqs); ?></td></tr><?php }
          if(!empty($sbsqs) || !empty($prvss)) { if($thtr_nm_dt) { ?><tr><td class="ovrvwcol1">Named:</td><td><?php echo $thtr_fll_nm.$thtr_nm_dt; ?></td></tr><?php }}
          if(!empty($prvss)) { ?><tr><td class="ovrvwcol1">Previously named:</td><td><?php echo implode('</br>', $prvss); ?></td></tr><?php } ?>
          </table></div></br>
          <?php }

          if($lctn_lnk_nm || !empty($thtr_typs) || !empty($thtr_comps))
          { ?>
          <div id="clssfctn" class="box"><table class="overview">
          <?php if($lctn_lnk_nm) { ?><tr><td class="ovrvwcol1">Location:</td><td><?php echo $lctn_lnk_nm;
          if(!empty($rel_lctns)) {echo ' ('.implode(' / ', $rel_lctns).')';} ?></td></tr><?php }
          if(!empty($thtr_typs)) { ?><tr><td class="ovrvwcol1">Type:</td><td><?php echo implode(' / ', $thtr_typs); ?></td></tr><?php }
          if(!empty($thtr_comps)) { ?><tr><td class="ovrvwcol1">Company:</td><td><?php echo implode(' / ', $thtr_comps); ?></td></tr><?php } ?>
          </table></div></br>
          <?php }

          if(!empty($sbsqads) || !empty($prvsads))
          { ?>
          <div id="prvs_sbsq_adrs" class="box"><table class="overview">
          <?php if(!empty($sbsqads)) {$i=0; $sbsqads_cnt=count($sbsqads); foreach($sbsqads as $sbsqad): ?>
          <tr><td class="ovrvwcol1"><?php if($i==0) { ?>Subsequently located:<?php } ?></td>
          <td><?php echo $sbsqad['sbsqad_thtr']; if(!empty($sbsqad['thtr_sbsqad_prvs_nms'])) {echo ' (previously named: '.implode(' / ', $sbsqad['thtr_sbsqad_prvs_nms']).')';}
          echo $sbsqad['sbsqad_thtr_adrs']; if($i<$sbsqads_cnt-1) {echo '</br>';} $i++; ?></td></tr><?php endforeach; }
          if(!empty($prvsads)) {$i=0; $prvsads_cnt=count($prvsads); foreach($prvsads as $prvsad): ?>
          <tr><td class="ovrvwcol1"><?php if($i==0) { ?>Previously located:<?php } ?></td>
          <td><?php echo $prvsad['prvsad_thtr']; if(!empty($prvsad['thtr_prvsad_prvs_nms'])) {echo ' (previously named: '.implode(' / ', $prvsad['thtr_prvsad_prvs_nms']).')';}
          echo $prvsad['prvsad_thtr_adrs']; if($i<$prvsads_cnt-1) {echo '</br>';} $i++; ?></td></tr><?php endforeach; } ?>
          </table></div></br>
          <?php }

          if(!empty($prds))
          { ?>
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
          } if($prd['prd_thtr_nt']) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3"><em>▪ <?php echo $prd['prd_thtr_nt']; ?></em></td>
          </tr>
          <?php }
          if(!empty($prd['sg_prds'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
          <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (in this theatre):';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($prd['sg_prds'] as $prd): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
          } if($prd['prd_thtr_nt']) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3"><em>▪ <?php echo $prd['prd_thtr_nt']; ?></em></td>
          </tr>
          <?php }
          endforeach; }
          $rowclass=1 - $rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($awrds))
          { ?>
          <div id="awrds"><table class="credits">
          <tr><th colspan="3">Awards</th></tr>
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
          <?php }
        } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="thtr_id" value="<?php echo $thtr_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>