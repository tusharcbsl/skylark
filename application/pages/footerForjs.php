<!-- jQuery  -->
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/detect.js"></script>
<script src="assets/js/fastclick.js"></script>
<script src="assets/js/jquery.slimscroll.js"></script>
<script src="assets/js/jquery.blockUI.js"></script>
<!--script src="assets/js/waves.js"></script-->
<script src="assets/js/wow.min.js"></script>
<script src="assets/js/jquery.nicescroll.js"></script>
<script src="assets/js/jquery.scrollTo.min.js"></script>
<script src="assets/plugins/peity/jquery.peity.min.js"></script>
<!-- jQuery  -->
<script src="assets/plugins/waypoints/lib/jquery.waypoints.js"></script>
<script src="assets/plugins/counterup/jquery.counterup.min.js"></script>

<script src="assets/js/jquery.core.js"></script>
<script src="assets/js/jquery.app.js"></script>

<!-- Sweet-Alert  -->
<script src="assets/plugins/sweetalert2/sweetalert2.min.js"></script>
<script src="assets/pages/jquery.sweet-alert.init.js"></script>

<script src="assets/plugins/notifyjs/js/notify.js"></script>
<script src="assets/plugins/notifications/notify-metro.js"></script>
<!---editable modified storage level js code-->
<script src="assets/plugins/magnific-popup/js/jquery.magnific-popup.min.js"></script>
<script src="assets/plugins/jquery-datatables-editable/jquery.dataTables.js"></script> 
<script src="assets/plugins/datatables/dataTables.bootstrap.js"></script>
<script src="assets/plugins/tiny-editable/mindmup-editabletable.js"></script>
<script src="assets/plugins/tiny-editable/numeric-input-example.js"></script>
<script src="assets/pages/datatables.editable.init.js"></script>
<!--- for sorting table according to records --start-->
<script src="assets/js/sort-table.js"></script>
<script src="assets/js/sort-table.min.js"></script>
<!-------------Lock File COde start------------>
<?php require_once './lockfile_html.php'; ?>
<?php require_once './lock_file_action.php'; ?>
<!-------------Lock File COde end------------>


<?php
if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
    $link = "https";
else
    $link = "http";

// Here append the common URL characters. 
$link .= "://";

// Append the host(domain name, ip) to the URL. 
$link .= $_SERVER['HTTP_HOST'];

// Append the requested resource location to the URL 
$link .= xss_clean($_SERVER['REQUEST_URI']);
?>
<!--Start Show Session Expire Warning Popup here -->
<div class="modal fade" id="session-expire-warning-modal" aria-hidden="true" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">                  
                <h4 class="modal-title">Session Expire Warning</h4>
            </div>
            <div class="modal-body">
                Your session will expire in <span id="seconds-timer"></span> seconds. 
            </div>
            <div class="modal-footer">
                <!--<button id="btnOk" type="button" class="btn btn-default btn-success" style="padding: 6px 12px; margin-bottom: 0; font-size: 14px; font-weight: normal; border: 1px solid transparent; border-radius: 4px;  background-color: #428bca; color: #FFF;">Ok</button>-->
                <button id="btnSessionExpiredCancelled"  value="<?php echo $link; ?>" class="btn btn-default btn-primary" data-dismiss="modal" style="padding: 6px 12px; margin-bottom: 0; font-size: 14px; font-weight: normal; border: 1px solid transparent; border-radius: 4px; background-color: #428bca; color: #FFF;">Cancel</button>
                <button id="btnLogoutNow" type="button" class="btn btn-default btn-danger" style="padding: 6px 12px; margin-bottom: 0; font-size: 14px; font-weight: normal; border: 1px solid transparent; border-radius: 4px;  background-color: #428bca; color: #FFF;">Logout now</button>
            </div>
        </div>
    </div>
</div>
<!--End Show Session Expire Warning Popup here -->
<!--Start Show Session Expire Popup here -->
<div class="modal fade" id="session-expired-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Session Expired</h4>
            </div>
            <div class="modal-body">
                Your session is expired.
            </div>
            <div class="modal-footer">
                <button id="btnExpiredOk" onclick="sessionExpiredRedirect()" type="button" class="btn btn-primary" data-dismiss="modal" style="padding: 6px 12px; margin-bottom: 0; font-size: 14px; font-weight: normal; border: 1px solid transparent; border-radius: 4px; background-color: #428bca; color: #FFF;">Ok</button>
            </div>
        </div>
    </div>
</div>
<script src="assets/pages/sessionTimeout.js"></script>
<div id="notifi"></div>

<script>
                    var logoutTimer = setInterval(function () {
                        warning();
                    }, 5000);
//notification of time elapse of task
                    function warning() {
                        $.post("application/ajax/notifications.php", {}, function (result, status) {
                            if (status == 'success') {
                                $('#notifi').html(result);
                                //alert(result);
                            }
                        });
                        $.post("application/ajax/lastActive.php", {lgt: 1}, function (result, status) {
                            if (result == '2') {
                                clearInterval(logoutTimer);
                                initSessionMonitor();

                            }
                        });
                    }
					
					//Restric/disabled F5/Cntrl+F5 click in whole application js
					// slight update to account for browsers not supporting e.which
						/* function disableF5(e) {
							if ((e.which || e.keyCode) == 116 || e.ctrlKey && e.keyCode == 82)
								e.preventDefault();
						} 
						$(document).ready(function () {
							$(document).on("keydown", disableF5);
						});*/
						//Restric/disabled right click in whole application js
						/* $(document).ready(function () {
							$("html").bind("contextmenu", function (e) {
								e.preventDefault();
							});
						}); */


</script>
<script>
    $(document).ready(function () {

        $("#lang").change(function () {
            var lang = $(this).val();
            $.post("lang.php", {lang: lang}, function (result, status) {
                if (status == 'success') {
                    location.reload();
                }
            });

        });

        $('.respecialchar').keyup(function ()
        {
            var groupName = $(this).val();
            re = /[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi;
            var isSplChar = re.test(groupName);
            if (isSplChar)
            {
                var no_spl_char = groupName.replace(/[`~!@#$%^&*()_|+\-=?;:'",.<>\{\}\[\]\\\/]/gi, '');
                $(this).val(no_spl_char);
            }
        });
    });
</script>
<script>
    $(document).ready(function () {
        $('<input>').attr({type: 'hidden', value: '<?php echo csrfToken::generate(); ?>', name: 'token'}).appendTo('form');
    });
    function getToken() {

        $.post("application/ajax/common.php", {action: 'getToken'}, function (result, status) {
            if (status == 'success') {
                var myObj = JSON.parse(result);

                $("input[name='token']").val(myObj.token);
            }
        });
    }
</script>

<script>
    function getUserIP(onNewIP) { //  onNewIp - your listener function for new IPs
        //compatibility for firefox and chrome
        var myPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
        var pc = new myPeerConnection({
            iceServers: []
        }),
                noop = function () {},
                localIPs = {},
                ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/g,
                key;

        function iterateIP(ip) {
            if (!localIPs[ip])
                onNewIP(ip);
            localIPs[ip] = true;
        }

        //create a bogus data channel
        pc.createDataChannel("");

        // create offer and set local description
        pc.createOffer(function (sdp) {
            sdp.sdp.split('\n').forEach(function (line) {
                if (line.indexOf('candidate') < 0)
                    return;
                line.match(ipRegex).forEach(iterateIP);
            });

            pc.setLocalDescription(sdp, noop, noop);
        }, noop);

        //listen for candidate events
        pc.onicecandidate = function (ice) {
            if (!ice || !ice.candidate || !ice.candidate.candidate || !ice.candidate.candidate.match(ipRegex))
                return;
            ice.candidate.candidate.match(ipRegex).forEach(iterateIP);
        };
    }

    function addTranslationClass() {

        $("input[type=text]").addClass('translatetext');
    }

</script>
<div id="help-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog"> 
        <div class="modal-content">
            <div class="modal-header"> 
                <h4 class="modal-title"><?php echo $lang['help_description'] . '?'; ?></h4> 
            </div>
            <div class="modal-body" id="helpmodalModify">											
                <img src="assets/images/load.gif" alt="load" class="img-responsive center-block" />
            </div> 

            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal"><?php echo $lang['Close']; ?></button> 
            </div>  

        </div> 
    </div>
</div>
<script>
    $("a#helpview").click(function () {
        var id = $(this).attr('data');
        $.post("application/ajax/viewHelp.php", {ID: id}, function (result, status) {
            if (status == 'success') {
                //alert(result);
                $("#helpmodalModify").html(result);
            }
        });
    });
</script>