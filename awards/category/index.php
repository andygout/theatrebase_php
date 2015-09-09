<?php
  include_once $_SERVER['DOCUMENT_ROOT'].'/includes/helpers.inc.php';

  if(isset($_POST['action']) and $_POST['action']=='Edit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $awrd_ctgry_id=cln($_POST['awrd_ctgry_id']);
    $sql="SELECT awrd_ctgry_nm FROM awrd_ctgry WHERE awrd_ctgry_id='$awrd_ctgry_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award category details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $pagetab='Edit: '.html($row['awrd_ctgry_nm']);
    $pagetitle=html($row['awrd_ctgry_nm']);
    $awrd_ctgry_nm=html($row['awrd_ctgry_nm']);
    $awrd_ctgry_id=html($awrd_ctgry_id);
    include 'editform.html.php';
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Submit')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $awrd_ctgry_id=cln($_POST['awrd_ctgry_id']);
    $awrd_ctgry_nm=trim(cln($_POST['awrd_ctgry_nm']));
    $awrd_ctgry_session=$_POST['awrd_ctgry_nm'];

    $errors=array();

    if(!preg_match('/\S+/', $awrd_ctgry_nm))
    {$errors['awrd_ctgry_nm']='**You must enter an award category name.**';}
    elseif(strlen($awrd_ctgry_nm)>255)
    {$errors['awrd_ctgry_nm']='</br>**Award category name is allowed a maximum of 255 characters.**';}
    elseif(preg_match('/@@/', $awrd_ctgry_nm) || preg_match('/==/', $awrd_ctgry_nm) || preg_match('/;;/', $awrd_ctgry_nm))
    {$errors['awrd_ctgry_nm']='**Award category cannot include any of the following: [@@], [==], [;;].**';}
    else
    {
      $awrd_ctgry_url=generateurl($awrd_ctgry_nm);
      $awrd_ctgry_alph=alph($awrd_ctgry_nm);

      $sql="SELECT awrd_ctgry_id, awrd_ctgry_nm FROM awrd_ctgry WHERE awrd_ctgry_url='$awrd_ctgry_url'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error checking for existing award category URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      if(mysqli_num_rows($result)>0 && $row['awrd_ctgry_id']!==$awrd_ctgry_id)
      {$errors['awrd_ctgry_nm']='</br>**Duplicate URL exists for: '.html($row['awrd_ctgry_nm']). '. You must keep the original name or assign an award category name without an existing URL.**';}
    }

    if(count($errors)>0)
    {
      include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

      $awrd_ctgry_id=cln($_POST['awrd_ctgry_id']);
      $sql="SELECT awrd_ctgry_nm FROM awrd_ctgry WHERE awrd_ctgry_id='$awrd_ctgry_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award category details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['awrd_ctgry_nm']);
      $pagetitle=html($row['awrd_ctgry_nm']);
      $awrd_ctgry_nm=$_POST['awrd_ctgry_nm'];
      $errors['awrd_ctgry_edit_error']='**There are errors on this page that need amending before submission can be successful.**';
      $awrd_ctgry_id=html($awrd_ctgry_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql= "UPDATE awrd_ctgry SET
            awrd_ctgry_nm='$awrd_ctgry_nm',
            awrd_ctgry_alph=CASE WHEN '$awrd_ctgry_alph'!='' THEN '$awrd_ctgry_alph' END,
            awrd_ctgry_url='$awrd_ctgry_url'
            WHERE awrd_ctgry_id='$awrd_ctgry_id'";
      if(!mysqli_query($link, $sql))
      {$error='Error updating awards info for submitted award category: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    }

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS AWARD CATEGORY HAS BEEN EDITED:'.' '.html($awrd_ctgry_session);
    header('Location: '.$awrd_ctgry_url);
    exit();
  }

  if(isset($_POST['edit']) and $_POST['edit']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';
    $awrd_ctgry_id=cln($_POST['awrd_ctgry_id']);
    $assocs=array(); $errors=array();

    $sql="SELECT 1 FROM awrdctgrys WHERE awrd_ctgryid='$awrd_ctgry_id' LIMIT 1";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error checking for award-award category associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0) {$assocs[]='Awards ceremony';}

    if(count($assocs)>0)
    {$errors['awrd_ctgry_dlt']='**Award category must have no associations before it can be deleted. Current associations: '.html(implode(' / ', $assocs)).'.**';}

    if(count($errors)>0)
    {
      $sql="SELECT awrd_ctgry_nm FROM awrd_ctgry WHERE awrd_ctgry_id='$awrd_ctgry_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award category details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab='Edit: '.html($row['awrd_ctgry_nm']);
      $pagetitle=html($row['awrd_ctgry_nm']);
      $awrd_ctgry_nm=$_POST['awrd_ctgry_nm'];
      $awrd_ctgry_id=html($awrd_ctgry_id);
      include 'editform.html.php';
      exit();
    }
    else
    {
      $sql="SELECT awrd_ctgry_nm FROM awrd_ctgry WHERE awrd_ctgry_id='$awrd_ctgry_id'";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring award category details: '.mysqli_error($link);  include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      $row=mysqli_fetch_array($result);
      $pagetab= 'Delete confirmation: '.html($row['awrd_ctgry_nm']);
      $pagetitle=html($row['awrd_ctgry_nm']);
      $awrd_ctgry_id=html($awrd_ctgry_id);
      include 'delete.html.php';
      exit();
    }
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Delete')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $awrd_ctgry_id=cln($_POST['awrd_ctgry_id']);
    $sql="SELECT awrd_ctgry_nm FROM awrd_ctgry WHERE awrd_ctgry_id='$awrd_ctgry_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award category details: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $awrd_ctgry_session=$row['awrd_ctgry_nm'];

    $sql="DELETE FROM awrdctgrys WHERE awrd_ctgryid='$awrd_ctgry_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting award-award category associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM awrdnoms WHERE awrd_ctgryid='$awrd_ctgry_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting award category-award nominations associations: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    $sql="DELETE FROM awrd_ctgry WHERE awrd_ctgry_id='$awrd_ctgry_id'";
    if(!mysqli_query($link, $sql)) {$error='Error deleting award category: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}

    session_start();
    $_SESSION['successclass']='success';
    $_SESSION['message']='THIS AWARD CATEGORY HAS BEEN DELETED FROM THE DATABASE:'.' '.html($awrd_ctgry_session);
    header('Location: http://'. $_SERVER['HTTP_HOST'].'/');
    exit();
  }

  if(isset($_POST['delete']) and $_POST['delete']=='Cancel')
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

    $awrd_ctgry_id=cln($_POST['awrd_ctgry_id']);
    $sql= "SELECT awrd_ctgry_url
          FROM awrd_ctgry
          WHERE awrd_ctgry_id='$awrd_ctgry_id'";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring award category URL: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    header('Location: '.$row['awrd_ctgry_url']);
    exit();
  }

  include $_SERVER['DOCUMENT_ROOT'].'/includes/db.inc.php';

  $awrds_url=cln($_GET['awrds_url']);
  $awrd_ctgry_url=cln($_GET['awrd_ctgry_url']);

  $sql= "SELECT awrds_id, awrd_ctgry_id
        FROM awrds
        INNER JOIN awrd ON awrds_id=awrdsid INNER JOIN awrdnoms ON awrd_id=awrdid INNER JOIN awrd_ctgry ON awrd_ctgryid=awrd_ctgry_id
        WHERE awrds_url='$awrds_url' AND awrd_ctgry_url='$awrd_ctgry_url'
        GROUP BY awrds_id, awrd_ctgry_id";
  $result=mysqli_query($link, $sql);
  if(!$result) {$error='Error checking that URLs have given valid data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
  $row=mysqli_fetch_array($result);
  $awrds_id=$row['awrds_id'];
  $awrd_ctgry_id=$row['awrd_ctgry_id'];

  if(mysqli_num_rows($result)==0)
  {
    include $_SERVER['DOCUMENT_ROOT'].'/includes/404.html.php';
  }
  else
  {
    $sql= "SELECT awrds_nm, awrds_url, awrd_ctgry_nm
          FROM awrds
          INNER JOIN awrd ON awrds_id=awrdsid INNER JOIN awrdnoms ON awrd_id=awrdid INNER JOIN awrd_ctgry ON awrd_ctgryid=awrd_ctgry_id
          WHERE awrds_id='$awrds_id' AND awrd_ctgry_id='$awrd_ctgry_id'
          GROUP BY awrds_id, awrd_ctgry_id";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards name and award category data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    $row=mysqli_fetch_array($result);
    $awrd_ctgry_nm=$row['awrd_ctgry_nm'];
    $pagetab=html($row['awrds_nm']).': '.html($awrd_ctgry_nm);
    $pagetitle=html($row['awrds_nm']).':</br>'.html($awrd_ctgry_nm);
    $awrd_nm_lnk='<a href="/awards/'.html($row['awrds_url']).'">'.html($row['awrds_nm']).'</a>';

    $sql= "SELECT awrds_url, awrd_yr, awrd_yr_end, awrd_yr_url, awrd_ctgry_url
          FROM awrds
          INNER JOIN awrd ON awrds_id=awrdsid INNER JOIN awrdnoms ON awrd_id=awrdid INNER JOIN awrd_ctgry ON awrd_ctgryid=awrd_ctgry_id
          WHERE awrds_id='$awrds_id' AND awrd_ctgry_id='$awrd_ctgry_id'
          GROUP BY awrds_id, awrd_id, awrd_ctgry_id
          ORDER BY awrd_yr DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring year list data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      if($row['awrd_yr_end']) {$awrd_yr_end=html(preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']));} else {$awrd_yr_end='';}
      $awrd_yrs[]='<a href="/awards/category/'.html($row['awrds_url']).'/'.html($row['awrd_ctgry_url']).'#'.html($row['awrd_yr_url']).'">'.html($row['awrd_yr']).html($awrd_yr_end).'</a>';
    }
    if(!empty($awrd_yrs)) {$awrd_yr_list=implode(' / ', $awrd_yrs);} else {$awrd_yr_list=NULL;}

    $sql= "SELECT awrd_ctgry_alt_nm
          FROM awrd
          INNER JOIN awrdctgrys ON awrd_id=awrdid
          WHERE awrdsid='$awrds_id' AND awrd_ctgryid='$awrd_ctgry_id' AND awrd_ctgry_alt_nm IS NOT NULL
          GROUP BY awrd_ctgry_alt_nm
          ORDER BY MAX(awrd_yr) DESC, MIN(awrd_yr) DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring alternate name data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    while($row=mysqli_fetch_array($result))
    {
      $alt_nms[]=html($row['awrd_ctgry_alt_nm']);
    }
    if(!empty($alt_nms)) {$alt_nm_list=implode(' / ', $alt_nms);} else {$alt_nm_list=NULL;}

    $sql= "SELECT awrds_url, awrd_id, awrd_yr, awrd_yr_end, awrd_yr_url, COALESCE(awrd_ctgry_alt_nm, awrd_ctgry_nm)awrd_ctgry_nm
          FROM awrds
          INNER JOIN awrd ON awrds_id=awrdsid INNER JOIN awrdnoms an ON awrd_id=an.awrdid INNER JOIN awrd_ctgry ON an.awrd_ctgryid=awrd_ctgry_id
          INNER JOIN awrdctgrys ac ON an.awrdid=ac.awrdid AND an.awrd_ctgryid=ac.awrd_ctgryid
          WHERE awrds_id='$awrds_id' AND ac.awrd_ctgryid='$awrd_ctgry_id'
          GROUP BY awrd_id
          ORDER BY awrd_yr DESC";
    $result=mysqli_query($link, $sql);
    if(!$result) {$error='Error acquiring awards year data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
    if(mysqli_num_rows($result)>0)
    {
      while($row=mysqli_fetch_array($result))
      {
        if($row['awrd_yr_end']) {$awrd_yr_end=html(preg_replace('/([0-9]{2})([0-9]{2})$/', '/$2', $row['awrd_yr_end']));} else {$awrd_yr_end='';}
        $awrd_nm='<a href="/awards/ceremony/'.html($row['awrds_url']).'/'.html($row['awrd_yr_url']).'">'.html($row['awrd_yr']).$awrd_yr_end.'</a>';
        $awrds[$row['awrd_id']]=array('awrd_nm'=>$awrd_nm, 'awrd_yr_url'=>html($row['awrd_yr_url']), 'awrd_ctgry_nm_tbl_hd'=>html($row['awrd_ctgry_nm']), 'noms'=>array());
      }

      $sql= "SELECT awrd_id, nom_id, nom_win_dscr, win_bool
            FROM awrdnoms
            INNER JOIN awrd ON awrdid=awrd_id
            WHERE awrdsid='$awrds_id' AND awrd_ctgryid='$awrd_ctgry_id'
            GROUP BY awrd_id, nom_id
            ORDER BY nom_id ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nomination data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {$awrds[$row['awrd_id']]['noms'][$row['nom_id']]=array('nom_win_dscr'=>html($row['nom_win_dscr']), 'win'=>$row['win_bool'], 'nomppl'=>array(), 'nomprds'=>array(), 'nompts'=>array());}

      $sql= "SELECT awrd_id, nomid, nom_ordr, nom_rl, comp_id, comp_nm, comp_url, comp_bool
            FROM awrdnomppl anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
            INNER JOIN awrd ON an.awrdid=awrd_id INNER JOIN comp ON nom_compid=comp_id
            WHERE awrdsid='$awrds_id' AND an.awrd_ctgryid='$awrd_ctgry_id' AND nom_prsnid='0'
            GROUP BY awrd_id, nomid, comp_id
            UNION
            SELECT awrd_id, nomid, nom_ordr, nom_rl, prsn_id, prsn_fll_nm, prsn_url, comp_bool
            FROM awrdnomppl anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
            INNER JOIN awrd ON an.awrdid=awrd_id INNER JOIN prsn ON nom_prsnid=prsn_id
            WHERE awrdsid='$awrds_id' AND an.awrd_ctgryid='$awrd_ctgry_id' AND nom_compid='0'
            GROUP BY awrd_id, nomid, prsn_id
            ORDER BY nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (company/people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        if(!preg_match('/^the-company$/', $row['comp_url'])) {$nom_prsn='<a href="/'.$comp_bool.'/'.html($row['comp_url']).'">'.html($row['comp_nm']).'</a>'.$nom_rl;}
        else {$nom_prsn=html($row['comp_nm']).$nom_rl;}
        $awrds[$row['awrd_id']]['noms'][$row['nomid']]['nomppl'][$row['comp_id']]=array('nom_prsn'=>$nom_prsn, 'nomcomp_ppl'=>array());
      }

      $sql= "SELECT awrd_id, nomid, nom_rl, nom_compid, prsn_fll_nm, prsn_url
            FROM awrdnomppl anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
            INNER JOIN awrd ON an.awrdid=awrd_id INNER JOIN prsn ON nom_prsnid=prsn_id
            WHERE awrdsid='$awrds_id' AND an.awrd_ctgryid='$awrd_ctgry_id' AND nom_compid!='0'
            GROUP BY awrd_id, nomid, nom_compid, prsn_id
            ORDER BY nom_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nomination/win (company people) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        if($row['nom_rl']) {$nom_rl=' (<em>'.html($row['nom_rl']).'</em>)';} else {$nom_rl='';}
        $prsn_nm='<a href="/person/'.html($row['prsn_url']).'">'.html($row['prsn_fll_nm']).'</a>'.$nom_rl;
        $awrds[$row['awrd_id']]['noms'][$row['nomid']]['nomppl'][$row['nom_compid']]['nomcomp_ppl'][]=$prsn_nm;
      }

      $sql= "SELECT awrd_id, nomid, prd_id, prd_url, prd_nm, DATE_FORMAT(prd_frst_dt, '%d %b %Y') AS prd_frst_dt_dsply,
            DATE_FORMAT(prd_lst_dt, '%d %b %Y') AS prd_lst_dt_dsply, prd_dts_info, prd_tbc_nt, COALESCE(prd_alph, prd_nm)prd_alph, thtr_fll_nm
            FROM awrdnomprds anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
            INNER JOIN awrd ON an.awrdid=awrd_id INNER JOIN prd p ON nom_prdid=prd_id INNER JOIN thtr ON p.thtrid=thtr_id
            WHERE awrdsid='$awrds_id' AND an.awrd_ctgryid='$awrd_ctgry_id'
            GROUP BY awrd_id, nomid, prd_id
            ORDER BY prd_frst_dt DESC, prd_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (productions) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/prd_rcv.inc.php';
        $awrds[$row['awrd_id']]['noms'][$row['nomid']]['nomprds'][]=array('prd_nm'=>$prd_nm, 'prd_dts'=>$prd_dts, 'thtr'=>$thtr);
      }

      $sql= "SELECT awrd_id, nomid, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url, COALESCE(pt_alph, pt_nm)pt_alph
            FROM awrdnompts anp
            INNER JOIN awrdnoms an ON anp.awrdid=an.awrdid AND anp.awrd_ctgryid=an.awrd_ctgryid AND nomid=nom_id
            INNER JOIN awrd ON an.awrdid=awrd_id INNER JOIN pt ON nom_ptid=pt_id
            WHERE awrdsid='$awrds_id' AND an.awrd_ctgryid='$awrd_ctgry_id'
            GROUP BY awrd_id, nomid, pt_id
            ORDER BY pt_yr_wrttn DESC, pt_alph ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring awards nominee/winner (playtexts) data: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $awrds[$row['awrd_id']]['noms'][$row['nomid']]['nompts'][]=$pt_nm.' ('.$pt_yr.')';
      }
    }

    $awrd_ctgry_id=html($awrd_ctgry_id);
    include 'awards-category.html.php';
  }
?>