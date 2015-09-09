<?php
      if($row['pt_yr_strtd_c']) {$pt_yr_strtd_c='c.';} else {$pt_yr_strtd_c='';}
      if($row['pt_yr_strtd'])
      {
        if(preg_match('/^-/', $row['pt_yr_strtd']))
        {
          $pt_yr_strtd=preg_replace('/^-([1-9][0-9]{0,3})/', '$1', $row['pt_yr_strtd']);
          if(!preg_match('/^-/', $row['pt_yr_wrttn'])) {$pt_yr_strtd .= ' BCE';}
        }
        else {$pt_yr_strtd=$row['pt_yr_strtd'];}
        $pt_yr_strtd .= '-';
      }
      else {$pt_yr_strtd='';}
      if($row['pt_yr_wrttn_c']) {$pt_yr_wrttn_c='c.';} else {$pt_yr_wrttn_c='';}
      if(preg_match('/^-/', $row['pt_yr_wrttn'])) {$pt_yr_wrttn=preg_replace('/^-([1-9][0-9]{0,3})/', '$1 BCE', $row['pt_yr_wrttn']);}
      else {$pt_yr_wrttn=$row['pt_yr_wrttn'];}
      $pt_yr=html($pt_yr_strtd_c.$pt_yr_strtd.$pt_yr_wrttn_c.$pt_yr_wrttn);
      $pt_nm='<a href="/playtext/'.html($row['pt_url']).'">'.html($row['pt_nm']).'</a>';
?>