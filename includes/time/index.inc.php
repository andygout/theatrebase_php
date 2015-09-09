<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $tm_id=cln($_POST['tm_id']);
    $sql="SELECT tm_nm, tm_frm_dt, tm_frm_dt_bce, tm_to_dt, tm_to_dt_bce, tm_rcr FROM tm WHERE tm_id='$tm_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting (time) details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['tm_nm']);
    $pagetitle=html($row['tm_nm']);
    $tm_nm=html($row['tm_nm']);
    $tm_frm_dt=html($row['tm_frm_dt']);
    $tm_frm_dt_bce=$row['tm_frm_dt_bce'];
    $tm_to_dt=html($row['tm_to_dt']);
    $tm_to_dt_bce=$row['tm_to_dt_bce'];
    $tm_rcr=$row['tm_rcr'];

    $sql="SELECT tm_nm FROM rel_tm INNER JOIN tm ON rel_tm2=tm_id WHERE rel_tm1='$tm_id' ORDER BY rel_tm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_tms[]=$row['tm_nm'];}
    if(!empty($rel_tms)) {$rel_tm_list=html(implode(',,', $rel_tms));} else {$rel_tm_list='';}

    $sql= "SELECT tm_nm, tm_url, COALESCE(tm_alph, tm_nm)tm_alph FROM rel_tm INNER JOIN tm ON rel_tm1=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NULL ORDER BY tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (BCE with no date): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while ($row=mysqli_fetch_array($result))
    {$rel_tms1[]=html($row['tm_nm']);}

    $sql= "SELECT tm_nm, tm_url, DATE_FORMAT(tm_frm_dt, '%Y') AS tm_frm_dt_YYYY, DATE_FORMAT(tm_frm_dt, '%m%d') AS tm_frm_dt_MMDD,
          DATE_FORMAT(tm_to_dt, '%Y') AS tm_to_dt_YYYY, DATE_FORMAT(tm_to_dt, '%m%d') AS tm_to_dt_MMDD, COALESCE(tm_alph, tm_nm)tm_alph
          FROM rel_tm
          INNER JOIN tm ON rel_tm1=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce=1 AND tm_frm_dt IS NOT NULL
          ORDER BY tm_frm_dt_YYYY DESC, tm_frm_dt_MMDD ASC, tm_to_dt_YYYY ASC, tm_to_dt_MMDD DESC, tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (BCE with date): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while ($row=mysqli_fetch_array($result))
    {$rel_tms1[]=html($row['tm_nm']);}

    $sql= "SELECT tm_nm, tm_url, COALESCE(tm_alph, tm_nm)tm_alph FROM rel_tm INNER JOIN tm ON rel_tm1=tm_id
          WHERE rel_tm2='$tm_id' AND tm_frm_dt_bce!=1 ORDER BY tm_frm_dt ASC, tm_to_dt DESC, tm_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related time (comprised of) data (all CE): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while ($row=mysqli_fetch_array($result))
    {$rel_tms1[]=html($row['tm_nm']);}

    $textarea='';
    $tm_id=html($tm_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $tm_id=cln($_POST['tm_id']);
    $tm_nm=trim(cln($_POST['tm_nm']));
    $tm_frm_dt=cln($_POST['tm_frm_dt']);
    if(isset($_POST['tm_frm_dt_bce'])) {$tm_frm_dt_bce='1';} else {$tm_frm_dt_bce='0';}
    $tm_to_dt=cln($_POST['tm_to_dt']);
    if(isset($_POST['tm_to_dt_bce'])) {$tm_to_dt_bce='1';} else {$tm_to_dt_bce='0';}
    if(isset($_POST['tm_rcr'])) {$tm_rcr='1';} else {$tm_rcr='0';}
    $rel_tm_list=cln($_POST['rel_tm_list']);

    $tm_url=generateurl($tm_nm);
    $tm_nm_session=$_POST['tm_nm'];
    $errors=array();

    if(!preg_match('/\S+/', $tm_nm))
    {$errors['tm_nm']='**You must enter a setting (time) name.**';}
    elseif(strlen($tm_nm)>255)
    {$errors['tm_nm']='</br>**Setting (time) name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $tm_nm) || preg_match('/##/', $tm_nm) || preg_match('/\+\+/', $tm_nm) || preg_match('/::/', $tm_nm)
    || preg_match('/;;/', $tm_nm))
    {$errors['tm_nm']='**Setting (time) name cannot include the following: [,,], [##], [++], [::], [;;].**';}
    else
    {
      $tm_alph=alph($tm_nm);

      $sql="SELECT tm_id, tm_nm FROM tm WHERE tm_url='$tm_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing setting (time) URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['tm_id']!==$tm_id)
      {$errors['tm_url']='</br>**Duplicate URL exists for: '.html($row['tm_nm']). '. You must keep the original name or assign a setting (time) name without an existing URL.**';}
    }

    if($tm_frm_dt)
    {
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $tm_frm_dt))
      {$errors['tm_frm_dt']='**You must enter a valid FROM date in the prescribed format or leave empty.**'; $tm_frm_dt=NULL;}
      else
      {
        list($tm_frm_dt_YYYY, $tm_frm_dt_MM, $tm_frm_dt_DD)=explode('-', $tm_frm_dt);
        if(!checkdate((int)$tm_frm_dt_MM, (int)$tm_frm_dt_DD, (int)$tm_frm_dt_YYYY)) {$errors['tm_frm_dt']='**You must enter a valid FROM date or leave empty.**'; $tm_frm_dt=NULL;}
        elseif($tm_rcr) {$errors['tm_rcr']='**FROM and TO dates must be left empty if this box is checked.**'; $tm_frm_dt=NULL;}
        else {if(!$tm_frm_dt_bce) {$tm_frm_dt_num=$tm_frm_dt;} else {$tm_frm_dt_num='-'.$tm_frm_dt;}}
      }
    }
    else
    {$tm_frm_dt=NULL;}

    if($tm_to_dt)
    {
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $tm_to_dt))
      {$errors['tm_to_dt']='**You must enter a valid TO date in the prescribed format or leave empty.**'; $tm_to_dt=NULL;}
      else
      {
        list($tm_to_dt_YYYY, $tm_to_dt_MM, $tm_to_dt_DD)=explode('-', $tm_to_dt);
        if(!checkdate((int)$tm_to_dt_MM, (int)$tm_to_dt_DD, (int)$tm_to_dt_YYYY)) {$errors['tm_to_dt']='**You must enter a valid TO date or leave empty.**'; $tm_to_dt=NULL;}
        elseif($tm_rcr) {$errors['tm_rcr']='**FROM and TO dates must be left empty if this box is checked.**'; $tm_to_dt=NULL;}
        else {if(!$tm_to_dt_bce) {$tm_to_dt_num=$tm_to_dt;} else {$tm_to_dt_num='-'.$tm_to_dt;}}
      }
    }
    else
    {$tm_to_dt=NULL;}

    if($tm_frm_dt && $tm_to_dt)
    {
      if(!$tm_frm_dt_bce && !$tm_to_dt_bce)
      {if($tm_frm_dt>$tm_to_dt) {$errors['tm_frm_dt']='**Must be earlier than the FROM date.**'; $errors['tm_to_dt']='**Must be later than the TO date.**';}}
      elseif(!$tm_frm_dt_bce && $tm_to_dt_bce)
      {$errors['tm_frm_dt']='**Must be earlier than the FROM date.**'; $errors['tm_to_dt']='**Must be later than the TO date.**';}
      elseif($tm_frm_dt_bce && $tm_to_dt_bce)
      {
        if($tm_frm_dt_YYYY<$tm_to_dt_YYYY) {$errors['tm_frm_dt']='**Must be earlier than the FROM date.**'; $errors['tm_to_dt']='**Must be later than the TO date.**';}
        elseif($tm_frm_dt_YYYY==$tm_to_dt_YYYY && $tm_frm_dt_MM.$tm_frm_dt_DD>$tm_to_dt_MM.$tm_to_dt_DD) {$errors['tm_frm_dt']='**Must be earlier than the FROM date.**'; $errors['tm_to_dt']='**Must be later than the TO date.**';}
      }
    }
    elseif($tm_frm_dt && !$tm_to_dt || !$tm_frm_dt && $tm_to_dt)
    {$errors['tm_frm_dt']='**FROM and TO date must both have date or both be left empty.**'; $errors['tm_to_dt']='**FROM and TO date must both have date or both be left empty.**';}
    else
    {if(!$tm_frm_dt_bce && $tm_to_dt_bce) {$errors['tm_frm_dt']='**Must be earlier than the FROM date.**'; $errors['tm_to_dt']='**Must be later than the TO date.**';}}

    if(preg_match('/\S+/', $rel_tm_list))
    {
      $rel_tm_nms=explode(',,', $_POST['rel_tm_list']);
      if(count($rel_tm_nms)>250)
      {$errors['rel_tm_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rel_tm_empty_err_arr=array(); $rel_tm_dplct_arr=array(); $rel_tm_url_err_arr=array();
        $rel_tm_inv_comb_err_arr=array(); $rel_tm_dt_mtch_err_arr=array();
        foreach($rel_tm_nms as $rel_tm_nm)
        {
          $rel_tm_errors=0;

          $rel_tm_nm=trim($rel_tm_nm);
          if(!preg_match('/\S+/', $rel_tm_nm))
          {
            $rel_tm_errors++; $rel_tm_empty_err_arr[]=$rel_tm_nm;
            if(count($rel_tm_empty_err_arr)==1) {$errors['rel_tm_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rel_tm_empty']='</br>**There are '.count($rel_tm_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $rel_tm_url=generateurl($rel_tm_nm);

            $rel_tm_dplct_arr[]=$rel_tm_url;
            if(count(array_unique($rel_tm_dplct_arr))<count($rel_tm_dplct_arr))
            {$errors['rel_tm_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($rel_tm_nm)>255 || strlen($rel_tm_url)>255)
            {$rel_tm_errors++; $errors['rel_tm_nm_excss_lngth']='</br>**Time name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($rel_tm_errors==0)
            {
              $rel_tm_nm_cln=cln($rel_tm_nm);
              $rel_tm_url_cln=cln($rel_tm_url);

              $sql= "SELECT tm_nm
                    FROM tm
                    WHERE NOT EXISTS (SELECT 1 FROM tm WHERE tm_nm='$rel_tm_nm_cln')
                    AND tm_url='$rel_tm_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing time URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $rel_tm_url_err_arr[]=$row['tm_nm'];
                if(count($rel_tm_url_err_arr)==1)
                {$errors['rel_tm_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $rel_tm_url_err_arr)).'?**';}
                else
                {$errors['rel_tm_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $rel_tm_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT tm_id, tm_nm, tm_url, tm_frm_dt, tm_frm_dt_bce, tm_to_dt, tm_to_dt_bce FROM tm WHERE tm_url='$rel_tm_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing time URL (for existing time check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                $rel_tm_url_lnk='<a href="'.html($row['tm_url']).'" target="'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';
                if(!$row['tm_frm_dt_bce']) {$rel_tm_frm_dt=$row['tm_frm_dt'];} else {$rel_tm_frm_dt='-'.$row['tm_frm_dt'];}
                if(!$row['tm_to_dt_bce']) {$rel_tm_to_dt=$row['tm_to_dt'];} else {$rel_tm_to_dt='-'.$row['tm_to_dt'];}
                if($row['tm_id']==$tm_id)
                {$errors['rel_tm_id_mtch']='</br>**You cannot assign this time as a related time of itself: '.html($rel_tm_nm).'.**';}
                else
                {
                  $rel_tm_id=$row['tm_id'];
                  $sql="SELECT 1 FROM rel_tm WHERE rel_tm2='$tm_id' AND rel_tm1='$rel_tm_id'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for inverse of proposed combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {$rel_tm_inv_comb_err_arr[]=$rel_tm_nm; $errors['rel_tm_inv_comb']='</br>**The following places cause an invalid inverse of existing time-relationship combinations: '.html(implode(' / ', $rel_tm_inv_comb_err_arr)).'.**';}
                  elseif(($rel_tm_frm_dt && $tm_frm_dt_num && $rel_tm_frm_dt>$tm_frm_dt_num) || ($rel_tm_frm_dt && $tm_to_dt_num && $rel_tm_frm_dt>$tm_to_dt_num) || ($rel_tm_to_dt && $tm_frm_dt_num && $rel_tm_to_dt<$tm_frm_dt_num) || ($rel_tm_to_dt && $tm_to_dt_num && $rel_tm_to_dt<$tm_to_dt_num))
                  {$rel_tm_dt_mtch_err_arr[]=$rel_tm_url_lnk; $errors['rel_tm_dt_mtch']='</br>**Related times must have a wider span of dates than this time. Please amend these dates or those of: '.implode(' / ', $rel_tm_dt_mtch_err_arr).'**';}
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

      $tm_id=cln($_POST['tm_id']);
      $sql= "SELECT tm_nm
            FROM tm
            WHERE tm_id='$tm_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring setting (time) details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['tm_nm']);
      $pagetitle=html($row['tm_nm']);
      $tm_nm=$_POST['tm_nm'];
      $tm_frm_dt=$_POST['tm_frm_dt'];
      $tm_to_dt=$_POST['tm_to_dt'];
      $rel_tm_list=$_POST['rel_tm_list'];
      $textarea=$_POST['textarea'];
      $errors['tm_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $tm_id=html($tm_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE tm SET
            tm_nm='$tm_nm',
            tm_alph=CASE WHEN '$tm_alph'!='' THEN '$tm_alph' END,
            tm_url='$tm_url',
            tm_frm_dt=CASE WHEN '$tm_frm_dt'!='' THEN '$tm_frm_dt' END,
            tm_frm_dt_bce=CASE WHEN '$tm_frm_dt'!='' THEN '$tm_frm_dt_bce' END,
            tm_to_dt=CASE WHEN '$tm_to_dt'!='' THEN '$tm_to_dt' END,
            tm_to_dt_bce=CASE WHEN '$tm_to_dt'!='' THEN '$tm_to_dt_bce' END,
            tm_rcr='$tm_rcr'
            WHERE tm_id='$tm_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted setting (time): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM rel_tm WHERE rel_tm1='$tm_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting time-related time associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $rel_tm_list))
      {
        $rel_tm_nms=explode(',,', $rel_tm_list);
        $n=0;
        foreach($rel_tm_nms as $rel_tm_nm)
        {
          $rel_tm_ordr=++$n;
          $rel_tm_url=generateurl($rel_tm_nm);
          $rel_tm_alph=alph($rel_tm_nm);

          $sql="SELECT 1 FROM tm WHERE tm_url='$rel_tm_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of time: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO tm(tm_nm, tm_alph, tm_url, tm_frm_dt_bce, tm_to_dt_bce, tm_rcr)
                  VALUES('$rel_tm_nm', CASE WHEN '$rel_tm_alph'!='' THEN '$rel_tm_alph' END, '$rel_tm_url', 0, 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding time data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql="INSERT INTO rel_tm(rel_tm_ordr, rel_tm1, rel_tm2)
              SELECT '$rel_tm_ordr', '$tm_id', tm_id FROM tm WHERE tm_url='$rel_tm_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding time-related time association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS SETTING (TIME) HAS BEEN EDITED:'.' '.html($tm_nm_session);
    header('Location: '.$tm_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $tm_id=cln($_POST['tm_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdsttng_tm WHERE sttng_tmid='$tm_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-setting (time) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM ptsttng_tm WHERE sttng_tmid='$tm_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-setting (time) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    $sql="SELECT 1 FROM rel_tm WHERE rel_tm2='$tm_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring time (related time)-time association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Related time';}

    if(count($assocs)>0)
    {$errors['tm_dlt']='**Setting (time) must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT tm_nm, tm_url FROM tm WHERE tm_id='$tm_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring setting (time) details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['tm_nm']);
      $pagetitle=html($row['tm_nm']);
      $tm_nm=$_POST['tm_nm'];
      $tm_frm_dt=$_POST['tm_frm_dt'];
      $tm_to_dt=$_POST['tm_to_dt'];
      if(isset($_POST['tm_frm_dt_bce'])) {$tm_frm_dt_bce='1';} else {$tm_frm_dt_bce='0';}
      if(isset($_POST['tm_to_dt_bce'])) {$tm_to_dt_bce='1';} else {$tm_to_dt_bce='0';}
      if(isset($_POST['tm_rcr'])) {$tm_rcr='1';} else {$tm_rcr='0';}
      $rel_tm_list=$_POST['rel_tm_list'];
      $textarea=$_POST['textarea'];
      $tm_id=html($tm_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT tm_nm FROM tm WHERE tm_id='$tm_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['tm_nm']);
      $pagetitle=html($row['tm_nm']);
      $tm_id=html($tm_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $tm_id=cln($_POST['tm_id']);
    $sql="SELECT tm_nm FROM tm WHERE tm_id='$tm_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting (time) details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $tm_nm_session=$row['tm_nm'];

    $sql="DELETE FROM prdsttng_tm WHERE sttng_tmid='$tm_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting setting (time)-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng_tm WHERE sttng_tmid='$tm_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting setting (time)-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM rel_tm WHERE rel_tm1='$tm_id' OR rel_tm2='$tm_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting time-related time associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM tm WHERE tm_id='$tm_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting setting (time): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS SETTING (TIME) HAS BEEN DELETED FROM THE DATABASE:'.' '.html($tm_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $tm_id=cln($_POST['tm_id']);
    $sql="SELECT tm_url FROM tm WHERE tm_id='$tm_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting (time) URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['tm_url']);
    exit();
  }
?>