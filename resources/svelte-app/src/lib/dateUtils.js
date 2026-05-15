export function formatThaiDateTime(dateStr) {
  if (!dateStr) return { time: '--:--', date: '--' };
  try {
    const spaceIdx = dateStr.indexOf(' ');
    if (spaceIdx < 0) return { time: '--:--', date: '--' };
    const datePart = dateStr.substring(0, spaceIdx);
    const timePart = dateStr.substring(spaceIdx + 1);
    const dp = datePart.split('-');
    const tp = timePart.split(':');
    if (dp.length < 3 || tp.length < 2) return { time: '--:--', date: '--' };
    const year = Number(dp[0]);
    const month = Number(dp[1]) - 1;
    const day = Number(dp[2]);
    const hour = Number(tp[0]);
    const minute = Number(tp[1]);
    const second = tp[2] ? Number(tp[2]) : 0;
    if (isNaN(year) || isNaN(month) || isNaN(day) || isNaN(hour) || isNaN(minute)) {
      return { time: '--:--', date: '--' };
    }
    const d = new Date(year, month, day, hour, minute, second);
    if (isNaN(d.getTime())) return { time: '--:--', date: '--' };
    return {
      time: d.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', timeZone: 'Asia/Bangkok' }),
      date: d.toLocaleDateString('th-TH', { day: 'numeric', month: 'short', timeZone: 'Asia/Bangkok' })
    };
  } catch (e) {
    console.error('Date parse error:', e);
    return { time: '--:--', date: '--' };
  }
}