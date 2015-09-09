<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $crs_typ_id=cln($_POST['crs_typ_id']);
    $sql="SELECT crs_typ_nm FROM crs_typ WHERE crs_typ_id='$crs_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['crs_typ_nm']);
    $pagetitle=html($row['crs_typ_nm']);
    $crs_typ_nm=html($row['crs_typ_nm']);
    $crs_typ_id=html($crs_typ_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $crs_typ_id=cln($_POST['crs_typ_id']);
    $crs_typ_nm=trim(cln($_POST['crs_typ_nm']));
    $crs_typ_url=generateurl($crs_typ_nm);
    $crs_typ_nm_session=$_POST['crs_typ_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $crs_typ_nm))
    {$errors['crs_typ_nm']='**You must enter a course type name.**';}
    if(strlen($crs_typ_nm)>255)
    {$errors['crs_typ_nm']='</br>**Course type name is allowed a maximum of 255 characters.**';}
    else
    {
      $sql= "SELECT crs_typ_id, crs_typ_nm
            FROM crs_typ
            WHERE crs_typ_url='$crs_typ_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing course type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['crs_typ_id']!==$crs_typ_id)
      {$errors['crs_typ_url']='</br>**Duplicate URL exists for: '.html($row['crs_typ_nm']). '. You must keep the original name or assign a course type name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $crs_typ_id=cln($_POST['crs_typ_id']);
      $sql= "SELECT crs_typ_nm
            FROM crs_typ
            WHERE crs_typ_id='$crs_typ_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring course type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['crs_typ_nm']);
      $pagetitle=html($row['crs_typ_nm']);
      $crs_typ_nm=$_POST['crs_typ_nm'];
      $errors['crs_typ_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $crs_typ_id=html($crs_typ_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE crs_typ SET
            crs_typ_nm='$crs_typ_nm',
            crs_typ_url='$crs_typ_url'
            WHERE crs_typ_id='$crs_typ_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating course type info for submitted course type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS COURSE TYPE HAS BEEN EDITED:'.' '.html($crs_typ_nm_session);
    header('Location: '.$crs_typ_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $crs_typ_id=cln($_POST['crs_typ_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM crs WHERE crs_typid='$crs_typ_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring course-course type association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Course';}

    if(count($assocs)>0)
    {$errors['crs_typ_dlt']='**Course type must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT crs_typ_nm
            FROM crs_typ
            WHERE crs_typ_id='$crs_typ_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error';
      $error='Error acquiring course type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['crs_typ_nm']);
      $pagetitle=html($row['crs_typ_nm']);
      $crs_typ_nm=$_POST['crs_typ_nm'];
      $crs_typ_id=html($crs_typ_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT crs_typ_nm
            FROM crs_typ
            WHERE crs_typ_id='$crs_typ_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error';
      $error='Error acquiring course type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab= 'Delete confirmation: '.html($row['crs_typ_nm']);
      $pagetitle=html($row['crs_typ_nm']);
      $crs_typ_id=html($crs_typ_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $crs_typ_id=cln($_POST['crs_typ_id']);
    $sql="SELECT crs_typ_nm FROM crs_typ WHERE crs_typ_id='$crs_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $crs_typ_nm_session=$row['crs_typ_nm'];

    $sql="UPDATE crs SET crs_typid=NULL WHERE crs_typid='$crs_typ_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-course type associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM crs_typ WHERE crs_typ_id='$crs_typ_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS COURSE TYPE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($crs_typ_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $crs_typ_id=cln($_POST['crs_typ_id']);
    $sql= "SELECT crs_typ_url
          FROM crs_typ
          WHERE crs_typ_id='$crs_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['crs_typ_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $comp_url=cln($_GET['comp_url']);
  $crs_typ_url=cln($_GET['crs_typ_url']);

  $sql= "SELECT crs_typid, crs_schlid
        FROM crs
        WHERE crs_typid=(SELECT crs_typ_id FROM crs_typ WHERE crs_typ_url='$crs_typ_url')
        AND crs_schlid=(SELECT comp_id FROM comp WHERE comp_url='$comp_url')";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $crs_typ_id=$row['crs_typid'];
  $crs_schl_id=$row['crs_schlid'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT crs_typ_nm, comp_nm, comp_url
          FROM crs
          INNER JOIN crs_typ ON crs_typid=crs_typ_id INNER JOIN comp ON crs_schlid=comp_id
          WHERE crs_typid=(SELECT crs_typ_id FROM crs_typ WHERE crs_typ_id='$crs_typ_id')
          AND crs_schlid=(SELECT comp_id FROM comp WHERE comp_id='$crs_schl_id')
          LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course school data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['comp_nm']).':</br>'.html($row['crs_typ_nm']);
    $crs_schl_nm='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';

    $sql= "SELECT comp_url, crs_typ_nm, crs_typ_url, crs_yr_strt, crs_yr_end, crs_yr_url,
          DATE_FORMAT(crs_dt_strt, '%d %b %Y') AS crs_dt_strt_dsply, DATE_FORMAT(crs_dt_end, '%d %b %Y') AS crs_dt_end_dsply
          FROM crs
          INNER JOIN comp ON crs_schlid=comp_id
          INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE crs_typid='$crs_typ_id' AND crs_schlid='$crs_schl_id'
          ORDER BY crs_yr_end DESC, crs_yr_strt DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring courses (as drama school): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['crs_yr_strt']!==$row['crs_yr_end'])
      {
        $crs_yr=$row['crs_yr_strt'].' - '.$row['crs_yr_end'];
        $crs_yr_nm_dsply=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);
      }
      else
      {$crs_yr=$row['crs_yr_strt']; $crs_yr_nm_dsply=$row['crs_yr_strt'];}
      if($row['crs_dt_strt_dsply'] && $row['crs_dt_end_dsply']) {$crs_dts=$row['crs_dt_strt_dsply'].' - '.$row['crs_dt_end_dsply'];}
      else {$crs_dts=$crs_yr;}
      $crs_nm='<a href="/course/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'/'.html($row['crs_yr_url']).'">'.html($row['crs_typ_nm']).' ('.$crs_yr_nm_dsply.')</a>';
      $crss[]=array('crs_nm'=>$crs_nm, 'crs_dts'=>html($crs_dts));
    }

    $sql= "SELECT comp_nm, comp_url, crs_typ_nm, crs_typ_url, COALESCE(comp_alph, comp_nm)comp_alph
          FROM crs
          INNER JOIN comp ON crs_schlid=comp_id INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE crs_typid='$crs_typ_id' AND crs_schlid!='$crs_schl_id'
          ORDER BY comp_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring drama schools (that also run this course type): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$comps[]='<a href="/course/type/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'">'.html($row['comp_nm']).': '.html($row['crs_typ_nm']).'</a>';}

    $crs_typ_id=html($crs_typ_id);
    include 'course-type.html.php';
  }
?>