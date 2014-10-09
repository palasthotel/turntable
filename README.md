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

On every client, these Drupal modules have to be installed first:

  1. [entity]

On the master, these Drupal modules need to be installed:

  1. [ctools]
  2. [entity]
  3. [libraries]
  4. [services]

[ctools]: https://www.drupal.org/project/ctools
[entity]: https://www.drupal.org/project/entity
[libraries]: https://www.drupal.org/project/libraries
[services]: https://www.drupal.org/project/services

### Installation

TODO

### Configuration
