<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title><?php echo $pagetab; ?> (award category) | TheatreBase</title>
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

        <h4>AWARDS CATEGORY:</h4>
        <h1><?php echo $pagetitle; ?></h1>

        <div id="clssfctn" class="box"><table class="overview">
        <tr><td class="ovrvwcol1">Awarded by:</td><td><?php echo $awrd_nm_lnk; ?></td></tr>
        <?php if($awrd_yr_list) { ?><tr><td class="ovrvwcol1">Years awarded:</td><td><?php echo $awrd_yr_list; ?></td></tr><?php } ?>
        <?php if($alt_nm_list) { ?><tr><td class="ovrvwcol1">Alternate names:</td><td><?php echo $alt_nm_list; ?></td></tr><?php } ?>
        </table></div>

        <?php if(!empty($awrds)) {foreach($awrds as $awrd): ?>
          <div id="<?php echo $awrd['awrd_yr_url']; ?>">
            <table class="credits">
              <tr>
                <th colspan="3"><?php echo $awrd['awrd_nm'].': '.$awrd['awrd_ctgry_nm_tbl_hd']; ?> <span style="font-weight:normal">[Nominees: <?php echo count($awrd['noms']) ?>]</span></th>
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
                    <td class="prdcol1"><?php echo $nomprd['prd_nm']; ?></a></td>
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
          <input type="hidden" name="awrd_ctgry_id" value="<?php echo $awrd_ctgry_id; ?>"/>
          <input type="submit" name="action" value="Edit" class="button"/>
        </form>
      </div>
    </div>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.inc.html.php'; ?>
  </div>
</body>
</html>