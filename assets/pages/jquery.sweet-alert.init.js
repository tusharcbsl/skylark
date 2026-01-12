
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
            swal({
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
            swal({
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
            swal({
                title: "Sweet!",
                text: "Here's a custom image.",
                imageUrl: "assets/plugins/bootstrap-sweetalert/thumbs-up.jpg"
            });
        });

        //Auto Close Timer
        $('#sa-close').click(function () {
            swal({
                title: "Auto close alert!",
                text: "I will close in 2 seconds.",
                timer: 2000,
                showConfirmButton: false
            });
        });

        //Primary
        $('#primary-alert').click(function () {
            swal({
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
            swal({
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
            swal({
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
            swal({
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
            swal({
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
    
  function bulkUploadSuccess(){
  swal({
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
function load(page){
    window.location.href=page;
}

function userCreatefailed(){
    swal({
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
    swal({
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

function loginsuccess(page){
    swal({
                title: "",
                text: "Enter OTP",
                type: "success",
                showCancelButton: false,
                cancelButtonClass: 'btn-white btn-md waves-effect',
                confirmButtonClass: 'btn-primary btn-md waves-effect waves-light',
                confirmButtonText: 'submit',
                input: 'text',
                inputPlaceholder: 'Enter OTP',
                inputValue: '',
                inputValidator: '',
                inputId:'otpvalidate'
            }).then(function () {
  swal(
    load(page)
  )
})
}


function loginfailed(page){
    swal({
                title: "OOPS?",
                text: "Username or Password is invalid!!!",
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

function metasuccess(page){
    swal({
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
function metafailed(page){
    swal({
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


function taskFailed(page,msg){
    swal({
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


function taskSuccess(page,msg){
    swal({
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



function uploadSuccess(page,msg){
    swal({
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

function uploadFailure(page,msg){
    swal({
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
function sessionLogout(page,msg){
    var timer = 10, // timer in seconds
    isTimerStarted = false;

(function customSwal() {
    swal({  
        title: "Please Wait !",
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
        setTimeout(customSwal, 1000);
    }
})();

}



