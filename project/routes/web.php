<?php


// ************************************ ADMIN SECTION **********************************************

Route::prefix('admin')->group(function () {

    //------------ ADMIN LOGIN SECTION ------------

    Route::get('/login', 'Auth\Admin\LoginController@showForm')->name('admin.login');
    Route::post('/login', 'Auth\Admin\LoginController@login')->name('admin.login.submit');
    Route::get('/logout', 'Auth\Admin\LoginController@logout')->name('admin.logout');

    //------------ ADMIN LOGIN SECTION ENDS ------------

    //------------ ADMIN FORGOT SECTION ------------

    Route::get('/forgot', 'Auth\Admin\ForgotController@showForm')->name('admin.forgot');
    Route::post('/forgot', 'Auth\Admin\ForgotController@forgot')->name('admin.forgot.submit');
    Route::get('/change-password/{token}', 'Auth\Admin\ForgotController@showChangePassForm')->name('admin.change.token');
    Route::post('/change-password', 'Auth\Admin\ForgotController@changepass')->name('admin.change.password');

    //------------ ADMIN FORGOT SECTION ENDS ------------

    //------------ ADMIN NOTIFICATION SECTION ------------

    // Notification Count
    Route::get('/all/notf/count', 'Admin\NotificationController@all_notf_count')->name('all-notf-count');
    // Notification Count Ends


    // User Notification
    Route::get('/user/notf/show', 'Admin\NotificationController@user_notf_show')->name('user-notf-show');
    Route::get('/user/notf/clear', 'Admin\NotificationController@user_notf_clear')->name('user-notf-clear');
    // User Notification Ends

    // Order Notification
    Route::get('/order/notf/show', 'Admin\NotificationController@order_notf_show')->name('order-notf-show');
    Route::get('/order/notf/clear', 'Admin\NotificationController@order_notf_clear')->name('order-notf-clear');
    // Order Notification Ends

    // Product Notification
    Route::get('/product/notf/show', 'Admin\NotificationController@product_notf_show')->name('product-notf-show');
    Route::get('/product/notf/clear', 'Admin\NotificationController@product_notf_clear')->name('product-notf-clear');
    // Product Notification Ends

    // Product Notification
    Route::get('/conv/notf/show', 'Admin\NotificationController@conv_notf_show')->name('conv-notf-show');
    Route::get('/conv/notf/clear', 'Admin\NotificationController@conv_notf_clear')->name('conv-notf-clear');
    // Product Notification Ends

    //------------ ADMIN NOTIFICATION SECTION ENDS ------------

    //------------ ADMIN DASHBOARD & PROFILE SECTION ------------
    Route::get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
    Route::get('/profile', 'Admin\DashboardController@profile')->name('admin.profile');
    Route::post('/profile/update', 'Admin\DashboardController@profileupdate')->name('admin.profile.update');
    Route::get('/password', 'Admin\DashboardController@passwordreset')->name('admin.password');
    Route::post('/password/update', 'Admin\DashboardController@changepass')->name('admin.password.update');
    //------------ ADMIN DASHBOARD & PROFILE SECTION ENDS ------------

    //------------ ADMIN ORDER SECTION ------------

    Route::group(['middleware' => 'permissions:orders'], function () {

        Route::get('/orders/datatables/{slug}', 'Admin\OrderController@datatables')->name('admin-order-datatables'); //JSON REQUEST
        Route::get('/orders', 'Admin\OrderController@orders')->name('admin-orders-all');
        Route::get('/order/edit/{id}', 'Admin\OrderController@edit')->name('admin-order-edit');
        Route::post('/order/update/{id}', 'Admin\OrderController@update')->name('admin-order-update');
        Route::get('/order/{id}/show', 'Admin\OrderController@show')->name('admin-order-show');
        Route::get('/order/{id}/invoice', 'Admin\OrderController@invoice')->name('admin-order-invoice');
        Route::get('/order/{id}/print', 'Admin\OrderController@printpage')->name('admin-order-print');
        Route::get('/order/{id1}/status/{status}', 'Admin\OrderController@status')->name('admin-order-status');
        Route::post('/order/email/', 'Admin\OrderController@emailsub')->name('admin-order-emailsub');
        Route::post('/order/{id}/license', 'Admin\OrderController@license')->name('admin-order-license');
        Route::post('/order/product-submit', 'Admin\OrderController@product_submit')->name('admin-order-product-submit');
        Route::get('/order/product-show/{id}', 'Admin\OrderController@product_show');
        Route::get('/order/addcart/{id}', 'Admin\OrderController@addcart');
        Route::get('/ordercart/product-edit/{id}/{itemid}/{orderid}', 'Admin\OrderController@product_edit')->name('admin-order-product-edit');
        Route::get('/order/updatecart/{id}', 'Admin\OrderController@updatecart');
        Route::get('/ordercart/product-delete/{id}/{orderid}', 'Admin\OrderController@product_delete')->name('admin-order-product-delete');
        // Order Tracking

        Route::get('/order/{id}/track', 'Admin\OrderTrackController@index')->name('admin-order-track');
        Route::get('/order/{id}/trackload', 'Admin\OrderTrackController@load')->name('admin-order-track-load');
        Route::post('/order/track/store', 'Admin\OrderTrackController@store')->name('admin-order-track-store');
        Route::get('/order/track/add', 'Admin\OrderTrackController@add')->name('admin-order-track-add');
        Route::get('/order/track/edit/{id}', 'Admin\OrderTrackController@edit')->name('admin-order-track-edit');
        Route::post('/order/track/update/{id}', 'Admin\OrderTrackController@update')->name('admin-order-track-update');
        Route::delete('/order/track/delete/{id}', 'Admin\OrderTrackController@delete')->name('admin-order-track-delete');

        // Order Tracking Ends

    });

    //------------ ADMIN ORDER SECTION ENDS------------


/////////////////////////////// ////////////////////////////////////////////


// --------------- ADMIN COUNRTY SECTION ---------------//
    Route::get('/country/datatables', 'Admin\CountryController@datatables')->name('admin-country-datatables');
    Route::get('/manage/country', 'Admin\CountryController@manageCountry')->name('admin-country-index');
    Route::get('/manage/country/status/{id1}/{id2}', 'Admin\CountryController@status')->name('admin-country-status');
    Route::get('/country/delete/{id}', 'Admin\CountryController@delete')->name('admin-country-delete');
    Route::get('/country/tax/datatables', 'Admin\CountryController@taxDatatables')->name('admin-country-tax-datatables');
    Route::get('/manage/country/tax', 'Admin\CountryController@country_tax')->name('admin-country-tax');

// --------------- ADMIN COUNRTY SECTION END -----------//


// tax
    Route::get('/country/set-tax/{id}', 'Admin\CountryController@setTax')->name('admin-set-tax');
    Route::post('/country/set-tax/store/{id}', 'Admin\CountryController@updateTax')->name('admin-tax-update');


// --------------- ADMIN STATE SECTION --------------------//


    Route::get('/state/datatables/{country}', 'Admin\StateController@datatables')->name('admin-state-datatables');
    Route::get('/manage/state/{country}', 'Admin\StateController@manageState')->name('admin-state-index');
    Route::get('/state/create/{country}', 'Admin\StateController@create')->name('admin-state-create');
    Route::post('/state/store/{country}', 'Admin\StateController@store')->name('admin-state-store');
    Route::get('/state/status/{id1}/{id2}', 'Admin\StateController@status')->name('admin-state-status');
    Route::get('/state/edit/{id}', 'Admin\StateController@edit')->name('admin-state-edit');
    Route::post('/state/update/{id}', 'Admin\StateController@update')->name('admin-state-update');
    Route::delete('/state/delete/{id}', 'Admin\StateController@delete')->name('admin-state-delete');


// --------------- ADMIN STATE SECTION --------------------//


    //------------ ADMIN CATEGORY SECTION ENDS------------

    Route::group(['middleware' => 'permissions:earning'], function () {

        // -------------------------- Admin Total Income Route --------------------------//
        Route::get('tax/calculate', 'Admin\IncomeController@taxCalculate')->name('admin-tax-calculate-income');
        Route::get('subscription/earning', 'Admin\IncomeController@subscriptionIncome')->name('admin-subscription-income');
        Route::get('withdraw/earning', 'Admin\IncomeController@withdrawIncome')->name('admin-withdraw-income');
        Route::get('commission/earning', 'Admin\IncomeController@commissionIncome')->name('admin-commission-income');
        // -------------------------- Admin Total Income Route --------------------------//
    });


/////////////////////////////// ////////////////////////////////////////////


    //------------ ADMIN MANAGE CATEGORY SECTION ------------

    Route::group(['middleware' => 'permissions:categories'], function () {

        Route::get('/category/datatables', 'Admin\CategoryController@datatables')->name('admin-cat-datatables'); //JSON REQUEST
        Route::get('/category', 'Admin\CategoryController@index')->name('admin-cat-index');
        Route::get('/category/create', 'Admin\CategoryController@create')->name('admin-cat-create');
        Route::post('/category/create', 'Admin\CategoryController@store')->name('admin-cat-store');
        Route::get('/category/edit/{id}', 'Admin\CategoryController@edit')->name('admin-cat-edit');
        Route::post('/category/edit/{id}', 'Admin\CategoryController@update')->name('admin-cat-update');
        Route::delete('/category/delete/{id}', 'Admin\CategoryController@destroy')->name('admin-cat-delete');
        Route::get('/category/status/{id1}/{id2}', 'Admin\CategoryController@status')->name('admin-cat-status');

        //------------ ADMIN ATTRIBUTE SECTION ------------

        Route::get('/attribute/datatables', 'Admin\AttributeController@datatables')->name('admin-attr-datatables'); //JSON REQUEST
        Route::get('/attribute', 'Admin\AttributeController@index')->name('admin-attr-index');
        Route::get('/attribute/{catid}/attrCreateForCategory', 'Admin\AttributeController@attrCreateForCategory')->name('admin-attr-createForCategory');
        Route::get('/attribute/{subcatid}/attrCreateForSubcategory', 'Admin\AttributeController@attrCreateForSubcategory')->name('admin-attr-createForSubcategory');
        Route::get('/attribute/{childcatid}/attrCreateForChildcategory', 'Admin\AttributeController@attrCreateForChildcategory')->name('admin-attr-createForChildcategory');
        Route::post('/attribute/store', 'Admin\AttributeController@store')->name('admin-attr-store');
        Route::get('/attribute/{id}/manage', 'Admin\AttributeController@manage')->name('admin-attr-manage');
        Route::get('/attribute/{attrid}/edit', 'Admin\AttributeController@edit')->name('admin-attr-edit');
        Route::post('/attribute/edit/{id}', 'Admin\AttributeController@update')->name('admin-attr-update');
        Route::get('/attribute/{id}/options', 'Admin\AttributeController@options')->name('admin-attr-options');
        Route::get('/attribute/delete/{id}', 'Admin\AttributeController@destroy')->name('admin-attr-delete');

        // SUBCATEGORY SECTION ------------

        Route::get('/subcategory/datatables', 'Admin\SubCategoryController@datatables')->name('admin-subcat-datatables'); //JSON REQUEST
        Route::get('/subcategory', 'Admin\SubCategoryController@index')->name('admin-subcat-index');
        Route::get('/subcategory/create', 'Admin\SubCategoryController@create')->name('admin-subcat-create');
        Route::post('/subcategory/create', 'Admin\SubCategoryController@store')->name('admin-subcat-store');
        Route::get('/subcategory/edit/{id}', 'Admin\SubCategoryController@edit')->name('admin-subcat-edit');
        Route::post('/subcategory/edit/{id}', 'Admin\SubCategoryController@update')->name('admin-subcat-update');
        Route::delete('/subcategory/delete/{id}', 'Admin\SubCategoryController@destroy')->name('admin-subcat-delete');
        Route::get('/subcategory/status/{id1}/{id2}', 'Admin\SubCategoryController@status')->name('admin-subcat-status');
        Route::get('/load/subcategories/{id}/', 'Admin\SubCategoryController@load')->name('admin-subcat-load'); //JSON REQUEST

        // SUBCATEGORY SECTION ENDS------------

        // CHILDCATEGORY SECTION ------------

        Route::get('/childcategory/datatables', 'Admin\ChildCategoryController@datatables')->name('admin-childcat-datatables'); //JSON REQUEST
        Route::get('/childcategory', 'Admin\ChildCategoryController@index')->name('admin-childcat-index');
        Route::get('/childcategory/create', 'Admin\ChildCategoryController@create')->name('admin-childcat-create');
        Route::post('/childcategory/create', 'Admin\ChildCategoryController@store')->name('admin-childcat-store');
        Route::get('/childcategory/edit/{id}', 'Admin\ChildCategoryController@edit')->name('admin-childcat-edit');
        Route::post('/childcategory/edit/{id}', 'Admin\ChildCategoryController@update')->name('admin-childcat-update');
        Route::delete('/childcategory/delete/{id}', 'Admin\ChildCategoryController@destroy')->name('admin-childcat-delete');
        Route::get('/childcategory/status/{id1}/{id2}', 'Admin\ChildCategoryController@status')->name('admin-childcat-status');
        Route::get('/load/childcategories/{id}/', 'Admin\ChildCategoryController@load')->name('admin-childcat-load'); //JSON REQUEST

        // CHILDCATEGORY SECTION ENDS------------

    });

    //------------ ADMIN MANAGE CATEGORY SECTION ENDS------------

    //------------ ADMIN PRODUCT SECTION ------------

    Route::group(['middleware' => 'permissions:products'], function () {


        Route::get('/products/datatables', 'Admin\ProductController@datatables')->name('admin-prod-datatables'); //JSON REQUEST
        Route::get('/products', 'Admin\ProductController@index')->name('admin-prod-index');

        Route::post('/products/upload/update/{id}', 'Admin\ProductController@uploadUpdate')->name('admin-prod-upload-update');

        Route::get('/products/deactive', 'Admin\ProductController@deactive')->name('admin-prod-deactive');

        Route::get('/products/catalogs/datatables', 'Admin\ProductController@catalogdatatables')->name('admin-prod-catalog-datatables'); //JSON REQUEST
        Route::get('/products/catalogs/', 'Admin\ProductController@productscatalog')->name('admin-prod-catalog-index');

        // CREATE SECTION
        Route::get('/products/types', 'Admin\ProductController@types')->name('admin-prod-types');
        Route::get('/products/{slug}/create', 'Admin\ProductController@create')->name('admin-prod-create');
        Route::post('/products/store', 'Admin\ProductController@store')->name('admin-prod-store');
        Route::get('/getattributes', 'Admin\ProductController@getAttributes')->name('admin-prod-getattributes');
        // CREATE SECTION

        // EDIT SECTION
        Route::get('/products/edit/{id}', 'Admin\ProductController@edit')->name('admin-prod-edit');
        Route::post('/products/edit/{id}', 'Admin\ProductController@update')->name('admin-prod-update');
        // EDIT SECTION ENDS

        // DELETE SECTION
        Route::delete('/products/delete/{id}', 'Admin\ProductController@destroy')->name('admin-prod-delete');
        // DELETE SECTION ENDS

        Route::get('/products/catalog/{id1}/{id2}', 'Admin\ProductController@catalog')->name('admin-prod-catalog');

        Route::get('/products/product-settings', 'Admin\ProductController@productsettings')->name('admin-gs-prod-settings');
        Route::post('/products/product-settings/update', 'Admin\ProductController@settingUpdate')->name('admin-gs-prod-settings-update');

    });

    //------------ ADMIN PRODUCT SECTION ENDS------------


    //------------ ADMIN AFFILIATE PRODUCT SECTION ------------

    Route::group(['middleware' => 'permissions:affilate_products'], function () {

        Route::get('/products/import/create-product', 'Admin\ImportController@createImport')->name('admin-import-create');
        Route::get('/products/import/edit/{id}', 'Admin\ImportController@edit')->name('admin-import-edit');

        Route::get('/products/import/datatables', 'Admin\ImportController@datatables')->name('admin-import-datatables'); //JSON REQUEST
        Route::get('/products/import/index', 'Admin\ImportController@index')->name('admin-import-index');

        Route::post('/products/import/store', 'Admin\ImportController@store')->name('admin-import-store');
        Route::post('/products/import/update/{id}', 'Admin\ImportController@update')->name('admin-import-update');

        // DELETE SECTION
        Route::delete('/affiliate/products/delete/{id}', 'Admin\ProductController@destroy')->name('admin-affiliate-prod-delete');
        // DELETE SECTION ENDS

    });

    //------------ ADMIN AFFILIATE PRODUCT SECTION ENDS ------------

    //------------ ADMIN CSV IMPORT SECTION ------------

    Route::group(['middleware' => 'permissions:bulk_product_upload'], function () {

        Route::get('/products/import', 'Admin\ProductController@import')->name('admin-prod-import');
        Route::post('/products/import-submit', 'Admin\ProductController@importSubmit')->name('admin-prod-importsubmit');

    });

    //------------ ADMIN CSV IMPORT SECTION ENDS ------------

    //------------ ADMIN PRODUCT DISCUSSION SECTION ------------

    Route::group(['middleware' => 'permissions:product_discussion'], function () {

        // RATING SECTION ENDS------------

        Route::get('/ratings/datatables', 'Admin\RatingController@datatables')->name('admin-rating-datatables'); //JSON REQUEST
        Route::get('/ratings', 'Admin\RatingController@index')->name('admin-rating-index');
        Route::delete('/ratings/delete/{id}', 'Admin\RatingController@destroy')->name('admin-rating-delete');
        Route::get('/ratings/show/{id}', 'Admin\RatingController@show')->name('admin-rating-show');

        // RATING SECTION ENDS------------

        // COMMENT SECTION ------------

        Route::get('/comments/datatables', 'Admin\CommentController@datatables')->name('admin-comment-datatables'); //JSON REQUEST
        Route::get('/comments', 'Admin\CommentController@index')->name('admin-comment-index');
        Route::delete('/comments/delete/{id}', 'Admin\CommentController@destroy')->name('admin-comment-delete');
        Route::get('/comments/show/{id}', 'Admin\CommentController@show')->name('admin-comment-show');

        // COMMENT SECTION ENDS ------------

        // REPORT SECTION ------------

        Route::get('/reports/datatables', 'Admin\ReportController@datatables')->name('admin-report-datatables'); //JSON REQUEST
        Route::get('/reports', 'Admin\ReportController@index')->name('admin-report-index');
        Route::delete('/reports/delete/{id}', 'Admin\ReportController@destroy')->name('admin-report-delete');
        Route::get('/reports/show/{id}', 'Admin\ReportController@show')->name('admin-report-show');

        // REPORT SECTION ENDS ------------

    });

    //------------ ADMIN PRODUCT DISCUSSION SECTION ENDS ------------

    //------------ ADMIN COUPON SECTION ------------

    Route::group(['middleware' => 'permissions:set_coupons'], function () {

        Route::get('/coupon/datatables', 'Admin\CouponController@datatables')->name('admin-coupon-datatables'); //JSON REQUEST
        Route::get('/coupon', 'Admin\CouponController@index')->name('admin-coupon-index');
        Route::get('/coupon/create', 'Admin\CouponController@create')->name('admin-coupon-create');
        Route::post('/coupon/create', 'Admin\CouponController@store')->name('admin-coupon-store');
        Route::get('/coupon/edit/{id}', 'Admin\CouponController@edit')->name('admin-coupon-edit');
        Route::post('/coupon/edit/{id}', 'Admin\CouponController@update')->name('admin-coupon-update');
        Route::delete('/coupon/delete/{id}', 'Admin\CouponController@destroy')->name('admin-coupon-delete');
        Route::get('/coupon/status/{id1}/{id2}', 'Admin\CouponController@status')->name('admin-coupon-status');

    });

    //------------ ADMIN COUPON SECTION ENDS------------

    //------------ ADMIN USER SECTION ------------

    Route::group(['middleware' => 'permissions:customers'], function () {

        Route::get('/users/datatables', 'Admin\UserController@datatables')->name('admin-user-datatables'); //JSON REQUEST
        Route::get('/users', 'Admin\UserController@index')->name('admin-user-index');
        Route::get('/users/create', 'Admin\UserController@create')->name('admin-user-create');
        Route::post('/users/store', 'Admin\UserController@store')->name('admin-user-store');
        Route::get('/users/edit/{id}', 'Admin\UserController@edit')->name('admin-user-edit');
        Route::post('/users/edit/{id}', 'Admin\UserController@update')->name('admin-user-update');
        Route::delete('/users/delete/{id}', 'Admin\UserController@destroy')->name('admin-user-delete');
        Route::get('/user/{id}/show', 'Admin\UserController@show')->name('admin-user-show');
        Route::get('/users/ban/{id1}/{id2}', 'Admin\UserController@ban')->name('admin-user-ban');
        Route::get('/user/default/image', 'Admin\GeneralSettingController@user_image')->name('admin-user-image');
        Route::get('/users/deposit/{id}', 'Admin\UserController@deposit')->name('admin-user-deposit');
        Route::post('/user/deposit/{id}', 'Admin\UserController@depositUpdate')->name('admin-user-deposit-update');
        Route::get('/users/vendor/{id}', 'Admin\UserController@vendor')->name('admin-user-vendor');
        Route::post('/user/vendor/{id}', 'Admin\UserController@setVendor')->name('admin-user-vendor-update');


        // WITHDRAW SECTION

        Route::get('/users/withdraws/datatables', 'Admin\UserController@withdrawdatatables')->name('admin-withdraw-datatables'); //JSON REQUEST
        Route::get('/users/withdraws', 'Admin\UserController@withdraws')->name('admin-withdraw-index');
        Route::get('/user/withdraw/{id}/show', 'Admin\UserController@withdrawdetails')->name('admin-withdraw-show');
        Route::get('/users/withdraws/accept/{id}', 'Admin\UserController@accept')->name('admin-withdraw-accept');
        Route::get('/user/withdraws/reject/{id}', 'Admin\UserController@reject')->name('admin-withdraw-reject');

        // WITHDRAW SECTION ENDS


    });

    //------------ ADMIN USER DEPOSIT & TRANSACTION SECTION ------------

    Route::group(['middleware' => 'permissions:customer_deposits'], function () {

        Route::get('/users/deposit/datatables/{status}', 'Admin\UserDepositController@datatables')->name('admin-user-deposit-datatables'); //JSON REQUEST
        Route::get('/users/deposits/{slug}', 'Admin\UserDepositController@deposits')->name('admin-user-deposits');
        Route::get('/users/deposits/status/{id1}/{id2}', 'Admin\UserDepositController@status')->name('admin-user-deposit-status');
        Route::get('/users/transactions/datatables', 'Admin\UserTransactionController@transdatatables')->name('admin-trans-datatables'); //JSON REQUEST
        Route::get('/users/transactions', 'Admin\UserTransactionController@index')->name('admin-trans-index');
        Route::get('/users/transactions/{id}/show', 'Admin\UserTransactionController@transhow')->name('admin-trans-show');

    });

    //------------ ADMIN USER DEPOSIT & TRANSACTION SECTION ------------

    //------------ ADMIN VENDOR SECTION ------------

    Route::group(['middleware' => 'permissions:vendors'], function () {

        Route::get('/vendors/datatables', 'Admin\VendorController@datatables')->name('admin-vendor-datatables');
        Route::get('/vendors', 'Admin\VendorController@index')->name('admin-vendor-index');

        Route::get('/vendors/{id}/show', 'Admin\VendorController@show')->name('admin-vendor-show');
        Route::get('/vendors/secret/login/{id}', 'Admin\VendorController@secret')->name('admin-vendor-secret');
        Route::get('/vendor/edit/{id}', 'Admin\VendorController@edit')->name('admin-vendor-edit');
        Route::post('/vendor/edit/{id}', 'Admin\VendorController@update')->name('admin-vendor-update');

        Route::get('/vendor/verify/{id}', 'Admin\VendorController@verify')->name('admin-vendor-verify');
        Route::post('/vendor/verify/{id}', 'Admin\VendorController@verifySubmit')->name('admin-vendor-verify-submit');

        Route::get('/add/subscription/{id}', 'Admin\VendorController@addSubs')->name('admin-vendor-add-subs');
        Route::post('/add/subscription/{id}', 'Admin\VendorController@addSubsStore')->name('admin-vendor-subs-store');

        Route::get('/vendor/color', 'Admin\GeneralSettingController@vendor_color')->name('admin-vendor-color');
        Route::get('/vendors/status/{id1}/{id2}', 'Admin\VendorController@status')->name('admin-vendor-st');
        Route::delete('/vendors/delete/{id}', 'Admin\VendorController@destroy')->name('admin-vendor-delete');

        Route::get('/vendors/withdraws/datatables', 'Admin\VendorController@withdrawdatatables')->name('admin-vendor-withdraw-datatables'); //JSON REQUEST
        Route::get('/vendors/withdraws', 'Admin\VendorController@withdraws')->name('admin-vendor-withdraw-index');
        Route::get('/vendors/withdraw/{id}/show', 'Admin\VendorController@withdrawdetails')->name('admin-vendor-withdraw-show');
        Route::get('/vendors/withdraws/accept/{id}', 'Admin\VendorController@accept')->name('admin-vendor-withdraw-accept');
        Route::get('/vendors/withdraws/reject/{id}', 'Admin\VendorController@reject')->name('admin-vendor-withdraw-reject');

    });

    //------------ ADMIN VENDOR SECTION ENDS ------------

    //------------ ADMIN SUBSCRIPTION SECTION ------------

    Route::group(['middleware' => 'permissions:vendor_subscriptions'], function () {

        Route::get('/subscription/datatables', 'Admin\SubscriptionController@datatables')->name('admin-subscription-datatables');
        Route::get('/subscription', 'Admin\SubscriptionController@index')->name('admin-subscription-index');
        Route::get('/subscription/create', 'Admin\SubscriptionController@create')->name('admin-subscription-create');
        Route::post('/subscription/create', 'Admin\SubscriptionController@store')->name('admin-subscription-store');
        Route::get('/subscription/edit/{id}', 'Admin\SubscriptionController@edit')->name('admin-subscription-edit');
        Route::post('/subscription/edit/{id}', 'Admin\SubscriptionController@update')->name('admin-subscription-update');
        Route::delete('/subscription/delete/{id}', 'Admin\SubscriptionController@destroy')->name('admin-subscription-delete');

    });

    //------------ ADMIN SUBSCRIPTION SECTION ENDS ------------

    //------------ ADMIN VENDOR VERIFICATION SECTION ------------

    Route::group(['middleware' => 'permissions:vendor_verifications'], function () {

        Route::get('/verificatons/datatables/{status}', 'Admin\VerificationController@datatables')->name('admin-vr-datatables');
        Route::get('/verificatons/{slug}', 'Admin\VerificationController@verificatons')->name('admin-vr-index');
        Route::get('/verificatons/show/attachment', 'Admin\VerificationController@show')->name('admin-vr-show');
        Route::get('/verificatons/edit/{id}', 'Admin\VerificationController@edit')->name('admin-vr-edit');
        Route::post('/verificatons/edit/{id}', 'Admin\VerificationController@update')->name('admin-vr-update');
        Route::get('/verificatons/status/{id1}/{id2}', 'Admin\VerificationController@status')->name('admin-vr-st');
        Route::delete('/verificatons/delete/{id}', 'Admin\VerificationController@destroy')->name('admin-vr-delete');

    });

    //------------ ADMIN VENDOR VERIFICATION SECTION ENDS ------------

    //------------ ADMIN VENDOR SUBSCRIPTION PLAN SECTION ------------

    Route::group(['middleware' => 'permissions:vendor_subscription_plans'], function () {

        Route::get('/vendors/subs/datatables/{status}', 'Admin\VendorSubscriptionController@subsdatatables')->name('admin-vendor-subs-datatables');
        Route::get('/vendors/subs/{slug}', 'Admin\VendorSubscriptionController@subs')->name('admin-vendor-subs');
        Route::get('/vendors/subs/status/{id1}/{id2}', 'Admin\VendorSubscriptionController@status')->name('admin-user-sub-status');
        Route::get('/vendors/sub/{id}', 'Admin\VendorSubscriptionController@sub')->name('admin-vendor-sub');

    });

    //------------ ADMIN VENDOR SUBSCRIPTION PLAN SECTION ------------

    //------------ ADMIN USER MESSAGE SECTION ------------

    Route::group(['middleware' => 'permissions:messages'], function () {

        Route::get('/messages/datatables/{type}', 'Admin\MessageController@datatables')->name('admin-message-datatables');
        Route::get('/tickets', 'Admin\MessageController@index')->name('admin-message-index');
        Route::get('/disputes', 'Admin\MessageController@dispute')->name('admin-message-dispute');
        Route::get('/message/{id}', 'Admin\MessageController@message')->name('admin-message-show');
        Route::get('/message/load/{id}', 'Admin\MessageController@messageshow')->name('admin-message-load');
        Route::post('/message/post', 'Admin\MessageController@postmessage')->name('admin-message-store');
        Route::delete('/message/{id}/delete', 'Admin\MessageController@messagedelete')->name('admin-message-delete');
        Route::post('/user/send/message', 'Admin\MessageController@usercontact')->name('admin-send-message');

    });

    //------------ ADMIN USER MESSAGE SECTION ENDS ------------

    //------------ ADMIN BLOG SECTION ------------

    Route::group(['middleware' => 'permissions:blog'], function () {

        Route::get('/blog/datatables', 'Admin\BlogController@datatables')->name('admin-blog-datatables'); //JSON REQUEST
        Route::get('/blog', 'Admin\BlogController@index')->name('admin-blog-index');
        Route::get('/blog/create', 'Admin\BlogController@create')->name('admin-blog-create');
        Route::post('/blog/create', 'Admin\BlogController@store')->name('admin-blog-store');
        Route::get('/blog/edit/{id}', 'Admin\BlogController@edit')->name('admin-blog-edit');
        Route::post('/blog/edit/{id}', 'Admin\BlogController@update')->name('admin-blog-update');
        Route::delete('/blog/delete/{id}', 'Admin\BlogController@destroy')->name('admin-blog-delete');

        Route::get('/blog/category/datatables', 'Admin\BlogCategoryController@datatables')->name('admin-cblog-datatables'); //JSON REQUEST
        Route::get('/blog/category', 'Admin\BlogCategoryController@index')->name('admin-cblog-index');
        Route::get('/blog/category/create', 'Admin\BlogCategoryController@create')->name('admin-cblog-create');
        Route::post('/blog/category/create', 'Admin\BlogCategoryController@store')->name('admin-cblog-store');
        Route::get('/blog/category/edit/{id}', 'Admin\BlogCategoryController@edit')->name('admin-cblog-edit');
        Route::post('/blog/category/edit/{id}', 'Admin\BlogCategoryController@update')->name('admin-cblog-update');
        Route::delete('/blog/category/delete/{id}', 'Admin\BlogCategoryController@destroy')->name('admin-cblog-delete');

        Route::get('/blog/blog-settings', 'Admin\BlogController@settings')->name('admin-gs-blog-settings');

    });

    //------------ ADMIN BLOG SECTION ENDS ------------


    //------------ ADMIN GENERAL SETTINGS SECTION ------------

    Route::group(['middleware' => 'permissions:general_settings'], function () {

        Route::get('/general-settings/logo', 'Admin\GeneralSettingController@logo')->name('admin-gs-logo');
        Route::get('/general-settings/favicon', 'Admin\GeneralSettingController@favicon')->name('admin-gs-fav');
        Route::get('/general-settings/loader', 'Admin\GeneralSettingController@loader')->name('admin-gs-load');
        Route::get('/general-settings/contents', 'Admin\GeneralSettingController@websitecontent')->name('admin-gs-contents');
        Route::get('/general-settings/affilate', 'Admin\GeneralSettingController@affilate')->name('admin-gs-affilate');
        Route::get('/general-settings/error-banner', 'Admin\GeneralSettingController@error_banner')->name('admin-gs-error-banner');
        Route::get('/general-settings/popup', 'Admin\GeneralSettingController@popup')->name('admin-gs-popup');
        Route::get('/general-settings/breadcrumb', 'Admin\GeneralSettingController@breadcrumb')->name('admin-gs-bread');
        Route::get('/general-settings/maintenance', 'Admin\GeneralSettingController@maintain')->name('admin-gs-maintenance');

// Deal Of The Day

        Route::get('/general-settings/deal_of_the_day', 'Admin\GeneralSettingController@deal')->name('admin-gs-deal');


        //------------ ADMIN PICKUP LOACTION ------------

        Route::get('/pickup/datatables', 'Admin\PickupController@datatables')->name('admin-pick-datatables'); //JSON REQUEST
        Route::get('/pickup', 'Admin\PickupController@index')->name('admin-pick-index');
        Route::get('/pickup/create', 'Admin\PickupController@create')->name('admin-pick-create');
        Route::post('/pickup/create', 'Admin\PickupController@store')->name('admin-pick-store');
        Route::get('/pickup/edit/{id}', 'Admin\PickupController@edit')->name('admin-pick-edit');
        Route::post('/pickup/edit/{id}', 'Admin\PickupController@update')->name('admin-pick-update');
        Route::delete('/pickup/delete/{id}', 'Admin\PickupController@destroy')->name('admin-pick-delete');

        //------------ ADMIN PICKUP LOACTION ENDS ------------

        //------------ ADMIN SHIPPING ------------

        Route::get('/shipping/datatables', 'Admin\ShippingController@datatables')->name('admin-shipping-datatables');
        Route::get('/shipping', 'Admin\ShippingController@index')->name('admin-shipping-index');
        Route::get('/shipping/create', 'Admin\ShippingController@create')->name('admin-shipping-create');
        Route::post('/shipping/create', 'Admin\ShippingController@store')->name('admin-shipping-store');
        Route::get('/shipping/edit/{id}', 'Admin\ShippingController@edit')->name('admin-shipping-edit');
        Route::post('/shipping/edit/{id}', 'Admin\ShippingController@update')->name('admin-shipping-update');
        Route::delete('/shipping/delete/{id}', 'Admin\ShippingController@destroy')->name('admin-shipping-delete');

        //------------ ADMIN SHIPPING ENDS ------------

        //------------ ADMIN PACKAGE ------------

        Route::get('/package/datatables', 'Admin\PackageController@datatables')->name('admin-package-datatables');
        Route::get('/package', 'Admin\PackageController@index')->name('admin-package-index');
        Route::get('/package/create', 'Admin\PackageController@create')->name('admin-package-create');
        Route::post('/package/create', 'Admin\PackageController@store')->name('admin-package-store');
        Route::get('/package/edit/{id}', 'Admin\PackageController@edit')->name('admin-package-edit');
        Route::post('/package/edit/{id}', 'Admin\PackageController@update')->name('admin-package-update');
        Route::delete('/package/delete/{id}', 'Admin\PackageController@destroy')->name('admin-package-delete');

        //------------ ADMIN PACKAGE ENDS------------

    });

    //------------ ADMIN GENERAL SETTINGS SECTION ENDS ------------

    //------------ ADMIN HOME PAGE SETTINGS SECTION ------------

    Route::group(['middleware' => 'permissions:home_page_settings'], function () {

        //------------ ADMIN SLIDER SECTION ------------

        Route::get('/slider/datatables', 'Admin\SliderController@datatables')->name('admin-sl-datatables'); //JSON REQUEST
        Route::get('/slider', 'Admin\SliderController@index')->name('admin-sl-index');
        Route::get('/slider/create', 'Admin\SliderController@create')->name('admin-sl-create');
        Route::post('/slider/create', 'Admin\SliderController@store')->name('admin-sl-store');
        Route::get('/slider/edit/{id}', 'Admin\SliderController@edit')->name('admin-sl-edit');
        Route::post('/slider/edit/{id}', 'Admin\SliderController@update')->name('admin-sl-update');
        Route::delete('/slider/delete/{id}', 'Admin\SliderController@destroy')->name('admin-sl-delete');

        //------------ ADMIN SLIDER SECTION ENDS ------------


        Route::get('/arrival/datatables', 'Admin\ArrivalsectionController@datatables')->name('admin-arrival-datatables');
        Route::get('/arrival', 'Admin\ArrivalsectionController@index')->name('admin-arrival-index');
        Route::get('/arrival/create', 'Admin\ArrivalsectionController@create')->name('admin-arrival-create');
        Route::post('/arrival/create', 'Admin\ArrivalsectionController@store')->name('admin-arrival-store');
        Route::get('/arrival/edit/{id}', 'Admin\ArrivalsectionController@edit')->name('admin-arrival-edit');
        Route::post('/arrival/edit/{id}', 'Admin\ArrivalsectionController@update')->name('admin-arrival-update');
        Route::delete('/arrival/delete/{id}', 'Admin\ArrivalsectionController@destroy')->name('admin-arrival-delete');
        Route::get('/country/status/{id1}/{id2}', 'Admin\ArrivalsectionController@status')->name('admin-arrival-status');

        //------------ ADMIN SERVICE SECTION ------------

        Route::get('/service/datatables', 'Admin\ServiceController@datatables')->name('admin-service-datatables'); //JSON REQUEST
        Route::get('/service', 'Admin\ServiceController@index')->name('admin-service-index');
        Route::get('/service/create', 'Admin\ServiceController@create')->name('admin-service-create');
        Route::post('/service/create', 'Admin\ServiceController@store')->name('admin-service-store');
        Route::get('/service/edit/{id}', 'Admin\ServiceController@edit')->name('admin-service-edit');
        Route::post('/service/edit/{id}', 'Admin\ServiceController@update')->name('admin-service-update');
        Route::delete('/service/delete/{id}', 'Admin\ServiceController@destroy')->name('admin-service-delete');

        //------------ ADMIN SERVICE SECTION ENDS ------------

        //------------ ADMIN BANNER SECTION ------------

        Route::get('/banner/datatables/{type}', 'Admin\BannerController@datatables')->name('admin-sb-datatables'); //JSON REQUEST
        Route::get('large/banner/', 'Admin\BannerController@large')->name('admin-sb-large');
        Route::get('large/banner/create', 'Admin\BannerController@largecreate')->name('admin-sb-create-large');
        Route::post('/banner/create', 'Admin\BannerController@store')->name('admin-sb-store');
        Route::get('/banner/edit/{id}', 'Admin\BannerController@edit')->name('admin-sb-edit');
        Route::post('/banner/edit/{id}', 'Admin\BannerController@update')->name('admin-sb-update');
        Route::delete('/banner/delete/{id}', 'Admin\BannerController@destroy')->name('admin-sb-delete');

        //------------ ADMIN BANNER SECTION ENDS ------------

        //------------ ADMIN PARTNER SECTION ------------

        Route::get('/partner/datatables', 'Admin\PartnerController@datatables')->name('admin-partner-datatables');
        Route::get('/partner', 'Admin\PartnerController@index')->name('admin-partner-index');
        Route::get('/partner/create', 'Admin\PartnerController@create')->name('admin-partner-create');
        Route::post('/partner/create', 'Admin\PartnerController@store')->name('admin-partner-store');
        Route::get('/partner/edit/{id}', 'Admin\PartnerController@edit')->name('admin-partner-edit');
        Route::post('/partner/edit/{id}', 'Admin\PartnerController@update')->name('admin-partner-update');
        Route::delete('/partner/delete/{id}', 'Admin\PartnerController@destroy')->name('admin-partner-delete');

        //------------ ADMIN PARTNER SECTION ENDS ------------

        //------------ ADMIN PAGE SETTINGS SECTION ------------

        Route::get('/page-settings/customize', 'Admin\PageSettingController@customize')->name('admin-ps-customize');
        Route::get('/page-settings/big-save', 'Admin\PageSettingController@big_save')->name('admin-ps-big-save');
        Route::get('/page-settings/best-seller', 'Admin\PageSettingController@best_seller')->name('admin-ps-best-seller');

    });

    //------------ ADMIN HOME PAGE SETTINGS SECTION ENDS ------------

    Route::group(['middleware' => 'permissions:menu_page_settings'], function () {

        //------------ ADMIN MENU PAGE SETTINGS SECTION ------------

        //------------ ADMIN FAQ SECTION ------------

        Route::get('/faq/datatables', 'Admin\FaqController@datatables')->name('admin-faq-datatables'); //JSON REQUEST
        Route::get('/faq', 'Admin\FaqController@index')->name('admin-faq-index');
        Route::get('/faq/create', 'Admin\FaqController@create')->name('admin-faq-create');
        Route::post('/faq/create', 'Admin\FaqController@store')->name('admin-faq-store');
        Route::get('/faq/edit/{id}', 'Admin\FaqController@edit')->name('admin-faq-edit');
        Route::post('/faq/update/{id}', 'Admin\FaqController@update')->name('admin-faq-update');
        Route::delete('/faq/delete/{id}', 'Admin\FaqController@destroy')->name('admin-faq-delete');

        //------------ ADMIN FAQ SECTION ENDS ------------

        //------------ ADMIN PAGE SECTION ------------

        Route::get('/page/datatables', 'Admin\PageController@datatables')->name('admin-page-datatables'); //JSON REQUEST
        Route::get('/page', 'Admin\PageController@index')->name('admin-page-index');
        Route::get('/page/create', 'Admin\PageController@create')->name('admin-page-create');
        Route::post('/page/create', 'Admin\PageController@store')->name('admin-page-store');
        Route::get('/page/edit/{id}', 'Admin\PageController@edit')->name('admin-page-edit');
        Route::post('/page/update/{id}', 'Admin\PageController@update')->name('admin-page-update');
        Route::delete('/page/delete/{id}', 'Admin\PageController@destroy')->name('admin-page-delete');
        Route::get('/page/header/{id1}/{id2}', 'Admin\PageController@header')->name('admin-page-header');
        Route::get('/page/footer/{id1}/{id2}', 'Admin\PageController@footer')->name('admin-page-footer');
        Route::get('/page/banner', 'Admin\PageSettingController@page_banner')->name('admin-ps-page-banner');
        Route::get('/right/banner', 'Admin\PageSettingController@right_banner')->name('admin-ps-right-banner');
        Route::get('/menu/links', 'Admin\PageSettingController@menu_links')->name('admin-ps-menu-links');
        //------------ ADMIN PAGE SECTION ENDS------------

        Route::get('/page-settings/contact', 'Admin\PageSettingController@contact')->name('admin-ps-contact');
        Route::post('/page-settings/update/all', 'Admin\PageSettingController@update')->name('admin-ps-update');

    });

//------------ ADMIN MENU PAGE SETTINGS SECTION ENDS ------------

    //------------ ADMIN EMAIL SETTINGS SECTION ------------

    Route::group(['middleware' => 'permissions:email_settings'], function () {

        Route::get('/email-templates/datatables', 'Admin\EmailController@datatables')->name('admin-mail-datatables');
        Route::get('/email-templates', 'Admin\EmailController@index')->name('admin-mail-index');
        Route::get('/email-templates/{id}', 'Admin\EmailController@edit')->name('admin-mail-edit');
        Route::post('/email-templates/{id}', 'Admin\EmailController@update')->name('admin-mail-update');
        Route::get('/email-config', 'Admin\EmailController@config')->name('admin-mail-config');
        Route::get('/groupemail', 'Admin\EmailController@groupemail')->name('admin-group-show');
        Route::post('/groupemailpost', 'Admin\EmailController@groupemailpost')->name('admin-group-submit');
    });

    //------------ ADMIN EMAIL SETTINGS SECTION ENDS ------------


    //------------ ADMIN PAYMENT SETTINGS SECTION ------------

    Route::group(['middleware' => 'permissions:payment_settings'], function () {

// Payment Informations

        Route::get('/payment-informations', 'Admin\GeneralSettingController@paymentsinfo')->name('admin-gs-payments');

// Payment Gateways

        Route::get('/paymentgateway/datatables', 'Admin\PaymentGatewayController@datatables')->name('admin-payment-datatables'); //JSON REQUEST
        Route::get('/paymentgateway', 'Admin\PaymentGatewayController@index')->name('admin-payment-index');
        Route::get('/paymentgateway/create', 'Admin\PaymentGatewayController@create')->name('admin-payment-create');
        Route::post('/paymentgateway/create', 'Admin\PaymentGatewayController@store')->name('admin-payment-store');
        Route::get('/paymentgateway/edit/{id}', 'Admin\PaymentGatewayController@edit')->name('admin-payment-edit');
        Route::post('/paymentgateway/update/{id}', 'Admin\PaymentGatewayController@update')->name('admin-payment-update');
        Route::delete('/paymentgateway/delete/{id}', 'Admin\PaymentGatewayController@destroy')->name('admin-payment-delete');
        Route::get('/paymentgateway/status/{field}/{id1}/{id2}', 'Admin\PaymentGatewayController@status')->name('admin-payment-status');

// Currency Settings

        // MULTIPLE CURRENCY

        Route::get('/currency/datatables', 'Admin\CurrencyController@datatables')->name('admin-currency-datatables'); //JSON REQUEST
        Route::get('/currency', 'Admin\CurrencyController@index')->name('admin-currency-index');
        Route::get('/currency/create', 'Admin\CurrencyController@create')->name('admin-currency-create');
        Route::post('/currency/create', 'Admin\CurrencyController@store')->name('admin-currency-store');
        Route::get('/currency/edit/{id}', 'Admin\CurrencyController@edit')->name('admin-currency-edit');
        Route::post('/currency/update/{id}', 'Admin\CurrencyController@update')->name('admin-currency-update');
        Route::delete('/currency/delete/{id}', 'Admin\CurrencyController@destroy')->name('admin-currency-delete');
        Route::get('/currency/status/{id1}/{id2}', 'Admin\CurrencyController@status')->name('admin-currency-status');


// -------------------- Reward Section Route ---------------------//
        Route::get('rewards/datatables', 'Admin\RewardController@datatables')->name('admin-reward-datatables');
        Route::get('rewards', 'Admin\RewardController@index')->name('admin-reward-index');
        Route::get('/general-settings/reward/{status}', 'Admin\GeneralSettingController@isreward')->name('admin-gs-is_reward');
        Route::post('reward/update/', 'Admin\RewardController@update')->name('admin-reward-update');
        Route::post('reward/information/update', 'Admin\RewardController@infoUpdate')->name('admin-reward-info-update');

// -------------------- Reward Section Route ---------------------//


    });

    //------------ ADMIN PAYMENT SETTINGS SECTION ENDS------------

    //------------ ADMIN SOCIAL SETTINGS SECTION ------------

    Route::group(['middleware' => 'permissions:social_settings'], function () {

        //------------ ADMIN SOCIAL LINK ------------

        Route::get('/social-link/datatables', 'Admin\SocialLinkController@datatables')->name('admin-sociallink-datatables'); //JSON REQUEST
        Route::get('/social-link', 'Admin\SocialLinkController@index')->name('admin-sociallink-index');
        Route::get('/social-link/create', 'Admin\SocialLinkController@create')->name('admin-sociallink-create');
        Route::post('/social-link/create', 'Admin\SocialLinkController@store')->name('admin-sociallink-store');
        Route::get('/social-link/edit/{id}', 'Admin\SocialLinkController@edit')->name('admin-sociallink-edit');
        Route::post('/social-link/edit/{id}', 'Admin\SocialLinkController@update')->name('admin-sociallink-update');
        Route::delete('/social-link/delete/{id}', 'Admin\SocialLinkController@destroy')->name('admin-sociallink-delete');
        Route::get('/social-link/status/{id1}/{id2}', 'Admin\SocialLinkController@status')->name('admin-sociallink-status');

        //------------ ADMIN SOCIAL LINK ENDS ------------
        Route::get('/social', 'Admin\SocialSettingController@index')->name('admin-social-index');
        Route::post('/social/update', 'Admin\SocialSettingController@socialupdate')->name('admin-social-update');
        Route::post('/social/update/all', 'Admin\SocialSettingController@socialupdateall')->name('admin-social-update-all');
        Route::get('/social/facebook', 'Admin\SocialSettingController@facebook')->name('admin-social-facebook');
        Route::get('/social/google', 'Admin\SocialSettingController@google')->name('admin-social-google');
        Route::get('/social/facebook/{status}', 'Admin\SocialSettingController@facebookup')->name('admin-social-facebookup');
        Route::get('/social/google/{status}', 'Admin\SocialSettingController@googleup')->name('admin-social-googleup');


    });
    //------------ ADMIN SOCIAL SETTINGS SECTION ENDS------------

    //------------ ADMIN LANGUAGE SETTINGS SECTION ------------

    Route::group(['middleware' => 'permissions:language_settings'], function () {

        //  Multiple Language Section

        //  Multiple Language Section Ends

        Route::get('/languages/datatables', 'Admin\LanguageController@datatables')->name('admin-lang-datatables'); //JSON REQUEST
        Route::get('/languages', 'Admin\LanguageController@index')->name('admin-lang-index');
        Route::get('/languages/create', 'Admin\LanguageController@create')->name('admin-lang-create');
        Route::get('/languages/import', 'Admin\LanguageController@import')->name('admin-lang-import');
        Route::get('/languages/edit/{id}', 'Admin\LanguageController@edit')->name('admin-lang-edit');
        Route::get('/languages/export/{id}', 'Admin\LanguageController@export')->name('admin-lang-export');
        Route::post('/languages/create', 'Admin\LanguageController@store')->name('admin-lang-store');
        Route::post('/languages/import/create', 'Admin\LanguageController@importStore')->name('admin-lang-import-store');
        Route::post('/languages/edit/{id}', 'Admin\LanguageController@update')->name('admin-lang-update');
        Route::get('/languages/status/{id1}/{id2}', 'Admin\LanguageController@status')->name('admin-lang-st');
        Route::delete('/languages/delete/{id}', 'Admin\LanguageController@destroy')->name('admin-lang-delete');

        //------------ ADMIN PANEL LANGUAGE SETTINGS SECTION ------------

        Route::get('/adminlanguages/datatables', 'Admin\AdminLanguageController@datatables')->name('admin-tlang-datatables'); //JSON REQUEST
        Route::get('/adminlanguages', 'Admin\AdminLanguageController@index')->name('admin-tlang-index');
        Route::get('/adminlanguages/create', 'Admin\AdminLanguageController@create')->name('admin-tlang-create');
        Route::get('/adminlanguages/edit/{id}', 'Admin\AdminLanguageController@edit')->name('admin-tlang-edit');
        Route::post('/adminlanguages/create', 'Admin\AdminLanguageController@store')->name('admin-tlang-store');
        Route::post('/adminlanguages/edit/{id}', 'Admin\AdminLanguageController@update')->name('admin-tlang-update');
        Route::get('/adminlanguages/status/{id1}/{id2}', 'Admin\AdminLanguageController@status')->name('admin-tlang-st');
        Route::delete('/adminlanguages/delete/{id}', 'Admin\AdminLanguageController@destroy')->name('admin-tlang-delete');

        //------------ ADMIN PANEL LANGUAGE SETTINGS SECTION ENDS ------------

        //------------ ADMIN LANGUAGE SETTINGS SECTION ENDS ------------

    });

    //------------ADMIN FONT SECTION------------------
    Route::get('/fonts/datatables', 'Admin\FontController@datatables')->name('admin.fonts.datatables');
    Route::get('/fonts', 'Admin\FontController@index')->name('admin.fonts.index');
    Route::get('/fonts/create', 'Admin\FontController@create')->name('admin.fonts.create');
    Route::post('/fonts/create', 'Admin\FontController@store')->name('admin.fonts.store');
    Route::get('/fonts/edit/{id}', 'Admin\FontController@edit')->name('admin.fonts.edit');
    Route::post('/fonts/edit/{id}', 'Admin\FontController@update')->name('admin.fonts.update');
    Route::delete('/fonts/delete/{id}', 'Admin\FontController@destroy')->name('admin.fonts.delete');
    Route::get('/fonts/status/{id}', 'Admin\FontController@status')->name('admin.fonts.status');
    //------------ADMIN FONT SECTION------------------

    //------------ ADMIN SEOTOOL SETTINGS SECTION ------------

    Route::group(['middleware' => 'permissions:seo_tools'], function () {

        Route::get('/seotools/analytics', 'Admin\SeoToolController@analytics')->name('admin-seotool-analytics');
        Route::post('/seotools/analytics/update', 'Admin\SeoToolController@analyticsupdate')->name('admin-seotool-analytics-update');
        Route::get('/seotools/keywords', 'Admin\SeoToolController@keywords')->name('admin-seotool-keywords');
        Route::post('/seotools/keywords/update', 'Admin\SeoToolController@keywordsupdate')->name('admin-seotool-keywords-update');
        Route::get('/products/popular/{id}', 'Admin\SeoToolController@popular')->name('admin-prod-popular');

    });

    //------------ ADMIN SEOTOOL SETTINGS SECTION ------------

    //------------ ADMIN STAFF SECTION ------------

    Route::group(['middleware' => 'permissions:manage_staffs'], function () {

        Route::get('/staff/datatables', 'Admin\StaffController@datatables')->name('admin-staff-datatables');
        Route::get('/staff', 'Admin\StaffController@index')->name('admin-staff-index');
        Route::get('/staff/create', 'Admin\StaffController@create')->name('admin-staff-create');
        Route::post('/staff/create', 'Admin\StaffController@store')->name('admin-staff-store');
        Route::get('/staff/edit/{id}', 'Admin\StaffController@edit')->name('admin-staff-edit');
        Route::post('/staff/update/{id}', 'Admin\StaffController@update')->name('admin-staff-update');
        Route::get('/staff/show/{id}', 'Admin\StaffController@show')->name('admin-staff-show');
        Route::delete('/staff/delete/{id}', 'Admin\StaffController@destroy')->name('admin-staff-delete');

    });

    //------------ ADMIN STAFF SECTION ENDS------------

    //------------ ADMIN SUBSCRIBERS SECTION ------------

    Route::group(['middleware' => 'permissions:subscribers'], function () {

        Route::get('/subscribers/datatables', 'Admin\SubscriberController@datatables')->name('admin-subs-datatables'); //JSON REQUEST
        Route::get('/subscribers', 'Admin\SubscriberController@index')->name('admin-subs-index');
        Route::get('/subscribers/download', 'Admin\SubscriberController@download')->name('admin-subs-download');

    });

    //------------ ADMIN SUBSCRIBERS ENDS ------------

// ------------ GLOBAL ----------------------
    Route::post('/general-settings/update/all', 'Admin\GeneralSettingController@generalupdate')->name('admin-gs-update');
    Route::post('/general-settings/update/payment', 'Admin\GeneralSettingController@generalupdatepayment')->name('admin-gs-update-payment');
    Route::post('/general-settings/update/mail', 'Admin\GeneralSettingController@generalMailUpdate')->name('admin-gs-update-mail');
    Route::get('/general-settings/status/{field}/{status}', 'Admin\GeneralSettingController@status')->name('admin-gs-status');


    // STATUS SECTION
    Route::get('/products/status/{id1}/{id2}', 'Admin\ProductController@status')->name('admin-prod-status');
    // STATUS SECTION ENDS

    // FEATURE SECTION
    Route::get('/products/feature/{id}', 'Admin\ProductController@feature')->name('admin-prod-feature');
    Route::post('/products/feature/{id}', 'Admin\ProductController@featuresubmit')->name('admin-prod-feature');
    // FEATURE SECTION ENDS

    // GALLERY SECTION ------------

    Route::get('/gallery/show', 'Admin\GalleryController@show')->name('admin-gallery-show');
    Route::post('/gallery/store', 'Admin\GalleryController@store')->name('admin-gallery-store');
    Route::get('/gallery/delete', 'Admin\GalleryController@destroy')->name('admin-gallery-delete');

    // GALLERY SECTION ENDS------------

    Route::post('/page-settings/update/all', 'Admin\PageSettingController@update')->name('admin-ps-update');
    Route::post('/page-settings/update/home', 'Admin\PageSettingController@homeupdate')->name('admin-ps-homeupdate');
    Route::post('/page-settings/menu-update', 'Admin\PageSettingController@menuupdate')->name('admin-ps-menuupdate');

// ------------ GLOBAL ENDS ----------------------

    Route::group(['middleware' => 'permissions:super'], function () {


        Route::get('/cache/clear', function () {
            Artisan::call('cache:clear');
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            return redirect()->route('admin.dashboard')->with('cache', 'System Cache Has Been Removed.');
        })->name('admin-cache-clear');

        Route::get('/check/movescript', 'Admin\DashboardController@movescript')->name('admin-move-script');
        Route::get('/generate/backup', 'Admin\DashboardController@generate_bkup')->name('admin-generate-backup');
        Route::get('/activation', 'Admin\DashboardController@activation')->name('admin-activation-form');
        Route::post('/activation', 'Admin\DashboardController@activation_submit')->name('admin-activate-purchase');
        Route::get('/clear/backup', 'Admin\DashboardController@clear_bkup')->name('admin-clear-backup');
        Route::get('/clear/demo/content', 'Admin\DashboardController@removeDemoContent')->name('admin-remove-democontent');

        // ------------ ROLE SECTION ----------------------

        Route::get('/role/datatables', 'Admin\RoleController@datatables')->name('admin-role-datatables');
        Route::get('/role', 'Admin\RoleController@index')->name('admin-role-index');
        Route::get('/role/create', 'Admin\RoleController@create')->name('admin-role-create');
        Route::post('/role/create', 'Admin\RoleController@store')->name('admin-role-store');
        Route::get('/role/edit/{id}', 'Admin\RoleController@edit')->name('admin-role-edit');
        Route::post('/role/edit/{id}', 'Admin\RoleController@update')->name('admin-role-update');
        Route::delete('/role/delete/{id}', 'Admin\RoleController@destroy')->name('admin-role-delete');

        // ------------ ROLE SECTION ENDS ----------------------

        // ------------ ADDON SECTION ----------------------

        Route::get('/addon/datatables', 'Admin\AddonController@datatables')->name('admin-addon-datatables');
        Route::get('/addon', 'Admin\AddonController@index')->name('admin-addon-index');
        Route::get('/addon/create', 'Admin\AddonController@create')->name('admin-addon-create');
        Route::post('/addon/install', 'Admin\AddonController@install')->name('admin-addon-install');
        Route::get('/addon/uninstall/{id}', 'Admin\AddonController@uninstall')->name('admin-addon-uninstall');

        // ------------ ADDON SECTION ENDS ----------------------


    });

    Route::get('/check/movescript', 'Admin\DashboardController@movescript')->name('admin-move-script');
    Route::get('/generate/backup', 'Admin\DashboardController@generate_bkup')->name('admin-generate-backup');
    Route::get('/activation', 'Admin\DashboardController@activation')->name('admin-activation-form');
    Route::post('/activation', 'Admin\DashboardController@activation_submit')->name('admin-activate-purchase');
    Route::get('/clear/backup', 'Admin\DashboardController@clear_bkup')->name('admin-clear-backup');


});


// ************************************ ADMIN SECTION ENDS**********************************************
Route::get('/under-maintenance', 'Front\FrontendController@maintenance')->name('front-maintenance');
Route::group(['middleware' => 'maintenance'], function () {

// ************************************ VENDOR SECTION **********************************************

    Route::prefix('vendor')->group(function () {
        Route::get('/login', 'User\LoginController@showVendorLoginForm')->name('vendor.login');

        Route::group(['middleware' => 'vendor'], function () {

            // VENDOR DASHBOARD

            Route::get('/dashboard', 'Vendor\VendorController@index')->name('vendor.dashboard');


            //------------ Brands SECTION ------------

            Route::get('/brands/datatables', 'Vendor\BrandsController@datatables')->name('vendor-brand-datatables');
            Route::get('/brands', 'Vendor\BrandsController@index')->name('vendor-brand-index');
            Route::get('/brands/create', 'Vendor\BrandsController@create')->name('vendor-brand-create');
            Route::post('/brands/store', 'Vendor\BrandsController@store')->name('vendor-brand-store');
            Route::get('/brands/edit/{id}', 'Vendor\BrandsController@edit')->name('vendor-brand-edit');
            Route::post('/brands/patch/{id}', 'Vendor\BrandsController@update')->name('vendor-brand-update');
            Route::delete('/brands/delete/{id}', 'Vendor\BrandsController@destroy')->name('vendor-brand-delete');
            Route::get('/brands/status/{id1}/{id2}', 'Vendor\BrandsController@status')->name('vendor-brand-status');
            //------------ ORDER SECTION ------------

            Route::get('/orders/datatables', 'Vendor\OrderController@datatables')->name('vendor-order-datatables');
            Route::get('/orders', 'Vendor\OrderController@index')->name('vendor-order-index');
            Route::get('/order/{id}/show', 'Vendor\OrderController@show')->name('vendor-order-show');
            Route::get('/order/{id}/invoice', 'Vendor\OrderController@invoice')->name('vendor-order-invoice');
            Route::get('/order/{id}/print', 'Vendor\OrderController@printpage')->name('vendor-order-print');
            Route::get('/order/{id1}/status/{status}', 'Vendor\OrderController@status')->name('vendor-order-status');
            Route::post('/order/email/', 'Vendor\OrderController@emailsub')->name('vendor-order-emailsub');
            Route::post('/order/{slug}/license', 'Vendor\OrderController@license')->name('vendor-order-license');

            //------------ ORDER SECTION ENDS------------

            //------------ SUBCATEGORY SECTION ------------

            Route::get('/load/subcategories/{id}/', 'Vendor\VendorController@subcatload')->name('vendor-subcat-load'); //JSON REQUEST

            //------------ SUBCATEGORY SECTION ENDS------------

            //------------ CHILDCATEGORY SECTION ------------

            Route::get('/load/childcategories/{id}/', 'Vendor\VendorController@childcatload')->name('vendor-childcat-load'); //JSON REQUEST

            //------------ CHILDCATEGORY SECTION ENDS------------

            //------------ PRODUCT SECTION ------------

            Route::get('/products/datatables', 'Vendor\ProductController@datatables')->name('vendor-prod-datatables'); //JSON REQUEST
            Route::get('/products', 'Vendor\ProductController@index')->name('vendor-prod-index');

            Route::post('/products/upload/update/{id}', 'Vendor\ProductController@uploadUpdate')->name('vendor-prod-upload-update');

            // CREATE SECTION
            Route::get('/products/types', 'Vendor\ProductController@types')->name('vendor-prod-types');
            Route::get('/products/{slug}/create', 'Vendor\ProductController@create')->name('vendor-prod-create');
            Route::post('/products/store', 'Vendor\ProductController@store')->name('vendor-prod-store');
            Route::get('/getattributes', 'Vendor\ProductController@getAttributes')->name('vendor-prod-getattributes');
            Route::get('/products/import', 'Vendor\ProductController@import')->name('vendor-prod-import');
            Route::post('/products/import-submit', 'Vendor\ProductController@importSubmit')->name('vendor-prod-importsubmit');

            Route::get('/products/catalog/datatables', 'Vendor\ProductController@catalogdatatables')->name('admin-vendor-catalog-datatables');
            Route::get('/products/catalogs', 'Vendor\ProductController@catalogs')->name('admin-vendor-catalog-index');

            // CREATE SECTION

            // EDIT SECTION
            Route::get('/products/edit/{id}', 'Vendor\ProductController@edit')->name('vendor-prod-edit');
            Route::post('/products/edit/{id}', 'Vendor\ProductController@update')->name('vendor-prod-update');

            Route::get('/products/catalog/{id}', 'Vendor\ProductController@catalogedit')->name('vendor-prod-catalog-edit');
            Route::post('/products/catalog/{id}', 'Vendor\ProductController@catalogupdate')->name('vendor-prod-catalog-update');

            // EDIT SECTION ENDS

            // IMPORT SECTION

            Route::get('/products/import/create-product', 'Vendor\ImportController@createImport')->name('vendor-import-create');
            Route::get('/products/import/edit/{id}', 'Vendor\ImportController@edit')->name('vendor-import-edit');
            Route::get('/products/import/csv', 'Vendor\ImportController@importCSV')->name('vendor-import-csv');
            Route::get('/products/import/datatables', 'Vendor\ImportController@datatables')->name('vendor-import-datatables');
            Route::get('/products/import/index', 'Vendor\ImportController@index')->name('vendor-import-index');
            Route::post('/products/import/store', 'Vendor\ImportController@store')->name('vendor-import-store');
            Route::post('/products/import/update/{id}', 'Vendor\ImportController@update')->name('vendor-import-update');
            Route::post('/products/import/csv/store', 'Vendor\ImportController@importStore')->name('vendor-import-csv-store');

            // IMPORT SECTION


            // STATUS SECTION
            Route::get('/products/status/{id1}/{id2}', 'Vendor\ProductController@status')->name('vendor-prod-status');
            // STATUS SECTION ENDS

            // DELETE SECTION
            Route::delete('/products/delete/{id}', 'Vendor\ProductController@destroy')->name('vendor-prod-delete');
            // DELETE SECTION ENDS

            //------------ VENDOR PRODUCT SECTION ENDS------------

            //------------ VENDOR GALLERY SECTION ------------

            Route::get('/gallery/show', 'Vendor\GalleryController@show')->name('vendor-gallery-show');
            Route::post('/gallery/store', 'Vendor\GalleryController@store')->name('vendor-gallery-store');
            Route::get('/gallery/delete', 'Vendor\GalleryController@destroy')->name('vendor-gallery-delete');

            //------------ VENDOR GALLERY SECTION ENDS------------

            //------------ ADMIN SHIPPING ------------

            Route::get('/shipping/datatables', 'Vendor\ShippingController@datatables')->name('vendor-shipping-datatables');
            Route::get('/shipping', 'Vendor\ShippingController@index')->name('vendor-shipping-index');
            Route::get('/shipping/create', 'Vendor\ShippingController@create')->name('vendor-shipping-create');
            Route::post('/shipping/create', 'Vendor\ShippingController@store')->name('vendor-shipping-store');
            Route::get('/shipping/edit/{id}', 'Vendor\ShippingController@edit')->name('vendor-shipping-edit');
            Route::post('/shipping/edit/{id}', 'Vendor\ShippingController@update')->name('vendor-shipping-update');
            Route::delete('/shipping/delete/{id}', 'Vendor\ShippingController@destroy')->name('vendor-shipping-delete');

            //------------ ADMIN SHIPPING ENDS ------------

            //------------ ADMIN PACKAGE ------------

            Route::get('/package/datatables', 'Vendor\PackageController@datatables')->name('vendor-package-datatables');
            Route::get('/package', 'Vendor\PackageController@index')->name('vendor-package-index');
            Route::get('/package/create', 'Vendor\PackageController@create')->name('vendor-package-create');
            Route::post('/package/create', 'Vendor\PackageController@store')->name('vendor-package-store');
            Route::get('/package/edit/{id}', 'Vendor\PackageController@edit')->name('vendor-package-edit');
            Route::post('/package/edit/{id}', 'Vendor\PackageController@update')->name('vendor-package-update');
            Route::delete('/package/delete/{id}', 'Vendor\PackageController@destroy')->name('vendor-package-delete');

            //------------ ADMIN PACKAGE ENDS------------

            //------------ VENDOR NOTIFICATION SECTION ------------

            Route::get('/order/notf/show/{id}', 'Vendor\NotificationController@order_notf_show')->name('vendor-order-notf-show');
            Route::get('/order/notf/count/{id}', 'Vendor\NotificationController@order_notf_count')->name('vendor-order-notf-count');
            Route::get('/order/notf/clear/{id}', 'Vendor\NotificationController@order_notf_clear')->name('vendor-order-notf-clear');

            //------------ VENDOR NOTIFICATION SECTION ENDS ------------

            // Vendor Profile
            Route::get('/profile', 'Vendor\VendorController@profile')->name('vendor-profile');
            Route::post('/profile', 'Vendor\VendorController@profileupdate')->name('vendor-profile-update');
            // Vendor Profile Ends

            // Vendor Shipping Cost
            Route::get('/banner', 'Vendor\VendorController@banner')->name('vendor-banner');

            // Vendor Social
            Route::get('/social', 'Vendor\VendorController@social')->name('vendor-social-index');
            Route::post('/social/update', 'Vendor\VendorController@socialupdate')->name('vendor-social-update');

            Route::get('/withdraw/datatables', 'Vendor\WithdrawController@datatables')->name('vendor-wt-datatables');
            Route::get('/withdraw', 'Vendor\WithdrawController@index')->name('vendor-wt-index');
            Route::get('/withdraw/create', 'Vendor\WithdrawController@create')->name('vendor-wt-create');
            Route::post('/withdraw/create', 'Vendor\WithdrawController@store')->name('vendor-wt-store');

            //------------ VENDOR SERVICE ------------

            Route::get('/service/datatables', 'Vendor\ServiceController@datatables')->name('vendor-service-datatables');
            Route::get('/service', 'Vendor\ServiceController@index')->name('vendor-service-index');
            Route::get('/service/create', 'Vendor\ServiceController@create')->name('vendor-service-create');
            Route::post('/service/create', 'Vendor\ServiceController@store')->name('vendor-service-store');
            Route::get('/service/edit/{id}', 'Vendor\ServiceController@edit')->name('vendor-service-edit');
            Route::post('/service/edit/{id}', 'Vendor\ServiceController@update')->name('vendor-service-update');
            Route::delete('/service/delete/{id}', 'Vendor\ServiceController@destroy')->name('vendor-service-delete');

            //------------ VENDOR SERVICE ENDS ------------

            //------------ VENDOR SOCIAL LINK ------------

            Route::get('/social-link/datatables', 'Vendor\SocialLinkController@datatables')->name('vendor-sociallink-datatables'); //JSON REQUEST
            Route::get('/social-link', 'Vendor\SocialLinkController@index')->name('vendor-sociallink-index');
            Route::get('/social-link/create', 'Vendor\SocialLinkController@create')->name('vendor-sociallink-create');
            Route::post('/social-link/create', 'Vendor\SocialLinkController@store')->name('vendor-sociallink-store');
            Route::get('/social-link/edit/{id}', 'Vendor\SocialLinkController@edit')->name('vendor-sociallink-edit');
            Route::post('/social-link/edit/{id}', 'Vendor\SocialLinkController@update')->name('vendor-sociallink-update');
            Route::delete('/social-link/delete/{id}', 'Vendor\SocialLinkController@destroy')->name('vendor-sociallink-delete');
            Route::get('/social-link/status/{id1}/{id2}', 'Vendor\SocialLinkController@status')->name('vendor-sociallink-status');

            //------------ VENDOR SOCIAL LINK ENDS ------------
            // -------------------------- Vendor Income ------------------------------------//
            Route::get('earning/datatables', "Vendor\IncomeController@datatables")->name('vendor.income.datatables');
            Route::get('total/earning', "Vendor\IncomeController@index")->name('vendor.income');

            Route::get('/verify', 'Vendor\VendorController@verify')->name('vendor-verify');
            Route::get('/warning/verify/{id}', 'Vendor\VendorController@warningVerify')->name('vendor-warning');
            Route::post('/verify', 'Vendor\VendorController@verifysubmit')->name('vendor-verify-submit');


            Route::get('/category/datatables', 'Vendor\CategoryController@datatables')->name('vendor-cat-datatables'); //JSON REQUEST
            Route::get('/category', 'Vendor\CategoryController@index')->name('vendor-cat-index');
            Route::get('/category/create', 'Vendor\CategoryController@create')->name('vendor-cat-create');
            Route::post('/category/create', 'Vendor\CategoryController@store')->name('vendor-cat-store');
            Route::get('/category/edit/{id}', 'Vendor\CategoryController@edit')->name('vendor-cat-edit');
            Route::post('/category/edit/{id}', 'Vendor\CategoryController@update')->name('vendor-cat-update');
            Route::delete('/category/delete/{id}', 'Vendor\CategoryController@destroy')->name('vendor-cat-delete');
            Route::get('/category/status/{id1}/{id2}', 'Vendor\CategoryController@status')->name('vendor-cat-status');

            //------------ ADMIN ATTRIBUTE SECTION ------------

            Route::get('/attribute/datatables', 'Vendor\AttributeController@datatables')->name('vendor-attr-datatables'); //JSON REQUEST
            Route::get('/attribute', 'Vendor\AttributeController@index')->name('vendor-attr-index');
            Route::get('/attribute/{catid}/attrCreateForCategory', 'Vendor\AttributeController@attrCreateForCategory')->name('vendor-attr-createForCategory');
            Route::get('/attribute/{subcatid}/attrCreateForSubcategory', 'Vendor\AttributeController@attrCreateForSubcategory')->name('vendor-attr-createForSubcategory');
            Route::get('/attribute/{childcatid}/attrCreateForChildcategory', 'Vendor\AttributeController@attrCreateForChildcategory')->name('vendor-attr-createForChildcategory');
            Route::post('/attribute/store', 'Vendor\AttributeController@store')->name('vendor-attr-store');
            Route::get('/attribute/{id}/manage', 'Vendor\AttributeController@manage')->name('vendor-attr-manage');
            Route::get('/attribute/{attrid}/edit', 'Vendor\AttributeController@edit')->name('vendor-attr-edit');
            Route::post('/attribute/edit/{id}', 'Vendor\AttributeController@update')->name('vendor-attr-update');
            Route::get('/attribute/{id}/options', 'Vendor\AttributeController@options')->name('vendor-attr-options');
            Route::get('/attribute/delete/{id}', 'Vendor\AttributeController@destroy')->name('vendor-attr-delete');

            // SUBCATEGORY SECTION ------------

            Route::get('/subcategory/datatables', 'Vendor\SubCategoryController@datatables')->name('vendor-subcat-datatables'); //JSON REQUEST
            Route::get('/subcategory', 'Vendor\SubCategoryController@index')->name('vendor-subcat-index');
            Route::get('/subcategory/create', 'Vendor\SubCategoryController@create')->name('vendor-subcat-create');
            Route::post('/subcategory/create', 'Vendor\SubCategoryController@store')->name('vendor-subcat-store');
            Route::get('/subcategory/edit/{id}', 'Vendor\SubCategoryController@edit')->name('vendor-subcat-edit');
            Route::post('/subcategory/edit/{id}', 'Vendor\SubCategoryController@update')->name('vendor-subcat-update');
            Route::delete('/subcategory/delete/{id}', 'Vendor\SubCategoryController@destroy')->name('vendor-subcat-delete');
            Route::get('/subcategory/status/{id1}/{id2}', 'Vendor\SubCategoryController@status')->name('vendor-subcat-status');
            Route::get('/load/subcategories/{id}/', 'Vendor\SubCategoryController@load')->name('vendor-subcat-load'); //JSON REQUEST

            // SUBCATEGORY SECTION ENDS------------

            // CHILDCATEGORY SECTION ------------

            Route::get('/childcategory/datatables', 'Vendor\ChildCategoryController@datatables')->name('vendor-childcat-datatables'); //JSON REQUEST
            Route::get('/childcategory', 'Vendor\ChildCategoryController@index')->name('vendor-childcat-index');
            Route::get('/childcategory/create', 'Vendor\ChildCategoryController@create')->name('vendor-childcat-create');
            Route::post('/childcategory/create', 'Vendor\ChildCategoryController@store')->name('vendor-childcat-store');
            Route::get('/childcategory/edit/{id}', 'Vendor\ChildCategoryController@edit')->name('vendor-childcat-edit');
            Route::post('/childcategory/edit/{id}', 'Vendor\ChildCategoryController@update')->name('vendor-childcat-update');
            Route::delete('/childcategory/delete/{id}', 'Vendor\ChildCategoryController@destroy')->name('vendor-childcat-delete');
            Route::get('/childcategory/status/{id1}/{id2}', 'Vendor\ChildCategoryController@status')->name('vendor-childcat-status');
            Route::get('/load/childcategories/{id}/', 'Vendor\ChildCategoryController@load')->name('vendor-childcat-load');
        });

    });

// ************************************ VENDOR SECTION ENDS**********************************************

// ************************************ USER SECTION **********************************************

    Route::prefix('user')->group(function () {


        // USER AUTH SECION
        Route::get('/login', 'User\LoginController@showLoginForm')->name('user.login');

        Route::get('/register', 'User\RegisterController@showRegisterForm')->name('user.register');
        Route::get('/vendor-register', 'User\RegisterController@showVendorRegisterForm')->name('vendor.register');
        // User Login
        Route::post('/login', 'Auth\User\LoginController@login')->name('user.login.submit');
        // User Login End

        // User Register
        Route::post('/register', 'Auth\User\RegisterController@register')->name('user-register-submit');
        Route::get('/register/verify/{token}', 'Auth\User\RegisterController@token')->name('user-register-token');
        // User Register End

        //------------ USER FORGOT SECTION ------------
        Route::get('/forgot', 'Auth\User\ForgotController@index')->name('user.forgot');
        Route::post('/forgot', 'Auth\User\ForgotController@forgot')->name('user.forgot.submit');
        Route::get('/change-password/{token}', 'Auth\User\ForgotController@showChangePassForm')->name('user.change.token');
        Route::post('/change-password', 'Auth\User\ForgotController@changepass')->name('user.change.password');

        //------------ USER FORGOT SECTION ENDS ------------

        //  --------------------- Reward Point Route ------------------------------//
        Route::get('reward/points', 'User\RewardController@rewards')->name('user-reward-index');
        Route::get('reward/convert', 'User\RewardController@convert')->name('user-reward-convernt');
        Route::post('reward/convert/submit', 'User\RewardController@convertSubmit')->name('user-reward-convert-submit');


        // User Logout
        Route::get('/logout', 'User\LoginController@logout')->name('user-logout');
        // User Logout Ends

        // USER AUTH AUCTION ENDS

        // User Dashboard
        Route::get('/dashboard', 'User\UserController@index')->name('user-dashboard');

        // User Reset
        Route::get('/reset', 'User\UserController@resetform')->name('user-reset');
        Route::post('/reset', 'User\UserController@reset')->name('user-reset-submit');
        // User Reset End

        // User Profile
        Route::get('/profile', 'User\UserController@profile')->name('user-profile');
        Route::post('/profile', 'User\UserController@profileupdate')->name('user-profile-update');
        // User Profile Ends

        // Display important Codes For Payment Gatweways
        Route::get('/payment/{slug1}/{slug2}', 'User\UserController@loadpayment')->name('user.load.payment');
        Route::get('/country/wise/state/{country_id}', 'Front\CheckoutController@getState')->name('country.wise.state');

        // User Wishlist
        Route::get('/wishlists', 'User\WishlistController@wishlists')->name('user-wishlists');
        Route::get('/wishlist/add/{id}', 'User\WishlistController@addwish')->name('user-wishlist-add');
        Route::get('/wishlist/remove/{id}', 'User\WishlistController@removewish')->name('user-wishlist-remove');
        // User Wishlist Ends

        // User Review
        Route::post('/review/submit', 'User\UserController@reviewsubmit')->name('front.review.submit');
        // User Review Ends

        // User Orders

        Route::get('/orders', 'User\OrderController@orders')->name('user-orders');
        Route::get('/order/tracking', 'User\OrderController@ordertrack')->name('user-order-track');
        Route::get('/order/trackings/{id}', 'User\OrderController@trackload')->name('user-order-track-search');
        Route::get('/order/{id}', 'User\OrderController@order')->name('user-order');
        Route::get('/download/order/{slug}/{id}', 'User\OrderController@orderdownload')->name('user-order-download');
        Route::get('print/order/print/{id}', 'User\OrderController@orderprint')->name('user-order-print');
        Route::get('/json/trans', 'User\OrderController@trans');

        // User Orders Ends

        // USER SUBSCRIPTION

        // Subscription Package
        Route::get('/package', 'User\SubscriptionController@package')->name('user-package');
        Route::get('/subscription/{id}', 'User\SubscriptionController@vendorrequest')->name('user-vendor-request');
        Route::post('/vendor-request', 'User\SubscriptionController@vendorrequestsub')->name('user-vendor-request-submit');

        // Subscription Payment Redirect
        Route::get('/payment/cancle', 'User\SubscriptionController@paycancle')->name('user.payment.cancle');
        Route::get('/payment/return', 'User\SubscriptionController@payreturn')->name('user.payment.return');
        Route::get('/shop/check', 'User\SubscriptionController@check')->name('user.shop.check');
        // Paypal
        Route::post('/paypal-submit', 'Payment\Subscription\PaypalController@store')->name('user.paypal.submit');
        Route::get('/paypal-notify', 'Payment\Subscription\PaypalController@notify')->name('user.paypal.notify');

        // Stripe
        Route::post('/stripe-submit', 'Payment\Subscription\StripeController@store')->name('user.stripe.submit');

        // Instamojo
        Route::post('/instamojo-submit', 'Payment\Subscription\InstamojoController@store')->name('user.instamojo.submit');
        Route::get('/instamojo-notify', 'Payment\Subscription\InstamojoController@notify')->name('user.instamojo.notify');

        // Paystack
        Route::post('/paystack-submit', 'Payment\Subscription\PaystackController@store')->name('user.paystack.submit');

        // PayTM
        Route::post('/paytm-submit', 'Payment\Subscription\PaytmController@store')->name('user.paytm.submit');;
        Route::post('/paytm-notify', 'Payment\Subscription\PaytmController@notify')->name('user.paytm.notify');

        // Molly
        Route::post('/molly-submit', 'Payment\Subscription\MollieController@store')->name('user.molly.submit');
        Route::get('/molly-notify', 'Payment\Subscription\MollieController@notify')->name('user.molly.notify');

        // RazorPay
        Route::post('/razorpay-submit', 'Payment\Subscription\RazorpayController@store')->name('user.razorpay.submit');
        Route::post('/razorpay-notify', 'Payment\Subscription\RazorpayController@notify')->name('user.razorpay.notify');

        // Authorize.Net
        Route::post('/authorize-submit', 'Payment\Subscription\AuthorizeController@store')->name('user.authorize.submit');

        // Mercadopago
        Route::post('/mercadopago-submit', 'Payment\Subscription\MercadopagoController@store')->name('user.mercadopago.submit');

        // Flutter Wave
        Route::post('/flutter-submit', 'Payment\Subscription\FlutterwaveController@store')->name('user.flutter.submit');

        // 2checkout
        Route::post('/twocheckout-submit', 'Payment\Subscription\TwoCheckoutController@store')->name('user.twocheckout.submit');

        // SSLCommerz
        Route::post('/ssl-submit', 'Payment\Subscription\SslController@store')->name('user.ssl.submit');
        Route::post('/ssl-notify', 'Payment\Subscription\SslController@notify')->name('user.ssl.notify');

        // Voguepay
        Route::post('/voguepay-submit', 'Payment\Subscription\VoguepayController@store')->name('user.voguepay.submit');

        // Manual
        Route::post('/manual-submit', 'Payment\Subscription\ManualPaymentController@store')->name('user.manual.submit');

        // USER SUBSCRIPTION ENDS

        // USER DEPOSIT

        // Deposit & Transaction

        Route::get('/deposit/transactions', 'User\DepositController@transactions')->name('user-transactions-index');
        Route::get('/deposit/transactions/{id}/show', 'User\DepositController@transhow')->name('user-trans-show');
        Route::get('/deposit/index', 'User\DepositController@index')->name('user-deposit-index');
        Route::get('/deposit/create', 'User\DepositController@create')->name('user-deposit-create');

        // Subscription Payment Redirect
        Route::get('/deposit/payment/cancle', 'User\DepositController@paycancle')->name('deposit.payment.cancle');
        Route::get('/deposit/payment/return', 'User\DepositController@payreturn')->name('deposit.payment.return');

        // Paypal
        Route::post('/deposit/paypal-submit', 'Payment\Deposit\PaypalController@store')->name('deposit.paypal.submit');
        Route::get('/deposit/paypal-notify', 'Payment\Deposit\PaypalController@notify')->name('deposit.paypal.notify');

        // Stripe
        Route::post('/deposit/stripe-submit', 'Payment\Deposit\StripeController@store')->name('deposit.stripe.submit');

        // Instamojo
        Route::post('/deposit/instamojo-submit', 'Payment\Deposit\InstamojoController@store')->name('deposit.instamojo.submit');
        Route::get('/deposit/instamojo-notify', 'Payment\Deposit\InstamojoController@notify')->name('deposit.instamojo.notify');

        // Paystack
        Route::post('/deposit/paystack-submit', 'Payment\Deposit\PaystackController@store')->name('deposit.paystack.submit');

        // PayTM
        Route::post('/deposit/paytm-submit', 'Payment\Deposit\PaytmController@store')->name('deposit.paytm.submit');;
        Route::post('/deposit/paytm-notify', 'Payment\Deposit\PaytmController@notify')->name('deposit.paytm.notify');

        // Molly
        Route::post('/deposit/molly-submit', 'Payment\Deposit\MollieController@store')->name('deposit.molly.submit');
        Route::get('/deposit/molly-notify', 'Payment\Deposit\MollieController@notify')->name('deposit.molly.notify');

        // RazorPay
        Route::post('/deposit/razorpay-submit', 'Payment\Deposit\RazorpayController@store')->name('deposit.razorpay.submit');
        Route::post('/deposit/razorpay-notify', 'Payment\Deposit\RazorpayController@notify')->name('deposit.razorpay.notify');

        // Authorize.Net
        Route::post('/deposit/authorize-submit', 'Payment\Deposit\AuthorizeController@store')->name('deposit.authorize.submit');

        // Mercadopago
        Route::post('/deposit/mercadopago-submit', 'Payment\Deposit\MercadopagoController@store')->name('deposit.mercadopago.submit');

        // Flutter Wave
        Route::post('/deposit/flutter-submit', 'Payment\Deposit\FlutterwaveController@store')->name('deposit.flutter.submit');

        // 2checkout
        Route::post('/deposit/twocheckout-submit', 'Payment\Deposit\TwoCheckoutController@store')->name('deposit.twocheckout.submit');

        // SSLCommerz
        Route::post('/deposit/ssl-submit', 'Payment\Deposit\SslController@store')->name('deposit.ssl.submit');
        Route::post('/deposit/ssl-notify', 'Payment\Deposit\SslController@notify')->name('deposit.ssl.notify');

        // Voguepay
        Route::post('/deposit/voguepay-submit', 'Payment\Deposit\VoguepayController@store')->name('deposit.voguepay.submit');

        // Manual
        Route::post('/deposit/manual-submit', 'Payment\Deposit\ManualPaymentController@store')->name('deposit.manual.submit');

        // USER DEPOSIT ENDS

        // User Vendor Send Message

        Route::post('/user/contact', 'User\MessageController@usercontact');
        Route::get('/messages', 'User\MessageController@messages')->name('user-messages');
        Route::get('/message/{id}', 'User\MessageController@message')->name('user-message');
        Route::post('/message/post', 'User\MessageController@postmessage')->name('user-message-post');
        Route::get('/message/{id}/delete', 'User\MessageController@messagedelete')->name('user-message-delete');
        Route::get('/message/load/{id}', 'User\MessageController@msgload')->name('user-vendor-message-load');

        // User Vendor Send Message Ends

        // User Admin Send Message

        // Tickets
        Route::get('admin/tickets', 'User\MessageController@adminmessages')->name('user-message-index');
        // Disputes
        Route::get('admin/disputes', 'User\MessageController@adminDiscordmessages')->name('user-dmessage-index');

        Route::get('admin/message/{id}', 'User\MessageController@adminmessage')->name('user-message-show');
        Route::post('admin/message/post', 'User\MessageController@adminpostmessage')->name('user-message-store');
        Route::get('admin/message/{id}/delete', 'User\MessageController@adminmessagedelete')->name('user-message-delete1');
        Route::post('admin/user/send/message', 'User\MessageController@adminusercontact')->name('user-send-message');
        Route::get('admin/message/load/{id}', 'User\MessageController@messageload')->name('user-message-load');
        // User Admin Send Message Ends

        Route::get('/affilate/program', 'User\UserController@affilate_code')->name('user-affilate-program');
        Route::get('/affilate/history', 'User\UserController@affilate_history')->name('user-affilate-history');

        Route::get('/affilate/withdraw', 'User\WithdrawController@index')->name('user-wwt-index');
        Route::get('/affilate/withdraw/create', 'User\WithdrawController@create')->name('user-wwt-create');
        Route::post('/affilate/withdraw/create', 'User\WithdrawController@store')->name('user-wwt-store');

        // User Favorite Seller

        Route::get('/favorite/seller', 'User\UserController@favorites')->name('user-favorites');
        Route::get('/favorite/{id1}/{id2}', 'User\UserController@favorite')->name('user-favorite');
        Route::get('/favorite/seller/{id}/delete', 'User\UserController@favdelete')->name('user-favorite-delete');

    });

    // ************************************ USER SECTION ENDS**********************************************

// ************************************ FRONT SECTION **********************************************

    Route::post('/item/report', 'Front\CatalogController@report')->name('product.report');

    Route::get('/', 'Front\FrontendController@index')->name('front.index');
    Route::get('/view', 'Front\CartController@view_cart')->name('front.cart-view');
    Route::get('/extras', 'Front\FrontendController@extraIndex')->name('front.extraIndex');
    Route::get('/currency/{id}', 'Front\FrontendController@currency')->name('front.currency');
    Route::get('/language/{id}', 'Front\FrontendController@language')->name('front.language');
    Route::get('/order/track/{id}', 'Front\FrontendController@trackload')->name('front.track.search');
    // BLOG SECTION
    Route::get('/blog', 'Front\FrontendController@blog')->name('front.blog');
    Route::get('/blog/{slug}', 'Front\FrontendController@blogshow')->name('front.blogshow');
    Route::get('/blog/category/{slug}', 'Front\FrontendController@blogcategory')->name('front.blogcategory');
    Route::get('/blog/tag/{slug}', 'Front\FrontendController@blogtags')->name('front.blogtags');
    Route::get('/blog-search', 'Front\FrontendController@blogsearch')->name('front.blogsearch');
    Route::get('/blog/archive/{slug}', 'Front\FrontendController@blogarchive')->name('front.blogarchive');
    // BLOG SECTION ENDS

    // FAQ SECTION
    Route::get('/faq', 'Front\FrontendController@faq')->name('front.faq');
    // FAQ SECTION ENDS

    // CONTACT SECTION
    Route::get('/contact', 'Front\FrontendController@contact')->name('front.contact');
    Route::post('/contact', 'Front\FrontendController@contactemail')->name('front.contact.submit');
    Route::get('/contact/refresh_code', 'Front\FrontendController@refresh_code');
    // CONTACT SECTION  ENDS

    // PRODCT AUTO SEARCH SECTION
    Route::get('/autosearch/product/{slug}', 'Front\FrontendController@autosearch');
    // PRODCT AUTO SEARCH SECTION ENDS

    // CATEGORY SECTION
    Route::get('/categories', 'Front\CatalogController@categories')->name('front.categories');
    Route::get('/category/{category?}/{subcategory?}/{childcategory?}', 'Front\CatalogController@category')->name('front.category');
    // CATEGORY SECTION ENDS

    // TAG SECTION
    Route::get('/tag/{slug}', 'Front\CatalogController@tag')->name('front.tag');
    // TAG SECTION ENDS

    // TAG SECTION
    Route::get('/search', 'Front\CatalogController@search')->name('front.search');
    // TAG SECTION ENDS

    // PRODCT SECTION

    Route::get('/item/{slug}', 'Front\ProductDetailsController@product')->name('front.product');
    Route::get('/afbuy/{slug}', 'Front\ProductDetailsController@affProductRedirect')->name('affiliate.product');
    Route::get('/item/quick/view/{id}/', 'Front\ProductDetailsController@quick')->name('product.quick');
    Route::post('/item/review', 'Front\ProductDetailsController@reviewsubmit')->name('front.review.submit');
    Route::get('/item/view/review/{id}', 'Front\ProductDetailsController@reviews')->name('front.reviews');
    Route::get('/item/view/side/review/{id}', 'Front\ProductDetailsController@sideReviews')->name('front.side.reviews');
    // PRODCT SECTION ENDS

    // COMMENT SECTION
    Route::post('/item/comment/store', 'Front\ProductDetailsController@comment')->name('product.comment');
    Route::post('/item/comment/edit/{id}', 'Front\ProductDetailsController@commentedit')->name('product.comment.edit');
    Route::get('/item/comment/delete/{id}', 'Front\ProductDetailsController@commentdelete')->name('product.comment.delete');
    // COMMENT SECTION ENDS

    // REPORT SECTION
    Route::post('/item/report', 'Front\ProductDetailsController@report')->name('product.report');
    // REPORT SECTION ENDS

    // REPLY SECTION
    Route::post('/item/reply/{id}', 'Front\ProductDetailsController@reply')->name('product.reply');
    Route::post('/item/reply/edit/{id}', 'Front\ProductDetailsController@replyedit')->name('product.reply.edit');
    Route::get('/item/reply/delete/{id}', 'Front\ProductDetailsController@replydelete')->name('product.reply.delete');
    // REPLY SECTION ENDS

    // CART SECTION
    Route::get('/carts/view', 'Front\CartController@cartview');
    Route::get('/carts', 'Front\CartController@cart')->name('front.cart');
    Route::get('/addcart/{id}', 'Front\CartController@addcart')->name('product.cart.add');
    Route::get('/addtocart/{id}', 'Front\CartController@addtocart')->name('product.cart.quickadd');
    Route::get('/addnumcart', 'Front\CartController@addnumcart')->name('details.cart');
    Route::get('/addtonumcart', 'Front\CartController@addtonumcart');
    Route::get('/addbyone', 'Front\CartController@addbyone');
    Route::get('/reducebyone', 'Front\CartController@reducebyone');
    Route::get('/upcolor', 'Front\CartController@upcolor');
    Route::get('/removecart/{id}', 'Front\CartController@removecart')->name('product.cart.remove');
    Route::get('/carts/coupon', 'Front\CouponController@coupon');
    // CART SECTION ENDS

    // COMPARE SECTION
    Route::get('/item/compare/view', 'Front\CompareController@compare')->name('product.compare');
    Route::get('/item/compare/add/{id}', 'Front\CompareController@addcompare')->name('product.compare.add');
    Route::get('/item/compare/remove/{id}', 'Front\CompareController@removecompare')->name('product.compare.remove');
    // COMPARE SECTION ENDS

    // CHECKOUT SECTION
    Route::get('/buy-now/{id}', 'Front\CheckoutController@buynow')->name('front.buynow');
    // Checkout
    Route::get('/checkout', 'Front\CheckoutController@checkout')->name('front.checkout');
    Route::get('/carts/coupon/check', 'Front\CouponController@couponcheck');
    Route::get('/checkout/payment/{slug1}/{slug2}', 'Front\CheckoutController@loadpayment')->name('front.load.payment');
    Route::get('/checkout/payment/return', 'Front\CheckoutController@payreturn')->name('front.payment.return');
    Route::get('/checkout/payment/cancle', 'Front\CheckoutController@paycancle')->name('front.payment.cancle');
    Route::get('/checkout/payment/wallet-check', 'Front\CheckoutController@walletcheck')->name('front.wallet.check');

    // Paypal
    Route::post('/checkout/payment/paypal-submit', 'Payment\Checkout\PaypalController@store')->name('front.paypal.submit');
    Route::get('/checkout/payment/paypal-notify', 'Payment\Checkout\PaypalController@notify')->name('front.paypal.notify');

    // Stripe
    Route::post('/checkout/payment/stripe-submit', 'Payment\Checkout\StripeController@store')->name('front.stripe.submit');

    // Instamojo
    Route::post('/checkout/payment/instamojo-submit', 'Payment\Checkout\InstamojoController@store')->name('front.instamojo.submit');
    Route::get('/checkout/payment/instamojo-notify', 'Payment\Checkout\InstamojoController@notify')->name('front.instamojo.notify');

    // Paystack
    Route::post('/checkout/payment/paystack-submit', 'Payment\Checkout\PaystackController@store')->name('front.paystack.submit');

    // PayTM
    Route::post('/checkout/payment/paytm-submit', 'Payment\Checkout\PaytmController@store')->name('front.paytm.submit');;
    Route::post('/checkout/payment/paytm-notify', 'Payment\Checkout\PaytmController@notify')->name('front.paytm.notify');

    // Molly
    Route::post('/checkout/payment/molly-submit', 'Payment\Checkout\MollieController@store')->name('front.molly.submit');
    Route::get('/checkout/payment/molly-notify', 'Payment\Checkout\MollieController@notify')->name('front.molly.notify');

    // RazorPay
    Route::post('/checkout/payment/razorpay-submit', 'Payment\Checkout\RazorpayController@store')->name('front.razorpay.submit');
    Route::post('/checkout/payment/razorpay-notify', 'Payment\Checkout\RazorpayController@notify')->name('front.razorpay.notify');

    // Authorize.Net
    Route::post('/checkout/payment/authorize-submit', 'Payment\Checkout\AuthorizeController@store')->name('front.authorize.submit');

    // Mercadopago
    Route::post('/checkout/payment/mercadopago-submit', 'Payment\Checkout\MercadopagoController@store')->name('front.mercadopago.submit');

    // Flutter Wave
    Route::post('/checkout/payment/flutter-submit', 'Payment\Checkout\FlutterwaveController@store')->name('front.flutter.submit');

    // 2checkout
    Route::post('/checkout/payment/twocheckout-submit', 'Payment\Checkout\TwoCheckoutController@store')->name('front.twocheckout.submit');

    // SSLCommerz
    Route::post('/checkout/payment/ssl-submit', 'Payment\Checkout\SslController@store')->name('front.ssl.submit');
    Route::post('/checkout/payment/ssl-notify', 'Payment\Checkout\SslController@notify')->name('front.ssl.notify');

    // Voguepay
    Route::post('/checkout/payment/voguepay-submit', 'Payment\Checkout\VoguepayController@store')->name('front.voguepay.submit');

    // Wallet
    Route::post('/checkout/payment/wallet-submit', 'Payment\Checkout\WalletPaymentController@store')->name('front.wallet.submit');

    // Manual
    Route::post('/checkout/payment/manual-submit', 'Payment\Checkout\ManualPaymentController@store')->name('front.manual.submit');

    // Cash On Delivery
    Route::post('/checkout/payment/cod-submit', 'Payment\Checkout\CashOnDeliveryController@store')->name('front.cod.submit');

    // Flutterwave Notify Routes

    // Deposit
    Route::post('/dflutter/notify', 'Payment\Deposit\FlutterwaveController@notify')->name('deposit.flutter.notify');

    // Subscription
    Route::post('/uflutter/notify', 'Payment\Subscription\FlutterwaveController@notify')->name('user.flutter.notify');

    // Checkout
    Route::post('/cflutter/notify', 'Payment\Checkout\FlutterwaveController@notify')->name('front.flutter.notify');

    // CHECKOUT SECTION ENDS

    // VENDOR SECTION

    Route::post('/vendor/contact', 'Front\VendorController@vendorcontact')->name('front.vendor.contact');

    // VENDOR SECTION ENDS

    // SUBSCRIBE SECTION

    Route::post('/subscriber/store', 'Front\FrontendController@subscribe')->name('front.subscribe');

    // SUBSCRIBE SECTION ENDS

    // LOGIN WITH FACEBOOK OR GOOGLE SECTION
    Route::get('auth/{provider}', 'Auth\User\SocialRegisterController@redirectToProvider')->name('social-provider');
    Route::get('auth/{provider}/callback', 'Auth\User\SocialRegisterController@handleProviderCallback');
    // LOGIN WITH FACEBOOK OR GOOGLE SECTION ENDS

    //  CRONJOB

    Route::get('/vendor/subscription/check', 'Front\FrontendController@subcheck');

    // CRONJOB ENDS

    Route::post('the/genius/ocean/2441139', 'Front\FrontendController@subscription');
    Route::get('finalize', 'Front\FrontendController@finalize');
    Route::get('update-finalize', 'Front\FrontendController@updateFinalize');


    // VENDOR AND PAGE SECTION
    Route::get('/country/tax/check', 'Front\CartController@country_tax');
    Route::get('/{slug}', 'Front\VendorController@index')->name('front.vendor');

    // VENDOR AND PAGE SECTION ENDS

// ************************************ FRONT SECTION ENDS**********************************************

});
