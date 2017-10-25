# animCJK

The purpose of this project is to display kanji (Japanese characters) or hanzi (Chinese characters) stroke by stroke.

For a demo, see http://gooo.free.fr/animCJK/official

Each character has a corresponding svg file in animCJK repository that contains paths defining its shape and some css code to animate it. The name of the svg is the decimal unicode of the character followed by the "svg" extension.

The svgsJa folder contains svg files corresponding to the "jōyō kanji" (2136 characters).

The svgsZhHans folder contains svg files corresponding to the "frequently used simplified hanzi" (3500 characters).

Note that some svg files are in both svgsJa and svgsZhHans. However, take care because characters are not always the same in Japanese and in Chinese even when they share the same unicode.

Each svg can be inserted as is in a web page, or with some modifications using for instance javascript.

To animate a character, animCJK uses the following method: the character shape is split in several paths (one per stroke). These paths are used as clip-path, and dashed lines are drawn over these paths. Initially, the space between two dashes is very large. Using a css animation, this space is reduced to zero. As a result, one has the impression that the strokes are drawn gradually.

If several characters are inserted in the same page, and if one need to animate them one after the other, one has to modify the animation-delay in the css of the svg. If a character is displayed several times in the same page, one also need to modify the id of its elements. This can be done using javascript. Alternately, one can encapsulate each svg in an iframe.

This project is derived from the remarquable makemeahanzi project which is designed to display Chinese characters. See https://github.com/skishore/makemeahanzi for more details. Many characters were modified or added in animCJK (about one thousand at the moment) because in Japanese, some characters have a different stroke order, or have a different glyph, or have a different stroke direction or are not commonly used in chinese (and therefore have no entry in makemeahanzi).

Even if character shapes have the same look in both makemeahanzi and animCJK project, the svg files are completely different. The css is different. The svg structure is different. The "median" paths are different. However, for people who would to reimport characters that were modified or added from animCJK to makemeahanzi, two files are provided (graphicsJa.txt and graphicsZhHans.txt) that have the same format as the graphics.txt of the makemeahanzi project.
