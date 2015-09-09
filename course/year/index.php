<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
  $crs_yr=cln($_GET['crs_yr']);

  if($crs_yr<1000 || $crs_yr>9999)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    if($crs_yr>1000) {$crs_yr_lst='<a href="/course/year/'.html($crs_yr - 1).'">'.html($crs_yr - 1).'</a>';} else {$crs_yr_lst=NULL;}
    if($crs_yr<9999) {$crs_yr_nxt='<a href="/course/year/'.html($crs_yr+1).'">'.html($crs_yr+1).'</a>';} else {$crs_yr_nxt=NULL;}
    $pagetitle=html($crs_yr);

    $sql= "SELECT comp_nm, comp_url, crs_typ_nm, crs_typ_url, crs_yr_strt, crs_yr_end, crs_yr_url,
          DATE_FORMAT(crs_dt_strt, '%d %b %Y') AS crs_dt_strt_dsply, DATE_FORMAT(crs_dt_end, '%d %b %Y') AS crs_dt_end_dsply
          FROM crs
          INNER JOIN comp ON crs_schlid=comp_id
          INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE crs_yr_end='$crs_yr'
          ORDER BY comp_nm ASC, crs_typ_nm ASC, crs_dt_end DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring courses (as drama school): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['crs_yr_strt']!==$row['crs_yr_end'])
      {
        $crs_yr_dsply=$row['crs_yr_strt'].' - '.$row['crs_yr_end'];
        $crs_yr_nm_dsply=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);
      }
      else
      {$crs_yr_dsply=$row['crs_yr_strt']; $crs_yr_nm_dsply=$row['crs_yr_strt'];}
      if($row['crs_dt_strt_dsply'] && $row['crs_dt_end_dsply']) {$crs_dts=$row['crs_dt_strt_dsply'].' - '.$row['crs_dt_end_dsply'];}
      else {$crs_dts=$crs_yr_dsply;}
      $crs_nm='<a href="/course/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'/'.html($row['crs_yr_url']).'">'.html($row['comp_nm']).': '.html($row['crs_typ_nm']).' ('.$crs_yr_nm_dsply.')</a>';
      $yr_crss[]=array('crs_nm'=>$crs_nm, 'crs_dts'=>html($crs_dts));
    }

    include 'course-year.html.php';
  }
?>