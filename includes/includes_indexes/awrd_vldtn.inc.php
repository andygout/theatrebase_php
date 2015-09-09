<?php
    $awrds_nm=trim(cln($_POST['awrds_nm']));
    $awrd_yr=trim(cln($_POST['awrd_yr']));
    $awrd_yr_end=trim(cln($_POST['awrd_yr_end']));
    $awrd_dt=trim(cln($_POST['awrd_dt']));
    $thtr_nm=trim(cln($_POST['thtr_nm']));
    $awrd_list=cln($_POST['awrd_list']);

    $errors=array();

    if(!preg_match('/\S+/', $awrds_nm))
    {$errors['awrds_nm']='**You must enter an awards name.**';}
    elseif(strlen($awrds_nm)>255)
    {$errors['awrds_nm']='</br>**Awards name is allowed a maximum of 255 characters.**';}
    else
    {
      $awrds_url=generateurl($awrds_nm);
      $awrds_alph=alph($awrds_nm);

      $sql= "SELECT awrds_nm
            FROM awrds
            WHERE NOT EXISTS (SELECT 1 FROM awrds WHERE awrds_nm='$awrds_nm')
            AND awrds_url='$awrds_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing awards URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0)
      {$errors['awrds_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html($row['awrds_nm']).'?**';}
    }

    if(!preg_match('/^[1-9][0-9]{3}$/', $awrd_yr))
    {$errors['awrd_yr']='**You must enter a valid year.**'; $awrd_yr_num=NULL;}
    else
    {$awrd_yr_num=$awrd_yr;}

    if($awrd_yr_end)
    {
      if(!preg_match('/^[1-9][0-9]{3}$/', $awrd_yr_end))
      {$errors['awrd_yr_end']='**You must enter a valid year or leave blank.**'; $awrd_yr_end_num=NULL;}
      else
      {$awrd_yr_end_num=$awrd_yr_end;}
    }
    else
    {$awrd_yr_end_num=NULL;}

    if($awrd_yr_num && $awrd_yr_end_num)
    {
      $diff=$awrd_yr_end_num - $awrd_yr_num;
      if($awrd_yr_num >= $awrd_yr_end_num)
      {$errors['awrd_yr']='**Must be earlier than award year (#2).**'; $errors['awrd_yr_end']='**Must be later than award year.**';}
      elseif($diff!==1)
      {$errors['awrd_yr']='**Must be no earlier than one year before award year (#2).**'; $errors['awrd_yr_end']='**Must be no later than one year after award year.**';}
    }

    if(count($errors)==0)
    {
      if(!$awrd_yr_end) {$awrd_yr_url=$awrd_yr; $awrd_session=$_POST['awrds_nm'].' '.$_POST['awrd_yr'];}
      else {$awrd_yr_url=$awrd_yr.'-'.$awrd_yr_end;
      $awrd_yr_end_session=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $_POST['awrd_yr_end']);
      $awrd_session=$_POST['awrds_nm'].' '.$_POST['awrd_yr'].$awrd_yr_end_session;}

      $sql= "SELECT awrds_nm, awrd_id, awrd_yr, awrd_yr_end
            FROM awrds
            INNER JOIN awrd ON awrds_id=awrdsid
            WHERE awrds_url='$awrds_url' AND awrd_yr='$awrd_yr'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing awards-award year combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['awrd_id']!==$awrd_id)
      {
        if($row['awrd_yr_end']) {$awrd_yr_end_cmb=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end_cmb='';}
        $errors['awrds_nm_awrd_yr']='</br>**Given award name and year already exists for: '.html($row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_cmb).'**';
      }
    }

    if($awrd_dt)
    {
      if(!preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $awrd_dt))
      {$errors['awrd_dt']='**You must enter a valid awards date in the prescribed format or leave empty.**'; $awrd_dt=NULL;}
      else
      {
        list($awrd_dt_YYYY, $awrd_dt_MM, $awrd_dt_DD)=explode('-', $awrd_dt);
        if(!checkdate((int)$awrd_dt_MM, (int)$awrd_dt_DD, (int)$awrd_dt_YYYY))
        {$errors['awrd_dt']='**You must enter a valid awards date or leave empty.**'; $awrd_dt=NULL;}
      }
    }
    else
    {$awrd_dt=NULL;}

    $thtr_errors=0;
    $thtr=$thtr_nm;
    if(substr_count($thtr, '--')>1) {$thtr_errors++; $thtr_sffx_num='0'; $errors['thtr_hyphn_excss']='</br>**You may only use [--] for theatre suffix assignment once.**';}
    elseif(preg_match('/^\S+.*--.+$/', $thtr))
    {
      list($thtr, $thtr_sffx_num)=explode('--', $thtr); $thtr=trim($thtr); $thtr_sffx_num=trim($thtr_sffx_num);
      if(!preg_match('/^[1-9][0-9]{0,1}$/', $thtr_sffx_num)) {$thtr_errors++; $thtr_sffx_num='0'; $errors['thtr_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend.**';}
    }
    elseif(substr_count($thtr, '--')==1) {$thtr_errors++; $thtr_sffx_num='0'; $thtr_sffx_rmn=''; $errors['thtr_hyphn']='</br>**Venue suffix assignation must use [--] in the correct format.**';}
    else {$thtr_sffx_num='0';}

    if($thtr_sffx_num) {$thtr_sffx_rmn=' ('.romannumeral($thtr_sffx_num).')';} else {$thtr_sffx_rmn='';}

    if(substr_count($thtr, '::')>1) {$thtr_errors++; $errors['thtr_cln_excss']='</br>**You may only use [::] once per theatre-location coupling.**'; $thtr_lctn=NULL; $thtr_lctn_dsply='';}
    elseif(preg_match('/\S+.*::.*\S+/', $thtr)) {list($thtr, $thtr_lctn)=explode('::', $thtr); $thtr=trim($thtr); $thtr_lctn=trim($thtr_lctn); $thtr_lctn_dsply=' ('.$thtr_lctn.')';}
    else {$thtr_lctn=NULL; $thtr_lctn_dsply='';}

    if(substr_count($thtr, ';;')>1) {$thtr_errors++; $errors['thtr_smcln_excss']='</br>**You may only use [;;] once per theatre-subtheatre coupling.**'; $sbthtr_nm=NULL; $sbthtr_nm_dsply='';}
    elseif(preg_match('/\S+.*;;.*\S+/', $thtr)) {list($thtr, $sbthtr_nm)=explode(';;', $thtr); $thtr=trim($thtr); $sbthtr_nm=trim($sbthtr_nm); $sbthtr_nm_dsply=': '.$sbthtr_nm;}
    else {$sbthtr_nm=NULL; $sbthtr_nm_dsply='';}

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
        if(mysqli_num_rows($result)>0) {$errors['thtr_tr_ov']='**You are not allowed to give a theatre that is assigned as a tour overview.**';}
      }
    }

    if(preg_match('/\S+/', $awrd_list))
    {
      $awrd_ctgrys=explode('@@', $_POST['awrd_list']);
      if(count($awrd_ctgrys)>250)
      {$errors['awrd_ctgry_array_excss']='**Maximum of 250 categories allowed.**';}
      else
      {
        $awrd_empty_err_arr=array(); $awrd_eql_excss_err_arr=array(); $awrd_ctgry_dtl_excss_err_arr=array();
        $awrd_ctgry_alt_nm_smcln_err_arr=array(); $awrd_ctgry_dplct_arr=array(); $awrd_ctgry_url_err_arr=array();
        $awrd_nom_dtl_dscr_empty_err_arr=array(); $awrd_nom_dtl_dscr_cln_excss_err_arr=array(); $awrd_nom_dtl_pls_excss_err_arr=array();
        $nom_pt_empty_err_arr=array(); $nom_pt_hyphn_excss_err_arr=array(); $nom_pt_sffx_err_arr=array();
        $nom_pt_hyphn_err_arr=array(); $nom_pt_hsh_excss_err_arr=array(); $nom_pt_yr_err_arr=array();
        $nom_pt_yr_frmt_err_arr=array(); $nom_pt_hsh_err_arr=array(); $nom_pt_url_err_arr=array();
        $awrd_nom_dtl_pls_err_arr=array(); $awrd_nom_dtl_hsh_excss_err_arr=array(); $awrd_nom_prd_empty_err_arr=array();
        $awrd_nom_prd_nonnmrcl_err_arr=array(); $awrd_nom_prd_nonexst_err_arr=array(); $awrd_nom_dtl_hsh_err_arr=array();
        $awrd_nom_prd_pt_err_arr=array(); $awrd_ctgry_prd_pt_err_arr=array(); $awrd_nom_prsn_empty_err_arr=array();
        $awrd_pipe_excss_err_arr=array(); $awrd_pipe_err_arr=array(); $awrd_comp_tld_excss_err_arr=array();
        $awrd_comp_tld_err_arr=array(); $awrd_comp_hyphn_excss_err_arr=array(); $awrd_comp_sffx_err_arr=array();
        $awrd_comp_hyphn_err_arr=array(); $awrd_comp_url_err_arr=array(); $awrd_prsn_empty_err_arr=array();
        $awrd_prsn_tld_excss_err_arr=array(); $awrd_prsn_tld_err_arr=array(); $awrd_prsn_hyphn_excss_err_arr=array();
        $awrd_prsn_sffx_err_arr=array(); $awrd_prsn_hyphn_err_arr=array(); $awrd_prsn_smcln_excss_err_arr=array();
        $awrd_prsn_smcln_err_arr=array(); $awrd_prsn_nm_err_arr=array(); $awrd_prsn_url_err_arr=array();
        $awrd_nom_dtl_dscr_cln_err_arr=array(); $awrd_eql_err_arr=array();
        foreach($awrd_ctgrys as $awrd_ctgry)
        {
          $awrd_ctgry_errors=0;
          $awrd_ctgry=trim($awrd_ctgry);
          if(!preg_match('/\S+/', $awrd_ctgry))
          {
            $awrd_empty_err_arr[]=$awrd_ctgry;
            if(count($awrd_empty_err_arr)==1) {$errors['awrd_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
            else {$errors['awrd_empty']='</br>**There are '.count($awrd_empty_err_arr).' empty entries in the string (caused by four consecutive at symbols [@@@@] or two at symbols [@@] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($awrd_ctgry, '==')>1)
            {
              $awrd_eql_excss_err_arr[]=$awrd_ctgry;
              $errors['awrd_eql_excss']='</br>**You may only use [==] once per category-nominees/winners listing. Please amend: '.html(implode(' / ', $awrd_eql_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*==.*\S+/', $awrd_ctgry))
            {
              list($awrd_ctgry_nm, $awrd_nom_dtl_dscr_list)=explode('==', $awrd_ctgry);
              $awrd_ctgry_nm=trim($awrd_ctgry_nm); $awrd_nom_dtl_dscr_list=trim($awrd_nom_dtl_dscr_list);

              if(substr_count($awrd_ctgry_nm, ';;')>1)
              {
                $awrd_ctgry_errors++; $awrd_ctgry_dtl_excss_err_arr[]=$awrd_ctgry_nm;
                $errors['awrd_ctgry_dtl_smcln_excss']='</br>**You may only use [;;] once per category name-alternate name. Please amend: '.html(implode(' / ', $awrd_ctgry_dtl_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*;;.*\S+/', $awrd_ctgry_nm))
              {
                list($awrd_ctgry_nm, $awrd_ctgry_alt_nm)=explode(';;', $awrd_ctgry_nm);
                $awrd_ctgry_nm=trim($awrd_ctgry_nm); $awrd_ctgry_alt_nm=trim($awrd_ctgry_alt_nm);

                if(strlen($awrd_ctgry_alt_nm)>255)
                {$errors['awrd_ctgry_alt_nm']='</br>**Award category alternate name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
              }
              elseif(substr_count($awrd_ctgry_nm, ';;')==1)
              {$awrd_ctgry_errors++; $awrd_ctgry_alt_nm_smcln_err_arr[]=$awrd_ctgry_nm;
              $errors['awrd_ctgry_alt_nm_smcln']='</br>**Award name-award alternate name assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $awrd_ctgry_alt_nm_smcln_err_arr)).'**';}

              if($awrd_ctgry_errors==0)
              {
                $awrd_ctgry_url=generateurl($awrd_ctgry_nm);

                $awrd_ctgry_dplct_arr[]=$awrd_ctgry_url;
                if(count(array_unique($awrd_ctgry_dplct_arr))<count($awrd_ctgry_dplct_arr))
                {$errors['awrd_ctgry_dplct']='</br>**There are entries within the array that create duplicate award category URLs.**';}

                if(strlen($awrd_ctgry_nm)>255)
                {$errors['awrd_ctgry_nm']='</br>**Award category name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                else
                {
                  $awrd_ctgry_nm_cln=cln($awrd_ctgry_nm);
                  $awrd_ctgry_url_cln=cln($awrd_ctgry_url);

                  $sql= "SELECT awrd_ctgry_nm
                        FROM awrd_ctgry
                        WHERE NOT EXISTS (SELECT 1 FROM awrd_ctgry WHERE awrd_ctgry_nm='$awrd_ctgry_nm_cln')
                        AND awrd_ctgry_url='$awrd_ctgry_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing award category URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $awrd_ctgry_url_err_arr[]=$row['awrd_ctgry_nm'];
                    $awrd_ctgry_url_error_list=implode(' / ', $awrd_ctgry_url_err_arr);
                    if(count($awrd_ctgry_url_err_arr)==1)
                    {$errors['awrd_ctgry_nm']='</br>**Duplicate category URL exists. Did you mean to type: '.html($awrd_ctgry_url_error_list).'?**';}
                    else
                    {$errors['awrd_ctgry_nm']='</br>**Duplicate category URLs exist. Did you mean to type: '.html($awrd_ctgry_url_error_list).'?**';}
                  }
                }
              }

              $awrd_nom_dtl_dscrs=explode(',,', $awrd_nom_dtl_dscr_list);
              if(count($awrd_nom_dtl_dscrs)>250)
              {$errors['awrd_nom_array_excss']='**Maximum of 250 nominations per category allowed.**';}

              $awrd_ctgrys=explode('@@', $_POST['awrd_list']);
              if(count($awrd_ctgrys)>250)
              {$errors['awrd_ctgry_array_excss']='**Maximum of 250 categories allowed.**';}

              $ctgry_prd_count_array=array(); $ctgry_pt_count_array=array();
              foreach($awrd_nom_dtl_dscrs as $awrd_nom_dtl_dscr)
              {
                $awrd_nom_dtl_dscr=trim($awrd_nom_dtl_dscr);
                if(!preg_match('/\S+/', $awrd_nom_dtl_dscr))
                {
                  $awrd_nom_dtl_dscr_empty_err_arr[]=$awrd_nom_dtl_dscr;
                  if(count($awrd_nom_dtl_dscr_empty_err_arr)==1) {$errors['awrd_nom_dtl_dscr_empty']='</br>**There is 1 empty entry in the nominations string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                  else {$errors['awrd_nom_dtl_dscr_empty']='</br>**There are '.count($awrd_nom_dtl_dscr_empty_err_arr).' empty entries in the nominations string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
                }
                else
                {
                  if(substr_count($awrd_nom_dtl_dscr, '::')>1)
                  {
                    $awrd_nom_dtl_dscr_cln_excss_err_arr[]=$awrd_nom_dtl_dscr;
                    $errors['awrd_nom_dtl_dscr_cln_excss']='</br>**You may only use [::] once per nomination-description assignation. Please amend: '.html(implode(' / ', $awrd_nom_dtl_dscr_cln_excss_err_arr)).'.**';
                  }
                  elseif(preg_match('/\S+.*::.*\S+/', $awrd_nom_dtl_dscr))
                  {
                    list($awrd_nom_dscr, $awrd_nom_ppl_prds_pts)=explode('::', $awrd_nom_dtl_dscr);
                    $awrd_nom_dscr=trim($awrd_nom_dscr); $awrd_nom_ppl_prds_pts=trim($awrd_nom_ppl_prds_pts);

                    if(preg_match('/^\S+.*\*$/', $awrd_nom_dscr))
                    {$awrd_nom_dscr=preg_replace('/(\S+.*)(\*)/', '$1', $awrd_nom_dscr); $win_bool='1';} else {$win_bool='0';}
                    $awrd_nom_dscr=trim($awrd_nom_dscr);

                    if(strlen($awrd_nom_dscr)>255)
                    {$errors['awrd_nom_dscr_excss_lngth']='</br>**Award nomination description (Nominee, Winner, etc.) is allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                    if(substr_count($awrd_nom_ppl_prds_pts, '++')>1)
                    {
                      $awrd_nom_dtl_pls_excss_err_arr[]=$awrd_nom_ppl_prds_pts; $awrd_nom_ppl_prds=''; $awrd_nom_pts='';
                      $errors['awrd_nom_dtl_pls_excss']='</br>**You may only use [++] once per playtext assignation. Please amend: '.html(implode(' / ', $awrd_nom_dtl_pls_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/(\S+.*)?\+\+.*\S+/', $awrd_nom_ppl_prds_pts))
                    {
                      list($awrd_nom_ppl_prds, $awrd_nom_pts)=explode('++', $awrd_nom_ppl_prds_pts);
                      $awrd_nom_ppl_prds=trim($awrd_nom_ppl_prds); $awrd_nom_pts=trim($awrd_nom_pts);

                      $nom_pts=explode('>>', $awrd_nom_pts);
                      if(count($nom_pts)>250)
                      {$errors['nom_pts_array_excss']='</br>**Maximum of 250 playtexts allowed per nomination. Please amend those that exceed this amount.**';}
                      else
                      {
                        $nom_pt_dplct_arr=array();
                        foreach($nom_pts as $nom_pt_nm_yr)
                        {
                          $nom_pt_errors=0;

                          if(!preg_match('/\S+/', $nom_pt_nm_yr))
                          {
                            $nom_pt_empty_err_arr[]=$nom_pt_nm_yr;
                            if(count($nom_pt_empty_err_arr)==1) {$errors['nom_pt_empty']='</br>**There is 1 empty entry in the playtext arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                            else {$errors['nom_pt_empty']='</br>**There are '.count($nom_pt_empty_err_arr).' empty entries in the playtext arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                          }
                          else
                          {
                            if(substr_count($nom_pt_nm_yr, '--')>1)
                            {
                              $nom_pt_errors++; $nom_pt_sffx_num='0'; $nom_pt_hyphn_excss_err_arr[]=$nom_pt_nm_yr;
                              $errors['nom_pt_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per playtext. Please amend: '.html(implode(' / ', $nom_pt_hyphn_excss_err_arr)).'.**';
                            }
                            elseif(preg_match('/^\S+.*--.+$/', $nom_pt_nm_yr))
                            {
                              list($nom_pt_nm_yr_no_sffx, $nom_pt_sffx_num)=explode('--', $nom_pt_nm_yr);
                              $nom_pt_nm_yr_no_sffx=trim($nom_pt_nm_yr_no_sffx); $nom_pt_sffx_num=trim($nom_pt_sffx_num);

                              if(!preg_match('/^[1-9][0-9]{0,1}$/', $nom_pt_sffx_num))
                              {
                                $nom_pt_errors++; $nom_pt_sffx_num='0'; $nom_pt_sffx_err_arr[]=$nom_pt_nm_yr;
                                $errors['nom_pt_sffx']='</br>**Playtext suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $nom_pt_sffx_err_arr)).'**';
                              }
                              $nom_pt_nm_yr=$nom_pt_nm_yr_no_sffx;
                            }
                            elseif(substr_count($nom_pt_nm_yr, '--')==1)
                            {$nom_pt_errors++; $nom_pt_sffx_num='0'; $nom_pt_hyphn_err_arr[]=$nom_pt_nm_yr;
                            $errors['nom_pt_hyphn']='</br>**Playtext suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $nom_pt_hyphn_err_arr)).'**';}
                            else {$nom_pt_sffx_num='0';}

                            if($nom_pt_sffx_num) {$nom_pt_sffx_rmn=' ('.romannumeral($nom_pt_sffx_num).')';} else {$nom_pt_sffx_rmn='';}

                            if(substr_count($nom_pt_nm_yr, '##')>1) {$nom_pt_errors++; $nom_pt_hsh_excss_err_arr[]=$nom_pt_nm_yr; $errors['nom_pt_hsh_excss']='</br>**You may only use [##] for year written assignment once per playtext. Please amend: '.html(implode(' / ', $nom_pt_hsh_excss_err_arr)).'.**';}
                            elseif(preg_match('/^\S+.*##.*\S+$/', $nom_pt_nm_yr))
                            {
                              list($nom_pt_nm, $nom_pt_yr)=explode('##', $nom_pt_nm_yr);
                              $nom_pt_nm=trim($nom_pt_nm); $nom_pt_yr=trim($nom_pt_yr);

                              if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}(\s*;;\s*(c)?(-)?[1-9][0-9]{0,3})?$/', $nom_pt_yr))
                              {
                                if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $nom_pt_yr))
                                {
                                  list($nom_pt_yr_strtd, $nom_pt_yr_wrttn)=explode(';;', $nom_pt_yr);
                                  $nom_pt_yr_strtd=trim($nom_pt_yr_strtd); $nom_pt_yr_wrttn=trim($nom_pt_yr_wrttn);

                                  if(preg_match('/^c(-)?/', $nom_pt_yr_strtd)) {$nom_pt_yr_strtd=preg_replace('/^c(.+)$/', '$1', $nom_pt_yr_strtd); $nom_pt_yr_strtd_c='1';}
                                  else {$nom_pt_yr_strtd_c=NULL;}

                                  if(preg_match('/^c(-)?/', $nom_pt_yr_wrttn)) {$nom_pt_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $nom_pt_yr_wrttn); $nom_pt_yr_wrttn_c='1';}
                                  else {$nom_pt_yr_wrttn_c=NULL;}

                                  if($nom_pt_yr_strtd >= $nom_pt_yr_wrttn) {$nom_pt_errors++; $nom_pt_yr_err_arr[]=$nom_pt_nm_yr; $errors['nom_pt_yr']='</br>**Playtext year started must be earlier than year written. Please amend: '.html(implode(' / ', $nom_pt_yr_err_arr)).'.**';}
                                }
                                else
                                {
                                  $nom_pt_yr_strtd_c=NULL; $nom_pt_yr_strtd=NULL; $nom_pt_yr_wrttn=$nom_pt_yr;
                                  if(preg_match('/^c(-)?/', $nom_pt_yr_wrttn)) {$nom_pt_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $nom_pt_yr_wrttn); $nom_pt_yr_wrttn_c='1';}
                                  else {$nom_pt_yr_wrttn_c=NULL;}
                                }

                                if($nom_pt_yr_strtd)
                                {
                                  if(preg_match('/^-/', $nom_pt_yr_strtd)) {$nom_pt_yr_strtd=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $nom_pt_yr_strtd); if(!preg_match('/^-/', $nom_pt_yr_wrttn)) {$nom_pt_yr_strtd .= ' BCE';}}
                                  $nom_pt_yr_strtd .= '-';
                                  if($nom_pt_yr_strtd_c) {$nom_pt_yr_strtd='c.'.$nom_pt_yr_strtd;}
                                }
                                else {$nom_pt_yr_strtd='';}

                                if(preg_match('/^-/', $nom_pt_yr_wrttn)) {$nom_pt_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE", $nom_pt_yr_wrttn);}
                                if($nom_pt_yr_wrttn_c) {$nom_pt_yr_wrttn='c.'.$nom_pt_yr_wrttn;}

                                $nom_pt_nm_yr=$nom_pt_nm.' ('.$nom_pt_yr_strtd.$nom_pt_yr_wrttn.')'; $nom_pt_url=generateurl($nom_pt_nm_yr.$nom_pt_sffx_rmn);
                                $nom_pt_dplct_arr[]=$nom_pt_url; if(count(array_unique($nom_pt_dplct_arr))<count($nom_pt_dplct_arr)) {$errors['nom_pt_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}
                                if(strlen($nom_pt_nm_yr)>255 || strlen($nom_pt_url)>255) {$nom_pt_errors++; $errors['nom_pt_nm_yr_excss_lngth']='</br>**Playtext name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                              }
                              else {$nom_pt_errors++; $nom_pt_yr_frmt_err_arr[]=$nom_pt_nm_yr; $errors['nom_pt_yr_frmt']='</br>**Playtexts must be assigned a valid year (or years). Please amend: '.html(implode(' / ', $nom_pt_yr_frmt_err_arr)).'.**';}
                            }
                            else {$nom_pt_errors++; $nom_pt_nm=$nom_pt_nm_yr; $nom_pt_hsh_err_arr[]=$nom_pt_nm_yr; $errors['nom_pt_hsh']='</br>**You must assign a playtext year in the correct format to the following using [##]: '.html(implode(' / ', $nom_pt_hsh_err_arr)).'.**';}

                            if($nom_pt_errors==0)
                            {
                              $nom_pt_nm_yr_cln=cln($nom_pt_nm_yr); $nom_pt_sffx_num_cln=cln($nom_pt_sffx_num); $nom_pt_url_cln=cln($nom_pt_url);
                              $sql= "SELECT pt_nm, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn
                                    FROM pt
                                    WHERE NOT EXISTS (SELECT 1 FROM pt WHERE pt_nm_yr='$nom_pt_nm_yr_cln' AND pt_sffx_num='$nom_pt_sffx_num_cln')
                                    AND pt_url='$nom_pt_url_cln'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking for existing playtext URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              $row=mysqli_fetch_array($result);
                              if(mysqli_num_rows($result)>0)
                              {
                                if($row['pt_yr_strtd_c']) {$pt_yr_strtd_c='c';} else {$pt_yr_strtd_c='';}
                                if($row['pt_yr_strtd']) {$pt_yr_strtd=$row['pt_yr_strtd'].';;';} else {$pt_yr_strtd='';}
                                if($row['pt_yr_wrttn_c']) {$pt_yr_wrttn_c='c';} else {$pt_yr_wrttn_c='';}
                                if($row['pt_sffx_num']) {$pt_sffx_num='--'.$row['pt_sffx_num'];} else {$pt_sffx_num='';}
                                $nom_pt_url_err_arr[]=$row['pt_nm'].'##'.$pt_yr_strtd_c.$pt_yr_strtd.$pt_yr_wrttn_c.$row['pt_yr_wrttn'].$pt_sffx_num;
                                if(count($nom_pt_url_err_arr)==1)
                                {$errors['nom_pt_url']='</br>**Duplicate playtext URL exists. Did you mean to type: '.html(implode(' / ', $nom_pt_url_err_arr)).'?**';}
                                else
                                {$errors['nom_pt_url']='</br>**Duplicate playtext URLs exist. Did you mean to type: '.html(implode(' / ', $nom_pt_url_err_arr)).'?**';}
                              }
                            }
                          }
                        }
                      }
                    }
                    elseif(substr_count($awrd_nom_ppl_prds_pts, '++')==1)
                    {
                      $awrd_nom_dtl_pls_err_arr[]=$awrd_nom_ppl_prds_pts; $awrd_nom_ppl_prds=''; $awrd_nom_pts='';
                      $errors['awrd_nom_dtl_pls']='</br>**Playtext assignation must use [++] in the correct format. Please amend: '.html(implode(' / ', $awrd_nom_dtl_pls_err_arr)).'.**';
                    }
                    else
                    {
                      $awrd_nom_ppl_prds=$awrd_nom_ppl_prds_pts; $awrd_nom_pts='';
                    }

                    if(substr_count($awrd_nom_ppl_prds, '##')>1)
                    {
                      $awrd_nom_dtl_hsh_excss_err_arr[]=$awrd_nom_ppl_prds; $awrd_nom_prsn_list=''; $awrd_nom_prds='';
                      $errors['awrd_nom_dtl_hsh_excss']='</br>**You may only use [##] once per production assignation. Please amend: '.html(implode(' / ', $awrd_nom_dtl_hsh_excss_err_arr)).'.**';
                    }
                    elseif(preg_match('/(\S+.*)?##.*\S+/', $awrd_nom_ppl_prds))
                    {
                      list($awrd_nom_prsn_list, $awrd_nom_prds)=explode('##', $awrd_nom_ppl_prds);
                      $awrd_nom_prsn_list=trim($awrd_nom_prsn_list); $awrd_nom_prds=trim($awrd_nom_prds);

                      $awrd_nom_prd_ids=explode('>>', $awrd_nom_prds);
                      if(count($awrd_nom_prd_ids)>250)
                      {$errors['nom_prd_array_excss']='</br>**Maximum of 250 productions allowed per nomination. Please amend those that exceed this amount**';}
                      else
                      {
                        $nom_prd_dplct_arr=array();
                        foreach($awrd_nom_prd_ids as $awrd_nom_prd_id)
                        {
                          $awrd_nom_prd_id=trim($awrd_nom_prd_id);
                          if(!preg_match('/\S+/', $awrd_nom_prd_id))
                          {
                            $awrd_nom_prd_empty_err_arr[]=$awrd_nom_prd_id;
                            if(count($awrd_nom_prd_empty_err_arr)==1) {$errors['awrd_nom_prd_empty']='</br>**There is 1 empty entry in the production arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                            else {$errors['awrd_nom_prd_empty']='</br>**There are '.count($awrd_nom_prd_empty_err_arr).' empty entries in the production arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                          }
                          elseif(!preg_match('/^[1-9][0-9]{0,9}$/', $awrd_nom_prd_id))
                          {
                            $awrd_nom_prd_nonnmrcl_err_arr[]=$awrd_nom_prd_id;
                            $errors['awrd_nom_prd_nonnmrcl']='</br>**The following production ids have not been assigned properly (must be positive integers): '.html(implode(' / ', $awrd_nom_prd_nonnmrcl_err_arr)).'.**';
                          }
                          else
                          {
                            $nom_prd_dplct_arr[]=$awrd_nom_prd_id;
                            if(count(array_unique($nom_prd_dplct_arr))<count($nom_prd_dplct_arr))
                            {$errors['nom_prd_dplct']='</br>**Duplicate production ids are not allowed within nomination arrays.**';}

                            $awrd_nom_prd_id_cln=cln($awrd_nom_prd_id);

                            $sql= "SELECT 1
                                  FROM prd
                                  WHERE prd_id='$awrd_nom_prd_id_cln' LIMIT 1";
                            $result=mysqli_query($link, $sql);
                            if(!$result) {$error='Error checking for existing production id (against tour nominated production id): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                            if(mysqli_num_rows($result)==0)
                            {
                              $awrd_nom_prd_nonexst_err_arr[]=$awrd_nom_prd_id;
                              $errors['awrd_nom_prd_nonexst']='</br>**The following are not existing production ids: '.html(implode(' / ', $awrd_nom_prd_nonexst_err_arr)).'.**';
                            }
                          }
                        }
                      }
                    }
                    elseif(substr_count($awrd_nom_ppl_prds, '##')==1)
                    {
                      $awrd_nom_dtl_hsh_err_arr[]=$awrd_nom_ppl_prds; $awrd_nom_prsn_list=''; $awrd_nom_prds='';
                      $errors['awrd_nom_dtl_hsh']='</br>**Production assignation must use [##] in the correct format. Please amend: '.html(implode(' / ', $awrd_nom_dtl_hsh_err_arr)).'.**';
                    }
                    else
                    {
                      $awrd_nom_prsn_list=$awrd_nom_ppl_prds; $awrd_nom_prds='';
                    }

                    if($awrd_nom_prds && $awrd_nom_pts)
                    {
                      $awrd_nom_prd_pt_err_arr[]=$awrd_nom_ppl_prds_pts;
                      $errors['awrd_nom_prd_pt']='</br>**Productions and playtexts may not be given in the same nominations. Please amend: '.html(implode(' / ', $awrd_nom_prd_pt_err_arr)).'**';
                    }

                    if($awrd_nom_prds) {$ctgry_prd_count_array[]='1';} if($awrd_nom_pts) {$ctgry_pt_count_array[]='1';}

                    if($awrd_nom_prsn_list)
                    {
                      $awrd_nom_ppl=explode('>>', $awrd_nom_prsn_list);
                      $awrd_nomnee_ttl_array=array(); $awrd_comp_nm_array=array(); $awrd_prsn_nm_array=array();
                      foreach($awrd_nom_ppl as $awrd_comp_prsn)
                      {
                        $awrd_comp_prsn=trim($awrd_comp_prsn);
                        if(!preg_match('/\S+/', $awrd_comp_prsn))
                        {
                          $awrd_nom_prsn_empty_err_arr[]=$awrd_comp_prsn;
                          if(count($awrd_nom_prsn_empty_err_arr)==1) {$errors['awrd_nom_prsn_empty']='</br>**There is 1 empty entry in the company/person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                          else {$errors['awrd_nom_prsn_empty']='</br>**There are '.count($awrd_nom_prsn_empty_err_arr).' empty entries in the company/person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                        }
                        else
                        {
                          if(substr_count($awrd_comp_prsn, '||')>1)
                          {
                            $awrd_prsn_nm_list=''; $awrd_nomnee_ttl_array[]=$awrd_comp_prsn; $awrd_pipe_excss_err_arr[]=$awrd_comp_prsn;
                            $errors['awrd_pipe_excss']='</br>**You may only use [||] once per nominee/winner company-members coupling. Please amend: '.html(implode(' / ', $awrd_pipe_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/\|\|/', $awrd_comp_prsn))
                          {
                            if(preg_match('/\S+.*\|\|(.*\S+)?/', $awrd_comp_prsn))
                            {
                              list($awrd_comp_nm, $awrd_prsn_nm_list)=explode('||', $awrd_comp_prsn);
                              $awrd_comp_nm=trim($awrd_comp_nm); $awrd_prsn_nm_list=trim($awrd_prsn_nm_list);
                              $awrd_comp_nm_array[]=$awrd_comp_nm; $awrd_nomnee_ttl_array[]=$awrd_comp_nm;
                            }
                            else
                            {
                              $awrd_prsn_nm_list=''; $awrd_pipe_err_arr[]=$awrd_comp_prsn;
                              $errors['awrd_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $awrd_pipe_err_arr)).'.**';
                            }
                          }
                          else
                          {$awrd_prsn_nm_array[]=$awrd_comp_prsn; $awrd_nomnee_ttl_array[]=$awrd_comp_prsn; $awrd_prsn_nm_list='';}

                          if($awrd_prsn_nm_list)
                          {
                            $awrd_prsn_nms=explode('//', $awrd_prsn_nm_list);
                            foreach($awrd_prsn_nms as $awrd_prsn_nm)
                            {
                              $awrd_prsn_nm=trim($awrd_prsn_nm);
                              if(!preg_match('/\S+/', $awrd_prsn_nm))
                              {
                                $awrd_prsn_empty_err_arr[]=$awrd_prsn_nm;
                                if(count($awrd_prsn_empty_err_arr)==1) {$errors['awrd_prsn_empty']='</br>**There is 1 empty entry in the company member arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                                else {$errors['awrd_prsn_empty']='</br>**There are '.count($awrd_prsn_empty_err_arr).' empty entries in the company member arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                              }
                              else
                              {$awrd_prsn_nm_array[]=$awrd_prsn_nm; $awrd_nomnee_ttl_array[]=$awrd_prsn_nm;}
                            }
                          }

                          if(count($awrd_nomnee_ttl_array)>250)
                          {$errors['awrd_nomnee_array_excss']='</br>**Maximum of 250 entries (companies and people per nomination) allowed.**';}
                        }
                      }

                      if(count($awrd_comp_nm_array)>0)
                      {
                        $nom_comp_dplct_arr=array();
                        foreach($awrd_comp_nm_array as $awrd_comp_nm)
                        {
                          $awrd_comp_nm=trim($awrd_comp_nm);
                          $awrd_comp_errors=0;
                          if(substr_count($awrd_comp_nm, '~~')>1)
                          {
                            $awrd_comp_errors++; $awrd_comp_tld_excss_err_arr[]=$awrd_comp_nm;
                            $errors['awrd_comp_tld_excss']='</br>**You may only use [~~] once per nominee/winner (company)-role coupling. Please amend: '.html(implode(' / ', $awrd_comp_tld_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/\S+.*~~.*\S+/', $awrd_comp_nm))
                          {
                            list($awrd_comp_nm, $awrd_comp_rl)=explode('~~', $awrd_comp_nm);
                            $awrd_comp_nm=trim($awrd_comp_nm); $awrd_comp_rl=trim($awrd_comp_rl);

                            if(strlen($awrd_comp_rl)>255)
                            {$errors['awrd_comp_rl']='</br>**Nominee/winner (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                          }
                          elseif(substr_count($awrd_comp_nm, '~~')==1)
                          {$awrd_comp_errors++; $awrd_comp_tld_err_arr[]=$awrd_comp_nm;
                          $errors['awrd_comp_tld']='</br>**Nominee/winner (company)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $awrd_comp_tld_err_arr)).'**';}

                          if(substr_count($awrd_comp_nm, '--')>1)
                          {
                            $awrd_comp_errors++; $awrd_comp_sffx_num='0'; $awrd_comp_hyphn_excss_err_arr[]=$awrd_comp_nm;
                            $errors['awrd_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per nominee/winner (company). Please amend: '.html(implode(' / ', $awrd_comp_hyphn_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/^\S+.*--.+$/', $awrd_comp_nm))
                          {
                            list($awrd_comp_nm_no_sffx, $awrd_comp_sffx_num)=explode('--', $awrd_comp_nm);
                            $awrd_comp_nm_no_sffx=trim($awrd_comp_nm_no_sffx); $awrd_comp_sffx_num=trim($awrd_comp_sffx_num);

                            if(!preg_match('/^[1-9][0-9]{0,1}$/', $awrd_comp_sffx_num))
                            {
                              $awrd_comp_errors++; $awrd_comp_sffx_num='0'; $awrd_comp_sffx_err_arr[]=$awrd_comp_nm;
                              $errors['awrd_comp_sffx']='</br>**The nominee/winner (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $awrd_comp_sffx_err_arr)).'**';
                            }
                            $awrd_comp_nm=$awrd_comp_nm_no_sffx;
                          }
                          elseif(substr_count($awrd_comp_nm, '--')==1)
                          {$awrd_comp_errors++; $awrd_comp_sffx_num='0'; $awrd_comp_hyphn_err_arr[]=$awrd_comp_nm;
                          $errors['awrd_comp_hyphn']='</br>**Nominee/winner (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $awrd_comp_hyphn_err_arr)).'**';}
                          else
                          {$awrd_comp_sffx_num='0';}

                          if($awrd_comp_sffx_num) {$awrd_comp_sffx_rmn=' ('.romannumeral($awrd_comp_sffx_num).')';} else {$awrd_comp_sffx_rmn='';}

                          $awrd_comp_url=generateurl($awrd_comp_nm.$awrd_comp_sffx_rmn);

                          if(strlen($awrd_comp_nm)>255 || strlen($awrd_comp_url)>255)
                          {$awrd_comp_errors++; $errors['awrd_comp_nm_excss_lngth']='</br>**Nominee/winner (company) name and its URL allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                          $nom_comp_dplct_arr[]=$awrd_comp_url;
                          if(count(array_unique($nom_comp_dplct_arr))<count($nom_comp_dplct_arr))
                          {$errors['nom_comp_dplct']='</br>**Duplicate company URLs are not allowed within nomination arrays.**';}

                          if($awrd_comp_errors==0)
                          {
                            $awrd_comp_nm_cln=cln($awrd_comp_nm);
                            $awrd_comp_sffx_num_cln=cln($awrd_comp_sffx_num);
                            $awrd_comp_url_cln=cln($awrd_comp_url);

                            $sql= "SELECT comp_nm, comp_sffx_num
                                  FROM comp
                                  WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$awrd_comp_nm_cln' AND comp_sffx_num='$awrd_comp_sffx_num_cln')
                                  AND comp_url='$awrd_comp_url_cln'";
                            $result=mysqli_query($link, $sql);
                            if(!$result) {$error='Error checking for existing nominee/winner company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                            $row=mysqli_fetch_array($result);
                            if(mysqli_num_rows($result)>0)
                            {
                              if($row['comp_sffx_num']) {$awrd_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                              else {$awrd_comp_url_error_sffx_dsply='';}
                              $awrd_comp_url_err_arr[]=$row['comp_nm'].$awrd_comp_url_error_sffx_dsply;
                              if(count($awrd_comp_url_err_arr)==1)
                              {$errors['awrd_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $awrd_comp_url_err_arr)).'?**';}
                              else
                              {$errors['awrd_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $awrd_comp_url_err_arr)).'?**';}
                            }
                          }
                        }
                      }

                      if(count($awrd_prsn_nm_array)>0)
                      {
                        $nom_prsn_dplct_arr=array();
                        foreach($awrd_prsn_nm_array as $awrd_prsn_nm)
                        {
                          $awrd_prsn_nm=trim($awrd_prsn_nm);
                          $awrd_prsn_errors=0;
                          if(substr_count($awrd_prsn_nm, '~~')>1)
                          {
                            $awrd_prsn_errors++; $awrd_prsn_tld_excss_err_arr[]=$awrd_prsn_nm;
                            $errors['awrd_prsn_tld_excss']='</br>**You may only use [~~] once per nominee/winner (person)-role coupling. Please amend: '.html(implode(' / ', $awrd_prsn_tld_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/\S+.*~~.*\S+/', $awrd_prsn_nm))
                          {
                            list($awrd_prsn_nm, $awrd_prsn_rl)=explode('~~', $awrd_prsn_nm);
                            $awrd_prsn_nm=trim($awrd_prsn_nm); $awrd_prsn_rl=trim($awrd_prsn_rl);

                            if(strlen($awrd_prsn_rl)>255)
                            {$errors['awrd_prsn_rl']='</br>**Nominee/winner (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                          }
                          elseif(substr_count($awrd_prsn_nm, '~~')==1)
                          {$awrd_prsn_errors++; $awrd_prsn_tld_err_arr[]=$awrd_prsn_nm;
                          $errors['awrd_prsn_tld']='</br>**Nominee/winner (person)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $awrd_prsn_tld_err_arr)).'**';}

                          if(substr_count($awrd_prsn_nm, '--')>1)
                          {
                            $awrd_prsn_errors++; $awrd_prsn_sffx_num='0'; $awrd_prsn_hyphn_excss_err_arr[]=$awrd_prsn_nm;
                            $errors['awrd_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per nominee/winner (person). Please amend: '.html(implode(' / ', $awrd_prsn_hyphn_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/^\S+.*--.+$/', $awrd_prsn_nm))
                          {
                            list($awrd_prsn_nm_no_sffx, $awrd_prsn_sffx_num)=explode('--', $awrd_prsn_nm);
                            $awrd_prsn_nm_no_sffx=trim($awrd_prsn_nm_no_sffx); $awrd_prsn_sffx_num=trim($awrd_prsn_sffx_num);

                            if(!preg_match('/^[1-9][0-9]{0,1}$/', $awrd_prsn_sffx_num))
                            {
                              $awrd_prsn_errors++; $awrd_prsn_sffx_num='0'; $awrd_prsn_sffx_err_arr[]=$awrd_prsn_nm;
                              $errors['awrd_prsn_sffx']='</br>**The nominee/winner (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $awrd_prsn_sffx_err_arr)).'**';
                            }
                            $awrd_prsn_nm=$awrd_prsn_nm_no_sffx;
                          }
                          elseif(substr_count($awrd_prsn_nm, '--')==1)
                          {$awrd_prsn_errors++; $awrd_prsn_sffx_num='0'; $awrd_prsn_hyphn_err_arr[]=$awrd_prsn_nm;
                          $errors['awrd_prsn_hyphn']='</br>**Nominee/winner (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $awrd_prsn_hyphn_err_arr)).'**';}
                          else
                          {$awrd_prsn_sffx_num='0';}

                          if($awrd_prsn_sffx_num) {$awrd_prsn_sffx_rmn=' ('.romannumeral($awrd_prsn_sffx_num).')';} else {$awrd_prsn_sffx_rmn='';}

                          if(substr_count($awrd_prsn_nm, ';;')>1)
                          {
                            $awrd_prsn_errors++; $awrd_prsn_smcln_excss_err_arr[]=$awrd_prsn_nm;
                            $errors['awrd_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $awrd_prsn_smcln_excss_err_arr)).'.**';
                          }
                          elseif(preg_match('/\S+.*;;(.*\S+)?/', $awrd_prsn_nm))
                          {
                            list($awrd_prsn_frst_nm, $awrd_prsn_lst_nm)=explode(';;', $awrd_prsn_nm);
                            $awrd_prsn_frst_nm=trim($awrd_prsn_frst_nm); $awrd_prsn_lst_nm=trim($awrd_prsn_lst_nm);

                            if(preg_match('/\S+/', $awrd_prsn_lst_nm)) {$awrd_prsn_lst_nm_dsply=' '.$awrd_prsn_lst_nm;}
                            else {$awrd_prsn_lst_nm_dsply='';}

                            $awrd_prsn_fll_nm=$awrd_prsn_frst_nm.$awrd_prsn_lst_nm_dsply;
                            $awrd_prsn_url=generateurl($awrd_prsn_fll_nm.$awrd_prsn_sffx_rmn);

                            if(strlen($awrd_prsn_fll_nm)>255 || strlen($awrd_prsn_url)>255)
                            {$awrd_prsn_errors++; $errors['awrd_prsn_excss_lngth']='</br>**Nominee/winner (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                            $nom_prsn_dplct_arr[]=$awrd_prsn_url;
                            if(count(array_unique($nom_prsn_dplct_arr))<count($nom_prsn_dplct_arr))
                            {$errors['nom_prsn_dplct']='</br>**Duplicate person URLs are not allowed within nomination arrays.**';}
                          }
                          else
                          {
                            $awrd_prsn_errors++; $awrd_prsn_smcln_err_arr[]=$awrd_prsn_nm;
                            $errors['awrd_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $awrd_prsn_smcln_err_arr)).'.**';
                          }

                          if($awrd_prsn_errors==0)
                          {
                            $awrd_prsn_frst_nm_cln=cln($awrd_prsn_frst_nm);
                            $awrd_prsn_lst_nm_cln=cln($awrd_prsn_lst_nm);
                            $awrd_prsn_fll_nm_cln=cln($awrd_prsn_fll_nm);
                            $awrd_prsn_sffx_num_cln=cln($awrd_prsn_sffx_num);
                            $awrd_prsn_url_cln=cln($awrd_prsn_url);

                            $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                                  FROM prsn
                                  WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$awrd_prsn_frst_nm_cln' AND prsn_lst_nm='$awrd_prsn_lst_nm_cln')
                                  AND prsn_fll_nm='$awrd_prsn_fll_nm_cln' AND prsn_sffx_num='$awrd_prsn_sffx_num_cln'";
                            $result=mysqli_query($link, $sql);
                            if(!$result) {$error='Error checking for nominee/winner person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                            $row=mysqli_fetch_array($result);
                            if(mysqli_num_rows($result)>0)
                            {
                              if($row['prsn_sffx_num']) {$awrd_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                              else {$awrd_prsn_nm_error_sffx_dsply='';}
                              $awrd_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$awrd_prsn_nm_error_sffx_dsply;
                              if(count($awrd_prsn_nm_err_arr)==1)
                              {$errors['awrd_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $awrd_prsn_nm_err_arr)).'.**';}
                              else
                              {$errors['awrd_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $awrd_prsn_nm_err_arr)).'.**';}
                            }

                            $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                                  FROM prsn
                                  WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$awrd_prsn_fll_nm_cln' AND prsn_sffx_num='$awrd_prsn_sffx_num_cln')
                                  AND prsn_url='$awrd_prsn_url_cln'";
                            $result=mysqli_query($link, $sql);
                            if(!$result) {$error='Error checking for existing nominee/winner person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                            $row=mysqli_fetch_array($result);
                            if(mysqli_num_rows($result)>0)
                            {
                              if($row['prsn_sffx_num']) {$awrd_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                              else {$awrd_prsn_url_error_sffx_dsply='';}
                              $awrd_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$awrd_prsn_url_error_sffx_dsply;
                              if(count($awrd_prsn_url_err_arr)==1)
                              {$errors['awrd_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $awrd_prsn_url_err_arr)).'?**';}
                              else
                              {$errors['awrd_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $awrd_prsn_url_err_arr)).'?**';}
                            }
                          }
                        }
                      }
                    }
                  }
                  else
                  {
                    $awrd_nom_dtl_dscr_cln_err_arr[]=$awrd_nom_dtl_dscr;
                    $errors['awrd_nom_dtl_dscr_cln']='</br>**You must assign nominee details and a corresponding description to the following using [::]: '.html(implode(' / ', $awrd_nom_dtl_dscr_cln_err_arr)).'.**';
                  }
                }
              }
              if(count($ctgry_prd_count_array)>0 && count($ctgry_pt_count_array)>0)
              {
                $awrd_ctgry_prd_pt_err_arr[]=$awrd_ctgry_nm;
                $errors['awrd_ctgry_prd_pt']='</br>**Productions and playtexts may not be given in the same category. Please address the following: '.html(implode(' / ', $awrd_ctgry_prd_pt_err_arr)).'**';
              }
            }
            else
            {
              $awrd_eql_err_arr[]=$awrd_ctgry;
              $errors['awrd_eql']='</br>**You must assign a category name and nominees/winners to the following in the correct format using [==]: '.html(implode(' / ', $awrd_eql_err_arr)).'.**';
            }
          }
        }
      }
    }
?>