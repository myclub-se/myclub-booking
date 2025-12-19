import {useEffect, useRef, useState, useMemo} from '@wordpress/element';
import {InspectorControls, useBlockProps} from '@wordpress/block-editor';
import {PanelBody, PanelRow, SelectControl} from '@wordpress/components';
import './editor.scss';
import {__} from "@wordpress/i18n";

import {getMyClubBookables} from "../shared/edit-functions";
import FullCalendar from "@fullcalendar/react";
import {getCalendarLocale, getFullCalendarOptions, showDialog, loadEvents} from "../shared/calendar-functions";
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
			showDialog(item, modal, calendarRef.current.getApi());
		}
	};

	const options = useMemo(() => getFullCalendarOptions({
		labels,
		events: loadEvents(attributes.bookable_id), // Provide events
		startOfWeek,
		locale: getCalendarLocale(currentLocale),
		smallScreen: window.innerWidth < 960,
		plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
		showEvent: (arg) => handleShowEvent(arg)
	}), [startOfWeek, currentLocale]);

	useEffect(() => {
		apiFetch( { path: '/myclub/v1/options' } ).then(options => {
			setCalendarTitle ( options.myclub_booking_calendar_title );
		} );

		getMyClubBookables( setPosts, selectPostLabel );
	}, []);

	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Content settings', 'myclub-booking' ) }>
					<PanelRow>
						<SelectControl
							label={ __('Bookable item', 'myclub-booking') }
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
						<FullCalendar id='calendar-div' ref={ calendarRef } { ...options } />
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
