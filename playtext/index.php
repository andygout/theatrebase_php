<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $pt_id=cln($_POST['pt_id']);

    $sql= "SELECT pt_nm, pt_sbnm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_sffx_num, pt_nm_yr, pt_pub_dt, pt_pub_dt_frmt, pt_coll, cst_m, cst_f, cst_non_spc, cst_addt, cst_nt
          FROM pt
          WHERE pt_id='$pt_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['pt_sffx_num']) {$pt_sffx_num=html($row['pt_sffx_num']); $pt_sffx_rmn=' ('.romannumeral($row['pt_sffx_num']).')';}
    else {$pt_sffx_num=''; $pt_sffx_rmn='';}
    $pagetab='Edit: '.html($row['pt_nm_yr'].$pt_sffx_rmn);
    $pagetitle=html($row['pt_nm_yr'].$pt_sffx_rmn);
    $pt_nm=html($row['pt_nm']);
    $pt_sbnm=html($row['pt_sbnm']);
    $pt_yr_strtd_c=$row['pt_yr_strtd_c'];
    if(!$row['pt_yr_strtd']) {$pt_yr_strtd=''; $pt_yr_strtd_bce='0';}
    elseif(preg_match('/^-/', $row['pt_yr_strtd'])) {$pt_yr_strtd=html(preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $row['pt_yr_strtd'])); $pt_yr_strtd_bce='1';}
    else {$pt_yr_strtd=html($row['pt_yr_strtd']); $pt_yr_strtd_bce='0';}
    $pt_yr_wrttn_c=$row['pt_yr_wrttn_c'];
    if(preg_match('/^-/', $row['pt_yr_wrttn'])) {$pt_yr_wrttn=html(preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $row['pt_yr_wrttn'])); $pt_yr_wrttn_bce='1';}
    else {$pt_yr_wrttn=html($row['pt_yr_wrttn']); $pt_yr_wrttn_bce='0';}
    $pt_pub_dt=html($row['pt_pub_dt']);
    $pt_pub_dt_frmt=html($row['pt_pub_dt_frmt']);
    $pt_coll=$row['pt_coll'];
    if($row['pt_coll']=='2') {$coll_dsply=' [COLLECTED WORKS]';} elseif($row['pt_coll']=='3') {$coll_dsply=' [COLLECTION]';}
    elseif($row['pt_coll']=='4') {$coll_dsply=' [PART OF COLLECTION]';} else {$coll_dsply='';}
    $cst_m=html($row['cst_m']);
    $cst_f=html($row['cst_f']);
    $cst_non_spc=html($row['cst_non_spc']);
    $cst_addt=html($row['cst_addt']);
    $cst_nt=html($row['cst_nt']);

    $sql="SELECT txt_vrsn_nm FROM pttxt_vrsn INNER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id WHERE ptid='$pt_id' ORDER BY txt_vrsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring text version data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$txt_vrsns[]=$row['txt_vrsn_nm'];}
    if(!empty($txt_vrsns)) {$txt_vrsn_list=html(implode(',,', $txt_vrsns));} else {$txt_vrsn_list='';}

    $sql="SELECT wrks_sbhdr_id, wrks_sbhdr FROM ptwrks_sbhdr WHERE wrks_ov='$pt_id' ORDER BY wrks_sbhdr_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring collected works subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$wrks_sg_sbhdrs[$row['wrks_sbhdr_id']]=array('wrks_sbhdr'=>$row['wrks_sbhdr'], 'wrks_sgs'=>array());}

    $sql= "SELECT wrks_sbhdrid, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_sffx_num, wrks_sg_rl
          FROM ptwrks INNER JOIN pt ON wrks_sg=pt_id WHERE wrks_ov='$pt_id' ORDER BY wrks_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext collected works segment data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      if(empty($wrks_sg_sbhdrs)) {$wrks_sg_sbhdrs['1']=array('wrks_sbhdr'=>NULL, 'wrks_sgs'=>array());}
      while($row=mysqli_fetch_array($result))
      {
        if($row['pt_yr_strtd']) {$wrks_sg_yr_strtd=$row['pt_yr_strtd'].';;';} else {$wrks_sg_yr_strtd='';}
        if($row['pt_yr_strtd_c']) {$wrks_sg_yr_strtd='c'.$wrks_sg_yr_strtd;}
        if($row['pt_yr_wrttn_c']) {$wrks_sg_yr_wrttn='c'.$row['pt_yr_wrttn'];} else {$wrks_sg_yr_wrttn=$row['pt_yr_wrttn'];}
        if($row['pt_sffx_num']) {$wrks_sg_sffx_num='--'.$row['pt_sffx_num'];} else {$wrks_sg_sffx_num='';}
        if($row['wrks_sg_rl']) {$wrks_sg_rl='::'.$row['wrks_sg_rl'];} else {$wrks_sg_rl='';}
        if($row['wrks_sbhdrid']) {$wrks_sbhdrid=$row['wrks_sbhdrid'];} else {$wrks_sbhdrid='1';}
        $wrks_sg_sbhdrs[$wrks_sbhdrid]['wrks_sgs'][]=$row['pt_nm'].'##'.$wrks_sg_yr_strtd.$wrks_sg_yr_wrttn.$wrks_sg_sffx_num.$wrks_sg_rl;
      }
    }

    if(!empty($wrks_sg_sbhdrs))
    {
      $wrks_sg_array=array();
      foreach($wrks_sg_sbhdrs as $wrks_sg_sbhdr)
      {
        if($wrks_sg_sbhdr['wrks_sbhdr']) {$wrks_sbhdr=$wrks_sg_sbhdr['wrks_sbhdr'].'==';} else {$wrks_sbhdr='';}
        $wrks_sg_array[]=$wrks_sbhdr.implode(',,', $wrks_sg_sbhdr['wrks_sgs']);
      }
      $pt_wrks_sg_list=html(implode('@@', $wrks_sg_array));
    }
    else {$pt_wrks_sg_list='';}

    $sql="SELECT coll_sbhdr_id, coll_sbhdr FROM ptcoll_sbhdr WHERE coll_ov='$pt_id' ORDER BY coll_sbhdr_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring collection subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$coll_sg_sbhdrs[$row['coll_sbhdr_id']]=array('coll_sbhdr'=>$row['coll_sbhdr'], 'coll_sgs'=>array());}

    $sql= "SELECT coll_sbhdrid, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_sffx_num
          FROM pt WHERE coll_ov='$pt_id' ORDER BY coll_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring collection segment data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      if(empty($coll_sg_sbhdrs)) {$coll_sg_sbhdrs['1']=array('coll_sbhdr'=>NULL, 'coll_sgs'=>array());}
      while($row=mysqli_fetch_array($result))
      {
        if($row['pt_yr_strtd']) {$coll_sg_yr_strtd=$row['pt_yr_strtd'].';;';} else {$coll_sg_yr_strtd='';}
        if($row['pt_yr_strtd_c']) {$coll_sg_yr_strtd='c'.$coll_sg_yr_strtd;}
        if($row['pt_yr_wrttn_c']) {$coll_sg_yr_wrttn='c'.$row['pt_yr_wrttn'];} else {$coll_sg_yr_wrttn=$row['pt_yr_wrttn'];}
        if($row['pt_sffx_num']) {$coll_sg_sffx_num='--'.$row['pt_sffx_num'];} else {$coll_sg_sffx_num='';}
        if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
        $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sgs'][]=$row['pt_nm'].'##'.$coll_sg_yr_strtd.$coll_sg_yr_wrttn.$coll_sg_sffx_num;
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
      $pt_coll_sg_list=html(implode('@@', $coll_sg_array));
    }
    else {$pt_coll_sg_list='';}

    $sql= "SELECT pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_sffx_num, COALESCE(pt_alph, pt_nm)pt_alph
          FROM ptlnk
          INNER JOIN pt ON lnk1=pt_id
          WHERE lnk2='$pt_id'
          UNION
          SELECT pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_sffx_num, COALESCE(pt_alph, pt_nm)pt_alph
          FROM ptlnk
          INNER JOIN pt ON lnk2=pt_id
          WHERE lnk1='$pt_id'
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext link data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['pt_yr_strtd']) {$lnk_yr_strtd=$row['pt_yr_strtd'].';;';} else {$lnk_yr_strtd='';}
      if($row['pt_yr_strtd_c']) {$lnk_yr_strtd='c'.$lnk_yr_strtd;}
      if($row['pt_yr_wrttn_c']) {$lnk_yr_wrttn='c'.$row['pt_yr_wrttn'];} else {$lnk_yr_wrttn=$row['pt_yr_wrttn'];}
      if($row['pt_sffx_num']) {$lnk_sffx_num='--'.$row['pt_sffx_num'];} else {$lnk_sffx_num='';}
      $pt_lnks[]=$row['pt_nm'].'##'.$lnk_yr_strtd.$lnk_yr_wrttn.$lnk_sffx_num;
    }
    if(!empty($pt_lnks)) {$pt_lnk_list=html(implode(',,', $pt_lnks));} else {$pt_lnk_list='';}

    $sql= "SELECT wri_rl_id, wri_rl, src_mat_rl
          FROM ptwrirl
          WHERE ptid='$pt_id'
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
          FROM ptsrc_mat
          INNER JOIN mat ON src_matid=mat_id
          INNER JOIN frmt ON frmtid=frmt_id
          WHERE ptid='$pt_id'
          ORDER BY src_mat_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring source material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['mat_sffx_num']) {$mat_sffx_num='--'.$row['mat_sffx_num'];} else {$mat_sffx_num='';}
      $wri_rls[$row['wri_rlid']]['src_mats'][]=$row['mat_nm'].';;'.$row['frmt_nm'].$mat_sffx_num;
    }

    $sql =  "SELECT wri_rlid, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, comp_bool
          FROM ptwri
          INNER JOIN comp ON wri_compid=comp_id
          WHERE ptid='$pt_id' AND wri_prsnid=0
          UNION
          SELECT wri_rlid, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, comp_bool
          FROM ptwri
          INNER JOIN prsn ON wri_prsnid=prsn_id
          WHERE ptid='$pt_id' AND wri_compid=0
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
          FROM ptwri
          INNER JOIN prsn ON wri_prsnid=prsn_id
          WHERE ptid='$pt_id' AND wri_compid!=0
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

    $sql= "SELECT cntr_rl_id, cntr_rl
          FROM ptcntrrl
          WHERE ptid='$pt_id'
          ORDER BY cntr_rl_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring contributor (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['cntr_rl']) {$cntr_rl=$row['cntr_rl'].'::';} else {$cntr_rl='';}
      $cntr_rls[$row['cntr_rl_id']]=array('cntr_rl'=>$cntr_rl, 'cntrs'=>array());
    }

    $sql= "SELECT cntr_rlid, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, cntr_sb_rl, cntr_ordr, comp_bool
          FROM ptcntr
          INNER JOIN comp ON cntr_compid=comp_id
          WHERE ptid='$pt_id' AND cntr_prsnid=0
          UNION
          SELECT cntr_rlid, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, cntr_sb_rl, cntr_ordr, comp_bool
          FROM ptcntr
          INNER JOIN prsn ON cntr_prsnid=prsn_id
          WHERE ptid='$pt_id' AND cntr_compid=0
          ORDER BY cntr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring contributor data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['cntr_sb_rl']) {$cntr_sb_rl=$row['cntr_sb_rl'].'~~';} else {$cntr_sb_rl='';}
          if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_cntr_nm=$cntr_sb_rl.$row['comp_nm1'].$comp_sffx_num.'||';
        }
        else
        {$comp_cntr_nm='';}
        $prsn_cntr_nm='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['cntr_sb_rl']) {$cntr_sb_rl=$row['cntr_sb_rl'].'~~';} else {$cntr_sb_rl='';}
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_cntr_nm=$cntr_sb_rl.$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num;
        }
        else
        {$prsn_cntr_nm='';}
        $comp_cntr_nm='';
      }
      $cntr_rls[$row['cntr_rlid']]['cntrs'][$row['comp_id']]=array('comp_cntr_nm'=>$comp_cntr_nm, 'prsn_cntr_nm'=>$prsn_cntr_nm, 'cntrcomp_ppl'=>array());
    }

    $sql= "SELECT cntr_rlid, cntr_compid, cntr_sb_rl, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM ptcntr
          INNER JOIN prsn ON cntr_prsnid=prsn_id
          WHERE ptid='$pt_id' AND cntr_compid!=0
          ORDER BY cntr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring contributor (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['cntr_sb_rl']) {$cntr_sb_rl=$row['cntr_sb_rl'].'~~';} else {$cntr_sb_rl='';}
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $cntr_rls[$row['cntr_rlid']]['cntrs'][$row['cntr_compid']]['cntrcomp_ppl'][]=$cntr_sb_rl.$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num;
    }

    if(!empty($cntr_rls))
    {
      $cntr_array=array();
      foreach($cntr_rls as $cntr_rl)
      {
        $cntr_comp_ppl_array=array();
        foreach($cntr_rl['cntrs'] as $cntr)
        {
          $cntrcomp_ppl_list=implode('//', $cntr['cntrcomp_ppl']);
          $cntr_comp_ppl_array[]=$cntr['comp_cntr_nm'].$cntr['prsn_cntr_nm'].$cntrcomp_ppl_list;
        }
        if(!empty($cntr_comp_ppl_array)) {$cntr_comp_ppl_list=implode('>>', $cntr_comp_ppl_array);} else {$cntr_comp_ppl_list='';}
        $cntr_array[]=$cntr_rl['cntr_rl'].$cntr_comp_ppl_list;
      }
      $cntr_list=html(implode(',,', $cntr_array));
    }
    else
    {$cntr_list='';}

    $sql= "SELECT mat_nm, frmt_nm, mat_sffx_num
          FROM ptmat
          INNER JOIN mat ON matid=mat_id
          INNER JOIN frmt ON frmtid=frmt_id
          WHERE ptid='$pt_id'
          ORDER BY mat_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['mat_sffx_num']) {$mat_sffx_num='--'.$row['mat_sffx_num'];} else {$mat_sffx_num='';}
      $mats[]=$row['mat_nm'].';;'.$row['frmt_nm'].$mat_sffx_num;
    }
    if(!empty($mats)) {$mat_list=html(implode(',,', $mats));} else {$mat_list='';}

    $sql= "SELECT ctgry_nm
          FROM ptctgry
          INNER JOIN ctgry ON ctgryid=ctgry_id
          WHERE ptid='$pt_id'
          ORDER BY ctgry_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ctgrys[]=$row['ctgry_nm'];}
    if(!empty($ctgrys)) {$ctgry_list=html(implode(',,', $ctgrys));} else {$ctgry_list='';}

    $sql="SELECT gnr_nm FROM ptgnr INNER JOIN gnr ON gnrid=gnr_id WHERE ptid='$pt_id' ORDER BY gnr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$gnrs[]=$row['gnr_nm'];}
    if(!empty($gnrs)) {$gnr_list=html(implode(',,', $gnrs));} else {$gnr_list='';}

    $sql="SELECT ftr_nm FROM ptftr INNER JOIN ftr ON ftrid=ftr_id WHERE ptid='$pt_id' ORDER BY ftr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring feature data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ftrs[]=$row['ftr_nm'];}
    if(!empty($ftrs)) {$ftr_list=html(implode(',,', $ftrs));} else {$ftr_list='';}

    $sql="SELECT thm_nm FROM ptthm INNER JOIN thm ON thmid=thm_id WHERE ptid='$pt_id' ORDER BY thm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$thms[]=$row['thm_nm'];}
    if(!empty($thms)) {$thm_list=html(implode(',,', $thms));} else {$thm_list='';}

    $sql= "SELECT sttngid FROM ptsttng_tm WHERE ptid='$pt_id' GROUP BY sttngid
          UNION
          SELECT sttngid FROM ptsttng_lctn WHERE ptid='$pt_id' GROUP BY sttngid
          UNION
          SELECT sttngid FROM ptsttng_plc WHERE ptid='$pt_id' GROUP BY sttngid
          ORDER BY sttngid ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting group data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$sttngs[$row['sttngid']]=array('tms'=>array(), 'tm_spns'=>array(), 'lctns'=>array(), 'plcs'=>array());}

    $sql= "SELECT sttngid, tm_nm, sttng_tm_nt1, sttng_tm_nt2 FROM ptsttng_tm
          INNER JOIN tm ON sttng_tmid=tm_id
          WHERE ptid='$pt_id'
          ORDER BY sttng_tm_ordr";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting time data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['sttng_tm_nt1']) {$sttng_tm_nt1=$row['sttng_tm_nt1'].'::';} else {$sttng_tm_nt1='';}
      if($row['sttng_tm_nt2']) {$sttng_tm_nt2=';;'.$row['sttng_tm_nt2'];} else {$sttng_tm_nt2='';}
      $sttngs[$row['sttngid']]['tms'][]=$sttng_tm_nt1.$row['tm_nm'].$sttng_tm_nt2;
    }

    $sql="SELECT sttng_id, tm_spn FROM ptsttng WHERE ptid='$pt_id' AND tm_spn=1 ORDER BY sttng_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting time span data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$sttngs[$row['sttng_id']]['tm_spns'][]=$row['tm_spn'];}

    $sql= "SELECT sttngid, lctn_id, lctn_nm, lctn_sffx_num, sttng_lctn_nt1, sttng_lctn_nt2 FROM ptsttng_lctn
          INNER JOIN lctn ON sttng_lctnid=lctn_id
          WHERE ptid='$pt_id'
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
          FROM ptsttng_lctn psl
          INNER JOIN rel_lctn ON psl.sttng_lctnid=rel_lctn1 INNER JOIN ptsttng_lctn_alt psla ON rel_lctn2=psla.sttng_lctn_altid
          INNER JOIN lctn ON psla.sttng_lctn_altid=lctn_id
          WHERE psl.ptid='$pt_id' AND psl.ptid=psla.ptid AND psl.sttngid=psla.sttngid AND psl.sttng_lctnid=psla.sttng_lctnid
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting alternate location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$lctn_sffx_num='';}
      $sttngs[$row['sttngid']]['lctns'][$row['sttng_lctnid']]['lctn_alts'][]=$row['lctn_nm'].$lctn_sffx_num;
    }

    $sql= "SELECT sttngid, plc_nm, sttng_plc_nt1, sttng_plc_nt2 FROM ptsttng_plc
          INNER JOIN plc ON sttng_plcid=plc_id
          WHERE ptid='$pt_id'
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

    $sql="SELECT char_grp_id, char_grp FROM ptchar_grp WHERE ptid='$pt_id' ORDER BY char_grp_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character group data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$char_grps[$row['char_grp_id']]=array('char_grp'=>$row['char_grp'], 'chars'=>array());}

    $sql= "SELECT char_grpid, char_nm, char_sffx_num, char_nt FROM ptchar
          INNER JOIN role ON charid=char_id WHERE ptid='$pt_id' ORDER BY char_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      if(empty($char_grps)) {$char_grps['1']=array('char_grp'=>NULL, 'chars'=>array());}
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_nt']) {$char_nt='::'.$row['char_nt'];} else {$char_nt='';}
        if($row['char_sffx_num']) {$char_sffx_num='--'.$row['char_sffx_num'];} else {$char_sffx_num='';}
        if($row['char_grpid']) {$char_grpid=$row['char_grpid'];} else {$char_grpid='1';}
        $char_grps[$char_grpid]['chars'][]=$row['char_nm'].$char_sffx_num.$char_nt;
      }
    }

    if(!empty($char_grps))
    {
      $char_array=array();
      foreach($char_grps as $char_grp)
      {
        if($char_grp['char_grp']) {$char_grp_ttl=$char_grp['char_grp'].'==';} else {$char_grp_ttl='';}
        $char_array[]=$char_grp_ttl.implode(',,', $char_grp['chars']);
      }
      $char_list=html(implode('@@', $char_array));
    }
    else {$char_list='';}

    $sql= "SELECT comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, lcnsr_rl, lcnsr_ordr, comp_bool
          FROM ptlcnsr
          INNER JOIN comp ON lcnsr_compid=comp_id
          WHERE ptid='$pt_id' AND lcnsr_prsnid=0
          UNION
          SELECT prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, lcnsr_rl, lcnsr_ordr, comp_bool
          FROM ptlcnsr
          INNER JOIN prsn ON lcnsr_prsnid=prsn_id
          WHERE ptid='$pt_id' AND lcnsr_compid=0
          ORDER BY lcnsr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring licensor data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_lcnsr_nm_rl=$row['comp_nm1'].$comp_sffx_num.'::'.$row['lcnsr_rl'].'||';
        }
        else
        {$comp_lcnsr_nm_rl='';}
        $prsn_lcnsr_nm_rl='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_lcnsr_nm_rl=$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num.'::'.$row['lcnsr_rl'];
        }
        else
        {$prsn_lcnsr_nm_rl='';}
        $comp_lcnsr_nm_rl='';
      }
      $lcnsrs[$row['comp_id']]=array('comp_lcnsr_nm_rl'=>$comp_lcnsr_nm_rl, 'prsn_lcnsr_nm_rl'=>$prsn_lcnsr_nm_rl, 'lcnsrcomp_ppl'=>array());
    }

    $sql= "SELECT lcnsr_compid, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, lcnsr_rl
          FROM ptlcnsr
          INNER JOIN prsn ON lcnsr_prsnid=prsn_id
          WHERE ptid='$pt_id' AND lcnsr_compid!=0
          ORDER BY lcnsr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring licensor (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $lcnsrs[$row['lcnsr_compid']]['lcnsrcomp_ppl'][]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num.'::'.$row['lcnsr_rl'];
    }
    if(!empty($lcnsrs))
    {
      $lcnsr_array=array();
      foreach($lcnsrs as $lcnsr)
      {
        $lcnsrcomp_ppl_list=implode('//', $lcnsr['lcnsrcomp_ppl']);
        $lcnsr_array[]=$lcnsr['comp_lcnsr_nm_rl'].$lcnsr['prsn_lcnsr_nm_rl'].$lcnsrcomp_ppl_list;
      }
      $lcnsr_list=html(implode(',,', $lcnsr_array));
    }
    else {$lcnsr_list='';}

    $sql= "SELECT pt_alt_nm, pt_alt_nm_dscr
          FROM pt_alt_nm
          WHERE ptid='$pt_id'
          ORDER BY pt_alt_nm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext alternate name data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['pt_alt_nm_dscr']) {$alt_nm_dscr='::'.$row['pt_alt_nm_dscr'];} else {$alt_nm_dscr='';}
      $alt_nms[]=$row['pt_alt_nm'].$alt_nm_dscr;
    }
    if(!empty($alt_nms)) {$alt_nm_list=html(implode(',,', $alt_nms));} else {$alt_nm_list='';}

    $textarea='';
    $pt_id=html($pt_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $pt_id=cln($_POST['pt_id']);
    $pt_nm=trim(cln($_POST['pt_nm']));
    $pt_sbnm=trim(cln($_POST['pt_sbnm']));
    $txt_vrsn_list=cln($_POST['txt_vrsn_list']);
    if(isset($_POST['pt_yr_strtd_c'])) {$pt_yr_strtd_c='1';} else {$pt_yr_strtd_c='0';}
    $pt_yr_strtd=cln($_POST['pt_yr_strtd']);
    if(isset($_POST['pt_yr_strtd_bce'])) {$pt_yr_strtd_bce='1';} else {$pt_yr_strtd_bce='0';}
    if(isset($_POST['pt_yr_wrttn_c'])) {$pt_yr_wrttn_c='1';} else {$pt_yr_wrttn_c='0';}
    $pt_yr_wrttn=cln($_POST['pt_yr_wrttn']);
    if(isset($_POST['pt_yr_wrttn_bce'])) {$pt_yr_wrttn_bce='1';} else {$pt_yr_wrttn_bce='0';}
    $pt_sffx_num=trim(cln($_POST['pt_sffx_num']));
    $pt_pub_dt=cln($_POST['pt_pub_dt']);
    if($_POST['pt_pub_dt_frmt']=='1') {$pt_pub_dt_frmt='1';} if($_POST['pt_pub_dt_frmt']=='2') {$pt_pub_dt_frmt='2';}
    if($_POST['pt_pub_dt_frmt']=='3') {$pt_pub_dt_frmt='3';} if($_POST['pt_pub_dt_frmt']=='4') {$pt_pub_dt_frmt='4';}
    if($_POST['pt_coll']=='1') {$pt_coll='1'; $coll_wrks=''; $coll_ov=''; $coll_sg='';}
    if($_POST['pt_coll']=='2') {$pt_coll='2'; $coll_wrks='1'; $coll_ov=''; $coll_sg='';}
    if($_POST['pt_coll']=='3') {$pt_coll='3'; $coll_wrks=''; $coll_ov='1'; $coll_sg='';}
    if($_POST['pt_coll']=='4') {$pt_coll='4'; $coll_wrks=''; $coll_ov=''; $coll_sg='1';}
    $pt_wrks_sg_list=cln($_POST['pt_wrks_sg_list']);
    $pt_coll_sg_list=cln($_POST['pt_coll_sg_list']);
    $pt_lnk_list=cln($_POST['pt_lnk_list']);
    $wri_list=cln($_POST['wri_list']);
    $cntr_list=cln($_POST['cntr_list']);
    $mat_list=cln($_POST['mat_list']);
    $ctgry_list=cln($_POST['ctgry_list']);
    $gnr_list=cln($_POST['gnr_list']);
    $ftr_list=cln($_POST['ftr_list']);
    $thm_list=cln($_POST['thm_list']);
    $sttng_list=cln($_POST['sttng_list']);
    $cst_m=cln($_POST['cst_m']);
    $cst_f=cln($_POST['cst_f']);
    $cst_non_spc=cln($_POST['cst_non_spc']);
    if(isset($_POST['cst_addt'])) {$cst_addt='1';} else {$cst_addt='0';}
    $cst_nt=trim(cln($_POST['cst_nt']));
    $char_list=cln($_POST['char_list']);
    $lcnsr_list=cln($_POST['lcnsr_list']);
    $alt_nm_list=cln($_POST['alt_nm_list']);
    $pt='1'; $tr_lg=NULL;

    $errors=array();

    if(!preg_match('/\S+/', $pt_nm))
    {$errors['pt_nm']='**You must enter a playtext name.**';}
    elseif(preg_match('/##/', $pt_nm) || preg_match('/--/', $pt_nm) || preg_match('/::/', $pt_nm) || preg_match('/,,/', $pt_nm) ||
    preg_match('/@@/', $pt_nm) || preg_match('/==/', $pt_nm) || preg_match('/>>/', $pt_nm) || preg_match('/~~/', $pt_nm) ||
    preg_match('/\+\+/', $pt_nm))
    {$errors['pt_nm']='</br>**Playtext name cannot include any of the following: [##], [--], [::], [,,], [@@], [==], [>>], [~~], [++].**';}

    if(preg_match('/\S+/', $pt_sbnm))
    {if(strlen($pt_sbnm)>255) {$errors['pt_sbnm_excss_lngth']='</br>**Playtext sub-name is allowed a maximum of 255 characters.**';}}

    include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_pt_vldtn.inc.php';
    //FILE COMPRISES: sbnm / mat_list / txt_vrsn_list / ctgry_list / gnr_list / ftr_list / thm_list / sttng_list / wri_list / alt_nm_list

    if($pt_yr_strtd)
    {
      if(!preg_match('/^[1-9][0-9]{0,3}$/', $pt_yr_strtd))
      {$errors['pt_yr_strtd']='**You must enter a valid year or leave blank.**'; $pt_yr_strtd_num=NULL;}
      elseif($pt_yr_strtd_bce)
      {$pt_yr_strtd_num='-'.$pt_yr_strtd;}
      else
      {$pt_yr_strtd_num=$pt_yr_strtd;}
    }
    else
    {$pt_yr_strtd_num=NULL;}

    if(!preg_match('/^[1-9][0-9]{0,3}$/', $pt_yr_wrttn))
    {$errors['pt_yr_wrttn']='**You must enter a valid year.**'; $pt_yr_wrttn_num=NULL;}
    elseif($pt_yr_wrttn_bce)
    {$pt_yr_wrttn_num='-'.$pt_yr_wrttn;}
    else
    {$pt_yr_wrttn_num=$pt_yr_wrttn;}

    if($pt_yr_strtd_num && $pt_yr_wrttn_num && $pt_yr_strtd_num >= $pt_yr_wrttn_num)
    {
      $errors['pt_yr_strtd']='**Must be earlier than year written date.**';
      $errors['pt_yr_wrttn']='**Must be later than year started date.**';
    }

    if(preg_match('/^[0]*$/', $pt_sffx_num) || !$pt_sffx_num)
    {$pt_sffx_num='0'; $pt_sffx_rmn='';}
    elseif(preg_match('/^[1-9][0-9]{0,1}$/', $pt_sffx_num))
    {$pt_sffx_rmn=romannumeral($pt_sffx_num);}
    else
    {$errors['pt_sffx']='**The suffix must be a valid integer between 1 and 99 (with no leading 0) or left blank (or as 0).**';}

    if(count($errors)==0)
    {
      if($pt_sffx_rmn) {$pt_sffx_rmn_session=' ('.romannumeral($_POST['pt_sffx_num']).')'; $pt_sffx_rmn_dsply=' ('.$pt_sffx_rmn.')';}
      else {$pt_sffx_rmn_session=''; $pt_sffx_rmn_dsply='';}

      if($pt_yr_strtd)
      {
        if($pt_yr_strtd_bce && !$pt_yr_wrttn_bce) {$pt_yr_strtd_dsply=$pt_yr_strtd.' BCE-'; $pt_yr_strtd_session=$_POST['pt_yr_strtd'].' BCE-';}
        else {$pt_yr_strtd_dsply=$pt_yr_strtd.'-'; $pt_yr_strtd_session=$_POST['pt_yr_strtd'].'-';}

        if($pt_yr_strtd_c) {$pt_yr_strtd_dsply='c.'.$pt_yr_strtd_dsply; $pt_yr_strtd_session='c.'.$pt_yr_strtd_session;}
      }
      else {$pt_yr_strtd_dsply=''; $pt_yr_strtd_session=''; $pt_yr_strtd_c='0';}

      if($pt_yr_wrttn_bce) {$pt_yr_wrttn_dsply=$pt_yr_wrttn.' BCE'; $pt_yr_wrttn_session=$_POST['pt_yr_wrttn'].' BCE';}
      else {$pt_yr_wrttn_dsply=$pt_yr_wrttn; $pt_yr_wrttn_session=$_POST['pt_yr_wrttn'];}

      if($pt_yr_wrttn_c) {$pt_yr_wrttn_dsply='c.'.$pt_yr_wrttn_dsply; $pt_yr_wrttn_session='c.'.$pt_yr_wrttn_session;}

      $pt_nm_yr=$pt_nm.' ('.$pt_yr_strtd_dsply.$pt_yr_wrttn_dsply.')';
      $pt_nm_yr_session=$_POST['pt_nm'].' ('.$pt_yr_strtd_session.$pt_yr_wrttn_session.')'.$pt_sffx_rmn_session;

      $pt_url=generateurl($pt_nm_yr.$pt_sffx_rmn_dsply);
      $pt_alph=alph($pt_nm);

      if(strlen($pt_nm_yr)>255 || strlen($pt_url)>255)
      {$errors['pt_nm_yr_excss_lngth']='</br>**Playtext full name (inc. year) and playtext URL are allowed a maximum of 255 characters each.**';}

      $sql= "SELECT pt_id, pt_nm_yr, pt_sffx_num
            FROM pt
            WHERE pt_url='$pt_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing playtext URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['pt_id']!==$pt_id)
      {
        if($row['pt_sffx_num']) {$pt_sffx_rmn=' ('.romannumeral($row['pt_sffx_num']).')';} else {$pt_sffx_rmn='';}
        $errors['pt_url']='</br>**Duplicate URL exists for: '.html($row['pt_nm_yr'].$pt_sffx_rmn).'. You must keep the original name and format or assign values without an existing URL.**';
      }
    }

    if($pt_pub_dt)
    {
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $pt_pub_dt))
      {$errors['pt_pub_dt']='**You must enter a valid publication date in the prescribed format or leave empty.**'; $pt_pub_dt=NULL;}
      else
      {
        list($pt_pub_dt_YYYY, $pt_pub_dt_MM, $pt_pub_dt_DD)=explode('-', $pt_pub_dt);
        if(!checkdate((int)$pt_pub_dt_MM, (int)$pt_pub_dt_DD, (int)$pt_pub_dt_YYYY))
        {$errors['pt_pub_dt']='**You must enter a valid publication date or leave empty.**'; $pt_pub_dt=NULL;}
      }
    }
    else
    {$pt_pub_dt=NULL;}

    if(!$coll_sg)
    {
      $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url
            FROM pt p1
            INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
            WHERE p1.pt_id='$pt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing collection overview associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        if($row['pt_yr_strtd_c']) {$ov_pt_yr_strtd_c='c.';} else {$ov_pt_yr_strtd_c='';}
        if($row['pt_yr_strtd'])
        { if(preg_match('/^-/', $row['pt_yr_strtd']))
          { $ov_pt_yr_strtd=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $row['pt_yr_strtd']);
            if(!preg_match('/^-/', $row['pt_yr_wrttn']))
            {$ov_pt_yr_strtd .= ' BCE';}
          }
          else
          {$ov_pt_yr_strtd=$row['pt_yr_strtd'];}
          $ov_pt_yr_strtd .= '-';
        }
        else {$ov_pt_yr_strtd='';}
        if($row['pt_yr_wrttn_c']) {$ov_pt_yr_wrttn_c='c.';} else {$ov_pt_yr_wrttn_c='';}
        if(preg_match('/^-/', $row['pt_yr_wrttn'])) {$ov_pt_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', '$1 BCE', $row['pt_yr_wrttn']);}
        else {$ov_pt_yr_wrttn=$row['pt_yr_wrttn'];}
        $ov_pt='<a href="/playtext/'.html($row['pt_url']).'" target="/playtext/'.html($row['pt_url']).'">'.html($row['pt_nm']).'</a> ('.html($ov_pt_yr_strtd_c.$ov_pt_yr_strtd.$ov_pt_yr_wrttn_c.$ov_pt_yr_wrttn).')';
        $errors['coll_ov_assoc']="</br>**Playtext has an existing collection overview association (with ".$ov_pt.") and must remain as a collection segment or first have its association removed (via the overview's edit page).**";
      }
    }

    if($coll_wrks)
    {
      $sql= "SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url
            FROM ptwrks
            INNER JOIN pt ON wrks_ov=pt_id
            WHERE wrks_sg='$pt_id' LIMIT 1";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing collected works associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        if($row['pt_yr_strtd_c']) {$wrks_pt_yr_strtd_c='c.';} else {$wrks_pt_yr_strtd_c='';}
        if($row['pt_yr_strtd'])
        { if(preg_match('/^-/', $row['pt_yr_strtd']))
          { $wrks_pt_yr_strtd=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $row['pt_yr_strtd']);
            if(!preg_match('/^-/', $row['pt_yr_wrttn']))
            {$wrks_pt_yr_strtd .= ' BCE';}
          }
          else
          {$wrks_pt_yr_strtd=$row['pt_yr_strtd'];}
          $wrks_pt_yr_strtd .= '-';
        }
        else {$wrks_pt_yr_strtd='';}
        if($row['pt_yr_wrttn_c']) {$wrks_pt_yr_wrttn_c='c.';} else {$wrks_pt_yr_wrttn_c='';}
        if(preg_match('/^-/', $row['pt_yr_wrttn'])) {$wrks_pt_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', '$1 BCE', $row['pt_yr_wrttn']);}
        else {$wrks_pt_yr_wrttn=$row['pt_yr_wrttn'];}
        $wrks_pt='<a href="/playtext/'.html($row['pt_url']).'" target="/playtext/'.html($row['pt_url']).'">'.html($row['pt_nm']).'</a> ('.html($wrks_pt_yr_strtd_c.$wrks_pt_yr_strtd.$wrks_pt_yr_wrttn_c.$wrks_pt_yr_wrttn).')';
        $errors['coll_wrks_assoc']="</br>**Playtext has an existing collected works association (with ".$wrks_pt.") and must remain as a collected works segment or first have its association removed (via the overview's edit page).**";
      }

      $sql= "SELECT prd_id, prd_nm, prd_url
            FROM prdpt
            INNER JOIN prd ON prdid=prd_id
            WHERE ptid='$pt_id' LIMIT 1";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing production associations (for collected works): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {$errors['coll_wrks_prd_assoc']="</br>**Playtext has existing production associations and if checked as a collected works must first have these association removed (via those productions' edit pages)**";}
    }

    if(preg_match('/\S+/', $pt_wrks_sg_list))
    {
      if(!$coll_wrks) {$errors['wrks_sg_ov_unchckd']='</br>**Collected works must be checked before you are able to assign collected works segments.**';}
      else
      {
        $wrks_sg_sbhdr_pts=explode('@@', $_POST['pt_wrks_sg_list']);
        if(count($wrks_sg_sbhdr_pts)>250) {$errors['wrks_sg_sbhdr_pt_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $wrks_sg_sbhdr_pt_empty_err_arr=array(); $wrks_sg_eql_excss_err_arr=array(); $wrks_sg_eql_err_arr=array();
          $wrks_sg_sbhdr_err_arr=array(); $wrks_sg_empty_err_arr=array(); $wrks_sg_cln_excss_err_arr=array();
          $wrks_sg_cln_err_arr=array(); $wrks_sg_hyphn_excss_err_arr=array(); $wrks_sg_sffx_err_arr=array();
          $wrks_sg_hyphn_err_arr=array(); $wrks_sg_hsh_excss_err_arr=array(); $wrks_sg_yr_err_arr=array();
          $wrks_sg_yr_frmt_err_arr=array(); $wrks_sg_hsh_err_arr=array(); $wrks_sg_dplct_arr=array();
          $wrks_sg_url_err_arr=array(); $wrks_sg_nonexst_err_arr=array(); $wrks_chckd_err_arr=array();
          $wrks_sg_id_array=array(); $wrks_lnk_assoc_err_arr=array(); $wrks_sg_lnk_assoc_err_arr=array();
          $wrks_coll_ov_sg_err_arr=array();
          foreach($wrks_sg_sbhdr_pts as $wrks_sg_sbhdr_pt)
          {
            $wrks_sg_sbhdr_pt = trim($wrks_sg_sbhdr_pt);
            if(!preg_match('/\S+/', $wrks_sg_sbhdr_pt))
            {
              $wrks_sg_sbhdr_pt_empty_err_arr[]=$wrks_sg_sbhdr_pt;
              if(count($wrks_sg_sbhdr_pt_empty_err_arr)==1) {$errors['wrks_sg_sbhdr_pt_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
              else {$errors['wrks_sg_sbhdr_pt_empty']='</br>**There are '.count($wrks_sg_sbhdr_pt_empty_err_arr).' empty entries in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($wrks_sg_sbhdr_pt, '==')>1)
              {
                $wrks_sg_list='0'; $wrks_sg_eql_excss_err_arr[]=$wrks_sg_sbhdr_pt;
                $errors['wrks_sg_eql_excss']='</br>**You may only use [==] for subheader assignment once per collected works segment array. Please amend: '.html(implode(' / ', $wrks_sg_eql_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*==.*\S$/', $wrks_sg_sbhdr_pt))
              {
                list($wrks_sbhdr, $wrks_sg_list)=explode('==', $wrks_sg_sbhdr_pt);
                $wrks_sbhdr=trim($wrks_sbhdr); $wrks_sg_list=trim($wrks_sg_list);
                if(strlen($wrks_sbhdr)>255) {$errors['wrks_sbhdr_excss_lngth']='</br>**Collection segment subheaders are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
              }
              elseif(substr_count($wrks_sg_sbhdr_pt, '==')==1)
              {$wrks_sg_list='0'; $wrks_sg_eql_err_arr[]=$wrks_sg_sbhdr_pt;
              $errors['wrks_sg_eql']='</br>**Collected works segment subheader assignation must use [==] in the correct format. Please amend: '.html(implode(' / ', $wrks_sg_eql_err_arr)).'**';}
              else
              {
                if(count($wrks_sg_sbhdr_pts)>1) {$wrks_sg_sbhdr_err_arr[]=$wrks_sg_sbhdr_pt; $errors['wrks_sg_sbhdr']='</br>**If more than one subdivision is created, subheaders must be assigned to each. Please amend: '.html(implode(' / ', $wrks_sg_sbhdr_err_arr)).'**';}
                $wrks_sg_list=$wrks_sg_sbhdr_pt;
              }

              if($wrks_sg_list)
              {
                $wrks_sg_nm_yrs=explode(',,', $wrks_sg_list);
                if(count($wrks_sg_nm_yrs)>250) {$errors['wrks_sg_pts_array_excss']='**Maximum of 250 entries allowed.**';}
                else
                {
                  foreach($wrks_sg_nm_yrs as $wrks_sg_nm_yr)
                  {
                    $wrks_sg_errors=0;
                    if(!preg_match('/\S+/', $wrks_sg_nm_yr))
                    {
                      $wrks_sg_empty_err_arr[]=$wrks_sg_nm_yr;
                      if(count($wrks_sg_empty_err_arr)==1) {$errors['wrks_sg_empty']='</br>**There is 1 empty entry in the segment arrays (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                      else {$errors['wrks_sg_empty']='</br>**There are '.count($wrks_sg_empty_err_arr).' empty entries in the segment arrays (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      if(substr_count($wrks_sg_nm_yr, '::')>1)
                      {
                        $wrks_sg_errors++; $wrks_sg_nm_yr=trim($wrks_sg_nm_yr);
                        $wrks_sg_cln_excss_err_arr[]=$wrks_sg_nm_yr;
                        $errors['wrks_sg_cln_excss']='</br>**You may only use [::] once per playtext collected works segment-role coupling. Please amend: '.html(implode(' / ', $wrks_sg_cln_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*::.*\S+/', $wrks_sg_nm_yr))
                      {
                        list($wrks_sg_nm_yr, $wrks_sg_rl)=explode('::', $wrks_sg_nm_yr);
                        $wrks_sg_nm_yr=trim($wrks_sg_nm_yr); $wrks_sg_rl=trim($wrks_sg_rl);

                        if(strlen($wrks_sg_rl)>255)
                        {$errors['wrks_sg_rl_excss_lngth']='</br>**Playtext collected works segment role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($wrks_sg_nm_yr, '::')==1)
                      {$wrks_sg_errors++; $wrks_sg_cln_err_arr[]=$wrks_sg_nm_yr;
                      $errors['wrks_sg_cln']='</br>**Collected works segment role assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $wrks_sg_cln_err_arr)).'**';}

                      if(substr_count($wrks_sg_nm_yr, '--')>1)
                      {
                        $wrks_sg_errors++; $wrks_sg_sffx_num='0'; $wrks_sg_hyphn_excss_err_arr[]=$wrks_sg_nm_yr;
                        $errors['wrks_sg_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per collected works segment playtext. Please amend: '.html(implode(' / ', $wrks_sg_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $wrks_sg_nm_yr))
                      {
                        list($wrks_sg_nm_yr_no_sffx, $wrks_sg_sffx_num)=explode('--', $wrks_sg_nm_yr);
                        $wrks_sg_nm_yr_no_sffx=trim($wrks_sg_nm_yr_no_sffx); $wrks_sg_sffx_num=trim($wrks_sg_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $wrks_sg_sffx_num))
                        {
                          $wrks_sg_errors++; $wrks_sg_sffx_num='0'; $wrks_sg_sffx_err_arr[]=$wrks_sg_nm_yr;
                          $errors['wrks_sg_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $wrks_sg_sffx_err_arr)).'**';
                        }
                        $wrks_sg_nm_yr=$wrks_sg_nm_yr_no_sffx;
                      }
                      elseif(substr_count($wrks_sg_nm_yr, '--')==1)
                      {$wrks_sg_errors++; $wrks_sg_sffx_num='0'; $wrks_sg_hyphn_err_arr[]=$wrks_sg_nm_yr;
                      $errors['wrks_sg_hyphn']='</br>**Collected works segment playtext suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $wrks_sg_hyphn_err_arr)).'**';}
                      else
                      {$wrks_sg_sffx_num='0';}

                      if($wrks_sg_sffx_num) {$wrks_sg_sffx_rmn=' ('.romannumeral($wrks_sg_sffx_num).')';} else {$wrks_sg_sffx_rmn='';}

                      if(substr_count($wrks_sg_nm_yr, '##')>1) {$wrks_sg_errors++; $wrks_sg_hsh_excss_err_arr[]=$wrks_sg_nm_yr; $errors['wrks_sg_hsh_excss']='</br>**You may only use [##] for year written assignment once per collected works segment playtext. Please amend: '.html(implode(' / ', $wrks_sg_hsh_excss_err_arr)).'.**';}
                      elseif(preg_match('/^\S+.*##.*\S+$/', $wrks_sg_nm_yr))
                      {
                        list($wrks_sg_nm, $wrks_sg_yr)=explode('##', $wrks_sg_nm_yr);
                        $wrks_sg_nm=trim($wrks_sg_nm); $wrks_sg_yr=trim($wrks_sg_yr);

                        if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}(\s*;;\s*(c)?(-)?[1-9][0-9]{0,3})?$/', $wrks_sg_yr))
                        {
                          if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $wrks_sg_yr))
                          {
                            list($wrks_sg_yr_strtd, $wrks_sg_yr_wrttn)=explode(';;', $wrks_sg_yr);
                            $wrks_sg_yr_strtd=trim($wrks_sg_yr_strtd); $wrks_sg_yr_wrttn=trim($wrks_sg_yr_wrttn);

                            if(preg_match('/^c(-)?/', $wrks_sg_yr_strtd)) {$wrks_sg_yr_strtd=preg_replace('/^c(.+)$/', '$1', $wrks_sg_yr_strtd); $wrks_sg_yr_strtd_c='1';}
                            else {$wrks_sg_yr_strtd_c=NULL;}

                            if(preg_match('/^c(-)?/', $wrks_sg_yr_wrttn)) {$wrks_sg_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $wrks_sg_yr_wrttn); $wrks_sg_yr_wrttn_c='1';}
                            else {$wrks_sg_yr_wrttn_c=NULL;}

                            if($wrks_sg_yr_strtd >= $wrks_sg_yr_wrttn) {$wrks_sg_errors++; $wrks_sg_yr_err_arr[]=$wrks_sg_nm_yr; $errors['wrks_sg_yr']='</br>**Collected works segment playtext year started must be earlier than year written. Please amend: '.html(implode(' / ', $wrks_sg_yr_err_arr)).'.**';}
                          }
                          else
                          {
                            $wrks_sg_yr_strtd_c=NULL; $wrks_sg_yr_strtd=NULL; $wrks_sg_yr_wrttn=$wrks_sg_yr;
                            if(preg_match('/^c(-)?/', $wrks_sg_yr_wrttn)) {$wrks_sg_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $wrks_sg_yr_wrttn); $wrks_sg_yr_wrttn_c='1';}
                            else {$wrks_sg_yr_wrttn_c=NULL;}
                          }

                          if($wrks_sg_yr_strtd)
                          {
                            if(preg_match('/^-/', $wrks_sg_yr_strtd)) {$wrks_sg_yr_strtd=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $wrks_sg_yr_strtd); if(!preg_match('/^-/', $wrks_sg_yr_wrttn)) {$wrks_sg_yr_strtd .= ' BCE';}}
                            $wrks_sg_yr_strtd .= '-';
                            if($wrks_sg_yr_strtd_c) {$wrks_sg_yr_strtd='c.'.$wrks_sg_yr_strtd;}
                          }
                          else {$wrks_sg_yr_strtd='';}

                          if(preg_match('/^-/', $wrks_sg_yr_wrttn)) {$wrks_sg_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE", $wrks_sg_yr_wrttn);}
                          if($wrks_sg_yr_wrttn_c) {$wrks_sg_yr_wrttn='c.'.$wrks_sg_yr_wrttn;}

                          $wrks_sg_nm_yr=$wrks_sg_nm.' ('.$wrks_sg_yr_strtd.$wrks_sg_yr_wrttn.')'; $wrks_sg_url=generateurl($wrks_sg_nm_yr.$wrks_sg_sffx_rmn);
                          $wrks_sg_dplct_arr[]=$wrks_sg_url; if(count(array_unique($wrks_sg_dplct_arr))<count($wrks_sg_dplct_arr)) {$errors['wrks_sg_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}
                          if(strlen($wrks_sg_nm_yr)>255 || strlen($wrks_sg_url)>255) {$wrks_sg_errors++; $errors['wrks_sg_nm_yr_excss_lngth']='</br>**Collected works segment playtext name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                        }
                        else {$wrks_sg_errors++; $wrks_sg_yr_frmt_err_arr[]=$wrks_sg_nm_yr; $errors['wrks_sg_yr_frmt']='</br>**Collected works segment playtexts must be assigned a valid year (or years). Please amend: '.html(implode(' / ', $wrks_sg_yr_frmt_err_arr)).'.**';}
                      }
                      else {$wrks_sg_errors++; $wrks_sg_nm=$wrks_sg_nm_yr; $wrks_sg_hsh_err_arr[]=$wrks_sg_nm_yr; $errors['wrks_sg_hsh']='</br>**You must assign a playtext year in the correct format to the following using [##]: '.html(implode(' / ', $wrks_sg_hsh_err_arr)).'.**';}

                      if($wrks_sg_errors==0)
                      {
                        $wrks_sg_nm_yr_cln=cln($wrks_sg_nm_yr); $wrks_sg_sffx_num_cln=cln($wrks_sg_sffx_num); $wrks_sg_url_cln=cln($wrks_sg_url);
                        $sql= "SELECT pt_nm, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn
                              FROM pt
                              WHERE NOT EXISTS (SELECT 1 FROM pt WHERE pt_nm_yr='$wrks_sg_nm_yr_cln' AND pt_sffx_num='$wrks_sg_sffx_num_cln')
                              AND pt_url='$wrks_sg_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing playtext URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['pt_yr_strtd_c']) {$wrks_sg_yr_strtd_c='c';} else {$wrks_sg_yr_strtd_c='';}
                          if($row['pt_yr_strtd']) {$wrks_sg_yr_strtd=$row['pt_yr_strtd'].';;';} else {$wrks_sg_yr_strtd='';}
                          if($row['pt_yr_wrttn_c']) {$wrks_sg_yr_wrttn_c='c';} else {$wrks_sg_yr_wrttn_c='';}
                          if($row['pt_sffx_num']) {$wrks_sg_sffx_num='--'.$row['pt_sffx_num'];} else {$wrks_sg_sffx_num='';}
                          $wrks_sg_url_err_arr[]=$row['pt_nm'].'##'.$wrks_sg_yr_strtd_c.$wrks_sg_yr_strtd.$wrks_sg_yr_wrttn_c.$row['pt_yr_wrttn'].$wrks_sg_sffx_num;
                          if(count($wrks_sg_url_err_arr)==1) {$errors['wrks_sg_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $wrks_sg_url_err_arr)).'?**';}
                          else {$errors['wrks_sg_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $wrks_sg_url_err_arr)).'?**';}
                        }
                        else
                        {
                          $sql="SELECT pt_id, pt_coll FROM pt WHERE pt_url='$wrks_sg_url_cln'";
                          $result=mysqli_query($link, $sql);
                          if(!$result) {$error='Error checking for existing playtext URL (against collected works segment playtext): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                          $row=mysqli_fetch_array($result);
                          if(mysqli_num_rows($result)==0)
                          {
                            $wrks_sg_nonexst_err_arr[]=$wrks_sg_nm_yr.$wrks_sg_sffx_rmn;
                            $errors['wrks_sg_nonexst']='</br>**The following are not existing playtexts: '.html(implode(' / ', $wrks_sg_nonexst_err_arr)).'.**';
                          }
                          else
                          {
                            $wrks_sg_id=$row['pt_id'];
                            if($wrks_sg_id==$pt_id)
                            {$errors['wrks_sg_id_mtch']='</br>**You cannot assign this playtext as a collected works segment of itself: '.html($wrks_sg_nm_yr.$wrks_sg_sffx_rmn).'.**';}
                            else
                            {
                              if($row['pt_coll']=='2')
                              {
                                $wrks_chckd_err_arr[]=$wrks_sg_nm_yr.$wrks_sg_sffx_rmn;
                                $errors['wrks_chckd']='</br>**Please amend the following to playtexts that have not been assigned as collected works: '.html(implode(' / ', $wrks_chckd_err_arr)).'.**';
                              }
                              $wrks_sg_id_array[]=$wrks_sg_id;

                              $sql= "SELECT 1 FROM ptlnk WHERE lnk1='$wrks_sg_id' AND lnk2='$pt_id'
                                    UNION
                                    SELECT 1 FROM ptlnk WHERE lnk2='$wrks_sg_id' AND lnk1='$pt_id'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking collected works segments do not have link associations to playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              if(mysqli_num_rows($result)>0)
                              {
                                $wrks_lnk_assoc_err_arr[]=$wrks_sg_nm_yr.$wrks_sg_sffx_rmn;
                                $errors['wrks_lnk_assoc']='</br>**Collected works segments cannot have link associations to playtext. Please remove the following: '.html(implode(' / ', $wrks_lnk_assoc_err_arr)).'.**';
                              }

                              $sql= "SELECT 1 FROM ptlnk pl1 INNER JOIN pt ON pl1.lnk1=pt_id INNER JOIN ptlnk pl2 ON pt_id=pl2.lnk1
                                    WHERE pl1.lnk2='$pt_id' AND pl2.lnk2='$wrks_sg_id'
                                    UNION
                                    SELECT 1 FROM ptlnk pl1 INNER JOIN pt ON pl1.lnk1=pt_id INNER JOIN ptlnk pl2 ON pt_id=pl2.lnk2
                                    WHERE pl1.lnk2='$pt_id' AND pl2.lnk1='$wrks_sg_id'
                                    UNION
                                    SELECT 1 FROM ptlnk pl1 INNER JOIN pt ON pl1.lnk2=pt_id INNER JOIN ptlnk pl2 ON pt_id=pl2.lnk1
                                    WHERE pl1.lnk1='$pt_id' AND pl2.lnk2='$wrks_sg_id'
                                    UNION
                                    SELECT 1 FROM ptlnk pl1 INNER JOIN pt ON pl1.lnk2=pt_id INNER JOIN ptlnk pl2 ON pt_id=pl2.lnk2
                                    WHERE pl1.lnk1='$pt_id' AND pl2.lnk1='$wrks_sg_id'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking collected works segments do not have same link associations as playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              if(mysqli_num_rows($result)>0)
                              {
                                $wrks_sg_lnk_assoc_err_arr[]=$wrks_sg_nm_yr.$wrks_sg_sffx_rmn;
                                $errors['wrks_sg_lnk_assoc']='</br>**Collected works segments cannot have same link associations as playtext. Please remove the following: '.html(implode(' / ', $wrks_sg_lnk_assoc_err_arr)).'.**';
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                  foreach($wrks_sg_id_array as $wrks_sg_id)
                  {
                    $sql="SELECT coll_ov FROM pt WHERE pt_id='$wrks_sg_id' AND coll_ov IS NOT NULL";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking collected works array where both overview and segment parts of a collection exist: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(in_array($row['coll_ov'], $wrks_sg_id_array))
                    {
                      $sql= "SELECT p1.pt_nm AS p1_nm, p1.pt_sffx_num AS p1_num, p1.pt_yr_strtd_c AS p1_strtd_c, p1.pt_yr_strtd AS p1_strtd, p1.pt_yr_wrttn_c AS p1_wrttn_c, p1.pt_yr_wrttn AS p1_wrttn, p2.pt_nm AS p2_nm, p2.pt_sffx_num AS p2_num, p2.pt_yr_strtd_c AS p2_strtd_c, p2.pt_yr_strtd AS p2_strtd, p2.pt_yr_wrttn_c AS p2_wrttn_c, p2.pt_yr_wrttn AS p2_wrttn
                            FROM pt p1
                            INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
                          WHERE p1.pt_id='$wrks_sg_id'";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error acquiring collection overview and segment details (for entries that both appear in collected works array): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      $row=mysqli_fetch_array($result);
                      if($row['p1_strtd_c']) {$wrks_sg_yr_strtd_c='c';} else {$wrks_sg_yr_strtd_c='';}
                      if($row['p1_strtd']) {$wrks_sg_yr_strtd=$row['p1_strtd'].';;';} else {$wrks_sg_yr_strtd='';}
                      if($row['p1_wrttn_c']) {$wrks_sg_yr_wrttn_c='c';} else {$wrks_sg_yr_wrttn_c='';}
                      if($row['p1_num']) {$wrks_sg_sffx_num='--'.$row['p1_num'];} else {$wrks_sg_sffx_num='';}
                      if($row['p2_strtd_c']) {$wrks_ov_yr_strtd_c='c';} else {$wrks_ov_yr_strtd_c='';}
                      if($row['p2_strtd']) {$wrks_ov_yr_strtd=$row['p2_strtd'].';;';} else {$wrks_ov_yr_strtd='';}
                      if($row['p2_wrttn_c']) {$wrks_ov_yr_wrttn_c='c';} else {$wrks_ov_yr_wrttn_c='';}
                      if($row['p2_num']) {$wrks_ov_sffx_num='--'.$row['p2_num'];} else {$wrks_ov_sffx_num='';}
                      $wrks_coll_ov_sg_err_arr[]=$row['p1_nm'].'##'.$wrks_sg_yr_strtd_c.$wrks_sg_yr_strtd.$wrks_sg_yr_wrttn_c.$row['p1_wrttn'].$wrks_sg_sffx_num.' (segment) and '.$row['p2_nm'].'##'.$wrks_ov_yr_strtd_c.$wrks_ov_yr_strtd.$wrks_ov_yr_wrttn_c.$row['p1_wrttn'].$wrks_ov_sffx_num.' (overview)';
                      $errors['wrks_coll_ov_sg_exst']='</br>**Collected works array cannot comprise segments that include both overview and segment parts of a collection. Please remove one part of each pairing of the following: '.html(implode(' / ', $wrks_coll_ov_sg_err_arr)).'.**';
                    }
                  }
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $pt_coll_sg_list))
    {
      if(!$coll_ov) {$errors['coll_sg_ov_unchckd']='</br>**Collection overview must be checked before you are able to assign collection segments.**';}
      else
      {
        $coll_sg_sbhdr_pts=explode('@@', $_POST['pt_coll_sg_list']);
        if(count($coll_sg_sbhdr_pts)>250) {$errors['coll_sg_sbhdr_pt_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $coll_sg_sbhdr_pt_empty_err_arr=array(); $coll_sg_eql_excss_err_arr=array(); $coll_sg_eql_err_arr=array();
          $coll_sg_sbhdr_err_arr=array(); $coll_sg_empty_err_arr=array(); $coll_sg_hyphn_excss_err_arr=array();
          $coll_sg_sffx_err_arr=array(); $coll_sg_hyphn_err_arr=array(); $coll_sg_hsh_excss_err_arr=array();
          $coll_sg_yr_err_arr=array(); $coll_sg_yr_frmt_err_arr=array(); $coll_sg_hsh_err_arr=array();
          $coll_sg_dplct_arr=array(); $coll_sg_url_err_arr=array(); $coll_sg_nonexst_err_arr=array();
          $coll_sg_assoc_err_arr=array(); $coll_sg_unchckd_err_arr=array(); $coll_lnk_assoc_err_arr=array();
          $coll_sg_lnk_assoc_err_arr=array();
          foreach($coll_sg_sbhdr_pts as $coll_sg_sbhdr_pt)
          {
            $coll_sg_sbhdr_pt = trim($coll_sg_sbhdr_pt);
            if(!preg_match('/\S+/', $coll_sg_sbhdr_pt))
            {
              $coll_sg_sbhdr_pt_empty_err_arr[]=$coll_sg_sbhdr_pt;
              if(count($coll_sg_sbhdr_pt_empty_err_arr)==1) {$errors['coll_sg_sbhdr_pt_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
              else {$errors['coll_sg_sbhdr_pt_empty']='</br>**There are '.count($coll_sg_sbhdr_pt_empty_err_arr).' empty entries in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($coll_sg_sbhdr_pt, '==')>1)
              {
                $coll_sg_list='0'; $coll_sg_eql_excss_err_arr[]=$coll_sg_sbhdr_pt;
                $errors['coll_sg_eql_excss']='</br>**You may only use [==] for subheader assignment once per collection segment array. Please amend: '.html(implode(' / ', $coll_sg_eql_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*==.*\S$/', $coll_sg_sbhdr_pt))
              {
                list($coll_sbhdr, $coll_sg_list)=explode('==', $coll_sg_sbhdr_pt);
                $coll_sbhdr=trim($coll_sbhdr); $coll_sg_list=trim($coll_sg_list);
                if(strlen($coll_sbhdr)>255) {$errors['coll_sbhdr_excss_lngth']='</br>**Collection segment subheaders are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
              }
              elseif(substr_count($coll_sg_sbhdr_pt, '==')==1)
              {$coll_sg_list='0'; $coll_sg_eql_err_arr[]=$coll_sg_sbhdr_pt;
              $errors['coll_sg_eql']='</br>**Collection segment subheader assignation must use [==] in the correct format. Please amend: '.html(implode(' / ', $coll_sg_eql_err_arr)).'**';}
              else
              {
                if(count($coll_sg_sbhdr_pts)>1) {$coll_sg_sbhdr_err_arr[]=$coll_sg_sbhdr_pt; $errors['coll_sg_sbhdr']='</br>**If more than one subdivision is created, subheaders must be assigned to each. Please amend: '.html(implode(' / ', $coll_sg_sbhdr_err_arr)).'**';}
                $coll_sg_list=$coll_sg_sbhdr_pt;
              }

              if($coll_sg_list)
              {
                $coll_sg_nm_yrs=explode(',,', $coll_sg_list);
                if(count($coll_sg_nm_yrs)>250)
                {$errors['coll_sg_pts_array_excss']='**Maximum of 250 entries allowed.**';}
                else
                {
                  foreach($coll_sg_nm_yrs as $coll_sg_nm_yr)
                  {
                    $coll_sg_errors=0;

                    if(!preg_match('/\S+/', $coll_sg_nm_yr))
                    {
                      $coll_sg_empty_err_arr[]=$coll_sg_nm_yr;
                      if(count($coll_sg_empty_err_arr)==1) {$errors['coll_sg_empty']='</br>**There is 1 empty entry in the segment arrays (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                      else {$errors['coll_sg_empty']='</br>**There are '.count($coll_sg_empty_err_arr).' empty entries in the segment arrays (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      if(substr_count($coll_sg_nm_yr, '--')>1)
                      {
                        $coll_sg_errors++; $coll_sg_sffx_num='0'; $coll_sg_hyphn_excss_err_arr[]=$coll_sg_nm_yr;
                        $errors['coll_sg_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per collection segment playtext. Please amend: '.html(implode(' / ', $coll_sg_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $coll_sg_nm_yr))
                      {
                        list($coll_sg_nm_yr_no_sffx, $coll_sg_sffx_num)=explode('--', $coll_sg_nm_yr);
                        $coll_sg_nm_yr_no_sffx=trim($coll_sg_nm_yr_no_sffx); $coll_sg_sffx_num=trim($coll_sg_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $coll_sg_sffx_num))
                        {
                          $coll_sg_errors++; $coll_sg_sffx_num='0'; $coll_sg_sffx_err_arr[]=$coll_sg_nm_yr;
                          $errors['coll_sg_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $coll_sg_sffx_err_arr)).'**';
                        }
                        $coll_sg_nm_yr=$coll_sg_nm_yr_no_sffx;
                      }
                      elseif(substr_count($coll_sg_nm_yr, '--')==1)
                      {$coll_sg_errors++; $coll_sg_sffx_num='0'; $coll_sg_hyphn_err_arr[]=$coll_sg_nm_yr;
                      $errors['coll_sg_hyphn']='</br>**Collection segment playtext suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $coll_sg_hyphn_err_arr)).'**';}
                      else
                      {$coll_sg_sffx_num='0';}

                      if($coll_sg_sffx_num) {$coll_sg_sffx_rmn=' ('.romannumeral($coll_sg_sffx_num).')';} else {$coll_sg_sffx_rmn='';}

                      if(substr_count($coll_sg_nm_yr, '##')>1) {$coll_sg_errors++; $coll_sg_hsh_excss_err_arr[]=$coll_sg_nm_yr; $errors['coll_sg_hsh_excss']='</br>**You may only use [##] for year written assignment once per collection segment playtext. Please amend: '.html(implode(' / ', $coll_sg_hsh_excss_err_arr)).'.**';}
                      elseif(preg_match('/^\S+.*##.*\S+$/', $coll_sg_nm_yr))
                      {
                        list($coll_sg_nm, $coll_sg_yr)=explode('##', $coll_sg_nm_yr);
                        $coll_sg_nm=trim($coll_sg_nm); $coll_sg_yr=trim($coll_sg_yr);

                        if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}(\s*;;\s*(c)?(-)?[1-9][0-9]{0,3})?$/', $coll_sg_yr))
                        {
                          if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $coll_sg_yr))
                          {
                            list($coll_sg_yr_strtd, $coll_sg_yr_wrttn)=explode(';;', $coll_sg_yr);
                            $coll_sg_yr_strtd=trim($coll_sg_yr_strtd); $coll_sg_yr_wrttn=trim($coll_sg_yr_wrttn);

                            if(preg_match('/^c(-)?/', $coll_sg_yr_strtd)) {$coll_sg_yr_strtd=preg_replace('/^c(.+)$/', '$1', $coll_sg_yr_strtd); $coll_sg_yr_strtd_c='1';}
                            else {$coll_sg_yr_strtd_c=NULL;}

                            if(preg_match('/^c(-)?/', $coll_sg_yr_wrttn)) {$coll_sg_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $coll_sg_yr_wrttn); $coll_sg_yr_wrttn_c='1';}
                            else {$coll_sg_yr_wrttn_c=NULL;}

                            if($coll_sg_yr_strtd >= $coll_sg_yr_wrttn) {$coll_sg_errors++; $coll_sg_yr_err_arr[]=$coll_sg_nm_yr; $errors['coll_sg_yr']='</br>**Collection segment playtext year started must be earlier than year written. Please amend: '.html(implode(' / ', $coll_sg_yr_err_arr)).'.**';}
                          }
                          else
                          {
                            $coll_sg_yr_strtd_c=NULL; $coll_sg_yr_strtd=NULL; $coll_sg_yr_wrttn=$coll_sg_yr;
                            if(preg_match('/^c(-)?/', $coll_sg_yr_wrttn)) {$coll_sg_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $coll_sg_yr_wrttn); $coll_sg_yr_wrttn_c='1';}
                            else {$coll_sg_yr_wrttn_c=NULL;}
                          }

                          if($coll_sg_yr_strtd)
                          {
                            if(preg_match('/^-/', $coll_sg_yr_strtd)) {$coll_sg_yr_strtd=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $coll_sg_yr_strtd); if(!preg_match('/^-/', $coll_sg_yr_wrttn)) {$coll_sg_yr_strtd .= ' BCE';}}
                            $coll_sg_yr_strtd .= '-';
                            if($coll_sg_yr_strtd_c) {$coll_sg_yr_strtd='c.'.$coll_sg_yr_strtd;}
                          }
                          else {$coll_sg_yr_strtd='';}

                          if(preg_match('/^-/', $coll_sg_yr_wrttn)) {$coll_sg_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE", $coll_sg_yr_wrttn);}
                          if($coll_sg_yr_wrttn_c) {$coll_sg_yr_wrttn='c.'.$coll_sg_yr_wrttn;}

                          $coll_sg_nm_yr=$coll_sg_nm.' ('.$coll_sg_yr_strtd.$coll_sg_yr_wrttn.')'; $coll_sg_url=generateurl($coll_sg_nm_yr.$coll_sg_sffx_rmn);
                          $coll_sg_dplct_arr[]=$coll_sg_url; if(count(array_unique($coll_sg_dplct_arr))<count($coll_sg_dplct_arr)) {$errors['coll_sg_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}
                          if(strlen($coll_sg_nm_yr)>255 || strlen($coll_sg_url)>255) {$coll_sg_errors++; $errors['coll_sg_nm_yr_excss_lngth']='</br>**Collection segment playtext name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                        }
                        else {$coll_sg_errors++; $coll_sg_yr_frmt_err_arr[]=$coll_sg_nm_yr; $errors['coll_sg_yr_frmt']='</br>**Collection segment playtexts must be assigned a valid year (or years). Please amend: '.html(implode(' / ', $coll_sg_yr_frmt_err_arr)).'.**';}
                      }
                      else {$coll_sg_errors++; $coll_sg_nm=$coll_sg_nm_yr; $coll_sg_hsh_err_arr[]=$coll_sg_nm_yr; $errors['coll_sg_hsh']='</br>**You must assign a playtext year in the correct format to the following using [##]: '.html(implode(' / ', $coll_sg_hsh_err_arr)).'.**';}

                      if($coll_sg_errors==0)
                      {
                        $coll_sg_nm_yr_cln=cln($coll_sg_nm_yr); $coll_sg_sffx_num_cln=cln($coll_sg_sffx_num); $coll_sg_url_cln=cln($coll_sg_url);
                        $sql= "SELECT pt_nm, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn
                              FROM pt
                              WHERE NOT EXISTS (SELECT 1 FROM pt WHERE pt_nm_yr='$coll_sg_nm_yr_cln' AND pt_sffx_num='$coll_sg_sffx_num_cln')
                              AND pt_url='$coll_sg_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing playtext URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['pt_yr_strtd_c']) {$coll_sg_yr_strtd_c='c';} else {$coll_sg_yr_strtd_c='';}
                          if($row['pt_yr_strtd']) {$coll_sg_yr_strtd=$row['pt_yr_strtd'].';;';} else {$coll_sg_yr_strtd='';}
                          if($row['pt_yr_wrttn_c']) {$coll_sg_yr_wrttn_c='c';} else {$coll_sg_yr_wrttn_c='';}
                          if($row['pt_sffx_num']) {$coll_sg_sffx_num='--'.$row['pt_sffx_num'];} else {$coll_sg_sffx_num='';}
                          $coll_sg_url_err_arr[]=$row['pt_nm'].'##'.$coll_sg_yr_strtd_c.$coll_sg_yr_strtd.$coll_sg_yr_wrttn_c.$row['pt_yr_wrttn'].$coll_sg_sffx_num;
                          if(count($coll_sg_url_err_arr)==1) {$errors['coll_sg_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $coll_sg_url_err_arr)).'?**';}
                          else {$errors['coll_sg_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $coll_sg_url_err_arr)).'?**';}
                        }
                        else
                        {
                          $sql="SELECT pt_id FROM pt WHERE pt_url='$coll_sg_url_cln'";
                          $result=mysqli_query($link, $sql);
                          if(!$result) {$error='Error checking for existing playtext URL (against collection segment playtext): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                          $row=mysqli_fetch_array($result);
                          if(mysqli_num_rows($result)==0)
                          {
                            $coll_sg_nonexst_err_arr[]=$coll_sg_nm_yr.$coll_sg_sffx_rmn;
                            $errors['coll_sg_nonexst']='</br>**The following are not existing playtexts: '.html(implode(' / ', $coll_sg_nonexst_err_arr)).'.**';
                          }
                          else
                          {
                            $coll_sg_id=$row['pt_id'];
                            if($coll_sg_id==$pt_id)
                            {$errors['coll_sg_id_mtch']='</br>**You cannot assign this playtext as a collection segment of itself: '.html($coll_sg_nm_yr.$coll_sg_sffx_rmn).'.**';}
                            else
                            {
                              $sql="SELECT coll_ov FROM pt WHERE pt_id='$coll_sg_id' AND coll_ov IS NOT NULL";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking for existing playtext id (against collection segment info): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              $row=mysqli_fetch_array($result);
                              if(mysqli_num_rows($result)>0 && $row['coll_ov']!==$pt_id)
                              {
                                $coll_sg_assoc_err_arr[]=$coll_sg_nm_yr.$coll_sg_sffx_rmn;
                                $errors['coll_sg_assoc']='</br>**Please amend the following to playtexts with no existing collection associations: '.html(implode(' / ', $coll_sg_assoc_err_arr)).'.**';
                              }

                              $sql="SELECT pt_coll FROM pt WHERE pt_id='$coll_sg_id'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking for playtext name and collection segment info (called from entered collection segment info): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              $row=mysqli_fetch_array($result);
                              if($row['pt_coll']!=='4')
                              {
                                $coll_sg_unchckd_err_arr[]=$coll_sg_nm_yr.$coll_sg_sffx_rmn;
                                $errors['coll_sg_unchckd']='</br>**Please amend the following to playtexts that have been assigned as collection segment: '.html(implode(' / ', $coll_sg_unchckd_err_arr)).'.**';
                              }

                              $sql= "SELECT 1 FROM ptlnk WHERE lnk1='$coll_sg_id' AND lnk2='$pt_id'
                                    UNION
                                    SELECT 1 FROM ptlnk WHERE lnk2='$coll_sg_id' AND lnk1='$pt_id'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking collection segments do not have link associations to playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              if(mysqli_num_rows($result)>0)
                              {
                                $coll_lnk_assoc_err_arr[]=$coll_sg_nm_yr.$coll_sg_sffx_rmn;
                                $errors['coll_lnk_assoc']='</br>**Collection segments cannot have link associations to playtext. Please remove the following: '.html(implode(' / ', $coll_lnk_assoc_err_arr)).'.**';
                              }

                              $sql= "SELECT 1 FROM ptlnk pl1 INNER JOIN pt ON pl1.lnk1=pt_id INNER JOIN ptlnk pl2 ON pt_id=pl2.lnk1
                                    WHERE pl1.lnk2='$pt_id' AND pl2.lnk2='$coll_sg_id'
                                    UNION
                                    SELECT 1 FROM ptlnk pl1 INNER JOIN pt ON pl1.lnk1=pt_id INNER JOIN ptlnk pl2 ON pt_id=pl2.lnk2
                                    WHERE pl1.lnk2='$pt_id' AND pl2.lnk1='$coll_sg_id'
                                    UNION
                                    SELECT 1 FROM ptlnk pl1 INNER JOIN pt ON pl1.lnk2=pt_id INNER JOIN ptlnk pl2 ON pt_id=pl2.lnk1
                                    WHERE pl1.lnk1='$pt_id' AND pl2.lnk2='$coll_sg_id'
                                    UNION
                                    SELECT 1 FROM ptlnk pl1 INNER JOIN pt ON pl1.lnk2=pt_id INNER JOIN ptlnk pl2 ON pt_id=pl2.lnk2
                                    WHERE pl1.lnk1='$pt_id' AND pl2.lnk1='$coll_sg_id'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking collection segments do not have same link associations as playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              if(mysqli_num_rows($result)>0)
                              {
                                $coll_sg_lnk_assoc_err_arr[]=$coll_sg_nm_yr.$coll_sg_sffx_rmn;
                                $errors['coll_sg_lnk_assoc']='</br>**Collection segments cannot have same link associations as playtext. Please remove the following: '.html(implode(' / ', $coll_sg_lnk_assoc_err_arr)).'.**';
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
    }

    if(preg_match('/\S+/', $pt_lnk_list))
    {
      $lnk_nm_yrs=explode(',,', $_POST['pt_lnk_list']);
      $lnk_empty_err_arr=array(); $lnk_hyphn_excss_err_arr=array(); $lnk_sffx_err_arr=array();
      $lnk_hyphn_err_arr=array(); $lnk_hsh_excss_err_arr=array(); $lnk_yr_err_arr=array();
      $lnk_yr_frmt_err_arr=array(); $lnk_hsh_err_arr=array(); $lnk_dplct_arr=array();
      $lnk_url_err_arr=array(); $lnk_nonexst_err_arr=array(); $lnk_id_array=array();
      $lnk_wrks_assoc_err_arr=array(); $lnk_wrks_lnk_assoc_err_arr=array(); $lnk_coll_assoc_err_arr=array();
      $lnk_coll_lnk_assoc_err_arr=array(); $lnk_wrks_ov_sg_err_arr=array(); $lnk_coll_ov_sg_err_arr=array();
      foreach($lnk_nm_yrs as $lnk_nm_yr)
      {
        $lnk_errors=0;
        if(!preg_match('/\S+/', $lnk_nm_yr))
        {
          $lnk_empty_err_arr[]=$lnk_nm_yr;
          if(count($lnk_empty_err_arr)==1) {$errors['lnk_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          else {$errors['lnk_empty']='</br>**There are '.count($lnk_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
        }
        else
        {
          if(substr_count($lnk_nm_yr, '--')>1)
          {
            $lnk_errors++; $lnk_sffx_num='0'; $lnk_hyphn_excss_err_arr[]=$lnk_nm_yr;
            $errors['lnk_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per link playtext. Please amend: '.html(implode(' / ', $lnk_hyphn_excss_err_arr)).'.**';
          }
          elseif(preg_match('/^\S+.*--.+$/', $lnk_nm_yr))
          {
            list($lnk_nm_yr_no_sffx, $lnk_sffx_num)=explode('--', $lnk_nm_yr);
            $lnk_nm_yr_no_sffx=trim($lnk_nm_yr_no_sffx); $lnk_sffx_num=trim($lnk_sffx_num);

            if(!preg_match('/^[1-9][0-9]{0,1}$/', $lnk_sffx_num))
            {
              $lnk_errors++; $lnk_sffx_num='0'; $lnk_sffx_err_arr[]=$lnk_nm_yr;
              $errors['lnk_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $lnk_sffx_err_arr)).'**';
            }
            $lnk_nm_yr=$lnk_nm_yr_no_sffx;
          }
          elseif(substr_count($lnk_nm_yr, '--')==1)
          {$lnk_errors++; $lnk_sffx_num='0'; $lnk_hyphn_err_arr[]=$lnk_nm_yr;
          $errors['lnk_hyphn']='</br>**Link playtext suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $lnk_hyphn_err_arr)).'**';}
          else
          {$lnk_sffx_num='0';}

          if($lnk_sffx_num) {$lnk_sffx_rmn=' ('.romannumeral($lnk_sffx_num).')';} else {$lnk_sffx_rmn='';}

          if(substr_count($lnk_nm_yr, '##')>1) {$lnk_errors++; $lnk_hsh_excss_err_arr[]=$lnk_nm_yr; $errors['lnk_hsh_excss']='</br>**You may only use [##] for year written assignment once per link playtext. Please amend: '.html(implode(' / ', $lnk_hsh_excss_err_arr)).'.**';}
          elseif(preg_match('/^\S+.*##.*\S+$/', $lnk_nm_yr))
          {
            list($lnk_nm, $lnk_yr)=explode('##', $lnk_nm_yr);
            $lnk_nm=trim($lnk_nm); $lnk_yr=trim($lnk_yr);

            if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}(\s*;;\s*(c)?(-)?[1-9][0-9]{0,3})?$/', $lnk_yr))
            {
              if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $lnk_yr))
              {
                list($lnk_yr_strtd, $lnk_yr_wrttn)=explode(';;', $lnk_yr);
                $lnk_yr_strtd=trim($lnk_yr_strtd); $lnk_yr_wrttn=trim($lnk_yr_wrttn);

                if(preg_match('/^c(-)?/', $lnk_yr_strtd)) {$lnk_yr_strtd=preg_replace('/^c(.+)$/', '$1', $lnk_yr_strtd); $lnk_yr_strtd_c='1';}
                else {$lnk_yr_strtd_c=NULL;}

                if(preg_match('/^c(-)?/', $lnk_yr_wrttn)) {$lnk_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $lnk_yr_wrttn); $lnk_yr_wrttn_c='1';}
                else {$lnk_yr_wrttn_c=NULL;}

                if($lnk_yr_strtd >= $lnk_yr_wrttn) {$lnk_errors++; $lnk_yr_err_arr[]=$lnk_nm_yr; $errors['lnk_yr']='</br>**Link playtext year started must be earlier than year written. Please amend: '.html(implode(' / ', $lnk_yr_err_arr)).'.**';}
              }
              else
              {
                $lnk_yr_strtd_c=NULL; $lnk_yr_strtd=NULL; $lnk_yr_wrttn=$lnk_yr;
                if(preg_match('/^c(-)?/', $lnk_yr_wrttn)) {$lnk_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $lnk_yr_wrttn); $lnk_yr_wrttn_c='1';}
                else {$lnk_yr_wrttn_c=NULL;}
              }

              if($lnk_yr_strtd)
              {
                if(preg_match('/^-/', $lnk_yr_strtd)) {$lnk_yr_strtd=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $lnk_yr_strtd); if(!preg_match('/^-/', $lnk_yr_wrttn)) {$lnk_yr_strtd .= ' BCE';}}
                $lnk_yr_strtd .= '-';
                if($lnk_yr_strtd_c) {$lnk_yr_strtd='c.'.$lnk_yr_strtd;}
              }
              else {$lnk_yr_strtd='';}

              if(preg_match('/^-/', $lnk_yr_wrttn)) {$lnk_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE", $lnk_yr_wrttn);}
              if($lnk_yr_wrttn_c) {$lnk_yr_wrttn='c.'.$lnk_yr_wrttn;}

              $lnk_nm_yr=$lnk_nm.' ('.$lnk_yr_strtd.$lnk_yr_wrttn.')'; $lnk_url=generateurl($lnk_nm_yr.$lnk_sffx_rmn);
              $lnk_dplct_arr[]=$lnk_url; if(count(array_unique($lnk_dplct_arr))<count($lnk_dplct_arr)) {$errors['lnk_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}
              if(strlen($lnk_nm_yr)>255 || strlen($lnk_url)>255) {$lnk_errors++; $errors['lnk_nm_yr_excss_lngth']='</br>**Link playtext name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
            }
            else {$lnk_errors++; $lnk_yr_frmt_err_arr[]=$lnk_nm_yr; $errors['lnk_yr_frmt']='</br>**Link playtexts must be assigned a valid year (or years). Please amend: '.html(implode(' / ', $lnk_yr_frmt_err_arr)).'.**';}
          }
          else {$lnk_errors++; $lnk_nm=$lnk_nm_yr; $lnk_hsh_err_arr[]=$lnk_nm_yr; $errors['lnk_hsh']='</br>**You must assign a playtext year in the correct format to the following using [##]: '.html(implode(' / ', $lnk_hsh_err_arr)).'.**';}

          if($lnk_errors==0)
          {
            $lnk_nm_yr_cln=cln($lnk_nm_yr); $lnk_sffx_num_cln=cln($lnk_sffx_num); $lnk_url_cln=cln($lnk_url);
            $sql= "SELECT pt_nm, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn
                  FROM pt
                  WHERE NOT EXISTS (SELECT 1 FROM pt WHERE pt_nm_yr='$lnk_nm_yr_cln' AND pt_sffx_num='$lnk_sffx_num_cln')
                  AND pt_url='$lnk_url_cln'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing playtext URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            $row=mysqli_fetch_array($result);
            if(mysqli_num_rows($result)>0)
            {
              if($row['pt_yr_strtd_c']) {$lnk_yr_strtd_c='c';} else {$lnk_yr_strtd_c='';}
              if($row['pt_yr_strtd']) {$lnk_yr_strtd=$row['pt_yr_strtd'].';;';} else {$lnk_yr_strtd='';}
              if($row['pt_yr_wrttn_c']) {$lnk_yr_wrttn_c='c';} else {$lnk_yr_wrttn_c='';}
              if($row['pt_sffx_num']) {$lnk_sffx_num='--'.$row['pt_sffx_num'];} else {$lnk_sffx_num='';}
              $lnk_url_err_arr[]=$row['pt_nm'].'##'.$lnk_yr_strtd_c.$lnk_yr_strtd.$lnk_yr_wrttn_c.$row['pt_yr_wrttn'].$lnk_sffx_num;
              if(count($lnk_url_err_arr)==1) {$errors['lnk_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $lnk_url_err_arr)).'?**';}
              else {$errors['lnk_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $lnk_url_err_arr)).'?**';}
            }
            else
            {
              $sql="SELECT pt_id FROM pt WHERE pt_url='$lnk_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing playtext URL (against link playtext): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)==0)
              {
                $lnk_nonexst_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                $errors['lnk_nonexst']='</br>**The following are not existing playtexts: '.html(implode(' / ', $lnk_nonexst_err_arr)).'.**';
              }
              else
              {
                $lnk_id=$row['pt_id'];
                if($lnk_id==$pt_id) {$errors['lnk_id_mtch']='</br>**You cannot assign this playtext as a link to itself: '.html($lnk_nm_yr.$lnk_sffx_rmn).'.**';}
                else
                {
                  $lnk_id_array[]=$lnk_id;
                  if($coll_wrks)
                  {
                    $sql="SELECT 1 FROM ptwrks WHERE wrks_sg='$lnk_id' AND wrks_ov='$pt_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error acquiring collected works segment details (if link is related to playtext as collected works overview): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)>0)
                    {
                      $lnk_wrks_assoc_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                      $errors['lnk_wrks_assoc']='</br>**Links array cannot comprise collected works segments to which the playtext is related as a collected works overview. Please remove the following: '.html(implode(' / ', $lnk_wrks_assoc_err_arr)).'.**';
                    }

                    $sql= "SELECT 1 FROM ptwrks INNER JOIN ptlnk ON wrks_sg=lnk1 WHERE wrks_ov='$pt_id' AND lnk2='$lnk_id'
                          UNION
                          SELECT 1 FROM ptwrks INNER JOIN ptlnk ON wrks_sg=lnk2 WHERE wrks_ov='$pt_id' AND lnk1='$lnk_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error verifying if playtext collected works segments are associated to entries in the links array: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)>0)
                    {
                      $lnk_wrks_lnk_assoc_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                      $errors['lnk_wrks_lnk_assoc']='</br>**Links array cannot comprise entries which are associated to its collected works segments. Please remove the following: '.html(implode(' / ', $lnk_wrks_lnk_assoc_err_arr)).'.**';
                    }
                  }
                  else
                  {
                    $sql="SELECT 1 FROM ptwrks WHERE wrks_ov='$lnk_id' AND wrks_sg='$pt_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error acquiring collected works overview details (if link is related to playtext as collected works overview): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)>0)
                    {
                      $lnk_wrks_assoc_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                      $errors['lnk_wrks_assoc']='</br>**Links array cannot comprise collected works overviews to which the playtext is related as a collected works segment. Please remove the following: '.html(implode(' / ', $lnk_wrks_assoc_err_arr)).'.**';
                    }

                    $sql= "SELECT 1 FROM ptwrks INNER JOIN ptlnk ON wrks_ov=lnk1 WHERE wrks_sg='$pt_id' AND lnk2='$lnk_id'
                          UNION
                          SELECT 1 FROM ptwrks INNER JOIN ptlnk ON wrks_ov=lnk2 WHERE wrks_sg='$pt_id' AND lnk1='$lnk_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error verifying if playtext collected works overview is associated to entries in the links array: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)>0)
                    {
                      $lnk_wrks_lnk_assoc_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                      $errors['lnk_wrks_lnk_assoc']='</br>**Links array cannot comprise entries which are associated to its collected works overview. Please remove the following: '.html(implode(' / ', $lnk_wrks_lnk_assoc_err_arr)).'.**';
                    }
                  }

                  if($coll_ov)
                  {
                    $sql="SELECT 1 FROM pt WHERE pt_id='$lnk_id' AND coll_ov='$pt_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error acquiring collection segment details (if link is related to playtext as collection overview): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)>0)
                    {
                      $lnk_coll_assoc_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                      $errors['lnk_coll_assoc']='</br>**Links array cannot comprise collection segments to which the playtext is related as a collection overview. Please remove the following: '.html(implode(' / ', $lnk_coll_assoc_err_arr)).'.**';
                    }

                    $sql= "SELECT 1 FROM pt INNER JOIN ptlnk ON pt_id=lnk1 WHERE coll_ov='$pt_id' AND lnk2='$lnk_id'
                          UNION
                          SELECT 1 FROM pt INNER JOIN ptlnk ON pt_id=lnk2 WHERE coll_ov='$pt_id' AND lnk1='$lnk_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error verifying if playtext collection segments are associated to entries in the links array: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)>0)
                    {
                      $lnk_coll_lnk_assoc_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                      $errors['lnk_coll_lnk_assoc']='</br>**Links array cannot comprise entries which are associated to its collection segments. Please remove the following: '.html(implode(' / ', $lnk_coll_lnk_assoc_err_arr)).'.**';
                    }
                  }
                  elseif($coll_sg)
                  {
                    $sql="SELECT 1 FROM pt WHERE coll_ov='$lnk_id' AND pt_id='$pt_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error acquiring collection overview details (if link is related to playtext as collection segment): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)>0)
                    {
                      $lnk_coll_assoc_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                      $errors['lnk_coll_assoc']='</br>**Links array cannot comprise collection overviews to which the playtext is related as a collection segment. Please remove the following: '.html(implode(' / ', $lnk_coll_assoc_err_arr)).'.**';
                    }

                    $sql= "SELECT 1 FROM pt INNER JOIN ptlnk ON coll_ov=lnk1 WHERE pt_id='$pt_id' AND lnk2='$lnk_id'
                          UNION
                          SELECT 1 FROM pt INNER JOIN ptlnk ON coll_ov=lnk2 WHERE pt_id='$pt_id' AND lnk1='$lnk_id'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error verifying if playtext collection overview is associated to entries in the links array: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)>0)
                    {
                      $lnk_coll_lnk_assoc_err_arr[]=$lnk_nm_yr.$lnk_sffx_rmn;
                      $errors['lnk_coll_lnk_assoc']='</br>**Links array cannot comprise entries which are associated to its collection overview. Please remove the following: '.html(implode(' / ', $lnk_coll_lnk_assoc_err_arr)).'.**';
                    }
                  }
                }
              }
            }
          }
        }
      }
      foreach($lnk_id_array as $lnk_id)
      {
        $sql="SELECT pt_nm, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn FROM pt WHERE pt_id='$lnk_id'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring playtext details (for entries that appear in links array): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        if($row['pt_yr_strtd_c']) {$lnk_yr_strtd_c='c';} else {$lnk_yr_strtd_c='';}
        if($row['pt_yr_strtd']) {$lnk_yr_strtd=$row['pt_yr_strtd'].';;';} else {$lnk_yr_strtd='';}
        if($row['pt_yr_wrttn_c']) {$lnk_yr_wrttn_c='c';} else {$lnk_yr_wrttn_c='';}
        if($row['pt_sffx_num']) {$lnk_sffx_num='--'.$row['pt_sffx_num'];} else {$lnk_sffx_num='';}
        $lnk_pt=$row['pt_nm'].'##'.$lnk_yr_strtd_c.$lnk_yr_strtd.$lnk_yr_wrttn_c.$row['pt_yr_wrttn'].$lnk_sffx_num;

        $sql= "SELECT wrks_ov, pt_nm, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn
              FROM ptwrks
              INNER JOIN pt ON wrks_ov=pt_id
              WHERE wrks_sg='$lnk_id'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking links array where both overview and segment parts of a collected works exist: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if(in_array($row['wrks_ov'], $lnk_id_array))
          {
            if($row['pt_yr_strtd_c']) {$lnk_wrks_ov_yr_strtd_c='c';} else {$lnk_wrks_ov_yr_strtd_c='';}
            if($row['pt_yr_strtd']) {$lnk_wrks_ov_yr_strtd=$row['p2_strtd'].';;';} else {$lnk_wrks_ov_yr_strtd='';}
            if($row['pt_yr_wrttn_c']) {$lnk_wrks_ov_yr_wrttn_c='c';} else {$lnk_wrks_ov_yr_wrttn_c='';}
            if($row['pt_sffx_num']) {$lnk_wrks_ov_sffx_num='--'.$row['p2_num'];} else {$lnk_wrks_ov_sffx_num='';}
            $lnk_wrks_ov_sg_err_arr[]=$lnk_pt.' (segment) and '.$row['pt_nm'].'##'.$lnk_wrks_ov_yr_strtd_c.$lnk_wrks_ov_yr_strtd.$lnk_wrks_ov_yr_wrttn_c.$row['pt_yr_wrttn'].$lnk_wrks_ov_sffx_num.' (overview)';
            $errors['lnk_wrks_ov_sg_exst']='</br>**Links array cannot comprise entries that include both overview and segment parts of a collected works. Please remove one part of each pairing of the following: '.html(implode(' / ', $lnk_wrks_ov_sg_err_arr)).'.**';
          }
        }

        $sql= "SELECT p1.coll_ov, p2.pt_nm, p2.pt_sffx_num, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn
              FROM pt p1
              INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
              WHERE p1.pt_id='$lnk_id'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking links array where both overview and segment parts of a collection exist: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        if(in_array($row['coll_ov'], $lnk_id_array))
        {
          if($row['pt_yr_strtd_c']) {$lnk_coll_ov_yr_strtd_c='c';} else {$lnk_coll_ov_yr_strtd_c='';}
          if($row['pt_yr_strtd']) {$lnk_coll_ov_yr_strtd=$row['pt_yr_strtd'].';;';} else {$lnk_coll_ov_yr_strtd='';}
          if($row['pt_yr_wrttn_c']) {$lnk_coll_ov_yr_wrttn_c='c';} else {$lnk_coll_ov_yr_wrttn_c='';}
          if($row['pt_sffx_num']) {$lnk_coll_ov_sffx_num='--'.$row['pt_sffx_num'];} else {$lnk_coll_ov_sffx_num='';}
          $lnk_coll_ov_sg_err_arr[]=$lnk_pt.' (segment) and '.$row['pt_nm'].'##'.$lnk_coll_ov_yr_strtd_c.$lnk_coll_ov_yr_strtd.$lnk_coll_ov_yr_wrttn_c.$row['pt_yr_wrttn'].$lnk_coll_ov_sffx_num.' (overview)';
          $errors['lnk_coll_ov_sg_exst']='</br>**Links array cannot comprise entries that include both overview and segment parts of a collection. Please remove one part of each pairing of the following: '.html(implode(' / ', $lnk_coll_ov_sg_err_arr)).'.**';
        }
      }
    }

    if(preg_match('/\S+/', $cntr_list))
    {
      $cntr_comp_prsn_rls=explode(',,', $_POST['cntr_list']);
      if(count($cntr_comp_prsn_rls)>250)
      {$errors['cntr_rl_array_excss']='**Maximum of 250 writer roles allowed.**';}
      else
      {
        $cntr_cln_excss_err_arr=array(); $cntr_cln_err_arr=array(); $cntr_comp_prsn_empty_err_arr=array();
        $cntr_pipe_excss_err_arr=array(); $cntr_pipe_err_arr=array(); $cntr_prsn_empty_err_arr=array();
        $cntr_comp_tld_excss_err_arr=array(); $cntr_comp_tld_err_arr=array(); $cntr_comp_hyphn_excss_err_arr=array();
        $cntr_comp_hyphn_excss_err_arr=array(); $cntr_comp_sffx_err_arr=array(); $cntr_comp_hyphn_err_arr=array();
        $cntr_comp_url_err_arr=array(); $cntr_prsn_tld_excss_err_arr=array(); $cntr_prsn_tld_err_arr=array();
        $cntr_prsn_hyphn_excss_err_arr=array(); $cntr_prsn_sffx_err_arr=array(); $cntr_prsn_hyphn_err_arr=array();
        $cntr_prsn_smcln_excss_err_arr=array(); $cntr_prsn_smcln_err_arr=array(); $cntr_prsn_nm_err_arr=array();
        $cntr_prsn_url_err_arr=array();
        foreach($cntr_comp_prsn_rls as $cntr_comp_prsn_rl)
        {
          $cntr_comp_prsn_rl=trim($cntr_comp_prsn_rl);

          if(!preg_match('/\S+/', $cntr_comp_prsn_rl))
          {
            $cntr_empty_err_arr[]=$cntr_comp_prsn_rl;
            if(count($cntr_empty_err_arr)==1) {$errors['cntr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['cntr_empty']='</br>**There are '.count($cntr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(preg_match('/\S+/', $cntr_comp_prsn_rl))
            {
              if(substr_count($cntr_comp_prsn_rl, '::')>1)
              {
                $cntr_cln_excss_err_arr[]=$cntr_comp_prsn_rl;
                $errors['cntr_cln_excss']='</br>**You may only use [::] once per contributor-role coupling. Please amend: '.html(implode(' / ', $cntr_cln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*::.*\S+/', $cntr_comp_prsn_rl))
              {
                list($cntr_rl, $cntr_comp_prsn_list)=explode('::', $cntr_comp_prsn_rl);
                $cntr_rl=trim($cntr_rl); $cntr_comp_prsn_list=trim($cntr_comp_prsn_list);

                if(strlen($cntr_rl)>255)
                {$errors['cntr_rl']='</br>**Contributor role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                $cntr_comps_ppl=explode('>>', $cntr_comp_prsn_list);
                $cntr_rl_ttl_array=array(); $cntr_comp_nm_array=array(); $cntr_prsn_nm_array=array();
                foreach($cntr_comps_ppl as $cntr_comp_prsn)
                {
                  $cntr_comp_prsn=trim($cntr_comp_prsn);
                  if(!preg_match('/\S+/', $cntr_comp_prsn))
                  {
                    $cntr_comp_prsn_empty_err_arr[]=$cntr_comp_prsn;
                    if(count($cntr_comp_prsn_empty_err_arr)==1) {$errors['cntr_comp_prsn_empty']='</br>**There is 1 empty entry in a person arrray (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                    else {$errors['cntr_comp_prsn_empty']='</br>**There are '.count($cntr_comp_prsn_empty_err_arr).' empty entries in person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                  }
                  else
                  {
                    if(substr_count($cntr_comp_prsn, '||')>1)
                    {
                      $cntr_prsn_nm_list=''; $cntr_pipe_excss_err_arr[]=$cntr_comp_prsn;
                      $errors['cntr_pipe_excss']='</br>**You may only use [||] once per contributor company-members coupling. Please amend: '.html(implode(' / ', $cntr_pipe_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/\|\|/', $cntr_comp_prsn))
                    {
                      if(preg_match('/\S+.*\|\|(.*\S+)?/', $cntr_comp_prsn))
                      {
                        list($cntr_comp_nm, $cntr_prsn_nm_list)=explode('||', $cntr_comp_prsn);
                        $cntr_comp_nm=trim($cntr_comp_nm); $cntr_prsn_nm_list=trim($cntr_prsn_nm_list);
                        $cntr_comp_nm_array[]=$cntr_comp_nm; $cntr_rl_ttl_array[]=$cntr_comp_nm;
                      }
                      else
                      {
                        $cntr_prsn_nm_list=''; $cntr_pipe_err_arr[]=$cntr_comp_prsn;
                        $errors['cntr_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $cntr_pipe_err_arr)).'.**';
                      }
                    }
                    else
                    {
                      $cntr_prsn_nm_array[]=$cntr_comp_prsn; $cntr_rl_ttl_array[]=$cntr_comp_prsn; $cntr_prsn_nm_list='';
                    }

                    if(preg_match('/\S+/', $cntr_prsn_nm_list))
                    {
                      $cntr_prsn_nms=explode('//', $cntr_prsn_nm_list);
                      foreach($cntr_prsn_nms as $cntr_prsn_nm)
                      {
                        $cntr_prsn_nm=trim($cntr_prsn_nm);
                        if(!preg_match('/\S+/', $cntr_prsn_nm))
                        {
                          $cntr_prsn_empty_err_arr[]=$cntr_prsn_nm;
                          if(count($cntr_prsn_empty_err_arr)==1) {$errors['cntr_prsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                          else {$errors['cntr_prsn_empty']='</br>**There are '.count($cntr_prsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                        }
                        else
                        {$cntr_prsn_nm_array[]=$cntr_prsn_nm; $cntr_rl_ttl_array[]=$cntr_prsn_nm;}
                      }
                    }

                    if(count($cntr_rl_ttl_array)>250)
                    {$errors['cntr_rl_ttl_array_excss']='</br>**Maximum of 250 entries (companies and people per role) allowed.**';}
                  }
                }

                if(count($cntr_comp_nm_array)>0)
                {
                  $cntr_comp_dplct_arr=array();
                  foreach($cntr_comp_nm_array as $cntr_comp_nm)
                  {
                    $cntr_comp_nm=trim($cntr_comp_nm);
                    $cntr_comp_errors=0;
                    if(substr_count($cntr_comp_nm, '~~')>1)
                    {
                      $cntr_comp_errors++; $cntr_comp_tld_excss_err_arr[]=$cntr_comp_nm;
                      $errors['cntr_comp_tld_excss']='</br>**You may only use [~~] once per contributor (company)-role coupling. Please amend: '.html(implode(' / ', $cntr_comp_tld_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/\S+.*~~.*\S+/', $cntr_comp_nm))
                    {
                      list($cntr_comp_rl, $cntr_comp_nm)=explode('~~', $cntr_comp_nm);
                      $cntr_comp_rl=trim($cntr_comp_rl); $cntr_comp_nm=trim($cntr_comp_nm);

                      if(strlen($cntr_comp_rl)>255)
                      {$errors['cntr_comp_rl']='</br>**Contributor (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                    }
                    elseif(substr_count($cntr_comp_nm, '~~')==1)
                    {$cntr_comp_errors++; $cntr_comp_tld_err_arr[]=$cntr_comp_nm;
                    $errors['cntr_comp_tld']='</br>**Contributor (company)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $cntr_comp_tld_err_arr)).'**';}

                    if(substr_count($cntr_comp_nm, '--')>1)
                    {
                      $cntr_comp_errors++; $cntr_comp_sffx_num='0'; $cntr_comp_hyphn_excss_err_arr[]=$cntr_comp_nm;
                      $errors['cntr_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per contributor (company). Please amend: '.html(implode(' / ', $cntr_comp_hyphn_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/^\S+.*--.+$/', $cntr_comp_nm))
                    {
                      list($cntr_comp_nm_no_sffx, $cntr_comp_sffx_num)=explode('--', $cntr_comp_nm);
                      $cntr_comp_nm_no_sffx=trim($cntr_comp_nm_no_sffx); $cntr_comp_sffx_num=trim($cntr_comp_sffx_num);

                      if(!preg_match('/^[1-9][0-9]{0,1}$/', $cntr_comp_sffx_num))
                      {
                        $cntr_comp_errors++; $cntr_comp_sffx_num='0'; $cntr_comp_sffx_err_arr[]=$cntr_comp_nm;
                        $errors['cntr_comp_sffx']='</br>**Contributor (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $cntr_comp_sffx_err_arr)).'**';
                      }
                      $cntr_comp_nm=$cntr_comp_nm_no_sffx;
                    }
                    elseif(substr_count($cntr_comp_nm, '--')==1)
                    {$cntr_comp_errors++; $cntr_comp_sffx_num='0'; $cntr_comp_hyphn_err_arr[]=$cntr_comp_nm;
                    $errors['cntr_comp_hyphn']='</br>**Contributor (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $cntr_comp_hyphn_err_arr)).'**';}
                    else
                    {$cntr_comp_sffx_num='0';}

                    if($cntr_comp_sffx_num) {$cntr_comp_sffx_rmn=' ('.romannumeral($cntr_comp_sffx_num).')';} else {$cntr_comp_sffx_rmn='';}

                    $cntr_comp_url=generateurl($cntr_comp_nm.$cntr_comp_sffx_rmn);

                    $cntr_comp_dplct_arr[]=$cntr_comp_url;
                    if(count(array_unique($cntr_comp_dplct_arr))<count($cntr_comp_dplct_arr))
                    {$errors['cntr_comp_dplct']='</br>**There are entries within a role array that create duplicate company URLs.**';}

                    if(strlen($cntr_comp_nm)>255 || strlen($cntr_comp_url)>255)
                    {$cntr_comp_errors++; $errors['cntr_comp_nm_excss_lngth']='</br>**Contributor (company) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                    if($cntr_comp_errors==0)
                    {
                      $cntr_comp_nm_cln=cln($cntr_comp_nm);
                      $cntr_comp_sffx_num_cln=cln($cntr_comp_sffx_num);
                      $cntr_comp_url_cln=cln($cntr_comp_url);

                      $sql= "SELECT comp_nm, comp_sffx_num
                            FROM comp
                            WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$cntr_comp_nm_cln' AND comp_sffx_num='$cntr_comp_sffx_num_cln')
                            AND comp_url='$cntr_comp_url_cln'";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error checking for existing contributor company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      $row=mysqli_fetch_array($result);
                      if(mysqli_num_rows($result)>0)
                      {
                        if($row['comp_sffx_num']) {$cntr_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                        else {$cntr_comp_url_error_sffx_dsply='';}
                        $cntr_comp_url_err_arr[]=$row['comp_nm'].$cntr_comp_url_error_sffx_dsply;
                        if(count($cntr_comp_url_err_arr)==1)
                        {$errors['cntr_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $cntr_comp_url_err_arr)).'?**';}
                        else
                        {$errors['cntr_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $cntr_comp_url_err_arr)).'?**';}
                      }
                    }
                  }
                }

                if(count($cntr_prsn_nm_array)> 0)
                {
                  $cntr_prsn_dplct_arr=array();
                  foreach($cntr_prsn_nm_array as $cntr_prsn_nm)
                  {
                    $cntr_prsn_nm=trim($cntr_prsn_nm);
                    $cntr_prsn_errors=0;
                    if(substr_count($cntr_prsn_nm, '~~')>1)
                    {
                      $cntr_prsn_errors++; $cntr_prsn_tld_excss_err_arr[]=$cntr_prsn_nm;
                      $errors['cntr_prsn_tld_excss']='</br>**You may only use [~~] once per contributor (person)-role coupling. Please amend: '.html(implode(' / ', $cntr_prsn_tld_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/\S+.*~~.*\S+/', $cntr_prsn_nm))
                    {
                      list($cntr_prsn_rl, $cntr_prsn_nm)=explode('~~', $cntr_prsn_nm);
                      $cntr_prsn_rl=trim($cntr_prsn_rl); $cntr_prsn_nm=trim($cntr_prsn_nm);

                      if(strlen($cntr_prsn_rl)>255)
                      {$errors['cntr_prsn_rl']='</br>**Contributor (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                    }
                    elseif(substr_count($cntr_prsn_nm, '~~')==1)
                    {$cntr_prsn_errors++; $cntr_prsn_tld_err_arr[]=$cntr_prsn_nm;
                    $errors['cntr_prsn_tld']='</br>**Contributor (person)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $cntr_prsn_tld_err_arr)).'**';}

                    if(substr_count($cntr_prsn_nm, '--')>1)
                    {
                      $cntr_prsn_errors++; $cntr_prsn_sffx_num='0'; $cntr_prsn_hyphn_excss_err_arr[]=$cntr_prsn_nm;
                      $errors['cntr_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per contributor (person). Please amend: '.html(implode(' / ', $cntr_prsn_hyphn_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/^\S+.*--.+$/', $cntr_prsn_nm))
                    {
                      list($cntr_prsn_nm_no_sffx, $cntr_prsn_sffx_num)=explode('--', $cntr_prsn_nm);
                      $cntr_prsn_nm_no_sffx=trim($cntr_prsn_nm_no_sffx); $cntr_prsn_sffx_num=trim($cntr_prsn_sffx_num);

                      if(!preg_match('/^[1-9][0-9]{0,1}$/', $cntr_prsn_sffx_num))
                      {
                        $cntr_prsn_errors++; $cntr_prsn_sffx_num='0'; $cntr_prsn_sffx_err_arr[]=$cntr_prsn_nm;
                        $errors['cntr_prsn_sffx']='</br>**Contributor (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $cntr_prsn_sffx_err_arr)).'**';
                      }
                      $cntr_prsn_nm=$cntr_prsn_nm_no_sffx;
                    }
                    elseif(substr_count($cntr_prsn_nm, '--')==1)
                    {$cntr_prsn_errors++; $cntr_prsn_sffx_num='0'; $cntr_prsn_hyphn_err_arr[]=$cntr_prsn_nm;
                    $errors['cntr_prsn_hyphn']='</br>**Contributor (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $cntr_prsn_hyphn_err_arr)).'**';}
                    else
                    {$cntr_prsn_sffx_num='0';}

                    if($cntr_prsn_sffx_num) {$cntr_prsn_sffx_rmn=' ('.romannumeral($cntr_prsn_sffx_num).')';} else {$cntr_prsn_sffx_rmn='';}

                    if(substr_count($cntr_prsn_nm, ';;')>1)
                    {
                      $cntr_prsn_errors++; $cntr_prsn_smcln_excss_err_arr[]=$cntr_prsn_nm;
                      $errors['cntr_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $cntr_prsn_smcln_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/\S+.*;;(.*\S+)?/', $cntr_prsn_nm))
                    {
                      list($cntr_prsn_frst_nm, $cntr_prsn_lst_nm)=explode(';;', $cntr_prsn_nm);
                      $cntr_prsn_frst_nm=trim($cntr_prsn_frst_nm); $cntr_prsn_lst_nm=trim($cntr_prsn_lst_nm);

                      if(preg_match('/\S+/', $cntr_prsn_lst_nm))
                      {$cntr_prsn_lst_nm_dsply=' '.$cntr_prsn_lst_nm;}
                      else
                      {$cntr_prsn_lst_nm_dsply='';}

                      $cntr_prsn_fll_nm=$cntr_prsn_frst_nm.$cntr_prsn_lst_nm_dsply;
                      $cntr_prsn_url=generateurl($cntr_prsn_fll_nm.$cntr_prsn_sffx_rmn);

                      $cntr_prsn_dplct_arr[]=$cntr_prsn_url;
                      if(count(array_unique($cntr_prsn_dplct_arr))<count($cntr_prsn_dplct_arr))
                      {$errors['cntr_prsn_dplct']='</br>**There are entries within a role array that create duplicate person URLs.**';}

                      if(strlen($cntr_prsn_fll_nm)>255 || strlen($cntr_prsn_url)>255)
                      {$cntr_prsn_errors++; $errors['cntr_prsn_excss_lngth']='</br>**Contributor (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                    }
                    else
                    {
                      $cntr_prsn_errors++; $cntr_prsn_smcln_err_arr[]=$cntr_prsn_nm;
                      $errors['cntr_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $cntr_prsn_smcln_err_arr)).'.**';
                    }

                    if($cntr_prsn_errors==0)
                    {
                      $cntr_prsn_frst_nm_cln=cln($cntr_prsn_frst_nm);
                      $cntr_prsn_lst_nm_cln=cln($cntr_prsn_lst_nm);
                      $cntr_prsn_fll_nm_cln=cln($cntr_prsn_fll_nm);
                      $cntr_prsn_sffx_num_cln=cln($cntr_prsn_sffx_num);
                      $cntr_prsn_url_cln=cln($cntr_prsn_url);

                      $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                            FROM prsn
                            WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$cntr_prsn_frst_nm_cln' AND prsn_lst_nm='$cntr_prsn_lst_nm_cln')
                            AND prsn_fll_nm='$cntr_prsn_fll_nm_cln' AND prsn_sffx_num='$cntr_prsn_sffx_num_cln'";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error checking for contributor person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      $row=mysqli_fetch_array($result);
                      if(mysqli_num_rows($result)>0)
                      {
                        if($row['prsn_sffx_num']) {$cntr_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                        else {$cntr_prsn_nm_error_sffx_dsply='';}
                        $cntr_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$cntr_prsn_nm_error_sffx_dsply;
                        if(count($cntr_prsn_nm_err_arr)==1)
                        {$errors['cntr_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $cntr_prsn_nm_err_arr)).'.**';}
                        else
                        {$errors['cntr_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $cntr_prsn_nm_err_arr)).'.**';}
                      }

                      $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                            FROM prsn
                            WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$cntr_prsn_fll_nm_cln' AND prsn_sffx_num='$cntr_prsn_sffx_num_cln')
                            AND prsn_url='$cntr_prsn_url_cln'";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error checking for existing contributor person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      $row=mysqli_fetch_array($result);
                      if(mysqli_num_rows($result)>0)
                      {
                        if($row['prsn_sffx_num']) {$cntr_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                        else {$cntr_prsn_url_error_sffx_dsply='';}
                        $cntr_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$cntr_prsn_url_error_sffx_dsply;
                        if(count($cntr_prsn_url_err_arr)==1)
                        {$errors['cntr_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $cntr_prsn_url_err_arr)).'?**';}
                        else
                        {$errors['cntr_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $cntr_prsn_url_err_arr)).'?**';}
                      }
                    }
                  }
                }
              }
              else
              {
                $cntr_cln_err_arr[]=$cntr_comp_prsn_rl;
                $errors['cntr_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $cntr_cln_err_arr)).'.**';
              }
            }
          }
        }
      }
    }

    if($coll_wrks && ($cst_m || $cst_f || $cst_non_spc || $cst_addt))
    {$errors['cst_coll_wrks_checked']='**These fields must be empty if collected works button is applied.**';}

    if($cst_m) {if(!preg_match('/^[0-9][0-9]{0,1}$/', $cst_m)) {$errors['cst_m']='</br>**Male cast field must be comprised of numbers only or left empty.**';}}
    else {$cst_m='0';}

    if($cst_f) {if(!preg_match('/^[0-9][0-9]{0,1}$/', $cst_f)) {$errors['cst_f']='</br>**Female cast field must be comprised of numbers only or left empty.**';}}
    else {$cst_f='0';}

    if($cst_non_spc) {if(!preg_match('/^[0-9][0-9]{0,1}$/', $cst_non_spc)) {$errors['cst_non_spc']='</br>**Non-specific sex cast field must be comprised of numbers only or left empty.**';}}
    else {$cst_non_spc='0';}

    $cst_ttl=$cst_m+$cst_f+$cst_non_spc;

    if(preg_match('/\S+/', $cst_nt))
    {
      if($coll_wrks) {$errors['cst_nt_coll_wrks_checked']='**This field must be empty if collected works button is applied.**';}
      elseif(strlen($cst_nt)>255) {$errors['cst_nt_excss_lngth']='</br>**Cast notes are allowed a maximum of 255 characters.**';}
    }

    if(preg_match('/\S+/', $char_list))
    {
      if($coll_wrks) {$errors['char_coll_wrks_checked']='**This field must be empty if collected works button is applied.**';}
      else
      {
        $char_grp_nms=explode('@@', $_POST['char_list']);
        if(count($char_grp_nms)>250) {$errors['char_grp_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $char_grp_nm_empty_err_arr=array(); $char_eql_excss_err_arr=array(); $char_eql_err_arr=array();
          $char_grp_err_arr=array(); $char_empty_err_arr=array(); $char_cln_excss_err_arr=array();
          $char_cln_err_arr=array(); $char_hyphn_excss_err_arr=array(); $char_sffx_err_arr=array();
          $char_hyphn_err_arr=array(); $char_dplct_arr=array(); $char_url_err_arr=array();
          foreach($char_grp_nms as $char_grp_nm)
          {
            $char_grp_nm = trim($char_grp_nm);
            if(!preg_match('/\S+/', $char_grp_nm))
            {
              $char_grp_nm_empty_err_arr[]=$char_grp_nm;
              if(count($char_grp_nm_empty_err_arr)==1) {$errors['char_grp_pt_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
              else {$errors['char_grp_pt_empty']='</br>**There are '.count($char_grp_nm_empty_err_arr).' empty entries in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($char_grp_nm, '==')>1)
              {
                $char_nm_list='0'; $char_eql_excss_err_arr[]=$char_grp_nm;
                $errors['char_eql_excss']='</br>**You may only use [==] for subheader assignment once per character array. Please amend: '.html(implode(' / ', $char_eql_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*==.*\S$/', $char_grp_nm))
              {
                list($coll_grp, $char_nm_list)=explode('==', $char_grp_nm);
                $coll_grp=trim($coll_grp); $char_nm_list=trim($char_nm_list);
                if(strlen($coll_grp)>255) {$errors['coll_grp_excss_lngth']='</br>**Character subheaders are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
              }
              elseif(substr_count($char_grp_nm, '==')==1)
              {$char_nm_list='0'; $char_eql_err_arr[]=$char_grp_nm;
              $errors['char_eql']='</br>**Character subheader assignation must use [==] in the correct format. Please amend: '.html(implode(' / ', $char_eql_err_arr)).'**';}
              else
              {
                if(count($char_grp_nms)>1) {$char_grp_err_arr[]=$char_grp_nm; $errors['char_grp']='</br>**If more than one subdivision is created, subheaders must be assigned to each. Please amend: '.html(implode(' / ', $char_grp_err_arr)).'**';}
                $char_nm_list=$char_grp_nm;
              }

              if($char_nm_list)
              {
                $char_nms=explode(',,', $char_nm_list);
                if(count($char_nms)>250) {$errors['char_nm_array_excss']='**Maximum of 250 entries allowed.**';}
                else
                {
                  foreach($char_nms as $char_nm)
                  {
                    $char_errors=0;
                    $char_nm=trim($char_nm);
                    if(!preg_match('/\S+/', $char_nm))
                    {
                      $char_empty_err_arr[]=$char_nm;
                      if(count($char_empty_err_arr)==1) {$errors['char_empty']='</br>**There is 1 empty entry in the character arrays (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                      else {$errors['char_empty']='</br>**There are '.count($char_empty_err_arr).' empty entries in the character arrays (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      if(substr_count($char_nm, '::')>1)
                      {
                        $char_errors++; $char_cln_excss_err_arr[]=$char_nm;
                        $errors['char_cln_excss']='</br>**You may only use [::] once per character-note coupling. Please amend: '.html(implode(' / ', $char_cln_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*::.*\S+/', $char_nm))
                      {
                        list($char_nm, $char_nt)=explode('::', $char_nm);
                        $char_nm=trim($char_nm); $char_nt=trim($char_nt);

                        if(strlen($char_nt)>255)
                        {$errors['char_nt_excss_lngth']='</br>**Character note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($char_nm, '::')==1)
                      {$char_errors++; $char_cln_err_arr[]=$char_nm;
                      $errors['char_cln']='</br>**Character note assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $char_cln_err_arr)).'**';}

                      if(substr_count($char_nm, '--')>1)
                      {
                        $char_errors++; $char_sffx_num='0'; $char_hyphn_excss_err_arr[]=$char_nm;
                        $errors['char_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per character. Please amend: '.html(implode(' / ', $char_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $char_nm))
                      {
                        list($char_nm_no_sffx, $char_sffx_num)=explode('--', $char_nm);
                        $char_nm_no_sffx=trim($char_nm_no_sffx); $char_sffx_num=trim($char_sffx_num);
                        $char_sffx_rmn=' ('.romannumeral($char_sffx_num).')';

                        if(!preg_match('/^[1-9][0-9]{0,5}$/', $char_sffx_num))
                        {
                          $char_errors++; $char_sffx_num='0'; $char_sffx_err_arr[]=$char_nm;
                          $errors['char_sffx']='</br>**The suffix must be a positive integer (between 1 and 999,999 (with no leading 0)). Please amend: '.html(implode(' / ', $char_sffx_err_arr)).'**';
                        }
                        $char_nm=$char_nm_no_sffx;
                      }
                      elseif(substr_count($char_nm, '--')==1)
                      {$char_errors++; $char_sffx_num='0'; $char_hyphn_err_arr[]=$char_nm;
                      $errors['char_hyphn']='</br>**Character suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $char_hyphn_err_arr)).'**';}
                      else
                      {$char_sffx_num='0';}

                      if($char_sffx_num) {$char_sffx_rmn=' ('.romannumeral($char_sffx_num).')';} else {$char_sffx_rmn='';}

                      $char_url=generateurl($char_nm.$char_sffx_rmn);

                      $char_dplct_arr[]=$char_url;
                      if(count(array_unique($char_dplct_arr))<count($char_dplct_arr))
                      {$errors['char_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

                      if(strlen($char_nm)>255 || strlen($char_url)>255)
                      {$char_errors++; $errors['char_nm_excss_lngth']='</br>**Character name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                      if($char_errors==0)
                      {
                        $char_nm_cln=cln($char_nm);
                        $char_sffx_num_cln=cln($char_sffx_num);
                        $char_url_cln=cln($char_url);

                        $sql= "SELECT char_nm, char_sffx_num
                              FROM role
                              WHERE NOT EXISTS (SELECT 1 FROM role WHERE char_nm='$char_nm_cln' AND char_sffx_num='$char_sffx_num_cln')
                              AND char_url='$char_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing character URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['char_sffx_num']) {$char_sffx_num='--'.$row['char_sffx_num'];}
                          else {$char_sffx_num='';}
                          $char_url_err_arr[]=$row['char_nm'].$char_sffx_num;
                          if(count($char_url_err_arr)==1)
                          {$errors['char_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $char_url_err_arr)).'?**';}
                          else
                          {$errors['char_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $char_url_err_arr)).'?**';}
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

    if(preg_match('/\S+/', $lcnsr_list))
    {
      if($coll_wrks) {$errors['lcnsr_coll_wrks_checked']='**This field must be empty if collected works button is applied.**';}
      else
      {
        $lcnsr_comp_prsns=explode(',,', $_POST['lcnsr_list']);

        $lcnsr_ttl_array=array(); $lcnsr_comp_nm_rl_array=array(); $lcnsr_prsn_nm_rl_array=array(); $lcnsr_empty_err_arr=array();
        $lcnsr_pipe_excss_err_arr=array(); $lcnsr_pipe_err_arr=array(); $lcnsr_comp_cln_excss_err_arr=array();
        $lcnsr_comp_dplct_arr=array(); $lcnsr_comp_cln_err_arr=array(); $lcnsr_comp_hyphn_excss_err_arr=array();
        $lcnsr_comp_sffx_err_arr=array(); $lcnsr_comp_hyphn_err_arr=array(); $lcnsr_comp_url_err_arr=array();
        $lcnsr_comp_nonexst_err_arr=array(); $lcnsr_prsn_empty_err_arr=array(); $lcnsr_prsn_cln_err_arr=array();
        $lcnsr_prsn_cln_excss_err_arr=array(); $lcnsr_prsn_sffx_err_arr=array(); $lcnsr_prsn_hyphn_err_arr=array();
        $lcnsr_prsn_hyphn_excss_err_arr=array(); $lcnsr_prsn_dplct_arr=array(); $lcnsr_prsn_smcln_err_arr=array();
        $lcnsr_prsn_smcln_excss_err_arr=array(); $lcnsr_prsn_nm_err_arr=array(); $lcnsr_prsn_url_err_arr=array();
        $lcnsr_prsn_nonexst_err_arr=array(); $agncy_lcnsr_no_assoc_err_arr=array(); $lcnsr_err_arr=array();
        foreach($lcnsr_comp_prsns as $lcnsr_comp_prsn)
        {
          $lcnsr_comp_prsn=trim($lcnsr_comp_prsn);
          if(!preg_match('/\S+/', $lcnsr_comp_prsn))
          {
            $lcnsr_empty_err_arr[]=$lcnsr_comp_prsn; $lcnsr_err_arr[]='1';
            if(count($lcnsr_empty_err_arr)==1) {$errors['lcnsr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['lcnsr_empty']='</br>**There are '.count($lcnsr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($lcnsr_comp_prsn, '||')>1)
            {
              $lcnsr_prsn_nm_rl_list='';
              $lcnsr_pipe_excss_err_arr[]=$lcnsr_comp_prsn; $lcnsr_err_arr[]='1';
              $errors['lcnsr_pipe_excss']='</br>**You may only use [||] once per company-member(s) coupling. Please amend: '.html(implode(' / ', $lcnsr_pipe_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\|\|/', $lcnsr_comp_prsn))
            {
              if(preg_match('/\S+.*\|\|(.*\S+)?/', $lcnsr_comp_prsn))
              {
                list($lcnsr_comp_nm_rl, $lcnsr_prsn_nm_rl_list)=explode('||', $lcnsr_comp_prsn);
                $lcnsr_comp_nm_rl=trim($lcnsr_comp_nm_rl); $lcnsr_prsn_nm_rl_list=trim($lcnsr_prsn_nm_rl_list);
                $lcnsr_comp_nm_rl_array[]=$lcnsr_comp_nm_rl; $lcnsr_ttl_array[]=$lcnsr_comp_nm_rl;
              }
              else
              {
                $lcnsr_pipe_err_arr[]=$lcnsr_comp_prsn; $lcnsr_err_arr[]='1';
                $lcnsr_prsn_nm_rl_list='';
                $errors['lcnsr_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $lcnsr_pipe_err_arr)).'.**';
              }
            }
            else
            {
              $lcnsr_prsn_nm_rl_array[]=$lcnsr_comp_prsn; $lcnsr_ttl_array[]=$lcnsr_comp_prsn; $lcnsr_prsn_nm_rl_list='';
            }

            if(preg_match('/\S+/', $lcnsr_prsn_nm_rl_list))
            {
              $lcnsr_prsn_nm_rls=explode('//', $lcnsr_prsn_nm_rl_list);
              foreach($lcnsr_prsn_nm_rls as $lcnsr_prsn_nm_rl)
              {
                $lcnsr_prsn_nm_rl=trim($lcnsr_prsn_nm_rl);
                if(!preg_match('/\S+/', $lcnsr_prsn_nm_rl))
                {
                  $lcnsr_prsn_empty_err_arr[]=$lcnsr_prsn_nm_rl;
                  if(count($lcnsr_prsn_empty_err_arr)==1) {$errors['lcnsr_prsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                  else {$errors['lcnsr_prsn_empty']='</br>**There are '.count($lcnsr_prsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                }
                {$lcnsr_prsn_nm_rl_array[]=$lcnsr_prsn_nm_rl; $lcnsr_ttl_array[]=$lcnsr_prsn_nm_rl;}
              }
            }
          }
        }

        if(count($lcnsr_ttl_array)>250)
        {$errors['lcnsr_array_excss']='**Maximum of 250 entries (companies and people collectively) allowed.**';}
        else
        {
          if(count($lcnsr_comp_nm_rl_array)>0)
          {
            foreach($lcnsr_comp_nm_rl_array as $lcnsr_comp_nm_rl)
            {
              $lcnsr_comp_errors=0;
              if(substr_count($lcnsr_comp_nm_rl, '::')>1)
              {
                $lcnsr_comp_errors++; $lcnsr_comp_nm=trim($lcnsr_comp_nm_rl);
                $lcnsr_comp_cln_excss_err_arr[]=$lcnsr_comp_nm_rl;
                $errors['lcnsr_comp_cln_excss']='</br>**You may only use [::] once per licensor-role coupling. Please amend: '.html(implode(' / ', $lcnsr_comp_cln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*::.*\S+/', $lcnsr_comp_nm_rl))
              {
                list($lcnsr_comp_nm, $lcnsr_comp_rl)=explode('::', $lcnsr_comp_nm_rl);
                $lcnsr_comp_nm=trim($lcnsr_comp_nm); $lcnsr_comp_rl=trim($lcnsr_comp_rl);

                if(strlen($lcnsr_comp_rl)>255)
                {$errors['lcnsr_comp_rl_excss_lngth']='</br>**Licensor (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
              }
              else
              {
                $lcnsr_comp_errors++; $lcnsr_comp_nm=trim($lcnsr_comp_nm_rl);
                $lcnsr_comp_cln_err_arr[]=$lcnsr_comp_nm_rl;
                $errors['lcnsr_comp_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $lcnsr_comp_cln_err_arr)).'.**';
              }

              if(substr_count($lcnsr_comp_nm, '--')>1)
              {
                $lcnsr_comp_errors++; $lcnsr_comp_sffx_num='0'; $lcnsr_comp_hyphn_excss_err_arr[]=$lcnsr_comp_nm;
                $errors['lcnsr_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per licensor (company). Please amend: '.html(implode(' / ', $lcnsr_comp_hyphn_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*--.+$/', $lcnsr_comp_nm))
              {
                list($lcnsr_comp_nm_no_sffx, $lcnsr_comp_sffx_num)=explode('--', $lcnsr_comp_nm);
                $lcnsr_comp_nm_no_sffx=trim($lcnsr_comp_nm_no_sffx); $lcnsr_comp_sffx_num=trim($lcnsr_comp_sffx_num);

                if(!preg_match('/^[1-9][0-9]{0,1}$/', $lcnsr_comp_sffx_num))
                {
                  $lcnsr_comp_errors++;  $lcnsr_comp_sffx_num='0'; $lcnsr_comp_sffx_err_arr[]=$lcnsr_comp_nm;
                  $errors['lcnsr_comp_sffx']='</br>**Licensor (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $lcnsr_comp_sffx_err_arr)).'**';
                }
                $lcnsr_comp_nm=$lcnsr_comp_nm_no_sffx;
              }
              elseif(substr_count($lcnsr_comp_nm, '--')==1)
              {$lcnsr_comp_errors++; $lcnsr_comp_sffx_num='0'; $lcnsr_comp_hyphn_err_arr[]=$lcnsr_comp_nm;
              $errors['lcnsr_comp_hyphn']='</br>**Licensor (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $lcnsr_comp_hyphn_err_arr)).'**';}
              else
              {$lcnsr_comp_sffx_num='0';}

              if($lcnsr_comp_sffx_num) {$lcnsr_comp_sffx_rmn=' ('.romannumeral($lcnsr_comp_sffx_num).')';} else {$lcnsr_comp_sffx_rmn='';}

              $lcnsr_comp_url=generateurl($lcnsr_comp_nm.$lcnsr_comp_sffx_rmn);

              $lcnsr_comp_dplct_arr[]=$lcnsr_comp_url;
              if(count(array_unique($lcnsr_comp_dplct_arr))<count($lcnsr_comp_dplct_arr))
              {$errors['lcnsr_comp_dplct']='</br>**There are entries within the array that create duplicate company URLs.**';}

              if(strlen($lcnsr_comp_nm)>255 || strlen($lcnsr_comp_url)>255)
              {$lcnsr_comp_errors++; $errors['lcnsr_comp_nm_excss_lngth']='</br>**Licensor (company) name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              if($lcnsr_comp_errors==0)
              {
                $lcnsr_comp_nm_cln=cln($lcnsr_comp_nm);
                $lcnsr_comp_sffx_num_cln=cln($lcnsr_comp_sffx_num);
                $lcnsr_comp_url_cln=cln($lcnsr_comp_url);

                $sql= "SELECT comp_nm, comp_sffx_num
                      FROM comp
                      WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$lcnsr_comp_nm_cln' AND comp_sffx_num='$lcnsr_comp_sffx_num_cln')
                      AND comp_url='$lcnsr_comp_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing licensor company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  $lcnsr_comp_errors++;
                  if($row['comp_sffx_num']) {$lcnsr_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                  else {$lcnsr_comp_url_error_sffx_dsply='';}
                  $lcnsr_comp_url_err_arr[]=$row['comp_nm'].$lcnsr_comp_url_error_sffx_dsply;
                  if(count($lcnsr_comp_url_err_arr)==1)
                  {$errors['lcnsr_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $lcnsr_comp_url_err_arr)).'?**';}
                  else
                  {$errors['lcnsr_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $lcnsr_comp_url_err_arr)).'?**';}
                }

                if($lcnsr_comp_errors==0)
                {
                  $sql= "SELECT comp_id
                        FROM comp
                        WHERE comp_url='$lcnsr_comp_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing company URL (against licensor company URL): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  $lcnsr_comp_id=$row['comp_id'];
                  if(mysqli_num_rows($result)==0)
                  {
                    $lcnsr_comp_errors++;
                    $lcnsr_comp_nonexst_err_arr[]=$lcnsr_comp_nm.$lcnsr_comp_sffx_rmn;
                    if(count($lcnsr_comp_nonexst_err_arr)==1)
                    {$errors['lcnsr_comp_nonexst']='</br>**The following is not an existing company: '.html(implode(' / ', $lcnsr_comp_nonexst_err_arr)).'.**';}
                    else
                    {$errors['lcnsr_comp_nonexst']='</br>**The following are not existing companies: '.html(implode(' / ', $lcnsr_comp_nonexst_err_arr)).'.**';}
                  }
                }
              }
              if($lcnsr_comp_errors>0) {$lcnsr_err_arr[]='1';}
            }
          }

          if(count($lcnsr_prsn_nm_rl_array)>0)
          {
            foreach($lcnsr_prsn_nm_rl_array as $lcnsr_prsn_nm_rl)
            {
              $lcnsr_prsn_errors=0;
              if(substr_count($lcnsr_prsn_nm_rl, '::')>1)
              {
                $lcnsr_prsn_errors++; $lcnsr_prsn_nm=trim($lcnsr_prsn_nm_rl);
                $lcnsr_prsn_cln_excss_err_arr[]=$lcnsr_prsn_nm_rl;
                $errors['lcnsr_prsn_cln_excss']='</br>**You may only use [::] once per licensor-role coupling. Please amend: '.html(implode(' / ', $lcnsr_prsn_cln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*::.*\S+/', $lcnsr_prsn_nm_rl))
              {
                list($lcnsr_prsn_nm, $lcnsr_prsn_rl)=explode('::', $lcnsr_prsn_nm_rl);
                $lcnsr_prsn_nm=trim($lcnsr_prsn_nm); $lcnsr_prsn_rl=trim($lcnsr_prsn_rl);

                if(strlen($lcnsr_prsn_rl)>255)
                {$errors['lcnsr_prsn_rl_excss_lngth']='</br>**Licensor (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
              }
              else
              {
                $lcnsr_prsn_errors++; $lcnsr_prsn_nm=trim($lcnsr_prsn_nm_rl);
                $lcnsr_prsn_cln_err_arr[]=$lcnsr_prsn_nm_rl;
                $errors['lcnsr_prsn_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $lcnsr_prsn_cln_err_arr)).'.**';
              }

              if(substr_count($lcnsr_prsn_nm, '--')>1)
              {
                $lcnsr_prsn_errors++; $lcnsr_prsn_sffx_num='0'; $lcnsr_prsn_hyphn_excss_err_arr[]=$lcnsr_prsn_nm;
                $errors['lcnsr_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per licensor (person). Please amend: '.html(implode(' / ', $lcnsr_prsn_hyphn_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*--.+$/', $lcnsr_prsn_nm))
              {
                list($lcnsr_prsn_nm_no_sffx, $lcnsr_prsn_sffx_num)=explode('--', $lcnsr_prsn_nm);
                $lcnsr_prsn_nm_no_sffx=trim($lcnsr_prsn_nm_no_sffx); $lcnsr_prsn_sffx_num=trim($lcnsr_prsn_sffx_num);

                if(!preg_match('/^[1-9][0-9]{0,1}$/', $lcnsr_prsn_sffx_num))
                {
                  $lcnsr_prsn_errors++; $lcnsr_prsn_sffx_num='0'; $lcnsr_prsn_sffx_err_arr[]=$lcnsr_prsn_nm;
                  $errors['lcnsr_prsn_sffx']='</br>**Licensor (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $lcnsr_prsn_sffx_err_arr)).'**';
                }
                $lcnsr_prsn_nm=$lcnsr_prsn_nm_no_sffx;
              }
              elseif(substr_count($lcnsr_prsn_nm, '--')==1)
              {$lcnsr_prsn_errors++; $lcnsr_prsn_sffx_num='0'; $lcnsr_prsn_hyphn_err_arr[]=$lcnsr_prsn_nm;
              $errors['lcnsr_prsn_hyphn']='</br>**Licensor (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $lcnsr_prsn_hyphn_err_arr)).'**';}
              else
              {$lcnsr_prsn_sffx_num='0';}

              if($lcnsr_prsn_sffx_num) {$lcnsr_prsn_sffx_rmn=' ('.romannumeral($lcnsr_prsn_sffx_num).')';} else {$lcnsr_prsn_sffx_rmn='';}

              if(substr_count($lcnsr_prsn_nm, ';;')>1)
              {
                $lcnsr_prsn_errors++; $lcnsr_prsn_smcln_excss_err_arr[]=$lcnsr_prsn_nm;
                $errors['lcnsr_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $lcnsr_prsn_smcln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*;;(.*\S+)?/', $lcnsr_prsn_nm))
              {
                list($lcnsr_prsn_frst_nm, $lcnsr_prsn_lst_nm)=explode(';;', $lcnsr_prsn_nm);
                $lcnsr_prsn_frst_nm=trim($lcnsr_prsn_frst_nm); $lcnsr_prsn_lst_nm=trim($lcnsr_prsn_lst_nm);

                if(preg_match('/\S+/', $lcnsr_prsn_lst_nm))
                {$lcnsr_prsn_lst_nm_dsply=' '.$lcnsr_prsn_lst_nm;}
                else
                {$lcnsr_prsn_lst_nm_dsply='';}

                $lcnsr_prsn_fll_nm=$lcnsr_prsn_frst_nm.$lcnsr_prsn_lst_nm_dsply;
                $lcnsr_prsn_url=generateurl($lcnsr_prsn_fll_nm.$lcnsr_prsn_sffx_rmn);

                $lcnsr_prsn_dplct_arr[]=$lcnsr_prsn_url;
                if(count(array_unique($lcnsr_prsn_dplct_arr))<count($lcnsr_prsn_dplct_arr))
                {$errors['lcnsr_prsn_dplct']='</br>**There are entries within the array that create duplicate person URLs.**';}

                if(strlen($lcnsr_prsn_fll_nm)>255 || strlen($lcnsr_prsn_url)>255)
                {$lcnsr_prsn_errors++; $errors['lcnsr_prsn_excss_lngth']='</br>**Licensor (person) full name and its URL are allowed a maximum of 255 characters respectively. Please amend entries that exceed this amount.**';}
              }
              else
              {
                $lcnsr_prsn_errors++; $lcnsr_prsn_smcln_err_arr[]=$lcnsr_prsn_nm;
                $errors['lcnsr_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $lcnsr_prsn_smcln_err_arr)).'.**';
              }

              if($lcnsr_prsn_errors==0)
              {
                $lcnsr_prsn_frst_nm_cln=cln($lcnsr_prsn_frst_nm);
                $lcnsr_prsn_lst_nm_cln=cln($lcnsr_prsn_lst_nm);
                $lcnsr_prsn_fll_nm_cln=cln($lcnsr_prsn_fll_nm);
                $lcnsr_prsn_sffx_num_cln=cln($lcnsr_prsn_sffx_num);
                $lcnsr_prsn_url_cln=cln($lcnsr_prsn_url);

                $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                      FROM prsn
                      WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$lcnsr_prsn_frst_nm_cln' AND prsn_lst_nm='$lcnsr_prsn_lst_nm_cln')
                      AND prsn_fll_nm='$lcnsr_prsn_fll_nm_cln' AND prsn_sffx_num='$lcnsr_prsn_sffx_num_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for licensor person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  $lcnsr_prsn_errors++;
                  if($row['prsn_sffx_num']) {$lcnsr_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                  else {$lcnsr_prsn_nm_error_sffx_dsply='';}
                  $lcnsr_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$lcnsr_prsn_nm_error_sffx_dsply;
                  if(count($lcnsr_prsn_nm_err_arr)==1)
                  {$errors['lcnsr_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $lcnsr_prsn_nm_err_arr)).'.**';}
                  else
                  {$errors['lcnsr_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $lcnsr_prsn_nm_err_arr)).'.**';}
                }

                $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                      FROM prsn
                      WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$lcnsr_prsn_fll_nm_cln' AND prsn_sffx_num='$lcnsr_prsn_sffx_num_cln')
                      AND prsn_url='$lcnsr_prsn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing licensor person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  $lcnsr_prsn_errors++;
                  if($row['prsn_sffx_num']) {$lcnsr_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                  else {$lcnsr_prsn_url_error_sffx_dsply='';}
                  $lcnsr_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$lcnsr_prsn_url_error_sffx_dsply;
                  if(count($lcnsr_prsn_url_err_arr)==1)
                  {$errors['lcnsr_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $lcnsr_prsn_url_err_arr)).'?**';}
                  else
                  {$errors['lcnsr_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $lcnsr_prsn_url_err_arr)).'?**';}
                }

                if($lcnsr_prsn_errors==0)
                {
                  $sql= "SELECT prsn_id
                        FROM prsn
                        WHERE prsn_url='$lcnsr_prsn_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing person URL (against licensor person URL): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  $lcnsr_prsn_id=$row['prsn_id'];
                  if(mysqli_num_rows($result)==0)
                  {
                    $lcnsr_prsn_errors++;
                    $lcnsr_prsn_nonexst_err_arr[]=$lcnsr_prsn_fll_nm.$lcnsr_prsn_sffx_rmn;
                    if(count($lcnsr_prsn_nonexst_err_arr)==1)
                    {$errors['lcnsr_prsn_nonexst']='</br>**The following is not an existing person: '.html(implode(' / ', $lcnsr_prsn_nonexst_err_arr)).'.**';}
                    else {$errors['lcnsr_prsn_nonexst']='</br>**The following are not existing people : '.html(implode(' / ', $lcnsr_prsn_nonexst_err_arr)).'.**';}
                  }
                }
              }
            }
            if($lcnsr_prsn_errors>0) {$lcnsr_err_arr[]='1';}
          }
        }

        if(count($lcnsr_err_arr)==0 && preg_match('/\S+/', $_POST['lcnsr_list']))
        {
          $lcnsr_comp_prsns=explode(',,', $_POST['lcnsr_list']);
          foreach($lcnsr_comp_prsns as $lcnsr_comp_prsn)
          {
            if(preg_match('/\|\|/', $lcnsr_comp_prsn))
            {
              list($lcnsr_comp_nm_rl, $lcnsr_prsn_nm_rl_list)=explode('||', $lcnsr_comp_prsn);
              $lcnsr_comp_nm_rl=trim($lcnsr_comp_nm_rl); $lcnsr_prsn_nm_rl_list=trim($lcnsr_prsn_nm_rl_list);
            }
            else
            {$lcnsr_comp_nm_rl=''; $lcnsr_prsn_nm_rl_list='';}

            if(preg_match('/\S+/', $lcnsr_comp_nm_rl))
            {
              list($lcnsr_comp_nm, $lcnsr_comp_rl)=explode('::', $lcnsr_comp_nm_rl);
              $lcnsr_comp_nm=trim($lcnsr_comp_nm); $lcnsr_comp_rl=trim($lcnsr_comp_rl);

              if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $lcnsr_comp_nm))
              {
                list($lcnsr_comp_nm, $lcnsr_comp_sffx_num)=explode('--', $lcnsr_comp_nm);
                $lcnsr_comp_nm=trim($lcnsr_comp_nm); $lcnsr_comp_sffx_num=trim($lcnsr_comp_sffx_num);
                $lcnsr_comp_sffx_rmn=' ('.romannumeral($lcnsr_comp_sffx_num).')';
              }
              else
              {$lcnsr_comp_sffx_num='0'; $lcnsr_comp_sffx_rmn='';}

              $lcnsr_comp_url_cln=cln(generateurl($lcnsr_comp_nm.$lcnsr_comp_sffx_rmn));

              $sql= "SELECT comp_id
                    FROM comp
                    WHERE comp_url='$lcnsr_comp_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing company URL (against licensor company URL): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              $lcnsr_comp_id=$row['comp_id'];
            }

            if(preg_match('/\S+/', $lcnsr_prsn_nm_rl_list))
            {
              $lcnsr_prsn_nm_rls=explode('//', $lcnsr_prsn_nm_rl_list);
              foreach($lcnsr_prsn_nm_rls as $lcnsr_prsn_nm_rl)
              {
                list($lcnsr_prsn_nm, $lcnsr_prsn_rl)=explode('::', $lcnsr_prsn_nm_rl);
                $lcnsr_prsn_nm=trim($lcnsr_prsn_nm); $lcnsr_prsn_rl=trim($lcnsr_prsn_rl);

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $lcnsr_prsn_nm))
                {
                  list($lcnsr_prsn_nm, $lcnsr_prsn_sffx_num)=explode('--', $lcnsr_prsn_nm);
                  $lcnsr_prsn_nm=trim($lcnsr_prsn_nm); $lcnsr_prsn_sffx_num=trim($lcnsr_prsn_sffx_num);
                  $lcnsr_prsn_sffx_rmn=' ('.romannumeral($lcnsr_prsn_sffx_num).')';
                }
                else
                {$lcnsr_prsn_sffx_num='0'; $lcnsr_prsn_sffx_rmn='';}

                list($lcnsr_prsn_frst_nm, $lcnsr_prsn_lst_nm)=explode(';;', $lcnsr_prsn_nm);
                $lcnsr_prsn_frst_nm=trim($lcnsr_prsn_frst_nm); $lcnsr_prsn_lst_nm=trim($lcnsr_prsn_lst_nm);

                if(preg_match('/\S+/', $lcnsr_prsn_lst_nm)) {$lcnsr_prsn_lst_nm_dsply=' '.$lcnsr_prsn_lst_nm;}
                else {$lcnsr_prsn_lst_nm_dsply='';}

                $lcnsr_prsn_fll_nm=$lcnsr_prsn_frst_nm.$lcnsr_prsn_lst_nm_dsply;
                $lcnsr_prsn_url_cln=cln(generateurl($lcnsr_prsn_fll_nm.$lcnsr_prsn_sffx_rmn));

                $sql= "SELECT 1
                      FROM compprsn
                      WHERE compid='$lcnsr_comp_id'
                      AND prsnid=(SELECT prsn_id FROM prsn WHERE prsn_url='$lcnsr_prsn_url_cln')";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing company-person relationship (for given licensors): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $comp_lcnsr_no_assoc_err_arr[]=$lcnsr_comp_nm.$lcnsr_comp_sffx_rmn.' - '.$lcnsr_prsn_fll_nm.$lcnsr_prsn_sffx_rmn;
                  if(count($comp_lcnsr_no_assoc_err_arr)==1)
                  {$errors['comp_lcnsr_no_assoc']='</br>**The following does not yet exist as a company-member relationship: '.html(implode(' / ', $comp_lcnsr_no_assoc_err_arr)).'.**';}
                  else {$errors['comp_lcnsr_no_assoc']='</br>**The following do not yet exist as company-member relationships: '.html(implode(' / ', $comp_lcnsr_no_assoc_err_arr)).'.**';}
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

      $pt_id=cln($_POST['pt_id']);
      $sql= "SELECT pt_nm_yr, pt_sffx_num
            FROM pt
            WHERE pt_id='$pt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring playtext details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['pt_sffx_num']) {$pt_sffx_rmn=' ('.romannumeral($row['pt_sffx_num']).')';} else {$pt_sffx_rmn='';}
      $pagetab='Edit: '.html($row['pt_nm_yr'].$pt_sffx_rmn);
      $pagetitle=html($row['pt_nm_yr'].$pt_sffx_rmn);
      $pt_nm=$_POST['pt_nm'];
      $pt_sbnm=$_POST['pt_sbnm'];
      $txt_vrsn_list=$_POST['txt_vrsn_list'];
      $pt_yr_strtd=$_POST['pt_yr_strtd'];
      $pt_yr_wrttn=$_POST['pt_yr_wrttn'];
      $pt_sffx_num=$_POST['pt_sffx_num'];
      $pt_pub_dt=$_POST['pt_pub_dt'];
      $pt_wrks_sg_list=$_POST['pt_wrks_sg_list'];
      $pt_coll_sg_list=$_POST['pt_coll_sg_list'];
      $pt_lnk_list=$_POST['pt_lnk_list'];
      if($pt_coll=='2') {$coll_dsply=' [COLLECTED WORKS]';} elseif($pt_coll=='3') {$coll_dsply=' [COLLECTION]';}
      elseif($pt_coll=='4') {$coll_dsply=' [PART OF COLLECTION]';} else {$coll_dsply='';}
      $wri_list=$_POST['wri_list'];
      $cntr_list=$_POST['cntr_list'];
      $mat_list=$_POST['mat_list'];
      $ctgry_list=$_POST['ctgry_list'];
      $gnr_list=$_POST['gnr_list'];
      $ftr_list=$_POST['ftr_list'];
      $thm_list=$_POST['thm_list'];
      $sttng_list=$_POST['sttng_list'];
      $cst_m=$_POST['cst_m'];
      $cst_f=$_POST['cst_f'];
      $cst_non_spc=$_POST['cst_non_spc'];
      $cst_nt=$_POST['cst_nt'];
      $char_list=$_POST['char_list'];
      $lcnsr_list=$_POST['lcnsr_list'];
      $alt_nm_list=$_POST['alt_nm_list'];
      $textarea=$_POST['textarea'];
      $errors['pt_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $pt_id=html($pt_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE pt SET
            pt_nm_yr='$pt_nm_yr',
            pt_nm='$pt_nm',
            pt_alph=CASE WHEN '$pt_alph'!='' THEN '$pt_alph' END,
            pt_yr_strtd_c=CASE WHEN '$pt_yr_strtd_num'!='' THEN '$pt_yr_strtd_c' END,
            pt_yr_strtd=CASE WHEN '$pt_yr_strtd_num'!='' THEN '$pt_yr_strtd_num' END,
            pt_yr_wrttn_c='$pt_yr_wrttn_c',
            pt_yr_wrttn='$pt_yr_wrttn_num',
            pt_sffx_num='$pt_sffx_num',
            pt_url='$pt_url',
            pt_sbnm='$pt_sbnm',
            pt_pub_dt=CASE WHEN '$pt_pub_dt'!='' THEN '$pt_pub_dt' END,
            pt_pub_dt_frmt=CASE WHEN '$pt_pub_dt'!='' THEN '$pt_pub_dt_frmt' END,
            pt_coll='$pt_coll',
            cst_m='$cst_m',
            cst_f='$cst_f',
            cst_non_spc='$cst_non_spc',
            cst_ttl='$cst_ttl',
            cst_addt='$cst_addt',
            cst_nt='$cst_nt'
            WHERE pt_id='$pt_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating playtext info for submitted playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM pttxt_vrsn WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-text version associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $txt_vrsn_list))
      {
        $txt_vrsn_nms=explode(',,', $txt_vrsn_list);
        $n=0;
        foreach($txt_vrsn_nms as $txt_vrsn_nm)
        {
          $txt_vrsn_nm=trim($txt_vrsn_nm);
          $txt_vrsn_url=generateurl($txt_vrsn_nm);
          $txt_vrsn_ordr=++$n;

          $sql="SELECT 1 FROM txt_vrsn WHERE txt_vrsn_url='$txt_vrsn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of text version: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql="INSERT INTO txt_vrsn(txt_vrsn_nm, txt_vrsn_url) VALUES('$txt_vrsn_nm', '$txt_vrsn_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding text version data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql="INSERT INTO pttxt_vrsn(ptid, txt_vrsn_ordr, txt_vrsnid) SELECT '$pt_id', '$txt_vrsn_ordr', txt_vrsn_id FROM txt_vrsn WHERE txt_vrsn_url='$txt_vrsn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding playtext-text version association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptwrks WHERE wrks_ov='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext (collected works overview)-collected works segment associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptwrks_sbhdr WHERE wrks_ov='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext (collected works overview)-collected works subheader associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $pt_wrks_sg_list))
      {
        $wrks_sg_sbhdr_pts=explode('@@', $pt_wrks_sg_list); $m=0;
        foreach($wrks_sg_sbhdr_pts as $wrks_sg_sbhdr_pt)
        {
          $wrks_sg_sbhdr_pt=trim($wrks_sg_sbhdr_pt); $wrks_sbhdr_id=++$m;
          if(preg_match('/^\S+.*==.*\S$/', $wrks_sg_sbhdr_pt))
          {
            list($wrks_sbhdr, $pt_wrks_sg_pt_list)=explode('==', $wrks_sg_sbhdr_pt);
            $wrks_sbhdr=trim($wrks_sbhdr); $pt_wrks_sg_pt_list=trim($pt_wrks_sg_pt_list);

            $sql="INSERT INTO ptwrks_sbhdr(wrks_ov, wrks_sbhdr_id, wrks_sbhdr) VALUES('$pt_id', '$wrks_sbhdr_id', '$wrks_sbhdr')";
            if(!mysqli_query($link, $sql)) {$error='Error adding playtext-collected works subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
          else {$pt_wrks_sg_pt_list=$wrks_sg_sbhdr_pt; $wrks_sbhdr_id=NULL;}

          $wrks_sg_nm_yrs=explode(',,', $pt_wrks_sg_pt_list); $n=0;
          foreach($wrks_sg_nm_yrs as $wrks_sg_nm_yr)
          {
            if(preg_match('/\S+.*::.*\S+/', $wrks_sg_nm_yr)) {list($wrks_sg_nm_yr, $wrks_sg_rl)=explode('::', $wrks_sg_nm_yr); $wrks_sg_nm_yr=trim($wrks_sg_nm_yr); $wrks_sg_rl=trim($wrks_sg_rl);}
            else {$wrks_sg_nm_yr=trim($wrks_sg_nm_yr); $wrks_sg_rl=NULL;}

            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $wrks_sg_nm_yr)) {list($wrks_sg_nm_yr, $wrks_sg_sffx_num)=explode('--', $wrks_sg_nm_yr); $wrks_sg_nm_yr=trim($wrks_sg_nm_yr); $wrks_sg_sffx_num=trim($wrks_sg_sffx_num); $wrks_sg_sffx_rmn=' ('.romannumeral($wrks_sg_sffx_num).')';}
            else {$wrks_sg_sffx_num='0'; $wrks_sg_sffx_rmn='';}

            list($wrks_sg_nm, $wrks_sg_yr)=explode('##', $wrks_sg_nm_yr); $wrks_sg_nm=trim($wrks_sg_nm); $wrks_sg_yr=trim($wrks_sg_yr);

            if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $wrks_sg_yr))
            {
              list($wrks_sg_yr_strtd, $wrks_sg_yr_wrttn)=explode(';;', $wrks_sg_yr); $wrks_sg_yr_strtd=trim($wrks_sg_yr_strtd); $wrks_sg_yr_wrttn=trim($wrks_sg_yr_wrttn);

              if(preg_match('/^c(-)?/', $wrks_sg_yr_strtd)) {$wrks_sg_yr_strtd=preg_replace('/^c(.+)$/', '$1', $wrks_sg_yr_strtd); $wrks_sg_yr_strtd_c='1';}
              else {$wrks_sg_yr_strtd_c='0';}

              if(preg_match('/^c(-)?/', $wrks_sg_yr_wrttn)) {$wrks_sg_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $wrks_sg_yr_wrttn); $wrks_sg_yr_wrttn_c='1';}
              else {$wrks_sg_yr_wrttn_c='0';}
            }
            else
            {
              $wrks_sg_yr_strtd_c='0'; $wrks_sg_yr_strtd='0'; $wrks_sg_yr_wrttn=$wrks_sg_yr;
              if(preg_match('/^c(-)?/', $wrks_sg_yr_wrttn)) {$wrks_sg_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $wrks_sg_yr_wrttn); $wrks_sg_yr_wrttn_c='1';}
              else {$wrks_sg_yr_wrttn_c='0';}
            }

            if($wrks_sg_yr_strtd)
            {
              if(preg_match('/^-/', $wrks_sg_yr_strtd))
              {
                $wrks_sg_yr_strtd_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $wrks_sg_yr_strtd);
                if(!preg_match('/^-/', $wrks_sg_yr_wrttn)) {$wrks_sg_yr_strtd_dsply .= ' BCE';}
                $wrks_sg_yr_strtd_dsply .= '-';
                if($wrks_sg_yr_strtd_c) {$wrks_sg_yr_strtd_dsply='c.'.$wrks_sg_yr_strtd_dsply;}
              }
              else {$wrks_sg_yr_strtd_dsply=$wrks_sg_yr_strtd.'-';}
            }
            else {$wrks_sg_yr_strtd_dsply='';}

            if(preg_match('/^-/', $wrks_sg_yr_wrttn)) {$wrks_sg_yr_wrttn_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE)", $wrks_sg_yr_wrttn);}
            else {$wrks_sg_yr_wrttn_dsply=$wrks_sg_yr_wrttn.')';}

            if($wrks_sg_yr_wrttn_c) {$wrks_sg_yr_wrttn_dsply='c.'.$wrks_sg_yr_wrttn_dsply;}

            $wrks_ordr=++$n;
            $wrks_sg_nm_yr=$wrks_sg_nm.' ('.$wrks_sg_yr_strtd_dsply.$wrks_sg_yr_wrttn_dsply;
            $wrks_sg_url=generateurl($wrks_sg_nm_yr.$wrks_sg_sffx_rmn);
            $wrks_sg_alph=alph($wrks_sg_nm);

            $sql ="SELECT 1 FROM pt WHERE pt_url='$wrks_sg_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existence of collected works segment (playtext): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $sql= "INSERT INTO pt(pt_url, pt_nm, pt_alph, pt_nm_yr, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn)
                    VALUES('$wrks_sg_url', '$wrks_sg_nm', CASE WHEN '$wrks_sg_alph'!='' THEN '$wrks_sg_alph' END, '$wrks_sg_nm_yr', '$wrks_sg_sffx_num', '$wrks_sg_yr_strtd_c', '$wrks_sg_yr_strtd', '$wrks_sg_yr_wrttn_c', '$wrks_sg_yr_wrttn')";
              if(!mysqli_query($link, $sql)) {$error='Error adding collected works segment (playtext) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }

            $sql= "INSERT INTO ptwrks(wrks_ov, wrks_sg_rl, wrks_ordr, wrks_sbhdrid, wrks_sg)
                  SELECT '$pt_id', '$wrks_sg_rl', '$wrks_ordr', CASE WHEN '$wrks_sbhdr_id'!='' THEN '$wrks_sbhdr_id' END, pt_id FROM pt WHERE pt_url='$wrks_sg_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding playtext-collected works segment association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      $sql="UPDATE pt SET coll_ov=NULL, coll_sbhdrid=NULL, coll_ordr=NULL WHERE coll_ov='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error nullifying playtext (collection overview)-collection segment associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptcoll_sbhdr WHERE coll_ov='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext (collection overview)-collection subheader associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $pt_coll_sg_list))
      {
        $coll_sg_sbhdr_pts=explode('@@', $pt_coll_sg_list); $m=0;
        foreach($coll_sg_sbhdr_pts as $coll_sg_sbhdr_pt)
        {
          $coll_sg_sbhdr_pt=trim($coll_sg_sbhdr_pt); $coll_sbhdr_id=++$m;
          if(preg_match('/^\S+.*==.*\S$/', $coll_sg_sbhdr_pt))
          {
            list($coll_sbhdr, $pt_coll_sg_pt_list)=explode('==', $coll_sg_sbhdr_pt);
            $coll_sbhdr=trim($coll_sbhdr); $pt_coll_sg_pt_list=trim($pt_coll_sg_pt_list);

            $sql="INSERT INTO ptcoll_sbhdr(coll_ov, coll_sbhdr_id, coll_sbhdr) VALUES('$pt_id', '$coll_sbhdr_id', '$coll_sbhdr')";
            if(!mysqli_query($link, $sql)) {$error='Error adding playtext-collection subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
          else {$pt_coll_sg_pt_list=$coll_sg_sbhdr_pt; $coll_sbhdr_id=NULL;}

          $coll_sg_nm_yrs=explode(',,', $pt_coll_sg_pt_list); $n=0;
          foreach($coll_sg_nm_yrs as $coll_sg_nm_yr)
          {
            $coll_sg_nm_yr=trim($coll_sg_nm_yr);
            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $coll_sg_nm_yr)) {list($coll_sg_nm_yr, $coll_sg_sffx_num)=explode('--', $coll_sg_nm_yr); $coll_sg_nm_yr=trim($coll_sg_nm_yr); $coll_sg_sffx_num=trim($coll_sg_sffx_num); $coll_sg_sffx_rmn=' ('.romannumeral($coll_sg_sffx_num).')';}
            else {$coll_sg_sffx_num='0'; $coll_sg_sffx_rmn='';}

            list($coll_sg_nm, $coll_sg_yr)=explode('##', $coll_sg_nm_yr); $coll_sg_nm=trim($coll_sg_nm); $coll_sg_yr=trim($coll_sg_yr);

            if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $coll_sg_yr))
            {
              list($coll_sg_yr_strtd, $coll_sg_yr_wrttn)=explode(';;', $coll_sg_yr); $coll_sg_yr_strtd=trim($coll_sg_yr_strtd); $coll_sg_yr_wrttn=trim($coll_sg_yr_wrttn);

              if(preg_match('/^c(-)?/', $coll_sg_yr_strtd)) {$coll_sg_yr_strtd=preg_replace('/^c(.+)$/', '$1', $coll_sg_yr_strtd); $coll_sg_yr_strtd_c='1';}
              else {$coll_sg_yr_strtd_c='0';}

              if(preg_match('/^c(-)?/', $coll_sg_yr_wrttn)) {$coll_sg_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $coll_sg_yr_wrttn); $coll_sg_yr_wrttn_c='1';}
              else {$coll_sg_yr_wrttn_c='0';}
            }
            else
            {
              $coll_sg_yr_strtd_c='0'; $coll_sg_yr_strtd='0'; $coll_sg_yr_wrttn=$coll_sg_yr;
              if(preg_match('/^c(-)?/', $coll_sg_yr_wrttn)) {$coll_sg_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $coll_sg_yr_wrttn); $coll_sg_yr_wrttn_c='1';}
              else {$coll_sg_yr_wrttn_c='0';}
            }

            if($coll_sg_yr_strtd)
            {
              if(preg_match('/^-/', $coll_sg_yr_strtd))
              {
                $coll_sg_yr_strtd_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $coll_sg_yr_strtd);
                if(!preg_match('/^-/', $coll_sg_yr_wrttn)) {$coll_sg_yr_strtd_dsply .= ' BCE';}
                $coll_sg_yr_strtd_dsply .= '-';
                if($coll_sg_yr_strtd_c) {$coll_sg_yr_strtd_dsply='c.'.$coll_sg_yr_strtd_dsply;}
              }
              else {$coll_sg_yr_strtd_dsply=$coll_sg_yr_strtd.'-';}
            }
            else {$coll_sg_yr_strtd_dsply='';}

            if(preg_match('/^-/', $coll_sg_yr_wrttn)) {$coll_sg_yr_wrttn_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE)", $coll_sg_yr_wrttn);}
            else {$coll_sg_yr_wrttn_dsply=$coll_sg_yr_wrttn.')';}

            if($coll_sg_yr_wrttn_c) {$coll_sg_yr_wrttn_dsply='c.'.$coll_sg_yr_wrttn_dsply;}

            $coll_ordr=++$n;
            $coll_sg_nm_yr=$coll_sg_nm.' ('.$coll_sg_yr_strtd_dsply.$coll_sg_yr_wrttn_dsply;
            $coll_sg_url=generateurl($coll_sg_nm_yr.$coll_sg_sffx_rmn);
            $coll_sg_alph=alph($coll_sg_nm);

            $sql="SELECT 1 FROM pt WHERE pt_url='$coll_sg_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existence of collection segment (playtext): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $sql= "INSERT INTO pt(pt_url, pt_nm, pt_alph, pt_nm_yr, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_coll)
                    VALUES('$coll_sg_url', '$coll_sg_nm', CASE WHEN '$coll_sg_alph'!='' THEN '$coll_sg_alph' END, '$coll_sg_nm_yr', '$coll_sg_sffx_num', '$coll_sg_yr_strtd_c', '$coll_sg_yr_strtd', '$coll_sg_yr_wrttn_c', '$coll_sg_yr_wrttn', 4)";
              if(!mysqli_query($link, $sql)) {$error='Error adding collection segment (playtext) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }

            $sql= "UPDATE pt AS p1, (SELECT pt_id FROM pt WHERE pt_url='$coll_sg_url') AS p2 SET
                  p1.coll_ov='$pt_id',
                  p1.coll_ordr='$coll_ordr',
                  p1.coll_sbhdrid=CASE WHEN '$coll_sbhdr_id'!='' THEN '$coll_sbhdr_id' END
                  WHERE p1.pt_id=p2.pt_id";
            if(!mysqli_query($link, $sql)) {$error='Error updating playtext-collection segment association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      $sql="DELETE FROM ptlnk WHERE lnk1='$pt_id' OR lnk2='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-link associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $pt_lnk_list))
      {
        $lnk_nm_yrs=explode(',,', $pt_lnk_list);
        $n=0;
        foreach($lnk_nm_yrs as $lnk_nm_yr)
        {
          $lnk_nm_yr=trim($lnk_nm_yr);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $lnk_nm_yr)) {list($lnk_nm_yr, $lnk_sffx_num)=explode('--', $lnk_nm_yr); $lnk_nm_yr=trim($lnk_nm_yr); $lnk_sffx_num=trim($lnk_sffx_num); $lnk_sffx_rmn=' ('.romannumeral($lnk_sffx_num).')';}
          else {$lnk_sffx_num='0'; $lnk_sffx_rmn='';}

          list($lnk_nm, $lnk_yr)=explode('##', $lnk_nm_yr); $lnk_nm=trim($lnk_nm); $lnk_yr=trim($lnk_yr);

          if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $lnk_yr))
          {
            list($lnk_yr_strtd, $lnk_yr_wrttn)=explode(';;', $lnk_yr); $lnk_yr_strtd=trim($lnk_yr_strtd); $lnk_yr_wrttn=trim($lnk_yr_wrttn);

            if(preg_match('/^c(-)?/', $lnk_yr_strtd)) {$lnk_yr_strtd=preg_replace('/^c(.+)$/', '$1', $lnk_yr_strtd); $lnk_yr_strtd_c='1';}
            else {$lnk_yr_strtd_c='0';}

            if(preg_match('/^c(-)?/', $lnk_yr_wrttn)) {$lnk_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $lnk_yr_wrttn); $lnk_yr_wrttn_c='1';}
            else {$lnk_yr_wrttn_c='0';}
          }
          else
          {
            $lnk_yr_strtd_c='0'; $lnk_yr_strtd='0'; $lnk_yr_wrttn=$lnk_yr;
            if(preg_match('/^c(-)?/', $lnk_yr_wrttn)) {$lnk_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $lnk_yr_wrttn); $lnk_yr_wrttn_c='1';}
            else {$lnk_yr_wrttn_c='0';}
          }

          if($lnk_yr_strtd)
          {
            if(preg_match('/^-/', $lnk_yr_strtd))
            {
              $lnk_yr_strtd_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $lnk_yr_strtd);
              if(!preg_match('/^-/', $lnk_yr_wrttn)) {$lnk_yr_strtd_dsply .= ' BCE';}
              $lnk_yr_strtd_dsply .= '-';
              if($lnk_yr_strtd_c) {$lnk_yr_strtd_dsply='c.'.$lnk_yr_strtd_dsply;}
            }
            else {$lnk_yr_strtd_dsply=$lnk_yr_strtd.'-';}
          }
          else {$lnk_yr_strtd_dsply='';}

          if(preg_match('/^-/', $lnk_yr_wrttn)) {$lnk_yr_wrttn_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE)", $lnk_yr_wrttn);}
          else {$lnk_yr_wrttn_dsply=$lnk_yr_wrttn.')';}

          if($lnk_yr_wrttn_c) {$lnk_yr_wrttn_dsply='c.'.$lnk_yr_wrttn_dsply;}

          $lnk_nm_yr=$lnk_nm.' ('.$lnk_yr_strtd_dsply.$lnk_yr_wrttn_dsply;
          $lnk_url=generateurl($lnk_nm_yr.$lnk_sffx_rmn);

          $sql= "INSERT INTO ptlnk(lnk1, lnk2)
                SELECT '$pt_id', pt_id FROM pt WHERE pt_url='$lnk_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding playtext link association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptwrirl WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-writer (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptwri WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-writer (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptsrc_mat WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-writer (source material) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $wri_list))
      {
        $wri_comp_prsn_rls=explode(',,', $wri_list);
        $m=0;
        foreach($wri_comp_prsn_rls as $wri_comp_prsn_rl)
        {
          $wri_rl_id=++$m;

          if(preg_match('/\S+.*\+\+(.*\S+)?/', $wri_comp_prsn_rl))
          {
            list($src_mats_rl, $wri_comp_prsn_rl)=explode('++', $wri_comp_prsn_rl);
            $src_mats_rl=trim($src_mats_rl); $wri_comp_prsn_rl=trim($wri_comp_prsn_rl);

            list($src_mat_rl, $src_mat_list)=explode('::', $src_mats_rl);
            $src_mat_rl=trim($src_mat_rl); $src_mat_list=trim($src_mat_list);

            $n=0;
            $src_mats=explode('>>', $src_mat_list);
            foreach($src_mats as $src_mat_nm_frmt)
            {
              if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $src_mat_nm_frmt))
              {
                list($src_mat_nm_frmt, $src_mat_sffx_num)=explode('--', $src_mat_nm_frmt);
                $src_mat_nm_frmt=trim($src_mat_nm_frmt); $src_mat_sffx_num=trim($src_mat_sffx_num);
                $src_mat_sffx_rmn=' ('.romannumeral($src_mat_sffx_num).')';
              }
              else
              {$src_mat_sffx_num='0'; $src_mat_sffx_rmn='';}

              list($src_mat_nm, $src_frmt_nm)=explode(';;', $src_mat_nm_frmt);
              $src_mat_nm=trim($src_mat_nm); $src_frmt_nm=trim($src_frmt_nm);

              $src_mat_ordr=++$n;
              $src_mat_url=generateurl($src_mat_nm.$src_mat_sffx_rmn);
              $src_frmt_url=generateurl($src_frmt_nm);
              $src_mat_alph=alph($src_mat_nm);

              $sql="SELECT 1 FROM frmt WHERE frmt_url='$src_frmt_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existence of format: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              if(mysqli_num_rows($result)==0)
              {
                $sql= "INSERT INTO frmt(frmt_nm, frmt_url)
                      VALUES('$src_frmt_nm', '$src_frmt_url')";
                if(!mysqli_query($link, $sql)) {$error='Error adding (source material) format data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              $sql="SELECT 1 FROM mat WHERE mat_url='$src_mat_url' AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$src_frmt_url')";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existence of source material-format combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              if(mysqli_num_rows($result)==0)
              {
                $sql= "INSERT INTO mat(mat_nm, mat_alph, mat_sffx_num, mat_url, frmtid)
                      SELECT '$src_mat_nm', CASE WHEN '$src_mat_alph'!='' THEN '$src_mat_alph' END, '$src_mat_sffx_num', '$src_mat_url', frmt_id FROM frmt WHERE frmt_url='$src_frmt_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding source material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              $sql= "INSERT INTO ptsrc_mat(ptid, wri_rlid, src_mat_ordr, src_matid)
                    SELECT $pt_id, $wri_rl_id, '$src_mat_ordr', mat_id FROM mat WHERE mat_url='$src_mat_url' AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$src_frmt_url')";
              if(!mysqli_query($link, $sql)) {$error='Error adding playtext-source material association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }
          }
          else {$src_mat_rl='';}

          if(preg_match('/\S+/', $wri_comp_prsn_rl))
          {
            list($wri_rl, $wri_comp_prsn_list)=explode('::', $wri_comp_prsn_rl);
            $wri_rl=trim($wri_rl); $wri_comp_prsn_list=trim($wri_comp_prsn_list);

            $o=0;
            $wri_comps_ppl=explode('>>', $wri_comp_prsn_list);
            foreach($wri_comps_ppl as $wri_comp_prsn)
            {
              if(preg_match('/\|\|/', $wri_comp_prsn))
              {
                list($wri_comp_nm, $wri_prsn_nm_list)=explode('||', $wri_comp_prsn);
                $wri_comp_nm=trim($wri_comp_nm); $wri_prsn_nm_list=trim($wri_prsn_nm_list); $wri_prsn_nm2='';

                if(preg_match('/^\S+.*\*\*\*$/', $wri_comp_nm))
                {$wri_comp_nm=preg_replace('/(\S+.*)(\*\*\*)/', '$1', $wri_comp_nm); $grntr='1'; $src_wri='0'; $org_wri='0'; $wri_comp_nm=trim($wri_comp_nm);}
                elseif(preg_match('/^\S+.*\*\*$/', $wri_comp_nm))
                {$wri_comp_nm=preg_replace('/(\S+.*)(\*\*)/', '$1', $wri_comp_nm); $grntr='0'; $src_wri='1'; $org_wri='0'; $wri_comp_nm=trim($wri_comp_nm);}
                elseif(preg_match('/^\S+.*\*$/', $wri_comp_nm))
                {$wri_comp_nm=preg_replace('/(\S+.*)(\*)/', '$1', $wri_comp_nm); $grntr='0'; $src_wri='0'; $org_wri='1'; $wri_comp_nm=trim($wri_comp_nm);}
                else {$grntr='0'; $src_wri='0'; $org_wri='0';}
              }
              else
              {
                if(preg_match('/^\S+.*\*\*\*$/', $wri_comp_prsn))
                {$wri_comp_prsn=preg_replace('/(\S+.*)(\*\*\*)/', '$1', $wri_comp_prsn); $grntr='1'; $src_wri='0'; $org_wri='0'; $wri_comp_prsn=trim($wri_comp_prsn);}
                elseif(preg_match('/^\S+.*\*\*$/', $wri_comp_prsn))
                {$wri_comp_prsn=preg_replace('/(\S+.*)(\*\*)/', '$1', $wri_comp_prsn); $grntr='0'; $src_wri='1'; $org_wri='0'; $wri_comp_prsn=trim($wri_comp_prsn);}
                elseif(preg_match('/^\S+.*\*$/', $wri_comp_prsn))
                {$wri_comp_prsn=preg_replace('/(\S+.*)(\*)/', '$1', $wri_comp_prsn); $grntr='0'; $src_wri='0'; $org_wri='1'; $wri_comp_prsn=trim($wri_comp_prsn);}
                else {$grntr='0'; $src_wri='0'; $org_wri='0';}

                $wri_comp_nm=''; $wri_prsn_nm_list=''; $wri_prsn_nm2=trim($wri_comp_prsn);
              }

              if(preg_match('/\S+/', $wri_comp_nm))
              {
                if(preg_match('/\S+.*~~.*\S+/', $wri_comp_nm))
                {
                  list($wri_comp_rl, $wri_comp_nm)=explode('~~', $wri_comp_nm);
                  $wri_comp_rl=trim($wri_comp_rl); $wri_comp_nm=trim($wri_comp_nm);
                }
                else {$wri_comp_rl='';}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $wri_comp_nm))
                {
                  list($wri_comp_nm, $wri_comp_sffx_num)=explode('--', $wri_comp_nm);
                  $wri_comp_nm=trim($wri_comp_nm); $wri_comp_sffx_num=trim($wri_comp_sffx_num);
                  $wri_comp_sffx_rmn=' ('.romannumeral($wri_comp_sffx_num).')';
                }
                else
                {$wri_comp_sffx_num='0'; $wri_comp_sffx_rmn='';}

                $wri_ordr=++$o;
                $wri_comp_url=generateurl($wri_comp_nm.$wri_comp_sffx_rmn);
                $wri_comp_alph=alph($wri_comp_nm);

                $sql="SELECT 1 FROM comp WHERE comp_url='$wri_comp_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of writer (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                        VALUES('$wri_comp_nm', CASE WHEN '$wri_comp_alph'!='' THEN '$wri_comp_alph' END, '$wri_comp_sffx_num', '$wri_comp_url', 1, 0, 0)";
                  if(!mysqli_query($link, $sql)) {$error='Error adding writer (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO ptwri(ptid, wri_rlid, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, wri_prsnid, wri_compid)
                      SELECT $pt_id, $wri_rl_id, '$wri_comp_rl', $wri_ordr, $org_wri, $src_wri, $grntr, '0', comp_id FROM comp WHERE comp_url='$wri_comp_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding playtext-writer (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              if(preg_match('/\S+/', $wri_prsn_nm_list))
              {
                $wri_prsn_nms=explode('//', $wri_prsn_nm_list);
                foreach($wri_prsn_nms as $wri_prsn_nm)
                {
                  $wri_prsn_nm=trim($wri_prsn_nm);
                  if(preg_match('/\S+.*~~.*\S+/', $wri_prsn_nm))
                  {
                    list($wri_prsn_rl, $wri_prsn_nm)=explode('~~', $wri_prsn_nm);
                    $wri_prsn_rl=trim($wri_prsn_rl); $wri_prsn_nm=trim($wri_prsn_nm);
                  }
                  else {$wri_prsn_rl='';}

                  if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $wri_prsn_nm))
                  {
                    list($wri_prsn_nm, $wri_prsn_sffx_num)=explode('--', $wri_prsn_nm);
                    $wri_prsn_nm=trim($wri_prsn_nm); $wri_prsn_sffx_num=trim($wri_prsn_sffx_num);
                    $wri_prsn_sffx_rmn=' ('.romannumeral($wri_prsn_sffx_num).')';
                  }
                  else
                  {$wri_prsn_sffx_num='0'; $wri_prsn_sffx_rmn='';}

                  list($wri_prsn_frst_nm, $wri_prsn_lst_nm)=explode(';;', $wri_prsn_nm);
                  $wri_prsn_frst_nm=trim($wri_prsn_frst_nm); $wri_prsn_lst_nm=trim($wri_prsn_lst_nm);

                  if(preg_match('/\S+/', $wri_prsn_lst_nm)) {$wri_prsn_lst_nm_dsply=' '.$wri_prsn_lst_nm;}
                  else {$wri_prsn_lst_nm_dsply='';}

                  $wri_prsn_fll_nm=$wri_prsn_frst_nm.$wri_prsn_lst_nm_dsply;
                  $wri_prsn_url=generateurl($wri_prsn_fll_nm.$wri_prsn_sffx_rmn);
                  $wri_ordr=++$o;

                  $sql="SELECT 1 FROM prsn WHERE prsn_url='$wri_prsn_url'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existence of writer (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  if(mysqli_num_rows($result)==0)
                  {
                    $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                          VALUES('$wri_prsn_fll_nm', '$wri_prsn_frst_nm', '$wri_prsn_lst_nm', '$wri_prsn_sffx_num', '$wri_prsn_url', '0')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding writer (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql= "INSERT INTO ptwri(ptid, wri_rlid, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, wri_compid, wri_prsnid)
                        SELECT $pt_id, $wri_rl_id, '$wri_prsn_rl', $wri_ordr, $org_wri, $src_wri, $grntr,
                        (SELECT comp_id FROM comp WHERE comp_url='$wri_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$wri_prsn_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding playtext-writer (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }

              if(preg_match('/\S+/', $wri_prsn_nm2))
              {
                if(preg_match('/\S+.*~~.*\S+/', $wri_prsn_nm2))
                {
                  list($wri_prsn_rl, $wri_prsn_nm)=explode('~~', $wri_prsn_nm2);
                  $wri_prsn_rl=trim($wri_prsn_rl); $wri_prsn_nm=trim($wri_prsn_nm);
                }
                else {$wri_prsn_rl=''; $wri_prsn_nm=$wri_prsn_nm2;}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $wri_prsn_nm))
                {
                  list($wri_prsn_nm, $wri_prsn_sffx_num)=explode('--', $wri_prsn_nm);
                  $wri_prsn_nm=trim($wri_prsn_nm); $wri_prsn_sffx_num=trim($wri_prsn_sffx_num);
                  $wri_prsn_sffx_rmn=' ('.romannumeral($wri_prsn_sffx_num).')';
                }
                else
                {$wri_prsn_sffx_num='0'; $wri_prsn_sffx_rmn='';}

                list($wri_prsn_frst_nm, $wri_prsn_lst_nm)=explode(';;', $wri_prsn_nm);
                $wri_prsn_frst_nm=trim($wri_prsn_frst_nm); $wri_prsn_lst_nm=trim($wri_prsn_lst_nm);

                if(preg_match('/\S+/', $wri_prsn_lst_nm)) {$wri_prsn_lst_nm_dsply=' '.$wri_prsn_lst_nm;}
                else {$wri_prsn_lst_nm_dsply='';}

                $wri_prsn_fll_nm=$wri_prsn_frst_nm.$wri_prsn_lst_nm_dsply;
                $wri_prsn_url=generateurl($wri_prsn_fll_nm.$wri_prsn_sffx_rmn);
                $wri_ordr=++$o;

                $sql="SELECT 1 FROM prsn WHERE prsn_url='$wri_prsn_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of writer (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                        VALUES('$wri_prsn_fll_nm', '$wri_prsn_frst_nm', '$wri_prsn_lst_nm', '$wri_prsn_sffx_num', '$wri_prsn_url', '0')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding writer (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO ptwri(ptid, wri_rlid, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, wri_compid, wri_prsnid)
                      SELECT $pt_id, $wri_rl_id, '$wri_prsn_rl', $wri_ordr, $org_wri, $src_wri, $grntr, '0', prsn_id FROM prsn WHERE prsn_url='$wri_prsn_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding playtext-writer (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
          }
          else {$wri_rl='';}

          $sql= "INSERT INTO ptwrirl(ptid, wri_rl_id, wri_rl, src_mat_rl)
                VALUES('$pt_id', '$wri_rl_id', '$wri_rl', '$src_mat_rl')";
          if(!mysqli_query($link, $sql)) {$error='Error adding writer-role association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptcntrrl WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-contributor (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptcntr WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-contributor (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $cntr_list))
      {
        $cntr_comp_prsn_rls=explode(',,', $cntr_list);
        $m=0;
        foreach($cntr_comp_prsn_rls as $cntr_comp_prsn_rl)
        {
          $cntr_rl_id=++$m;

          if(preg_match('/\S+/', $cntr_comp_prsn_rl))
          {
            list($cntr_rl, $cntr_comp_prsn_list)=explode('::', $cntr_comp_prsn_rl);
            $cntr_rl=trim($cntr_rl); $cntr_comp_prsn_list=trim($cntr_comp_prsn_list);

            $o=0;
            $cntr_comps_ppl=explode('>>', $cntr_comp_prsn_list);
            foreach($cntr_comps_ppl as $cntr_comp_prsn)
            {
              if(preg_match('/\|\|/', $cntr_comp_prsn))
              {
                list($cntr_comp_nm, $cntr_prsn_nm_list)=explode('||', $cntr_comp_prsn);
                $cntr_comp_nm=trim($cntr_comp_nm); $cntr_prsn_nm_list=trim($cntr_prsn_nm_list);
                $cntr_prsn_nm2='';
              }
              else
              {$cntr_comp_nm=''; $cntr_prsn_nm_list=''; $cntr_prsn_nm2=trim($cntr_comp_prsn);}

              if(preg_match('/\S+/', $cntr_comp_nm))
              {
                if(preg_match('/\S+.*~~.*\S+/', $cntr_comp_nm))
                {
                  list($cntr_comp_rl, $cntr_comp_nm)=explode('~~', $cntr_comp_nm);
                  $cntr_comp_rl=trim($cntr_comp_rl); $cntr_comp_nm=trim($cntr_comp_nm);
                }
                else {$cntr_comp_rl='';}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $cntr_comp_nm))
                {
                  list($cntr_comp_nm, $cntr_comp_sffx_num)=explode('--', $cntr_comp_nm);
                  $cntr_comp_nm=trim($cntr_comp_nm); $cntr_comp_sffx_num=trim($cntr_comp_sffx_num);
                  $cntr_comp_sffx_rmn=' ('.romannumeral($cntr_comp_sffx_num).')';
                }
                else
                {$cntr_comp_sffx_num='0'; $cntr_comp_sffx_rmn='';}

                $cntr_ordr=++$o;
                $cntr_comp_url=generateurl($cntr_comp_nm.$cntr_comp_sffx_rmn);
                $cntr_comp_alph=alph($cntr_comp_nm);

                $sql="SELECT 1 FROM comp WHERE comp_url='$cntr_comp_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of contributor (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                        VALUES('$cntr_comp_nm', CASE WHEN '$cntr_comp_alph'!='' THEN '$cntr_comp_alph' END, '$cntr_comp_sffx_num', '$cntr_comp_url', 1, 0, 0)";
                  if(!mysqli_query($link, $sql)) {$error='Error adding contributor (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO ptcntr(ptid, cntr_rlid, cntr_sb_rl, cntr_ordr, cntr_prsnid, cntr_compid)
                      SELECT $pt_id, $cntr_rl_id, '$cntr_comp_rl', $cntr_ordr, '0', comp_id FROM comp WHERE comp_url='$cntr_comp_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding playtext-contributor (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              if(preg_match('/\S+/', $cntr_prsn_nm_list))
              {
                $cntr_prsn_nms=explode('//', $cntr_prsn_nm_list);
                foreach($cntr_prsn_nms as $cntr_prsn_nm)
                {
                  $cntr_prsn_nm=trim($cntr_prsn_nm);
                  if(preg_match('/\S+.*~~.*\S+/', $cntr_prsn_nm))
                  {
                    list($cntr_prsn_rl, $cntr_prsn_nm)=explode('~~', $cntr_prsn_nm);
                    $cntr_prsn_rl=trim($cntr_prsn_rl); $cntr_prsn_nm=trim($cntr_prsn_nm);
                  }
                  else {$cntr_prsn_rl='';}

                  if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $cntr_prsn_nm))
                  {
                    list($cntr_prsn_nm, $cntr_prsn_sffx_num)=explode('--', $cntr_prsn_nm);
                    $cntr_prsn_nm=trim($cntr_prsn_nm); $cntr_prsn_sffx_num=trim($cntr_prsn_sffx_num);
                    $cntr_prsn_sffx_rmn=' ('.romannumeral($cntr_prsn_sffx_num).')';
                  }
                  else
                  {$cntr_prsn_sffx_num='0'; $cntr_prsn_sffx_rmn='';}

                  list($cntr_prsn_frst_nm, $cntr_prsn_lst_nm)=explode(';;', $cntr_prsn_nm);
                  $cntr_prsn_frst_nm=trim($cntr_prsn_frst_nm); $cntr_prsn_lst_nm=trim($cntr_prsn_lst_nm);

                  if(preg_match('/\S+/', $cntr_prsn_lst_nm)) {$cntr_prsn_lst_nm_dsply=' '.$cntr_prsn_lst_nm;}
                  else {$cntr_prsn_lst_nm_dsply='';}

                  $cntr_prsn_fll_nm=$cntr_prsn_frst_nm.$cntr_prsn_lst_nm_dsply;
                  $cntr_prsn_url=generateurl($cntr_prsn_fll_nm.$cntr_prsn_sffx_rmn);
                  $cntr_ordr=++$o;

                  $sql="SELECT 1 FROM prsn WHERE prsn_url='$cntr_prsn_url'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existence of contributor (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  if(mysqli_num_rows($result)==0)
                  {
                    $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                          VALUES('$cntr_prsn_fll_nm', '$cntr_prsn_frst_nm', '$cntr_prsn_lst_nm', '$cntr_prsn_sffx_num', '$cntr_prsn_url', '0')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding contributor (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql= "INSERT INTO ptcntr(ptid, cntr_rlid, cntr_sb_rl, cntr_ordr, cntr_compid, cntr_prsnid)
                        SELECT $pt_id, $cntr_rl_id, '$cntr_prsn_rl', $cntr_ordr,
                        (SELECT comp_id FROM comp WHERE comp_url='$cntr_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$cntr_prsn_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding playtext-contributor (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }

              if(preg_match('/\S+/', $cntr_prsn_nm2))
              {
                if(preg_match('/\S+.*~~.*\S+/', $cntr_prsn_nm2))
                {
                  list($cntr_prsn_rl, $cntr_prsn_nm)=explode('~~', $cntr_prsn_nm2);
                  $cntr_prsn_rl=trim($cntr_prsn_rl); $cntr_prsn_nm=trim($cntr_prsn_nm);
                }
                else {$cntr_prsn_rl=''; $cntr_prsn_nm=$cntr_prsn_nm2;}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $cntr_prsn_nm))
                {
                  list($cntr_prsn_nm, $cntr_prsn_sffx_num)=explode('--', $cntr_prsn_nm);
                  $cntr_prsn_nm=trim($cntr_prsn_nm); $cntr_prsn_sffx_num=trim($cntr_prsn_sffx_num);
                  $cntr_prsn_sffx_rmn=' ('.romannumeral($cntr_prsn_sffx_num).')';
                }
                else
                {$cntr_prsn_sffx_num='0'; $cntr_prsn_sffx_rmn='';}

                list($cntr_prsn_frst_nm, $cntr_prsn_lst_nm)=explode(';;', $cntr_prsn_nm);
                $cntr_prsn_frst_nm=trim($cntr_prsn_frst_nm); $cntr_prsn_lst_nm=trim($cntr_prsn_lst_nm);

                if(preg_match('/\S+/', $cntr_prsn_lst_nm)) {$cntr_prsn_lst_nm_dsply=' '.$cntr_prsn_lst_nm;}
                else {$cntr_prsn_lst_nm_dsply='';}

                $cntr_prsn_fll_nm=$cntr_prsn_frst_nm.$cntr_prsn_lst_nm_dsply;
                $cntr_prsn_url=generateurl($cntr_prsn_fll_nm.$cntr_prsn_sffx_rmn);
                $cntr_ordr=++$o;

                $sql="SELECT 1 FROM prsn WHERE prsn_url='$cntr_prsn_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of contributor (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                        VALUES('$cntr_prsn_fll_nm', '$cntr_prsn_frst_nm', '$cntr_prsn_lst_nm', '$cntr_prsn_sffx_num', '$cntr_prsn_url', '0')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding contributor (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO ptcntr(ptid, cntr_rlid, cntr_sb_rl, cntr_ordr, cntr_compid, cntr_prsnid)
                      SELECT $pt_id, $cntr_rl_id, '$cntr_prsn_rl', $cntr_ordr, '0', prsn_id FROM prsn WHERE prsn_url='$cntr_prsn_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding playtext-contributor (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
          }
          else {$cntr_rl='';}

          $sql= "INSERT INTO ptcntrrl(ptid, cntr_rl_id, cntr_rl)
                VALUES('$pt_id', '$cntr_rl_id', '$cntr_rl')";
          if(!mysqli_query($link, $sql)) {$error='Error adding contributor-role association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptmat WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $mat_list))
      {
        $mat_nm_frmts=explode(',,', $mat_list);
        $n=0;
        foreach($mat_nm_frmts as $mat_nm_frmt)
        {
          $mat_nm_frmt=trim($mat_nm_frmt);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $mat_nm_frmt))
          {
            list($mat_nm_frmt, $mat_sffx_num)=explode('--', $mat_nm_frmt);
            $mat_nm_frmt=trim($mat_nm_frmt); $mat_sffx_num=trim($mat_sffx_num);
            $mat_sffx_rmn=' ('.romannumeral($mat_sffx_num).')';
          }
          else
          {$mat_sffx_num='0'; $mat_sffx_rmn='';}

          list($mat_nm, $frmt_nm)=explode(';;', $mat_nm_frmt);
          $mat_nm=trim($mat_nm); $frmt_nm=trim($frmt_nm);

          $mat_ordr=++$n;
          $mat_url=generateurl($mat_nm.$mat_sffx_rmn);
          $frmt_url=generateurl($frmt_nm);
          $mat_alph=alph($mat_nm);

          $sql="SELECT 1 FROM frmt WHERE frmt_url='$frmt_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of (material) format: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO frmt(frmt_nm, frmt_url)
                  VALUES('$frmt_nm', '$frmt_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding (material) format data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql="SELECT 1 FROM mat WHERE mat_url='$mat_url' AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$frmt_url')";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of material-format combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO mat(mat_nm, mat_alph, mat_sffx_num, mat_url, frmtid)
                  SELECT '$mat_nm', CASE WHEN '$mat_alph'!='' THEN '$mat_alph' END, '$mat_sffx_num', '$mat_url', frmt_id FROM frmt WHERE frmt_url='$frmt_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO ptmat(ptid, mat_ordr, matid)
                SELECT $pt_id, $mat_ordr, mat_id FROM mat WHERE mat_url='$mat_url' AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$frmt_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding playtext-material association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptctgry WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-category associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $ctgry_list))
      {
        $ctgry_nms=explode(',,', $ctgry_list);
        $n=0;
        foreach($ctgry_nms as $ctgry_nm)
        {
          $ctgry_nm=trim($ctgry_nm);
          $ctgry_url=generateurl($ctgry_nm);
          $ctgry_ordr=++$n;

          $sql="SELECT 1 FROM ctgry WHERE ctgry_url='$ctgry_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of category: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO ctgry(ctgry_nm, ctgry_url)
                  VALUES('$ctgry_nm', '$ctgry_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO ptctgry(ptid, ctgry_ordr, ctgryid)
                SELECT '$pt_id', '$ctgry_ordr', ctgry_id FROM ctgry WHERE ctgry_url='$ctgry_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding playtext-category association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptgnr WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-genre associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $gnr_list))
      {
        $gnr_nms=explode(',,', $gnr_list);
        $n=0;
        foreach($gnr_nms as $gnr_nm)
        {
          $gnr_nm=trim($gnr_nm);
          $gnr_url=generateurl($gnr_nm);
          $gnr_ordr=++$n;

          $sql="SELECT 1 FROM gnr WHERE gnr_url='$gnr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of genre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO gnr(gnr_nm, gnr_url)
                  VALUES('$gnr_nm', '$gnr_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO ptgnr(ptid, gnr_ordr, gnrid)
                SELECT '$pt_id', '$gnr_ordr', gnr_id FROM gnr WHERE gnr_url='$gnr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding playtext-genre association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptftr WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-feature associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $ftr_list))
      {
        $ftr_nms=explode(',,', $ftr_list);
        $n=0;
        foreach($ftr_nms as $ftr_nm)
        {
          $ftr_nm=trim($ftr_nm);
          $ftr_url=generateurl($ftr_nm);
          $ftr_ordr=++$n;

          $sql="SELECT 1 FROM ftr WHERE ftr_url='$ftr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of feature: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO ftr(ftr_nm, ftr_url)
                  VALUES('$ftr_nm', '$ftr_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding feature data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO ptftr(ptid, ftr_ordr, ftrid)
                SELECT '$pt_id', '$ftr_ordr', ftr_id FROM ftr WHERE ftr_url='$ftr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding playtext-feature association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptthm WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-theme associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $thm_list))
      {
        $thm_nms=explode(',,', $thm_list);
        $n=0;
        foreach($thm_nms as $thm_nm)
        {
          $thm_nm=trim($thm_nm);
          $thm_ordr=++$n;
          $thm_url=generateurl($thm_nm);
          $thm_alph=alph($thm_nm);

          $sql="SELECT 1 FROM thm WHERE thm_url='$thm_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of theme: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO thm(thm_nm, thm_alph, thm_url)
                  VALUES('$thm_nm', CASE WHEN '$thm_alph'!='' THEN '$thm_alph' END, '$thm_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO ptthm(ptid, thm_ordr, thmid)
                SELECT '$pt_id', '$thm_ordr', thm_id FROM thm WHERE thm_url='$thm_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding playtext-theme association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM ptsttng WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptsttng_plc WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting (place) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptsttng_lctn WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting (location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptsttng_lctn_alt WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptsttng_tm WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting (time) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $sttng_list))
      {
        $n=0;
        $sttng_tm_lctn_plcs=explode(',,', $sttng_list);
        foreach($sttng_tm_lctn_plcs as $sttng_tm_lctn_plc)
        {
          $sttng_id=++$n;

          if(preg_match('/(\S+.*)?\+\+.*\S+/', $sttng_tm_lctn_plc))
          {
            list($sttng_tm_lctn, $sttng_plc_list)=explode('++', $sttng_tm_lctn_plc);
            $sttng_tm_lctn=trim($sttng_tm_lctn); $sttng_plc_list=trim($sttng_plc_list);

            $sttng_plcs=explode('//', $sttng_plc_list);
            $o=0;
            foreach($sttng_plcs as $sttng_plc)
            {
              $sttng_plc=trim($sttng_plc);

              if(preg_match('/\S+.*;;.*\S+/', $sttng_plc)) {list($sttng_plc, $sttng_plc_nt2)=explode(';;', $sttng_plc); $sttng_plc=trim($sttng_plc); $sttng_plc_nt2=trim($sttng_plc_nt2);}
              else {$sttng_plc_nt2='';}

              if(preg_match('/\S+.*::.*\S+/', $sttng_plc)) {list($sttng_plc_nt1, $sttng_plc)=explode('::', $sttng_plc); $sttng_plc_nt1=trim($sttng_plc_nt1); $sttng_plc=trim($sttng_plc);}
              else {$sttng_plc_nt1='';}

              $sttng_plc_url=generateurl($sttng_plc);
              $sttng_plc_ordr=++$o;

              $sql="SELECT 1 FROM plc WHERE plc_url='$sttng_plc_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existence of setting (place): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              if(mysqli_num_rows($result)==0)
              {
                $sql= "INSERT INTO plc(plc_nm, plc_url)
                      VALUES('$sttng_plc', '$sttng_plc_url')";
                if(!mysqli_query($link, $sql)) {$error='Error adding setting (place) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              $sql= "INSERT INTO ptsttng_plc(ptid, sttngid, sttng_plc_ordr, sttng_plc_nt1, sttng_plc_nt2, sttng_plcid)
                    SELECT '$pt_id', '$sttng_id', '$sttng_plc_ordr', '$sttng_plc_nt1', '$sttng_plc_nt2', plc_id
                    FROM plc WHERE plc_url='$sttng_plc_url'";
              if(!mysqli_query($link, $sql)) {$error='Error adding playtext-setting (place) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }
          }
          else
          {$sttng_tm_lctn=$sttng_tm_lctn_plc;}

          if(preg_match('/(\S+.*)?##.*\S+/', $sttng_tm_lctn))
          {
            list($sttng_tm_list, $sttng_lctn_list)=explode('##', $sttng_tm_lctn);
            $sttng_tm_list=trim($sttng_tm_list); $sttng_lctn_list=trim($sttng_lctn_list);

            $sttng_lctns=explode('//', $sttng_lctn_list);
            $p=0;
            foreach($sttng_lctns as $sttng_lctn)
            {
              $sttng_lctn=trim($sttng_lctn);

              if(preg_match('/\S+.*\|\|.*\S+/', $sttng_lctn))
              {
                list($sttng_lctn, $sttng_lctn_alt_list)=explode('||', $sttng_lctn);
                $sttng_lctn=trim($sttng_lctn); $sttng_lctn_alt_list=trim($sttng_lctn_alt_list);
              }
              else {$sttng_lctn_alt_list='';}

              if(preg_match('/\S+.*;;.*\S+/', $sttng_lctn)) {list($sttng_lctn, $sttng_lctn_nt2)=explode(';;', $sttng_lctn); $sttng_lctn=trim($sttng_lctn); $sttng_lctn_nt2=trim($sttng_lctn_nt2);}
              else {$sttng_lctn_nt2='';}

              if(preg_match('/\S+.*::.*\S+/', $sttng_lctn)) {list($sttng_lctn_nt1, $sttng_lctn)=explode('::', $sttng_lctn); $sttng_lctn_nt1=trim($sttng_lctn_nt1); $sttng_lctn=trim($sttng_lctn);}
              else {$sttng_lctn_nt1='';}

              if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $sttng_lctn))
              {
                list($sttng_lctn, $sttng_lctn_sffx_num)=explode('--', $sttng_lctn);
                $sttng_lctn=trim($sttng_lctn); $sttng_lctn_sffx_num=trim($sttng_lctn_sffx_num);
                $sttng_lctn_sffx_rmn=' ('.romannumeral($sttng_lctn_sffx_num).')';
              }
              else {$sttng_lctn_sffx_num='0'; $sttng_lctn_sffx_rmn='';}

              $sttng_lctn_ordr=++$p;
              $sttng_lctn_url=generateurl($sttng_lctn.$sttng_lctn_sffx_rmn);
              $sttng_lctn_alph=alph($sttng_lctn);

              $sql="SELECT 1 FROM lctn WHERE lctn_url='$sttng_lctn_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existence of setting (location): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              if(mysqli_num_rows($result)==0)
              {
                $sql= "INSERT INTO lctn(lctn_nm, lctn_alph, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn)
                      VALUES('$sttng_lctn', CASE WHEN '$sttng_lctn_alph'!='' THEN '$sttng_lctn_alph' END, '$sttng_lctn_sffx_num', '$sttng_lctn_url', 0, 0)";
                if(!mysqli_query($link, $sql)) {$error='Error adding setting (location) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              $sql= "INSERT INTO ptsttng_lctn(ptid, sttngid, sttng_lctn_ordr, sttng_lctn_nt1, sttng_lctn_nt2, sttng_lctnid)
                    SELECT '$pt_id', '$sttng_id', '$sttng_lctn_ordr', '$sttng_lctn_nt1', '$sttng_lctn_nt2', lctn_id
                    FROM lctn WHERE lctn_url='$sttng_lctn_url'";
              if(!mysqli_query($link, $sql)) {$error='Error adding playtext-setting (location) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

              if($sttng_lctn_alt_list)
              {
                $sttng_lctn_alts=explode('>>', $sttng_lctn_alt_list);
                foreach($sttng_lctn_alts as $sttng_lctn_alt)
                {
                  $sttng_lctn_alt=trim($sttng_lctn_alt);

                  if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $sttng_lctn_alt))
                  {
                    list($sttng_lctn_alt, $sttng_lctn_alt_sffx_num)=explode('--', $sttng_lctn_alt);
                    $sttng_lctn_alt=trim($sttng_lctn_alt); $sttng_lctn_alt_sffx_num=trim($sttng_lctn_alt_sffx_num);
                    $sttng_lctn_alt_sffx_rmn=' ('.romannumeral($sttng_lctn_alt_sffx_num).')';
                  }
                  else {$sttng_lctn_alt_sffx_num='0'; $sttng_lctn_alt_sffx_rmn='';}

                  $sttng_lctn_alt_url=generateurl($sttng_lctn_alt.$sttng_lctn_alt_sffx_rmn);

                  $sql= "INSERT INTO ptsttng_lctn_alt(ptid, sttngid, sttng_lctnid, sttng_lctn_altid)
                        SELECT '$pt_id', '$sttng_id',
                        (SELECT lctn_id FROM lctn WHERE lctn_url='$sttng_lctn_url'),
                        (SELECT lctn_id FROM lctn WHERE lctn_url='$sttng_lctn_alt_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding playtext-setting (alternate location) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }
            }
          }
          else
          {$sttng_tm_list=$sttng_tm_lctn;}

          if($sttng_tm_list)
          {
            if(preg_match('/^\S+.*\*$/', $sttng_tm_list))
            {
              $sttng_tm_list=preg_replace('/(\S+.*)(\*)/', '$1', $sttng_tm_list); $sttng_tm_list=trim($sttng_tm_list);
              $sql= "INSERT INTO ptsttng(ptid, sttng_id, tm_spn)
                    SELECT '$pt_id', '$sttng_id', '1'";
              if(!mysqli_query($link, $sql)) {$error='Error adding playtext-setting (time) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }

            $sttng_tms=explode('//', $sttng_tm_list);
            $q=0;
            foreach($sttng_tms as $sttng_tm)
            {
              $sttng_tm=trim($sttng_tm);

              if(preg_match('/\S+.*;;.*\S+/', $sttng_tm)) {list($sttng_tm, $sttng_tm_nt2)=explode(';;', $sttng_tm); $sttng_tm=trim($sttng_tm); $sttng_tm_nt2=trim($sttng_tm_nt2);}
              else {$sttng_tm_nt2='';}

              if(preg_match('/\S+.*::.*\S+/', $sttng_tm)) {list($sttng_tm_nt1, $sttng_tm)=explode('::', $sttng_tm); $sttng_tm_nt1=trim($sttng_tm_nt1); $sttng_tm=trim($sttng_tm);}
              else {$sttng_tm_nt1='';}

              $sttng_tm_ordr=++$q;
              $sttng_tm_url=generateurl($sttng_tm);
              $sttng_tm_alph=alph($sttng_tm);

              $sql="SELECT 1 FROM tm WHERE tm_url='$sttng_tm_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existence of setting (time): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              if(mysqli_num_rows($result)==0)
              {
                $sql= "INSERT INTO tm(tm_nm, tm_alph, tm_url, tm_frm_dt_bce, tm_to_dt_bce, tm_rcr)
                      VALUES('$sttng_tm', CASE WHEN '$sttng_tm_alph'!='' THEN '$sttng_tm_alph' END, '$sttng_tm_url', 0, 0, 0)";
                if(!mysqli_query($link, $sql)) {$error='Error adding setting (time) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              $sql= "INSERT INTO ptsttng_tm(ptid, sttngid, sttng_tm_ordr, sttng_tm_nt1, sttng_tm_nt2, sttng_tmid)
                    SELECT '$pt_id', '$sttng_id', '$sttng_tm_ordr', '$sttng_tm_nt1', '$sttng_tm_nt2', tm_id
                    FROM tm WHERE tm_url='$sttng_tm_url'";
              if(!mysqli_query($link, $sql)) {$error='Error adding playtext-setting (time) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }
          }
        }
      }

      $sql="DELETE FROM ptchar WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-character associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptchar_grp WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-character group associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $char_list))
      {
        $char_grp_nms=explode('@@', $char_list); $m=0;
        foreach($char_grp_nms as $char_grp_nm)
        {
          $char_grp_nm=trim($char_grp_nm); $char_grp_id=++$m;
          if(preg_match('/^\S+.*==.*\S$/', $char_grp_nm))
          {
            list($char_grp, $char_nm_list)=explode('==', $char_grp_nm);
            $char_grp=trim($char_grp); $char_nm_list=trim($char_nm_list);

            $sql="INSERT INTO ptchar_grp(ptid, char_grp_id, char_grp) VALUES('$pt_id', '$char_grp_id', '$char_grp')";
            if(!mysqli_query($link, $sql)) {$error='Error adding playtext-character group data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
          else {$char_nm_list=$char_grp_nm; $char_grp_id=NULL;}

          $char_nms=explode(',,', $char_nm_list); $n=0;
          foreach($char_nms as $char_nm)
          {
            if(preg_match('/\S+.*::.*\S+/', $char_nm) && (substr_count($char_nm, '::')==1)) {list($char_nm, $char_nt)=explode('::', $char_nm); $char_nm=trim($char_nm); $char_nt=trim($char_nt);}
            else {$char_nm=trim($char_nm); $char_nt=NULL;}

            if(preg_match('/^\S+.*--[1-9][0-9]{0,5}$/', $char_nm)) {list($char_nm, $char_sffx_num)=explode('--', $char_nm); $char_nm=trim($char_nm); $char_sffx_num=trim($char_sffx_num); $char_sffx_rmn=' ('.romannumeral($char_sffx_num).')';}
            else {$char_sffx_num='0'; $char_sffx_rmn='';}

            $char_ordr=++$n;
            $char_url=generateurl($char_nm.$char_sffx_rmn);
            $char_alph=alph($char_nm);

            $sql="SELECT 1 FROM role WHERE char_url='$char_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existence of character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $sql= "INSERT INTO role(char_nm, char_alph, char_sffx_num, char_lnk, char_url, char_amnt)
                    VALUES('$char_nm', CASE WHEN '$char_alph'!='' THEN '$char_alph' END, '$char_sffx_num', '$char_nm', '$char_url', '1')";
              if(!mysqli_query($link, $sql)) {$error='Error adding character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }

            $sql= "INSERT INTO ptchar(ptid, char_ordr, char_nt, char_grpid, charid)
                  SELECT '$pt_id', '$char_ordr', '$char_nt', CASE WHEN '$char_grp_id'!='' THEN '$char_grp_id' END, char_id
                  FROM role WHERE char_url='$char_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding playtext-character association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      $sql="SELECT char_sx, char_amnt, char_mlti FROM ptchar INNER JOIN role ON charid=char_id WHERE ptid='$pt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring character data for count: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$char_counts[]=array('char_sx'=>$row['char_sx'], 'char_amnt'=>$row['char_amnt'], 'char_mlti'=>$row['char_mlti']);}
      if(!empty($char_counts))
      {
        $char_ttl_array=array(); $char_m_array=array(); $char_f_array=array(); $char_non_spc_array=array(); $char_na_array=array(); $char_addt_array=array();
        foreach($char_counts as $char_count)
        {
          if($char_count['char_mlti']=='1')
          {$char_addt_array[]='1';}
          else
          {
            $char_ttl_array[]=$char_count['char_amnt'];
            if($char_count['char_sx']=='2') {$char_m_array[]=$char_count['char_amnt'];}
            elseif($char_count['char_sx']=='3') {$char_f_array[]=$char_count['char_amnt'];}
            elseif($char_count['char_sx']=='4') {$char_non_spc_array[]=$char_count['char_amnt'];}
            else {$char_na_array[]=$char_count['char_amnt'];}
          }
        }
        $char_ttl=array_sum($char_ttl_array);
        $char_m=array_sum($char_m_array);
        $char_f=array_sum($char_f_array);
        $char_non_spc=array_sum($char_non_spc_array);
        $char_na=array_sum($char_na_array);
        $char_addt=array_sum($char_addt_array);

        if($char_addt>0)
        {$char_addt='1';} else {$char_addt='0';}
      }
      else
      {$char_ttl=0; $char_m=0; $char_f=0; $char_non_spc=0; $char_na=0; $char_addt='0';}

      $sql= "UPDATE pt SET
            char_ttl='$char_ttl',
            char_m='$char_m',
            char_f='$char_f',
            char_non_spc='$char_non_spc',
            char_na='$char_na',
            char_addt='$char_addt'
            WHERE pt_id='$pt_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating character totals for submitted playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM ptlcnsr WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-licensor associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $lcnsr_list))
      {
        $lcnsr_comp_prsns=explode(',,', $lcnsr_list);
        $n=0;
        foreach($lcnsr_comp_prsns as $lcnsr_comp_prsn)
        {
          if(preg_match('/\|\|/', $lcnsr_comp_prsn))
          {
            list($lcnsr_comp_nm_rl, $lcnsr_prsn_nm_rl_list)=explode('||', $lcnsr_comp_prsn);
            $lcnsr_comp_nm_rl=trim($lcnsr_comp_nm_rl); $lcnsr_prsn_nm_rl_list=trim($lcnsr_prsn_nm_rl_list);
            $lcnsr_prsn_nm_rl2='';
          }
          else
          {$lcnsr_comp_nm_rl=''; $lcnsr_prsn_nm_rl_list=''; $lcnsr_prsn_nm_rl2=trim($lcnsr_comp_prsn);}

          if(preg_match('/\S+/', $lcnsr_comp_nm_rl))
          {
            list($lcnsr_comp_nm, $lcnsr_comp_rl)=explode('::', $lcnsr_comp_nm_rl);
            $lcnsr_comp_nm=trim($lcnsr_comp_nm); $lcnsr_comp_rl=trim($lcnsr_comp_rl);

            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $lcnsr_comp_nm))
            {
              list($lcnsr_comp_nm, $lcnsr_comp_sffx_num)=explode('--', $lcnsr_comp_nm);
              $lcnsr_comp_nm=trim($lcnsr_comp_nm); $lcnsr_comp_sffx_num=trim($lcnsr_comp_sffx_num);
              $lcnsr_comp_sffx_rmn=' ('.romannumeral($lcnsr_comp_sffx_num).')';
            }
            else
            {$lcnsr_comp_sffx_num='0'; $lcnsr_comp_sffx_rmn='';}

            $lcnsr_ordr=++$n;
            $lcnsr_comp_url=generateurl($lcnsr_comp_nm.$lcnsr_comp_sffx_rmn);
            $lcnsr_comp_alph=alph($lcnsr_comp_nm);

            $sql="SELECT 1 FROM comp WHERE comp_url='$lcnsr_comp_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existence of licensor (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool)
                    VALUES('$lcnsr_comp_nm', CASE WHEN '$lcnsr_comp_alph'!='' THEN '$lcnsr_comp_alph' END, '$lcnsr_comp_sffx_num', '$lcnsr_comp_url', 1, 0, 0)";
              if(!mysqli_query($link, $sql)) {$error='Error adding licensor (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }

            $sql= "INSERT INTO ptlcnsr(ptid, lcnsr_ordr, lcnsr_rl, lcnsr_prsnid, lcnsr_compid)
                  SELECT $pt_id, $lcnsr_ordr, '$lcnsr_comp_rl', '0', comp_id
                  FROM comp WHERE comp_url='$lcnsr_comp_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding playtext-licensor (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          if(preg_match('/\S+/', $lcnsr_prsn_nm_rl_list))
          {
            $lcnsr_prsn_nm_rls=explode('//', $lcnsr_prsn_nm_rl_list);
            foreach($lcnsr_prsn_nm_rls as $lcnsr_prsn_nm_rl)
            {
              list($lcnsr_prsn_nm, $lcnsr_prsn_rl)=explode('::', $lcnsr_prsn_nm_rl);
              $lcnsr_prsn_nm=trim($lcnsr_prsn_nm); $lcnsr_prsn_rl=trim($lcnsr_prsn_rl);

              if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $lcnsr_prsn_nm))
              {
                list($lcnsr_prsn_nm, $lcnsr_prsn_sffx_num)=explode('--', $lcnsr_prsn_nm);
                $lcnsr_prsn_nm=trim($lcnsr_prsn_nm); $lcnsr_prsn_sffx_num=trim($lcnsr_prsn_sffx_num);
                $lcnsr_prsn_sffx_rmn=' ('.romannumeral($lcnsr_prsn_sffx_num).')';
              }
              else
              {$lcnsr_prsn_sffx_num='0'; $lcnsr_prsn_sffx_rmn='';}

              list($lcnsr_prsn_frst_nm, $lcnsr_prsn_lst_nm)=explode(';;', $lcnsr_prsn_nm);
              $lcnsr_prsn_frst_nm=trim($lcnsr_prsn_frst_nm); $lcnsr_prsn_lst_nm=trim($lcnsr_prsn_lst_nm);

              if(preg_match('/\S+/', $lcnsr_prsn_lst_nm))
              {$lcnsr_prsn_lst_nm_dsply=' '.$lcnsr_prsn_lst_nm;}
              else
              {$lcnsr_prsn_lst_nm_dsply='';}

              $lcnsr_prsn_fll_nm=$lcnsr_prsn_frst_nm.$lcnsr_prsn_lst_nm_dsply;
              $lcnsr_prsn_url=generateurl($lcnsr_prsn_fll_nm.$lcnsr_prsn_sffx_rmn);
              $lcnsr_ordr=++$n;

              $sql="SELECT 1 FROM prsn WHERE prsn_url='$lcnsr_prsn_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existence of licensor (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              if(mysqli_num_rows($result)==0)
              {
                $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                      VALUES('$lcnsr_prsn_fll_nm', '$lcnsr_prsn_frst_nm', '$lcnsr_prsn_lst_nm', '$lcnsr_prsn_sffx_num', '$lcnsr_prsn_url', '0')";
                if(!mysqli_query($link, $sql)) {$error='Error adding licensor (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              $sql= "INSERT INTO ptlcnsr(ptid, lcnsr_ordr, lcnsr_rl, lcnsr_compid, lcnsr_prsnid)
                    SELECT $pt_id, $lcnsr_ordr, '$lcnsr_prsn_rl',
                    (SELECT comp_id FROM comp WHERE comp_url='$lcnsr_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$lcnsr_prsn_url')";
              if(!mysqli_query($link, $sql)) {$error='Error adding playtext-licensor (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }
          }

          if(preg_match('/\S+/', $lcnsr_prsn_nm_rl2))
          {
            list($lcnsr_prsn_nm, $lcnsr_prsn_rl)=explode('::', $lcnsr_prsn_nm_rl2);
            $lcnsr_prsn_nm=trim($lcnsr_prsn_nm); $lcnsr_prsn_rl=trim($lcnsr_prsn_rl);

            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $lcnsr_prsn_nm))
            {
              list($lcnsr_prsn_nm, $lcnsr_prsn_sffx_num)=explode('--', $lcnsr_prsn_nm);
              $lcnsr_prsn_nm=trim($lcnsr_prsn_nm); $lcnsr_prsn_sffx_num=trim($lcnsr_prsn_sffx_num);
              $lcnsr_prsn_sffx_rmn=' ('.romannumeral($lcnsr_prsn_sffx_num).')';
            }
            else
            {$lcnsr_prsn_sffx_num='0'; $lcnsr_prsn_sffx_rmn='';}

            list($lcnsr_prsn_frst_nm, $lcnsr_prsn_lst_nm)=explode(';;', $lcnsr_prsn_nm);
            $lcnsr_prsn_frst_nm=trim($lcnsr_prsn_frst_nm); $lcnsr_prsn_lst_nm=trim($lcnsr_prsn_lst_nm);

            if(preg_match('/\S+/', $lcnsr_prsn_lst_nm))
            {$lcnsr_prsn_lst_nm_dsply=' '.$lcnsr_prsn_lst_nm;}
            else
            {$lcnsr_prsn_lst_nm_dsply='';}

            $lcnsr_prsn_fll_nm=$lcnsr_prsn_frst_nm.$lcnsr_prsn_lst_nm_dsply;
            $lcnsr_prsn_url=generateurl($lcnsr_prsn_fll_nm.$lcnsr_prsn_sffx_rmn);
            $lcnsr_ordr=++$n;

            $sql="SELECT 1 FROM prsn WHERE prsn_url='$lcnsr_prsn_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existence of licensor (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                    VALUES('$lcnsr_prsn_fll_nm', '$lcnsr_prsn_frst_nm', '$lcnsr_prsn_lst_nm', '$lcnsr_prsn_sffx_num', '$lcnsr_prsn_url', '0')";
              if(!mysqli_query($link, $sql)) {$error='Error adding licensor (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }

            $sql= "INSERT INTO ptlcnsr(ptid, lcnsr_ordr, lcnsr_rl, lcnsr_compid, lcnsr_prsnid)
                  SELECT $pt_id, $lcnsr_ordr, '$lcnsr_prsn_rl', '0', prsn_id
                  FROM prsn WHERE prsn_url='$lcnsr_prsn_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding playtext-licensor (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      $sql="DELETE FROM pt_alt_nm WHERE ptid='$pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-playtext alternate name associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $alt_nm_list))
      {
        $alt_nms=explode(',,', $alt_nm_list);
        $n=0;
        foreach($alt_nms as $alt_nm)
        {
          $alt_nm=trim($alt_nm);
          $alt_nm_ordr=++$n;

          if(preg_match('/^\S+.*::.*\S+$/', $alt_nm))
          {
            list($alt_nm, $alt_nm_dscr)=explode('::', $alt_nm);
            $alt_nm=trim($alt_nm); $alt_nm_dscr=trim($alt_nm_dscr);
          }
          else
          {$alt_nm_dscr='';}

          $sql= "INSERT INTO pt_alt_nm(ptid, pt_alt_nm, pt_alt_nm_dscr, pt_alt_nm_ordr)
                SELECT '$pt_id', '$alt_nm', '$alt_nm_dscr', '$alt_nm_ordr'";
          if(!mysqli_query($link, $sql)) {$error='Error adding playtext-playtext alternate name association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PLAYTEXT HAS BEEN EDITED:'.' '.html($pt_nm_yr_session);
    header('Location: '.$pt_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $pt_id=cln($_POST['pt_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdpt WHERE ptid='$pt_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-playtext association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production';}

    $sql="SELECT 1 FROM ptwrks WHERE wrks_ov='$pt_id' OR wrks_sg='$pt_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext collected works-playtext segment association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext collected works (overview/segment)';}

    $sql="SELECT 1 FROM pt WHERE (coll_ov='$pt_id') OR (pt_id='$pt_id' AND coll_ov IS NOT NULL) LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext collection-playtext segment association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext collection (overview/segment)';}

    $sql="SELECT 1 FROM ptlcnsr WHERE ptid='$pt_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring licensor-playtext association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Licensor';}

    $sql="SELECT 1 FROM awrdnompts WHERE nom_ptid='$pt_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring award-playtext (nominee/winner) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Awards nomination/win';}

    if(count($assocs)>0)
    {$errors['pt_dlt']='**Playtext must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT pt_nm_yr, pt_sffx_num
            FROM pt
            WHERE pt_id='$pt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring playtext details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['pt_sffx_num']) {$pt_sffx_rmn=' ('.romannumeral($row['pt_sffx_num']).')';} else {$pt_sffx_rmn='';}
      $pagetab='Edit: '.html($row['pt_nm_yr'].$pt_sffx_rmn);
      $pagetitle=html($row['pt_nm_yr'].$pt_sffx_rmn);
      $pt_nm=$_POST['pt_nm'];
      $pt_sbnm=$_POST['pt_sbnm'];
      $txt_vrsn_list=$_POST['txt_vrsn_list'];
      if(isset($_POST['pt_yr_strtd_c'])) {$pt_yr_strtd_c='1';} else {$pt_yr_strtd_c='0';}
      $pt_yr_strtd=$_POST['pt_yr_strtd'];
      if(isset($_POST['pt_yr_strtd_bce'])) {$pt_yr_strtd_bce='1';} else {$pt_yr_strtd_bce='0';}
      if(isset($_POST['pt_yr_wrttn_c'])) {$pt_yr_wrttn_c='1';} else {$pt_yr_wrttn_c='0';}
      $pt_yr_wrttn=$_POST['pt_yr_wrttn'];
      if(isset($_POST['pt_yr_wrttn_bce'])) {$pt_yr_wrttn_bce='1';} else {$pt_yr_wrttn_bce='0';}
      $pt_sffx_num=$_POST['pt_sffx_num'];
      $pt_pub_dt=$_POST['pt_pub_dt'];
      if($_POST['pt_pub_dt_frmt']=='1') {$pt_pub_dt_frmt='1';} if($_POST['pt_pub_dt_frmt']=='2') {$pt_pub_dt_frmt='2';}
      if($_POST['pt_pub_dt_frmt']=='3') {$pt_pub_dt_frmt='3';} if($_POST['pt_pub_dt_frmt']=='4') {$pt_pub_dt_frmt='4';}
      if($_POST['pt_coll']=='1') {$pt_coll='1'; $coll_dsply='';}
      if($_POST['pt_coll']=='2') {$pt_coll='2'; $coll_dsply=' [COLLECTED WORKS]';}
      if($_POST['pt_coll']=='3') {$pt_coll='3'; $coll_dsply=' [COLLECTION]';}
      if($_POST['pt_coll']=='4') {$pt_coll='4'; $coll_dsply=' [PART OF COLLECTION]';}
      $pt_wrks_sg_list=$_POST['pt_wrks_sg_list'];
      $pt_coll_sg_list=$_POST['pt_coll_sg_list'];
      $pt_lnk_list=$_POST['pt_lnk_list'];
      $wri_list=$_POST['wri_list'];
      $cntr_list=$_POST['cntr_list'];
      $mat_list=$_POST['mat_list'];
      $ctgry_list=$_POST['ctgry_list'];
      $gnr_list=$_POST['gnr_list'];
      $ftr_list=$_POST['ftr_list'];
      $thm_list=$_POST['thm_list'];
      $sttng_list=$_POST['sttng_list'];
      $cst_m=$_POST['cst_m'];
      $cst_f=$_POST['cst_f'];
      $cst_non_spc=$_POST['cst_non_spc'];
      if(isset($_POST['cst_addt'])) {$cst_addt='1';} else {$cst_addt='0';}
      $cst_nt=$_POST['cst_nt'];
      $char_list=$_POST['char_list'];
      $lcnsr_list=$_POST['lcnsr_list'];
      $alt_nm_list=$_POST['alt_nm_list'];
      $textarea=$_POST['textarea'];
      $pt_id=html($pt_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT pt_nm_yr, pt_sffx_num
            FROM pt
            WHERE pt_id='$pt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring playtext details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['pt_sffx_num']) {$pt_sffx_rmn=' ('.romannumeral($row['pt_sffx_num']).')';} else {$pt_sffx_rmn='';}
      $pagetab= 'Delete confirmation: '.html($row['pt_nm_yr'].$pt_sffx_rmn);
      $pagetitle=html($row['pt_nm_yr'].$pt_sffx_rmn);
      $pt_id=html($pt_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $pt_id=cln($_POST['pt_id']);

    $sql="SELECT pt_nm_yr, pt_sffx_num FROM pt WHERE pt_id='$pt_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['pt_sffx_num']) {$pt_sffx_rmn=' ('.romannumeral($row['pt_sffx_num']).')';} else {$pt_sffx_rmn='';}
    $pt_nm_yr=$row['pt_nm_yr'].$pt_sffx_rmn;

    $sql="DELETE FROM prdpt WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM pttxt_vrsn WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-text version associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptwrks WHERE wrks_ov='$pt_id' OR wrks_sg='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-collected works associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptwrks_sbhdr WHERE wrks_ov='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-collected works subheader associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="UPDATE pt SET coll_ov=NULL, coll_sbhdrid=NULL, coll_ordr=NULL WHERE coll_ov='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error nullifying playtext-collection associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptcoll_sbhdr WHERE coll_ov='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-collection subheader associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptlnk WHERE lnk1='$pt_id' OR lnk2='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-link associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptwrirl WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-writer (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptwri WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-writer associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsrc_mat WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-source material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptmat WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-material associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptcntrrl WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-contributor (role) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptcntrrl WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-contributor associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptctgry WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-category associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptgnr WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-genre associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptftr WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-feature associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptthm WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-theme associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng_tm WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting (time) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng_lctn WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting (location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng_lctn_alt WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptsttng_plc WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-setting (place) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptchar WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-character associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptlcnsr WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-licensor associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM pt_alt_nm WHERE ptid='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-playtext alternate name associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM pt WHERE pt_id='$pt_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PLAYTEXT HAS BEEN DELETED FROM THE DATABASE:'.' '.html($pt_nm_yr);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $pt_id=cln($_POST['pt_id']);
    $sql="SELECT pt_url FROM pt WHERE pt_id='$pt_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['pt_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $pt_url=cln($_GET['pt_url']);

  $sql="SELECT pt_id FROM pt WHERE pt_url='$pt_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $pt_id=$row['pt_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT pt_nm_yr, pt_sffx_num, pt_nm, pt_sbnm, pt_yr_strtd, pt_yr_strtd_c, pt_yr_wrttn, pt_yr_wrttn_c, CASE WHEN pt_pub_dt_frmt=1 THEN DATE_FORMAT(pt_pub_dt, '%d %b %Y') WHEN pt_pub_dt_frmt=2 THEN DATE_FORMAT(pt_pub_dt, '%b %Y') WHEN pt_pub_dt_frmt=3 THEN DATE_FORMAT(pt_pub_dt, '%Y') ELSE NULL END AS pt_pub_dt, pt_coll, cst_m, cst_f, cst_non_spc, cst_ttl, cst_addt, cst_nt, char_ttl, char_m, char_f, char_non_spc, char_na, char_addt
          FROM pt
          WHERE pt_id='$pt_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['pt_sffx_num']) {$pt_sffx_rmn=' ('.romannumeral($row['pt_sffx_num']).')';} else {$pt_sffx_rmn='';}
    $pagetab=html($row['pt_nm_yr'].$pt_sffx_rmn);
    $pagetitle=html($row['pt_nm']);
    $pt_sbnm=html($row['pt_sbnm']);
    $pt_pub_dt=html($row['pt_pub_dt']);
    if($row['pt_coll']=='2') {$coll_dsply=' [COLLECTED WORKS]'; $coll_wrks='1'; $coll_ov=''; $coll_sg=''; $pt_wrttn_compld=' compiled';}
    elseif($row['pt_coll']=='3') {$coll_dsply=' [COLLECTION]'; $coll_wrks=''; $coll_ov='1'; $coll_sg=''; $pt_wrttn_compld=' written';}
    elseif($row['pt_coll']=='4') {$coll_dsply=' [PART OF COLLECTION]'; $coll_wrks=''; $coll_ov=''; $coll_sg='1'; $pt_wrttn_compld=' written';}
    else {$coll_dsply=''; $coll_wrks=''; $coll_ov=''; $coll_sg=''; $coll_dsply=''; $pt_wrttn_compld=' written';}
    if($row['cst_nt']) {$cst_nt=html($row['cst_nt']);} else {$cst_nt=NULL;}

    $cst_array=array();
    if($row['cst_ttl']) {$cst_ttl=html($row['cst_ttl']);} else {$cst_ttl=NULL;}
    if($row['cst_m']) {$cst_m='Male: '.html($row['cst_m']); $cst_array[]=$cst_m;}
    if($row['cst_f']) {$cst_f='Female: '.html($row['cst_f']); $cst_array[]=$cst_f;}
    if($row['cst_non_spc']) {$cst_non_spc='Non-specific: '.html($row['cst_non_spc']); $cst_array[]=$cst_non_spc;}
    if($row['cst_addt']) {$cst_addt='<em>Plus additional roles</em>'; $cst_array[]=$cst_addt;}
    $cst=implode(' | ', $cst_array);

    $char_array=array();
    if($row['char_ttl']) {$char_ttl=html($row['char_ttl']);} else {$char_ttl=NULL;}
    if($row['char_m']) {$char_m='Male: '.html($row['char_m']); $char_array[]=$char_m;}
    if($row['char_f']) {$char_f='Female: '.html($row['char_f']); $char_array[]=$char_f;}
    if($row['char_non_spc']) {$char_non_spc='Non-specific: '.html($row['char_non_spc']); $char_array[]=$char_non_spc;}
    if($row['char_na']) {$char_na='Unassigned: '.html($row['char_na']); $char_array[]=$char_na;}
    if($row['char_addt']) {$char_addt='<em>Plus additional roles</em>'; $char_array[]=$char_addt;}
    $char_dtls=implode(' | ', $char_array);

    if($row['pt_yr_strtd_c']) {$pt_yr_strtd_c='c.';} else {$pt_yr_strtd_c='';}
    if($row['pt_yr_strtd'])
    {
      if(preg_match('/^-/', $row['pt_yr_strtd']))
      {
        $pt_yr_strtd=html(preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $row['pt_yr_strtd']));
        if(!preg_match('/^-/', $row['pt_yr_wrttn'])) {$pt_yr_strtd .= ' BCE';}
      }
      else {$pt_yr_strtd=html($row['pt_yr_strtd']);}
      $pt_yr_strtd .= '-';
    }
    else {$pt_yr_strtd='';}
    if($row['pt_yr_wrttn_c']) {$pt_yr_wrttn_c='c.';} else {$pt_yr_wrttn_c='';}
    if(!preg_match('/^-/', $row['pt_yr_wrttn'])) {$pt_yr_wrttn=$row['pt_yr_wrttn']; $pt_yr_wrttn_url=$row['pt_yr_wrttn'];}
    else {$pt_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', '$1 BCE', $row['pt_yr_wrttn']); $pt_yr_wrttn_url=preg_replace('/^-([1-9][0-9]{0,3})/', '$1-bce', $row['pt_yr_wrttn']);}
    $pt_yr_wrttn_lnk='<a href="/playtext/year/'.html($pt_yr_wrttn_url).'">'.html($pt_yr_wrttn).'</a>';
    $pt_yr_dsply=$pt_yr_strtd_c.$pt_yr_strtd.$pt_yr_wrttn_c.$pt_yr_wrttn_lnk;

    $awrd_pt_ids=array();

    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdpt
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE ptid='$pt_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdpt
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE ptid='$pt_id' AND coll_ov IS NULL
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
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prd_ids))
      {
        foreach($prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdpt WHERE prdid='$prd_id' AND ptid='$pt_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdpt
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE ptid='$pt_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring production segments: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

    $sql= "SELECT txt_vrsn_nm, txt_vrsn_url
          FROM pttxt_vrsn
          INNER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE ptid='$pt_id'
          ORDER BY txt_vrsn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring text version data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$txt_vrsns[]='<a href="/playtext/text-version/'.html($row['txt_vrsn_url']).'">'.html($row['txt_vrsn_nm']).'</a>';}

    if($coll_wrks)
    {
      $sql="SELECT wrks_sbhdr_id, wrks_sbhdr FROM ptwrks_sbhdr WHERE wrks_ov='$pt_id' ORDER BY wrks_sbhdr_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collected works subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result)) {$wrks_sg_sbhdrs[$row['wrks_sbhdr_id']]=array('wrks_sbhdr'=>html($row['wrks_sbhdr']), 'wrks_sg_pts'=>array());}

      $sql= "SELECT wrks_sbhdrid, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, wrks_sg_rl, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwrks
            INNER JOIN pt ON wrks_sg=pt_id LEFT OUTER JOIN pttxt_vrsn ON pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE wrks_ov='$pt_id'
            GROUP BY pt_id ORDER BY wrks_sbhdrid ASC, wrks_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collected works segment playtext details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        if(empty($wrks_sg_sbhdrs)) {$wrks_sg_sbhdrs['1']=array('wrks_sbhdr'=>NULL, 'wrks_sg_pts'=>array());}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          if($row['wrks_sbhdrid']) {$wrks_sbhdrid=$row['wrks_sbhdrid'];} else {$wrks_sbhdrid='1';}
          $wrks_sg_sbhdrs[$wrks_sbhdrid]['wrks_sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wrks_sg_rl'=>html($row['wrks_sg_rl']), 'wri_rls'=>array(), 'sg_pts'=>array());
        }

        $sql= "SELECT wrks_sbhdrid, ptid, wri_rl_id, wri_rl, src_mat_rl FROM ptwrks
              INNER JOIN ptwrirl ON wrks_sg=ptid WHERE wrks_ov='$pt_id'
              GROUP BY ptid, wri_rl_id ORDER BY wri_rl_id";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring writer (roles) data (for collected works segment playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['wrks_sbhdrid']) {$wrks_sbhdrid=$row['wrks_sbhdrid'];} else {$wrks_sbhdrid='1';}
          $wrks_sg_sbhdrs[$wrks_sbhdrid]['wrks_sg_pts'][$row['ptid']]['wri_rls'][$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());
        }

        $sql= "SELECT wrks_sbhdrid, ptid, wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url FROM ptwrks
              INNER JOIN ptsrc_mat ON wrks_sg=ptid INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id WHERE wrks_ov='$pt_id'
              GROUP BY ptid, wri_rlid, mat_id ORDER BY src_mat_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring source material data (for collected works segment playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          $src_mat_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['mat_nm']).'</a>';
          $src_mat_frmt_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['frmt_nm']).'</a>';
          if($row['wrks_sbhdrid']) {$wrks_sbhdrid=$row['wrks_sbhdrid'];} else {$wrks_sbhdrid='1';}
          $wrks_sg_sbhdrs[$wrks_sbhdrid]['wrks_sg_pts'][$row['ptid']]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_url'=>$src_mat_url, 'src_mat_frmt_url'=>$src_mat_frmt_url, 'src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));
        }

        $sql= "SELECT wrks_sbhdrid, ptid, wri_rlid, comp_id, comp_nm, comp_url, wri_sb_rl, wri_ordr, comp_bool FROM ptwrks
              INNER JOIN ptwri ON wrks_sg=ptid INNER JOIN comp ON wri_compid=comp_id
              WHERE wrks_ov='$pt_id' AND wri_prsnid=0
              GROUP BY ptid, wri_rlid, comp_id
              UNION
              SELECT wrks_sbhdrid, ptid, wri_rlid, prsn_id, prsn_fll_nm, prsn_url, wri_sb_rl, wri_ordr, comp_bool FROM ptwrks
              INNER JOIN ptwri ON wrks_sg=ptid INNER JOIN prsn ON wri_prsnid=prsn_id
              WHERE wrks_ov='$pt_id' AND wri_compid=0
              GROUP BY ptid, wri_rlid, prsn_id
              ORDER BY wri_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring writer data (for collected works segment playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
          if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
          else {$comp_nm=html($row['comp_nm']);}
          if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ';} else {$wri_sb_rl='';}
          if($row['wrks_sbhdrid']) {$wrks_sbhdrid=$row['wrks_sbhdrid'];} else {$wrks_sbhdrid='1';}
          $wrks_sg_sbhdrs[$wrks_sbhdrid]['wrks_sg_pts'][$row['ptid']]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
        }

        $sql= "SELECT wrks_sbhdrid, ptid, wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm, prsn_url
              FROM ptwrks
              INNER JOIN ptwri ON wrks_sg=ptid INNER JOIN prsn ON wri_prsnid=prsn_id WHERE wrks_ov='$pt_id' AND wri_compid!=0
              GROUP BY ptid, wri_rlid, wri_compid, prsn_id ORDER BY wri_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring writer (company people) data (for collected works segment playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
          if($row['wri_sb_rl']) {if(!preg_match('/^(:|;|,|\.)/', $row['wri_sb_rl'])) {$wri_sb_rl=' '.html($row['wri_sb_rl']).' ';} else {$wri_sb_rl=html($row['wri_sb_rl']).' ';}} else {$wri_sb_rl='';}
          if($row['wrks_sbhdrid']) {$wrks_sbhdrid=$row['wrks_sbhdrid'];} else {$wrks_sbhdrid='1';}
          $wrks_sg_sbhdrs[$wrks_sbhdrid]['wrks_sg_pts'][$row['ptid']]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'wri_sb_rl'=>$wri_sb_rl);
        }

        $sql= "SELECT wrks_sbhdrid, p2.coll_ov, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url
              FROM ptwrks
              INNER JOIN pt p1 ON wrks_sg=p1.pt_id INNER JOIN pt p2 ON p1.pt_id=p2.coll_ov
              WHERE wrks_ov='$pt_id'
              GROUP BY coll_ov, pt_id
              ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring segment playtext data (for collected works overview playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          if($row['wrks_sbhdrid']) {$wrks_sbhdrid=$row['wrks_sbhdrid'];} else {$wrks_sbhdrid='1';}
          $wrks_sg_sbhdrs[$wrks_sbhdrid]['wrks_sg_pts'][$row['coll_ov']]['sg_pts'][]=$pt_nm.' ('.$pt_yr.')';
        }
      }
    }
    else
    {
      $sql= "SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph, wrks_sbhdr, wrks_sg_rl
            FROM ptwrks pw
            INNER JOIN pt ON wrks_ov=pt_id LEFT OUTER JOIN ptwrks_sbhdr pwsh ON pw.wrks_ov=pwsh.wrks_ov AND wrks_sbhdrid=wrks_sbhdr_id
            WHERE wrks_sg='$pt_id' GROUP BY pt_id ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring playtext collected works overview details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $wrks_ov_pt_ids[]=$row['pt_id'];
          $wrks_ov_pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>'Collected Works', 'pt_yr'=>$pt_yr, 'wrks_sg_rl'=>html($row['wrks_sg_rl']), 'wrks_sbhdr'=>html($row['wrks_sbhdr']), 'cntr_rls'=>array(), 'sg_pts'=>array());
        }

        if(!empty($wrks_ov_pt_ids))
        {
          foreach($wrks_ov_pt_ids as $wrks_ov_pt_id)
          {
            $sql="SELECT cntr_rl_id, cntr_rl FROM ptcntrrl WHERE ptid='$wrks_ov_pt_id' GROUP BY ptid, cntr_rl_id ORDER BY cntr_rl_id";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring contributor (roles) data for collected works overview playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {$wrks_ov_pts[$wrks_ov_pt_id]['cntr_rls'][$row['cntr_rl_id']]=array('cntr_rl'=>html($row['cntr_rl']), 'cntrs'=>array());}

            $sql= "SELECT cntr_rlid, comp_id, comp_nm, comp_url, cntr_sb_rl, cntr_ordr, comp_bool FROM ptcntr
                  INNER JOIN comp ON cntr_compid=comp_id WHERE ptid='$wrks_ov_pt_id' AND cntr_prsnid=0
                  UNION
                  SELECT cntr_rlid, prsn_id, prsn_fll_nm, prsn_url, cntr_sb_rl, cntr_ordr, comp_bool FROM ptcntr
                  INNER JOIN prsn ON cntr_prsnid=prsn_id WHERE ptid='$wrks_ov_pt_id' AND cntr_compid=0
                  ORDER BY cntr_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring contributor (company / people) data for collected works overview playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
              if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
              else {$comp_nm=html($row['comp_nm']);}
              if($row['cntr_sb_rl']) {$cntr_sb_rl=' (<em>'.html($row['cntr_sb_rl']).'</em>)';} else {$cntr_sb_rl='';}
              $wrks_ov_pts[$wrks_ov_pt_id]['cntr_rls'][$row['cntr_rlid']]['cntrs'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'cntr_sb_rl'=>$cntr_sb_rl, 'cntrcomp_ppl'=>array());
            }

            $sql= "SELECT cntr_rlid, cntr_compid, cntr_sb_rl, prsn_fll_nm, prsn_url FROM ptcntr
                  INNER JOIN prsn ON cntr_prsnid=prsn_id WHERE ptid='$wrks_ov_pt_id' AND cntr_compid!=0
                  ORDER BY cntr_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring contributor (company people) data for collected works overview playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
              if($row['cntr_sb_rl']) {$cntr_sb_rl=' (<em>'.html($row['cntr_sb_rl']).'</em>)';} else {$cntr_sb_rl='';}
              $wrks_ov_pts[$wrks_ov_pt_id]['cntr_rls'][$row['cntr_rlid']]['cntrs'][$row['cntr_compid']]['cntrcomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'cntr_sb_rl'=>$cntr_sb_rl);
            }
          }
        }

        $sql= "SELECT p2.coll_ov, p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url
              FROM ptwrks pw1
              INNER JOIN pt p1 ON pw1.wrks_ov=p1.pt_id INNER JOIN ptwrks pw2 ON p1.pt_id=pw2.wrks_ov INNER JOIN pt p2 ON pw2.wrks_sg=p2.pt_id
              WHERE pw1.wrks_sg='$pt_id' AND p2.pt_id!='$pt_id'
              GROUP BY coll_ov, pt_id ORDER BY pw2.wrks_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring segment playtext data (for collected works overview playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $wrks_ov_pts[$row['coll_ov']]['sg_pts'][]=$pt_nm.' ('.$pt_yr.')';
        }
      }

      if($coll_ov)
      {
        $sql="SELECT coll_sbhdr_id, coll_sbhdr FROM ptcoll_sbhdr WHERE coll_ov='$pt_id' ORDER BY coll_sbhdr_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring collection subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result)) {$coll_sg_sbhdrs[$row['coll_sbhdr_id']]=array('coll_sbhdr'=>html($row['coll_sbhdr']), 'coll_sg_pts'=>array());}

        $sql= "SELECT coll_sbhdrid, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
              FROM pt
              LEFT OUTER JOIN pttxt_vrsn ON pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
              WHERE coll_ov='$pt_id'
              GROUP BY pt_id
              ORDER BY coll_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring collection segment playtext details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          if(empty($coll_sg_sbhdrs)) {$coll_sg_sbhdrs['1']=array('coll_sbhdr'=>NULL, 'coll_sg_pts'=>array());}
          while($row=mysqli_fetch_array($result))
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
            if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
            $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
            $awrd_pt_ids[]=$row['pt_id'];
          }

          $sql= "SELECT coll_sbhdrid, ptid, wri_rl_id, wri_rl, src_mat_rl FROM pt
                INNER JOIN ptwrirl ON pt_id=ptid WHERE coll_ov='$pt_id'
                GROUP BY ptid, wri_rl_id ORDER BY wri_rl_id";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring writer (roles) data (for collection segment playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
            $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_pts'][$row['ptid']]['wri_rls'][$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());
          }

          $sql= "SELECT coll_sbhdrid, ptid, wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url FROM pt
                INNER JOIN ptsrc_mat ON pt_id=ptid INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id
                WHERE coll_ov='$pt_id' GROUP BY ptid, wri_rlid, mat_id ORDER BY src_mat_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring source material data (for collection segment playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            $src_mat_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['mat_nm']).'</a>';
            $src_mat_frmt_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['frmt_nm']).'</a>';
            if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
            $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_pts'][$row['ptid']]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_url'=>$src_mat_url, 'src_mat_frmt_url'=>$src_mat_frmt_url, 'src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));
          }

          $sql= "SELECT coll_sbhdrid, ptid, wri_rlid, comp_id, comp_nm, comp_url, wri_sb_rl, wri_ordr, comp_bool FROM pt
                INNER JOIN ptwri ON pt_id=ptid INNER JOIN comp ON wri_compid=comp_id WHERE coll_ov='$pt_id' AND wri_prsnid=0
                GROUP BY ptid, wri_rlid, comp_id
                UNION
                SELECT coll_sbhdrid, ptid, wri_rlid, prsn_id, prsn_fll_nm, prsn_url, wri_sb_rl, wri_ordr, comp_bool FROM pt
                INNER JOIN ptwri ON pt_id=ptid INNER JOIN prsn ON wri_prsnid=prsn_id WHERE coll_ov='$pt_id' AND wri_compid=0
                GROUP BY ptid, wri_rlid, prsn_id
                ORDER BY wri_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring writer data (for collection segment playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
            if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
            else {$comp_nm=html($row['comp_nm']);}
            if($row['wri_sb_rl']) {if(!preg_match('/^(:|;|,|\.)/', $row['wri_sb_rl'])) {$wri_sb_rl=' '.html($row['wri_sb_rl']).' ';} else {$wri_sb_rl=html($row['wri_sb_rl']).' ';}} else {$wri_sb_rl='';}
            if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
            $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_pts'][$row['ptid']]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
          }

          $sql= "SELECT coll_sbhdrid, ptid, wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm, prsn_url FROM pt
                INNER JOIN ptwri ON pt_id=ptid INNER JOIN prsn ON wri_prsnid=prsn_id WHERE coll_ov='$pt_id' AND wri_compid!=0
                GROUP BY ptid, wri_rlid, wri_compid, prsn_id ORDER BY wri_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring writer (company people) data (for collection segment playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
            if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ' ;} else {$wri_sb_rl='';}
            if($row['coll_sbhdrid']) {$coll_sbhdrid=$row['coll_sbhdrid'];} else {$coll_sbhdrid='1';}
            $coll_sg_sbhdrs[$coll_sbhdrid]['coll_sg_pts'][$row['ptid']]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'wri_sb_rl'=>$wri_sb_rl);
          }
        }
      }

      if($coll_sg)
      {
        $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, coll_sbhdr
              FROM pt p1
              INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id LEFT OUTER JOIN pttxt_vrsn ON p2.pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
              LEFT OUTER JOIN ptcoll_sbhdr pcsh ON p1.coll_ov=pcsh.coll_ov AND p1.coll_sbhdrid=coll_sbhdr_id
              WHERE p1.pt_id='$pt_id'
              GROUP BY pt_id
              ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring playtext collection overview details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          while($row=mysqli_fetch_array($result))
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
            $coll_ov_pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>'Collection', 'pt_yr'=>$pt_yr, 'coll_sbhdr'=>html($row['coll_sbhdr']), 'wri_rls'=>array(), 'sg_pts'=>array());
            $awrd_pt_ids[]=$row['pt_id'];
          }

          $sql= "SELECT ptid, wri_rl_id, wri_rl, src_mat_rl FROM pt
                INNER JOIN ptwrirl ON coll_ov=ptid WHERE pt_id='$pt_id'
                GROUP BY ptid, wri_rl_id ORDER BY wri_rl_id";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring writer (roles) data (for collection overview playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {$coll_ov_pts[$row['ptid']]['wri_rls'][$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());}

          $sql= "SELECT ptid, wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url FROM pt
                INNER JOIN ptsrc_mat ON coll_ov=ptid INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id
                WHERE pt_id='$pt_id'
                GROUP BY ptid, wri_rlid, mat_id ORDER BY src_mat_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring source material data (for collection overview playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            $src_mat_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['mat_nm']).'</a>';
            $src_mat_frmt_url='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'">'.html($row['frmt_nm']).'</a>';
            $coll_ov_pts[$row['ptid']]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_url'=>$src_mat_url, 'src_mat_frmt_url'=>$src_mat_frmt_url, 'src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));
          }

          $sql= "SELECT ptid, wri_rlid, comp_id, comp_nm, comp_url, wri_sb_rl, wri_ordr, comp_bool FROM pt
                INNER JOIN ptwri ON coll_ov=ptid INNER JOIN comp ON wri_compid=comp_id WHERE pt_id='$pt_id' AND wri_prsnid=0
                GROUP BY ptid, wri_rlid, comp_id
                UNION
                SELECT ptid, wri_rlid, prsn_id, prsn_fll_nm, prsn_url, wri_sb_rl, wri_ordr, comp_bool FROM pt
                INNER JOIN ptwri ON coll_ov=ptid INNER JOIN prsn ON wri_prsnid=prsn_id WHERE pt_id='$pt_id' AND wri_compid=0
                GROUP BY ptid, wri_rlid, prsn_id
                ORDER BY wri_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring writer data (for collection overview playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
            if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
            else {$comp_nm=html($row['comp_nm']);}
            if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ';} else {$wri_sb_rl='';}
            $coll_ov_pts[$row['ptid']]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
          }

          $sql= "SELECT ptid, wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm, prsn_url FROM pt
                INNER JOIN ptwri ON coll_ov=ptid INNER JOIN prsn ON wri_prsnid=prsn_id WHERE pt_id='$pt_id' AND wri_compid!=0
                GROUP BY ptid, wri_rlid, wri_compid, prsn_id ORDER BY wri_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring writer (company people) data (for collection overview playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
            if($row['wri_sb_rl']) {if(!preg_match('/^(:|;|,|\.)/', $row['wri_sb_rl'])) {$wri_sb_rl=' '.html($row['wri_sb_rl']).' ';} else {$wri_sb_rl=html($row['wri_sb_rl']).' ';}} else {$wri_sb_rl='';}
            $coll_ov_pts[$row['ptid']]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'wri_sb_rl'=>$wri_sb_rl);
          }

          $sql= "SELECT p2.pt_id, p3.pt_nm, p3.pt_yr_strtd_c, p3.pt_yr_strtd, p3.pt_yr_wrttn_c, p3.pt_yr_wrttn, p3.pt_url
                FROM pt p1
                INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id INNER JOIN pt p3 ON p2.pt_id=p3.coll_ov
                WHERE p1.pt_id='$pt_id' AND p3.pt_id!='$pt_id'
                GROUP BY pt_id ORDER BY p3.coll_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring segment playtext data (for collection overview playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
            $coll_ov_pts[$row['pt_id']]['sg_pts'][]=$pt_nm.' ('.$pt_yr.')';
          }
        }
      }
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptlnk
          INNER JOIN pt p1 ON lnk2=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn ON p2.pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE lnk1='$pt_id' GROUP BY pt_id
          UNION
          SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptlnk
          INNER JOIN pt p1 ON lnk1=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn ON p2.pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE lnk2='$pt_id' GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptlnk
          INNER JOIN pt p1 ON lnk2=pt_id LEFT OUTER JOIN pttxt_vrsn ON pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE lnk1='$pt_id' AND coll_ov IS NULL GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll,
          (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptlnk
          INNER JOIN pt p1 ON lnk1=pt_id LEFT OUTER JOIN pttxt_vrsn ON pt_id=ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE lnk2='$pt_id' AND coll_ov IS NULL GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring link playtext details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        if($row['pt_id'])
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          if($row['pt_coll']=='2') {$wrks_ids[]=$row['pt_id']; $txt_vrsn_nm='Collected Works';}
          else {$lnk_pt_ids[]=$row['pt_id']; if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}}
          $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($txt_vrsn_nm), 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'coll_sg_lst_pts'=>array(), 'cntr_rls'=>array(), 'wrks_sg_pts'=>array(), 'sg_pts'=>array());
        }
      }

      $pt_id_org=$pt_id;
      if(!empty($lnk_pt_ids))
      {
        foreach($lnk_pt_ids as $pt_id)
        {
          $sql= "SELECT 1 FROM ptlnk WHERE lnk1='$pt_id' AND lnk2='$pt_id_org'
                UNION
                SELECT 1 FROM ptlnk WHERE lnk2='$pt_id' AND lnk1='$pt_id_org'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this playtext (as a link playtext): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';

            $sql= "SELECT p2.coll_ov, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url
                  FROM pt p1
                  INNER JOIN pt p2 ON p1.pt_id=p2.coll_ov WHERE p1.pt_id='$pt_id'
                  GROUP BY coll_ov, pt_id ORDER BY p2.coll_sbhdrid ASC, p2.coll_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring segment playtext data (for link playtexts that are collection overviews): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
              $pts[$pt_id]['coll_sg_lst_pts'][]=$pt_nm.' ('.$pt_yr.')';
            }
          }
        }
      }

      if(!empty($wrks_ids))
      {
        foreach($wrks_ids as $pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_cntr_rcv.inc.php';
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wrks_sg_rcv.inc.php';
        }
      }
      $pt_id=$pt_id_org;

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, coll_sbhdrid, coll_ordr, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM ptlnk
            INNER JOIN pt ON lnk1=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE lnk2='$pt_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            UNION
            SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, coll_sbhdrid, coll_ordr, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM ptlnk
            INNER JOIN pt ON lnk2=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE lnk1='$pt_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data (for collection overview playtexts): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $lnk_sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
      }

      if(!empty($lnk_sg_pt_ids)) {foreach($lnk_sg_pt_ids as $sg_pt_id) {include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';}}
      $lnk_pts=$pts;
    }

    $sql="SELECT wri_rl_id, wri_rl, src_mat_rl FROM ptwrirl WHERE ptid='$pt_id' ORDER BY wri_rl_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring writer (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$wri_rls[$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());}

      $sql= "SELECT wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url
            FROM ptsrc_mat
            INNER JOIN mat ON src_matid=mat_id
            INNER JOIN frmt ON frmtid=frmt_id
            WHERE ptid='$pt_id'
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
            FROM ptwri
            INNER JOIN comp ON wri_compid=comp_id
            WHERE ptid='$pt_id' AND wri_prsnid=0
            UNION
            SELECT wri_rlid, prsn_id, prsn_fll_nm, prsn_url, wri_sb_rl, wri_ordr, comp_bool
            FROM ptwri
            INNER JOIN prsn ON wri_prsnid=prsn_id
            WHERE ptid='$pt_id' AND wri_compid=0
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
            FROM ptwri
            INNER JOIN prsn ON wri_prsnid=prsn_id
            WHERE ptid='$pt_id' AND wri_compid!=0
            ORDER BY wri_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring writer (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ' ;} else {$wri_sb_rl='';}
        $wri_rls[$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'wri_sb_rl'=>$wri_sb_rl);
      }
    }

    $sql="SELECT cntr_rl_id, cntr_rl FROM ptcntrrl WHERE ptid='$pt_id' ORDER BY cntr_rl_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring contributor (roles) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$cntr_rls[$row['cntr_rl_id']]=array('cntr_rl'=>html($row['cntr_rl']), 'cntrs'=>array());}

      $sql= "SELECT cntr_rlid, comp_id, comp_nm, comp_url, cntr_sb_rl, cntr_ordr, comp_bool FROM ptcntr
            INNER JOIN comp ON cntr_compid=comp_id WHERE ptid='$pt_id' AND cntr_prsnid=0
            UNION
            SELECT cntr_rlid, prsn_id, prsn_fll_nm, prsn_url, cntr_sb_rl, cntr_ordr, comp_bool FROM ptcntr
            INNER JOIN prsn ON cntr_prsnid=prsn_id WHERE ptid='$pt_id' AND cntr_compid=0
            ORDER BY cntr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring contributor data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        if($row['cntr_sb_rl']) {$cntr_sb_rl=' (<em>'.html($row['cntr_sb_rl']).'</em>)';} else {$cntr_sb_rl='';}
        $cntr_rls[$row['cntr_rlid']]['cntrs'][$row['comp_id']]=array('comp_nm'=>$comp_nm, 'cntr_sb_rl'=>$cntr_sb_rl, 'cntrcomp_ppl'=>array());
      }

      $sql= "SELECT cntr_rlid, cntr_compid, cntr_sb_rl, prsn_fll_nm, prsn_url FROM ptcntr
            INNER JOIN prsn ON cntr_prsnid=prsn_id WHERE ptid='$pt_id' AND cntr_compid!=0
            ORDER BY cntr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring contributor (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        if($row['cntr_sb_rl']) {$cntr_sb_rl=' (<em>'.html($row['cntr_sb_rl']).'</em>)';} else {$cntr_sb_rl='';}
        $cntr_rls[$row['cntr_rlid']]['cntrs'][$row['cntr_compid']]['cntrcomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'cntr_sb_rl'=>$cntr_sb_rl);
      }
    }

    $sql= "SELECT mat_nm, mat_url, frmt_nm, frmt_url FROM ptmat
          INNER JOIN mat ON matid=mat_id INNER JOIN frmt ON frmtid=frmt_id WHERE ptid='$pt_id' ORDER BY mat_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $mat_nm='<a href="/material/'.html($row['frmt_url']).'/'.html($row['mat_url']).'"><em>'.html($row['mat_nm']).'</em> ('.html($row['frmt_nm']).')</a>';
      $mats[]=array('mat_nm'=>$mat_nm);
    }

    $sql="SELECT ctgry_nm, ctgry_url FROM ptctgry INNER JOIN ctgry ON ctgryid=ctgry_id WHERE ptid='$pt_id' ORDER BY ctgry_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ctgrys[]='<a href="/playtext/category/'.html($row['ctgry_url']).'">'.html($row['ctgry_nm']).'</a>';}

    $sql="SELECT gnr_id, gnr_nm, gnr_url FROM ptgnr INNER JOIN gnr ON gnrid=gnr_id WHERE ptid='$pt_id' ORDER BY gnr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$gnrs[$row['gnr_id']]=array('gnr_nm'=>'<a href="/playtext/genre/'.html($row['gnr_url']).'">'.html($row['gnr_nm']).'</a>', 'rel_gnrs'=>array());}

      $sql= "SELECT rel_gnr1, gnr_nm, gnr_url
            FROM ptgnr
            INNER JOIN rel_gnr ON gnrid=rel_gnr1 INNER JOIN gnr ON rel_gnr2=gnr_id
            WHERE ptid='$pt_id'
            ORDER BY rel_gnr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$gnrs[$row['rel_gnr1']]['rel_gnrs'][]='<a href="/playtext/genre/'.html($row['gnr_url']).'">'.html($row['gnr_nm']).'</a>';}
    }

    $sql="SELECT ftr_nm, ftr_url FROM ptftr INNER JOIN ftr ON ftrid=ftr_id WHERE ptid='$pt_id' ORDER BY ftr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring feature data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ftrs[]='<a href="/playtext/feature/'.html($row['ftr_url']).'">'.html($row['ftr_nm']).'</a>';}

    $sql="SELECT thm_id, thm_nm, thm_url FROM ptthm INNER JOIN thm ON thmid=thm_id WHERE ptid='$pt_id' ORDER BY thm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$thms[$row['thm_id']]=array('thm_nm'=>'<a href="/playtext/theme/'.html($row['thm_url']).'">'.html($row['thm_nm']).'</a>', 'rel_thms'=>array());}

      $sql= "SELECT rel_thm1, thm_nm, thm_url
            FROM ptthm
            INNER JOIN rel_thm ON thmid=rel_thm1 INNER JOIN thm ON rel_thm2=thm_id
            WHERE ptid='$pt_id'
            ORDER BY rel_thm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related theme data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$thms[$row['rel_thm1']]['rel_thms'][]='<a href="/playtext/theme/'.html($row['thm_url']).'">'.html($row['thm_nm']).'</a>';}
    }

    $sql= "SELECT sttngid FROM ptsttng_tm WHERE ptid='$pt_id' GROUP BY sttngid
          UNION
          SELECT sttngid FROM ptsttng_lctn WHERE ptid='$pt_id' GROUP BY sttngid
          UNION
          SELECT sttngid FROM ptsttng_plc WHERE ptid='$pt_id' GROUP BY sttngid
          ORDER BY sttngid ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring setting group data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$sttngs[$row['sttngid']]=array('tms'=>array(), 'tm_spns'=>array(), 'lctns'=>array(), 'plcs'=>array());}

      $sql= "SELECT sttngid, tm_id, tm_nm, tm_url, sttng_tm_nt1, sttng_tm_nt2 FROM ptsttng_tm
            INNER JOIN tm ON sttng_tmid=tm_id
            WHERE ptid='$pt_id'
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
          $sttngs[$row['sttngid']]['tms'][$row['tm_id']]=array('tm'=>$sttng_tm_nt1.'<a href="/playtext/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>'.$sttng_tm_nt2, 'rel_tms'=>array());
        }

        $sql= "SELECT sttngid, rel_tm1, tm_nm, tm_url
              FROM ptsttng_tm
              INNER JOIN rel_tm ON sttng_tmid=rel_tm1 INNER JOIN tm ON rel_tm2=tm_id
              WHERE ptid='$pt_id'
              ORDER BY rel_tm_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring related time data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$sttngs[$row['sttngid']]['tms'][$row['rel_tm1']]['rel_tms'][]='<a href="/playtext/setting/time/'.html($row['tm_url']).'">'.html($row['tm_nm']).'</a>';}
      }

      $sql= "SELECT sttng_id, tm_spn FROM ptsttng
            WHERE ptid='$pt_id' AND tm_spn=1
            ORDER BY sttng_id";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring setting time span data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$sttngs[$row['sttng_id']]['tm_spns'][]=$row['tm_spn'];}

      $sql= "SELECT sttngid, lctn_id, lctn_nm, lctn_url, sttng_lctn_nt1, sttng_lctn_nt2 FROM ptsttng_lctn
            INNER JOIN lctn ON sttng_lctnid=lctn_id
            WHERE ptid='$pt_id'
            ORDER BY sttng_lctn_ordr";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring setting location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_lctn_nt1']) {$sttng_lctn_nt1=html($row['sttng_lctn_nt1']).' ';} else {$sttng_lctn_nt1='';}
          if($row['sttng_lctn_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_lctn_nt2'])) {$sttng_lctn_nt2=html($row['sttng_lctn_nt2']);} else {$sttng_lctn_nt2=' '.html($row['sttng_lctn_nt2']);}}
          else {$sttng_lctn_nt2='';}
          $sttngs[$row['sttngid']]['lctns'][$row['lctn_id']]=array('lctn'=>$sttng_lctn_nt1.'<a href="/playtext/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$sttng_lctn_nt2, 'rel_lctns'=>array());
        }

        $sql= "SELECT psl.sttngid, rel_lctn1, lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
              FROM ptsttng_lctn psl
              INNER JOIN rel_lctn ON sttng_lctnid=rel_lctn1 INNER JOIN lctn ON rel_lctn2=lctn_id
              LEFT OUTER JOIN ptsttng_lctn_alt psla ON psl.ptid=psla.ptid AND psl.sttngid=psla.sttngid AND psl.sttng_lctnid=psla.sttng_lctnid
              WHERE psl.ptid='$pt_id' AND lctn_exp=0 AND lctn_fctn=0 AND psla.ptid IS NULL
              UNION
              SELECT psl.sttngid, rel_lctn1, lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
              FROM ptsttng_lctn psl
              INNER JOIN rel_lctn ON psl.sttng_lctnid=rel_lctn1 INNER JOIN ptsttng_lctn_alt psla ON rel_lctn2=psla.sttng_lctn_altid
              INNER JOIN lctn ON psla.sttng_lctn_altid=lctn_id
              WHERE psl.ptid='$pt_id' AND psl.ptid=psla.ptid AND psl.sttngid=psla.sttngid AND psl.sttng_lctnid=psla.sttng_lctnid
              ORDER BY rel_lctn_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring related location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']).' ';} else {$rel_lctn_nt1='';}
          if($row['rel_lctn_nt2']) {$rel_lctn_nt2=' '.html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2='';}
          $sttngs[$row['sttngid']]['lctns'][$row['rel_lctn1']]['rel_lctns'][]=$rel_lctn_nt1.'<a href="/playtext/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt2;
        }
      }

      $sql= "SELECT sttngid, plc_id, plc_nm, plc_url, sttng_plc_nt1, sttng_plc_nt2 FROM ptsttng_plc
            INNER JOIN plc ON sttng_plcid=plc_id
            WHERE ptid='$pt_id'
            ORDER BY sttng_plc_ordr";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring setting place data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          if($row['sttng_plc_nt1']) {$sttng_plc_nt1=html($row['sttng_plc_nt1']).' '; $plc_nm=html($row['plc_nm']);} else {$sttng_plc_nt1=''; $plc_nm=html(ucfirst($row['plc_nm']));}
          if($row['sttng_plc_nt2']) {if(preg_match('/^(:|;|,|\.)/', $row['sttng_plc_nt2'])) {$sttng_plc_nt2=html($row['sttng_plc_nt2']);} else {$sttng_plc_nt2=' '.html($row['sttng_plc_nt2']);}}
          else {$sttng_plc_nt2='';}
          $sttngs[$row['sttngid']]['plcs'][$row['plc_id']]=array('plc'=>$sttng_plc_nt1.'<a href="/playtext/setting/place/'.html($row['plc_url']).'">'.$plc_nm.'</a>'.$sttng_plc_nt2, 'rel_plcs'=>array());
        }

        $sql= "SELECT sttngid, rel_plc1, plc_nm, plc_url
              FROM ptsttng_plc
              INNER JOIN rel_plc ON sttng_plcid=rel_plc1 INNER JOIN plc ON rel_plc2=plc_id
              WHERE ptid='$pt_id'
              ORDER BY rel_plc_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring related place data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$sttngs[$row['sttngid']]['plcs'][$row['rel_plc1']]['rel_plcs'][]='<a href="/playtext/setting/place/'.html($row['plc_url']).'">'.html(ucfirst($row['plc_nm'])).'</a>';}
      }
    }

    $sql="SELECT char_grp_id, char_grp FROM ptchar_grp WHERE ptid='$pt_id' ORDER BY char_grp_id ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character group data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$char_grps[$row['char_grp_id']]=array('char_grp'=>html($row['char_grp']), 'chars'=>array());}

    $sql= "SELECT char_grpid, char_nm, char_url, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti, char_nt
          FROM ptchar INNER JOIN role ON charid=char_id WHERE ptid='$pt_id' ORDER BY char_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      if(empty($char_grps)) {$char_grps['1']=array('char_grp'=>NULL, 'chars'=>array());}
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_sx']=='2') {$char_sx='Male';} elseif($row['char_sx']=='3') {$char_sx='Female';} elseif($row['char_sx']=='4') {$char_sx='Non-specific';} else {$char_sx=NULL;}
        if($row['char_age_frm']==$row['char_age_to']) {$char_age=html($row['char_age_frm']);} else {$char_age=html($row['char_age_frm']).' - '.html($row['char_age_to']);}
        if($row['char_dscr']) {$char_dscr='<em>'.html($row['char_dscr']).'</em>';} else {$char_dscr=NULL;}
        if($row['char_amnt']>1) {$char_amnt=' ['.html($row['char_amnt']).']';} elseif($row['char_mlti']) {$char_amnt=' [<em>multiple roles</em>]';} else {$char_amnt=NULL;}
        if($row['char_nt']) {$char_nt='</br><em>'.html($row['char_nt']).'</em>';} else {$char_nt=NULL;}
        if($row['char_grpid']) {$char_grpid=$row['char_grpid'];} else {$char_grpid='1';}
        $char_nm='<a href="/character/'.html($row['char_url']).'">'.html($row['char_nm']).'</a>';
        $char_grps[$char_grpid]['chars'][]=array('char_nm'=>$char_nm, 'char_sx'=>$char_sx, 'char_age'=>$char_age, 'char_dscr'=>$char_dscr, 'char_amnt'=>$char_amnt, 'char_nt'=>$char_nt);
      }
    }

    $sql= "SELECT comp_id, comp_nm, comp_url, lcnsr_rl, lcnsr_ordr, comp_bool
          FROM ptlcnsr
          INNER JOIN comp ON lcnsr_compid=comp_id
          WHERE ptid='$pt_id' AND lcnsr_prsnid=0
          UNION
          SELECT prsn_id, prsn_fll_nm, prsn_url, lcnsr_rl, lcnsr_ordr, comp_bool
          FROM ptlcnsr
          INNER JOIN prsn ON lcnsr_prsnid=prsn_id
          WHERE ptid='$pt_id' AND lcnsr_compid=0
          ORDER BY lcnsr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring licensor data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        $lcnsrs[$row['comp_id']]=array('comp_nm'=>$comp_nm, 'lcnsr_rl'=>html($row['lcnsr_rl']), 'lcnsrcomp_ppl'=>array());
      }

      $sql= "SELECT lcnsr_compid, prsn_fll_nm, prsn_url, lcnsr_rl
            FROM ptlcnsr
            INNER JOIN prsn ON lcnsr_prsnid=prsn_id
            WHERE ptid='$pt_id' AND lcnsr_compid!=0
            ORDER BY lcnsr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring rights hadler (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $lcnsrs[$row['lcnsr_compid']]['lcnsrcomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'lcnsr_rl'=>html($row['lcnsr_rl']));
      }
    }

    $sql= "SELECT pt_alt_nm, pt_alt_nm_dscr
          FROM pt_alt_nm
          WHERE ptid='$pt_id'
          ORDER BY pt_alt_nm_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring production alternate name data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['pt_alt_nm_dscr']) {$alt_nm_dscr=' ('.html($row['pt_alt_nm_dscr']).')';} else {$alt_nm_dscr='';}
      $alt_nm='<em>'.html($row['pt_alt_nm']).'</em>'.$alt_nm_dscr;
      $alt_nms[]=$alt_nm;
    }

    $awrd_pt_ids[]=$pt_id;
    $awrd_pt_id=implode($awrd_pt_ids, ' OR nom_ptid=');
    $anp_awrd_pt_id=implode($awrd_pt_ids, ' OR anp1.nom_ptid=');

    $awrds_ttl_wins=array(); $awrds_ttl_noms=array();
    $sql= "SELECT awrds_id, awrds_nm, awrds_url, COALESCE(awrds_alph, awrds_nm)awrds_alph FROM awrdnompts
          INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id
          WHERE (nom_ptid=$awrd_pt_id) GROUP BY awrds_id ORDER BY awrds_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$awrds[$row['awrds_id']]=array('awrds_nm'=>html($row['awrds_nm']), 'awrd_yrs'=>array(), 'awrd_wins'=>array(), 'awrd_noms'=>array());}

      $sql= "SELECT awrdsid, awrd_id, awrd_yr, awrd_yr_end, awrd_yr_url, awrds_url FROM awrdnompts
            INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id
            WHERE (nom_ptid=$awrd_pt_id) GROUP BY awrdsid, awrd_id ORDER BY awrd_yr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['awrd_yr_end']) {$awrd_yr_end=html(preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']));} else {$awrd_yr_end='';}
        $awrd_lnk='<b><a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrd_yr']).$awrd_yr_end.'</a></b>';
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]=array('awrd_lnk'=>$awrd_lnk, 'awrd_yr_wins'=>array(), 'awrd_yr_noms'=>array(), 'ctgrys'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, awrd_ctgry_id, COALESCE(awrd_ctgry_alt_nm, awrd_ctgry_nm)awrd_ctgry_nm
            FROM awrdnompts anp
            INNER JOIN awrd ON anp.awrdid=awrd_id
            INNER JOIN awrdctgrys ac ON anp.awrdid=ac.awrdid AND anp.awrd_ctgryid=ac.awrd_ctgryid INNER JOIN awrd_ctgry ON ac.awrd_ctgryid=awrd_ctgry_id
            WHERE (nom_ptid=$awrd_pt_id) GROUP BY awrdsid, awrd_id, awrd_ctgry_id ORDER BY awrd_ctgry_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgry_id']]=array('awrd_ctgry_nm'=>html($row['awrd_ctgry_nm']), 'noms'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, nom_id, nom_win_dscr, win_bool FROM awrdnompts anp
            INNER JOIN awrd ON anp.awrdid=awrd_id INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND anp.nomid=nom_id
            WHERE (nom_ptid=$awrd_pt_id) GROUP BY awrdsid, awrd_id, awrd_ctgryid, nom_id ORDER BY nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award nominations data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['win_bool']) {$awrds_ttl_wins[]=1; $awrds[$row['awrdsid']]['awrd_wins'][]=1; $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_wins'][]=1;}
        else {$awrds_ttl_noms[]=1; $awrds[$row['awrdsid']]['awrd_noms'][]=1; $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_noms'][]=1;}
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nom_id']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'win'=>$row['win_bool'], 'nomppl'=>array(), 'co_nompts'=>array(), 'cowins'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anpt.awrd_ctgryid, anpt.nomid, nom_ordr, nom_rl, comp_id, comp_nm, comp_url, comp_bool FROM awrdnompts anpt
            INNER JOIN awrd ON anpt.awrdid=awrd_id
            INNER JOIN awrdnomppl anp ON anpt.awrdid=anp.awrdid AND anpt.awrd_ctgryid=anp.awrd_ctgryid AND anpt.nomid=anp.nomid
            INNER JOIN comp ON nom_compid=comp_id
            WHERE (nom_ptid=$awrd_pt_id) AND nom_prsnid=0 GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, comp_id
            UNION
            SELECT awrdsid, awrd_id, anpt.awrd_ctgryid, anpt.nomid, nom_ordr, nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool
            FROM awrdnompts anpt
            INNER JOIN awrd ON anpt.awrdid=awrd_id
            INNER JOIN awrdnomppl anp ON anpt.awrdid=anp.awrdid AND anpt.awrd_ctgryid=anp.awrd_ctgryid AND anpt.nomid=anp.nomid
            INNER JOIN prsn ON nom_prsnid=prsn_id
            WHERE (nom_ptid=$awrd_pt_id) AND nom_compid=0 GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, prsn_id
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

      $sql= "SELECT awrdsid, awrd_id, anpt.awrd_ctgryid, anpt.nomid, nom_compid, nom_rl, prsn_fll_nm, prsn_url FROM awrdnompts anpt
            INNER JOIN awrd ON anpt.awrdid=awrd_id
            INNER JOIN awrdnomppl anp ON anpt.awrdid=anp.awrdid AND anpt.awrd_ctgryid=anp.awrd_ctgryid AND anpt.nomid=anp.nomid
            INNER JOIN prsn ON nom_prsnid=prsn_id
            WHERE (nom_ptid=$awrd_pt_id) AND nom_compid !=0 GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, nom_compid, prsn_id
            ORDER BY nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nomination/win (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nomppl'][$row['nom_compid']]['nomcomp_ppl'][]=$prsn_nm;
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph
            FROM awrdnompts anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnompts anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid
            INNER JOIN pt ON anp2.nom_ptid=pt_id
            WHERE (anp1.nom_ptid=$anp_awrd_pt_id) GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, pt_id ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (playtexts) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['co_nompts'][]=array('pt_id'=>html($row['pt_id']), 'pt_nm'=>$pt_nm.' ('.$pt_yr.')');
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, an2.nom_id AS n2, an2.nom_win_dscr FROM awrdnompts anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
            INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
            INNER JOIN awrdnompts anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid
            WHERE (anp1.nom_ptid=$anp_awrd_pt_id) AND an1.win_bool=1 AND an2.win_bool=1
            AND an2.nom_id NOT IN(SELECT nomid FROM awrdnompts WHERE (nom_ptid=$awrd_pt_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2 ORDER BY an2.nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (co-winner) award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {$awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'cowin_ppl'=>array(), 'cowin_nompts'=>array());}

        $sql= "SELECT awrdsid, awrd_id, anpt.awrd_ctgryid, anpt.nomid AS n1, anp.nomid AS n2, anp.nom_ordr, anp.nom_rl, comp_id, comp_nm, comp_url, comp_bool
              FROM awrdnompts anpt
              INNER JOIN awrd ON anpt.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anpt.awrdid=an1.awrdid AND anpt.awrd_ctgryid=an1.awrd_ctgryid AND anpt.nomid=an1.nom_id
              INNER JOIN awrd_ctgry ON an1.awrd_ctgryid=awrd_ctgry_id
              INNER JOIN awrdnoms an2 ON awrd_ctgry_id=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp ON an2.awrdid=anp.awrdid AND an2.awrd_ctgryid=anp.awrd_ctgryid AND an2.nom_id=anp.nomid INNER JOIN comp ON anp.nom_compid=comp_id
              WHERE (nom_ptid=$awrd_pt_id) AND an1.awrdid=an2.awrdid AND an1.win_bool=1 AND an2.win_bool=1 AND anp.nom_prsnid=0
              AND anp.nomid NOT IN(SELECT nomid FROM awrdnompts WHERE (nom_ptid=$awrd_pt_id) AND awrdid=anpt.awrdid AND awrd_ctgryid=anpt.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, comp_id
              UNION
              SELECT awrdsid, awrd_id, anpt.awrd_ctgryid, anpt.nomid AS n1, anp.nomid AS n2, anp.nom_ordr, anp.nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool
              FROM awrdnompts anpt
              INNER JOIN awrd ON anpt.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anpt.awrdid=an1.awrdid AND anpt.awrd_ctgryid=an1.awrd_ctgryid AND anpt.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp ON an2.awrdid=anp.awrdid AND an2.awrd_ctgryid=anp.awrd_ctgryid AND an2.nom_id=anp.nomid
              INNER JOIN prsn ON anp.nom_prsnid=prsn_id
              WHERE (nom_ptid=$awrd_pt_id) AND an1.win_bool=1 AND an2.win_bool=1 AND anp.nom_compid=0
              AND anp.nomid NOT IN(SELECT nomid FROM awrdnompts WHERE (nom_ptid=$awrd_pt_id) AND awrdid=anpt.awrdid AND awrd_ctgryid=anpt.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prsn_id
              ORDER BY nom_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards company/people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          if(!preg_match('/^the-company$/', $row['comp_url'])) {$cowin_prsn='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
          else {$cowin_prsn=html($row['comp_nm']).$nom_rl;}
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['comp_id']]=array('cowin_prsn'=>$cowin_prsn, 'cowincomp_ppl'=>array());
        }

        $sql= "SELECT awrdsid, awrd_id, anpt.awrd_ctgryid, anpt.nomid AS n1, anp.nomid AS n2, anp.nom_compid, anp.nom_ordr, anp.nom_rl, prsn_id, prsn_fll_nm, prsn_url
              FROM awrdnompts anpt
              INNER JOIN awrd ON anpt.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anpt.awrdid=an1.awrdid AND anpt.awrd_ctgryid=an1.awrd_ctgryid AND anpt.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp ON an2.awrdid=anp.awrdid AND an2.awrd_ctgryid=anp.awrd_ctgryid AND an2.nom_id=anp.nomid
              INNER JOIN prsn ON anp.nom_prsnid=prsn_id
              WHERE (nom_ptid=$awrd_pt_id) AND an1.win_bool=1 AND an2.win_bool=1 AND anp.nom_compid!=0
              AND anp.nomid NOT IN(SELECT nomid FROM awrdnompts WHERE (nom_ptid=$awrd_pt_id) AND awrdid=anpt.awrdid AND awrd_ctgryid=anpt.awrd_ctgryid)
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prsn_id ORDER BY nom_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards company people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          $cowincomp_prsn='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['nom_compid']]['cowincomp_ppl'][]=$cowincomp_prsn;
        }

        $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, anp2.nomid AS n2, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph
              FROM awrdnompts anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnompts anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid AND an2.nom_id=anp2.nomid
              INNER JOIN pt ON anp2.nom_ptid=pt_id
              WHERE (anp1.nom_ptid=$anp_awrd_pt_id) AND an1.win_bool=1 AND an2.win_bool=1
              AND anp2.nomid NOT IN(SELECT nomid FROM awrdnompts WHERE (nom_ptid=$awrd_pt_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, pt_id ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_pts'][]=$pt_nm.' ('.$pt_yr.')';
        }
      }
    }

    $assoc_prd_ids=array(); $awrd_prd_ids=array();
    foreach($awrd_pt_ids as $awrd_pt_id)
    {
      $sql="SELECT prdid FROM prdpt WHERE ptid='$pt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring associated productions data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$assoc_prd_ids[]=$row['prdid']; $awrd_prd_ids[]=$row['prdid'];}
    }

    foreach($assoc_prd_ids as $assoc_prd_id)
    {
      $sql= "SELECT prd_id FROM prd WHERE coll_ov='$assoc_prd_id' UNION
            SELECT coll_ov FROM prd WHERE prd_id='$assoc_prd_id' AND coll_ov IS NOT NULL UNION
            SELECT prd_id FROM prd WHERE tr_ov='$assoc_prd_id' UNION
            SELECT p2.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.coll_ov=p2.tr_ov WHERE p1.prd_id='$assoc_prd_id' UNION
            SELECT p2.prd_id FROM prd p1 INNER JOIN prd p2 ON p1.prd_id=p2.tr_ov WHERE p1.coll_ov='$assoc_prd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring associated productions data (collection overviews/segments and tour overviews): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$awrd_prd_ids[]=$row['prd_id'];}
    }

    $awrd_prd_ids=array_unique($awrd_prd_ids);

    if(!empty($awrd_prd_ids))
    {
      $awrd_prd_id=implode($awrd_prd_ids, ' OR nom_prdid=');
      $anp_awrd_prd_id=implode($awrd_prd_ids, ' OR anp1.nom_prdid=');

      $prd_awrds_ttl_wins=array(); $prd_awrds_ttl_noms=array();
      $sql= "SELECT awrds_id, awrds_nm, awrds_url, COALESCE(awrds_alph, awrds_nm)awrds_alph FROM awrdnomprds
            INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id WHERE (nom_prdid=$awrd_prd_id)
            GROUP BY awrds_id ORDER BY awrds_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards name data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {$prd_awrds[$row['awrds_id']]=array('awrds_nm'=>html($row['awrds_nm']), 'awrd_yrs'=>array(), 'awrd_wins'=>array(), 'awrd_noms'=>array());}

        $sql= "SELECT awrdsid, awrd_id, awrd_yr, awrd_yr_end, awrd_yr_url, awrds_url FROM awrdnomprds
              INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id WHERE (nom_prdid=$awrd_prd_id)
              GROUP BY awrdsid, awrd_id ORDER BY awrd_yr DESC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring award year data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['awrd_yr_end']) {$awrd_yr_end=html(preg_replace('/([0-9]{2})([0-9]{2})$/', "/$2", $row['awrd_yr_end']));} else {$awrd_yr_end='';}
          $awrd_lnk='<b><a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrd_yr']).$awrd_yr_end.'</a></b>';
          $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]=array('awrd_lnk'=>$awrd_lnk, 'awrd_yr_wins'=>array(), 'awrd_yr_noms'=>array(), 'ctgrys'=>array());
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
          $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgry_id']]=array('awrd_ctgry_nm'=>html($row['awrd_ctgry_nm']), 'noms'=>array());
        }

        $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, nom_id, nom_win_dscr, win_bool FROM awrdnomprds anp
              INNER JOIN awrd ON anp.awrdid=awrd_id INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND anp.nomid=nom_id
              WHERE (nom_prdid=$awrd_prd_id) GROUP BY awrdsid, awrd_id, awrd_ctgryid, nom_id ORDER BY nom_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring award nominations data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['win_bool']) {$prd_awrds_ttl_wins[]=1; $prd_awrds[$row['awrdsid']]['awrd_wins'][]=1; $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_wins'][]=1;}
          else {$prd_awrds_ttl_noms[]=1; $prd_awrds[$row['awrdsid']]['awrd_noms'][]=1; $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_noms'][]=1;}
          $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nom_id']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'win'=>$row['win_bool'], 'nomppl'=>array(), 'nomprds'=>array(), 'co_nomprds'=>array(), 'cowins'=>array());
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
        if(!$result) {$error='Error acquiring awards nominee/winner (company/people) data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          if(!preg_match('/^the-company$/', $row['comp_url'])) {$nom_prsn=' <a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
          else {$nom_prsn=html($row['comp_nm']).$nom_rl;}
          $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nomppl'][$row['comp_id']]=array('nom_prsn'=>$nom_prsn, 'nomcomp_ppl'=>array());
        }

        $sql= "SELECT awrdsid, awrd_id, anprd.awrd_ctgryid, anprd.nomid, nom_compid, nom_rl, prsn_fll_nm, prsn_url FROM awrdnomprds anprd
              INNER JOIN awrd ON anprd.awrdid=awrd_id
              INNER JOIN awrdnomppl anp ON anprd.awrdid=anp.awrdid AND anprd.awrd_ctgryid=anp.awrd_ctgryid AND anprd.nomid=anp.nomid
              INNER JOIN prsn ON nom_prsnid=prsn_id WHERE (nom_prdid=$awrd_prd_id) AND nom_compid!=0
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, nom_compid, prsn_id ORDER BY nom_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring awards nomination/win (company people) data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
          $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nomppl'][$row['nom_compid']]['nomcomp_ppl'][]=$prsn_nm;
        }

        $sql= "SELECT awrdsid, awrd_id, awrd_ctgryid, nomid, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm
              FROM awrdnomprds
              INNER JOIN awrd ON awrdid=awrd_id
              INNER JOIN prd p ON nom_prdid=prd_id
              INNER JOIN thtr ON p.thtrid=thtr_id
              WHERE (nom_prdid=$awrd_prd_id) GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring award nominated/winning productions data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nomprds'][]=array('prd_nm'=>$prd_nm, 'prd_id'=>$row['prd_id'], 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
        }

        $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm FROM awrdnomprds anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnomprds anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid
              INNER JOIN prd p ON anp2.nom_prdid=prd_id
              INNER JOIN thtr ON p.thtrid=thtr_id WHERE (anp1.nom_prdid=$anp_awrd_prd_id)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring award co-nominated/winning productions data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['co_nomprds'][]=array('prd_id'=>html($row['prd_id']), 'prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
        }

        $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, an2.nom_id AS n2, an2.nom_win_dscr FROM awrdnomprds anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              WHERE (anp1.nom_prdid=$anp_awrd_prd_id) AND an1.win_bool=1 AND an2.win_bool=1
              AND an2.nom_id NOT IN(SELECT nomid FROM awrdnomprds WHERE (nom_prdid=$awrd_prd_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2 ORDER BY an2.nom_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) award categories data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          while($row=mysqli_fetch_array($result))
          {$prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'cowin_ppl'=>array(), 'cowin_nomprds'=>array());}

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
          if(!$result) {$error='Error acquiring (co-winner) awards company/people data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
            if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
            if(!preg_match('/^the-company$/', $row['comp_url'])) {$cowin_prsn=' <a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
            else {$cowin_prsn=html($row['comp_nm']).$nom_rl;}
            $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['comp_id']]=array('cowin_prsn'=>$cowin_prsn, 'cowincomp_ppl'=>array());
          }

          $sql= "SELECT awrdsid, awrd_id, anprd.awrd_ctgryid, anprd.nomid AS n1, anp.nomid AS n2, anp.nom_compid, anp.nom_ordr, anp.nom_rl, prsn_id, prsn_fll_nm, prsn_url
                FROM awrdnomprds anprd
                INNER JOIN awrd ON anprd.awrdid=awrd_id
                INNER JOIN awrdnoms an1 ON anprd.awrdid=an1.awrdid AND anprd.awrd_ctgryid=an1.awrd_ctgryid AND anprd.nomid=an1.nom_id
                INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
                INNER JOIN awrdnomppl anp ON an2.awrdid=anp.awrdid AND an2.awrd_ctgryid=anp.awrd_ctgryid AND an2.nom_id=anp.nomid
                INNER JOIN prsn ON anp.nom_prsnid=prsn_id
                WHERE (nom_prdid=$awrd_prd_id) AND an1.win_bool=1 AND an2.win_bool=1 AND anp.nom_compid!=0
                AND anp.nomid NOT IN(SELECT nomid FROM awrdnomprds WHERE (nom_prdid=$awrd_prd_id) AND awrdid=anprd.awrdid AND awrd_ctgryid=anprd.awrd_ctgryid)
                GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prsn_id ORDER BY nom_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring (co-winner) awards company people data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
            $cowincomp_prsn='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
            $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['nom_compid']]['cowincomp_ppl'][]=$cowincomp_prsn;
          }

          $sql =  "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, anp2.nomid AS n2, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm
                FROM awrdnomprds anp1
                INNER JOIN awrd ON anp1.awrdid=awrd_id
                INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
                INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
                INNER JOIN awrdnomprds anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid AND an2.nom_id=anp2.nomid
                INNER JOIN prd p ON anp2.nom_prdid=prd_id
                INNER JOIN thtr ON p.thtrid=thtr_id
                WHERE (anp1.nom_prdid=$anp_awrd_prd_id) AND an1.win_bool=1 AND an2.win_bool=1
                AND anp2.nomid NOT IN(SELECT nomid FROM awrdnomprds WHERE (nom_prdid=$awrd_prd_id) AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
                GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring (co-winner) awards production data (productions): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
            $prd_awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_prds'][]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
          }
        }
      }
    }

    $pt_id=html($pt_id);
    include 'playtext.html.php';
  }
?>