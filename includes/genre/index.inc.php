<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $gnr_id=cln($_POST['gnr_id']);
    $sql="SELECT gnr_nm FROM gnr WHERE gnr_id='$gnr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring genre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['gnr_nm']);
    $pagetitle=html($row['gnr_nm']);
    $gnr_nm=html($row['gnr_nm']);

    $sql="SELECT gnr_nm FROM rel_gnr INNER JOIN gnr ON rel_gnr2=gnr_id WHERE rel_gnr1='$gnr_id' ORDER BY rel_gnr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_gnrs[]=$row['gnr_nm'];}
    if(!empty($rel_gnrs)) {$rel_gnr_list=html(implode(',,', $rel_gnrs));} else {$rel_gnr_list='';}

    $sql="SELECT gnr_nm FROM rel_gnr INNER JOIN gnr ON rel_gnr1=gnr_id WHERE rel_gnr2='$gnr_id' ORDER BY gnr_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related genre (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_gnrs1[]=html($row['gnr_nm']);}

    $gnr_id=html($gnr_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $gnr_id=cln($_POST['gnr_id']);
    $gnr_nm=trim(cln($_POST['gnr_nm']));
    $rel_gnr_list=cln($_POST['rel_gnr_list']);
    $gnr_url=generateurl($gnr_nm);
    $gnr_nm_session=$_POST['gnr_nm'];
    $errors=array();

    if(!preg_match('/\S+/', $gnr_nm))
    {$errors['gnr_nm']='**You must enter a genre name.**';}
    elseif(strlen($gnr_nm)>255)
    {$errors['gnr_nm']='</br>**Genre name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $gnr_nm))
    {$errors['gnr_nm']='**Genre name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT gnr_id, gnr_nm FROM gnr WHERE gnr_url='$gnr_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing genre URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['gnr_id']!==$gnr_id)
      {$errors['gnr_url']='</br>**Duplicate URL exists for: '.html($row['gnr_nm']). '. You must keep the original name or assign a genre name without an existing URL.**';}
    }

    if(preg_match('/\S+/', $rel_gnr_list))
    {
      $rel_gnr_nms=explode(',,', $_POST['rel_gnr_list']);
      if(count($rel_gnr_nms)>250)
      {$errors['rel_gnr_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rel_gnr_empty_err_arr=array(); $rel_gnr_dplct_arr=array(); $rel_gnr_url_err_arr=array();
        $rel_gnr_inv_comb_err_arr=array();
        foreach($rel_gnr_nms as $rel_gnr_nm)
        {
          $rel_gnr_errors=0;

          $rel_gnr_nm=trim($rel_gnr_nm);
          if(!preg_match('/\S+/', $rel_gnr_nm))
          {
            $rel_gnr_errors++; $rel_gnr_empty_err_arr[]=$rel_gnr_nm;
            if(count($rel_gnr_empty_err_arr)==1) {$errors['rel_gnr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rel_gnr_empty']='</br>**There are '.count($rel_gnr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $rel_gnr_url=generateurl($rel_gnr_nm);

            $rel_gnr_dplct_arr[]=$rel_gnr_url;
            if(count(array_unique($rel_gnr_dplct_arr))<count($rel_gnr_dplct_arr))
            {$errors['rel_gnr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($rel_gnr_nm)>255 || strlen($rel_gnr_url)>255)
            {$rel_gnr_errors++; $errors['rel_gnr_nm_excss_lngth']='</br>**Genre name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($rel_gnr_errors==0)
            {
              $rel_gnr_nm_cln=cln($rel_gnr_nm);
              $rel_gnr_url_cln=cln($rel_gnr_url);

              $sql="SELECT gnr_nm FROM gnr WHERE NOT EXISTS (SELECT 1 FROM gnr WHERE gnr_nm='$rel_gnr_nm_cln') AND gnr_url='$rel_gnr_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing genre URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $rel_gnr_url_err_arr[]=$row['gnr_nm'];
                if(count($rel_gnr_url_err_arr)==1)
                {$errors['rel_gnr_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $rel_gnr_url_err_arr)).'?**';}
                else
                {$errors['rel_gnr_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $rel_gnr_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT gnr_id FROM gnr WHERE gnr_url='$rel_gnr_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing genre URL (for existing genre check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($row['gnr_id']==$gnr_id)
                {$errors['rel_gnr_id_mtch']='</br>**You cannot assign this genre as a related genre of itself: '.html($rel_gnr_nm).'.**';}
                else
                {
                  $rel_gnr_id=$row['gnr_id'];
                  $sql="SELECT 1 FROM rel_gnr WHERE rel_gnr2='$gnr_id' AND rel_gnr1='$rel_gnr_id'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for inverse of proposed combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $rel_gnr_inv_comb_err_arr[]=$rel_gnr_nm;
                    $errors['rel_gnr_inv_comb']='</br>**The following locations cause an invalid inverse of existing genre-relationship combinations: '.html(implode(' / ', $rel_gnr_inv_comb_err_arr)).'.**';
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

      $gnr_id=cln($_POST['gnr_id']);
      $sql="SELECT gnr_nm FROM gnr WHERE gnr_id='$gnr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring genre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['gnr_nm']);
      $pagetitle=html($row['gnr_nm']);
      $gnr_nm=$_POST['gnr_nm'];
      $rel_gnr_list=$_POST['rel_gnr_list'];
      $errors['gnr_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $gnr_id=html($gnr_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE gnr SET
            gnr_nm='$gnr_nm',
            gnr_url='$gnr_url'
            WHERE gnr_id='$gnr_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted genre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM rel_gnr WHERE rel_gnr1='$gnr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting genre-related genre associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $rel_gnr_list))
      {
        $rel_gnr_nms=explode(',,', $rel_gnr_list);
        $n=0;
        foreach($rel_gnr_nms as $rel_gnr_nm)
        {
          $rel_gnr_ordr=++$n;
          $rel_gnr_url=generateurl($rel_gnr_nm);

          $sql="SELECT 1 FROM gnr WHERE gnr_url='$rel_gnr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of genre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql="INSERT INTO gnr(gnr_nm, gnr_url) VALUES('$rel_gnr_nm', '$rel_gnr_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO rel_gnr(rel_gnr_ordr, rel_gnr1, rel_gnr2)
                SELECT '$rel_gnr_ordr', '$gnr_id', gnr_id FROM gnr WHERE gnr_url='$rel_gnr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding genre-related genre association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS GENRE HAS BEEN EDITED:'.' '.html($gnr_nm_session);
    header('Location: '.$gnr_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $gnr_id=cln($_POST['gnr_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdgnr WHERE gnrid='$gnr_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production-genre association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM ptgnr WHERE gnrid='$gnr_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext-genre association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    $sql="SELECT 1 FROM rel_gnr WHERE rel_gnr2='$gnr_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring genre (related genre)-genre association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Related genre';}

    if(count($assocs)>0)
    {$errors['gnr_dlt']='**Genre must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT gnr_nm FROM gnr WHERE gnr_id='$gnr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring genre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['gnr_nm']);
      $pagetitle=html($row['gnr_nm']);
      $gnr_nm=$_POST['gnr_nm'];
      $rel_gnr_list=$_POST['rel_gnr_list'];
      $gnr_id=html($gnr_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT gnr_nm FROM gnr WHERE gnr_id='$gnr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['gnr_nm']);
      $pagetitle=html($row['gnr_nm']);
      $gnr_id=html($gnr_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $gnr_id=cln($_POST['gnr_id']);
    $sql="SELECT gnr_nm FROM gnr WHERE gnr_id='$gnr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring genre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $gnr_nm_session=$row['gnr_nm'];

    $sql="DELETE FROM prdgnr WHERE gnrid='$gnr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting genre-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptgnr WHERE gnrid='$gnr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting genre-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM rel_gnr WHERE rel_gnr1='$gnr_id' OR rel_gnr2='$gnr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting genre-related genre associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM gnr WHERE gnr_id='$gnr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting genre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS GENRE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($gnr_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $gnr_id=cln($_POST['gnr_id']);
    $sql="SELECT gnr_url FROM gnr WHERE gnr_id='$gnr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring genre URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['gnr_url']);
    exit();
  }
?>