function isMobile(){
    // credit to Timothy Huang for this regex test:
    // https://dev.to/timhuang/a-simple-way-to-detect-if-browser-is-on-a-mobile-device-with-javascript-44j3
    if(/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)){
        return true
    }
    else{
        return false
    }
}

$(document).ready(function () {
    console.log("scripts.js is ready!");

    const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
    const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

    var liveSite = "https://" + window.location.hostname + "/";
    $('.toast').toast('show');
    $('[data-toggle="tooltip"]').tooltip();
    $('.chosen').chosen({});

    if (isMobile()) {
        $(".chosenRequired").removeClass("chosenRequired");
    }

    $('#sidebarCollapse').on('click', function () {
        $('#sidebar').toggleClass('active');
        if ($("#sidebar").is(".active")) {
            $('#content').css('width', '100%');
        } else {
            $('#content').css('width', 'calc(100% - 15rem)');
        }
    });


    var play = 0;
    $('#vista .overlay').on('click', function () {
        if (play == 0) {
            $('#vista').find('audio').prop('muted', false).get(0).play();
            play = 1;
        } else {
            $('#vista').find('audio').prop('muted', false).get(0).pause();
            play = 0;
        }
    });

    //GESTIONE MODAL DELETE: passaggio dei parametri
    $('#modalElimina').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id = button.data('id');
        var nomeElemento = button.data('nome');
        var sezione = button.data('sezione');
        var sezioneBody = button.data('sezione');
        console.log(id, sezione);

        if (sezione == "clienti") {
            sezioneBody = "il cliente";
        }

        var modal = $(this)
        modal.find('.modal-content form').attr("action", "/delete/" + sezione + "/" + id);
        modal.find('.modal-content .modal-title span.sezione').text(sezione);
        modal.find('.modal-body span.sezione').text(sezioneBody);
        modal.find('.modal-body .nomeElemento').text(nomeElemento);
    })


    //GESTIONE MODAL RICHIESTA AUDIO: passaggio dei parametri
    $('#modalRichiesta').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var id = button.data('id');
        var nomeElemento = button.data('nome');
        console.log(id);


        var modal = $(this)
        modal.find('.modal-content form').attr("action", "/richiesta/audio/"+ id);
        modal.find('.modal-body .nomeElemento').text(nomeElemento);
    })



});
