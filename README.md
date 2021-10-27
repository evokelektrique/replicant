# This project is under development

## Structrue (It's not completed yet, It needs more description)

| Path            | Description                                               |
|-----------------|-----------------------------------------------------------|
| /dist           | Compiled assets                                           |
| /languages      | Language related folders                                  |
| /resources      | Assets                                                    |
| /replicant.php  | Main file and preloads everything                         |
| /webpack.mix.js | Laravel Mix config file                                   |
| /includes       | Contains classes, hooks, views, controllers, functions... |

### On going stuff

   - [ ] Logs Expiration.
   - [X] Two Way Acceptance of Nodes.
   - [X] Create a notification badge and have a list of awaiting to be trusted nodes in main-menu.php and have buttons to reject/accept them.
   - [X] Create Acceptance Requests In form handler or delayed job.
   - [X] Fix `request_trust` variable `host` related to `accept_trust`
         deform it and remove http:// or https:// from it.
   - [X] Fix node `hash` and get the target node `hash` at node creation
         or get all The data form that so everything would be identical.
   - [X] Add trust queue badge counter on nodes-menu.
   - [X] Add delete listener for posts.
   - [X] Add update listener for posts.
   - [X] Add unique identifier hash for Posts and its types, because checking the.
         existence of a post by its `title` is wrong and causes duplication on update event.
   - [X] Implement "acting_as" for "Node" class, fetch the default from database.
   - [X] Add sender node validator in `publish.php` controller.
   - [X] Fix duplication bug at `Post` listener in `listen_save` at line 48
   - [ ] Add "acting as" select option.
   - [ ] Add "Post" publisher check how should current node act based on its "acting_as".
