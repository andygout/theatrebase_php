<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/ethnicity/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $ethn_url=cln($_GET['ethn_url']);

  $sql="SELECT ethn_id FROM ethn WHERE ethn_url='$ethn_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $ethn_id=$row['ethn_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT ethn_nm, ethn_url FROM ethn WHERE ethn_id='$ethn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['ethn_nm']);
    $ethn_nm=html($row['ethn_nm']);
    $ethn_url=html($row['ethn_url']);

    $sql="SELECT 1 FROM prsn WHERE ethnid='$ethn_id' UNION SELECT 1 FROM rel_ethn INNER JOIN prsn ON rel_ethn1=ethnid WHERE rel_ethn2='$ethn_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of ethnicity for person: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {$prsn_lnk='<a href="/person/ethnicity/'.$ethn_url.'">People</a> of a '.$ethn_nm.' ethnicity';} else {$prsn_lnk='';}

    $sql= "SELECT ethn_nm, ethn_url FROM rel_ethn INNER JOIN ethn ON rel_ethn2=ethn_id
          WHERE rel_ethn1='$ethn_id' AND (EXISTS(SELECT 1 FROM charethn WHERE ethnid='$ethn_id') OR EXISTS(SELECT 1 FROM rel_ethn INNER JOIN charethn ON rel_ethn1=ethnid WHERE rel_ethn2='$ethn_id'))
          ORDER BY rel_ethn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related ethnicity (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_ethns2[]='<a href="/character/ethnicity/'.html($row['ethn_url']).'">'.html($row['ethn_nm']).'</a>';}

    $sql= "SELECT ethn_nm, ethn_url FROM rel_ethn INNER JOIN charethn ON rel_ethn1=ethnid INNER JOIN ethn ON ethnid=ethn_id WHERE rel_ethn2='$ethn_id'
          UNION
          SELECT ethn_nm, ethn_url FROM rel_ethn re1
          INNER JOIN charethn ON re1.rel_ethn1=ethnid INNER JOIN rel_ethn re2 ON ethnid=re2.rel_ethn1 INNER JOIN ethn ON re2.rel_ethn2=ethn_id
          WHERE re1.rel_ethn2='$ethn_id' AND ethn_id!=re1.rel_ethn2 AND ethn_id IN(SELECT rel_ethn1 FROM rel_ethn WHERE rel_ethn2='$ethn_id')
          ORDER BY ethn_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related ethnicity (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_ethns1[]='<a href="/character/ethnicity/'.html($row['ethn_url']).'">'.html($row['ethn_nm']).'</a>';}

    $char_ids=array();

    $k=0;
    $sql= "SELECT char_id, char_nm, char_sffx_num, char_url, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti,
          COALESCE(char_alph, char_nm)char_alph, NULL AS ethn_nm, (SELECT COUNT(*) FROM ptchar WHERE charid=char_id) AS pt_cnt
          FROM charethn INNER JOIN role ON charid=char_id WHERE ethnid='$ethn_id' GROUP BY char_id
          UNION
          SELECT char_id, char_nm, char_sffx_num, char_url, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti,
          COALESCE(char_alph, char_nm)char_alph, GROUP_CONCAT(DISTINCT ethn_nm ORDER BY ethn_ordr ASC SEPARATOR ' / ') AS ethn_nm, (SELECT COUNT(*) FROM ptchar WHERE charid=char_id) AS pt_cnt
          FROM rel_ethn INNER JOIN ethn ON rel_ethn1=ethn_id INNER JOIN charethn ON ethn_id=ethnid INNER JOIN role ON charid=char_id
          WHERE rel_ethn2='$ethn_id' GROUP BY char_id
          ORDER BY char_alph ASC, char_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['char_sx']=='2') {$char_sx='Male';} elseif($row['char_sx']=='3') {$char_sx='Female';} elseif($row['char_sx']=='4') {$char_sx='Non-specific';} else {$char_sx=NULL;}if($row['char_age_frm']==$row['char_age_to']) {$char_age=html($row['char_age_frm']);} else {$char_age=html($row['char_age_frm']).' - '.html($row['char_age_to']);}
      if($row['char_dscr']) {$char_dscr='<em>'.html($row['char_dscr']).'</em>';} else {$char_dscr=NULL;}
      if($row['char_amnt']>1) {$char_amnt=' ['.html($row['char_amnt']).']';} elseif($row['char_mlti']) {$char_amnt=' [<em>multiple roles</em>]';} else {$char_amnt=NULL;}
      $char_nm='<a href="/character/'.html($row['char_url']).'">'.html($row['char_nm']).'</a>';
      $pt_cnt=$row['pt_cnt']-3;
      if($row['ethn_nm'] && html($row['ethn_nm'])!==$ethn_nm) {$k++;}
      $char_ids[]=$row['char_id'];
      $chars[$row['char_id']]=array('char_nm'=>$char_nm, 'char_sx'=>$char_sx, 'char_age'=>$char_age, 'char_dscr'=>$char_dscr, 'char_amnt'=>$char_amnt, 'ethn_nm'=>html($row['ethn_nm']), 'pt_cnt'=>$pt_cnt, 'pts'=>array());
    }

    if(!empty($char_ids))
    {
      foreach($char_ids as $char_id)
      {
        $sql= "SELECT charid, pt_nm, pt_url, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, COALESCE(pt_alph, pt_nm)pt_alph
              FROM ptchar
              INNER JOIN pt ON ptid=pt_id WHERE charid='$char_id'
              GROUP BY charid, pt_id ORDER BY pt_yr_wrttn ASC, coll_ordr ASC, pt_alph DESC LIMIT 3";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring character playtext data data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $pt_nm=html($row['pt_nm']);
          $chars[$row['charid']]['pts'][]=$pt_nm.' ('.$pt_yr.')';
        }
      }
    }

    $ethn_id=html($ethn_id);
    include 'character-ethnicity.html.php';
  }
?>