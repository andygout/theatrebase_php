<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (character) | TheatreBase</title>
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
        <h4>CHARACTER:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if($char_sx || $char_age || $char_dscr || $char_amnt)
        { ?>
        <div id="dtls" class="box"><table class="overview">
        <?php
        if($char_sx){ ?><tr><td class="ovrvwcol1">Sex:</td><td><?php echo $char_sx; ?></td></tr><?php }
        if($char_age){ ?><tr><td class="ovrvwcol1">Age:</td><td><?php echo $char_age; ?></td></tr><?php }
        if($char_dscr){ ?><tr><td class="ovrvwcol1">Description:</td><td><?php echo $char_dscr; ?></td></tr><?php }
        if($char_amnt){ ?><tr><td class="ovrvwcol1">Amount:</td><td><?php echo $char_amnt; ?></td></tr><?php }
        ?>
        </table></div></br>
        <?php } ?>

        <?php if(!empty($ethns) || !empty($org_lctns) || !empty($profs) || !empty($attrs) || !empty($abils))
        { ?>
        <div id="dscrptn" class="box"><table class="overview">
        <?php
        if(!empty($ethns)){$i=0; $ethn_cnt=count($ethns); ?><tr><td class="ovrvwcol1">Ethnicity:</td><td>
        <?php foreach($ethns as $ethn): echo $ethn['ethn_nm']; if(!empty($ethn['rel_ethns'])) {echo ' ('.implode(' / ', $ethn['rel_ethns']).')';}
        if($i<$ethn_cnt-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        if(!empty($org_lctns)) { $i=0; $lctns=count($org_lctns); ?><tr><td class="ovrvwcol1">Place of origin:</td><td><?php foreach($org_lctns as $org_lctn):
        echo $org_lctn['org_lctn']; if(!empty($org_lctn['rel_lctns'])) {echo ' ('.implode(' / ', $org_lctn['rel_lctns']).')';} if($i<$lctns-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        if(!empty($profs)){$i=0; $prof_cnt=count($profs); ?><tr><td class="ovrvwcol1">Profession:</td><td>
        <?php foreach($profs as $prof): echo $prof['prof_nm']; if(!empty($prof['rel_profs'])) {echo ' ('.implode(' / ', $prof['rel_profs']).')';}
        if($i<$prof_cnt-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        if(!empty($attrs)){$i=0; $attr_cnt=count($attrs); ?><tr><td class="ovrvwcol1">Attributes:</td><td>
        <?php foreach($attrs as $attr): echo $attr['attr_nm']; if(!empty($attr['rel_attrs'])) {echo ' ('.implode(' / ', $attr['rel_attrs']).')';}
        if($i<$attr_cnt-1) {echo ' / ';} $i++; endforeach; ?></td></tr><?php }
        if(!empty($abils)){ ?><tr><td class="ovrvwcol1">Abilities:</td><td><?php echo implode(' / ', $abils); ?></td></tr><?php }
        ?>
        </table></div></br>
        <?php }

        if(!empty($char_pts))
        { ?>
        <div id="char_pts"><table class="credits">
        <tr><th colspan="3">Playtext<?php if(count($pt_cnt)>1) {echo 's';} echo $char_pt_tbl_hdr; ?></th></tr>
        <?php $rowclass=0;
        foreach($char_pts as $pt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
        if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
        <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes (with this character):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($pt['sg_pts'] as $pt): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';}
        endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if($pt_vrnt_nm) { ?>
        <div id="pt_vrnt_nms" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Variant names:</td><td><em><?php echo $pt_vrnt_nm; ?></em></td></tr>
        </table></div>
        <?php }

        if(!empty($var_char_pts)) { ?>
        <div id="var_char_pts"><table class="credits">
        <tr><th colspan="3">Playtext<?php if(count(array_unique($var_char_pt_cnt))>1) {echo 's';} echo $var_char_pt_tbl_hdr; if($pt_vrnt_nm) {echo ' (including variant names thereof)';} ?></th></tr>
        <?php $rowclass=0;
        foreach($var_char_pts as $pt): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></a></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';
        } if(!empty($pt['var_chars'])) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3">▪ <?php echo implode(' / ', $pt['var_chars']); ?></td>
        </tr>
        <?php } if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
        <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes (with this character):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($pt['sg_pts'] as $pt): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="ptcol1"><?php echo $pt['pt_nm']; ?></td>
          <td class="ptcol2"><?php echo $pt['txt_vrsn_nm']; ?></td>
          <td class="ptcol3"><?php echo $pt['pt_yr']; ?></td>
        </tr>
        <?php if(!empty($pt['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/pt_wri_dsply.inc.html.php';
        } ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3">▪ <?php echo implode(' / ', $pt['var_chars']); ?></td>
        </tr>
        <?php endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php }

        if($prd_vrnt_nm) { ?>
        <div id="prd_vrnt_nms" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Variant names:</td><td><em><?php echo $prd_vrnt_nm; ?></em></td></tr>
        </table></div>
        <?php }

        if(!empty($prds)) { ?>
        <div id="prds"><table class="credits">
        <tr><th colspan="3">Production<?php if(count(array_unique($prd_ids))>1) {echo 's';} echo $prd_tbl_hdr; if($prd_vrnt_nm) {echo ' (including variant names thereof)';} ?></th></tr>
        <?php $rowclass=0;
        foreach($prds as $prd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls']) && (!empty($prd['prd_prfs']) || !empty($prd['prd_uss']))) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        } ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">
            <?php
            foreach($prd['prd_prfs'] as $prd_prf): echo '▪ '.$prd_prf['prsn_nm']; ?> ..... <em><?php echo implode(' / ', $prd_prf['prf_rls']); if(!empty($prd_prf['prf_othr_rls'])) {echo ' (also performed: '.implode(' / ', $prd_prf['prf_othr_rls']).')';} ?></em></br><?php endforeach;
            foreach($prd['prd_uss'] as $prd_us): echo '▪ '.$prd_us['prsn_nm']; ?> ..... <em><?php echo implode(' / ', $prd_us['us_rls']); if(!empty($prd_us['us_othr_rls'])) {echo ' (also performed: '.implode(' / ', $prd_us['us_othr_rls']).')';} ?></em> (u/s)</br><?php endforeach;
            ?>
          </td>
        </tr>
        <?php if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (with performances of this character):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        } ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">
            <?php
            foreach($prd['prd_prfs'] as $prd_prf): echo '▪ '.$prd_prf['prsn_nm']; ?> ..... <em><?php echo implode(' / ', $prd_prf['prf_rls']); if(!empty($prd_prf['prf_othr_rls'])) {echo ' (also performed: '.implode(' / ', $prd_prf['prf_othr_rls']).')';} ?></em></br><?php endforeach;
            foreach($prd['prd_uss'] as $prd_us): echo '▪ '.$prd_us['prsn_nm']; ?> ..... <em><?php echo implode(' / ', $prd_us['us_rls']); if(!empty($prd_us['us_othr_rls'])) {echo ' (also performed: '.implode(' / ', $prd_us['us_othr_rls']).')';} ?></em> (u/s)</br><?php endforeach;
            ?>
          </td>
        </tr>
        <?php endforeach; }
        $rowclass=1-$rowclass;
        endforeach; ?>
        </table></div>
        <?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="char_id" value="<?php echo $char_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>