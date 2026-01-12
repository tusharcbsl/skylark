/**
 * http://stackoverflow.com/questions/432493/how-do-you-access-the-matched-groups-in-a-javascript-regular- 
 * expression
 *  examples:
 *
 *  var matches = getRegexMatches(/(dog)/, "dog boat, cat car dog");
 *  console.log(matches);
 *
 *  var matches = getRegexMatches(/(dog|cat) (boat|car)/, "dog boat, cat car");
 *  console.log(matches);
 */
function getRegexMatches(regex, string) {
    if(!(regex instanceof RegExp)) {
        return "ERROR";
    }
    else {
        if (!regex.global) {
            // If global flag not set, create new one.
            var flags = "g";
            if (regex.ignoreCase) flags += "i";
            if (regex.multiline) flags += "m";
            if (regex.sticky) flags += "y";
            regex = RegExp(regex.source, flags);
        }
    }
    var matches = [];
    var match = regex.exec(string);
    while (match) {
        if (match.length > 2) {
            var group_matches = [];
            for (var i = 1; i < match.length; i++) {
                group_matches.push(match[i]);
            }
            matches.push(group_matches);
        }
        else {
            matches.push(match[1]);
        }
        match = regex.exec(string);
    }
    return matches;
}

/**
 * get the select_row or select_col checkboxes dependening on the selectType row/col
 */
function getSelectCheckboxes(selectType) {
  var regex=new RegExp("select_"+selectType+"_");
  var result= $('input').filter(function() {return this.id.match(regex);});
  return result;
}

/**
 * matrix selection logic 
 * the goal is to provide select all / select row x / select col x
 * checkboxes that will allow to 
 *   select all: select all grid elements 
 *   select row: select the grid elements in the given row
 *   select col: select the grid elements in the given col
 *
 *   There is a naming convention for the ids and css style classes of the the selectors and elements:
 *   select all -> id: selectall
 *   select row -> id: select_row_row e.g. select_row_2
 *   select col -> id: select_col_col e.g. select_col_3 
 *   grid element -> class checkBoxClass col_col row_row e.g. checkBoxClass row_2 col_3
 */
$(document).ready(function () {
    // handle click event for Select all check box
    $("#selectall").click(function () {
       // set the checked property of all grid elements to be the same as
       // the state of the SelectAll check box
       var state=$("#selectall").prop('checked');
       $(".checkBoxClass").prop('checked', state);
       getSelectCheckboxes('row').prop('checked', state);
       getSelectCheckboxes('col').prop('checked', state);
    });

    // handle clicks within the grid
    $(".checkBoxClass").on( "click", function() {
      // get the list of grid checkbox elements
      // all checkboxes
      var all = $('.checkBoxClass');
      // all select row check boxes
      var rows = getSelectCheckboxes('row');
      // all select columnn check boxes
      var cols = getSelectCheckboxes('col');
      // console.log("rows: "+rows.length+", cols:"+cols.length+" total: "+all.length);
      // get the total number of checkboxes in the grid
      var allLen=all.length;
      // get the number of checkboxes in the checked state
      var filterLen=all.filter(':checked').length;
      // console.log(allLen+"-"+filterLen);
      // if all checkboxes are in the checked state  
      // set the state of the selectAll checkbox to checked to be able
      // to deselect all at once, otherwise set it to unchecked to be able to select all at once
      if (allLen == filterLen) {
        $("#selectall").prop("checked", true);
      } else {
        $("#selectall").prop("checked", false);
      }
      
      // now check the completeness of the rows
      for (row = 0; row < rows.length; row++) {
        var rowall=$('.row_'+row);
        var rowchecked=rowall.filter(':checked');
        if (rowall.length == rowchecked.length) {
          $("#select_row_"+row).prop("checked", true);
        } else {  
          $("#select_row_"+row).prop("checked", false);
        }
     }
    });
     
    $('input')
      .filter(function() {
        return this.id.match(/select_row_|select_col_/);
    }).on( "click", function() {
      var matchRowColArr=getRegexMatches(/select_(row|col)_([0-9]+)/,this.id);
      var matchRowCol=matchRowColArr[0];
      // console.log(matchRowCol);
      if (matchRowCol.length==2) {
        var selectType=matchRowCol[0];  // e.g. row
        var selectIndex=matchRowCol[1]; // e.g. 2
        // console.log(this.id+" clicked to select "+selectType+" "+selectIndex);
        // e.g. .row_2
        $("."+selectType+"_"+selectIndex)
         .prop('checked', $("#select_"+selectType+"_"+selectIndex).prop('checked'));
     }
    });
  });