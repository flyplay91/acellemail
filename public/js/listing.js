function listCheckAllPages(form) {
    form.find('input[type=checkbox]').prop("checked", true);
    form.find('.check_all_list').removeClass('check-some');
    form.find('.list_actions').removeClass('hide');
    $.uniform.update();
}

function listUncheckAllPages(form) {
    form.find('input[type=checkbox]').prop("checked", false);
    form.find('.check_all_list').removeClass('check-some');
    form.find('.list_actions').addClass('hide');
    $.uniform.update();
}

// Generate unique id
function guid() {
  function s4() {
    return Math.floor((1 + Math.random()) * 0x10000)
      .toString(16)
      .substring(1);
  }
  return s4() + s4() + '-' + s4() + '-' + s4() + '-' +
    s4() + '-' + s4() + s4() + s4();
}

function hiliter(word, element) {
    var rgxp = new RegExp(word, 'gi');
    var string = element.html().match(rgxp);
    var repl = '<hl>' + string + '</hl>';
    element.html(element.html().replace(rgxp, repl));
}

function hiliterList(form, keyword) {
    if (keyword.length > 1) {
        form.find(".kq_search").each(function() {
            hiliter(keyword, $(this));
        });
    }
}

function tableRefreshAll() {
    $(".listing-form").each(function() {
        tableFilter($(this), $(this).attr("current-url"));
    });
}

function tableFilterAll() {
    $(".listing-form").each(function() {
        tableFilter($(this));
    });
}

function tableFilter(form, custom_url) {
    var url = form.attr("data-url");
    var id = form.attr('data-id');
    var per_page = form.attr("per-page")
    var sort_order = form.find("select[name='sort-order']").val();
    var container = form.find(".pml-table-container");

    // Remove sort direction when sort order == custom_order
    if(sort_order == 'custom_order') {
        form.find(".sort-direction").attr("rel", "asc");
        form.find(".sort-direction").find("i").attr("class", "icon-sort-amount-asc");
        form.find(".sort-direction").hide();
    } else {
        form.find(".sort-direction").show();
    }

    var sort_direction = form.find(".sort-direction").attr("rel");
    var keyword = form.find("input[name='search_keyword'], input[name='keyword']").val();

    // Default page
    if(typeof(custom_url) !== 'undefined') {
        url = custom_url;
    }

    // showed columns
    var columns = form.find("input[name='columns[]']:checked").map(function () {
        return this.value;
    }).get().join(",");

    // all data
    var d = {};
    form.serializeArray().forEach(function(entry) {
        if(entry.value!="") {
            d[entry.name] = entry.value
        }
    });

    container.addClass('loading');
    container.prepend('<div class="list-loading-spinner"><div class="spinner2 black">'+
      '<div class="bounce1"></div>'+
      '<div class="bounce2"></div>'+
      '<div class="bounce3"></div>'+
    '</div></div>');

    // ajax update custom sort
    // ajax update custom sort
	if(datalists[id] && datalists[id].readyState != 4){
		datalists[id].abort();
	}
    datalists[id] = $.ajax({
        method: "GET",
        url: url,
        data: {
            per_page: per_page,
            sort_order: sort_order,
            sort_direction: sort_direction,
            keyword: keyword,
            columns: columns,
            filters: d
        }
    })
    .done(function( msg ) {
        container.html(msg);

        // Uniform
        container.find(".styled").uniform({
            radioClass: 'choice'
        });

        // Default tooltip
        container.find('[data-popup=tooltip]').tooltip({
            template: '<div class="tooltip"><div class="bg-teal-800"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div></div>'
        });

        // Pagination class
        // ------------------------------
        form.find(".pagination").addClass('pagination-separated');

        // Select2
        // ------------------------------
        form.find(".select").select2({minimumResultsForSearch: -1});

        // Hightlight
        if (typeof(keyword) != 'undefined' && keyword.trim() != '') {
            keywords = keyword.split(" ");
            keywords.forEach(function (v) {
                hiliterList(form, v.trim());
            });
        }

        // update checklist
        updateCheckList();

        // Save current url for listing form
        form.attr("current-url", url);

        container.removeClass('loading');
    });
}

function listCheckAll(form) {
    form.find('table input[type=checkbox]').prop("checked", true);
    form.find('.check_all_list').removeClass('check-some');
    form.find('.list_actions').removeClass('hide');
    // form.find('.select_tool').val('whole_page').trigger('change.select2');
    $.uniform.update();
}

function listUncheckAll(form) {
    form.find('table input[type=checkbox]').prop("checked", false);
    form.find('.check_all_list').removeClass('check-some');
    form.find('.list_actions').addClass('hide');
    form.find('.select_tool').val('').trigger('change.select2');
    $.uniform.update();
}

function updateCheckList() {
    $(".listing-form").each(function() {
        var form = $(this);
        var total = form.find("input[name='ids[]']").length;
        var vals = form.find("input[name='ids[]']:checked").map(function () {
                    return this.value;
                }).get();
        var check_all_pages = form.find('.check_all_pages');
        var select_tool = form.find('.select_tool');
        var checked_count = vals.length;

        // Check if none checked
        if (checked_count == 0) {
            form.find('.check_all_list input.check_all').prop("checked", false);
            form.find('.check_all_list').removeClass('check-some');
            form.find('.list_actions').removeClass('check-some');
            form.find('.list_actions').addClass('hide');
            form.find('input.check_all_pages').prop("checked", false);
            form.find('.select_tool').val('').trigger('change.select2');
            $.uniform.update();
        }

        // Check if some checked
        else if (total > checked_count) {
            form.find('.check_all_list input.check_all').prop("checked", true);
            form.find('.check_all_list').addClass('check-some');
            form.find('.list_actions').removeClass('hide');
            form.find('input.check_all_pages').prop("checked", false);
            form.find('.select_tool').val('').trigger('change.select2');
            $.uniform.update();
        }

        // check if all checked
        else if (total == checked_count) {
            form.find('.check_all_list input.check_all').prop("checked", true);
            form.find('.check_all_list').removeClass('check-some');
            form.find('.list_actions').removeClass('hide');
            if (select_tool.val() !== 'all_items') {
                form.find('.select_tool').val('whole_page').trigger('change.select2');
            }
            listCheckAll(form);
        }

        // If check all pages
        if (check_all_pages.is(":checked") || select_tool.val() == 'all_items') {
            checked_count = form.find('input[name=total_items_count]').val();
        }

        // update count span
        if (checked_count > 0) {
          if (form.find('.list_actions button .checked_count').length) {
            form.find('.list_actions button .checked_count').html(checked_count);
          } else {
            form.find('.list_actions button .caret').before(' <span class="badge bg-grey checked_count">'+checked_count+'</span>');
          }
        } else {
          form.find('.list_actions button .checked_count').remove();
        }

    });
}

function updateCustomOrder(form) {
    var per_page = parseInt(form.attr("per-page"));
    var page = parseInt(form.find('table').attr("current-page"));
    var sort = [];
    var sort_url = form.attr("sort-url");
    form.find("input.node").each(function(index) {
        var num = index + ((page-1)*per_page);
        var row = [];
        row.push($(this).val());
        row.push(num);
        sort.push(row);
        // if row has custom order input
        if ($(this).parents('tr').find('input.custom_order').length) {
            $(this).parents('tr').find('input.custom_order').val(num);
        }
    });
    console.log(JSON.stringify(sort));

    // ajax update custom sort
    if (typeof(sort_url) != 'undefined') {
        $.ajax({
            method: "GET",
            url: sort_url,
            data: { sort: JSON.stringify(sort) }
        })
        .done(function( msg ) {
            // Success alert
            swal({
                title: msg,
                text: "",
                confirmButtonColor: "#00695C",
                type: "success",
                allowOutsideClick: true,
                confirmButtonText: LANG_OK,
            });
        });
    }
}

var drag_child;
var datalists = {};
$(document).ready(function() {

    $(".listing-form").each(function() {
        var form = $(this);
        var id = form.attr('data-id');

        if(typeof(id) === 'undefined') {
            form.attr('data-id', guid());
        }
        if (form.find('.table-boxes').length) {
            // sortable box
            // Make list sortable
            form.sortable({
                connectWith: '.boxes-plans',
                items: '.plan-box-list',
                helper: 'original',
                cursor: 'move',
                handle: '.panel-heading',
                revert: 100,
                containment: '.boxes-plans',
                forceHelperSize: true,
                placeholder: 'sortable-placeholder',
                forcePlaceholderSize: true,
                tolerance: 'pointer'
            });
        } else {
          // Make list sortable
          form.sortable({
              connectWith: '.row-sortable',
              items: 'tr',
              helper: 'original',
              cursor: 'move',
              handle: '[data-action=move]',
              revert: 100,
              containment: 'tbody',
              forceHelperSize: true,
              placeholder: 'sortable-placeholder',
              forcePlaceholderSize: true,
              tolerance: 'pointer',
              start: function(e, ui) {
                  drag_child = ui.item.next().next();
                  $("tr.child").hide();
                  ui.placeholder.height(ui.item.outerHeight());
              },
              update: function(e, ui) {
                  $("tr.child").each(function() {
                      var rel = $(this).attr("parent");
                      $(this).insertAfter($("tr[rel='"+rel+"']"));
                  });
                  $("tr.child").fadeIn();
                  updateCustomOrder(form);
              },
              stop: function(event, ui) {
                  $("tr.child").fadeIn();
              }
          });
        }

        // Render table
        tableFilter(form);
    });

    // Update checkbox list
    $(document).on("change", "input[name='ids[]']", function() {
        var form = $(this).parents(".listing-form");

        $(".check_all_pages").prop("checked", false);

        updateCheckList();
    });

    // Check / Uncheck all
    $(document).on("mouseup", ".check_all_list", function() {
        var checked = $(this).find('input.check_all').is(':checked');
        var form = $(this).parents(".listing-form");

        if (checked) {
            listUncheckAll($(this).parents(".listing-form"));
        } else {
            listCheckAll($(this).parents(".listing-form"));
        }

        setTimeout(function() {
            $(".check_all_pages").prop("checked", false);
            updateCheckList();
        }, 200);
    });

    // Change page
    $(document).on("click", ".listing-form .pagination a", function(e) {
        e.preventDefault();

        tableFilter($(this).parents(".listing-form"), $(this).attr("href"));
    });

    // Change item per page
    $(document).on("change", ".num_per_page select", function(e) {
        var form = $(this).parents(".listing-form");
        var value = $(this).val();

        form.attr("per-page", value);

        tableFilter(form);
    });

    // Sort direction button
    $(document).on("click", ".sort-direction", function(e) {
        var val = $(this).attr("rel");
        var form = $(this).parents(".listing-form");

        if (val == "asc") {
            $(this).attr("rel", "desc");
            $(this).find("i").attr("class", "icon-sort-amount-desc");
        } else {
            $(this).attr("rel", "asc");
            $(this).find("i").attr("class", "icon-sort-amount-asc");
        }

        tableFilter(form);
    });

    // Sort button
    $(document).on("change", ".listing-form select:not(.select_tool)", function() {
        var form = $(this).parents(".listing-form");

        tableFilter(form);
    });

    // Search when typing
    $(document).on("keyup", "input[name='search_keyword'], input[name='keyword']", function() {
        var form = $(this).parents(".listing-form");

        tableFilter(form);
    });

    // Columns filters
    $(document).on("click", ".list_columns ul li", function(e) {
        e.stopImmediatePropagation();
    });

    // Columns filters
    $(document).on("change", ".listing-form input[name='columns[]'], .listing-form .filter-box input[type=checkbox]:not(.check_all):not(.check_all_pages)", function(e) {
        var form = $(this).parents(".listing-form");

        tableFilter(form);
    });

    // Check / Uncheck all pages
    $(document).on("change", ".check_all_pages", function() {
        var checked = $(this).is(':checked');

        if (!checked) {
            listUncheckAllPages($(this).parents(".listing-form"));
        } else {
            listCheckAllPages($(this).parents(".listing-form"));
        }

        setTimeout("updateCheckList()", 200);
    });

    // Check / Uncheck all pages
    $(document).on("change", ".select_tool", function() {
        var val = $(this).val();
        var form = $(this).parents(".listing-form");

        if (val === '') {
            listUncheckAll(form);
        } else {
            listCheckAll(form);
        }

        setTimeout("updateCheckList()", 200);
    });
});
