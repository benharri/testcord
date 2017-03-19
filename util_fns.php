<?php

require __DIR__.'/vendor/autoload.php';
include_once __DIR__.'/env_stuff.php';

function char_in($str) {
    for ($i = 0; $i <= strlen($str); $i++)
        yield substr($str, $i, 1);
}

function send($msg, $txt, $embed = null) {
    if (strlen($txt) > 2000) {
        $split = str_split($txt, 2000);
        print_r($split);
        foreach ($split as $part) {
            $msg->channel->sendMessage($part, false, $embed);
        }
    }
    return $msg->channel->sendMessage($txt, false, $embed);
}

function is_dm($msg) {
    return $msg->author instanceOf Discord\Parts\User\User;
}


function ascii_from_img($filepath) {
    $ret = "";
    $img = imagecreatefromstring(file_get_contents($filepath));
    list($width, $height) = getimagesize($filepath);
    $scale = 10;
    $chars = [
        ' ', '\'', '.', ':',
        '|', 'T',  'X', '0',
        '#',
    ];
    $chars = array_reverse($chars);
    $c_count = count($chars);
    for($y = 0; $y <= $height - $scale - 1; $y += $scale) {
        for($x = 0; $x <= $width - ($scale / 2) - 1; $x += ($scale / 2)) {
            $rgb = imagecolorat($img, $x, $y);
            $r = (($rgb >> 16) & 0xFF);
            $g = (($rgb >> 8) & 0xFF);
            $b = ($rgb & 0xFF);
            $sat = ($r + $g + $b) / (255 * 3);
            $ret .= $chars[ (int)( $sat * ($c_count - 1) ) ];
        }
        $ret .= "\n";
    }
    return $ret;
}


function format_weather($json) {
    $fahr = round($json->main->temp * 5 / 9 + 32);
    $ret = <<<EOD
it's {$json->main->temp}°C ({$fahr}°F) with {$json->weather[0]->description} in {$json->name}, {$json->sys->country}
EOD;
    return $ret;
}



function register_help($cmd_name) {
    global $discord; global $help;
    $help[$cmd_name] = $discord->getCommand($cmd_name)->getHelp(';')["text"];
}


function ask_cleverbot($input) {
    $url = "https://www.cleverbot.com/getreply";
    $key = get_thing('cleverbot');
    $input = rawurlencode($input);
    $apidata = json_decode(file_get_contents("$url?input=$input&key=$key"));

    return $apidata->output;
}




