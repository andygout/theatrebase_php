<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ethn_id=cln($_POST['ethn_id']);
    $sql="SELECT ethn_nm FROM ethn WHERE ethn_id='$ethn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ethnicity details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['ethn_nm']);
    $pagetitle=html($row['ethn_nm']);
    $ethn_nm=html($row['ethn_nm']);

    $sql="SELECT ethn_nm FROM rel_ethn INNER JOIN ethn ON rel_ethn2=ethn_id WHERE rel_ethn1='$ethn_id' ORDER BY rel_ethn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_ethns[]=$row['ethn_nm'];}
    if(!empty($rel_ethns)) {$rel_ethn_list=html(implode(',,', $rel_ethns));} else {$rel_ethn_list='';}

    $ethn_id=html($ethn_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $ethn_id=cln($_POST['ethn_id']);
    $ethn_nm=trim(cln($_POST['ethn_nm']));
    $rel_ethn_list=cln($_POST['rel_ethn_list']);
    $ethn_url=generateurl($ethn_nm);
    $ethn_nm_session=$_POST['ethn_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $ethn_nm))
    {$errors['ethn_nm']='**You must enter an ethnicity name.**';}
    elseif(strlen($ethn_nm)>255)
    {$errors['ethn_nm']='</br>**Ethnicity name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $ethn_nm))
    {$errors['ethn_nm']='**Ethnicity name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT ethn_id, ethn_nm FROM ethn WHERE ethn_url='$ethn_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing ethnicity URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['ethn_id']!==$ethn_id)
      {$errors['ethn_url']='</br>**Duplicate URL exists for: '.html($row['ethn_nm']). '. You must keep the original name or assign an ethnicity name without an existing URL.**';}
    }

    if(preg_match('/\S+/', $rel_ethn_list))
    {
      $rel_ethn_nms=explode(',,', $_POST['rel_ethn_list']);
      if(count($rel_ethn_nms)>250)
      {$errors['rel_ethn_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rel_ethn_empty_err_arr=array(); $rel_ethn_dplct_arr=array(); $rel_ethn_url_err_arr=array();
        $rel_ethn_inv_comb_err_arr=array();
        foreach($rel_ethn_nms as $rel_ethn_nm)
        {
          $rel_ethn_errors=0;

          $rel_ethn_nm=trim($rel_ethn_nm);
          if(!preg_match('/\S+/', $rel_ethn_nm))
          {
            $rel_ethn_errors++; $rel_ethn_empty_err_arr[]=$rel_ethn_nm;
            if(count($rel_ethn_empty_err_arr)==1) {$errors['rel_ethn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rel_ethn_empty']='</br>**There are '.count($rel_ethn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $rel_ethn_url=generateurl($rel_ethn_nm);

            $rel_ethn_dplct_arr[]=$rel_ethn_url;
            if(count(array_unique($rel_ethn_dplct_arr))<count($rel_ethn_dplct_arr))
            {$errors['rel_ethn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($rel_ethn_nm)>255 || strlen($rel_ethn_url)>255)
            {$rel_ethn_errors++; $errors['rel_ethn_nm_excss_lngth']='</br>**Ethnicity name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($rel_ethn_errors==0)
            {
              $rel_ethn_nm_cln=cln($rel_ethn_nm);
              $rel_ethn_url_cln=cln($rel_ethn_url);

              $sql="SELECT ethn_nm FROM ethn WHERE NOT EXISTS (SELECT 1 FROM ethn WHERE ethn_nm='$rel_ethn_nm_cln') AND ethn_url='$rel_ethn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing ethnicity URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $rel_ethn_url_err_arr[]=$row['ethn_nm'];
                if(count($rel_ethn_url_err_arr)==1)
                {$errors['rel_ethn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $rel_ethn_url_err_arr)).'?**';}
                else
                {$errors['rel_ethn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $rel_ethn_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT ethn_id FROM ethn WHERE ethn_url='$rel_ethn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing ethnicity URL (for existing ethnicity check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($row['ethn_id']==$ethn_id)
                {$errors['rel_ethn_id_mtch']='</br>**You cannot assign this ethnicity as a related ethnicity of itself: '.html($rel_ethn_nm).'.**';}
                else
                {
                  $rel_ethn_id=$row['ethn_id'];
                  $sql="SELECT 1 FROM rel_ethn WHERE rel_ethn2='$ethn_id' AND rel_ethn1='$rel_ethn_id'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for inverse of proposed combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $rel_ethn_inv_comb_err_arr[]=$rel_ethn_nm;
                    $errors['rel_ethn_inv_comb']='</br>**The following locations cause an invalid inverse of existing ethnicity-relationship combinations: '.html(implode(' / ', $rel_ethn_inv_comb_err_arr)).'.**';
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

      $ethn_id=cln($_POST['ethn_id']);
      $sql="SELECT ethn_nm FROM ethn WHERE ethn_id='$ethn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring ethnicity details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['ethn_nm']);
      $pagetitle=html($row['ethn_nm']);
      $ethn_nm=$_POST['ethn_nm'];
      $rel_ethn_list=$_POST['rel_ethn_list'];
      $errors['ethn_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $ethn_id=html($ethn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE ethn SET
            ethn_nm='$ethn_nm',
            ethn_url='$ethn_url'
            WHERE ethn_id='$ethn_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted ethnicity: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM rel_ethn WHERE rel_ethn1='$ethn_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting ethnicity-related ethnicity associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $rel_ethn_list))
      {
        $rel_ethn_nms=explode(',,', $rel_ethn_list);
        $n=0;
        foreach($rel_ethn_nms as $rel_ethn_nm)
        {
          $rel_ethn_ordr=++$n;
          $rel_ethn_url=generateurl($rel_ethn_nm);

          $sql="SELECT 1 FROM ethn WHERE ethn_url='$rel_ethn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of ethnicity: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql="INSERT INTO ethn(ethn_nm, ethn_url) VALUES('$rel_ethn_nm', '$rel_ethn_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO rel_ethn(rel_ethn_ordr, rel_ethn1, rel_ethn2)
                SELECT '$rel_ethn_ordr', '$ethn_id', ethn_id FROM ethn WHERE ethn_url='$rel_ethn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding ethnicity-related ethnicity association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS ETHNICITY HAS BEEN EDITED:'.' '.html($ethn_nm_session);
    header('Location: '.$ethn_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $ethn_id=cln($_POST['ethn_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prsn WHERE ethnid='$ethn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring person-ethnicity association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Person';}

    $sql="SELECT 1 FROM charethn WHERE ethnid='$ethn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring character-ethnicity association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Character';}

    if(count($assocs)>0)
    {$errors['ethn_dlt']='**Ethnicity must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT ethn_nm
            FROM ethn
            WHERE ethn_id='$ethn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring ethnicity details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['ethn_nm']);
      $pagetitle=html($row['ethn_nm']);
      $ethn_nm=$_POST['ethn_nm'];
      $rel_ethn_list=$_POST['rel_ethn_list'];
      $ethn_id=html($ethn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT ethn_nm FROM ethn WHERE ethn_id='$ethn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['ethn_nm']);
      $pagetitle=html($row['ethn_nm']);
      $ethn_id=html($ethn_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ethn_id=cln($_POST['ethn_id']);
    $sql="SELECT ethn_nm FROM ethn WHERE ethn_id='$ethn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ethnicity details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $ethn_nm_session=$row['ethn_nm'];

    $sql="UPDATE prsn SET ethnid=NULL WHERE ethnid='$ethn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error nullifying ethnicity-person associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charethn WHERE ethnid='$ethn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting ethnicity-character associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM rel_ethn WHERE rel_ethn1='$ethn_id' OR rel_ethn2='$ethn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting ethnicity-related ethnicity associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ethn WHERE ethn_id='$ethn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting ethnicity: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS ETHNICITY HAS BEEN DELETED FROM THE DATABASE:'.' '.html($ethn_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');e.
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $ethn_id=cln($_POST['ethn_id']);
    $sql="SELECT ethn_url FROM ethn WHERE ethn_id='$ethn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ethnicity URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['ethn_url']);
    exit();
  }
?>