<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
/* Reset */
body { margin: 0; padding: 0; width: 100% !important; -webkit-text-size-adjust: none; background-color: #F0F4F1; }
table { border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
td { vertical-align: top; }
img { border: none; -ms-interpolation-mode: bicubic; max-width: 100%; }
a { color: #23823F; }

/* Structure */
.wrapper { background-color: #F0F4F1; width: 100%; }
.content { width: 100%; max-width: 600px; margin: 0 auto; }

/* Header */
.header { padding: 32px 0 24px; background-color: #0F3D27; text-align: center; border-radius: 12px 12px 0 0; }
.header a { text-decoration: none; }

/* Body */
.body { background-color: #F0F4F1; }
.inner-body { background-color: #ffffff; border: 1px solid #DCE7DF; border-radius: 0 0 8px 8px; width: 570px; }
.content-cell { padding: 36px 48px; color: #18352A; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size: 15px; line-height: 1.6; }

/* Typography */
.content-cell p { margin: 0 0 16px; color: #18352A; }
.content-cell h1, .content-cell h2 { color: #0F3D27; margin: 0 0 16px; font-weight: 700; }
.content-cell strong { color: #18352A; }
.content-cell a { color: #23823F; }
.content-cell hr { border: none; border-top: 1px solid #DCE7DF; margin: 24px 0; }

/* Button */
.button { display: inline-block; border-radius: 8px; font-weight: 600; font-size: 15px; line-height: 1; padding: 14px 28px; text-decoration: none; }
.button-primary { background-color: #23823F; color: #ffffff !important; border: 2px solid #23823F; }
.button-primary:hover { background-color: #174C32; }

/* Panel / blockquote */
.panel { background-color: #F3FAF4; border: 1px solid #C2DFC8; border-radius: 8px; padding: 16px 20px; margin: 0 0 16px; }
.panel-content p { color: #18352A; margin: 0; }

/* Footer */
.footer { width: 570px; }
.footer-content { padding: 24px 48px 32px; text-align: center; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Helvetica, Arial, sans-serif; font-size: 13px; color: #68766D; }
.footer-content a { color: #68766D; text-decoration: underline; }

/* Sub-copy (action URL fallback) */
.subcopy { border-top: 1px solid #DCE7DF; padding-top: 16px; margin-top: 24px; font-size: 13px; color: #68766D; }
.subcopy a { color: #23823F; word-break: break-all; }

@media only screen and (max-width: 600px) {
    .inner-body { width: 100% !important; }
    .footer { width: 100% !important; }
    .content-cell { padding: 28px 24px !important; }
    .footer-content { padding: 20px 24px 28px !important; }
}
@media only screen and (max-width: 500px) {
    .button { width: 100% !important; text-align: center !important; display: block !important; box-sizing: border-box !important; }
}
</style>
{!! $head ?? '' !!}
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center" style="padding: 32px 16px;">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">

{!! $header ?? '' !!}

<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell">
{!! Illuminate\Mail\Markdown::parse($slot) !!}
{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}

</table>
</td>
</tr>
</table>
</body>
</html>
