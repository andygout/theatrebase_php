<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $comp_id=cln($_POST['comp_id']);
    $sql= "SELECT comp_nm, comp_sffx_num, comp_reg_nm, comp_reg_adrs, comp_est_dt, comp_est_dt_frmt, comp_dslv_dt, comp_dslv_dt_frmt, comp_dslv,
          comp_nm_frm_dt, comp_nm_frm_dt_frmt, comp_nm_exp_dt, comp_nm_exp_dt_frmt, comp_nm_exp
          FROM comp
          WHERE comp_id='$comp_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['comp_sffx_num']) {$comp_sffx_num=html($row['comp_sffx_num']); $comp_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';}
    else {$comp_sffx_num=''; $comp_sffx_rmn='';}
    $pagetab='Edit: '.html($row['comp_nm'].$comp_sffx_rmn);
    $pagetitle=html($row['comp_nm'].$comp_sffx_rmn);
    $comp_nm=html($row['comp_nm']);
    $comp_reg_nm=html($row['comp_reg_nm']);
    $comp_reg_adrs=html($row['comp_reg_adrs']);
    $comp_est_dt=html($row['comp_est_dt']);
    $comp_est_dt_frmt=html($row['comp_est_dt_frmt']);
    $comp_dslv_dt=html($row['comp_dslv_dt']);
    $comp_dslv_dt_frmt=html($row['comp_dslv_dt_frmt']);
    $comp_dslv=html($row['comp_dslv']);
    $comp_nm_frm_dt=html($row['comp_nm_frm_dt']);
    $comp_nm_frm_dt_frmt=html($row['comp_nm_frm_dt_frmt']);
    $comp_nm_exp_dt=html($row['comp_nm_exp_dt']);
    $comp_nm_exp_dt_frmt=html($row['comp_nm_exp_dt_frmt']);
    $comp_nm_exp=html($row['comp_nm_exp']);

    $sql="SELECT comp_adrs, comp_adrs_ttl FROM compadrs WHERE compid='$comp_id' ORDER BY comp_adrs_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company address data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_adrs_ttl']) {$comp_adrs_ttl=$row['comp_adrs_ttl'].'::';} else {$comp_adrs_ttl='';}
      $comp_adrss[]=$comp_adrs_ttl.$row['comp_adrs'];
    }
    if(!empty($comp_adrss)) {$comp_adrs_list=html(implode('//', $comp_adrss));} else {$comp_adrs_list='';}

    $sql= "SELECT comp_nm, comp_sffx_num, COALESCE(comp_alph, comp_nm)comp_alph FROM comp_aka INNER JOIN comp ON comp_sbsq_id=comp_id
          WHERE comp_prvs_id='$comp_id' ORDER BY comp_dslv_dt ASC, comp_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring "company subsequently known as" data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_sffx_num']) {$sbsq_comp_sffx_num='--'.$row['comp_sffx_num'];} else {$sbsq_comp_sffx_num='';}
      $comp_sbsqs[]=$row['comp_nm'].$sbsq_comp_sffx_num;
    }
    if(!empty($comp_sbsqs)) {$sbsq_comp_list=html(implode(',,', $comp_sbsqs));} else {$sbsq_comp_list='';}

    $sql= "SELECT comp_nm, comp_sffx_num, COALESCE(comp_alph, comp_nm)comp_alph FROM comp_aka INNER JOIN comp ON comp_prvs_id=comp_id
          WHERE comp_sbsq_id='$comp_id' ORDER BY comp_dslv_dt ASC, comp_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring "company previously known as" data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_sffx_num']) {$prvs_comp_sffx_num='--'.$row['comp_sffx_num'];} else {$prvs_comp_sffx_num='';}
      $comp_prvss[]=$row['comp_nm'].$prvs_comp_sffx_num;
    }
    if(!empty($comp_prvss)) {$prvs_comp_list=html(implode(',,', $comp_prvss));} else {$prvs_comp_list='';}

    $sql= "SELECT lctn_id, lctn_nm, lctn_sffx_num FROM comp_lctn INNER JOIN lctn ON comp_lctnid=lctn_id WHERE compid='$comp_id' ORDER BY comp_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$comp_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$comp_lctn_sffx_num='';}
      $comp_lctns[$row['lctn_id']]=array('lctn'=>$row['lctn_nm'].$comp_lctn_sffx_num, 'lctn_alts'=>array());
    }

    $sql= "SELECT cl.comp_lctnid, lctn_nm, lctn_sffx_num
          FROM comp_lctn cl
          INNER JOIN rel_lctn ON cl.comp_lctnid=rel_lctn1 INNER JOIN comp_lctn_alt cla ON rel_lctn2=cla.comp_lctn_altid INNER JOIN lctn ON cla.comp_lctn_altid=lctn_id
          WHERE cl.compid='$comp_id' AND cl.compid=cla.compid AND cl.comp_lctnid=cla.comp_lctnid
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring alternate place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$lctn_sffx_num='';}
      $comp_lctns[$row['comp_lctnid']]['lctn_alts'][]=$row['lctn_nm'].$lctn_sffx_num;
    }

    if(!empty($comp_lctns))
    {
      $comp_lctn_array=array();
      foreach($comp_lctns as $comp_lctn)
      {
        if(!empty($comp_lctn['lctn_alts'])) {$lctn_alt_list='||'.implode('>>', $comp_lctn['lctn_alts']);} else {$lctn_alt_list='';}
        $comp_lctn_array[]=$comp_lctn['lctn'].$lctn_alt_list;
      }
      $comp_lctn_list=html(implode(',,', $comp_lctn_array));
    }
    else {$comp_lctn_list='';}

    $sql= "SELECT comp_typ_nm FROM comptyp INNER JOIN comp_typ ON comp_typid=comp_typ_id WHERE compid='$comp_id' ORDER BY comp_typ_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$comp_typs[]=$row['comp_typ_nm'];}
    if(!empty($comp_typs)) {$comp_typ_list=html(implode(',,', $comp_typs));} else {$comp_typ_list='';}

    $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, compprsn_rl, compprsn_rl_nt, compprsn_yr_strt, compprsn_yr_end
          FROM compprsn
          INNER JOIN prsn ON prsnid=prsn_id
          WHERE compid='$comp_id'
          ORDER BY compprsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company member (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prsn_sffx_num']) {$comp_prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$comp_prsn_sffx_num='';}
      if($row['compprsn_rl_nt']) {$compprsn_rl_nt=';;'.$row['compprsn_rl_nt'];} else {$compprsn_rl_nt='';}
      if($row['compprsn_yr_strt']) {$compprsn_yr_strt='##'.$row['compprsn_yr_strt'];} else {$compprsn_yr_strt='';}
      if($row['compprsn_yr_end']) {$compprsn_yr_end=';;'.$row['compprsn_yr_end'];} else {$compprsn_yr_end='';}
      $comp_ppl[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$comp_prsn_sffx_num.'::'.$row['compprsn_rl'].$compprsn_rl_nt.$compprsn_yr_strt.$compprsn_yr_end;
    }
    if(!empty($comp_ppl)) {$comp_prsn_list=html(implode(',,', $comp_ppl));}
    else {$comp_prsn_list='';}

    $textarea='';
    $comp_id=html($comp_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $comp_id=cln($_POST['comp_id']);
    $comp_nm=trim(cln($_POST['comp_nm']));
    $comp_sffx_num=trim(cln($_POST['comp_sffx_num']));
    $comp_adrs_list=trim(cln($_POST['comp_adrs_list']));
    $comp_reg_nm=trim(cln($_POST['comp_reg_nm']));
    $comp_reg_adrs=trim(cln($_POST['comp_reg_adrs']));
    $comp_est_dt=cln($_POST['comp_est_dt']);
    if($_POST['comp_est_dt_frmt']=='1') {$comp_est_dt_frmt='1';}
    if($_POST['comp_est_dt_frmt']=='2') {$comp_est_dt_frmt='2';}
    if($_POST['comp_est_dt_frmt']=='3') {$comp_est_dt_frmt='3';}
    if($_POST['comp_est_dt_frmt']=='4') {$comp_est_dt_frmt='4';}
    $comp_dslv_dt=cln($_POST['comp_dslv_dt']);
    if($_POST['comp_dslv_dt_frmt']=='1') {$comp_dslv_dt_frmt='1';}
    if($_POST['comp_dslv_dt_frmt']=='2') {$comp_dslv_dt_frmt='2';}
    if($_POST['comp_dslv_dt_frmt']=='3') {$comp_dslv_dt_frmt='3';}
    if($_POST['comp_dslv_dt_frmt']=='4') {$comp_dslv_dt_frmt='4';}
    if(isset($_POST['comp_dslv'])) {$comp_dslv='1';} else {$comp_dslv='0';}
    $comp_nm_frm_dt=cln($_POST['comp_nm_frm_dt']);
    if($_POST['comp_nm_frm_dt_frmt']=='1') {$comp_nm_frm_dt_frmt='1';}
    if($_POST['comp_nm_frm_dt_frmt']=='2') {$comp_nm_frm_dt_frmt='2';}
    if($_POST['comp_nm_frm_dt_frmt']=='3') {$comp_nm_frm_dt_frmt='3';}
    if($_POST['comp_nm_frm_dt_frmt']=='4') {$comp_nm_frm_dt_frmt='4';}
    $comp_nm_exp_dt=cln($_POST['comp_nm_exp_dt']);
    if($_POST['comp_nm_exp_dt_frmt']=='1') {$comp_nm_exp_dt_frmt='1';}
    if($_POST['comp_nm_exp_dt_frmt']=='2') {$comp_nm_exp_dt_frmt='2';}
    if($_POST['comp_nm_exp_dt_frmt']=='3') {$comp_nm_exp_dt_frmt='3';}
    if($_POST['comp_nm_exp_dt_frmt']=='4') {$comp_nm_exp_dt_frmt='4';}
    if(isset($_POST['comp_nm_exp'])) {$comp_nm_exp='1';} else {$comp_nm_exp='0';}
    $sbsq_comp_list=cln($_POST['sbsq_comp_list']);
    $prvs_comp_list=cln($_POST['prvs_comp_list']);
    $comp_lctn_list=trim(cln($_POST['comp_lctn_list']));
    $comp_typ_list=trim(cln($_POST['comp_typ_list']));
    $comp_prsn_list=cln($_POST['comp_prsn_list']);

    $comp_nm_session=$_POST['comp_nm'];
    $errors=array();

    if(!preg_match('/\S+/', $comp_nm))
    {$errors['comp_nm']='**You must enter a company name.**';}
    elseif(preg_match('/;;/', $comp_nm) || preg_match('/--/', $comp_nm) || preg_match('/::/', $comp_nm) || preg_match('/##/', $comp_nm) ||
    preg_match('/\|\|/', $comp_nm) || preg_match('/,,/', $comp_nm) || preg_match('/@@/', $comp_nm) || preg_match('/==/', $comp_nm) ||
    preg_match('/>>/', $comp_nm) || preg_match('/~~/', $comp_nm) || preg_match('/\+\+/', $comp_nm) || preg_match('/\/\//', $comp_nm) ||
    preg_match('/\^\^/', $comp_nm) || preg_match('/¬¬/', $comp_nm))
    {$errors['comp_nm']='</br>**Company name cannot include any of the following: [;;], [--], [::], [##], [||], [,,], [@@], [==], [>>], [~~], [++], [//], [^^], [¬¬].**';}

    if(preg_match('/^[0]*$/', $comp_sffx_num) || !$comp_sffx_num)
    {$comp_sffx_num='0'; $comp_sffx_rmn='';}
    elseif(preg_match('/^[1-9][0-9]{0,1}$/', $comp_sffx_num))
    {$comp_sffx_rmn=' ('.romannumeral($comp_sffx_num).')'; $comp_nm_session .= ' ('.romannumeral($_POST['comp_sffx_num']).')';}
    else
    {$errors['comp_sffx']='**The suffix must be a valid integer between 1 and 99 (with no leading 0) or left blank (or as 0).**'; $comp_sffx_rmn='';}

    $comp_url=generateurl($comp_nm.$comp_sffx_rmn);

    if(strlen($comp_nm)>255 || strlen($comp_url)>255)
    {$errors['comp_excss_lngth']='</br>**Company name and its URL are allowed a maximum of 255 characters each.**';}

    $comp_alph=alph($comp_nm);

    if(count($errors)==0)
    {
      $sql="SELECT comp_id, comp_nm, comp_sffx_num FROM comp WHERE comp_url='$comp_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['comp_id']!==$comp_id)
      {
        if($row['comp_sffx_num']) {$comp_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';} else {$comp_sffx_rmn='';}
        $errors['comp_url']='</br>**Duplicate URL exists for: '.html($row['comp_nm'].$comp_sffx_rmn). '. You must keep the original name or assign a company name without an existing URL.**';
      }
    }

    if(preg_match('/\S+/', $comp_adrs_list))
    {
      $comp_adrss=explode('//', $comp_adrs_list);
      if(count($comp_adrss)>250)
      {$errors['comp_adrs_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $comp_adrs_empty_err_arr=array(); $comp_adrs_cln_err_arr=array();
        $comp_adrs_cnt=(count($comp_adrss));
        foreach($comp_adrss as $comp_adrs)
        {
          $comp_adrs=trim($comp_adrs);
          if(!preg_match('/\S+/', $comp_adrs))
          {
            $comp_adrs_empty_err_arr[]=$comp_adrs;
            if(count($comp_adrs_empty_err_arr)==1) {$errors['comp_adrs_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
            else {$errors['comp_adrs_empty']='</br>**There are '.count($comp_adrs_empty_err_arr).' empty entries in the string (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($comp_adrs, '::')>1)
            {$errors['comp_adrs_cln_excss']='</br>**You may only use [::] once per address title-address coupling.**';}
            elseif(preg_match('/\S+.*::.*\S+/', $comp_adrs))
            {list($comp_adrs_ttl, $comp_adrs)=explode('::', $comp_adrs); $comp_adrs_ttl=trim($comp_adrs_ttl); $comp_adrs=trim($comp_adrs);
            if(strlen($comp_adrs_ttl)>255) {$errors['comp_adrs_ttl_excss_lngth']='</br>**Address title is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}}
            else
            {
              if($comp_adrs_cnt>1) {$comp_adrs_cln_err_arr[]=$comp_adrs; {$errors['comp_adrs_cln']='</br>**You must assign a title to the following using [::]: '.html(implode(' / ', $comp_adrs_cln_err_arr)).'.**';}}
              else{if(substr_count($comp_adrs, '::')==1) {$errors['comp_adrs_cln']='</br>**Address title assignation must use [::] in the correct format.**';}}
            }
            if(strlen($comp_adrs)>255) {$errors['comp_adrs_excss_lngth']='</br>**Address is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
          }
        }
      }
    }

    if(preg_match('/\S+/', $comp_reg_nm))
    {if(strlen($comp_reg_nm)>255) {$errors['reg_nm_excss_lngth']='</br>**Registered name is allowed a maximum of 255 characters.**';}}

    if(preg_match('/\S+/', $comp_reg_adrs))
    {if(strlen($comp_reg_adrs)>255) {$errors['reg_adrs_excss_lngth']='</br>**Registered address is allowed a maximum of 255 characters.**';}}

    if($comp_est_dt)
    {
      if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $comp_est_dt))
      {$errors['comp_est_dt']='**You must enter a valid established date in the prescribed format or leave empty.**'; $comp_est_dt=NULL;}
      else
      {
        list($comp_est_dt_YYYY, $comp_est_dt_MM, $comp_est_dt_DD)=explode('-', $comp_est_dt);
        if(!checkdate((int)$comp_est_dt_MM, (int)$comp_est_dt_DD, (int)$comp_est_dt_YYYY))
        {$errors['comp_est_dt']='**You must enter a valid established date or leave empty.**'; $comp_est_dt=NULL;}
      }
    }
    else
    {$comp_est_dt=NULL;}

    if($comp_dslv_dt)
    {
      if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $comp_dslv_dt)) {$errors['comp_dslv_dt']='**You must enter a valid dissolved date in the prescribed format or leave empty.**'; $comp_dslv_dt=NULL;}
      else
      {
        date_default_timezone_set('Europe/London'); list($comp_dslv_dt_YYYY, $comp_dslv_dt_MM, $comp_dslv_dt_DD)=explode('-', $comp_dslv_dt);
        if(!checkdate((int)$comp_dslv_dt_MM, (int)$comp_dslv_dt_DD, (int)$comp_dslv_dt_YYYY)) {$errors['comp_dslv_dt']='**You must enter a valid dissolved date or leave empty.**'; $comp_dslv_dt=NULL;}
        elseif(strtotime($comp_dslv_dt)>time() && $comp_dslv) {$errors['comp_dslv_dt_comp_dslv']='**You cannot check the company as dissolved and set the dissolved date as a future date.**';}
        elseif(strtotime($comp_dslv_dt) <= time()) {$comp_dslv='1';}
      }
    }
    else {$comp_dslv_dt=NULL;}

    if($comp_est_dt && $comp_dslv_dt && $comp_est_dt>$comp_dslv_dt) {$errors['comp_est_dt']='**Must be earlier than the dissolved date.**'; $errors['comp_dslv_dt']='**Must be later than the established date.**';}

    if($comp_nm_frm_dt)
    {
      if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $comp_nm_frm_dt)) {$errors['comp_nm_frm_dt']='**You must enter a valid name start date in the prescribed format or leave empty.**'; $comp_nm_frm_dt=NULL;}
      else
      {
        list($comp_nm_frm_dt_YYYY, $comp_nm_frm_dt_MM, $comp_nm_frm_dt_DD)=explode('-', $comp_nm_frm_dt);
        if(!checkdate((int)$comp_nm_frm_dt_MM, (int)$comp_nm_frm_dt_DD, (int)$comp_nm_frm_dt_YYYY)) {$errors['comp_nm_frm_dt']='**You must enter a valid name start date or leave empty.**'; $comp_nm_frm_dt=NULL;}
      }
    }
    else {$comp_nm_frm_dt=NULL;}

    if($comp_nm_exp_dt)
    {
      if(!preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $comp_nm_exp_dt)) {$errors['comp_nm_exp_dt']='**You must enter a valid name expiry date in the prescribed format or leave empty.**'; $comp_nm_exp_dt=NULL;}
      else
      {
        date_default_timezone_set('Europe/London'); list($comp_nm_exp_dt_YYYY, $comp_nm_exp_dt_MM, $comp_nm_exp_dt_DD)=explode('-', $comp_nm_exp_dt);
        if(!checkdate((int)$comp_nm_exp_dt_MM, (int)$comp_nm_exp_dt_DD, (int)$comp_nm_exp_dt_YYYY)) {$errors['comp_nm_exp_dt']='**You must enter a valid name expiry date or leave empty.**'; $comp_nm_exp_dt=NULL;}
        elseif(strtotime($comp_nm_exp_dt)>time() && $comp_nm_exp) {$errors['comp_nm_exp_dt_nm_exp']='**You cannot check the company name as expired and set the expiry date as a future date.**';}
      }
    }
    else {$comp_nm_exp_dt=NULL;}

    if($comp_nm_frm_dt && $comp_nm_exp_dt && $comp_nm_frm_dt>$comp_nm_exp_dt) {$errors['comp_nm_frm_dt']='**Must be earlier than the name start date.**'; $errors['comp_nm_exp_dt']='**Must be later than the name expiry date.**';}

    if($comp_nm_frm_dt)
    {
      if($comp_est_dt && !$comp_dslv_dt && $comp_est_dt>$comp_nm_frm_dt) {$errors['comp_est_nm_frm_dt_mtch']='**Must not be earlier than the established date.**';}
      elseif(!$comp_est_dt && $comp_dslv_dt && $comp_dslv_dt<$comp_nm_frm_dt) {$errors['comp_est_nm_frm_dt_mtch']='**Must not be later than the dissolved date.**';}
      elseif($comp_est_dt && $comp_dslv_dt && ($comp_est_dt>$comp_nm_frm_dt || $comp_dslv_dt<$comp_nm_frm_dt)) {$errors['comp_est_nm_frm_dt_mtch']='**Must not be earlier than the established date or later than the dissolved date.**';}
    }

    if($comp_nm_exp_dt)
    {
      if($comp_est_dt && !$comp_dslv_dt && $comp_est_dt>$comp_nm_exp_dt) {$errors['comp_dslv_nm_exp_dt_mtch']='**Must not be earlier than the established date.**';}
      elseif(!$comp_est_dt && $comp_dslv_dt && $comp_dslv_dt<$comp_nm_exp_dt) {$errors['comp_dslv_nm_exp_dt_mtch']='**Must not be later than the dissolved date.**';}
      elseif($comp_est_dt && $comp_dslv_dt && ($comp_est_dt>$comp_nm_exp_dt || $comp_dslv_dt<$comp_nm_exp_dt)) {$errors['comp_dslv_nm_exp_dt_mtch']='**Must not be earlier than the established date later than the dissolved date.**';}
    }

    if(preg_match('/\S+/', $sbsq_comp_list))
    {
      if(!$comp_nm_exp) {$errors['sbsq_comp_nm_exp_unchckd']='**This field must be empty unless company name replaced button is applied.**';}
      else
      {
        $sbsq_comp_nms=explode(',,', $_POST['sbsq_comp_list']);
        if(count($sbsq_comp_nms)>250)
        {$errors['sbsq_comp_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $sbsq_comp_empty_err_arr=array(); $sbsq_comp_hyphn_excss_err_arr=array(); $sbsq_comp_sffx_err_arr=array();
          $sbsq_comp_hyphn_err_arr=array(); $sbsq_comp_dplct_arr=array(); $sbsq_comp_url_err_arr=array();
          $sbsq_comp_inv_comb_err_arr=array(); $sbsq_comp_est_dt_mtch_err_arr=array(); $sbsq_comp_dslv_dt_mtch_err_arr=array();
          $sbsq_comp_nm_dt_mtch_err_arr=array();
          foreach($sbsq_comp_nms as $sbsq_comp_nm)
          {
            $sbsq_comp_errors=0; $sbsq_comp_nm=trim($sbsq_comp_nm);
            if(!preg_match('/\S+/', $sbsq_comp_nm))
            {
              $sbsq_comp_empty_err_arr[]=$sbsq_comp_nm;
              if(count($sbsq_comp_empty_err_arr)==1) {$errors['sbsq_comp_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['sbsq_comp_empty']='</br>**There are '.count($sbsq_comp_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $sbsq_comp_nm_err=$sbsq_comp_nm;
              if(substr_count($sbsq_comp_nm, '--')>1) {$sbsq_comp_errors++; $sbsq_comp_sffx_num='0'; $sbsq_comp_hyphn_excss_err_arr[]=$sbsq_comp_nm_err; $errors['sbsq_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per company. Please amend: '.html(implode(' / ', $sbsq_comp_hyphn_excss_err_arr)).'.**';}
              elseif(preg_match('/^\S+.*--.+$/', $sbsq_comp_nm))
              {
                list($sbsq_comp_nm_no_sffx, $sbsq_comp_sffx_num)=explode('--', $sbsq_comp_nm); $sbsq_comp_nm_no_sffx=trim($sbsq_comp_nm_no_sffx); $sbsq_comp_sffx_num=trim($sbsq_comp_sffx_num);
                if(!preg_match('/^[1-9][0-9]{0,1}$/', $sbsq_comp_sffx_num)) {$sbsq_comp_errors++; $sbsq_comp_sffx_num='0'; $sbsq_comp_sffx_err_arr[]=$sbsq_comp_nm_err; $errors['sbsq_comp_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $sbsq_comp_sffx_err_arr)).'**';}
                $sbsq_comp_nm=$sbsq_comp_nm_no_sffx;
              }
              elseif(substr_count($sbsq_comp_nm, '--')==1) {$sbsq_comp_errors++; $sbsq_comp_sffx_num='0'; $sbsq_comp_hyphn_err_arr[]=$sbsq_comp_nm_err; $errors['sbsq_comp_hyphn']='</br>**Company suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $sbsq_comp_hyphn_err_arr)).'**';}
              else {$sbsq_comp_sffx_num='0';}

              if($sbsq_comp_sffx_num) {$sbsq_comp_sffx_rmn=' ('.romannumeral($sbsq_comp_sffx_num).')';} else {$sbsq_comp_sffx_rmn='';}

              $sbsq_comp_url=generateurl($sbsq_comp_nm.$sbsq_comp_sffx_rmn);

              $sbsq_comp_dplct_arr[]=$sbsq_comp_url;
              if(count(array_unique($sbsq_comp_dplct_arr))<count($sbsq_comp_dplct_arr)) {$errors['sbsq_comp_dplct']='</br>**There are entries within the array that create duplicate company URLs.**';}

              if(strlen($sbsq_comp_nm)>255 || strlen($sbsq_comp_url)>255) {$sbsq_comp_errors++; $errors['sbsq_comp_nm_excss_lngth']='</br>**Company name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

              if($sbsq_comp_errors==0)
              {
                $sbsq_comp_nm_cln=cln($sbsq_comp_nm);
                $sbsq_comp_sffx_num_cln=cln($sbsq_comp_sffx_num);
                $sbsq_comp_url_cln=cln($sbsq_comp_url);

                $sql= "SELECT comp_nm, comp_sffx_num
                      FROM comp
                      WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$sbsq_comp_nm_cln' AND comp_sffx_num='$sbsq_comp_sffx_num_cln')
                      AND comp_url='$sbsq_comp_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing subsequently named company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['comp_sffx_num']) {$sbsq_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];} else {$sbsq_comp_url_error_sffx_dsply='';}
                  $sbsq_comp_url_err_arr[]=$row['comp_nm'].$sbsq_comp_url_error_sffx_dsply;
                  if(count($sbsq_comp_url_err_arr)==1) {$errors['sbsq_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $sbsq_comp_url_err_arr)).'?**';}
                  else {$errors['sbsq_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $sbsq_comp_url_err_arr)).'?**';}
                }
                else
                {
                  $sql="SELECT comp_id, comp_nm, comp_sffx_num, comp_url, comp_est_dt, comp_dslv_dt, comp_nm_frm_dt, comp_nm_exp_dt FROM comp WHERE comp_url='$sbsq_comp_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing company URL (against subsequently known as company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if($row['comp_id']==$comp_id) {$errors['sbsq_comp_id_mtch']='</br>**You cannot assign this company as a subsequently named company of itself: '.html($sbsq_comp_nm_err).'.**';}
                  else
                  {
                    $sbsq_comp_id=$row['comp_id'];
                    if($row['comp_sffx_num']) {$sbsq_comp_sffx_rmn_url_lnk=' ('.romannumeral($row['comp_sffx_num']).')';} else {$sbsq_comp_sffx_rmn_url_lnk='';}
                    $sbsq_comp_url_lnk='<a href="/company/'.html($row['comp_url']).'" target="/company/'.html($row['comp_url']).'">'.html($row['comp_nm'].$sbsq_comp_sffx_rmn_url_lnk).'</a>';

                    $sql="SELECT 1 FROM comp_aka WHERE comp_sbsq_id='$comp_id' AND comp_prvs_id='$sbsq_comp_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for inverse of proposed combination (subsequently named companies): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      $sbsq_comp_inv_comb_err_arr[]=$sbsq_comp_nm.$sbsq_comp_sffx_rmn;
                      $errors['sbsq_comp_inv_comb']='</br>**The following companies cause an invalid inverse of existing company relationship combinations: '.html(implode(' / ', $sbsq_comp_inv_comb_err_arr)).'.**';
                    }
                    else
                    {
                      if($row['comp_est_dt'] && $comp_est_dt && $row['comp_est_dt']!==$comp_est_dt)
                      {$sbsq_comp_est_dt_mtch_err_arr[]=$sbsq_comp_url_lnk; $errors['sbsq_comp_est_dt_mtch']='</br>**Subsequently named companies must match established date of this company. Please amend: '.implode(' / ', $sbsq_comp_est_dt_mtch_err_arr).'**';}
                      if($row['comp_dslv_dt'] && $comp_dslv_dt && $row['comp_dslv_dt']!==$comp_dslv_dt)
                      {$sbsq_comp_dslv_dt_mtch_err_arr[]=$sbsq_comp_url_lnk; $errors['sbsq_comp_dslv_dt_mtch']='</br>**Subsequently named companies must match dissolved date of this company. Please amend: '.implode(' / ', $sbsq_comp_dslv_dt_mtch_err_arr).'**';}
                      if(($row['comp_nm_frm_dt'] && $comp_nm_frm_dt && $row['comp_nm_frm_dt'] <= $comp_nm_frm_dt) || ($row['comp_nm_exp_dt'] && $comp_nm_frm_dt && $row['comp_nm_exp_dt'] <= $comp_nm_frm_dt) || ($row['comp_nm_frm_dt'] && $comp_nm_exp_dt && $row['comp_nm_frm_dt'] <= $comp_nm_exp_dt) || ($row['comp_nm_exp_dt'] && $comp_nm_exp_dt && $row['comp_nm_exp_dt'] <= $comp_nm_exp_dt))
                      {$sbsq_comp_nm_dt_mtch_err_arr[]=$sbsq_comp_url_lnk; $errors['sbsq_comp_nm_dt_mtch']='</br>**Subsequently named companies must commence name usage after expiry of name of this company. Please amend: '.implode(' / ', $sbsq_comp_nm_dt_mtch_err_arr).'**';}
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $prvs_comp_list))
    {
      $prvs_comp_nms=explode(',,', $_POST['prvs_comp_list']);
      if(count($prvs_comp_nms)>250)
      {$errors['prvs_comp_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $prvs_comp_empty_err_arr=array(); $prvs_comp_hyphn_excss_err_arr=array(); $prvs_comp_sffx_err_arr=array();
        $prvs_comp_hyphn_err_arr=array(); $prvs_comp_dplct_arr=array(); $prvs_comp_url_err_arr=array();
        $prvs_comp_inv_comb_err_arr=array(); $prvs_comp_est_dt_mtch_err_arr=array(); $prvs_comp_dslv_dt_mtch_err_arr=array();
        $prvs_comp_nm_dt_mtch_err_arr=array(); $prvs_comp_nm_exp_err_arr=array();
        foreach($prvs_comp_nms as $prvs_comp_nm)
        {
          $prvs_comp_errors=0; $prvs_comp_nm=trim($prvs_comp_nm);
          if(!preg_match('/\S+/', $prvs_comp_nm))
          {
            $prvs_comp_empty_err_arr[]=$prvs_comp_nm;
            if(count($prvs_comp_empty_err_arr)==1) {$errors['prvs_comp_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['prvs_comp_empty']='</br>**There are '.count($prvs_comp_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $prvs_comp_nm_err=$prvs_comp_nm;
            if(substr_count($prvs_comp_nm, '--')>1) {$prvs_comp_errors++; $prvs_comp_sffx_num='0'; $prvs_comp_hyphn_excss_err_arr[]=$prvs_comp_nm_err; $errors['prvs_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per company. Please amend: '.html(implode(' / ', $prvs_comp_hyphn_excss_err_arr)).'.**';}
            elseif(preg_match('/^\S+.*--.+$/', $prvs_comp_nm))
            {
              list($prvs_comp_nm_no_sffx, $prvs_comp_sffx_num)=explode('--', $prvs_comp_nm); $prvs_comp_nm_no_sffx=trim($prvs_comp_nm_no_sffx); $prvs_comp_sffx_num=trim($prvs_comp_sffx_num);
              if(!preg_match('/^[1-9][0-9]{0,1}$/', $prvs_comp_sffx_num)) {$prvs_comp_errors++; $prvs_comp_sffx_num='0'; $prvs_comp_sffx_err_arr[]=$prvs_comp_nm_err; $errors['prvs_comp_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $prvs_comp_sffx_err_arr)).'**';}
              $prvs_comp_nm=$prvs_comp_nm_no_sffx;
            }
            elseif(substr_count($prvs_comp_nm, '--')==1) {$prvs_comp_errors++; $prvs_comp_sffx_num='0'; $prvs_comp_hyphn_err_arr[]=$prvs_comp_nm_err; $errors['prvs_comp_hyphn']='</br>**Company suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $prvs_comp_hyphn_err_arr)).'**';}
            else {$prvs_comp_sffx_num='0';}

            if($prvs_comp_sffx_num) {$prvs_comp_sffx_rmn=' ('.romannumeral($prvs_comp_sffx_num).')';} else {$prvs_comp_sffx_rmn='';}

            $prvs_comp_url=generateurl($prvs_comp_nm.$prvs_comp_sffx_rmn);

            $prvs_comp_dplct_arr[]=$prvs_comp_url;
            if(count(array_unique($prvs_comp_dplct_arr))<count($prvs_comp_dplct_arr)) {$errors['prvs_comp_dplct']='</br>**There are entries within the array that create duplicate company URLs.**';}

            if(strlen($prvs_comp_nm)>255 || strlen($prvs_comp_url)>255) {$prvs_comp_errors++; $errors['prvs_comp_nm_excss_lngth']='</br>**Company name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($prvs_comp_errors==0)
            {
              $prvs_comp_nm_cln=cln($prvs_comp_nm);
              $prvs_comp_sffx_num_cln=cln($prvs_comp_sffx_num);
              $prvs_comp_url_cln=cln($prvs_comp_url);

              $sql= "SELECT comp_nm, comp_sffx_num
                    FROM comp
                    WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$prvs_comp_nm_cln' AND comp_sffx_num='$prvs_comp_sffx_num_cln')
                    AND comp_url='$prvs_comp_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing previously named company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['comp_sffx_num']) {$prvs_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];} else {$prvs_comp_url_error_sffx_dsply='';}
                $prvs_comp_url_err_arr[]=$row['comp_nm'].$prvs_comp_url_error_sffx_dsply;
                if(count($prvs_comp_url_err_arr)==1) {$errors['prvs_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $prvs_comp_url_err_arr)).'?**';}
                else {$errors['prvs_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $prvs_comp_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT comp_id, comp_nm, comp_sffx_num, comp_url, comp_est_dt, comp_dslv_dt, comp_nm_frm_dt, comp_nm_exp_dt, comp_nm_exp FROM comp WHERE comp_url='$prvs_comp_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing company URL (against previously known as company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($row['comp_id']==$comp_id) {$errors['prvs_comp_id_mtch']='</br>**You cannot assign this company as a previously named company of itself: '.html($prvs_comp_nm_err).'.**';}
                else
                {
                  $prvs_comp_id=$row['comp_id'];
                  if($row['comp_sffx_num']) {$prvs_comp_sffx_rmn_url_lnk=' ('.romannumeral($row['comp_sffx_num']).')';} else {$prvs_comp_sffx_rmn_url_lnk='';}
                  $prvs_comp_url_lnk='<a href="/company/'.html($row['comp_url']).'" target="/company/'.html($row['comp_url']).'">'.html($row['comp_nm'].$prvs_comp_sffx_rmn_url_lnk).'</a>';
                  $prvs_comp_est_dt=$row['comp_est_dt']; $prvs_comp_dslv_dt=$row['comp_dslv_dt'];
                  $prvs_comp_nm_frm_dt=$row['comp_nm_frm_dt']; $prvs_comp_nm_exp_dt=$row['comp_nm_exp_dt'];
                  $prvs_comp_nm_exp=$row['comp_nm_exp'];

                  $sql="SELECT 1 FROM comp_aka WHERE comp_prvs_id='$comp_id' AND comp_sbsq_id='$prvs_comp_id'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for inverse of proposed combination (previously named companies): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $prvs_comp_inv_comb_err_arr[]=$prvs_comp_nm.$prvs_comp_sffx_rmn;
                    $errors['prvs_comp_inv_comb']='</br>**The following companies cause an invalid inverse of existing company relationship combinations: '.html(implode(' / ', $prvs_comp_inv_comb_err_arr)).'.**';
                  }
                  else
                  {
                    if($prvs_comp_est_dt && $comp_est_dt && $prvs_comp_est_dt!==$comp_est_dt)
                    {$prvs_comp_est_dt_mtch_err_arr[]=$prvs_comp_url_lnk; $errors['prvs_comp_est_dt_mtch']='</br>**Previously named companies must match established date of this company. Please amend: '.implode(' / ', $prvs_comp_est_dt_mtch_err_arr).'**';}
                    if($prvs_comp_dslv_dt && $comp_dslv_dt && $prvs_comp_dslv_dt!==$comp_dslv_dt)
                    {$prvs_comp_dslv_dt_mtch_err_arr[]=$prvs_comp_url_lnk; $errors['prvs_comp_dslv_dt_mtch']='</br>**Previously named companies must match dissolved date of this company. Please amend: '.implode(' / ', $prvs_comp_dslv_dt_mtch_err_arr).'**';}
                    if(($prvs_comp_nm_frm_dt && $comp_nm_frm_dt && $prvs_comp_nm_frm_dt >= $comp_nm_frm_dt) || ($prvs_comp_nm_exp_dt && $comp_nm_frm_dt && $prvs_comp_nm_exp_dt >= $comp_nm_frm_dt) || ($prvs_comp_nm_frm_dt && $comp_nm_exp_dt && $prvs_comp_nm_frm_dt >= $comp_nm_exp_dt) || ($prvs_comp_nm_exp_dt && $comp_nm_exp_dt && $prvs_comp_nm_exp_dt >= $comp_nm_exp_dt))
                    {$prvs_comp_nm_dt_mtch_err_arr[]=$prvs_comp_url_lnk; $errors['prvs_comp_nm_dt_mtch']='</br>**Previously named companies must expire name usage before commencement of name of this company. Please amend: '.implode(' / ', $prvs_comp_nm_dt_mtch_err_arr).'**';}
                    if(!$prvs_comp_nm_exp)
                    {$prvs_comp_nm_exp_err_arr[]=$prvs_comp_url_lnk; $errors['prvs_comp_nm_exp']='</br>**Previously named companies must be set as name being expired. Please amend: '.implode(' / ', $prvs_comp_nm_exp_err_arr).'**';}
                  }
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $comp_lctn_list))
    {
      $comp_lctn_nms=explode(',,', $_POST['comp_lctn_list']);
      if(count($comp_lctn_nms)>250)
      {$errors['comp_lctn_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $comp_lctn_empty_err_arr=array(); $comp_lctn_pipe_excss_err_arr=array(); $comp_lctn_pipe_err_arr=array();
        $comp_lctn_hyphn_excss_err_arr=array(); $comp_lctn_sffx_err_arr=array(); $comp_lctn_hyphn_err_arr=array();
        $comp_lctn_dplct_arr=array(); $comp_lctn_url_err_arr=array(); $comp_lctn_alt_list_err_arr=array();
        $comp_lctn_alt_empty_err_arr=array(); $comp_lctn_alt_hyphn_excss_err_arr=array(); $comp_lctn_alt_sffx_err_arr=array();
        $comp_lctn_alt_hyphn_err_arr=array(); $comp_lctn_alt_dplct_arr=array(); $comp_lctn_alt_url_err_arr=array();
        $comp_lctn_alt_err_arr=array(); $comp_lctn_alt_no_assocs=array(); $comp_lctn_alt_assoc_err_arr=array();
        foreach($comp_lctn_nms as $comp_lctn_nm)
        {
          $comp_lctn_errors=0;

          $comp_lctn_nm=trim($comp_lctn_nm);
          if(!preg_match('/\S+/', $comp_lctn_nm))
          {
            $comp_lctn_empty_err_arr[]=$comp_lctn_nm;
            if(count($comp_lctn_empty_err_arr)==1) {$errors['comp_lctn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['comp_lctn_empty']='</br>**There are '.count($comp_lctn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($comp_lctn_nm, '||')>1) {$comp_lctn_errors++; $comp_lctn_pipe_excss_err_arr[]=$comp_lctn_nm; $comp_lctn_alt_list=''; $errors['comp_lctn_pipe_excss']='</br>**You may only use [||] once per location for alternate location assignation. Please amend: '.html(implode(' / ', $comp_lctn_pipe_excss_err_arr)).'.**';}
            elseif(preg_match('/\S+.*\|\|.*\S+/', $comp_lctn_nm))
            {
              list($comp_lctn_nm, $comp_lctn_alt_list)=explode('||', $comp_lctn_nm);
              $comp_lctn_nm=trim($comp_lctn_nm); $comp_lctn_alt_list=trim($comp_lctn_alt_list);
            }
            elseif(substr_count($comp_lctn_nm, '||')==1) {$comp_lctn_errors++; $comp_lctn_pipe_err_arr[]=$comp_lctn_nm; $comp_lctn_alt_list=''; $errors['comp_lctn_pipe']='</br>**Alternate location assignation must use [||] in the correct format. Please amend: '.html(implode(' / ', $comp_lctn_pipe_err_arr)).'.**';}
            else {$comp_lctn_alt_list='';}

            if(substr_count($comp_lctn_nm, '--')>1)
            {
              $comp_lctn_errors++; $comp_lctn_sffx_num='0'; $comp_lctn_hyphn_excss_err_arr[]=$comp_lctn_nm;
              $errors['comp_lctn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per company location. Please amend: '.html(implode(' / ', $comp_lctn_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $comp_lctn_nm))
            {
              list($comp_lctn_nm_no_sffx, $comp_lctn_sffx_num)=explode('--', $comp_lctn_nm);
              $comp_lctn_nm_no_sffx=trim($comp_lctn_nm_no_sffx); $comp_lctn_sffx_num=trim($comp_lctn_sffx_num);

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $comp_lctn_sffx_num))
              {
                $comp_lctn_errors++; $comp_lctn_sffx_num='0'; $comp_lctn_sffx_err_arr[]=$comp_lctn_nm;
                $errors['comp_lctn_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $comp_lctn_sffx_err_arr)).'**';
              }
              $comp_lctn_nm=$comp_lctn_nm_no_sffx;
            }
            elseif(substr_count($comp_lctn_nm, '--')==1)
            {$comp_lctn_errors++; $comp_lctn_sffx_num='0'; $comp_lctn_hyphn_err_arr[]=$comp_lctn_nm;
            $errors['comp_lctn_hyphn']='</br>**Company location suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $comp_lctn_hyphn_err_arr)).'**';}
            else
            {$comp_lctn_sffx_num='0';}

            if($comp_lctn_sffx_num) {$comp_lctn_sffx_rmn=' ('.romannumeral($comp_lctn_sffx_num).')';} else {$comp_lctn_sffx_rmn='';}

            $comp_lctn_url=generateurl($comp_lctn_nm.$comp_lctn_sffx_rmn);

            $comp_lctn_dplct_arr[]=$comp_lctn_url;
            if(count(array_unique($comp_lctn_dplct_arr))<count($comp_lctn_dplct_arr))
            {$errors['comp_lctn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($comp_lctn_nm)>255 || strlen($comp_lctn_url)>255)
            {$comp_lctn_errors++; $errors['comp_lctn_nm_excss_lngth']='</br>**Company location and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($comp_lctn_errors==0)
            {
              $comp_lctn_nm_cln=cln($comp_lctn_nm);
              $comp_lctn_sffx_num_cln=cln($comp_lctn_sffx_num);
              $comp_lctn_url_cln=cln($comp_lctn_url);

              $sql= "SELECT lctn_nm, lctn_sffx_num
                    FROM lctn
                    WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$comp_lctn_nm_cln' AND lctn_sffx_num='$comp_lctn_sffx_num_cln')
                    AND lctn_url='$comp_lctn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing (company) location URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['lctn_sffx_num']) {$comp_lctn_url_err_sffx_num='--'.$row['lctn_sffx_num'];} else {$comp_lctn_url_err_sffx_num='';}
                $comp_lctn_url_err_arr[]=$row['lctn_nm'].$comp_lctn_url_err_sffx_num;
                if(count($comp_lctn_url_err_arr)==1) {$errors['comp_lctn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $comp_lctn_url_err_arr)).'?**';}
                else {$errors['comp_lctn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $comp_lctn_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT lctn_id, lctn_nm, lctn_sffx_num, lctn_url FROM lctn WHERE lctn_url='$comp_lctn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($comp_lctn_alt_list)
                {
                  if(mysqli_num_rows($result)==0) {$comp_lctn_alt_list_err_arr[]=$comp_lctn_nm.$comp_lctn_sffx_rmn; $errors['comp_lctn_alt_list']='</br>**The following locations do not yet exist (and therefore cannot be assigned alternate locations): '.html(implode(' / ', $comp_lctn_alt_list_err_arr)).'.**';}
                  else
                  {
                    $lctn_id=$row['lctn_id'];
                    if($row['lctn_sffx_num']) {$lctn_sffx_rmn_url_lnk=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$lctn_sffx_rmn_url_lnk='';}
                    $lctn_url_lnk='<a href="/company/location/'.html($row['lctn_url']).'" target="/company/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm'].$lctn_sffx_rmn_url_lnk).'</a>';

                    $comp_lctn_alts=explode('>>', $comp_lctn_alt_list);
                    if(count($comp_lctn_alts)>250)
                    {$errors['comp_lctn_alt_array_excss']='**Maximum of 250 locations per alternate location array allowed.**';}
                    else
                    {
                      $comp_lctn_alt_dplct_arr=array();
                      foreach($comp_lctn_alts as $comp_lctn_alt)
                      {
                        $comp_lctn_alt=trim($comp_lctn_alt);
                        if(!preg_match('/\S+/', $comp_lctn_alt))
                        {
                          $comp_lctn_alt_empty_err_arr[]=$comp_lctn_alt;
                          if(count($comp_lctn_alt_empty_err_arr)==1) {$errors['comp_lctn_alt_empty']='</br>**There is 1 empty entry in an alternate location array (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                          else {$errors['comp_lctn_alt_empty']='</br>**There are '.count($comp_lctn_alt_empty_err_arr).' empty entries in alternate location arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                        }
                        else
                        {
                          $comp_lctn_alt_errors=0;

                          if(substr_count($comp_lctn_alt, '--')>1)
                          {
                            $comp_lctn_alt_errors++; $comp_lctn_alt_sffx_num='0'; $comp_lctn_alt_hyphn_excss_err_arr[]=$comp_lctn_alt;
                            $errors['comp_lctn_alt_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per alternate location. Please amend: '.html(implode(' / ', $comp_lctn_alt_hyphn_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/^\S+.*--.+$/', $comp_lctn_alt))
                          {
                            list($comp_lctn_alt_no_sffx, $comp_lctn_alt_sffx_num)=explode('--', $comp_lctn_alt);
                            $comp_lctn_alt_no_sffx=trim($comp_lctn_alt_no_sffx); $comp_lctn_alt_sffx_num=trim($comp_lctn_alt_sffx_num);

                            if(!preg_match('/^[1-9][0-9]{0,1}$/', $comp_lctn_alt_sffx_num))
                            {
                              $comp_lctn_alt_errors++; $comp_lctn_alt_sffx_num='0'; $comp_lctn_alt_sffx_err_arr[]=$comp_lctn_alt;
                              $errors['comp_lctn_alt_sffx']='</br>**The suffix (for alternate locations) must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $comp_lctn_alt_sffx_err_arr)).'**';
                            }
                            $comp_lctn_alt=$comp_lctn_alt_no_sffx;
                          }
                          elseif(substr_count($comp_lctn_alt, '--')==1)
                          {$comp_lctn_alt_errors++; $comp_lctn_alt_sffx_num='0'; $comp_lctn_alt_hyphn_err_arr[]=$comp_lctn_alt;
                          $errors['comp_lctn_alt_hyphn']='</br>**Alternate location suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $comp_lctn_alt_hyphn_err_arr)).'**';}
                          else
                          {$comp_lctn_alt_sffx_num='0';}

                          if($comp_lctn_alt_sffx_num) {$comp_lctn_alt_sffx_rmn=' ('.romannumeral($comp_lctn_alt_sffx_num).')';} else {$comp_lctn_alt_sffx_rmn='';}

                          $comp_lctn_alt_url=generateurl($comp_lctn_alt.$comp_lctn_alt_sffx_rmn);
                          $comp_lctn_alt_dplct_arr[]=$comp_lctn_alt_url;
                          if(count(array_unique($comp_lctn_alt_dplct_arr))<count($comp_lctn_alt_dplct_arr))
                          {$errors['comp_lctn_alt_dplct']='</br>**There are entries within alternate location arrays that create duplicate location URLs.**';}

                          if(strlen($comp_lctn_alt)>255 || strlen($comp_lctn_alt_url)>255)
                          {$comp_lctn_alt_errors++; $errors['comp_lctn_alt_excss_lngth']='</br>**Alternate location name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                          if($comp_lctn_alt_errors==0)
                          {
                            $comp_lctn_alt_cln=cln($comp_lctn_alt);
                            $comp_lctn_alt_sffx_num_cln=cln($comp_lctn_alt_sffx_num);
                            $comp_lctn_alt_url_cln=cln($comp_lctn_alt_url);

                            $sql= "SELECT lctn_nm FROM lctn
                                  WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$comp_lctn_alt_cln' AND lctn_sffx_num='$comp_lctn_alt_sffx_num_cln')
                                  AND lctn_url='$comp_lctn_alt_url_cln'";
                            $result=mysqli_query($link, $sql);
                            if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                            $row=mysqli_fetch_array($result);
                            if(mysqli_num_rows($result)>0)
                            {
                              $comp_lctn_alt_url_err_arr[]=$row['lctn_nm'];
                              if(count($comp_lctn_alt_url_err_arr)==1) {$errors['comp_lctn_alt_url']='</br>**Duplicate location URL exists (from alternate location arrays). Did you mean to type: '.html(implode(' / ', $comp_lctn_alt_url_err_arr)).'?**';}
                              else {$errors['comp_lctn_alt_url']='</br>**Duplicate location URLs exist (from alternate location arrays). Did you mean to type: '.html(implode(' / ', $comp_lctn_alt_url_err_arr)).'?**';}
                            }
                            else
                            {
                              $sql="SELECT lctn_id FROM lctn WHERE lctn_url='$comp_lctn_alt_url_cln'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking for existence of location (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              $row=mysqli_fetch_array($result);
                              if(mysqli_num_rows($result)==0) {$comp_lctn_alt_err_arr[]=$comp_lctn_alt.$comp_lctn_alt_sffx_rmn; $errors['comp_lctn_alt']='</br>**The following locations from alternate location arrays do not yet exist (and can therefore not be assigned): '.html(implode(' / ', $comp_lctn_alt_err_arr)).'.';}
                              else
                              {
                                $lctn_alt_id=$row['lctn_id'];
                                $sql="SELECT 1 FROM rel_lctn WHERE rel_lctn1='$lctn_id' AND rel_lctn2='$lctn_alt_id'";
                                $result=mysqli_query($link, $sql);
                                if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                                $row=mysqli_fetch_array($result);
                                if(mysqli_num_rows($result)==0) {$comp_lctn_alt_no_assocs[]=$comp_lctn_alt.$comp_lctn_alt_sffx_rmn;}
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }
            }
          }

          if(count($comp_lctn_alt_no_assocs)>0)
          {
            $comp_lctn_alt_assoc_err_arr[]=$lctn_url_lnk.': '.implode(' / ', $comp_lctn_alt_no_assocs);
            $errors['comp_lctn_alt_assoc']='</br>**Associations do not exist between the following locations and their listed alternates. Please amend:**</br>'.implode('</br>', $comp_lctn_alt_assoc_err_arr);
          }
        }
      }
    }

    if(preg_match('/\S+/', $comp_typ_list))
    {
      $comp_typ_nms=explode(',,', $comp_typ_list);
      if(count($comp_typ_nms)>250)
      {$errors['comp_typ_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $comp_typ_empty_err_arr=array(); $comp_typ_dplct_arr=array(); $comp_typ_url_err_arr=array();
        foreach($comp_typ_nms as $comp_typ_nm)
        {
          $comp_typ_nm=trim($comp_typ_nm);
          if(!preg_match('/\S+/', $comp_typ_nm))
          {
            $comp_typ_empty_err_arr[]=$comp_typ_nm;
            if(count($comp_typ_empty_err_arr)==1) {$errors['comp_typ_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['comp_typ_empty']='</br>**There are '.count($comp_typ_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $comp_typ_url=generateurl($comp_typ_nm);
            $comp_typ_dplct_arr[]=$comp_typ_url;
            if(count(array_unique($comp_typ_dplct_arr))<count($comp_typ_dplct_arr)) {$errors['comp_typ_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($comp_typ_nm)>255) {$errors['comp_typ_nm_excss_lngth']='</br>**Company type name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

            $sql= "SELECT comp_typ_nm
                  FROM comp_typ
                  WHERE NOT EXISTS (SELECT 1 FROM comp_typ WHERE comp_typ_nm='$comp_typ_nm')
                  AND comp_typ_url='$comp_typ_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing company type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            $row=mysqli_fetch_array($result);
            if(mysqli_num_rows($result)>0)
            {
              $comp_typ_url_err_arr[]=$row['comp_typ_nm'];
              if(count($comp_typ_url_err_arr)==1)
              {$errors['comp_typ_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $comp_typ_url_err_arr)).'?**';}
              else
              {$errors['comp_typ_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $comp_typ_url_err_arr)).'?**';}
            }
          }
        }
      }
    }

    $new_comp_prsn_ids_array=array();
    $exstng_agnt_prsn_ids_array=array(); $exstng_lcnsr_prsn_ids_array=array();
    if(preg_match('/\S+/', $_POST['comp_prsn_list']))
    {
      $comp_ppl=explode(',,', $_POST['comp_prsn_list']);

      if(count($comp_ppl)>250)
      {
        $errors['comp_prsn_nm_rl_array_excss']='**Maximum of 250 entries allowed.**';
      }
      else
      {
        $comp_prsn_empty_err_arr=array(); $comp_prsn_hsh_excss_err_arr=array(); $comp_prsn_yr_err_arr=array();
        $comp_prsn_yr_frmt_err_arr=array(); $comp_prsn_hsh_err_arr=array(); $comp_prsn_cln_excss_err_arr=array();
        $comp_prsn_rl_smcln_excss_err_arr=array(); $comp_prsn_rl_smcln_err_arr=array(); $comp_prsn_cln_err_arr=array();
        $comp_prsn_hyphn_excss_err_arr=array(); $comp_prsn_sffx_err_arr=array(); $comp_prsn_hyphn_err_arr=array();
        $comp_prsn_smcln_excss_err_arr=array(); $comp_prsn_dplct_arr=array(); $comp_prsn_smcln_err_arr=array();
        $comp_prsn_nm_err_arr=array(); $comp_prsn_url_err_arr=array();
        foreach($comp_ppl as $comp_prsn_nm_yr)
        {
          $comp_prsn_errors=0;
          if(!preg_match('/\S+/', $comp_prsn_nm_yr))
          {
            $comp_prsn_empty_err_arr[]=$comp_prsn_nm_yr;
            if(count($comp_prsn_empty_err_arr)==1) {$errors['comp_prsn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['comp_prsn_empty']='</br>**There are '.count($comp_prsn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($comp_prsn_nm_yr, '##')>1)
            {
              $comp_prsn_errors++; $comp_prsn_yr_strt=NULL; $comp_prsn_yr_end=NULL; $comp_prsn_hsh_excss_err_arr[]=$comp_prsn_nm_yr;
              $errors['comp_prsn_hsh_excss']='</br>**You may only use [##] for year assignment once per company member. Please amend: '.html(implode(' / ', $comp_prsn_hsh_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*##.*\S+$/', $comp_prsn_nm_yr))
            {
              list($comp_prsn, $comp_prsn_yr)=explode('##', $comp_prsn_nm_yr);
              $comp_prsn=trim($comp_prsn); $comp_prsn_yr=trim($comp_prsn_yr);

              if(preg_match('/^[1-9][0-9]{0,3}(\s*;;\s*[1-9][0-9]{0,3})?$/', $comp_prsn_yr))
              {
                if(preg_match('/^[1-9][0-9]{0,3}\s*;;\s*[1-9][0-9]{0,3}$/', $comp_prsn_yr))
                {
                  list($comp_prsn_yr_strt, $comp_prsn_yr_end)=explode(';;', $comp_prsn_yr);
                  $comp_prsn_yr_strt=trim($comp_prsn_yr_strt); $comp_prsn_yr_end=trim($comp_prsn_yr_end);

                  if($comp_prsn_yr_strt>$comp_prsn_yr_end)
                  {
                    $comp_prsn_yr_err_arr[]=$comp_prsn_nm_yr;
                    $errors['comp_prsn_yr']='</br>**Year started must be earlier than year written. Please amend: '.html(implode(' / ', $comp_prsn_yr_err_arr)).'**';
                  }
                }
              }
              else
              {
                $comp_prsn_yr_strt=NULL; $comp_prsn_yr_end=NULL; $comp_prsn_yr_frmt_err_arr[]=$comp_prsn_nm_yr;
                $errors['comp_prsn_yr_frmt']='</br>**The following company members have not been assigned a valid year (or years). Please amend: '.html(implode(' / ', $comp_prsn_yr_frmt_err_arr)).'.**';
              }
            }
            elseif(substr_count($comp_prsn_nm_yr, '##')==1)
            {$comp_prsn_errors++;  $comp_prsn_yr_strt=NULL; $comp_prsn_yr_end=NULL; $comp_prsn_hsh_err_arr[]=$comp_prsn_nm_yr;
            $errors['comp_prsn_hsh']='</br>**Company member year assignation must use [##] in the correct format. Please amend: '.html(implode(' / ', $comp_prsn_hsh_err_arr)).'**';}
            else {$comp_prsn=$comp_prsn_nm_yr;}

            if(substr_count($comp_prsn, '::')>1)
            {
              $comp_prsn_errors++; $comp_prsn_cln_excss_err_arr[]=$comp_prsn;
              $errors['comp_prsn_cln_excss']='</br>**You may only use [::] once per company member-role coupling. Please amend: '.html(implode(' / ', $comp_prsn_cln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*::.*\S+/', $comp_prsn))
            {
              list($comp_prsn, $comp_prsn_rl)=explode('::', $comp_prsn);
              $comp_prsn=trim($comp_prsn); $comp_prsn_rl=trim($comp_prsn_rl);

              if(substr_count($comp_prsn_rl, ';;')>1)
              {
                $comp_prsn_errors++; $comp_prsn_rl_smcln_excss_err_arr[]=$comp_prsn_rl;
                $errors['comp_prsn_rl_smcln_excss']='</br>**You may only use [;;] once per company member role-note coupling. Please amend: '.html(implode(' / ', $comp_prsn_rl_smcln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*;;.*\S+/', $comp_prsn_rl))
              {
                list($comp_prsn_rl, $comp_prsn_rl_nt)=explode(';;', $comp_prsn_rl);
                $comp_prsn_rl=trim($comp_prsn_rl); $comp_prsn_rl_nt=trim($comp_prsn_rl_nt);

                if(strlen($comp_prsn_rl_nt)>255)
                {$errors['comp_prsn_rl_nt_excss_lngth']='</br>**Company member role note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
              }
              elseif(substr_count($comp_prsn_rl, ';;')==1)
              {$comp_prsn_errors++; $comp_prsn_rl_smcln_err_arr[]=$comp_prsn_rl;
              $errors['comp_prsn_rl_smcln']='</br>**Company member role note assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $comp_prsn_rl_smcln_err_arr)).'**';}

              if(strlen($comp_prsn_rl)>255)
              {$errors['comp_prsn_rl_excss_lngth']='</br>**Company member role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              if(substr_count($comp_prsn, '--')>1)
              {
                $comp_prsn_errors++; $comp_prsn_sffx_num='0'; $comp_prsn_hyphn_excss_err_arr[]=$comp_prsn;
                $errors['comp_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per course staff member. Please amend: '.html(implode(' / ', $comp_prsn_hyphn_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*--.+$/', $comp_prsn))
              {
                list($comp_prsn_nm_no_sffx, $comp_prsn_sffx_num)=explode('--', $comp_prsn);
                $comp_prsn_nm_no_sffx=trim($comp_prsn_nm_no_sffx); $comp_prsn_sffx_num=trim($comp_prsn_sffx_num);

                if(!preg_match('/^[1-9][0-9]{0,1}$/', $comp_prsn_sffx_num))
                {
                  $comp_prsn_errors++; $comp_prsn_sffx_num='0'; $comp_prsn_sffx_err_arr[]=$comp_prsn;
                  $errors['comp_prsn_sffx']= '</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $comp_prsn_sffx_err_arr)).'**';
                }
                $comp_prsn=$comp_prsn_nm_no_sffx;
              }
              elseif(substr_count($comp_prsn, '--')==1)
              {$comp_prsn_errors++; $comp_prsn_sffx_num='0'; $comp_prsn_hyphn_err_arr[]=$comp_prsn;
              $errors['comp_prsn_hyphn']='</br>**Company member suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $comp_prsn_hyphn_err_arr)).'**';}
              else
              {$comp_prsn_sffx_num='0';}

              if($comp_prsn_sffx_num) {$comp_prsn_sffx_rmn=' ('.romannumeral($comp_prsn_sffx_num).')';} else {$comp_prsn_sffx_rmn='';}

              if(substr_count($comp_prsn, ';;')>1)
              {
                $comp_prsn_errors++; $comp_prsn_frst_nm=''; $comp_prsn_lst_nm=''; $comp_prsn_fll_nm=''; $comp_prsn_url='';
                $comp_prsn_smcln_excss_err_arr[]=$comp_prsn;
                $errors['comp_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $comp_prsn_smcln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*;;(.*\S+)?/', $comp_prsn))
              {
                list($comp_prsn_frst_nm, $comp_prsn_lst_nm)=explode(';;', $comp_prsn);
                $comp_prsn_frst_nm=trim($comp_prsn_frst_nm); $comp_prsn_lst_nm=trim($comp_prsn_lst_nm);

                if(preg_match('/\S+/', $comp_prsn_lst_nm)) {$comp_prsn_lst_nm_dsply=' '.$comp_prsn_lst_nm;}
                else {$comp_prsn_lst_nm_dsply='';}

                $comp_prsn_fll_nm=$comp_prsn_frst_nm.$comp_prsn_lst_nm_dsply;
                $comp_prsn_url=generateurl($comp_prsn_fll_nm.$comp_prsn_sffx_rmn);

                $comp_prsn_dplct_arr[]=$comp_prsn_url;
                if(count(array_unique($comp_prsn_dplct_arr))<count($comp_prsn_dplct_arr))
                {$errors['comp_prsn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

                if(strlen($comp_prsn_fll_nm)>255 || strlen($comp_prsn_url)>255)
                {$comp_prsn_errors++; $errors['comp_prsn_excss_lngth']='</br>**Course staff (person) full name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
              }
              else
              {
                $comp_prsn_errors++; $comp_prsn_smcln_err_arr[]=$comp_prsn;
                $errors['comp_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $comp_prsn_smcln_err_arr)).'.**';
              }
            }
            else
            {
              $comp_prsn_errors++; $comp_prsn_cln_err_arr[]=$comp_prsn;
              $errors['comp_prsn_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $comp_prsn_cln_err_arr)).'.**';
            }

            if($comp_prsn_errors==0)
            {
              $comp_prsn_frst_nm_cln=cln($comp_prsn_frst_nm);
              $comp_prsn_lst_nm_cln=cln($comp_prsn_lst_nm);
              $comp_prsn_fll_nm_cln=cln($comp_prsn_fll_nm);
              $comp_prsn_sffx_num_cln=cln($comp_prsn_sffx_num);
              $comp_prsn_url_cln=cln($comp_prsn_url);

              $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                    FROM prsn
                    WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$comp_prsn_frst_nm_cln' AND prsn_lst_nm='$comp_prsn_lst_nm_cln')
                    AND prsn_fll_nm='$comp_prsn_fll_nm_cln' AND prsn_sffx_num='$comp_prsn_sffx_num_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for (staff) person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $comp_prsn_errors++;
                if($row['prsn_sffx_num']) {$comp_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                else {$comp_prsn_nm_error_sffx_dsply='';}
                $comp_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$comp_prsn_nm_error_sffx_dsply;
                if(count($comp_prsn_nm_err_arr)==1)
                {$errors['comp_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $comp_prsn_nm_err_arr)).'.**';}
                else
                {$errors['comp_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $comp_prsn_nm_err_arr)).'.**';}
              }

              $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                    FROM prsn
                    WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$comp_prsn_fll_nm_cln' AND prsn_sffx_num='$comp_prsn_sffx_num_cln')
                    AND prsn_url='$comp_prsn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing (staff) person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $comp_prsn_errors++;
                if($row['prsn_sffx_num']) {$comp_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                else {$comp_prsn_url_error_sffx_dsply='';}
                $comp_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$comp_prsn_url_error_sffx_dsply;
                if(count($comp_prsn_url_err_arr)==1)
                {$errors['comp_prsn_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $comp_prsn_url_err_arr)).'?**';}
                else
                {$errors['comp_prsn_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $comp_prsn_url_err_arr)).'?**';}
              }
            }

            if($comp_prsn_errors==0)
            {
              $sql="SELECT prsn_id FROM prsn WHERE prsn_url='$comp_prsn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing person URL (against company member URL): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0) {$new_comp_prsn_ids_array[]=$row['prsn_id'];}
            }
          }
        }
      }
    }

    $sql="SELECT DISTINCT agnt_prsnid FROM compprsn INNER JOIN prsnagnt ON compid=agnt_compid WHERE compid='$comp_id' AND agnt_prsnid!=0";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for members of this company with associations as agent: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$exstng_agnt_prsn_ids_array[]=$row['agnt_prsnid'];}

    $rmvd_agnts_array=array_diff($exstng_agnt_prsn_ids_array, $new_comp_prsn_ids_array);
    if(count($rmvd_agnts_array)>0)
    {$errors['rmvd_agnts']='</br>**The changes being made will delete company-member associations required for existing client-representation relationships.**';}

    $sql="SELECT DISTINCT lcnsr_prsnid FROM compprsn INNER JOIN ptlcnsr ON compid=lcnsr_compid WHERE compid='$comp_id' AND lcnsr_prsnid!=0";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for members of this company with associations as licensor: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$exstng_lcnsr_prsn_ids_array[]=$row['lcnsr_prsnid'];}

    $rmvd_lcnsrs_array=array_diff($exstng_lcnsr_prsn_ids_array, $new_comp_prsn_ids_array);
    if(count($rmvd_lcnsrs_array)>0)
    {$errors['rmvd_lcnsrs']='</br>**The changes being made will delete company-member associations required for existing playtext-licensor relationships.**';}

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $comp_id=cln($_POST['comp_id']);
      $sql= "SELECT comp_nm, comp_sffx_num
            FROM comp
            WHERE comp_id='$comp_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring company details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $row=mysqli_fetch_array($result);
      if($row['comp_sffx_num']) {$comp_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';} else {$comp_sffx_rmn='';}
      $pagetab='Edit: '.html($row['comp_nm'].$comp_sffx_rmn);
      $pagetitle=html($row['comp_nm'].$comp_sffx_rmn);
      $comp_nm=$_POST['comp_nm'];
      $comp_sffx_num=$_POST['comp_sffx_num'];
      $comp_adrs_list=$_POST['comp_adrs_list'];
      $comp_reg_nm=$_POST['comp_reg_nm'];
      $comp_reg_adrs=$_POST['comp_reg_adrs'];
      $comp_est_dt=$_POST['comp_est_dt'];
      $comp_dslv_dt=$_POST['comp_dslv_dt'];
      $comp_nm_frm_dt=$_POST['comp_nm_frm_dt'];
      $comp_nm_exp_dt=$_POST['comp_nm_exp_dt'];
      $sbsq_comp_list=$_POST['sbsq_comp_list'];
      $prvs_comp_list=$_POST['prvs_comp_list'];
      $comp_lctn_list=$_POST['comp_lctn_list'];
      $comp_typ_list=$_POST['comp_typ_list'];
      $comp_prsn_list=$_POST['comp_prsn_list'];
      $textarea=$_POST['textarea'];
      $errors['comp_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $comp_id=html($comp_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE comp SET
            comp_nm='$comp_nm',
            comp_alph=CASE WHEN '$comp_alph'!='' THEN '$comp_alph' END,
            comp_sffx_num='$comp_sffx_num',
            comp_url='$comp_url',
            comp_reg_nm='$comp_reg_nm',
            comp_reg_adrs='$comp_reg_adrs',
            comp_est_dt=CASE WHEN '$comp_est_dt'!='' THEN '$comp_est_dt' END,
            comp_est_dt_frmt=CASE WHEN '$comp_est_dt'!='' THEN '$comp_est_dt_frmt' END,
            comp_dslv_dt=CASE WHEN '$comp_dslv_dt'!='' THEN '$comp_dslv_dt' END,
            comp_dslv_dt_frmt=CASE WHEN '$comp_dslv_dt'!='' THEN '$comp_dslv_dt_frmt' END,
            comp_nm_frm_dt=CASE WHEN '$comp_nm_frm_dt'!='' THEN '$comp_nm_frm_dt' END,
            comp_nm_frm_dt_frmt=CASE WHEN '$comp_nm_frm_dt'!='' THEN '$comp_nm_frm_dt_frmt' END,
            comp_nm_exp_dt=CASE WHEN '$comp_nm_exp_dt'!='' THEN '$comp_nm_exp_dt' END,
            comp_nm_exp_dt_frmt=CASE WHEN '$comp_nm_exp_dt'!='' THEN '$comp_nm_exp_dt_frmt' END,
            comp_dslv='$comp_dslv',
            comp_nm_exp='$comp_nm_exp'
            WHERE comp_id='$comp_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating company info for submitted company: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM compadrs WHERE compid='$comp_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting company-address associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $comp_adrs_list))
      {
        $comp_adrss=explode('//', $comp_adrs_list);
        $n=0;
        foreach($comp_adrss as $comp_adrs)
        {
          $comp_adrs=trim($comp_adrs);
          $comp_adrs_ordr=++$n;

          if(preg_match('/\S+.*::.*\S+/', $comp_adrs)) {list($comp_adrs_ttl, $comp_adrs)=explode('::', $comp_adrs); $comp_adrs_ttl=trim($comp_adrs_ttl); $comp_adrs=trim($comp_adrs);}
          else {$comp_adrs_ttl='';}

          $sql= "INSERT INTO compadrs(compid, comp_adrs, comp_adrs_ttl, comp_adrs_ordr)
                SELECT '$comp_id', '$comp_adrs', '$comp_adrs_ttl', '$comp_adrs_ordr'";
          if(!mysqli_query($link, $sql)) {$error='Error adding company-address association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM comp_aka WHERE comp_prvs_id='$comp_id' OR comp_sbsq_id='$comp_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting company-previously/subsequently known as associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $sbsq_comp_list))
      {
        $sbsq_comp_nms=explode(',,', $sbsq_comp_list);
        foreach($sbsq_comp_nms as $sbsq_comp_nm)
        {
          $sbsq_comp_nm=trim($sbsq_comp_nm);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $sbsq_comp_nm))
          {
            list($sbsq_comp_nm, $sbsq_comp_sffx_num)=explode('--', $sbsq_comp_nm);
            $sbsq_comp_nm=trim($sbsq_comp_nm); $sbsq_comp_sffx_num=trim($sbsq_comp_sffx_num);
            $sbsq_comp_sffx_rmn=' ('.romannumeral($sbsq_comp_sffx_num).')';
          }
          else
          {$sbsq_comp_sffx_num='0'; $sbsq_comp_sffx_rmn='';}

          $sbsq_comp_alph=alph($sbsq_comp_nm);
          $sbsq_comp_url=generateurl($sbsq_comp_nm.$sbsq_comp_sffx_rmn);

          $sql="SELECT 1 FROM comp WHERE comp_url='$sbsq_comp_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of company (against subsequently known as company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool)
                  VALUES('$sbsq_comp_nm', CASE WHEN '$sbsq_comp_alph'!='' THEN '$sbsq_comp_alph' END, '$sbsq_comp_sffx_num', '$sbsq_comp_url', '1')";
            if(!mysqli_query($link, $sql)) {$error='Error adding creative (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO comp_aka(comp_prvs_id, comp_sbsq_id)
                SELECT $comp_id, comp_id FROM comp WHERE comp_url='$sbsq_comp_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding company-subsequently known as association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $prvs_comp_list))
      {
        $prvs_comp_nms=explode(',,', $prvs_comp_list);
        foreach($prvs_comp_nms as $prvs_comp_nm)
        {
          $prvs_comp_nm=trim($prvs_comp_nm);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prvs_comp_nm))
          {
            list($prvs_comp_nm, $prvs_comp_sffx_num)=explode('--', $prvs_comp_nm);
            $prvs_comp_nm=trim($prvs_comp_nm); $prvs_comp_sffx_num=trim($prvs_comp_sffx_num);
            $prvs_comp_sffx_rmn=' ('.romannumeral($prvs_comp_sffx_num).')';
          }
          else
          {$prvs_comp_sffx_num='0'; $prvs_comp_sffx_rmn='';}

          $prvs_comp_alph=alph($prvs_comp_nm);
          $prvs_comp_url=generateurl($prvs_comp_nm.$prvs_comp_sffx_rmn);

          $sql="SELECT 1 FROM comp WHERE comp_url='$prvs_comp_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of company (against previously known as company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_nm_exp)
                  VALUES('$prvs_comp_nm', CASE WHEN '$prvs_comp_alph'!='' THEN '$prvs_comp_alph' END, '$prvs_comp_sffx_num', '$prvs_comp_url', '1', '1')";
            if(!mysqli_query($link, $sql)) {$error='Error adding creative (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO comp_aka(comp_sbsq_id, comp_prvs_id)
                SELECT $comp_id, comp_id FROM comp WHERE comp_url='$prvs_comp_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding company-previously known as association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM comp_lctn WHERE compid='$comp_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting company-location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM comp_lctn_alt WHERE compid='$comp_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting company-location (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $comp_lctn_list))
      {
        $comp_lctn_nms=explode(',,', $comp_lctn_list);
        $n=0;
        foreach($comp_lctn_nms as $comp_lctn_nm)
        {
          $comp_lctn_nm=trim($comp_lctn_nm);

          if(preg_match('/\S+.*\|\|.*\S+/', $comp_lctn_nm))
          {
            list($comp_lctn_nm, $comp_lctn_alt_list)=explode('||', $comp_lctn_nm);
            $comp_lctn_nm=trim($comp_lctn_nm); $comp_lctn_alt_list=trim($comp_lctn_alt_list);
          }
          else {$comp_lctn_alt_list='';}

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $comp_lctn_nm))
          {
            list($comp_lctn_nm, $comp_lctn_sffx_num)=explode('--', $comp_lctn_nm);
            $comp_lctn_nm=trim($comp_lctn_nm); $comp_lctn_sffx_num=trim($comp_lctn_sffx_num);
            $comp_lctn_sffx_rmn=' ('.romannumeral($comp_lctn_sffx_num).')';
          }
          else
          {
            $comp_lctn_sffx_num='0';
            $comp_lctn_sffx_rmn='';
          }

          $comp_lctn_alph=alph($comp_lctn_nm);
          $comp_lctn_ordr=++$n;

          $sql="SELECT 1 FROM lctn WHERE lctn_url='$comp_lctn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of place of origin: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO lctn(lctn_nm, lctn_alph, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn)
                  VALUES('$comp_lctn_nm', CASE WHEN '$comp_lctn_alph'!='' THEN '$comp_lctn_alph' END, '$comp_lctn_sffx_num', '$comp_lctn_url', 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding (company) location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO comp_lctn(compid, comp_lctn_ordr, comp_lctnid)
                SELECT '$comp_id', '$comp_lctn_ordr', lctn_id FROM lctn WHERE lctn_url='$comp_lctn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding company-location association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

          if($comp_lctn_alt_list)
          {
            $comp_lctn_alts=explode('>>', $comp_lctn_alt_list);
            foreach($comp_lctn_alts as $comp_lctn_alt)
            {
              $comp_lctn_alt=trim($comp_lctn_alt);

              if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $comp_lctn_alt))
              {
                list($comp_lctn_alt, $comp_lctn_alt_sffx_num)=explode('--', $comp_lctn_alt);
                $comp_lctn_alt=trim($comp_lctn_alt); $comp_lctn_alt_sffx_num=trim($comp_lctn_alt_sffx_num);
                $comp_lctn_alt_sffx_rmn=' ('.romannumeral($comp_lctn_alt_sffx_num).')';
              }
              else {$comp_lctn_alt_sffx_num='0'; $comp_lctn_alt_sffx_rmn='';}

              $comp_lctn_alt_url=generateurl($comp_lctn_alt.$comp_lctn_alt_sffx_rmn);

              $sql= "INSERT INTO comp_lctn_alt(compid, comp_lctnid, comp_lctn_altid)
                    SELECT '$comp_id',
                    (SELECT lctn_id FROM lctn WHERE lctn_url='$comp_lctn_url'),
                    (SELECT lctn_id FROM lctn WHERE lctn_url='$comp_lctn_alt_url')";
              if(!mysqli_query($link, $sql)) {$error='Error adding company-location (alternate location) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }
          }
        }
      }

      $sql="DELETE FROM comptyp WHERE compid='$comp_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting company-type associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $comp_typ_list))
      {
        $comp_typ_nms=explode(',,', $comp_typ_list);
        $n=0;
        foreach($comp_typ_nms as $comp_typ_nm)
        {
          $comp_typ_nm=trim($comp_typ_nm);
          $comp_typ_url=generateurl($comp_typ_nm);
          $comp_typ_ordr=++$n;

          $sql="SELECT 1 FROM comp_typ WHERE comp_typ_url='$comp_typ_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of company type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO comp_typ(comp_typ_nm, comp_typ_url)
                  VALUES('$comp_typ_nm', '$comp_typ_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding company type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO comptyp(compid, comp_typ_ordr, comp_typid)
                SELECT '$comp_id', '$comp_typ_ordr', comp_typ_id FROM comp_typ WHERE comp_typ_url='$comp_typ_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding company-type association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM compprsn WHERE compid='$comp_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting company-member (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $comp_prsn_list))
      {
        $comp_ppl=explode(',,', $comp_prsn_list);
        $n=0;
        foreach($comp_ppl as $comp_prsn)
        {
          if(preg_match('/^\S+.*##\s*[1-9][0-9]{0,3}(\s*;;\s*[1-9][0-9]{0,3})?$/', $comp_prsn))
          {
            list($comp_prsn, $comp_prsn_yr)=explode('##', $comp_prsn);
            $comp_prsn=trim($comp_prsn); $comp_prsn_yr=trim($comp_prsn_yr);

            if(preg_match('/^[1-9][0-9]{0,3}\s*;;\s*[1-9][0-9]{0,3}$/', $comp_prsn_yr))
            {
              list($comp_prsn_yr_strt, $comp_prsn_yr_end)=explode(';;', $comp_prsn_yr);
              $comp_prsn_yr_strt=trim($comp_prsn_yr_strt); $comp_prsn_yr_end=trim($comp_prsn_yr_end);
            }
            else
            {$comp_prsn_yr_strt=$comp_prsn_yr; $comp_prsn_yr_end='0';}
          }
          else
          {$comp_prsn_yr_strt='0'; $comp_prsn_yr_end='0';}

          list($comp_prsn, $comp_prsn_rl)=explode('::', $comp_prsn);
          $comp_prsn=trim($comp_prsn); $comp_prsn_rl=trim($comp_prsn_rl);

          if(preg_match('/^\S+.*;;.*\S+$/', $comp_prsn_rl))
          {
            list($comp_prsn_rl, $comp_prsn_rl_nt)=explode(';;', $comp_prsn_rl);
            $comp_prsn_rl=trim($comp_prsn_rl); $comp_prsn_rl_nt=trim($comp_prsn_rl_nt);
          }
          else
          {$comp_prsn_rl_nt=NULL;}

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $comp_prsn))
          {
            list($comp_prsn, $comp_prsn_sffx_num)=explode('--', $comp_prsn);
            $comp_prsn=trim($comp_prsn); $comp_prsn_sffx_num=trim($comp_prsn_sffx_num);
            $comp_prsn_sffx_rmn=' ('.romannumeral($comp_prsn_sffx_num).')';
          }
          else
          {$comp_prsn_sffx_num='0'; $comp_prsn_sffx_rmn='';}

          list($comp_prsn_frst_nm, $comp_prsn_lst_nm)=explode(';;', $comp_prsn);
          $comp_prsn_frst_nm=trim($comp_prsn_frst_nm); $comp_prsn_lst_nm=trim($comp_prsn_lst_nm);

          if(preg_match('/\S+/', $comp_prsn_lst_nm))
          {$comp_prsn_lst_nm_dsply=' '.$comp_prsn_lst_nm;}
          else
          {$comp_prsn_lst_nm_dsply='';}

          $comp_prsn_fll_nm=$comp_prsn_frst_nm.$comp_prsn_lst_nm_dsply;
          $comp_prsn_url=generateurl($comp_prsn_fll_nm.$comp_prsn_sffx_rmn);
          $comp_prsn_ordr=++$n;

          $sql= "SELECT 1 FROM prsn WHERE prsn_url='$comp_prsn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of company member (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO prsn(prsn_url, prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, comp_bool)
                VALUES('$comp_prsn_url', '$comp_prsn_fll_nm', '$comp_prsn_frst_nm', '$comp_prsn_lst_nm', '$comp_prsn_sffx_num', '0')";
            if(!mysqli_query($link, $sql)) {$error='Error adding company member (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO compprsn(compid, compprsn_ordr, compprsn_rl, compprsn_rl_nt, compprsn_yr_strt, compprsn_yr_end, prsnid)
                SELECT '$comp_id', '$comp_prsn_ordr', '$comp_prsn_rl', '$comp_prsn_rl_nt', '$comp_prsn_yr_strt', '$comp_prsn_yr_end',
              prsn_id FROM prsn WHERE prsn_url='$comp_prsn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding company-member (person) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS COMPANY HAS BEEN EDITED:'.' '.html($comp_nm_session);
    header('Location: '.$comp_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $comp_id=cln($_POST['comp_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdwri WHERE wri_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-company (writer) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Writer (production)';}

    $sql="SELECT 1 FROM prdprdcr WHERE prdcr_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-company (producer) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Producer (production)';}

    $sql="SELECT 1 FROM prdmscn WHERE mscn_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-company (musician) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Musician (production)';}

    $sql="SELECT 1 FROM prdcrtv WHERE crtv_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-company (creative) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Creative (production)';}

    $sql="SELECT 1 FROM prdprdtm WHERE prdtm_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-company (prod team) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production team (production)';}

    $sql="SELECT 1 FROM prdrvw WHERE rvw_pub_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-company (publication) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Review publication (production)';}

    $sql="SELECT 1 FROM ptwri WHERE wri_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-company (writer) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Writer (playtext)';}

    $sql="SELECT 1 FROM crs WHERE crs_schlid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring course-company (school) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Course school';}

    $sql="SELECT 1 FROM crscdntr WHERE cdntr_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring course-company (course coordinator) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Course coordinator';}

    $sql="SELECT 1 FROM compprsn WHERE compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring company-member (company) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Company (member)';}

    $sql="SELECT 1 FROM prsnagnt WHERE agnt_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring client-company (agency) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Agency (client)';}

    $sql="SELECT 1 FROM ptlcnsr WHERE lcnsr_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-company (licensor) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Licensor (playtext)';}

    $sql="SELECT 1 FROM awrdnomppl WHERE nom_compid='$comp_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring award-company (nominee/winner) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Awards nomination/win';}

    if(count($assocs)>0)
    {$errors['comp_dlt']='**Company must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT comp_nm, comp_sffx_num FROM comp WHERE comp_id='$comp_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring company details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['comp_sffx_num']) {$comp_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';} else {$comp_sffx_rmn='';}
      $pagetab='Edit: '.html($row['comp_nm'].$comp_sffx_rmn);
      $pagetitle=html($row['comp_nm'].$comp_sffx_rmn);
      $comp_nm=$_POST['comp_nm'];
      $comp_sffx_num=$_POST['comp_sffx_num'];
      $comp_adrs_list=$_POST['comp_adrs_list'];
      $comp_reg_nm=$_POST['comp_reg_nm'];
      $comp_reg_adrs=$_POST['comp_reg_adrs'];
      $comp_est_dt=$_POST['comp_est_dt'];
      if($_POST['comp_est_dt_frmt']=='1') {$comp_est_dt_frmt='1';}
      if($_POST['comp_est_dt_frmt']=='2') {$comp_est_dt_frmt='2';}
      if($_POST['comp_est_dt_frmt']=='3') {$comp_est_dt_frmt='3';}
      if($_POST['comp_est_dt_frmt']=='4') {$comp_est_dt_frmt='4';}
      $comp_dslv_dt=$_POST['comp_dslv_dt'];
      if($_POST['comp_dslv_dt_frmt']=='1') {$comp_dslv_dt_frmt='1';}
      if($_POST['comp_dslv_dt_frmt']=='2') {$comp_dslv_dt_frmt='2';}
      if($_POST['comp_dslv_dt_frmt']=='3') {$comp_dslv_dt_frmt='3';}
      if($_POST['comp_dslv_dt_frmt']=='4') {$comp_dslv_dt_frmt='4';}
      if(isset($_POST['comp_dslv'])) {$comp_dslv='1';} else {$comp_dslv='0';}
      $comp_nm_frm_dt=$_POST['comp_nm_frm_dt'];
      if($_POST['comp_nm_frm_dt_frmt']=='1') {$comp_nm_frm_dt_frmt='1';}
      if($_POST['comp_nm_frm_dt_frmt']=='2') {$comp_nm_frm_dt_frmt='2';}
      if($_POST['comp_nm_frm_dt_frmt']=='3') {$comp_nm_frm_dt_frmt='3';}
      if($_POST['comp_nm_frm_dt_frmt']=='4') {$comp_nm_frm_dt_frmt='4';}
      $comp_nm_exp_dt=$_POST['comp_nm_exp_dt'];
      if($_POST['comp_nm_exp_dt_frmt']=='1') {$comp_nm_exp_dt_frmt='1';}
      if($_POST['comp_nm_exp_dt_frmt']=='2') {$comp_nm_exp_dt_frmt='2';}
      if($_POST['comp_nm_exp_dt_frmt']=='3') {$comp_nm_exp_dt_frmt='3';}
      if($_POST['comp_nm_exp_dt_frmt']=='4') {$comp_nm_exp_dt_frmt='4';}
      if(isset($_POST['comp_nm_exp'])) {$comp_nm_exp='1';} else {$comp_nm_exp='0';}
      $sbsq_comp_list=$_POST['sbsq_comp_list'];
      $prvs_comp_list=$_POST['prvs_comp_list'];
      $comp_lctn_list=$_POST['comp_lctn_list'];
      $comp_typ_list=$_POST['comp_typ_list'];
      $comp_prsn_list=$_POST['comp_prsn_list'];
      $textarea=$_POST['textarea'];
      $comp_id=html($comp_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT comp_nm, comp_sffx_num
            FROM comp
            WHERE comp_id='$comp_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring company details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['comp_sffx_num']) {$comp_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';} else {$comp_sffx_rmn='';}
      $pagetab='Delete confirmation: '.html($row['comp_nm'].$comp_sffx_rmn);
      $pagetitle=html($row['comp_nm'].$comp_sffx_rmn);
      $comp_id=html($comp_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $comp_id=cln($_POST['comp_id']);
    $sql= "SELECT comp_nm, comp_sffx_num
          FROM comp
          WHERE comp_id='$comp_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['comp_sffx_num']) {$comp_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';} else {$comp_sffx_rmn='';}
    $comp_nm_session=$row['comp_nm'].$comp_sffx_rmn;

    $sql="DELETE FROM prdwri WHERE wri_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-company (writer) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdcr WHERE prdcr_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-company (producer) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdmscn WHERE mscn_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-company (musician) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdcrtv WHERE crtv_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-company (creative) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdtm WHERE prdtm_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-company (prod team) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdrvw WHERE rvw_pub_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-company (publication) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptwri WHERE wri_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-company (writer) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="UPDATE crs SET crs_schlid=NULL WHERE crs_schlid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-company (school) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM comp_aka WHERE comp_prvs_id='$comp_id' OR comp_sbsq_id='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company-previously/subsequently known as associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM comp_lctn WHERE compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company-location associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM comp_lctn_alt WHERE compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company-location (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM comptyp WHERE compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company-type associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM compprsn WHERE compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company-member (company) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prsnagnt WHERE agnt_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting client-company (agency) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptlcnsr WHERE lcnsr_compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-company (licensor) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thtrcomp WHERE compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting thetare-company (owned by) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql ="DELETE FROM comp WHERE comp_id='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS COMPANY HAS BEEN DELETED FROM THE DATABASE:'.' '.html($comp_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $comp_id=cln($_POST['comp_id']);
    $sql= "SELECT comp_url
          FROM comp
          WHERE comp_id='$comp_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['comp_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $comp_url=cln($_GET['comp_url']);

  $sql= "SELECT comp_id
        FROM comp
        WHERE comp_url='$comp_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $comp_id=$row['comp_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT comp_nm, comp_sffx_num, comp_url, comp_reg_nm, comp_reg_adrs,
          CASE WHEN comp_est_dt_frmt=1 THEN DATE_FORMAT(comp_est_dt, '%d %b %Y') WHEN comp_est_dt_frmt=2 THEN DATE_FORMAT(comp_est_dt, '%b %Y')
          WHEN comp_est_dt_frmt=3 THEN DATE_FORMAT(comp_est_dt, '%Y') ELSE NULL END AS comp_est_dt,
          CASE WHEN comp_dslv_dt_frmt=1 THEN DATE_FORMAT(comp_dslv_dt, '%d %b %Y') WHEN comp_dslv_dt_frmt=2 THEN DATE_FORMAT(comp_dslv_dt, '%b %Y')
          WHEN comp_dslv_dt_frmt=3 THEN DATE_FORMAT(comp_dslv_dt, '%Y') ELSE NULL END AS comp_dslv_dt
          FROM comp
          WHERE comp_id='$comp_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['comp_sffx_num']) {$comp_sffx_rmn=' ('.romannumeral($row['comp_sffx_num']).')';} else {$comp_sffx_rmn='';}
    $pagetab=html($row['comp_nm'].$comp_sffx_rmn);
    $pagetitle=html($row['comp_nm']);
    $comp_nm=html($row['comp_nm']);
    $comp_url=html($row['comp_url']);
    $comp_reg_nm=html($row['comp_reg_nm']);
    $comp_reg_adrs=preg_replace('/,,/', ', ', html($row['comp_reg_adrs']));
    $comp_est_dt=html($row['comp_est_dt']);
    $comp_dslv_dt=html($row['comp_dslv_dt']);

    $sql="SELECT comp_adrs, comp_adrs_ttl FROM compadrs WHERE compid='$comp_id' ORDER BY comp_adrs_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company address data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_adrs_ttl']) {$comp_adrs_ttl='<u>'.html($row['comp_adrs_ttl']).'</u></br>';} else {$comp_adrs_ttl='';}
      $comp_adrs=preg_replace('/,,/', ', ', html($row['comp_adrs']));
      $adrss[]=$comp_adrs_ttl.$comp_adrs;
    }

    $sql= "SELECT comp_nm, comp_url,
          CASE WHEN comp_nm_frm_dt_frmt=1 THEN DATE_FORMAT(comp_nm_frm_dt, '%d %b %Y') WHEN comp_nm_frm_dt_frmt=2 THEN DATE_FORMAT(comp_nm_frm_dt, '%b %Y')
          WHEN comp_nm_frm_dt_frmt=3 THEN DATE_FORMAT(comp_nm_frm_dt, '%Y') ELSE NULL END AS comp_nm_frm_dt_frmt,
          CASE WHEN comp_nm_exp_dt_frmt=1 THEN DATE_FORMAT(comp_nm_exp_dt, '%d %b %Y') WHEN comp_nm_exp_dt_frmt=2 THEN DATE_FORMAT(comp_nm_exp_dt, '%b %Y')
          WHEN comp_nm_exp_dt_frmt=3 THEN DATE_FORMAT(comp_nm_exp_dt, '%Y') ELSE NULL END AS comp_nm_exp_dt_frmt
          FROM comp_aka
          INNER JOIN comp ON comp_sbsq_id=comp_id
          WHERE comp_prvs_id='$comp_id'
          ORDER BY comp_nm_frm_dt DESC, comp_nm_exp_dt DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring subsequently known as data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_nm_frm_dt_frmt'] || $row['comp_nm_exp_dt_frmt'])
      {
        if($row['comp_nm_frm_dt_frmt']) {$sbsq_comp_nm_frm_dt='from '.html($row['comp_nm_frm_dt_frmt']);} else {$sbsq_comp_nm_frm_dt='';}
        if($row['comp_nm_exp_dt_frmt']) {$sbsq_comp_nm_exp_dt='until '.html($row['comp_nm_exp_dt_frmt']);} else {$sbsq_comp_nm_exp_dt='';}
        if($row['comp_nm_frm_dt_frmt'] && $row['comp_nm_exp_dt_frmt']) {$sbsq_spc=' ';} else {$sbsq_spc='';}
        $sbsq_comp_nm_dt=' <em>('.$sbsq_comp_nm_frm_dt.$sbsq_spc.$sbsq_comp_nm_exp_dt.')</em>';
      }
      else {$sbsq_comp_nm_dt='';}
      $sbsqs[]='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$sbsq_comp_nm_dt;
    }

    $sql= "SELECT comp_nm, comp_url,
          CASE WHEN comp_nm_frm_dt_frmt=1 THEN DATE_FORMAT(comp_nm_frm_dt, '%d %b %Y') WHEN comp_nm_frm_dt_frmt=2 THEN DATE_FORMAT(comp_nm_frm_dt, '%b %Y')
          WHEN comp_nm_frm_dt_frmt=3 THEN DATE_FORMAT(comp_nm_frm_dt, '%Y') ELSE NULL END AS comp_nm_frm_dt_frmt,
          CASE WHEN comp_nm_exp_dt_frmt=1 THEN DATE_FORMAT(comp_nm_exp_dt, '%d %b %Y') WHEN comp_nm_exp_dt_frmt=2 THEN DATE_FORMAT(comp_nm_exp_dt, '%b %Y')
          WHEN comp_nm_exp_dt_frmt=3 THEN DATE_FORMAT(comp_nm_exp_dt, '%Y') ELSE NULL END AS comp_nm_exp_dt_frmt
          FROM comp_aka
          INNER JOIN comp ON comp_prvs_id=comp_id
          WHERE comp_sbsq_id='$comp_id'
          ORDER BY comp_nm_frm_dt DESC, comp_nm_exp_dt DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring previously known as data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_nm_frm_dt_frmt'] || $row['comp_nm_exp_dt_frmt'])
      {
        if($row['comp_nm_frm_dt_frmt']) {$prvs_comp_nm_frm_dt='from '.html($row['comp_nm_frm_dt_frmt']);} else {$prvs_comp_nm_frm_dt='';}
        if($row['comp_nm_exp_dt_frmt']) {$prvs_comp_nm_exp_dt='until '.html($row['comp_nm_exp_dt_frmt']);} else {$prvs_comp_nm_exp_dt='';}
        if($row['comp_nm_frm_dt_frmt'] && $row['comp_nm_exp_dt_frmt']) {$prvs_spc=' ';} else {$prvs_spc='';}
        $prvs_comp_nm_dt=' <em>('.$prvs_comp_nm_frm_dt.$prvs_spc.$prvs_comp_nm_exp_dt.')</em>';
      }
      else {$prvs_comp_nm_dt='';}
      $prvss[]='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$prvs_comp_nm_dt;
    }

    if(!empty($sbsqs) || !empty($prvss))
    {
      $sql= "SELECT CASE WHEN comp_nm_frm_dt_frmt=1 THEN DATE_FORMAT(comp_nm_frm_dt, '%d %b %Y') WHEN comp_nm_frm_dt_frmt=2 THEN DATE_FORMAT(comp_nm_frm_dt, '%b %Y')
            WHEN comp_nm_frm_dt_frmt=3 THEN DATE_FORMAT(comp_nm_frm_dt, '%Y') ELSE NULL END AS comp_nm_frm_dt,
            CASE WHEN comp_nm_exp_dt_frmt=1 THEN DATE_FORMAT(comp_nm_exp_dt, '%d %b %Y') WHEN comp_nm_exp_dt_frmt=2 THEN DATE_FORMAT(comp_nm_exp_dt, '%b %Y')
            WHEN comp_nm_exp_dt_frmt=3 THEN DATE_FORMAT(comp_nm_exp_dt, '%Y') ELSE NULL END AS comp_nm_exp_dt
            FROM comp WHERE comp_id='$comp_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring company (name from/until) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['comp_nm_frm_dt'] || $row['comp_nm_exp_dt'])
      {
        if($row['comp_nm_frm_dt']) {$comp_nm_frm_dt='from '.html($row['comp_nm_frm_dt']);} else {$comp_nm_frm_dt='';}
        if($row['comp_nm_exp_dt']) {$comp_nm_exp_dt='until '.html($row['comp_nm_exp_dt']);} else {$comp_nm_exp_dt='';}
        if($row['comp_nm_frm_dt'] && $row['comp_nm_exp_dt']) {$spc=' ';} else {$spc='';}
        $comp_nm_dt=' <em>('.$comp_nm_frm_dt.$spc.$comp_nm_exp_dt.')</em>';
      }
      else {$comp_nm_dt='';}
    }

    $comp_crdt_ids=array();
    $comp_crdt_ids[]=$comp_id;
    $sql= "SELECT comp_id FROM comp_aka INNER JOIN comp ON comp_sbsq_id=comp_id WHERE comp_prvs_id='$comp_id'
          UNION
          SELECT comp_id FROM comp_aka INNER JOIN comp ON comp_prvs_id=comp_id WHERE comp_sbsq_id='$comp_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring previously & subsequently known as company_ids: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$comp_crdt_ids[]=$row['comp_id'];}

    $wri_id=implode($comp_crdt_ids, ' OR wri_compid=');
    $pw_wri_id=implode($comp_crdt_ids, ' OR pw1.wri_compid=');
    $prdcr_id=implode($comp_crdt_ids, ' OR prdcr_compid=');
    $pp_prdcr_id=implode($comp_crdt_ids, ' OR pp1.prdcr_compid=');
    $mscn_id=implode($comp_crdt_ids, ' OR mscn_compid=');
    $pm_mscn_id=implode($comp_crdt_ids, ' OR pm1.mscn_compid=');
    $crtv_id=implode($comp_crdt_ids, ' OR crtv_compid=');
    $pc_crtv_id=implode($comp_crdt_ids, ' OR pc1.crtv_compid=');
    $prdtm_id=implode($comp_crdt_ids, ' OR prdtm_compid=');
    $ppt_prdtm_id=implode($comp_crdt_ids, ' OR ppt1.prdtm_compid=');
    $crs_schl_id=implode($comp_crdt_ids, ' OR crs_schlid=');
    $crdt_id=implode($comp_crdt_ids, ' OR comp_id=');
    $rvw_pub_id=implode($comp_crdt_ids, ' OR rvw_pub_compid=');
    $cdntr_id=implode($comp_crdt_ids, ' OR cdntr_compid=');
    $cc_cdntr_id=implode($comp_crdt_ids, ' OR cc1.cdntr_compid=');
    $cntr_id=implode($comp_crdt_ids, ' OR cntr_compid=');
    $pc_cntr_id=implode($comp_crdt_ids, ' OR pc1.cntr_compid=');
    $lcnsr_id=implode($comp_crdt_ids, ' OR lcnsr_compid=');
    $awrd_id=implode($comp_crdt_ids, ' OR nom_compid=');
    $anp1_awrd_id=implode($comp_crdt_ids, ' OR anp1.nom_compid=');
    $anp_awrd_id=implode($comp_crdt_ids, ' OR anp.nom_compid=');

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdwri
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=0
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdwri
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=0 AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (writer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $wri_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($wri_prd_ids))
      {
        foreach($wri_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdwri WHERE prdid='$prd_id' AND (wri_compid=$wri_id) AND wri_prsnid=0 LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdwri
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment data for (writer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $wri_sg_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
      }

      if(!empty($wri_sg_prd_ids))
      {
        foreach($wri_sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }
      $wri_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdwri
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=1
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdwri
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=1 AND coll_ov IS NULL
          GROUP BY prd_id
          UNION
          SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdprdcr
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=1
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdprdcr
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=1 AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $grntr_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'prdcr_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($grntr_prd_ids))
      {
        foreach($grntr_prd_ids as $prd_id)
        {
          $sql= "SELECT 1 FROM prdwri WHERE prdid='$prd_id' AND (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=1 LIMIT 1
                UNION
                SELECT 1 FROM prdprdcr WHERE prdid='$prd_id' AND (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=1 LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as grantor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, prdcr_rl_id, prdcr_rl
            FROM prdprdcr pp
            INNER JOIN prdprdcrrl ppr ON pp.prdid=ppr.prdid AND prdcr_rlid=prdcr_rl_id INNER JOIN prd ON pp.prdid=prd_id
            WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=1 AND coll_ov IS NULL
            GROUP BY prd_id, prdcr_rl_id
            ORDER BY prdcr_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring producer role data for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {$prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rl_id']]=array('prdcr_rl'=>html($row['prdcr_rl']), 'prdcrs'=>array());}

        $sql= "SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, comp_id, comp_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN comp ON pp2.prdcr_compid=comp_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=1 AND pp2.prdcr_prsnid=0 AND coll_ov IS NULL
              GROUP BY prd_id, prdcr_rlid, comp_id
              UNION
              SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, prsn_id, prsn_fll_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=1 AND pp2.prdcr_compid=0 AND coll_ov IS NULL
              GROUP BY prd_id, prdcr_rlid, prsn_id
              ORDER BY prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credited producers for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['prdcr_sb_rl']) {$prdcr_sb_rl=html($row['prdcr_sb_rl']).' ';} else {$prdcr_sb_rl='';}
          $prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'prdcr_sb_rl'=>$prdcr_sb_rl, 'prdcrcomp_ppl_crdt'=>array());
        }

        $sql= "SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_compid, prsn_fll_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=1 AND pp2.prdcr_crdt=1 AND coll_ov IS NULL
              GROUP BY prd_id, prdcr_rlid, prdcr_compid, prsn_id
              ORDER BY pp2.prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credited producer (company people) for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['prdcrcomp_ppl_crdt'][]=array('prsn_nm'=>html($row['prsn_fll_nm']));}
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM prdwri
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            UNION
            SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM prdprdcr
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment data for (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $grntr_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'prdcr_rls'=>array());
        }

        if(!empty($grntr_sg_prd_ids))
        {
          foreach($grntr_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, prdcr_rl_id, prdcr_rl
              FROM prdprdcr pp
              INNER JOIN prdprdcrrl ppr ON pp.prdid=ppr.prdid AND prdcr_rlid=prdcr_rl_id INNER JOIN prd ON pp.prdid=prd_id
              WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=1 AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdcr_rl_id
              ORDER BY prdcr_rl_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring producer role data for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          while($row=mysqli_fetch_array($result))
          {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdcr_rls'][$row['prdcr_rl_id']]=array('prdcr_rl'=>html($row['prdcr_rl']), 'prdcrs'=>array());}

          $sql= "SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, comp_id, comp_nm
                FROM prdprdcr pp1
                INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
                INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
                INNER JOIN comp ON pp2.prdcr_compid=comp_id INNER JOIN prd ON pp1.prdid=prd_id
                WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=1 AND pp2.prdcr_prsnid=0 AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prdid, prdcr_rlid, comp_id
                UNION
                SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, prsn_id, prsn_fll_nm
                FROM prdprdcr pp1
                INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
                INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
                INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
                WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=1 AND pp2.prdcr_compid=0 AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prd_id, prdcr_rlid, prsn_id
                ORDER BY prdcr_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring credited producers for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['prdcr_sb_rl']) {$prdcr_sb_rl=html($row['prdcr_sb_rl']).' ';} else {$prdcr_sb_rl='';}
            $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'prdcr_sb_rl'=>$prdcr_sb_rl, 'prdcrcomp_ppl_crdt'=>array());
          }

          $sql= "SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_compid, prsn_fll_nm
                FROM prdprdcr pp1
                INNER JOIN prdprdcrrl ppr ON pp1.prdid= ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
                INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
                INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
                WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=1 AND pp2.prdcr_crdt=1 AND coll_ov IS NOT NULL
                GROUP BY prd_id, prdcr_rlid, prdcr_compid, prsn_id
                ORDER BY pp2.prdcr_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring credited producers (company people) for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['prdcrcomp_ppl_crdt'][]=array('prsn_nm'=>html($row['prsn_fll_nm']));}
        }
      }
      $grntr_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply,
        p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
        FROM prdprdcr
        INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
        WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=0
        GROUP BY prd_id
        UNION
        SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply,
        prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
        FROM prdprdcr
        INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
        WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=0 AND coll_ov IS NULL
        GROUP BY prd_id
        ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $prdcr_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'prdcr_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prdcr_prd_ids))
      {
        foreach($prdcr_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdprdcr WHERE prdid='$prd_id' AND prdcr_compid='$comp_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as producer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, prdcr_rl_id, prdcr_rl
            FROM prdprdcr pp
            INNER JOIN prdprdcrrl ppr ON pp.prdid=ppr.prdid AND prdcr_rlid=prdcr_rl_id INNER JOIN prd ON pp.prdid=prd_id
            WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=0 AND coll_ov IS NULL
            GROUP BY prd_id, prdcr_rl_id
            ORDER BY prdcr_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring producer role data for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rl_id']]=array('prdcr_rl'=>html($row['prdcr_rl']), 'prdcrs'=>array());}

      $sql= "SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, comp_id, comp_nm
            FROM prdprdcr pp1
            INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
            INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
            INNER JOIN comp ON pp2.prdcr_compid=comp_id INNER JOIN prd ON pp1.prdid=prd_id
            WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=0 AND pp2.prdcr_prsnid=0 AND coll_ov IS NULL
            GROUP BY prd_id, prdcr_rlid, comp_id
            UNION
            SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, prsn_id, prsn_fll_nm
            FROM prdprdcr pp1
            INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
            INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
            INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
            WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=0 AND pp2.prdcr_compid=0 AND coll_ov IS NULL
            GROUP BY prd_id, prdcr_rlid, prsn_id
            ORDER BY prdcr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring credited producers for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['prdcr_sb_rl']) {$prdcr_sb_rl=html($row['prdcr_sb_rl']).' ';} else {$prdcr_sb_rl='';}
        $prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'prdcr_sb_rl'=>$prdcr_sb_rl, 'prdcrcomp_ppl_crdt'=>array());
      }

      $sql= "SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_compid, prsn_fll_nm
            FROM prdprdcr pp1
            INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
            INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
            INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
            WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=0 AND pp2.prdcr_crdt=1 AND coll_ov IS NULL
            GROUP BY prd_id, prdcr_rlid, prdcr_compid, prsn_id
            ORDER BY pp2.prdcr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring credited producer (company people) for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['prdcrcomp_ppl_crdt'][]=array('prsn_nm'=>html($row['prsn_fll_nm']));}

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdprdcr
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment data for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $prdcr_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'prdcr_rls'=>array());
        }

        if(!empty($prdcr_sg_prd_ids))
        {
          foreach($prdcr_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, prdcr_rl_id, prdcr_rl
              FROM prdprdcr pp
              INNER JOIN prdprdcrrl ppr ON pp.prdid=ppr.prdid AND prdcr_rlid=prdcr_rl_id INNER JOIN prd ON pp.prdid=prd_id
              WHERE (prdcr_compid=$prdcr_id) AND prdcr_prsnid=0 AND grntr=0 AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdcr_rl_id
              ORDER BY prdcr_rl_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring producer role data for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdcr_rls'][$row['prdcr_rl_id']]=array('prdcr_rl'=>html($row['prdcr_rl']), 'prdcrs'=>array());}

        $sql= "SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, comp_id, comp_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN comp ON pp2.prdcr_compid=comp_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=0 AND pp2.prdcr_prsnid=0 AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdcr_rlid, comp_id
              UNION
              SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, prsn_id, prsn_fll_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=0 AND pp2.prdcr_compid=0 AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdcr_rlid, prsn_id
              ORDER BY prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credited producers for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['prdcr_sb_rl']) {$prdcr_sb_rl=html($row['prdcr_sb_rl']).' ';} else {$prdcr_sb_rl='';}
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'prdcr_sb_rl'=>$prdcr_sb_rl, 'prdcrcomp_ppl_crdt'=>array());
        }

        $sql= "SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_compid, prsn_fll_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE (pp1.prdcr_compid=$pp_prdcr_id) AND pp1.prdcr_prsnid=0 AND pp1.grntr=0 AND pp2.prdcr_crdt=1 AND coll_ov IS NOT NULL
              GROUP BY prd_id, prdcr_rlid, prdcr_compid, prsn_id
              ORDER BY pp2.prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credited producers (company people) for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['prdcrcomp_ppl_crdt'][]=array('prsn_nm'=>html($row['prsn_fll_nm']));}
      }
      $prdcr_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          NULL AS mscn_sb_rl, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt, NULL AS mscn_rl
          FROM prdmscn
          LEFT OUTER JOIN comp ON mscn_compid=comp_id INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (mscn_compid=$mscn_id) AND mscn_prsnid=0
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          mscn_sb_rl, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt, mscn_rl
          FROM prdmscn pm
          LEFT OUTER JOIN comp ON mscn_compid=comp_id INNER JOIN prdmscnrl pmr ON pm.prdid=pmr.prdid AND mscn_rlid=mscn_rl_id
          INNER JOIN prd p1 ON pm.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE (mscn_compid=$mscn_id) AND mscn_prsnid=0 AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (musician) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        if($row['mscn_sb_rl']) {$mscn_rl=$row['mscn_sb_rl'];} else {$mscn_rl=$row['mscn_rl'];}
        $mscn_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'mscn_rl'=>html($mscn_rl), 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'co_ppl'=>array(), 'sg_prds'=>array());
      }

      if(!empty($mscn_prd_ids))
      {
        foreach($mscn_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdmscn WHERE prdid='$prd_id' AND mscn_compid='$comp_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as musician): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, pm2.mscn_ordr, comp_nm
            FROM prdmscn pm1
            INNER JOIN prdmscnrl pmr ON pm1.prdid=pmr.prdid AND pm1.mscn_rlid=mscn_rl_id
            INNER JOIN prdmscn pm2 ON pmr.prdid=pm2.prdid AND mscn_rl_id=pm2.mscn_rlid
            INNER JOIN comp ON pm2.mscn_compid=comp_id INNER JOIN prd ON pm1.prdid=prd_id
            WHERE (pm1.mscn_compid=$pm_mscn_id) AND pm1.mscn_prsnid=0 AND pm2.mscn_prsnid=0 AND pm1.mscn_compid!=pm2.mscn_compid AND coll_ov IS NULL
            GROUP BY prd_id, comp_id
            UNION
            SELECT prd_id, pm2.mscn_ordr, prsn_fll_nm
            FROM prdmscn pm1
            INNER JOIN prdmscnrl pmr ON pm1.prdid=pmr.prdid AND pm1.mscn_rlid=mscn_rl_id
            INNER JOIN prdmscn pm2 ON pmr.prdid=pm2.prdid AND mscn_rl_id=pm2.mscn_rlid
            INNER JOIN prsn ON pm2.mscn_prsnid=prsn_id INNER JOIN prd ON pm1.prdid=prd_id
            WHERE (pm1.mscn_compid=$pm_mscn_id) AND pm1.mscn_prsnid=0 AND pm2.mscn_compid=0 AND pm1.mscn_compid!=pm2.mscn_compid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY mscn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-credited musician (company/people) data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['co_ppl'][]=array('co_prsn'=>html($row['comp_nm']));}

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, mscn_sb_rl, mscn_rl
            FROM prdmscn pm
            LEFT OUTER JOIN comp ON mscn_compid=comp_id INNER JOIN prdmscnrl pmr ON pm.prdid=pmr.prdid AND mscn_rlid=mscn_rl_id
            INNER JOIN prd ON pm.prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE (mscn_compid=$mscn_id) AND mscn_prsnid=0 AND coll_ov IS NOT NULL
            GROUP BY prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (musician) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          if($row['mscn_sb_rl']) {$mscn_rl=$row['mscn_sb_rl'];} else {$mscn_rl=$row['mscn_rl'];}
          $mscn_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'mscn_rl'=>html($mscn_rl), 'wri_rls'=>array(), 'co_ppl'=>array());
        }

        if(!empty($mscn_sg_prd_ids))
        {
          foreach($mscn_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, pm2.mscn_ordr, comp_id, comp_nm
              FROM prdmscn pm1
              INNER JOIN prdmscnrl pmr ON pm1.prdid=pmr.prdid AND pm1.mscn_rlid=mscn_rl_id
              INNER JOIN prdmscn pm2 ON pmr.prdid=pm2.prdid AND mscn_rl_id=pm2.mscn_rlid
              INNER JOIN comp ON pm2.mscn_compid=comp_id INNER JOIN prd ON pm1.prdid=prd_id
              WHERE (pm1.mscn_compid=$pm_mscn_id) AND pm1.mscn_prsnid=0 AND pm2.mscn_prsnid=0 AND pm1.mscn_compid!=pm2.mscn_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prdid, comp_id
              UNION
              SELECT coll_ov, prd_id, pm2.mscn_ordr, prsn_id, prsn_fll_nm
              FROM prdmscn pm1
              INNER JOIN prdmscnrl pmr ON pm1.prdid=pmr.prdid AND pm1.mscn_rlid=mscn_rl_id
              INNER JOIN prdmscn pm2 ON pmr.prdid=pm2.prdid AND mscn_rl_id=pm2.mscn_rlid
              INNER JOIN prsn ON pm2.mscn_prsnid=prsn_id INNER JOIN prd ON pm1.prdid=prd_id
              WHERE (pm1.mscn_compid=$pm_mscn_id) AND pm1.mscn_prsnid=0 AND pm2.mscn_compid=0 AND pm1.mscn_compid!=pm2.mscn_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prsn_id
              ORDER BY mscn_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring awards co-credited musician (company/people) data for production segments: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['co_ppl'][$row['comp_id']]=array('co_prsn'=>html($row['comp_nm']));}
      }
      $mscn_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph,
          (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdcrtv
          LEFT OUTER JOIN comp ON crtv_compid=comp_id INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (crtv_compid=$crtv_id) AND crtv_prsnid=0
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph,
          (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdcrtv
          LEFT OUTER JOIN comp ON crtv_compid=comp_id INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE (crtv_compid=$crtv_id) AND crtv_prsnid=0 AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (creative) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $crtv_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'crtv_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($crtv_prd_ids))
      {
        foreach($crtv_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdcrtv WHERE prdid='$prd_id' AND crtv_compid='$comp_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as creative): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, crtv_rl_id, crtv_sb_rl, crtv_rl
            FROM prdcrtv pc
            INNER JOIN prdcrtvrl pcr ON pc.prdid=pcr.prdid AND crtv_rlid=crtv_rl_id INNER JOIN prd ON pc.prdid=prd_id
            WHERE (crtv_compid=$crtv_id) AND crtv_prsnid=0 AND coll_ov IS NULL
            GROUP BY prd_id, crtv_rl_id
            ORDER BY crtv_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring creative team roles for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['crtv_sb_rl']) {$crtv_rl=$row['crtv_sb_rl'];} else {$crtv_rl=$row['crtv_rl'];}
        $prds[$row['prd_id']]['crtv_rls'][$row['crtv_rl_id']]=array('crtv_rl'=>html($crtv_rl), 'co_comp_ppl'=>array(), 'co_ppl'=>array());
      }

      $sql= "SELECT prd_id, pc1.crtv_rlid, prsn_fll_nm
            FROM prdcrtv pc1
            INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
            INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
            INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
            WHERE (pc1.crtv_compid=$pc_crtv_id) AND pc1.crtv_prsnid=0
            AND pc2.crtv_compid!=0 AND pc1.crtv_compid=pc2.crtv_compid AND coll_ov IS NULL
            GROUP BY prd_id, pc1.crtv_compid, prsn_id
            ORDER BY pc2.crtv_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-credited creative (company people - same company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_comp_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT prd_id, pc1.crtv_rlid, pc2.crtv_ordr, comp_id, comp_nm
            FROM prdcrtv pc1
            INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid  AND pc1.crtv_rlid=crtv_rl_id
            INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
            INNER JOIN comp ON pc2.crtv_compid=comp_id INNER JOIN prd ON pc1.prdid=prd_id
            WHERE (pc1.crtv_compid=$pc_crtv_id) AND pc1.crtv_prsnid=0
            AND pc2.crtv_prsnid=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NULL
            GROUP BY prd_id, comp_id
            UNION
            SELECT prd_id, pc1.crtv_rlid, pc2.crtv_ordr, prsn_id, prsn_fll_nm
            FROM prdcrtv pc1
            INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
            INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
            INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
            WHERE (pc1.crtv_compid=$pc_crtv_id) AND pc1.crtv_prsnid=0
            AND pc2.crtv_compid=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY crtv_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-credited creative (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_ppl'][$row['comp_id']]=array('co_prsn'=>html($row['comp_nm']), 'comp_ppl'=>array());}

      $sql= "SELECT prd_id, pc1.crtv_rlid, pc2.crtv_compid, prsn_fll_nm
            FROM prdcrtv pc1
            INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
            INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
            INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
            WHERE (pc1.crtv_compid=$pc_crtv_id) AND pc1.crtv_prsnid=0
            AND pc2.crtv_compid!=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NULL
            GROUP BY prd_id, crtv_compid, prsn_id
            ORDER BY pc2.crtv_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-credited creative (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_ppl'][$row['crtv_compid']]['comp_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdcrtv
            LEFT OUTER JOIN comp ON crtv_compid=comp_id INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE (crtv_compid=$crtv_id) AND crtv_prsnid=0 AND coll_ov IS NOT NULL
            GROUP BY prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (creative) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $crtv_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'crtv_rls'=>array());
        }

        if(!empty($crtv_sg_prd_ids))
        {
          foreach($crtv_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, crtv_rl_id, crtv_sb_rl, crtv_rl
              FROM prdcrtv pc
              INNER JOIN prdcrtvrl pcr ON pc.prdid=pcr.prdid AND crtv_rlid=crtv_rl_id INNER JOIN prd ON pc.prdid=prd_id
              WHERE (crtv_compid=$crtv_id) AND crtv_prsnid=0 AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, crtv_rl_id
              ORDER BY crtv_rl_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring creative team roles for production segments: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['crtv_sb_rl']) {$crtv_rl=$row['crtv_sb_rl'];} else {$crtv_rl=$row['crtv_rl'];}
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['crtv_rls'][$row['crtv_rl_id']]=array('crtv_rl'=>html($crtv_rl));
        }

        $sql= "SELECT coll_ov, prd_id, pc1.crtv_rlid, prsn_fll_nm
              FROM prdcrtv pc1
              INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
              INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
              INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
              WHERE (pc1.crtv_compid=$pc_crtv_id) AND pc1.crtv_prsnid=0 AND pc2.crtv_compid!=0 AND pc1.crtv_compid=pc2.crtv_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, pc1.crtv_compid, prsn_id
              ORDER BY pc2.crtv_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring awards co-credited creative (company people - same company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_comp_ppl'][]=html($row['prsn_fll_nm']);}

        $sql= "SELECT coll_ov, prd_id, pc1.crtv_rlid, pc2.crtv_ordr, comp_id, comp_nm
              FROM prdcrtv pc1
              INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
              INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
              INNER JOIN comp ON pc2.crtv_compid=comp_id INNER JOIN prd ON pc1.prdid=prd_id
              WHERE (pc1.crtv_compid=$pc_crtv_id) AND pc1.crtv_prsnid=0 AND pc2.crtv_prsnid=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, comp_id
              UNION
              SELECT coll_ov, prd_id, pc1.crtv_rlid, pc2.crtv_ordr, prsn_id, prsn_fll_nm
              FROM prdcrtv pc1
              INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
              INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
              INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
              WHERE (pc1.crtv_compid=$pc_crtv_id) AND pc1.crtv_prsnid=0 AND pc2.crtv_compid=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prsn_id
              ORDER BY crtv_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring awards co-credited creative (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_ppl'][$row['comp_id']]=array('co_prsn'=>html($row['comp_nm']), 'comp_ppl'=>array());}

        $sql= "SELECT coll_ov, prd_id, pc1.crtv_rlid, pc2.crtv_compid, prsn_fll_nm
              FROM prdcrtv pc1
              INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
              INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
              INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
              WHERE (pc1.crtv_compid=$pc_crtv_id) AND pc1.crtv_prsnid=0 AND pc2.crtv_compid!=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, crtv_compid, prsn_id
              ORDER BY pc2.crtv_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring awards co-credited creative (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_ppl'][$row['crtv_compid']]['comp_ppl'][]=html($row['prsn_fll_nm']);}
      }
      $crtv_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph,
          (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdprdtm
          LEFT OUTER JOIN comp ON prdtm_compid=comp_id INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (prdtm_compid=$prdtm_id) AND prdtm_prsnid=0
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph,
          (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdprdtm
          LEFT OUTER JOIN comp ON prdtm_compid=comp_id INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE (prdtm_compid=$prdtm_id) AND prdtm_prsnid=0 AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (production team) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $prdtm_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'prdtm_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prdtm_prd_ids))
      {
        foreach($prdtm_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdprdtm WHERE prdid='$prd_id' AND prdtm_compid='$comp_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as production team): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, prdtm_rl_id, prdtm_sb_rl, prdtm_rl
            FROM prdprdtm ppt
            INNER JOIN prdprdtmrl pptr ON ppt.prdid=pptr.prdid AND prdtm_rlid=prdtm_rl_id INNER JOIN prd ON ppt.prdid=prd_id
            WHERE (prdtm_compid=$prdtm_id) AND prdtm_prsnid=0 AND coll_ov IS NULL
            GROUP BY prd_id, prdtm_rl_id
            ORDER BY prdtm_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring production team roles for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['prdtm_sb_rl']) {$prdtm_rl=$row['prdtm_sb_rl'];} else {$prdtm_rl=$row['prdtm_rl'];}
        $prds[$row['prd_id']]['prdtm_rls'][$row['prdtm_rl_id']]=array('prdtm_rl'=>html($prdtm_rl), 'co_comp_ppl'=>array(), 'co_ppl'=>array());
      }

      $sql= "SELECT prd_id, ppt1.prdtm_rlid, prsn_fll_nm
            FROM prdprdtm ppt1
            INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
            INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
            INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
            WHERE (ppt1.prdtm_compid=$ppt_prdtm_id) AND ppt1.prdtm_prsnid=0
            AND ppt2.prdtm_compid!=0 AND ppt1.prdtm_compid=ppt2.prdtm_compid AND coll_ov IS NULL
            GROUP BY prd_id, ppt1.prdtm_compid, prsn_id
            ORDER BY ppt2.prdtm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring co-credited production team (company people - same company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['prdtm_rls'][$row['prdtm_rlid']]['co_comp_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT prd_id, ppt1.prdtm_rlid, ppt2.prdtm_ordr, comp_id, comp_nm
            FROM prdprdtm ppt1
            INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
            INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
            INNER JOIN comp ON ppt2.prdtm_compid=comp_id INNER JOIN prd ON ppt1.prdid=prd_id
            WHERE (ppt1.prdtm_compid=$ppt_prdtm_id) AND ppt1.prdtm_prsnid=0
            AND ppt2.prdtm_prsnid=0 AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NULL
            GROUP BY prd_id, comp_id
            UNION
            SELECT prd_id, ppt1.prdtm_rlid, ppt2.prdtm_ordr, prsn_id, prsn_fll_nm
            FROM prdprdtm ppt1
            INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
            INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
            INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
            WHERE (ppt1.prdtm_compid=$ppt_prdtm_id) AND ppt1.prdtm_prsnid=0
            AND ppt2.prdtm_compid=0 AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY prdtm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring co-credited production team (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prdid']]['prdtm_rls'][$row['prdtm_rlid']]['co_ppl'][$row['comp_id']]=array('co_prsn'=>html($row['comp_nm']), 'comp_ppl'=>array());}

      $sql= "SELECT prd_id, ppt1.prdtm_rlid, ppt2.prdtm_compid, prsn_fll_nm
            FROM prdprdtm ppt1
            INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
            INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
            INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
            WHERE (ppt1.prdtm_compid=$ppt_prdtm_id) AND ppt1.prdtm_prsnid=0
            AND ppt2.prdtm_compid!=0 AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NULL
            GROUP BY prd_id, prdtm_compid, prsn_id
            ORDER BY ppt2.prdtm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring co-credited production team (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['prdtm_rls'][$row['prdtm_rlid']]['co_ppl'][$row['prdtm_compid']]['comp_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdprdtm
            LEFT OUTER JOIN comp ON prdtm_compid=comp_id INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE (prdtm_compid=$prdtm_id) AND prdtm_prsnid=0 AND coll_ov IS NOT NULL
            GROUP BY prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (production team) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $prdtm_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'prdtm_rls'=>array());
        }

        if(!empty($prdtm_sg_prd_ids))
        {
          foreach($prdtm_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, prdtm_rl_id, prdtm_sb_rl, prdtm_rl
              FROM prdprdtm ppt
              INNER JOIN prdprdtmrl pptr ON ppt.prdid=pptr.prdid AND prdtm_rlid=prdtm_rl_id INNER JOIN prd ON ppt.prdid=prd_id
              WHERE (prdtm_compid=$prdtm_id) AND prdtm_prsnid=0 AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdtm_rl_id
              ORDER BY prdtm_rl_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring production team roles for production segments: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['prdtm_sb_rl']) {$prdtm_rl=$row['prdtm_sb_rl'];} else {$prdtm_rl=$row['prdtm_rl'];}
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdtm_rls'][$row['prdtm_rl_id']]=array('prdtm_rl'=>html($prdtm_rl), 'co_comp_ppl'=>array(), 'co_ppl'=>array());
        }

        $sql= "SELECT coll_ov, prd_id, ppt1.prdtm_rlid, prsn_fll_nm
              FROM prdprdtm ppt1
              INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
              INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
              INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
              WHERE (ppt1.prdtm_compid=$ppt_prdtm_id) AND ppt1.prdtm_prsnid=0
              AND ppt2.prdtm_compid!=0 AND ppt1.prdtm_compid=ppt2.prdtm_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, ppt1.prdtm_compid, prsn_id
              ORDER BY ppt2.prdtm_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring co-credited production team (company people - same company) data for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdtm_rls'][$row['prdtm_rlid']]['co_comp_ppl'][]=html($row['prsn_fll_nm']);}

        $sql= "SELECT coll_ov, prd_id, ppt1.prdtm_rlid, ppt2.prdtm_ordr, comp_id, comp_nm
              FROM prdprdtm ppt1
              INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
              INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
              INNER JOIN comp ON ppt2.prdtm_compid=comp_id INNER JOIN prd ON ppt1.prdid=prd_id
              WHERE (ppt1.prdtm_compid=$ppt_prdtm_id) AND ppt1.prdtm_prsnid=0
              AND ppt2.prdtm_prsnid=0 AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, comp_id
              UNION
              SELECT coll_ov, prd_id, ppt1.prdtm_rlid, ppt2.prdtm_ordr, prsn_id, prsn_fll_nm
              FROM prdprdtm ppt1
              INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
              INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
              INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
              WHERE (ppt1.prdtm_compid=$ppt_prdtm_id) AND ppt1.prdtm_prsnid=0
              AND ppt2.prdtm_compid=0 AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prsn_id
              ORDER BY prdtm_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring co-credited production team (company/people) data for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdtm_rls'][$row['prdtm_rlid']]['co_ppl'][$row['comp_id']]=array('co_prsn'=>html($row['comp_nm']), 'comp_ppl'=>array());}

        $sql= "SELECT coll_ov, prd_id, ppt1.prdtm_rlid, ppt2.prdtm_compid, prsn_fll_nm
              FROM prdprdtm ppt1
              INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
              INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
              INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
              WHERE (ppt1.prdtm_compid=$ppt_prdtm_id) AND ppt1.prdtm_prsnid=0
              AND ppt2.prdtm_compid!=0 AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdtm_compid, prsn_id
              ORDER BY ppt2.prdtm_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring co-credited production team (company people) data for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdtm_rls'][$row['prdtm_rlid']]['co_ppl'][$row['prdtm_compid']]['comp_ppl'][]=html($row['prsn_fll_nm']);}
      }
      $prdtm_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM crs
          INNER JOIN prdcrs ON crs_id=crsid
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (crs_schlid=$crs_schl_id)
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM crs
          INNER JOIN prdcrs ON crs_id=crsid INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE (crs_schlid=$crs_schl_id) AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring productions (as drama school): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $crs_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>$row['prd_nm'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array(), 'prd_crss'=>array());
      }

      if(!empty($crs_prd_ids))
      {
        foreach($crs_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM crs INNER JOIN prdcrs ON crs_id=crsid WHERE (crs_schlid=$crs_schl_id) AND prdid='$prd_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as course coordinator): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, crs_typ_nm, crs_yr_strt, crs_yr_end
            FROM crs
            INNER JOIN crs_typ ON crs_typid=crs_typ_id INNER JOIN prdcrs ON crs_id=crsid
            INNER JOIN prd ON prdid=prd_id
            WHERE (crs_schlid=$crs_schl_id) AND coll_ov IS NULL
            ORDER BY crs_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring course info for productions (as drama school): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);}
        else {$crs_yr=$row['crs_yr_strt'];}
        $prds[$row['prd_id']]['prd_crss'][]=html($row['crs_typ_nm'].' ('.$crs_yr.')');
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM crs
            INNER JOIN prdcrs ON crs_id=crsid INNER JOIN prd ON prdid=prd_id
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE (crs_schlid=$crs_schl_id) AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for productions (as drama school): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $crs_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'prd_crss'=>array(), 'wri_rls'=>array());
        }

        if(!empty($crs_sg_prd_ids))
        {
          foreach($crs_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, crs_typ_nm, crs_yr_strt, crs_yr_end
              FROM crs
              INNER JOIN crs_typ ON crs_typid=crs_typ_id INNER JOIN prdcrs ON crs_id=crsid
              INNER JOIN prd ON prdid=prd_id
              WHERE (crs_schlid=$crs_schl_id) AND coll_ov IS NOT NULL
              ORDER BY crs_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring course info for segment productions (as drama school): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);}
          else {$crs_yr=$row['crs_yr_strt'];}
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prd_crss'][]=html($row['crs_typ_nm'].' ('.$crs_yr.')');
        }
      }
      $crs_prds=$prds;
    }

    $sql= "SELECT comp_url, MIN(crs_yr_strt) AS mincrs_yr_strt, MAX(crs_yr_end) AS maxcrs_yr_end, crs_typ_nm, crs_typ_url
          FROM comp
          INNER JOIN crs ON comp_id=crs_schlid INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE (comp_id=$crdt_id)
          GROUP BY crs_id
          ORDER BY mincrs_yr_strt DESC, maxcrs_yr_end DESC, crs_typ_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $crs_typ='<a href="/course/type/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'">'.html($row['crs_typ_nm']).'</a>';
      if($row['mincrs_yr_strt']==$row['maxcrs_yr_end']) {$crs_typ_dts=$row['mincrs_yr_strt'];}
      else {$crs_typ_dts=$row['mincrs_yr_strt'].' - '.$row['maxcrs_yr_end'];}
      $crs_typs[]=array('crs_typ'=>$crs_typ, 'crs_typ_dts'=>$crs_typ_dts);
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph,
          (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdrvw
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE (rvw_pub_compid=$rvw_pub_id) AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW()
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph,
          (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdrvw
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE (rvw_pub_compid=$rvw_pub_id) AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW() AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (review publication) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $rvw_pub_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'rvws'=>array(), 'sg_prds'=>array());
      }

      if(!empty($rvw_pub_prd_ids))
      {
        foreach($rvw_pub_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdrvw WHERE prdid='$prd_id' AND rvw_pub_compid='$comp_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as review publication): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv_tr.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, prsn_fll_nm, prsn_url, DATE_FORMAT(rvw_dt, '%d %b %Y') AS rvw_dt_dsply, rvw_url
            FROM prdrvw
            INNER JOIN prsn ON rvw_crtc_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
            WHERE (rvw_pub_compid=$rvw_pub_id) AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW() AND coll_ov IS NULL
            GROUP BY prd_id, rvw_url
            ORDER BY rvw_dt ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring reviews for (publication) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.$row['prsn_url'].'">'.$row['prsn_fll_nm'].'</a>';
        $rvw_url='<a href="'.html($row['rvw_url']).'" target="'.html($row['rvw_url']).'">review link</a>';
        $prds[$row['prd_id']]['rvws'][]=array('prsn_nm'=>$prsn_nm, 'rvw_dt'=>html($row['rvw_dt_dsply']), 'rvw_url'=>$rvw_url);
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
            prsn_fll_nm, prsn_url, DATE_FORMAT(rvw_dt, '%d %b %Y') AS rvw_dt, rvw_url
            FROM prdrvw
            INNER JOIN prsn ON rvw_crtc_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE (rvw_pub_compid=$rvw_pub_id) AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW() AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for (review publication) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $rvw_pub_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'rvws'=>array());
        }
      }

      if(!empty($rvw_pub_sg_prd_ids))
      {
        foreach($rvw_pub_sg_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv_tr.inc.php';
        }
      }

      $sql= "SELECT coll_ov, prd_id, prsn_fll_nm, prsn_url, DATE_FORMAT(rvw_dt, '%d %b %Y') AS rvw_dt_dsply, rvw_url
            FROM prdrvw
            INNER JOIN prsn ON rvw_crtc_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
            WHERE (rvw_pub_compid=$rvw_pub_id) AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW() AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id, rvw_url
            ORDER BY rvw_dt ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring reviews for (publication) production segments: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.$row['prsn_url'].'">'.$row['prsn_fll_nm'].'</a>';
        $rvw_url='<a href="'.html($row['rvw_url']).'" target="'.html($row['rvw_url']).'">review link</a>';
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['rvws'][]=array('prsn_nm'=>$prsn_nm, 'rvw_dt'=>html($row['rvw_dt_dsply']), 'rvw_url'=>$rvw_url);
      }
      $rvw_pub_prds=$prds;
    }

    $sql= "SELECT cc.crsid, comp_nm, comp_url, crs_typ_nm, crs_typ_url, crs_yr_strt, crs_yr_end, crs_yr_url,
          DATE_FORMAT(crs_dt_strt, '%d %b %Y') AS crs_dt_strt_dsply, DATE_FORMAT(crs_dt_end, '%d %b %Y') AS crs_dt_end_dsply, cdntr_rl, cdntr_sb_rl
          FROM crscdntr cc
          INNER JOIN crs ON cc.crsid=crs_id INNER JOIN comp ON crs_schlid=comp_id INNER JOIN crs_typ ON crs_typid=crs_typ_id
          INNER JOIN crscdntrrl ccr ON cc.crsid=ccr.crsid
          WHERE (cdntr_compid=$cdntr_id) AND cdntr_prsnid=0
          ORDER BY crs_dt_end DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring courses (as course coordinator): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].' - '.$row['crs_yr_end'];} else {$crs_yr=$row['crs_yr_strt'];}
        if($row['crs_dt_strt_dsply'] && $row['crs_dt_end_dsply']) {$crs_dts=$row['crs_dt_strt_dsply'].' - '.$row['crs_dt_end_dsply'];} else {$crs_dts=$crs_yr;}
        $crs_nm='<a href="/course/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'/'.html($row['crs_yr_url']).'">'.html($row['comp_nm']).': '.html($row['crs_typ_nm']).' '.$crs_yr.'</a>';
        if($row['cdntr_sb_rl']) {$cdntr_rl=$row['cdntr_sb_rl'];} else {$cdntr_rl=$row['cdntr_rl'];}
        $cdntr_crss[$row['crsid']]=array('crs_nm'=>$crs_nm, 'crs_dts'=>html($crs_dts), 'cdntr_rl'=>html($cdntr_rl), 'co_ppl'=>array());
      }

      $sql= "SELECT cc1.crsid, cc2.cdntr_ordr, comp_nm
            FROM crscdntr cc1
            INNER JOIN crscdntrrl ccr ON cc1.crsid=ccr.crsid AND cc1.cdntr_rlid=cdntr_rl_id
            INNER JOIN crscdntr cc2 ON ccr.crsid=cc2.crsid AND cdntr_rl_id=cc2.cdntr_rlid
            INNER JOIN comp ON cc2.cdntr_compid=comp_id
            WHERE (cc1.cdntr_compid=$cc_cdntr_id) AND cc1.cdntr_prsnid=0 AND cc2.cdntr_prsnid=0 AND cc1.cdntr_compid!=cc2.cdntr_compid
            GROUP BY crsid, comp_id
            UNION
            SELECT cc1.crsid, cc2.cdntr_ordr, prsn_fll_nm
            FROM crscdntr cc1
            INNER JOIN crscdntrrl ccr ON cc1.crsid=ccr.crsid AND cc1.cdntr_rlid=cdntr_rl_id
            INNER JOIN crscdntr cc2 ON ccr.crsid=cc2.crsid AND cdntr_rl_id=cc2.cdntr_rlid
            INNER JOIN prsn ON cc2.cdntr_prsnid=prsn_id
            WHERE (cc1.cdntr_compid=$cc_cdntr_id) AND cc1.cdntr_prsnid=0 AND cc2.cdntr_compid=0 AND cc1.cdntr_compid!=cc2.cdntr_compid
            GROUP BY crsid, prsn_id
            ORDER BY cdntr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-credited course coordinator (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$cdntr_crss[$row['crsid']]['co_ppl'][]=array('co_prsn'=>html($row['comp_nm']));}
    }

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, p2.pt_pub_dt, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND org_wri=0 AND src_wri=0 AND grntr=0
          GROUP BY p2.pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, pt_pub_dt, COALESCE(pt_alph, pt_nm)pt_alph,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND org_wri=0 AND src_wri=0 AND grntr=0 AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_pub_dt DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (writer) playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $wri_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($wri_pt_ids))
      {
        foreach($wri_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptwri WHERE ptid='$pt_id' AND (wri_compid=$wri_id) AND wri_prsnid=0 AND org_wri=0 AND src_wri=0 AND grntr=0";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this company (as writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
            GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwri pw
            INNER JOIN pt ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND org_wri=0 AND src_wri=0 AND grntr=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for (writer) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $wri_sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
        }

        if(!empty($wri_sg_pt_ids))
        {
          foreach($wri_sg_pt_ids as $sg_pt_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
          }
        }
      }
      $wri_pts=$pts;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, p2.pt_pub_dt, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND org_wri=1
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, pt_pub_dt, COALESCE(pt_alph, pt_nm)pt_alph,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND org_wri=1 AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_pub_dt DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (original writer) playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $org_wri_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($org_wri_pt_ids))
      {
        foreach($org_wri_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptwri WHERE ptid='$pt_id' AND (wri_compid=$wri_id) AND wri_prsnid=0 AND org_wri=1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this company (as original writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
            GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwri pw
            INNER JOIN pt ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND org_wri=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for (original writer) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $org_wri_sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
        }

        if(!empty($org_wri_sg_pt_ids))
        {
          foreach($org_wri_sg_pt_ids as $sg_pt_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
          }
        }
      }
      $org_wri_pts=$pts;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, p2.pt_pub_dt, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND src_wri=1
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, pt_pub_dt, COALESCE(pt_alph, pt_nm)pt_alph,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND src_wri=1 AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_pub_dt DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (source writer) playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $src_wri_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($src_wri_pt_ids))
      {
        foreach($src_wri_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptwri WHERE ptid='$pt_id' AND (wri_compid=$wri_id) AND wri_prsnid=0 AND src_wri=1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this company (as source writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
            GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwri pw
            INNER JOIN pt ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND src_wri=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for (source writer) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $src_wri_sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
        }

        if(!empty($src_wri_sg_pt_ids))
        {
          foreach($src_wri_sg_pt_ids as $sg_pt_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
          }
        }
      }
      $src_wri_pts=$pts;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=1
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=1 AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (rights grantor) playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $grntr_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($grntr_pt_ids))
      {
        foreach($grntr_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptwri WHERE ptid='$pt_id' AND (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=1 LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this company (as grantor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
            GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwri pw
            INNER JOIN pt ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE (wri_compid=$wri_id) AND wri_prsnid=0 AND grntr=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for (rights grantor) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $grntr_sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
        }

        if(!empty($grntr_sg_pt_ids))
        {
          foreach($grntr_sg_pt_ids as $sg_pt_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
          }
        }
      }
      $grntr_pts=$pts;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll,
          COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptcntr pc
          INNER JOIN pt p1 ON pc.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (cntr_compid=$cntr_id) AND cntr_prsnid=0
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll,
          COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptcntr pc
          INNER JOIN pt p1 ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (cntr_compid=$cntr_id) AND cntr_prsnid=0 AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (contributor) playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='2') {$txt_vrsn_nm='Collected Works';} elseif($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $cntr_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'cntr_rls'=>array(), 'sg_pts'=>array(), 'wrks_sg_pts'=>array());
      }

      if(!empty($cntr_pt_ids))
      {
        foreach($cntr_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptcntr WHERE ptid='$pt_id' AND (cntr_compid=$cntr_id) AND cntr_prsnid=0 LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this person (as contributor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_cntr_rcv.inc.php';
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wrks_sg_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
            GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptcntr pc
            INNER JOIN pt ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE (cntr_compid=$cntr_id) AND cntr_prsnid=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for (contributor) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $cntr_sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('sg_pt_nm'=>$pt_nm, 'sg_pt_nm_pln'=>html($row['pt_nm']), 'sg_txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'sg_pt_yr'=>$pt_yr, 'sg_cntr_rls'=>array());
        }

        if(!empty($cntr_sg_pt_ids))
        {
          foreach($cntr_sg_pt_ids as $sg_pt_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_cntr_rcv.inc.php';
          }
        }
      }
      $cntr_pts=$pts;
    }

    $sql= "SELECT thtr_id, thtr_nm, thtr_lctn, thtr_url
          FROM thtrcomp
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE compid='$comp_id' AND thtr_clsd=0 AND thtr_nm_exp=0
          GROUP BY thtr_id
          ORDER BY thtr_nm ASC, thtr_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['thtr_lctn']) {$thtr_lctn=' ('.html($row['thtr_lctn']).')';} else {$thtr_lctn='';}
      $thtr_ids[]=$row['thtr_id'];
      $thtrs[$row['thtr_id']]=array('thtr'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>'.$thtr_lctn, 'thtr_sbsq_nms'=>array(), 'thtr_prvs_nms'=>array(), 'thtr_sbthtrs'=>array());
    }

    $sql= "SELECT thtr_id, comp_nm, comp_url FROM thtrcomp tc1
          INNER JOIN thtr ON tc1.thtrid=thtr_id INNER JOIN thtrcomp tc2 ON thtr_id=tc2.thtrid INNER JOIN comp ON tc2.compid=comp_id
          WHERE tc1.compid='$comp_id' AND thtr_clsd=0 AND thtr_nm_exp=0 AND tc1.compid!=tc2.compid ORDER BY tc2.thtr_comp_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre subsequently named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$thtrs[$row['thtr_id']]['co_ownr_comps'][]='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}

    if(!empty($thtr_ids))
    {
      foreach($thtr_ids as $thtr_id)
      {
        $sql= "SELECT thtr_sbsq_id, thtr_nm, thtr_url
              FROM thtr_aka
              INNER JOIN thtr ON thtr_sbsq_id=thtr_id
              WHERE thtr_prvs_id='$thtr_id'
              ORDER BY thtr_nm_frm_dt DESC, thtr_nm_exp_dt DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring theatre subsequently named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['thtr_sbsq_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}

        $sql= "SELECT thtr_prvs_id, thtr_nm, thtr_url
              FROM thtr_aka
              INNER JOIN thtr ON thtr_prvs_id=thtr_id
              WHERE thtr_sbsq_id='$thtr_id'
              ORDER BY thtr_nm_frm_dt DESC, thtr_nm_exp_dt DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring theatre previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['thtr_prvs_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}

        $sql= "SELECT sbthtrid, sbthtr_nm, thtr_url
              FROM thtrsbthtr
              INNER JOIN thtr ON sbthtrid=thtr_id
              WHERE thtrid='$thtr_id' AND thtr_clsd=0 AND thtr_nm_exp=0
              ORDER BY sbthtr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring sub-theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['sbthtrs'][$row['sbthtrid']]=array('sbthtr'=>'<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>', 'sbthtr_sbsq_nms'=>array(), 'sbthtr_prvs_nms'=>array());}

        $sql= "SELECT sbthtrid, t2.sbthtr_nm, t2.thtr_url
              FROM thtrsbthtr
              INNER JOIN thtr t1 ON sbthtrid=t1.thtr_id INNER JOIN thtr_aka ON t1.thtr_id=thtr_prvs_id INNER JOIN thtr t2 ON thtr_sbsq_id=t2.thtr_id
              WHERE thtrid='$thtr_id' AND t1.thtr_clsd=0 AND t1.thtr_nm_exp=0
              ORDER BY sbthtr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring sub-theatre subsequently named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['sbthtrs'][$row['sbthtrid']]['sbthtr_sbsq_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}

        $sql= "SELECT sbthtrid, t2.sbthtr_nm, t2.thtr_url
              FROM thtrsbthtr
              INNER JOIN thtr t1 ON sbthtrid=t1.thtr_id INNER JOIN thtr_aka ON t1.thtr_id=thtr_sbsq_id INNER JOIN thtr t2 ON thtr_prvs_id=t2.thtr_id
              WHERE thtrid='$thtr_id' AND t1.thtr_clsd=0 AND t1.thtr_nm_exp=0
              ORDER BY sbthtr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring sub-theatre previously named data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$thtrs[$thtr_id]['sbthtrs'][$row['sbthtrid']]['sbthtr_prvs_nms'][]='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>';}
      }
    }

    $sql="SELECT lctn_id, lctn_nm, lctn_url FROM comp_lctn INNER JOIN lctn ON comp_lctnid=lctn_id WHERE compid='$comp_id' ORDER BY comp_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$lctns[$row['lctn_id']]=array('lctn'=>'<a href="/company/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>', 'rel_lctns'=>array());}

      $sql= "SELECT rel_lctn1, lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
            FROM comp_lctn cl
            INNER JOIN rel_lctn ON comp_lctnid=rel_lctn1 INNER JOIN lctn ON rel_lctn2=lctn_id
            LEFT OUTER JOIN comp_lctn_alt cla ON cl.compid=cla.compid AND cl.comp_lctnid=cla.comp_lctnid
            WHERE cl.compid='$comp_id' AND lctn_exp=0 AND lctn_fctn=0 AND cla.compid IS NULL
            UNION
            SELECT rel_lctn1, lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
            FROM comp_lctn cl
            INNER JOIN rel_lctn ON cl.comp_lctnid=rel_lctn1 INNER JOIN comp_lctn_alt cla ON rel_lctn2=comp_lctn_altid
            INNER JOIN lctn ON comp_lctn_altid=lctn_id
            WHERE cl.compid='$comp_id' AND cl.compid=cla.compid AND cl.comp_lctnid=cla.comp_lctnid
            ORDER BY rel_lctn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related location data (for company location): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']);} else {$rel_lctn_nt1='';}
        if($row['rel_lctn_nt2']) {$rel_lctn_nt2=html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2='';}
        $lctns[$row['rel_lctn1']]['rel_lctns'][]=$rel_lctn_nt1.'<a href="/company/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt2;
      }
    }

    $sql= "SELECT comp_typ_nm, comp_typ_url
          FROM comptyp
          INNER JOIN comp_typ ON comp_typid=comp_typ_id
          WHERE compid='$comp_id'
          ORDER BY comp_typ_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$comp_typs[]='<a href="/company/type/'.html($row['comp_typ_url']).'">'.html($row['comp_typ_nm']).'</a>';}

    $sql= "SELECT prsn_fll_nm, prsn_url, compprsn_rl, compprsn_rl_nt, compprsn_yr_strt, compprsn_yr_end
          FROM compprsn
          INNER JOIN prsn ON prsnid=prsn_id
          WHERE compid='$comp_id'
          ORDER BY compprsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring company member (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['compprsn_rl_nt']) {$comp_prsn_rl_nt=' <em>('.html($row['compprsn_rl_nt']).')</em>';} else {$comp_prsn_rl_nt='';}
      if($row['compprsn_yr_strt']) {$comp_prsn_yr_strt=$row['compprsn_yr_strt'];} else {$comp_prsn_yr_strt=NULL;}
      if($row['compprsn_yr_end'])
      {if($row['compprsn_yr_end']==$row['compprsn_yr_strt']) {$comp_prsn_yr_end='';} else {$comp_prsn_yr_end=' - '.$row['compprsn_yr_end'];}}
      else {if($row['compprsn_yr_strt']) {$comp_prsn_yr_end=' - TBC';} else {$comp_prsn_yr_end='';}}
      if($comp_prsn_yr_strt || $comp_prsn_yr_end) {$comp_prsn_yrs=' ('.html($comp_prsn_yr_strt.$comp_prsn_yr_end).')';} else {$comp_prsn_yrs='';}
      $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
      $comp_ppl[]=array('prsn_nm'=>$prsn_nm, 'comp_prsn_yrs'=>$comp_prsn_yrs, 'comp_prsn_rl'=>html($row['compprsn_rl']), 'comp_prsn_rl_nt'=>$comp_prsn_rl_nt);
    }

    $sql= "SELECT prsn_id, prsn_fll_nm, prsn_url, agnt_rl
          FROM prsnagnt
          INNER JOIN prsn ON prsnid=prsn_id
          WHERE agnt_compid='$comp_id'
          ORDER BY prsn_lst_nm ASC, prsn_frst_nm ASC, prsn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring client (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $clnts[$row['prsn_id']]=array('prsn_nm'=>$prsn_nm, 'agnt_rl'=>html($row['agnt_rl']), 'agnts'=>array());
      }

      $sql= "SELECT prsnid, prsn_fll_nm, prsn_url, agnt_rl
            FROM prsnagnt
            INNER JOIN prsn ON agnt_prsnid=prsn_id
            WHERE agnt_compid='$comp_id'
            ORDER BY agnt_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring agency/agent (inc. role) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $clnts[$row['prsnid']]['agnts'][]=array('prsn_nm'=>$prsn_nm, 'agnt_rl'=>html($row['agnt_rl']));
      }
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, NULL AS lcnsr_rl,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, p2.pt_coll,
          COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptlcnsr pl
          INNER JOIN pt p1 ON pl.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (lcnsr_compid=$lcnsr_id) AND lcnsr_prsnid=0
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, lcnsr_rl,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll,
          COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptlcnsr pl
          INNER JOIN pt p1 ON pl.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (lcnsr_compid=$lcnsr_id) AND lcnsr_prsnid=0 AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext data (as licensor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $lcnsr_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'lcnsr_rl'=>html($row['lcnsr_rl']), 'sg_cnt'=>$row['sg_cnt'], 'lcnsr_ppl'=>array(), 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($lcnsr_pt_ids))
      {
        foreach($lcnsr_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptlcnsr WHERE ptid='$pt_id' AND (lcnsr_compid=$lcnsr_id) AND lcnsr_prsnid=0 LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this company (as licensor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT pt_id, prsn_fll_nm, prsn_url, lcnsr_rl
            FROM ptlcnsr
            INNER JOIN prsn ON lcnsr_prsnid=prsn_id INNER JOIN pt ON ptid=pt_id
            WHERE (lcnsr_compid=$lcnsr_id) AND coll_ov IS NULL
            ORDER BY lcnsr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring licensors (inc. role) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $pts[$row['pt_id']]['lcnsr_ppl'][]=array('prsn_nm'=>$prsn_nm, 'lcnsr_rl'=>$row['lcnsr_rl']);
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, lcnsr_rl,
            GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM ptlcnsr pl
            INNER JOIN pt ON pl.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE (lcnsr_compid=$lcnsr_id) AND lcnsr_prsnid=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data (as licensor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $lcnsr_sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'lcnsr_rl'=>html($row['lcnsr_rl']), 'sg_lcnsr_ppl'=>array(), 'wri_rls'=>array());
        }

        if(!empty($lcnsr_sg_pt_ids))
        {
          foreach($lcnsr_sg_pt_ids as $sg_pt_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, pt_id, prsn_fll_nm, prsn_url, lcnsr_rl
              FROM ptlcnsr
              INNER JOIN prsn ON lcnsr_prsnid=prsn_id INNER JOIN pt ON ptid=pt_id
              WHERE (lcnsr_compid=$lcnsr_id) AND coll_ov IS NOT NULL
              ORDER BY lcnsr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring licensors (inc. role) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]['lcnsr_ppl'][]=array('prsn_nm'=>$prsn_nm, 'lcnsr_rl'=>$row['lcnsr_rl']);
        }
      }
      $lcnsr_pts=$pts;
    }

    $awrds_ttl_wins=array(); $awrds_ttl_noms=array();
    $sql= "SELECT awrds_id, awrds_nm, awrds_url, COALESCE(awrds_alph, awrds_nm)awrds_alph FROM awrdnomppl
          INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id
          WHERE (nom_compid=$awrd_id) AND nom_prsnid=0 GROUP BY awrds_id ORDER BY awrds_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$awrds[$row['awrds_id']]=array('awrds_nm'=>html($row['awrds_nm']), 'awrd_yrs'=>array(), 'awrd_wins'=>array(), 'awrd_noms'=>array());}

      $sql= "SELECT awrds_id, awrd_id, awrd_yr, awrd_yr_end, awrd_yr_url, awrds_url FROM awrdnomppl
            INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id
            WHERE (nom_compid=$awrd_id) AND nom_prsnid=0 GROUP BY awrds_id, awrd_id ORDER BY awrd_yr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['awrd_yr_end']) {$awrd_yr_end=html(preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']));} else {$awrd_yr_end='';}
        $awrd_lnk='<b><a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrd_yr']).$awrd_yr_end.'</a></b>';
        $awrds[$row['awrds_id']]['awrd_yrs'][$row['awrd_id']]=array('awrd_lnk'=>$awrd_lnk, 'awrd_yr_wins'=>array(), 'awrd_yr_noms'=>array(), 'ctgrys'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, awrd_ctgry_id, COALESCE(awrd_ctgry_alt_nm, awrd_ctgry_nm)awrd_ctgry_nm
            FROM awrdnomppl anp
            INNER JOIN awrd ON anp.awrdid=awrd_id
            INNER JOIN awrdctgrys ac ON anp.awrdid=ac.awrdid AND anp.awrd_ctgryid=ac.awrd_ctgryid INNER JOIN awrd_ctgry ON ac.awrd_ctgryid=awrd_ctgry_id
            WHERE (nom_compid=$awrd_id) AND nom_prsnid=0 GROUP BY awrdsid, awrd_id, awrd_ctgry_id ORDER BY awrd_ctgry_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgry_id']]=array('awrd_ctgry_nm'=>html($row['awrd_ctgry_nm']), 'noms'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, nom_id, nom_win_dscr, win_bool, nom_rl FROM awrdnomppl anp
            INNER JOIN awrd ON anp.awrdid=awrd_id INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND anp.nomid=nom_id
            WHERE (nom_compid=$awrd_id) AND nom_prsnid=0 GROUP BY awrdsid, awrd_id, awrd_ctgryid, nom_id ORDER BY nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award nominations data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        if($row['win_bool']) {$awrds_ttl_wins[]=1; $awrds[$row['awrdsid']]['awrd_wins'][]=1; $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_wins'][]=1;}
        else {$awrds_ttl_noms[]=1; $awrds[$row['awrdsid']]['awrd_noms'][]=1; $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_noms'][]=1;}
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nom_id']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'win'=>$row['win_bool'], 'nom_rl'=>$nom_rl, 'nomcomp_ppl'=>array(), 'co_nomppl'=>array(), 'nomprds'=>array(), 'nompts'=>array(), 'cowins'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, nom_id, prsn_fll_nm, prsn_url, nom_rl FROM awrdnomppl anp
            INNER JOIN awrd ON anp.awrdid=awrd_id INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND anp.nomid=nom_id
            INNER JOIN prsn ON anp.nom_prsnid=prsn_id
            WHERE (nom_compid=$awrd_id) GROUP BY awrdsid, awrd_id, awrd_ctgryid, nom_id, prsn_id ORDER BY nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award company member data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nom_id']]['nomcomp_ppl'][]=$prsn_nm;
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, anp2.nom_ordr, anp2.nom_rl, comp_id, comp_nm, comp_url, comp_bool FROM awrdnomppl anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnomppl anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid AND anp1.nom_compid!=anp2.nom_compid
            INNER JOIN comp ON anp2.nom_compid=comp_id
            WHERE (anp1.nom_compid=$anp1_awrd_id) AND anp1.nom_prsnid=0 AND anp2.nom_prsnid=0
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, comp_id
            UNION
            SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, anp2.nom_ordr, anp2.nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool FROM awrdnomppl anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnomppl anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid
            INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
            WHERE (anp1.nom_compid=$anp1_awrd_id) AND anp1.nom_prsnid=0 AND anp2.nom_compid=0
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, prsn_id
            ORDER BY nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-nominee/winner (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$nom_prsn='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
        else {$nom_prsn=html($row['comp_nm']).$nom_rl;}
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['co_nomppl'][$row['comp_id']]=array('nom_prsn'=>$nom_prsn);
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, anp2.nom_rl, anp2.nom_compid, prsn_fll_nm, prsn_url FROM awrdnomppl anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnomppl anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid AND anp1.nom_compid!=anp2.nom_compid
            INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
            WHERE (anp1.nom_compid=$anp1_awrd_id) AND anp1.nom_prsnid=0 AND anp2.nom_compid!=0
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, nom_compid, prsn_id ORDER BY anp2.nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-nominee/winner (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        $nom_compprsn='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['co_nomppl'][$row['nom_compid']]['co_nomcomp_ppl'][]=$nom_compprsn;
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, anp.nomid, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm FROM awrdnomppl anp
            INNER JOIN awrd ON anp.awrdid=awrd_id
            INNER JOIN awrdnomprds anprd ON anp.awrdid=anprd.awrdid AND anp.awrd_ctgryid=anprd.awrd_ctgryid AND anp.nomid=anprd.nomid
            INNER JOIN prd p ON nom_prdid=prd_id
            INNER JOIN thtr ON p.thtrid=thtr_id
            WHERE (nom_compid=$awrd_id) AND nom_prsnid=0 GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (production) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nomprds'][]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, anp.nomid, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph
            FROM awrdnomppl anp
            INNER JOIN awrd ON anp.awrdid=awrd_id
            INNER JOIN awrdnompts anpt ON anp.awrdid=anpt.awrdid AND anp.awrd_ctgryid=anpt.awrd_ctgryid AND anp.nomid=anpt.nomid
            INNER JOIN pt ON nom_ptid=pt_id
            WHERE (nom_compid=$awrd_id) AND nom_prsnid=0 GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, pt_id ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (playtext) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nompts'][]=$pt_nm.' ('.$pt_yr.')';
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, an2.nom_id AS n2, an2.nom_win_dscr FROM awrdnomppl anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
            INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
            INNER JOIN awrdnomppl anp2 ON an2.awrdid=anp2.awrdid
            WHERE (anp1.nom_compid=$anp1_awrd_id) AND anp1.nom_prsnid=0 AND an1.win_bool=1 AND an2.win_bool=1
            AND an2.nom_id NOT IN(SELECT nomid FROM awrdnomppl WHERE (nom_compid=$awrd_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2 ORDER BY an2.nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (co-winner) award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {$awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'cowin_nomppl'=>array(), 'cowin_nomprds'=>array(), 'cowin_nompts'=>array());}

        $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, anp2.nomid AS n2, anp2.nom_ordr, anp2.nom_rl, comp_id, comp_nm, comp_url, comp_bool
              FROM awrdnomppl anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid AND an2.nom_id=anp2.nomid
              INNER JOIN comp ON anp2.nom_compid=comp_id
              WHERE (anp1.nom_compid=$anp1_awrd_id) AND anp1.nom_prsnid=0 AND an1.win_bool=1 AND an2.win_bool=1 AND anp2.nom_prsnid=0
              AND anp2.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE (nom_compid=$awrd_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, comp_id
              UNION
              SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, anp2.nomid AS n2, anp2.nom_ordr, anp2.nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool
              FROM awrdnomppl anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid AND an2.nom_id=anp2.nomid
              INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
              WHERE (anp1.nom_compid=$anp1_awrd_id) AND anp1.nom_prsnid=0 AND an1.win_bool=1 AND an2.win_bool=1 AND anp2.nom_compid=0
              AND anp2.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE (nom_compid=$awrd_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prsn_id
              ORDER BY nom_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards company/people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          $cowin_prsn='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;
          if(!preg_match('/^the-company$/', $row['comp_url'])) {$cowin_prsn='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
          else {$cowin_prsn=html($row['comp_nm']).$nom_rl;}
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['comp_id']]=array('cowin_prsn'=>$cowin_prsn, 'cowincomp_ppl'=>array());
        }

        $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, anp2.nomid AS n2, anp2.nom_compid, anp2.nom_ordr, anp2.nom_rl, prsn_id, prsn_fll_nm, prsn_url
              FROM awrdnomppl anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid AND an2.nom_id=anp2.nomid
              INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
              WHERE (anp1.nom_compid=$anp1_awrd_id) AND anp1.nom_prsnid=0 AND an1.win_bool=1 AND an2.win_bool=1 AND anp2.nom_compid!='0'
              AND anp2.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE (nom_compid=$awrd_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, nom_compid, prsn_id ORDER BY nom_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards company/people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          $cowincomp_prsn='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['nom_compid']]['cowincomp_ppl'][]=$cowincomp_prsn;
        }

        $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, anp.nomid AS n1, anprd.nomid AS n2, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
              DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm FROM awrdnomppl anp
              INNER JOIN awrd ON anp.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp.awrdid=an1.awrdid AND anp.awrd_ctgryid=an1.awrd_ctgryid AND anp.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomprds anprd ON an2.awrdid=anprd.awrdid AND an2.awrd_ctgryid=anprd.awrd_ctgryid AND an2.nom_id=anprd.nomid
              INNER JOIN prd p ON nom_prdid=prd_id
              INNER JOIN thtr ON p.thtrid=thtr_id
              WHERE (anp.nom_compid=$anp_awrd_id) AND anp.nom_prsnid=0 AND an1.win_bool=1 AND an2.win_bool=1
              AND anprd.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE (nom_compid=$awrd_id) AND awrdid=anp.awrdid AND awrd_ctgryid=anp.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards (productions) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_prds'][]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
        }

        $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, anp.nomid AS n1, anpt.nomid AS n2,
            pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph FROM awrdnomppl anp
              INNER JOIN awrd ON anp.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp.awrdid=an1.awrdid AND anp.awrd_ctgryid=an1.awrd_ctgryid AND anp.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnompts anpt ON an2.awrdid=anpt.awrdid AND an2.awrd_ctgryid=anpt.awrd_ctgryid AND an2.nom_id=anpt.nomid
              INNER JOIN pt ON nom_ptid=pt_id
              WHERE (anp.nom_compid=$anp_awrd_id) AND anp.nom_prsnid=0 AND an1.win_bool=1 AND an2.win_bool=1
              AND anpt.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE (nom_compid=$awrd_id) AND awrdid=anp.awrdid AND awrd_ctgryid=anp.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, pt_id ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards (playtexts) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_pts'][]=$pt_nm.' ('.$pt_yr.')';
        }
      }
    }

    $comp_id=html($comp_id);
    include 'company.html.php';
  }
?>