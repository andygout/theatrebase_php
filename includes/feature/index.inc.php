<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ftr_id=cln($_POST['ftr_id']);
    $sql="SELECT ftr_nm FROM ftr WHERE ftr_id='$ftr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring feature details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['ftr_nm']);
    $pagetitle=html($row['ftr_nm']);
    $ftr_nm=html($row['ftr_nm']);
    $ftr_id=html($ftr_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $ftr_id=cln($_POST['ftr_id']);
    $ftr_nm=trim(cln($_POST['ftr_nm']));
    $ftr_url=generateurl($ftr_nm);
    $ftr_nm_session=$_POST['ftr_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $ftr_nm))
    {$errors['ftr_nm']='**You must enter a feature name.**';}
    elseif(strlen($ftr_nm)>255)
    {$errors['ftr_nm']='</br>**Feature name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $ftr_nm))
    {$errors['ftr_nm']='**Feature name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT ftr_id, ftr_nm FROM ftr WHERE ftr_url='$ftr_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing feature URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['ftr_id']!==$ftr_id)
      {$errors['ftr_url']='</br>**Duplicate URL exists for: '.html($row['ftr_nm']). '. You must keep the original name or assign a feature name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $ftr_id=cln($_POST['ftr_id']);
      $sql="SELECT ftr_nm FROM ftr WHERE ftr_id='$ftr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring feature details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['ftr_nm']);
      $pagetitle=html($row['ftr_nm']);
      $ftr_nm=$_POST['ftr_nm'];
      $errors['ftr_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $ftr_id=html($ftr_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE ftr SET
            ftr_nm='$ftr_nm',
            ftr_url='$ftr_url'
            WHERE ftr_id='$ftr_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted feature: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS FEATURE HAS BEEN EDITED:'.' '.html($ftr_nm_session);
    header('Location: '.$ftr_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $ftr_id=cln($_POST['ftr_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdftr WHERE ftrid='$ftr_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production-feature association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM ptftr WHERE ftrid='$ftr_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext-feature association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    if(count($assocs)>0)
    {$errors['ftr_dlt']='**Feature must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT ftr_nm
            FROM ftr
            WHERE ftr_id='$ftr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring feature details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['ftr_nm']);
      $pagetitle=html($row['ftr_nm']);
      $ftr_nm=$_POST['ftr_nm'];
      $ftr_id=html($ftr_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT ftr_nm
            FROM ftr
            WHERE ftr_id='$ftr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['ftr_nm']);
      $pagetitle=html($row['ftr_nm']);
      $ftr_id=html($ftr_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ftr_id=cln($_POST['ftr_id']);
    $sql= "SELECT ftr_nm
          FROM ftr
          WHERE ftr_id='$ftr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring feature details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $ftr_nm_session=$row['ftr_nm'];

    $sql="DELETE FROM prdftr WHERE ftrid='$ftr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting feature-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptftr WHERE ftrid='$ftr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting feature-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ftr WHERE ftr_id='$ftr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting feature: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS FEATURE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($ftr_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ftr_id=cln($_POST['ftr_id']);
    $sql= "SELECT ftr_url
          FROM ftr
          WHERE ftr_id='$ftr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring feature URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['ftr_url']);
    exit();
  }
?>