//supreme.js

$(document).ready(function() {
    //Function to check balance
    $('.balance-button').click(function() {
        var buttonValue = $(this).attr("value");
        var buttonStore = $(this).prev().prev().attr("value");
        //Create the loading bar element and prepare it to be upgraded into an MDL element
        var loadingBar = document.createElement('div');
        loadingBar.className = "mdl-progress mdl-js-progress mdl-progress__indeterminate";
        loadingBar.id = "loading-bar";
        //MUST upgrade the element when using MDL in a dynamic environment
        componentHandler.upgradeElement(loadingBar);
        $(this).parent().append("<br><br>");
        $(this).parent().append(loadingBar);
        
        //Send a POST request to the check balance script.
        //A successful reponse holds the balance of the card sent in the request.
        var request = $.ajax ({
            url: "php/checkbalance.php",
            method: "POST",
            data: { 
                card_number: buttonValue,
                store: buttonStore
            },
            dataType: "text",
        }).done(function(response){ //On success, remove the loading bars.
            $('#loading-bar').prev().remove();
            $('#loading-bar').prev().remove();
            $('#loading-bar').remove();
            var snackbarContainer = document.querySelector('#snackbar');
            var data = {
                message: response,
                timeout: 10000
            };
            snackbarContainer.MaterialSnackbar.showSnackbar(data);
        }).fail(function() {    //On failure, remove the loading bars and report back the error message.
            $('#loading-bar').prev().remove();
            $('#loading-bar').prev().remove();
            $('#loading-bar').prev().remove();
            var snackbarContainer = document.querySelector('#snackbar');
            var data = {
                message: "Error, balance check failed.",
                timeout: 10000
            };
            snackbarContainer.MaterialSnackbar.showSnackbar(data);
        });

    });
    var dialog = document.getElementById('dialog-box');
    //Function to generate the barcode
    if(! dialog.showModal) {
        dialogPolyfill.registerDialog(dialog);
    }
    $('.barcode-button').click(function() {
        var barCode = $(this).attr("value");
        JsBarcode("#barcode", barCode, {
            width: 3,
            height: 200
        });
        dialog.showModal();
    });
    dialog.querySelector('.close').addEventListener('click', function() {
        dialog.close();
    });

});
