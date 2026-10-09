/**
 * Homepage "Meet your therapist": collapse a long bio to roughly the photo's
 * height (desktop) or a comfortable preview (phones), with a fade and a
 * "Continue reading" / "Show less" toggle. Short bios are left alone, and
 * without JavaScript the full bio shows.
 */
( function () {
	const bio = document.getElementById( 'home-about-bio' );
	const button = document.querySelector( '.home-about__more' );
	if ( ! bio || ! button ) {
		return;
	}

	const media = document.querySelector( '.home-about__media' );
	const textColumn = bio.closest( '.home-about__text' );
	const MIN_PREVIEW = 240; // px of bio always shown when collapsed
	const WORTH_IT = 140; // only collapse if it hides at least this much

	let expanded = false;

	// Same breakpoint as main.css: below it the photo stacks above the text.
	const sideBySide = window.matchMedia( '(min-width: 861px)' );

	// Side-by-side: end the preview about level with the photo's bottom.
	// Measured from heights/offsets, not screen position, because the photo
	// is sticky and may already be shifted if the page loaded mid-scroll.
	// Stacked (phones): a fixed preview.
	const previewHeight = () => {
		if ( ! media || ! sideBySide.matches ) {
			return 320;
		}
		const bioOffset = bio.getBoundingClientRect().top - textColumn.getBoundingClientRect().top;
		const buttonSpace = 56;
		return Math.max( MIN_PREVIEW, Math.round( media.offsetHeight - bioOffset - buttonSpace ) );
	};

	const apply = () => {
		if ( expanded ) {
			return;
		}
		bio.style.maxHeight = '';
		bio.classList.remove( 'is-collapsed' );
		const limit = previewHeight();
		const needed = bio.scrollHeight > limit + WORTH_IT;
		button.hidden = ! needed;
		if ( needed ) {
			bio.style.maxHeight = limit + 'px';
			bio.classList.add( 'is-collapsed' );
		}
	};

	const setExpanded = ( open ) => {
		expanded = open;
		button.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		button.textContent = open ? button.dataset.less : button.dataset.more;
		if ( open ) {
			bio.style.maxHeight = '';
			bio.classList.remove( 'is-collapsed' );
		} else {
			apply();
			// Bring the start of the section back into view after collapsing.
			const top = textColumn.getBoundingClientRect().top;
			if ( top < 0 ) {
				textColumn.scrollIntoView( { block: 'start' } );
			}
		}
	};

	button.addEventListener( 'click', () => setExpanded( ! expanded ) );

	// A keyboard user tabbing into a link hidden in the collapsed part:
	// expand so the focused link is visible.
	bio.addEventListener( 'focusin', () => {
		if ( ! expanded && bio.classList.contains( 'is-collapsed' ) ) {
			setExpanded( true );
		}
	} );

	let resizeTimer;
	window.addEventListener( 'resize', () => {
		clearTimeout( resizeTimer );
		resizeTimer = setTimeout( apply, 150 );
	} );

	// Measure after images/fonts settle so the photo height is final.
	if ( document.readyState === 'complete' ) {
		apply();
	} else {
		window.addEventListener( 'load', apply );
	}
	apply();
} )();
