<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_GET['addrequest']))
  {
    include 'addrequest.html.php';
    exit();
  }

  if(isset($_POST['add']) and $_POST['add']=='Add Production')
  {
    $pagetitle='Add Production';
    $pagesubtitle='Add a new production to the database.';
    $pagetab='Add Production | TheatreBase';
    $prd_nm='';
    $prd_sbnm='';
    $prd_crtd='TBC';
    $prd_updtd='TBC';
    $mat_list='';
    $pt_list='';
    $prd_frst_dt='';
    $prd_prss_dt='';
    $prd_prss_dt_2='';
    $prd_lst_dt='';
    $prd_prss_dt_tbc='0';
    $prd_prv_only='0';
    $prd_dts_info='1';
    $prd_prss_wrdng='';
    $prd_tbc_nt='';
    $prd_dt_nt='';
    $thtr_nm='';
    $prd_thtr_nt='';
    $prd_clss='1';
    $prd_clss_dsply='';
    $prd_tr='1';
    $tr_dsply='';
    $tr_lg_list='';
    $prd_coll='1';
    $coll_dsply='';
    $coll_sg_list='';
    $rep_list='';
    $prdrn_list='';
    $prd_vrsn_list='';
    $txt_vrsn_list='';
    $ctgry_list='';
    $gnr_list='';
    $ftr_list='';
    $thm_list='';
    $sttng_list='';
    $wri_list='';
    $prdcr_list='';
    $prf_list='';
    $prd_othr_prts='0';
    $prd_cst_nt='';
    $us_list='';
    $mscn_list='';
    $crtv_list='';
    $prdtm_list='';
    $ssn_nm='';
    $fstvl_nm='';
    $crs_list='';
    $rvw_list='';
    $alt_nm_list='';
    $textarea='';
    $frmactn='?add';
    $edit=NULL;
    include 'addform.html.php';
    exit();
  }

  if(isset($_POST['add']) and $_POST['add']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $prd_id=NULL;
    $edit=NULL;
    include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_vldtn.inc.php';

    if(count($errors)>0)
    {
      $pagetitle='Add Production';
      $pagesubtitle='Add a new production to the database.';
      $pagetab='Add Production | TheatreBase';
      $prd_nm=$_POST['prd_nm'];
      $prd_sbnm=$_POST['prd_sbnm'];
      $prd_crtd='TBC';
      $prd_updtd='TBC';
      $mat_list=$_POST['mat_list'];
      $pt_list=$_POST['pt_list'];
      $prd_frst_dt=$_POST['prd_frst_dt'];
      $prd_prss_dt=$_POST['prd_prss_dt'];
      $prd_prss_dt_2=$_POST['prd_prss_dt_2'];
      $prd_lst_dt=$_POST['prd_lst_dt'];
      $prd_prss_wrdng=$_POST['prd_prss_wrdng'];
      $prd_tbc_nt=$_POST['prd_tbc_nt'];
      $prd_dt_nt=$_POST['prd_dt_nt'];
      $tr_lg_list=$_POST['tr_lg_list'];
      if($prd_tr=='2') {$tr_dsply=' [TOUR]';} elseif($prd_tr=='3') {$tr_dsply=' [TOUR LEG]';} else {$tr_dsply='';}
      $coll_sg_list=$_POST['coll_sg_list'];
      if($prd_coll=='2') {$coll_dsply=' [COLLECTION]';} elseif($prd_coll=='3') {$coll_dsply=' [PART OF COLLECTION]';} else {$coll_dsply='';}
      $rep_list=$_POST['rep_list'];
      $prdrn_list=$_POST['prdrn_list'];
      $thtr_nm=$_POST['thtr_nm'];
      $prd_thtr_nt=$_POST['prd_thtr_nt'];
      if($prd_clss=='1') {$prd_clss_dsply='';} elseif($prd_clss=='2') {$prd_clss_dsply='AMATEUR ';} else {$prd_clss_dsply='DRAMA SCHOOL ';}
      $prd_vrsn_list=$_POST['prd_vrsn_list'];
      $txt_vrsn_list=$_POST['txt_vrsn_list'];
      $ctgry_list=$_POST['ctgry_list'];
      $gnr_list=$_POST['gnr_list'];
      $ftr_list=$_POST['ftr_list'];
      $thm_list=$_POST['thm_list'];
      $sttng_list=$_POST['sttng_list'];
      $wri_list=$_POST['wri_list'];
      $prdcr_list=$_POST['prdcr_list'];
      $prf_list=$_POST['prf_list'];
      $prd_cst_nt=$_POST['prd_cst_nt'];
      $us_list=$_POST['us_list'];
      $mscn_list=$_POST['mscn_list'];
      $crtv_list=$_POST['crtv_list'];
      $prdtm_list=$_POST['prdtm_list'];
      $ssn_nm=$_POST['ssn_nm'];
      $fstvl_nm=$_POST['fstvl_nm'];
      $crs_list=$_POST['crs_list'];
      $rvw_list=$_POST['rvw_list'];
      $alt_nm_list=$_POST['alt_nm_list'];
      $textarea=$_POST['textarea'];
      $errors['prd_add_edit_error']='**There are errors on this page that need amending before submission can be successful.**</br>';
      $frmactn='?add';
      include 'addform.html.php';
      exit();
    }
    else
    {
      $sql= "INSERT INTO prd SET
            prd_nm='$prd_nm',
            prd_alph=CASE WHEN '$prd_alph'!='' THEN '$prd_alph' END,
            prd_url='$prd_url',
            prd_sbnm='$prd_sbnm',
            prd_crtd=NOW(),
            prd_updtd=NOW(),
            prd_frst_dt='$prd_frst_dt',
            prd_prss_dt=CASE WHEN '$prd_prss_dt'!='' THEN '$prd_prss_dt' END,
            prd_prss_dt_2=CASE WHEN '$prd_prss_dt_2'!='' THEN '$prd_prss_dt_2' END,
            prd_lst_dt='$prd_lst_dt',
            prd_prss_dt_tbc='$prd_prss_dt_tbc',
            prd_prv_only='$prd_prv_only',
            prd_dts_info='$prd_dts_info',
            prd_prss_wrdng='$prd_prss_wrdng',
            prd_tbc_nt='$prd_tbc_nt',
            prd_dt_nt='$prd_dt_nt',
            prd_tr='$prd_tr',
            prd_coll='$prd_coll',
            prd_thtr_nt='$prd_thtr_nt',
            prd_clss='$prd_clss',
            prd_othr_prts='$prd_othr_prts',
            prd_cst_nt='$prd_cst_nt'";
      if(!mysqli_query($link, $sql)) {$error='Error adding production data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $prd_id=mysqli_insert_id($link);

      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_insrtn.inc.php';
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PRODUCTION HAS BEEN ADDED TO THE DATABASE: '.html($prd_nm_session);
    header('Location: '.$prd_id.'/'.$prd_url);
    exit();
  }

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prd_id=cln($_POST['prd_id']);
    $sql= "SELECT prd_id, prd_nm, prd_sbnm, prd_frst_dt, prd_prss_dt, prd_prss_dt_2, prd_lst_dt, DATE_FORMAT(prd_crtd, '%d %b %Y at %H:%i') AS prd_crtd, DATE_FORMAT(prd_updtd, '%d %b %Y at %H:%i') AS prd_updtd, DATE_FORMAT(prd_frst_dt, '%a, %d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%a, %d %b %Y') AS prd_lst_dt_dsply, prd_prss_dt_tbc, prd_prv_only, prd_dts_info, prd_prss_wrdng, prd_tbc_nt, prd_dt_nt, prd_tr, prd_coll, prd_thtr_nt, prd_clss, prd_othr_prts, prd_cst_nt, thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
          FROM prd
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE prd_id='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production and theatre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $prd_nm=html($row['prd_nm']);
    $prd_sbnm=html($row['prd_sbnm']);
    $prd_crtd=html($row['prd_crtd']);
    $prd_updtd=html($row['prd_updtd']);
    $prd_frst_dt=html($row['prd_frst_dt']);
    $prd_prss_dt=html($row['prd_prss_dt']);
    $prd_prss_dt_2=html($row['prd_prss_dt_2']);
    $prd_lst_dt=html($row['prd_lst_dt']);
    $prd_prss_dt_tbc=html($row['prd_prss_dt_tbc']);
    $prd_prv_only=html($row['prd_prv_only']);
    $prd_dts_info=html($row['prd_dts_info']);
    $prd_prss_wrdng=html($row['prd_prss_wrdng']);
    $prd_tbc_nt=html($row['prd_tbc_nt']);
    $prd_dt_nt=html($row['prd_dt_nt']);
    $prd_tr=html($row['prd_tr']); if($row['prd_tr']=='2') {$tr_dsply=' [TOUR]';} elseif($row['prd_tr']=='3') {$tr_dsply=' [TOUR LEG]';} else {$tr_dsply='';}
    $prd_coll=html($row['prd_coll']); if($row['prd_coll']=='2') {$coll_dsply=' [COLLECTION]';} elseif($row['prd_coll']=='3') {$coll_dsply=' [PART OF COLLECTION]';} else {$coll_dsply='';}
    $prd_thtr_nt=html($row['prd_thtr_nt']);
    $prd_clss=html($row['prd_clss']); if($prd_clss=='1') {$prd_clss_dsply='';} elseif($prd_clss=='2') {$prd_clss_dsply='AMATEUR ';} else {$prd_clss_dsply='DRAMA SCHOOL ';}
    $prd_othr_prts=html($row['prd_othr_prts']);
    $prd_cst_nt=html($row['prd_cst_nt']);
    if($row['sbthtr_nm']) {$sbthtr_nm=';;'.$row['sbthtr_nm']; $sbthtr_nm_tb=': '.$row['sbthtr_nm'];} else {$sbthtr_nm=''; $sbthtr_nm_tb='';}
    if($row['thtr_lctn']) {$thtr_lctn='::'.$row['thtr_lctn']; $thtr_lctn_tb=' ('.$row['thtr_lctn'].')';} else {$thtr_lctn=''; $thtr_lctn_tb='';}
    if($row['thtr_sffx_num']) {$thtr_sffx_num='--'.$row['thtr_sffx_num'];} else {$thtr_sffx_num='';}
    $thtr_nm=html($row['thtr_nm'].$sbthtr_nm.$thtr_lctn.$thtr_sffx_num);

    if($row['prd_tbc_nt']) {$prd_tbc_nt_tb=': '.$row['prd_tbc_nt'];} else {$prd_tbc_nt_tb='';}
    if($row['prd_dts_info']=='4') {$prd_dts_tb='Dates TBC'.$prd_tbc_nt_tb;}
    else
    { if($row['prd_frst_dt_dsply']!==$row['prd_lst_dt_dsply'])
      { $prd_dts_tb=$row['prd_frst_dt_dsply'].' - ';
        if($row['prd_dts_info']=='3') {$prd_dts_tb .= 'TBC';}
        else {if($row['prd_dts_info']=='2') {$prd_dts_tb .= $row['prd_lst_dt_dsply'];} else {$prd_dts_tb .= $row['prd_lst_dt_dsply'];}}
      }
      else
      { if($row['prd_dts_info']=='3') {$prd_dts_tb='Dates TBC';}
        else {if($row['prd_dts_info']=='2') {$prd_dts_tb=$row['prd_frst_dt_dsply'].' only';} else {$prd_dts_tb=$row['prd_frst_dt_dsply'].' only';}}
      }
    }
    $pagetab='Edit: '.$prd_nm.' ('.html($row['thtr_nm'].$sbthtr_nm_tb.$thtr_lctn_tb.': '.$prd_dts_tb.')').' | TheatreBase';
    $pagetitle=$prd_nm;
    $pagesubtitle='Edit this existing production.';

    $sql= "SELECT mat_nm, frmt_nm, mat_sffx_num FROM prdmat
          INNER JOIN mat ON matid=mat_id INNER JOIN frmt ON frmtid=frmt_id
          WHERE prdid='$prd_id' ORDER BY mat_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['mat_sffx_num']) {$mat_sffx_num='--'.$row['mat_sffx_num'];} else {$mat_sffx_num='';}
      $mats[]=$row['mat_nm'].';;'.$row['frmt_nm'].$mat_sffx_num;
    }
    if(!empty($mats)) {$mat_list=html(implode(',,', $mats));} else {$mat_list='';}

    $sql= "SELECT pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_sffx_num, COALESCE(pt_alph, pt_nm)pt_alph
          FROM prdpt
          INNER JOIN pt ON ptid=pt_id
          WHERE prdid='$prd_id'
          ORDER BY pt_yr_wrttn ASC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['pt_yr_strtd']) {$pt_yr_strtd=$row['pt_yr_strtd'].';;';} else {$pt_yr_strtd='';}
      if($row['pt_yr_strtd_c']) {$pt_yr_strtd='c'.$pt_yr_strtd;}
      if($row['pt_yr_wrttn_c']) {$pt_yr_wrttn='c'.$row['pt_yr_wrttn'];} else {$pt_yr_wrttn=$row['pt_yr_wrttn'];}
      if($row['pt_sffx_num']) {$pt_sffx_num='--'.$row['pt_sffx_num'];} else {$pt_sffx_num='';}
      $pts[]=$row['pt_nm'].'##'.$pt_yr_strtd.$pt_yr_wrttn.$pt_sffx_num;
    }
    if(!empty($pts)) {$pt_list=html(implode(',,', $pts));} else {$pt_list='';}

    $sql="SELECT prd_id FROM prd WHERE tr_ov='$prd_id' ORDER BY prd_frst_dt ASC, tr_lg_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring tour leg data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$tr_lg_ids[]=$row['prd_id'];}
    if(!empty($tr_lg_ids)) {$tr_lg_list=html(implode(',,', $tr_lg_ids));} else {$tr_lg_list='';}

    $sql="SELECT coll_sbhdr_id, coll_sbhdr FROM prdcoll_sbhdr WHERE coll_ov='$prd_id' ORDER BY coll_sbhdr_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring collection subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$coll_sg_sbhdrs[$row['coll_sbhdr_id']]=array('coll_sbhdr'=>$row['coll_sbhdr'], 'coll_sgs'=>array());}

    $sql= "SELECT p2.coll_sbhdrid, p2.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.prd_id=p2.coll_ov
          WHERE p1.prd_id='$prd_id' ORDER BY p2.prd_frst_dt ASC, p2.coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring collection segment data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      if(empty($coll_sg_sbhdrs)) {$coll_sg_sbhdrs['1']=array('coll_sbhdr'=>NULL, 'coll_sgs'=>array());}
      while($row=mysqli_fetch_array($result))
      {
        if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
        $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sgs'][]=$row['prd_id'];
      }
    }

    if(!empty($coll_sg_sbhdrs))
    {
      $coll_sg_array=array();
      foreach($coll_sg_sbhdrs as $coll_sg_sbhdr)
      {
        if($coll_sg_sbhdr['coll_sbhdr']) {$coll_sbhdr=$coll_sg_sbhdr['coll_sbhdr'].'==';} else {$coll_sbhdr='';}
        $coll_sg_array[]=$coll_sbhdr.implode(',,', $coll_sg_sbhdr['coll_sgs']);
      }
      $coll_sg_list=html(implode('@@', $coll_sg_array));
    }
    else {$coll_sg_list='';}

    $sql= "SELECT rep1 AS prdid_rep, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph FROM prdrep INNER JOIN prd ON rep1=prd_id
          WHERE rep2='$prd_id'
          UNION
          SELECT rep2 AS prdid_rep, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph FROM prdrEP INNER JOIN prd ON rep2=prd_id
          WHERE rep1='$prd_id'
          ORDER BY prd_frst_dt ASC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring rep data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rep_ids[]=$row['prdid_rep'];}
    if(!empty($rep_ids)) {$rep_list=html(implode(',,', $rep_ids));} else {$rep_list='';}

    $sql= "SELECT prdrn1 AS prdrn_id, prd_frst_dt FROM prdrn INNER JOIN prd ON prdrn1=prd_id WHERE prdrn2='$prd_id'
          UNION
          SELECT prdrn2 AS prdrn_id, prd_frst_dt FROM prdrn INNER JOIN prd ON prdrn2=prd_id WHERE prdrn1='$prd_id'
          ORDER BY prd_frst_dt ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production run data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$prdrn_ids[]=$row['prdrn_id'];}
    if(!empty($prdrn_ids)) {$prdrn_list=html(implode(',,', $prdrn_ids));} else {$prdrn_list='';}

    $sql= "SELECT prd_vrsn_nm
          FROM prdprd_vrsn
          INNER JOIN prd_vrsn ON prd_vrsnid=prd_vrsn_id
          WHERE prdid='$prd_id'
          ORDER BY prd_vrsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring prod version data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$prd_vrsns[]=$row['prd_vrsn_nm'];}
    if(!empty($prd_vrsns)) {$prd_vrsn_list=html(implode(',,', $prd_vrsns));} else {$prd_vrsn_list='';}

    $sql= "SELECT txt_vrsn_nm
          FROM prdtxt_vrsn
          INNER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE prdid='$prd_id'
          ORDER BY txt_vrsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring text version data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$txt_vrsns[]=$row['txt_vrsn_nm'];}
    if(!empty($txt_vrsns)) {$txt_vrsn_list=html(implode(',,', $txt_vrsns));} else {$txt_vrsn_list='';}

    $sql= "SELECT ctgry_nm
          FROM prdctgry
          INNER JOIN ctgry ON ctgryid=ctgry_id
          WHERE prdid='$prd_id'
          ORDER BY ctgry_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ctgrys[]=$row['ctgry_nm'];}
    if(!empty($ctgrys)) {$ctgry_list=html(implode(',,', $ctgrys));} else {$ctgry_list='';}

    $sql= "SELECT gnr_nm
          FROM prdgnr
          INNER JOIN gnr ON gnrid=gnr_id
          WHERE prdid='$prd_id'
          ORDER BY gnr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$gnrs[]=$row['gnr_nm'];}
    if(!empty($gnrs)) {$gnr_list=html(implode(',,', $gnrs));} else {$gnr_list='';}

    $sql= "SELECT ftr_nm
          FROM prdftr
          INNER JOIN ftr ON ftrid=ftr_id
          WHERE prdid='$prd_id'
          ORDER BY ftr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring feature data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ftrs[]=$row['ftr_nm'];}
    if(!empty($ftrs)) {$ftr_list=html(implode(',,', $ftrs));} else {$ftr_list='';}

    $sql= "SELECT thm_nm
          FROM prdthm
          INNER JOIN thm ON thmid=thm_id
          WHERE prdid='$prd_id'
          ORDER BY thm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$thms[]=$row['thm_nm'];}
    if(!empty($thms)) {$thm_list=html(implode(',,', $thms));} else {$thm_list='';}

    $sql= "SELECT sttngid FROM prdsttng_tm WHERE prdid='$prd_id' GROUP BY sttngid
          UNION
          SELECT sttngid FROM prdsttng_lctn WHERE prdid='$prd_id' GROUP BY sttngid
          UNION
          SELECT sttngid FROM prdsttng_plc WHERE prdid='$prd_id' GROUP BY sttngid
          ORDER BY sttngid ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting group data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$sttngs[$row['sttngid']]=array('tms'=>array(), 'tm_spns'=>array(), 'lctns'=>array(), 'plcs'=>array());}

    $sql= "SELECT sttngid, tm_nm, sttng_tm_nt1, sttng_tm_nt2 FROM prdsttng_tm
          INNER JOIN tm ON sttng_tmid=tm_id
          WHERE prdid='$prd_id'
          ORDER BY sttng_tm_ordr";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting time data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['sttng_tm_nt1']) {$sttng_tm_nt1=$row['sttng_tm_nt1'].'::';} else {$sttng_tm_nt1='';}
      if($row['sttng_tm_nt2']) {$sttng_tm_nt2=';;'.$row['sttng_tm_nt2'];} else {$sttng_tm_nt2='';}
      $sttngs[$row['sttngid']]['tms'][]=$sttng_tm_nt1.$row['tm_nm'].$sttng_tm_nt2;
    }

    $sql= "SELECT sttng_id, tm_spn FROM prdsttng
          WHERE prdid='$prd_id' AND tm_spn=1
          ORDER BY sttng_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting time span data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$sttngs[$row['sttng_id']]['tm_spns'][]=$row['tm_spn'];}

    $sql= "SELECT sttngid, lctn_id, lctn_nm, lctn_sffx_num, sttng_lctn_nt1, sttng_lctn_nt2 FROM prdsttng_lctn
          INNER JOIN lctn ON sttng_lctnid=lctn_id
          WHERE prdid='$prd_id'
          ORDER BY sttng_lctn_ordr";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$lctn_sffx_num='';}
      if($row['sttng_lctn_nt1']) {$sttng_lctn_nt1=$row['sttng_lctn_nt1'].'::';} else {$sttng_lctn_nt1='';}
      if($row['sttng_lctn_nt2']) {$sttng_lctn_nt2=';;'.$row['sttng_lctn_nt2'];} else {$sttng_lctn_nt2='';}
      $sttngs[$row['sttngid']]['lctns'][$row['lctn_id']]=array('lctn'=>$sttng_lctn_nt1.$row['lctn_nm'].$lctn_sffx_num.$sttng_lctn_nt2, 'lctn_alts'=>array());
    }

    $sql= "SELECT psl.sttngid, psl.sttng_lctnid, lctn_nm, lctn_sffx_num
          FROM prdsttng_lctn psl
          INNER JOIN rel_lctn ON psl.sttng_lctnid=rel_lctn1 INNER JOIN prdsttng_lctn_alt psla ON rel_lctn2=psla.sttng_lctn_altid
          INNER JOIN lctn ON psla.sttng_lctn_altid=lctn_id
          WHERE psl.prdid='$prd_id'
          AND psl.prdid=psla.prdid AND psl.sttngid=psla.sttngid AND psl.sttng_lctnid=psla.sttng_lctnid
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting alternate location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$lctn_sffx_num='';}
      $sttngs[$row['sttngid']]['lctns'][$row['sttng_lctnid']]['lctn_alts'][]=$row['lctn_nm'].$lctn_sffx_num;
    }

    $sql= "SELECT sttngid, plc_nm, sttng_plc_nt1, sttng_plc_nt2 FROM prdsttng_plc
          INNER JOIN plc ON sttng_plcid=plc_id
          WHERE prdid='$prd_id'
          ORDER BY sttng_plc_ordr";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting place data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['sttng_plc_nt1']) {$sttng_plc_nt1=$row['sttng_plc_nt1'].'::';} else {$sttng_plc_nt1='';}
      if($row['sttng_plc_nt2']) {$sttng_plc_nt2=';;'.$row['sttng_plc_nt2'];} else {$sttng_plc_nt2='';}
      $sttngs[$row['sttngid']]['plcs'][]=$sttng_plc_nt1.$row['plc_nm'].$sttng_plc_nt2;
    }

    if(!empty($sttngs))
    {
      $sttng_array=array();
      foreach($sttngs as $sttng)
      {
        if(!empty($sttng['tms'])) {$tm_list=implode('//', $sttng['tms']);} else {$tm_list='';}
        if(!empty($sttng['tm_spns'])) {$tm_spn='*';} else {$tm_spn='';}
        if(!empty($sttng['lctns']))
        {
          $sttng_lctn_array=array();
          foreach($sttng['lctns'] as $sttng_lctn)
          {
            if(!empty($sttng_lctn['lctn_alts'])) {$lctn_alt_list='||'.implode('>>', $sttng_lctn['lctn_alts']);} else {$lctn_alt_list='';}
            $sttng_lctn_array[]=$sttng_lctn['lctn'].$lctn_alt_list;
          }
          $lctn_list='##'.implode('//', $sttng_lctn_array);
        }
        else {$lctn_list='';}
        if(!empty($sttng['plcs'])) {$plc_list='++'.implode('//', $sttng['plcs']);} else {$plc_list='';}
        $sttng_array[]=$tm_list.$tm_spn.$lctn_list.$plc_list;
      }
      $sttng_list=html(implode(',,', $sttng_array));
    }
    else {$sttng_list='';}

    $sql= "SELECT wri_rl_id, wri_rl, src_mat_rl
          FROM prdwrirl
          WHERE prdid='$prd_id'
          ORDER BY wri_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring writer (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['wri_rl']) {if(!preg_match('/:$/', $row['wri_rl'])) {$wri_rl=$row['wri_rl'].'::';} else {$wri_rl=$row['wri_rl'].' ::';}} else {$wri_rl='';}
      if($row['src_mat_rl']) {if(!preg_match('/:$/', $row['src_mat_rl'])) {$src_mat_rl=$row['src_mat_rl'].'::';} else {$src_mat_rl=$row['src_mat_rl'].' ::';}} else {$src_mat_rl='';}
      if($row['src_mat_rl']) {$src_mat_pls='++';} else {$src_mat_pls='';}
      $wri_rls[$row['wri_rl_id']]=array('src_mat_rl'=>$src_mat_rl, 'src_mat_pls'=>$src_mat_pls, 'wri_rl'=>$wri_rl, 'src_mats'=>array(), 'wris'=>array());
    }

    $sql= "SELECT wri_rlid, mat_nm, mat_sffx_num, frmt_nm
          FROM prdsrc_mat
          INNER JOIN mat ON src_matid=mat_id
          INNER JOIN frmt ON frmtid=frmt_id
          WHERE prdid='$prd_id'
          ORDER BY src_mat_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring source material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['mat_sffx_num']) {$mat_sffx_num='--'.$row['mat_sffx_num'];} else {$mat_sffx_num='';}
      $wri_rls[$row['wri_rlid']]['src_mats'][]=$row['mat_nm'].';;'.$row['frmt_nm'].$mat_sffx_num;
    }

    $sql= "SELECT wri_rlid, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, comp_bool
          FROM prdwri
          INNER JOIN comp ON wri_compid=comp_id
          WHERE prdid='$prd_id' AND wri_prsnid=0
          UNION
          SELECT wri_rlid, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, comp_bool
          FROM prdwri
          INNER JOIN prsn ON wri_prsnid=prsn_id
          WHERE prdid='$prd_id' AND wri_compid=0
          ORDER BY wri_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring writer data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['org_wri']) {$org_wri='*';} else {$org_wri='';}
          if($row['src_wri']) {$src_wri='**';} else {$src_wri='';}
          if($row['grntr']) {$grntr='***';} else {$grntr='';}
          if($row['wri_sb_rl']) {$wri_sb_rl=$row['wri_sb_rl'].'~~';} else {$wri_sb_rl='';}
          if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_wri_nm=$wri_sb_rl.$row['comp_nm1'].$comp_sffx_num.$org_wri.$src_wri.$grntr.'||';
        }
        else
        {$comp_wri_nm='';}
        $prsn_wri_nm='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['org_wri']) {$org_wri='*';} else {$org_wri='';}
          if($row['src_wri']) {$src_wri='**';} else {$src_wri='';}
          if($row['grntr']) {$grntr='***';} else {$grntr='';}
          if($row['wri_sb_rl']) {$wri_sb_rl=$row['wri_sb_rl'].'~~';} else {$wri_sb_rl='';}
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_wri_nm=$wri_sb_rl.$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num.$org_wri.$src_wri.$grntr;
        }
        else
        {$prsn_wri_nm='';}
        $comp_wri_nm='';
      }
      $wri_rls[$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_wri_nm'=>$comp_wri_nm, 'prsn_wri_nm'=>$prsn_wri_nm, 'wricomp_ppl'=>array());
    }

    $sql= "SELECT wri_rlid, wri_compid, wri_sb_rl, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM prdwri
          INNER JOIN prsn ON wri_prsnid=prsn_id
          WHERE prdid='$prd_id' AND wri_compid!=0
          ORDER BY wri_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring writer (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['wri_sb_rl']) {$wri_sb_rl=$row['wri_sb_rl'].'~~';} else {$wri_sb_rl='';}
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $wri_rls[$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=$wri_sb_rl.$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num;
    }

    if(!empty($wri_rls))
    {
      $wri_array=array();
      foreach($wri_rls as $wri_rl)
      {
        $wri_comp_ppl_array=array();
        foreach($wri_rl['wris'] as $wri)
        {
          $wricomp_ppl_list=implode('//', $wri['wricomp_ppl']);
          $wri_comp_ppl_array[]=$wri['comp_wri_nm'].$wri['prsn_wri_nm'].$wricomp_ppl_list;
        }
        if(!empty($wri_comp_ppl_array)) {$wri_comp_ppl_list=implode('>>', $wri_comp_ppl_array);} else {$wri_comp_ppl_list='';}
        if(!empty($wri_rl['src_mats'])) {$src_mats_list=implode('>>', $wri_rl['src_mats']);} else {$src_mats_list='';}
        $wri_array[]=$wri_rl['src_mat_rl'].$src_mats_list.$wri_rl['src_mat_pls'].$wri_rl['wri_rl'].$wri_comp_ppl_list;
      }
      $wri_list=html(implode(',,', $wri_array));
    }
    else
    {$wri_list='';}

    $sql= "SELECT prdcr_rl_id, prdcr_rl
          FROM prdprdcrrl
          WHERE prdid='$prd_id'
          ORDER BY prdcr_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring producer (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prdcr_rl']) {if(!preg_match('/:$/', $row['prdcr_rl'])) {$prdcr_rl=$row['prdcr_rl'].'::';} else {$prdcr_rl=$row['prdcr_rl'].' ::';}} else {$prdcr_rl='';}
      $prdcr_rls[$row['prdcr_rl_id']]=array('prdcr_rl'=>$prdcr_rl, 'prdcrs'=>array());
    }

    $sql= "SELECT prdcr_rlid, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, prdcr_sb_rl, prdcr_ordr, grntr, comp_bool
          FROM prdprdcr
          INNER JOIN comp ON prdcr_compid=comp_id
          WHERE prdid='$prd_id' AND prdcr_prsnid=0
          UNION
          SELECT prdcr_rlid, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prdcr_sb_rl, prdcr_ordr, grntr, comp_bool
          FROM prdprdcr
          INNER JOIN prsn ON prdcr_prsnid=prsn_id
          WHERE prdid='$prd_id' AND prdcr_compid=0
          ORDER BY prdcr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring producer data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['grntr']) {$grntr='***';} else {$grntr='';}
          if($row['prdcr_sb_rl']) {$prdcr_sb_rl=$row['prdcr_sb_rl'].'~~';} else {$prdcr_sb_rl='';}
          if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_prdcr_nm=$prdcr_sb_rl.$row['comp_nm1'].$comp_sffx_num.$grntr.'||';
        }
        else
        {$comp_prdcr_nm='';}
        $prsn_prdcr_nm='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['grntr']) {$grntr='***';} else {$grntr='';}
          if($row['prdcr_sb_rl']) {$prdcr_sb_rl=$row['prdcr_sb_rl'].'~~';} else {$prdcr_sb_rl='';}
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_prdcr_nm=$prdcr_sb_rl.$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num.$grntr;
        }
        else
        {$prsn_prdcr_nm='';}
        $comp_prdcr_nm='';
      }
      $prdcr_rls[$row['prdcr_rlid']]['prdcrs'][$row['comp_id']]=array('comp_prdcr_nm'=>$comp_prdcr_nm, 'prsn_prdcr_nm'=>$prsn_prdcr_nm, 'comp_rls'=>array());
    }

    $sql= "SELECT prdcr_rlid, prdcr_compid, prdcr_comp_rl_id, prdcr_comprl
          FROM prdprdcr pp
          INNER JOIN prdprdcr_comprl ppcr ON pp.prdid=ppcr.prdid
          WHERE pp.prdid='$prd_id' AND prdcr_comp_rlid=prdcr_comp_rl_id
          GROUP BY prdcr_rlid, prdcr_compid, prdcr_comp_rl_id
          ORDER BY prdcr_comp_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring producer (company people roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prdcr_comprl']) {$prdcr_comprl=$row['prdcr_comprl'].'~~';} else {$prdcr_comprl='';}
      $prdcr_rls[$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['comp_rls'][$row['prdcr_comp_rl_id']]=array('prdcr_comprl'=>$prdcr_comprl, 'prdcrcomp_ppl'=>array());
    }

    $sql= "SELECT prdcr_rlid, prdcr_compid, prdcr_comp_rlid, prdcr_sb_rl, prdcr_crdt, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM prdprdcr
          INNER JOIN prsn ON prdcr_prsnid=prsn_id
          WHERE prdid='$prd_id' AND prdcr_compid!=0
          ORDER BY prdcr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring producer (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prdcr_crdt']) {$prdcr_crdt='*';} else {$prdcr_crdt='';}
      if($row['prdcr_sb_rl']) {$prdcr_sb_rl=$row['prdcr_sb_rl'].'^^';} else {$prdcr_sb_rl='';}
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $prdcr_rls[$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['comp_rls'][$row['prdcr_comp_rlid']]['prdcrcomp_ppl'][]=$prdcr_sb_rl.$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num.$prdcr_crdt;
    }

    if(!empty($prdcr_rls))
    {
      $prdcr_array=array();
      foreach($prdcr_rls as $prdcr_rl)
      {
        $prdcr_comp_rls_array=array();
        foreach($prdcr_rl['prdcrs'] as $prdcr)
        {
          $prdcr_comp_ppl_array=array();
          foreach($prdcr['comp_rls'] as $comp_rl)
          {
            $prdcrcomp_ppl_list=implode('¬¬', $comp_rl['prdcrcomp_ppl']);
            $prdcr_comp_ppl_array[]=$comp_rl['prdcr_comprl'].$prdcrcomp_ppl_list;
          }
          if(!empty($prdcr_comp_ppl_array)) {$prdcr_comp_ppl_list=implode('//', $prdcr_comp_ppl_array);} else {$prdcr_comp_ppl_list='';}
          $prdcr_comp_rls_array[]=$prdcr['comp_prdcr_nm'].$prdcr['prsn_prdcr_nm'].$prdcr_comp_ppl_list;
        }
        if(!empty($prdcr_comp_rls_array)) {$prdcr_comp_rl_list=implode('>>', $prdcr_comp_rls_array);} else {$prdcr_comp_rl_list='';}
        $prdcr_array[]=$prdcr_rl['prdcr_rl'].$prdcr_comp_rl_list;
      }
      $prdcr_list=html(implode(',,', $prdcr_array));
    }
    else
    {$prdcr_list='';}

    $sql= "SELECT prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM prdprf
          INNER JOIN prsn ON prf_prsnid=prsn_id
          WHERE prdid='$prd_id'
          GROUP BY prsn_id
          ORDER BY prf_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring performer (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prsn_sffx_num']) {$prf_prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prf_prsn_sffx_num='';}
      $prfs[$row['prsn_id']]=array('prsn_frst_nm'=>$row['prsn_frst_nm'], 'prsn_lst_nm'=>$row['prsn_lst_nm'], 'prsn_sffx_num'=>$prf_prsn_sffx_num, 'prf_rls'=>array());
    }

    $sql= "SELECT prf_prsnid, prf_rl, prf_rl_lnk, prf_rl_dscr, prf_rl_alt
          FROM prdprf
          WHERE prdid='$prd_id'
          ORDER BY prf_ordr ASC, prf_rl_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result)
    {$error='Error acquiring performer (person) role data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prf_rl_lnk'] && $row['prf_rl_lnk']!==$row['prf_rl']){$perf_rl_lnk='||'.$row['prf_rl_lnk'];} else {$perf_rl_lnk='';}
      if($row['prf_rl_dscr']) {$perf_rl_dscr=';;'.$row['prf_rl_dscr'];} else {$perf_rl_dscr='';}
      if($row['prf_rl_alt']) {$perf_rl_alt='*';} else {$perf_rl_alt='';}
      $prfs[$row['prf_prsnid']]['prf_rls'][]=$row['prf_rl'].$perf_rl_lnk.$perf_rl_dscr.$perf_rl_alt;
    }
    if(!empty($prfs))
    {
      $prf_array=array();
      foreach($prfs as $prf)
      {
        $prf_rl_list=implode('//', $prf['prf_rls']);
        $prf_array[]=$prf['prsn_frst_nm'].';;'.$prf['prsn_lst_nm'].$prf['prsn_sffx_num'].'::'.$prf_rl_list;
      }
      $prf_list=html(implode(',,', $prf_array));
    }
    else
    {$prf_list='';}

    $sql= "SELECT prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM prdus
          INNER JOIN prsn ON us_prsnid=prsn_id
          WHERE prdid='$prd_id'
          GROUP BY prsn_id
          ORDER BY us_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring understudy (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prsn_sffx_num']) {$us_sffx_num='--'.$row['prsn_sffx_num'];} else {$us_sffx_num='';}
      $uss[$row['prsn_id']]=array('prsn_frst_nm'=>$row['prsn_frst_nm'], 'prsn_lst_nm'=>$row['prsn_lst_nm'], 'prsn_sffx_num'=>$us_sffx_num, 'us_rls'=>array());
    }

    $sql= "SELECT us_prsnid, us_rl, us_rl_lnk, us_rl_dscr, us_rl_alt
          FROM prdus
          WHERE prdid='$prd_id'
          ORDER BY us_ordr ASC, us_rl_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring understudy (person) rl data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['us_rl_lnk'] && $row['us_rl_lnk']!==$row['us_rl']) {$us_rl_lnk='||'.$row['us_rl_lnk'];} else {$us_rl_lnk='';}
      if($row['us_rl_dscr']) {$us_rl_dscr=';;'.$row['us_rl_dscr'];} else {$us_rl_dscr='';}
      if($row['us_rl_alt']) {$us_rl_alt='*';} else {$us_rl_alt='';}
      $uss[$row['us_prsnid']]['us_rls'][]=$row['us_rl'].$us_rl_lnk.$us_rl_dscr.$us_rl_alt;
    }
    if(!empty($uss))
    {
      $us_array=array();
      foreach($uss as $us)
      {
        $us_rl_list=implode('//', $us['us_rls']);
        $us_array[]=$us['prsn_frst_nm'].';;'.$us['prsn_lst_nm'].$us['prsn_sffx_num'].'::'.$us_rl_list;
      }
      $us_list=html(implode(',,', $us_array));
    }
    else
    {$us_list='';}

    $sql= "SELECT mscn_rl_id, mscn_rl
          FROM prdmscnrl
          WHERE prdid='$prd_id'
          ORDER BY mscn_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring musician (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['mscn_rl']) {$mscn_rl=$row['mscn_rl'].'::';} else {$mscn_rl='';}
      $mscn_rls[$row['mscn_rl_id']]=array('mscn_rl'=>$mscn_rl, 'mscns'=>array());
    }

    $sql= "SELECT mscn_rlid, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, mscn_sb_rl, mscn_ordr, comp_bool
          FROM prdmscn
          INNER JOIN comp ON mscn_compid=comp_id
          WHERE prdid='$prd_id' AND mscn_prsnid=0
          UNION
          SELECT mscn_rlid, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, mscn_sb_rl, mscn_ordr, comp_bool
          FROM prdmscn
          INNER JOIN prsn ON mscn_prsnid=prsn_id
          WHERE prdid='$prd_id' AND mscn_compid=0
          ORDER BY mscn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring musician data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['mscn_sb_rl']) {$mscn_sb_rl=$row['mscn_sb_rl'].'~~';} else {$mscn_sb_rl='';}
        if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_mscn_nm=$mscn_sb_rl.$row['comp_nm1'].$comp_sffx_num.'||';
        }
        else
        {$comp_mscn_nm='';}
        $prsn_mscn_nm='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['mscn_sb_rl']) {$mscn_sb_rl=$row['mscn_sb_rl'].'~~';} else {$mscn_sb_rl='';}
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_mscn_nm=$mscn_sb_rl.$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num;
        }
        else
        {$prsn_mscn_nm='';}
        $comp_mscn_nm='';
      }
      $mscn_rls[$row['mscn_rlid']]['mscns'][$row['comp_id']]=array('comp_mscn_nm'=>$comp_mscn_nm, 'prsn_mscn_nm'=>$prsn_mscn_nm, 'comp_rls'=>array());
    }

    $sql= "SELECT mscn_rlid, mscn_compid, mscn_comp_rl_id, mscn_comprl
          FROM prdmscn pp
          INNER JOIN prdmscn_comprl ppcr ON pp.prdid=ppcr.prdid
          WHERE pp.prdid='$prd_id' AND mscn_comp_rlid=mscn_comp_rl_id
          GROUP BY mscn_rlid, mscn_compid, mscn_comp_rl_id
          ORDER BY mscn_comp_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring musician (company people roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['mscn_comprl']) {$mscn_comprl=$row['mscn_comprl'].'~~';} else {$mscn_comprl='';}
      $mscn_rls[$row['mscn_rlid']]['mscns'][$row['mscn_compid']]['comp_rls'][$row['mscn_comp_rl_id']]=array('mscn_comprl'=>$mscn_comprl, 'mscncomp_ppl'=>array());
    }

    $sql= "SELECT mscn_rlid, mscn_compid, mscn_comp_rlid, mscn_sb_rl, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM prdmscn
          INNER JOIN prsn ON mscn_prsnid=prsn_id
          WHERE prdid='$prd_id' AND mscn_compid!=0
          ORDER BY mscn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring musician (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['mscn_sb_rl']) {$mscn_sb_rl=$row['mscn_sb_rl'].'^^';} else {$mscn_sb_rl='';}
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $mscn_rls[$row['mscn_rlid']]['mscns'][$row['mscn_compid']]['comp_rls'][$row['mscn_comp_rlid']]['mscncomp_ppl'][]=$mscn_sb_rl.$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num;
    }

    if(!empty($mscn_rls))
    {
      $mscn_array=array();
      foreach($mscn_rls as $mscn_rl)
      {
        $mscn_comp_rls_array=array();
        foreach($mscn_rl['mscns'] as $mscn)
        {
          $mscn_comp_ppl_array=array();
          foreach($mscn['comp_rls'] as $comp_rl)
          {
            $mscncomp_ppl_list=implode('¬¬', $comp_rl['mscncomp_ppl']);
            $mscn_comp_ppl_array[]=$comp_rl['mscn_comprl'].$mscncomp_ppl_list;
          }
          if(!empty($mscn_comp_ppl_array)) {$mscn_comp_ppl_list=implode('//', $mscn_comp_ppl_array);} else {$mscn_comp_ppl_list='';}
          $mscn_comp_rls_array[]=$mscn['comp_mscn_nm'].$mscn['prsn_mscn_nm'].$mscn_comp_ppl_list;
        }
        if(!empty($mscn_comp_rls_array)) {$mscn_comp_rl_list=implode('>>', $mscn_comp_rls_array);} else {$mscn_comp_rl_list='';}
        $mscn_array[]=$mscn_rl['mscn_rl'].$mscn_comp_rl_list;
      }
      $mscn_list=html(implode(',,', $mscn_array));
    }
    else
    {$mscn_list='';}

    $sql= "SELECT crtv_rl_id, crtv_rl
          FROM prdcrtvrl
          WHERE prdid='$prd_id'
          ORDER BY crtv_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring creative (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['crtv_rl']) {$crtv_rl=$row['crtv_rl'].'::';} else {$crtv_rl='';}
      $crtv_rls[$row['crtv_rl_id']]=array('crtv_rl'=>$crtv_rl, 'crtvs'=>array());
    }

    $sql= "SELECT crtv_rlid, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, crtv_sb_rl, crtv_ordr, comp_bool
          FROM prdcrtv
          INNER JOIN comp ON crtv_compid=comp_id
          WHERE prdid='$prd_id' AND crtv_prsnid=0
          UNION
          SELECT crtv_rlid, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, crtv_sb_rl, crtv_ordr, comp_bool
          FROM prdcrtv
          INNER JOIN prsn ON crtv_prsnid=prsn_id
          WHERE prdid='$prd_id' AND crtv_compid=0
          ORDER BY crtv_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring creative data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['crtv_sb_rl']) {$crtv_sb_rl=$row['crtv_sb_rl'].'~~';} else {$crtv_sb_rl='';}
          if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_crtv_nm=$crtv_sb_rl.$row['comp_nm1'].$comp_sffx_num.'||';
        }
        else
        {$comp_crtv_nm='';}
        $prsn_crtv_nm='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['crtv_sb_rl']) {$crtv_sb_rl=$row['crtv_sb_rl'].'~~';} else {$crtv_sb_rl='';}
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_crtv_nm=$crtv_sb_rl.$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num;
        }
        else
        {$prsn_crtv_nm='';}
        $comp_crtv_nm='';
      }
      $crtv_rls[$row['crtv_rlid']]['crtvs'][$row['comp_id']]=array('comp_crtv_nm'=>$comp_crtv_nm, 'prsn_crtv_nm'=>$prsn_crtv_nm, 'crtvcomp_ppl'=>array());
    }

    $sql= "SELECT crtv_rlid, crtv_compid, crtv_sb_rl, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM prdcrtv
          INNER JOIN prsn ON crtv_prsnid=prsn_id
          WHERE prdid='$prd_id' AND crtv_compid!=0
          ORDER BY crtv_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring creative (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['crtv_sb_rl']) {$crtv_sb_rl=$row['crtv_sb_rl'].'~~';} else {$crtv_sb_rl='';}
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $crtv_rls[$row['crtv_rlid']]['crtvs'][$row['crtv_compid']]['crtvcomp_ppl'][]=$crtv_sb_rl.$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num;
    }

    if(!empty($crtv_rls))
    {
      $crtv_array=array();
      foreach($crtv_rls as $crtv_rl)
      {
        $crtv_comp_ppl_array=array();
        foreach($crtv_rl['crtvs'] as $crtv)
        {
          $crtvcomp_ppl_list=implode('//', $crtv['crtvcomp_ppl']);
          $crtv_comp_ppl_array[]=$crtv['comp_crtv_nm'].$crtv['prsn_crtv_nm'].$crtvcomp_ppl_list;
        }
        if(!empty($crtv_comp_ppl_array)) {$crtv_comp_ppl_list=implode('>>', $crtv_comp_ppl_array);} else {$crtv_comp_ppl_list='';}
        $crtv_array[]=$crtv_rl['crtv_rl'].$crtv_comp_ppl_list;
      }
      $crtv_list=html(implode(',,', $crtv_array));
    }
    else
    {$crtv_list='';}

    $sql= "SELECT prdtm_rl_id, prdtm_rl
          FROM prdprdtmrl
          WHERE prdid='$prd_id'
          ORDER BY prdtm_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production team (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prdtm_rl']) {$prdtm_rl=$row['prdtm_rl'].'::';} else {$prdtm_rl='';}
      $prdtm_rls[$row['prdtm_rl_id']]=array('prdtm_rl'=>$prdtm_rl, 'prdtms'=>array());
    }

    $sql= "SELECT prdtm_rlid, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, prdtm_sb_rl, prdtm_ordr, comp_bool
          FROM prdprdtm
          INNER JOIN comp ON prdtm_compid=comp_id
          WHERE prdid='$prd_id' AND prdtm_prsnid=0
          UNION
          SELECT prdtm_rlid, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prdtm_sb_rl, prdtm_ordr, comp_bool
          FROM prdprdtm
          INNER JOIN prsn ON prdtm_prsnid=prsn_id
          WHERE prdid='$prd_id' AND prdtm_compid=0
          ORDER BY prdtm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production team data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['prdtm_sb_rl']) {$prdtm_sb_rl=$row['prdtm_sb_rl'].'~~';} else {$prdtm_sb_rl='';}
          if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_prdtm_nm=$prdtm_sb_rl.$row['comp_nm1'].$comp_sffx_num.'||';
        }
        else
        {$comp_prdtm_nm='';}
        $prsn_prdtm_nm='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['prdtm_sb_rl']) {$prdtm_sb_rl=$row['prdtm_sb_rl'].'~~';} else {$prdtm_sb_rl='';}
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_prdtm_nm=$prdtm_sb_rl.$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num;
        }
        else
        {$prsn_prdtm_nm='';}
        $comp_prdtm_nm='';
      }
      $prdtm_rls[$row['prdtm_rlid']]['prdtms'][$row['comp_id']]=array('comp_prdtm_nm'=>$comp_prdtm_nm, 'prsn_prdtm_nm'=>$prsn_prdtm_nm, 'prdtmcomp_ppl'=>array());
    }

    $sql= "SELECT prdtm_rlid, prdtm_compid, prdtm_sb_rl, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM prdprdtm
          INNER JOIN prsn ON prdtm_prsnid=prsn_id
          WHERE prdid='$prd_id' AND prdtm_compid!=0
          ORDER BY prdtm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production team (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prdtm_sb_rl']) {$prdtm_sb_rl=$row['prdtm_sb_rl'].'~~';} else {$prdtm_sb_rl='';}
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $prdtm_rls[$row['prdtm_rlid']]['prdtms'][$row['prdtm_compid']]['prdtmcomp_ppl'][]=$prdtm_sb_rl.$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num;
    }

    if(!empty($prdtm_rls))
    {
      $prdtm_array=array();
      foreach($prdtm_rls as $prdtm_rl)
      {
        $prdtm_comp_ppl_array=array();
        foreach($prdtm_rl['prdtms'] as $prdtm)
        {
          $prdtmcomp_ppl_list=implode('//', $prdtm['prdtmcomp_ppl']);
          $prdtm_comp_ppl_array[]=$prdtm['comp_prdtm_nm'].$prdtm['prsn_prdtm_nm'].$prdtmcomp_ppl_list;
        }
        if(!empty($prdtm_comp_ppl_array)) {$prdtm_comp_ppl_list=implode('>>', $prdtm_comp_ppl_array);} else {$prdtm_comp_ppl_list='';}
        $prdtm_array[]=$prdtm_rl['prdtm_rl'].$prdtm_comp_ppl_list;
      }
      $prdtm_list=html(implode(',,', $prdtm_array));
    }
    else
    {$prdtm_list='';}

    $sql= "SELECT ssn_nm
          FROM prdssn
          INNER JOIN ssn ON ssnid=ssn_id
          WHERE prdid='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring season data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$row=mysqli_fetch_array($result); $ssn_nm=html($row['ssn_nm']);} else {$ssn_nm='';}

    $sql= "SELECT fstvl_nm
          FROM prdfstvl
          INNER JOIN fstvl ON fstvlid=fstvl_id
          WHERE prdid='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring festival data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$row=mysqli_fetch_array($result); $fstvl_nm=html($row['fstvl_nm']);} else {$fstvl_nm='';}

    $sql= "SELECT comp_nm, comp_sffx_num, crs_typ_nm, crs_yr_strt, crs_yr_end, crs_sffx_num
          FROM prdcrs
          INNER JOIN crs ON crsid=crs_id
          INNER JOIN comp ON crs_schlid=comp_id
          INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE prdid='$prd_id'
          ORDER BY crs_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].';;'.$row['crs_yr_end'];} else {$crs_yr=$row['crs_yr_strt'];}
      if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
      if($row['crs_sffx_num']) {$crs_sffx_num='--'.$row['crs_sffx_num'];} else {$crs_sffx_num='';}
      $crs=$row['comp_nm'].$comp_sffx_num.'::'.$row['crs_typ_nm'].'##'.$crs_yr.$crs_sffx_num;
      $crss[]=$crs;
    }
    if(!empty($crss)) {$crs_list=html(implode(',,', $crss));} else {$crs_list='';}

    $sql= "SELECT comp_nm, comp_sffx_num, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, DATE_FORMAT(rvw_dt, '%d-%m-%Y') AS rvw_dt_dsply, rvw_url
          FROM prdrvw
          INNER JOIN comp ON rvw_pub_compid=comp_id
          INNER JOIN prsn ON rvw_crtc_prsnid=prsn_id
          WHERE prdid='$prd_id'
          ORDER BY rvw_dt ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring review data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_sffx_num']) {$rvw_pub_sffx_num='--'.$row['comp_sffx_num'];} else {$rvw_pub_sffx_num='';}
      if($row['prsn_sffx_num']) {$rvw_crtc_sffx_num='--'.$row['prsn_sffx_num'];} else {$rvw_crtc_sffx_num='';}
      $rvws[]=$row['comp_nm'].$rvw_pub_sffx_num.'||'.$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$rvw_crtc_sffx_num.'##'.$row['rvw_dt_dsply'].'::'.$row['rvw_url'];
    }
    if(!empty($rvws)) {$rvw_list=html(implode(',,', $rvws));} else {$rvw_list='';}

    $sql= "SELECT prd_alt_nm, prd_alt_nm_dscr
          FROM prd_alt_nm
          WHERE prdid='$prd_id'
          ORDER BY prd_alt_nm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production alternate name data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prd_alt_nm_dscr']) {$alt_nm_dscr='::'.$row['prd_alt_nm_dscr'];} else {$alt_nm_dscr='';}
      $alt_nms[]=$row['prd_alt_nm'].$alt_nm_dscr;
    }
    if(!empty($alt_nms)) {$alt_nm_list=html(implode(',,', $alt_nms));} else {$alt_nm_list='';}

    $textarea='';
    $prd_id=html($prd_id);
    $frmactn='?edit';
    $edit='1';
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $prd_id=cln($_POST['prd_id']);
    $edit='1';
    include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_vldtn.inc.php';

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $sql= "SELECT prd_nm, DATE_FORMAT(prd_crtd, '%d %b %Y at %H:%i') AS prd_crtd, DATE_FORMAT(prd_updtd, '%d %b %Y at %H:%i') AS prd_updtd, DATE_FORMAT(prd_frst_dt, '%a, %d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%a, %d %b %Y') AS prd_lst_dt_dsply, prd_tbc_nt, prd_dts_info, thtr_nm, sbthtr_nm, thtr_lctn
            FROM prd
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE prd_id='$prd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring production and theatre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $prd_crtd=html($row['prd_crtd']);
      $prd_updtd=html($row['prd_updtd']);
      if($row['prd_tbc_nt']) {$prd_tbc_nt_tb=': '.$row['prd_tbc_nt'];} else {$prd_tbc_nt_tb='';}
      if($row['prd_dts_info']=='4') {$prd_dts_tb='Dates TBC'.$prd_tbc_nt_tb;}
      else
      { if($row['prd_frst_dt_dsply']!==$row['prd_lst_dt_dsply'])
        { $prd_dts_tb=$row['prd_frst_dt_dsply'].' - ';
          if($row['prd_dts_info']=='3') {$prd_dts_tb .= 'TBC';}
          else {if($row['prd_dts_info']=='2') {$prd_dts_tb .= $row['prd_lst_dt_dsply'];} else {$prd_dts_tb .= $row['prd_lst_dt_dsply'];}}
        }
        else
        { if($row['prd_dts_info']=='3') {$prd_dts_tb='Dates TBC';}
          else {if($row['prd_dts_info']=='2') {$prd_dts_tb=$row['prd_frst_dt_dsply'].' only';} else {$prd_dts_tb=$row['prd_frst_dt_dsply'].' only';}}
        }
      }
      if($row['sbthtr_nm']) {$sbthtr_nm_tb=': '.$row['sbthtr_nm'];} else {$sbthtr_nm_tb='';}
      if($row['thtr_lctn']) {$thtr_lctn_tb=' ('.$row['thtr_lctn'].')';} else {$thtr_lctn_tb='';}
      $pagetab='Edit: '.html($row['prd_nm'].' ('.$row['thtr_nm'].$sbthtr_nm_tb.$thtr_lctn_tb.': '.$prd_dts_tb.')').' | TheatreBase';
      $pagetitle=html($row['prd_nm']);
      $pagesubtitle='Edit this existing production.';
      $prd_nm=$_POST['prd_nm'];
      $prd_sbnm=$_POST['prd_sbnm'];
      $mat_list=$_POST['mat_list'];
      $pt_list=$_POST['pt_list'];
      $prd_frst_dt=$_POST['prd_frst_dt'];
      $prd_prss_dt=$_POST['prd_prss_dt'];
      $prd_prss_dt_2=$_POST['prd_prss_dt_2'];
      $prd_lst_dt=$_POST['prd_lst_dt'];
      $prd_prss_wrdng=$_POST['prd_prss_wrdng'];
      $prd_tbc_nt=$_POST['prd_tbc_nt'];
      $prd_dt_nt=$_POST['prd_dt_nt'];
      $tr_lg_list=$_POST['tr_lg_list'];
      if($prd_tr=='2') {$tr_dsply=' [TOUR]';} elseif($prd_tr=='3') {$tr_dsply=' [TOUR LEG]';} else {$tr_dsply='';}
      $coll_sg_list=$_POST['coll_sg_list'];
      if($prd_coll=='2') {$coll_dsply=' [COLLECTION]';} elseif($prd_coll=='3') {$coll_dsply=' [PART OF COLLECTION]';} else {$coll_dsply='';}
      $rep_list=$_POST['rep_list'];
      $prdrn_list=$_POST['prdrn_list'];
      $thtr_nm=$_POST['thtr_nm'];
      $prd_thtr_nt=$_POST['prd_thtr_nt'];
      if($prd_clss=='1') {$prd_clss_dsply='';} elseif($prd_clss=='2') {$prd_clss_dsply='AMATEUR ';} else {$prd_clss_dsply='DRAMA SCHOOL ';}
      $prd_vrsn_list=$_POST['prd_vrsn_list'];
      $txt_vrsn_list=$_POST['txt_vrsn_list'];
      $ctgry_list=$_POST['ctgry_list'];
      $gnr_list=$_POST['gnr_list'];
      $ftr_list=$_POST['ftr_list'];
      $thm_list=$_POST['thm_list'];
      $sttng_list=$_POST['sttng_list'];
      $wri_list=$_POST['wri_list'];
      $prdcr_list=$_POST['prdcr_list'];
      $prf_list=$_POST['prf_list'];
      $prd_cst_nt=$_POST['prd_cst_nt'];
      $us_list=$_POST['us_list'];
      $mscn_list=$_POST['mscn_list'];
      $crtv_list=$_POST['crtv_list'];
      $prdtm_list=$_POST['prdtm_list'];
      $ssn_nm=$_POST['ssn_nm'];
      $fstvl_nm=$_POST['fstvl_nm'];
      $crs_list=$_POST['crs_list'];
      $rvw_list=$_POST['rvw_list'];
      $alt_nm_list=$_POST['alt_nm_list'];
      $textarea=$_POST['textarea'];
      $errors['prd_add_edit_error']='**There are errors on this page that need amending before submission can be successful.**</br>';
      $prd_id=html($prd_id);
      $frmactn='?edit';
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE prd SET
            prd_nm='$prd_nm',
            prd_alph=CASE WHEN '$prd_alph'!='' THEN '$prd_alph' END,
            prd_url='$prd_url',
            prd_sbnm='$prd_sbnm',
            prd_updtd=NOW(),
            prd_frst_dt='$prd_frst_dt',
            prd_prss_dt=CASE WHEN '$prd_prss_dt'!='' THEN '$prd_prss_dt' END,
            prd_prss_dt_2=CASE WHEN '$prd_prss_dt_2'!='' THEN '$prd_prss_dt_2' END,
            prd_lst_dt='$prd_lst_dt',
            prd_prss_dt_tbc='$prd_prss_dt_tbc',
            prd_prv_only='$prd_prv_only',
            prd_dts_info='$prd_dts_info',
            prd_prss_wrdng='$prd_prss_wrdng',
            prd_tbc_nt='$prd_tbc_nt',
            prd_dt_nt='$prd_dt_nt',
            prd_tr='$prd_tr',
            prd_coll='$prd_coll',
            prd_thtr_nt='$prd_thtr_nt',
            prd_clss='$prd_clss',
            prd_othr_prts='$prd_othr_prts',
            prd_cst_nt='$prd_cst_nt'
            WHERE prd_id='$prd_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating production data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdmat WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdpt WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="UPDATE prd SET tr_ov=NULL, tr_lg_ordr=NULL WHERE tr_ov='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error nullifying production (tour overview)-tour leg associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="UPDATE prd SET coll_ov=NULL, coll_sbhdrid=NULL, coll_ordr=NULL WHERE coll_ov='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error nullifying production (collection overview)-collection segment associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdcoll_sbhdr WHERE coll_ov='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production(collection overview)-collection subheader associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdrep WHERE rep1='$prd_id' OR rep2='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-rep associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdrn WHERE prdrn1='$prd_id' OR prdrn2='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-production run associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdprd_vrsn WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-prod version associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdtxt_vrsn WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-text version associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdctgry WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-category associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdgnr WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-genre associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdftr WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-feature associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdthm WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-theme associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdsttng WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdsttng_plc WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting (place) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdsttng_lctn WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting (location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdsttng_lctn_alt WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdsttng_tm WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting (time) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdwrirl WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-writer (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdwri WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-writer (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdsrc_mat WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-writer (source material) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdprdcrrl WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-producer (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdprdcr_comprl WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-producer (company people roles) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdprdcr WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-producer (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdprf WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-performer (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdus WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-understudy (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdmscnrl WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-musician (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdmscn_comprl WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-musician (company people roles) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdmscn WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-musician (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdcrtvrl WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-creative (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdcrtv WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-creative (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdprdtmrl WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-production team (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdprdtm WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-production team (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdssn WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-season associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdfstvl WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-festival associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdcrs WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-course associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prdrvw WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-review associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prd_alt_nm WHERE prdid='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-production alternate name associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_insrtn.inc.php';
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PRODUCTION HAS BEEN EDITED: '.html($prd_nm_session);
    header('Location: '.$prd_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $prd_id=cln($_POST['prd_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM awrdnomprds WHERE nom_prdid='$prd_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring award-production (nominee/winner) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Awards nomination/win';}

    if(count($assocs)>0)
    {$errors['prd_dlt']='**Production must have no associations before being deleted. Current associations: '.html(implode(' / ', $assocs)).'.**</br>';}

    if(count($errors)>0)
    {
      $sql= "SELECT prd_nm, DATE_FORMAT(prd_crtd, '%d %b %Y at %H:%i') AS prd_crtd,
            DATE_FORMAT(prd_updtd, '%d %b %Y at %H:%i') AS prd_updtd, DATE_FORMAT(prd_frst_dt, '%a, %d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%a, %d %b %Y') AS prd_lst_dt_dsply, prd_tbc_nt, prd_dts_info, prd_dt_nt, thtr_nm, sbthtr_nm, thtr_lctn
            FROM prd
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE prd_id='$prd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production and theatre details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['prd_tbc_nt']) {$prd_tbc_nt_tb=': '.$row['prd_tbc_nt'];} else {$prd_tbc_nt_tb='';}
      if($row['prd_dts_info']=='4') {$prd_dts_tb='Dates TBC'.$prd_tbc_nt_tb;}
      else
      { if($row['prd_frst_dt_dsply']!==$row['prd_lst_dt_dsply'])
        { $prd_dts_tb=$row['prd_frst_dt_dsply'].' - ';
          if($row['prd_dts_info']=='3') {$prd_dts_tb .= 'TBC';}
          else {if($row['prd_dts_info']=='2') {$prd_dts_tb .= $row['prd_lst_dt_dsply'];} else {$prd_dts_tb .= $row['prd_lst_dt_dsply'];}}
        }
        else
        { if($row['prd_dts_info']=='3') {$prd_dts_tb='Dates TBC';}
          else {if($row['prd_dts_info']=='2') {$prd_dts_tb=$row['prd_frst_dt_dsply'].' only';} else {$prd_dts_tb=$row['prd_frst_dt_dsply'].' only';}}
        }
      }
      if($row['sbthtr_nm']) {$sbthtr_nm_tb=': '.$row['sbthtr_nm'];} else {$sbthtr_nm_tb='';}
      if($row['thtr_lctn']) {$thtr_lctn_tb=' ('.$row['thtr_lctn'].')';} else {$thtr_lctn_tb='';}
      $pagetab='Edit: '.html($row['prd_nm'].' ('.$row['thtr_nm'].$sbthtr_nm_tb.$thtr_lctn_tb.': '.$prd_dts_tb.')').' | TheatreBase';
      $pagetitle=html($row['prd_nm']);
      $pagesubtitle='Edit this existing production.';
      $prd_crtd=html($row['prd_crtd']);
      $prd_updtd=html($row['prd_updtd']);
      $prd_nm=$_POST['prd_nm'];
      $prd_sbnm=$_POST['prd_sbnm'];
      $mat_list=$_POST['mat_list'];
      $pt_list=$_POST['pt_list'];
      $prd_frst_dt=$_POST['prd_frst_dt'];
      $prd_prss_dt=$_POST['prd_prss_dt'];
      $prd_prss_dt_2=$_POST['prd_prss_dt_2'];
      $prd_lst_dt=$_POST['prd_lst_dt'];
      if(isset($_POST['prd_prss_dt_tbc'])) {$prd_prss_dt_tbc='1';} else {$prd_prss_dt_tbc='0';}
      if(isset($_POST['prd_prv_only'])) {$prd_prv_only='1';} else {$prd_prv_only='0';}
      if($_POST['prd_dts_info']=='1') {$prd_dts_info='1';}
      if($_POST['prd_dts_info']=='2') {$prd_dts_info='2';}
      if($_POST['prd_dts_info']=='3') {$prd_dts_info='3';}
      if($_POST['prd_dts_info']=='4') {$prd_dts_info='4';}
      $prd_prss_wrdng=$_POST['prd_prss_wrdng'];
      $prd_tbc_nt=$_POST['prd_tbc_nt'];
      $prd_dt_nt=$_POST['prd_dt_nt'];
      $thtr_nm=$_POST['thtr_nm'];
      $prd_thtr_nt=$_POST['prd_thtr_nt'];
      if($_POST['prd_clss']=='1') {$prd_clss='1';}
      if($_POST['prd_clss']=='2') {$prd_clss='2';}
      if($_POST['prd_clss']=='3') {$prd_clss='3';}
      if($prd_clss=='1') {$prd_clss_dsply='';} elseif($prd_clss=='2') {$prd_clss_dsply='AMATEUR ';} else {$prd_clss_dsply='DRAMA SCHOOL ';}
      if($_POST['prd_tr']=='1') {$prd_tr='1'; $tr_dsply='';}
      if($_POST['prd_tr']=='2') {$prd_tr='2'; $tr_dsply=' [TOUR]';}
      if($_POST['prd_tr']=='3') {$prd_tr='3'; $tr_dsply=' [TOUR LEG]';}
      $tr_lg_list=$_POST['tr_lg_list'];
      $coll_sg_list=$_POST['coll_sg_list'];
      if($_POST['prd_coll']=='1') {$prd_coll='1'; $coll_dsply='';}
      if($_POST['prd_coll']=='2') {$prd_coll='2'; $coll_dsply=' [COLLECTION]';}
      if($_POST['prd_coll']=='3') {$prd_coll='3'; $coll_dsply=' [PART OF COLLECTION]';}
      $rep_list=$_POST['rep_list'];
      $prdrn_list=$_POST['prdrn_list'];
      $prd_vrsn_list=$_POST['prd_vrsn_list'];
      $txt_vrsn_list=$_POST['txt_vrsn_list'];
      $ctgry_list=$_POST['ctgry_list'];
      $gnr_list=$_POST['gnr_list'];
      $ftr_list=$_POST['ftr_list'];
      $thm_list=$_POST['thm_list'];
      $sttng_list=$_POST['sttng_list'];
      $wri_list=$_POST['wri_list'];
      $prdcr_list=$_POST['prdcr_list'];
      $prf_list=$_POST['prf_list'];
      if(isset($_POST['prd_othr_prts'])) {$prd_othr_prts='1';} else {$prd_othr_prts='0';}
      $prd_cst_nt=$_POST['prd_cst_nt'];
      $us_list=$_POST['us_list'];
      $mscn_list=$_POST['mscn_list'];
      $crtv_list=$_POST['crtv_list'];
      $prdtm_list=$_POST['prdtm_list'];
      $ssn_nm=$_POST['ssn_nm'];
      $fstvl_nm=$_POST['fstvl_nm'];
      $crs_list=$_POST['crs_list'];
      $rvw_list=$_POST['rvw_list'];
      $alt_nm_list=$_POST['alt_nm_list'];
      $textarea=$_POST['textarea'];
      $prd_id=html($prd_id);
      $frmactn='?edit';
      $edit='1';
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT prd_nm, prd_tr, prd_coll, DATE_FORMAT(prd_frst_dt, '%a, %d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%a, %d %b %Y') AS prd_lst_dt_dsply, prd_tbc_nt, prd_dts_info, prd_clss, thtr_nm, sbthtr_nm, thtr_lctn
            FROM prd
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE prd_id='$prd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production and theatre details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php';  exit();}
      $row=mysqli_fetch_array($result);
      if($row['prd_tbc_nt']) {$prd_tbc_nt_tb=': '.$row['prd_tbc_nt'];} else {$prd_tbc_nt_tb='';}
      if($row['prd_dts_info']=='4') {$prd_dts_tb='Dates TBC'.$prd_tbc_nt_tb;}
      else
      { if($row['prd_frst_dt_dsply']!==$row['prd_lst_dt_dsply'])
        { $prd_dts_tb=$row['prd_frst_dt_dsply'].' - ';
          if($row['prd_dts_info']=='3') {$prd_dts_tb .= 'TBC';}
          else {if($row['prd_dts_info']=='2') {$prd_dts_tb .= $row['prd_lst_dt_dsply'];} else {$prd_dts_tb .= $row['prd_lst_dt_dsply'];}}
        }
        else
        { if($row['prd_dts_info']=='3') {$prd_dts_tb='Dates TBC';}
          else {if($row['prd_dts_info']=='2') {$prd_dts_tb=$row['prd_frst_dt_dsply'].' only';} else {$prd_dts_tb=$row['prd_frst_dt_dsply'].' only';}}
        }
      }
      if($row['sbthtr_nm']) {$sbthtr_nm_tb=': '.$row['sbthtr_nm'];} else {$sbthtr_nm_tb='';}
      if($row['thtr_lctn']) {$thtr_lctn_tb=' ('.$row['thtr_lctn'].')';} else {$thtr_lctn_tb='';}
      $pagetab='Delete confirmation: '.html($row['prd_nm'].' ('.$row['thtr_nm'].$sbthtr_nm_tb.$thtr_lctn_tb.': '.$prd_dts_tb.')').' | TheatreBase';
      $pagetitle=html($row['prd_nm']);
      if($row['prd_clss']=='1') {$prd_clss_dsply='';} elseif($row['prd_clss']=='2') {$prd_clss_dsply='AMATEUR ';} else {$prd_clss_dsply='DRAMA SCHOOL ';}
      if($row['prd_tr']=='2') {$tr_dsply=' [TOUR]';} elseif($row['prd_tr']=='3') {$tr_dsply=' [TOUR LEG]';} else {$tr_dsply='';}
      if($row['prd_coll']=='2') {$coll_dsply=' [COLLECTION]';} elseif($row['prd_coll']=='3') {$coll_dsply=' [PART OF COLLECTION]';} else {$coll_dsply='';}
      $prd_id=html($prd_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $prd_id=cln($_POST['prd_id']);

    $sql="SELECT prd_nm FROM prd WHERE prd_id='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $prd_nm_session=$row['prd_nm'];

    $sql="DELETE FROM prdmat WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdpt WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdsrc_mat WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-source material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="UPDATE prd SET tr_ov=NULL, tr_lg_ordr=NULL WHERE tr_ov='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error nullifying production-tour associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="UPDATE prd SET coll_ov=NULL, coll_sbhdrid=NULL, coll_ordr=NULL WHERE coll_ov='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error nullifying production-collection associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdcoll_sbhdr WHERE coll_ov='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-collection subheader associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdrep WHERE rep1='$prd_id' OR rep2='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-rep associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprd_vrsn WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-prod version associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdtxt_vrsn WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-text version associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdctgry WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-category associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdgnr WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-genre associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdftr WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-feature associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdthm WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-theme associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdsttng WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdsttng_tm WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting (time) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdsttng_lctn WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting (location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdsttng_lctn_alt WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdsttng_plc WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-setting (place) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdwrirl WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-writer (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdwri WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-writer associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdcrrl WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-producer (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdcr_comprl WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-producer (company role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdcr WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-producer associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprf WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-performer (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdus WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-understudy (person) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdmscnrl WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-musician (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdmscn_comprl WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-musician (company role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdmscn WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-musician associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdcrtvrl WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-creative (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdcrtv WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-creative associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdtmrl WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-prod team (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdtm WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-prod team associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdssn WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-season associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdfstvl WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-festival associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdcrs WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-course associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdrvw WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-review associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prd_alt_nm WHERE prdid='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-production alternate name associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prd WHERE prd_id='$prd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PRODUCTION HAS BEEN DELETED FROM THE DATABASE: '.html($prd_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prd_id=cln($_POST['prd_id']);
    $sql= "SELECT prd_url
          FROM prd
          WHERE prd_id='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['prd_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $prd_id=cln($_GET['prd_id']);
  $prd_url=cln($_GET['prd_url']);

  $sql="SELECT 1 FROM prd WHERE prd_id='$prd_id' AND prd_url='$prd_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT p1.prd_nm, p1.prd_sbnm, p1.prd_frst_dt AS prd_frst_dt_num, DATE_FORMAT(p1.prd_frst_dt, '%a, %d %b %Y') AS prd_frst_dt, DATE_FORMAT(p1.prd_prss_dt, '%a, %d %b %Y') AS prd_prss_dt, DATE_FORMAT(p1.prd_prss_dt_2, '%a, %d %b %Y') AS prd_prss_dt_2, DATE_FORMAT(p1.prd_lst_dt, '%a, %d %b %Y') AS prd_lst_dt, p1.prd_prss_dt_tbc, p1.prd_prv_only, p1.prd_dts_info, p1.prd_prss_wrdng AS prd_prss_wrdng, p1.prd_tbc_nt, p1.prd_dt_nt, p1.prd_tr, p1.prd_coll, p1.prd_thtr_nt, p1.prd_clss, p1.prd_othr_prts, p1.prd_cst_nt, t1.thtr_nm, t1.sbthtr_nm, t1.thtr_lctn, t1.thtr_url, t1.thtr_tr_ov, t2.thtr_url AS t2_thtr_url, CASE WHEN (p1.prd_frst_dt<=p2.prd_prss_dt AND p1.prd_lst_dt>=p2.prd_prss_dt) THEN DATE_FORMAT(p2.prd_prss_dt, '%a, %d %b %Y') ELSE NULL END AS p2_prd_prss_dt, CASE WHEN (p1.prd_frst_dt<=p2.prd_prss_dt_2 AND p1.prd_lst_dt>=p2.prd_prss_dt_2) THEN DATE_FORMAT(p2.prd_prss_dt_2, '%a, %d %b %Y') ELSE NULL END AS p2_prd_prss_dt_2, CASE WHEN (p1.prd_frst_dt<=p2.prd_prss_dt AND p1.prd_lst_dt>=p2.prd_prss_dt) THEN p2.prd_prss_wrdng ELSE NULL END AS p2_prd_prss_wrdng
          FROM prd p1
          INNER JOIN thtr t1 ON p1.thtrid=t1.thtr_id LEFT OUTER JOIN thtr t2 ON t1.srthtrid=t2.thtr_id LEFT OUTER JOIN prd p2 ON p1.tr_ov=p2.prd_id
          WHERE p1.prd_id='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production and theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $prd_nm=html($row['prd_nm']);
    $prd_sbnm=html($row['prd_sbnm']);
    $prd_frst_dt_num=html($row['prd_frst_dt_num']);
    $prd_frst_dt=html($row['prd_frst_dt']);
    $prd_prss_dt=html($row['prd_prss_dt']); if($row['p2_prd_prss_dt']) {$prd_prss_dt=html($row['p2_prd_prss_dt']);};
    if($row['prd_prss_dt_2']) {$prd_prss_dt_2=' and '.html($row['prd_prss_dt_2']);}
    elseif($row['p2_prd_prss_dt_2']) {$prd_prss_dt_2=' and '.html($row['p2_prd_prss_dt_2']);} else {$prd_prss_dt_2='';}
    $prd_lst_dt=html($row['prd_lst_dt']);
    $prd_prss_dt_tbc=$row['prd_prss_dt_tbc'];
    if($row['prd_prv_only']) {$prd_prv_only=' (previews only)';} else {$prd_prv_only='';}
    if($row['prd_dts_info']=='2') {$prd_bkng_untl='1';} else {$prd_bkng_untl='';}
    if($row['prd_dts_info']=='3') {$prd_lst_dt_tbc='1';} else {$prd_lst_dt_tbc='';}
    if($row['prd_dts_info']=='4') {$prd_dts_tbc='1';} else {$prd_dts_tbc='';}
    if(!$row['prd_prss_wrdng'] && !$row['p2_prd_prss_wrdng']) {$prd_prss_wrdng='Press Night';}
    elseif($row['prd_prss_wrdng']) {$prd_prss_wrdng=html($row['prd_prss_wrdng']);}
    elseif($row['p2_prd_prss_wrdng']) {$prd_prss_wrdng=html($row['p2_prd_prss_wrdng']);}
    if($row['prd_tbc_nt']) {$prd_tbc_nt=': '.html($row['prd_tbc_nt']);} else {$prd_tbc_nt='';}
    $prd_dt_nt=html($row['prd_dt_nt']);
    if($row['prd_clss']=='1') {$prd_clss_dsply='';} elseif($row['prd_clss']=='2') {$prd_clss_dsply='AMATEUR ';} else {$prd_clss_dsply='DRAMA SCHOOL ';}
    if($row['prd_tr']=='2') {$tr_lg=''; $tr_dsply=' [TOUR]';} elseif($row['prd_tr']=='3') {$tr_lg='1'; $tr_dsply=' [TOUR LEG]';} else {$tr_lg=''; $tr_dsply='';}
    if($row['prd_coll']=='2') {$coll_dsply=' [COLLECTION]';} elseif($row['prd_coll']=='3') {$coll_dsply=' [PART OF COLLECTION]';} else {$coll_dsply='';}
    $prd_thtr_nt=html($row['prd_thtr_nt']);
    $prd_othr_prts=$row['prd_othr_prts'];
    $prd_cst_nt=html($row['prd_cst_nt']);
    $thtr_nm=html($row['thtr_nm']);
    $sbthtr_nm=html($row['sbthtr_nm']);
    $thtr_lctn=html($row['thtr_lctn']);
    $thtr_url=html($row['thtr_url']);
    $thtr_tr_ov=$row['thtr_tr_ov'];
    $t2_thtr_url=html($row['t2_thtr_url']);
    if(!$prd_dts_tbc)
    { if($prd_frst_dt!==$prd_lst_dt)
      { if($prd_prss_dt)
        { if($prd_frst_dt!==$prd_prss_dt) {$dt_dsply='<tr><td><b>First Preview:</b></td><td>'.$prd_frst_dt.'</td></tr>
                                           <tr><td><b>'.$prd_prss_wrdng.':</b></td><td>'.$prd_prss_dt.$prd_prss_dt_2.'</td></tr>';}
          else {$dt_dsply='<tr><td><b>Opening Performance:</b></td><td>'.$prd_frst_dt.'</td></tr>';}
        }
        else
        { if(!$prd_prss_dt_tbc) {$dt_dsply='<tr><td><b>First Performance:</b></td><td>'.$prd_frst_dt.'</td></tr>';}
          else {$dt_dsply='<tr><td><b>First Preview:</b></td><td>'.$prd_frst_dt.'</td></tr>
                     <tr><td><b>'.$prd_prss_wrdng.':</b></td><td><em>TBC</em></td></tr>';}
        }

        if(!$prd_bkng_untl && !$prd_lst_dt_tbc) {$dt_dsply .= '<tr><td><b>Last Performance:</b></td><td>'.$prd_lst_dt.'</td></tr>';}
        elseif($prd_bkng_untl) {$dt_dsply .= '<tr><td><b>Booking until:</b></td><td><em>'.$prd_lst_dt.'</em></td></tr>';}
        elseif($prd_lst_dt_tbc) {$dt_dsply .= '<tr><td><b>Last Performance:</b></td><td><em>TBC</em></td></tr>';}
      }
      else
      {
        if(!$prd_bkng_untl && !$prd_lst_dt_tbc) {$dt_dsply='<tr><td><b>Performs:</b></td><td>'.$prd_frst_dt. ' </td></tr>';}
        elseif($prd_bkng_untl) {$dt_dsply='<tr><td><b>Performs (booking until):</b></td><td><em>'.$prd_frst_dt.'</em></td></tr>';}
        elseif($prd_lst_dt_tbc) {$dt_dsply='<tr><td><em>TBC</em></td></tr>';}
      }
    }
    else {$dt_dsply='<tr><td><em>TBC'.$prd_tbc_nt.'</em></td></tr>';}

    if($prd_dts_tbc) {$prd_dts='Dates TBC'.$prd_tbc_nt;}
    else
    {
      if($prd_frst_dt!==$prd_lst_dt) {$prd_dts=$prd_frst_dt.' - '; if($prd_lst_dt_tbc) {$prd_dts .= 'TBC';} else {if($prd_bkng_untl) {$prd_dts .= '<em>'.$prd_lst_dt.'</em>';} else {$prd_dts .= $prd_lst_dt;}}}
      else {if($prd_lst_dt_tbc) {$prd_dts='Dates TBC';} else {if($prd_bkng_untl) {$prd_dts=$prd_frst_dt.' only';} else {$prd_dts=$prd_frst_dt.' only';}}}
    }

    if($thtr_lctn) {$thtr_lctn=' ('.$thtr_lctn.')';} else {$thtr_lctn='';}
    if(!$thtr_tr_ov)
    {
      if(!preg_match('/TBC$/', $thtr_nm))
      {
        if($sbthtr_nm)
        {
          if($t2_thtr_url) {$thtr_dsply='<a href="/theatre/'.$t2_thtr_url.'">'.$thtr_nm.'</a>: <a href="/theatre/'.$thtr_url.'">'.$sbthtr_nm.'</a>'.$thtr_lctn; $sbthtr_nm_tb=': '.$sbthtr_nm;}
          else {$thtr_dsply=$thtr_nm.': <a href="/theatre/'.$thtr_url.'">'.$sbthtr_nm.'</a>'.$thtr_lctn; $sbthtr_nm_tb=': '.$sbthtr_nm;}
        }
        else {$thtr_dsply='<a href="/theatre/'.$thtr_url.'">'.$thtr_nm.'</a>'.$thtr_lctn; $sbthtr_nm_tb='';}
      }
      else {$thtr_dsply='<em>TBC</em>'; $sbthtr_nm_tb='';}
      $thtr_header='Theatre'; $thtr_div_id='theatre';
    }
    else
    {
      if(!preg_match('/TBC$/', $thtr_nm)) {$thtr_dsply='<a href="/tour-type/'.$thtr_url.'">'.$thtr_nm.'</a>';}
      else {$thtr_dsply='<em>TBC</em>';}
      $thtr_header='Tour type'; $thtr_div_id='tour-type'; $sbthtr_nm_tb='';
    }
    $pagetitle=$prd_nm;
    $pagetab=$prd_nm.' ('.$thtr_nm.$sbthtr_nm_tb.$thtr_lctn.': '.$prd_dts.')'.' | TheatreBase';

    $sql="SELECT tr_ov FROM prd WHERE prd_id='$prd_id' AND tr_ov IS NOT NULL";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring tour-overview prd_id: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0) {$id=$row['tr_ov'];} else {$id=$prd_id;}

    $awrd_prd_ids=array(); $rvw_ids=array();

    $sql= "SELECT mat_nm, mat_url, frmt_nm, frmt_url
          FROM prdmat
          INNER JOIN mat ON matid=mat_id INNER JOIN frmt ON frmtid=frmt_id
          WHERE prdid='$id'
          ORDER BY mat_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $mat_nm='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'"><em>'.html($row['mat_nm']).'</em> ('.html($row['frmt_nm']).')</a>';
      $mats[]=array('mat_nm'=>$mat_nm);
    }

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM prdpt
          INNER JOIN pt p1 ON ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE prdid='$id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM prdpt pp
          INNER JOIN pt p1 ON pp.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE prdid='$id' AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($pt_ids))
      {
        foreach($pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM prdpt WHERE ptid='$pt_id' AND prdid='$id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM prdpt pp
            INNER JOIN pt ON pp.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE prdid='$id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr);
      }

      if(!empty($sg_pt_ids))
      {
        foreach($sg_pt_ids as $sg_pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
        }
      }
    }

    $sql="SELECT coll_sbhdr_id, coll_sbhdr FROM prdcoll_sbhdr WHERE coll_ov='$prd_id' ORDER BY coll_sbhdr_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring collection subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$coll_sg_sbhdrs[$row['coll_sbhdr_id']]=array('coll_sbhdr'=>html($row['coll_sbhdr']), 'coll_sg_prds'=>array());}

    $sql= "SELECT coll_sbhdrid, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
          FROM prd
          INNER JOIN thtr ON thtrid=thtr_id
          WHERE coll_ov='$prd_id'
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring collection segment details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      if(empty($coll_sg_sbhdrs)) {$coll_sg_sbhdrs['1']=array('coll_sbhdr'=>NULL, 'coll_sg_prds'=>array());}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
        $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
        $awrd_prd_ids[]=$row['prd_id']; $rvw_ids[]=$row['prd_id'];
      }

      if(!$tr_lg) {$sql_in='prd_id';} else {$sql_in='tr_ov';}
      $sql= "SELECT coll_sbhdrid, prd_id, wri_rl_id, wri_rl, src_mat_rl FROM prd
            INNER JOIN prdwrirl ON $sql_in=prdid WHERE coll_ov='$prd_id'
            GROUP BY prd_id, wri_rl_id ORDER BY wri_rl_id";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer (roles) data (for production segments): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
        $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_prds'][$row['prd_id']]['wri_rls'][$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());
      }

      $sql= "SELECT coll_sbhdrid, prd_id, wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url FROM prd
            INNER JOIN prdsrc_mat ON $sql_in=prdid INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id WHERE coll_ov='$prd_id'
            GROUP BY prd_id, wri_rlid, mat_id ORDER BY src_mat_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring source material data (for production segments): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $src_mat_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['mat_nm']).'</a>';
        $src_mat_frmt_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['frmt_nm']).'</a>';
        if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
        $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_prds'][$row['prd_id']]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_url'=>$src_mat_url, 'src_mat_frmt_url'=>$src_mat_frmt_url, 'src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));
      }

      $sql= "SELECT coll_sbhdrid, prd_id, wri_rlid, comp_id, comp_nm, comp_url, wri_sb_rl, wri_ordr, comp_bool FROM prd
            INNER JOIN prdwri ON $sql_in=prdid INNER JOIN comp ON wri_compid=comp_id WHERE coll_ov='$prd_id' AND wri_prsnid=0 GROUP BY prd_id, wri_rlid, comp_id
            UNION
            SELECT coll_sbhdrid, prd_id, wri_rlid, prsn_id, prsn_fll_nm, prsn_url, wri_sb_rl, wri_ordr, comp_bool FROM prd
            INNER JOIN prdwri ON $sql_in=prdid INNER JOIN prsn ON wri_prsnid=prsn_id WHERE coll_ov='$prd_id' AND wri_compid=0 GROUP BY prd_id, wri_rlid, prsn_id
            ORDER BY wri_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer data (for production segments): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        if($row['wri_sb_rl']) {if(!preg_match('/^(:|;|,|\.)/', $row['wri_sb_rl'])) {$wri_sb_rl=' '.html($row['wri_sb_rl']).' ';} else {$wri_sb_rl=html($row['wri_sb_rl']).' ';}} else {$wri_sb_rl='';}
        if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
        $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_prds'][$row['prd_id']]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
      }

      $sql= "SELECT coll_sbhdrid, prd_id, wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm, prsn_url FROM prd
            INNER JOIN prdwri ON $sql_in=prdid INNER JOIN prsn ON wri_prsnid=prsn_id WHERE coll_ov='$prd_id' AND wri_compid!=0
            GROUP BY prd_id, wri_rlid, wri_compid, prsn_id ORDER BY wri_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer (company people) data (for production segments): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ' ;} else {$wri_sb_rl='';}
        if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
        $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_prds'][$row['prd_id']]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'wri_sb_rl'=>$wri_sb_rl);
      }
    }

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, coll_sbhdr
          FROM prd p1
          INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          LEFT OUTER JOIN prdcoll_sbhdr pcsh ON p1.coll_ov=pcsh.coll_ov AND p1.coll_sbhdrid=coll_sbhdr_id
          WHERE p1.prd_id='$prd_id'
          GROUP BY prd_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring collection overview details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $coll_ov_prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'coll_sbhdr'=>html($row['coll_sbhdr']), 'wri_rls'=>array(), 'sg_prds'=>array());
        $awrd_prd_ids[]=$row['prd_id'];
      }

      if(!$tr_lg) {$key_id='prdid'; $sql_in='INNER JOIN prdwrirl ON coll_ov=prdid';}
      else {$key_id='p1.coll_ov'; $sql_in='INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN prdwrirl ON p2.tr_ov=prdid';}
      $sql= "SELECT $key_id AS key_id, wri_rl_id, wri_rl, src_mat_rl FROM prd p1
            $sql_in WHERE p1.prd_id='$prd_id'
            GROUP BY $key_id, wri_rl_id ORDER BY wri_rl_id";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer (roles) data (for overview productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$coll_ov_prds[$row['key_id']]['wri_rls'][$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());}

      if(!$tr_lg) {$sql_in='INNER JOIN prdsrc_mat ON coll_ov=prdid';}
      else {$sql_in='INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN prdsrc_mat ON p2.tr_ov=prdid';}
      $sql= "SELECT $key_id AS key_id, wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url FROM prd p1
            $sql_in INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id WHERE p1.prd_id='$prd_id'
            GROUP BY $key_id, wri_rlid, mat_id ORDER BY src_mat_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring source material data (for overview productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $src_mat_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['mat_nm']).'</a>';
        $src_mat_frmt_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['frmt_nm']).'</a>';
        $coll_ov_prds[$row['key_id']]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_url'=>$src_mat_url, 'src_mat_frmt_url'=>$src_mat_frmt_url, 'src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));
      }

      if(!$tr_lg) {$sql_in='INNER JOIN prdwri ON coll_ov=prdid';}
      else {$sql_in='INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN prdwri ON p2.tr_ov=prdid';}
      $sql= "SELECT $key_id AS key_id, wri_rlid, comp_id, comp_nm, comp_url, wri_sb_rl, wri_ordr, comp_bool FROM prd p1
            $sql_in INNER JOIN comp ON wri_compid=comp_id WHERE p1.prd_id='$prd_id' AND wri_prsnid=0 GROUP BY $key_id, wri_rlid, comp_id
            UNION
            SELECT $key_id AS key_id, wri_rlid, prsn_id, prsn_fll_nm, prsn_url, wri_sb_rl, wri_ordr, comp_bool FROM prd p1
            $sql_in INNER JOIN prsn ON wri_prsnid=prsn_id WHERE p1.prd_id='$prd_id' AND wri_compid=0 GROUP BY $key_id, wri_rlid, prsn_id
            ORDER BY wri_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer data (for overview productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        if($row['wri_sb_rl']) {if(!preg_match('/^(:|;|,|\.)/', $row['wri_sb_rl'])) {$wri_sb_rl=' '.html($row['wri_sb_rl']).' ';} else {$wri_sb_rl=html($row['wri_sb_rl']).' ';}} else {$wri_sb_rl='';}
        $coll_ov_prds[$row['key_id']]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
      }

      $sql= "SELECT $key_id AS key_id, wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm, prsn_url FROM prd p1
            $sql_in INNER JOIN prsn ON wri_prsnid=prsn_id WHERE p1.prd_id='$prd_id' AND wri_compid!=0
            GROUP BY $key_id, wri_rlid, wri_compid, prsn_id ORDER BY wri_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer (company people) data (for overview productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ' ;} else {$wri_sb_rl='';}
        $coll_ov_prds[$row['key_id']]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'wri_sb_rl'=>$wri_sb_rl);
      }

      $sql= "SELECT p3.coll_ov, p3.prd_id, p3.prd_nm, p3.prd_url FROM prd p1
            INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN prd p3 ON p2.prd_id=p3.coll_ov
            WHERE p1.prd_id='$prd_id' AND p3.prd_id!='$prd_id'
            GROUP BY prd_id
            ORDER BY p3.prd_frst_dt ASC, p3.coll_sbhdrid ASC, p3.coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data (for overview productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$coll_ov_prds[$row['coll_ov']]['sg_prds'][]='<a href="/production/'.html($row['prd_id']).'/'.html($row['prd_url']).' ">'.html($row['prd_nm']).'</a>';}
    }

    if(!$tr_lg)
    {
      $sql= "SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prd
            INNER JOIN thtr ON thtrid=thtr_id
            WHERE tr_ov='$prd_id'
            ORDER BY prd_frst_dt DESC, tr_lg_ordr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring tour leg details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $tr_lg_prds[]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
        $awrd_prd_ids[]=$row['prd_id']; $rvw_ids[]=$row['prd_id'];
      }

      $sql="SELECT p2.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.prd_id=p2.coll_ov WHERE p1.tr_ov='$prd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segments of tour leg details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$awrd_prd_ids[]=$row['prd_id']; $rvw_ids[]=$row['prd_id'];}
    }
    else
    {
      $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm
            FROM prd p1
            INNER JOIN prd p2 ON p1.tr_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
            WHERE p1.prd_id='$prd_id' AND p2.coll_ov IS NULL
            GROUP BY prd_id
            UNION
            SELECT p3.prd_id, p3.prd_nm, p3.prd_url, DATE_FORMAT(p3.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p3.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p3.prd_dts_info, p3.prd_tbc_nt, thtr_fll_nm
            FROM prd p1
            INNER JOIN prd p2 ON p1.tr_ov=p2.prd_id INNER JOIN prd p3 ON p2.coll_ov=p3.prd_id INNER JOIN thtr ON p3.thtrid=thtr_id
            WHERE p1.prd_id='$prd_id'
            GROUP BY prd_id";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring tour overview details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $tr_ov_prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_prds'=>array());
          $awrd_prd_ids[]=$row['prd_id'];
        }

        $sql= "SELECT p2.coll_ov, p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm
              FROM prd p1
              INNER JOIN prd p2 ON p1.tr_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
              WHERE p1.prd_id='$prd_id' AND p2.coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id
              ORDER BY p2.prd_frst_dt DESC, p2.coll_sbhdrid ASC, p2.coll_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring productions (for tour overview productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $tr_ov_prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('sg_prd_nm'=>$prd_nm, 'sg_prd_nm_pln'=>html($row['prd_nm']), 'sg_prd_dts'=>$prd_dts, 'sg_thtr'=>$thtr);
          $awrd_prd_ids[]=$row['prd_id'];
        }
      }
    }

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, p2.prd_tbc_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdrep
          INNER JOIN prd p1 ON rep2=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE rep1='$id'
          GROUP BY prd_id
          UNION
          SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, p2.prd_tbc_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdrep
          INNER JOIN prd p1 ON rep1=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE rep2='$id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, COALESCE(prd_alph, prd_nm)prd_alph, prd_tbc_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdrep
          INNER JOIN prd p1 ON rep2=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE rep1='$id' AND coll_ov IS NULL
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, COALESCE(prd_alph, prd_nm)prd_alph, prd_tbc_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdrep
          INNER JOIN prd p1 ON rep1=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE rep2='$id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring rep productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $rep_ids[]=$row['prd_id'];
        $reps[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'sg_prds'=>array());
      }

      if(!empty($rep_ids))
      {
        foreach($rep_ids as $rep_id)
        {
          $sql="SELECT 1 FROM prdrep WHERE (rep1='$prd_id' AND rep2='$rep_id') OR (rep2='$prd_id' AND rep1='$rep_id')";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this production (as rep productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            $sql="SELECT wri_rl_id, wri_rl, src_mat_rl FROM prdwrirl WHERE prdid='$rep_id' GROUP BY prdid, wri_rl_id ORDER BY wri_rl_id";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring writer (roles) data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {$reps[$rep_id]['wri_rls'][$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());}

            $sql= "SELECT wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url FROM prdsrc_mat
                  INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id WHERE prdid='$rep_id'
                  GROUP BY prdid, wri_rlid, mat_id ORDER BY src_mat_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring source material data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {$reps[$rep_id]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));}

            $sql= "SELECT wri_rlid, wri_ordr, wri_sb_rl, comp_id, comp_nm FROM prdwri
                  INNER JOIN comp ON wri_compid=comp_id WHERE prdid='$rep_id' AND wri_prsnid=0
                  GROUP BY prdid, wri_rlid, comp_id
                  UNION
                  SELECT wri_rlid, wri_ordr, wri_sb_rl, prsn_id, prsn_fll_nm FROM prdwri
                  INNER JOIN prsn ON wri_prsnid=prsn_id WHERE prdid='$rep_id' AND wri_compid=0
                  GROUP BY prdid, wri_rlid, prsn_id
                  ORDER BY wri_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring writer data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ';} else {$wri_sb_rl='';}
              $reps[$rep_id]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
            }

            $sql= "SELECT wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm FROM prdwri
                  INNER JOIN prsn ON wri_prsnid=prsn_id WHERE prdid='$rep_id' AND wri_compid!=0
                  GROUP BY prdid, wri_rlid, wri_compid, prsn_id ORDER BY wri_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring writer (company people) data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ' ;} else {$wri_sb_rl='';}
              $reps[$rep_id]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>html($row['prsn_fll_nm']), 'wri_sb_rl'=>html($row['wri_sb_rl']));
            }
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, coll_sbhdrid, coll_ordr
            FROM prdrep
            INNER JOIN prd ON rep1=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE rep2='$id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            UNION
            SELECT coll_ov, prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, coll_sbhdrid, coll_ordr
            FROM prdrep
            INNER JOIN prd ON rep2=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE rep1='$id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment rep productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $sg_rep_ids[]=$row['prd_id'];
        $reps[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array());
      }

      if(!empty($sg_rep_ids))
      {
        foreach($sg_rep_ids as $sg_rep_id)
        {
          $sql= "SELECT coll_ov, wri_rl_id, src_mat_rl, wri_rl FROM prdwrirl
                INNER JOIN prd ON prdid=prd_id
                WHERE prdid='$sg_rep_id' AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prdid, wri_rl_id ORDER BY wri_rl_id ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring writer role data for segment productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {$reps[$row['coll_ov']]['sg_prds'][$sg_rep_id]['wri_rls'][$row['wri_rl_id']]=array('wri_rl'=>html($row['wri_rl']), 'src_mat_rl'=>html($row['src_mat_rl']), 'src_mats'=>array(), 'wris'=>array());}

          $sql= "SELECT coll_ov, wri_rlid, mat_nm, frmt_nm FROM prdsrc_mat
                INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id INNER JOIN prd ON prdid=prd_id
                WHERE prdid='$sg_rep_id' AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prdid, wri_rlid, mat_id ORDER BY src_mat_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring credited source materials for segment productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {$reps[$row['coll_ov']]['sg_prds'][$sg_rep_id]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));}

          $sql= "SELECT coll_ov, wri_rlid, wri_ordr, wri_sb_rl, comp_id, comp_nm
                FROM prdwri
                INNER JOIN comp ON wri_compid=comp_id INNER JOIN prd ON prdid=prd_id
                WHERE prdid='$sg_rep_id' AND wri_prsnid=0 AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prdid, wri_rlid, comp_id
                UNION
                SELECT coll_ov, wri_rlid, wri_ordr, wri_sb_rl, prsn_id, prsn_fll_nm
                FROM prdwri
                INNER JOIN prsn ON wri_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
                WHERE prdid='$sg_rep_id' AND wri_compid=0 AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prdid, wri_rlid, prsn_id
                ORDER BY wri_ordr ASC";
          $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring credited writers for segment productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ';} else {$wri_sb_rl='';}
            $reps[$row['coll_ov']]['sg_prds'][$sg_rep_id]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
          }

          $sql= "SELECT coll_ov, wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm FROM prdwri
                INNER JOIN prsn ON wri_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
                WHERE prdid='$sg_rep_id' AND wri_compid!=0 AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prdid, wri_rlid, wri_compid, prsn_id ORDER BY wri_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring writer (company people) data for segment productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ';} else {$wri_sb_rl='';}
            $reps[$row['coll_ov']]['sg_prds'][$sg_rep_id]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>html($row['prsn_fll_nm']), 'wri_sb_rl'=>$wri_sb_rl);
          }
        }
      }
    }

    $sql="SELECT 1 FROM prdrn WHERE prdrn2='$id' UNION SELECT 1 FROM prdrn WHERE prdrn1='$id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for existence of previous and subsequent runs of this production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt, p1.prd_frst_dt AS coll_sg_frst_dt
            FROM prdrn
            INNER JOIN prd p1 ON prdrn2=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
            WHERE prdrn1='$id'
            GROUP BY prd_id
            UNION
            SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt, p1.prd_frst_dt AS coll_sg_frst_dt
            FROM prdrn
            INNER JOIN prd p1 ON prdrn1=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
            WHERE prdrn2='$id'
            GROUP BY prd_id
            UNION
            SELECT prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt, NULL AS coll_sg_frst_dt
            FROM prdrn
            INNER JOIN prd p1 ON prdrn2=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE prdrn1='$id' AND coll_ov IS NULL
            GROUP BY prd_id
            UNION
            SELECT prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt, NULL AS coll_sg_frst_dt
            FROM prdrn
            INNER JOIN prd p1 ON prdrn1=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE prdrn2='$id' AND coll_ov IS NULL
            GROUP BY prd_id
          ORDER BY prd_frst_dt DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring previous and subsequent runs of this productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          if(!$row['coll_sg_frst_dt']) {$prdrn_frst_dt=$row['prd_frst_dt'];}
          else {$prdrn_frst_dt=$row['coll_sg_frst_dt'];}
          if($prdrn_frst_dt <= $prd_frst_dt_num) {$prdrn_prvs_prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'sg_prds'=>array());}
          else {$prdrn_sbsq_prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'sg_prds'=>array());}
          $awrd_prd_ids[]=$row['prd_id'];
        }

        $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, coll_sbhdrid, coll_ordr
              FROM prdrn
              INNER JOIN prd ON prdrn1=prd_id INNER JOIN thtr ON thtrid=thtr_id
              WHERE prdrn2='$id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id
              UNION
              SELECT coll_ov, prd_id, prd_nm, prd_url, prd_frst_dt, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, coll_sbhdrid, coll_ordr
              FROM prdrn
              INNER JOIN prd ON prdrn2=prd_id INNER JOIN thtr ON thtrid=thtr_id
              WHERE prdrn1='$id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id
              ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring previous and subsequent runs (segments) of this productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          if($row['prd_frst_dt'] <= $prd_frst_dt_num) {$prdrn_prvs_prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('sg_prd_nm'=>$prd_nm, 'sg_prd_nm_pln'=>html($row['prd_nm']), 'sg_prd_dts'=>$prd_dts, 'sg_thtr'=>$thtr, 'sg_wri_rls'=>array());}
          else {$prdrn_sbsq_prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('sg_prd_nm'=>$prd_nm, 'sg_prd_nm_pln'=>html($row['prd_nm']), 'sg_prd_dts'=>$prd_dts, 'sg_thtr'=>$thtr, 'sg_wri_rls'=>array());}
          $awrd_prd_ids[]=$row['prd_id'];
        }
      }
    }

    $sql="SELECT prd_vrsn_nm, prd_vrsn_url FROM prdprd_vrsn INNER JOIN prd_vrsn ON prd_vrsnid=prd_vrsn_id WHERE prdid='$id' ORDER BY prd_vrsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring prod version data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$prd_vrsns[]='<a href="/production/prod-version/'.html($row['prd_vrsn_url']).'">'.html($row['prd_vrsn_nm']).'</a>';}

    $sql="SELECT txt_vrsn_nm, txt_vrsn_url FROM prdtxt_vrsn INNER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id WHERE prdid='$id' ORDER BY txt_vrsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring text version data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$txt_vrsns[]='<a href="/production/text-version/'.html($row['txt_vrsn_url']).'">'.html($row['txt_vrsn_nm']).'</a>';}

    $sql="SELECT ctgry_nm, ctgry_url FROM prdctgry INNER JOIN ctgry ON ctgryid=ctgry_id WHERE prdid='$id' ORDER BY ctgry_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ctgrys[]='<a href="/production/category/'.html($row['ctgry_url']).'">'.html($row['ctgry_nm']).'</a>';}

    $sql="SELECT gnr_id, gnr_nm, gnr_url FROM prdgnr INNER JOIN gnr ON gnrid=gnr_id WHERE prdid='$id' ORDER BY gnr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$gnrs[$row['gnr_id']]=array('gnr_nm'=>'<a href="/production/genre/'.html($row['gnr_url']).'">'.html($row['gnr_nm']).'</a>', 'rel_gnrs'=>array());}

      $sql= "SELECT rel_gnr1, gnr_nm, gnr_url
            FROM prdgnr INNER JOIN rel_gnr ON gnrid=rel_gnr1 INNER JOIN gnr ON rel_gnr2=gnr_id
            WHERE prdid='$id' ORDER BY rel_gnr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$gnrs[$row['rel_gnr1']]['rel_gnrs'][]='<a href="/production/genre/'.html($row['gnr_url']).'">'.html($row['gnr_nm']).'</a>';}
    }

    $sql="SELECT ftr_nm, ftr_url FROM prdftr INNER JOIN ftr ON ftrid=ftr_id WHERE prdid='$id' ORDER BY ftr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring feature data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ftrs[]='<a href="/production/feature/'.html($row['ftr_url']).'">'.html($row['ftr_nm']).'</a>';}

    $sql="SELECT thm_id, thm_nm, thm_url FROM prdthm INNER JOIN thm ON thmid=thm_id WHERE prdid='$id' ORDER BY thm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$thms[$row['thm_id']]=array('thm_nm'=>'<a href="/production/theme/'.html($row['thm_url']).'">'.html($row['thm_nm']).'</a>', 'rel_thms'=>array());}

      $sql= "SELECT rel_thm1, thm_nm, thm_url
            FROM prdthm INNER JOIN rel_thm ON thmid=rel_thm1 INNER JOIN thm ON rel_thm2=thm_id
            WHERE prdid='$id' ORDER BY rel_thm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$thms[$row['rel_thm1']]['rel_thms'][]='<a href="/production/theme/'.html($row['thm_url']).'">'.html($row['thm_nm']).'</a>';}
    }

    $sql= "SELECT sttngid FROM prdsttng_tm WHERE prdid='$id' GROUP BY sttngid
          UNION
          SELECT sttngid FROM prdsttng_lctn WHERE prdid='$id' GROUP BY sttngid
          UNION
          SELECT sttngid FROM prdsttng_plc WHERE prdid='$id' GROUP BY sttngid
          ORDER BY sttngid ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting group data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$sttngs[$row['sttngid']]=array('tms'=>array(), 'tm_spns'=>array(), 'lctns'=>array(), 'plcs'=>array());}

      $sql= "SELECT sttngid, tm_id, tm_nm, tm_url, sttng_tm_nt1, sttng_tm_nt2 FROM prdsttng_tm
            INNER JOIN tm ON sttng_tmid=tm_id
            WHERE prdid='$id'
            ORDER BY sttng_tm_ordr";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring setting time data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_tm_nt1']) {if(!preg_match('/(^c\.| c\.)$/', $row['sttng_tm_nt1'])) {$sttng_tm_nt1=html($row['sttng_tm_nt1']).' ';} else {$sttng_tm_nt1=html($row['sttng_tm_nt1']);}}
          else {$sttng_tm_nt1='';}
          if($row['sttng_tm_nt2']) {if(!preg_match('/^(:|;|,|\.)/', $row['sttng_tm_nt2'])) {$sttng_tm_nt2=' '.html($row['sttng_tm_nt2']);} else {$sttng_tm_nt2=html($row['sttng_tm_nt2']);}}
          else {$sttng_tm_nt2='';}
          $sttngs[$row['sttngid']]['tms'][$row['tm_id']]=array('tm'=>$sttng_tm_nt1.'<a href="/production/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>'.$sttng_tm_nt2, 'rel_tms'=>array());
        }

        $sql= "SELECT sttngid, rel_tm1, tm_nm, tm_url FROM prdsttng_tm
              INNER JOIN rel_tm ON sttng_tmid=rel_tm1 INNER JOIN tm ON rel_tm2=tm_id
              WHERE prdid='$id'
              ORDER BY rel_tm_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring related time data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$sttngs[$row['sttngid']]['tms'][$row['rel_tm1']]['rel_tms'][]='<a href="/production/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}
      }

      $sql="SELECT sttng_id, tm_spn FROM prdsttng WHERE prdid='$id' AND tm_spn=1 ORDER BY sttng_id";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring setting time span data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$sttngs[$row['sttng_id']]['tm_spns'][]=$row['tm_spn'];}

      $sql= "SELECT sttngid, lctn_id, lctn_nm, lctn_url, sttng_lctn_nt1, sttng_lctn_nt2
            FROM prdsttng_lctn INNER JOIN lctn ON sttng_lctnid=lctn_id
            WHERE prdid='$id' ORDER BY sttng_lctn_ordr";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring setting location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_lctn_nt1']) {$sttng_lctn_nt1=html($row['sttng_lctn_nt1']).' ';} else {$sttng_lctn_nt1='';}
          if($row['sttng_lctn_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_lctn_nt2'])) {$sttng_lctn_nt2=html($row['sttng_lctn_nt2']);} else {$sttng_lctn_nt2=' '.html($row['sttng_lctn_nt2']);}}
          else {$sttng_lctn_nt2='';}
          $sttngs[$row['sttngid']]['lctns'][$row['lctn_id']]=array('lctn'=>$sttng_lctn_nt1.'<a href="/production/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$sttng_lctn_nt2, 'rel_lctns'=>array());
        }

        $sql= "SELECT psl.sttngid, rel_lctn1, lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
              FROM prdsttng_lctn psl
              INNER JOIN rel_lctn ON sttng_lctnid=rel_lctn1 INNER JOIN lctn ON rel_lctn2=lctn_id
              LEFT OUTER JOIN prdsttng_lctn_alt psla ON psl.prdid=psla.prdid AND psl.sttngid=psla.sttngid AND psl.sttng_lctnid=psla.sttng_lctnid
              WHERE psl.prdid='$id'
              AND lctn_exp=0 AND lctn_fctn=0 AND psla.prdid IS NULL
              UNION
              SELECT psl.sttngid, rel_lctn1, lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
              FROM prdsttng_lctn psl
              INNER JOIN rel_lctn ON psl.sttng_lctnid=rel_lctn1 INNER JOIN prdsttng_lctn_alt psla ON rel_lctn2=psla.sttng_lctn_altid
              INNER JOIN lctn ON psla.sttng_lctn_altid=lctn_id
              WHERE psl.prdid='$id'
              AND psl.prdid=psla.prdid AND psl.sttngid=psla.sttngid AND psl.sttng_lctnid=psla.sttng_lctnid
              ORDER BY rel_lctn_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring related location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']).' ';} else {$rel_lctn_nt1='';}
          if($row['rel_lctn_nt2']) {$rel_lctn_nt2=' '.html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2='';}
          $sttngs[$row['sttngid']]['lctns'][$row['rel_lctn1']]['rel_lctns'][]=$rel_lctn_nt1.'<a href="/production/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt2;
        }
      }

      $sql= "SELECT sttngid, plc_id, plc_nm, plc_url, sttng_plc_nt1, sttng_plc_nt2
            FROM prdsttng_plc INNER JOIN plc ON sttng_plcid=plc_id
            WHERE prdid='$id' ORDER BY sttng_plc_ordr";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring setting place data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_plc_nt1']) {$sttng_plc_nt1=html($row['sttng_plc_nt1']).' '; $plc_nm=html($row['plc_nm']);} else {$sttng_plc_nt1=''; $plc_nm=html(ucfirst($row['plc_nm']));}
          if($row['sttng_plc_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_plc_nt2'])) {$sttng_plc_nt2=html($row['sttng_plc_nt2']);} else {$sttng_plc_nt2=' '.html($row['sttng_plc_nt2']);}}
          else {$sttng_plc_nt2='';}
          $sttngs[$row['sttngid']]['plcs'][$row['plc_id']]=array('plc'=>$sttng_plc_nt1.'<a href="/production/setting/place/'.html($row['plc_url']).'">'.$plc_nm.'</a>'.$sttng_plc_nt2, 'rel_plcs'=>array());
        }

        $sql= "SELECT sttngid, rel_plc1, plc_nm, plc_url
              FROM prdsttng_plc INNER JOIN rel_plc ON sttng_plcid=rel_plc1 INNER JOIN plc ON rel_plc2=plc_id
              WHERE prdid='$id' ORDER BY rel_plc_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring related place data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$sttngs[$row['sttngid']]['plcs'][$row['rel_plc1']]['rel_plcs'][]='<a href="/production/setting/place/'.html($row['plc_url']).'">'.html(ucfirst($row['plc_nm'])).'</a>';}
      }
    }

    $sql="SELECT wri_rl_id, wri_rl, src_mat_rl FROM prdwrirl WHERE prdid='$id' ORDER BY wri_rl_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring writer (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$wri_rls[$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());}

      $sql= "SELECT wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url
            FROM prdsrc_mat
            INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id
            WHERE prdid='$id'
            ORDER BY src_mat_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring source material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $src_mat_url='<em><a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['mat_nm']).'</a></em>';
        $src_mat_frmt_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['frmt_nm']).'</a>';
        $wri_rls[$row['wri_rlid']]['src_mats'][]=array('src_mat_url'=>$src_mat_url, 'src_mat_frmt_url'=>$src_mat_frmt_url, 'src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));
      }

      $sql= "SELECT wri_rlid, comp_id, comp_nm, comp_url, wri_sb_rl, wri_ordr, comp_bool
            FROM prdwri INNER JOIN comp ON wri_compid=comp_id WHERE prdid='$id' AND wri_prsnid=0
            UNION
            SELECT wri_rlid, prsn_id, prsn_fll_nm, prsn_url, wri_sb_rl, wri_ordr, comp_bool
            FROM prdwri INNER JOIN prsn ON wri_prsnid=prsn_id WHERE prdid='$id' AND wri_compid=0
            ORDER BY wri_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        if($row['wri_sb_rl']) {if(!preg_match('/^(:|;|,|\.)/', $row['wri_sb_rl'])) {$wri_sb_rl=' '.html($row['wri_sb_rl']).' ';} else {$wri_sb_rl=html($row['wri_sb_rl']).' ';}} else {$wri_sb_rl='';}
        $wri_rls[$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
      }

      $sql= "SELECT wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm, prsn_url
            FROM prdwri INNER JOIN prsn ON wri_prsnid=prsn_id
            WHERE prdid='$id' AND wri_compid!=0 ORDER BY wri_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ' ;} else {$wri_sb_rl='';}
        $wri_rls[$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'wri_sb_rl'=>$wri_sb_rl);
      }
    }

    $sql="SELECT prdcr_rl_id, prdcr_rl FROM prdprdcrrl WHERE prdid='$id' ORDER BY prdcr_rl_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring producer (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$prdcr_rls[$row['prdcr_rl_id']]=array('prdcr_rl'=>html($row['prdcr_rl']), 'prdcrs'=>array());}

      $sql= "SELECT prdcr_rlid, comp_id, comp_nm, comp_url, prdcr_sb_rl, prdcr_ordr, comp_bool
            FROM prdprdcr INNER JOIN comp ON prdcr_compid=comp_id WHERE prdid='$id' AND prdcr_prsnid=0
            UNION
            SELECT prdcr_rlid, prsn_id, prsn_fll_nm, prsn_url, prdcr_sb_rl, prdcr_ordr, comp_bool
            FROM prdprdcr INNER JOIN prsn ON prdcr_prsnid=prsn_id WHERE prdid='$id' AND prdcr_compid=0
            ORDER BY prdcr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring producer data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        if($row['prdcr_sb_rl']) {$prdcr_sb_rl=html($row['prdcr_sb_rl']).' ';} else {$prdcr_sb_rl='';}
        $prdcr_rls[$row['prdcr_rlid']]['prdcrs'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'comp_nm_pln'=>html($row['comp_nm']), 'prdcr_sb_rl'=>$prdcr_sb_rl, 'prdcrcomp_ppl_crdt'=>array(), 'comp_rls'=>array());
      }

      $sql= "SELECT prdcr_rlid, prdcr_compid, prsn_fll_nm, prsn_url
            FROM prdprdcr INNER JOIN prsn ON prdcr_prsnid=prsn_id
            WHERE prdid='$id' AND prdcr_compid!=0 AND prdcr_crdt=1 ORDER BY prdcr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring producer (company people - credited) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $prdcr_rls[$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['prdcrcomp_ppl_crdt'][]=array('prsn_nm'=>$prsn_nm);
      }

      $sql= "SELECT prdcr_rlid, prdcr_compid, prdcr_comp_rl_id, prdcr_comprl
            FROM prdprdcr pp INNER JOIN prdprdcr_comprl ppcr ON pp.prdid=ppcr.prdid
            WHERE pp.prdid='$id' AND prdcr_comp_rlid=prdcr_comp_rl_id
            GROUP BY prdcr_rlid, prdcr_compid, prdcr_comp_rl_id ORDER BY prdcr_comp_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring producer (company people roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prdcr_rls[$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['comp_rls'][$row['prdcr_comp_rl_id']]=array('prdcr_comprl'=>html($row['prdcr_comprl']), 'prdcrcomp_ppl'=>array());}

      $sql= "SELECT prdcr_rlid, prdcr_compid, prdcr_comp_rlid, prsn_fll_nm, prsn_url
            FROM prdprdcr INNER JOIN prsn ON prdcr_prsnid=prsn_id
            WHERE prdid='$id' AND prdcr_compid!=0 ORDER BY prdcr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring producer (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $prdcr_rls[$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['comp_rls'][$row['prdcr_comp_rlid']]['prdcrcomp_ppl'][]=$prsn_nm;
      }
    }

    $sql="SELECT prsn_id, prsn_fll_nm, prsn_url FROM prdprf INNER JOIN prsn ON prf_prsnid=prsn_id WHERE prdid='$id' ORDER BY prf_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring performer (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $prfs[$row['prsn_id']]=array('prsn_nm'=>$prsn_nm, 'prf_rls'=>array());
      }

      $sql= "SELECT prf_prsnid, prf_rl, prf_rl_dscr, prf_rl_alt,
            (SELECT char_url FROM prdpt pp INNER JOIN ptchar pc ON pp.ptid=pc.ptid INNER JOIN role ON charid=char_id WHERE prdid='$id' AND (char_nm=prf_rl OR char_lnk=prf_rl_lnk) LIMIT 1) AS char_url
            FROM prdprf WHERE prdid='$id' ORDER BY prf_ordr ASC, prf_rl_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring performer (person) role data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_url']) {$prf_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['prf_rl']).'</a>';} else {$prf_rl=html($row['prf_rl']);}
        if($row['prf_rl_dscr']) {$prf_rl.=' ('.html($row['prf_rl_dscr']).')';}
        if($row['prf_rl_alt']) {$prf_rl.='<span style="font-style:normal"> (alt)</span>';}
        $prfs[$row['prf_prsnid']]['prf_rls'][]=$prf_rl;
      }
    }

    $sql="SELECT prsn_id, prsn_fll_nm, prsn_url FROM prdus INNER JOIN prsn ON us_prsnid=prsn_id WHERE prdid='$id' ORDER BY us_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring understudy (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $uss[$row['prsn_id']]=array('prsn_nm'=>$prsn_nm, 'us_rls'=>array());
      }

      $sql= "SELECT us_prsnid, us_rl, us_rl_dscr, us_rl_alt, (SELECT char_url FROM prdpt pp INNER JOIN ptchar pc ON pp.ptid=pc.ptid INNER JOIN role ON charid=char_id
            WHERE prdid='$id'
            AND (char_nm=us_rl OR char_lnk=us_rl_lnk) LIMIT 1) AS char_url
            FROM prdus
            WHERE prdid='$id'
            ORDER BY us_ordr ASC, us_rl_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring understudy (person) role data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_url']) {$us_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['us_rl']).'</a>';} else {$us_rl=html($row['us_rl']);}
        if($row['us_rl_dscr']) {$us_rl.=' ('.html($row['us_rl_dscr']).')';}
        if($row['us_rl_alt']) {$us_rl.='<span style="font-style:normal"> (alt)</span>';}
        $uss[$row['us_prsnid']]['us_rls'][]=$us_rl;
      }
    }

    $sql= "SELECT mscn_rl_id, mscn_rl
          FROM prdmscnrl
          WHERE prdid='$id'
          ORDER BY mscn_rl_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring musician (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$mscn_rls[$row['mscn_rl_id']]=array('mscn_rl'=>html($row['mscn_rl']), 'mscns'=>array());}

      $sql= "SELECT mscn_rlid, comp_id, comp_nm, comp_url, mscn_ordr, comp_bool
            FROM prdmscn
            INNER JOIN comp ON mscn_compid=comp_id
            WHERE prdid='$id'
            AND mscn_prsnid=0
            UNION
            SELECT mscn_rlid, prsn_id, prsn_fll_nm, prsn_url, mscn_ordr, comp_bool
            FROM prdmscn
            INNER JOIN prsn ON mscn_prsnid=prsn_id
            WHERE prdid='$id'
            AND mscn_compid=0
            ORDER BY mscn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring musician data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        $mscn_rls[$row['mscn_rlid']]['mscns'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'comp_nm_pln'=>html($row['comp_nm']), 'comp_rls'=>array());
      }

      $sql= "SELECT mscn_rlid, mscn_compid, mscn_comp_rl_id, mscn_comprl
            FROM prdmscn pp
            INNER JOIN prdmscn_comprl ppcr ON pp.prdid=ppcr.prdid
            WHERE pp.prdid='$id'
            AND mscn_comp_rlid=mscn_comp_rl_id
            GROUP BY mscn_rlid, mscn_compid, mscn_comp_rl_id
            ORDER BY mscn_comp_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring musician (company people roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$mscn_rls[$row['mscn_rlid']]['mscns'][$row['mscn_compid']]['comp_rls'][$row['mscn_comp_rl_id']]=array('mscn_comprl'=>html($row['mscn_comprl']), 'mscncomp_ppl'=>array());}

      $sql= "SELECT mscn_rlid, mscn_compid, mscn_comp_rlid, prsn_fll_nm, prsn_url
            FROM prdmscn
            INNER JOIN prsn ON mscn_prsnid=prsn_id
            WHERE prdid='$id'
            AND mscn_compid!=0
            ORDER BY mscn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring musician (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $mscn_rls[$row['mscn_rlid']]['mscns'][$row['mscn_compid']]['comp_rls'][$row['mscn_comp_rlid']]['mscncomp_ppl'][]=$prsn_nm;
      }
    }

    $sql= "SELECT crtv_rl_id, crtv_rl
          FROM prdcrtvrl
          WHERE prdid='$id'
          ORDER BY crtv_rl_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring creative (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$crtv_rls[$row['crtv_rl_id']]=array('crtv_rl'=>html($row['crtv_rl']), 'crtvs'=>array());}

      $sql= "SELECT crtv_rlid, comp_id, comp_nm, comp_url, crtv_ordr, comp_bool
            FROM prdcrtv
            INNER JOIN comp ON crtv_compid=comp_id
            WHERE prdid='$id'
            AND crtv_prsnid=0
            UNION
            SELECT crtv_rlid, prsn_id, prsn_fll_nm, prsn_url, crtv_ordr, comp_bool
            FROM prdcrtv
            INNER JOIN prsn ON crtv_prsnid=prsn_id
            WHERE prdid='$id'
            AND crtv_compid=0
            ORDER BY crtv_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring creative data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        $crtv_rls[$row['crtv_rlid']]['crtvs'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'crtvcomp_ppl'=>array());
      }

      $sql= "SELECT crtv_rlid, crtv_compid, prsn_fll_nm, prsn_url
            FROM prdcrtv
            INNER JOIN prsn ON crtv_prsnid=prsn_id
            WHERE prdid='$id'
            AND crtv_compid!=0
            ORDER BY crtv_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring creative (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $crtv_rls[$row['crtv_rlid']]['crtvs'][$row['crtv_compid']]['crtvcomp_ppl'][]=array('prsn_nm'=>$prsn_nm);
      }
    }

    $sql= "SELECT prdtm_rl_id, prdtm_rl
          FROM prdprdtmrl
          WHERE prdid='$id'
          ORDER BY prdtm_rl_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production team (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$prdtm_rls[$row['prdtm_rl_id']]=array('prdtm_rl'=>html($row['prdtm_rl']), 'prdtms'=>array());}

      $sql= "SELECT prdtm_rlid, comp_id, comp_nm, comp_url, prdtm_ordr, comp_bool
            FROM prdprdtm
            INNER JOIN comp ON prdtm_compid=comp_id
            WHERE prdid='$id'
            AND prdtm_prsnid=0
            UNION
            SELECT prdtm_rlid, prsn_id, prsn_fll_nm, prsn_url, prdtm_ordr, comp_bool
            FROM prdprdtm
            INNER JOIN prsn ON prdtm_prsnid=prsn_id
            WHERE prdid='$id'
            AND prdtm_compid=0
            ORDER BY prdtm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring production team data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        $prdtm_rls[$row['prdtm_rlid']]['prdtms'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'prdtmcomp_ppl'=>array());
      }

      $sql= "SELECT prdtm_rlid, prdtm_compid, prsn_fll_nm, prsn_url
            FROM prdprdtm
            INNER JOIN prsn ON prdtm_prsnid=prsn_id
            WHERE prdid='$id'
            AND prdtm_compid!=0
            ORDER BY prdtm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring production team (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $prdtm_rls[$row['prdtm_rlid']]['prdtms'][$row['prdtm_compid']]['prdtmcomp_ppl'][]=array('prsn_nm'=>$prsn_nm);
      }
    }

    $sql= "SELECT ssn_nm, ssn_url
          FROM prdssn
          INNER JOIN ssn ON ssnid=ssn_id
          WHERE prdid='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring season data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0)
    {$ssn_nm='<a href="/season/'.html($row['ssn_url']).'">'.html($row['ssn_nm']).'</a>';} else {$ssn_nm=NULL;}

    $sql= "SELECT fstvl_nm, fstvl_url
          FROM prdfstvl
          INNER JOIN fstvl ON fstvlid=fstvl_id
          WHERE prdid='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring festival data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0)
    {$fstvl_nm='<a href="/festival/'.html($row['fstvl_url']).'">'.html($row['fstvl_nm']).'</a>';} else {$fstvl_nm=NULL;}

    $sql= "SELECT comp_nm, comp_url, crs_typ_nm, crs_typ_url, crs_yr_strt, crs_yr_end, crs_yr_url
          FROM prdcrs
          INNER JOIN crs ON crsid=crs_id INNER JOIN comp ON crs_schlid=comp_id INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE prdid='$id'
          ORDER BY crs_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring course data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['crs_yr_strt']!==$row['crs_yr_end'])
      {$crs_yr='('.html($row['crs_yr_strt']).html(preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end'])).')';}
      else {$crs_yr='('.html($row['crs_yr_strt']).')';}
      $crs='<a href="/course/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'/'.html($row['crs_yr_url']).'">'.html($row['comp_nm']).': '.html($row['crs_typ_nm']).' '.$crs_yr.'</a>';
      $crss[]=$crs;
    }

    $rvw_ids[]=$prd_id;
    $rvw_id=implode($rvw_ids, ' OR prdid=');

    $sql= "SELECT DISTINCT comp_nm, comp_url, prsn_fll_nm, prsn_url, DATE_FORMAT(rvw_dt, '%d %b %Y') AS rvw_dt_dsply, rvw_url
          FROM prdrvw
          INNER JOIN comp ON rvw_pub_compid=comp_id INNER JOIN prsn ON rvw_crtc_prsnid=prsn_id
          WHERE (prdid=$rvw_id)
          ORDER BY rvw_dt ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring review data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $rvw_lnk='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>, <a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a> ('.html($row['rvw_dt_dsply']).') [<a href="'.html($row['rvw_url']).'" target="'.html($row['rvw_url']).'">review link</a>]';
      $rvws[]=array('rvw_lnk'=>$rvw_lnk);
    }

    $sql= "SELECT prd_alt_nm, prd_alt_nm_dscr
          FROM prd_alt_nm
          WHERE prdid='$id'
          ORDER BY prd_alt_nm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production alternate name data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prd_alt_nm_dscr']) {$alt_nm_dscr=' ('.html($row['prd_alt_nm_dscr']).')';} else {$alt_nm_dscr='';}
      $alt_nm='<em>'.html($row['prd_alt_nm']).'</em>'.$alt_nm_dscr;
      $alt_nms[]=$alt_nm;
    }

    $sql= "SELECT p2.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.coll_ov=p2.tr_ov WHERE p1.prd_id='$prd_id'
          UNION
          SELECT p2.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.tr_ov=p2.coll_ov WHERE p1.prd_id='$prd_id'
          UNION
          SELECT p2.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.tr_ov=p2.tr_ov WHERE p1.prd_id='$prd_id' AND p1.prd_id!=p2.prd_id
          UNION
          SELECT p3.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.tr_ov=p2.coll_ov INNER JOIN prd p3 ON p2.prd_id=p3.tr_ov WHERE p1.prd_id='$prd_id'
          UNION
          SELECT p3.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.tr_ov=p2.prd_id INNER JOIN prd p3 ON p2.coll_ov=p3.tr_ov WHERE p1.prd_id='$prd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring associated productions data (tour legs from associated collections / collection segments from associated tours / other tour legs of same collection segment / tour legs of collection segments of tour overview): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$awrd_prd_ids[]=$row['prd_id'];}

    $sql= "SELECT prd_id FROM prdrn INNER JOIN prd ON prdrn2=coll_ov WHERE prdrn1='$id'
          UNION
          SELECT prd_id FROM prdrn INNER JOIN prd ON prdrn1=coll_ov WHERE prdrn2='$id'
          UNION
          SELECT prd_id FROM prdrn INNER JOIN prd ON prdrn2=tr_ov WHERE prdrn1='$id'
          UNION
          SELECT prd_id FROM prdrn INNER JOIN prd ON prdrn1=tr_ov WHERE prdrn2='$id'
          UNION
          SELECT p2.prd_id FROM prdrn INNER JOIN prd p1 ON prdrn2=p1.coll_ov INNER JOIN prd p2 ON p1.prd_id=p2.tr_ov WHERE prdrn1='$id'
          UNION
          SELECT p2.prd_id FROM prdrn INNER JOIN prd p1 ON prdrn1=p1.coll_ov INNER JOIN prd p2 ON p1.prd_id=p2.tr_ov WHERE prdrn2='$id'
          UNION
          SELECT p2.prd_id FROM prdrn INNER JOIN prd p1 ON prdrn2=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.tr_ov WHERE prdrn1='$id'
          UNION
          SELECT p2.prd_id FROM prdrn INNER JOIN prd p1 ON prdrn1=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.tr_ov WHERE prdrn2='$id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring associated productions data (tour legs / collection segments from associated previous/subsequent production run productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$awrd_prd_ids[]=$row['prd_id'];}

    $awrd_prd_ids[]=$prd_id;
    $awrd_prd_ids=array_unique($awrd_prd_ids);
    $awrd_prd_id=implode($awrd_prd_ids, ' OR nom_prdid=');
    $anp_awrd_prd_id=implode($awrd_prd_ids, ' OR anp1.nom_prdid=');

    $awrds_ttl_wins=array(); $awrds_ttl_noms=array();
    $sql= "SELECT awrds_id, awrds_nm, awrds_url, COALESCE(awrds_alph, awrds_nm)awrds_alph FROM awrdnomprds
          INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id WHERE (nom_prdid=$awrd_prd_id)
          GROUP BY awrds_id ORDER BY awrds_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards name data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$awrds[$row['awrds_id']]=array('awrds_nm'=>html($row['awrds_nm']), 'awrd_yrs'=>array(), 'awrd_wins'=>array(), 'awrd_noms'=>array());}

      $sql= "SELECT awrdsid, awrd_id, awrd_yr, awrd_yr_end, awrd_yr_url, awrds_url FROM awrdnomprds
            INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id WHERE (nom_prdid=$awrd_prd_id)
            GROUP BY awrdsid, awrd_id ORDER BY awrd_yr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award year data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['awrd_yr_end']) {$awrd_yr_end=html(preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']));} else {$awrd_yr_end='';}
        $awrd_lnk='<b><a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrd_yr']).$awrd_yr_end.'</a></b>';
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]=array('awrd_lnk'=>$awrd_lnk, 'awrd_yr_wins'=>array(), 'awrd_yr_noms'=>array(), 'ctgrys'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, awrd_ctgry_id, COALESCE(awrd_ctgry_alt_nm, awrd_ctgry_nm)awrd_ctgry_nm
            FROM awrdnomprds anp
            INNER JOIN awrd ON anp.awrdid=awrd_id
            INNER JOIN awrdctgrys ac ON anp.awrdid=ac.awrdid AND anp.awrd_ctgryid=ac.awrd_ctgryid INNER JOIN awrd_ctgry ON ac.awrd_ctgryid=awrd_ctgry_id
            WHERE (nom_prdid=$awrd_prd_id) GROUP BY awrdsid, awrd_id, awrd_ctgry_id ORDER BY awrd_ctgry_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgry_id']]=array('awrd_ctgry_nm'=>html($row['awrd_ctgry_nm']), 'noms'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, nom_id, nom_win_dscr, win_bool FROM awrdnomprds anp
            INNER JOIN awrd ON anp.awrdid=awrd_id INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND anp.nomid=nom_id
            WHERE (nom_prdid=$awrd_prd_id) GROUP BY awrdsid, awrd_id, awrd_ctgryid, nom_id ORDER BY nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award nominations data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['win_bool']) {$awrds_ttl_wins[]=1; $awrds[$row['awrdsid']]['awrd_wins'][]=1; $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_wins'][]=1;}
        else {$awrds_ttl_noms[]=1; $awrds[$row['awrdsid']]['awrd_noms'][]=1; $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_noms'][]=1;}
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nom_id']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'win'=>$row['win_bool'], 'nomppl'=>array(), 'co_nomprds'=>array(), 'cowins'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anprd.awrd_ctgryid, anprd.nomid, nom_ordr, nom_rl, comp_id, comp_nm, comp_url, comp_bool FROM awrdnomprds anprd
            INNER JOIN awrd ON anprd.awrdid=awrd_id
            INNER JOIN awrdnomppl anp ON anprd.awrdid=anp.awrdid AND anprd.awrd_ctgryid=anp.awrd_ctgryid AND anprd.nomid=anp.nomid
            INNER JOIN comp ON nom_compid=comp_id
            WHERE (nom_prdid=$awrd_prd_id) AND nom_prsnid=0 GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, comp_id
            UNION
            SELECT awrdsid, awrd_id, anprd.awrd_ctgryid, anprd.nomid, nom_ordr, nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool FROM awrdnomprds anprd
            INNER JOIN awrd ON anprd.awrdid=awrd_id
            INNER JOIN awrdnomppl anp ON anprd.awrdid=anp.awrdid AND anprd.awrd_ctgryid=anp.awrd_ctgryid AND anprd.nomid=anp.nomid
            INNER JOIN prsn ON nom_prsnid=prsn_id
            WHERE (nom_prdid=$awrd_prd_id) AND nom_compid=0 GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, prsn_id
            ORDER BY nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$nom_prsn=' <a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
        else {$nom_prsn=html($row['comp_nm']).$nom_rl;}
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nomppl'][$row['comp_id']]=array('nom_prsn'=>$nom_prsn, 'nomcomp_ppl'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anprd.awrd_ctgryid, anprd.nomid, nom_compid, nom_rl, prsn_fll_nm, prsn_url FROM awrdnomprds anprd
            INNER JOIN awrd ON anprd.awrdid=awrd_id
            INNER JOIN awrdnomppl anp ON anprd.awrdid=anp.awrdid AND anprd.awrd_ctgryid=anp.awrd_ctgryid AND anprd.nomid=anp.nomid
            INNER JOIN prsn ON nom_prsnid=prsn_id
            WHERE (nom_prdid=$awrd_prd_id) AND nom_compid!='0' GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, nom_compid, prsn_id ORDER BY nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nomination/win (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nomppl'][$row['nom_compid']]['nomcomp_ppl'][]=$prsn_nm;
      }

      $sql =  "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph FROM awrdnomprds anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnomprds anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid
            INNER JOIN prd p ON anp2.nom_prdid=prd_id
            INNER JOIN thtr ON p.thtrid=thtr_id
            WHERE (anp1.nom_prdid=$anp_awrd_prd_id) GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award co-nominated/winning productions data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['co_nomprds'][]=array('prd_id'=>html($row['prd_id']), 'prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, an2.nom_id AS n2, an2.nom_win_dscr FROM awrdnomprds anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
            INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
            WHERE (anp1.nom_prdid=$anp_awrd_prd_id) AND an1.win_bool=1 AND an2.win_bool=1
            AND an2.nom_id NOT IN(SELECT nomid FROM awrdnomprds WHERE (nom_prdid=$awrd_prd_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2 ORDER BY an2.nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (co-winner) award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {$awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'cowin_ppl'=>array(), 'cowin_nomprds'=>array());}

        $sql= "SELECT awrdsid, awrd_id, anprd.awrd_ctgryid, anprd.nomid AS n1, anp.nomid AS n2, anp.nom_ordr, anp.nom_rl, comp_id, comp_nm, comp_url, comp_bool
              FROM awrdnomprds anprd
              INNER JOIN awrd ON anprd.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anprd.awrdid=an1.awrdid AND anprd.awrd_ctgryid=an1.awrd_ctgryid AND anprd.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp ON an2.awrdid=anp.awrdid AND an2.awrd_ctgryid=anp.awrd_ctgryid AND an2.nom_id=anp.nomid
              INNER JOIN comp ON anp.nom_compid=comp_id
              WHERE (nom_prdid=$awrd_prd_id) AND an1.win_bool=1 AND an2.win_bool=1 AND anp.nom_prsnid=0
              AND anp.nomid NOT IN(SELECT nomid FROM awrdnomprds WHERE (nom_prdid=$awrd_prd_id) AND awrdid=anprd.awrdid AND awrd_ctgryid=anprd.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, comp_id
              UNION
              SELECT awrdsid, awrd_id, anprd.awrd_ctgryid, anprd.nomid AS n1, anp.nomid AS n2, anp.nom_ordr, anp.nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool
              FROM awrdnomprds anprd
              INNER JOIN awrd ON anprd.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anprd.awrdid=an1.awrdid AND anprd.awrd_ctgryid=an1.awrd_ctgryid AND anprd.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp ON an2.awrdid=anp.awrdid AND an2.awrd_ctgryid=anp.awrd_ctgryid AND an2.nom_id=anp.nomid
              INNER JOIN prsn ON anp.nom_prsnid=prsn_id
              WHERE (nom_prdid=$awrd_prd_id) AND an1.win_bool=1 AND an2.win_bool=1 AND anp.nom_compid=0
              AND anp.nomid NOT IN(SELECT nomid FROM awrdnomprds WHERE (nom_prdid=$awrd_prd_id) AND awrdid=anprd.awrdid AND awrd_ctgryid=anprd.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prsn_id
              ORDER BY nom_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards company/people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          if(!preg_match('/^the-company$/', $row['comp_url'])) {$cowin_prsn=' <a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
          else {$cowin_prsn=html($row['comp_nm']).$nom_rl;}
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['comp_id']]=array('cowin_prsn'=>$cowin_prsn, 'cowincomp_ppl'=>array());
        }

        $sql= "SELECT awrdsid, awrd_id, anprd.awrd_ctgryid, anprd.nomid AS n1, anp.nomid AS n2, anp.nom_compid, anp.nom_ordr, anp.nom_rl, prsn_id, prsn_fll_nm, prsn_url
              FROM awrdnomprds anprd
              INNER JOIN awrd ON anprd.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anprd.awrdid=an1.awrdid AND anprd.awrd_ctgryid=an1.awrd_ctgryid AND anprd.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp ON an2.awrdid=anp.awrdid AND an2.awrd_ctgryid=anp.awrd_ctgryid AND an2.nom_id=anp.nomid
              INNER JOIN prsn ON anp.nom_prsnid=prsn_id
              WHERE (nom_prdid=$awrd_prd_id) AND an1.win_bool=1 AND an2.win_bool=1 AND anp.nom_compid!='0'
              AND anp.nomid NOT IN(SELECT nomid FROM awrdnomprds WHERE (nom_prdid=$awrd_prd_id) AND awrdid=anprd.awrdid AND awrd_ctgryid=anprd.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prsn_id ORDER BY nom_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards company people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          $cowincomp_prsn='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['nom_compid']]['cowincomp_ppl'][]=$cowincomp_prsn;
        }

        $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, anp2.nomid AS n2, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
              DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm
              FROM awrdnomprds anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomprds anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid AND an2.nom_id=anp2.nomid INNER JOIN prd p ON anp2.nom_prdid=prd_id
              INNER JOIN thtr ON p.thtrid=thtr_id
              WHERE (anp1.nom_prdid=$anp_awrd_prd_id) AND an1.win_bool=1 AND an2.win_bool=1
              AND anp2.nomid NOT IN(SELECT nomid FROM awrdnomprds WHERE (nom_prdid=$awrd_prd_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards production data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_prds'][]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
        }
      }
    }

    $prd_id=html($prd_id);
    include 'production.html.php';
  }
?>
