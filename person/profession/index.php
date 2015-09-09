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

    $sql= "SELECT 1 FROM charprof WHERE profid='$prof_id'
          UNION
          SELECT 1 FROM rel_prof INNER JOIN charprof ON rel_prof1=profid WHERE rel_prof2='$prof_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of profession for character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {$char_lnk='<a href="/character/profession/'.$prof_url.'">Characters</a> with '.$prof_nm.' as profession';} else {$char_lnk='';}

    $sql= "SELECT prof_nm, prof_url FROM rel_prof INNER JOIN prof ON rel_prof2=prof_id
          WHERE rel_prof1='$prof_id' AND (EXISTS(SELECT 1 FROM prsnprof WHERE profid='$prof_id') OR EXISTS(SELECT 1 FROM rel_prof INNER JOIN prsnprof ON rel_prof1=profid WHERE rel_prof2='$prof_id'))
          ORDER BY rel_prof_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related profession (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_profs2[]='<a href="/person/profession/'.html($row['prof_url']).'">'.html($row['prof_nm']).'</a>';}

    $sql= "SELECT prof_nm, prof_url FROM rel_prof INNER JOIN prsnprof ON rel_prof1=profid INNER JOIN prof ON profid=prof_id WHERE rel_prof2='$prof_id'
          UNION
          SELECT prof_nm, prof_url FROM rel_prof rg1
          INNER JOIN prsnprof ON rg1.rel_prof1=profid INNER JOIN rel_prof rg2 ON profid=rg2.rel_prof1 INNER JOIN prof ON rg2.rel_prof2=prof_id
          WHERE rg1.rel_prof2='$prof_id' AND prof_id!=rg1.rel_prof2 AND prof_id IN(SELECT rel_prof1 FROM rel_prof WHERE rel_prof2='$prof_id')
          ORDER BY prof_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related profession (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_profs1[]='<a href="/person/profession/'.html($row['prof_url']).'">'.html($row['prof_nm']).'</a>';}

    $k=0;
    $sql= "SELECT prsn_fll_nm, prsn_url, prsn_lst_nm, prsn_frst_nm, prsn_sffx_num, NULL AS prof_nm FROM prsnprof INNER JOIN prsn ON prsnid=prsn_id
          WHERE profid='$prof_id' GROUP BY prsn_id
          UNION
          SELECT prsn_fll_nm, prsn_url, prsn_lst_nm, prsn_frst_nm, prsn_sffx_num,
          GROUP_CONCAT(DISTINCT prof_nm ORDER BY prof_ordr ASC SEPARATOR ' / ') AS prof_nm
          FROM rel_prof INNER JOIN prof ON rel_prof1=prof_id INNER JOIN prsnprof ON prof_id=profid INNER JOIN prsn ON prsnid=prsn_id
          WHERE rel_prof2='$prof_id' GROUP BY prsn_id
          ORDER BY prsn_lst_nm ASC, prsn_frst_nm ASC, prsn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prof_nm'] && html($row['prof_nm'])!==$prof_nm) {$k++;}
      $ppl[]=array('prsn_nm'=>'<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>', 'prof_nm'=>html($row['prof_nm']));
    }

    $prof_id=html($prof_id);
    include 'person-profession.html.php';
  }
?>