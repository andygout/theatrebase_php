<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (theme - productions) | TheatreBase</title>
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
        <h4>THEME (productions):</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(($lnk) || !empty($rel_thms1) || !empty($rel_thms2))
        { ?>
        <div id="dscrptn" class="box"><table class="overview">
        <?php
        if($lnk) { ?><tr><td class="ovrvwcol1">Link to:</td><td><?php echo $lnk; ?></td></tr><?php }
        if(!empty($rel_thms2)){ ?><tr><td class="ovrvwcol1">Broader themes:</td><td><?php echo implode(' / ', $rel_thms2); ?></td></tr><?php }
        if(!empty($rel_thms1)){ ?><tr><td class="ovrvwcol1">Sub-themes:</td><td><?php echo implode(' / ', $rel_thms1); ?></td></tr><?php }
        ?>
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
        } if($k>0 && $prd['thm_nm']) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php echo $prd['thm_nm']; ?></em></td>
        </tr>
        <?php } if(!empty($prd['sg_prds'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5" colspan="3">
        <?php if($prd['sg_cnt']>count($prd['sg_prds'])) {echo 'Includes (with this theme):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($prd['sg_prds'] as $prd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol1"><?php echo $prd['prd_nm']; ?></td>
          <td class="prdcol2"><?php echo $prd['thtr']; ?></td>
          <td class="prdcol4"><?php echo $prd['prd_dts']; ?></td>
        </tr>
        <?php if(!empty($prd['wri_rls'])) {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_display/prd_wri_dsply.inc.html.php';
        } if($k>0) { ?>
        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3">▪ <em><?php echo $prd['thm_nm']; ?></em></td>
        </tr>
        <?php } endforeach; }
        $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php } else { ?></br><h5><em>NO PRODUCTIONS YET LISTED FOR THIS THEME</em></h5><?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="thm_id" value="<?php echo $thm_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>