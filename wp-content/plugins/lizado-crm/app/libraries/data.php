<?php
include 'Curl.php';
include_once 'simple_html_dom.php';

$objCurl = new cUrl();
$respon = $objCurl->custom_curl('http://153.126.211.218:8112/ndaja/view_data2.cgi',false,'cookies-153-126-211-218.txt');

$html   = str_get_html($respon);


$th_data = [];
foreach( $html->find('.pubTable thead th') as $th ){
    $children = $th->children; // get an array of children
    foreach ($children AS $child) {
        $child->outertext = ''; // This removes the element, but MAY NOT remove it from the original $myDiv
    }
    $th_data[] = trim($th->innertext);
}

$tr_data = [];

// echo '<pre>';
// print_r($th_data);

foreach( $html->find('.pubTable tbody tr') as $key => $tr ){
    $tr_data[$key] = [];
    foreach( $tr->find('td') as $key_td => $td ){
        
        if( $key_td == 0 ){
            continue;
        }
        switch ($key_td) {
            case 1:
                $key_label = 'data_title';//商品名：tên sản phẩm
                $value = $td->find('.cau_msg',0)->innertext;
                break;
            case 2:
                $key_label = 'product_name';//商品名：tên sản phẩm
                $value = $td->find('input',0)->attr['value'];
                break;
            case 9:
                $key_label = 'product_number';//型番: số hiệu
                $value = $td->find('input',0)->attr['value'];
                break;
            case 10:
                $key_label = 'image_number';//画像型番:số hiệu ảnh
                $value = $td->find('input',0)->attr['value'];
                break;
            case 11:
                $key_label = 'image_url';//画像型番:số hiệu ảnh
                $value = $td->innertext;
                break;
            case 13:
                $key_label = 'store_name';//店名：tên cửa hàng
                $value = $td->innertext;
                $value = strip_tags($value);
                break;
            case 14:
                $key_label = 'classification';//区分：phân loại( sell/buy)
                $value = $td->innertext;
                $value = strip_tags($value);
                break;
            case 15:
                $key_label = 'category';//カテゴリ:categori
                $value = $td->innertext;
                $value = strip_tags($value);
                break;
            case 17:
                $key_label = 'price';//値段:giá
                $value = $td->find('input',0)->attr['value'];
                break;
            case 18:
                $key_label = 'stock';//値段:giá
                $value = $td->find('input',0)->attr['value'];
                break;
            case 19:
                $key_label = 'quality';//品質：chất lượng (cột M trong csv)
                $value = $td->find('input',0)->attr['value'];
                break;
            case 20:
                $key_label = 'other_factory';
                $value = $td->find('input',0)->attr['value'];
                break;
            default:
                $key_label = $key_td;
                if( $td->find('input',0) ){
                    $value = $td->find('input',0)->attr['value'];
                }else{
                    $value = $td->innertext;
                }
                break;
        }
        $value = strip_tags($value);
        $tr_data[$key][$key_label] = $value;
    }
}

echo '<pre>';
print_r($tr_data);
die();