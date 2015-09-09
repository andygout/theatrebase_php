<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $comp_typ_id=cln($_POST['comp_typ_id']);
    $sql="SELECT comp_typ_nm FROM comp_typ WHERE comp_typ_id='$comp_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['comp_typ_nm']);
    $pagetitle=html($row['comp_typ_nm']);
    $comp_typ_nm=html($row['comp_typ_nm']);
    $comp_typ_id=html($comp_typ_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $comp_typ_id=cln($_POST['comp_typ_id']);
    $comp_typ_nm=trim(cln($_POST['comp_typ_nm']));
    $comp_typ_url=generateurl($comp_typ_nm);
    $comp_typ_nm_session=$_POST['comp_typ_nm'];

    $errors=array();

    if(!preg_match("/\S+/", $comp_typ_nm))
    {$errors['comp_typ_nm']='**You must enter a company type name.**';}
    elseif(strlen($comp_typ_nm)>255)
    {$errors['comp_typ_nm']='</br>**Company type is allowed a maximum of 255 characters.**';}
    elseif(preg_match("/,,/", $comp_typ_nm))
    {$errors['comp_typ_nm']='**Company type name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT comp_typ_id, comp_typ_nm FROM comp_typ WHERE comp_typ_url='$comp_typ_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing company type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['comp_typ_id']!==$comp_typ_id)
      {$errors['comp_typ_url']='</br>**Duplicate URL exists for: '.html($row['comp_typ_nm']). '. You must keep the original name or assign a company type name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $comp_typ_id=cln($_POST['comp_typ_id']);
      $sql="SELECT comp_typ_nm FROM comp_typ WHERE comp_typ_id='$comp_typ_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring company type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['comp_typ_nm']);
      $pagetitle=html($row['comp_typ_nm']);
      $comp_typ_nm=$_POST['comp_typ_nm'];
      $errors['comp_typ_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $comp_typ_id=html($comp_typ_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE comp_typ SET
            comp_typ_nm='$comp_typ_nm',
            comp_typ_url='$comp_typ_url'
            WHERE comp_typ_id='$comp_typ_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating company info for submitted company type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS COMPANY TYPE HAS BEEN EDITED:'.' '.html($comp_typ_nm_session);
    header('Location: '.$comp_typ_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $comp_typ_id=cln($_POST['comp_typ_id']);

    $sql="SELECT comp_typ_nm FROM comp_typ WHERE comp_typ_id='$comp_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$pagetitle='Error'; $error='Error acquiring company type details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Delete confirmation: '.html($row['comp_typ_nm']);
    $pagetitle=html($row['comp_typ_nm']);
    $comp_typ_id=html($comp_typ_id);
    include 'delete.html.php';
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $comp_typ_id=cln($_POST['comp_typ_id']);
    $sql= "SELECT comp_typ_nm
          FROM comp_typ
          WHERE comp_typ_id='$comp_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company type details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $comp_typ_nm_session=$row['comp_typ_nm'];

    $sql="DELETE FROM comptyp WHERE comp_typid='$comp_typ_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company-type associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM comp_typ WHERE comp_typ_id='$comp_typ_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS COMPANY TYPE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($thtr_typ_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $comp_typ_id=cln($_POST['comp_typ_id']);
    $sql= "SELECT comp_typ_url
          FROM comp_typ
          WHERE comp_typ_id='$comp_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['comp_typ_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $comp_typ_url=cln($_GET['comp_typ_url']);

  $sql= "SELECT comp_typ_id
        FROM comp_typ
        WHERE comp_typ_url='$comp_typ_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $comp_typ_id=$row['comp_typ_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT comp_typ_nm FROM comp_typ WHERE comp_typ_id='$comp_typ_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['comp_typ_nm']);

    $sql= "SELECT comp_id, comp_nm, comp_url, COALESCE(comp_alph, comp_nm)comp_alph
          FROM comptyp
          INNER JOIN comp ON compid=comp_id
          WHERE comp_typid='$comp_typ_id' AND comp_dslv=0 AND comp_nm_exp=0
          ORDER BY comp_alph ASC, comp_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $comp_ids[]=$row['comp_id'];
      $comps[$row['comp_id']]=array('comp'=>'<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>', 'comp_sbsq_nms'=>array(), 'comp_prvs_nms'=>array());
    }

    if(!empty($comp_ids))
    {
      foreach($comp_ids as $comp_id)
      {
        $sql= "SELECT comp_sbsq_id, comp_nm, comp_url
              FROM comp_aka
              INNER JOIN comp ON comp_sbsq_id=comp_id
              WHERE comp_prvs_id='$comp_id'
              ORDER BY comp_nm_frm_dt DESC, comp_nm_exp_dt DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring company subsequently named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$comps[$comp_id]['comp_sbsq_nms'][]='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}

        $sql= "SELECT comp_prvs_id, comp_nm, comp_url
              FROM comp_aka
              INNER JOIN comp ON comp_prvs_id=comp_id
              WHERE comp_sbsq_id='$comp_id'
              ORDER BY comp_nm_frm_dt DESC, comp_nm_exp_dt DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring company previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$comps[$comp_id]['comp_prvs_nms'][]='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
      }
    }

    $sql= "SELECT comp_id, comp_nm, comp_url, COALESCE(comp_alph, comp_nm)comp_alph
          FROM comptyp
          INNER JOIN comp ON compid=comp_id
          WHERE comp_typid='$comp_typ_id' AND (comp_dslv=1 OR comp_dslv_dt <= CURDATE()) AND comp_nm_exp=0
          ORDER BY comp_alph ASC, comp_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $comp_dslvd_ids[]=$row['comp_id'];
      $comps_dslvd[$row['comp_id']]=array('comp_dslvd'=>'<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>', 'comp_dslvd_prvs_nms'=>array());
    }

    if(!empty($comp_dslvd_ids))
    {
      foreach($comp_dslvd_ids as $comp_dslvd_id)
      {
        $sql= "SELECT comp_prvs_id, comp_nm, comp_url
              FROM comp_aka
              INNER JOIN comp ON comp_prvs_id=comp_id
              WHERE comp_sbsq_id='$comp_dslvd_id'
              ORDER BY comp_nm_frm_dt DESC, comp_nm_exp_dt DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring dissolved company previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$comps_dslvd[$comp_dslvd_id]['comp_dslvd_prvs_nms'][]='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
      }
    }

    $comp_typ_id=html($comp_typ_id);
    include 'company-type.html.php';
  }
?>