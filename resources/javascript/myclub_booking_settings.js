jQuery(document).ready(function($) {
    function addNotice(text, type) {
        var $notice = $( '<div></div>' )
            .attr( 'role', 'alert' )
            .attr( 'tabindex', '-1' )
            .addClass( 'is-dismissible notice notice-' + type )
            .append( $( '<p></p>' ).html( '<strong>' + text + '</strong>' ) )
            .append(
                $( '<button></button>' )
                    .attr( 'type', 'button' )
                    .addClass( 'notice-dismiss myclub-dismiss' )
                    .append( $( '<span></span>' ).addClass( 'screen-reader-text' ).text( wp.i18n.__( 'Dismiss this notice.' ) ) )
            );

        $('.myclub-dismiss').parent().hide();
        $("#myclub-settings-form").find("h2").first().after($notice);
    }

    function setSortableItems() {
        const $sortList = $("#myclub_booking_show_items_order");
        const $items = $sortList.find("li input");
        const $selectedItems = $(".sort-item-setter");
        const sortedItems = $items.map((_, item) => $(item).val()).get();

        $selectedItems.each(function(_, item) {
            const $checkbox = $(item);
            const name = $checkbox.data('name');
            const displayName = $checkbox.data('display-name');

            if (!sortedItems.includes(name) && $checkbox.is(':checked')) {
                $sortList.append(`<li><input type="hidden" name="myclub_booking_show_items_order[]" value="${name}">${displayName}</li>`);
            }
        });

        sortedItems.forEach(value => {
            const $checkbox = $selectedItems.filter(`[data-name="${value}"]`);

            if ($checkbox.length === 0 || !$checkbox.is(':checked')) {
                $sortList.find('li input').filter((_, item) => $(item).val() === value).parent().remove();
            }
        });

        try {
            $sortList.sortable("refresh");
        } catch(e) {
            $sortList.sortable();
        }
    }

    $("#myclub-reload-bookables-button").on("click", function() {
        addNotice(wp.i18n.__('Reloading bookable items', 'myclub-booking'), 'success');
        $("#myclub_booking_last_bookable_sync").html(wp.i18n.__('The bookable items update task is currently running', 'myclub-booking'));

        $.ajax({
            url: ajaxurl,
            data: {
                "action": "myclub_reload_bookables"
            },
            success: function(returned_data) {
                addNotice(returned_data.data.message, returned_data.success ? 'success' : 'error');
            },
            error: function(errorThrown) {
                addNotice(wp.i18n.__('Unable to reload bookables', 'myclub-booking'), 'error');
                console.log(errorThrown);
            }
        });
    });

    $("#myclub-sync-slots-button").on("click", function() {
        addNotice(wp.i18n.__('Synchronizing slots', 'myclub-booking'), 'success');
        $("#myclub_booking_last_slots_sync").html(wp.i18n.__('Slot synchronization is currently running', 'myclub-booking'));

        $.ajax({
            url: ajaxurl,
            data: {
                "action": "myclub_sync_slots"
            },
            success: function(returned_data) {
                addNotice(returned_data.data.message, returned_data.success ? 'success' : 'error');
            },
            error: function(errorThrown) {
                addNotice(wp.i18n.__('Unable to synchronize slots', 'myclub-booking'), 'error');
                console.log(errorThrown);
            }
        })
    });

    $("#myclub-settings-form").on("click", ".myclub-dismiss", function() {
        $(this).parent().hide();
    });

    $('.sort-item-setter').on('change', function() {
        setSortableItems();
    });

    setSortableItems();
});