<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (genre - playtexts) | TheatreBase</title>
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

        <h4>GENRE (playtexts):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(($lnk) || !empty($rel_gnrs1) || !empty($rel_gnrs2))
        { ?>
        <div id="dscrptn" class='box'><table class='overview'>
        <?php
        if($lnk) { ?><tr><td class='ovrvwcol1'>Link to:</td><td><?php echo $lnk; ?></td></tr><?php }
        if(!empty($rel_gnrs2)){ ?><tr><td class="ovrvwcol1">Related genres:</td><td><?php echo implode(' / ', $rel_gnrs2); ?></td></tr><?php }
        if(!empty($rel_gnrs1)){ ?><tr><td class="ovrvwcol1">Sub-genres:</td><td><?php echo implode(' / ', $rel_gnrs1); ?></td></tr><?php }
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
        if($k>0 && $pt['gnr_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php echo $pt['gnr_nm']; ?></em></td>
        </tr>
        <?php } if(!empty($pt['sg_pts'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="ptcol4" colspan="3">
        <?php if($pt['sg_cnt']>count($pt['sg_pts'])) {echo 'Includes (in this genre):';} else {echo 'Comprises:';} ?></td></tr>
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
          <td class="prdcol5" colspan="3">▪ <em><?php echo $pt['gnr_nm']; ?></em></td>
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
        <?php } else { ?></br><h5><em>NO PLAYTEXTS YET LISTED FOR THIS GENRE</em></h5><?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="gnr_id" value="<?php echo $gnr_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>