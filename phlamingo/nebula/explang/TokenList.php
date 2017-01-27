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
    }