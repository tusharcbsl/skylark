<?php
$status = 0;
$checkfileLockqry = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and is_active='1' and user_id='$_SESSION[cdes_user_id]'");
if (mysqli_num_rows($checkfileLockqry) > 0) {

    $checkfileLock = mysqli_query($db_con, "SELECT * FROM `tbl_locked_file_master` WHERE doc_id='$file_row[doc_id]' and is_locked='1' and user_id='$_SESSION[cdes_user_id]'");
    if (mysqli_num_rows($checkfileLock) > 0) {
        $status = 1;
    } else {
        
        $status = 0;
    }
} else {
    $status = 1;
}
if ($status == 1) {
    if (strtolower($file_row['doc_extn']) == 'pdf') {
        ?>
        <?php if ($rwgetRole['pdf_file'] == '1') { ?>
            <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
                <?php if(file_exists('thumbnail/'.base64_encode($file_row['doc_id']).'.jpg')){ ?>
                    <div> 
                        <img class="thumb-image" src="thumbnail/<?=base64_encode($file_row['doc_id'])?>.jpg"> 
                    </div>
                <?php } ?>
                <i class="fa fa-file-pdf-o" data-toggle="tooltip" title="<?php echo $lang['pdf_file']; ?>"></i>
            </a>
            <a href="flipflop-viewer?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
            <i class="ti-book" data-toggle="tooltip" style="font-size: 18px;" title="<?php echo $lang['View_Filpflop']; ?>"></i></a>

            

            <?php if (basename($_SERVER['PHP_SELF']) == 'search.php') { ?>
                <a href="anottFrTltp/index?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])) ?>&id1=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&pn=1&pg=<?php echo urlencode(base64_encode($pageNumbers)); ?>&pgindx=0" id="fancybox-inner" class="pdfview" data-toggle="tooltip"  target="_blank" title="Filtered PDF">
                    <i class="fa fa-file-text-o"></i></a>
                <?php
            }
        }
        ?>
        <?php
    } else if ($rwgetRole['image_file'] == '1' && (strtolower($file_row['doc_extn']) == 'jpg' || strtolower($file_row['doc_extn']) == 'jpeg' || strtolower($file_row['doc_extn']) == 'png' || strtolower($file_row['doc_extn']) == 'gif' || strtolower($file_row['doc_extn']) == 'bmp')) {
        ?>
        <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>"  target="_blank">
            <?php if(file_exists('thumbnail/'.base64_encode($file_row['doc_id']).'.jpg')){ ?>
                <div> 
                    <img class="thumb-image" src="thumbnail/<?=base64_encode($file_row['doc_id'])?>.jpg"> 
                </div>
            <?php }else{ ?>
                <i class="fa fa-file-image-o" data-toggle="tooltip" title="<?php echo $lang['image_file']; ?>"></i> <!--<?php echo $lang['image_file']; ?>-->
            <?php } ?>
        </a>

    <?php } else if ($rwgetRole['tif_file'] == '1' && (strtolower($file_row['doc_extn']) == 'tif' || strtolower($file_row['doc_extn']) == 'tiff')) { ?>
        <a href="tiff-viewer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" >
            <i class="fa fa-picture-o" data-toggle="tooltip"  title="<?php echo $lang['Tiff_File']; ?>"></i> <!--<?php echo $lang['Tiff_File']; ?>-->
        </a>
    <?php } else if ($rwgetRole['excel_file'] == '1' && strtolower($file_row['doc_extn']) == 'xlsx') {
        ?>
        <a href="gdrivedocs?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&perm=<?php echo urlencode(base64_encode('reader')); ?>" target="_blank"><i class="fa fa-eye" data-toggle="tooltip" title="File View"></i></a>
    <?php } else if ($rwgetRole['excel_file'] == '1' && strtolower($file_row['doc_extn']) == 'xls') {
        ?>
       <a href="gdrivedocs?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&perm=<?php echo urlencode(base64_encode('reader')); ?>" target="_blank"><i class="fa fa-eye" data-toggle="tooltip" title="File View"></i></a>
	   
          <?php } else if ($rwgetRole['view_csv'] == '1' && strtolower($file_row['doc_extn']) == 'csv') {
            ?>
            <a href="csv-viewer?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
            <i class="fa fa-file" data-toggle="tooltip" title="<?php echo $lang['csv_file']; ?>"></i></a>
                
                
        <?php } else if ($rwgetRole['doc_file'] == '1' && (strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
        
			<a href="gdrivedocs?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&perm=<?php echo urlencode(base64_encode('reader')); ?>" target="_blank"><i class="fa fa-eye" data-toggle="tooltip" title="File View"></i></a>
			
        <?php if ($rwgetRole['word_edit'] == '1' && $page != 'recycle.php' && $page != 'retention-period-document.php' && $page != 'expired-document-list.php') { ?>
           
		   <a href="gdrivedocs?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&perm=<?php echo urlencode(base64_encode('reader')); ?>" target="_blank"><i class="fa fa-eye" data-toggle="tooltip" title="File View"></i></a>
            <?php
        }
    } else if ($rwgetRole['view_psd'] == '1' && strtolower($file_row['doc_extn']) == 'psd') {
        ?>
        <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" class="pdfview" data-toggle="tooltip" title="<?php echo $lang['View_File']; ?>">
            <img src="<?= BASE_URL ?>assets/images/psd.png"> <!--<?php echo $lang['View_File']; ?>--></a>
    <?php } else if ($rwgetRole['view_cdr'] == '1' && strtolower($file_row['doc_extn']) == 'cdr') { ?>
        <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" class="pdfview" data-toggle="tooltip" title="<?php echo $lang['View_File']; ?>">
            <img src="<?= BASE_URL ?>assets/images/cdr.png"> <!--<?php echo $lang['View_File']; ?>--></a>
    <?php } else if (strtolower($file_row['doc_extn']) == 'odt' && $rwgetRole['view_odt'] == '1') { ?>
        <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" id="waitImage" data-toggle="tooltip" class="pdfview" title="<?php echo $lang['odt_file']; ?>">
            <i class="fa fa-file" aria-hidden="true"></i></a>
    <?php } else if (strtolower($file_row['doc_extn']) == 'rtf' && $rwgetRole['view_rtf'] == '1') { ?>
        <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" class="pdfview" data-toggle="tooltip" title="<?php echo $lang['rtf_file']; ?>">
            <i class="fa fa-file-word-o" aria-hidden="true"></i></a>
    <?php } else if ((strtolower($file_row['doc_extn']) == 'mp3' || strtolower($file_row['doc_extn']) == 'wav') && $rwgetRole['audio_file'] == '1') { ?> 
        <a href="audioplayer?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank">
            <i class="fa fa-music" data-toggle="tooltip" title="<?php echo $lang['Audio_file']; ?>"></i></a>

    <?php } else if ((strtolower($file_row['doc_extn']) == 'mp4' || strtolower($file_row['doc_extn']) == '3gp'|| strtolower($file_row['doc_extn']) == 'ogg' || strtolower($file_row['doc_extn']) == 'flac' || strtolower($file_row['doc_extn']) == '3g2' || strtolower($file_row['doc_extn']) == 'avi' || strtolower($file_row['doc_extn']) == 'mkv' || strtolower($file_row['doc_extn']) == 'mov' || strtolower($file_row['doc_extn']) == 'mpg' || strtolower($file_row['doc_extn']) == 'wmv' || strtolower($file_row['doc_extn']) == 'webm' || strtolower($file_row['doc_extn']) == 'ogv' || strtolower($file_row['doc_extn']) == 'flv' || strtolower($file_row['doc_extn']) == 'wmv') && $rwgetRole['video_file'] == '1') { ?>
        <a href="video-player?id=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" >
            <i class="fa fa-video-camera" data-toggle="tooltip" title="<?php echo $lang['Video_file']; ?>"></i></a>
        <?php
    } else {
		if ($rwgetRole['pdf_download'] == '1' && isFolderReadable($db_con, $file_row['doc_name'])) { 
			$sql = mysqli_query($db_con, "select * from tbl_storage_level where sl_id='$slid'") or die('Error');
			$pass_check = mysqli_fetch_assoc($sql);

			if ($pass_check['is_protected'] == 1) {
				?>
				<a data-toggle="modal" data-target="#myModal"  data-toggle="tooltip" download title="<?php echo $file_row['old_doc_name']; ?>" onclick="setDownloadDocId('<?php echo urlencode(base64_encode($file_row['doc_id'])) ?>');"> <i class="fa fa-download" id ="download_btn" title="<?php echo $lang['Download']; ?>"></i>
				</a> <?php } else { ?>                         
				<a href="downloaddoc?file=<?php echo urlencode(base64_encode($file_row['doc_id'])) ?>" id="fancybox-inner" target="_blank" download> <i class="ti-import" data-toggle="tooltip" title="<?php echo $lang['Download']; ?>"></i>
				</a>
				<?php
			}
		}
    }
} else {
    ?>
    <a href="javascript:void(0)"  data="<?php echo $file_row['doc_id'] ?>" class="send_lock_request dropdown-toggle profile waves-effect waves-light" data-toggle="dropdown" aria-expanded="true"><i class="fa fa-lock" data-toggle="tooltip" title="<?php echo $lang['lock_folder']; ?>"></i></a>

    <?php
}
?>
