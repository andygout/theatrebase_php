<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $txt_vrsn_id=cln($_POST['txt_vrsn_id']);
    $sql="SELECT txt_vrsn_nm FROM txt_vrsn WHERE txt_vrsn_id='$txt_vrsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring text version details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['txt_vrsn_nm']);
    $pagetitle=html($row['txt_vrsn_nm']);
    $txt_vrsn_nm=html($row['txt_vrsn_nm']);
    $txt_vrsn_id=html($txt_vrsn_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $txt_vrsn_id=cln($_POST['txt_vrsn_id']);
    $txt_vrsn_nm=trim(cln($_POST['txt_vrsn_nm']));
    $txt_vrsn_url=generateurl($txt_vrsn_nm);
    $txt_vrsn_nm_session=$_POST['txt_vrsn_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $txt_vrsn_nm))
    {$errors['txt_vrsn_nm']='**You must enter a text version name.**';}
    if(strlen($txt_vrsn_nm)>255)
    {$errors['txt_vrsn_nm']='</br>**Text version name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $txt_vrsn_nm))
    {$errors['txt_vrsn_nm']='**Text version name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT txt_vrsn_id, txt_vrsn_nm FROM txt_vrsn WHERE txt_vrsn_url='$txt_vrsn_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing text version URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['txt_vrsn_id']!==$txt_vrsn_id)
      {$errors['txt_vrsn_url']='</br>**Duplicate URL exists for: '.html($row['txt_vrsn_nm']). '. You must keep the original name or assign a text version name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $txt_vrsn_id=cln($_POST['txt_vrsn_id']);
      $sql="SELECT txt_vrsn_nm FROM txt_vrsn WHERE txt_vrsn_id='$txt_vrsn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring text version details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['txt_vrsn_nm']);
      $pagetitle=html($row['txt_vrsn_nm']);
      $txt_vrsn_nm=$_POST['txt_vrsn_nm'];
      $errors['txt_vrsn_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $txt_vrsn_id=html($txt_vrsn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE txt_vrsn SET
            txt_vrsn_nm='$txt_vrsn_nm',
            txt_vrsn_url='$txt_vrsn_url'
            WHERE txt_vrsn_id='$txt_vrsn_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted text version: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS TEXT VERSION HAS BEEN EDITED:'.' '.html($txt_vrsn_nm_session);
    header('Location: '.$txt_vrsn_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $txt_vrsn_id=cln($_POST['txt_vrsn_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdtxt_vrsn WHERE txt_vrsnid='$txt_vrsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-text version association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM pttxt_vrsn WHERE txt_vrsnid='$txt_vrsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-text version association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    if(count($assocs)>0)
    {$errors['txt_vrsn_dlt']='**Text version must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT txt_vrsn_nm
            FROM txt_vrsn
            WHERE txt_vrsn_id='$txt_vrsn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring text version details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['txt_vrsn_nm']);
      $pagetitle=html($row['txt_vrsn_nm']);
      $txt_vrsn_nm=$_POST['txt_vrsn_nm'];
      $txt_vrsn_id=html($txt_vrsn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT txt_vrsn_nm
            FROM txt_vrsn
            WHERE txt_vrsn_id='$txt_vrsn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['txt_vrsn_nm']);
      $pagetitle=html($row['txt_vrsn_nm']);
      $txt_vrsn_id=html($txt_vrsn_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $txt_vrsn_id=cln($_POST['txt_vrsn_id']);
    $sql= "SELECT txt_vrsn_nm
          FROM txt_vrsn
          WHERE txt_vrsn_id='$txt_vrsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring text version details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $txt_vrsn_nm_session=$row['txt_vrsn_nm'];

    $sql="DELETE FROM prdtxt_vrsn WHERE txt_vrsnid='$txt_vrsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting text version-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM pttxt_vrsn WHERE txt_vrsnid='$txt_vrsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting text version-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM txt_vrsn WHERE txt_vrsn_id='$txt_vrsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting text version: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS TEXT VERSION HAS BEEN DELETED FROM THE DATABASE:'.' '.html($txt_vrsn_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $txt_vrsn_id=cln($_POST['txt_vrsn_id']);
    $sql= "SELECT txt_vrsn_url
          FROM txt_vrsn
          WHERE txt_vrsn_id='$txt_vrsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring text version URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['txt_vrsn_url']);
    exit();
  }
?>