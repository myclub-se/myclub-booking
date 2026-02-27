import {getCalendarLocale, getFullCalendarOptions, loadEvents, toggleSlotSelection, updateSelectedSlotsPanel} from "../shared/calendar-functions";

document.addEventListener('DOMContentLoaded', () => {
    const smallScreen = document.documentElement.clientWidth < 960;
    const calendarEl = document.getElementById('calendar-div');
    const labels = JSON.parse(calendarEl.dataset.labels);
    const bookableId = calendarEl.dataset.bookableId;
    const firstDayOfWeek = calendarEl.dataset.firstDayOfWeek;
    const modal = document.getElementById("calendar-modal");
    const selectedSlots = [];

    const calendar = new FullCalendar.Calendar(calendarEl, getFullCalendarOptions({
        labels,
        events: loadEvents(bookableId),
        locale: getCalendarLocale(calendarEl.dataset.locale),
        firstDay: firstDayOfWeek,
        smallScreen,
        plugins: [],
        showEvent: (arg) => {
            toggleSlotSelection(arg.event, selectedSlots, calendar, modal);
        }
    }));

    calendar.render();

    updateSelectedSlotsPanel(selectedSlots, calendar, modal);
});