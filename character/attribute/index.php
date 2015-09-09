<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $attr_id=cln($_POST['attr_id']);
    $sql="SELECT attr_nm FROM attr WHERE attr_id='$attr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring attribute details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['attr_nm']);
    $pagetitle=html($row['attr_nm']);
    $attr_nm=html($row['attr_nm']);

    $sql="SELECT attr_nm FROM rel_attr INNER JOIN attr ON rel_attr2=attr_id WHERE rel_attr1='$attr_id' ORDER BY rel_attr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related attribute data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_attrs[]=$row['attr_nm'];}
    if(!empty($rel_attrs)) {$rel_attr_list=html(implode(',,', $rel_attrs));} else {$rel_attr_list='';}

    $attr_id=html($attr_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $attr_id=cln($_POST['attr_id']);
    $attr_nm=trim(cln($_POST['attr_nm']));
    $rel_attr_list=cln($_POST['rel_attr_list']);
    $attr_url=generateurl($attr_nm);
    $attr_nm_session=$_POST['attr_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $attr_nm))
    {$errors['attr_nm']='**You must enter an attribute name.**';}
    elseif(strlen($attr_nm)>255)
    {$errors['attr_nm']='</br>**Attribute name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/,,/', $attr_nm))
    {$errors['attr_nm']='**Attribute name cannot include the following: [,,].**';}
    else
    {
      $sql="SELECT attr_id, attr_nm FROM attr WHERE attr_url='$attr_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing attribute URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['attr_id']!==$attr_id)
      {$errors['attr_url']='</br>**Duplicate URL exists for: '.html($row['attr_nm']). '. You must keep the original name or assign an attribute name without an existing URL.**';}
    }

    if(preg_match('/\S+/', $rel_attr_list))
    {
      $rel_attr_nms=explode(',,', $_POST['rel_attr_list']);
      if(count($rel_attr_nms)>250)
      {$errors['rel_attr_nm_array_excss']='**Maximum of 250 entries allowed.**';}
      else
      {
        $rel_attr_empty_err_arr=array(); $rel_attr_dplct_arr=array(); $rel_attr_url_err_arr=array();
        $rel_attr_inv_comb_err_arr=array();
        foreach($rel_attr_nms as $rel_attr_nm)
        {
          $rel_attr_errors=0;

          $rel_attr_nm=trim($rel_attr_nm);
          if(!preg_match('/\S+/', $rel_attr_nm))
          {
            $rel_attr_errors++; $rel_attr_empty_err_arr[]=$rel_attr_nm;
            if(count($rel_attr_empty_err_arr)==1) {$errors['rel_attr_empty']='</br>**There is 1 empty entry in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
            else {$errors['rel_attr_empty']='</br>**There are '.count($rel_attr_empty_err_arr).' empty entries in the array (caused by four consecutive commas [,,,,] or two commas [,,] with no text beforehand or thereafter).**';}
          }
          else
          {
            $rel_attr_url=generateurl($rel_attr_nm);

            $rel_attr_dplct_arr[]=$rel_attr_url;
            if(count(array_unique($rel_attr_dplct_arr))<count($rel_attr_dplct_arr))
            {$errors['rel_attr_dplct']='</br>**There are entries within the array that create duplicate URLs.**';}

            if(strlen($rel_attr_nm)>255 || strlen($rel_attr_url)>255)
            {$rel_attr_errors++; $errors['rel_attr_nm_excss_lngth']='</br>**Attribute name and its URL are allowed a maximum of 255 characters each. Please amend entries that exceed this amount.**';}

            if($rel_attr_errors==0)
            {
              $rel_attr_nm_cln=cln($rel_attr_nm);
              $rel_attr_url_cln=cln($rel_attr_url);

              $sql="SELECT attr_nm FROM attr WHERE NOT EXISTS (SELECT 1 FROM attr WHERE attr_nm='$rel_attr_nm_cln') AND attr_url='$rel_attr_url_cln'";
              $result=mysqli_query($link, $sql);
              if(!$result) {$error='Error checking for existing attribute URL (for duplicate URL check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
              $row=mysqli_fetch_array($result);
              if(mysqli_num_rows($result)>0)
              {
                $rel_attr_url_err_arr[]=$row['attr_nm'];
                if(count($rel_attr_url_err_arr)==1)
                {$errors['rel_attr_nm']='</br>**Duplicate URL exists. Did you mean to type: '.html(implode(' / ', $rel_attr_url_err_arr)).'?**';}
                else
                {$errors['rel_attr_nm']='</br>**Duplicate URLs exist. Did you mean to type: '.html(implode(' / ', $rel_attr_url_err_arr)).'?**';}
              }
              else
              {
                $sql="SELECT attr_id FROM attr WHERE attr_url='$rel_attr_url_cln'";
                $result=mysqli_query($link, $sql);
                if(!$result) {$error='Error checking for existing attribute URL (for existing attribute check): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                $row=mysqli_fetch_array($result);
                if($row['attr_id']==$attr_id)
                {$errors['rel_attr_id_mtch']='</br>**You cannot assign this attribute as a related attribute of itself: '.html($rel_attr_nm).'.**';}
                else
                {
                  $rel_attr_id=$row['attr_id'];
                  $sql="SELECT 1 FROM rel_attr WHERE rel_attr2='$attr_id' AND rel_attr1='$rel_attr_id'";
                  $result=mysqli_query($link, $sql);
                  if(!$result) {$error='Error checking for inverse of proposed combination: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
                  $row=mysqli_fetch_array($result);
                  if(mysqli_num_rows($result)>0)
                  {
                    $rel_attr_inv_comb_err_arr[]=$rel_attr_nm;
                    $errors['rel_attr_inv_comb']='</br>**The following locations cause an invalid inverse of existing attribute-relationship combinations: '.html(implode(' / ', $rel_attr_inv_comb_err_arr)).'.**';
                  }
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

      $attr_id=cln($_POST['attr_id']);
      $sql="SELECT attr_nm FROM attr WHERE attr_id='$attr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring attribute details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['attr_nm']);
      $pagetitle=html($row['attr_nm']);
      $attr_nm=$_POST['attr_nm'];
      $rel_attr_list=$_POST['rel_attr_list'];
      $errors['attr_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $attr_id=html($attr_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE attr SET
            attr_nm='$attr_nm',
            attr_url='$attr_url'
            WHERE attr_id='$attr_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating theatre info for submitted attribute: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM rel_attr WHERE rel_attr1='$attr_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting attribute-related attribute associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      if(preg_match('/\S+/', $rel_attr_list))
      {
        $rel_attr_nms=explode(',,', $rel_attr_list);
        $n=0;
        foreach($rel_attr_nms as $rel_attr_nm)
        {
          $rel_attr_ordr=++$n;
          $rel_attr_url=generateurl($rel_attr_nm);

          $sql="SELECT 1 FROM attr WHERE attr_url='$rel_attr_url'";
          $result=mysqli_query($link, $sql);
          if(!$result) {$error='Error checking for existence of attribute: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          if(mysqli_num_rows($result)==0)
          {
            $sql="INSERT INTO attr(attr_nm, attr_url) VALUES('$rel_attr_nm', '$rel_attr_url')";
            if(!mysqli_query($link, $sql)) {$error='Error adding attribute data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
          }

          $sql= "INSERT INTO rel_attr(rel_attr_ordr, rel_attr1, rel_attr2)
                SELECT '$rel_attr_ordr', '$attr_id', attr_id FROM attr WHERE attr_url='$rel_attr_url'";
          if(!mysqli_query($link, $sql)) {$error='Error adding attribute-related attribute association data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        }
      }
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS ATTRIBUTE HAS BEEN EDITED:'.' '.html($attr_nm_session);
    header('Location: '.$attr_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $attr_id=cln($_POST['attr_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM charattr WHERE attrid='$attr_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring character-attribute association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Character';}

    if(count($assocs)>0)
    {$errors['attr_dlt']='**Attribute must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT attr_nm
            FROM attr
            WHERE attr_id='$attr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring attribute details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['attr_nm']);
      $pagetitle=html($row['attr_nm']);
      $attr_nm=$_POST['attr_nm'];
      $rel_attr_list=$_POST['rel_attr_list'];
      $attr_id=html($attr_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT attr_nm FROM attr WHERE attr_id='$attr_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$pagetitle='Error'; $error='Error acquiring production details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Delete confirmation: '.html($row['attr_nm']);
      $pagetitle=html($row['attr_nm']);
      $attr_id=html($attr_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $attr_id=cln($_POST['attr_id']);
    $sql="SELECT attr_nm FROM attr WHERE attr_id='$attr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring attribute details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $attr_nm_session=$row['attr_nm'];

    $sql="DELETE FROM charattr WHERE attrid='$attr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting attribute-character associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM rel_attr WHERE rel_attr1='$attr_id' OR rel_attr2='$attr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting attribute-related attribute associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM attr WHERE attr_id='$attr_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting attribute: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS ATTRIBUTE HAS BEEN DELETED FROM THE DATABASE:'.' '.html($attr_nm_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');e.
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $attr_id=cln($_POST['attr_id']);
    $sql="SELECT attr_url FROM attr WHERE attr_id='$attr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring attribute URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);

    header('Location: '.$row['attr_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $attr_url=cln($_GET['attr_url']);

  $sql="SELECT attr_id FROM attr WHERE attr_url='$attr_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $attr_id=$row['attr_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql="SELECT attr_nm FROM attr WHERE attr_id='$attr_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring attribute data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetitle=html($row['attr_nm']);
    $attr_nm=html($row['attr_nm']);

    $sql= "SELECT attr_nm, attr_url FROM rel_attr INNER JOIN attr ON rel_attr2=attr_id
          WHERE rel_attr1='$attr_id' AND (EXISTS(SELECT 1 FROM charattr WHERE attrid='$attr_id') OR EXISTS(SELECT 1 FROM rel_attr INNER JOIN charattr ON rel_attr1=attrid WHERE rel_attr2='$attr_id'))
          ORDER BY rel_attr_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related attribute (part of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_attrs2[]='<a href="/character/attribute/'.html($row['attr_url']).'">'.html($row['attr_nm']).'</a>';}

    $sql= "SELECT attr_nm, attr_url FROM rel_attr INNER JOIN charattr ON rel_attr1=attrid INNER JOIN attr ON attrid=attr_id WHERE rel_attr2='$attr_id'
          UNION
          SELECT attr_nm, attr_url FROM rel_attr ra1
          INNER JOIN charattr ON ra1.rel_attr1=attrid INNER JOIN rel_attr ra2 ON attrid=ra2.rel_attr1 INNER JOIN attr ON ra2.rel_attr2=attr_id
          WHERE ra1.rel_attr2='$attr_id' AND attr_id!=ra1.rel_attr2 AND attr_id IN(SELECT rel_attr1 FROM rel_attr WHERE rel_attr2='$attr_id')
          ORDER BY attr_nm ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring related attribute (comprised of) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$rel_attrs1[]='<a href="/character/attribute/'.html($row['attr_url']).'">'.html($row['attr_nm']).'</a>';}

    $char_ids=array();

    $k=0;
    $sql= "SELECT char_id, char_nm, char_sffx_num, char_url, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti,
          COALESCE(char_alph, char_nm)char_alph, NULL AS attr_nm, (SELECT COUNT(*) FROM ptchar WHERE charid=char_id) AS pt_cnt
          FROM charattr INNER JOIN role ON charid=char_id WHERE attrid='$attr_id' GROUP BY char_id
          UNION
          SELECT char_id, char_nm, char_sffx_num, char_url, char_sx, char_age_frm, char_age_to, char_dscr, char_amnt, char_mlti,
          COALESCE(char_alph, char_nm)char_alph, GROUP_CONCAT(DISTINCT attr_nm ORDER BY attr_ordr ASC SEPARATOR ' / ') AS attr_nm, (SELECT COUNT(*) FROM ptchar WHERE charid=char_id) AS pt_cnt
          FROM rel_attr INNER JOIN attr ON rel_attr1=attr_id INNER JOIN charattr ON attr_id=attrid INNER JOIN role ON charid=char_id
          WHERE rel_attr2='$attr_id' GROUP BY char_id
          ORDER BY char_alph ASC, char_sffx_num ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring character data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['char_sx']=='2') {$char_sx='Male';} elseif($row['char_sx']=='3') {$char_sx='Female';} elseif($row['char_sx']=='4') {$char_sx='Non-specific';} else {$char_sx=NULL;}if($row['char_age_frm']==$row['char_age_to']) {$char_age=html($row['char_age_frm']);} else {$char_age=html($row['char_age_frm']).' - '.html($row['char_age_to']);}
      if($row['char_dscr']) {$char_dscr='<em>'.html($row['char_dscr']).'</em>';} else {$char_dscr=NULL;}
      if($row['char_amnt']>1) {$char_amnt=' ['.html($row['char_amnt']).']';} elseif($row['char_mlti']) {$char_amnt=' [<em>multiple roles</em>]';} else {$char_amnt=NULL;}
      $char_nm='<a href="/character/'.html($row['char_url']).'">'.html($row['char_nm']).'</a>';
      $pt_cnt=$row['pt_cnt']-3;
      if($row['attr_nm'] && html($row['attr_nm'])!==$attr_nm) {$k++;}
      $char_ids[]=$row['char_id'];
      $chars[$row['char_id']]=array('char_nm'=>$char_nm, 'char_sx'=>$char_sx, 'char_age'=>$char_age, 'char_dscr'=>$char_dscr, 'char_amnt'=>$char_amnt, 'attr_nm'=>html($row['attr_nm']), 'pt_cnt'=>$pt_cnt, 'pts'=>array());
    }

    if(!empty($char_ids))
    {
      foreach($char_ids as $char_id)
      {
        $sql= "SELECT charid, pt_nm, pt_url, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, COALESCE(pt_alph, pt_nm)pt_alph
              FROM ptchar
              INNER JOIN pt ON ptid=pt_id WHERE charid='$char_id'
              GROUP BY charid, pt_id ORDER BY pt_yr_wrttn ASC, coll_ordr ASC, pt_alph DESC LIMIT 3";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring character playtext data data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
          $pt_nm=html($row['pt_nm']);
          $chars[$row['charid']]['pts'][]=$pt_nm.' ('.$pt_yr.')';
        }
      }
    }

    $attr_id=html($attr_id);
    include 'character-attribute.html.php';
  }
?>