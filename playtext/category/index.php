<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/category/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $ctgry_url=cln($_GET['ctgry_url']);

  $sql="SELECT ctgry_id FROM ctgry WHERE ctgry_url='$ctgry_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $ctgry_id=$row['ctgry_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT ctgry_nm FROM ctgry WHERE ctgry_id='$ctgry_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['ctgry_nm']);

    $sql="SELECT 1 FROM prdctgry WHERE ctgryid='$ctgry_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of category for production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/production/category/'.html($ctgry_url).'">Productions</a> in this category';} else {$lnk=NULL;}

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptctgry
          INNER JOIN pt p1 ON ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE ctgryid='$ctgry_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptctgry pc
          INNER JOIN pt p1 ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE ctgryid='$ctgry_id' AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM ptctgry WHERE ptid='$pt_id' AND ctgryid='$ctgry_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this category: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
            FROM ptctgry pc
            INNER JOIN pt ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE ctgryid='$ctgry_id' and coll_ov IS NOT NULL
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

    $ctgry_id=html($ctgry_id);
    include 'playtext-category.html.php';
  }
?>