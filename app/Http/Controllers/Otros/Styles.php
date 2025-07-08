<?php

namespace App\Http\Controllers\Otros;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class Styles
{
    public static $FONT_BLACK = ["font" => ["color" => ["argb" => "000000"]]];
    public static $FONT_BLUE  = ["font" => ["color" => ["argb" => "0070c0"]]];
    public static $FONT_BOLD  = ["font" => ["bold" => true]];
    public static $FONT_WHITE = ["font" => ["color" => ["argb" => "ffffff"]]];
    public static $FONT_ARIAL = ["font" => ["name"  => "Arial"]];

    public static $BORDER_ALL_THIN = ["borders" => [
        "allBorders" => ["borderStyle" => Border::BORDER_THIN]
    ]];

    public static $BORDER_BOTTOM_THIN = ["borders" => [
        "bottom" => ["borderStyle" => Border::BORDER_THIN]
    ]];

    public static $BORDER_EXTERNAL_BOLD = ["borders" => [
        "outline" => ["borderStyle" => Border::BORDER_MEDIUM]
    ]];

    public static $HORIZONTAL_CENTER = ["alignment" => ["horizontal" => Alignment::HORIZONTAL_CENTER]];
    public static $VERTICAL_CENTER = ["alignment" => ["vertical" => Alignment::VERTICAL_CENTER]];
    public static $WRAP_TEXT = ["alignment" => ["wrapText" => true]];

    public static $BG_BLUE = ['fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => "0070c0"]
    ]];

    public static $BG_GRAY = ['fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => "BFBFBF"]
    ]];

    public static $BG_GRAY2 = ['fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => "D9D9D9"]
    ]];

    public static $BG_WHITE = ['fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => "ffffff"]
    ]];

    public static $BG_YELLOW = ['fill' => [
        'fillType' => Fill::FILL_SOLID,
        'color' => ['argb' => "FFC000"]
    ]];

    public static function FontSize($font_size)
    {
        return ["font" => ["size" => $font_size]];
    }

    public static function FontFamily($font_family)
    {
        return ["font" => ["name" => $font_family]];
    }
}
