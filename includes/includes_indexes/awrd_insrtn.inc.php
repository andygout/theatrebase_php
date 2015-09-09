<?php
      $sql="SELECT 1 FROM awrds WHERE awrds_url='$awrds_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existence of awards: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)==0)
      {
        $sql="INSERT INTO awrds(awrds_nm, awrds_alph, awrds_url) VALUES('$awrds_nm', CASE WHEN '$awrds_alph'!='' THEN '$awrds_alph' END, '$awrds_url')";
        if(!mysqli_query($link, $sql)) {$error='Error adding awards data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      if($edit)
      {
        $sql="SELECT awrds_id FROM awrds WHERE awrds_url='$awrds_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring for awrds_id with which to update awrd set: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        $awrds_id=$row['awrds_id'];

        $sql= "UPDATE awrd SET
              awrd_yr='$awrd_yr',
              awrd_yr_end=CASE WHEN '$awrd_yr_end'!='' THEN '$awrd_yr_end' END,
              awrd_yr_url='$awrd_yr_url',
              awrd_dt=CASE WHEN '$awrd_dt'!='' THEN '$awrd_dt' END,
              awrdsid='$awrds_id'
              WHERE awrd_id='$awrd_id'";
        if(!mysqli_query($link, $sql))
        {$error='Error updating awards info for submitted award: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
      else
      {
        $sql= "INSERT INTO awrd(awrd_yr, awrd_yr_end, awrd_yr_url, awrd_dt, awrdsid)
              SELECT '$awrd_yr', CASE WHEN '$awrd_yr_end'!='' THEN '$awrd_yr_end' END, '$awrd_yr_url', CASE WHEN '$awrd_dt'!='' THEN '$awrd_dt' END, awrds_id
              FROM awrds WHERE awrds_url='$awrds_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding award data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

        $awrd_id=mysqli_insert_id($link);
      }

      $sql="SELECT 1 FROM awrds WHERE awrds_url='$awrds_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existence of awards: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)==0)
      {
        $sql="INSERT INTO awrds(awrds_nm, awrds_url) VALUES('$awrds_nm', '$awrds_url')";
        if(!mysqli_query($link, $sql)) {$error='Error adding awards data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      if($thtr_nm)
      {
        $thtr=$thtr_nm;
        if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $thtr))
        {
          list($thtr, $thtr_sffx_num)=explode('--', $thtr);
          $thtr=trim($thtr); $thtr_sffx_num=trim($thtr_sffx_num);
          $thtr_sffx_rmn=' ('.romannumeral($thtr_sffx_num).')';
        }
        else
        {$thtr_sffx_num='0'; $thtr_sffx_rmn='';}

        if(preg_match('/\S+.*::.*\S+/', $thtr))
        {
          list($thtr, $thtr_lctn)=explode('::', $thtr);
          $thtr=trim($thtr); $thtr_lctn=trim($thtr_lctn);
          $thtr_lctn_dsply=' ('.$thtr_lctn.')';
        }
        else
        {$thtr_lctn=''; $thtr_lctn_dsply='';}

        if(preg_match('/\S+.*;;.*\S+/', $thtr))
        {
          list($thtr, $sbthtr_nm)=explode(';;', $thtr);
          $thtr=trim($thtr); $sbthtr_nm=trim($sbthtr_nm);
          $sbthtr_nm_dsply=': '.$sbthtr_nm;
        }
        else
        {$sbthtr_nm=''; $sbthtr_nm_dsply='';}

        $thtr_fll_nm=$thtr.$sbthtr_nm_dsply.$thtr_lctn_dsply;
        $thtr_url=generateurl($thtr_fll_nm.$thtr_sffx_rmn);
        $thtr_alph=alph($thtr_fll_nm);

        $sql="SELECT 1 FROM thtr WHERE thtr_url='$thtr_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of theatre: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO thtr(thtr_nm, sbthtr_nm, thtr_lctn, thtr_fll_nm, thtr_alph, thtr_sffx_num, thtr_url, thtr_clsd, thtr_nm_exp, thtr_tr_ov)
                VALUES('$thtr', '$sbthtr_nm', '$thtr_lctn', '$thtr_fll_nm', CASE WHEN '$thtr_alph'!='' THEN '$thtr_alph' END, '$thtr_sffx_num', '$thtr_url', 0, 0, 0)";
          if(!mysqli_query($link, $sql)) {$error='Error adding theatre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql="UPDATE awrd SET thtrid=(SELECT thtr_id FROM thtr WHERE thtr_url='$thtr_url') WHERE awrd_id='$awrd_id'";
        if(!mysqli_query($link, $sql))
        {$error='Error updating award-venue association data for submitted award: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
      else
      {
        $sql="UPDATE awrd SET thtrid=NULL WHERE awrd_id='$awrd_id'";
        if(!mysqli_query($link, $sql))
        {$error='Error updating award-venue association data for submitted award: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      if(preg_match('/\S+/', $awrd_list))
      {
        $m=0;
        $awrd_ctgrys=explode('@@', $awrd_list);
        foreach($awrd_ctgrys as $awrd_ctgry)
        {
          $awrd_ctgry_ordr=++$m;

          list($awrd_ctgry_nm, $awrd_nom_dtl_dscr_list)=explode('==', $awrd_ctgry);
          $awrd_ctgry_nm=trim($awrd_ctgry_nm); $awrd_nom_dtl_dscr_list=trim($awrd_nom_dtl_dscr_list);

          if(preg_match('/\S+.*;;.*\S+/', $awrd_ctgry_nm))
          {
            list($awrd_ctgry_nm, $awrd_ctgry_alt_nm)=explode(';;', $awrd_ctgry_nm);
            $awrd_ctgry_nm=trim($awrd_ctgry_nm); $awrd_ctgry_alt_nm=trim($awrd_ctgry_alt_nm);
          }
          else
          {$awrd_ctgry_alt_nm=NULL;}

          $awrd_ctgry_url=generateurl($awrd_ctgry_nm);
          $awrd_ctgry_alph=alph($awrd_ctgry_nm);

          $sql="SELECT 1 FROM awrd_ctgry WHERE awrd_ctgry_url='$awrd_ctgry_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of award category: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO awrd_ctgry(awrd_ctgry_nm, awrd_ctgry_alph, awrd_ctgry_url)
                  VALUES('$awrd_ctgry_nm', CASE WHEN '$awrd_ctgry_alph'!='' THEN '$awrd_ctgry_alph' END, '$awrd_ctgry_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding award category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO awrdctgrys(awrdid, awrd_ctgry_ordr, awrd_ctgry_alt_nm, awrd_ctgryid)
                SELECT $awrd_id, '$awrd_ctgry_ordr', CASE WHEN '$awrd_ctgry_alt_nm'!='' THEN '$awrd_ctgry_alt_nm' END, awrd_ctgry_id
                FROM awrd_ctgry
                WHERE awrd_ctgry_url='$awrd_ctgry_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding awards categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

          $n=0;
          $awrd_nom_dtl_dscrs=explode(',,', $awrd_nom_dtl_dscr_list);
          foreach($awrd_nom_dtl_dscrs as $awrd_nom_dtl_dscr)
          {
            $nom_id=++$n;
            list($awrd_nom_dscr, $awrd_nom_ppl_prds_pts)=explode('::', $awrd_nom_dtl_dscr);
            $awrd_nom_dscr=trim($awrd_nom_dscr); $awrd_nom_ppl_prds_pts=trim($awrd_nom_ppl_prds_pts);

            if(preg_match('/^\S+.*\*$/', $awrd_nom_dscr))
            {$awrd_nom_dscr=preg_replace('/(\S+.*)(\*)/', '$1', $awrd_nom_dscr); $win_bool='1';} else {$win_bool='0';}
            $awrd_nom_dscr=trim($awrd_nom_dscr);

            $sql= "INSERT INTO awrdnoms(awrdid, nom_id, nom_win_dscr, win_bool, awrd_ctgryid)
                  SELECT $awrd_id, '$nom_id', '$awrd_nom_dscr', '$win_bool', awrd_ctgry_id FROM awrd_ctgry WHERE awrd_ctgry_url='$awrd_ctgry_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding awards-nomination association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

            if(preg_match('/(\S+.*)?\+\+.*\S+/', $awrd_nom_ppl_prds_pts))
            {
              list($awrd_nom_ppl_prds, $awrd_nom_pts)=explode('++', $awrd_nom_ppl_prds_pts);
              $awrd_nom_ppl_prds=trim($awrd_nom_ppl_prds); $awrd_nom_pts=trim($awrd_nom_pts);

              $nom_pts=explode('>>', $awrd_nom_pts);
              foreach($nom_pts as $nom_pt_nm_yr)
              {
                $nom_pt_nm_yr=trim($nom_pt_nm_yr);
                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $nom_pt_nm_yr))
                {
                  list($nom_pt_nm_yr, $nom_pt_sffx_num)=explode('--', $nom_pt_nm_yr);
                  $nom_pt_nm_yr=trim($nom_pt_nm_yr); $nom_pt_sffx_num=trim($nom_pt_sffx_num);
                  $nom_pt_sffx_rmn=' ('.romannumeral($nom_pt_sffx_num).')';
                }
                else
                {$nom_pt_sffx_num='0'; $nom_pt_sffx_rmn='';}

                list($nom_pt_nm, $nom_pt_yr)=explode('##', $nom_pt_nm_yr);
                $nom_pt_nm=trim($nom_pt_nm); $nom_pt_yr=trim($nom_pt_yr);

                if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $nom_pt_yr))
                {
                  list($nom_pt_yr_strtd, $nom_pt_yr_wrttn)=explode(';;', $nom_pt_yr); $nom_pt_yr_strtd=trim($nom_pt_yr_strtd); $nom_pt_yr_wrttn=trim($nom_pt_yr_wrttn);

                  if(preg_match('/^c(-)?/', $nom_pt_yr_strtd)) {$nom_pt_yr_strtd=preg_replace('/^c(.+)$/', '$1', $nom_pt_yr_strtd); $nom_pt_yr_strtd_c='1';}
                  else {$nom_pt_yr_strtd_c='0';}

                  if(preg_match('/^c(-)?/', $nom_pt_yr_wrttn)) {$nom_pt_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $nom_pt_yr_wrttn); $nom_pt_yr_wrttn_c='1';}
                  else {$nom_pt_yr_wrttn_c='0';}
                }
                else
                {
                  $nom_pt_yr_strtd_c='0'; $nom_pt_yr_strtd='0'; $nom_pt_yr_wrttn=$nom_pt_yr;
                  if(preg_match('/^c(-)?/', $nom_pt_yr_wrttn)) {$nom_pt_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $nom_pt_yr_wrttn); $nom_pt_yr_wrttn_c='1';}
                  else {$nom_pt_yr_wrttn_c='0';}
                }

                if($nom_pt_yr_strtd)
                {
                  if(preg_match('/^-/', $nom_pt_yr_strtd))
                  {
                    $nom_pt_yr_strtd_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $nom_pt_yr_strtd);
                    if(!preg_match('/^-/', $nom_pt_yr_wrttn)) {$nom_pt_yr_strtd_dsply .= ' BCE';}
                    $nom_pt_yr_strtd_dsply .= '-';
                    if($nom_pt_yr_strtd_c) {$nom_pt_yr_strtd_dsply='c.'.$nom_pt_yr_strtd_dsply;}
                  }
                  else {$nom_pt_yr_strtd_dsply=$nom_pt_yr_strtd.'-';}
                }
                else {$nom_pt_yr_strtd_dsply='';}

                if(preg_match('/^-/', $nom_pt_yr_wrttn)) {$nom_pt_yr_wrttn_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE)", $nom_pt_yr_wrttn);}
                else {$nom_pt_yr_wrttn_dsply=$nom_pt_yr_wrttn.')';}

                if($nom_pt_yr_wrttn_c) {$nom_pt_yr_wrttn_dsply='c.'.$nom_pt_yr_wrttn_dsply;}

                $nom_pt_nm_yr=$nom_pt_nm.' ('.$nom_pt_yr_strtd_dsply.$nom_pt_yr_wrttn_dsply;
                $nom_pt_url=generateurl($nom_pt_nm_yr.$nom_pt_sffx_rmn);
                $nom_pt_alph=alph($nom_pt_nm);

                $sql="SELECT 1 FROM pt WHERE pt_url='$nom_pt_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of nominee/winner (playtext): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO pt(pt_url, pt_nm, pt_alph, pt_nm_yr, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn)
                        VALUES('$nom_pt_url', '$nom_pt_nm', CASE WHEN '$nom_pt_alph'!='' THEN '$nom_pt_alph' END, '$nom_pt_nm_yr', '$nom_pt_sffx_num', '$nom_pt_yr_strtd_c', '$nom_pt_yr_strtd', '$nom_pt_yr_wrttn_c', '$nom_pt_yr_wrttn')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO awrdnompts(awrdid, nomid, nom_ptid, awrd_ctgryid)
                      SELECT $awrd_id, '$nom_id',
                      (SELECT pt_id FROM pt WHERE pt_url='$nom_pt_url'),
                      (SELECT awrd_ctgry_id FROM awrd_ctgry WHERE awrd_ctgry_url='$awrd_ctgry_url')";
                if(!mysqli_query($link, $sql)) {$error='Error adding awards-nominee/winner (playtext) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
            else
            {$awrd_nom_ppl_prds=$awrd_nom_ppl_prds_pts;}

            if(preg_match('/(\S+.*)?##.*\S+/', $awrd_nom_ppl_prds))
            {
              list($awrd_comp_prsn_list, $awrd_nom_prds)=explode('##', $awrd_nom_ppl_prds);
              $awrd_comp_prsn_list=trim($awrd_comp_prsn_list); $awrd_nom_prds=trim($awrd_nom_prds);

              $prdids=explode('>>', $awrd_nom_prds);
              foreach($prdids as $prdid)
              {
                $sql= "INSERT INTO awrdnomprds(awrdid, nomid, nom_prdid, awrd_ctgryid)
                      SELECT $awrd_id, '$nom_id', '$prdid', awrd_ctgry_id FROM awrd_ctgry WHERE awrd_ctgry_url='$awrd_ctgry_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding awards-nominee/winner (production) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
            else
            {$awrd_comp_prsn_list=$awrd_nom_ppl_prds;}

            if($awrd_comp_prsn_list)
            {
              $o=0;
              $awrd_comp_ppl=explode('>>', $awrd_comp_prsn_list);
              foreach($awrd_comp_ppl as $awrd_comp_prsn)
              {
                $nom_ordr=++$o;
                if(preg_match('/\|\|/', $awrd_comp_prsn))
                {
                  list($awrd_comp_nm, $awrd_prsn_nm_list)=explode('||', $awrd_comp_prsn);
                  $awrd_comp_nm=trim($awrd_comp_nm); $awrd_prsn_nm_list=trim($awrd_prsn_nm_list); $awrd_prsn_nm2='';
                }
                else
                {$awrd_comp_nm=''; $awrd_prsn_nm_list=''; $awrd_prsn_nm2=trim($awrd_comp_prsn);}

                if($awrd_comp_nm)
                {
                  $awrd_comp_nm=trim($awrd_comp_nm);
                  if(preg_match('/\S+.*~~.*\S+/', $awrd_comp_nm))
                  {
                    list($awrd_comp_nm, $awrd_comp_rl)=explode('~~', $awrd_comp_nm);
                    $awrd_comp_nm=trim($awrd_comp_nm); $awrd_comp_rl=trim($awrd_comp_rl);
                  }
                  else {$awrd_comp_rl='';}

                  if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $awrd_comp_nm))
                  {
                    list($awrd_comp_nm, $awrd_comp_sffx_num)=explode('--', $awrd_comp_nm);
                    $awrd_comp_nm=trim($awrd_comp_nm); $awrd_comp_sffx_num=trim($awrd_comp_sffx_num);
                    $awrd_comp_sffx_rmn=' ('.romannumeral($awrd_comp_sffx_num).')';
                  }
                  else
                  {$awrd_comp_sffx_num='0'; $awrd_comp_sffx_rmn='';}

                  $awrd_comp_url=generateurl($awrd_comp_nm.$awrd_comp_sffx_rmn);
                  $awrd_comp_alph=alph($awrd_comp_nm);

                  $sql="SELECT 1 FROM comp WHERE comp_url='$awrd_comp_url'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existence of nominee/winner (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  if(mysqli_num_rows($result)==0)
                  {
                    $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                          VALUES('$awrd_comp_nm', CASE WHEN '$awrd_comp_alph'!='' THEN '$awrd_comp_alph' END, '$awrd_comp_sffx_num', '$awrd_comp_url', 1, 0, 0)";
                    if(!mysqli_query($link, $sql)) {$error='Error adding nominee/winner (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql= "INSERT INTO awrdnomppl(awrdid, nomid, nom_ordr, nom_rl, nom_prsnid, nom_compid, awrd_ctgryid)
                        SELECT $awrd_id, '$nom_id', '$nom_ordr', '$awrd_comp_rl', '0',
                        (SELECT comp_id FROM comp WHERE comp_url='$awrd_comp_url'),
                        (SELECT awrd_ctgry_id FROM awrd_ctgry WHERE awrd_ctgry_url='$awrd_ctgry_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding awards-nominee/winner (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                if($awrd_prsn_nm_list)
                {
                  $awrd_prsn_nms=explode('//', $awrd_prsn_nm_list);
                  foreach($awrd_prsn_nms as $awrd_prsn_nm)
                  {
                    $nom_ordr=++$o;
                    $awrd_prsn_nm=trim($awrd_prsn_nm);
                    if(preg_match('/\S+.*~~.*\S+/', $awrd_prsn_nm))
                    {
                      list($awrd_prsn_nm, $awrd_prsn_rl)=explode('~~', $awrd_prsn_nm);
                      $awrd_prsn_nm=trim($awrd_prsn_nm); $awrd_prsn_rl=trim($awrd_prsn_rl);
                    }
                    else {$awrd_prsn_rl='';}

                    if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $awrd_prsn_nm))
                    {
                      list($awrd_prsn_nm, $awrd_prsn_sffx_num)=explode('--', $awrd_prsn_nm);
                      $awrd_prsn_nm=trim($awrd_prsn_nm); $awrd_prsn_sffx_num=trim($awrd_prsn_sffx_num);
                      $awrd_prsn_sffx_rmn=' ('.romannumeral($awrd_prsn_sffx_num).')';
                    }
                    else
                    {$awrd_prsn_sffx_num='0'; $awrd_prsn_sffx_rmn='';}

                    list($awrd_prsn_frst_nm, $awrd_prsn_lst_nm)=explode(';;', $awrd_prsn_nm);
                    $awrd_prsn_frst_nm=trim($awrd_prsn_frst_nm); $awrd_prsn_lst_nm=trim($awrd_prsn_lst_nm);

                    if(preg_match('/\S+/', $awrd_prsn_lst_nm))
                    {$awrd_prsn_lst_nm_dsply=' '.$awrd_prsn_lst_nm;}
                    else
                    {$awrd_prsn_lst_nm_dsply='';}

                    $awrd_prsn_fll_nm=$awrd_prsn_frst_nm.$awrd_prsn_lst_nm_dsply;
                    $awrd_prsn_url=generateurl($awrd_prsn_fll_nm.$awrd_prsn_sffx_rmn);

                    $sql="SELECT 1 FROM prsn WHERE prsn_url='$awrd_prsn_url'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existence of nominee/winner (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)==0)
                    {
                      $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                            VALUES('$awrd_prsn_fll_nm', '$awrd_prsn_frst_nm', '$awrd_prsn_lst_nm', '$awrd_prsn_sffx_num', '$awrd_prsn_url', '0')";
                      if(!mysqli_query($link, $sql)) {$error='Error adding nominee/winner (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    }

                    $sql= "INSERT INTO awrdnomppl(awrdid, nomid, nom_ordr, nom_rl, nom_compid, nom_prsnid, awrd_ctgryid)
                          SELECT $awrd_id, '$nom_id', '$nom_ordr', '$awrd_prsn_rl',
                          (SELECT comp_id FROM comp WHERE comp_url='$awrd_comp_url'),
                          (SELECT prsn_id FROM prsn WHERE prsn_url='$awrd_prsn_url'),
                          (SELECT awrd_ctgry_id FROM awrd_ctgry WHERE awrd_ctgry_url='$awrd_ctgry_url')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding awards-nominee/winner (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }
                }

                if($awrd_prsn_nm2)
                {
                  $awrd_prsn_nm=trim($awrd_prsn_nm2);
                  if(preg_match('/\S+.*~~.*\S+/', $awrd_prsn_nm2))
                  {
                    list($awrd_prsn_nm, $awrd_prsn_rl)=explode('~~', $awrd_prsn_nm2);
                    $awrd_prsn_nm=trim($awrd_prsn_nm); $awrd_prsn_rl=trim($awrd_prsn_rl);
                  }
                  else
                  {$awrd_prsn_rl='';}

                  if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $awrd_prsn_nm))
                  {
                    list($awrd_prsn_nm, $awrd_prsn_sffx_num)=explode('--', $awrd_prsn_nm);
                    $awrd_prsn_nm=trim($awrd_prsn_nm); $awrd_prsn_sffx_num=trim($awrd_prsn_sffx_num);
                    $awrd_prsn_sffx_rmn=' ('.romannumeral($awrd_prsn_sffx_num).')';
                  }
                  else
                  {$awrd_prsn_sffx_num='0'; $awrd_prsn_sffx_rmn='';}

                  list($awrd_prsn_frst_nm, $awrd_prsn_lst_nm)=explode(';;', $awrd_prsn_nm);
                  $awrd_prsn_frst_nm=trim($awrd_prsn_frst_nm); $awrd_prsn_lst_nm=trim($awrd_prsn_lst_nm);

                  if(preg_match('/\S+/', $awrd_prsn_lst_nm))
                  {$awrd_prsn_lst_nm_dsply=' '.$awrd_prsn_lst_nm;}
                  else
                  {$awrd_prsn_lst_nm_dsply='';}

                  $awrd_prsn_fll_nm=$awrd_prsn_frst_nm.$awrd_prsn_lst_nm_dsply;
                  $awrd_prsn_url=generateurl($awrd_prsn_fll_nm.$awrd_prsn_sffx_rmn);

                  $sql="SELECT 1 FROM prsn WHERE prsn_url='$awrd_prsn_url'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existence of nominee/winner (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  if(mysqli_num_rows($result)==0)
                  {
                    $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                          VALUES('$awrd_prsn_fll_nm', '$awrd_prsn_frst_nm', '$awrd_prsn_lst_nm', '$awrd_prsn_sffx_num', '$awrd_prsn_url', '0')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding nominee/winner (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql= "INSERT INTO awrdnomppl(awrdid, nomid, nom_ordr, nom_rl, nom_compid, nom_prsnid, awrd_ctgryid)
                        SELECT $awrd_id, '$nom_id', '$nom_ordr', '$awrd_prsn_rl', '0',
                        (SELECT prsn_id FROM prsn WHERE prsn_url='$awrd_prsn_url'),
                        (SELECT awrd_ctgry_id FROM awrd_ctgry WHERE awrd_ctgry_url='$awrd_ctgry_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding awards-nominee/winner (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }
            }
          }
        }
      }
?>