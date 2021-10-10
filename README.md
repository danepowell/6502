# DP6502 computer

These are the project files for my version of [Ben Eater's 6502 computer](https://eater.net/6502).

## Getting started

- Install VSCode and Arduino IDE V2 (I couldn't get the online editor to work reliably with the serial interface)
- Install MADS extension for nice assembly highlighting
- Install VASM using [latest release binary](http://www.compilers.de/vasm.html)
- `vasm6502_oldstyle -Fbin -dotdir -o blink.out blink.asm`

## Future plans

- Change LCD interface from 8 pins to 4
- Automatic power-on reset ([schematics](https://www.grappendorf.net/projects/6502-home-computer/reset-circuit.html))
- Bootloader or something to avoid having to pop and flash the EEPROM for every change ([source](https://github.com/janroesner/sixty5o2))
- What to do with other switches
- [Turn into a PCB](https://www.reddit.com/r/beneater/comments/dgcpt3/i_made_a_pcb_version_of_ben_eaters_6502_computer/)
- Emulator: starting in PHP, or [following example](https://www.reddit.com/r/beneater/comments/phn3sd/started_work_on_an_emulator_for_my_6502_tms9918/)
- Space Invaders ([source code](https://github.com/visrealm/hbc-56/tree/master/code/6502/invaders))

## Resources / credits

- https://eater.net/6502
- https://github.com/dbuchwald/6502
