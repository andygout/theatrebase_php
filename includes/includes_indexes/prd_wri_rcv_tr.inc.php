<?php
            $sql= "SELECT wri_rl_id, wri_rl, src_mat_rl FROM prdwrirl LEFT OUTER JOIN prd ON prdid=prd_id
                  WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$prd_id') IS NULL THEN '$prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$prd_id') END
                  AND coll_ov IS NULL GROUP BY prdid, wri_rl_id ORDER BY wri_rl_id";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring writer (roles) data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {$prds[$prd_id]['wri_rls'][$row['wri_rl_id']]=array('src_mat_rl'=>html($row['src_mat_rl']), 'wri_rl'=>html($row['wri_rl']), 'src_mats'=>array(), 'wris'=>array());}

            $sql= "SELECT wri_rlid, mat_nm, mat_url, frmt_nm, frmt_url
                  FROM prdsrc_mat
                  INNER JOIN mat ON src_matid=mat_id INNER JOIN frmt ON frmtid=frmt_id LEFT OUTER JOIN prd ON prdid=prd_id
                  WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$prd_id') IS NULL THEN '$prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$prd_id') END
                  AND coll_ov IS NULL
                  GROUP BY prdid, wri_rlid, mat_id
                  ORDER BY src_mat_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring source material data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {$prds[$prd_id]['wri_rls'][$row['wri_rlid']]['src_mats'][]=array('src_mat_nm'=>html($row['mat_nm']), 'src_mat_frmt'=>html($row['frmt_nm']));}

            $sql= "SELECT wri_rlid, wri_ordr, wri_sb_rl, comp_id, comp_nm
                  FROM prdwri
                  INNER JOIN comp ON wri_compid=comp_id LEFT OUTER JOIN prd ON prdid=prd_id
                  WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$prd_id') IS NULL THEN '$prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$prd_id') END
                  AND wri_prsnid=0 AND coll_ov IS NULL
                  GROUP BY prdid, wri_rlid, comp_id
                  UNION
                  SELECT wri_rlid, wri_ordr, wri_sb_rl, prsn_id, prsn_fll_nm
                  FROM prdwri
                  INNER JOIN prsn ON wri_prsnid=prsn_id LEFT OUTER JOIN prd ON prdid=prd_id
                  WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$prd_id') IS NULL THEN '$prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$prd_id') END
                  AND wri_compid=0 AND coll_ov IS NULL
                  GROUP BY prdid, wri_rlid, prsn_id
                  ORDER BY wri_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring writer data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              if($row['wri_sb_rl']) {if(!preg_match('/^(:|;|,|\.)/', $row['wri_sb_rl'])) {$wri_sb_rl=' '.html($row['wri_sb_rl']).' ';} else {$wri_sb_rl=html($row['wri_sb_rl']).' ';}} else {$wri_sb_rl='';}
              $prds[$prd_id]['wri_rls'][$row['wri_rlid']]['wris'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'wri_sb_rl'=>$wri_sb_rl, 'wricomp_ppl'=>array());
            }

            $sql= "SELECT wri_rlid, wri_compid, wri_sb_rl, prsn_fll_nm
                  FROM prdwri
                  INNER JOIN prsn ON wri_prsnid=prsn_id LEFT OUTER JOIN prd ON prdid=prd_id
                  WHERE prdid=CASE WHEN(SELECT tr_ov FROM prd WHERE prd_id='$prd_id') IS NULL THEN '$prd_id' ELSE (SELECT tr_ov FROM prd WHERE prd_id='$prd_id') END
                  AND wri_compid!=0 AND coll_ov IS NULL
                  GROUP BY prdid, wri_rlid, wri_compid, prsn_id
                  ORDER BY wri_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring writer (company people) data for productions: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              if($row['wri_sb_rl']) {$wri_sb_rl=html($row['wri_sb_rl']).' ' ;} else {$wri_sb_rl='';}
              $prds[$prd_id]['wri_rls'][$row['wri_rlid']]['wris'][$row['wri_compid']]['wricomp_ppl'][]=array('prsn_nm'=>html($row['prsn_fll_nm']), 'wri_sb_rl'=>html($row['wri_sb_rl']));
            }
?>