<?php
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
            $sql="INSERT INTO frmt(frmt_nm, frmt_url) VALUES('$frmt_nm', '$frmt_url')";
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

          $sql= "INSERT INTO prdmat(prdid, mat_ordr, matid)
                SELECT $prd_id, $mat_ordr, mat_id FROM mat WHERE mat_url='$mat_url' AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$frmt_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-material association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $pt_list))
      {
        $pt_nm_yrs=explode(',,', $pt_list);
        $n=0;
        foreach($pt_nm_yrs as $pt_nm_yr)
        {
          $pt_nm_yr=trim($pt_nm_yr);
          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $pt_nm_yr)) {list($pt_nm_yr, $pt_sffx_num)=explode('--', $pt_nm_yr); $pt_nm_yr=trim($pt_nm_yr); $pt_sffx_num=trim($pt_sffx_num); $pt_sffx_rmn=' ('.romannumeral($pt_sffx_num).')';}
          else {$pt_sffx_num='0'; $pt_sffx_rmn='';}

          list($pt_nm, $pt_yr)=explode('##', $pt_nm_yr); $pt_nm=trim($pt_nm); $pt_yr=trim($pt_yr);

          if(preg_match('/^(c)?(-)?[1-9][0-9]{0,3}\s*;;\s*(c)?(-)?[1-9][0-9]{0,3}$/', $pt_yr))
          {
            list($pt_yr_strtd, $pt_yr_wrttn)=explode(';;', $pt_yr); $pt_yr_strtd=trim($pt_yr_strtd); $pt_yr_wrttn=trim($pt_yr_wrttn);

            if(preg_match('/^c(-)?/', $pt_yr_strtd)) {$pt_yr_strtd=preg_replace('/^c(.+)$/', '$1', $pt_yr_strtd); $pt_yr_strtd_c='1';}
            else {$pt_yr_strtd_c='0';}

            if(preg_match('/^c(-)?/', $pt_yr_wrttn)) {$pt_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $pt_yr_wrttn); $pt_yr_wrttn_c='1';}
            else {$pt_yr_wrttn_c='0';}
          }
          else
          {
            $pt_yr_strtd_c='0'; $pt_yr_strtd='0'; $pt_yr_wrttn=$pt_yr;
            if(preg_match('/^c(-)?/', $pt_yr_wrttn)) {$pt_yr_wrttn=preg_replace('/^c(.+)$/', '$1', $pt_yr_wrttn); $pt_yr_wrttn_c='1';}
            else {$pt_yr_wrttn_c='0';}
          }

          if($pt_yr_strtd)
          {
            if(preg_match('/^-/', $pt_yr_strtd))
            {
              $pt_yr_strtd_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $pt_yr_strtd);
              if(!preg_match('/^-/', $pt_yr_wrttn)) {$pt_yr_strtd_dsply .= ' BCE';}
              $pt_yr_strtd_dsply .= '-';
              if($pt_yr_strtd_c) {$pt_yr_strtd_dsply='c.'.$pt_yr_strtd_dsply;}
            }
            else {$pt_yr_strtd_dsply=$pt_yr_strtd.'-';}
          }
          else {$pt_yr_strtd_dsply='';}

          if(preg_match('/^-/', $pt_yr_wrttn)) {$pt_yr_wrttn_dsply=preg_replace('/^-([1-9][0-9]{0,3})/', "$1 BCE)", $pt_yr_wrttn);}
          else {$pt_yr_wrttn_dsply=$pt_yr_wrttn.')';}

          if($pt_yr_wrttn_c) {$pt_yr_wrttn_dsply='c.'.$pt_yr_wrttn_dsply;}

          $pt_nm_yr=$pt_nm.' ('.$pt_yr_strtd_dsply.$pt_yr_wrttn_dsply;
          $pt_url=generateurl($pt_nm_yr.$pt_sffx_rmn);
          $pt_alph=alph($pt_nm);

          $sql="SELECT 1 FROM pt WHERE pt_url='$pt_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO pt(pt_url, pt_nm, pt_alph, pt_nm_yr, pt_sffx_num, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn)
                  VALUES('$pt_url', '$pt_nm', CASE WHEN '$pt_alph'!='' THEN '$pt_alph' END, '$pt_nm_yr', '$pt_sffx_num', '$pt_yr_strtd_c', '$pt_yr_strtd', '$pt_yr_wrttn_c', '$pt_yr_wrttn')";
            if(!mysqli_query($link, $sql)) {$error='Error adding playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql="INSERT INTO prdpt(prdid, ptid) SELECT $prd_id, pt_id FROM pt WHERE pt_url='$pt_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-playtext association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $thtr=$thtr_nm;
      if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $thtr))
      {
        list($thtr, $thtr_sffx_num)=explode('--', $thtr);
        $thtr=trim($thtr); $thtr_sffx_num=trim($thtr_sffx_num);
        $thtr_sffx_rmn=' ('.romannumeral($thtr_sffx_num).')';
      }
      else
      {$thtr_sffx_num='0'; $thtr_sffx_rmn='';}

      if(preg_match('/\S+.*::.*\S+/', $thtr)) {list($thtr, $thtr_lctn)=explode('::', $thtr); $thtr=trim($thtr); $thtr_lctn=trim($thtr_lctn); $thtr_lctn_dsply=' ('.$thtr_lctn.')';}
      else {$thtr_lctn=''; $thtr_lctn_dsply='';}

      if(preg_match('/\S+.*;;.*\S+/', $thtr)) {list($thtr, $sbthtr_nm)=explode(';;', $thtr); $thtr=trim($thtr); $sbthtr_nm=trim($sbthtr_nm); $sbthtr_nm_dsply=': '.$sbthtr_nm;}
      else {$sbthtr_nm=''; $sbthtr_nm_dsply='';}

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

      $sql="UPDATE prd SET thtrid=(SELECT thtr_id FROM thtr WHERE thtr_url='$thtr_url') WHERE prd_id='$prd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error updating production-theatre association data for submitted production: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $tr_lg_list))
      {
        $tr_lg_ids=explode(',,', $tr_lg_list); $n=0;
        foreach($tr_lg_ids as $tr_lg_id)
        {
          $tr_lg_id=trim($tr_lg_id); $tr_lg_ordr=++$n;
          $sql="UPDATE prd SET tr_ov='$prd_id', tr_lg_ordr='$tr_lg_ordr' WHERE prd_id='$tr_lg_id'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-tour leg association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $coll_sg_list))
      {
        $coll_sg_sbhdr_ids=explode('@@', $coll_sg_list); $m=0;
        foreach($coll_sg_sbhdr_ids as $coll_sg_sbhdr_id)
        {
          $coll_sg_sbhdr_id = trim($coll_sg_sbhdr_id); $coll_sbhdr_id=++$m;
          if(preg_match('/^\S+.*==.*\S$/', $coll_sg_sbhdr_id))
          {
            list($coll_sbhdr, $coll_sg_id_list)=explode('==', $coll_sg_sbhdr_id);
            $coll_sbhdr=trim($coll_sbhdr); $coll_sg_id_list=trim($coll_sg_id_list);

            $sql="INSERT INTO prdcoll_sbhdr(coll_ov, coll_sbhdr_id, coll_sbhdr) VALUES('$prd_id', '$coll_sbhdr_id', '$coll_sbhdr')";
            if(!mysqli_query($link, $sql)) {$error='Error adding production-collection subheader data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
          else {$coll_sg_id_list=$coll_sg_sbhdr_id; $coll_sbhdr_id=NULL;}

          $coll_sg_ids=explode(',,', $coll_sg_id_list); $n=0;
          foreach($coll_sg_ids as $coll_sg_id)
          {
            $coll_sg_id=trim($coll_sg_id); $coll_ordr=++$n;
            $sql= "UPDATE prd SET coll_ov='$prd_id', coll_ordr='$coll_ordr', coll_sbhdrid=CASE WHEN '$coll_sbhdr_id'!='' THEN '$coll_sbhdr_id' END
                  WHERE prd_id='$coll_sg_id'";
            if(!mysqli_query($link, $sql)) {$error='Error updating production-collection segment association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      if(preg_match('/\S+/', $rep_list))
      {
        $rep_ids=explode(',,', $rep_list);
        foreach($rep_ids as $rep_id)
        {
          $rep_id=trim($rep_id);
          $sql="INSERT INTO prdrep(rep1, rep2) VALUES('$prd_id', '$rep_id')";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-rep association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $prdrn_list))
      {
        $prdrn_ids=explode(',,', $prdrn_list);
        foreach($prdrn_ids as $prdrn_id)
        {
          $prdrn_id=trim($prdrn_id);
          $sql="INSERT INTO prdrn(prdrn1, prdrn2) VALUES('$prd_id', '$prdrn_id')";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-production run association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $prd_vrsn_list))
      {
        $prd_vrsn_nms=explode(',,', $prd_vrsn_list);
        $n=0;
        foreach($prd_vrsn_nms as $prd_vrsn_nm)
        {
          $prd_vrsn_nm=trim($prd_vrsn_nm);
          $prd_vrsn_url=generateurl($prd_vrsn_nm);
          $prd_vrsn_ordr=++$n;

          $sql="SELECT 1 FROM prd_vrsn WHERE prd_vrsn_url='$prd_vrsn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of prod version: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql="INSERT INTO prd_vrsn(prd_vrsn_nm, prd_vrsn_url) VALUES('$prd_vrsn_nm', '$prd_vrsn_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding prod version data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql="INSERT INTO prdprd_vrsn(prdid, prd_vrsn_ordr, prd_vrsnid)
              SELECT '$prd_id', '$prd_vrsn_ordr', prd_vrsn_id FROM prd_vrsn WHERE prd_vrsn_url='$prd_vrsn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-prod version association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

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

          $sql="INSERT INTO prdtxt_vrsn(prdid, txt_vrsn_ordr, txt_vrsnid)
              SELECT '$prd_id', '$txt_vrsn_ordr', txt_vrsn_id FROM txt_vrsn WHERE txt_vrsn_url='$txt_vrsn_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-text version association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

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
            $sql="INSERT INTO ctgry(ctgry_nm, ctgry_url) VALUES('$ctgry_nm', '$ctgry_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO prdctgry(prdid, ctgry_ordr, ctgryid)
                SELECT '$prd_id', '$ctgry_ordr', ctgry_id FROM ctgry WHERE ctgry_url='$ctgry_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-category association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

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
            $sql="INSERT INTO gnr(gnr_nm, gnr_url) VALUES('$gnr_nm', '$gnr_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding genre data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO prdgnr(prdid, gnr_ordr, gnrid)
                SELECT '$prd_id', '$gnr_ordr', gnr_id FROM gnr WHERE gnr_url='$gnr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-genre association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

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

          $sql= "INSERT INTO prdftr(prdid, ftr_ordr, ftrid)
                SELECT '$prd_id', '$ftr_ordr', ftr_id FROM ftr WHERE ftr_url='$ftr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-feature association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

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

          $sql= "INSERT INTO prdthm(prdid, thm_ordr, thmid)
                SELECT '$prd_id', '$thm_ordr', thm_id FROM thm WHERE thm_url='$thm_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-theme association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

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

              $sql= "INSERT INTO prdsttng_plc(prdid, sttngid, sttng_plc_ordr, sttng_plc_nt1, sttng_plc_nt2, sttng_plcid)
                    SELECT '$prd_id', '$sttng_id', '$sttng_plc_ordr', '$sttng_plc_nt1', '$sttng_plc_nt2', plc_id
                    FROM plc WHERE plc_url='$sttng_plc_url'";
              if(!mysqli_query($link, $sql)) {$error='Error adding production-setting (place) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

              $sql= "INSERT INTO prdsttng_lctn(prdid, sttngid, sttng_lctn_ordr, sttng_lctn_nt1, sttng_lctn_nt2, sttng_lctnid)
                    SELECT '$prd_id', '$sttng_id', '$sttng_lctn_ordr', '$sttng_lctn_nt1', '$sttng_lctn_nt2', lctn_id
                    FROM lctn WHERE lctn_url='$sttng_lctn_url'";
              if(!mysqli_query($link, $sql)) {$error='Error adding production-setting (location) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

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

                  $sql= "INSERT INTO prdsttng_lctn_alt(prdid, sttngid, sttng_lctnid, sttng_lctn_altid)
                        SELECT '$prd_id', '$sttng_id',
                        (SELECT lctn_id FROM lctn WHERE lctn_url='$sttng_lctn_url'),
                        (SELECT lctn_id FROM lctn WHERE lctn_url='$sttng_lctn_alt_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding production-setting (alternate location) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
              $sql= "INSERT INTO prdsttng(prdid, sttng_id, tm_spn)
                    SELECT '$prd_id', '$sttng_id', '1'";
              if(!mysqli_query($link, $sql)) {$error='Error adding production-setting (time) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

              $sql= "INSERT INTO prdsttng_tm(prdid, sttngid, sttng_tm_ordr, sttng_tm_nt1, sttng_tm_nt2, sttng_tmid)
                    SELECT '$prd_id', '$sttng_id', '$sttng_tm_ordr', '$sttng_tm_nt1', '$sttng_tm_nt2', tm_id
                    FROM tm WHERE tm_url='$sttng_tm_url'";
              if(!mysqli_query($link, $sql)) {$error='Error adding production-setting (time) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }
          }
        }
      }

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
                      SELECT '$src_mat_nm', CASE WHEN '$src_mat_alph'!='' THEN '$src_mat_alph' END, '$src_mat_sffx_num', '$src_mat_url', frmt_id
                      FROM frmt WHERE frmt_url='$src_frmt_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding source material data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              $sql= "INSERT INTO prdsrc_mat(prdid, wri_rlid, src_mat_ordr, src_matid)
                    SELECT $prd_id, $wri_rl_id, '$src_mat_ordr', mat_id
                    FROM mat WHERE mat_url='$src_mat_url' AND frmtid=(SELECT frmt_id FROM frmt WHERE frmt_url='$src_frmt_url')";
              if(!mysqli_query($link, $sql)) {$error='Error adding production-source material association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

                $sql= "INSERT INTO prdwri(prdid, wri_rlid, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, wri_prsnid, wri_compid)
                      SELECT $prd_id, $wri_rl_id, '$wri_comp_rl', $wri_ordr, $org_wri, $src_wri, $grntr, '0', comp_id
                      FROM comp WHERE comp_url='$wri_comp_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-writer (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

                  $sql= "INSERT INTO prdwri(prdid, wri_rlid, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, wri_compid, wri_prsnid)
                        SELECT $prd_id, $wri_rl_id, '$wri_prsn_rl', $wri_ordr, $org_wri, $src_wri, $grntr,
                        (SELECT comp_id FROM comp WHERE comp_url='$wri_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$wri_prsn_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding production-writer (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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

                $sql= "INSERT INTO prdwri(prdid, wri_rlid, wri_sb_rl, wri_ordr, org_wri, src_wri, grntr, wri_compid, wri_prsnid)
                      SELECT $prd_id, $wri_rl_id, '$wri_prsn_rl', $wri_ordr, $org_wri, $src_wri, $grntr, '0', prsn_id
                      FROM prsn WHERE prsn_url='$wri_prsn_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-writer (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
          }
          else {$wri_rl='';}

          $sql= "INSERT INTO prdwrirl(prdid, wri_rl_id, wri_rl, src_mat_rl)
                VALUES('$prd_id', '$wri_rl_id', '$wri_rl', '$src_mat_rl')";
          if(!mysqli_query($link, $sql)) {$error='Error adding writer-role association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $prdcr_list))
      {
        $prdcr_comp_prsn_rls=explode(',,', $prdcr_list);
        $m=0; $n=0;
        foreach($prdcr_comp_prsn_rls as $prdcr_comp_prsn_rl)
        {
          $prdcr_rl_id=++$m;
          if(preg_match('/\S+/', $prdcr_comp_prsn_rl))
          {
            list($prdcr_rl, $prdcr_comp_prsn_list)=explode('::', $prdcr_comp_prsn_rl);
            $prdcr_rl=trim($prdcr_rl); $prdcr_comp_prsn_list=trim($prdcr_comp_prsn_list);

            $o=0;
            $prdcr_comps_ppl=explode('>>', $prdcr_comp_prsn_list);
            foreach($prdcr_comps_ppl as $prdcr_comp_prsn)
            {
              if(preg_match('/\|\|/', $prdcr_comp_prsn))
              {
                list($prdcr_comp_nm, $prdcr_prsn_nm_list)=explode('||', $prdcr_comp_prsn);
                $prdcr_comp_nm=trim($prdcr_comp_nm); $prdcr_prsn_nm_list=trim($prdcr_prsn_nm_list); $prdcr_prsn_nm2='';

                if(preg_match('/^\S+.*\*\*\*$/', $prdcr_comp_nm))
                {$prdcr_comp_nm=preg_replace('/(\S+.*)(\*\*\*)/', '$1', $prdcr_comp_nm); $grntr='1'; $wri_comp_nm=trim($wri_comp_nm);}
                else {$grntr='0';}
              }
              else
              {
                if(preg_match('/^\S+.*\*\*\*$/', $prdcr_comp_prsn))
                {$prdcr_comp_prsn=preg_replace('/(\S+.*)(\*\*\*)/', '$1', $prdcr_comp_prsn); $grntr='1'; $prdcr_comp_prsn=trim($prdcr_comp_prsn);}
                else {$grntr='0';}

                $prdcr_comp_nm=''; $prdcr_prsn_nm_list=''; $prdcr_prsn_nm2=trim($prdcr_comp_prsn);
              }

              if(preg_match('/\S+/', $prdcr_comp_nm))
              {
                if(preg_match('/\S+.*~~.*\S+/', $prdcr_comp_nm))
                {
                  list($prdcr_comp_sb_rl, $prdcr_comp_nm)=explode('~~', $prdcr_comp_nm);
                  $prdcr_comp_sb_rl=trim($prdcr_comp_sb_rl); $prdcr_comp_nm=trim($prdcr_comp_nm);
                }
                else {$prdcr_comp_sb_rl='';}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prdcr_comp_nm))
                {
                  list($prdcr_comp_nm, $prdcr_comp_sffx_num)=explode('--', $prdcr_comp_nm);
                  $prdcr_comp_nm=trim($prdcr_comp_nm); $prdcr_comp_sffx_num=trim($prdcr_comp_sffx_num);
                  $prdcr_comp_sffx_rmn=' ('.romannumeral($prdcr_comp_sffx_num).')';
                }
                else
                {$prdcr_comp_sffx_num='0'; $prdcr_comp_sffx_rmn='';}

                $prdcr_ordr=++$o;
                $prdcr_comp_url=generateurl($prdcr_comp_nm.$prdcr_comp_sffx_rmn);
                $prdcr_comp_alph=alph($prdcr_comp_nm);

                $sql="SELECT 1 FROM comp WHERE comp_url='$prdcr_comp_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of producer (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                        VALUES('$prdcr_comp_nm', CASE WHEN '$prdcr_comp_alph'!='' THEN '$prdcr_comp_alph' END, '$prdcr_comp_sffx_num', '$prdcr_comp_url', 1, 0, 0)";
                  if(!mysqli_query($link, $sql)) {$error='Error adding producer (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO prdprdcr(prdid, prdcr_rlid, prdcr_comp_rlid, prdcr_sb_rl, prdcr_ordr, grntr, prdcr_crdt, prdcr_prsnid, prdcr_compid)
                      SELECT $prd_id, $prdcr_rl_id, '0', '$prdcr_comp_sb_rl', $prdcr_ordr, $grntr, '0', '0', comp_id
                      FROM comp WHERE comp_url='$prdcr_comp_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-producer (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              if(preg_match('/\S+/', $prdcr_prsn_nm_list))
              {
                $prdcr_prsn_nms=explode('//', $prdcr_prsn_nm_list);
                foreach($prdcr_prsn_nms as $prdcr_prsn_nm)
                {
                  $prdcr_comp_rl_id=++$n;
                  $prdcr_prsn_nm=trim($prdcr_prsn_nm);

                  list($prdcr_compprsn_rl, $prdcr_prsn_nm)=explode('~~', $prdcr_prsn_nm);
                  $prdcr_compprsn_rl=trim($prdcr_compprsn_rl); $prdcr_prsn_nm=trim($prdcr_prsn_nm);

                  $prdcr_prsn_nms=explode('¬¬', $prdcr_prsn_nm);
                  foreach($prdcr_prsn_nms as $prdcr_prsn_nm)
                  {
                    $prdcr_prsn_nm=trim($prdcr_prsn_nm);
                    if(preg_match('/\S+.*\^\^.*\S+/', $prdcr_prsn_nm))
                    {
                      list($prdcr_compprsn_sb_rl, $prdcr_prsn_nm)=explode('^^', $prdcr_prsn_nm);
                      $prdcr_compprsn_sb_rl=trim($prdcr_compprsn_sb_rl); $prdcr_prsn_nm=trim($prdcr_prsn_nm);
                    }
                    else {$prdcr_compprsn_sb_rl='';}

                    if(preg_match('/^\S+.*\*$/', $prdcr_prsn_nm))
                    {$prdcr_prsn_nm=preg_replace('/(\S+.*)(\*)/', '$1', $prdcr_prsn_nm); $prdcr_crdt='1'; $prdcr_prsn_nm=trim($prdcr_prsn_nm);}
                    else
                    {$prdcr_crdt='0';}

                    if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prdcr_prsn_nm))
                    {
                      list($prdcr_prsn_nm, $prdcr_prsn_sffx_num)=explode('--', $prdcr_prsn_nm);
                      $prdcr_prsn_nm=trim($prdcr_prsn_nm); $prdcr_prsn_sffx_num=trim($prdcr_prsn_sffx_num);
                      $prdcr_prsn_sffx_rmn=' ('.romannumeral($prdcr_prsn_sffx_num).')';
                    }
                    else
                    {$prdcr_prsn_sffx_num='0'; $prdcr_prsn_sffx_rmn='';}

                    list($prdcr_prsn_frst_nm, $prdcr_prsn_lst_nm)=explode(';;', $prdcr_prsn_nm);
                    $prdcr_prsn_frst_nm=trim($prdcr_prsn_frst_nm); $prdcr_prsn_lst_nm=trim($prdcr_prsn_lst_nm);

                    if(preg_match('/\S+/', $prdcr_prsn_lst_nm)) {$prdcr_prsn_lst_nm_dsply=' '.$prdcr_prsn_lst_nm;}
                    else {$prdcr_prsn_lst_nm_dsply='';}

                    $prdcr_prsn_fll_nm=$prdcr_prsn_frst_nm.$prdcr_prsn_lst_nm_dsply;
                    $prdcr_prsn_url=generateurl($prdcr_prsn_fll_nm.$prdcr_prsn_sffx_rmn);
                    $prdcr_ordr=++$o;

                    $sql="SELECT 1 FROM prsn WHERE prsn_url='$prdcr_prsn_url'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existence of producer (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)==0)
                    {
                      $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                            VALUES('$prdcr_prsn_fll_nm', '$prdcr_prsn_frst_nm', '$prdcr_prsn_lst_nm', '$prdcr_prsn_sffx_num', '$prdcr_prsn_url', '0')";
                      if(!mysqli_query($link, $sql)) {$error='Error adding producer (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    }

                    $sql= "INSERT INTO prdprdcr(prdid, prdcr_rlid, prdcr_comp_rlid, prdcr_sb_rl, prdcr_ordr, grntr, prdcr_crdt, prdcr_compid, prdcr_prsnid)
                          SELECT $prd_id, $prdcr_rl_id, $prdcr_comp_rl_id, '$prdcr_compprsn_sb_rl', $prdcr_ordr, $grntr, $prdcr_crdt,
                          (SELECT comp_id FROM comp WHERE comp_url='$prdcr_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$prdcr_prsn_url')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding production-producer (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql="INSERT INTO prdprdcr_comprl(prdid, prdcr_comp_rl_id, prdcr_comprl)
                      SELECT $prd_id, $prdcr_comp_rl_id, '$prdcr_compprsn_rl'";
                  if(!mysqli_query($link, $sql)) {$error='Error adding production-producer (person - company member) association data (role within company only): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }

              if(preg_match('/\S+/', $prdcr_prsn_nm2))
              {
                if(preg_match('/\S+.*~~.*\S+/', $prdcr_prsn_nm2))
                {
                  list($prdcr_prsn_sb_rl, $prdcr_prsn_nm)=explode('~~', $prdcr_prsn_nm2);
                  $prdcr_prsn_sb_rl=trim($prdcr_prsn_sb_rl); $prdcr_prsn_nm=trim($prdcr_prsn_nm);
                }
                else {$prdcr_prsn_sb_rl=''; $prdcr_prsn_nm=$prdcr_prsn_nm2;}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prdcr_prsn_nm))
                {
                  list($prdcr_prsn_nm, $prdcr_prsn_sffx_num)=explode('--', $prdcr_prsn_nm);
                  $prdcr_prsn_nm=trim($prdcr_prsn_nm); $prdcr_prsn_sffx_num=trim($prdcr_prsn_sffx_num);
                  $prdcr_prsn_sffx_rmn=' ('.romannumeral($prdcr_prsn_sffx_num).')';
                }
                else
                {$prdcr_prsn_sffx_num='0'; $prdcr_prsn_sffx_rmn='';}

                list($prdcr_prsn_frst_nm, $prdcr_prsn_lst_nm)=explode(';;', $prdcr_prsn_nm);
                $prdcr_prsn_frst_nm=trim($prdcr_prsn_frst_nm); $prdcr_prsn_lst_nm=trim($prdcr_prsn_lst_nm);

                if(preg_match('/\S+/', $prdcr_prsn_lst_nm)) {$prdcr_prsn_lst_nm_dsply=' '.$prdcr_prsn_lst_nm;}
                else {$prdcr_prsn_lst_nm_dsply='';}

                $prdcr_prsn_fll_nm=$prdcr_prsn_frst_nm.$prdcr_prsn_lst_nm_dsply;
                $prdcr_prsn_url=generateurl($prdcr_prsn_fll_nm.$prdcr_prsn_sffx_rmn);
                $prdcr_ordr=++$o;

                $sql="SELECT 1 FROM prsn WHERE prsn_url='$prdcr_prsn_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of producer (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                        VALUES('$prdcr_prsn_fll_nm', '$prdcr_prsn_frst_nm', '$prdcr_prsn_lst_nm', '$prdcr_prsn_sffx_num', '$prdcr_prsn_url', '0')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding producer (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO prdprdcr(prdid, prdcr_rlid, prdcr_comp_rlid, prdcr_sb_rl, prdcr_ordr, grntr, prdcr_crdt, prdcr_compid, prdcr_prsnid)
                      SELECT $prd_id, $prdcr_rl_id, '0', '$prdcr_prsn_sb_rl', $prdcr_ordr, $grntr, '0', '0', prsn_id
                      FROM prsn WHERE prsn_url='$prdcr_prsn_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-producer (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
          }
          else {$prdcr_rl='';}

          $sql= "INSERT INTO prdprdcrrl(prdid, prdcr_rl_id, prdcr_rl)
                VALUES('$prd_id', '$prdcr_rl_id', '$prdcr_rl')";
          if(!mysqli_query($link, $sql)) {$error='Error adding producer-role association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $prf_list))
      {
        $prf_prsn_nm_rls=explode(',,', $prf_list);
        $n=0;
        foreach($prf_prsn_nm_rls as $prf_prsn_nm_rl)
        {
          list($prf_prsn_nm, $prf_prsn_rl)=explode('::', $prf_prsn_nm_rl);
          $prf_prsn_nm=trim($prf_prsn_nm);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prf_prsn_nm))
          {
            list($prf_prsn_nm, $prf_prsn_sffx_num)=explode('--', $prf_prsn_nm);
            $prf_prsn_nm=trim($prf_prsn_nm); $prf_prsn_sffx_num=trim($prf_prsn_sffx_num);
            $prf_prsn_sffx_rmn=' ('.romannumeral($prf_prsn_sffx_num).')';
          }
          else {$prf_prsn_sffx_num='0'; $prf_prsn_sffx_rmn='';}

          list($prf_prsn_frst_nm, $prf_prsn_lst_nm)=explode(';;', $prf_prsn_nm);
          $prf_prsn_frst_nm=trim($prf_prsn_frst_nm); $prf_prsn_lst_nm=trim($prf_prsn_lst_nm);

          if(preg_match('/\S+/', $prf_prsn_lst_nm)) {$prf_prsn_lst_nm_dsply=' '.$prf_prsn_lst_nm;}
          else {$prf_prsn_lst_nm_dsply='';}

          $prf_prsn_fll_nm=$prf_prsn_frst_nm.$prf_prsn_lst_nm_dsply;
          $prf_prsn_url=generateurl($prf_prsn_fll_nm.$prf_prsn_sffx_rmn);
          $prf_prsn_ordr=++$n;

          $sql="SELECT 1 FROM prsn WHERE prsn_url='$prf_prsn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of performer (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO prsn(prsn_url, prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, comp_bool)
                  VALUES('$prf_prsn_url', '$prf_prsn_fll_nm', '$prf_prsn_frst_nm', '$prf_prsn_lst_nm', '$prf_prsn_sffx_num', '0')";
            if(!mysqli_query($link, $sql)) {$error='Error adding performer (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $prf_prsn_rls=explode('//', $prf_prsn_rl);
          $m=0;
          foreach($prf_prsn_rls as $prf_prsn_rl)
          {
            $prf_prsn_rl_ordr=++$m;
            $prf_prsn_rl=trim($prf_prsn_rl);

            if(preg_match('/^\S+.*\*$/', $prf_prsn_rl)) {$prf_prsn_rl=preg_replace('/(\S+.*)(\*)/', '$1', $prf_prsn_rl); $prf_prsn_rl_alt='1'; $prf_prsn_rl=trim($prf_prsn_rl);}
            else {$prf_prsn_rl_alt='0';}

            if(preg_match('/^\S+.*;;.*\S+$/', $prf_prsn_rl)) {list($prf_prsn_rl, $prf_prsn_rl_dscr)=explode(';;', $prf_prsn_rl); $prf_prsn_rl=trim($prf_prsn_rl); $prf_prsn_rl_dscr=trim($prf_prsn_rl_dscr);}
            else {$prf_prsn_rl_dscr=NULL;}

            if(preg_match('/^\S+.*\|\|.*\S+$/', $prf_prsn_rl)) {list($prf_prsn_rl, $prf_prsn_rl_lnk)=explode('||', $prf_prsn_rl); $prf_prsn_rl=trim($prf_prsn_rl); $prf_prsn_rl_lnk=trim($prf_prsn_rl_lnk);}
            else {$prf_prsn_rl_lnk=$prf_prsn_rl;}

            $prf_prsn_rl_alph=alph($prf_prsn_rl);

            $sql= "INSERT INTO prdprf(prdid, prf_ordr, prf_rl, prf_rl_alph, prf_rl_lnk, prf_rl_dscr, prf_rl_alt, prf_rl_ordr, prf_prsnid)
                  SELECT $prd_id, $prf_prsn_ordr, '$prf_prsn_rl', CASE WHEN '$prf_prsn_rl_alph'!='' THEN '$prf_prsn_rl_alph' END, '$prf_prsn_rl_lnk', '$prf_prsn_rl_dscr', '$prf_prsn_rl_alt', '$prf_prsn_rl_ordr', prsn_id
                  FROM prsn WHERE prsn_url='$prf_prsn_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding production-performer (person) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      if(preg_match('/\S+/', $us_list))
      {
        $us_prsn_nm_rls=explode(',,', $us_list);
        $n=0;
        foreach($us_prsn_nm_rls as $us_prsn_nm_rl)
        {
          list($us_prsn_nm, $us_prsn_rl)=explode('::', $us_prsn_nm_rl);
          $us_prsn_nm=trim($us_prsn_nm); $us_prsn_rl=trim($us_prsn_rl);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $us_prsn_nm))
          {
            list($us_prsn_nm, $us_prsn_sffx_num)=explode('--', $us_prsn_nm);
            $us_prsn_nm=trim($us_prsn_nm); $us_prsn_sffx_num=trim($us_prsn_sffx_num);
            $us_prsn_sffx_rmn=' ('.romannumeral($us_prsn_sffx_num).')';
          }
          else {$us_prsn_sffx_num='0'; $us_prsn_sffx_rmn='';}

          list($us_prsn_frst_nm, $us_prsn_lst_nm)=explode(';;', $us_prsn_nm);
          $us_prsn_frst_nm=trim($us_prsn_frst_nm); $us_prsn_lst_nm=trim($us_prsn_lst_nm);

          if(preg_match('/\S+/', $us_prsn_lst_nm)) {$us_prsn_lst_nm_dsply=' '.$us_prsn_lst_nm;}
          else {$us_prsn_lst_nm_dsply='';}

          $us_prsn_fll_nm=$us_prsn_frst_nm.$us_prsn_lst_nm_dsply;
          $us_prsn_url=generateurl($us_prsn_fll_nm.$us_prsn_sffx_rmn);
          $us_prsn_ordr=++$n;

          $sql="SELECT 1 FROM prsn WHERE prsn_url='$us_prsn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of understudy (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO prsn(prsn_url, prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, comp_bool)
                  VALUES('$us_prsn_url', '$us_prsn_fll_nm', '$us_prsn_frst_nm', '$us_prsn_lst_nm', '$us_prsn_sffx_num', '0')";
            if(!mysqli_query($link, $sql)) {$error='Error adding understudy (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $us_prsn_rls=explode('//', $us_prsn_rl);
          $m=0;
          foreach($us_prsn_rls as $us_prsn_rl)
          {
            $us_prsn_rl_ordr=++$m;
            $us_prsn_rl=trim($us_prsn_rl);

            if(preg_match('/^\S+.*\*$/', $us_prsn_rl)) {$us_prsn_rl=preg_replace('/(\S+.*)(\*)/', '$1', $us_prsn_rl); $us_prsn_rl_alt='1'; $us_prsn_rl=trim($us_prsn_rl);}
            else {$us_prsn_rl_alt='0';}

            if(preg_match('/^\S+.*;;.*\S+$/', $us_prsn_rl)) {list($us_prsn_rl, $us_prsn_rl_dscr)=explode(';;', $us_prsn_rl); $us_prsn_rl=trim($us_prsn_rl); $us_prsn_rl_dscr=trim($us_prsn_rl_dscr);}
            else {$us_prsn_rl_dscr=NULL;}

            if(preg_match('/^\S+.*\|\|.*\S+$/', $us_prsn_rl)) {list($us_prsn_rl, $us_prsn_rl_lnk)=explode('||', $us_prsn_rl); $us_prsn_rl=trim($us_prsn_rl); $us_prsn_rl_lnk=trim($us_prsn_rl_lnk);}
            else {$us_prsn_rl_lnk=$us_prsn_rl;}

            $us_prsn_rl_alph=alph($us_prsn_rl);

            $sql= "INSERT INTO prdus(prdid, us_ordr, us_rl, us_rl_alph, us_rl_lnk, us_rl_dscr, us_rl_alt, us_rl_ordr, us_prsnid)
                  SELECT $prd_id, $us_prsn_ordr, '$us_prsn_rl', CASE WHEN '$us_prsn_rl_alph'!='' THEN '$us_prsn_rl_alph' END, '$us_prsn_rl_lnk', '$us_prsn_rl_dscr', '$us_prsn_rl_alt', '$us_prsn_rl_ordr', prsn_id
                  FROM prsn WHERE prsn_url='$us_prsn_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding production-understudy (person) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      if(preg_match('/\S+/', $mscn_list))
      {
        $mscn_comp_prsn_rls=explode(',,', $mscn_list);
        $m=0; $n=0;
        foreach($mscn_comp_prsn_rls as $mscn_comp_prsn_rl)
        {
          $mscn_rl_id=++$m;
          if(preg_match('/\S+/', $mscn_comp_prsn_rl))
          {
            list($mscn_rl, $mscn_comp_prsn_list)=explode('::', $mscn_comp_prsn_rl);
            $mscn_rl=trim($mscn_rl); $mscn_comp_prsn_list=trim($mscn_comp_prsn_list);

            $o=0;
            $mscn_comps_ppl=explode('>>', $mscn_comp_prsn_list);
            foreach($mscn_comps_ppl as $mscn_comp_prsn)
            {
              if(preg_match('/\|\|/', $mscn_comp_prsn))
              {
                list($mscn_comp_nm, $mscn_prsn_nm_list)=explode('||', $mscn_comp_prsn);
                $mscn_comp_nm=trim($mscn_comp_nm); $mscn_prsn_nm_list=trim($mscn_prsn_nm_list); $mscn_prsn_nm2='';
              }
              else
              {
                $mscn_comp_nm=''; $mscn_prsn_nm_list=''; $mscn_prsn_nm2=trim($mscn_comp_prsn);
              }

              if(preg_match('/\S+/', $mscn_comp_nm))
              {
                if(preg_match('/\S+.*~~.*\S+/', $mscn_comp_nm))
                {
                  list($mscn_comp_sb_rl, $mscn_comp_nm)=explode('~~', $mscn_comp_nm);
                  $mscn_comp_sb_rl=trim($mscn_comp_sb_rl); $mscn_comp_nm=trim($mscn_comp_nm);
                }
                else {$mscn_comp_sb_rl='';}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $mscn_comp_nm))
                {
                  list($mscn_comp_nm, $mscn_comp_sffx_num)=explode('--', $mscn_comp_nm);
                  $mscn_comp_nm=trim($mscn_comp_nm); $mscn_comp_sffx_num=trim($mscn_comp_sffx_num);
                  $mscn_comp_sffx_rmn=' ('.romannumeral($mscn_comp_sffx_num).')';
                }
                else
                {$mscn_comp_sffx_num='0'; $mscn_comp_sffx_rmn='';}

                $mscn_ordr=++$o;
                $mscn_comp_url=generateurl($mscn_comp_nm.$mscn_comp_sffx_rmn);
                $mscn_comp_alph=alph($mscn_comp_nm);

                $sql="SELECT 1 FROM comp WHERE comp_url='$mscn_comp_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of musician (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                        VALUES('$mscn_comp_nm', CASE WHEN '$mscn_comp_alph'!='' THEN '$mscn_comp_alph' END, '$mscn_comp_sffx_num', '$mscn_comp_url', 1, 0, 0)";
                  if(!mysqli_query($link, $sql)) {$error='Error adding musician (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO prdmscn(prdid, mscn_rlid, mscn_comp_rlid, mscn_sb_rl, mscn_ordr, mscn_prsnid, mscn_compid)
                      SELECT $prd_id, $mscn_rl_id, '0', '$mscn_comp_sb_rl', $mscn_ordr, '0', comp_id
                      FROM comp WHERE comp_url='$mscn_comp_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-musician (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              if(preg_match('/\S+/', $mscn_prsn_nm_list))
              {
                $mscn_prsn_nms=explode('//', $mscn_prsn_nm_list);
                foreach($mscn_prsn_nms as $mscn_prsn_nm)
                {
                  $mscn_comp_rl_id=++$n;
                  $mscn_prsn_nm=trim($mscn_prsn_nm);

                  list($mscn_compprsn_rl, $mscn_prsn_nm)=explode('~~', $mscn_prsn_nm);
                  $mscn_compprsn_rl=trim($mscn_compprsn_rl); $mscn_prsn_nm=trim($mscn_prsn_nm);

                  $mscn_prsn_nms=explode('¬¬', $mscn_prsn_nm);
                  foreach($mscn_prsn_nms as $mscn_prsn_nm)
                  {
                    $mscn_prsn_nm=trim($mscn_prsn_nm);
                    if(preg_match('/\S+.*\^\^.*\S+/', $mscn_prsn_nm))
                    {
                      list($mscn_compprsn_sb_rl, $mscn_prsn_nm)=explode('^^', $mscn_prsn_nm);
                      $mscn_compprsn_sb_rl=trim($mscn_compprsn_sb_rl); $mscn_prsn_nm=trim($mscn_prsn_nm);
                    }
                    else {$mscn_compprsn_sb_rl='';}

                    if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $mscn_prsn_nm))
                    {
                      list($mscn_prsn_nm, $mscn_prsn_sffx_num)=explode('--', $mscn_prsn_nm);
                      $mscn_prsn_nm=trim($mscn_prsn_nm); $mscn_prsn_sffx_num=trim($mscn_prsn_sffx_num);
                      $mscn_prsn_sffx_rmn=' ('.romannumeral($mscn_prsn_sffx_num).')';
                    }
                    else
                    {$mscn_prsn_sffx_num='0'; $mscn_prsn_sffx_rmn='';}

                    list($mscn_prsn_frst_nm, $mscn_prsn_lst_nm)=explode(';;', $mscn_prsn_nm);
                    $mscn_prsn_frst_nm=trim($mscn_prsn_frst_nm); $mscn_prsn_lst_nm=trim($mscn_prsn_lst_nm);

                    if(preg_match('/\S+/', $mscn_prsn_lst_nm)) {$mscn_prsn_lst_nm_dsply=' '.$mscn_prsn_lst_nm;}
                    else {$mscn_prsn_lst_nm_dsply='';}

                    $mscn_prsn_fll_nm=$mscn_prsn_frst_nm.$mscn_prsn_lst_nm_dsply;
                    $mscn_prsn_url=generateurl($mscn_prsn_fll_nm.$mscn_prsn_sffx_rmn);
                    $mscn_ordr=++$o;

                    $sql="SELECT 1 FROM prsn WHERE prsn_url='$mscn_prsn_url'";
                    $result=mysqli_query($link, $sql);
                    if(!$result) {$error='Error checking for existence of musician (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    if(mysqli_num_rows($result)==0)
                    {
                      $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                            VALUES('$mscn_prsn_fll_nm', '$mscn_prsn_frst_nm', '$mscn_prsn_lst_nm', '$mscn_prsn_sffx_num', '$mscn_prsn_url', '0')";
                      if(!mysqli_query($link, $sql)) {$error='Error adding musician (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                    }

                    $sql= "INSERT INTO prdmscn(prdid, mscn_rlid, mscn_comp_rlid, mscn_sb_rl, mscn_ordr, mscn_compid, mscn_prsnid)
                          SELECT $prd_id, $mscn_rl_id, $mscn_comp_rl_id, '$mscn_compprsn_sb_rl', $mscn_ordr,
                          (SELECT comp_id FROM comp WHERE comp_url='$mscn_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$mscn_prsn_url')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding production-musician (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql= "INSERT INTO prdmscn_comprl(prdid, mscn_comp_rl_id, mscn_comprl)
                        SELECT $prd_id, $mscn_comp_rl_id, '$mscn_compprsn_rl'";
                  if(!mysqli_query($link, $sql)) {$error='Error adding production-musician (person - company member) association data (role within company only): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }

              if(preg_match('/\S+/', $mscn_prsn_nm2))
              {
                if(preg_match('/\S+.*~~.*\S+/', $mscn_prsn_nm2))
                {
                  list($mscn_prsn_sb_rl, $mscn_prsn_nm)=explode('~~', $mscn_prsn_nm2);
                  $mscn_prsn_sb_rl=trim($mscn_prsn_sb_rl); $mscn_prsn_nm=trim($mscn_prsn_nm);
                }
                else {$mscn_prsn_sb_rl=''; $mscn_prsn_nm=$mscn_prsn_nm2;}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $mscn_prsn_nm))
                {
                  list($mscn_prsn_nm, $mscn_prsn_sffx_num)=explode('--', $mscn_prsn_nm);
                  $mscn_prsn_nm=trim($mscn_prsn_nm); $mscn_prsn_sffx_num=trim($mscn_prsn_sffx_num);
                  $mscn_prsn_sffx_rmn=' ('.romannumeral($mscn_prsn_sffx_num).')';
                }
                else
                {$mscn_prsn_sffx_num='0'; $mscn_prsn_sffx_rmn='';}

                list($mscn_prsn_frst_nm, $mscn_prsn_lst_nm)=explode(';;', $mscn_prsn_nm);
                $mscn_prsn_frst_nm=trim($mscn_prsn_frst_nm); $mscn_prsn_lst_nm=trim($mscn_prsn_lst_nm);

                if(preg_match('/\S+/', $mscn_prsn_lst_nm)) {$mscn_prsn_lst_nm_dsply=' '.$mscn_prsn_lst_nm;}
                else {$mscn_prsn_lst_nm_dsply='';}

                $mscn_prsn_fll_nm=$mscn_prsn_frst_nm.$mscn_prsn_lst_nm_dsply;
                $mscn_prsn_url=generateurl($mscn_prsn_fll_nm.$mscn_prsn_sffx_rmn);
                $mscn_ordr=++$o;

                $sql="SELECT 1 FROM prsn WHERE prsn_url='$mscn_prsn_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of musician (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                        VALUES('$mscn_prsn_fll_nm', '$mscn_prsn_frst_nm', '$mscn_prsn_lst_nm', '$mscn_prsn_sffx_num', '$mscn_prsn_url', '0')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding musician (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO prdmscn(prdid, mscn_rlid, mscn_comp_rlid, mscn_sb_rl, mscn_ordr, mscn_compid, mscn_prsnid)
                      SELECT $prd_id, $mscn_rl_id, '0', '$mscn_prsn_sb_rl', $mscn_ordr, '0', prsn_id FROM prsn WHERE prsn_url='$mscn_prsn_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-musician (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
          }
          else {$mscn_rl='';}

          $sql= "INSERT INTO prdmscnrl(prdid, mscn_rl_id, mscn_rl) VALUES('$prd_id', '$mscn_rl_id', '$mscn_rl')";
          if(!mysqli_query($link, $sql)) {$error='Error adding musician-role association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $crtv_list))
      {
        $crtv_comp_prsn_rls=explode(',,', $crtv_list);
        $m=0;
        foreach($crtv_comp_prsn_rls as $crtv_comp_prsn_rl)
        {
          $crtv_rl_id=++$m;

          if(preg_match('/\S+/', $crtv_comp_prsn_rl))
          {
            list($crtv_rl, $crtv_comp_prsn_list)=explode('::', $crtv_comp_prsn_rl);
            $crtv_rl=trim($crtv_rl); $crtv_comp_prsn_list=trim($crtv_comp_prsn_list);

            $o=0;
            $crtv_comps_ppl=explode('>>', $crtv_comp_prsn_list);
            foreach($crtv_comps_ppl as $crtv_comp_prsn)
            {
              if(preg_match('/\|\|/', $crtv_comp_prsn))
              {
                list($crtv_comp_nm, $crtv_prsn_nm_list)=explode('||', $crtv_comp_prsn);
                $crtv_comp_nm=trim($crtv_comp_nm); $crtv_prsn_nm_list=trim($crtv_prsn_nm_list);
                $crtv_prsn_nm2='';
              }
              else
              {$crtv_comp_nm=''; $crtv_prsn_nm_list=''; $crtv_prsn_nm2=trim($crtv_comp_prsn);}

              if(preg_match('/\S+/', $crtv_comp_nm))
              {
                if(preg_match('/\S+.*~~.*\S+/', $crtv_comp_nm))
                {
                  list($crtv_comp_rl, $crtv_comp_nm)=explode('~~', $crtv_comp_nm);
                  $crtv_comp_rl=trim($crtv_comp_rl); $crtv_comp_nm=trim($crtv_comp_nm);
                }
                else {$crtv_comp_rl='';}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $crtv_comp_nm))
                {
                  list($crtv_comp_nm, $crtv_comp_sffx_num)=explode('--', $crtv_comp_nm);
                  $crtv_comp_nm=trim($crtv_comp_nm); $crtv_comp_sffx_num=trim($crtv_comp_sffx_num);
                  $crtv_comp_sffx_rmn=' ('.romannumeral($crtv_comp_sffx_num).')';
                }
                else
                {$crtv_comp_sffx_num='0'; $crtv_comp_sffx_rmn='';}

                $crtv_ordr=++$o;
                $crtv_comp_url=generateurl($crtv_comp_nm.$crtv_comp_sffx_rmn);
                $crtv_comp_alph=alph($crtv_comp_nm);

                $sql="SELECT 1 FROM comp WHERE comp_url='$crtv_comp_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of creative (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                        VALUES('$crtv_comp_nm', CASE WHEN '$crtv_comp_alph'!='' THEN '$crtv_comp_alph' END, '$crtv_comp_sffx_num', '$crtv_comp_url', 1, 0, 0)";
                  if(!mysqli_query($link, $sql)) {$error='Error adding creative (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO prdcrtv(prdid, crtv_rlid, crtv_sb_rl, crtv_ordr, crtv_prsnid, crtv_compid)
                      SELECT $prd_id, $crtv_rl_id, '$crtv_comp_rl', $crtv_ordr, '0', comp_id FROM comp WHERE comp_url='$crtv_comp_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-creative (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              if(preg_match('/\S+/', $crtv_prsn_nm_list))
              {
                $crtv_prsn_nms=explode('//', $crtv_prsn_nm_list);
                foreach($crtv_prsn_nms as $crtv_prsn_nm)
                {
                  $crtv_prsn_nm=trim($crtv_prsn_nm);
                  if(preg_match('/\S+.*~~.*\S+/', $crtv_prsn_nm))
                  {
                    list($crtv_prsn_rl, $crtv_prsn_nm)=explode('~~', $crtv_prsn_nm);
                    $crtv_prsn_rl=trim($crtv_prsn_rl); $crtv_prsn_nm=trim($crtv_prsn_nm);
                  }
                  else {$crtv_prsn_rl='';}

                  if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $crtv_prsn_nm))
                  {
                    list($crtv_prsn_nm, $crtv_prsn_sffx_num)=explode('--', $crtv_prsn_nm);
                    $crtv_prsn_nm=trim($crtv_prsn_nm); $crtv_prsn_sffx_num=trim($crtv_prsn_sffx_num);
                    $crtv_prsn_sffx_rmn=' ('.romannumeral($crtv_prsn_sffx_num).')';
                  }
                  else
                  {$crtv_prsn_sffx_num='0'; $crtv_prsn_sffx_rmn='';}

                  list($crtv_prsn_frst_nm, $crtv_prsn_lst_nm)=explode(';;', $crtv_prsn_nm);
                  $crtv_prsn_frst_nm=trim($crtv_prsn_frst_nm); $crtv_prsn_lst_nm=trim($crtv_prsn_lst_nm);

                  if(preg_match('/\S+/', $crtv_prsn_lst_nm)) {$crtv_prsn_lst_nm_dsply=' '.$crtv_prsn_lst_nm;}
                  else {$crtv_prsn_lst_nm_dsply='';}

                  $crtv_prsn_fll_nm=$crtv_prsn_frst_nm.$crtv_prsn_lst_nm_dsply;
                  $crtv_prsn_url=generateurl($crtv_prsn_fll_nm.$crtv_prsn_sffx_rmn);
                  $crtv_ordr=++$o;

                  $sql="SELECT 1 FROM prsn WHERE prsn_url='$crtv_prsn_url'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existence of creative (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  if(mysqli_num_rows($result)==0)
                  {
                    $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                          VALUES('$crtv_prsn_fll_nm', '$crtv_prsn_frst_nm', '$crtv_prsn_lst_nm', '$crtv_prsn_sffx_num', '$crtv_prsn_url', '0')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding creative (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql= "INSERT INTO prdcrtv(prdid, crtv_rlid, crtv_sb_rl, crtv_ordr, crtv_compid, crtv_prsnid)
                        SELECT $prd_id, $crtv_rl_id, '$crtv_prsn_rl', $crtv_ordr,
                        (SELECT comp_id FROM comp WHERE comp_url='$crtv_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$crtv_prsn_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding production-creative (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }

              if(preg_match('/\S+/', $crtv_prsn_nm2))
              {
                if(preg_match('/\S+.*~~.*\S+/', $crtv_prsn_nm2))
                {
                  list($crtv_prsn_rl, $crtv_prsn_nm)=explode('~~', $crtv_prsn_nm2);
                  $crtv_prsn_rl=trim($crtv_prsn_rl); $crtv_prsn_nm=trim($crtv_prsn_nm);
                }
                else {$crtv_prsn_rl=''; $crtv_prsn_nm=$crtv_prsn_nm2;}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $crtv_prsn_nm))
                {
                  list($crtv_prsn_nm, $crtv_prsn_sffx_num)=explode('--', $crtv_prsn_nm);
                  $crtv_prsn_nm=trim($crtv_prsn_nm); $crtv_prsn_sffx_num=trim($crtv_prsn_sffx_num);
                  $crtv_prsn_sffx_rmn=' ('.romannumeral($crtv_prsn_sffx_num).')';
                }
                else
                {$crtv_prsn_sffx_num='0'; $crtv_prsn_sffx_rmn='';}

                list($crtv_prsn_frst_nm, $crtv_prsn_lst_nm)=explode(';;', $crtv_prsn_nm);
                $crtv_prsn_frst_nm=trim($crtv_prsn_frst_nm); $crtv_prsn_lst_nm=trim($crtv_prsn_lst_nm);

                if(preg_match('/\S+/', $crtv_prsn_lst_nm)) {$crtv_prsn_lst_nm_dsply=' '.$crtv_prsn_lst_nm;}
                else {$crtv_prsn_lst_nm_dsply='';}

                $crtv_prsn_fll_nm=$crtv_prsn_frst_nm.$crtv_prsn_lst_nm_dsply;
                $crtv_prsn_url=generateurl($crtv_prsn_fll_nm.$crtv_prsn_sffx_rmn);
                $crtv_ordr=++$o;

                $sql="SELECT 1 FROM prsn WHERE prsn_url='$crtv_prsn_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of creative (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                        VALUES('$crtv_prsn_fll_nm', '$crtv_prsn_frst_nm', '$crtv_prsn_lst_nm', '$crtv_prsn_sffx_num', '$crtv_prsn_url', '0')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding creative (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO prdcrtv(prdid, crtv_rlid, crtv_sb_rl, crtv_ordr, crtv_compid, crtv_prsnid)
                      SELECT $prd_id, $crtv_rl_id, '$crtv_prsn_rl', $crtv_ordr, '0', prsn_id
                      FROM prsn WHERE prsn_url='$crtv_prsn_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-creative (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
          }
          else {$crtv_rl='';}

          $sql= "INSERT INTO prdcrtvrl(prdid, crtv_rl_id, crtv_rl)
                VALUES('$prd_id', '$crtv_rl_id', '$crtv_rl')";
          if(!mysqli_query($link, $sql)) {$error='Error adding creative-role association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $prdtm_list))
      {
        $prdtm_comp_prsn_rls=explode(',,', $prdtm_list);
        $m=0;
        foreach($prdtm_comp_prsn_rls as $prdtm_comp_prsn_rl)
        {
          $prdtm_rl_id=++$m;

          if(preg_match('/\S+/', $prdtm_comp_prsn_rl))
          {
            list($prdtm_rl, $prdtm_comp_prsn_list)=explode('::', $prdtm_comp_prsn_rl);
            $prdtm_rl=trim($prdtm_rl); $prdtm_comp_prsn_list=trim($prdtm_comp_prsn_list);

            $o=0;
            $prdtm_comps_ppl=explode('>>', $prdtm_comp_prsn_list);
            foreach($prdtm_comps_ppl as $prdtm_comp_prsn)
            {
              if(preg_match('/\|\|/', $prdtm_comp_prsn))
              {
                list($prdtm_comp_nm, $prdtm_prsn_nm_list)=explode('||', $prdtm_comp_prsn);
                $prdtm_comp_nm=trim($prdtm_comp_nm); $prdtm_prsn_nm_list=trim($prdtm_prsn_nm_list);
                $prdtm_prsn_nm2='';
              }
              else
              {$prdtm_comp_nm=''; $prdtm_prsn_nm_list=''; $prdtm_prsn_nm2=trim($prdtm_comp_prsn);}

              if(preg_match('/\S+/', $prdtm_comp_nm))
              {
                if(preg_match('/\S+.*~~.*\S+/', $prdtm_comp_nm))
                {
                  list($prdtm_comp_rl, $prdtm_comp_nm)=explode('~~', $prdtm_comp_nm);
                  $prdtm_comp_rl=trim($prdtm_comp_rl); $prdtm_comp_nm=trim($prdtm_comp_nm);
                }
                else {$prdtm_comp_rl='';}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prdtm_comp_nm))
                {
                  list($prdtm_comp_nm, $prdtm_comp_sffx_num)=explode('--', $prdtm_comp_nm);
                  $prdtm_comp_nm=trim($prdtm_comp_nm); $prdtm_comp_sffx_num=trim($prdtm_comp_sffx_num);
                  $prdtm_comp_sffx_rmn=' ('.romannumeral($prdtm_comp_sffx_num).')';
                }
                else
                {$prdtm_comp_sffx_num='0'; $prdtm_comp_sffx_rmn='';}

                $prdtm_ordr=++$o;
                $prdtm_comp_url=generateurl($prdtm_comp_nm.$prdtm_comp_sffx_rmn);
                $prdtm_comp_alph=alph($prdtm_comp_nm);

                $sql="SELECT 1 FROM comp WHERE comp_url='$prdtm_comp_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of production team (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                        VALUES('$prdtm_comp_nm', CASE WHEN '$prdtm_alph'!='' THEN '$prdtm_alph' END, '$prdtm_comp_sffx_num', '$prdtm_comp_url', 1, 0, 0)";
                  if(!mysqli_query($link, $sql)) {$error='Error adding production team (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO prdprdtm(prdid, prdtm_rlid, prdtm_sb_rl, prdtm_ordr, prdtm_prsnid, prdtm_compid)
                      SELECT $prd_id, $prdtm_rl_id, '$prdtm_comp_rl', $prdtm_ordr, '0', comp_id
                      FROM comp WHERE comp_url='$prdtm_comp_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-production team (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              if(preg_match('/\S+/', $prdtm_prsn_nm_list))
              {
                $prdtm_prsn_nms=explode('//', $prdtm_prsn_nm_list);
                foreach($prdtm_prsn_nms as $prdtm_prsn_nm)
                {
                  $prdtm_prsn_nm=trim($prdtm_prsn_nm);
                  if(preg_match('/\S+.*~~.*\S+/', $prdtm_prsn_nm))
                  {
                    list($prdtm_prsn_rl, $prdtm_prsn_nm)=explode('~~', $prdtm_prsn_nm);
                    $prdtm_prsn_rl=trim($prdtm_prsn_rl); $prdtm_prsn_nm=trim($prdtm_prsn_nm);
                  }
                  else {$prdtm_prsn_rl='';}

                  if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prdtm_prsn_nm))
                  {
                    list($prdtm_prsn_nm, $prdtm_prsn_sffx_num)=explode('--', $prdtm_prsn_nm);
                    $prdtm_prsn_nm=trim($prdtm_prsn_nm); $prdtm_prsn_sffx_num=trim($prdtm_prsn_sffx_num);
                    $prdtm_prsn_sffx_rmn=' ('.romannumeral($prdtm_prsn_sffx_num).')';
                  }
                  else
                  {$prdtm_prsn_sffx_num='0'; $prdtm_prsn_sffx_rmn='';}

                  list($prdtm_prsn_frst_nm, $prdtm_prsn_lst_nm)=explode(';;', $prdtm_prsn_nm);
                  $prdtm_prsn_frst_nm=trim($prdtm_prsn_frst_nm); $prdtm_prsn_lst_nm=trim($prdtm_prsn_lst_nm);

                  if(preg_match('/\S+/', $prdtm_prsn_lst_nm)) {$prdtm_prsn_lst_nm_dsply=' '.$prdtm_prsn_lst_nm;}
                  else {$prdtm_prsn_lst_nm_dsply='';}

                  $prdtm_prsn_fll_nm=$prdtm_prsn_frst_nm.$prdtm_prsn_lst_nm_dsply;
                  $prdtm_prsn_url=generateurl($prdtm_prsn_fll_nm.$prdtm_prsn_sffx_rmn);
                  $prdtm_ordr=++$o;

                  $sql="SELECT 1 FROM prsn WHERE prsn_url='$prdtm_prsn_url'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for existence of production team (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  if(mysqli_num_rows($result)==0)
                  {
                    $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                          VALUES('$prdtm_prsn_fll_nm', '$prdtm_prsn_frst_nm', '$prdtm_prsn_lst_nm', '$prdtm_prsn_sffx_num', '$prdtm_prsn_url', '0')";
                    if(!mysqli_query($link, $sql)) {$error='Error adding production team (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  }

                  $sql= "INSERT INTO prdprdtm(prdid, prdtm_rlid, prdtm_sb_rl, prdtm_ordr, prdtm_compid, prdtm_prsnid)
                        SELECT $prd_id, $prdtm_rl_id, '$prdtm_prsn_rl', $prdtm_ordr,
                        (SELECT comp_id FROM comp WHERE comp_url='$prdtm_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$prdtm_prsn_url')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding production-production team (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }
              }

              if(preg_match('/\S+/', $prdtm_prsn_nm2))
              {
                if(preg_match('/\S+.*~~.*\S+/', $prdtm_prsn_nm2))
                {
                  list($prdtm_prsn_rl, $prdtm_prsn_nm)=explode('~~', $prdtm_prsn_nm2);
                  $prdtm_prsn_rl=trim($prdtm_prsn_rl); $prdtm_prsn_nm=trim($prdtm_prsn_nm);
                }
                else {$prdtm_prsn_rl=''; $prdtm_prsn_nm=$prdtm_prsn_nm2;}

                if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $prdtm_prsn_nm))
                {
                  list($prdtm_prsn_nm, $prdtm_prsn_sffx_num)=explode('--', $prdtm_prsn_nm);
                  $prdtm_prsn_nm=trim($prdtm_prsn_nm); $prdtm_prsn_sffx_num=trim($prdtm_prsn_sffx_num);
                  $prdtm_prsn_sffx_rmn=' ('.romannumeral($prdtm_prsn_sffx_num).')';
                }
                else
                {$prdtm_prsn_sffx_num='0'; $prdtm_prsn_sffx_rmn='';}

                list($prdtm_prsn_frst_nm, $prdtm_prsn_lst_nm)=explode(';;', $prdtm_prsn_nm);
                $prdtm_prsn_frst_nm=trim($prdtm_prsn_frst_nm); $prdtm_prsn_lst_nm=trim($prdtm_prsn_lst_nm);

                if(preg_match('/\S+/', $prdtm_prsn_lst_nm)) {$prdtm_prsn_lst_nm_dsply=' '.$prdtm_prsn_lst_nm;}
                else {$prdtm_prsn_lst_nm_dsply='';}

                $prdtm_prsn_fll_nm=$prdtm_prsn_frst_nm.$prdtm_prsn_lst_nm_dsply;
                $prdtm_prsn_url=generateurl($prdtm_prsn_fll_nm.$prdtm_prsn_sffx_rmn);
                $prdtm_ordr=++$o;

                $sql="SELECT 1 FROM prsn WHERE prsn_url='$prdtm_prsn_url'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of production team (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                if(mysqli_num_rows($result)==0)
                {
                  $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                        VALUES('$prdtm_prsn_fll_nm', '$prdtm_prsn_frst_nm', '$prdtm_prsn_lst_nm', '$prdtm_prsn_sffx_num', '$prdtm_prsn_url', '0')";
                  if(!mysqli_query($link, $sql)) {$error='Error adding production team (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                }

                $sql= "INSERT INTO prdprdtm(prdid, prdtm_rlid, prdtm_sb_rl, prdtm_ordr, prdtm_compid, prdtm_prsnid)
                      SELECT $prd_id, $prdtm_rl_id, '$prdtm_prsn_rl', $prdtm_ordr, '0', prsn_id
                      FROM prsn WHERE prsn_url='$prdtm_prsn_url'";
                if(!mysqli_query($link, $sql)) {$error='Error adding production-production team (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }
            }
          }
          else {$prdtm_rl='';}

          $sql= "INSERT INTO prdprdtmrl(prdid, prdtm_rl_id, prdtm_rl)
                VALUES('$prd_id', '$prdtm_rl_id', '$prdtm_rl')";
          if(!mysqli_query($link, $sql)) {$error='Error adding production team-role association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $ssn_nm))
      {
        $ssn_url=generateurl($ssn_nm);
        $ssn_alph=alph($ssn_nm);

        $sql="SELECT 1 FROM ssn WHERE ssn_url='$ssn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of season: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO ssn(ssn_nm, ssn_alph, ssn_url)
                VALUES('$ssn_nm', CASE WHEN '$ssn_alph'!='' THEN '$ssn_alph' END, '$ssn_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding season data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql= "INSERT INTO prdssn(prdid, ssnid) SELECT '$prd_id', ssn_id FROM ssn WHERE ssn_url='$ssn_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding production-season association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      if(preg_match('/\S+/', $fstvl_nm))
      {
        $fstvl_url=generateurl($fstvl_nm);
        $fstvl_alph=alph($fstvl_nm);

        $sql="SELECT 1 FROM fstvl WHERE fstvl_url='$fstvl_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of festival: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO fstvl(fstvl_nm, fstvl_alph, fstvl_url)
                VALUES('$fstvl_nm', CASE WHEN '$fstvl_alph'!='' THEN '$fstvl_alph' END, '$fstvl_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding festival data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql="INSERT INTO prdfstvl(prdid, fstvlid) SELECT '$prd_id', fstvl_id FROM fstvl WHERE fstvl_url='$fstvl_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding production-festival association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      if(preg_match('/\S+/', $crs_list))
      {
        $ctgry_nms=explode(',,', $ctgry_list);
        $n=0;
        foreach($crs_nms as $crs_schl_typ_yr)
        {
          list($crs_schl_typ, $crs_yr)=explode('##', $crs_schl_typ_yr);
          $crs_schl_typ=trim($crs_schl_typ); $crs_yr=trim($crs_yr);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $crs_yr))
          {
            list($crs_yr, $crs_sffx_num)=explode('--', $crs_yr);
            $crs_yr=trim($crs_yr); $crs_sffx_num=trim($crs_sffx_num);
            $crs_sffx_rmn=' ('.romannumeral($crs_sffx_num).')';
          }
          else
          {$crs_sffx_num='0'; $crs_sffx_rmn='';}

          if(preg_match('/^[1-9][0-9]{0,3}\s*;;\s*[1-9][0-9]{0,3}$/', $crs_yr))
          {
            list($crs_yr_strt, $crs_yr_end)=explode(';;', $crs_yr);
            $crs_yr_strt=trim($crs_yr_strt); $crs_yr_end=trim($crs_yr_end);
            $crs_yr_url=$crs_yr_strt.'-'. $crs_yr_end;
          }
          else
          {$crs_yr_strt=$crs_yr; $crs_yr_end=$crs_yr; $crs_yr_url=$crs_yr;}

          list($crs_schl_nm, $crs_typ_nm)=explode('::', $crs_schl_typ);
          $crs_schl_nm=trim($crs_schl_nm); $crs_typ_nm=trim($crs_typ_nm);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $crs_schl_nm))
          {
            list($crs_schl_nm, $crs_schl_sffx_num)=explode('--', $crs_schl_nm);
            $crs_schl_nm=trim($crs_schl_nm); $crs_schl_sffx_num=trim($crs_schl_sffx_num);
            $crs_schl_sffx_rmn=' ('.romannumeral($crs_schl_sffx_num).')';
          }
          else
          {$crs_schl_sffx_num='0'; $crs_schl_sffx_rmn='';}

          $crs_ordr=++$n;
          $crs_schl_url=generateurl($crs_schl_nm.$crs_schl_sffx_rmn);
          $crs_typ_url=generateurl($crs_typ_nm);
          $crs_schl_alph=alph($crs_schl_nm);

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

          $sql= "SELECT 1 FROM crs
                WHERE crs_schlid=(SELECT comp_id FROM comp WHERE comp_url='$crs_schl_url')
                AND crs_typid=(SELECT crs_typ_id FROM crs_typ WHERE crs_typ_url='$crs_typ_url')
                AND crs_yr_url='$crs_yr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of course: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO crs(crs_yr_strt, crs_yr_end, crs_yr_url, crs_schlid, crs_typid)
                  SELECT '$crs_yr_strt', '$crs_yr_end', '$crs_yr_url',
                  (SELECT comp_id FROM comp WHERE comp_url='$crs_schl_url'), (SELECT crs_typ_id FROM crs_typ WHERE crs_typ_url='$crs_typ_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding course data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO prdcrs(prdid, crs_ordr, crsid)
                SELECT $prd_id, $crs_ordr,
                crs_id FROM crs WHERE crs_schlid=(SELECT comp_id FROM comp WHERE comp_url='$crs_schl_url')
                AND crs_typid=(SELECT crs_typ_id FROM crs_typ WHERE crs_typ_url='$crs_typ_url')
                AND crs_yr_url='$crs_yr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-course association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      if(preg_match('/\S+/', $rvw_list))
      {
        $rvw_pub_crtc_nm_dt_urls=explode(',,', $rvw_list);
        foreach($rvw_pub_crtc_nm_dt_urls as $rvw_pub_crtc_nm_dt_url)
        {
          list($rvw_pub_crtc_nm_dt, $rvw_url)=explode('::', $rvw_pub_crtc_nm_dt_url);
          $rvw_pub_crtc_nm_dt=trim($rvw_pub_crtc_nm_dt); $rvw_url=trim($rvw_url);

          list($rvw_pub_crtc_nm, $rvw_dt)=explode('##', $rvw_pub_crtc_nm_dt);
          $rvw_dt=preg_replace('/([0-9]{2})-([0-9]{2})-([0-9]{4})/', "$3-$2-$1", $rvw_dt);

          list($rvw_pub_nm, $rvw_crtc_nm)=explode('||', $rvw_pub_crtc_nm);
          $rvw_pub_nm=trim($rvw_pub_nm); $rvw_crtc_nm=trim($rvw_crtc_nm);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $rvw_pub_nm))
          {
            list($rvw_pub_nm, $rvw_pub_sffx_num)=explode('--', $rvw_pub_nm);
            $rvw_pub_nm=trim($rvw_pub_nm); $rvw_pub_sffx_num=trim($rvw_pub_sffx_num);
            $rvw_pub_sffx_rmn=' ('.romannumeral($rvw_pub_sffx_num).')';
          }
          else
          {
            $rvw_pub_sffx_num='0'; $rvw_pub_sffx_rmn='';
          }

          $rvw_pub_url=generateurl($rvw_pub_nm.$rvw_pub_sffx_rmn);
          $rvw_pub_alph=alph($rvw_pub_nm);

          if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $rvw_crtc_nm))
          {
            list($rvw_crtc_nm, $rvw_crtc_sffx_num)=explode('--', $rvw_crtc_nm);
            $rvw_crtc_nm=trim($rvw_crtc_nm); $rvw_crtc_sffx_num=trim($rvw_crtc_sffx_num);
            $rvw_crtc_sffx_rmn=' ('.romannumeral($rvw_crtc_sffx_num).')';
          }
          else {$rvw_crtc_sffx_num='0'; $rvw_crtc_sffx_rmn='';}

          list($rvw_crtc_frst_nm, $rvw_crtc_lst_nm)=explode(';;', $rvw_crtc_nm);
          $rvw_crtc_frst_nm=trim($rvw_crtc_frst_nm); $rvw_crtc_lst_nm=trim($rvw_crtc_lst_nm);

          if(preg_match('/\S+/', $rvw_crtc_lst_nm)) {$rvw_crtc_lst_nm_dsply=' '.$rvw_crtc_lst_nm;}
          else {$rvw_crtc_lst_nm_dsply='';}

          $rvw_crtc_fll_nm=$rvw_crtc_frst_nm.$rvw_crtc_lst_nm_dsply;
          $rvw_crtc_url=generateurl($rvw_crtc_fll_nm.$rvw_crtc_sffx_rmn);

          $sql="SELECT 1 FROM comp WHERE comp_url='$rvw_pub_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of publication (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                  VALUES('$rvw_pub_nm', CASE WHEN '$rvw_pub_alph'!='' THEN '$rvw_pub_alph' END, '$rvw_pub_sffx_num', '$rvw_pub_url', 1, 0, 0)";
            if(!mysqli_query($link, $sql)) {$error='Error adding publication (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql="SELECT 1 FROM prsn WHERE prsn_url='$rvw_crtc_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of critic (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                  VALUES('$rvw_crtc_fll_nm', '$rvw_crtc_frst_nm', '$rvw_crtc_lst_nm', '$rvw_crtc_sffx_num', '$rvw_crtc_url', '0')";
            if(!mysqli_query($link, $sql)) {$error='Error adding review critic (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO prdrvw(prdid, rvw_url, rvw_dt, rvw_pub_compid, rvw_crtc_prsnid)
                SELECT $prd_id, '$rvw_url', '$rvw_dt',
                (SELECT comp_id FROM comp WHERE comp_url='$rvw_pub_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$rvw_crtc_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-review association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

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

          $sql= "INSERT INTO prd_alt_nm(prdid, prd_alt_nm, prd_alt_nm_dscr, prd_alt_nm_ordr)
                SELECT '$prd_id', '$alt_nm', '$alt_nm_dscr', '$alt_nm_ordr'";
          if(!mysqli_query($link, $sql)) {$error='Error adding production-production alternate name association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
?>