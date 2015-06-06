<?php
/*
 * eRanker add-ons methods
 */
if( file_exists(dirname(__FILE__) . '/erreport.addons.php') ):
    include_once(dirname(__FILE__) . '/erreport.addons.php');
endif;

/*
 * eRanker Report
 */
function render_erreport( $json, $report, $logged_in = false, $is_pdf = false ) {
    $json       = is_object( $json ) ? $json : json_decode( $json ); // make sure it's an object
    $report     = is_object( $report ) ? $report : json_decode( $report ); // make sure it's an object also
    $score      = 0; // need to skip score somehow
    $categories = array(); // store categories
    $groups     = array(); // store groups
    $ratings    = array( 'starsbg' => 'star-o', 'stars' => 'star' ); // store rating stars
    $retval     = ''; // final result
    $pdfval     = ''; // pdf description result
    $is_pdf     = $logged_in ? $is_pdf : false; // disable pdf is user is not logged in
    foreach( $json as $key => $properties ):
        if( $score == 0 ): // score output here
            $sitescreen = !is_null( $properties->thumbnail ) ? '<img id="sitescreen" src="' . $properties->thumbnail . '">' : '';
            $report_url = $report->urls[0]->url;
            $totalscore = $properties->factors->green + $properties->factors->red + $properties->factors->orange + $properties->factors->missing;         
            $retval .= '<div class="row score-table">';
            $retval .= '<div class="col can-float factors-percent">' // factors-percent
                    . '<aside>'
                    . '<div class="overall-score">'
                    . '<p>Overall</p>'
                    . '<h1>' . round( $properties->percentage ) . '</h1>'
                    . '<p>out of 100</p>'
                    . '<div class="circle" id="circles" data-percent="' . $properties->percentage . '" ></div>' // percentage chart
                    . '</div>' // overall
                    . '<div class="additional-ratings">'
                    . '<span>Generated on ' . get_date_time( $report->date_generated ) . '<br /><a id="update-now" onclick="hasSupport()">Update now</a></span>'
                    . '<ul id="rating-stars">';
            foreach( $ratings as $position => $stars ):
                $retval .= '<li class="rating-' . $position . '" style="' . ( $position == 'stars' ? 'width:' . round( $properties->percentage ) / 10 * 10.6 . 'px' : '' ) . '"><div>';
                for( $i = 0; $i < 5; $i++ ): // 5 stars
                    $retval .= '<i class="fa fa-' . $stars . '"></i>';
                endfor;
                $retval .= '</div></li>';
            endforeach;
            $retval .= '</ul>'
                    . '</div>' // additional ratings
                    . '</aside>' 
                    . '<div><a download id="download-pdf" onclick="hasSupport()">Download PDF Report</a></div>'
                    . '</div>' // end factors-percent
                    . '<div class="col can-float factors-score">' // factors score
                    . '<p>Report of</p>'
                    . '<h2>' . $report_url . '</h2>'
                    . '<ul>'
                    . '<style type="text/css"> '
                    . 'li.green::after { width: ' . ( $properties->factors->green * 100 / $totalscore ) .  '%;  -webkit-animation: greenscore 1s; animation: greenscore 1s; }'
                    . 'li.orange::after { width: ' . ( $properties->factors->orange * 100 / $totalscore ) .  '%;  -webkit-animation: orangrescore 1s; animation: greenscore 1s; }'
                    . 'li.red::after { width: ' . ( ( $properties->factors->red + $properties->factors->missing ) * 100 / $totalscore ) .  '%;  -webkit-animation: redscore 1s; animation: greenscore 1s; }'
                    . '@-webkit-keyframes greenscore { from { width: 0%; } to { width: ' . ( $properties->factors->green * 100 / $totalscore ) .  '%; } }'
                    . '@-webkit-keyframes orangescore { from { width: 0%; } to { width: ' . ( $properties->factors->orange * 100 / $totalscore ) .  '%; } }'
                    . '@-webkit-keyframes redscore { from { width: 0%; } to { width: ' . ( ( $properties->factors->red + $properties->factors->missing ) * 100 / $totalscore ) .  '%; } }'
                    . '@keyframes greenscore { from { width: 0%; } to { width: ' . ( $properties->factors->green * 100 / $totalscore ) .  '%; } }'
                    . '@keyframes orangescore { from { width: 0%; } to { width: ' . ( $properties->factors->orange * 100 / $totalscore ) .  '%; } }'
                    . '@keyframes redscore { from { width: 0%; } to { width: ' . ( ( $properties->factors->red + $properties->factors->missing ) * 100 / $totalscore ) .  '%; } }'
                    . '</style>'
                    . '<li class="col green"><i class="fa fa-check"></i><b class="factor-score">Successfully passed<span>' . $properties->factors->green . '</span></b></li>'
                    . '<li class="col orange"><i class="fa fa-minus"></i><b class="factor-score">Room for improvement<span>' . $properties->factors->orange . '</span></b></li>'
                    . '<li class="col red"><i class="fa fa-times"></i><b class="factor-score">Errors<span>' . ( $properties->factors->red + $properties->factors->missing ) . '</span></b></li>'
                    . '<div class="clearfix"></div>'
                    . '</ul>'
                    . '</div>' // end factors-score
                    . '<div class="col can-float factors-site">' // site screen
                    . '<div class="printscreen">'
                    . $sitescreen // actual site screen
                    . '</div>'
                    . '</div>'; // end factors-site
            $retval .= '<div class="clearfix"></div></div>'; // end score-table
        elseif( $score > 0 ): // factors output here 
            $status = ''; 
            $retval .= '<div class="row" data-category="' . $properties->model->category->display . '">'; // group all data within the row
            if( !in_array( $properties->model->category->display, $categories ) ): 
                array_push( $categories, $properties->model->category->display );
                $retval .= empty( $properties->model->category->display ) 
                        ? '' 
                        : '<div class=" can-float category-name"><h3>' . $properties->model->category->display . '</h3></div>'
                        . '<div class="col can-float category-progress" id="progress-' . str_replace( ' ', '', strtolower( $properties->model->category->display ) ) . '"></div>'
                        . '<div class="clearfix"></div>';
                $pdfval .= $is_pdf 
                        ? '<h5>' . $properties->model->category->display . '</h5>' 
                        : '';
            endif;
            if( !in_array( $properties->model->category->group->name, $groups ) ): 
                array_push( $groups, $properties->model->category->group->name );
                $retval .= empty( $properties->model->category->group->name ) 
                        ? '' 
                        : '<div class="col group-name">' . $properties->model->category->group->name . '</div>'
                        . '<div class="clearfix"></div>';
                $pdfval .= $is_pdf 
                        ? '<h6>' . $properties->model->category->group->name . '</h6>'  
                        : '';
            endif;
            switch( $properties->model->status ):
                case 'MISSING':
                case 'RED': { 
                    $status = 'times';
                    break; 
                }
                case 'ORANGE': { 
                    $status = 'minus';
                    break; 
                }
                case 'GREEN': { 
                    $status = 'check';
                    break; 
                }
                default: {
                    $status = 'question-circle';
                }
            endswitch;
            $status = $logged_in ? $status : 'question-circle';
            $statuscolor = $logged_in ? strtolower( $properties->model->status ) : '';
            $rendering = array( 
                'name'  => $properties->model->name, 
                'type'  => $properties->model->type, 
                'model' => $properties->model->model, 
                'data'  => $properties->data, 
                'status'=> $properties->model->status,
                'free'  => $properties->model->free,
                'pro'   => $properties->model->pro_only,
                );
            //$colsize = 8; // set default to 8
            if( $properties->model->display == 'Text/HTML ratio' ) $rendering['data'] = intval( round( $rendering['data'] ) );
            $retval .= '<section id="factor-' . $properties->model->name . '" data-status="' . $properties->model->status . '" '
                    . 'onclick="' . ( $logged_in ? 'niceToggle($(this).attr(\'id\'))' : '' ) . '">'
                    . '<i class="fa fa-plus expandtoggle"></i>'
                    . '<div class="row">';
            $retval .= '<div class="col can-float factor-name">'
                    . ( $status ? '<i class="fa fa-' . $status . ' ' . $statuscolor . '"></i>' : '' )
                    . $properties->model->display 
                    . '</div>';
            $pdfval .= $is_pdf 
                    ? '<li><strong>' . $properties->model->display . '</strong>:&nbsp;' 
                    : '';
            $retval .= '<div class="col can-float factor-data">';
            $retval .= render2html( $rendering['name'], $rendering['type'], $rendering['model'], $rendering['data'], $rendering['status'], $rendering['free'], $rendering['pro'], $logged_in );
            $retval .= '</div>';
            if( $is_pdf ):                
                $pdfval .= html_entity_decode( $properties->model->description ) . '<br /><em>Solution: ' . $properties->model->solution . '</em><br /><em>Article: ' . $properties->model->article . '</em>'
                        . '</li>'; // close factors item 
            else:
                $retval .= $logged_in ? '<div class="clearfix col factor-info"><p>' . html_entity_decode( $properties->model->description ) . '</p></div>' : '';
            endif;
            $retval .= '<div class="clearfix"></div></div>' // close inside section row
                    . '</section>'
                    . '</div>'; // row ends here
        endif;
        $score++;
    endforeach;
    if( $is_pdf ):
        $retval .= '<!-- pdf starts here --><div class="row pdf-desctiptions"><ol><h4>Factors description</h4>'
                . $pdfval
                . '</ol></div>';
    endif;
    return '<!-- generated_report --><div data-display="" id="erreport-seo" class="container">' . $retval . '</div>';
}

function render2html( $name, $type, $model, $data, $status, $free, $pro, $logged ) {
    if( $logged || ( !$logged && $free ) ):
        $model = html_entity_decode( $model );
        // get factor type from data-type, in case it's missing in DB
        $factor_type = "NULL";
        if( !is_null( $data ) ):
            if( is_bool( $data ) ):
                $factor_type = "BOOLEAN";
            endif;
            if( is_numeric( $data ) ):
                 $factor_type = "NUMBER";
            endif;
            if( is_string( $data ) ):
                $factor_type = "STRING";
            endif;
            if( is_array( $data ) ): 
                $factor_type = "ARRAY";
            endif;
            if( is_object( $data ) ):
                $factor_type = "OBJECT";
            endif;
        endif;
        // if present in DB use DB factor-type
        if( !is_null( $type ) && !is_null( $data ) ):
            $factor_type = $type;
        endif;
        switch( $factor_type ): 
            case 'NULL': {
                $html = $model;
                break;
            }
            case 'BOOLEAN': {
                $html = !is_null( $model ) 
                        ? $model 
                        : $data;
                break;
            }
            case 'NUMBER': {            
                    $html = !is_null( $model ) 
                        ? sprintf( $model, $data ) 
                        : $data;            
                break;
            }
            case 'STRING': {
                $html = !is_null( $model ) && !empty( $model ) 
                        ? sprintf( $model, $data )
                        : $data;
                break;
            }
            case 'ARRAY': {
                $html = !is_null( $model ) || !$model
                        ? sprintf( $model, implode( ', ', $data ) ) 
                        : '';
                break;
            }
            case 'OBJECT': {
                $html = '';
                foreach( $data as $prop => $value ):
                    $html .= $prop . '(' . $value .')';
                endforeach;
                break;
            }
            case 'URL': {
                $html = !is_null( $model ) && !empty( $model ) 
                        ? sprintf( $model, $data, strlen( $data ) )
                        : $data;
                break;
            }
            case 'SPEED_ANALYSIS': {
                $html = render_speed_analysis( $model, $data );
                break;
            }
            case 'BACKLINKS': {
                $html = render_backlinks( $data );
                break;
            }
            case 'GMAPS': {
                // TO-TEST
                $html = render_gmaps( $data );
                break;
            }
            case 'GOOGLE_PREVIEW': {
                // TODO - TEST and FIX
                $html = !is_null( $model ) || !$model
                        ? sprintf( $model, implode( ', ', $data ) ) 
                        : '';
                break;
            }
            case 'IMAGE': {
                $html = '';
                $html_temp = '';
                if( $name == 'emails' ):
                    foreach( $data as $emailinfo ):
                        $html_temp .= '<img src="' . get_imagefromtext( html_entity_decode($emailinfo) ) . '" /><br />';
                    endforeach;
                endif;
                if( $name == 'phone' ):
                    $phone_temp = '';
                    foreach( $data as $phoneinfo ):
                        if($phoneinfo->region == null) {
                            $phone_temp = "Phone International: " . $phoneinfo->phone_international . ";" ."Phone: ". $phoneinfo->phone . ';'."Type: " . $phoneinfo->type;
                        }
                        elseif($phoneinfo->phone_international == null){
                            $phone_temp ="Region: ".$phoneinfo->region .";" ."Phone: ". $phoneinfo->phone . ';'."Type: " . $phoneinfo->type;
                        }
                        elseif($phoneinfo->phone == null){
                            $phone_temp = "Region: ".$phoneinfo->region . ";"."Phone International: " . $phoneinfo->phone_international . ";" ."Type: " . $phoneinfo->type;
                        }
                        elseif($phoneinfo->type == null){
                            $phone_temp = "Region: ".$phoneinfo->region . ";"."Phone International: " . $phoneinfo->phone_international . ";" ."Phone: ". $phoneinfo->phone;
                        }
                        else {
                            $phone_temp = "Region: ".$phoneinfo->region . ";"."Phone International: " . $phoneinfo->phone_international . ";" ."Phone: ". $phoneinfo->phone . ';'."Type: " . $phoneinfo->type ;
                        }
                        $phone_arr = explode(";" , $phone_temp);
                        foreach($phone_arr as $value){
                            $html_temp .= '<img src="' . get_imagefromtext( html_entity_decode ( $value ) ) . '" /><br />';
                        }
                    endforeach;
                endif;
                $html = !empty( $data ) ? $html_temp : $model;
                break;
            }
            case 'BASE64': {
                $html = '';
                $html_temp = '';
                $img = get_base64image( $data );
                switch( $name):
                    case 'favicon': 
                        $width = 24; 
                        break;
                    case 'logo': 
                        $width = 50; 
                        break;
                endswitch;
                $html_temp = !is_null( $img ) ? sprintf( $model, '<img style="width:' . $width . 'px;" src="' . $img . '" />' ) : '';
                $html = !empty( $data ) ? $html_temp : $model;
                break;
            }
            default: {
            }
        endswitch;
    else:
        $html = '<div class="has-blur"></div>';
    endif;
    return $html ?: false;
}

function render_speed_analysis( $model, $data ) {
    $html = !is_null( $model ) && !empty( $model ) 
            ? sprintf( $model, $data->score )
            : $data;
    $html .= $data->grades ? '</div><!-- grades --><div class="clearfix col factor-special">' : ''; // trick div
    $factors_labels = array(
        'favicon'   => 'Make favicon small and cacheable', 
        'no404'     => 'Avoid HTTP 404 (Not Found) error', 
        'cdn'       => 'Use a Content Delivery Network (CDN)',
        'expires'   => 'Add Expires headers',
        'compress'  => 'Compress components with gzip',
        'numreq'    => 'Make fewer HTTP Requests',
        'emptysrc'  => 'Avoid empty src or href',
        'csstop'    => 'Put CSS at top',
        'jsbottom'  => 'Put JavaScript at bottom',
        'mindom'    => 'Reduce the number of DOM elements',
        'mincookie' => 'Reduce cookie size',
        'imgnoscale'=> 'Do not scale images in HTML',
        'etags'     => 'Configure entity tags (ETags)',
        'dns'       => 'Reduce DNS lookups',
        'cookiefree'=> 'Use cookie-free domains',
        'dupes'     => 'Remove duplicate JavasScript and CSS',
        'redirects' => 'Avoid URL redirects',
        'xhr'       => 'Make AJAX cacheable',
        'xhrmethod' => 'Use GET for AJAX requests',
        );
    foreach( $data->grades as $label => $grade ):
        $html .= '<div class="row">';
        $html .= '<div class="col can-float speed-label">' . $factors_labels[$label] . '</div>'
                . '<div class="col can-float speed-progress">'
                . '<div class="load-progress-grade" style="width:' . ( $grade != 0 ? $grade : '0.5' ) . '%">&nbsp;</div>'
                . '</div>'
                . '<div class="col can-float speed-grade">' . $grade . '%</div>';
        $html .= '<div class="clearfix"></div></div>'; // close row
    endforeach;
    $html .= '<div class="clearfix"></div>';
    return $html;
}

function get_base64image( $value ) {
    $favicon = str_replace("http://", "", str_replace("https://", "", $value));
    $valueLen = strlen($favicon);
    $result = null;
    $has_favicon = false;
    if ($valueLen > 0) {
        if (strpos($favicon, ".ico") !== false) {
            $has_favicon = true;
            $has_mime = "data:image/png;base64,";
        }
        if (strpos($favicon, ".jpg") !== false) {
            $has_favicon = true;
            $has_mime = "data:image/jpeg;base64,";
        }
        if (strpos($favicon, ".png") !== false) {
            $has_favicon = true;
            $has_mime = "data:image/png;base64,";
        }
        if (strpos($favicon, ".gif") !== false) {
            $has_favicon = true;
            $has_mime = "data:image/gif;base64,";
        }
        if (strpos($favicon, ".svg") !== false) {
            $has_favicon = true;
            $has_mime = "data:image/svg+xml;base64,";
        }
        if ($has_favicon && $has_mime) {
            $callback = @file_get_contents($value);
            if ($callback) {
                $result = $has_mime . base64_encode($callback);
            } else {
                $result = $has_mime;
            }
        }
    }
    return $result;
}

function get_imagefromtext( $string ) {
    ob_start();
    $width = 100;
    $height = 25;
    $font  = 4;
    $font_family = 'fonts/open-sans/OpenSans-Regular.ttf';
    $width  = imagefontwidth($font) * strlen($string);
    $height = imagefontheight($font);
    $image = imagecreatetruecolor ($width,$height);
    $white = imagecolorallocate ($image,255,255,255);
    $black = imagecolorallocate ($image,0,0,0);
    imagefill($image,0,0,$white);
    imagestring ($image,$font,0,0,$string,$black);
    //imagettftext($image, 20, 0, 10, 20, $black, $font_family, $string);
    imagepng ($image);
    imagedestroy($image);
    $sImage = base64_encode(ob_get_contents());
    ob_end_clean();
    return 'data:image/png;base64,'.$sImage;
}

function render_backlinks( $value ) {
    $out = '';
    $pairs = array( 
        /* pairs of min. two as key => label */
        array( 'text' => 'Text', 'image' => 'Images' ),
        array( 'refpages' => 'Referal Pages', 'pages' => 'Pages' ),
        array( 'nofollow' => 'NoFollow', 'dofollow' => 'DoFollow' ),
        array( 'sitewide' => 'Site Wide', 'not_sitewide' => 'Not Site Wide' ),
        array( 'links_internal' => 'Internal links', 'links_external' => 'External links' ),
        array( 'gov' => 'Gov', 'edu' => 'Edu', 'rss' => 'Rss' ),
        array( 'redirect' => 'Redirect', 'canonical' => 'Canonical' ),
        array( 'alternate' => 'Alternate', 'html_pages' => 'HTML Pages' ),
    );
    if( is_object( $value ) ) {
        foreach( $pairs as $pair ) {
            $chart = '<div class="hidden piechart" data-labels="false" data-donut="false" data-pos-values="true">';
            foreach( $pair as $key => $label ) {
                $chart .= '<div class="data-chart" id="' . $key . '" data-label="' . $label . '" data-value="' . $value->{$key} . '"></div>';
            }
            $chart .= '</div>';
            $out .= $chart;
        }
    }
    return '</div><div class="clearfix col factor-special">' // trick div
        . '<div class="row" id="backlinkscharts">' . $out . '</div>'
        . '<div id="givemepie" class="row"></div>';
}

function render_gmaps( $value ) {
    $ispdf = $_GET['pdf'] ? true : false;
    if( is_object( $value ) ) {
        if( !empty( $value->longitude ) && !empty( $value->latitude ) ) {
            $map = '<div id="basic-google-map" class="basic-google-map' . ( $ispdf ? ' map-on-pdf' : '' ) . '" ';
            if( !empty( $value->companyname ) ) $map .= 'data-title="' . $value->companyname . '" ';
            $map .= 'data-maplng="' . $value->longitude . '" data-maplat="' . $value->latitude . '"></div>';
        }    
    } 
    return $map ?: false;
}
 
/*
 * eRanker default methods
 */
if( !function_exists('get_date_time') ) {
    function get_date_time( $date_time ) {
        return date( 'd/m/y, H:i', strtotime( get_date_from_gmt( $date_time, get_option( 'date_format' ) . ' ' . get_option('time_format') ) ) );
    }
}

?>