$(document).ready(function(){
    Permisos.load();
});

var Permisos  = (function(){

    var anadirComportamientoCheckPermiso = function(){
        $('.chk-permiso-js').click(function(event){
            $chk_permiso_js = $(this);

            var permiso_id = $chk_permiso_js.data('permission_id');
            var es_chk_permitir = $chk_permiso_js.data('permitir');

            if($chk_permiso_js.is(":checked")){
                if (es_chk_permitir == 1){
                    $('#denegar_' + permiso_id).prop('checked', false);
                }
                else{
                    $('#permitir_' + permiso_id).prop('checked', false);
                }

            }

        })
    };

    return {
        load: function($context){
            anadirComportamientoCheckPermiso();
        },
    }
})();