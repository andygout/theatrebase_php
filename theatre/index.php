<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/theatre/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $thtr_url=cln($_GET['thtr_url']);

  $sql="SELECT thtr_id FROM thtr WHERE thtr_url='$thtr_url' AND (thtr_tr_ov!=1 OR thtr_tr_ov IS NULL)";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $thtr_id=$row['thtr_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT thtr_fll_nm, thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, thtr_adrs, thtr_cpcty, CASE WHEN thtr_opn_dt_frmt=1 THEN DATE_FORMAT(thtr_opn_dt, '%d %b %Y') WHEN thtr_opn_dt_frmt=2 THEN DATE_FORMAT(thtr_opn_dt, '%b %Y') WHEN thtr_opn_dt_frmt=3 THEN DATE_FORMAT(thtr_opn_dt, '%Y') ELSE NULL END AS thtr_opn_dt, CASE WHEN thtr_cls_dt_frmt=1 THEN DATE_FORMAT(thtr_cls_dt, '%d %b %Y') WHEN thtr_cls_dt_frmt=2 THEN DATE_FORMAT(thtr_cls_dt, '%b %Y') WHEN thtr_cls_dt_frmt=3 THEN DATE_FORMAT(thtr_cls_dt, '%Y') ELSE NULL END AS thtr_cls_dt
          FROM thtr
          WHERE thtr_id='$thtr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $thtr_nm=html($row['thtr_nm']);
    if($row['thtr_sffx_num']) {$thtr_sffx_rmn=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$thtr_sffx_rmn='';}
    $pagetab=html($row['thtr_fll_nm'].$thtr_sffx_rmn);
    if($row['thtr_cpcty']) {$thtr_cpcty=html($row['thtr_cpcty']); if(preg_match('/[1-9][0-9]{3}/', $thtr_cpcty)) {$thtr_cpcty=preg_replace('/([0-9])([0-9]{3})/', '$1,$2', $thtr_cpcty);}}
    else {$thtr_cpcty=NULL;}
    $thtr_opn_dt=html($row['thtr_opn_dt']);
    $thtr_cls_dt=html($row['thtr_cls_dt']);
    $thtr_fll_nm=html($row['thtr_fll_nm']);
    if($row['sbthtr_nm']) {$sbthtr_nm=':</br>'.html($row['sbthtr_nm']);} else {$sbthtr_nm='';}
    if($row['thtr_lctn']) {$thtr_lctn='('.html($row['thtr_lctn']).')';} else {$thtr_lctn='';}

    $sql= "SELECT thtr_adrs FROM thtr
          WHERE thtr_id=(CASE WHEN (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') IS NULL THEN '$thtr_id' ELSE (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') END)";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $thtr_adrs=preg_replace('/,,/', ', ', html($row['thtr_adrs']));

    $sql= "SELECT lctn_nm, lctn_url
          FROM thtr
          INNER JOIN lctn ON thtr_lctnid=lctn_id
          WHERE thtr_id=(CASE WHEN (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') IS NULL THEN '$thtr_id' ELSE (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') END)";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring location (link) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0)
    {$lctn_lnk_nm='<a href="/theatre/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>';} else {$lctn_lnk_nm=NULL;}

    $sql= "SELECT lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
          FROM thtr t
          INNER JOIN rel_lctn ON thtr_lctnid=rel_lctn1 INNER JOIN lctn ON rel_lctn2=lctn_id
          LEFT OUTER JOIN thtr_lctn_alt tla ON thtr_id=thtrid AND t.thtr_lctnid=tla.thtr_lctnid
          WHERE thtr_id=(CASE WHEN (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') IS NULL THEN '$thtr_id' ELSE (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') END) AND lctn_exp=0 AND lctn_fctn=0 AND tla.thtrid IS NULL
          UNION
          SELECT lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
          FROM thtr t
          INNER JOIN rel_lctn ON t.thtr_lctnid=rel_lctn1 INNER JOIN thtr_lctn_alt tla ON rel_lctn2=thtr_lctn_altid
          INNER JOIN lctn ON thtr_lctn_altid=lctn_id
          WHERE thtr_id=(CASE WHEN (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') IS NULL THEN '$thtr_id' ELSE (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') END) AND thtr_id=thtrid AND t.thtr_lctnid=tla.thtr_lctnid
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related location (link) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']);} else {$rel_lctn_nt1='';}
      if($row['rel_lctn_nt2']) {$rel_lctn_nt2=html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2='';}
      $rel_lctns[]=$rel_lctn_nt1.'<a href="/theatre/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt2;
    }

    $sql= "SELECT thtr_typ_nm, thtr_typ_url
          FROM thtrtyp
          INNER JOIN thtr_typ ON thtr_typid=thtr_typ_id
          WHERE thtrid='$thtr_id'
          ORDER BY thtr_typ_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$thtr_typs[]='<a href="/theatre/type/'.html($row['thtr_typ_url']).'">'.html($row['thtr_typ_nm']).'</a>';}

    $sql= "SELECT comp_nm, comp_url
          FROM thtrcomp
          INNER JOIN comp ON compid=comp_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE thtrid=(CASE WHEN (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') IS NULL THEN '$thtr_id' ELSE (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') END) AND thtr_clsd=0
          ORDER BY thtr_comp_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company (owned by) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$thtr_comps[]='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}

    $sql= "SELECT thtr_id, sbthtr_nm, thtr_url
          FROM thtr
          WHERE srthtrid='$thtr_id' AND thtr_clsd=0 AND thtr_nm_exp=0
          ORDER BY sbthtr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring sub-theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$sbthtrs[$row['thtr_id']]=array('sbthtr'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>', 'sbthtr_sbsq_nms'=>array(), 'sbthtr_prvs_nms'=>array());}

      $sql= "SELECT thtr_prvs_id, t2.sbthtr_nm, t2.thtr_url
            FROM thtr t1
            INNER JOIN thtr_aka ON t1.thtr_id=thtr_prvs_id INNER JOIN thtr t2 ON thtr_sbsq_id=t2.thtr_id
            WHERE t1.srthtrid='$thtr_id' AND t1.thtr_clsd=0 AND t1.thtr_nm_exp=0
            ORDER BY t2.thtr_nm_frm_dt DESC, t2.thtr_nm_exp_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring sub-theatre subsequently named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$sbthtrs[$row['thtr_prvs_id']]['sbthtr_sbsq_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}

      $sql= "SELECT thtr_sbsq_id, t2.sbthtr_nm, t2.thtr_url
            FROM thtr t1
            INNER JOIN thtr_aka ON t1.thtr_id=thtr_sbsq_id INNER JOIN thtr t2 ON thtr_prvs_id=t2.thtr_id
            WHERE t1.srthtrid='$thtr_id' AND t1.thtr_clsd=0 AND t1.thtr_nm_exp=0
            ORDER BY t2.thtr_nm_frm_dt DESC, t2.thtr_nm_exp_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring sub-theatre previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$sbthtrs[$row['thtr_sbsq_id']]['sbthtr_prvs_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}
    }

    $sql= "SELECT thtr_id, sbthtr_nm, thtr_url
          FROM thtr
          WHERE srthtrid='$thtr_id' AND (thtr_clsd=1 OR thtr_cls_dt <= CURDATE()) AND thtr_nm_exp=0
          ORDER BY sbthtr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (closed) sub-theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$sbthtrs_clsd[$row['thtr_id']]=array('sbthtr_clsd'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>', 'sbthtr_clsd_prvs_nm'=>array());}

      $sql= "SELECT thtr_sbsq_id, t2.sbthtr_nm, t2.thtr_url
            FROM thtr t1
            INNER JOIN thtr_aka ON sbthtrid=thtr_sbsq_id
            INNER JOIN thtr t2 ON thtr_prvs_id=t2.thtr_id
            WHERE srthtrid='$thtr_id' AND (t1.thtr_clsd=1 OR t1.thtr_cls_dt <= CURDATE()) AND t1.thtr_nm_exp=0
            ORDER BY t2.thtr_nm_frm_dt DESC, t2.thtr_nm_exp_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (closed) sub-theatre previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$sbthtrs_clsd[$row['thtr_sbsq_id']]['sbthtr_clsd_prvs_nm'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}
    }

    $sql= "SELECT t2.thtr_id, t2.thtr_nm, t2.thtr_url
          FROM thtr t1
          INNER JOIN thtr t2 ON t1.srthtrid=t2.thtr_id
          WHERE t1.thtr_id='$thtr_id' AND t2.thtr_clsd=0 AND t2.thtr_nm_exp=0
          ORDER BY thtr_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring main theatre data (for which theatre is as a sub-theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$thtrs[$row['thtr_id']]=array('thtr'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>', 'thtr_sbsq_nms'=>array(), 'thtr_prvs_nms'=>array());}

      $sql= "SELECT thtr_prvs_id, t3.thtr_nm, t3.thtr_url
            FROM thtr t1
            INNER JOIN thtr t2 ON t1.srthtrid=t2.thtr_id INNER JOIN thtr_aka ON t1.srthtrid=thtr_prvs_id INNER JOIN thtr t3 ON thtr_sbsq_id=t3.thtr_id
            WHERE t1.thtr_id='$thtr_id' AND t2.thtr_clsd=0 AND t2.thtr_nm_exp=0
            ORDER BY t3.thtr_nm_frm_dt DESC, t3.thtr_nm_exp_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring main theatre (for which theatre is as a sub-theatre) previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$thtrs[$row['thtr_prvs_id']]['thtr_sbsq_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}

      $sql= "SELECT thtr_sbsq_id, t3.thtr_nm, t3.thtr_url
            FROM thtr t1
            INNER JOIN thtr t2 ON t1.srthtrid=t2.thtr_id INNER JOIN thtr_aka ON t1.srthtrid=thtr_sbsq_id INNER JOIN thtr t3 ON thtr_prvs_id=t3.thtr_id
            WHERE t1.thtr_id='$thtr_id' AND t2.thtr_clsd=0 AND t2.thtr_nm_exp=0
            ORDER BY t3.thtr_nm_frm_dt DESC, t3.thtr_nm_exp_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring main theatre (for which theatre is as a sub-theatre) subsequently named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$thtrs[$row['thtr_sbsq_id']]['thtr_prvs_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}
    }

    $sql= "SELECT t2.thtr_id, t2.thtr_nm, t2.thtr_url
          FROM thtr t1
          INNER JOIN thtr t2 ON t1.srthtrid=t2.thtr_id
          WHERE t1.thtr_id='$thtr_id' AND (t2.thtr_clsd=1 OR t2.thtr_cls_dt <= CURDATE()) AND t2.thtr_nm_exp=0
          ORDER BY thtr_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring main theatre (closed) data (for which theatre is as a sub-theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$thtrs_clsd[$row['thtr_id']]=array('thtr_clsd'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>', 'thtr_clsd_prvs_nm'=>array());}

      $sql= "SELECT thtr_sbsq_id, t3.thtr_nm, t3.thtr_url
            FROM thtr t1
            INNER JOIN thtr t2 ON t1.srthtrid=t2.thtr_id INNER JOIN thtr_aka ON t2.srthtrid=thtr_sbsq_id INNER JOIN thtr t3 ON thtr_prvs_id=t3.thtr_id
            WHERE t1.thtr_id='$thtr_id' AND (t2.thtr_clsd=1 OR t2.thtr_cls_dt <= CURDATE()) AND t2.thtr_nm_exp=0
            ORDER BY t3.thtr_nm_frm_dt DESC, t3.thtr_nm_exp_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring main theatre (closed) previously named data (for which theatre is as a sub-theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$thtrs_clsd[$row['thtr_sbsq_id']]['thtr_clsd_prvs_nm'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}
    }

    $sql= "SELECT t1.thtr_nm, t1.sbthtr_nm, t1.thtr_lctn, t1.thtr_url, t2.thtr_url AS t2_thtr_url, CASE WHEN t1.thtr_nm_frm_dt_frmt=1 THEN DATE_FORMAT(t1.thtr_nm_frm_dt, '%d %b %Y') WHEN t1.thtr_nm_frm_dt_frmt=2 THEN DATE_FORMAT(t1.thtr_nm_frm_dt, '%b %Y') WHEN t1.thtr_nm_frm_dt_frmt=3 THEN DATE_FORMAT(t1.thtr_nm_frm_dt, '%Y') ELSE NULL END AS thtr_nm_frm_dt_frmt, CASE WHEN t1.thtr_nm_exp_dt_frmt=1 THEN DATE_FORMAT(t1.thtr_nm_exp_dt, '%d %b %Y') WHEN t1.thtr_nm_exp_dt_frmt=2 THEN DATE_FORMAT(t1.thtr_nm_exp_dt, '%b %Y') WHEN t1.thtr_nm_exp_dt_frmt=3 THEN DATE_FORMAT(t1.thtr_nm_exp_dt, '%Y') ELSE NULL END AS thtr_nm_exp_dt_frmt
          FROM thtr_aka
          INNER JOIN thtr t1 ON thtr_sbsq_id=t1.thtr_id LEFT OUTER JOIN thtr t2 ON t1.srthtrid = t2.thtr_id
          WHERE thtr_prvs_id='$thtr_id'
          ORDER BY t1.thtr_nm_frm_dt DESC, t1.thtr_nm_exp_dt DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring subsequently known as data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['thtr_lctn']) {$sbsq_thtr_lctn=' ('.html($row['thtr_lctn']).')';} else {$sbsq_thtr_lctn='';}
      if($row['sbthtr_nm']) {$sbsq_thtr='<a href="/theatre/'.html($row['t2_thtr_url']).'">'.html($row['thtr_nm']).'</a>: <a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>'.$sbsq_thtr_lctn;}
      else {$sbsq_thtr='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>'.$sbsq_thtr_lctn;}
      if($row['thtr_nm_frm_dt_frmt'] || $row['thtr_nm_exp_dt_frmt'])
      {
        if($row['thtr_nm_frm_dt_frmt']) {$sbsq_thtr_nm_frm_dt='from '.html($row['thtr_nm_frm_dt_frmt']);} else {$sbsq_thtr_nm_frm_dt='';}
        if($row['thtr_nm_exp_dt_frmt']) {$sbsq_thtr_nm_exp_dt='until '.html($row['thtr_nm_exp_dt_frmt']);} else {$sbsq_thtr_nm_exp_dt='';}
        if($row['thtr_nm_frm_dt_frmt'] && $row['thtr_nm_exp_dt_frmt']) {$sbsq_spc=' ';} else {$sbsq_spc='';}
        $sbsq_thtr_nm_dt=' <em>('.$sbsq_thtr_nm_frm_dt.$sbsq_spc.$sbsq_thtr_nm_exp_dt.')</em>';
      }
      else {$sbsq_thtr_nm_dt='';}
      $sbsqs[]=$sbsq_thtr.$sbsq_thtr_nm_dt;
    }

    $sql= "SELECT t1.thtr_nm, t1.sbthtr_nm, t1.thtr_lctn, t1.thtr_url, t2.thtr_url AS t2_thtr_url, CASE WHEN t1.thtr_nm_frm_dt_frmt=1 THEN DATE_FORMAT(t1.thtr_nm_frm_dt, '%d %b %Y') WHEN t1.thtr_nm_frm_dt_frmt=2 THEN DATE_FORMAT(t1.thtr_nm_frm_dt, '%b %Y') WHEN t1.thtr_nm_frm_dt_frmt=3 THEN DATE_FORMAT(t1.thtr_nm_frm_dt, '%Y') ELSE NULL END AS thtr_nm_frm_dt_frmt, CASE WHEN t1.thtr_nm_exp_dt_frmt=1 THEN DATE_FORMAT(t1.thtr_nm_exp_dt, '%d %b %Y') WHEN t1.thtr_nm_exp_dt_frmt=2 THEN DATE_FORMAT(t1.thtr_nm_exp_dt, '%b %Y') WHEN t1.thtr_nm_exp_dt_frmt=3 THEN DATE_FORMAT(t1.thtr_nm_exp_dt, '%Y') ELSE NULL END AS thtr_nm_exp_dt_frmt
          FROM thtr_aka
          INNER JOIN thtr t1 ON thtr_prvs_id=thtr_id LEFT OUTER JOIN thtr t2 ON t1.srthtrid = t2.thtr_id
          WHERE thtr_sbsq_id='$thtr_id'
          ORDER BY t1.thtr_nm_frm_dt DESC, t1.thtr_nm_exp_dt DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring previously known as data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['thtr_lctn']) {$prvs_thtr_lctn=' ('.html($row['thtr_lctn']).')';} else {$prvs_thtr_lctn='';}
      if($row['sbthtr_nm']) {$prvs_thtr='<a href="/theatre/'.html($row['t2_thtr_url']).'">'.html($row['thtr_nm']).'</a>: <a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>'.$prvs_thtr_lctn;}
      else {$prvs_thtr='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>'.$prvs_thtr_lctn;}
      if($row['thtr_nm_frm_dt_frmt'] || $row['thtr_nm_exp_dt_frmt'])
      {
        if($row['thtr_nm_frm_dt_frmt']) {$prvs_thtr_nm_frm_dt='from '.html($row['thtr_nm_frm_dt_frmt']);} else {$prvs_thtr_nm_frm_dt='';}
        if($row['thtr_nm_exp_dt_frmt']) {$prvs_thtr_nm_exp_dt='until '.html($row['thtr_nm_exp_dt_frmt']);} else {$prvs_thtr_nm_exp_dt='';}
        if($row['thtr_nm_frm_dt_frmt'] && $row['thtr_nm_exp_dt_frmt']) {$prvs_spc=' ';} else {$prvs_spc='';}
        $prvs_thtr_nm_dt=' <em>('.$prvs_thtr_nm_frm_dt.$prvs_spc.$prvs_thtr_nm_exp_dt.')</em>';
      }
      else {$prvs_thtr_nm_dt='';}
      $prvss[]=$prvs_thtr.$prvs_thtr_nm_dt;
    }

    if(!empty($sbsqs) || !empty($prvss))
    {
      $sql= "SELECT CASE WHEN thtr_nm_frm_dt_frmt=1 THEN DATE_FORMAT(thtr_nm_frm_dt, '%d %b %Y') WHEN thtr_nm_frm_dt_frmt=2 THEN DATE_FORMAT(thtr_nm_frm_dt, '%b %Y') WHEN thtr_nm_frm_dt_frmt=3 THEN DATE_FORMAT(thtr_nm_frm_dt, '%Y') ELSE NULL END AS thtr_nm_frm_dt, CASE WHEN thtr_nm_exp_dt_frmt=1 THEN DATE_FORMAT(thtr_nm_exp_dt, '%d %b %Y') WHEN thtr_nm_exp_dt_frmt=2 THEN DATE_FORMAT(thtr_nm_exp_dt, '%b %Y') WHEN thtr_nm_exp_dt_frmt=3 THEN DATE_FORMAT(thtr_nm_exp_dt, '%Y') ELSE NULL END AS thtr_nm_exp_dt
            FROM thtr
            WHERE thtr_id='$thtr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring theatre (name from/until) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['thtr_nm_frm_dt'] || $row['thtr_nm_exp_dt'])
      {
        if($row['thtr_nm_frm_dt']) {$thtr_nm_frm_dt='from '.html($row['thtr_nm_frm_dt']);} else {$thtr_nm_frm_dt='';}
        if($row['thtr_nm_exp_dt']) {$thtr_nm_exp_dt='until '.html($row['thtr_nm_exp_dt']);} else {$thtr_nm_exp_dt='';}
        if($row['thtr_nm_frm_dt'] && $row['thtr_nm_exp_dt']) {$spc=' ';} else {$spc='';}
        $thtr_nm_dt=' <em>('.$thtr_nm_frm_dt.$spc.$thtr_nm_exp_dt.')</em>';
      }
      else {$thtr_nm_dt='';}
    }

    $sql= "SELECT t1.thtr_id, t1.thtr_nm, t1.sbthtr_nm, t1.thtr_lctn, t1.thtr_url, t2.thtr_url AS t2_thtr_url, CASE WHEN (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') IS NULL THEN t1.thtr_adrs ELSE t2.thtr_adrs END AS thtr_adrs, CASE WHEN t1.thtr_opn_dt_frmt=1 THEN DATE_FORMAT(t1.thtr_opn_dt, '%d %b %Y') WHEN t1.thtr_opn_dt_frmt=2 THEN DATE_FORMAT(t1.thtr_opn_dt, '%b %Y') WHEN t1.thtr_opn_dt_frmt=3 THEN DATE_FORMAT(t1.thtr_opn_dt, '%Y') ELSE NULL END AS thtr_opn_dt_frmt, CASE WHEN t1.thtr_cls_dt_frmt=1 THEN DATE_FORMAT(t1.thtr_cls_dt, '%d %b %Y') WHEN t1.thtr_cls_dt_frmt=2 THEN DATE_FORMAT(t1.thtr_cls_dt, '%b %Y') WHEN t1.thtr_cls_dt_frmt=3 THEN DATE_FORMAT(t1.thtr_cls_dt, '%Y') ELSE NULL END AS thtr_cls_dt_frmt
          FROM thtr_alt_adrs
          INNER JOIN thtr t1 ON thtr_sbsqad_id=t1.thtr_id LEFT OUTER JOIN thtr t2 ON t1.srthtrid = t2.thtr_id
          WHERE thtr_prvsad_id='$thtr_id' AND t1.thtr_nm_exp=0
          ORDER BY t1.thtr_opn_dt DESC, t1.thtr_cls_dt DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring subsequently located data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        if($row['thtr_lctn']) {$sbsqad_thtr_lctn=' ('.html($row['thtr_lctn']).')';} else {$sbsqad_thtr_lctn='';}
        if($row['sbthtr_nm']) {$sbsqad_thtr='<a href="/theatre/'.html($row['t2_thtr_url']).'">'.html($row['thtr_nm']).'</a>: <a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>'.$sbsqad_thtr_lctn;}
        else {$sbsqad_thtr='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>'.$sbsqad_thtr_lctn;}
        if($row['thtr_adrs']) {$sbsqad_thtr_adrs='</br>'.preg_replace('/,,/', ', ', html($row['thtr_adrs']));} else {$sbsqad_thtr_adrs='';}
        if($row['thtr_opn_dt_frmt'] || $row['thtr_cls_dt_frmt'])
        {
          if($row['thtr_opn_dt_frmt']) {$sbsqad_thtr_opn_dt='from '.html($row['thtr_opn_dt_frmt']);} else {$sbsqad_thtr_opn_dt='';}
          if($row['thtr_cls_dt_frmt']) {$sbsqad_thtr_cls_dt='until '.html($row['thtr_cls_dt_frmt']);} else {$sbsqad_thtr_cls_dt='';}
          if($row['thtr_opn_dt_frmt'] && $row['thtr_cls_dt_frmt']) {$sbsqad_spc=' ';} else {$sbsqad_spc='';}
          $sbsqad_thtr_nm_dt=' <em>('.$sbsqad_thtr_opn_dt.$sbsqad_spc.$sbsqad_thtr_cls_dt.')</em>';
        }
        else {$sbsqad_thtr_nm_dt='';}
        $sbsqads[$row['thtr_id']]=array('sbsqad_thtr'=>$sbsqad_thtr.$sbsqad_thtr_nm_dt, 'sbsqad_thtr_adrs'=>$sbsqad_thtr_adrs, 'thtr_sbsqad_prvs_nms'=>array());
      }

      $sql= "SELECT thtr_sbsqad_id, t2.thtr_nm, t2.sbthtr_nm, t2.thtr_url, t3.thtr_url AS t3_thtr_url
            FROM thtr_alt_adrs
            INNER JOIN thtr t1 ON thtr_sbsqad_id=t1.thtr_id INNER JOIN thtr_aka ON thtr_sbsqad_id=thtr_sbsq_id
            INNER JOIN thtr t2 ON thtr_prvs_id=t2.thtr_id LEFT OUTER JOIN thtr t3 ON t2.srthtrid=t3.thtr_id
            WHERE thtr_prvsad_id='$thtr_id' AND t1.thtr_nm_exp=0 AND t2.thtr_nm_exp=1
            ORDER BY t2.thtr_nm_frm_dt DESC, t2.thtr_nm_exp_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring previously named data (for which theatre is a subsequently located theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['sbthtr_nm']) {$sbsqad_thtr_prvs_nm='<a href="/theatre/'.html($row['t3_thtr_url']).'">'.html($row['thtr_nm']).'</a>: <a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}
        else {$sbsqad_thtr_prvs_nm='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}
        $sbsqads[$row['thtr_sbsqad_id']]['thtr_sbsqad_prvs_nms'][]=$sbsqad_thtr_prvs_nm;
      }
    }

    $sql= "SELECT t1.thtr_id, t1.thtr_nm, t1.sbthtr_nm, t1.thtr_lctn, t1.thtr_url, t2.thtr_url AS t2_thtr_url, CASE WHEN (SELECT srthtrid FROM thtr WHERE thtr_id='$thtr_id') IS NULL THEN t1.thtr_adrs ELSE t2.thtr_adrs END AS thtr_adrs, CASE WHEN t1.thtr_opn_dt_frmt=1 THEN DATE_FORMAT(t1.thtr_opn_dt, '%d %b %Y') WHEN t1.thtr_opn_dt_frmt=2 THEN DATE_FORMAT(t1.thtr_opn_dt, '%b %Y') WHEN t1.thtr_opn_dt_frmt=3 THEN DATE_FORMAT(t1.thtr_opn_dt, '%Y') ELSE NULL END AS thtr_opn_dt_frmt, CASE WHEN t1.thtr_cls_dt_frmt=1 THEN DATE_FORMAT(t1.thtr_cls_dt, '%d %b %Y') WHEN t1.thtr_cls_dt_frmt=2 THEN DATE_FORMAT(t1.thtr_cls_dt, '%b %Y') WHEN t1.thtr_cls_dt_frmt=3 THEN DATE_FORMAT(t1.thtr_cls_dt, '%Y') ELSE NULL END AS thtr_cls_dt_frmt
          FROM thtr_alt_adrs
          INNER JOIN thtr t1 ON thtr_prvsad_id=t1.thtr_id LEFT OUTER JOIN thtr t2 ON t1.srthtrid = t2.thtr_id
          WHERE thtr_sbsqad_id='$thtr_id' AND t1.thtr_nm_exp=0
          ORDER BY t1.thtr_opn_dt DESC, t1.thtr_cls_dt DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring previously located data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        if($row['thtr_lctn']) {$prvsad_thtr_lctn=' ('.html($row['thtr_lctn']).')';} else {$prvsad_thtr_lctn='';}
        if($row['sbthtr_nm']) {$prvsad_thtr='<a href="/theatre/'.html($row['t2_thtr_url']).'">'.html($row['thtr_nm']).'</a>: <a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>'.$prvsad_thtr_lctn;}
        else {$prvsad_thtr='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>'.$prvsad_thtr_lctn;}
        if($row['thtr_adrs']) {$prvsad_thtr_adrs='</br>'.preg_replace('/,,/', ', ', html($row['thtr_adrs']));} else {$prvsad_thtr_adrs='';}
        if($row['thtr_opn_dt_frmt'] || $row['thtr_cls_dt_frmt'])
        {
          if($row['thtr_opn_dt_frmt']) {$prvsad_thtr_opn_dt='from '.html($row['thtr_opn_dt_frmt']);} else {$prvsad_thtr_opn_dt='';}
          if($row['thtr_cls_dt_frmt']) {$prvsad_thtr_cls_dt='until '.html($row['thtr_cls_dt_frmt']);} else {$prvsad_thtr_cls_dt='';}
          if($row['thtr_opn_dt_frmt'] && $row['thtr_cls_dt_frmt']) {$prvsad_spc=' ';} else {$prvsad_spc='';}
          $prvsad_thtr_nm_dt=' <em>('.$prvsad_thtr_opn_dt.$prvsad_spc.$prvsad_thtr_cls_dt.')</em>';
        }
        else {$prvsad_thtr_nm_dt='';}
        $prvsads[$row['thtr_id']]=array('prvsad_thtr'=>$prvsad_thtr.$prvsad_thtr_nm_dt, 'prvsad_thtr_adrs'=>$prvsad_thtr_adrs, 'thtr_prvsad_prvs_nms'=>array());
      }

      $sql= "SELECT thtr_prvsad_id, t2.thtr_nm, t2.sbthtr_nm, t2.thtr_url, t3.thtr_url AS t3_thtr_url
            FROM thtr_alt_adrs
            INNER JOIN thtr t1 ON thtr_prvsad_id=t1.thtr_id INNER JOIN thtr_aka ON thtr_prvsad_id=thtr_sbsq_id
            INNER JOIN thtr t2 ON thtr_prvs_id=t2.thtr_id LEFT OUTER JOIN thtr t3 ON t2.srthtrid=t3.thtr_id
            WHERE thtr_sbsqad_id='$thtr_id' AND t1.thtr_nm_exp=0 AND t2.thtr_nm_exp=1
            ORDER BY t2.thtr_nm_frm_dt DESC, t2.thtr_nm_exp_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring previously named data (for which theatre is a previously located theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['sbthtr_nm']) {$prvsad_thtr_prvs_nm='<a href="/theatre/'.html($row['t3_thtr_url']).'">'.html($row['thtr_nm']).'</a>: <a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}
        else {$prvsad_thtr_prvs_nm='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}
        $prvsads[$row['thtr_prvsad_id']]['thtr_prvsad_prvs_nms'][]=$prvsad_thtr_prvs_nm;
      }
    }

    $thtr_crdt_ids=array();
    $thtr_crdt_ids[]=$thtr_id;
    $sql= "SELECT thtr_id FROM thtr_aka INNER JOIN thtr ON thtr_sbsq_id=thtr_id WHERE thtr_prvs_id='$thtr_id'
          UNION
          SELECT thtr_id FROM thtr_aka INNER JOIN thtr ON thtr_prvs_id=thtr_id WHERE thtr_sbsq_id='$thtr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring previously & subsequently known as theatre_ids: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$thtr_crdt_ids[]=$row['thtr_id'];}

    $thtr_crdt_id=implode($thtr_crdt_ids, ' OR thtrid=');
    $p_thtr_crdt_id=implode($thtr_crdt_ids, ' OR p1.thtrid=');
    $srthtr_crdt_id=implode($thtr_crdt_ids, ' OR srthtrid=');
    $t_srthtr_crdt_id=implode($thtr_crdt_ids, ' OR t1.srthtrid=');

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, p2.prd_dts_info, p2.prd_tbc_nt, p2.prd_thtr_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prd p1
          INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (p1.thtrid=$p_thtr_crdt_id)
          GROUP BY prd_id
          UNION
          SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, p2.prd_dts_info, p2.prd_tbc_nt, p2.prd_thtr_nt, t2.thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM thtr t1
          INNER JOIN prd p1 ON t1.thtr_id=p1.thtrid INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr t2 ON p2.thtrid=t2.thtr_id
          WHERE (t1.srthtrid=$t_srthtr_crdt_id)
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, COALESCE(prd_alph, prd_nm)prd_alph, prd_dts_info, prd_tbc_nt, prd_thtr_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prd p1
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE (thtrid=$thtr_crdt_id) AND coll_ov IS NULL
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, COALESCE(prd_alph, prd_nm)prd_alph, prd_dts_info, prd_tbc_nt, prd_thtr_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM thtr
          INNER JOIN prd p1 ON thtr_id=thtrid
          WHERE (srthtrid=$srthtr_crdt_id) AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'prd_thtr_nt'=>html($row['prd_thtr_nt']), 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql= "SELECT 1 FROM prd WHERE prd_id='$prd_id' AND thtrid='$thtr_id'
                UNION
                SELECT 1 FROM thtr INNER JOIN prd ON thtr_id=thtrid WHERE prd_id='$prd_id' AND srthtrid='$thtr_id'
                UNION
                SELECT 1 FROM thtr_aka INNER JOIN prd ON thtr_prvs_id=thtrid WHERE prd_id='$prd_id' AND thtr_sbsq_id='$thtr_id'
                UNION
                SELECT 1 FROM thtr_aka INNER JOIN prd ON thtr_sbsq_id=thtrid WHERE prd_id='$prd_id' AND thtr_prvs_id='$thtr_id'
                UNION
                SELECT 1 FROM thtr_aka INNER JOIN thtr ON thtr_prvs_id=srthtrid INNER JOIN prd ON thtr_id=thtrid WHERE prd_id='$prd_id' AND thtr_sbsq_id='$thtr_id'
                UNION
                SELECT 1 FROM thtr_aka INNER JOIN thtr ON thtr_sbsq_id=srthtrid INNER JOIN prd ON thtr_id=thtrid WHERE prd_id='$prd_id' AND thtr_prvs_id='$thtr_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this theatre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv_tr.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, prd_thtr_nt, thtr_fll_nm FROM prd
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE (thtrid=$thtr_crdt_id) AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            UNION
            SELECT coll_ov, prd_id, prd_nm, prd_url, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, prd_thtr_nt, thtr_fll_nm FROM thtr
            INNER JOIN prd ON thtr_id=thtrid
            WHERE (srthtrid=$srthtr_crdt_id) AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, prd_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $sg_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'prd_thtr_nt'=>html($row['prd_thtr_nt']), 'thtr'=>$thtr, 'wri_rls'=>array());
      }

      if(!empty($sg_prd_ids))
      {
        foreach($sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv_tr.inc.php';
        }
      }
    }

    $sql= "SELECT awrds_nm, awrds_url, awrd_yr, awrd_yr_end, awrd_yr_url, awrd_dt, DATE_FORMAT(awrd_dt, '%a, %d %b %Y') AS awrd_dt_dsply, thtr_fll_nm
          FROM awrd
          INNER JOIN thtr ON thtrid=thtr_id INNER JOIN awrds ON awrdsid=awrds_id
          WHERE (thtrid=$thtr_crdt_id)
          UNION
          SELECT awrds_nm, awrds_url, awrd_yr, awrd_yr_end, awrd_yr_url, awrd_dt, DATE_FORMAT(awrd_dt, '%a, %d %b %Y') AS awrd_dt_dsply, thtr_fll_nm
          FROM thtr
          INNER JOIN awrd a ON thtr_id=a.thtrid INNER JOIN awrds ON a.awrdsid=awrds_id
          WHERE (srthtrid=$srthtr_crdt_id)
          ORDER BY awrd_dt DESC, awrd_yr DESC, awrds_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if(preg_match('/TBC$/', $row['thtr_fll_nm'])) {$thtr='<em>'.html($row['thtr_fll_nm']).'</em>';} else {$thtr=html($row['thtr_fll_nm']);}
      if($row['awrd_yr_end']) {$awrd_yr_end=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end='';}
      $awrd_nm_yr='<a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrds_nm']. ' '.$row['awrd_yr']).html($awrd_yr_end).'</a>';
      $awrds[]=array('awrd_nm_yr'=>$awrd_nm_yr, 'awrd_dt'=>$row['awrd_dt_dsply'], 'thtr'=>$thtr);
    }

    $thtr_id=html($thtr_id);
    include 'theatre.html.php';
  }
?>