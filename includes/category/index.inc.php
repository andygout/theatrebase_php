<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ctgry_id=cln($_POST['ctgry_id']);
    $sql="SELECT ctgry_nm FROM ctgry WHERE ctgry_id='$ctgry_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['ctgry_nm']);
    $pagetitle=html($row['ctgry_nm']);
    $ctgry_nm=html($row['ctgry_nm']);
    $ctgry_id=html($ctgry_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $ctgry_id=cln($_POST['ctgry_id']);
    $ctgry_nm=trim(cln($_POST['ctgry_nm']));
    $ctgry_nm_session=$_POST['ctgry_nm'];
    $ctgry_url=generateurl($ctgry_nm);

    $errors=array();

    if(!preg_match('/\S+/', $ctgry_nm))
    {$errors['ctgry_nm']='**You must enter a category name.**';}
    elseif(strlen($ctgry_nm)>255)
    {$errors['ctgry_nm']='</br>**Category name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $ctgry_nm))
    {$errors['ctgry_nm']='**Category name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT ctgry_id, ctgry_nm FROM ctgry WHERE ctgry_url='$ctgry_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing category URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['ctgry_id']!==$ctgry_id)
      {$errors['ctgry_url']='</br>**Duplicate URL exists for: '.html($row['ctgry_nm']). '. You must keep the original name or assign a category name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $ctgry_id=cln($_POST['ctgry_id']);
      $sql="SELECT ctgry_nm FROM ctgry WHERE ctgry_id='$ctgry_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring category details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['ctgry_nm']);
      $pagetitle=html($row['ctgry_nm']);
      $ctgry_nm=$_POST['ctgry_nm'];
      $errors['ctgry_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $ctgry_id=html($ctgry_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE ctgry SET
            ctgry_nm='$ctgry_nm',
            ctgry_url='$ctgry_url'
            WHERE ctgry_id='$ctgry_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted category: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS CATEGORY HAS BEEN EDITED:'.' '.html($ctgry_nm_session);
    header('Location: '.$ctgry_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $ctgry_id=cln($_POST['ctgry_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdctgry WHERE ctgryid='$ctgry_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-category association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM ptctgry WHERE ctgryid='$ctgry_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-category association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    if(count($assocs)>0)
    {$errors['ctgry_dlt']='**Category must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT ctgry_nm
            FROM ctgry
            WHERE ctgry_id='$ctgry_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring category details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['ctgry_nm']);
      $pagetitle=html($row['ctgry_nm']);
      $ctgry_nm=$_POST['ctgry_nm'];
      $ctgry_id=html($ctgry_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT ctgry_nm
            FROM ctgry
            WHERE ctgry_id='$ctgry_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['ctgry_nm']);
      $pagetitle=html($row['ctgry_nm']);
      $ctgry_id=html($ctgry_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ctgry_id=cln($_POST['ctgry_id']);
    $sql= "SELECT ctgry_nm
          FROM ctgry
          WHERE ctgry_id='$ctgry_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $ctgry_nm_session=$row['ctgry_nm'];

    $sql="DELETE FROM prdctgry WHERE ctgryid='$ctgry_id'";
    if(!mysqli_query($link, $sql))
    {$error='Error deleting category-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptctgry WHERE ctgryid='$ctgry_id'";
    if(!mysqli_query($link, $sql))
    {$error='Error deleting category-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ctgry WHERE ctgry_id='$ctgry_id'";
    if(!mysqli_query($link, $sql))
    {$error='Error deleting category: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS CATEGORY HAS BEEN DELETED FROM THE DATABASE:'.' '.html($ctgry_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ctgry_id=cln($_POST['ctgry_id']);
    $sql= "SELECT ctgry_url
          FROM ctgry
          WHERE ctgry_id='$ctgry_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['ctgry_url']);
    exit();
  }
?>