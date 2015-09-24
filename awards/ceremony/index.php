<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_GET['addrequest']))
  {
    include 'addrequest.html.php';
    exit();
  }

  if(isset($_POST['add']) and $_POST['add']=='Add Awards')
  {
    $pagetitle='Add Awards';
    $pagesubtitle='Add new awards to the database.';
    $pagetab='Add Awards | TheatreBase';
    $awrds_nm='';
    $awrd_yr='';
    $awrd_yr_end='';
    $awrd_dt='';
    $thtr_nm='';
    $awrd_list='';
    $textarea='';
    $edit=NULL;
    include 'addform.html.php';
    exit();
  }

  if(isset($_POST['add']) and $_POST['add']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $awrd_id=NULL;
    include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/awrd_vldtn.inc.php';

    if(count($errors)>0)
    {
      $pagetitle='Add Awards';
      $pagesubtitle='Add new awards to the database.';
      $pagetab='Add Awards | TheatreBase';
      $awrds_nm=$_POST['awrds_nm'];
      $awrd_yr=$_POST['awrd_yr'];
      $awrd_yr_end=$_POST['awrd_yr_end'];
      $awrd_dt=$_POST['awrd_dt'];
      $thtr_nm=$_POST['thtr_nm'];
      $awrd_list=$_POST['awrd_list'];
      $textarea=$_POST['textarea'];
      $errors['awrd_add_edit_error']='**There are errors on this page that need amending before submission can be successful.**</br>';
      $edit=NULL;
      include 'addform.html.php';
      exit();
    }
    else
    {
      $edit=NULL;
      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/awrd_insrtn.inc.php';
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS AWARDS CEREMONY HAS BEEN ADDED TO THE DATABASE: '.html($awrd_session);
    header('Location: '.$awrds_url.'/'.$awrd_yr_url);
    exit();
  }

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $awrd_id=cln($_POST['awrd_id']);

    $sql= "SELECT awrds_nm, awrd_yr, awrd_yr_end, awrd_dt, thtr_nm, sbthtr_nm, thtr_lctn, thtr_sffx_num
          FROM awrd
          INNER JOIN awrds ON awrdsid=awrds_id
          LEFT OUTER JOIN thtr ON thtrid=thtr_id
          WHERE awrd_id='$awrd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['awrd_yr_end']) {$awrd_yr_end_dsply=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end_dsply='';}
    $pagetab='Edit: '.html($row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_dsply).' (awards ceremony) | TheatreBase';
    $pagetitle=html($row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_dsply);
    $pagesubtitle='Edit these existing awards.';
    $awrds_nm=html($row['awrds_nm']);
    $awrd_yr=html($row['awrd_yr']);
    $awrd_yr_end=html($row['awrd_yr_end']);
    $awrd_dt=html($row['awrd_dt']);
    if($row['sbthtr_nm']) {$sbthtr_nm=';;'.$row['sbthtr_nm'];} else {$sbthtr_nm='';}
    if($row['thtr_lctn']) {$thtr_lctn='::'.$row['thtr_lctn'];} else {$thtr_lctn='';}
    if($row['thtr_sffx_num']) {$thtr_sffx_num='--'.$row['thtr_sffx_num'];} else {$thtr_sffx_num='';}
    $thtr_nm=html($row['thtr_nm'].$sbthtr_nm.$thtr_lctn.$thtr_sffx_num);

    $sql= "SELECT awrd_ctgry_id, awrd_ctgry_nm, awrd_ctgry_alt_nm
          FROM awrdctgrys
          INNER JOIN awrd_ctgry ON awrd_ctgryid=awrd_ctgry_id
          WHERE awrdid='$awrd_id'
          ORDER BY awrd_ctgry_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['awrd_ctgry_alt_nm']) {$awrd_ctgry_alt_nm=';;'.$row['awrd_ctgry_alt_nm'];} else {$awrd_ctgry_alt_nm='';}
      $awrds[$row['awrd_ctgry_id']]=array('awrd_ctgry_nm'=>$row['awrd_ctgry_nm'].$awrd_ctgry_alt_nm, 'ctgry_noms'=>array());
    }

    $sql= "SELECT awrd_ctgryid, nom_id, nom_win_dscr, win_bool
          FROM awrdnoms
          INNER JOIN awrd_ctgry ON awrd_ctgryid=awrd_ctgry_id
          WHERE awrdid='$awrd_id'
          ORDER BY nom_id ASC";
      $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award nominations data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['win_bool']) {$win_bool='*';} else {$win_bool='';}
      $awrds[$row['awrd_ctgryid']]['ctgry_noms'][$row['nom_id']]=array('nom_win_dscr'=>$row['nom_win_dscr'], 'win_bool'=>$win_bool, 'ctgry_nomppl'=>array(), 'ctgry_nomprds'=>array(), 'ctgry_nompts'=>array());
    }

    $sql= "SELECT an.awrd_ctgryid, nomid, nom_ordr, nom_rl, comp_id, comp_nm AS comp_nm1, comp_nm AS comp_nm2, comp_sffx_num, comp_bool
          FROM awrdnomppl anp
          INNER JOIN awrdnoms an ON anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
          INNER JOIN comp ON nom_compid=comp_id
          WHERE anp.awrdid='$awrd_id' AND an.awrdid='$awrd_id' AND nom_prsnid='0'
          UNION
          SELECT an.awrd_ctgryid, nomid, nom_ordr, nom_rl, prsn_id, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num, comp_bool
          FROM awrdnomppl anp
          INNER JOIN awrdnoms an ON anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
          INNER JOIN prsn ON nom_prsnid=prsn_id
          WHERE anp.awrdid='$awrd_id' AND an.awrdid='$awrd_id' AND nom_compid='0'
          ORDER BY nom_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards nominee/winner (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['comp_bool'])
      {
        if($row['comp_nm1'])
        {
          if($row['comp_sffx_num']) {$comp_sffx_num='--'.$row['comp_sffx_num'];} else {$comp_sffx_num='';}
          if($row['nom_rl']) {$nom_rl='~~'.$row['nom_rl'];} else {$nom_rl='';}
          $comp_nom_nm_rl=$row['comp_nm1'].$comp_sffx_num.$nom_rl.'||';
        }
        else
        {$comp_nom_nm_rl='';}
        $prsn_nom_nm_rl='';
      }
      else
      {
        if($row['comp_nm1'])
        {
          if($row['comp_sffx_num']) {$prsn_sffx_num='--'.$row['comp_sffx_num'];} else {$prsn_sffx_num='';}
          if($row['nom_rl']) {$nom_rl='~~'.$row['nom_rl'];} else {$nom_rl='';}
          $prsn_nom_nm_rl=$row['comp_nm1'].';;'.$row['comp_nm2'].$prsn_sffx_num.$nom_rl;
        }
        else
        {$prsn_nom_nm_rl='';}
        $comp_nom_nm_rl='';
      }
      $awrds[$row['awrd_ctgryid']]['ctgry_noms'][$row['nomid']]['ctgry_nomppl'][$row['comp_id']]=array('comp_nom_nm_rl'=>$comp_nom_nm_rl, 'prsn_nom_nm_rl'=>$prsn_nom_nm_rl, 'nomcomp_ppl'=>array());
    }

    $sql= "SELECT an.awrd_ctgryid, nomid, nom_rl, nom_compid, prsn_frst_nm, prsn_lst_nm, prsn_sffx_num
          FROM awrdnomppl anp
          INNER JOIN awrdnoms an ON anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
          INNER JOIN prsn ON nom_prsnid=prsn_id
          WHERE anp.awrdid='$awrd_id' AND an.awrdid='$awrd_id' AND nom_compid!='0'
          ORDER BY nom_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards nomination/win (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['prsn_sffx_num']) {$prsn_sffx_num='--'.$row['prsn_sffx_num'];} else {$prsn_sffx_num='';}
      if($row['nom_rl']) {$nom_rl='~~'.$row['nom_rl'];} else {$nom_rl='';}
      $awrds[$row['awrd_ctgryid']]['ctgry_noms'][$row['nomid']]['ctgry_nomppl'][$row['nom_compid']]['nomcomp_ppl'][]=$row['prsn_frst_nm'].';;'.$row['prsn_lst_nm'].$prsn_sffx_num.$nom_rl;
    }

    $sql= "SELECT an.awrd_ctgryid, nomid, prd_id, COALESCE(prd_alph, prd_nm)prd_alph
          FROM awrdnomprds anp
          INNER JOIN awrdnoms an ON anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
          INNER JOIN prd ON nom_prdid=prd_id
          WHERE anp.awrdid='$awrd_id' AND an.awrdid='$awrd_id'
          ORDER BY prd_frst_dt DESC, prd_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards nominee/winner (productions) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {$awrds[$row['awrd_ctgryid']]['ctgry_noms'][$row['nomid']]['ctgry_nomprds'][]=$row['prd_id'];}

    $sql= "SELECT an.awrd_ctgryid, nomid, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_sffx_num, COALESCE(pt_alph, pt_nm)pt_alph
          FROM awrdnompts anp
          INNER JOIN awrdnoms an ON anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
          INNER JOIN pt ON nom_ptid=pt_id
          WHERE anp.awrdid='$awrd_id' AND an.awrdid='$awrd_id'
          ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards nominee/winner (productions) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['pt_yr_strtd']) {$pt_yr_strtd=$row['pt_yr_strtd'].';;';} else {$pt_yr_strtd='';}
      if($row['pt_yr_strtd_c']) {$pt_yr_strtd='c'.$pt_yr_strtd;}
      if($row['pt_yr_wrttn_c']) {$pt_yr_wrttn='c'.$row['pt_yr_wrttn'];} else {$pt_yr_wrttn=$row['pt_yr_wrttn'];}
      if($row['pt_sffx_num']) {$pt_sffx_num='--'.$row['pt_sffx_num'];} else {$pt_sffx_num='';}
      $pt=$row['pt_nm'].'##'.$pt_yr_strtd.$pt_yr_wrttn.$pt_sffx_num;
      $awrds[$row['awrd_ctgryid']]['ctgry_noms'][$row['nomid']]['ctgry_nompts'][]=$pt;
    }

    if(!empty($awrds))
    {
      $awrd_array=array();
      foreach($awrds as $awrd)
      {
        $ctgry_array=array();
        foreach($awrd['ctgry_noms'] as $ctgry)
        {
          $nomppl_array=array();
          foreach($ctgry['ctgry_nomppl'] as $nom_prsn)
          {
            $nomcomp_ppl_list=implode('//', $nom_prsn['nomcomp_ppl']);
            $nomppl_array[]=$nom_prsn['comp_nom_nm_rl'].$nom_prsn['prsn_nom_nm_rl'].$nomcomp_ppl_list;
          }
          if(!empty($nomppl_array)) {$nomppl_list=implode('>>', $nomppl_array);} else {$nomppl_list='';}
          if(!empty($ctgry['ctgry_nomprds'])) {$nomprds_list='##'.implode('>>', $ctgry['ctgry_nomprds']);} else {$nomprds_list='';}
          if(!empty($ctgry['ctgry_nompts'])) {$nompts_list='++'.implode('>>', $ctgry['ctgry_nompts']);} else {$nompts_list='';}
          $ctgry_array[]=$ctgry['nom_win_dscr'].$ctgry['win_bool'].'::'.$nomppl_list.$nomprds_list.$nompts_list;
          $ctgry_list=implode(',,', $ctgry_array);
        }
        $awrd_array[]=$awrd['awrd_ctgry_nm'].'=='.$ctgry_list;
      }
      $awrd_list=html(implode('@@', $awrd_array));
    }
    else
    {$awrd_list='';}

    $textarea='';
    $awrd_id=html($awrd_id);
    $edit='1';
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $awrd_id=cln($_POST['awrd_id']);
    include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/awrd_vldtn.inc.php';

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $awrd_id=cln($_POST['awrd_id']);
      $sql="SELECT awrds_nm, awrd_yr, awrd_yr_end FROM awrd INNER JOIN awrds ON awrdsid=awrds_id WHERE awrd_id='$awrd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['awrd_yr_end']) {$awrd_yr_end_dsply=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end_dsply='';}
      $pagetab='Edit: '.html($row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_dsply).' (awards ceremony) | TheatreBase';
      $pagetitle=html($row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_dsply);
      $pagesubtitle='Edit these existing awards.';
      $awrds_nm=$_POST['awrds_nm'];
      $awrd_yr=$_POST['awrd_yr'];
      $awrd_yr_end=$_POST['awrd_yr_end'];
      $awrd_dt=$_POST['awrd_dt'];
      $thtr_nm=$_POST['thtr_nm'];
      $awrd_list=$_POST['awrd_list'];
      $textarea=$_POST['textarea'];
      $errors['awrd_add_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $awrd_id=html($awrd_id);
      $edit='1';
      include 'editform.html.php';
      exit();
    }
    else
    {
      $edit='1';

      $sql="DELETE FROM awrdctgrys WHERE awrdid='$awrd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting award-category associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM awrdnoms WHERE awrdid='$awrd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting award-nomination/win associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM awrdnompts WHERE awrdid='$awrd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting award-nominees/winners (playtexts) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM awrdnomprds WHERE awrdid='$awrd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting award-nominees/winners (productions) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      $sql="DELETE FROM awrdnomppl WHERE awrdid='$awrd_id'";
      if(!mysqli_query($link, $sql)) {$error='Error deleting award-nominees/winners (company/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

      include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/awrd_insrtn.inc.php';
    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS AWARDS CEREMONY HAS BEEN EDITED: '.html($awrd_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/awards/ceremony/'.$awrds_url.'/'.$awrd_yr_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $awrd_id=cln($_POST['awrd_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM awrdnoms WHERE awrdid='$awrd_id' LIMIT 1";
    $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring prod-awards association details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Nomination/Win';}

    if(count($assocs)>0)
    {$errors['awrd_dlt']='**Awards must have no associations before they can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql= "SELECT awrds_nm, awrd_yr, awrd_yr_end
            FROM awrd
            INNER JOIN awrds ON awrdsid=awrds_id
            WHERE awrd_id='$awrd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['awrd_yr_end']) {$awrd_yr_end_dsply=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end_dsply='';}
      $pagetab='Edit: '.html($row['awrds_nm'].' ' .$row['awrd_yr'].$awrd_yr_end_dsply).' (awards ceremony) | TheatreBase';
      $pagetitle=html($row['awrds_nm'].' ' .$row['awrd_yr'].$awrd_yr_end_dsply);
      $pagesubtitle='Edit these existing awards.';
      $awrds_nm=$_POST['awrds_nm'];
      $awrd_yr=$_POST['awrd_yr'];
      $awrd_yr_end=$_POST['awrd_yr_end'];
      $awrd_dt=$_POST['awrd_dt'];
      $thtr_nm=$_POST['thtr_nm'];
      $awrd_list=$_POST['awrd_list'];
      $textarea=$_POST['textarea'];
      $awrd_id=html($awrd_id);
      $edit='1';
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "SELECT awrds_nm, awrd_yr, awrd_yr_end
            FROM awrd
            INNER JOIN awrds ON awrdsid=awrds_id
            WHERE awrd_id='$awrd_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if($row['awrd_yr_end']) {$awrd_yr_end_dsply=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end_dsply='';}
      $pagetab= 'Delete confirmation: '.html($row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_dsply).' (awards ceremony) | TheatreBase';
      $pagetitle=html($row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_dsply);
      $awrd_id=html($awrd_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $awrd_id=cln($_POST['awrd_id']);
    $sql= "SELECT awrds_nm, awrd_yr, awrd_yr_end
          FROM awrd
          INNER JOIN awrds ON awrdsid=awrds_id
          WHERE awrd_id='$awrd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['awrd_yr_end']) {$awrd_yr_end_dsply=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end_dsply='';}
    $awrd_session=$row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_dsply;

    $sql="DELETE FROM awrdnoms WHERE awrdid='$awrd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting award-award nominations associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM awrdnomprds WHERE awrdid='$awrd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting award-award nominations (productions) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM awrdnompts WHERE awrdid='$awrd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting award-award nominations (playtexts) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM awrdnomppl WHERE awrdid='$awrd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting award-award nominations (companies/people) associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM awrd WHERE awrd_id='$awrd_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting awards: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS AWARDS CEREMONY HAS BEEN DELETED FROM THE DATABASE:'.' '.html($awrd_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $awrd_id=cln($_POST['awrd_id']);
    $sql= "SELECT awrds_url, awrd_yr_url
          FROM awrd
          INNER JOIN awrds ON awrdsid=awrds_id
          WHERE awrd_id='$awrd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['awrd_yr_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $awrds_url=cln($_GET['awrds_url']);
  $awrd_yr_url=cln($_GET['awrd_yr_url']);

  $sql= "SELECT awrd_id
        FROM awrd
        INNER JOIN awrds ON awrdsid=awrds_id
        WHERE awrds_url='$awrds_url' AND awrd_yr_url='$awrd_yr_url'";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URL has given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $awrd_id=$row['awrd_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT awrds_nm, awrds_url, awrd_yr, awrd_yr_end, DATE_FORMAT(awrd_dt, '%a, %d %b %Y') AS awrd_dt,
          t1.thtr_nm, t1.sbthtr_nm, t1.thtr_lctn, t1.thtr_url, t1.thtr_tr_ov, t2.thtr_url AS t2_thtr_url
          FROM awrd
          INNER JOIN awrds ON awrdsid=awrds_id
          LEFT OUTER JOIN thtr t1 ON thtrid=t1.thtr_id LEFT OUTER JOIN thtr t2 ON t1.srthtrid=t2.thtr_id
          WHERE awrd_id='$awrd_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    if($row['awrd_yr_end']) {$awrd_yr_end_dsply=preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']); $awrd_yr_end_othr=' and <a href="/awards/list/'.html($row['awrd_yr_end']).'">'.html($row['awrd_yr_end']).'</a>';}
    else {$awrd_yr_end_dsply=''; $awrd_yr_end_othr='';}
    $pagetitle=html($row['awrds_nm'].' '.$row['awrd_yr'].$awrd_yr_end_dsply);
    $awrds_nm='<a href="/awards/'.html($row['awrds_url']).'">'.html($row['awrds_nm']).'</a>';
    $awrds_url=$row['awrds_url'];
    $awrd_dt=$row['awrd_dt'];
    $awrd_yr_othr='<a href="/awards/year/'.html($row['awrd_yr']).'">'.html($row['awrd_yr']).'</a>';

    if($row['thtr_nm'])
    {
      if($row['thtr_lctn']) {$thtr_lctn=' ('.html($row['thtr_lctn']).')';} else {$thtr_lctn='';}
      if(!$row['thtr_tr_ov'])
      {
        if(!preg_match('/TBC$/', $row['thtr_nm']))
        {
          if($row['sbthtr_nm'])
          {
            if($row['t2_thtr_url']) {$thtr_dsply='<a href="/theatre/'.html($row['t2_thtr_url']).'">'.html($row['thtr_nm']).'</a>: <a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>'.$thtr_lctn;}
            else {$thtr_dsply=html($row['thtr_nm']).': <a href="/theatre/'.html($row['thtr_url']).'">'.html($row['sbthtr_nm']).'</a>'.$thtr_lctn;}
          }
          else {$thtr_dsply='<a href="/theatre/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>'.$thtr_lctn;}
        }
        else {$thtr_dsply='<em>TBC</em>';}
      }
      else
      {
        if(!preg_match('/TBC$/', $row['thtr_nm'])) {$thtr_dsply='<a href="/tour-type/'.html($row['thtr_url']).'">'.html($row['thtr_nm']).'</a>';}
        else {$thtr_dsply='<em>TBC</em>';}
      }
    }
    else
    {$thtr_dsply=NULL;}

    $sql= "SELECT a2.awrd_id, a2.awrd_yr, a2.awrd_yr_end, a2.awrd_yr_url, awrds_url
          FROM awrd a1
          INNER JOIN awrds ON a1.awrdsid=awrds_id INNER JOIN awrd a2 ON awrds_id=a2.awrdsid
          WHERE a1.awrd_id='$awrd_id'
          ORDER BY a2.awrd_yr DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['awrd_yr_end']) {$awrd_yr_end=preg_replace('/([0-9]{2})([0-9]{2})$', '/$2', $row['awrd_yr_end']);} else {$awrd_yr_end='';}
      if($row['awrd_id']!==$awrd_id)
      {$awrd_yr='<a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrd_yr']).html($awrd_yr_end).'</a>';}
      else {$awrd_yr=html($row['awrd_yr']).html($awrd_yr_end);}
      $awrds_yrs[]=$awrd_yr;
    }

    $sql= "SELECT awrd_ctgry_id, awrds_url, awrd_yr_url, awrd_ctgry_nm, awrd_ctgry_url, COALESCE(awrd_ctgry_alt_nm, awrd_ctgry_nm)awrd_ctgry_nm
          FROM awrdnoms an
          INNER JOIN awrd_ctgry ON an.awrd_ctgryid=awrd_ctgry_id INNER JOIN awrd ON an.awrdid=awrd_id
          INNER JOIN awrds ON awrdsid=awrds_id INNER JOIN awrdctgrys ac ON an.awrdid=ac.awrdid AND an.awrd_ctgryid=ac.awrd_ctgryid
          WHERE an.awrdid='$awrd_id'
          GROUP BY awrd_ctgry_id
          ORDER BY awrd_ctgry_ordr ASC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award categories data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        $awrd_ctgrys[]='<a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'#'.html($row['awrd_ctgry_url']).'">'.html($row['awrd_ctgry_nm']).'</a>';
        $pst_nom_wns='<a href="/awards/category/'.html($awrds_url).'/'.html($row['awrd_ctgry_url']). '">past nominees/winners</a>';
        $awrds[$row['awrd_ctgry_id']]=array('awrd_ctgry_nm'=>html($row['awrd_ctgry_nm']), 'awrd_ctgry_url'=>html($row['awrd_ctgry_url']), 'pst_nom_wns'=>$pst_nom_wns, 'noms'=>array());
      }

      $sql= "SELECT awrd_ctgry_id, nom_id, nom_win_dscr, win_bool
            FROM awrdnoms
            INNER JOIN awrd_ctgry ON awrd_ctgryid=awrd_ctgry_id
            WHERE awrdid='$awrd_id'
            GROUP BY awrd_ctgry_id, nom_id
            ORDER BY nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award nominations data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$awrds[$row['awrd_ctgry_id']]['noms'][$row['nom_id']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'win'=>$row['win_bool'], 'nomppl'=>array(), 'nomprds'=>array(), 'nompts'=>array());}

      $sql= "SELECT an.awrd_ctgryid, nom_id, nom_ordr, nom_rl, comp_id, comp_nm, comp_url, comp_bool
            FROM awrdnomppl anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id INNER JOIN comp ON nom_compid=comp_id
            WHERE anp.awrdid='$awrd_id' AND nom_prsnid=0
            GROUP BY awrd_ctgryid, nom_id, comp_id
            UNION
            SELECT an.awrd_ctgryid, nom_id, nom_ordr, nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool
            FROM awrdnomppl anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id INNER JOIN prsn ON nom_prsnid=prsn_id
            WHERE anp.awrdid='$awrd_id' AND nom_compid=0
            GROUP BY awrd_ctgryid, nom_id, prsn_id
            ORDER BY nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$nom_prsn='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
        else {$nom_prsn=html($row['comp_nm']).$nom_rl;}
        $awrds[$row['awrd_ctgryid']]['noms'][$row['nom_id']]['nomppl'][$row['comp_id']]=array('nom_prsn'=>$nom_prsn, 'nomcomp_ppl'=>array());
      }

      $sql= "SELECT an.awrd_ctgryid, nom_id, nom_rl, nom_compid, prsn_fll_nm, prsn_url
            FROM awrdnomppl anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id INNER JOIN prsn ON nom_prsnid=prsn_id
            WHERE anp.awrdid='$awrd_id' AND nom_compid!='0'
            GROUP BY awrd_ctgryid, nom_id, nom_compid, prsn_id
            ORDER BY nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nomination/win (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
        $awrds[$row['awrd_ctgryid']]['noms'][$row['nom_id']]['nomppl'][$row['nom_compid']]['nomcomp_ppl'][]=$prsn_nm;
      }

      $sql= "SELECT an.awrd_ctgryid, nom_id, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm
            FROM awrdnomprds anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
            INNER JOIN prd ON nom_prdid=prd_id INNER JOIN thtr ON thtrid=thtr_id
            WHERE anp.awrdid='$awrd_id'
            GROUP BY awrd_ctgryid, nom_id, prd_id
            ORDER BY prd_frst_dt DESC, prd_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (productions) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $awrds[$row['awrd_ctgryid']]['noms'][$row['nom_id']]['nomprds'][]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
      }

      $sql= "SELECT an.awrd_ctgryid, nom_id, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph
            FROM awrdnompts anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id INNER JOIN pt ON nom_ptid=pt_id
            WHERE anp.awrdid='$awrd_id'
            GROUP BY awrd_ctgryid, nom_id, pt_id
            ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (playtexts) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $awrds[$row['awrd_ctgryid']]['noms'][$row['nom_id']]['nompts'][]=$pt_nm.' ('.$pt_yr.')';
      }
    }

    $awrd_id=html($awrd_id);
    include 'awards-ceremony.html.php';
  }
?>