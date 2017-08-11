<?php
  $total = mysqli_num_rows($res_count);
  $total_pages = ceil($total / PAGE_COUNT);
  $nearest_10 = ceil($page / 10) * 10;
?>
<div class="pagination-main left">
 
  <ul class="pagination">
      <?php 
        if($page<=1) {
          echo '<li class="disabled"><a style="width:70px" href="#">&lt;&lt; Prev. 10</a></li>';
        } else {
          echo '<li><a style="width:70px" onclick="document.getElementById(\'page\').value=\''.($nearest_10-15>=1 ? $nearest_10-15 : 5).'\';document.forms.Form2.submit();" href="javascript:{}">&lt;&lt; Prev. 10</a></li>';
        }
        ///////////////
        if($page<=1) {
          echo '<li class="disabled"><a href="#">&lt;</a></li>';
        } else {
          echo '<li><a onclick="document.getElementById(\'page\').value=\''.($page-1).'\';document.forms.Form2.submit();" href="javascript:{}">&lt;</a></li>';
        }
      ?>
      <?php
        $start_page = $page - 4 >= 1 ? $page - 4 : 1;
        for($i = $start_page;$i < $start_page + 10 && $i <= $total_pages; $i++){
          if($page==$i) {
            echo '<li class="active"><a href="#">'.$i.'</a></li>';
          } else {
            echo '<li><a onclick="document.getElementById(\'page\').value=\''.$i.'\';document.forms.Form2.submit();" href="javascript:{}">'.$i.'</a></li>';
          } 
        }
        
      ?>
      <?php 
        if($page>=$total_pages) {
          echo '<li class="disabled"><a href="#">&gt;</a></li>';
        } else {
          echo '<li><a onclick="document.getElementById(\'page\').value=\''.($page+1).'\';document.forms.Form2.submit();" href="javascript:{}">&gt;</a></li>';
        }
        ///////////////////////
        if($page>=$total_pages) {
          echo '<li class="disabled"><a style="width:70px" href="#">Next 10 &gt;&gt;</a></li>';
        } else {

          echo '<li><a style="width:70px" onclick="document.getElementById(\'page\').value=\''.($nearest_10+5<=$total_pages ? $nearest_10+5 : $total_pages).'\';document.forms.Form2.submit();" href="javascript:{}">Next 10 &gt;&gt;</a></li>';
        }
      ?>
  </ul>

</div>