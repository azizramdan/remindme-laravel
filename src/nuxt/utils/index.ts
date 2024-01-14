// TODO: include timezone in date
export function unixToDateTime(unix: number) {
  const date = new Date(unix * 1000);

  const options: Intl.DateTimeFormatOptions = {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: 'numeric',
    minute: 'numeric',
    hour12: true,
    timeZoneName: 'short',
  };

  return new Intl.DateTimeFormat('en-US', options).format(date);
}

export function dateStringToUnix(date: string) {
  return new Date(date).getTime() / 1000;
}