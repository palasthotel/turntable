# Turntable

A module for Drupal (Wordpress support coming soon) that allows to share nodes
between multiple instances of Drupal.

A sensible installation of this module will consist out of at least three
instances of Drupal. One instance will act as the master, all other Drupals will
be clients of that master. A client can only have one master at a time.

A client can share a node with the master. Afterwards all other nodes can import
that node as a copy or a reference. References will be updated once the original
client updates the node. Therefore local changes will be dropped in favor of the
original node's changes. Copies won't ever be changed.


## Drupal Installation

### Prerequisites

On every client, these Drupal modules have to be installed and enabled first:

  1. [entity]

On the master, these Drupal modules need to be installed and enabled:

  1. [ctools]
  2. [entity]
  3. [libraries]
  4. [services]

[ctools]: https://www.drupal.org/project/ctools
[entity]: https://www.drupal.org/project/entity
[libraries]: https://www.drupal.org/project/libraries
[services]: https://www.drupal.org/project/services

### Installation

Install all prerequisites first. Then [download this repository][this-zip] and
[turntable-drupal][turntable-drupal-zip].

[this-zip]: https://github.com/palasthotel/turntable/archive/master.zip
[turntable-drupal-zip]: https://github.com/palasthotel/turntable-drupal/archive/master.zip

#### Client

On every client, extract and upload *turntable* to
`sites/all/libraries/turntable`. Extract and upload *turntable-drupal* to
`sites/all/modules/turntable`. Temporarily turn off cron. Install and enable
"Turntable Client" in the modules section of the administration interface. Clear
Drupal's cache.

In the configuration (admin/config/turntable-client/settings), set the URL of
your master Drupal installation (including trailing slash, e.g.
http://turntable-master.example.com/). Optionally, you can change the ID of your
client. Later, you will need to set this ID in the master. You can also define,
which node types should be shared by default.

#### Master

Extract and upload *turntable* to `sites/all/libraries/turntable`. Extract and
upload *turntable-drupal* to `sites/all/modules/turntable`. Install and enable
"Turntable Master" in the modules section of the administration interface. Clear
the cache.

After that you need to enable the JSON API in the resource configuration
interface (admin/structure/services/list/turntable_master_v1/resources).
Therefore, check the two resources "image" and "node-shared". Save your settings
and clear the cache again.

As a last step, you need to enable clients to share their content with the
master. In the master's configuration settings you have to enter all IDs of the
clients that are allowed to send and fetch content from the master.

After that, you can re-enable cron on the clients. They will start to upload the
default nodes during the next run of cron. Furthermore, you can define if a node
should be uploaded in the tab "Turntable" that is visible when you edit a node.
The node will also be uploaded, once it is saved for the next time.

You can import remote nodes by copy or by reference in the tab "Turntable
Search" in the content section of the administration interface.
