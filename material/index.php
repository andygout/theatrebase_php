<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $mat_id=cln($_POST['mat_id']);
    $sql="SELECT mat_nm, frmt_nm, mat_sffx_num FROM mat INNER JOIN frmt ON frmtid=frmt_id WHERE mat_id='$mat_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring material details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['mat_sffx_num']) {$mat_sffx_num=html($row['mat_sffx_num']); $mat_sffx_rmn=' ('.romannumeral($row['mat_sffx_num']).')';}
    else {$mat_sffx_num=''; $mat_sffx_rmn='';}
    $pagetab='Edit: '.html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
    $pagetitle=html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
    $mat_nm=html($row['mat_nm']);
    $frmt_nm=html($row['frmt_nm']);
    $mat_id=html($mat_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $mat_id=cln($_POST['mat_id']);
    $mat_nm=trim(cln($_POST['mat_nm']));
    $frmt_nm=trim(cln($_POST['frmt_nm']));
    $mat_sffx_num=trim(cln($_POST['mat_sffx_num']));

    $errors=array();

    if(!preg_match('/\S+/', $mat_nm))
    {$errors['mat_nm']='**You must enter a material name.**';}
    elseif(preg_match('/--/', $mat_nm) || preg_match('/;;/', $mat_nm) || preg_match('/::/', $mat_nm) || preg_match('/,,/', $mat_nm))
    {$errors['mat_nm']='**Material name cannot include any of the following: [--], [;;], [::], [,,].**';}

    if(preg_match('/^[0]*$/', $mat_sffx_num) || !$mat_sffx_num)
    {$mat_sffx_num='0'; $mat_sffx_rmn=''; $mat_sffx_rmn_session='';}
    elseif(preg_match('/^[1-9][0-9]{0,1}$/', $mat_sffx_num))
    {$mat_sffx_rmn=' ('.romannumeral($mat_sffx_num).')'; $mat_sffx_rmn_session=' ('.romannumeral($_POST['mat_sffx_num']).')';}
    else
    {
      $errors['mat_sffx']='**The suffix must be a valid integer between 1 and 99 (with no leading 0) or left blank (or as 0).**';
      $mat_sffx_rmn=''; $mat_sffx_rmn_session='';
    }

    $mat_url=generateurl($mat_nm.$mat_sffx_rmn);

    if(strlen($mat_nm)>255 || strlen($mat_url)>255)
    {$errors['mat_nm_excss_lngth']='</br>**Material name and its URL are allowed a maximum of 255 characters each.**';}

    $mat_alph=alph($mat_nm);

    if(!preg_match('/\S+/', $frmt_nm))
    {$errors['frmt_nm']='**You must enter a format name.**';}
    elseif(strlen($frmt_nm)>255)
    {$errors['frmt_nm']='</br>**Format name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/--/', $frmt_nm) || preg_match('/;;/', $frmt_nm) || preg_match('/::/', $frmt_nm) || preg_match('/,,/', $frmt_nm))
    {$errors['frmt_nm']='**Format name cannot include any of the following: [--], [;;], [::], [,,].**';}
    else
    {
      $frmt_url=generateurl($frmt_nm);

      $sql="SELECT frmt_nm FROM frmt WHERE NOT EXISTS (SELECT 1 FROM frmt WHERE frmt_nm='$frmt_nm') AND frmt_url='$frmt_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing course-type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {$errors['frmt_nm']='</br>**Duplicate format URL exists. Did you mean to type: '.html($row['frmt_nm']).'?**';}
    }

    if(count($errors)==0)
    {
      $mat_nm_frmt_session=$_POST['mat_nm'].' ('.$_POST['frmt_nm'].')'.$mat_sffx_rmn_session;

      $sql="SELECT mat_id, mat_nm, mat_sffx_num, frmt_nm FROM mat INNER JOIN frmt ON frmtid=frmt_id WHERE mat_url='$mat_url' AND frmt_url='$frmt_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing material-format combination URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['mat_id']!==$mat_id)
      {
        if($row['mat_sffx_num']) {$mat_sffx_rmn=' ('.romannumeral($row['mat_sffx_num']).')';} else {$mat_sffx_rmn='';}
        $errors['mat_url']='</br>**Duplicate URL exists for: '.html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn).'. You must keep the original name and format or assign values without an existing URL.**';
      }
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $mat_id=cln($_POST['mat_id']);
      $sql="SELECT mat_nm, frmt_nm, mat_sffx_num FROM mat INNER JOIN frmt ON frmtid=frmt_id WHERE mat_id='$mat_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring material details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['mat_sffx_num']) {$mat_sffx_rmn=' ('.romannumeral($row['mat_sffx_num']).')';} else {$mat_sffx_rmn='';}
      $pagetab='Edit: '.html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
      $pagetitle=html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
      $mat_nm=$_POST['mat_nm'];
      $frmt_nm=$_POST['frmt_nm'];
      $mat_sffx_num=$_POST['mat_sffx_num'];
      $errors['mat_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $mat_id=html($mat_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT 1 FROM frmt WHERE frmt_url='$frmt_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existence of format: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)==0)
      {
        $sql="INSERT INTO frmt(frmt_nm, frmt_url) VALUES('$frmt_nm', '$frmt_url')";
        if(!mysqli_query($link, $sql)) {$error='Error adding format data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      $sql= "UPDATE mat SET
            mat_nm='$mat_nm',
            mat_alph=CASE WHEN '$mat_alph'!='' THEN '$mat_alph' END,
            mat_sffx_num='$mat_sffx_num',
            mat_url='$mat_url',
            frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$frmt_url')
            WHERE mat_id='$mat_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating material info for submitted material: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS MATERIAL HAS BEEN EDITED:'.' '.html($mat_nm_frmt_session);
    header('Location: '.$mat_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $mat_id=cln($_POST['mat_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdmat WHERE matid='$mat_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring prod-material association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production (as material)';}

    $sql="SELECT 1 FROM prdsrc_mat WHERE src_matid='$mat_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring prod-source material association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production (as source material)';}

    $sql="SELECT 1 FROM ptmat WHERE matid='$mat_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-material association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext (as material)';}

    $sql="SELECT 1 FROM ptsrc_mat WHERE src_matid='$mat_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-source material association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext (as source material)';}

    if(count($assocs)>0)
    {$errors['mat_dlt']='**Material must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT mat_nm, frmt_nm, mat_sffx_num FROM mat INNER JOIN frmt ON frmtid=frmt_id WHERE mat_id='$mat_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring material details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['mat_sffx_num']) {$mat_sffx_rmn=' ('.romannumeral($row['mat_sffx_num']).')';} else {$mat_sffx_rmn='';}
      $pagetab='Edit: '.html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
      $pagetitle=html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
      $mat_nm=$_POST['mat_nm'];
      $frmt_nm=$_POST['frmt_nm'];
      $mat_sffx_num=$_POST['mat_sffx_num'];
      $mat_id=html($mat_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT mat_nm, frmt_nm, mat_sffx_num FROM mat INNER JOIN frmt ON frmtid=frmt_id WHERE mat_id='$mat_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring material details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['mat_sffx_num']) {$mat_sffx_rmn=' ('.romannumeral($row['mat_sffx_num']).')';} else {$mat_sffx_rmn='';}
      $pagetab= 'Delete confirmation: '.html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
      $pagetitle=html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
      $mat_id=html($mat_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $mat_id=cln($_POST['mat_id']);
    $sql="SELECT mat_nm, frmt_nm, mat_sffx_num FROM mat INNER JOIN frmt ON frmtid=frmt_id WHERE mat_id='$mat_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring material details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['mat_sffx_num']) {$mat_sffx_rmn=' ('.romannumeral($row['mat_sffx_num']).')';} else {$mat_sffx_rmn='';}
    $mat_nm_frmt_session=$row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn;

    $sql="DELETE FROM prdmat WHERE matid='$mat_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdsrc_mat WHERE src_matid='$mat_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-source material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptmat WHERE matid='$mat_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsrc_mat WHERE src_matid='$mat_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-source material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM mat WHERE mat_id='$mat_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting material: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS MATERIAL HAS BEEN DELETED FROM THE DATABASE:'.' '.html($mat_nm_frmt_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $mat_id=cln($_POST['mat_id']);
    $sql="SELECT mat_url FROM mat WHERE mat_id='$mat_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring material URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['mat_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $frmt_url=cln($_GET['frmt_url']);
  $mat_url=cln($_GET['mat_url']);

  $sql="SELECT mat_id FROM mat WHERE mat_url='$mat_url' AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$frmt_url')";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $mat_id=$row['mat_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT mat_nm, mat_sffx_num, frmt_nm, frmt_url FROM mat INNER JOIN frmt ON frmtid=frmt_id WHERE mat_id='$mat_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['mat_sffx_num']) {$mat_sffx_rmn=' ('.romannumeral($row['mat_sffx_num']).')';} else {$mat_sffx_rmn='';}
    $pagetitle=html($row['mat_nm']);
    $pagetab=html($row['mat_nm'].' ('.$row['frmt_nm'].')'.$mat_sffx_rmn);
    $frmt='<a href="/material/format/'.html($row['frmt_url']).'">'.html(ucfirst($row['frmt_nm'])).'</a>';;

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdmat
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE matid='$mat_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdmat
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE matid='$mat_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $mat_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($mat_prd_ids))
      {
        foreach($mat_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdmat WHERE prdid='$prd_id' AND matid='$mat_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this material: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdmat
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE matid='$mat_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $mat_sg_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
      }

      if(!empty($mat_sg_prd_ids))
      {
        foreach($mat_sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }
      $mat_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdsrc_mat
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE src_matid='$mat_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdsrc_mat
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE src_matid='$mat_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring productions (as source material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $src_mat_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($src_mat_prd_ids))
      {
        foreach($src_mat_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdsrc_mat WHERE prdid='$prd_id' AND src_matid='$mat_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this material (as source material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdsrc_mat
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE src_matid='$mat_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring productions (as source material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $src_mat_sg_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
      }

      if(!empty($src_mat_sg_prd_ids))
      {
        foreach($src_mat_sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }
      $src_mat_prds=$prds;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, p2.pt_coll,
          COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptmat
          INNER JOIN pt p1 ON ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE matid='$mat_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, pt_coll,
          COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptmat pm
          INNER JOIN pt p1 ON ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE matid='$mat_id' AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext data (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $mat_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($mat_pt_ids))
      {
        foreach($mat_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptmat WHERE ptid='$pt_id' AND matid='$mat_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this material: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM ptmat pm
            INNER JOIN pt ON ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE matid='$mat_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $mat_sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
      }

      if(!empty($mat_sg_pt_ids))
      {
        foreach($mat_sg_pt_ids as $sg_pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
        }
      }
      $mat_pts=$pts;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptsrc_mat psm
          INNER JOIN pt p1 ON ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE src_matid='$mat_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptsrc_mat psm
          INNER JOIN pt p1 ON ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE src_matid='$mat_id' AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext data (as source material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $src_mat_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($src_mat_pt_ids))
      {
        foreach($src_mat_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptsrc_mat WHERE ptid='$pt_id' AND src_matid='$mat_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this material (as source material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, pt_coll
            FROM ptsrc_mat psm
            INNER JOIN pt ON ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE src_matid='$mat_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data (as source material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $src_mat_sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
      }

      if(!empty($src_mat_sg_pt_ids))
      {
        foreach($src_mat_sg_pt_ids as $sg_pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
        }
      }
      $src_mat_pts=$pts;
    }

    $mat_id=html($mat_id);
    include 'material.html.php';
  }
?>