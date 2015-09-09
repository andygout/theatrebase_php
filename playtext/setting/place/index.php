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

    $sql= "SELECT 1 FROM prdsttng_plc WHERE sttng_plcid='$plc_id'
          UNION
          SELECT 1 FROM rel_plc INNER JOIN prdsttng_plc ON rel_plc1=sttng_plcid WHERE rel_plc2='$plc_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of place as setting (place) for production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/production/setting/place/'.$plc_url.'">Productions</a> with '.$plc_nm.' as setting';} else {$lnk=NULL;}

    $sql= "SELECT plc_nm, plc_url
          FROM rel_plc
          INNER JOIN plc ON rel_plc2=plc_id
          WHERE rel_plc1='$plc_id' AND (EXISTS(SELECT 1 FROM ptsttng_plc WHERE sttng_plcid='$plc_id') OR EXISTS(SELECT 1 FROM rel_plc INNER JOIN ptsttng_plc ON rel_plc1=sttng_plcid WHERE rel_plc2='$plc_id'))
          ORDER BY rel_plc_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related place (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_plcs2[]='<a href="/playtext/setting/place/'.html($row['plc_url']).'">'.html(ucfirst($row['plc_nm'])).'</a>';}

    $sql= "SELECT plc_nm, plc_url
          FROM rel_plc
          INNER JOIN ptsttng_plc ON rel_plc1=sttng_plcid INNER JOIN plc ON sttng_plcid=plc_id
          WHERE rel_plc2='$plc_id'
          UNION
          SELECT plc_nm, plc_url
          FROM rel_plc rp1
          INNER JOIN ptsttng_plc ON rp1.rel_plc1=sttng_plcid INNER JOIN rel_plc rp2 ON sttng_plcid=rp2.rel_plc1 INNER JOIN plc ON rp2.rel_plc2=plc_id
          WHERE rp1.rel_plc2='$plc_id' AND plc_id!=rp1.rel_plc2 AND plc_id IN(SELECT rel_plc1 FROM rel_plc WHERE rel_plc2='$plc_id')
          AND rp1.plc_typ_rel=rp2.plc_typ_rel
          ORDER BY plc_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related place (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_plcs1[]='<a href="/playtext/setting/place/'.html($row['plc_url']).'">'.html(ucfirst($row['plc_nm'])).'</a>';}

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptsttng_plc psp
          INNER JOIN pt p1 ON psp.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE psp.sttng_plcid='$plc_id'
          GROUP BY pt_id
          UNION
          SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM rel_plc
          INNER JOIN ptsttng_plc psp ON rel_plc1=sttng_plcid INNER JOIN pt p1 ON psp.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE rel_plc2='$plc_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptsttng_plc psp
          INNER JOIN pt p1 ON psp.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE sttng_plcid='$plc_id' AND coll_ov IS NULL
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM rel_plc
          INNER JOIN ptsttng_plc psp ON rel_plc1=sttng_plcid INNER JOIN pt p1 ON psp.ptid=pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE rel_plc2='$plc_id' AND coll_ov IS NULL
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
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'plcs'=>array(), 'sg_pts'=>array(), 'wrks_sg_pts'=>array());
      }

      if(!empty($pt_ids))
      {
        foreach($pt_ids as $pt_id)
        {
          $sql= "SELECT 1 FROM ptsttng_plc WHERE ptid='$pt_id' AND sttng_plcid='$plc_id'
                UNION
                SELECT 1 FROM rel_plc INNER JOIN ptsttng_plc ON rel_plc1=sttng_plcid WHERE ptid='$pt_id' AND rel_plc2='$plc_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this place: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

      $k=0;
      $sql= "SELECT pt_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2, sttng_plc_nt1, sttngid, sttng_plc_ordr
            FROM ptsttng_plc
            INNER JOIN plc ON sttng_plcid=plc_id INNER JOIN pt ON ptid=pt_id
            WHERE sttng_plcid='$plc_id' AND coll_ov IS NULL
            GROUP BY pt_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2
            UNION
            SELECT pt_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2, sttng_plc_nt1, sttngid, sttng_plc_ordr
            FROM rel_plc
            INNER JOIN plc ON rel_plc1=plc_id INNER JOIN ptsttng_plc ON plc_id=sttng_plcid INNER JOIN pt ON ptid=pt_id
            WHERE rel_plc2='$plc_id' AND coll_ov IS NULL
            GROUP BY pt_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2
            ORDER BY sttngid ASC, sttng_plc_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring place data for playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['sttng_plc_nt1']) {$sttng_plc_nt1=html($row['sttng_plc_nt1']).' ';} else {$sttng_plc_nt1='';}
        if($row['sttng_plc_nt2']) {if(!preg_match('/^(:|;|,|\.)/', $row['sttng_plc_nt2'])) {$sttng_plc_nt2=' '.html($row['sttng_plc_nt2']);} else {$sttng_plc_nt2=html($row['sttng_plc_nt2']);}}
        else {$sttng_plc_nt2='';}
        $sttng_plc=ucfirst($sttng_plc_nt1.html($row['plc_nm']).$sttng_plc_nt2);
        if($sttng_plc!==$plc_nm) {$k++;}
        $pts[$row['pt_id']]['plcs'][]=$sttng_plc;
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, coll_sbhdrid, coll_ordr
            FROM ptsttng_plc psp
            INNER JOIN pt ON psp.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE sttng_plcid='$plc_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            UNION
            SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, coll_sbhdrid, coll_ordr
            FROM rel_plc
            INNER JOIN ptsttng_plc psp ON rel_plc1=sttng_plcid INNER JOIN pt ON psp.ptid=pt_id
            LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE rel_plc2='$plc_id'AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array(), 'plcs'=>array());
        }

        if(!empty($sg_pt_ids)) {foreach($sg_pt_ids as $sg_pt_id) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';}}

        $sql= "SELECT coll_ov, pt_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2, sttng_plc_nt1, sttngid, sttng_plc_ordr
              FROM ptsttng_plc
              INNER JOIN plc ON sttng_plcid=plc_id INNER JOIN pt ON ptid=pt_id
              WHERE sttng_plcid='$plc_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, pt_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2
              UNION
              SELECT coll_ov, pt_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2, sttng_plc_nt1, sttngid, sttng_plc_ordr
              FROM rel_plc
              INNER JOIN plc ON rel_plc1=plc_id INNER JOIN ptsttng_plc ON plc_id=sttng_plcid INNER JOIN pt ON ptid=pt_id
              WHERE rel_plc2='$plc_id'AND coll_ov IS NOT NULL
              GROUP BY coll_ov, pt_id, sttng_plc_nt1, plc_nm, sttng_plc_nt2
              ORDER BY sttngid ASC, sttng_plc_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring place data for segment playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_plc_nt1']) {$sttng_plc_nt1=html($row['sttng_plc_nt1']).' ';} else {$sttng_plc_nt1='';}
          if($row['sttng_plc_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_plc_nt2'])) {$sttng_plc_nt2=html($row['sttng_plc_nt2']);} else {$sttng_plc_nt2=' '.html($row['sttng_plc_nt2']);}}
          else {$sttng_plc_nt2='';}
          $sttng_plc=ucfirst($sttng_plc_nt1.html($row['plc_nm']).$sttng_plc_nt2);
          if($sttng_plc!==$plc_nm) {$k++;}
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]['plcs'][]=$sttng_plc;
        }
      }
    }

    $plc_id=html($plc_id);
    include 'playtext-setting-place.html.php';
  }
?>