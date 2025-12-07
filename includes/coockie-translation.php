<?php
// Hook into WordPress content
add_filter('the_content', 'translate_page_content_for_visitor');

function translate_page_content_for_visitor($content) {
    $options = get_option('ccp_settings');
      $terms_page_id = isset($options['terms_page']) ? $options['terms_page'] : '';
        
    $apiKey=$options['transapi_key'] ?? '';
    if (empty($apiKey)) {
        return $content; // No API key set, return original content
    }
    if (!empty($terms_page_id)&& is_page($terms_page_id)) { // Check if the visitor is viewing page ID 6
        $language_code = get_user_language_from_country(); // your country->language function
        $translated_content = translate_text_google($content, $language_code); // translate content
        return $translated_content;
    }

    return $content; // return normal content for other pages
}

/**
 * Example: function to get language code from visitor country
 */
function get_user_language_from_country() {
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = @file_get_contents("http://ip-api.com/json/{$ip}");
    $countryCode = '';
    if ($response) {
        $data = json_decode($response, true);
        if ($data['status'] === 'success') {
            $countryCode = $data['countryCode'];
        }
    }

 

   $country_to_lang = [
    'AD' => 'ca', // Andorra -> Catalan
    'AE' => 'ar', // United Arab Emirates -> Arabic
    'AF' => 'fa', // Afghanistan -> Persian
    'AG' => 'en', // Antigua and Barbuda -> English
    'AI' => 'en', // Anguilla -> English
    'AL' => 'sq', // Albania -> Albanian
    'AM' => 'hy', // Armenia -> Armenian
    'AO' => 'pt', // Angola -> Portuguese
    'AQ' => 'en', // Antarctica -> English
    'AR' => 'es', // Argentina -> Spanish
    'AS' => 'en', // American Samoa -> English
    'AT' => 'de', // Austria -> German
    'AU' => 'en', // Australia -> English
    'AW' => 'nl', // Aruba -> Dutch
    'AX' => 'sv', // Åland Islands -> Swedish
    'AZ' => 'az', // Azerbaijan -> Azerbaijani
    'BA' => 'bs', // Bosnia and Herzegovina -> Bosnian
    'BB' => 'en', // Barbados -> English
    'BD' => 'bn', // Bangladesh -> Bengali
    'BE' => 'nl', // Belgium -> Dutch (Flemish)
    'BF' => 'fr', // Burkina Faso -> French
    'BG' => 'bg', // Bulgaria -> Bulgarian
    'BH' => 'ar', // Bahrain -> Arabic
    'BI' => 'fr', // Burundi -> French
    'BJ' => 'fr', // Benin -> French
    'BL' => 'fr', // Saint Barthélemy -> French
    'BM' => 'en', // Bermuda -> English
    'BN' => 'ms', // Brunei Darussalam -> Malay
    'BO' => 'es', // Bolivia -> Spanish
    'BQ' => 'nl', // Caribbean Netherlands -> Dutch
    'BR' => 'pt', // Brazil -> Portuguese
    'BS' => 'en', // Bahamas -> English
    'BT' => 'dz', // Bhutan -> Dzongkha
    'BW' => 'en', // Botswana -> English
    'BY' => 'be', // Belarus -> Belarusian
    'BZ' => 'en', // Belize -> English
    'CA' => 'en', // Canada -> English (note: also 'fr' for Quebec)
    'CC' => 'en', // Cocos (Keeling) Islands -> English
    'CD' => 'fr', // Congo, Democratic Republic -> French
    'CF' => 'fr', // Central African Republic -> French
    'CG' => 'fr', // Congo -> French
    'CH' => 'de', // Switzerland -> German (also 'fr', 'it', 'rm')
    'CI' => 'fr', // Côte d'Ivoire -> French
    'CK' => 'en', // Cook Islands -> English
    'CL' => 'es', // Chile -> Spanish
    'CM' => 'fr', // Cameroon -> French
    'CN' => 'zh', // China -> Chinese
    'CO' => 'es', // Colombia -> Spanish
    'CR' => 'es', // Costa Rica -> Spanish
    'CU' => 'es', // Cuba -> Spanish
    'CV' => 'pt', // Cabo Verde -> Portuguese
    'CW' => 'nl', // Curaçao -> Dutch
    'CX' => 'en', // Christmas Island -> English
    'CY' => 'el', // Cyprus -> Greek
    'CZ' => 'cs', // Czech Republic -> Czech
    'DE' => 'de', // Germany -> German
    'DJ' => 'fr', // Djibouti -> French
    'DK' => 'da', // Denmark -> Danish
    'DM' => 'en', // Dominica -> English
    'DO' => 'es', // Dominican Republic -> Spanish
    'DZ' => 'ar', // Algeria -> Arabic
    'EC' => 'es', // Ecuador -> Spanish
    'EE' => 'et', // Estonia -> Estonian
    'EG' => 'ar', // Egypt -> Arabic
    'EH' => 'ar', // Western Sahara -> Arabic
    'ER' => 'ti', // Eritrea -> Tigrinya
    'ES' => 'es', // Spain -> Spanish
    'ET' => 'am', // Ethiopia -> Amharic
    'FI' => 'fi', // Finland -> Finnish
    'FJ' => 'en', // Fiji -> English
    'FK' => 'en', // Falkland Islands -> English
    'FM' => 'en', // Micronesia -> English
    'FO' => 'fo', // Faroe Islands -> Faroese
    'FR' => 'fr', // France -> French
    'GA' => 'fr', // Gabon -> French
    'GB' => 'en', // United Kingdom -> English
    'GD' => 'en', // Grenada -> English
    'GE' => 'ka', // Georgia -> Georgian
    'GF' => 'fr', // French Guiana -> French
    'GG' => 'en', // Guernsey -> English
    'GH' => 'en', // Ghana -> English
    'GI' => 'en', // Gibraltar -> English
    'GL' => 'kl', // Greenland -> Greenlandic
    'GM' => 'en', // Gambia -> English
    'GN' => 'fr', // Guinea -> French
    'GP' => 'fr', // Guadeloupe -> French
    'GQ' => 'es', // Equatorial Guinea -> Spanish
    'GR' => 'el', // Greece -> Greek
    'GS' => 'en', // South Georgia -> English
    'GT' => 'es', // Guatemala -> Spanish
    'GU' => 'en', // Guam -> English
    'GW' => 'pt', // Guinea-Bissau -> Portuguese
    'GY' => 'en', // Guyana -> English
    'HK' => 'zh', // Hong Kong -> Chinese
    'HN' => 'es', // Honduras -> Spanish
    'HR' => 'hr', // Croatia -> Croatian
    'HT' => 'fr', // Haiti -> French (also 'ht' for Haitian Creole)
    'HU' => 'hu', // Hungary -> Hungarian
    'ID' => 'id', // Indonesia -> Indonesian
    'IE' => 'en', // Ireland -> English
    'IL' => 'he', // Israel -> Hebrew
    'IM' => 'en', // Isle of Man -> English
    'IN' => 'hi', // India -> Hindi (official, though many languages)
    'IO' => 'en', // British Indian Ocean Territory -> English
    'IQ' => 'ar', // Iraq -> Arabic
    'IR' => 'fa', // Iran -> Persian
    'IS' => 'is', // Iceland -> Icelandic
    'IT' => 'it', // Italy -> Italian
    'JE' => 'en', // Jersey -> English
    'JM' => 'en', // Jamaica -> English
    'JO' => 'ar', // Jordan -> Arabic
    'JP' => 'ja', // Japan -> Japanese
    'KE' => 'sw', // Kenya -> Swahili
    'KG' => 'ky', // Kyrgyzstan -> Kyrgyz
    'KH' => 'km', // Cambodia -> Khmer
    'KI' => 'en', // Kiribati -> English
    'KM' => 'ar', // Comoros -> Arabic
    'KN' => 'en', // Saint Kitts and Nevis -> English
    'KP' => 'ko', // North Korea -> Korean
    'KR' => 'ko', // South Korea -> Korean
    'KW' => 'ar', // Kuwait -> Arabic
    'KY' => 'en', // Cayman Islands -> English
    'KZ' => 'kk', // Kazakhstan -> Kazakh
    'LA' => 'lo', // Laos -> Lao
    'LB' => 'ar', // Lebanon -> Arabic
    'LC' => 'en', // Saint Lucia -> English
    'LI' => 'de', // Liechtenstein -> German
    'LK' => 'si', // Sri Lanka -> Sinhala
    'LR' => 'en', // Liberia -> English
    'LS' => 'en', // Lesotho -> English
    'LT' => 'lt', // Lithuania -> Lithuanian
    'LU' => 'lb', // Luxembourg -> Luxembourgish (also 'fr', 'de')
    'LV' => 'lv', // Latvia -> Latvian
    'LY' => 'ar', // Libya -> Arabic
    'MA' => 'ar', // Morocco -> Arabic
    'MC' => 'fr', // Monaco -> French
    'MD' => 'ro', // Moldova -> Romanian
    'ME' => 'sr', // Montenegro -> Serbian
    'MF' => 'fr', // Saint Martin -> French
    'MG' => 'mg', // Madagascar -> Malagasy
    'MH' => 'en', // Marshall Islands -> English
    'MK' => 'mk', // North Macedonia -> Macedonian
    'ML' => 'fr', // Mali -> French
    'MM' => 'my', // Myanmar -> Burmese
    'MN' => 'mn', // Mongolia -> Mongolian
    'MO' => 'zh', // Macao -> Chinese
    'MP' => 'en', // Northern Mariana Islands -> English
    'MQ' => 'fr', // Martinique -> French
    'MR' => 'ar', // Mauritania -> Arabic
    'MS' => 'en', // Montserrat -> English
    'MT' => 'mt', // Malta -> Maltese
    'MU' => 'en', // Mauritius -> English
    'MV' => 'dv', // Maldives -> Dhivehi
    'MW' => 'en', // Malawi -> English
    'MX' => 'es', // Mexico -> Spanish
    'MY' => 'ms', // Malaysia -> Malay
    'MZ' => 'pt', // Mozambique -> Portuguese
    'NA' => 'en', // Namibia -> English
    'NC' => 'fr', // New Caledonia -> French
    'NE' => 'fr', // Niger -> French
    'NF' => 'en', // Norfolk Island -> English
    'NG' => 'en', // Nigeria -> English
    'NI' => 'es', // Nicaragua -> Spanish
    'NL' => 'nl', // Netherlands -> Dutch
    'NO' => 'no', // Norway -> Norwegian
    'NP' => 'ne', // Nepal -> Nepali
    'NR' => 'en', // Nauru -> English
    'NU' => 'en', // Niue -> English
    'NZ' => 'en', // New Zealand -> English
    'OM' => 'ar', // Oman -> Arabic
    'PA' => 'es', // Panama -> Spanish
    'PE' => 'es', // Peru -> Spanish
    'PF' => 'fr', // French Polynesia -> French
    'PG' => 'en', // Papua New Guinea -> English
    'PH' => 'en', // Philippines -> English (also 'fil' for Filipino)
    'PK' => 'ur', // Pakistan -> Urdu
    'PL' => 'pl', // Poland -> Polish
    'PM' => 'fr', // Saint Pierre and Miquelon -> French
    'PN' => 'en', // Pitcairn -> English
    'PR' => 'es', // Puerto Rico -> Spanish
    'PS' => 'ar', // Palestine -> Arabic
    'PT' => 'pt', // Portugal -> Portuguese
    'PW' => 'en', // Palau -> English
    'PY' => 'es', // Paraguay -> Spanish
    'QA' => 'ar', // Qatar -> Arabic
    'RE' => 'fr', // Réunion -> French
    'RO' => 'ro', // Romania -> Romanian
    'RS' => 'sr', // Serbia -> Serbian
    'RU' => 'ru', // Russia -> Russian
    'RW' => 'rw', // Rwanda -> Kinyarwanda
    'SA' => 'ar', // Saudi Arabia -> Arabic
    'SB' => 'en', // Solomon Islands -> English
    'SC' => 'fr', // Seychelles -> French
    'SD' => 'ar', // Sudan -> Arabic
    'SE' => 'sv', // Sweden -> Swedish
    'SG' => 'en', // Singapore -> English
    'SH' => 'en', // Saint Helena -> English
    'SI' => 'sl', // Slovenia -> Slovenian
    'SJ' => 'no', // Svalbard and Jan Mayen -> Norwegian
    'SK' => 'sk', // Slovakia -> Slovak
    'SL' => 'en', // Sierra Leone -> English
    'SM' => 'it', // San Marino -> Italian
    'SN' => 'fr', // Senegal -> French
    'SO' => 'so', // Somalia -> Somali
    'SR' => 'nl', // Suriname -> Dutch
    'SS' => 'en', // South Sudan -> English
    'ST' => 'pt', // Sao Tome and Principe -> Portuguese
    'SV' => 'es', // El Salvador -> Spanish
    'SX' => 'nl', // Sint Maarten -> Dutch
    'SY' => 'ar', // Syrian Arab Republic -> Arabic
    'SZ' => 'en', // Eswatini -> English
    'TC' => 'en', // Turks and Caicos Islands -> English
    'TD' => 'fr', // Chad -> French
    'TF' => 'fr', // French Southern Territories -> French
    'TG' => 'fr', // Togo -> French
    'TH' => 'th', // Thailand -> Thai
    'TJ' => 'tg', // Tajikistan -> Tajik
    'TK' => 'en', // Tokelau -> English
    'TL' => 'pt', // Timor-Leste -> Portuguese
    'TM' => 'tk', // Turkmenistan -> Turkmen
    'TN' => 'ar', // Tunisia -> Arabic
    'TO' => 'en', // Tonga -> English
    'TR' => 'tr', // Turkey -> Turkish
    'TT' => 'en', // Trinidad and Tobago -> English
    'TV' => 'en', // Tuvalu -> English
    'TW' => 'zh', // Taiwan -> Chinese
    'TZ' => 'sw', // Tanzania -> Swahili
    'UA' => 'uk', // Ukraine -> Ukrainian
    'UG' => 'en', // Uganda -> English
    'UM' => 'en', // US Minor Outlying Islands -> English
    'US' => 'en', // United States -> English
    'UY' => 'es', // Uruguay -> Spanish
    'UZ' => 'uz', // Uzbekistan -> Uzbek
    'VA' => 'la', // Vatican City -> Latin (also 'it')
    'VC' => 'en', // Saint Vincent and the Grenadines -> English
    'VE' => 'es', // Venezuela -> Spanish
    'VG' => 'en', // Virgin Islands (British) -> English
    'VI' => 'en', // Virgin Islands (US) -> English
    'VN' => 'vi', // Vietnam -> Vietnamese
    'VU' => 'bi', // Vanuatu -> Bislama
    'WF' => 'fr', // Wallis and Futuna -> French
    'WS' => 'sm', // Samoa -> Samoan
    'YE' => 'ar', // Yemen -> Arabic
    'YT' => 'fr', // Mayotte -> French
    'ZA' => 'af', // South Africa -> Afrikaans (also 'en', 'zu', 'xh', etc.)
    'ZM' => 'en', // Zambia -> English
    'ZW' => 'en', // Zimbabwe -> English
];

    return $country_to_lang[$countryCode] ?? 'en';
}

/**
 * Function to translate text using Google Translate API
 */
function translate_text_google($text, $target_lang) {
     $options = get_option('ccp_settings');
    $apiKey=$options['transapi_key'] ?? '';
    //$apiKey = 'AIzaSyD5So-nRCwr4riKVVyB3OtetaJjvk65f7I'; // replace with your API key
    $url = 'https://translation.googleapis.com/language/translate/v2?key=' . $apiKey;

    $data = [
        'q' => $text,
        'target' => $target_lang,
        'format' => 'text'
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n",
            'method'  => 'POST',
            'content' => json_encode($data),
        ],
    ];

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    if ($result === FALSE) { return $text; } // fallback

    $response = json_decode($result, true);
    return $response['data']['translations'][0]['translatedText'] ?? $text;
}
