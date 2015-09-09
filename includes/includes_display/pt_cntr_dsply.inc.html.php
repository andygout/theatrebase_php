        <tr class="row<?php echo $rowclass; ?>">
          <td class="ptcol4" colspan="3"><em><?php
          if(!empty($pt['cntr_rls'])) {
          $f=0; $rls=count($pt['cntr_rls']); foreach($pt['cntr_rls'] as $cntr_rl):
          if(!empty($cntr_rl['cntrs'])) {
          $h=0; foreach($cntr_rl['cntrs'] as $cntr): if(!empty($cntr['cntrcomp_ppl'])) {$h++;} endforeach;
          $i=0; $ppl=count($cntr_rl['cntrs']); echo $cntr_rl['cntr_rl'].' ';
          foreach($cntr_rl['cntrs'] as $cntr):
          if(!empty($cntr['cntrcomp_ppl'])) {$j=0; $compppl=count($cntr['cntrcomp_ppl']); foreach($cntr['cntrcomp_ppl'] as $cntrcomp_prsn):
          echo $cntrcomp_prsn['prsn_nm'].$cntrcomp_prsn['cntr_sb_rl'];
          if($j<$compppl-2) {echo ', ';} elseif($j<$compppl-1) {echo ' and ';} $j++; endforeach; echo ' for ';} echo $cntr['comp_nm'].$cntr['cntr_sb_rl'];
          if($i<$ppl-2) {if($h>0) {echo '; ';} else {echo ', ';}} elseif($i<$ppl-1) {if($h>0) {echo '; and ';} else {echo ' and ';}} $i++; endforeach;}
          if($f<$rls-1) {echo '</br>';} $f++; endforeach;} ?>
          </em></td>
        </tr>