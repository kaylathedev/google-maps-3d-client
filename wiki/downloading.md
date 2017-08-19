# Downloading

**Observations**

When using `curl 'https://www.google.com/maps/'` with no user agent, the server sends back a 302 which appends an @ to the url. Following the redirection leads us to another 302 with '?dg=dbrw&newdg=1' appended.

Final url is 'https://www.google.com/maps/@?dg=dbrw&newdg=1'

Setting the user agent to one typically used by chromium will prevent the 2 redirections.

**Url**

Look at "https://www.google.com/maps/@39.956846,-76.7271487,652a,35y,350.07h,44.69t/data=!3m1!1e3
"

This url needed consists of 4 parts.

Protocol, domain, path, the parameters, and flags. The parameters begin with an "@" symbol and are separated by commas.

The parameters are as follows

 * Latitude "39.956846"
 * Longitude "-76.7271487"

Then we have unordered paramters. Their meaning is indicated by the last letter.

 * "m" Height - Only appears when the camera is perfectly face-down, and compass points north.
 * "a" Height - Doesn't appear to be meters.
 * "y" Unknown - Always stays 35.
 * "h" Rotation - (0 deg to 360 deg)
 * "t" Tilt - How close to the horizion is the camera. Face-down is 0 deg. Horizon is 90 deg.

The flags are to tell the server to use Earth mode. [1]

## Version 1

https://www.google.com/maps/@39.956846,-76.7271487,652a,35y,350.07h,44.69t/data=!3m1!1e3

    VERSION=1
    UA='Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/60.0.3112.78 Chrome/60.0.3112.78 Safari/537.36'
    curl 'https://www.google.com/maps/@39.956846,-76.7271487,652a,35y,350.07h,44.69t/data=!3m1!1e3?dg=dbrw&newdg=1' -H '$UA' > data/$VERSION/raw/index.html
    curl 'https://www.google.com/maps/_/js/k=maps.m.en.GI084nf_urg.O/m=sc2,mo,per,ti,ds,stx,b/rt=j/d=1/rs=ACT90oHjrkifREgugnHBZGa3YYDKt5kFFg' -H '$UA' > data/$VERSION/raw/main.js

# Credits

[1] See Dave Shaw's blog post at http://dddavemaps.blogspot.com/2015/07/google-maps-url-tricks.html
