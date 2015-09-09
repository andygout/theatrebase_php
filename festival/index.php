<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $fstvl_id=cln($_POST['fstvl_id']);
    $sql="SELECT fstvl_nm FROM fstvl WHERE fstvl_id='$fstvl_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring festival details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['fstvl_nm']);
    $pagetitle=html($row['fstvl_nm']);
    $fstvl_nm=html($row['fstvl_nm']);
    $fstvl_id=html($fstvl_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $fstvl_id=cln($_POST['fstvl_id']);
    $fstvl_nm=trim(cln($_POST['fstvl_nm']));
    $fstvl_url=generateurl($fstvl_nm);
    $fstvl_nm_session=$_POST['fstvl_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $fstvl_nm))
    {$errors['fstvl_nm']='**You must enter a festival name.**';}
    elseif(strlen($fstvl_nm)>255)
    {$errors['fstvl_nm']='</br>**Festival name is allowed a maximum of 255 characters.**';}
    else
    {
      $fstvl_alph=alph($fstvl_nm);

      $sql="SELECT fstvl_id, fstvl_nm FROM fstvl WHERE fstvl_url='$fstvl_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing festival URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['fstvl_id']!==$fstvl_id)
      {$errors['fstvl_url']='</br>**Duplicate URL exists for: '.html($row['fstvl_nm']). '. You must keep the original name or assign a festival name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $fstvl_id=cln($_POST['fstvl_id']);
      $sql="SELECT fstvl_nm FROM fstvl WHERE fstvl_id='$fstvl_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring festival details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['fstvl_nm']);
      $pagetitle=html($row['fstvl_nm']);
      $fstvl_nm=$_POST['fstvl_nm'];
      $errors['fstvl_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $fstvl_id=html($fstvl_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE fstvl SET
            fstvl_nm='$fstvl_nm',
            fstvl_alph=CASE WHEN '$fstvl_alph'!='' THEN '$fstvl_alph' END,
            fstvl_url='$fstvl_url'
            WHERE fstvl_id='$fstvl_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating festival info for submitted festival: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS FESTIVAL HAS BEEN EDITED:'.' '.html($fstvl_nm_session);
    header('Location: '.$fstvl_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $fstvl_id=cln($_POST['fstvl_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prd WHERE fstvlid='$fstvl_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring prod-festival association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    if(count($assocs)>0)
    {$errors['fstvl_dlt']='**Festival must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT fstvl_nm FROM fstvl WHERE fstvl_id='$fstvl_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring festival details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['fstvl_nm']);
      $pagetitle=html($row['fstvl_nm']);
      $fstvl_nm=$_POST['fstvl_nm'];
      $fstvl_id=html($fstvl_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT fstvl_nm FROM fstvl WHERE fstvl_id='$fstvl_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring festival details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab= 'Delete confirmation: '.html($row['fstvl_nm']);
      $pagetitle=html($row['fstvl_nm']);
      $fstvl_id=html($fstvl_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $fstvl_id=cln($_POST['fstvl_id']);
    $sql="SELECT fstvl_nm FROM fstvl WHERE fstvl_id='$fstvl_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring festival details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $fstvl_nm_session=$row['fstvl_nm'];

    $sql="DELETE FROM prdfstvl WHERE fstvlid='$fstvl_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting festival-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM fstvl WHERE fstvl_id='$fstvl_id'";
    if(!mysqli_query($link, $sql))
    {$error='Error deleting festival: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS FESTIVAL HAS BEEN DELETED FROM THE DATABASE:'.' '.html($fstvl_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $fstvl_id=cln($_POST['fstvl_id']);
    $sql="SELECT fstvl_url FROM fstvl WHERE fstvl_id='$fstvl_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring festival URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['fstvl_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $fstvl_url=cln($_GET['fstvl_url']);

  $sql="SELECT fstvl_id FROM fstvl WHERE fstvl_url='$fstvl_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $fstvl_id=$row['fstvl_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT fstvl_nm FROM fstvl WHERE fstvl_id='$fstvl_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring festival data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['fstvl_nm']);

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdfstvl
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE fstvlid='$fstvl_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdfstvl
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE fstvlid='$fstvl_id' AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM prdfstvl WHERE prdid='$prd_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this festival: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv_tr.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdfstvl
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE fstvlid='$fstvl_id' AND coll_ov IS NOT NULL
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

    $fstvl_id=html($fstvl_id);
    include 'festival.html.php';
  }
?>