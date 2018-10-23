(function(f){if(typeof exports==="object"&&typeof module!=="undefined"){module.exports=f()}else if(typeof define==="function"&&define.amd){define([],f)}else{var g;if(typeof window!=="undefined"){g=window}else if(typeof global!=="undefined"){g=global}else if(typeof self!=="undefined"){g=self}else{g=this}g.Animated_GIF = f()}})(function(){var define,module,exports;return (function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);var f=new Error("Cannot find module '"+o+"'");throw f.code="MODULE_NOT_FOUND",f}var l=n[o]={exports:{}};t[o][0].call(l.exports,function(e){var n=t[o][1][e];return s(n?n:e)},l,l.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){
// (c) Dean McNamee <dean@gmail.com>, 2013.
//
// https://github.com/deanm/omggif
//
// Permission is hereby granted, free of charge, to any person obtaining a copy
// of this software and associated documentation files (the "Software"), to
// deal in the Software without restriction, including without limitation the
// rights to use, copy, modify, merge, publish, distribute, sublicense, and/or
// sell copies of the Software, and to permit persons to whom the Software is
// furnished to do so, subject to the following conditions:
//
// The above copyright notice and this permission notice shall be included in
// all copies or substantial portions of the Software.
//
// THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
// IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
// FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
// AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
// LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
// FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS
// IN THE SOFTWARE.
//
// omggif is a JavaScript implementation of a GIF 89a encoder and decoder,
// including animation and compression.  It does not rely on any specific
// underlying system, so should run in the browser, Node, or Plask.

function GifWriter(buf, width, height, gopts) {
  var p = 0;

  var gopts = gopts === undefined ? { } : gopts;
  var loop_count = gopts.loop === undefined ? null : gopts.loop;
  var global_palette = gopts.palette === undefined ? null : gopts.palette;

  if (width <= 0 || height <= 0 || width > 65535 || height > 65535)
    throw "Width/Height invalid."

  function check_palette_and_num_colors(palette) {
    var num_colors = palette.length;
    if (num_colors < 2 || num_colors > 256 ||  num_colors & (num_colors-1))
      throw "Invalid code/color length, must be power of 2 and 2 .. 256.";
    return num_colors;
  }

  // - Header.
  buf[p++] = 0x47; buf[p++] = 0x49; buf[p++] = 0x46;  // GIF
  buf[p++] = 0x38; buf[p++] = 0x39; buf[p++] = 0x61;  // 89a

  // Handling of Global Color Table (palette) and background index.
  var gp_num_colors_pow2 = 0;
  var background = 0;
  if (global_palette !== null) {
    var gp_num_colors = check_palette_and_num_colors(global_palette);
    while (gp_num_colors >>= 1) ++gp_num_colors_pow2;
    gp_num_colors = 1 << gp_num_colors_pow2;
    --gp_num_colors_pow2;
    if (gopts.background !== undefined) {
      background = gopts.background;
      if (background >= gp_num_colors) throw "Background index out of range.";
      // The GIF spec states that a background index of 0 should be ignored, so
      // this is probably a mistake and you really want to set it to another
      // slot in the palette.  But actually in the end most browsers, etc end
      // up ignoring this almost completely (including for dispose background).
      if (background === 0)
        throw "Background index explicitly passed as 0.";
    }
  }

  // - Logical Screen Descriptor.
  // NOTE(deanm): w/h apparently ignored by implementations, but set anyway.
  buf[p++] = width & 0xff; buf[p++] = width >> 8 & 0xff;
  buf[p++] = height & 0xff; buf[p++] = height >> 8 & 0xff;
  // NOTE: Indicates 0-bpp original color resolution (unused?).
  buf[p++] = (global_palette !== null ? 0x80 : 0) |  // Global Color Table Flag.
             gp_num_colors_pow2;  // NOTE: No sort flag (unused?).
  buf[p++] = background;  // Background Color Index.
  buf[p++] = 0;  // Pixel aspect ratio (unused?).

  // - Global Color Table
  if (global_palette !== null) {
    for (var i = 0, il = global_palette.length; i < il; ++i) {
      var rgb = global_palette[i];
      buf[p++] = rgb >> 16 & 0xff;
      buf[p++] = rgb >> 8 & 0xff;
      buf[p++] = rgb & 0xff;
    }
  }

  if (loop_count !== null) {  // Netscape block for looping.
    if (loop_count < 0 || loop_count > 65535)
      throw "Loop count invalid."
    // Extension code, label, and length.
    buf[p++] = 0x21; buf[p++] = 0xff; buf[p++] = 0x0b;
    // NETSCAPE2.0
    buf[p++] = 0x4e; buf[p++] = 0x45; buf[p++] = 0x54; buf[p++] = 0x53;
    buf[p++] = 0x43; buf[p++] = 0x41; buf[p++] = 0x50; buf[p++] = 0x45;
    buf[p++] = 0x32; buf[p++] = 0x2e; buf[p++] = 0x30;
    // Sub-block
    buf[p++] = 0x03; buf[p++] = 0x01;
    buf[p++] = loop_count & 0xff; buf[p++] = loop_count >> 8 & 0xff;
    buf[p++] = 0x00;  // Terminator.
  }


  var ended = false;

  this.addFrame = function(x, y, w, h, indexed_pixels, opts) {
    if (ended === true) { --p; ended = false; }  // Un-end.

    opts = opts === undefined ? { } : opts;

    // TODO(deanm): Bounds check x, y.  Do they need to be within the virtual
    // canvas width/height, I imagine?
    if (x < 0 || y < 0 || x > 65535 || y > 65535)
      throw "x/y invalid."

    if (w <= 0 || h <= 0 || w > 65535 || h > 65535)
      throw "Width/Height invalid."

    if (indexed_pixels.length < w * h)
      throw "Not enough pixels for the frame size.";

    var using_local_palette = true;
    var palette = opts.palette;
    if (palette === undefined || palette === null) {
      using_local_palette = false;
      palette = global_palette;
    }

    if (palette === undefined || palette === null)
      throw "Must supply either a local or global palette.";

    var num_colors = check_palette_and_num_colors(palette);

    // Compute the min_code_size (power of 2), destroying num_colors.
    var min_code_size = 0;
    while (num_colors >>= 1) ++min_code_size;
    num_colors = 1 << min_code_size;  // Now we can easily get it back.

    var delay = opts.delay === undefined ? 0 : opts.delay;

    // From the spec:
    //     0 -   No disposal specified. The decoder is
    //           not required to take any action.
    //     1 -   Do not dispose. The graphic is to be left
    //           in place.
    //     2 -   Restore to background color. The area used by the
    //           graphic must be restored to the background color.
    //     3 -   Restore to previous. The decoder is required to
    //           restore the area overwritten by the graphic with
    //           what was there prior to rendering the graphic.
    //  4-7 -    To be defined.
    // NOTE(deanm): Dispose background doesn't really work, apparently most
    // browsers ignore the background palette index and clear to transparency.
    var disposal = opts.disposal === undefined ? 0 : opts.disposal;
    if (disposal < 0 || disposal > 3)  // 4-7 is reserved.
      throw "Disposal out of range.";

    var use_transparency = false;
    var transparent_index = 0;
    if (opts.transparent !== undefined && opts.transparent !== null) {
      use_transparency = true;
      transparent_index = opts.transparent;
      if (transparent_index < 0 || transparent_index >= num_colors)
        throw "Transparent color index.";
    }

    if (disposal !== 0 || use_transparency || delay !== 0) {
      // - Graphics Control Extension
      buf[p++] = 0x21; buf[p++] = 0xf9;  // Extension / Label.
      buf[p++] = 4;  // Byte size.

      buf[p++] = disposal << 2 | (use_transparency === true ? 1 : 0);
      buf[p++] = delay & 0xff; buf[p++] = delay >> 8 & 0xff;
      buf[p++] = transparent_index;  // Transparent color index.
      buf[p++] = 0;  // Block Terminator.
    }

    // - Image Descriptor
    buf[p++] = 0x2c;  // Image Seperator.
    buf[p++] = x & 0xff; buf[p++] = x >> 8 & 0xff;  // Left.
    buf[p++] = y & 0xff; buf[p++] = y >> 8 & 0xff;  // Top.
    buf[p++] = w & 0xff; buf[p++] = w >> 8 & 0xff;
    buf[p++] = h & 0xff; buf[p++] = h >> 8 & 0xff;
    // NOTE: No sort flag (unused?).
    // TODO(deanm): Support interlace.
    buf[p++] = using_local_palette === true ? (0x80 | (min_code_size-1)) : 0;

    // - Local Color Table
    if (using_local_palette === true) {
      for (var i = 0, il = palette.length; i < il; ++i) {
        var rgb = palette[i];
        buf[p++] = rgb >> 16 & 0xff;
        buf[p++] = rgb >> 8 & 0xff;
        buf[p++] = rgb & 0xff;
      }
    }

    p = GifWriterOutputLZWCodeStream(
            buf, p, min_code_size < 2 ? 2 : min_code_size, indexed_pixels);
  };

  this.end = function() {
    if (ended === false) {
      buf[p++] = 0x3b;  // Trailer.
      ended = true;
    }
    return p;
  };
}

// Main compression routine, palette indexes -> LZW code stream.
// |index_stream| must have at least one entry.
function GifWriterOutputLZWCodeStream(buf, p, min_code_size, index_stream) {
  buf[p++] = min_code_size;
  var cur_subblock = p++;  // Pointing at the length field.

  var clear_code = 1 << min_code_size;
  var code_mask = clear_code - 1;
  var eoi_code = clear_code + 1;
  var next_code = eoi_code + 1;

  var cur_code_size = min_code_size + 1;  // Number of bits per code.
  var cur_shift = 0;
  // We have at most 12-bit codes, so we should have to hold a max of 19
  // bits here (and then we would write out).
  var cur = 0;

  function emit_bytes_to_buffer(bit_block_size) {
    while (cur_shift >= bit_block_size) {
      buf[p++] = cur & 0xff;
      cur >>= 8; cur_shift -= 8;
      if (p === cur_subblock + 256) {  // Finished a subblock.
        buf[cur_subblock] = 255;
        cur_subblock = p++;
      }
    }
  }

  function emit_code(c) {
    cur |= c << cur_shift;
    cur_shift += cur_code_size;
    emit_bytes_to_buffer(8);
  }

  // I am not an expert on the topic, and I don't want to write a thesis.
  // However, it is good to outline here the basic algorithm and the few data
  // structures and optimizations here that make this implementation fast.
  // The basic idea behind LZW is to build a table of previously seen runs
  // addressed by a short id (herein called output code).  All data is
  // referenced by a code, which represents one or more values from the
  // original input stream.  All input bytes can be referenced as the same
  // value as an output code.  So if you didn't want any compression, you
  // could more or less just output the original bytes as codes (there are
  // some details to this, but it is the idea).  In order to achieve
  // compression, values greater then the input range (codes can be up to
  // 12-bit while input only 8-bit) represent a sequence of previously seen
  // inputs.  The decompressor is able to build the same mapping while
  // decoding, so there is always a shared common knowledge between the
  // encoding and decoder, which is also important for "timing" aspects like
  // how to handle variable bit width code encoding.
  //
  // One obvious but very important consequence of the table system is there
  // is always a unique id (at most 12-bits) to map the runs.  'A' might be
  // 4, then 'AA' might be 10, 'AAA' 11, 'AAAA' 12, etc.  This relationship
  // can be used for an effecient lookup strategy for the code mapping.  We
  // need to know if a run has been seen before, and be able to map that run
  // to the output code.  Since we start with known unique ids (input bytes),
  // and then from those build more unique ids (table entries), we can
  // continue this chain (almost like a linked list) to always have small
  // integer values that represent the current byte chains in the encoder.
  // This means instead of tracking the input bytes (AAAABCD) to know our
  // current state, we can track the table entry for AAAABC (it is guaranteed
  // to exist by the nature of the algorithm) and the next character D.
  // Therefor the tuple of (table_entry, byte) is guaranteed to also be
  // unique.  This allows us to create a simple lookup key for mapping input
  // sequences to codes (table indices) without having to store or search
  // any of the code sequences.  So if 'AAAA' has a table entry of 12, the
  // tuple of ('AAAA', K) for any input byte K will be unique, and can be our
  // key.  This leads to a integer value at most 20-bits, which can always
  // fit in an SMI value and be used as a fast sparse array / object key.

  // Output code for the current contents of the index buffer.
  var ib_code = index_stream[0] & code_mask;  // Load first input index.
  var code_table = { };  // Key'd on our 20-bit "tuple".

  emit_code(clear_code);  // Spec says first code should be a clear code.

  // First index already loaded, process the rest of the stream.
  for (var i = 1, il = index_stream.length; i < il; ++i) {
    var k = index_stream[i] & code_mask;
    var cur_key = ib_code << 8 | k;  // (prev, k) unique tuple.
    var cur_code = code_table[cur_key];  // buffer + k.

    // Check if we have to create a new code table entry.
    if (cur_code === undefined) {  // We don't have buffer + k.
      // Emit index buffer (without k).
      // This is an inline version of emit_code, because this is the core
      // writing routine of the compressor (and V8 cannot inline emit_code
      // because it is a closure here in a different context).  Additionally
      // we can call emit_byte_to_buffer less often, because we can have
      // 30-bits (from our 31-bit signed SMI), and we know our codes will only
      // be 12-bits, so can safely have 18-bits there without overflow.
      // emit_code(ib_code);
      cur |= ib_code << cur_shift;
      cur_shift += cur_code_size;
      while (cur_shift >= 8) {
        buf[p++] = cur & 0xff;
        cur >>= 8; cur_shift -= 8;
        if (p === cur_subblock + 256) {  // Finished a subblock.
          buf[cur_subblock] = 255;
          cur_subblock = p++;
        }
      }

      if (next_code === 4096) {  // Table full, need a clear.
        emit_code(clear_code);
        next_code = eoi_code + 1;
        cur_code_size = min_code_size + 1;
        code_table = { };
      } else {  // Table not full, insert a new entry.
        // Increase our variable bit code sizes if necessary.  This is a bit
        // tricky as it is based on "timing" between the encoding and
        // decoder.  From the encoders perspective this should happen after
        // we've already emitted the index buffer and are about to create the
        // first table entry that would overflow our current code bit size.
        if (next_code >= (1 << cur_code_size)) ++cur_code_size;
        code_table[cur_key] = next_code++;  // Insert into code table.
      }

      ib_code = k;  // Index buffer to single input k.
    } else {
      ib_code = cur_code;  // Index buffer to sequence in code table.
    }
  }

  emit_code(ib_code);  // There will still be something in the index buffer.
  emit_code(eoi_code);  // End Of Information.

  // Flush / finalize the sub-blocks stream to the buffer.
  emit_bytes_to_buffer(1);

  // Finish the sub-blocks, writing out any unfinished lengths and
  // terminating with a sub-block of length 0.  If we have already started
  // but not yet used a sub-block it can just become the terminator.
  if (cur_subblock + 1 === p) {  // Started but unused.
    buf[cur_subblock] = 0;
  } else {  // Started and used, write length and additional terminator block.
    buf[cur_subblock] = p - cur_subblock - 1;
    buf[p++] = 0;
  }
  return p;
}

function GifReader(buf) {
  var p = 0;

  // - Header (GIF87a or GIF89a).
  if (buf[p++] !== 0x47 ||            buf[p++] !== 0x49 || buf[p++] !== 0x46 ||
      buf[p++] !== 0x38 || (buf[p++]+1 & 0xfd) !== 0x38 || buf[p++] !== 0x61) {
    throw "Invalid GIF 87a/89a header.";
  }

  // - Logical Screen Descriptor.
  var width = buf[p++] | buf[p++] << 8;
  var height = buf[p++] | buf[p++] << 8;
  var pf0 = buf[p++];  // <Packed Fields>.
  var global_palette_flag = pf0 >> 7;
  var num_global_colors_pow2 = pf0 & 0x7;
  var num_global_colors = 1 << (num_global_colors_pow2 + 1);
  var background = buf[p++];
  buf[p++];  // Pixel aspect ratio (unused?).

  var global_palette_offset = null;

  if (global_palette_flag) {
    global_palette_offset = p;
    p += num_global_colors * 3;  // Seek past palette.
  }

  var no_eof = true;

  var frames = [ ];

  var delay = 0;
  var transparent_index = null;
  var disposal = 0;  // 0 - No disposal specified.
  var loop_count = null;

  this.width = width;
  this.height = height;

  while (no_eof && p < buf.length) {
    switch (buf[p++]) {
      case 0x21:  // Graphics Control Extension Block
        switch (buf[p++]) {
          case 0xff:  // Application specific block
            // Try if it's a Netscape block (with animation loop counter).
            if (buf[p   ] !== 0x0b ||  // 21 FF already read, check block size.
                // NETSCAPE2.0
                buf[p+1 ] == 0x4e && buf[p+2 ] == 0x45 && buf[p+3 ] == 0x54 &&
                buf[p+4 ] == 0x53 && buf[p+5 ] == 0x43 && buf[p+6 ] == 0x41 &&
                buf[p+7 ] == 0x50 && buf[p+8 ] == 0x45 && buf[p+9 ] == 0x32 &&
                buf[p+10] == 0x2e && buf[p+11] == 0x30 &&
                // Sub-block
                buf[p+12] == 0x03 && buf[p+13] == 0x01 && buf[p+16] == 0) {
              p += 14;
              loop_count = buf[p++] | buf[p++] << 8;
              p++;  // Skip terminator.
            } else {  // We don't know what it is, just try to get past it.
              p += 12;
              while (true) {  // Seek through subblocks.
                var block_size = buf[p++];
                if (block_size === 0) break;
                p += block_size;
              }
            }
            break;

          case 0xf9:  // Graphics Control Extension
            if (buf[p++] !== 0x4 || buf[p+4] !== 0)
              throw "Invalid graphics extension block.";
            var pf1 = buf[p++];
            delay = buf[p++] | buf[p++] << 8;
            transparent_index = buf[p++];
            if ((pf1 & 1) === 0) transparent_index = null;
            disposal = pf1 >> 2 & 0x7;
            p++;  // Skip terminator.
            break;

          case 0xfe:  // Comment Extension.
            while (true) {  // Seek through subblocks.
              var block_size = buf[p++];
              if (block_size === 0) break;
              // console.log(buf.slice(p, p+block_size).toString('ascii'));
              p += block_size;
            }
            break;

          default:
            throw "Unknown graphic control label: 0x" + buf[p-1].toString(16);
        }
        break;

      case 0x2c:  // Image Descriptor.
        var x = buf[p++] | buf[p++] << 8;
        var y = buf[p++] | buf[p++] << 8;
        var w = buf[p++] | buf[p++] << 8;
        var h = buf[p++] | buf[p++] << 8;
        var pf2 = buf[p++];
        var local_palette_flag = pf2 >> 7;
        var interlace_flag = pf2 >> 6 & 1;
        var num_local_colors_pow2 = pf2 & 0x7;
        var num_local_colors = 1 << (num_local_colors_pow2 + 1);
        var palette_offset = global_palette_offset;
        var has_local_palette = false;
        if (local_palette_flag) {
          var has_local_palette = true;
          palette_offset = p;  // Override with local palette.
          p += num_local_colors * 3;  // Seek past palette.
        }

        var data_offset = p;

        p++;  // codesize
        while (true) {
          var block_size = buf[p++];
          if (block_size === 0) break;
          p += block_size;
        }

        frames.push({x: x, y: y, width: w, height: h,
                     has_local_palette: has_local_palette,
                     palette_offset: palette_offset,
                     data_offset: data_offset,
                     data_length: p - data_offset,
                     transparent_index: transparent_index,
                     interlaced: !!interlace_flag,
                     delay: delay,
                     disposal: disposal});
        break;

      case 0x3b:  // Trailer Marker (end of file).
        no_eof = false;
        break;

      default:
        throw "Unknown gif block: 0x" + buf[p-1].toString(16);
        break;
    }
  }

  this.numFrames = function() {
    return frames.length;
  };

  this.loopCount = function() {
    return loop_count;
  };

  this.frameInfo = function(frame_num) {
    if (frame_num < 0 || frame_num >= frames.length)
      throw "Frame index out of range.";
    return frames[frame_num];
  }

  this.decodeAndBlitFrameBGRA = function(frame_num, pixels) {
    var frame = this.frameInfo(frame_num);
    var num_pixels = frame.width * frame.height;
    var index_stream = new Uint8Array(num_pixels);  // At most 8-bit indices.
    GifReaderLZWOutputIndexStream(
        buf, frame.data_offset, index_stream, num_pixels);
    var palette_offset = frame.palette_offset;

    // NOTE(deanm): It seems to be much faster to compare index to 256 than
    // to === null.  Not sure why, but CompareStub_EQ_STRICT shows up high in
    // the profile, not sure if it's related to using a Uint8Array.
    var trans = frame.transparent_index;
    if (trans === null) trans = 256;

    // We are possibly just blitting to a portion of the entire frame.
    // That is a subrect within the framerect, so the additional pixels
    // must be skipped over after we finished a scanline.
    var framewidth  = frame.width;
    var framestride = width - framewidth;
    var xleft       = framewidth;  // Number of subrect pixels left in scanline.

    // Output indicies of the top left and bottom right corners of the subrect.
    var opbeg = ((frame.y * width) + frame.x) * 4;
    var opend = ((frame.y + frame.height) * width + frame.x) * 4;
    var op    = opbeg;

    var scanstride = framestride * 4;

    // Use scanstride to skip past the rows when interlacing.  This is skipping
    // 7 rows for the first two passes, then 3 then 1.
    if (frame.interlaced === true) {
      scanstride += width * 4 * 7;  // Pass 1.
    }

    var interlaceskip = 8;  // Tracking the row interval in the current pass.

    for (var i = 0, il = index_stream.length; i < il; ++i) {
      var index = index_stream[i];

      if (xleft === 0) {  // Beginning of new scan line
        op += scanstride;
        xleft = framewidth;
        if (op >= opend) { // Catch the wrap to switch passes when interlacing.
          scanstride = framestride * 4 + width * 4 * (interlaceskip-1);
          // interlaceskip / 2 * 4 is interlaceskip << 1.
          op = opbeg + (framewidth + framestride) * (interlaceskip << 1);
          interlaceskip >>= 1;
        }
      }

      if (index === trans) {
        op += 4;
      } else {
        var r = buf[palette_offset + index * 3];
        var g = buf[palette_offset + index * 3 + 1];
        var b = buf[palette_offset + index * 3 + 2];
        pixels[op++] = b;
        pixels[op++] = g;
        pixels[op++] = r;
        pixels[op++] = 255;
      }
      --xleft;
    }
  };

  // I will go to copy and paste hell one day...
  this.decodeAndBlitFrameRGBA = function(frame_num, pixels) {
    var frame = this.frameInfo(frame_num);
    var num_pixels = frame.width * frame.height;
    var index_stream = new Uint8Array(num_pixels);  // At most 8-bit indices.
    GifReaderLZWOutputIndexStream(
        buf, frame.data_offset, index_stream, num_pixels);
    var palette_offset = frame.palette_offset;

    // NOTE(deanm): It seems to be much faster to compare index to 256 than
    // to === null.  Not sure why, but CompareStub_EQ_STRICT shows up high in
    // the profile, not sure if it's related to using a Uint8Array.
    var trans = frame.transparent_index;
    if (trans === null) trans = 256;

    // We are possibly just blitting to a portion of the entire frame.
    // That is a subrect within the framerect, so the additional pixels
    // must be skipped over after we finished a scanline.
    var framewidth  = frame.width;
    var framestride = width - framewidth;
    var xleft       = framewidth;  // Number of subrect pixels left in scanline.

    // Output indicies of the top left and bottom right corners of the subrect.
    var opbeg = ((frame.y * width) + frame.x) * 4;
    var opend = ((frame.y + frame.height) * width + frame.x) * 4;
    var op    = opbeg;

    var scanstride = framestride * 4;

    // Use scanstride to skip past the rows when interlacing.  This is skipping
    // 7 rows for the first two passes, then 3 then 1.
    if (frame.interlaced === true) {
      scanstride += width * 4 * 7;  // Pass 1.
    }

    var interlaceskip = 8;  // Tracking the row interval in the current pass.

    for (var i = 0, il = index_stream.length; i < il; ++i) {
      var index = index_stream[i];

      if (xleft === 0) {  // Beginning of new scan line
        op += scanstride;
        xleft = framewidth;
        if (op >= opend) { // Catch the wrap to switch passes when interlacing.
          scanstride = framestride * 4 + width * 4 * (interlaceskip-1);
          // interlaceskip / 2 * 4 is interlaceskip << 1.
          op = opbeg + (framewidth + framestride) * (interlaceskip << 1);
          interlaceskip >>= 1;
        }
      }

      if (index === trans) {
        op += 4;
      } else {
        var r = buf[palette_offset + index * 3];
        var g = buf[palette_offset + index * 3 + 1];
        var b = buf[palette_offset + index * 3 + 2];
        pixels[op++] = r;
        pixels[op++] = g;
        pixels[op++] = b;
        pixels[op++] = 255;
      }
      --xleft;
    }
  };
}

function GifReaderLZWOutputIndexStream(code_stream, p, output, output_length) {
  var min_code_size = code_stream[p++];

  var clear_code = 1 << min_code_size;
  var eoi_code = clear_code + 1;
  var next_code = eoi_code + 1;

  var cur_code_size = min_code_size + 1;  // Number of bits per code.
  // NOTE: This shares the same name as the encoder, but has a different
  // meaning here.  Here this masks each code coming from the code stream.
  var code_mask = (1 << cur_code_size) - 1;
  var cur_shift = 0;
  var cur = 0;

  var op = 0;  // Output pointer.
  
  var subblock_size = code_stream[p++];

  // TODO(deanm): Would using a TypedArray be any faster?  At least it would
  // solve the fast mode / backing store uncertainty.
  // var code_table = Array(4096);
  var code_table = new Int32Array(4096);  // Can be signed, we only use 20 bits.

  var prev_code = null;  // Track code-1.

  while (true) {
    // Read up to two bytes, making sure we always 12-bits for max sized code.
    while (cur_shift < 16) {
      if (subblock_size === 0) break;  // No more data to be read.

      cur |= code_stream[p++] << cur_shift;
      cur_shift += 8;

      if (subblock_size === 1) {  // Never let it get to 0 to hold logic above.
        subblock_size = code_stream[p++];  // Next subblock.
      } else {
        --subblock_size;
      }
    }

    // TODO(deanm): We should never really get here, we should have received
    // and EOI.
    if (cur_shift < cur_code_size)
      break;

    var code = cur & code_mask;
    cur >>= cur_code_size;
    cur_shift -= cur_code_size;

    // TODO(deanm): Maybe should check that the first code was a clear code,
    // at least this is what you're supposed to do.  But actually our encoder
    // now doesn't emit a clear code first anyway.
    if (code === clear_code) {
      // We don't actually have to clear the table.  This could be a good idea
      // for greater error checking, but we don't really do any anyway.  We
      // will just track it with next_code and overwrite old entries.

      next_code = eoi_code + 1;
      cur_code_size = min_code_size + 1;
      code_mask = (1 << cur_code_size) - 1;

      // Don't update prev_code ?
      prev_code = null;
      continue;
    } else if (code === eoi_code) {
      break;
    }

    // We have a similar situation as the decoder, where we want to store
    // variable length entries (code table entries), but we want to do in a
    // faster manner than an array of arrays.  The code below stores sort of a
    // linked list within the code table, and then "chases" through it to
    // construct the dictionary entries.  When a new entry is created, just the
    // last byte is stored, and the rest (prefix) of the entry is only
    // referenced by its table entry.  Then the code chases through the
    // prefixes until it reaches a single byte code.  We have to chase twice,
    // first to compute the length, and then to actually copy the data to the
    // output (backwards, since we know the length).  The alternative would be
    // storing something in an intermediate stack, but that doesn't make any
    // more sense.  I implemented an approach where it also stored the length
    // in the code table, although it's a bit tricky because you run out of
    // bits (12 + 12 + 8), but I didn't measure much improvements (the table
    // entries are generally not the long).  Even when I created benchmarks for
    // very long table entries the complexity did not seem worth it.
    // The code table stores the prefix entry in 12 bits and then the suffix
    // byte in 8 bits, so each entry is 20 bits.

    var chase_code = code < next_code ? code : prev_code;

    // Chase what we will output, either {CODE} or {CODE-1}.
    var chase_length = 0;
    var chase = chase_code;
    while (chase > clear_code) {
      chase = code_table[chase] >> 8;
      ++chase_length;
    }

    var k = chase;
    
    var op_end = op + chase_length + (chase_code !== code ? 1 : 0);
    if (op_end > output_length) {
      console.log("Warning, gif stream longer than expected.");
      return;
    }

    // Already have the first byte from the chase, might as well write it fast.
    output[op++] = k;

    op += chase_length;
    var b = op;  // Track pointer, writing backwards.

    if (chase_code !== code)  // The case of emitting {CODE-1} + k.
      output[op++] = k;

    chase = chase_code;
    while (chase_length--) {
      chase = code_table[chase];
      output[--b] = chase & 0xff;  // Write backwards.
      chase >>= 8;  // Pull down to the prefix code.
    }

    if (prev_code !== null && next_code < 4096) {
      code_table[next_code++] = prev_code << 8 | k;
      // TODO(deanm): Figure out this clearing vs code growth logic better.  I
      // have an feeling that it should just happen somewhere else, for now it
      // is awkward between when we grow past the max and then hit a clear code.
      // For now just check if we hit the max 12-bits (then a clear code should
      // follow, also of course encoded in 12-bits).
      if (next_code >= code_mask+1 && cur_code_size < 12) {
        ++cur_code_size;
        code_mask = code_mask << 1 | 1;
      }
    }

    prev_code = code;
  }

  if (op !== output_length) {
    console.log("Warning, gif stream shorter than expected.");
  }

  return output;
}

try { exports.GifWriter = GifWriter; exports.GifReader = GifReader } catch(e) { }  // CommonJS.

},{}],2:[function(require,module,exports){
// A library/utility for generating GIF files
// Uses Dean McNamee's omggif library
// and Anthony Dekker's NeuQuant quantizer (JS 0.3 version with many fixes)
//
// @author sole / http://soledadpenades.com
function Animated_GIF(options) {
    'use strict';

    options = options || {};

    var GifWriter = require('omggif').GifWriter;

    var width = options.width || 160;
    var height = options.height || 120;
    var dithering = options.dithering || null;
    var palette = options.palette || null;
    var canvas = null, ctx = null, repeat = 0, delay = 250;
    var frames = [];
    var numRenderedFrames = 0;
    var onRenderCompleteCallback = function() {};
    var onRenderProgressCallback = function() {};
    var sampleInterval;
    var workers = [], availableWorkers = [], numWorkers;
    var generatingGIF = false;

    // We'll try to be a little lenient with the palette so as to make the library easy to use
    // The only thing we can't cope with is having a non-array so we'll bail on that one.
    if(palette) {

        if(!(palette instanceof Array)) {

            throw('Palette MUST be an array but it is: ', palette);

        } else {

            // Now there are other two constraints that we will warn about
            // and silently fix them... somehow:

            // a) Must contain between 2 and 256 colours
            if(palette.length < 2 || palette.length > 256) {
                console.error('Palette must hold only between 2 and 256 colours');

                while(palette.length < 2) {
                    palette.push(0x000000);
                }

                if(palette.length > 256) {
                    palette = palette.slice(0, 256);
                }
            }

            // b) Must be power of 2
            if(!powerOfTwo(palette.length)) {
                console.error('Palette must have a power of two number of colours');

                while(!powerOfTwo(palette.length)) {
                    palette.splice(palette.length - 1, 1);
                }
            }

        }

    }

    options = options || {};
    sampleInterval = options.sampleInterval || 10;
    numWorkers = options.numWorkers || 2;

    for(var i = 0; i < numWorkers; i++) {
        var w = new Worker(window.URL.createObjectURL(new Blob(['(function e(t,n,r){function s(o,u){if(!n[o]){if(!t[o]){var a=typeof require=="function"&&require;if(!u&&a)return a(o,!0);if(i)return i(o,!0);throw new Error("Cannot find module \'"+o+"\'")}var f=n[o]={exports:{}};t[o][0].call(f.exports,function(e){var n=t[o][1][e];return s(n?n:e)},f,f.exports,e,t,n,r)}return n[o].exports}var i=typeof require=="function"&&require;for(var o=0;o<r.length;o++)s(r[o]);return s})({1:[function(require,module,exports){\n\nfunction colorClamp(value) {\n\tif(value < 0) return 0;\n\telse if(value > 255) return 255;\n\n\treturn value;\n}\n\nvar bayerMatrix8x8 = [\n\t[  1, 49, 13, 61,  4, 52, 16, 64 ],\n\t[ 33, 17, 45, 29, 36, 20, 48, 32 ],\n\t[  9, 57,  5, 53, 12, 60,  8, 56 ],\n\t[ 41, 25, 37, 21, 44, 28, 40, 24 ],\n\t[  3, 51, 15, 63,  2, 50, 14, 62 ],\n\t[ 35, 19, 47, 31, 34, 18, 46, 30 ],\n\t[ 11, 59,  7, 55, 10, 58,  6, 54 ],\n\t[ 43, 27, 39, 23, 42, 26, 38, 22 ]\n\t];\n\n\t// int r, int g, int b, int[][] palette, int paletteLength\n\tfunction getClosestPaletteColorIndex(r, g, b, palette, paletteLength) {\n\t\tvar minDistance = 195076;\n\t\tvar diffR, diffG, diffB;\n\t\tvar distanceSquared;\n\t\tvar bestIndex = 0;\n\t\tvar paletteChannels;\n\n\t\tfor(var i = 0; i < paletteLength; i++) {\n\n\t\t\tpaletteChannels = palette[i];\n\t\t\tdiffR = r - paletteChannels[0];\n\t\t\tdiffG = g - paletteChannels[1];\n\t\t\tdiffB = b - paletteChannels[2];\n\n\t\t\tdistanceSquared = diffR*diffR + diffG*diffG + diffB*diffB;\n\n\t\t\tif(distanceSquared < minDistance) {\n\t\t\t\tbestIndex = i;\n\t\t\t\tminDistance = distanceSquared;\n\t\t\t}\n\n\t\t}\n\n\t\treturn bestIndex;\n\t}\n\n// TODO: inPixels -> inComponents or inColors or something more accurate\nfunction BayerDithering(inPixels, width, height, palette) {\n\tvar offset = 0;\n\tvar indexedOffset = 0;\n\tvar r, g, b;\n\tvar pixel, threshold, index;\n\tvar paletteLength = palette.length;\n\tvar matrix = bayerMatrix8x8;\n\tvar indexedPixels = new Uint8Array( width * height );\n\n\tvar modI = 8;\n\tvar modJ = 8;\n\n\tfor(var j = 0; j < height; j++) {\n\t\tvar modj = j % modJ;\n\n\t\tfor(var i = 0; i < width; i++) {\n\n\t\t\tthreshold = matrix[i % modI][modj];\n\n\t\t\tr = colorClamp( inPixels[offset++] + threshold );\n\t\t\tg = colorClamp( inPixels[offset++] + threshold );\n\t\t\tb = colorClamp( inPixels[offset++] + threshold );\n\n\t\t\tindex = getClosestPaletteColorIndex(r, g, b, palette, paletteLength);\n\t\t\tindexedPixels[indexedOffset++] = index;\n\n\t\t}\n\t}\n\n\treturn indexedPixels;\n}\n\n\nfunction ClosestDithering(inPixels, width, height, palette) {\n\n\tvar offset = 0;\n\tvar indexedOffset = 0;\n\tvar r, g, b;\n\tvar index;\n\tvar paletteLength = palette.length;\n\tvar matrix = bayerMatrix8x8;\n\tvar numPixels = width * height;\n\tvar indexedPixels = new Uint8Array( numPixels );\n\n\tfor(var i = 0; i < numPixels; i++) {\n\n\t\tr = inPixels[offset++];\n\t\tg = inPixels[offset++];\n\t\tb = inPixels[offset++];\n\n\t\tindexedPixels[i] = getClosestPaletteColorIndex(r, g, b, palette, paletteLength);\n\n\t}\n\n\treturn indexedPixels;\n\n}\n\n\nfunction FloydSteinberg(inPixels, width, height, palette) {\n\tvar paletteLength = palette.length;\n\tvar offset = 0;\n\tvar indexedOffset = 0;\n\tvar r, g, b;\n\tvar widthLimit = width - 1;\n\tvar heightLimit = height - 1;\n\tvar offsetNextI, offsetNextJ;\n\tvar offsetPrevINextJ;\n\tvar channels, nextChannels;\n\tvar indexedPixels = new Uint8Array( width * height );\n\n\tfor(var j = 0; j < height; j++) {\n\t\tfor(var i = 0; i < width; i++) {\n\n\t\t\tr = colorClamp(inPixels[offset++]);\n\t\t\tg = colorClamp(inPixels[offset++]);\n\t\t\tb = colorClamp(inPixels[offset++]);\n\n\t\t\tvar colorIndex = getClosestPaletteColorIndex(r, g, b, palette, paletteLength);\n\t\t\tvar paletteColor = palette[colorIndex];\n\t\t\tvar closestColor = paletteColor[3];\n\n\t\t\t// We are done with finding the best value for this pixel\n\t\t\tindexedPixels[indexedOffset] = colorIndex;\n\n\t\t\t// Now find difference between assigned value and original color\n\t\t\t// and propagate that error forward\n\t\t\tvar errorR = r - paletteColor[0];\n\t\t\tvar errorG = g - paletteColor[1];\n\t\t\tvar errorB = b - paletteColor[2];\n\n\t\t\tif(i < widthLimit) {\n\n\t\t\t\toffsetNextI = offset + 1;\n\n\t\t\t\tinPixels[offsetNextI++] += (errorR * 7) >> 4;\n\t\t\t\tinPixels[offsetNextI++] += (errorG * 7) >> 4;\n\t\t\t\tinPixels[offsetNextI++] += (errorB * 7) >> 4;\n\n\t\t\t}\n\n\n\t\t\tif(j < heightLimit) {\n\n\t\t\t\tif(i > 0) {\n\n\t\t\t\t\toffsetPrevINextJ = offset - 1 + width;\n\n\t\t\t\t\tinPixels[offsetPrevINextJ++] += (errorR * 3) >> 4;\n\t\t\t\t\tinPixels[offsetPrevINextJ++] += (errorG * 3) >> 4;\n\t\t\t\t\tinPixels[offsetPrevINextJ++] += (errorB * 3) >> 4;\n\n\t\t\t\t}\n\n\t\t\t\toffsetNextJ = offset + width;\n\n\t\t\t\tinPixels[offsetNextJ++] += (errorR * 5) >> 4;\n\t\t\t\tinPixels[offsetNextJ++] += (errorG * 5) >> 4;\n\t\t\t\tinPixels[offsetNextJ++] += (errorB * 5) >> 4;\n\n\n\t\t\t\tif(i < widthLimit) {\n\n\t\t\t\t\tinPixels[offsetNextJ++] += errorR >> 4;\n\t\t\t\t\tinPixels[offsetNextJ++] += errorG >> 4;\n\t\t\t\t\tinPixels[offsetNextJ++] += errorB >> 4;\n\n\t\t\t\t}\n\n\t\t\t}\n\n\t\t\tindexedOffset++;\n\t\t}\n\t}\n\n\treturn indexedPixels;\n}\n\nmodule.exports = {\n\tBayer: BayerDithering,\n\tClosest: ClosestDithering,\n\tFloydSteinberg: FloydSteinberg\n};\n\n\n},{}],2:[function(require,module,exports){\nvar NeuQuant = require(\'./lib/NeuQuant\');\nvar Dithering = require(\'node-dithering\');\n\nfunction channelizePalette( palette ) {\n    var channelizedPalette = [];\n\n    for(var i = 0; i < palette.length; i++) {\n        var color = palette[i];\n\n        var r = (color & 0xFF0000) >> 16;\n        var g = (color & 0x00FF00) >>  8;\n        var b = (color & 0x0000FF);\n\n        channelizedPalette.push([ r, g, b, color ]);\n    }\n\n    return channelizedPalette;\n\n}\n\n\nfunction dataToRGB( data, width, height ) {\n    var i = 0;\n    var length = width * height * 4;\n    var rgb = [];\n\n    while(i < length) {\n        rgb.push( data[i++] );\n        rgb.push( data[i++] );\n        rgb.push( data[i++] );\n        i++; // for the alpha channel which we don\'t care about\n    }\n\n    return rgb;\n}\n\n\nfunction componentizedPaletteToArray(paletteRGB) {\n\n    var paletteArray = [];\n\n    for(var i = 0; i < paletteRGB.length; i += 3) {\n        var r = paletteRGB[ i ];\n        var g = paletteRGB[ i + 1 ];\n        var b = paletteRGB[ i + 2 ];\n        paletteArray.push(r << 16 | g << 8 | b);\n    }\n\n    return paletteArray;\n}\n\n\n// This is the "traditional" Animated_GIF style of going from RGBA to indexed color frames\nfunction processFrameWithQuantizer(imageData, width, height, sampleInterval) {\n\n    var rgbComponents = dataToRGB( imageData, width, height );\n    var nq = new NeuQuant(rgbComponents, rgbComponents.length, sampleInterval);\n    var paletteRGB = nq.process();\n    var paletteArray = new Uint32Array(componentizedPaletteToArray(paletteRGB));\n\n    var numberPixels = width * height;\n    var indexedPixels = new Uint8Array(numberPixels);\n\n    var k = 0;\n    for(var i = 0; i < numberPixels; i++) {\n        r = rgbComponents[k++];\n        g = rgbComponents[k++];\n        b = rgbComponents[k++];\n        indexedPixels[i] = nq.map(r, g, b);\n    }\n\n    return {\n        pixels: indexedPixels,\n        palette: paletteArray\n    };\n\n}\n\n\n// And this is a version that uses dithering against of quantizing\n// It can also use a custom palette if provided, or will build one otherwise\nfunction processFrameWithDithering(imageData, width, height, ditheringType, palette) {\n\n    // Extract component values from data\n    var rgbComponents = dataToRGB( imageData, width, height );\n\n\n    // Build palette if none provided\n    if(palette === null) {\n\n        var nq = new NeuQuant(rgbComponents, rgbComponents.length, 16);\n        var paletteRGB = nq.process();\n        palette = componentizedPaletteToArray(paletteRGB);\n\n    }\n\n    var paletteArray = new Uint32Array( palette );\n    var paletteChannels = channelizePalette( palette );\n\n    // Convert RGB image to indexed image\n    var ditheringFunction;\n\n    if(ditheringType === \'closest\') {\n        ditheringFunction = Dithering.Closest;\n    } else if(ditheringType === \'floyd\') {\n        ditheringFunction = Dithering.FloydSteinberg;\n    } else {\n        ditheringFunction = Dithering.Bayer;\n    }\n\n    pixels = ditheringFunction(rgbComponents, width, height, paletteChannels);\n\n    return ({\n        pixels: pixels,\n        palette: paletteArray\n    });\n\n}\n\n\n// ~~~\n\nfunction run(frame) {\n    var width = frame.width;\n    var height = frame.height;\n    var imageData = frame.data;\n    var dithering = frame.dithering;\n    var palette = frame.palette;\n    var sampleInterval = frame.sampleInterval;\n\n    if(dithering) {\n        return processFrameWithDithering(imageData, width, height, dithering, palette);\n    } else {\n        return processFrameWithQuantizer(imageData, width, height, sampleInterval);\n    }\n\n}\n\n\nself.onmessage = function(ev) {\n    var data = ev.data;\n    var response = run(data);\n    postMessage(response);\n};\n\n},{"./lib/NeuQuant":3,"node-dithering":1}],3:[function(require,module,exports){\n/*\n* NeuQuant Neural-Net Quantization Algorithm\n* ------------------------------------------\n*\n* Copyright (c) 1994 Anthony Dekker\n*\n* NEUQUANT Neural-Net quantization algorithm by Anthony Dekker, 1994. See\n* "Kohonen neural networks for optimal colour quantization" in "Network:\n* Computation in Neural Systems" Vol. 5 (1994) pp 351-367. for a discussion of\n* the algorithm.\n*\n* Any party obtaining a copy of these files from the author, directly or\n* indirectly, is granted, free of charge, a full and unrestricted irrevocable,\n* world-wide, paid up, royalty-free, nonexclusive right and license to deal in\n* this software and documentation files (the "Software"), including without\n* limitation the rights to use, copy, modify, merge, publish, distribute,\n* sublicense, and/or sell copies of the Software, and to permit persons who\n* receive copies from any such party to do so, with the only requirement being\n* that this copyright notice remain intact.\n*/\n\n/*\n* This class handles Neural-Net quantization algorithm\n* @author Kevin Weiner (original Java version - kweiner@fmsware.com)\n* @author Thibault Imbert (AS3 version - bytearray.org)\n* @version 0.1 AS3 implementation\n* @version 0.2 JS->AS3 "translation" by antimatter15\n* @version 0.3 JS clean up + using modern JS idioms by sole - http://soledadpenades.com\n* Also implement fix in color conversion described at http://stackoverflow.com/questions/16371712/neuquant-js-javascript-color-quantization-hidden-bug-in-js-conversion\n*/\n\nmodule.exports = function NeuQuant() {\n\n    var netsize = 256; // number of colours used\n\n    // four primes near 500 - assume no image has a length so large\n    // that it is divisible by all four primes\n    var prime1 = 499;\n    var prime2 = 491;\n    var prime3 = 487;\n    var prime4 = 503;\n\n    // minimum size for input image\n    var minpicturebytes = (3 * prime4);\n\n    // Network Definitions\n\n    var maxnetpos = (netsize - 1);\n    var netbiasshift = 4; // bias for colour values\n    var ncycles = 100; // no. of learning cycles\n\n    // defs for freq and bias\n    var intbiasshift = 16; // bias for fractions\n    var intbias = (1 << intbiasshift);\n    var gammashift = 10; // gamma = 1024\n    var gamma = (1 << gammashift);\n    var betashift = 10;\n    var beta = (intbias >> betashift); // beta = 1/1024\n    var betagamma = (intbias << (gammashift - betashift));\n\n    // defs for decreasing radius factor\n    // For 256 colors, radius starts at 32.0 biased by 6 bits\n    // and decreases by a factor of 1/30 each cycle\n    var initrad = (netsize >> 3);\n    var radiusbiasshift = 6;\n    var radiusbias = (1 << radiusbiasshift);\n    var initradius = (initrad * radiusbias);\n    var radiusdec = 30;\n\n    // defs for decreasing alpha factor\n    // Alpha starts at 1.0 biased by 10 bits\n    var alphabiasshift = 10;\n    var initalpha = (1 << alphabiasshift);\n    var alphadec;\n\n    // radbias and alpharadbias used for radpower calculation\n    var radbiasshift = 8;\n    var radbias = (1 << radbiasshift);\n    var alpharadbshift = (alphabiasshift + radbiasshift);\n    var alpharadbias = (1 << alpharadbshift);\n\n\n    // Input image\n    var thepicture;\n    // Height * Width * 3\n    var lengthcount;\n    // Sampling factor 1..30\n    var samplefac;\n\n    // The network itself\n    var network;\n    var netindex = [];\n\n    // for network lookup - really 256\n    var bias = [];\n\n    // bias and freq arrays for learning\n    var freq = [];\n    var radpower = [];\n\n    function NeuQuantConstructor(thepic, len, sample) {\n\n        var i;\n        var p;\n\n        thepicture = thepic;\n        lengthcount = len;\n        samplefac = sample;\n\n        network = new Array(netsize);\n\n        for (i = 0; i < netsize; i++) {\n            network[i] = new Array(4);\n            p = network[i];\n            p[0] = p[1] = p[2] = ((i << (netbiasshift + 8)) / netsize) | 0;\n            freq[i] = (intbias / netsize) | 0; // 1 / netsize\n            bias[i] = 0;\n        }\n\n    }\n\n    function colorMap() {\n        var map = [];\n        var index = new Array(netsize);\n        for (var i = 0; i < netsize; i++)\n            index[network[i][3]] = i;\n        var k = 0;\n        for (var l = 0; l < netsize; l++) {\n            var j = index[l];\n            map[k++] = (network[j][0]);\n            map[k++] = (network[j][1]);\n            map[k++] = (network[j][2]);\n        }\n        return map;\n    }\n\n    // Insertion sort of network and building of netindex[0..255]\n    // (to do after unbias)\n    function inxbuild() {\n        var i;\n        var j;\n        var smallpos;\n        var smallval;\n        var p;\n        var q;\n        var previouscol;\n        var startpos;\n\n        previouscol = 0;\n        startpos = 0;\n\n        for (i = 0; i < netsize; i++)\n        {\n\n            p = network[i];\n            smallpos = i;\n            smallval = p[1]; // index on g\n            // find smallest in i..netsize-1\n            for (j = i + 1; j < netsize; j++) {\n\n                q = network[j];\n\n                if (q[1] < smallval) { // index on g\n                    smallpos = j;\n                    smallval = q[1]; // index on g\n                }\n            }\n\n            q = network[smallpos];\n\n            // swap p (i) and q (smallpos) entries\n            if (i != smallpos) {\n                j = q[0];\n                q[0] = p[0];\n                p[0] = j;\n                j = q[1];\n                q[1] = p[1];\n                p[1] = j;\n                j = q[2];\n                q[2] = p[2];\n                p[2] = j;\n                j = q[3];\n                q[3] = p[3];\n                p[3] = j;\n            }\n\n            // smallval entry is now in position i\n            if (smallval != previouscol) {\n\n                netindex[previouscol] = (startpos + i) >> 1;\n\n                for (j = previouscol + 1; j < smallval; j++) {\n                    netindex[j] = i;\n                }\n\n                previouscol = smallval;\n                startpos = i;\n\n            }\n\n        }\n\n        netindex[previouscol] = (startpos + maxnetpos) >> 1;\n        for (j = previouscol + 1; j < 256; j++) {\n            netindex[j] = maxnetpos; // really 256\n        }\n\n    }\n\n\n    // Main Learning Loop\n\n    function learn() {\n        var i;\n        var j;\n        var b;\n        var g;\n        var r;\n        var radius;\n        var rad;\n        var alpha;\n        var step;\n        var delta;\n        var samplepixels;\n        var p;\n        var pix;\n        var lim;\n\n        if (lengthcount < minpicturebytes) {\n            samplefac = 1;\n        }\n\n        alphadec = 30 + ((samplefac - 1) / 3);\n        p = thepicture;\n        pix = 0;\n        lim = lengthcount;\n        samplepixels = lengthcount / (3 * samplefac);\n        delta = (samplepixels / ncycles) | 0;\n        alpha = initalpha;\n        radius = initradius;\n\n        rad = radius >> radiusbiasshift;\n        if (rad <= 1) {\n            rad = 0;\n        }\n\n        for (i = 0; i < rad; i++) {\n            radpower[i] = alpha * (((rad * rad - i * i) * radbias) / (rad * rad));\n        }\n\n\n        if (lengthcount < minpicturebytes) {\n            step = 3;\n        } else if ((lengthcount % prime1) !== 0) {\n            step = 3 * prime1;\n        } else {\n\n            if ((lengthcount % prime2) !== 0) {\n                step = 3 * prime2;\n            } else {\n                if ((lengthcount % prime3) !== 0) {\n                    step = 3 * prime3;\n                } else {\n                    step = 3 * prime4;\n                }\n            }\n\n        }\n\n        i = 0;\n\n        while (i < samplepixels) {\n\n            b = (p[pix + 0] & 0xff) << netbiasshift;\n            g = (p[pix + 1] & 0xff) << netbiasshift;\n            r = (p[pix + 2] & 0xff) << netbiasshift;\n            j = contest(b, g, r);\n\n            altersingle(alpha, j, b, g, r);\n\n            if (rad !== 0) {\n                // Alter neighbours\n                alterneigh(rad, j, b, g, r);\n            }\n\n            pix += step;\n\n            if (pix >= lim) {\n                pix -= lengthcount;\n            }\n\n            i++;\n\n            if (delta === 0) {\n                delta = 1;\n            }\n\n            if (i % delta === 0) {\n                alpha -= alpha / alphadec;\n                radius -= radius / radiusdec;\n                rad = radius >> radiusbiasshift;\n\n                if (rad <= 1) {\n                    rad = 0;\n                }\n\n                for (j = 0; j < rad; j++) {\n                    radpower[j] = alpha * (((rad * rad - j * j) * radbias) / (rad * rad));\n                }\n\n            }\n\n        }\n\n    }\n\n    // Search for BGR values 0..255 (after net is unbiased) and return colour index\n    function map(b, g, r) {\n        var i;\n        var j;\n        var dist;\n        var a;\n        var bestd;\n        var p;\n        var best;\n\n        // Biggest possible distance is 256 * 3\n        bestd = 1000;\n        best = -1;\n        i = netindex[g]; // index on g\n        j = i - 1; // start at netindex[g] and work outwards\n\n        while ((i < netsize) || (j >= 0)) {\n\n            if (i < netsize) {\n\n                p = network[i];\n\n                dist = p[1] - g; // inx key\n\n                if (dist >= bestd) {\n                    i = netsize; // stop iter\n                } else {\n\n                    i++;\n\n                    if (dist < 0) {\n                        dist = -dist;\n                    }\n\n                    a = p[0] - b;\n\n                    if (a < 0) {\n                        a = -a;\n                    }\n\n                    dist += a;\n\n                    if (dist < bestd) {\n                        a = p[2] - r;\n\n                        if (a < 0) {\n                            a = -a;\n                        }\n\n                        dist += a;\n\n                        if (dist < bestd) {\n                            bestd = dist;\n                            best = p[3];\n                        }\n                    }\n\n                }\n\n            }\n\n            if (j >= 0) {\n\n                p = network[j];\n\n                dist = g - p[1]; // inx key - reverse dif\n\n                if (dist >= bestd) {\n                    j = -1; // stop iter\n                } else {\n\n                    j--;\n                    if (dist < 0) {\n                        dist = -dist;\n                    }\n                    a = p[0] - b;\n                    if (a < 0) {\n                        a = -a;\n                    }\n                    dist += a;\n\n                    if (dist < bestd) {\n                        a = p[2] - r;\n                        if (a < 0) {\n                            a = -a;\n                        }\n                        dist += a;\n                        if (dist < bestd) {\n                            bestd = dist;\n                            best = p[3];\n                        }\n                    }\n\n                }\n\n            }\n\n        }\n\n        return (best);\n\n    }\n\n    function process() {\n        learn();\n        unbiasnet();\n        inxbuild();\n        return colorMap();\n    }\n\n    // Unbias network to give byte values 0..255 and record position i\n    // to prepare for sort\n    function unbiasnet() {\n        var i;\n        var j;\n\n        for (i = 0; i < netsize; i++) {\n            network[i][0] >>= netbiasshift;\n            network[i][1] >>= netbiasshift;\n            network[i][2] >>= netbiasshift;\n            network[i][3] = i; // record colour no\n        }\n    }\n\n    // Move adjacent neurons by precomputed alpha*(1-((i-j)^2/[r]^2))\n    // in radpower[|i-j|]\n    function alterneigh(rad, i, b, g, r) {\n\n        var j;\n        var k;\n        var lo;\n        var hi;\n        var a;\n        var m;\n\n        var p;\n\n        lo = i - rad;\n        if (lo < -1) {\n            lo = -1;\n        }\n\n        hi = i + rad;\n\n        if (hi > netsize) {\n            hi = netsize;\n        }\n\n        j = i + 1;\n        k = i - 1;\n        m = 1;\n\n        while ((j < hi) || (k > lo)) {\n\n            a = radpower[m++];\n\n            if (j < hi) {\n\n                p = network[j++];\n\n                try {\n\n                    p[0] -= ((a * (p[0] - b)) / alpharadbias) | 0;\n                    p[1] -= ((a * (p[1] - g)) / alpharadbias) | 0;\n                    p[2] -= ((a * (p[2] - r)) / alpharadbias) | 0;\n\n                } catch (e) {}\n\n            }\n\n            if (k > lo) {\n\n                p = network[k--];\n\n                try {\n\n                    p[0] -= ((a * (p[0] - b)) / alpharadbias) | 0;\n                    p[1] -= ((a * (p[1] - g)) / alpharadbias) | 0;\n                    p[2] -= ((a * (p[2] - r)) / alpharadbias) | 0;\n\n                } catch (e) {}\n\n            }\n\n        }\n\n    }\n\n\n    // Move neuron i towards biased (b,g,r) by factor alpha\n    function altersingle(alpha, i, b, g, r) {\n\n        // alter hit neuron\n        var n = network[i];\n        var alphaMult = alpha / initalpha;\n        n[0] -= ((alphaMult * (n[0] - b))) | 0;\n        n[1] -= ((alphaMult * (n[1] - g))) | 0;\n        n[2] -= ((alphaMult * (n[2] - r))) | 0;\n\n    }\n\n    // Search for biased BGR values\n    function contest(b, g, r) {\n\n        // finds closest neuron (min dist) and updates freq\n        // finds best neuron (min dist-bias) and returns position\n        // for frequently chosen neurons, freq[i] is high and bias[i] is negative\n        // bias[i] = gamma*((1/netsize)-freq[i])\n\n        var i;\n        var dist;\n        var a;\n        var biasdist;\n        var betafreq;\n        var bestpos;\n        var bestbiaspos;\n        var bestd;\n        var bestbiasd;\n        var n;\n\n        bestd = ~(1 << 31);\n        bestbiasd = bestd;\n        bestpos = -1;\n        bestbiaspos = bestpos;\n\n        for (i = 0; i < netsize; i++) {\n\n            n = network[i];\n            dist = n[0] - b;\n\n            if (dist < 0) {\n                dist = -dist;\n            }\n\n            a = n[1] - g;\n\n            if (a < 0) {\n                a = -a;\n            }\n\n            dist += a;\n\n            a = n[2] - r;\n\n            if (a < 0) {\n                a = -a;\n            }\n\n            dist += a;\n\n            if (dist < bestd) {\n                bestd = dist;\n                bestpos = i;\n            }\n\n            biasdist = dist - ((bias[i]) >> (intbiasshift - netbiasshift));\n\n            if (biasdist < bestbiasd) {\n                bestbiasd = biasdist;\n                bestbiaspos = i;\n            }\n\n            betafreq = (freq[i] >> betashift);\n            freq[i] -= betafreq;\n            bias[i] += (betafreq << gammashift);\n\n        }\n\n        freq[bestpos] += beta;\n        bias[bestpos] -= betagamma;\n        return (bestbiaspos);\n\n    }\n\n    NeuQuantConstructor.apply(this, arguments);\n\n    var exports = {};\n    exports.map = map;\n    exports.process = process;\n\n    return exports;\n}\n\n},{}]},{},[2])'],{type:"text/javascript"})));
        workers.push(w);
        availableWorkers.push(w);
    }

    // ---

    // Return a worker for processing a frame
    function getWorker() {
        if(availableWorkers.length === 0) {
            throw ('No workers left!');
        }

        return availableWorkers.pop();
    }

    // Restore a worker to the pool
    function freeWorker(worker) {
        availableWorkers.push(worker);
    }

    // Faster/closurized bufferToString function
    // (caching the String.fromCharCode values)
    var bufferToString = (function() {
        var byteMap = [];
        for(var i = 0; i < 256; i++) {
            byteMap[i] = String.fromCharCode(i);
        }

        return (function(buffer) {
            var numberValues = buffer.length;
            var str = '';

            for(var i = 0; i < numberValues; i++) {
                str += byteMap[ buffer[i] ];
            }

            return str;
        });
    })();

    function startRendering(completeCallback) {
        var numFrames = frames.length;

        onRenderCompleteCallback = completeCallback;

        for(var i = 0; i < numWorkers && i < frames.length; i++) {
            processFrame(i);
        }
    }

    function processFrame(position) {
        var frame;
        var worker;

        frame = frames[position];

        if(frame.beingProcessed || frame.done) {
            console.error('Frame already being processed or done!', frame.position);
            onFrameFinished();
            return;
        }

        frame.sampleInterval = sampleInterval;
        frame.beingProcessed = true;

        worker = getWorker();

        worker.onmessage = function(ev) {
            var data = ev.data;

            // Delete original data, and free memory
            delete(frame.data);

            // TODO grrr... HACK for object -> Array
            frame.pixels = Array.prototype.slice.call(data.pixels);
            frame.palette = Array.prototype.slice.call(data.palette);
            frame.done = true;
            frame.beingProcessed = false;

            freeWorker(worker);

            onFrameFinished();
        };


        // TODO transfer objects should be more efficient
        /*var frameData = frame.data;
        //worker.postMessage(frameData, [frameData]);
        worker.postMessage(frameData);*/

        worker.postMessage(frame);
    }

    function processNextFrame() {

        var position = -1;

        for(var i = 0; i < frames.length; i++) {
            var frame = frames[i];
            if(!frame.done && !frame.beingProcessed) {
                position = i;
                break;
            }
        }

        if(position >= 0) {
            processFrame(position);
        }
    }


    function onFrameFinished() { // ~~~ taskFinished

        // The GIF is not written until we're done with all the frames
        // because they might not be processed in the same order
        var allDone = frames.every(function(frame) {
            return !frame.beingProcessed && frame.done;
        });

        numRenderedFrames++;
        onRenderProgressCallback(numRenderedFrames * 0.75 / frames.length);

        if(allDone) {
            if(!generatingGIF) {
                generateGIF(frames, onRenderCompleteCallback);
            }
        } else {
            setTimeout(processNextFrame, 1);
        }

    }


    // Takes the already processed data in frames and feeds it to a new
    // GifWriter instance in order to get the binary GIF file
    function generateGIF(frames, callback) {

        // TODO: Weird: using a simple JS array instead of a typed array,
        // the files are WAY smaller o_o. Patches/explanations welcome!
        var buffer = []; // new Uint8Array(width * height * frames.length * 5);
        var globalPalette;
        var gifOptions = { loop: repeat };

        // Using global palette but only if we're also using dithering
        if(dithering !== null && palette !== null) {
            globalPalette = palette;
            gifOptions.palette = globalPalette;
        }

        var gifWriter = new GifWriter(buffer, width, height, gifOptions);

        generatingGIF = true;

        frames.forEach(function(frame, index) {

            var framePalette;

            if(!globalPalette) {
               framePalette = frame.palette;
            }

            onRenderProgressCallback(0.75 + 0.25 * frame.position * 1.0 / frames.length);
            gifWriter.addFrame(0, 0, width, height, frame.pixels, {
                palette: framePalette,
                delay: delay
            });
        });

        gifWriter.end();
        onRenderProgressCallback(1.0);

        frames = [];
        generatingGIF = false;

        callback(buffer);
    }


    function powerOfTwo(value) {
        return (value !== 0) && ((value & (value - 1)) === 0);
    }


    // ---

    this.setSize = function(w, h) {
        width = w;
        height = h;
        canvas = document.createElement('canvas');
        canvas.width = w;
        canvas.height = h;
        ctx = canvas.getContext('2d');
    };

    // Internally, GIF uses tenths of seconds to store the delay
    this.setDelay = function(seconds) {
        delay = seconds * 0.1;
    };

    // From GIF: 0 = loop forever, null = not looping, n > 0 = loop n times and stop
    this.setRepeat = function(r) {
        repeat = r;
    };

    this.addFrame = function(element) {

        if(ctx === null) {
            this.setSize(width, height);
        }

        ctx.drawImage(element, 0, 0, width, height);
        var imageData = ctx.getImageData(0, 0, width, height);

        this.addFrameImageData(imageData);
    };

    this.addFrameImageData = function(imageData) {

        var dataLength = imageData.length,
            imageDataArray = new Uint8Array(imageData.data);

        frames.push({
            data: imageDataArray,
            width: imageData.width,
            height: imageData.height,
            palette: palette,
            dithering: dithering,
            done: false,
            beingProcessed: false,
            position: frames.length
        });
    };

    this.onRenderProgress = function(callback) {
        onRenderProgressCallback = callback;
    };

    this.isRendering = function() {
        return generatingGIF;
    };

    this.getBase64GIF = function(completeCallback) {

        var onRenderComplete = function(buffer) {
            var str = bufferToString(buffer);
            var gif = 'data:image/gif;base64,' + btoa(str);
            completeCallback(gif);
        };

        startRendering(onRenderComplete);

    };


    this.getBlobGIF = function(completeCallback) {

        var onRenderComplete = function(buffer) {
            var array = new Uint8Array(buffer);
            var blob = new Blob([ array ], { type: 'image/gif' });
            completeCallback(blob);
        };

        startRendering(onRenderComplete);

    };


    // Once this function is called, the object becomes unusable
    // and you'll need to create a new one.
    this.destroy = function() {

        // Explicitly ask web workers to die so they are explicitly GC'ed
        workers.forEach(function(w) {
            w.terminate();
        });

    };

}

// Not using the full blown exporter because this is supposed to be built
// into dist/Animated_GIF.js using a build step with browserify
module.exports = Animated_GIF;

},{"omggif":1}]},{},[2])(2)
});