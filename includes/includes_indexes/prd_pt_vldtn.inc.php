<?php
//FILE COMPRISES: sbnm / mat_list / txt_vrsn_list / ctgry_list / gnr_list / ftr_list / thm_list / sttng_list / wri_list / alt_nm_list
    if(preg_match('/\S+/', $mat_list))
    {
      if($tr_lg) {$errors['mat_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      elseif($coll_wrks) {$errors['mat_coll_wrks_checked']='**This field must be empty if collected works button is applied.**';}
      else
      {
        $mat_nm_frmts=explode(',,', $_POST['mat_list']);
        if(count($mat_nm_frmts)>250)
        {$errors['mat_nm_frmt_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $mat_empty_err_arr=array(); $mat_hyphn_excss_err_arr=array(); $mat_sffx_err_arr=array();
          $mat_hyphn_err_arr=array(); $mat_smcln_excss_err_arr=array(); $mat_frmt_dplct_arr=array();
          $frmt_url_err_arr=array(); $mat_url_err_arr=array(); $mat_smcln_err_arr=array();
          foreach($mat_nm_frmts as $mat_nm_frmt)
          {
            $mat_errors=0; $frmt_errors=0;

            if(!preg_match('/\S+/', $mat_nm_frmt))
            {
              $mat_empty_err_arr[]=$mat_nm_frmt;
              if(count($mat_empty_err_arr)==1) {$errors['mat_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['mat_empty']='</br>**There are '.count($mat_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($mat_nm_frmt, '--')>1)
              {
                $mat_errors++; $mat_sffx_num='0'; $mat_hyphn_excss_err_arr[]=$mat_nm_frmt;
                $errors['mat_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per material. Please amend: '.html(implode(' / ', $mat_hyphn_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*--.+$/', $mat_nm_frmt))
              {
                list($mat_nm_frmt_no_sffx, $mat_sffx_num)=explode('--', $mat_nm_frmt);
                $mat_nm_frmt_no_sffx=trim($mat_nm_frmt_no_sffx); $mat_sffx_num=trim($mat_sffx_num);

                if(!preg_match('/^[1-9][0-9]{0,1}$/', $mat_sffx_num))
                {
                  $mat_errors++; $mat_sffx_num='0'; $mat_sffx_err_arr[]=$mat_nm_frmt;
                  $errors['mat_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $mat_sffx_err_arr)).'**';
                }
                $mat_nm_frmt=$mat_nm_frmt_no_sffx;
              }
              elseif(substr_count($mat_nm_frmt, '--')==1)
              {$mat_errors++; $mat_sffx_num='0'; $mat_hyphn_err_arr[]=$mat_nm_frmt;
              $errors['mat_hyphn']='</br>**Material suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $mat_hyphn_err_arr)).'**';}
              else
              {$mat_sffx_num='0';}

              if($mat_sffx_num) {$mat_sffx_rmn=' ('.romannumeral($mat_sffx_num).')';} else {$mat_sffx_rmn='';}

              if(substr_count($mat_nm_frmt, ';;')>1)
              {
                $mat_smcln_excss_err_arr[]=$mat_nm_frmt;
                $errors['mat_smcln_excss']='</br>**You may only use [;;] once per material name-format coupling. Please amend: '.html(implode(' / ', $mat_smcln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*;;.*\S+/', $mat_nm_frmt))
              {
                list($mat_nm, $frmt_nm)=explode(';;', $mat_nm_frmt);
                $mat_nm=trim($mat_nm); $frmt_nm=trim($frmt_nm);

                $mat_url=generateurl($mat_nm.$mat_sffx_rmn);
                $frmt_url=generateurl($frmt_nm);

                $mat_frmt_dplct_arr[]=$mat_url.' '.$frmt_url;
                if(count(array_unique($mat_frmt_dplct_arr))<count($mat_frmt_dplct_arr))
                {$errors['mat_dplct']='</br>**There are entries within the array that create duplicate entries.**';}

                if(strlen($frmt_nm)>255)
                {$frmt_errors++; $errors['frmt_nm_excss_lngth']='</br>**Format name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                if($mat_errors==0 && $frmt_errors==0)
                {
                  $frmt_nm_cln=cln($frmt_nm);
                  $frmt_url_cln=cln($frmt_url);

                  $sql= "SELECT frmt_nm
                        FROM frmt
                        WHERE NOT EXISTS (SELECT 1 FROM frmt WHERE frmt_nm='$frmt_nm_cln')
                        AND frmt_url='$frmt_url_cln'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existing material URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $mat_errors++; $frmt_url_err_arr[]=$row['frmt_nm'];
                    if(count($frmt_url_err_arr)==1)
                    {$errors['frmt_url']='</br>**Duplicate format URL exists. Did you mean to type: '.html(implode(' / ', $frmt_url_err_arr)).'?**';}
                    else
                    {$errors['frmt_url']='</br>**Duplicate format URLs exist. Did you mean to type: '.html(implode(' / ', $frmt_url_err_arr)).'?**';}
                  }

                  if(strlen($mat_nm)>255 || strlen($mat_url)>255)
                  {$mat_errors++; $errors['mat_nm_excss_lngth']='</br>**Material name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                  $mat_nm_cln=cln($mat_nm);
                  $mat_sffx_num_cln=cln($mat_sffx_num);
                  $mat_url_cln=cln($mat_url);

                  if($mat_errors==0)
                  {
                    $sql= "SELECT mat_nm, frmt_nm, mat_sffx_num
                          FROM mat
                          INNER JOIN frmt ON frmtid=frmt_id
                          WHERE NOT EXISTS (SELECT 1 FROM mat WHERE mat_nm='$mat_nm_cln' AND mat_sffx_num='$mat_sffx_num_cln')
                          AND mat_url='$mat_url_cln'
                          AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$frmt_url_cln')";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existing material URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    $row=mysqli_fetch_array($result);
                    if(mysqli_num_rows($result)>0)
                    {
                      if($row['mat_sffx_num']) {$mat_sffx_num='--'.$row['mat_sffx_num'];} else {$mat_sffx_num='';}
                      $mat_url_err_arr[]=$row['mat_nm'].';;'.$row['frmt_nm'].$mat_sffx_num;
                      if(count($mat_url_err_arr)==1)
                      {$errors['mat_url']='</br>**Duplicate material URL exists. Did you mean to type: '.html(implode(' / ', $mat_url_err_arr)).'?**';}
                      else
                      {$errors['mat_url']='</br>**Duplicate material URLs exist. Did you mean to type: '.html(implode(' / ', $mat_url_err_arr)).'?**';}
                    }
                  }
                }
              }
              else
              {
                $mat_smcln_err_arr[]=$mat_nm_frmt;
                $errors['mat_smcln']='</br>**You must assign a name and corresponding format to the following using [;;]: '.html(implode(' / ', $mat_smcln_err_arr)).'.**';
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $txt_vrsn_list))
    {
      if($tr_lg) {$errors['txt_vrsn_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      elseif($coll_wrks) {$errors['txt_vrsn_coll_wrks_checked']='**This field must be empty if collected works button is applied.**';}
      elseif($coll_ov && $pt) {$errors['txt_vrsn_coll_ov_checked']='**This field must be empty if collection button is applied.**';}
      else
      {
        $txt_vrsn_nms=explode(',,', $txt_vrsn_list);
        if(count($txt_vrsn_nms)>250)
        {$errors['txt_vrsn_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $txt_vrsn_empty_err_arr=array(); $txt_vrsn_dplct_arr=array(); $txt_vrsn_url_err_arr=array();
          foreach($txt_vrsn_nms as $txt_vrsn_nm)
          {
            $txt_vrsn_nm=trim($txt_vrsn_nm);
            if(!preg_match('/\S+/', $txt_vrsn_nm))
            {
              $txt_vrsn_empty_err_arr[]=$txt_vrsn_nm;
              if(count($txt_vrsn_empty_err_arr)==1) {$errors['txt_vrsn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['txt_vrsn_empty']='</br>**There are '.count($txt_vrsn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $txt_vrsn_url=generateurl($txt_vrsn_nm);
              $txt_vrsn_dplct_arr[]=$txt_vrsn_url;
              if(count(array_unique($txt_vrsn_dplct_arr))<count($txt_vrsn_dplct_arr))
              {$errors['txt_vrsn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($txt_vrsn_nm)>255)
              {$errors['txt_vrsn_nm_excss_lngth']='</br>**Text version name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              $sql= "SELECT txt_vrsn_nm
                    FROM txt_vrsn
                    WHERE NOT EXISTS (SELECT 1 FROM txt_vrsn WHERE txt_vrsn_nm='$txt_vrsn_nm')
                    AND txt_vrsn_url='$txt_vrsn_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing text version URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $txt_vrsn_url_err_arr[]=$row['txt_vrsn_nm'];
                if(count($txt_vrsn_url_err_arr)==1)
                {$errors['txt_vrsn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $txt_vrsn_url_err_arr)).'?**';}
                else
                {$errors['txt_vrsn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $txt_vrsn_url_err_arr)).'?**';}
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $ctgry_list))
    {
      if($tr_lg) {$errors['ctgry_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $ctgry_nms=explode(',,', $ctgry_list);
        if(count($ctgry_nms)>250)
        {$errors['ctgry_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $ctgry_empty_err_arr=array(); $ctgry_dplct_arr=array(); $ctgry_url_err_arr=array();
          foreach($ctgry_nms as $ctgry_nm)
          {
            $ctgry_nm=trim($ctgry_nm);
            if(!preg_match('/\S+/', $ctgry_nm))
            {
              $ctgry_empty_err_arr[]=$ctgry_nm;
              if(count($ctgry_empty_err_arr)==1) {$errors['ctgry_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['ctgry_empty']='</br>**There are '.count($ctgry_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $ctgry_url=generateurl($ctgry_nm);
              $ctgry_dplct_arr[]=$ctgry_url;
              if(count(array_unique($ctgry_dplct_arr))<count($ctgry_dplct_arr))
              {$errors['ctgry_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($ctgry_nm)>255)
              {$errors['ctgry_nm_excss_lngth']='</br>**Category name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              $sql= "SELECT ctgry_nm
                    FROM ctgry
                    WHERE NOT EXISTS (SELECT 1 FROM ctgry WHERE ctgry_nm='$ctgry_nm')
                    AND ctgry_url='$ctgry_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing category URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $ctgry_url_err_arr[]=$row['ctgry_nm'];
                if(count($ctgry_url_err_arr)==1)
                {$errors['ctgry_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $ctgry_url_err_arr)).'?**';}
                else
                {$errors['ctgry_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $ctgry_url_err_arr)).'?**';}
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $gnr_list))
    {
      if($tr_lg) {$errors['gnr_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $gnr_nms=explode(',,', $gnr_list);
        if(count($gnr_nms)>250)
        {$errors['gnr_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $gnr_empty_err_arr=array(); $gnr_dplct_arr=array(); $gnr_url_err_arr=array();
          foreach($gnr_nms as $gnr_nm)
          {
            $gnr_nm=trim($gnr_nm);
            if(!preg_match('/\S+/', $gnr_nm))
            {
              $gnr_empty_err_arr[]=$gnr_nm;
              if(count($gnr_empty_err_arr)==1) {$errors['gnr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['gnr_empty']='</br>**There are '.count($gnr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $gnr_url=generateurl($gnr_nm);
              $gnr_dplct_arr[]=$gnr_url;
              if(count(array_unique($gnr_dplct_arr))<count($gnr_dplct_arr))
              {$errors['gnr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($gnr_nm)>255)
              {$errors['gnr_nm_excss_lngth']='</br>**Genre name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              $sql= "SELECT gnr_nm
                    FROM gnr
                    WHERE NOT EXISTS (SELECT 1 FROM gnr WHERE gnr_nm='$gnr_nm')
                    AND gnr_url='$gnr_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing genre URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $gnr_url_err_arr[]=$row['gnr_nm'];
                if(count($gnr_url_err_arr)==1)
                {$errors['gnr_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $gnr_url_err_arr)).'?**';}
                else
                {$errors['gnr_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $gnr_url_err_arr)).'?**';}
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $ftr_list))
    {
      if($tr_lg) {$errors['ftr_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $ftr_nms=explode(',,', $ftr_list);
        if(count($ftr_nms)>250)
        {$errors['ftr_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $ftr_empty_err_arr=array(); $ftr_dplct_arr=array(); $ftr_url_err_arr=array();
          foreach($ftr_nms as $ftr_nm)
          {
            $ftr_nm=trim($ftr_nm);
            if(!preg_match('/\S+/', $ftr_nm))
            {
              $ftr_empty_err_arr[]=$ftr_nm;
              if(count($ftr_empty_err_arr)==1) {$errors['ftr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['ftr_empty']='</br>**There are '.count($ftr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $ftr_url=generateurl($ftr_nm);
              $ftr_dplct_arr[]=$ftr_url;
              if(count(array_unique($ftr_dplct_arr))<count($ftr_dplct_arr))
              {$errors['ftr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($ftr_nm)>255)
              {$errors['ftr_nm_excss_lngth']='</br>**Feature name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              $sql= "SELECT ftr_nm
                    FROM ftr
                    WHERE NOT EXISTS (SELECT 1 FROM ftr WHERE ftr_nm='$ftr_nm')
                    AND ftr_url='$ftr_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing feature URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $ftr_url_err_arr[]=$row['ftr_nm'];
                if(count($ftr_url_err_arr)==1)
                {$errors['ftr_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $ftr_url_err_arr)).'?**';}
                else
                {$errors['ftr_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $ftr_url_err_arr)).'?**';}
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $thm_list))
    {
      if($tr_lg) {$errors['thm_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $thm_nms=explode(',,', $thm_list);
        if(count($thm_nms)>250)
        {$errors['thm_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $thm_empty_err_arr=array(); $thm_dplct_arr=array(); $thm_url_err_arr=array();
          foreach($thm_nms as $thm_nm)
          {
            $thm_nm=trim($thm_nm);
            if(!preg_match('/\S+/', $thm_nm))
            {
              $thm_empty_err_arr[]=$thm_nm;
              if(count($thm_empty_err_arr)==1) {$errors['thm_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['thm_empty']='</br>**There are '.count($thm_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              $thm_url=generateurl($thm_nm);
              $thm_dplct_arr[]=$thm_url;
              if(count(array_unique($thm_dplct_arr))<count($thm_dplct_arr))
              {$errors['thm_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

              if(strlen($thm_nm)>255)
              {$errors['thm_nm_excss_lngth']='</br>**Theme name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

              $sql= "SELECT thm_nm
                    FROM thm
                    WHERE NOT EXISTS (SELECT 1 FROM thm WHERE thm_nm='$thm_nm')
                    AND thm_url='$thm_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing theme URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $thm_url_err_arr[]=$row['thm_nm'];
                if(count($thm_url_err_arr)==1)
                {$errors['thm_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $thm_url_err_arr)).'?**';}
                else
                {$errors['thm_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $thm_url_err_arr)).'?**';}
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $sttng_list))
    {
      if($tr_lg) {$errors['sttng_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $sttng_tm_lctn_plcs=explode(',,', $_POST['sttng_list']);
        if(count($sttng_tm_lctn_plcs)>250)
        {$errors['sttng_array_excss']='**Maximum of 250 setting groups allowed.**';}
        else
        {
          $sttng_empty_err_arr=array(); $sttng_pls_excss_err_arr=array(); $sttng_plc_empty_err_arr=array();
          $sttng_plc_smcln_excss_err_arr=array(); $sttng_plc_nt2_cln_err_arr=array(); $sttng_plc_smcln_err_arr=array();
          $sttng_plc_cln_excss_err_arr=array(); $sttng_plc_cln_err_arr=array(); $sttng_plc_hsh_err_arr=array();
          $sttng_plc_url_err_arr=array(); $sttng_pls_err_arr=array(); $sttng_hsh_excss_err_arr=array();
          $sttng_lctn_empty_err_arr=array(); $sttng_lctn_pipe_excss_err_arr=array(); $sttng_lctn_pipe_err_arr=array();
          $sttng_lctn_smcln_excss_err_arr=array(); $sttng_lctn_nt2_cln_err_arr=array(); $sttng_lctn_smcln_err_arr=array();
          $sttng_lctn_cln_excss_err_arr=array(); $sttng_lctn_cln_err_arr=array(); $sttng_lctn_hyphn_excss_err_arr=array();
          $sttng_lctn_sffx_err_arr=array(); $sttng_lctn_hyphn_err_arr=array(); $sttng_lctn_url_err_arr=array();
          $sttng_lctn_alt_list_err_arr=array(); $sttng_lctn_alt_empty_err_arr=array(); $sttng_lctn_alt_hyphn_excss_err_arr=array();
          $sttng_lctn_alt_sffx_err_arr=array(); $sttng_lctn_alt_hyphn_err_arr=array(); $sttng_lctn_alt_url_err_arr=array();
          $sttng_lctn_alt_err_arr=array(); $sttng_lctn_alt_assoc_err_arr=array(); $sttng_hsh_err_arr=array();
          $sttng_tm_empty_err_arr=array(); $sttng_tm_smcln_excss_err_arr=array(); $sttng_tm_nt2_tm_spn_err_arr=array();
          $sttng_tm_nt2_cln_err_arr=array(); $sttng_tm_smcln_err_arr=array(); $sttng_tm_cln_excss_err_arr=array();
          $sttng_tm_nt1_tm_spn_err_arr=array(); $sttng_tm_cln_err_arr=array(); $sttng_tm_url_err_arr=array();
          foreach($sttng_tm_lctn_plcs as $sttng_tm_lctn_plc)
          {
            $sttng_tm_lctn_plc=trim($sttng_tm_lctn_plc);
            if(!preg_match('/\S+/', $sttng_tm_lctn_plc))
            {
              $sttng_empty_err_arr[]=$sttng_tm_lctn_plc;
              if(count($sttng_empty_err_arr)==1) {$errors['sttng_empty']='</br>**There is 1 empty entry in the group array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['sttng_empty']='</br>**There are '.count($sttng_empty_err_arr).' empty entries in the group array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($sttng_tm_lctn_plc, '++')>1) {$sttng_pls_excss_err_arr[]=$sttng_tm_lctn_plc; $sttng_tm_lctn=''; $errors['sttng_pls_excss']='</br>**You may only use [++] once per group for place assignation. Please amend: '.html(implode(' / ', $sttng_pls_excss_err_arr)).'.**';}
              elseif(preg_match('/(\S+.*)?\+\+.*\S+/', $sttng_tm_lctn_plc))
              {
                list($sttng_tm_lctn, $sttng_plc_list)=explode('++', $sttng_tm_lctn_plc);
                $sttng_tm_lctn=trim($sttng_tm_lctn); $sttng_plc_list=trim($sttng_plc_list);

                $sttng_plcs=explode('//', $sttng_plc_list);
                if(count($sttng_plcs)>250)
                {$errors['sttng_plc_array_excss']='**Maximum of 250 places per group allowed.**';}
                else
                {
                  $sttng_plc_dplct_arr=array();
                  foreach($sttng_plcs as $sttng_plc)
                  {
                    $sttng_plc=trim($sttng_plc);
                    if(!preg_match('/\S+/', $sttng_plc))
                    {
                      $sttng_plc_empty_err_arr[]=$sttng_plc;
                      if(count($sttng_plc_empty_err_arr)==1) {$errors['sttng_plc_empty']='</br>**There is 1 empty entry in a place array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                      else {$errors['sttng_plc_empty']='</br>**There are '.count($sttng_plc_empty_err_arr).' empty entries in the place arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      $sttng_plc_errors=0;

                      if(preg_match('/##/', $sttng_plc))
                      {$sttng_plc_errors++; $sttng_plc_hsh_err_arr[]=$sttng_plc; $errors['sttng_plc_hsh']='</br>**Place name may not include [##]. Please amend: '.html(implode(' / ', $sttng_plc_hsh_err_arr)).'.**';}

                      if(substr_count($sttng_plc, ';;')>1) {$sttng_plc_errors++; $sttng_plc_smcln_excss_err_arr[]=$sttng_plc; $errors['sttng_plc_smcln_excss']='</br>**You may only use [;;] once per place-suffix note coupling. Please amend: '.html(implode(' / ', $sttng_plc_smcln_excss_err_arr)).'.**'; $sttng_plc_nt2='';}
                      elseif(preg_match('/\S+.*;;.*\S+/', $sttng_plc)) {list($sttng_plc, $sttng_plc_nt2)=explode(';;', $sttng_plc); $sttng_plc=trim($sttng_plc); $sttng_plc_nt2=trim($sttng_plc_nt2);
                      if(strlen($sttng_plc_nt2)>255) {$errors['sttng_plc_nt2_excss_lngth']='</br>**Place suffix note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      elseif(preg_match('/::/', $sttng_plc_nt2)) {$sttng_plc_errors++; $sttng_plc_nt2_cln_err_arr[]=$sttng_plc_nt2; $errors['sttng_plc_nt2_cln']='</br>**Place suffix note may not include [::]. Please amend: '.html(implode(' / ', $sttng_plc_nt2_cln_err_arr)).'.**';}}
                      elseif(substr_count($sttng_plc, ';;')==1) {$sttng_plc_errors++; $sttng_plc_smcln_err_arr[]=$sttng_plc; $errors['sttng_plc_smcln']='</br>**Place suffix note assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $sttng_plc_smcln_err_arr)).'.**'; $sttng_plc_nt2='';}
                      else {$sttng_plc_nt2='';}

                      if(substr_count($sttng_plc, '::')>1) {$sttng_plc_errors++; $sttng_plc_cln_excss_err_arr[]=$sttng_plc; $errors['sttng_plc_cln_excss']='</br>**You may only use [::] once per place-prefix note coupling. Please amend: '.html(implode(' / ', $sttng_plc_cln_excss_err_arr)).'.**'; $sttng_plc_nt1='';}
                      elseif(preg_match('/\S+.*::.*\S+/', $sttng_plc)) {list($sttng_plc_nt1, $sttng_plc)=explode('::', $sttng_plc); $sttng_plc_nt1=trim($sttng_plc_nt1); $sttng_plc=trim($sttng_plc);
                      if(strlen($sttng_plc_nt1)>255) {$errors['sttng_plc_nt1_excss_lngth']='</br>**Place prefix note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}}
                      elseif(substr_count($sttng_plc, '::')==1) {$sttng_plc_errors++; $sttng_plc_cln_err_arr[]=$sttng_plc; $errors['sttng_plc_cln']='</br>**Place prefix note assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $sttng_plc_cln_err_arr)).'.**'; $sttng_plc_nt1='';}
                      else {$sttng_plc_nt1='';}

                      $sttng_plc_url=generateurl($sttng_plc);
                      $sttng_plc_dplct_arr[]=$sttng_plc_url;
                      if(count(array_unique($sttng_plc_dplct_arr))<count($sttng_plc_dplct_arr))
                      {$errors['sttng_plc_dplct']='</br>**There are entries within group arrays that create duplicate place URLs.**';}

                      if(strlen($sttng_plc)>255)
                      {$sttng_plc_errors++; $errors['sttng_plc_excss_lngth']='</br>**Place name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                      if($sttng_plc_errors==0)
                      {
                        $sttng_plc_cln=cln($sttng_plc);
                        $sttng_plc_url_cln=cln($sttng_plc_url);

                        $sql= "SELECT plc_nm
                              FROM plc
                              WHERE NOT EXISTS (SELECT 1 FROM plc WHERE plc_nm='$sttng_plc_cln')
                              AND plc_url='$sttng_plc_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing place URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          $sttng_plc_url_err_arr[]=$row['plc_nm'];
                          if(count($sttng_plc_url_err_arr)==1)
                          {$errors['sttng_plc_url']='</br>**Duplicate place URL exists. Did you mean to type: '.html(implode(' / ', $sttng_plc_url_err_arr)).'?**';}
                          else
                          {$errors['sttng_plc_url']='</br>**Duplicate place URLs exist. Did you mean to type: '.html(implode(' / ', $sttng_plc_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }
                }
              }
              elseif(substr_count($sttng_tm_lctn_plc, '++')==1) {$sttng_pls_err_arr[]=$sttng_tm_lctn_plc; $sttng_tm_lctn=''; $errors['sttng_pls']='</br>**Place assignation must use [++] in the correct format. Please amend: '.html(implode(' / ', $sttng_pls_err_arr)).'.**';}
              else {$sttng_tm_lctn=$sttng_tm_lctn_plc;}

              if(substr_count($sttng_tm_lctn, '##')>1) {$sttng_hsh_excss_err_arr[]=$sttng_tm_lctn; $sttng_tm_list=''; $errors['sttng_hsh_excss']='</br>**You may only use [##] once per group for location assignation. Please amend: '.html(implode(' / ', $sttng_hsh_excss_err_arr)).'.**';}
              elseif(preg_match('/(\S+.*)?##.*\S+/', $sttng_tm_lctn))
              {
                list($sttng_tm_list, $sttng_lctn_list)=explode('##', $sttng_tm_lctn);
                $sttng_tm_list=trim($sttng_tm_list); $sttng_lctn_list=trim($sttng_lctn_list);

                $sttng_lctns=explode('//', $sttng_lctn_list);
                if(count($sttng_lctns)>250)
                {$errors['sttng_lctn_array_excss']='**Maximum of 250 locations per group allowed.**';}
                else
                {
                  $sttng_lctn_dplct_arr=array();
                  foreach($sttng_lctns as $sttng_lctn)
                  {
                    $sttng_lctn_alt_no_assocs=array();
                    $sttng_lctn=trim($sttng_lctn);
                    if(!preg_match('/\S+/', $sttng_lctn))
                    {
                      $sttng_lctn_empty_err_arr[]=$sttng_lctn;
                      if(count($sttng_lctn_empty_err_arr)==1) {$errors['sttng_lctn_empty']='</br>**There is 1 empty entry in a location array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                      else {$errors['sttng_lctn_empty']='</br>**There are '.count($sttng_lctn_empty_err_arr).' empty entries in the location arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      $sttng_lctn_errors=0;

                      if(substr_count($sttng_lctn, '||')>1) {$sttng_lctn_errors++; $sttng_lctn_pipe_excss_err_arr[]=$sttng_lctn; $sttng_lctn_alt_list=''; $errors['sttng_lctn_pipe_excss']='</br>**You may only use [||] once per location for alternate location assignation. Please amend: '.html(implode(' / ', $sttng_lctn_pipe_excss_err_arr)).'.**';}
                      elseif(preg_match('/\S+.*\|\|.*\S+/', $sttng_lctn))
                      {
                        list($sttng_lctn, $sttng_lctn_alt_list)=explode('||', $sttng_lctn);
                        $sttng_lctn=trim($sttng_lctn); $sttng_lctn_alt_list=trim($sttng_lctn_alt_list);
                      }
                      elseif(substr_count($sttng_lctn, '||')==1) {$sttng_lctn_errors++; $sttng_lctn_pipe_err_arr[]=$sttng_lctn; $sttng_lctn_alt_list=''; $errors['sttng_lctn_pipe']='</br>**Alternate location assignation must use [||] in the correct format. Please amend: '.html(implode(' / ', $sttng_lctn_pipe_err_arr)).'.**';}
                      else {$sttng_lctn_alt_list='';}

                      if(substr_count($sttng_lctn, ';;')>1) {$sttng_lctn_errors++; $sttng_lctn_smcln_excss_err_arr[]=$sttng_lctn; $errors['sttng_lctn_smcln_excss']='</br>**You may only use [;;] once per location-suffix note coupling. Please amend: '.html(implode(' / ', $sttng_lctn_smcln_excss_err_arr)).'.**'; $sttng_lctn_nt2='';}
                      elseif(preg_match('/\S+.*;;.*\S+/', $sttng_lctn)) {list($sttng_lctn, $sttng_lctn_nt2)=explode(';;', $sttng_lctn); $sttng_lctn=trim($sttng_lctn); $sttng_lctn_nt2=trim($sttng_lctn_nt2);
                      if(strlen($sttng_lctn_nt2)>255) {$errors['sttng_lctn_nt2_excss_lngth']='</br>**Location suffix note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      elseif(preg_match('/::/', $sttng_lctn_nt2)) {$sttng_lctn_errors++; $sttng_lctn_nt2_cln_err_arr[]=$sttng_lctn_nt2; $errors['sttng_lctn_nt2_cln']='</br>**Location suffix note may not include [::]. Please amend: '.html(implode(' / ', $sttng_lctn_nt2_cln_err_arr)).'.**';}}
                      elseif(substr_count($sttng_lctn, ';;')==1) {$sttng_lctn_errors++; $sttng_lctn_smcln_err_arr[]=$sttng_lctn; $errors['sttng_lctn_smcln']='</br>**Location suffix note assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $sttng_lctn_smcln_err_arr)).'.**'; $sttng_lctn_nt2='';}
                      else {$sttng_lctn_nt2='';}

                      if(substr_count($sttng_lctn, '::')>1) {$sttng_lctn_errors++; $sttng_lctn_cln_excss_err_arr[]=$sttng_lctn; $errors['sttng_lctn_cln_excss']='</br>**You may only use [::] once per location-prefix note coupling. Please amend: '.html(implode(' / ', $sttng_lctn_cln_excss_err_arr)).'.**'; $sttng_lctn_nt1='';}
                      elseif(preg_match('/\S+.*::.*\S+/', $sttng_lctn)) {list($sttng_lctn_nt1, $sttng_lctn)=explode('::', $sttng_lctn); $sttng_lctn_nt1=trim($sttng_lctn_nt1); $sttng_lctn=trim($sttng_lctn);
                      if(strlen($sttng_lctn_nt1)>255) {$errors['sttng_lctn_nt1_excss_lngth']='</br>**Location prefix note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}}
                      elseif(substr_count($sttng_lctn, '::')==1) {$sttng_lctn_errors++; $sttng_lctn_cln_err_arr[]=$sttng_lctn; $errors['sttng_lctn_cln']='</br>**Location prefix note assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $sttng_lctn_cln_err_arr)).'.**'; $sttng_lctn_nt1='';}
                      else {$sttng_lctn_nt1='';}

                      if(substr_count($sttng_lctn, '--')>1)
                      {
                        $sttng_lctn_errors++; $sttng_lctn_sffx_num='0'; $sttng_lctn_hyphn_excss_err_arr[]=$sttng_lctn;
                        $errors['sttng_lctn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per location. Please amend: '.html(implode(' / ', $sttng_lctn_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $sttng_lctn))
                      {
                        list($sttng_lctn_no_sffx, $sttng_lctn_sffx_num)=explode('--', $sttng_lctn);
                        $sttng_lctn_no_sffx=trim($sttng_lctn_no_sffx); $sttng_lctn_sffx_num=trim($sttng_lctn_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $sttng_lctn_sffx_num))
                        {
                          $sttng_lctn_errors++; $sttng_lctn_sffx_num='0'; $sttng_lctn_sffx_err_arr[]=$sttng_lctn;
                          $errors['sttng_lctn_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $sttng_lctn_sffx_err_arr)).'**';
                        }
                        $sttng_lctn=$sttng_lctn_no_sffx;
                      }
                      elseif(substr_count($sttng_lctn, '--')==1)
                      {$sttng_lctn_errors++; $sttng_lctn_sffx_num='0'; $sttng_lctn_hyphn_err_arr[]=$sttng_lctn;
                      $errors['sttng_lctn_hyphn']='</br>**Location suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $sttng_lctn_hyphn_err_arr)).'**';}
                      else
                      {$sttng_lctn_sffx_num='0';}

                      if($sttng_lctn_sffx_num) {$sttng_lctn_sffx_rmn=' ('.romannumeral($sttng_lctn_sffx_num).')';} else {$sttng_lctn_sffx_rmn='';}

                      $sttng_lctn_url=generateurl($sttng_lctn.$sttng_lctn_sffx_rmn);
                      $sttng_lctn_dplct_arr[]=$sttng_lctn_url;
                      if(count(array_unique($sttng_lctn_dplct_arr))<count($sttng_lctn_dplct_arr))
                      {$errors['sttng_lctn_dplct']='</br>**There are entries within group arrays that create duplicate location URLs.**';}

                      if(strlen($sttng_lctn)>255 || strlen($sttng_lctn_url)>255)
                      {$sttng_lctn_errors++; $errors['sttng_lctn_excss_lngth']='</br>**Location name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                      if($sttng_lctn_errors==0)
                      {
                        $sttng_lctn_cln=cln($sttng_lctn);
                        $sttng_lctn_sffx_num_cln=cln($sttng_lctn_sffx_num);
                        $sttng_lctn_url_cln=cln($sttng_lctn_url);

                        $sql= "SELECT lctn_nm
                              FROM lctn
                              WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$sttng_lctn_cln' AND lctn_sffx_num='$sttng_lctn_sffx_num_cln')
                              AND lctn_url='$sttng_lctn_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing location URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          $sttng_lctn_url_err_arr[]=$row['lctn_nm'];
                          if(count($sttng_lctn_url_err_arr)==1) {$errors['sttng_lctn_url']='</br>**Duplicate location URL exists. Did you mean to type: '.html(implode(' / ', $sttng_lctn_url_err_arr)).'?**';}
                          else {$errors['sttng_lctn_url']='</br>**Duplicate location URLs exist. Did you mean to type: '.html(implode(' / ', $sttng_lctn_url_err_arr)).'?**';}
                        }
                        else
                        {
                          $sql="SELECT lctn_id, lctn_nm, lctn_sffx_num, lctn_url FROM lctn WHERE lctn_url='$sttng_lctn_url_cln'";
                          $result=mysqli_query($link, $sql);
                          if(!$result) {$error='Error checking for existence of location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                          $row=mysqli_fetch_array($result);
                          if($sttng_lctn_alt_list)
                          {
                            if(mysqli_num_rows($result)==0) {$sttng_lctn_alt_list_err_arr[]=$sttng_lctn.$sttng_lctn_sffx_rmn; $errors['sttng_lctn_alt_list']='</br>**The following locations do not yet exist (and therefore cannot be assigned alternate locations): '.html(implode(' / ', $sttng_lctn_alt_list_err_arr)).'.**';}
                            else
                            {
                              $lctn_id=$row['lctn_id'];
                              if($row['lctn_sffx_num']) {$lctn_sffx_rmn_url_lnk=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$lctn_sffx_rmn_url_lnk='';}
                              $lctn_url_lnk='<a href="/production/setting/location/'.html($row['lctn_url']).'" target="/production/setting/location/'.html($row['lctn_url']).'">'.html($row['lctn_nm'].$lctn_sffx_rmn_url_lnk).'</a>';

                              $sttng_lctn_alts=explode('>>', $sttng_lctn_alt_list);
                              if(count($sttng_lctn_alts)>250)
                              {$errors['sttng_lctn_alt_array_excss']='**Maximum of 250 locations per alternate location array allowed.**';}
                              else
                              {
                                $sttng_lctn_alt_dplct_arr=array();
                                foreach($sttng_lctn_alts as $sttng_lctn_alt)
                                {
                                  $sttng_lctn_alt=trim($sttng_lctn_alt);
                                  if(!preg_match('/\S+/', $sttng_lctn_alt))
                                  {
                                    $sttng_lctn_alt_empty_err_arr[]=$sttng_lctn_alt;
                                    if(count($sttng_lctn_alt_empty_err_arr)==1) {$errors['sttng_lctn_alt_empty']='</br>**There is 1 empty entry in an alternate location array (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                                    else {$errors['sttng_lctn_alt_empty']='</br>**There are '.count($sttng_lctn_alt_empty_err_arr).' empty entries in alternate location arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                                  }
                                  else
                                  {
                                    $sttng_lctn_alt_errors=0;

                                    if(substr_count($sttng_lctn_alt, '--')>1)
                                    {
                                      $sttng_lctn_alt_errors++; $sttng_lctn_alt_sffx_num='0'; $sttng_lctn_alt_hyphn_excss_err_arr[]=$sttng_lctn_alt;
                                      $errors['sttng_lctn_alt_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per alternate location. Please amend: '.html(implode(' / ', $sttng_lctn_alt_hyphn_excss_err_arr)).'.**';
                                    }
                                    elseif(preg_match('/^\S+.*--.+$/', $sttng_lctn_alt))
                                    {
                                      list($sttng_lctn_alt_no_sffx, $sttng_lctn_alt_sffx_num)=explode('--', $sttng_lctn_alt);
                                      $sttng_lctn_alt_no_sffx=trim($sttng_lctn_alt_no_sffx); $sttng_lctn_alt_sffx_num=trim($sttng_lctn_alt_sffx_num);

                                      if(!preg_match('/^[1-9][0-9]{0,1}$/', $sttng_lctn_alt_sffx_num))
                                      {
                                        $sttng_lctn_alt_errors++; $sttng_lctn_alt_sffx_num='0'; $sttng_lctn_alt_sffx_err_arr[]=$sttng_lctn_alt;
                                        $errors['sttng_lctn_alt_sffx']='</br>**The suffix (for alternate locations) must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $sttng_lctn_alt_sffx_err_arr)).'**';
                                      }
                                      $sttng_lctn_alt=$sttng_lctn_alt_no_sffx;
                                    }
                                    elseif(substr_count($sttng_lctn_alt, '--')==1)
                                    {$sttng_lctn_alt_errors++; $sttng_lctn_alt_sffx_num='0'; $sttng_lctn_alt_hyphn_err_arr[]=$sttng_lctn_alt;
                                    $errors['sttng_lctn_alt_hyphn']='</br>**Alternate location suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $sttng_lctn_alt_hyphn_err_arr)).'**';}
                                    else
                                    {$sttng_lctn_alt_sffx_num='0';}

                                    if($sttng_lctn_alt_sffx_num) {$sttng_lctn_alt_sffx_rmn=' ('.romannumeral($sttng_lctn_alt_sffx_num).')';} else {$sttng_lctn_alt_sffx_rmn='';}

                                    $sttng_lctn_alt_url=generateurl($sttng_lctn_alt.$sttng_lctn_alt_sffx_rmn);
                                    $sttng_lctn_alt_dplct_arr[]=$sttng_lctn_alt_url;
                                    if(count(array_unique($sttng_lctn_alt_dplct_arr))<count($sttng_lctn_alt_dplct_arr))
                                    {$errors['sttng_lctn_alt_dplct']='</br>**There are entries within alternate location arrays that create duplicate location URLs.**';}

                                    if(strlen($sttng_lctn_alt)>255 || strlen($sttng_lctn_alt_url)>255)
                                    {$sttng_lctn_alt_errors++; $errors['sttng_lctn_alt_excss_lngth']='</br>**Alternate location name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                                    if($sttng_lctn_alt_errors==0)
                                    {
                                      $sttng_lctn_alt_cln=cln($sttng_lctn_alt);
                                      $sttng_lctn_alt_sffx_num_cln=cln($sttng_lctn_alt_sffx_num);
                                      $sttng_lctn_alt_url_cln=cln($sttng_lctn_alt_url);

                                      $sql= "SELECT lctn_nm FROM lctn
                                            WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$sttng_lctn_alt_cln' AND lctn_sffx_num='$sttng_lctn_alt_sffx_num_cln')
                                            AND lctn_url='$sttng_lctn_alt_url_cln'";
                                      $result=mysqli_query($link, $sql);
                                      if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                                      $row=mysqli_fetch_array($result);
                                      if(mysqli_num_rows($result)>0)
                                      {
                                        $sttng_lctn_alt_url_err_arr[]=$row['lctn_nm'];
                                        if(count($sttng_lctn_alt_url_err_arr)==1) {$errors['sttng_lctn_alt_url']='</br>**Duplicate location URL exists (from alternate location arrays). Did you mean to type: '.html(implode(' / ', $sttng_lctn_alt_url_err_arr)).'?**';}
                                        else {$errors['sttng_lctn_alt_url']='</br>**Duplicate location URLs exist (from alternate location arrays). Did you mean to type: '.html(implode(' / ', $sttng_lctn_alt_url_err_arr)).'?**';}
                                      }
                                      else
                                      {
                                        $sql="SELECT lctn_id FROM lctn WHERE lctn_url='$sttng_lctn_alt_url_cln'";
                                        $result=mysqli_query($link, $sql);
                                        if(!$result) {$error='Error checking for existence of location (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                                        $row=mysqli_fetch_array($result);
                                        if(mysqli_num_rows($result)==0) {$sttng_lctn_alt_err_arr[]=$sttng_lctn_alt.$sttng_lctn_alt_sffx_rmn; $errors['sttng_lctn_alt']='</br>**The following locations from alternate location arrays do not yet exist (and can therefore not be assigned): '.html(implode(' / ', $sttng_lctn_alt_err_arr)).'.';}
                                        else
                                        {
                                          $lctn_alt_id=$row['lctn_id'];

                                          $sql="SELECT 1 FROM rel_lctn WHERE rel_lctn1='$lctn_id' AND rel_lctn2='$lctn_alt_id'";
                                          $result=mysqli_query($link, $sql);
                                          if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                                          $row=mysqli_fetch_array($result);
                                          if(mysqli_num_rows($result)==0) {$sttng_lctn_alt_no_assocs[]=$sttng_lctn_alt.$sttng_lctn_alt_sffx_rmn;}
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

                    if(count($sttng_lctn_alt_no_assocs)>0)
                    {
                      $sttng_lctn_alt_assoc_err_arr[]=$lctn_url_lnk.': '.implode(' / ', $sttng_lctn_alt_no_assocs);
                      $errors['sttng_lctn_alt_assoc']='</br>**Associations do not exist between the following locations and their listed alternates. Please amend:**</br>'.implode('</br>', $sttng_lctn_alt_assoc_err_arr);
                    }
                  }
                }
              }
              elseif(substr_count($sttng_tm_lctn, '##')==1) {$sttng_hsh_err_arr[]=$sttng_tm_lctn; $sttng_tm_list=''; $errors['sttng_hsh']='</br>**Location assignation must use [##] in the correct format. Please amend: '.html(implode(' / ', $sttng_hsh_err_arr)).'.**';}
              else {$sttng_tm_list=$sttng_tm_lctn;}

              if($sttng_tm_list)
              {
                if(preg_match('/^\S+.*\*$/', $sttng_tm_list))
                {$sttng_tm_list=preg_replace('/(\S+.*)(\*)/', '$1', $sttng_tm_list); $tm_spn='1'; $sttng_tm_list=trim($sttng_tm_list);}
                else {$tm_spn='0';}

                $sttng_tms=explode('//', $sttng_tm_list);
                if(count($sttng_tms)>250) {$errors['sttng_tm_array_excss']='**Maximum of 250 times per group allowed.**';}
                elseif(count($sttng_tms)<2 && $tm_spn) {$errors['sttng_tm_spn']='**Minimum of two times per group required when using time span function.**';}
                else
                {
                  $sttng_tm_dplct_arr=array();
                  foreach($sttng_tms as $sttng_tm)
                  {
                    $sttng_tm=trim($sttng_tm);
                    if(!preg_match('/\S+/', $sttng_tm))
                    {
                      $sttng_tm_empty_err_arr[]=$sttng_tm;
                      if(count($sttng_tm_empty_err_arr)==1) {$errors['sttng_tm_empty']='</br>**There is 1 empty entry in a time array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                      else {$errors['sttng_tm_empty']='</br>**There are '.count($sttng_tm_empty_err_arr).' empty entries in the time arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      $sttng_tm_errors=0;

                      if(substr_count($sttng_tm, ';;')>1) {$sttng_tm_errors++; $sttng_tm_smcln_excss_err_arr[]=$sttng_tm; $errors['sttng_tm_smcln_excss']='</br>**You may only use [;;] once per timr-suffix note coupling. Please amend: '.html(implode(' / ', $sttng_tm_smcln_excss_err_arr)).'.**'; $sttng_tm_nt2='';}
                      elseif(preg_match('/\S+.*;;.*\S+/', $sttng_tm)) {list($sttng_tm, $sttng_tm_nt2)=explode(';;', $sttng_tm); $sttng_tm=trim($sttng_tm); $sttng_tm_nt2=trim($sttng_tm_nt2);
                      if(strlen($sttng_tm_nt2)>255) {$errors['sttng_tm_nt2_excss_lngth']='</br>**Time suffix note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      elseif($tm_spn) {$sttng_tm_nt2_tm_spn_err_arr[]=$sttng_tm.';;'.$sttng_tm_nt2; $errors['sttng_tm_nt2_tm_spn']='</br>**Time suffix note cannot be applied if time is part of a time span. Please amend: '.html(implode(' / ', $sttng_tm_nt2_tm_spn_err_arr)).'.**';}
                      elseif(preg_match('/::/', $sttng_tm_nt2)) {$sttng_tm_errors++; $sttng_tm_nt2_cln_err_arr[]=$sttng_tm_nt2; $errors['sttng_tm_nt2_cln']='</br>**Time suffix note may not include [::]. Please amend: '.html(implode(' / ', $sttng_tm_nt2_cln_err_arr)).'.**';}}
                      elseif(substr_count($sttng_tm, ';;')==1) {$sttng_tm_errors++; $sttng_tm_smcln_err_arr[]=$sttng_tm; $errors['sttng_tm_smcln']='</br>**Time suffix note assignation must use [;;] in the correct format. Please amend: '.html(implode(' / ', $sttng_tm_smcln_err_arr)).'.**'; $sttng_tm_nt2='';}
                      else {$sttng_tm_nt2='';}

                      if(substr_count($sttng_tm, '::')>1) {$sttng_tm_errors++; $sttng_tm_cln_excss_err_arr[]=$sttng_tm; $errors['sttng_tm_cln_excss']='</br>**You may only use [::] once per time-prefix note coupling. Please amend: '.html(implode(' / ', $sttng_tm_cln_excss_err_arr)).'.**'; $sttng_tm_nt1='';}
                      elseif(preg_match('/\S+.*::.*\S+/', $sttng_tm)) {list($sttng_tm_nt1, $sttng_tm)=explode('::', $sttng_tm); $sttng_tm_nt1=trim($sttng_tm_nt1); $sttng_tm=trim($sttng_tm);
                      if(strlen($sttng_tm_nt1)>255) {$errors['sttng_tm_nt1_excss_lngth']='</br>**Time prefix note is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      elseif($tm_spn) {$sttng_tm_nt1_tm_spn_err_arr[]=$sttng_tm_nt1.'::'.$sttng_tm; $errors['sttng_tm_nt1_tm_spn']='</br>**Time prefix note cannot be applied if time is part of a time span. Please amend: '.html(implode(' / ', $sttng_tm_nt1_tm_spn_err_arr)).'.**';}}
                      elseif(substr_count($sttng_tm, '::')==1) {$sttng_tm_errors++; $sttng_tm_cln_err_arr[]=$sttng_tm; $errors['sttng_tm_cln']='</br>**Time prefix note assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $sttng_tm_cln_err_arr)).'.**'; $sttng_tm_nt1='';}
                      else {$sttng_tm_nt1='';}

                      $sttng_tm_url=generateurl($sttng_tm);
                      $sttng_tm_dplct_arr[]=$sttng_tm_url;
                      if(count(array_unique($sttng_tm_dplct_arr))<count($sttng_tm_dplct_arr))
                      {$errors['sttng_tm_dplct']='</br>**There are entries within group arrays that create duplicate time URLs.**';}

                      if(strlen($sttng_tm)>255)
                      {$sttng_tm_errors++; $errors['sttng_tm_excss_lngth']='</br>**Time name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                      if($sttng_tm_errors==0)
                      {
                        $sttng_tm_cln=cln($sttng_tm);
                        $sttng_tm_url_cln=cln($sttng_tm_url);

                        $sql= "SELECT tm_nm
                              FROM tm
                              WHERE NOT EXISTS (SELECT 1 FROM tm WHERE tm_nm='$sttng_tm_cln')
                              AND tm_url='$sttng_tm_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing time URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          $sttng_tm_url_err_arr[]=$row['tm_nm'];
                          if(count($sttng_tm_url_err_arr)==1)
                          {$errors['sttng_tm_url']='</br>**Duplicate time URL exists. Did you mean to type: '.html(implode(' / ', $sttng_tm_url_err_arr)).'?**';}
                          else
                          {$errors['sttng_tm_url']='</br>**Duplicate time URLs exist. Did you mean to type: '.html(implode(' / ', $sttng_tm_url_err_arr)).'?**';}
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

    if(preg_match('/\S+/', $wri_list))
    {
      if($tr_lg) {$errors['wri_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      elseif($coll_wrks) {$errors['wri_coll_wrks_checked']='**This field must be empty if collected works button is applied.**';}
      else
      {
        $wri_comp_prsn_rls=explode(',,', $_POST['wri_list']);
        if(count($wri_comp_prsn_rls)>250)
        {$errors['wri_rl_array_excss']='**Maximum of 250 writer roles allowed.**';}
        else
        {
          $wri_empty_err_arr=array(); $wri_pls_excss_err_arr=array(); $src_mat_cln_excss_err_arr=array();
          $src_mat_empty_err_arr=array(); $src_mat_hyphn_excss_err_arr=array(); $src_mat_sffx_err_arr=array();
          $src_mat_hyphn_err_arr=array(); $src_mat_smcln_excss_err_arr=array(); $src_frmt_url_err_arr=array();
          $src_mat_url_err_arr=array(); $src_mat_smcln_err_arr=array(); $src_mat_cln_err_arr=array();
          $wri_pls_err_arr=array(); $wri_cln_excss_err_arr=array(); $wri_cln_err_arr=array();
          $wri_comp_prsn_empty_err_arr=array(); $wri_pipe_excss_err_arr=array(); $wri_pipe_err_arr=array();
          $wri_prsn_empty_err_arr=array(); $wri_comp_tld_excss_err_arr=array(); $wri_comp_tld_err_arr=array();
          $wri_comp_hyphn_excss_err_arr=array(); $wri_comp_hyphn_excss_err_arr=array(); $wri_comp_sffx_err_arr=array();
          $wri_comp_hyphn_err_arr=array(); $wri_comp_url_err_arr=array(); $wri_prsn_tld_excss_err_arr=array();
          $wri_prsn_tld_err_arr=array(); $wri_prsn_hyphn_excss_err_arr=array(); $wri_prsn_sffx_err_arr=array();
          $wri_prsn_hyphn_err_arr=array(); $wri_prsn_smcln_excss_err_arr=array(); $wri_prsn_smcln_err_arr=array();
          $wri_prsn_nm_err_arr=array(); $wri_prsn_url_err_arr=array();
          foreach($wri_comp_prsn_rls as $wri_comp_prsn_rl)
          {
            $wri_comp_prsn_rl=trim($wri_comp_prsn_rl);

            if(!preg_match('/\S+/', $wri_comp_prsn_rl))
            {
              $wri_empty_err_arr[]=$wri_comp_prsn_rl;
              if(count($wri_empty_err_arr)==1) {$errors['wri_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['wri_empty']='</br>**There are '.count($wri_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($wri_comp_prsn_rl, '++')>1)
              {
                $wri_pls_excss_err_arr[]=$wri_comp_prsn_rl;  $wri_comp_prsn_rl=''; $src_mat=NULL;
                $errors['wri_pls_excss']='</br>**You may only use [++] once per writer role-source material assignation. Please amend: '.html(implode(' / ', $wri_pls_excss_err_arr)).'.**';
              }
              elseif(preg_match('/\S+.*\+\+(.*\S+)?/', $wri_comp_prsn_rl))
              {
                list($src_mats_rl, $wri_comp_prsn_rl)=explode('++', $wri_comp_prsn_rl);
                $src_mats_rl=trim($src_mats_rl); $wri_comp_prsn_rl=trim($wri_comp_prsn_rl);

                if(substr_count($src_mats_rl, '::')>1)
                {
                  $src_mat_cln_excss_err_arr[]=$src_mats_rl;
                  $errors['src_mat_cln_excss']='</br>**You may only use [::] once per source material-role coupling. Please amend: '.html(implode(' / ', $src_mat_cln_excss_err_arr)).'.**';
                }
                elseif(preg_match('/\S+.*::.*\S+/', $src_mats_rl))
                {
                  list($src_mat_rl, $src_mat_list)=explode('::', $src_mats_rl);
                  $src_mat_rl=trim($src_mat_rl); $src_mat_list=trim($src_mat_list);

                  if(strlen($src_mat_rl)>255)
                  {$errors['src_mat_rl']='</br>**Source material role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                  $src_mats=explode('>>', $src_mat_list);
                  if(count($src_mats)>250)
                  {$errors['src_mats_array_excss']='</br>**Maximum of 250 source materials allowed per role.**';}
                  else
                  {
                    $src_mat_dplct_arr=array();
                    foreach($src_mats as $src_mat_nm_frmt)
                    {
                      $src_mat_errors=0; $src_frmt_errors=0;

                      if(!preg_match('/\S+/', $src_mat_nm_frmt))
                      {
                        $src_mat_empty_err_arr[]=$src_mat_nm_frmt;
                        if(count($src_mat_empty_err_arr)==1) {$errors['src_mat_empty']='</br>**There is 1 empty entry in a source material array (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                        else {$errors['src_mat_empty']='</br>**There are '.count($src_mat_empty_err_arr).' empty entries in source material arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                      }
                      else
                      {
                        if(substr_count($src_mat_nm_frmt, '--')>1)
                        {
                          $src_mat_errors++; $src_mat_sffx_num='0'; $src_mat_hyphn_excss_err_arr[]=$src_mat_nm_frmt;
                          $errors['src_mat_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per source material. Please amend: '.html(implode(' / ', $src_mat_hyphn_excss_err_arr)).'.**';
                        }
                        elseif(preg_match('/^\S+.*--.+$/', $src_mat_nm_frmt))
                        {
                          list($src_mat_nm_frmt_no_sffx, $src_mat_sffx_num)=explode('--', $src_mat_nm_frmt);
                          $src_mat_nm_frmt_no_sffx=trim($src_mat_nm_frmt_no_sffx); $src_mat_sffx_num=trim($src_mat_sffx_num);

                          if(!preg_match('/^[1-9][0-9]{0,1}$/', $src_mat_sffx_num))
                          {
                            $src_mat_errors++; $src_mat_sffx_num='0'; $src_mat_sffx_err_arr[]=$src_mat_nm_frmt;
                            $errors['src_mat_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $src_mat_sffx_err_arr)).'**';
                          }
                          $src_mat_nm_frmt=$src_mat_nm_frmt_no_sffx;
                        }
                        elseif(substr_count($src_mat_nm_frmt, '--')==1)
                        {$src_mat_errors++; $src_mat_sffx_num='0'; $src_mat_hyphn_err_arr[]=$src_mat_nm_frmt;
                        $errors['src_mat_hyphn']='</br>**Source material suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $src_mat_hyphn_err_arr)).'**';}
                        else
                        {$src_mat_sffx_num='0';}

                        if($src_mat_sffx_num) {$src_mat_sffx_rmn=' ('.romannumeral($src_mat_sffx_num).')';} else {$src_mat_sffx_rmn='';}

                        if(substr_count($src_mat_nm_frmt, ';;')>1)
                        {
                          $src_mat_smcln_excss_err_arr[]=$src_mat_nm_frmt;
                          $errors['src_mat_smcln_excss']='</br>**You may only use [;;] once per material name-format coupling. Please amend: '.html(implode(' / ', $src_mat_smcln_excss_err_arr)).'.**';
                        }
                        elseif(preg_match('/\S+.*;;.*\S+/', $src_mat_nm_frmt))
                        {
                          list($src_mat_nm, $src_frmt_nm)=explode(';;', $src_mat_nm_frmt);
                          $src_mat_nm=trim($src_mat_nm); $src_frmt_nm=trim($src_frmt_nm);

                          $src_mat_url=generateurl($src_mat_nm.$src_mat_sffx_rmn);
                          $src_frmt_url=generateurl($src_frmt_nm);

                          $src_mat_dplct_arr[]=$src_mat_url.' '.$src_frmt_url;
                          if(count(array_unique($src_mat_dplct_arr))<count($src_mat_dplct_arr))
                          {$errors['src_mat_dplct']='</br>**There are entries within a role array that create duplicate source material URLs.**';}

                          if(strlen($src_frmt_nm)>255)
                          {$src_frmt_errors++; $errors['src_frmt_nm_excss_lngth']='</br>**Format name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                          if($src_mat_errors==0 && $src_frmt_errors==0)
                          {
                            $src_frmt_nm_cln=cln($src_frmt_nm);
                            $src_frmt_url_cln=cln($src_frmt_url);

                            $sql= "SELECT frmt_nm
                                  FROM frmt
                                  WHERE NOT EXISTS (SELECT 1 FROM frmt WHERE frmt_nm='$src_frmt_nm_cln')
                                  AND frmt_url='$src_frmt_url_cln'";
                            $result=mysqli_query($link, $sql);
                            if(!$result) {$error='Error checking for existing material URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                            $row=mysqli_fetch_array($result);
                            if(mysqli_num_rows($result)>0)
                            {
                              $src_mat_errors++; $src_frmt_url_err_arr[]=$row['frmt_nm'];
                              if(count($src_frmt_url_err_arr)==1)
                              {$errors['src_frmt_url']='</br>**Duplicate format URL exists. Did you mean to type: '.html(implode(' / ', $src_frmt_url_err_arr)).'?**';}
                              else
                              {$errors['src_frmt_url']='</br>**Duplicate format URLs exist. Did you mean to type: '.html(implode(' / ', $src_frmt_url_err_arr)).'?**';}
                            }

                            if(strlen($src_mat_nm)>255 || strlen($src_mat_url)>255)
                            {$src_mat_errors++; $errors['src_mat_nm_excss_lngth']='</br>**Material name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                            $src_mat_nm_cln=cln($src_mat_nm);
                            $src_mat_sffx_num_cln=cln($src_mat_sffx_num);
                            $src_mat_url_cln=cln($src_mat_url);

                            if($src_mat_errors==0)
                            {
                              $sql= "SELECT mat_nm, frmt_nm, mat_sffx_num
                                    FROM mat
                                    INNER JOIN frmt ON frmtid=frmt_id
                                    WHERE NOT EXISTS (SELECT 1 FROM mat WHERE mat_nm='$src_mat_nm_cln' AND mat_sffx_num='$src_mat_sffx_num_cln')
                                    AND mat_url='$src_mat_url_cln'
                                    AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$src_frmt_url_cln')";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking for existing material URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              $row=mysqli_fetch_array($result);
                              if(mysqli_num_rows($result)>0)
                              {
                                if($row['mat_sffx_num']) {$src_mat_sffx_num='--'.$row['mat_sffx_num'];} else {$src_mat_sffx_num='';}
                                $src_mat_url_err_arr[]=$row['mat_nm'].';;'.$row['frmt_nm'].$src_mat_sffx_num;
                                if(count($src_mat_url_err_arr)==1)
                                {$errors['src_mat_url']='</br>**Duplicate material URL exists. Did you mean to type: '.html(implode(' / ', $src_mat_url_err_arr)).'?**';}
                                else
                                {$errors['src_mat_url']='</br>**Duplicate material URLs exist. Did you mean to type: '.html(implode(' / ', $src_mat_url_err_arr)).'?**';}
                              }
                            }
                          }
                        }
                        else
                        {
                          $src_mat_smcln_err_arr[]=$src_mat_nm_frmt;
                          $errors['src_mat_smcln']='</br>**You must assign a name and corresponding format to the following using [;;]: '.html(implode(' / ', $src_mat_smcln_err_arr)).'.**';
                        }
                      }
                    }
                  }
                }
                else
                {
                  $src_mat_cln_err_arr[]=$src_mats_rl;
                  $errors['src_mat_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $src_mat_cln_err_arr)).'.**';
                }
              }
              elseif(substr_count($wri_comp_prsn_rl, '++')==1)
              {
                $wri_pls_err_arr[]=$wri_comp_prsn_rl; $wri_comp_prsn_rl='';  $src_mat=NULL;
                $errors['wri_pls']='</br>**Writer role-source material assignation must use [++] in the correct format. Please amend: '.html(implode(' / ', $wri_pls_err_arr)).'.**';
              }

              if(preg_match('/\S+/', $wri_comp_prsn_rl))
              {
                if(substr_count($wri_comp_prsn_rl, '::')>1)
                {
                  $wri_cln_excss_err_arr[]=$wri_comp_prsn_rl;
                  $errors['wri_cln_excss']='</br>**You may only use [::] once per writer-role coupling. Please amend: '.html(implode(' / ', $wri_cln_excss_err_arr)).'.**';
                }
                elseif(preg_match('/\S+.*::.*\S+/', $wri_comp_prsn_rl))
                {
                  list($wri_rl, $wri_comp_prsn_list)=explode('::', $wri_comp_prsn_rl);
                  $wri_rl=trim($wri_rl); $wri_comp_prsn_list=trim($wri_comp_prsn_list);

                  if(strlen($wri_rl)>255)
                  {$errors['wri_rl']='</br>**Writer role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

                  $wri_comps_ppl=explode('>>', $wri_comp_prsn_list);
                  $wri_rl_ttl_array=array(); $wri_comp_nm_array=array(); $wri_prsn_nm_array=array();
                  foreach($wri_comps_ppl as $wri_comp_prsn)
                  {
                    $wri_comp_prsn=trim($wri_comp_prsn);
                    if(!preg_match('/\S+/', $wri_comp_prsn))
                    {
                      $wri_comp_prsn_empty_err_arr[]=$wri_comp_prsn;
                      if(count($wri_comp_prsn_empty_err_arr)==1) {$errors['wri_comp_prsn_empty']='</br>**There is 1 empty entry in a person arrray (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                      else {$errors['wri_comp_prsn_empty']='</br>**There are '.count($wri_comp_prsn_empty_err_arr).' empty entries in person arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      if(substr_count($wri_comp_prsn, '||')>1)
                      {
                        $wri_prsn_nm_list=''; $wri_pipe_excss_err_arr[]=$wri_comp_prsn;
                        $errors['wri_pipe_excss']='</br>**You may only use [||] once per writer company-members coupling. Please amend: '.html(implode(' / ', $wri_pipe_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\|\|/', $wri_comp_prsn))
                      {
                        if(preg_match('/\S+.*\|\|(.*\S+)?/', $wri_comp_prsn))
                        {
                          list($wri_comp_nm, $wri_prsn_nm_list)=explode('||', $wri_comp_prsn);
                          $wri_comp_nm=trim($wri_comp_nm); $wri_prsn_nm_list=trim($wri_prsn_nm_list);

                          if(preg_match('/^\S+.*\*\*\*$/', $wri_comp_nm))
                          {$wri_comp_nm=preg_replace('/(\S+.*)(\*\*\*)/', '$1', $wri_comp_nm); $wri_comp_nm=trim($wri_comp_nm);}
                          elseif(preg_match('/^\S+.*\*\*$/', $wri_comp_nm))
                          {$wri_comp_nm=preg_replace('/(\S+.*)(\*\*)/', '$1', $wri_comp_nm); $wri_comp_nm=trim($wri_comp_nm);}
                          elseif(preg_match('/^\S+.*\*$/', $wri_comp_nm))
                          {$wri_comp_nm=preg_replace('/(\S+.*)(\*)/', '$1', $wri_comp_nm); $wri_comp_nm=trim($wri_comp_nm);}

                          $wri_comp_nm_array[]=$wri_comp_nm; $wri_rl_ttl_array[]=$wri_comp_nm;
                        }
                        else
                        {
                          $wri_prsn_nm_list=''; $wri_pipe_err_arr[]=$wri_comp_prsn;
                          $errors['wri_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $wri_pipe_err_arr)).'.**';
                        }
                      }
                      else
                      {
                        if(preg_match('/^\S+.*\*\*\*$/', $wri_comp_prsn))
                        {$wri_comp_prsn=preg_replace('/(\S+.*)(\*\*\*)/', '$1', $wri_comp_prsn); $wri_comp_prsn=trim($wri_comp_prsn);}
                        elseif(preg_match('/^\S+.*\*\*$/', $wri_comp_prsn))
                        {$wri_comp_prsn=preg_replace('/(\S+.*)(\*\*)/', '$1', $wri_comp_prsn); $wri_comp_prsn=trim($wri_comp_prsn);}
                        elseif(preg_match('/^\S+.*\*$/', $wri_comp_prsn))
                        {$wri_comp_prsn=preg_replace('/(\S+.*)(\*)/', '$1', $wri_comp_prsn); $wri_comp_prsn=trim($wri_comp_prsn);}

                        $wri_prsn_nm_array[]=$wri_comp_prsn; $wri_rl_ttl_array[]=$wri_comp_prsn; $wri_prsn_nm_list='';
                      }

                      if(preg_match('/\S+/', $wri_prsn_nm_list))
                      {
                        $wri_prsn_nms=explode('//', $wri_prsn_nm_list);
                        foreach($wri_prsn_nms as $wri_prsn_nm)
                        {
                          $wri_prsn_nm=trim($wri_prsn_nm);
                          if(!preg_match('/\S+/', $wri_prsn_nm))
                          {
                            $wri_prsn_empty_err_arr[]=$wri_prsn_nm;
                            if(count($wri_prsn_empty_err_arr)==1) {$errors['wri_prsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                            else {$errors['wri_prsn_empty']='</br>**There are '.count($wri_prsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                          }
                          else
                          {$wri_prsn_nm_array[]=$wri_prsn_nm; $wri_rl_ttl_array[]=$wri_prsn_nm;}
                        }
                      }

                      if(count($wri_rl_ttl_array)>250)
                      {$errors['wri_rl_ttl_array_excss']='</br>**Maximum of 250 entries (companies and people per role) allowed.**';}
                    }
                  }

                  if(count($wri_comp_nm_array)>0)
                  {
                    $wri_comp_dplct_arr=array();
                    foreach($wri_comp_nm_array as $wri_comp_nm)
                    {
                      $wri_comp_nm=trim($wri_comp_nm);
                      $wri_comp_errors=0;
                      if(substr_count($wri_comp_nm, '~~')>1)
                      {
                        $wri_comp_errors++; $wri_comp_tld_excss_err_arr[]=$wri_comp_nm;
                        $errors['wri_comp_tld_excss']='</br>**You may only use [~~] once per writer (company)-role coupling. Please amend: '.html(implode(' / ', $wri_comp_tld_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*~~.*\S+/', $wri_comp_nm))
                      {
                        list($wri_comp_rl, $wri_comp_nm)=explode('~~', $wri_comp_nm);
                        $wri_comp_rl=trim($wri_comp_rl); $wri_comp_nm=trim($wri_comp_nm);

                        if(strlen($wri_comp_rl)>255)
                        {$errors['wri_comp_rl']='</br>**Writer (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($wri_comp_nm, '~~')==1)
                      {$wri_comp_errors++; $wri_comp_tld_err_arr[]=$wri_comp_nm;
                      $errors['wri_comp_tld']='</br>**Writer (company)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $wri_comp_tld_err_arr)).'**';}

                      if(substr_count($wri_comp_nm, '--')>1)
                      {
                        $wri_comp_errors++; $wri_comp_sffx_num='0'; $wri_comp_hyphn_excss_err_arr[]=$wri_comp_nm;
                        $errors['wri_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per writer (company). Please amend: '.html(implode(' / ', $wri_comp_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $wri_comp_nm))
                      {
                        list($wri_comp_nm_no_sffx, $wri_comp_sffx_num)=explode('--', $wri_comp_nm);
                        $wri_comp_nm_no_sffx=trim($wri_comp_nm_no_sffx); $wri_comp_sffx_num=trim($wri_comp_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $wri_comp_sffx_num))
                        {
                          $wri_comp_errors++; $wri_comp_sffx_num='0'; $wri_comp_sffx_err_arr[]=$wri_comp_nm;
                          $errors['wri_comp_sffx']='</br>**Writer (company) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $wri_comp_sffx_err_arr)).'**';
                        }
                        $wri_comp_nm=$wri_comp_nm_no_sffx;
                      }
                      elseif(substr_count($wri_comp_nm, '--')==1)
                      {$wri_comp_errors++; $wri_comp_sffx_num='0'; $wri_comp_hyphn_err_arr[]=$wri_comp_nm;
                      $errors['wri_comp_hyphn']='</br>**Writer (company) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $wri_comp_hyphn_err_arr)).'**';}
                      else
                      {$wri_comp_sffx_num='0';}

                      if($wri_comp_sffx_num) {$wri_comp_sffx_rmn=' ('.romannumeral($wri_comp_sffx_num).')';} else {$wri_comp_sffx_rmn='';}

                      $wri_comp_url=generateurl($wri_comp_nm.$wri_comp_sffx_rmn);

                      $wri_comp_dplct_arr[]=$wri_comp_url;
                      if(count(array_unique($wri_comp_dplct_arr))<count($wri_comp_dplct_arr))
                      {$errors['wri_comp_dplct']='</br>**There are entries within a role array that create duplicate company URLs.**';}

                      if(strlen($wri_comp_nm)>255 || strlen($wri_comp_url)>255)
                      {$wri_comp_errors++; $errors['wri_comp_nm_excss_lngth']='</br>**Writer (company) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                      if($wri_comp_errors==0)
                      {
                        $wri_comp_nm_cln=cln($wri_comp_nm);
                        $wri_comp_sffx_num_cln=cln($wri_comp_sffx_num);
                        $wri_comp_url_cln=cln($wri_comp_url);

                        $sql= "SELECT comp_nm, comp_sffx_num
                              FROM comp
                              WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$wri_comp_nm_cln' AND comp_sffx_num='$wri_comp_sffx_num_cln')
                              AND comp_url='$wri_comp_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing writer company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['comp_sffx_num']) {$wri_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                          else {$wri_comp_url_error_sffx_dsply='';}
                          $wri_comp_url_err_arr[]=$row['comp_nm'].$wri_comp_url_error_sffx_dsply;
                          if(count($wri_comp_url_err_arr)==1)
                          {$errors['wri_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $wri_comp_url_err_arr)).'?**';}
                          else
                          {$errors['wri_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $wri_comp_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }

                  if(count($wri_prsn_nm_array)> 0)
                  {
                    $wri_prsn_dplct_arr=array();
                    foreach($wri_prsn_nm_array as $wri_prsn_nm)
                    {
                      $wri_prsn_nm=trim($wri_prsn_nm);
                      $wri_prsn_errors=0;
                      if(substr_count($wri_prsn_nm, '~~')>1)
                      {
                        $wri_prsn_errors++; $wri_prsn_tld_excss_err_arr[]=$wri_prsn_nm;
                        $errors['wri_prsn_tld_excss']='</br>**You may only use [~~] once per writer (person)-role coupling. Please amend: '.html(implode(' / ', $wri_prsn_tld_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*~~.*\S+/', $wri_prsn_nm))
                      {
                        list($wri_prsn_rl, $wri_prsn_nm)=explode('~~', $wri_prsn_nm);
                        $wri_prsn_rl=trim($wri_prsn_rl); $wri_prsn_nm=trim($wri_prsn_nm);

                        if(strlen($wri_prsn_rl)>255)
                        {$errors['wri_prsn_rl']='</br>**Writer (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
                      }
                      elseif(substr_count($wri_prsn_nm, '~~')==1)
                      {$wri_prsn_errors++; $wri_prsn_tld_err_arr[]=$wri_prsn_nm;
                      $errors['wri_prsn_tld']='</br>**Writer (person)-role assignation must use [~~] in the correct format. Please amend: '.html(implode(' / ', $wri_prsn_tld_err_arr)).'**';}

                      if(substr_count($wri_prsn_nm, '--')>1)
                      {
                        $wri_prsn_errors++; $wri_prsn_sffx_num='0'; $wri_prsn_hyphn_excss_err_arr[]=$wri_prsn_nm;
                        $errors['wri_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per writer (person). Please amend: '.html(implode(' / ', $wri_prsn_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $wri_prsn_nm))
                      {
                        list($wri_prsn_nm_no_sffx, $wri_prsn_sffx_num)=explode('--', $wri_prsn_nm);
                        $wri_prsn_nm_no_sffx=trim($wri_prsn_nm_no_sffx); $wri_prsn_sffx_num=trim($wri_prsn_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $wri_prsn_sffx_num))
                        {
                          $wri_prsn_errors++; $wri_prsn_sffx_num='0'; $wri_prsn_sffx_err_arr[]=$wri_prsn_nm;
                          $errors['wri_prsn_sffx']='</br>**Writer (person) suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $wri_prsn_sffx_err_arr)).'**';
                        }
                        $wri_prsn_nm=$wri_prsn_nm_no_sffx;
                      }
                      elseif(substr_count($wri_prsn_nm, '--')==1)
                      {$wri_prsn_errors++; $wri_prsn_sffx_num='0'; $wri_prsn_hyphn_err_arr[]=$wri_prsn_nm;
                      $errors['wri_prsn_hyphn']='</br>**Writer (person) suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $wri_prsn_hyphn_err_arr)).'**';}
                      else
                      {$wri_prsn_sffx_num='0';}

                      if($wri_prsn_sffx_num) {$wri_prsn_sffx_rmn=' ('.romannumeral($wri_prsn_sffx_num).')';} else {$wri_prsn_sffx_rmn='';}

                      if(substr_count($wri_prsn_nm, ';;')>1)
                      {
                        $wri_prsn_errors++; $wri_prsn_smcln_excss_err_arr[]=$wri_prsn_nm;
                        $errors['wri_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $wri_prsn_smcln_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/\S+.*;;(.*\S+)?/', $wri_prsn_nm))
                      {
                        list($wri_prsn_frst_nm, $wri_prsn_lst_nm)=explode(';;', $wri_prsn_nm);
                        $wri_prsn_frst_nm=trim($wri_prsn_frst_nm); $wri_prsn_lst_nm=trim($wri_prsn_lst_nm);

                        if(preg_match('/\S+/', $wri_prsn_lst_nm)) {$wri_prsn_lst_nm_dsply=' '.$wri_prsn_lst_nm;}
                        else {$wri_prsn_lst_nm_dsply='';}

                        $wri_prsn_fll_nm=$wri_prsn_frst_nm.$wri_prsn_lst_nm_dsply;
                        $wri_prsn_url=generateurl($wri_prsn_fll_nm.$wri_prsn_sffx_rmn);

                        $wri_prsn_dplct_arr[]=$wri_prsn_url;
                        if(count(array_unique($wri_prsn_dplct_arr))<count($wri_prsn_dplct_arr))
                        {$errors['wri_prsn_dplct']='</br>**There are entries within a role array that create duplicate person URLs.**';}

                        if(strlen($wri_prsn_fll_nm)>255 || strlen($wri_prsn_url)>255)
                        {$wri_prsn_errors++; $errors['wri_prsn_excss_lngth']='</br>**Writer (person) name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}
                      }
                      else
                      {
                        $wri_prsn_errors++; $wri_prsn_smcln_err_arr[]=$wri_prsn_nm;
                        $errors['wri_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $wri_prsn_smcln_err_arr)).'.**';
                      }

                      if($wri_prsn_errors==0)
                      {
                        $wri_prsn_frst_nm_cln=cln($wri_prsn_frst_nm);
                        $wri_prsn_lst_nm_cln=cln($wri_prsn_lst_nm);
                        $wri_prsn_fll_nm_cln=cln($wri_prsn_fll_nm);
                        $wri_prsn_sffx_num_cln=cln($wri_prsn_sffx_num);
                        $wri_prsn_url_cln=cln($wri_prsn_url);

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$wri_prsn_frst_nm_cln' AND prsn_lst_nm='$wri_prsn_lst_nm_cln')
                              AND prsn_fll_nm='$wri_prsn_fll_nm_cln' AND prsn_sffx_num='$wri_prsn_sffx_num_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for writer person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$wri_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$wri_prsn_nm_error_sffx_dsply='';}
                          $wri_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$wri_prsn_nm_error_sffx_dsply;
                          if(count($wri_prsn_nm_err_arr)==1)
                          {$errors['wri_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $wri_prsn_nm_err_arr)).'.**';}
                          else
                          {$errors['wri_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $wri_prsn_nm_err_arr)).'.**';}
                        }

                        $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                              FROM prsn
                              WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$wri_prsn_fll_nm_cln' AND prsn_sffx_num='$wri_prsn_sffx_num_cln')
                              AND prsn_url='$wri_prsn_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing writer person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          if($row['prsn_sffx_num']) {$wri_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                          else {$wri_prsn_url_error_sffx_dsply='';}
                          $wri_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$wri_prsn_url_error_sffx_dsply;
                          if(count($wri_prsn_url_err_arr)==1)
                          {$errors['wri_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $wri_prsn_url_err_arr)).'?**';}
                          else
                          {$errors['wri_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $wri_prsn_url_err_arr)).'?**';}
                        }
                      }
                    }
                  }
                }
                else
                {
                  $wri_cln_err_arr[]=$wri_comp_prsn_rl;
                  $errors['wri_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $wri_cln_err_arr)).'.**';
                }
              }
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $alt_nm_list))
    {
      if($tr_lg) {$errors['alt_nm_tr_lg_chckd']='**This field must be empty if tour leg button is applied.**';}
      else
      {
        $alt_nms=explode(',,', $alt_nm_list);
        if(count($alt_nms)>250)
        {$errors['alt_nm_array_excss']='**Maximum of 250 entries allowed.**';}
        else
        {
          $alt_nm_empty_err_arr=array(); $alt_nm_cln_excss_err_arr=array(); $alt_nm_cln_err_arr=array();
          $alt_nm_dplct_arr=array();
          foreach($alt_nms as $alt_nm)
          {
            $alt_nm=trim($alt_nm);
            if(!preg_match('/\S+/', $alt_nm))
            {
              $alt_nm_empty_err_arr[]=$alt_nm;
              if(count($alt_nm_empty_err_arr)==1) {$errors['alt_nm_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
              else {$errors['alt_nm_empty']='</br>**There are '.count($alt_nm_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            }
            else
            {
              if(substr_count($alt_nm, '::')>1)
              {
                $alt_nm_cln_excss_err_arr[]=$alt_nm;
                $errors['alt_nm_cln_excss']='</br>**You may only use [::] for description assignment once per alternate name. Please amend: '.html(implode(' / ', $alt_nm_cln_excss_err_arr)).'.**';
              }
              elseif(preg_match('/^\S+.*::.*\S+$/', $alt_nm))
              {
                list($alt_nm, $alt_nm_dscr)=explode('::', $alt_nm);
                $alt_nm=trim($alt_nm); $alt_nm_dscr=trim($alt_nm_dscr);

                if(strlen($alt_nm_dscr)>255)
                {$errors['alt_nm_dscr_excss_lngth']='</br>**Alternate name description is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
              }
              elseif(substr_count($alt_nm, '::')==1)
              {$alt_nm_cln_err_arr[]=$alt_nm;
              $errors['alt_nm_cln']='</br>**Alternate name description assignation must use [::] in the correct format. Please amend: '.html(implode(' / ', $alt_nm_cln_err_arr)).'**';}

              $alt_nm_dplct_arr[]=$alt_nm;
              if(count(array_unique($alt_nm_dplct_arr))<count($alt_nm_dplct_arr))
              {$errors['alt_nm_dplct']='</br>**There are duplicate entries within the array.**';}

              if(strlen($alt_nm)>255)
              {$errors['alt_nm_excss_lngth']='</br>**Alternate name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
            }
          }
        }
      }
    }
?>