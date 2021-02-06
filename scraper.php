<?php 
    require_once 'simple_html_dom.php';

    //get html content
    //I chose 'family friendly' and 'support for Norwegian' filters as these properties are not provided in DOM.
    //The rest is being filtered here
    $dom = file_get_html('https://store.steampowered.com/search/?tags=5350&category1=998&supportedlang=norwegian', false);

    $answer = array();

    if(!empty($dom)) {
        $aClass = $title = "";
        $i = 0;
        foreach($dom->find(".search_result_row") as $aClass) {

            $title = $aClass->find(".title", 0)->plaintext;
            $photoURL = $aClass->find("img", 0)->src;
            $price = trim($aClass->find(".col.search_price", 0)->plaintext);
            $releaseDate = $aClass->find(".col.search_released", 0)->plaintext;

            $review = $aClass->find(".col.search_reviewscore", 0)->first_child()->class;

            $pos = strpos($title, 'a');

            //parse Double
            $priceDouble = doubleval(str_replace(',', '.', $price));

            $reviewType = strpos($review, 'positive');

            //convert to Date object
            $toDate = date_format(date_create_from_format('d M, Y', $releaseDate), 'd-M-Y');

            //check if element's title contains 'a', if price is max 90 kr and if reviews are positive
            if (!$pos && $priceDouble <= 99.0 && $reviewType) {
                $answer[$i]['title'] = $title;
                $answer[$i]['photoURL'] = $photoURL;
                $answer[$i]['price'] = $priceDouble;
                $answer[$i]['release_date'] = $toDate;

                $i++;

            }

        }
    }

    //create a .json file and save the results
    $file = fopen('results.json', 'w');
    fwrite($file, json_encode($answer));
    fclose($file);
?>