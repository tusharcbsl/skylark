



$('.selectUser').click(function () {
var arrUser = [];

  $('input.selectUser:checked').each(function () {
    arrUser.push($(this).val());
  });

  //alert(arr);
   // alert( " Value: " + selectedValue);
       
    $('#seluser').text("Selected User:-" + " " +arrUser);
                
  
});


$('.selectGroup').click(function () {
var arrGroup = [];
 
  $('input.selectGroup:checked').each(function () {
    arrGroup.push($(this).val());
  });       
   $('#selgroup').text("Selected Group:-" + " " +arrGroup);
                
  
});


$('.selectAction').click(function () {
var arrAction = [];
 
  $('input.selectAction:checked').each(function () {
    arrAction.push($(this).val());
  });       
   $('#selaction').text("Selected Action:-" + " " +arrAction);
                
  
});



 $("#depth_level").change(function(){
     
   
    var lbl=$(this).val();
    //alert(lbl);
    $.post("application/ajax/slparentList.php", {level:lbl}, function(result,status){
            if(status=='success'){
                $("#allpar").html(result);
                 var selectedValue = $("#depth_level").val();
               
              // alert( " Value: " + selectedValue);
       
             $('#showlevel').text("Storage Level:-" + " " +selectedValue+ " > ");
                
               
            }
              
        }); 
        
             
}); 




 $("#parent").change(function(){
    var slId=$(this).val();
   // alert(slId);
    $.post("application/ajax/slchildList.php", {sl_id:slId}, function(result,status){
            if(status=='success'){
                $("#childall").html(result);
                
                var selectedText = $("#parent").find("option:selected").text();
               
              // alert($("#depth_level").val());
               
               $('#showparent').text(selectedText+ " > ").prepend("Storage Level:-" + " " + $("#depth_level").val()+ " > ");
                
            }
        }); 
});

function chidepth(){

var sel = document.getElementById("child_level");

var text = sel.options[sel.selectedIndex].text;




document.getElementById("showchild").innerHTML =  " " +text;

}
 
 /*
  $('.selectUser').click(function () {
    
     $('input.selectUser:checked').each(function () {
  $.ajax({
    type: 'GET',
    url: './application/ajax/getData.php?id=' + $(this).attr("id"), // the id gets passed here
     
    success: function(data) {
         // alert(data); 
       
     $('#selgroup').html("Selected Group:-"+ " " + data);
  }
      // $('#selgroup').text("Selected Group:-" + " " + data);
    });
  });
  });
  */