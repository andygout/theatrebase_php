        <tr class="row<?php echo $rowclass; ?>">
          <td class="prdcol5" colspan="3"><em><?php
          $f=0; $rls=count($prd['wri_rls']); foreach($prd['wri_rls'] as $wri_rl):
          if(!empty($wri_rl['src_mats'])) {$g=0; $h=0; $sms=count($wri_rl['src_mats']); echo $wri_rl['src_mat_rl'].' ';
          foreach($wri_rl['src_mats'] as $src_mat):
          if(!preg_match("/^{$prd['prd_nm_pln']}$/i", $src_mat['src_mat_nm'])) {echo 'the '.$src_mat['src_mat_frmt'].', <span style="font-style:normal">'.$src_mat['src_mat_nm'].'</span>';
          if($h==$sms-2 || ($h==$sms-1 && $wri_rl['wri_rl'])) {echo ',';}}
          else {if($g==0) {echo 'the ';} echo $src_mat['src_mat_frmt']; $g++;}
          if($h<$sms-2) {echo ', ';} elseif($h<$sms-1) {echo ' and ';} $h++; endforeach; echo ' ';}
          if(!empty($wri_rl['wris'])) {$i=0; $ppl=count($wri_rl['wris']); echo $wri_rl['wri_rl'].' ';
          foreach($wri_rl['wris'] as $wri): if($i>0 && $i<$ppl-1 && !$wri['wri_sb_rl']) {echo ', ';} elseif($i>0 && $i==$ppl-1 && !$wri['wri_sb_rl']) {echo ' and ';}
          echo $wri['wri_sb_rl'].$wri['comp_nm'];
          if(!empty($wri['wricomp_ppl'])) { echo ' ('; $j=0; $compppl=count($wri['wricomp_ppl']); foreach($wri['wricomp_ppl'] as $wricomp_prsn):
          echo $wricomp_prsn['wri_sb_rl'].$wricomp_prsn['prsn_nm'];
          if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ')'; }
          $i++; endforeach;} if($f<$rls-1) {echo '</br>';} $f++; endforeach; ?>
          </em></td>
        </tr>