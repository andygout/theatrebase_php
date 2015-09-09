<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';
  include $_SERVER['DOCUMENT_ROOT'].'/includes/profession/index.inc.php';

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $prof_url=cln($_GET['prof_url']);

  $sql="SELECT prof_id FROM prof WHERE prof_url='$prof_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $prof_id=$row['prof_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT prof_nm, prof_url FROM prof WHERE prof_id='$prof_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['prof_nm']);
    $prof_nm=html($row['prof_nm']);
    $prof_url=html($row['prof_url']);

    $sql="SELECT 1 FROM prsnprof WHERE profid='$prof_id' UNION SELECT 1 FROM rel_prof INNER JOIN prsnprof ON rel_prof1=profid WHERE rel_prof2='$prof_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of profession for person: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {$prsn_lnk='<a href="/person/profession/'.$prof_url.'">People</a> with '.$prof_nm.' as a profession';} else {$prsn_lnk='';}

    $sql= "SELECT prof_nm, prof_url FROM rel_prof INNER JOIN prof ON rel_prof2=prof_id
          WHERE rel_prof1='$prof_id' AND (EXISTS(SELECT 1 FROM charprof WHERE profid='$prof_id') OR EXISTS(SELECT 1 FROM rel_prof INNER JOIN charprof ON rel_prof1=profid WHERE rel_prof2='$prof_id'))
          ORDER BY rel_prof_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related profession (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_profs2[]='<a href="/character/profession/'.html($row['prof_url']).'">'.html($row['prof_nm']).'</a>';}

    $sql= "SELECT prof_nm, prof_url FROM rel_prof INNER JOIN charprof ON rel_prof1=profid INNER JOIN prof ON profid=prof_id WHERE rel_prof2='$prof_id'
          UNION
          SELECT prof_nm, prof_url FROM rel_prof rp1
          INNER JOIN charprof ON rp1.rel_prof1=profid INNER JOIN rel_prof rp2 ON profid=rp2.rel_prof1 INNER JOIN prof ON rp2.rel_prof2=prof_id
          WHERE rp1.rel_prof2='$prof_id' AND prof_id!=rp1.rel_prof2 AND prof_id IN(SELECT rel_prof1 FROM rel_prof WHERE rel_prof2='$prof_id')
          ORDER BY prof_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related profession (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_profs1[]='<a href="/character/profession/'.html($row['prof_url']).'">'.html($row['prof_nm']).'</a>';}

    $char_ids=array();

    $k=0;
    $sql= "SELECT char_id, char_nm, char_sffx_num, char_url, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti,
          COALESCE(char_alph, char_nm)char_alph, NULL AS prof_nm, (SELECT COUNT(*) FROM ptchar WHERE charid=char_id) AS pt_cnt
          FROM charprof INNER JOIN role ON charid=char_id WHERE profid='$prof_id' GROUP BY char_id
          UNION
          SELECT char_id, char_nm, char_sffx_num, char_url, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti,
          COALESCE(char_alph, char_nm)char_alph, GROUP_CONCAT(DISTINCT prof_nm ORDER BY prof_ordr ASC SEPARATOR ' / ') AS prof_nm, (SELECT COUNT(*) FROM ptchar WHERE charid=char_id) AS pt_cnt
          FROM rel_prof INNER JOIN prof ON rel_prof1=prof_id INNER JOIN charprof ON prof_id=profid INNER JOIN role ON charid=char_id
          WHERE rel_prof2='$prof_id' GROUP BY char_id
          ORDER BY char_alph ASC, char_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['char_sx']=='2') {$char_sx='Male';} elseif($row['char_sx']=='3') {$char_sx='Female';} elseif($row['char_sx']=='4') {$char_sx='Non-specific';} else {$char_sx=NULL;}
      if($row['char_age_frm']==$row['char_age_to']) {$char_age=html($row['char_age_frm']);} else {$char_age=html($row['char_age_frm']).' - '.html($row['char_age_to']);}
      if($row['char_dscr']) {$char_dscr='<em>'.html($row['char_dscr']).'</em>';} else {$char_dscr=NULL;}
      if($row['char_amnt']>1) {$char_amnt=' ['.html($row['char_amnt']).']';} elseif($row['char_mlti']) {$char_amnt=' [<em>multiple roles</em>]';} else {$char_amnt=NULL;}
      $char_nm='<a href="/character/'.html($row['char_url']).'">'.html($row['char_nm']).'</a>';
      $pt_cnt=$row['pt_cnt']-3;
      if($row['prof_nm'] && html($row['prof_nm'])!==$prof_nm) {$k++;}
      $char_ids[]=$row['char_id'];
      $chars[$row['char_id']]=array('char_nm'=>$char_nm, 'char_sx'=>$char_sx, 'char_age'=>$char_age, 'char_dscr'=>$char_dscr, 'char_amnt'=>$char_amnt, 'prof_nm'=>html($row['prof_nm']), 'pt_cnt'=>$pt_cnt, 'pts'=>array());
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

    $prof_id=html($prof_id);
    include 'character-profession.html.php';
  }
?>