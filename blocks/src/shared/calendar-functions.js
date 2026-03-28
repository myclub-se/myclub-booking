import svLocale from './sv';
import enLocale from '@fullcalendar/core/locales/en-gb';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {__, _n, sprintf} from "@wordpress/i18n";

export const getCalendarLocale = (locale) => {
    return locale === 'sv_SE' ? svLocale : enLocale;
};

export const getFullCalendarOptions = ({labels, events, locale, firstDay, smallScreen, plugins, showEvent}) => {
    const rightToolbar = smallScreen ? 'timeGridDay,listMonth' : 'dayGridMonth,timeGridWeek,listMonth';
    const initialView = smallScreen ? 'timeGridDay' : 'timeGridWeek';

    return {
        allDaySlot: false,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: rightToolbar,
        },
        locale,
        events,
        firstDay,
        timeZone: 'Europe/Stockholm',
        weekNumbers: true,
        weekText: labels.weekText,
        weekTextLong: labels.weekTextLong,
        initialView,
        plugins,
        eventClick: (arg) => showEvent(arg),
        eventContent: (arg) => {
            const item = arg.event;
            const element = document.createElement('div');
            let timeText = arg.timeText;
            element.classList.add('fc-event-title');

            if (item.extendedProps.meetUpTime && item.extendedProps.meetUpTime !== item.extendedProps.startTime) {
                if (!timeText) {
                    timeText = item.extendedProps.startTime.substring(0, 5);
                }

                timeText += ` (${item.extendedProps.meetUpTime.substring(0, 5)})`;
            }

            element.innerHTML = '<div class="myclub-booking-event-time">' +
                timeText + '</div><div class="myclub-booking-event-title">' +
                item.title.replaceAll('u0022', '\"') +
                '</div>';

            let arrayOfDomNodes = [
                element
            ];

            return {domNodes: arrayOfDomNodes};
        },
    };
};

export const setupEvents = (slots) => {
    return slots.map((slot) => {
        let backgroundColor = '#395B9E';

        return {
            id: slot.id,
            title: slot.bookable_name,
            start: `${slot.day} ${slot.start_time}`,
            end: `${slot.day} ${slot.end_time}`,
            backgroundColor,
            borderColor: backgroundColor,
            color: '#fff',
            display: 'block',
            extendedProps: {
                base_type: slot.base_type,
                calendar_name: slot.calendar_name,
                location: slot.location,
                day: slot.day,
                endTime: slot.end_time,
                startTime: slot.start_time,
                bookableId: slot.bookable_id,
                bookableName: slot.bookable_name,
                slotId: slot.id,
            },
        };
    });
}

export const loadEvents = (bookableId) => {
    return (fetchInfo, successCallback, _) => {
        if (!bookableId) {
            return;
        }
        const queryParams = {
            start_date: fetchInfo.start.toISOString().slice(0, 10), end_date: fetchInfo.end.toISOString().slice(0, 10)
        };
        apiFetch({path: addQueryArgs(`/myclub/v1/bookables/${bookableId}/slots`, queryParams)}).then((slots) => {
            const allSlots = slots.results;
            successCallback(setupEvents(allSlots));
        });
    }
}

export const refetchSlot = (bookableId, slotId) => {
    return apiFetch({path: `/myclub/v1/bookables/${bookableId}/slots/${slotId}`});
}

const SELECTED_COLOR = '#2d7a2d';
const DEFAULT_COLOR = '#395B9E';

export const updateSelectedSlotsPanel = (selectedSlots, calendarRef, modal) => {
    const doc = calendarRef.el?.ownerDocument || document;
    const panel = doc.getElementById('selected-slots-panel');
    const list = doc.getElementById('selected-slots-list');
    const bookBtn = doc.getElementById('book-selected-slots-btn');
    if (!panel || !list || !bookBtn) return;

    if (selectedSlots.length === 0) {
        panel.classList.remove('is-visible');
        return;
    }

    panel.classList.add('is-visible');
    /* translators: %d: number of selected booking slots */
    bookBtn.textContent = sprintf(_n('Book %d slot', 'Book %d slots', selectedSlots.length, 'myclub-booking'), selectedSlots.length);

    list.innerHTML = '';
    selectedSlots.forEach((slot) => {
        const item = document.createElement('div');
        item.className = 'myclub-selected-slot-item';
        const [y, m, d] = slot.day.split('-');
        const dateStr = new Date(y, m - 1, d).toLocaleDateString(undefined, {month: 'short', day: 'numeric'});
        const label = document.createElement('span');
        label.textContent = `${slot.title} · ${dateStr}, ${slot.startTime.slice(0, 5)}–${slot.endTime.slice(0, 5)}`;
        const removeBtn = document.createElement('button');
        removeBtn.className = 'myclub-remove-slot';
        removeBtn.setAttribute('aria-label', __('Remove', 'myclub-booking'));
        removeBtn.textContent = '×';
        removeBtn.addEventListener('click', () => {
            const idx = selectedSlots.findIndex(s => s.slotId === slot.slotId);
            if (idx !== -1) {
                selectedSlots.splice(idx, 1);
                const fcEvent = calendarRef.getEventById(slot.slotId);
                if (fcEvent) {
                    fcEvent.setProp('backgroundColor', DEFAULT_COLOR);
                    fcEvent.setProp('borderColor', DEFAULT_COLOR);
                }
                updateSelectedSlotsPanel(selectedSlots, calendarRef, modal);
            }
        });
        item.appendChild(label);
        item.appendChild(removeBtn);
        list.appendChild(item);
    });

    bookBtn.onclick = () => showBulkBookingDialog(selectedSlots, calendarRef, modal);
};

export const toggleSlotSelection = (fcEvent, selectedSlots, calendarRef, modal) => {
    const {slotId, bookableId, startTime, endTime, day} = fcEvent.extendedProps;
    const idx = selectedSlots.findIndex(s => s.slotId === slotId);
    if (idx === -1) {
        selectedSlots.push({slotId, bookableId, startTime, endTime, day, title: fcEvent.title});
        fcEvent.setProp('backgroundColor', SELECTED_COLOR);
        fcEvent.setProp('borderColor', SELECTED_COLOR);
    } else {
        selectedSlots.splice(idx, 1);
        fcEvent.setProp('backgroundColor', DEFAULT_COLOR);
        fcEvent.setProp('borderColor', DEFAULT_COLOR);
    }
    updateSelectedSlotsPanel(selectedSlots, calendarRef, modal);
};

export const showBulkBookingDialog = (selectedSlots, calendarRef, modal) => {
    if (selectedSlots.length === 0) return;
    const content = modal?.querySelector('.modal-body');
    const close = modal?.querySelector('.close');

    const slotCount = selectedSlots.length;
    let output = `<div class="name" id="booking-form">${__('Book selected slots', 'myclub-booking')} (${slotCount})</div>`;
    output += '<form class="myclub-form">';
    output += `<div class="myclub-input-wrapper"><label for="mc-email">${__("Email address", 'myclub-booking')}</label><input id="mc-email" class="myclub-input" name="email" type="email" autocomplete="email" required></div>`;
    output += `<div class="myclub-input-wrapper"><label for="mc-first">${__("First name", 'myclub-booking')}</label><input id="mc-first" class="myclub-input" name="first_name" type="text" autocomplete="given-name"></div>`;
    output += `<div class="myclub-input-wrapper"><label for="mc-last">${__("Last name", 'myclub-booking')}</label><input id="mc-last" class="myclub-input" name="last_name" type="text" autocomplete="family-name"></div>`;
    output += `<div class="myclub-button-wrapper"><button type="submit" class="myclub-button">${__("Confirm booking", 'myclub-booking')}</button></div>`;
    output += `<div id="myclub-payment-link"></div>`;
    output += '</form>';

    content.innerHTML = output;
    modal.classList.add('modal-open');

    const closeModal = () => {
        modal.classList.remove('modal-open');
        close?.removeEventListener('click', closeModal);
    };
    close?.addEventListener('click', closeModal);

    content.querySelector('form').addEventListener('submit', (event) => {
        event.preventDefault();
        const form = event.target;
        const email = form.querySelector('input[name="email"]').value;
        const firstName = form.querySelector('input[name="first_name"]').value;
        const lastName = form.querySelector('input[name="last_name"]').value;

        const sessions = selectedSlots.map(slot => ({
            slot_id: slot.slotId,
            start_time: slot.startTime,
            end_time: slot.endTime,
            bookable_zones_taken: 1,
        }));

        apiFetch({
            path: '/myclub/v1/bookables/sessions/bulk',
            method: 'POST',
            data: {email, first_name: firstName, last_name: lastName, sessions},
        }).then((res) => {
            form.querySelector('.myclub-button').disabled = true;
            form.querySelector('.myclub-button').innerHTML = __('Booking successful', 'myclub-booking');
            selectedSlots.splice(0, selectedSlots.length);
            updateSelectedSlotsPanel(selectedSlots, calendarRef, modal);
            calendarRef.refetchEvents();
            if (res.payment_order_id) {
                const paymentLink = `https://app.myclub.se/public/payment-orders/${res.payment_order_id}/${res.token}?from=booking`;
                const paymentLinkDiv = document.createElement("div");
                paymentLinkDiv.innerHTML = __('Your payment link: ', 'myclub-booking') + `<a href="${paymentLink}">${paymentLink}</a>`;
                form.querySelector('#myclub-payment-link').appendChild(paymentLinkDiv);
                window.location.href = paymentLink;
            } else {
                setTimeout(() => closeModal(), 3000);
            }
        });
    });
};