<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/genre/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $gnr_url=cln($_GET['gnr_url']);

  $sql="SELECT gnr_id FROM gnr WHERE gnr_url='$gnr_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $gnr_id=$row['gnr_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT gnr_nm FROM gnr WHERE gnr_id='$gnr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['gnr_nm']);
    $gnr_nm=html($row['gnr_nm']);

    $sql= "SELECT 1 FROM prdgnr WHERE gnrid='$gnr_id'
          UNION
          SELECT 1 FROM rel_gnr INNER JOIN prdgnr ON rel_gnr1=gnrid WHERE rel_gnr2='$gnr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of genre as genre for production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/production/genre/'.html($gnr_url).'">Productions</a> in this genre';} else {$lnk=NULL;}

    $sql= "SELECT gnr_nm, gnr_url
          FROM rel_gnr
          INNER JOIN gnr ON rel_gnr2=gnr_id
          WHERE rel_gnr1='$gnr_id' AND (EXISTS(SELECT 1 FROM ptgnr WHERE gnrid='$gnr_id') OR EXISTS(SELECT 1 FROM rel_gnr INNER JOIN ptgnr ON rel_gnr1=gnrid WHERE rel_gnr2='$gnr_id'))
          ORDER BY rel_gnr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related genre (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_gnrs2[]='<a href="/playtext/genre/'.html($row['gnr_url']).'">'.html($row['gnr_nm']).'</a>';}

    $sql= "SELECT gnr_nm, gnr_url
          FROM rel_gnr
          INNER JOIN ptgnr ON rel_gnr1=gnrid INNER JOIN gnr ON gnrid=gnr_id
          WHERE rel_gnr2='$gnr_id'
          UNION
          SELECT gnr_nm, gnr_url
          FROM rel_gnr rg1
          INNER JOIN ptgnr ON rg1.rel_gnr1=gnrid INNER JOIN rel_gnr rg2 ON gnrid=rg2.rel_gnr1 INNER JOIN gnr ON rg2.rel_gnr2=gnr_id
          WHERE rg1.rel_gnr2='$gnr_id' AND gnr_id!=rg1.rel_gnr2 AND gnr_id IN(SELECT rel_gnr1 FROM rel_gnr WHERE rel_gnr2='$gnr_id')
          ORDER BY gnr_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related genre (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_gnrs1[]='<a href="/playtext/genre/'.html($row['gnr_url']).'">'.html($row['gnr_nm']).'</a>';}

    $k=0;
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, NULL AS gnr_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptgnr pg
          INNER JOIN pt p1 ON pg.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE pg.gnrid='$gnr_id' GROUP BY pt_id
          UNION
          SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, NULL AS gnr_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM rel_gnr
          INNER JOIN ptgnr pg ON rel_gnr1=gnrid INNER JOIN pt p1 ON pg.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE rel_gnr2='$gnr_id' GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, gnr_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptgnr pg
          INNER JOIN gnr ON gnrid=gnr_id INNER JOIN pt p1 ON pg.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE gnrid='$gnr_id' AND coll_ov IS NULL GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, GROUP_CONCAT(DISTINCT gnr_nm ORDER BY gnr_ordr ASC SEPARATOR ' / ') AS gnr_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM rel_gnr
          INNER JOIN gnr ON rel_gnr1=gnr_id INNER JOIN ptgnr pg ON rel_gnr1=gnrid INNER JOIN pt p1 ON pg.ptid=pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE rel_gnr2='$gnr_id' AND coll_ov IS NULL GROUP BY pt_id
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
        if($row['gnr_nm'] && html($row['gnr_nm'])!==$gnr_nm) {$k++;}
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'gnr_nm'=>html($row['gnr_nm']), 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array(), 'wrks_sg_pts'=>array());
      }

      if(!empty($pt_ids))
      {
        foreach($pt_ids as $pt_id)
        {
          $sql= "SELECT 1 FROM ptgnr WHERE ptid='$pt_id' AND gnrid='$gnr_id'
                UNION
                SELECT 1 FROM rel_gnr INNER JOIN ptgnr ON rel_gnr1=gnrid WHERE ptid='$pt_id' AND rel_gnr2='$gnr_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this genre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, gnr_nm, coll_sbhdrid, coll_ordr
            FROM ptgnr pg
            INNER JOIN gnr ON gnrid=gnr_id INNER JOIN pt ON pg.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE gnrid='$gnr_id' AND coll_ov IS NOT NULL GROUP BY coll_ov, pt_id
            UNION
            SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, GROUP_CONCAT(DISTINCT gnr_nm ORDER BY gnr_ordr ASC SEPARATOR ' / ') AS gnr_nm, coll_sbhdrid, coll_ordr
            FROM rel_gnr
            INNER JOIN gnr ON rel_gnr1=gnr_id INNER JOIN ptgnr pg ON rel_gnr1=gnrid INNER JOIN pt ON pg.ptid=pt_id
            LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE rel_gnr2='$gnr_id' AND coll_ov IS NOT NULL GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if(html($row['gnr_nm'])!==$gnr_nm) {$k++;}
        $sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'gnr_nm'=>html($row['gnr_nm']), 'wri_rls'=>array());
      }

      if(!empty($sg_pt_ids)) {foreach($sg_pt_ids as $sg_pt_id) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';}}
    }

    $gnr_id=html($gnr_id);
    include 'playtext-genre.html.php';
  }
?>