import svLocale from './sv';
import enLocale from '@fullcalendar/core/locales/en-gb';
import apiFetch from '@wordpress/api-fetch';
import {addQueryArgs} from '@wordpress/url';
import {__} from "@wordpress/i18n";

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

export const submitBooking = (startTime, endTime, bookableId, slotId) => {
    return function (event) {
        event.preventDefault();
        refetchSlot(bookableId, slotId).then((res) => {
            const form = event.target;
            const email = form.querySelector('input[name="email"]').value;
            const firstName = form.querySelector('input[name="first_name"]').value;
            const lastName = form.querySelector('input[name="last_name"]').value;
            if (res.open_sessions.length === 0) {
                form.querySelector('.myclub-button').disabled = true;
                form.querySelector('.myclub-button').backgroundColor = '#ccc';
                form.querySelector('.myclub-button').innerHTML = __('Fully booked', 'myclub-booking');
                form.querySelector('input[name="email"]').disabled = true;
                form.querySelector('input[name="first_name"]').disabled = true;
                form.querySelector('input[name="last_name"]').disabled = true;
            } else {
                apiFetch({
                    path: `/myclub/v1/bookables/${bookableId}/slots/${slotId}/book`,
                    method: 'POST',
                    data: {
                        first_name: firstName,
                        last_name: lastName,
                        email: email,
                        start_time: startTime,
                        end_time: endTime
                    },
                }).then((res) => {
                    console.log(form, res);
                    form.querySelector('.myclub-button').disabled = true;
                    form.querySelector('.myclub-button').innerHTML = __('Booking successful', 'myclub-booking');
                });
            }
        });
    }
}

export const refetchSlot = (bookableId, slotId) => {
    return apiFetch({path: `/myclub/v1/bookables/${bookableId}/slots/${slotId}`});
}

export const showDialog = (item, modal, calendarRef) => {
    const {startTime, endTime, bookableId, bookableName, slotId} = item.extendedProps;
    const content = modal?.querySelector('.modal-body');
    const close = modal?.querySelector('.close');

    const title = __("Book ", 'myclub-booking') + `${bookableName} (${startTime} - ${endTime})`;

    let output = `<div class="name" id="booking-form">${title}</div>`;
    output += '<form class="myclub-form">';
    output += `<div class="myclub-input-wrapper"><label>${__("Email address", 'myclub-booking')}</label><input class="myclub-input" name="email" type="email" required></input></div>`;
    output += `<div class="myclub-input-wrapper"><label>${__("First name", 'myclub-booking')}</label><input class="myclub-input" name="first_name" type="text" required></input></div>`;
    output += `<div class="myclub-input-wrapper"><label>${__("Last name", 'myclub-booking')}</label><input class="myclub-input" name="last_name" type="text" required></input></div>`;
    output += `<div class="myclub-button-wrapper"><button type="submit" class="myclub-button">${__("Book session", 'myclub-booking')}</div>`;
    output += '</form>';

    content.innerHTML = output;
    content.querySelector('form').addEventListener('submit', submitBooking(startTime, endTime, bookableId, slotId));

    modal.classList.add('modal-open');
    const closeModal = () => {
        calendarRef.refetchEvents();
        modal.classList.remove('modal-open');
        close?.removeEventListener('click', closeModal);
    };
    close?.addEventListener('click', closeModal);

    refetchSlot(bookableId, slotId).then((res) => {
        if (res.open_sessions.length === 0) {
            const form = content.querySelector('form');
            form.querySelector('.myclub-button').disabled = true;
            form.querySelector('.myclub-button').backgroundColor = '#ccc';
            form.querySelector('.myclub-button').innerHTML = __('Fully booked', 'myclub-booking');
            form.querySelector('input[name="email"]').disabled = true;
            form.querySelector('input[name="first_name"]').disabled = true;
            form.querySelector('input[name="last_name"]').disabled = true;
        }
    })
}