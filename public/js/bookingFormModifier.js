$(document).ready(function () {
    const $skillCard = $('#booking_skillCard');
    const $form = $('form[name = "booking"]');

    const sessionField = function () {
        const $session = $('#booking_session');

        $session.change(function () {
            const data = Object.assign(
                catchFieldValue($skillCard),
                catchFieldValue($session)
            );
            sendAjax($form, data, function (html) {
                replaceFormField('#booking_turn', html);
            });
        });
    };

    $skillCard.change(function () {
        sendAjax($form, catchFieldValue($skillCard), function (html) {
            replaceFormField('#booking_module', html);
            replaceFormField('#booking_session', html);
            $('#booking_turn').find('option').remove().end();
            sessionField();
        });
    });
});

function catchFieldValue($jObj) {
    let data = {};
    data[$jObj.attr('name')] = $jObj.val();
    return data;
}

function sendAjax($form, data, successCallBack) {
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