<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/time/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $tm_url=cln($_GET['tm_url']);

  $sql="SELECT tm_id FROM tm WHERE tm_url='$tm_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $tm_id=$row['tm_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT tm_nm, tm_url FROM tm WHERE tm_id='$tm_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting (time) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['tm_nm']);
    $tm_nm=html($row['tm_nm']);
    $tm_url=html($row['tm_url']);

    $sql= "SELECT 1 FROM ptsttng_tm WHERE sttng_tmid='$tm_id'
          UNION
          SELECT 1 FROM rel_tm INNER JOIN ptsttng_tm ON rel_tm1=sttng_tmid WHERE rel_tm2='$tm_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of time as setting (time) for playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/playtext/setting/time/'.$tm_url.'">Playtexts</a> with '.$tm_nm.' as setting';} else {$lnk=NULL;}

    $sql= "SELECT tm_nm, tm_url
          FROM rel_tm
          INNER JOIN tm ON rel_tm2=tm_id
          WHERE rel_tm1='$tm_id' AND (EXISTS(SELECT 1 FROM prdsttng_tm WHERE sttng_tmid='$tm_id') OR EXISTS(SELECT 1 FROM rel_tm INNER JOIN prdsttng_tm ON rel_tm1=sttng_tmid WHERE rel_tm2='$tm_id'))
          ORDER BY rel_tm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms2[]='<a href="/production/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}

    $sql= "SELECT tm_nm, tm_url, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm
          INNER JOIN prdsttng_tm ON rel_tm1=sttng_tmid INNER JOIN tm ON sttng_tmid=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NULL
          UNION
          SELECT tm_nm, tm_url, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm rt1
          INNER JOIN prdsttng_tm ON rt1.rel_tm1=sttng_tmid INNER JOIN rel_tm rt2 ON sttng_tmid=rt2.rel_tm1 INNER JOIN tm ON rt2.rel_tm2=tm_id
          WHERE rt1.rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NULL AND tm_id!=rt1.rel_tm2
          AND tm_rcr=CASE WHEN(SELECT 1 FROM tm WHERE tm_id='$tm_id' AND tm_rcr=1) IS NULL THEN '0' ELSE '1' END
          AND tm_id IN(SELECT rel_tm1 FROM rel_tm WHERE rel_tm2='$tm_id')
          ORDER BY tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (BCE with no date): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms1[]='<a href="/production/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}

    $sql= "SELECT tm_nm, tm_url, DATE_FORMAT(tm_frm_dt, '%Y') AS tm_frm_dt_YYYY, DATE_FORMAT(tm_frm_dt, '%m%d') AS tm_frm_dt_MMDD, DATE_FORMAT(tm_to_dt, '%Y') AS tm_to_dt_YYYY, DATE_FORMAT(tm_to_dt, '%m%d') AS tm_to_dt_MMDD, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm
          INNER JOIN prdsttng_tm ON rel_tm1=sttng_tmid INNER JOIN tm ON sttng_tmid=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NOT NULL
          UNION
          SELECT tm_nm, tm_url, DATE_FORMAT(tm_frm_dt, '%Y') AS tm_frm_dt_YYYY, DATE_FORMAT(tm_frm_dt, '%m%d') AS tm_frm_dt_MMDD, DATE_FORMAT(tm_to_dt, '%Y') AS tm_to_dt_YYYY, DATE_FORMAT(tm_to_dt, '%m%d') AS tm_to_dt_MMDD, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm rt1
          INNER JOIN prdsttng_tm ON rt1.rel_tm1=sttng_tmid INNER JOIN rel_tm rt2 ON sttng_tmid=rt2.rel_tm1 INNER JOIN tm ON rt2.rel_tm2=tm_id
          WHERE rt1.rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NOT NULL AND tm_id!=rt1.rel_tm2
          AND tm_rcr=CASE WHEN(SELECT 1 FROM tm WHERE tm_id='$tm_id' AND tm_rcr=1) IS NULL THEN '0' ELSE '1' END
          AND tm_id IN(SELECT rel_tm1 FROM rel_tm WHERE rel_tm2='$tm_id')
          ORDER BY tm_frm_dt_YYYY DESC, tm_frm_dt_MMDD ASC, tm_to_dt_YYYY ASC, tm_to_dt_MMDD DESC, tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (BCE with date): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms1[]='<a href="/production/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}

    $sql= "SELECT tm_nm, tm_url, tm_frm_dt, tm_to_dt, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm
          INNER JOIN prdsttng_tm ON rel_tm1=sttng_tmid INNER JOIN tm ON sttng_tmid=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce!=1
          UNION
          SELECT tm_nm, tm_url, tm_frm_dt, tm_to_dt, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm rt1
          INNER JOIN prdsttng_tm ON rt1.rel_tm1=sttng_tmid INNER JOIN rel_tm rt2 ON sttng_tmid=rt2.rel_tm1 INNER JOIN tm ON rt2.rel_tm2=tm_id
          WHERE rt1.rel_tm2='$tm_id' AND tm_frm_dt_bce!=1 AND tm_id!=rt1.rel_tm2
          AND tm_rcr=CASE WHEN(SELECT 1 FROM tm WHERE tm_id='$tm_id' AND tm_rcr=1) IS NULL THEN '0' ELSE '1' END
          AND tm_id IN(SELECT rel_tm1 FROM rel_tm WHERE rel_tm2='$tm_id')
          ORDER BY tm_frm_dt ASC, tm_to_dt DESC, tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (all CE): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms1[]='<a href="/production/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdsttng_tm pst
          INNER JOIN prd p1 ON pst.prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE pst.sttng_tmid='$tm_id'
          GROUP BY prd_id
          UNION
          SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM rel_tm
          INNER JOIN prdsttng_tm pst ON rel_tm1=sttng_tmid INNER JOIN prd p1 ON pst.prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE rel_tm2='$tm_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdsttng_tm pst
          INNER JOIN prd p1 ON pst.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE sttng_tmid='$tm_id' AND coll_ov IS NULL
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM rel_tm
          INNER JOIN prdsttng_tm pst ON rel_tm1=sttng_tmid INNER JOIN prd p1 ON pst.prdid=prd_id
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE rel_tm2='$tm_id' AND coll_ov IS NULL
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
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'tms'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql= "SELECT 1 FROM prdsttng_tm WHERE prdid='$prd_id' AND sttng_tmid='$tm_id'
                UNION
                SELECT 1 FROM rel_tm INNER JOIN prdsttng_tm ON rel_tm1=sttng_tmid WHERE prdid='$prd_id' AND rel_tm2='$tm_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this time: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $k=0;
      $sql= "SELECT prd_id, NULL AS sttng_tm_nt1, CONCAT((SELECT tm_nm FROM prdsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MIN(pst2.sttng_tm_ordr) AND prdid=pst1.prdid AND sttngid=pst1.sttngid), ' to ', (SELECT tm_nm FROM prdsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MAX(pst2.sttng_tm_ordr) AND prdid=pst1.prdid AND sttngid=pst1.sttngid)) AS tm_nm, NULL AS sttng_tm_nt2, pst1.sttngid, pst1.sttng_tm_ordr
            FROM prdsttng_tm pst1
            INNER JOIN tm t1 ON pst1.sttng_tmid=t1.tm_id INNER JOIN prdsttng ps ON pst1.prdid=ps.prdid AND pst1.sttngid=sttng_id
            INNER JOIN prdsttng_tm pst2 ON ps.prdid=pst2.prdid AND sttng_id=pst2.sttngid LEFT OUTER JOIN prd ON pst1.prdid=prd_id
            WHERE pst1.sttng_tmid='$tm_id' AND coll_ov IS NULL
            GROUP BY prd_id
            UNION
            SELECT prd_id, NULL AS sttng_tm_nt1, CONCAT((SELECT tm_nm FROM prdsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MIN(pst2.sttng_tm_ordr) AND prdid=pst1.prdid AND sttngid=pst1.sttngid), ' to ', (SELECT tm_nm FROM prdsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MAX(pst2.sttng_tm_ordr) AND prdid=pst1.prdid AND sttngid=pst1.sttngid)) AS tm_nm, NULL AS sttng_tm_nt2, pst1.sttngid, pst1.sttng_tm_ordr
            FROM rel_tm
            INNER JOIN tm t1 ON rel_tm1=t1.tm_id INNER JOIN prdsttng_tm pst1 ON t1.tm_id=pst1.sttng_tmid
            INNER JOIN prdsttng ps ON pst1.prdid=ps.prdid AND pst1.sttngid=sttng_id INNER JOIN prdsttng_tm pst2 ON ps.prdid=pst2.prdid AND sttng_id=pst2.sttngid
            LEFT OUTER JOIN prd ON pst1.prdid=prd_id
            WHERE rel_tm2='$tm_id' AND coll_ov IS NULL
            GROUP BY prd_id
            UNION
            SELECT prd_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2, sttngid, sttng_tm_ordr
            FROM prdsttng_tm pst
            INNER JOIN tm ON sttng_tmid=tm_id LEFT OUTER JOIN prdsttng ps ON pst.prdid=ps.prdid AND sttngid=sttng_id LEFT OUTER JOIN prd ON pst.prdid=prd_id
            WHERE sttng_tmid='$tm_id' AND sttng_id IS NULL AND coll_ov IS NULL
            GROUP BY prd_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2
            UNION
            SELECT prd_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2, sttngid, sttng_tm_ordr
            FROM rel_tm
            INNER JOIN tm ON rel_tm1=tm_id INNER JOIN prdsttng_tm pst ON tm_id=sttng_tmid
            LEFT OUTER JOIN prdsttng ps ON pst.prdid=ps.prdid AND sttngid=sttng_id LEFT OUTER JOIN prd ON pst.prdid=prd_id
            WHERE rel_tm2='$tm_id' AND sttng_id IS NULL AND coll_ov IS NULL
            GROUP BY prd_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2
            ORDER BY sttngid ASC, sttng_tm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring time data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['sttng_tm_nt1']) {if(!preg_match('/(^c\.| c\.)$/', $row['sttng_tm_nt1'])) {$sttng_tm_nt1=html($row['sttng_tm_nt1']).' ';} else {$sttng_tm_nt1=html($row['sttng_tm_nt1']);}}
        else {$sttng_tm_nt1='';}
        if($row['sttng_tm_nt2']) {if(!preg_match('/^(:|;|,|\.)/', $row['sttng_tm_nt2'])) {$sttng_tm_nt2=' '.html($row['sttng_tm_nt2']);} else {$sttng_tm_nt2=html($row['sttng_tm_nt2']);}}
        else {$sttng_tm_nt2='';}
        $sttng_tm=$sttng_tm_nt1.html($row['tm_nm']).$sttng_tm_nt2;
        if($sttng_tm!==$tm_nm) {$k++;}
        $prds[$row['prd_id']]['tms'][]=$sttng_tm;
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM prdsttng_tm
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE sttng_tmid='$tm_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            UNION
            SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM rel_tm
            INNER JOIN prdsttng_tm ON rel_tm1=sttng_tmid INNER JOIN prd ON prdid=prd_id
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE rel_tm2='$tm_id' AND coll_ov IS NOT NULL
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
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'tms'=>array());
        }

        if(!empty($sg_prd_ids))
        {
          foreach($sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, NULL AS sttng_tm_nt1, CONCAT((SELECT tm_nm FROM prdsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MIN(pst2.sttng_tm_ordr) AND prdid=pst1.prdid AND sttngid=pst1.sttngid), ' to ', (SELECT tm_nm FROM prdsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MAX(pst2.sttng_tm_ordr) AND prdid=pst1.prdid AND sttngid=pst1.sttngid)) AS tm_nm, NULL AS sttng_tm_nt2, pst1.sttngid, pst1.sttng_tm_ordr
              FROM prdsttng_tm pst1
              INNER JOIN tm t1 ON pst1.sttng_tmid=t1.tm_id INNER JOIN prdsttng ps ON pst1.prdid=ps.prdid AND pst1.sttngid=sttng_id
              INNER JOIN prdsttng_tm pst2 ON ps.prdid=pst2.prdid AND sttng_id=pst2.sttngid INNER JOIN prd ON pst1.prdid=prd_id
              WHERE pst1.sttng_tmid='$tm_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id
              UNION
              SELECT coll_ov, prd_id, NULL AS sttng_tm_nt1, CONCAT((SELECT tm_nm FROM prdsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MIN(pst2.sttng_tm_ordr) AND prdid=pst1.prdid AND sttngid=pst1.sttngid), ' to ', (SELECT tm_nm FROM prdsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MAX(pst2.sttng_tm_ordr) AND prdid=pst1.prdid AND sttngid=pst1.sttngid)) AS tm_nm, NULL AS sttng_tm_nt2, pst1.sttngid, pst1.sttng_tm_ordr
              FROM rel_tm
              INNER JOIN tm t1 ON rel_tm1=t1.tm_id INNER JOIN prdsttng_tm pst1 ON t1.tm_id=pst1.sttng_tmid
              INNER JOIN prdsttng ps ON pst1.prdid=ps.prdid AND pst1.sttngid=sttng_id INNER JOIN prdsttng_tm pst2 ON ps.prdid=pst2.prdid AND sttng_id=pst2.sttngid
              INNER JOIN prd ON pst1.prdid=prd_id
              WHERE rel_tm2='$tm_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id
              UNION
              SELECT coll_ov, prd_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2, sttngid, sttng_tm_ordr
              FROM prdsttng_tm pst
              INNER JOIN tm ON sttng_tmid=tm_id LEFT OUTER JOIN prdsttng ps ON pst.prdid=ps.prdid AND sttngid=sttng_id
              INNER JOIN prd ON pst.prdid=prd_id
              WHERE sttng_tmid='$tm_id' AND sttng_id IS NULL AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2
              UNION
              SELECT coll_ov, prd_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2, sttngid, sttng_tm_ordr
              FROM rel_tm
              INNER JOIN tm ON rel_tm1=tm_id INNER JOIN prdsttng_tm pst ON tm_id=sttng_tmid
              LEFT OUTER JOIN prdsttng ps ON pst.prdid=ps.prdid AND sttngid=sttng_id INNER JOIN prd ON pst.prdid=prd_id
              WHERE rel_tm2='$tm_id' AND sttng_id IS NULL AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2
              ORDER BY sttngid ASC, sttng_tm_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring time data for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_tm_nt1']) {$sttng_tm_nt1=html($row['sttng_tm_nt1']).' ';} else {$sttng_tm_nt1='';}
          if($row['sttng_tm_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_tm_nt2'])) {$sttng_tm_nt2=html($row['sttng_tm_nt2']);} else {$sttng_tm_nt2=' '.html($row['sttng_tm_nt2']);}}
          else {$sttng_tm_nt2='';}
          $sttng_tm=$sttng_tm_nt1.html($row['tm_nm']).$sttng_tm_nt2;
          if($sttng_tm!==$tm_nm) {$k++;}
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['tms'][]=$sttng_tm;
        }
      }
    }

    $tm_id=html($tm_id);
    include 'prod-setting-time.html.php';
  }
?>