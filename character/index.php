<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $char_id=cln($_POST['char_id']);
    $sql="SELECT char_nm, char_sffx_num, char_lnk, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti FROM role WHERE char_id='$char_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['char_sffx_num']) {$char_sffx_num=html($row['char_sffx_num']); $char_sffx_rmn=' ('.romannumeral($row['char_sffx_num']).')';}
    else {$char_sffx_num=''; $char_sffx_rmn='';}
    $pagetab='Edit: '.html($row['char_nm'].$char_sffx_rmn);
    $pagetitle=html($row['char_nm'].$char_sffx_rmn);
    $char_nm=html($row['char_nm']);
    $char_lnk=html($row['char_lnk']);
    $char_sx=html($row['char_sx']);
    $char_age_frm=html($row['char_age_frm']);
    $char_age_to=html($row['char_age_to']);
    $char_dscr=html($row['char_dscr']);
    $char_amnt=html($row['char_amnt']);
    $char_mlti=$row['char_mlti'];

    $sql="SELECT ethn_nm FROM charethn INNER JOIN ethn ON ethnid=ethn_id WHERE charid='$char_id' ORDER BY ethn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$ethns[]=$row['ethn_nm'];}
    if(!empty($ethns)) {$ethn_list=html(implode(',,', $ethns));} else {$ethn_list='';}

    $sql= "SELECT lctn_id, lctn_nm, lctn_sffx_num FROM charorg_lctn INNER JOIN lctn ON org_lctnid=lctn_id WHERE charid='$char_id' ORDER BY org_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$org_lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$org_lctn_sffx_num='';}
      $org_lctns[$row['lctn_id']]=array('lctn'=>$row['lctn_nm'].$org_lctn_sffx_num, 'lctn_alts'=>array());
    }

    $sql= "SELECT col.org_lctnid, lctn_nm, lctn_sffx_num
          FROM charorg_lctn col
          INNER JOIN rel_lctn ON col.org_lctnid=rel_lctn1 INNER JOIN charorg_lctn_alt cola ON rel_lctn2=cola.org_lctn_altid INNER JOIN lctn ON cola.org_lctn_altid=lctn_id
          WHERE col.charid='$char_id' AND col.charid=cola.charid AND col.org_lctnid=cola.org_lctnid
          ORDER BY rel_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring alternate place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['lctn_sffx_num']) {$lctn_sffx_num='--'.$row['lctn_sffx_num'];} else {$lctn_sffx_num='';}
      $org_lctns[$row['org_lctnid']]['lctn_alts'][]=$row['lctn_nm'].$lctn_sffx_num;
    }

    if(!empty($org_lctns))
    {
      $org_lctn_array=array();
      foreach($org_lctns as $org_lctn)
      {
        if(!empty($org_lctn['lctn_alts'])) {$lctn_alt_list='||'.implode('>>', $org_lctn['lctn_alts']);} else {$lctn_alt_list='';}
        $org_lctn_array[]=$org_lctn['lctn'].$lctn_alt_list;
      }
      $org_lctn_list=html(implode(',,', $org_lctn_array));
    }
    else {$org_lctn_list='';}

    $sql= "SELECT prof_nm FROM charprof INNER JOIN prof ON profid=prof_id WHERE charid='$char_id' ORDER BY prof_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$profs[]=$row['prof_nm'];}
    if(!empty($profs)) {$prof_list=html(implode(',,', $profs));} else {$prof_list='';}

    $sql= "SELECT attr_nm FROM charattr INNER JOIN attr ON attrid=attr_id WHERE charid='$char_id' ORDER BY attr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring physical attribute data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$attrs[]=$row['attr_nm'];}
    if(!empty($attrs)) {$attr_list=html(implode(',,', $attrs));} else {$attr_list='';}

    $sql= "SELECT abil_nm FROM charabil INNER JOIN abil ON abilid=abil_id WHERE charid='$char_id' ORDER BY abil_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ability data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$abils[]=$row['abil_nm'];}
    if(!empty($abils)) {$abil_list=html(implode(',,', $abils));} else {$abil_list='';}

    $sql= "SELECT char_nm, char_sffx_num, COALESCE(char_alph, char_nm)char_alph FROM var_char INNER JOIN role ON var_char1=char_id WHERE var_char2='$char_id'
          UNION
          SELECT char_nm, char_sffx_num, COALESCE(char_alph, char_nm)char_alph FROM var_char INNER JOIN role ON var_char2=char_id WHERE var_char1='$char_id'
          ORDER BY char_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring variable character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['char_sffx_num']) {$var_char_sffx_num='--'.$row['char_sffx_num'];} else {$var_char_sffx_num='';}
      $var_char_nms[]=$row['char_nm'].$var_char_sffx_num;
    }
    if(!empty($var_char_nms)) {$var_char_list=html(implode(',,', $var_char_nms));} else {$var_char_list='';}

    $textarea='';
    $char_id=html($char_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $char_id=cln($_POST['char_id']);
    $char_nm=trim(cln($_POST['char_nm']));
    $char_sffx_num=trim(cln($_POST['char_sffx_num']));
    $char_lnk=trim(cln($_POST['char_lnk']));
    if($_POST['char_sx']=='1') {$char_sx='1';}
    if($_POST['char_sx']=='2') {$char_sx='2';}
    if($_POST['char_sx']=='3') {$char_sx='3';}
    if($_POST['char_sx']=='4') {$char_sx='4';}
    $char_age_frm=trim(cln($_POST['char_age_frm']));
    $char_age_to=trim(cln($_POST['char_age_to']));
    $char_dscr=trim(cln($_POST['char_dscr']));
    $char_amnt=trim(cln($_POST['char_amnt']));
    if(isset($_POST['char_mlti'])) {$char_mlti='1'; $char_amnt='0';} else {$char_mlti='0';}
    $ethn_list=cln($_POST['ethn_list']);
    $org_lctn_list=cln($_POST['org_lctn_list']);
    $prof_list=cln($_POST['prof_list']);
    $attr_list=cln($_POST['attr_list']);
    $abil_list=cln($_POST['abil_list']);
    $var_char_list=cln($_POST['var_char_list']);

    $char_nm_session=$_POST['char_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $char_nm))
    {$errors['char_nm']='**You must enter a character name.**';}
    elseif(preg_match('/--/', $char_nm) || preg_match('/::/', $char_nm) || preg_match('/,,/', $char_nm) || preg_match('/\[alt\]/', $char_nm))
    {$errors['char_nm']='</br>**Character name cannot include any of the following: [--], [::], [,,], [[alt]].**';}

    if(preg_match('/^[0]*$/', $char_sffx_num) || !$char_sffx_num)
    {
      $char_sffx_num='0'; $char_sffx_rmn='';
    }
    elseif(preg_match('/^[1-9][0-9]{0,5}$/', $char_sffx_num))
    {
      $char_sffx_rmn=' ('.romannumeral($char_sffx_num).')';
      $char_nm_session=$char_nm_session.' ('.romannumeral($_POST['char_sffx_num']).')';
    }
    else
    {
      $char_sffx_rmn='';
      $errors['char_sffx']='**The suffix must be a valid integer between 1 and 999,999 (with no leading 0) or left blank (or as 0).**';
    }

    $char_url=generateurl($char_nm.$char_sffx_rmn);

    if(strlen($char_nm)>255 || strlen($char_url)>255)
    {$errors['char_excss_lngth']='</br>**Character name and its URL are allowed a maximum of 255 characters each.**';}

    $char_alph=alph($char_nm);

    if(count($errors)==0)
    {
      $sql="SELECT char_id, char_nm, char_sffx_num FROM role WHERE char_url='$char_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing character URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['char_id']!==$char_id)
      {
        if($row['char_sffx_num']) {$char_sffx_rmn=' ('.romannumeral($row['char_sffx_num']).')';} else {$char_sffx_rmn='';}
        $errors['char_url']='</br>**Duplicate URL exists for: '.html($row['char_nm'].$char_sffx_rmn). '. You must keep the original name or assign a character name without an existing URL.**';
      }
    }

    if(strlen($char_lnk)>255) {$errors['char_lnk_excss_lngth']='</br>**Character link is allowed a maximum of 255 characters.**';}
    if(strlen($char_dscr)>255) {$errors['char_dscr_excss_lngth']='</br>**Character description is allowed a maximum of 255 characters.**';}

    if(!preg_match('/\S+/', $char_lnk)) {$char_lnk=$char_nm;}

    if(preg_match('/^[0]*$/', $char_age_frm) || !$char_age_frm)
    {$char_age_frm=NULL;}
    elseif(!preg_match('/^[1-9][0-9]{0,2}$/', $char_age_frm))
    {$errors['char_age_frm']='</br>**Age range (from) must be comprised of valid integers between 1 and 999 (with no leading 0) or left blank (or as 0).**';}

    if(preg_match('/^[0]*$/', $char_age_to) || !$char_age_to)
    {$char_age_to=NULL;}
    elseif(!preg_match('/^[1-9][0-9]{0,2}$/', $char_age_to))
    {$errors['char_age_to']='</br>**Age range (to) must be comprised of valid integers between 0 and 999 (with no leading 0) or left blank (or as 0).**';}

    if(($char_age_frm && !$char_age_to) || (!$char_age_frm && $char_age_to))
    {$errors['char_age_frm_to']='</br>**Age ranges must both be comprised of valid integers between 0 and 999 or both left blank (or as 0).**';}
    elseif(preg_match('/^[1-9][0-9]{0,2}$/', $char_age_to) && preg_match('/^[1-9][0-9]{0,2}$/', $char_age_frm) && $char_age_frm>$char_age_to)
    {$errors['char_age_frm_to']='</br>**Age range (from) must not be higher than age range (to).**';}

    if($char_amnt && !preg_match('/^[1-9][0-9]{0,1}$/', $char_amnt))
    {$errors['char_amnt']='</br>**The character amount must be a valid integer between 1 and 99 (with no leading 0).**';}

    if(preg_match('/\S+/', $ethn_list))
    {
      $ethns=explode(',,', $ethn_list);
      if(count($ethns)>250)
      {$errors['ethn_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $ethn_empty_err_arr=array(); $ethn_dplct_arr=array(); $ethn_url_err_arr=array();
        foreach($ethns as $ethn_nm)
        {
          $ethn_nm=trim($ethn_nm);
          if(!preg_match('/\S+/', $ethn_nm))
          {
            $ethn_empty_err_arr[]=$ethn_nm;
            if(count($ethn_empty_err_arr)==1) {$errors['ethn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['ethn_empty']='</br>**There are '.count($ethn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $ethn_url=generateurl($ethn_nm);

            $ethn_dplct_arr[]=$ethn_url;
            if(count(array_unique($ethn_dplct_arr))<count($ethn_dplct_arr))
            {$errors['ethn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($ethn_nm)>255)
            {$errors['ethn_excss_lngth']='</br>**Ethnicity name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

            $sql= "SELECT ethn_nm
                  FROM ethn
                  WHERE NOT EXISTS (SELECT 1 FROM ethn WHERE ethn_nm='$ethn_nm')
                  AND ethn_url='$ethn_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing ethnicity URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            $row=mysqli_fetch_array($result);
            if(mysqli_num_rows($result)>0)
            {
              $ethn_url_err_arr[]=$row['ethn_nm'];
              if(count($ethn_url_err_arr)==1)
              {$errors['ethn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $ethn_url_err_arr)).'?**';}
              else
              {$errors['ethn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $ethn_url_err_arr)).'?**';}
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $org_lctn_list))
    {
      $org_lctn_nms=explode(',,', $_POST['org_lctn_list']);
      if(count($org_lctn_nms)>250)
      {$errors['org_lctn_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $org_lctn_empty_err_arr=array(); $org_lctn_pipe_excss_err_arr=array(); $org_lctn_pipe_err_arr=array();
        $org_lctn_hyphn_excss_err_arr=array(); $org_lctn_sffx_err_arr=array(); $org_lctn_hyphn_err_arr=array();
        $org_lctn_dplct_arr=array(); $org_lctn_url_err_arr=array(); $org_lctn_alt_list_err_arr=array();
        $org_lctn_alt_empty_err_arr=array(); $org_lctn_alt_hyphn_excss_err_arr=array(); $org_lctn_alt_sffx_err_arr=array();
        $org_lctn_alt_hyphn_err_arr=array(); $org_lctn_alt_dplct_arr=array(); $org_lctn_alt_url_err_arr=array();
        $org_lctn_alt_err_arr=array(); $org_lctn_alt_no_assocs=array(); $org_lctn_alt_assoc_err_arr=array();
        foreach($org_lctn_nms as $org_lctn_nm)
        {
          $org_lctn_errors=0;

          $org_lctn_nm=trim($org_lctn_nm);
          if(!preg_match('/\S+/', $org_lctn_nm))
          {
            $org_lctn_empty_err_arr[]=$org_lctn_nm;
            if(count($org_lctn_empty_err_arr)==1) {$errors['org_lctn_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['org_lctn_empty']='</br>**There are '.count($org_lctn_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($org_lctn_nm, '||')>1) {$org_lctn_errors++; $org_lctn_pipe_excss_err_arr[]=$org_lctn_nm; $org_lctn_alt_list=''; $errors['org_lctn_pipe_excss']='</br>**You may only use [||] once per location for alternate location assignation. Please amend: '.html(implode(' / ', $org_lctn_pipe_excss_err_arr)).'.**';}
            elseif(preg_match('/\S+.*\|\|.*\S+/', $org_lctn_nm))
            {
              list($org_lctn_nm, $org_lctn_alt_list)=explode('||', $org_lctn_nm);
              $org_lctn_nm=trim($org_lctn_nm); $org_lctn_alt_list=trim($org_lctn_alt_list);
            }
            elseif(substr_count($org_lctn_nm, '||')==1) {$org_lctn_errors++; $org_lctn_pipe_err_arr[]=$org_lctn_nm; $org_lctn_alt_list=''; $errors['org_lctn_pipe']='</br>**Alternate location assignation must use [||] in the correct format. Please amend: '.html(implode(' / ', $org_lctn_pipe_err_arr)).'.**';}
            else {$org_lctn_alt_list='';}

            if(substr_count($org_lctn_nm, '--')>1)
            {
              $org_lctn_errors++; $org_lctn_sffx_num='0'; $org_lctn_hyphn_excss_err_arr[]=$org_lctn_nm;
              $errors['org_lctn_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per place of origin. Please amend: '.html(implode(' / ', $org_lctn_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $org_lctn_nm))
            {
              list($org_lctn_nm_no_sffx, $org_lctn_sffx_num)=explode('--', $org_lctn_nm);
              $org_lctn_nm_no_sffx=trim($org_lctn_nm_no_sffx); $org_lctn_sffx_num=trim($org_lctn_sffx_num);

              if(!preg_match('/^[1-9][0-9]{0,1}$/', $org_lctn_sffx_num))
              {
                $org_lctn_errors++; $org_lctn_sffx_num='0'; $org_lctn_sffx_err_arr[]=$org_lctn_nm;
                $errors['org_lctn_sffx']='</br>**The suffix must be a positive integer (between 1 and 99 (with no leading 0)). Please amend: '.html(implode(' / ', $org_lctn_sffx_err_arr)).'**';
              }
              $org_lctn_nm=$org_lctn_nm_no_sffx;
            }
            elseif(substr_count($org_lctn_nm, '--')==1)
            {$org_lctn_errors++; $org_lctn_sffx_num='0'; $org_lctn_hyphn_err_arr[]=$org_lctn_nm;
            $errors['org_lctn_hyphn']='</br>**Place of origin suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $org_lctn_hyphn_err_arr)).'**';}
            else
            {$org_lctn_sffx_num='0';}

            if($org_lctn_sffx_num) {$org_lctn_sffx_rmn=' ('.romannumeral($org_lctn_sffx_num).')';} else {$org_lctn_sffx_rmn='';}

            $org_lctn_url=generateurl($org_lctn_nm.$org_lctn_sffx_rmn);

            $org_lctn_dplct_arr[]=$org_lctn_url;
            if(count(array_unique($org_lctn_dplct_arr))<count($org_lctn_dplct_arr))
            {$errors['org_lctn_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($org_lctn_nm)>255 || strlen($org_lctn_url)>255)
            {$org_lctn_errors++; $errors['org_lctn_nm_excss_lngth']='</br>**Place of origin and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($org_lctn_errors==0)
            {
              $org_lctn_nm_cln=cln($org_lctn_nm);
              $org_lctn_sffx_num_cln=cln($org_lctn_sffx_num);
              $org_lctn_url_cln=cln($org_lctn_url);

              $sql= "SELECT lctn_nm, lctn_sffx_num
                    FROM lctn
                    WHERE NOT EXISTS (SELECT 1 FROM lctn WHERE lctn_nm='$org_lctn_nm_cln' AND lctn_sffx_num='$org_lctn_sffx_num_cln')
                    AND lctn_url='$org_lctn_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing place of origin URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['lctn_sffx_num']) {$org_lctn_url_err_sffx_num='--'.$row['lctn_sffx_num'];} else {$org_lctn_url_err_sffx_num='';}
                $org_lctn_url_err_arr[]=$row['lctn_nm'].$org_lctn_url_err_sffx_num;
                if(count($org_lctn_url_err_arr)==1) {$errors['org_lctn_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $org_lctn_url_err_arr)).'?**';}
                else {$errors['org_lctn_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $org_lctn_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT lctn_id, lctn_nm, lctn_sffx_num, lctn_url FROM lctn WHERE lctn_url='$org_lctn_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existence of location: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($org_lctn_alt_list)
                {
                  if(mysqli_num_rows($result)==0) {$org_lctn_alt_list_err_arr[]=$org_lctn_nm.$org_lctn_sffx_rmn; $errors['org_lctn_alt_list']='</br>**The following locations do not yet exist (and therefore cannot be assigned alternate locations): '.html(implode(' / ', $org_lctn_alt_list_err_arr)).'.**';}
                  else
                  {
                    $lctn_id=$row['lctn_id'];
                    if($row['lctn_sffx_num']) {$lctn_sffx_rmn_url_lnk=' ('.romannumeral($row['lctn_sffx_num']).')';} else {$lctn_sffx_rmn_url_lnk='';}
                    $lctn_url_lnk='<a href="/character/origin/'.html($row['lctn_url']).'" target="/character/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm'].$lctn_sffx_rmn_url_lnk).'</a>';

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
                              $sql="SELECT lctn_id FROM lctn WHERE lctn_url='$org_lctn_alt_url_cln'";
                              $result=mysqli_query($link, $sql);
                              if(!$result) {$error='Error checking for existence of location (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                              $row=mysqli_fetch_array($result);
                              if(mysqli_num_rows($result)==0) {$org_lctn_alt_err_arr[]=$org_lctn_alt.$org_lctn_alt_sffx_rmn; $errors['org_lctn_alt']='</br>**The following locations from alternate location arrays do not yet exist (and can therefore not be assigned): '.html(implode(' / ', $org_lctn_alt_err_arr)).'.';}
                              else
                              {
                                $lctn_alt_id=$row['lctn_id'];
                                $sql="SELECT 1 FROM rel_lctn WHERE rel_lctn1='$lctn_id' AND rel_lctn2='$lctn_alt_id'";
                                $result=mysqli_query($link, $sql);
                                if(!$result) {$error='Error checking for existing location URL (from alternate location arrays): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                                $row=mysqli_fetch_array($result);
                                if(mysqli_num_rows($result)==0) {$org_lctn_alt_no_assocs[]=$org_lctn_alt.$org_lctn_alt_sffx_rmn;}
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

          if(count($org_lctn_alt_no_assocs)>0)
          {
            $org_lctn_alt_assoc_err_arr[]=$lctn_url_lnk.': '.implode(' / ', $org_lctn_alt_no_assocs);
            $errors['org_lctn_alt_assoc']='</br>**Associations do not exist between the following locations and their listed alternates. Please amend:**</br>'.implode('</br>', $org_lctn_alt_assoc_err_arr);
          }
        }
      }
    }

    if(preg_match('/\S+/', $prof_list))
    {
      $profs=explode(',,', $prof_list);
      if(count($profs)>250)
      {$errors['prof_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $prof_empty_err_arr=array(); $prof_dplct_arr=array(); $prof_url_err_arr=array();
        foreach($profs as $prof_nm)
        {
          $prof_nm=trim($prof_nm);
          if(!preg_match('/\S+/', $prof_nm))
          {
            $prof_empty_err_arr[]=$prof_nm;
            if(count($prof_empty_err_arr)==1) {$errors['prof_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['prof_empty']='</br>**There are '.count($prof_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $prof_url=generateurl($prof_nm);

            $prof_dplct_arr[]=$prof_url;
            if(count(array_unique($prof_dplct_arr))<count($prof_dplct_arr))
            {$errors['prof_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($prof_nm)>255)
            {$errors['prof_excss_lngth']='</br>**Profession name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

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

    if(preg_match('/\S+/', $attr_list))
    {
      $attrs=explode(',,', $attr_list);
      if(count($attrs)>250)
      {$errors['attr_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $attr_empty_err_arr=array(); $attr_dplct_arr=array(); $attr_url_err_arr=array();
        foreach($attrs as $attr_nm)
        {
          $attr_nm=trim($attr_nm);
          if(!preg_match('/\S+/', $attr_nm))
          {
            $attr_empty_err_arr[]=$attr_nm;
            if(count($attr_empty_err_arr)==1) {$errors['attr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['attr_empty']='</br>**There are '.count($attr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $attr_url=generateurl($attr_nm);

            $attr_dplct_arr[]=$attr_url;
            if(count(array_unique($attr_dplct_arr))<count($attr_dplct_arr))
            {$errors['attr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($attr_nm)>255)
            {$errors['attr_excss_lngth']='</br>**Attribute name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

            $sql= "SELECT attr_nm
                  FROM attr
                  WHERE NOT EXISTS (SELECT 1 FROM attr WHERE attr_nm='$attr_nm')
                  AND attr_url='$attr_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing attribute URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            $row=mysqli_fetch_array($result);
            if(mysqli_num_rows($result)>0)
            {
              $attr_url_err_arr[]=$row['attr_nm'];
              if(count($attr_url_err_arr)==1)
              {$errors['attr_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $attr_url_err_arr)).'?**';}
              else
              {$errors['attr_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $attr_url_err_arr)).'?**';}
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $abil_list))
    {
      $abils=explode(',,', $abil_list);
      if(count($abils)>250)
      {$errors['abil_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $abil_empty_err_arr=array(); $abil_dplct_arr=array(); $abil_url_err_arr=array();
        foreach($abils as $abil_nm)
        {
          $abil_nm=trim($abil_nm);
          if(!preg_match('/\S+/', $abil_nm))
          {
            $abil_empty_err_arr[]=$abil_nm;
            if(count($abil_empty_err_arr)==1) {$errors['abil_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['abil_empty']='</br>**There are '.count($abil_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $abil_url=generateurl($abil_nm);

            $abil_dplct_arr[]=$abil_url;
            if(count(array_unique($abil_dplct_arr))<count($abil_dplct_arr))
            {$errors['abil_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($abil_nm)>255)
            {$errors['abil_excss_lngth']='</br>**Ability name is allowed a maximum of 255 characters. Please amend entries that exceed this amount.**';}

            $sql= "SELECT abil_nm
                  FROM abil
                  WHERE NOT EXISTS (SELECT 1 FROM abil WHERE abil_nm='$abil_nm')
                  AND abil_url='$abil_url'";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error checking for existing ability URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            $row=mysqli_fetch_array($result);
            if(mysqli_num_rows($result)>0)
            {
              $abil_url_err_arr[]=$row['abil_nm'];
              if(count($abil_url_err_arr)==1)
              {$errors['abil_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $abil_url_err_arr)).'?**';}
              else
              {$errors['abil_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $abil_url_err_arr)).'?**';}
            }
          }
        }
      }
    }

    if(preg_match('/\S+/', $var_char_list))
    {
      $var_char_nms=explode(',,', $_POST['var_char_list']);
      if(count($var_char_nms)>250)
      {$errors['var_char_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $var_char_empty_err_arr=array(); $var_char_hyphn_excss_err_arr=array(); $var_char_sffx_err_arr=array();
        $var_char_hyphn_err_arr=array(); $var_char_dplct_arr=array(); $var_char_url_err_arr=array();
        foreach($var_char_nms as $var_char_nm)
        {
          $var_char_errors=0;

          $var_char_nm=trim($var_char_nm);
          if(!preg_match('/\S+/', $var_char_nm))
          {
            $var_char_errors++; $var_char_empty_err_arr[]=$var_char_nm;
            if(count($var_char_empty_err_arr)==1) {$errors['var_char_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['var_char_empty']='</br>**There are '.count($var_char_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            if(substr_count($var_char_nm, '--')>1)
            {
              $var_char_errors++; $var_char_sffx_num='0'; $var_char_hyphn_excss_err_arr[]=$var_char_nm;
              $errors['var_char_hyphn_excss']='</br>**You may only use [--] for suffix assignment once per character. Please amend: '.html(implode(' / ', $var_char_hyphn_excss_err_arr)).'.**';
            }
            elseif(preg_match('/^\S+.*--.+$/', $var_char_nm))
            {
              list($var_char_nm_no_sffx, $var_char_sffx_num)=explode('--', $var_char_nm);
              $var_char_nm_no_sffx=trim($var_char_nm_no_sffx); $var_char_sffx_num=trim($var_char_sffx_num);

              if(!preg_match('/^[1-9][0-9]{0,5}$/', $var_char_sffx_num))
              {
                $var_char_errors++; $var_char_sffx_num='0'; $var_char_sffx_err_arr[]=$var_char_nm;
                $errors['var_char_sffx']='</br>**The suffix must be a positive integer (between 1 and 999,999 (with no leading 0)). Please amend: '.html(implode(' / ', $var_char_sffx_err_arr)).'**';
              }
              $var_char_nm=$var_char_nm_no_sffx;
            }
            elseif(substr_count($var_char_nm, '--')==1)
            {$var_char_errors++; $var_char_sffx_num='0'; $var_char_hyphn_err_arr[]=$var_char_nm;
            $errors['var_char_hyphn']='</br>**Character suffix assignation must use [--] in the correct format. Please amend: '.html(implode(' / ', $var_char_hyphn_err_arr)).'**';}
            else
            {$var_char_sffx_num='0';}

            if($var_char_sffx_num) {$var_char_sffx_rmn=' ('.romannumeral($var_char_sffx_num).')';} else {$var_char_sffx_rmn='';}

            $var_char_url=generateurl($var_char_nm.$var_char_sffx_rmn);

            $var_char_dplct_arr[]=$var_char_url;
            if(count(array_unique($var_char_dplct_arr))<count($var_char_dplct_arr))
            {$errors['var_char_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($var_char_nm)>255 || strlen($var_char_url)>255)
            {$var_char_errors++; $errors['var_char_nm_excss_lngth']='</br>**Character name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($var_char_errors==0)
            {
              $var_char_nm_cln=cln($var_char_nm);
              $var_char_sffx_num_cln=cln($var_char_sffx_num);
              $var_char_url_cln=cln($var_char_url);

              $sql= "SELECT char_nm, char_sffx_num
                    FROM role
                    WHERE NOT EXISTS (SELECT 1 FROM role WHERE char_nm='$var_char_nm_cln' AND char_sffx_num='$var_char_sffx_num_cln')
                    AND char_url='$var_char_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing character URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                if($row['char_sffx_num']) {$var_char_sffx_num='--'.$row['char_sffx_num'];}
                else {$var_char_sffx_num='';}
                $var_char_url_err_arr[]=$row['char_nm'].$var_char_sffx_num;
                if(count($var_char_url_err_arr)==1)
                {$errors['var_char_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $var_char_url_err_arr)).'?**';}
                else
                {$errors['var_char_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $var_char_url_err_arr)).'?**';}
              }
              else
              {
                $sql= "SELECT char_id
                      FROM role
                      WHERE char_url='$var_char_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing character URL (for existing character check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if(mysqli_num_rows($result)==0)
                {
                  $var_char_nonexst_err_arr[]=$var_char_nm.$var_char_sffx_rmn;
                  $errors['var_char_nonexst']='</br>**The following are not existing characters: '.html(implode(' / ', $var_char_nonexst_err_arr)).'.**';
                }
                elseif($row['char_id']==$char_id)
                {$errors['var_char_id_mtch']='</br>**You cannot assign this characer as a variant of itself: '.html($var_char_nm.$var_char_sffx_rmn).'.**';}
              }
            }
          }
        }
      }
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $char_id=cln($_POST['char_id']);
      $sql= "SELECT char_nm, char_sffx_num
            FROM role
            WHERE char_id='$char_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring character details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['char_sffx_num']) {$char_sffx_rmn=' ('.romannumeral($row['char_sffx_num']).')';} else {$char_sffx_rmn='';}
      $pagetab='Edit: '.html($row['char_nm'].$char_sffx_rmn);
      $pagetitle=html($row['char_nm'].$char_sffx_rmn);
      $char_nm=$_POST['char_nm'];
      $char_sffx_num=$_POST['char_sffx_num'];
      $char_lnk=$_POST['char_lnk'];
      $char_age_frm=$_POST['char_age_frm'];
      $char_age_to=$_POST['char_age_to'];
      $char_dscr=$_POST['char_dscr'];
      $char_amnt=$_POST['char_amnt'];
      $ethn_list=$_POST['ethn_list'];
      $org_lctn_list=$_POST['org_lctn_list'];
      $prof_list=$_POST['prof_list'];
      $attr_list=$_POST['attr_list'];
      $abil_list=$_POST['abil_list'];
      $var_char_list=$_POST['var_char_list'];
      $textarea=$_POST['textarea'];
      $errors['char_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $char_id=html($char_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE role SET
            char_nm='$char_nm',
            char_alph=CASE WHEN '$char_alph'!='' THEN '$char_alph' END,
            char_sffx_num='$char_sffx_num',
            char_url='$char_url',
            char_lnk='$char_lnk',
            char_sx='$char_sx',
            char_age_frm=CASE WHEN '$char_age_frm'!='' THEN '$char_age_frm' END,
            char_age_to=CASE WHEN '$char_age_to'!='' THEN '$char_age_to' END,
            char_dscr='$char_dscr',
            char_amnt='$char_amnt',
            char_mlti='$char_mlti'
            WHERE char_id='$char_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating character info for submitted character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    $sql="SELECT ptid FROM ptchar WHERE charid='$char_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring associated playtext ids of character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$assoc_pt_ids[]=array('ptid'=>$row['ptid']);}
    foreach($assoc_pt_ids as $assoc_pt_id)
    {
      $assoc_pt_id=$assoc_pt_id['ptid'];

      $sql= "SELECT char_sx, char_amnt, char_mlti FROM ptchar INNER JOIN role ON charid=char_id WHERE ptid='$assoc_pt_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring character data for count: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $char_ttl=0; $char_m=0; $char_f=0; $char_non_spc=0; $char_na=0; $char_mlti=0;
      while($row=mysqli_fetch_array($result))
      {
        if($row['char_mlti']=='1') {$char_addt += 1;}
        else
        {
          $char_ttl += $row['char_amnt'];
          if($row['char_sx']=='2') {$char_m += $row['char_amnt'];}
          elseif($row['char_sx']=='3') {$char_f += $row['char_amnt'];}
          elseif($row['char_sx']=='4') {$char_non_spc += $row['char_amnt'];}
          else {$char_na += $row['char_amnt'];}
        }
      }
      if($char_addt>0) {$char_addt='1';} else {$char_addt='0';}

      $sql= "UPDATE pt SET
            char_ttl='$char_ttl',
            char_m='$char_m',
            char_f='$char_f',
            char_non_spc='$char_non_spc',
            char_na='$char_na',
            char_addt='$char_addt'
            WHERE pt_id='$assoc_pt_id'";
      if(!mysqli_query($link, $sql)) {$error='Error updating character totals for submitted playtext: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    }

    $sql="DELETE FROM charethn WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-ethnicity associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    if(preg_match('/\S+/', $ethn_list))
    {
      $ethns=explode(',,', $ethn_list);
      $n=0;
      foreach($ethns as $ethn_nm)
      {
        $ethn_nm=trim($ethn_nm);
        $ethn_url=generateurl($ethn_nm);
        $ethn_ordr=++$n;

        $sql="SELECT 1 FROM ethn WHERE ethn_url='$ethn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of ethnicity: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql="INSERT INTO ethn(ethn_nm, ethn_url) VALUES('$ethn_nm', '$ethn_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql= "INSERT INTO charethn(charid, ethn_ordr, ethnid)
              SELECT '$char_id', '$ethn_ordr', ethn_id FROM ethn WHERE ethn_url='$ethn_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding character-ethnicity association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
    }

    $sql="DELETE FROM charorg_lctn WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-place of origin associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charorg_lctn_alt WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-place of origin (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    if(preg_match('/\S+/', $org_lctn_list))
    {
      $org_lctn_nms=explode(',,', $org_lctn_list);
      $n=0;
      foreach($org_lctn_nms as $org_lctn_nm)
      {
        $org_lctn_nm=trim($org_lctn_nm);

        if(preg_match('/\S+.*\|\|.*\S+/', $org_lctn_nm))
        {
          list($org_lctn_nm, $org_lctn_alt_list)=explode('||', $org_lctn_nm);
          $org_lctn_nm=trim($org_lctn_nm); $org_lctn_alt_list=trim($org_lctn_alt_list);
        }
        else {$org_lctn_alt_list='';}

        if(preg_match('/^\S+.*--[1-9][0-9]{0,1}$/', $org_lctn_nm))
        {
          list($org_lctn_nm, $org_lctn_sffx_num)=explode('--', $org_lctn_nm);
          $org_lctn_nm=trim($org_lctn_nm); $org_lctn_sffx_num=trim($org_lctn_sffx_num);
          $org_lctn_sffx_rmn=' ('.romannumeral($org_lctn_sffx_num).')';
        }
        else
        {
          $org_lctn_sffx_num='0';
          $org_lctn_sffx_rmn='';
        }

        $org_lctn_url=generateurl($org_lctn_nm.$org_lctn_sffx_rmn);
        $org_lctn_alph=alph($org_lctn_nm);
        $org_lctn_ordr=++$n;

        $sql="SELECT 1 FROM lctn WHERE lctn_url='$org_lctn_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of place of origin: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO lctn(lctn_nm, lctn_alph, lctn_sffx_num, lctn_url, lctn_exp, lctn_fctn)
                VALUES('$org_lctn_nm', CASE WHEN '$org_lctn_alph'!='' THEN '$org_lctn_alph' END, '$org_lctn_sffx_num', '$org_lctn_url', 0, 0)";
          if(!mysqli_query($link, $sql)) {$error='Error adding place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql= "INSERT INTO charorg_lctn(charid, org_lctn_ordr, org_lctnid)
              SELECT '$char_id', '$org_lctn_ordr', lctn_id FROM lctn WHERE lctn_url='$org_lctn_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding character-place of origin association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

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

            $sql= "INSERT INTO charorg_lctn_alt(charid, org_lctnid, org_lctn_altid)
                  SELECT '$char_id',
                  (SELECT lctn_id FROM lctn WHERE lctn_url='$org_lctn_url'),
                  (SELECT lctn_id FROM lctn WHERE lctn_url='$org_lctn_alt_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding character-place of origin (alternate location) association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }
        }
      }
    }

    $sql="DELETE FROM charprof WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-profession associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    if(preg_match('/\S+/', $prof_list))
    {
      $profs=explode(',,', $prof_list);
      $n=0;
      foreach($profs as $prof_nm)
      {
        $prof_nm=trim($prof_nm);
        $prof_url=generateurl($prof_nm);
        $prof_ordr=++$n;

        $sql="SELECT 1 FROM prof WHERE prof_url='$prof_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of profession: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO prof(prof_nm, prof_url)
                VALUES('$prof_nm', '$prof_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql= "INSERT INTO charprof(charid, prof_ordr, profid)
              SELECT '$char_id', '$prof_ordr', prof_id FROM prof WHERE prof_url='$prof_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding character-profession association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
    }

    $sql="DELETE FROM charattr WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-attribute associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    if(preg_match('/\S+/', $attr_list))
    {
      $attrs=explode(',,', $attr_list);
      $n=0;
      foreach($attrs as $attr_nm)
      {
        $attr_nm=trim($attr_nm);
        $attr_url=generateurl($attr_nm);
        $attr_ordr=++$n;

        $sql="SELECT 1 FROM attr WHERE attr_url='$attr_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of attribute: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql= "INSERT INTO attr(attr_nm, attr_url)
                VALUES('$attr_nm', '$attr_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding attribute data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql= "INSERT INTO charattr(charid, attr_ordr, attrid)
              SELECT '$char_id', '$attr_ordr', attr_id FROM attr WHERE attr_url='$attr_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding character-attribute association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
    }

    $sql="DELETE FROM charabil WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-ability associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    if(preg_match('/\S+/', $abil_list))
    {
      $abils=explode(',,', $abil_list);
      $n=0;
      foreach($abils as $abil_nm)
      {
        $abil_nm=trim($abil_nm);
        $abil_url=generateurl($abil_nm);
        $abil_ordr=++$n;

        $sql="SELECT 1 FROM abil WHERE abil_url='$abil_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existence of ability: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        if(mysqli_num_rows($result)==0)
        {
          $sql="INSERT INTO abil(abil_nm, abil_url) VALUES('$abil_nm', '$abil_url')";
          if(!mysqli_query($link, $sql)) {$error='Error adding ability data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }

        $sql= "INSERT INTO charabil(charid, abil_ordr, abilid)
              SELECT '$char_id', '$abil_ordr', abil_id FROM abil WHERE abil_url='$abil_url'";
        if(!mysqli_query($link, $sql)) {$error='Error adding character-ability association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
    }

    $sql="DELETE FROM var_char WHERE var_char1='$char_id' OR var_char2='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting char-variable character associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    if(preg_match('/\S+/', $var_char_list))
    {
      $var_char_nms=explode(',,', $var_char_list);
      foreach($var_char_nms as $var_char_nm)
      {
        if(preg_match('/^\S+.*--[1-9][0-9]{0,5}$/', $var_char_nm))
        {
          list($var_char_nm, $var_char_sffx_num)=explode('--', $var_char_nm);
          $var_char_nm=trim($var_char_nm); $var_char_sffx_num=trim($var_char_sffx_num);
          $var_char_sffx_rmn=' ('.romannumeral($var_char_sffx_num).')';
        }
        else
        {
          $var_char_nm=trim($var_char_nm);
          $var_char_sffx_rmn='';
        }

        $var_char_url=generateurl($var_char_nm.$var_char_sffx_rmn);

        $sql= "SELECT char_id
              FROM role
              WHERE char_url='$var_char_url'";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error checking for existing character URL (for existing character check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        $row=mysqli_fetch_array($result);
        $var_char_id=$row['char_id'];

        $sql= "INSERT INTO var_char(var_char1, var_char2)
              VALUES('$char_id', '$var_char_id')";
        if(!mysqli_query($link, $sql)) {$error='Error adding char-variable character association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS CHARACTER HAS BEEN EDITED:'.' '.html($char_nm_session);
    header('Location: '.$char_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $char_id=cln($_POST['char_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM ptchar WHERE charid='$char_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring playtext-character association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Playtext';}

    if(count($assocs)>0)
    {$errors['char_dlt']='**Character must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT char_nm, char_sffx_num
            FROM role
            WHERE char_id='$char_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring character details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['char_sffx_num']) {$char_sffx_rmn=' ('.romannumeral($row['char_sffx_num']).')';} else {$char_sffx_rmn='';}
      $pagetab='Edit: '.html($row['char_nm'].$char_sffx_rmn);
      $pagetitle=html($row['char_nm'].$char_sffx_rmn);
      $char_nm=$_POST['char_nm'];
      $char_sffx_num=$_POST['char_sffx_num'];
      $char_lnk=$_POST['char_lnk'];
      if($_POST['char_sx']=='1') {$char_sx='1';}
      if($_POST['char_sx']=='2') {$char_sx='2';}
      if($_POST['char_sx']=='3') {$char_sx='3';}
      if($_POST['char_sx']=='4') {$char_sx='4';}
      $char_age_frm=$_POST['char_age_frm'];
      $char_age_to=$_POST['char_age_frm'];
      $char_dscr=$_POST['char_dscr'];
      $char_amnt=$_POST['char_amnt'];
      if(isset($_POST['char_mlti'])) {$char_mlti='1';}  else {$char_mlti='0';}
      $ethn_list=$_POST['ethn_list'];
      $org_lctn_list=$_POST['org_lctn_list'];
      $prof_list=$_POST['prof_list'];
      $attr_list=$_POST['attr_list'];
      $abil_list=$_POST['abil_list'];
      $var_char_list=$_POST['var_char_list'];
      $textarea=$_POST['textarea'];
      $char_id=html($char_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT char_nm, char_sffx_num
            FROM role
            WHERE char_id='$char_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['char_sffx_num']) {$char_sffx_rmn=' ('.romannumeral($row['char_sffx_num']).')';} else {$char_sffx_rmn='';}
      $pagetab='Delete confirmation: '.html($row['char_nm'].$char_sffx_rmn);
      $pagetitle=html($row['char_nm'].$char_sffx_rmn);
      $char_id=html($char_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $char_id=cln($_POST['char_id']);
    $sql= "SELECT char_nm, char_sffx_num
          FROM role
          WHERE char_id='$char_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['char_sffx_num']) {$char_sffx_rmn=' ('.romannumeral($row['char_sffx_num']).')';} else {$char_sffx_rmn='';}
    $char_nm_session=$row['char_nm'].$char_sffx_rmn;

    $sql="DELETE FROM ptchar WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-playtext associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charethn WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-ethnicity associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charorg_lctn WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-place of origin associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charorg_lctn_alt WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-place of origin (alternate location) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charprof WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-profession associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charattr WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-attribute associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM charabil WHERE charid='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character-ability associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM var_char WHERE var_char1='$char_id' OR var_char2='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting char-variable character associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM role WHERE char_id='$char_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS CHARACTER HAS BEEN DELETED FROM THE DATABASE:'.' '.html($char_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $char_id=cln($_POST['char_id']);
    $sql="SELECT char_url FROM role WHERE char_id='$char_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $char_id=html($char_id);

    header('Location: '.$row['char_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $char_url=cln($_GET['char_url']);

  $sql="SELECT char_id FROM role WHERE char_url='$char_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $char_id=$row['char_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT char_nm, char_sffx_num, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti
          FROM role
          WHERE char_id='$char_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['char_sffx_num']) {$char_sffx_rmn=' ('.romannumeral($row['char_sffx_num']).')';} else {$char_sffx_rmn='';}
    $pagetab=html($row['char_nm'].$char_sffx_rmn);
    $pagetitle=html($row['char_nm']);
    $char_nm=html($row['char_nm']);
    if($row['char_sx']=='2') {$char_sx='Male';} elseif($row['char_sx']=='3') {$char_sx='Female';} elseif($row['char_sx']=='4') {$char_sx='Non-specific';} else {$char_sx=NULL;}
    if($row['char_age_frm']==$row['char_age_to']) {$char_age=html($row['char_age_frm']);} else {$char_age=html($row['char_age_frm'].' - '.$row['char_age_to']);}
    if($row['char_dscr']) {$char_dscr='<em>'.html($row['char_dscr']).'</em>';} else {$char_dscr=NULL;}
    if($row['char_amnt']>1) {$char_amnt=' <em>'.html($row['char_amnt']).'</em>';} elseif($row['char_mlti']) {$char_amnt='<em>Multiple roles</em>';} else {$char_amnt=NULL;}
    if($row['char_amnt']==1) {$char_pt_tbl_hdr=' in which this character appears'; $var_char_pt_tbl_hdr=' in which variations of this character appears'; $prd_tbl_hdr=' in which this character has been performed';}
    else {$char_pt_tbl_hdr=' in which these characters appear'; $var_char_pt_tbl_hdr=' in which variations of these characters appear'; $prd_tbl_hdr=' in which these characters have been performed';}

    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, p2.pt_coll,
          COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptchar
          INNER JOIN pt p1 ON ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE charid='$char_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, pt_coll,
          COALESCE(pt_alph, pt_nm)pt_alph, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptchar pc
          INNER JOIN pt p1 ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE charid='$char_id' AND coll_ov IS NULL
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
          $sql="SELECT 1 FROM ptchar WHERE ptid='$pt_id' AND charid='$char_id' LIMIT 1";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for pt_ids directly credited to this character: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)>0)
          {
            $pt_cnt[]='1';
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
          }
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url,
            GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM ptchar pc
            INNER JOIN pt ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE charid='$char_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtext data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $sg_pt_ids[]=$row['pt_id']; $pt_cnt[]='1';
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'wri_rls'=>array());
      }

      if(!empty($sg_pt_ids))
      {
        foreach($sg_pt_ids as $sg_pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
        }
      }
      $char_pts=$pts;
    }

    $char_crdt_ids[]=$char_id;
    $sql= "SELECT var_char2 AS char_id FROM var_char WHERE var_char1='$char_id'
          UNION
          SELECT var_char1 AS char_id FROM var_char WHERE var_char2='$char_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for variant character associations of character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$char_crdt_ids[]=$row['char_id'];}

    $r_var_char_pt_crdt_id=implode($char_crdt_ids, ' OR r1.char_id=');
    $sql= "SELECT r2.char_id FROM role r1
          INNER JOIN ptchar ptc1 ON r1.char_id=ptc1.charid INNER JOIN pt pt1 ON ptc1.ptid=pt1.pt_id
          INNER JOIN ptmat ptm1 ON pt1.pt_id=ptm1.ptid INNER JOIN mat ON ptm1.matid=mat_id
          INNER JOIN ptmat ptm2 ON mat_id=ptm2.matid INNER JOIN pt pt2 ON ptm2.ptid=pt2.pt_id
          INNER JOIN ptchar ptc2 ON pt2.pt_id=ptc2.ptid INNER JOIN role r2 ON ptc2.charid=r2.char_id
          WHERE (r1.char_id=$r_var_char_pt_crdt_id) AND (r1.char_nm=r2.char_nm OR r1.char_lnk=r2.char_lnk) AND r1.char_id!=r2.char_id
          UNION
          SELECT r2.char_id FROM role r1
          INNER JOIN ptchar ptc1 ON r1.char_id=ptc1.charid INNER JOIN pt pt1 ON ptc1.ptid=pt1.pt_id
          INNER JOIN ptsrc_mat ptsm1 ON pt1.pt_id=ptsm1.ptid INNER JOIN mat ON ptsm1.src_matid=mat_id
          INNER JOIN ptsrc_mat ptsm2 ON mat_id=ptsm2.src_matid INNER JOIN pt pt2 ON ptsm2.ptid=pt2.pt_id
          INNER JOIN ptchar ptc2 ON pt2.pt_id=ptc2.ptid INNER JOIN role r2 ON ptc2.charid=r2.char_id
          WHERE (r1.char_id=$r_var_char_pt_crdt_id) AND (r1.char_nm=r2.char_nm OR r1.char_lnk=r2.char_lnk) AND r1.char_id!=r2.char_id
          UNION
          SELECT r2.char_id FROM role r1
          INNER JOIN ptchar ptc1 ON r1.char_id=ptc1.charid INNER JOIN pt pt1 ON ptc1.ptid=pt1.pt_id
          INNER JOIN ptmat ptm1 ON pt1.pt_id=ptm1.ptid INNER JOIN mat ON ptm1.matid=mat_id
          INNER JOIN ptsrc_mat ptsm2 ON mat_id=ptsm2.src_matid INNER JOIN pt pt2 ON ptsm2.ptid=pt2.pt_id
          INNER JOIN ptchar ptc2 ON pt2.pt_id=ptc2.ptid INNER JOIN role r2 ON ptc2.charid=r2.char_id
          WHERE (r1.char_id=$r_var_char_pt_crdt_id) AND (r1.char_nm=r2.char_nm OR r1.char_lnk=r2.char_lnk) AND r1.char_id!=r2.char_id
          UNION
          SELECT r2.char_id FROM role r1
          INNER JOIN ptchar ptc1 ON r1.char_id=ptc1.charid INNER JOIN pt pt1 ON ptc1.ptid=pt1.pt_id
          INNER JOIN ptsrc_mat ptsm1 ON pt1.pt_id=ptsm1.ptid INNER JOIN mat ON ptsm1.src_matid=mat_id
          INNER JOIN ptmat ptm2 ON mat_id=ptm2.matid INNER JOIN pt pt2 ON ptm2.ptid=pt2.pt_id
          INNER JOIN ptchar ptc2 ON pt2.pt_id=ptc2.ptid INNER JOIN role r2 ON ptc2.charid=r2.char_id
          WHERE (r1.char_id=$r_var_char_pt_crdt_id) AND (r1.char_nm=r2.char_nm OR r1.char_lnk=r2.char_lnk) AND r1.char_id!=r2.char_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring variant character names from playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result)) {$char_crdt_ids[]=$row['char_id'];}

    $r_var_char_pt_crdt_id=implode(array_unique($char_crdt_ids), ' OR r1.char_id=');
    $sql= "SELECT r2.char_id, r2.char_nm, COALESCE(r2.char_alph, r2.char_nm)char_alph, r2.char_sffx_num FROM role r1
          INNER JOIN var_char ON r1.char_id=var_char1 INNER JOIN role r2 ON var_char2=r2.char_id WHERE (r1.char_id=$r_var_char_pt_crdt_id) AND r2.char_id!='$char_id'
          UNION
          SELECT r2.char_id, r2.char_nm, COALESCE(r2.char_alph, r2.char_nm)char_alph, r2.char_sffx_num FROM role r1
          INNER JOIN var_char ON r1.char_id=var_char2 INNER JOIN role r2 ON var_char1=r2.char_id WHERE (r1.char_id=$r_var_char_pt_crdt_id) AND r2.char_id!='$char_id'
          UNION
          SELECT char_id, char_nm, COALESCE(char_alph, char_nm)char_alph, char_sffx_num FROM role r1 WHERE (r1.char_id=$r_var_char_pt_crdt_id) AND r1.char_id!='$char_id'
          ORDER BY char_alph ASC, char_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring variant character names from playtexts (ii): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$char_crdt_ids[]=$row['char_id']; if(html($row['char_nm'])!==$char_nm) {$pt_vrnt_nms[]=html($row['char_nm']);}}
    if(!empty($pt_vrnt_nms)) {$pt_vrnt_nm=implode(' / ', array_unique($pt_vrnt_nms));} else {$pt_vrnt_nm=NULL;}

    $pts=array();
    $var_char_pt_crdt_id=implode(array_unique($char_crdt_ids), ' OR charid=');
    $sql= "SELECT p2.pt_id, p2.pt_nm, p2.pt_yr_strtd_c, p2.pt_yr_strtd, p2.pt_yr_wrttn_c, p2.pt_yr_wrttn, p2.pt_url, COALESCE(p2.pt_alph, p2.pt_nm)pt_alph, p2.pt_coll,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, (SELECT COUNT(*) FROM pt WHERE coll_ov=p2.pt_id) AS sg_cnt
          FROM ptchar pc
          INNER JOIN pt p1 ON pc.ptid=p1.pt_id INNER JOIN pt p2 ON p1.coll_ov=p2.pt_id
          LEFT OUTER JOIN pttxt_vrsn pttv ON p2.pt_id=pttv.ptid LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (charid=$var_char_pt_crdt_id) AND charid!='$char_id'
          GROUP BY pt_id
          UNION
          SELECT pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph, pt_coll,
          GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') AS txt_vrsn_nm, (SELECT COUNT(*) FROM pt WHERE coll_ov=p1.pt_id) AS sg_cnt
          FROM ptchar pc
          INNER JOIN role ON pc.charid=char_id INNER JOIN pt p1 ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
          LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
          WHERE (charid=$var_char_pt_crdt_id) AND charid!='$char_id' AND coll_ov IS NULL
          GROUP BY pt_id
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring playtexts (feat. variations of character): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        if($row['pt_coll']=='3') {$txt_vrsn_nm='Collection';} else {$txt_vrsn_nm=html($row['txt_vrsn_nm']);}
        $pts[$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>$txt_vrsn_nm, 'pt_yr'=>$pt_yr, 'sg_cnt'=>$row['sg_cnt'], 'var_chars'=>array(), 'wri_rls'=>array(), 'sg_pts'=>array());
      }

      $sql= "SELECT pt_id, char_nm, char_url, char_ordr FROM ptchar
            INNER JOIN role ON charid=char_id INNER JOIN pt ON ptid=pt_id
            WHERE (charid=$var_char_pt_crdt_id) AND charid!='$char_id' AND coll_ov IS NULL GROUP BY pt_id, char_id
            ORDER BY char_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring variant characters for playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        $var_char_pt_ids[]=$row['pt_id']; $var_char_pt_cnt[]=$row['pt_id'];
        $pts[$row['pt_id']]['var_chars'][]=$var_char_nm='<em><a href="/character/'.html($row['char_url']).'">'.html($row['char_nm']).'</a></em>';
      }

      if(!empty($var_char_pt_ids))
      {
        foreach($var_char_pt_ids as $pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_wri_rcv.inc.php';
        }
      }

      $sql= "SELECT coll_ov, pt_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, pt_coll,
            GROUP_CONCAT(DISTINCT char_nm ORDER BY pc.char_ordr ASC SEPARATOR ' / ') AS char_nm, GROUP_CONCAT(DISTINCT txt_vrsn_nm ORDER BY txt_vrsn_ordr ASC SEPARATOR ' / ') as txt_vrsn_nm
            FROM ptchar pc
            INNER JOIN role ON pc.charid=char_id INNER JOIN pt ON pc.ptid=pt_id LEFT OUTER JOIN pttxt_vrsn pttv ON pt_id=pttv.ptid
            LEFT OUTER JOIN txt_vrsn ON txt_vrsnid=txt_vrsn_id
            WHERE (charid=$var_char_pt_crdt_id) AND charid!='$char_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id, char_id
            ORDER BY coll_sbhdrid ASC, coll_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collection segment playtexts (feat. variations of character): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $var_char_sg_pt_ids[]=$row['ptid']; $var_char_pt_cnt[]=$row['ptid'];
        $pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]=array('pt_nm'=>$pt_nm, 'pt_nm_pln'=>html($row['pt_nm']), 'txt_vrsn_nm'=>html($row['txt_vrsn_nm']), 'pt_yr'=>$pt_yr, 'var_chars'=>array(), 'wri_rls'=>array());
      }

      $sql= "SELECT coll_ov, pt_id, char_nm, char_url FROM ptchar
            INNER JOIN role ON charid=char_id INNER JOIN pt ON pt_id=pt_id
            WHERE (charid=$var_char_pt_crdt_id) AND charid!='$char_id' AND coll_ov IS NOT NULL
            GROUP BY coll_ov, pt_id ORDER BY char_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring variant characters for collection segment playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$pts[$row['coll_ov']]['sg_pts'][$row['pt_id']]['var_chars'][]='<em><a href="/character/'.html($row['char_url']).'">'.html($row['char_nm']).'</a></em>';}

      if(!empty($var_char_sg_pt_ids))
      {
        foreach($var_char_sg_pt_ids as $sg_pt_id)
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_pt_wri_rcv.inc.php';
        }
      }
      $var_char_pts=$pts;
    }

    $char_prd_crdt_id=implode(array_unique($char_crdt_ids), ' OR char_id=');
    $sql= "SELECT char_nm, char_lnk, prd_id, prf_rl, COALESCE(prf_rl_alph, prf_rl)prf_rl_alph FROM role
          INNER JOIN ptchar ON char_id=charid INNER JOIN pt ON ptid=pt_id INNER JOIN ptmat ptm ON pt_id=ptm.ptid
          INNER JOIN mat ON ptm.matid=mat_id INNER JOIN prdmat pm ON mat_id=pm.matid INNER JOIN prd ON prdid=prd_id INNER JOIN prdprf pp ON prd_id=pp.prdid
          WHERE (char_id=$char_prd_crdt_id) AND (prf_rl=char_nm OR prf_rl_lnk=char_lnk)
          UNION
          SELECT char_nm, char_lnk, prd_id, prf_rl, COALESCE(prf_rl_alph, prf_rl)prf_rl_alph FROM role
          INNER JOIN ptchar ON char_id=charid INNER JOIN pt ON ptid=pt_id INNER JOIN ptsrc_mat ptsm ON pt_id=ptsm.ptid
          INNER JOIN mat ON ptsm.src_matid=mat_id INNER JOIN prdsrc_mat psm ON mat_id=psm.src_matid INNER JOIN prd ON prdid=prd_id INNER JOIN prdprf pp ON prd_id=pp.prdid
          WHERE (char_id=$char_prd_crdt_id) AND (prf_rl=char_nm OR prf_rl_lnk=char_lnk)
          UNION
          SELECT char_nm, char_lnk, prd_id, prf_rl, COALESCE(prf_rl_alph, prf_rl)prf_rl_alph FROM role
          INNER JOIN ptchar ON char_id=charid INNER JOIN pt ON ptid=pt_id INNER JOIN ptmat ptm ON pt_id=ptm.ptid
          INNER JOIN mat ON ptm.matid=mat_id INNER JOIN prdsrc_mat psm ON mat_id=psm.src_matid INNER JOIN prd ON prdid=prd_id INNER JOIN prdprf pp ON prd_id=pp.prdid
          WHERE (char_id=$char_prd_crdt_id) AND (prf_rl=char_nm OR prf_rl_lnk=char_lnk)
          UNION
          SELECT char_nm, char_lnk, prd_id, prf_rl, COALESCE(prf_rl_alph, prf_rl)prf_rl_alph FROM role
          INNER JOIN ptchar ON char_id=charid INNER JOIN pt ON ptid=pt_id INNER JOIN ptsrc_mat ptsm ON pt_id=ptsm.ptid
          INNER JOIN mat ON ptsm.src_matid=mat_id INNER JOIN prdmat pm ON mat_id=pm.matid INNER JOIN prd ON prdid=prd_id INNER JOIN prdprf pp ON prd_id=pp.prdid
          WHERE (char_id=$char_prd_crdt_id) AND (prf_rl=char_nm OR prf_rl_lnk=char_lnk)
          UNION
          SELECT char_nm, char_lnk, prd_id, us_rl, COALESCE(us_rl_alph, us_rl)us_rl_alph FROM role
          INNER JOIN ptchar ON char_id=charid INNER JOIN pt ON ptid=pt_id INNER JOIN ptmat ptm ON pt_id=ptm.ptid
          INNER JOIN mat ON ptm.matid=mat_id INNER JOIN prdmat pm ON mat_id=pm.matid INNER JOIN prd ON prdid=prd_id INNER JOIN prdus pu ON prd_id=pu.prdid
          WHERE (char_id=$char_prd_crdt_id) AND (us_rl=char_nm OR us_rl_lnk=char_lnk)
          UNION
          SELECT char_nm, char_lnk, prd_id, us_rl, COALESCE(us_rl_alph, us_rl)us_rl_alph FROM role
          INNER JOIN ptchar ON char_id=charid INNER JOIN pt ON ptid=pt_id INNER JOIN ptsrc_mat ptsm ON pt_id=ptsm.ptid
          INNER JOIN mat ON ptsm.src_matid=mat_id INNER JOIN prdsrc_mat psm ON mat_id=psm.src_matid INNER JOIN prd ON prdid=prd_id INNER JOIN prdus pu ON prd_id=pu.prdid
          WHERE (char_id=$char_prd_crdt_id) AND (us_rl=char_nm OR us_rl_lnk=char_lnk)
          UNION
          SELECT char_nm, char_lnk, prd_id, us_rl, COALESCE(us_rl_alph, us_rl)us_rl_alph FROM role
          INNER JOIN ptchar ON char_id=charid INNER JOIN pt ON ptid=pt_id INNER JOIN ptmat ptm ON pt_id=ptm.ptid
          INNER JOIN mat ON ptm.matid=mat_id INNER JOIN prdsrc_mat psm ON mat_id=psm.src_matid INNER JOIN prd ON prdid=prd_id INNER JOIN prdus pu ON prd_id=pu.prdid
          WHERE (char_id=$char_prd_crdt_id) AND (us_rl=char_nm OR us_rl_lnk=char_lnk)
          UNION
          SELECT char_nm, char_lnk, prd_id, us_rl, COALESCE(us_rl_alph, us_rl)us_rl_alph FROM role
          INNER JOIN ptchar ON char_id=charid INNER JOIN pt ON ptid=pt_id INNER JOIN ptsrc_mat ptsm ON pt_id=ptsm.ptid
          INNER JOIN mat ON ptsm.src_matid=mat_id INNER JOIN prdmat pm ON mat_id=pm.matid INNER JOIN prd ON prdid=prd_id INNER JOIN prdus pu ON prd_id=pu.prdid
          WHERE (char_id=$char_prd_crdt_id) AND (us_rl=char_nm OR us_rl_lnk=char_lnk)
          ORDER BY prf_rl_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring variant character names from productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $all_prd_ids[]=$row['prd_id'];
      $char_prdprf_crdt_ids[]='(pp1.prdid='.$row['prd_id'].' AND (pp1.prf_rl="'.$row['char_nm'].'" OR pp1.prf_rl_lnk="'.$row['char_lnk'].'"))';
      $char_prdus_crdt_ids[]='(pu1.prdid='.$row['prd_id'].' AND (pu1.us_rl="'.$row['char_nm'].'" OR pu1.us_rl_lnk="'.$row['char_lnk'].'"))';
      if($row['prf_rl']!==$char_nm) {$prd_vrnt_nms[]=html($row['prf_rl']);}
    }
    if(!empty($prd_vrnt_nms)) {$prd_vrnt_nm=html(implode(' / ', array_unique($prd_vrnt_nms)));} else {$prd_vrnt_nm=NULL;}

    if(!empty($all_prd_ids))
    {
      $prd_ids=array();
      $char_prdprf_crdt_id=implode(' OR ', $char_prdprf_crdt_ids); $char_prdus_crdt_id=implode(' OR ', $char_prdus_crdt_ids);
      $prd_id=implode(array_unique($all_prd_ids), ' OR prd_id='); $p_prd_id=implode(array_unique($all_prd_ids), ' OR p1.prd_id=');
      $sql= "SELECT p2.prd_id, p2.prd_nm, p2.prd_url, DATE_FORMAT(p2.prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(p2.prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply,
            p2.prd_dts_info, p2.prd_tbc_nt, thtr_fll_nm, p2.prd_frst_dt, COALESCE(p2.prd_alph, p2.prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p2.prd_id) AS sg_cnt
            FROM prd p1 INNER JOIN prd p2 ON p1.coll_ov=p2.prd_id INNER JOIN thtr ON p2.thtrid=thtr_id WHERE (p1.prd_id=$p_prd_id)
            UNION
            SELECT prd_id, prd_nm, prd_url, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply,
            prd_dts_info, prd_tbc_nt, thtr_fll_nm, prd_frst_dt, COALESCE(prd_alph, prd_nm)prd_alph, (SELECT COUNT(*) FROM prd WHERE coll_ov=p1.prd_id) AS sg_cnt
            FROM prd p1 INNER JOIN thtr ON thtrid=thtr_id WHERE (prd_id=$prd_id) AND coll_ov IS NULL
            ORDER BY prd_frst_dt DESC, prd_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      if(mysqli_num_rows($result)>0)
      {
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
          $prds[$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'sg_cnt'=>$row['sg_cnt'], 'wri_rls'=>array(), 'prd_prfs'=>array(), 'prd_uss'=>array(), 'sg_prds'=>array());
          if(in_array($row['prd_id'], $all_prd_ids)) {$prd_ids[]=$row['prd_id'];}
          $sg_prd_ids=array_diff($all_prd_ids, $prd_ids);
        }

        if(!empty($prd_ids))
        {
          foreach($prd_ids as $prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_wri_rcv.inc.php';
          }
        }

        $sql= "SELECT prd_id, prsn_id, prf_ordr, prsn_fll_nm, prsn_url
              FROM prdprf pp1
              INNER JOIN prsn ON prf_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
              WHERE ($char_prdprf_crdt_id) AND coll_ov IS NULL
              GROUP BY prd_id, prsn_id
              ORDER BY prf_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring performers from productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['prd_id']]['prd_prfs'][$row['prsn_id']]=array('prsn_nm'=>'<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>', 'prf_rls'=>array(), 'prf_othr_rls'=>array());}

        $sql= "SELECT pp1.prdid AS p1, pp1.prf_prsnid, pp2.prf_rl AS pr, pp2.prf_rl_alt, pp2.prf_rl_lnk AS prl, pp2.prf_rl_ordr,
              CASE WHEN pp1.prf_rl=pp2.prf_rl THEN 1 ELSE NULL END AS mn_rl,
              (SELECT char_url FROM prdprf pp1 INNER JOIN prsn ON pp1.prf_prsnid=prsn_id INNER JOIN prdprf pp2 ON prsn_id=pp2.prf_prsnid
              INNER JOIN prdpt ppt ON pp2.prdid=ppt.prdid INNER JOIN ptchar pc ON ppt.ptid = pc.ptid INNER JOIN role ON charid=char_id
              LEFT OUTER JOIN prd ON pp1.prdid=prd_id
              WHERE ($char_prdprf_crdt_id) AND (char_nm=pr OR char_lnk=prl) AND pp1.prdid=p1 AND pp2.prdid=p1 AND coll_ov IS NULL AND char_url!='$char_url' LIMIT 1) AS char_url
              FROM prdprf pp1
              INNER JOIN prdprf pp2 ON pp1.prdid=pp2.prdid AND pp1.prf_prsnid=pp2.prf_prsnid INNER JOIN prd ON pp1.prdid=prd_id
              WHERE ($char_prdprf_crdt_id) AND coll_ov IS NULL
              ORDER BY prf_rl_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (performer) roles from productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['char_url']) {$prf_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['pr']).'</a>';} else {$prf_rl=html($row['pr']);}
          if($row['prf_rl_alt']) {$prf_rl.='<span style="font-style:normal"> (alt)</span>';}
          if($row['mn_rl']) {$prds[$row['p1']]['prd_prfs'][$row['prf_prsnid']]['prf_rls'][]=$prf_rl;}
          else {$prds[$row['p1']]['prd_prfs'][$row['prf_prsnid']]['prf_othr_rls'][]=$prf_rl;}
        }

        $sql= "SELECT prd_id, prsn_id, us_ordr, prsn_fll_nm, prsn_url
              FROM prdus pu1
              INNER JOIN prsn ON us_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
              WHERE ($char_prdus_crdt_id) AND coll_ov IS NULL
              GROUP BY prd_id, prsn_id
              ORDER BY us_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring understudies from productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['prd_id']]['prd_uss'][$row['prsn_id']]=array('prsn_nm'=>'<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>', 'us_rls'=>array(), 'us_othr_rls'=>array());}

        $sql= "SELECT pu1.prdid AS p1, pu1.us_prsnid, pu2.us_rl AS pr, pu2.us_rl_alt, pu2.us_rl_lnk AS prl, pu2.us_rl_ordr,
              CASE WHEN pu1.us_rl=pu2.us_rl THEN 1 ELSE NULL END AS mn_rl,
              (SELECT char_url FROM prdus pu1 INNER JOIN prsn ON pu1.us_prsnid=prsn_id INNER JOIN prdus pu2 ON prsn_id=pu2.us_prsnid
              INNER JOIN prdpt ppt ON pu2.prdid=ppt.prdid INNER JOIN ptchar pc ON ppt.ptid = pc.ptid INNER JOIN role ON charid=char_id
              LEFT OUTER JOIN prd ON pu1.prdid=prd_id
              WHERE ($char_prdus_crdt_id) AND (char_nm=pr OR char_lnk=prl) AND pu1.prdid=p1 AND pu2.prdid=p1 AND coll_ov IS NULL AND char_url!='$char_url' LIMIT 1) AS char_url
              FROM prdus pu1
              INNER JOIN prdus pu2 ON pu1.prdid=pu2.prdid AND pu1.us_prsnid=pu2.us_prsnid INNER JOIN prd ON pu1.prdid=prd_id
              WHERE ($char_prdus_crdt_id) AND coll_ov IS NULL
              ORDER BY us_rl_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring (understudy) roles from productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['char_url']) {$us_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['pr']).'</a>';} else {$us_rl=html($row['pr']);}
          if($row['us_rl_alt']) {$us_rl.='<span style="font-style:normal"> (alt)</span>';}
          if($row['mn_rl']) {$prds[$row['p1']]['prd_uss'][$row['us_prsnid']]['us_rls'][]=$us_rl;}
          else {$prds[$row['p1']]['prd_uss'][$row['us_prsnid']]['us_othr_rls'][]=$us_rl;}
        }

        if(!empty($sg_prd_ids))
        {
          $sg_prd_id=implode($sg_prd_ids, ' OR prd_id=');
          $sql= "SELECT coll_ov, prd_id, prd_nm, prd_url, prd_frst_dt, prd_dts_info, prd_tbc_nt, thtr_fll_nm,
                DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply, DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply
                FROM prd INNER JOIN thtr ON thtrid=thtr_id WHERE (prd_id=$sg_prd_id) AND coll_ov IS NOT NULL
                ORDER BY prd_frst_dt DESC, coll_sbhdrid ASC, coll_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring collection segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
            $prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]=array('prd_nm'=>$prd_nm, 'prd_nm_pln'=>html($row['prd_nm']), 'prd_dts'=>$prd_dts, 'thtr'=>$thtr, 'wri_rls'=>array(), 'prd_prfs'=>array(), 'prd_uss'=>array());
          }

          foreach($sg_prd_ids as $sg_prd_id)
          {
            include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/sg_prd_wri_rcv.inc.php';
          }

          $sql= "SELECT coll_ov, prd_id, prsn_id, pp1.prf_ordr, prsn_fll_nm, prsn_url
                FROM prdprf pp1
                INNER JOIN prsn ON pp1.prf_prsnid=prsn_id INNER JOIN prd ON pp1.prdid=prd_id
                WHERE ($char_prdprf_crdt_id) AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prd_id, prsn_id
                ORDER BY prf_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring performers from segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {$prds[$row['coll_ov']]['sg_prds'][$row['prd_id']]['prd_prfs'][$row['prsn_id']]=array('prsn_nm'=>'<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>', 'prf_rls'=>array(), 'prf_othr_rls'=>array());}

          $sql= "SELECT coll_ov, pp1.prdid AS p1, pp1.prf_prsnid, pp2.prf_rl AS pr, pp2.prf_rl_alt, pp2.prf_rl_lnk AS prl, pp2.prf_rl_ordr,
                CASE WHEN pp1.prf_rl=pp2.prf_rl THEN 1 ELSE NULL END AS mn_rl,
                (SELECT char_url FROM prdprf pp1 INNER JOIN prsn ON pp1.prf_prsnid=prsn_id INNER JOIN prdprf pp2 ON prsn_id=pp2.prf_prsnid
                INNER JOIN prdpt ppt ON pp2.prdid=ppt.prdid INNER JOIN ptchar pc ON ppt.ptid = pc.ptid INNER JOIN role ON charid=char_id
                INNER JOIN prd ON pp1.prdid=prd_id
                WHERE ($char_prdprf_crdt_id) AND (char_nm=pr OR char_lnk=prl) AND pp1.prdid=p1 AND pp2.prdid=p1 AND char_url!='$char_url' LIMIT 1) AS char_url
                FROM prdprf pp1
                INNER JOIN prdprf pp2 ON pp1.prdid=pp2.prdid AND pp1.prf_prsnid=pp2.prf_prsnid INNER JOIN prd ON pp1.prdid=prd_id
                WHERE ($char_prdprf_crdt_id) AND coll_ov IS NOT NULL
                ORDER BY prf_rl_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring (performer) roles from segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['char_url']) {$prf_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['pr']).'</a>';} else {$prf_rl=html($row['pr']);}
            if($row['prf_rl_alt']) {$prf_rl.='<span style="font-style:normal"> (alt)</span>';}
            if($row['mn_rl']) {$prds[$row['coll_ov']]['sg_prds'][$row['p1']]['prd_prfs'][$row['prf_prsnid']]['prf_rls'][]=$prf_rl;}
            else {$prds[$row['coll_ov']]['sg_prds'][$row['p1']]['prd_prfs'][$row['prf_prsnid']]['prf_othr_rls'][]=$prf_rl;}
          }

          $sql= "SELECT coll_ov, prd_id, prsn_id, pu1.us_ordr, prsn_fll_nm, prsn_url
                FROM prdus pu1
                INNER JOIN prsn ON pu1.us_prsnid=prsn_id INNER JOIN prd ON pu1.prdid=prd_id
                WHERE ($char_prdus_crdt_id) AND coll_ov IS NOT NULL
                GROUP BY coll_ov, prd_id, prsn_id
                ORDER BY us_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring understudies from segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {$prds[$row['coll_ov']]['sg_prds'][$row['prdid']]['prd_uss'][$row['prsn_id']]=array('prsn_nm'=>'<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>', 'us_rls'=>array(), 'us_othr_rls'=>array());}

          $sql= "SELECT coll_ov, pu1.prdid AS p1, pu1.us_prsnid, pu2.us_rl AS pr, pu2.us_rl_alt, pu2.us_rl_lnk AS prl, pu2.us_rl_ordr,
                CASE WHEN pu1.us_rl=pu2.us_rl THEN 1 ELSE NULL END AS mn_rl,
                (SELECT char_url FROM prdus pu1 INNER JOIN prsn ON pu1.us_prsnid=prsn_id INNER JOIN prdus pu2 ON prsn_id=pu2.us_prsnid
                INNER JOIN prdpt ppt ON pu2.prdid=ppt.prdid INNER JOIN ptchar pc ON ppt.ptid = pc.ptid INNER JOIN role ON charid=char_id
                INNER JOIN prd ON pu1.prdid=prd_id
                WHERE ($char_prdus_crdt_id) AND (char_nm=pr OR char_lnk=prl) AND pu1.prdid=p1 AND pu2.prdid=p1 AND char_url!='$char_url' LIMIT 1) AS char_url
                FROM prdus pu1
                INNER JOIN prdus pu2 ON pu1.prdid=pu2.prdid AND pu1.us_prsnid=pu2.us_prsnid INNER JOIN prd ON pu1.prdid=prd_id
                WHERE ($char_prdus_crdt_id) AND coll_ov IS NOT NULL
                ORDER BY us_rl_ordr ASC";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error acquiring (understudy) roles from segment productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          while($row=mysqli_fetch_array($result))
          {
            if($row['char_url']) {$us_rl='<a href="/character/'.html($row['char_url']).'">'.html($row['pr']).'</a>';} else {$us_rl=html($row['pr']);}
            if($row['us_rl_alt']) {$us_rl.='<span style="font-style:normal"> (alt)</span>';}
            if($row['mn_rl']) {$prds[$row['coll_ov']]['sg_prds'][$row['p1']]['prd_uss'][$row['us_prsnid']]['us_rls'][]=$us_rl;}
            else {$prds[$row['coll_ov']]['sg_prds'][$row['p1']]['prd_uss'][$row['us_prsnid']]['us_othr_rls'][]=$us_rl;}
          }
        }
      }
    }

    $sql="SELECT ethn_id, ethn_nm, ethn_url FROM charethn INNER JOIN ethn ON ethnid=ethn_id WHERE charid='$char_id' ORDER BY ethn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$ethns[$row['ethn_id']]=array('ethn_nm'=>'<a href="/character/ethnicity/'.html($row['ethn_url']).'">'.html($row['ethn_nm']).'</a>', 'rel_ethns'=>array());}

      $sql="SELECT rel_ethn1, ethn_nm, ethn_url FROM charethn INNER JOIN rel_ethn ON ethnid=rel_ethn1 INNER JOIN ethn ON rel_ethn2=ethn_id WHERE charid='$char_id' ORDER BY rel_ethn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related ethnicity data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$ethns[$row['rel_ethn1']]['rel_ethns'][]='<a href="/character/ethnicity/'.html($row['ethn_url']).'">'.html($row['ethn_nm']).'</a>';}
    }

    $sql="SELECT lctn_id, lctn_nm, lctn_url FROM charorg_lctn INNER JOIN lctn ON org_lctnid=lctn_id WHERE charid='$char_id' ORDER BY org_lctn_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring place of origin data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$org_lctns[$row['lctn_id']]=array('org_lctn'=>'<a href="/character/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>', 'rel_lctns'=>array());}

      $sql= "SELECT rel_lctn1, lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
            FROM charorg_lctn col
            INNER JOIN rel_lctn ON org_lctnid=rel_lctn1 INNER JOIN lctn ON rel_lctn2=lctn_id
            LEFT OUTER JOIN charorg_lctn_alt cola ON col.charid=cola.charid AND col.org_lctnid=cola.org_lctnid
            WHERE col.charid='$char_id' AND lctn_exp=0 AND lctn_fctn=0 AND cola.charid IS NULL
            UNION
            SELECT rel_lctn1, lctn_nm, lctn_url, rel_lctn_nt1, rel_lctn_nt2, rel_lctn_ordr
            FROM charorg_lctn col
            INNER JOIN rel_lctn ON col.org_lctnid=rel_lctn1 INNER JOIN charorg_lctn_alt cola ON rel_lctn2=org_lctn_altid
            INNER JOIN lctn ON org_lctn_altid=lctn_id
            WHERE col.charid='$char_id' AND col.charid=cola.charid AND col.org_lctnid=cola.org_lctnid
            ORDER BY rel_lctn_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related location data (for place of origin): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['rel_lctn_nt1']) {$rel_lctn_nt1=html($row['rel_lctn_nt1']);} else {$rel_lctn_nt1='';}
        if($row['rel_lctn_nt2']) {$rel_lctn_nt2=html($row['rel_lctn_nt2']);} else {$rel_lctn_nt2='';}
        $org_lctns[$row['rel_lctn1']]['rel_lctns'][]=$rel_lctn_nt1.'<a href="/character/origin/'.html($row['lctn_url']).'">'.html($row['lctn_nm']).'</a>'.$rel_lctn_nt2;
      }
    }

    $sql="SELECT prof_id, prof_nm, prof_url FROM charprof INNER JOIN prof ON profid=prof_id WHERE charid='$char_id' ORDER BY prof_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$profs[$row['prof_id']]=array('prof_nm'=>'<a href="/character/profession/'.html($row['prof_url']).'">'.html($row['prof_nm']).'</a>', 'rel_profs'=>array());}

      $sql="SELECT rel_prof1, prof_nm, prof_url FROM charprof INNER JOIN rel_prof ON profid=rel_prof1 INNER JOIN prof ON rel_prof2=prof_id WHERE charid='$char_id' ORDER BY rel_prof_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related profession data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$profs[$row['rel_prof1']]['rel_profs'][]='<a href="/character/profession/'.html($row['prof_url']).'">'.html($row['prof_nm']).'</a>';}
    }

    $sql="SELECT attr_id, attr_nm, attr_url FROM charattr INNER JOIN attr ON attrid=attr_id WHERE charid='$char_id' ORDER BY attr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring attribute data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {$attrs[$row['attr_id']]=array('attr_nm'=>'<a href="/character/attribute/'.html($row['attr_url']).'">'.html($row['attr_nm']).'</a>', 'rel_attrs'=>array());}

      $sql="SELECT rel_attr1, attr_nm, attr_url FROM charattr INNER JOIN rel_attr ON attrid=rel_attr1 INNER JOIN attr ON rel_attr2=attr_id WHERE charid='$char_id' ORDER BY rel_attr_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring related attribute data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$attrs[$row['rel_attr1']]['rel_attrs'][]='<a href="/character/attribute/'.html($row['attr_url']).'">'.html($row['attr_nm']).'</a>';}
    }

    $sql="SELECT abil_nm, abil_url FROM charabil INNER JOIN abil ON abilid=abil_id WHERE charid='$char_id' ORDER BY abil_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring ability data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$abils[]='<a href="/character/ability/'.html($row['abil_url']).'">'.html($row['abil_nm']).'</a>';}

    $char_id=html($char_id);
    include 'character.html.php';
  }
?>