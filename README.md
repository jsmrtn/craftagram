# craftagram plugin for Craft CMS 4.x / 5.x

Grab Instagram content through the Instagram API with Instagram Login.

## Requirements

This plugin requires Craft CMS 4.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require jsmrtn/craftagram

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for craftagram.

## Set up

This plugin only presently supports the `Instagram API with Instagram Login` route for using the API. This means you do not need to have a Facebook page linked to your Instagram professional account.

0. Run thorough the `Pre-requisites` list below.
1. Log in to [https://developers.facebook.com](https://developers.facebook.com), and in All Apps click Create App.
2. When asked if you want to connect a business portfolio, select `I don't want to connect a business portfolio yet`.
3. When asked what you want your app to do, select `Other`.
4. Select `Business` as your app type.
5. Add a suitable app name and contact email (you can ignore adding a business portfolio).
6. You will be redirected to your new app, from the dasboard locate the Instagram product, and click `Set Up` to add it to your app.
7. Click `Add account` under _Generate access tokens_, you will be prompted to link your instagram account. You will note it specifies your personal account will be converted to a professional account automatically, if it isn't already. You can select either `Business` or `Creator`, depending on your use case.
8. Enter your _Primary Site base URL_, appended with `/actions/craftagram/default/auth` (i.e. https://www.yourwebsite.com/actions/craftagram/default/auth) into `Set up Instagram business login`.
9. Copy `Embed URL`, `Instagram app ID` and `Instagram app secret` for use in `Configuring craftagram` below.

You do not have to fill out webhooks, as there is no functionality avaialble for webhooks in this plugin. Likewise, you can likely skip app review as generally usage of this plugin is for individual Instagram businesses and not client solutions. Finally, it should be possible to leave your app in Development mode, rather than switching it live. 

If you do opt to switch to a live app and send for app review, please note that this is a process separate from this plugin, and I cannot offer support for this process.

### Pre-requisites

#### Instagram Business Account

To create an app with the Instagram API with Instagram Login, you need an Instagram business account. It's a simple process, one that you can do on personal accounts, too, without causing any damage.

1. Go to your profile and tap in the top-right corner. 
2. Tap Settings and privacy, then Account type and tools and Switch to professional account.
3. Pick a category that best describes your business, then select Business.
4. All done. You have a business account.

#### Meta Developer

You must be a registered as a meta developer before you can integrate with their APIs. You can follow this process [here](https://developers.facebook.com/docs/development/register).



## Configuring craftagram

Go to the settings page for `craftagram` and enter your `App ID`, `App Secret` and `Embed URL` from the steps above into the required boxes, and hit 'Save'. When the page refreshes, you'll see there's a new button `Authorise Craft`. Click that button to go to instagram to complete the authorisation procedure.

> Tip: The App ID and App Secret settings can be set to environment variables. See Environmental Configuration in the Craft docs to learn more about that.

You with likely be presented a login screen, so enter your login details, then click 'Authorize'. You will be redirected back to Craft with the Long Access Token field populated.

> :warning: Check you're logged in to the correct account before you try to authenticate (or don't be logged in at all). If you're logged in with a different user in the current browser session, you're going to have issues.

### Keeping your token active

Instagram tokens expire in 60 days, so you'll need to set up a cron job to keep the token alive. The refresh action is `actions/craftagram/default/refresh-token`.

For example, this would run the token refresh every month, for all enabled sites with tokens

```
0 0 1 * * /usr/bin/wget -q https://www.yourwebsite.com/actions/craftagram/default/refresh-token >/dev/null 2>&1
```

If you just want to update a single site you can add the optional param `siteId`

 ```
 0 0 1 * * /usr/bin/wget -q https://www.yourwebsite.com/actions/craftagram/default/refresh-token?siteId=<your siteId> >/dev/null 2>&1
 ```

If you fail to set up the cron, you can still refresh the token manaully, by going to the settings page, clicking the `Authorise Craft` and following the steps outlined above.

> :warning: You cannot refresh access tokens for private Instagram accounts, so ensure the account used in your tester invite above is public

## Using craftagram

Using the plugin is pretty simple

```
{% set craftagram = craft.craftagram.getInstagramFeed() %}

{% if craftagram|length %}
    {% for item in craftagram.data %}
        <img src={{item.media_url}} />
    {% endfor %}
{% endif %}
```

There are two parameters available to the variable, `limit` and `siteId`. The default limit from instagram is 25.

```
{% set craftagram = craft.craftagram.getInstagramFeed(25, currentSite.id) %}
```

| Field Name | Description |
| --- | --- |
| limit | The default limit from instagram is 25 |
| siteId | The current site's ID. If you only have one site on your install you can leave this blank, otherwise pass the `siteId` for the site you have added the authorisation to. You can hard-code the site ID if you have only set up authorisation on one of your multi-site installs, otherwise pass the current `siteId` dynamically |

The plugin returns all fields that are [provided from the API endpoint.](https://developers.facebook.com/docs/instagram-platform/instagram-graph-api/reference/ig-media#fields) provided from the API endpoint.

### Profile Information

You get first-class support for basic profile information for the connected user.

```
{% set craftagram = craft.craftagram.getInstagramProfileInformation() %}
```

The plugin returns all fields that are [provided fro the API endpoint.](https://developers.facebook.com/docs/instagram-platform/instagram-api-with-instagram-login/get-started#fields)

### Pagination

If you have limits on your feed, you can pass the `after` (or `before` if you're paginating backwards) parameter and init an AJAX function to return the data.

Remember to pass `limit`, and also to pass the correct `siteId` for the active site, if appropriate.

For example, you could do this to have a 'load more' button:
```
{% set craftagram = craft.craftagram.getInstagramFeed(10) %}

{% if craftagram|length %}
    <div data-js="insta-wrapper">
        {% for item in craftagram.data %}
            <img src={{item.media_url}} />
        {% endfor %}
    </div>

    <a data-after="{{ craftagram.paging.cursors.after }}" data-js="load-more">Load more</a>
{% endif %}

{% js %}
    $("[data-js=load-more]").click(function(e) {
        e.preventDefault();
        $.get("{{ parseEnv(craft.app.sites.primarySite.baseUrl) }}/actions/craftagram/default/get-next-page?siteId={{ currentSite.id }}&limit=10&url=" + $(this).data('after'), function(res) {
            data = $.parseJSON(res);

            // For each, append the item to our wrapper
            $.each(data["data"], function() {
                $("[data-js='insta-wrapper']").append("<img src="+$(this)[0]["media_url"]+" />");
            });

            // Update the paging with the next after.
            $("[data-js=load-more]").data("after", data["paging"]["cursors"]["after"]);
        });
    });
{% endjs %}
```

### Headless mode

If you're using Craft headless (or generally just need a JSON formatted version of your results), you can access the instagram feed via `/actions/craftagram/default/api` (or `/craftagramApi` if you want to save some bytes), which will return the raw JSON data from instagram. You can pass the following parameters:

| URL Parameter | Description |
| --- | --- |
| limit | The default limit from instagram is 25 |
| siteId | The current site's ID. If you only have one site on your install you can leave this blank, otherwise pass the `siteId` for the site you have added the authorisation to. You can hard-code the site ID if you have only set up authorisation on one of your multi-site installs, otherwise pass the current `siteId` dynamically |
| url | Pass the `after` or `before` parameters from `data->paging->cursors` to get the next or the previous set of results |

#### Security

There is a setting to opt-in to a more secure API endpoint. If you switch it on, you must pass a `Basic Auth` header to access this endpoint, otherwise you will receive an error. The Username and Password should be for an activated Craft user. **Please note** that you must enable the secure endpoint for each site individually.

### Rate Limits

Be conscious you might be subject to rate limits from instagram, so if you're on a high traffic website you might get rate limited. You can read more about rate limits at instagram's [documentation](https://developers.facebook.com/docs/graph-api/overview/rate-limiting#instagram). 
