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

    $("#myclub-settings-form").on("click", ".myclub-dismiss", function() {
        $(this).parent().hide();
    });

    $('.sort-item-setter').on('change', function() {
        setSortableItems();
    });

    setSortableItems();
});