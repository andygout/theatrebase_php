<?php
      if($row['prd_dts_info']=='4') {if($row['prd_tbc_nt']){$prd_dts='<em>'.html($row['prd_tbc_nt']).'</em>';} else {$prd_dts='<em>Dates TBC</em>';}}
      else
      { if($row['prd_frst_dt_dsply']!==$row['prd_lst_dt_dsply'])
        {
          $prd_dts=html($row['prd_frst_dt_dsply']).' - ';
          if($row['prd_dts_info']=='3') {$prd_dts .= '<em>TBC</em>';}
          else {if($row['prd_dts_info']=='2') {$prd_dts .= '<em>'.html($row['prd_lst_dt_dsply']).'</em>';} else {$prd_dts .= html($row['prd_lst_dt_dsply']);}}
        }
        else
        {
          if($row['prd_dts_info']=='3') {$prd_dts='<em>Dates TBC</em>';}
          else {if($row['prd_dts_info']=='2') {$prd_dts='<em>'.html($row['prd_frst_dt_dsply']).' only</em>';} else {$prd_dts=html($row['prd_frst_dt_dsply']).' only';}}
        }
      }
      if(preg_match('/TBC$/', $row['thtr_fll_nm'])) {$thtr='<em>'.html($row['thtr_fll_nm']).'</em>';} else {$thtr=html($row['thtr_fll_nm']);}
      $prd_nm='<a href="/production/'.html($row['prd_id']).'/'.html($row['prd_url']).' ">'.html($row['prd_nm']).'</a>';
?>