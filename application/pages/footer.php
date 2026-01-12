 <footer class="footer">
  <div class="row">
      <div class="col-md-4"> &copy <?= date('Y'); ?> <a href="http://www.cbslgroup.in/" target="_blank"> <span class="text-primary"><?= $lang['cbslgroup']; ?></span></a> <?php echo $lang['Copyright_CBSL_Grp_All_rights_rsrvd']; ?></div>
         <div class="col-md-6"> <span style="font-size: 15px;"> <i class="fa fa-phone"></i> 1800 - 212 - 1526</span> <span style="font-size: 15px; margin-left:12px;"> <i class="fa fa-envelope-o text-primary"></i>  <a href="mailto:support@ezeedigitalsolutions.in">ezee.support@cbslgroup.in</a></span></div>
         <div class="col-md-2"><?php echo $lang['Vsn'] ?> 1.5.7</div> 
     </div>
 </footer>
<!-- jQuery  -->
<script src="assets/js/jquery.min.js"></script>
<script type="text/javascript" src="assets/plugins/jquery-validation/js/jquery.validate.min.js"></script>

<script>
    var resizefunc = [];
</script>
<script>
    function goPrevious(){
        
        window.history.back();
    }
    
    $("input, body, textarea").bind("click keyup",function(){
    
        $.post("application/ajax/lastActive.php", {lgt:2}, function(result, status){
            //alert(result);
            if(result=='2'){
                
                initSessionMonitor();
              		
            }else{
                <?php  $_SESSION['LAST_ACTIVITY'] = time(); ?>
            }
        }); 
    });
    
    $(document).ready(function(){
        
       $('.specialchaecterlock').keyup(function ()
        {
            var groupName = $(this).val();
            re = /[`1234567890~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(groupName);
            if (isSplChar)
            {
                var no_spl_char = groupName.replace(/[`~!@#$%^&*()|+\=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
        
    });
</script>