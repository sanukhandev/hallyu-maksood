<?php

header("Content-type: text/css; charset: UTF-8");
if(isset($_GET['color']))
{
  $color = '#'.$_GET['color'];
}
else {
  $color = '#c9030f';
}

if(isset($_GET['header_color']))
{
  $header_color = $_GET['header_color'];
}
?>

.hover-text-primary:hover *, a.hover-text-primary:hover, .list-color-secondary li.mixitup-control-active, .list-color-dark li.mixitup-control-active, .tab-simple li.mixitup-control-active, .nav-primary-hover .navbar-nav .nav-link:hover, .nav-primary-hover .navbar-nav .active>.nav-link:hover, .nav-primary-hover .navbar-nav .active>.nav-link, .list-text-hover-primary li a:hover, .media-icon-white a:hover, .text-primary, .pro-details-sidebar-item h3,  .time-box ul li span:first-child {
    color: <?php echo $color; ?> !important;
    
}
div.summary .price-summary .price-summary-content h5, .fixed-bg-primary.fixed-top, .nav-primary-hover.nav-line-active .navbar-nav .active>.nav-link:before, .nav-primary-hover.nav-down-line-active .navbar-nav .active>.nav-link:before, .bg-primary {
    background-color: <?php echo $color; ?> !important;
}
.list-color-dark li:hover, .list-color-dark li a:hover, .list-color-dark a:hover, .nav-dark-hover .navbar-nav .nav-link:hover, .nav-dark-hover .navbar-nav .active>.nav-link:hover, .nav-dark-hover .navbar-nav .active>.nav-link:hover, .nav-dark .navbar-nav .nav-link:hover {
    color: <?php echo $color; ?> !important;
}
 a:hover, .btn-link, .btn-link:hover, .post-admin ul li, .footer-widget li a:hover, .list-color-primary li a, .list-active-color-primary a.active, .list-color-primary li, .list-color-primary a, .nav-primary-hover .navbar-nav .nav-link:hover, .nav-primary-hover .navbar-nav .active>.nav-link:hover, .nav-primary-hover .navbar-nav .active>.nav-link, .navbar .navbar-nav li>ul.dropdown-menu li.active>a, .product-detail .woocommerce-loop-product__title:hover, .footer-simple-dark .footer-widget li a:hover, .footer-default-dark .media-widget a:hover, .nav-primary .navbar-nav .nav-link, .nav-secondary .navbar-nav .active>.nav-link, .search-form .btn-search, .media-widget a:hover {
    color: <?php echo $color; ?> !important;
    
}
.wishlist-view span.header-wishlist-count, .refresh-view span.header-compare-count, [class*="header-cart-"] .cart .cart-icon .header-cart-count {   
    background-color: <?php echo $color; ?> !important; 
}
.dataTables_paginate span .paginate_button.current {
    background:<?php echo $color; ?> !important;
}
.btn-primary,.btn-secondary {
    background-color:<?php echo $color; ?> !important; 
}
.btn-primary:hover,.btn-secondary:hover {
    color: <?php echo $color; ?> !important;
    border: 1px solid <?php echo $color; ?> !important; 
    background-color:#fff !important;
}
.slide-btn{
  background:<?php echo $color; ?> !important;
}
.product-wrapper .on-sale {
    background-color: <?php echo $color; ?> !important;
}
.add_to_cart_button {
    background-color: <?php echo $color; ?> !important;
}

element.style {
}
.flash a:hover {
    background: #000;
    color: #fff;
}
.flash a:hover {
   
    color: <?php echo $color; ?> !important;
    border: 1px solid <?php echo $color; ?> !important;
}
.product-style-1 .product-wrapper .hover-area [class*="-button"] a:hover {
    border: 1px solid <?php echo $color; ?> !important;
    color: <?php echo $color; ?>!important;
}
.e-title-hover-primary .product-wrapper .product-title a:hover {
    color: <?php echo $color; ?>!important;
}
button.btn.btn-secondary.rounded-right-pill.text-white:hover {
    color: <?php echo $color; ?>!important;
}
.widget_product_categories .cat-item.cat-parent.open>a, .widget_product_categories ul li a:hover {
    color: <?php echo $color; ?>!important;
}
.ui-slider .ui-slider-handle {
    background: <?php echo $color; ?>!important;
}
.ui-widget-header {
    background: <?php echo $color; ?>!important;
}
.hover-bg-primary:hover, .nav-tab-border .nav-link.mixitup-control-active, .page-item.active .page-link, .down-line::before {
    background-color: <?php echo $color; ?>!important;
}
.nav-tab-border .nav-link.mixitup-control-active, .page-item.active .page-link {
    border-color: <?php echo $color; ?>!important;
}
a.page-link:hover {
    background: <?php echo $color; ?>!important;
    color:#fff !important;
}
.genius{
  color:<?php echo $color; ?>!important;
}
a.active {
    color: <?php echo $color; ?>!important;
}
table.cart a.remove {
    color: <?php echo $color; ?>!important;
}   
table.cart .actions button {
    background-color: <?php echo $color; ?>!important;
}
.cart_totals .order-total .woocommerce-Price-amount {
    color: <?php echo $color; ?>!important;
}
.shipping-calculator-form .button, .wc-proceed-to-checkout .checkout-button {
    background-color: <?php echo $color; ?>!important;
    color:#fff !important;
}
.checkout-area .checkout-process li a.active {
    background: <?php echo $color; ?>!important;

}
.checkout-area .checkout-process li a.active::before {
    border-left: 20px solid <?php echo $color; ?>!important;
}
.mybtn1 {
    
    color: #fff;
    background:<?php echo $color; ?>!important;
}
.mybtn1:hover {
    
    color: <?php echo $color; ?>!important;
    background:#fff !important;
    border:1px solid <?php echo $color; ?>!important;
}
.radio-design .checkmark::after {
    
    background: <?php echo $color; ?>!important;
   
}
.checkout-area .content-box .content .billing-info-area .info-list li p i {
   
    color: <?php echo $color; ?>!important;
   
}
.checkout-area .content-box .content .payment-information .nav a span::after {
        
    background: <?php echo $color; ?>!important;
  
}
input[type="checkbox"]:checked + label:before {
    background-color: <?php echo $color; ?>!important;
    border-color: <?php echo $color; ?>!important;
    
}
.subscribePreloader__text {
    background: <?php echo $color; ?>!important;
}
.product-offer-item::before {
    color:<?php echo $color; ?>!important;
}
.fancy-star-rating .fancy-rating.good {
    background-color:<?php echo $color; ?>!important;
}
span.on-sale {
    background:<?php echo $color; ?>!important;
    color:#fff;
}
li.addtocart a {
    background: <?php echo $color; ?>!important;
    color:#fff !important;
}
li.addtocart a:hover {
    border:1px solid <?php echo $color; ?>!important;
    color:<?php echo $color; ?>!important;
}
.scroller{
    background:<?php echo $color; ?>!important;
   
}
.scroller:hover{
    background:#fff !important;
    color:<?php echo $color; ?>!important;
    border:1px solid <?php echo $color; ?>!important;
}
a.print-order-btn {
    background: <?php echo $color; ?>!important;
    color: #fff !important;
}
a.back-btn.theme-bg {
    background: <?php echo $color; ?>!important;
    color: #fff !important;
    }
    .process-steps li.done:after, .process-steps li.active:after, .process-steps li.active .icon {
    color: #fff !important;
    background: <?php echo $color; ?>!important;
}
.upload-file label {
    background: <?php echo $color; ?>!important;
    color: #fff;
}
.all-comment li .replay-area button {
   
    background: <?php echo $color; ?>!important;
    border: 1px solid <?php echo $color; ?>!important;
    
}
.all-comment li .replay-area button:hover {
   background:#fff !important;
   color: <?php echo $color; ?>!important;
   border: 1px solid <?php echo $color; ?>!important;
   
}
.all-comment li .replay-area .remove {

    background:<?php echo $color; ?>!important;
    border: 1px solid <?php echo $color; ?>!important;
    color:#fff !important;
}
.all-comment li .replay-area .remove:hover {
    background:#fff !important;
   color: <?php echo $color; ?>!important;
   border: 1px solid <?php echo $color; ?>!important;

}

.closed a {

    color: #fff !important;
    
}
.closed a:hover {
    background-color: #fff !important;
    border: 1px solid <?php echo $color; ?>!important;
    color: <?php echo $color; ?>!important;
}
.report-item{
color: <?php echo $color; ?>!important;
}
.btn--base {
    background-color: <?php echo $color; ?>!important;
    color: #fff;
}
.btn--base:hover {
    color: <?php echo $color; ?>!important;
    background-color: #fff !important; 
    border: 1px solid <?php echo $color; ?>!important;
}
.message-modal .modal .modal-dialog .modal-header {
    background: <?php echo $color; ?>!important;
}
.message-modal .modal .contact-form .submit-btn {

    background: <?php echo $color; ?>!important;

}
.message-modal .modal .contact-form .submit-btn:hover {
    background: #fff !important;
    color: <?php echo $color; ?>!important;
    border: 1px solid <?php echo $color; ?>!important;
}
.price-summary .price-summary-content h5 {
    background: <?php echo $color; ?>!important;

}
.report .login-area .header-area .title {

    color: <?php echo $color; ?>!important;
}
.report .login-area .submit-btn {
    background: <?php echo $color; ?>!important;
    color: #fff !important;

}
.report .login-area .submit-btn:hover {
    color: <?php echo $color; ?>!important;
    background: #fff !important;
    border:1px solid <?php echo $color; ?>!important;

}
.fvrt{
    color:<?php echo $color; ?>!important;
}
.woocommerce-tabs .nav-pills .nav-link:hover, .woocommerce-tabs .nav-pills .nav-link.active {
    
    border-bottom-color: <?php echo $color; ?>!important;
  
}
.product-size .siz-list li.active .box, .product-size .siz-list li:hover .box {
    border: 1px solid <?php echo $color; ?>!important;
    background: <?php echo $color; ?>!important;
    color: white;
}
.section-head-side-title {
    border-bottom: 2px solid <?php echo $color; ?>!important;

}
.all-comment li .single-comment .right-area .comment-footer .links a {
	
	color: <?php echo $color; ?>!important;

}
.cmn--btn  {
    background: <?php echo $color; ?>!important;
}
.btn-dark {
  background-color: <?php echo $color; ?>!important;
}
.btn-dark:hover {
  color: <?php echo $color; ?>!important;
  background-color: #fff !important;
  border: 1px solid <?php echo $color; ?>!important;
}
.form-check-input:checked {
  background-color: <?php echo $color; ?>!important;
 
}
.table-responsive .table thead tr {
  background: <?php echo $color; ?>!important;
  color: #fff !important;
}
#menu-and-category .nav-link.active,
#menu-and-category2 .nav-link.active {
    background-color: <?php echo $color; ?> !important;
    color: var(--theme-white-color) !important;
}