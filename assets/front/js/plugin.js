/* 

1. way poin js
2. Counter js
3. owl carousel
4. easing js
5. meanmenu js
6. pricing-range


*/

/*!
 * jquery.counterup.js 1.0
 *
 * Copyright 2013, Benjamin Intal http://gambit.ph @bfintal
 * Released under the GPL v2 License
 *
 * Date: Nov 26, 2013
 */ (function (e) {
	"use strict";
	e.fn.counterUp = function (t) {
		var n = e.extend({ time: 400, delay: 10 }, t);
		return this.each(function () {
			var t = e(this),
				r = n,
				i = function () {
					var e = [],
						n = r.time / r.delay,
						i = t.text(),
						s = /[0-9]+,[0-9]+/.test(i);
					i = i.replace(/,/g, "");
					var o = /^[0-9]+$/.test(i),
						u = /^[0-9]+\.[0-9]+$/.test(i),
						a = u ? (i.split(".")[1] || []).length : 0;
					for (var f = n; f >= 1; f--) {
						var l = parseInt((i / n) * f);
						u && (l = parseFloat((i / n) * f).toFixed(a));
						if (s)
							while (/(\d+)(\d{3})/.test(l.toString()))
								l = l.toString().replace(/(\d+)(\d{3})/, "$1,$2");
						e.unshift(l);
					}
					t.data("counterup-nums", e);
					t.text("0");
					var c = function () {
						t.text(t.data("counterup-nums").shift());
						if (t.data("counterup-nums").length)
							setTimeout(t.data("counterup-func"), r.delay);
						else {
							delete t.data("counterup-nums");
							t.data("counterup-nums", null);
							t.data("counterup-func", null);
						}
					};
					t.data("counterup-func", c);
					setTimeout(t.data("counterup-func"), r.delay);
				};
			t.waypoint(i, { offset: "100%", triggerOnce: !0 });
		});
	};
})(jQuery);

// AMD support (Thanks to @FagnerMartinsBrack)
(function (factory) {
	"use strict";

	if (typeof define === "function" && define.amd) {
		define(["jquery"], factory);
	} else {
		factory(jQuery);
	}
})(function ($) {
	"use strict";

	var instances = [],
		matchers = [],
		defaultOptions = {
			precision: 100, // 0.1 seconds, used to update the DOM
			elapse: false,
			defer: false,
		};
	// Miliseconds
	matchers.push(/^[0-9]*$/.source);
	// Month/Day/Year [hours:minutes:seconds]
	matchers.push(
		/([0-9]{1,2}\/){2}[0-9]{4}( [0-9]{1,2}(:[0-9]{2}){2})?/.source
	);
	// Year/Day/Month [hours:minutes:seconds] and
	// Year-Day-Month [hours:minutes:seconds]
	matchers.push(
		/[0-9]{4}([\/\-][0-9]{1,2}){2}( [0-9]{1,2}(:[0-9]{2}){2})?/.source
	);
	// Cast the matchers to a regular expression object
	matchers = new RegExp(matchers.join("|"));
	// Parse a Date formatted has String to a native object
	function parseDateString(dateString) {
		// Pass through when a native object is sent
		if (dateString instanceof Date) {
			return dateString;
		}
		// Caste string to date object
		if (String(dateString).match(matchers)) {
			// If looks like a milisecond value cast to number before
			// final casting (Thanks to @msigley)
			if (String(dateString).match(/^[0-9]*$/)) {
				dateString = Number(dateString);
			}
			// Replace dashes to slashes
			if (String(dateString).match(/\-/)) {
				dateString = String(dateString).replace(/\-/g, "/");
			}
			return new Date(dateString);
		} else {
			throw new Error(
				"Couldn't cast `" + dateString + "` to a date object."
			);
		}
	}
	// Map to convert from a directive to offset object property
	var DIRECTIVE_KEY_MAP = {
		Y: "years",
		m: "months",
		n: "daysToMonth",
		d: "daysToWeek",
		w: "weeks",
		W: "weeksToMonth",
		H: "hours",
		M: "minutes",
		S: "seconds",
		D: "totalDays",
		I: "totalHours",
		N: "totalMinutes",
		T: "totalSeconds",
	};
	// Returns an escaped regexp from the string
	function escapedRegExp(str) {
		var sanitize = str.toString().replace(/([.?*+^$[\]\\(){}|-])/g, "\\$1");
		return new RegExp(sanitize);
	}
	// Time string formatter
	function strftime(offsetObject) {
		return function (format) {
			var directives = format.match(/%(-|!)?[A-Z]{1}(:[^;]+;)?/gi);
			if (directives) {
				for (var i = 0, len = directives.length; i < len; ++i) {
					var directive = directives[i].match(
							/%(-|!)?([a-zA-Z]{1})(:[^;]+;)?/
						),
						regexp = escapedRegExp(directive[0]),
						modifier = directive[1] || "",
						plural = directive[3] || "",
						value = null;
					// Get the key
					directive = directive[2];
					// Swap shot-versions directives
					if (DIRECTIVE_KEY_MAP.hasOwnProperty(directive)) {
						value = DIRECTIVE_KEY_MAP[directive];
						value = Number(offsetObject[value]);
					}
					if (value !== null) {
						// Pluralize
						if (modifier === "!") {
							value = pluralize(plural, value);
						}
						// Add zero-padding
						if (modifier === "") {
							if (value < 10) {
								value = "0" + value.toString();
							}
						}
						// Replace the directive
						format = format.replace(regexp, value.toString());
					}
				}
			}
			format = format.replace(/%%/, "%");
			return format;
		};
	}
	// Pluralize
	function pluralize(format, count) {
		var plural = "s",
			singular = "";
		if (format) {
			format = format.replace(/(:|;|\s)/gi, "").split(/\,/);
			if (format.length === 1) {
				plural = format[0];
			} else {
				singular = format[0];
				plural = format[1];
			}
		}
		// Fix #187
		if (Math.abs(count) > 1) {
			return plural;
		} else {
			return singular;
		}
	}
	// The Final Countdown
	var Countdown = function (el, finalDate, options) {
		this.el = el;
		this.$el = $(el);
		this.interval = null;
		this.offset = {};
		this.options = $.extend({}, defaultOptions);
		// This helper variable is necessary to mimick the previous check for an
		// event listener on this.$el. Because of the event loop there might not
		// be a registered event listener during the first tick. In order to work
		// as expected a second tick is necessary, so that the events can be fired
		// and handled properly.
		this.firstTick = true;
		// Register this instance
		this.instanceNumber = instances.length;
		instances.push(this);
		// Save the reference
		this.$el.data("countdown-instance", this.instanceNumber);
		// Handle options or callback
		if (options) {
			// Register the callbacks when supplied
			if (typeof options === "function") {
				this.$el.on("update.countdown", options);
				this.$el.on("stoped.countdown", options);
				this.$el.on("finish.countdown", options);
			} else {
				this.options = $.extend({}, defaultOptions, options);
			}
		}
		// Set the final date and start
		this.setFinalDate(finalDate);
		// Starts the countdown automatically unless it's defered,
		// Issue #198
		if (this.options.defer === false) {
			this.start();
		}
	};
	$.extend(Countdown.prototype, {
		start: function () {
			if (this.interval !== null) {
				clearInterval(this.interval);
			}
			var self = this;
			this.update();
			this.interval = setInterval(function () {
				self.update.call(self);
			}, this.options.precision);
		},
		stop: function () {
			clearInterval(this.interval);
			this.interval = null;
			this.dispatchEvent("stoped");
		},
		toggle: function () {
			if (this.interval) {
				this.stop();
			} else {
				this.start();
			}
		},
		pause: function () {
			this.stop();
		},
		resume: function () {
			this.start();
		},
		remove: function () {
			this.stop.call(this);
			instances[this.instanceNumber] = null;
			// Reset the countdown instance under data attr (Thanks to @assiotis)
			delete this.$el.data().countdownInstance;
		},
		setFinalDate: function (value) {
			this.finalDate = parseDateString(value); // Cast the given date
		},
		update: function () {
			// Stop if dom is not in the html (Thanks to @dleavitt)
			if (this.$el.closest("html").length === 0) {
				this.remove();
				return;
			}
			var now = new Date(),
				newTotalSecsLeft;
			// Create an offset date object
			newTotalSecsLeft = this.finalDate.getTime() - now.getTime(); // Millisecs
			// Calculate the remaining time
			newTotalSecsLeft = Math.ceil(newTotalSecsLeft / 1000); // Secs
			// If is not have to elapse set the finish
			newTotalSecsLeft =
				!this.options.elapse && newTotalSecsLeft < 0
					? 0
					: Math.abs(newTotalSecsLeft);
			// Do not proceed to calculation if the seconds have not changed or
			// during the first tick
			if (this.totalSecsLeft === newTotalSecsLeft || this.firstTick) {
				this.firstTick = false;
				return;
			} else {
				this.totalSecsLeft = newTotalSecsLeft;
			}
			// Check if the countdown has elapsed
			this.elapsed = now >= this.finalDate;
			// Calculate the offsets
			this.offset = {
				seconds: this.totalSecsLeft % 60,
				minutes: Math.floor(this.totalSecsLeft / 60) % 60,
				hours: Math.floor(this.totalSecsLeft / 60 / 60) % 24,
				days: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7,
				daysToWeek: Math.floor(this.totalSecsLeft / 60 / 60 / 24) % 7,
				daysToMonth: Math.floor(
					(this.totalSecsLeft / 60 / 60 / 24) % 30.4368
				),
				weeks: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7),
				weeksToMonth: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 7) % 4,
				months: Math.floor(this.totalSecsLeft / 60 / 60 / 24 / 30.4368),
				years: Math.abs(this.finalDate.getFullYear() - now.getFullYear()),
				totalDays: Math.floor(this.totalSecsLeft / 60 / 60 / 24),
				totalHours: Math.floor(this.totalSecsLeft / 60 / 60),
				totalMinutes: Math.floor(this.totalSecsLeft / 60),
				totalSeconds: this.totalSecsLeft,
			};
			// Dispatch an event
			if (!this.options.elapse && this.totalSecsLeft === 0) {
				this.stop();
				this.dispatchEvent("finish");
			} else {
				this.dispatchEvent("update");
			}
		},
		dispatchEvent: function (eventName) {
			var event = $.Event(eventName + ".countdown");
			event.finalDate = this.finalDate;
			event.elapsed = this.elapsed;
			event.offset = $.extend({}, this.offset);
			event.strftime = strftime(this.offset);
			this.$el.trigger(event);
		},
	});
	// Register the jQuery selector actions
	$.fn.countdown = function () {
		var argumentsArray = Array.prototype.slice.call(arguments, 0);
		return this.each(function () {
			// If no data was set, jQuery.data returns undefined
			var instanceNumber = $(this).data("countdown-instance");
			// Verify if we already have a countdown for this node ...
			// Fix issue #22 (Thanks to @romanbsd)
			if (instanceNumber !== undefined) {
				var instance = instances[instanceNumber],
					method = argumentsArray[0];
				// If method exists in the prototype execute
				if (Countdown.prototype.hasOwnProperty(method)) {
					instance[method].apply(instance, argumentsArray.slice(1));
					// If method look like a date try to set a new final date
				} else if (String(method).match(/^[$A-Z_][0-9A-Z_$]*$/i) === null) {
					instance.setFinalDate.call(instance, method);
					// Allow plugin to restart after finished
					// Fix issue #38 (thanks to @yaoazhen)
					instance.start();
				} else {
					$.error(
						"Method %s does not exist on jQuery.countdown".replace(
							/\%s/gi,
							method
						)
					);
				}
			} else {
				// ... if not we create an instance
				new Countdown(this, argumentsArray[0], argumentsArray[1]);
			}
		});
	};
});

// jquery easing
!(function (n) {
	"function" == typeof define && define.amd
		? define(["jquery"], function (e) {
				return n(e);
		  })
		: "object" == typeof module && "object" == typeof module.exports
		? (exports = n(require("jquery")))
		: n(jQuery);
})(function (n) {
	function e(n) {
		var e = 7.5625,
			t = 2.75;
		return n < 1 / t
			? e * n * n
			: n < 2 / t
			? e * (n -= 1.5 / t) * n + 0.75
			: n < 2.5 / t
			? e * (n -= 2.25 / t) * n + 0.9375
			: e * (n -= 2.625 / t) * n + 0.984375;
	}
	void 0 !== n.easing && (n.easing.jswing = n.easing.swing);
	var t = Math.pow,
		u = Math.sqrt,
		r = Math.sin,
		i = Math.cos,
		a = Math.PI,
		c = 1.70158,
		o = 1.525 * c,
		s = (2 * a) / 3,
		f = (2 * a) / 4.5;
	n.extend(n.easing, {
		def: "easeOutQuad",
		swing: function (e) {
			return n.easing[n.easing.def](e);
		},
		easeInQuad: function (n) {
			return n * n;
		},
		easeOutQuad: function (n) {
			return 1 - (1 - n) * (1 - n);
		},
		easeInOutQuad: function (n) {
			return n < 0.5 ? 2 * n * n : 1 - t(-2 * n + 2, 2) / 2;
		},
		easeInCubic: function (n) {
			return n * n * n;
		},
		easeOutCubic: function (n) {
			return 1 - t(1 - n, 3);
		},
		easeInOutCubic: function (n) {
			return n < 0.5 ? 4 * n * n * n : 1 - t(-2 * n + 2, 3) / 2;
		},
		easeInQuart: function (n) {
			return n * n * n * n;
		},
		easeOutQuart: function (n) {
			return 1 - t(1 - n, 4);
		},
		easeInOutQuart: function (n) {
			return n < 0.5 ? 8 * n * n * n * n : 1 - t(-2 * n + 2, 4) / 2;
		},
		easeInQuint: function (n) {
			return n * n * n * n * n;
		},
		easeOutQuint: function (n) {
			return 1 - t(1 - n, 5);
		},
		easeInOutQuint: function (n) {
			return n < 0.5 ? 16 * n * n * n * n * n : 1 - t(-2 * n + 2, 5) / 2;
		},
		easeInSine: function (n) {
			return 1 - i((n * a) / 2);
		},
		easeOutSine: function (n) {
			return r((n * a) / 2);
		},
		easeInOutSine: function (n) {
			return -(i(a * n) - 1) / 2;
		},
		easeInExpo: function (n) {
			return 0 === n ? 0 : t(2, 10 * n - 10);
		},
		easeOutExpo: function (n) {
			return 1 === n ? 1 : 1 - t(2, -10 * n);
		},
		easeInOutExpo: function (n) {
			return 0 === n
				? 0
				: 1 === n
				? 1
				: n < 0.5
				? t(2, 20 * n - 10) / 2
				: (2 - t(2, -20 * n + 10)) / 2;
		},
		easeInCirc: function (n) {
			return 1 - u(1 - t(n, 2));
		},
		easeOutCirc: function (n) {
			return u(1 - t(n - 1, 2));
		},
		easeInOutCirc: function (n) {
			return n < 0.5
				? (1 - u(1 - t(2 * n, 2))) / 2
				: (u(1 - t(-2 * n + 2, 2)) + 1) / 2;
		},
		easeInElastic: function (n) {
			return 0 === n
				? 0
				: 1 === n
				? 1
				: -t(2, 10 * n - 10) * r((10 * n - 10.75) * s);
		},
		easeOutElastic: function (n) {
			return 0 === n
				? 0
				: 1 === n
				? 1
				: t(2, -10 * n) * r((10 * n - 0.75) * s) + 1;
		},
		easeInOutElastic: function (n) {
			return 0 === n
				? 0
				: 1 === n
				? 1
				: n < 0.5
				? -(t(2, 20 * n - 10) * r((20 * n - 11.125) * f)) / 2
				: (t(2, -20 * n + 10) * r((20 * n - 11.125) * f)) / 2 + 1;
		},
		easeInBack: function (n) {
			return (c + 1) * n * n * n - c * n * n;
		},
		easeOutBack: function (n) {
			return 1 + (c + 1) * t(n - 1, 3) + c * t(n - 1, 2);
		},
		easeInOutBack: function (n) {
			return n < 0.5
				? (t(2 * n, 2) * (7.189819 * n - o)) / 2
				: (t(2 * n - 2, 2) * ((o + 1) * (2 * n - 2) + o) + 2) / 2;
		},
		easeInBounce: function (n) {
			return 1 - e(1 - n);
		},
		easeOutBounce: e,
		easeInOutBounce: function (n) {
			return n < 0.5 ? (1 - e(1 - 2 * n)) / 2 : (1 + e(2 * n - 1)) / 2;
		},
	});
});

/*    01. Mean Menu    */
!(function ($) {
	"use strict";
	$.fn.meanmenu = function (e) {
		var n = {
			meanMenuTarget: jQuery(this),
			meanMenuContainer: "body",
			meanMenuClose: "X",
			meanMenuCloseSize: "18px",
			meanMenuOpen: "<span /><span /><span />",
			meanRevealPosition: "right",
			meanRevealPositionDistance: "0",
			meanRevealColour: "",
			meanScreenWidth: "480",
			meanNavPush: "",
			meanShowChildren: !0,
			meanExpandableChildren: !0,
			meanExpand: "+",
			meanContract: "-",
			meanRemoveAttrs: !1,
			onePage: !1,
			meanDisplay: "block",
			removeElements: "",
		};
		e = $.extend(n, e);
		var a = window.innerWidth || document.documentElement.clientWidth;
		return this.each(function () {
			var n = e.meanMenuTarget,
				t = e.meanMenuContainer,
				r = e.meanMenuClose,
				i = e.meanMenuCloseSize,
				s = e.meanMenuOpen,
				u = e.meanRevealPosition,
				m = e.meanRevealPositionDistance,
				l = e.meanRevealColour,
				o = e.meanScreenWidth,
				c = e.meanNavPush,
				v = ".meanmenu-reveal",
				h = e.meanShowChildren,
				d = e.meanExpandableChildren,
				y = e.meanExpand,
				j = e.meanContract,
				Q = e.meanRemoveAttrs,
				f = e.onePage,
				g = e.meanDisplay,
				p = e.removeElements,
				C = !1;
			(navigator.userAgent.match(/iPhone/i) ||
				navigator.userAgent.match(/iPod/i) ||
				navigator.userAgent.match(/iPad/i) ||
				navigator.userAgent.match(/Android/i) ||
				navigator.userAgent.match(/Blackberry/i) ||
				navigator.userAgent.match(/Windows Phone/i)) &&
				(C = !0),
				(navigator.userAgent.match(/MSIE 8/i) ||
					navigator.userAgent.match(/MSIE 7/i)) &&
					jQuery("html").css("overflow-y", "scroll");
			var w = "",
				x = function () {
					if ("center" === u) {
						var e =
								window.innerWidth ||
								document.documentElement.clientWidth,
							n = e / 2 - 22 + "px";
						(w = "left:" + n + ";right:auto;"),
							C
								? jQuery(".meanmenu-reveal").animate({ left: n })
								: jQuery(".meanmenu-reveal").css("left", n);
					}
				},
				A = !1,
				E = !1;
			"right" === u && (w = "right:" + m + ";left:auto;"),
				"left" === u && (w = "left:" + m + ";right:auto;"),
				x();
			var M = "",
				P = function () {
					M.html(jQuery(M).is(".meanmenu-reveal.meanclose") ? r : s);
				},
				W = function () {
					jQuery(".mean-bar,.mean-push").remove(),
						jQuery(t).removeClass("mean-container"),
						jQuery(n).css("display", g),
						(A = !1),
						(E = !1),
						jQuery(p).removeClass("mean-remove");
				},
				b = function () {
					var e = "background:" + l + ";color:" + l + ";" + w;
					if (o >= a) {
						jQuery(p).addClass("mean-remove"),
							(E = !0),
							jQuery(t).addClass("mean-container"),
							jQuery(".mean-container").prepend(
								'<div class="mean-bar"><a href="#nav" class="meanmenu-reveal" style="' +
									e +
									'">Show Navigation</a><nav class="mean-nav"></nav></div>'
							);
						var r = jQuery(n).html();
						jQuery(".mean-nav").html(r),
							Q &&
								jQuery("nav.mean-nav ul, nav.mean-nav ul *").each(
									function () {
										jQuery(this).is(".mean-remove")
											? jQuery(this).attr("class", "mean-remove")
											: jQuery(this).removeAttr("class"),
											jQuery(this).removeAttr("id");
									}
								),
							jQuery(n).before('<div class="mean-push" />'),
							jQuery(".mean-push").css("margin-top", c),
							jQuery(n).hide(),
							jQuery(".meanmenu-reveal").show(),
							jQuery(v).html(s),
							(M = jQuery(v)),
							jQuery(".mean-nav ul").hide(),
							h
								? d
									? (jQuery(".mean-nav ul ul").each(function () {
											jQuery(this).children().length &&
												jQuery(this, "li:first")
													.parent()
													.append(
														'<a class="mean-expand" href="#" style="font-size: ' +
															i +
															'">' +
															y +
															"</a>"
													);
									  }),
									  jQuery(".mean-expand").on("click", function (e) {
											e.preventDefault(),
												jQuery(this).hasClass("mean-clicked")
													? (jQuery(this).text(y),
													  jQuery(this)
															.prev("ul")
															.slideUp(300, function () {}))
													: (jQuery(this).text(j),
													  jQuery(this)
															.prev("ul")
															.slideDown(300, function () {})),
												jQuery(this).toggleClass("mean-clicked");
									  }))
									: jQuery(".mean-nav ul ul").show()
								: jQuery(".mean-nav ul ul").hide(),
							jQuery(".mean-nav ul li").last().addClass("mean-last"),
							M.removeClass("meanclose"),
							jQuery(M).click(function (e) {
								e.preventDefault(),
									A === !1
										? (M.css("text-align", "center"),
										  M.css("text-indent", "0"),
										  M.css("font-size", i),
										  jQuery(".mean-nav ul:first").slideDown(),
										  (A = !0))
										: (jQuery(".mean-nav ul:first").slideUp(),
										  (A = !1)),
									M.toggleClass("meanclose"),
									P(),
									jQuery(p).addClass("mean-remove");
							}),
							f &&
								jQuery(".mean-nav ul > li > a:first-child").on(
									"click",
									function () {
										jQuery(".mean-nav ul:first").slideUp(),
											(A = !1),
											jQuery(M).toggleClass("meanclose").html(s);
									}
								);
					} else W();
				};
			C ||
				jQuery(window).resize(function () {
					(a = window.innerWidth || document.documentElement.clientWidth),
						a > o,
						W(),
						o >= a ? (b(), x()) : W();
				}),
				jQuery(window).resize(function () {
					(a = window.innerWidth || document.documentElement.clientWidth),
						C
							? (x(), o >= a ? E === !1 && b() : W())
							: (W(), o >= a && (b(), x()));
				}),
				b();
		});
	};
})(jQuery);

// pricing range

$(document).ready(function () {
	$("#price-range-submit").hide();

	$("#min_price,#max_price").on("change", function () {
		$("#price-range-submit").show();

		var min_price_range = parseInt($("#min_price").val());

		var max_price_range = parseInt($("#max_price").val());

		if (min_price_range > max_price_range) {
			$("#max_price").val(min_price_range);
		}

		$("#slider-range").slider({
			values: [min_price_range, max_price_range],
		});
	});

	$("#min_price,#max_price").on("paste keyup", function () {
		$("#price-range-submit").show();

		var min_price_range = parseInt($("#min_price").val());

		var max_price_range = parseInt($("#max_price").val());

		if (min_price_range == max_price_range) {
			max_price_range = min_price_range + 100;

			$("#min_price").val(min_price_range);
			$("#max_price").val(max_price_range);
		}

		$("#slider-range").slider({
			values: [min_price_range, max_price_range],
		});
	});

	$("#slider-range,#price-range-submit").click(function () {
		var min_price = $("#min_price").val();
		var max_price = $("#max_price").val();

		$("#searchResults").text(
			"Here List of products will be shown which are cost between " +
				min_price +
				" " +
				"and" +
				" " +
				max_price +
				"."
		);
	});
});
