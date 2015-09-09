<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prof_id=cln($_POST['prof_id']);
    $sql="SELECT prof_nm FROM prof WHERE prof_id='$prof_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring profession details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['prof_nm']);
    $pagetitle=html($row['prof_nm']);
    $prof_nm=html($row['prof_nm']);

    $sql="SELECT prof_nm FROM rel_prof INNER JOIN prof ON rel_prof2=prof_id WHERE rel_prof1='$prof_id' ORDER BY rel_prof_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_profs[]=$row['prof_nm'];}
    if(!empty($rel_profs)) {$rel_prof_list=html(implode(',,', $rel_profs));} else {$rel_prof_list='';}

    $prof_id=html($prof_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $prof_id=cln($_POST['prof_id']);
    $prof_nm=trim(cln($_POST['prof_nm']));
    $rel_prof_list=cln($_POST['rel_prof_list']);
    $prof_url=generateurl($prof_nm);
    $prof_nm_session=$_POST['prof_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $prof_nm))
    {$errors['prof_nm']='**You must enter an profession name.**';}
    elseif(strlen($prof_nm)>255)
    {$errors['prof_nm']='</br>**Profession name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $prof_nm))
    {$errors['prof_nm']='**Profession name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT prof_id, prof_nm FROM prof WHERE prof_url='$prof_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing profession URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['prof_id']!==$prof_id)
      {$errors['prof_url']='</br>**Duplicate URL exists for: '.html($row['prof_nm']). '. You must keep the original name or assign an profession name without an existing URL.**';}
    }

    if(preg_match('/\S+/', $rel_prof_list))
    {
      $rel_prof_nms=explode(',,', $_POST['rel_prof_list']);
      if(count($rel_prof_nms)>250)
      {$errors['rel_prof_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rel_prof_empty_err_arr=array(); $rel_prof_dplct_arr=array(); $rel_prof_url_err_arr=array();
        $rel_prof_inv_comb_err_arr=array();
        foreach($rel_prof_nms as $rel_prof_nm)
        {
          $rel_prof_errors=0;

          $rel_prof_nm=trim($rel_prof_nm);
          if(!preg_match('/\S+/', $rel_prof_nm))
          {
            $rel_prof_errors++; $rel_prof_empty_err_arr[]=$rel_prof_nm;
            if(count($rel_prof_empty_err_arr)==1) {$errors['rel_prof_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rel_prof_empty']='</br>**There are '.count($rel_prof_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $rel_prof_url=generateurl($rel_prof_nm);

            $rel_prof_dplct_arr[]=$rel_prof_url;
            if(count(array_unique($rel_prof_dplct_arr))<count($rel_prof_dplct_arr))
            {$errors['rel_prof_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($rel_prof_nm)>255 || strlen($rel_prof_url)>255)
            {$rel_prof_errors++; $errors['rel_prof_nm_excss_lngth']='</br>**Profession name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($rel_prof_errors==0)
            {
              $rel_prof_nm_cln=cln($rel_prof_nm);
              $rel_prof_url_cln=cln($rel_prof_url);

              $sql="SELECT prof_nm FROM prof WHERE NOT EXISTS (SELECT 1 FROM prof WHERE prof_nm='$rel_prof_nm_cln') AND prof_url='$rel_prof_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing profession URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $rel_prof_url_err_arr[]=$row['prof_nm'];
                if(count($rel_prof_url_err_arr)==1)
                {$errors['rel_prof_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $rel_prof_url_err_arr)).'?**';}
                else
                {$errors['rel_prof_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $rel_prof_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT prof_id FROM prof WHERE prof_url='$rel_prof_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing profession URL (for existing profession check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($row['prof_id']==$prof_id)
                {$errors['rel_prof_id_mtch']='</br>**You cannot assign this profession as a related profession of itself: '.html($rel_prof_nm).'.**';}
                else
                {
                  $rel_prof_id=$row['prof_id'];
                  $sql="SELECT 1 FROM rel_prof WHERE rel_prof2='$prof_id' AND rel_prof1='$rel_prof_id'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for inverse of proposed combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $rel_prof_inv_comb_err_arr[]=$rel_prof_nm;
                    $errors['rel_prof_inv_comb']='</br>**The following locations cause an invalid inverse of existing profession-relationship combinations: '.html(implode(' / ', $rel_prof_inv_comb_err_arr)).'.**';
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

      $prof_id=cln($_POST['prof_id']);
      $sql="SELECT prof_nm FROM prof WHERE prof_id='$prof_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring profession details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['prof_nm']);
      $pagetitle=html($row['prof_nm']);
      $prof_nm=$_POST['prof_nm'];
      $rel_prof_list=$_POST['rel_prof_list'];
      $errors['prof_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $prof_id=html($prof_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE prof SET
            prof_nm='$prof_nm',
            prof_url='$prof_url'
            WHERE prof_id='$prof_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted profession: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM rel_prof WHERE rel_prof1='$prof_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting profession-related profession associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $rel_prof_list))
      {
        $rel_prof_nms=explode(',,', $rel_prof_list);
        $n=0;
        foreach($rel_prof_nms as $rel_prof_nm)
        {
          $rel_prof_ordr=++$n;
          $rel_prof_url=generateurl($rel_prof_nm);

          $sql="SELECT 1 FROM prof WHERE prof_url='$rel_prof_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of profession: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO prof(prof_nm, prof_url)
                  VALUES('$rel_prof_nm', '$rel_prof_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO rel_prof(rel_prof_ordr, rel_prof1, rel_prof2)
                SELECT '$rel_prof_ordr', '$prof_id', prof_id FROM prof WHERE prof_url='$rel_prof_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding profession-related profession association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PROFESSION HAS BEEN EDITED:'.' '.html($prof_nm_session);
    header('Location: '.$prof_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $prof_id=cln($_POST['prof_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM charprof WHERE profid='$prof_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring character-profession association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Character';}

    if(count($assocs)>0)
    {$errors['prof_dlt']='**Profession must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT prof_nm FROM prof WHERE prof_id='$prof_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring profession details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['prof_nm']);
      $pagetitle=html($row['prof_nm']);
      $prof_nm=$_POST['prof_nm'];
      $rel_prof_list=$_POST['rel_prof_list'];
      $prof_id=html($prof_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT prof_nm FROM prof WHERE prof_id='$prof_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['prof_nm']);
      $pagetitle=html($row['prof_nm']);
      $prof_id=html($prof_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prof_id=cln($_POST['prof_id']);
    $sql="SELECT prof_nm FROM prof WHERE prof_id='$prof_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring profession details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $prof_nm_session=$row['prof_nm'];

    $sql="DELETE FROM charprof WHERE profid='$prof_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting profession-character associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM rel_prof WHERE rel_prof1='$prof_id' OR rel_prof2='$prof_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting profession-related profession associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prof WHERE prof_id='$prof_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting profession: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PROFESSION HAS BEEN DELETED FROM THE DATABASE:'.' '.html($prof_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prof_id=cln($_POST['prof_id']);
    $sql="SELECT prof_url FROM prof WHERE prof_id='$prof_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring profession URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['prof_url']);
    exit();
  }
?>