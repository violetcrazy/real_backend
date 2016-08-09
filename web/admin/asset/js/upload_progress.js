window.addEventListener('message', function(event) {
    console.log(event.data);
    if(event.data.meta.callback != '') {
        window[event.data.meta.callback](event.data);
    }
}, false);

$(document).ready(function () {
    $('table').on('click', '.delete-row', function (event) {
        event.preventDefault();
        deleteRow($(this));
    });

}).on('click', '.add-gallery', function(event){
    event.preventDefault();
    console.log($(this).data());
    var callback = $(this).data('callback');
    var url = file_upload + '?callback=' + callback;
    $.fancybox({
        'width': '90%',
        'height': '90%',
        'autoScale': true,
        'transitionIn': 'fade',
        'transitionOut': 'fade',
        'type': 'iframe',
        'href': url
    });

});

function deleteRow(el) {
    el.closest('tr').fadeOut('fast', function () {
        $(this).remove();
    })
}