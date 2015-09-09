<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $abil_id=cln($_POST['abil_id']);
    $sql="SELECT abil_nm FROM abil WHERE abil_id='$abil_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ability details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['abil_nm']);
    $pagetitle=html($row['abil_nm']);
    $abil_nm=html($row['abil_nm']);
    $abil_id=html($abil_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $abil_id=cln($_POST['abil_id']);
    $abil_nm=trim(cln($_POST['abil_nm']));
    $abil_url=generateurl($abil_nm);
    $abil_nm_session=$_POST['abil_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $abil_nm))
    {$errors['abil_nm']='**You must enter an ability name.**';}
    elseif(strlen($abil_nm)>255)
    {$errors['abil_nm']='</br>**Ability name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $abil_nm))
    {$errors['abil_nm']='**Ability name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT abil_id, abil_nm FROM abil WHERE abil_url='$abil_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing ability URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['abil_id']!==$abil_id)
      {$errors['abil_url']='</br>**Duplicate URL exists for: '.html($row['abil_nm']). '. You must keep the original name or assign an ability name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $abil_id=cln($_POST['abil_id']);
      $sql="SELECT abil_nm FROM abil WHERE abil_id='$abil_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring ability details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['abil_nm']);
      $pagetitle=html($row['abil_nm']);
      $abil_nm=$_POST['abil_nm'];
      $errors['abil_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $abil_id=html($abil_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE abil SET
            abil_nm='$abil_nm',
            abil_url='$abil_url'
            WHERE abil_id='$abil_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted ability: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS ABILITY HAS BEEN EDITED:'.' '.html($abil_nm_session);
    header('Location: '.$abil_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $abil_id=cln($_POST['abil_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM charabil WHERE abilid='$abil_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring character-ability association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Character';}

    if(count($assocs)>0)
    {$errors['abil_dlt']='**Ability must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT abil_nm
            FROM abil
            WHERE abil_id='$abil_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring ability details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['abil_nm']);
      $pagetitle=html($row['abil_nm']);
      $abil_nm=$_POST['abil_nm'];
      $abil_id=html($abil_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT abil_nm
            FROM abil
            WHERE abil_id='$abil_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['abil_nm']);
      $pagetitle=html($row['abil_nm']);
      $abil_id=html($abil_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $abil_id=cln($_POST['abil_id']);
    $sql= "SELECT abil_nm
          FROM abil
          WHERE abil_id='$abil_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ability details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $abil_nm_session=$row['abil_nm'];

    $sql="DELETE FROM charabil WHERE abilid='$abil_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting ability-character associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM abil WHERE abil_id='$abil_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting ability: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS ABILITY HAS BEEN DELETED FROM THE DATABASE:'.' '.html($abil_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');e.
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $abil_id=cln($_POST['abil_id']);
    $sql= "SELECT abil_url
          FROM abil
          WHERE abil_id='$abil_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ability URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['abil_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $abil_url=cln($_GET['abil_url']);

  $sql= "SELECT abil_id
        FROM abil
        WHERE abil_url='$abil_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $abil_id=$row['abil_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT abil_nm FROM abil WHERE abil_id='$abil_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ability data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['abil_nm']);

    $char_ids=array();

    $sql= "SELECT char_id, char_nm, char_sffx_num, char_url, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti,
          COALESCE(char_alph, char_nm)char_alph, (SELECT COUNT(*) FROM ptchar WHERE charid=char_id) AS pt_cnt
          FROM charabil
          INNER JOIN role ON charid=char_id
          WHERE abilid='$abil_id'
          GROUP BY char_id ORDER BY char_alph ASC, char_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['char_sx']=='2') {$char_sx='Male';} elseif($row['char_sx']=='3') {$char_sx='Female';} elseif($row['char_sx']=='4') {$char_sx='Non-specific';} else {$char_sx=NULL;}if($row['char_age_frm']==$row['char_age_to']) {$char_age=html($row['char_age_frm']);} else {$char_age=html($row['char_age_frm']).' - '.html($row['char_age_to']);}
      if($row['char_dscr']) {$char_dscr='<em>'.html($row['char_dscr']).'</em>';} else {$char_dscr=NULL;}
      if($row['char_amnt']>1) {$char_amnt=' ['.html($row['char_amnt']).']';} elseif($row['char_mlti']) {$char_amnt=' [<em>multiple roles</em>]';} else {$char_amnt=NULL;}
      $char_nm='<a href="/character/'.html($row['char_url']).'">'.html($row['char_nm']).'</a>';
      $pt_cnt=$row['pt_cnt']-3;
      $chars[$row['char_id']]=array('char_nm'=>$char_nm, 'char_sx'=>$char_sx, 'char_age'=>$char_age, 'char_dscr'=>$char_dscr, 'char_amnt'=>$char_amnt, 'pt_cnt'=>$pt_cnt, 'pts'=>array());
      $char_ids[]=$row['char_id'];
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

    $abil_id=html($abil_id);
    include 'character-ability.html.php';
  }
?>