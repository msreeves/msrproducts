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

	const cssMenu = document.getElementById('cssmenu');

	// --- Mobile submenu as accordion (accessibility) ---
	const collapseEl = document.getElementById('navbarSupportedContent');
	const isMobileNav = () => window.matchMedia('(max-width: 991.98px)').matches;
	const searchPanel = document.getElementById('site-search-panel');
	const searchTriggers = document.querySelectorAll('[data-nav-search-toggle]');

	function getAccordionItems() {
		if ( ! cssMenu ) return [];
		return cssMenu.querySelectorAll('ul.manic-menu > li.has-sub');
	}

	function getAccordionTrigger(li) {
		if ( ! li ) return null;
		return li.querySelector(':scope > a');
	}

	function getAccordionSubmenu(li) {
		if ( ! li ) return null;
		return li.querySelector(':scope > ul.mega-menu, :scope > ul.sub-menu, :scope > ul');
	}

	function setAccordionState(li, expanded) {
		const trigger = getAccordionTrigger(li);
		const submenu = getAccordionSubmenu(li);
		if ( ! trigger || ! submenu ) return;

		li.classList.toggle('expanded', expanded);
		trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');

		if ( isMobileNav() ) {
			submenu.hidden = ! expanded;
			submenu.setAttribute('aria-hidden', expanded ? 'false' : 'true');
		} else {
			// On desktop, submenu visibility is controlled by CSS hover/focus.
			submenu.hidden = false;
			submenu.setAttribute('aria-hidden', 'false');
		}
	}

	function closeAllAccordion(exceptLi) {
		getAccordionItems().forEach(function(li){
			if ( exceptLi && li === exceptLi ) return;
			setAccordionState(li, false);
		});
	}

	function toggleAccordion(li) {
		const trigger = getAccordionTrigger(li);
		if ( ! trigger ) return;

		const expanded = trigger.getAttribute('aria-expanded') === 'true';
		closeAllAccordion();
		setAccordionState(li, ! expanded);
	}

	function initMobileAccordion() {
		getAccordionItems().forEach(function(li, idx){
			if ( li.dataset.accordionInit === '1' ) return;

			const trigger = getAccordionTrigger(li);
			const submenu = getAccordionSubmenu(li);
			if ( ! trigger || ! submenu ) return;

			const submenuId = submenu.id || 'msr-accordion-submenu-' + idx;
			submenu.id = submenuId;

			trigger.setAttribute('aria-haspopup', 'true');
			trigger.setAttribute('aria-controls', submenuId);
			trigger.setAttribute('aria-expanded', 'false');

			if ( isMobileNav() ) {
				submenu.hidden = true;
				submenu.setAttribute('aria-hidden', 'true');
			} else {
				submenu.hidden = false;
				submenu.setAttribute('aria-hidden', 'false');
			}

			trigger.addEventListener('click', function(e){
				if ( ! isMobileNav() ) return;
				e.preventDefault();
				toggleAccordion(li);
			});

			trigger.addEventListener('keydown', function(e){
				if ( ! isMobileNav() ) return;
				if ( e.key === 'Enter' || e.key === ' ' ) {
					e.preventDefault();
					toggleAccordion(li);
				}
			});

			li.dataset.accordionInit = '1';
		});
	}

	function initDesktopMegaPreview() {
		if ( ! cssMenu ) return;
		const megaMenus = cssMenu.querySelectorAll('ul.manic-menu > li.has-sub > ul.mega-menu');

		megaMenus.forEach(function(megaMenu) {
			if ( megaMenu.dataset.splitInit === '1' ) return;

			megaMenu.classList.add('mega-menu--split');

			const previewItem = document.createElement('li');
			previewItem.className = 'mega-preview';
			previewItem.setAttribute('aria-hidden', 'true');
			previewItem.innerHTML = '<div class="mega-preview-grid"></div>';
			megaMenu.appendChild(previewItem);

			const previewGrid = previewItem.querySelector('.mega-preview-grid');
			const links = megaMenu.querySelectorAll(':scope > li:not(.mega-preview) > a');
			if ( ! links.length ) return;

			function markActive(link) {
				megaMenu.querySelectorAll(':scope > li').forEach(function(item) {
					item.classList.remove('is-active');
				});
				if ( link && link.parentElement ) {
					link.parentElement.classList.add('is-active');
				}

				if ( ! previewGrid ) return;
				const activeId = link && link.parentElement ? link.parentElement.id : '';
				previewGrid.querySelectorAll('.mega-preview-card').forEach(function(card){
					card.classList.toggle('is-active', card.dataset.sourceId === activeId);
				});
			}

			links.forEach(function(link) {
				const sourceItem = link.parentElement;
				const sourceId = sourceItem ? sourceItem.id : '';
				const previewMeta = link.querySelector('.menu-preview-meta');
				const imageEl = link.querySelector('.nav-product-image');
				const titleEl = link.querySelector('.menu-label, .nav-product-title, span');
				const priceEl = link.querySelector('.nav-product-price');

				const imageSrc = previewMeta ? (previewMeta.dataset.previewImage || '') : (imageEl ? imageEl.getAttribute('src') : '');
				const titleText = previewMeta ? (previewMeta.dataset.previewTitle || '') : (titleEl ? titleEl.textContent.trim() : link.textContent.trim());
				const priceText = previewMeta ? (previewMeta.dataset.previewPrice || '') : (priceEl ? priceEl.textContent.trim() : '');

				if ( ! imageSrc ) return;

				const card = document.createElement('article');
				card.className = 'mega-preview-card';
				card.dataset.sourceId = sourceId;
				card.innerHTML = '' +
					'<div class="mega-preview-image-wrap"><img class="mega-preview-image" alt="" src="' + imageSrc + '"></div>' +
					'<div class="mega-preview-copy">' +
						'<span class="mega-preview-title"></span>' +
						'<span class="mega-preview-price"></span>' +
					'</div>';
				const cardTitle = card.querySelector('.mega-preview-title');
				const cardPrice = card.querySelector('.mega-preview-price');
				const cardImage = card.querySelector('.mega-preview-image');
				if ( cardTitle ) cardTitle.textContent = titleText || '';
				if ( cardPrice ) cardPrice.textContent = priceText || '';
				if ( cardImage ) cardImage.alt = titleText || 'Navigation preview';
				previewGrid.appendChild(card);
			});

			const firstLink = links[0];
			markActive(firstLink);

			links.forEach(function(link) {
				link.addEventListener('mouseenter', function() {
					markActive(link);
				});
				link.addEventListener('focus', function() {
					markActive(link);
				});
			});

			megaMenu.dataset.splitInit = '1';
		});
	}

	// Initialize once; sync states on viewport changes.
	initMobileAccordion();
	initDesktopMegaPreview();
	if ( isMobileNav() ) {
		closeAllAccordion();
	}
	window.addEventListener('resize', function(){
		// Keep it simple: refresh only submenu state, not listeners.
		if ( isMobileNav() ) {
			closeAllAccordion();
		} else {
			getAccordionItems().forEach(function(li){
				setAccordionState(li, false);
			});
		}
	});

	function toggleSearchPanel(forceState) {
		if ( ! searchPanel ) return;
		const isOpen = !searchPanel.hidden;
		const nextState = typeof forceState === 'boolean' ? forceState : !isOpen;

		searchPanel.hidden = !nextState;
		searchPanel.setAttribute('aria-hidden', nextState ? 'false' : 'true');
		searchTriggers.forEach(function(trigger) {
			trigger.setAttribute('aria-expanded', nextState ? 'true' : 'false');
		});
	}

	function focusFirstNavLink() {
		if ( ! collapseEl ) return;
		const firstLink = collapseEl.querySelector('#cssmenu a');
		if ( firstLink ) firstLink.focus({ preventScroll: true });
	}

	// Open/close focus management
	if ( collapseEl ) {
		collapseEl.addEventListener('shown.bs.collapse', function(){
			if ( isMobileNav() ) {
				closeAllAccordion();
				focusFirstNavLink();
			}
		});
		collapseEl.addEventListener('hidden.bs.collapse', function(){
			if ( isMobileNav() ) {
				closeAllAccordion();
			}
			button.focus({ preventScroll: true });
		});
	}

	searchTriggers.forEach(function(trigger) {
		trigger.addEventListener('click', function() {
			toggleSearchPanel();
		});
	});

	// Close on Escape
	document.addEventListener('keydown', function(e){
		if ( e.key !== 'Escape' ) return;

		if ( searchPanel && !searchPanel.hidden ) {
			e.preventDefault();
			toggleSearchPanel(false);
			return;
		}

		if ( collapseEl && collapseEl.classList.contains('show') ) {
			e.preventDefault();
			closeAllAccordion();
			button.click();
		}
	});

	// Minimal focus trap while mobile menu is open
	document.addEventListener('keydown', function(e){
		if ( ! collapseEl ) return;
		if ( ! collapseEl.classList.contains('show') ) return;
		if ( e.key !== 'Tab' ) return;

		const focusable = Array.prototype.slice.call(
			collapseEl.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])')
		).filter(function(el){ return el.offsetParent !== null; });

		if ( focusable.length === 0 ) return;
		const first = focusable[0];
		const last = focusable[focusable.length - 1];
		const active = document.activeElement;

		if ( e.shiftKey && active === first ) {
			e.preventDefault();
			last.focus({ preventScroll: true });
		} else if ( ! e.shiftKey && active === last ) {
			e.preventDefault();
			first.focus({ preventScroll: true });
		}
	});

	// Toggle the .toggled class and the aria-expanded value each time the button is clicked.
	button.addEventListener( 'click', function() {
		siteNavigation.classList.toggle( 'toggled' );
		toggleSearchPanel(false);

		if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
			button.setAttribute( 'aria-expanded', 'false' );
		} else {
			button.setAttribute( 'aria-expanded', 'true' );
		}
	} );

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target );
		const isSearchPanelClick = searchPanel ? searchPanel.contains(event.target) : false;

		if ( ! isClickInside ) {
			siteNavigation.classList.remove( 'toggled' );
			button.setAttribute( 'aria-expanded', 'false' );
		}

		if ( searchPanel && !isSearchPanelClick && !event.target.closest('[data-nav-search-toggle]') ) {
			toggleSearchPanel(false);
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
	function toggleFocus(event) {
		if ( ! event ) return;

		if ( event.type === 'focus' || event.type === 'blur' ) {
			let self = this;
			// Move up through the ancestors of the current link until we hit .nav-menu.
			while ( self && ! self.classList.contains( 'nav-menu' ) ) {
				// On li elements toggle the class .focus.
				if ( 'li' === self.tagName.toLowerCase() ) {
					self.classList.toggle( 'focus' );
				}
				self = self.parentNode;
			}
		}

		if ( event.type === 'touchstart' ) {
			const menuItem = this.parentNode;
			for ( const link of menuItem.parentNode.children ) {
				if ( menuItem !== link ) {
					link.classList.remove( 'focus' );
				}
			}
			menuItem.classList.toggle( 'focus' );
		}
	}
}() );
