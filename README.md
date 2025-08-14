# Craft RRULE Wrapper

A **Craft CMS module** that exposes the excellent [rlanvin/php-rrule](https://github.com/rlanvin/php-rrule/wiki) library to **Twig** so you can work with recurrence rules in templates.

> ℹ️ In **PHP**, you can (and should) use the original library classes directly (`RRule\RRule`, `RRule\RSet`). The wrapper mainly exists for **Twig** access via `craft.rrule`.

---

## Installation

```bash
composer require lewisjenkins/craft-rrule
```

---

## Timezone formatting

Each occurrence (`DateTime`) returned by RRule/RSet retains its own timezone (from `DTSTART`/`TZID`). In Twig, always format using the event's timezone to avoid converting to the site's default:

```twig
{% for d in rr.getOccurrences() %}
  {{ d|date('Y-m-d H:i e', d.timezone) }}<br>
{% endfor %}
```

---

## RRULE

An **RRULE** describes a single recurrence pattern.  
Reference: **RRule wiki** → https://github.com/rlanvin/php-rrule/wiki/RRule

### 1) Create from **string** and list all occurrences
```twig
{% set rr = craft.rrule.rrule('FREQ=DAILY;COUNT=3;DTSTART=20250809T090000Z') %}
{% for d in rr.getOccurrences() %}
  {{ d|date('Y-m-d H:i e', d.timezone) }}<br>
{% endfor %}
```
**Expected output**
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

---

### 2) Create from **array** and list all occurrences
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  COUNT: 4,
  BYDAY: ['MO','WE'],
  DTSTART: date('2025-08-04 09:00')
}) %}
{% for d in rr.getOccurrences() %}
  {{ d|date('Y-m-d H:i e', d.timezone) }}<br>
{% endfor %}
```
**Expected output**
```
2025-08-04 09:00
2025-08-06 09:00
2025-08-11 09:00
2025-08-13 09:00
```

---

### 3) Get occurrences **between** two dates
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  BYDAY: ['MO','FR'],
  DTSTART: date('2025-01-01 09:00')
}) %}
{% set dates = rr.getOccurrencesBetween(date('2025-01-01'), date('2025-02-01')) %}
{% for d in dates %}{{ d|date('Y-m-d H:i e', d.timezone) }}<br>{% endfor %}
```
**Expected output**
```
2025-01-03 09:00
2025-01-06 09:00
2025-01-10 09:00
2025-01-13 09:00
2025-01-17 09:00
2025-01-20 09:00
2025-01-24 09:00
2025-01-27 09:00
2025-01-31 09:00
```

---

### 4) Get the **next** occurrence (after a date, with limit)
> API: `getOccurrencesAfter($after, bool $inclusive = false, int $limit = 0)`
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  BYDAY: 'MO',
  DTSTART: date('2025-01-01 09:00')
}) %}
{% set next = rr.getOccurrencesAfter('now', true, 1)|first %}
Next: {{ next|date('Y-m-d H:i e', next.timezone) }}
```
**Expected output** (if “now” is 2025-08-09)
```
Next: 2025-08-11 09:00
```

---

### 5) Get occurrences **before** a date (with limit)
> API: `getOccurrencesBefore($before, bool $inclusive = false, int $limit = 0)`
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'DAILY',
  COUNT: 5,
  DTSTART: date('2025-08-09 09:00')
}) %}
{% set list = rr.getOccurrencesBefore(date('2025-08-12 09:00'), true, 2) %}
{% for d in list %}{{ d|date('Y-m-d H:i e', d.timezone) }}<br>{% endfor %}
```
**Expected output**
```
2025-08-11 09:00
2025-08-12 09:00
```

---

### 6) `count()` (finite rules)
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'DAILY',
  COUNT: 3,
  DTSTART: date('2025-08-09 09:00')
}) %}
Count: {{ rr.count() }}
```
**Expected output**
```
Count: 3
```

---

### 7) Human‑readable summary
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'WEEKLY',
  BYDAY: ['MO','FR'],
  COUNT: 4,
  DTSTART: date('2025-01-01 09:00')
}) %}
{{ rr.humanReadable() }}
```
**Possible output**
```
Every week on Monday, Friday, 4 times
```

---

### 8) RFC string (serialize)
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'MONTHLY',
  BYDAY: 'MO',
  BYSETPOS: 1,
  DTSTART: date('2025-01-01 09:00')
}) %}
{{ rr.rfcString() }}
```
**Example output**
```
RRULE:FREQ=MONTHLY;BYDAY=MO;BYSETPOS=1;DTSTART=20250101T090000Z
```

---

### 9) Inspect the parsed rule (`getRule()`)
```twig
{% set rr = craft.rrule.rrule({
  FREQ: 'DAILY',
  COUNT: 2,
  DTSTART: date('2025-08-09 09:00')
}) %}
{{ dump(rr.getRule()) }}
```

---

## RSET

An **RSET** combines multiple RRULEs, exception rules, and explicit include/exclude dates.  
Reference: **RSet wiki** → https://github.com/rlanvin/php-rrule/wiki/RSet

### Create from RFC-style string
```twig
{% set rset = craft.rrule.rset('\nDTSTART;TZID=America/New_York:19970901T090000\nRRULE:FREQ=DAILY;COUNT=3\nEXRULE:FREQ=DAILY;INTERVAL=2;COUNT=1\nEXDATE;TZID=America/New_York:19970903T090000\nRDATE;TZID=America/New_York:19970904T090000\n') %}
{% for d in rset.getOccurrences() %}{{ d|date('Y-m-d H:i e', d.timezone) }}<br>{% endfor %}
```
**Expected output**
```
1997-09-02 09:00
1997-09-04 09:00
```

---

### 1) Build with an RRULE and an **exclusion** date
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'DAILY', COUNT: 5, DTSTART: date('2025-08-09') }) %}
{% do rset.addExDate(date('2025-08-11')) %}
{% for d in rset.getOccurrences() %}{{ d|date('Y-m-d', d.timezone) }}<br>{% endfor %}
```
**Expected output**
```
2025-08-09
2025-08-10
2025-08-12
2025-08-13
```

---

### 2) Add **multiple** RRULEs
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'YEARLY', COUNT: 2, BYDAY: 'TU', DTSTART: date('1997-09-02 09:00') }) %}
{% do rset.addRRule({ FREQ: 'YEARLY', COUNT: 1, BYDAY: 'TH', DTSTART: date('1997-09-02 09:00') }) %}
{% for d in rset.getOccurrences() %}{{ d|date('Y-m-d H:i e', d.timezone) }}<br>{% endfor %}
```
**Expected output**
```
1997-09-02 09:00
1997-09-04 09:00
1997-09-09 09:00
```

---

### 3) Get occurrences **between** dates
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'WEEKLY', BYDAY: ['MO','FR'], DTSTART: date('2025-01-01 09:00') }) %}
{% set dates = rset.getOccurrencesBetween(date('2025-01-01'), date('2025-02-01')) %}
{% for d in dates %}{{ d|date('Y-m-d H:i e', d.timezone) }}<br>{% endfor %}
```
**Expected output**
```
2025-01-03 09:00
2025-01-06 09:00
2025-01-10 09:00
2025-01-13 09:00
2025-01-17 09:00
2025-01-20 09:00
2025-01-24 09:00
2025-01-27 09:00
2025-01-31 09:00
```

---

### 4) Get occurrences **after**/**before** with a **limit**
> API: `getOccurrencesAfter($after, bool $inclusive = false, int $limit = 0)`
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({ FREQ: 'DAILY', DTSTART: date('2025-08-09 09:00') }) %}
{% set next3 = rset.getOccurrencesAfter('now', true, 3) %}
{% for d in next3 %}{{ d|date('Y-m-d H:i e', d.timezone) }}<br>{% endfor %}
```
**Expected output** (if “now” is 2025-08-09 09:00)
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-11 09:00
```

---

### 5) Add/remove explicit dates (RDATE/EXDATE)
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRDate(date('2025-08-10 12:00')) %}
{% do rset.addExDate(date('2025-08-11 09:00')) %}
{% do rset.addRRule({ FREQ: 'DAILY', COUNT: 3, DTSTART: date('2025-08-09 09:00') }) %}
{% for d in rset.getOccurrences() %}{{ d|date('Y-m-d H:i e', d.timezone) }}<br>{% endfor %}
```
**Expected output**
```
2025-08-09 09:00
2025-08-10 09:00
2025-08-10 12:00
2025-08-12 09:00
```

---

## ⚠ Infinite recurrences (what they are & how to avoid)

An RRULE (or an RSET containing one) is **infinite** if it omits both `COUNT` and `UNTIL`. Iterating an infinite rule with `getOccurrences()` will **never finish**.

**Infinite rule example (don’t do this)**
```twig
{% set rr = craft.rrule.rrule('FREQ=DAILY;DTSTART=20250809T090000Z') %}
{# ❌ Will never end: #}
{# for d in rr.getOccurrences() %}{{ d|date('Y-m-d H:i e', d.timezone) }}{% endfor #}
```

**Safe alternatives**
```twig
{# 1) Add COUNT #}
{% set r1 = craft.rrule.rrule('FREQ=DAILY;COUNT=5;DTSTART=20250809T090000Z') %}

{# 2) Limit with a window #}
{% set r2 = craft.rrule.rrule('FREQ=DAILY;DTSTART=20250809T090000Z') %}
{% set window = r2.getOccurrencesBetween('2025-08-09','2025-08-12') %}

{# 3) Cap using getOccurrencesAfter with a limit #}
{% set next3 = r2.getOccurrencesAfter('2025-08-09 09:00', true, 3) %}
```

---

## References
- **RRule**: https://github.com/rlanvin/php-rrule/wiki/RRule  
- **RSet**: https://github.com/rlanvin/php-rrule/wiki/RSet  
- **RFC 5545** (iCalendar RRULE): https://datatracker.ietf.org/doc/html/rfc5545
