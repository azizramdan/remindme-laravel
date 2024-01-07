import mitt, { type Emitter } from 'mitt'
import type { TReminder } from '~/types/reminder'

type TEvents = {
  'modal:reminder:delete': TReminder,
}

const emitter: Emitter<TEvents> = mitt<TEvents>()

export const useEventEmit = emitter.emit
export const useEventOn = emitter.on
export const useEventOff = emitter.off