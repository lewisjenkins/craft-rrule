# Craft RRULE Wrapper

A minimal **Craft CMS 5 module** that exposes the [php-rrule](https://github.com/rlanvin/php-rrule) library to **Twig**. This wrapper stays faithful to the original library and does not change its behaviour — it simply makes `RRule` and `RSet` available in templates.

## Requirements
- PHP 8.2+
- Craft CMS 5+

## Installation

```bash
composer require lewisjenkins/craft-rrule
```

## Usage (Twig)
The module exposes two helpers on `craft.rrule`:

- `craft.rrule.rrule(spec)` — create an `RRule` from an **array** or **RFC-style string**
- `craft.rrule.rset(spec?)` — create an `RSet`; optional **multi-line RFC block** string

### RRULE — simple string example
```twig
{% set rr = craft.rrule.rrule('
DTSTART;TZID=America/New_York:20250809T090000
RRULE:FREQ=DAILY;COUNT=3
') %}
{% for d in rr.getOccurrences() %}
  {{ d|date('Y-m-d H:i e', d.timezone) }}<br>
{% endfor %}
```

### Expected output
```
2025-08-09 09:00 America/New_York
2025-08-10 09:00 America/New_York
2025-08-11 09:00 America/New_York
```

### RRULE — array example
```twig
{% set rr = craft.rrule.rrule({
  'FREQ': 'DAILY',
  'COUNT': 3,
  'DTSTART': date('2025-08-09 09:00', 'America/New_York')
}) %}
{% for d in rr.getOccurrences() %}
  {{ d|date('Y-m-d H:i e', d.timezone) }}<br>
{% endfor %}
```
> Note: Twig doesn't provide `date_create()` or `timezone()`. Use Twig's `date()` function to create a DateTime in a specific timezone.

### Expected output
```
2025-08-09 09:00 America/New_York
2025-08-10 09:00 America/New_York
2025-08-11 09:00 America/New_York
```

### RSET — multi-line block example
```twig
{% set rset = craft.rrule.rset('
DTSTART;TZID=America/New_York:19970901T090000
RRULE:FREQ=DAILY;COUNT=3
EXDATE;TZID=America/New_York:19970902T090000
') %}
{% for d in rset.getOccurrences() %}
  {{ d|date('Y-m-d H:i e', d.timezone) }}<br>
{% endfor %}
```

### Expected output
```
1997-09-01 09:00 America/New_York
1997-09-03 09:00 America/New_York
```

### RSET — array example
```twig
{% set rset = craft.rrule.rset() %}
{% do rset.addRRule({
  'FREQ': 'DAILY',
  'COUNT': 3,
  'DTSTART': date('1997-09-01 09:00', 'America/New_York')
}) %}
{% do rset.addExDate(date('1997-09-02 09:00', 'America/New_York')) %}
{% for d in rset.getOccurrences() %}
  {{ d|date('Y-m-d H:i e', d.timezone) }}<br>
{% endfor %}
```

### Expected output
```
1997-09-01 09:00 America/New_York
1997-09-03 09:00 America/New_York
```

### Timezone formatting
Each occurrence (`DateTime`) retains its own timezone. Always format using the occurrence’s timezone:
```twig
{{ d|date('Y-m-d H:i e', d.timezone) }}
```

## Full API & Examples
Refer to the original library for the complete API and many examples:
- **RRule**: https://github.com/rlanvin/php-rrule/wiki/RRule
- **RSet**: https://github.com/rlanvin/php-rrule/wiki/RSet
