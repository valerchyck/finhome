$(function () {
    $('textarea').autosize();

    $('.remove-user').on('click', function () {
        if (!confirm('Удалить запись?')) {
            return false;
        }
    });
});

function showAccessModal(userId) {
    $.ajax({
        type: 'get',
        url: '/user/access',
        data: {id: userId},
        success: function (response) {
            $('#user-objects-modal .modal-body').html(response);
            $('#user-objects-modal').modal('show');
        }
    });
}

function saveAccess(userId) {
    var data = [];
    $('[name="lists"]:checked').each(function () {
        data.push($(this).prop('id'));
    });

    $.ajax({
        type: 'post',
        url:  'user/save-access?id=' + userId,
        data: {
            data:   data,
            isEdit: ~~$('[name="isEdit"]').is(':checked')
        },
        success: function (response) {
            if (response == true) {
                $('#user-objects-modal').modal('hide');
            }
        }
    });
}
