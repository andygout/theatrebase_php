<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>TheatreBase</title>
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

        <h1>Home</h1>

        <?php if(!empty($all_prds)) { ?>
        <div id="all_prds"><table class="credits">
        <tr><th colspan="3">Productions</th></tr>
        <?php $rowclass=0;
        foreach($all_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">Comprises:</td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        endforeach;}
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($prvnxtwk_prds))
        { ?>
        <div id="prvnxtwk_prds"><table class="credits">
        <tr><th colspan="3">Productions previewing in next week</th></tr>
        <?php $rowclass=0;
        foreach($prvnxtwk_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (previewing in the next week):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        endforeach;}
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($crrntprv_prds))
        { ?>
        <div id="crrntprv_prds"><table class="credits">
        <tr><th colspan="3">Productions currently previewing</th></tr>
        <?php $rowclass=0;
        foreach($crrntprv_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (currently previewing):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        endforeach;}
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($opnnxtwk_prds))
        { ?>
        <div id="opnnxtwk_prds"><table class="credits">
        <tr><th colspan="3">Productions opening in next week (refer to production page for opening date)</th></tr>
        <?php $rowclass=0;
        foreach($opnnxtwk_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (opening in next week):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($crrnt_prds))
        { ?>
        <div id="crrnt_prds"><table class="credits">
        <tr><th colspan="3">Productions currently playing</th></tr>
        <?php $rowclass=0;
        foreach($crrnt_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (currently playing):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($clsnxtwk_prds))
        { ?>
        <div id="clsnxtwk_prds"><table class="credits">
        <tr><th colspan="3">Productions closing in next week</th></tr>
        <?php $rowclass=0;
        foreach($clsnxtwk_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (closing in next week):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
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
        if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">Comprises:</td></tr>
        <?php foreach($pt['sg_pts'] as $pt): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
        endforeach;}
        if(!empty($pt['wrks_sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><?php echo 'Comprises: '.implode(' / ', $pt['wrks_sg_pts']); ?></td>
        </tr>
        <?php }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php } ?>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>