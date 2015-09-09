<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/theatre/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $thtr_url=cln($_GET['thtr_url']);

  $sql="SELECT thtr_id FROM thtr WHERE thtr_url='$thtr_url' AND thtr_tr_ov=1";
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
    $sql="SELECT thtr_fll_nm, thtr_sffx_num FROM thtr WHERE thtr_id='$thtr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['thtr_sffx_num']) {$thtr_sffx_rmn=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$thtr_sffx_rmn='';}
    $pagetab=html($row['thtr_fll_nm'].$thtr_sffx_rmn);
    $pagetitle=html($row['thtr_fll_nm']);

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, p2.prd_thtr_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prd p1
          INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE p1.thtrid='$thtr_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, prd_thtr_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prd p1
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE thtrid='$thtr_id' AND coll_ov IS NULL
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
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'prd_thtr_nt'=>html($row['prd_thtr_nt']), 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prd WHERE prd_id='$prd_id' AND thtrid='$thtr_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this festival: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, prd_thtr_nt, thtr_fll_nm
            FROM prd
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE thtrid='$thtr_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $sg_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'prd_thtr_nt'=>html($row['prd_thtr_nt']), 'thtr'=>$thtr, 'wri_rls'=>array());
      }

      if(!empty($sg_prd_ids))
      {
        foreach($sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }
    }

    $sql= "SELECT awrds_nm, awrds_url, awrd_yr, awrd_yr_end, awrd_yr_url, awrd_dt, DATE_FORMAT(awrd_dt, '%a, %d %b %Y') AS awrd_dt_dsply, thtr_fll_nm
          FROM awrd
          INNER JOIN thtr ON thtrid=thtr_id
          INNER JOIN awrds ON awrdsid=awrds_id
          WHERE thtrid='$thtr_id'
          ORDER BY awrd_dt DESC, awrd_yr DESC, awrds_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['awrd_yr_end']) {$awrd_yr_end=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end='';}
      $awrd_nm_yr='<a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrds_nm']. ' '.$row['awrd_yr']).html($awrd_yr_end).'</a>';
      $awrds[]=array('awrd_nm_yr'=>$awrd_nm_yr, 'awrd_dt'=>$row['awrd_dt_dsply'], 'thtr'=>$row['thtr_fll_nm']);
    }

    $thtr_id=html($thtr_id);
    include 'tour-type.html.php';
  }
?>