#!/usr/bin/env python3
"""Generate 1200x630 social preview card for animCJK."""
from PIL import Image, ImageDraw, ImageFont

W, H = 1200, 630
img = Image.new("RGB", (W, H), "#fbfbf9")
d = ImageDraw.Draw(img)

CJK = "/usr/share/fonts/truetype/wqy/wqy-zenhei.ttc"
DEJAVU_B = "/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf"
DEJAVU = "/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf"

f_glyph = ImageFont.truetype(CJK, 330)
f_title = ImageFont.truetype(DEJAVU_B, 96)
f_tag = ImageFont.truetype(DEJAVU, 40)
f_url = ImageFont.truetype(DEJAVU, 28)

INK = "#202124"
GRAY = "#5f6368"
LIGHT = "#9aa0a6"
RED = "#c00"

# faint vertical guide + red top swoosh accent
d.rectangle([0, 0, W, 14], fill=RED)
d.rectangle([90, 118, 118, 500], fill="#f0f0ec")  # subtle glyph backdrop panel

# big CJK glyph 永 (all basic strokes) on the left
d.text((118, 150), "永", font=f_glyph, fill=INK)

# right side text block
x = 480
d.text((x, 190), "animCJK", font=f_title, fill=INK)
tw = d.textlength("animCJK", font=f_title)
# red underline whisker under wordmark
d.rectangle([x, 312, x + tw, 324], fill=RED)

d.text((x, 360), "Animated stroke order for", font=f_tag, fill=GRAY)
d.text((x, 410), "Kanji, Kana, Hanja & Hanzi", font=f_tag, fill=GRAY)
d.text((x, 500), "Free & open source SVG characters", font=f_url, fill=LIGHT)
d.text((x, 546), "parsimonhi.github.io/animCJK", font=f_url, fill=INK)

img.save("/home/paperclip/adventure-agent/work/animCJK/social-preview.png", "PNG")
print("saved", img.size)
