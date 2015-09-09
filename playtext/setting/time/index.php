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

    $sql= "SELECT 1 FROM prdsttng_tm WHERE sttng_tmid='$tm_id'
          UNION
          SELECT 1 FROM rel_tm INNER JOIN prdsttng_tm ON rel_tm1=sttng_tmid WHERE rel_tm2='$tm_id'
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of time as setting (time) for production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$lnk='<a href="/production/setting/time/'.$tm_url.'">Productions</a> with '.$tm_nm.' as setting';} else {$lnk=NULL;}

    $sql= "SELECT tm_nm, tm_url
          FROM rel_tm
          INNER JOIN tm ON rel_tm2=tm_id
          WHERE rel_tm1='$tm_id' AND (EXISTS(SELECT 1 FROM ptsttng_tm WHERE sttng_tmid='$tm_id') OR EXISTS(SELECT 1 FROM rel_tm INNER JOIN ptsttng_tm ON rel_tm1=sttng_tmid WHERE rel_tm2='$tm_id'))
          ORDER BY rel_tm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms2[]='<a href="/playtext/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}

    $sql= "SELECT tm_nm, tm_url, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm
          INNER JOIN ptsttng_tm ON rel_tm1=sttng_tmid INNER JOIN tm ON sttng_tmid=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NULL
          UNION
          SELECT tm_nm, tm_url, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm rt1
          INNER JOIN ptsttng_tm ON rt1.rel_tm1=sttng_tmid INNER JOIN rel_tm rt2 ON sttng_tmid=rt2.rel_tm1 INNER JOIN tm ON rt2.rel_tm2=tm_id
          WHERE rt1.rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NULL AND tm_id!=rt1.rel_tm2
          AND tm_rcr=CASE WHEN(SELECT 1 FROM tm WHERE tm_id='$tm_id' AND tm_rcr=1) IS NULL THEN '0' ELSE '1' END
          AND tm_id IN(SELECT rel_tm1 FROM rel_tm WHERE rel_tm2='$tm_id')
          ORDER BY tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (BCE with no date): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms1[]='<a href="/playtext/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}

    $sql= "SELECT tm_nm, tm_url, DATE_FORMAT(tm_frm_dt, '%Y') AS tm_frm_dt_YYYY, DATE_FORMAT(tm_frm_dt, '%m%d') AS tm_frm_dt_MMDD, DATE_FORMAT(tm_to_dt, '%Y') AS tm_to_dt_YYYY, DATE_FORMAT(tm_to_dt, '%m%d') AS tm_to_dt_MMDD, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm
          INNER JOIN ptsttng_tm ON rel_tm1=sttng_tmid INNER JOIN tm ON sttng_tmid=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NOT NULL
          UNION
          SELECT tm_nm, tm_url, DATE_FORMAT(tm_frm_dt, '%Y') AS tm_frm_dt_YYYY, DATE_FORMAT(tm_frm_dt, '%m%d') AS tm_frm_dt_MMDD, DATE_FORMAT(tm_to_dt, '%Y') AS tm_to_dt_YYYY, DATE_FORMAT(tm_to_dt, '%m%d') AS tm_to_dt_MMDD, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm rt1
          INNER JOIN ptsttng_tm ON rt1.rel_tm1=sttng_tmid INNER JOIN rel_tm rt2 ON sttng_tmid=rt2.rel_tm1 INNER JOIN tm ON rt2.rel_tm2=tm_id
          WHERE rt1.rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NOT NULL AND tm_id!=rt1.rel_tm2
          AND tm_rcr=CASE WHEN(SELECT 1 FROM tm WHERE tm_id='$tm_id' AND tm_rcr=1) IS NULL THEN '0' ELSE '1' END
          AND tm_id IN(SELECT rel_tm1 FROM rel_tm WHERE rel_tm2='$tm_id')
          ORDER BY tm_frm_dt_YYYY DESC, tm_frm_dt_MMDD ASC, tm_to_dt_YYYY ASC, tm_to_dt_MMDD DESC, tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (BCE with date): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms1[]='<a href="/playtext/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}

    $sql= "SELECT tm_nm, tm_url, tm_frm_dt, tm_to_dt, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm
          INNER JOIN ptsttng_tm ON rel_tm1=sttng_tmid INNER JOIN tm ON sttng_tmid=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce!=1
          UNION
          SELECT tm_nm, tm_url, tm_frm_dt, tm_to_dt, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm rt1
          INNER JOIN ptsttng_tm ON rt1.rel_tm1=sttng_tmid INNER JOIN rel_tm rt2 ON sttng_tmid=rt2.rel_tm1 INNER JOIN tm ON rt2.rel_tm2=tm_id
          WHERE rt1.rel_tm2='$tm_id' AND tm_frm_dt_bce!=1 AND tm_id!=rt1.rel_tm2
          AND tm_rcr=CASE WHEN(SELECT 1 FROM tm WHERE tm_id='$tm_id' AND tm_rcr=1) IS NULL THEN '0' ELSE '1' END
          AND tm_id IN(SELECT rel_tm1 FROM rel_tm WHERE rel_tm2='$tm_id')
          ORDER BY tm_frm_dt ASC, tm_to_dt DESC, tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (all CE): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms1[]='<a href="/playtext/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptsttng_tm pst
          INNER JOIN pt p1 ON pst.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE pst.sttng_tmid='$tm_id'
          GROUP BY pt_id
          UNION
          SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM rel_tm
          INNER JOIN ptsttng_tm pst ON rel_tm1=sttng_tmid INNER JOIN pt p1 ON pst.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE rel_tm2='$tm_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptsttng_tm pst
          INNER JOIN pt p1 ON pst.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE sttng_tmid='$tm_id' AND coll_ov IS NULL
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM rel_tm
          INNER JOIN ptsttng_tm pst ON rel_tm1=sttng_tmid INNER JOIN pt p1 ON pst.ptid=pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE rel_tm2='$tm_id' AND coll_ov IS NULL
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
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'tms'=>array(), 'sg_pts'=>array(), 'wrks_sg_pts'=>array());
      }

      if(!empty($pt_ids))
      {
        foreach($pt_ids as $pt_id)
        {
          $sql= "SELECT 1 FROM ptsttng_tm WHERE ptid='$pt_id' AND sttng_tmid='$tm_id'
                UNION
                SELECT 1 FROM rel_tm INNER JOIN ptsttng_tm ON rel_tm1=sttng_tmid WHERE ptid='$pt_id' AND rel_tm2='$tm_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this time: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
      $sql= "SELECT pt_id, NULL AS sttng_tm_nt1, CONCAT((SELECT tm_nm FROM ptsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MIN(pst2.sttng_tm_ordr) AND ptid=pst1.ptid AND sttngid=pst1.sttngid), ' to ', (SELECT tm_nm FROM ptsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MAX(pst2.sttng_tm_ordr) AND ptid=pst1.ptid AND sttngid=pst1.sttngid)) AS tm_nm, NULL AS sttng_tm_nt2, pst1.sttngid, pst1.sttng_tm_ordr
            FROM ptsttng_tm pst1
            INNER JOIN tm t1 ON pst1.sttng_tmid=t1.tm_id INNER JOIN ptsttng ps ON pst1.ptid=ps.ptid AND pst1.sttngid=sttng_id
            INNER JOIN ptsttng_tm pst2 ON ps.ptid=pst2.ptid AND sttng_id=pst2.sttngid INNER JOIN pt ON pst1.ptid=pt_id
            WHERE pst1.sttng_tmid='$tm_id' AND coll_ov IS NULL
            GROUP BY pt_id
            UNION
            SELECT pt_id, NULL AS sttng_tm_nt1, CONCAT((SELECT tm_nm FROM ptsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MIN(pst2.sttng_tm_ordr) AND ptid=pst1.ptid AND sttngid=pst1.sttngid), ' to ', (SELECT tm_nm FROM ptsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MAX(pst2.sttng_tm_ordr) AND ptid=pst1.ptid AND sttngid=pst1.sttngid)) AS tm_nm, NULL AS sttng_tm_nt2, pst1.sttngid, pst1.sttng_tm_ordr
            FROM rel_tm
            INNER JOIN tm t1 ON rel_tm1=t1.tm_id INNER JOIN ptsttng_tm pst1 ON t1.tm_id=pst1.sttng_tmid INNER JOIN ptsttng ps ON pst1.ptid=ps.ptid AND pst1.sttngid=sttng_id
            INNER JOIN ptsttng_tm pst2 ON ps.ptid=pst2.ptid AND sttng_id=pst2.sttngid INNER JOIN pt ON pst1.ptid=pt_id
            WHERE rel_tm2='$tm_id' AND coll_ov IS NULL
            GROUP BY pt_id
            UNION
            SELECT pt_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2, sttngid, sttng_tm_ordr
            FROM ptsttng_tm pst
            INNER JOIN tm ON sttng_tmid=tm_id
            LEFT OUTER JOIN ptsttng ps ON pst.ptid=ps.ptid AND sttngid=sttng_id INNER JOIN pt ON pst.ptid=pt_id
            WHERE sttng_tmid='$tm_id' AND sttng_id IS NULL AND coll_ov IS NULL
            GROUP BY pt_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2
            UNION
            SELECT pt_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2, sttngid, sttng_tm_ordr
            FROM rel_tm
            INNER JOIN tm ON rel_tm1=tm_id INNER JOIN ptsttng_tm pst ON tm_id=sttng_tmid
            LEFT OUTER JOIN ptsttng ps ON pst.ptid=ps.ptid AND sttngid=sttng_id INNER JOIN pt ON pst.ptid=pt_id
            WHERE rel_tm2='$tm_id' AND sttng_id IS NULL AND coll_ov IS NULL
            GROUP BY pt_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2
            ORDER BY sttngid ASC, sttng_tm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring time data for playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['sttng_tm_nt1']) {if(!preg_match('/(^c\.| c\.)$/', $row['sttng_tm_nt1'])) {$sttng_tm_nt1=html($row['sttng_tm_nt1']).' ';} else {$sttng_tm_nt1=html($row['sttng_tm_nt1']);}}
        else {$sttng_tm_nt1='';}
        if($row['sttng_tm_nt2']) {if(!preg_match('/^(:|;|,|\.)/', $row['sttng_tm_nt2'])) {$sttng_tm_nt2=' '.html($row['sttng_tm_nt2']);} else {$sttng_tm_nt2=html($row['sttng_tm_nt2']);}}
        else {$sttng_tm_nt2='';}
        $sttng_tm=$sttng_tm_nt1.html($row['tm_nm']).$sttng_tm_nt2;
        if($sttng_tm!==$tm_nm) {$k++;}
        $pts[$row['pt_id']]['tms'][]=$sttng_tm;
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, coll_sbhdrid, coll_ordr
            FROM ptsttng_tm pst
            INNER JOIN pt ON pst.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE sttng_tmid='$tm_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            UNION
            SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, coll_sbhdrid, coll_ordr
            FROM rel_tm
            INNER JOIN ptsttng_tm pst ON rel_tm1=sttng_tmid INNER JOIN pt ON pst.ptid=pt_id
            LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE rel_tm2='$tm_id' AND coll_ov IS NOT NULL
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
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array(), 'tms'=>array());
        }

        if(!empty($sg_pt_ids)) {foreach($sg_pt_ids as $sg_pt_id) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';}}

        $sql= "SELECT coll_ov, pt_id, NULL AS sttng_tm_nt1, CONCAT((SELECT tm_nm FROM ptsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MIN(pst2.sttng_tm_ordr) AND ptid=pst1.ptid AND sttngid=pst1.sttngid), ' to ', (SELECT tm_nm FROM ptsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MAX(pst2.sttng_tm_ordr) AND ptid=pst1.ptid AND sttngid=pst1.sttngid)) AS tm_nm, NULL AS sttng_tm_nt2, pst1.sttngid, pst1.sttng_tm_ordr
              FROM ptsttng_tm pst1
              INNER JOIN tm t1 ON pst1.sttng_tmid=t1.tm_id INNER JOIN ptsttng ps ON pst1.ptid=ps.ptid AND pst1.sttngid=sttng_id
              INNER JOIN ptsttng_tm pst2 ON ps.ptid=pst2.ptid AND sttng_id=pst2.sttngid INNER JOIN pt ON pst1.ptid=pt_id
              WHERE pst1.sttng_tmid='$tm_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, pt_id
              UNION
              SELECT coll_ov, pt_id, NULL AS sttng_tm_nt1, CONCAT((SELECT tm_nm FROM ptsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MIN(pst2.sttng_tm_ordr) AND ptid=pst1.ptid AND sttngid=pst1.sttngid), ' to ', (SELECT tm_nm FROM ptsttng_tm INNER JOIN tm ON sttng_tmid=tm_id WHERE sttng_tm_ordr=MAX(pst2.sttng_tm_ordr) AND ptid=pst1.ptid AND sttngid=pst1.sttngid)) AS tm_nm, NULL AS sttng_tm_nt2, pst1.sttngid, pst1.sttng_tm_ordr
              FROM rel_tm
              INNER JOIN tm t1 ON rel_tm1=t1.tm_id INNER JOIN ptsttng_tm pst1 ON t1.tm_id=pst1.sttng_tmid INNER JOIN ptsttng ps ON pst1.ptid=ps.ptid AND pst1.sttngid=sttng_id
              INNER JOIN ptsttng_tm pst2 ON ps.ptid=pst2.ptid AND sttng_id=pst2.sttngid INNER JOIN pt ON pst1.ptid=pt_id
              WHERE rel_tm2='$tm_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, pt_id
              UNION
              SELECT coll_ov, pt_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2, sttngid, sttng_tm_ordr
              FROM ptsttng_tm pst
              INNER JOIN tm ON sttng_tmid=tm_id LEFT OUTER JOIN ptsttng ps ON pst.ptid=ps.ptid AND sttngid=sttng_id INNER JOIN pt ON pst.ptid=pt_id
              WHERE sttng_tmid='$tm_id' AND sttng_id IS NULL AND coll_ov IS NOT NULL
              GROUP BY coll_ov, pt_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2
              UNION
              SELECT coll_ov, pt_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2, sttngid, sttng_tm_ordr
              FROM rel_tm
              INNER JOIN tm ON rel_tm1=tm_id INNER JOIN ptsttng_tm pst ON tm_id=sttng_tmid LEFT OUTER JOIN ptsttng ps ON pst.ptid=ps.ptid AND sttngid=sttng_id INNER JOIN pt ON pst.ptid=pt_id
              WHERE rel_tm2='$tm_id' AND sttng_id IS NULL AND coll_ov IS NOT NULL
              GROUP BY coll_ov, pt_id, sttng_tm_nt1, tm_nm, sttng_tm_nt2
              ORDER BY sttngid ASC, sttng_tm_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring time data for segment playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_tm_nt1']) {$sttng_tm_nt1=html($row['sttng_tm_nt1']).' ';} else {$sttng_tm_nt1='';}
          if($row['sttng_tm_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_tm_nt2'])) {$sttng_tm_nt2=html($row['sttng_tm_nt2']);} else {$sttng_tm_nt2=' '.html($row['sttng_tm_nt2']);}}
          else {$sttng_tm_nt2='';}
          $sttng_tm=$sttng_tm_nt1.html($row['tm_nm']).$sttng_tm_nt2;
          if($sttng_tm!==$tm_nm) {$k++;}
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]['tms'][]=$sttng_tm;
        }
      }
    }

    $tm_id=html($tm_id);
    include 'playtext-setting-time.html.php';
  }
?>