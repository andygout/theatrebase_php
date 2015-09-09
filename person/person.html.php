<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (person) | TheatreBase</title>
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
        <h4>PERSON:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if($prsn_sx || $ethn || $org_lctn_nm || !empty($profs))
        { ?>
        <div id="dtls" class="box"><table class="overview">
        <?php
        if($prsn_sx) { ?><tr><td class="ovrvwcol1">Sex:</td><td><?php echo $prsn_sx; ?></td></tr><?php }
        if($ethn) { ?><tr><td class="ovrvwcol1">Ethnicity:</td><td><?php echo $ethn;
        if(!empty($rel_ethns)) {echo ' ('.implode(', ', $rel_ethns).')';} ?></td></tr><?php }
        if($org_lctn_nm) { ?><tr><td class="ovrvwcol1">Place of origin:</td><td><?php echo $org_lctn_nm;
        if(!empty($rel_lctns)) {echo ' ('.implode(' / ', $rel_lctns).')';} ?></td></tr><?php }
        if(!empty($profs)){$i=0; $prof_cnt=count($profs); ?><tr><td class="ovrvwcol1">Credited as:</td><td>
        <?php foreach($profs as $prof): echo $prof['prof_nm']; if(!empty($prof['rel_profs'])) {echo ' ('.implode(' / ', $prof['rel_profs']).')';}
        if($i<$prof_cnt-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php } ?>
        </table></div></br>
        <?php }

        if(!empty($comps))
        { ?>
        <div id="comps"><h5>Company positions</h5>
        <table class="prod1">
        <?php foreach($comps as $comp): ?>
        <tr><td><?php echo $comp['comp_nm']; ?></td><td>.....</td><td><?php echo $comp['comp_prsn_rl'].$comp['comp_prsn_yrs'].$comp['comp_prsn_rl_nt']; ?></td></tr>
        <?php endforeach; ?></table></div>
        <?php }

        if(!empty($agnts))
        { ?>
        <div id="agnts"><h5>Representation</h5><table class="prod1">
        <?php foreach($agnts as $agnt): ?>
        <tr><td><?php echo $agnt['comp_nm']; ?></td><td>.....</td>
        <td>
        <?php if(empty($agnt['agntcomp_ppl'])) {echo $agnt['agnt_rl'];}
        else {foreach($agnt['agntcomp_ppl'] as $agntcomp_prsn): echo $agntcomp_prsn['prsn_nm'].' - '.$agntcomp_prsn['agnt_rl']; ?></br><?php endforeach; } ?>
        </td></tr>
        <?php endforeach; ?>
        </table></div><?php }

        if(!empty($agnts))
        { ?>
        <div id="agnts"><table class="credits">
        <tr><th colspan="3">Representation</th></tr>
        <?php $rowclass=0;
        foreach($agnts as $agnt): ?>
          <?php if(!empty($agnt['agntcomp_ppl'])) {$i=0; ?>
          <?php foreach($agnt['agntcomp_ppl'] as $agntcomp_prsn): ?>
          <tr class="<?php if($i==0){echo 'newcredit';}else{echo 'newsubcredit';}?> row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php if($i==0) {echo $agnt['comp_nm'];} ?></td>
          <td class="prdcol2"><?php echo $agntcomp_prsn['prsn_nm']; ?></td>
          <td class="prdcol4"><?php echo $agntcomp_prsn['agnt_rl']; ?></td>
          <?php $i++; endforeach; }
          else { ?>
          <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $agnt['comp_nm']; ?></td>
          <td class="prdcol2"></td>
          <td class="prdcol4"><?php echo $agnt['agnt_rl']; ?></td>
          <?php }
          ?>
        </tr>
        <?php $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($clnts))
        { ?>
        <div id="clnts"><h5>Clients</h5><table class="prod1">
        <?php foreach($clnts as $clnt): ?>
        <tr><td><?php echo $clnt['prsn_nm']; ?></td><td>.....</td><td><?php echo $clnt['agnt_rl']; if($clnt['comp_nm']) {echo ' ('.$clnt['comp_nm'].')';} ?></td></tr>
        <?php endforeach; ?></table></div>
        <?php }

        if(!empty($clnts))
        { ?>
        <div id="clnts"><table class="credits">
        <tr><th colspan="3">Clients</th></tr>
        <?php $rowclass=0;
        foreach($clnts as $clnt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $clnt['prsn_nm']; ?></td>
          <td class="prdcol2"><?php echo $clnt['comp_nm']; ?></td>
          <td class="prdcol4"><?php echo $clnt['agnt_rl']; ?></td>
        </tr>
        <?php $rowclass=1-$rowclass;
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
        if($pt['lcnsr_rl']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <?php echo $pt['lcnsr_rl'].$pt['comp_nm']; ?></td>
        </tr>
        <?php } if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
        <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes (licensed by this person):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($pt['sg_pts'] as $pt): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
        if($pt['lcnsr_rl']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <?php echo $pt['lcnsr_rl'].$pt['comp_nm']; ?></td>
        </tr>
        <?php } endforeach; }
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
        <?php } if($prd['comp_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php
          foreach($prd['prdcr_comprls'] as $prdcr_comprl): echo $prdcr_comprl; endforeach;
          if(!empty($prd['comprl_co_ppl'])) {echo ' (with '; $k=0; $comprl_co_ppl=count($prd['comprl_co_ppl']);
          foreach($prd['comprl_co_ppl'] as $comprl_coprsn): echo $comprl_coprsn;
          if($k<$comprl_co_ppl-2) {echo ', ';} elseif($k<$comprl_co_ppl-1) {echo ' and ';} $k++; endforeach; echo ')';}
          echo $prd['comp_nm']; ?></em></td>
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
        <?php } if($prd['comp_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php
          foreach($prd['prdcr_comprls'] as $prdcr_comprl): echo $prdcr_comprl; endforeach;
          if(!empty($prd['comprl_co_ppl'])) {echo ' (with '; $k=0; $comprl_co_ppl=count($prd['comprl_co_ppl']);
          foreach($prd['comprl_co_ppl'] as $comprl_coprsn): echo $comprl_coprsn;
          if($k<$comprl_co_ppl-2) {echo ', ';} elseif($k<$comprl_co_ppl-1) {echo ' and ';} $k++; endforeach; echo ')';}
          echo $prd['comp_nm']; ?></em></td>
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
        <?php } if($prd['comp_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php
          foreach($prd['prdcr_comprls'] as $prdcr_comprl): echo $prdcr_comprl; endforeach;
          if(!empty($prd['comprl_co_ppl'])) {echo ' (with '; $j=0; $comprl_co_ppl=count($prd['comprl_co_ppl']);
          foreach($prd['comprl_co_ppl'] as $comprl_coprsn): echo $comprl_coprsn;
          if($j<$comprl_co_ppl-2) {echo ', ';} elseif($j<$comprl_co_ppl-1) {echo ' and ';} $j++; endforeach; echo ')';}
          echo $prd['comp_nm']; ?></em></td>
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
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
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
        <?php } if($prd['comp_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php
          foreach($prd['prdcr_comprls'] as $prdcr_comprl): echo $prdcr_comprl; endforeach;
          if(!empty($prd['comprl_co_ppl'])) {echo ' (with '; $j=0; $comprl_co_ppl=count($prd['comprl_co_ppl']);
          foreach($prd['comprl_co_ppl'] as $comprl_coprsn): echo $comprl_coprsn;
          if($j<$comprl_co_ppl-2) {echo ', ';} elseif($j<$comprl_co_ppl-1) {echo ' and ';} $j++; endforeach; echo ')';}
          echo $prd['comp_nm']; ?></em></td>
        </tr>
        <?php } endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($prf_prds))
        { ?>
        <div id="prf_prds"><table class="credits">
        <tr><th colspan="3">Productions as performer</th></tr>
        <?php $rowclass=0;
        foreach($prf_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        if(!empty($prd['prf_rls'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php echo implode(' / ', $prd['prf_rls']); ?></em></td>
          </tr>
        <?php } if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['sg_prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['sg_thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['sg_prd_dts']; ?></td>
        </tr>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php'; ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php echo implode(' / ', $prd['sg_prf_rls']); ?></em></td>
        </tr>
        <?php endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($us_prds))
        { ?>
        <div id="us_prds"><table class="credits">
        <tr><th colspan="3">Productions as understudy</th></tr>
        <?php $rowclass=0;
        foreach($us_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        if(!empty($prd['us_rls'])) { ?>
          <tr class="row<?php echo $rowclass; ?>">
            <td class="prdcol5" colspan="3">▪ <em><?php echo implode(' / ', $prd['us_rls']); ?></em></td>
          </tr>
        <?php } if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes:';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['sg_prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['sg_thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['sg_prd_dts']; ?></td>
        </tr>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php'; ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php echo implode(' / ', $prd['sg_us_rls']); ?></em></td>
        </tr>
        <?php endforeach; }
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
        if(!empty($prd['mscn_rls'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em>
          <?php foreach($prd['mscn_rls'] as $mscn_rl): echo $mscn_rl['mscn_rl'];
          if(!empty($mscn_rl['co_ppl'])) {echo ' (with '; $h=0; $co_ppl=count($mscn_rl['co_ppl']);
          foreach($mscn_rl['co_ppl'] as $co_prsn): echo $co_prsn;
          if($h<$co_ppl-2) {echo ', ';} elseif($h<$co_ppl-1) {echo ' and ';} $h++; endforeach; echo ')';}
          if(!empty($mscn_rl['comprl_co_ppl'])) {echo ' (with '; $i=0; $comprl_co_ppl=count($mscn_rl['comprl_co_ppl']);
          foreach($mscn_rl['comprl_co_ppl'] as $comprl_coprsn): echo $comprl_coprsn;
          if($i<$comprl_co_ppl-2) {echo ', ';} elseif($i<$comprl_co_ppl-1) {echo ' and ';} $i++; endforeach; echo ')';}
          echo $mscn_rl['comp_nm']; endforeach; ?></em></td>
        </tr>
        <?php }
        if(!empty($prd['sg_prds'])) { ?>
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
          <td class="prdcol5" colspan="3">▪ <em>
          <?php foreach($prd['mscn_rls'] as $mscn_rl): echo $mscn_rl['mscn_rl'];
          if(!empty($mscn_rl['co_ppl'])) {echo ' (with '; $j=0; $co_ppl=count($mscn_rl['co_ppl']);
          foreach($mscn_rl['co_ppl'] as $co_prsn): echo $co_prsn;
          if($j<$co_ppl-2) {echo ', ';} elseif($j<$co_ppl-1) {echo ' and ';} $j++; endforeach; echo ')';}
          if(!empty($mscn_rl['comprl_co_ppl'])) {echo ' (with '; $k=0; $comprl_co_ppl=count($mscn_rl['comprl_co_ppl']);
          foreach($mscn_rl['comprl_co_ppl'] as $comprl_coprsn): echo $comprl_coprsn;
          if($k<$comprl_co_ppl-2) {echo ', ';} elseif($k<$comprl_co_ppl-1) {echo ' and ';} $k++; endforeach; echo ')';}
          echo $mscn_rl['comp_nm']; endforeach; ?></em></td>
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
          if($crtv_rl['comp_nm']) {
          if(!empty($crtv_rl['co_comp_ppl'])) {$j=0; $co_comp_ppl=count($crtv_rl['co_comp_ppl']); echo ' (with ';
          foreach($crtv_rl['co_comp_ppl'] as $co_comp_prsn): echo $co_comp_prsn;
          if($j<$co_comp_ppl-2) {echo ', ';} elseif($j<$co_comp_ppl-1) {echo ' and ';} $j++; endforeach; echo ')';} echo $crtv_rl['comp_nm'];}
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
          if($crtv_rl['comp_nm']) {
          if(!empty($crtv_rl['co_comp_ppl'])) {$j=0; $co_comp_ppl=count($crtv_rl['co_comp_ppl']); echo ' (with ';
          foreach($crtv_rl['co_comp_ppl'] as $co_comp_prsn): echo $co_comp_prsn;
          if($j<$co_comp_ppl-2) {echo ', ';} elseif($j<$co_comp_ppl-1) {echo ' and ';} $j++; endforeach; echo ')';} echo $crtv_rl['comp_nm'];}
          if(!empty($crtv_rl['co_ppl'])) {if(!empty($crtv_rl['co_comp_ppl'])) {echo ' (also with ';} else {echo ' (with ';}
          $i=0; foreach($crtv_rl['co_ppl'] as $co_prsn): if(!empty($co_prsn['comp_ppl'])) {$i++;} endforeach;
          $k=0; $co_ppl=count($crtv_rl['co_ppl']); foreach($crtv_rl['co_ppl'] as $co_prsn):
          if(!empty($co_prsn['comp_ppl'])) {$l=0; $comp_ppl=count($co_prsn['comp_ppl']);
          foreach($co_prsn['comp_ppl'] as $comp_prsn): echo $comp_prsn;
          if($l<$comp_ppl-2) {echo ', ';} elseif($l<$comp_ppl-1) {echo ' and ';} $l++; endforeach; echo ' for ';}
          echo $co_prsn['co_prsn'];
          if($k<$co_ppl-2) {if($i>0) {echo '; ';} else {echo ', ';}} elseif($k<$co_ppl-1) {if($i>0) {echo '; and ';} else {echo ' and ';}}
          $k++; endforeach; echo ')';}
          if($h<$crtv_rls-1) {echo ' / ';} $h++; endforeach;?>
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
          if($prdtm_rl['comp_nm']) {
          if(!empty($prdtm_rl['co_comp_ppl'])) {$j=0; $co_comp_ppl=count($prdtm_rl['co_comp_ppl']); echo ' (with ';
          foreach($prdtm_rl['co_comp_ppl'] as $co_comp_prsn): echo $co_comp_prsn;
          if($j<$co_comp_ppl-2) {echo ', ';} elseif($j<$co_comp_ppl-1) {echo ' and ';} $j++; endforeach; echo ')';} echo $prdtm_rl['comp_nm'];}
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
          if($prdtm_rl['comp_nm']) {
          if(!empty($prdtm_rl['co_comp_ppl'])) {$j=0; $co_comp_ppl=count($prdtm_rl['co_comp_ppl']); echo ' (with ';
          foreach($prdtm_rl['co_comp_ppl'] as $co_comp_prsn): echo $co_comp_prsn;
          if($j<$co_comp_ppl-2) {echo ', ';} elseif($j<$co_comp_ppl-1) {echo ' and ';} $j++; endforeach;} echo ')'; echo $prdtm_rl['comp_nm'];}
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
          <td class="prdcol3" colspan="2">▪ <em>
          <?php foreach($cdntr_crs['cdntr_rls'] as $cdntr_rl): echo $cdntr_rl; endforeach;
          if(!empty($cdntr_crs['co_ppl'])) {echo ' (with '; $h=0; $co_ppl=count($cdntr_crs['co_ppl']);
          foreach($cdntr_crs['co_ppl'] as $co_prsn): echo $co_prsn;
          if($h<$co_ppl-2) {echo ', ';} elseif($h<$co_ppl-1) {echo ' and ';} $h++; endforeach; echo ')';}
          if(!empty($cdntr_crs['comprl_co_ppl'])) {echo ' (with '; $i=0; $comprl_co_ppl=count($cdntr_crs['comprl_co_ppl']);
          foreach($cdntr_crs['comprl_co_ppl'] as $comprl_coprsn): echo $comprl_coprsn;
          if($i<$comprl_co_ppl-2) {echo ', ';} elseif($i<$comprl_co_ppl-1) {echo ' and ';} $i++; endforeach; echo ')';}
          echo $cdntr_crs['comp_nm']; ?></em></td>
        </tr>
        <?php $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($stff_prsn_crss))
        { ?>
        <div id="stff_prsn_crss"><table class="credits">
        <tr><th colspan="2">Courses as staff</th></tr>
        <?php $rowclass=0;
        foreach($stff_prsn_crss as $stff_prsn_crs): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol3"><?php echo $stff_prsn_crs['crs_nm']; ?></td>
          <td class="prdcol4"><?php echo $stff_prsn_crs['crs_dts']; ?></td>
        </tr>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol3" colspan="2">▪ <em><?php echo $stff_prsn_crs['stff_prsn_rl']; ?></em></td>
        </tr>
        <?php $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($stdnt_prsn_crss))
        { ?>
        <div id="stdnt_prsn_crss"><table class="credits">
        <tr><th colspan="2">Courses as student</th></tr>
        <?php $rowclass=0;
        foreach($stdnt_prsn_crss as $stdnt_prsn_crs): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol3"><?php echo $stdnt_prsn_crs['crs_nm']; ?></td>
          <td class="prdcol4"><?php echo $stdnt_prsn_crs['crs_dts']; ?></td>
        </tr>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol3" colspan="2">▪ <em><?php echo $stdnt_prsn_crs['stdnt_prsn_rl']; ?></em></td>
        </tr>
        <?php $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if(!empty($rvw_crtc_prds))
        { ?>
        <div id="rvw_crtc_prds"><table class="credits">
        <tr><th colspan="3">Productions reviewed as theatre critic (from past year)</th></tr>
        <?php $rowclass=0;
        foreach($rvw_crtc_prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr'] ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts'] ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';}
        foreach($prd['rvws'] as $rvw): ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em>Reviewed for <?php echo $rvw['comp_nm'].' ('.$rvw['rvw_dt'].') ['.$rvw['rvw_url'].']'; ?></em></td>
        </tr>
        <?php endforeach;
        if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (reviewed by this critic):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        foreach($prd['rvws'] as $rvw): ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em>Reviewed for <?php echo $rvw['comp_nm'].' ('.$rvw['rvw_dt'].') ['.$rvw['rvw_url'].']'; ?></em></td>
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
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_cntr_dsply.inc.html.php';
        endforeach; }
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
                <?php $f=0; foreach($awrd_yr['ctgrys'] as $ctgry): foreach($ctgry['noms'] as $nom): ?>
                  <tr class="<?php if($f==0){echo 'newcredit';}else{echo 'newsubcredit';}?> row<?php echo $rowclass; ?>">
                    <td class="prdcol5" colspan="3">▪
                      <?php if($nom['win']) {echo '<b>';} echo $ctgry['awrd_ctgry_nm'].' - '.$nom['nom_win_dscr']; if($nom['win']) {echo '</b>';} echo $nom['nom_rl'];
                      if($nom['comp_nm']) {echo ' (';
                      if(!empty($nom['nom_co_comp_ppl'])) {$g=0; $nom_co_comp_ppl=count($nom['nom_co_comp_ppl']); echo 'with ';
                      foreach($nom['nom_co_comp_ppl'] as $nom_co_comp_prsn): echo $nom_co_comp_prsn;
                      if($g<$nom_co_comp_ppl-2) {echo ', ';} elseif($g<$nom_co_comp_ppl-1) {echo ' and ';} $g++; endforeach; echo ' ';}
                      echo 'for '.$nom['comp_nm'].')';}
                      if(!empty($nom['co_nomppl'])) {echo ' (shared with: ';
                      $h=0; foreach($nom['co_nomppl'] as $co_nomprsn): if(!empty($co_nomprsn['co_nomcomp_ppl'])) {$h++;} endforeach;
                      $i=0; $ppl=count($nom['co_nomppl']); foreach($nom['co_nomppl'] as $co_nomprsn):
                      if(!empty($co_nomprsn['co_nomcomp_ppl'])) {$j=0; $co_compppl=count($co_nomprsn['co_nomcomp_ppl']);
                      foreach($co_nomprsn['co_nomcomp_ppl'] as $co_nomcomp_prsn): echo $co_nomcomp_prsn;
                      if($j<$co_compppl-2) {echo ', ';} elseif($j<$co_compppl-1) {echo ' and ';} $j++; endforeach; echo ' for ';}
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
                  <?php endforeach; }
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
                $f++;
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
          <input type="hidden" name="prsn_id" value="<?php echo $prsn_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>