<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (theatre type) | TheatreBase</title>
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
        <h4>THEATRE TYPE:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <?php if(!empty($thtrs))
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
        <?php if(!empty($thtr['sbthtrs'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5">
        <?php if($thtr['sbthtr_cnt']>count($thtr['sbthtrs'])) {echo 'Includes (of this type):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($thtr['sbthtrs'] as $sbthtr): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $sbthtr['sbthtr'];
          if(!empty($sbthtr['sbthtr_sbsq_nms'])) {echo ' (Subsequently named: '.implode(' / ', $sbthtr['sbthtr_sbsq_nms']).')';}
          if(!empty($sbthtr['sbthtr_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $sbthtr['sbthtr_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php endforeach; }
        $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php } ?>

        <?php if(!empty($thtrs_clsd))
        { ?>
        <div id="thtrs_clsd"><table class="credits">
        <tr><th colspan="1">Theatres (closed)</th></tr>
        <?php $rowclass=0;
        foreach($thtrs_clsd as $thtr_clsd): ?>
        <tr class="newcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $thtr_clsd['thtr'];
          if(!empty($thtr_clsd['thtr_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $thtr_clsd['thtr_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php if(!empty($thtr_clsd['sbthtrs'])) { ?>
        <tr class="row<?php echo $rowclass; ?>"><td class="prdcol5">
        <?php if($thtr_clsd['sbthtr_cnt']>count($thtr_clsd['sbthtrs'])) {echo 'Includes (of this type; closed):';} else {echo 'Comprises:';} ?></td></tr>
        <?php foreach($thtr_clsd['sbthtrs'] as $sbthtr_clsd): ?>
        <tr class="newsubcredit row<?php echo $rowclass; ?>">
          <td class="prdcol5"><?php echo $sbthtr_clsd['sbthtr'];
          if(!empty($sbthtr_clsd['sbthtr_prvs_nms'])) {echo ' (Previously named: '.implode(' / ', $sbthtr_clsd['sbthtr_prvs_nms']).')';} ?>
          </td>
        </tr>
        <?php endforeach; }
        $rowclass=1 - $rowclass;
        endforeach; ?>
        </table></div>
        <?php } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="thtr_typ_id" value="<?php echo $thtr_typ_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>