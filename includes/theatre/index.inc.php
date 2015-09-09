<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thtr_id=cln($_POST['thtr_id']);
    $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, thtr_fll_nm, thtr_adrs, thtr_cpcty, thtr_opn_dt, thtr_opn_dt_frmt, thtr_cls_dt, thtr_cls_dt_frmt, thtr_clsd, thtr_nm_frm_dt, thtr_nm_frm_dt_frmt, thtr_nm_exp_dt, thtr_nm_exp_dt_frmt, thtr_nm_exp, thtr_tr_ov
          FROM thtr
          WHERE thtr_id='$thtr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['thtr_sffx_num']) {$thtr_sffx_num=html($row['thtr_sffx_num']); $thtr_sffx_rmn=' ('.romannumeral($row['thtr_sffx_num']).')';}
    else {$thtr_sffx_num=''; $thtr_sffx_rmn='';}
    $pagetab='Edit: '.html($row['thtr_fll_nm'].$thtr_sffx_rmn);
    if(!$row['thtr_tr_ov']) {$pagehdr='THEATRE:'; $pagedscr='theatre';} else {$pagehdr='TOUR TYPE:'; $pagedscr='tour type';}
    $thtr_nm_dsply=html($row['thtr_nm']);
    if($row['sbthtr_nm']) {$sbthtr_nm_dsply=':</br>'.html($row['sbthtr_nm']);} else {$sbthtr_nm_dsply='';}
    if($row['thtr_lctn']) {$thtr_lctn_dsply='('.html($row['thtr_lctn']).')';} else {$thtr_lctn_dsply='';}
    $thtr_nm=html($row['thtr_nm']);
    $sbthtr_nm=html($row['sbthtr_nm']);
    $thtr_lctn=html($row['thtr_lctn']);
    $thtr_adrs=html($row['thtr_adrs']);
    $thtr_cpcty=html($row['thtr_cpcty']);
    $thtr_opn_dt=html($row['thtr_opn_dt']);
    $thtr_opn_dt_frmt=html($row['thtr_opn_dt_frmt']);
    $thtr_cls_dt=html($row['thtr_cls_dt']);
    $thtr_cls_dt_frmt=html($row['thtr_cls_dt_frmt']);
    $thtr_clsd=html($row['thtr_clsd']);
    $thtr_nm_frm_dt=html($row['thtr_nm_frm_dt']);
    $thtr_nm_frm_dt_frmt=html($row['thtr_nm_frm_dt_frmt']);
    $thtr_nm_exp_dt=html($row['thtr_nm_exp_dt']);
    $thtr_nm_exp_dt_frmt=html($row['thtr_nm_exp_dt_frmt']);
    $thtr_nm_exp=html($row['thtr_nm_exp']);
    $thtr_tr_ov=html($row['thtr_tr_ov']);

    $sql="SELECT lctn_nm, lctn_sffx_num FROM thtr INNER JOIN lctn ON thtr_lctnid=lctn_id WHERE thtr_id='$thtr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring location (link) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      $row=mysqli_fetch_array($result);
      if($row['lctn_sffx_num']) {$thtr_lctn_lnk_sffx_num='--'.$row['lctn_sffx_num'];} else {$thtr_lctn_lnk_sffx_num='';}
      $lctn_lnk_nm=html($row['lctn_nm'].$thtr_lctn_lnk_sffx_num);
    }
    else {$lctn_lnk_nm='';}

    $sql= "SELECT lctn_nm, lctn_sffx_num FROM thtr t
          INNER JOIN rel_lctn ON t.thtr_lctnid=rel_lctn1 INNER JOIN thtr_lctn_alt tla ON rel_lctn2=tla.thtr_lctn_altid INNER JOIN lctn ON tla.thtr_lctn_altid=lctn_id
          WHERE t.thtr_id='$thtr_id' AND t.thtr_id=tla.thtrid AND t.thtr_lctnid=tla.thtr_lctnid
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring alternate location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$lctn_lnk_alt_sffx_num='--'.$row['lctn_sffx_num'];} else {$lctn_lnk_alt_sffx_num='';}
      $lctn_lnk_alts[]=$row['lctn_nm'].$lctn_lnk_alt_sffx_num;
    }
    if(!empty($lctn_lnk_alts)) {$lctn_lnk_alt_list='||'.implode('>>', $lctn_lnk_alts);} else {$lctn_lnk_alt_list='';}
    $lctn_lnk_nm .= $lctn_lnk_alt_list;

    $sql="SELECT thtr_typ_nm FROM thtrtyp INNER JOIN thtr_typ ON thtr_typid=thtr_typ_id WHERE thtrid='$thtr_id' ORDER BY thtr_typ_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$thtr_typs[]=$row['thtr_typ_nm'];}
    if(!empty($thtr_typs)) {$thtr_typ_list=html(implode(',,', $thtr_typs));} else {$thtr_typ_list='';}

    $sql="SELECT comp_nm, comp_sffx_num FROM thtrcomp INNER JOIN comp ON compid=comp_id WHERE thtrid='$thtr_id' ORDER BY thtr_comp_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre company (owned by) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_sffx_num']) {$thtr_comp_sffx_num='--'.$row['comp_sffx_num'];} else {$thtr_comp_sffx_num='';}
      $thtr_comps[]=$row['comp_nm'].$thtr_comp_sffx_num;
    }
    if(!empty($thtr_comps)) {$thtr_comp_list=html(implode(',,', $thtr_comps));} else {$thtr_comp_list='';}

    $sql="SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num FROM thtr WHERE srthtrid='$thtr_id' ORDER BY sbthtr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring subtheatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['sbthtr_nm']) {$sbsbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbsbthtr_nm='';}
      if($row['thtr_lctn']) {$sbthtr_lctn='::'.$row['thtr_lctn'];} else {$sbthtr_lctn='';}
      if($row['thtr_sffx_num']) {$sbthtr_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbthtr_sffx_num='';}
      $sbthtrs[]=$row['thtr_nm'].$sbsbthtr_nm.$sbthtr_lctn.$sbthtr_sffx_num;
    }
    if(!empty($sbthtrs)) {$sbthtr_list=html(implode(',,', $sbthtrs));} else {$sbthtr_list='';}

    $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, COALESCE(thtr_alph, thtr_nm)thtr_alph
          FROM thtr_aka INNER JOIN thtr ON thtr_sbsq_id=thtr_id
          WHERE thtr_prvs_id='$thtr_id' ORDER BY thtr_cls_dt ASC, thtr_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring "theatre subsequently known as" data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['sbthtr_nm']) {$sbsq_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbsq_sbthtr_nm='';}
      if($row['thtr_lctn']) {$sbsq_thtr_lctn='::'.$row['thtr_lctn'];} else {$sbsq_thtr_lctn='';}
      if($row['thtr_sffx_num']) {$sbsq_thtr_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbsq_thtr_sffx_num='';}
      $thtr_sbsqs[]=$row['thtr_nm'].$sbsq_sbthtr_nm.$sbsq_thtr_lctn.$sbsq_thtr_sffx_num;
    }
    if(!empty($thtr_sbsqs)) {$sbsq_thtr_list=html(implode(',,', $thtr_sbsqs));} else {$sbsq_thtr_list='';}

    $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, COALESCE(thtr_alph, thtr_nm)thtr_alph FROM thtr_aka INNER JOIN thtr ON thtr_prvs_id=thtr_id
          WHERE thtr_sbsq_id='$thtr_id' ORDER BY thtr_cls_dt ASC, thtr_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring "theatre previously known as" data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['sbthtr_nm']) {$prvs_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$prvs_sbthtr_nm='';}
      if($row['thtr_lctn']) {$prvs_thtr_lctn='::'.$row['thtr_lctn'];} else {$prvs_thtr_lctn='';}
      if($row['thtr_sffx_num']) {$prvs_thtr_sffx_num='--'.$row['thtr_sffx_num'];} else {$prvs_thtr_sffx_num='';}
      $thtr_prvss[]=$row['thtr_nm'].$prvs_sbthtr_nm.$prvs_thtr_lctn.$prvs_thtr_sffx_num;
    }
    if(!empty($thtr_prvss)) {$prvs_thtr_list=html(implode(',,', $thtr_prvss));} else {$prvs_thtr_list='';}

    $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, COALESCE(thtr_alph, thtr_nm)thtr_alph FROM thtr_alt_adrs
          INNER JOIN thtr ON thtr_sbsqad_id=thtr_id WHERE thtr_prvsad_id='$thtr_id' ORDER BY thtr_cls_dt ASC, thtr_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring "theatre subsequently located" data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['sbthtr_nm']) {$sbsqad_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbsqad_sbthtr_nm='';}
      if($row['thtr_lctn']) {$sbsqad_thtr_lctn='::'.$row['thtr_lctn'];} else {$sbsqad_thtr_lctn='';}
      if($row['thtr_sffx_num']) {$sbsqad_thtr_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbsqad_thtr_sffx_num='';}
      $thtr_sbsqads[]=$row['thtr_nm'].$sbsqad_sbthtr_nm.$sbsqad_thtr_lctn.$sbsqad_thtr_sffx_num;
    }
    if(!empty($thtr_sbsqads)) {$sbsqad_thtr_list=html(implode(',,', $thtr_sbsqads));} else {$sbsqad_thtr_list='';}

    $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, COALESCE(thtr_alph, thtr_nm)thtr_alph
          FROM thtr_alt_adrs INNER JOIN thtr ON thtr_prvsad_id=thtr_id
          WHERE thtr_sbsqad_id='$thtr_id' ORDER BY thtr_cls_dt ASC, thtr_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring "theatre previously located" data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['sbthtr_nm']) {$prvsad_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$prvsad_sbthtr_nm='';}
      if($row['thtr_lctn']) {$prvsad_thtr_lctn='::'.$row['thtr_lctn'];} else {$prvsad_thtr_lctn='';}
      if($row['thtr_sffx_num']) {$prvsad_thtr_sffx_num='--'.$row['thtr_sffx_num'];} else {$prvsad_thtr_sffx_num='';}
      $thtr_prvsads[]=$row['thtr_nm'].$prvsad_sbthtr_nm.$prvsad_thtr_lctn.$prvsad_thtr_sffx_num;
    }
    if(!empty($thtr_prvsads)) {$prvsad_thtr_list=html(implode(',,', $thtr_prvsads));} else {$prvsad_thtr_list='';}

    $textarea='';
    $thtr_id=html($thtr_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thtr_id=cln($_POST['thtr_id']);
    $thtr_nm=trim(cln($_POST['thtr_nm']));
    $sbthtr_nm=trim(cln($_POST['sbthtr_nm']));
    $thtr_lctn=trim(cln($_POST['thtr_lctn']));
    $thtr_sffx_num=trim(cln($_POST['thtr_sffx_num']));
    $thtr_adrs=trim(cln($_POST['thtr_adrs']));
    $lctn_lnk_nm=trim(cln($_POST['lctn_lnk_nm']));
    $thtr_typ_list=trim(cln($_POST['thtr_typ_list']));
    $thtr_comp_list=trim(cln($_POST['thtr_comp_list']));
    $sbthtr_list=trim(cln($_POST['sbthtr_list']));
    $thtr_cpcty=cln($_POST['thtr_cpcty']);
    $thtr_opn_dt=cln($_POST['thtr_opn_dt']);
    if($_POST['thtr_opn_dt_frmt']=='1') {$thtr_opn_dt_frmt='1';} if($_POST['thtr_opn_dt_frmt']=='2') {$thtr_opn_dt_frmt='2';}
    if($_POST['thtr_opn_dt_frmt']=='3') {$thtr_opn_dt_frmt='3';} if($_POST['thtr_opn_dt_frmt']=='4') {$thtr_opn_dt_frmt='4';}
    $thtr_cls_dt=cln($_POST['thtr_cls_dt']);
    if($_POST['thtr_cls_dt_frmt']=='1') {$thtr_cls_dt_frmt='1';} if($_POST['thtr_cls_dt_frmt']=='2') {$thtr_cls_dt_frmt='2';}
    if($_POST['thtr_cls_dt_frmt']=='3') {$thtr_cls_dt_frmt='3';} if($_POST['thtr_cls_dt_frmt']=='4') {$thtr_cls_dt_frmt='4';}
    if(isset($_POST['thtr_clsd'])) {$thtr_clsd='1';} else {$thtr_clsd='0';}
    $thtr_nm_frm_dt=cln($_POST['thtr_nm_frm_dt']);
    if($_POST['thtr_nm_frm_dt_frmt']=='1') {$thtr_nm_frm_dt_frmt='1';} if($_POST['thtr_nm_frm_dt_frmt']=='2') {$thtr_nm_frm_dt_frmt='2';}
    if($_POST['thtr_nm_frm_dt_frmt']=='3') {$thtr_nm_frm_dt_frmt='3';} if($_POST['thtr_nm_frm_dt_frmt']=='4') {$thtr_nm_frm_dt_frmt='4';}
    $thtr_nm_exp_dt=cln($_POST['thtr_nm_exp_dt']);
    if($_POST['thtr_nm_exp_dt_frmt']=='1') {$thtr_nm_exp_dt_frmt='1';} if($_POST['thtr_nm_exp_dt_frmt']=='2') {$thtr_nm_exp_dt_frmt='2';}
    if($_POST['thtr_nm_exp_dt_frmt']=='3') {$thtr_nm_exp_dt_frmt='3';} if($_POST['thtr_nm_exp_dt_frmt']=='4') {$thtr_nm_exp_dt_frmt='4';}
    if(isset($_POST['thtr_nm_exp'])) {$thtr_nm_exp='1';} else {$thtr_nm_exp='0';}
    $sbsq_thtr_list=cln($_POST['sbsq_thtr_list']);
    $prvs_thtr_list=cln($_POST['prvs_thtr_list']);
    $sbsqad_thtr_list=cln($_POST['sbsqad_thtr_list']);
    $prvsad_thtr_list=cln($_POST['prvsad_thtr_list']);
    if(isset($_POST['thtr_tr_ov'])) {$thtr_tr_ov='1';} else {$thtr_tr_ov='0';}

    $thtr_session=$_POST['thtr_nm'];
    $thtr_dsply=$thtr_nm;

    $errors=array();

    if(!preg_match('/\S+/', $thtr_nm)) {$errors['thtr_nm']='**You must enter a theatre name.**';}
    elseif(preg_match('/--/', $thtr_nm) || preg_match('/;;/', $thtr_nm) || preg_match('/::/', $thtr_nm) || preg_match('/,,/', $thtr_nm)) {$errors['thtr_nm']='</br>**Theatre name cannot include any of the following: [--], [;;], [::], [,,].**';}

    if(!$thtr_tr_ov)
    {
      if(preg_match('/--/', $sbthtr_nm) || preg_match('/::/', $sbthtr_nm) || preg_match('/;;/', $sbthtr_nm)) {$errors['sbthtr_nm']='</br>**Subtheatre name cannot include a double hyphen [--], a double colon [::] or a double semicolon [;;].**';}
      elseif(preg_match('/\S+/', $sbthtr_nm)) {$thtr_dsply .= ': '.$sbthtr_nm; $thtr_session .= ': '.$_POST['sbthtr_nm'];}

      if(preg_match('/--/', $thtr_lctn) || preg_match('/::/', $thtr_lctn)) {$errors['thtr_lctn_nm']='</br>**Theatre location cannot include a double hyphen [--] or a double colon [::].**';}
      elseif(preg_match('/\S+/', $thtr_lctn)) {$thtr_dsply .= ' ('.$thtr_lctn.')'; $thtr_session .= ' ('.$_POST['thtr_lctn'].')';}
    }

    if(preg_match('/^[0]*$/', $thtr_sffx_num) || !$thtr_sffx_num) {$thtr_sffx_num='0'; $thtr_sffx_rmn='';}
    elseif(preg_match('/^[1-9][0-9]{0,1}$/', $thtr_sffx_num)) {$thtr_sffx_rmn=' ('.romannumeral($thtr_sffx_num).')'; $thtr_session .= ' ('.romannumeral($_POST['thtr_sffx_num']).')';}
    else {$errors['thtr_sffx']='**The suffix must be a valid integer between 1 and 99 (with no leading 0) or left blank (or as 0).**'; $thtr_sffx_rmn='';}

    $thtr_fll_nm=$thtr_dsply;
    $thtr_url=generateurl($thtr_fll_nm.$thtr_sffx_rmn);
    if(strlen($thtr_fll_nm)>255 || strlen($thtr_url)>255) {$errors['thtr_excss_lngth']='</br>**Theatre full name and its URL are allowed a maximum of 255 characters each.**';}

    $thtr_alph=alph($thtr_nm);

    if(count($errors)==0)
    {
      $sql="SELECT thtr_id, thtr_fll_nm, thtr_sffx_num FROM thtr WHERE thtr_url='$thtr_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing theatre URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['thtr_id']!==$thtr_id)
      {
        if($row['thtr_sffx_num']) {$thtr_sffx_rmn=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$thtr_sffx_rmn='';}
        $errors['thtr_url']='</br>**Duplicate URL exists for: '.html($row['thtr_fll_nm'].$thtr_sffx_rmn).'. You must keep the original name or assign a theatre/tour type name without an existing URL.**';
      }
    }

    $prd_tr_ov_assocs=0; $non_prd_tr_ov_assocs=0;
    $sql="SELECT prd_tr FROM prd WHERE thtrid='$thtr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for associations with productions (that are tour overviews or otherwise): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {if($row['prd_tr']=='2') {$prd_tr_ov_assocs++;} else {$non_prd_tr_ov_assocs++;}}
    if(!$thtr_tr_ov && $prd_tr_ov_assocs>0) {$errors['prd_tr_ov_assocs']='</br>**Associations exist with productions that are tour overviews and so this must first dissociate or be a tour overview.**';}
    if($thtr_tr_ov && $non_prd_tr_ov_assocs>0) {$errors['non_prd_tr_ov_assocs']='</br>**Associations exist with productions that are not tour overviews and so this must first dissociate or not be a tour overview.**';}

    if(!$thtr_tr_ov)
    {
      if(preg_match('/\S+/', $thtr_adrs))
      {if(strlen($thtr_adrs)>255) {$errors['adrs_excss_lngth']='</br>**Address is allowed a maximum of 255 characters.**';}}

      if($thtr_cpcty)
      {
        if(!preg_match('/^[1-9][0-9]{0,3}$/', $thtr_cpcty))
        {$errors['thtr_cpcty']='**Field must be left empty or be comprised of numbers only (with no leading 0).**';}
      }
      else
      {$thtr_cpcty=NULL;}

      if($thtr_opn_dt)
      {
        if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $thtr_opn_dt))
        {$errors['thtr_opn_dt']='**You must enter a valid opening date in the prescribed format or leave empty.**'; $thtr_opn_dt=NULL;}
        else
        {
          list($thtr_opn_dt_YYYY, $thtr_opn_dt_MM, $thtr_opn_dt_DD)=explode('-', $thtr_opn_dt);
          if(!checkdate((int)$thtr_opn_dt_MM, (int)$thtr_opn_dt_DD, (int)$thtr_opn_dt_YYYY))
          {$errors['thtr_opn_dt']='**You must enter a valid opening date or leave empty.**'; $thtr_opn_dt=NULL;}
        }
      }
      else
      {$thtr_opn_dt=NULL;}

      if($thtr_cls_dt)
      {
        if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $thtr_cls_dt)) {$errors['thtr_cls_dt']='**You must enter a valid closing date in the prescribed format or leave empty.**'; $thtr_cls_dt=NULL;}
        else
        {
          date_default_timezone_set('Europe/London'); list($thtr_cls_dt_YYYY, $thtr_cls_dt_MM, $thtr_cls_dt_DD)=explode('-', $thtr_cls_dt);
          if(!checkdate((int)$thtr_cls_dt_MM, (int)$thtr_cls_dt_DD, (int)$thtr_cls_dt_YYYY)) {$errors['thtr_cls_dt']='**You must enter a valid closing date or leave empty.**'; $thtr_cls_dt=NULL;}
          else {if(strtotime($thtr_cls_dt) <= time()) {$thtr_clsd='1';} elseif(strtotime($thtr_cls_dt)>time() && $thtr_clsd) {$errors['thtr_cls_dt_thtr_clsd']='**You cannot check the theatre as closed and set the closing date as a future date.**';}}
        }
      }
      else {$thtr_cls_dt=NULL;}

      if($thtr_opn_dt && $thtr_cls_dt && $thtr_opn_dt>$thtr_cls_dt) {$errors['thtr_opn_dt']='**Must be earlier than the closing date.**'; $errors['thtr_cls_dt']='**Must be later than the opening date.**';}

      if($thtr_nm_frm_dt)
      {
        if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $thtr_nm_frm_dt)) {$errors['thtr_nm_frm_dt']='**You must enter a valid name start date in the prescribed format or leave empty.**'; $thtr_nm_frm_dt=NULL;}
        else
        {
          list($thtr_nm_frm_dt_YYYY, $thtr_nm_frm_dt_MM, $thtr_nm_frm_dt_DD)=explode('-', $thtr_nm_frm_dt);
          if(!checkdate((int)$thtr_nm_frm_dt_MM, (int)$thtr_nm_frm_dt_DD, (int)$thtr_nm_frm_dt_YYYY)) {$errors['thtr_nm_frm_dt']='**You must enter a valid name start date or leave empty.**'; $thtr_nm_frm_dt=NULL;}
        }
      }
      else {$thtr_nm_frm_dt=NULL;}

      if($thtr_nm_exp_dt)
      {
        if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $thtr_nm_exp_dt)) {$errors['thtr_nm_exp_dt']='**You must enter a valid name expiry date in the prescribed format or leave empty.**'; $thtr_nm_exp_dt=NULL;}
        else
        {
          date_default_timezone_set('Europe/London'); list($thtr_nm_exp_dt_YYYY, $thtr_nm_exp_dt_MM, $thtr_nm_exp_dt_DD)=explode('-', $thtr_nm_exp_dt);
          if(!checkdate((int)$thtr_nm_exp_dt_MM, (int)$thtr_nm_exp_dt_DD, (int)$thtr_nm_exp_dt_YYYY)) {$errors['thtr_nm_exp_dt']='**You must enter a valid name expiry date or leave empty.**'; $thtr_nm_exp_dt=NULL;}
          elseif(strtotime($thtr_nm_exp_dt)>time() && $thtr_nm_exp) {$errors['thtr_nm_exp_dt_nm_exp']='**You cannot check the theatre name as expired and set the expiry date as a future date.**';}
        }
      }
      else {$thtr_nm_exp_dt=NULL;}

      if($thtr_nm_frm_dt && $thtr_nm_exp_dt && $thtr_nm_frm_dt>$thtr_nm_exp_dt) {$errors['thtr_nm_frm_dt']='**Must be earlier than the name start date.**'; $errors['thtr_nm_exp_dt']='**Must be later than the name expiry date.**';}

      if($thtr_nm_frm_dt)
      {
        if($thtr_opn_dt && !$thtr_cls_dt && $thtr_opn_dt>$thtr_nm_frm_dt) {$errors['thtr_opn_nm_frm_dt_mtch']='**Must not be earlier than the opening date.**';}
        elseif(!$thtr_opn_dt && $thtr_cls_dt && $thtr_cls_dt<$thtr_nm_frm_dt) {$errors['thtr_opn_nm_frm_dt_mtch']='**Must not be later than the closing date.**';}
        elseif($thtr_opn_dt && $thtr_cls_dt && ($thtr_opn_dt>$thtr_nm_frm_dt || $thtr_cls_dt<$thtr_nm_frm_dt)) {$errors['thtr_opn_nm_frm_dt_mtch']='**Must not be earlier than the opening date or later than the closing date.**';}
      }

      if($thtr_nm_exp_dt)
      {
        if($thtr_opn_dt && !$thtr_cls_dt && $thtr_opn_dt>$thtr_nm_exp_dt) {$errors['thtr_cls_nm_exp_dt_mtch']='**Must not be earlier than the opening date.**';}
        elseif(!$thtr_opn_dt && $thtr_cls_dt && $thtr_cls_dt<$thtr_nm_exp_dt) {$errors['thtr_cls_nm_exp_dt_mtch']='**Must not be later than the closing date.**';}
        elseif($thtr_opn_dt && $thtr_cls_dt && ($thtr_opn_dt>$thtr_nm_exp_dt || $thtr_cls_dt<$thtr_nm_exp_dt)) {$errors['thtr_cls_nm_exp_dt_mtch']='**Must not be earlier than the opening date later than the closing date.**';}
      }

      if($thtr_clsd) {$sbthtr_clsd='1';} else {$sbthtr_clsd='0';}

      if(preg_match('/\S+/', $lctn_lnk_nm))
      {
        $lctn_lnk=$lctn_lnk_nm;
        $lctn_lnk_pipe_excss_err_arr=array(); $lctn_lnk_pipe_err_arr=array(); $lctn_lnk_alt_empty_err_arr=array();
        $lctn_lnk_alt_hyphn_excss_err_arr=array(); $lctn_lnk_alt_sffx_err_arr=array(); $lctn_lnk_alt_hyphn_err_arr=array();
        $lctn_lnk_alt_dplct_arr=array(); $lctn_lnk_alt_url_err_arr=array(); $lctn_lnk_alt_err_arr=array();
        $lctn_lnk_alt_fctn_err_arr=array(); $lctn_lnk_alt_no_assocs=array();

        $lctn_lnk_errors=0;

        if(substr_count($lctn_lnk, '||')>1) {$lctn_lnk_errors++; $lctn_lnk_pipe_excss_err_arr[]=$lctn_lnk; $lctn_lnk_alt_list=''; $errors['lctn_lnk_pipe_excss']='</br>**You may only use [||] once per location for alternate location assignation. Please amend: '.html(implode(' / ', $lctn_lnk_pipe_excss_err_arr)).'.**';}
        elseif(preg_match('/\S+.*\|\|.*\S+/', $lctn_lnk))
        {
          list($lctn_lnk, $lctn_lnk_alt_list)=explode('||', $lctn_lnk);
          $lctn_lnk=trim($lctn_lnk); $lctn_lnk_alt_list=trim($lctn_lnk_alt_list);
        }
        elseif(substr_count($lctn_lnk, '||')==1) {$lctn_lnk_errors++; $lctn_lnk_pipe_err_arr[]=$lctn_lnk; $lctn_lnk_alt_list=''; $errors['lctn_lnk_pipe']='</br>**Alternate location assignation must use [||] in the correct format. Please amend: '.html(implode(' / ', $lctn_lnk_pipe_err_arr)).'.**';}
        else {$lctn_lnk_alt_list='';}

        if(substr_count($lctn_lnk, '--')>1)
        {
          $lctn_lnk_errors++; $lctn_lnk_sffx_num='0';
          $errors['lctn_lnk_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per location (link).**';
        }
        elseif(preg_match('/^\S+.*--.+$/', $lctn_lnk))
        {
          list($lctn_lnk, $lctn_lnk_sffx_num)=explode('--', $lctn_lnk);
          $lctn_lnk=trim($lctn_lnk); $lctn_lnk_sffx_num=trim($lctn_lnk_sffx_num);

          if(!preg_match('/^[1-9][0-9]{0,1}$/', $lctn_lnk_sffx_num))
          {
            $lctn_lnk_errors++; $lctn_lnk_sffx_num='0';
            $errors['lctn_lnk_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)).**';
          }
        }
        elseif(substr_count($lctn_lnk, '--')==1)
        {$lctn_lnk_errors++; $lctn_lnk_sffx_num='0'; $errors['lctn_lnk_hyphn']='</br>**Suffix assignation must use [--] in the correct format.**';}
        else
        {$lctn_lnk_sffx_num='0';}

        if($lctn_lnk_sffx_num) {$lctn_lnk_sffx_rmn=' ('.romannumeral($lctn_lnk_sffx_num).')';} else {$lctn_lnk_sffx_rmn='';}

        $lctn_lnk_url=generateurl($lctn_lnk.$lctn_lnk_sffx_rmn);

        if(strlen($lctn_lnk)>255 || strlen($lctn_lnk_url)>255)
        {$lctn_lnk_errors++; $errors['lctn_lnk_nm_excss_lngth']='</br>**Location link and its URL are allowed a maximum of 255 characters each.**';}

        if($lctn_lnk_errors==0)
        {
          $sql= "SELECT lctn_nm, lctn_sffx_num
                FROM lctn
                WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$lctn_lnk' AND lctn_sffx_num='$lctn_lnk_sffx_num')
                AND lctn_url='$lctn_lnk_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existing location (link) URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          $row=mysqli_fetch_array($result);
          if(mysqli_num_rows($result)>0)
          {
            if($row['lctn_sffx_num']) {$lctn_lnk_url_err_sffx_num='--'.$row['lctn_sffx_num'];} else {$lctn_lnk_url_err_sffx_num='';}
            $errors['lctn_lnk_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html($row['lctn_nm'].$lctn_lnk_url_err_sffx_num).'?**';
          }
          else
          {
            $sql="SELECT lctn_id, lctn_fctn FROM lctn WHERE lctn_url='$lctn_lnk_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking whether location link is a fictional location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            $row=mysqli_fetch_array($result);
            if($row['lctn_fctn']) {$errors['lctn_lnk_nm']='</br>**Location link cannot be a fictional location.**';}
            else
            {
              if($lctn_lnk_alt_list)
              {
                if(mysqli_num_rows($result)==0) {$errors['lctn_lnk_alt_list']='</br>**The given location does not yet exist (and therefore cannot be assigned alternate locations).**';}
                else
                {
                  $lctn_id=$row['lctn_id'];

                  $lctn_lnk_alts=explode('>>', $lctn_lnk_alt_list);
                  if(count($lctn_lnk_alts)>250)
                  {$errors['lctn_lnk_alt_array_excss']='**Maximum of 250 locations per alternate location array allowed.**';}
                  else
                  {
                    $lctn_lnk_alt_dplct_arr=array();
                    foreach($lctn_lnk_alts as $lctn_lnk_alt)
                    {
                      $lctn_lnk_alt=trim($lctn_lnk_alt);
                      if(!preg_match('/\S+/', $lctn_lnk_alt))
                      {
                        $lctn_lnk_alt_empty_err_arr[]=$lctn_lnk_alt;
                        if(count($lctn_lnk_alt_empty_err_arr)==1) {$errors['lctn_lnk_alt_empty']='</br>**There is 1 empty entry in an alternate location array (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                        else {$errors['lctn_lnk_alt_empty']='</br>**There are '.count($lctn_lnk_alt_empty_err_arr).' empty entries in alternate location arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                      }
                      else
                      {
                        $lctn_lnk_alt_errors=0;

                        if(substr_count($lctn_lnk_alt, '--')>1)
                        {
                          $lctn_lnk_alt_errors++; $lctn_lnk_alt_sffx_num='0'; $lctn_lnk_alt_hyphn_excss_err_arr[]=$lctn_lnk_alt;
                          $errors['lctn_lnk_alt_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per alternate location. Please amend: '.html(implode(' / ', $lctn_lnk_alt_hyphn_excss_err_arr)).'.**';
                        }
                        elseif(preg_match('/^\S+.*--.+$/', $lctn_lnk_alt))
                        {
                          list($lctn_lnk_alt_no_sffx, $lctn_lnk_alt_sffx_num)=explode('--', $lctn_lnk_alt);
                          $lctn_lnk_alt_no_sffx=trim($lctn_lnk_alt_no_sffx); $lctn_lnk_alt_sffx_num=trim($lctn_lnk_alt_sffx_num);

                          if(!preg_match('/^[1-9][0-9]{0,1}$/', $lctn_lnk_alt_sffx_num))
                          {
                            $lctn_lnk_alt_errors++; $lctn_lnk_alt_sffx_num='0'; $lctn_lnk_alt_sffx_err_arr[]=$lctn_lnk_alt;
                            $errors['lctn_lnk_alt_sffx']='</br>**The suffix (for alternate locations) must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $lctn_lnk_alt_sffx_err_arr)).'**';
                          }
                          $lctn_lnk_alt=$lctn_lnk_alt_no_sffx;
                        }
                        elseif(substr_count($lctn_lnk_alt, '--')==1)
                        {$lctn_lnk_alt_errors++; $lctn_lnk_alt_sffx_num='0'; $lctn_lnk_alt_hyphn_err_arr[]=$lctn_lnk_alt;
                        $errors['lctn_lnk_alt_hyphn']='</br>**Alternate location suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $lctn_lnk_alt_hyphn_err_arr)).'**';}
                        else
                        {$lctn_lnk_alt_sffx_num='0';}

                        if($lctn_lnk_alt_sffx_num) {$lctn_lnk_alt_sffx_rmn=' ('.romannumeral($lctn_lnk_alt_sffx_num).')';} else {$lctn_lnk_alt_sffx_rmn='';}

                        $lctn_lnk_alt_url=generateurl($lctn_lnk_alt.$lctn_lnk_alt_sffx_rmn);
                        $lctn_lnk_alt_dplct_arr[]=$lctn_lnk_alt_url;
                        if(count(array_unique($lctn_lnk_alt_dplct_arr))<count($lctn_lnk_alt_dplct_arr))
                        {$errors['lctn_lnk_alt_dplct']='</br>**There are entries within alternate location arrays that create duplicate location URLs.**';}

                        if(strlen($lctn_lnk_alt)>255 || strlen($lctn_lnk_alt_url)>255)
                        {$lctn_lnk_alt_errors++; $errors['lctn_lnk_alt_excss_lngth']='</br>**Alternate location name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                        if($lctn_lnk_alt_errors==0)
                        {
                          $lctn_lnk_alt_cln=cln($lctn_lnk_alt);
                          $lctn_lnk_alt_sffx_num_cln=cln($lctn_lnk_alt_sffx_num);
                          $lctn_lnk_alt_url_cln=cln($lctn_lnk_alt_url);

                          $sql= "SELECT lctn_nm FROM lctn
                                WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$lctn_lnk_alt_cln' AND lctn_sffx_num='$lctn_lnk_alt_sffx_num_cln')
                                AND lctn_url='$lctn_lnk_alt_url_cln'";
                          $result=mysqli_query($link, $sql);
                          if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                          $row=mysqli_fetch_array($result);
                          if(mysqli_num_rows($result)>0)
                          {
                            $lctn_lnk_alt_url_err_arr[]=$row['lctn_nm'];
                            if(count($lctn_lnk_alt_url_err_arr)==1) {$errors['lctn_lnk_alt_url']='</br>**Duplicate location URL exists (from alternate location arrays). Did you mean to type: '.html(implode(' / ', $lctn_lnk_alt_url_err_arr)).'?**';}
                            else {$errors['lctn_lnk_alt_url']='</br>**Duplicate location URLs exist (from alternate location arrays). Did you mean to type: '.html(implode(' / ', $lctn_lnk_alt_url_err_arr)).'?**';}
                          }
                          else
                          {
                            $sql="SELECT lctn_id, lctn_fctn FROM lctn WHERE lctn_url='$lctn_lnk_alt_url_cln'";
                            $result=mysqli_query($link, $sql);
                            if(!$result) {$error='Error checking for existence of location (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                            $row=mysqli_fetch_array($result);
                            if(mysqli_num_rows($result)==0) {$lctn_lnk_alt_err_arr[]=$lctn_lnk_alt.$lctn_lnk_alt_sffx_rmn; $errors['lctn_lnk_alt']='</br>**The following locations from alternate location arrays do not yet exist (and can therefore not be assigned): '.html(implode(' / ', $lctn_lnk_alt_err_arr)).'.';}
                            elseif($row['lctn_fctn']) {$lctn_lnk_alt_fctn_err_arr[]=$lctn_lnk_alt.$lctn_lnk_alt_sffx_rmn; $errors['lctn_lnk_alt_fctn']='</br>**The following locations from alternate location arrays are fictional (and can therefore not be assigned): '.html(implode(' / ', $lctn_lnk_alt_fctn_err_arr)).'.';}
                            else
                            {
                              $lctn_alt_id=$row['lctn_id'];
                              $sql="SELECT 1 FROM rel_lctn WHERE rel_lctn1='$lctn_id' AND rel_lctn2='$lctn_alt_id'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              $row=mysqli_fetch_array($result);
                              if(mysqli_num_rows($result)==0)
                              {
                                $lctn_lnk_alt_no_assocs[]=$lctn_lnk_alt.$lctn_lnk_alt_sffx_rmn;
                                $errors['lctn_lnk_alt_assoc']='</br>**Associations do not exist between this location and its listed alternates. Please amend: '.implode(' / ', $lctn_lnk_alt_no_assocs).'**';
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
        }
      }

      if(preg_match('/\S+/', $thtr_typ_list))
      {
        $thtr_typ_nms=explode(',,', $thtr_typ_list);
        if(count($thtr_typ_nms)>250)
        {$errors['thtr_typ_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $thtr_typ_empty_err_arr=array(); $thtr_typ_dplct_arr=array(); $thtr_typ_url_err_arr=array();
          foreach($thtr_typ_nms as $thtr_typ_nm)
          {
            $thtr_typ_nm=trim($thtr_typ_nm);
            if(!preg_match('/\S+/', $thtr_typ_nm))
            {
              $thtr_typ_empty_err_arr[]=$thtr_typ_nm;
              if(count($thtr_typ_empty_err_arr)==1) {$errors['thtr_typ_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['thtr_typ_empty']='</br>**There are '.count($thtr_typ_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $thtr_typ_url=generateurl($thtr_typ_nm);
              $thtr_typ_dplct_arr[]=$thtr_typ_url;
              if(count(array_unique($thtr_typ_dplct_arr))<count($thtr_typ_dplct_arr)) {$errors['thtr_typ_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($thtr_typ_nm)>255) {$errors['thtr_typ_nm_excss_lngth']='</br>**Theatre type name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              $sql= "SELECT thtr_typ_nm
                    FROM thtr_typ
                    WHERE NOT EXISTS (SELECT 1 FROM thtr_typ WHERE thtr_typ_nm='$thtr_typ_nm')
                    AND thtr_typ_url='$thtr_typ_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing theatre type URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $thtr_typ_url_err_arr[]=$row['thtr_typ_nm'];
                if(count($thtr_typ_url_err_arr)==1)
                {$errors['thtr_typ_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $thtr_typ_url_err_arr)).'?**';}
                else
                {$errors['thtr_typ_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $thtr_typ_url_err_arr)).'?**';}
              }
            }
          }
        }
      }

      if(preg_match('/\S+/', $thtr_comp_list))
      {
        if($thtr_clsd) {$errors['thtr_comp_thtr_clsd']='**This field must be empty if theatre is closed.**';}
        else
        {
          $thtr_comp_nms=explode(',,', $_POST['thtr_comp_list']);
          if(count($thtr_comp_nms)>250) {$errors['thtr_comp_array_excss']='**Maximum of 250 entries allowed.**';}
          else
          {
            $thtr_comp_hyphn_excss_err_arr=array(); $thtr_comp_sffx_err_arr=array(); $thtr_comp_hyphn_err_arr=array();
            $thtr_comp_dplct_arr=array(); $thtr_comp_url_err_arr=array();
            foreach($thtr_comp_nms as $thtr_comp)
            {
              $thtr_comp=trim($thtr_comp);
              if(!preg_match('/\S+/', $thtr_comp))
              {
                $thtr_comp_empty_err_arr[]=$thtr_comp;
                if(count($thtr_comp_empty_err_arr)==1) {$errors['thtr_comp_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                else {$errors['thtr_comp_empty']='</br>**There are '.count($thtr_comp_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              }
              else
              {
                $thtr_comp_err=$thtr_comp; $thtr_comp_errors=0;

                if(substr_count($thtr_comp, '--')>1) {$thtr_comp_errors++; $thtr_comp_sffx_num='0'; $thtr_comp_hyphn_excss_err_arr[]=$thtr_comp_err; $errors['thtr_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per company. Please amend: '.html(implode(' / ', $thtr_comp_hyphn_excss_err_arr)).'.**';}
                elseif(preg_match('/^\S+.*--.+$/', $thtr_comp))
                {
                  list($thtr_comp, $thtr_comp_sffx_num)=explode('--', $thtr_comp); $thtr_comp=trim($thtr_comp); $thtr_comp_sffx_num=trim($thtr_comp_sffx_num);
                  if(!preg_match('/^[1-9][0-9]{0,1}$/', $thtr_comp_sffx_num)) {$thtr_comp_errors++; $thtr_comp_sffx_num='0'; $thtr_comp_sffx_err_arr[]=$thtr_comp_err; $errors['thtr_comp_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $thtr_comp_sffx_err_arr)).'.**';}
                }
                elseif(substr_count($thtr_comp, '--')==1) {$thtr_comp_errors++; $thtr_comp_sffx_num='0'; $thtr_comp_hyphn_err_arr[]=$thtr_comp_err; $errors['thtr_comp_hyphn']='</br>**Suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $thtr_comp_hyphn_err_arr)).'.**';}
                else {$thtr_comp_sffx_num='0';}

                if($thtr_comp_sffx_num) {$thtr_comp_sffx_rmn=' ('.romannumeral($thtr_comp_sffx_num).')';} else {$thtr_comp_sffx_rmn='';}

                $thtr_comp_url=generateurl($thtr_comp.$thtr_comp_sffx_rmn);
                $thtr_comp_dplct_arr[]=$thtr_comp_url;
                if(count(array_unique($thtr_comp_dplct_arr))<count($thtr_comp_dplct_arr)) {$errors['thtr_comp_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}
                if(strlen($thtr_comp)>255 || strlen($thtr_comp_url)>255) {$thtr_comp_errors++; $errors['thtr_comp_nm_excss_lngth']='</br>**Company name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                if($thtr_comp_errors==0)
                {
                  $thtr_comp_cln=cln($thtr_comp);
                  $thtr_comp_sffx_num_cln=cln($thtr_comp_sffx_num);
                  $thtr_comp_url_cln=cln($thtr_comp_url);

                  $sql= "SELECT comp_nm, comp_sffx_num
                        FROM comp
                        WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$thtr_comp_cln' AND comp_sffx_num='$thtr_comp_sffx_num_cln')
                        AND comp_url='$thtr_comp_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    if($row['comp_sffx_num']) {$thtr_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];} else {$thtr_comp_url_error_sffx_dsply='';}
                    $thtr_comp_url_err_arr[]=html($row['comp_nm'].$thtr_comp_url_error_sffx_dsply);
                    if(count($thtr_comp_url_err_arr)==1)
                    {$errors['thtr_comp_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $thtr_comp_url_err_arr)).'?**';}
                    else {$errors['thtr_comp_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $thtr_comp_url_err_arr)).'?**';}
                  }
                }
              }
            }
          }
        }
      }

      if(preg_match('/\S+/', $sbthtr_list))
      {
        if(preg_match('/\S+/', $sbthtr_nm)) {$errors['sbthtr_list_sbthtr_nm']='</br>**If assigning subtheatres to this theatre, then it must not include a subtheatre as part of its own name.**';}
        if(preg_match('/\S+/', $thtr_cpcty)) {$errors['sbthtr_list_thtr_cpcty']='</br>**This field must be left empty if assigning subtheatres to this theatre.**';}

        $sbthtr_nms=explode(',,', $_POST['sbthtr_list']);
        if(count($sbthtr_nms)>250) {$errors['sbthtr_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $sbthtr_empty_err_arr=array(); $sbthtr_hyphn_excss_err_arr=array(); $sbthtr_sffx_err_arr=array();
          $sbthtr_hyphn_err_arr=array(); $sbthtr_cln_excss_err_arr=array(); $sbthtr_cln_err_arr=array();
          $sbthtr_thtr_lctn_mtch_err_arr=array(); $sbthtr_smcln_excss_err_arr=array(); $sbthtr_thtr_nm_mtch_err_arr=array();
          $sbthtr_smcln_err_arr=array(); $sbthtr_dplct_arr=array(); $sbthtr_cmpstn_err_arr=array();
          $sbthtr_url_err_arr=array(); $sbthtr_dt_mtch_err_arr=array(); $sbthtr_clsd_mtch_err_arr=array();
          $sbthtr_assoc_err_arr=array();
          foreach($sbthtr_nms as $sbthtr)
          {
            $sbthtr=trim($sbthtr);
            if(!preg_match('/\S+/', $sbthtr))
            {
              $sbthtr_empty_err_arr[]=$sbthtr;
              if(count($sbthtr_empty_err_arr)==1) {$errors['sbthtr_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['sbthtr_empty']='</br>**There are '.count($sbthtr_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $sbthtr_err=$sbthtr; $sbthtr_errors=0;

              if(substr_count($sbthtr, '--')>1) {$sbthtr_errors++; $sbthtr_sffx_num='0'; $sbthtr_hyphn_excss_err_arr[]=$sbthtr_err; $errors['sbthtr_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per subtheatre. Please amend: '.html(implode(' / ', $sbthtr_hyphn_excss_err_arr)).'.**';}
              elseif(preg_match('/^\S+.*--.+$/', $sbthtr)) {list($sbthtr_no_sffx, $sbthtr_sffx_num)=explode('--', $sbthtr); $sbthtr_no_sffx=trim($sbthtr_no_sffx); $sbthtr_sffx_num=trim($sbthtr_sffx_num); if(!preg_match('/^[1-9][0-9]{0,1}$/', $sbthtr_sffx_num)) {$sbthtr_errors++; $sbthtr_sffx_num='0'; $sbthtr_sffx_err_arr[]=$sbthtr_err; $errors['sbthtr_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $sbthtr_sffx_err_arr)).'**';} $sbthtr=$sbthtr_no_sffx;}
              elseif(substr_count($sbthtr, '--')==1) {$sbthtr_errors++; $sbthtr_sffx_num='0'; $sbthtr_hyphn_err_arr[]=$sbthtr_err; $errors['sbthtr_hyphn']='</br>**Suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $sbthtr_hyphn_err_arr)).'**';}
              else {$sbthtr_sffx_num='0';}

              if($sbthtr_sffx_num) {$sbthtr_sffx_rmn=' ('.romannumeral($sbthtr_sffx_num).')';} else {$sbthtr_sffx_rmn='';}

              if(substr_count($sbthtr, '::')>1) {$sbthtr_errors++; $sbthtr_lctn=''; $sbthtr_lctn_dsply=''; $sbthtr_cln_excss_err_arr[]=$sbthtr_err; $errors['sbthtr_cln_excss']='</br>**You may only use [::] once per subtheatre-location coupling. Please amend: '.html(implode(' / ', $sbthtr_cln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*::.*\S+/', $sbthtr)) {list($sbthtr, $sbthtr_lctn)=explode('::', $sbthtr); $sbthtr=trim($sbthtr); $sbthtr_lctn=trim($sbthtr_lctn); $sbthtr_lctn_dsply=' ('.$sbthtr_lctn.')';}
              elseif(substr_count($sbthtr, '::')==1) {$sbthtr_errors++; $sbthtr_lctn=''; $sbthtr_lctn_dsply=''; $sbthtr_cln_err_arr[]=$sbthtr_err; $errors['sbthtr_cln']='</br>**Location assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $sbthtr_cln_err_arr)).'**';}
              else {$sbthtr_lctn=''; $sbthtr_lctn_dsply='';}
              if($sbthtr_lctn!==$thtr_lctn) {$sbthtr_thtr_lctn_mtch_err_arr[]=$sbthtr_err; $errors['sbthtr_thtr_lctn_mtch']='</br>**Subtheatres must match the location of the main theatre. Please amend: '.html(implode(' / ', $sbthtr_thtr_lctn_mtch_err_arr)).'.**';}

              if(substr_count($sbthtr, ';;')>1) {$sbthtr_errors++; $sbsbthtr_nm=''; $sbsbthtr_nm_dsply=''; $sbthtr_smcln_excss_err_arr[]=$sbthtr_err; $errors['sbthtr_smcln_excss']='</br>**You may only use [;;] once per theatre-subtheatre coupling. Please amend: '.html(implode(' / ', $sbthtr_smcln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*;;.*\S+/', $sbthtr)) {list($sbthtr, $sbsbthtr_nm)=explode(';;', $sbthtr); $sbthtr=trim($sbthtr); $sbsbthtr_nm=trim($sbsbthtr_nm); $sbsbthtr_nm_dsply=': '.$sbsbthtr_nm; if($sbthtr!==$thtr_nm) {$sbthtr_thtr_nm_mtch_err_arr[]=$sbthtr_err; $errors['sbthtr_thtr_nm_mtch']='</br>**Subtheatres must match the name of the main theatre. Please amend: '.html(implode(' / ', $sbthtr_thtr_nm_mtch_err_arr)).'.**';}}
              else {$sbthtr_errors++; $sbsbthtr_nm=''; $sbsbthtr_nm_dsply=''; $sbthtr_smcln_err_arr[]=$sbthtr_err; $errors['sbthtr_smcln']='</br>**You must list theatres that include subtheatres and therefore assign a subtheatre to the following using [;;]: '.html(implode(' / ', $sbthtr_smcln_err_arr)).'.**';}

              $sbthtr_fll_nm=$sbthtr.$sbsbthtr_nm_dsply.$sbthtr_lctn_dsply;
              $sbthtr_url=generateurl($sbthtr_fll_nm.$sbthtr_sffx_rmn);

              $sbthtr_dplct_arr[]=$sbthtr_url;
              if(count(array_unique($sbthtr_dplct_arr))<count($sbthtr_dplct_arr)) {$errors['sbthtr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}
              if(strlen($sbthtr_fll_nm)>255 || strlen($sbthtr_url)>255) {$sbthtr_errors++; $errors['sbthtr_excss_lngth']='</br>**Theatre name and its URL are allowed a maximum of 255 characters each.**';}

              if($sbthtr_errors==0)
              {
                $sbthtr_nm_cln=cln($sbthtr);
                $sbsbthtr_nm_cln=cln($sbsbthtr_nm);
                $sbthtr_lctn_cln=cln($sbthtr_lctn);
                $sbthtr_sffx_num_cln=cln($sbthtr_sffx_num);
                $sbthtr_fll_nm_cln=cln($sbthtr_fll_nm);
                $sbthtr_url_cln=cln($sbthtr_url);

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_nm='$sbthtr_nm_cln' AND sbthtr_nm='$sbsbthtr_nm_cln' AND thtr_lctn='$sbthtr_lctn_cln')
                      AND thtr_fll_nm='$sbthtr_fll_nm_cln' AND thtr_sffx_num='$sbthtr_sffx_num_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for theatre with assigned components: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  $sbthtr_errors++;
                  if($row['sbthtr_nm']) {$sbthtr_cmpstn_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbthtr_cmpstn_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$sbthtr_cmpstn_error_thtr_lctn=':  :'.$row['thtr_lctn'];} else {$sbthtr_cmpstn_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$sbthtr_cmpstn_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbthtr_cmpstn_error_sffx_num='';}
                  $sbthtr_cmpstn_err_arr[]=$row['thtr_nm'].$sbthtr_cmpstn_error_sbthtr_nm.$sbthtr_cmpstn_error_thtr_lctn.$sbthtr_cmpstn_error_sffx_num;
                  if(count($sbthtr_cmpstn_err_arr)==1)
                  {$errors['sbthtr_cmpstn']='</br>**This theatre does not adhere to its correct component assignation: '.html(implode(' / ', $sbthtr_cmpstn_err_arr)).'.**';}
                  else {$errors['sbthtr_cmpstn']='</br>**These theatres does not adhere to their correct component assignation: '.html(implode(' / ', $sbthtr_cmpstn_err_arr)).'.**';}
                }

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_fll_nm='$sbthtr_fll_nm_cln' AND thtr_sffx_num='$sbthtr_sffx_num_cln')
                      AND thtr_url='$sbthtr_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing theatre URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  $sbthtr_errors++;
                  if($row['sbthtr_nm']) {$sbthtr_url_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbthtr_url_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$sbthtr_url_error_thtr_lctn='::'.$row['thtr_lctn'];} else {$sbthtr_url_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$sbthtr_url_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbthtr_url_error_sffx_num='';}
                  $sbthtr_url_err_arr[]=$row['thtr_nm'].$sbthtr_url_error_sbthtr_nm.$sbthtr_url_error_thtr_lctn.$sbthtr_url_error_sffx_num;
                  if(count($sbthtr_url_err_arr)==1)
                  {$errors['sbthtr_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $sbthtr_url_err_arr)).'?**';}
                  else {$errors['sbthtr_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $sbthtr_url_err_arr)).'?**';}
                }
                else
                {
                  $sql= "SELECT thtr_id, thtr_fll_nm, thtr_sffx_num, thtr_url, thtr_opn_dt, thtr_cls_dt, thtr_clsd
                        FROM thtr
                        WHERE thtr_url='$sbthtr_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing theatre URL (against subtheatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $sbthtr_id=$row['thtr_id'];
                    if($row['thtr_sffx_num']) {$sbthtr_sffx_rmn_url_lnk=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$sbthtr_sffx_rmn_url_lnk='';}
                    $sbthtr_url_lnk='<a href="/theatre/'.html($row['thtr_url']).'" target="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm'].$sbthtr_sffx_rmn_url_lnk).'</a>';
                    if(($row['thtr_opn_dt'] && $thtr_opn_dt && $row['thtr_opn_dt']<$thtr_opn_dt) || ($row['thtr_cls_dt'] && $thtr_opn_dt && $row['thtr_cls_dt']<$thtr_opn_dt) || ($row['thtr_opn_dt'] && $thtr_cls_dt && $row['thtr_opn_dt']>$thtr_cls_dt) || ($row['thtr_cls_dt'] && $thtr_cls_dt && $row['thtr_cls_dt']>$thtr_cls_dt))
                    {$sbthtr_dt_mtch_err_arr[]=$sbthtr_url_lnk; $errors['sbthtr_dt_mtch']='</br>**Subtheatres must have opened and closed with the opening and closing dates given for this theatre. Please amend: '.implode(' / ', $sbthtr_dt_mtch_err_arr).'**';}
                    if($thtr_clsd && !$row['thtr_clsd'])
                    {$sbthtr_clsd_mtch_err_arr[]=$sbthtr_url_lnk; $errors['sbthtr_clsd_mtch']='</br>**If theatre is closed then its subtheatres must also be closed. Please amend: '.implode(' / ', $sbthtr_clsd_mtch_err_arr).'**';}
                    else
                    {
                      $sql="SELECT srthtrid FROM thtr WHERE thtr_id='$sbthtr_id' AND srthtrid IS NOT NULL";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error checking for existing associated theatre data (against subtheatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      $row=mysqli_fetch_array($result);
                      if(mysqli_num_rows($result)>0)
                      {if($row['srthtrid']!==$thtr_id) {$sbthtr_assoc_err_arr[]=$sbthtr_url_lnk; $errors['sbthtr_assocs']='</br>**Theatres can only be assigned as subtheatres to one theatre. The following have already been assigned: '.implode(' / ', $sbthtr_assoc_err_arr).'.**';}}
                    }
                  }
                }
              }
            }
          }
        }
      }

      if(preg_match('/\S+/', $sbsq_thtr_list))
      {
        $sbsq_thtr_nms=explode(',,', $_POST['sbsq_thtr_list']);
        if(count($sbsq_thtr_nms)>250) {$errors['sbsq_thtr_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $sbsq_thtr_empty_err_arr=array(); $sbsq_thtr_hyphn_excss_err_arr=array(); $sbsq_thtr_sffx_err_arr=array();
          $sbsq_thtr_hyphn_err_arr=array(); $sbsq_thtr_cln_excss_err_arr=array(); $sbsq_thtr_cln_err_arr=array();
          $sbsq_thtr_lctn_mtch_err_arr=array(); $sbsq_thtr_smcln_excss_err_arr=array(); $sbsq_thtr_sbthtr_err_arr=array();
          $sbsq_thtr_smcln_err_arr=array(); $sbthtr_sbsq_thtr_err_arr=array(); $sbsq_thtr_dplct_arr=array();
          $sbsq_thtr_cmpstn_err_arr=array(); $sbsq_thtr_url_err_arr=array(); $sbsq_thtr_opn_dt_mtch_err_arr=array();
          $sbsq_thtr_cls_dt_mtch_err_arr=array(); $sbsq_thtr_nm_dt_mtch_err_arr=array(); $sbsq_thtr_nm_tr_ov_err_arr=array();
          foreach($sbsq_thtr_nms as $sbsq_thtr_nm)
          {
            $sbsq_thtr_errors=0; $sbsq_thtr_nm=trim($sbsq_thtr_nm);
            if(!preg_match('/\S+/', $sbsq_thtr_nm))
            {
              $sbsq_thtr_empty_err_arr[]=$sbsq_thtr_nm;
              if(count($sbsq_thtr_empty_err_arr)==1) {$errors['sbsq_thtr_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['sbsq_thtr_empty']='</br>**There are '.count($sbsq_thtr_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $sbsq_thtr_nm_err=$sbsq_thtr_nm;

              if(substr_count($sbsq_thtr_nm, '--')>1) {$sbsq_thtr_errors++; $sbsq_thtr_sffx_num='0'; $sbsq_thtr_hyphn_excss_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per theatre. Please amend: '.html(implode(' / ', $sbsq_thtr_hyphn_excss_err_arr)).'.**';}
              elseif(preg_match('/^\S+.*--.+$/', $sbsq_thtr_nm))
              {
                list($sbsq_thtr_no_sffx, $sbsq_thtr_sffx_num)=explode('--', $sbsq_thtr_nm); $sbsq_thtr_no_sffx=trim($sbsq_thtr_no_sffx); $sbsq_thtr_sffx_num=trim($sbsq_thtr_sffx_num);
                if(!preg_match('/^[1-9][0-9]{0,1}$/', $sbsq_thtr_sffx_num)) {$sbsq_thtr_errors++; $sbsq_thtr_sffx_num='0'; $sbsq_thtr_sffx_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $sbsq_thtr_sffx_err_arr)).'**';}
                $sbsq_thtr_nm=$sbsq_thtr_no_sffx;
              }
              elseif(substr_count($sbsq_thtr_nm, '--')==1) {$sbsq_thtr_errors++; $sbsq_thtr_sffx_num='0'; $sbsq_thtr_hyphn_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_hyphn']='</br>**Suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $sbsq_thtr_hyphn_err_arr)).'**';}
              else {$sbsq_thtr_sffx_num='0';}

              if($sbsq_thtr_sffx_num) {$sbsq_thtr_sffx_rmn=' ('.romannumeral($sbsq_thtr_sffx_num).')';} else {$sbsq_thtr_sffx_rmn='';}

              if(substr_count($sbsq_thtr_nm, '::')>1) {$sbsq_thtr_errors++; $sbsq_thtr_lctn=''; $sbsq_thtr_lctn_dsply=''; $sbsq_thtr_cln_excss_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_cln_excss']='</br>**You may only use [::] once per theatre-location coupling. Please amend: '.html(implode(' / ', $sbsq_thtr_cln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*::.*\S+/', $sbsq_thtr_nm)) {list($sbsq_thtr_nm, $sbsq_thtr_lctn)=explode('::', $sbsq_thtr_nm); $sbsq_thtr_nm=trim($sbsq_thtr_nm); $sbsq_thtr_lctn=trim($sbsq_thtr_lctn); $sbsq_thtr_lctn_dsply=' ('.$sbsq_thtr_lctn.')';}
              elseif(substr_count($sbsq_thtr_nm, '::')==1) {$sbsq_thtr_errors++; $sbsq_thtr_lctn=''; $sbsq_thtr_lctn_dsply=''; $sbsq_thtr_cln_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_cln']='</br>**Location assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $sbsq_thtr_cln_err_arr)).'**';}
              else {$sbsq_thtr_lctn=''; $sbsq_thtr_lctn_dsply='';}
              if($sbsq_thtr_lctn!==$thtr_lctn) {$sbsq_thtr_lctn_mtch_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_lctn_mtch']='</br>**Subsequently named theatres must match the location of this theatre. Please amend: '.html(implode(' / ', $sbsq_thtr_lctn_mtch_err_arr)).'.**';}

              if(substr_count($sbsq_thtr_nm, ';;')>1) {$sbsq_thtr_errors++; $sbsq_sbthtr_nm=''; $sbsq_sbthtr_nm_dsply=''; $sbsq_thtr_smcln_excss_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_smcln_excss']='</br>**You may only use [;;] once per theatre-subtheatre coupling. Please amend: '.html(implode(' / ', $sbsq_thtr_smcln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*;;.*\S+/', $sbsq_thtr_nm)) {list($sbsq_thtr_nm, $sbsq_sbthtr_nm)=explode(';;', $sbsq_thtr_nm); $sbsq_thtr_nm=trim($sbsq_thtr_nm); $sbsq_sbthtr_nm=trim($sbsq_sbthtr_nm); $sbsq_sbthtr_nm_dsply=': '.$sbsq_sbthtr_nm;
              if(!$sbthtr_nm) {$sbsq_thtr_sbthtr_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_sbthtr']='</br>**This theatre is not a subtheatre and subsequently named theatres cannot therefore be subtheatres. Please amend: '.html(implode(' / ', $sbsq_thtr_sbthtr_err_arr)).'.**';}
              elseif($thtr_nm!==$sbsq_thtr_nm) {$sbsq_thtr_sbthtr_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_sbthtr']='</br>**This theatre is a subtheatre and subsequently named theatres must match its main theatre name. Please amend: '.html(implode(' / ', $sbsq_thtr_sbthtr_err_arr)).'.**';}}
              elseif(substr_count($sbsq_thtr_nm, ';;')==1) {$sbsq_thtr_errors++; $sbsq_sbthtr_nm=''; $sbsq_sbthtr_nm_dsply=''; $sbsq_thtr_smcln_err_arr[]=$sbsq_thtr_nm_err; $errors['sbsq_thtr_smcln']='</br>**Subtheatre assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $sbsq_thtr_smcln_err_arr)).'**';}
              else {$sbsq_sbthtr_nm=''; $sbsq_sbthtr_nm_dsply='';}
              if(!$sbsq_sbthtr_nm && $sbthtr_nm) {$sbthtr_sbsq_thtr_err_arr[]=$sbsq_thtr_nm_err; $errors['sbthtr_sbsq_thtr']='</br>**This theatre is a subtheatre and subsequently named theatres must therefore be subtheatres. Please amend: '.html(implode(' / ', $sbthtr_sbsq_thtr_err_arr)).'.**';}

              $sbsq_thtr_fll_nm=$sbsq_thtr_nm.$sbsq_sbthtr_nm_dsply.$sbsq_thtr_lctn_dsply;
              $sbsq_thtr_url=generateurl($sbsq_thtr_fll_nm.$sbsq_thtr_sffx_rmn);

              $sbsq_thtr_dplct_arr[]=$sbsq_thtr_url;
              if(count(array_unique($sbsq_thtr_dplct_arr))<count($sbsq_thtr_dplct_arr)) {$errors['sbsq_thtr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($sbsq_thtr_fll_nm)>255 || strlen($sbsq_thtr_url)>255) {$sbsq_thtr_errors++; $errors['sbsq_thtr_excss_lngth']='</br>**Theatre name and its URL are allowed a maximum of 255 characters each.**';}

              if($sbsq_thtr_errors==0)
              {
                $sbsq_thtr_nm_cln=cln($sbsq_thtr_nm);
                $sbsq_sbthtr_nm_cln=cln($sbsq_sbthtr_nm);
                $sbsq_thtr_lctn_cln=cln($sbsq_thtr_lctn);
                $sbsq_thtr_sffx_num_cln=cln($sbsq_thtr_sffx_num);
                $sbsq_thtr_fll_nm_cln=cln($sbsq_thtr_fll_nm);
                $sbsq_thtr_url_cln=cln($sbsq_thtr_url);

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_nm='$sbsq_thtr_nm_cln' AND sbthtr_nm='$sbsq_sbthtr_nm_cln' AND thtr_lctn='$sbsq_thtr_lctn_cln')
                      AND thtr_fll_nm='$sbsq_thtr_fll_nm_cln' AND thtr_sffx_num='$sbsq_thtr_sffx_num_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for theatre with assigned components (against subsequently known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['sbthtr_nm']) {$sbsq_thtr_cmpstn_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbsq_thtr_cmpstn_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$sbsq_thtr_cmpstn_error_thtr_lctn=':  :'.$row['thtr_lctn'];} else {$sbsq_thtr_cmpstn_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$sbsq_thtr_cmpstn_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbsq_thtr_cmpstn_error_sffx_num='';}
                  $sbsq_thtr_cmpstn_err_arr[]=$row['thtr_nm'].$sbsq_thtr_cmpstn_error_sbthtr_nm.$sbsq_thtr_cmpstn_error_thtr_lctn.$sbsq_thtr_cmpstn_error_sffx_num;
                  if(count($sbsq_thtr_cmpstn_err_arr)==1) {$errors['sbsq_thtr_cmpstn']='</br>**This theatre does not adhere to its correct component assignation: '.html(implode(' / ', $sbsq_thtr_cmpstn_err_arr)).'.**';}
                  else {$errors['sbsq_thtr_cmpstn']='</br>**These theatres does not adhere to their correct component assignation: '.html(implode(' / ', $sbsq_thtr_cmpstn_err_arr)).'.**';}
                }

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_fll_nm='$sbsq_thtr_fll_nm_cln' AND thtr_sffx_num='$sbsq_thtr_sffx_num_cln')
                      AND thtr_url='$sbsq_thtr_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing theatre URL (against subsequently known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['sbthtr_nm']) {$sbsq_thtr_url_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbsq_thtr_url_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$sbsq_thtr_url_error_thtr_lctn='::'.$row['thtr_lctn'];} else {$sbsq_thtr_url_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$sbsq_thtr_url_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbsq_thtr_url_error_sffx_num='';}
                  $sbsq_thtr_url_err_arr[]=$row['thtr_nm'].$sbsq_thtr_url_error_sbthtr_nm.$sbsq_thtr_url_error_thtr_lctn.$sbsq_thtr_url_error_sffx_num;
                  if(count($sbsq_thtr_url_err_arr)==1) {$errors['sbsq_thtr_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $sbsq_thtr_url_err_arr)).'?**';}
                  else {$errors['sbsq_thtr_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $sbsq_thtr_url_err_arr)).'?**';}
                }
                else
                {
                  $sql= "SELECT thtr_id, thtr_fll_nm, thtr_sffx_num, thtr_url, thtr_opn_dt, thtr_cls_dt, thtr_nm_frm_dt, thtr_nm_exp_dt, thtr_tr_ov
                        FROM thtr
                        WHERE thtr_url='$sbsq_thtr_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing theatre URL (against subsequently known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    if($row['thtr_id']==$thtr_id) {$errors['sbsq_thtr_id_mtch']='</br>**You cannot assign this theatre as a subsequently named theatre of itself: '.html($sbsq_thtr_nm_err).'.**';}
                    else
                    {
                      if($row['thtr_sffx_num']) {$sbsq_thtr_sffx_rmn_url_lnk=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$sbsq_thtr_sffx_rmn_url_lnk='';}
                      if(!$row['thtr_tr_ov']) {$sbsq_thtr_url_lnk='<a href="/theatre/'.html($row['thtr_url']).'" target="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm'].$sbsq_thtr_sffx_rmn_url_lnk).'</a>';}
                      else {$sbsq_thtr_url_lnk='<a href="/tour-type/'.html($row['thtr_url']).'" target="/tour-type/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm']).'</a>';}
                      if($row['thtr_opn_dt'] && $thtr_opn_dt && $row['thtr_opn_dt']!==$thtr_opn_dt)
                      {$sbsq_thtr_opn_dt_mtch_err_arr[]=$sbsq_thtr_url_lnk;
                      $errors['sbsq_thtr_opn_dt_mtch']='</br>**Subsequently named theatres must match opening date of this theatre. Please amend: '.implode(' / ', $sbsq_thtr_opn_dt_mtch_err_arr).'**';}
                      if($row['thtr_cls_dt'] && $thtr_cls_dt && $row['thtr_cls_dt']!==$thtr_cls_dt)
                      {$sbsq_thtr_cls_dt_mtch_err_arr[]=$sbsq_thtr_url_lnk;
                      $errors['sbsq_thtr_cls_dt_mtch']='</br>**Subsequently named theatres must match closing date of this theatre. Please amend: '.implode(' / ', $sbsq_thtr_cls_dt_mtch_err_arr).'**';}
                      if(($row['thtr_nm_frm_dt'] && $thtr_nm_frm_dt && $row['thtr_nm_frm_dt'] <= $thtr_nm_frm_dt) || ($row['thtr_nm_exp_dt'] && $thtr_nm_frm_dt && $row['thtr_nm_exp_dt'] <= $thtr_nm_frm_dt) || ($row['thtr_nm_frm_dt'] && $thtr_nm_exp_dt && $row['thtr_nm_frm_dt'] <= $thtr_nm_exp_dt) || ($row['thtr_nm_exp_dt'] && $thtr_nm_exp_dt && $row['thtr_nm_exp_dt'] <= $thtr_nm_exp_dt))
                      {$sbsq_thtr_nm_dt_mtch_err_arr[]=$sbsq_thtr_url_lnk;
                      $errors['sbsq_thtr_nm_dt_mtch']='</br>**Subsequently named theatres must commence name usage after expiry of name of this theatre. Please amend: '.implode(' / ', $sbsq_thtr_nm_dt_mtch_err_arr).'**';}
                      if($row['thtr_tr_ov'])
                      {$sbsq_thtr_nm_tr_ov_err_arr[]=$sbsq_thtr_url_lnk;
                      $errors['sbsq_thtr_nm_tr_ov']='</br>**Subsequently named theatres must not be tour overviews. Please amend: '.implode(' / ', $sbsq_thtr_nm_tr_ov_err_arr).'**';}
                    }
                  }
                }
              }
            }
          }
        }
      }

      if(preg_match('/\S+/', $prvs_thtr_list))
      {
        $prvs_thtr_nms=explode(',,', $_POST['prvs_thtr_list']);
        if(count($prvs_thtr_nms)>250) {$errors['prvs_thtr_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $prvs_thtr_empty_err_arr=array(); $prvs_thtr_hyphn_excss_err_arr=array(); $prvs_thtr_sffx_err_arr=array();
          $prvs_thtr_hyphn_err_arr=array(); $prvs_thtr_cln_excss_err_arr=array(); $prvs_thtr_cln_err_arr=array();
          $prvs_thtr_lctn_mtch_err_arr=array(); $prvs_thtr_smcln_excss_err_arr=array(); $prvs_thtr_sbthtr_err_arr=array();
          $prvs_thtr_smcln_err_arr=array(); $sbthtr_prvs_thtr_err_arr=array(); $prvs_thtr_dplct_arr=array();
          $prvs_thtr_cmpstn_err_arr=array(); $prvs_thtr_url_err_arr=array(); $prvs_thtr_opn_dt_mtch_err_arr=array();
          $prvs_thtr_cls_dt_mtch_err_arr=array(); $prvs_thtr_nm_dt_mtch_err_arr=array(); $prvs_thtr_nm_tr_ov_err_arr=array();
          foreach($prvs_thtr_nms as $prvs_thtr_nm)
          {
            $prvs_thtr_errors=0; $prvs_thtr_nm=trim($prvs_thtr_nm);
            if(!preg_match('/\S+/', $prvs_thtr_nm))
            {
              $prvs_thtr_empty_err_arr[]=$prvs_thtr_nm;
              if(count($prvs_thtr_empty_err_arr)==1) {$errors['prvs_thtr_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['prvs_thtr_empty']='</br>**There are '.count($prvs_thtr_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $prvs_thtr_nm_err=$prvs_thtr_nm;

              if(substr_count($prvs_thtr_nm, '--')>1) {$prvs_thtr_errors++; $prvs_thtr_sffx_num='0'; $prvs_thtr_hyphn_excss_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per theatre. Please amend: '.html(implode(' / ', $prvs_thtr_hyphn_excss_err_arr)).'.**';}
              elseif(preg_match('/^\S+.*--.+$/', $prvs_thtr_nm))
              {
                list($prvs_thtr_no_sffx, $prvs_thtr_sffx_num)=explode('--', $prvs_thtr_nm); $prvs_thtr_no_sffx=trim($prvs_thtr_no_sffx); $prvs_thtr_sffx_num=trim($prvs_thtr_sffx_num);
                if(!preg_match('/^[1-9][0-9]{0,1}$/', $prvs_thtr_sffx_num)) {$prvs_thtr_errors++; $prvs_thtr_sffx_num='0'; $prvs_thtr_sffx_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $prvs_thtr_sffx_err_arr)).'**';}
                $prvs_thtr_nm=$prvs_thtr_no_sffx;
              }
              elseif(substr_count($prvs_thtr_nm, '--')==1) {$prvs_thtr_errors++; $prvs_thtr_sffx_num='0'; $prvs_thtr_hyphn_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_hyphn']='</br>**Suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $prvs_thtr_hyphn_err_arr)).'**';}
              else {$prvs_thtr_sffx_num='0';}

              if($prvs_thtr_sffx_num) {$prvs_thtr_sffx_rmn=' ('.romannumeral($prvs_thtr_sffx_num).')';} else {$prvs_thtr_sffx_rmn='';}

              if(substr_count($prvs_thtr_nm, '::')>1) {$prvs_thtr_errors++; $prvs_thtr_lctn=''; $prvs_thtr_lctn_dsply=''; $prvs_thtr_cln_excss_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_cln_excss']='</br>**You may only use [::] once per theatre-location coupling. Please amend: '.html(implode(' / ', $prvs_thtr_cln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*::.*\S+/', $prvs_thtr_nm)) {list($prvs_thtr_nm, $prvs_thtr_lctn)=explode('::', $prvs_thtr_nm); $prvs_thtr_nm=trim($prvs_thtr_nm); $prvs_thtr_lctn=trim($prvs_thtr_lctn); $prvs_thtr_lctn_dsply=' ('.$prvs_thtr_lctn.')';}
              elseif(substr_count($prvs_thtr_nm, '::')==1) {$prvs_thtr_errors++; $prvs_thtr_lctn=''; $prvs_thtr_lctn_dsply=''; $prvs_thtr_cln_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_cln']='</br>**Location assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $prvs_thtr_cln_err_arr)).'**';}
              else {$prvs_thtr_lctn=''; $prvs_thtr_lctn_dsply='';}
              if($prvs_thtr_lctn!==$thtr_lctn) {$prvs_thtr_lctn_mtch_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_lctn_mtch']='</br>**Previously named theatres must match the location of this theatre. Please amend: '.html(implode(' / ', $prvs_thtr_lctn_mtch_err_arr)).'.**';}

              if(substr_count($prvs_thtr_nm, ';;')>1) {$prvs_thtr_errors++; $prvs_sbthtr_nm=''; $prvs_sbthtr_nm_dsply=''; $prvs_thtr_smcln_excss_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_smcln_excss']='</br>**You may only use [;;] once per theatre-subtheatre coupling. Please amend: '.html(implode(' / ', $prvs_thtr_smcln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*;;.*\S+/', $prvs_thtr_nm)) {list($prvs_thtr_nm, $prvs_sbthtr_nm)=explode(';;', $prvs_thtr_nm); $prvs_thtr_nm=trim($prvs_thtr_nm); $prvs_sbthtr_nm=trim($prvs_sbthtr_nm); $prvs_sbthtr_nm_dsply=': '.$prvs_sbthtr_nm;
              if(!$sbthtr_nm) {$prvs_thtr_sbthtr_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_sbthtr']='</br>**This theatre is not a subtheatre and previously named theatres cannot therefore be subtheatres. Please amend: '.html(implode(' / ', $prvs_thtr_sbthtr_err_arr)).'.**';}
              elseif($thtr_nm!==$prvs_thtr_nm) {$prvs_thtr_sbthtr_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_sbthtr']='</br>**This theatre is a subtheatre and previously named theatres must match its main theatre name. Please amend: '.html(implode(' / ', $prvs_thtr_sbthtr_err_arr)).'.**';}}
              elseif(substr_count($prvs_thtr_nm, ';;')==1) {$prvs_thtr_errors++; $prvs_sbthtr_nm=''; $prvs_sbthtr_nm_dsply=''; $prvs_thtr_smcln_err_arr[]=$prvs_thtr_nm_err; $errors['prvs_thtr_smcln']='</br>**Subtheatre assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $prvs_thtr_smcln_err_arr)).'**';}
              else {$prvs_sbthtr_nm=''; $prvs_sbthtr_nm_dsply='';}
              if(!$prvs_sbthtr_nm && $sbthtr_nm) {$sbthtr_prvs_thtr_err_arr[]=$prvs_thtr_nm_err; $errors['sbthtr_prvs_thtr']='</br>**This theatre is a subtheatre and previously named theatres must therefore be subtheatres. Please amend: '.html(implode(' / ', $sbthtr_prvs_thtr_err_arr)).'.**';}

              $prvs_thtr_fll_nm=$prvs_thtr_nm.$prvs_sbthtr_nm_dsply.$prvs_thtr_lctn_dsply;
              $prvs_thtr_url=generateurl($prvs_thtr_fll_nm.$prvs_thtr_sffx_rmn);

              $prvs_thtr_dplct_arr[]=$prvs_thtr_url;
              if(count(array_unique($prvs_thtr_dplct_arr))<count($prvs_thtr_dplct_arr)) {$errors['prvs_thtr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($prvs_thtr_fll_nm)>255 || strlen($prvs_thtr_url)>255) {$prvs_thtr_errors++; $errors['prvs_thtr_excss_lngth']='</br>**Theatre name and its URL are allowed a maximum of 255 characters each.**';}

              if($prvs_thtr_errors==0)
              {
                $prvs_thtr_nm_cln=cln($prvs_thtr_nm);
                $prvs_sbthtr_nm_cln=cln($prvs_sbthtr_nm);
                $prvs_thtr_lctn_cln=cln($prvs_thtr_lctn);
                $prvs_thtr_sffx_num_cln=cln($prvs_thtr_sffx_num);
                $prvs_thtr_fll_nm_cln=cln($prvs_thtr_fll_nm);
                $prvs_thtr_url_cln=cln($prvs_thtr_url);

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_nm='$prvs_thtr_nm_cln' AND sbthtr_nm='$prvs_sbthtr_nm_cln' AND thtr_lctn='$prvs_thtr_lctn_cln')
                      AND thtr_fll_nm='$prvs_thtr_fll_nm_cln' AND thtr_sffx_num='$prvs_thtr_sffx_num_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for theatre with assigned components (against previously known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['sbthtr_nm']) {$prvs_thtr_cmpstn_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$prvs_thtr_cmpstn_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$prvs_thtr_cmpstn_error_thtr_lctn=':  :'.$row['thtr_lctn'];} else {$prvs_thtr_cmpstn_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$prvs_thtr_cmpstn_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$prvs_thtr_cmpstn_error_sffx_num='';}
                  $prvs_thtr_cmpstn_err_arr[]=$row['thtr_nm'].$prvs_thtr_cmpstn_error_sbthtr_nm.$prvs_thtr_cmpstn_error_thtr_lctn.$prvs_thtr_cmpstn_error_sffx_num;
                  if(count($prvs_thtr_cmpstn_err_arr)==1) {$errors['prvs_thtr_cmpstn']='</br>**This theatre does not adhere to its correct component assignation: '.html(implode(' / ', $prvs_thtr_cmpstn_err_arr)).'.**';}
                  else {$errors['prvs_thtr_cmpstn']='</br>**These theatres does not adhere to their correct component assignation: '.html(implode(' / ', $prvs_thtr_cmpstn_err_arr)).'.**';}
                }

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_fll_nm='$prvs_thtr_fll_nm_cln' AND thtr_sffx_num='$prvs_thtr_sffx_num_cln')
                      AND thtr_url='$prvs_thtr_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing theatre URL (against previously known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['sbthtr_nm']) {$prvs_thtr_url_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$prvs_thtr_url_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$prvs_thtr_url_error_thtr_lctn='::'.$row['thtr_lctn'];} else {$prvs_thtr_url_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$prvs_thtr_url_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$prvs_thtr_url_error_sffx_num='';}
                  $prvs_thtr_url_err_arr[]=$row['thtr_nm'].$prvs_thtr_url_error_sbthtr_nm.$prvs_thtr_url_error_thtr_lctn.$prvs_thtr_url_error_sffx_num;
                  if(count($prvs_thtr_url_err_arr)==1) {$errors['prvs_thtr_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $prvs_thtr_url_err_arr)).'?**';}
                  else {$errors['prvs_thtr_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $prvs_thtr_url_err_arr)).'?**';}
                }
                else
                {
                  $sql= "SELECT thtr_id, thtr_fll_nm, thtr_sffx_num, thtr_url, thtr_opn_dt, thtr_cls_dt, thtr_nm_frm_dt, thtr_nm_exp_dt, thtr_tr_ov
                        FROM thtr
                        WHERE thtr_url='$prvs_thtr_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing theatre URL (against previously known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if($row['thtr_id']==$thtr_id) {$errors['prvs_thtr_id_mtch']='</br>**You cannot assign this theatre as a previously named theatre of itself: '.html($prvs_thtr_nm_err).'.**';}
                  else
                  {
                    if($row['thtr_sffx_num']) {$prvs_thtr_sffx_rmn_url_lnk=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$prvs_thtr_sffx_rmn_url_lnk='';}
                    if(!$row['thtr_tr_ov']) {$prvs_thtr_url_lnk='<a href="/theatre/'.html($row['thtr_url']).'" target="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm'].$prvs_thtr_sffx_rmn_url_lnk).'</a>';}
                    else {$prvs_thtr_url_lnk='<a href="/tour-type/'.html($row['thtr_url']).'" target="/tour-type/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm']).'</a>';}
                    if($row['thtr_opn_dt'] && $thtr_opn_dt && $row['thtr_opn_dt']!==$thtr_opn_dt)
                    {$prvs_thtr_opn_dt_mtch_err_arr[]=$prvs_thtr_url_lnk;
                    $errors['prvs_thtr_opn_dt_mtch']='</br>**Previously named theatres must match opening date of this theatre. Please amend: '.implode(' / ', $prvs_thtr_opn_dt_mtch_err_arr).'**';}
                    if($row['thtr_cls_dt'] && $thtr_cls_dt && $row['thtr_cls_dt']!==$thtr_cls_dt)
                    {$prvs_thtr_cls_dt_mtch_err_arr[]=$prvs_thtr_url_lnk;
                    $errors['prvs_thtr_cls_dt_mtch']='</br>**Previously named theatres must match closing date of this theatre. Please amend: '.implode(' / ', $prvs_thtr_cls_dt_mtch_err_arr).'**';}
                    if(($row['thtr_nm_frm_dt'] && $thtr_nm_frm_dt && $row['thtr_nm_frm_dt'] >= $thtr_nm_frm_dt) || ($row['thtr_nm_exp_dt'] && $thtr_nm_frm_dt && $row['thtr_nm_exp_dt'] >= $thtr_nm_frm_dt) || ($row['thtr_nm_frm_dt'] && $thtr_nm_exp_dt && $row['thtr_nm_frm_dt'] >= $thtr_nm_exp_dt) || ($row['thtr_nm_exp_dt'] && $thtr_nm_exp_dt && $row['thtr_nm_exp_dt'] >= $thtr_nm_exp_dt))
                    {$prvs_thtr_nm_dt_mtch_err_arr[]=$prvs_thtr_url_lnk;
                    $errors['prvs_thtr_nm_dt_mtch']='</br>**Previously named theatres must expire name usage before commencement of name of this theatre. Please amend: '.implode(' / ', $prvs_thtr_nm_dt_mtch_err_arr).'**';}
                    if($row['thtr_tr_ov'])
                    {$prvs_thtr_nm_tr_ov_err_arr[]=$prvs_thtr_url_lnk;
                    $errors['prvs_thtr_nm_tr_ov']='</br>**Previously named theatres must not be tour overviews. Please amend: '.implode(' / ', $prvs_thtr_nm_tr_ov_err_arr).'**';}
                  }
                }
              }
            }
          }
        }
      }

      if(preg_match('/\S+/', $sbsqad_thtr_list))
      {
        $sbsqad_thtr_nms=explode(',,', $_POST['sbsqad_thtr_list']);
        if(count($sbsqad_thtr_nms)>250) {$errors['sbsqad_thtr_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $sbsqad_thtr_empty_err_arr=array(); $sbsqad_thtr_hyphn_excss_err_arr=array(); $sbsqad_thtr_sffx_err_arr=array();
          $sbsqad_thtr_hyphn_err_arr=array(); $sbsqad_thtr_cln_excss_err_arr=array(); $sbsqad_thtr_cln_err_arr=array();
          $sbsqad_thtr_smcln_excss_err_arr=array(); $sbsqad_thtr_sbthtr_err_arr=array(); $sbsqad_thtr_smcln_err_arr=array();
          $sbthtr_sbsqad_thtr_err_arr=array(); $sbsqad_thtr_dplct_arr=array(); $sbsqad_thtr_cmpstn_err_arr=array();
          $sbsqad_thtr_url_err_arr=array(); $sbsqadad_thtr_opn_cls_dt_err_arr=array(); $sbsqad_thtr_opn_dt_mtch_err_arr=array();
          $sbsqad_thtr_nm_tr_ov_err_arr=array();
          foreach($sbsqad_thtr_nms as $sbsqad_thtr_nm)
          {
            $sbsqad_thtr_errors=0; $sbsqad_thtr_nm=trim($sbsqad_thtr_nm);
            if(!preg_match('/\S+/', $sbsqad_thtr_nm))
            {
              $sbsqad_thtr_empty_err_arr[]=$sbsqad_thtr_nm;
              if(count($sbsqad_thtr_empty_err_arr)==1) {$errors['sbsqad_thtr_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['sbsqad_thtr_empty']='</br>**There are '.count($sbsqad_thtr_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $sbsqad_thtr_nm_err=$sbsqad_thtr_nm;
              if(substr_count($sbsqad_thtr_nm, '--')>1) {$sbsqad_thtr_errors++; $sbsqad_thtr_sffx_num='0'; $sbsqad_thtr_hyphn_excss_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbsqad_thtr_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per theatre. Please amend: '.html(implode(' / ', $sbsqad_thtr_hyphn_excss_err_arr)).'.**';}
              elseif(preg_match('/^\S+.*--.+$/', $sbsqad_thtr_nm))
              {
                list($sbsqad_thtr_no_sffx, $sbsqad_thtr_sffx_num)=explode('--', $sbsqad_thtr_nm); $sbsqad_thtr_no_sffx=trim($sbsqad_thtr_no_sffx); $sbsqad_thtr_sffx_num=trim($sbsqad_thtr_sffx_num);
                if(!preg_match('/^[1-9][0-9]{0,1}$/', $sbsqad_thtr_sffx_num)) {$sbsqad_thtr_errors++; $sbsqad_thtr_sffx_num='0'; $sbsqad_thtr_sffx_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbsqad_thtr_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $sbsqad_thtr_sffx_err_arr)).'**';}
                $sbsqad_thtr_nm=$sbsqad_thtr_no_sffx;
              }
              elseif(substr_count($sbsqad_thtr_nm, '--')==1) {$sbsqad_thtr_errors++; $sbsqad_thtr_sffx_num='0'; $sbsqad_thtr_hyphn_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbsqad_thtr_hyphn']='</br>**Suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $sbsqad_thtr_hyphn_err_arr)).'**';}
              else {$sbsqad_thtr_sffx_num='0';}

              if($sbsqad_thtr_sffx_num) {$sbsqad_thtr_sffx_rmn=' ('.romannumeral($sbsqad_thtr_sffx_num).')';} else {$sbsqad_thtr_sffx_rmn='';}

              if(substr_count($sbsqad_thtr_nm, '::')>1) {$sbsqad_thtr_errors++; $sbsqad_thtr_lctn=''; $sbsqad_thtr_lctn_dsply=''; $sbsqad_thtr_cln_excss_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbsqad_thtr_cln_excss']='</br>**You may only use [::] once per theatre-location coupling. Please amend: '.html(implode(' / ', $sbsqad_thtr_cln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*::.*\S+/', $sbsqad_thtr_nm)) {list($sbsqad_thtr_nm, $sbsqad_thtr_lctn)=explode('::', $sbsqad_thtr_nm); $sbsqad_thtr_nm=trim($sbsqad_thtr_nm); $sbsqad_thtr_lctn=trim($sbsqad_thtr_lctn); $sbsqad_thtr_lctn_dsply=' ('.$sbsqad_thtr_lctn.')';}
              elseif(substr_count($sbsqad_thtr_nm, '::')==1) {$sbsqad_thtr_errors++; $sbsqad_thtr_lctn=''; $sbsqad_thtr_lctn_dsply=''; $sbsqad_thtr_cln_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbsqad_thtr_cln']='</br>**Location assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $sbsqad_thtr_cln_err_arr)).'**';}
              else {$sbsqad_thtr_lctn=''; $sbsqad_thtr_lctn_dsply='';}

              if(substr_count($sbsqad_thtr_nm, ';;')>1) {$sbsqad_thtr_errors++; $sbsqad_sbthtr_nm=''; $sbsqad_sbthtr_nm_dsply=''; $sbsqad_thtr_smcln_excss_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbsqad_thtr_smcln_excss']='</br>**You may only use [;;] once per theatre-subtheatre coupling. Please amend: '.html(implode(' / ', $sbsqad_thtr_smcln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*;;.*\S+/', $sbsqad_thtr_nm)) {list($sbsqad_thtr_nm, $sbsqad_sbthtr_nm)=explode(';;', $sbsqad_thtr_nm); $sbsqad_thtr_nm=trim($sbsqad_thtr_nm); $sbsqad_sbthtr_nm=trim($sbsqad_sbthtr_nm); $sbsqad_sbthtr_nm_dsply=': '.$sbsqad_sbthtr_nm;
              if(!$sbthtr_nm) {$sbsqad_thtr_sbthtr_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbsqad_thtr_sbthtr']='</br>**This theatre is not a subtheatre and subsequently located theatres cannot therefore be subtheatres. Please amend: '.html(implode(' / ', $sbsqad_thtr_sbthtr_err_arr)).'.**';}}
              elseif(substr_count($sbsqad_thtr_nm, ';;')==1) {$sbsqad_thtr_errors++; $sbsqad_sbthtr_nm=''; $sbsqad_sbthtr_nm_dsply=''; $sbsqad_thtr_smcln_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbsqad_thtr_smcln']='</br>**Subtheatre assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $sbsqad_thtr_smcln_err_arr)).'**';}
              else {$sbsqad_sbthtr_nm=''; $sbsqad_sbthtr_nm_dsply='';}
              if(!$sbsqad_sbthtr_nm && $sbthtr_nm) {$sbthtr_sbsqad_thtr_err_arr[]=$sbsqad_thtr_nm_err; $errors['sbthtr_sbsqad_thtr']='</br>**This theatre is a subtheatre and subsequently located theatres must therefore be subtheatres. Please amend: '.html(implode(' / ', $sbthtr_sbsqad_thtr_err_arr)).'.**';}

              $sbsqad_thtr_fll_nm=$sbsqad_thtr_nm.$sbsqad_sbthtr_nm_dsply.$sbsqad_thtr_lctn_dsply;
              $sbsqad_thtr_url=generateurl($sbsqad_thtr_fll_nm.$sbsqad_thtr_sffx_rmn);

              $sbsqad_thtr_dplct_arr[]=$sbsqad_thtr_url;
              if(count(array_unique($sbsqad_thtr_dplct_arr))<count($sbsqad_thtr_dplct_arr)) {$errors['sbsqad_thtr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($sbsqad_thtr_fll_nm)>255 || strlen($sbsqad_thtr_url)>255) {$sbsqad_thtr_errors++; $errors['sbsqad_thtr_excss_lngth']='</br>**Theatre name and its URL are allowed a maximum of 255 characters each.**';}

              if($sbsqad_thtr_errors==0)
              {
                $sbsqad_thtr_nm_cln=cln($sbsqad_thtr_nm);
                $sbsqad_sbthtr_nm_cln=cln($sbsqad_sbthtr_nm);
                $sbsqad_thtr_lctn_cln=cln($sbsqad_thtr_lctn);
                $sbsqad_thtr_sffx_num_cln=cln($sbsqad_thtr_sffx_num);
                $sbsqad_thtr_fll_nm_cln=cln($sbsqad_thtr_fll_nm);
                $sbsqad_thtr_url_cln=cln($sbsqad_thtr_url);

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_nm='$sbsqad_thtr_nm_cln' AND sbthtr_nm='$sbsqad_sbthtr_nm_cln' AND thtr_lctn='$sbsqad_thtr_lctn_cln')
                      AND thtr_fll_nm='$sbsqad_thtr_fll_nm_cln' AND thtr_sffx_num='$sbsqad_thtr_sffx_num_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for theatre with assigned components (against subsequently known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['sbthtr_nm']) {$sbsqad_thtr_cmpstn_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbsqad_thtr_cmpstn_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$sbsqad_thtr_cmpstn_error_thtr_lctn=':  :'.$row['thtr_lctn'];} else {$sbsqad_thtr_cmpstn_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$sbsqad_thtr_cmpstn_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbsqad_thtr_cmpstn_error_sffx_num='';}
                  $sbsqad_thtr_cmpstn_err_arr[]=$row['thtr_nm'].$sbsqad_thtr_cmpstn_error_sbthtr_nm.$sbsqad_thtr_cmpstn_error_thtr_lctn.$sbsqad_thtr_cmpstn_error_sffx_num;
                  if(count($sbsqad_thtr_cmpstn_err_arr)==1) {$errors['sbsqad_thtr_cmpstn']='</br>**This theatre does not adhere to its correct component assignation: '.html(implode(' / ', $sbsqad_thtr_cmpstn_err_arr)).'.**';}
                  else {$errors['sbsqad_thtr_cmpstn']='</br>**These theatres does not adhere to their correct component assignation: '.html(implode(' / ', $sbsqad_thtr_cmpstn_err_arr)).'.**';}
                }

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_fll_nm='$sbsqad_thtr_fll_nm_cln' AND thtr_sffx_num='$sbsqad_thtr_sffx_num_cln')
                      AND thtr_url='$sbsqad_thtr_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing theatre URL (against subsequently known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['sbthtr_nm']) {$sbsqad_thtr_url_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbsqad_thtr_url_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$sbsqad_thtr_url_error_thtr_lctn='::'.$row['thtr_lctn'];} else {$sbsqad_thtr_url_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$sbsqad_thtr_url_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$sbsqad_thtr_url_error_sffx_num='';}
                  $sbsqad_thtr_url_err_arr[]=$row['thtr_nm'].$sbsqad_thtr_url_error_sbthtr_nm.$sbsqad_thtr_url_error_thtr_lctn.$sbsqad_thtr_url_error_sffx_num;
                  if(count($sbsqad_thtr_url_err_arr)==1) {$errors['sbsqad_thtr_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $sbsqad_thtr_url_err_arr)).'?**';}
                  else {$errors['sbsqad_thtr_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $sbsqad_thtr_url_err_arr)).'?**';}
                }
                else
                {
                  $sql= "SELECT thtr_id, thtr_fll_nm, thtr_sffx_num, thtr_url, thtr_opn_dt, thtr_cls_dt, thtr_tr_ov
                        FROM thtr
                        WHERE thtr_url='$sbsqad_thtr_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing theatre URL (against subsequently known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if($row['thtr_id']==$thtr_id) {$errors['sbsqad_thtr_id_mtch']='</br>**You cannot assign this theatre as a subsequently located theatre of itself: '.html($sbsqad_thtr_nm_err).'.**';}
                  else
                  {
                    if($row['thtr_sffx_num']) {$sbsqad_thtr_sffx_rmn_url_lnk=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$sbsqad_thtr_sffx_rmn_url_lnk='';}
                    if(!$row['thtr_tr_ov']) {$sbsqad_thtr_url_lnk='<a href="/theatre/'.html($row['thtr_url']).'" target="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm'].$sbsqad_thtr_sffx_rmn_url_lnk).'</a>';}
                    else {$sbsqad_thtr_url_lnk='<a href="/tour-type/'.html($row['thtr_url']).'" target="/tour-type/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm']).'</a>';}
                    if(($row['thtr_opn_dt'] && $thtr_opn_dt && $row['thtr_opn_dt'] <= $thtr_opn_dt) || ($row['thtr_cls_dt'] && $thtr_opn_dt && $row['thtr_cls_dt'] <= $thtr_opn_dt) || ($row['thtr_opn_dt'] && $thtr_cls_dt && $row['thtr_opn_dt'] <= $thtr_cls_dt) || ($row['thtr_cls_dt'] && $thtr_cls_dt && $row['thtr_cls_dt'] <= $thtr_cls_dt))
                    {$sbsqad_thtr_opn_cls_dt_err_arr[]=$sbsqad_thtr_url_lnk;
                    $errors['sbsqad_thtr_opn_cls_dt']='</br>**Subsequently located theatres must have closed before this theatre opened. Please amend: '.implode(' / ', $sbsqad_thtr_opn_cls_dt_err_arr).'**';}
                    if($row['thtr_tr_ov'])
                    {$sbsqad_thtr_nm_tr_ov_err_arr[]=$sbsqad_thtr_url_lnk;
                    $errors['sbsqad_thtr_nm_tr_ov']='</br>**Subsequently named theatres must not be tour overviews. Please amend: '.implode(' / ', $sbsqad_thtr_nm_tr_ov_err_arr).'**';}
                  }
                }
              }
            }
          }
        }
      }

      if(preg_match('/\S+/', $prvsad_thtr_list))
      {
        $prvsad_thtr_nms=explode(',,', $_POST['prvsad_thtr_list']);
        if(count($prvsad_thtr_nms)>250) {$errors['prvsad_thtr_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $prvsad_thtr_empty_err_arr=array(); $prvsad_thtr_hyphn_excss_err_arr=array(); $prvsad_thtr_sffx_err_arr=array();
          $prvsad_thtr_hyphn_err_arr=array(); $prvsad_thtr_cln_excss_err_arr=array(); $prvsad_thtr_cln_err_arr=array();
          $prvsad_thtr_smcln_excss_err_arr=array(); $prvsad_thtr_sbthtr_err_arr=array(); $prvsad_thtr_smcln_err_arr=array();
          $sbthtr_prvsad_thtr_err_arr=array(); $prvsad_thtr_dplct_arr=array(); $prvsad_thtr_cmpstn_err_arr=array();
          $prvsad_thtr_url_err_arr=array(); $prvsadad_thtr_opn_cls_dt_err_arr=array(); $prvsad_thtr_opn_dt_mtch_err_arr=array();
          $prvsad_thtr_nm_tr_ov_err_arr=array();
          foreach($prvsad_thtr_nms as $prvsad_thtr_nm)
          {
            $prvsad_thtr_errors=0; $prvsad_thtr_nm=trim($prvsad_thtr_nm);
            if(!preg_match('/\S+/', $prvsad_thtr_nm))
            {
              $prvsad_thtr_empty_err_arr[]=$prvsad_thtr_nm;
              if(count($prvsad_thtr_empty_err_arr)==1) {$errors['prvsad_thtr_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['prvsad_thtr_empty']='</br>**There are '.count($prvsad_thtr_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $prvsad_thtr_nm_err=$prvsad_thtr_nm;

              if(substr_count($prvsad_thtr_nm, '--')>1) {$prvsad_thtr_errors++; $prvsad_thtr_sffx_num='0'; $prvsad_thtr_hyphn_excss_err_arr[]=$prvsad_thtr_nm_err; $errors['prvsad_thtr_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per theatre. Please amend: '.html(implode(' / ', $prvsad_thtr_hyphn_excss_err_arr)).'.**';}
              elseif(preg_match('/^\S+.*--.+$/', $prvsad_thtr_nm))
              {
                list($prvsad_thtr_no_sffx, $prvsad_thtr_sffx_num)=explode('--', $prvsad_thtr_nm); $prvsad_thtr_no_sffx=trim($prvsad_thtr_no_sffx); $prvsad_thtr_sffx_num=trim($prvsad_thtr_sffx_num);
                if(!preg_match('/^[1-9][0-9]{0,1}$/', $prvsad_thtr_sffx_num)) {$prvsad_thtr_errors++; $prvsad_thtr_sffx_num='0'; $prvsad_thtr_sffx_err_arr[]=$prvsad_thtr_nm_err; $errors['prvsad_thtr_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $prvsad_thtr_sffx_err_arr)).'**';}
                $prvsad_thtr_nm=$prvsad_thtr_no_sffx;
              }
              elseif(substr_count($prvsad_thtr_nm, '--')==1) {$prvsad_thtr_errors++; $prvsad_thtr_sffx_num='0'; $prvsad_thtr_hyphn_err_arr[]=$prvsad_thtr_nm_err; $errors['prvsad_thtr_hyphn']='</br>**Suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $prvsad_thtr_hyphn_err_arr)).'**';}
              else {$prvsad_thtr_sffx_num='0';}

              if($prvsad_thtr_sffx_num) {$prvsad_thtr_sffx_rmn=' ('.romannumeral($prvsad_thtr_sffx_num).')';} else {$prvsad_thtr_sffx_rmn='';}

              if(substr_count($prvsad_thtr_nm, '::')>1) {$prvsad_thtr_errors++; $prvsad_thtr_lctn=''; $prvsad_thtr_lctn_dsply=''; $prvsad_thtr_cln_excss_err_arr[]=$prvsad_thtr_nm_err; $errors['prvsad_thtr_cln_excss']='</br>**You may only use [::] once per theatre-location coupling. Please amend: '.html(implode(' / ', $prvsad_thtr_cln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*::.*\S+/', $prvsad_thtr_nm)) {list($prvsad_thtr_nm, $prvsad_thtr_lctn)=explode('::', $prvsad_thtr_nm); $prvsad_thtr_nm=trim($prvsad_thtr_nm); $prvsad_thtr_lctn=trim($prvsad_thtr_lctn); $prvsad_thtr_lctn_dsply=' ('.$prvsad_thtr_lctn.')';}
              elseif(substr_count($prvsad_thtr_nm, '::')==1) {$prvsad_thtr_errors++; $prvsad_thtr_lctn=''; $prvsad_thtr_lctn_dsply=''; $prvsad_thtr_cln_err_arr[]=$prvsad_thtr_nm_err; $errors['prvsad_thtr_cln']='</br>**Location assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $prvsad_thtr_cln_err_arr)).'**';}
              else {$prvsad_thtr_lctn=''; $prvsad_thtr_lctn_dsply='';}

              if(substr_count($prvsad_thtr_nm, ';;')>1) {$prvsad_thtr_errors++; $prvsad_sbthtr_nm=''; $prvsad_sbthtr_nm_dsply=''; $prvsad_thtr_smcln_excss_err_arr[]=$prvsad_thtr_nm_err; $errors['prvsad_thtr_smcln_excss']='</br>**You may only use [;;] once per theatre-subtheatre coupling. Please amend: '.html(implode(' / ', $prvsad_thtr_smcln_excss_err_arr)).'.**';}
              elseif(preg_match('/\S+.*;;.*\S+/', $prvsad_thtr_nm)) {list($prvsad_thtr_nm, $prvsad_sbthtr_nm)=explode(';;', $prvsad_thtr_nm); $prvsad_thtr_nm=trim($prvsad_thtr_nm); $prvsad_sbthtr_nm=trim($prvsad_sbthtr_nm); $prvsad_sbthtr_nm_dsply=': '.$prvsad_sbthtr_nm;
              if(!$sbthtr_nm) {$prvsad_thtr_sbthtr_err_arr[]=$prvsad_thtr_nm_err; $errors['prvsad_thtr_sbthtr']='</br>**This theatre is not a subtheatre and previously located theatres cannot therefore be subtheatres. Please amend: '.html(implode(' / ', $prvsad_thtr_sbthtr_err_arr)).'.**';}}
              elseif(substr_count($prvsad_thtr_nm, ';;')==1) {$prvsad_thtr_errors++; $prvsad_sbthtr_nm=''; $prvsad_sbthtr_nm_dsply=''; $prvsad_thtr_smcln_err_arr[]=$prvsad_thtr_nm_err; $errors['prvsad_thtr_smcln']='</br>**Subtheatre assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $prvsad_thtr_smcln_err_arr)).'**';}
              else {$prvsad_sbthtr_nm=''; $prvsad_sbthtr_nm_dsply='';}
              if(!$prvsad_sbthtr_nm && $sbthtr_nm) {$sbthtr_prvsad_thtr_err_arr[]=$prvsad_thtr_nm_err; $errors['sbthtr_prvsad_thtr']='</br>**This theatre is a subtheatre and previously located theatres must therefore be subtheatres. Please amend: '.html(implode(' / ', $sbthtr_prvsad_thtr_err_arr)).'.**';}

              $prvsad_thtr_fll_nm=$prvsad_thtr_nm.$prvsad_sbthtr_nm_dsply.$prvsad_thtr_lctn_dsply;
              $prvsad_thtr_url=generateurl($prvsad_thtr_fll_nm.$prvsad_thtr_sffx_rmn);

              $prvsad_thtr_dplct_arr[]=$prvsad_thtr_url;
              if(count(array_unique($prvsad_thtr_dplct_arr))<count($prvsad_thtr_dplct_arr)) {$errors['prvsad_thtr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($prvsad_thtr_fll_nm)>255 || strlen($prvsad_thtr_url)>255) {$prvsad_thtr_errors++; $errors['prvsad_thtr_excss_lngth']='</br>**Theatre name and its URL are allowed a maximum of 255 characters each.**';}

              if($prvsad_thtr_errors==0)
              {
                $prvsad_thtr_nm_cln=cln($prvsad_thtr_nm);
                $prvsad_sbthtr_nm_cln=cln($prvsad_sbthtr_nm);
                $prvsad_thtr_lctn_cln=cln($prvsad_thtr_lctn);
                $prvsad_thtr_sffx_num_cln=cln($prvsad_thtr_sffx_num);
                $prvsad_thtr_fll_nm_cln=cln($prvsad_thtr_fll_nm);
                $prvsad_thtr_url_cln=cln($prvsad_thtr_url);

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_nm='$prvsad_thtr_nm_cln' AND sbthtr_nm='$prvsad_sbthtr_nm_cln' AND thtr_lctn='$prvsad_thtr_lctn_cln')
                      AND thtr_fll_nm='$prvsad_thtr_fll_nm_cln' AND thtr_sffx_num='$prvsad_thtr_sffx_num_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for theatre with assigned components (against previously known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['sbthtr_nm']) {$prvsad_thtr_cmpstn_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$prvsad_thtr_cmpstn_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$prvsad_thtr_cmpstn_error_thtr_lctn=':  :'.$row['thtr_lctn'];} else {$prvsad_thtr_cmpstn_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$prvsad_thtr_cmpstn_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$prvsad_thtr_cmpstn_error_sffx_num='';}
                  $prvsad_thtr_cmpstn_err_arr[]=$row['thtr_nm'].$prvsad_thtr_cmpstn_error_sbthtr_nm.$prvsad_thtr_cmpstn_error_thtr_lctn.$prvsad_thtr_cmpstn_error_sffx_num;
                  if(count($prvsad_thtr_cmpstn_err_arr)==1) {$errors['prvsad_thtr_cmpstn']='</br>**This theatre does not adhere to its correct component assignation: '.html(implode(' / ', $prvsad_thtr_cmpstn_err_arr)).'.**';}
                  else {$errors['prvsad_thtr_cmpstn']='</br>**These theatres does not adhere to their correct component assignation: '.html(implode(' / ', $prvsad_thtr_cmpstn_err_arr)).'.**';}
                }

                $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
                      FROM thtr
                      WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_fll_nm='$prvsad_thtr_fll_nm_cln' AND thtr_sffx_num='$prvsad_thtr_sffx_num_cln')
                      AND thtr_url='$prvsad_thtr_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing theatre URL (against previously known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['sbthtr_nm']) {$prvsad_thtr_url_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$prvsad_thtr_url_error_sbthtr_nm='';}
                  if($row['thtr_lctn']) {$prvsad_thtr_url_error_thtr_lctn='::'.$row['thtr_lctn'];} else {$prvsad_thtr_url_error_thtr_lctn='';}
                  if($row['thtr_sffx_num']) {$prvsad_thtr_url_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$prvsad_thtr_url_error_sffx_num='';}
                  $prvsad_thtr_url_err_arr[]=$row['thtr_nm'].$prvsad_thtr_url_error_sbthtr_nm.$prvsad_thtr_url_error_thtr_lctn.$prvsad_thtr_url_error_sffx_num;
                  if(count($prvsad_thtr_url_err_arr)==1) {$errors['prvsad_thtr_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $prvsad_thtr_url_err_arr)).'?**';}
                  else {$errors['prvsad_thtr_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $prvsad_thtr_url_err_arr)).'?**';}
                }
                else
                {
                  $sql= "SELECT thtr_id, thtr_fll_nm, thtr_sffx_num, thtr_url, thtr_opn_dt, thtr_cls_dt, thtr_tr_ov
                        FROM thtr
                        WHERE thtr_url='$prvsad_thtr_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing theatre URL (against previously known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if($row['thtr_id']==$thtr_id) {$errors['prvsad_thtr_id_mtch']='</br>**You cannot assign this theatre as a previously located theatre of itself: '.html($prvsad_thtr_nm_err).'.**';}
                  else
                  {
                    if($row['thtr_sffx_num']) {$prvsad_thtr_sffx_rmn_url_lnk=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$prvsad_thtr_sffx_rmn_url_lnk='';}
                    if(!$row['thtr_tr_ov']) {$prvsad_thtr_url_lnk='<a href="/theatre/'.html($row['thtr_url']).'" target="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm'].$prvsad_thtr_sffx_rmn_url_lnk).'</a>';}
                    else {$prvsad_thtr_url_lnk='<a href="/tour-type/'.html($row['thtr_url']).'" target="/tour-type/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm']).'</a>';}
                    if(($row['thtr_opn_dt'] && $thtr_opn_dt && $row['thtr_opn_dt'] >= $thtr_opn_dt) || ($row['thtr_cls_dt'] && $thtr_opn_dt && $row['thtr_cls_dt'] >= $thtr_opn_dt) || ($row['thtr_opn_dt'] && $thtr_cls_dt && $row['thtr_opn_dt'] >= $thtr_cls_dt) || ($row['thtr_cls_dt'] && $thtr_cls_dt && $row['thtr_cls_dt'] >= $thtr_cls_dt))
                    {$prvsad_thtr_opn_cls_dt_err_arr[]=$prvsad_thtr_url_lnk;
                    $errors['prvsad_thtr_opn_cls_dt']='</br>**Previously located theatres must have closed before this theatre opened. Please amend: '.implode(' / ', $prvsad_thtr_opn_cls_dt_err_arr).'**';}
                    if($row['thtr_tr_ov'])
                    {$prvsad_thtr_nm_tr_ov_err_arr[]=$prvsad_thtr_url_lnk;
                    $errors['prvsad_thtr_nm_tr_ov']='</br>**Previously named theatres must not be tour overviews. Please amend: '.implode(' / ', $prvsad_thtr_nm_tr_ov_err_arr).'**';}
                  }
                }
              }
            }
          }
        }
      }
    }
    else
    {
      if(preg_match('/\S+/', $sbthtr_nm)) {$errors['thtr_tr_ov_sbthtr_nm']='**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_lctn)) {$errors['thtr_tr_ov_thtr_lctn_nm']='**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_adrs)) {$errors['thtr_tr_ov_adrs']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $lctn_lnk_nm)) {$errors['thtr_tr_ov_lctn_lnk']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_typ_list)) {$errors['thtr_tr_ov_thtr_typ_list']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_comp_list)) {$errors['thtr_tr_ov_comp']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $sbthtr_list)) {$errors['thtr_tr_ov_sbthtr_list']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_cpcty)) {$errors['thtr_tr_ov_thtr_cpcty']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_opn_dt)) {$errors['thtr_tr_ov_opn_dt']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_cls_dt)) {$errors['thtr_tr_ov_cls_dt']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_nm_frm_dt)) {$errors['thtr_tr_ov_nm_frm_dt']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $thtr_nm_exp_dt)) {$errors['thtr_tr_ov_nm_exp_dt']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $sbsq_thtr_list)) {$errors['thtr_tr_ov_sbsq_thtr']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $prvs_thtr_list)) {$errors['thtr_tr_ov_prvs_thtr']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $sbsqad_thtr_list)) {$errors['thtr_tr_ov_sbsqad_thtr']='</br>**This field must be left empty if tour overview is checked.**';}
      if(preg_match('/\S+/', $prvsad_thtr_list)) {$errors['thtr_tr_ov_prvsad_thtr']='</br>**This field must be left empty if tour overview is checked.**';}

      $sql="SELECT 1 FROM thtr WHERE thtr_id='$thtr_id' AND srthtrid IS NOT NULL";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for theatre associations as subtheatre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0) {$errors['thtr_tr_ov_sbthtr_assoc_exst']='</br>**Theatre cannot be assigned as a tour (by checking tour overview) while it has existing associations as a subtheatre.**';}
    }

    $sql= "SELECT thtr_fll_nm, thtr_nm, thtr_url, thtr_lctn, thtr_opn_dt, thtr_cls_dt, thtr_clsd
          FROM thtr
          WHERE thtr_id='$thtr_id' AND srthtrid IS NOT NULL";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for theatre associations as sub-theatre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0)
    {
      $sbthtr_assoc_thtr_url='<a href="/theatre/'.html($row['thtr_url']).'" target="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_fll_nm']).'</a>';
      if(!preg_match('/\S+/', $sbthtr_nm)) {$errors['sbthtr_assoc_sbthtr_nm_empty']='</br>**If theatre is assigned as a subtheatre then this section must contain name of subtheatre.**';}
      if($row['thtr_nm']!==$thtr_nm) {$errors['sbthtr_assoc_thtr_nm_mtch']='</br>**If theatre is assigned as a subtheatre then it must share the same theatre name as the associated theatre: '.html($row['thtr_nm']).'.**';}
      if($row['thtr_lctn']!==$thtr_lctn) {$errors['sbthtr_assoc_thtr_lctn_mtch']='</br>**If theatre is assigned as a subtheatre then it must share the same theatre location as the associated theatre: '.html($row['thtr_lctn']).'.**';}
      if(($row['thtr_opn_dt'] && $thtr_opn_dt && $row['thtr_opn_dt']>$thtr_opn_dt) || ($row['thtr_cls_dt'] && $thtr_opn_dt && $row['thtr_cls_dt']<$thtr_opn_dt))
      {$errors['sbthtr_assoc_thtr_opn_dt_mtch']='</br>**If theatre is assigned as a subtheatre then its opening date must fall within the opening and closing dates of the associated theatre: '.$sbthtr_assoc_thtr_url.'.**';}
      if(($row['thtr_opn_dt'] && $thtr_cls_dt && $row['thtr_opn_dt']>$thtr_cls_dt) || ($row['thtr_cls_dt'] && $thtr_cls_dt && $row['thtr_cls_dt']<$thtr_cls_dt))
      {$errors['sbthtr_assoc_thtr_cls_dt_mtch']='</br>**If theatre is assigned as a subtheatre then its closing date must fall within the opening and closing dates of the associated theatre: '.$sbthtr_assoc_thtr_url.'.**';}
      if(!$thtr_clsd && $row['thtr_clsd']) {$errors['sbthtr_assoc_thtr_clsd']='</br>**If theatre is assigned as a subtheatre and its associated theatre is closed then it too must be closed: '.$sbthtr_assoc_thtr_url.'.**';}
      if($thtr_adrs) {$errors['sbthtr_assoc_thtr_adrs']='</br>**If theatre is assigned as a subtheatre then this field must be left empty as its address will be inherited from its associated theatre: '.$sbthtr_assoc_thtr_url.'.**';}
      if($lctn_lnk_nm) {$errors['sbthtr_assoc_thtr_lctn_lnk']='</br>**If theatre is assigned as a subtheatre then this field must be left empty as its location link will be inherited from its associated theatre: '.$sbthtr_assoc_thtr_url.'.**';}
      if($thtr_comp_list) {$errors['sbthtr_assoc_thtr_comp']='</br>**If theatre is assigned as a subtheatre then this field must be left empty as its company link will be inherited from its associated theatre: '.$sbthtr_assoc_thtr_url.'.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $thtr_id=cln($_POST['thtr_id']);
      $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, thtr_fll_nm
            FROM thtr
            WHERE thtr_id='$thtr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring theatre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['thtr_sffx_num']) {$thtr_sffx_rmn=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$thtr_sffx_rmn='';}
      $pagetab='Edit: '.html($row['thtr_fll_nm'].$thtr_sffx_rmn);
      if(!$thtr_tr_ov) {$pagehdr='THEATRE:'; $pagedscr='theatre';} else {$pagehdr='TOUR TYPE:'; $pagedscr='tour type';}
      $thtr_nm_dsply=html($row['thtr_nm']);
      if($row['sbthtr_nm']) {$sbthtr_nm_dsply=':</br>'.html($row['sbthtr_nm']);} else {$sbthtr_nm_dsply='';}
      if($row['thtr_lctn']) {$thtr_lctn_dsply='('.html($row['thtr_lctn']).')';} else {$thtr_lctn_dsply='';}
      $thtr_nm=$_POST['thtr_nm'];
      $sbthtr_nm=$_POST['sbthtr_nm'];
      $thtr_lctn=$_POST['thtr_lctn'];
      $thtr_sffx_num=$_POST['thtr_sffx_num'];
      $thtr_adrs=$_POST['thtr_adrs'];
      $lctn_lnk_nm=$_POST['lctn_lnk_nm'];
      $thtr_typ_list=$_POST['thtr_typ_list'];
      $thtr_comp_list=$_POST['thtr_comp_list'];
      $sbthtr_list=$_POST['sbthtr_list'];
      $thtr_cpcty=$_POST['thtr_cpcty'];
      $thtr_opn_dt=$_POST['thtr_opn_dt'];
      $thtr_cls_dt=$_POST['thtr_cls_dt'];
      $thtr_nm_frm_dt=$_POST['thtr_nm_frm_dt'];
      $thtr_nm_exp_dt=$_POST['thtr_nm_exp_dt'];
      $sbsq_thtr_list=$_POST['sbsq_thtr_list'];
      $prvs_thtr_list=$_POST['prvs_thtr_list'];
      $sbsqad_thtr_list=$_POST['sbsqad_thtr_list'];
      $prvsad_thtr_list=$_POST['prvsad_thtr_list'];
      $textarea=$_POST['textarea'];
      $errors['thtr_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $thtr_id=html($thtr_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE thtr SET
            thtr_nm='$thtr_nm',
            sbthtr_nm='$sbthtr_nm',
            thtr_lctn='$thtr_lctn',
            thtr_fll_nm='$thtr_fll_nm',
            thtr_alph=CASE WHEN '$thtr_alph'!='' THEN '$thtr_alph' END,
            thtr_sffx_num='$thtr_sffx_num',
            thtr_url='$thtr_url',
            thtr_adrs='$thtr_adrs',
            thtr_cpcty=CASE WHEN '$thtr_cpcty'!='' THEN '$thtr_cpcty' END,
            thtr_opn_dt=CASE WHEN '$thtr_opn_dt'!='' THEN '$thtr_opn_dt' END,
            thtr_opn_dt_frmt=CASE WHEN '$thtr_opn_dt'!='' THEN '$thtr_opn_dt_frmt' END,
            thtr_cls_dt=CASE WHEN '$thtr_cls_dt'!='' THEN '$thtr_cls_dt' END,
            thtr_cls_dt_frmt=CASE WHEN '$thtr_cls_dt'!='' THEN '$thtr_cls_dt_frmt' END,
            thtr_clsd='$thtr_clsd',
            thtr_nm_frm_dt=CASE WHEN '$thtr_nm_frm_dt'!='' THEN '$thtr_nm_frm_dt' END,
            thtr_nm_frm_dt_frmt=CASE WHEN '$thtr_nm_frm_dt'!='' THEN '$thtr_nm_frm_dt_frmt' END,
            thtr_nm_exp_dt=CASE WHEN '$thtr_nm_exp_dt'!='' THEN '$thtr_nm_exp_dt' END,
            thtr_nm_exp_dt_frmt=CASE WHEN '$thtr_nm_exp_dt'!='' THEN '$thtr_nm_exp_dt_frmt' END,
            thtr_nm_exp='$thtr_nm_exp',
            thtr_tr_ov=CASE WHEN '$thtr_tr_ov'!='' THEN '$thtr_tr_ov' END
            WHERE thtr_id='$thtr_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted theatre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="UPDATE thtr SET thtr_lctnid=NULL WHERE thtr_id='$thtr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error nullifying theatre-location (link) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM thtr_lctn_alt WHERE thtrid='$thtr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-location (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $lctn_lnk_nm))
      {
        $lctn_lnk=$lctn_lnk_nm;
        if(preg_match('/\S+.*\|\|.*\S+/', $lctn_lnk))
        {
          list($lctn_lnk, $lctn_lnk_alt_list)=explode('||', $lctn_lnk);
          $lctn_lnk=trim($lctn_lnk); $lctn_lnk_alt_list=trim($lctn_lnk_alt_list);
        }
        else {$lctn_lnk_alt_list='';}

        if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $lctn_lnk))
        {list($lctn_lnk, $lctn_lnk_sffx_num)=explode('--', $lctn_lnk); $lctn_lnk=trim($lctn_lnk); $lctn_lnk_sffx_num=trim($lctn_lnk_sffx_num); $lctn_lnk_sffx_rmn=' ('.romannumeral($lctn_lnk_sffx_num).')';}
        else
        {$lctn_lnk_sffx_num='0'; $lctn_lnk_sffx_rmn='';}

        $lctn_lnk_url=generateurl($lctn_lnk.$lctn_lnk_sffx_rmn);
        $lctn_lnk_alph=alph($lctn_lnk);

        $sql="SELECT 1 FROM lctn WHERE lctn_url='$lctn_lnk_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of location (link): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO lctn(lctn_nm, lctn_alph, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn)
                VALUES('$lctn_lnk', CASE WHEN '$lctn_lnk_alph'!='' THEN '$lctn_lnk_alph' END, '$lctn_lnk_sffx_num', '$lctn_lnk_url', 0, 0)";
          if(!mysqli_query($link, $sql)) {$error='Error adding location (link) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql="UPDATE thtr SET thtr_lctnid=(SELECT lctn_id FROM lctn WHERE lctn_url='$lctn_lnk_url') WHERE thtr_id='$thtr_id'";
        if(!mysqli_query($link, $sql)) {$error='Error adding theatre-location (link) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

        if($lctn_lnk_alt_list)
        {
          $lctn_lnk_alts=explode('>>', $lctn_lnk_alt_list);
          foreach($lctn_lnk_alts as $lctn_lnk_alt)
          {
            $lctn_lnk_alt=trim($lctn_lnk_alt);

            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $lctn_lnk_alt))
            {
              list($lctn_lnk_alt, $lctn_lnk_alt_sffx_num)=explode('--', $lctn_lnk_alt);
              $lctn_lnk_alt=trim($lctn_lnk_alt); $lctn_lnk_alt_sffx_num=trim($lctn_lnk_alt_sffx_num);
              $lctn_lnk_alt_sffx_rmn=' ('.romannumeral($lctn_lnk_alt_sffx_num).')';
            }
            else {$lctn_lnk_alt_sffx_num='0'; $lctn_lnk_alt_sffx_rmn='';}

            $lctn_lnk_alt_url=generateurl($lctn_lnk_alt.$lctn_lnk_alt_sffx_rmn);

            $sql= "INSERT INTO thtr_lctn_alt(thtrid, thtr_lctnid, thtr_lctn_altid)
                  SELECT '$thtr_id',
                  (SELECT lctn_id FROM lctn WHERE lctn_url='$lctn_lnk_url'),
                  (SELECT lctn_id FROM lctn WHERE lctn_url='$lctn_lnk_alt_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding theatre-location (alternate location) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      $sql="DELETE FROM thtrtyp WHERE thtrid='$thtr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-type associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $thtr_typ_list))
      {
        $thtr_typ_nms=explode(',,', $thtr_typ_list);
        $n=0;
        foreach($thtr_typ_nms as $thtr_typ_nm)
        {
          $thtr_typ_nm=trim($thtr_typ_nm);
          $thtr_typ_url=generateurl($thtr_typ_nm);
          $thtr_typ_ordr=++$n;

          $sql="SELECT 1 FROM thtr_typ WHERE thtr_typ_url='$thtr_typ_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of theatre type: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql="INSERT INTO thtr_typ(thtr_typ_nm, thtr_typ_url) VALUES('$thtr_typ_nm', '$thtr_typ_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding theatre type data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql="INSERT INTO thtrtyp(thtrid, thtr_typ_ordr, thtr_typid)
              SELECT '$thtr_id', '$thtr_typ_ordr', thtr_typ_id FROM thtr_typ WHERE thtr_typ_url='$thtr_typ_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding theatre-type association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM thtrcomp WHERE thtrid='$thtr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-company (owned by) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $thtr_comp_list))
      {
        $thtr_comp_nms=explode(',,', $thtr_comp_list);
        $n=0;
        foreach($thtr_comp_nms as $thtr_comp)
        {
          $thtr_comp=trim($thtr_comp);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $thtr_comp))
          {
            list($thtr_comp, $thtr_comp_sffx_num)=explode('--', $thtr_comp);
            $thtr_comp=trim($thtr_comp); $thtr_comp_sffx_num=trim($thtr_comp_sffx_num);
            $thtr_comp_sffx_rmn=' ('.romannumeral($thtr_comp_sffx_num).')';
          }
          else
          {$thtr_comp_sffx_num='0'; $thtr_comp_sffx_rmn='';}

          $thtr_comp_ordr=++$n;
          $thtr_comp_url=generateurl($thtr_comp.$thtr_comp_sffx_rmn);
          $thtr_comp_alph=alph($thtr_comp);

          $sql="SELECT 1 FROM comp WHERE comp_url='$thtr_comp_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of creative (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                  VALUES('$thtr_comp', CASE WHEN '$thtr_comp_alph'!='' THEN '$thtr_comp_alph' END, '$thtr_comp_sffx_num', '$thtr_comp_url', 1, 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding creative (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql="INSERT INTO thtrcomp(thtrid, thtr_comp_ordr, compid) SELECT $thtr_id, $thtr_comp_ordr, comp_id FROM comp WHERE comp_url='$thtr_comp_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-creative (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="UPDATE thtr SET srthtrid=NULL, sbthtr_ordr=NULL WHERE srthtrid='$thtr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error nullifying theatre-subtheatre associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $sbthtr_list))
      {
        $sbthtr_nms=explode(',,', $sbthtr_list);
        $n=0;
        foreach($sbthtr_nms as $sbthtr_nm)
        {
          $sbthtr_nm=trim($sbthtr_nm);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $sbthtr_nm))
          {
            list($sbthtr_nm, $sbthtr_sffx_num)=explode('--', $sbthtr_nm);
            $sbthtr_nm=trim($sbthtr_nm); $sbthtr_sffx_num=trim($sbthtr_sffx_num);
            $sbthtr_sffx_rmn=' ('.romannumeral($sbthtr_sffx_num).')';
          }
          else
          {$sbthtr_sffx_num='0'; $sbthtr_sffx_rmn='';}

          if(preg_match('/\S+.*::.*\S+/', $sbthtr_nm))
          {
            list($sbthtr_nm, $sbthtr_lctn)=explode('::', $sbthtr_nm);
            $sbthtr_nm=trim($sbthtr_nm); $sbthtr_lctn=trim($sbthtr_lctn);
            $sbthtr_lctn_dsply=' ('.$sbthtr_lctn.')';
          }
          else
          {$sbthtr_lctn=NULL; $sbthtr_lctn_dsply='';}

          if(preg_match('/\S+.*;;.*\S+/', $sbthtr_nm))
          {
            list($sbthtr_nm, $sbsbthtr_nm)=explode(';;', $sbthtr_nm);
            $sbthtr_nm=trim($sbthtr_nm); $sbsbthtr_nm=trim($sbsbthtr_nm);
            $sbsbthtr_nm_dsply=': '.$sbsbthtr_nm;
          }
          else
          {$sbsbthtr_nm=NULL; $sbsbthtr_nm_dsply='';}

          $sbthtr_fll_nm=$sbthtr_nm.$sbsbthtr_nm_dsply.$sbthtr_lctn_dsply;
          $sbthtr_ordr=++$n;
          $sbthtr_url=generateurl($sbthtr_fll_nm.$sbthtr_sffx_rmn);
          $sbthtr_alph=alph($sbthtr_fll_nm);

          $sql="SELECT 1 FROM thtr WHERE thtr_url='$sbthtr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of theatre (against subsequently known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO thtr(thtr_nm, sbthtr_nm, thtr_lctn, thtr_fll_nm, thtr_alph, thtr_sffx_num, thtr_url, thtr_clsd, thtr_nm_exp, thtr_tr_ov)
                  VALUES('$sbthtr_nm', '$sbsbthtr_nm', '$sbthtr_lctn', '$sbthtr_fll_nm', CASE WHEN '$sbthtr_alph'!='' THEN '$sbthtr_alph' END, '$sbthtr_sffx_num', '$sbthtr_url', '$sbthtr_clsd', 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "UPDATE thtr AS t1, (SELECT thtr_id FROM thtr WHERE thtr_url='$sbthtr_url') AS t2 SET
                t1.srthtrid='$thtr_id',
                t1.sbthtr_ordr='$sbthtr_ordr'
                WHERE t1.thtr_id=t2.thtr_id";
          if(!mysqli_query($link, $sql)) {$error='Error updating theatre-subtheatre association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM thtr_aka WHERE thtr_prvs_id='$thtr_id' OR thtr_sbsq_id='$thtr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-previously/subsequently known as associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $sbsq_thtr_list))
      {
        $sbsq_thtr_nms=explode(',,', $sbsq_thtr_list);
        foreach($sbsq_thtr_nms as $sbsq_thtr_nm)
        {
          $sbsq_thtr_nm=trim($sbsq_thtr_nm);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $sbsq_thtr_nm)) {list($sbsq_thtr_nm, $sbsq_thtr_sffx_num)=explode('--', $sbsq_thtr_nm); $sbsq_thtr_nm=trim($sbsq_thtr_nm); $sbsq_thtr_sffx_num=trim($sbsq_thtr_sffx_num); $sbsq_thtr_sffx_rmn=' ('.romannumeral($sbsq_thtr_sffx_num).')';}
          else {$sbsq_thtr_sffx_num='0'; $sbsq_thtr_sffx_rmn='';}

          if(preg_match('/\S+.*::.*\S+/', $sbsq_thtr_nm)) {list($sbsq_thtr_nm, $sbsq_thtr_lctn)=explode('::', $sbsq_thtr_nm); $sbsq_thtr_nm=trim($sbsq_thtr_nm); $sbsq_thtr_lctn=trim($sbsq_thtr_lctn); $sbsq_thtr_lctn_dsply=' ('.$sbsq_thtr_lctn.')';}
          else {$sbsq_thtr_lctn=NULL; $sbsq_thtr_lctn_dsply='';}

          if(preg_match('/\S+.*;;.*\S+/', $sbsq_thtr_nm)) {list($sbsq_thtr_nm, $sbsq_sbthtr_nm)=explode(';;', $sbsq_thtr_nm); $sbsq_thtr_nm=trim($sbsq_thtr_nm); $sbsq_sbthtr_nm=trim($sbsq_sbthtr_nm); $sbsq_sbthtr_nm_dsply=': '.$sbsq_sbthtr_nm;}
          else {$sbsq_sbthtr_nm=NULL; $sbsq_sbthtr_nm_dsply='';}

          $sbsq_thtr_fll_nm=$sbsq_thtr_nm.$sbsq_sbthtr_nm_dsply.$sbsq_thtr_lctn_dsply;
          $sbsq_thtr_url=generateurl($sbsq_thtr_fll_nm.$sbsq_thtr_sffx_rmn);
          $sbsq_thtr_alph=alph($sbsq_thtr_fll_nm);

          $sql="SELECT 1 FROM thtr WHERE thtr_url='$sbsq_thtr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of theatre (against subsequently known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO thtr(thtr_nm, sbthtr_nm, thtr_lctn, thtr_fll_nm, thtr_alph, thtr_sffx_num, thtr_url, thtr_clsd, thtr_nm_exp, thtr_tr_ov)
                  VALUES('$sbsq_thtr_nm', '$sbsq_sbthtr_nm', '$sbsq_thtr_lctn', '$sbsq_thtr_fll_nm', CASE WHEN '$sbsq_thtr_alph'!='' THEN '$sbsq_thtr_alph' END, '$sbsq_thtr_sffx_num', '$sbsq_thtr_url', 0, 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding subsequently known as theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO thtr_aka(thtr_prvs_id, thtr_sbsq_id)
                SELECT $thtr_id, thtr_id FROM thtr WHERE thtr_url='$sbsq_thtr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding theatre-subsequently known as association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $prvs_thtr_list))
      {
        $prvs_thtr_nms=explode(',,', $prvs_thtr_list);
        foreach($prvs_thtr_nms as $prvs_thtr_nm)
        {
          $prvs_thtr_nm=trim($prvs_thtr_nm);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prvs_thtr_nm)) {list($prvs_thtr_nm, $prvs_thtr_sffx_num)=explode('--', $prvs_thtr_nm); $prvs_thtr_nm=trim($prvs_thtr_nm); $prvs_thtr_sffx_num=trim($prvs_thtr_sffx_num); $prvs_thtr_sffx_rmn=' ('.romannumeral($prvs_thtr_sffx_num).')';}
          else {$prvs_thtr_sffx_num='0'; $prvs_thtr_sffx_rmn='';}

          if(preg_match('/\S+.*::.*\S+/', $prvs_thtr_nm)) {list($prvs_thtr_nm, $prvs_thtr_lctn)=explode('::', $prvs_thtr_nm); $prvs_thtr_nm=trim($prvs_thtr_nm); $prvs_thtr_lctn=trim($prvs_thtr_lctn); $prvs_thtr_lctn_dsply=' ('.$prvs_thtr_lctn.')';}
          else {$prvs_thtr_lctn=NULL; $prvs_thtr_lctn_dsply='';}

          if(preg_match('/\S+.*;;.*\S+/', $prvs_thtr_nm)) {list($prvs_thtr_nm, $prvs_sbthtr_nm)=explode(';;', $prvs_thtr_nm); $prvs_thtr_nm=trim($prvs_thtr_nm); $prvs_sbthtr_nm=trim($prvs_sbthtr_nm); $prvs_sbthtr_nm_dsply=': '.$prvs_sbthtr_nm;}
          else {$prvs_sbthtr_nm=NULL; $prvs_sbthtr_nm_dsply='';}

          $prvs_thtr_fll_nm=$prvs_thtr_nm.$prvs_sbthtr_nm_dsply.$prvs_thtr_lctn_dsply;
          $prvs_thtr_url=generateurl($prvs_thtr_fll_nm.$prvs_thtr_sffx_rmn);
          $prvs_thtr_alph=alph($prvs_thtr_fll_nm);

          $sql="SELECT 1 FROM thtr WHERE thtr_url='$prvs_thtr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of theatre (against previously known as theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO thtr(thtr_nm, sbthtr_nm, thtr_lctn, thtr_fll_nm, thtr_alph, thtr_sffx_num, thtr_url, thtr_clsd, thtr_nm_exp, thtr_tr_ov)
                  VALUES('$prvs_thtr_nm', '$prvs_sbthtr_nm', '$prvs_thtr_lctn', '$prvs_thtr_fll_nm', CASE WHEN '$prvs_thtr_alph'!='' THEN '$prvs_thtr_alph' END, '$prvs_thtr_sffx_num', '$prvs_thtr_url', 0, 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding previously known as theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO thtr_aka(thtr_sbsq_id, thtr_prvs_id)
                SELECT $thtr_id, thtr_id FROM thtr WHERE thtr_url='$prvs_thtr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding theatre-previously known as association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM thtr_alt_adrs WHERE thtr_prvsad_id='$thtr_id' OR thtr_sbsqad_id='$thtr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-previously/subsequently located associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $sbsqad_thtr_list))
      {
        $sbsqad_thtr_nms=explode(',,', $sbsqad_thtr_list);
        foreach($sbsqad_thtr_nms as $sbsqad_thtr_nm)
        {
          $sbsqad_thtr_nm=trim($sbsqad_thtr_nm);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $sbsqad_thtr_nm)) {list($sbsqad_thtr_nm, $sbsqad_thtr_sffx_num)=explode('--', $sbsqad_thtr_nm); $sbsqad_thtr_nm=trim($sbsqad_thtr_nm); $sbsqad_thtr_sffx_num=trim($sbsqad_thtr_sffx_num); $sbsqad_thtr_sffx_rmn=' ('.romannumeral($sbsqad_thtr_sffx_num).')';}
          else {$sbsqad_thtr_sffx_num='0'; $sbsqad_thtr_sffx_rmn='';}

          if(preg_match('/\S+.*::.*\S+/', $sbsqad_thtr_nm)) {list($sbsqad_thtr_nm, $sbsqad_thtr_lctn)=explode('::', $sbsqad_thtr_nm); $sbsqad_thtr_nm=trim($sbsqad_thtr_nm); $sbsqad_thtr_lctn=trim($sbsqad_thtr_lctn); $sbsqad_thtr_lctn_dsply=' ('.$sbsqad_thtr_lctn.')';}
          else {$sbsqad_thtr_lctn=NULL; $sbsqad_thtr_lctn_dsply='';}

          if(preg_match('/\S+.*;;.*\S+/', $sbsqad_thtr_nm)) {list($sbsqad_thtr_nm, $sbsqad_sbthtr_nm)=explode(';;', $sbsqad_thtr_nm); $sbsqad_thtr_nm=trim($sbsqad_thtr_nm); $sbsqad_sbthtr_nm=trim($sbsqad_sbthtr_nm); $sbsqad_sbthtr_nm_dsply=': '.$sbsqad_sbthtr_nm;}
          else {$sbsqad_sbthtr_nm=NULL; $sbsqad_sbthtr_nm_dsply='';}

          $sbsqad_thtr_fll_nm=$sbsqad_thtr_nm.$sbsqad_sbthtr_nm_dsply.$sbsqad_thtr_lctn_dsply;
          $sbsqad_thtr_url=generateurl($sbsqad_thtr_fll_nm.$sbsqad_thtr_sffx_rmn);
          $sbsqad_thtr_alph=alph($sbsqad_thtr_fll_nm);

          $sql="SELECT 1 FROM thtr WHERE thtr_url='$sbsqad_thtr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of theatre (against subsequently located theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO thtr(thtr_nm, sbthtr_nm, thtr_lctn, thtr_fll_nm, thtr_alph, thtr_sffx_num, thtr_url, thtr_clsd, thtr_nm_exp, thtr_tr_ov)
                  VALUES('$sbsqad_thtr_nm', '$sbsqad_sbthtr_nm', '$sbsqad_thtr_lctn', '$sbsqad_thtr_fll_nm', CASE WHEN '$sbsqad_thtr_alph'!='' THEN '$sbsqad_thtr_alph' END, '$sbsqad_thtr_sffx_num', '$sbsqad_thtr_url', 0, 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding subsequent address theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO thtr_alt_adrs(thtr_prvsad_id, thtr_sbsqad_id)
                SELECT $thtr_id, thtr_id FROM thtr WHERE thtr_url='$sbsqad_thtr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding theatre-subsequently located association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $prvsad_thtr_list))
      {
        $prvsad_thtr_nms=explode(',,', $prvsad_thtr_list);
        foreach($prvsad_thtr_nms as $prvsad_thtr_nm)
        {
          $prvsad_thtr_nm=trim($prvsad_thtr_nm);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prvsad_thtr_nm)) {list($prvsad_thtr_nm, $prvsad_thtr_sffx_num)=explode('--', $prvsad_thtr_nm); $prvsad_thtr_nm=trim($prvsad_thtr_nm); $prvsad_thtr_sffx_num=trim($prvsad_thtr_sffx_num); $prvsad_thtr_sffx_rmn=' ('.romannumeral($prvsad_thtr_sffx_num).')';}
          else {$prvsad_thtr_sffx_num='0'; $prvsad_thtr_sffx_rmn='';}

          if(preg_match('/\S+.*::.*\S+/', $prvsad_thtr_nm)) {list($prvsad_thtr_nm, $prvsad_thtr_lctn)=explode('::', $prvsad_thtr_nm); $prvsad_thtr_nm=trim($prvsad_thtr_nm); $prvsad_thtr_lctn=trim($prvsad_thtr_lctn); $prvsad_thtr_lctn_dsply=' ('.$prvsad_thtr_lctn.')';}
          else {$prvsad_thtr_lctn=NULL; $prvsad_thtr_lctn_dsply='';}

          if(preg_match('/\S+.*;;.*\S+/', $prvsad_thtr_nm)) {list($prvsad_thtr_nm, $prvsad_sbthtr_nm)=explode(';;', $prvsad_thtr_nm); $prvsad_thtr_nm=trim($prvsad_thtr_nm); $prvsad_sbthtr_nm=trim($prvsad_sbthtr_nm); $prvsad_sbthtr_nm_dsply=': '.$prvsad_sbthtr_nm;}
          else {$prvsad_sbthtr_nm=NULL; $prvsad_sbthtr_nm_dsply='';}

          $prvsad_thtr_fll_nm=$prvsad_thtr_nm.$prvsad_sbthtr_nm_dsply.$prvsad_thtr_lctn_dsply;
          $prvsad_thtr_url=generateurl($prvsad_thtr_fll_nm.$prvsad_thtr_sffx_rmn);
          $prvsad_thtr_alph=alph($prvsad_thtr_fll_nm);

          $sql="SELECT 1 FROM thtr WHERE thtr_url='$prvsad_thtr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of theatre (against previously located theatre): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO thtr(thtr_nm, sbthtr_nm, thtr_lctn, thtr_fll_nm, thtr_alph, thtr_sffx_num, thtr_url, thtr_clsd, thtr_nm_exp, thtr_tr_ov)
                  VALUES('$prvsad_thtr_nm', '$prvsad_sbthtr_nm', '$prvsad_thtr_lctn', '$prvsad_thtr_fll_nm', CASE WHEN '$prvsad_thtr_alph'!='' THEN '$prvsad_thtr_alph' END, '$prvsad_thtr_sffx_num', '$prvsad_thtr_url', 0, 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding previous address theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO thtr_alt_adrs(thtr_sbsqad_id, thtr_prvsad_id)
                SELECT $thtr_id, thtr_id FROM thtr WHERE thtr_url='$prvsad_thtr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding theatre-previously located association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    if($thtr_tr_ov)
    {
      session_start();
      $_SESSION['successclass']='success';
      $_SESSION['message']='THIS TOUR TYPE HAS BEEN EDITED:'.' '.html($thtr_session);
      header('Location: http://'. $_SERVER['HTTP_HOST'].'/tour-type/'.$thtr_url);
    }
    else
    {
      session_start();
      $_SESSION['successclass']='success';
      $_SESSION['message']='THIS THEATRE HAS BEEN EDITED:'.' '.html($thtr_session);
      header('Location: http://'. $_SERVER['HTTP_HOST'].'/theatre/'.$thtr_url);
    }
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $thtr_id=cln($_POST['thtr_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prd WHERE thtrid='$thtr_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring theatre-production association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM thtr WHERE (thtr_id='$thtr_id' AND srthtrid IS NOT NULL) OR (srthtrid='$thtr_id') LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring theatre-subtheatre association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Theatre (main theatre/subtheatre)';}

    $sql="SELECT 1 FROM awrd WHERE thtrid='$thtr_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring theatre (tour type)-awards ceremony association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Awards ceremony';}

    if(count($assocs)>0)
    {$errors['thtr_dlt']='**Theatre must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, thtr_fll_nm, thtr_tr_ov FROM thtr WHERE thtr_id='$thtr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring theatre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);

      if($row['thtr_sffx_num']) {$thtr_sffx_rmn=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$thtr_sffx_rmn='';}
      $pagetab='Edit: '.html($row['thtr_fll_nm'].$thtr_sffx_rmn);
      if(!$row['thtr_tr_ov']) {$pagehdr='THEATRE:'; $pagedscr='theatre';} else {$pagehdr='TOUR TYPE:'; $pagedscr='tour type';}
      $thtr_nm_dsply=html($row['thtr_nm']);
      if($row['sbthtr_nm']) {$sbthtr_nm_dsply=':</br>'.html($row['sbthtr_nm']);} else {$sbthtr_nm_dsply='';}
      if($row['thtr_lctn']) {$thtr_lctn_dsply='('.html($row['thtr_lctn']).')';} else {$thtr_lctn_dsply='';}
      $thtr_nm=$_POST['thtr_nm'];
      $sbthtr_nm=$_POST['sbthtr_nm'];
      $thtr_lctn=$_POST['thtr_lctn'];
      $thtr_sffx_num=$_POST['thtr_sffx_num'];
      $thtr_adrs=$_POST['thtr_adrs'];
      $lctn_lnk_nm=$_POST['lctn_lnk_nm'];
      $thtr_typ_list=$_POST['thtr_typ_list'];
      $thtr_comp_nm=$_POST['thtr_comp_nm'];
      $sbthtr_list=$_POST['sbthtr_list'];
      $thtr_cpcty=$_POST['thtr_cpcty'];
      $thtr_opn_dt=$_POST['thtr_opn_dt'];
      if($_POST['thtr_opn_dt_frmt']=='1') {$thtr_opn_dt_frmt='1';} if($_POST['thtr_opn_dt_frmt']=='2') {$thtr_opn_dt_frmt='2';}
      if($_POST['thtr_opn_dt_frmt']=='3') {$thtr_opn_dt_frmt='3';} if($_POST['thtr_opn_dt_frmt']=='4') {$thtr_opn_dt_frmt='4';}
      $thtr_cls_dt=$_POST['thtr_cls_dt'];
      if($_POST['thtr_cls_dt_frmt']=='1') {$thtr_cls_dt_frmt='1';} if($_POST['thtr_cls_dt_frmt']=='2') {$thtr_cls_dt_frmt='2';}
      if($_POST['thtr_cls_dt_frmt']=='3') {$thtr_cls_dt_frmt='3';} if($_POST['thtr_cls_dt_frmt']=='4') {$thtr_cls_dt_frmt='4';}
      if(isset($_POST['thtr_clsd'])) {$thtr_clsd='1';} else {$thtr_clsd='0';}
      $thtr_nm_frm_dt=$_POST['thtr_nm_frm_dt'];
      if($_POST['thtr_nm_frm_dt_frmt']=='1') {$thtr_nm_frm_dt_frmt='1';} if($_POST['thtr_nm_frm_dt_frmt']=='2') {$thtr_nm_frm_dt_frmt='2';}
      if($_POST['thtr_nm_frm_dt_frmt']=='3') {$thtr_nm_frm_dt_frmt='3';} if($_POST['thtr_nm_frm_dt_frmt']=='4') {$thtr_nm_frm_dt_frmt='4';}
      $thtr_nm_exp_dt=$_POST['thtr_nm_exp_dt'];
      if($_POST['thtr_nm_exp_dt_frmt']=='1') {$thtr_nm_exp_dt_frmt='1';} if($_POST['thtr_nm_exp_dt_frmt']=='2') {$thtr_nm_exp_dt_frmt='2';}
      if($_POST['thtr_nm_exp_dt_frmt']=='3') {$thtr_nm_exp_dt_frmt='3';} if($_POST['thtr_nm_exp_dt_frmt']=='4') {$thtr_nm_exp_dt_frmt='4';}
      if(isset($_POST['thtr_nm_exp'])) {$thtr_nm_exp='1';} else {$thtr_nm_exp='0';}
      $sbsq_thtr_list=$_POST['sbsq_thtr_list'];
      $prvs_thtr_list=$_POST['prvs_thtr_list'];
      $sbsqad_thtr_list=$_POST['sbsqad_thtr_list'];
      $prvsad_thtr_list=$_POST['prvsad_thtr_list'];
      if(isset($_POST['thtr_tr_ov'])) {$thtr_tr_ov='1';} else {$thtr_tr_ov='0';}
      $textarea=$_POST['textarea'];
      $thtr_id=html($thtr_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num, thtr_fll_nm, thtr_tr_ov FROM thtr WHERE thtr_id='$thtr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring theatre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['thtr_sffx_num']) {$thtr_sffx_rmn=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$thtr_sffx_rmn='';}
      $pagetab= 'Delete confirmation: '.html($row['thtr_fll_nm'].$thtr_sffx_rmn);
      if(!$row['thtr_tr_ov']) {$pagehdr='THEATRE:'; $pagedscr='theatre';} else {$pagehdr='TOUR TYPE:'; $pagedscr='tour type';}
      $thtr_nm_dsply=html($row['thtr_nm']);
      if($row['sbthtr_nm']) {$sbthtr_nm_dsply=':</br>'.html($row['sbthtr_nm']);} else {$sbthtr_nm_dsply='';}
      if($row['thtr_lctn']) {$thtr_lctn_dsply='('.html($row['thtr_lctn']).')';} else {$thtr_lctn_dsply='';}
      $thtr_tr_ov=$row['thtr_tr_ov'];
      $thtr_id=html($thtr_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thtr_id=cln($_POST['thtr_id']);
    $sql="SELECT thtr_fll_nm, thtr_sffx_num, thtr_tr_ov FROM thtr WHERE thtr_id='$thtr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    if($row['thtr_sffx_num']) {$thtr_sffx_rmn=' ('.romannumeral($row['thtr_sffx_num']).')';} else {$thtr_sffx_rmn='';}
    $thtr_session=$row['thtr_fll_nm'].$thtr_sffx_rmn;

    $sql="UPDATE prd SET thtrid=NULL WHERE thtrid='$thtr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error nullifying theatre-production associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thtrtyp WHERE thtrid='$thtr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-type associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thtrcomp WHERE thtrid='$thtr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-company (owned by) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="UPDATE thtr SET srthtrid=NULL, sbthtr_ordr=NULL WHERE srthtrid='$thtr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error nullifying theatre-subtheatre associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thtr_aka WHERE thtr_prvs_id='$thtr_id' OR thtr_sbsq_id='$thtr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-previously/subsequently known as associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thtr_alt_adrs WHERE thtr_prvsad_id='$thtr_id' OR thtr_sbsqad_id='$thtr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theatre-previously/subsequently located associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM comp_lctn_alt WHERE compid='$comp_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company-location (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM thtr WHERE thtr_id='$thtr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting theatre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    if($row['thtr_tr_ov'])
    {
      session_start();
      $_SESSION['successclass']='success';
      $_SESSION['message']='THIS TOUR TYPE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($thtr_session);
      header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    }
    else
    {
      session_start();
      $_SESSION['successclass']='success';
      $_SESSION['message']='THIS THEATRE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($thtr_session);
      header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    }
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $thtr_id=cln($_POST['thtr_id']);
    $sql="SELECT thtr_url FROM thtr WHERE thtr_id='$thtr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theatre URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['thtr_url']);
    exit();
  }
?>