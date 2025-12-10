$(document).ready(function () {
    inputValidation.load()
});

var inputValidation = (function () {

    var checkPhone = function() {
        $('.phoneValidation').on('keydown', function(event) {
          let key = event.key;
          let validCharacters = /^[0-9+\- ]*$/;
          let allowedKeys = ['ArrowLeft', 'ArrowRight', 'Backspace', 'Delete'];
      
          if (!key.match(validCharacters) && !allowedKeys.includes(key)) {
            event.preventDefault();
          }
        });
      };

    return {
        load: function () {
            checkPhone();
        }
    }

})();
