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

    $sql= "SELECT 1 FROM ptthm WHERE thmid='$thm_id'
          UNION
          SELECT 1 FROM rel_thm INNER JOIN ptthm ON rel_thm1=thmid WHERE rel_thm2='$thm_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of theme as theme for playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/playtext/theme/'.html($thm_url).'">Playtexts</a> with this theme';} else {$lnk=NULL;}

    $sql= "SELECT thm_nm, thm_url
          FROM rel_thm
          INNER JOIN thm ON rel_thm2=thm_id
          WHERE rel_thm1='$thm_id' AND (EXISTS(SELECT 1 FROM prdthm WHERE thmid='$thm_id') OR EXISTS(SELECT 1 FROM rel_thm INNER JOIN prdthm ON rel_thm1=thmid WHERE rel_thm2='$thm_id'))
          ORDER BY rel_thm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related theme (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_thms2[]='<a href="/production/theme/'.html($row['thm_url']).'">'.html($row['thm_nm']).'</a>';}

    $sql= "SELECT thm_nm, thm_url FROM rel_thm
          INNER JOIN prdthm ON rel_thm1=thmid INNER JOIN thm ON thmid=thm_id
          WHERE rel_thm2='$thm_id'
          UNION
          SELECT thm_nm, thm_url FROM rel_thm rg1
          INNER JOIN prdthm ON rg1.rel_thm1=thmid INNER JOIN rel_thm rg2 ON thmid=rg2.rel_thm1 INNER JOIN thm ON rg2.rel_thm2=thm_id
          WHERE rg1.rel_thm2='$thm_id' AND thm_id!=rg1.rel_thm2 AND thm_id IN(SELECT rel_thm1 FROM rel_thm WHERE rel_thm2='$thm_id')
          ORDER BY thm_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related theme (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_thms1[]='<a href="/production/theme/'.html($row['thm_url']).'">'.html($row['thm_nm']).'</a>';}

    $k=0;
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, NULL AS thm_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdthm pg
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE pg.thmid='$thm_id'
          GROUP BY prd_id
          UNION
          SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, NULL AS thm_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM rel_thm
          INNER JOIN prdthm pg ON rel_thm1=thmid INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE rel_thm2='$thm_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, thm_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdthm pg
          INNER JOIN thm ON thmid=thm_id INNER JOIN prd p1 ON pg.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE thmid='$thm_id' AND coll_ov IS NULL
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, GROUP_CONCAT(DISTINCT thm_nm ORDER BY thm_ordr ASC SEPARATOR ' / ') AS thm_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM rel_thm
          INNER JOIN thm ON rel_thm1=thm_id INNER JOIN prdthm pg ON rel_thm1=thmid INNER JOIN prd p1 ON pg.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE rel_thm2='$thm_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        if($row['thm_nm'] && html($row['thm_nm'])!==$thm_nm) {$k++;}
        $prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'thm_nm'=>html($row['thm_nm']), 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql= "SELECT 1 FROM prdthm WHERE prdid='$prd_id' AND thmid='$thm_id' UNION SELECT 1 FROM rel_thm INNER JOIN prdthm ON rel_thm1=thmid WHERE prdid='$prd_id' AND rel_thm2='$thm_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this theme: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, thm_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM prdthm pg
            INNER JOIN thm ON thmid=thm_id INNER JOIN prd ON pg.prdid=prd_id
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE thmid='$thm_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            UNION
            SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, GROUP_CONCAT(DISTINCT thm_nm ORDER BY thm_ordr ASC SEPARATOR ' / ') AS thm_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM rel_thm
            INNER JOIN thm ON rel_thm1=thm_id INNER JOIN prdthm pg ON rel_thm1=thmid INNER JOIN prd ON pg.prdid=prd_id
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE rel_thm2='$thm_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        if(html($row['thm_nm'])!==$thm_nm) {$k++;}
        $sg_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'thm_nm'=>html($row['thm_nm']), 'wri_rls'=>array());
      }

      if(!empty($sg_prd_ids))
      {
        foreach($sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }
    }

    $thm_id=html($thm_id);
    include 'prod-theme.html.php';
  }
?>