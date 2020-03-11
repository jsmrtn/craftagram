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

1. Go to https://developers.facebook.com, click My Apps, and create a new app. Once you have created the app and are in the App Dashboard, navigate to Settings > Basic, scroll the bottom of page, and click Add Platform.
2. Choose Website, add your website’s URL, and save your changes.
3. Click Products, locate the Instagram product, and click Set Up to add it to your app.
4. Click Basic Display, scroll to the bottom of the page, then click Create New App.
5. In the form that appears, complete each section using the below:
    - **Display Name** Enter the name of the Facebook app you just created. This _should_ pre-populate.
    - **Valid OAuth Redirect URIs** Enter your _Primary Site_ URL, appended with `/actions/craftagram/default/auth` (i.e. https://scaramanga.agency/actions/craftagram/default/auth)
    - **Deauthorize Callback URL** and **Data Deletion Request Callback URL** Use the same URL as above.
    - You can ignore App Review, but **please note** that if you plan to publish this app, then you will need to turn on the `instagram_graph_user_profile` option, as this is required for creating a long access token.
6. Navigate to Roles > Roles and scroll down to the Instagram Testers section. Click Add Instagram Testers and enter the name of the Instagram account you're linking up.
7. Open a new web browser and go to www.instagram.com and sign into your Instagram account that you just invited. Navigate to (Profile Icon) > Edit Profile > Apps and Websites > Tester Invites and accept the invitation.

That's it! You won't need any extra setup now. What you will need to do is go to Products > Instagram > Basic Display and scroll down to `Instagram App ID
` and `Instagram App Secret`, as you'll need to add these in the next step.

## Configuring craftagram

Go to the settings page for `craftagram` and enter your `App ID` and `App Secret` from the step above into the required boxes, and hit 'Save'. When the page refreshes, you'll see there's a new button `Get Authorization URL`. Click that button to generate the URL, and then follow the URL to Instagram.

Instagram may challenge you with a login screen, so handle that, then click 'Authorize'. It'll do some work, and then redirect you back to Craft with the Long Access Token field populated.

### Keeping your token active

Instagram tokens expire in 60 days, so you'll need to set up a cron job to keep the token alive. The refresh action is `actions/craftagram/default/refresh-token`.

If you fail to set up the cron, you can still refresh the token manaully, by going to the settings page, clicking the `Get Authorizaton URL` and following the steps outlined above.

## Using craftagram

Using the plugin is pretty simple

```
{% set craftagram = craft.craftagram.getInstagramFeed() %}

{% for item in craftagram.data %}
    <img src={{item.media_url}} />
{% endfor %}
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

<div data-js="insta-wrapper">
    {% for item in craftagram.data %}
        <img src={{item.media_url}} />
    {% endfor %}
</div>

<a href="{{ craftagram.paging.next|url_encode }}" data-js="load-more">Load more</a>

{% js %}
    $("[data-js=load-more]").click(function(e) {
        e.preventDefault();
        $.get("{{ craft.app.sites.primarySite.baseUrl }}/actions/craftagram/default/get-next-page?url=" + $(this).attr('href'), function(res) {
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
---
Brought to you by [Scaramanga Agency](https://scaramanga.agency)
