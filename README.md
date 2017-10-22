# animCJK

The purpose of this project is to display kanji (japanese characters) or hanzi (chinese characters) stroke by stroke.

For a demo, see http://gooo.free.fr/animCJK/official

Each character has a corresponding svg file in animCJK repository that contains paths defining its shape and some css code to animate them. The name of the svg is the decimal unicode of the character followed by the "svg" extension.

The svgsJa folder contains svg files corresponding to the "jōyō kanji" (2136 characters).

The svgsZhHans folder contains svg files corresponding to the "frequently used simplified hanzi" (3500 characters).

Note that some character svg files are in both svgsJa and svgsZhHans. However, take care because characters are not always the same in japanese and in chinese even when they share the same unicode.

Each svg can be inserted as is in a web page, or with some modification using for instance javascript.

To animate the character, one use the following method: the character shape is split in several paths (one per stroke). One uses these paths as clip-path, and one draws over these paths very large dashed lines. Initially, the space between two dashes is very large. Using a css animation, one reduces this space to zero. As a result, one has the impression that the strokes are drawn gradually.

If several characters are inserted in the same page, and if one need to animate them one after the other, one need to modify the animation-delay of the css of the svg. If the same character is displayed several times in the same page, one also need to modify the id of its elements. This can be done using javascript. Alternately, one can encapsulate each svg in an iframe.

This project is derived from the remarquable makemeahanzi project which is designed to display chinese characters. See https://github.com/skishore/makemeahanzi for more details. However, many characters were modified or added in animCJK (about one thousand at the moment) because in japanese, some characters have a different stroke order, or have a different glyph, or have a different stroke direction or are not commonly used in chinese (and therefore have no entry in makemeahanzi).

Even if character shapes have the same look in both makemeahanzi and animCJK project, the svg files are completely different. The css is different. The svg structure is different. The "median" paths are differents. However, for people who would to reimport characters that were modified or added from animCJK to makemeahanzi, two files are provided (graphicsJa.txt and graphicsZhHans.txt) that have the same format as the graphics.txt of the makemeahanzi project.
