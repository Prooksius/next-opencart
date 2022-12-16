(function($) {

    let comment_sent = 'Комментарий добавлен и будет опубликован после прохождения модерации';
    let captcha_fail = 'Проверка каптчи не пройдена';

    $(document).ready(function(){

        $(document).delegate('.reply-comment-link', 'click', function() {
            let reply_form = $('#reply-form');
            let add_form = $('#add-comment-form');
            let reply_form_cont = $('.reply-form-cont');
            let comment_id = $(this).closest('.blog-comment').attr('data-id');
            reply_form.find('.parent-id-input').val(comment_id);
            let comment_reply_cont = $(this).closest('.blog-reply-comment');
            reply_form.slideUp(400, function(){
                reply_form.appendTo(comment_reply_cont);
                reply_form.slideDown();
            });
            $('.reply-comment-link').removeClass('hidden');
            $(this).addClass('hidden');
            add_form.slideUp();
        });
        $(document).delegate('.cancel-reply-btn', 'click', function() {
            let reply_form = $('#reply-form');
            let add_form = $('#add-comment-form');
            let reply_form_cont = $('.reply-form-cont');
            reply_form.find('.parent-id-input').val('');
            $(this).closest('.blog-reply-comment').find('.reply-comment-link').removeClass('hidden');
            reply_form.slideUp(400, function(){
                reply_form.appendTo(reply_form_cont);
            });
            add_form.slideDown();
        });
        $(document).delegate('.submit-comment', 'click', function() {
            var form = $(this).closest('form');
            form.yiiActiveForm('validate', true);
        });

        $(document).delegate('.blog-comment-form form', 'beforeSubmit', function () {
            let $yiiform = $(this);
            let comment_reply_cont = $(this).closest('.blog-reply-comment');
            let reply_form = $('#reply-form');
            let add_form = $('#add-comment-form');
            let reply_form_cont = $('.reply-form-cont');

            grecaptcha.execute('6LezU60UAAAAABoDznk_-QRnVkf7IqCzZNPBbj77', {action: 'add_comment'})
                .then(function(token) {
                    let csrfParam = $('meta[name="csrf-param"]').attr("content");
                    let csrfToken = $('meta[name="csrf-token"]').attr("content");

                    let post_data = {
                        'action': 'add_comment',
                        'token': token,
                        'csrfParam': csrfToken,
                    };

                    $.ajax({
                        url: '/blog/default/verifycaptcha',
                        type: 'POST',
                        dataType: 'json',
                        data: post_data,
                        success: function(data){
                            if(data) {
                                $.ajax({
                                    type: $yiiform.attr('method'),
                                    url: '/blog/default/updatecomment?id='+$yiiform.attr('data-post-id'),
                                    data: $yiiform.serializeArray(),
                                })
                                .done(function(data) {
                                    if(data.success) {
                                        // data is saved
                                        reply_form.find('.parent-id-input').val('');
                                        reply_form.slideUp(400, function(){
                                            reply_form.appendTo(reply_form_cont);
                                        });
                                        add_form.slideDown();
                                        if (comment_reply_cont.length) {
                                            comment_reply_cont.append('<p style="color: green;font-weight: bold;">'+comment_sent+'</p>');
                                        } else {
                                            add_form.find('form').hide();
                                            add_form.append('<p style="color: green;font-weight: bold;">'+comment_sent+'</p>');
                                        }
                                    } else if (data.validation) {
                                        // server validation failed
                                        $yiiform.yiiActiveForm('updateMessages', data.validation, true); // renders validation messages at appropriate places
                                    } else {
                                        // incorrect server response
                                    }
                                })
                                .fail(function () {
                                    // request failed
                                });
                            } else {
                                reply_form.find('.parent-id-input').val('');
                                reply_form.appendTo(reply_form_cont);
                                add_form.removeClass('hidden');
                                if (comment_reply_cont.length) {
                                    comment_reply_cont.append('<p style="color: red;font-weight: bold;">'+captcha_fail+'</p>');
                                } else {
                                    add_form.find('form').hide();
                                    add_form.append('<p style="color: red;font-weight: bold;">'+captcha_fail+'</p>');
                                }
                            }
                        },
                        error: function(xhr, ajaxOptions, thrownError) {
                            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        }
                    });
                });

            return false; // prevent default form submission
        });

    });

})( jQuery );

