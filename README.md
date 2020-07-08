# craftagram plugin for Craft CMS 3.x

Grab Instagram content through the Instagram Basic Display API

## Requirements

This plugin requires Craft CMS 3.0.0 or later.

## Installation

To install the plugin, follow these instructions.

1. Open your terminal and go to your Craft project:

        cd /path/to/project

2. Then tell Composer to load the plugin:

        composer require scaramangagency/craftagram

3. In the Control Panel, go to Settings → Plugins and click the “Install” button for craftagram.

## Setting up your Facebook App

This is just a shortened version of what is available at the [official docs](https://developers.facebook.com/docs/instagram-basic-display-api/getting-started), so if you get stuck, check out the official docs.

1. Go to https://developers.facebook.com, click My Apps, and create a new app.
2. Once you have created the app and are in the App Dashboard, navigate to Settings > Basic, scroll the bottom of page, and click Add Platform.
3. Choose Website, add your website’s URL, and save your changes.
4. Click Products, locate the Instagram product, and click Set Up to add it to your app.
5. Click Basic Display under Products > Instagram in the sidebar, scroll to the bottom of the page, then click Create New App.
6. In the form that appears, complete each section using the below:
    - **Display Name** Enter the name of the Facebook app you just created. This _should_ pre-populate.
    - **Valid OAuth Redirect URIs** Enter your _Primary Site_ URL, appended with `/actions/craftagram/default/auth` (i.e. https://www.yourwebsite.com/actions/craftagram/default/auth)
    - **Deauthorize Callback URL** and **Data Deletion Request Callback URL** Use the same URL as above.
    - You can ignore App Review, but **please note** that if you plan to publish this app, then you will need to turn on the `instagram_graph_user_profile` option, as this is required for creating a long access token.
    - Save Changes
7. Navigate to Roles > Roles and scroll down to the Instagram Testers section. Click Add Instagram Testers and enter the name of the Instagram account you're linking up.
8. Open a new web browser and go to www.instagram.com and sign into your Instagram account that you just invited. Navigate to (Profile Icon) > Edit Profile > Apps and Websites > Tester Invites and accept the invitation.

That's it! You won't need any extra setup now. What you will need to do is go to Products > Instagram > Basic Display and scroll down to `Instagram App ID
` and `Instagram App Secret`, as you'll need to add these in the next step.

## Configuring craftagram

Go to the settings page for `craftagram` and enter your `App ID` and `App Secret` from the step above into the required boxes, and hit 'Save'. When the page refreshes, you'll see there's a new button `Authorise Craft`. Click that button to go to instagram to complete the authorisation procedure.

Instagram may challenge you with a login screen, so handle that, then click 'Authorize'. It'll do some work, and then redirect you back to Craft with the Long Access Token field populated.

### Keeping your token active

Instagram tokens expire in 60 days, so you'll need to set up a cron job to keep the token alive. The refresh action is `actions/craftagram/default/refresh-token`.

For example, this would run the token refresh every month

```
/usr/bin/wget -q 0 0 1 * * https://www.yourwebsite.com/actions/craftagram/default/refresh-token >/dev/null 2>&1
```

If you fail to set up the cron, you can still refresh the token manaully, by going to the settings page, clicking the `Authorise Craft` and following the steps outlined above.

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

You can pass one parameter to the variable, `limit`. The default limit from instagram is 25.

```
{% set craftagram = craft.craftagram.getInstagramFeed(10) %}
```

The options that you get are [all of the options](https://developers.facebook.com/docs/instagram-basic-display-api/reference/media#fields) provided from the API endpoint. For brevity, they are:

| Field Name | Description |
| --- | --- |
| caption | The Media's caption text. Not returnable for Media in albums. |
| id | The Media's ID. |
| media_type | The Media's type. Can be IMAGE, VIDEO, or CAROUSEL_ALBUM. |
| media_url | The Media's URL. |
| permalink | The Media's permanent URL. |
| thumbnail_url | The Media's thumbnail image URL. Only available on VIDEO Media. |
| timestamp | The Media's publish date in ISO 8601 format. |
| username | The Media owner's username. |

### Pagination

If you're limiting, you'll need to paginate. You can get the next URL using `{{ craftagram.paging.next|url_encode }}`. **You will need** to use the `url_encode` filter, otherwise the pagination will fail.

For example, you could do this to have a 'load more' button:
```
{% set craftagram = craft.craftagram.getInstagramFeed(10) %}

{% if craftagram|length %}
    <div data-js="insta-wrapper">
        {% for item in craftagram.data %}
            <img src={{item.media_url}} />
        {% endfor %}
    </div>

    <a href="{{ craftagram.paging.next|url_encode }}" data-js="load-more">Load more</a>
{% endif %}

{% js %}
    $("[data-js=load-more]").click(function(e) {
        e.preventDefault();
        $.get("{{ parseEnv(craft.app.sites.primarySite.baseUrl) }}/actions/craftagram/default/get-next-page?url=" + $(this).attr('href'), function(res) {
            data = $.parseJSON(res);

            // For each, append the item to our wrapper
            $.each(data["data"], function() {
                $("[data-js='insta-wrapper']").append("<img src="+$(this)[0]["media_url"]+" />");
            });

            // Update the paging URL. Note the encodeURIComponent
            $("[data-js=load-more]").attr("href", encodeURIComponent(data["paging"]["next"]));
        });
    });
{% endjs %}
```

### Profile Information

> :warning: This uses the publically available instagram GraphQL API, accessible by adding *?__a=1* to an instagram URL. This may be deprecated or removed in the future.

Used to grab some basic profile information not available natively in the Basic Display API. You can pass in any instagram profile, as this endpoint returns this information regardless of public or private status.

```
{% set craftagram = craft.craftagram.getProfileMeta("scaramanga_agency") %}
```

This variable has 4 available fields:

| Field Name |
| --- |
| profile_picture |
| profile_picture_hd |
| followers |
| following |

### Rate Limits

Be concious you might be subject to rate limits from instagram, so if you're on a high traffic website you might get rate limited. You can read more about rate limits at instagram's [documentation](https://developers.facebook.com/docs/graph-api/overview/rate-limiting#instagram). 

### Media Size

The image returned from the API is an immutable size–it used to be you could use modifiers like `large` to get an image at a certain size, but no more. You will need to use a plugin that supports transforming images from remote URL's to resize the images returned from Instagram.


---
Brought to you by [Scaramanga Agency](https://scaramanga.agency)
