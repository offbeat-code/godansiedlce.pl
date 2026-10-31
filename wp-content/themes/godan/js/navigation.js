/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens and enables TAB key
 * navigation support for dropdown menus.
 */
( function() {
	const siteNavigation = document.getElementById( 'site-navigation' );

	// Return early if the navigation doesn't exist.
	if ( ! siteNavigation ) {
		return;
	}

	const button = siteNavigation.getElementsByTagName( 'button' )[ 0 ];

	// Return early if the button doesn't exist.
	if ( 'undefined' === typeof button ) {
		return;
	}

	const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		button.style.display = 'none';
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	// Toggle the .toggled class and the aria-expanded value each time the button is clicked.
	button.addEventListener( 'click', function() {
		siteNavigation.classList.toggle( 'toggled' );

		if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
			button.setAttribute( 'aria-expanded', 'false' );
		} else {
			button.setAttribute( 'aria-expanded', 'true' );
		}
	} );

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target );

		if ( ! isClickInside ) {
			siteNavigation.classList.remove( 'toggled' );
			button.setAttribute( 'aria-expanded', 'false' );
		}
	} );

	// Get all the link elements within the menu.
	const links = menu.getElementsByTagName( 'a' );

	// Get all the link elements with children within the menu.
	const linksWithChildren = menu.querySelectorAll( '.menu-item-has-children > a, .page_item_has_children > a' );

	// Toggle focus each time a menu link is focused or blurred.
	for ( const link of links ) {
		link.addEventListener( 'focus', toggleFocus, true );
		link.addEventListener( 'blur', toggleFocus, true );
	}

	// Toggle focus each time a menu link with children receive a touch event.
	for ( const link of linksWithChildren ) {
		link.addEventListener( 'touchstart', toggleFocus, false );
	}

	/**
	 * Sets or removes .focus class on an element.
	 */
	function toggleFocus() {
		if ( event.type === 'focus' || event.type === 'blur' ) {
			let self = this;
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( ! self.classList.contains( 'nav-menu' ) ) {
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus' );
				}
				self = self.parentNode;
			}
		}

		if ( event.type === 'touchstart' ) {
			const menuItem = this.parentNode;
			event.preventDefault();
			for ( const link of menuItem.parentNode.children ) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			}
			menuItem.classList.toggle( 'focus' );
		}
	}
}() );

/**
 * Mobile Navigation
 */
( function() {
	const burger = document.querySelector( '.nav-mobile__burger' );
	const closeBtn = document.querySelector( '.nav-mobile__close' );
	const overlay = document.querySelector( '.nav-mobile__overlay' );
	const drawer = document.querySelector( '.nav-mobile__drawer' );

	if ( ! burger || ! drawer ) {
		return;
	}

	// Move overlay and drawer to body for proper z-index and backdrop-filter
	document.body.appendChild( overlay );
	document.body.appendChild( drawer );

	let scrollPosition = 0;

	function openMenu() {
		scrollPosition = window.pageYOffset;
		document.body.style.overflow = 'hidden';
		document.body.style.position = 'fixed';
		document.body.style.top = `-${scrollPosition}px`;
		document.body.style.left = '0';
		document.body.style.right = '0';
		document.body.classList.add( 'menu-open' );

		burger.setAttribute( 'aria-expanded', 'true' );
		overlay.classList.add( 'is-active' );
		drawer.classList.add( 'is-active' );
	}

	function closeMenu() {
		document.body.style.overflow = '';
		document.body.style.position = '';
		document.body.style.top = '';
		document.body.style.left = '';
		document.body.style.right = '';
		document.body.classList.remove( 'menu-open' );
		window.scrollTo( 0, scrollPosition );

		burger.setAttribute( 'aria-expanded', 'false' );
		overlay.classList.remove( 'is-active' );
		drawer.classList.remove( 'is-active' );
	}

	burger.addEventListener( 'click', openMenu );
	closeBtn.addEventListener( 'click', closeMenu );
	overlay.addEventListener( 'click', closeMenu );

	// Close on ESC key
	document.addEventListener( 'keydown', function( e ) {
		if ( e.key === 'Escape' && drawer.classList.contains( 'is-active' ) ) {
			closeMenu();
		}
	} );

	// Handle mobile submenu toggle
	const mobileParentItems = drawer.querySelectorAll( '.menu-item-has-children' );
	mobileParentItems.forEach( function( item ) {
		const link = item.querySelector( ':scope > a' );

		link.addEventListener( 'click', function( e ) {
			e.preventDefault();

			// Close other open submenus
			mobileParentItems.forEach( function( otherItem ) {
				if ( otherItem !== item ) {
					otherItem.classList.remove( 'is-open' );
				}
			} );

			// Toggle current submenu
			item.classList.toggle( 'is-open' );
		} );
	} );

	// Close when clicking submenu links (not parent items)
	const menuLinks = drawer.querySelectorAll( '.nav-mobile__menu-list a' );
	menuLinks.forEach( function( link ) {
		const parentLi = link.parentElement;
		if ( ! parentLi.classList.contains( 'menu-item-has-children' ) ) {
			link.addEventListener( 'click', closeMenu );
		}
	} );

	// Also close menu when clicking submenu items
	const subMenuLinks = drawer.querySelectorAll( '.sub-menu a' );
	subMenuLinks.forEach( function( link ) {
		link.addEventListener( 'click', closeMenu );
	} );

	// Close menu when clicking CTA or phone links
	const ctaLink = drawer.querySelector( '.nav-mobile__cta' );
	const phoneLink = drawer.querySelector( '.nav-mobile__phone' );

	if ( ctaLink ) {
		ctaLink.addEventListener( 'click', closeMenu );
	}
	if ( phoneLink ) {
		phoneLink.addEventListener( 'click', closeMenu );
	}
}() );

/**
 * Desktop Navigation - Prevent click on parent items
 */
( function() {
	const desktopParentItems = document.querySelectorAll( '.nav-desktop__menu-list > .menu-item-has-children > a' );

	desktopParentItems.forEach( function( link ) {
		link.addEventListener( 'click', function( e ) {
			e.preventDefault();
		} );
	} );
}() );

/**
 * Header scroll effect
 * Adds 'scrolled' class to header when page is scrolled
 */
( function() {
	const header = document.getElementById( 'masthead' );

	if ( ! header ) {
		return;
	}

	function handleScroll() {
		if ( window.scrollY > 20 ) {
			header.classList.add( 'scrolled' );
		} else {
			header.classList.remove( 'scrolled' );
		}
	}

	// Run on scroll
	window.addEventListener( 'scroll', handleScroll );

	// Run on page load (in case page is already scrolled)
	handleScroll();
}() );

/**
 * Testimonials Swiper
 */
( function() {
	const testimonialsSlider = document.querySelector( '.testimonials__slider' );

	if ( ! testimonialsSlider || typeof Swiper === 'undefined' ) {
		return;
	}

	new Swiper( testimonialsSlider, {
		slidesPerView: 1,
		spaceBetween: 16,
		loop: true,
		speed: 500,
		grabCursor: true,
		autoplay: {
			delay: 4000,
			disableOnInteraction: false,
			pauseOnMouseEnter: true
		},
		breakpoints: {
			640: {
				slidesPerView: 2,
				spaceBetween: 24
			},
			1024: {
				slidesPerView: 3,
				spaceBetween: 32
			},
			1536: {
				slidesPerView: 4,
				spaceBetween: 32
			}
		}
	} );
}() );

/**
 * Lucide Icons
 */
( function() {
	if ( typeof lucide !== 'undefined' ) {
		lucide.createIcons();
	}
}() );
