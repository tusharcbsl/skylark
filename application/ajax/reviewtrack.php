<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}


$data = file_get_contents($file);
$lang = json_decode($data, true);
require './../config/database.php';

if(!isset($_POST['token'], $_POST['ID'])){
   echo "Unauthrized Access";  
}
 
$tktid = preg_replace("/[^0-9A-Za-z_ ]/", "", $_POST['ID']);

$qryFetch = mysqli_query($db_con, "select * from tbl_doc_review where ticket_id='$tktid' order by review_order asc");
?> 
<div class="row">
    <div class="col-md-12">
        <section id="cd-timeline" class="cd-container">
            <div class="cd-timeline" style="padding: 20px">
                <div class="cd-timeline-img cd-info">
                    <i class="fa fa-star-half-full"></i>
                </div>
            </div>  
            <?php
            while ($row = mysqli_fetch_assoc($qryFetch)) {
                ?>
                <div class="cd-timeline-block">
                    <?php if ($row['review_status'] == 1) { ?>
                        <div class="cd-timeline-img cd-primary">
                            <i class="fa fa-eye"></i>
                        </div>
                        <?php } else {
                        ?>
                        <div class="cd-timeline-img cd-warning">
                            <i class="fa fa-eye-slash"></i>
                        </div>
                    <?php }
                    ?>
                    <!-- cd-timeline-img -->

                    <div class="cd-timeline-content">
                        <?php
                        $userQry = mysqli_query($db_con, "select concat(first_name,' ',last_name) as name from tbl_user_master where user_id=$row[action_by]");
                        $fetchUser = mysqli_fetch_assoc($userQry);
                        if (!empty($row['action_time'])) {
                            ?>
                           <p><strong><?php echo $lang['Reviewed_By']; ?> : </strong><span class="text-primary"><?php echo $fetchUser['name']; ?></span></p>
                            <p><strong><?php echo $lang['Review_Status']; ?> : </strong><span class="text-primary"><?php echo $row['task_status']; ?></span></p>
                            <p><strong><?php echo $lang['Assign_date']; ?> : </strong><span class="text-primary"><?php echo $row['start_date']; ?></span></p>
                            <p><strong><?php echo $lang['Review_Date']; ?> : </strong><span class="text-primary"><?php echo $row['action_time']; ?></span></p>
                            <?php
                        } else {
                            ?>
                            <h5><?php echo $lang['pending_no_action']; ?></h5>
                        <?php }
                        ?>

                    </div> <!-- cd-timeline-content -->
                </div> <!-- cd-timeline-block -->
                <?php
            }
            $qry = mysqli_query($db_con, "select review_status from `tbl_doc_review` where ticket_id='$tktid' and review_status='0'");
            if (mysqli_num_rows($qry) > 0) {
                ?>

                <div class="cd-timeline-block">
                    <div class="cd-timeline-img cd-danger">
                        <i class="fa fa-thumbs-down"></i>
                    </div>
                </div>  
                <?php
            } else {
                ?>
                <div class="cd-timeline-block">
                    <div class="cd-timeline-img cd-success">
                        <i class="fa fa-thumbs-up"></i>
                    </div>
                </div>  
            <?php } ?>
        </section> <!-- cd-timeline -->
    </div>
</div><!-- Row -->
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>

<script src="assets/js/jquery.slimscroll.js"></script>

<script src="assets/js/wow.min.js"></script>
<script src="assets/js/jquery.nicescroll.js"></script>
<script src="assets/js/jquery.scrollTo.min.js"></script>


<script type="text/javascript">
    jQuery(document).ready(function ($) {
        var $timeline_block = $('.cd-timeline-block');

        //hide timeline blocks which are outside the viewport
        $timeline_block.each(function () {
            if ($(this).offset().top > $(window).scrollTop() + $(window).height() * 0.75) {
                $(this).find('.cd-timeline-img, .cd-timeline-content').addClass('is-hidden');
            }
        });

        //on scolling, show/animate timeline blocks when enter the viewport
        $(window).on('scroll', function () {
            $timeline_block.each(function () {
                if ($(this).offset().top <= $(window).scrollTop() + $(window).height() * 0.75 && $(this).find('.cd-timeline-img').hasClass('is-hidden')) {
                    $(this).find('.cd-timeline-img, .cd-timeline-content').removeClass('is-hidden').addClass('bounce-in');
                }
            });
        });
    });
</script>