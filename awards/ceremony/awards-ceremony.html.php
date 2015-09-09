<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetitle; ?> (awards ceremony) | TheatreBase</title>
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/head.inc.html.php'; ?>
</head>
<body>
  <div id="container">
  <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.inc.html.php'; ?>
    <div id="content">
      <div id="results">
        <div class="
        <?php if(isset($_SESSION['successclass'])) {echo $_SESSION['successclass']; unset($_SESSION['successclass']);} ?>">
        <?php if(isset($_SESSION['message'])) {echo $_SESSION['message']; unset($_SESSION['message']);} ?>
        </div>

        <h4>AWARDS CEREMONY:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <div id="awrds" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Part of:</td><td><?php echo $awrds_nm; ?></td></tr>
        <?php if(count($awrds_yrs)>1) { ?><tr><td class="ovrvwcol1">All years held:</td><td><?php echo implode(' / ', $awrds_yrs); ?></td></tr><?php } ?>
        <tr><td class="ovrvwcol1">All awards for:</td><td><?php echo $awrd_yr_othr.$awrd_yr_end_othr; ?></td></tr>
        <?php if(!empty($awrd_ctgrys)) { ?><tr><td class="ovrvwcol1">Categories [<?php echo count($awrd_ctgrys); ?>]:</td><td><?php echo implode(' / ', $awrd_ctgrys); ?></td></tr><?php } ?>
        </table></div></br>

        <?php if($awrd_dt) { ?>
        <h5>Awards date</h5>
        <div id ="dts"><table class="prod1">
        <tr><td><?php echo $awrd_dt; ?></td></tr>
        </table></div>
        <?php } ?>

        <?php if($thtr_dsply) { ?>
        <div id ="thtr">
        <h5>Venue</h5>
        <table class="prod2">
        <tr><td><?php echo $thtr_dsply ?></td></tr>
        </table></div>
        <?php } ?>

        <?php if(!empty($awrds)) {foreach($awrds as $awrd): ?>
          <div id="<?php echo $awrd['awrd_ctgry_url']; ?>">
            <table class="credits">
              <tr>
                <th colspan="3"><?php echo $awrd['awrd_ctgry_nm']; ?><span style="font-weight:normal"> [Nominees: <?php echo count($awrd['noms']) ?>] [<?php echo $awrd['pst_nom_wns']; ?>]</span></th>
              </tr>
              <?php $rowclass=0;
              foreach($awrd['noms'] as $nom): ?>
                <tr class="newcredit row<?php echo $rowclass; ?>">
                  <td class="prdcol5" colspan="3">▪ <?php if($nom['win']) {echo '<b>';} echo $nom['nom_win_dscr'].': '; if($nom['win']) {echo '</b>';}
                    if(!empty($nom['nomppl'])) {
                    $h=0; foreach($nom['nomppl'] as $nomprsn): if(!empty($nomprsn['nomcomp_ppl'])) {$h++;} endforeach;
                    $i=0; $ppl=count($nom['nomppl']); foreach($nom['nomppl'] as $nomprsn):
                    if(!empty($nomprsn['nomcomp_ppl'])) {$j=0; $compppl=count($nomprsn['nomcomp_ppl']);
                    foreach($nomprsn['nomcomp_ppl'] as $nomcomp_prsn): echo $nomcomp_prsn;
                    if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ' for ';}
                    echo $nomprsn['nom_prsn'];
                    if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}}
                    elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}}
                    $i++; endforeach; if(!empty($nom['nomprds'])) {echo ' for:';}}
                    if(!empty($nom['nompts'])) {$k=0; $nom_pts=count($nom['nompts']); if(!empty($nom['nomppl'])) {echo ' for ';}
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
                $rowclass=1 - $rowclass;
              endforeach; ?>
            </table>
          </div>
        <?php endforeach; } ?>
      </div>

      <div id="buttons" class="buttons">
        <form action="?edit" method="post">
          <input type="hidden" name="awrd_id" value="<?php echo $awrd_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>