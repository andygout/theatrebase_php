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

    $sql= "SELECT 1 FROM ptgnr WHERE gnrid='$gnr_id'
          UNION
          SELECT 1 FROM rel_gnr INNER JOIN ptgnr ON rel_gnr1=gnrid WHERE rel_gnr2='$gnr_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of genre as genre for playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/playtext/genre/'.html($gnr_url).'">Playtexts</a> in this genre';} else {$lnk=NULL;}

    $sql= "SELECT gnr_nm, gnr_url
          FROM rel_gnr
          INNER JOIN gnr ON rel_gnr2=gnr_id
          WHERE rel_gnr1='$gnr_id' AND (EXISTS(SELECT 1 FROM prdgnr WHERE gnrid='$gnr_id') OR EXISTS(SELECT 1 FROM rel_gnr INNER JOIN prdgnr ON rel_gnr1=gnrid WHERE rel_gnr2='$gnr_id'))
          ORDER BY rel_gnr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related genre (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_gnrs2[]='<a href="/production/genre/'.html($row['gnr_url']).'">'.html($row['gnr_nm']).'</a>';}

    $sql= "SELECT gnr_nm, gnr_url FROM rel_gnr INNER JOIN prdgnr ON rel_gnr1=gnrid INNER JOIN gnr ON gnrid=gnr_id WHERE rel_gnr2='$gnr_id'
          UNION
          SELECT gnr_nm, gnr_url FROM rel_gnr rg1
          INNER JOIN prdgnr ON rg1.rel_gnr1=gnrid INNER JOIN rel_gnr rg2 ON gnrid=rg2.rel_gnr1 INNER JOIN gnr ON rg2.rel_gnr2=gnr_id
          WHERE rg1.rel_gnr2='$gnr_id' AND gnr_id!=rg1.rel_gnr2 AND gnr_id IN(SELECT rel_gnr1 FROM rel_gnr WHERE rel_gnr2='$gnr_id')
          ORDER BY gnr_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related genre (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_gnrs1[]='<a href="/production/genre/'.html($row['gnr_url']).'">'.html($row['gnr_nm']).'</a>';}

    $k=0;
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, NULL AS gnr_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdgnr pg
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE pg.gnrid='$gnr_id'
          GROUP BY prd_id
          UNION
          SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, NULL AS gnr_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM rel_gnr
          INNER JOIN prdgnr pg ON rel_gnr1=gnrid INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE rel_gnr2='$gnr_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, gnr_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdgnr pg
          INNER JOIN gnr ON gnrid=gnr_id INNER JOIN prd p1 ON pg.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE gnrid='$gnr_id' AND coll_ov IS NULL
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, GROUP_CONCAT(DISTINCT gnr_nm ORDER BY gnr_ordr ASC SEPARATOR ' / ') AS gnr_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM rel_gnr
          INNER JOIN gnr ON rel_gnr1=gnr_id INNER JOIN prdgnr pg ON rel_gnr1=gnrid INNER JOIN prd p1 ON pg.prdid=prd_id
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE rel_gnr2='$gnr_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        if($row['gnr_nm'] && html($row['gnr_nm'])!==$gnr_nm) {$k++;}
        $prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'gnr_nm'=>html($row['gnr_nm']), 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql= "SELECT 1 FROM prdgnr WHERE prdid='$prd_id' AND gnrid='$gnr_id'
                UNION
                SELECT 1 FROM rel_gnr INNER JOIN prdgnr ON rel_gnr1=gnrid WHERE prdid='$prd_id' AND rel_gnr2='$gnr_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this genre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, gnr_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM prdgnr pg
            INNER JOIN gnr ON gnrid=gnr_id INNER JOIN prd ON pg.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE gnrid='$gnr_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            UNION
            SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, GROUP_CONCAT(DISTINCT gnr_nm ORDER BY gnr_ordr ASC SEPARATOR ' / ') AS gnr_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM rel_gnr
            INNER JOIN gnr ON rel_gnr1=gnr_id INNER JOIN prdgnr pg ON rel_gnr1=gnrid INNER JOIN prd ON pg.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE rel_gnr2='$gnr_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        if(html($row['gnr_nm'])!==$gnr_nm) {$k++;}
        $sg_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'gnr_nm'=>html($row['gnr_nm']), 'wri_rls'=>array());
      }

      if(!empty($sg_prd_ids))
      {
        foreach($sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }
    }

    $gnr_id=html($gnr_id);
    include 'prod-genre.html.php';
  }
?>