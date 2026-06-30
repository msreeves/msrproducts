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

	const button = siteNavigation.querySelector( '.navbar-toggler' );

	const menu = siteNavigation.getElementsByTagName( 'ul' )[ 0 ];

	// Hide menu toggle button if menu is empty and return early.
	if ( 'undefined' === typeof menu ) {
		if ( button ) {
			button.style.display = 'none';
		}
		return;
	}

	if ( ! menu.classList.contains( 'nav-menu' ) ) {
		menu.classList.add( 'nav-menu' );
	}

	const cssMenu = document.getElementById('cssmenu');

	// --- Mobile submenu as accordion (accessibility) ---
	const collapseEl = document.getElementById('navbarSupportedContent');
	const MOBILE_BREAKPOINT = 767.98;
	const isMobileNav = () => window.matchMedia('(max-width: ' + MOBILE_BREAKPOINT + 'px)').matches;
	const searchPanel = document.getElementById('site-search-panel');
	const searchTriggers = document.querySelectorAll('[data-nav-search-toggle]');
	const filterShell = document.querySelector('[data-filter-shell]');
	const filterToggle = document.querySelector('[data-filter-toggle]');
	const filterReset = document.querySelector('[data-filter-reset]');
	const filterSort = document.querySelector('[data-filter-sort]');
	const compareButtons = document.querySelectorAll('[data-compare-toggle]');
	const searchForm = document.getElementById('searchform');
	const compareModal = document.getElementById('compare-preview-modal');
	const compareModalLink = document.getElementById('compare-preview-link');
	const compareModalClose = document.getElementById('compare-preview-close');
	const cookieBanner = document.getElementById('cookie-consent-banner');
	const cookieAccept = document.getElementById('cookie-consent-accept');

	const COMPARE_KEY = 'msrproducts_compare_ids';

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
		const toggleBtn = li ? li.querySelector(':scope > .menu-sub-toggle') : null;
		if ( ! trigger || ! submenu ) return;

		li.classList.toggle('expanded', expanded);
		trigger.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		if ( toggleBtn ) {
			toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
		}

		if ( isMobileNav() ) {
			submenu.hidden = ! expanded;
			if ( expanded ) {
				submenu.removeAttribute('hidden');
			} else {
				submenu.setAttribute('hidden', '');
			}
			submenu.setAttribute('aria-hidden', expanded ? 'false' : 'true');
		} else {
			// On desktop, submenu visibility is controlled by CSS hover/focus.
			submenu.hidden = false;
			submenu.removeAttribute('hidden');
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

			let toggleBtn = li.querySelector(':scope > .menu-sub-toggle');
			if ( ! toggleBtn ) {
				toggleBtn = document.createElement('button');
				toggleBtn.type = 'button';
				toggleBtn.className = 'menu-sub-toggle';
				toggleBtn.setAttribute('aria-label', 'Toggle submenu');
				toggleBtn.innerHTML = '<span class="menu-sub-toggle__icon" aria-hidden="true"></span>';
				li.insertBefore(toggleBtn, submenu);
			}
			toggleBtn.setAttribute('aria-controls', submenuId);
			toggleBtn.setAttribute('aria-expanded', 'false');

			if ( isMobileNav() ) {
				submenu.hidden = true;
				submenu.setAttribute('hidden', '');
				submenu.setAttribute('aria-hidden', 'true');
			} else {
				submenu.hidden = false;
				submenu.removeAttribute('hidden');
				submenu.setAttribute('aria-hidden', 'false');
			}

			toggleBtn.addEventListener('click', function(e){
				if ( ! isMobileNav() ) return;
				e.preventDefault();
				toggleAccordion(li);
				const expanded = li.classList.contains('expanded');
				toggleBtn.setAttribute('aria-expanded', expanded ? 'true' : 'false');
			});

			li.dataset.accordionInit = '1';
		});
	}

	function initFilterToolbar() {
		if ( ! filterShell ) return;
		const filterLinks = filterShell.querySelectorAll('[data-filter-link]');
		const panes = filterShell.querySelectorAll('.filter-pane, .tab-pane');

		function parsePriceFromCard(card) {
			if ( ! card ) return 0;
			const priceNode = card.querySelector('.card-price');
			if ( ! priceNode ) return 0;
			const text = (priceNode.textContent || '').replace(/,/g, '');
			const match = text.match(/-?\d+(\.\d+)?/);
			return match ? Number(match[0]) : 0;
		}

		function sortCurrentPane(mode) {
			const activePane = filterShell.querySelector('.filter-pane.active, .tab-pane.active');
			if ( ! activePane ) return;
			const row = activePane.querySelector('.row');
			if ( ! row ) return;
			const cards = Array.from(row.children || []).filter(function(col){
				return !!col.querySelector('.product-card');
			});
			if ( cards.length < 2 ) return;

			if ( mode === 'default' ) {
				cards
					.sort(function(a, b) {
						const aPos = Number(a.getAttribute('data-default-pos') || '0');
						const bPos = Number(b.getAttribute('data-default-pos') || '0');
						return aPos - bPos;
					})
					.forEach(function(node){ row.appendChild(node); });
				return;
			}

			const sorted = cards.slice().sort(function(a, b) {
				const aCard = a.querySelector('.product-card');
				const bCard = b.querySelector('.product-card');
				const aTitle = ((aCard && aCard.querySelector('h3') ? aCard.querySelector('h3').textContent : '') || '').trim().toLowerCase();
				const bTitle = ((bCard && bCard.querySelector('h3') ? bCard.querySelector('h3').textContent : '') || '').trim().toLowerCase();
				const aPrice = parsePriceFromCard(aCard);
				const bPrice = parsePriceFromCard(bCard);

				if ( mode === 'az' ) return aTitle.localeCompare(bTitle);
				if ( mode === 'za' ) return bTitle.localeCompare(aTitle);
				if ( mode === 'price-asc' ) return aPrice - bPrice;
				if ( mode === 'price-desc' ) return bPrice - aPrice;
				return 0;
			});

			sorted.forEach(function(node){ row.appendChild(node); });
		}

		function activateFilter(value) {
			const target = value || 'all';
			panes.forEach(function(pane) {
				const isActive = pane.id === target;
				pane.classList.toggle('show', isActive);
				pane.classList.toggle('active', isActive);
			});
			filterLinks.forEach(function(link) {
				const linkTarget = link.getAttribute('data-filter-link');
				const isActive = linkTarget === target;
				link.setAttribute( 'aria-pressed', isActive ? 'true' : 'false' );
				link.removeAttribute( 'aria-selected' );
				if ( link.parentElement ) {
					link.parentElement.classList.toggle('active', isActive);
				}
			});
			if ( filterSort ) {
				sortCurrentPane(filterSort.value || 'default');
			}
		}

		function setFilterInUrl(value) {
			const url = new URL(window.location.href);
			if ( value && value !== 'all' ) {
				url.searchParams.set('filter', value);
			} else {
				url.searchParams.delete('filter');
			}
			window.history.replaceState({}, '', url.toString());
		}

		function activateFromUrl() {
			const activeFilter = new URL(window.location.href).searchParams.get('filter');
			if ( ! activeFilter ) {
				activateFilter('all');
				return;
			}
			const link = filterShell.querySelector('[data-filter-link="' + activeFilter + '"]');
			activateFilter(link ? activeFilter : 'all');
		}

		if ( filterToggle ) {
			filterToggle.addEventListener('click', function() {
				const expanded = filterToggle.getAttribute('aria-expanded') === 'true';
				filterToggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
				filterShell.classList.toggle('filters-open', !expanded);
			});
		}

		if ( filterReset ) {
			filterReset.addEventListener('click', function() {
				if ( filterSort ) {
					filterSort.value = 'default';
				}
				activateFilter('all');
				setFilterInUrl('all');
				sortCurrentPane('default');
			});
		}

		filterLinks.forEach(function(link) {
			link.addEventListener('click', function() {
				const target = link.getAttribute('data-filter-link') || 'all';
				activateFilter(target);
				setFilterInUrl(target);
			});
		});

		panes.forEach(function(pane) {
			const row = pane.querySelector('.row');
			if ( ! row ) return;
			Array.from(row.children || []).forEach(function(col, idx) {
				col.setAttribute('data-default-pos', String(idx));
			});
		});

		if ( filterSort ) {
			filterSort.addEventListener('change', function() {
				sortCurrentPane(filterSort.value || 'default');
			});
		}

		activateFromUrl();
	}

	function getCompareIds() {
		try {
			const raw = window.localStorage.getItem(COMPARE_KEY);
			if ( ! raw ) return [];
			const parsed = JSON.parse(raw);
			return Array.isArray(parsed) ? parsed.filter(Boolean) : [];
		} catch (e) {
			return [];
		}
	}

	function setCompareIds(ids) {
		window.localStorage.setItem(COMPARE_KEY, JSON.stringify(ids.slice(0, 3)));
	}

	function initCompareButtons() {
		if ( ! compareButtons.length ) return;
		compareButtons.forEach(function(btn) {
			btn.addEventListener('click', function() {
				const productId = btn.getAttribute('data-product-id');
				if ( ! productId ) return;
				let ids = getCompareIds();
				if ( ids.includes(productId) ) {
					ids = ids.filter(function(id) { return id !== productId; });
					btn.classList.remove('is-active');
				} else {
					if ( ids.length >= 3 ) {
						ids.shift();
					}
					ids.push(productId);
					btn.classList.add('is-active');
				}
				setCompareIds(ids);
				const compareBase = compareModalLink ? compareModalLink.getAttribute('href') : '/';
				const joiner = compareBase.indexOf('?') === -1 ? '?' : '&';
				const compareUrl = compareBase + joiner + 'ids=' + ids.join(',');
				btn.setAttribute('data-compare-url', compareUrl);
				if ( compareModal && compareModalLink ) {
					compareModal.hidden = false;
					compareModal.setAttribute('aria-hidden', 'false');
					compareModalLink.setAttribute('href', compareUrl);
				}
				if ( window.dataLayer && Array.isArray(window.dataLayer) ) {
					window.dataLayer.push({
						event: 'compare_toggle',
						product_id: productId,
						selection_count: ids.length
					});
				}
			});
		});
	}

	function initCompareModal() {
		if ( ! compareModal ) return;
		function closeModal() {
			compareModal.hidden = true;
			compareModal.setAttribute('aria-hidden', 'true');
		}
		if ( compareModalClose ) {
			compareModalClose.addEventListener('click', closeModal);
		}
		compareModal.addEventListener('click', function(event) {
			if ( event.target === compareModal ) {
				closeModal();
			}
		});
	}

	function initAnalyticsEvents() {
		if ( searchForm ) {
			searchForm.addEventListener('submit', function() {
				if ( window.dataLayer && Array.isArray(window.dataLayer) ) {
					window.dataLayer.push({
						event: 'catalog_search',
						query: (searchForm.querySelector('#s') || {}).value || ''
					});
				}
			});
		}
	}

	function initCookieBanner() {
		if ( ! cookieBanner ) return;
		const key = 'msrproducts_cookie_consent';
		const hasConsent = window.localStorage.getItem(key) === 'accepted';
		if ( ! hasConsent ) {
			cookieBanner.hidden = false;
		}
		if ( cookieAccept ) {
			cookieAccept.addEventListener('click', function() {
				window.localStorage.setItem(key, 'accepted');
				cookieBanner.hidden = true;
			});
		}
	}

	function initPredictiveSearchWidgets() {
		const widgets = document.querySelectorAll('[data-predictive-search]');
		if ( ! widgets.length ) return;

		widgets.forEach(function(widget) {
			const input = widget.querySelector('[data-predictive-search-input]');
			const listEl = widget.querySelector('[data-predictive-search-list]');
			const box = widget.querySelector('[data-predictive-search-box]');
			const jsonEl = widget.querySelector('[data-search-items-json]');
			if ( ! input || ! listEl || ! box || ! jsonEl ) return;

			let items = [];
			try {
				items = JSON.parse(jsonEl.textContent || '[]');
				if ( ! Array.isArray(items) ) {
					items = [];
				}
			} catch (e) {
				items = [];
			}

			let activeIndex = -1;
			let filteredItems = [];

			function hideBox() {
				box.hidden = true;
				input.setAttribute('aria-expanded', 'false');
				activeIndex = -1;
			}

			function showBox() {
				box.hidden = false;
				input.setAttribute('aria-expanded', 'true');
			}

			function render(list) {
				listEl.innerHTML = '';
				if ( ! list.length ) {
					hideBox();
					return;
				}
				list.forEach(function(item, idx) {
					const li = document.createElement('li');
					const link = document.createElement('a');
					link.href = item.url || '/';
					link.className = 'site-search-autocomplete__item';
					link.setAttribute('data-search-item-index', String(idx));
					link.innerHTML =
						'<span class="site-search-autocomplete__left">' +
							'<span class="site-search-autocomplete__icon" aria-hidden="true">' + (item.icon === 'category' ? '▦' : '◷') + '</span>' +
							'<span class="site-search-autocomplete__label"></span>' +
						'</span>' +
						'<span class="site-search-autocomplete__type"></span>';
					const label = link.querySelector('.site-search-autocomplete__label');
					const type = link.querySelector('.site-search-autocomplete__type');
					if ( label ) label.textContent = item.label || '';
					if ( type ) type.textContent = item.type || '';
					li.appendChild(link);
					listEl.appendChild(li);
				});
				showBox();
			}

			function updateActive() {
				const links = listEl.querySelectorAll('.site-search-autocomplete__item');
				links.forEach(function(link, idx) {
					link.classList.toggle('is-active', idx === activeIndex);
				});
			}

			function filter(query) {
				const q = (query || '').trim().toLowerCase();
				if ( q.length < 1 ) {
					hideBox();
					return;
				}
				filteredItems = items.filter(function(item) {
					const label = (item && item.label ? String(item.label) : '').toLowerCase();
					const type = (item && item.type ? String(item.type) : '').toLowerCase();
					return label.indexOf(q) !== -1 || type.indexOf(q) !== -1;
				}).slice(0, 8);
				activeIndex = -1;
				render(filteredItems);
			}

			input.addEventListener('input', function() {
				filter(input.value);
			});

			input.addEventListener('focus', function() {
				if ( input.value.trim().length > 0 ) {
					filter(input.value);
				}
			});

			input.addEventListener('keydown', function(event) {
				if ( box.hidden || ! filteredItems.length ) return;
				if ( event.key === 'ArrowDown' ) {
					event.preventDefault();
					activeIndex = Math.min(filteredItems.length - 1, activeIndex + 1);
					updateActive();
				} else if ( event.key === 'ArrowUp' ) {
					event.preventDefault();
					activeIndex = Math.max(0, activeIndex - 1);
					updateActive();
				} else if ( event.key === 'Enter' && activeIndex >= 0 && filteredItems[activeIndex] ) {
					event.preventDefault();
					window.location.href = filteredItems[activeIndex].url || '/';
				}
			});

			document.addEventListener('click', function(event) {
				const insideInput = input.contains(event.target);
				const insideBox = box.contains(event.target);
				if ( ! insideInput && ! insideBox ) {
					hideBox();
				}
			});
		});
	}

	function initDesktopMegaPreview() {
		if ( ! cssMenu ) return;
		const isDesktopMegaPreviewNav = () => window.matchMedia('(min-width: 992px) and (hover: hover) and (pointer: fine)').matches;
		const megaMenus = cssMenu.querySelectorAll('ul.manic-menu > li.has-sub > ul.mega-menu');

		if ( ! isDesktopMegaPreviewNav() ) {
			megaMenus.forEach(function(megaMenu) {
				megaMenu.classList.remove('mega-menu--split');
				megaMenu.querySelectorAll(':scope > li.mega-preview').forEach(function(node) {
					node.remove();
				});
				megaMenu.querySelectorAll(':scope > li').forEach(function(item) {
					item.classList.remove('is-active');
				});
				delete megaMenu.dataset.splitInit;
			});
			return;
		}

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

				const card = document.createElement('a');
				card.className = 'mega-preview-card';
				card.dataset.sourceId = sourceId;
				card.href = link.getAttribute('href') || '/';
				card.setAttribute('aria-label', titleText || 'Open product');
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

	function initDesktopClickDropdown() {
		if ( ! cssMenu ) return;
		const isDesktopHoverNav = () => window.matchMedia('(min-width: 768px) and (hover: hover) and (pointer: fine)').matches;
		if ( ! isDesktopHoverNav() ) return;
		const menuItems = cssMenu.querySelectorAll('ul.manic-menu > li.has-sub');
		if ( ! menuItems.length ) return;
		const OPEN_DELAY_MS = 80;
		const CLOSE_DELAY_MS = 170;
		const SCROLL_SUPPRESS_MS = 320;
		let openTimer = null;
		let closeTimer = null;
		let suppressHoverUntil = 0;

		function closeAllDesktopDropdown(exceptItem) {
			menuItems.forEach(function(node) {
				if ( exceptItem && node === exceptItem ) return;
				node.classList.remove('is-open');
				const anchor = node.querySelector(':scope > a');
				if ( anchor ) anchor.setAttribute('aria-expanded', 'false');
			});
		}

		function openDesktopDropdown(item) {
			const trigger = item.querySelector(':scope > a');
			closeAllDesktopDropdown(item);
			item.classList.add('is-open');
			if ( trigger ) trigger.setAttribute('aria-expanded', 'true');
		}

		function clearOpenTimer() {
			if ( openTimer ) {
				window.clearTimeout(openTimer);
				openTimer = null;
			}
		}

		function clearCloseTimer() {
			if ( closeTimer ) {
				window.clearTimeout(closeTimer);
				closeTimer = null;
			}
		}

		function scheduleOpen(item) {
			if ( Date.now() < suppressHoverUntil ) return;
			clearOpenTimer();
			clearCloseTimer();
			openTimer = window.setTimeout(function() {
				openDesktopDropdown(item);
			}, OPEN_DELAY_MS);
		}

		function scheduleClose() {
			clearOpenTimer();
			clearCloseTimer();
			closeTimer = window.setTimeout(function() {
				closeAllDesktopDropdown();
			}, CLOSE_DELAY_MS);
		}

		menuItems.forEach(function(item, idx) {
			const submenu = item.querySelector(':scope > ul.mega-menu');
			const trigger = item.querySelector(':scope > a');
			if ( ! trigger ) return;

			const submenuId = submenu && submenu.id ? submenu.id : ('msr-desktop-submenu-' + idx);
			if ( submenu ) submenu.id = submenuId;

			trigger.setAttribute('aria-haspopup', 'true');
			trigger.setAttribute('aria-controls', submenuId);
			trigger.setAttribute('aria-expanded', item.classList.contains('is-open') ? 'true' : 'false');

			item.addEventListener('mouseenter', function() {
				if ( ! isDesktopHoverNav() ) return;
				scheduleOpen(item);
			});

			item.addEventListener('mouseleave', function(event) {
				if ( ! isDesktopHoverNav() ) return;
				const nextTarget = event && event.relatedTarget ? event.relatedTarget : null;
				if ( submenu && nextTarget && submenu.contains(nextTarget) ) return;
				scheduleClose();
			});

			if ( submenu ) {
				submenu.addEventListener('mouseenter', function() {
					if ( ! isDesktopHoverNav() ) return;
					clearCloseTimer();
				});

				submenu.addEventListener('mouseleave', function(event) {
					if ( ! isDesktopHoverNav() ) return;
					const nextTarget = event && event.relatedTarget ? event.relatedTarget : null;
					if ( item.contains(nextTarget) ) return;
					scheduleClose();
				});
			}

			item.addEventListener('keydown', function(event) {
				if ( event.key !== 'Escape' ) return;
				item.classList.remove('is-open');
				trigger.setAttribute('aria-expanded', 'false');
			});
		});

		document.addEventListener('click', function(event) {
			if ( ! cssMenu.contains(event.target) ) {
				closeAllDesktopDropdown();
			}
		});

		cssMenu.addEventListener('mouseenter', function() {
			clearCloseTimer();
		});

		cssMenu.addEventListener('mouseleave', function() {
			if ( ! isDesktopHoverNav() ) return;
			scheduleClose();
		});

		cssMenu.addEventListener('click', function(event) {
			if ( ! isDesktopHoverNav() ) return;
			const submenuLink = event.target.closest('ul.mega-menu a');
			if ( submenuLink ) {
				closeAllDesktopDropdown();
			}
		});

		let lastScrollY = window.scrollY;
		window.addEventListener('scroll', function() {
			if ( ! isDesktopHoverNav() ) return;
			const delta = Math.abs(window.scrollY - lastScrollY);
			lastScrollY = window.scrollY;
			if ( delta < 16 ) return;
			const hoveringOpenNav = !!cssMenu.querySelector('ul.manic-menu > li.has-sub.is-open:hover, ul.manic-menu > li.has-sub.is-open > ul.mega-menu:hover');
			if ( hoveringOpenNav ) return;
			suppressHoverUntil = Date.now() + SCROLL_SUPPRESS_MS;
			closeAllDesktopDropdown();
		}, { passive: true });

		window.addEventListener('resize', function() {
			closeAllDesktopDropdown();
		});
	}

	// Initialize once; sync states on viewport changes.
	initMobileAccordion();
	initDesktopMegaPreview();
	initDesktopClickDropdown();
	initFilterToolbar();
	initCompareButtons();
	initCompareModal();
	initAnalyticsEvents();
	initCookieBanner();
	initPredictiveSearchWidgets();
	if ( isMobileNav() ) {
		closeAllAccordion();
	}
	window.addEventListener('resize', function(){
		// Keep it simple: refresh only submenu state, not listeners.
		initDesktopMegaPreview();
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
			if ( button ) {
				button.focus({ preventScroll: true });
			}
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
			if ( button ) {
				button.click();
			}
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
	if ( button ) {
		button.addEventListener( 'click', function() {
			siteNavigation.classList.toggle( 'toggled' );
			toggleSearchPanel(false);

			if ( button.getAttribute( 'aria-expanded' ) === 'true' ) {
				button.setAttribute( 'aria-expanded', 'false' );
			} else {
				button.setAttribute( 'aria-expanded', 'true' );
			}
		} );
	}

	// Remove the .toggled class and set aria-expanded to false when the user clicks outside the navigation.
	document.addEventListener( 'click', function( event ) {
		const isClickInside = siteNavigation.contains( event.target );
		const isSearchPanelClick = searchPanel ? searchPanel.contains(event.target) : false;

		if ( ! isClickInside ) {
			siteNavigation.classList.remove( 'toggled' );
			if ( button ) {
				button.setAttribute( 'aria-expanded', 'false' );
			}
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
