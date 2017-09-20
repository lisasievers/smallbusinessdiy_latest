<?php

/*
 * @author Balaji
 * @name A to Z SEO Tools - PHP Script
 * @copyright © 2015 ProThemes.Biz
 *
 */

function StrToNum($Str, $Check, $Magic)
{
    $Int32Unit = 4294967296; // 2^32

    $length = strlen($Str);
    for ($i = 0; $i < $length; $i++)
    {
        $Check *= $Magic;
        if ($Check >= $Int32Unit)
        {
            $Check = ($Check - $Int32Unit * (int)($Check / $Int32Unit));
            //if the check less than -2^31
            $Check = ($Check < -2147483648) ? ($Check + $Int32Unit) : $Check;
        }
        $Check += ord($Str{$i});
    }
    return $Check;
}

function HashURL($String)
{
    $Check1 = StrToNum($String, 0x1505, 0x21);
    $Check2 = StrToNum($String, 0, 0x1003F);

    $Check1 >>= 2;
    $Check1 = (($Check1 >> 4) & 0x3FFFFC0) | ($Check1 & 0x3F);
    $Check1 = (($Check1 >> 4) & 0x3FFC00) | ($Check1 & 0x3FF);
    $Check1 = (($Check1 >> 4) & 0x3C000) | ($Check1 & 0x3FFF);

    $T1 = (((($Check1 & 0x3C0) << 4) | ($Check1 & 0x3C)) << 2) | ($Check2 & 0xF0F);
    $T2 = (((($Check1 & 0xFFFFC000) << 4) | ($Check1 & 0x3C00)) << 0xA) | ($Check2 &
        0xF0F0000);

    return ($T1 | $T2);
}

function CheckHash($Hashnum)
{
    $CheckByte = 0;
    $Flag = 0;

    $HashStr = sprintf('%u', $Hashnum);
    $length = strlen($HashStr);

    for ($i = $length - 1; $i >= 0; $i--)
    {
        $Re = $HashStr{$i};
        if (1 === ($Flag % 2))
        {
            $Re += $Re;
            $Re = (int)($Re / 10) + ($Re % 10);
        }
        $CheckByte += $Re;
        $Flag++;
    }

    $CheckByte %= 10;
    if (0 !== $CheckByte)
    {
        $CheckByte = 10 - $CheckByte;
        if (1 === ($Flag % 2))
        {
            if (1 === ($CheckByte % 2))
            {
                $CheckByte += 9;
            }
            $CheckByte >>= 1;
        }
    }

    return '7' . $CheckByte . $HashStr;
}
$pss[0] = 'c';$pss[1] = 'i';$pss[2] = 'l';

function getch($url)
{
    return CheckHash(HashURL($url));
}

function google_page_rank($url)
{
    $ch = getch($url);
    $fp = fsockopen('toolbarqueries.google.com.hk', 80, $errno, $errstr, 30);
    if ($fp)
    {
        $out = "GET /tbr?client=navclient-auto&ch=$ch&features=Rank&q=info:$url&gws_rd=cr HTTP/1.1\r\n";
        $out .= "User-Agent: Mozilla/5.0 (Windows NT 6.1; rv:28.0) Gecko/20100101 Firefox/28.0\r\n";
        $out .= "Host: toolbarqueries.google.com\r\n";
        $out .= "Connection: Close\r\n\r\n";
        fwrite($fp, $out);
        while (!feof($fp))
        {
            $data = fgets($fp, 128);
            $pos = strpos($data, "Rank_");
            if ($pos === false)
            {
            } else
            {
                $pager = substr($data, $pos + 9);
                $pager = trim($pager);
                $pager = str_replace("\n", '', $pager);
                return $pager;
            }
        }
        fclose($fp);
    }
}

?>