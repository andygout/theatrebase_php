<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/place/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $plc_url=cln($_GET['plc_url']);

  $sql="SELECT plc_id FROM plc WHERE plc_url='$plc_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $plc_id=$row['plc_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT plc_nm, plc_url FROM plc WHERE plc_id='$plc_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting (place) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab=html(ucfirst($row['plc_nm']));
    $pagetitle=html(ucfirst($row['plc_nm']));
    $plc_nm=html(ucfirst($row['plc_nm']));
    $plc_url=html($row['plc_url']);

    $sql= "SELECT 1 FROM ptsttng_plc WHERE sttng_plcid='$plc_id'
          UNION
          SELECT 1 FROM rel_plc INNER JOIN ptsttng_plc ON rel_plc1=sttng_plcid WHERE rel_plc2='$plc_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of place as setting (place) for playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/playtext/setting/place/'.$plc_url.'">Playtexts</a> with '.$plc_nm.' as setting';} else {$lnk=NULL;}

    $sql= "SELECT plc_nm, plc_url
          FROM rel_plc
          INNER JOIN plc ON rel_plc2=plc_id
          WHERE rel_plc1='$plc_id' AND (EXISTS(SELECT 1 FROM prdsttng_plc WHERE sttng_plcid='$plc_id') OR EXISTS(SELECT 1 FROM rel_plc INNER JOIN prdsttng_plc ON rel_plc1=sttng_plcid WHERE rel_plc2='$plc_id'))
          ORDER BY rel_plc_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related place (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_plcs2[]='<a href="/production/setting/place/'.html($row['plc_url']).'">'.html(ucfirst($row['plc_nm'])).'</a>';}

    $sql= "SELECT plc_nm, plc_url
          FROM rel_plc
          INNER JOIN prdsttng_plc ON rel_plc1=sttng_plcid INNER JOIN plc ON sttng_plcid=plc_id
          WHERE rel_plc2='$plc_id'
          UNION
          SELECT plc_nm, plc_url
          FROM rel_plc rp1
          INNER JOIN prdsttng_plc ON rp1.rel_plc1=sttng_plcid INNER JOIN rel_plc rp2 ON sttng_plcid=rp2.rel_plc1 INNER JOIN plc ON rp2.rel_plc2=plc_id
          WHERE rp1.rel_plc2='$plc_id' AND plc_id!=rp1.rel_plc2 AND plc_id IN(SELECT rel_plc1 FROM rel_plc WHERE rel_plc2='$plc_id')
          AND rp1.plc_typ_rel=rp2.plc_typ_rel
          ORDER BY plc_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related place (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_plcs1[]='<a href="/production/setting/place/'.html($row['plc_url']).'">'.html(ucfirst($row['plc_nm'])).'</a>';}

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdsttng_plc psp
          INNER JOIN prd p1 ON psp.prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE psp.sttng_plcid='$plc_id'
          GROUP BY prd_id
          UNION
          SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM rel_plc
          INNER JOIN prdsttng_plc psp ON rel_plc1=sttng_plcid INNER JOIN prd p1 ON psp.prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE rel_plc2='$plc_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdsttng_plc psp
          INNER JOIN prd p1 ON psp.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE sttng_plcid='$plc_id' AND coll_ov IS NULL
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM rel_plc
          INNER JOIN prdsttng_plc psp ON rel_plc1=sttng_plcid INNER JOIN prd p1 ON psp.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE rel_plc2='$plc_id' AND coll_ov IS NULL
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
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'plcs'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql= "SELECT 1 FROM prdsttng_plc WHERE prdid='$prd_id' AND sttng_plcid='$plc_id'
                UNION
                SELECT 1 FROM rel_plc INNER JOIN prdsttng_plc ON rel_plc1=sttng_plcid WHERE prdid='$prd_id' AND rel_plc2='$plc_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this place: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $k=0;
      $sql= "SELECT prd_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2, sttng_plc_nt1, sttngid, sttng_plc_ordr
            FROM prdsttng_plc
            INNER JOIN plc ON sttng_plcid=plc_id LEFT OUTER JOIN prd ON prdid=prd_id
            WHERE sttng_plcid='$plc_id' AND coll_ov IS NULL
            GROUP BY prd_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2
            UNION
            SELECT prdid, sttng_plc_nt1, plc_nm, sttng_plc_nt2, sttng_plc_nt1, sttngid, sttng_plc_ordr
            FROM rel_plc
            INNER JOIN plc ON rel_plc1=plc_id INNER JOIN prdsttng_plc ON plc_id=sttng_plcid LEFT OUTER JOIN prd ON prdid=prd_id
            WHERE rel_plc2='$plc_id' AND coll_ov IS NULL
            GROUP BY prd_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2
            ORDER BY sttngid ASC, sttng_plc_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring place data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['sttng_plc_nt1']) {$sttng_plc_nt1=html($row['sttng_plc_nt1']).' ';} else {$sttng_plc_nt1='';}
        if($row['sttng_plc_nt2']) {if(!preg_match('/^(:|;|,|\.)/', $row['sttng_plc_nt2'])) {$sttng_plc_nt2=' '.html($row['sttng_plc_nt2']);} else {$sttng_plc_nt2=html($row['sttng_plc_nt2']);}}
        else {$sttng_plc_nt2='';}
        $sttng_plc=ucfirst($sttng_plc_nt1.html($row['plc_nm']).$sttng_plc_nt2);
        if($sttng_plc!==$plc_nm) {$k++;}
        $prds[$row['prd_id']]['plcs'][]=$sttng_plc;
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM prdsttng_plc
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE sttng_plcid='$plc_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            UNION
            SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM rel_plc
            INNER JOIN prdsttng_plc ON rel_plc1=sttng_plcid INNER JOIN prd ON prdid=prd_id
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE rel_plc2='$plc_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'plcs'=>array());
        }

        if(!empty($sg_prd_ids))
        {
          foreach($sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2, sttng_plc_nt1, sttngid, sttng_plc_ordr
              FROM prdsttng_plc
              INNER JOIN plc ON sttng_plcid=plc_id INNER JOIN prd ON prdid=prd_id
              WHERE sttng_plcid='$plc_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2
              UNION
              SELECT coll_ov, prd_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2, sttng_plc_nt1, sttngid, sttng_plc_ordr
              FROM rel_plc
              INNER JOIN plc ON rel_plc1=plc_id INNER JOIN prdsttng_plc ON plc_id=sttng_plcid INNER JOIN prd ON prdid=prd_id
              WHERE rel_plc2='$plc_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2
              ORDER BY sttngid ASC, sttng_plc_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring place data for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_plc_nt1']) {$sttng_plc_nt1=html($row['sttng_plc_nt1']).' ';} else {$sttng_plc_nt1='';}
          if($row['sttng_plc_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_plc_nt2'])) {$sttng_plc_nt2=html($row['sttng_plc_nt2']);} else {$sttng_plc_nt2=' '.html($row['sttng_plc_nt2']);}}
          else {$sttng_plc_nt2='';}
          $sttng_plc=ucfirst($sttng_plc_nt1.html($row['plc_nm']).$sttng_plc_nt2);
          if($sttng_plc!==$plc_nm) {$k++;}
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['plcs'][]=$sttng_plc;
        }
      }
    }

    $plc_id=html($plc_id);
    include 'prod-setting-place.html.php';
  }
?>