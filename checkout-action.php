
<?php if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['pdf_file'] == '1' && strtolower($file_row['doc_extn']) == 'pdf') { ?>
    <li> <a href="viewer?id=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" id="fancybox-inner" class="pdfview" target="_blank">
            <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>

<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['image_file'] == '1' && strtolower($file_row['doc_extn']) == 'jpg' || strtolower($file_row['doc_extn']) == 'png' || strtolower($file_row['doc_extn']) == 'gif' || strtolower($file_row['doc_extn']) == 'bmp') { ?>
    <li> <a href="imageviewer?uid=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>"  target="_blank">
            <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>
<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['excel_file'] == '1' && strtolower($file_row['doc_extn']) == 'xls') { ?>
    <li> 
	<a href="gdrivedocs?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&perm=<?php echo urlencode(base64_encode('reader')); ?>" target="_blank"><i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
	
   
    </li>
<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['view_ppt_pptx'] == '1' && (strtolower($file_row['doc_extn']) == 'ppt' || strtolower($file_row['doc_extn']) == 'pptx')) { ?>
    <li> 
	<a href="gdrivedocs?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&perm=<?php echo urlencode(base64_encode('reader')); ?>" target="_blank"><i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
	
   
    </li>
<?php  } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['excel_file'] == '1' && strtolower($file_row['doc_extn']) == 'xlsx') { ?>
    <li> 
	<a href="gdrivedocs?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&perm=<?php echo urlencode(base64_encode('reader')); ?>" target="_blank"><i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>
<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['tif_file'] == '1' && (strtolower($file_row['doc_extn']) == 'tiff' || strtolower($file_row['doc_extn']) == 'tif')) { ?>
    <li> <a href="tiff-viewer?id=<?php echo urlencode(base64_encode($_SESSION['cdes_user_id'])); ?>&i=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>"  target="_blank">
            <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>
<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['excel_file'] == '1' && (strtolower($file_row['doc_extn']) == 'doc' || strtolower($file_row['doc_extn']) == 'docx')) { ?>
    <li> 
	<a href="gdrivedocs?uid=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&file=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>&perm=<?php echo urlencode(base64_encode('reader')); ?>" target="_blank"><i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>

<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['view_psd'] == '1' && (strtolower($file_row['doc_extn']) == 'psd')) { ?>
    <li>  <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" class="pdfview">
            <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>
<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['view_cdr'] == '1' && (strtolower($file_row['doc_extn']) == 'cdr')) { ?>
    <li>  <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" class="pdfview">
            <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>
<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['view_odt'] == '1' && (strtolower($file_row['doc_extn']) == 'odt')) { ?>
    <li>  <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" class="pdfview">
            <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>
<?php } else if ($rwgetRole['checkin_checkout'] == '1' && $rwgetRole['view_rtf'] == '1' && (strtolower($file_row['doc_extn']) == 'rtf')) { ?>
    <li>  <a href="viewword?i=<?php echo urlencode(base64_encode($_SESSION[cdes_user_id])); ?>&id=<?php echo urlencode(base64_encode($file_row['doc_id'])); ?>" target="_blank" class="pdfview">
            <i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a>
    </li>
<?php } else { ?>
    <li> <a href="javascript:void(0)" data-toggle="modal" data-target="#editmetadata" id="editMdata" data="<?php echo $file_row['doc_id']; ?>"><i class="fa fa-sign-in"></i> <?php echo $lang['Chk_In']; ?></a></li>
    <?php
}
?>