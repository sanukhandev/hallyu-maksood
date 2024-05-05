<?php
header("Content-type: text/css; charset: UTF-8");
if(isset($_GET['font_familly']))
{
  $font_familly = $_GET['font_familly'];
}
else {
  $font_familly = 'Open Sans';
}
?>

html{
  font-family: <?php echo $font_familly?>;
}

body{
  font-family: <?php echo $font_familly?>;
}

h1,
h2,
h3 {
  font-weight: 900 !important;
}
h1,
h2,
h3,
h4,
h5,
h6 {
  font-family: <?php echo $font_familly?>;
}
:root {
    --theme-general-font: <?php echo $font_familly?> !important;
    /* Font use for normal text and general text ok*/
}

:root {
    --theme-hiperlink-font: <?php echo $font_familly?> !important;
    /* Font use for normal text and general text ok*/
}

:root {
    --theme-highlight-font: <?php echo $font_familly?> !important;
    /* Font used in title or special area ok*/
}
.ordenery-font {
	font-family: <?php echo $font_familly?> !important;
}
.higlight-font {
	font-family: <?php echo $font_familly?> !important;
}
.extra-font {
	font-family:<?php echo $font_familly?> !important;
}
#place_order {
	font-family: <?php echo $font_familly?> !important;
}
.product-add-to-cart .add_to_cart_button {
	font-family: <?php echo $font_familly?> !important;
}
.report-item {
	font-family: <?php echo $font_familly?> !important;
}
.btn--base {
	font-family: <?php echo $font_familly?> !important;
}
.navbar-expand-lg .navbar-nav .nav-link {
	font-family: <?php echo $font_familly?> !important;

}
.dropdown-item {
	font-family: <?php echo $font_familly?> !important;
}
.entry-summary form button,
.entry-summary form div button {

	font-family: <?php echo $font_familly?> !important;
}
.btn-link {
	font-family: <?php echo $font_familly?> !important;

}
.btn {
	font-family: <?php echo $font_familly?> !important;
}
select,
select option,
select.form-control,
select.form-control option,
input::placeholder,
input.form-control::placeholder {
	font-family: <?php echo $font_familly?> !important;

}
.banner-seven .product-cats a {

	font-family: <?php echo $font_familly?> !important;

}
.today-deal .product-wrapper .product-info .available-items {
	font-family: <?php echo $font_familly?> !important;
}
.short-info .product-wrapper .product-info .strok {
	font-family: <?php echo $font_familly?> !important;
}
.product-wrapper .product-image [class*="product-labels"] [class*="badge"] {
	font-family: <?php echo $font_familly?> !important;
}
.product-wrapper .product-info .product-cats a {
	font-family: <?php echo $font_familly?> !important;
}
div.summary .product-price-discount,
.product-wrapper .on-sale {
	font-family: <?php echo $font_familly?> !important;
}
.shipping-cost {
	font-family: <?php echo $font_familly?> !important;
}
.product-wrapper .shipping-feed-back .sold-items {
	font-family: <?php echo $font_familly?> !important;
}
.product-wrapper .shipping-feed-back .shipping-cost,
.product-wrapper .shipping-feed-back .star-rating {

	font-family: <?php echo $font_familly?> !important;
}
.offer-product .product-wrapper .product-info .product-cats a {
	font-family: <?php echo $font_familly?> !important;
}
.extra-font {
	font-family: <?php echo $font_familly?> !important;
}
.extra2-font {
	font-family: <?php echo $font_familly?> !important;
}