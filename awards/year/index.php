<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
  $awrd_yr=cln($_GET['awrd_yr']);

  if($awrd_yr<1000 || $awrd_yr>9999)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    if($awrd_yr>1000) {$awrd_yr_lst='<a href="/awards/year/'.html($awrd_yr-1).'">'.html($awrd_yr-1).'</a>';} else {$awrd_yr_lst=NULL;}
    if($awrd_yr<9999) {$awrd_yr_nxt='<a href="/awards/year/'.html($awrd_yr+1).'">'.html($awrd_yr+1).'</a>';} else {$awrd_yr_nxt=NULL;}
    $pagetitle=html($awrd_yr);

    $sql= "SELECT awrds_nm, awrds_url, awrd_yr, awrd_yr_end, awrd_yr_url, DATE_FORMAT(awrd_dt, '%d %b %Y') AS awrd_dt_dsply, COALESCE(awrds_alph, awrds_nm)awrds_alph, thtr_fll_nm
          FROM awrd
          INNER JOIN awrds ON awrdsid=awrds_id
          LEFT OUTER JOIN thtr ON thtrid=thtr_id
          WHERE awrd_yr='$awrd_yr' OR awrd_yr_end='$awrd_yr'
          ORDER BY awrd_dt DESC, awrds_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award categories (for display) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if(preg_match('/TBC$/', $row['thtr_fll_nm'])) {$thtr='<em>'.html($row['thtr_fll_nm']).'</em>';} else {$thtr=html($row['thtr_fll_nm']);}
      if($row['awrd_yr_end']) {$awrd_yr_end=html(preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']));} else {$awrd_yr_end='';}
      $awrd_nm_yr='<a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrds_nm']).' '.html($row['awrd_yr']).$awrd_yr_end.'</a>';
      $awrds[]=array('awrd_nm_yr'=>$awrd_nm_yr, 'awrd_dt'=>$row['awrd_dt_dsply'], 'thtr'=>$thtr);
    }

    include 'awards-year.html.php';
  }
?>