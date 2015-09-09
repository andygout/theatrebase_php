<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $plc_id=cln($_POST['plc_id']);
    $sql="SELECT plc_nm FROM plc WHERE plc_id='$plc_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring place details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html(ucfirst($row['plc_nm']));
    $pagetitle=html(ucfirst($row['plc_nm']));
    $plc_nm=html($row['plc_nm']);

    $sql="SELECT plc_nm, plc_typ_rel FROM rel_plc INNER JOIN plc ON rel_plc2=plc_id WHERE rel_plc1='$plc_id' ORDER BY rel_plc_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related place data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['plc_typ_rel']) {$plc_typ_rel='*';} else {$plc_typ_rel='';}
      $rel_plcs[]=$row['plc_nm'].$plc_typ_rel;
    }
    if(!empty($rel_plcs)) {$rel_plc_list=html(implode(',,', $rel_plcs));} else {$rel_plc_list='';}

    $sql="SELECT plc_nm FROM rel_plc INNER JOIN plc ON rel_plc1=plc_id WHERE rel_plc2='$plc_id' ORDER BY plc_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related place (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_plcs1[]=html(ucfirst($row['plc_nm']));}

    $textarea='';
    $plc_id=html($plc_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $plc_id=cln($_POST['plc_id']);
    $plc_nm=trim(cln($_POST['plc_nm']));
    $rel_plc_list=cln($_POST['rel_plc_list']);

    $plc_url=generateurl($plc_nm);
    $plc_nm_session=$_POST['plc_nm'];
    $errors=array();

    if(!preg_match('/\S+/', $plc_nm))
    {$errors['plc_nm']='**You must enter a place name.**';}
    elseif(strlen($plc_nm)>255)
    {$errors['plc_nm']='</br>**Setting (place) name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $plc_nm) || preg_match('/##/', $plc_nm) || preg_match('/\+\+/', $plc_nm) || preg_match('/::/', $plc_nm)
    || preg_match('/;;/', $plc_nm))
    {$errors['plc_nm']='</br>**Place cannot include any of the following: [,,], [##], [++], [::], [;;].**';}
    else
    {
      $sql="SELECT plc_id, plc_nm FROM plc WHERE plc_url='$plc_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing place URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['plc_id']!==$plc_id)
      {$errors['plc_url']='</br>**Duplicate URL exists for: '.html($row['plc_nm']). '. You must keep the original name or assign a place name without an existing URL.**';}
    }

    if(preg_match('/\S+/', $rel_plc_list))
    {
      $rel_plc_nms=explode(',,', $_POST['rel_plc_list']);
      if(count($rel_plc_nms)>250)
      {$errors['rel_plc_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rel_plc_empty_err_arr=array(); $rel_plc_dplct_arr=array(); $rel_plc_url_err_arr=array();
        $rel_plc_inv_comb_err_arr=array();
        foreach($rel_plc_nms as $rel_plc_nm)
        {
          $rel_plc_errors=0;

          $rel_plc_nm=trim($rel_plc_nm);
          if(!preg_match('/\S+/', $rel_plc_nm))
          {
            $rel_plc_errors++; $rel_plc_empty_err_arr[]=$rel_plc_nm;
            if(count($rel_plc_empty_err_arr)==1) {$errors['rel_plc_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rel_plc_empty']='</br>**There are '.count($rel_plc_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(preg_match('/^\S+.*\*$/', $rel_plc_nm)) {$rel_plc_nm=preg_replace('/(\S+.*)(\*)/', '$1', $rel_plc_nm); $plc_typ_rel='1'; $rel_plc_nm=trim($rel_plc_nm);}
            else {$plc_typ_rel='0';}

            $rel_plc_url=generateurl($rel_plc_nm);

            $rel_plc_dplct_arr[]=$rel_plc_url;
            if(count(array_unique($rel_plc_dplct_arr))<count($rel_plc_dplct_arr))
            {$errors['rel_plc_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($rel_plc_nm)>255 || strlen($rel_plc_url)>255)
            {$rel_plc_errors++; $errors['rel_plc_nm_excss_lngth']='</br>**Place name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($rel_plc_errors==0)
            {
              $rel_plc_nm_cln=cln($rel_plc_nm);
              $rel_plc_url_cln=cln($rel_plc_url);

              $sql="SELECT plc_nm FROM plc WHERE NOT EXISTS (SELECT 1 FROM plc WHERE plc_nm='$rel_plc_nm_cln') AND plc_url='$rel_plc_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing place URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $rel_plc_url_err_arr[]=$row['plc_nm'];
                if(count($rel_plc_url_err_arr)==1)
                {$errors['rel_plc_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $rel_plc_url_err_arr)).'?**';}
                else
                {$errors['rel_plc_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $rel_plc_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT plc_id FROM plc WHERE plc_url='$rel_plc_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing place URL (for existing place check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($row['plc_id']==$plc_id)
                {$errors['rel_plc_id_mtch']='</br>**You cannot assign this place as a related place of itself: '.html($rel_plc_nm).'.**';}
                else
                {
                  $rel_plc_id=$row['plc_id'];
                  $sql="SELECT 1 FROM rel_plc WHERE rel_plc2='$plc_id' AND rel_plc1='$rel_plc_id'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for inverse of proposed combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $rel_plc_inv_comb_err_arr[]=$rel_plc_nm;
                    $errors['rel_plc_inv_comb']='</br>**The following places cause an invalid inverse of existing place-relationship combinations: '.html(implode(' / ', $rel_plc_inv_comb_err_arr)).'.**';
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

      $plc_id=cln($_POST['plc_id']);
      $sql="SELECT plc_nm FROM plc WHERE plc_id='$plc_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring place details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['plc_nm']);
      $pagetitle=html($row['plc_nm']);
      $plc_nm=$_POST['plc_nm'];
      $rel_plc_list=$_POST['rel_plc_list'];
      $textarea=$_POST['textarea'];
      $errors['plc_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $plc_id=html($plc_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE plc SET
            plc_nm='$plc_nm',
            plc_url='$plc_url'
            WHERE plc_id='$plc_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating place info for submitted place: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM rel_plc WHERE rel_plc1='$plc_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting place-related place associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $rel_plc_list))
      {
        $rel_plc_nms=explode(',,', $rel_plc_list);
        $n=0;
        foreach($rel_plc_nms as $rel_plc_nm)
        {
          $rel_plc_ordr=++$n;

          if(preg_match('/^\S+.*\*$/', $rel_plc_nm)) {$rel_plc_nm=preg_replace('/(\S+.*)(\*)/', '$1', $rel_plc_nm); $plc_typ_rel='1'; $rel_plc_nm=trim($rel_plc_nm);}
          else {$plc_typ_rel='0';}

          $rel_plc_url=generateurl($rel_plc_nm);

          $sql="SELECT 1 FROM plc WHERE plc_url='$rel_plc_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of place: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO plc(plc_nm, plc_url)
                  VALUES('$rel_plc_nm', '$rel_plc_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding place data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO rel_plc(rel_plc_ordr, plc_typ_rel, rel_plc1, rel_plc2)
                SELECT '$rel_plc_ordr', '$plc_typ_rel', '$plc_id', plc_id FROM plc WHERE plc_url='$rel_plc_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding place-related place association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PLACE HAS BEEN EDITED:'.' '.html($plc_nm_session);
    header('Location: '.$plc_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $plc_id=cln($_POST['plc_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdsttng_plc WHERE sttng_plcid='$plc_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-setting (place) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM ptsttng_plc WHERE sttng_plcid='$plc_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-setting (place) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    $sql="SELECT 1 FROM rel_plc WHERE rel_plc2='$plc_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring place (related place)-place association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Related place';}

    if(count($assocs)>0)
    {$errors['plc_dlt']='**Place must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT plc_nm
            FROM plc
            WHERE plc_id='$plc_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error';
      $error='Error acquiring place details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html(ucfirst($row['plc_nm']));
      $pagetitle=html(ucfirst($row['plc_nm']));
      $plc_nm=$_POST['plc_nm'];
      $rel_plc_list=$_POST['rel_plc_list'];
      $textarea=$_POST['textarea'];
      $plc_id=html($plc_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT plc_nm
            FROM plc
            WHERE plc_id='$plc_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html(ucfirst($row['plc_nm']));
      $pagetitle=html(ucfirst($row['plc_nm']));
      $plc_id=html($plc_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $plc_id=cln($_POST['plc_id']);
    $sql= "SELECT plc_nm
          FROM plc
          WHERE plc_id='$plc_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring place details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $plc_nm_session=$row['plc_nm'];

    $sql="DELETE FROM prdsttng_plc WHERE sttng_plcid='$plc_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting setting (place)-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng_plc WHERE sttng_plcid='$plc_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting setting (place)-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM rel_plc WHERE rel_plc1='$plc_id' OR rel_plc2='$plc_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting place-related place associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM plc WHERE plc_id='$plc_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting place: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PLACE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($plc_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $plc_id=cln($_POST['plc_id']);
    $sql= "SELECT plc_url
          FROM plc
          WHERE plc_id='$plc_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring place URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['plc_url']);
    exit();
  }
?>