# Mautic - Cronfig plugin

This plugin will add a new menu item to the right hand side Mautic menu called *Cronfig*. Under this menu item there will be a dashboard which will let you create cron tasks in the [Cronfig.io](https://cronfig.io) service which will take care of the Mautic background tasks.

## Installation

1. Download the plugin.
2. Unzip the downloaded package.
3. Upload the unziped folder to the `plugins` folder. Make sure the plugin folder is also correctly named: `plugins/CronfigBundle`.
4. Clear Mautic's cache. Either run `app/console cache:clear` command in the Mautic's root dir or simply delete the content of `app/cache` dir.
5. Go to the right hand side Mautic menu (click the cog icon in the top right corner to slide the menu out) and go to *Plugins*.
6. Click the *Install/Upgrade Plugins* button. The Cronfig icon should appear in the list of plugins.
7. Go to the right hand side Mautic menu again. A new menu item called *Cronfig* should be there. Hit it. A register/login screan should appear.
8. Your email should be prefilled. Click on the *Register* button and check your email inbox. Your API key should appear in your inbox any second.
9. If you received the Cronfig registration email, copy the API key from it, insert it to the *Log in* input and log in.

## Create the cron tasks

Mautic plugin prepared the cron tasks for you. Just allow the ones you want. The Free account lets you run the required Mautic tasks for free for unlimited time. Check the pricing page for more details. 
