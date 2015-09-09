<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/location/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $lctn_url=cln($_GET['lctn_url']);

  $sql="SELECT lctn_id FROM lctn WHERE lctn_url='$lctn_url' AND lctn_fctn=0";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $lctn_id=$row['lctn_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $rel_lctn_cnt=array();

    $sql= "SELECT lctn_nm, lctn_sffx_num, lctn_url, lctn_exp, lctn_est_dt_c, lctn_est_dt_bce, lctn_exp_dt_c, lctn_exp_dt_bce, CASE WHEN lctn_est_dt_frmt=1 THEN DATE_FORMAT(lctn_est_dt, '%d %b %Y') WHEN lctn_est_dt_frmt=2 THEN DATE_FORMAT(lctn_est_dt, '%b %Y') WHEN lctn_est_dt_frmt=3 THEN DATE_FORMAT(lctn_est_dt, '%Y') ELSE NULL END AS lctn_est_dt_frmt, CASE WHEN lctn_exp_dt_frmt=1 THEN DATE_FORMAT(lctn_exp_dt, '%d %b %Y') WHEN lctn_exp_dt_frmt=2 THEN DATE_FORMAT(lctn_exp_dt, '%b %Y') WHEN lctn_exp_dt_frmt=3 THEN DATE_FORMAT(lctn_exp_dt, '%Y') ELSE NULL END AS lctn_exp_dt_frmt
          FROM lctn
          WHERE lctn_id='$lctn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting (location) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['lctn_sffx_num']) {$sttng_lctn_sffx_rmn=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$sttng_lctn_sffx_rmn='';}
    $pagetab=html($row['lctn_nm'].$sttng_lctn_sffx_rmn);
    $pagetitle=html($row['lctn_nm']);
    $lctn_nm=html($row['lctn_nm']);
    $lctn_url=html($row['lctn_url']);
    if($row['lctn_exp']) {$lctn_exp=' [PRE-EXISTING]';} else {$lctn_exp='';}
    if($row['lctn_est_dt_frmt'] || $row['lctn_exp_dt_frmt'])
    {
      if($row['lctn_est_dt_c']) {$lctn_est_dt_c='c.';} else {$lctn_est_dt_c='';}
      if($row['lctn_est_dt_bce']) {$lctn_est_dt_bce=' BCE';} else {$lctn_est_dt_bce='';}
      if($row['lctn_est_dt_frmt']) {$lctn_est_dt='from '.$lctn_est_dt_c.html(ltrim($row['lctn_est_dt_frmt'], '0')).$lctn_est_dt_bce;} else {$lctn_est_dt='';}
      if($row['lctn_exp_dt_c']) {$lctn_exp_dt_c='c.';} else {$lctn_exp_dt_c='';}
      if($row['lctn_exp_dt_bce']) {$lctn_exp_dt_bce=' BCE';} else {$lctn_exp_dt_bce='';}
      if($row['lctn_exp_dt_frmt']) {$lctn_exp_dt='until '.$lctn_exp_dt_c.html(ltrim($row['lctn_exp_dt_frmt'], '0')).$lctn_exp_dt_bce;} else {$lctn_exp_dt='';}
      if($row['lctn_est_dt_frmt'] && $row['lctn_exp_dt_frmt']) {$spc=' ';} else {$spc='';}
      $lctn_dt=ucfirst($lctn_est_dt.$spc.$lctn_exp_dt);
      $rel_lctn_cnt[]='1';
    }
    else {$lctn_dt='';}
    if($row['lctn_exp']) {$exp_insrt='AND lctn_exp=1';} else {$exp_insrt='';}

    $lnks=array(); $lnk_cnt=array();

    $sql= "SELECT 1 FROM prdsttng_lctn WHERE sttng_lctnid='$lctn_id'
          UNION
          SELECT 1 FROM rel_lctn INNER JOIN prdsttng_lctn ON rel_lctn1=sttng_lctnid WHERE rel_lctn2='$lctn_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of location as setting (location) for production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$prd_lnk='1'; $lnk_cnt[]='1';} else {$prd_lnk=NULL;}

    $sql= "SELECT 1 FROM ptsttng_lctn WHERE sttng_lctnid='$lctn_id'
          UNION
          SELECT 1 FROM rel_lctn INNER JOIN ptsttng_lctn ON rel_lctn1=sttng_lctnid WHERE rel_lctn2='$lctn_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of location as setting (location) for playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$pt_lnk='1'; $lnk_cnt[]='1';} else {$pt_lnk=NULL;}

    if($prd_lnk && $pt_lnk) {$lnks[]='<a href="/production/setting/location/'.$lctn_url.'">Productions</a> and <a href="/playtext/setting/location/'.$lctn_url.'">playtexts</a> with '.html($lctn_nm).' as setting';}
    elseif($prd_lnk && !$pt_lnk) {$lnks[]='<a href="/production/setting/location/'.$lctn_url.'">Productions</a> with '.$lctn_nm.' as setting';}
    elseif(!$prd_lnk && $pt_lnk) {$lnks[]='<a href="/playtext/setting/location/'.$lctn_url.'">Playtexts</a> with '.$lctn_nm.' as setting';}

    $sql= "SELECT 1 FROM thtr WHERE thtr_lctnid='$lctn_id'
          UNION
          SELECT 1 FROM rel_lctn INNER JOIN thtr ON rel_lctn1=thtr_lctnid WHERE rel_lctn2='$lctn_id' AND EXISTS(SELECT 1 FROM lctn WHERE lctn_id='$lctn_id' AND lctn_fctn=0)
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of location as theatre location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$thtr_lnk='1'; $lnk_cnt[]='1';} else {$thtr_lnk=NULL;}

    $sql= "SELECT 1 FROM comp_lctn WHERE comp_lctnid='$lctn_id'
          UNION
          SELECT 1 FROM rel_lctn INNER JOIN comp_lctn ON rel_lctn1=comp_lctnid WHERE rel_lctn2='$lctn_id' AND EXISTS(SELECT 1 FROM lctn WHERE lctn_id='$lctn_id' AND lctn_fctn=0)
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of location as company location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$comp_lnk='1'; $lnk_cnt[]='1';} else {$comp_lnk=NULL;}

    if($thtr_lnk && $comp_lnk) {$lnks[]='<a href="/theatre/location/'.$lctn_url.'">Theatres</a> and <a href="/company/location/'.$lctn_url.'">companies</a> located in '.$lctn_nm;}
    elseif($thtr_lnk && !$comp_lnk) {$lnks[]='<a href="/theatre/location/'.$lctn_url.'">Theatres</a> located in '.$lctn_nm;}
    elseif(!$thtr_lnk && $comp_lnk) {$lnks[]='<a href="/company/location/'.$lctn_url.'">Companies</a> located in '.$lctn_nm;}

    $sql= "SELECT 1 FROM charorg_lctn WHERE org_lctnid='$lctn_id'
          UNION
          SELECT 1 FROM rel_lctn INNER JOIN charorg_lctn ON rel_lctn1=org_lctnid WHERE rel_lctn2='$lctn_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of location as place of origin for character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {$lnks[]='<a href="/character/origin/'.$lctn_url.'">Characters</a> with '.$lctn_nm.' as place of origin'; $lnk_cnt[]='1';}

    if(!empty($lnks)) {$rel_lctn_cnt[]='1';}

    $sql= "SELECT lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, lctn_exp
          FROM rel_lctn
          INNER JOIN lctn ON rel_lctn2=lctn_id
          WHERE rel_lctn1='$lctn_id' AND lctn_fctn=0 AND (EXISTS(SELECT 1 FROM prsn WHERE org_lctnid='$lctn_id') OR EXISTS(SELECT 1 FROM rel_lctn INNER JOIN prsn ON rel_lctn1=org_lctnid WHERE rel_lctn2='$lctn_id'))
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related location (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']).' ';} else {$rel_lctn_nt1='';}
      if($row['rel_lctn_nt2']) {if(!preg_match('/^(:|;|,|\.)/', $row['rel_lctn_nt2'])) {$rel_lctn_nt2=' '.html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2=html($row['rel_lctn_nt2']);}}
      else {$rel_lctn_nt2='';}
      $rel_lctn_nm=$rel_lctn_nt1.'<a href="/person/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt2;
      if(!$row['lctn_exp']) {$rel_lctns2[]=$rel_lctn_nm;} else {$rel_lctns2_exp[]=$rel_lctn_nm;}
      $rel_lctn_cnt[]='1';
    }

    $sql= "SELECT lctn_nm, COALESCE(lctn_alph, lctn_nm)lctn_alph, lctn_sffx_num, lctn_url, rel_lctn_nt1, rel_lctn_nt2, lctn_exp
          FROM rel_lctn
          INNER JOIN prsn ON rel_lctn1=org_lctnid INNER JOIN lctn ON org_lctnid=lctn_id
          WHERE rel_lctn2='$lctn_id' AND lctn_fctn=0
          UNION
          SELECT lctn_nm, COALESCE(lctn_alph, lctn_nm)lctn_alph, lctn_sffx_num, lctn_url, rl3.rel_lctn_nt1, rl3.rel_lctn_nt2, lctn_exp
          FROM rel_lctn rl1
          INNER JOIN prsn ON rl1.rel_lctn1=org_lctnid INNER JOIN rel_lctn rl2 ON org_lctnid=rl2.rel_lctn1 INNER JOIN lctn ON rl2.rel_lctn2=lctn_id
          LEFT OUTER JOIN rel_lctn rl3 ON lctn_id=rl3.rel_lctn1 AND '$lctn_id'=rl3.rel_lctn2
          WHERE rl1.rel_lctn2='$lctn_id' AND lctn_fctn=0 AND lctn_id!=rl1.rel_lctn2 AND lctn_id IN(SELECT rel_lctn1 FROM rel_lctn WHERE rel_lctn2='$lctn_id')
          ORDER BY lctn_alph ASC, lctn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related location (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']).' ';} else {$rel_lctn_nt1='';}
      if($row['rel_lctn_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['rel_lctn_nt2'])) {$rel_lctn_nt2=html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2=' '.html($row['rel_lctn_nt2']);}}
      else {$rel_lctn_nt2='';}
      if($row['rel_lctn_nt1'] || $row['rel_lctn_nt2']) {$rel_lctn_nt=' ('.$rel_lctn_nt1.$lctn_nm.$rel_lctn_nt2.')';} else {$rel_lctn_nt='';}
      $rel_lctn_nm='<a href="/person/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt;
      if(!$row['lctn_exp']) {$rel_lctns1[]=$rel_lctn_nm;} else {$rel_lctns1_exp[]=$rel_lctn_nm;}
      $rel_lctn_cnt[]='1';
    }

    $sql= "SELECT lctn_nm, lctn_url, COALESCE(lctn_alph, lctn_nm)lctn_alph, lctn_est_dt_c, lctn_est_dt_bce, lctn_exp_dt_c, lctn_exp_dt_bce, lctn_prvs_sg, lctn_sbsq_sg, CASE WHEN lctn_est_dt_frmt=1 THEN DATE_FORMAT(lctn_est_dt, '%d %b %Y') WHEN lctn_est_dt_frmt=2 THEN DATE_FORMAT(lctn_est_dt, '%b %Y') WHEN lctn_est_dt_frmt=3 THEN DATE_FORMAT(lctn_est_dt, '%Y') ELSE NULL END AS lctn_est_dt_frmt, CASE WHEN lctn_exp_dt_frmt=1 THEN DATE_FORMAT(lctn_exp_dt, '%d %b %Y') WHEN lctn_exp_dt_frmt=2 THEN DATE_FORMAT(lctn_exp_dt, '%b %Y') WHEN lctn_exp_dt_frmt=3 THEN DATE_FORMAT(lctn_exp_dt, '%Y') ELSE NULL END AS lctn_exp_dt_frmt
          FROM lctn_aka
          INNER JOIN lctn ON lctn_sbsq_id=lctn_id
          WHERE lctn_prvs_id='$lctn_id' AND lctn_fctn=0 AND EXISTS(SELECT 1 FROM prsn WHERE lctn_id=org_lctnid UNION SELECT 1 FROM rel_lctn INNER JOIN prsn ON rel_lctn1=org_lctnid WHERE rel_lctn2=lctn_id)
          ORDER BY lctn_est_dt DESC, lctn_exp_dt DESC, lctn_alph ASC, lctn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring subsequent location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_est_dt_frmt'] || $row['lctn_exp_dt_frmt'])
      {
        if($row['lctn_est_dt_c']) {$sbsq_lctn_est_dt_c='c.';} else {$sbsq_lctn_est_dt_c='';}
        if($row['lctn_est_dt_bce']) {$sbsq_lctn_est_dt_bce=' BCE';} else {$sbsq_lctn_est_dt_bce='';}
        if($row['lctn_est_dt_frmt']) {$sbsq_lctn_est_dt='from '.$sbsq_lctn_est_dt_c.html(ltrim($row['lctn_est_dt_frmt'], '0')).$sbsq_lctn_est_dt_bce;} else {$sbsq_lctn_est_dt='';}
        if($row['lctn_exp_dt_c']) {$sbsq_lctn_exp_dt_c='c.';} else {$sbsq_lctn_exp_dt_c='';}
        if($row['lctn_exp_dt_bce']) {$sbsq_lctn_exp_dt_bce=' BCE';} else {$sbsq_lctn_exp_dt_bce='';}
        if($row['lctn_exp_dt_frmt']) {$sbsq_lctn_exp_dt='until '.$sbsq_lctn_exp_dt_c.html(ltrim($row['lctn_exp_dt_frmt'], '0')).$sbsq_lctn_exp_dt_bce;} else {$sbsq_lctn_exp_dt='';}
        if($row['lctn_est_dt_frmt'] && $row['lctn_exp_dt_frmt']) {$sbsq_spc=' ';} else {$sbsq_spc='';}
        $sbsq_lctn_dt=' <em>('.$sbsq_lctn_est_dt.$sbsq_spc.$sbsq_lctn_exp_dt.')</em>';
      }
      else {$sbsq_lctn_dt='';}
      $sbsq_lctn_nm='<a href="/person/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$sbsq_lctn_dt;
      if(!$row['lctn_prvs_sg'] && !$row['lctn_sbsq_sg']) {$sbsq_lctns[]=$sbsq_lctn_nm;} elseif($row['lctn_prvs_sg']) {$sbsq_lctns_prt_of[]=$sbsq_lctn_nm;} else {$sbsq_lctns_cmprs[]=$sbsq_lctn_nm;}
      $rel_lctn_cnt[]='1';
    }

    $sql= "SELECT lctn_nm, lctn_url, COALESCE(lctn_alph, lctn_nm)lctn_alph, lctn_sffx_num, lctn_est_dt_c, lctn_est_dt_bce, lctn_exp_dt_c, lctn_exp_dt_bce, lctn_prvs_sg, lctn_sbsq_sg, CASE WHEN lctn_est_dt_frmt=1 THEN DATE_FORMAT(lctn_est_dt, '%d %b %Y') WHEN lctn_est_dt_frmt=2 THEN DATE_FORMAT(lctn_est_dt, '%b %Y') WHEN lctn_est_dt_frmt=3 THEN DATE_FORMAT(lctn_est_dt, '%Y') ELSE NULL END AS lctn_est_dt_frmt, CASE WHEN lctn_exp_dt_frmt=1 THEN DATE_FORMAT(lctn_exp_dt, '%d %b %Y') WHEN lctn_exp_dt_frmt=2 THEN DATE_FORMAT(lctn_exp_dt, '%b %Y') WHEN lctn_exp_dt_frmt=3 THEN DATE_FORMAT(lctn_exp_dt, '%Y') ELSE NULL END AS lctn_exp_dt_frmt
          FROM lctn_aka
          INNER JOIN lctn ON lctn_prvs_id=lctn_id
          WHERE lctn_sbsq_id='$lctn_id' AND lctn_fctn=0 AND EXISTS(SELECT 1 FROM prsn WHERE lctn_id=org_lctnid UNION SELECT 1 FROM rel_lctn INNER JOIN prsn ON rel_lctn1=org_lctnid WHERE rel_lctn2=lctn_id)
          ORDER BY lctn_est_dt DESC, lctn_exp_dt DESC, lctn_alph ASC, lctn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring previous location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_est_dt_frmt'] || $row['lctn_exp_dt_frmt'])
      {
        if($row['lctn_est_dt_c']) {$prvs_lctn_est_dt_c='c.';} else {$prvs_lctn_est_dt_c='';}
        if($row['lctn_est_dt_bce']) {$prvs_lctn_est_dt_bce=' BCE';} else {$prvs_lctn_est_dt_bce='';}
        if($row['lctn_est_dt_frmt']) {$prvs_lctn_est_dt='from '.$prvs_lctn_est_dt_c.html(ltrim($row['lctn_est_dt_frmt'], '0')).$prvs_lctn_est_dt_bce;} else {$prvs_lctn_est_dt='';}
        if($row['lctn_exp_dt_c']) {$prvs_lctn_exp_dt_c='c.';} else {$prvs_lctn_exp_dt_c='';}
        if($row['lctn_exp_dt_bce']) {$prvs_lctn_exp_dt_bce=' BCE';} else {$prvs_lctn_exp_dt_bce='';}
        if($row['lctn_exp_dt_frmt']) {$prvs_lctn_exp_dt='until '.$prvs_lctn_exp_dt_c.html(ltrim($row['lctn_exp_dt_frmt'], '0')).$prvs_lctn_exp_dt_bce;} else {$prvs_lctn_exp_dt='';}
        if($row['lctn_est_dt_frmt'] && $row['lctn_exp_dt_frmt']) {$prvs_spc=' ';} else {$prvs_spc='';}
        $prvs_lctn_dt=' <em>('.$prvs_lctn_est_dt.$prvs_spc.$prvs_lctn_exp_dt.')</em>';
      }
      else {$prvs_lctn_dt='';}
      $prvs_lctn_nm='<a href="/person/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$prvs_lctn_dt;
      if(!$row['lctn_prvs_sg'] && !$row['lctn_sbsq_sg']) {$prvs_lctns[]=$prvs_lctn_nm;} elseif($row['lctn_sbsq_sg']) {$prvs_lctns_prt_of[]=$prvs_lctn_nm;} else {$prvs_lctns_cmprs[]=$prvs_lctn_nm;}
      $rel_lctn_cnt[]='1';
    }

    $k=0;
    $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_fll_nm, prsn_url, lctn_nm
          FROM prsn
          INNER JOIN lctn ON org_lctnid=lctn_id WHERE org_lctnid='$lctn_id'
          GROUP BY prsn_id
          UNION
          SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_fll_nm, prsn_url, lctn_nm
          FROM rel_lctn
          INNER JOIN lctn ON rel_lctn1=lctn_id INNER JOIN prsn p ON rel_lctn1=org_lctnid
          LEFT OUTER JOIN prsnorg_lctn_alt pola ON prsn_id=prsnid AND p.org_lctnid=pola.org_lctnid
          WHERE rel_lctn2='$lctn_id' AND prsnid IS NULL $exp_insrt
          GROUP BY prsn_id
          UNION
          SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_fll_nm, prsn_url, lctn_nm
          FROM prsnorg_lctn_alt pola
          INNER JOIN lctn ON pola.org_lctnid=lctn_id INNER JOIN prsn ON prsnid=prsn_id
          WHERE org_lctn_altid='$lctn_id'
          GROUP BY prsn_id
          ORDER BY prsn_lst_nm ASC, prsn_frst_nm ASC, prsn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if(html($row['lctn_nm'])!==$lctn_nm) {$k++;}
      $ppl[]=array('prsn_nm'=>'<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>', 'lctn_nm'=>html($row['lctn_nm']));
    }

    $lctn_id=html($lctn_id);
    include 'person-origin.html.php';
  }
?>