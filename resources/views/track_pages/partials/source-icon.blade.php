@php
    $sourceToDomainMap = [
        'google' => 'google.com',
        'facebook' => 'facebook.com',
        'twitter' => 'twitter.com',
        'bing' => 'bing.com',
        'linkedin' => 'linkedin.com',
        'pinterest' => 'pinterest.com',
        'tiktok' => 'tiktok.com',
        'youtube' => 'youtube.com',
        'instagram' => 'instagram.com',
        'tawk' => 'tawk.to',
        'reddit' => 'reddit.com',
        'whatsapp' => 'whatsapp.com',
        'telegram' => 'telegram.org',
        'snapchat' => 'snapchat.com',
        'microsoft' => 'microsoft.com',
        'amazon' => 'amazon.com',
        'yahoo' => 'yahoo.com',
        'apple' => 'apple.com',
        'baidu' => 'baidu.com',
        'qq' => 'qq.com',
        'wikipedia' => 'wikipedia.org',
        'spotify' => 'spotify.com',
        'tumblr' => 'tumblr.com',
        'vimeo' => 'vimeo.com',
        'flickr' => 'flickr.com',
        'dropbox' => 'dropbox.com',
        'slack' => 'slack.com',
        'etsy' => 'etsy.com',
        'zoom' => 'zoom.us',
        'discord' => 'discord.com',
        'steam' => 'steampowered.com',
        'wordpress' => 'wordpress.com',
        'stackoverflow' => 'stackoverflow.com',
        'github' => 'github.com',
        'bitbucket' => 'bitbucket.org',
        'salesforce' => 'salesforce.com',
        'reddit' => 'reddit.com',
        'medium' => 'medium.com',
        'whatsapp' => 'whatsapp.com',
        'snapchat' => 'snapchat.com',
        'airbnb' => 'airbnb.com',
        'uber' => 'uber.com',
        'lyft' => 'lyft.com',
    ];

    $source = strtolower($data['sessionSource']);

    $domain = null;
    foreach ($sourceToDomainMap as $key => $mapDomain) {
        if (strpos($source, $key) !== false) {
            $domain = $mapDomain;
            break;
        }
    }

    $domain = $domain ?? $source;

    $faviconUrl = 'https://' . $domain . '/favicon.ico';
@endphp

<div class="d-flex align-items-center">
    <img src="{{ $faviconUrl }}" alt="{{ $domain }} favicon" width="16" height="16" class="me-2"
        onerror="this.style.display='none'">
    {{ ucfirst($source) }}
</div>
