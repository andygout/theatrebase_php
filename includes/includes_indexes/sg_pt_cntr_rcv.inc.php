<?php
            $sql =  "SELECT coll_ov, cntr_rl_id, cntr_rl FROM ptcntrrl
                INNER JOIN pt ON ptid=pt_id WHERE ptid='$sg_pt_id' AND coll_ov IS NOT NULL
                GROUP BY coll_ov, pt_id, cntr_rl_id ORDER BY cntr_rl_id";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring contributor (roles) data for collected works overview playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {$pts[$row['coll_ov']]['sg_pts'][$sg_pt_id]['cntr_rls'][$row['cntr_rl_id']]=array('cntr_rl'=>html($row['cntr_rl']), 'cntrs'=>array());}

            $sql =  "SELECT coll_ov, cntr_rlid, comp_id, comp_nm, cntr_sb_rl, cntr_ordr, comp_bool FROM ptcntr
                INNER JOIN comp ON cntr_compid=comp_id INNER JOIN pt ON ptid=pt_id WHERE ptid='$sg_pt_id' AND cntr_prsnid=0 AND coll_ov IS NOT NULL
                UNION
                SELECT coll_ov, cntr_rlid, prsn_id, prsn_fll_nm, cntr_sb_rl, cntr_ordr, comp_bool FROM ptcntr
                INNER JOIN prsn ON cntr_prsnid=prsn_id INNER JOIN pt ON ptid=pt_id WHERE ptid='$sg_pt_id' AND cntr_compid=0 AND coll_ov IS NOT NULL
                ORDER BY coll_ov, cntr_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring contributor (company / people) data for collected works overview playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              if($row['comp_bool']) {$comp_bool='company';} else {$comp_bool='person';}
              if($row['cntr_sb_rl']) {$cntr_sb_rl=' (<em>'.html($row['cntr_sb_rl']).'</em>)';} else {$cntr_sb_rl='';}
              $pts[$row['coll_ov']]['sg_pts'][$sg_pt_id]['cntr_rls'][$row['cntr_rlid']]['cntrs'][$row['comp_id']]=array('comp_nm'=>html($row['comp_nm']), 'cntr_sb_rl'=>$cntr_sb_rl, 'cntrcomp_ppl'=>array());
            }

            $sql= "SELECT coll_ov, cntr_rlid, cntr_compid, cntr_sb_rl, prsn_fll_nm FROM ptcntr
                INNER JOIN prsn ON cntr_prsnid=prsn_id INNER JOIN pt ON ptid=pt_id WHERE ptid='$sg_pt_id' AND cntr_compid!=0 AND coll_ov IS NOT NULL
                ORDER BY coll_ov, cntr_ordr ASC";
            $result=mysqli_query($link, $sql);
            if(!$result) {$error='Error acquiring contributor (company people) data for collected works overview playtexts: '.mysqli_error($link); include $_SERVER['DOCUMENT_ROOT'].'/includes/error.html.php'; exit();}
            while($row=mysqli_fetch_array($result))
            {
              if($row['cntr_sb_rl']) {$cntr_sb_rl=' (<em>'.html($row['cntr_sb_rl']).'</em>)';} else {$cntr_sb_rl='';}
              $pts[$row['coll_ov']]['sg_pts'][$sg_pt_id]['cntr_rls'][$row['cntr_rlid']]['cntrs'][$row['cntr_compid']]['cntrcomp_ppl'][]=array('prsn_nm'=>html($row['prsn_fll_nm']), 'cntr_sb_rl'=>$cntr_sb_rl);
            }
?>