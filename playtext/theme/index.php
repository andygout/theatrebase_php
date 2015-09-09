<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/theme/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $thm_url=cln($_GET['thm_url']);

  $sql="SELECT thm_id FROM thm WHERE thm_url='$thm_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $thm_id=$row['thm_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT thm_nm FROM thm WHERE thm_id='$thm_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['thm_nm']);
    $thm_nm=html($row['thm_nm']);

    $sql= "SELECT 1 FROM prdthm WHERE thmid='$thm_id'
          UNION
          SELECT 1 FROM rel_thm INNER JOIN prdthm ON rel_thm1=thmid WHERE rel_thm2='$thm_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of theme as theme for production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/production/theme/'.html($thm_url).'">Productions</a> with this theme';} else {$lnk=NULL;}

    $sql= "SELECT thm_nm, thm_url
          FROM rel_thm
          INNER JOIN thm ON rel_thm2=thm_id
          WHERE rel_thm1='$thm_id' AND (EXISTS(SELECT 1 FROM ptthm WHERE thmid='$thm_id') OR EXISTS(SELECT 1 FROM rel_thm INNER JOIN ptthm ON rel_thm1=thmid WHERE rel_thm2='$thm_id'))
          ORDER BY rel_thm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related theme (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_thms2[]='<a href="/playtext/theme/'.html($row['thm_url']).'">'.html($row['thm_nm']).'</a>';}

    $sql= "SELECT thm_nm, thm_url FROM rel_thm
          INNER JOIN ptthm ON rel_thm1=thmid INNER JOIN thm ON thmid=thm_id
          WHERE rel_thm2='$thm_id'
          UNION
          SELECT thm_nm, thm_url FROM rel_thm rg1
          INNER JOIN ptthm ON rg1.rel_thm1=thmid INNER JOIN rel_thm rg2 ON thmid=rg2.rel_thm1 INNER JOIN thm ON rg2.rel_thm2=thm_id
          WHERE rg1.rel_thm2='$thm_id' AND thm_id!=rg1.rel_thm2 AND thm_id IN(SELECT rel_thm1 FROM rel_thm WHERE rel_thm2='$thm_id')
          ORDER BY thm_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related theme (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_thms1[]='<a href="/playtext/theme/'.html($row['thm_url']).'">'.html($row['thm_nm']).'</a>';}

    $k=0;
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, NULL AS thm_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptthm pg
          INNER JOIN pt p1 ON pg.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE pg.thmid='$thm_id'
          GROUP BY pt_id
          UNION
          SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, NULL AS thm_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM rel_thm
          INNER JOIN ptthm pg ON rel_thm1=thmid INNER JOIN pt p1 ON pg.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE rel_thm2='$thm_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, thm_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptthm pg
          INNER JOIN thm ON thmid=thm_id INNER JOIN pt p1 ON pg.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE thmid='$thm_id' AND coll_ov IS NULL
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, GROUP_CONCAT(DISTINCT thm_nm ORDER BY thm_ordr ASC SEPARATOR ' / ') AS thm_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM rel_thm
          INNER JOIN thm ON rel_thm1=thm_id INNER JOIN ptthm pg ON rel_thm1=thmid INNER JOIN pt p1 ON pg.ptid=pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE rel_thm2='$thm_id' AND coll_ov IS NULL
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
        if($row['thm_nm'] && html($row['thm_nm'])!==$thm_nm) {$k++;}
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'thm_nm'=>html($row['thm_nm']), 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array(), 'wrks_sg_pts'=>array());
      }

      if(!empty($pt_ids))
      {
        foreach($pt_ids as $pt_id)
        {
          $sql= "SELECT 1 FROM ptthm WHERE ptid='$pt_id' AND thmid='$thm_id' LIMIT 1
                UNION
                SELECT 1 FROM rel_thm INNER JOIN ptthm ON rel_thm1=thmid WHERE ptid='$pt_id' AND rel_thm2='$thm_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this theme: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, thm_nm, coll_sbhdrid, coll_ordr
            FROM ptthm pg
            INNER JOIN thm ON thmid=thm_id INNER JOIN pt ON pg.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE thmid='$thm_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            UNION
            SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, GROUP_CONCAT(DISTINCT thm_nm ORDER BY thm_ordr ASC SEPARATOR ' / ') AS thm_nm, coll_sbhdrid, coll_ordr
            FROM rel_thm
            INNER JOIN thm ON rel_thm1=thm_id INNER JOIN ptthm pg ON rel_thm1=thmid INNER JOIN pt ON pg.ptid=pt_id
            LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE rel_thm2='$thm_id' and coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if(html($row['thm_nm'])!==$thm_nm) {$k++;}
        $sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'thm_nm'=>html($row['thm_nm']), 'wri_rls'=>array());
      }

      if(!empty($sg_pt_ids)) {foreach($sg_pt_ids as $sg_pt_id) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';}}
    }

    $thm_id=html($thm_id);
    include 'playtext-theme.html.php';
  }
?>