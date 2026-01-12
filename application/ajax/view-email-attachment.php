<?php
require_once '../../sessionstart.php';
if (!isset($_SESSION['cdes_user_id'])) {
    header("location:../../logout.php");
}
require './../config/database.php';
if (isset($_SESSION['lang'])) {
    $file = "../../" . $_SESSION['lang'] . ".json";
} else {
    $file = "../../English.json";
}
$data = file_get_contents($file);
$lang = json_decode($data, true);
$userId = $_POST['UID'];
$mailId = $_POST['MID'];
$mailAttachement = $_POST['MAID'];
?>
<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th><?= $lang['SNO']; ?></th>
            <th><?= $lang['document_name']; ?></th>
            <th><?= $lang['view']; ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if ($mailAttachement != 0) {
            $attachedPath1 = '../../extract-here/emailattachment/' . $userId . '/' . $mailId . '/';
            $attachedPath = 'extract-here/emailattachment/' . $userId . '/' . $mailId . '/';
            $files = scandir($attachedPath1);
            for ($i = 2; $i < count($files); $i++) {
                $filname = $files[$i];
                $filextn = substr($filname, strrpos($filname, '.') + 1);
                ?>
                <tr>
                    <td style="width:60px"><?php echo $i - 1 . '.'; ?></td>
                    <td><?php echo $files[$i]; ?></td>
                    <td>
                        <?php if ($filname != "Thumbs.db") { ?>
                            <?php if (strtolower($filextn) == 'pdf') { ?>
                                <a href="<?php echo $attachedPath . $files[$i]; ?>" id="fancybox-inner" target="_blank">
                                    <i class="fa fa-file-pdf-o"></i></a>

                            <?php } else if (strtolower($filextn) == 'jpg' || strtolower($filextn) == 'png' || strtolower($filextn) == 'gif' || strtolower($filextn) == 'bmp' || strtolower($filextn) == 'jpeg') { ?>

                                <a href="<?php echo $attachedPath . $files[$i]; ?>"  target="_blank">
                                    <i class="fa fa-file-image-o"></i> <?php echo $lang['Image']; ?> </a>

                            <?php } else if (strtolower($filextn) == 'tif' || strtolower($filextn) == 'tiff') { ?>

                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank"><i class="fa fa-picture-o"></i></a>

                            <?php } else if (strtolower($filextn) == 'xlsx') { ?>

                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank"> <i class="fa fa-file-excel-o"></i></a>

                            <?php } else if (strtolower($filextn) == 'xls') {
                                ?>
                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank"> <i class="fa fa-file-excel-o"></i></a>

                            <?php } else if (strtolower($filextn) == 'doc' || strtolower($filextn) == 'docx') { ?>

                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank"> <i class="fa fa-file-word-o"></i></a>

                            <?php } else if (strtolower($filextn) == 'psd') { ?>
                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank" class="pdfview"> <img src="<?= BASE_URL ?>assets/images/psd.png"></a>
                            <?php } else if (strtolower($filextn) == 'cdr') { ?>
                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank" class="pdfview"> <img src="<?= BASE_URL ?>assets/images/cdr.png"></a>
                            <?php } else if (strtolower($filextn) == 'odt') { ?>
                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank" class="pdfview"> <i class="fa fa-file" aria-hidden="true"></i></a>
                            <?php } else if (strtolower($filextn) == 'rtf') { ?>
                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank" class="pdfview"> <i class="fa fa-file-word-o" aria-hidden="true"></i></a>
                            <?php } else if (strtolower($filextn) == 'mp3') { ?>

                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank" class="pdfview"> <i class="fa fa-music"></i></a>
                            <?php } else if (strtolower($filextn) == 'mp4') { ?>
                                <a href="<?php echo $attachedPath . $files[$i]; ?>" target="_blank" class="pdfview"> <i class="fa fa-video-camera"></i></a>
                            <?php } else { ?>
                                <a href="<?php echo $attachedPath . $files[$i]; ?>" id="fancybox-inner" target="_blank" download>
                                    <?php echo $files[$i]; ?> <i class="fa fa-download"></i></a>
                                    <?php
                                }
                            }
                            ?>
                    </td>
                </tr>
            <?php }
        } else {
            ?>
            <tr>
                <td class="text-danger text-center" colspan="3"><?php echo $lang['no_email_attachement_founds']; ?></td>
            </tr> 
<?php } ?>
    </tbody>
</table>