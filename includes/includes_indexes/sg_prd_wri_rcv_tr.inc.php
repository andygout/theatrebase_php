<?php
        $sql =  "SELECT CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN coll_ov ELSE (SELECT coll_ov FROM prd WHERE prd_id='$sg_prd_id') END AS coll_ov,
            wri_rl_id, src_mat_rl, wri_rl
            FROM prdwrirl
            INNER JOIN prd ON prdid=prd_id
            WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN '$sg_prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') END
            AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id, wri_rl_id
            ORDER BY wri_rl_id ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring writer role data for segment productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$sg_prd_id]['wri_rls'][$row['wri_rl_id']]=array('wri_rl'=>html($row['wri_rl']), 'src_mat_rl'=>html($row['src_mat_rl']), 'src_mats'=>array(), 'wris'=>array());}

        $sql= "SELECT CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN coll_ov ELSE (SELECT coll_ov FROM prd WHERE prd_id='$sg_prd_id') END AS coll_ov,
            wri_rlid, mat_nm, frmt_nm
            FROM prdsrc_mat
            INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id INNER JOIN prd ON prdid=prd_id
            WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN '$sg_prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') END
            AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id, wri_rlid, mat_id
            ORDER BY src_mat_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring credited source materials for segment productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {$prds[$row['coll_ov']]['sg_prds'][$sg_prd_id]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));}

        $sql= "SELECT CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN coll_ov ELSE (SELECT coll_ov FROM prd WHERE prd_id='$sg_prd_id') END AS coll_ov,
            wri_rlid, wri_ordr, wri_sb_rl, comp_id, comp_nm
            FROM prdwri
            INNER JOIN comp ON wri_compid=comp_id INNER JOIN prd ON prdid=prd_id
            WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN '$sg_prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') END
            AND coll_ov IS NOT NULL
            AND wri_prsnid=0
            GROUP BY coll_ov, prd_id, wri_rlid, comp_id
            UNION
            SELECT CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN coll_ov ELSE (SELECT coll_ov FROM prd WHERE prd_id='$sg_prd_id') END AS coll_ov,
            wri_rlid, wri_ordr, wri_sb_rl, prsn_id, prsn_fll_nm
            FROM prdwri
            INNER JOIN prsn ON wri_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
            WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN '$sg_prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') END
            AND coll_ov IS NOT NULL
            AND wri_compid=0
            GROUP BY coll_ov, prd_id, wri_rlid, prsn_id
            ORDER BY wri_ordr ASC";
        $result=mysqli_query($link, $sql); if(!$result) {$error='Error acquiring credited writers for segment productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['wri_sb_rl']) {if(!preg_match('/^(:|;|,|\.)/', $row['wri_sb_rl'])) {$wri_sb_rl=' '.html($row['wri_sb_rl']).' ';} else {$wri_sb_rl=html($row['wri_sb_rl']).' ';}} else {$wri_sb_rl='';}
          $prds[$row['coll_ov']]['sg_prds'][$sg_prd_id]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
        }

        $sql= "SELECT CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN coll_ov ELSE (SELECT coll_ov FROM prd WHERE prd_id='$sg_prd_id') END AS coll_ov,
            wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm
            FROM prdwri
            INNER JOIN prsn ON wri_prsnid=prsn_id INNER JOIN prd ON prdid=prd_id
            WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') IS NULL THEN '$sg_prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$sg_prd_id') END
            AND wri_compid!=0 AND coll_ov IS NOT NULL
            GROUP BY coll_ov, prd_id, wri_rlid, wri_compid, prsn_id
            ORDER BY wri_ordr ASC";
        $result=mysqli_query($link, $sql);
        if(!$result) {$error='Error acquiring writer (company people) data for segment productions (as material): '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
        while($row=mysqli_fetch_array($result))
        {
          if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ';} else {$wri_sb_rl='';}
          $prds[$row['coll_ov']]['sg_prds'][$sg_prd_id]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>html($row['prsn_fll_nm']), 'wri_sb_rl'=>$wri_sb_rl);
        }
?>