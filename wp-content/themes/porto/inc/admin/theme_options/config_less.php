// Porto Config Less File
// Created at <?php echo date("Y-m-d H:i:s") ?>

<?php
$b = porto_check_theme_options();
$dark = $b['css-type'] == 'dark'
?>

@dark: <?php echo $dark ? '1' : '0' ?>;

// Border radius
<?php if ($b['border-radius']) : ?>
    @border_base: 2px;
    @border_medium: 2px;
    
    @searchform_border: 20px;
    @searchform_border_large: 25px;
<?php else : ?>
    @border_base: 0;
    @border_medium: 0;
<?php endif ?><?php if ($b['search-border-radius']) : ?>
    @searchform_border: 20px;
    @searchform_border_large: 25px;

<?php else : ?>	
    @searchform_border: 0;
    @searchform_border_large: 0;

<?php endif ?>

// Button Style
@button_style_borders: <?php echo porto_get_button_style() == 'btn-borders' ? '1' : '0' ?>;
@button_style_3d: <?php echo porto_get_button_style() == 'btn-3d' ? '1' : '0' ?>;

// Skin
@skinColor: <?php echo $b['skin-color'] ?>;
@container_width: <?php echo $b['container-width'] ?>px;
@grid_gutter_width: <?php echo $b['grid-gutter-width'] ?>px;
@screen_large: <?php echo $b['container-width'] + ($b['grid-gutter-width'] - 1) ?>px;

// Color Variables
@color-primary: @skinColor;
@color-primary-inverse: <?php echo $b['skin-color-inverse'] ?>;

@color-secondary: <?php echo $b['secondary-color'] ?>;
@color-secondary-inverse: <?php echo $b['secondary-color-inverse'] ?>;

@color-tertiary: <?php echo $b['tertiary-color'] ?>;
@color-tertiary-inverse: <?php echo $b['tertiary-color-inverse'] ?>;

@color-quaternary: <?php echo $b['quaternary-color'] ?>;
@color-quaternary-inverse: <?php echo $b['quaternary-color-inverse'] ?>;

@color-dark: <?php echo $b['dark-color'] ?>;
@color-dark-inverse: <?php echo $b['dark-color-inverse'] ?>;

@color-light: <?php echo $b['light-color'] ?>;
@color-light-inverse: <?php echo $b['light-color-inverse'] ?>;

@social-color: <?php echo $b['social-color'] ? '1' : '0' ?>;

// Typography
@body_font_family: <?php echo $b['body-font']['font-family'] ?>;
@body_font_weight: <?php echo $b['body-font']['font-weight'] ?>;
@body_font_size: <?php echo $b['body-font']['font-size'] ?>;
@body_line_height: <?php echo $b['body-font']['line-height'] ?>;

@body_letter_spacing: <?php echo  $b['body-font']['letter-spacing']  ?>;

@body_color: <?php echo $b['body-font']['color'] ?>;

@body_mobile_font_size_scale: <?php echo ((float)$b['body-font']['font-size'] == 0 || (float)$b['body-mobile-font']['font-size'] == 0) ? 1 : ((float)$b['body-mobile-font']['font-size'] / (float)$b['body-font']['font-size']) ?>;
@body_mobile_line_height_scale: <?php echo ((float)$b['body-font']['line-height'] == 0 || (float)$b['body-mobile-font']['line-height'] == 0) ? 1 : ((float)$b['body-mobile-font']['line-height'] / (float)$b['body-font']['line-height']) ?>;@body_mobile_letter_spacing: <?php echo $b['body-mobile-font']['letter-spacing'] ?>;

@alt_font_family: <?php echo $b['alt-font']['font-family'] ?>;
@alt_font_weight: <?php echo $b['alt-font']['font-weight'] ?>;

@logo_font_family: <?php echo $b['logo-font']['font-family'] ?>;
@logo_font_weight: <?php echo $b['logo-font']['font-weight'] ?>;
@logo_font_size: <?php echo $b['logo-font']['font-size'] ?>;
@logo_line_height: <?php echo $b['logo-font']['line-height'] ?>;

@logo_letter_spacing: <?php echo  $b['logo-font']['letter-spacing']  ?>;

@logo_color: <?php echo $b['logo-font']['color'] ?>;

@h1_font_family: <?php echo $b['h1-font']['font-family'] ?>;
@h1_font_weight: <?php echo $b['h1-font']['font-weight'] ?>;
@h1_font_size: <?php echo $b['h1-font']['font-size'] ?>;
@h1_line_height: <?php echo $b['h1-font']['line-height'] ?>;

@h1_letter_spacing: <?php echo $b['h1-font']['letter-spacing'] ?>;

@h1_color: <?php echo $b['h1-font']['color'] ?>;

@h2_font_family: <?php echo $b['h2-font']['font-family'] ?>;
@h2_font_weight: <?php echo $b['h2-font']['font-weight'] ?>;
@h2_font_size: <?php echo $b['h2-font']['font-size'] ?>;
@h2_line_height: <?php echo $b['h2-font']['line-height'] ?>;
@h2_letter_spacing: <?php echo $b['h2-font']['letter-spacing'] ?>;@h2_color: <?php echo $b['h2-font']['color'] ?>;

@h3_font_family: <?php echo $b['h3-font']['font-family'] ?>;
@h3_font_weight: <?php echo $b['h3-font']['font-weight'] ?>;
@h3_font_size: <?php echo $b['h3-font']['font-size'] ?>;
@h3_line_height: <?php echo $b['h3-font']['line-height'] ?>;
@h3_letter_spacing: <?php echo $b['h3-font']['letter-spacing'] ?>;@h3_color: <?php echo $b['h3-font']['color'] ?>;

@h4_font_family: <?php echo $b['h4-font']['font-family'] ?>;
@h4_font_weight: <?php echo $b['h4-font']['font-weight'] ?>;
@h4_font_size: <?php echo $b['h4-font']['font-size'] ?>;
@h4_line_height: <?php echo $b['h4-font']['line-height'] ?>;
@h4_letter_spacing: <?php echo $b['h4-font']['letter-spacing'] ?>;@h4_color: <?php echo $b['h4-font']['color'] ?>;

@h5_font_family: <?php echo $b['h5-font']['font-family'] ?>;
@h5_font_weight: <?php echo $b['h5-font']['font-weight'] ?>;
@h5_font_size: <?php echo $b['h5-font']['font-size'] ?>;
@h5_line_height: <?php echo $b['h5-font']['line-height'] ?>;
@h5_letter_spacing: <?php echo $b['h5-font']['letter-spacing'] ?>;@h5_color: <?php echo $b['h5-font']['color'] ?>;

@h6_font_family: <?php echo $b['h6-font']['font-family'] ?>;
@h6_font_weight: <?php echo $b['h6-font']['font-weight'] ?>;
@h6_font_size: <?php echo $b['h6-font']['font-size'] ?>;
@h6_line_height: <?php echo $b['h6-font']['line-height'] ?>;

@h6_letter_spacing: <?php echo $b['h6-font']['letter-spacing'] ?>;

@h6_color: <?php echo $b['h6-font']['color'] ?>;

@menu_font_family: <?php echo $b['menu-font']['font-family'] ?>;
@menu_font_weight: <?php echo $b['menu-font']['font-weight'] ?>;
@menu_font_size: <?php echo $b['menu-font']['font-size'] ?>;
@menu_line_height: <?php echo $b['menu-font']['line-height'] ?>;

@menu_letter_spacing: <?php echo $b['menu-font']['letter-spacing'] ?>;@menu_md_font_size: <?php echo $b['menu-font-md']['font-size'] ?>;
@menu_md_line_height: <?php echo $b['menu-font-md']['line-height'] ?>;

@menu_md_letter_spacing: <?php echo $b['menu-font-md']['letter-spacing'] ?>;

@menu_text_transform: <?php echo $b['menu-text-transform'] ?>;

@menu_side_font_family: <?php echo $b['menu-side-font']['font-family'] ?>;
@menu_side_font_weight: <?php echo $b['menu-side-font']['font-weight'] ?>;
@menu_side_font_size: <?php echo $b['menu-side-font']['font-size'] ?>;
@menu_side_line_height: <?php echo $b['menu-side-font']['line-height'] ?>;

@menu_side_letter_spacing: <?php echo $b['menu-side-font']['letter-spacing'] ?>;@menu_popup_font_family: <?php echo $b['menu-popup-font']['font-family'] ?>;
@menu_popup_font_weight: <?php echo $b['menu-popup-font']['font-weight'] ?>;
@menu_popup_font_size: <?php echo $b['menu-popup-font']['font-size'] ?>;
@menu_popup_line_height: <?php echo $b['menu-popup-font']['line-height'] ?>;

@menu_popup_letter_spacing: <?php echo $b['menu-popup-font']['letter-spacing'] ?>;// Backgrounds

@mobile_menu_toggle_text_color: <?php echo $b['mobile-menu-toggle-text-color'] ?>;
@mobile_menu_toggle_bg_color: <?php echo ( empty( $b['mobile-menu-toggle-bg-color'] ) ? $b['skin-color'] : $b['mobile-menu-toggle-bg-color'] ); ?>;// Mobile Panel

@panel_hover_color: <?php echo $b['panel-link-color']['hover'] ?>;

// Header Top
@header_top_bg_color: <?php echo $b['header-top-bg-color'] ?>;
@header_top_text_color: <?php echo $b['header-top-text-color'] ?>;
@header_top_link_color: <?php echo $b['header-top-link-color']['regular'] ?>;
@header_top_hover_color: <?php echo $b['header-top-link-color']['hover'] ?>;
@header_top_bottom_border_width: <?php echo $b['header-top-bottom-border']['border-top'] ?>;
@header_top_bottom_border_color: <?php echo $b['header-top-bottom-border']['border-color'] ?>;@header_top_menu_padding_top: <?php echo porto_config_value($b['header-top-menu-padding']['padding-top']) ?>px;
@header_top_menu_padding_right: <?php echo porto_config_value($b['header-top-menu-padding']['padding-right']) ?>px;
@header_top_menu_padding_bottom: <?php echo porto_config_value($b['header-top-menu-padding']['padding-bottom']) ?>px;
@header_top_menu_padding_left: <?php echo porto_config_value($b['header-top-menu-padding']['padding-left']) ?>px;

<?php if ( $b['header-top-menu-hide-sep']) : ?>
@header_top_menu_hide_sep: true;
<?php else: ?>
@header_top_menu_hide_sep: false;
<?php endif ?>// Header

@header_border_top_width: <?php echo $b['header-top-border']['border-top'] ?>;
@header_border_top_color: <?php echo $b['header-top-border']['border-color'] ?>;
@header_margin_top: <?php echo porto_config_value($b['header-margin']['margin-top']) ?>px;
@header_margin_bottom: <?php echo porto_config_value($b['header-margin']['margin-bottom']) ?>px;
<?php if (is_rtl()) : ?>
@header_margin_left: <?php echo porto_config_value($b['header-margin']['margin-right']) ?>px;
@header_margin_right: <?php echo porto_config_value($b['header-margin']['margin-left']) ?>px;
<?php else : ?>
@header_margin_right: <?php echo porto_config_value($b['header-margin']['margin-right']) ?>px;
@header_margin_left: <?php echo porto_config_value($b['header-margin']['margin-left']) ?>px;
<?php endif; ?>

@header_bg_color: <?php echo $b['header-bg']['background-color'] ?>;

<?php if( $porto_settings['show-sticky-logo'] ): ?>
@sticky_header_logo: 1;
<?php else: ?>
@sticky_header_logo: 0;
<?php endif; ?>

@header_text_color: <?php echo $b['header-text-color'] ?>;
@header_link_color: <?php echo $b['header-link-color']['regular'] ?>;
@header_hover_color: <?php echo $b['header-link-color']['hover'] ?>;

@searchform_opacity: <?php echo ((int)$b['searchform-opacity']) ? (int)$b['searchform-opacity'] : 50 ?>%;
@menuwrap_opacity: <?php echo ((int)$b['menuwrap-opacity']) ? (int)$b['menuwrap-opacity'] : 30 ?>%;
@menu_opacity: <?php echo ((int)$b['menu-opacity']) ? (int)$b['menu-opacity'] : 30 ?>%;

// Side Social, Copyright
@side_social_bg_color: <?php echo $b['side-social-bg-color'] ?>;
@side_social_color: <?php echo $b['side-social-color'] ?>;
@side_copyright_color: <?php echo $b['side-copyright-color'] ?>;

// Switcher
@header_switcher_bg_color: <?php echo $b['switcher-bg-color'] ?>;
@header_switcher_hbg_color: <?php echo $b['switcher-hbg-color'] ?>;
@header_switcher_link_color: <?php echo $b['switcher-link-color']['regular'] ?>;
@header_switcher_hover_color: <?php echo $b['switcher-link-color']['hover'] ?>;

// Searchform
@searchform_bg_color: <?php echo $b['searchform-bg-color'] ?>;
@searchform_border_color: <?php echo $b['searchform-border-color'] ?>;
@searchform_popup_border_color: <?php echo $b['searchform-popup-border-color'] ?>;
@searchform_text_color: <?php echo $b['searchform-text-color'] ?>;
@searchform_hover_color: <?php echo $b['searchform-hover-color'] ?>;
@sticky_searchform_popup_border_color: <?php echo $b['sticky-searchform-popup-border-color'] ?>;
@sticky_searchform_toggle_text_color: <?php echo $b['sticky-searchform-toggle-text-color'] ?>;
@sticky_searchform_toggle_hover_color: <?php echo $b['sticky-searchform-toggle-hover-color'] ?>;
<?php if( $porto_settings['show-sticky-searchform'] ): ?>
@sticky_header_searchform: 1;
<?php else: ?>
@sticky_header_searchform: 0;
<?php endif; ?>
// Mini Cart
<?php if( $porto_settings['show-sticky-minicart'] ): ?>
@sticky_header_minicart: 1;
<?php else: ?>
@sticky_header_minicart: 0;
<?php endif; ?>
// Main Menu
@main_menu_wrapper_bg_color: <?php echo $b['mainmenu-wrap-bg-color'] ?>;
<?php if (is_rtl()) : ?>
@main_menu_wrapper_padding: <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-left']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-right']) ?>px;
<?php else : ?>
@main_menu_wrapper_padding: <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-right']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding']['padding-left']) ?>px;
<?php endif ?>
<?php if (is_rtl()) : ?>
    @main_menu_wrapper_padding_sticky: <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-left']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-right']) ?>px;
<?php else : ?>
    @main_menu_wrapper_padding_sticky: <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-top']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-right']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-bottom']) ?>px <?php echo porto_config_value($b['mainmenu-wrap-padding-sticky']['padding-left']) ?>px;
<?php endif ?>
@main_menu_bg_color: <?php echo $b['mainmenu-bg-color'] ?>;
@main_menu_popup_border: <?php echo $b['mainmenu-popup-border'] ? '1' : '0' ?>;
@main_menu_popup_border_color: <?php echo $b['mainmenu-popup-border-color'] ?>;
@main_menu_popup_bg_color: <?php echo $b['mainmenu-popup-bg-color'] ?>;
@main_menu_popup_heading_color: <?php echo $b['mainmenu-popup-heading-color'] ?>;
@main_menu_popup_link_color: <?php echo $b['mainmenu-popup-text-color']['regular'] ?>;
@main_menu_popup_hover_color: <?php echo $b['mainmenu-popup-text-color']['hover'] ?>;
@main_menu_popup_link_hbg_color: <?php echo $b['mainmenu-popup-text-hbg-color'] ?>;
@main_menu_level1_link_color: <?php echo $b['mainmenu-toplevel-link-color']['regular'] ?>;
@main_menu_level1_hover_color: <?php echo $b['mainmenu-toplevel-link-color']['hover'] ?>;

@main_menu_level1_active_color: <?php echo $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-alink-color'] : $b['mainmenu-toplevel-link-color']['hover'] ?>;
@main_menu_level1_hbg_color: <?php echo $b['mainmenu-toplevel-hbg-color'] ?>;
@main_menu_level1_abg_color: <?php echo $b['mainmenu-toplevel-config-active'] ? $b['mainmenu-toplevel-abg-color'] : $b['mainmenu-toplevel-hbg-color'] ?>;
<?php if (is_rtl()) : ?>
@main_menu_level1_padding1_right: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-left']) ?>px;
@main_menu_level1_padding1_left: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-right']) ?>px;
<?php else : ?>
@main_menu_level1_padding1_left: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-left']) ?>px;
@main_menu_level1_padding1_right: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-right']) ?>px;
<?php endif ?>
@main_menu_level1_padding1_top: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-top']) ?>px;
@main_menu_level1_padding1_bottom: <?php echo porto_config_value($b['mainmenu-toplevel-padding1']['padding-bottom']) ?>px;
<?php if (is_rtl()) : ?>
@main_menu_level1_padding2_right: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-left']) ?>px;
@main_menu_level1_padding2_left: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-right']) ?>px;
<?php else : ?>
@main_menu_level1_padding2_left: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-left']) ?>px;
@main_menu_level1_padding2_right: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-right']) ?>px;
<?php endif ?>
@main_menu_level1_padding2_top: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-top']) ?>px;
@main_menu_level1_padding2_bottom: <?php echo porto_config_value($b['mainmenu-toplevel-padding2']['padding-bottom']) ?>px;
@main_menu_narrow_type: <?php echo isset($b['mainmenu-popup-narrow-type']) && $b['mainmenu-popup-narrow-type'] ? '1' : '0' ?>;
@main_menu_tip_bg_color: <?php echo $b['mainmenu-tip-bg-color'] ?>;
@main_menu_custom_text_color: <?php echo $b['menu-custom-text-color'] ?>;
@main_menu_custom_link_color: <?php echo $b['menu-custom-link']['regular'] ?>;
@main_menu_custom_link_hcolor: <?php echo $b['menu-custom-link']['hover'] ?>;

<?php /*@sticky_menu_bg_color: <?php if ($b['mainmenu-bg-color'] && $b['mainmenu-bg-color'] != 'transparent') echo $b['mainmenu-bg-color']; else if ($b['mainmenu-wrap-bg-color'] && $b['mainmenu-wrap-bg-color'] != 'transparent') echo $b['mainmenu-wrap-bg-color']; else echo $b['sticky-header-bg']['background-color'] ?>;
*/ ?>
<?php if( $porto_settings['show-sticky-menu-custom-content'] ): ?>
@sticky_header_menu_custom_content: 1;
<?php else: ?>
@sticky_header_menu_custom_content: 0;
<?php endif; ?>// Footer

@footer_heading_color: <?php echo $b['footer-heading-color'] ?>;
@footer_text_color: <?php echo $b['footer-text-color'] ?>;
@footer_link_color: <?php echo $b['footer-link-color']['regular'] ?>;
@footer_link_hcolor: <?php echo $b['footer-link-color']['hover'] ?>;
@footer_ribbon_bg_color: <?php echo $b['footer-ribbon-bg-color'] ?>;
@footer_ribbon_text_color: <?php echo $b['footer-ribbon-text-color'] ?>;

@footer_bottom_link_color: <?php echo $b['footer-bottom-link-color']['regular'] ?>;
@footer_bottom_link_hcolor: <?php echo $b['footer-bottom-link-color']['hover'] ?>;
@footer_opacity: <?php echo ((int)$b['footer-opacity']) ? (int)$b['footer-opacity'] : 80 ?>%;
@footer_social_bg_color: <?php echo $b['footer-social-bg-color'] ?>;
@footer_social_link_color: <?php echo $b['footer-social-link-color'] ?>;

// Breadcrumbs
@breadcrumbs_bg_color: <?php echo $b['breadcrumbs-bg']['background-color'] ?>;

@breadcrumbs_border_top_width: <?php echo $b['breadcrumbs-top-border']['border-top'] ?>;
@breadcrumbs_border_top_color: <?php echo $b['breadcrumbs-top-border']['border-color'] ?>;
@breadcrumbs_border_bottom_width: <?php echo $b['breadcrumbs-bottom-border']['border-top'] ?>;
@breadcrumbs_border_bottom_color: <?php echo $b['breadcrumbs-bottom-border']['border-color'] ?>;
@breadcrumbs_text_color: <?php echo $b['breadcrumbs-text-color'] ?>;
@breadcrumbs_link_color: <?php echo $b['breadcrumbs-link-color'] ?>;
@breadcrumbs_title_color: <?php echo $b['breadcrumbs-title-color'] ?>;
@breadcrumbs_subtitle_color: <?php echo $b['breadcrumbs-subtitle-color'] ?>;
<?php if (is_rtl()) : ?>
@breadcrumbs_padding: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-top']) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-left']) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-bottom']) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-right']) ?>px;
@breadcrumbs_padding_right: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-left']) ?>px;
@breadcrumbs_padding_left: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-right']) ?>px;
<?php else : ?>
@breadcrumbs_padding: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-top']) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-right']) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-bottom']) ?>px <?php echo porto_config_value($b['breadcrumbs-padding']['padding-left']) ?>px;
@breadcrumbs_padding_left: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-left']) ?>px;
@breadcrumbs_padding_right: <?php echo porto_config_value($b['breadcrumbs-padding']['padding-right']) ?>px;
<?php endif ?>
<?php if (is_rtl()) : ?>
    @breadcrumbs_subtitle_margin: <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-top']) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-left']) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-bottom']) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-right']) ?>px;
<?php else : ?>
    @breadcrumbs_subtitle_margin: <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-top']) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-right']) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-bottom']) ?>px <?php echo porto_config_value($b['breadcrumbs-subtitle-margin']['margin-left']) ?>px;
<?php endif ?>

// Container Width
<?php
    $header_bg_empty = ( empty( $b['header-bg']['background-color'] ) || $b['header-bg']['background-color'] == 'transparent' ) && ( empty( $b['header-bg']['background-image'] ) || $b['header-bg']['background-image'] == 'none' );
    $breadcrumb_bg_empty = ( empty( $b['breadcrumbs-bg']['background-color'] ) || $b['breadcrumbs-bg']['background-color'] == 'transparent' ) && ( empty( $b['breadcrumbs-bg']['background-image'] ) || $b['breadcrumbs-bg']['background-image'] == 'none' );
    $content_bg_empty = ( empty( $b['content-bg']['background-color'] ) || $b['content-bg']['background-color'] == 'transparent' ) && ( empty( $b['content-bg']['background-image'] ) || $b['content-bg']['background-image'] == 'none' );
    $footer_bg_empty = ( empty( $b['footer-bg']['background-color'] ) || $b['footer-bg']['background-color'] == 'transparent' ) && ( empty( $b['footer-bg']['background-image'] ) || $b['footer-bg']['background-image'] == 'none' );
?>
@calc_banner_width: <?php echo $header_bg_empty && !$content_bg_empty ? '1' : '0' ?>;
@calc_breadcrumbs_width: <?php echo $header_bg_empty && !$breadcrumb_bg_empty ? '1' : '0' ?>;
@calc_content_width: <?php echo $header_bg_empty && !$content_bg_empty ? '1' : '0' ?>;
@calc_footer_width: <?php echo $header_bg_empty && !$footer_bg_empty ? '1' : '0' ?>;

@color-hot: <?php echo $b['hot-color'] ?>;
@color-hot-inverse: <?php echo $b['hot-color-inverse'] ?>;