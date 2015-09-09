<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thtr_typ_id=cln($_POST['thtr_typ_id']);
    $sql="SELECT thtr_typ_nm FROM thtr_typ WHERE thtr_typ_id='$thtr_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['thtr_typ_nm']);
    $pagetitle=html($row['thtr_typ_nm']);
    $thtr_typ_nm=html($row['thtr_typ_nm']);
    $thtr_typ_id=html($thtr_typ_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $thtr_typ_id=cln($_POST['thtr_typ_id']);
    $thtr_typ_nm=trim(cln($_POST['thtr_typ_nm']));
    $thtr_typ_url=generateurl($thtr_typ_nm);
    $thtr_typ_nm_session=$_POST['thtr_typ_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $thtr_typ_nm))
    {$errors['thtr_typ_nm']='**You must enter a theatre type name.**';}
    elseif(strlen($thtr_typ_nm)>255)
    {$errors['thtr_typ_nm']='</br>**Theatre type is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $thtr_typ_nm))
    {$errors['thtr_typ_nm']='**Theatre type name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT thtr_typ_id, thtr_typ_nm FROM thtr_typ WHERE thtr_typ_url='$thtr_typ_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing theatre type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['thtr_typ_id']!==$thtr_typ_id)
      {$errors['thtr_typ_url']='</br>**Duplicate URL exists for: '.html($row['thtr_typ_nm']). '. You must keep the original name or assign a theatre type name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $thtr_typ_id=cln($_POST['thtr_typ_id']);
      $sql="SELECT thtr_typ_nm FROM thtr_typ WHERE thtr_typ_id='$thtr_typ_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring theatre type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['thtr_typ_nm']);
      $pagetitle=html($row['thtr_typ_nm']);
      $thtr_typ_nm=$_POST['thtr_typ_nm'];
      $errors['thtr_typ_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $thtr_typ_id=html($thtr_typ_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE thtr_typ SET
            thtr_typ_nm='$thtr_typ_nm',
            thtr_typ_url='$thtr_typ_url'
            WHERE thtr_typ_id='$thtr_typ_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted theatre type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS THEATRE TYPE HAS BEEN EDITED:'.' '.html($thtr_typ_nm_session);
    header('Location: '.$thtr_typ_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $thtr_typ_id=cln($_POST['thtr_typ_id']);

    $sql="SELECT thtr_typ_nm FROM thtr_typ WHERE thtr_typ_id='$thtr_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$pagetitle='Error'; $error='Error acquiring theatre type details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Delete confirmation: '.html($row['thtr_typ_nm']);
    $pagetitle=html($row['thtr_typ_nm']);
    $thtr_typ_id=html($thtr_typ_id);
    include 'delete.html.php';
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thtr_typ_id=cln($_POST['thtr_typ_id']);
    $sql= "SELECT thtr_typ_nm
          FROM thtr_typ
          WHERE thtr_typ_id='$thtr_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $thtr_typ_nm_session=$row['thtr_typ_nm'];

    $sql="DELETE FROM thtrtyp WHERE thtr_typid='$thtr_typ_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-type associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thtr_typ WHERE thtr_typ_id='$thtr_typ_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theatre type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS THEATRE TYPE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($thtr_typ_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thtr_typ_id=cln($_POST['thtr_typ_id']);
    $sql= "SELECT thtr_typ_url
          FROM thtr_typ
          WHERE thtr_typ_id='$thtr_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['thtr_typ_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $thtr_typ_url=cln($_GET['thtr_typ_url']);

  $sql= "SELECT thtr_typ_id
        FROM thtr_typ
        WHERE thtr_typ_url='$thtr_typ_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $thtr_typ_id=$row['thtr_typ_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT thtr_typ_nm FROM thtr_typ WHERE thtr_typ_id='$thtr_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['thtr_typ_nm']);

    $sql= "SELECT thtr_id, thtr_nm, thtr_lctn, thtr_sffx_num, thtr_url, COALESCE(thtr_alph, thtr_nm)thtr_alph, (SELECT COUNT(*) FROM thtr WHERE srthtrid=t1.thtr_id AND thtr_clsd=0 AND thtr_nm_exp=0) AS sbthtr_cnt
          FROM thtrtyp
          INNER JOIN thtr t1 ON thtrid=thtr_id
          WHERE thtr_typid='$thtr_typ_id' AND thtr_clsd=0 AND thtr_nm_exp=0 AND srthtrid IS NULL
          UNION
          SELECT t2.thtr_id, t2.thtr_nm, t2.thtr_lctn, t2.thtr_sffx_num, t2.thtr_url, COALESCE(t2.thtr_alph, t2.thtr_nm)thtr_alph, (SELECT COUNT(*) FROM thtr WHERE srthtrid=t2.thtr_id AND thtr_clsd=0 AND thtr_nm_exp=0) AS sbthtr_cnt
          FROM thtrtyp tt
          INNER JOIN thtr t1 ON tt.thtrid=t1.thtr_id INNER JOIN thtr t2 on t1.srthtrid=t2.thtr_id
          WHERE thtr_typid='$thtr_typ_id' AND t1.thtr_clsd=0 AND t1.thtr_nm_exp=0
          ORDER BY thtr_alph ASC, thtr_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['thtr_lctn']) {$thtr_lctn=' ('.html($row['thtr_lctn']).')';} else {$thtr_lctn='';}
      $thtr_ids[]=$row['thtr_id'];
      $thtrs[$row['thtr_id']]=array('thtr'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>'.$thtr_lctn, 'sbthtr_cnt'=>$row['sbthtr_cnt'], 'thtr_sbsq_nms'=>array(), 'thtr_prvs_nms'=>array(), 'thtr_sbthtrs'=>array());
    }

    if(!empty($thtr_ids))
    {
      foreach($thtr_ids as $thtr_id)
      {
        $sql= "SELECT thtr_sbsq_id, thtr_nm, thtr_url
              FROM thtr_aka
              INNER JOIN thtr ON thtr_sbsq_id=thtr_id
              WHERE thtr_prvs_id='$thtr_id'
              ORDER BY thtr_nm_frm_dt DESC, thtr_nm_exp_dt DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring theatre subsequently named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['thtr_sbsq_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}

        $sql= "SELECT thtr_prvs_id, thtr_nm, thtr_url
              FROM thtr_aka
              INNER JOIN thtr ON thtr_prvs_id=thtr_id
              WHERE thtr_sbsq_id='$thtr_id'
              ORDER BY thtr_nm_frm_dt DESC, thtr_nm_exp_dt DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring theatre previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['thtr_prvs_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}

        $sql= "SELECT thtr_id, sbthtr_nm, thtr_url
              FROM thtr
              INNER JOIN thtrtyp tt ON thtr_id=tt.thtrid
              WHERE srthtrid='$thtr_id' AND thtr_typid='$thtr_typ_id' AND thtr_clsd=0 AND thtr_nm_exp=0
              ORDER BY sbthtr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring sub-theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['sbthtrs'][$row['thtr_id']]=array('sbthtr'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>', 'sbthtr_sbsq_nms'=>array(), 'sbthtr_prvs_nms'=>array());}

        $sql= "SELECT t1.thtr_id, t2.sbthtr_nm, t2.thtr_url
              FROM thtr t1
              INNER JOIN thtrtyp tt ON t1.thtr_id=tt.thtrid INNER JOIN thtr_aka ON t1.thtr_id=thtr_prvs_id INNER JOIN thtr t2 ON thtr_sbsq_id=t2.thtr_id
              WHERE t1.srthtrid='$thtr_id' AND thtr_typid='$thtr_typ_id' AND t1.thtr_clsd=0 AND t1.thtr_nm_exp=0
              ORDER BY t1.sbthtr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring sub-theatre subsequently named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['sbthtrs'][$row['sbthtrid']]['sbthtr_sbsq_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}

        $sql= "SELECT t1.thtr_id, t2.sbthtr_nm, t2.thtr_url
              FROM thtr t1
              INNER JOIN thtrtyp tt ON t1.thtr_id=tt.thtrid INNER JOIN thtr_aka ON t1.thtr_id=thtr_sbsq_id INNER JOIN thtr t2 ON thtr_prvs_id=t2.thtr_id
              WHERE t1.srthtrid='$thtr_id' AND thtr_typid='$thtr_typ_id' AND t1.thtr_clsd=0 AND t1.thtr_nm_exp=0
              ORDER BY t1.sbthtr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring sub-theatre previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['sbthtrs'][$row['thtr_id']]['sbthtr_prvs_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}
      }
    }

    $sql= "SELECT thtr_id, thtr_nm, thtr_lctn, thtr_sffx_num, thtr_url, (SELECT COUNT(*) FROM thtr WHERE srthtrid=t1.thtr_id AND thtr_nm_exp=0) AS sbthtr_cnt
          FROM thtrtyp
          INNER JOIN thtr t1 ON thtrid=thtr_id
          WHERE thtr_typid='$thtr_typ_id' AND (thtr_clsd=1 OR thtr_cls_dt <= CURDATE()) AND thtr_nm_exp=0 AND srthtrid IS NULL
          UNION
          SELECT t1.thtr_id, t1.thtr_nm, t1.thtr_lctn, t1.thtr_sffx_num, t1.thtr_url, (SELECT COUNT(*) FROM thtr WHERE srthtrid=t1.thtr_id AND thtr_nm_exp=0) AS sbthtr_cnt
          FROM thtrtyp tt
          INNER JOIN thtr t1 ON tt.thtrid=t1.thtr_id INNER JOIN thtr t2 ON t1.thtr_id=t2.srthtrid
          WHERE thtr_typid='$thtr_typ_id' AND (t1.thtr_clsd=1 OR t1.thtr_cls_dt <= CURDATE()) AND t1.thtr_nm_exp=0
          ORDER BY thtr_nm ASC, thtr_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring closed theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['thtr_lctn']) {$thtr_lctn=' ('.html($row['thtr_lctn']).')';} else {$thtr_lctn='';}
      $thtr_clsd_ids[]=$row['thtr_id'];
      $thtrs_clsd[$row['thtr_id']]=array('thtr'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>'.$thtr_lctn, 'sbthtr_cnt'=>$row['sbthtr_cnt'], 'thtr_sbsq_nms'=>array(), 'thtr_prvs_nms'=>array(), 'sbthtrs'=>array());
    }

    if(!empty($thtr_clsd_ids))
    {
      foreach($thtr_clsd_ids as $thtr_clsd_id)
      {
        $sql= "SELECT thtr_prvs_id, thtr_nm, thtr_url
              FROM thtr_aka
              INNER JOIN thtr ON thtr_prvs_id=thtr_id
              WHERE thtr_sbsq_id='$thtr_clsd_id'
              ORDER BY thtr_nm_frm_dt DESC, thtr_nm_exp_dt DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring theatre previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs_clsd[$thtr_clsd_id]['thtr_prvs_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}

        $sql= "SELECT thtr_id, sbthtr_nm, thtr_url
              FROM thtr
              INNER JOIN thtrtyp tt ON thtr_id=tt.thtrid
              WHERE srthtrid='$thtr_clsd_id' AND thtr_typid='$thtr_typ_id' AND (thtr_clsd=1 OR thtr_cls_dt <= CURDATE()) AND thtr_nm_exp=0
              ORDER BY sbthtr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring closed sub-theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs_clsd[$thtr_clsd_id]['sbthtrs'][$row['thtr_id']]=array('sbthtr'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>', 'sbthtr_sbsq_nms'=>array(), 'sbthtr_prvs_nms'=>array());}

        $sql= "SELECT t1.thtr_id, t2.sbthtr_nm, t2.thtr_url
              FROM thtr t1
              INNER JOIN thtrtyp tt ON t1.thtr_id=tt.thtrid INNER JOIN thtr_aka ON t1.thtr_id=thtr_sbsq_id INNER JOIN thtr t2 ON thtr_prvs_id=t2.thtr_id
              WHERE t1.srthtrid='$thtr_clsd_id' AND thtr_typid='$thtr_typ_id' AND (t1.thtr_clsd=1 OR t1.thtr_cls_dt <= CURDATE()) AND t1.thtr_nm_exp=0
              ORDER BY t1.sbthtr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring closed sub-theatre previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs_clsd[$thtr_clsd_id]['sbthtrs'][$row['thtr_id']]['sbthtr_prvs_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}
      }
    }

    $thtr_typ_id=html($thtr_typ_id);
    include 'theatre-type.html.php';
  }
?>