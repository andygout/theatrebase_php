<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thm_id=cln($_POST['thm_id']);
    $sql="SELECT thm_nm FROM thm WHERE thm_id='$thm_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theme details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['thm_nm']);
    $pagetitle=html($row['thm_nm']);
    $thm_nm=html($row['thm_nm']);

    $sql="SELECT thm_nm FROM rel_thm INNER JOIN thm ON rel_thm2=thm_id WHERE rel_thm1='$thm_id' ORDER BY rel_thm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_thms[]=$row['thm_nm'];}
    if(!empty($rel_thms)) {$rel_thm_list=html(implode(',,', $rel_thms));} else {$rel_thm_list='';}

    $sql="SELECT thm_nm FROM rel_thm INNER JOIN thm ON rel_thm1=thm_id WHERE rel_thm2='$thm_id' ORDER BY thm_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related theme (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_thms1[]=html($row['thm_nm']);}

    $thm_id=html($thm_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thm_id=cln($_POST['thm_id']);
    $thm_nm=trim(cln($_POST['thm_nm']));
    $rel_thm_list=cln($_POST['rel_thm_list']);

    $thm_url=generateurl($thm_nm);

    $thm_nm_session=$_POST['thm_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $thm_nm))
    {$errors['thm_nm']='**You must enter a theme name.**';}
    if(strlen($thm_nm)>255)
    {$errors['thm_nm']='</br>**Theme name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $thm_nm))
    {$errors['thm_nm']='**Theme name cannot include the following: [,,].**';}
    else
    {
      $thm_alph=alph($thm_nm);

      $sql="SELECT thm_id, thm_nm FROM thm WHERE thm_url='$thm_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing theme URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['thm_id']!==$thm_id)
      {$errors['thm_url']='</br>**Duplicate URL exists for: '.html($row['thm_nm']). '. You must keep the original name or assign a theme name without an existing URL.**';}
    }

    if(preg_match('/\S+/', $rel_thm_list))
    {
      $rel_thm_nms=explode(',,', $_POST['rel_thm_list']);
      if(count($rel_thm_nms)>250)
      {$errors['rel_thm_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rel_thm_empty_err_arr=array(); $rel_thm_dplct_arr=array(); $rel_thm_url_err_arr=array();
        $rel_thm_inv_comb_err_arr=array();
        foreach($rel_thm_nms as $rel_thm_nm)
        {
          $rel_thm_errors=0;

          $rel_thm_nm=trim($rel_thm_nm);
          if(!preg_match('/\S+/', $rel_thm_nm))
          {
            $rel_thm_errors++; $rel_thm_empty_err_arr[]=$rel_thm_nm;
            if(count($rel_thm_empty_err_arr)==1) {$errors['rel_thm_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rel_thm_empty']='</br>**There are '.count($rel_thm_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $rel_thm_url=generateurl($rel_thm_nm);

            $rel_thm_dplct_arr[]=$rel_thm_url;
            if(count(array_unique($rel_thm_dplct_arr))<count($rel_thm_dplct_arr))
            {$errors['rel_thm_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($rel_thm_nm)>255 || strlen($rel_thm_url)>255)
            {$rel_thm_errors++; $errors['rel_thm_nm_excss_lngth']='</br>**Theme name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($rel_thm_errors==0)
            {
              $rel_thm_nm_cln=cln($rel_thm_nm);
              $rel_thm_url_cln=cln($rel_thm_url);

              $sql="SELECT thm_nm FROM thm WHERE NOT EXISTS (SELECT 1 FROM thm WHERE thm_nm='$rel_thm_nm_cln') AND thm_url='$rel_thm_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing theme URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $rel_thm_url_err_arr[]=$row['thm_nm'];
                if(count($rel_thm_url_err_arr)==1)
                {$errors['rel_thm_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $rel_thm_url_err_arr)).'?**';}
                else
                {$errors['rel_thm_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $rel_thm_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT thm_id FROM thm WHERE thm_url='$rel_thm_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing theme URL (for existing theme check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($row['thm_id']==$thm_id)
                {$errors['rel_thm_id_mtch']='</br>**You cannot assign this theme as a related theme of itself: '.html($rel_thm_nm).'.**';}
                else
                {
                  $rel_thm_id=$row['thm_id'];
                  $sql="SELECT 1 FROM rel_thm WHERE rel_thm2='$thm_id' AND rel_thm1='$rel_thm_id'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for inverse of proposed combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $rel_thm_inv_comb_err_arr[]=$rel_thm_nm;
                    $errors['rel_thm_inv_comb']='</br>**The following locations cause an invalid inverse of existing theme-relationship combinations: '.html(implode(' / ', $rel_thm_inv_comb_err_arr)).'.**';
                  }
                }
              }
            }
          }
        }
      }
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $thm_id=cln($_POST['thm_id']);
      $sql= "SELECT thm_nm
            FROM thm
            WHERE thm_id='$thm_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring theme details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['thm_nm']);
      $pagetitle=html($row['thm_nm']);
      $thm_nm=$_POST['thm_nm'];
      $rel_thm_list=$_POST['rel_thm_list'];
      $errors['thm_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $thm_id=html($thm_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE thm SET
            thm_nm='$thm_nm',
            thm_alph=CASE WHEN '$thm_alph'!='' THEN '$thm_alph' END,
            thm_url='$thm_url'
            WHERE thm_id='$thm_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted theme: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM rel_thm WHERE rel_thm1='$thm_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting theme-related theme associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $rel_thm_list))
      {
        $rel_thm_nms=explode(',,', $rel_thm_list);
        $n=0;
        foreach($rel_thm_nms as $rel_thm_nm)
        {
          $rel_thm_ordr=++$n;
          $rel_thm_url=generateurl($rel_thm_nm);
          $rel_thm_alph=alph($rel_thm_nm);

          $sql="SELECT 1 FROM thm WHERE thm_url='$rel_thm_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of theme: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO thm(thm_nm, thm_alph, thm_url)
                  VALUES('$rel_thm_nm', CASE WHEN '$rel_thm_alph'!='' THEN '$rel_thm_alph' END, '$rel_thm_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO rel_thm(rel_thm_ordr, rel_thm1, rel_thm2)
                SELECT '$rel_thm_ordr', '$thm_id', thm_id FROM thm WHERE thm_url='$rel_thm_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding theme-related theme association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS THEME HAS BEEN EDITED:'.' '.html($thm_nm_session);
    header('Location: '.$thm_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $thm_id=cln($_POST['thm_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdthm WHERE thmid='$thm_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-theme association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM ptthm WHERE thmid='$thm_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-theme association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    $sql="SELECT 1 FROM rel_thm WHERE rel_thm2='$thm_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring theme (related theme)-theme association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Related theme';}

    if(count($assocs)>0)
    {$errors['thm_dlt']='**Theme must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT thm_nm
            FROM thm
            WHERE thm_id='$thm_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring theme details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['thm_nm']);
      $pagetitle=html($row['thm_nm']);
      $thm_nm=$_POST['thm_nm'];
      $rel_thm_list=$_POST['rel_thm_list'];
      $thm_id=html($thm_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT thm_nm
            FROM thm
            WHERE thm_id='$thm_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['thm_nm']);
      $pagetitle=html($row['thm_nm']);
      $thm_id=html($thm_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thm_id=cln($_POST['thm_id']);
    $sql= "SELECT thm_nm
          FROM thm
          WHERE thm_id='$thm_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theme details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $thm_nm_session=$row['thm_nm'];

    $sql="DELETE FROM prdthm WHERE thmid='$thm_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theme-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptthm WHERE thmid='$thm_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theme-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM rel_thm WHERE rel_thm1='$thm_id' OR rel_thm2='$thm_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theme-related theme associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thm WHERE thm_id='$thm_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theme: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS THEME HAS BEEN DELETED FROM THE DATABASE:'.' '.html($thm_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thm_id=cln($_POST['thm_id']);
    $sql= "SELECT thm_url
          FROM thm
          WHERE thm_id='$thm_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theme URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['thm_url']);
    exit();
  }
?>