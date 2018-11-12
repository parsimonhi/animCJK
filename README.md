# AnimCJK

## Description

The purpose of this project is to display kanji and kana (Japanese characters) or hanzi (Chinese characters) stroke by stroke.

For a demo, see http://gooo.free.fr/animCJK/official

Each character has a corresponding svg file in AnimCJK repository that contains paths defining its shape and some css code to animate it. The name of the svg is the decimal unicode of the character followed by the "svg" extension.

## SVG files

The svgsJa folder contains svg files corresponding to the union of the Japanese "jōyō kanji" (2136 characters) and the Japanese "jinmeyō Kanji" (862 characters). As a result, svgsJa folder contains 2998 characters.

The svgsKana folder contains svg files corresponding to the Japanese "hiragana" (86 characters) and Japanese "katakana" (91 characters). As a result, svgsKana folder contains 177 characters.

The svgsZhHans folder contains svg files corresponding to Chinese "commonly used hanzi" (7000 characters). This set includes the "HSK hanzi" (2663) and the "frequently used simplified hanzi" (3500 characters). 38 characters of the "HSK hanzi" are not in the "frequently used simplified hanzi".

Note that some svg files are in both svgsJa and svgsZhHans. However, take care because characters are not always the same in Japanese and in Chinese even when they share the same unicode. For instance 勉 (21193.svg) in Japanese has not the same shape as 勉 (21193.svg) in Chinese.

Take care of compatibility characters such as 勉 (64051.svg) that has in Japanese the same shape as 勉 (21193.svg) in Chinese, but has not the same unicode.

Each svg can be inserted as is in a web page, or with some modifications using for instance javascript.

## Samples

Several samples are provided to show how to use AnimCJK.
These samples are stored in the samples folder.
See also http://gooo.free.fr/animCJK/official/samples

## Decomposition system

Character decompositions using a specific system to AnimCJK are stored in dictionaryJa.txt and dictionaryZhHans.txt).

A decomposition starts with a character, followed by its number of stroke (which indicates that the character is not decomposed), or followed by an ideographic description character (which indicates that the character is decomposed).
The ideographic description character is followed by several component decompositions (3 for "⿲" and "⿳", 2 for ⿰","⿱","⿴","⿵","⿶","⿷","⿸","⿹","⿺" and "⿻).
If a component has no corresponding character to represent it, its decomposition just starts with the ideographic description character.
If a component has no corresponding character to represent it and no decomposition, its decomposition starts by a "?" followed by its number of stroke.
A component may be represented by a character that has more strokes than it, followed by the decomposition of the component instead of the representing character.
A component may be represented by a character that has a different glyph, but is semantically the same as the component.
Special case 1: when a component is also the radical of the main character, a special mark is inserted just after the component character (actually a ".").
Special case 2: sometimes, a component is split in several parts (when some strokes of other components are drawn "between" its parts). In such a case, each part is represented by a specific decomposition starting with the component character which is split, followed by a special mark (actually a ":"), followed by the decomposition of this part.
When there is more than one special mark, the radical special mark is inserted first.
It is mandatory to decompose a component which contains the radical of the main character. It is optional to decompose other components.
If a character has several possible decompositions, just concatenate them.

## Plugin for Wordpress

A plugin for Wordpress to insert animated kanji in a webpage using BBCode can be downloaded at:

http://gooo.free.fr/animCJK/animkanji_wp_plugin_page.php

## Technical details

To animate a character, AnimCJK uses the following method: the character shape is split in several paths (one per stroke). These paths are used as clip-path, and dashed lines are drawn over these paths. Initially, the space between two dashes is very large. Using a css animation, this space is reduced to zero. As a result, one has the impression that the strokes are drawn gradually.

If several characters are inserted in the same page, and if one need to animate them one after the other, one has to modify the animation-delay in the css of the svg. If a character is displayed several times in the same page, one also need to modify the id of its elements. This can be done using javascript. Alternately, one can encapsulate each svg in an iframe.

Note: some kana (those which have a stroke overlapping on itself as あ, ぬ etc.) are special. The stroke which overlaps is split in several parts. So automatic procedures on these characters require some specific codes.

## Related works

### Makemeahanzi

This project is derived from the remarquable Makemeahanzi project which is designed to display Chinese characters. See https://github.com/skishore/makemeahanzi for more details. Many characters were modified or added in AnimCJK (between two or three thousand at the moment) for various reasons:
- many Japanese characters have a different stroke order, or have a different glyph, or have a different stroke direction or are not commonly used in Chinese, and therefore have no entry in Makemeahanzi.
- many character shapes were just slightly modified to look prettier.
- some commonly used (but not frequently used) Chinese characters have no entry in Makemeahanzi. 

Even if character shapes have the same look in both Makemeahanzi and AnimCJK project, the svg files are completely different. The css is different. The svg structure is different. The "median" paths are different. However, for people who would to reimport characters that were modified or added from AnimCJK to Makemeahanzi, two files are provided (graphicsJa.txt and graphicsZhHans.txt) that have the same format as the graphics.txt of the Makemeahanzi project.

### Arphic PL KaitiM GB and Arphic PL UKai fonts

Makemeahanzi itself makes an extensive use of the Arphic PL KaitiM GB and Arphic PL UKai fonts generously provided by Arphic Technology. See https://apps.ubuntu.com/cat/applications/precise/fonts-arphic-gkai00mp/ and https://apps.ubuntu.com/cat/applications/fonts-arphic-ukai/ for more details about these fonts.

Many characters of AnimCJK are not present in these fonts (especially but not exclusively Japanese characters since these Arphic fonts are designed for Chinese). One used parts of other characters to design these missing characters and/or used various editors (mainly Inkscape and BBEdit) to modify their shape. One didn't use any other fonts.

### Animated_GIF

Part of "animated GIF" sample of AnimCJK is derived from a sample of Animated_GIF project called "basic". See https://github.com/sole/Animated_GIF for more details.

### Autocomplete

The samples/index.php script uses two files, "autocomplete.js" and "autocomplete.css", derived from https://www.w3schools.com/howto/howto_js_autocomplete.asp, then adapted for AnimCJK project.

## Licences

See https://github.com/parsimonhi/animCJK/blob/master/licenses/COPYING.txt for more details about licences concerning this project.
