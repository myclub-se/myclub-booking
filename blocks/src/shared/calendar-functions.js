import svLocale from './sv';
import enLocale from '@fullcalendar/core/locales/en-gb';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

export const getCalendarLocale = (locale) => {
    return locale === 'sv_SE' ? svLocale : enLocale;
};

const subtractMinutes = (time, minutes) => {
    let parts = time.split(':');
    let date = new Date();
    date.setHours(parts[0]);
    date.setMinutes(parts[1]);
    date.setSeconds(parts[2]);

    date.setMinutes(date.getMinutes() - minutes);

    let hrs = ("0" + date.getHours()).slice(-2);
    let mins = ("0" + date.getMinutes()).slice(-2);
    let secs = ("0" + date.getSeconds()).slice(-2);

    return `${hrs}:${mins}:${secs}`;
}

export const getFullCalendarOptions = ({labels, events, locale, firstDay, smallScreen, plugins, showEvent}) => {
    const rightToolbar = smallScreen ? 'timeGridDay,listMonth' : 'dayGridMonth,timeGridWeek,listMonth';
    const initialView = smallScreen ? 'listMonth' : 'dayGridMonth';

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
                type: slot.type,
            },
        };
    });
}

export const showDialog = (item, modal, labels) => {
    const {type, startTime, endTime, location} = item.extendedProps;
    const content = modal?.querySelector('.modal-body');
    const close = modal?.querySelector('.close');

    let output = `<div class="name">${type}</div>`;
    output += '<table>';
    output += `<tr><th>${labels.name}</th><td>${item.title}</td></tr>`;
    output += `<tr><th>${labels.when}</th><td>${startTime.substring(0, 5)} - ${endTime.substring(0, 5)}</td></tr>`;
    output += `<tr><th>${labels.location}</th><td>${location}</td></tr>`;
    output += '</table>';

    content.innerHTML = output;

    modal.classList.add('modal-open');
    const closeModal = () => {
        modal.classList.remove('modal-open');
        close?.removeEventListener('click', closeModal);
    };
    close?.addEventListener('click', closeModal);
    modal.addEventListener('click', closeModal);
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