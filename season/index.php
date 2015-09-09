<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ssn_id=cln($_POST['ssn_id']);
    $sql="SELECT ssn_nm FROM ssn WHERE ssn_id='$ssn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring season details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['ssn_nm']);
    $pagetitle=html($row['ssn_nm']);
    $ssn_nm=html($row['ssn_nm']);
    $ssn_id=html($ssn_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $ssn_id=cln($_POST['ssn_id']);
    $ssn_nm=trim(cln($_POST['ssn_nm']));
    $ssn_url=generateurl($ssn_nm);
    $ssn_nm_session=$_POST['ssn_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $ssn_nm))
    {$errors['ssn_nm']='**You must enter a season name.**';}
    if(strlen($ssn_nm)>255)
    {$errors['ssn_nm']='</br>**Season name is allowed a maximum of 255 characters.**';}
    else
    {
      $ssn_alph=alph($ssn_nm);

      $sql="SELECT ssn_id, ssn_nm FROM ssn WHERE ssn_url='$ssn_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing season URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['ssn_id']!==$ssn_id)
      {$errors['ssn_url']='</br>**Duplicate URL exists for: '.html($row['ssn_nm']). '. You must keep the original name or assign a season name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $ssn_id=cln($_POST['ssn_id']);
      $sql="SELECT ssn_nm FROM ssn WHERE ssn_id='$ssn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring season details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['ssn_nm']);
      $pagetitle=html($row['ssn_nm']);
      $ssn_nm=$_POST['ssn_nm'];
      $errors['ssn_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $ssn_id=html($ssn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE ssn SET
            ssn_nm='$ssn_nm',
            ssn_alph=CASE WHEN '$ssn_alph'!='' THEN '$ssn_alph' END,
            ssn_url='$ssn_url'
            WHERE ssn_id='$ssn_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating season info for submitted season: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS SEASON HAS BEEN EDITED:'.' '.html($ssn_nm_session);
    header('Location: '.$ssn_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $ssn_id=cln($_POST['ssn_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prd WHERE ssnid='$ssn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring prod-season association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    if(count($assocs)>0)
    {$errors['ssn_dlt']='**Season must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT ssn_nm FROM ssn WHERE ssn_id='$ssn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring season details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['ssn_nm']);
      $pagetitle=html($row['ssn_nm']);
      $ssn_nm=$_POST['ssn_nm'];
      $ssn_id=html($ssn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT ssn_nm FROM ssn WHERE ssn_id='$ssn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error';
      $error='Error acquiring season details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab= 'Delete confirmation: '.html($row['ssn_nm']);
      $pagetitle=html($row['ssn_nm']);
      $ssn_id=html($ssn_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ssn_id=cln($_POST['ssn_id']);
    $sql="SELECT ssn_nm FROM ssn WHERE ssn_id='$ssn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring season details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $ssn_nm_session=$row['ssn_nm'];

    $sql="DELETE FROM prdssn WHERE ssnid='$ssn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting season-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ssn WHERE ssn_id='$ssn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting season: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS SEASON HAS BEEN DELETED FROM THE DATABASE:'.' '.html($ssn_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ssn_id=cln($_POST['ssn_id']);
    $sql="SELECT ssn_url FROM ssn WHERE ssn_id='$ssn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring season URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['ssn_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $ssn_url=cln($_GET['ssn_url']);

  $sql="SELECT ssn_id FROM ssn WHERE ssn_url='$ssn_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $ssn_id=$row['ssn_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT ssn_nm FROM ssn WHERE ssn_id='$ssn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring season data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['ssn_nm']);

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdssn
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE ssnid='$ssn_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdssn
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE ssnid='$ssn_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdssn WHERE prdid='$prd_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this season: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv_tr.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdssn
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE ssnid='$ssn_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $sg_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
      }

      if(!empty($sg_prd_ids))
      {
        foreach($sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv_tr.inc.php';
        }
      }
    }

    $ssn_id=html($ssn_id);
    include 'season.html.php';
  }
?>