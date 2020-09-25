$(document).ready(function () {
    const $skillCard = $('#booking_skillCard');
    const $session = $('#booking_session');

    $skillCard.change(function () {
        const $form = $skillCard.closest('form');
        let data = {};
        data[$skillCard.attr('name')] = $skillCard.val();
        console.log($form.prop('action'));
        $.ajax({
            url: $form.prop('action'),
            type: $form.prop('method'),
            data: data,
            success: (html) => {
                replaceFormField('#booking_module', html);
                replaceFormField('#booking_session', html);

                $('#booking_session').bind('change', function() {
                    alert('hello');
                })
            }
        })
    });
});

function replaceFormField(selector, html) {
    $(selector).replaceWith(
        $(html).find(selector)
    );
}