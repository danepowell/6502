; Address ranges
; RAM: 0x0000 - 0x3fff
; VIA: 0x6000 - 0x7fff
; ROM: 0x8000 - 0xffff

; RAM addresses
value = $0200   ; 2 bytes; working value to display on LCD
mod10 = $0202   ; 2 bytes
message = $0204 ; 6 bytes; message to display on LCD
counter = $020a ; 2 bytes; internal counter incremented on interrupt

; VIA addresses
PORTB = $6000 ; LCD data
PORTA = $6001 ; LCD instructions
DDRB = $6002  ; Data direction register "B"
DDRA = $6003  ; Data direction register "A"
T1CL = $6004  ; Timer 1 low-order counter
T1CH = $6005  ; Timer 1 high-order counter
ACR = $600b   ; Auxiliary control regiser
PCR = $600c   ; Peripheral control register
IFR = $600d   ; Interrupt flag register
IER = $600e   ; Interrupt enable register

; LCD instruction flags
E  = %10000000
RW = %01000000
RS = %00100000

  .org $8000

; Main program
reset:
  ; Decrement from end of stack to more easily track stack capacity
  ldx #$ff
  txs

  ; Clear interrupt disable bit
  cli

  ; Enable CA1 interrupt handler, negative active edge
  lda #%10000010
  sta IER
  lda #$00
  sta PCR

  ; Configure VIA to output to LCD data and instructions
  lda #%11111111 ; Set all pins on port B to output
  sta DDRB
  lda #%11100000 ; Set top 3 pins on port A to output
  sta DDRA

  ; Initialize LCD
  lda #%00111000 ; 8-bit mode; 2-line display; 5x8 font
  jsr lcd_instruction
  lda #%00001110 ; Display on; cursor on; blink off
  jsr lcd_instruction
  lda #%00000110 ; Increment and shift cursor; don't shift display
  jsr lcd_instruction
  lda #%00000001 ; Clear display
  jsr lcd_instruction

  ; Initialize counter
  lda #0
  sta counter
  sta counter + 1

loop:
  ; Send cursor home
  lda #%00000010 ; Home
  jsr lcd_instruction

  ; Initialize message
  lda #0
  sta message

  ; Initialize value to be the number to convert
  ; Disable interrupts so counter isn't updated
  sei
  lda counter
  sta value
  lda counter + 1
  sta value + 1
  cli

divide:
  ; Initialize the remainder to zero
  lda #0
  sta mod10
  sta mod10 + 1
  clc
  ldx #16

divloop:
  ; Rotate quotient and remainder
  rol value
  rol value + 1
  rol mod10
  rol mod10 + 1
  ; a,y = dividend - divisor
  sec
  lda mod10
  sbc #10
  tay ; save low byte in Y
  lda mod10 + 1
  sbc #0
  bcc ignore_result ; branch if dividend < advisor
  sty mod10
  sta mod10 + 1

ignore_result:
  dex
  bne divloop
  rol value ; shift in the last bit of the quotient
  rol value + 1
  lda mod10
  clc
  adc #"0"
  jsr push_char
  ; if value != 0, then continue dividing
  lda value
  ora value + 1
  bne divide ; branch if value not zero
  ldx #0

print:
  lda message,x
  beq loop
  jsr print_char
  inx
  jmp print

; Subroutines

; Add the character in the A register to the beginning of the
; null-terminated string `message`
push_char:
  pha ; Push new first char onto stack
  ldy #0

char_loop:
  lda message,y ; Get char on string and put into X
  tax
  pla
  sta message,y ; Pull char off stack and add it to the string
  iny
  txa
  pha ; Push char from string onto stack
  bne char_loop
  pla
  sta message,y
  rts

lcd_wait:
  pha
  lda #%00000000 ; Port B is input
  sta DDRB

lcdbusy:
  lda #RW
  sta PORTA
  lda #(RW | E)
  sta PORTA
  lda PORTB
  and #%10000000
  bne lcdbusy
  lda #RW ; WHYY?
  sta PORTA
  lda #%11111111 ; Port B is output
  sta DDRB
  pla
  rts

lcd_instruction:
  jsr lcd_wait
  sta PORTB      ; Send LCD data
  lda #0         ; Clear RS/RW/E bits
  sta PORTA
  lda #E         ; Set E bit to send instruction
  sta PORTA
  lda #0         ; Clear RS/RW/E bits
  sta PORTA
  rts

print_char:
  jsr lcd_wait
  sta PORTB
  lda #RS        ; Set RS; Clear RW/E bits
  sta PORTA
  lda #(RS | E)  ; Set E bit to send instruction
  sta PORTA
  lda #RS        ; Set RS; Clear RW/E bits
  sta PORTA
  rts

; Interrupts

nmi:
irq:
  ; Push A, X, Y to stack
  pha
  txa
  pha
  tya
  pha

  ; Check if this is CA1 or timer
  lda IFR
  and #%00000010
  bne irq_ca1

irq_timer1:
  ; Disable timer
  lda #%01000000
  sta IER
  ; Clear timer interrupt
  bit T1CL
  ; Enable CA1 interrupt handler
  lda #%10000010
  sta IER
  jmp irq_finish

irq_ca1
  ; Enable timer
  lda #%11000000
  sta IER

  ; Start timer
  ; ACR6 and ACR7 = 0, can we assume this?
  ; Should be 66 ms
  lda #$FF
  sta T1CL
  lda #$FF
  sta T1CH

  ; Disable CA1
  lda #%00000010
  sta IER

  ; Clear CA1
  bit PORTA

  ; Increment counter
  inc counter
  bne irq_finish
  inc counter + 1

irq_finish:
  ; Pull A, X, Y from stack
  pla
  tay
  pla
  tax
  pla
  rti

; Reset vector and such
  .org $fffa
  .word nmi
  .word reset
  .word irq
