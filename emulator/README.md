# DP6502 Emulator

This is a PHP-based emulator for my version of [Ben Eater’s 6502](https://eater.net/6502) computer.

I’m modeling the 6502 at the level of individual instructions. I’ve flirted with modeling at a more granular level (i.e. individual clock cycles) but this turns out to be difficult due to idiosyncracies (or clever optimizations, really) of the 6502, such that there’s no clear mapping between instructions and clock cycles. For instance, [this post](http://forum.6502.org/viewtopic.php?p=9466&sid=5f0d5a945d7d41c2f5e49df12fd5da83#p9466) describes the internal logic associated with the LDA instruction: it takes essentially 4.5 clock cycles, and the cycles are tightly coupled not only to each other but also to the instructions running before and after LDA. If I do proceed down this path, the [6500 programming manual](https://archive.org/details/6500-50a_mcs6500pgmmanjan76/page/n119/mode/2up) will help.

## Why write an emulator from scratch?

For the lulz and just to see if I can. I didn’t say it would be any good. I’m not using any existing emulators as reference, [clean-room](https://en.wikipedia.org/wiki/Clean_room_design) design is more challenging and therefore more fun.

## Okay, but why PHP?

- It’s my daily language
- I enjoy a challenge
- It’s the world’s first PHP-based 6502 emulator
- I’m delighted to introduce a new namespace collision into the world, viz. with the [PHP OpCode](https://sites.google.com/site/6502asembly/6502-instruction-set/php)

## Okay, but seriously... why PHP?

I don’t judge your lifestyle choices, don’t judge mine. :smile:
