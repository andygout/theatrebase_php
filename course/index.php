<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $crs_id=cln($_POST['crs_id']);
    $sql= "SELECT comp_nm, comp_sffx_num, crs_typ_nm, crs_yr_strt, crs_yr_end, crs_sffx_num, crs_dt_strt, crs_dt_strt_frmt, crs_dt_end, crs_dt_end_frmt
          FROM crs
          INNER JOIN comp ON crs_schlid=comp_id INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE crs_id='$crs_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['comp_sffx_num']) {$crs_schl_sffx_num=html($row['comp_sffx_num']); $crs_schl_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';}
    else {$crs_schl_sffx_num=''; $crs_schl_sffx_rmn='';}
    if($row['crs_sffx_num']) {$crs_sffx_num=html($row['crs_sffx_num']); $crs_sffx_rmn=' ('.romannumeral($row['crs_sffx_num']).')';}
    else {$crs_sffx_num=''; $crs_sffx_rmn='';}
    if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']); $crs_yr_strt=html($row['crs_yr_strt']); $crs_yr_end=html($row['crs_yr_end']);}
    else {$crs_yr=$row['crs_yr_strt']; $crs_yr_strt=html($row['crs_yr_strt']); $crs_yr_end='';}
    $pagetab='Edit: '.html($row['comp_nm'].$crs_schl_sffx_rmn.': '.$row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
    $pagetitle=html($row['comp_nm'].$crs_schl_sffx_rmn).':</br>'.html($row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
    $crs_schl_nm=html($row['comp_nm']);
    $crs_typ_nm=html($row['crs_typ_nm']);
    $crs_dt_strt=html($row['crs_dt_strt']);
    $crs_dt_strt_frmt=html($row['crs_dt_strt_frmt']);
    $crs_dt_end=html($row['crs_dt_end']);
    $crs_dt_end_frmt=html($row['crs_dt_end_frmt']);

    $sql="SELECT cdntr_rl_id, cdntr_rl FROM crscdntrrl WHERE crsid='$crs_id' ORDER BY cdntr_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring coordinator (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['cdntr_rl']) {$cdntr_rl=$row['cdntr_rl'].'::';} else {$cdntr_rl='';}
      $cdntr_rls[$row['cdntr_rl_id']]=array('cdntr_rl'=>$cdntr_rl, 'cdntrs'=>array());
    }

    $sql= "SELECT cdntr_rlid, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, cdntr_sb_rl, cdntr_ordr, comp_bool
          FROM crscdntr
          INNER JOIN comp ON cdntr_compid=comp_id
          WHERE crsid='$crs_id' AND cdntr_prsnid=0
          UNION
          SELECT cdntr_rlid, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, cdntr_sb_rl, cdntr_ordr, comp_bool
          FROM crscdntr
          INNER JOIN prsn ON cdntr_prsnid=prsn_id
          WHERE crsid='$crs_id' AND cdntr_compid=0
          ORDER BY cdntr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring coordinator data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['cdntr_sb_rl']) {$cdntr_sb_rl=$row['cdntr_sb_rl'].'~~';} else {$cdntr_sb_rl='';}
        if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_cdntr_nm=$cdntr_sb_rl.$row['comp_nm1'].$comp_sffx_num.'||';
        }
        else
        {$comp_cdntr_nm='';}
        $prsn_cdntr_nm='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['cdntr_sb_rl']) {$cdntr_sb_rl=$row['cdntr_sb_rl'].'~~';} else {$cdntr_sb_rl='';}
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_cdntr_nm=$cdntr_sb_rl.$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num;
        }
        else
        {$prsn_cdntr_nm='';}
        $comp_cdntr_nm='';
      }
      $cdntr_rls[$row['cdntr_rlid']]['cdntrs'][$row['comp_id']]=array('comp_cdntr_nm'=>$comp_cdntr_nm, 'prsn_cdntr_nm'=>$prsn_cdntr_nm, 'comp_rls'=>array());
    }

    $sql= "SELECT cdntr_rlid, cdntr_compid, cdntr_comp_rl_id, cdntr_comprl
          FROM crscdntr pp
          INNER JOIN crscdntr_comprl ppcr ON pp.crsid=ppcr.crsid
          WHERE pp.crsid='$crs_id' AND cdntr_comp_rlid=cdntr_comp_rl_id
          GROUP BY cdntr_rlid, cdntr_compid, cdntr_comp_rl_id
          ORDER BY cdntr_comp_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring coordinator (company people roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['cdntr_comprl']) {$cdntr_comprl=$row['cdntr_comprl'].'~~';} else {$cdntr_comprl='';}
      $cdntr_rls[$row['cdntr_rlid']]['cdntrs'][$row['cdntr_compid']]['comp_rls'][$row['cdntr_comp_rl_id']]=array('cdntr_comprl'=>$cdntr_comprl, 'cdntrcomp_ppl'=>array());
    }

    $sql= "SELECT cdntr_rlid, cdntr_compid, cdntr_comp_rlid, cdntr_sb_rl, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM crscdntr
          INNER JOIN prsn ON cdntr_prsnid=prsn_id
          WHERE crsid='$crs_id' AND cdntr_compid!=0
          ORDER BY cdntr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring coordinator (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['cdntr_sb_rl']) {$cdntr_sb_rl=$row['cdntr_sb_rl'].'^^';} else {$cdntr_sb_rl='';}
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $cdntr_rls[$row['cdntr_rlid']]['cdntrs'][$row['cdntr_compid']]['comp_rls'][$row['cdntr_comp_rlid']]['cdntrcomp_ppl'][]=$cdntr_sb_rl.$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num;
    }

    if(!empty($cdntr_rls))
    {
      $cdntr_array=array();
      foreach($cdntr_rls as $cdntr_rl)
      {
        $cdntr_comp_rls_array=array();
        foreach($cdntr_rl['cdntrs'] as $cdntr)
        {
          $cdntr_comp_ppl_array=array();
          foreach($cdntr['comp_rls'] as $comp_rl)
          {
            $cdntrcomp_ppl_list=implode('Â¬Â¬', $comp_rl['cdntrcomp_ppl']);
            $cdntr_comp_ppl_array[]=$comp_rl['cdntr_comprl'].$cdntrcomp_ppl_list;
          }
          if(!empty($cdntr_comp_ppl_array)) {$cdntr_comp_ppl_list=implode('//', $cdntr_comp_ppl_array);} else {$cdntr_comp_ppl_list='';}
          $cdntr_comp_rls_array[]=$cdntr['comp_cdntr_nm'].$cdntr['prsn_cdntr_nm'].$cdntr_comp_ppl_list;
        }
        if(!empty($cdntr_comp_rls_array)) {$cdntr_comp_rl_list=implode('>>', $cdntr_comp_rls_array);} else {$cdntr_comp_rl_list='';}
        $cdntr_array[]=$cdntr_rl['cdntr_rl'].$cdntr_comp_rl_list;
      }
      $cdntr_list=html(implode(',,', $cdntr_array));
    }
    else
    {$cdntr_list='';}

    $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, stff_prsn_rl
          FROM crsstff_prsn
          INNER JOIN prsn ON stff_prsnid=prsn_id
          WHERE crsid='$crs_id'
          ORDER BY stff_prsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result){$error='Error acquiring course staff (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prsn_sffx_num']) {$stff_prsn_sffx_num='--'.$row['prsn_sffx_num'];}
      else {$stff_prsn_sffx_num='';}
      $stff_ppl[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$stff_prsn_sffx_num.'::'.$row['stff_prsn_rl'];
    }
    if(!empty($stff_ppl)) {$stff_prsn_rl_list=html(implode(',,', $stff_ppl));} else {$stff_prsn_rl_list='';}

    $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, stdnt_prsn_rl
          FROM crsstdnt_prsn
          INNER JOIN prsn ON stdnt_prsnid=prsn_id
          WHERE crsid='$crs_id'
          ORDER BY prsn_lst_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course student (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prsn_sffx_num']) {$stdnt_prsn_sffx_num='--'.$row['prsn_sffx_num'];}
      else {$stdnt_prsn_sffx_num='';}
      $stdnt_ppl[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$stdnt_prsn_sffx_num.'::'.$row['stdnt_prsn_rl'];
    }
    if(!empty($stdnt_ppl)) {$stdnt_prsn_rl_list=html(implode(',,', $stdnt_ppl));} else {$stdnt_prsn_rl_list='';}

    $textarea='';
    $crs_id=html($crs_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $crs_id=cln($_POST['crs_id']);
    $crs_schl_nm=trim(cln($_POST['crs_schl_nm']));
    $crs_schl_sffx_num=trim(cln($_POST['crs_schl_sffx_num']));
    $crs_typ_nm=trim(cln($_POST['crs_typ_nm']));
    $crs_yr_strt=cln($_POST['crs_yr_strt']);
    $crs_yr_end=cln($_POST['crs_yr_end']);
    $crs_sffx_num=cln($_POST['crs_sffx_num']);
    $crs_dt_strt=cln($_POST['crs_dt_strt']);
    if($_POST['crs_dt_strt_frmt']=='1') {$crs_dt_strt_frmt='1';}
    if($_POST['crs_dt_strt_frmt']=='2') {$crs_dt_strt_frmt='2';}
    if($_POST['crs_dt_strt_frmt']=='3') {$crs_dt_strt_frmt='3';}
    if($_POST['crs_dt_strt_frmt']=='4') {$crs_dt_strt_frmt='4';}
    $crs_dt_end=cln($_POST['crs_dt_end']);
    if($_POST['crs_dt_end_frmt']=='1') {$crs_dt_end_frmt='1';}
    if($_POST['crs_dt_end_frmt']=='2') {$crs_dt_end_frmt='2';}
    if($_POST['crs_dt_end_frmt']=='3') {$crs_dt_end_frmt='3';}
    if($_POST['crs_dt_end_frmt']=='4') {$crs_dt_end_frmt='4';}
    $cdntr_list=cln($_POST['cdntr_list']);
    $stff_prsn_rl_list=cln($_POST['stff_prsn_rl_list']);
    $stdnt_prsn_rl_list=cln($_POST['stdnt_prsn_rl_list']);

    $errors=array();

    if(!preg_match('/\S+/', $crs_schl_nm))
    {$errors['crs_schl_nm']='**You must enter a course school name.**';}
    elseif(preg_match('/;;/', $crs_schl_nm) || preg_match('/--/', $crs_schl_nm) || preg_match('/::/', $crs_schl_nm) || preg_match('/##/', $crs_schl_nm) ||
    preg_match('/\|\|/', $crs_schl_nm) || preg_match('/,,/', $crs_schl_nm) || preg_match('/==/', $crs_schl_nm) || preg_match('/~~/', $crs_schl_nm) ||
    preg_match('/>>/', $crs_schl_nm) || preg_match('/@@/', $crs_schl_nm) || preg_match('/\/\//', $crs_schl_nm))
    {$errors['crs_schl_nm']='</br>**Course school name cannot include any of the following: [;;], [--], [::], [##], [||], [,,], [==], [~~], [>>], [@@], [//].**';}

    if(preg_match('/^[0]*$/', $crs_schl_sffx_num) || !$crs_schl_sffx_num)
    {$crs_schl_sffx_num='0'; $crs_schl_sffx_rmn=''; $crs_schl_sffx_rmn_session='';}
    elseif(preg_match('/^[1-9][0-9]{0,1}$/', $crs_schl_sffx_num))
    {$crs_schl_sffx_rmn=' ('.romannumeral($crs_schl_sffx_num).')'; $crs_schl_sffx_rmn_session=' ('.romannumeral($_POST['crs_schl_sffx_num']).')';}
    else
    {$crs_schl_sffx_rmn=''; $errors['crs_schl_sffx']='**The suffix must be a valid integer between 1 and 99 (with no leading 0) or left blank (or as 0).**';}

    $crs_schl_url=generateurl($crs_schl_nm.$crs_schl_sffx_rmn);

    if(strlen($crs_schl_nm)>255 || strlen($crs_schl_url)>255)
    {$errors['crs_schl_excss_lngth']='</br>**Course school name and its URL are allowed a maximum of 255 characters each.**';}

    $crs_schl_alph=alph($crs_schl_nm);

    if(count($errors)==0)
    {
      $sql= "SELECT comp_nm, comp_sffx_num
            FROM comp
            WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$crs_schl_nm' AND comp_sffx_num='$crs_schl_sffx_num')
            AND comp_url='$crs_schl_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing course school (company) URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
        $errors['crs_schl_url']='</br>**Duplicate URL exists. Did you mean to type: '.html($row['comp_nm'].$comp_sffx_num).'?**';
      }
    }

    if(!preg_match('/\S+/', $crs_typ_nm))
    {$errors['crs_typ_nm']='**You must enter a course type name.**';}
    elseif(strlen($crs_typ_nm)>255)
    {$errors['crs_typ_nm']='</br>**Course type name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/::/', $crs_typ_nm) || preg_match('/##/', $crs_typ_nm) || preg_match('/,,/', $crs_typ_nm))
    {$errors['crs_typ_nm']='**Course type name cannot include the following: [::], [##], [,,].**';}
    else
    {
      $crs_typ_url=generateurl($crs_typ_nm);

      $sql= "SELECT crs_typ_nm
            FROM crs_typ
            WHERE NOT EXISTS (SELECT 1 FROM crs_typ WHERE crs_typ_nm='$crs_typ_nm')
            AND crs_typ_url='$crs_typ_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing course-type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {$errors['crs_typ_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html($row['crs_typ_nm']).'?**';}
    }

    if(!preg_match('/^[1-9][0-9]{3}$/', $crs_yr_strt)) {$errors['crs_yr_strt']='**You must enter a valid year.**'; $crs_yr_strt_num=NULL;}
    else {$crs_yr_strt_num=$crs_yr_strt;}

    if($crs_yr_end)
    {
      if(!preg_match('/^[1-9][0-9]{3}$/', $crs_yr_end))
      {$errors['crs_yr_end']='**You must enter a valid year or leave blank.**'; $crs_yr_end_num=NULL;}
      else
      {$crs_yr_end_num=$crs_yr_end;}
    }
    else {$crs_yr_end_num=NULL; $crs_yr_end=$crs_yr_strt;}

    if($crs_yr_strt_num && $crs_yr_end_num)
    {
      if($crs_yr_strt_num >= $crs_yr_end_num)
      {$errors['crs_yr_strt']='**Must be earlier than course end year.**'; $errors['crs_yr_end']='**Must be later than course start year.**';}
    }

    if(preg_match('/^[0]*$/', $crs_sffx_num) || !$crs_sffx_num)
    {$crs_sffx_num='0'; $crs_sffx_rmn=''; $crs_sffx_rmn_session='';}
    elseif(preg_match('/^[1-9][0-9]{0,1}$/', $crs_sffx_num))
    {$crs_sffx_rmn=' ('.romannumeral($crs_sffx_num).')'; $crs_sffx_rmn_session=' ('.romannumeral($_POST['crs_sffx_num']).')';}
    else
    {$crs_sffx_rmn=''; $errors['crs_sffx']='**The suffix must be a valid integer between 1 and 99 (with no leading 0) or left blank (or as 0).**';}

    if(count($errors)==0)
    {
      if($crs_yr_strt==$crs_yr_end)
      {$crs_yr_url=generateurl($crs_yr_strt.$crs_sffx_rmn); $crs_yr_end_session='';}
      else
      {$crs_yr_url=generateurl($crs_yr_strt.'-'.$crs_yr_end.$crs_sffx_rmn); $crs_yr_end_session=preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $_POST['crs_yr_end']);}

      $crs_session=$_POST['crs_schl_nm'].$crs_schl_sffx_rmn_session.': '.$_POST['crs_typ_nm'].' ('.$_POST['crs_yr_strt'].$crs_yr_end_session.')'.$crs_sffx_rmn_session;

      $sql= "SELECT crs_id, comp_nm, comp_sffx_num, crs_typ_nm, crs_yr_strt, crs_yr_end, crs_sffx_num
            FROM crs
            INNER JOIN comp ON crs_schlid=comp_id
            INNER JOIN crs_typ ON crs_typid=crs_typ_id
            WHERE crs_yr_url='$crs_yr_url' AND comp_url='$crs_schl_url' AND crs_typ_url='$crs_typ_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing course school-type-year combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['crs_id']!==$crs_id)
      {
        if($row['comp_sffx_num']) {$crs_schl_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';} else {$crs_schl_sffx_rmn='';}
        if($row['crs_sffx_num']) {$crs_sffx_rmn=' ('.romannumeral($row['crs_sffx_num']).')';} else {$crs_sffx_rmn='';}
        if($row['crs_yr_strt']!==$row['crs_yr_end'])
        {$crs_yr=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);}
        else
        {$crs_yr=$row['crs_yr_strt'];}
        $errors['crs_schl_typ_yr']='</br>**Given course school, type and year(s) already exists for: '.html($row['comp_nm'].$crs_schl_sffx_rmn.': '.$row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn).'**';
      }
    }

    if($crs_dt_strt)
    {
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $crs_dt_strt))
      {$errors['crs_dt_strt']='**You must enter a valid course start date in the prescribed format or leave empty.**'; $crs_dt_strt=NULL;}
      else
      {
        list($crs_dt_strt_YYYY, $crs_dt_strt_MM, $crs_dt_strt_DD)=explode('-', $crs_dt_strt);
        if(!checkdate((int)$crs_dt_strt_MM, (int)$crs_dt_strt_DD, (int)$crs_dt_strt_YYYY))
        {$errors['crs_dt_strt']='**You must enter a valid course start date or leave empty.**'; $crs_dt_strt=NULL;}
        elseif(preg_match('/^[1-9][0-9]{3}$/', $crs_yr_strt) && $crs_dt_strt_YYYY!==$crs_yr_strt)
        {$errors['crs_dt_strt']='**Course start date must have the same year as the course start year.**';}
      }
    }
    else
    {$crs_dt_strt=NULL;}

    if($crs_dt_end)
    {
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $crs_dt_end))
      {$errors['crs_dt_end']='**You must enter a valid course end date in the prescribed format or leave empty.**'; $crs_dt_end=NULL;}
      else
      {
        list($crs_dt_end_YYYY, $crs_dt_end_MM, $crs_dt_end_DD)=explode('-', $crs_dt_end);
        if(!checkdate((int)$crs_dt_end_MM, (int)$crs_dt_end_DD, (int)$crs_dt_end_YYYY))
        {$errors['crs_dt_end']='**You must enter a valid course end date or leave empty.**'; $crs_dt_end=NULL;}
        elseif(preg_match('/^[1-9][0-9]{3}$/', $crs_yr_end) && $crs_dt_end_YYYY!==$crs_yr_end)
        {$errors['crs_dt_end']='**Course end date must have the same year as the course end year.**';}
      }
    }
    else
    {$crs_dt_end=NULL;}

    if($crs_dt_strt && $crs_dt_end && $crs_dt_strt>$crs_dt_end)
    {
      $errors['crs_dt_strt']='**Must be earlier than or equal to course start date.**';
      $errors['crs_dt_end']='**Must be later than or equal to course end date.**';
    }

    if(preg_match('/\S+/', $cdntr_list))
    {
      $cdntr_comp_prsn_rls=explode(',,', $_POST['cdntr_list']);
      if(count($cdntr_comp_prsn_rls)>250)
      {$errors['cdntr_rl_array_excss']='**Maximum of 250 coordinator roles allowed.**';}
      else
      {
        $cdntr_empty_err_arr=array(); $cdntr_cln_excss_err_arr=array(); $cdntr_cln_err_arr=array();
        $cdntr_comp_prsn_empty_err_arr=array(); $cdntr_pipe_excss_err_arr=array(); $cdntr_comp_tld_excss_err_arr=array();
        $cdntr_comp_tld_err_arr=array(); $cdntr_pipe_err_arr=array(); $cdntr_prsn_tld_excss_err_arr=array();
        $cdntr_prsn_tld_err_arr=array(); $cdntr_compprsn_rl_empty_err_arr=array(); $cdntr_compprsn_tld_excss_err_arr=array();
        $cdntr_compprsn_empty_err_arr=array(); $cdntr_compprsn_crt_excss_err_arr=array(); $cdntr_compprsn_crt_err_arr=array();
        $cdntr_compprsn_tld_err_arr=array(); $cdntr_comp_hyphn_excss_err_arr=array(); $cdntr_comp_hyphn_excss_err_arr=array();
        $cdntr_comp_sffx_err_arr=array(); $cdntr_comp_hyphn_err_arr=array(); $cdntr_comp_dplct_arr=array();
        $cdntr_comp_url_err_arr=array(); $cdntr_prsn_hyphn_excss_err_arr=array(); $cdntr_prsn_sffx_err_arr=array();
        $cdntr_prsn_hyphn_err_arr=array(); $cdntr_prsn_smcln_excss_err_arr=array(); $cdntr_prsn_dplct_arr=array();
        $cdntr_prsn_smcln_err_arr=array(); $cdntr_prsn_nm_err_arr=array(); $cdntr_prsn_url_err_arr=array();
        foreach($cdntr_comp_prsn_rls as $cdntr_comp_prsn_rl)
        {
          $cdntr_comp_prsn_rl=trim($cdntr_comp_prsn_rl);

          if(!preg_match('/\S+/', $cdntr_comp_prsn_rl))
          {
            $cdntr_empty_err_arr[]=$cdntr_comp_prsn_rl;
            if(count($cdntr_empty_err_arr)==1) {$errors['cdntr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['cdntr_empty']='</br>**There are '.count($cdntr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(preg_match('/\S+/', $cdntr_comp_prsn_rl))
            {
              if(substr_count($cdntr_comp_prsn_rl, '::')>1)
              {
                $cdntr_cln_excss_err_arr[]=$cdntr_comp_prsn_rl;
                $errors['cdntr_cln_excss']='</br>**You may only use [::] once per coordinator-role coupling. Please amend: '.html(implode(' / ', $cdntr_cln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*::.*\S+/', $cdntr_comp_prsn_rl))
              {
                list($cdntr_rl, $cdntr_comp_prsn_list)=explode('::', $cdntr_comp_prsn_rl);
                $cdntr_rl=trim($cdntr_rl); $cdntr_comp_prsn_list=trim($cdntr_comp_prsn_list);

                if(strlen($cdntr_rl)>255)
                {$errors['cdntr_rl']='</br>**Ccoordinator role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                $cdntr_comps_ppl=explode('>>', $cdntr_comp_prsn_list);
                $cdntr_rl_ttl_array=array(); $cdntr_comp_nm_array=array(); $cdntr_prsn_nm_array=array();
                foreach($cdntr_comps_ppl as $cdntr_comp_prsn)
                {
                  $cdntr_comp_errors=0; $cdntr_prsn_errors=0;

                  $cdntr_comp_prsn=trim($cdntr_comp_prsn);
                  if(!preg_match('/\S+/', $cdntr_comp_prsn))
                  {
                    $cdntr_comp_prsn_empty_err_arr[]=$cdntr_comp_prsn;
                    if(count($cdntr_comp_prsn_empty_err_arr)==1) {$errors['cdntr_comp_prsn_empty']='</br>**There is 1 empty entry in a person arrray (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                    else {$errors['cdntr_comp_prsn_empty']='</br>**There are '.count($cdntr_comp_prsn_empty_err_arr).' empty entries in person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                  }
                  else
                  {
                    if(substr_count($cdntr_comp_prsn, '||')>1)
                    {
                      $cdntr_prsn_nm_list=''; $cdntr_pipe_excss_err_arr[]=$cdntr_comp_prsn;
                      $errors['cdntr_pipe_excss']='</br>**You may only use [||] once per coordinator company-members coupling. Please amend: '.html(implode(' / ', $cdntr_pipe_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/\|\|/', $cdntr_comp_prsn))
                    {
                      if(preg_match('/\S+.*\|\|(.*\S+)?/', $cdntr_comp_prsn))
                      {
                        list($cdntr_comp_nm, $cdntr_prsn_nm_list)=explode('||', $cdntr_comp_prsn);
                        $cdntr_comp_nm=trim($cdntr_comp_nm); $cdntr_prsn_nm_list=trim($cdntr_prsn_nm_list);

                        if(substr_count($cdntr_comp_nm, '~~')>1)
                        {
                          $cdntr_comp_errors++; $cdntr_comp_tld_excss_err_arr[]=$cdntr_comp_nm;
                          $errors['cdntr_comp_tld_excss']='</br>**You may only use [~~] once per coordinator (company)-role coupling. Please amend: '.html(implode(' / ', $cdntr_comp_tld_excss_err_arr)).'.**';
                        }
                        elseif(preg_match('/\S+.*~~.*\S+/', $cdntr_comp_nm))
                        {
                          list($cdntr_comp_rl, $cdntr_comp_nm)=explode('~~', $cdntr_comp_nm);
                          $cdntr_comp_rl=trim($cdntr_comp_rl); $cdntr_comp_nm=trim($cdntr_comp_nm);

                          if(strlen($cdntr_comp_rl)>255)
                          {$cdntr_comp_errors++; $errors['cdntr_comp_rl']='</br>**Ccoordinator (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                        }
                        elseif(substr_count($cdntr_comp_nm, '~~')==1)
                        {
                          $cdntr_comp_errors++; $cdntr_comp_tld_err_arr[]=$cdntr_comp_nm;
                          $errors['cdntr_comp_tld']='</br>**Coordinator (company)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $cdntr_comp_tld_err_arr)).'**';
                        }

                        if($cdntr_comp_errors==0) {$cdntr_comp_nm_array[]=$cdntr_comp_nm; $cdntr_rl_ttl_array[]=$cdntr_comp_nm;}
                      }
                      else
                      {
                        $cdntr_prsn_nm_list=''; $cdntr_pipe_err_arr[]=$cdntr_comp_prsn;
                        $errors['cdntr_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $cdntr_pipe_err_arr)).'.**';
                      }
                    }
                    else
                    {
                      if(substr_count($cdntr_comp_prsn, '~~')>1)
                      {
                        $cdntr_prsn_errors++; $cdntr_prsn_tld_excss_err_arr[]=$cdntr_comp_prsn;
                        $errors['cdntr_prsn_tld_excss']='</br>**You may only use [~~] once per coordinator (person)-role coupling. Please amend: '.html(implode(' / ', $cdntr_prsn_tld_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*~~.*\S+/', $cdntr_comp_prsn))
                      {
                        list($cdntr_prsn_rl, $cdntr_comp_prsn)=explode('~~', $cdntr_comp_prsn);
                        $cdntr_prsn_rl=trim($cdntr_prsn_rl); $cdntr_comp_prsn=trim($cdntr_comp_prsn);

                        if(strlen($cdntr_prsn_rl)>255)
                        {$cdntr_prsn_errors++; $errors['cdntr_prsn_rl']='</br>**Coordinator (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($cdntr_comp_prsn, '~~')==1)
                      {
                        $cdntr_prsn_errors++; $cdntr_prsn_tld_err_arr[]=$cdntr_comp_prsn;
                        $errors['cdntr_prsn_tld']='</br>**Coordinator (person)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $cdntr_prsn_tld_err_arr)).'**';
                      }

                      $cdntr_prsn_nm_list='';
                      if($cdntr_prsn_errors==0) {$cdntr_prsn_nm_array[]=$cdntr_comp_prsn; $cdntr_rl_ttl_array[]=$cdntr_comp_prsn;}
                    }

                    if(preg_match('/\S+/', $cdntr_prsn_nm_list))
                    {
                      $cdntr_prsn_nms=explode('//', $cdntr_prsn_nm_list);
                      foreach($cdntr_prsn_nms as $cdntr_prsn_nm)
                      {
                        $cdntr_prsn_nm=trim($cdntr_prsn_nm);
                        if(!preg_match('/\S+/', $cdntr_prsn_nm))
                        {
                          $cdntr_compprsn_rl_empty_err_arr[]=$cdntr_prsn_nm;
                          if(count($cdntr_compprsn_rl_empty_err_arr)==1) {$errors['cdntr_compprsn_rl_empty']='</br>**There is 1 empty entry in a company member-role array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                          else {$errors['cdntr_compprsn_rl_empty']='</br>**There are '.count($cdntr_compprsn_rl_empty_err_arr).' empty entries in company member-role arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                        }
                        else
                        {
                          if(substr_count($cdntr_prsn_nm, '~~')>1)
                          {
                            $cdntr_compprsn_tld_excss_err_arr[]=$cdntr_prsn_nm;
                            $errors['cdntr_compprsn_tld_excss']='</br>**You may only use [~~] once per coordinator (company person)-role coupling. Please amend: '.html(implode(' / ', $cdntr_compprsn_tld_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/\S+.*~~.*\S+/', $cdntr_prsn_nm))
                          {
                            list($cdntr_compprsn_rl, $cdntr_prsn_nm)=explode('~~', $cdntr_prsn_nm);
                            $cdntr_compprsn_rl=trim($cdntr_compprsn_rl); $cdntr_prsn_nm=trim($cdntr_prsn_nm);

                            if(strlen($cdntr_compprsn_rl)>255)
                            {$errors['cdntr_compprsn_rl']='</br>**Coordinator (company person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                            $cdntr_prsn_nms=explode('Â¬Â¬', $cdntr_prsn_nm);
                            foreach($cdntr_prsn_nms as $cdntr_prsn_nm)
                            {
                              $cdntr_prsn_nm=trim($cdntr_prsn_nm);
                              if(!preg_match('/\S+/', $cdntr_prsn_nm))
                              {
                                $cdntr_compprsn_empty_err_arr[]=$cdntr_prsn_nm;
                                if(count($cdntr_compprsn_empty_err_arr)==1) {$errors['cdntr_compprsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive logical negation symbols [¬¬¬¬] or two symbols [¬¬] with no text beforehand or thereafter).**';}
                                else {$errors['cdntr_compprsn_empty']='</br>**There are '.count($cdntr_compprsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive logical negation symbols [¬¬¬¬] or two symbols [¬¬] with no text beforehand or thereafter).**';}
                              }
                              else
                              {
                                if(substr_count($cdntr_prsn_nm, '^^')>1)
                                {
                                  $cdntr_compprsn_crt_excss_err_arr[]=$cdntr_prsn_nm;
                                  $errors['cdntr_compprsn_crt_excss']='</br>**You may only use [^^] once per coordinator-(credit display) role coupling. Please amend: '.html(implode(' / ', $cdntr_compprsn_crt_excss_err_arr)).'.**';
                                }
                                elseif(preg_match('/\S+.*\^\^.*\S+/', $cdntr_prsn_nm))
                                {
                                  list($cdntr_compprsn_sb_rl, $cdntr_prsn_nm)=explode('^^', $cdntr_prsn_nm);
                                  $cdntr_compprsn_sb_rl=trim($cdntr_compprsn_sb_rl); $cdntr_prsn_nm=trim($cdntr_prsn_nm);

                                  if(strlen($cdntr_compprsn_sb_rl)>255)
                                  {$errors['cdntr_compprsn_sb_rl']='</br>**Coordinator (company person) sub-role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                                  $cdntr_prsn_nm_array[]=$cdntr_prsn_nm; $cdntr_rl_ttl_array[]=$cdntr_prsn_nm;
                                }
                                elseif(substr_count($cdntr_prsn_nm, '^^')==1)
                                {$cdntr_compprsn_crt_err_arr[]=$cdntr_prsn_nm;
                                $errors['cdntr_compprsn_crt']='</br>**Coordinator (company person)-(credit display) role assignation must use [^^] in the correct format. Please amend: '.html(implode(' / ', $cdntr_compprsn_crt_err_arr)).'**';}
                                else
                                {$cdntr_prsn_nm_array[]=$cdntr_prsn_nm; $cdntr_rl_ttl_array[]=$cdntr_prsn_nm;}
                              }
                            }
                          }
                          else
                          {
                            $cdntr_compprsn_tld_err_arr[]=$cdntr_prsn_nm;
                            $errors['cdntr_compprsn_tld']='</br>**You must assign a company role to the following using [~~]: '.html(implode(' / ', $cdntr_compprsn_tld_err_arr)).'.**';
                          }
                        }
                      }
                    }

                    if(count($cdntr_rl_ttl_array)>250)
                    {$errors['cdntr_rl_ttl_array_excss']='</br>**Maximum of 250 entries (companies and people per role) allowed.**';}
                  }
                }

                if(count($cdntr_comp_nm_array)>0)
                {
                  foreach($cdntr_comp_nm_array as $cdntr_comp_nm)
                  {
                    $cdntr_comp_errors=0;

                    if(substr_count($cdntr_comp_nm, '--')>1)
                    {
                      $cdntr_comp_errors++; $cdntr_comp_sffx_num='0'; $cdntr_comp_hyphn_excss_err_arr[]=$cdntr_comp_nm;
                      $errors['cdntr_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per coordinator (company). Please amend: '.html(implode(' / ', $cdntr_comp_hyphn_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/^\S+.*--.+$/', $cdntr_comp_nm))
                    {
                      list($cdntr_comp_nm_no_sffx, $cdntr_comp_sffx_num)=explode('--', $cdntr_comp_nm);
                      $cdntr_comp_nm_no_sffx=trim($cdntr_comp_nm_no_sffx); $cdntr_comp_sffx_num=trim($cdntr_comp_sffx_num);

                      if(!preg_match('/^[1-9][0-9]{0,1}$/', $cdntr_comp_sffx_num))
                      {
                        $cdntr_comp_errors++; $cdntr_comp_sffx_num='0'; $cdntr_comp_sffx_err_arr[]=$cdntr_comp_nm;
                        $errors['cdntr_comp_sffx']='</br>**Coordinator (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $cdntr_comp_sffx_err_arr)).'**';
                      }
                      $cdntr_comp_nm=$cdntr_comp_nm_no_sffx;
                    }
                    elseif(substr_count($cdntr_comp_nm, '--')==1)
                    {$cdntr_comp_errors++; $cdntr_comp_sffx_num='0'; $cdntr_comp_hyphn_err_arr[]=$cdntr_comp_nm;
                    $errors['cdntr_comp_hyphn']='</br>**Coordinator (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $cdntr_comp_hyphn_err_arr)).'**';}
                    else
                    {$cdntr_comp_sffx_num='0';}

                    if($cdntr_comp_sffx_num) {$cdntr_comp_sffx_rmn=' ('.romannumeral($cdntr_comp_sffx_num).')';} else {$cdntr_comp_sffx_rmn='';}

                    $cdntr_comp_url=generateurl($cdntr_comp_nm.$cdntr_comp_sffx_rmn);

                    $cdntr_comp_dplct_arr[]=$cdntr_comp_url;
                    if(count(array_unique($cdntr_comp_dplct_arr))<count($cdntr_comp_dplct_arr))
                    {$errors['cdntr_comp_dplct']='</br>**There are entries within the array that create duplicate company URLs.**';}

                    if(strlen($cdntr_comp_nm)>255 || strlen($cdntr_comp_url)>255)
                    {$cdntr_comp_errors++; $errors['cdntr_comp_nm_excss_lngth']='</br>**Coordinator (company) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                    if($cdntr_comp_errors==0)
                    {
                      $cdntr_comp_nm_cln=cln($cdntr_comp_nm);
                      $cdntr_comp_sffx_num_cln=cln($cdntr_comp_sffx_num);
                      $cdntr_comp_url_cln=cln($cdntr_comp_url);

                      $sql= "SELECT comp_nm, comp_sffx_num
                            FROM comp
                            WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$cdntr_comp_nm_cln' AND comp_sffx_num='$cdntr_comp_sffx_num_cln')
                            AND comp_url='$cdntr_comp_url_cln'";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error checking for existing coordinator company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      $row=mysqli_fetch_array($result);
                      if(mysqli_num_rows($result)>0)
                      {
                        if($row['comp_sffx_num']) {$cdntr_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                        else {$cdntr_comp_url_error_sffx_dsply='';}
                        $cdntr_comp_url_err_arr[]=$row['comp_nm'].$cdntr_comp_url_error_sffx_dsply;
                        if(count($cdntr_comp_url_err_arr)==1)
                        {$errors['cdntr_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $cdntr_comp_url_err_arr)).'?**';}
                        else
                        {$errors['cdntr_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $cdntr_comp_url_err_arr)).'?**';}
                      }
                    }
                  }
                }

                if(count($cdntr_prsn_nm_array)>0)
                {
                  foreach($cdntr_prsn_nm_array as $cdntr_prsn_nm)
                  {
                    $cdntr_prsn_nm=trim($cdntr_prsn_nm);
                    $cdntr_prsn_errors=0;
                    if(substr_count($cdntr_prsn_nm, '--')>1)
                    {
                      $cdntr_prsn_errors++; $cdntr_prsn_sffx_num='0'; $cdntr_prsn_hyphn_excss_err_arr[]=$cdntr_prsn_nm;
                      $errors['cdntr_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per coordinator (person). Please amend: '.html(implode(' / ', $cdntr_prsn_hyphn_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/^\S+.*--.+$/', $cdntr_prsn_nm))
                    {
                      list($cdntr_prsn_nm_no_sffx, $cdntr_prsn_sffx_num)=explode('--', $cdntr_prsn_nm);
                      $cdntr_prsn_nm_no_sffx=trim($cdntr_prsn_nm_no_sffx); $cdntr_prsn_sffx_num=trim($cdntr_prsn_sffx_num);

                      if(!preg_match('/^[1-9][0-9]{0,1}$/', $cdntr_prsn_sffx_num))
                      {
                        $cdntr_prsn_errors++; $cdntr_prsn_sffx_num='0'; $cdntr_prsn_sffx_err_arr[]=$cdntr_prsn_nm;
                        $errors['cdntr_prsn_sffx']='</br>**Coordinator (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $cdntr_prsn_sffx_err_arr)).'**';
                      }
                      $cdntr_prsn_nm=$cdntr_prsn_nm_no_sffx;
                    }
                    elseif(substr_count($cdntr_prsn_nm, '--')==1)
                    {$cdntr_prsn_errors++; $cdntr_prsn_sffx_num='0'; $cdntr_prsn_hyphn_err_arr[]=$cdntr_prsn_nm;
                    $errors['cdntr_prsn_hyphn']='</br>**Coordinator (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $cdntr_prsn_hyphn_err_arr)).'**';}
                    else
                    {$cdntr_prsn_sffx_num='0';}

                    if($cdntr_prsn_sffx_num) {$cdntr_prsn_sffx_rmn=' ('.romannumeral($cdntr_prsn_sffx_num).')';} else {$cdntr_prsn_sffx_rmn='';}

                    if(substr_count($cdntr_prsn_nm, ';;')>1)
                    {
                      $cdntr_prsn_errors++; $cdntr_prsn_smcln_excss_err_arr[]=$cdntr_prsn_nm;
                      $errors['cdntr_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $cdntr_prsn_smcln_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/\S+.*;;(.*\S+)?/', $cdntr_prsn_nm))
                    {
                      list($cdntr_prsn_frst_nm, $cdntr_prsn_lst_nm)=explode(';;', $cdntr_prsn_nm);
                      $cdntr_prsn_frst_nm=trim($cdntr_prsn_frst_nm); $cdntr_prsn_lst_nm=trim($cdntr_prsn_lst_nm);

                      if(preg_match('/\S+/', $cdntr_prsn_lst_nm))
                      {$cdntr_prsn_lst_nm_dsply=' '.$cdntr_prsn_lst_nm;}
                      else
                      {$cdntr_prsn_lst_nm_dsply='';}

                      $cdntr_prsn_fll_nm=$cdntr_prsn_frst_nm.$cdntr_prsn_lst_nm_dsply;
                      $cdntr_prsn_url=generateurl($cdntr_prsn_fll_nm.$cdntr_prsn_sffx_rmn);

                      $cdntr_prsn_dplct_arr[]=$cdntr_prsn_url;
                      if(count(array_unique($cdntr_prsn_dplct_arr))<count($cdntr_prsn_dplct_arr))
                      {$errors['cdntr_prsn_dplct']='</br>**There are entries within the array that create duplicate person URLs.**';}

                      if(strlen($cdntr_prsn_fll_nm)>255 || strlen($cdntr_prsn_url)>255)
                      {$cdntr_prsn_errors++; $errors['cdntr_prsn_excss_lngth']='</br>**Coordinator (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                    }
                    else
                    {
                      $cdntr_prsn_errors++; $cdntr_prsn_smcln_err_arr[]=$cdntr_prsn_nm;
                      $errors['cdntr_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $cdntr_prsn_smcln_err_arr)).'.**';
                    }

                    if($cdntr_prsn_errors==0)
                    {
                      $cdntr_prsn_frst_nm_cln=cln($cdntr_prsn_frst_nm);
                      $cdntr_prsn_lst_nm_cln=cln($cdntr_prsn_lst_nm);
                      $cdntr_prsn_fll_nm_cln=cln($cdntr_prsn_fll_nm);
                      $cdntr_prsn_sffx_num_cln=cln($cdntr_prsn_sffx_num);
                      $cdntr_prsn_url_cln=cln($cdntr_prsn_url);

                      $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                            FROM prsn
                            WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$cdntr_prsn_frst_nm_cln' AND prsn_lst_nm='$cdntr_prsn_lst_nm_cln')
                            AND prsn_fll_nm='$cdntr_prsn_fll_nm_cln' AND prsn_sffx_num='$cdntr_prsn_sffx_num_cln'";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error checking for coordinator person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      $row=mysqli_fetch_array($result);
                      if(mysqli_num_rows($result)>0)
                      {
                        if($row['prsn_sffx_num']) {$cdntr_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                        else {$cdntr_prsn_nm_error_sffx_dsply='';}
                        $cdntr_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$cdntr_prsn_nm_error_sffx_dsply;
                        if(count($cdntr_prsn_nm_err_arr)==1)
                        {$errors['cdntr_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $cdntr_prsn_nm_err_arr)).'.**';}
                        else
                        {$errors['cdntr_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $cdntr_prsn_nm_err_arr)).'.**';}
                      }

                      $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                            FROM prsn
                            WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$cdntr_prsn_fll_nm_cln' AND prsn_sffx_num='$cdntr_prsn_sffx_num_cln')
                            AND prsn_url='$cdntr_prsn_url_cln'";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error checking for existing coordinator person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      $row=mysqli_fetch_array($result);
                      if(mysqli_num_rows($result)>0)
                      {
                        if($row['prsn_sffx_num']) {$cdntr_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                        else {$cdntr_prsn_url_error_sffx_dsply='';}
                        $cdntr_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$cdntr_prsn_url_error_sffx_dsply;
                        if(count($cdntr_prsn_url_err_arr)==1)
                        {$errors['cdntr_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $cdntr_prsn_url_err_arr)).'?**';}
                        else
                        {$errors['cdntr_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $cdntr_prsn_url_err_arr)).'?**';}
                      }
                    }
                  }
                }
              }
              else
              {
                $cdntr_cln_err_arr[]=$cdntr_comp_prsn_rl;
                $errors['cdntr_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $cdntr_cln_err_arr)).'.**';
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $stff_prsn_rl_list))
    {
      $stff_prsn_nm_rls=explode(',,', $_POST['stff_prsn_rl_list']);

      if(count($stff_prsn_nm_rls)>250)
      {
        $errors['stff_prsn_nm_rl_array_excss']='**Maximum of 250 entries allowed.**';
      }
      else
      {
        $stff_prsn_empty_err_arr=array(); $stff_prsn_cln_err_arr=array(); $stff_prsn_cln_excss_err_arr=array();
        $stff_prsn_sffx_err_arr=array(); $stff_prsn_hyphn_err_arr=array(); $stff_prsn_hyphn_excss_err_arr=array();
        $stff_prsn_dplct_arr=array(); $stff_prsn_smcln_err_arr=array(); $stff_prsn_smcln_excss_err_arr=array();
        $stff_prsn_nm_err_arr=array(); $stff_prsn_url_err_arr=array();
        foreach($stff_prsn_nm_rls as $stff_prsn_nm_rl)
        {
          $stff_prsn_errors=0;

          if(!preg_match('/\S+/', $stff_prsn_nm_rl))
          {
            $stff_prsn_empty_err_arr[]=$stff_prsn_nm_rl;
            if(count($stff_prsn_empty_err_arr)==1) {$errors['stff_prsn_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['stff_prsn_empty']='</br>**There are '.count($stff_prsn_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($stff_prsn_nm_rl, '::')>1)
            {
              $stff_prsn_errors++; $stff_prsn_nm=trim($stff_prsn_nm_rl);
              $stff_prsn_cln_excss_err_arr[]=$stff_prsn_nm_rl;
              $errors['stff_prsn_cln_excss']='</br>**You may only use [::] once per course staff-role coupling. Please amend: '.html(implode(' / ', $stff_prsn_cln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*::.*\S+/', $stff_prsn_nm_rl))
            {
              list($stff_prsn_nm, $stff_prsn_rl)=explode('::', $stff_prsn_nm_rl);
              $stff_prsn_nm=trim($stff_prsn_nm); $stff_prsn_rl=trim($stff_prsn_rl);

              if(strlen($stff_prsn_rl)>255)
              {$errors['stff_prsn_rl_excss_lngth']='</br>**Course staff (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
            }
            else
            {
              $stff_prsn_errors++; $stff_prsn_nm=trim($stff_prsn_nm_rl);
              $stff_prsn_cln_err_arr[]=$stff_prsn_nm_rl;
              $errors['stff_prsn_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $stff_prsn_cln_err_arr)).'.**';
            }

            if(substr_count($stff_prsn_nm, '--')>1)
            {
              $stff_prsn_errors++; $stff_prsn_sffx_num='0'; $stff_prsn_hyphn_excss_err_arr[]=$stff_prsn_nm;
              $errors['stff_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per course staff member. Please amend: '.html(implode(' / ', $stff_prsn_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $stff_prsn_nm))
            {
              list($stff_prsn_nm_no_sffx, $stff_prsn_sffx_num)=explode('--', $stff_prsn_nm);
              $stff_prsn_nm_no_sffx=trim($stff_prsn_nm_no_sffx); $stff_prsn_sffx_num=trim($stff_prsn_sffx_num);

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $stff_prsn_sffx_num))
              {
                $stff_prsn_errors++; $stff_prsn_sffx_num='0'; $stff_prsn_sffx_err_arr[]=$stff_prsn_nm;
                $errors['stff_prsn_sffx']= '</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $stff_prsn_sffx_err_arr)).'**';
              }
              $stff_prsn_nm=$stff_prsn_nm_no_sffx;
            }
            elseif(substr_count($stff_prsn_nm, '--')==1)
            {$stff_prsn_errors++; $stff_prsn_sffx_num='0'; $stff_prsn_hyphn_err_arr[]=$stff_prsn_nm;
            $errors['stff_prsn_hyphn']='</br>**Staff member suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $stff_prsn_hyphn_err_arr)).'**';}
            else
            {$stff_prsn_sffx_num='0';}

            if($stff_prsn_sffx_num) {$stff_prsn_sffx_rmn=' ('.romannumeral($stff_prsn_sffx_num).')';} else {$stff_prsn_sffx_rmn='';}

            if(substr_count($stff_prsn_nm, ';;')>1)
            {
              $stff_prsn_errors++; $stff_prsn_frst_nm=''; $stff_prsn_lst_nm=''; $stff_prsn_fll_nm=''; $stff_prsn_url='';
              $stff_prsn_smcln_excss_err_arr[]=$stff_prsn_nm;
              $errors['stff_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $stff_prsn_smcln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*;;(.*\S+)?/', $stff_prsn_nm))
            {
              list($stff_prsn_frst_nm, $stff_prsn_lst_nm)=explode(';;', $stff_prsn_nm);
              $stff_prsn_frst_nm=trim($stff_prsn_frst_nm); $stff_prsn_lst_nm=trim($stff_prsn_lst_nm);

              if(preg_match('/\S+/', $stff_prsn_lst_nm)) {$stff_prsn_lst_nm_dsply=' '.$stff_prsn_lst_nm;}
              else {$stff_prsn_lst_nm_dsply='';}

              $stff_prsn_fll_nm=$stff_prsn_frst_nm.$stff_prsn_lst_nm_dsply;
              $stff_prsn_url=generateurl($stff_prsn_fll_nm.$stff_prsn_sffx_rmn);

              $stff_prsn_dplct_arr[]=$stff_prsn_url;
              if(count(array_unique($stff_prsn_dplct_arr))<count($stff_prsn_dplct_arr))
              {$errors['stff_prsn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($stff_prsn_fll_nm)>255 || strlen($stff_prsn_url)>255)
              {$stff_prsn_errors++; $errors['stff_prsn_excss_lngth']='</br>**Course staff (person) full name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
            }
            else
            {
              $stff_prsn_errors++; $stff_prsn_smcln_err_arr[]=$stff_prsn_nm;
              $errors['stff_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $stff_prsn_smcln_err_arr)).'.**';
            }

            if($stff_prsn_errors==0)
            {
              $stff_prsn_frst_nm_cln=cln($stff_prsn_frst_nm);
              $stff_prsn_lst_nm_cln=cln($stff_prsn_lst_nm);
              $stff_prsn_fll_nm_cln=cln($stff_prsn_fll_nm);
              $stff_prsn_sffx_num_cln=cln($stff_prsn_sffx_num);
              $stff_prsn_url_cln=cln($stff_prsn_url);

              $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                    FROM prsn
                    WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$stff_prsn_frst_nm_cln' AND prsn_lst_nm='$stff_prsn_lst_nm_cln')
                    AND prsn_fll_nm='$stff_prsn_fll_nm_cln' AND prsn_sffx_num='$stff_prsn_sffx_num_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for (staff) person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['prsn_sffx_num']) {$stff_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                else {$stff_prsn_nm_error_sffx_dsply='';}
                $stff_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$stff_prsn_nm_error_sffx_dsply;
                if(count($stff_prsn_nm_err_arr)==1)
                {$errors['stff_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $stff_prsn_nm_err_arr)).'.**';}
                else
                {$errors['stff_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $stff_prsn_nm_err_arr)).'.**';}
              }

              $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                    FROM prsn
                    WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$stff_prsn_fll_nm_cln' AND prsn_sffx_num='$stff_prsn_sffx_num_cln')
                    AND prsn_url='$stff_prsn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing (staff) person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['prsn_sffx_num']) {$stff_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                else {$stff_prsn_url_error_sffx_dsply='';}
                $stff_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$stff_prsn_url_error_sffx_dsply;
                if(count($stff_prsn_url_err_arr)==1)
                {$errors['stff_prsn_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $stff_prsn_url_err_arr)).'?**';}
                else
                {$errors['stff_prsn_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $stff_prsn_url_err_arr)).'?**';}
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $stdnt_prsn_rl_list))
    {
      $stdnt_prsn_nm_rls=explode(',,', $_POST['stdnt_prsn_rl_list']);

      if(count($stdnt_prsn_nm_rls)>250)
      {
        $errors['stdnt_prsn_nm_rl_array_excss']='**Maximum of 250 entries allowed.**';
      }
      else
      {
        $stdnt_prsn_empty_err_arr=array(); $stdnt_prsn_cln_err_arr=array(); $stdnt_prsn_cln_excss_err_arr=array();
        $stdnt_prsn_sffx_err_arr=array(); $stdnt_prsn_hyphn_err_arr=array(); $stdnt_prsn_hyphn_excss_err_arr=array();
        $stdnt_prsn_dplct_arr=array(); $stdnt_prsn_smcln_err_arr=array(); $stdnt_prsn_smcln_excss_err_arr=array();
        $stdnt_prsn_nm_err_arr=array(); $stdnt_prsn_url_err_arr=array();
        foreach($stdnt_prsn_nm_rls as $stdnt_prsn_nm_rl)
        {
          $stdnt_prsn_errors=0;

          if(!preg_match('/\S+/', $stdnt_prsn_nm_rl))
          {
            $stdnt_prsn_empty_err_arr[]=$stdnt_prsn_nm_rl;
            if(count($stdnt_prsn_empty_err_arr)==1) {$errors['stdnt_prsn_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['stdnt_prsn_empty']='</br>**There are '.count($stdnt_prsn_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($stdnt_prsn_nm_rl, '::')>1)
            {
              $stdnt_prsn_errors++; $stdnt_prsn_nm=trim($stdnt_prsn_nm_rl);
              $stdnt_prsn_cln_excss_err_arr[]=$stdnt_prsn_nm_rl;
              $errors['stdnt_prsn_cln_excss']='</br>**You may only use [::] once per course student-role coupling. Please amend: '.html(implode(' / ', $stdnt_prsn_cln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*::.*\S+/', $stdnt_prsn_nm_rl))
            {
              list($stdnt_prsn_nm, $stdnt_prsn_rl)=explode('::', $stdnt_prsn_nm_rl);
              $stdnt_prsn_nm=trim($stdnt_prsn_nm); $stdnt_prsn_rl=trim($stdnt_prsn_rl);

              if(strlen($stdnt_prsn_rl)>255)
              {$errors['stdnt_prsn_rl_excss_lngth']='</br>**Course student (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
            }
            else
            {
              $stdnt_prsn_errors++; $stdnt_prsn_nm=trim($stdnt_prsn_nm_rl);
              $stdnt_prsn_cln_err_arr[]=$stdnt_prsn_nm_rl;
              $errors['stdnt_prsn_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $stdnt_prsn_cln_err_arr)).'.**';
            }

            if(substr_count($stdnt_prsn_nm, '--')>1)
            {
              $stdnt_prsn_errors++; $stdnt_prsn_sffx_num='0'; $stdnt_prsn_hyphn_excss_err_arr[]=$stdnt_prsn_nm;
              $errors['stdnt_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per course student. Please amend: '.html(implode(' / ', $stdnt_prsn_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $stdnt_prsn_nm))
            {
              list($stdnt_prsn_nm_no_sffx, $stdnt_prsn_sffx_num)=explode('--', $stdnt_prsn_nm);
              $stdnt_prsn_nm_no_sffx=trim($stdnt_prsn_nm_no_sffx); $stdnt_prsn_sffx_num=trim($stdnt_prsn_sffx_num);

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $stdnt_prsn_sffx_num))
              {
                $stdnt_prsn_errors++; $stdnt_prsn_sffx_num='0'; $stdnt_prsn_sffx_err_arr[]=$stdnt_prsn_nm;
                if(count($stdnt_prsn_sffx_err_arr)>0)
                {
                  $stdnt_prsn_sffx_error_list=implode(' / ', $stdnt_prsn_sffx_err_arr);
                  $errors['stdnt_prsn_sffx']= '</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html($stdnt_prsn_sffx_error_list).'**';
                }
              }
              $stdnt_prsn_nm=$stdnt_prsn_nm_no_sffx;
            }
            elseif(substr_count($stdnt_prsn_nm, '--')==1)
            {$stdnt_prsn_errors++; $stdnt_prsn_sffx_num='0'; $stdnt_prsn_hyphn_err_arr[]=$stdnt_prsn_nm;
            $errors['stdnt_prsn_hyphn']='</br>**Student suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $stdnt_prsn_hyphn_err_arr)).'**';}
            else
            {$stdnt_prsn_sffx_num='0';}

            if($stdnt_prsn_sffx_num) {$stdnt_prsn_sffx_rmn=' ('.romannumeral($stdnt_prsn_sffx_num).')';} else {$stdnt_prsn_sffx_rmn='';}

            if(substr_count($stdnt_prsn_nm, ';;')>1)
            {
              $stdnt_prsn_errors++; $stdnt_prsn_frst_nm=''; $stdnt_prsn_lst_nm=''; $stdnt_prsn_fll_nm=''; $stdnt_prsn_url='';
              $stdnt_prsn_smcln_excss_err_arr[]=$stdnt_prsn_nm;
              $errors['stdnt_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $stdnt_prsn_smcln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*;;(.*\S+)?/', $stdnt_prsn_nm))
            {
              list($stdnt_prsn_frst_nm, $stdnt_prsn_lst_nm)=explode(';;', $stdnt_prsn_nm);
              $stdnt_prsn_frst_nm=trim($stdnt_prsn_frst_nm); $stdnt_prsn_lst_nm=trim($stdnt_prsn_lst_nm);

              if(preg_match('/\S+/', $stdnt_prsn_lst_nm)) {$stdnt_prsn_lst_nm_dsply=' '.$stdnt_prsn_lst_nm;}
              else {$stdnt_prsn_lst_nm_dsply='';}

              $stdnt_prsn_fll_nm=$stdnt_prsn_frst_nm.$stdnt_prsn_lst_nm_dsply;
              $stdnt_prsn_url=generateurl($stdnt_prsn_fll_nm.$stdnt_prsn_sffx_rmn);

              $stdnt_prsn_dplct_arr[]=$stdnt_prsn_url;
              if(count(array_unique($stdnt_prsn_dplct_arr))<count($stdnt_prsn_dplct_arr))
              {$errors['stdnt_prsn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($stdnt_prsn_fll_nm)>255 || strlen($stdnt_prsn_url)>255)
              {$stdnt_prsn_errors++; $errors['stdnt_prsn_excss_lngth']='</br>**Course student (person) full name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
            }
            else
            {
              $stdnt_prsn_errors++; $stdnt_prsn_smcln_err_arr[]=$stdnt_prsn_nm;
              $errors['stdnt_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $stdnt_prsn_smcln_err_arr)).'.**';
            }

            if($stdnt_prsn_errors==0)
            {
              $stdnt_prsn_frst_nm_cln=cln($stdnt_prsn_frst_nm);
              $stdnt_prsn_lst_nm_cln=cln($stdnt_prsn_lst_nm);
              $stdnt_prsn_fll_nm_cln=cln($stdnt_prsn_fll_nm);
              $stdnt_prsn_sffx_num_cln=cln($stdnt_prsn_sffx_num);
              $stdnt_prsn_url_cln=cln($stdnt_prsn_url);

              $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                    FROM prsn
                    WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$stdnt_prsn_frst_nm_cln' AND prsn_lst_nm='$stdnt_prsn_lst_nm_cln')
                    AND prsn_fll_nm='$stdnt_prsn_fll_nm_cln' AND prsn_sffx_num='$stdnt_prsn_sffx_num_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for (student) person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['prsn_sffx_num']) {$stdnt_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                else {$stdnt_prsn_nm_error_sffx_dsply='';}
                $stdnt_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$stdnt_prsn_nm_error_sffx_dsply;
                if(count($stdnt_prsn_nm_err_arr)==1)
                {$errors['stdnt_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $stdnt_prsn_nm_err_arr)).'.**';}
                else
                {$errors['stdnt_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $stdnt_prsn_nm_err_arr)).'.**';}
              }

              $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                    FROM prsn
                    WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$stdnt_prsn_fll_nm_cln' AND prsn_sffx_num='$stdnt_prsn_sffx_num_cln')
                    AND prsn_url='$stdnt_prsn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing (student) person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['prsn_sffx_num']) {$stdnt_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                else {$stdnt_prsn_url_error_sffx_dsply='';}
                $stdnt_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$stdnt_prsn_url_error_sffx_dsply;
                if(count($stdnt_prsn_url_err_arr)==1)
                {$errors['stdnt_prsn_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $stdnt_prsn_url_err_arr)).'?**';}
                else
                {$errors['stdnt_prsn_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $stdnt_prsn_url_err_arr)).'?**';}
              }
            }
          }
        }
      }
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $crs_id=cln($_POST['crs_id']);
      $sql= "SELECT comp_nm, comp_sffx_num, crs_typ_nm, crs_yr_strt, crs_yr_end, crs_sffx_num, crs_dt_strt, crs_dt_end
            FROM crs
            INNER JOIN comp ON crs_schlid=comp_id
            INNER JOIN crs_typ ON crs_typid=crs_typ_id
            WHERE crs_id='$crs_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring course details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['comp_sffx_num']) {$crs_schl_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';}
      else {$crs_schl_sffx_rmn='';}
      if($row['crs_sffx_num']) {$crs_sffx_rmn=' ('.romannumeral($row['crs_sffx_num']).')';}
      else {$crs_sffx_rmn='';}
      if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);}
      else {$crs_yr=$row['crs_yr_strt'];}
      $pagetab='Edit: '.html($row['comp_nm'].$crs_schl_sffx_rmn.': '.$row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
      $pagetitle=html($row['comp_nm'].$crs_schl_sffx_rmn).':</br>'.html($row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
      $crs_schl_nm=$_POST['crs_schl_nm'];
      $crs_schl_sffx_num=$_POST['crs_schl_sffx_num'];
      $crs_typ_nm=$_POST['crs_typ_nm'];
      $crs_yr_strt=$_POST['crs_yr_strt'];
      $crs_yr_end=$_POST['crs_yr_end'];
      $crs_sffx_num=$_POST['crs_sffx_num'];
      $crs_dt_strt=$_POST['crs_dt_strt'];
      $crs_dt_end=$_POST['crs_dt_end'];
      $cdntr_list=$_POST['cdntr_list'];
      $stff_prsn_rl_list=$_POST['stff_prsn_rl_list'];
      $stdnt_prsn_rl_list=$_POST['stdnt_prsn_rl_list'];
      $textarea=$_POST['textarea'];
      $crs_id=html($crs_id);
      $errors['crs_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $crs_id=html($crs_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT 1 FROM comp WHERE comp_url='$crs_schl_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existence of course school (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)==0)
      {
        $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
              VALUES('$crs_schl_nm', CASE WHEN '$crs_schl_alph'!='' THEN '$crs_schl_alph' END, '$crs_schl_sffx_num', '$crs_schl_url', 1, 0, 0)";
        if(!mysqli_query($link, $sql)) {$error='Error adding drama school (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      $sql="SELECT 1 FROM crs_typ WHERE crs_typ_url='$crs_typ_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existence of course-type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)==0)
      {
        $sql= "INSERT INTO crs_typ(crs_typ_nm, crs_typ_url)
              VALUES('$crs_typ_nm', '$crs_typ_url')";
        if(!mysqli_query($link, $sql)) {$error='Error adding course-type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      $sql= "UPDATE crs SET
            crs_schlid=(SELECT comp_id FROM comp WHERE comp_url='$crs_schl_url'),
            crs_typid=(SELECT crs_typ_id FROM crs_typ WHERE crs_typ_url='$crs_typ_url'),
            crs_yr_strt='$crs_yr_strt',
            crs_yr_end='$crs_yr_end',
            crs_yr_url='$crs_yr_url',
            crs_sffx_num='$crs_sffx_num',
            crs_dt_strt=CASE WHEN '$crs_dt_strt'!='' THEN '$crs_dt_strt' END,
            crs_dt_strt_frmt=CASE WHEN '$crs_dt_strt'!='' THEN '$crs_dt_strt_frmt' END,
            crs_dt_end=CASE WHEN '$crs_dt_end'!='' THEN '$crs_dt_end' END,
            crs_dt_end_frmt=CASE WHEN '$crs_dt_end'!='' THEN '$crs_dt_end_frmt' END
            WHERE crs_id='$crs_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted course: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM crscdntrrl WHERE crsid='$crs_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting course-coordinator (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM crscdntr_comprl WHERE crsid='$crs_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting course-coordinator (company people roles) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM crscdntr WHERE crsid='$crs_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting course-coordinator (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $cdntr_list))
      {
        $cdntr_comp_prsn_rls=explode(',,', $cdntr_list);
        $m=0; $n=0;
        foreach($cdntr_comp_prsn_rls as $cdntr_comp_prsn_rl)
        {
          $cdntr_rl_id=++$m;
          if(preg_match('/\S+/', $cdntr_comp_prsn_rl))
          {
            list($cdntr_rl, $cdntr_comp_prsn_list)=explode('::', $cdntr_comp_prsn_rl);
            $cdntr_rl=trim($cdntr_rl); $cdntr_comp_prsn_list=trim($cdntr_comp_prsn_list);

            $o=0;
            $cdntr_comps_ppl=explode('>>', $cdntr_comp_prsn_list);
            foreach($cdntr_comps_ppl as $cdntr_comp_prsn)
            {
              if(preg_match('/\|\|/', $cdntr_comp_prsn))
              {
                list($cdntr_comp_nm, $cdntr_prsn_nm_list)=explode('||', $cdntr_comp_prsn);
                $cdntr_comp_nm=trim($cdntr_comp_nm); $cdntr_prsn_nm_list=trim($cdntr_prsn_nm_list); $cdntr_prsn_nm2='';
              }
              else
              {
                $cdntr_comp_nm=''; $cdntr_prsn_nm_list=''; $cdntr_prsn_nm2=trim($cdntr_comp_prsn);
              }

              if(preg_match('/\S+/', $cdntr_comp_nm))
              {
                if(preg_match('/\S+.*~~.*\S+/', $cdntr_comp_nm))
                {
                  list($cdntr_comp_sb_rl, $cdntr_comp_nm)=explode('~~', $cdntr_comp_nm);
                  $cdntr_comp_sb_rl=trim($cdntr_comp_sb_rl); $cdntr_comp_nm=trim($cdntr_comp_nm);
                }
                else {$cdntr_comp_sb_rl='';}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $cdntr_comp_nm))
                {
                  list($cdntr_comp_nm, $cdntr_comp_sffx_num)=explode('--', $cdntr_comp_nm);
                  $cdntr_comp_nm=trim($cdntr_comp_nm); $cdntr_comp_sffx_num=trim($cdntr_comp_sffx_num);
                  $cdntr_comp_sffx_rmn=' ('.romannumeral($cdntr_comp_sffx_num).')';
                }
                else
                {$cdntr_comp_sffx_num='0'; $cdntr_comp_sffx_rmn='';}

                $cdntr_comp_alph=alph($cdntr_comp_alph);
                $cdntr_comp_url=generateurl($cdntr_comp_nm.$cdntr_comp_sffx_rmn);
                $cdntr_ordr=++$o;

                $sql="SELECT 1 FROM comp WHERE comp_url='$cdntr_comp_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of coordinator (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                        VALUES('$cdntr_comp_nm', CASE WHEN '$cdntr_comp_alph'!='' THEN '$cdntr_comp_alph' END, '$cdntr_comp_sffx_num', '$cdntr_comp_url', 1, 0, 0)";
                  if(!mysqli_query($link, $sql)) {$error='Error adding coordinator (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO crscdntr(crsid, cdntr_rlid, cdntr_comp_rlid, cdntr_sb_rl, cdntr_ordr, cdntr_prsnid, cdntr_compid)
                      SELECT $crs_id, $cdntr_rl_id, '0', '$cdntr_comp_sb_rl', $cdntr_ordr, '0', comp_id FROM comp WHERE comp_url='$cdntr_comp_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding course-coordinator (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              if(preg_match('/\S+/', $cdntr_prsn_nm_list))
              {
                $cdntr_prsn_nms=explode('//', $cdntr_prsn_nm_list);
                foreach($cdntr_prsn_nms as $cdntr_prsn_nm)
                {
                  $cdntr_comp_rl_id=++$n;
                  $cdntr_prsn_nm=trim($cdntr_prsn_nm);

                  list($cdntr_compprsn_rl, $cdntr_prsn_nm)=explode('~~', $cdntr_prsn_nm);
                  $cdntr_compprsn_rl=trim($cdntr_compprsn_rl); $cdntr_prsn_nm=trim($cdntr_prsn_nm);

                  $cdntr_prsn_nms=explode('Â¬Â¬', $cdntr_prsn_nm);
                  foreach($cdntr_prsn_nms as $cdntr_prsn_nm)
                  {
                    $cdntr_prsn_nm=trim($cdntr_prsn_nm);
                    if(preg_match('/\S+.*\^\^.*\S+/', $cdntr_prsn_nm))
                    {
                      list($cdntr_compprsn_sb_rl, $cdntr_prsn_nm)=explode('^^', $cdntr_prsn_nm);
                      $cdntr_compprsn_sb_rl=trim($cdntr_compprsn_sb_rl); $cdntr_prsn_nm=trim($cdntr_prsn_nm);
                    }
                    else {$cdntr_compprsn_sb_rl='';}

                    if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $cdntr_prsn_nm))
                    {
                      list($cdntr_prsn_nm, $cdntr_prsn_sffx_num)=explode('--', $cdntr_prsn_nm);
                      $cdntr_prsn_nm=trim($cdntr_prsn_nm); $cdntr_prsn_sffx_num=trim($cdntr_prsn_sffx_num);
                      $cdntr_prsn_sffx_rmn=' ('.romannumeral($cdntr_prsn_sffx_num).')';
                    }
                    else
                    {$cdntr_prsn_sffx_num='0'; $cdntr_prsn_sffx_rmn='';}

                    list($cdntr_prsn_frst_nm, $cdntr_prsn_lst_nm)=explode(';;', $cdntr_prsn_nm);
                    $cdntr_prsn_frst_nm=trim($cdntr_prsn_frst_nm); $cdntr_prsn_lst_nm=trim($cdntr_prsn_lst_nm);

                    if(preg_match('/\S+/', $cdntr_prsn_lst_nm)) {$cdntr_prsn_lst_nm_dsply=' '.$cdntr_prsn_lst_nm;}
                    else {$cdntr_prsn_lst_nm_dsply='';}

                    $cdntr_prsn_fll_nm=$cdntr_prsn_frst_nm.$cdntr_prsn_lst_nm_dsply;
                    $cdntr_prsn_url=generateurl($cdntr_prsn_fll_nm.$cdntr_prsn_sffx_rmn);
                    $cdntr_ordr=++$o;

                    $sql="SELECT 1 FROM prsn WHERE prsn_url='$cdntr_prsn_url'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existence of coordinator (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)==0)
                    {
                      $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                            VALUES('$cdntr_prsn_fll_nm', '$cdntr_prsn_frst_nm', '$cdntr_prsn_lst_nm', '$cdntr_prsn_sffx_num', '$cdntr_prsn_url', '0')";
                      if(!mysqli_query($link, $sql)) {$error='Error adding coordinator (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    }

                    $sql= "INSERT INTO crscdntr(crsid, cdntr_rlid, cdntr_comp_rlid, cdntr_sb_rl, cdntr_ordr, cdntr_compid, cdntr_prsnid)
                          SELECT $crs_id, $cdntr_rl_id, $cdntr_comp_rl_id, '$cdntr_compprsn_sb_rl', $cdntr_ordr,
                          (SELECT comp_id FROM comp WHERE comp_url='$cdntr_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$cdntr_prsn_url')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding course-coordinator (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql= "INSERT INTO crscdntr_comprl(crsid, cdntr_comp_rl_id, cdntr_comprl)
                        SELECT $crs_id, $cdntr_comp_rl_id, '$cdntr_compprsn_rl'";
                  if(!mysqli_query($link, $sql)) {$error='Error adding course-coordinator (person - company member) association data (role within company only): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }

              if(preg_match('/\S+/', $cdntr_prsn_nm2))
              {
                if(preg_match('/\S+.*~~.*\S+/', $cdntr_prsn_nm2))
                {
                  list($cdntr_prsn_sb_rl, $cdntr_prsn_nm)=explode('~~', $cdntr_prsn_nm2);
                  $cdntr_prsn_sb_rl=trim($cdntr_prsn_sb_rl); $cdntr_prsn_nm=trim($cdntr_prsn_nm);
                }
                else {$cdntr_prsn_sb_rl=''; $cdntr_prsn_nm=$cdntr_prsn_nm2;}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $cdntr_prsn_nm))
                {
                  list($cdntr_prsn_nm, $cdntr_prsn_sffx_num)=explode('--', $cdntr_prsn_nm);
                  $cdntr_prsn_nm=trim($cdntr_prsn_nm); $cdntr_prsn_sffx_num=trim($cdntr_prsn_sffx_num);
                  $cdntr_prsn_sffx_rmn=' ('.romannumeral($cdntr_prsn_sffx_num).')';
                }
                else
                {$cdntr_prsn_sffx_num='0'; $cdntr_prsn_sffx_rmn='';}

                list($cdntr_prsn_frst_nm, $cdntr_prsn_lst_nm)=explode(';;', $cdntr_prsn_nm);
                $cdntr_prsn_frst_nm=trim($cdntr_prsn_frst_nm); $cdntr_prsn_lst_nm=trim($cdntr_prsn_lst_nm);

                if(preg_match('/\S+/', $cdntr_prsn_lst_nm)) {$cdntr_prsn_lst_nm_dsply=' '.$cdntr_prsn_lst_nm;}
                else {$cdntr_prsn_lst_nm_dsply='';}

                $cdntr_prsn_fll_nm=$cdntr_prsn_frst_nm.$cdntr_prsn_lst_nm_dsply;
                $cdntr_prsn_url=generateurl($cdntr_prsn_fll_nm.$cdntr_prsn_sffx_rmn);
                $cdntr_ordr=++$o;

                $sql="SELECT 1 FROM prsn WHERE prsn_url='$cdntr_prsn_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of coordinator (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                        VALUES('$cdntr_prsn_fll_nm', '$cdntr_prsn_frst_nm', '$cdntr_prsn_lst_nm', '$cdntr_prsn_sffx_num', '$cdntr_prsn_url', '0')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding coordinator (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO crscdntr(crsid, cdntr_rlid, cdntr_comp_rlid, cdntr_sb_rl, cdntr_ordr, cdntr_compid, cdntr_prsnid)
                      SELECT $crs_id, $cdntr_rl_id, '0', '$cdntr_prsn_sb_rl', $cdntr_ordr, '0', prsn_id FROM prsn WHERE prsn_url='$cdntr_prsn_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding course-coordinator (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
          }
          else {$cdntr_rl='';}

          $sql= "INSERT INTO crscdntrrl(crsid, cdntr_rl_id, cdntr_rl)
                VALUES('$crs_id', '$cdntr_rl_id', '$cdntr_rl')";
          if(!mysqli_query($link, $sql)) {$error='Error adding coordinator-role association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM crsstff_prsn WHERE crsid='$crs_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting course-course staff (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $stff_prsn_rl_list))
      {
        $stff_prsn_nm_rls=explode(',,', $stff_prsn_rl_list);
        $n=0;
        foreach($stff_prsn_nm_rls as $stff_prsn_nm_rl)
        {
          list($stff_prsn_nm, $stff_prsn_rl)=explode('::', $stff_prsn_nm_rl);
          $stff_prsn_nm=trim($stff_prsn_nm); $stff_prsn_rl=trim($stff_prsn_rl);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $stff_prsn_nm))
          {
            list($stff_prsn_nm, $stff_prsn_sffx_num)=explode('--', $stff_prsn_nm);
            $stff_prsn_nm=trim($stff_prsn_nm); $stff_prsn_sffx_num=trim($stff_prsn_sffx_num);
            $stff_prsn_sffx_rmn=' ('.romannumeral($stff_prsn_sffx_num).')';
          }
          else
          {
            $stff_prsn_sffx_num='0'; $stff_prsn_sffx_rmn='';
          }

          list($stff_prsn_frst_nm, $stff_prsn_lst_nm)=explode(';;', $stff_prsn_nm);
          $stff_prsn_frst_nm=trim($stff_prsn_frst_nm); $stff_prsn_lst_nm=trim($stff_prsn_lst_nm);

          if(preg_match('/\S+/', $stff_prsn_lst_nm))
          {$stff_prsn_lst_nm_dsply=' '.$stff_prsn_lst_nm;}
          else
          {$stff_prsn_lst_nm_dsply='';}

          $stff_prsn_fll_nm=$stff_prsn_frst_nm.$stff_prsn_lst_nm_dsply;
          $stff_prsn_url=generateurl($stff_prsn_fll_nm.$stff_prsn_sffx_rmn);
          $stff_prsn_ordr=++$n;

          $sql="SELECT 1 FROM prsn WHERE prsn_url='$stff_prsn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of staff (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO prsn(prsn_url, prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, comp_bool)
                  VALUES('$stff_prsn_url', '$stff_prsn_fll_nm', '$stff_prsn_frst_nm', '$stff_prsn_lst_nm', '$stff_prsn_sffx_num', '0')";
            if(!mysqli_query($link, $sql)) {$error='Error adding course staff (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO crsstff_prsn(crsid, stff_prsn_ordr, stff_prsn_rl, stff_prsnid)
                SELECT $crs_id, $stff_prsn_ordr, '$stff_prsn_rl', prsn_id FROM prsn WHERE prsn_url='$stff_prsn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding course-course staff (person) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM crsstdnt_prsn WHERE crsid='$crs_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting course-student (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $stdnt_prsn_rl_list))
      {
        $stdnt_prsn_nm_rls=explode(',,', $stdnt_prsn_rl_list);
        $n=0;
        foreach($stdnt_prsn_nm_rls as $stdnt_prsn_nm_rl)
        {
          list($stdnt_prsn_nm, $stdnt_prsn_rl)=explode('::', $stdnt_prsn_nm_rl);
          $stdnt_prsn_nm=trim($stdnt_prsn_nm); $stdnt_prsn_rl=trim($stdnt_prsn_rl);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $stdnt_prsn_nm))
          {
            list($stdnt_prsn_nm, $stdnt_prsn_sffx_num)=explode('--', $stdnt_prsn_nm);
            $stdnt_prsn_nm=trim($stdnt_prsn_nm); $stdnt_prsn_sffx_num=trim($stdnt_prsn_sffx_num);
            $stdnt_prsn_sffx_rmn=' ('.romannumeral($stdnt_prsn_sffx_num).')';
          }
          else
          {
            $stdnt_prsn_sffx_num='0'; $stdnt_prsn_sffx_rmn='';
          }

          list($stdnt_prsn_frst_nm, $stdnt_prsn_lst_nm)=explode(';;', $stdnt_prsn_nm);
          $stdnt_prsn_frst_nm=trim($stdnt_prsn_frst_nm); $stdnt_prsn_lst_nm=trim($stdnt_prsn_lst_nm);

          if(preg_match('/\S+/', $stdnt_prsn_lst_nm))
          {$stdnt_prsn_lst_nm_dsply=' '.$stdnt_prsn_lst_nm;}
          else
          {$stdnt_prsn_lst_nm_dsply='';}

          $stdnt_prsn_fll_nm=$stdnt_prsn_frst_nm.$stdnt_prsn_lst_nm_dsply;
          $stdnt_prsn_url=generateurl($stdnt_prsn_fll_nm.$stdnt_prsn_sffx_rmn);

          $sql="SELECT 1 FROM prsn WHERE prsn_url='$stdnt_prsn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of student (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO prsn(prsn_url, prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, comp_bool)
                  VALUES('$stdnt_prsn_url', '$stdnt_prsn_fll_nm', '$stdnt_prsn_frst_nm', '$stdnt_prsn_lst_nm', '$stdnt_prsn_sffx_num', '0')";
            if(!mysqli_query($link, $sql)) {$error='Error adding course student (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO crsstdnt_prsn(crsid, stdnt_prsn_rl, stdnt_prsnid)
                SELECT $crs_id, '$stdnt_prsn_rl', prsn_id FROM prsn WHERE prsn_url='$stdnt_prsn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding course-student (person) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS COURSE HAS BEEN EDITED:'.' '.html($crs_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/course/'.$crs_schl_url.'/'.$crs_typ_url.'/'.$crs_yr_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $crs_id=cln($_POST['crs_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdcrs WHERE crsid='$crs_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-course association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    if(count($assocs)>0)
    {$errors['crs_dlt']='**Course must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT comp_nm, comp_sffx_num, crs_typ_nm, crs_yr_strt, crs_yr_end, crs_sffx_num, crs_dt_strt, crs_dt_end
            FROM crs
            INNER JOIN comp ON crs_schlid=comp_id
            INNER JOIN crs_typ ON crs_typid=crs_typ_id
            WHERE crs_id='$crs_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring course details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['comp_sffx_num']) {$crs_schl_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';}
      else {$crs_schl_sffx_rmn='';}
      if($row['crs_sffx_num']) {$crs_sffx_rmn=' ('.romannumeral($row['crs_sffx_num']).')';}
      else {$crs_sffx_rmn='';}
      if($row['crs_yr_strt']!==$row['crs_yr_end'])
      {$crs_yr=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);;}
      else {$crs_yr=$row['crs_yr_strt'];}
      $pagetab='Edit: '.html($row['comp_nm'].$crs_schl_sffx_rmn.': '.$row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
      $pagetitle=html($row['comp_nm'].$crs_schl_sffx_rmn).':</br>'.html($row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
      $crs_schl_nm=$_POST['crs_schl_nm'];
      $crs_schl_sffx_num=$_POST['crs_schl_sffx_num'];
      $crs_typ_nm=$_POST['crs_typ_nm'];
      $crs_yr_strt=$_POST['crs_yr_strt'];
      $crs_yr_end=$_POST['crs_yr_end'];
      $crs_sffx_num=$_POST['crs_sffx_num'];
      $crs_dt_strt=$_POST['crs_dt_strt'];
      if($_POST['crs_dt_strt_frmt']=='1') {$crs_dt_strt_frmt='1';}
      if($_POST['crs_dt_strt_frmt']=='2') {$crs_dt_strt_frmt='2';}
      if($_POST['crs_dt_strt_frmt']=='3') {$crs_dt_strt_frmt='3';}
      if($_POST['crs_dt_strt_frmt']=='4') {$crs_dt_strt_frmt='4';}
      $crs_dt_end=$_POST['crs_dt_end'];
      if($_POST['crs_dt_end_frmt']=='1') {$crs_dt_end_frmt='1';}
      if($_POST['crs_dt_end_frmt']=='2') {$crs_dt_end_frmt='2';}
      if($_POST['crs_dt_end_frmt']=='3') {$crs_dt_end_frmt='3';}
      if($_POST['crs_dt_end_frmt']=='4') {$crs_dt_end_frmt='4';}
      $cdntr_list=$_POST['cdntr_list'];
      $stff_prsn_rl_list=$_POST['stff_prsn_rl_list'];
      $stdnt_prsn_rl_list=$_POST['stdnt_prsn_rl_list'];
      $textarea=$_POST['textarea'];
      $crs_id=html($crs_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT comp_nm, comp_sffx_num, crs_typ_nm, crs_yr_strt, crs_yr_end, crs_sffx_num
            FROM crs
            INNER JOIN comp ON crs_schlid=comp_id
            INNER JOIN crs_typ ON crs_typid=crs_typ_id
            WHERE crs_id='$crs_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring course details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['comp_sffx_num']) {$crs_schl_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';} else {$crs_schl_sffx_rmn='';}
      if($row['crs_sffx_num']) {$crs_sffx_rmn=' ('.romannumeral($row['crs_sffx_num']).')';} else {$crs_sffx_rmn='';}
      if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);}
      else {$crs_yr=$row['crs_yr_strt'];}
      $pagetab='Delete confirmation: '.html($row['comp_nm'].$crs_schl_sffx_rmn.': '.$row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
      $pagetitle=html($row['comp_nm'].$crs_schl_sffx_rmn).':<.br>'.html($row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
      $crs_id=html($crs_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $crs_id=cln($_POST['crs_id']);
    $sql= "SELECT comp_nm, comp_sffx_num, crs_typ_nm, crs_yr_strt, crs_yr_end, crs_sffx_num
          FROM crs
          INNER JOIN comp ON crs_schlid=comp_id
          INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE crs_id='$crs_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['comp_sffx_num']) {$crs_schl_sffx_rmn_dsply=' ('.romannumeral($row['comp_sffx_num']).')';} else {$crs_schl_sffx_rmn_dsply='';}
    if($row['crs_sffx_num']) {$crs_sffx_rmn_dsply=' ('.romannumeral($row['crs_sffx_num']).')';} else {$crs_sffx_rmn_dsply='';}
    if($row['crs_yr_strt']!==$row['crs_yr_end'])
    {$crs_yr_dsply=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);}
    else {$crs_yr_dsply=$row['crs_yr_strt'];}
    $crs_session=html($row['comp_nm'].$crs_schl_sffx_rmn_dsply.': '.$row['crs_typ_nm'].' ('.$crs_yr_dsply.')'.$crs_sffx_rmn_dsply);

    $sql="DELETE FROM prdcrs WHERE crsid='$crs_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM crscdntrrl WHERE crsid='$crs_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-course coordinator (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM crscdntr_comprl WHERE crsid='$crs_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-course coordinator (company role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM crscdntr WHERE crsid='$crs_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-course coordinator associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM crsstff_prsn WHERE crsid='$crs_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-course staff (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM crsstdnt_prsn WHERE crsid='$crs_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-course student (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql ="DELETE FROM crs WHERE crs_id='$crs_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS COURSE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($crs_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $crs_id=cln($_POST['crs_id']);
    $sql= "SELECT crs_yr_url
          FROM crs
          WHERE crs_id='$crs_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course URLs (company / type / year): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['crs_yr_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $comp_url=cln($_GET['comp_url']);
  $crs_typ_url=cln($_GET['crs_typ_url']);
  $crs_yr_url=cln($_GET['crs_yr_url']);

  $sql= "SELECT crs_id
        FROM crs
        WHERE crs_yr_url='$crs_yr_url'
        AND crs_typid=(SELECT crs_typ_id FROM crs_typ WHERE crs_typ_url='$crs_typ_url')
        AND crs_schlid=(SELECT comp_id FROM comp WHERE comp_url='$comp_url')";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $crs_id=$row['crs_id'];
  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT comp_nm, comp_url, comp_sffx_num, crs_typ_nm, crs_typ_url, crs_yr_strt, crs_yr_end, crs_sffx_num,
          CASE WHEN crs_dt_strt_frmt=1 THEN DATE_FORMAT(crs_dt_strt, '%a, %d %b %Y') WHEN crs_dt_strt_frmt=2 THEN DATE_FORMAT(crs_dt_strt, '%b %Y')
          WHEN crs_dt_strt_frmt=3 THEN DATE_FORMAT(crs_dt_strt, '%Y') ELSE NULL END AS crs_dt_strt,
          CASE WHEN crs_dt_end_frmt=1 THEN DATE_FORMAT(crs_dt_end, '%a, %d %b %Y') WHEN crs_dt_end_frmt=2 THEN DATE_FORMAT(crs_dt_end, '%b %Y')
          WHEN crs_dt_end_frmt=3 THEN DATE_FORMAT(crs_dt_end, '%Y') ELSE NULL END AS crs_dt_end
          FROM crs
          INNER JOIN comp ON crs_schlid=comp_id INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE crs_id='$crs_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['comp_sffx_num']) {$crs_schl_sffx_num=$row['comp_sffx_num']; $crs_schl_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';}
    else {$crs_schl_sffx_num=''; $crs_schl_sffx_rmn='';}
    if($row['crs_sffx_num']) {$crs_sffx_num=$row['crs_sffx_num']; $crs_sffx_rmn=' ('.romannumeral($row['crs_sffx_num']).')';}
    else {$crs_sffx_num=''; $crs_sffx_rmn='';}
    if($row['crs_yr_strt']!==$row['crs_yr_end'])
    {
      $crs_yr=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);
      $crs_yr_strt='<a href="/course/year/'.html($row['crs_yr_strt']).'">'.html($row['crs_yr_strt']).'</a>';
      $crs_yr_end='<a href="/course/year/'.html($row['crs_yr_end']).'">'.html($row['crs_yr_end']).'</a>';
    }
    else
    {
      $crs_yr=$row['crs_yr_strt'];
      $crs_yr_strt='<a href="/course/year/'.html($row['crs_yr_strt']).'">'.html($row['crs_yr_strt']).'</a>';
      $crs_yr_end='';
    }
    $pagetab=html($row['comp_nm'].$crs_schl_sffx_rmn.': '.$row['crs_typ_nm'].' ('.$crs_yr.')'.$crs_sffx_rmn);
    $pagetitle=html($row['comp_nm']).':</br>'.html($row['crs_typ_nm'].' ('.$crs_yr.')');

    $crs_schl='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';
    $crs_typ='<a href="/course/type/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'">'.html($row['crs_typ_nm']).'</a>';
    $crs_dt_strt=html($row['crs_dt_strt']);
    $crs_dt_end=html($row['crs_dt_end']);

    $sql="SELECT cdntr_rl_id, cdntr_rl FROM crscdntrrl WHERE crsid='$crs_id' ORDER BY cdntr_rl_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring coordinator (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$cdntr_rls[$row['cdntr_rl_id']]=array('cdntr_rl'=>html($row['cdntr_rl']), 'cdntrs'=>array());}

      $sql= "SELECT cdntr_rlid, comp_id, comp_nm, comp_url, cdntr_ordr, comp_bool
            FROM crscdntr
            INNER JOIN comp ON cdntr_compid=comp_id
            WHERE crsid='$crs_id'
            AND cdntr_prsnid=0
            UNION
            SELECT cdntr_rlid, prsn_id, prsn_fll_nm, prsn_url, cdntr_ordr, comp_bool
            FROM crscdntr
            INNER JOIN prsn ON cdntr_prsnid=prsn_id
            WHERE crsid='$crs_id'
            AND cdntr_compid=0
            ORDER BY cdntr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring coordinator data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        $cdntr_rls[$row['cdntr_rlid']]['cdntrs'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'comp_nm_pln'=>html($row['comp_nm']), 'comp_rls'=>array());
      }

      $sql= "SELECT cdntr_rlid, cdntr_compid, cdntr_comp_rl_id, cdntr_comprl
            FROM crscdntr pp
            INNER JOIN crscdntr_comprl ppcr ON pp.crsid=ppcr.crsid
            WHERE pp.crsid='$crs_id'
            AND cdntr_comp_rlid=cdntr_comp_rl_id
            GROUP BY cdntr_rlid, cdntr_compid, cdntr_comp_rl_id
            ORDER BY cdntr_comp_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring coordinator (company people roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$cdntr_rls[$row['cdntr_rlid']]['cdntrs'][$row['cdntr_compid']]['comp_rls'][$row['cdntr_comp_rl_id']]=array('cdntr_comprl'=>html($row['cdntr_comprl']), 'cdntrcomp_ppl'=>array());}

      $sql= "SELECT cdntr_rlid, cdntr_compid, cdntr_comp_rlid, prsn_fll_nm, prsn_url
            FROM crscdntr
            INNER JOIN prsn ON cdntr_prsnid=prsn_id
            WHERE crsid='$crs_id'
            AND cdntr_compid!=0
            ORDER BY cdntr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring coordinator (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $cdntr_rls[$row['cdntr_rlid']]['cdntrs'][$row['cdntr_compid']]['comp_rls'][$row['cdntr_comp_rlid']]['cdntrcomp_ppl'][]=$prsn_nm;
      }
    }

    $sql= "SELECT prsn_fll_nm, prsn_url, stff_prsn_rl
          FROM crsstff_prsn
          INNER JOIN prsn ON stff_prsnid=prsn_id
          WHERE crsid='$crs_id'
          ORDER BY stff_prsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course staff (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
      $stff_ppl[]=array('prsn_nm'=>$prsn_nm, 'stff_prsn_rl'=>html($row['stff_prsn_rl']));
    }

    $sql= "SELECT prsn_fll_nm, prsn_url, stdnt_prsn_rl
          FROM crsstdnt_prsn
          INNER JOIN prsn ON stdnt_prsnid=prsn_id
          WHERE crsid='$crs_id'
          ORDER BY prsn_lst_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course student (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
      $stdnt_ppl[]=array('prsn_nm'=>$prsn_nm, 'stdnt_prsn_rl'=>html($row['stdnt_prsn_rl']));
    }

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdcrs
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE crsid='$crs_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdcrs
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE crsid='$crs_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_id'=>$row['prd_id'], 'prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdcrs WHERE prdid='$prd_id' AND crsid='$crs_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this course: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdcrs
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE crsid='$crs_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
    }

    $crs_id=html($crs_id);
    include 'course.html.php';
  }
?>