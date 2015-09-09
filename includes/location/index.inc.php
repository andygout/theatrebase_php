<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $lctn_id=cln($_POST['lctn_id']);
    $sql= "SELECT lctn_nm, lctn_sffx_num, lctn_est_dt_c, lctn_est_dt, lctn_est_dt_bce, lctn_est_dt_frmt, lctn_exp_dt_c, lctn_exp_dt, lctn_exp_dt_bce, lctn_exp_dt_frmt, lctn_exp, lctn_fctn
          FROM lctn
          WHERE lctn_id='$lctn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring location details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['lctn_sffx_num']) {$lctn_sffx_num=html($row['lctn_sffx_num']); $lctn_sffx_rmn=' ('.romannumeral($row['lctn_sffx_num']).')';}
    else {$lctn_sffx_num=''; $lctn_sffx_rmn='';}
    $pagetab='Edit: '.html($row['lctn_nm'].$lctn_sffx_rmn);
    $pagetitle=html($row['lctn_nm'].$lctn_sffx_rmn);
    $lctn_nm=html($row['lctn_nm']);
    $lctn_est_dt_c=$row['lctn_est_dt_c'];
    $lctn_est_dt=html($row['lctn_est_dt']);
    $lctn_est_dt_bce=$row['lctn_est_dt_bce'];
    $lctn_est_dt_frmt=html($row['lctn_est_dt_frmt']);
    $lctn_exp_dt_c=$row['lctn_exp_dt_c'];
    $lctn_exp_dt=html($row['lctn_exp_dt']);
    $lctn_exp_dt_bce=$row['lctn_exp_dt_bce'];
    $lctn_exp_dt_frmt=html($row['lctn_exp_dt_frmt']);
    $lctn_exp=html($row['lctn_exp']);
    $lctn_fctn=html($row['lctn_fctn']);

    $sql= "SELECT lctn_nm, lctn_sffx_num, rel_lctn_nt1, rel_lctn_nt2 FROM rel_lctn INNER JOIN lctn ON rel_lctn2=lctn_id
          WHERE rel_lctn1='$lctn_id' ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$rel_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$rel_lctn_sffx_num='';}
      if($row['rel_lctn_nt1']) {$rel_lctn_nt1=$row['rel_lctn_nt1'].'::';} else {$rel_lctn_nt1='';}
      if($row['rel_lctn_nt2']) {$rel_lctn_nt2=';;'.$row['rel_lctn_nt2'];} else {$rel_lctn_nt2='';}
      $rel_lctns[]=$rel_lctn_nt1.$row['lctn_nm'].$rel_lctn_sffx_num.$rel_lctn_nt2;
    }
    if(!empty($rel_lctns)) {$rel_lctn_list=html(implode(',,', $rel_lctns));} else {$rel_lctn_list='';}

    $sql= "SELECT lctn_nm, lctn_sffx_num, lctn_prvs_sg, lctn_sbsq_sg, COALESCE(lctn_alph, lctn_nm)lctn_alph FROM lctn_aka INNER JOIN lctn ON lctn_sbsq_id=lctn_id
          WHERE lctn_prvs_id='$lctn_id' ORDER BY lctn_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring "location subsequently known as" data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_prvs_sg']) {$lctn_prvs_sg='*';} else {$lctn_prvs_sg='';}
      if($row['lctn_sbsq_sg']) {$lctn_sbsq_sg='*';} else {$lctn_sbsq_sg='';}
      if($row['lctn_sffx_num']) {$sbsq_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$sbsq_lctn_sffx_num='';}
      $sbsq_lctns[]=$lctn_prvs_sg.$row['lctn_nm'].$sbsq_lctn_sffx_num.$lctn_sbsq_sg;
    }
    if(!empty($sbsq_lctns)) {$sbsq_lctn_list=html(implode(',,', $sbsq_lctns));} else {$sbsq_lctn_list='';}

    $sql= "SELECT lctn_nm, lctn_sffx_num, lctn_prvs_sg, lctn_sbsq_sg, COALESCE(lctn_alph, lctn_nm)lctn_alph FROM lctn_aka INNER JOIN lctn ON lctn_prvs_id=lctn_id
          WHERE lctn_sbsq_id='$lctn_id' ORDER BY lctn_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring "location previously known as" data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_prvs_sg']) {$lctn_prvs_sg='*';} else {$lctn_prvs_sg='';}
      if($row['lctn_sbsq_sg']) {$lctn_sbsq_sg='*';} else {$lctn_sbsq_sg='';}
      if($row['lctn_sffx_num']) {$prvs_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$prvs_lctn_sffx_num='';}
      $prvs_lctns[]=$lctn_prvs_sg.$row['lctn_nm'].$prvs_lctn_sffx_num.$lctn_sbsq_sg;
    }
    if(!empty($prvs_lctns)) {$prvs_lctn_list=html(implode(',,', $prvs_lctns));} else {$prvs_lctn_list='';}

    $sql="SELECT lctn_nm, lctn_exp, lctn_fctn FROM rel_lctn INNER JOIN lctn ON rel_lctn1=lctn_id WHERE rel_lctn2='$lctn_id' ORDER BY lctn_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related location (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if(!$row['lctn_exp'] && !$row['lctn_fctn']) {$rel_lctns1[]=html($row['lctn_nm']);}
      elseif(!$row['lctn_fctn']) {$rel_lctns1_exp[]=html($row['lctn_nm']);}
      else {$rel_lctns1_fctn[]=html($row['lctn_nm']);}
    }

    $textarea='';
    $lctn_id=html($lctn_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $lctn_id=cln($_POST['lctn_id']);
    $lctn_nm=trim(cln($_POST['lctn_nm']));
    $lctn_sffx_num=trim(cln($_POST['lctn_sffx_num']));
    $rel_lctn_list=cln($_POST['rel_lctn_list']);
    if(isset($_POST['lctn_est_dt_c'])) {$lctn_est_dt_c='1';} else {$lctn_est_dt_c='0';}
    $lctn_est_dt=cln($_POST['lctn_est_dt']);
    if(isset($_POST['lctn_est_dt_bce'])) {$lctn_est_dt_bce='1';} else {$lctn_est_dt_bce='0';}
    if($_POST['lctn_est_dt_frmt']=='1') {$lctn_est_dt_frmt='1';}
    if($_POST['lctn_est_dt_frmt']=='2') {$lctn_est_dt_frmt='2';}
    if($_POST['lctn_est_dt_frmt']=='3') {$lctn_est_dt_frmt='3';}
    if($_POST['lctn_est_dt_frmt']=='4') {$lctn_est_dt_frmt='4';}
    if(isset($_POST['lctn_exp_dt_c'])) {$lctn_exp_dt_c='1';} else {$lctn_exp_dt_c='0';}
    $lctn_exp_dt=cln($_POST['lctn_exp_dt']);
    if(isset($_POST['lctn_exp_dt_bce'])) {$lctn_exp_dt_bce='1';} else {$lctn_exp_dt_bce='0';}
    if($_POST['lctn_exp_dt_frmt']=='1') {$lctn_exp_dt_frmt='1';}
    if($_POST['lctn_exp_dt_frmt']=='2') {$lctn_exp_dt_frmt='2';}
    if($_POST['lctn_exp_dt_frmt']=='3') {$lctn_exp_dt_frmt='3';}
    if($_POST['lctn_exp_dt_frmt']=='4') {$lctn_exp_dt_frmt='4';}
    if(isset($_POST['lctn_exp'])) {$lctn_exp='1';} else {$lctn_exp='0';}
    if(isset($_POST['lctn_fctn'])) {$lctn_fctn='1';} else {$lctn_fctn='0';}
    $sbsq_lctn_list=cln($_POST['sbsq_lctn_list']);
    $prvs_lctn_list=cln($_POST['prvs_lctn_list']);

    $lctn_nm_session=$_POST['lctn_nm'];
    $errors=array();

    if(!preg_match('/\S+/', $lctn_nm))
    {$errors['lctn_nm']='**You must enter a location name.**';}
    elseif(preg_match('/--/', $lctn_nm) || preg_match('/,,/', $lctn_nm) || preg_match('/##/', $lctn_nm) || preg_match('/\+\+/', $lctn_nm)
    || preg_match('/::/', $lctn_nm) || preg_match('/;;/', $lctn_nm) || preg_match('/\|\|/', $lctn_nm) || preg_match('/>>/', $lctn_nm))
    {$errors['lctn_nm']='</br>**Location cannot include any of the following: [--], [,,], [##], [++], [::], [;;], [||], [>>].**';}

    if(preg_match('/^[0]*$/', $lctn_sffx_num) || !$lctn_sffx_num)
    {
      $lctn_sffx_num='0';
      $lctn_sffx_rmn='';
    }
    elseif(preg_match('/^[1-9][0-9]{0,1}$/', $lctn_sffx_num))
    {
      $lctn_sffx_rmn=' ('.romannumeral($lctn_sffx_num).')';
      $lctn_nm_session .= ' ('.romannumeral($_POST['lctn_sffx_num']).')';
    }
    else
    {
      $errors['lctn_sffx']='**The suffix must be a valid integer between 1 and 99 (with no leading 0) or left blank (or as 0).**';
      $lctn_sffx_rmn='';
    }

    $lctn_url=generateurl($lctn_nm.$lctn_sffx_rmn);

    if(strlen($lctn_nm)>255 || strlen($lctn_url)>255)
    {$errors['lctn_excss_lngth']='</br>**Location URL is allowed a maximum of 255 characters.**';}

    $lctn_alph=alph($lctn_nm);

    if(count($errors)==0)
    {
      $sql="SELECT lctn_id, lctn_nm, lctn_sffx_num FROM lctn WHERE lctn_url='$lctn_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing location URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['lctn_id']!==$lctn_id)
      {
        if($row['lctn_sffx_num']) {$lctn_sffx_rmn=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$lctn_sffx_rmn='';}
        $errors['lctn_url']='</br>**Duplicate URL exists for: '.html($row['lctn_nm'].$lctn_sffx_rmn). '. You must keep the original name or assign a location name without an existing URL.**';
      }
    }

    $new_rel_lctn_ids_array=array(); $exstng_rel_lctn_ids_array=array();
    if(preg_match('/\S+/', $rel_lctn_list))
    {
      $rel_lctn_nms=explode(',,', $_POST['rel_lctn_list']);
      if(count($rel_lctn_nms)>250)
      {$errors['rel_lctn_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rel_lctn_empty_err_arr=array(); $rel_lctn_nm_smcln_excss_err_arr=array(); $rel_lctn_nm_smcln_err_arr=array();
        $rel_lctn_nm_cln_excss_err_arr=array(); $rel_lctn_nm_cln_err_arr=array(); $rel_lctn_hyphn_excss_err_arr=array();
        $rel_lctn_sffx_err_arr=array(); $rel_lctn_dplct_arr=array(); $rel_lctn_url_err_arr=array();
        $rel_lctn_inv_comb_err_arr=array();
        foreach($rel_lctn_nms as $rel_lctn_nm)
        {
          $rel_lctn_errors=0;

          $rel_lctn_nm=trim($rel_lctn_nm);
          if(!preg_match('/\S+/', $rel_lctn_nm))
          {
            $rel_lctn_errors++; $rel_lctn_empty_err_arr[]=$rel_lctn_nm;
            if(count($rel_lctn_empty_err_arr)==1) {$errors['rel_lctn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rel_lctn_empty']='</br>**There are '.count($rel_lctn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($rel_lctn_nm, ';;')>1) {$rel_lctn_errors++; $rel_lctn_nm_smcln_excss_err_arr[]=$rel_lctn_nm; $errors['rel_lctn_nm_smcln_excss']='</br>**You may only use [;;] once per location-suffix note coupling. Please amend: '.html(implode(' / ', $rel_lctn_nm_smcln_excss_err_arr)).'.**'; $rel_lctn_nm_nt2='';}
            elseif(preg_match('/\S+.*;;.*\S+/', $rel_lctn_nm)) {list($rel_lctn_nm, $rel_lctn_nt2)=explode(';;', $rel_lctn_nm); $rel_lctn_nm=trim($rel_lctn_nm); $rel_lctn_nt2=trim($rel_lctn_nt2);
            if(strlen($rel_lctn_nt2)>255) {$errors['rel_lctn_nm_nt2_excss_lngth']='</br>**Location prefix note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}}
            elseif(substr_count($rel_lctn_nm, ';;')==1) {$rel_lctn_errors++; $rel_lctn_nm_smcln_err_arr[]=$rel_lctn_nm; $errors['rel_lctn_nm_smcln']='</br>**Location-suffix note assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $rel_lctn_nm_smcln_err_arr)).'.**'; $rel_lctn_nm_nt2='';}
            else {$rel_lctn_nt2='';}

            if(substr_count($rel_lctn_nm, '::')>1) {$rel_lctn_errors++; $rel_lctn_nm_cln_excss_err_arr[]=$rel_lctn_nm; $errors['rel_lctn_nm_cln_excss']='</br>**You may only use [::] once per location-prefix note coupling. Please amend: '.html(implode(' / ', $rel_lctn_nm_cln_excss_err_arr)).'.**'; $rel_lctn_nm_nt1='';}
            elseif(preg_match('/\S+.*::.*\S+/', $rel_lctn_nm)) {list($rel_lctn_nt1, $rel_lctn_nm)=explode('::', $rel_lctn_nm); $rel_lctn_nt1=trim($rel_lctn_nt1); $rel_lctn_nm=trim($rel_lctn_nm);
            if(strlen($rel_lctn_nt1)>255) {$errors['rel_lctn_nm_nt1_excss_lngth']='</br>**Location suffix note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}}
            elseif(substr_count($rel_lctn_nm, '::')==1) {$rel_lctn_errors++; $rel_lctn_nm_cln_err_arr[]=$rel_lctn_nm; $errors['rel_lctn_nm_cln']='</br>**Location-prefix note assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $rel_lctn_nm_cln_err_arr)).'.**'; $rel_lctn_nm_nt1='';}
            else {$rel_lctn_nt1='';}

            if(substr_count($rel_lctn_nm, '--')>1)
            {
              $rel_lctn_errors++; $rel_lctn_sffx_num='0'; $rel_lctn_sffx_rmn='';
              $rel_lctn_hyphn_excss_err_arr[]=$rel_lctn_nm;
              $errors['rel_lctn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per location. Please amend: '.html(implode(' / ', $rel_lctn_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $rel_lctn_nm))
            {
              list($rel_lctn_nm_no_sffx, $rel_lctn_sffx_num)=explode('--', $rel_lctn_nm);
              $rel_lctn_nm_no_sffx=trim($rel_lctn_nm_no_sffx); $rel_lctn_sffx_num=trim($rel_lctn_sffx_num);
              $rel_lctn_sffx_rmn=' ('.romannumeral($rel_lctn_sffx_num).')';

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $rel_lctn_sffx_num))
              {
                $rel_lctn_errors++; $rel_lctn_sffx_err_arr[]=$rel_lctn_nm;
                $errors['rel_lctn_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $rel_lctn_sffx_err_arr)).'**';
              }
              $rel_lctn_nm=$rel_lctn_nm_no_sffx;
            }
            else
            {$rel_lctn_sffx_num='0'; $rel_lctn_sffx_rmn='';}

            $rel_lctn_url=generateurl($rel_lctn_nm.$rel_lctn_sffx_rmn);

            $rel_lctn_dplct_arr[]=$rel_lctn_url;
            if(count(array_unique($rel_lctn_dplct_arr))<count($rel_lctn_dplct_arr))
            {$errors['rel_lctn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($rel_lctn_nm)>255 || strlen($rel_lctn_url)>255)
            {$rel_lctn_errors++; $errors['rel_lctn_nm_excss_lngth']='</br>**Location name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($rel_lctn_errors==0)
            {
              $rel_lctn_nm_cln=cln($rel_lctn_nm);
              $rel_lctn_sffx_num_cln=cln($rel_lctn_sffx_num);
              $rel_lctn_url_cln=cln($rel_lctn_url);

              $sql= "SELECT lctn_nm, lctn_sffx_num
                    FROM lctn
                    WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$rel_lctn_nm_cln' AND lctn_sffx_num='$rel_lctn_sffx_num_cln')
                    AND lctn_url='$rel_lctn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing location URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $rel_lctn_errors++;
                if($row['lctn_sffx_num']) {$rel_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$rel_lctn_sffx_num='';}
                $rel_lctn_url_err_arr[]=$row['lctn_nm'].$rel_lctn_sffx_num;
                if(count($rel_lctn_url_err_arr)==1)
                {$errors['rel_lctn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $rel_lctn_url_err_arr)).'?**';}
                else
                {$errors['rel_lctn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $rel_lctn_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT lctn_id FROM lctn WHERE lctn_url='$rel_lctn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing location URL (for existing location check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['lctn_id']==$lctn_id)
                  {$rel_lctn_errors++; $errors['rel_lctn_id_mtch']='</br>**You cannot assign this location as a related location of itself: '.html($rel_lctn_nm.$rel_lctn_sffx_rmn).'.**';}
                  else
                  {
                    $rel_lctn_id=$row['lctn_id'];
                    $sql="SELECT 1 FROM rel_lctn WHERE rel_lctn2='$lctn_id' AND rel_lctn1='$rel_lctn_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for inverse of proposed combination (related locations): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      $rel_lctn_errors++;
                      $rel_lctn_inv_comb_err_arr[]=$rel_lctn_nm.$rel_lctn_sffx_rmn;
                      $errors['rel_lctn_inv_comb']='</br>**The following locations cause an invalid inverse of existing location relationship combinations: '.html(implode(' / ', $rel_lctn_inv_comb_err_arr)).'.**';
                    }
                    if($rel_lctn_errors==0) {$new_rel_lctn_ids_array[]=$rel_lctn_id;}
                  }
                }
              }
            }
          }
        }
      }
    }

    $sql="SELECT rel_lctn2 FROM rel_lctn WHERE rel_lctn1='$lctn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existing related locations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$exstng_rel_lctn_ids_array[]=$row['rel_lctn2'];}

    $rmvd_rel_lctn_ids_array=array_diff($exstng_rel_lctn_ids_array, $new_rel_lctn_ids_array);

    $rmvd_rel_lctn_prds_err_arr=array();
    foreach($rmvd_rel_lctn_ids_array as $rmvd_rel_lctn_id)
    {
      $sql="SELECT lctn_nm, lctn_sffx_num, lctn_url FROM lctn WHERE lctn_id='$rmvd_rel_lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        if($row['lctn_sffx_num']) {$rmvd_rel_lctn_sffx_rmn_url_lnk=' ('.html(romannumeral($row['lctn_sffx_num'])).')';} else {$rmvd_rel_lctn_sffx_rmn_url_lnk='';}
        $rmvd_rel_lctn_nm=html($row['lctn_nm']); $rmvd_rel_lctn_url=html($row['lctn_url']);
      }

      $sql="SELECT 1 FROM prdsttng_lctn_alt WHERE sttng_lctnid='$lctn_id' AND sttng_lctn_altid='$rmvd_rel_lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring production data applying to removed related locations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        $rmvd_rel_lctn_prds_err_arr[]='<a href="/production/setting/location/'.$rmvd_rel_lctn_url.'" target="/production/setting/location/'.$rmvd_rel_lctn_url.'">'.$rmvd_rel_lctn_nm.$rmvd_rel_lctn_sffx_rmn_url_lnk.'</a>';
        $errors['rmvd_rel_lctn_prds']='</br>**Deleted locations have existing associations (as alternate related locations) with productions: '.implode(' / ', $rmvd_rel_lctn_prds_err_arr).'**';
      }

      $sql="SELECT 1 FROM ptsttng_lctn_alt WHERE sttng_lctnid='$lctn_id' AND sttng_lctn_altid='$rmvd_rel_lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring playtext data applying to removed related locations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        $rmvd_rel_lctn_pts_err_arr[]='<a href="/playtext/setting/location/'.$rmvd_rel_lctn_url.'" target="/playtext/setting/location/'.$rmvd_rel_lctn_url.'">'.$rmvd_rel_lctn_nm.$rmvd_rel_lctn_sffx_rmn_url_lnk.'</a>';
        $errors['rmvd_rel_lctn_pts']='</br>**Deleted locations have existing associations (as alternate related locations) with playtexts: '.implode(' / ', $rmvd_rel_lctn_pts_err_arr).'**';
      }

      $sql="SELECT 1 FROM prsnorg_lctn_alt WHERE org_lctnid='$lctn_id' AND org_lctn_altid='$rmvd_rel_lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring person data applying to removed related locations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        $rmvd_rel_lctn_ppl_err_arr[]='<a href="/person/origin/'.$rmvd_rel_lctn_url.'" target="/person/origin/'.$rmvd_rel_lctn_url.'">'.$rmvd_rel_lctn_nm.$rmvd_rel_lctn_sffx_rmn_url_lnk.'</a>';
        $errors['rmvd_rel_lctn_ppl']='</br>**Deleted locations have existing associations (as alternate related locations) with people: '.implode(' / ', $rmvd_rel_lctn_ppl_err_arr).'**';
      }

      $sql="SELECT 1 FROM charorg_lctn_alt WHERE org_lctnid='$lctn_id' AND org_lctn_altid='$rmvd_rel_lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring character data applying to removed related locations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        $rmvd_rel_lctn_chars_err_arr[]='<a href="/character/origin/'.$rmvd_rel_lctn_url.'" target="/character/origin/'.$rmvd_rel_lctn_url.'">'.$rmvd_rel_lctn_nm.$rmvd_rel_lctn_sffx_rmn_url_lnk.'</a>';
        $errors['rmvd_rel_lctn_chars']='</br>**Deleted locations have existing associations (as alternate related locations) with characters: '.implode(' / ', $rmvd_rel_lctn_chars_err_arr).'**';
      }

      $sql="SELECT 1 FROM thtr_lctn_alt WHERE thtr_lctnid='$lctn_id' AND thtr_lctn_altid='$rmvd_rel_lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring theatre data applying to removed related locations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        $rmvd_rel_lctn_thtrs_err_arr[]='<a href="/theatre/location/'.$rmvd_rel_lctn_url.'" target="/theatre/location/'.$rmvd_rel_lctn_url.'">'.$rmvd_rel_lctn_nm.$rmvd_rel_lctn_sffx_rmn_url_lnk.'</a>';
        $errors['rmvd_rel_lctn_thtrs']='</br>**Deleted locations have existing associations (as alternate related locations) with theatres: '.implode(' / ', $rmvd_rel_lctn_thtrs_err_arr).'**';
      }

      $sql="SELECT 1 FROM comp_lctn_alt WHERE comp_lctnid='$lctn_id' AND comp_lctn_altid='$rmvd_rel_lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring company data applying to removed related locations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        $rmvd_rel_lctn_comps_err_arr[]='<a href="/company/location/'.$rmvd_rel_lctn_url.'" target="/company/location/'.$rmvd_rel_lctn_url.'">'.$rmvd_rel_lctn_nm.$rmvd_rel_lctn_sffx_rmn_url_lnk.'</a>';
        $errors['rmvd_rel_lctn_comps']='</br>**Deleted locations have existing associations (as alternate related locations) with companies: '.implode(' / ', $rmvd_rel_lctn_comps_err_arr).'**';
      }
    }

    if($lctn_est_dt)
    {
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $lctn_est_dt)) {$errors['lctn_est_dt']='**You must enter a valid ESTABLISHED date in the prescribed format or leave empty.**'; $lctn_est_dt=NULL;}
      else {list($lctn_est_dt_YYYY, $lctn_est_dt_MM, $lctn_est_dt_DD)=explode('-', $lctn_est_dt);
      if(!checkdate((int)$lctn_est_dt_MM, (int)$lctn_est_dt_DD, (int)$lctn_est_dt_YYYY)) {$errors['lctn_est_dt']='**You must enter a valid ESTABLISHED date or leave empty.**'; $lctn_est_dt=NULL;}
      else {if(!$lctn_est_dt_bce) {$lctn_est_dt_num=$lctn_est_dt;} else {$lctn_est_dt_num='-'.$lctn_est_dt;}}}
    }
    else {$lctn_est_dt=NULL;}

    if($lctn_exp_dt)
    {
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $lctn_exp_dt))
      {$errors['lctn_exp_dt']='**You must enter a valid EXPIRED date in the prescribed format or leave empty.**'; $lctn_exp_dt=NULL;}
      else
      {
        date_default_timezone_set('Europe/London');
        list($lctn_exp_dt_YYYY, $lctn_exp_dt_MM, $lctn_exp_dt_DD)=explode('-', $lctn_exp_dt);
        if(!checkdate((int)$lctn_exp_dt_MM, (int)$lctn_exp_dt_DD, (int)$lctn_exp_dt_YYYY)) {$errors['lctn_exp_dt']='**You must enter a valid EXPIRED date or leave empty.**'; $lctn_exp_dt=NULL;}
        else
        {
          if(!$lctn_exp_dt_bce) {$lctn_exp_dt_num=$lctn_exp_dt;} else {$lctn_exp_dt_num='-'.$lctn_exp_dt;}
          if(strtotime($lctn_exp_dt)>time() && $lctn_exp && !$lctn_exp_dt_bce) {$errors['lctn_exp_dt_lctn_exp']='**You cannot check the location as expired and set the EXPIRED date as a future date.**';}
          elseif((strtotime($lctn_exp_dt) <= time() && !$lctn_exp_dt_bce) || ($lctn_exp_dt_bce)) {$lctn_exp='1';}
        }
      }
    }
    else {$lctn_exp_dt=NULL;}

    if($lctn_est_dt && $lctn_exp_dt)
    {
      if(!$lctn_est_dt_bce && !$lctn_exp_dt_bce)
      {if($lctn_est_dt>$lctn_exp_dt) {$errors['lctn_est_dt']='**Must be earlier than the ESTABLISHED date.**'; $errors['lctn_exp_dt']='**Must be later than the EXPIRED date.**';}}
      elseif(!$lctn_est_dt_bce && $lctn_exp_dt_bce)
      {$errors['lctn_est_dt']='**Must be earlier than the ESTABLISHED date.**'; $errors['lctn_exp_dt']='**Must be later than the EXPIRED date.**';}
      elseif($lctn_est_dt_bce && $lctn_exp_dt_bce)
      {
        if($lctn_est_dt_YYYY<$lctn_exp_dt_YYYY) {$errors['lctn_est_dt']='**Must be earlier than the ESTABLISHED date.**'; $errors['lctn_exp_dt']='**Must be later than the EXPIRED date.**';}
        elseif($lctn_est_dt_YYYY==$lctn_exp_dt_YYYY && $lctn_est_dt_MM.$lctn_est_dt_DD>$lctn_exp_dt_MM.$lctn_exp_dt_DD) {$errors['lctn_est_dt']='**Must be earlier than the ESTABLISHED date.**'; $errors['lctn_exp_dt']='**Must be later than the EXPIRED date.**';}
      }
    }
    else
    {if(!$lctn_est_dt_bce && $lctn_exp_dt_bce) {$errors['lctn_est_dt']='**Must be earlier than the ESTABLISHED date.**'; $errors['lctn_exp_dt']='**Must be later than the EXPIRED date.**';}}

    if(preg_match('/\S+/', $sbsq_lctn_list))
    {
      $sbsq_lctn_nms=explode(',,', $_POST['sbsq_lctn_list']);
      if(count($sbsq_lctn_nms)>250)
      {$errors['sbsq_lctn_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $sbsq_lctn_empty_err_arr=array(); $sbsq_lctn_astrsk_excss_err_arr=array(); $sbsq_lctn_hyphn_excss_err_arr=array();
        $sbsq_lctn_sffx_err_arr=array(); $sbsq_lctn_dplct_arr=array(); $sbsq_lctn_url_err_arr=array();
        $sbsq_lctn_inv_comb_err_arr=array(); $sbsq_lctn_non_exp_err_arr=array(); $sbsq_lctn_dt_mtch_err_arr=array();
        foreach($sbsq_lctn_nms as $sbsq_lctn_nm)
        {
          $sbsq_lctn_errors=0;

          $sbsq_lctn_nm=trim($sbsq_lctn_nm);
          if(!preg_match('/\S+/', $sbsq_lctn_nm))
          {
            $sbsq_lctn_errors++; $sbsq_lctn_empty_err_arr[]=$sbsq_lctn_nm;
            if(count($sbsq_lctn_empty_err_arr)==1) {$errors['sbsq_lctn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['sbsq_lctn_empty']='</br>**There are '.count($sbsq_lctn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(preg_match('/^\*.*\S+.*\*$/', $sbsq_lctn_nm)) {$sbsq_lctn_errors++; $sbsq_lctn_astrsk_excss_err_arr[]=$sbsq_lctn_nm; $errors['sbsq_lctn_astrsk_excss']='</br>**You may only use [*] for "part of" assignment once per location (either at start or end). Please amend: '.html(implode(' / ', $sbsq_lctn_astrsk_excss_err_arr)).'.**';}

            if(preg_match('/^\S+.*\*$/', $sbsq_lctn_nm)){$sbsq_lctn_nm=preg_replace('/(\S+.*)(\*)/', '$1', $sbsq_lctn_nm); $sbsq_lctn_nm=trim($sbsq_lctn_nm);}
            if(preg_match('/^\*.*\S+$/', $sbsq_lctn_nm)){$sbsq_lctn_nm=preg_replace('/(\*)(.*\S+)/', '$1', $sbsq_lctn_nm); $sbsq_lctn_nm=trim($sbsq_lctn_nm);}

            if(substr_count($sbsq_lctn_nm, '--')>1)
            {
              $sbsq_lctn_errors++; $sbsq_lctn_sffx_num='0'; $sbsq_lctn_sffx_rmn='';
              $sbsq_lctn_hyphn_excss_err_arr[]=$sbsq_lctn_nm;
              $errors['sbsq_lctn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per location. Please amend: '.html(implode(' / ', $sbsq_lctn_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $sbsq_lctn_nm))
            {
              list($sbsq_lctn_nm_no_sffx, $sbsq_lctn_sffx_num)=explode('--', $sbsq_lctn_nm);
              $sbsq_lctn_nm_no_sffx=trim($sbsq_lctn_nm_no_sffx); $sbsq_lctn_sffx_num=trim($sbsq_lctn_sffx_num);
              $sbsq_lctn_sffx_rmn=' ('.romannumeral($sbsq_lctn_sffx_num).')';

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $sbsq_lctn_sffx_num))
              {
                $sbsq_lctn_errors++; $sbsq_lctn_sffx_err_arr[]=$sbsq_lctn_nm;
                $errors['sbsq_lctn_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $sbsq_lctn_sffx_err_arr)).'**';
              }
              $sbsq_lctn_nm=$sbsq_lctn_nm_no_sffx;
            }
            else
            {$sbsq_lctn_sffx_num='0'; $sbsq_lctn_sffx_rmn='';}

            $sbsq_lctn_url=generateurl($sbsq_lctn_nm.$sbsq_lctn_sffx_rmn);

            $sbsq_lctn_dplct_arr[]=$sbsq_lctn_url;
            if(count(array_unique($sbsq_lctn_dplct_arr))<count($sbsq_lctn_dplct_arr))
            {$errors['sbsq_lctn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($sbsq_lctn_nm)>255 || strlen($sbsq_lctn_url)>255)
            {$sbsq_lctn_errors++; $errors['sbsq_lctn_nm_excss_lngth']='</br>**Location name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($sbsq_lctn_errors==0)
            {
              $sbsq_lctn_nm_cln=cln($sbsq_lctn_nm);
              $sbsq_lctn_sffx_num_cln=cln($sbsq_lctn_sffx_num);
              $sbsq_lctn_url_cln=cln($sbsq_lctn_url);

              $sql= "SELECT lctn_nm, lctn_sffx_num
                    FROM lctn
                    WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$sbsq_lctn_nm_cln' AND lctn_sffx_num='$sbsq_lctn_sffx_num_cln')
                    AND lctn_url='$sbsq_lctn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing location URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['lctn_sffx_num']) {$sbsq_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$sbsq_lctn_sffx_num='';}
                $sbsq_lctn_url_err_arr[]=$row['lctn_nm'].$sbsq_lctn_sffx_num;
                if(count($sbsq_lctn_url_err_arr)==1) {$errors['sbsq_lctn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $sbsq_lctn_url_err_arr)).'?**';}
                else {$errors['sbsq_lctn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $sbsq_lctn_url_err_arr)).'?**';}
              }
              else
              {
                $sql= "SELECT lctn_id, lctn_nm, lctn_sffx_num, lctn_url, lctn_est_dt, lctn_est_dt_bce, lctn_exp_dt, lctn_exp_dt_bce, lctn_fctn
                      FROM lctn WHERE lctn_url='$sbsq_lctn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing location URL (for existing location check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['lctn_sffx_num']) {$sbsq_lctn_sffx_rmn_url_lnk=' ('.html(romannumeral($row['lctn_sffx_num'])).')';} else {$sbsq_lctn_sffx_rmn_url_lnk='';}
                  $sbsq_lctn_url_lnk='<a href="'.html($row['lctn_url']).'" target="'.html($row['lctn_url']).'">'.html($row['lctn_nm']).$sbsq_lctn_sffx_rmn_url_lnk.'</a>';
                  if(!$row['lctn_est_dt_bce']) {$sbsq_lctn_est_dt=$row['lctn_est_dt'];} else {$sbsq_lctn_est_dt='-'.$row['lctn_est_dt'];}
                  if(!$row['lctn_exp_dt_bce']) {$sbsq_lctn_exp_dt=$row['lctn_exp_dt'];} else {$sbsq_lctn_exp_dt='-'.$row['lctn_exp_dt'];}
                  $sbsq_lctn_fctn=$row['lctn_fctn'];
                  if($row['lctn_id']==$lctn_id)
                  {$errors['sbsq_lctn_id_mtch']='</br>**You cannot assign this location as a subsequent location of itself: '.html($sbsq_lctn_nm.$sbsq_lctn_sffx_rmn).'.**';}
                  else
                  {
                    $sbsq_lctn_id=$row['lctn_id'];
                    $sql="SELECT 1 FROM lctn_aka WHERE lctn_sbsq_id='$lctn_id' AND lctn_prvs_id='$sbsq_lctn_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for inverse of proposed combination (subsequently named locations): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      $sbsq_lctn_inv_comb_err_arr[]=$sbsq_lctn_nm.$sbsq_lctn_sffx_rmn;
                      $errors['sbsq_lctn_inv_comb']='</br>**The following locations cause an invalid inverse of existing location relationship combinations: '.html(implode(' / ', $sbsq_lctn_inv_comb_err_arr)).'.**';
                    }
                    elseif(!$lctn_exp && !$lctn_fctn && !$sbsq_lctn_fctn) {$sbsq_lctn_non_exp_err_arr[]=$sbsq_lctn_url_lnk; $errors['sbsq_lctn_non_exp']='</br>**Location (if not fictional) must be set as expired to assign subsequent (non-fictional) locations. Please amend: '.implode(' / ', $sbsq_lctn_non_exp_err_arr).'**';}
                    elseif(($sbsq_lctn_est_dt && $lctn_est_dt_num && $sbsq_lctn_est_dt <= $lctn_est_dt_num) || ($sbsq_lctn_exp_dt && $lctn_est_dt_num && $sbsq_lctn_exp_dt <= $lctn_est_dt_num) || ($sbsq_lctn_exp_dt && $lctn_exp_dt_num && $sbsq_lctn_exp_dt <= $lctn_exp_dt_num))
                    {$sbsq_lctn_dt_mtch_err_arr[]=$sbsq_lctn_url_lnk; $errors['sbsq_lctn_dt_mtch']='</br>**Subsequent locations must have been established after expiry of this location. Please amend: '.implode(' / ', $sbsq_lctn_dt_mtch_err_arr).'**';}
                  }
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $prvs_lctn_list))
    {
      $prvs_lctn_nms=explode(',,', $_POST['prvs_lctn_list']);
      if(count($prvs_lctn_nms)>250)
      {$errors['prvs_lctn_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $prvs_lctn_empty_err_arr=array(); $prvs_lctn_astrsk_excss_err_arr=array(); $prvs_lctn_hyphn_excss_err_arr=array();
        $prvs_lctn_sffx_err_arr=array(); $prvs_lctn_dplct_arr=array(); $prvs_lctn_url_err_arr=array();
        $prvs_lctn_inv_comb_err_arr=array(); $prvs_lctn_non_exp_err_arr=array(); $prvs_lctn_dt_mtch_err_arr=array();
        foreach($prvs_lctn_nms as $prvs_lctn_nm)
        {
          $prvs_lctn_errors=0;

          $prvs_lctn_nm=trim($prvs_lctn_nm);
          if(!preg_match('/\S+/', $prvs_lctn_nm))
          {
            $prvs_lctn_errors++; $prvs_lctn_empty_err_arr[]=$prvs_lctn_nm;
            if(count($prvs_lctn_empty_err_arr)==1) {$errors['prvs_lctn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['prvs_lctn_empty']='</br>**There are '.count($prvs_lctn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(preg_match('/^\*.*\S+.*\*$/', $prvs_lctn_nm)) {$prvs_lctn_errors++; $prvs_lctn_astrsk_excss_err_arr[]=$prvs_lctn_nm; $errors['prvs_lctn_astrsk_excss']='</br>**You may only use [*] for "part of" assignment once per location (either at start or end). Please amend: '.html(implode(' / ', $prvs_lctn_astrsk_excss_err_arr)).'.**';}

            if(preg_match('/^\S+.*\*$/', $prvs_lctn_nm)){$prvs_lctn_nm=preg_replace('/(\S+.*)(\*)/', '$1', $prvs_lctn_nm); $prvs_lctn_nm=trim($prvs_lctn_nm);}
            if(preg_match('/^\*.*\S+$/', $prvs_lctn_nm)){$prvs_lctn_nm=preg_replace('/(\*)(.*\S+)/', '$1', $prvs_lctn_nm); $prvs_lctn_nm=trim($prvs_lctn_nm);}

            if(substr_count($prvs_lctn_nm, '--')>1)
            {
              $prvs_lctn_errors++; $prvs_lctn_sffx_num='0'; $prvs_lctn_sffx_rmn='';
              $prvs_lctn_hyphn_excss_err_arr[]=$prvs_lctn_nm;
              $errors['prvs_lctn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per location. Please amend: '.html(implode(' / ', $prvs_lctn_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $prvs_lctn_nm))
            {
              list($prvs_lctn_nm_no_sffx, $prvs_lctn_sffx_num)=explode('--', $prvs_lctn_nm);
              $prvs_lctn_nm_no_sffx=trim($prvs_lctn_nm_no_sffx); $prvs_lctn_sffx_num=trim($prvs_lctn_sffx_num);
              $prvs_lctn_sffx_rmn=' ('.romannumeral($prvs_lctn_sffx_num).')';

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $prvs_lctn_sffx_num))
              {
                $prvs_lctn_errors++; $prvs_lctn_sffx_err_arr[]=$prvs_lctn_nm;
                $errors['prvs_lctn_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $prvs_lctn_sffx_err_arr)).'**';
              }
              $prvs_lctn_nm=$prvs_lctn_nm_no_sffx;
            }
            else
            {$prvs_lctn_sffx_num='0'; $prvs_lctn_sffx_rmn='';}

            $prvs_lctn_url=generateurl($prvs_lctn_nm.$prvs_lctn_sffx_rmn);

            $prvs_lctn_dplct_arr[]=$prvs_lctn_url;
            if(count(array_unique($prvs_lctn_dplct_arr))<count($prvs_lctn_dplct_arr))
            {$errors['prvs_lctn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($prvs_lctn_nm)>255 || strlen($prvs_lctn_url)>255)
            {$prvs_lctn_errors++; $errors['prvs_lctn_nm_excss_lngth']='</br>**Location name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($prvs_lctn_errors==0)
            {
              $prvs_lctn_nm_cln=cln($prvs_lctn_nm);
              $prvs_lctn_sffx_num_cln=cln($prvs_lctn_sffx_num);
              $prvs_lctn_url_cln=cln($prvs_lctn_url);

              $sql= "SELECT lctn_nm, lctn_sffx_num
                    FROM lctn
                    WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$prvs_lctn_nm_cln' AND lctn_sffx_num='$prvs_lctn_sffx_num_cln')
                    AND lctn_url='$prvs_lctn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing location URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['lctn_sffx_num']) {$prvs_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$prvs_lctn_sffx_num='';}
                $prvs_lctn_url_err_arr[]=$row['lctn_nm'].$prvs_lctn_sffx_num;
                if(count($prvs_lctn_url_err_arr)==1) {$errors['prvs_lctn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $prvs_lctn_url_err_arr)).'?**';}
                else {$errors['prvs_lctn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $prvs_lctn_url_err_arr)).'?**';}
              }
              else
              {
                $sql= "SELECT lctn_id, lctn_nm, lctn_sffx_num, lctn_url, lctn_est_dt, lctn_est_dt_bce, lctn_exp_dt, lctn_exp_dt_bce, lctn_exp, lctn_fctn
                      FROM lctn WHERE lctn_url='$prvs_lctn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing location URL (for existing location check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['lctn_sffx_num']) {$prvs_lctn_sffx_rmn_url_lnk=' ('.html(romannumeral($row['lctn_sffx_num'])).')';} else {$prvs_lctn_sffx_rmn_url_lnk='';}
                  $prvs_lctn_url_lnk='<a href="'.html($row['lctn_url']).'" target="'.html($row['lctn_url']).'">'.html($row['lctn_nm']).$prvs_lctn_sffx_rmn_url_lnk.'</a>';
                  if(!$row['lctn_est_dt_bce']) {$prvs_lctn_est_dt=$row['lctn_est_dt'];} else {$prvs_lctn_est_dt='-'.$row['lctn_est_dt'];}
                  if(!$row['lctn_exp_dt_bce']) {$prvs_lctn_exp_dt=$row['lctn_exp_dt'];} else {$prvs_lctn_exp_dt='-'.$row['lctn_exp_dt'];}
                  $prvs_lctn_exp=$row['lctn_exp']; $prvs_lctn_fctn=$row['lctn_fctn'];
                  if($row['lctn_id']==$lctn_id)
                  {$errors['prvs_lctn_id_mtch']='</br>**You cannot assign this location as a previous location of itself: '.html($prvs_lctn_nm.$prvs_lctn_sffx_rmn).'.**';}
                  else
                  {
                    $prvs_lctn_id=$row['lctn_id'];
                    $sql="SELECT 1 FROM lctn_aka WHERE lctn_prvs_id='$lctn_id' AND lctn_sbsq_id='$prvs_lctn_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for inverse of proposed combination (previously named locations): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      $prvs_lctn_inv_comb_err_arr[]=$prvs_lctn_nm.$prvs_lctn_sffx_rmn;
                      $errors['prvs_lctn_inv_comb']='</br>**The following locations cause an invalid inverse of existing location relationship combinations: '.html(implode(' / ', $prvs_lctn_inv_comb_err_arr)).'.**';
                    }
                    elseif(!$prvs_lctn_exp && !$prvs_lctn_fctn && !$lctn_fctn) {$prvs_lctn_non_exp_err_arr[]=$prvs_lctn_url_lnk; $errors['prvs_lctn_non_exp']='</br>**Previous (non-fictional) locations must be set as expired (if this location is not fictional). Please amend: '.implode(' / ', $prvs_lctn_non_exp_err_arr).'**';}
                    elseif(($prvs_lctn_est_dt && $lctn_est_dt_num && $prvs_lctn_est_dt >= $lctn_est_dt_num) || ($prvs_lctn_est_dt && $lctn_exp_dt_num && $prvs_lctn_est_dt >= $lctn_exp_dt_num) || ($prvs_lctn_exp_dt && $lctn_exp_dt_num && $prvs_lctn_exp_dt >= $lctn_exp_dt_num))
                    {$prvs_lctn_dt_mtch_err_arr[]=$prvs_lctn_url_lnk; $errors['prvs_lctn_dt_mtch']='</br>**Previous locations must have expired before establishment of this location. Please amend: '.implode(' / ', $prvs_lctn_dt_mtch_err_arr).'**';}
                  }
                }
              }
            }
          }
        }
      }
    }

    if($lctn_fctn)
    {
      $sql="SELECT 1 FROM prsn WHERE org_lctnid='$lctn_id' UNION SELECT 1 FROM comp_lctn WHERE comp_lctnid='$lctn_id' UNION SELECT 1 FROM thtr WHERE thtr_lctnid='$lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for location associations with people (as place of origin), companies or theatres (as location): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0) {$errors['lctn_fctn']='</br>**Location must remain non-fictional while associations exist with people, companies or theatres.**';}

      $sql= "SELECT 1 FROM prsnorg_lctn_alt WHERE org_lctn_altid='$lctn_id'
            UNION
            SELECT 1 FROM comp_lctn_alt WHERE comp_lctn_altid='$lctn_id'
            UNION
            SELECT 1 FROM thtr_lctn_alt WHERE thtr_lctn_altid='$lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for alternate related location associations with people (as place of origin), companies or theatres (as location): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0) {$errors['lctn_fctn_alt']='</br>**Location must remain non-fictional while associations (as alternate related location) exist with people, companies or theatres.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $lctn_id=cln($_POST['lctn_id']);
      $sql= "SELECT lctn_nm, lctn_sffx_num
            FROM lctn
            WHERE lctn_id='$lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring location details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['lctn_sffx_num']) {$lctn_sffx_rmn=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$lctn_sffx_rmn='';}
      $pagetab='Edit: '.html($row['lctn_nm'].$lctn_sffx_rmn);
      $pagetitle=html($row['lctn_nm'].$lctn_sffx_rmn);
      $lctn_nm=$_POST['lctn_nm'];
      $lctn_sffx_num=$_POST['lctn_sffx_num'];
      $rel_lctn_list=$_POST['rel_lctn_list'];
      $sbsq_lctn_list=$_POST['sbsq_lctn_list'];
      $prvs_lctn_list=$_POST['prvs_lctn_list'];
      $textarea=$_POST['textarea'];
      $errors['lctn_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $lctn_id=html($lctn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE lctn SET
            lctn_nm='$lctn_nm',
            lctn_alph=CASE WHEN '$lctn_alph'!='' THEN '$lctn_alph' END,
            lctn_sffx_num='$lctn_sffx_num',
            lctn_url='$lctn_url',
            lctn_est_dt=CASE WHEN '$lctn_est_dt'!='' THEN '$lctn_est_dt' END,
            lctn_est_dt_c=CASE WHEN '$lctn_est_dt'!='' THEN '$lctn_est_dt_c' END,
            lctn_est_dt_bce=CASE WHEN '$lctn_est_dt'!='' THEN '$lctn_est_dt_bce' END,
            lctn_est_dt_frmt=CASE WHEN '$lctn_est_dt'!='' THEN '$lctn_est_dt_frmt' END,
            lctn_exp_dt=CASE WHEN '$lctn_exp_dt'!='' THEN '$lctn_exp_dt' END,
            lctn_exp_dt_c=CASE WHEN '$lctn_exp_dt'!='' THEN '$lctn_exp_dt_c' END,
            lctn_exp_dt_bce=CASE WHEN '$lctn_exp_dt'!='' THEN '$lctn_exp_dt_bce' END,
            lctn_exp_dt_frmt=CASE WHEN '$lctn_exp_dt'!='' THEN '$lctn_exp_dt_frmt' END,
            lctn_exp='$lctn_exp',
            lctn_fctn='$lctn_fctn'
            WHERE lctn_id='$lctn_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating location info for submitted location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM rel_lctn WHERE rel_lctn1='$lctn_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting location-related location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $rel_lctn_list))
      {
        $rel_lctn_nms=explode(',,', $rel_lctn_list);
        $n=0;
        foreach($rel_lctn_nms as $rel_lctn_nm)
        {
          $rel_lctn_ordr=++$n;

          if(preg_match('/\S+.*;;.*\S+/', $rel_lctn_nm)) {list($rel_lctn_nm, $rel_lctn_nt2)=explode(';;', $rel_lctn_nm); $rel_lctn_nm=trim($rel_lctn_nm); $rel_lctn_nt2=trim($rel_lctn_nt2);}
          else {$rel_lctn_nt2='';}

          if(preg_match('/\S+.*::.*\S+/', $rel_lctn_nm)) {list($rel_lctn_nt1, $rel_lctn_nm)=explode('::', $rel_lctn_nm); $rel_lctn_nt1=trim($rel_lctn_nt1); $rel_lctn_nm=trim($rel_lctn_nm).' ';}
          else {$rel_lctn_nt1='';}

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $rel_lctn_nm))
          {
            list($rel_lctn_nm, $rel_lctn_sffx_num)=explode('--', $rel_lctn_nm);
            $rel_lctn_nm=trim($rel_lctn_nm); $rel_lctn_sffx_num=trim($rel_lctn_sffx_num);
            $rel_lctn_sffx_rmn=' ('.romannumeral($rel_lctn_sffx_num).')';
          }
          else {$rel_lctn_nm=trim($rel_lctn_nm); $rel_lctn_sffx_rmn='';}

          $rel_lctn_url=generateurl($rel_lctn_nm.$rel_lctn_sffx_rmn);
          $rel_lctn_alph=alph($rel_lctn_nm);

          $sql="SELECT 1 FROM lctn WHERE lctn_url='$rel_lctn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO lctn(lctn_nm, lctn_alph, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn)
                  VALUES('$rel_lctn_nm', CASE WHEN '$rel_lctn_alph'!='' THEN '$rel_lctn_alph' END, '$rel_lctn_sffx_num', '$rel_lctn_url', 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO rel_lctn(rel_lctn_ordr, rel_lctn_nt1, rel_lctn_nt2, rel_lctn1, rel_lctn2)
                SELECT '$rel_lctn_ordr', '$rel_lctn_nt1', '$rel_lctn_nt2', '$lctn_id', lctn_id FROM lctn WHERE lctn_url='$rel_lctn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding location-related location association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    $sql="DELETE FROM lctn_aka WHERE lctn_prvs_id='$lctn_id' OR lctn_sbsq_id='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting location-previously/subsequently known as associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    if(preg_match('/\S+/', $sbsq_lctn_list))
    {
      $sbsq_lctn_nms=explode(',,', $sbsq_lctn_list);
      foreach($sbsq_lctn_nms as $sbsq_lctn_nm)
      {
        if(preg_match('/^\S+.*\*$/', $sbsq_lctn_nm)){$sbsq_lctn_nm=preg_replace('/(\S+.*)(\*)/', '$1', $sbsq_lctn_nm); $lctn_sbsq_sg='1'; $sbsq_lctn_nm=trim($sbsq_lctn_nm);} else {$lctn_sbsq_sg='0';}
        if(preg_match('/^\*.*\S+$/', $sbsq_lctn_nm)){$sbsq_lctn_nm=preg_replace('/(\*)(.*\S+)/', '$2', $sbsq_lctn_nm); $lctn_prvs_sg='1'; $sbsq_lctn_nm=trim($sbsq_lctn_nm);} else {$lctn_prvs_sg='0';}

        if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $sbsq_lctn_nm))
        {
          list($sbsq_lctn_nm, $sbsq_lctn_sffx_num)=explode('--', $sbsq_lctn_nm);
          $sbsq_lctn_nm=trim($sbsq_lctn_nm); $sbsq_lctn_sffx_num=trim($sbsq_lctn_sffx_num);
          $sbsq_lctn_sffx_rmn=' ('.romannumeral($sbsq_lctn_sffx_num).')';
        }
        else {$sbsq_lctn_nm=trim($sbsq_lctn_nm); $sbsq_lctn_sffx_rmn='';}

        $sbsq_lctn_url=generateurl($sbsq_lctn_nm.$sbsq_lctn_sffx_rmn);
        $sbsq_lctn_alph=alph($sbsq_lctn_nm);

        $sql="SELECT 1 FROM lctn WHERE lctn_url='$sbsq_lctn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO lctn(lctn_nm, lctn_alph, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn)
                VALUES('$sbsq_lctn_nm', CASE WHEN '$sbsq_lctn_alph'!='' THEN '$sbsq_lctn_alph' END, '$sbsq_lctn_sffx_num', '$sbsq_lctn_url', 0, 0)";
          if(!mysqli_query($link, $sql)) {$error='Error adding location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql= "INSERT INTO lctn_aka(lctn_prvs_sg, lctn_sbsq_sg, lctn_prvs_id, lctn_sbsq_id)
              SELECT '$lctn_prvs_sg', '$lctn_sbsq_sg', '$lctn_id', lctn_id FROM lctn WHERE lctn_url='$sbsq_lctn_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding location-subsequently known as location association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
    }

    if(preg_match('/\S+/', $prvs_lctn_list))
    {
      if(!$lctn_fctn) {$prvs_lctn_exp='1';} else {$prvs_lctn_exp='0';}
      $prvs_lctn_nms=explode(',,', $prvs_lctn_list);
      foreach($prvs_lctn_nms as $prvs_lctn_nm)
      {
        if(preg_match('/^\S+.*\*$/', $prvs_lctn_nm)){$prvs_lctn_nm=preg_replace('/(\S+.*)(\*)/', '$1', $prvs_lctn_nm); $lctn_sbsq_sg='1'; $prvs_lctn_nm=trim($prvs_lctn_nm);} else {$lctn_sbsq_sg='0';}
        if(preg_match('/^\*.*\S+$/', $prvs_lctn_nm)){$prvs_lctn_nm=preg_replace('/(\*)(.*\S+)/', '$2', $prvs_lctn_nm); $lctn_prvs_sg='1'; $prvs_lctn_nm=trim($prvs_lctn_nm);} else {$lctn_prvs_sg='0';}

        if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prvs_lctn_nm))
        {
          list($prvs_lctn_nm, $prvs_lctn_sffx_num)=explode('--', $prvs_lctn_nm);
          $prvs_lctn_nm=trim($prvs_lctn_nm); $prvs_lctn_sffx_num=trim($prvs_lctn_sffx_num);
          $prvs_lctn_sffx_rmn=' ('.romannumeral($prvs_lctn_sffx_num).')';
        }
        else {$prvs_lctn_nm=trim($prvs_lctn_nm); $prvs_lctn_sffx_rmn='';}

        $prvs_lctn_url=generateurl($prvs_lctn_nm.$prvs_lctn_sffx_rmn);
        $prvs_lctn_alph=alph($prvs_lctn_nm);

        $sql="SELECT 1 FROM lctn WHERE lctn_url='$prvs_lctn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO lctn(lctn_nm, lctn_alph, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn)
                VALUES('$prvs_lctn_nm', CASE WHEN '$prvs_lctn_alph'!='' THEN '$prvs_lctn_alph' END, '$prvs_lctn_sffx_num', '$prvs_lctn_url', '$prvs_lctn_exp', 0)";
          if(!mysqli_query($link, $sql)) {$error='Error adding location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql= "INSERT INTO lctn_aka(lctn_prvs_sg, lctn_sbsq_sg, lctn_sbsq_id, lctn_prvs_id)
              SELECT '$lctn_prvs_sg', '$lctn_sbsq_sg', '$lctn_id', lctn_id FROM lctn WHERE lctn_url='$prvs_lctn_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding location-previously known as location association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS LOCATION HAS BEEN EDITED:'.' '.html($lctn_nm_session);
    header('Location: '.$lctn_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $lctn_id=cln($_POST['lctn_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdsttng_lctn WHERE sttng_lctnid='$lctn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-setting (location) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM ptsttng_lctn WHERE sttng_lctnid='$lctn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-setting (location) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    if(count($assocs)>0)
    {$errors['lctn_dlt']='**Location must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT lctn_nm, lctn_sffx_num
            FROM lctn
            WHERE lctn_id='$lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error';
      $error='Error acquiring location details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['lctn_sffx_num']) {$lctn_sffx_rmn=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$lctn_sffx_rmn='';}
      $pagetab='Edit: '.html($row['lctn_nm'].$lctn_sffx_rmn);
      $pagetitle=html($row['lctn_nm'].$lctn_sffx_rmn);
      $lctn_nm=$_POST['lctn_nm'];
      $lctn_sffx_num=$_POST['lctn_sffx_num'];
      $rel_lctn_list=$_POST['rel_lctn_list'];
      if(isset($_POST['lctn_est_dt_c'])) {$lctn_est_dt_c='1';} else {$lctn_est_dt_c='0';}
      $lctn_est_dt=$_POST['lctn_est_dt'];
      if(isset($_POST['lctn_est_dt_bce'])) {$lctn_est_dt_bce='1';} else {$lctn_est_dt_bce='0';}
      if($_POST['lctn_est_dt_frmt']=='1') {$lctn_est_dt_frmt='1';}
      if($_POST['lctn_est_dt_frmt']=='2') {$lctn_est_dt_frmt='2';}
      if($_POST['lctn_est_dt_frmt']=='3') {$lctn_est_dt_frmt='3';}
      if($_POST['lctn_est_dt_frmt']=='4') {$lctn_est_dt_frmt='4';}
      if(isset($_POST['lctn_exp_dt_c'])) {$lctn_exp_dt_c='1';} else {$lctn_exp_dt_c='0';}
      $lctn_exp_dt=$_POST['lctn_exp_dt'];
      if(isset($_POST['lctn_exp_dt_bce'])) {$lctn_exp_dt_bce='1';} else {$lctn_exp_dt_bce='0';}
      if($_POST['lctn_exp_dt_frmt']=='1') {$lctn_exp_dt_frmt='1';}
      if($_POST['lctn_exp_dt_frmt']=='2') {$lctn_exp_dt_frmt='2';}
      if($_POST['lctn_exp_dt_frmt']=='3') {$lctn_exp_dt_frmt='3';}
      if($_POST['lctn_exp_dt_frmt']=='4') {$lctn_exp_dt_frmt='4';}
      if(isset($_POST['lctn_exp'])) {$lctn_exp='1';} else {$lctn_exp='0';}
      if(isset($_POST['lctn_fctn'])) {$lctn_fctn='1';} else {$lctn_fctn='0';}
      $sbsq_lctn_list=$_POST['sbsq_lctn_list'];
      $prvs_lctn_list=$_POST['prvs_lctn_list'];
      $textarea=$_POST['textarea'];
      $lctn_id=html($lctn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT lctn_nm, lctn_sffx_num
            FROM lctn
            WHERE lctn_id='$lctn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['lctn_sffx_num']) {$lctn_sffx_rmn=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$lctn_sffx_rmn='';}
      $pagetab='Delete confirmation: '.html($row['lctn_nm'].$lctn_sffx_rmn);
      $pagetitle=html($row['lctn_nm'].$lctn_sffx_rmn);
      $lctn_id=html($lctn_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $lctn_id=cln($_POST['lctn_id']);
    $sql= "SELECT lctn_nm, lctn_sffx_num
          FROM lctn
          WHERE lctn_id='$lctn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring location details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['lctn_sffx_num']) {$lctn_sffx_rmn=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$lctn_sffx_rmn='';}
    $lctn_nm_session=$row['lctn_nm'].$lctn_sffx_rmn;

    $sql="DELETE FROM prdsttng_lctn WHERE sttng_lctnid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting setting (location)-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdsttng_lctn_alt WHERE sttng_lctnid='$lctn_id' OR sttng_lctn_altid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting location-alternate location (production) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng_lctn WHERE sttng_lctnid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting setting (location)-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng_lctn_alt WHERE sttng_lctnid='$lctn_id' OR sttng_lctn_altid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting location-alternate location (playtext) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM rel_lctn WHERE rel_lctn1='$lctn_id' OR rel_lctn2='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting location-related location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM lctn_aka WHERE lctn_prvs_id='$lctn_id' OR lctn_sbsq_id='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting location-"previously/subsequently known as" location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="UPDATE prsn SET org_lctnid=NULL WHERE org_lctnid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error nullifying person (place of origin)-location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prsnorg_lctn_alt WHERE org_lctnid='$lctn_id' OR org_lctn_altid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting person (place of origin)-location (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charorg_lctn WHERE org_lctnid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character (place of origin)-location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charorg_lctn_alt WHERE org_lctnid='$lctn_id' OR org_lctn_altid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character (place of origin)-location (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="UPDATE thtr SET thtr_lctnid=NULL WHERE thtr_lctnid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error nullifying theatre (location link)-location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thtr_lctn_alt WHERE thtr_lctnid='$lctn_id' OR thtr_lctn_altid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theatre (location link)-location (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM comp_lctn WHERE comp_lctnid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company (location)-location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM comp_lctn_alt WHERE comp_lctnid='$lctn_id' OR comp_lctn_altid='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company (location)-location (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM lctn WHERE lctn_id='$lctn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS LOCATION HAS BEEN DELETED FROM THE DATABASE:'.' '.html($lctn_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $lctn_id=cln($_POST['lctn_id']);
    $sql= "SELECT lctn_url
          FROM lctn
          WHERE lctn_id='$lctn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring location URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['lctn_url']);
    exit();
  }
?>