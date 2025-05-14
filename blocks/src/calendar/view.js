import {getCalendarLocale, getFullCalendarOptions, showDialog, loadEvents} from "../shared/calendar-functions";

document.addEventListener('DOMContentLoaded', () => {
    const smallScreen = document.documentElement.clientWidth < 960;
    const calendarEl = document.getElementById('calendar-div');
    const labels = JSON.parse(calendarEl.dataset.labels);
    const bookableId = calendarEl.dataset.bookableId;
    const firstDayOfWeek = calendarEl.dataset.firstDayOfWeek;

    const calendar = new FullCalendar.Calendar(calendarEl, getFullCalendarOptions({
        labels,
        events: loadEvents(bookableId),
        locale: getCalendarLocale(calendarEl.dataset.locale),
        firstDay: firstDayOfWeek,
        smallScreen,
        plugins: [],
        showEvent: (arg) => {
            const item = arg.event;
            const modal = document.getElementById("calendar-modal");

            if (modal) {
                showDialog(item, modal, labels);
            }
        }
    }));

    calendar.render();
});