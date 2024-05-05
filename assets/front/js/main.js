(function ($) {
	"use strict";

	$(document).ready(function () {
		//**************************** WISHLIST SECTION ****************************************

		$(".dashboard-sidebar-btn").on("click", function () {
			$("#sidebar, .dashboard-overlay").addClass("active");
		});

		$(".dashboard-overlay, .dashbaord-sidebar-close").on(
			"click",
			function () {
				$("#sidebar, .dashboard-overlay").removeClass("active");
			}
		);

		$(document).on("click", ".new", function (e) {
			e.preventDefault();

			if ($(this).data("href")) {
				$.get($(this).data("href"), function (data) {
					if (data[0] == 1) {
						toastr.success(data["success"]);
						$("#wishlist-count").html(data[1]);
						$("#wishlist-count1").html(data[1]);
					} else {
						toastr.error(data["error"]);
					}
				});
			}
		});

		$("#registerform").on("submit", function (e) {
			var $this = $(this).parent();
			e.preventDefault();
			$this.find("button.submit-btn").prop("disabled", true);
			$this.find(".alert-info").show();
			$this.find(".alert-info p").html($("#processdata").val());
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				dataType: "JSON",
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data == 1) {
						window.location = mainurl + "/user/dashboard";
					} else {
						if (data.errors) {
							$this.find(".alert-success").hide();
							$this.find(".alert-info").hide();
							$this.find(".alert-danger").addClass("d-flex");
							$this.find(".alert-danger").show();
							$this.find(".alert-danger ul").html("");
							for (var error in data.errors) {
								$this.find(".alert-danger p").html(data.errors[error]);
							}
							$this.find("button.submit-btn").prop("disabled", false);
						} else {
							$this.find(".alert-info").hide();
							$this.find(".alert-danger").hide();
							$this.find(".alert-success").addClass("d-flex");
							$this.find(".alert-success").show();
							$this.find(".alert-success p").html(data);
							$this.find("button.submit-btn").prop("disabled", false);
						}
					}
					$(".refresh_code").click();
				},
			});
		});

		$("#reportform").on("submit", function (e) {
			e.preventDefault();
			$(".gocover").show();
			var $reportform = $(this);
			$reportform.find("button.submit-btn").prop("disabled", true);
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				dataType: "JSON",
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data.errors) {
						for (var error in data.errors) {
							$reportform.find(".alert-danger").show();
							$reportform
								.find(".alert-danger p")
								.html(data.errors[error]);
						}
					} else {
						$reportform.find("input[type=text],textarea").val("");

						$("#report-modal").modal("hide");
						toastr.success("Report Submitted Successfully.");
					}

					$(".gocover").hide();
					$reportform.find("button.submit-btn").prop("disabled", false);
				},
			});
		});

		// REPORT FORM ENDS

		$(document).on("change", ".product-attr", function () {
			var total = 0;
			total = getAmount() + getSizePrice();
			total = total.toFixed(2);
			var pos = $("#curr_pos").val();
			var sign = $("#curr_sign").val();
			if (pos == "0") {
				$("#sizeprice").html(sign + total);
			} else {
				$("#sizeprice").html(total + sign);
			}
		});

		// Date Counting
		$("[data-countdown]").each(function () {
			var $this = $(this),
				finalDate = $(this).data("countdown");

			$this.countdown(finalDate, function (event) {
				$this.html(
					event.strftime(
						"<ul><li><span>%D</span><span>Day</span></li> <li><span>%H</span><span>Hour</span></li> <li><span>%M</span><span>Min</span></li> <li><span>%S</span><span>Sec</span></li></ul>"
					)
				);
			});
		});

		function number_format(number, decimals, dec_point, thousands_sep) {
			// Strip all characters but numerical ones.
			number = (number + "").replace(/[^0-9+\-Ee.]/g, "");
			var n = !isFinite(+number) ? 0 : +number,
				prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
				sep = typeof thousands_sep === "undefined" ? "," : thousands_sep,
				dec = typeof dec_point === "undefined" ? "." : dec_point,
				s = "",
				toFixedFix = function (n, prec) {
					var k = Math.pow(10, prec);
					return "" + Math.round(n * k) / k;
				};
			// Fix for IE parseFloat(0.55).toFixed(0) = 0;
			s = (prec ? toFixedFix(n, prec) : "" + Math.round(n)).split(".");
			if (s[0].length > 3) {
				s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
			}
			if ((s[1] || "").length < prec) {
				s[1] = s[1] || "";
				s[1] += new Array(prec - s[1].length + 1).join("0");
			}
			return s.join(dec);
		}

		$(document).on("click", ".favorite-prod", function () {
			var $this = $(this);
			$.get($(this).data("href"), function (data) {
				$this.attr("data-href", "");
				$this.html(data["icon"] + " " + data["text"]);
			});
		});

		$("button.alert-close").on("click", function () {
			$(this).parent().addClass("d-none");
		});

		// MODAL LOGIN FORM
		// LOGIN FORM
		$("#loginform").on("submit", function (e) {
			var $this = $(this).parent();
			e.preventDefault();
			$this.find("button.submit-btn").prop("disabled", true);
			$this.find(".alert-info").show();
			$this.find(".alert-info p").html($("#authdata").val());
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				dataType: "JSON",
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data.errors) {
						$this.find(".alert-success").hide();
						$this.find(".alert-info").hide();
						$this.find(".alert-danger").addClass("d-flex");
						$this.find(".alert-danger").show();
						$this.find(".alert-danger ul").html("");
						for (var error in data.errors) {
							$this.find(".alert-danger p").html(data.errors[error]);
						}
					} else {
						$this.find(".alert-info").hide();
						$this.find(".alert-danger").hide();
						$this.find(".alert-success").addClass("d-flex");
						$this.find(".alert-success").show();
						$this.find(".alert-success p").html("Success !");
						if (data == 1) {
							location.reload();
						} else {
							window.location = data;
						}
					}
					$this.find("button.submit-btn").prop("disabled", false);
				},
			});
		});
		// MODAL LOGIN FORM ENDS

		$("#forgotform").on("submit", function (e) {
			e.preventDefault();
			var $this = $(this).parent();
			$this.find("button.submit-btn").prop("disabled", true);
			$this.find(".alert-info").show();
			$this.find(".alert-info p").html($(".authdata").val());
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				dataType: "JSON",
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data.errors) {
						$this.find(".alert-success").hide();
						$this.find(".alert-info").hide();
						$this.find(".alert-danger").show();
						$this.find(".alert-danger ul").html("");
						for (var error in data.errors) {
							$this.find(".alert-danger p").html(data.errors[error]);
						}
					} else {
						$this.find(".alert-info").hide();
						$this.find(".alert-danger").hide();
						$this.find(".alert-success").show();
						$this.find(".alert-success p").html(data);
						$this.find("input[type=email]").val("");
					}
					$this.find("button.submit-btn").prop("disabled", false);
				},
			});
		});

		$("#category_select").on("change", function () {
			var val = $(this).val();
			$("#category_id").val(val);
			$("#searchForm").attr(
				"action",
				mainurl + "/category/" + $(this).val()
			);
		});

		// Catalog Search Options Ends

		// Auto Complete Section
		$("#prod_name2").on("keyup", function () {
			var search = encodeURIComponent($(this).val());
			if (search == "") {
				$(".autocomplete2").hide();
			} else {
				$(".autocomplete2").show();
				$("#myInputautocomplete-list2").load(
					mainurl + "/autosearch/product/" + search
				);
			}
		});
		// Auto Complete Section Ends
		$("#prod_name").on("keyup", function () {
			var search = encodeURIComponent($(this).val());
			if (search == "") {
				$(".autocomplete").hide();
			} else {
				$(".autocomplete").show();
				$("#myInputautocomplete-list").load(
					mainurl + "/autosearch/product/" + search
				);
			}
		});

		$(document).on("click", ".add-cart", function (e) {
			e.preventDefault();
			$.get($(this).data("href"), function (data) {
				if (data == "digital") {
					toastr.error(lang.cart_already);
				} else if (data[0] == 0) {
					toastr.error(lang.cart_out);
				} else {
					$("#cart-count").html(data[0]);
					$("#cart-count1").html(data[0]);
					$("#total-cost").html(data[1]);
					$(".cart-popup").load(mainurl + "/carts/view");

					toastr.success(lang.cart_success);
				}
			});
			return false;
		});

		$(document).on("click", ".wishlist-remove", function (e) {
			e.preventDefault();
			$(this).parent().parent().parent().remove();
			$.get($(this).data("href"), function (data) {
				$("#wishlist-count").html(data[1]);
				$("#wishlist-count1").html(data[1]);
				toastr.success(data["success"]);
				$("table").load(mainurl + "/wishlists");
			});
		});

		// CART OUT OF STOCK

		$(document).on("click", ".cart-out-of-stock", function () {
			return false;
		});

		$(document).on("click", ".affilate-btn", function (e) {
			e.preventDefault();
			window.location = $(this).data("href");
		});

		$(".subscribeform").on("submit", function (e) {
			var $this = $(this);
			e.preventDefault();
			$this.find("input").prop("readonly", true);
			$this.find("button").prop("disabled", true);
			$("#sub-btn").prop("disabled", true);
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data.errors) {
						for (var error in data.errors) {
							toastr.error(data.errors[error]);
						}
					} else {
						toastr.success(data);
						$this.find("input[name=email]").val("");
					}
					$this.find("input").prop("readonly", false);
					$this.find("button").prop("disabled", false);
				},
			});
		});

		// CART REMOVE

		$(document).on("click", ".cart-remove", function () {
			var $selector = $(this).data("class");
			$("." + $selector).hide();
			$.get($(this).data("href"), function (data) {
				if (data[0] == 0) {
					$("#cart-count").html(data[0]);
					$("#cart-count1").html(data[0]);
					$(".cart-table").html(
						'<h3 class="mt-1 pl-3 text-center">' +
							lang.cart_empty +
							"</h3>"
					);
					$(".cart-popup").html(
						'<p class="mt-1 pl-3 text-left">' + lang.cart_empty + "</p>"
					);
					$(".cartpage .col-xl-4").html("");
					$("#total-cost").html(data[1]);
				} else {
					$(".cart-popup").load(mainurl + "/carts/view");
					$(".cartpage").load(mainurl + "/view");

					var currentURL = window.location.href;

					if (currentURL == mainurl + "/carts") {
						$(".full-row").load(mainurl + "/carts");
					}

					$(".cart-quantity").html(data[1]);
					$(".cart-total").html(data[0]);
					$("#total-cost").html(data[0]);
					$(".coupon-total").val(data[0]);
					$(".main-total").html(data[3]);
				}
			});
		});

		// Initialization
		var sizes = "";
		var size_qty =
			$(".product-color .color-list li.active").length > 0
				? parseFloat(
						$(".product-color .color-list li.active")
							.find(".size_qty")
							.val()
				  )
				: "";
		var size_price = "";
		var size_key = "";
		var colors = "";
		var total = "";
		var stock = $("#stock").val();
		var keys = "";
		var values = "";
		var prices = "";

		// Product Details Product Size Active Js Code
		// Product Details Product Size Active Js Code
		$(document).on("click", ".product-size .siz-list .box", function () {
			var parent = $(this).parent();
			sizes = $(this).find("input.size").val();
			size_key = $(this).find("input.size_key").val();
			$(".product-size .siz-list li").removeClass("active");
			parent.addClass("active");
			$(".qttotal").val("1");
			if ($(this).parent().parent().parent().attr("data-key") != "false") {
				$(".product-color .color-list li").removeClass("show-colors");
				var size_color = $(
					".product-color .color-list li." + parent.data("key")
				);
				size_color.addClass("show-colors").first().addClass("active");
				colors = size_color.find("span.box").data("color");
				size_qty = size_color.find(".size_qty").val();
				size_price = size_color.find(".size_price").val();
				size_key = size_color.find(".size_key").val();
				sizes = size_color.find(".size").val();
				total = getAmount() + parseFloat(size_price);
				total = total.toFixed(2);
				stock = size_qty;
				total = number_format(
					total,
					2,
					gs.decimal_separator,
					gs.thousand_separator
				);
				var pos = $("#curr_pos").val();
				var sign = $("#curr_sign").val();
				if (pos == "0") {
					$("#sizeprice").html(sign + total);
				} else {
					$("#sizeprice").html(total + sign);
				}
			}
		});

		function getSizePrice() {
			var total = 0;
			if ($(".product-color .color-list li.active").length > 0) {
				total = parseFloat(
					$(".product-color .color-list li.active")
						.find(".size_price")
						.val()
				);
			}
			return total;
		}

		function getAmount() {
			var total = 0;
			var value = parseFloat($("#product_price").val());
			var datas = $(".product-attr:checked")
				.map(function () {
					return $(this).data("price");
				})
				.get();

			var data;
			for (data in datas) {
				total += parseFloat(datas[data]);
			}
			total += value;
			return total;
		}

		// Product Details Product Color Active Js Code
		$(document).on("click", ".product-color .color-list .box", function () {
			$(".qttotal").val("1");
			colors = $(this).data("color");

			var parent = $(this).parent();
			$(".product-color .color-list li").removeClass("active");
			parent.addClass("active");

			if ($(this).parent().parent().parent().attr("data-key") != "false") {
				size_qty = $(this).find(".size_qty").val();
				size_price = $(this).find(".size_price").val();
				size_key = $(this).find(".size_key").val();
				sizes = $(this).find(".size").val();

				total = getAmount() + parseFloat(size_price);
				total = total.toFixed(2);
				stock = size_qty;

				total = number_format(
					total,
					2,
					gs.decimal_separator,
					gs.thousand_separator
				);

				var pos = $("#curr_pos").val();
				var sign = $("#curr_sign").val();
				if (pos == "0") {
					$("#sizeprice").html(sign + total);
				} else {
					$("#sizeprice").html(total + sign);
				}
			}
		});

		$(document).on("click", ".compare", function (e) {
			e.preventDefault();
			$.get($(this).data("href"), function (data) {
				$("#compare-count").html(data[1]);
				$("#compare-count1").html(data[1]);
				if (data[0] == 0) {
					toastr.success(data["success"]);
				} else {
					toastr.error(data["error"]);
				}
			});
		});

		// COMMENT FORM

		$(document).on("submit", "#comment-form", function (e) {
			e.preventDefault();
			$("#comment-form button[type=submit]").prop("disabled", true);
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					$("#comment-form textarea").val("");
					$(".all-comment").prepend(data);

					$("#comment-form button[type=submit]").prop("disabled", false);
				},
			});
		});

		// COMMENT DELETE
		$(document).on("click", ".comment-delete", function () {
			var count = parseInt($("#comment_count").html());
			count--;
			$("#comment_count").html(count);
			$(this).parent().parent().parent().parent().parent().remove();
			$.get($(this).data("href"));
		});
		// COMMENT DELETE ENDS

		// COMMENT REPLY
		$(document).on("click", ".reply", function () {
			$(this)
				.parent()
				.parent()
				.parent()
				.parent()
				.parent()
				.show()
				.find(".reply-reply-area")
				.removeClass("d-none");
			$(this)
				.parent()
				.parent()
				.parent()
				.parent()
				.parent()
				.show()
				.find(".reply-reply-area .reply-form textarea")
				.focus();
		});
		// COMMENT REPLY ENDS

		// REPLY DELETE
		$(document).on("click", ".reply-delete", function () {
			$(this).parent().parent().parent().parent().remove();
			$.get($(this).data("href"));
		});
		// REPLY DELETE ENDS

		// View Replies
		$(document).on("click", ".view-reply", function () {
			$(this)
				.parent()
				.parent()
				.parent()
				.parent()
				.siblings(".replay-review")
				.toggleClass("d-none");
		});

		$(".cmn-rmv").on("click", function () {
			$(this).parent().parent().addClass("d-none");
		});

		// CANCEL CLICK ENDS

		// COMMENT FORM ENDS
		$(document).on("click", "#addcrt", function () {
			var qty = $(".qttotal").val() ? $(".qttotal").val() : 1;
			var pid = $("#product_id").val();

			if ($(".product-attr").length > 0) {
				values = $(".product-attr:checked")
					.map(function () {
						return $(this).val();
					})
					.get();

				keys = $(".product-attr:checked")
					.map(function () {
						return $(this).data("key");
					})
					.get();

				prices = $(".product-attr:checked")
					.map(function () {
						return $(this).data("price");
					})
					.get();

				if (!isNaN(size_qty)) {
					if (size_qty == "0") {
						toastr.error(lang.cart_out);
						return false;
					}
				} else {
					size_qty = null;
				}
			}

			$.ajax({
				type: "GET",
				url: mainurl + "/addnumcart",
				data: {
					id: pid,
					qty: qty,
					size: sizes,
					color: colors,
					size_qty: size_qty,
					size_price: size_price,
					size_key: size_key,
					keys: keys,
					values: values,
					prices: prices,
				},
				success: function (data) {
					if (data == "digital") {
						toastr.error("Already Added To Cart");
					} else if (data == 0) {
						toastr.error("Out Of Stock");
					} else if (data[3]) {
						toastr.error(lang.minimum_qty_error + " " + data[4]);
					} else {
						$("#cart-count").html(data[0]);
						$("#cart-count1").html(data[0]);
						$(".cart-popup").load(mainurl + "/carts/view");
						$("#cart-items").load(mainurl + "/carts/view");
						toastr.success("Successfully Added To Cart");
					}
				},
			});
		});

		$(document).on("click", "#qaddcrt", function () {
			var qty = $(".qttotal").val();
			var pid = $("#product_id").val();
			

			if ($(".product-attr").length > 0) {
				values = $(".product-attr:checked")
					.map(function () {
						return $(this).val();
					})
					.get();

				keys = $(".product-attr:checked")
					.map(function () {
						return $(this).data("key");
					})
					.get();

				prices = $(".product-attr:checked")
					.map(function () {
						return $(this).data("price");
					})
					.get();
			}

			window.location =
				mainurl +
				"/addtonumcart?id=" +
				pid +
				"&qty=" +
				qty +
				"&size=" +
				sizes +
				"&color=" +
				colors.substring(1, colors.length) +
				"&size_qty=" +
				size_qty +
				"&size_price=" +
				size_price +
				"&size_key=" +
				size_key +
				"&keys=" +
				keys +
				"&values=" +
				values +
				"&prices=" +
				prices;
		});

		// CART ADD BY ONE

		$(document).on("click", ".quantity-up", function () {
			var pid = $(this).parent().parent().parent().find(".prodid").val();

			var itemid = $(this).parent().parent().parent().find(".itemid").val();
			var size_qty = $(this)
				.parent()
				.parent()
				.parent()
				.find(".size_qty")
				.val();

			var size_price = $(this)
				.parent()
				.parent()
				.parent()
				.find(".size_price")
				.val();

			var stck = $("#stock" + itemid).val();
			var qty = parseInt($("#qty" + itemid).val());
			if (stck != "") {
				var stk = parseInt(stck);
				if (qty < stk) {
					qty++;
					$("#qty" + itemid).html(qty);
				}
			} else {
				qty++;
				$("#qty" + itemid).html(qty);
			}
			$.ajax({
				type: "GET",
				url: mainurl + "/addbyone",
				data: {
					id: pid,
					itemid: itemid,
					size_qty: size_qty,
					size_price: size_price,
				},
				success: function (data) {
					$(".gocover").hide();
					if (data == 0) {
						toastr.error(lang.cart_out);
					} else {
						$.get(mainurl + "/carts", function (response) {
							$(".load_cart").html(response);
						});
					}
				},
			});
		});

		// CART REDUCE BY ONE

		// Product Add Qty
		$(document).on("click", ".qtplus", function () {
			var el = $(this);
			var $tselector = el.parent().parent().find(".qttotal");
			total = $($tselector).val();
			if (stock != "") {
				var stk = parseInt(stock);
				if (total < stk) {
					total++;
					$($tselector).val(total);
				}
			} else {
				total++;
			}

			$($tselector).val(total);
		});

		// Product Minus Qty
		$(document).on("click", ".qtminus", function () {
			var el = $(this);
			var $tselector = el.parent().parent().find(".qttotal");
			total = $($tselector).val();
			if (total > 1) {
				total--;
			}
			$($tselector).val(total);
		});

		$(".qttotal").keypress(function (e) {
			if (this.value.length == 0 && e.which == 48) {
				return false;
			}
			if (e.which != 8 && e.which != 32) {
				if (isNaN(String.fromCharCode(e.which))) {
					e.preventDefault();
				}
			}
		});

		//**************************** USER FORM SUBMIT SECTION ****************************************

		$(document).on("submit", "#userform", function (e) {
			e.preventDefault();
			$(".gocover").show();
			$("button.submit-btn").prop("disabled", true);
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data.errors) {
						for (var error in data.errors) {
							toastr.error(data.errors[error]);
						}
					} else {
						toastr.success(data);
					}
					$(window).scrollTop(-1);
					$(".gocover").hide();
					$("button.submit-btn").prop("disabled", false);
				},
			});
		});

		$(document).on("click", ".quantity-down", function () {
			var pid = $(this).parent().parent().parent().find(".prodid").val();

			var itemid = $(this).parent().parent().parent().find(".itemid").val();
			var size_qty = $(this)
				.parent()
				.parent()
				.parent()
				.find(".size_qty")
				.val();

			var size_price = $(this)
				.parent()
				.parent()
				.parent()
				.find(".size_price")
				.val();

			var qty = parseInt($("#qty" + itemid).val());

			var minimum_qty = $(this)
				.parent()
				.parent()
				.parent()
				.find(".minimum_qty")
				.val();
			$(".gocover").show();
			if (qty < 1) {
				$("#qty" + itemid).val("1");
				$(".gocover").hide();
				return false;
			} else if (qty < minimum_qty) {
				return false;
			} else {
				$(".gocover").show();

				$("#qty" + itemid).val(qty);
				$.ajax({
					type: "GET",
					url: mainurl + "/reducebyone",
					data: {
						id: pid,
						itemid: itemid,
						size_qty: size_qty,
						size_price: size_price,
					},
					success: function (data) {
						if (data.qty >= 1) {
							$.get(mainurl + "/carts", function (response) {
								$(".load_cart").html(response);
							});
						} else {
							return false;
						}
					},
				});
			}
		});

		$("#coupon-form").on("submit", function () {
			var val = $("#code").val();
			var total = $("#grandtotal").val();
			$.ajax({
				type: "GET",
				url: mainurl + "/carts/coupon",
				data: {
					code: val,
					total: total,
				},
				success: function (data) {
					if (data == 0) {
						toastr.error(lang.no_coupon);
						$("#code").val("");
					} else if (data == 2) {
						toastr.error(lang.already_coupon);
						$("#code").val("");
					} else if (data == 4) {
						toastr.error(lang.enter_coupon);
						$("#code").val("");
					} else {
						$("#coupon_form").toggle();
						$(".main-total").html(data[0]);
						$(".discount").html(data[4]);
						toastr.success(lang.coupon_found);
						$("#code").val("");
					}
				},
			});
			return false;
		});

		//**************************** POPUP BANNER ****************************************

		$(".preload-close").click(function () {
			$(".subscribe-preloader-wrap").hide();
		});

		//**************************** COOKIE ****************************************

		//**************************** COOKIE ENDS ****************************************

		//**************************** POPUP BANNER ENDS ****************************************

		// Coupon code toggle code
		$("#coupon-link").on("click", function () {
			$("#coupon-form,#check-coupon-form").toggle();
		});

		// IMAGE UPLOADING :)

		$(".upload").on("change", function () {
			var imgpath = $(this).parent().parent().parent().prev().find("img");
			var file = $(this);
			readURL(this, imgpath);
		});

		function readURL(input, imgpath) {
			if (input.files && input.files[0]) {
				var reader = new FileReader();
				reader.onload = function (e) {
					imgpath.attr("src", e.target.result);
				};
				reader.readAsDataURL(input.files[0]);
			}
		}
		// IMAGE UPLOADING ENDS :)

		//**************************** MESSAGE FORM SUBMIT SECTION ****************************************

		$(document).on("submit", "#messageform", function (e) {
			e.preventDefault();
			var href = $(this).data("href");
			$(".gocover").show();
			$("button.mybtn1").prop("disabled", true);
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data.errors) {
						for (var error in data.errors) {
							toastr.error(data.errors[error]);
						}
					} else {
						toastr.success(data);
						$("#messageform textarea").val("");
						$("#messages").load(href);
					}
					$(".gocover").hide();
					$("button.mybtn1").prop("disabled", false);
				},
			});
		});

		//**************************** MESSAGE FORM SUBMIT SECTION ENDS ****************************************

		//**************************** CONTACT FORM SUBMIT SECTION ****************************************

		$(".contactform").on("submit", function (e) {
			var $this = $(this);
			e.preventDefault();
			$this.find(".gocover").show();
			$this.find(".input-field").prop("readonly", true);
			$this.find("button").prop("disabled", true);
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data.errors) {
						for (var error in data.errors) {
							toastr.error(data.errors[error]);
						}
					} else {
						toastr.success(data);
						$this.find(".input-field").val("");
						$(".off-canvas-menu").removeClass("show");
						$(".off-canvas-menu-overlay").removeClass("show");
					}
					$this.find(".gocover").hide();
					$this.find(".input-field").prop("readonly", false);
					$this.find("button").prop("disabled", false);
					$this.find(".refresh_code").trigger("click");
				},
			});
		});

		if (gs.is_cookie == 1) {
			$(".btn-accept").on("click", function () {
				$(".cookie-bar-wrap").removeClass("show");
				$(".cookie-bar-wrap").addClass("hide");
			});
		}

		$(document).on("click", ".add-to-cart-quick", function (e) {
			e.preventDefault();
			window.location = $(this).data("href");
		});

		$(document).on("click", ".stars", function () {
			$(".stars").removeClass("active");
			$(this).addClass("active");
			$("#rating").val($(this).data("val"));
		});

		//  edit starts
		$(document).on("click", ".edit", function () {
			var text = $(this).parent().parent().prev().find("p").html();
			text = $.trim(text);
			$(this)
				.parent()
				.parent()
				.parent()
				.parent()
				.next(".edit-area")
				.find("textarea")
				.val(text);
			$(this)
				.parent()
				.parent()
				.parent()
				.parent()
				.next(".edit-area")
				.toggleClass("d-none");
		});

		//Edit Ends

		// UPDATE
		$(document).on("submit", ".update", function (e) {
			e.preventDefault();
			var btn = $(this).find("button[type=submit]");
			var text = $(this).parent().prev().find(".right-area .comment-body p");
			var $this = $(this).parent();
			btn.prop("disabled", true);
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					text.html(data);
					$this.addClass("d-none");
					btn.prop("disabled", false);
				},
			});
		});
		// UPDATE ENDS

		//**************************** CONTACT FORM SUBMIT SECTION ENDS ****************************************

		// Review Submit

		$(document).on("submit", "#reviewform", function (e) {
			var $this = $(this);
			e.preventDefault();
			$(".gocover").show();
			$("#reviewform button[type=submit]").prop("disabled", true);
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					if (data.errors) {
						for (var error in data.errors) {
							toastr.error(data.errors[error]);
							$("#reviewform button.mybtn1").eq(0).focus();
						}
					} else {
						toastr.success(data);
						$("#reviewform textarea").eq(0).focus();
						$("#reviewform textarea").val("");
						$("#product-reviews").load($this.data("href"));
						$("#rating-load").load($this.data("side-href"));
					}

					$(".gocover").hide();
					$("#reviewform button[type=submit]").prop("disabled", false);
				},
			});
		});

		//**************************** REVIEW SECTION ENDS ****************************************
		$(document).on("click", ".compare-remove", function () {
			var class_name = $(this).attr("data-class");
			$.get($(this).data("href"), function (data) {
				$("#compare-count").html(data[1]);
				$("#compare-count1").html(data[1]);
				if (data[0] == 0) {
					$("." + class_name).remove();
					toastr.success(data["success"]);
				} else {
					$("h2.title").html(data["error"]);
					$(".compare-page-content-wrap").remove();
					$("." + class_name).remove();
					toastr.success(data["success"]);
				}
			});
		});

		//**************************** COMPARE SECTION ENDS ****************************************

		//**************************** GLOBAL CAPCHA****************************************

		$(".refresh_code").on("click", function () {
			$.get(mainurl + "/contact/refresh_code", function (data, status) {
				$(".codeimg1").attr(
					"src",
					mainurl + "/assets/images/capcha_code.png?time=" + Math.random()
				);
			});
		});

		if ($(".refresh_code").length > 0) {
			$.get(mainurl + "/contact/refresh_code", function (data, status) {
				$(".codeimg1").attr(
					"src",
					mainurl + "/assets/images/capcha_code.png?time=" + Math.random()
				);
			});
		}

		//**************************** GLOBAL CAPCHA ENDS****************************************

		$("#category_select").on("change", function () {
			var val = $(this).val();
			$("#category_id").val(val);
			$("#searchForm").attr(
				"action",
				mainurl + "/category/" + $(this).val()
			);
		});

		// REPLY FORM

		$(document).on("submit", ".reply-form", function (e) {
			e.preventDefault();
			var btn = $(this).find("button[type=submit]");
			btn.prop("disabled", true);
			var $this = $(this).parent();
			var text = $(this).find("textarea");
			$.ajax({
				method: "POST",
				url: $(this).prop("action"),
				data: new FormData(this),
				contentType: false,
				cache: false,
				processData: false,
				success: function (data) {
					$("#comment-form textarea").val("");
					$("button.submit-btn").prop("disabled", false);
					$this.before(data);
					$this.addClass("d-none");
					text.val("");
					btn.prop("disabled", false);
				},
			});
		});

		// REPLY FORM ENDS
	});
})(jQuery);
