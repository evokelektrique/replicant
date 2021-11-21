<h1>
   <img src="./replicant.png" alt="Replicant" />
</h1>
   
An advanced WordPress plugin to synchronize posts and pages across all your websites.

## Features

   - [X] Sync WordPress posts, including **(Create, Update, Delete)** across all trusted nodes
   - [X] Sync WordPress pages, including **(Create, Update, Delete)** across all trusted nodes
   - [ ] Sync WordPress comments
   - [ ] Sync WordPress users
   - [ ] Sync WooCommerce products

# Getting started

This section describes how to install and configure the plugin.

## Installation

To install this plugin, you can easily navigate to the plugins page in your WordPress dashboard and search for `Replicant` plugin name and simply click on the **install** button.

The other way is by cloning the repo with `git clone https://github.com/evokelektrique/replicant` command and build the project by running the `./build.sh` shell script.

## Configuration

First, all your WordPress websites have to have this plugin installed already. Once that's set up, you need to configure your nodes according to the **Trust model** and when the target node accepted your request, your content will be synced from now on.

> Note that currently the depth limit of nodes could be only 1 and maybe in futrue updates we will increase the limit.

# TODO
   - [ ] Improve UI and CSS styles
   - [ ] Improve logging
   - [ ] Improve settings
   - [ ] Add more translations
   - [ ] Implement logs expiration
   - [ ] Add `acting as` model
   - [ ] Add `Post` publisher check how should current node act based on its `acting_as`
   - [ ] Add website / github pages
   - [ ] Add Unit tests
