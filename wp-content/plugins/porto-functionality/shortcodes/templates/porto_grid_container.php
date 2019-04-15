<?php
$output = $grid_size = $gutter_size = $max_width = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(shortcode_atts(array(
    'grid_size' => '0',
    'gutter_size' => '2%',
    'max_width' => '767px',
    'animation_type' => '',
    'animation_duration' => 1000,
    'animation_delay' => 0,
    'el_class' => ''
), $atts));
if ( !$gutter_size ) {
    $gutter_size = '0%';
}
$validCharacters = 'abcdefghijklmnopqrstuvwxyz0123456789';
$rand = '';
$length = 32;
for ($n = 1; $n < $length; $n++) {
    $whichCharacter = rand(0, strlen($validCharacters)-1);
    $rand .= $validCharacters{$whichCharacter};
}

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-grid-container"';
if ($animation_type) {
    $output .= ' data-appear-animation="'.$animation_type.'"';
    if ($animation_delay)
        $output .= ' data-appear-animation-delay="'.$animation_delay.'"';
    if ($animation_duration && $animation_duration != 1000)
        $output .= ' data-appear-animation-duration="'.$animation_duration.'"';
}
$output .= '>';
preg_match_all( '/\[porto_grid_item\s[^]]*width="([^]"]*)"[^]]*\]/',$content,$matches );

$column_width = 0;
$column_width_str = '';
if ( isset( $matches[1] ) && is_array( $matches[1] ) ) {
    foreach( $matches[1] as $index => $item ) {
        $w = preg_replace( '/[^.0-9]/', '', $item );
        if ( $column_width > (float)$w || $index == 0 ) {
            $column_width = (float)$w;
            $column_width_str = $item;
        }
    }
}

if ( $column_width > 0 ) {
    $replace_count = 1;
    $content = str_replace( '[porto_grid_item width="'. esc_attr( $column_width_str ) .'"', '[porto_grid_item width="'. esc_attr( $column_width_str ) .'" column_class="true"', $content, $replace_count );
}

$isoOptions = array();
if ( !( function_exists( 'vc_is_inline' ) && vc_is_inline() ) ) {
    $isoOptions['itemSelector'] = ".porto-grid-item";
} else {
    $isoOptions['itemSelector'] = ".vc_porto_grid_item";
}

$isoOptions['layoutMode'] = "masonry";
$isoOptions['masonry'] = array( 'columnWidth' => '.iso-column-class' );
$isoOptions['animationEngine'] = "best-available";
$isoOptions['resizable'] = false;

$output .= '<div id="grid-' . $rand . '" class="' . $el_class . ' wpb_content_element clearfix" data-plugin-masonry data-plugin-options=\''. json_encode( $isoOptions ) .'\'">';
$output .= do_shortcode($content);
$output .= '</div>';

$max_width = esc_js($max_width);
$rand = esc_js($rand);

$gutter_size_number = preg_replace( '/[^.0-9]/', '', $gutter_size );
$gutter_size = str_replace( $gutter_size_number, (float)($gutter_size_number / 2), $gutter_size );
$gutter_size = esc_js($gutter_size);

$output .= '<style type="text/css">
                #grid-' . $rand. ' .porto-grid-item {
                    padding: '. $gutter_size .';
                }

                #grid-' . $rand. ' {
                    margin: -'. $gutter_size .' -' . $gutter_size . ' ' . $gutter_size . ';
                }

                @media (max-width:' . $max_width . ') {
                    #grid-' . $rand. ' {
                        height: auto !important;
                    }
                    #grid-' . $rand. ' .porto-grid-item:first-child {
                        margin-top: 0;
                    }
                    #grid-' . $rand. ' .porto-grid-item {

                        width: 100% !important;
                        position: static !important;
                        float: none;

                    }
                }';
if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
    $output .= '.porto-grid-container .porto-grid-item { float: none; } .porto-grid-container .vc_porto_grid_item { float: left; }';
    $output .= '.porto-grid-container .porto-grid-item .wpb_single_image { margin-bottom: 0; }';
}
$output .= '</style>';

$output .= '</div>';

echo $output;

if ( function_exists( 'vc_is_inline' ) && vc_is_inline() ) {
?>
    <script type="text/javascript">
        var column_class<?php echo $rand ?> = 100;
        $('.porto-grid-container .vc_porto_grid_item').each(function() {
            if ($(this).children('.porto-grid-item').length) {
                var widthAttr = $(this).children('.porto-grid-item').attr('style'),
                    width = widthAttr.replace('width:', '').replace('%', '').replace(' ', '');
                try {
                    width = parseInt(width, 10);
                } catch(e) {
                    width = 0;
                }
                if (column_class<?php echo $rand ?> > width) {
                    column_class<?php echo $rand ?> = width;
                }
                $(this).children('.porto-grid-item').css('width', '');
                $(this).attr('style', widthAttr);
            }
        });
        $('.porto-grid-container .vc_porto_grid_item').each(function() {
            if ($(this).children('.porto-grid-item').length) {
                var widthAttr = $(this).attr('style'),
                    width = widthAttr.replace('width:', '').replace('%', '').replace(' ', '');
                try {
                    width = parseInt(width, 10);
                } catch(e) {
                    width = 0;
                }
                if (width === column_class<?php echo $rand ?>) {
                    $(this).addClass('iso-column-class');
                }
            }
        });
    </script>
<?php
}