<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (playtext) | TheatreBase</title>
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

        <h4>PLAYTEXT<?php echo $coll_dsply; ?>:</h4>
        <h1><?php echo $pagetitle; ?></h1>
        <h2><?php echo $pt_sbnm; ?></h2>

        <div id="clssfctn" class="box">
        <table class="overview">
        <?php if(!empty($txt_vrsns)) { ?><tr><td class="ovrvwcol1">Text version:</td><td><?php echo implode(' / ', $txt_vrsns); ?></td></tr><?php } ?>
        <tr><td class="ovrvwcol1">Year<?php echo $pt_wrttn_compld; ?>:</td><td><?php echo $pt_yr_dsply; ?></td></tr>
        <?php if($pt_pub_dt) { ?><tr><td class="ovrvwcol1">Publication date:</td><td><?php echo $pt_pub_dt; ?></td></tr><?php }
        if(!empty($ctgrys)) { ?><tr><td class="ovrvwcol1">Category:</td><td><?php echo implode(' / ', $ctgrys); ?></td></tr><?php }
        if(!empty($gnrs)) {$i=0; $gnr_cnt=count($gnrs); ?><tr><td class="ovrvwcol1">Genre:</td><td>
        <?php foreach($gnrs as $gnr): echo $gnr['gnr_nm']; if(!empty($gnr['rel_gnrs'])) {echo ' ('.implode(' / ', $gnr['rel_gnrs']).')';}
        if($i<$gnr_cnt-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        if(!empty($ftrs)) { ?><tr><td class="ovrvwcol1">Features:</td><td><?php echo implode(' / ', $ftrs); ?></td></tr><?php } ?>
        </table></div></br>

        <?php if(!empty($wri_rls)) { ?>
        <div id="wris"><h5>Writers</h5><table class="prod2">
        <?php if(!empty($wri_rls)) {
        foreach($wri_rls as $wri_rl): ?>
        <tr><td>
        <?php if(!empty($wri_rl['src_mats'])) {$g=0; $h=0; $sms=count($wri_rl['src_mats']); echo $wri_rl['src_mat_rl'].' ';
        foreach($wri_rl['src_mats'] as $src_mat):
        if(!preg_match("/^{$pagetitle}$/i", $src_mat['src_mat_nm'])) {echo 'the '.$src_mat['src_mat_frmt'].', '.$src_mat['src_mat_url'];
        if($h==$sms-2 || ($h==$sms-1 && $wri_rl['wri_rl'])) {echo ',';}}
        else {if($g==0) {echo 'the ';} echo $src_mat['src_mat_frmt_url']; $g++;}
        if($h<$sms-2) {echo ', ';} elseif($h<$sms-1) {echo ' and ';} $h++; endforeach; echo ' ';}
        if(!empty($wri_rl['wris'])) {$i=0; $ppl=count($wri_rl['wris']); echo $wri_rl['wri_rl'].' ';
        foreach($wri_rl['wris'] as $wri): if($i>0 && $i<$ppl-1 && !$wri['wri_sb_rl']) {echo ', ';} elseif($i>0 && $i==$ppl-1 && !$wri['wri_sb_rl']) {echo ' and ';}
        echo $wri['wri_sb_rl'].$wri['comp_nm'];
        if(!empty($wri['wricomp_ppl'])) { echo ' ('; $j=0; $compppl=count($wri['wricomp_ppl']); foreach($wri['wricomp_ppl'] as $wricomp_prsn):
        echo $wricomp_prsn['wri_sb_rl'].$wricomp_prsn['prsn_nm'];
        if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++;endforeach; echo ')'; }
        $i++; endforeach; } ?>
        </td></tr>
        <?php endforeach; } ?>
        </table></div><?php }

        if(!empty($cntr_rls)) { ?>
        <div id="cntrs"><h5>Contributors</h5><table class="prod2">
        <?php if(!empty($cntr_rls)) {
        foreach($cntr_rls as $cntr_rl): ?>
        <tr><td>
        <?php if(!empty($cntr_rl['cntrs'])) {
        $h=0; foreach($cntr_rl['cntrs'] as $cntr): if(!empty($cntr['cntrcomp_ppl'])) {$h++;} endforeach;
        $i=0; $ppl=count($cntr_rl['cntrs']); echo $cntr_rl['cntr_rl'].' ';
        foreach($cntr_rl['cntrs'] as $cntr):
        if(!empty($cntr['cntrcomp_ppl'])) {$j=0; $compppl=count($cntr['cntrcomp_ppl']); foreach($cntr['cntrcomp_ppl'] as $cntrcomp_prsn):
        echo $cntrcomp_prsn['prsn_nm'].$cntrcomp_prsn['cntr_sb_rl'];
        if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ' for ';} echo $cntr['comp_nm'].$cntr['cntr_sb_rl'];
        if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}} elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}} $i++; endforeach;} ?>
        </td></tr>
        <?php endforeach; } ?>
        </table></div><?php }

        if(!empty($mats)) { ?>
        <div id="mats"><h5>Material</h5>
        <table class="prod1">
        <?php foreach($mats as $mat): ?>
        <tr><td><?php echo $mat['mat_nm']; ?></td></tr>
        <?php endforeach; ?></table></div><?php }

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

        if(!empty($wrks_sg_sbhdrs))
        { ?>
        <div id="wrks_sg_pts"><table class="credits">
        <tr><th colspan="3">Playtexts of which this collected works is comprised:</th></tr>
        <?php $rowclass=0;
        foreach($wrks_sg_sbhdrs as $wrks_sg_sbhdr):
          if($wrks_sg_sbhdr['wrks_sbhdr']) {$rowclass=0; ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol6" colspan="3"><?php echo $wrks_sg_sbhdr['wrks_sbhdr']; ?></td>
          </tr>
          <?php }
          foreach($wrks_sg_sbhdr['wrks_sg_pts'] as $wrks_sg_pt): ?>
            <tr class="newcredit row<?php echo $rowclass; ?>">
              <td class="ptcol1"><?php echo $wrks_sg_pt['pt_nm']; ?></td>
              <td class="ptcol2"><?php echo $wrks_sg_pt['txt_vrsn_nm']; ?></td>
              <td class="ptcol3"><?php echo $wrks_sg_pt['pt_yr']; ?></td>
            </tr>
            <?php if(!empty($wrks_sg_pt['wri_rls'])) { ?>
            <tr class="row<?php echo $rowclass; ?>">
              <td class="ptcol4" colspan="3"><em><?php
              $f=0; $rls=count($wrks_sg_pt['wri_rls']); foreach($wrks_sg_pt['wri_rls'] as $wri_rl):
              if(!empty($wri_rl['src_mats'])) {$g=0; $h=0; $sms=count($wri_rl['src_mats']); echo $wri_rl['src_mat_rl'].' ';
              foreach($wri_rl['src_mats'] as $src_mat):
              if(!preg_match("/^{$wrks_sg_pt['pt_nm_pln']}$/i", $src_mat['src_mat_nm'])) {echo 'the '.$src_mat['src_mat_frmt'].', <span style="font-style:normal">'.$src_mat['src_mat_url'].'</span>';
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
            if(!empty($wrks_sg_pt['sg_pts'])) { ?>
            <tr class="row<?php echo $rowclass; ?>">
              <td class="ptcol4" colspan="3">Comprises: <?php echo implode(' / ', $wrks_sg_pt['sg_pts']); ?></td>
            </tr>
            <?php }
            if($wrks_sg_pt['wrks_sg_rl']) { ?>
            <tr class="row<?php echo $rowclass; ?>">
              <td class="ptcol4" colspan="3"><em><?php echo $wrks_sg_pt['wrks_sg_rl']; ?></em></td>
            </tr>
            <?php }
            $rowclass=1-$rowclass;
          endforeach;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($coll_sg_sbhdrs))
        { ?>
        <div id="coll_sg_pts"><table class="credits">
        <tr><th colspan="3">Playtexts of which this collection is comprised:</th></tr>
        <?php $rowclass=0;
        foreach($coll_sg_sbhdrs as $coll_sg_sbhdr):
          if($coll_sg_sbhdr['coll_sbhdr']) {$rowclass=0; ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol6" colspan="3"><?php echo $coll_sg_sbhdr['coll_sbhdr']; ?></td>
          </tr>
          <?php }
          foreach($coll_sg_sbhdr['coll_sg_pts'] as $coll_sg_pt): ?>
            <tr class="newcredit row<?php echo $rowclass; ?>">
              <td class="ptcol1"><?php echo $coll_sg_pt['pt_nm']; ?></td>
              <td class="ptcol2"><?php echo $coll_sg_pt['txt_vrsn_nm']; ?></td>
              <td class="ptcol3"><?php echo $coll_sg_pt['pt_yr']; ?></td>
            </tr>
            <?php if(!empty($coll_sg_pt['wri_rls'])) { ?>
            <tr class="row<?php echo $rowclass; ?>">
              <td class="ptcol4" colspan="3"><em><?php
              $f=0; $rls=count($coll_sg_pt['wri_rls']); foreach($coll_sg_pt['wri_rls'] as $wri_rl):
              if(!empty($wri_rl['src_mats'])) {$g=0; $h=0; $sms=count($wri_rl['src_mats']); echo $wri_rl['src_mat_rl'].' ';
              foreach($wri_rl['src_mats'] as $src_mat):
              if(!preg_match("/^{$coll_sg_pt['pt_nm_pln']}$/i", $src_mat['src_mat_nm'])) {echo 'the '.$src_mat['src_mat_frmt'].', <span style="font-style:normal">'.$src_mat['src_mat_url'].'</span>';
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

        if(!empty($wrks_ov_pts))
        { ?>
        <div id="wrks_ov_pts"><table class="credits">
        <tr><th colspan="3">Part of collected works:</th></tr>
        <?php $rowclass=0;
        foreach($wrks_ov_pts as $wrks_ov_pt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $wrks_ov_pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $wrks_ov_pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $wrks_ov_pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($wrks_ov_pt['cntr_rls'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><em><?php
          $f=0; $rls=count($wrks_ov_pt['cntr_rls']); foreach($wrks_ov_pt['cntr_rls'] as $cntr_rl):
          $h=0; foreach($cntr_rl['cntrs'] as $cntr): if(!empty($cntr['cntrcomp_ppl'])) {$h++;} endforeach;
          $i=0; $ppl=count($cntr_rl['cntrs']); echo $cntr_rl['cntr_rl'].' ';
          foreach($cntr_rl['cntrs'] as $cntr):
          if(!empty($cntr['cntrcomp_ppl'])) {$j=0; $compppl=count($cntr['cntrcomp_ppl']); foreach($cntr['cntrcomp_ppl'] as $cntrcomp_prsn):
          echo $cntrcomp_prsn['prsn_nm'].$cntrcomp_prsn['cntr_sb_rl'];
          if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ' for ';} echo $cntr['comp_nm'].$cntr['cntr_sb_rl'];
          if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}} elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}} $i++; endforeach;
          if($f<$rls-1) {echo '</br>';} $f++; endforeach; ?>
          </em></td>
        </tr>
        <?php }
        if($wrks_ov_pt['wrks_sbhdr']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">Section: <em><?php echo $wrks_ov_pt['wrks_sbhdr']; ?></em></td>
        </tr>
        <?php }
        if($wrks_ov_pt['wrks_sg_rl']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><em><?php echo $wrks_ov_pt['wrks_sg_rl']; ?></em></td>
        </tr>
        <?php }
        if(!empty($wrks_ov_pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><?php echo 'Collected works also comprises: '.implode(' / ', $wrks_ov_pt['sg_pts']); ?></td>
        </tr>
        <?php }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($coll_ov_pts))
        { ?>
        <div id="coll_ov_pts"><table class="credits">
        <tr><th colspan="3">Part of (collection):</th></tr>
        <?php $rowclass=0;
        foreach($coll_ov_pts as $coll_ov_pt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $coll_ov_pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $coll_ov_pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $coll_ov_pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($coll_ov_pt['wri_rls'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><em><?php
          $f=0; $rls=count($coll_ov_pt['wri_rls']); foreach($coll_ov_pt['wri_rls'] as $wri_rl):
          if(!empty($wri_rl['src_mats'])) {$g=0; $h=0; $sms=count($wri_rl['src_mats']); echo $wri_rl['src_mat_rl'].' ';
          foreach($wri_rl['src_mats'] as $src_mat):
          if(!preg_match("/^{$coll_ov_pt['pt_nm_pln']}$/i", $src_mat['src_mat_nm'])) {echo 'the '.$src_mat['src_mat_frmt'].', <span style="font-style:normal">'.$src_mat['src_mat_url'].'</span>';
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
        if($coll_ov_pt['coll_sbhdr']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">Section: <em><?php echo $coll_ov_pt['coll_sbhdr']; ?></em></td>
        </tr>
        <?php }
        if(!empty($coll_ov_pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><?php echo 'Collection also comprises: '.implode(' / ', $coll_ov_pt['sg_pts']); ?></td>
        </tr>
        <?php }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($lnk_pts))
        { ?>
        <div id="lnk_pts"><table class="credits">
        <tr><th colspan="3">Linked to:</th></tr>
        <?php $rowclass=0;
        foreach($lnk_pts as $pt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
        if(!empty($pt['coll_sg_lst_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><?php echo 'Comprises: '.implode(' / ', $pt['coll_sg_lst_pts']); ?></td>
        </tr>
        <?php }
        if(!empty($pt['cntr_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_cntr_dsply.inc.html.php';}
        if(!empty($pt['wrks_sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><?php echo 'Comprises: '.implode(' / ', $pt['wrks_sg_pts']); ?></td>
        </tr>
        <?php } if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
        <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes (linked to this playtext):';} else {echo 'Comprises:';} ?></td></tr>
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

        if($cst_ttl) { ?>
        <div id="cst_reqd" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Cast required [<?php echo $cst_ttl; ?>]:</td><td>[<?php echo $cst ?>]</td></tr>
        <?php if($cst_nt)
        { ?>
        <tr><td class="ovrvwcol1">Cast notes:</td><td><em><?php echo $cst_nt; ?></em></td></tr>
        <?php } ?>
        </table></div>
        <?php }

        if(!empty($char_grps)) { ?>
        <div id="chars"><table class="credits">
        <tr><th colspan="5">Characters<?php if($char_ttl) { ?> [<?php echo $char_ttl; ?>]<span style="font-weight:normal"> [<?php echo $char_dtls; ?>]</span><?php } ?></th></tr>
        <?php $rowclass=0;
        foreach($char_grps as $char_grp):
          if($char_grp['char_grp']) {$rowclass=0; ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol6" colspan="5"><?php echo $char_grp['char_grp']; ?></td>
          </tr>
          <?php }
          foreach($char_grp['chars'] as $char): ?>
            <tr class="newcredit row<?php echo $rowclass; ?>">
              <td class="charcol1"><?php echo $char['char_nm'].$char['char_amnt'].$char['char_nt']; ?></td>
              <td class="charcol2"><?php echo $char['char_sx']; ?></td>
              <td class="charcol3"><?php echo $char['char_age']; ?></td>
              <td class="charcol4"><?php echo $char['char_dscr']; ?></td>
            </tr>
            <?php $rowclass=1-$rowclass;
          endforeach;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($lcnsrs))
        { ?>
        <div id="lcnsrs"><h5>Licensors</h5><table class="prod1">
        <?php foreach($lcnsrs as $lcnsr): ?>
        <tr><td><?php echo $lcnsr['comp_nm']; ?></td><td>.....</td>
        <td>
        <?php if(empty($lcnsr['lcnsrcomp_ppl'])) { echo $lcnsr['lcnsr_rl'];}
        else { foreach($lcnsr['lcnsrcomp_ppl'] as $lcnsrcomp_prsn): echo $lcnsrcomp_prsn['prsn_nm'].' - '.$lcnsrcomp_prsn['lcnsr_rl']; ?></br><?php endforeach; } ?>
        </td></tr>
        <?php endforeach; ?>
        </table></div><?php }

        if(!empty($alt_nms)) { ?>
        <div id="alt_nms" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Alternate titles:</td><td><?php echo implode('</br>', $alt_nms); ?></td></tr>
        </table></div></br>
        <?php }

        if(!empty($prds)) { ?>
        <div id="prds"><table class="credits">
        <tr><th colspan="3">Productions of this playtext</th></tr>
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
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
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
                  <?php $g=0; foreach($awrd_yr['ctgrys'] as $ctgry): foreach($ctgry['noms'] as $nom): ?>
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
                        $i++;
                        endforeach;
                        if(!empty($nom['co_nompts'])) {
                        $p=0; $q=0; $co_nompt_cnt1=0; $co_nompt_cnt2=0; foreach($nom['co_nompts'] as $co_nompt): if($co_nompt['pt_id']!==$pt_id) {if(in_array($co_nompt['pt_id'], $awrd_pt_ids)) {$co_nompt_cnt1++;} else {$co_nompt_cnt2++;}} endforeach;
                        if($co_nompt_cnt1>0) {echo ' for '; foreach($nom['co_nompts'] as $co_nompt):
                        if($co_nompt['pt_id']!==$pt_id && in_array($co_nompt['pt_id'], $awrd_pt_ids)) {echo $co_nompt['pt_nm'];
                        if($p<$co_nompt_cnt1-2) {echo ', ';} elseif($p<$co_nompt_cnt1-1) {echo ' and ';} $p++;}
                        endforeach;}
                        if($co_nompt_cnt2>0) {echo ' (also for ';
                        foreach($nom['co_nompts'] as $co_nompt):
                        if($co_nompt['pt_id']!==$pt_id && !in_array($co_nompt['pt_id'], $awrd_pt_ids)) {echo $co_nompt['pt_nm'];
                        if($q<$co_nompt_cnt2-2) {echo ', ';} elseif($q<$co_nompt_cnt2-1) {echo ' and ';} $q++;}
                        endforeach; echo ')';};}
                        ?>
                      </td>
                    </tr>
                    <?php if(!empty($nom['cowins'])) { ?>
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
                        $l++; endforeach;}
                        if(!empty($cowin['cowin_pts'])) {$n=0; $cowin_pts=count($cowin['cowin_pts']); if(!empty($cowin['cowin_ppl'])) {echo ' for ';}
                        foreach($cowin['cowin_pts'] as $cowin_pt): echo $cowin_pt;
                        if($n<$cowin_pts-2) {echo ', ';} elseif($n<$cowin_pts-1) {echo ' and ';} $n++; endforeach;} ?>
                      </td>
                    </tr>
                    <?php endforeach; }
                  $g++;
                  endforeach; endforeach;
                  $rowclass=1-$rowclass;
                endforeach; ?>
              </table>
            </div>
          <?php endforeach;
        }

        if(!empty($prd_awrds)) { ?>
          <h5>Awards for productions of this playtext</h5></br>
          <div id="prd_awrds_cnt" class="box"><table class="overview">
            <?php if(array_sum($prd_awrds_ttl_wins)>0) { ?><tr><td class="ovrvwcol1">Total wins:</td><td><?php echo array_sum($prd_awrds_ttl_wins); ?></td></tr>
            <?php if(array_sum($prd_awrds_ttl_noms)>0) { ?><tr><td class="ovrvwcol1">Other nominations:</td><td><?php echo array_sum($prd_awrds_ttl_noms); ?></td></tr>
            <?php }} else { ?><tr><td class="ovrvwcol1">Total nominations:</td><td><?php echo array_sum($prd_awrds_ttl_noms); ?></td></tr>
            <?php } ?>
          </table></div>
          <?php foreach($prd_awrds as $prd_awrd): ?>
            <div id="prd_awrds">
              <table class="credits">
                <tr>
                  <th colspan="3"><?php echo $prd_awrd['awrds_nm']; ?>
                    <span style="font-weight:normal">
                    <?php echo '[';
                    if(array_sum($prd_awrd['awrd_wins'])>0) {echo 'Wins: '.array_sum($prd_awrd['awrd_wins']);
                    if(array_sum($prd_awrd['awrd_noms'])>0) {echo ' | Other nominations: '.array_sum($prd_awrd['awrd_noms']);}}
                    else {echo 'Nominations: '.array_sum($prd_awrd['awrd_noms']);}
                    echo ']';?></span>
                  </th>
                </tr>
                <?php $rowclass=0;
                foreach($prd_awrd['awrd_yrs'] as $awrd_yr): ?>
                  <tr class="newcredit row<?php echo $rowclass; ?>">
                    <td class="prdcol5" colspan="3">
                      <?php echo $awrd_yr['awrd_lnk'].' [';
                      if(array_sum($awrd_yr['awrd_yr_wins'])>0) {echo 'Wins: '.array_sum($awrd_yr['awrd_yr_wins']);
                      if(array_sum($awrd_yr['awrd_yr_noms'])>0) {echo ' | Other nominations: '.array_sum($awrd_yr['awrd_yr_noms']);}}
                      else {echo 'Nominations: '.array_sum($awrd_yr['awrd_yr_noms']);}
                      echo ']'; ?>
                    </td>
                  </tr>
                  <?php $g=0; foreach($awrd_yr['ctgrys'] as $ctgry): foreach($ctgry['noms'] as $nom): ?>
                    <tr class="<?php if($g==0){echo 'newcredit';}else{echo 'newsubcredit';}?> row<?php echo $rowclass; ?>">
                      <td class="prdcol5" colspan="3">▪
                        <?php if($nom['win']) {echo '<b>';} echo $ctgry['awrd_ctgry_nm'].' - '.$nom['nom_win_dscr'];
                        if(!empty($nom['nomppl']) || !empty($nom['nomprds'])) {echo ': ';} if($nom['win']) {echo '</b>';};
                        if(!empty($nom['nomppl'])) {
                        $h=0; foreach($nom['nomppl'] as $nomprsn): if(!empty($nomprsn['nomcomp_ppl'])) {$h++;} endforeach;
                        $i=0; $ppl=count($nom['nomppl']); foreach($nom['nomppl'] as $nomprsn):
                        if(!empty($nomprsn['nomcomp_ppl'])) {$j=0; $compppl=count($nomprsn['nomcomp_ppl']);
                        foreach($nomprsn['nomcomp_ppl'] as $nomcomp_prsn): echo $nomcomp_prsn;
                        if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ' for '; }
                        echo $nomprsn['nom_prsn'];
                        if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}}
                        elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}}
                        $i++; endforeach; if(!empty($nom['nomprds'])) {echo ' for:';}} ?>
                      </td>
                    </tr>
                    <?php foreach($nom['nomprds'] as $nomprd): ?>
                    <tr class="row<?php echo $rowclass; ?>">
                      <td class="prdcol1"><?php echo $nomprd['prd_nm']; ?></td>
                      <td class="prdcol2"><?php echo $nomprd['thtr']; ?></td>
                      <td class="prdcol4"><?php echo $nomprd['prd_dts']; ?></td>
                    </tr>
                    <?php endforeach;
                    if(!empty($nom['co_nomprds'])) {
                    $co_nomprd_ids=array(); foreach($nom['co_nomprds'] as $co_nomprd):
                    $co_nomprd_ids[]=$co_nomprd['prd_id']; endforeach;
                    $co_nomprds_diff_array=array_diff($co_nomprd_ids, $awrd_prd_ids);
                    if(count($co_nomprds_diff_array)>0) { ?>
                    <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">Also for:</td></tr>
                    <?php foreach($nom['co_nomprds'] as $co_nomprd):
                    if(in_array($co_nomprd['prd_id'], $co_nomprds_diff_array)) { ?>
                    <tr class="row<?php echo $rowclass; ?>">
                      <td class="prdcol1"><?php echo $co_nomprd['prd_nm']; ?></td>
                      <td class="prdcol2"><?php echo $co_nomprd['thtr']; ?></td>
                      <td class="prdcol4"><?php echo $co_nomprd['prd_dts']; ?></td>
                    </tr><?php }
                    endforeach;}}
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
                        $l++; endforeach;  if(!empty($cowin['cowin_prds'])) {echo ' for:';}} ?>
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
          <input type="hidden" name="pt_id" value="<?php echo $pt_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>