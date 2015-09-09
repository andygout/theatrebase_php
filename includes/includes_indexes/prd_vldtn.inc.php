<?php
    $prd_nm=trim(cln($_POST['prd_nm']));
    $prd_sbnm=trim(cln($_POST['prd_sbnm']));
    $mat_list=cln($_POST['mat_list']);
    $pt_list=cln($_POST['pt_list']);
    $prd_frst_dt=cln($_POST['prd_frst_dt']);
    $prd_prss_dt=cln($_POST['prd_prss_dt']);
    $prd_prss_dt_2=cln($_POST['prd_prss_dt_2']);
    $prd_lst_dt=cln($_POST['prd_lst_dt']);
    if(isset($_POST['prd_prss_dt_tbc'])) {$prd_prss_dt_tbc='1';} else {$prd_prss_dt_tbc='0';}
    if(isset($_POST['prd_prv_only'])) {$prd_prv_only='1';} else {$prd_prv_only='0';}
    if($_POST['prd_dts_info']=='1') {$prd_dts_info='1';}
    if($_POST['prd_dts_info']=='2') {$prd_dts_info='2';}
    if($_POST['prd_dts_info']=='3') {$prd_dts_info='3';}
    if($_POST['prd_dts_info']=='4') {$prd_dts_info='4';}
    $prd_prss_wrdng=trim(cln($_POST['prd_prss_wrdng']));
    $prd_tbc_nt=trim(cln($_POST['prd_tbc_nt']));
    $prd_dt_nt=trim(cln($_POST['prd_dt_nt']));
    $thtr_nm=trim(cln($_POST['thtr_nm']));
    $prd_thtr_nt=trim(cln($_POST['prd_thtr_nt']));
    if($_POST['prd_clss']=='1') {$prd_clss='1';}
    if($_POST['prd_clss']=='2') {$prd_clss='2';}
    if($_POST['prd_clss']=='3') {$prd_clss='3';}
    if($_POST['prd_tr']=='1') {$prd_tr='1'; $tr_ov=''; $tr_lg='';}
    if($_POST['prd_tr']=='2') {$prd_tr='2'; $tr_ov='1'; $tr_lg='';}
    if($_POST['prd_tr']=='3') {$prd_tr='3'; $tr_ov=''; $tr_lg='1';}
    $tr_lg_list=cln($_POST['tr_lg_list']);
    if($_POST['prd_coll']=='1') {$prd_coll='1'; $coll_ov=''; $coll_sg='';}
    if($_POST['prd_coll']=='2') {$prd_coll='2'; $coll_ov='1'; $coll_sg='';}
    if($_POST['prd_coll']=='3') {$prd_coll='3'; $coll_ov=''; $coll_sg='1';}
    $coll_sg_list=cln($_POST['coll_sg_list']);
    $rep_list=cln($_POST['rep_list']);
    $prdrn_list=trim(cln($_POST['prdrn_list']));
    $prd_vrsn_list=cln($_POST['prd_vrsn_list']);
    $txt_vrsn_list=cln($_POST['txt_vrsn_list']);
    $ctgry_list=cln($_POST['ctgry_list']);
    $gnr_list=cln($_POST['gnr_list']);
    $ftr_list=cln($_POST['ftr_list']);
    $thm_list=cln($_POST['thm_list']);
    $sttng_list=cln($_POST['sttng_list']);
    $wri_list=cln($_POST['wri_list']);
    $prdcr_list=cln($_POST['prdcr_list']);
    $prf_list=cln($_POST['prf_list']);
    if(isset($_POST['prd_othr_prts'])) {$prd_othr_prts='1';} else {$prd_othr_prts='0';}
    $prd_cst_nt=trim(cln($_POST['prd_cst_nt']));
    $us_list=cln($_POST['us_list']);
    $mscn_list=cln($_POST['mscn_list']);
    $crtv_list=cln($_POST['crtv_list']);
    $prdtm_list=cln($_POST['prdtm_list']);
    $ssn_nm=trim(cln($_POST['ssn_nm']));
    $fstvl_nm=trim(cln($_POST['fstvl_nm']));
    $crs_list=cln($_POST['crs_list']);
    $rvw_list=cln($_POST['rvw_list']);
    $alt_nm_list=cln($_POST['alt_nm_list']);
    $pt=NULL; $coll_wrks=NULL;

    $prd_nm_session=$_POST['prd_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $prd_nm))
    {$errors['prd_nm']='**You must enter a production name.**';}
    elseif(strlen($prd_nm)>255)
    {$errors['prd_nm']='**Production name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $prd_nm))
    {$errors['prd_nm']='**Production name cannot include the following [,,].**';}
    else
    {$prd_url=generateurl($prd_nm); $prd_alph=alph($prd_nm);}

    if(preg_match('/\S+/', $prd_sbnm))
    {if(strlen($prd_sbnm)>255) {$errors['prd_sbnm_excss_lngth']='</br>**Production sub-name is allowed a maximum of 255 characters.**';}}

    include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_pt_vldtn.inc.php';
    //FILE COMPRISES: sbnm / mat_list / txt_vrsn_list / ctgry_list / gnr_list / ftr_list / thm_list / sttng_list / wri_list / alt_nm_list

    if(preg_match('/\S+/', $pt_list))
    {
      if($tr_lg) {$errors['pt_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $pt_nm_yrs=explode(',,', $_POST['pt_list']);
        if(count($pt_nm_yrs)>250)
        {$errors['pt_list_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $pt_empty_err_arr=array(); $pt_hsh_excss_err_arr=array(); $pt_yr_err_arr=array();
          $pt_yr_frmt_err_arr=array(); $pt_hsh_err_arr=array(); $pt_hyphn_excss_err_arr=array();
          $pt_sffx_err_arr=array(); $pt_hyphn_err_arr=array(); $pt_dplct_arr=array();
          $pt_url_err_arr=array(); $pt_coll_wrks_err_arr=array();
          foreach($pt_nm_yrs as $pt_nm_yr)
          {
            $pt_errors=0;

            $pt_nm_yr=trim($pt_nm_yr);
            if(!preg_match('/\S+/', $pt_nm_yr))
            {
              $pt_empty_err_arr[]=$pt_nm_yr;
              if(count($pt_empty_err_arr)==1) {$errors['pt_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['pt_empty']='</br>**There are '.count($pt_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($pt_nm_yr, '--')>1)
              {
                $pt_errors++; $pt_sffx_num='0'; $pt_hyphn_excss_err_arr[]=$pt_nm_yr;
                $errors['pt_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per playtext. Please amend: '.html(implode(' / ', $pt_hyphn_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*--.+$/', $pt_nm_yr))
              {
                list($pt_nm_yr_no_sffx, $pt_sffx_num)=explode('--', $pt_nm_yr);
                $pt_nm_yr_no_sffx=trim($pt_nm_yr_no_sffx); $pt_sffx_num=trim($pt_sffx_num);

                if(!preg_match('/^[1-9][0-9]{0,1}$/', $pt_sffx_num))
                {
                  $pt_errors++; $pt_sffx_num='0'; $pt_sffx_err_arr[]=$pt_nm_yr;
                  $errors['pt_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $pt_sffx_err_arr)).'**';
                }
                $pt_nm_yr=$pt_nm_yr_no_sffx;
              }
              elseif(substr_count($pt_nm_yr, '--')==1)
              {$pt_errors++; $pt_sffx_num='0'; $pt_hyphn_err_arr[]=$pt_nm_yr;
              $errors['pt_hyphn']='</br>**Playtext suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $pt_hyphn_err_arr)).'**';}
              else
              {$pt_sffx_num='0';}

              if($pt_sffx_num) {$pt_sffx_rmn=' ('.romannumeral($pt_sffx_num).')';} else {$pt_sffx_rmn='';}

              if(substr_count($pt_nm_yr, '##')>1) {$pt_errors++; $pt_hsh_excss_err_arr[]=$pt_nm_yr; $errors['pt_hsh_excss']='</br>**You may only use [##] for year written assignment once per playtext. Please amend: '.html(implode(' / ', $pt_hsh_excss_err_arr)).'.**';}
              elseif(preg_match('/^\S+.*##.*\S+$/', $pt_nm_yr))
              {
                list($pt_nm, $pt_yr)=explode('##', $pt_nm_yr);
                $pt_nm=trim($pt_nm); $pt_yr=trim($pt_yr);

                if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}(\s*;;\s*(c)?(-)?[1-9][0-9]{0,3})?$/', $pt_yr))
                {
                  if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $pt_yr))
                  {
                    list($pt_yr_strtd, $pt_yr_wrttn)=explode(';;', $pt_yr);
                    $pt_yr_strtd=trim($pt_yr_strtd); $pt_yr_wrttn=trim($pt_yr_wrttn);

                    if(preg_match('/^c(-)?/', $pt_yr_strtd)) {$pt_yr_strtd=preg_replace('/^c(.+)$/', '$1', $pt_yr_strtd); $pt_yr_strtd_c='1';}
                    else {$pt_yr_strtd_c=NULL;}

                    if(preg_match('/^c(-)?/', $pt_yr_wrttn)) {$pt_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $pt_yr_wrttn); $pt_yr_wrttn_c='1';}
                    else {$pt_yr_wrttn_c=NULL;}

                    if($pt_yr_strtd >= $pt_yr_wrttn) {$pt_errors++; $pt_yr_err_arr[]=$pt_nm_yr; $errors['pt_yr']='</br>**Playtext year started must be earlier than year written. Please amend: '.html(implode(' / ', $pt_yr_err_arr)).'.**';}
                  }
                  else
                  {
                    $pt_yr_strtd_c=NULL; $pt_yr_strtd=NULL; $pt_yr_wrttn=$pt_yr;
                    if(preg_match('/^c(-)?/', $pt_yr_wrttn)) {$pt_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $pt_yr_wrttn); $pt_yr_wrttn_c='1';}
                    else {$pt_yr_wrttn_c=NULL;}
                  }

                  if($pt_yr_strtd)
                  {
                    if(preg_match('/^-/', $pt_yr_strtd)) {$pt_yr_strtd=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $pt_yr_strtd); if(!preg_match('/^-/', $pt_yr_wrttn)) {$pt_yr_strtd .= ' BCE';}}
                    $pt_yr_strtd .= '-';
                    if($pt_yr_strtd_c) {$pt_yr_strtd='c.'.$pt_yr_strtd;}
                  }
                  else {$pt_yr_strtd='';}

                  if(preg_match('/^-/', $pt_yr_wrttn)) {$pt_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE", $pt_yr_wrttn);}
                  if($pt_yr_wrttn_c) {$pt_yr_wrttn='c.'.$pt_yr_wrttn;}

                  $pt_nm_yr=$pt_nm.' ('.$pt_yr_strtd.$pt_yr_wrttn.')'; $pt_url=generateurl($pt_nm_yr.$pt_sffx_rmn);
                  $pt_dplct_arr[]=$pt_url; if(count(array_unique($pt_dplct_arr))<count($pt_dplct_arr)) {$errors['pt_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}
                  if(strlen($pt_nm_yr)>255 || strlen($pt_url)>255) {$pt_errors++; $errors['pt_nm_yr_excss_lngth']='</br>**Playtext name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                }
                else {$pt_errors++; $pt_yr_frmt_err_arr[]=$pt_nm_yr; $errors['pt_yr_frmt']='</br>**Playtexts must be assigned a valid year (or years). Please amend: '.html(implode(' / ', $pt_yr_frmt_err_arr)).'.**';}
              }
              else {$pt_errors++; $pt_nm=$pt_nm_yr; $pt_hsh_err_arr[]=$pt_nm_yr; $errors['pt_hsh']='</br>**You must assign a playtext year in the correct format to the following using [##]: '.html(implode(' / ', $pt_hsh_err_arr)).'.**';}

              if($pt_errors==0)
              {
                $pt_nm_yr_cln=cln($pt_nm_yr); $pt_sffx_num_cln=cln($pt_sffx_num); $pt_url_cln=cln($pt_url);
                $sql= "SELECT pt_nm, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn
                      FROM pt
                      WHERE NOT EXISTS (SELECT 1 FROM pt WHERE pt_nm_yr='$pt_nm_yr_cln' AND pt_sffx_num='$pt_sffx_num_cln')
                      AND pt_url='$pt_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing playtext URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['pt_yr_strtd_c']) {$pt_yr_strtd_c='c';} else {$pt_yr_strtd_c='';}
                  if($row['pt_yr_strtd']) {$pt_yr_strtd=$row['pt_yr_strtd'].';;';} else {$pt_yr_strtd='';}
                  if($row['pt_yr_wrttn_c']) {$pt_yr_wrttn_c='c';} else {$pt_yr_wrttn_c='';}
                  if($row['pt_sffx_num']) {$pt_sffx_num='--'.$row['pt_sffx_num'];} else {$pt_sffx_num='';}
                  $pt_url_err_arr[]=$row['pt_nm'].'##'.$pt_yr_strtd_c.$pt_yr_strtd.$pt_yr_wrttn_c.$row['pt_yr_wrttn'].$pt_sffx_num;
                  if(count($pt_url_err_arr)==1)
                  {$errors['pt_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $pt_url_err_arr)).'?**';}
                  else
                  {$errors['pt_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $pt_url_err_arr)).'?**';}
                }
                else
                {
                  $sql= "SELECT pt_nm, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn
                        FROM pt
                        WHERE pt_url='$pt_url_cln' AND pt_coll=2";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing playtext URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    if($row['pt_yr_strtd_c']) {$pt_yr_strtd_c='c';} else {$pt_yr_strtd_c='';}
                    if($row['pt_yr_strtd']) {$pt_yr_strtd=$row['pt_yr_strtd'].';;';} else {$pt_yr_strtd='';}
                    if($row['pt_yr_wrttn_c']) {$pt_yr_wrttn_c='c';} else {$pt_yr_wrttn_c='';}
                    if($row['pt_sffx_num']) {$pt_sffx_num='--'.$row['pt_sffx_num'];} else {$pt_sffx_num='';}
                    $pt_coll_wrks_err_arr[]=$row['pt_nm'].'##'.$pt_yr_strtd_c.$pt_yr_strtd.$pt_yr_wrttn_c.$row['pt_yr_wrttn'].$pt_sffx_num;
                    if(count($pt_coll_wrks_err_arr)==1)
                    {$errors['pt_coll_wrks']='</br>**The following is a collected works and cannot be assigned to productions: '.html(implode(' / ', $pt_coll_wrks_err_arr)).'**';}
                    else
                    {$errors['pt_coll_wrks']='</br>**The following are collected works and cannot be assigned to productions: '.html(implode(' / ', $pt_coll_wrks_err_arr)).'**';}
                  }
                }
              }
            }
          }
        }
      }
    }

    if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $prd_frst_dt)) {$errors['prd_frst_dt']='**You must enter a valid First Performance date in the prescribed format.**'; $prd_frst_dt=NULL;}
    else
    {
      list($prd_frst_dt_YYYY, $prd_frst_dt_MM, $prd_frst_dt_DD)=explode('-', $prd_frst_dt);
      if(!checkdate((int)$prd_frst_dt_MM, (int)$prd_frst_dt_DD, (int)$prd_frst_dt_YYYY)) {$errors['prd_frst_dt']='**You must enter a valid First Performance date.**'; $prd_frst_dt=NULL;}
    }

    if($prd_prss_dt)
    {
      if($tr_lg) {$errors['prd_prss_dt']='**This field must be empty if tour leg button is applied.**'; $prd_prss_dt=NULL;}
      else
      {
        if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $prd_prss_dt)) {$errors['prd_prss_dt']='**You must enter a valid Press Performance date in the prescribed format or leave empty.**'; $prd_prss_dt=NULL;}
        else
        {
          list($prd_prss_dt_YYYY, $prd_prss_dt_MM, $prd_prss_dt_DD)=explode('-', $prd_prss_dt);
          if(!checkdate((int)$prd_prss_dt_MM, (int)$prd_prss_dt_DD, (int)$prd_prss_dt_YYYY)) {$errors['prd_prss_dt']='**You must enter a valid Press Performance date or leave empty.**'; $prd_prss_dt=NULL;}
          else
          {
            if($prd_prss_dt_tbc) {$errors['prd_prss_dt_tbc']='**Press Performance date must be left empty if this box is checked.**'; $prd_prss_dt=NULL;}
            if($prd_prv_only) {$errors['prd_prv_only']='**Press Performance date must be left empty if this box is checked.**'; $prd_prss_dt=NULL;}
          }
        }
      }
    }
    else {$prd_prss_dt=NULL;}

    if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $prd_lst_dt)) {$errors['prd_lst_dt']='**You must enter a valid Last Performance date in the prescribed format.**'; $prd_lst_dt=NULL;}
    else
    {
      list($prd_lst_dt_YYYY, $prd_lst_dt_MM, $prd_lst_dt_DD)=explode('-', $prd_lst_dt);
      if(!checkdate((int)$prd_lst_dt_MM, (int)$prd_lst_dt_DD, (int)$prd_lst_dt_YYYY)) {$errors['prd_lst_dt']='**You must enter a valid Last Performance date.**'; $prd_lst_dt=NULL;}
    }

    if($prd_frst_dt && $prd_lst_dt && $prd_frst_dt>$prd_lst_dt)
    {
      $errors['prd_frst_dt']='**Must be earlier than or equal to Last Performance date.**';
      $errors['prd_lst_dt']='**Must be later than or equal to First Performance date.**';
    }

    if($prd_prss_dt && ($prd_frst_dt && $prd_prss_dt<$prd_frst_dt) || ($prd_lst_dt && $prd_prss_dt>$prd_lst_dt))
    {$errors['prd_prss_dt']='**Must be later than or equal to First Performance and earlier than or equal to Last Performance.**'; $prd_prss_dt=NULL;}

    if($prd_prss_dt_2)
    {
      if($tr_lg) {$errors['prd_prss_dt_2']='**This field must be empty if tour leg button is applied.**'; $prd_prss_dt_2=NULL;}
      elseif(!$prd_prss_dt) {$errors['prd_prss_dt_2']='**This field must be empty unless a valid Press Performance date is given.**'; $prd_prss_dt_2=NULL;}
      else
      {
        if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $prd_prss_dt_2)) {$errors['prd_prss_dt_2']='**You must enter a valid Second Press Performance date in the prescribed format or leave empty.**'; $prd_prss_dt_2=NULL;}
        else
        {
          list($prd_prss_dt_2_YYYY, $prd_prss_dt_2_MM, $prd_prss_dt_2_DD)=explode('-', $prd_prss_dt_2);
          if(!checkdate((int)$prd_prss_dt_2_MM, (int)$prd_prss_dt_2_DD, (int)$prd_prss_dt_2_YYYY)) {$errors['prd_prss_dt_2']='**You must enter a valid Second Press Performance date or leave empty.**'; $prd_prss_dt_2=NULL;}
        }
      }
    }
    else {$prd_prss_dt_2=NULL;}

    if($prd_prss_dt_2 && $prd_prss_dt_2<=$prd_prss_dt) {$errors['prd_prss_dt_2']='**Must be later than Press Performance date.**';}
    elseif($prd_prss_dt_2 && $prd_prss_dt_2>$prd_lst_dt) {$errors['prd_prss_dt_2']='**Must be earlier than or equal to Last Performance.**';}

    if($prd_prss_wrdng)
    {
      if($tr_lg) {$errors['prd_prss_wrdng']='**This field must be empty if tour leg button is applied.**';}
      elseif(strlen($prd_prss_wrdng)>20) {$errors['prd_prss_wrdng']='</br>**Press date wording is allowed a maximum of 20 characters.**';}
    }

    if($prd_dts_info!=='4')
    {if(preg_match('/\S+/', $_POST['prd_tbc_nt'])) {$errors['prd_tbc_nt']='**This field must be left empty unless Dates TBC has been checked.**';}}
    else
    {if(strlen($prd_tbc_nt)>14) {$errors['prd_tbc_nt']='**Dates TBC note is allowed a maximum of 14 characters.**';}}

    if(strlen($prd_dt_nt)>255) {$errors['prd_dt_nt_excss_lngth']='</br>**Dates notes are allowed a maximum of 255 characters.**';}

    if(!preg_match('/\S+/', $thtr_nm))
    {$errors['thtr_nm']='**You must enter a theatre name.**';}
    else
    {
      $thtr=$thtr_nm;
      $thtr_errors=0;
      if(substr_count($thtr, '--')>1) {$thtr_errors++; $thtr_sffx_num='0'; $errors['thtr_hyphn_excss']='</br>**You may only use [--] for theatre suffix assignment once.**';}
      elseif(preg_match('/^\S+.*--.+$/', $thtr))
      {
        list($thtr, $thtr_sffx_num)=explode('--', $thtr); $thtr=trim($thtr); $thtr_sffx_num=trim($thtr_sffx_num);
        if(!preg_match('/^[1-9][0-9]{0,1}$/', $thtr_sffx_num)){$thtr_errors++; $thtr_sffx_num='0'; $errors['thtr_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend.**';}
      }
      elseif(substr_count($thtr, '--')==1) {$thtr_errors++; $thtr_sffx_num='0'; $thtr_sffx_rmn=''; $errors['thtr_hyphn']='</br>**Venue suffix assignation must use [--] in the correct format.**';}
      else {$thtr_sffx_num='0';}

      if($thtr_sffx_num) {$thtr_sffx_rmn=' ('.romannumeral($thtr_sffx_num).')';} else {$thtr_sffx_rmn='';}

      if(substr_count($thtr, '::')>1) {$thtr_errors++; $errors['thtr_cln']='</br>**You may only use [::] once per theatre-location coupling.**'; $thtr_lctn=''; $thtr_lctn_dsply='';}
      elseif(preg_match('/\S+.*::.*\S+/', $thtr)) {list($thtr, $thtr_lctn)=explode('::', $thtr); $thtr=trim($thtr); $thtr_lctn=trim($thtr_lctn); $thtr_lctn_dsply=' ('.$thtr_lctn.')';}
      elseif(substr_count($thtr, '::')==1) {$thtr_errors++; $errors['thtr_cln']='</br>**Theatre location assignation must use [::] in the correct format.**'; $thtr_lctn=''; $thtr_lctn_dsply='';}
      else {$thtr_lctn=''; $thtr_lctn_dsply='';}

      if(substr_count($thtr, ';;')>1) {$thtr_errors++; $errors['thtr_smcln']='</br>**You may only use [;;] once per theatre-subtheatre coupling.**'; $sbthtr_nm=''; $sbthtr_nm_dsply='';}
      elseif(preg_match('/\S+.*;;.*\S+/', $thtr)) {list($thtr, $sbthtr_nm)=explode(';;', $thtr); $thtr=trim($thtr); $sbthtr_nm=trim($sbthtr_nm); $sbthtr_nm_dsply=': '.$sbthtr_nm;}
      elseif(substr_count($thtr, ';;')==1) {$thtr_errors++; $errors['thtr_smcln']='</br>**Theatre subtheatre assignation must use [;;] in the correct format.**'; $sbthtr_nm=''; $sbthtr_nm_dsply='';}
      else {$sbthtr_nm=''; $sbthtr_nm_dsply='';}

      $thtr_fll_nm=$thtr.$sbthtr_nm_dsply.$thtr_lctn_dsply;
      $thtr_url=generateurl($thtr_fll_nm.$thtr_sffx_rmn);

      if(strlen($thtr_fll_nm)>255 || strlen($thtr_url)>255)
      {$thtr_errors++; $errors['thtr_excss_lngth']='</br>**Theatre name and its URL are allowed a maximum of 255 characters each.**';}

      if($thtr_errors==0)
      {
        $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
              FROM thtr
              WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_nm='$thtr' AND sbthtr_nm='$sbthtr_nm' AND thtr_lctn='$thtr_lctn')
              AND thtr_fll_nm='$thtr_fll_nm' AND thtr_sffx_num='$thtr_sffx_num'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for theatre with assigned components: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        if(mysqli_num_rows($result)>0)
        {
          if($row['sbthtr_nm']) {$thtr_cmpstn_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$thtr_cmpstn_error_sbthtr_nm='';}
          if($row['thtr_lctn']) {$thtr_cmpstn_error_thtr_lctn='::'.$row['thtr_lctn'];} else {$thtr_cmpstn_error_thtr_lctn='';}
          if($row['thtr_sffx_num']) {$thtr_cmpstn_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$thtr_cmpstn_error_sffx_num='';}
          $thtr_cmpstn_error=$row['thtr_nm'].$thtr_cmpstn_error_sbthtr_nm.$thtr_cmpstn_error_thtr_lctn.$thtr_cmpstn_error_sffx_num;
          $errors['thtr_cmpstn']='</br>**Theatre does not adhere to its correct component assignation: '.html($thtr_cmpstn_error).'.**';
        }

        $sql= "SELECT thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
              FROM thtr
              WHERE NOT EXISTS (SELECT 1 FROM thtr WHERE thtr_fll_nm='$thtr_fll_nm' AND thtr_sffx_num='$thtr_sffx_num')
              AND thtr_url='$thtr_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existing theatre URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        if(mysqli_num_rows($result)>0)
        {
          if($row['sbthtr_nm']) {$thtr_url_error_sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$thtr_url_error_sbthtr_nm='';}
          if($row['thtr_lctn']) {$thtr_url_error_thtr_lctn='::'.$row['thtr_lctn'];} else {$thtr_url_error_thtr_lctn='';}
          if($row['thtr_sffx_num']) {$thtr_url_error_sffx_num='--'.$row['thtr_sffx_num'];} else {$thtr_url_error_sffx_num='';}
          $thtr_url_error=$row['thtr_nm'].$thtr_url_error_sbthtr_nm.$thtr_url_error_thtr_lctn.$thtr_url_error_sffx_num;
          $errors['thtr_url']='</br>**Duplicate URL exists. Did you mean to type: '.html($thtr_url_error).'?**';
        }
        else
        {
          $sql="SELECT 1 FROM thtr WHERE thtr_url='$thtr_url' AND thtr_tr_ov='1'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking if theatre is a tour overview: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          $row=mysqli_fetch_array($result);
          if(mysqli_num_rows($result)>0) {if(!$tr_ov) {$errors['thtr_tr_ov']='**Given theatre is a tour overview and production must also be assigned as such.**';}}
        }
      }
    }

    if(strlen($prd_thtr_nt)>255) {$errors['prd_thtr_nt_excss_lngth']='</br>**Theatre notes are allowed a maximum of 255 characters.**';}

    if(preg_match('/\S+/', $tr_lg_list))
    {
      if(!$tr_ov) {$errors['tr_lg_entry_ov_unchckd']='</br>**Tour overview button must be applied before you are able to assign tour legs.**';}
      else
      {
        $tr_lg_ids=explode(',,', $_POST['tr_lg_list']);
        $tr_lg_empty_err_arr=array(); $tr_lg_nonnmrcl_err_arr=array(); $tr_lg_dplct_arr=array();
        $tr_lg_nonexst_err_arr=array(); $tr_lg_assoc_err_arr=array(); $tr_lg_prd_nm_mtch_err_arr=array();
        $tr_lg_prd_nm_mtch_err_arr=array(); $tr_lg_prd_unchckd_err_arr=array();
        foreach($tr_lg_ids as $tr_lg_id)
        {
          $tr_lg_id=trim($tr_lg_id);
          if(!preg_match('/\S+/', $tr_lg_id))
          {
            $tr_lg_empty_err_arr[]=$tr_lg_id;
            if(count($tr_lg_empty_err_arr)==1) {$errors['tr_lg_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['tr_lg_empty']='</br>**There are '.count($tr_lg_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          elseif(!preg_match('/^[1-9][0-9]*$/', $tr_lg_id))
          {
            $tr_lg_nonnmrcl_err_arr[]=$tr_lg_id;
            $errors['tr_lg_nonnmrcl']='</br>**The following production ids have not been assigned properly (must be positive integers): '.html(implode(' / ', $tr_lg_nonnmrcl_err_arr)).'.**';
          }
          else
          {
            $tr_lg_dplct_arr[]=$tr_lg_id;
            if(count(array_unique($tr_lg_dplct_arr))<count($tr_lg_dplct_arr))
            {$errors['tr_lg_dplct']='</br>**There are duplicate ids within the array.**';}

            $tr_lg_id_cln=cln($tr_lg_id);

            $sql="SELECT 1 FROM prd WHERE prd_id='$tr_lg_id_cln'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing production id (against tour leg id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $tr_lg_nonexst_err_arr[]=$tr_lg_id;
              $errors['tr_lg_nonexst']='</br>**The following are not existing production ids: '.html(implode(' / ', $tr_lg_nonexst_err_arr)).'.**';
            }
            else
            {
              if($tr_lg_id==$prd_id)
              {$errors['tr_lg_prd_id_mtch']='</br>**You cannot assign this production as a tour leg of itself: '.html($prd_id).'.**';}
              else
              {
                $sql="SELECT tr_ov FROM prd WHERE prd_id='$tr_lg_id_cln' AND tr_ov IS NOT NULL";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing tour overview id (against tour leg id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0 && $row['tr_ov']!==$prd_id)
                {
                  $tr_lg_assoc_err_arr[]=$tr_lg_id;
                  $errors['tr_lg_assoc']='</br>**Please amend the following to production ids with no existing tour associations: '.html(implode(' / ', $tr_lg_assoc_err_arr)).'.**';
                }

                $sql= "SELECT prd_nm, prd_frst_dt, prd_lst_dt, prd_tr, prd_clss
                      FROM prd
                      WHERE prd_id='$tr_lg_id_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for production name and tour leg info (called from entered tour leg id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($row['prd_nm']!==$prd_nm) {$tr_lg_prd_nm_mtch_err_arr[]=$tr_lg_id; $errors['tr_lg_prd_nm_mtch']='</br>**Please amend the following to production ids that match the name of this production: '.html(implode(' / ', $tr_lg_prd_nm_mtch_err_arr)).'.**';}
                if($row['prd_frst_dt']<$prd_frst_dt || $row['prd_lst_dt']>$prd_lst_dt) {$tr_lg_prd_dts_mtch_err_arr[]=$tr_lg_id; $errors['tr_lg_prd_dts_mtch']='</br>**Please amend the following to production ids where the dates fall within those of this production: '.html(implode(' / ', $tr_lg_prd_dts_mtch_err_arr)).'.**';}
                if($row['prd_tr']!=='3') {$tr_lg_prd_unchckd_err_arr[]=$tr_lg_id; $errors['tr_lg_prd_unchckd']='</br>**Please amend the following to production ids that have been assigned as tour leg: '.html(implode(' / ', $tr_lg_prd_unchckd_err_arr)).'.**';}
                if($row['prd_clss']!==$prd_clss) {$tr_lg_prd_clss_mtch_err_arr[]=$tr_lg_id; $errors['tr_lg_prd_clss_mtch']='</br>**Please amend the following to production ids that have the same production class as this production: '.html(implode(' / ', $tr_lg_prd_clss_mtch_err_arr)).'.**';}
              }
            }
          }
        }
      }
    }

    if($edit)
    {
      $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, p2.prd_lst_dt, p2.prd_clss
            FROM prd p1
            INNER JOIN prd p2 ON p1.tr_ov=p2.prd_id
            WHERE p1.prd_id='$prd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing tour overview associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        $tr_ov_prd='<a href="/production/'.html($row['prd_id']).'/'.html($row['prd_url']).'" target="/production/'.html($row['prd_id']).'/'.html($row['prd_url']).'">'.html($row['prd_nm']).'</a>';
        if($row['prd_nm']!==$prd_nm) {$errors['tr_ov_prd_nm_mtch']="</br>**Production has an existing tour overview association (with ".$tr_ov_prd.") and must match its name (else have its association removed via that production's edit page).**";}
        if($prd_frst_dt<$row['prd_frst_dt']) {$errors['tr_ov_prd_frst_dt_mtch']="</br>**Production has an existing tour overview association (with ".$tr_ov_prd.") and its first date must not come before that of the overview (else have its association removed via that production's edit page).**";}
        if($prd_lst_dt>$row['prd_lst_dt']) {$errors['tr_ov_prd_lst_dt_mtch']="</br>**Production has an existing tour overview association (with ".$tr_ov_prd.") and its last date must not come after that of the overview (else have its association removed via that production's edit page).**";}
        if(!$tr_lg) {$errors['tr_ov_assoc']="</br>**Production has an existing tour overview association (with ".$tr_ov_prd.") and must remain as a tour leg (else have its association removed via the overview's edit page).**";}
        if($row['prd_clss']!==$prd_clss) {$errors['tr_ov_prd_clss_mtch']="</br>**Production has an existing tour overview association (with ".$tr_ov_prd.") and must have the same production class as that of the overview (else have its association removed via the overview's edit page).**";}
      }
    }

    if(preg_match('/\S+/', $coll_sg_list))
    {
      if(!$coll_ov)
      {$errors['coll_sg_entry_ov_unchckd']='</br>**Collection overview button must be applied before you are able to assign collection segments.**';}
      else
      {
        $coll_sg_sbhdr_ids=explode('@@', $_POST['coll_sg_list']);
        if(count($coll_sg_sbhdr_ids)>250) {$errors['coll_sg_sbhdr_id_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $coll_sg_sbhdr_id_empty_err_arr=array(); $coll_sg_eql_excss_err_arr=array(); $coll_sg_eql_err_arr=array();
          $coll_sg_sbhdr_err_arr=array(); $coll_sg_empty_err_arr=array(); $coll_sg_nonnmrcl_err_arr=array();
          $coll_sg_dplct_arr=array(); $coll_sg_nonexst_err_arr=array(); $coll_sg_assoc_err_arr=array();
          $coll_sg_prd_dts_mtch_err_arr=array(); $coll_sg_prd_unchckd_err_arr=array(); $coll_sg_prd_clss_mtch_err_arr=array();
          foreach($coll_sg_sbhdr_ids as $coll_sg_sbhdr_id)
          {
            $coll_sg_sbhdr_id = trim($coll_sg_sbhdr_id);
            if(!preg_match('/\S+/', $coll_sg_sbhdr_id))
            {
              $coll_sg_sbhdr_id_empty_err_arr[]=$coll_sg_sbhdr_id;
              if(count($coll_sg_sbhdr_id_empty_err_arr)==1) {$errors['coll_sg_sbhdr_id_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
              else {$errors['coll_sg_sbhdr_id_empty']='</br>**There are '.count($coll_sg_sbhdr_id_empty_err_arr).' empty entries in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($coll_sg_sbhdr_id, '==')>1)
              {
                $coll_sg_id_list='0'; $coll_sg_eql_excss_err_arr[]=$coll_sg_sbhdr_id;
                $errors['coll_sg_eql_excss']='</br>**You may only use [==] for subheader assignment once per collection segment array. Please amend: '.html(implode(' / ', $coll_sg_eql_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*==.*\S$/', $coll_sg_sbhdr_id))
              {
                list($coll_sbhdr, $coll_sg_id_list)=explode('==', $coll_sg_sbhdr_id);
                $coll_sbhdr=trim($coll_sbhdr); $coll_sg_id_list=trim($coll_sg_id_list);
                if(strlen($coll_sbhdr)>255) {$errors['coll_sbhdr_excss_lngth']='</br>**Collection segment subheaders are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
              }
              elseif(substr_count($coll_sg_sbhdr_id, '==')==1)
              {$coll_sg_id_list='0'; $coll_sg_eql_err_arr[]=$coll_sg_sbhdr_id;
              $errors['coll_sg_eql']='</br>**Collection segment subheader assignation must use [==] in the correct format. Please amend: '.html(implode(' / ', $coll_sg_eql_err_arr)).'**';}
              else
              {
                if(count($coll_sg_sbhdr_ids)>1) {$coll_sg_sbhdr_err_arr[]=$coll_sg_sbhdr_id; $errors['coll_sg_sbhdr']='</br>**If more than one subdivision is created, subheaders must be assigned to each. Please amend: '.html(implode(' / ', $coll_sg_sbhdr_err_arr)).'**';}
                $coll_sg_id_list=$coll_sg_sbhdr_id;
              }

              if($coll_sg_id_list)
              {
                $coll_sg_ids=explode(',,', $coll_sg_id_list);
                if(count($coll_sg_ids)>250)
                {$errors['coll_sg_list_array_excss']='**Maximum of 250 entries allowed.**';}
                else
                {
                  foreach($coll_sg_ids as $coll_sg_id)
                  {
                    $coll_sg_id=trim($coll_sg_id);
                    if(!preg_match('/\S+/', $coll_sg_id))
                    {
                      $coll_sg_empty_err_arr[]=$coll_sg_id;
                      if(count($coll_sg_empty_err_arr)==1) {$errors['coll_sg_empty']='</br>**There is 1 empty entry in the segment arrays (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                      else {$errors['coll_sg_empty']='</br>**There are '.count($coll_sg_empty_err_arr).' empty entries in the segment arrays (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                    }
                    elseif(!preg_match('/^[1-9][0-9]*$/', $coll_sg_id))
                    {
                      $coll_sg_nonnmrcl_err_arr[]=$coll_sg_id;
                      $errors['coll_sg_nonnmrcl']='</br>**The following production ids have not been assigned properly (must be positive integers): '.html(implode(' / ', $coll_sg_nonnmrcl_err_arr)).'.**';
                    }
                    else
                    {
                      $coll_sg_dplct_arr[]=$coll_sg_id;
                      if(count(array_unique($coll_sg_dplct_arr))<count($coll_sg_dplct_arr))
                      {$errors['coll_sg_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

                      $coll_sg_id_cln=cln($coll_sg_id);

                      $sql="SELECT 1 FROM prd WHERE prd_id='$coll_sg_id_cln' LIMIT 1";
                      $result=mysqli_query($link, $sql);
                      if(!$result) {$error='Error checking for existing production id (against collection segment id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                      if(mysqli_num_rows($result)==0)
                      {
                        $coll_sg_nonexst_err_arr[]=$coll_sg_id;
                        $errors['coll_sg_nonexst']='</br>**The following are not existing production ids: '.html(implode(' / ', $coll_sg_nonexst_err_arr)).'.**';
                      }
                      else
                      {
                        if($coll_sg_id==$prd_id)
                        {$errors['coll_sg_prd_id_mtch']='</br>**You cannot assign this production as a collection segment of itself: '.html($prd_id).'.**';}
                        else
                        {
                          $sql="SELECT coll_ov FROM prd WHERE prd_id='$coll_sg_id_cln' AND coll_ov IS NOT NULL";
                          $result=mysqli_query($link, $sql);
                          if(!$result) {$error='Error checking for existing production id (against collection segment id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                          $row=mysqli_fetch_array($result);
                          if(mysqli_num_rows($result)>0 && $row['coll_ov']!==$prd_id)
                          {
                            $coll_sg_assoc_err_arr[]=$coll_sg_id;
                            $errors['coll_sg_assoc']='</br>**Please amend the following to production ids with no existing collection associations: '.html(implode(' / ', $coll_sg_assoc_err_arr)).'.**';
                          }

                          $sql="SELECT prd_frst_dt, prd_lst_dt, prd_coll, prd_clss FROM prd WHERE prd_id='$coll_sg_id_cln'";
                          $result=mysqli_query($link, $sql);
                          if(!$result) {$error='Error checking for production name and collection segment info (called from entered collection segment id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                          $row=mysqli_fetch_array($result);
                          if($row['prd_frst_dt']<$prd_frst_dt || $row['prd_lst_dt']>$prd_lst_dt) {$coll_sg_prd_dts_mtch_err_arr[]=$coll_sg_id; $errors['coll_sg_prd_dts_mtch']='</br>**Please amend the following to production ids where the dates fall within those of this production: '.html(implode(' / ', $coll_sg_prd_dts_mtch_err_arr)).'.**';}
                          if($row['prd_coll']!=='3') {$coll_sg_prd_unchckd_err_arr[]=$coll_sg_id; $errors['coll_sg_prd_unchckd']='</br>**Please amend the following to production ids that have been assigned as collection segment: '.html(implode(' / ', $coll_sg_prd_unchckd_err_arr)).'.**';}
                          if($row['prd_clss']!==$prd_clss) {$coll_sg_prd_clss_mtch_err_arr[]=$coll_sg_id; $errors['coll_sg_prd_clss_mtch']='</br>**Please amend the following to production ids that have the same production class as this production: '.html(implode(' / ', $coll_sg_prd_clss_mtch_err_arr)).'.**';}
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

    if($edit)
    {
      $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, p2.prd_frst_dt, p2.prd_lst_dt, p2.prd_clss
            FROM prd p1
            INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id
            WHERE p1.prd_id='$prd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing collection overview associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {
        $coll_ov_prd='<a href="/production/'.html($row['prd_id']).'/'.html($row['prd_url']).'" target="/production/'.html($row['prd_id']).'/'.html($row['prd_url']).'">'.html($row['prd_nm']).'</a>';
        if($prd_frst_dt<$row['prd_frst_dt']) {$errors['coll_ov_prd_frst_dt_mtch']="</br>**Production has an existing collection overview association (with ".$coll_ov_prd.") and its first date must not come before that of the overview (else have its association removed via that production's edit page).**";}
        if($prd_lst_dt>$row['prd_lst_dt']) {$errors['coll_ov_prd_lst_dt_mtch']="</br>**Production has an existing collection overview association (with ".$coll_ov_prd.") and its last date must not come after that of the overview (else have its association removed via that production's edit page).**";}
        if(!$coll_sg) {$errors['coll_ov_assoc']="</br>**Production has an existing collection overview association (with ".$coll_ov_prd.") and must remain as a collection segment (else have its association removed via the overview's edit page).**";}
        if($row['prd_clss']!==$prd_clss) {$errors['coll_ov_prd_clss_mtch']="</br>**Production has an existing collection overview association (with ".$coll_ov_prd.") and must have the same production class as that of the overview (else have its association removed via the overview's edit page).**";}
      }
    }

    if(preg_match('/\S+/', $rep_list))
    {
      $rep_ids=explode(',,', $_POST['rep_list']);
      $rep_empty_err_arr=array(); $rep_nonnmrcl_err_arr=array(); $rep_dplct_arr=array(); $rep_nonexst_err_arr=array();
      foreach($rep_ids as $rep_id)
      {
        $rep_id=trim($rep_id);
        if(!preg_match('/\S+/', $rep_id))
        {
          $rep_empty_err_arr[]=$rep_id;
          if(count($rep_empty_err_arr)==1) {$errors['rep_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          else {$errors['rep_empty']='</br>**There are '.count($rep_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
        }
        elseif(!preg_match('/^[1-9][0-9]*$/', $rep_id))
        {
          $rep_nonnmrcl_err_arr[]=$rep_id;
          $errors['rep_nonnmrcl']='</br>**The following production ids have not been assigned properly (must be positive integers): '.html(implode(' / ', $rep_nonnmrcl_err_arr)).'.**';
        }
        else
        {
          $rep_dplct_arr[]=$rep_id;
          if(count(array_unique($rep_dplct_arr))<count($rep_dplct_arr))
          {$errors['rep_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

          $rep_id_cln=cln($rep_id);

          $sql="SELECT 1 FROM prd WHERE prd_id='$rep_id_cln' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existing production id (against rep id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $rep_nonexst_err_arr[]=$rep_id;
            $errors['rep_nonexst']='</br>**The following are not existing production ids: '.html(implode(' / ', $rep_nonexst_err_arr)).'.**';
          }
          else
          {
            if($rep_id==$prd_id)
            {$errors['rep_prd_id_mtch']='</br>**You cannot assign this production as a collection segment of itself: '.html($prd_id).'.**';}
          }
        }
      }
    }

    if(preg_match('/\S+/', $prdrn_list))
    {
      if($tr_lg) {$errors['prdrn_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $prdrn_ids=explode(',,', $_POST['prdrn_list']);
        $prdrn_empty_err_arr=array(); $prdrn_nonnmrcl_err_arr=array(); $prdrn_dplct_arr=array();
        $prdrn_nonexst_err_arr=array();
        foreach($prdrn_ids as $prdrn_id)
        {
          $prdrn_id=trim($prdrn_id);
          if(!preg_match('/\S+/', $prdrn_id))
          {
            $prdrn_empty_err_arr[]=$prdrn_id;
            if(count($prdrn_empty_err_arr)==1) {$errors['prdrn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['prdrn_empty']='</br>**There are '.count($prdrn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          elseif(!preg_match('/^[1-9][0-9]*$/', $prdrn_id))
          {
            $prdrn_nonnmrcl_err_arr[]=$prdrn_id;
            $errors['prdrn_nonnmrcl']='</br>**The following production ids have not been assigned properly (must be positive integers): '.html(implode(' / ', $prdrn_nonnmrcl_err_arr)).'.**';
          }
          else
          {
            $prdrn_dplct_arr[]=$prdrn_id;
            if(count(array_unique($prdrn_dplct_arr))<count($prdrn_dplct_arr))
            {$errors['prdrn_dplct']='</br>**There are duplicate ids within the array.**';}

            $prdrn_id_cln=cln($prdrn_id);

            $sql="SELECT 1 FROM prd WHERE prd_id='$prdrn_id_cln' LIMIT 1";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing production id (against production run id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $prdrn_nonexst_err_arr[]=$prdrn_id;
              $errors['prdrn_nonexst']='</br>**The following are not existing production ids: '.html(implode(' / ', $prdrn_nonexst_err_arr)).'.**';
            }
            else
            {
              if($prdrn_id==$prd_id)
              {$errors['prdrn_prd_id_mtch']='</br>**You cannot assign this production as a previous or subsequent run of itself: '.html($prd_id).'.**';}
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $prd_vrsn_list))
    {
      if($tr_lg) {$errors['prd_vrsn_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $prd_vrsn_nms=explode(',,', $prd_vrsn_list);
        if(count($prd_vrsn_nms)>250)
        {$errors['prd_vrsn_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $prd_vrsn_empty_err_arr=array(); $prd_vrsn_dplct_arr=array(); $prd_vrsn_url_err_arr=array();
          foreach($prd_vrsn_nms as $prd_vrsn_nm)
          {
            $prd_vrsn_nm=trim($prd_vrsn_nm);
            if(!preg_match('/\S+/', $prd_vrsn_nm))
            {
              $prd_vrsn_empty_err_arr[]=$prd_vrsn_nm;
              if(count($prd_vrsn_empty_err_arr)==1) {$errors['prd_vrsn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['prd_vrsn_empty']='</br>**There are '.count($prd_vrsn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $prd_vrsn_url=generateurl($prd_vrsn_nm);
              $prd_vrsn_dplct_arr[]=$prd_vrsn_url;
              if(count(array_unique($prd_vrsn_dplct_arr))<count($prd_vrsn_dplct_arr))
              {$errors['prd_vrsn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($prd_vrsn_nm)>255)
              {$errors['prd_vrsn_nm_excss_lngth']='</br>**Production version name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              $sql= "SELECT prd_vrsn_nm
                    FROM prd_vrsn
                    WHERE NOT EXISTS (SELECT 1 FROM prd_vrsn WHERE prd_vrsn_nm='$prd_vrsn_nm')
                    AND prd_vrsn_url='$prd_vrsn_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing prod version URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $prd_vrsn_url_err_arr[]=$row['prd_vrsn_nm'];
                if(count($prd_vrsn_url_err_arr)==1)
                {$errors['prd_vrsn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $prd_vrsn_url_err_arr)).'?**';}
                else
                {$errors['prd_vrsn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $prd_vrsn_url_err_arr)).'?**';}
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $prdcr_list))
    {
      if($tr_lg) {$errors['prdcr_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $prdcr_comp_prsn_rls=explode(',,', $_POST['prdcr_list']);
        if(count($prdcr_comp_prsn_rls)>250)
        {$errors['prdcr_rl_array_excss']='**Maximum of 250 producer roles allowed.**';}
        else
        {
          $prdcr_empty_err_arr=array(); $prdcr_cln_excss_err_arr=array(); $prdcr_cln_err_arr=array();
          $prdcr_comp_prsn_empty_err_arr=array(); $prdcr_pipe_excss_err_arr=array(); $prdcr_comp_tld_excss_err_arr=array();
          $prdcr_comp_tld_err_arr=array(); $prdcr_pipe_err_arr=array(); $prdcr_prsn_tld_excss_err_arr=array();
          $prdcr_prsn_tld_err_arr=array(); $prdcr_compprsn_rl_empty_err_arr=array(); $prdcr_compprsn_tld_excss_err_arr=array();
          $prdcr_compprsn_empty_err_arr=array(); $prdcr_compprsn_crt_excss_err_arr=array(); $prdcr_compprsn_crt_err_arr=array();
          $prdcr_compprsn_tld_err_arr=array(); $prdcr_comp_hyphn_excss_err_arr=array(); $prdcr_comp_sffx_err_arr=array();
          $prdcr_comp_hyphn_err_arr=array(); $prdcr_comp_dplct_arr=array(); $prdcr_comp_url_err_arr=array();
          $prdcr_prsn_hyphn_excss_err_arr=array(); $prdcr_prsn_sffx_err_arr=array(); $prdcr_prsn_hyphn_err_arr=array();
          $prdcr_prsn_smcln_excss_err_arr=array(); $prdcr_prsn_dplct_arr=array(); $prdcr_prsn_smcln_err_arr=array();
          $prdcr_prsn_nm_err_arr=array(); $prdcr_prsn_url_err_arr=array();
          foreach($prdcr_comp_prsn_rls as $prdcr_comp_prsn_rl)
          {
            $prdcr_comp_prsn_rl=trim($prdcr_comp_prsn_rl);

            if(!preg_match('/\S+/', $prdcr_comp_prsn_rl))
            {
              $prdcr_empty_err_arr[]=$prdcr_comp_prsn_rl;
              if(count($prdcr_empty_err_arr)==1) {$errors['prdcr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['prdcr_empty']='</br>**There are '.count($prdcr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(preg_match('/\S+/', $prdcr_comp_prsn_rl))
              {
                if(substr_count($prdcr_comp_prsn_rl, '::')>1)
                {
                  $prdcr_cln_excss_err_arr[]=$prdcr_comp_prsn_rl;
                  $errors['prdcr_cln_excss']='</br>**You may only use [::] once per producer-role coupling. Please amend: '.html(implode(' / ', $prdcr_cln_excss_err_arr)).'.**';
                }
                elseif(preg_match('/\S+.*::.*\S+/', $prdcr_comp_prsn_rl))
                {
                  list($prdcr_rl, $prdcr_comp_prsn_list)=explode('::', $prdcr_comp_prsn_rl);
                  $prdcr_rl=trim($prdcr_rl); $prdcr_comp_prsn_list=trim($prdcr_comp_prsn_list);

                  if(strlen($prdcr_rl)>255)
                  {$errors['prdcr_rl']='</br>**Producer role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                  $prdcr_comps_ppl=explode('>>', $prdcr_comp_prsn_list);
                  $prdcr_rl_ttl_array=array(); $prdcr_comp_nm_array=array(); $prdcr_prsn_nm_array=array();
                  foreach($prdcr_comps_ppl as $prdcr_comp_prsn)
                  {
                    $prdcr_comp_errors=0; $prdcr_prsn_errors=0;

                    $prdcr_comp_prsn=trim($prdcr_comp_prsn);
                    if(!preg_match('/\S+/', $prdcr_comp_prsn))
                    {
                      $prdcr_comp_prsn_empty_err_arr[]=$prdcr_comp_prsn;
                      if(count($prdcr_comp_prsn_empty_err_arr)==1) {$errors['prdcr_comp_prsn_empty']='</br>**There is 1 empty entry in a person arrray (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                      else {$errors['prdcr_comp_prsn_empty']='</br>**There are '.count($prdcr_comp_prsn_empty_err_arr).' empty entries in person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      if(substr_count($prdcr_comp_prsn, '||')>1)
                      {
                        $prdcr_prsn_nm_list=''; $prdcr_pipe_excss_err_arr[]=$prdcr_comp_prsn;
                        $errors['prdcr_pipe_excss']='</br>**You may only use [||] once per producer company-members coupling. Please amend: '.html(implode(' / ', $prdcr_pipe_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\|\|/', $prdcr_comp_prsn))
                      {
                        if(preg_match('/\S+.*\|\|(.*\S+)?/', $prdcr_comp_prsn))
                        {
                          list($prdcr_comp_nm, $prdcr_prsn_nm_list)=explode('||', $prdcr_comp_prsn);
                          $prdcr_comp_nm=trim($prdcr_comp_nm); $prdcr_prsn_nm_list=trim($prdcr_prsn_nm_list);

                          if(preg_match('/^\S+.*\*\*\*$/', $prdcr_comp_nm))
                          {$prdcr_comp_nm=preg_replace('/(\S+.*)(\*\*\*)/', '$1', $prdcr_comp_nm); $prdcr_comp_nm=trim($prdcr_comp_nm);}

                          if(substr_count($prdcr_comp_nm, '~~')>1)
                          {
                            $prdcr_comp_errors++; $prdcr_comp_tld_excss_err_arr[]=$prdcr_comp_nm;
                            $errors['prdcr_comp_tld_excss']='</br>**You may only use [~~] once per producer (company)-role coupling. Please amend: '.html(implode(' / ', $prdcr_comp_tld_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/\S+.*~~.*\S+/', $prdcr_comp_nm))
                          {
                            list($prdcr_comp_sb_rl, $prdcr_comp_nm)=explode('~~', $prdcr_comp_nm);
                            $prdcr_comp_sb_rl=trim($prdcr_comp_sb_rl); $prdcr_comp_nm=trim($prdcr_comp_nm);

                            if(strlen($prdcr_comp_sb_rl)>255)
                            {$prdcr_comp_errors++; $errors['prdcr_comp_sb_rl']='</br>**Producer (company) sub-role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                          }
                          elseif(substr_count($prdcr_comp_nm, '~~')==1)
                          {
                            $prdcr_comp_errors++; $prdcr_comp_tld_err_arr[]=$prdcr_comp_nm;
                            $errors['prdcr_comp_tld']='</br>**Producer (company)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $prdcr_comp_tld_err_arr)).'**';
                          }

                          if($prdcr_comp_errors==0) {$prdcr_comp_nm_array[]=$prdcr_comp_nm; $prdcr_rl_ttl_array[]=$prdcr_comp_nm;}
                        }
                        else
                        {
                          $prdcr_prsn_nm_list=''; $prdcr_pipe_err_arr[]=$prdcr_comp_prsn;
                          $errors['prdcr_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $prdcr_pipe_err_arr)).'.**';
                        }
                      }
                      else
                      {
                        if(preg_match('/^\S+.*\*\*\*$/', $prdcr_comp_prsn))
                        {$prdcr_comp_prsn=preg_replace('/(\S+.*)(\*\*\*)/', '$1', $prdcr_comp_prsn); $prdcr_comp_prsn=trim($prdcr_comp_prsn);}

                        if(substr_count($prdcr_comp_prsn, '~~')>1)
                        {
                          $prdcr_prsn_errors++; $prdcr_prsn_tld_excss_err_arr[]=$prdcr_comp_prsn;
                          $errors['prdcr_prsn_tld_excss']='</br>**You may only use [~~] once per producer (person)-role coupling. Please amend: '.html(implode(' / ', $prdcr_prsn_tld_excss_err_arr)).'.**';
                        }
                        elseif(preg_match('/\S+.*~~.*\S+/', $prdcr_comp_prsn))
                        {
                          list($prdcr_prsn_sb_rl, $prdcr_comp_prsn)=explode('~~', $prdcr_comp_prsn);
                          $prdcr_prsn_sb_rl=trim($prdcr_prsn_sb_rl); $prdcr_comp_prsn=trim($prdcr_comp_prsn);

                          if(strlen($prdcr_prsn_sb_rl)>255)
                          {$prdcr_prsn_errors++; $errors['prdcr_prsn_sb_rl']='</br>**Producer (person) sub-role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                        }
                        elseif(substr_count($prdcr_comp_prsn, '~~')==1)
                        {
                          $prdcr_prsn_errors++; $prdcr_prsn_tld_err_arr[]=$prdcr_comp_prsn;
                          $errors['prdcr_prsn_tld']='</br>**Producer (person)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $prdcr_prsn_tld_err_arr)).'**';
                        }

                        $prdcr_prsn_nm_list='';
                        if($prdcr_prsn_errors==0) {$prdcr_prsn_nm_array[]=$prdcr_comp_prsn; $prdcr_rl_ttl_array[]=$prdcr_comp_prsn;}
                      }

                      if(preg_match('/\S+/', $prdcr_prsn_nm_list))
                      {
                        $prdcr_prsn_nms=explode('//', $prdcr_prsn_nm_list);
                        foreach($prdcr_prsn_nms as $prdcr_prsn_nm)
                        {
                          $prdcr_prsn_nm=trim($prdcr_prsn_nm);
                          if(!preg_match('/\S+/', $prdcr_prsn_nm))
                          {
                            $prdcr_compprsn_rl_empty_err_arr[]=$prdcr_prsn_nm;
                            if(count($prdcr_compprsn_rl_empty_err_arr)==1) {$errors['prdcr_compprsn_rl_empty']='</br>**There is 1 empty entry in a company member-role array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                            else {$errors['prdcr_compprsn_rl_empty']='</br>**There are '.count($prdcr_compprsn_rl_empty_err_arr).' empty entries in company member-role arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                          }
                          else
                          {
                            if(substr_count($prdcr_prsn_nm, '~~')>1)
                            {
                              $prdcr_compprsn_tld_excss_err_arr[]=$prdcr_prsn_nm;
                              $errors['prdcr_compprsn_tld_excss']='</br>**You may only use [~~] once per producer (company person)-role coupling. Please amend: '.html(implode(' / ', $prdcr_compprsn_tld_excss_err_arr)).'.**';
                            }
                            elseif(preg_match('/\S+.*~~.*\S+/', $prdcr_prsn_nm))
                            {
                              list($prdcr_compprsn_rl, $prdcr_prsn_nm)=explode('~~', $prdcr_prsn_nm);
                              $prdcr_compprsn_rl=trim($prdcr_compprsn_rl); $prdcr_prsn_nm=trim($prdcr_prsn_nm);

                              if(strlen($prdcr_compprsn_rl)>255)
                              {$errors['prdcr_compprsn_rl']='</br>**Producer (company person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                              $prdcr_prsn_nms=explode('Â¬Â¬', $prdcr_prsn_nm);
                              foreach($prdcr_prsn_nms as $prdcr_prsn_nm)
                              {
                                $prdcr_prsn_nm=trim($prdcr_prsn_nm);
                                if(!preg_match('/\S+/', $prdcr_prsn_nm))
                                {
                                  $prdcr_compprsn_empty_err_arr[]=$prdcr_prsn_nm;
                                  if(count($prdcr_compprsn_empty_err_arr)==1) {$errors['prdcr_compprsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive logical negation symbols [¬¬¬¬] or two symbols [¬¬] with no text beforehand or thereafter).**';}
                                  else {$errors['prdcr_compprsn_empty']='</br>**There are '.count($prdcr_compprsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive logical negation symbols [¬¬¬¬] or two symbols [¬¬] with no text beforehand or thereafter).**';}
                                }
                                else
                                {
                                  if(preg_match('/^\S+.*\*$/', $prdcr_prsn_nm))
                                  {$prdcr_prsn_nm=preg_replace('/(\S+.*)(\*)/', '$1', $prdcr_prsn_nm); $prdcr_prsn_nm=trim($prdcr_prsn_nm);}

                                  if(substr_count($prdcr_prsn_nm, '^^')>1)
                                  {
                                    $prdcr_compprsn_crt_excss_err_arr[]=$prdcr_prsn_nm;
                                    $errors['prdcr_compprsn_crt_excss']='</br>**You may only use [^^] once per performer-(credit display) role coupling. Please amend: '.html(implode(' / ', $prdcr_compprsn_crt_excss_err_arr)).'.**';
                                  }
                                  elseif(preg_match('/\S+.*\^\^.*\S+/', $prdcr_prsn_nm))
                                  {
                                    list($prdcr_compprsn_sb_rl, $prdcr_prsn_nm)=explode('^^', $prdcr_prsn_nm);
                                    $prdcr_compprsn_sb_rl=trim($prdcr_compprsn_sb_rl); $prdcr_prsn_nm=trim($prdcr_prsn_nm);

                                    if(strlen($prdcr_compprsn_sb_rl)>255)
                                    {$errors['prdcr_compprsn_sb_rl']='</br>**Producer (company person) sub-role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                                    $prdcr_prsn_nm_array[]=$prdcr_prsn_nm; $prdcr_rl_ttl_array[]=$prdcr_prsn_nm;
                                  }
                                  elseif(substr_count($prdcr_prsn_nm, '^^')==1)
                                  {$prdcr_compprsn_crt_err_arr[]=$prdcr_prsn_nm;
                                  $errors['prdcr_compprsn_crt']='</br>**Producer (company person)-(credit display) role assignation must use [^^] in the correct format. Please amend: '.html(implode(' / ', $prdcr_compprsn_crt_err_arr)).'**';}
                                  else
                                  {$prdcr_prsn_nm_array[]=$prdcr_prsn_nm; $prdcr_rl_ttl_array[]=$prdcr_prsn_nm;}
                                }
                              }
                            }
                            else
                            {
                              $prdcr_compprsn_tld_err_arr[]=$prdcr_prsn_nm;
                              $errors['prdcr_compprsn_tld']='</br>**You must assign a company role to the following using [~~]: '.html(implode(' / ', $prdcr_compprsn_tld_err_arr)).'.**';
                            }
                          }
                        }
                      }

                      if(count($prdcr_rl_ttl_array)>250)
                      {$errors['prdcr_rl_ttl_array_excss']='</br>**Maximum of 250 entries (companies and people per role) allowed.**';}
                    }
                  }

                  if(count($prdcr_comp_nm_array)>0)
                  {
                    foreach($prdcr_comp_nm_array as $prdcr_comp_nm)
                    {
                      $prdcr_comp_errors=0;

                      if(substr_count($prdcr_comp_nm, '--')>1)
                      {
                        $prdcr_comp_errors++; $prdcr_comp_sffx_num='0'; $prdcr_comp_hyphn_excss_err_arr[]=$prdcr_comp_nm;
                        $errors['prdcr_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per producer (company). Please amend: '.html(implode(' / ', $prdcr_comp_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $prdcr_comp_nm))
                      {
                        list($prdcr_comp_nm_no_sffx, $prdcr_comp_sffx_num)=explode('--', $prdcr_comp_nm);
                        $prdcr_comp_nm_no_sffx=trim($prdcr_comp_nm_no_sffx); $prdcr_comp_sffx_num=trim($prdcr_comp_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $prdcr_comp_sffx_num))
                        {
                          $prdcr_comp_errors++; $prdcr_comp_sffx_num='0'; $prdcr_comp_sffx_err_arr[]=$prdcr_comp_nm;
                          $errors['prdcr_comp_sffx']='</br>**Producer (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $prdcr_comp_sffx_err_arr)).'**';
                        }
                        $prdcr_comp_nm=$prdcr_comp_nm_no_sffx;
                      }
                      elseif(substr_count($prdcr_comp_nm, '--')==1)
                      {$prdcr_comp_errors++; $prdcr_comp_sffx_num='0'; $prdcr_comp_hyphn_err_arr[]=$prdcr_comp_nm;
                      $errors['prdcr_comp_hyphn']='</br>**Producer (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $prdcr_comp_hyphn_err_arr)).'**';}
                      else
                      {$prdcr_comp_sffx_num='0';}

                      if($prdcr_comp_sffx_num) {$prdcr_comp_sffx_rmn=' ('.romannumeral($prdcr_comp_sffx_num).')';} else {$prdcr_comp_sffx_rmn='';}

                      $prdcr_comp_url=generateurl($prdcr_comp_nm.$prdcr_comp_sffx_rmn);

                      $prdcr_comp_dplct_arr[]=$prdcr_comp_url;
                      if(count(array_unique($prdcr_comp_dplct_arr))<count($prdcr_comp_dplct_arr))
                      {$errors['prdcr_comp_dplct']='</br>**There are entries within the array that create duplicate company URLs.**';}

                      if(strlen($prdcr_comp_nm)>255 || strlen($prdcr_comp_url)>255)
                      {$prdcr_comp_errors++; $errors['prdcr_comp_nm_excss_lngth']='</br>**Producer (company) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                      if($prdcr_comp_errors==0)
                      {
                        $prdcr_comp_nm_cln=cln($prdcr_comp_nm);
                        $prdcr_comp_sffx_num_cln=cln($prdcr_comp_sffx_num);
                        $prdcr_comp_url_cln=cln($prdcr_comp_url);

                        $sql= "SELECT comp_nm, comp_sffx_num
                              FROM comp
                              WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$prdcr_comp_nm_cln' AND comp_sffx_num='$prdcr_comp_sffx_num_cln')
                              AND comp_url='$prdcr_comp_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing producer company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['comp_sffx_num']) {$prdcr_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                          else {$prdcr_comp_url_error_sffx_dsply='';}
                          $prdcr_comp_url_err_arr[]=$row['comp_nm'].$prdcr_comp_url_error_sffx_dsply;
                          if(count($prdcr_comp_url_err_arr)==1)
                          {$errors['prdcr_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $prdcr_comp_url_err_arr)).'?**';}
                          else
                          {$errors['prdcr_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $prdcr_comp_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }

                  if(count($prdcr_prsn_nm_array)>0)
                  {
                    foreach($prdcr_prsn_nm_array as $prdcr_prsn_nm)
                    {
                      $prdcr_prsn_nm=trim($prdcr_prsn_nm);
                      $prdcr_prsn_errors=0;
                      if(substr_count($prdcr_prsn_nm, '--')>1)
                      {
                        $prdcr_prsn_errors++; $prdcr_prsn_sffx_num='0'; $prdcr_prsn_hyphn_excss_err_arr[]=$prdcr_prsn_nm;
                        $errors['prdcr_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per producer (person). Please amend: '.html(implode(' / ', $prdcr_prsn_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $prdcr_prsn_nm))
                      {
                        list($prdcr_prsn_nm_no_sffx, $prdcr_prsn_sffx_num)=explode('--', $prdcr_prsn_nm);
                        $prdcr_prsn_nm_no_sffx=trim($prdcr_prsn_nm_no_sffx); $prdcr_prsn_sffx_num=trim($prdcr_prsn_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $prdcr_prsn_sffx_num))
                        {
                          $prdcr_prsn_errors++; $prdcr_prsn_sffx_num='0'; $prdcr_prsn_sffx_err_arr[]=$prdcr_prsn_nm;
                          $errors['prdcr_prsn_sffx']='</br>**Producer (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $prdcr_prsn_sffx_err_arr)).'**';
                        }
                        $prdcr_prsn_nm=$prdcr_prsn_nm_no_sffx;
                      }
                      elseif(substr_count($prdcr_prsn_nm, '--')==1)
                      {$prdcr_prsn_errors++; $prdcr_prsn_sffx_num='0'; $prdcr_prsn_hyphn_err_arr[]=$prdcr_prsn_nm;
                      $errors['prdcr_prsn_hyphn']='</br>**Producer (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $prdcr_prsn_hyphn_err_arr)).'**';}
                      else
                      {$prdcr_prsn_sffx_num='0';}

                      if($prdcr_prsn_sffx_num) {$prdcr_prsn_sffx_rmn=' ('.romannumeral($prdcr_prsn_sffx_num).')';} else {$prdcr_prsn_sffx_rmn='';}

                      if(substr_count($prdcr_prsn_nm, ';;')>1)
                      {
                        $prdcr_prsn_errors++; $prdcr_prsn_smcln_excss_err_arr[]=$prdcr_prsn_nm;
                        $errors['prdcr_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $prdcr_prsn_smcln_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*;;(.*\S+)?/', $prdcr_prsn_nm))
                      {
                        list($prdcr_prsn_frst_nm, $prdcr_prsn_lst_nm)=explode(';;', $prdcr_prsn_nm);
                        $prdcr_prsn_frst_nm=trim($prdcr_prsn_frst_nm); $prdcr_prsn_lst_nm=trim($prdcr_prsn_lst_nm);

                        if(preg_match('/\S+/', $prdcr_prsn_lst_nm)) {$prdcr_prsn_lst_nm_dsply=' '.$prdcr_prsn_lst_nm;}
                        else {$prdcr_prsn_lst_nm_dsply='';}

                        $prdcr_prsn_fll_nm=$prdcr_prsn_frst_nm.$prdcr_prsn_lst_nm_dsply;
                        $prdcr_prsn_url=generateurl($prdcr_prsn_fll_nm.$prdcr_prsn_sffx_rmn);

                        $prdcr_prsn_dplct_arr[]=$prdcr_prsn_url;
                        if(count(array_unique($prdcr_prsn_dplct_arr))<count($prdcr_prsn_dplct_arr))
                        {$errors['prdcr_prsn_dplct']='</br>**There are entries within the array that create duplicate person URLs.**';}

                        if(strlen($prdcr_prsn_fll_nm)>255 || strlen($prdcr_prsn_url)>255)
                        {$prdcr_prsn_errors++; $errors['prdcr_prsn_excss_lngth']='</br>**Producer (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                      }
                      else
                      {
                        $prdcr_prsn_errors++; $prdcr_prsn_smcln_err_arr[]=$prdcr_prsn_nm;
                        $errors['prdcr_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $prdcr_prsn_smcln_err_arr)).'.**';
                      }

                      if($prdcr_prsn_errors==0)
                      {
                        $prdcr_prsn_frst_nm_cln=cln($prdcr_prsn_frst_nm);
                        $prdcr_prsn_lst_nm_cln=cln($prdcr_prsn_lst_nm);
                        $prdcr_prsn_fll_nm_cln=cln($prdcr_prsn_fll_nm);
                        $prdcr_prsn_sffx_num_cln=cln($prdcr_prsn_sffx_num);
                        $prdcr_prsn_url_cln=cln($prdcr_prsn_url);

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$prdcr_prsn_frst_nm_cln' AND prsn_lst_nm='$prdcr_prsn_lst_nm_cln')
                              AND prsn_fll_nm='$prdcr_prsn_fll_nm_cln' AND prsn_sffx_num='$prdcr_prsn_sffx_num_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for producer person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$prdcr_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$prdcr_prsn_nm_error_sffx_dsply='';}
                          $prdcr_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prdcr_prsn_nm_error_sffx_dsply;
                          if(count($prdcr_prsn_nm_err_arr)==1)
                          {$errors['prdcr_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $prdcr_prsn_nm_err_arr)).'.**';}
                          else
                          {$errors['prdcr_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $prdcr_prsn_nm_err_arr)).'.**';}
                        }

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$prdcr_prsn_fll_nm_cln' AND prsn_sffx_num='$prdcr_prsn_sffx_num_cln')
                              AND prsn_url='$prdcr_prsn_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing producer person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$prdcr_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$prdcr_prsn_url_error_sffx_dsply='';}
                          $prdcr_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prdcr_prsn_url_error_sffx_dsply;
                          if(count($prdcr_prsn_url_err_arr)==1)
                          {$errors['prdcr_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $prdcr_prsn_url_err_arr)).'?**';}
                          else
                          {$errors['prdcr_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $prdcr_prsn_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }
                }
                else
                {
                  $prdcr_cln_err_arr[]=$prdcr_comp_prsn_rl;
                  $errors['prdcr_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $prdcr_cln_err_arr)).'.**';
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $prf_list))
    {
      if($tr_lg) {$errors['prf_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $prf_prsn_nm_rls=explode(',,', $_POST['prf_list']);
        if(count($prf_prsn_nm_rls)>250)
        {$errors['prf_nm_rl_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $prf_prsn_empty_err_arr=array(); $prf_prsn_rl_dplct_err_arr=array(); $prf_prsn_cln_err_arr=array();
          $prf_prsn_cln_excss_err_arr=array(); $prf_prsn_rl_smcln_excss_err_arr=array(); $prf_prsn_rl_smcln_err_arr=array();
          $prf_prsn_rl_pipe_excss_err_arr=array(); $prf_prsn_rl_pipe_err_arr=array(); $prf_prsn_sffx_err_arr=array();
          $prf_prsn_hyphn_err_arr=array(); $prf_prsn_hyphn_excss_err_arr=array(); $prf_prsn_dplct_arr=array();
          $prf_prsn_smcln_err_arr=array(); $prf_prsn_smcln_excss_err_arr=array(); $prf_prsn_nm_err_arr=array();
          $prf_prsn_url_err_arr=array();
          foreach($prf_prsn_nm_rls as $prf_prsn_nm_rl)
          {
            $prf_prsn_errors=0;

            if(!preg_match('/\S+/', $prf_prsn_nm_rl))
            {
              $prf_prsn_empty_err_arr[]=$prf_prsn_nm_rl;
              if(count($prf_prsn_empty_err_arr)==1) {$errors['prf_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['prf_empty']='</br>**There are '.count($prf_prsn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($prf_prsn_nm_rl, '::')>1)
              {
                $prf_prsn_errors++; $prf_prsn_nm=trim($prf_prsn_nm_rl);
                $prf_prsn_cln_excss_err_arr[]=$prf_prsn_nm_rl;
                $errors['prf_cln_excss']='</br>**You may only use [::] once per performer-role coupling. Please amend: '.html(implode(' / ', $prf_prsn_cln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*::.*\S+/', $prf_prsn_nm_rl))
              {
                list($prf_prsn_nm, $prf_prsn_rl)=explode('::', $prf_prsn_nm_rl);
                $prf_prsn_nm=trim($prf_prsn_nm);

                $prf_prsn_rls=explode('//', $prf_prsn_rl);
                if(count($prf_prsn_rls)>250)
                {$errors['prf_rl_array_excss']='**Maximum of 250 roles per entry allowed.**';}
                else
                {
                  $prf_rl_dplct_arr=array();
                  foreach($prf_prsn_rls as $prf_prsn_rl)
                  {
                    $prf_prsn_rl=trim($prf_prsn_rl);

                    if(preg_match('/^\S+.*\*$/', $prf_prsn_rl))
                    {$prf_prsn_rl=preg_replace('/(\S+.*)(\*)/', '$1', $prf_prsn_rl); $prf_prsn_rl=trim($prf_prsn_rl);}

                    if(substr_count($prf_prsn_rl, ';;')>1)
                    {
                      $prf_prsn_errors++; $prf_prsn_rl_smcln_excss_err_arr[]=$prf_prsn_rl;
                      $errors['prf_rl_smcln_excss']='</br>**You may only use [;;] once per role-description coupling. Please amend: '.html(implode(' / ', $prf_prsn_rl_smcln_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/^\S+.*;;.*\S+$/', $prf_prsn_rl))
                    {
                      list($prf_prsn_rl, $prf_prsn_rl_dscr)=explode(';;', $prf_prsn_rl);
                      $prf_prsn_rl=trim($prf_prsn_rl); $prf_prsn_rl_dscr=trim($prf_prsn_rl_dscr);

                      if(strlen($prf_prsn_rl_dscr)>255)
                      {$errors['prf_rl_dscr_excss_lngth']='</br>**Role description is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                    }
                    elseif(substr_count($prf_prsn_rl, ';;')==1)
                    {$prf_prsn_errors++; $prf_prsn_rl_smcln_err_arr[]=$prf_prsn_rl;
                    $errors['prf_rl_smcln']='</br>**Role description assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $prf_prsn_rl_smcln_err_arr)).'**';}

                    if(substr_count($prf_prsn_rl, '||')>1)
                    {
                      $prf_prsn_errors++; $prf_prsn_rl_pipe_excss_err_arr[]=$prf_prsn_rl;
                      $errors['prf_rl_pipe_excss']='</br>**You may only use [||] once per role-link coupling. Please amend: '.html(implode(' / ', $prf_prsn_rl_pipe_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/^\S+.*\|\|.*\S+$/', $prf_prsn_rl))
                    {
                      list($prf_prsn_rl, $prf_prsn_rl_lnk)=explode('||', $prf_prsn_rl);
                      $prf_prsn_rl=trim($prf_prsn_rl); $prf_prsn_rl_lnk=trim($prf_prsn_rl_lnk);

                      if(strlen($prf_prsn_rl_lnk)>255)
                      {$errors['prf_rl_lnk_excss_lngth']='</br>**Role link is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                    }
                    elseif(substr_count($prf_prsn_rl, ';;')==1)
                    {$prf_prsn_errors++; $prf_prsn_rl_pipe_err_arr[]=$prf_prsn_rl;
                    $errors['prf_rl_pipe']='</br>**Role link assignation must use [||] in the correct format. Please amend: '.html(implode(' / ', $prf_prsn_rl_pipe_err_arr)).'**';}

                    if(strlen($prf_prsn_rl)>255)
                    {$errors['prf_rl_excss_lngth']='</br>**Role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                    else
                    {
                      $prf_rl_dplct_arr[]=$prf_prsn_rl;
                      if(count(array_unique($prf_rl_dplct_arr))<count($prf_rl_dplct_arr))
                      {$prf_prsn_rl_dplct_err_arr[]=$prf_prsn_nm;}
                    }

                    if(count($prf_prsn_rl_dplct_err_arr)>0)
                    {
                      if(count($prf_prsn_rl_dplct_err_arr)==1)
                      {$errors['prf_rl_dplct']='</br>**The following performer lists duplicate roles: '.html(implode(' / ', $prf_prsn_rl_dplct_err_arr)).'.**';}
                      else
                      {$errors['prf_rl_dplct']='</br>**The following performers list duplicate roles: '.html(implode(' / ', $prf_prsn_rl_dplct_err_arr)).'.**';}
                    }
                  }
                  unset($prf_rl_dplct_arr);
                }
              }
              else
              {
                $prf_prsn_errors++; $prf_prsn_nm=trim($prf_prsn_nm_rl);
                $prf_prsn_cln_err_arr[]=$prf_prsn_nm_rl;
                $errors['prf_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $prf_prsn_cln_err_arr)).'.**';
              }

              if(substr_count($prf_prsn_nm, '--')>1)
              {
                $prf_prsn_errors++; $prf_prsn_sffx_num='0'; $prf_prsn_hyphn_excss_err_arr[]=$prf_prsn_nm;
                $errors['prf_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per performer. Please amend: '.html(implode(' / ', $prf_prsn_hyphn_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*--.+$/', $prf_prsn_nm))
              {
                list($prf_prsn_nm_no_sffx, $prf_prsn_sffx_num)=explode('--', $prf_prsn_nm);
                $prf_prsn_nm_no_sffx=trim($prf_prsn_nm_no_sffx); $prf_prsn_sffx_num=trim($prf_prsn_sffx_num);

                if(!preg_match('/^[1-9][0-9]{0,1}$/', $prf_prsn_sffx_num))
                {
                  $prf_prsn_errors++; $prf_prsn_sffx_num='0'; $prf_prsn_sffx_err_arr[]=$prf_prsn_nm;
                  $errors['prf_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $prf_prsn_sffx_err_arr)).'**';
                }
                $prf_prsn_nm=$prf_prsn_nm_no_sffx;
              }
              elseif(substr_count($prf_prsn_nm, '--')==1)
              {$prf_prsn_errors++; $prf_prsn_sffx_num='0'; $prf_prsn_hyphn_err_arr[]=$prf_prsn_nm;
              $errors['prf_hyphn']='</br>**Performer suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $prf_prsn_hyphn_err_arr)).'**';}
              else
              {$prf_prsn_sffx_num='0';}

              if($prf_prsn_sffx_num) {$prf_prsn_sffx_rmn=' ('.romannumeral($prf_prsn_sffx_num).')';} else {$prf_prsn_sffx_rmn='';}

              if(substr_count($prf_prsn_nm, ';;')>1)
              {
                $prf_prsn_errors++; $prf_prsn_frst_nm=''; $prf_prsn_lst_nm=''; $prf_prsn_fll_nm=''; $prf_prsn_url='';
                $prf_prsn_smcln_excss_err_arr[]=$prf_prsn_nm;
                $errors['prf_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $prf_prsn_smcln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*;;(.*\S+)?/', $prf_prsn_nm))
              {
                list($prf_prsn_frst_nm, $prf_prsn_lst_nm)=explode(';;', $prf_prsn_nm);
                $prf_prsn_frst_nm=trim($prf_prsn_frst_nm); $prf_prsn_lst_nm=trim($prf_prsn_lst_nm);

                if(preg_match('/\S+/', $prf_prsn_lst_nm)) {$prf_prsn_lst_nm_dsply=' '.$prf_prsn_lst_nm;}
                else {$prf_prsn_lst_nm_dsply='';}

                $prf_prsn_fll_nm=$prf_prsn_frst_nm.$prf_prsn_lst_nm_dsply;
                $prf_prsn_url=generateurl($prf_prsn_fll_nm.$prf_prsn_sffx_rmn);

                $prf_prsn_dplct_arr[]=$prf_prsn_url;
                if(count(array_unique($prf_prsn_dplct_arr))<count($prf_prsn_dplct_arr))
                {$errors['prf_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

                if(strlen($prf_prsn_fll_nm)>255 || strlen($prf_prsn_url)>255)
                {$prf_prsn_errors++; $errors['prf_fll_nm_excss_lngth']='</br>**Performer (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
              }
              else
              {
                $prf_prsn_errors++; $prf_prsn_smcln_err_arr[]=$prf_prsn_nm;
                $errors['prf_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $prf_prsn_smcln_err_arr)).'.**';
              }

              if($prf_prsn_errors==0)
              {
                $prf_prsn_frst_nm_cln=cln($prf_prsn_frst_nm);
                $prf_prsn_lst_nm_cln=cln($prf_prsn_lst_nm);
                $prf_prsn_fll_nm_cln=cln($prf_prsn_fll_nm);
                $prf_prsn_sffx_num_cln=cln($prf_prsn_sffx_num);
                $prf_prsn_url_cln=cln($prf_prsn_url);

                $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                      FROM prsn
                      WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$prf_prsn_frst_nm_cln' AND prsn_lst_nm='$prf_prsn_lst_nm_cln')
                      AND prsn_fll_nm='$prf_prsn_fll_nm_cln' AND prsn_sffx_num='$prf_prsn_sffx_num_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for performer person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['prsn_sffx_num']) {$prf_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                  else {$prf_prsn_nm_error_sffx_dsply='';}
                  $prf_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prf_prsn_nm_error_sffx_dsply;
                  if(count($prf_prsn_nm_err_arr)==1)
                  {$errors['prf_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $prf_prsn_nm_err_arr)).'.**';}
                  else
                  {$errors['prf_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $prf_prsn_nm_err_arr)).'.**';}
                }

                $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                      FROM prsn
                      WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$prf_prsn_fll_nm_cln' AND prsn_sffx_num='$prf_prsn_sffx_num_cln')
                      AND prsn_url='$prf_prsn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing performer person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['prsn_sffx_num']) {$prf_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                  else {$prf_prsn_url_error_sffx_dsply='';}
                  $prf_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prf_prsn_url_error_sffx_dsply;
                  if(count($prf_prsn_url_err_arr)==1)
                  {$errors['prf_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $prf_prsn_url_err_arr)).'?**';}
                  else
                  {$errors['prf_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $prf_prsn_url_err_arr)).'?**';}
                }
              }
            }
          }
        }
      }
    }

    if(strlen($prd_cst_nt)>255) {$errors['prd_cst_nt_excss_lngth']='</br>**Cast notes are allowed a maximum of 255 characters.**';}

    if(preg_match('/\S+/', $us_list))
    {
      if($tr_lg) {$errors['us_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $us_prsn_nm_rls=explode(',,', $_POST['us_list']);
        if(count($us_prsn_nm_rls)>250)
        {$errors['us_nm_rl_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $us_prsn_empty_err_arr=array(); $us_prsn_rl_dplct_err_arr=array(); $us_prsn_cln_err_arr=array();
          $us_prsn_cln_excss_err_arr=array(); $us_prsn_rl_smcln_excss_err_arr=array(); $us_prsn_rl_smcln_err_arr=array();
          $us_prsn_rl_pipe_excss_err_arr=array(); $us_prsn_rl_pipe_err_arr=array(); $us_prsn_sffx_err_arr=array();
          $us_prsn_hyphn_err_arr=array(); $us_prsn_hyphn_excss_err_arr=array(); $us_prsn_dplct_arr=array();
          $us_prsn_smcln_err_arr=array(); $us_prsn_smcln_excss_err_arr=array(); $us_prsn_nm_err_arr=array();
          $us_prsn_url_err_arr=array();
          foreach($us_prsn_nm_rls as $us_prsn_nm_rl)
          {
            $us_prsn_errors=0;

            if(!preg_match('/\S+/', $us_prsn_nm_rl))
            {
              $us_prsn_empty_err_arr[]=$us_prsn_nm_rl;
              if(count($us_prsn_empty_err_arr)==1) {$errors['us_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['us_empty']='</br>**There are '.count($us_prsn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($us_prsn_nm_rl, '::')>1)
              {
                $us_prsn_errors++; $us_prsn_nm=trim($us_prsn_nm_rl);
                $us_prsn_cln_excss_err_arr[]=$us_prsn_nm_rl;
                $errors['us_cln_excss']='</br>**You may only use [::] once per understudy-role coupling. Please amend: '.html(implode(' / ', $us_prsn_cln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*::.*\S+/', $us_prsn_nm_rl))
              {
                list($us_prsn_nm, $us_prsn_rl)=explode('::', $us_prsn_nm_rl);
                $us_prsn_nm=trim($us_prsn_nm);

                $us_prsn_rls=explode('//', $us_prsn_rl);
                if(count($us_prsn_rls)>250)
                {$errors['us_rl_array_excss']='**Maximum of 250 roles per entry allowed.**';}
                else
                {
                  $us_rl_dplct_arr=array();
                  foreach($us_prsn_rls as $us_prsn_rl)
                  {
                    $us_prsn_rl=trim($us_prsn_rl);

                    if(preg_match('/^\S+.*\*$/', $us_prsn_rl))
                    {$us_prsn_rl=preg_replace('/(\S+.*)(\*)/', '$1', $us_prsn_rl); $us_prsn_rl=trim($us_prsn_rl);}

                    if(substr_count($us_prsn_rl, ';;')>1)
                    {
                      $us_prsn_errors++; $us_prsn_rl_smcln_excss_err_arr[]=$us_prsn_rl;
                      $errors['us_rl_smcln_excss']='</br>**You may only use [;;] once per role-description coupling. Please amend: '.html(implode(' / ', $us_prsn_rl_smcln_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/^\S+.*;;.*\S+$/', $us_prsn_rl))
                    {
                      list($us_prsn_rl, $us_prsn_rl_dscr)=explode(';;', $us_prsn_rl);
                      $us_prsn_rl=trim($us_prsn_rl); $us_prsn_rl_dscr=trim($us_prsn_rl_dscr);

                      if(strlen($us_prsn_rl_dscr)>255)
                      {$errors['us_rl_dscr_excss_lngth']='</br>**Role description is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                    }
                    elseif(substr_count($us_prsn_rl, ';;')==1)
                    {$us_prsn_errors++; $us_prsn_rl_smcln_err_arr[]=$us_prsn_rl;
                    $errors['us_rl_smcln']='</br>**Role description assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $us_prsn_rl_smcln_err_arr)).'**';}

                    if(substr_count($us_prsn_rl, '||')>1)
                    {
                      $us_prsn_errors++; $us_prsn_rl_pipe_excss_err_arr[]=$us_prsn_rl;
                      $errors['us_rl_pipe_excss']='</br>**You may only use [||] once per role-link coupling. Please amend: '.html(implode(' / ', $us_prsn_rl_pipe_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/^\S+.*\|\|.*\S+$/', $us_prsn_rl))
                    {
                      list($us_prsn_rl, $us_prsn_rl_lnk)=explode('||', $us_prsn_rl);
                      $us_prsn_rl=trim($us_prsn_rl); $us_prsn_rl_lnk=trim($us_prsn_rl_lnk);

                      if(strlen($us_prsn_rl_lnk)>255)
                      {$errors['us_rl_lnk_excss_lngth']='</br>**Role link is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                    }
                    elseif(substr_count($us_prsn_rl, ';;')==1)
                    {$us_prsn_errors++; $us_prsn_rl_pipe_err_arr[]=$us_prsn_rl;
                    $errors['us_rl_pipe']='</br>**Role link assignation must use [||] in the correct format. Please amend: '.html(implode(' / ', $us_prsn_rl_pipe_err_arr)).'**';}

                    if(strlen($us_prsn_rl)>255)
                    {$errors['us_rl_excss_lngth']='</br>**Role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                    else
                    {
                      $us_rl_dplct_arr[]=$us_prsn_rl;
                      if(count(array_unique($us_rl_dplct_arr))<count($us_rl_dplct_arr))
                      {$us_prsn_rl_dplct_err_arr[]=$us_prsn_nm;}
                    }

                    if(count($us_prsn_rl_dplct_err_arr)>0)
                    {
                      if(count($us_prsn_rl_dplct_err_arr)==1)
                      {$errors['us_rl_dplct']='</br>**The following understudy lists duplicate roles: '.html(implode(' / ', $us_prsn_rl_dplct_err_arr)).'.**';}
                      else
                      {$errors['us_rl_dplct']='</br>**The following understudies list duplicate roles: '.html(implode(' / ', $us_prsn_rl_dplct_err_arr)).'.**';}
                    }
                  }
                  unset($us_rl_dplct_arr);
                }
              }
              else
              {
                $us_prsn_errors++; $us_prsn_nm=trim($us_prsn_nm_rl);
                $us_prsn_cln_err_arr[]=$us_prsn_nm_rl;
                $errors['us_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $us_prsn_cln_err_arr)).'.**';
              }

              if(substr_count($us_prsn_nm, '--')>1)
              {
                $us_prsn_errors++; $us_prsn_sffx_num='0'; $us_prsn_hyphn_excss_err_arr[]=$us_prsn_nm;
                $errors['us_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per understudy. Please amend: '.html(implode(' / ', $us_prsn_hyphn_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*--.+$/', $us_prsn_nm))
              {
                list($us_prsn_nm_no_sffx, $us_prsn_sffx_num)=explode('--', $us_prsn_nm);
                $us_prsn_nm_no_sffx=trim($us_prsn_nm_no_sffx); $us_prsn_sffx_num=trim($us_prsn_sffx_num);

                if(!preg_match('/^[1-9][0-9]{0,1}$/', $us_prsn_sffx_num))
                {
                  $us_prsn_errors++; $us_prsn_sffx_num='0'; $us_prsn_sffx_err_arr[]=$us_prsn_nm;
                  $errors['us_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $us_prsn_sffx_err_arr)).'**';
                }
                $us_prsn_nm=$us_prsn_nm_no_sffx;
              }
              elseif(substr_count($us_prsn_nm, '--')==1)
              {$us_prsn_errors++; $us_prsn_sffx_num='0'; $us_prsn_hyphn_err_arr[]=$us_prsn_nm;
              $errors['us_hyphn']='</br>**Understudy suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $us_prsn_hyphn_err_arr)).'**';}
              else
              {$us_prsn_sffx_num='0';}

              if($us_prsn_sffx_num) {$us_prsn_sffx_rmn=' ('.romannumeral($us_prsn_sffx_num).')';} else {$us_prsn_sffx_rmn='';}

              if(substr_count($us_prsn_nm, ';;')>1)
              {
                $us_prsn_errors++; $us_prsn_frst_nm=''; $us_prsn_lst_nm=''; $us_prsn_fll_nm=''; $us_prsn_url='';
                $us_prsn_smcln_excss_err_arr[]=$us_prsn_nm;
                $errors['us_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $us_prsn_smcln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*;;(.*\S+)?/', $us_prsn_nm))
              {
                list($us_prsn_frst_nm, $us_prsn_lst_nm)=explode(';;', $us_prsn_nm);
                $us_prsn_frst_nm=trim($us_prsn_frst_nm); $us_prsn_lst_nm=trim($us_prsn_lst_nm);

                if(preg_match('/\S+/', $us_prsn_lst_nm)) {$us_prsn_lst_nm_dsply=' '.$us_prsn_lst_nm;}
                else {$us_prsn_lst_nm_dsply='';}

                $us_prsn_fll_nm=$us_prsn_frst_nm.$us_prsn_lst_nm_dsply;
                $us_prsn_url=generateurl($us_prsn_fll_nm.$us_prsn_sffx_rmn);

                $us_prsn_dplct_arr[]=$us_prsn_url;
                if(count(array_unique($us_prsn_dplct_arr))<count($us_prsn_dplct_arr))
                {$errors['us_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

                if(strlen($us_prsn_fll_nm)>255 || strlen($us_prsn_url)>255)
                {$us_prsn_errors++; $errors['us_fll_nm_excss_lngth']='</br>**Understudy (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
              }
              else
              {
                $us_prsn_errors++; $us_prsn_smcln_err_arr[]=$us_prsn_nm;
                $errors['us_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $us_prsn_smcln_err_arr)).'.**';
              }

              if($us_prsn_errors==0)
              {
                $us_prsn_frst_nm_cln=cln($us_prsn_frst_nm);
                $us_prsn_lst_nm_cln=cln($us_prsn_lst_nm);
                $us_prsn_fll_nm_cln=cln($us_prsn_fll_nm);
                $us_prsn_sffx_num_cln=cln($us_prsn_sffx_num);
                $us_prsn_url_cln=cln($us_prsn_url);

                $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                      FROM prsn
                      WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$us_prsn_frst_nm_cln' AND prsn_lst_nm='$us_prsn_lst_nm_cln')
                      AND prsn_fll_nm='$us_prsn_fll_nm_cln' AND prsn_sffx_num='$us_prsn_sffx_num_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for understudy person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['prsn_sffx_num']) {$us_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                  else {$us_prsn_nm_error_sffx_dsply='';}
                  $us_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$us_prsn_nm_error_sffx_dsply;
                  if(count($us_prsn_nm_err_arr)==1)
                  {$errors['us_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $us_prsn_nm_err_arr)).'.**';}
                  else
                  {$errors['us_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $us_prsn_nm_err_arr)).'.**';}
                }

                $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                      FROM prsn
                      WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$us_prsn_fll_nm_cln' AND prsn_sffx_num='$us_prsn_sffx_num_cln')
                      AND prsn_url='$us_prsn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing understudy person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)>0)
                {
                  if($row['prsn_sffx_num']) {$us_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                  else {$us_prsn_url_error_sffx_dsply='';}
                  $us_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$us_prsn_url_error_sffx_dsply;
                  if(count($us_prsn_url_err_arr)==1)
                  {$errors['us_url']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $us_prsn_url_err_arr)).'?**';}
                  else
                  {$errors['us_url']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $us_prsn_url_err_arr)).'?**';}
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $mscn_list))
    {
      if($tr_lg) {$errors['mscn_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $mscn_comp_prsn_rls=explode(',,', $_POST['mscn_list']);
        if(count($mscn_comp_prsn_rls)>250)
        {$errors['mscn_rl_array_excss']='**Maximum of 250 musician roles allowed.**';}
        else
        {
          $mscn_empty_err_arr=array(); $mscn_cln_excss_err_arr=array(); $mscn_cln_err_arr=array();
          $mscn_comp_prsn_empty_err_arr=array(); $mscn_pipe_excss_err_arr=array(); $mscn_comp_tld_excss_err_arr=array();
          $mscn_comp_tld_err_arr=array(); $mscn_pipe_err_arr=array(); $mscn_prsn_tld_excss_err_arr=array();
          $mscn_prsn_tld_err_arr=array(); $mscn_compprsn_rl_empty_err_arr=array(); $mscn_compprsn_tld_excss_err_arr=array();
          $mscn_compprsn_empty_err_arr=array(); $mscn_compprsn_crt_excss_err_arr=array(); $mscn_compprsn_crt_err_arr=array();
          $mscn_compprsn_tld_err_arr=array(); $mscn_comp_hyphn_excss_err_arr=array(); $mscn_comp_hyphn_excss_err_arr=array();
          $mscn_comp_sffx_err_arr=array(); $mscn_comp_hyphn_err_arr=array(); $mscn_comp_dplct_arr=array();
          $mscn_comp_url_err_arr=array(); $mscn_prsn_hyphn_excss_err_arr=array(); $mscn_prsn_sffx_err_arr=array();
          $mscn_prsn_hyphn_err_arr=array(); $mscn_prsn_smcln_excss_err_arr=array(); $mscn_prsn_dplct_arr=array();
          $mscn_prsn_smcln_err_arr=array(); $mscn_prsn_nm_err_arr=array(); $mscn_prsn_url_err_arr=array();
          foreach($mscn_comp_prsn_rls as $mscn_comp_prsn_rl)
          {
            $mscn_comp_prsn_rl=trim($mscn_comp_prsn_rl);

            if(!preg_match('/\S+/', $mscn_comp_prsn_rl))
            {
              $mscn_empty_err_arr[]=$mscn_comp_prsn_rl;
              if(count($mscn_empty_err_arr)==1) {$errors['mscn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['mscn_empty']='</br>**There are '.count($mscn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(preg_match('/\S+/', $mscn_comp_prsn_rl))
              {
                if(substr_count($mscn_comp_prsn_rl, '::')>1)
                {
                  $mscn_cln_excss_err_arr[]=$mscn_comp_prsn_rl;
                  $errors['mscn_cln_excss']='</br>**You may only use [::] once per musician-role coupling. Please amend: '.html(implode(' / ', $mscn_cln_excss_err_arr)).'.**';
                }
                elseif(preg_match('/\S+.*::.*\S+/', $mscn_comp_prsn_rl))
                {
                  list($mscn_rl, $mscn_comp_prsn_list)=explode('::', $mscn_comp_prsn_rl);
                  $mscn_rl=trim($mscn_rl); $mscn_comp_prsn_list=trim($mscn_comp_prsn_list);

                  if(strlen($mscn_rl)>255)
                  {$errors['mscn_rl']='</br>**Musician role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                  $mscn_comps_ppl=explode('>>', $mscn_comp_prsn_list);
                  $mscn_rl_ttl_array=array(); $mscn_comp_nm_array=array(); $mscn_prsn_nm_array=array();
                  foreach($mscn_comps_ppl as $mscn_comp_prsn)
                  {
                    $mscn_comp_errors=0; $mscn_prsn_errors=0;

                    $mscn_comp_prsn=trim($mscn_comp_prsn);
                    if(!preg_match('/\S+/', $mscn_comp_prsn))
                    {
                      $mscn_comp_prsn_empty_err_arr[]=$mscn_comp_prsn;
                      if(count($mscn_comp_prsn_empty_err_arr)==1) {$errors['mscn_comp_prsn_empty']='</br>**There is 1 empty entry in a person arrray (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                      else {$errors['mscn_comp_prsn_empty']='</br>**There are '.count($mscn_comp_prsn_empty_err_arr).' empty entries in person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      if(substr_count($mscn_comp_prsn, '||')>1)
                      {
                        $mscn_prsn_nm_list=''; $mscn_pipe_excss_err_arr[]=$mscn_comp_prsn;
                        $errors['mscn_pipe_excss']='</br>**You may only use [||] once per musician company-members coupling. Please amend: '.html(implode(' / ', $mscn_pipe_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\|\|/', $mscn_comp_prsn))
                      {
                        if(preg_match('/\S+.*\|\|(.*\S+)?/', $mscn_comp_prsn))
                        {
                          list($mscn_comp_nm, $mscn_prsn_nm_list)=explode('||', $mscn_comp_prsn);
                          $mscn_comp_nm=trim($mscn_comp_nm); $mscn_prsn_nm_list=trim($mscn_prsn_nm_list);

                          if(substr_count($mscn_comp_nm, '~~')>1)
                          {
                            $mscn_comp_errors++; $mscn_comp_tld_excss_err_arr[]=$mscn_comp_nm;
                            $errors['mscn_comp_tld_excss']='</br>**You may only use [~~] once per musician (company)-role coupling. Please amend: '.html(implode(' / ', $mscn_comp_tld_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/\S+.*~~.*\S+/', $mscn_comp_nm))
                          {
                            list($mscn_comp_rl, $mscn_comp_nm)=explode('~~', $mscn_comp_nm);
                            $mscn_comp_rl=trim($mscn_comp_rl); $mscn_comp_nm=trim($mscn_comp_nm);

                            if(strlen($mscn_comp_rl)>255)
                            {$mscn_comp_errors++; $errors['mscn_comp_rl']='</br>**Musician (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                          }
                          elseif(substr_count($mscn_comp_nm, '~~')==1)
                          {
                            $mscn_comp_errors++; $mscn_comp_tld_err_arr[]=$mscn_comp_nm;
                            $errors['mscn_comp_tld']='</br>**Musician (company)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $mscn_comp_tld_err_arr)).'**';
                          }

                          if($mscn_comp_errors==0) {$mscn_comp_nm_array[]=$mscn_comp_nm; $mscn_rl_ttl_array[]=$mscn_comp_nm;}
                        }
                        else
                        {
                          $mscn_prsn_nm_list=''; $mscn_pipe_err_arr[]=$mscn_comp_prsn;
                          $errors['mscn_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $mscn_pipe_err_arr)).'.**';
                        }
                      }
                      else
                      {
                        if(substr_count($mscn_comp_prsn, '~~')>1)
                        {
                          $mscn_prsn_errors++; $mscn_prsn_tld_excss_err_arr[]=$mscn_comp_prsn;
                          $errors['mscn_prsn_tld_excss']='</br>**You may only use [~~] once per musician (person)-role coupling. Please amend: '.html(implode(' / ', $mscn_prsn_tld_excss_err_arr)).'.**';
                        }
                        elseif(preg_match('/\S+.*~~.*\S+/', $mscn_comp_prsn))
                        {
                          list($mscn_prsn_rl, $mscn_comp_prsn)=explode('~~', $mscn_comp_prsn);
                          $mscn_prsn_rl=trim($mscn_prsn_rl); $mscn_comp_prsn=trim($mscn_comp_prsn);

                          if(strlen($mscn_prsn_rl)>255)
                          {$mscn_prsn_errors++; $errors['mscn_prsn_rl']='</br>**Musician (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                        }
                        elseif(substr_count($mscn_comp_prsn, '~~')==1)
                        {
                          $mscn_prsn_errors++; $mscn_prsn_tld_err_arr[]=$mscn_comp_prsn;
                          $errors['mscn_prsn_tld']='</br>**Musician (person)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $mscn_prsn_tld_err_arr)).'**';
                        }

                        $mscn_prsn_nm_list='';
                        if($mscn_prsn_errors==0) {$mscn_prsn_nm_array[]=$mscn_comp_prsn; $mscn_rl_ttl_array[]=$mscn_comp_prsn;}
                      }

                      if(preg_match('/\S+/', $mscn_prsn_nm_list))
                      {
                        $mscn_prsn_nms=explode('//', $mscn_prsn_nm_list);
                        foreach($mscn_prsn_nms as $mscn_prsn_nm)
                        {
                          $mscn_prsn_nm=trim($mscn_prsn_nm);
                          if(!preg_match('/\S+/', $mscn_prsn_nm))
                          {
                            $mscn_compprsn_rl_empty_err_arr[]=$mscn_prsn_nm;
                            if(count($mscn_compprsn_rl_empty_err_arr)==1) {$errors['mscn_compprsn_rl_empty']='</br>**There is 1 empty entry in a company member-role array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                            else {$errors['mscn_compprsn_rl_empty']='</br>**There are '.count($mscn_compprsn_rl_empty_err_arr).' empty entries in company member-role arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                          }
                          else
                          {
                            if(substr_count($mscn_prsn_nm, '~~')>1)
                            {
                              $mscn_compprsn_tld_excss_err_arr[]=$mscn_prsn_nm;
                              $errors['mscn_compprsn_tld_excss']='</br>**You may only use [~~] once per musician (company person)-role coupling. Please amend: '.html(implode(' / ', $mscn_compprsn_tld_excss_err_arr)).'.**';
                            }
                            elseif(preg_match('/\S+.*~~.*\S+/', $mscn_prsn_nm))
                            {
                              list($mscn_compprsn_rl, $mscn_prsn_nm)=explode('~~', $mscn_prsn_nm);
                              $mscn_compprsn_rl=trim($mscn_compprsn_rl); $mscn_prsn_nm=trim($mscn_prsn_nm);

                              if(strlen($mscn_compprsn_rl)>255)
                              {$errors['mscn_compprsn_rl']='</br>**Musician (company person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                              $mscn_prsn_nms=explode('Â¬Â¬', $mscn_prsn_nm);
                              foreach($mscn_prsn_nms as $mscn_prsn_nm)
                              {
                                $mscn_prsn_nm=trim($mscn_prsn_nm);
                                if(!preg_match('/\S+/', $mscn_prsn_nm))
                                {
                                  $mscn_compprsn_empty_err_arr[]=$mscn_prsn_nm;
                                  if(count($mscn_compprsn_empty_err_arr)==1) {$errors['mscn_compprsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive logical negation symbols [¬¬¬¬] or two symbols [¬¬] with no text beforehand or thereafter).**';}
                                  else {$errors['mscn_compprsn_empty']='</br>**There are '.count($mscn_compprsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive logical negation symbols [¬¬¬¬] or two symbols [¬¬] with no text beforehand or thereafter).**';}
                                }
                                else
                                {
                                  if(substr_count($mscn_prsn_nm, '^^')>1)
                                  {
                                    $mscn_compprsn_crt_excss_err_arr[]=$mscn_prsn_nm;
                                    $errors['mscn_compprsn_crt_excss']='</br>**You may only use [^^] once per musician-(credit display) role coupling. Please amend: '.html(implode(' / ', $mscn_compprsn_crt_excss_err_arr)).'.**';
                                  }
                                  elseif(preg_match('/\S+.*\^\^.*\S+/', $mscn_prsn_nm))
                                  {
                                    list($mscn_compprsn_sb_rl, $mscn_prsn_nm)=explode('^^', $mscn_prsn_nm);
                                    $mscn_compprsn_sb_rl=trim($mscn_compprsn_sb_rl); $mscn_prsn_nm=trim($mscn_prsn_nm);

                                    if(strlen($mscn_compprsn_sb_rl)>255)
                                    {$errors['mscn_compprsn_sb_rl']='</br>**Musician (company person) sub-role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                                    $mscn_prsn_nm_array[]=$mscn_prsn_nm; $mscn_rl_ttl_array[]=$mscn_prsn_nm;
                                  }
                                  elseif(substr_count($mscn_prsn_nm, '^^')==1)
                                  {$mscn_compprsn_crt_err_arr[]=$mscn_prsn_nm;
                                  $errors['mscn_compprsn_crt']='</br>**Musician (company person)-(credit display) role assignation must use [^^] in the correct format. Please amend: '.html(implode(' / ', $mscn_compprsn_crt_err_arr)).'**';}
                                  else
                                  {$mscn_prsn_nm_array[]=$mscn_prsn_nm; $mscn_rl_ttl_array[]=$mscn_prsn_nm;}
                                }
                              }
                            }
                            else
                            {
                              $mscn_compprsn_tld_err_arr[]=$mscn_prsn_nm;
                              $errors['mscn_compprsn_tld']='</br>**You must assign a company role to the following using [~~]: '.html(implode(' / ', $mscn_compprsn_tld_err_arr)).'.**';
                            }
                          }
                        }
                      }

                      if(count($mscn_rl_ttl_array)>250)
                      {$errors['mscn_rl_ttl_array_excss']='</br>**Maximum of 250 entries (companies and people per role) allowed.**';}
                    }
                  }

                  if(count($mscn_comp_nm_array)>0)
                  {
                    foreach($mscn_comp_nm_array as $mscn_comp_nm)
                    {
                      $mscn_comp_errors=0;

                      if(substr_count($mscn_comp_nm, '--')>1)
                      {
                        $mscn_comp_errors++; $mscn_comp_sffx_num='0'; $mscn_comp_hyphn_excss_err_arr[]=$mscn_comp_nm;
                        $errors['mscn_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per musician (company). Please amend: '.html(implode(' / ', $mscn_comp_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $mscn_comp_nm))
                      {
                        list($mscn_comp_nm_no_sffx, $mscn_comp_sffx_num)=explode('--', $mscn_comp_nm);
                        $mscn_comp_nm_no_sffx=trim($mscn_comp_nm_no_sffx); $mscn_comp_sffx_num=trim($mscn_comp_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $mscn_comp_sffx_num))
                        {
                          $mscn_comp_errors++; $mscn_comp_sffx_num='0'; $mscn_comp_sffx_err_arr[]=$mscn_comp_nm;
                          $errors['mscn_comp_sffx']='</br>**Musician (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $mscn_comp_sffx_err_arr)).'**';
                        }
                        $mscn_comp_nm=$mscn_comp_nm_no_sffx;
                      }
                      elseif(substr_count($mscn_comp_nm, '--')==1)
                      {$mscn_comp_errors++; $mscn_comp_sffx_num='0'; $mscn_comp_hyphn_err_arr[]=$mscn_comp_nm;
                      $errors['mscn_comp_hyphn']='</br>**Musician (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $mscn_comp_hyphn_err_arr)).'**';}
                      else
                      {$mscn_comp_sffx_num='0';}

                      if($mscn_comp_sffx_num) {$mscn_comp_sffx_rmn=' ('.romannumeral($mscn_comp_sffx_num).')';} else {$mscn_comp_sffx_rmn='';}

                      $mscn_comp_url=generateurl($mscn_comp_nm.$mscn_comp_sffx_rmn);

                      $mscn_comp_dplct_arr[]=$mscn_comp_url;
                      if(count(array_unique($mscn_comp_dplct_arr))<count($mscn_comp_dplct_arr))
                      {$errors['mscn_comp_dplct']='</br>**There are entries within the array that create duplicate company URLs.**';}

                      if(strlen($mscn_comp_nm)>255 || strlen($mscn_comp_url)>255)
                      {$mscn_comp_errors++; $errors['mscn_comp_nm_excss_lngth']='</br>**Musician (company) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                      if($mscn_comp_errors==0)
                      {
                        $mscn_comp_nm_cln=cln($mscn_comp_nm);
                        $mscn_comp_sffx_num_cln=cln($mscn_comp_sffx_num);
                        $mscn_comp_url_cln=cln($mscn_comp_url);

                        $sql= "SELECT comp_nm, comp_sffx_num
                              FROM comp
                              WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$mscn_comp_nm_cln' AND comp_sffx_num='$mscn_comp_sffx_num_cln')
                              AND comp_url='$mscn_comp_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing musician company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['comp_sffx_num']) {$mscn_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                          else {$mscn_comp_url_error_sffx_dsply='';}
                          $mscn_comp_url_err_arr[]=$row['comp_nm'].$mscn_comp_url_error_sffx_dsply;
                          if(count($mscn_comp_url_err_arr)==1)
                          {$errors['mscn_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $mscn_comp_url_err_arr)).'?**';}
                          else
                          {$errors['mscn_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $mscn_comp_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }

                  if(count($mscn_prsn_nm_array)>0)
                  {
                    foreach($mscn_prsn_nm_array as $mscn_prsn_nm)
                    {
                      $mscn_prsn_nm=trim($mscn_prsn_nm);
                      $mscn_prsn_errors=0;
                      if(substr_count($mscn_prsn_nm, '--')>1)
                      {
                        $mscn_prsn_errors++; $mscn_prsn_sffx_num='0'; $mscn_prsn_hyphn_excss_err_arr[]=$mscn_prsn_nm;
                        $errors['mscn_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per musician (person). Please amend: '.html(implode(' / ', $mscn_prsn_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $mscn_prsn_nm))
                      {
                        list($mscn_prsn_nm_no_sffx, $mscn_prsn_sffx_num)=explode('--', $mscn_prsn_nm);
                        $mscn_prsn_nm_no_sffx=trim($mscn_prsn_nm_no_sffx); $mscn_prsn_sffx_num=trim($mscn_prsn_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $mscn_prsn_sffx_num))
                        {
                          $mscn_prsn_errors++; $mscn_prsn_sffx_num='0'; $mscn_prsn_sffx_err_arr[]=$mscn_prsn_nm;
                          $errors['mscn_prsn_sffx']='</br>**Musician (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $mscn_prsn_sffx_err_arr)).'**';
                        }
                        $mscn_prsn_nm=$mscn_prsn_nm_no_sffx;
                      }
                      elseif(substr_count($mscn_prsn_nm, '--')==1)
                      {$mscn_prsn_errors++; $mscn_prsn_sffx_num='0'; $mscn_prsn_hyphn_err_arr[]=$mscn_prsn_nm;
                      $errors['mscn_prsn_hyphn']='</br>**Musician (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $mscn_prsn_hyphn_err_arr)).'**';}
                      else
                      {$mscn_prsn_sffx_num='0';}

                      if($mscn_prsn_sffx_num) {$mscn_prsn_sffx_rmn=' ('.romannumeral($mscn_prsn_sffx_num).')';} else {$mscn_prsn_sffx_rmn='';}

                      if(substr_count($mscn_prsn_nm, ';;')>1)
                      {
                        $mscn_prsn_errors++; $mscn_prsn_smcln_excss_err_arr[]=$mscn_prsn_nm;
                        $errors['mscn_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $mscn_prsn_smcln_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*;;(.*\S+)?/', $mscn_prsn_nm))
                      {
                        list($mscn_prsn_frst_nm, $mscn_prsn_lst_nm)=explode(';;', $mscn_prsn_nm);
                        $mscn_prsn_frst_nm=trim($mscn_prsn_frst_nm); $mscn_prsn_lst_nm=trim($mscn_prsn_lst_nm);

                        if(preg_match('/\S+/', $mscn_prsn_lst_nm)) {$mscn_prsn_lst_nm_dsply=' '.$mscn_prsn_lst_nm;}
                        else {$mscn_prsn_lst_nm_dsply='';}

                        $mscn_prsn_fll_nm=$mscn_prsn_frst_nm.$mscn_prsn_lst_nm_dsply;
                        $mscn_prsn_url=generateurl($mscn_prsn_fll_nm.$mscn_prsn_sffx_rmn);

                        $mscn_prsn_dplct_arr[]=$mscn_prsn_url;
                        if(count(array_unique($mscn_prsn_dplct_arr))<count($mscn_prsn_dplct_arr))
                        {$errors['mscn_prsn_dplct']='</br>**There are entries within the array that create duplicate person URLs.**';}

                        if(strlen($mscn_prsn_fll_nm)>255 || strlen($mscn_prsn_url)>255)
                        {$mscn_prsn_errors++; $errors['mscn_prsn_excss_lngth']='</br>**Musician (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                      }
                      else
                      {
                        $mscn_prsn_errors++; $mscn_prsn_smcln_err_arr[]=$mscn_prsn_nm;
                        $errors['mscn_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $mscn_prsn_smcln_err_arr)).'.**';
                      }

                      if($mscn_prsn_errors==0)
                      {
                        $mscn_prsn_frst_nm_cln=cln($mscn_prsn_frst_nm);
                        $mscn_prsn_lst_nm_cln=cln($mscn_prsn_lst_nm);
                        $mscn_prsn_fll_nm_cln=cln($mscn_prsn_fll_nm);
                        $mscn_prsn_sffx_num_cln=cln($mscn_prsn_sffx_num);
                        $mscn_prsn_url_cln=cln($mscn_prsn_url);

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$mscn_prsn_frst_nm_cln' AND prsn_lst_nm='$mscn_prsn_lst_nm_cln')
                              AND prsn_fll_nm='$mscn_prsn_fll_nm_cln' AND prsn_sffx_num='$mscn_prsn_sffx_num_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for musician person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$mscn_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$mscn_prsn_nm_error_sffx_dsply='';}
                          $mscn_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$mscn_prsn_nm_error_sffx_dsply;
                          if(count($mscn_prsn_nm_err_arr)==1)
                          {$errors['mscn_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $mscn_prsn_nm_err_arr)).'.**';}
                          else
                          {$errors['mscn_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $mscn_prsn_nm_err_arr)).'.**';}
                        }

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$mscn_prsn_fll_nm_cln' AND prsn_sffx_num='$mscn_prsn_sffx_num_cln')
                              AND prsn_url='$mscn_prsn_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing musician person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$mscn_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$mscn_prsn_url_error_sffx_dsply='';}
                          $mscn_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$mscn_prsn_url_error_sffx_dsply;
                          if(count($mscn_prsn_url_err_arr)==1)
                          {$errors['mscn_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $mscn_prsn_url_err_arr)).'?**';}
                          else
                          {$errors['mscn_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $mscn_prsn_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }
                }
                else
                {
                  $mscn_cln_err_arr[]=$mscn_comp_prsn_rl;
                  $errors['mscn_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $mscn_cln_err_arr)).'.**';
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $crtv_list))
    {
      if($tr_lg) {$errors['crtv_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $crtv_comp_prsn_rls=explode(',,', $_POST['crtv_list']);
        if(count($crtv_comp_prsn_rls)>250)
        {$errors['crtv_rl_array_excss']='**Maximum of 250 creative roles allowed.**';}
        else
        {
          $crtv_empty_err_arr=array(); $crtv_cln_excss_err_arr=array(); $crtv_cln_err_arr=array();
          $crtv_comp_prsn_empty_err_arr=array(); $crtv_pipe_excss_err_arr=array(); $crtv_pipe_err_arr=array();
          $crtv_prsn_empty_err_arr=array();
          $crtv_comp_tld_excss_err_arr=array();
          $crtv_comp_tld_err_arr=array(); $crtv_comp_hyphn_excss_err_arr=array(); $crtv_comp_hyphn_excss_err_arr=array();
          $crtv_comp_sffx_err_arr=array(); $crtv_comp_hyphn_err_arr=array(); $crtv_comp_url_err_arr=array();
          $crtv_prsn_tld_excss_err_arr=array();
          $crtv_prsn_tld_err_arr=array();
          $crtv_prsn_hyphn_excss_err_arr=array(); $crtv_prsn_sffx_err_arr=array(); $crtv_prsn_hyphn_err_arr=array();
          $crtv_prsn_smcln_excss_err_arr=array(); $crtv_prsn_smcln_err_arr=array(); $crtv_prsn_nm_err_arr=array();
          $crtv_prsn_url_err_arr=array();
          foreach($crtv_comp_prsn_rls as $crtv_comp_prsn_rl)
          {
            $crtv_comp_prsn_rl=trim($crtv_comp_prsn_rl);

            if(!preg_match('/\S+/', $crtv_comp_prsn_rl))
            {
              $crtv_empty_err_arr[]=$crtv_comp_prsn_rl;
              if(count($crtv_empty_err_arr)==1) {$errors['crtv_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['crtv_empty']='</br>**There are '.count($crtv_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(preg_match('/\S+/', $crtv_comp_prsn_rl))
              {
                if(substr_count($crtv_comp_prsn_rl, '::')>1)
                {
                  $crtv_cln_excss_err_arr[]=$crtv_comp_prsn_rl;
                  $errors['crtv_cln_excss']='</br>**You may only use [::] once per creative-role coupling. Please amend: '.html(implode(' / ', $crtv_cln_excss_err_arr)).'.**';
                }
                elseif(preg_match('/\S+.*::.*\S+/', $crtv_comp_prsn_rl))
                {
                  list($crtv_rl, $crtv_comp_prsn_list)=explode('::', $crtv_comp_prsn_rl);
                  $crtv_rl=trim($crtv_rl); $crtv_comp_prsn_list=trim($crtv_comp_prsn_list);

                  if(strlen($crtv_rl)>255)
                  {$errors['crtv_rl']='</br>**Creative role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                  $crtv_comps_ppl=explode('>>', $crtv_comp_prsn_list);
                  $crtv_rl_ttl_array=array(); $crtv_comp_nm_array=array(); $crtv_prsn_nm_array=array();
                  foreach($crtv_comps_ppl as $crtv_comp_prsn)
                  {
                    $crtv_comp_prsn=trim($crtv_comp_prsn);
                    if(!preg_match('/\S+/', $crtv_comp_prsn))
                    {
                      $crtv_comp_prsn_empty_err_arr[]=$crtv_comp_prsn;
                      if(count($crtv_comp_prsn_empty_err_arr)==1) {$errors['crtv_comp_prsn_empty']='</br>**There is 1 empty entry in a person arrray (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                      else {$errors['crtv_comp_prsn_empty']='</br>**There are '.count($crtv_comp_prsn_empty_err_arr).' empty entries in person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      if(substr_count($crtv_comp_prsn, '||')>1)
                      {
                        $crtv_prsn_nm_list=''; $crtv_pipe_excss_err_arr[]=$crtv_comp_prsn;
                        $errors['crtv_pipe_excss']='</br>**You may only use [||] once per creative company-members coupling. Please amend: '.html(implode(' / ', $crtv_pipe_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\|\|/', $crtv_comp_prsn))
                      {
                        if(preg_match('/\S+.*\|\|(.*\S+)?/', $crtv_comp_prsn))
                        {
                          list($crtv_comp_nm, $crtv_prsn_nm_list)=explode('||', $crtv_comp_prsn);
                          $crtv_comp_nm=trim($crtv_comp_nm); $crtv_prsn_nm_list=trim($crtv_prsn_nm_list);
                          $crtv_comp_nm_array[]=$crtv_comp_nm; $crtv_rl_ttl_array[]=$crtv_comp_nm;
                        }
                        else
                        {
                          $crtv_prsn_nm_list=''; $crtv_pipe_err_arr[]=$crtv_comp_prsn;
                          $errors['crtv_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $crtv_pipe_err_arr)).'.**';
                        }
                      }
                      else
                      {
                        $crtv_prsn_nm_array[]=$crtv_comp_prsn; $crtv_rl_ttl_array[]=$crtv_comp_prsn; $crtv_prsn_nm_list='';
                      }

                      if(preg_match('/\S+/', $crtv_prsn_nm_list))
                      {
                        $crtv_prsn_nms=explode('//', $crtv_prsn_nm_list);
                        foreach($crtv_prsn_nms as $crtv_prsn_nm)
                        {
                          $crtv_prsn_nm=trim($crtv_prsn_nm);
                          if(!preg_match('/\S+/', $crtv_prsn_nm))
                          {
                            $crtv_prsn_empty_err_arr[]=$crtv_prsn_nm;
                            if(count($crtv_prsn_empty_err_arr)==1) {$errors['crtv_prsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                            else {$errors['crtv_prsn_empty']='</br>**There are '.count($crtv_prsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                          }
                          else
                          {$crtv_prsn_nm_array[]=$crtv_prsn_nm; $crtv_rl_ttl_array[]=$crtv_prsn_nm;}
                        }
                      }

                      if(count($crtv_rl_ttl_array)>250)
                      {$errors['crtv_rl_ttl_array_excss']='</br>**Maximum of 250 entries (companies and people per role) allowed.**';}
                    }
                  }

                  if(count($crtv_comp_nm_array)>0)
                  {
                    $crtv_comp_dplct_arr=array();
                    foreach($crtv_comp_nm_array as $crtv_comp_nm)
                    {
                      $crtv_comp_nm=trim($crtv_comp_nm);
                      $crtv_comp_errors=0;
                      if(substr_count($crtv_comp_nm, '~~')>1)
                      {
                        $crtv_comp_errors++; $crtv_comp_tld_excss_err_arr[]=$crtv_comp_nm;
                        $errors['crtv_comp_tld_excss']='</br>**You may only use [~~] once per creative (company)-role coupling. Please amend: '.html(implode(' / ', $crtv_comp_tld_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*~~.*\S+/', $crtv_comp_nm))
                      {
                        list($crtv_comp_rl, $crtv_comp_nm)=explode('~~', $crtv_comp_nm);
                        $crtv_comp_rl=trim($crtv_comp_rl); $crtv_comp_nm=trim($crtv_comp_nm);

                        if(strlen($crtv_comp_rl)>255)
                        {$errors['crtv_comp_rl']='</br>**Creative (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($crtv_comp_nm, '~~')==1)
                      {$crtv_comp_errors++; $crtv_comp_tld_err_arr[]=$crtv_comp_nm;
                      $errors['crtv_comp_tld']='</br>**Creative (company)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $crtv_comp_tld_err_arr)).'**';}

                      if(substr_count($crtv_comp_nm, '--')>1)
                      {
                        $crtv_comp_errors++; $crtv_comp_sffx_num='0'; $crtv_comp_hyphn_excss_err_arr[]=$crtv_comp_nm;
                        $errors['crtv_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per creative (company). Please amend: '.html(implode(' / ', $crtv_comp_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $crtv_comp_nm))
                      {
                        list($crtv_comp_nm_no_sffx, $crtv_comp_sffx_num)=explode('--', $crtv_comp_nm);
                        $crtv_comp_nm_no_sffx=trim($crtv_comp_nm_no_sffx); $crtv_comp_sffx_num=trim($crtv_comp_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $crtv_comp_sffx_num))
                        {
                          $crtv_comp_errors++; $crtv_comp_sffx_num='0'; $crtv_comp_sffx_err_arr[]=$crtv_comp_nm;
                          $errors['crtv_comp_sffx']='</br>**Creative (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $crtv_comp_sffx_err_arr)).'**';
                        }
                        $crtv_comp_nm=$crtv_comp_nm_no_sffx;
                      }
                      elseif(substr_count($crtv_comp_nm, '--')==1)
                      {$crtv_comp_errors++; $crtv_comp_sffx_num='0'; $crtv_comp_hyphn_err_arr[]=$crtv_comp_nm;
                      $errors['crtv_comp_hyphn']='</br>**Creative (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $crtv_comp_hyphn_err_arr)).'**';}
                      else
                      {$crtv_comp_sffx_num='0';}

                      if($crtv_comp_sffx_num) {$crtv_comp_sffx_rmn=' ('.romannumeral($crtv_comp_sffx_num).')';} else {$crtv_comp_sffx_rmn='';}

                      $crtv_comp_url=generateurl($crtv_comp_nm.$crtv_comp_sffx_rmn);

                      $crtv_comp_dplct_arr[]=$crtv_comp_url;
                      if(count(array_unique($crtv_comp_dplct_arr))<count($crtv_comp_dplct_arr))
                      {$errors['crtv_comp_dplct']='</br>**There are entries within a role array that create duplicate company URLs.**';}

                      if(strlen($crtv_comp_nm)>255 || strlen($crtv_comp_url)>255)
                      {$crtv_comp_errors++; $errors['crtv_comp_nm_excss_lngth']='</br>**Creative (company) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                      if($crtv_comp_errors==0)
                      {
                        $crtv_comp_nm_cln=cln($crtv_comp_nm);
                        $crtv_comp_sffx_num_cln=cln($crtv_comp_sffx_num);
                        $crtv_comp_url_cln=cln($crtv_comp_url);

                        $sql= "SELECT comp_nm, comp_sffx_num
                              FROM comp
                              WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$crtv_comp_nm_cln' AND comp_sffx_num='$crtv_comp_sffx_num_cln')
                              AND comp_url='$crtv_comp_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing creative company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['comp_sffx_num']) {$crtv_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                          else {$crtv_comp_url_error_sffx_dsply='';}
                          $crtv_comp_url_err_arr[]=$row['comp_nm'].$crtv_comp_url_error_sffx_dsply;
                          if(count($crtv_comp_url_err_arr)==1)
                          {$errors['crtv_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $crtv_comp_url_err_arr)).'?**';}
                          else
                          {$errors['crtv_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $crtv_comp_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }

                  if(count($crtv_prsn_nm_array)> 0)
                  {
                    $crtv_prsn_dplct_arr=array();
                    foreach($crtv_prsn_nm_array as $crtv_prsn_nm)
                    {
                      $crtv_prsn_nm=trim($crtv_prsn_nm);
                      $crtv_prsn_errors=0;
                      if(substr_count($crtv_prsn_nm, '~~')>1)
                      {
                        $crtv_prsn_errors++; $crtv_prsn_tld_excss_err_arr[]=$crtv_prsn_nm;
                        $errors['crtv_prsn_tld_excss']='</br>**You may only use [~~] once per creative (person)-role coupling. Please amend: '.html(implode(' / ', $crtv_prsn_tld_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*~~.*\S+/', $crtv_prsn_nm))
                      {
                        list($crtv_prsn_rl, $crtv_prsn_nm)=explode('~~', $crtv_prsn_nm);
                        $crtv_prsn_rl=trim($crtv_prsn_rl); $crtv_prsn_nm=trim($crtv_prsn_nm);

                        if(strlen($crtv_prsn_rl)>255)
                        {$errors['crtv_prsn_rl']='</br>**Creative (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($crtv_prsn_nm, '~~')==1)
                      {$crtv_prsn_errors++; $crtv_prsn_tld_err_arr[]=$crtv_prsn_nm;
                      $errors['crtv_prsn_tld']='</br>**Creative (person)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $crtv_prsn_tld_err_arr)).'**';}

                      if(substr_count($crtv_prsn_nm, '--')>1)
                      {
                        $crtv_prsn_errors++; $crtv_prsn_sffx_num='0'; $crtv_prsn_hyphn_excss_err_arr[]=$crtv_prsn_nm;
                        $errors['crtv_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per creative (person). Please amend: '.html(implode(' / ', $crtv_prsn_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $crtv_prsn_nm))
                      {
                        list($crtv_prsn_nm_no_sffx, $crtv_prsn_sffx_num)=explode('--', $crtv_prsn_nm);
                        $crtv_prsn_nm_no_sffx=trim($crtv_prsn_nm_no_sffx); $crtv_prsn_sffx_num=trim($crtv_prsn_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $crtv_prsn_sffx_num))
                        {
                          $crtv_prsn_errors++; $crtv_prsn_sffx_num='0'; $crtv_prsn_sffx_err_arr[]=$crtv_prsn_nm;
                          $errors['crtv_prsn_sffx']='</br>**Creative (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $crtv_prsn_sffx_err_arr)).'**';
                        }
                        $crtv_prsn_nm=$crtv_prsn_nm_no_sffx;
                      }
                      elseif(substr_count($crtv_prsn_nm, '--')==1)
                      {$crtv_prsn_errors++; $crtv_prsn_sffx_num='0'; $crtv_prsn_hyphn_err_arr[]=$crtv_prsn_nm;
                      $errors['crtv_prsn_hyphn']='</br>**Creative (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $crtv_prsn_hyphn_err_arr)).'**';}
                      else
                      {$crtv_prsn_sffx_num='0';}

                      if($crtv_prsn_sffx_num) {$crtv_prsn_sffx_rmn=' ('.romannumeral($crtv_prsn_sffx_num).')';} else {$crtv_prsn_sffx_rmn='';}

                      if(substr_count($crtv_prsn_nm, ';;')>1)
                      {
                        $crtv_prsn_errors++; $crtv_prsn_smcln_excss_err_arr[]=$crtv_prsn_nm;
                        $errors['crtv_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $crtv_prsn_smcln_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*;;(.*\S+)?/', $crtv_prsn_nm))
                      {
                        list($crtv_prsn_frst_nm, $crtv_prsn_lst_nm)=explode(';;', $crtv_prsn_nm);
                        $crtv_prsn_frst_nm=trim($crtv_prsn_frst_nm); $crtv_prsn_lst_nm=trim($crtv_prsn_lst_nm);

                        if(preg_match('/\S+/', $crtv_prsn_lst_nm)) {$crtv_prsn_lst_nm_dsply=' '.$crtv_prsn_lst_nm;}
                        else {$crtv_prsn_lst_nm_dsply='';}

                        $crtv_prsn_fll_nm=$crtv_prsn_frst_nm.$crtv_prsn_lst_nm_dsply;
                        $crtv_prsn_url=generateurl($crtv_prsn_fll_nm.$crtv_prsn_sffx_rmn);

                        $crtv_prsn_dplct_arr[]=$crtv_prsn_url;
                        if(count(array_unique($crtv_prsn_dplct_arr))<count($crtv_prsn_dplct_arr))
                        {$errors['crtv_prsn_dplct']='</br>**There are entries within a role array that create duplicate person URLs.**';}

                        if(strlen($crtv_prsn_fll_nm)>255 || strlen($crtv_prsn_url)>255)
                        {$crtv_prsn_errors++; $errors['crtv_prsn_excss_lngth']='</br>**Creative (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                      }
                      else
                      {
                        $crtv_prsn_errors++; $crtv_prsn_smcln_err_arr[]=$crtv_prsn_nm;
                        $errors['crtv_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $crtv_prsn_smcln_err_arr)).'.**';
                      }

                      if($crtv_prsn_errors==0)
                      {
                        $crtv_prsn_frst_nm_cln=cln($crtv_prsn_frst_nm);
                        $crtv_prsn_lst_nm_cln=cln($crtv_prsn_lst_nm);
                        $crtv_prsn_fll_nm_cln=cln($crtv_prsn_fll_nm);
                        $crtv_prsn_sffx_num_cln=cln($crtv_prsn_sffx_num);
                        $crtv_prsn_url_cln=cln($crtv_prsn_url);

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$crtv_prsn_frst_nm_cln' AND prsn_lst_nm='$crtv_prsn_lst_nm_cln')
                              AND prsn_fll_nm='$crtv_prsn_fll_nm_cln' AND prsn_sffx_num='$crtv_prsn_sffx_num_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for creative person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$crtv_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$crtv_prsn_nm_error_sffx_dsply='';}
                          $crtv_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$crtv_prsn_nm_error_sffx_dsply;
                          if(count($crtv_prsn_nm_err_arr)==1)
                          {$errors['crtv_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $crtv_prsn_nm_err_arr)).'.**';}
                          else
                          {$errors['crtv_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $crtv_prsn_nm_err_arr)).'.**';}
                        }

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$crtv_prsn_fll_nm_cln' AND prsn_sffx_num='$crtv_prsn_sffx_num_cln')
                              AND prsn_url='$crtv_prsn_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing creative person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$crtv_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$crtv_prsn_url_error_sffx_dsply='';}
                          $crtv_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$crtv_prsn_url_error_sffx_dsply;
                          if(count($crtv_prsn_url_err_arr)==1)
                          {$errors['crtv_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $crtv_prsn_url_err_arr)).'?**';}
                          else
                          {$errors['crtv_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $crtv_prsn_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }
                }
                else
                {
                  $crtv_cln_err_arr[]=$crtv_comp_prsn_rl;
                  $errors['crtv_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $crtv_cln_err_arr)).'.**';
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $prdtm_list))
    {
      if($tr_lg) {$errors['prdtm_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $prdtm_comp_prsn_rls=explode(',,', $_POST['prdtm_list']);
        if(count($prdtm_comp_prsn_rls)>250)
        {$errors['prdtm_rl_array_excss']='**Maximum of 250 production team roles allowed.**';}
        else
        {
          $prdtm_empty_err_arr=array(); $prdtm_cln_excss_err_arr=array(); $prdtm_cln_err_arr=array();
          $prdtm_comp_prsn_empty_err_arr=array(); $prdtm_pipe_excss_err_arr=array(); $prdtm_pipe_err_arr=array();
          $prdtm_prsn_empty_err_arr=array(); $prdtm_comp_tld_excss_err_arr=array(); $prdtm_comp_tld_err_arr=array();
          $prdtm_comp_hyphn_excss_err_arr=array(); $prdtm_comp_hyphn_excss_err_arr=array(); $prdtm_comp_sffx_err_arr=array();
          $prdtm_comp_hyphn_err_arr=array(); $prdtm_comp_url_err_arr=array(); $prdtm_prsn_tld_excss_err_arr=array();
          $prdtm_prsn_tld_err_arr=array(); $prdtm_prsn_hyphn_excss_err_arr=array(); $prdtm_prsn_sffx_err_arr=array();
          $prdtm_prsn_hyphn_err_arr=array(); $prdtm_prsn_smcln_excss_err_arr=array(); $prdtm_prsn_smcln_err_arr=array();
          $prdtm_prsn_nm_err_arr=array(); $prdtm_prsn_url_err_arr=array();
          foreach($prdtm_comp_prsn_rls as $prdtm_comp_prsn_rl)
          {
            $prdtm_comp_prsn_rl=trim($prdtm_comp_prsn_rl);

            if(!preg_match('/\S+/', $prdtm_comp_prsn_rl))
            {
              $prdtm_empty_err_arr[]=$prdtm_comp_prsn_rl;
              if(count($prdtm_empty_err_arr)==1) {$errors['prdtm_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['prdtm_empty']='</br>**There are '.count($prdtm_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(preg_match('/\S+/', $prdtm_comp_prsn_rl))
              {
                if(substr_count($prdtm_comp_prsn_rl, '::')>1)
                {
                  $prdtm_cln_excss_err_arr[]=$prdtm_comp_prsn_rl;
                  $errors['prdtm_cln_excss']='</br>**You may only use [::] once per creative-role coupling. Please amend: '.html(implode(' / ', $prdtm_cln_excss_err_arr)).'.**';
                }
                elseif(preg_match('/\S+.*::.*\S+/', $prdtm_comp_prsn_rl))
                {
                  list($prdtm_rl, $prdtm_comp_prsn_list)=explode('::', $prdtm_comp_prsn_rl);
                  $prdtm_rl=trim($prdtm_rl); $prdtm_comp_prsn_list=trim($prdtm_comp_prsn_list);

                  if(strlen($prdtm_rl)>255)
                  {$errors['prdtm_rl']='</br>**Production team role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                  $prdtm_comps_ppl=explode('>>', $prdtm_comp_prsn_list);
                  $prdtm_rl_ttl_array=array(); $prdtm_comp_nm_array=array(); $prdtm_prsn_nm_array=array();
                  foreach($prdtm_comps_ppl as $prdtm_comp_prsn)
                  {
                    $prdtm_comp_prsn=trim($prdtm_comp_prsn);
                    if(!preg_match('/\S+/', $prdtm_comp_prsn))
                    {
                      $prdtm_comp_prsn_empty_err_arr[]=$prdtm_comp_prsn;
                      if(count($prdtm_comp_prsn_empty_err_arr)==1) {$errors['prdtm_comp_prsn_empty']='</br>**There is 1 empty entry in a person arrray (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                      else {$errors['prdtm_comp_prsn_empty']='</br>**There are '.count($prdtm_comp_prsn_empty_err_arr).' empty entries in person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      if(substr_count($prdtm_comp_prsn, '||')>1)
                      {
                        $prdtm_prsn_nm_list=''; $prdtm_pipe_excss_err_arr[]=$prdtm_comp_prsn;
                        $errors['prdtm_pipe_excss']='</br>**You may only use [||] once per production team company-members coupling. Please amend: '.html(implode(' / ', $prdtm_pipe_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\|\|/', $prdtm_comp_prsn))
                      {
                        if(preg_match('/\S+.*\|\|(.*\S+)?/', $prdtm_comp_prsn))
                        {
                          list($prdtm_comp_nm, $prdtm_prsn_nm_list)=explode('||', $prdtm_comp_prsn);
                          $prdtm_comp_nm=trim($prdtm_comp_nm); $prdtm_prsn_nm_list=trim($prdtm_prsn_nm_list);
                          $prdtm_comp_nm_array[]=$prdtm_comp_nm; $prdtm_rl_ttl_array[]=$prdtm_comp_nm;
                        }
                        else
                        {
                          $prdtm_prsn_nm_list=''; $prdtm_pipe_err_arr[]=$prdtm_comp_prsn;
                          $errors['prdtm_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $prdtm_pipe_err_arr)).'.**';
                        }
                      }
                      else
                      {
                        $prdtm_prsn_nm_array[]=$prdtm_comp_prsn; $prdtm_rl_ttl_array[]=$prdtm_comp_prsn; $prdtm_prsn_nm_list='';
                      }

                      if(preg_match('/\S+/', $prdtm_prsn_nm_list))
                      {
                        $prdtm_prsn_nms=explode('//', $prdtm_prsn_nm_list);
                        foreach($prdtm_prsn_nms as $prdtm_prsn_nm)
                        {
                          $prdtm_prsn_nm=trim($prdtm_prsn_nm);
                          if(!preg_match('/\S+/', $prdtm_prsn_nm))
                          {
                            $prdtm_prsn_empty_err_arr[]=$prdtm_prsn_nm;
                            if(count($prdtm_prsn_empty_err_arr)==1) {$errors['prdtm_prsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                            else {$errors['prdtm_prsn_empty']='</br>**There are '.count($prdtm_prsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                          }
                          else
                          {$prdtm_prsn_nm_array[]=$prdtm_prsn_nm; $prdtm_rl_ttl_array[]=$prdtm_prsn_nm;}
                        }
                      }

                      if(count($prdtm_rl_ttl_array)>250)
                      {$errors['prdtm_rl_ttl_array_excss']='</br>**Maximum of 250 entries (companies and people per role) allowed.**';}
                    }
                  }

                  if(count($prdtm_comp_nm_array)>0)
                  {
                    $prdtm_comp_dplct_arr=array();
                    foreach($prdtm_comp_nm_array as $prdtm_comp_nm)
                    {
                      $prdtm_comp_nm=trim($prdtm_comp_nm);
                      $prdtm_comp_errors=0;
                      if(substr_count($prdtm_comp_nm, '~~')>1)
                      {
                        $prdtm_comp_errors++; $prdtm_comp_tld_excss_err_arr[]=$prdtm_comp_nm;
                        $errors['prdtm_comp_tld_excss']='</br>**You may only use [~~] once per production team (company)-role coupling. Please amend: '.html(implode(' / ', $prdtm_comp_tld_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*~~.*\S+/', $prdtm_comp_nm))
                      {
                        list($prdtm_comp_rl, $prdtm_comp_nm)=explode('~~', $prdtm_comp_nm);
                        $prdtm_comp_rl=trim($prdtm_comp_rl); $prdtm_comp_nm=trim($prdtm_comp_nm);

                        if(strlen($prdtm_comp_rl)>255)
                        {$errors['prdtm_comp_rl']='</br>**Production team (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($prdtm_comp_nm, '~~')==1)
                      {$prdtm_comp_errors++; $prdtm_comp_tld_err_arr[]=$prdtm_comp_nm;
                      $errors['prdtm_comp_tld']='</br>**Production team (company)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $prdtm_comp_tld_err_arr)).'**';}

                      if(substr_count($prdtm_comp_nm, '--')>1)
                      {
                        $prdtm_comp_errors++; $prdtm_comp_sffx_num='0'; $prdtm_comp_hyphn_excss_err_arr[]=$prdtm_comp_nm;
                        $errors['prdtm_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per production team (company). Please amend: '.html(implode(' / ', $prdtm_comp_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $prdtm_comp_nm))
                      {
                        list($prdtm_comp_nm_no_sffx, $prdtm_comp_sffx_num)=explode('--', $prdtm_comp_nm);
                        $prdtm_comp_nm_no_sffx=trim($prdtm_comp_nm_no_sffx); $prdtm_comp_sffx_num=trim($prdtm_comp_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $prdtm_comp_sffx_num))
                        {
                          $prdtm_comp_errors++; $prdtm_comp_sffx_num='0'; $prdtm_comp_sffx_err_arr[]=$prdtm_comp_nm;
                          $errors['prdtm_comp_sffx']='</br>**Production team (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $prdtm_comp_sffx_err_arr)).'**';
                        }
                        $prdtm_comp_nm=$prdtm_comp_nm_no_sffx;
                      }
                      elseif(substr_count($prdtm_comp_nm, '--')==1)
                      {$prdtm_comp_errors++; $prdtm_comp_sffx_num='0'; $prdtm_comp_hyphn_err_arr[]=$prdtm_comp_nm;
                      $errors['prdtm_comp_hyphn']='</br>**Production team (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $prdtm_comp_hyphn_err_arr)).'**';}
                      else
                      {$prdtm_comp_sffx_num='0';}

                      if($prdtm_comp_sffx_num) {$prdtm_comp_sffx_rmn=' ('.romannumeral($prdtm_comp_sffx_num).')';} else {$prdtm_comp_sffx_rmn='';}

                      $prdtm_comp_url=generateurl($prdtm_comp_nm.$prdtm_comp_sffx_rmn);

                      $prdtm_comp_dplct_arr[]=$prdtm_comp_url;
                      if(count(array_unique($prdtm_comp_dplct_arr))<count($prdtm_comp_dplct_arr))
                      {$errors['prdtm_comp_dplct']='</br>**There are entries within a role array that create duplicate company URLs.**';}

                      if(strlen($prdtm_comp_nm)>255 || strlen($prdtm_comp_url)>255)
                      {$prdtm_comp_errors++; $errors['prdtm_comp_nm_excss_lngth']='</br>**Production team (company) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                      if($prdtm_comp_errors==0)
                      {
                        $prdtm_comp_nm_cln=cln($prdtm_comp_nm);
                        $prdtm_comp_sffx_num_cln=cln($prdtm_comp_sffx_num);
                        $prdtm_comp_url_cln=cln($prdtm_comp_url);

                        $sql= "SELECT comp_nm, comp_sffx_num
                              FROM comp
                              WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$prdtm_comp_nm_cln' AND comp_sffx_num='$prdtm_comp_sffx_num_cln')
                              AND comp_url='$prdtm_comp_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing production team company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['comp_sffx_num']) {$prdtm_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                          else {$prdtm_comp_url_error_sffx_dsply='';}
                          $prdtm_comp_url_err_arr[]=$row['comp_nm'].$prdtm_comp_url_error_sffx_dsply;
                          if(count($prdtm_comp_url_err_arr)==1)
                          {$errors['prdtm_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $prdtm_comp_url_err_arr)).'?**';}
                          else
                          {$errors['prdtm_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $prdtm_comp_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }

                  if(count($prdtm_prsn_nm_array)> 0)
                  {
                    $prdtm_prsn_dplct_arr=array();
                    foreach($prdtm_prsn_nm_array as $prdtm_prsn_nm)
                    {
                      $prdtm_prsn_nm=trim($prdtm_prsn_nm);
                      $prdtm_prsn_errors=0;
                      if(substr_count($prdtm_prsn_nm, '~~')>1)
                      {
                        $prdtm_prsn_errors++; $prdtm_prsn_tld_excss_err_arr[]=$prdtm_prsn_nm;
                        $errors['prdtm_prsn_tld_excss']='</br>**You may only use [~~] once per production team (person)-role coupling. Please amend: '.html(implode(' / ', $prdtm_prsn_tld_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*~~.*\S+/', $prdtm_prsn_nm))
                      {
                        list($prdtm_prsn_rl, $prdtm_prsn_nm)=explode('~~', $prdtm_prsn_nm);
                        $prdtm_prsn_rl=trim($prdtm_prsn_rl); $prdtm_prsn_nm=trim($prdtm_prsn_nm);

                        if(strlen($prdtm_prsn_rl)>255)
                        {$errors['prdtm_prsn_rl']='</br>**Production team (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($prdtm_prsn_nm, '~~')==1)
                      {$prdtm_prsn_errors++; $prdtm_prsn_tld_err_arr[]=$prdtm_prsn_nm;
                      $errors['prdtm_prsn_tld']='</br>**Production team (person)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $prdtm_prsn_tld_err_arr)).'**';}

                      if(substr_count($prdtm_prsn_nm, '--')>1)
                      {
                        $prdtm_prsn_errors++; $prdtm_prsn_sffx_num='0'; $prdtm_prsn_hyphn_excss_err_arr[]=$prdtm_prsn_nm;
                        $errors['prdtm_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per production team (person). Please amend: '.html(implode(' / ', $prdtm_prsn_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $prdtm_prsn_nm))
                      {
                        list($prdtm_prsn_nm_no_sffx, $prdtm_prsn_sffx_num)=explode('--', $prdtm_prsn_nm);
                        $prdtm_prsn_nm_no_sffx=trim($prdtm_prsn_nm_no_sffx); $prdtm_prsn_sffx_num=trim($prdtm_prsn_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $prdtm_prsn_sffx_num))
                        {
                          $prdtm_prsn_errors++; $prdtm_prsn_sffx_num='0'; $prdtm_prsn_sffx_err_arr[]=$prdtm_prsn_nm;
                          $errors['prdtm_prsn_sffx']='</br>**Production team (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $prdtm_prsn_sffx_err_arr)).'**';
                        }
                        $prdtm_prsn_nm=$prdtm_prsn_nm_no_sffx;
                      }
                      elseif(substr_count($prdtm_prsn_nm, '--')==1)
                      {$prdtm_prsn_errors++; $prdtm_prsn_sffx_num='0'; $prdtm_prsn_hyphn_err_arr[]=$prdtm_prsn_nm;
                      $errors['prdtm_prsn_hyphn']='</br>**Production team (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $prdtm_prsn_hyphn_err_arr)).'**';}
                      else
                      {$prdtm_prsn_sffx_num='0';}

                      if($prdtm_prsn_sffx_num) {$prdtm_prsn_sffx_rmn=' ('.romannumeral($prdtm_prsn_sffx_num).')';} else {$prdtm_prsn_sffx_rmn='';}

                      if(substr_count($prdtm_prsn_nm, ';;')>1)
                      {
                        $prdtm_prsn_errors++; $prdtm_prsn_smcln_excss_err_arr[]=$prdtm_prsn_nm;
                        $errors['prdtm_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $prdtm_prsn_smcln_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*;;(.*\S+)?/', $prdtm_prsn_nm))
                      {
                        list($prdtm_prsn_frst_nm, $prdtm_prsn_lst_nm)=explode(';;', $prdtm_prsn_nm);
                        $prdtm_prsn_frst_nm=trim($prdtm_prsn_frst_nm); $prdtm_prsn_lst_nm=trim($prdtm_prsn_lst_nm);

                        if(preg_match('/\S+/', $prdtm_prsn_lst_nm)) {$prdtm_prsn_lst_nm_dsply=' '.$prdtm_prsn_lst_nm;}
                        else {$prdtm_prsn_lst_nm_dsply='';}

                        $prdtm_prsn_fll_nm=$prdtm_prsn_frst_nm.$prdtm_prsn_lst_nm_dsply;
                        $prdtm_prsn_url=generateurl($prdtm_prsn_fll_nm.$prdtm_prsn_sffx_rmn);

                        $prdtm_prsn_dplct_arr[]=$prdtm_prsn_url;
                        if(count(array_unique($prdtm_prsn_dplct_arr))<count($prdtm_prsn_dplct_arr))
                        {$errors['prdtm_prsn_dplct']='</br>**There are entries within a role array that create duplicate person URLs.**';}

                        if(strlen($prdtm_prsn_fll_nm)>255 || strlen($prdtm_prsn_url)>255)
                        {$prdtm_prsn_errors++; $errors['prdtm_prsn_excss_lngth']='</br>**Production team (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                      }
                      else
                      {
                        $prdtm_prsn_errors++; $prdtm_prsn_smcln_err_arr[]=$prdtm_prsn_nm;
                        $errors['prdtm_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $prdtm_prsn_smcln_err_arr)).'.**';
                      }

                      if($prdtm_prsn_errors==0)
                      {
                        $prdtm_prsn_frst_nm_cln=cln($prdtm_prsn_frst_nm);
                        $prdtm_prsn_lst_nm_cln=cln($prdtm_prsn_lst_nm);
                        $prdtm_prsn_fll_nm_cln=cln($prdtm_prsn_fll_nm);
                        $prdtm_prsn_sffx_num_cln=cln($prdtm_prsn_sffx_num);
                        $prdtm_prsn_url_cln=cln($prdtm_prsn_url);

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$prdtm_prsn_frst_nm_cln' AND prsn_lst_nm='$prdtm_prsn_lst_nm_cln')
                              AND prsn_fll_nm='$prdtm_prsn_fll_nm_cln' AND prsn_sffx_num='$prdtm_prsn_sffx_num_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for production team person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$prdtm_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$prdtm_prsn_nm_error_sffx_dsply='';}
                          $prdtm_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prdtm_prsn_nm_error_sffx_dsply;
                          if(count($prdtm_prsn_nm_err_arr)==1)
                          {$errors['prdtm_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $prdtm_prsn_nm_err_arr)).'.**';}
                          else
                          {$errors['prdtm_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $prdtm_prsn_nm_err_arr)).'.**';}
                        }

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$prdtm_prsn_fll_nm_cln' AND prsn_sffx_num='$prdtm_prsn_sffx_num_cln')
                              AND prsn_url='$prdtm_prsn_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing production team person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$prdtm_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$prdtm_prsn_url_error_sffx_dsply='';}
                          $prdtm_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prdtm_prsn_url_error_sffx_dsply;
                          if(count($prdtm_prsn_url_err_arr)==1)
                          {$errors['prdtm_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $prdtm_prsn_url_err_arr)).'?**';}
                          else
                          {$errors['prdtm_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $prdtm_prsn_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }
                }
                else
                {
                  $prdtm_cln_err_arr[]=$prdtm_comp_prsn_rl;
                  $errors['prdtm_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $prdtm_cln_err_arr)).'.**';
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $ssn_nm))
    {
      if(strlen($ssn_nm)>255)
      {$errors['ssn_nm']='</br>**Season is allowed a maximum of 255 characters.**';}
      else
      {
        $ssn_url=generateurl($ssn_nm);
        $sql= "SELECT ssn_nm
              FROM ssn
              WHERE NOT EXISTS (SELECT 1 FROM ssn WHERE ssn_nm='$ssn_nm')
              AND ssn_url='$ssn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existing season URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        if(mysqli_num_rows($result)>0)
        {$errors['ssn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html($row['ssn_nm']).'?**';}
      }
    }

    if(preg_match('/\S+/', $fstvl_nm))
    {
      if(strlen($fstvl_nm)>255)
      {$errors['fstvl_nm']='</br>**Festival is allowed a maximum of 255 characters.**';}
      else
      {
        $fstvl_url=generateurl($fstvl_nm);
        $sql= "SELECT fstvl_nm
              FROM fstvl
              WHERE NOT EXISTS (SELECT 1 FROM fstvl WHERE fstvl_nm='$fstvl_nm')
              AND fstvl_url='$fstvl_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existing festival URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        if(mysqli_num_rows($result)>0)
        {$errors['fstvl_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html($row['fstvl_nm']).'?**';}
      }
    }

    if(preg_match('/\S+/', $crs_list))
    {
      if($tr_lg || $prd_clss!=='3')
      {
        if($tr_lg) {$errors['crs_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
        if($prd_clss!=='3') {$errors['crs_prd_clss_chckd']='</br>**This field must be empty unless Drama School production class button is applied.**';}
      }
      else
      {
        $crs_nms=explode(',,', $crs_list);
        if(count($crs_nms)>250)
        {$errors['crs_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $crs_empty_err_arr=array(); $crs_hsh_excss_err_arr=array(); $crs_hyphn_excss_err_arr=array();
          $crs_sffx_err_arr=array(); $crs_hyphn_err_arr=array(); $crs_dt_err_arr=array();
          $crs_dt_frmt_err_arr=array(); $crs_cln_excss_err_arr=array(); $crs_schl_hyphn_excss_err_arr=array();
          $crs_schl_sffx_err_arr=array(); $crs_schl_hyphn_err_arr=array(); $crs_schl_url_err_arr=array();
          $crs_typ_url_err_arr=array(); $crs_dplct_arr=array(); $crs_cln_err_arr=array();
          $crs_hsh_err_arr=array();
          foreach($crs_nms as $crs_schl_typ_yr)
          {
            $crs_schl_typ_yr=trim($crs_schl_typ_yr);
            if(!preg_match('/\S+/', $crs_schl_typ_yr))
            {
              $crs_empty_err_arr[]=$crs_schl_typ_yr;
              if(count($crs_empty_err_arr)==1) {$errors['crs_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['crs_empty']='</br>**There are '.count($crs_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($crs_schl_typ_yr, '##')>1)
              {
                $crs_hsh_excss_err_arr[]=$crs_schl_typ_yr;
                $errors['crs_hsh_excss']='</br>**You may only use [##] for year assignment once per course. Please amend: '.html(implode(' / ', $crs_hsh_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*##.*\S+$/', $crs_schl_typ_yr))
              {
                list($crs_schl_typ, $crs_yr)=explode('##', $crs_schl_typ_yr);
                $crs_schl_typ=trim($crs_schl_typ); $crs_yr=trim($crs_yr);

                if(substr_count($crs_yr, '--')>1)
                {
                  $crs_sffx_num='0'; $crs_hyphn_excss_err_arr[]=$crs_yr;
                  $errors['crs_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per course. Please amend: '.html(implode(' / ', $crs_hyphn_excss_err_arr)).'.**';
                }
                elseif(preg_match('/^\S+.*--.+$/', $crs_yr))
                {
                  list($crs_schl_typ_yr_no_sffx, $crs_sffx_num)=explode('--', $crs_yr);
                  $crs_schl_typ_yr_no_sffx=trim($crs_schl_typ_yr_no_sffx); $crs_sffx_num=trim($crs_sffx_num);

                  if(!preg_match('/^[1-9][0-9]{0,1}$/', $crs_sffx_num))
                  {
                    $crs_sffx_num='0'; $crs_sffx_err_arr[]=$crs_sffx_num;
                    $errors['crs_sffx']='</br>**Course suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $crs_sffx_err_arr)).'**';
                  }
                  $crs_yr=$crs_schl_typ_yr_no_sffx;
                }
                elseif(substr_count($crs_yr, '--')==1)
                {$crs_sffx_num='0'; $crs_hyphn_err_arr[]=$crs_yr;
                $errors['crs_hyphn']='</br>**Course suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $crs_hyphn_err_arr)).'**';}
                else
                {$crs_sffx_num='0';}

                if($crs_sffx_num) {$crs_sffx_rmn=' ('.romannumeral($crs_sffx_num).')';} else {$crs_sffx_rmn='';}

                if(preg_match('/^[1-9][0-9]{3}(\s*;;\s*[1-9][0-9]{3})?$/', $crs_yr))
                {
                  if(preg_match('/^[1-9][0-9]{3}\s*;;\s*[1-9][0-9]{3}$/', $crs_yr))
                  {
                    list($crs_yr_strt, $crs_yr_end)=explode(';;', $crs_yr);
                    $crs_yr_strt=trim($crs_yr_strt); $crs_yr_end=trim($crs_yr_end);

                    if($crs_yr_strt >= $crs_yr_end)
                    {
                      $crs_dt_err_arr[]=$crs_schl_typ_yr; $crs_yr_url=NULL;
                      $errors['crs_dt']='</br>**Course year started must be earlier than course year ended. Please amend: '.html(implode(' / ', $crs_dt_err_arr)).'.**';
                    }
                    else
                    {$crs_yr_url=$crs_yr_strt.'-'. $crs_yr_end;}
                  }
                  else
                  {$crs_yr_strt=$crs_yr; $crs_yr_end=$crs_yr; $crs_yr_url=$crs_yr;}
                }
                else
                {
                  $crs_dt_frmt_err_arr[]=$crs_schl_typ_yr; $crs_yr_url=NULL;
                  $errors['crs_dt_frmt']='</br>**Course year(s) must be given in the correct format. Please amend: '.html(implode(' / ', $crs_dt_frmt_err_arr)).'.**';
                }

                if(substr_count($crs_schl_typ, '::')>1)
                {
                  $crs_cln_excss_err_arr[]=$crs_schl_typ;
                  $errors['crs_cln_excss']='</br>**You may only use [::] for drama school-course type assignment once per course. Please amend: '.html(implode(' / ', $crs_cln_excss_err_arr)).'.**';
                }
                elseif(preg_match('/^\S+.*::.*\S+$/', $crs_schl_typ))
                {
                  $crs_schl_errors=0; $crs_typ_errors=0;

                  list($crs_schl_nm, $crs_typ_nm)=explode('::', $crs_schl_typ);
                  $crs_schl_nm=trim($crs_schl_nm); $crs_typ_nm=trim($crs_typ_nm);

                  if(substr_count($crs_schl_nm, '--')>1)
                  {
                    $crs_schl_errors++; $crs_schl_sffx_num='0'; $crs_schl_hyphn_excss_err_arr[]=$crs_schl_nm;
                    $errors['crs_schl_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per course school (company). Please amend: '.html(implode(' / ', $crs_schl_hyphn_excss_err_arr)).'.**';
                  }
                  elseif(preg_match('/^\S+.*--.+$/', $crs_schl_nm))
                  {
                    list($crs_schl_no_sffx, $crs_schl_sffx_num)=explode('--', $crs_schl_nm);
                    $crs_schl_no_sffx=trim($crs_schl_no_sffx); $crs_schl_sffx_num=trim($crs_schl_sffx_num);

                    if(!preg_match('/^[1-9][0-9]{0,1}$/', $crs_schl_sffx_num))
                    {
                      $crs_schl_errors++; $crs_schl_sffx_num='0'; $crs_schl_sffx_err_arr[]=$crs_schl_nm;
                      $errors['crs_schl_sffx']='</br>**Course school (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $crs_schl_sffx_err_arr)).'**';
                    }
                    $crs_schl_nm=$crs_schl_no_sffx;
                  }
                  elseif(substr_count($crs_schl_nm, '--')==1)
                  {$crs_schl_errors++; $crs_schl_sffx_num='0'; $crs_schl_hyphn_err_arr[]=$crs_schl_nm;
                  $errors['crs_schl_hyphn']='</br>**Course school (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $crs_schl_hyphn_err_arr)).'**';}
                  else
                  {$crs_schl_sffx_num='0';}

                  if($crs_schl_sffx_num) {$crs_schl_sffx_rmn=' ('.romannumeral($crs_schl_sffx_num).')';} else {$crs_schl_sffx_rmn='';}

                  $crs_schl_url=generateurl($crs_schl_nm);

                  if(strlen($crs_schl_nm)>255 || strlen($crs_schl_url)>255)
                  {$crs_schl_errors++; $errors['crs_schl_excss_lngth']='</br>**Drama school is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                  if($crs_schl_errors==0)
                  {
                    $crs_schl_nm_cln=cln($crs_schl_nm);
                    $crs_schl_sffx_num_cln=cln($crs_schl_sffx_num);
                    $crs_schl_url_cln=cln($crs_schl_url);

                    $sql= "SELECT comp_nm, comp_sffx_num
                          FROM comp
                          WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$crs_schl_nm_cln' AND comp_sffx_num='$crs_schl_sffx_num_cln')
                          AND comp_url='$crs_schl_url_cln'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existing company (drama school) URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      if($row['comp_sffx_num']) {$crs_schl_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                      else {$crs_schl_url_error_sffx_dsply='';}
                      $crs_schl_url_err_arr[]=$row['comp_nm'].$crs_schl_url_error_sffx_dsply;
                      if(count($crs_schl_url_err_arr)==1)
                      {$errors['crs_schl_nm']='</br>**Duplicate drama school URL exists. Did you mean to type: '.html(implode(' / ', $crs_schl_url_err_arr)).'?**';}
                      else
                      {$errors['crs_schl_nm']='</br>**Duplicate drama school URLs exist. Did you mean to type: '.html(implode(' / ', $crs_schl_url_err_arr)).'?**';}
                    }
                  }

                  $crs_typ_url=generateurl($crs_typ_nm);

                  if(strlen($crs_typ_nm)>255)
                  {$crs_typ_errors++; $errors['crs_typ_excss_lngth']='</br>**Course type is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                  if($crs_typ_errors==0)
                  {
                    $crs_typ_nm_cln=cln($crs_typ_nm);
                    $crs_typ_url_cln=cln($crs_typ_url);

                    $sql= "SELECT crs_typ_nm
                          FROM crs_typ
                          WHERE NOT EXISTS (SELECT 1 FROM crs_typ WHERE crs_typ_nm='$crs_typ_nm_cln')
                          AND crs_typ_url='$crs_typ_url_cln'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existing drama school URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      $crs_typ_url_err_arr[]=$row['crs_typ_nm'];
                      if(count($crs_typ_url_err_arr)==1)
                      {$errors['crs_typ_nm']='</br>**Duplicate course type URL exists. Did you mean to type: '.html(implode(' / ', $crs_typ_url_err_arr)).'?**';}
                      else
                      {$errors['crs_typ_nm']='</br>**Duplicate course type URLs exist. Did you mean to type: '.html(implode(' / ', $crs_typ_url_err_arr)).'?**';}
                    }
                  }

                  if($crs_yr_url && $crs_schl_errors==0 && $crs_typ_errors==0)
                  {
                    $crs_dplct_arr[]=$crs_schl_url.' '.$crs_typ_url.' '.$crs_yr_url;
                    if(count(array_unique($crs_dplct_arr))<count($crs_dplct_arr))
                    {$errors['crs_dplct']='</br>**There are entries within the array that create duplicate courses.**';}
                  }
                }
                else
                {
                  $crs_cln_err_arr[]=$crs_schl_typ;
                  $errors['crs_cln']='</br>**You must assign a course school and type in the correct format to the following using [::]: '.html(implode(' / ', $crs_cln_err_arr)).'.**';
                }
              }
              else
              {
                $crs_hsh_err_arr[]=$crs_schl_typ_yr;
                $errors['crs_hsh']='</br>**You must assign a course year(s) in the correct format to the following using [##]: '.html(implode(' / ', $crs_hsh_err_arr)).'.**';
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $rvw_list))
    {
      $rvw_pub_crtc_dt_urls=explode(',,', $_POST['rvw_list']);
      if(count($rvw_pub_crtc_dt_urls)>250)
      {$errors['rvw_list_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rvw_empty_err_arr=array(); $rvw_cln_excss_err_arr=array(); $rvw_url_dplct_arr=array();
        $rvw_url_err_arr=array(); $rvw_hsh_excss_err_arr=array(); $rvw_dt_err_arr=array();
        $rvw_dt_frmt_err_arr=array(); $rvw_pipes_excss_err_arr=array(); $rvw_pub_hyphn_excss_err_arr=array();
        $rvw_pub_sffx_err_arr=array(); $rvw_pub_hyphn_err_arr=array(); $rvw_pub_comp_url_err_arr=array();
        $rvw_crtc_hyphn_excss_err_arr=array(); $rvw_crtc_sffx_err_arr=array(); $rvw_crtc_hyphn_err_arr=array();
        $rvw_crtc_smcln_excss_err_arr=array(); $rvw_crtc_smcln_err_arr=array(); $rvw_crtc_nm_err_arr=array();
        $rvw_crtc_url_err_arr=array(); $rvw_pipes_err_arr=array(); $rvw_hsh_err_arr=array();
        $rvw_cln_err_arr=array();
        foreach($rvw_pub_crtc_dt_urls as $rvw_pub_crtc_dt_url)
        {
          if(!preg_match('/\S+/', $rvw_pub_crtc_dt_url))
          {
            $rvw_empty_err_arr[]=$rvw_pub_crtc_dt_url;
            if(count($rvw_empty_err_arr)==1) {$errors['rvw_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rvw_empty']='</br>**There are '.count($rvw_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($rvw_pub_crtc_dt_url, '::')>1)
            {
              $rvw_cln_excss_err_arr[]=$rvw_pub_crtc_dt_url;
              $errors['rvw_cln_excss']='</br>**You may only use [::] once per review entry. Please amend: '.html(implode(' / ', $rvw_cln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*::.*\S+$/', $rvw_pub_crtc_dt_url))
            {
              list($rvw_pub_crtc_dt, $rvw_url)=explode('::', $rvw_pub_crtc_dt_url);
              $rvw_pub_crtc_dt=trim($rvw_pub_crtc_dt); $rvw_url=trim($rvw_url);

              if(preg_match('/http(s)?\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\S*)?\s*$/', $rvw_url))
              {
                $rvw_url_dplct_arr[]=$rvw_url;
                if(count(array_unique($rvw_url_dplct_arr))<count($rvw_url_dplct_arr))
                {$errors['rvw_url_dplct']='</br>**There are duplicate review URLs given in the array.**';}

                if(strlen($rvw_url)>255)
                {$errors['rvw_url_excss_lngth']='</br>**Review URL is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
              }
              else
              {
                $rvw_url_err_arr[]=$rvw_url;
                $errors['rvw_url']='</br>**The following are not valid URLs: '.html(implode(' / ', $rvw_url_err_arr)).'.**';
              }

              if(substr_count($rvw_pub_crtc_dt, '##')>1)
              {
                $rvw_hsh_excss_err_arr[]=$rvw_pub_crtc_dt;
                $errors['rvw_hsh_excss']='</br>**You may only use [##] once per review entry. Please amend: '.html(implode(' / ', $rvw_hsh_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*##.*\S+$/', $rvw_pub_crtc_dt))
              {
                list($rvw_pub_crtc, $rvw_dt)=explode('##', $rvw_pub_crtc_dt);
                $rvw_pub_crtc=trim($rvw_pub_crtc); $rvw_dt=trim($rvw_dt);

                if(preg_match('/^[0-9]{2}-[0-9]{2}-[0-9]{4}$/', $rvw_dt))
                {
                  list($rvw_dt_DD, $rvw_dt_MM, $rvw_dt_YYYY)=explode('-', $rvw_dt);
                  if(!checkdate((int)$rvw_dt_MM, (int)$rvw_dt_DD, (int)$rvw_dt_YYYY))
                  {
                    $rvw_dt_err_arr[]=$rvw_pub_crtc_dt;
                    $errors['rvw_dt']='</br>**The following do not contain valid review dates: '.html(implode(' / ', $rvw_dt_err_arr)).'.**';
                  }
                  else
                  {$rvw_dt=$rvw_dt_YYYY.$rvw_dt_MM.$rvw_dt_DD;}
                }
                else
                {
                  $rvw_dt_frmt_err_arr[]=$rvw_pub_crtc_dt;
                  $errors['rvw_dt_frmt']='</br>**The following do not contain valid review dates (must adhere to format: [DD]-[MM]-[YYYY]): '.html(implode(' / ', $rvw_dt_frmt_err_arr)).'.**';
                }

                $rvw_pub_errors=0; $rvw_crtc_errors=0;

                if(substr_count($rvw_pub_crtc, '||')>1)
                {
                  $rvw_pipes_excss_err_arr[]=$rvw_pub_crtc;
                  $errors['rvw_pipes_excss']='</br>**You may only use [||] once per review entry. Please amend: '.html(implode(' / ', $rvw_pipes_excss_err_arr)).'.**';
                }
                elseif(preg_match('/\S+.*\|\|.*\S+/', $rvw_pub_crtc))
                {
                  list($rvw_pub_nm, $rvw_crtc)=explode('||', $rvw_pub_crtc);
                  $rvw_pub_nm=trim($rvw_pub_nm); $rvw_crtc=trim($rvw_crtc);

                  if(substr_count($rvw_pub_nm, '--')>1)
                  {
                    $rvw_pub_errors++; $rvw_pub_sffx_num='0'; $rvw_pub_hyphn_excss_err_arr[]=$rvw_pub_nm;
                    $errors['rvw_pub_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per review publication (company). Please amend: '.html(implode(' / ', $rvw_pub_hyphn_excss_err_arr)).'.**';
                  }
                  elseif(preg_match('/^\S+.*--.+$/', $rvw_pub_nm))
                  {
                    list($rvw_pub_nm_no_sffx, $rvw_pub_sffx_num)=explode('--', $rvw_pub_nm);
                    $rvw_pub_nm_no_sffx=trim($rvw_pub_nm_no_sffx); $rvw_pub_sffx_num=trim($rvw_pub_sffx_num);

                    if(!preg_match('/^[1-9][0-9]{0,1}$/', $rvw_pub_sffx_num))
                    {
                      $rvw_pub_errors++; $rvw_pub_sffx_num='0'; $rvw_pub_sffx_err_arr[]=$rvw_pub_nm;
                      $errors['rvw_pub_sffx']='</br>**Review publication (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $rvw_pub_sffx_err_arr)).'**';
                    }
                    $rvw_pub_nm=$rvw_pub_nm_no_sffx;
                  }
                  elseif(substr_count($rvw_pub_nm, '--')==1)
                  {$rvw_pub_errors++; $rvw_pub_sffx_num='0'; $rvw_pub_hyphn_err_arr[]=$rvw_pub_nm;
                  $errors['rvw_pub_hyphn']='</br>**Review publication (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $rvw_pub_hyphn_err_arr)).'**';}
                  else
                  {$rvw_pub_sffx_num='0';}

                  if($rvw_pub_sffx_num) {$rvw_pub_sffx_rmn=' ('.romannumeral($rvw_pub_sffx_num).')';} else {$rvw_pub_sffx_rmn='';}

                  $rvw_pub_url=generateurl($rvw_pub_nm.$rvw_pub_sffx_rmn);

                  if(strlen($rvw_pub_nm)>255 || strlen($rvw_pub_url)>255)
                  {$rvw_pub_errors++; $errors['rvw_pub_excss_lngth']='</br>**Publication and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                  if($rvw_pub_errors==0)
                  {
                    $rvw_pub_nm_cln=cln($rvw_pub_nm);
                    $rvw_pub_sffx_num_cln=cln($rvw_pub_sffx_num);
                    $rvw_pub_url_cln=cln($rvw_pub_url);

                    $sql= "SELECT comp_nm, comp_sffx_num
                          FROM comp
                          WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$rvw_pub_nm_cln' AND comp_sffx_num='$rvw_pub_sffx_num_cln')
                          AND comp_url='$rvw_pub_url_cln'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existing publication company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      if($row['comp_sffx_num']) {$rvw_pub_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                      else {$rvw_pub_comp_url_error_sffx_dsply='';}
                      $rvw_pub_comp_url_err_arr[]=$row['comp_nm'].$rvw_pub_comp_url_error_sffx_dsply;
                      if(count($rvw_pub_comp_url_err_arr)==1)
                      {$errors['rvw_pub_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $rvw_pub_comp_url_err_arr)).'?**';}
                      else
                      {$errors['rvw_pub_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $rvw_pub_comp_url_err_arr)).'?**';}
                    }
                  }

                  if(substr_count($rvw_crtc, '--')>1)
                  {
                    $rvw_crtc_errors++; $rvw_crtc_sffx_num='0'; $rvw_crtc_hyphn_excss_err_arr[]=$rvw_crtc;
                    $errors['rvw_crtc_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per review critic (person). Please amend: '.html(implode(' / ', $rvw_crtc_hyphn_excss_err_arr)).'.**';
                  }
                  elseif(preg_match('/^\S+.*--.+$/', $rvw_crtc))
                  {
                    list($rvw_crtc_no_sffx, $rvw_crtc_sffx_num)=explode('--', $rvw_crtc);
                    $rvw_crtc_no_sffx=trim($rvw_crtc_no_sffx); $rvw_crtc_sffx_num=trim($rvw_crtc_sffx_num);

                    if(!preg_match('/^[1-9][0-9]{0,1}$/', $rvw_crtc_sffx_num))
                    {
                      $rvw_crtc_errors++; $rvw_crtc_sffx_num='0'; $rvw_crtc_sffx_err_arr[]=$rvw_crtc;
                      $errors['rvw_crtc_sffx']='</br>**Review critic (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $rvw_crtc_sffx_err_arr)).'**';
                    }
                    $rvw_crtc=$rvw_crtc_no_sffx;
                  }
                  elseif(substr_count($rvw_crtc, '--')==1)
                  {$rvw_crtc_errors++; $rvw_crtc_sffx_num='0'; $rvw_crtc_hyphn_err_arr[]=$rvw_crtc;
                  $errors['rvw_crtc_hyphn']='</br>**Review critic (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $rvw_crtc_hyphn_err_arr)).'**';}
                  else
                  {$rvw_crtc_sffx_num='0';}

                  if($rvw_crtc_sffx_num) {$rvw_crtc_sffx_rmn=' ('.romannumeral($rvw_crtc_sffx_num).')';} else {$rvw_crtc_sffx_rmn='';}

                  if(substr_count($rvw_crtc, ';;')>1)
                  {
                    $rvw_crtc_errors++; $rvw_crtc_smcln_excss_err_arr[]=$rvw_crtc;
                    $errors['rvw_crtc_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $rvw_crtc_smcln_excss_err_arr)).'.**';
                  }
                  elseif(preg_match('/\S+.*;;(.*\S+)?/', $rvw_crtc))
                  {
                    list($rvw_crtc_frst_nm, $rvw_crtc_lst_nm)=explode(';;', $rvw_crtc);
                    $rvw_crtc_frst_nm=trim($rvw_crtc_frst_nm); $rvw_crtc_lst_nm=trim($rvw_crtc_lst_nm);

                    if(preg_match('/\S+/', $rvw_crtc_lst_nm)) {$rvw_crtc_lst_nm_dsply=' '.$rvw_crtc_lst_nm;}
                    else {$rvw_crtc_lst_nm_dsply='';}

                    $rvw_crtc_fll_nm=$rvw_crtc_frst_nm.$rvw_crtc_lst_nm_dsply;
                    $rvw_crtc_url=generateurl($rvw_crtc_fll_nm.$rvw_crtc_sffx_rmn);

                    if(strlen($rvw_crtc_fll_nm)>255 || strlen($rvw_crtc_url)>255)
                    {$rvw_crtc_errors++; $errors['rvw_crtc_fll_nm_excss_lngth']='</br>**Critic name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                  }
                  else
                  {
                    $rvw_crtc_errors++; $rvw_crtc_smcln_err_arr[]=$rvw_crtc;
                    $errors['rvw_crtc_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $rvw_crtc_smcln_err_arr)).'.**';
                  }

                  if($rvw_crtc_errors==0)
                  {
                    $rvw_crtc_frst_nm_cln=cln($rvw_crtc_frst_nm);
                    $rvw_crtc_lst_nm_cln=cln($rvw_crtc_lst_nm);
                    $rvw_crtc_fll_nm_cln=cln($rvw_crtc_fll_nm);
                    $rvw_crtc_sffx_num_cln=cln($rvw_crtc_sffx_num);
                    $rvw_crtc_url_cln=cln($rvw_crtc_url);

                    $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                          FROM prsn
                          WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$rvw_crtc_frst_nm_cln' AND prsn_lst_nm='$rvw_crtc_lst_nm_cln')
                          AND prsn_fll_nm='$rvw_crtc_fll_nm_cln' AND prsn_sffx_num='$rvw_crtc_sffx_num_cln'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for critic person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      if($row['prsn_sffx_num']) {$rvw_crtc_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                      else {$rvw_crtc_nm_error_sffx_dsply='';}
                      $rvw_crtc_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$rvw_crtc_nm_error_sffx_dsply;
                      if(count($rvw_crtc_nm_err_arr)==1)
                      {$errors['rvw_crtc_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $rvw_crtc_nm_err_arr)).'.**';}
                      else
                      {$errors['rvw_crtc_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $rvw_crtc_nm_err_arr)).'.**';}
                    }

                    $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                          FROM prsn
                          WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$rvw_crtc_fll_nm_cln' AND prsn_sffx_num='$rvw_crtc_sffx_num_cln')
                          AND prsn_url='$rvw_crtc_url_cln'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existing critic person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      if($row['prsn_sffx_num']) {$rvw_crtc_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                      else {$rvw_crtc_url_error_sffx_dsply='';}
                      $rvw_crtc_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$rvw_crtc_url_error_sffx_dsply;
                      if(count($rvw_crtc_url_err_arr)==1)
                      {$errors['rvw_crtc_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $rvw_crtc_url_err_arr)).'?**';}
                      else
                      {$errors['rvw_crtc_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $rvw_crtc_url_err_arr)).'?**';}
                    }
                  }
                }
                else
                {
                  $rvw_pipes_err_arr[]=$rvw_pub_crtc;
                  $errors['rvw_pipes']='</br>**You must assign a publication and corresponding critic to the following using [||]: '.html(implode(' / ', $rvw_pipes_err_arr)).'.**';
                }
              }
              else
              {
                $rvw_hsh_err_arr[]=$rvw_pub_crtc_dt;
                $errors['rvw_hsh']='</br>**You must assign a review date in the prescribed format ([DD]-[MM]-[YYYY]) to the following using [##]: '.html(implode(' / ', $rvw_hsh_err_arr)).'.**';
              }
            }
            else
            {
              $rvw_cln_err_arr[]=$rvw_pub_crtc_dt_url;
              $errors['rvw_cln']='</br>**You must assign a URL to the following using [::]: '.html(implode(' / ', $rvw_cln_err_arr)).'.**';
            }
          }
        }
      }
    }
?>