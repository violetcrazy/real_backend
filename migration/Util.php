<?php
class Util
{
    public static function usernameValidation($username)
    {
        $username = trim($username);

        if (preg_match('/^[a-z0-9_]+$/', $username)) {
            return true;
        }

        return false;
    }

    public static function dateValidation($date)
    {
        $date = trim($date);

        if (preg_match('/^(?:(?:31(\/|-|\.)(?:0?[13578]|1[02]))\1|(?:(?:29|30)(\/|-|\.)(?:0?[1,3-9]|1[0-2])\2))(?:(?:1[6-9]|[2-9]\d)?\d{2})$|^(?:29(\/|-|\.)0?2\3(?:(?:(?:1[6-9]|[2-9]\d)?(?:0[48]|[2468][048]|[13579][26])|(?:(?:16|[2468][048]|[3579][26])00))))$|^(?:0?[1-9]|1\d|2[0-8])(\/|-|\.)(?:(?:0?[1-9])|(?:1[0-2]))\4(?:(?:1[6-9]|[2-9]\d)?\d{2})$/', $date)) {
            return true;
        }

        return false;
    }

    public static function phoneValidation($phone)
    {
        $phone = trim($phone);

        if (preg_match('/^([0-9\(\)\+\s\-]*)$/', $phone)) {
            return true;
        }

        return false;
    }

    public static function emailValidation($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function numberOnly($string)
    {
        return trim(preg_replace('/[^0-9]/', '', $string));
    }

    public static function hashPassword($raw_password)
    {
        return crypt($raw_password, '$1$' . md5($raw_password) . '$');
    }

    public static function hashId($raw_id)
    {
        $raw_id = (int) $raw_id;

        $padded_id = sprintf('%08s', dechex($raw_id));
        $string_array = str_split($padded_id, 2);

        return implode(array_reverse($string_array));
    }

    public static function unhashId($hash_id)
    {
        $hash_id = trim($hash_id);

        $string_array = str_split($hash_id, 2);
        $padded_id = implode(array_reverse($string_array));

        return hexdec($padded_id);
    }

    public static function upperFirstLetter($string)
    {
        $string = strip_tags($string);
        $string = trim(mb_strtoupper(mb_substr($string, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($string, 1, mb_strlen($string, 'UTF-8'), 'UTF-8'));

        return $string;
    }

    public static function upperFirstLetters($string)
    {
        $string = strip_tags($string);

        $string = preg_replace_callback('/(\w)\s+([\)\}])/u', function($matches) {
            return $matches[1] . $matches[2];
        }, trim($string));

        $string = preg_replace_callback('/([\(\{])\s+(\w)/u', function($matches) {
            return $matches[1] . $matches[2];
        }, trim($string));

        $string = preg_replace_callback('/(\w)([\(\{])/u', function($matches) {
            return $matches[1] . ' ' . $matches[2];
        }, trim($string));

        $string = preg_replace_callback('/([\)\}])(\w)/u', function($matches) {
            return $matches[1] . ' ' . $matches[2];
        }, trim($string));

        $string = preg_replace_callback('/(\w)([\/])/u', function($matches) {
            return $matches[1] . ' ' . $matches[2];
        }, trim($string));

        $string = preg_replace_callback('/([\/])(\w)/u', function($matches) {
            return $matches[1] . ' ' . $matches[2];
        }, trim($string));

        $string = preg_replace_callback('/([\;\!\?])\s+(\w)/u', function($matches) {
            return mb_strtoupper($matches[1] . ' ' . $matches[2], 'UTF-8');
        }, trim(mb_convert_case($string, MB_CASE_TITLE, 'UTF-8')));

        $string = preg_replace_callback('/([\.\;\!\?])(\w)/u', function($matches) {
            return mb_strtoupper($matches[1] . $matches[2], 'UTF-8');
        }, trim(mb_convert_case($string, MB_CASE_TITLE, 'UTF-8')));

        return $string;
    }

    public static function slug($string, $separator = '-')
    {
        $string = self::ascii($string);
        $string = trim(preg_replace('/[^a-zA-Z0-9]/', ' ', $string));
        $string = trim(preg_replace('/[\s]+/', ' ', $string));
        $string = trim(preg_replace('/\s/', $separator, $string));

        if ($string != '') {
            return strtolower($string);
        } else {
            return 'n-a';
        }
    }

    public static function ascii($string)
    {
        $string = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $string);
        $string = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $string);
        $string = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $string);
        $string = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $string);
        $string = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $string);
        $string = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $string);
        $string = preg_replace('/(đ)/', 'd', $string);

        $string = preg_replace('/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/', 'A', $string);
        $string = preg_replace('/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/', 'E', $string);
        $string = preg_replace('/(Ì|Í|Ị|Ỉ|Ĩ)/', 'I', $string);
        $string = preg_replace('/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/', 'O', $string);
        $string = preg_replace('/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/', 'U', $string);
        $string = preg_replace('/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/', 'Y', $string);
        $string = preg_replace('/(Đ)/', 'D', $string);

        $string = trim($string);

        return $string;
    }

    public static function removeJunkSpace($string)
    {
        $words = array_filter(explode(' ', trim($string)));
        return trim(implode(' ', $words));
    }

    public static function punctuation($string)
    {
        $punctuation = '\;\:';
        $spaced_punctuation = array(
            ' ?',
            ' !',
            ' .',
            ' ,',
            ' ;',
            '( ',
            ' )'
        );
        $unspaced_punctuation = array(
            '?',
            '!',
            '.',
            ',',
            ';',
            '(',
            ')'
        );

        $string = preg_replace('/([\,\;\:])+/iS', '$1', $string);
        $string = preg_replace('/[[:space:]]+/', ' ', $string);
        $string = str_replace($spaced_punctuation, $unspaced_punctuation, $string);
        $string = preg_replace('/([' . $punctuation . '])[\s]*/', '\1 ', $string);
        $string = preg_replace('/(?<!\d),|,(?!\d{3})/', ', ', $string);
        $string = preg_replace('/(\.)([[:alpha:]]{2,})/', '$1 $2', $string);
        $string = trim($string);
        $string = preg_replace('/([\.!\?]\s+|\A)(\w)/e', '"$1" . strtoupper("$2")', $string);

        if ($string[strlen($string) - 1] == ',') {
            $string = substr($string, 0, -1) . '.';
        }

        $string = trim($string);

        return $string;
    }

    public static function niceWordsByChars($text, $max_char = 100, $end = '...')
    {
        $text = trim(strip_tags($text));
        $max_char = (int) $max_char;
        $end = trim($end);

        if ($text != '') {
            $text = self::removeJunkSpace($text);
        }

        $output = '';

        if (mb_strlen($text, 'UTF-8') > $max_char) {
            $words = explode(' ', $text);
            $i = 0;

            while (1) {
                $length = mb_strlen($output, 'UTF-8') + mb_strlen($words[$i], 'UTF-8');

                if ($length > $max_char) {
                    break;
                } else {
                    $output .= ' ' . $words[$i];
                    ++$i;
                }
            }

            $output .= $end;
        } else {
            $output = $text;
        }

        return trim($output);
    }

    public static function cutTextByChars($text, $max_char, $end = '...')
    {
        $text = trim($text);
        $max_char = (int) $max_char;
        $end = trim($end);

        $words_array = preg_split('//', $text, -1, PREG_SPLIT_NO_EMPTY);
        $string = '';

        if (count($words_array) <= $max_char) {
            return $text;
        }

        for ($i = 0; $i < $max_char; $i++) {
            if (isset($words_array[$i])) {
                $string .= $words_array[$i];
            }
        }

        if (count($words_array) <= $max_char) {
            return trim($string);
        } else {
            return trim($string) . $end;
        }
    }

    public static function currencyFormat($number)
    {
        return number_format($number, 0, '.', ',');
    }

    public static function randomPassword($count = 10)
    {
        $random = '';
        $base = explode(' ', '0 1 2 3 4 5 6 7 8 9 a b c d e f g h i j k l m n o p q r s t u v w x y z');

        for ($i = 0; $i < $count; $i++) {
            $random .= $base[mt_rand(0, 35)];
        }
        $random = trim($random);

        return $random;
    }

    public static function randomNumbers($count = 10)
    {
        $random = '';
        $base = explode(' ', '0 1 2 3 4 5 6 7 8 9');

        for ($i = 0; $i < $count; $i++) {
            $random .= $base[mt_rand(0, 9)];
        }
        $random = trim($random);

        return $random;
    }

    public static function token()
    {
        return md5(uniqid() . time());
    }

    public static function curlGet($url, $get = array(), $options = array())
    {
        $url = trim($url);
        if (is_array($get) && count($get)) {
            $url .= '?' . http_build_query($get);
        }

        $defaults = array(
            CURLOPT_URL => $url,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function curlPost($url, $post = array(), $options = array())
    {
        $url = trim($url);
        if (is_array($post) && count($post)) {
            $data = http_build_query($post);
        } else {
            $data = $post;
        }

        $defaults = array(
            CURLOPT_POST => true,
            CURLOPT_URL => $url,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HEADER => false,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_FORBID_REUSE => true,
            CURLOPT_FRESH_CONNECT => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
        );

        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function curlPostJson($url, $post = array())
    {
        $url = trim($url);
        if (is_array($post) && count($post)) {
            $data = json_encode($post);
        } else {
            $data = $post;
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data))
        );

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function countFormat($number, $point = '.', $separator = ',')
    {
        if ($number < 0) {
            return 0;
        }

        if ($number < 10000) {
            return number_format($number, 0, $point, $separator);
        }

        $d = $number < 1000000 ? 1000 : 1000000;
        $f = floor($number / $d);

        $split = ($d == 1000) ? 'K' : 'M';
        $plus = '';

        if ($number % $d != 0) {
            $plus = '+';
        }

        return number_format($f, 0, $point, $separator) . $split . $plus;
    }

    public static function stringToHex($string)
    {
        $hex = '';

        for ($i = 0; $i < strlen($string); $i++) {
            $hex .= dechex(ord($string[$i]));
        }

        return $hex;
    }

    public static function hexToString($hex)
    {
        $string = '';

        for ($i = 0; $i < strlen($hex) - 1; $i += 2) {
            $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
        }

        return $string;
    }

    public static function truncateText($text, $length = 100, $options = array())
    {
        $default = array(
            'ending' => '...',
            'exact' => true,
            'html' => false
        );
        $options = array_merge($default, $options);

        if (!function_exists('mb_strlen')) {
            class_exists('Multibyte');
        }

        if ($options['html']) {
            if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
                return $text;
            }
            $total_length = mb_strlen(strip_tags($options['ending']));
            $open_tags = array();
            $truncate = '';

            $tags = array();
            $close_tag = array();
            $entities = array();
            preg_match_all('/(<\/?([\w+]+)[^>]*>)?([^<>]*)/', $text, $tags, PREG_SET_ORDER);
            foreach ($tags as $tag) {
                if (!preg_match('/img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param/s', $tag[2])) {
                    if (preg_match('/<[\w]+[^>]*>/s', $tag[0])) {
                        array_unshift($open_tags, $tag[2]);
                    } elseif (preg_match('/<\/([\w]+)[^>]*>/s', $tag[0], $close_tag)) {
                        $pos = array_search($close_tag[1], $open_tags);
                        if ($pos !== false) {
                            array_splice($open_tags, $pos, 1);
                        }
                    }
                }
                $truncate .= $tag[1];

                $content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $tag[3]));
                if ($content_length + $total_length > $length) {
                    $left = $length - $total_length;
                    $entities_length = 0;
                    if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $tag[3], $entities, PREG_OFFSET_CAPTURE)) {
                        foreach ($entities[0] as $entity) {
                            if ($entity[1] + 1 - $entities_length <= $left) {
                                $left--;
                                $entities_length += mb_strlen($entity[0]);
                            } else {
                                break;
                            }
                        }
                    }

                    $truncate .= mb_substr($tag[3], 0, $left + $entities_length);
                    break;
                } else {
                    $truncate .= $tag[3];
                    $total_length += $content_length;
                }
                if ($total_length >= $length) {
                    break;
                }
            }
        } else {
            if (mb_strlen($text) <= $length) {
                return $text;
            } else {
                $truncate = mb_substr($text, 0, $length - mb_strlen($options['ending']));
            }
        }

        if (!$options['exact']) {
            $space_pos = mb_strrpos($truncate, ' ');
            if ($options['html']) {
                $last_tag_matches = array();
                $dropped_tags = array();

                $truncate_check = mb_substr($truncate, 0, $space_pos);
                $last_open_tag = mb_strrpos($truncate_check, '<');
                $last_close_tag = mb_strrpos($truncate_check, '>');
                if ($last_open_tag > $last_close_tag) {
                    preg_match_all('/<[\w]+[^>]*>/s', $truncate, $last_tag_matches);
                    $last_tag = array_pop($last_tag_matches[0]);
                    $space_pos = mb_strrpos($truncate, $last_tag) + mb_strlen($last_tag);
                }
                $bits = mb_substr($truncate, $space_pos);
                preg_match_all('/<\/([a-z]+)>/', $bits, $dropped_tags, PREG_SET_ORDER);
                if (!empty($dropped_tags)) {
                    if (!empty($open_tags)) {
                        foreach ($dropped_tags as $closing_tag) {
                            if (!in_array($closing_tag[1], $open_tags)) {
                                array_unshift($open_tags, $closing_tag[1]);
                            }
                        }
                    } else {
                        foreach ($dropped_tags as $closing_tag) {
                            $open_tags[] = $closing_tag[1];
                        }
                    }
                }
            }
            $truncate = mb_substr($truncate, 0, $space_pos);
        }
        $truncate .= $options['ending'];

        if ($options['html']) {
            foreach ($open_tags as $tag) {
                $truncate .= '</' . $tag . '>';
            }
        }

        return $truncate;
    }

    public static function filterBlacklist($string)
    {
        $blacklist = array(
            'www.',
            'http://',
            'https://'
        );

        $string = trim($string);
        if (count($blacklist) && $string != '') {
            $string = str_replace($blacklist, '', $string);
            $string = trim($string);
        }

        $string = trim(preg_replace('/<img[^>]+\>/i', '', $string));
        $string = trim(preg_replace('/<a[^>]+\>/i', '', $string));
        $string = trim(preg_replace('/<\/a>/i', '', $string));

        return $string;
    }

    public static function filterHtmlTag($string)
    {
        $string = trim($string);

        $matches = array();
        preg_match_all('~<a.*>~isU', $string, $matches);

        if (count($matches) && isset($matches[0])) {
            for ($i = 0; $i <= sizeof($matches[0]); $i++) {
                if (isset($matches[0][$i]) && !preg_match('~nofollow~is', $matches[0][$i])) {
                    $result = trim($matches[0][$i], '>');
                    $result .= ' rel="nofollow">';
                    $string = str_replace($matches[0][$i], $result, $string);
                }
            }
        }

        return $string;
    }

    public static function getTimeAgo($timestamp)
    {
        if (!is_numeric($timestamp)) {
            return '';
        }

        // Get Current Timestamp
        $current_timestamp = time();
        if ($timestamp >= $current_timestamp) {
            return '';
        }

        // Get Difference between Current Timestamp
        // and Timestamp of item
        $difference = $current_timestamp - $timestamp;

        // Save the periods in an array
        $periods = array('giây', 'phút', 'giờ', 'ngày', 'tuần', 'tháng', 'năm');

        // Save the lengths of each period in another array
        $lengths = array('60', '60', '24', '7', '4.35', '12');

        // The loop below will go through the lengths array
        // starting at key 0 and incrementing each time the
        // loop is run
        // Each time, the $difference variable is divided by the
        // length being accessed
        // The loop will stop running once $difference is smaller
        // than the value of the length being accessed at the time
        // The aim of this loop is find out how old the item is
        for ($j = 0; $difference >= $lengths[$j]; $j++) {
            $difference /= $lengths[$j];
        }

        // If 'j' is equal to 6, this means the item was added
        // over a month ago. In this case, we simply want to
        // display the month and the year.
        if ($j < 6) {
            // On the other hand, if the item is more recent, we use
            // the code below to display the age of the item such as:
            // '3 days ago' or '7 hours ago'
            $difference = round($difference);

            /*
            if ($difference != 1) {
                $periods[$j] .= 's';
            }
            */

            $return = $difference . ' ' . $periods[$j] . ' trước';
        } else {
            $return = date('d-m-Y H:i', $timestamp);
        }

        return $return;
    }

    public static function formatPhoneNumber($string)
    {
        $string = sprintf(
            '%s %s %s',
            substr($string, 0, 3),
            substr($string, 3, 3),
            substr($string, 6)
        );

        return $string;
    }
}