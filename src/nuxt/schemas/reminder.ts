import { number, object, string, ref } from "yup";

export const reminderSchema = object({
  title: string().required().label('Title'),
  description: string().required().label('Description'),
  event_at: number()
    .required()
    .label('Event date & time')
    .transform((value, originalValue) => dateStringToUnix(originalValue))
    .min(dateStringToUnix((new Date()).toLocaleString()), 'Event date must be in the future'),
  remind_at: number()
    .required()
    .label('Reminder date & time')
    .transform((value, originalValue) => dateStringToUnix(originalValue))
    .min(dateStringToUnix((new Date()).toLocaleString()), 'Reminder date must be in the future')
    .lessThan(ref('event_at'), 'Reminder date must be before event date'),
})