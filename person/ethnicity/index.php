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

    $sql= "SELECT 1 FROM charethn WHERE ethnid='$ethn_id'
          UNION
          SELECT 1 FROM rel_ethn INNER JOIN charethn ON rel_ethn1=ethnid WHERE rel_ethn2='$ethn_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of ethnicity for character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {$char_lnk='<a href="/character/ethnicity/'.$ethn_url.'">Characters</a> of a '.$ethn_nm.' ethnicity';} else {$char_lnk='';}

    $sql= "SELECT ethn_nm, ethn_url FROM rel_ethn INNER JOIN ethn ON rel_ethn2=ethn_id
          WHERE rel_ethn1='$ethn_id' AND (EXISTS(SELECT 1 FROM prsn WHERE ethnid='$ethn_id') OR EXISTS(SELECT 1 FROM rel_ethn INNER JOIN prsn ON rel_ethn1=ethnid WHERE rel_ethn2='$ethn_id'))
          ORDER BY rel_ethn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related ethnicity (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_ethns2[]='<a href="/person/ethnicity/'.html($row['ethn_url']).'">'.html($row['ethn_nm']).'</a>';}

    $sql= "SELECT ethn_nm, ethn_url FROM rel_ethn INNER JOIN prsn ON rel_ethn1=ethnid INNER JOIN ethn ON ethnid=ethn_id WHERE rel_ethn2='$ethn_id'
          UNION
          SELECT ethn_nm, ethn_url FROM rel_ethn rg1
          INNER JOIN prsn ON rg1.rel_ethn1=ethnid INNER JOIN rel_ethn rg2 ON ethnid=rg2.rel_ethn1 INNER JOIN ethn ON rg2.rel_ethn2=ethn_id
          WHERE rg1.rel_ethn2='$ethn_id' AND ethn_id!=rg1.rel_ethn2 AND ethn_id IN(SELECT rel_ethn1 FROM rel_ethn WHERE rel_ethn2='$ethn_id')
          ORDER BY ethn_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related ethnicity (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_ethns1[]='<a href="/person/ethnicity/'.html($row['ethn_url']).'">'.html($row['ethn_nm']).'</a>';}

    $k=0;
    $sql= "SELECT prsn_fll_nm, prsn_url, prsn_lst_nm, prsn_frst_nm, prsn_sffx_num, NULL AS ethn_nm FROM prsn
          WHERE ethnid='$ethn_id' GROUP BY prsn_id
          UNION
          SELECT prsn_fll_nm, prsn_url, prsn_lst_nm, prsn_frst_nm, prsn_sffx_num, ethn_nm
          FROM rel_ethn INNER JOIN ethn ON rel_ethn1=ethn_id INNER JOIN prsn ON ethn_id=ethnid
          WHERE rel_ethn2='$ethn_id' GROUP BY prsn_id
          ORDER BY prsn_lst_nm ASC, prsn_frst_nm ASC, prsn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['ethn_nm'] && html($row['ethn_nm'])!==$ethn_nm) {$k++;}
      $ppl[]=array('prsn_nm'=>'<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>', 'ethn_nm'=>html($row['ethn_nm']));
    }

    $ethn_id=html($ethn_id);
    include 'person-ethnicity.html.php';
  }
?>