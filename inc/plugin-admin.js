jQuery(document).ready(function($){  

    var post_type = GetQueryString('post_type');

    if ( post_type == 'jvps' ){
        $('#title-prompt-text').html('PID');
        $('#title').attr("required", "true");
    }
















});

function GetQueryString(name){
    var reg = new RegExp("(^|&)"+ name +"=([^&]*)(&|$)");
    var r = window.location.search.substr(1).match(reg);
    if(r!=null) return unescape(r[2]); return null;
}