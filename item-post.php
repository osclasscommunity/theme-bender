<?php
osc_add_hook('header', 'mtx_follow_construct');
mtx_add_body_class('ad ad-post');


if(Params::getParam('action') == 'item_add') {
    $action = 'item_add_post';
    $edit = 0;
} else {
    $action = 'item_edit_post';
    $edit = 1;
}

osc_current_web_theme_path('header.php');

ItemForm::location_javascript();
?>
<div class="container-fluid bg-lighter">
    <div class="container">
        <div class="row">
            <div class="col-12 mt-5 mb-3">
                <h1 class="title cl-accent-dark"><?php _e('Post an ad', 'matrix'); ?></h1>
                <p class="subtitle cl-darker"><?php _e('Publish your ad on our site and get hundreds of views.', 'matrix'); ?></p>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-9">
                <section class="adpost-category border">
                    <h3 class="bg-darker"><?php _e('Category', 'matrix'); ?></h3>
                    <label><?php _e('Pick a category that best suits your product or service.', 'matrix'); ?></label>
                    <?php ItemForm::category_select(null, null, __('Select a category', 'matrix')); ?>
                </section>
                <section class="adpost-description border">
                    <h3 class="bg-darker"><?php _e('Description', 'matrix'); ?></h3>
                    <label><?php _e('Describe your product or service to the seller.', 'matrix'); ?></label>
                    <?php ItemFormMatrix::title_description(); ?>
                </section>
            </div>
        </div>
    </div>
</div>

<div class="form-container form-horizontal">
    <div class="resp-wrapper">
        <div class="header">
            <h1><?php _e('Publish a listing', 'matrix'); ?></h1>
        </div>
        <ul id="error_list"></ul>
        <form name="item" action="<?php echo osc_base_url(true);?>" method="post" enctype="multipart/form-data" id="item-post">
            <fieldset>
            <input type="hidden" name="action" value="<?php echo $action; ?>" />
                <input type="hidden" name="page" value="item" />
            <?php if($edit){ ?>
                <input type="hidden" name="id" value="<?php echo osc_item_id();?>" />
                <input type="hidden" name="secret" value="<?php echo osc_item_secret();?>" />
            <?php } ?>
                <h2><?php _e('General Information', 'matrix'); ?></h2>
                <div class="control-group">
                    <label class="control-label" for="select_1"><?php _e('Category', 'matrix'); ?></label>
                    <div class="controls">
                        <?php ItemForm::category_select(null, null, __('Select a category', 'matrix')); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="title[<?php echo osc_current_user_locale(); ?>]"><?php _e('Title', 'matrix'); ?></label>
                    <div class="controls">
                        <?php ItemForm::title_input('title',osc_current_user_locale(), osc_esc_html( mtx_item_title() )); ?>
                    </div>
                </div>
                <div class="control-group">
                    <label class="control-label" for="description[<?php echo osc_current_user_locale(); ?>]"><?php _e('Description', 'matrix'); ?></label>
                    <div class="controls">
                        <?php ItemForm::description_textarea('description',osc_current_user_locale(), osc_esc_html( mtx_item_description() )); ?>
                    </div>
                </div>
                <?php if( osc_price_enabled_at_items() ) { ?>
                <div class="control-group control-group-price">
                    <label class="control-label" for="price"><?php _e('Price', 'matrix'); ?></label>
                    <div class="controls">
                        <?php ItemForm::price_input_text(); ?>
                        <?php ItemForm::currency_select(); ?>
                    </div>
                </div>
                <?php } ?>
                <?php if( osc_images_enabled_at_items() ) {
                    ItemForm::ajax_photos();
                 } ?>
                <div class="box location">
                    <h2><?php _e('Listing Location', 'matrix'); ?></h2>
                    <?php if(count(osc_get_countries()) > 1) { ?>
                    <div class="control-group">
                        <label class="control-label" for="country"><?php _e('Country', 'matrix'); ?></label>
                        <div class="controls">
                            <?php ItemForm::country_select(osc_get_countries(), osc_user()); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="regionId"><?php _e('Region', 'matrix'); ?></label>
                        <div class="controls">
                            <?php
                            if (mtx_default_location_show_as() == 'dropdown') {
                                if($edit) {
                                    ItemForm::region_select(osc_get_regions(osc_item_country_code()), osc_item());
                                } else {
                                    ItemForm::region_select(osc_get_regions(osc_user_field('fk_c_country_code')), osc_user());
                                }
                            } else {
                                if($edit) {
                                    ItemForm::region_text(osc_item());
                                } else {
                                    ItemForm::region_text(osc_user());
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                    } else {
                        $aCountries = osc_get_countries();
                        $aRegions = osc_get_regions($aCountries[0]['pk_c_code']);
                        ?>
                    <input type="hidden" id="countryId" name="countryId" value="<?php echo osc_esc_html($aCountries[0]['pk_c_code']); ?>"/>
                    <div class="control-group">
                        <label class="control-label" for="region"><?php _e('Region', 'matrix'); ?></label>
                        <div class="controls">
                          <?php
                            if (mtx_default_location_show_as() == 'dropdown') {
                                if($edit) {
                                    ItemForm::region_select($aRegions, osc_item());
                                } else {
                                    ItemForm::region_select($aRegions, osc_user());
                                }
                            } else {
                                if($edit) {
                                    ItemForm::region_text(osc_item());
                                } else {
                                    ItemForm::region_text(osc_user());
                                }
                            }
                            ?>
                        </div>
                    </div>
                    <?php } ?>

                    <div class="control-group">
                        <label class="control-label" for="city"><?php _e('City', 'matrix'); ?></label>
                        <div class="controls">
                            <?php
                            if (mtx_default_location_show_as() == 'dropdown') {
                                if($edit) {
                                    ItemForm::city_select(null, osc_item());
                                } else { // add new item
                                    ItemForm::city_select(osc_get_cities(osc_user_region_id()), osc_user());
                                }
                            } else {
                                ItemForm::city_text(osc_user());
                            }
                            ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="cityArea"><?php _e('City Area', 'matrix'); ?></label>
                        <div class="controls">
                            <?php ItemForm::city_area_text(osc_user()); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="address"><?php _e('Address', 'matrix'); ?></label>
                        <div class="controls">
                          <?php ItemForm::address_text(osc_user()); ?>
                        </div>
                    </div>
                </div>
                <!-- seller info -->
                <?php if(!osc_is_web_user_logged_in() ) { ?>
                <div class="box seller_info">
                    <h2><?php _e("Seller's information", 'matrix'); ?></h2>
                    <div class="control-group">
                        <label class="control-label" for="contactName"><?php _e('Name', 'matrix'); ?></label>
                        <div class="controls">
                            <?php ItemForm::contact_name_text(); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <label class="control-label" for="contactEmail"><?php _e('E-mail', 'matrix'); ?></label>
                        <div class="controls">
                            <?php ItemForm::contact_email_text(); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <div class="controls checkbox">
                            <?php ItemForm::show_email_checkbox(); ?> <label for="showEmail"><?php _e('Show e-mail on the listing page', 'matrix'); ?></label>
                        </div>
                    </div>
                </div>
                <?php
                }
                if($edit) {
                    ItemForm::plugin_edit_item();
                } else {
                    ItemForm::plugin_post_item();
                }
                ?>
                <div class="control-group">
                    <?php if( osc_recaptcha_items_enabled() ) { ?>
                        <div class="controls">
                            <?php osc_show_recaptcha(); ?>
                        </div>
                    <?php }?>
                    <div class="controls">
                        <button type="submit" class="ui-button ui-button-middle ui-button-main"><?php if($edit) { _e("Update", 'matrix'); } else { _e("Publish", 'matrix'); } ?></button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>
</div>
<script type="text/javascript">
    $('#price').bind('hide-price', function(){
        $('.control-group-price').hide();
    });

    $('#price').bind('show-price', function(){
        $('.control-group-price').show();
    });

    <?php if(osc_locale_thousands_sep()!='' || osc_locale_dec_point() != '') { ?>
        $().ready(function(){
            $("#price").blur(function(event) {
                var price = $("#price").prop("value");
                <?php if(osc_locale_thousands_sep()!='') { ?>
                while(price.indexOf('<?php echo osc_esc_js(osc_locale_thousands_sep());  ?>')!=-1) {
                    price = price.replace('<?php echo osc_esc_js(osc_locale_thousands_sep());  ?>', '');
                }
                <?php }; ?>
                <?php if(osc_locale_dec_point()!='') { ?>
                var tmp = price.split('<?php echo osc_esc_js(osc_locale_dec_point())?>');
                if(tmp.length>2) {
                    price = tmp[0]+'<?php echo osc_esc_js(osc_locale_dec_point())?>'+tmp[1];
                }
                <?php }; ?>
                $("#price").prop("value", price);
            });
        });
    <?php }; ?>
</script>
<?php osc_current_web_theme_path('footer.php'); ?>
