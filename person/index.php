<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prsn_id=cln($_POST['prsn_id']);
    $sql="SELECT prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_sx FROM prsn WHERE prsn_id='$prsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring person details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['prsn_sffx_num']) {$prsn_sffx_num=html($row['prsn_sffx_num']); $prsn_sffx_rmn=' ('.romannumeral($row['prsn_sffx_num']).')';}
    else {$prsn_sffx_num=''; $prsn_sffx_rmn='';}
    $pagetab='Edit: '.html($row['prsn_fll_nm'].$prsn_sffx_rmn);
    $pagetitle=html($row['prsn_fll_nm'].$prsn_sffx_rmn);
    $prsn_frst_nm=html($row['prsn_frst_nm']);
    $prsn_lst_nm=html($row['prsn_lst_nm']);
    $prsn_sx=html($row['prsn_sx']);

    $sql="SELECT ethn_nm FROM prsn INNER JOIN ethn ON ethnid=ethn_id WHERE prsn_id='$prsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$row=mysqli_fetch_array($result); $ethn_nm=html($row['ethn_nm']);}
    else {$ethn_nm='';}

    $sql="SELECT lctn_nm, lctn_sffx_num FROM prsn INNER JOIN lctn ON org_lctnid=lctn_id WHERE prsn_id='$prsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      $row=mysqli_fetch_array($result);
      if($row['lctn_sffx_num']) {$org_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$org_lctn_sffx_num='';}
      $org_lctn_nm=html($row['lctn_nm'].$org_lctn_sffx_num);
    }
    else {$org_lctn_nm='';}

    $sql= "SELECT lctn_nm, lctn_sffx_num FROM prsn p
          INNER JOIN rel_lctn ON p.org_lctnid=rel_lctn1 INNER JOIN prsnorg_lctn_alt pola ON rel_lctn2=pola.org_lctn_altid INNER JOIN lctn ON pola.org_lctn_altid=lctn_id
          WHERE p.prsn_id='$prsn_id' AND p.prsn_id=pola.prsnid AND p.org_lctnid=pola.org_lctnid
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring alternate place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$org_lctn_alt_sffx_num='--'.$row['lctn_sffx_num'];} else {$org_lctn_alt_sffx_num='';}
      $org_lctn_alts[]=$row['lctn_nm'].$org_lctn_alt_sffx_num;
    }
    if(!empty($org_lctn_alts)) {$org_lctn_alt_list='||'.implode('>>', $org_lctn_alts);} else {$org_lctn_alt_list='';}
    $org_lctn_nm .= $org_lctn_alt_list;

    $sql="SELECT prof_nm FROM prsnprof INNER JOIN prof ON profid=prof_id WHERE prsnid='$prsn_id' ORDER BY prof_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$profs[]=$row['prof_nm'];}
    if(!empty($profs)) {$prof_list=html(implode(',,', $profs));} else {$prof_list='';}

    $sql= "SELECT comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, agnt_rl, agnt_ordr, comp_bool
          FROM prsnagnt
          INNER JOIN comp ON agnt_compid=comp_id
          WHERE prsnid='$prsn_id' AND agnt_prsnid=0
          UNION
          SELECT prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, agnt_rl, agnt_ordr, comp_bool
          FROM prsnagnt
          INNER JOIN prsn ON agnt_prsnid=prsn_id
          WHERE prsnid='$prsn_id' AND agnt_compid=0
          ORDER BY agnt_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring agent data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          $comp_agnt_nm_rl=$row['comp_nm1'].$comp_sffx_num.'::'.$row['agnt_rl'].'||';
        }
        else
        {$comp_agnt_nm_rl='';}
        $prsn_agnt_nm_rl='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          $prsn_agnt_nm_rl=$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num.'::'.$row['agnt_rl'];
        }
        else
        {$prsn_agnt_nm_rl='';}
        $comp_agnt_nm_rl='';
      }
      $agnts[$row['comp_id']]=array('comp_agnt_nm_rl'=>$comp_agnt_nm_rl, 'prsn_agnt_nm_rl'=>$prsn_agnt_nm_rl, 'agntcomp_ppl'=>array());
    }

    $sql= "SELECT agnt_compid, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, agnt_rl
          FROM prsnagnt
          INNER JOIN prsn ON agnt_prsnid=prsn_id
          WHERE prsnid='$prsn_id' AND agnt_compid!=0
          ORDER BY agnt_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring agent (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      $agnts[$row['agnt_compid']]['agntcomp_ppl'][]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num.'::'.$row['agnt_rl'];
    }

    if(!empty($agnts))
    {
      $agnt_array=array();
      foreach($agnts as $agnt)
      {
        $agntcomp_ppl_list=implode('//', $agnt['agntcomp_ppl']);
        $agnt_array[]=$agnt['comp_agnt_nm_rl'].$agnt['prsn_agnt_nm_rl'].$agntcomp_ppl_list;
      }
      $agnt_list=html(implode(',,', $agnt_array));
    }
    else
    {$agnt_list='';}

    $textarea='';
    $prsn_id=html($prsn_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prsn_id=cln($_POST['prsn_id']);
    $prsn_frst_nm=trim(cln($_POST['prsn_frst_nm']));
    $prsn_lst_nm=trim(cln($_POST['prsn_lst_nm']));
    $prsn_sffx_num=trim(cln($_POST['prsn_sffx_num']));
    if($_POST['prsn_sx']=='1') {$prsn_sx='1';}
    if($_POST['prsn_sx']=='2') {$prsn_sx='2';}
    if($_POST['prsn_sx']=='3') {$prsn_sx='3';}
    $ethn_nm=trim(cln($_POST['ethn_nm']));
    $org_lctn_nm=trim(cln($_POST['org_lctn_nm']));
    $prof_list=trim(cln($_POST['prof_list']));
    $agnt_list=cln($_POST['agnt_list']);

    $errors=array();

    if(!preg_match('/\S+/', $prsn_frst_nm))
    {$errors['prsn_frst_nm']='**You must enter a given name.**';}
    elseif(preg_match('/;;/', $prsn_frst_nm) || preg_match('/--/', $prsn_frst_nm) || preg_match('/::/', $prsn_frst_nm) || preg_match('/##/', $prsn_frst_nm) ||
    preg_match('/\|\|/', $prsn_frst_nm) || preg_match('/,,/', $prsn_frst_nm) || preg_match('/@@/', $prsn_frst_nm) || preg_match('/==/', $prsn_frst_nm) ||
    preg_match('/>>/', $prsn_frst_nm) || preg_match('/~~/', $prsn_frst_nm) || preg_match('/\+\+/', $prsn_frst_nm) || preg_match('/\/\//', $prsn_frst_nm) ||
    preg_match('/\^\^/', $prsn_frst_nm) || preg_match('/¬¬/', $prsn_frst_nm))
    {$errors['prsn_frst_nm']='**Given name cannot include any of the following: [;;], [--], [::], [##], [||], [,,], [@@], [==], [>>], [~~], [++], [//], [^^], [¬¬].**';}

    if(preg_match('/;;/', $prsn_lst_nm) || preg_match('/--/', $prsn_lst_nm) || preg_match('/::/', $prsn_lst_nm) || preg_match('/##/', $prsn_lst_nm) ||
    preg_match('/\|\|/', $prsn_lst_nm) || preg_match('/,,/', $prsn_lst_nm) || preg_match('/@@/', $prsn_lst_nm) || preg_match('/==/', $prsn_lst_nm) ||
    preg_match('/>>/', $prsn_lst_nm) || preg_match('/~~/', $prsn_lst_nm) || preg_match('/\+\+/', $prsn_lst_nm) || preg_match('/\/\//', $prsn_lst_nm) ||
    preg_match('/\^\^/', $prsn_lst_nm) || preg_match('/¬¬/', $prsn_lst_nm))
    {$errors['prsn_lst_nm']='**Family name cannot include any of the following: [;;], [--], [::], [##], [||], [,,], [@@], [==], [>>], [~~], [++], [//], [^^], [¬¬].**';}

    if(preg_match('/\S+/', $prsn_lst_nm)) {$prsn_fll_nm=$prsn_frst_nm.' '.$prsn_lst_nm; $prsn_fll_nm_session=$_POST['prsn_frst_nm'].' '.$_POST['prsn_lst_nm'];}
    else {$prsn_fll_nm=$prsn_frst_nm; $prsn_fll_nm_session=$_POST['prsn_frst_nm'];}

    if(preg_match('/^[0]*$/', $prsn_sffx_num) || !$prsn_sffx_num) {$prsn_sffx_num='0';}
    elseif(preg_match('/^AG$/i', $prsn_sffx_num)) {date_default_timezone_set('Europe/London'); $errors['prsn_sffx']='**TheatreBase: Database design © Andy Gout 2012 - '.date('Y').'.**';}
    elseif(preg_match('/^[1-9][0-9]{0,1}$/', $prsn_sffx_num)) {$prsn_fll_nm_session .= ' ('.romannumeral($_POST['prsn_sffx_num']).')';}
    else {$errors['prsn_sffx']='**The suffix must be a valid integer between 1 and 99 (with no leading 0) or left blank (or as 0).**';}

    if($prsn_sffx_num) {$prsn_sffx_rmn=' ('.romannumeral($prsn_sffx_num).')';} else {$prsn_sffx_rmn='';}
    $prsn_url=generateurl($prsn_fll_nm.$prsn_sffx_rmn);

    if(strlen($prsn_fll_nm)>255 || strlen($prsn_url)>255)
    {$errors['prsn_fll_nm_excss_lngth']='</br>**Person full name and person URL are allowed a maximum of 255 characters respectively.**';}

    if(count($errors)==0)
    {
      $sql= "SELECT prsn_id, prsn_fll_nm, prsn_sffx_num
            FROM prsn
            WHERE prsn_url='$prsn_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['prsn_id']!==$prsn_id)
      {
        if($row['prsn_sffx_num']) {$prsn_sffx_rmn=' ('.romannumeral($row['prsn_sffx_num']).')';} else {$prsn_sffx_rmn='';}
        $errors['prsn_fll_nm']='</br>**Duplicate URL exists for: '.html($row['prsn_fll_nm'].$prsn_sffx_rmn).'. You must keep the original name or assign a name without an existing URL.**';
      }
    }

    if(preg_match('/\S+/', $ethn_nm))
    {
      if(strlen($ethn_nm)>255) {$errors['ethn_nm']='</br>**Ethnicity name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
      else
      {
        $ethn_url=generateurl($ethn_nm);
        $sql="SELECT ethn_nm FROM ethn WHERE NOT EXISTS (SELECT 1 FROM ethn WHERE ethn_nm='$ethn_nm') AND ethn_url='$ethn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existing ethnicity URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        if(mysqli_num_rows($result)>0)
        {$errors['ethn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html($row['ethn_nm']).'?**';}
      }
    }

    if(preg_match('/\S+/', $org_lctn_nm))
    {
      $org_lctn=$org_lctn_nm;
      $org_lctn_pipe_excss_err_arr=array(); $org_lctn_pipe_err_arr=array(); $org_lctn_alt_empty_err_arr=array();
      $org_lctn_alt_hyphn_excss_err_arr=array(); $org_lctn_alt_sffx_err_arr=array(); $org_lctn_alt_hyphn_err_arr=array();
      $org_lctn_alt_dplct_arr=array(); $org_lctn_alt_url_err_arr=array(); $org_lctn_alt_err_arr=array();
      $org_lctn_alt_fctn_err_arr=array(); $org_lctn_alt_no_assocs=array();

      $org_lctn_errors=0;

      if(substr_count($org_lctn, '||')>1) {$org_lctn_errors++; $org_lctn_pipe_excss_err_arr[]=$org_lctn; $org_lctn_alt_list=''; $errors['org_lctn_pipe_excss']='</br>**You may only use [||] once per location for alternate location assignation. Please amend: '.html(implode(' / ', $org_lctn_pipe_excss_err_arr)).'.**';}
      elseif(preg_match('/\S+.*\|\|.*\S+/', $org_lctn))
      {
        list($org_lctn, $org_lctn_alt_list)=explode('||', $org_lctn);
        $org_lctn=trim($org_lctn); $org_lctn_alt_list=trim($org_lctn_alt_list);
      }
      elseif(substr_count($org_lctn, '||')==1) {$org_lctn_errors++; $org_lctn_pipe_err_arr[]=$org_lctn; $org_lctn_alt_list=''; $errors['org_lctn_pipe']='</br>**Alternate location assignation must use [||] in the correct format. Please amend: '.html(implode(' / ', $org_lctn_pipe_err_arr)).'.**';}
      else {$org_lctn_alt_list='';}

      if(substr_count($org_lctn, '--')>1)
      {
        $org_lctn_errors++; $org_lctn_sffx_num='0';
        $errors['org_lctn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per place of origin.**';
      }
      elseif(preg_match('/^\S+.*--.+$/', $org_lctn))
      {
        list($org_lctn_no_sffx, $org_lctn_sffx_num)=explode('--', $org_lctn);
        $org_lctn=trim($org_lctn); $org_lctn_sffx_num=trim($org_lctn_sffx_num);

        if(!preg_match('/^[1-9][0-9]{0,1}$/', $org_lctn_sffx_num))
        {
          $org_lctn_errors++; $org_lctn_sffx_num='0';
          $errors['org_lctn_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)).**';
        }
      }
      elseif(substr_count($org_lctn, '--')==1)
      {$org_lctn_errors++; $org_lctn_sffx_num='0';
      $errors['org_lctn_hyphn']='</br>**Suffix assignation must use [--] in the correct format.**';}
      else
      {$org_lctn_sffx_num='0';}

      if($org_lctn_sffx_num) {$org_lctn_sffx_rmn=' ('.romannumeral($org_lctn_sffx_num).')';} else {$org_lctn_sffx_rmn='';}

      $org_lctn_url=generateurl($org_lctn.$org_lctn_sffx_rmn);

      if(strlen($org_lctn)>255 || strlen($org_lctn_url)>255)
      {$org_lctn_errors++; $errors['org_lctn_nm_excss_lngth']='</br>**Place of origin and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

      if($org_lctn_errors==0)
      {
        $sql= "SELECT lctn_nm, lctn_sffx_num
              FROM lctn
              WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$org_lctn' AND lctn_sffx_num='$org_lctn_sffx_num')
              AND lctn_url='$org_lctn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existing place of origin URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        if(mysqli_num_rows($result)>0)
        {
          if($row['lctn_sffx_num']) {$org_lctn_url_err_sffx_num='--'.$row['lctn_sffx_num'];} else {$org_lctn_url_err_sffx_num='';}
          $errors['org_lctn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html($row['lctn_nm']).html($org_lctn_url_err_sffx_num).'?**';
        }
        else
        {
          $sql="SELECT lctn_id, lctn_fctn FROM lctn WHERE lctn_url='$org_lctn_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking whether place of origin is a fictional location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          $row=mysqli_fetch_array($result);
          if($row['lctn_fctn']) {$errors['org_lctn_nm']='</br>**Place of origin cannot be a fictional location.**';}
          else
          {
            if($org_lctn_alt_list)
            {
              if(mysqli_num_rows($result)==0) {$errors['org_lctn_alt_list']='</br>**The given location does not yet exist (and therefore cannot be assigned alternate locations).**';}
              else
              {
                $lctn_id=$row['lctn_id'];

                $org_lctn_alts=explode('>>', $org_lctn_alt_list);
                if(count($org_lctn_alts)>250)
                {$errors['org_lctn_alt_array_excss']='**Maximum of 250 locations per alternate location array allowed.**';}
                else
                {
                  $org_lctn_alt_dplct_arr=array();
                  foreach($org_lctn_alts as $org_lctn_alt)
                  {
                    $org_lctn_alt=trim($org_lctn_alt);
                    if(!preg_match('/\S+/', $org_lctn_alt))
                    {
                      $org_lctn_alt_empty_err_arr[]=$org_lctn_alt;
                      if(count($org_lctn_alt_empty_err_arr)==1) {$errors['org_lctn_alt_empty']='</br>**There is 1 empty entry in an alternate location array (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                      else {$errors['org_lctn_alt_empty']='</br>**There are '.count($org_lctn_alt_empty_err_arr).' empty entries in alternate location arrays (caused by four consecutive chevrons [>>>>] or two chevrons [>>] with no text beforehand or thereafter).**';}
                    }
                    else
                    {
                      $org_lctn_alt_errors=0;

                      if(substr_count($org_lctn_alt, '--')>1)
                      {
                        $org_lctn_alt_errors++; $org_lctn_alt_sffx_num='0'; $org_lctn_alt_hyphn_excss_err_arr[]=$org_lctn_alt;
                        $errors['org_lctn_alt_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per alternate location. Please amend: '.html(implode(' / ', $org_lctn_alt_hyphn_excss_err_arr)).'.**';
                      }
                      elseif(preg_match('/^\S+.*--.+$/', $org_lctn_alt))
                      {
                        list($org_lctn_alt_no_sffx, $org_lctn_alt_sffx_num)=explode('--', $org_lctn_alt);
                        $org_lctn_alt_no_sffx=trim($org_lctn_alt_no_sffx); $org_lctn_alt_sffx_num=trim($org_lctn_alt_sffx_num);

                        if(!preg_match('/^[1-9][0-9]{0,1}$/', $org_lctn_alt_sffx_num))
                        {
                          $org_lctn_alt_errors++; $org_lctn_alt_sffx_num='0'; $org_lctn_alt_sffx_err_arr[]=$org_lctn_alt;
                          $errors['org_lctn_alt_sffx']='</br>**The suffix (for alternate locations) must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $org_lctn_alt_sffx_err_arr)).'**';
                        }
                        $org_lctn_alt=$org_lctn_alt_no_sffx;
                      }
                      elseif(substr_count($org_lctn_alt, '--')==1)
                      {$org_lctn_alt_errors++; $org_lctn_alt_sffx_num='0'; $org_lctn_alt_hyphn_err_arr[]=$org_lctn_alt;
                      $errors['org_lctn_alt_hyphn']='</br>**Alternate location suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $org_lctn_alt_hyphn_err_arr)).'**';}
                      else
                      {$org_lctn_alt_sffx_num='0';}

                      if($org_lctn_alt_sffx_num) {$org_lctn_alt_sffx_rmn=' ('.romannumeral($org_lctn_alt_sffx_num).')';} else {$org_lctn_alt_sffx_rmn='';}

                      $org_lctn_alt_url=generateurl($org_lctn_alt.$org_lctn_alt_sffx_rmn);
                      $org_lctn_alt_dplct_arr[]=$org_lctn_alt_url;
                      if(count(array_unique($org_lctn_alt_dplct_arr))<count($org_lctn_alt_dplct_arr))
                      {$errors['org_lctn_alt_dplct']='</br>**There are entries within alternate location arrays that create duplicate location URLs.**';}

                      if(strlen($org_lctn_alt)>255 || strlen($org_lctn_alt_url)>255)
                      {$org_lctn_alt_errors++; $errors['org_lctn_alt_excss_lngth']='</br>**Alternate location name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

                      if($org_lctn_alt_errors==0)
                      {
                        $org_lctn_alt_cln=cln($org_lctn_alt);
                        $org_lctn_alt_sffx_num_cln=cln($org_lctn_alt_sffx_num);
                        $org_lctn_alt_url_cln=cln($org_lctn_alt_url);

                        $sql= "SELECT lctn_nm FROM lctn
                              WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$org_lctn_alt_cln' AND lctn_sffx_num='$org_lctn_alt_sffx_num_cln')
                              AND lctn_url='$org_lctn_alt_url_cln'";
                        $result=mysqli_query($link, $sql);
                        if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                        $row=mysqli_fetch_array($result);
                        if(mysqli_num_rows($result)>0)
                        {
                          $org_lctn_alt_url_err_arr[]=$row['lctn_nm'];
                          if(count($org_lctn_alt_url_err_arr)==1) {$errors['org_lctn_alt_url']='</br>**Duplicate location URL exists (from alternate location arrays). Did you mean to type: '.html(implode(' / ', $org_lctn_alt_url_err_arr)).'?**';}
                          else {$errors['org_lctn_alt_url']='</br>**Duplicate location URLs exist (from alternate location arrays). Did you mean to type: '.html(implode(' / ', $org_lctn_alt_url_err_arr)).'?**';}
                        }
                        else
                        {
                          $sql="SELECT lctn_id, lctn_fctn FROM lctn WHERE lctn_url='$org_lctn_alt_url_cln'";
                          $result=mysqli_query($link, $sql);
                          if(!$result) {$error='Error checking for existence of location (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                          $row=mysqli_fetch_array($result);
                          if(mysqli_num_rows($result)==0) {$org_lctn_alt_err_arr[]=$org_lctn_alt.$org_lctn_alt_sffx_rmn; $errors['org_lctn_alt']='</br>**The following locations from alternate location arrays do not yet exist (and can therefore not be assigned): '.html(implode(' / ', $org_lctn_alt_err_arr)).'.';}
                          elseif($row['lctn_fctn']) {$org_lctn_alt_fctn_err_arr[]=$org_lctn_alt.$org_lctn_alt_sffx_rmn; $errors['org_lctn_alt_fctn']='</br>**The following locations from alternate location arrays are fictional (and can therefore not be assigned): '.html(implode(' / ', $org_lctn_alt_fctn_err_arr)).'.';}
                          else
                          {
                            $lctn_alt_id=$row['lctn_id'];
                            $sql="SELECT 1 FROM rel_lctn WHERE rel_lctn1='$lctn_id' AND rel_lctn2='$lctn_alt_id'";
                            $result=mysqli_query($link, $sql);
                            if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                            $row=mysqli_fetch_array($result);
                            if(mysqli_num_rows($result)==0)
                            {
                              $org_lctn_alt_no_assocs[]=$org_lctn_alt.$org_lctn_alt_sffx_rmn;
                              $errors['org_lctn_alt_assoc']='</br>**Associations do not exist between this location and its listed alternates. Please amend: '.implode(' / ', $org_lctn_alt_no_assocs).'**';
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

    if(preg_match('/\S+/', $prof_list))
    {
      $prof_nms=explode(',,', $prof_list);
      if(count($prof_nms)>250)
      {$errors['prof_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $prof_empty_err_arr=array(); $prof_dplct_arr=array(); $prof_url_err_arr=array();
        foreach($prof_nms as $prof_nm)
        {
          $prof_nm=trim($prof_nm);
          if(!preg_match('/\S+/', $prof_nm))
          {
            $prof_empty_err_arr[]=$prof_nm;
            if(count($prof_empty_err_arr)==1) {$errors['prof_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['prof_empty']='</br>**There are '.count($prof_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $prof_url=generateurl($prof_nm);
            $prof_dplct_arr[]=$prof_url;
            if(count(array_unique($prof_dplct_arr))<count($prof_dplct_arr)) {$errors['prof_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($prof_nm)>255) {$errors['prof_nm_excss_lngth']='</br>**Profession is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

            $sql= "SELECT prof_nm
                  FROM prof
                  WHERE NOT EXISTS (SELECT 1 FROM prof WHERE prof_nm='$prof_nm')
                  AND prof_url='$prof_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing profession URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            $row=mysqli_fetch_array($result);
            if(mysqli_num_rows($result)>0)
            {
              $prof_url_err_arr[]=$row['prof_nm'];
              if(count($prof_url_err_arr)==1)
              {$errors['prof_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $prof_url_err_arr)).'?**';}
              else
              {$errors['prof_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $prof_url_err_arr)).'?**';}
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $agnt_list))
    {
      $agnt_comp_prsns=explode(',,', $_POST['agnt_list']);

      $agnt_ttl_array=array(); $agnt_comp_nm_rl_array=array(); $agnt_prsn_nm_rl_array=array(); $agnt_empty_err_arr=array();
      $agnt_pipe_excss_err_arr=array(); $agnt_pipe_err_arr=array(); $agnt_comp_cln_excss_err_arr=array();
      $agnt_comp_dplct_arr=array(); $agnt_comp_cln_err_arr=array(); $agnt_comp_hyphn_excss_err_arr=array();
      $agnt_comp_sffx_err_arr=array(); $agnt_comp_hyphn_err_arr=array(); $agnt_comp_url_err_arr=array();
      $agnt_comp_nonexst_err_arr=array(); $agnt_prsn_empty_err_arr=array(); $agnt_prsn_cln_err_arr=array();
      $agnt_prsn_cln_excss_err_arr=array(); $agnt_prsn_sffx_err_arr=array(); $agnt_prsn_hyphn_err_arr=array();
      $agnt_prsn_hyphn_excss_err_arr=array(); $agnt_prsn_dplct_arr=array(); $agnt_prsn_smcln_err_arr=array();
      $agnt_prsn_smcln_excss_err_arr=array(); $agnt_prsn_nm_err_arr=array(); $agnt_prsn_url_err_arr=array();
      $agnt_prsn_nonexst_err_arr=array(); $agncy_agnt_no_assoc_err_arr=array(); $agnt_err_arr=array();
      foreach($agnt_comp_prsns as $agnt_comp_prsn)
      {
        $agnt_comp_prsn=trim($agnt_comp_prsn);
        if(!preg_match('/\S+/', $agnt_comp_prsn))
        {
          $agnt_empty_err_arr[]=$agnt_comp_prsn; $agnt_err_arr[]='1';
          if(count($agnt_empty_err_arr)==1) {$errors['agnt_empty']='</br>**There is 1 empty entry in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          else {$errors['agnt_empty']='</br>**There are '.count($agnt_empty_err_arr).' empty entries in the string (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
        }
        else
        {
          if(substr_count($agnt_comp_prsn, '||')>1)
          {
            $agnt_prsn_nm_rl_list='';
            $agnt_pipe_excss_err_arr[]=$agnt_comp_prsn; $agnt_err_arr[]='1';
            $errors['agnt_pipe_excss']='</br>**You may only use [||] once per agency-agent(s) coupling. Please amend: '.html(implode(' / ', $agnt_pipe_excss_err_arr)).'.**';
          }
          elseif(preg_match('/\|\|/', $agnt_comp_prsn))
          {
            if(preg_match('/\S+.*\|\|(.*\S+)?/', $agnt_comp_prsn))
            {
              list($agnt_comp_nm_rl, $agnt_prsn_nm_rl_list)=explode('||', $agnt_comp_prsn);
              $agnt_comp_nm_rl=trim($agnt_comp_nm_rl); $agnt_prsn_nm_rl_list=trim($agnt_prsn_nm_rl_list);
              $agnt_comp_nm_rl_array[]=$agnt_comp_nm_rl; $agnt_ttl_array[]=$agnt_comp_nm_rl;
            }
            else
            {
              $agnt_pipe_err_arr[]=$agnt_comp_prsn; $agnt_err_arr[]='1';
              $agnt_prsn_nm_rl_list='';
              $errors['agnt_pipe']='</br>**You must assign the following as company/member using [||] in the correct format: '.html(implode(' / ', $agnt_pipe_err_arr)).'.**';
            }
          }
          else
          {
            $agnt_prsn_nm_rl_array[]=$agnt_comp_prsn; $agnt_ttl_array[]=$agnt_comp_prsn; $agnt_prsn_nm_rl_list='';
          }

          if(preg_match('/\S+/', $agnt_prsn_nm_rl_list))
          {
            $agnt_prsn_nm_rls=explode('//', $agnt_prsn_nm_rl_list);
            foreach($agnt_prsn_nm_rls as $agnt_prsn_nm_rl)
            {
              $agnt_prsn_nm_rl=trim($agnt_prsn_nm_rl);
              if(!preg_match('/\S+/', $agnt_prsn_nm_rl))
              {
                $agnt_prsn_empty_err_arr[]=$agnt_prsn_nm_rl;
                if(count($agnt_prsn_empty_err_arr)==1) {$errors['agnt_prsn_empty']='</br>**There is 1 empty entry in a company member array (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
                else {$errors['agnt_prsn_empty']='</br>**There are '.count($agnt_prsn_empty_err_arr).' empty entries in company member arrays (caused by four consecutive slashes [////] or two slashes [//] with no text beforehand or thereafter).**';}
              }
              {$agnt_prsn_nm_rl_array[]=$agnt_prsn_nm_rl; $agnt_ttl_array[]=$agnt_prsn_nm_rl;}
            }
          }
        }
      }

      if(count($agnt_ttl_array)>250)
      {$errors['agnt_array_excss']='**Maximum of 250 entries (companies and people collectively) allowed.**';}
      else
      {
        if(count($agnt_comp_nm_rl_array)>0)
        {
          foreach($agnt_comp_nm_rl_array as $agnt_comp_nm_rl)
          {
            $agnt_comp_errors=0;
            if(substr_count($agnt_comp_nm_rl, '::')>1)
            {
              $agnt_comp_errors++; $agnt_comp_nm=trim($agnt_comp_nm_rl);
              $agnt_comp_cln_excss_err_arr[]=$agnt_comp_nm_rl;
              $errors['agnt_comp_cln_excss']='</br>**You may only use [::] once per agency-role coupling. Please amend: '.html(implode(' / ', $agnt_comp_cln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*::.*\S+/', $agnt_comp_nm_rl))
            {
              list($agnt_comp_nm, $agnt_comp_rl)=explode('::', $agnt_comp_nm_rl);
              $agnt_comp_nm=trim($agnt_comp_nm); $agnt_comp_rl=trim($agnt_comp_rl);

              if(strlen($agnt_comp_rl)>255)
              {$errors['agnt_comp_rl_excss_lngth']='</br>**Agency (company) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
            }
            else
            {
              $agnt_comp_errors++; $agnt_comp_nm=trim($agnt_comp_nm_rl);
              $agnt_comp_cln_err_arr[]=$agnt_comp_nm_rl;
              $errors['agnt_comp_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $agnt_comp_cln_err_arr)).'.**';
            }

            if(substr_count($agnt_comp_nm, '--')>1)
            {
              $agnt_comp_errors++; $agnt_comp_sffx_num='0'; $agnt_comp_hyphn_excss_err_arr[]=$agnt_comp_nm;
              $errors['agnt_comp_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per agency. Please amend: '.html(implode(' / ', $agnt_comp_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $agnt_comp_nm))
            {
              list($agnt_comp_nm_no_sffx, $agnt_comp_sffx_num)=explode('--', $agnt_comp_nm);
              $agnt_comp_nm_no_sffx=trim($agnt_comp_nm_no_sffx); $agnt_comp_sffx_num=trim($agnt_comp_sffx_num);

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $agnt_comp_sffx_num))
              {
                $agnt_comp_errors++; $agnt_comp_sffx_num='0'; $agnt_comp_sffx_err_arr[]=$agnt_comp_nm;
                $errors['agnt_comp_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $agnt_comp_sffx_err_arr)).'**';
              }
              $agnt_comp_nm=$agnt_comp_nm_no_sffx;
            }
            elseif(substr_count($agnt_comp_nm, '--')==1)
            {$agnt_comp_errors++; $agnt_comp_sffx_num='0'; $agnt_comp_hyphn_err_arr[]=$agnt_comp_nm;
            $errors['agnt_comp_hyphn']='</br>**Agency suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $agnt_comp_hyphn_err_arr)).'**';}
            else
            {$agnt_comp_sffx_num='0';}

            if($agnt_comp_sffx_num) {$agnt_comp_sffx_rmn=' ('.romannumeral($agnt_comp_sffx_num).')';} else {$agnt_comp_sffx_rmn='';}

            $agnt_comp_url=generateurl($agnt_comp_nm.$agnt_comp_sffx_rmn);

            $agnt_comp_dplct_arr[]=$agnt_comp_url;
            if(count(array_unique($agnt_comp_dplct_arr))<count($agnt_comp_dplct_arr))
            {$errors['agnt_comp_dplct']='</br>**There are entries within the array that create duplicate company URLs.**';}

            if(strlen($agnt_comp_nm)>255 || strlen($agnt_comp_url)>255)
            {$agnt_comp_errors++; $errors['agnt_comp_nm_excss_lngth']='</br>**Agency (company) name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

            if($agnt_comp_errors==0)
            {
              $agnt_comp_nm_cln=cln($agnt_comp_nm);
              $agnt_comp_sffx_num_cln=cln($agnt_comp_sffx_num);
              $agnt_comp_url_cln=cln($agnt_comp_url);

              $sql= "SELECT comp_nm, comp_sffx_num
                    FROM comp
                    WHERE NOT EXISTS (SELECT 1 FROM comp WHERE comp_nm='$agnt_comp_nm_cln' AND comp_sffx_num='$agnt_comp_sffx_num_cln')
                    AND comp_url='$agnt_comp_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing agency company URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $agnt_comp_errors++;
                if($row['comp_sffx_num']) {$agnt_comp_url_error_sffx_dsply='--'.$row['comp_sffx_num'];}
                else {$agnt_comp_url_error_sffx_dsply='';}
                $agnt_comp_url_err_arr[]=$row['comp_nm'].$agnt_comp_url_error_sffx_dsply;
                if(count($agnt_comp_url_err_arr)==1)
                {$errors['agnt_comp_url']='</br>**Duplicate company URL exists. Did you mean to type: '.html(implode(' / ', $agnt_comp_url_err_arr)).'?**';}
                else
                {$errors['agnt_comp_url']='</br>**Duplicate company URLs exist. Did you mean to type: '.html(implode(' / ', $agnt_comp_url_err_arr)).'?**';}
              }
            }
            if($agnt_comp_errors>0) {$agnt_err_arr[]='1';}
          }
        }

        if(count($agnt_prsn_nm_rl_array)>0)
        {
          foreach($agnt_prsn_nm_rl_array as $agnt_prsn_nm_rl)
          {
            $agnt_prsn_errors=0;
            if(substr_count($agnt_prsn_nm_rl, '::')>1)
            {
              $agnt_prsn_errors++; $agnt_prsn_nm=trim($agnt_prsn_nm_rl);
              $agnt_prsn_cln_excss_err_arr[]=$agnt_prsn_nm_rl;
              $errors['agnt_prsn_cln_excss']='</br>**You may only use [::] once per agent-role coupling. Please amend: '.html(implode(' / ', $agnt_prsn_cln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*::.*\S+/', $agnt_prsn_nm_rl))
            {
              list($agnt_prsn_nm, $agnt_prsn_rl)=explode('::', $agnt_prsn_nm_rl);
              $agnt_prsn_nm=trim($agnt_prsn_nm); $agnt_prsn_rl=trim($agnt_prsn_rl);

              if(strlen($agnt_prsn_rl)>255)
              {$errors['agnt_prsn_rl_excss_lngth']='</br>**Agent (person) role is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}
            }
            else
            {
              $agnt_prsn_errors++; $agnt_prsn_nm=trim($agnt_prsn_nm_rl);
              $agnt_prsn_cln_err_arr[]=$agnt_prsn_nm_rl;
              $errors['agnt_prsn_cln']='</br>**You must assign a role to the following using [::]: '.html(implode(' / ', $agnt_prsn_cln_err_arr)).'.**';
            }

            if(substr_count($agnt_prsn_nm, '--')>1)
            {
              $agnt_prsn_errors++; $agnt_prsn_sffx_num='0'; $agnt_prsn_hyphn_excss_err_arr[]=$agnt_prsn_nm;
              $errors['agnt_prsn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per agent. Please amend: '.html(implode(' / ', $agnt_prsn_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $agnt_prsn_nm))
            {
              list($agnt_prsn_nm_no_sffx, $agnt_prsn_sffx_num)=explode('--', $agnt_prsn_nm);
              $agnt_prsn_nm_no_sffx=trim($agnt_prsn_nm_no_sffx); $agnt_prsn_sffx_num=trim($agnt_prsn_sffx_num);

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $agnt_prsn_sffx_num))
              {
                $agnt_prsn_errors++; $agnt_prsn_sffx_num='0'; $agnt_prsn_sffx_err_arr[]=$agnt_prsn_nm;
                $errors['agnt_prsn_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 with no leading 0). Please amend: '.html(implode(' / ', $agnt_prsn_sffx_err_arr)).'**';
              }
              $agnt_prsn_nm=$agnt_prsn_nm_no_sffx;
            }
            elseif(substr_count($agnt_prsn_nm, '--')==1)
            {$agnt_prsn_errors++; $agnt_prsn_sffx_num='0'; $agnt_prsn_hyphn_err_arr[]=$agnt_prsn_nm;
            $errors['agnt_prsn_hyphn']='</br>**Agent suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $agnt_prsn_hyphn_err_arr)).'**';}
            else
            {$agnt_prsn_sffx_num='0';}

            if($agnt_prsn_sffx_num) {$agnt_prsn_sffx_rmn=' ('.romannumeral($agnt_prsn_sffx_num).')';} else {$agnt_prsn_sffx_rmn='';}

            if(substr_count($agnt_prsn_nm, ';;')>1)
            {
              $agnt_prsn_errors++; $agnt_prsn_smcln_excss_err_arr[]=$agnt_prsn_nm;
              $errors['agnt_prsn_smcln_excss']='</br>**You may only use [;;] once per given-family name coupling. Please amend: '.html(implode(' / ', $agnt_prsn_smcln_excss_err_arr)).'.**';
            }
            elseif(preg_match('/\S+.*;;(.*\S+)?/', $agnt_prsn_nm))
            {
              list($agnt_prsn_frst_nm, $agnt_prsn_lst_nm)=explode(';;', $agnt_prsn_nm);
              $agnt_prsn_frst_nm=trim($agnt_prsn_frst_nm); $agnt_prsn_lst_nm=trim($agnt_prsn_lst_nm);

              if(preg_match('/\S+/', $agnt_prsn_lst_nm))
              {$agnt_prsn_lst_nm_dsply=' '.$agnt_prsn_lst_nm;}
              else
              {$agnt_prsn_lst_nm_dsply='';}

              $agnt_prsn_fll_nm=$agnt_prsn_frst_nm.$agnt_prsn_lst_nm_dsply;
              $agnt_prsn_url=generateurl($agnt_prsn_fll_nm.$agnt_prsn_sffx_rmn);

              $agnt_prsn_dplct_arr[]=$agnt_prsn_url;
              if(count(array_unique($agnt_prsn_dplct_arr))<count($agnt_prsn_dplct_arr))
              {$errors['agnt_prsn_dplct']='</br>**There are entries within the array that create duplicate person URLs.**';}

              if(strlen($agnt_prsn_fll_nm)>255 || strlen($agnt_prsn_url)>255)
              {$agnt_prsn_errors++; $errors['agnt_prsn_excss_lngth']='</br>**Agent (person) full name and its URL are allowed a maximum of 255 characters respectively. Please amend entries that exceed this amount.**';}
            }
            else
            {
              $agnt_prsn_errors++; $agnt_prsn_smcln_err_arr[]=$agnt_prsn_nm;
              $errors['agnt_prsn_smcln']='</br>**You must assign a given name and family name to the following using [;;]: '.html(implode(' / ', $agnt_prsn_smcln_err_arr)).'.**';
            }

            if($agnt_prsn_errors==0)
            {
              $agnt_prsn_frst_nm_cln=cln($agnt_prsn_frst_nm);
              $agnt_prsn_lst_nm_cln=cln($agnt_prsn_lst_nm);
              $agnt_prsn_fll_nm_cln=cln($agnt_prsn_fll_nm);
              $agnt_prsn_sffx_num_cln=cln($agnt_prsn_sffx_num);
              $agnt_prsn_url_cln=cln($agnt_prsn_url);

              $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                    FROM prsn
                    WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_frst_nm='$agnt_prsn_frst_nm_cln' AND prsn_lst_nm='$agnt_prsn_lst_nm_cln')
                    AND prsn_fll_nm='$agnt_prsn_fll_nm_cln' AND prsn_sffx_num='$agnt_prsn_sffx_num_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for agent person full name with assigned given name and family name: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $agnt_prsn_errors++;
                if($row['prsn_sffx_num']) {$agnt_prsn_nm_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                else {$agnt_prsn_nm_error_sffx_dsply='';}
                $agnt_prsn_nm_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$agnt_prsn_nm_error_sffx_dsply;
                if(count($agnt_prsn_nm_err_arr)==1)
                {$errors['agnt_prsn_nm']='</br>**This name has had its given and family name assigned incorrectly: '.html(implode(' / ', $agnt_prsn_nm_err_arr)).'.**';}
                else
                {$errors['agnt_prsn_nm']='</br>**These names have had their given and family names assigned incorrectly: '.html(implode(' / ', $agnt_prsn_nm_err_arr)).'.**';}
              }

              $sql= "SELECT prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
                    FROM prsn
                    WHERE NOT EXISTS (SELECT 1 FROM prsn WHERE prsn_fll_nm='$agnt_prsn_fll_nm_cln' AND prsn_sffx_num='$agnt_prsn_sffx_num_cln')
                    AND prsn_url='$agnt_prsn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing agent person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $agnt_prsn_errors++;
                if($row['prsn_sffx_num']) {$agnt_prsn_url_error_sffx_dsply='--'.$row['prsn_sffx_num'];}
                else {$agnt_prsn_url_error_sffx_dsply='';}
                $agnt_prsn_url_err_arr[]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$agnt_prsn_url_error_sffx_dsply;
                if(count($agnt_prsn_url_err_arr)==1)
                {$errors['agnt_prsn_url']='</br>**Duplicate person URL exists. Did you mean to type: '.html(implode(' / ', $agnt_prsn_url_err_arr)).'?**';}
                else
                {$errors['agnt_prsn_url']='</br>**Duplicate person URLs exist. Did you mean to type: '.html(implode(' / ', $agnt_prsn_url_err_arr)).'?**';}
              }

              if($agnt_prsn_errors==0)
              {
                $sql= "SELECT prsn_id
                      FROM prsn
                      WHERE prsn_url='$agnt_prsn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing person URL (against agent person URL): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                $agnt_prsn_id=$row['prsn_id'];
                if(mysqli_num_rows($result)==0)
                {
                  $agnt_prsn_errors++;
                  $agnt_prsn_nonexst_err_arr[]=$agnt_prsn_fll_nm.$agnt_prsn_sffx_rmn;
                  if(count($agnt_prsn_nonexst_err_arr)==1)
                  {$errors['agnt_prsn_nonexst']='</br>**The following is not an existing person: '.html(implode(' / ', $agnt_prsn_nonexst_err_arr)).'.**';}
                  else
                  {$errors['agnt_prsn_nonexst']='</br>**The following are not existing people : '.html(implode(' / ', $agnt_prsn_nonexst_err_arr)).'.**';}
                }
                elseif($agnt_prsn_id==$prsn_id)
                {
                  $errors['agnt_prsn_id_mtch']='</br>**You cannot assign this person as an agent of themself: '.html($agnt_prsn_fll_nm.$agnt_prsn_sffx_rmn).'.**';
                }
              }
            }
          }
          if($agnt_prsn_errors>0) {$agnt_err_arr[]='1';}
        }
      }

      if(count($agnt_err_arr)==0 && preg_match('/\S+/', $_POST['agnt_list']))
      {
        $agnt_comp_prsns=explode(',,', $_POST['agnt_list']);
        foreach($agnt_comp_prsns as $agnt_comp_prsn)
        {
          if(preg_match('/\|\|/', $agnt_comp_prsn))
          {
            list($agnt_comp_nm_rl, $agnt_prsn_nm_rl_list)=explode('||', $agnt_comp_prsn);
            $agnt_comp_nm_rl=trim($agnt_comp_nm_rl); $agnt_prsn_nm_rl_list=trim($agnt_prsn_nm_rl_list);
          }
          else
          {$agnt_comp_nm_rl=''; $agnt_prsn_nm_rl_list='';}

          if(preg_match('/\S+/', $agnt_comp_nm_rl))
          {
            list($agnt_comp_nm, $agnt_comp_rl)=explode('::', $agnt_comp_nm_rl);
            $agnt_comp_nm=trim($agnt_comp_nm); $agnt_comp_rl=trim($agnt_comp_rl);

            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $agnt_comp_nm))
            {
              list($agnt_comp_nm, $agnt_comp_sffx_num)=explode('--', $agnt_comp_nm);
              $agnt_comp_nm=trim($agnt_comp_nm); $agnt_comp_sffx_num=trim($agnt_comp_sffx_num);
              $agnt_comp_sffx_rmn=' ('.romannumeral($agnt_comp_sffx_num).')';
            }
            else
            {$agnt_comp_sffx_num='0'; $agnt_comp_sffx_rmn='';}

            $agnt_comp_url_cln=cln(generateurl($agnt_comp_nm.$agnt_comp_sffx_rmn));

            $sql= "SELECT comp_id
                  FROM comp
                  WHERE comp_url='$agnt_comp_url_cln'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing company URL (against agency company URL): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            $row=mysqli_fetch_array($result);
            $agnt_comp_id=$row['comp_id'];
          }

          if(preg_match('/\S+/', $agnt_prsn_nm_rl_list))
          {
            $agnt_prsn_nm_rls=explode('//', $agnt_prsn_nm_rl_list);
            foreach($agnt_prsn_nm_rls as $agnt_prsn_nm_rl)
            {
              list($agnt_prsn_nm, $agnt_prsn_rl)=explode('::', $agnt_prsn_nm_rl);
              $agnt_prsn_nm=trim($agnt_prsn_nm); $agnt_prsn_rl=trim($agnt_prsn_rl);

              if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $agnt_prsn_nm))
              {
                list($agnt_prsn_nm, $agnt_prsn_sffx_num)=explode('--', $agnt_prsn_nm);
                $agnt_prsn_nm=trim($agnt_prsn_nm); $agnt_prsn_sffx_num=trim($agnt_prsn_sffx_num);
                $agnt_prsn_sffx_rmn=' ('.romannumeral($agnt_prsn_sffx_num).')';
              }
              else
              {$agnt_prsn_sffx_num='0'; $agnt_prsn_sffx_rmn='';}

              list($agnt_prsn_frst_nm, $agnt_prsn_lst_nm)=explode(';;', $agnt_prsn_nm);
              $agnt_prsn_frst_nm=trim($agnt_prsn_frst_nm); $agnt_prsn_lst_nm=trim($agnt_prsn_lst_nm);

              if(preg_match('/\S+/', $agnt_prsn_lst_nm)) {$agnt_prsn_lst_nm_dsply=' '.$agnt_prsn_lst_nm;}
              else {$agnt_prsn_lst_nm_dsply='';}

              $agnt_prsn_fll_nm=$agnt_prsn_frst_nm.$agnt_prsn_lst_nm_dsply;
              $agnt_prsn_url_cln=cln(generateurl($agnt_prsn_fll_nm.$agnt_prsn_sffx_rmn));

              $sql= "SELECT 1
                    FROM compprsn
                    WHERE compid='$agnt_comp_id'
                    AND prsnid=(SELECT prsn_id FROM prsn WHERE prsn_url='$agnt_prsn_url_cln')";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing company-person relationship (for given agency-agent): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              if(mysqli_num_rows($result)==0)
              {
                $agncy_agnt_no_assoc_err_arr[]=$agnt_comp_nm.$agnt_comp_sffx_rmn.' - '.$agnt_prsn_fll_nm.$agnt_prsn_sffx_rmn;
                if(count($agncy_agnt_no_assoc_err_arr)==1)
                {$errors['agncy_agnt_no_assoc']='</br>**The following does not yet exist as a company-member relationship: '.html(implode(' / ', $agncy_agnt_no_assoc_err_arr)).'.**';}
                else
                {$errors['agncy_agnt_no_assoc']='</br>**The following do not yet exist as company-member relationships: '.html(implode(' / ', $agncy_agnt_no_assoc_err_arr)).'.**';}
              }
            }
          }
        }
      }
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $prsn_id=cln($_POST['prsn_id']);
      $sql= "SELECT prsn_fll_nm, prsn_sffx_num
            FROM prsn
            WHERE prsn_id='$prsn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result)
      {$error='Error acquiring person details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['prsn_sffx_num']) {$prsn_sffx_rmn=' ('.romannumeral($row['prsn_sffx_num']).')';} else {$prsn_sffx_rmn='';}
      $pagetab='Edit: '.html($row['prsn_fll_nm'].$prsn_sffx_rmn);
      $pagetitle=html($row['prsn_fll_nm'].$prsn_sffx_rmn);
      $prsn_frst_nm=$_POST['prsn_frst_nm'];
      $prsn_lst_nm=$_POST['prsn_lst_nm'];
      $prsn_sffx_num=$_POST['prsn_sffx_num'];
      $ethn_nm=$_POST['ethn_nm'];
      $org_lctn_nm=$_POST['org_lctn_nm'];
      $prof_list=$_POST['prof_list'];
      $agnt_list=$_POST['agnt_list'];
      $textarea=$_POST['textarea'];
      $errors['prsn_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $prsn_id=html($prsn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE prsn SET
            prsn_fll_nm='$prsn_fll_nm',
            prsn_frst_nm='$prsn_frst_nm',
            prsn_lst_nm='$prsn_lst_nm',
            prsn_sffx_num='$prsn_sffx_num',
            prsn_url='$prsn_url',
            prsn_sx='$prsn_sx'
            WHERE prsn_id='$prsn_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating person info for submitted person: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="UPDATE prsn SET ethnid=NULL WHERE prsn_id='$prsn_id'";
      if(!mysqli_query($link, $sql)) {$error='Error nullifying person-ethnicity associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $ethn_nm))
      {
        $ethn_url=generateurl($ethn_nm);

        $sql="SELECT 1 FROM ethn WHERE ethn_url='$ethn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of ethnicity: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO ethn(ethn_nm, ethn_url) VALUES('$ethn_nm', '$ethn_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql="UPDATE prsn SET ethnid=(SELECT ethn_id FROM ethn WHERE ethn_url='$ethn_url')
            WHERE prsn_id='$prsn_id'";
        if(!mysqli_query($link, $sql)) {$error='Error adding person-ethnicity association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }

      $sql="UPDATE prsn SET org_lctnid=NULL WHERE prsn_id='$prsn_id'";
      if(!mysqli_query($link, $sql)) {$error='Error nullifying person-place of origin associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM prsnorg_lctn_alt WHERE prsnid='$prsn_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting person-place of origin (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $org_lctn_nm))
      {
        $org_lctn=$org_lctn_nm;
        if(preg_match('/\S+.*\|\|.*\S+/', $org_lctn))
        {
          list($org_lctn, $org_lctn_alt_list)=explode('||', $org_lctn);
          $org_lctn=trim($org_lctn); $org_lctn_alt_list=trim($org_lctn_alt_list);
        }
        else {$org_lctn_alt_list='';}

        if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $org_lctn))
        {
          list($org_lctn, $org_lctn_sffx_num)=explode('--', $org_lctn);
          $org_lctn=trim($org_lctn); $org_lctn_sffx_num=trim($org_lctn_sffx_num);
          $org_lctn_sffx_rmn=' ('.romannumeral($org_lctn_sffx_num).')';
        }
        else
        {
          $org_lctn_sffx_num='0';
          $org_lctn_sffx_rmn='';
        }

        $org_lctn_url=generateurl($org_lctn.$org_lctn_sffx_rmn);
        $org_lctn_alph=alph($org_lctn);

        $sql="SELECT 1 FROM lctn WHERE lctn_url='$org_lctn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of place of origin: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO lctn(lctn_nm, lctn_alph, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn)
                VALUES('$org_lctn', CASE WHEN '$org_lctn_alph'!='' THEN '$org_lctn_alph' END, '$org_lctn_sffx_num', '$org_lctn_url', 0, 0)";
          if(!mysqli_query($link, $sql)) {$error='Error adding place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql="UPDATE prsn SET org_lctnid=(SELECT lctn_id FROM lctn WHERE lctn_url='$org_lctn_url') WHERE prsn_id='$prsn_id'";
        if(!mysqli_query($link, $sql)) {$error='Error adding person-place of origin association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

        if($org_lctn_alt_list)
        {
          $org_lctn_alts=explode('>>', $org_lctn_alt_list);
          foreach($org_lctn_alts as $org_lctn_alt)
          {
            $org_lctn_alt=trim($org_lctn_alt);

            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $org_lctn_alt))
            {
              list($org_lctn_alt, $org_lctn_alt_sffx_num)=explode('--', $org_lctn_alt);
              $org_lctn_alt=trim($org_lctn_alt); $org_lctn_alt_sffx_num=trim($org_lctn_alt_sffx_num);
              $org_lctn_alt_sffx_rmn=' ('.romannumeral($org_lctn_alt_sffx_num).')';
            }
            else {$org_lctn_alt_sffx_num='0'; $org_lctn_alt_sffx_rmn='';}

            $org_lctn_alt_url=generateurl($org_lctn_alt.$org_lctn_alt_sffx_rmn);

            $sql= "INSERT INTO prsnorg_lctn_alt(prsnid, org_lctnid, org_lctn_altid)
                  SELECT '$prsn_id',
                  (SELECT lctn_id FROM lctn WHERE lctn_url='$org_lctn_url'),
                  (SELECT lctn_id FROM lctn WHERE lctn_url='$org_lctn_alt_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding person-place of origin (alternate location) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }

      $sql="DELETE FROM prsnprof WHERE prsnid='$prsn_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting person-profession associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $prof_list))
      {
        $prof_nms=explode(',,', $prof_list);
        $n=0;
        foreach($prof_nms as $prof_nm)
        {
          $prof_nm=trim($prof_nm);
          $prof_url=generateurl($prof_nm);
          $prof_ordr=++$n;

          $sql="SELECT 1 FROM prof WHERE prof_url='$prof_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of profession: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql="INSERT INTO prof(prof_nm, prof_url) VALUES('$prof_nm', '$prof_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO prsnprof(prsnid, prof_ordr, profid)
                SELECT '$prsn_id', '$prof_ordr', prof_id FROM prof WHERE prof_url='$prof_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding person-profession association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }

      $sql="DELETE FROM prsnagnt WHERE prsnid='$prsn_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting production-production team associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(preg_match('/\S+/', $agnt_list))
      {
        $agnt_comp_prsns=explode(',,', $agnt_list);
        $n=0;
        foreach($agnt_comp_prsns as $agnt_comp_prsn)
        {
          if(preg_match('/\|\|/', $agnt_comp_prsn))
          {
            list($agnt_comp_nm_rl, $agnt_prsn_nm_rl_list)=explode('||', $agnt_comp_prsn);
            $agnt_comp_nm_rl=trim($agnt_comp_nm_rl); $agnt_prsn_nm_rl_list=trim($agnt_prsn_nm_rl_list);
            $agnt_prsn_nm_rl2='';
          }
          else
          {$agnt_comp_nm_rl=''; $agnt_prsn_nm_rl_list=''; $agnt_prsn_nm_rl2=trim($agnt_comp_prsn);}

          if(preg_match('/\S+/', $agnt_comp_nm_rl))
          {
            list($agnt_comp_nm, $agnt_comp_rl)=explode('::', $agnt_comp_nm_rl);
            $agnt_comp_nm=trim($agnt_comp_nm); $agnt_comp_rl=trim($agnt_comp_rl);

            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $agnt_comp_nm))
            {
              list($agnt_comp_nm, $agnt_comp_sffx_num)=explode('--', $agnt_comp_nm);
              $agnt_comp_nm=trim($agnt_comp_nm); $agnt_comp_sffx_num=trim($agnt_comp_sffx_num);
              $agnt_comp_sffx_rmn=' ('.romannumeral($agnt_comp_sffx_num).')';
            }
            else
            {$agnt_comp_sffx_num='0'; $agnt_comp_sffx_rmn='';}

            $agnt_ordr=++$n;
            $agnt_comp_url=generateurl($agnt_comp_nm.$agnt_comp_sffx_rmn);
            $agnt_comp_alph=alph($agnt_comp_nm);

            $sql="SELECT 1 FROM comp WHERE comp_url='$agnt_comp_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existence of agency (company): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $sql= "INSERT INTO comp(comp_nm, comp_alph, comp_sffx_num, comp_url, comp_bool, comp_dslv, comp_nm_exp)
                    VALUES('$agnt_comp_nm', CASE WHEN '$agnt_comp_alph'!='' THEN '$agnt_comp_alph' END, '$agnt_comp_sffx_num', '$agnt_comp_url', 1, 0, 0)";
              if(!mysqli_query($link, $sql)) {$error='Error adding agency (company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }

            $sql= "INSERT INTO prsnagnt(prsnid, agnt_ordr, agnt_rl, agnt_prsnid, agnt_compid)
                  SELECT $prsn_id, $agnt_ordr, '$agnt_comp_rl', '0', comp_id FROM comp WHERE comp_url='$agnt_comp_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding person-agency (company) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          if(preg_match('/\S+/', $agnt_prsn_nm_rl_list))
          {
            $agnt_prsn_nm_rls=explode('//', $agnt_prsn_nm_rl_list);
            foreach($agnt_prsn_nm_rls as $agnt_prsn_nm_rl)
            {
              list($agnt_prsn_nm, $agnt_prsn_rl)=explode('::', $agnt_prsn_nm_rl);
              $agnt_prsn_nm=trim($agnt_prsn_nm); $agnt_prsn_rl=trim($agnt_prsn_rl);

              if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $agnt_prsn_nm))
              {
                list($agnt_prsn_nm, $agnt_prsn_sffx_num)=explode('--', $agnt_prsn_nm);
                $agnt_prsn_nm=trim($agnt_prsn_nm); $agnt_prsn_sffx_num=trim($agnt_prsn_sffx_num);
                $agnt_prsn_sffx_rmn=' ('.romannumeral($agnt_prsn_sffx_num).')';
              }
              else
              {$agnt_prsn_sffx_num='0'; $agnt_prsn_sffx_rmn='';}

              list($agnt_prsn_frst_nm, $agnt_prsn_lst_nm)=explode(';;', $agnt_prsn_nm);
              $agnt_prsn_frst_nm=trim($agnt_prsn_frst_nm); $agnt_prsn_lst_nm=trim($agnt_prsn_lst_nm);

              if(preg_match('/\S+/', $agnt_prsn_lst_nm))
              {$agnt_prsn_lst_nm_dsply=' '.$agnt_prsn_lst_nm;}
              else
              {$agnt_prsn_lst_nm_dsply='';}

              $agnt_prsn_fll_nm=$agnt_prsn_frst_nm.$agnt_prsn_lst_nm_dsply;
              $agnt_prsn_url=generateurl($agnt_prsn_fll_nm.$agnt_prsn_sffx_rmn);
              $agnt_ordr=++$n;

              $sql="SELECT 1 FROM prsn WHERE prsn_url='$agnt_prsn_url'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existence of agent (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              if(mysqli_num_rows($result)==0)
              {
                $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                      VALUES('$agnt_prsn_fll_nm', '$agnt_prsn_frst_nm', '$agnt_prsn_lst_nm', '$agnt_prsn_sffx_num', '$agnt_prsn_url', '0')";
                if(!mysqli_query($link, $sql)) {$error='Error adding agent (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              }

              $sql= "INSERT INTO prsnagnt(prsnid, agnt_ordr, agnt_rl, agnt_compid, agnt_prsnid)
                    SELECT $prsn_id, $agnt_ordr, '$agnt_prsn_rl',
                    (SELECT comp_id FROM comp WHERE comp_url='$agnt_comp_url'), (SELECT prsn_id FROM prsn WHERE prsn_url='$agnt_prsn_url')";
              if(!mysqli_query($link, $sql)) {$error='Error adding person-agent (person - company member) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }
          }

          if(preg_match('/\S+/', $agnt_prsn_nm_rl2))
          {
            list($agnt_prsn_nm, $agnt_prsn_rl)=explode('::', $agnt_prsn_nm_rl2);
            $agnt_prsn_nm=trim($agnt_prsn_nm); $agnt_prsn_rl=trim($agnt_prsn_rl);

            if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $agnt_prsn_nm))
            {
              list($agnt_prsn_nm, $agnt_prsn_sffx_num)=explode('--', $agnt_prsn_nm);
              $agnt_prsn_nm=trim($agnt_prsn_nm); $agnt_prsn_sffx_num=trim($agnt_prsn_sffx_num);
              $agnt_prsn_sffx_rmn=' ('.romannumeral($agnt_prsn_sffx_num).')';
            }
            else
            {$agnt_prsn_sffx_num='0'; $agnt_prsn_sffx_rmn='';}

            list($agnt_prsn_frst_nm, $agnt_prsn_lst_nm)=explode(';;', $agnt_prsn_nm);
            $agnt_prsn_frst_nm=trim($agnt_prsn_frst_nm); $agnt_prsn_lst_nm=trim($agnt_prsn_lst_nm);

            if(preg_match('/\S+/', $agnt_prsn_lst_nm))
            {$agnt_prsn_lst_nm_dsply=' '.$agnt_prsn_lst_nm;}
            else
            {$agnt_prsn_lst_nm_dsply='';}

            $agnt_prsn_fll_nm=$agnt_prsn_frst_nm.$agnt_prsn_lst_nm_dsply;
            $agnt_prsn_url=generateurl($agnt_prsn_fll_nm.$agnt_prsn_sffx_rmn);
            $agnt_ordr=++$n;

            $sql="SELECT 1 FROM prsn WHERE prsn_url='$agnt_prsn_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existence of agent (person): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            if(mysqli_num_rows($result)==0)
            {
              $sql= "INSERT INTO prsn(prsn_fll_nm, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, prsn_url, comp_bool)
                    VALUES('$agnt_prsn_fll_nm', '$agnt_prsn_frst_nm', '$agnt_prsn_lst_nm', '$agnt_prsn_sffx_num', '$agnt_prsn_url', '0')";
              if(!mysqli_query($link, $sql)) {$error='Error adding agent (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            }

            $sql= "INSERT INTO prsnagnt(prsnid, agnt_ordr, agnt_rl, agnt_compid, agnt_prsnid)
                  SELECT $prsn_id, $agnt_ordr, '$agnt_prsn_rl', '0', prsn_id FROM prsn WHERE prsn_url='$agnt_prsn_url'";
            if(!mysqli_query($link, $sql)) {$error='Error adding person-agent (person - independent) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PERSON HAS BEEN EDITED:'.' '.html($prsn_fll_nm_session);
    header('Location: '.$prsn_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $prsn_id=cln($_POST['prsn_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM prdwri WHERE wri_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-person (writer) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Writer (production)';}

    $sql="SELECT 1 FROM prdprdcr WHERE prdcr_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-person (producer) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Producer (production)';}

    $sql="SELECT 1 FROM prdprf WHERE prf_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-person (performer) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Performer (production)';}

    $sql="SELECT 1 FROM prdus WHERE us_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-person (understudy) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Understudy (production)';}

    $sql="SELECT 1 FROM prdmscn WHERE mscn_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-person (musician) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Musician (production)';}

    $sql="SELECT 1 FROM prdcrtv WHERE crtv_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-person (creative) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Creative (production)';}

    $sql="SELECT 1 FROM prdprdtm WHERE prdtm_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-person (prod team) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Production team (production)';}

    $sql="SELECT 1 FROM prdrvw WHERE rvw_crtc_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring production-person (critic) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Review critic (production)';}

    $sql="SELECT 1 FROM ptwri WHERE wri_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-person (writer) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Writer (playtext)';}

    $sql="SELECT 1 FROM crsstff_prsn WHERE stff_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring course-person (course staff) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Course staff';}

    $sql="SELECT 1 FROM crsstdnt_prsn WHERE stdnt_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring course-person (course student) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Course student';}

    $sql="SELECT 1 FROM compprsn WHERE prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring company-member (person) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Member (company)';}

    $sql="SELECT 1 FROM prsnagnt WHERE prsnid='$prsn_id' OR agnt_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring agent-person (client and agent) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Agent (client)';}

    $sql="SELECT 1 FROM ptlcnsr WHERE lcnsr_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-person (licensor) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Licensor (playtext)';}

    $sql="SELECT 1 FROM awrdnomppl WHERE nom_prsnid='$prsn_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring award-person (nominee/winner) association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Awards nomination/win';}

    if(count($assocs)>0)
    {$errors['prsn_dlt']='**Person must have no associations before being deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT prsn_fll_nm, prsn_sffx_num
            FROM prsn
            WHERE prsn_id='$prsn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring person details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['prsn_sffx_num']) {$prsn_sffx_rmn=' ('.romannumeral($row['prsn_sffx_num']).')';} else {$prsn_sffx_rmn='';}
      $pagetab='Edit: '.html($row['prsn_fll_nm'].$prsn_sffx_rmn);
      $pagetitle=html($row['prsn_fll_nm'].$prsn_sffx_rmn);
      $prsn_frst_nm=$_POST['prsn_frst_nm'];
      $prsn_lst_nm=$_POST['prsn_lst_nm'];
      $prsn_sffx_num=$_POST['prsn_sffx_num'];
      if($_POST['prsn_sx']=='1') {$prsn_sx='1';}
      if($_POST['prsn_sx']=='2') {$prsn_sx='2';}
      if($_POST['prsn_sx']=='3') {$prsn_sx='3';}
      $ethn_nm=$_POST['ethn_nm'];
      $org_lctn_nm=$_POST['org_lctn_nm'];
      $prof_list=$_POST['prof_list'];
      $agnt_list=$_POST['agnt_list'];
      $textarea=$_POST['textarea'];
      $prsn_id=html($prsn_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT prsn_fll_nm, prsn_sffx_num
            FROM prsn
            WHERE prsn_id='$prsn_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring person details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['prsn_sffx_num']) {$prsn_sffx_rmn=' ('.romannumeral($row['prsn_sffx_num']).')';} else {$prsn_sffx_rmn='';}
      $pagetab='Delete confirmation: '.html($row['prsn_fll_nm'].$prsn_sffx_rmn);
      $pagetitle=html($row['prsn_fll_nm']);
      $prsn_id=html($prsn_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prsn_id=cln($_POST['prsn_id']);
    $sql= "SELECT prsn_fll_nm, prsn_sffx_num
          FROM prsn
          WHERE prsn_id='$prsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring person details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['prsn_sffx_num']) {$prsn_sffx_rmn=' ('.romannumeral($row['prsn_sffx_num']).')';} else {$prsn_sffx_rmn='';}
    $prsn_fll_nm=$row['prsn_fll_nm'].$prsn_sffx_rmn;

    $sql="DELETE FROM prsnprof WHERE prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting person-profession associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdwri WHERE wri_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-person (writer) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdcr WHERE prdcr_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-person (producer) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprf WHERE prf_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-person (performer) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdus WHERE us_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-person (understudy) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdmscn WHERE mscn_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-person (musician) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdcrtv WHERE crtv_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-person (creative) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdprdtm WHERE prdtm_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-person (prod team) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prdrvw WHERE rvw_crtc_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting production-person (critic) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptwri WHERE wri_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting playtext-person (writer) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM crsstff_prsn WHERE stff_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-person (staff) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM crsstdnt_prsn WHERE stdnt_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting course-person (student) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM compprsn WHERE prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting company-member associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prsnagnt WHERE prsnid='$prsn_id' OR agnt_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting person-agent associations (as agent and client): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM ptlcnsr WHERE lcnsr_prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting person-licensor associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prsnorg_lctn_alt WHERE prsnid='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting person-place of origin (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM prsn WHERE prsn_id='$prsn_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting person: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS PERSON HAS BEEN DELETED FROM THE DATABASE:'.' '.html($prsn_fll_nm);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $prsn_id=cln($_POST['prsn_id']);
    $sql= "SELECT prsn_url
          FROM prsn
          WHERE prsn_id='$prsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring person URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['prsn_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $prsn_url=cln($_GET['prsn_url']);

  $sql="SELECT prsn_id FROM prsn WHERE prsn_url='$prsn_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $prsn_id=$row['prsn_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT prsn_fll_nm, prsn_sffx_num, prsn_sx FROM prsn WHERE prsn_id='$prsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring person data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['prsn_sffx_num']) {$prsn_sffx_rmn=' ('.romannumeral($row['prsn_sffx_num']).')';} else {$prsn_sffx_rmn='';}
    $pagetab=html($row['prsn_fll_nm'].$prsn_sffx_rmn);
    $pagetitle=html($row['prsn_fll_nm']);
    if($row['prsn_sx']=='2') {$prsn_sx='Male';} elseif($row['prsn_sx']=='3') {$prsn_sx='Female';} else {$prsn_sx=NULL;}

    $sql="SELECT ethn_nm, ethn_url FROM prsn INNER JOIN ethn ON ethnid=ethn_id WHERE prsn_id='$prsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0)
    {$ethn='<a href="/person/ethnicity/'.html($row['ethn_url']).'">'.html($row['ethn_nm']).'</a>';} else {$ethn=NULL;}

    $sql= "SELECT ethn_nm, ethn_url FROM prsn INNER JOIN rel_ethn ON ethnid=rel_ethn1 INNER JOIN ethn ON rel_ethn2=ethn_id
          WHERE prsn_id='$prsn_id' ORDER BY rel_ethn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_ethns[]='<a href="/person/ethnicity/'.html($row['ethn_url']).'">'.html($row['ethn_nm']).'</a>';}

    $sql="SELECT lctn_nm, lctn_url FROM prsn INNER JOIN lctn ON org_lctnid=lctn_id WHERE prsn_id='$prsn_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if(mysqli_num_rows($result)>0)
    {$org_lctn_nm='<a href="/person/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>';} else {$org_lctn_nm=NULL;}

    $sql= "SELECT lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
          FROM prsn p
          INNER JOIN rel_lctn ON org_lctnid=rel_lctn1 INNER JOIN lctn ON rel_lctn2=lctn_id
          LEFT OUTER JOIN prsnorg_lctn_alt pola ON prsn_id=prsnid AND p.org_lctnid=pola.org_lctnid
          WHERE prsn_id='$prsn_id' AND lctn_exp=0 AND lctn_fctn=0 AND pola.prsnid IS NULL
          UNION
          SELECT lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
          FROM prsn p
          INNER JOIN rel_lctn ON p.org_lctnid=rel_lctn1 INNER JOIN prsnorg_lctn_alt pola ON rel_lctn2=org_lctn_altid
          INNER JOIN lctn ON org_lctn_altid=lctn_id
          WHERE prsn_id='$prsn_id' AND prsn_id=prsnid AND p.org_lctnid=pola.org_lctnid
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related location data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']);} else {$rel_lctn_nt1='';}
      if($row['rel_lctn_nt2']) {$rel_lctn_nt2=html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2='';}
      $rel_lctns[]=$rel_lctn_nt1.'<a href="/person/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt2;
    }

    $sql="SELECT prof_id, prof_nm, prof_url FROM prsnprof INNER JOIN prof ON profid=prof_id WHERE prsnid='$prsn_id' ORDER BY prof_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$profs[$row['prof_id']]=array('prof_nm'=>'<a href="/person/profession/'.html($row['prof_url']).'">'.html($row['prof_nm']).'</a>', 'rel_profs'=>array());}

      $sql= "SELECT rel_prof1, prof_nm, prof_url FROM prsnprof INNER JOIN rel_prof ON profid=rel_prof1 INNER JOIN prof ON rel_prof2=prof_id
            WHERE prsnid='$prsn_id' ORDER BY rel_prof_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$profs[$row['rel_prof1']]['rel_profs'][]='<a href="/person/profession/'.html($row['prof_url']).'">'.html($row['prof_nm']).'</a>';}
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm,
          p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdwri
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE wri_prsnid='$prsn_id' AND grntr=0
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
          DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
          prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdwri
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE wri_prsnid='$prsn_id' AND grntr=0 AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM prdwri WHERE prdid='$prd_id' AND wri_prsnid='$prsn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdwri
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE wri_prsnid='$prsn_id' AND grntr=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for (writer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, NULL AS comp_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdwri
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE wri_prsnid='$prsn_id' AND grntr=1
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, NULL AS comp_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdwri
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE wri_prsnid='$prsn_id' AND grntr=1 AND coll_ov IS NULL
          GROUP BY prd_id
          UNION
          SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, NULL AS comp_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdprdcr
          LEFT OUTER JOIN comp ON prdcr_compid=comp_id INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id
          INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE prdcr_prsnid='$prsn_id' AND grntr=1
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, comp_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdprdcr
          LEFT OUTER JOIN comp ON prdcr_compid=comp_id INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE prdcr_prsnid='$prsn_id' AND grntr=1 AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
        $grntr_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'comp_nm'=>$comp_nm, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'prdcr_comprls'=>array(), 'comprl_co_ppl'=>array(), 'prdcr_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($grntr_prd_ids))
      {
        foreach($grntr_prd_ids as $prd_id)
        {
          $sql= "SELECT 1 FROM prdwri WHERE prdid='$prd_id' AND wri_prsnid='$prsn_id' AND grntr=1
                UNION
                SELECT 1 FROM prdprdcr WHERE prdid='$prd_id' AND prdcr_prsnid='$prsn_id' AND grntr=1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as grantor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT pp.prdid, prdcr_sb_rl, prdcr_comprl
            FROM prdprdcr pp
            INNER JOIN prdprdcr_comprl ppcr ON pp.prdid=ppcr.prdid AND prdcr_comp_rlid=prdcr_comp_rl_id INNER JOIN prd ON pp.prdid=prd_id
            WHERE prdcr_prsnid='$prsn_id' AND grntr=1 AND pp.prdcr_compid!=0 AND coll_ov IS NULL";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring producer company role credits for (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['prdcr_sb_rl']) {$prdcr_comprl=html($row['prdcr_sb_rl']);} else {$prdcr_comprl=html($row['prdcr_comprl']);}
        $prds[$row['prdid']]['prdcr_comprls'][]=$prdcr_comprl;
      }

      $sql= "SELECT prd_id, prsn_fll_nm
            FROM prdprdcr pp1
            INNER JOIN prdprdcr_comprl ppcr ON pp1.prdid=ppcr.prdid AND pp1.prdcr_comp_rlid=prdcr_comp_rl_id
            INNER JOIN prdprdcr pp2 ON ppcr.prdid=pp2.prdid AND prdcr_comp_rl_id=pp2.prdcr_comp_rlid
            INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
            WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=1 AND pp1.prdcr_prsnid!=pp2.prdcr_prsnid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY pp2.prdcr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring people who share producer company role credits for (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['comprl_co_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT prd_id, prdcr_rl_id, prdcr_rl
            FROM prdprdcr pp
            INNER JOIN prdprdcrrl ppr ON pp.prdid=ppr.prdid AND prdcr_rlid=prdcr_rl_id INNER JOIN prd ON pp.prdid=prd_id
            WHERE prdcr_prsnid='$prsn_id' AND grntr=1 AND (prdcr_compid=0 OR prdcr_crdt=1) AND coll_ov IS NULL
            GROUP BY prd_id, prdcr_rl_id
            ORDER BY prdcr_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring producer role data for (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {$prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rl_id']]=array('prdcr_rl'=>html($row['prdcr_rl']), 'prdcrs'=>array());}

        $sql= "SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, comp_id, comp_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN comp ON pp2.prdcr_compid=comp_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=1 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_prsnid=0 AND coll_ov IS NULL
              GROUP BY prd_id, prdcr_rlid, comp_id
              UNION
              SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, prsn_id, prsn_fll_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=1 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_compid=0 AND coll_ov IS NULL
              GROUP BY prd_id, prdcr_rlid, prsn_id
              ORDER BY prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credited producers for (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
              WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=1 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_crdt=1 AND coll_ov IS NULL
              GROUP BY prd_id, prdcr_rlid, prdcr_compid, prsn_id
              ORDER BY pp2.prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credited producers for (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['prdcrcomp_ppl_crdt'][]=array('prsn_nm'=>html($row['prsn_fll_nm']));}
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, NULL AS comp_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM prdwri
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE wri_prsnid='$prsn_id' AND grntr=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            UNION
            SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, comp_nm, prd_frst_dt, coll_sbhdrid, coll_ordr
            FROM prdprdcr
            LEFT OUTER JOIN comp ON prdcr_compid=comp_id INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE prdcr_prsnid='$prsn_id' AND grntr=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment data for (rights grantor) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
          $grntr_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'comp_nm'=>$comp_nm, 'wri_rls'=>array(), 'prdcr_comprls'=>array(), 'comprl_co_ppl'=>array(), 'prdcr_rls'=>array());
        }

        if(!empty($grntr_sg_prd_ids))
        {
          foreach($grntr_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, pp.prdid, prdcr_sb_rl, prdcr_comprl
              FROM prdprdcr pp
              INNER JOIN prdprdcr_comprl ppcr ON pp.prdid=ppcr.prdid AND prdcr_comp_rlid=prdcr_comp_rl_id INNER JOIN prd ON pp.prdid=prd_id
              WHERE prdcr_prsnid='$prsn_id' AND grntr=1 AND pp.prdcr_compid!=0 AND coll_ov IS NOT NULL";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring producer company role credits for (rights grantor) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['prdcr_sb_rl']) {$prdcr_comprl=html($row['prdcr_sb_rl']);} else {$prdcr_comprl=html($row['prdcr_comprl']);}
          $prds[$row['coll_ov']]['sg_prds'][$row['prdid']]['prdcr_comprls'][]=$prdcr_comprl;
        }

        $sql= "SELECT coll_ov, prd_id, prsn_fll_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcr_comprl ppcr ON pp1.prdid=ppcr.prdid AND pp1.prdcr_comp_rlid=prdcr_comp_rl_id
              INNER JOIN prdprdcr pp2 ON ppcr.prdid=pp2.prdid AND prdcr_comp_rl_id=pp2.prdcr_comp_rlid
              INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=1 AND pp1.prdcr_prsnid!=pp2.prdcr_prsnid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prsn_id
              ORDER BY pp2.prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring people who share producer company role credits for (rights grantor) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['comprl_co_ppl'][]=html($row['prsn_fll_nm']);}

        $sql= "SELECT coll_ov, prd_id, prdcr_rl_id, prdcr_rl
              FROM prdprdcr pp
              INNER JOIN prdprdcrrl ppr ON pp.prdid=ppr.prdid AND prdcr_rlid=prdcr_rl_id INNER JOIN prd ON pp.prdid=prd_id
              WHERE prdcr_prsnid='$prsn_id' AND grntr=1 AND (prdcr_compid=0 OR prdcr_crdt=1) AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdcr_rl_id
              ORDER BY prdcr_rl_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring producer role data for (rights grantor) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)>0)
        {
          while($row=mysqli_fetch_array($result))
          {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdcr_rls'][$row['prdcr_rl_id']]=array('prdcr_rl'=>html($row['prdcr_rl']), 'prdcrs'=>array());}

          $sql= "SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, comp_id, comp_nm
                FROM prdprdcr pp1
                INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
                INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
                INNER JOIN comp ON pp2.prdcr_compid=comp_id INNER JOIN prd ON pp1.prdid=prd_id
                WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=1 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_prsnid=0 AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prd_id, prdcr_rlid, comp_id
                UNION
                SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, prsn_id, prsn_fll_nm
                FROM prdprdcr pp1
                INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
                INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
                INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
                WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=1 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_compid=0 AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prd_id, prdcr_rlid, prsn_id
                ORDER BY prdcr_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring credited producers for (rights grantor) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
                WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=1 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_crdt=1 AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prd_id, prdcr_rlid, prdcr_compid, prsn_id
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
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, NULL AS comp_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdprdcr
          LEFT OUTER JOIN comp ON prdcr_compid=comp_id INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id
          INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE prdcr_prsnid='$prsn_id' AND grntr=0
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, comp_nm, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdprdcr
          LEFT OUTER JOIN comp ON prdcr_compid=comp_id INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE prdcr_prsnid='$prsn_id' AND grntr=0 AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
        $prdcr_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'comp_nm'=>$comp_nm, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'prdcr_comprls'=>array(), 'comprl_co_ppl'=>array(), 'prdcr_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prdcr_prd_ids))
      {
        foreach($prdcr_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdprdcr WHERE prdid='$prd_id' AND prdcr_prsnid='$prsn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as producer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT pp.prdid, prdcr_sb_rl, prdcr_comprl
            FROM prdprdcr pp
            INNER JOIN prdprdcr_comprl ppcr ON pp.prdid=ppcr.prdid AND prdcr_comp_rlid=prdcr_comp_rl_id INNER JOIN prd ON pp.prdid=prd_id
            WHERE prdcr_prsnid='$prsn_id' AND grntr=0 AND pp.prdcr_compid!=0 AND coll_ov IS NULL";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring credits for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['prdcr_sb_rl']) {$prdcr_comprl=html($row['prdcr_sb_rl']);} else {$prdcr_comprl=html($row['prdcr_comprl']);}
        $prds[$row['prdid']]['prdcr_comprls'][]=$prdcr_comprl;
      }

      $sql= "SELECT prd_id, prsn_fll_nm
            FROM prdprdcr pp1
            INNER JOIN prdprdcr_comprl ppcr ON pp1.prdid=ppcr.prdid AND pp1.prdcr_comp_rlid=prdcr_comp_rl_id
            INNER JOIN prdprdcr pp2 ON ppcr.prdid=pp2.prdid AND prdcr_comp_rl_id=pp2.prdcr_comp_rlid
            INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
            WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=0 AND pp1.prdcr_prsnid!=pp2.prdcr_prsnid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY pp2.prdcr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring people who share company role credits for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['comprl_co_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT prd_id, prdcr_rl_id, prdcr_rl
            FROM prdprdcr pp
            INNER JOIN prdprdcrrl ppr ON pp.prdid=ppr.prdid AND prdcr_rlid=prdcr_rl_id INNER JOIN prd ON pp.prdid=prd_id
            WHERE prdcr_prsnid='$prsn_id' AND grntr=0 AND (prdcr_compid=0 OR prdcr_crdt=1) AND coll_ov IS NULL
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
            WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=0 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_prsnid=0 AND coll_ov IS NULL
            GROUP BY prd_id, prdcr_rlid, comp_id
            UNION
            SELECT prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, prsn_id, prsn_fll_nm
            FROM prdprdcr pp1
            INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
            INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
            INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
            WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=0 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_compid=0 AND coll_ov IS NULL
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
            WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=0 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_crdt=1 AND coll_ov IS NULL
            GROUP BY prd_id, prdcr_rlid, prdcr_compid, prsn_id
            ORDER BY pp2.prdcr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring credited producers for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['prdcrcomp_ppl_crdt'][]=array('prsn_nm'=>html($row['prsn_fll_nm']));}

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, comp_nm
            FROM prdprdcr
            LEFT OUTER JOIN comp ON prdcr_compid=comp_id INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE prdcr_prsnid='$prsn_id' AND grntr=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment data for (producer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
          $prdcr_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'comp_nm'=>$comp_nm, 'wri_rls'=>array(), 'prdcr_comprls'=>array(), 'comprl_co_ppl'=>array(), 'prdcr_rls'=>array());
        }

        if(!empty($prdcr_sg_prd_ids))
        {
          foreach($prdcr_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, pp.prdid, prdcr_sb_rl, prdcr_comprl
              FROM prdprdcr pp
              INNER JOIN prdprdcr_comprl ppcr ON pp.prdid=ppcr.prdid AND prdcr_comp_rlid=prdcr_comp_rl_id INNER JOIN prd ON pp.prdid=prd_id
              WHERE prdcr_prsnid='$prsn_id' AND grntr=0 AND pp.prdcr_compid!=0 AND coll_ov IS NOT NULL";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credits for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['prdcr_sb_rl']) {$prdcr_comprl=html($row['prdcr_sb_rl']);} else {$prdcr_comprl=html($row['prdcr_comprl']);}
          $prds[$row['coll_ov']]['sg_prds'][$row['prdid']]['prdcr_comprls'][]=$prdcr_comprl;
        }

        $sql= "SELECT coll_ov, prd_id, prsn_fll_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcr_comprl ppcr ON pp1.prdid=ppcr.prdid AND pp1.prdcr_comp_rlid=prdcr_comp_rl_id
              INNER JOIN prdprdcr pp2 ON ppcr.prdid=pp2.prdid AND prdcr_comp_rl_id=pp2.prdcr_comp_rlid
              INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=0 AND pp1.prdcr_prsnid!=pp2.prdcr_prsnid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prsn_id
              ORDER BY pp2.prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring people who share company role credits for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['comprl_co_ppl'][]=html($row['prsn_fll_nm']);}

        $sql= "SELECT coll_ov, prd_id, prdcr_rl_id, prdcr_rl
              FROM prdprdcr pp
              INNER JOIN prdprdcrrl ppr ON pp.prdid=ppr.prdid AND prdcr_rlid=prdcr_rl_id INNER JOIN prd ON pp.prdid=prd_id
              WHERE prdcr_prsnid='$prsn_id' AND grntr=0 AND (prdcr_compid=0 OR prdcr_crdt=1) AND coll_ov IS NOT NULL
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
              WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=0 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_prsnid=0 AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdcr_rlid, comp_id
              UNION
              SELECT coll_ov, prd_id, pp2.prdcr_rlid, pp2.prdcr_ordr, pp2.prdcr_sb_rl, prsn_id, prsn_fll_nm
              FROM prdprdcr pp1
              INNER JOIN prdprdcrrl ppr ON pp1.prdid=ppr.prdid AND pp1.prdcr_rlid=prdcr_rl_id
              INNER JOIN prdprdcr pp2 ON ppr.prdid=pp2.prdid AND prdcr_rl_id=pp2.prdcr_rlid
              INNER JOIN prsn ON pp2.prdcr_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
              WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=0 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_compid=0 AND coll_ov IS NOT NULL
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
              WHERE pp1.prdcr_prsnid='$prsn_id' AND pp1.grntr=0 AND (pp1.prdcr_compid=0 OR pp1.prdcr_crdt=1) AND pp2.prdcr_crdt=1 AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdcr_rlid, prdcr_compid, prsn_id
              ORDER BY pp2.prdcr_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credited producers (company people) for (producer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdcr_rls'][$row['prdcr_rlid']]['prdcrs'][$row['prdcr_compid']]['prdcrcomp_ppl_crdt'][]=array('prsn_nm'=>html($row['prsn_fll_nm']));}
      }
      $prdcr_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdprf
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE prf_prsnid='$prsn_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdprf
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE prf_prsnid='$prsn_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (performer) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $prf_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'prf_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($prf_prd_ids))
      {
        foreach($prf_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdprf WHERE prdid='$prd_id' AND prf_prsnid='$prsn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as performer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prdid AS p1, prf_rl AS pr, prf_rl_alt, prf_rl_lnk AS prl, (SELECT char_url FROM prdprf pp INNER JOIN prdpt ppt ON pp.prdid=ppt.prdid INNER JOIN ptchar pc ON ppt.ptid = pc.ptid INNER JOIN role ON charid=char_id LEFT OUTER JOIN prd ON pp.prdid=prd_id WHERE prf_prsnid='$prsn_id' AND (char_nm=pr OR char_lnk=prl) AND pp.prdid=p1 AND coll_ov IS NULL LIMIT 1) AS char_url
            FROM prdprf
            INNER JOIN prd ON prdid=prd_id
            WHERE prf_prsnid='$prsn_id' AND coll_ov IS NULL
            ORDER BY prf_rl_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (performer) roles for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_url']) {$prf_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['pr']).'</a>';} else {$prf_rl=html($row['pr']);}
        if($row['prf_rl_alt']) {$prf_rl.='<span style="font-style:normal"> (alt)</span>';}
        $prds[$row['p1']]['prf_rls'][]=$prf_rl;
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdprf
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE prf_prsnid='$prsn_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (performer) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $sg_prf_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('sg_prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'sg_prd_dts'=>$prd_dts, 'sg_thtr'=>$thtr, 'wri_rls'=>array(), 'sg_prf_rls'=>array());
      }

      if(!empty($sg_prf_prd_ids))
      {
        foreach($sg_prf_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }

      $sql= "SELECT coll_ov, prdid AS p1, prf_rl AS pr, prf_rl_alt, prf_rl_lnk AS prl, (SELECT char_url FROM prdprf pp INNER JOIN prdpt ppt ON pp.prdid=ppt.prdid INNER JOIN ptchar pc ON ppt.ptid = pc.ptid INNER JOIN role ON charid=char_id INNER JOIN prd ON pp.prdid=prd_id WHERE prf_prsnid='$prsn_id' AND (char_nm=pr OR char_lnk=prl) AND pp.prdid=p1 AND coll_ov IS NOT NULL LIMIT 1) AS char_url
            FROM prdprf
            INNER JOIN prd ON prdid=prd_id
            WHERE prf_prsnid='$prsn_id' AND coll_ov IS NOT NULL
            ORDER BY prf_rl_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (performer) roles for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_url']) {$prf_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['pr']).'</a>';} else {$prf_rl=html($row['pr']);}
        if($row['prf_rl_alt']) {$prf_rl.='<span style="font-style:normal"> (alt)</span>';}
        $prds[$row['coll_ov']]['sg_prds'][$row['p1']]['sg_prf_rls'][]=$prf_rl;
      }
      $prf_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdus
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE us_prsnid='$prsn_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdus
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE us_prsnid='$prsn_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (understudy) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $us_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'us_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($us_prd_ids))
      {
        foreach($us_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdus WHERE prdid='$prd_id' AND us_prsnid='$prsn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as understudy): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prdid AS p1, us_rl AS usr, us_rl_alt, us_rl_lnk AS usrl, (SELECT char_url FROM prdus pus INNER JOIN prdpt ppt ON pus.prdid=ppt.prdid INNER JOIN ptchar pc ON ppt.ptid = pc.ptid INNER JOIN role ON charid=char_id LEFT OUTER JOIN prd ON pus.prdid=prd_id WHERE us_prsnid='$prsn_id' AND (char_nm=usr OR char_lnk=usrl) AND pus.prdid=p1 AND coll_ov IS NULL LIMIT 1) AS char_url
            FROM prdus
            INNER JOIN prd ON prdid=prd_id
            WHERE us_prsnid='$prsn_id' AND coll_ov IS NULL
            ORDER BY us_rl_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (understudy) roles for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_url']) {$us_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['usr']).'</a>';} else {$us_rl=html($row['usr']);}
        if($row['us_rl_alt']) {$us_rl.='<span style="font-style:normal"> (alt)</span>';}
        $prds[$row['p1']]['us_rls'][]=$us_rl;
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdus
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE us_prsnid='$prsn_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (understudy) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $sg_us_prd_ids[]=$row['prd_id'];
        $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('sg_prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'sg_prd_dts'=>$prd_dts, 'sg_thtr'=>$thtr, 'wri_rls'=>array(), 'sg_us_rls'=>array());
      }

      if(!empty($sg_us_prd_ids))
      {
        foreach($sg_us_prd_ids as $sg_prd_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
        }
      }

      $sql= "SELECT coll_ov, prdid AS p1, us_rl AS usr, us_rl_alt, us_rl_lnk AS usrl, (SELECT char_url FROM prdus pus INNER JOIN prdpt ppt ON pus.prdid=ppt.prdid INNER JOIN ptchar pc ON ppt.ptid = pc.ptid INNER JOIN role ON charid=char_id INNER JOIN prd ON pus.prdid=prd_id WHERE us_prsnid='$prsn_id' AND (char_nm=usr OR char_lnk=usrl) AND pus.prdid=p1 AND coll_ov IS NOT NULL LIMIT 1) AS char_url
            FROM prdus
            INNER JOIN prd ON prdid=prd_id
            WHERE us_prsnid='$prsn_id' AND coll_ov IS NOT NULL
            ORDER BY us_rl_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (understudy) roles for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_url']) {$us_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['usr']).'</a>';} else {$us_rl=html($row['usr']);}
        if($row['us_rl_alt']) {$us_rl.='<span style="font-style:normal"> (alt)</span>';}
        $prds[$row['coll_ov']]['sg_prds'][$row['p1']]['sg_us_rls'][]=$us_rl;
      }
      $us_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdmscn
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE mscn_prsnid='$prsn_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdmscn pm
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE mscn_prsnid='$prsn_id' AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (musician) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $mscn_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'mscn_rls'=>array(), 'sg_prds'=>array());
      }

      if(!empty($mscn_prd_ids))
      {
        foreach($mscn_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdmscn WHERE prdid='$prd_id' AND mscn_prsnid='$prsn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as musician): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT pm.prdid, mscn_rl_id, mscn_sb_rl, mscn_rl, NULL AS comp_nm
            FROM prdmscn pm
            INNER JOIN prdmscnrl pmr ON pm.prdid=pmr.prdid AND mscn_rlid=mscn_rl_id INNER JOIN prd ON pm.prdid=prd_id
            WHERE mscn_prsnid='$prsn_id' AND pm.mscn_compid=0 AND coll_ov IS NULL
            UNION
            SELECT pm.prdid, mscn_comp_rl_id, mscn_sb_rl, mscn_comprl, comp_nm
            FROM prdmscn pm
            INNER JOIN comp ON mscn_compid=comp_id INNER JOIN prdmscn_comprl pmcr ON pm.prdid=pmcr.prdid AND mscn_comp_rlid=mscn_comp_rl_id
            INNER JOIN prd ON pm.prdid=prd_id
            WHERE mscn_prsnid='$prsn_id' AND pm.mscn_compid!=0 AND coll_ov IS NULL";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring credits for (musician) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['mscn_sb_rl']) {$mscn_rl=$row['mscn_sb_rl'];} else {$mscn_rl=$row['mscn_rl'];}
        if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
        $prds[$row['prdid']]['mscn_rls'][$row['mscn_rl_id']]=array('mscn_rl'=>html($mscn_rl), 'comp_nm'=>$comp_nm, 'co_ppl'=>array(), 'comprl_co_ppl'=>array());
      }

      $sql= "SELECT prd_id, pm1.mscn_rlid, pm2.mscn_ordr, comp_nm
            FROM prdmscn pm1
            INNER JOIN prdmscnrl pmr ON pm1.prdid=pmr.prdid AND pm1.mscn_rlid=mscn_rl_id
            INNER JOIN prdmscn pm2 ON pmr.prdid=pm2.prdid AND mscn_rl_id=pm2.mscn_rlid
            INNER JOIN comp ON pm2.mscn_compid=comp_id INNER JOIN prd ON pm1.prdid=prd_id
            WHERE pm1.mscn_prsnid='$prsn_id' AND pm1.mscn_compid=0 AND pm2.mscn_prsnid=0 AND pm1.mscn_compid!=pm2.mscn_compid AND coll_ov IS NULL
            GROUP BY prd_id, comp_id
            UNION
            SELECT prd_id, pm1.mscn_rlid, pm2.mscn_ordr, prsn_fll_nm
            FROM prdmscn pm1
            INNER JOIN prdmscnrl pmr ON pm1.prdid=pmr.prdid AND pm1.mscn_rlid=mscn_rl_id
            INNER JOIN prdmscn pm2 ON pmr.prdid=pm2.prdid AND mscn_rl_id=pm2.mscn_rlid
            INNER JOIN prsn ON pm2.mscn_prsnid=prsn_id INNER JOIN prd ON pm1.prdid=prd_id
            WHERE pm1.mscn_prsnid='$prsn_id' AND pm1.mscn_compid=0 AND pm2.mscn_compid=0 AND pm1.mscn_prsnid!=pm2.mscn_prsnid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY mscn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-credited musician (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['mscn_rls'][$row['mscn_rlid']]['co_ppl'][]=html($row['comp_nm']);}

      $sql= "SELECT prd_id, pm1.mscn_comp_rlid, prsn_fll_nm
            FROM prdmscn pm1
            INNER JOIN prdmscn_comprl pmcr ON pm1.prdid=pmcr.prdid AND pm1.mscn_comp_rlid=mscn_comp_rl_id
            INNER JOIN prdmscn pm2 ON pmcr.prdid=pm2.prdid AND mscn_comp_rl_id=pm2.mscn_comp_rlid
            INNER JOIN prsn ON pm2.mscn_prsnid=prsn_id INNER JOIN prd ON pm1.prdid=prd_id
            WHERE pm1.mscn_prsnid='$prsn_id' AND pm1.mscn_prsnid!=pm2.mscn_prsnid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY pm2.mscn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring people who share company role credits for (musician) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['mscn_rls'][$row['mscn_comp_rlid']]['comprl_co_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdmscn
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE mscn_prsnid='$prsn_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring (musician) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $mscn_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'mscn_rls'=>array());
        }

        if(!empty($mscn_sg_prd_ids))
        {
          foreach($mscn_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT coll_ov, pm.prdid, mscn_rl_id, mscn_sb_rl, mscn_rl, NULL AS comp_nm
              FROM prdmscn pm
              INNER JOIN prdmscnrl pmr ON pm.prdid=pmr.prdid AND mscn_rlid=mscn_rl_id INNER JOIN prd ON pm.prdid=prd_id
              WHERE mscn_prsnid='$prsn_id' AND pm.mscn_compid=0 AND coll_ov IS NOT NULL
              UNION
              SELECT coll_ov, pm.prdid, mscn_comp_rl_id, mscn_sb_rl, mscn_comprl, comp_nm
              FROM prdmscn pm
              INNER JOIN comp ON mscn_compid=comp_id INNER JOIN prdmscn_comprl pmcr ON pm.prdid=pmcr.prdid AND mscn_comp_rlid=mscn_comp_rl_id
              INNER JOIN prd ON pm.prdid=prd_id
              WHERE mscn_prsnid='$prsn_id' AND pm.mscn_compid!=0 AND coll_ov IS NOT NULL";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credits for (musician) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['mscn_sb_rl']) {$mscn_rl=$row['mscn_sb_rl'];} else {$mscn_rl=$row['mscn_rl'];}
          if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
          $prds[$row['coll_ov']]['sg_prds'][$row['prdid']]['mscn_rls'][$row['mscn_rl_id']]=array('mscn_rl'=>html($mscn_rl), 'comp_nm'=>$comp_nm, 'co_ppl'=>array(), 'comprl_co_ppl'=>array());
        }

        $sql= "SELECT coll_ov, prd_id, pm1.mscn_rlid, pm2.mscn_ordr, comp_nm
              FROM prdmscn pm1
              INNER JOIN prdmscnrl pmr ON pm1.prdid=pmr.prdid AND pm1.mscn_rlid=mscn_rl_id
              INNER JOIN prdmscn pm2 ON pmr.prdid=pm2.prdid AND mscn_rl_id=pm2.mscn_rlid
              INNER JOIN comp ON pm2.mscn_compid=comp_id INNER JOIN prd ON pm1.prdid=prd_id
              WHERE pm1.mscn_prsnid='$prsn_id' AND pm1.mscn_compid=0 AND pm2.mscn_prsnid=0 AND pm1.mscn_rlid!=pm2.mscn_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, comp_id
              UNION
              SELECT coll_ov, prd_id, pm1.mscn_rlid, pm2.mscn_ordr, prsn_fll_nm
              FROM prdmscn pm1
              INNER JOIN prdmscnrl pmr ON pm1.prdid=pmr.prdid AND pm1.mscn_rlid=mscn_rl_id
              INNER JOIN prdmscn pm2 ON pmr.prdid=pm2.prdid AND mscn_rl_id=pm2.mscn_rlid
              INNER JOIN prsn ON pm2.mscn_prsnid=prsn_id INNER JOIN prd ON pm1.prdid=prd_id
              WHERE pm1.mscn_prsnid='$prsn_id' AND pm1.mscn_compid=0 AND pm2.mscn_compid=0 AND pm1.mscn_prsnid!=pm2.mscn_prsnid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prsn_id
              ORDER BY mscn_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring awards co-credited musician (company/people) data for segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['mscn_rls'][$row['mscn_rlid']]['co_ppl'][]=html($row['comp_nm']);}

        $sql= "SELECT coll_ov, prd_id, pm1.mscn_comp_rlid, prsn_fll_nm
              FROM prdmscn pm1
              INNER JOIN prdmscn_comprl pmcr ON pm1.prdid=pmcr.prdid AND pm1.mscn_comp_rlid=mscn_comp_rl_id
              INNER JOIN prdmscn pm2 ON pmcr.prdid=pm2.prdid AND mscn_comp_rl_id=pm2.mscn_comp_rlid
              INNER JOIN prsn ON pm2.mscn_prsnid=prsn_id INNER JOIN prd ON pm1.prdid=prd_id
              WHERE pm1.mscn_prsnid='$prsn_id' AND pm1.mscn_prsnid!=pm2.mscn_prsnid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prsn_id
              ORDER BY pm2.mscn_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring people who share company role credits for (musician) segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['prds'][$row['prd_id']]['mscn_rls'][$row['mscn_comp_rlid']]['comprl_co_ppl'][]=html($row['prsn_fll_nm']);}
      }
      $mscn_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdcrtv
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE crtv_prsnid='$prsn_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdcrtv pc
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE crtv_prsnid='$prsn_id' AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM prdcrtv WHERE prdid='$prd_id' AND crtv_prsnid='$prsn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as creative): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, crtv_rl_id, crtv_sb_rl, comp_nm, crtv_rl
            FROM prdcrtv pc
            LEFT OUTER JOIN comp ON crtv_compid=comp_id INNER JOIN prdcrtvrl pcr ON pc.prdid=pcr.prdid AND crtv_rlid=crtv_rl_id
            INNER JOIN prd ON pc.prdid=prd_id
            WHERE crtv_prsnid='$prsn_id' AND coll_ov IS NULL
            GROUP BY prd_id, crtv_rl_id
            ORDER BY crtv_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring creative team roles for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['crtv_sb_rl']) {$crtv_rl=$row['crtv_sb_rl'];} else {$crtv_rl=$row['crtv_rl'];}
        if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
        $prds[$row['prd_id']]['crtv_rls'][$row['crtv_rl_id']]=array('crtv_rl'=>html($crtv_rl), 'comp_nm'=>$comp_nm, 'co_comp_ppl'=>array(), 'co_ppl'=>array());
      }

      $sql= "SELECT prd_id, pc1.crtv_rlid, pc1.crtv_compid, prsn_fll_nm
            FROM prdcrtv pc1
            INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
            INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
            INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
            WHERE pc1.crtv_prsnid='$prsn_id' AND pc2.crtv_compid!=0 AND pc1.crtv_compid=pc2.crtv_compid
            AND pc1.crtv_prsnid!=pc2.crtv_prsnid AND coll_ov IS NULL
            GROUP BY prd_id, crtv_compid, prsn_id
            ORDER BY pc2.crtv_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring co-credited creative (company people - same company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_comp_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT prd_id, pc1.crtv_rlid, pc2.crtv_ordr, comp_id, comp_nm
            FROM prdcrtv pc1
            INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
            INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
            INNER JOIN comp ON pc2.crtv_compid=comp_id INNER JOIN prd ON pc1.prdid=prd_id
            WHERE pc1.crtv_prsnid='$prsn_id' AND pc2.crtv_prsnid=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NULL
            GROUP BY prd_id, comp_id
            UNION
            SELECT prd_id, pc1.crtv_rlid, pc2.crtv_ordr, prsn_id, prsn_fll_nm
            FROM prdcrtv pc1
            INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
            INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
            INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
            WHERE pc1.crtv_prsnid='$prsn_id' AND pc2.crtv_compid=0 AND pc1.crtv_prsnid!=pc2.crtv_prsnid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY crtv_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring co-credited creative (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_ppl'][$row['comp_id']]=array('co_prsn'=>html($row['comp_nm']), 'comp_ppl'=>array());}

      $sql= "SELECT prd_id, pc1.crtv_rlid, pc2.crtv_compid, prsn_fll_nm
            FROM prdcrtv pc1
            INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
            INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
            INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
            WHERE pc1.crtv_prsnid='$prsn_id' AND pc2.crtv_compid!=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NULL
            GROUP BY prd_id, crtv_compid, prsn_id
            ORDER BY pc2.crtv_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring co-credited creative (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_ppl'][$row['crtv_compid']]['comp_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdcrtv
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE crtv_prsnid='$prsn_id' AND coll_ov IS NOT NULL
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

        $sql= "SELECT coll_ov, prd_id, crtv_rl_id, crtv_sb_rl, comp_nm, crtv_rl
              FROM prdcrtv pc
              LEFT OUTER JOIN comp ON crtv_compid=comp_id INNER JOIN prdcrtvrl pcr ON pc.prdid=pcr.prdid AND crtv_rlid=crtv_rl_id
              INNER JOIN prd ON pc.prdid=prd_id
              WHERE crtv_prsnid='$prsn_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, crtv_rl_id
              ORDER BY crtv_rl_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring creative team roles for production segments: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['crtv_sb_rl']) {$crtv_rl=$row['crtv_sb_rl'];} else {$crtv_rl=$row['crtv_rl'];}
          if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['crtv_rls'][$row['crtv_rl_id']]=array('crtv_rl'=>html($crtv_rl), 'comp_nm'=>$comp_nm, 'co_comp_ppl'=>array(), 'co_ppl'=>array());
        }

        $sql= "SELECT coll_ov, prd_id, pc1.crtv_rlid, pc1.crtv_compid, prsn_fll_nm
              FROM prdcrtv pc1
              INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
              INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
              INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
              WHERE pc1.crtv_prsnid='$prsn_id' AND pc2.crtv_compid!=0 AND pc1.crtv_compid=pc2.crtv_compid AND pc1.crtv_prsnid!=pc2.crtv_prsnid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, crtv_compid, prsn_id
              ORDER BY pc2.crtv_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring co-credited creative (company people - same company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_comp_ppl'][]=html($row['prsn_fll_nm']);}

        $sql= "SELECT coll_ov, prd_id, pc1.crtv_rlid, pc2.crtv_ordr, comp_id, comp_nm
              FROM prdcrtv pc1
              INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
              INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
              INNER JOIN comp ON pc2.crtv_compid=comp_id INNER JOIN prd ON pc1.prdid=prd_id
              WHERE pc1.crtv_prsnid='$prsn_id' AND pc2.crtv_prsnid=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, comp_id
              UNION
              SELECT coll_ov, prd_id, pc1.crtv_rlid, pc2.crtv_ordr, prsn_id, prsn_fll_nm
              FROM prdcrtv pc1
              INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
              INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
              INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
              WHERE pc1.crtv_prsnid='$prsn_id' AND pc2.crtv_compid=0 AND pc1.crtv_prsnid!=pc2.crtv_prsnid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prsn_id
              ORDER BY crtv_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring co-credited creative (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_ppl'][$row['comp_id']]=array('co_prsn'=>html($row['comp_nm']), 'comp_ppl'=>array());}

        $sql= "SELECT coll_ov, prd_id, pc1.crtv_rlid, pc2.crtv_compid, prsn_fll_nm
              FROM prdcrtv pc1
              INNER JOIN prdcrtvrl pcr ON pc1.prdid=pcr.prdid AND pc1.crtv_rlid=crtv_rl_id
              INNER JOIN prdcrtv pc2 ON pcr.prdid=pc2.prdid AND crtv_rl_id=pc2.crtv_rlid
              INNER JOIN prsn ON pc2.crtv_prsnid=prsn_id INNER JOIN prd ON pc1.prdid=prd_id
              WHERE pc1.crtv_prsnid='$prsn_id' AND pc2.crtv_compid!=0 AND pc1.crtv_compid!=pc2.crtv_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, crtv_compid, prsn_id
              ORDER BY pc2.crtv_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring co-credited creative (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['crtv_rls'][$row['crtv_rlid']]['co_ppl'][$row['crtv_compid']]['comp_ppl'][]=html($row['prsn_fll_nm']);}
      }
      $crtv_prds=$prds;
    }

    $prds=array();
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdprdtm
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE prdtm_prsnid='$prsn_id'
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdprdtm
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE prdtm_prsnid='$prsn_id' AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM prdprdtm WHERE prdid='$prd_id' AND prdtm_prsnid='$prsn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as production team): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, prdtm_rl_id, prdtm_sb_rl, comp_nm, prdtm_rl
            FROM prdprdtm ppt
            LEFT OUTER JOIN comp ON prdtm_compid=comp_id INNER JOIN prdprdtmrl pptr ON ppt.prdid=pptr.prdid AND prdtm_rlid=prdtm_rl_id
            INNER JOIN prd ON ppt.prdid=prd_id
            WHERE prdtm_prsnid='$prsn_id' AND coll_ov IS NULL
            GROUP BY prd_id, prdtm_rl_id
            ORDER BY prdtm_rl_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring production team roles for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['prdtm_sb_rl']) {$prdtm_rl=$row['prdtm_sb_rl'];} else {$prdtm_rl=$row['prdtm_rl'];}
        if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
        $prds[$row['prd_id']]['prdtm_rls'][$row['prdtm_rl_id']]=array('prdtm_rl'=>html($prdtm_rl), 'comp_nm'=>$comp_nm, 'co_comp_ppl'=>array(), 'co_ppl'=>array());
      }

      $sql= "SELECT prd_id, ppt1.prdtm_rlid, ppt1.prdtm_compid, prsn_fll_nm
            FROM prdprdtm ppt1
            INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
            INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
            INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
            WHERE ppt1.prdtm_prsnid='$prsn_id' AND ppt2.prdtm_compid!=0
            AND ppt1.prdtm_compid=ppt2.prdtm_compid AND ppt1.prdtm_prsnid!=ppt2.prdtm_prsnid AND coll_ov IS NULL
            GROUP BY prd_id, prdtm_compid, prsn_id
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
            WHERE ppt1.prdtm_prsnid='$prsn_id' AND ppt2.prdtm_prsnid=0
            AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NULL
            GROUP BY prd_id, comp_id
            UNION
            SELECT prd_id, ppt1.prdtm_rlid, ppt2.prdtm_ordr, prsn_id, prsn_fll_nm
            FROM prdprdtm ppt1
            INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
            INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
            INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
            WHERE ppt1.prdtm_prsnid='$prsn_id' AND ppt2.prdtm_compid=0
            AND ppt1.prdtm_prsnid!=ppt2.prdtm_prsnid AND coll_ov IS NULL
            GROUP BY prd_id, prsn_id
            ORDER BY prdtm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring co-credited production team (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['prdtm_rls'][$row['prdtm_rlid']]['co_ppl'][$row['comp_id']]=array('co_prsn'=>html($row['comp_nm']), 'comp_ppl'=>array());}

      $sql= "SELECT prd_id, ppt1.prdtm_rlid, ppt2.prdtm_compid, prsn_fll_nm
            FROM prdprdtm ppt1
            INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
            INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
            INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
            WHERE ppt1.prdtm_prsnid='$prsn_id' AND ppt2.prdtm_compid!=0
            AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NULL
            GROUP BY prd_id, prdtm_compid, prsn_id
            ORDER BY ppt2.prdtm_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring co-credited production team (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$prds[$row['prd_id']]['prdtm_rls'][$row['prdtm_rlid']]['co_ppl'][$row['prdtm_compid']]['comp_ppl'][]=html($row['prsn_fll_nm']);}

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdprdtm
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE prdtm_prsnid='$prsn_id' AND coll_ov IS NOT NULL
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

        $sql= "SELECT coll_ov, prd_id, prdtm_rl_id, prdtm_sb_rl, comp_nm, prdtm_rl
              FROM prdprdtm ppt
              LEFT OUTER JOIN comp ON prdtm_compid=comp_id INNER JOIN prdprdtmrl pptr ON ppt.prdid=pptr.prdid AND prdtm_rlid=prdtm_rl_id
              INNER JOIN prd ON ppt.prdid=prd_id
              WHERE prdtm_prsnid='$prsn_id' AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdtm_rl_id
              ORDER BY prdtm_rl_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring production team roles for production segments: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['prdtm_sb_rl']) {$prdtm_rl=$row['prdtm_sb_rl'];} else {$prdtm_rl=$row['prdtm_rl'];}
          if($row['comp_nm']) {$comp_nm=' for '.html($row['comp_nm']);} else {$comp_nm='';}
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prdtm_rls'][$row['prdtm_rl_id']]=array('prdtm_rl'=>html($prdtm_rl), 'comp_nm'=>$comp_nm, 'co_comp_ppl'=>array(), 'co_ppl'=>array());
        }

        $sql= "SELECT coll_ov, prd_id, ppt1.prdtm_rlid, ppt1.prdtm_compid, prsn_fll_nm
              FROM prdprdtm ppt1
              INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
              INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
              INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
              WHERE ppt1.prdtm_prsnid='$prsn_id' AND ppt2.prdtm_compid!=0
              AND ppt1.prdtm_compid=ppt2.prdtm_compid AND ppt1.prdtm_prsnid!=ppt2.prdtm_prsnid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, prdtm_compid, prsn_id
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
              WHERE ppt1.prdtm_prsnid='$prsn_id' AND ppt2.prdtm_prsnid=0 AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, comp_id
              UNION
              SELECT coll_ov, prd_id, ppt1.prdtm_rlid, ppt2.prdtm_ordr, prsn_id, prsn_fll_nm
              FROM prdprdtm ppt1
              INNER JOIN prdprdtmrl pptr ON ppt1.prdid=pptr.prdid AND ppt1.prdtm_rlid=prdtm_rl_id
              INNER JOIN prdprdtm ppt2 ON pptr.prdid=ppt2.prdid AND prdtm_rl_id=ppt2.prdtm_rlid
              INNER JOIN prsn ON ppt2.prdtm_prsnid=prsn_id INNER JOIN prd ON ppt1.prdid=prd_id
              WHERE ppt1.prdtm_prsnid='$prsn_id' AND ppt2.prdtm_compid=0 AND ppt1.prdtm_prsnid!=ppt2.prdtm_prsnid AND coll_ov IS NOT NULL
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
              WHERE ppt1.prdtm_prsnid='$prsn_id' AND ppt2.prdtm_compid!=0 AND ppt1.prdtm_compid!=ppt2.prdtm_compid AND coll_ov IS NOT NULL
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
    $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
          FROM prdrvw
          INNER JOIN prd p1 ON prdid=p1.prd_id INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id
          WHERE rvw_crtc_prsnid='$prsn_id' AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW()
          GROUP BY prd_id
          UNION
          SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
          FROM prdrvw
          INNER JOIN prd p1 ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
          WHERE rvw_crtc_prsnid='$prsn_id' AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW() AND coll_ov IS NULL
          GROUP BY prd_id
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (critic) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $rvw_crtc_prd_ids[]=$row['prd_id'];
        $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'rvws'=>array(), 'sg_prds'=>array());
      }

      if(!empty($rvw_crtc_prd_ids))
      {
        foreach($rvw_crtc_prd_ids as $prd_id)
        {
          $sql="SELECT 1 FROM prdrvw WHERE prdid='$prd_id' AND rvw_crtc_prsnid='$prsn_id'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for prd_ids directly credited to this person (as review critic): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv_tr.inc.php';
          }
        }
      }

      $sql= "SELECT prd_id, comp_nm, comp_url, DATE_FORMAT(rvw_dt, '%d %b %Y') AS rvw_dt_dsply, rvw_url
            FROM prdrvw
            INNER JOIN comp ON rvw_pub_compid=comp_id INNER JOIN prd ON prdid=prd_id
            WHERE rvw_crtc_prsnid='$prsn_id' AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW() AND coll_ov IS NULL
            GROUP BY prd_id, rvw_url
            ORDER BY rvw_dt ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring reviews for (critic) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_url']) {$comp_nm='<a href="/company/'.$row['comp_url'].'">'.$row['comp_nm'].'</a>';} else {$comp_nm='';}
        $rvw_url='<a href="'.html($row['rvw_url']).'" target="'.html($row['rvw_url']).'">review link</a>';
        $prds[$row['prd_id']]['rvws'][]=array('comp_nm'=>$comp_nm, 'rvw_dt'=>html($row['rvw_dt_dsply']), 'rvw_url'=>$rvw_url);
      }

      $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, thtr_fll_nm
            FROM prdrvw
            INNER JOIN prd ON prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE rvw_crtc_prsnid='$prsn_id' AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW() AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id
            ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment production data for (critic) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $rvw_crtc_sg_prd_ids[]=$row['prd_id'];
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'rvws'=>array());
        }

        if(!empty($rvw_crtc_sg_prd_ids))
        {
          foreach($rvw_crtc_sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv_tr.inc.php';
          }
        }

        $sql= "SELECT coll_ov, prd_id, comp_nm, comp_url, DATE_FORMAT(rvw_dt, '%d %b %Y') AS rvw_dt_dsply, rvw_url
              FROM prdrvw
              INNER JOIN comp ON rvw_pub_compid=comp_id INNER JOIN prd ON prdid=prd_id
              WHERE rvw_crtc_prsnid='$prsn_id' AND rvw_dt >= DATE_SUB(NOW(), INTERVAL 365 DAY) AND rvw_dt <= NOW() AND coll_ov IS NOT NULL
              GROUP BY coll_ov, prd_id, rvw_url
              ORDER BY rvw_dt ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring reviews for (critic) production segments: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['comp_url']) {$comp_nm='<a href="/company/'.$row['comp_url'].'">'.$row['comp_nm'].'</a>';} else {$comp_nm='';}
          $rvw_url='<a href="'.html($row['rvw_url']).'" target="'.html($row['rvw_url']).'">review link</a>';
          $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['rvws'][]=array('comp_nm'=>$comp_nm, 'rvw_dt'=>html($row['rvw_dt_dsply']), 'rvw_url'=>$rvw_url);
        }
      }
      $rvw_crtc_prds=$prds;
    }

    $sql= "SELECT crsid, c1.comp_nm AS crs_schl_nm, c1.comp_url, crs_typ_nm, crs_typ_url, crs_yr_strt, crs_yr_end, crs_yr_url, c2.comp_nm AS comp_nm, DATE_FORMAT(crs_dt_strt, '%d %b %Y') AS crs_dt_strt_dsply, DATE_FORMAT(crs_dt_end, '%d %b %Y') AS crs_dt_end_dsply
          FROM crscdntr cc
          INNER JOIN crs ON cc.crsid=crs_id INNER JOIN comp c1 ON crs_schlid=c1.comp_id
          INNER JOIN crs_typ ON crs_typid=crs_typ_id LEFT OUTER JOIN comp c2 ON cdntr_compid=c2.comp_id
          WHERE cdntr_prsnid='$prsn_id'
          ORDER BY crs_dt_end DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (coordinator) courses: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].' - '.$row['crs_yr_end'];} else {$crs_yr=$row['crs_yr_strt'];}
        if($row['crs_dt_strt_dsply'] && $row['crs_dt_end_dsply']) {$crs_dts=$row['crs_dt_strt_dsply'].' - '.$row['crs_dt_end_dsply'];} else {$crs_dts=$crs_yr;}
        $crs_nm='<a href="/course/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'/'.html($row['crs_yr_url']).'">'.html($row['crs_schl_nm']).': '.html($row['crs_typ_nm']).' '.$crs_yr.'</a>';
        if($row['comp_nm']) {$comp_nm=' for '.$row['comp_nm'];} else {$comp_nm='';}
        $cdntr_crss[$row['crsid']]=array('comp_nm'=>html($comp_nm), 'crs_nm'=>$crs_nm, 'crs_dts'=>html($crs_dts), 'cdntr_rls'=>array(), 'co_ppl'=>array(), 'comprl_co_ppl'=>array());
      }

      $sql= "SELECT cc.crsid, cdntr_sb_rl, cdntr_rl
            FROM crscdntr cc
            INNER JOIN crscdntrrl ccr ON cc.crsid=ccr.crsid AND cdntr_rlid=cdntr_rl_id
            WHERE cdntr_prsnid='$prsn_id' AND cc.cdntr_compid=0
            UNION
            SELECT cc.crsid, cdntr_sb_rl, cdntr_comprl
            FROM crscdntr cc
            INNER JOIN crscdntr_comprl cccr ON cc.crsid=cccr.crsid AND cdntr_comp_rlid=cdntr_comp_rl_id
            WHERE cdntr_prsnid='$prsn_id' AND cc.cdntr_compid!=0";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring credits for (coordinator) courses: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['cdntr_sb_rl']) {$cdntr_rl=$row['cdntr_sb_rl'];} else {$cdntr_rl=$row['cdntr_rl'];}
        $cdntr_crss[$row['crsid']]['cdntr_rls'][]=html($cdntr_rl);
      }

      $sql= "SELECT cc1.crsid, cc2.cdntr_ordr, comp_nm
            FROM crscdntr cc1
            INNER JOIN crscdntrrl ccr ON cc1.crsid=ccr.crsid AND cc1.cdntr_rlid=cdntr_rl_id
            INNER JOIN crscdntr cc2 ON ccr.crsid=cc2.crsid AND cdntr_rl_id=cc2.cdntr_rlid
            INNER JOIN comp ON cc2.cdntr_compid=comp_id
            WHERE cc1.cdntr_prsnid='$prsn_id' AND cc1.cdntr_compid=0 AND cc2.cdntr_prsnid=0 AND cc1.cdntr_compid!=cc2.cdntr_compid
            GROUP BY crsid, comp_id
            UNION
            SELECT cc1.crsid, cc2.cdntr_ordr, prsn_fll_nm
            FROM crscdntr cc1
            INNER JOIN crscdntrrl ccr ON cc1.crsid=ccr.crsid AND cc1.cdntr_rlid=cdntr_rl_id
            INNER JOIN crscdntr cc2 ON ccr.crsid=cc2.crsid AND cdntr_rl_id=cc2.cdntr_rlid
            INNER JOIN prsn ON cc2.cdntr_prsnid=prsn_id
            WHERE cc1.cdntr_prsnid='$prsn_id' AND cc1.cdntr_compid=0 AND cc2.cdntr_compid=0 AND cc1.cdntr_prsnid!=cc2.cdntr_prsnid
            GROUP BY crsid, prsn_id
            ORDER BY cdntr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-credited coordinator (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$cdntr_crss[$row['crsid']]['co_ppl'][]=html($row['comp_nm']);}

      $sql= "SELECT cc1.crsid, prsn_fll_nm
            FROM crscdntr cc1
            INNER JOIN crscdntr_comprl cccr ON cc1.crsid=cccr.crsid AND cc1.cdntr_comp_rlid=cdntr_comp_rl_id
            INNER JOIN crscdntr cc2 ON cccr.crsid=cc2.crsid AND cdntr_comp_rl_id=cc2.cdntr_comp_rlid
            INNER JOIN prsn ON cc2.cdntr_prsnid=prsn_id
            WHERE cc1.cdntr_prsnid='$prsn_id' AND cc1.cdntr_prsnid!=cc2.cdntr_prsnid
            GROUP BY crsid, prsn_id
            ORDER BY cc2.cdntr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring people who share company role credits for (coordinator) productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$cdntr_crss[$row['crsid']]['comprl_co_ppl'][]=html($row['prsn_fll_nm']);}
    }

    $sql= "SELECT comp_nm, comp_url, crs_typ_nm, crs_typ_url, crs_yr_strt, crs_yr_end, crs_yr_url, DATE_FORMAT(crs_dt_strt, '%d %b %Y') AS crs_dt_strt_dsply, DATE_FORMAT(crs_dt_end, '%d %b %Y') AS crs_dt_end_dsply, stff_prsn_rl
          FROM crsstff_prsn ccsp
          INNER JOIN crs ON ccsp.crsid=crs_id INNER JOIN comp ON crs_schlid=comp_id INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE stff_prsnid='$prsn_id'
          ORDER BY crs_dt_end DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (staff) courses: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['crs_yr_strt']!==$row['crs_yr_end']) {$crs_yr=$row['crs_yr_strt'].' - '.$row['crs_yr_end'];} else {$crs_yr=$row['crs_yr_strt'];}
      if($row['crs_dt_strt_dsply'] && $row['crs_dt_end_dsply']) {$crs_dts=$row['crs_dt_strt_dsply'].' - '.$row['crs_dt_end_dsply'];}
      else {$crs_dts=$crs_yr;}
      $crs_nm='<a href="/course/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'/'.html($row['crs_yr_url']).'">'.html($row['comp_nm']).': '.html($row['crs_typ_nm']).' '.$crs_yr.'</a>';
      $stff_prsn_crss[]=array('crs_nm'=>$crs_nm, 'crs_dts'=>html($crs_dts), 'stff_prsn_rl'=>html($row['stff_prsn_rl']));
    }

    $sql= "SELECT comp_nm, comp_url, crs_typ_nm, crs_typ_url, crs_yr_strt, crs_yr_end, crs_yr_url, DATE_FORMAT(crs_dt_strt, '%d %b %Y') AS crs_dt_strt_dsply, DATE_FORMAT(crs_dt_end, '%d %b %Y') AS crs_dt_end_dsply, stdnt_prsn_rl
          FROM crsstdnt_prsn ccsp
          INNER JOIN crs ON ccsp.crsid=crs_id INNER JOIN comp ON crs_schlid=comp_id INNER JOIN crs_typ ON crs_typid=crs_typ_id
          WHERE stdnt_prsnid='$prsn_id'
          ORDER BY crs_dt_end DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring (student) courses: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['crs_yr_strt']!==$row['crs_yr_end'])
      {
        $crs_yr=$row['crs_yr_strt'].' - '.$row['crs_yr_end'];
        $crs_yr_nm_dsply=$row['crs_yr_strt'].preg_replace('/([0-9]{2})([0-9]{2})$/', '-$2', $row['crs_yr_end']);
      }
      else
      {$crs_yr=$row['crs_yr_strt']; $crs_yr_nm_dsply=$row['crs_yr_strt'];}
      if($row['crs_dt_strt_dsply'] && $row['crs_dt_end_dsply']) {$crs_dts=$row['crs_dt_strt_dsply'].' - '.$row['crs_dt_end_dsply'];}
      else {$crs_dts=$crs_yr;}
      $crs_nm='<a href="/course/'.html($row['comp_url']).'/'.html($row['crs_typ_url']).'/'.html($row['crs_yr_url']).'">'.html($row['comp_nm']).': '.html($row['crs_typ_nm']).' ('.$crs_yr_nm_dsply.')</a>';
      $stdnt_prsn_crss[]=array('crs_nm'=>$crs_nm, 'crs_dts'=>html($crs_dts), 'stdnt_prsn_rl'=>html($row['stdnt_prsn_rl']));
    }

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, p2.pt_pub_dt, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE wri_prsnid='$prsn_id' AND org_wri=0 AND src_wri=0 AND grntr=0
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, pt_pub_dt, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE wri_prsnid='$prsn_id' AND org_wri=0 AND src_wri=0 AND grntr=0 AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_pub_dt DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext (as writer) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
          $sql="SELECT 1 FROM ptwri WHERE ptid='$pt_id' AND wri_prsnid='$prsn_id' AND org_wri=0 AND src_wri=0 AND grntr=0";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this person (as writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwri pw
            INNER JOIN pt ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE wri_prsnid='$prsn_id' AND org_wri=0 AND src_wri=0 AND grntr=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_ordr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for playtexts (as writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
      $wri_pts=$pts;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, p2.pt_pub_dt, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE wri_prsnid='$prsn_id' AND org_wri=1
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, pt_pub_dt, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE wri_prsnid='$prsn_id' AND org_wri=1 AND src_wri=0 AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_pub_dt DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtext (as original writer) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
          $sql="SELECT 1 FROM ptwri WHERE ptid='$pt_id' AND wri_prsnid='$prsn_id' AND org_wri=1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this person (as original writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwri pw
            INNER JOIN pt ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE wri_prsnid='$prsn_id' AND org_wri=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_ordr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for (original writer) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
      $org_wri_pts=$pts;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, p2.pt_pub_dt, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE wri_prsnid='$prsn_id' AND src_wri=1
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, pt_pub_dt, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE wri_prsnid='$prsn_id' AND org_wri=0 AND src_wri=1 AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM ptwri WHERE ptid='$pt_id' AND wri_prsnid='$prsn_id' AND src_wri=1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this person (as source writer): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwri pw
            INNER JOIN pt ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE wri_prsnid='$prsn_id' AND src_wri=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_ordr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for (source writer) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
      $src_wri_pts=$pts;
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE wri_prsnid='$prsn_id' AND grntr=1
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptwri pw
          INNER JOIN pt p1 ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE wri_prsnid='$prsn_id' AND grntr=1 AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM ptwri WHERE ptid='$pt_id' AND wri_prsnid='$prsn_id' AND grntr=1 LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this person (as grantor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptwri pw
            INNER JOIN pt ON pw.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE wri_prsnid='$prsn_id' AND grntr=1 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_ordr DESC";
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
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptcntr pc
          INNER JOIN pt p1 ON pc.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE cntr_prsnid='$prsn_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptcntr pc
          INNER JOIN pt p1 ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE cntr_prsnid='$prsn_id' AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM ptcntr WHERE ptid='$pt_id' AND cntr_prsnid='$prsn_id' LIMIT 1";
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

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm
            FROM ptcntr pc
            INNER JOIN pt ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE cntr_prsnid='$prsn_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_ordr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring segment playtext data for (contributor) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $cntr_sg_pt_ids[]=$row['pt_id'];
          $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'cntr_rls'=>array());
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

    $sql= "SELECT comp_nm, comp_url, compprsn_rl, compprsn_rl_nt, compprsn_yr_strt, compprsn_yr_end, COALESCE(comp_alph, comp_nm)comp_alph
          FROM compprsn
          INNER JOIN comp ON compid=comp_id
          WHERE prsnid='$prsn_id'
          ORDER BY compprsn_yr_strt DESC, comp_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring companies (of which person is a member): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['compprsn_rl_nt']) {$comp_prsn_rl_nt=' <em>('.html($row['compprsn_rl_nt']).')</em>';} else {$comp_prsn_rl_nt='';}
      if($row['compprsn_yr_strt']) {$comp_prsn_yr_strt=$row['compprsn_yr_strt'];} else {$comp_prsn_yr_strt=NULL;}
      if($row['compprsn_yr_end']) {if($row['compprsn_yr_end']==$row['compprsn_yr_strt']) {$comp_prsn_yr_end='';} else {$comp_prsn_yr_end=' - '.$row['compprsn_yr_end'];}}
      else {if($row['compprsn_yr_strt']) {$comp_prsn_yr_end=' - TBC';} else {$comp_prsn_yr_end='';}}
      if($comp_prsn_yr_strt || $comp_prsn_yr_end) {$comp_prsn_yrs=' ('.html($comp_prsn_yr_strt.$comp_prsn_yr_end).')';} else {$comp_prsn_yrs='';}
      $comp_nm='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';
      $comps[]=array('comp_nm'=>$comp_nm, 'comp_prsn_yrs'=>$comp_prsn_yrs, 'comp_prsn_rl'=>html($row['compprsn_rl']), 'comp_prsn_rl_nt'=>html($comp_prsn_rl_nt));
    }

    $sql= "SELECT comp_id, comp_nm, comp_url, agnt_rl, agnt_ordr, comp_bool
          FROM prsnagnt
          INNER JOIN comp ON agnt_compid=comp_id
          WHERE prsnid='$prsn_id' AND agnt_prsnid=0
          UNION
          SELECT prsn_id, prsn_fll_nm, prsn_url, agnt_rl, agnt_ordr, comp_bool
          FROM prsnagnt
          INNER JOIN prsn ON agnt_prsnid=prsn_id
          WHERE prsnid='$prsn_id' AND agnt_compid=0
          ORDER BY agnt_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring agent data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$comp_nm='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';}
        else {$comp_nm=html($row['comp_nm']);}
        $agnts[$row['comp_id']]=array('comp_nm'=>$comp_nm, 'agnt_rl'=>html($row['agnt_rl']), 'agntcomp_ppl'=>array());
      }

      $sql= "SELECT agnt_compid, prsn_fll_nm, prsn_url, agnt_rl
            FROM prsnagnt
            INNER JOIN prsn ON agnt_prsnid=prsn_id
            WHERE prsnid='$prsn_id' AND agnt_compid!=0
            ORDER BY agnt_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring agent (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
        $agnts[$row['agnt_compid']]['agntcomp_ppl'][]=array('prsn_nm'=>$prsn_nm, 'agnt_rl'=>html($row['agnt_rl']));
      }
    }

    $sql= "SELECT prsn_fll_nm, prsn_url, comp_nm, comp_url, agnt_rl
          FROM prsnagnt
          INNER JOIN prsn ON prsnid=prsn_id
          LEFT OUTER JOIN comp ON agnt_compid=comp_id
          WHERE agnt_prsnid='$prsn_id'
          ORDER BY prsn_lst_nm ASC, prsn_frst_nm ASC, prsn_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring client (person) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_nm']) {$comp_nm='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';} else {$comp_nm='';}
      $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>';
      $clnts[]=array('prsn_nm'=>$prsn_nm, 'comp_nm'=>$comp_nm, 'agnt_rl'=>html($row['agnt_rl']));
    }

    $pts=array();
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, NULL AS lcnsr_rl, NULL AS comp_nm, NULL AS comp_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm, p2.pt_coll, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptlcnsr pl
          INNER JOIN pt p1 ON pl.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE lcnsr_prsnid='$prsn_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, lcnsr_rl, comp_nm, comp_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll, COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptlcnsr pl
          INNER JOIN pt p1 ON pl.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id LEFT OUTER JOIN comp ON lcnsr_compid=comp_id
          WHERE lcnsr_prsnid='$prsn_id' AND coll_ov IS NULL
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
        if($row['comp_nm']) {$comp_nm=' (<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>)';} else {$comp_nm='';}
        $lcnsr_pt_ids[]=$row['pt_id'];
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'comp_nm'=>$comp_nm, 'lcnsr_rl'=>html($row['lcnsr_rl']), 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      if(!empty($lcnsr_pt_ids))
      {
        foreach($lcnsr_pt_ids as $pt_id)
        {
          $sql="SELECT 1 FROM ptlcnsr WHERE ptid='$pt_id' AND lcnsr_prsnid='$prsn_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this person (as licensor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, lcnsr_rl, comp_nm, comp_url, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM ptlcnsr pl
            INNER JOIN pt ON pl.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id LEFT OUTER JOIN comp ON lcnsr_compid=comp_id
            WHERE lcnsr_prsnid='$prsn_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_ordr DESC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data (as licensor): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['comp_nm']) {$comp_nm=' (<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>)';} else {$comp_nm='';}
        $lcnsr_sg_pt_ids[]=$row['pt_id'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'comp_nm'=>$comp_nm, 'lcnsr_rl'=>html($row['lcnsr_rl']), 'wri_rls'=>array());
      }

      if(!empty($lcnsr_sg_pt_ids))
      {
        foreach($lcnsr_sg_pt_ids as $sg_pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
        }
      }
      $lcnsr_pts=$pts;
    }

    $awrds_ttl_wins=array(); $awrds_ttl_noms=array();
    $sql= "SELECT awrds_id, awrds_nm, awrds_url, COALESCE(awrds_alph, awrds_nm)awrds_alph
          FROM awrdnomppl
          INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id
          WHERE nom_prsnid='$prsn_id' GROUP BY awrds_id ORDER BY awrds_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$awrds[$row['awrds_id']]=array('awrds_nm'=>html($row['awrds_nm']), 'awrd_yrs'=>array(), 'awrd_wins'=>array(), 'awrd_noms'=>array());}

      $sql= "SELECT awrds_id, awrd_id, awrd_yr, awrd_yr_end, awrd_yr_url, awrds_url
            FROM awrdnomppl
            INNER JOIN awrd ON awrdid=awrd_id INNER JOIN awrds ON awrdsid=awrds_id
            WHERE nom_prsnid='$prsn_id' GROUP BY awrds_id, awrd_id ORDER BY awrd_yr DESC";
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
            WHERE nom_prsnid='$prsn_id' GROUP BY awrdsid, awrd_id, awrd_ctgry_id ORDER BY awrd_ctgry_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgry_id']]=array('awrd_ctgry_nm'=>html($row['awrd_ctgry_nm']), 'noms'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, nom_id, nom_win_dscr, win_bool, nom_rl, comp_url, comp_nm
            FROM awrdnomppl anp
            INNER JOIN awrd ON anp.awrdid=awrd_id INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND anp.nomid=nom_id
            LEFT OUTER JOIN comp ON anp.nom_compid=comp_id
            WHERE nom_prsnid='$prsn_id' GROUP BY awrdsid, awrd_id, awrd_ctgryid, nom_id ORDER BY nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award nominations data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        if($row['comp_nm']) {$comp_nm='<a href="/company/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>';} else {$comp_nm='';}
        if($row['win_bool']) {$awrds_ttl_wins[]=1; $awrds[$row['awrdsid']]['awrd_wins'][]=1; $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_wins'][]=1;}
        else {$awrds_ttl_noms[]=1; $awrds[$row['awrdsid']]['awrd_noms'][]=1; $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['awrd_yr_noms'][]=1;}
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nom_id']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'win'=>$row['win_bool'], 'nom_rl'=>$nom_rl, 'comp_nm'=>$comp_nm, 'nom_co_comp_ppl'=>array(), 'co_nomppl'=>array(), 'nomprds'=>array(), 'nompts'=>array(), 'cowins'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, anp2.nom_rl, anp2.nom_compid, prsn_fll_nm, prsn_url FROM awrdnomppl anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnomppl anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid
            AND anp1.nom_compid=anp2.nom_compid AND anp1.nom_prsnid!=anp2.nom_prsnid
            INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
            WHERE anp1.nom_prsnid='$prsn_id' AND anp1.nom_compid!=0
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, nom_compid, prsn_id ORDER BY anp2.nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-nominee/winner (company people - of same company) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        $nom_co_comp_prsn='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nom_co_comp_ppl'][]=$nom_co_comp_prsn;
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, anp2.nom_ordr, anp2.nom_rl, comp_id, comp_nm, comp_url, comp_bool FROM awrdnomppl anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnomppl anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid AND anp1.nom_compid!=anp2.nom_compid
            INNER JOIN comp ON anp2.nom_compid=comp_id
            WHERE anp1.nom_prsnid='$prsn_id' AND anp2.nom_prsnid=0
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, comp_id
            UNION
            SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, anp2.nom_ordr, anp2.nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool FROM awrdnomppl anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnomppl anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid AND anp1.nom_prsnid!=anp2.nom_prsnid
            INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
            WHERE anp1.nom_prsnid='$prsn_id' AND anp2.nom_compid=0
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
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['co_nomppl'][$row['comp_id']]=array('nom_prsn'=>$nom_prsn, 'co_nomcomp_ppl'=>array());
      }

      $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid, anp2.nom_rl, anp2.nom_compid, prsn_fll_nm, prsn_url FROM awrdnomppl anp1
            INNER JOIN awrd ON anp1.awrdid=awrd_id
            INNER JOIN awrdnomppl anp2 ON anp1.awrdid=anp2.awrdid AND anp1.awrd_ctgryid=anp2.awrd_ctgryid AND anp1.nomid=anp2.nomid AND anp1.nom_compid!=anp2.nom_compid
            INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
            WHERE anp1.nom_prsnid='$prsn_id' AND anp2.nom_compid!=0
            GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, nom_compid, prsn_id ORDER BY anp2.nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards co-nominee/winner (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        $nomcomp_prsn='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['co_nomppl'][$row['nom_compid']]['co_nomcomp_ppl'][]=$nomcomp_prsn;
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, anp.nomid, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm FROM awrdnomppl anp
            INNER JOIN awrd ON anp.awrdid=awrd_id
            INNER JOIN awrdnomprds anprd ON anp.awrdid=anprd.awrdid AND anp.awrd_ctgryid=anprd.awrd_ctgryid AND anp.nomid=anprd.nomid
            INNER JOIN prd p ON nom_prdid=prd_id INNER JOIN thtr ON p.thtrid=thtr_id
            WHERE nom_prsnid='$prsn_id' GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (productions) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
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
            WHERE nom_prsnid='$prsn_id' GROUP BY awrdsid, awrd_id, awrd_ctgryid, nomid, pt_id ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (playtexts) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['nomid']]['nompts'][]=$pt_nm.' ('.$pt_yr.')';
      }

      $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, anp.nomid AS n1, an2.nom_id AS n2, an2.nom_win_dscr FROM awrdnomppl anp
            INNER JOIN awrd ON anp.awrdid=awrd_id
            INNER JOIN awrdnoms an1 ON anp.awrdid=an1.awrdid AND anp.awrd_ctgryid=an1.awrd_ctgryid AND anp.nomid=an1.nom_id
            INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
            WHERE anp.nom_prsnid='$prsn_id' AND an1.win_bool=1 AND an2.win_bool=1
            AND an2.nom_id NOT IN(SELECT nomid FROM awrdnomppl WHERE nom_prsnid='$prsn_id' AND awrdid=anp.awrdid)
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
              WHERE anp1.nom_prsnid='$prsn_id' AND an1.win_bool=1 AND an2.win_bool=1 AND anp2.nom_prsnid=0
              AND anp2.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE nom_prsnid='$prsn_id' AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, comp_id
              UNION
              SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, anp2.nomid AS n2, anp2.nom_ordr, anp2.nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool
              FROM awrdnomppl anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid AND an2.nom_id=anp2.nomid
              INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
              WHERE anp1.nom_prsnid='$prsn_id' AND an1.win_bool=1 AND an2.win_bool=1 AND anp2.nom_compid=0
              AND anp2.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE nom_prsnid='$prsn_id' AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n2, n2, prsn_id
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

        $sql= "SELECT awrdsid, awrd_id, anp1.awrd_ctgryid, anp1.nomid AS n1, anp2.nomid AS n2, anp2.nom_compid, anp2.nom_ordr, anp2.nom_rl, prsn_id, prsn_fll_nm, prsn_url
              FROM awrdnomppl anp1
              INNER JOIN awrd ON anp1.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp1.awrdid=an1.awrdid AND anp1.awrd_ctgryid=an1.awrd_ctgryid AND anp1.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomppl anp2 ON an2.awrdid=anp2.awrdid AND an2.awrd_ctgryid=anp2.awrd_ctgryid AND an2.nom_id=anp2.nomid
              INNER JOIN prsn ON anp2.nom_prsnid=prsn_id
              WHERE anp1.nom_prsnid='$prsn_id' AND an1.win_bool=1 AND an2.win_bool=1 AND anp2.nom_compid!=0
              AND anp2.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE nom_prsnid='$prsn_id' AND awrdid=anp1.awrdid AND awrd_ctgryid=anp1.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, nom_compid, prsn_id ORDER BY nom_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards company people data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
          $cowincomp_prsn='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_ppl'][$row['nom_compid']]['cowincomp_ppl'][]=$cowincomp_prsn;
        }

        $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, anp.nomid AS n1, anprd.nomid AS n2, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm
              FROM awrdnomppl anp
              INNER JOIN awrd ON anp.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp.awrdid=an1.awrdid AND anp.awrd_ctgryid=an1.awrd_ctgryid AND anp.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnomprds anprd ON an2.awrdid=anprd.awrdid AND an2.awrd_ctgryid=anprd.awrd_ctgryid AND an2.nom_id=anprd.nomid
              INNER JOIN prd p ON nom_prdid=prd_id
              INNER JOIN thtr ON p.thtrid=thtr_id
              WHERE anp.nom_prsnid='$prsn_id' AND an1.win_bool=1 AND an2.win_bool=1
              AND anprd.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE nom_prsnid='$prsn_id' AND awrdid=anp.awrdid AND awrd_ctgryid=anp.awrd_ctgryid)
              GROUP BY awrdsid, awrd_id, awrd_ctgryid, n1, n2, prd_id ORDER BY prd_frst_dt DESC, prd_alph ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (co-winner) awards (productions) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $awrds[$row['awrdsid']]['awrd_yrs'][$row['awrd_id']]['ctgrys'][$row['awrd_ctgryid']]['noms'][$row['n1']]['cowins'][$row['n2']]['cowin_prds'][]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
        }

        $sql= "SELECT awrdsid, awrd_id, anp.awrd_ctgryid, anp.nomid AS n1, anpt.nomid AS n2, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph
              FROM awrdnomppl anp
              INNER JOIN awrd ON anp.awrdid=awrd_id
              INNER JOIN awrdnoms an1 ON anp.awrdid=an1.awrdid AND anp.awrd_ctgryid=an1.awrd_ctgryid AND anp.nomid=an1.nom_id
              INNER JOIN awrdnoms an2 ON an1.awrdid=an2.awrdid AND an1.awrd_ctgryid=an2.awrd_ctgryid
              INNER JOIN awrdnompts anpt ON an2.awrdid=anpt.awrdid AND an2.awrd_ctgryid=anpt.awrd_ctgryid AND an2.nom_id=anpt.nomid
              INNER JOIN pt ON nom_ptid=pt_id
              WHERE anp.nom_prsnid='$prsn_id' AND an1.win_bool=1 AND an2.win_bool=1
              AND anpt.nomid NOT IN(SELECT nomid FROM awrdnomppl WHERE nom_prsnid='$prsn_id' AND awrdid=anp.awrdid AND awrd_ctgryid=anp.awrd_ctgryid)
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

    $prsn_id=html($prsn_id);
    include 'person.html.php';
  }
?>