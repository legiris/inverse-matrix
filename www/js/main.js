$(function(){

   $('#dimension').change(function() {
      $('#table').remove();
      var count = 2;
      var table = '';
      var dimension = $('#dimension').val();

      if (dimension) {
        count = dimension;
      }

      table += '<div id="table"><table>';
      for (var i = 1; i <= count; i++) {
          table += '<tr>';
          for (var j = 1; j <= count; j++) {
              table += '<td><input type="text" size="1" name="matrix['+i+']['+j+']" /></td>';
          }
          table += '</tr>';
      }
      table += '</table></div>';

      $('#formData').append(table);
   }).change();

});
