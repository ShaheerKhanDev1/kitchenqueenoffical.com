<?php
/** Shared PHP header fragment for API utility pages. */
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($pageTitle ?? 'Kitchen Queen API') ?></title>
  <style>
    body{font-family:Inter,system-ui,sans-serif;margin:0;background:#111;color:#f4e7c3}
    header{padding:18px 24px;background:#1a1a1a;border-bottom:1px solid #333;display:flex;justify-content:space-between;align-items:center}
    header strong{color:#d4a017}
    main{padding:24px;max-width:960px;margin:0 auto}
  </style>
</head>
<body>
<header>
  <strong>Kitchen Queen</strong>
  <span><?= htmlspecialchars($pageTitle ?? 'Backend') ?></span>
</header>
<main>
