/* ------------------------------------------------------------------------------
*
*  # Styled checkboxes, radios and file input
*
*  Specific JS code additions for form_checkboxes_radios.html page
*
*  Version: 1.0
*  Latest update: Aug 1, 2015
*
* ---------------------------------------------------------------------------- */

function removeMaskLoading() {
    $('.mask-loading-effect').remove();
}

function addMaskLoading(text) {
    if (typeof(text) === 'undefined') {
        var text = '';
    }
    $('body').append('<div class="mask-loading-effect"><div class="content">'+text+'<div class="spinner2">'+
        '<div class="bounce1"></div>'+
        '<div class="bounce2"></div>'+
        '<div class="bounce3"></div>'+
        '</div></div><div>');
}

function showAjaxDetailBox(items) {
    items.each(function() {
        var container = $(this);
        var form_class = container.attr('data-form');
        var form = $(form_class);
        var url = container.attr('data-url');
        var method = form.attr('method');
        var hook_class = container.attr('hook');
        var loading_message = container.attr('loading-message');

        if (typeof(hook_class) === 'undefined') {
            hook_class = 'hook';
        }

        if(typeof(method) === 'undefined') {
            method = 'POST';
        }

        $(document).on('change', form_class + ' .' + hook_class, function() {
            data = form.serialize();

            if (typeof(loading_message) !== 'undefined') {
                container.html(loading_message);
            }

            $.ajax({
                method: 'GET',
                url: url,
                data: data
            })
            .done(function(msg) {
                container.html(msg);
                container.find('.pickadate').pickadate({format: 'yyyy-mm-dd', selectYears: 100});
                updatePickadateDateMask(container.find('.pickadate'));

                // Select2
                container.find('select').select2({
                    minimumResultsForSearch: 101,
                    templateResult: formatSelect2TextOption,
                    templateSelection: formatSelect2TextSelected
                });

                // showAjaxDetailBox(items)
                showAjaxDetailBox(container.find('.ajax-detail-box'));
            });
        });
    });
}

function formatSelect2TextSelected(d) {
    var text = d.text;
    var parts = text.split('|||');

    return parts[0];
}

function formatSelect2TextOption(d) {
    var text = d.text;
    var parts = text.split('|||');

    if (parts.length == 1) {
        return parts[0];
    } else {
        return '<div class="select2_title">' + parts[0] + '</div>' + '<div class="select2_sub_line">' + parts[1] + '</div>';
    }
}

function startProgress(box) {
    var url = box.attr('data-url');

    if (typeof(url) == 'undefined') {
        return;
    }

    $.ajax({
        method: "GET",
        url: url
    })
    .done(function( data ) {
        if (data != 'done') {
            box.html(data);
            setTimeout (function() {
                startProgress(box);
            }, 1000);
        } else {
            location.reload();
        }
    });
}

function swalError(msg) {
    swal({
        title: msg,
        text: "",
        confirmButtonColor: "#00695C",
        type: "error",
        allowOutsideClick: true,
        confirmButtonText: LANG_OK,
        customClass: "swl-success"
    });
}


function pickadateMask(selector) {
    $(document).on('change', selector, function() {
        updatePickadateDateMask($(this));
    });
    $(selector).each(function() {
        updatePickadateDateMask($(this));
    });
    $(document).on('focusout', selector, function() {
        $(this).val($(this).parent().find('.date-mask-control').html());
    });
}

function updatePickadateDateMask(control) {
    control.each(function() {
        var mask = $(this).parent().find('.date-mask-control');
        var value = $(this).val();

        if(value !== '') {
            var date = moment(value); //Get the current date
            mask.html(date.format(LANG_DATE_FORMAT.toUpperCase()));
        }
    });
}

function updateAutoEventNumber() {
    var num = 1;
    $('.auto-event-line').each(function() {
        if(!$(this).hasClass('event-inactive')) {
            $(this).find('.timeline-icon-i').html(num);
            num = num + 1;
        } else {
             $(this).find('.timeline-icon-i').html("#");
        }
    });
}

function loadAutomationEmail(container) {
    var url = container.attr('data-url');

    $.ajax({
        method: "GET",
        url: url
    })
    .done(function( data ) {
        container.html(data);
    });
}

function loadAutomationEmails() {
    $('.event-campaigns-container').each(function() {
        loadAutomationEmail($(this));
    });
}

function updateEmbeddedForm(form, url) {
    var data = {};
    form.serializeArray().forEach(function(entry) {
        if(entry.value!=="") {
            data[entry.name] = entry.value;
        }
    });

    $.ajax({
        method: "GET",
        url: url,
        data: data
    })
    .done(function( msg ) {
        // $(".embedded-form-result").html($("<div>").html(msg).find(".embedded-form-result"));
        var html = $("<div>").html(msg).find(".embedded-form-result").html();
        $(".embedded-form-result").html(html);

        // Hightlight code
        Prism.highlightAll();
    });
}

function dashboardQuickview(item, box) {
    var id = item.val();
    var url = box.attr("data-url");

    $.ajax({
        method: "GET",
        url: url,
        data: {
            uid: id
        }
    })
    .done(function( msg ) {
        box.html(msg);
        // Setup chart
        $('.chart').each(function() {
            updateChart($(this));
        });
    });
}

// Update checking backend / frontend access
function updateCheckAccess() {
    if($('#backend_access').is(":checked")) {
        $('.backend-box').removeClass('hide');
    } else {
        $('.backend-box').addClass('hide');
        $('li.frontend-box a').trigger('click');
    }

    if($('#frontend_access').is(":checked")) {
        $('.frontend-box').removeClass('hide');
    } else {
        $('.frontend-box').addClass('hide');
        $('li.backend-box a').trigger('click');
    }

    if(!$('#backend_access').is(":checked") && !$('#frontend_access').is(":checked")) {
        $('.options-container').hide();
    } else {
        $('.options-container').show();
    }
}

// Preview upload image
function readURL(input, img) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            img.attr('src', e.target.result);

            // calculate crop part
            var box_width = img.parent().width();
            var box_height = img.parent().height();
            var width = img[0].naturalWidth;
            var height = img[0].naturalHeight;
            var cal_width, cal_height;

            if(width/height < box_width/box_height) {
                cal_height = box_height;
                cal_width = box_height*(height/width);
            } else {
                cal_width = box_width;
                cal_height = box_width*(width/height);
            }

            img.width(cal_height);
            img.height(cal_width);

            var mleft = -Math.abs(cal_width - box_width)/2;
            var mtop = -Math.abs(cal_height - box_height)/2;
            img.css("margin-left", mtop+"px");
            img.css("margin-top", mleft+"px");
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function popupwindow(url, title, w, h) {
  var left = (screen.width/2)-(w/2);
  var top = 0;
  return window.open(url, title, 'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width='+w+', height='+screen.height+', top='+top+', left='+left);
}

$(function() {
    // Default tooltip
    $('[data-popup=tooltip]').tooltip({
		template: '<div class="tooltip"><div class="bg-teal-800"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div></div>'
	});

    // Basic select2
    // ------------------------------

    // Default initialization
    $('.select').select2({
        minimumResultsForSearch: 101,
        templateResult: formatSelect2TextOption,
        templateSelection: formatSelect2TextSelected
    });

    // Select with search
    $('.select-search').select2({
        minimumResultsForSearch: 101,
        templateResult: formatSelect2TextOption,
        templateSelection: formatSelect2TextSelected
    });


    // Checkboxes/radios (Uniform)
    // ------------------------------

    // Default initialization
    $(".styled, .multiselect-container input").uniform({
        radioClass: 'choice'
    });


    // Form help text
    // ------------------------------

    $(".form-control").focus( function() {
        $(this).parents(".form-group").find(".help").addClass("showed");
    });

    $(".form-control").blur( function() {
        $(this).parents(".form-group").find(".help").removeClass("showed");
    });

    // Preview upload image
    $(".previewable").change(function() {
        var img = $("img[preview-for='" + $(this).attr("name") + "']");
        readURL(this, img);
    });
    $(".remove-profile-image").click(function() {
        var img = $(this).parents(".profile-image").find("img");
        var imput = $(this).parents(".profile-image").find("input[name='_remove_image']");
        img.attr("src", img.attr("empty-src"));
        imput.val("true");
    });

    //
    // Switchery
    // ------------------------------

    // Initialize multiple switches
    $('.switchery').each(function() {
        if($(this).attr("data-switchery") != "true") {
            var switchery = new Switchery(this, {color: $(".navbar-inverse").css("background-color")});
        }
    });

    // Bootstrap switch
    // ------------------------------
    $(".switch").bootstrapSwitch();


    // Action list event
    // ------------------------------
    $(document).on("click", ".list_actions a", function(e) {
        var form = $(this).parents(".listing-form");
        var vals = form.find("input[name='ids[]']:checked").map(function () {
            return this.value;
        }).get();

        var new_href = $(this).attr("href") + "?uids=" + vals.join(",");

        if (form.find('.select_tool').length && form.find('.select_tool').val() === 'all_items') {
            new_href += '&'+form.serialize();
            new_href += '&select_tool='+form.find('.select_tool').val();
        }

        $(this).attr("new-href", new_href);
        $(this).attr("items-count", form.find('.checked_count').html());
    });


    // Confirm event
    // ------------------------------
    $(document).on("click", "a[delete-confirm]", function(e) {
        var mgs = $(this).attr("delete-confirm");
        var method = $(this).attr("data-method");

        if (typeof(method) === 'undefined' || method.trim() === '') {
            method = 'GET';
        }

        if (typeof($(this).attr('new-href')) === 'undefined' || $(this).attr('new-href') === '') {
            $(this).attr("new-href", $(this).attr("href"));
        }
        // count items
        var count = 1;
        if (typeof($(this).attr("items-count")) != 'undefined') {
            count = $(this).attr("items-count");
        }

        $('#delete_confirm_model').modal("show");
        $("#delete_confirm_model h6").html(mgs.replace(":number", "<span class='text-bold text-danger'>" + count + "</span>"));
        $(".delete-confirm-button").attr("href", $(this).attr("new-href"));
        $(".delete-confirm-button").attr("data-method", method);

        if(typeof($(this).attr("no-ajax")) != "undefined") {
            $(".delete-confirm-button").removeClass("ajax_link");
        } else if ($(this).hasClass('link-method')) {
            $(".delete-confirm-button").removeClass("ajax_link");
            $(".delete-confirm-button").attr("link-method", method);
        } else {
            if(!$(".delete-confirm-button").hasClass("ajax_link")) {
                $(".delete-confirm-button").addClass("ajax_link");
            }
        }

        //if($(this).parents(".list_actions").length) {
        //    var url = $(this).attr("new-href");
        //    var form = $(this).parents(".listing-form");
        //    var vals = form.find("input[name='ids[]']:checked").map(function () {
        //        return this.value;
        //    }).get();
        //
        //    url = url + "?uids=" + vals.join(",");
        //     $(".delete-confirm-button").attr("href", url);
        //}

        e.stopImmediatePropagation();
        e.preventDefault();
    });
    $(document).on("click", ".delete-confirm-button", function(e) {
        if($('.confirm-delete-form').valid()) {

        } else {
            e.stopImmediatePropagation();
            e.preventDefault();
        }
    });
    // List delete confirm event
    // ------------------------------
    $(document).on("click", "a[list-delete-confirm]", function(e) {
        var url = $(this).attr("href");
        var curl = $(this).attr("list-delete-confirm");
        var method = $(this).attr("data-method");
        var a_ = $(this);

        if (typeof(method) === 'undefined' || method.trim() === '') {
            method = 'GET';
        }

        if (typeof($(this).attr('new-href')) === 'undefined' || $(this).attr('new-href') === '') {
            $(this).attr("new-href", $(this).attr("href"));
        }

        if($(this).parents(".list_actions").length) {
            var form = $(this).parents(".listing-form");
            var vals = form.find("input[name='ids[]']:checked").map(function () {
                return this.value;
            }).get();

            url = url + "?uids=" + vals.join(",");
            curl = curl + "?uids=" + vals.join(",");
        }

        $('#list_delete_confirm_model').modal("show");
        $(".list-delete-confirm-button").attr("href", url);
        $(".list-delete-confirm-button").attr("data-method", method);

        // reload when need
        if (a_.hasClass('reload_page')) {
            $(".list-delete-confirm-button").addClass("reload_page");
        } else {
            $(".list-delete-confirm-button").removeClass("reload_page");
        }

        // Get message
        // ajax update custom sort
        $.ajax({
            method: "GET",
            url: curl,
        })
        .done(function( msg ) {
            $("#list_delete_confirm_model .content").html(msg);
        });

        e.stopImmediatePropagation();
        e.preventDefault();
    });
    $(document).on("click", ".list-delete-confirm-button", function(e) {
        if($('.list-confirm-delete-form').valid()) {

        } else {
            e.stopImmediatePropagation();
            e.preventDefault();
        }
    });
    // Link confirm
    $(document).on("click", "a[link-confirm]", function(e) {
        var mgs = $(this).attr("link-confirm");
        var url = $(this).attr("href");
        var method = $(this).attr("data-method");

        if (typeof(method) === 'undefined' || method.trim() === '') {
            method = 'GET';
        }

        if (typeof($(this).attr('new-href')) === 'undefined' || $(this).attr('new-href') === '') {
            $(this).attr("new-href", $(this).attr("href"));
        }

        if($(this).parents(".list_actions").length) {
            var form = $(this).parents(".listing-form");
            var vals = form.find("input[name='ids[]']:checked").map(function () {
                return this.value;
            }).get();

            var sign = (url.indexOf('?') !== -1 ? '&' : '?');
            url = url + sign + "uids=" + vals.join(",");

            // Select tool
            if (form.find('.select_tool').length && form.find('.select_tool').val() == 'all_items') {
                var select_tool = form.find('.select_tool').val();
                url = url + "&select_tool=" + select_tool;
                url = url + "&" + form.serialize();
            }
        }

        // count items
        var count = 1;
        if (typeof($(this).attr("items-count")) != 'undefined') {
            count = $(this).attr("items-count");
        }

        $('#link_confirm_model').modal("show");

        mgs = mgs.replace(":number", "<span class='text-bold text-teal-800'>" + count + "</span>");
        mgs = mgs.replace(":name", "<span class='text-bold text-teal-800'>" + $(this).html() + "</span>");

        $("#link_confirm_model h6").html(mgs);
        $(".link-confirm-button").attr("href", url);
        $(".link-confirm-button").attr("data-method", method);

        // set method
        if(typeof(method) != 'undefined') {
            $(".link-confirm-button").attr("data-method", method);
        } else {
            $(".link-confirm-button").removeAttr("data-method");
        }

        // Link confirm
        if (typeof($(this).attr('link-method')) !== 'undefined') {
            $(".link-confirm-button").attr("link-method", $(this).attr('link-method'));
            $(".link-confirm-button").removeClass("ajax_link");
        }

        e.stopImmediatePropagation();
        e.preventDefault();
    });

    // List fields
    // ------------------------------
    // Change item per page
    $(document).on("click", ".add-custom-field-button", function(e) {
        var type_name = $(this).attr("type_name");
        var sample = $("."+type_name+"_sample ");
        var sample_url = $(this).attr("sample-url");

        // ajax update custom sort
        $.ajax({
            method: "GET",
            url: sample_url,
            data: {
                type: type_name,
            }
        })
        .done(function( msg ) {
            var index = $('.field-list tr').length;

            msg = msg.replace(/__index__/g, index);
            msg = msg.replace(/__type__/g, type_name);

            $('.field-list').append($('<div>').html(msg).find("table tbody").html());
            $('.field-list tr').last().find('.pickadate').pickadate({format: 'yyyy-mm-dd', selectYears: 100});

            // Time picker
            if ($('.field-list tr').last().find(".pickadatetime").length) {
                $('.field-list tr').last().find(".pickadatetime").AnyTime_picker({
                    format: LANG_ANY_DATETIME_FORMAT
                });
            }

            //
            // Switchery
            // ------------------------------

            // Initialize multiple switches
            $('.switchery').each(function() {
                if($(this).attr("data-switchery") != "true") {
                    var switchery = new Switchery(this, {color: $(".navbar-inverse").css("background-color")});
                }
            });
        });
    });
    $(document).on("click", ".remove-not-saved-field", function(e) {
        $('tr[parent="'+$(this).parents('tr').attr('rel')+'"]').remove();
        $(this).parents('tr').remove();
    });
    $(document).on("click", ".add_label_value_group", function(e) {
        var last_item = $(this).parents("tr").find(".label-value-groups .label-value-group").last();
        var pre = last_item.attr("rel");
        var num = parseInt(pre)+1;
        var clone = $('<div>').append(last_item.clone()).html();

        clone = clone.replace('rel="'+pre+'"', 'rel="'+num+'"');
        clone = clone.replace('[options]['+pre+'][', '[options]['+num+'][');
        clone = clone.replace('[options]['+pre+'][', '[options]['+num+'][');
        clone = clone.replace('[options]['+pre+'][', '[options]['+num+'][');
        clone = clone.replace('[options]['+pre+'][', '[options]['+num+'][');
        clone = clone.replace('[options]['+pre+'][', '[options]['+num+'][');
        clone = clone.replace('[options]['+pre+'][', '[options]['+num+'][');
        clone = clone.replace('[options]['+pre+'][', '[options]['+num+'][');

        $(this).parents("tr").find(".label-value-groups").append(clone);
        $(this).parents("tr").find(".label-value-groups .label-value-group").last().find("input").val("");
        $(this).parents("tr").find(".label-value-groups .label-value-group").last().find(".help-block").remove();
        $(this).parents("tr").find(".label-value-groups .label-value-group").last().find(".form-group").removeClass("has-error");
    });

    // Validate confirm modal
    jQuery.validator.addMethod("deleteConfirm", function(value, element) {
        return value.toLowerCase() == "delete";
    }, LANG_DELETE_VALIDATE);
    $(".confirm-delete-form").each(function() {
        $(this).validate({
            rules: {
                delete: { deleteConfirm: true }
            }
        });
    });

    // Basic options
    $('.pickadate').pickadate({format: 'yyyy-mm-dd', selectYears: 100});

    // Numberic input
    $(".numeric").numeric();

    // add segment condition
    $(document).on("click", ".add-segment-condition", function(e) {
        // ajax update custom sort
        $.ajax({
            method: "GET",
            url: $(this).attr('sample-url'),
        })
        .done(function( msg ) {
            var num = "0";

            if($('.segment-conditions-container .condition-line').length) {
                num = parseInt($('.segment-conditions-container .condition-line').last().attr("rel"))+1;
            }

            msg = msg.replace(/__index__/g, num);

            $('.segment-conditions-container').append(msg);

            var new_line = $('.segment-conditions-container .condition-line').last();
            new_line.find('select').select2({
                templateResult: formatSelect2TextOption,
                templateSelection: formatSelect2TextSelected
            });
            new_line.find('select').trigger('change');
        });
    });

    // add segment condition
    $(document).on("change", ".condition-line .operator-col select", function(e) {
        var op = $(this).val();

        if(op == 'blank' || op == 'not_blank') {
            $(this).parents(".condition-line").find('.value-col').css("visibility", "hidden");
        } else {
            $(this).parents(".condition-line").find('.value-col').css("visibility", "visible");
        }
    });

    // add segment condition
    $(document).on("click", "a.ajax_link", function(e) {
        e.preventDefault();

        var url = $(this).attr("href");
        var a_ = $(this);
        var form = $(this).parents(".listing-form");
        var method = $(this).attr('data-method');

        // Skip if previous ajax calling
        if (a_.hasClass('loading')) {
            return;
        }

        // Default data
        var data = {
            "_token": CSRF_TOKEN
        };

        var in_form = $(this).attr('data-in-form');
        if (typeof(in_form) !== 'undefined' && in_form == 'true' && $(this).parents('form').length > 0) {
            data = $(this).parents('form').serialize();
            data += "&_method=" + method;
            if (!$(this).parents('form').valid()) {
                return;
            }
        }

        if(typeof(method) === 'undefined' || method.trim() === '') {
            method = "GET";
        }

        // Add loading effect
        var mask_text = a_.attr('mask-title');
        if(typeof(mask_text) === 'undefined') {
            mask_text = '';
        }
        a_.addClass('loading');
        a_.append(' <div class="spinner a-loading"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');
        addMaskLoading(mask_text);

        $.ajax({
            method: method,
            url: url,
            data: data
        })
        .done(function( msg ) {
            var type = 'success';
            if(msg.indexOf('is-error') !== -1) {
                type = 'error';
            }

            try
            {
               var data = JSON.parse(msg);
               type = data.status;
               msg = data.message;
            }
            catch(e)
            {
            }

            // Reload table if exists
            if ($(".listing-form").length > 0) {
                tableFilter($(".listing-form"));
            }

            if(msg !== '' && !a_.hasClass('reload_page')) {
                swal({
                    title: msg,
                    text: "",
                    confirmButtonColor: "#00695C",
                    type: type,
                    allowOutsideClick: true,
                    confirmButtonText: LANG_OK,
                    customClass: "swl-success",
                    html: true
                });
            }

            // Remove loading effect
            a_.removeClass('loading');
            a_.find('.a-loading').remove();
            removeMaskLoading();

            if (type != 'error' || a_.parents('form').find('input').length == 0) {
                $(".modal").modal('hide');
            }

            // reload when need
            if (a_.hasClass('reload_page')) {
                location.reload();
            }
        });
    });

    // Primary file input
	$(".file-styled-primary").uniform({
		wrapperClass: 'bg-warning',
		fileButtonHtml: '<i class="icon-plus2"></i>'
	});

    // Styled file input
    $(".file-styled").uniform({
        wrapperClass: 'bg-teal-400',
        fileButtonHtml: '<i class="icon-googleplus5"></i>'
    });

    // page preview action
    $(document).on("click", ".preview-page-button", function(e) {
        var url = $(this).attr('page-url');
        tinyMCE.triggerSave();
        var formData = new FormData($("#update-page")[0]);
        var frame = $('.preview_page_frame');
        var current_action = $("#update-page").attr("action");
        $("#update-page").attr('target', 'preview_page_frame');
        $("#update-page").attr('action', url);
        $("#update-page").submit();

        // after submit
        $("#update-page").removeAttr('target');
        $("#update-page").attr('action', current_action);

    });

    // Click to insert tag
    $(document).on("click", ".insert_tag_button", function() {
        var tag = $(this).attr("data-tag-name");

        if(!$(".plain_text_li").hasClass("active")) {
            tinymce.activeEditor.execCommand('mceInsertContent', false, tag);
        } else {
            $('textarea[name="plain"]').val($('textarea[name="plain"]').val()+tag);
        }
    });

    // Segments select box by list
    $(document).on("change", ".list_select_box select", function() {
        var url = $(this).parents('.list_select_box').attr("segments-url");
        var box = $(this).parents('.list-segment-container').find("."+$(this).parents('.list_select_box').attr("target-box"));
        var id = $(this).val();
        var index = $(this).parents('.condition-line').attr('rel');

        if(id !== '') {
            $.ajax({
                method: "GET",
                url: url,
                data: {
                    list_uid: id,
                    index: index
                }
            })
            .done(function( msg ) {
                box.html(msg);

                // Select with search
                $('.select-search').select2({
                    templateResult: formatSelect2TextOption,
                    templateSelection: formatSelect2TextSelected
                });
            });
        } else {
            box.html('');
        }
    });

    // tab error
    $('.form-group.has-error').each(function() {
        var id = $(this).parents('.tab-pane').attr("id");
        $('a[href="#'+id+'"]').addClass('error');
    });

    // choose template
    $(document).on("click", ".choose-template-button", function() {
        var url = $(this).attr("data-url");

        $.ajax({
            method: "GET",
            url: url,
        })
        .done(function( msg ) {
            tinymce.activeEditor.execCommand('mceSetContent', false, msg);
            $(".modal").modal('hide');
        });
    });

    // Time picker
    if ($(".pickatime").length) {
        $(".pickatime").AnyTime_picker({
            format: "%H:%i"
        });
    }

    // DateTime picker
    if ($(".pickadatetime").length) {
        $(".pickadatetime").AnyTime_picker({
            format: LANG_ANY_DATETIME_FORMAT
        });
    }

    // Backend / Frontend
    updateCheckAccess();
    $(document).on('change', '#frontend_access', function() {
        updateCheckAccess();
    });
    $(document).on('change', '#backend_access', function() {
        updateCheckAccess();
    });

    // Setup chart
    $('.chart').each(function() {
        updateChart($(this));
    });

    // Campaign quickview dashboard
    $(document).on('change', '.dashboard-campaign-select', function() {
        dashboardQuickview($(this), $('.campaign-quickview-container'));
    });
    $('.dashboard-campaign-select').trigger("change");

    // List quickview dashboard
    $(document).on('change', '.dashboard-list-select', function() {
        dashboardQuickview($(this), $('.list-quickview-container'));
    });
    $('.dashboard-list-select').trigger("change");

    // scrollbar
    $('.scrollbar-box').mCustomScrollbar({theme:"minimal"});

    // Select2 ajax
    $(".select2-ajax").each(function() {
        var url = $(this).attr("data-url");
        var placeholder = $(this).attr("placeholder");
        if(typeof(placeholder) == 'undefined') {
            placeholder = "";
        }
        $(this).select2({
            placeholder: placeholder,
            allowClear: true,
            ajax: {
                url: url,
                dataType: 'json',
                delay: 250,

                data: function (params) {
                  return {
                    q: params.term, // search term
                    page: params.page
                  };
                },
                processResults: function (data, params) {
                    // parse the results into the format expected by Select2
                    // since we are using custom formatting functions we do not need to
                    // alter the remote JSON data, except to indicate that infinite
                    // scrolling can be used
                    params.page = params.page || 1;

                    return {
                        results: data.items,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                },
                cache: true
            },
            escapeMarkup: function (markup) { return markup; }, // let our custom formatter work
            minimumInputLength: 0,
            templateResult: formatSelect2TextOption,
            templateSelection: formatSelect2TextSelected,
        });
    });

    // Campaign quickview dashboard
    $(document).on('click', '.top-quota-button', function() {
        var url = $(this).attr("data-url");
        $.ajax({
            method: "GET",
            url: url,
        })
        .done(function( msg ) {
            $('#quota_modal .modal-body').html(msg);
            $('#quota_modal').modal("show");
        });
    });

    // unlimited check
    $(document).on('change', '.unlimited-check input[type=checkbox]', function() {
        var box = $(this).parents(".boxing");
        var checked = $(this).is(":checked");

        box.find("input[type=text]").each(function() {
            var input = $(this);
            var d_val = $(this).attr('default-value');

            if (typeof(d_val) === 'undefined') {
                d_val = 0;
            }

            if(checked) {
                input.val(-1);
                input.addClass("text-trans");
                input.attr("readonly", "readonly");
            } else {
                if(input.val() == "-1") {
                    input.val(d_val);
                }
                input.removeClass("text-trans");
                input.removeAttr("readonly", "readonly");
            }
        });

    });
    $('.unlimited-check input').trigger("change");

    // sending quota unlimited check check
    $(document).on('change', '.sending_quota-check input[type=checkbox]', function() {
        var box = $(this).parents(".boxing");

        if($(this).is(":checked")) {
            box.find("input[type=text]").val("-1");
            box.find("input[type=text]").addClass("text-trans");
            box.find("input[type=text]").attr("readonly", "readonly");

            $('select[name="options[frontend][sending_quota_time_unit]"]').val('month').change();
            $('input[name="options[frontend][sending_quota_time]"]').val(1);
            $('input[name="options[frontend][sending_quota_time]"]').addClass("disabled");
            $('input[name="options[frontend][sending_quota_time]"]').attr("readonly", "readonly");
        } else {
            if(box.find("input[type=text]").val() == "-1") {
                box.find("input[type=text]").val(1000);
            }
            box.find("input[type=text]").removeClass("text-trans");
            box.find("input[type=text]").removeAttr("readonly", "readonly");

            $('input[name="options[frontend][sending_quota_time]"]').removeClass("disabled");
            $('input[name="options[frontend][sending_quota_time]"]').removeAttr("readonly", "readonly");
        }
    });
    $('.sending_quota-check input').trigger("change");

    // unlimited check
    $(document).on('click', 'ul.install-steps li:not(.enabled) a', function(e) {
        e.preventDefault();
    });

    // unlimited check
    $(document).on('click', '.copy-list-link', function(e) {
        var uid = $(this).attr("data-uid");
        var name = $(this).attr("data-name");

        $('.ajax_copy_list_form input[name=copy_list_uid]').val(uid);
        $('.ajax_copy_list_form input[name=copy_list_name]').val(name);

        $('#copy_list').modal("show");
    });

    // Ajax copy list
    $(".ajax_copy_list_form").submit(function(e) {
        var url = $(this).attr("action");

        $.ajax({
            type: "POST",
            url: url,
            data: $(".ajax_copy_list_form").serialize(), // serializes the form's elements.
            success: function(msg)
            {
                tableFilter($(".listing-form"));
                if(msg != '') {
                    swal({
                        title: msg,
                        text: "",
                        confirmButtonColor: "#00695C",
                        type: "success",
                        allowOutsideClick: true,
                        confirmButtonText: LANG_OK,
                        customClass: "swl-success",
                        html: true
                    });
                }
            }
        });

        $(".copy-list-close").trigger("click");
        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    // copy campaign
    $(document).on('click', '.copy-campaign-link', function(e) {
        var uid = $(this).attr("data-uid");
        var name = $(this).attr("data-name");

        $('input[name=copy_campaign_uid]').val(uid);
        $('input[name=copy_campaign_name]').val(name);
        $('#copy_campaign').modal("show");
    });

    // Ajax copy campaign
    $(".ajax_copy_campaign_form").submit(function(e) {
        var url = $(this).attr("action");

        $.ajax({
            type: "POST",
            url: url,
            data: $(".ajax_copy_campaign_form").serialize(), // serializes the form's elements.
            success: function(msg)
            {
                tableFilter($(".listing-form"));
                if(msg != '') {
                    swal({
                        title: msg,
                        text: "",
                        confirmButtonColor: "#00695C",
                        type: "success",
                        allowOutsideClick: true,
                        confirmButtonText: LANG_OK,
                        customClass: "swl-success",
                        html: true
                    });
                }
            }
        });

        $(".copy-campaign-close").trigger("click");
        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    // disble campaign step link
    $(document).on("click", ".campaign-steps li.disabled a", function(e) {
        e.preventDefault();
    });

    //embedded-options-form
    $(document).on("change", ".embedded-options-form input[type=text], .embedded-options-form textarea, .embedded-options-form input[name='required_fields'], .embedded-options-form input[name='show_invisible'], .embedded-options-form input[name='javascript'], .embedded-options-form input[name='stylesheet']", function() {
        var url = $(this).parents("form").attr("action");

        updateEmbeddedForm($(this).parents("form"), url);
    });

    //embedded-options-form
    $(document).on("keyup", ".embedded-options-form input[type=text]", function() {
        var url = $(this).parents("form").attr("action");

        updateEmbeddedForm($(this).parents("form"), url);
    });

    $(".embedded-options-form input").trigger("change");


    // send a test email
    $(document).on('click', '.send-a-test-email-link', function(e) {
        var uid = $(this).attr("data-uid");

        $('input[name=send_test_email_campaign_uid]').val(uid);
        $('#send_a_test_email').modal("show");

        e.preventDefault();
    });

    // Ajax send a test email
    $(".ajax_send_a_test_email_form").submit(function(e) {
        var url = $(this).attr("action");
        var form = $(".ajax_send_a_test_email_form");

        if(form.valid()) {
            form.addClass("loading");
            form.find("button[type='submit']").addClass("disabled");
            form.find("button[type='submit']").before('<i class="icon-spinner10 spinner position-left loading-icon"></i>');
            $.ajax({
                type: "POST",
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(data)
                {
                    data = JSON.parse(data);
                    swal({
                        title: '',
                        text: data.message,
                        confirmButtonColor: "#00695C",
                        type: data.status,
                        allowOutsideClick: true,
                        confirmButtonText: LANG_OK,
                        customClass: "swl-success",
                        html: true
                    });

                    form.addClass("loading");
                    form.find("button[type='submit']").removeClass("disabled");
                    form.find('.loading-icon').remove();
                    $(".copy-campaign-close").trigger("click");
                }
            });
        }

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    // Table select
    $(document).on('change', '.tabable-select-radio input[type="radio"]', function() {
        var box = $(this).parents('.tabable-select-box');
        var container = box.find('.tabable-container');
        var val = $(this).val();

        container.find('.tabable-tab').hide();
        box.find('.' + val).show();
    });
    $('.tabable-select-radio input[type="radio"]:checked').trigger('change');

    // addable multiple form
    $(document).on("click", ".addable-multiple-form .add-form", function(e) {
        var form = $(this).parents('.addable-multiple-form');
        var container = form.find('.addable-multiple-container');
        var status = $(this).attr('automation-status');

        if(status == 'active') {
            //show disable automation confirm
            $('#disable_automation_confirm').modal('show');
            return;
        }

        // ajax update custom sort
        $.ajax({
            method: "GET",
            url: $(this).attr('sample-url'),
        })
        .done(function( msg ) {
            var num = "0";

            if(container.find('.condition-line').length) {
                num = parseInt(container.find('.condition-line').last().attr("rel"))+1;
            }

            msg = msg.replace(/__index__/g, num);

            container.append(msg);

            var new_line = container.find('.condition-line').last();
            new_line.find('select').select2({
                templateResult: formatSelect2TextOption,
                templateSelection: formatSelect2TextSelected
            });
            new_line.find('.styled').uniform({
                radioClass: 'choice'
            });

            if(new_line.find('.event-campaigns-container').length) {
                loadAutomationEmail(new_line.find('.event-campaigns-container'));
            }
        });
    });

    // radio-box
    $(document).on("change", ".radio-box .radio-button", function(e) {
        $(".radio-box .radio-more").hide();
        $(".radio-box .radio-button:checked").parents(".radio-box").find(".radio-more").show();
    });

    // load automation email
    loadAutomationEmails();

    // Grap link with data-method attribute
    $(document).on('click', '.event-campaign-add', function(e) {
        e.preventDefault();

        var url = $(this).attr('data-url');
        var container = $(this).parents('.event-campaigns-box').find('.event-campaigns-container');

        $.ajax({
            method: "POST",
            url: url,
            data: {
                "_token": CSRF_TOKEN
            }
        })
        .done(function( data ) {
            loadAutomationEmail(container);
        });

    });

    // Grap link with data-method attribute
    $(document).on('click', '.auto-campaign-delete', function(e) {
        e.preventDefault();

        var confirm_msg = $(this).attr('data-confirm');

        $('#delete_auto_campaign_confirm_model h6').html(confirm_msg);
        $('#delete_auto_campaign_confirm_model').modal('show');

        $('.auto-campaign-delete-confirmed').attr('data-url', $(this).attr('data-url'));
    });
    // Grap link with data-method attribute
    $(document).on('click', '.auto-campaign-delete-confirmed', function(e) {
        e.preventDefault();

        var url = $(this).attr('data-url');

        $.ajax({
            method: "DELETE",
            url: url,
            data: {
                "_token": CSRF_TOKEN
            }
        })
        .done(function() {
            loadAutomationEmails();
            $('#delete_auto_campaign_confirm_model').modal('hide');
        });

    });

    // Grap link with data-method attribute
    $(document).on('click', '.auto-event-delete', function(e) {
        e.preventDefault();

        var confirm_msg = $(this).attr('data-confirm');

        $('#delete_auto_event_confirm_model h6').html(confirm_msg);
        $('#delete_auto_event_confirm_model').modal('show');

        $('.auto-event-delete-confirmed').attr('data-url', $(this).attr('data-url'));
        $('.auto-event-delete-confirmed').attr('data-id', $(this).attr('data-id'));
    });
    // Grap link with data-method attribute
    $(document).on('click', '.auto-event-delete-confirmed', function(e) {
        e.preventDefault();

        var url = $(this).attr('data-url');
        var box_id = $(this).attr('data-id');

        $.ajax({
            method: "DELETE",
            url: url,
            data: {
                "_token": CSRF_TOKEN
            }
        })
        .done(function() {
            $('[rel="'+box_id+'"]').remove();
            $('#delete_auto_event_confirm_model').modal('hide');
        });
    });

    // Auto event saving
    $(document).on('submit', '.auto-event-line form', function(e) {
        e.preventDefault();

        var form = $(this);
        var url = form.attr('action');
        var method = form.attr('method');
        var line = form.parents('.auto-event-line');

        $.ajax({
            method: method,
            url: url,
            data: form.serialize()
        })
        .done(function(msg) {
            form.parents('.auto-event-line').removeClass('changing');

            swal({
                title: msg,
                text: "",
                confirmButtonColor: "#00695C",
                type: "success",
                allowOutsideClick: true,
                confirmButtonText: LANG_OK,
                customClass: "swl-success"
            });
        });
    });

    // Auto event saving
    $(document).on('click', '.auto-event-line .btn-close', function(e) {
        e.preventDefault();

        var box = $(this).parents('.auto-event-line');

        if(box.hasClass('changing')) {
            // svae current value
            box.find('input, select').each(function() {
                $(this).val($(this).attr('old-value')).trigger("change");
                $(this).trigger("change");
            });

            box.removeClass('changing');
        }
    });

    // Auto event saving
    $(document).on('click', '.auto-event-line .before', function(e) {
        e.preventDefault();

        var box = $(this).parents('.auto-event-line');
        box.find('.btn-close').trigger('click');
    });

    // save current auto event values
    $(document).on('click', '.auto-event-line', function() {
        if(!$(this).hasClass('changing')) {
            // svae current value
            $(this).find('input, select').each(function() {
                $(this).attr('old-value', $(this).val());
            });
        }
    });

    // auto event change value
    $(document).on('change keyup', '.auto-event-line input, .auto-event-line select', function() {
        $(this).parents('.auto-event-line').addClass('changing');
    });

    // Grap link with data-method attribute
    $(document).on('click', 'a[link-method]', function(e) {
        e.preventDefault();

        var method = $(this).attr("link-method");
        var action = $(this).attr("href");

        if(typeof(method) != 'undefined') {
            var newForm = jQuery('<form>', {
                'action': action,
                'method': 'POST'
            });
            newForm.append(jQuery('<input>', {
                'name': '_token',
                'value': CSRF_TOKEN,
                'type': 'hidden'
            }));
            newForm.append(jQuery('<input>', {
                'name': '_method',
                'value': method,
                'type': 'hidden'
            }));
            $(document.body).append(newForm);
            newForm.submit();
        }
    });

    // Check all checkboxes group
    $(document).on('click', '.checkboxes_check_all', function() {
        var box = $(this).parents('.form-group');

        box.find('input[type=checkbox]').prop('checked', true);
        $.uniform.update();
    });
    // Uncheck all checkboxes group
    $(document).on('click', '.checkboxes_check_none', function() {
        var box = $(this).parents('.form-group');

        box.find('input[type=checkbox]').prop('checked', false);
        $.uniform.update();
    });

    // radio group check
    $(document).on('change', '[radio-group]', function() {
        var checked = $(this).is(':checked');
        var group = $(this).attr('radio-group');

        if(checked) {
            $('[radio-group="' + group + '"]').prop('checked', false);
            $(this).prop('checked', true);

            $.uniform.update();
        }
    });

    // pickadate mask
    pickadateMask('.pickadate-control');

    // Check all checkboxes group
    $(document).on('click', '.re_generate_remote_job_url', function(e) {
        e.preventDefault();

        $.ajax({
            method: 'POST',
            url: '',
            data: {
                re_generate_remote_job_url: true,
                _token: CSRF_TOKEN
            }
        })
        .done(function(msg) {
            $('.remote-job-url').html(msg);
        });
    });

    // ajax form review link
    showAjaxDetailBox($('.ajax-detail-box'));

    // Payment method button click
    $(document).on('click', '.payment_method_type_button', function() {
        var value = $(this).attr('data-value');

        $('input[name="payment_method_uid"]').val(value);
    });

    // load locale js
    $.getScript(JVALIDATE_TRANSLATE_URL, function(){
    });

    // Campaign quickview dashboard
    $(document).on('click', '.payments-button', function() {
        var url = $(this).attr("data-url");
        $.ajax({
            method: "GET",
            url: url,
        })
        .done(function( msg ) {
            $('#full_modal .modal-body').html(msg);
            // Default tooltip
            $('#full_modal .modal-body').find('[data-popup=tooltip]').tooltip({
                template: '<div class="tooltip"><div class="bg-teal-800"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div></div>'
            });
            $('#full_modal').modal("show");
        });
    });

    // Copy Move subscribers
    $(document).on('click', '.copy_move_subscriber', function() {
        var url = $(this).attr('data-url');
        var data = {};
        // Data list action
        if ($(this).parents('.list_actions').length) {
            var form = $(this).parents(".listing-form");
            var vals = form.find("input[name='ids[]']:checked").map(function () {
                return this.value;
            }).get();

            data = {
                uids: vals.join(",")
            };

            // select_tool
            var select_tool = '';
            if (form.find('.select_tool').length && form.find('.select_tool').val() == 'all_items') {
                select_tool = form.find('.select_tool').val();
                arr = form.serializeArray();
                for (var i = 0; i < arr.length; i++){
                  data[arr[i]['name']] = arr[i]['value'];
                }
            }

        }

        $.ajax({
            method: 'GET',
            url: url,
            data: data
        })
        .done(function(data) {
            $('#copy-move-subscribers-form').remove();
            $('body').append(data);
            $('#copy-move-subscribers-form').modal('show');
            $('#copy-move-subscribers-form').find('select').select2({
                minimumResultsForSearch: 101,
                templateResult: formatSelect2TextOption,
                templateSelection: formatSelect2TextSelected
            });
            $('#copy-move-subscribers-form').find('.styled').uniform({
                radioClass: 'choice'
            });
            customValidate($('#copy-move-subscribers-form').find('form'));
        });
    });

    // Ajax copy list
    $(document).on('submit', '#copy-move-subscribers-form form', function(e) {
        var form = $(this);
        var url = form.attr("action");

        addMaskLoading();

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(), // serializes the form's elements.
            success: function(msg)
            {
                tableFilter($(".listing-form"));
                if(msg != '') {
                    swal({
                        title: msg,
                        text: "",
                        confirmButtonColor: "#00695C",
                        type: "success",
                        allowOutsideClick: true,
                        confirmButtonText: LANG_OK,
                        customClass: "swl-success",
                        html: true
                    });
                }
                $('#copy-move-subscribers-form').modal('hide');

                removeMaskLoading();
            }
        });

        $(".copy-list-close").trigger("click");
        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    // Copy Move subscribers
    $(document).on('click', '.list-form-button', function() {
        var form = $($(this).attr('data-target')).find('form');
        var message = $(this).attr('message');
        var uids = $(this).attr('data-uids');
        var action = $(this).attr('data-url');
        var method = $(this).attr('data-method');
        var input = form.find('input[name=uids]');

        form.attr('action', action);
        form.attr('method', method);
        input.val(uids);

        message = message.replace(":number", "<span class='text-bold text-info-800'>1</span>");

        form.find('h4').html(message);
    });
    // Ajax copy list
    $(document).on('submit', '.list-form-modal form', function(e) {
        var form = $(this);
        var modal = form.parents('.list-form-modal');
        var url = form.attr("action");
        var method = form.attr("method");

        if (form.valid()) {
            $.ajax({
                type: method,
                url: url,
                data: form.serialize(), // serializes the form's elements.
                success: function(msg)
                {
                    tableFilter($(".listing-form"));
                    if(msg != '') {
                        swal({
                            title: msg,
                            text: "",
                            confirmButtonColor: "#00695C",
                            type: "success",
                            allowOutsideClick: true,
                            confirmButtonText: LANG_OK,
                            customClass: "swl-success",
                            html: true
                        });
                    }
                    modal.modal('hide');
                }
            });
        }

        e.preventDefault(); // avoid to execute the actual submit of the form.
    });

    // Dotted list more/less
    $(document).on('click', '.dotted-list > li.more a', function() {
        var box = $(this).parents('.dotted-list');

        box.find('li').removeClass('hide');
        $(this).parents('li').hide();
    });

    // Default list auto recheck if empty
    $(document).on('click', '.list-segment-container .btn', function() {
        if (!$('.list-segment-container input[type=radio]:checked').length) {
            $('.list-segment-container input[type=radio]').eq(0).click();
        }
    });

    // Close notify
    $(document).on('click', '.btn-close-pnotify', function() {
        $(this).parents('.ui-pnotify').remove();
    });

    // Plan time unit unlimited selection
    $(document).on('change', 'select[name=frequency_unit]', function() {
        var val = $(this).val();

        if (val == 'unlimited') {
            $('input[name=frequency_amount]').val(-1).addClass('text-trans').prop('readonly', true);
        } else {
            $('input[name=frequency_amount]').val(1).removeClass('text-trans').prop('readonly', false);
        }
    });
    $('select[name=frequency_unit]').trigger('change');

    // Segment condition field type select
    $(document).on('change', '.condition-field-select', function() {
        var line = $(this).parents('.condition-line');
        var field_uid = $(this).val();
        var value_col = line.find('.operator_value_col');
        var url = value_col.attr('data-url');
        var index = line.attr('rel');
        var operator = line.find('.operator-col select').val();

        value_col.html('');

        if (field_uid != '') {
            $.ajax({
                type: 'GET',
                url: url,
                data: {
                    field_uid: field_uid,
                    index: index,
                    operator: field_uid
                }, // serializes the form's elements.
                success: function(data)
                {
                    value_col.html(data);
                    value_col.find('select').select2();
                }
            });
        }
    });
    // $('.condition-field-select').trigger('change');

    $('.operator-col select').trigger('change');

    // progress bar ajax reload
    $('.progress-box').each(function() {
        startProgress($(this));
    });

    // add segment condition
    $(document).on("click", "a.modal_link", function(e) {
        e.preventDefault();
        $(".modal").modal('hide');
        var url = $(this).attr("href");
        var a_ = $(this);
        var method = $(this).attr('data-method');

        // Finding modal
        var modal = $('#ajax-modal[rel="'+url+'"]');
        if (!modal.length) {
            $('body').append('<div id="ajax-modal" rel="'+url+'" class="modal fade ajax-modal"><div class="modal-dialog modal-md"></div></div>');
            modal = $('#ajax-modal[rel="'+url+'"]');
        }

        // Skip if previous ajax calling
        if (a_.hasClass('loading')) {
            return;
        }

        // Default data
        var data = {
            "_token": CSRF_TOKEN
        };

        var in_form = $(this).attr('data-in-form');
        if (typeof(in_form) !== 'undefined' && in_form == 'true' && $(this).parents('form').length > 0) {
            data = $(this).parents('form').serialize();
            data += "&_method=" + method;
            if (!$(this).parents('form').valid()) {
                return;
            }
        }

        if(typeof(method) === 'undefined' || method.trim() === '') {
            method = "GET";
        }

        // Add loading effect
        a_.addClass('loading');
        a_.append(' <div class="spinner a-loading"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');

        $.ajax({
            method: method,
            url: url,
            data: data
        })
        .done(function( data ) {
            modal.find('.modal-dialog').html(data);
            modal.modal('show');

            // Remove loading effect
            a_.removeClass('loading');
            a_.find('.a-loading').remove();
        });
    });

    // add segment condition
    $(document).on("click", "button.click-effect", function(e) {
        var a_ = $(this);
        var form = a_.parents('form');

        if (!form.length || !form.hasClass('form-validate-jquery') || (form.hasClass('form-validate-jquery') && form.valid())) {
            // Add loading effect
            a_.addClass('loading');
            a_.append(' <div class="spinner a-loading"><div class="double-bounce1"></div><div class="double-bounce2"></div></div>');
        }
    });

    // Group radio box
    $(document).on("click", ".control-radio .radio_box .main-control", function() {
        var radio_control = $(this).parents('.control-radio');
        var radio_box = $(this).parents('.radio_box');
        var radio = $(this).find('input');

        radio_control.find('.radio_more_box').hide();
        if (radio.is(":checked")) {
            radio_box.find('.radio_more_box').show();
        } else {
            radio_box.find('.radio_more_box').hide();
        }
    });
    $(".control-radio .radio_box .main-control input:checked").parents('.main-control').trigger('click');


    // show with control helper
    $("[show-with-control]").each(function() {
        var box = $(this);
        var control = $(box.attr('show-with-control'));

        control.change(function() {
            var checked = $(this).is(':checked');

            if (checked) {
                box.show();
            } else {
                box.hide();
            }
        });

        control.trigger('change');
    });
});
