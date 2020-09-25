$(document).ready(function () {
    const $skillCard = $('#booking_skillCard');
    const $session = $('#booking_session');
    const $form = $('form[name = "booking"]');

    $skillCard.change(function () {
        sendAjax($skillCard, $form, function (html) {
            replaceFormField('#booking_module', html);
            replaceFormField('#booking_session', html);

            $('#booking_session').bind('change', function () {
                sendAjax($session, $form, function (html) {
                    replaceFormField('#booking_turn', html);
                });
            });
        });
    });
});

function sendAjax($jObj, $form, successCallBack) {
    let data = {};
    data[$jObj.attr('name')] = $jObj.val();
    console.log($form);
    $.ajax({
        url: $form.prop('action'),
        type: $form.prop('method'),
        data: data,
        success: successCallBack
    })
}

function replaceFormField(selector, html) {
    $(selector).replaceWith(
        $(html).find(selector)
    );
}