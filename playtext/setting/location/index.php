<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/location/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $lctn_url=cln($_GET['lctn_url']);

  $sql="SELECT lctn_id FROM lctn WHERE lctn_url='$lctn_url'";
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

    $sql= "SELECT lctn_nm, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn, lctn_est_dt_c, lctn_est_dt_bce, lctn_exp_dt_c, lctn_exp_dt_bce, CASE WHEN lctn_est_dt_frmt=1 THEN DATE_FORMAT(lctn_est_dt, '%d %b %Y') WHEN lctn_est_dt_frmt=2 THEN DATE_FORMAT(lctn_est_dt, '%b %Y') WHEN lctn_est_dt_frmt=3 THEN DATE_FORMAT(lctn_est_dt, '%Y') ELSE NULL END AS lctn_est_dt_frmt, CASE WHEN lctn_exp_dt_frmt=1 THEN DATE_FORMAT(lctn_exp_dt, '%d %b %Y') WHEN lctn_exp_dt_frmt=2 THEN DATE_FORMAT(lctn_exp_dt, '%b %Y') WHEN lctn_exp_dt_frmt=3 THEN DATE_FORMAT(lctn_exp_dt, '%Y') ELSE NULL END AS lctn_exp_dt_frmt
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
    if($row['lctn_exp'] && $row['lctn_fctn']) {$lctn_exp_fctn=' [PRE-EXISTING / FICTIONAL]';}
    elseif($row['lctn_exp'] && !$row['lctn_fctn']) {$lctn_exp_fctn=' [PRE-EXISTING]';}
    elseif(!$row['lctn_exp'] && $row['lctn_fctn']) {$lctn_exp_fctn=' [FICTIONAL]';}
    else {$lctn_exp_fctn='';}
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
    if($row['lctn_exp'] || $row['lctn_fctn']) {$exp_fctn_insrt='INNER JOIN lctn ON ptsl.sttng_lctnid=lctn_id';} else {$exp_fctn_insrt='';}
    if($row['lctn_exp']) {$exp_insrt='AND lctn_exp=1';} else {$exp_insrt='';}
    if($row['lctn_fctn']) {$fctn_insrt='AND lctn_fctn=1';} else {$fctn_insrt='';}

    $lnks=array(); $lnk_cnt=array();

    $sql= "SELECT 1 FROM prdsttng_lctn WHERE sttng_lctnid='$lctn_id'
          UNION
          SELECT 1 FROM rel_lctn INNER JOIN prdsttng_lctn ON rel_lctn1=sttng_lctnid WHERE rel_lctn2='$lctn_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of location as setting (location) for production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnks[]='<a href="/production/setting/location/'.$lctn_url.'">Productions</a> with '.$lctn_nm.' as setting'; $lnk_cnt[]='1';}

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

    $sql= "SELECT 1 FROM prsn WHERE org_lctnid='$lctn_id'
          UNION
          SELECT 1 FROM rel_lctn INNER JOIN prsn ON rel_lctn1=org_lctnid WHERE rel_lctn2='$lctn_id' AND EXISTS(SELECT 1 FROM lctn WHERE lctn_id='$lctn_id' AND lctn_fctn=0)
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of location as place of origin for person: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$prsn_lnk='1'; $lnk_cnt[]='1';} else {$prsn_lnk=NULL;}

    $sql= "SELECT 1 FROM charorg_lctn WHERE org_lctnid='$lctn_id'
          UNION
          SELECT 1 FROM rel_lctn INNER JOIN charorg_lctn ON rel_lctn1=org_lctnid WHERE rel_lctn2='$lctn_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of location as place of origin for character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$char_lnk='1'; $lnk_cnt[]='1';} else {$char_lnk=NULL;}

    if($prsn_lnk && $char_lnk) {$lnks[]='<a href="/person/origin/'.$lctn_url.'">People</a> and <a href="/character/origin/'.$lctn_url.'">characters</a> with '.html($lctn_nm).' as place of origin';}
    elseif($prsn_lnk && !$char_lnk) {$lnks[]='<a href="/person/origin/'.$lctn_url.'">People</a> with '.$lctn_nm.' as place of origin';}
    elseif(!$prsn_lnk && $char_lnk) {$lnks[]='<a href="/character/origin/'.$lctn_url.'">Characters</a> with '.$lctn_nm.' as place of origin';}

    if(!empty($lnks)) {$rel_lctn_cnt[]='1';}

    $sql= "SELECT lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, lctn_exp, lctn_fctn
          FROM rel_lctn
          INNER JOIN lctn ON rel_lctn2=lctn_id
          WHERE rel_lctn1='$lctn_id' AND (EXISTS(SELECT 1 FROM ptsttng_lctn WHERE sttng_lctnid='$lctn_id') OR EXISTS(SELECT 1 FROM rel_lctn INNER JOIN ptsttng_lctn ON rel_lctn1=sttng_lctnid WHERE rel_lctn2='$lctn_id'))
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related location (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']).' ';} else {$rel_lctn_nt1='';}
      if($row['rel_lctn_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['rel_lctn_nt2'])) {$rel_lctn_nt2=html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2=' '.html($row['rel_lctn_nt2']);}}
      else {$rel_lctn_nt2='';}
      $rel_lctn_nm=$rel_lctn_nt1.'<a href="/playtext/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt2;
      if(!$row['lctn_exp'] && !$row['lctn_fctn']) {$rel_lctns2[]=$rel_lctn_nm;}
      elseif(!$row['lctn_fctn']) {$rel_lctns2_exp[]=$rel_lctn_nm;}
      else {$rel_lctns2_fctn[]=$rel_lctn_nm;}
      $rel_lctn_cnt[]='1';
    }

    $sql= "SELECT lctn_nm, COALESCE(lctn_alph, lctn_nm)lctn_alph, lctn_sffx_num, lctn_url, rel_lctn_nt1, rel_lctn_nt2, lctn_exp, lctn_fctn
          FROM rel_lctn
          INNER JOIN ptsttng_lctn ON rel_lctn1=sttng_lctnid INNER JOIN lctn ON sttng_lctnid=lctn_id
          WHERE rel_lctn2='$lctn_id'
          UNION
          SELECT lctn_nm, COALESCE(lctn_alph, lctn_nm)lctn_alph, lctn_sffx_num, lctn_url, rl3.rel_lctn_nt1, rl3.rel_lctn_nt2, lctn_exp, lctn_fctn
          FROM rel_lctn rl1
          INNER JOIN ptsttng_lctn ON rl1.rel_lctn1=sttng_lctnid INNER JOIN rel_lctn rl2 ON sttng_lctnid=rl2.rel_lctn1 INNER JOIN lctn ON rl2.rel_lctn2=lctn_id
          LEFT OUTER JOIN rel_lctn rl3 ON lctn_id=rl3.rel_lctn1 AND '$lctn_id'=rl3.rel_lctn2
          WHERE rl1.rel_lctn2='$lctn_id' AND lctn_id!=rl1.rel_lctn2 AND lctn_id IN(SELECT rel_lctn1 FROM rel_lctn WHERE rel_lctn2='$lctn_id')
          ORDER BY lctn_alph ASC, lctn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related location (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']).' ';} else {$rel_lctn_nt1='';}
      if($row['rel_lctn_nt2']) {if(!preg_match('/^(:|;|,|\.)/', $row['rel_lctn_nt2'])) {$rel_lctn_nt2=' '.html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2=html($row['rel_lctn_nt2']);}}
      else {$rel_lctn_nt2='';}
      if($row['rel_lctn_nt1'] || $row['rel_lctn_nt2']) {$rel_lctn_nt=' ('.$rel_lctn_nt1.$lctn_nm.$rel_lctn_nt2.')';} else {$rel_lctn_nt='';}
      $rel_lctn_nm='<a href="/playtext/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt;
      if(!$row['lctn_exp'] && !$row['lctn_fctn']) {$rel_lctns1[]=$rel_lctn_nm;}
      elseif(!$row['lctn_fctn']) {$rel_lctns1_exp[]=$rel_lctn_nm;}
      else {$rel_lctns1_fctn[]=$rel_lctn_nm;}
      $rel_lctn_cnt[]='1';
    }

    $sql= "SELECT lctn_nm, lctn_url, COALESCE(lctn_alph, lctn_nm)lctn_alph, lctn_fctn, lctn_est_dt_c, lctn_est_dt_bce, lctn_exp_dt_c, lctn_exp_dt_bce, lctn_prvs_sg, lctn_sbsq_sg, CASE WHEN lctn_est_dt_frmt=1 THEN DATE_FORMAT(lctn_est_dt, '%d %b %Y') WHEN lctn_est_dt_frmt=2 THEN DATE_FORMAT(lctn_est_dt, '%b %Y') WHEN lctn_est_dt_frmt=3 THEN DATE_FORMAT(lctn_est_dt, '%Y') ELSE NULL END AS lctn_est_dt_frmt, CASE WHEN lctn_exp_dt_frmt=1 THEN DATE_FORMAT(lctn_exp_dt, '%d %b %Y') WHEN lctn_exp_dt_frmt=2 THEN DATE_FORMAT(lctn_exp_dt, '%b %Y') WHEN lctn_exp_dt_frmt=3 THEN DATE_FORMAT(lctn_exp_dt, '%Y') ELSE NULL END AS lctn_exp_dt_frmt
          FROM lctn_aka
          INNER JOIN lctn ON lctn_sbsq_id=lctn_id
          WHERE lctn_prvs_id='$lctn_id' AND EXISTS(SELECT 1 FROM ptsttng_lctn WHERE lctn_id=sttng_lctnid UNION SELECT 1 FROM rel_lctn INNER JOIN ptsttng_lctn ON rel_lctn1=sttng_lctnid WHERE rel_lctn2=lctn_id)
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
      $sbsq_lctn_nm='<a href="/playtext/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$sbsq_lctn_dt;
      if(!$row['lctn_fctn']) {if(!$row['lctn_prvs_sg'] && !$row['lctn_sbsq_sg']) {$sbsq_lctns[]=$sbsq_lctn_nm;} elseif($row['lctn_prvs_sg']) {$sbsq_lctns_prt_of[]=$sbsq_lctn_nm;} else {$sbsq_lctns_cmprs[]=$sbsq_lctn_nm;}}
      else {if(!$row['lctn_prvs_sg'] && !$row['lctn_sbsq_sg']) {$sbsq_lctns_fctn[]=$sbsq_lctn_nm;} elseif($row['lctn_prvs_sg']) {$sbsq_lctns_fctn_prt_of[]=$sbsq_lctn_nm;} else {$sbsq_lctns_fctn_cmprs[]=$sbsq_lctn_nm;}}
      $rel_lctn_cnt[]='1';
    }

    $sql =  "SELECT lctn_nm, lctn_url, COALESCE(lctn_alph, lctn_nm)lctn_alph, lctn_sffx_num, lctn_fctn, lctn_est_dt_c, lctn_est_dt_bce, lctn_exp_dt_c, lctn_exp_dt_bce, lctn_prvs_sg, lctn_sbsq_sg, CASE WHEN lctn_est_dt_frmt=1 THEN DATE_FORMAT(lctn_est_dt, '%d %b %Y') WHEN lctn_est_dt_frmt=2 THEN DATE_FORMAT(lctn_est_dt, '%b %Y') WHEN lctn_est_dt_frmt=3 THEN DATE_FORMAT(lctn_est_dt, '%Y') ELSE NULL END AS lctn_est_dt_frmt, CASE WHEN lctn_exp_dt_frmt=1 THEN DATE_FORMAT(lctn_exp_dt, '%d %b %Y') WHEN lctn_exp_dt_frmt=2 THEN DATE_FORMAT(lctn_exp_dt, '%b %Y') WHEN lctn_exp_dt_frmt=3 THEN DATE_FORMAT(lctn_exp_dt, '%Y') ELSE NULL END AS lctn_exp_dt_frmt
          FROM lctn_aka
          INNER JOIN lctn ON lctn_prvs_id=lctn_id
          WHERE lctn_sbsq_id='$lctn_id' AND EXISTS(SELECT 1 FROM ptsttng_lctn WHERE lctn_id=sttng_lctnid UNION SELECT 1 FROM rel_lctn INNER JOIN ptsttng_lctn ON rel_lctn1=sttng_lctnid WHERE rel_lctn2=lctn_id)
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
      $prvs_lctn_nm='<a href="/playtext/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$prvs_lctn_dt;
      if(!$row['lctn_fctn']) {if(!$row['lctn_prvs_sg'] && !$row['lctn_sbsq_sg']) {$prvs_lctns[]=$prvs_lctn_nm;} elseif($row['lctn_sbsq_sg']) {$prvs_lctns_prt_of[]=$prvs_lctn_nm;} else {$prvs_lctns_cmprs[]=$prvs_lctn_nm;}}
      else {if(!$row['lctn_prvs_sg'] && !$row['lctn_sbsq_sg']) {$prvs_lctns_fctn[]=$prvs_lctn_nm;} elseif($row['lctn_sbsq_sg']) {$prvs_lctns_fctn_prt_of[]=$prvs_lctn_nm;} else {$prvs_lctns_fctn_cmprs[]=$prvs_lctn_nm;}}
      $rel_lctn_cnt[]='1';
    }

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptsttng_lctn ptsl
          INNER JOIN pt p1 ON ptsl.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE ptsl.sttng_lctnid='$lctn_id'
          GROUP BY pt_id
          UNION
          SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM rel_lctn
          INNER JOIN ptsttng_lctn ptsl ON rel_lctn1=sttng_lctnid $exp_fctn_insrt INNER JOIN pt p1 ON ptsl.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          LEFT OUTER JOIN ptsttng_lctn_alt ptsla ON ptsl.ptid=ptsla.ptid AND ptsl.sttngid=ptsla.sttngid AND ptsl.sttng_lctnid=ptsla.sttng_lctnid
          WHERE rel_lctn2='$lctn_id' AND ptsla.ptid IS NULL $exp_insrt $fctn_insrt
          GROUP BY pt_id
          UNION
          SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptsttng_lctn_alt
          INNER JOIN pt p1 ON ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE sttng_lctn_altid='$lctn_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptsttng_lctn ptsl
          INNER JOIN pt p1 ON ptsl.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE sttng_lctnid='$lctn_id' AND coll_ov IS NULL
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM rel_lctn
          INNER JOIN ptsttng_lctn ptsl ON rel_lctn1=sttng_lctnid $exp_fctn_insrt INNER JOIN pt p1 ON ptsl.ptid=pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          LEFT OUTER JOIN ptsttng_lctn_alt ptsla ON ptsl.ptid=ptsla.ptid AND ptsl.sttngid=ptsla.sttngid AND ptsl.sttng_lctnid=ptsla.sttng_lctnid
          WHERE rel_lctn2='$lctn_id' AND coll_ov IS NULL AND ptsla.ptid IS NULL $exp_insrt $fctn_insrt
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptsttng_lctn_alt ptsla
          INNER JOIN pt p1 ON ptsla.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE sttng_lctn_altid='$lctn_id' AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='2') {$wrks_ids[]=$row['pt_id']; $txt_vrsn_nm='Collected Works';}
        else {$pt_ids[]=$row['pt_id']; if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}}
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'lctns'=>array(), 'sg_pts'=>array(), 'wrks_sg_pts'=>array());
      }

      if(!empty($pt_ids))
      {
        foreach($pt_ids as $pt_id)
        {
          $sql= "SELECT 1 FROM ptsttng_lctn WHERE ptid='$pt_id' AND sttng_lctnid='$lctn_id'
                UNION
                SELECT 1 FROM rel_lctn INNER JOIN ptsttng_lctn ON rel_lctn1=sttng_lctnid WHERE ptid='$pt_id' AND rel_lctn2='$lctn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      if(!empty($wrks_ids))
      {
        foreach($wrks_ids as $pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_cntr_rcv.inc.php';
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wrks_sg_rcv.inc.php';
        }
      }

      $k=0;
      $sql= "SELECT pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2, sttngid, sttng_lctn_ordr
            FROM ptsttng_lctn
            INNER JOIN lctn ON sttng_lctnid=lctn_id INNER JOIN pt ON ptid=pt_id
            WHERE sttng_lctnid='$lctn_id' AND coll_ov IS NULL
            GROUP BY pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2
            UNION
            SELECT pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2, ptsl.sttngid, sttng_lctn_ordr
            FROM rel_lctn
            INNER JOIN lctn ON rel_lctn1=lctn_id INNER JOIN ptsttng_lctn ptsl ON lctn_id=sttng_lctnid INNER JOIN pt ON ptsl.ptid=pt_id
            LEFT OUTER JOIN ptsttng_lctn_alt ptsla ON ptsl.ptid=ptsla.ptid AND ptsl.sttngid=ptsla.sttngid AND ptsl.sttng_lctnid=ptsla.sttng_lctnid
            WHERE rel_lctn2='$lctn_id' AND coll_ov IS NULL AND ptsla.ptid IS NULL $exp_insrt $fctn_insrt
            GROUP BY pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2
            UNION
            SELECT pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2, ptsla.sttngid, sttng_lctn_ordr
            FROM ptsttng_lctn_alt ptsla
            INNER JOIN ptsttng_lctn ptsl ON ptsla.ptid=ptsl.ptid AND ptsla.sttngid=ptsl.sttngid AND ptsla.sttng_lctnid=ptsl.sttng_lctnid
            INNER JOIN lctn ON ptsla.sttng_lctnid=lctn_id INNER JOIN pt ON ptsla.ptid=pt_id
            WHERE sttng_lctn_altid='$lctn_id' AND coll_ov IS NULL
            GROUP BY pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2
            ORDER BY sttngid ASC, sttng_lctn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring location data for playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['sttng_lctn_nt1']) {$sttng_lctn_nt1=html($row['sttng_lctn_nt1']).' ';} else {$sttng_lctn_nt1='';}
        if($row['sttng_lctn_nt2']) {if(!preg_match('/^(:|;|,|\.)/', $row['sttng_lctn_nt2'])) {$sttng_lctn_nt2=' '.html($row['sttng_lctn_nt2']);} else {$sttng_lctn_nt2=html($row['sttng_lctn_nt2']);}}
        else {$sttng_lctn_nt2='';}
        $sttng_lctn=$sttng_lctn_nt1.html($row['lctn_nm']).$sttng_lctn_nt2;
        if($sttng_lctn!==$lctn_nm) {$k++;}
        $pts[$row['pt_id']]['lctns'][]=$sttng_lctn;
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, coll_sbhdrid, coll_ordr
            FROM ptsttng_lctn ptsl
            INNER JOIN pt ON ptsl.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE sttng_lctnid='$lctn_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            UNION
            SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, coll_sbhdrid, coll_ordr
            FROM rel_lctn
            INNER JOIN ptsttng_lctn ptsl ON rel_lctn1=ptsl.sttng_lctnid $exp_fctn_insrt INNER JOIN pt ON ptsl.ptid=pt_id
            LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            LEFT OUTER JOIN ptsttng_lctn_alt ptsla ON ptsl.ptid=ptsla.ptid AND ptsl.sttngid=ptsla.sttngid AND ptsl.sttng_lctnid=ptsla.sttng_lctnid
            WHERE rel_lctn2='$lctn_id' AND ptsla.ptid IS NULL AND coll_ov IS NOT NULL $exp_insrt $fctn_insrt
            GROUP BY coll_ov, pt_id
            UNION
            SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, coll_sbhdrid, coll_ordr
            FROM ptsttng_lctn_alt ptsla
            INNER JOIN pt ON ptsla.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE sttng_lctn_altid='$lctn_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array(), 'lctns'=>array());
        }

        if(!empty($sg_pt_ids)) {foreach($sg_pt_ids as $sg_pt_id) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';}}

        $sql= "SELECT coll_ov, pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2, sttngid, sttng_lctn_ordr
              FROM ptsttng_lctn
              INNER JOIN lctn ON sttng_lctnid=lctn_id INNER JOIN pt ON ptid=pt_id
              WHERE sttng_lctnid='$lctn_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2
              UNION
              SELECT coll_ov, pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2, ptsl.sttngid, sttng_lctn_ordr
              FROM rel_lctn
              INNER JOIN lctn ON rel_lctn1=lctn_id INNER JOIN ptsttng_lctn ptsl ON lctn_id=sttng_lctnid INNER JOIN pt ON ptsl.ptid=pt_id
              LEFT OUTER JOIN ptsttng_lctn_alt ptsla ON ptsl.ptid=ptsla.ptid AND ptsl.sttngid=ptsla.sttngid AND ptsl.sttng_lctnid=ptsla.sttng_lctnid
              WHERE rel_lctn2='$lctn_id' AND ptsla.ptid IS NULL AND coll_ov IS NOT NULL $exp_insrt $fctn_insrt
              GROUP BY coll_ov, pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2
              UNION
              SELECT coll_ov, pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2, ptsla.sttngid, sttng_lctn_ordr
              FROM ptsttng_lctn_alt ptsla
              INNER JOIN ptsttng_lctn ptsl ON ptsla.ptid=ptsl.ptid AND ptsla.sttngid=ptsl.sttngid AND ptsla.sttng_lctnid=ptsl.sttng_lctnid
              INNER JOIN lctn ON ptsla.sttng_lctnid=lctn_id INNER JOIN pt ON ptsla.ptid=pt_id
              WHERE sttng_lctn_altid='$lctn_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, pt_id, sttng_lctn_nt1, lctn_nm, sttng_lctn_nt2
              ORDER BY sttngid ASC, sttng_lctn_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring location data for segment playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_lctn_nt1']) {$sttng_lctn_nt1=html($row['sttng_lctn_nt1']).' ';} else {$sttng_lctn_nt1='';}
          if($row['sttng_lctn_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_lctn_nt2'])) {$sttng_lctn_nt2=html($row['sttng_lctn_nt2']);} else {$sttng_lctn_nt2=' '.html($row['sttng_lctn_nt2']);}}
          else {$sttng_lctn_nt2='';}
          $sttng_lctn=$sttng_lctn_nt1.html($row['lctn_nm']).$sttng_lctn_nt2;
          if($sttng_lctn!==$lctn_nm) {$k++;}
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]['lctns'][]=$sttng_lctn;
        }
      }
    }

    $lctn_id=html($lctn_id);
    include 'playtext-setting-location.html.php';
  }
?>