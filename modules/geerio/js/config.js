/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(
    function (){
        refeshRadioLink();
        $('.list-page-radio').click(
            function (){
                refeshRadioLink();
            }
        );
        $('.value-for-link').keyup(
        function(){
            var id = $(this).attr('id');
            id = id.replace('value-for-','');
            var type = id.replace('link-','');
            $('#'+id).attr('value',type+'_'+$(this).attr('value'));
        } 
       );
    }
            
);

function refeshRadioLink(){
    $('.value-for-link').prop("disabled",true);
    var get_id_radio = $('.list-page-radio').find('input:checked').attr('id');
    $('#value-for-'+get_id_radio).prop("disabled",false);
}

 