# Tracking: GTM or GA4

Every site runs **Microsoft Clarity**. For Google, a site uses **one** of two setups, depending on what the client provides:

| The client provides | Activate | Configure in *Settings* |
| --- | --- | --- |
| A GTM container (`GTM-XXXX`) | `duracelltomi-google-tag-manager` | *Google Tag Manager*: container ID. GA4, Meta Pixel and any other tag are set up **inside GTM** |
| Only a GA4 property (`G-XXXX`) | `ga-google-analytics` | *Google Analytics*: Measurement ID, tracking method "GA4" |

```bash
docker compose exec app php artisan plugins:activate duracelltomi-google-tag-manager   # GTM
docker compose exec app php artisan plugins:activate ga-google-analytics               # or GA4
```

::: danger Never both
GA4 configured in GTM **and** through GA Google Analytics counts every visit twice. Both plugins ship inactive, and `plugins:activate` without arguments leaves them inactive.
:::

## Clarity

Activate it, then connect the Clarity project in *Settings → Microsoft Clarity*.

## What happened to Hotjar, Pixel and the `.env` codes

- Hotjar is replaced by Clarity.
- The Meta Pixel and other marketing tags go inside GTM.
- `GTM_CODE`, `GOOGLE_ANALYTICS`, `HOTJAR_KEY` and `FACEBOOK_PIXEL` are gone from `.env`.
