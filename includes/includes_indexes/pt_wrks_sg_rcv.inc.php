<?php
      $sql =  "SELECT wrks_ov, pt_nm, pt_yr_strtd_c, pt_yr_strtd, pt_yr_wrttn_c, pt_yr_wrttn, pt_url FROM ptwrks
          INNER JOIN pt ON wrks_sg=pt_id WHERE wrks_ov='$pt_id' GROUP BY wrks_ov, pt_id ORDER BY wrks_sbhdrid ASC, wrks_ordr ASC";
      $result=mysqli_query($link, $sql);
      if(!$result) {$error='Error acquiring collected works segment playtext data for (contributor) playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
      while($row=mysqli_fetch_array($result))
      {
        include $_SERVER['DOCUMENT_ROOT'].'/includes/includes_indexes/pt_rcv.inc.php';
        $pts[$pt_id]['wrks_sg_pts'][]=$pt_nm.' ('.$pt_yr.')';
      }
?>