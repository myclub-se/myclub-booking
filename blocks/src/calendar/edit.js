import {useEffect, useRef, useState, useMemo} from '@wordpress/element';
import {InspectorControls, useBlockProps} from '@wordpress/block-editor';
import {PanelBody, PanelRow, SelectControl} from '@wordpress/components';
import './editor.scss';
import {__} from "@wordpress/i18n";

import {getMyClubBookables} from "../shared/edit-functions";
import FullCalendar from "@fullcalendar/react";
import {getCalendarLocale, getFullCalendarOptions, setupEvents, showDialog} from "../shared/calendar-functions";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";
import listPlugin from "@fullcalendar/list";

const labels = {
	calendar: __('Calendar', 'myclub-booking'),
	name: __('Name', 'myclub-booking'),
	when: __('When', 'myclub-booking'),
	location: __('Location', 'myclub-booking'),
	weekText: __('W', 'myclub-booking'),
	weekTextLong: __('Week', 'myclub-booking'),
};

export default function Edit( { attributes, setAttributes } ) {
	const [calendarTitle, setCalendarTitle] = useState('');
	const [postEvents, setPostEvents] = useState({events: [], loaded: false});
	const [posts, setPosts] = useState([]);
	const {apiFetch} = wp;
	const {useSelect} = wp.data;
	let calendarRef = useRef();
	let outerRef = useRef();
	let modalRef = useRef();
	const currentLocale = useSelect((select) => {
		if (select("core").getSite()) {
			return select('core').getSite().language;
		}

		return 'sv_SE';
	});
	const startOfWeek = useSelect((select) => {
		if (select("core").getSite()) {
			const startOfWeek = select('core').getSite().start_of_week;
			if (calendarRef && calendarRef.current) {
				const api = calendarRef.current.getApi();
				api.setOption('firstDay', startOfWeek);
			}
			return startOfWeek;
		}

		return 1;
	});
	const selectPostLabel = {
		label: __( 'Select a bookable item', 'myclub-booking' ),
		value: ''
	};
	const handleShowEvent = (arg) => {
		const item = arg.event;
		const modal = modalRef?.current;

		if (modal) {
			showDialog(item, modal, labels);
		}
	};
	const resetPostEvents = (loaded = false) => {
		setPostEvents({
			events: [],
			loaded,
		});
	};

	const options = useMemo(() => getFullCalendarOptions({
		labels,
		events: postEvents.events || [], // Provide events
		startOfWeek,
		locale: getCalendarLocale(currentLocale),
		smallScreen: window.innerWidth < 960,
		plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
		showEvent: (arg) => handleShowEvent(arg)
	}), [postEvents.events, startOfWeek, currentLocale]);

	const fetchEvents = async (bookable_id) => {
		try {
			const slots = await apiFetch({ path: `/myclub/v1/bookables/${bookable_id}/slots` });
			const allSlots = slots.results;
			const events = setupEvents(allSlots);

			setPostEvents({
				events,
				loaded: true, // Mark as successfully loaded
			});
		} catch (error) {
			// Handle errors and reset state
			throw new Error(error.message);
		}
	};

	useEffect(() => {
		apiFetch( { path: '/myclub/v1/options' } ).then(options => {
			setCalendarTitle ( options.myclub_booking_calendar_title );
		} );

		getMyClubBookables( setPosts, selectPostLabel );
	}, []);

	useEffect(() => {
		// Ensure the calendar reference exists before attempting to update events
		if (calendarRef && calendarRef.current) {
			const api = calendarRef.current.getApi();

			// Only update the calendar if there are new events and they are loaded
			if (postEvents.loaded) {
				api.removeAllEvents(); // Clear previous events
				api.addEventSource(postEvents.events); // Add the new event source
			}
		}
	}, [postEvents]);

	useEffect(() => {
		// Reset the postEvents state whenever the post_id changes
		resetPostEvents();

		// Fetch events if a valid post_id is provided
		if (attributes.bookable_id) {
			fetchEvents(attributes.bookable_id).catch(error => {
				console.error('Error fetching events:', error); // Log fetch errors
				setPostEvents({
					events: [],
					loaded: true, // Mark as loaded to avoid infinite effect calls
				});
			});
		} else {
			resetPostEvents(true);
		}
		// Depend on post_id so it executes correctly when attributes.post_id changes
	}, [attributes.post_id]);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Content settings', 'myclub-booking' ) }>
					<PanelRow>
						<SelectControl
							label={ __('Bookable item id', 'myclub-booking') }
							value={ attributes.bookable_id }
							options={ posts }
							onChange={ ( value ) => {
								setAttributes({bookable_id: value});
							} }
						/>
					</PanelRow>
				</PanelBody>
			</InspectorControls>
			<div {...useBlockProps()}>
				<div className="myclub-booking-calendar" ref={ outerRef }>
					<div class="myclub-booking-calendar-container">
						<h3 class="myclub-booking-header">{ calendarTitle }</h3>
						<FullCalendar ref={ calendarRef } { ...options } />
					</div>
					<div className="calendar-modal" ref={ modalRef }>
						<div className="modal-content">
							<span className="close">&times;</span>
							<div className="modal-body">
							</div>
						</div>
					</div>
				</div>
			</div>
		</>
	);
}
