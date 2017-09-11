    var current_content;
    var current_block;
    var current_select_block;
    var tmp_content = '';

    function updateConentDesign() {
        if (typeof(current_select_block) != 'undefined') {
            current_select_block.css("background-color", $('#background-color .colorpicker_new_color').css("background-color"));
            current_select_block.find("td").css("color", $('#text-color .colorpicker_new_color').css("background-color"));

            $('#design input').each(function() {
                var name = $(this).attr("name");
                var val = $(this).val();
                if(typeof(name) != 'undefined') {
                    current_select_block.css(name, val);
                }
            });
        }
    }

    function rgb2hex(orig){
        var rgb = orig.replace(/\s/g,'').match(/^rgba?\((\d+),(\d+),(\d+)/i);
        return (rgb && rgb.length === 4) ? "#" +
        ("0" + parseInt(rgb[1],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2],10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3],10).toString(16)).slice(-2) : orig;
    }

    function updateTextarea() {
        var css = '';
        $('textarea.template_content').val(css+'<content>' + $('content').html() + '</content>');
    }

    function insertElement(name) {
        $("content table.outer").append($('.'+name).clone());
        $('content .'+name).removeClass(name);

        updateContent();
    }

    function updateConentCol() {
        if (typeof(current_content) != 'undefined') {
            content = tinyMCE.activeEditor.getContent({format : 'html'});
            if (tmp_content != content) {
                current_content.html(content);
                tmp_content = content;
            }
        }
    }

    function ajustLayout() {
        $( "content" ).height( $( window ).height() - 55 );
        $( "editor" ).height( $( window ).height() - 75 );
        $( "leftbar" ).height( $( window ).height() - 55 );
    }

    function updateToolbox() {
        $('.toolbox-container').html($('toolbox').html());
        // Sortable
        sortable();
    }

    function updateContent() {
        $('content .tool-item').each(function() {
            var template = $(this).attr('template');
            $(this).after($('.'+template).clone());
            $('content .'+template).removeClass(template);
            $(this).remove();
        });

        // Find content col
        $('content td').each(function() {
            if(!$(this).find('table').length) {
                $(this).addClass('content-col');
            }
        });

        // add action button
        $("tr.block").on("mouseover", function () {
            $(".remove_block").show();
            $(".remove_block").css('top', $(this).offset().top);
            $(".remove_block").css('left', $(this).offset().left + $(this).width() - $(".remove_block").width());

            current_block = $(this);
        });
        $(".remove_block").on("mouseover", function () {
            $(".remove_block").show();
        });
        // add action button
        $("tr.block").on("mouseout", function () {
            $(".remove_block").hide();
        });
        // remove block
        $(".remove_block").on("click", function () {
            current_block.remove();
            $(".remove_block").hide();
            // Hide editor
            $('editor').fadeOut();
        });
    }

    function sortable() {
        $( ".left-box" ).sortable({
            items: '.block',
            connectWith: ".right-box",
            start: function(e, ui) {
                console.log(ui.item);
            },
            stop: function(event,ui){
                // Update content
                updateContent();
                // Update toolbox
                updateToolbox();
            }
        });
        // Content sortable
        $( ".right-box" ).sortable({
            items: '.block',
        });
    }

    $( function() {
        ajustLayout();

        // Update content
        updateContent();
        // Update toolbox
        updateToolbox();
        // Sortable
        sortable();

        // Click on content col
        $(document).on("click", ".content-col", function() {
            current_content = $(this);

            $(".content-col").removeClass('current');
            current_content.addClass('current');

            // Set content
            tinyMCE.activeEditor.setContent(current_content.html(), {format : 'html'});

            // Show editor
            $('editor').fadeIn();
        });

        // Click on block
        $(document).on("click", "tr.block", function() {
            current_select_block = $(this);

            $("tr.block").removeClass('current');
            current_block.addClass('current');

            // Set content
            $('#background-color').ColorPickerSetColor(rgb2hex(current_select_block.css("background-color")));
            $('#text-color').ColorPickerSetColor(rgb2hex(current_select_block.find("td").css("color")));

            $('#design input').each(function() {
                var name = $(this).attr("name");
                if(typeof(name) != 'undefined') {
                    $(this).val(current_select_block.css(name));
                }
            });

            // Show editor
            $('editor').fadeIn();
        });

        // Click on content col
        $(document).on("click", ".close-editor", function() {
            // Hide editor
            $('editor').fadeOut();
        });

        // Auto update content
        setInterval("updateConentCol()", 500);
        setInterval("updateConentDesign()", 500);
        setInterval("updateTextarea()", 500);

        // Click to insert tag
        $(document).on("click", ".insert_tag_button", function(e) {
            var tag = $(this).attr("data-tag-name");

            tinymce.activeEditor.execCommand('mceInsertContent', false, tag);
        });

        // Tab change
        $(document).on("click", ".tab ul li", function(e) {
            $(".tab ul li").removeClass("active");
            $(this).addClass("active");

            //
            var tab = $($(this).find("a").attr("href"));
            $(".tab .tab-pane").removeClass("active");
            tab.addClass("active");
        });

        // background
        $('.color').ColorPicker({flat: true});

        // Prevent click on bulder content
        $(document).on('click', '.builder-page content a', function(e) {
            e.preventDefault();
        });

    });
    $( window ).resize(function() {
        ajustLayout()
    });
