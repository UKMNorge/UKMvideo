<?php

function makesurethisisutf8($_string)
{
    $string = utf8_decode($_string);
    if (!mb_detect_encoding($string, 'UTF-8', true)) {
        $string = $_string;
    }

    $string = mb_convert_encoding(
        $string,
        'UTF-8',
        mb_detect_encoding($string)
    );

    $trans_from = ['Ã¸', 'Ã¥', 'Ã?', 'Ã¦', 'Ã…', 'Ã', 'â€™', 'Å˜', 'aÌŠ', 'Å©', 'â€“', 'Å‰', 'Å¡'];
    $trans_to = ['ø', 'å', 'Ø', 'æ', 'Å', 'Å', "'", 'Ø', 'å', 'é', '-', 'É', 'á'];

    return stripslashes(stripslashes(str_replace($trans_from, $trans_to, $string)));
}
