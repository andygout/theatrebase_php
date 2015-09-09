<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $prds=array();
  $sql= "SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
        FROM prd
        INNER JOIN thtr ON thtrid=thtr_id
        WHERE coll_ov IS NULL
        GROUP BY prd_id
        ORDER BY prd_frst_dt DESC, prd_tr DESC, prd_alph ASC";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error acquiring production details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  if(mysqli_num_rows($result)>0)
  {
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $prd_ids[]=$row['prd_id'];
      $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'sg_prds'=>array());
    }

    if(!empty($prd_ids))
    {
      foreach($prd_ids as $prd_id)
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv_tr.inc.php';
      }
    }

    $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
          FROM prd
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE coll_ov IS NOT NULL
          GROUP BY coll_ov, prd_id
          ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production data for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $sg_prd_ids[]=$row['prd_id'];
      $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
    }

    if(!empty($sg_prd_ids))
    {
      foreach($sg_prd_ids as $sg_prd_id)
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv_tr.inc.php';
      }
    }
    $all_prds=$prds;
  }

  $prds=array();
  $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
        WHERE p1.prd_tr!=3 AND p1.prd_dts_info!=4
        AND ((p1.prd_prss_dt IS NOT NULL AND p1.prd_frst_dt!=p1.prd_prss_dt) OR (p1.prd_prv_only=1))
        AND p1.prd_frst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND p1.prd_frst_dt >= CURDATE()
        GROUP BY prd_id
        UNION
        SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN thtr ON thtrid=thtr_id
        WHERE prd_tr!=3 AND prd_dts_info!=4 AND ((prd_prss_dt IS NOT NULL AND prd_frst_dt!=prd_prss_dt) OR (prd_prv_only=1))
        AND prd_frst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_frst_dt >= CURDATE() AND coll_ov IS NULL
        GROUP BY prd_id
        ORDER BY prd_frst_dt DESC, prd_alph ASC";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error acquiring production details (previewing next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  if(mysqli_num_rows($result)>0)
  {
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $prvnxtwk_prd_ids[]=$row['prd_id'];
      $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
    }

    if(!empty($prvnxtwk_prd_ids))
    {
      foreach($prvnxtwk_prd_ids as $prd_id)
      {
        $sql= "SELECT 1 FROM prd WHERE prd_id='$prd_id'
              AND prd_tr!=3 AND prd_dts_info!=4 AND ((prd_prss_dt IS NOT NULL AND prd_frst_dt!=prd_prss_dt) OR (prd_prv_only=1))
              AND prd_frst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_frst_dt >= CURDATE()";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for prd_ids of productions (previewing next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
        }
      }
    }

    $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
          FROM prd
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE prd_tr!=3 AND prd_dts_info!=4 AND ((prd_prss_dt IS NOT NULL AND prd_frst_dt!=prd_prss_dt) OR (prd_prv_only=1))
          AND prd_frst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_frst_dt >= CURDATE() AND coll_ov IS NOT NULL
          GROUP BY coll_ov, prd_id
          ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring segment production data for productions (previewing next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $prvnxtwk_sg_prd_ids[]=$row['prd_id'];
      $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
    }

    if(!empty($prvnxtwk_sg_prd_ids))
    {
      foreach($prvnxtwk_sg_prd_ids as $sg_prd_id)
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
      }
    }
    $prvnxtwk_prd_ids=$prds;
  }

  $prds=array();
  $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
        WHERE p1.prd_tr!=3 AND p1.prd_dts_info!=4 AND p1.prd_frst_dt <= CURDATE()
        AND ((p1.prd_prss_dt IS NOT NULL AND p1.prd_frst_dt!=p1.prd_prss_dt AND p1.prd_prss_dt>CURDATE()) OR (p1.prd_prv_only=1 AND p1.prd_lst_dt >= CURDATE()))
        GROUP BY prd_id
        UNION
        SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN thtr ON thtrid=thtr_id
        WHERE prd_tr!=3 AND prd_dts_info!=4 AND prd_frst_dt <= CURDATE()
        AND ((prd_prss_dt IS NOT NULL AND prd_frst_dt!=prd_prss_dt AND prd_prss_dt>CURDATE()) OR (prd_prv_only=1 AND prd_lst_dt >= CURDATE())) AND coll_ov IS NULL
        GROUP BY prd_id
        ORDER BY prd_frst_dt DESC, prd_alph ASC";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error acquiring production details (currently previewing): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  if(mysqli_num_rows($result)>0)
  {
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $crrntprv_prd_ids[]=$row['prd_id'];
      $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
    }

    if(!empty($crrntprv_prd_ids))
    {
      foreach($crrntprv_prd_ids as $prd_id)
      {
        $sql= "SELECT 1 FROM prd WHERE prd_id='$prd_id'
              AND prd_tr!=3 AND prd_dts_info!=4 AND prd_frst_dt <= CURDATE()
              AND ((prd_prss_dt IS NOT NULL AND prd_frst_dt!=prd_prss_dt AND prd_prss_dt>CURDATE()) OR (prd_prv_only=1 AND prd_lst_dt >= CURDATE()))";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for prd_ids of productions (currently previewing): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
        }
      }
    }

    $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
          FROM prd
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE prd_tr!=3 AND prd_dts_info!=4 AND prd_frst_dt <= CURDATE()
          AND ((prd_prss_dt IS NOT NULL AND prd_frst_dt!=prd_prss_dt AND prd_prss_dt>CURDATE()) OR (prd_prv_only=1 AND prd_lst_dt >= CURDATE())) AND coll_ov IS NOT NULL
          GROUP BY coll_ov, prd_id
          ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring segment production data for productions (currently previewing): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $crrntprv_sg_prd_ids[]=$row['prd_id'];
      $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
    }

    if(!empty($crrntprv_sg_prd_ids))
    {
      foreach($crrntprv_sg_prd_ids as $sg_prd_id)
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
      }
    }
    $crrntprv_prds=$prds;
  }

  $prds=array();
  $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
        WHERE p1.prd_tr!=3 AND p1.prd_dts_info!=4 AND ((p1.prd_prss_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND p1.prd_prss_dt >= CURDATE())
        OR (p1.prd_prss_dt IS NULL AND p1.prd_prv_only=0 AND p1.prd_frst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND p1.prd_frst_dt >= CURDATE()))
        GROUP BY prd_id
        UNION
        SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN thtr ON thtrid=thtr_id
        WHERE prd_tr!=3 AND prd_dts_info!=4 AND ((prd_prss_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_prss_dt >= CURDATE())
        OR (prd_prss_dt IS NULL AND prd_prv_only=0 AND prd_frst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_frst_dt >= CURDATE())) AND coll_ov IS NULL
        GROUP BY prd_id
        ORDER BY prd_frst_dt DESC, prd_alph ASC";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error acquiring production details (opening next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  if(mysqli_num_rows($result)>0)
  {
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $opnnxtwk_prd_ids[]=$row['prd_id'];
      $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
    }

    if(!empty($opnnxtwk_prd_ids))
    {
      foreach($opnnxtwk_prd_ids as $prd_id)
      {
        $sql= "SELECT 1 FROM prd WHERE prd_id='$prd_id'
              AND prd_tr!=3 AND prd_dts_info!=4 AND ((prd_prss_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_prss_dt >= CURDATE())
              OR (prd_prss_dt IS NULL AND prd_prv_only=0 AND prd_frst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_frst_dt >= CURDATE()))";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for prd_ids of productions (opening next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
        }
      }
    }

    $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
          FROM prd
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE prd_tr!=3 AND prd_dts_info!=4 AND ((prd_prss_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_prss_dt >= CURDATE())
          OR (prd_prss_dt IS NULL AND prd_prv_only=0 AND prd_frst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_frst_dt >= CURDATE())) AND coll_ov IS NOT NULL
          GROUP BY coll_ov, prd_id
          ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring segment production data for productions (opening next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $opnnxtwk_sg_prd_ids[]=$row['prd_id'];
      $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
    }

    if(!empty($opnnxtwk_sg_prd_ids))
    {
      foreach($opnnxtwk_sg_prd_ids as $sg_prd_id)
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
      }
    }
    $opnnxtwk_prds=$prds;
  }

  $prds=array();
  $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
        WHERE p1.prd_tr!=3 AND p1.prd_dts_info NOT IN(3,4) AND p1.prd_prv_only=0
        AND ((p1.prd_prss_dt <= CURDATE() AND p1.prd_lst_dt >= CURDATE()) OR (p1.prd_prss_dt IS NULL AND p1.prd_frst_dt <= CURDATE() AND p1.prd_lst_dt >= CURDATE()))
        GROUP BY prd_id
        UNION
        SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN thtr ON thtrid=thtr_id
        WHERE prd_tr!=3 AND prd_dts_info NOT IN(3,4) AND prd_prv_only=0
        AND ((prd_prss_dt <= CURDATE() AND prd_lst_dt >= CURDATE()) OR (prd_prss_dt IS NULL AND prd_frst_dt <= CURDATE() AND prd_lst_dt >= CURDATE())) AND coll_ov IS NULL
        GROUP BY prd_id
        ORDER BY prd_frst_dt DESC, prd_alph ASC";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error acquiring production details (currently playing): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  if(mysqli_num_rows($result)>0)
  {
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $crrnt_prd_ids[]=$row['prd_id'];
      $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
    }

    if(!empty($crrnt_prd_ids))
    {
      foreach($crrnt_prd_ids as $prd_id)
      {
        $sql= "SELECT 1 FROM prd WHERE prd_id='$prd_id'
              AND prd_tr!=3 AND prd_dts_info NOT IN(3,4) AND prd_prv_only=0
              AND ((prd_prss_dt <= CURDATE() AND prd_lst_dt >= CURDATE()) OR (prd_prss_dt IS NULL AND prd_frst_dt <= CURDATE() AND prd_lst_dt >= CURDATE()))";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for prd_ids of productions (currently playing): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
        }
      }
    }

    $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
          FROM prd
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE prd_tr!=3 AND prd_dts_info NOT IN(3,4) AND prd_prv_only=0
          AND ((prd_prss_dt <= CURDATE() AND prd_lst_dt >= CURDATE()) OR (prd_prss_dt IS NULL AND prd_frst_dt <= CURDATE() AND prd_lst_dt >= CURDATE())) AND coll_ov IS NOT NULL
          GROUP BY coll_ov, prd_id
          ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring segment production data for productions (currently playing): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $crrnt_sg_prd_ids[]=$row['prd_id'];
      $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
    }

    if(!empty($crrnt_sg_prd_ids))
    {
      foreach($crrnt_sg_prd_ids as $sg_prd_id)
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
      }
    }
    $crrnt_prds=$prds;
  }

  $prds=array();
  $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
        WHERE p1.prd_tr!=3 AND p1.prd_dts_info NOT IN(2,3,4) AND p1.prd_prv_only=0 AND p1.prd_lst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND p1.prd_lst_dt >= CURDATE()
        GROUP BY prd_id
        UNION
        SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply,
        prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
        FROM prd p1
        INNER JOIN thtr ON thtrid=thtr_id
        WHERE prd_tr!=3 AND prd_dts_info NOT IN(2,3,4) AND prd_prv_only=0 AND prd_lst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_lst_dt >= CURDATE() AND coll_ov IS NULL
        GROUP BY prd_id
        ORDER BY prd_frst_dt DESC, prd_alph ASC";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error acquiring production details (closing next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  if(mysqli_num_rows($result)>0)
  {
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $clsnxtwk_prd_ids[]=$row['prd_id'];
      $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
    }

    if(!empty($clsnxtwk_prd_ids))
    {
      foreach($clsnxtwk_prd_ids as $prd_id)
      {
        $sql= "SELECT 1 FROM prd
              WHERE prd_id='$prd_id' AND prd_tr!=3 AND prd_dts_info NOT IN(2,3,4) AND prd_prv_only=0 AND prd_lst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_lst_dt >= CURDATE()";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for prd_ids of productions (closing next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
        }
      }
    }

    $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
          FROM prd
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE prd_tr!=3 AND prd_dts_info NOT IN(2,3,4) AND prd_prv_only=0 AND prd_lst_dt <= DATE_ADD(CURDATE(), INTERVAL 1 WEEK) AND prd_lst_dt >= CURDATE()
          AND coll_ov IS NOT NULL
          GROUP BY coll_ov, prd_id
          ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring segment production data for productions (closing next week): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
      $clsnxtwk_sg_prd_ids[]=$row['prd_id'];
      $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
    }

    if(!empty($clsnxtwk_sg_prd_ids))
    {
      foreach($clsnxtwk_sg_prd_ids as $sg_prd_id)
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
      }
    }
    $clsnxtwk_prds=$prds;
  }

  $sql= "SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, pt_coll
        FROM pt
        LEFT OUTER JOIN pttxt_vrsn ON pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
        WHERE coll_ov IS NULL
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
      $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'wri_rls'=>array(), 'sg_pts'=>array(), 'wrks_sg_pts'=>array());
    }

    if(!empty($pt_ids)) {foreach($pt_ids as $pt_id) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';}}

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
          LEFT OUTER JOIN pttxt_vrsn ON pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE coll_ov IS NOT NULL
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

    if(!empty($sg_pt_ids))
    {
      foreach($sg_pt_ids as $sg_pt_id)
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
      }
    }
  }

  include 'home.html.php';
?>