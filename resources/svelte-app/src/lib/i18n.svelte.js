import th from './locales/th.json';
import my from './locales/my.json';
import en from './locales/en.json';

const dictionaries = { th, my, en };

function getInitialLocale() {
  if (typeof localStorage !== 'undefined') {
    return localStorage.getItem('warp-locale') || 'th';
  }
  return 'th';
}

export const i18n = $state({ locale: getInitialLocale() });

export function setLocale(lang) {
  i18n.locale = lang;
  if (typeof localStorage !== 'undefined') {
    localStorage.setItem('warp-locale', lang);
  }
}

function resolve(obj, path) {
  const keys = path.split('.');
  let current = obj;
  for (const key of keys) {
    if (current == null || typeof current !== 'object') return undefined;
    current = current[key];
  }
  return current;
}

export function t(key, params = {}) {
  const dict = dictionaries[i18n.locale] || dictionaries.th;
  let value = resolve(dict, key);
  if (value == null) {
    value = resolve(dictionaries.th, key);
  }
  if (value == null) return key;
  return String(value).replace(/\{(\w+)\}/g, (_, k) => params[k] ?? `{${k}}`);
}

export const locales = [
  { code: 'th', name: 'ไทย' },
  { code: 'my', name: 'မြန်မာ' },
  { code: 'en', name: 'English' }
];
