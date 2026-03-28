import {useEffect, useRef, useState, useMemo, useCallback} from '@wordpress/element';
import {InspectorControls, useBlockProps} from '@wordpress/block-editor';
import {PanelBody, PanelRow, SelectControl} from '@wordpress/components';
import './editor.scss';
import {__} from "@wordpress/i18n";

import {getMyClubBookables} from "../shared/edit-functions";
import {Calendar} from "@fullcalendar/core";
import {getCalendarLocale, getFullCalendarOptions, toggleSlotSelection, loadEvents} from "../shared/calendar-functions";
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

/**
 * Pre-inject a <style data-fullcalendar> element so that FullCalendar's
 * ensureElHasStyles() finds it via querySelector instead of trying to
 * insertBefore the DOCTYPE node in the block-editor iframe.
 */
function ensureStyleElement(el) {
	if (!el || !el.isConnected) return;

	const rootNode = el.getRootNode();
	if (!rootNode || rootNode.querySelector('style[data-fullcalendar]')) return;

	const styleEl = document.createElement('style');
	styleEl.setAttribute('data-fullcalendar', '');

	const head = rootNode === document
		? document.head
		: (rootNode.head || rootNode.querySelector('head'));

	if (head) {
		head.appendChild(styleEl);
	}
}

export default function Edit( { attributes, setAttributes } ) {
	const [calendarTitle, setCalendarTitle] = useState('');
	const [posts, setPosts] = useState([]);
	const {apiFetch} = wp;
	const {useSelect} = wp.data;
	let calendarRef = useRef(null);
	let calendarElRef = useRef();
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
			if (calendarRef.current) {
				calendarRef.current.setOption('firstDay', startOfWeek);
			}
			return startOfWeek;
		}

		return 1;
	});
	const selectPostLabel = {
		label: __( 'Select a bookable item', 'myclub-booking' ),
		value: ''
	};
	const editorSelectedSlots = [];
	const handleShowEvent = useCallback((arg) => {
		const modal = modalRef?.current;
		if (calendarRef.current) {
			toggleSlotSelection(arg.event, editorSelectedSlots, calendarRef.current, modal);
		}
	}, []);

	// Create/destroy the calendar instance
	useEffect(() => {
		const el = calendarElRef.current;
		if (!el) return;

		ensureStyleElement(el);

		const options = getFullCalendarOptions({
			labels,
			events: loadEvents(attributes.bookable_id),
			startOfWeek,
			locale: getCalendarLocale(currentLocale),
			smallScreen: window.innerWidth < 960,
			plugins: [dayGridPlugin, timeGridPlugin, listPlugin],
			showEvent: (arg) => handleShowEvent(arg)
		});

		const calendar = new Calendar(el, options);
		calendar.render();
		calendarRef.current = calendar;

		return () => {
			calendar.destroy();
			calendarRef.current = null;
		};
	}, [startOfWeek, currentLocale, attributes.bookable_id]);

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
					<div className="myclub-booking-calendar-container">
						<h3 className="myclub-booking-header">{ calendarTitle }</h3>
						<div id='calendar-div' ref={ calendarElRef } />
					</div>
					<div id="selected-slots-panel" className="myclub-selected-slots-panel">
						<span className="myclub-panel-label">{__('Selected', 'myclub-booking')}</span>
						<div id="selected-slots-list" className="myclub-selected-slots-list"></div>
						<button id="book-selected-slots-btn" className="myclub-book-btn"></button>
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