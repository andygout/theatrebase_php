<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $pt_yr_num=cln($_GET['pt_yr_wrttn']);

  if(!preg_match('/^[1-9][0-9]{0,3}(-bce)?$/', $pt_yr_num))
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    if(preg_match('/^[1-9][0-9]{0,3}-bce$/', $pt_yr_num)) {$pt_yr_num=preg_replace('/^([1-9][0-9]{0,3})(-bce)/', '-$1', $pt_yr_num);}

    if(preg_match('/^-/', $pt_yr_num)) {$pt_yr_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $pt_yr_num); $pt_yr_dsply .= ' BCE';}
    else {$pt_yr_dsply=$pt_yr_num;}

    $pt_yr_lst=$pt_yr_num-1;
    if(preg_match('/^-/', $pt_yr_lst)) {$pt_yr_lst_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', '$1 BCE', $pt_yr_lst); $pt_yr_lst=preg_replace('/^-([1-9][0-9]{0,3})/', '$1-bce', $pt_yr_lst);}
    else {$pt_yr_lst_dsply=$pt_yr_lst;}
    $pt_yr_lst_lnk='<a href="/playtext/year/'.html($pt_yr_lst).'">'.html($pt_yr_lst_dsply).'</a>';

    $pt_yr_nxt=$pt_yr_num+1;
    if(preg_match('/^-/', $pt_yr_nxt)) {$pt_yr_nxt_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', '$1 BCE', $pt_yr_nxt); $pt_yr_nxt=preg_replace('/^-([1-9][0-9]{0,3})/', '$1-bce', $pt_yr_nxt);}
    else {$pt_yr_nxt_dsply=$pt_yr_nxt;}
    $pt_yr_nxt_lnk='<a href="/playtext/year/'.html($pt_yr_nxt).'">'.html($pt_yr_nxt_dsply).'</a>';

    $pagetitle=html($pt_yr_dsply);

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM pt p1
          INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE p1.pt_yr_wrttn='$pt_yr_num'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM pt p1
          LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE pt_yr_wrttn='$pt_yr_num' AND coll_ov IS NULL
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
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array(), 'wrks_sg_pts'=>array());
      }

      if(!empty($pt_ids))
      {
        foreach($pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM pt WHERE pt_id='$pt_id' AND pt_yr_wrttn='$pt_yr_num'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this year: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM pt
            LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE pt_yr_wrttn='$pt_yr_num' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
      }

      if(!empty($sg_pt_ids)) {foreach($sg_pt_ids as $sg_pt_id) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';}}
    }

    include 'playtext-year.html.php';
  }
?>