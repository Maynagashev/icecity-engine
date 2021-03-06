<?php

/******************************************************************************
 *                                                                            *
 *   Bdo.php, v 0.00 2007/04/29 - This is part of xBB library                 *
 *   Copyright (C) 2006-2007  Dmitriy Skorobogatov  dima@pc.uz                *
 *                                                                            *
 *   This program is free software; you can redistribute it and/or modify     *
 *   it under the terms of the GNU General Public License as published by     *
 *   the Free Software Foundation; either version 2 of the License, or        *
 *   (at your option) any later version.                                      *
 *                                                                            *
 *   This program is distributed in the hope that it will be useful,          *
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of           *
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            *
 *   GNU General Public License for more details.                             *
 *                                                                            *
 *   You should have received a copy of the GNU General Public License        *
 *   along with this program; if not, write to the Free Software              *
 *   Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301 USA *
 *                                                                            *
 ******************************************************************************/

// РљР»Р°СЃСЃ РґР»СЏ С‚РµРіР° [bdo]
class Xbb_Tags_Bdo extends bbcode {
    public $behaviour = 'span';
    function get_html($tree = null) {
        $dir = '';
        switch (strtolower($this->attrib['bdo'])) {
            case 'ltr':
                $dir = 'ltr';
                break;
            case 'rtl':
                $dir = 'rtl';
        }
        if (! $dir) {
            return parent::get_html();
        }
        /* <bdo> РјРѕР¶РµС‚ РёРјРµС‚СЊ СЃР»РµРґСѓСЋС‰РёРµ Р°С‚СЂРёР±СѓС‚С‹: dir, lang, id, class, style,
           title. */
        $attr = 'dir="' . $dir . '" class="bb"';
        if (isset($this->attrib['lang'])) {
            $attr .= ' lang="' . htmlspecialchars($this->attrib['lang']) . '"';
        }
        return '<bdo ' . $attr . '>' . parent::get_html() . '</bdo>';
    }
}
?>