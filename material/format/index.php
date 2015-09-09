<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $frmt_id=cln($_POST['frmt_id']);
    $sql="SELECT frmt_nm FROM frmt WHERE frmt_id='$frmt_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring format details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html(ucfirst($row['frmt_nm']));
    $pagetitle=html(ucfirst($row['frmt_nm']));
    $frmt_nm=html($row['frmt_nm']);
    $frmt_id=html($frmt_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $frmt_id=cln($_POST['frmt_id']);
    $frmt_nm=trim(cln($_POST['frmt_nm']));
    $frmt_url=generateurl($frmt_nm);
    $frmt_nm_session=$_POST['frmt_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $frmt_nm))
    {$errors['frmt_nm']='**You must enter a format name.**';}
    if(strlen($frmt_nm)>255)
    {$errors['frmt_nm']='</br>**Format name is allowed a maximum of 255 characters.**';}
    else
    {
      $sql="SELECT frmt_id, frmt_nm FROM frmt WHERE frmt_url='$frmt_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing format URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['frmt_id']!==$frmt_id)
      {$errors['frmt_url']='</br>**Duplicate URL exists for: '.html($row['frmt_nm']). '. You must keep the original name or assign a format name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $frmt_id=cln($_POST['frmt_id']);
      $sql="SELECT frmt_nm FROM frmt WHERE frmt_id='$frmt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring format details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html(ucfirst($row['frmt_nm']));
      $pagetitle=html(ucfirst($row['frmt_nm']));
      $frmt_nm=$_POST['frmt_nm'];
      $errors['frmt_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $frmt_id=html($frmt_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE frmt SET
            frmt_nm='$frmt_nm',
            frmt_url='$frmt_url'
            WHERE frmt_id='$frmt_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating format info for submitted format: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS FORMAT HAS BEEN EDITED:'.' '.html($frmt_nm_session);
    header('Location: '.$frmt_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $frmt_id=cln($_POST['frmt_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM mat WHERE frmtid='$frmt_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring material-format association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Material';}

    if(count($assocs)>0)
    {$errors['frmt_dlt']='**Format must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT frmt_nm FROM frmt WHERE frmt_id='$frmt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring format details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html(ucfirst($row['frmt_nm']));
      $pagetitle=html(ucfirst($row['frmt_nm']));
      $frmt_nm=$_POST['frmt_nm'];
      $frmt_id=html($frmt_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT frmt_nm FROM frmt WHERE frmt_id='$frmt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error';
      $error='Error acquiring format details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab= 'Delete confirmation: '.html(ucfirst($row['frmt_nm']));
      $pagetitle=html(ucfirst($row['frmt_nm']));
      $frmt_id=html($frmt_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $frmt_id=cln($_POST['frmt_id']);
    $sql="SELECT frmt_nm FROM frmt WHERE frmt_id='$frmt_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring format details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $frmt_nm_session=$row['frmt_nm'];

    $sql="UPDATE mat SET frmtid=NULL WHERE frmtid='$frmt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting format-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM frmt WHERE frmt_id='$frmt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting format: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS FORMAT HAS BEEN DELETED FROM THE DATABASE:'.' '.html($frmt_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $frmt_id=cln($_POST['frmt_id']);
    $sql="SELECT frmt_url FROM frmt WHERE frmt_id='$frmt_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring format URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['frmt_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $frmt_url=cln($_GET['frmt_url']);

  $sql="SELECT frmt_id FROM frmt WHERE frmt_url='$frmt_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $frmt_id=$row['frmt_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT frmt_nm FROM frmt WHERE frmt_id='$frmt_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring format data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html(ucfirst($row['frmt_nm']));

    $sql="SELECT mat_nm, mat_url, frmt_url, COALESCE(mat_alph, mat_nm)mat_alph FROM frmt INNER JOIN mat ON frmt_id=frmtid WHERE frmt_id='$frmt_id' ORDER BY mat_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring materials: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$mats[]='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['mat_nm']);}

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM mat
          INNER JOIN prdsrc_mat psm ON mat_id=psm.src_matid INNER JOIN prd p1 ON psm.prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id
          INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE frmtid='$frmt_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM mat
          INNER JOIN prdsrc_mat psm ON mat_id=psm.src_matid INNER JOIN prd p1 ON psm.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE frmtid='$frmt_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production data (for source material productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
          $sql="SELECT 1 FROM prdsrc_mat INNER JOIN mat ON src_matid=mat_id WHERE prdid='$prd_id' AND frmtid='$frmt_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this format (using such format as source material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM mat
            INNER JOIN prdsrc_mat psm ON mat_id=psm.src_matid INNER JOIN prd ON psm.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE frmtid='$frmt_id' AND coll_ov IS NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment production data (for source material productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }
      $src_mat_prds=$prds;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM mat
          INNER JOIN ptsrc_mat psm ON mat_id=psm.src_matid INNER JOIN pt p1 ON psm.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE frmtid='$frmt_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM mat
          INNER JOIN ptsrc_mat psm ON mat_id=psm.src_matid INNER JOIN pt p1 ON psm.ptid=pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE frmtid='$frmt_id' AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring source material playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
          $sql="SELECT 1 FROM ptsrc_mat INNER JOIN mat ON src_matid=mat_id WHERE ptid='$pt_id' AND frmtid='$frmt_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this format (using such format as source material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM mat
            INNER JOIN ptsrc_mat psm ON mat_id=psm.src_matid INNER JOIN pt ON psm.ptid=pt_id
            LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE frmtid='$frmt_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data (for source material playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $src_mat_sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
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

    $frmt_id=html($frmt_id);
    include 'material-format.html.php';
  }
?>