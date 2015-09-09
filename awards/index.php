<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $awrds_id=cln($_POST['awrds_id']);
    $sql="SELECT awrds_nm FROM awrds WHERE awrds_id='$awrds_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['awrds_nm']);
    $pagetitle=html($row['awrds_nm']);
    $awrds_nm=html($row['awrds_nm']);
    $awrds_id=html($awrds_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $awrds_id=cln($_POST['awrds_id']);
    $awrds_nm=trim(cln($_POST['awrds_nm']));
    $awrds_session=$_POST['awrds_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $awrds_nm))
    {$errors['awrds_nm']='**You must enter an awards name.**';}
    elseif(strlen($awrds_nm)>255)
    {$errors['awrds_nm']='</br>**Awards name is allowed a maximum of 255 characters.**';}
    else
    {
      $awrds_url=generateurl($awrds_nm);
      $awrds_alph=alph($awrds_nm);

      $sql="SELECT awrds_id, awrds_nm FROM awrds WHERE awrds_url='$awrds_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing awards URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['awrds_id']!==$awrds_id)
      {$errors['awrds_nm']='</br>**Duplicate URL exists for: '.html($row['awrds_nm']). '. You must keep the original name or assign an awards name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $awrds_id=cln($_POST['awrds_id']);
      $sql="SELECT awrds_nm FROM awrds WHERE awrds_id='$awrds_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['awrds_nm']);
      $pagetitle=html($row['awrds_nm']);
      $awrds_nm=$_POST['awrds_nm'];
      $errors['awrds_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $awrds_id=html($awrds_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE awrds SET
            awrds_nm='$awrds_nm',
            awrds_alph=CASE WHEN '$awrds_alph'!='' THEN '$awrds_alph' END,
            awrds_url='$awrds_url'
            WHERE awrds_id='$awrds_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating awards info for submitted awards: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THESE AWARDS HAVE BEEN EDITED:'.' '.html($awrds_session);
    header('Location: '.$awrds_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $awrds_id=cln($_POST['awrds_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM awrd WHERE awrdsid='$awrds_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for award-awards associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Awards ceremony';}

    if(count($assocs)>0)
    {$errors['awrds_dlt']='**Awards must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $awrds_id=cln($_POST['awrds_id']);
      $sql="SELECT awrds_nm FROM awrds WHERE awrds_id='$awrds_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['awrds_nm']);
      $pagetitle=html($row['awrds_nm']);
      $awrds_nm=$_POST['awrds_nm'];
      $awrds_id=html($awrds_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT awrds_nm FROM awrds WHERE awrds_id='$awrds_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab= 'Delete confirmation: '.html($row['awrds_nm']);
      $pagetitle=html($row['awrds_nm']);
      $awrds_id=html($awrds_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $awrds_id=cln($_POST['awrds_id']);
    $sql="SELECT awrds_nm FROM awrds WHERE awrds_id='$awrds_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $awrds_session=$row['awrds_nm'];

    $sql="DELETE FROM awrd WHERE awrdsid='$awrds_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting awards-award associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM awrds WHERE awrds_id='$awrds_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting awards: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THESE AWARDS HAVE BEEN DELETED FROM THE DATABASE:'.' '.html($awrds_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $awrds_id=cln($_POST['awrds_id']);
    $sql="SELECT awrds_url FROM awrds WHERE awrds_id='$awrds_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['awrds_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
  $awrds_url=cln($_GET['awrds_url']);

  $sql="SELECT awrds_id FROM awrds WHERE awrds_url='$awrds_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $awrds_id=$row['awrds_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT awrds_nm FROM awrds WHERE awrds_id='$awrds_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['awrds_nm']);

    $sql= "SELECT awrds_nm, awrds_url, awrd_yr, awrd_yr_end, awrd_yr_url, DATE_FORMAT(awrd_dt, '%a, %d %b %Y') AS awrd_dt, thtr_fll_nm
          FROM awrds
          INNER JOIN awrd ON awrds_id=awrdsid LEFT OUTER JOIN thtr ON thtrid=thtr_id
          WHERE awrds_id='$awrds_id'
          ORDER BY awrd_yr DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award year data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if(preg_match('/TBC$/', $row['thtr_fll_nm'])) {$thtr='<em>'.html($row['thtr_fll_nm']).'</em>';} else {$thtr=html($row['thtr_fll_nm']);}
      if($row['awrd_yr_end']) {$awrd_yr_end=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end='';}
      $awrd_nm_yr='<a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrds_nm']. ' '.$row['awrd_yr']).html($awrd_yr_end).'</a>';
      $awrd_yrs[]=array('awrd_nm_yr'=>$awrd_nm_yr, 'awrd_dt'=>$row['awrd_dt'], 'thtr'=>$thtr);
    }

    $sql= "SELECT awrds_url, MIN(awrd_yr) AS minawrd_yr, MIN(awrd_yr_end) AS minawrd_yr_end,
          MAX(awrd_yr) AS maxawrd_yr, MAX(awrd_yr_end) AS maxawrd_yr_end, awrd_ctgry_nm, awrd_ctgry_url, COALESCE(awrd_ctgry_alph, awrd_ctgry_nm)awrd_ctgry_alph,
          GROUP_CONCAT(DISTINCT awrd_ctgry_alt_nm ORDER BY awrd_yr DESC SEPARATOR ' / ') AS ctgry_alt_nm
          FROM awrds
          INNER JOIN awrd ON awrds_id=awrdsid INNER JOIN awrdnoms an ON awrd_id=an.awrdid INNER JOIN awrd_ctgry ON an.awrd_ctgryid=awrd_ctgry_id
          INNER JOIN awrdctgrys ac ON an.awrdid=ac.awrdid AND an.awrd_ctgryid=ac.awrd_ctgryid
          WHERE awrds_id='$awrds_id'
          GROUP BY awrd_ctgry_id ORDER BY maxawrd_yr DESC, minawrd_yr DESC, awrd_ctgry_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $ctgry='<a href="/awards/category/'.html($row['awrds_url']).'/'.html($row['awrd_ctgry_url']).'">'.html($row['awrd_ctgry_nm']).'</a>';
      if($row['minawrd_yr']==$row['maxawrd_yr'])
      {
        if($row['minawrd_yr_end'] && abs($row['minawrd_yr_end'] - $row['minawrd_yr'])>1) {$minawrd_yr_end='';}
        else {$minawrd_yr_end=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['minawrd_yr_end']);}
        $ctgry_dts=$row['minawrd_yr'].$minawrd_yr_end;
      }
      else
      {
        if(!$row['minawrd_yr_end'] || ($row['minawrd_yr_end'] && $row['minawrd_yr_end'] - $row['minawrd_yr']!==1)) {$minawrd_yr_end='';}
        else {$minawrd_yr_end=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['minawrd_yr_end']);}
        if(!$row['maxawrd_yr_end'] || ($row['maxawrd_yr_end'] && $row['maxawrd_yr_end'] - $row['maxawrd_yr']!==1)) {$maxawrd_yr_end='';}
        else {$maxawrd_yr_end=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['maxawrd_yr_end']);}
        $ctgry_dts=$row['minawrd_yr'].$minawrd_yr_end.' - '.$row['maxawrd_yr'].$maxawrd_yr_end;
      }
      $ctgrys[]=array('ctgry'=>$ctgry, 'ctgry_dts'=>$ctgry_dts, 'ctgry_alt_nm'=>html($row['ctgry_alt_nm']));
    }

    $awrds_id=html($awrds_id);
    include 'awards.html.php';
  }
?>