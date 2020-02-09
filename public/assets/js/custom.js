(function($) {
	/* Activation des tooltip de bootstrap */
	$('[data-toggle="tooltip"]').tooltip();

	/* Carousel Bootstrap */
	$('.carousel').carousel({
		interval : false
	});
	$('.carousel-controlBar button').click(function() {
		$('.overlay-card').remove();
	});

	/*$('input#search').click(function () {
		$(this).parent('form').submit();
	});*/
	/* Quelques var globales pour l'overlay */
	var overlay_card_w	= 220;
	var overlay_card_h	= 0;
	var one_card_w	 	= 0;
	var size 			= 2;
	var card_data;
	/* ===========================================================================
	* 							A J A X - FUNCTION RESPONSE
	* ============================================================================*/
	function ajax_response (message, removed, new_quantity, one_card) {
		let ajax_response = $('#ajax-response');
		let timeout;
		$('#ajax-response p').text(message);

		if (ajax_response.css('display') === 'none') {
			ajax_response.show('fast');
		}
		else {
			if (timeout) {
				clearTimeout(timeout);
			}
			ajax_response.hide();
			ajax_response.show('fast');
		}
		if (one_card !== null) {
			if (removed === true) {
				one_card.hide('slow').remove();
				$('.overlay-card').remove();
			}
			if (new_quantity !== null) {
				one_card.find('.card-quantity').html(new_quantity);
			}
		}
		timeout = setTimeout(function() {
			ajax_response.hide('slow');
		}, 4000);
	}

	/* Vérification du carousel */
	var carousel = $('.carousel').attr('id');

	switch (carousel) {
		case 'ext-carousel':
    		console.log('ext');
    		carousel = 1;
    		break;
  		case 'col-carousel':
  			console.log('col');
  			carousel = 2;
  			break;
  		case 'wish-carousel':
  			console.log('wish');
  			carousel = 3;
    		break;
  		default:
	}

	/* Sélection de la taille des cartes dans le carousel */
	$('.sizing .dropdown-item').click(function(){

		size 			= $(this).data('size');
		var each_row  	= $('.carousel-inner .row');
		var lastClass 	= each_row.attr('class').split(' ').pop();
		each_row.removeClass(lastClass);

		switch (size) {
  			case 1:
    			each_row.addClass('small');
    			break;
  			case 2:
  				each_row.addClass('medium');
  				overlay_card_w = 220;
  				break;
  			case 3:
  				each_row.addClass('large');
  				overlay_card_w = 299;
    			break;
  			default:
		}

	});

	/* Placement des overlays sur les cartes */

	$('.carousel-item .one-card').hover(function() {
			$('.overlay-card').remove();
			var overlay_card = document.createElement("div");
			overlay_card.className = "overlay-card";
			overlay_card.style.display = "none";

			var overlay_position = $(this).position();
			var parent 			 = $(this).parent();
			var img_card 		 = $(this).children("img");
			var one_card		 = $(this);
			var parent_width 	 = parent.width();
			var float = "";

			one_card_w 		= $(this).width();

			if ((overlay_position.left + one_card_w) > (parent_width-1) && 
				(overlay_position.left + one_card_w) < (parent_width+1)) {

				overlay_card.style.top  = overlay_position.top +"px";
				overlay_card.style.right = "0px";
				float = "last";
			}
			else {
				overlay_card.style.top  = overlay_position.top +"px";
				overlay_card.style.left = overlay_position.left + "px";
				float = "";
			}
			card_data = {
				id		: $(this).data("id"),
				title	: $(this).data("title"),
				color	: $(this).data("color"),
				type	: $(this).data("type"),
				rarity	: $(this).data("rarity"),
				ext		: $(this).data("ext"),
				ext_num	: $(this).data("ext-num")
			};

			overlay_card.innerHTML = formCreation(float, card_data, size, carousel);

			parent.append(overlay_card);

			/* ===========================================================================
			* 								 A J A X - OVERLAY
			* ============================================================================*/
			$('.form-container form').on('submit', function (event) {
				event.preventDefault();

				//let location = window.location.host+"/pwd/";
				let location = window.location.origin+"/pwd/";
				console.log("Est ce que ça marche ? "+location);
				let route = "";
				let quantity = $("input", this).val();
				/* juste pour les ajouts / suppression dans le menu des wishlists */
				let wish_target = one_card.data('wishid');
				/* les données qui vont être envoyée en ajax */
				let data = { quantity: quantity, wishlist: wish_target };

				switch (this.className) {
					case "add-wishlist":
						route = "/Users/wishlists/add/"+card_data['id'];
						break;
					case "remove-wishlist":
						route = "/Users/wishlists/remove/"+card_data['id'];
						break;
					case "add-collection":
						route = "/Users/collection/add/"+card_data['id'];
						break;
					case "remove-collection":
						route = "/Users/collection/remove/"+card_data['id'];
						break;
					default:
						console.log("gros caca !")
				}

				console.log("La route appellée est :"+route);
				console.log("La wishlist ciblée est :" +wish_target);

				let message;
				let removed;
				let new_quantity;
				let target = location + route;

				$.post(target, data, function( data ) {
					console.log(data);
					message = data.message;
					removed = data.removed;
					new_quantity = data.quantity;
				})
					.done(function () {
						ajax_response(message, removed, new_quantity, one_card);
					})
					.fail(function () {

					})
					.always(function () {

					});
			});

			/* =========================================================================== */

			/* bouton pour agrandir la carte */
			$('.scaler').on('click', function(event) {
				event.preventDefault();
			});
			$('.scaler').hover(
				function (){
					if (float == "last") {
						img_card.addClass('scaleUpLeft');
					}
					img_card.addClass('scaleUp');
					one_card.addClass('scaleUp');

					$('.overlay-card').addClass('scaleUp');

					$(one_card).children('.you-own').hide();
					$('.overlay-card .flex-container').hide();
					$('.overlay-card .info-card').hide();
				},
				function() {
					if (float == "last") {
						img_card.removeClass('scaleUpLeft');
					}
					img_card.removeClass('scaleUp');
					one_card.removeClass('scaleUp');

					$('.overlay-card').removeClass('scaleUp');

					$(one_card).children('.you-own').show();
					$('.overlay-card .flex-container').show();
					$('.overlay-card .info-card').show();
				}
			);
			$('.overlay-card').show('fast');

			$('.overlay-card').hover(
				function() {
					//console.log("Tu es sur l'overlay !");
				}, 
				function() {
					//console.log("Tu quittes l'overlay !");
					$(this).hide('fast', function() {
						$(this).remove();
					});
				}
			);
		}, 
		function() {
			
		}
	);

	/* ===========================================================================
	* 								 A J A X - SEARCH
	* ============================================================================*/
	$('.form-search form').on('submit', function (event) {
        event.preventDefault();

        let location = window.location.origin;
        let route = "";
        let quantity = $("input", this).val();
        let data = { quantity: quantity };
		let card_id = $('.search-result .one-card').data('id');

        switch (this.className) {
            case "add-wishlist":
                route = "/Users/wishlists/add/"+card_id;
                break;
            case "add-collection":
                route = "/Users/collection/add/"+card_id;
                break;
            default:
                console.log("Euh...")
        }
        console.log("La route appellée est :"+route);
        let message;
        let removed;
        let new_quantity;
        let target = location+route;

        $.post(target, data, function( data ) {
            console.log(data);
            message = data.message;
            removed = data.removed;
            new_quantity = data.quantity;
        })
            .done(function () {
                ajax_response(message, removed, new_quantity, null);
            })
            .fail(function () {

            })
            .always(function () {

            });
    });
	/* ======================================================================== */
	/* Creation de formulaire */
	function formCreation (float, card_data, size, carousel) {
		let ret_val;
		let wishlist_form;
		let collection_form;
		let form_container;
		/* Modification du formulaire en fonction de 
		la taille des cartes choisies par l'utiliateur */
		switch (carousel) {
  			case 1: // carousel des extensions
    			switch (size) {
		  			case 1:
		    			wishlist_form = `
			            <h3>Wishlist</h3>
		                <div class="form-group">
		                    <div>
		                        <input type="number" name="remove" value="1" min="1" max="99">
		                    </div>
		                    <div>
		                        <button class="btn-wishlist" type="submit">+</button>
		                    </div>
		                </div>`;

		            	collection_form = `
		                <h3>Collection</h3>
		                <div class="form-group">
		                    <div>
		                        <input type="number" name="remove" value="1" min="1" max="99">
		                    </div>
		                    <div>
		                        <button class="btn-collection" type="submit">+</button>
		                    </div>
		                </div>`;
		  				break;
		  			case 2:
		  				wishlist_form = `
			            <h3>Add to Wishlist</h3>
		                <div class="form-group">
		                    <div>
		                        <input type="number" name="remove" value="1" min="1" max="99">
		                    </div>
		                    <div>
		                        <button class="btn-wishlist" type="submit">Add</button>
		                    </div>
		                </div>`;

		            	collection_form = `
		                <h3>Add to collection</h3>
		                <div class="form-group">
		                    <div>
		                        <input type="number" name="remove" value="1" min="1" max="99">
		                    </div>
		                    <div>
		                        <button class="btn-collection" type="submit">Add</button>
		                    </div>
		                </div>`;
		  				break;
		  			case 3:
		  				wishlist_form = `
			            <h3>Add to Wishlist</h3>
		                <div class="form-group">
		                    <div>
		                        <label>Quantité</label>
		                    </div>
		                    <div>
		                        <input type="number" name="remove" value="1" min="1" max="99">
		                    </div>
		                </div>
		                <button class="btn-wishlist" type="submit">Ajouter</button>`;

		            	collection_form = `
			            <h3>Add to Collection</h3>
		                <div class="form-group">
		                    <div>
		                        <label>Quantité</label>
		                    </div>
		                    <div>
		                        <input type="number" name="remove" value="1" min="1" max="99">
		                    </div>
		                </div>
		                <button class="btn-collection" type="submit">Ajouter</button>`;
		    			break;
		  			default:
				}
				form_container = 
				`<form class="add-wishlist">`
					+wishlist_form+
				`</form>
				<form class="add-collection">`
					+collection_form+
				`</form>`;
    			break;
  			case 2: // carousel de la collection
  				form_container = `
  				<form class="add-collection">
					<h3>Add</h3>
		            <div class="form-group">
		                <div>
		                    <input type="number" name="remove" value="1" min="1" max="99">
		                </div>
		                <div>
		                    <button type="submit">
		                    	<i class="fa fa-plus" aria-hidden="true"></i>
							</button>
		                </div>
		            </div>
				</form>
				<form class="remove-collection">
					<h3>Remove</h3>
		            <div class="form-group">
		                <div>
		                    <input type="number" name="remove" value="1" min="1" max="99">
		                </div>
		                <div>
		                    <button type="submit">
		                    	<i class="fa fa-minus" aria-hidden="true"></i>
		                    </button>
		                </div>
		            </div>
				</form>`;
  				break;
  			case 3: // carousel des wishlists
				form_container = `
  				<form class="add-wishlist">
					<h3>Add</h3>
		            <div class="form-group">
		                <div>
		                    <input type="number" name="remove" value="1" min="1" max="99">
		                </div>
		                <div>
		                    <button type="submit">
		                    	<i class="fa fa-plus" aria-hidden="true"></i>
							</button>
		                </div>
		            </div>
				</form>
				<form class="remove-wishlist">
					<h3>Remove</h3>
		            <div class="form-group">
		                <div>
		                    <input type="number" name="remove" value="1" min="1" max="99">
		                </div>
		                <div>
		                    <button type="submit">
		                    	<i class="fa fa-minus" aria-hidden="true"></i>
		                    </button>
		                </div>
		            </div>
				</form>`;
    			break;
  			default:
		}
		

		ret_val = `
		<div class="overlay-container `+float+`">

			<div class="flex-container">
				<div class="top-of-card">
					<a href="`+card_data.ext+`/`+card_data.id+`" class="stretched-link"></a>
				</div>
				<div class="form-container">`
					+form_container+
		    	`</div>
	    	</div>

	    	<table class="info-card">
			    <tr>
			      	<th>Titre :</th>
			        <td>`+card_data.title+`</td>
			    </tr>
			    <tr>
			    	<th>Couleur :</th>
			    	<td>`+card_data.color+`</td>
			    </tr>
			    <tr>
			        <th>Type :</th>
			        <td>`+card_data.type+`</td>
			    </tr>
			    <tr>
			    	<th>Rareté :</th>
			        <td>`+card_data.rarity+`</td>
			    </tr>
			    <tr>
			    	<th>Numéro :</th>
			        <td>`+card_data.ext_num+`</td>
			    </tr>
			</table>
			<a class="scaler" href="#"><i class="fa fa-search-plus" aria-hidden="true"></i>
			</a>
	    </div>`;
        return ret_val;
	}

	$('#col-selector').change(function () {
		let optionSelected = $("option:selected", this);
		let valueSelected = this.value;
		valueSelected = "/pwd/"+valueSelected;
		$('#col-displayer').attr("href", valueSelected);
	});

	$('#wish-selector').change(function () {
		let optionSelected = $("option:selected", this);
		let valueSelected = this.value;
		valueSelected = "/pwd/"+valueSelected;
		$('#wish-displayer').attr("href", valueSelected);
	});

	$('.owl-carousel').owlCarousel({
	    items:4,
	    lazyLoad:true,
	    loop:true,
	    dots:true,
	    margin:30,
	    responsiveClass:true,
		    responsive:{
		        0:{
		            items:1,
		        },
		        600:{
		            items:1,
		        },
		        1000:{
		            items:1,
		        }
		    }
	});

	var	$window = $(window),
		$head = $('head'),
		$body = $('body');

	// Breakpoints.
		breakpoints({
			xlarge:   [ '1281px',  '1680px' ],
			large:    [ '981px',   '1280px' ],
			medium:   [ '737px',   '980px'  ],
			small:    [ '481px',   '736px'  ],
			xsmall:   [ '361px',   '480px'  ],
			xxsmall:  [ null,      '360px'  ],
			'xlarge-to-max':    '(min-width: 1681px)',
			'small-to-xlarge':  '(min-width: 481px) and (max-width: 1680px)'
		});

	// Stops animations/transitions until the page has ...

		// ... loaded.
			$window.on('load', function() {
				window.setTimeout(function() {
					$body.removeClass('is-preload');
				}, 100);
			});

		// ... stopped resizing.
			var resizeTimeout;

			$window.on('resize', function() {

				// Mark as resizing.
					$body.addClass('is-resizing');

				// Unmark after delay.
					clearTimeout(resizeTimeout);

					resizeTimeout = setTimeout(function() {
						$body.removeClass('is-resizing');
					}, 100);

			});

	// Fixes.

		// Object fit images.
			if (!browser.canUse('object-fit')
			||	browser.name == 'safari')
				$('.image.object').each(function() {

					var $this = $(this),
						$img = $this.children('img');

					// Hide original image.
						$img.css('opacity', '0');

					// Set background.
						$this
							.css('background-image', 'url("' + $img.attr('src') + '")')
							.css('background-size', $img.css('object-fit') ? $img.css('object-fit') : 'cover')
							.css('background-position', $img.css('object-position') ? $img.css('object-position') : 'center');

				});

	// Sidebar.
		var $sidebar = $('#sidebar'),
			$sidebar_inner = $sidebar.children('.inner');

		// Inactive by default on <= large.
			breakpoints.on('<=large', function() {
				$sidebar.addClass('inactive');
			});

			breakpoints.on('>large', function() {
				$sidebar.removeClass('inactive');
			});

		// Hack: Workaround for Chrome/Android scrollbar position bug.
			if (browser.os == 'android'
			&&	browser.name == 'chrome')
				$('<style>#sidebar .inner::-webkit-scrollbar { display: none; }</style>')
					.appendTo($head);

		// Toggle.
			$('<a href="#sidebar" class="toggle">Toggle</a>')
				.appendTo($sidebar)
				.on('click', function(event) {

					// Prevent default.
						event.preventDefault();
						event.stopPropagation();

					// Toggle.
						$sidebar.toggleClass('inactive');

				});

		// Events.

			// Link clicks.
				$sidebar.on('click', 'a', function(event) {

					// >large? Bail.
						if (breakpoints.active('>large'))
							return;

					// Vars.
						var $a = $(this),
							href = $a.attr('href'),
							target = $a.attr('target');

					// Prevent default.
						event.preventDefault();
						event.stopPropagation();

					// Check URL.
						if (!href || href == '#' || href == '')
							return;

					// Hide sidebar.
						$sidebar.addClass('inactive');

					// Redirect to href.
						setTimeout(function() {

							if (target == '_blank')
								window.open(href);
							else
								window.location.href = href;

						}, 500);

				});

			// Prevent certain events inside the panel from bubbling.
				$sidebar.on('click touchend touchstart touchmove', function(event) {

					// >large? Bail.
						if (breakpoints.active('>large'))
							return;

					// Prevent propagation.
						event.stopPropagation();

				});

			// Hide panel on body click/tap.
				$body.on('click touchend', function(event) {

					// >large? Bail.
						if (breakpoints.active('>large'))
							return;

					// Deactivate.
						$sidebar.addClass('inactive');

				});

		// Scroll lock.
		// Note: If you do anything to change the height of the sidebar's content, be sure to
		// trigger 'resize.sidebar-lock' on $window so stuff doesn't get out of sync.

			$window.on('load.sidebar-lock', function() {

				var sh, wh, st;

				// Reset scroll position to 0 if it's 1.
					if ($window.scrollTop() == 1)
						$window.scrollTop(0);

				$window
					.on('scroll.sidebar-lock', function() {

						var x, y;

						// <=large? Bail.
							if (breakpoints.active('<=large')) {

								$sidebar_inner
									.data('locked', 0)
									.css('position', '')
									.css('top', '');

								return;

							}

						// Calculate positions.
							x = Math.max(sh - wh, 0);
							y = Math.max(0, $window.scrollTop() - x);

						// Lock/unlock.
							if ($sidebar_inner.data('locked') == 1) {

								if (y <= 0)
									$sidebar_inner
										.data('locked', 0)
										.css('position', '')
										.css('top', '');
								else
									$sidebar_inner
										.css('top', -1 * x);

							}
							else {

								if (y > 0)
									$sidebar_inner
										.data('locked', 1)
										.css('position', 'fixed')
										.css('top', -1 * x);

							}

					})
					.on('resize.sidebar-lock', function() {

						// Calculate heights.
							wh = $window.height();
							sh = $sidebar_inner.outerHeight() + 30;

						// Trigger scroll.
							$window.trigger('scroll.sidebar-lock');

					})
					.trigger('resize.sidebar-lock');

				});

	// Menu.
		var $menu = $('#menu'),
			$menu_openers = $menu.children('ul').find('.opener');

		// Openers.
			$menu_openers.each(function() {

				var $this = $(this);

				$this.on('click', function(event) {

					// Prevent default.
						event.preventDefault();

					// Toggle.
						$menu_openers.not($this).removeClass('active');
						$this.toggleClass('active');

					// Trigger resize (sidebar lock).
						$window.triggerHandler('resize.sidebar-lock');

				});

			});

})(jQuery);