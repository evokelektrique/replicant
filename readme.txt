=== Plugin Name ===
Contributors: evokelektrique
Donate link: https://github.com/evokelektrique/replicant
Tags: Synchronize, Distribute, Replicate
Requires at least: 5.2
Tested up to: 5.8
Stable tag: 0.7.1
Requires PHP: 7.0
License: AGPLv3
License URI: https://www.gnu.org/licenses/agpl-3.0.txt

A plugin that replicates and synchronize content in your WordPress websites.

== Description ==
# Replicant

A plugin that replicates and synchronize content in your WordPress websites.

## Features
* Sync WordPress posts, including **(Create, Update, Delete)** across all trusted nodes
* Sync WordPress pages, including **(Create, Update, Delete)** across all trusted nodes
* *(SOON)* Sync WordPress comments
* *(SOON)* Sync WordPress users
* *(SOON)* Sync WooCommerce products

> Note that currently the depth limit of nodes could be only 1 and maybe in futrue updates we will increase the limit.

### Example use case
* For example if you need to create chained websites, The problem arise when you need to copy countless content in multiple websites, Replicant makes this job simple and easy for you.

* You want to have a backup of your posts or pages with their metadata included, Replicant is a good choice for you.

== Frequently Asked Questions ==

= How it works? =

First, all your WordPress websites have to have this plugin installed already. Once that's set up, you need to configure your nodes according to the **Trust model** and when the target node accepted your request, your content will be synced from now on.

= Do I need to Install Replicant to sync content for my websites? =

Yes, For using **Replicant** to synchronizing content, it needs to be installed on your websites

= Can it be tested on localhost? =

It depends, if all your websites are on localhost then Yes! otherwise, your websites on remote servers might not sync with your localhost websites, because the your remote websites do not have access to your machine localhost.

= Does this plugin Syncrhonize all my content(Posts, Pages) at once? =

No. Replicant for Content will just synchronize with the one Post/Page content which you're creating, editing or deleting.

= Is Replicant Gutenberg compatible? =

Yes, Replicant is compatible with WordPress plugins

= Is Replicant supports WooCommerce? =

No, Replicant is currently not available for WooCommerce, But it is going to support WooCommerce very soon in future updates.

== Screenshots ==

1. Add Node(Website)
2. Edit Nodes(Website)
3. Websites
4. Synced posts
5. Node information

== Changelog ==

= 0.7.0 =
* Add support for categories
* Add support for tags
* Add support for featured images
* Fix minor bugs

= 0.6.2 =
* Initial stable release
