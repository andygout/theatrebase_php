<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (setting: location - playtexts) | TheatreBase</title>
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
        <h4>SETTING: LOCATION<?php echo $lctn_exp_fctn; ?> (playtexts):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(!empty($rel_lctn_cnt)) { ?>
        <div id="dscrptn" class="box"><table class="overview">
        <?php
        if(!empty($lnks)) { ?><tr><td class="ovrvwcol1">Link<?php if(count($lnk_cnt)>1) {echo 's';} ?> to:</td><td><?php echo implode('</br>', $lnks); ?></td></tr><?php }
        if(!empty($rel_lctns2)){ ?><tr><td class="ovrvwcol1">Part of:</td><td><?php echo implode(' / ', $rel_lctns2); ?></td></tr><?php }
        if(!empty($rel_lctns2_exp)){ ?><tr><td class="ovrvwcol1">Part of (pre-existing):</td><td><?php echo implode(' / ', $rel_lctns2_exp); ?></td></tr><?php }
        if(!empty($rel_lctns2_fctn)){ ?><tr><td class="ovrvwcol1">Part of (fictional):</td><td><?php echo implode(' / ', $rel_lctns2_fctn); ?></td></tr><?php }
        if(!empty($rel_lctns1)){ ?><tr><td class="ovrvwcol1">Comprises:</td><td><?php echo implode(' / ', $rel_lctns1); ?></td></tr><?php }
        if(!empty($rel_lctns1_exp)) { ?><tr><td class="ovrvwcol1">Comprises</br>(pre-existing):</td><td><?php echo implode(' / ', $rel_lctns1_exp); ?></td></tr><?php }
        if(!empty($rel_lctns1_fctn)) { ?><tr><td class="ovrvwcol1">Comprises (fictional):</td><td><?php echo implode(' / ', $rel_lctns1_fctn); ?></td></tr><?php }
        if($lctn_dt) { ?><tr><td class="ovrvwcol1">Dates of existence:</td><td><?php echo $lctn_dt; ?></td></tr><?php }
        if(!empty($sbsq_lctns)) { ?><tr><td class="ovrvwcol1">Subsequently:</td><td><?php echo implode(' / ', $sbsq_lctns); ?></td></tr><?php }
        if(!empty($sbsq_lctns_prt_of)) { ?><tr><td class="ovrvwcol1">Subsequently part of:</td><td><?php echo implode(' / ', $sbsq_lctns_prt_of); ?></td></tr><?php }
        if(!empty($sbsq_lctns_cmprs)) { ?><tr><td class="ovrvwcol1">Succeeded by:</td><td><?php echo implode(' / ', $sbsq_lctns_cmprs); ?></td></tr><?php }
        if(!empty($sbsq_lctns_fctn)) { ?><tr><td class="ovrvwcol1">Subsequently (fictional):</td><td><?php echo implode(' / ', $sbsq_lctns_fctn); ?></td></tr><?php }
        if(!empty($sbsq_lctns_fctn_prt_of)) { ?><tr><td class="ovrvwcol1">Subsequently part of (fictional):</td><td><?php echo implode(' / ', $sbsq_lctns_fctn_prt_of); ?></td></tr><?php }
        if(!empty($sbsq_lctns_fctn_cmprs)) { ?><tr><td class="ovrvwcol1">Succeeded by (fictional):</td><td><?php echo implode(' / ', $sbsq_lctns_fctn_cmprs); ?></td></tr><?php }
        if(!empty($prvs_lctns)) { ?><tr><td class="ovrvwcol1">Previously:</td><td><?php echo implode(' / ', $prvs_lctns); ?></td></tr><?php }
        if(!empty($prvs_lctns_prt_of)) { ?><tr><td class="ovrvwcol1">Previously part of:</td><td><?php echo implode(' / ', $prvs_lctns_prt_of); ?></td></tr><?php }
        if(!empty($prvs_lctns_cmprs)) { ?><tr><td class="ovrvwcol1">Preceded by:</td><td><?php echo implode(' / ', $prvs_lctns_cmprs); ?></td></tr><?php }
        if(!empty($prvs_lctns_fctn)) { ?><tr><td class="ovrvwcol1">Previously (fictional):</td><td><?php echo implode(' / ', $prvs_lctns_fctn); ?></td></tr><?php }
        if(!empty($prvs_lctns_fctn_prt_of)) { ?><tr><td class="ovrvwcol1">Previously part of (fictional):</td><td><?php echo implode(' / ', $prvs_lctns_fctn_prt_of); ?></td></tr><?php }
        if(!empty($prvs_lctns_fctn_cmps)) { ?><tr><td class="ovrvwcol1">Preceded by (fictional):</td><td><?php echo implode(' / ', $prvs_lctns_fctn_cmprs); ?></td></tr><?php }
        ?>
        </table></div></br>
        <?php }

        if(!empty($pts))
        { ?>
        <div id="pts"><table class="credits">
        <tr><th colspan="3">Playtexts</th></tr>
        <?php $rowclass=0;
        foreach($pts as $pt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
        if(!empty($pt['cntr_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_cntr_dsply.inc.html.php';}
        if($k>0 && !empty($pt['lctns'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php echo implode(array_unique($pt['lctns']), ' / '); ?></em></td>
        </tr>
        <?php }
        if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
        <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes (with this location as setting):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($pt['sg_pts'] as $pt): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';
        } if($k>0) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php echo implode(array_unique($pt['lctns']), ' / '); ?></em></td>
        </tr>
        <?php }
        endforeach; }
        if(!empty($pt['wrks_sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><?php echo 'Comprised of: '.implode(' / ', $pt['wrks_sg_pts']); ?></td>
        </tr>
        <?php }
        $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php } else { ?></br><h5><em>NO PLAYTEXTS YET LISTED FOR THIS SETTING (LOCATION)</em></h5><?php } ?>
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