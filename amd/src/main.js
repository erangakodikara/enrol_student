define(['jquery', 'core/ajax', 'core/templates', 'core/notification'], function ($, ajax, templates, notification) {
    return {
        init: function () {
            $('#user_id').on('click', function () {
                var promises = ajax.call([{
                    methodname: 'block_enrol_student_enrol_student_data',
                    args: {}
                }]);
                promises[0].done(function (data) {

                    templates.render('block_enrol_student/email_list', data).done(function (html, js) {
                        $("#first_name_id").show();
                        $("#last_name_id").show();
                        templates.runTemplateJS(js);
                    }).fail(notification.exception);
                }).fail(notification.exception);
            });
        }
    };
});
