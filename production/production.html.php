<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?></title>
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

        <h4><?php echo $prd_clss_dsply; ?>PRODUCTION<?php echo $tr_dsply.$coll_dsply; ?>:</h4>
        <h1><?php echo $pagetitle; ?></h1>
        <h2><?php echo $prd_sbnm; ?></h2>

        <?php if(!empty($wri_rls)) { ?>
        <div id="wris"><h5>Writers</h5><table class="prod2">
        <?php foreach($wri_rls as $wri_rl): ?>
        <tr><td>
        <?php if(!empty($wri_rl['src_mats'])) {$g=0; $h=0; $sms=count($wri_rl['src_mats']); echo $wri_rl['src_mat_rl'].' ';
        foreach($wri_rl['src_mats'] as $src_mat):
        if(!preg_match("/^{$pagetitle}$/i", $src_mat['src_mat_nm'])) {echo 'the '.$src_mat['src_mat_frmt'].', '.$src_mat['src_mat_url'];
        if($h==$sms-2 || ($h==$sms-1 && $wri_rl['wri_rl'])) {echo ',';}}
        else {if($g==0) {echo 'the ';} echo $src_mat['src_mat_frmt_url']; $g++;}
        if($h<$sms-2) {echo ', ';} elseif($h<$sms-1) {echo ' and ';} $h++; endforeach; echo ' ';}
        if(!empty($wri_rl['wris'])) {$i=0; $ppl=count($wri_rl['wris']); echo $wri_rl['wri_rl'].' ';
        foreach($wri_rl['wris'] as $wri):
        if($i>0 && $i<$ppl-1 && !$wri['wri_sb_rl']) {echo ', ';} elseif($i>0 && $i==$ppl-1 && !$wri['wri_sb_rl']) {echo ' and ';}
        echo $wri['wri_sb_rl'].$wri['comp_nm'];
        if(!empty($wri['wricomp_ppl'])) { echo ' ('; $j=0; $compppl=count($wri['wricomp_ppl']); foreach($wri['wricomp_ppl'] as $wricomp_prsn):
        echo $wricomp_prsn['wri_sb_rl'].$wricomp_prsn['prsn_nm'];
        if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ')'; } $i++; endforeach; } ?>
        </td></tr>
        <?php endforeach; ?>
        </table></div><?php }

        if(!empty($prd_vrsns) || !empty($txt_vrsns) || !empty($ctgrys) || !empty($gnrs) || !empty($ftrs)) { ?>
        <div id="clssfctn" class="box"><table class="overview">
        <?php
        if(!empty($prd_vrsns)) { ?><tr><td class="ovrvwcol1">Production Version:</td><td><?php echo implode(' / ', $prd_vrsns); ?></td></tr><?php }
        if(!empty($txt_vrsns)) { ?><tr><td class="ovrvwcol1">Text Version:</td><td><?php echo implode(' / ', $txt_vrsns); ?></td></tr><?php }
        if(!empty($ctgrys)) { ?><tr><td class="ovrvwcol1">Category:</td><td><?php echo implode(' / ', $ctgrys); ?></td></tr><?php }
        if(!empty($gnrs)) {$i=0; $gnr_cnt=count($gnrs); ?><tr><td class="ovrvwcol1">Genre:</td><td>
        <?php foreach($gnrs as $gnr): echo $gnr['gnr_nm']; if(!empty($gnr['rel_gnrs'])) {echo ' ('.implode(' / ', $gnr['rel_gnrs']).')';}
        if($i<$gnr_cnt-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        if(!empty($ftrs)) { ?><tr><td class="ovrvwcol1">Features:</td><td><?php echo implode(' / ', $ftrs); ?></td></tr><?php } ?>
        </table></div></br>
        <?php } ?>

        <div id ="dts">
        <h5>Dates<?php echo $prd_prv_only; ?></h5>
        <table class="prod1">
        <?php echo $dt_dsply; ?>
        </table>

        <?php if($prd_dt_nt)
        { ?>
        <table class="prod1">
        <tr><td><em><?php echo $prd_dt_nt; ?></em></td></tr>
        </table><?php } ?></div>

        <div id ="<?php echo $thtr_div_id; ?>"><h5><?php echo $thtr_header; ?></h5>
        <table class="prod2">
        <tr><td><?php echo $thtr_dsply ?></td></tr>
        </table></div>

        <?php if($prd_thtr_nt) { ?>
        <table class="prod1">
        <tr><td><em><?php echo $prd_thtr_nt; ?></em></td></tr>
        </table><?php }

        if(!empty($tr_lg_prds))
        { ?>
        <div id="tr_lg_prds"><table class="credits">
        <tr><th colspan="3">Tour Dates:</th></tr>
        <?php $rowclass=0;
        foreach($tr_lg_prds as $tr_lg_prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $tr_lg_prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $tr_lg_prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $tr_lg_prd['prd_dts']; ?></td>
        </tr>
        <?php $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($tr_ov_prds)) { ?>
        <div id="tr_ov_prds"><table class="credits">
        <tr><th colspan="3">Tour Overview (including full tour schedule):</th></tr>
        <?php $rowclass=0;
        foreach($tr_ov_prds as $tr_ov_prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $tr_ov_prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $tr_ov_prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $tr_ov_prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($tr_ov_prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">Includes:</td></tr>
        <?php foreach($tr_ov_prd['sg_prds'] as $sg_prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $sg_prd['sg_prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $sg_prd['sg_thtr']; ?></td>
          <td class="prdcol4"><?php echo $sg_prd['sg_prd_dts']; ?></td>
        </tr>
        <?php endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($coll_sg_sbhdrs))
        { ?>
        <div id="coll_sg_prds"><table class="credits">
        <tr><th colspan="3">Productions of which this collection is comprised:</th></tr>
        <?php $rowclass=0;
        foreach($coll_sg_sbhdrs as $coll_sg_sbhdr):
          if($coll_sg_sbhdr['coll_sbhdr']) {$rowclass=0; ?>
          <tr class="newcredit">
            <td class="prdcol6" colspan="3"><?php echo $coll_sg_sbhdr['coll_sbhdr']; ?></td>
          </tr>
          <?php }
          foreach($coll_sg_sbhdr['coll_sg_prds'] as $coll_sg_prd): ?>
            <tr class="newcredit row<?php echo $rowclass; ?>">
              <td class="prdcol1"><?php echo $coll_sg_prd['prd_nm']; ?></td>
              <td class="prdcol2"><?php echo $coll_sg_prd['thtr']; ?></td>
              <td class="prdcol4"><?php echo $coll_sg_prd['prd_dts']; ?></td>
            </tr>
            <?php if(!empty($coll_sg_prd['wri_rls'])) { ?>
            <tr class="row<?php echo $rowclass; ?>">
              <td class="prdcol5" colspan="3"><em><?php
              $f=0; $rls=count($coll_sg_prd['wri_rls']); foreach($coll_sg_prd['wri_rls'] as $wri_rl):
              if(!empty($wri_rl['src_mats'])) {$g=0; $h=0; $sms=count($wri_rl['src_mats']); echo $wri_rl['src_mat_rl'].' ';
              foreach($wri_rl['src_mats'] as $src_mat):
              if(!preg_match("/^{$coll_sg_prd['prd_nm_pln']}$/i", $src_mat['src_mat_nm'])) {echo 'the '.$src_mat['src_mat_frmt'].', <span style="font-style:normal">'.$src_mat['src_mat_url'].'</span>';
              if($h==$sms-2 || ($h==$sms-1 && $wri_rl['wri_rl'])) {echo ',';}}
              else {if($g==0) {echo 'the ';} echo $src_mat['src_mat_frmt_url']; $g++;}
              if($h<$sms-2) {echo ', ';} elseif($h<$sms-1) {echo ' and ';} $h++; endforeach; echo ' ';}
              if(!empty($wri_rl['wris'])) {$i=0; $ppl=count($wri_rl['wris']); echo $wri_rl['wri_rl'].' ';
              foreach($wri_rl['wris'] as $wri): if($i>0 && $i<$ppl-1 && !$wri['wri_sb_rl']) {echo ', ';} elseif($i>0 && $i==$ppl-1 && !$wri['wri_sb_rl']) {echo ' and ';}
              echo $wri['wri_sb_rl'].$wri['comp_nm'];
              if(!empty($wri['wricomp_ppl'])) { echo ' ('; $j=0; $compppl=count($wri['wricomp_ppl']); foreach($wri['wricomp_ppl'] as $wricomp_prsn):
              echo $wricomp_prsn['wri_sb_rl'].$wricomp_prsn['prsn_nm'];
              if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ')'; }
              $i++; endforeach;} if($f<$rls-1) {echo '</br>';} $f++; endforeach; ?>
              </em></td>
            </tr>
            <?php }
            $rowclass=1-$rowclass;
          endforeach;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($coll_ov_prds))
        { ?>
        <div id="coll_ov_prds"><table class="credits">
        <tr><th colspan="3">Part of (collection):</th></tr>
        <?php $rowclass=0;
        foreach($coll_ov_prds as $coll_ov_prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $coll_ov_prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $coll_ov_prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $coll_ov_prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($coll_ov_prd['wri_rls'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3"><em><?php
          $f=0; $rls=count($coll_ov_prd['wri_rls']); foreach($coll_ov_prd['wri_rls'] as $wri_rl):
          if(!empty($wri_rl['src_mats'])) {$g=0; $h=0; $sms=count($wri_rl['src_mats']); echo $wri_rl['src_mat_rl'].' ';
          foreach($wri_rl['src_mats'] as $src_mat):
          if(!preg_match("/^{$coll_ov_prd['prd_nm_pln']}$/i", $src_mat['src_mat_nm'])) {echo 'the '.$src_mat['src_mat_frmt'].', <span style="font-style:normal">'.$src_mat['src_mat_url'].'</span>';
          if($h==$sms-2 || ($h==$sms-1 && $wri_rl['wri_rl'])) {echo ',';}}
          else {if($g==0) {echo 'the ';} echo $src_mat['src_mat_frmt_url']; $g++;}
          if($h<$sms-2) {echo ', ';} elseif($h<$sms-1) {echo ' and ';} $h++; endforeach; echo ' ';}
          if(!empty($wri_rl['wris'])) {$i=0; $ppl=count($wri_rl['wris']); echo $wri_rl['wri_rl'].' ';
          foreach($wri_rl['wris'] as $wri): if($i>0 && $i<$ppl-1 && !$wri['wri_sb_rl']) {echo ', ';} elseif($i>0 && $i==$ppl-1 && !$wri['wri_sb_rl']) {echo ' and ';}
          echo $wri['wri_sb_rl'].$wri['comp_nm'];
          if(!empty($wri['wricomp_ppl'])) {echo ' ('; $j=0; $compppl=count($wri['wricomp_ppl']); foreach($wri['wricomp_ppl'] as $wricomp_prsn):
          echo $wricomp_prsn['wri_sb_rl'].$wricomp_prsn['prsn_nm'];
          if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ')';}
          $i++; endforeach;} if($f<$rls-1) {echo '</br>';} $f++; endforeach; ?>
          </em></td>
        </tr>
        <?php }
        if($coll_ov_prd['coll_sbhdr']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">Section: <em><?php echo $coll_ov_prd['coll_sbhdr']; ?></em></td>
        </tr>
        <?php }
        if(!empty($coll_ov_prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">Collection also comprises: <?php echo implode(' / ', $coll_ov_prd['sg_prds']); ?></td>
        </tr>
        <?php }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($prdcr_rls)) { ?>
        <div id="prdcrs"><h5>Producers</h5><table class="prod2">
        <?php $g=0; foreach($prdcr_rls as $prdcr_rl): $g+=count($prdcr_rl['prdcrs']) ?>
        <tr><td>
        <?php if(!empty($prdcr_rl['prdcrs'])) {
        $h=0; foreach($prdcr_rl['prdcrs'] as $prdcr): if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$h++;} endforeach;
        $i=0; $ppl=count($prdcr_rl['prdcrs']); echo $prdcr_rl['prdcr_rl'].' ';
        foreach($prdcr_rl['prdcrs'] as $prdcr):
        if($i>0 && $i<$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; ';} else {echo ', ';}}
        elseif($i>0 && $i==$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; and ';} else {echo ' and ';}}
        echo ' '.$prdcr['prdcr_sb_rl'];
        if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$j=0; $ppl_crdt=count($prdcr['prdcrcomp_ppl_crdt']);
        foreach($prdcr['prdcrcomp_ppl_crdt'] as $prdcrcomp_prsn_crdt): echo $prdcrcomp_prsn_crdt['prsn_nm'];
        if($j<$ppl_crdt-2) {echo ', ';} if($j==$ppl_crdt-2) {echo ' and ';} $j++; endforeach; echo ' for ';}
        echo $prdcr['comp_nm']; $i++; endforeach;} endforeach; ?>
        </td></tr>
        </table>
        <?php foreach($prdcr_rls as $prdcr_rl): foreach($prdcr_rl['prdcrs'] as $prdcr): if(!empty($prdcr['comp_rls'])) { ?>
        <table class="prod3">
        <?php if($g>1) { ?><tr><td colspan="3"><u>For <?php echo $prdcr['comp_nm_pln']; ?>:</u></td></tr><?php }
        foreach($prdcr['comp_rls'] as $comp_rl): ?>
        <tr><td><?php echo $comp_rl['prdcr_comprl']; ?></td><td>.....</td><td><?php $j=0; $compppl=count($comp_rl['prdcrcomp_ppl']);
        foreach($comp_rl['prdcrcomp_ppl'] as $prdcrcomp_prsn): echo $prdcrcomp_prsn;
        if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; ?></td></tr>
        <?php endforeach;} endforeach; endforeach; ?>
        </table></div>
        <?php }

        if(!empty($prfs))
        { ?>
        <div id="prfs"><h5>Performers</h5>
        <table class="prod1">
        <?php foreach($prfs as $prf): ?>
        <tr><td><?php echo $prf['prsn_nm']; ?></td><td>.....</td>
        <td><em><?php echo implode('</br>', $prf['prf_rls']); ?></em></td></tr>
        <?php endforeach; ?>
        </table></div>

        <?php if($prd_othr_prts) { ?>
        <div id="prd_othr_prts"><table class="prod1">
        <tr><td><em>Other parts played by members of the company.</em></td></tr>
        </table></div><?php }

        if($prd_cst_nt) { ?>
        <table class="prod1">
        <tr><td><em><?php echo $prd_cst_nt; ?></em></td></tr>
        </table><?php }
        }

        if(!empty($uss))
        { ?>
        <div id="uss"><h5>Understudies</h5>
        <table class="prod1">
        <?php foreach($uss as $us): ?>
        <tr><td><?php echo $us['prsn_nm']; ?></td><td>.....</td>
        <td><em><?php echo implode('</br>', $us['us_rls']); ?></em></td></tr>
        <?php endforeach; ?></table></div><?php }

        if(!empty($mscn_rls)) { ?>
        <div id="mscns"><h5>Musicians</h5><table class="prod1">
        <?php $h=0; foreach($mscn_rls as $mscn_rl): $h+=count($mscn_rl['mscns']) ?>
        <tr><?php if(!empty($mscn_rl['mscns'])) {$i=0; $ppl=count($mscn_rl['mscns']); ?>
        <td><?php echo $mscn_rl['mscn_rl'].' '; ?></td>
        <td>.....</td>
        <td><?php foreach($mscn_rl['mscns'] as $mscn):
        echo $mscn['comp_nm']; if($i<$ppl-2) {echo ', ';} elseif($i==$ppl-2) {echo ' and ';} $i++; endforeach;} endforeach; ?>
        </td></tr>
        </table>
        <?php foreach($mscn_rls as $mscn_rl): foreach($mscn_rl['mscns'] as $mscn): if(!empty($mscn['comp_rls'])) { ?>
        <table class="prod3">
        <?php if($h>1) { ?><tr><td colspan="3"><u>For <?php echo $mscn['comp_nm_pln']; ?>:</u></td></tr><?php }
        foreach($mscn['comp_rls'] as $comp_rl): ?>
        <tr><td><?php echo $comp_rl['mscn_comprl']; ?></td><td>.....</td><td><?php $j=0; $compppl=count($comp_rl['mscncomp_ppl']);
        foreach($comp_rl['mscncomp_ppl'] as $mscncomp_prsn): echo $mscncomp_prsn;
        if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; ?></td></tr>
        <?php endforeach;} endforeach; endforeach; ?>
        </table></div>
        <?php }

        if(!empty($crtv_rls)) { ?>
        <div id="crtvs"><h5>Creative Team</h5><table class="prod1">
        <?php foreach($crtv_rls as $crtv_rl): ?>
        <tr><?php if(!empty($crtv_rl['crtvs'])) {$i=0; $ppl=count($crtv_rl['crtvs']); ?>
        <td><?php echo $crtv_rl['crtv_rl'].' '; ?></td>
        <td>.....</td>
        <td><?php
        $h=0; foreach($crtv_rl['crtvs'] as $crtv): if(!empty($crtv['crtvcomp_ppl'])) {$h++;} endforeach;
        foreach($crtv_rl['crtvs'] as $crtv):
        if(!empty($crtv['crtvcomp_ppl'])) {$j=0; $compppl=count($crtv['crtvcomp_ppl']); foreach($crtv['crtvcomp_ppl'] as $crtvcomp_prsn):
        echo $crtvcomp_prsn['prsn_nm']; if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ' for ';}
        echo $crtv['comp_nm'];
        if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}} elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}}
        $i++; endforeach; } ?>
        </td></tr>
        <?php endforeach; ?>
        </table></div> <?php }

        if(!empty($prdtm_rls)) { ?>
        <div id="prdtms"><h5>Production Team</h5><table class="prod1">
        <?php foreach($prdtm_rls as $prdtm_rl): ?>
        <tr><?php if(!empty($prdtm_rl['prdtms'])) {$i=0; $ppl=count($prdtm_rl['prdtms']); ?>
        <td><?php echo $prdtm_rl['prdtm_rl'].' '; ?></td>
        <td>.....</td>
        <td><?php
        $h=0; foreach($prdtm_rl['prdtms'] as $prdtm): if(!empty($prdtm['prdtmcomp_ppl'])) {$h++;} endforeach;
        foreach($prdtm_rl['prdtms'] as $prdtm):
        if(!empty($prdtm['prdtmcomp_ppl'])) {$j=0; $compppl=count($prdtm['prdtmcomp_ppl']); foreach($prdtm['prdtmcomp_ppl'] as $prdtmcomp_prsn):
        echo $prdtmcomp_prsn['prsn_nm']; if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ' for ';}
        echo $prdtm['comp_nm'];
        if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}} elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}}
        $i++; endforeach; } ?>
        </td></tr>
        <?php endforeach; ?>
        </table></div> <?php }

        if(!empty($thms)) {$i=0; $thm_cnt=count($thms); ?>
        <div id="thms" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Themes:</td><td>
        <?php foreach($thms as $thm): echo $thm['thm_nm']; if(!empty($thm['rel_thms'])) {echo ' ('.implode(' / ', $thm['rel_thms']).')';}
        if($i<$thm_cnt-1) {echo ' / ';} $i++; endforeach; ?></td></tr>
        </table></div></br>
        <?php }

        if(!empty($sttngs)) { ?>
        <div id="sttngs" class="box"><table class="overview">
        <tr><td class="ovrvwcol1" colspan="2"><u>Setting<?php if(count($sttngs)>1) {echo 's';} ?></u></td></tr>
        <?php $h=0;
        foreach($sttngs as $sttng): $h++; ?>
        <?php if(!empty($sttng['tms'])) {$i=0; $tms=count($sttng['tms']); ?>
        <tr class="<?php if($h>1) {echo 'newcredit';} ?>"><td class="ovrvwcol1">Time:</td>
        <td><?php if(!empty($sttng['tm_spns'])) {$j=0; foreach($sttng['tms'] as $tm):
        if($j==0) {echo $tm['tm']; if(!empty($tm['rel_tms'])) {echo ' ('.implode(' / ', $tm['rel_tms']).')';} echo ' to ';}
        if($j==$tms-1) {echo $tm['tm']; if(!empty($tm['rel_tms'])) {echo ' ('.implode(' / ', $tm['rel_tms']).')';}} $j++; endforeach; echo '</br>';}
        foreach($sttng['tms'] as $tm): echo $tm['tm'];
        if(!empty($tm['rel_tms'])) {echo ' ('.implode(' / ', $tm['rel_tms']).')';} if($i<$tms-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        if(!empty($sttng['lctns'])) {$i=0; $lctns=count($sttng['lctns']); ?>
        <tr class="<?php if($h>1 && empty($sttng['tms'])) {echo 'newcredit';} ?>"><td class="ovrvwcol1">Location:</td>
        <td><?php foreach($sttng['lctns'] as $lctn):
        echo $lctn['lctn']; if(!empty($lctn['rel_lctns'])) {echo ' ('.implode(' / ', $lctn['rel_lctns']).')';}
        if($i<$lctns-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        if(!empty($sttng['plcs'])) {$i=0; $plcs=count($sttng['plcs']); ?>
        <tr class="<?php if($h>1 && empty($sttng['tms']) && empty($sttng['plcs'])) {echo 'newcredit';} ?>"><td class="ovrvwcol1">Place:</td>
        <td><?php foreach($sttng['plcs'] as $plc):
        echo $plc['plc']; if(!empty($plc['rel_plcs'])) {echo ' ('.implode(' / ', $plc['rel_plcs']).')';}
        if($i<$plcs-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        endforeach; ?>
        </table></div></br>
        <?php }

        if(!empty($prdrn_sbsq_prds))
        { ?>
        <div id="prdrn_sbsq_prds"><table class="credits">
        <tr><th colspan="3">Subsequent runs of this production:</th></tr>
        <?php $rowclass=0;
        foreach($prdrn_sbsq_prds as $prdrn_sbsq_prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prdrn_sbsq_prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prdrn_sbsq_prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prdrn_sbsq_prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prdrn_sbsq_prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prdrn_sbsq_prd['sg_cnt']>count($prdrn_sbsq_prd['sg_prds'])) {echo 'Includes (subsequent run of this production):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prdrn_sbsq_prd['sg_prds'] as $sg_prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $sg_prd['sg_prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $sg_prd['sg_thtr']; ?></td>
          <td class="prdcol4"><?php echo $sg_prd['sg_prd_dts']; ?></td>
        </tr>
        <?php endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($prdrn_prvs_prds))
        { ?>
        <div id="prdrn_prvs_prds"><table class="credits">
        <tr><th colspan="3">Previous runs of this production:</th></tr>
        <?php $rowclass=0;
        foreach($prdrn_prvs_prds as $prdrn_prvs_prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prdrn_prvs_prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prdrn_prvs_prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prdrn_prvs_prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prdrn_prvs_prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prdrn_prvs_prd['sg_cnt']>count($prdrn_prvs_prd['sg_prds'])) {echo 'Includes (previous run of this production):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prdrn_prvs_prd['sg_prds'] as $sg_prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $sg_prd['sg_prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $sg_prd['sg_thtr']; ?></td>
          <td class="prdcol4"><?php echo $sg_prd['sg_prd_dts']; ?></td>
        </tr>
        <?php endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if($ssn_nm)
        { ?>
        <div id="ssn"><h5>Part of (season):</h5>
        <table class="prod2">
        <tr><td><?php echo $ssn_nm; ?></td></tr>
        </table></div>
        <?php }

        if($fstvl_nm)
        { ?>
        <div id="fstvl"><h5>Part of (festival):</h5>
        <table class="prod2">
        <tr><td><?php echo $fstvl_nm; ?></td></tr>
        </table></div>
        <?php }

        if(!empty($crss))
        { ?>
        <div id="crss"><h5>Part of (course):</h5>
        <table class="prod2">
        <?php foreach($crss as $crs): ?>
        <tr><td><?php echo $crs; ?></td></tr>
        <?php endforeach; ?>
        </table></div>
        <?php }

        if(!empty($reps))
        { ?>
        <div id="reps"><table class="credits">
        <tr><th colspan="3">Plays in rep with:</th></tr>
        <?php $rowclass=0;
        foreach($reps as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        } if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (in rep with this production):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        } endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($mats))
        { ?>
        <div id="mats"><h5>Other productions of:</h5>
        <table class="prod2">
        <?php foreach($mats as $mat): ?>
        <tr><td><?php echo $mat['mat_nm']; ?></td></tr>
        <?php endforeach; ?>
        </table></div>
        <?php }

        if(!empty($pts))
        { ?>
        <div id="pts"><table class="credits">
        <tr><th colspan="3">Playtext details:</th></tr>
        <?php $rowclass=0;
        foreach($pts as $pt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';
        } if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
        <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($pt['sg_pts'] as $pt): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';
        endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($rvws))
        { ?>
        <div id="rvws"><h5>Reviews:</h5>
        <table class="prod2">
        <?php foreach($rvws as $rvw): ?>
        <tr><td><?php echo $rvw['rvw_lnk']; ?></td></tr>
        <?php endforeach; ?>
        </table></div>
        <?php }

        if(!empty($alt_nms)) { ?>
        <div id="alt_nms" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Alternate titles:</td><td><?php echo implode('</br>', $alt_nms); ?></td></tr>
        </table></div></br>
        <?php }

        if(!empty($awrds)) { ?>
          <h5>Awards</h5></br>
          <div id="awrds_cnt" class="box"><table class="overview">
            <?php if(array_sum($awrds_ttl_wins)>0) { ?><tr><td class="ovrvwcol1">Total wins:</td><td><?php echo array_sum($awrds_ttl_wins); ?></td></tr>
            <?php if(array_sum($awrds_ttl_noms)>0) { ?><tr><td class="ovrvwcol1">Other nominations:</td><td><?php echo array_sum($awrds_ttl_noms); ?></td></tr>
            <?php }} else { ?><tr><td class="ovrvwcol1">Total nominations:</td><td><?php echo array_sum($awrds_ttl_noms); ?></td></tr>
            <?php } ?>
          </table></div>
          <?php foreach($awrds as $awrd): ?>
            <div id="awrds">
              <table class="credits">
                <tr>
                  <th colspan="3"><?php echo $awrd['awrds_nm']; ?>
                    <span style="font-weight:normal">
                    <?php echo '[';
                    if(array_sum($awrd['awrd_wins'])>0) {echo 'Wins: '.array_sum($awrd['awrd_wins']);
                    if(array_sum($awrd['awrd_noms'])>0) {echo ' | Other nominations: '.array_sum($awrd['awrd_noms']);}}
                    else {echo 'Nominations: '.array_sum($awrd['awrd_noms']);}
                    echo ']';?></span>
                  </th>
                </tr>
                <?php $rowclass=0;
                foreach($awrd['awrd_yrs'] as $awrd_yr): ?>
                  <tr class="newcredit row<?php echo $rowclass; ?>">
                    <td class="prdcol5" colspan="3">
                      <?php echo $awrd_yr['awrd_lnk'].' [';
                      if(array_sum($awrd_yr['awrd_yr_wins'])>0) {echo 'Wins: '.array_sum($awrd_yr['awrd_yr_wins']);
                      if(array_sum($awrd_yr['awrd_yr_noms'])>0) {echo ' | Other nominations: '.array_sum($awrd_yr['awrd_yr_noms']);}}
                      else {echo 'Nominations: '.array_sum($awrd_yr['awrd_yr_noms']);}
                      echo ']'; ?>
                    </td>
                  </tr>
                    <?php $g=0; foreach($awrd_yr['ctgrys'] as $ctgry): foreach($ctgry['noms'] as $nom):
                    if(!empty($nom['co_nomprds'])) {$n=0; $p=0; $q=0; foreach($nom['co_nomprds'] as $co_nomprd):
                    if($co_nomprd['prd_id']==$prd_id) {$n++;} elseif(in_array($co_nomprd['prd_id'], $awrd_prd_ids)) {$p++;} else {$q++;} endforeach;} ?>
                    <tr class="<?php if($g==0){echo 'newcredit';}else{echo 'newsubcredit';}?> row<?php echo $rowclass; ?>">
                      <td class="prdcol5" colspan="3">▪
                        <?php if($nom['win']) {echo '<b>';} echo $ctgry['awrd_ctgry_nm'].' - '.$nom['nom_win_dscr']; if(!empty($nom['nomppl'])) {echo ': ';} if($nom['win']) {echo '</b>';};
                        $h=0; foreach($nom['nomppl'] as $nomprsn): if(!empty($nomprsn['nomcomp_ppl'])) {$h++;} endforeach;
                        $i=0; $ppl=count($nom['nomppl']); foreach($nom['nomppl'] as $nomprsn):
                        if(!empty($nomprsn['nomcomp_ppl'])) {$j=0; $compppl=count($nomprsn['nomcomp_ppl']);
                        foreach($nomprsn['nomcomp_ppl'] as $nomcomp_prsn): echo $nomcomp_prsn;
                        if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ' for '; }
                        echo $nomprsn['nom_prsn'];
                        if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}}
                        elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}}
                        $i++; endforeach;
                        if(!empty($nom['co_nomprds'])) {if($p>0) {if($n>0) {
                        if($p<2) {echo '; also for this run of production:';}
                        else {echo '; also for these runs of production:';}}
                        else {echo ' for:';}}}
                        ?>
                      </td>
                    </tr>
                    <?php if(!empty($nom['co_nomprds'])) {
                    if($p>0) {foreach($nom['co_nomprds'] as $co_nomprd): if($co_nomprd['prd_id']!==$prd_id && in_array($co_nomprd['prd_id'], $awrd_prd_ids)) { ?>
                    <tr class="row<?php echo $rowclass; ?>">
                      <td class="prdcol1"><?php echo $co_nomprd['prd_nm']; ?></td>
                      <td class="prdcol2"><?php echo $co_nomprd['thtr']; ?></td>
                      <td class="prdcol4"><?php echo $co_nomprd['prd_dts']; ?></td>
                    </tr>
                    <?php } endforeach;};
                    if($q>0) { ?><tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">Also for:</td></tr>
                    <?php foreach($nom['co_nomprds'] as $co_nomprd): if($co_nomprd['prd_id']!==$prd_id && !in_array($co_nomprd['prd_id'], $awrd_prd_ids)) { ?>
                    <tr class="row<?php echo $rowclass; ?>">
                      <td class="prdcol1"><?php echo $co_nomprd['prd_nm']; ?></td>
                      <td class="prdcol2"><?php echo $co_nomprd['thtr']; ?></td>
                      <td class="prdcol4"><?php echo $co_nomprd['prd_dts']; ?></td>
                    </tr>
                    <?php } endforeach;};}

                    if(!empty($nom['cowins'])) { ?>
                    <tr class="row<?php echo $rowclass; ?>">
                      <td colspan="3" class="prdcol5">Also awarded:</td>
                    </tr>
                    <?php foreach($nom['cowins'] as $cowin): ?>
                    <tr class="row<?php echo $rowclass; ?>">
                      <td class="prdcol5" colspan="3"><?php echo '<b>'.$cowin['nom_win_dscr'].': </b>';
                        if(!empty($cowin['cowin_ppl'])) {
                        $k=0; foreach($cowin['cowin_ppl'] as $cowinprsn): if(!empty($cowinprsn['cowincomp_ppl'])) {$k++;} endforeach;
                        $l=0; $cowin_ppl=count($cowin['cowin_ppl']); foreach($cowin['cowin_ppl'] as $cowinprsn):
                        if(!empty($cowinprsn['cowincomp_ppl'])) {$m=0; $cowin_compppl=count($cowinprsn['cowincomp_ppl']);
                        foreach($cowinprsn['cowincomp_ppl'] as $cowincomp_prsn ): echo $cowincomp_prsn;
                        if($m<$cowin_compppl-2) {echo ', ';} elseif($m<$cowin_compppl-1) {echo ' and ';} $m++; endforeach; echo ' for '; }
                        echo $cowinprsn['cowin_prsn'];
                        if($l<$cowin_ppl-2) {if($k>0) {echo '; ';} else {echo ', ';}}
                        elseif($l<$cowin_ppl-1) {if($k>0) {echo '; and ';} else {echo ' and ';}}
                        $l++; endforeach; if(!empty($cowin['cowin_prds'])) {echo ' for:';}} ?>
                      </td>
                    </tr>
                    <?php if(!empty($cowin['cowin_prds'])) {
                    foreach($cowin['cowin_prds'] as $cowin_prd): ?>
                      <tr class="row<?php echo $rowclass; ?>">
                        <td class="prdcol1"><?php echo $cowin_prd['prd_nm']; ?></td>
                        <td class="prdcol2"><?php echo $cowin_prd['thtr']; ?></td>
                        <td class="prdcol4"><?php echo $cowin_prd['prd_dts']; ?></td>
                      </tr>
                    <?php endforeach; }
                    endforeach; }
                  $g++;
                  endforeach; endforeach;
                  $rowclass=1-$rowclass;
                endforeach; ?>
              </table>
            </div>
          <?php endforeach;
        } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="prd_id" value="<?php echo $prd_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>