// Opera 8.0+
var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;

// Firefox 1.0+
var isFirefox = typeof InstallTrigger !== 'undefined';

// Safari 3.0+ "[object HTMLElementConstructor]" 
var isSafari = /constructor/i.test(window.HTMLElement) || (function (p) {
    return p.toString() === "[object SafariRemoteNotification]";
})(!window['safari'] || (typeof safari !== 'undefined' && safari.pushNotification));

// Internet Explorer 6-11
var isIE = /*@cc_on!@*/false || !!document.documentMode;

// Edge 20+
var isEdge = !isIE && !!window.StyleMedia;

// Chrome 1 - 71
var isChrome = !!window.chrome && (!!window.chrome.webstore || !!window.chrome.runtime);

// Blink engine detection
var isBlink = (isChrome || isOpera) && !!window.CSS;


/* var output = 'Detecting browsers by ducktyping:<hr>';
 output += 'isFirefox: ' + isFirefox + '<br>';
 output += 'isChrome: ' + isChrome + '<br>';
 output += 'isSafari: ' + isSafari + '<br>';
 output += 'isOpera: ' + isOpera + '<br>';
 output += 'isIE: ' + isIE + '<br>';
 output += 'isEdge: ' + isEdge + '<br>';
 output += 'isBlink: ' + isBlink + '<br>';
 document.body.innerHTML = output; */

function load(page) {
    window.location.href = page;
}
if (isSafari != true)
{
    function SHA1(r) {
        function o(r, o) {
            return r << o | r >>> 32 - o
        }
        function e(r) {
            var o, e = "";
            for (o = 7; o >= 0; o--)
                e += (r >>> 4 * o & 15).toString(16);
            return e
        }
        var t, a, h, n, C, c, f, d, A, u = new Array(80), g = 1732584193, i = 4023233417, s = 2562383102, S = 271733878, m = 3285377520, p = (r = function (r) {
            r = r.replace(/\r\n/g, "\n");
            for (var o = "", e = 0; e < r.length; e++) {
                var t = r.charCodeAt(e);
                t < 128 ? o += String.fromCharCode(t) : t > 127 && t < 2048 ? (o += String.fromCharCode(t >> 6 | 192), o += String.fromCharCode(63 & t | 128)) : (o += String.fromCharCode(t >> 12 | 224), o += String.fromCharCode(t >> 6 & 63 | 128), o += String.fromCharCode(63 & t | 128))
            }
            return o
        }(r)).length, l = new Array;
        for (a = 0; a < p - 3; a += 4)
            h = r.charCodeAt(a) << 24 | r.charCodeAt(a + 1) << 16 | r.charCodeAt(a + 2) << 8 | r.charCodeAt(a + 3), l.push(h);
        switch (p % 4) {
            case 0:
                a = 2147483648;
                break;
            case 1:
                a = r.charCodeAt(p - 1) << 24 | 8388608;
                break;
            case 2:
                a = r.charCodeAt(p - 2) << 24 | r.charCodeAt(p - 1) << 16 | 32768;
                break;
            case 3:
                a = r.charCodeAt(p - 3) << 24 | r.charCodeAt(p - 2) << 16 | r.charCodeAt(p - 1) << 8 | 128
        }
        for (l.push(a); l.length % 16 != 14; )
            l.push(0);
        for (l.push(p >>> 29), l.push(p << 3 & 4294967295), t = 0; t < l.length; t += 16) {
            for (a = 0; a < 16; a++)
                u[a] = l[t + a];
            for (a = 16; a <= 79; a++)
                u[a] = o(u[a - 3] ^ u[a - 8] ^ u[a - 14] ^ u[a - 16], 1);
            for (n = g, C = i, c = s, f = S, d = m, a = 0; a <= 19; a++)
                A = o(n, 5) + (C & c | ~C & f) + d + u[a] + 1518500249 & 4294967295, d = f, f = c, c = o(C, 30), C = n, n = A;
            for (a = 20; a <= 39; a++)
                A = o(n, 5) + (C ^ c ^ f) + d + u[a] + 1859775393 & 4294967295, d = f, f = c, c = o(C, 30), C = n, n = A;
            for (a = 40; a <= 59; a++)
                A = o(n, 5) + (C & c | C & f | c & f) + d + u[a] + 2400959708 & 4294967295, d = f, f = c, c = o(C, 30), C = n, n = A;
            for (a = 60; a <= 79; a++)
                A = o(n, 5) + (C ^ c ^ f) + d + u[a] + 3395469782 & 4294967295, d = f, f = c, c = o(C, 30), C = n, n = A;
            g = g + n & 4294967295, i = i + C & 4294967295, s = s + c & 4294967295, S = S + f & 4294967295, m = m + d & 4294967295
        }
        return(A = e(g) + e(i) + e(s) + e(S) + e(m)).toLowerCase()
    }
    /**
     * Theme: Ubold Admin Template
     * Author: Coderthemes
     * SweetAlert
     */

    !function ($) {
        "use strict";

        var SweetAlert = function () {
        };

        //examples
        SweetAlert.prototype.init = function () {

            //Basic
            $('#sa-basic').click(function () {
                swal("Here's a message!");
            });

            //A title with a text under
            $('#sa-title').click(function () {
                swal("Here's a message!", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat, tincidunt vitae ipsum et, pellentesque maximus enim. Mauris eleifend ex semper, lobortis purus sed, pharetra felis")
            });

            //Success Message
            $('#sa-success').click(function () {
                swal("Good job!", "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed lorem erat, tincidunt vitae ipsum et, pellentesque maximus enim. Mauris eleifend ex semper, lobortis purus sed, pharetra felis", "success")
            });

            //Warning Message
            $('#sa-warning').click(function () {
                swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this imaginary file!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: 'btn-warning',
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                }, function () {
                    swal("Deleted!", "Your imaginary file has been deleted.", "success");
                });
            });

            //Parameter
            $('#sa-params').click(function () {
                swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this imaginary file!",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "No, cancel plx!",
                    closeOnConfirm: false,
                    closeOnCancel: false
                }, function (isConfirm) {
                    if (isConfirm) {
                        swal("Deleted!", "Your imaginary file has been deleted.", "success");
                    } else {
                        swal("Cancelled", "Your imaginary file is safe :)", "error");
                    }
                });
            });

            //Custom Image
            $('#sa-image').click(function () {
                swal.fire({
                    title: "Sweet!",
                    text: "Here's a custom image.",
                    imageUrl: "assets/plugins/bootstrap-sweetalert/thumbs-up.jpg"
                });
            });

            //Auto Close Timer
            $('#sa-close').click(function () {
                swal.fire({
                    title: "Auto close alert!",
                    text: "I will close in 2 seconds.",
                    timer: 2000,
                    showConfirmButton: false
                });
            });

            //Primary
            $('#primary-alert').click(function () {
                swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this imaginary file!",
                    type: "info",
                    showCancelButton: true,
                    cancelButtonClass: 'btn-white btn-md waves-effect',
                    confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
                    confirmButtonText: 'Primary!'
                });
            });

            //Info
            $('#info-alert').click(function () {
                swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this imaginary file!",
                    type: "info",
                    showCancelButton: true,
                    cancelButtonClass: 'btn-white btn-md waves-effect',
                    confirmButtonClass: 'btn-info btn-md waves-effect waves-light',
                    confirmButtonText: 'Info!'
                });
            });

            //Success
            $('#success-alert').click(function () {
                swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this imaginary file!",
                    type: "success",
                    showCancelButton: true,
                    cancelButtonClass: 'btn-white btn-md waves-effect',
                    confirmButtonClass: 'btn-success btn-md waves-effect waves-light',
                    confirmButtonText: 'Success!'
                });
            });

            //Warning
            $('#warning-alert').click(function () {
                swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this imaginary file!",
                    type: "warning",
                    showCancelButton: true,
                    cancelButtonClass: 'btn-white btn-md waves-effect',
                    confirmButtonClass: 'btn-warning btn-md waves-effect waves-light',
                    confirmButtonText: 'Warning!'
                });
            });

            //Danger
            $('#danger-alert').click(function () {
                swal.fire({
                    title: "Are you sure?",
                    text: "You will not be able to recover this imaginary file!",
                    type: "error",
                    showCancelButton: true,
                    cancelButtonClass: 'btn-white btn-md waves-effect',
                    confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
                    confirmButtonText: 'Danger!'
                });
            });


        },
                //init
                $.SweetAlert = new SweetAlert, $.SweetAlert.Constructor = SweetAlert
    }(window.jQuery),
//initializing
            function ($) {
                "use strict";
                $.SweetAlert.init()
            }(window.jQuery);

    function bulkUploadSuccess() {
        swal.fire({
            title: 'file Uploading successful!',
            text: "",
            type: 'success',
            showCancelButton: false,
            confirmButtonText: 'Confirm',
            cancelButtonText: 'No, cancel!',
            confirmButtonClass: 'btn-success btn-md waves-effect waves-light',
            cancelButtonClass: 'btn btn-danger',

        }).then(function () {
            swal(
                    load('index.php')
                    )
        }, function (dismiss) {
            // dismiss can be 'cancel', 'overlay',
            // 'close', and 'timer'
            if (dismiss === 'cancel') {
                swal(
                        'Cancelled',
                        'Your imaginary file is safe :)',
                        'error'
                        )
            }
        })
    }
    function userCreatefailed() {
        swal.fire({
            title: "OOPS?",
            text: "please enter valid email id!",
            type: "error",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
            confirmButtonText: 'Ok'
        });
    }
    /*
     function loginsuccess(page){
     swal.fire({
     title: "Success?",
     text: "You have logged in successfully.",
     type: "success",
     showCancelButton: false,
     cancelButtonClass: 'btn-white btn-md waves-effect',
     confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
     confirmButtonText: 'Ok'
     }).then(function () {
     swal(
     load(page)
     )
     })
     }
     */

    function loginSuccess(page, msg) {
        swal.fire({
            title: "Success!",
            text: msg,
            type: "success",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
            confirmButtonText: 'submit',
            input: 'text',
            inputPlaceholder: 'Enter OTP',
            inputValue: '',
            inputValidator: '',
            inputId: 'otpvalidate'
        }).then(function () {
            swal(
                    load(page)
                    )
        })
    }


    function loginfailed(page) {
        swal.fire({
            title: "OOPS?",
            text: "Username or Password is invalid.",
            type: "error",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
            confirmButtonText: 'Ok'
        }).then(function () {
            swal(
                    load(page)
                    )
        })
    }

    function metasuccess(page) {
        swal.fire({
            title: "Success!",
            text: "Metadata Assigned Successfully",
            type: "success",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
            confirmButtonText: 'Ok'
        }).then(function () {
            swal(
                    load(page)
                    )
        })
    }
    function metafailed(page) {
        swal.fire({
            title: "OOPS?",
            text: "failed to assign meta. already assigned!!!",
            type: "error",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
            confirmButtonText: 'Ok'
        }).then(function () {
            swal(
                    load(page)
                    )
        })
    }


    function taskFailed(page, msg) {
        swal.fire({
            title: "OOPS?",
            text: msg,
            type: "error",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-danger btn-md waves-effect waves-light',
            confirmButtonText: 'Ok'
        }).then(function () {
            swal(
                    load(page)
                    )
        })
    }


    function taskSuccess(page, msg) {
        swal.fire({
            title: "Success!",
            text: msg,
            type: "success",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
            confirmButtonText: 'Ok'
        }).then(function () {
            swal(
                    load(page)
                    )
        })
    }



    function uploadSuccess(page, msg) {
        swal.fire({
            title: "Success!",
            text: msg,
            type: "success",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
            confirmButtonText: 'Ok'
        }).then(function () {
            swal(
                    load(page)
                    )
        })
    }

    function uploadFailure(page, msg) {
        swal.fire({
            title: "Success!",
            text: msg,
            type: "success",
            showCancelButton: false,
            cancelButtonClass: 'btn-white btn-md waves-effect',
            confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
            confirmButtonText: 'Ok'
        }).then(function () {
            swal(
                    load(page)
                    )
        })
    }

    /* function sessionLogout(page,msg){
     var timer = 10, // timer in seconds
     isTimerStarted = false;
     
     (function customSwal() {
     swal.fire({  
     title: "Please wdfgdfgait !",
     text: "Your login time about to end..." + timer,
     timer: !isTimerStarted ? timer * 1000 : undefined,
     showConfirmButton: true,
     confirmButtonText: 'Cancel'
     }).catch(function(){
     load(page);
     })
     isTimerStarted = true;
     if(timer) {
     timer--;
     setTimeout(customSwal, 10000);
     }
     })();
     
     } 
     function sessionLogout(page,msg){
     var title="<strong>Please zxcsdfsdfsd!</strong>";
     $("#btnccals").trigger("click");
     $('#safarimyModal').modal("show");
     var mark='<button type="button" class="close" style="float:none !important; text-align:center;" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle-o" aria-hidden="true" style="color:red!important;font-size:50px;"></i></button> ';
     var sbmtbtn='<button type="button" class="btn-danger btn-md waves-effect waves-light text-center"  id="finish" >OK</button>&nbsp;&nbsp;<a href="index" id="cdnbtn" class="btn btn-primary btn-infon waves-effect waves-light btn-lg text-white">Cancel</a>';
     $('#iconsim').html(mark);
     $('#modaltitle').html(title);
     $('#sbmtbtn').html(sbmtbtn);	
     $('#abc').html('Your login time about to end within 10 seconds');
     $("#finish").click(function (){
     $('#safarimyModal').modal("hide");
     load(page);
     });		
     $("#cdnbtn").click(function (){
     load('index');
     });
     setTimeout( function() {load(page)}, 10000);
     
     
     }
     */
} else
{
    function taskSuccess(page, msg) {
        var title = "<strong>Success!</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<i class="fa fa-check-circle-o" aria-hidden="true" style="color:#00FF7F!important;font-size:50px;"></i>';
        var sbmtbtn = '<button type="button" class="btn-primary btn-md waves-effect waves-light text-center" id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html(msg);
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });

    }
    function uploadSuccess(page, msg) {
        var title = "<strong>Success!</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<i class="fa fa-check-circle-o" aria-hidden="true" style="color:#00FF7F!important;font-size:50px;"></i>';
        var sbmtbtn = '<button type="button" class="btn-primary btn-md waves-effect waves-light text-center" id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html(msg);
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });

    }
    function taskFailed(page, msg) {
        var title = "<strong>OOPS?</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<button type="button" class="close" style="float:none !important; text-align:center;" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle-o" aria-hidden="true" style="color:red!important;font-size:50px;"></i></button> ';
        var sbmtbtn = '<button type="button" class="btn-danger btn-md waves-effect waves-light text-center"  id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html(msg);
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });
    }
    function userCreatefailed() {
        var title = "<strong>OOPS?</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<button type="button" class="close" style="float:none !important; text-align:center;" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle-o" aria-hidden="true" style="color:red!important;font-size:50px;"></i></button> ';
        var sbmtbtn = '<button type="button" class="btn-danger btn-md waves-effect waves-light text-center"  id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html('please enter valid email id!');
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });
    }
    function loginSuccess(page, msg) {
        var title = "<strong>Success!</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<i class="fa fa-check-circle-o" aria-hidden="true" style="color:#00FF7F!important;font-size:50px;"></i>';
        var sbmtbtn = '<button type="button" class="btn-primary btn-md waves-effect waves-light text-center" id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html(msg);
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });

    }
    function loginfailed(page) {
        var title = "<strong>OOPS?</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<button type="button" class="close" style="float:none !important; text-align:center;" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle-o" aria-hidden="true" style="color:red!important;font-size:50px;"></i></button> ';
        var sbmtbtn = '<button type="button" class="btn-danger btn-md waves-effect waves-light text-center"  id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html('Username or Password is invalid.!');
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });
    }
    function metasuccess(page) {
        var title = "<strong>Success!</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<i class="fa fa-check-circle-o" aria-hidden="true" style="color:#00FF7F!important;font-size:50px;"></i>';
        var sbmtbtn = '<button type="button" class="btn-primary btn-md waves-effect waves-light text-center" id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html('Metadata Assigned Successfully');
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });

    }
    function metafailed(page) {
        var title = "<strong>OOPS?</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<button type="button" class="close" style="float:none !important; text-align:center;" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle-o" aria-hidden="true" style="color:red!important;font-size:50px;"></i></button> ';
        var sbmtbtn = '<button type="button" class="btn-danger btn-md waves-effect waves-light text-center"  id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html('failed to assign meta. already assigned!!!');
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });
    }
    function uploadFailure(page, msg) {
        var title = "<strong>OOPS?</strong>";
        $("#btnccals").trigger("click");
        $('#safarimyModal').modal("show");
        var mark = '<button type="button" class="close" style="float:none !important; text-align:center;" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle-o" aria-hidden="true" style="color:red!important;font-size:50px;"></i></button> ';
        var sbmtbtn = '<button type="button" class="btn-danger btn-md waves-effect waves-light text-center"  id="finish" >OK</button>';
        $('#iconsim').html(mark);
        $('#modaltitle').html(title);
        $('#sbmtbtn').html(sbmtbtn);
        $('#abc').html(msg);
        $("#finish").click(function () {
            $('#safarimyModal').modal("hide");
            load(page);
        });

    }
}

function sessionLogout(page, msg) {
    document.getElementById('timer').innerHTML =
            00 + ":" + 10;
    startTimer();
    function startTimer() {
        var presentTime = document.getElementById('timer').innerHTML;
        var timeArray = presentTime.split(/[:]+/);
        var m = timeArray[0];
        var s = (timeArray[1] - 1);
        m = 00
        if (s > -1) {
            document.getElementById('timer').innerHTML =
                    m + ":" + 0 + s;
        }
        setTimeout(startTimer, 1000);
    }

    // var title = "<strong style='font-size: 20px;'>Need More Time?</strong>";
    $("#btnccals").trigger("click");
    $('#safarimyModal').modal("show");
    //var mark = '<button type="button" class="close" style="float:none !important; text-align:center;" data-dismiss="modal" aria-hidden="true"><i class="fa fa-times-circle-o" aria-hidden="true" style="color:red!important;font-size:50px;"></i></button> ';
    var sbmtbtn = '<button type="button" class="btn-danger btn waves-effect waves-light text-center"  id="finish" style="border-radius:3px;">Sign out</button>&nbsp;&nbsp;<a href="index" id="cdnbtn" class="btn btn-primary btn-infon waves-effect waves-light btn text-white">Stay Signed in</a>';
    //$('#iconsim').html(mark);
    //$('.panel-title').html(title);
    $('#sbmtbtn').html(sbmtbtn);
    $('#abc').html('Your session is about to expire, You will automatically signed out in');
    $("#finish").click(function () {
        $('#safarimyModal').modal("hide");
        load(page);
    });
    $("#cdnbtn").click(function () {
        load('index');
    });
    setTimeout(
            function ()
            {
                load(page)
            }, 10000);


}