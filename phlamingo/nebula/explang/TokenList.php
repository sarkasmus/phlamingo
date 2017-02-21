<?php

    /**
     * @author Michal Doubek <michal@doubkovi.cz>
     * @license LGPL 3
     *
     * This code is distributed under LGPL license version 3
     * For full license information view LICENSE file which is
     * Distributed with this source code
     *
     * This source code is part of Phlamingo project
     */

    namespace Phlamingo\Nebula\ExpLang;

    use Phlamingo\Core\Object;


    /**
     * {Description}
     */
    class TokenList extends Object
    {
        const T_FOREACH = 0;
        const T_FOR = 1;
        const T_IF = 2;
        const T_ELSEIF = 3;
        const T_AS = 4;
        const T_STRING = 5;
        const T_AND = 6;
        const T_OR = 7;
        const T_BLOCK = 8;
        const T_WHILE = 9;
        const T_ENDFOREACH = 10;
        const T_ENDFOR = 11;
        const T_ENDIF = 12;
        const T_ARROW = 13;
        const T_BOOL = 14;
        const T_DECREMENT = 15;
        const T_DIVIDE = 16;
        const T_MULTIPLY = 17;
        const T_SUBSTRACT = 18;
        const T_ADD = 19;
        const T_ELSE = 20;
        const T_ENDWHILE = 21;
        const T_EQUAL = 22;
        const T_GREATER_THAN = 23;
        const T_GREATER_OR_EQUAL_THAN = 24;
        const T_SMALLER_THAN = 25;
        const T_SMALLER_OR_EQUAL_THAN = 26;
        const T_INTEGER = 27;
        const T_IS_EQUAL = 28;
        const T_IS_SAME = 29;
        const T_NOT_EQUAL = 30;
        const T_NOT_SAME = 31;
        const T_MODULO = 32;
        const T_NEGATE = 33;
        const T_ENDBLOCK = 34;
        const T_NOESCAPE  = 35;
        const T_REPEAT = 36;
        const T_LINK = 37;
        const T_ENDREPEAT = 38;
        const T_INCREMENT = 39;
        const T_SEMICOLON = 40;
        const T_LEFT_BRACKET = 41;
        const T_RIGHT_BRACKET = 42;
        const T_LEFT_SQUARE_BRACKET = 43;
        const T_RIGHT_SQUARE_BRACKET = 44;
        const T_MARKDOWN = 45;
        const T_ENDMARKDOWN = 46;
        const T_ICON = 47;
        const T_LOAD = 48;
        const T_CONTAINER = 49;
        const T_ENDCONTAINER = 50;
        const T_ROW = 51;
        const T_ENDROW = 52;
        const T_COLUMN = 53;
        const T_ENDCOLUMN = 54;

        const DICTIONARY = [
            0 => "T_FOREACH",
            1 => "T_FOR",
            2 => "T_IF",
            3 => "T_ELSEIF",
            4 => "T_AS",
            5 => "T_STRING",
            6 => "T_AND",
            7 => "T_OR",
            8 => "T_BLOCK",
            9 => "T_WHILE",
            10 => "T_ENDFOREACH",
            11 => "T_ENDFOR",
            12 => "T_ENDIF",
            13 => "T_ARROW",
            14 => "T_BOOL",
            15 => "T_DECREMENT",
            16 => "T_DIVIDE",
            17 => "T_MULTIPLY",
            18 => "T_SUBSTRACT",
            19 => "T_ADD",
            20 => "T_ELSE",
            21 => "T_ENDWHILE",
            22 => "T_EQUAL",
            23 => "T_GREATER_THAN",
            24 => "T_GREATER_OR_EQUAL_THAN",
            25 => "T_SMALLER_THAN",
            26 => "T_SMALLER_OR_EQUAL_THAN",
            27 => "T_INTEGER",
            28 => "T_IS_EQUAL",
            29 => "T_IS_SAME",
            30 => "T_NOT_EQUAL",
            31 => "T_NOT_SAME",
            32 => "T_MODULO",
            33 => "T_NEGATE",
            34 => "T_ENDBLOCK",
            35 => "T_NOESCAPE",
            36 => "T_REPEAT",
            37 => "T_LINK",
            38 => "T_ENDREPEAT",
            39 => "T_INCREMENT",
            40 => "T_SEMICOLON",
            41 => "T_LEFT_BRACKET",
            42 => "T_RIGHT_BRACKET",
            43 => "T_LEFT_SQUARE_BRACKET",
            44 => "T_RIGHT_SQUARE_BRACKET",
            45 => "T_MARKDOWN",
            46 => "T_ENDMARKDOWN",
            47 => "T_ICON",
            48 => "T_LOAD",
            49 => "T_CONTAINER",
            50 => "T_ENDCONTAINER",
            51 => "T_ROW",
            52 => "T_ENDROW",
            53 => "T_COLUMN",
            54 => "T_ENDCOLUMN",
        ];
    }