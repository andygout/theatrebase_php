<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (company) | TheatreBase</title>
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

        <h4>COMPANY:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(!preg_match('/^the-company$/', $comp_url))
        {
          if(!empty($adrss))
          { ?>
          <div id="comp_adrss" class="box"><table class="overview">
          <?php if(!empty($adrss)) {$h=0; foreach($adrss as $adrs): ?><tr><td class="ovrvwcol1">
          <?php if($h==0) {echo 'Address'; if(count($adrss)>1) {echo 'es';} echo ':';} ?></td><td><?php echo $adrs; ?></td></tr><?php $h++; endforeach;} ?>
          </table></div></br>
          <?php }

          if($comp_reg_nm || $comp_est_dt || $comp_dslv_dt || !empty($sbsqs) || !empty($prvss))
          { ?>
          <div id="comp_clssfctn" class="box"><table class="overview">
          <?php if($comp_reg_nm) { ?><tr><td class="ovrvwcol1">Registered name:</td><td><?php echo $comp_reg_nm; ?></td></tr>
          <?php if($comp_reg_adrs) { ?><tr><td class="ovrvwcol1">Registered office:</td><td><?php echo $comp_reg_adrs; ?></td></tr><?php }}
          if($comp_dslv_dt) { ?><tr><td class="ovrvwcol1">Dissolved:</td><td><?php echo $comp_dslv_dt; ?></td></tr><?php }
          if($comp_est_dt) { ?><tr><td class="ovrvwcol1">Established:</td><td><?php echo $comp_est_dt; ?></td></tr><?php }
          if(!empty($sbsqs)) { ?><tr><td class="ovrvwcol1">Subsequently named:</td><td><?php echo implode('</br>', $sbsqs); ?></td></tr><?php }
          if(!empty($sbsqs) || !empty($prvss)) {if($comp_nm_dt) { ?><tr><td class="ovrvwcol1">Named:</td><td><?php echo $comp_nm.$comp_nm_dt; ?></td></tr><?php }}
          if(!empty($prvss)) { ?><tr><td class="ovrvwcol1">Previously named:</td><td><?php echo implode('</br>', $prvss); ?></td></tr><?php } ?>
          </table></div></br>
          <?php }

          if(!empty($lctns) || !empty($comp_typs))
          { ?>
          <div id="comp_clssfctn" class="box"><table class="overview">
          <?php if(!empty($lctns)) {$i=0; $lctn_cnt=count($lctns); ?><tr><td class="ovrvwcol1">Location<?php if($lctn_cnt>1) {echo 's';} ?>:</td><td><?php foreach($lctns as $lctn):
          echo $lctn['lctn']; if(!empty($lctn['rel_lctns'])) {echo ' ('.implode(' / ', $lctn['rel_lctns']).')';} if($i<$lctn_cnt-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
          if(!empty($comp_typs)) { ?><tr><td class="ovrvwcol1">Type:</td><td><?php echo implode(' / ', $comp_typs); ?></td></tr><?php } ?>
          </table></div></br>
          <?php }

          if(!empty($comp_ppl))
          { ?>
          <div id="comp_ppl"><h5>Company members</h5>
          <table class="prod1">
          <?php foreach($comp_ppl as $comp_prsn): ?>
          <tr><td><?php echo $comp_prsn['prsn_nm']; ?></td><td>.....</td><td><?php echo $comp_prsn['comp_prsn_rl'].$comp_prsn['comp_prsn_yrs'].$comp_prsn['comp_prsn_rl_nt']; ?></td></tr>
          <?php endforeach; ?></table></div>
          <?php }

          if(!empty($clnts))
          { ?>
          <div id="clnts"><h5>Clients</h5>
          <table class="prod1">
          <?php foreach($clnts as $clnt): ?>
          <tr><td><?php echo $clnt['prsn_nm']; ?></a></td><td>.....</td>
          <td>
          <?php if(empty($clnt['agnts'])) {echo $clnt['agnt_rl']; ?>
          <?php } else { foreach($clnt['agnts'] as $agnt): echo $agnt['prsn_nm'].' - '.$agnt['agnt_rl']; ?></br><?php endforeach; } ?>
          </td></tr>
          <?php endforeach; ?>
          </table></div><?php }

          if(!empty($clnts))
          { ?>
          <div id="clnts"><table class="credits">
          <tr><th colspan="3">Clients</th></tr>
          <?php $rowclass=0;
          foreach($clnts as $clnt): ?>
            <?php if(!empty($clnt['agnts'])) {$i=0; ?>
            <?php foreach($clnt['agnts'] as $agnt): ?>
            <tr class="<?php if($i==0){echo 'newcredit';}else{echo 'newsubcredit';}?> row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php if($i==0) {echo $clnt['prsn_nm'];} ?></td>
            <td class="prdcol2"><?php echo $agnt['prsn_nm']; ?></td>
            <td class="prdcol4"><?php echo $agnt['agnt_rl']; ?></td>
            <?php $i++; endforeach; }
            else { ?>
            <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $clnt['prsn_nm']; ?></td>
            <td class="prdcol2"></td>
            <td class="prdcol4"><?php echo $agnt['agnt_rl']; ?></td>
            <?php }
            ?>
          </tr>
          <?php $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
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
          <?php if(!empty($thtr['co_ownr_comps'])) {$n=0; $co_ownr_comps=count($thtr['co_ownr_comps']); ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5"><?php echo ' Co-owned with: ';
            foreach($thtr['co_ownr_comps'] as $co_ownr_comp): echo $co_ownr_comp; if($n<$co_ownr_comps-2) {echo ', ';} elseif($n<$co_ownr_comps-1) {echo ' and ';} $n++; endforeach; ?>
            </td>
          </tr>
          <?php } if(!empty($thtr['sbthtrs'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5">Comprises:</td></tr>
          <?php foreach($thtr['sbthtrs'] as $sbthtr): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol5"><?php echo $sbthtr['sbthtr'];
            if(!empty($sbthtr['sbthtr_sbsq_nms'])) {echo ' (Subsequently named: '.implode(' / ', $sbthtr['sbthtr_sbsq_nms']).')';}
            if(!empty($sbthtr['sbthtr_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $sbthtr['sbthtr_prvs_nms']).')';} ?>
            </td>
          </tr>
          <?php endforeach; }
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($lcnsr_pts))
          { ?>
          <div id="lcnsr_pts"><table class="credits">
          <tr><th colspan="3">Playtexts as licensor</th></tr>
          <?php $rowclass=0;
          foreach($lcnsr_pts as $pt): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
            <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
            <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
          </tr>
          <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
          if((empty($pt['lcnsr_ppl']) && $pt['lcnsr_rl']) || !empty($pt['lcnsr_ppl'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">
              <?php if(empty($pt['lcnsr_ppl'])) {echo '▪ '.$pt['lcnsr_rl'];}
              else {foreach($pt['lcnsr_ppl'] as $lcnsr_prsn): echo '▪ '.$lcnsr_prsn['prsn_nm'].' - '.$lcnsr_prsn['lcnsr_rl'].'</br>'; endforeach;} ?>
            </td>
          </tr>
          <?php } if(!empty($pt['sg_pts'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
          <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes (licensed by this company):';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($pt['sg_pts'] as $pt): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
            <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
            <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
          </tr>
          <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
          if((empty($pt['lcnsr_ppl']) && $pt['lcnsr_rl']) || !empty($pt['lcnsr_ppl'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">
              <?php if(empty($pt['lcnsr_ppl'])) {echo '▪ '.$pt['lcnsr_rl'];}
              else {foreach($pt['lcnsr_ppl'] as $lcnsr_prsn): echo '▪ '.$lcnsr_prsn['prsn_nm'].' - '.$lcnsr_prsn['lcnsr_rl'].'</br>'; endforeach;} ?>
            </td>
          </tr>
          <?php } endforeach; }
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($prdcr_prds))
          { ?>
          <div id="prdcr_prds"><table class="credits">
          <tr><th colspan="3">Productions as producer</th></tr>
          <?php $rowclass=0;
          foreach($prdcr_prds as $prd): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
          if(!empty($prd['prdcr_rls'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php
            foreach($prd['prdcr_rls'] as $prdcr_rl):
            $h=0; foreach($prdcr_rl['prdcrs'] as $prdcr): if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$h++;} endforeach;
            $i=0; $ppl=count($prdcr_rl['prdcrs']); echo $prdcr_rl['prdcr_rl'].' ';
            foreach($prdcr_rl['prdcrs'] as $prdcr):
            if($i>0 && $i<$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; ';} else {echo ', ';}}
            elseif($i>0 && $i==$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; and ';} else {echo ' and ';}}
            echo ' '.$prdcr['prdcr_sb_rl'];
            if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$j=0; $ppl_crdt=count($prdcr['prdcrcomp_ppl_crdt']);
            foreach($prdcr['prdcrcomp_ppl_crdt'] as $prdcrcomp_prsn_crdt): echo $prdcrcomp_prsn_crdt['prsn_nm'];
            if($j<$ppl_crdt-2) {echo ', ';} if($j==$ppl_crdt-2) {echo ' and ';} $j++; endforeach; echo ' for ';}
            echo $prdcr['comp_nm']; $i++; endforeach; endforeach; ?>
            </em></td>
          </tr>
          <?php } if(!empty($prd['sg_prds'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
          <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($prd['sg_prds'] as $prd): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php'; ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php
            foreach($prd['prdcr_rls'] as $prdcr_rl):
            $h=0; foreach($prdcr_rl['prdcrs'] as $prdcr): if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$h++;} endforeach;
            $i=0; $ppl=count($prdcr_rl['prdcrs']); echo $prdcr_rl['prdcr_rl'].' ';
            foreach($prdcr_rl['prdcrs'] as $prdcr):
            if($i>0 && $i<$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; ';} else {echo ', ';}}
            elseif($i>0 && $i==$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; and ';} else {echo ' and ';}}
            echo ' '.$prdcr['prdcr_sb_rl'];
            if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$j=0; $ppl_crdt=count($prdcr['prdcrcomp_ppl_crdt']);
            foreach($prdcr['prdcrcomp_ppl_crdt'] as $prdcrcomp_prsn_crdt): echo $prdcrcomp_prsn_crdt['prsn_nm'];
            if($j<$ppl_crdt-2) {echo ', ';} if($j==$ppl_crdt-2) {echo ' and ';} $j++; endforeach; echo ' for ';}
            echo $prdcr['comp_nm']; $i++; endforeach; endforeach; ?>
            </em></td>
          </tr>
          <?php endforeach; }
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($wri_prds))
          { ?>
          <div id="wri_prds"><table class="credits">
          <tr><th colspan="3">Productions as writer</th></tr>
          <?php $rowclass=0;
          foreach($wri_prds as $prd): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
          if(!empty($prd['sg_prds'])) { ?>
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

          if(!empty($grntr_prds))
          { ?>
          <div id="grntr_prds"><table class="credits">
          <tr><th colspan="3">Productions as rights grantor</th></tr>
          <?php $rowclass=0;
          foreach($grntr_prds as $prd): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
          if(!empty($prd['prdcr_rls'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php
            foreach($prd['prdcr_rls'] as $prdcr_rl):
            $h=0; foreach($prdcr_rl['prdcrs'] as $prdcr): if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$h++;} endforeach;
            $i=0; $ppl=count($prdcr_rl['prdcrs']); echo $prdcr_rl['prdcr_rl'].' ';
            foreach($prdcr_rl['prdcrs'] as $prdcr):
            if($i>0 && $i<$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; ';} else {echo ', ';}}
            elseif($i>0 && $i==$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; and ';} else {echo ' and ';}}
            echo ' '.$prdcr['prdcr_sb_rl'];
            if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$j=0; $ppl_crdt=count($prdcr['prdcrcomp_ppl_crdt']);
            foreach($prdcr['prdcrcomp_ppl_crdt'] as $prdcrcomp_prsn_crdt): echo $prdcrcomp_prsn_crdt['prsn_nm'];
            if($j<$ppl_crdt-2) {echo ', ';} if($j==$ppl_crdt-2) {echo ' and ';} $j++; endforeach; echo ' for ';}
            echo $prdcr['comp_nm']; $i++; endforeach; endforeach; ?>
            </em></td>
          </tr>
          <?php } if(!empty($prd['sg_prds'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
          <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($prd['sg_prds'] as $prd): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';} if(!empty($prd['prdcr_rls'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php
            foreach($prd['prdcr_rls'] as $prdcr_rl):
            $h=0; foreach($prdcr_rl['prdcrs'] as $prdcr): if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$h++;} endforeach;
            $i=0; $ppl=count($prdcr_rl['prdcrs']); echo $prdcr_rl['prdcr_rl'].' ';
            foreach($prdcr_rl['prdcrs'] as $prdcr):
            if($i>0 && $i<$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; ';} else {echo ', ';}}
            elseif($i>0 && $i==$ppl-1 && !$prdcr['prdcr_sb_rl']) {if($h>0) {echo '; and ';} else {echo ' and ';}}
            echo ' '.$prdcr['prdcr_sb_rl'];
            if(!empty($prdcr['prdcrcomp_ppl_crdt'])) {$j=0; $ppl_crdt=count($prdcr['prdcrcomp_ppl_crdt']);
            foreach($prdcr['prdcrcomp_ppl_crdt'] as $prdcrcomp_prsn_crdt): echo $prdcrcomp_prsn_crdt['prsn_nm'];
            if($j<$ppl_crdt-2) {echo ', ';} if($j==$ppl_crdt-2) {echo ' and ';} $j++; endforeach; echo ' for ';}
            echo $prdcr['comp_nm']; $i++; endforeach; endforeach; ?>
            </em></td>
          </tr>
          <?php } endforeach;}
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($mscn_prds))
          { ?>
          <div id="mscn_prds"><table class="credits">
          <tr><th colspan="3">Productions as musician</th></tr>
          <?php $rowclass=0;
          foreach($mscn_prds as $prd): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
          if($prd['mscn_rl']) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php echo $prd['mscn_rl'];
            if(!empty($prd['co_ppl'])) {echo ' (with ';
            $k=0; $co_ppl=count($prd['co_ppl']); foreach($prd['co_ppl'] as $co_prsn): echo $co_prsn['co_prsn'];
            if($k<$co_ppl-2) {echo ', ';} elseif($k<$co_ppl-1) {echo ' and ';} $k++; endforeach; echo ')';} ?>
            </em></td>
          </tr>
          <?php } if(!empty($prd['sg_prds'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
          <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($prd['sg_prds'] as $prd): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php'; ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php echo $prd['mscn_rl'];
            if(!empty($prd['co_ppl'])) {echo ' (with ';
            $k=0; $co_ppl=count($prd['co_ppl']); foreach($prd['co_ppl'] as $co_prsn): echo $co_prsn['co_prsn'];
            if($k<$co_ppl-2) {echo ', ';} elseif($k<$co_ppl-1) {echo ' and ';} $k++; endforeach; echo ')';} ?>
            </em></td>
          </tr>
          <?php endforeach; }
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($crtv_prds))
          { ?>
          <div id="crtv_prds"><table class="credits">
          <tr><th colspan="3">Productions as creative</th></tr>
          <?php $rowclass=0;
          foreach($crtv_prds as $prd): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
          if(!empty($prd['crtv_rls'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php
            $h=0; $crtv_rls=count($prd['crtv_rls']); foreach($prd['crtv_rls'] as $crtv_rl): echo $crtv_rl['crtv_rl'];
            if(!empty($crtv_rl['co_comp_ppl'])) {$j=0; $co_comp_ppl=count($crtv_rl['co_comp_ppl']); echo ': ';
            foreach($crtv_rl['co_comp_ppl'] as $co_comp_prsn): echo $co_comp_prsn;
            if($j<$co_comp_ppl-2) {echo ', ';} elseif($j<$co_comp_ppl-1) {echo ' and ';} $j++; endforeach;}
            if(!empty($crtv_rl['co_ppl'])) {if(!empty($crtv_rl['co_comp_ppl'])) {echo ' (also with ';} else {echo ' (with ';}
            $i=0; foreach($crtv_rl['co_ppl'] as $co_prsn): if(!empty($co_prsn['comp_ppl'])) {$i++;} endforeach;
            $k=0; $co_ppl=count($crtv_rl['co_ppl']); foreach($crtv_rl['co_ppl'] as $co_prsn):
            if(!empty($co_prsn['comp_ppl'])) {$l=0; $comp_ppl=count($co_prsn['comp_ppl']);
            foreach($co_prsn['comp_ppl'] as $comp_prsn): echo $comp_prsn;
            if($l<$comp_ppl-2) {echo ', ';} elseif($l<$comp_ppl-1) {echo ' and ';} $l++; endforeach; echo ' for ';}
            echo $co_prsn['co_prsn'];
            if($k<$co_ppl-2) {if($i>0) {echo '; ';} else {echo ', ';}} elseif($k<$co_ppl-1) {if($i>0) {echo '; and ';} else {echo ' and ';}}
            $k++; endforeach; echo ')';}
            if($h<$crtv_rls-1) {echo ' / ';} $h++; endforeach; ?>
            </em></td>
          </tr>
          <?php } if(!empty($prd['sg_prds'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
          <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($prd['sg_prds'] as $prd): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php'; ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php
            $h=0; $crtv_rls=count($prd['crtv_rls']); foreach($prd['crtv_rls'] as $crtv_rl): echo $crtv_rl['crtv_rl'];
            if(!empty($crtv_rl['co_comp_ppl'])) {$j=0; $co_comp_ppl=count($crtv_rl['co_comp_ppl']); echo ': ';
            foreach($crtv_rl['co_comp_ppl'] as $co_comp_prsn): echo $co_comp_prsn;
            if($j<$co_comp_ppl-2) {echo ', ';} elseif($j<$co_comp_ppl-1) {echo ' and ';} $j++; endforeach;}
            if(!empty($crtv_rl['co_ppl'])) {if(!empty($crtv_rl['co_comp_ppl'])) {echo ' (also with ';} else {echo ' (with ';}
            $i=0; foreach($crtv_rl['co_ppl'] as $co_prsn): if(!empty($co_prsn['comp_ppl'])) {$i++;} endforeach;
            $k=0; $co_ppl=count($crtv_rl['co_ppl']); foreach($crtv_rl['co_ppl'] as $co_prsn):
            if(!empty($co_prsn['comp_ppl'])) {$l=0; $comp_ppl=count($co_prsn['comp_ppl']);
            foreach($co_prsn['comp_ppl'] as $comp_prsn): echo $comp_prsn;
            if($l<$comp_ppl-2) {echo ', ';} elseif($l<$comp_ppl-1) {echo ' and ';} $l++; endforeach; echo ' for ';}
            echo $co_prsn['co_prsn'];
            if($k<$co_ppl-2) {if($i>0) {echo '; ';} else {echo ', ';}} elseif($k<$co_ppl-1) {if($i>0) {echo '; and ';} else {echo ' and ';}}
            $k++; endforeach; echo ')';}
            if($h<$crtv_rls-1) {echo ' / ';} $h++; endforeach; ?>
            </em></td>
          </tr>
          <?php endforeach; }
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($prdtm_prds))
          { ?>
          <div id="prdtm_prds"><table class="credits">
          <tr><th colspan="3">Productions as part of production team</th></tr>
          <?php $rowclass=0;
          foreach($prdtm_prds as $prd): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
          if(!empty($prd['prdtm_rls'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php
            $h=0; $prdtm_rls=count($prd['prdtm_rls']); foreach($prd['prdtm_rls'] as $prdtm_rl): echo $prdtm_rl['prdtm_rl'];
            if(!empty($prdtm_rl['co_comp_ppl'])) {$j=0; $co_comp_ppl=count($prdtm_rl['co_comp_ppl']); echo ': ';
            foreach($prdtm_rl['co_comp_ppl'] as $co_comp_prsn): echo $co_comp_prsn;
            if($j<$co_comp_ppl-2) {echo ', ';} elseif($j<$co_comp_ppl-1) {echo ' and ';} $j++; endforeach;}
            if(!empty($prdtm_rl['co_ppl'])) {if(!empty($prdtm_rl['co_comp_ppl'])) {echo ' (also with ';} else {echo ' (with ';}
            $i=0; foreach($prdtm_rl['co_ppl'] as $co_prsn): if(!empty($co_prsn['comp_ppl'])) {$i++;} endforeach;
            $k=0; $co_ppl=count($prdtm_rl['co_ppl']); foreach($prdtm_rl['co_ppl'] as $co_prsn):
            if(!empty($co_prsn['comp_ppl'])) {$l=0; $comp_ppl=count($co_prsn['comp_ppl']);
            foreach($co_prsn['comp_ppl'] as $comp_prsn): echo $comp_prsn;
            if($l<$comp_ppl-2) {echo ', ';} elseif($l<$comp_ppl-1) {echo ' and ';} $l++; endforeach; echo ' for ';}
            echo $co_prsn['co_prsn'];
            if($k<$co_ppl-2) {if($i>0) {echo '; ';} else {echo ', ';}} elseif($k<$co_ppl-1) {if($i>0) {echo '; and ';} else {echo ' and ';}}
            $k++; endforeach; echo ')';}
            if($h<$prdtm_rls-1) {echo ' / ';} $h++; endforeach; ?>
            </em></td>
          </tr>
          <?php } if(!empty($prd['sg_prds'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
          <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($prd['sg_prds'] as $prd): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php'; ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php
            $h=0; $prdtm_rls=count($prd['prdtm_rls']); foreach($prd['prdtm_rls'] as $prdtm_rl): echo $prdtm_rl['prdtm_rl'];
            if(!empty($prdtm_rl['co_comp_ppl'])) {$j=0; $co_comp_ppl=count($prdtm_rl['co_comp_ppl']); echo ': ';
            foreach($prdtm_rl['co_comp_ppl'] as $co_comp_prsn): echo $co_comp_prsn;
            if($j<$co_comp_ppl-2) {echo ', ';} elseif($j<$co_comp_ppl-1) {echo ' and ';} $j++; endforeach;}
            if(!empty($prdtm_rl['co_ppl'])) {if(!empty($prdtm_rl['co_comp_ppl'])) {echo ' (also with ';} else {echo ' (with ';}
            $i=0; foreach($prdtm_rl['co_ppl'] as $co_prsn): if(!empty($co_prsn['comp_ppl'])) {$i++;} endforeach;
            $k=0; $co_ppl=count($prdtm_rl['co_ppl']); foreach($prdtm_rl['co_ppl'] as $co_prsn):
            if(!empty($co_prsn['comp_ppl'])) {$l=0; $comp_ppl=count($co_prsn['comp_ppl']);
            foreach($co_prsn['comp_ppl'] as $comp_prsn): echo $comp_prsn;
            if($l<$comp_ppl-2) {echo ', ';} elseif($l<$comp_ppl-1) {echo ' and ';} $l++; endforeach; echo ' for ';}
            echo $co_prsn['co_prsn'];
            if($k<$co_ppl-2) {if($i>0) {echo '; ';} else {echo ', ';}} elseif($k<$co_ppl-1) {if($i>0) {echo '; and ';} else {echo ' and ';}}
            $k++; endforeach; echo ')';}
            if($h<$prdtm_rls-1) {echo ' / ';} $h++; endforeach; ?>
            </em></td>
          </tr>
          <?php endforeach; }
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($crs_prds))
          { ?>
          <div id="crs_prds"><table class="credits">
          <tr><th colspan="3">Course productions</th></tr>
          <?php $rowclass=0;
          foreach($crs_prds as $prd): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
          if(!empty($prd['prd_crss'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3"><em>▪ <?php echo implode(' / ', $prd['prd_crss']) ?></em></td>
          </tr>
          <?php } if(!empty($prd['sg_prds'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
          <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($prd['sg_prds'] as $prd): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';} ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3"><em>▪ <?php echo implode(' / ', $prd['prd_crss']) ?></em></td>
          </tr>
          <?php endforeach; }
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($crs_typs))
          { ?>
          <div id="crs_typs"><table class="credits">
          <tr><th colspan="2">Courses</th></tr>
          <?php $rowclass=0;
          foreach($crs_typs as $crs_typ): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol3"><?php echo $crs_typ['crs_typ']; ?></td><td class="prdcol4"><?php echo $crs_typ['crs_typ_dts']; ?></td>
          </tr>
          <?php $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($cdntr_crss))
          { ?>
          <div id="cdntr_crss"><table class="credits">
          <tr><th colspan="2">Courses as coordinator</th></tr>
          <?php $rowclass=0;
          foreach($cdntr_crss as $cdntr_crs): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol3"><?php echo $cdntr_crs['crs_nm']; ?></td>
            <td class="prdcol4"><?php echo $cdntr_crs['crs_dts']; ?></td>
          </tr>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol3" colspan="2">▪ <em><?php echo $cdntr_crs['cdntr_rl'];
            if(!empty($cdntr_crs['co_ppl'])) {echo ' (with ';
            $k=0; $co_ppl=count($cdntr_crs['co_ppl']); foreach($cdntr_crs['co_ppl'] as $co_prsn): echo $co_prsn['co_prsn'];
            if($k<$co_ppl-2) {echo ', ';} elseif($k<$co_ppl-1) {echo ' and ';} $k++; endforeach; echo ')';} ?>
            </em></td>
          </tr>
          <?php $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($rvw_pub_prds))
          { ?>
          <div id="rvw_pub_prds"><table class="credits">
          <tr><th colspan="3">Productions reviewed (from past year)</th></tr>
          <?php $rowclass=0;
          foreach($rvw_pub_prds as $prd): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr'] ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts'] ?></td>
          </tr>
          <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
          foreach($prd['rvws'] as $rvw): ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em>Reviewed by <?php echo $rvw['prsn_nm'].' ('.$rvw['rvw_dt'].') ['.$rvw['rvw_url'].']'; ?></em></td>
          </tr>
          <?php endforeach;
          if(!empty($prd['sg_prds'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
          <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (reviewed by this publication):';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($prd['sg_prds'] as $prd): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
            <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
            <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
          </tr>
          <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
          foreach($prd['rvws'] as $rvw): ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em>Reviewed by <?php echo $rvw['prsn_nm'].' ('.$rvw['rvw_dt'].') ['.$rvw['rvw_url'].']'; ?></em></td>
          </tr>
          <?php endforeach; endforeach; }
          $rowclass=1-$rowclass;
          endforeach; ?>
          </table></div>
          <?php }

          if(!empty($wri_pts))
          { ?>
          <div id="wri_pts"><table class="credits">
          <tr><th colspan="3">Playtext bibliography</th></tr>
          <?php $rowclass=0;
          foreach($wri_pts as $pt): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
            <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
            <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
          </tr>
          <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
          if(!empty($pt['sg_pts'])) { ?>
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

          if(!empty($org_wri_pts))
          { ?>
          <div id="org_wri_pts"><table class="credits">
          <tr><th colspan="3">Subsequent versions of their work</th></tr>
          <?php $rowclass=0;
          foreach($org_wri_pts as $pt): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
            <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
            <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
          </tr>
          <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
          if(!empty($pt['sg_pts'])) { ?>
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

          if(!empty($src_wri_pts))
          { ?>
          <div id="src_wri_pts"><table class="credits">
          <tr><th colspan="3">Playtexts using their work as source material</th></tr>
          <?php $rowclass=0;
          foreach($src_wri_pts as $pt): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
            <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
            <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
          </tr>
          <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
          if(!empty($pt['sg_pts'])) { ?>
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

          if(!empty($grntr_pts))
          { ?>
          <div id="grntr_pts"><table class="credits">
          <tr><th colspan="3">Playtexts as rights grantor</th></tr>
          <?php $rowclass=0;
          foreach($grntr_pts as $pt): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
            <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
            <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
          </tr>
          <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
          if(!empty($pt['sg_pts'])) { ?>
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

          if(!empty($cntr_pts))
          { ?>
          <div id="cntr_pts"><table class="credits">
          <tr><th colspan="3">Contributions to playtexts</th></tr>
          <?php $rowclass=0;
          foreach($cntr_pts as $pt): ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
            <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
            <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
            <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
          </tr>
          <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
          if(!empty($pt['cntr_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_cntr_dsply.inc.html.php';}
          if(!empty($pt['sg_pts'])) { ?>
          <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
          <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
          <?php foreach($pt['sg_pts'] as $pt): ?>
          <tr class="newsubcredit row<?php echo $rowclass; ?>">
            <td class="ptcol1"><?php echo $pt['sg_pt_nm']; ?></td>
            <td class="ptcol2"><?php echo $pt['sg_txt_vrsn_nm']; ?></td>
            <td class="ptcol3"><?php echo $pt['sg_pt_yr']; ?></td>
          </tr>
          <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php'; ?>
          <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_cntr_dsply.inc.html.php'; ?>
          <?php endforeach; }
          if(!empty($pt['wrks_sg_pts'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="ptcol4" colspan="3"><?php echo 'Comprised of: '.implode(' / ', $pt['wrks_sg_pts']); ?></td>
          </tr>
          <?php }
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
                        <?php if($nom['win']) {echo '<b>';} echo $ctgry['awrd_ctgry_nm'].' - '.$nom['nom_win_dscr']; if($nom['win']) {echo '</b>';} echo $nom['nom_rl'];
                        if(!empty($nom['nomcomp_ppl'])) {$g=0; $compppl=count($nom['nomcomp_ppl']);
                        echo ' ('; foreach($nom['nomcomp_ppl'] as $nomcomp_prsn): echo $nomcomp_prsn;
                        if($g<$compppl-2) {echo ', ';} elseif($g<$compppl-1) {echo ' and ';} $g++; endforeach; echo ')'; }
                        if(!empty($nom['co_nomppl'])) {echo ' (shared with: ';
                        $h=0; foreach($nom['co_nomppl'] as $co_nomprsn): if(!empty($co_nomprsn['co_nomcomp_ppl'])) {$h++;} endforeach;
                        $i=0; $ppl=count($nom['co_nomppl']); foreach($nom['co_nomppl'] as $co_nomprsn):
                        if(!empty($co_nomprsn['co_nomcomp_ppl'])) {$j=0; $co_compppl=count($co_nomprsn['co_nomcomp_ppl']);
                        foreach($co_nomprsn['co_nomcomp_ppl'] as $co_nomcomp_prsn): echo $co_nomcomp_prsn;
                        if($j<$co_compppl-2) {echo ', ';} elseif($j<$co_compppl-1) {echo ' and ';} $j++; endforeach; echo ' for '; }
                        echo $co_nomprsn['nom_prsn'];
                        if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}} elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}}
                        $i++; endforeach; echo ')';}
                        if(!empty($nom['nomprds'])) {echo ' for:';}
                        if(!empty($nom['nompts'])) {$k=0; $nom_pts=count($nom['nompts']); echo ' for ';
                        foreach($nom['nompts'] as $nom_pt): echo $nom_pt;
                        if($k<$nom_pts-2) {echo ', ';} elseif($k<$nom_pts-1) {echo ' and ';} $k++; endforeach;} ?>
                      </td>
                    </tr>
                    <?php if(!empty($nom['nomprds'])) {
                    foreach($nom['nomprds'] as $nomprd): ?>
                      <tr class="row<?php echo $rowclass; ?>">
                        <td class="prdcol1"><?php echo $nomprd['prd_nm']; ?></td>
                        <td class="prdcol2"><?php echo $nomprd['thtr']; ?></td>
                        <td class="prdcol4"><?php echo $nomprd['prd_dts']; ?></td>
                      </tr>
                    <?php endforeach;}
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
                        $l++; endforeach;}
                        if(!empty($cowin['cowin_prds'])) {echo ' for:';}
                        if(!empty($cowin['cowin_pts'])) {$n=0; $cowin_pts=count($cowin['cowin_pts']); if(!empty($cowin['cowin_ppl'])) {echo ' for ';}
                        foreach($cowin['cowin_pts'] as $cowin_pt): echo $cowin_pt;
                        if($n<$cowin_pts-2) {echo ', ';} elseif($n<$cowin_pts-1) {echo ' and ';} $n++; endforeach;} ?>
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
          }
        } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="comp_id" value="<?php echo $comp_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>